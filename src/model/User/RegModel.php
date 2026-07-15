<?php
	namespace dackou\model\User;

	use support\Request;
	use support\Db;
	use dackou\Preg;
	use dackou\Generateion;
	use dackou\service\Token\TokenService;

	class RegModel extends \dackou\Model{
		protected $table = 'User';
		protected $title = '用户';
		protected $primaryKey = 'uid';
		protected $option_key = 'user';
    	protected $truncate = false;

    	public function checkReg(Request $request){
    		$type = $request->input('type','username');
    		$_method = 'checkRegBy' . \ucfirst($type);
    		if(\method_exists($this, $_method)){
    			return $this->$_method($request);
    		}

    		return 100007;
    	}

    	private function checkRegByUsername($request){
    		try{
    			$username = trim($request->post('username',''));
    			$password = trim($request->post('password',''));
    			$checkpwd = trim($request->post('checkpwd',''));
    			$numcode = trim($request->post('numcode',''));

    			if(!$username){
    				return '请输入用户名';
    			}
    			if(strlen($username) < 5 && strlen($username) > 32){
    				return '用户名大于5位小于32位';
    			}
    			if($this->checkExists(['UserName'=>$username],0)){
    				return '用户名已存在';
    			}
    			if(!$password){
    				return '请输入密码';
    			}
    			if(!$checkpwd){
    				return '请确认密码';
    			}
    			if($checkpwd !== $password){
    				return '密码确认有误';
    			}

    			if(!$numcode){
    				return '验证码不能为空';
    			}

    			if(!$this->checkValidateCode($request,$numcode,'numcode')){
    				return '验证码不正确';
    			}

    			$data['username'] = $username;

    			$gener = Generateion::create();
    			$data['uuid'] = $gener['uuid'];
    			$data['uid'] = $gener['uid'];
    			$data['token'] = $gener['token'];
    			$data['invite_code'] = $gener['invite'];
    			$data['password'] = $this->makePassword($password);
    			$data['reg_ip'] = $request->getRealIp($safe_mode=true);
    			$data['ip_address'] = $this->getIPAddress($request);
    			$data['role_id'] = $this->getRoleId($request);
    			$data['province_id'] = 1;
    			$data['city_id'] = 1;
    			$data['district_id'] = 1;
    			$data['status'] = 1;

    			$result = $this->insertData($request,$data);
    			if($result){
    				return [
    					'uid' => $data['uid'],
    					'username' => $username
    				];
    			}

    			return $result;
    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
    	}

    	private function checkRegByMobile(Request $request){
    		try{
    			$mobile = trim($request->post('mobile',''));
    			$smscode = trim($request->post('smscode',''));
    			if(!$mobile){
    				return '手机号码不能为空';
    			}
    			if(!Preg::isMobile($mobile)){
    				return '手机号码格式不正确';
    			}
    			if($this->checkExists(['Mobile'=>$mobile],0)){
    				return '手机号码已存在,请重新输入';
    			}
    			if(!$smscode){
    				return '短信验证码不能为空';
    			}
    			if(!$this->checkValidateCode($request,$smscode,'smscode')){
    				return '短信验证码不正确';
    			}

    			$user = $this->getList($request,'mobile',$mobile);
    			if($user && is_object($user)){
    				return [
    					'uid' 		=> $user->uid,
    					'username' 	=> $user->username,
    					'face' 		=> $user->face,
    					'token' 	=> $this->getToken($request,$user)
    				];
    			}else{
    				$data['mobile'] = $mobile;

    				$gener = Generateion::create();
    				$data['uuid'] = $gener['uuid'];
    				$data['uid'] = $gener['uid'];
    				$data['token'] = $gener['token'];
    				$data['invite_code'] = $gener['invite'];
    				$password = isset($this->config['default_password']) ? $this->config['default_password'] : 'F135246';
    				$data['password'] = $this->makePassword($password);
    				$data['reg_ip'] = $request->getRealIp($safe_mode=true);
    				$data['ip_address'] = $this->getIPAddress($request);
    				$data['role_id'] = $this->getRoleId($request);
    				$data['province_id'] = 1;
    				$data['city_id'] = 1;
    				$data['district_id'] = 1;
    				$data['status'] = 1;
    				
    				$result = $this->insertData($request,$data);
    				if($result){
    					$sign = $this->getSign($data['role_id']);
    					return [
    						'uid' 		=> $data['uid'],
    						'username'  => $mobile,
    						'face'		=> '',
    						'token'		=> $this->getToken($request,[
    							'uid'     => $data['uid'],
    							'token'   => $data['token'],
    							'role_id' => $data['role_id'],
    							'sign'    => $sign
    						])
    					];
    				}

    				return $result;
    			}

    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
    	}

    	private function checkRegByEmail($request){
    		try{
    			$email = trim($request->post('email',''));
    			$password = trim($request->post('password',''));
    			$checkpwd = trim($request->post('checkpwd',''));
    			$numcode = trim($request->post('numcode',''));

    			if(!$email){
    				return '请输入邮箱地址';
    			}
    			if(!Preg::isEmail($email)){
    				return '邮箱地址格式不正确';
    			}
    			if($this->checkExists(['Email'=>$email],0)){
    				return '邮箱地址已存在';
    			}
    			if(!$password){
    				return '请输入密码';
    			}
    			if(!$checkpwd){
    				return '请确认密码';
    			}
    			if($checkpwd !== $password){
    				return '密码确认有误';
    			}

    			if(!$numcode){
    				return '验证码不能为空';
    			}

    			if(!$this->checkValidateCode($request,$numcode,'numcode')){
    				return '验证码不正确';
    			}

    			$data['email'] = $email;

    			$gener = Generateion::create();
    			$data['uuid'] = $gener['uuid'];
    			$data['uid'] = $gener['uid'];
    			$data['token'] = $gener['token'];
    			$data['invite_code'] = $gener['invite'];
    			$data['password'] = $this->makePassword($password);
    			$data['reg_ip'] = $request->getRealIp($safe_mode=true);
    			$data['ip_address'] = $this->getIPAddress($request);
    			$data['role_id'] = $this->getRoleId($request);
    			$data['province_id'] = 1;
    			$data['city_id'] = 1;
    			$data['district_id'] = 1;
    			$data['status'] = 1;

    			$result = $this->insertData($request,$data);
    			if($result){
    				return [
    					'uid' => $data['uid'],
    					'email' => $email
    				];
    			}

    			return $result;
    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
    	}

    	protected function getMobileList(Request $request,$mobile){
    		try{
    			$field = [
    				'AccountID as uid',
    				'UserName as username',
    				'Face as face',
    				'Mobile as mobile',
    				'RoleID as role_id',
    				'Sign as sign',
    				$this->table.'.Token as token',
    			];
    			$where = [['Status','>',0],['Mobile','=',$mobile]];
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

    	protected function getRoleId(Request $request){
    		if(isset($this->config['user_default_reg_role_id']) && $this->config['user_default_reg_role_id']){
    			return $this->config['user_default_reg_role_id'];
    		}

    		$_class_name = $this->getClassName('Role');
    		$service = new $_class_name();

    		$obj = $service->getList($request,'default');
    		if($obj && is_object($obj)){
    			return $obj->id;
    		}

    		return 0;
    	}

    	private function getSign($role_id){
    		try{
    			$object = Db::table('role')
    						->select('Sign as sign')
    						->where('RoleID',$role_id)
    						->first();
    			return $object ? $object->sign : '';
    		}catch(\Exception $e){
    			return '';
    		}
    	}

    	protected function getToken(Request $request,$user){
    		try{
	    		if(is_object($user) && \property_exists($user, 'uid')){
	    			$data = [
	    				'uid' 		=> $user->uid,
						'token' 	=> $user->token,
						'sign' 		=> $user->sign,
						'rid' 		=> $user->role_id,
						'expire' 	=> date('Y-m-d H:i:s',(time()+24*3600*365*10))
	    			];
	    		}else{
	    			$data = [
	    				'uid' 		=> $user['uid'],
						'token' 	=> $user['token'],
						'sign' 		=> $user['sign'],
						'rid' 		=> $user['role_id'],
						'expire' 	=> date('Y-m-d H:i:s',(time()+24*3600*365*10))
	    			];
	    		}
	    		$result = TokenService::createToken($data);
	    		return $result['token'];
	    	}catch(\Exception $e){
	    		return '';
	    	}
    	}
	}
?>