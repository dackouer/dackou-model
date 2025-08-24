<?php
	namespace dackou\model\User;

	use support\Request;
	use support\Db;
	use dackou\Preg;
	use dackou\Generateion;

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
				var_dump('_method: '.$_method);
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
						array_push($where,['WechatOpenid','=',$value]);
						break;
					default:
						array_push($where,['UserName','=',$value]);
				}

				$object = Db::table($this->table)
								->join('role','RoleID','=','role.ID')
								->select(...$field)
								->where($where)
								->first();
				return $object;
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		private function getLoginData(Request $request,$user){
			$this->updateData($request,[
				'LoginCount'	=> $user->login_count+1,
				'IsOnline'		=> 1,
				'LastLoginTime'	=> time(),
				'LastLoginIp'	=> $request->getRealIp($safe_mode = true)
			],$user->uid);
			$token = \dackou\Token::generateToken($request,$user);
			$data = [
				'uid'			=> $user->uid,
				'nickname' 		=> $user->nickname,
				'face'			=> $user->face,
				'invite'		=> $user->invite,
				'mobile'		=> $user->mobile,
				'is_mobile'		=> $user->is_valid_mobile,
				'is_nickname' 	=> $user->is_nickname,
				'expire_time'	=> $token['expire_time'],
				'token' 		=> $token['token']
			];
			$this->setLoginSession($request,$user);
			return $data;
		}

		protected function setLoginSession(Request $request,$user){
			$this->setSession($request,[
				'uid'		=> $user->uid,
				'group_id' 	=> $user->group_id,
				'is_admin' 	=> $user->is_admin,
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

				$plat = $request->post('plat','');
				var_dump('plat: '.$plat);
				if(!$plat){
					$numcode = trim($request->post('numcode',''));
					if(!$numcode){
						return 100103;
					}
					if(!$this->checkValidateCode($request,$numcode,'numcode')){
						return 100104;
					}
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
				var_dump('mobile login:');
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
 						var_dump('查询openid用户数据：');
 						var_dump($res);
 						if($res && is_object($res)){
 							$result = $this->updateData($request,['Mobile'=>$mobile,'IsValidMobile'=>1],$res->uid);
 							if($result !== false){
 								var_dump('用户信息更新成功，返回登录数据：');
 								$res->mobile = $mobile;
 								$result = $this->getLoginData($request,$res);
 								var_dump($result);
 								return $result;
 							}
 							return '授权超时，请重试';
 						}
 					}

 					var_dump('开始自动注册');
 					// 自动注册
 					$data = [
 						'Mobile' 		=> $mobile,
 						'IsValidMobile' => 1,
 						'OpenidWechat'	=> $openid
 					];

 					$data = array_merge($data,$this->setRequest($request));
 					var_dump('预注册的用户数据：');
 					var_dump($data);
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
 					var_dump('mobile login user data:');
 					var_dump($result);
 					return $result;
 				}
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		// 微信登录
		private function checkWechatLogin(Request $request){
			try{

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

		private function setRequest(Request $request){
			$gener = Generateion::create();

			$data['uuid'] = $gener['uuid'];
			$data['uid'] = $gener['uid'];
			$data['token'] = $gener['token'];
			$data['invite_code'] = $gener['invite'];
			$password = isset($this->config['default_password']) ? $this->config['default_password'] : 'F135246';
			$data['password'] = $this->makePassword($password);
			$data['create_ip'] = $request->getRealIp($safe_mode=true);
			$data['ip_address'] = $this->getIPAddress($request);

			return $data;
		}
	}
?>