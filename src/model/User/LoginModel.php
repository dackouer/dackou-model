<?php
	namespace dackou\model\User;

	use support\Request;
	use support\Db;
	use dackou\Preg;
	use dackou\Generateion;
	use dackou\service\Token\TokenService;

	class LoginModel extends \dackou\Model{
		protected $table = 'User';
		protected $title = '用户';
		protected $primaryKey = 'uid';
		protected $option_key = 'user';
    	protected $truncate = false;

		public function checkLogin(Request $request){
			try{
				$type = \ucfirst($request->input('type','username'));
				$_method = "check{$type}Login";
				if(method_exists($this,$_method)){
					return $this->$_method($request);
				}
				return '非法操作';
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		protected function getLoginList(Request $request,$value,$type = 'username'){
			try{
				$field = $this->getList($request,'field',true);
				array_push($field,'Title as role_name');
				array_push($field,'role.Pic as role_pic');
				array_push($field,'Sign as sign');
				array_push($field,'PID as group_id');
				array_push($field,'IsAdmin as is_admin');

				$where = $this->getWhere($request);
				switch($type){
					case 'username':
						array_push($where,['UserName','=',$value]);
						break;
					case 'mobile':
						array_push($where,['Mobile','=',$value]);
						break;
					case 'email':
						array_push($where,['Email','=',$value]);
						break;
					case 'wechat':
					case 'openid':
						array_push($where,['OpenidWechat','=',$value]);
						break;
					default:
						array_push($where,['UserName','=',$value]);
				}
				
				$object = Db::table($this->table)
								->join('role','RoleID','=','role.ID')
								->select(...$field)
								->where($where)
								->first();

				if(!$object || is_null($object)){
					if($type === 'username'){
						if(Preg::isMobile($value)){
							array_pop($where);
							array_push($where,['Mobile','=',$value]);
							$object = Db::table($this->table)
								->join('role','RoleID','=','role.ID')
								->select(...$field)
								->where($where)
								->first();
						}elseif(Preg::isEmail($value)){
							array_pop($where);
							array_push($where,['Email','=',$value]);
							$object = Db::table($this->table)
								->join('role','RoleID','=','role.ID')
								->select(...$field)
								->where($where)
								->first();
						}
					}
				}
				return $object;
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		private function getLoginData(Request $request,$user){
			// var_dump('get login data: ',$user);
			$this->updateUserLogin($request,is_object($user) ? $user->uid : $user['AccountID']);
			$token = TokenService::generateToken($request,$user);
			// var_dump('token: ',$token);
			$nickname = $user->nickname ?? $user['NickName'] ?? '';
			$data = [
				'uid'			=> $user->uid ?? $user['AccountID'],
				'nickname' 		=> $user->nickname ?? $user['NickName'] ?? '',
				'face'			=> $user->face ?? $user['Face'] ?? '',
				'invite'		=> $user->invite ?? $user['InviteCode'],
				'mobile'		=> $user->mobile ?? $user['Mobile'],
				'is_mobile'		=> $user->is_valid_mobile ?? 0,
				'is_nickname' 	=> $nickname ? true : false,
				'expire_time'	=> $token['expire_time'],
				'token' 		=> $token['token']
			];
			// var_dump($data);
			$this->setLoginSession($request,$user);
			return $data;
		}

		private function updateUserLogin(Request $request,$uid){
			$sql = "UPDATE `".$this->tab."` SET LoginCount = LoginCount + 1,IsOnline = 1,LastLoginTime = ?,LastLoginIp = ? WHERE AccountID = ?";
			return Db::update($sql,[time(),$request->getRealIp($safe_mode = true),$uid]);
		}

		protected function setLoginSession(Request $request,$user){
			$is_admin = $user->is_admin ?? $user['IsAdmin'] ?? 0;
			$this->setSession($request,[
				'uid'		=> $user->uid ?? $user['AccountID'],
				'group_id' 	=> $user->group_id ?? $user['GroupID'] ?? 0,
				'is_admin' 	=> $is_admin,
			]);
		}

		// 账号登录
		private function checkUsernameLogin(Request $request){
			try{
				$username = trim($request->post('username',''));
				if(!$username){
					return 100101;
				}
				$password = trim($request->post('password',''));
				if(!$password){
					return 100102;
				}

				$plat = $request->post('plat',$request->post('platform',''));
				if(!$plat){
					// $numcode = trim($request->post('numcode',''));
					// if(!$numcode){
					// 	return 100103;
					// }
					// if(!$this->checkValidateCode($request,$numcode,'numcode')){
					// 	return 100104;
					// }
				}

				$user = $this->getList($request,'login',$username,'username');
				if(!$user || !is_object($user) || !property_exists($user,'uid')){
					return 100105;
				}
				
				if(!$user->password || !$this->checkPassword($password,$user->password)){
					return 100112;
				}

				if($user->status === 0){
					return 100106;
				}

				if($user->status === 2){
					return 100151;
				}

				if($user->status === -1){
					return 100151;
				}

				if($user->status === -2){
					return '用户已注销';
				}
				
				return $this->getLoginData($request,$user);


			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		// 短信登录
		private function checkMobileLogin(Request $request){
			try{
				// var_dump('mobile login:');
 				$mobile = trim($request->post('mobile',''));
 				if(!$mobile){
 					return '手机号码不能为空';
 				}
 				if(!Preg::isMobile($mobile)){
 					return '手机号码格式不正确';
 				}
 				$smscode = trim($request->post('smscode',''));
 				if(!$smscode){
 					return '短信验证码不能为空';
 				}
 				if(!$this->checkValidateCode($request,$smscode,'smscode')){
 					return '短信验证码不正确';
 				}

 				$user = $this->getList($request,'login',$mobile,'mobile');
 				// var_dump('查询mobile用户：');
 				// var_dump($user);
 				if(!$user || !is_object($user)){
 					// var_dump('手机没有注册，查询openid:');
 					if(isset($this->config['user_auto_reg_mobile']) && !(int)$this->config['user_auto_reg_mobile']){
 						return '手机未注册';
 					}

 					$openid = '';
 					$code = trim($request->post('code',''));
 					if($code){
 						$service = new EasyWechatService();
 						$resp = $service->setAuth($request);
 						// var_dump($resp);
 						if(!$resp || !isset($resp['openid'])){
 							return '授权超时，请重试';
 						}
 						$openid = $resp['openid'];

 						// if($resp && isset($resp['openid'])){
 						// 	$openid = $resp['openid'];
 						// 	$rs = $this->getList($request,'openid',$openid);
 						// 	var_dump('查询openid用户：');
 						// 	if($rs && is_object($rs)){
 						// 		var_dump('查询到openid用户,更新openid用户信息');
 						// 		$data = ['Mobile'=>$mobile,'IsValidMobile'=>1];
 						// 		$result = $this->updateData($request,$data,$rs->uid);
 						// 		if($result !== false){
 						// 			return $this->getLoginData($request,$rs);
 						// 		}

 						// 		return $rs;
 						// 	}
 						// }
 					}

 					if($openid){
 						$res = $this->getList($request,'openid',$openid);
 						// var_dump('查询openid用户数据：');
 						// var_dump($res);
 						if($res && is_object($res)){
 							$result = $this->updateData($request,['Mobile'=>$mobile,'IsValidMobile'=>1],$res->uid);
 							if($result !== false){
 								// var_dump('用户信息更新成功，返回登录数据：');
 								$res->mobile = $mobile;
 								$result = $this->getLoginData($request,$res);
 								// var_dump($result);
 								return $result;
 							}
 							return '授权超时，请重试';
 						}
 					}

 					// var_dump('开始自动注册');
 					// 自动注册
 					$data = [
 						'Mobile' 		=> $mobile,
 						'IsValidMobile' => 1,
 						'OpenidWechat'	=> $openid
 					];

 					$data = array_merge($data,$this->setRequest($request));
 					// var_dump('预注册的用户数据：');
 					// var_dump($data);
 					$res = $this->insertData($request,$data);
 					if($res && is_array($res)){
 						$service = new RoleModel();
 						$role = $service->getList($request,$res['RoleID']);
 						$res['Sign'] = $role->sign;
 						$res['NickName'] = $mobile;
 						if(!isset($res['Face'])){
 							$res['Face'] = $this->default_face;
 						}
 						// 注册成功后记录日志
 						if(isset($data['Score']) && $data['Score']){
 							RecordModel::log(['Type'=>4,'UserID'=>$data['AccountID'],'SourceID'=>'','Value'=>$data['Score'],'Content'=>'注册赠送','Status'=>1]);
 						}
 						return $this->getLoginData($request,$res);
 					}
 					return '登录失败';
 				}else{
 					if(!$user->is_valid){
 						return 100106;
 					}

 					if($user->is_locked){
 						return 100151;
 					}

 					if($user->is_del){
 						return 100151;
 					}

 					$result = $this->getLoginData($request,$user);
 					// var_dump('mobile login user data:');
 					// var_dump($result);
 					return $result;
 				}
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		// 邮箱登录
		private function checkEmailLogin(Request $request){
			try{
				$email = trim($request->post('email',''));
				if(!$email){
					return '请填写邮箱地址';
				}
				$password = trim($request->post('password',''));
				if(!$password){
					return 100102;
				}

				$plat = $request->post('plat',$request->post('platform',''));
				if(!$plat){
					$numcode = trim($request->post('numcode',''));
					if(!$numcode){
						return 100103;
					}
					if(!$this->checkValidateCode($request,$numcode,'numcode')){
						return 100104;
					}
				}

				$user = $this->getList($request,'login',$email,'email');
				if(!$user || !is_object($user) || !property_exists($user,'uid')){
					return 100105;
				}
				
				if(!$user->password || !$this->checkPassword($password,$user->password)){
					return 100112;
				}

				if($user->status === 0){
					return 100106;
				}

				if($user->status === 2){
					return 100151;
				}

				if($user->status === -1){
					return 100151;
				}

				if($user->status === -2){
					return '用户已注销';
				}
				
				return $this->getLoginData($request,$user);


			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		// 微信授权登录
		private function checkWechatLogin(Request $request){
			try{
				$type = $request->input('type','mini');
				$service = new \dackou\service\EasyWechat\EasyWechatService();
				$result = $service->getPhoneNumber($request,$type);
				if(is_array($result) && isset($result['phoneNumber']) && isset($result['openid'])){
					$country = $result['countryCode'] ?? '86';
					$mobile = $result['phoneNumber'];
					$openid = $result['openid'];
					$unionid = $result['unionid'] ?? '';

					if(!$openid){
						return '无效的授权';
					}

					$user = $this->getList($request,'login',$openid,'openid');
					// var_dump('get openid user:',$user);
					if(is_array($user) && isset($user['code'])){
						return $user;
					}elseif(!$user && $mobile){
						$user = $this->getList($request,'login',$mobile,'mobile');
						// var_dump('get mobile user: ',$user);
						if(is_array($user) && isset($user['code'])){
							return $user;
						}
					}

					if($user){
						if(!$user->is_valid){
	 						return 100106;
	 					}

	 					if($user->is_locked){
	 						return 100151;
	 					}

	 					if($user->is_del){
	 						return 100151;
	 					}
	 					if($user->status === 0){
							return 100106;
						}

						if($user->status === 2){
							return 100151;
						}

						if($user->status === -1){
							return 100151;
						}

						if($user->status === -2){
							return '用户已注销';
						}

						// var_dump('老用户');
						$data = [];
						if(!$user->mobile){
							$data['Mobile'] = $mobile;
						}
						if(!$user->openid){
							$data['OpenidWechat'] = $openid;
						}
						if($user->unionid){
							$data['UnionidWechat'] = $unionid;
						}
						// var_dump('update data: ',$data);
						if($data){
							$this->updateData($request,$data,$user->uid);
						}

						return $this->getLoginData($request,$user);
					}else{
						// var_dump('新用户,开始注册');
	 					// 自动注册
	 					$data = [
	 						'Mobile' 		=> $mobile,
	 						'IsValidMobile' => $mobile ? 1 : 0,
	 						'OpenidWechat'	=> $openid,
	 						'UnionidWechat'	=> $unionid,
	 					];

	 					$data = array_merge($data,$this->setRequest($request));
	 					// var_dump('预注册的用户数据：');
	 					// var_dump($data);
	 					$res = $this->insertData($request,$data);
	 					if($res && is_array($res)){
		 					$res['Sign'] = $data['Sign'];
		 					$res['NickName'] = $mobile;
		 					// var_dump('注册成功的用户：',$res);
							// 注册成功后记录日志
							if(isset($data['Score']) && $data['Score']){
								RecordModel::log(['Type'=>4,'UserID'=>$data['AccountID'],'SourceID'=>'','Value'=>$data['Score'],'Content'=>'注册赠送','Status'=>1]);
							}

		 					return $this->getLoginData($request,$res);
		 				}


	 					// if($res && is_array($res)){
	 					// 	$service = new RoleModel();
	 					// 	$role = $service->getList($request,$res['RoleID']);
	 					// 	$res['Sign'] = $role->sign;
	 					// 	$res['NickName'] = $mobile;
	 					// 	if(!isset($res['Face'])){
	 					// 		$res['Face'] = $this->default_face;
	 					// 	}
	 					// 	return $this->getLoginData($request,$res);
	 					// }
	 					return '登录失败';
					}

					// var_dump('get user data: ',$user);
				}else{
					return '授权失败';
				}

			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		// QQ登录
		private function checkQqLogin(Request $request){
			try{

			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		// 微博登录
		private function checkWeiboLogin(Request $request){
			try{

			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		private function getDefaultRole(Request $request){
			$role_id = $this->getConfig('user_default_reg_role_id');
			// var_dump('role_id: '.$role_id);

			$_class_name = '\app\model\Role\RoleModel';
			if(!class_exists($_class_name)){
				$_class_name = '\dackou\model\Role\RoleModel';
			}
			if($role_id){
				$service = new $_class_name();
				return $service->getList($request,$role_id);
			}else{
				$service = new $_class_name();
				return $service->getList($request,'default');
			}
		}

		private function setRequest(Request $request){
			$gener = Generateion::create();

			$data['uuid'] = $gener['uuid'];
			$data['uid'] = $gener['uid'];
			$data['token'] = $gener['token'];
			$data['invite_code'] = $gener['invite'];
			$password = isset($this->config['default_password']) ? $this->config['default_password'] : 'F135246';
			$data['password'] = $this->makePassword($password);
			// $data['CreateIP'] = $request->getRealIp($safe_mode=true);
			$data['ip_address'] = $this->getIPAddress($request);
			$face = $request->post('face',$this->default_face);
			if($face){
				$data['Face'] = $face;
			}
			$user_reg_send_score = $this->getConfig('user_reg_send_score');
			if($user_reg_send_score){
				$data['Score'] = $user_reg_send_score;
			}
			$user_reg_send_balance = $this->getConfig('user_reg_send_balance');
			if($user_reg_send_balance){
				$data['Balance'] = $user_reg_send_balance;
			}
			$user_reg_send_coin = $this->getConfig('user_reg_send_coin');
			if($user_reg_send_coin){
				$data['Coin'] = $user_reg_send_coin;
			}

			$invite = trim($request->input('invite',''));
			if($invite){
				$data['Invite'] = $invite;
			}

			$user_is_active = $this->getConfig('user_is_active');
			if($user_is_active){
				$data['IsValid'] = 1;
				$data['Status'] = 1;
			}

			$role = $this->getDefaultRole($request);
			if($role && is_object($role)){
				$data['RoleID'] = $role->id;
				$data['Sign'] = $role->sign;
			}

			return $data;
		}
	}
?>