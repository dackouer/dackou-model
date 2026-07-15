<?php
	namespace dackou\model\User;

	use support\Request;
	use support\Db;
	use dackou\Preg;

	class AuthModel extends \dackou\Model{
		protected $table = 'User';
		protected $title = '用户';
		protected $primaryKey = 'uid';
		protected $option_key = 'user';
    	protected $truncate = false;
    	protected $type_value = [];

    	/**
    	 * 统一认证入口
    	 * @param  Request $request [description]
    	 * @return [type]           [description]
    	 */
    	public function checkAuth(Request $request){
    		$type = trim($request->input('type','person'));
    		$user_id = $request->post('user_id',0);
    		if(!$user_id || !is_numeric($user_id) || $user_id <= 0){
    			return '无效的认证用户';
    		}

    		$_class_name = $this->getClassName('User');
    		$service = new $_class_name();
    		$user = $service->getList($request,$user_id);
    		if(!$user || !is_object($user)){
    			return '无效的认证用户';
    		}

    		if($user->status !== 1){
    			return '无效的用户状态';
    		}

    		if($user->is_auth && $user->auth_type == $type){
    			return '用户已完成该认证';
    		}

    		$is_admin = $this->getTokenData($request,'is_admin');
    		if(!$is_admin){
    			// return '非法认证';
    		}

    		$_method = 'checkAuthBy' . \ucfirst($type);
    		if(\method_exists($this,$_method)){
    			return $this->$_method($request,$user);
    		}

    		return 100007;
    	}

    	// 个人认证
    	private function checkAuthByPerson(Request $request,$user){
    		$realname = trim($request->post('realname',''));
    		$idcard = trim($request->post('idcard',''));

    		if(!$realname){
    			return '请填写您的真实姓名';
    		}
    		if(!$idcard){
    			return '请填写您的身份证号码';
    		}
    		if(!Preg::isIdcard($idcard)){
    			return '身份证号码格式不正确';
    		}

    		if($this->checkExists(['Idcard'=>$idcard],$user->uid)){
    			return '该身份证已有用户认证,请重新输入';
    		}

    		$gener = $this->getGender($idcard);
    		$birth = $this->getBirth($idcard);
    		$age = $this->getAge($birth);
    		$zodiac = $this->getZodiac($idcard);
    		$constellation = $this->getConstellation($idcard);

    		$data = [
    			'RealName' 		=> $realname,
    			'Idcard'   		=> $idcard,
    			'Gender'		=> $gener,
    			'Age'			=> $age,
    			'Birth'			=> $birth,
    			'Zodiac'		=> $zodiac,
    			'Constellation' => $constellation,
    			'IsAuth'		=> 1,
    			'AuthType'		=> 'person',
    			'AuthTime'		=> time(),
    			'AuthCode'		=> '',
    			'AuthName'		=> '',
    		];
    		if(!$user->nickname){
    			$data['NickName'] = $realname;
    		}
    		// var_dump('data: ',$data);
    		return $this->updateData($request,$data,$user->uid);
    	}

    	// 企业认证
    	private function checkAuthByEnterprise(Request $request,$user){

    	}

    	// 商户认证
    	private function checkAuthByMerchant(Request $request,$user){
    		$merchant_name = trim($request->post('merchant_name',''));
    		$legal_name = trim($request->post('legal_name',''));
    		$credit_code = trim($request->post('credit_code',''));
    		$contact_name = trim($request->post('contact_name',''));
    		$idcard = trim($request->post('idcard',''));
    		$telphone = trim($request->post('telphone',''));
    		$mobile = trim($request->post('mobile',''));
    		$hotline = trim($request->post('hotline',''));
    		$email = trim($request->post('email',''));
    		$website = trim($request->post('website',''));
    		$city = $request->post('city',[]);
    		$address = trim($request->post('address',''));

    		if(!$merchant_name){
    			return '商户名称不能为空';
    		}

    		if($this->checkMerchantExists(['MerchantName'=>$merchant_name])){
    			return '该商户名称已存在,请重新输入';
    		}

    		if(!$contact_name){
    			return '请填写您的真实姓名';
    		}
    		if(!$idcard){
    			return '请填写您的身份证号码';
    		}
    		if(!Preg::isIdcard($idcard)){
    			return '身份证号码格式不正确';
    		}

    		if($this->checkMerchantExists(['Idcard'=>$idcard])){
    			return '该身份证已有商户认证,请重新输入';
    		}

    		if($mobile && !Preg::isMobile($mobile)){
    			return '手机号码格式不正确';
    		}

    		if($email && !Preg::isEmail($email)){
    			return '邮箱地址格式不正确';
    		}

    		$gener = $this->getGender($idcard);
    		$birth = $this->getBirth($idcard);
    		$age = $this->getAge($birth);
    		$zodiac = $this->getZodiac($idcard);
    		$constellation = $this->getConstellation($idcard);

    		if(!$city || !is_array($city)){
    			return '请选择城市';
    		}

    		$province_id = $city['province'] ?? $city['province_id'] ?? $city[0][0];
			$city_id = $city['city'] ?? $city['city_id'] ?? $city[1][0];
			$district_id = $city['district'] ?? $city['district_id'] ?? $city[2][0];
			$city_name = '';

			$_class_name = $this->getClassName('City');
			$service = new $_class_name();
			$province = $service->getList($request,$province_id);
			if($province && is_object($province)){
				$city_name .= $province->title;

				$service = new $_class_name();
				$city = $service->getList($request,$city_id);
				if($city && is_object($city)){
					$city_name .= '-' . $city->title;

					$service = new $_class_name();
					$district = $service->getList($request,$district_id);
					if($district && is_object($district)){
						$city_name .= '-' . $district->title;
					}
				}
			}

			$_class_name = $this->getClassName('Merchant');
			$service = new $_class_name();
			$code = $service->createUniqueCode('MerchantCode',12);
			$time = time();
			$ip = $request->getRealIp($safe_mode = true);

    		$data = [
    			'UserID'		=> $user->uid,
    			'MerchantName'	=> $merchant_name,
    			'CateID'		=> 1,
    			'MerchantCode'	=> $code,
    			'LegalName'		=> $legal_name,
    			'CreditCode'	=> $credit_code,
    			'ContactName'	=> $contact_name,
    			'Idcard'		=> $idcard,
    			'Telphone'		=> $telphone,
    			'Mobile'		=> $mobile,
    			'Hotline'		=> $hotline,
    			'Email'			=> $email,
    			'Website'		=> $website,
    			'ProvinceID'	=> $province_id,
    			'CityID'		=> $city_id,
    			'DistrictID'	=> $district_id,
    			'CityName'		=> $city_name,
    			'Address'		=> $address,
    			'CreateTime'	=> $time,
    			'CreateIP'		=> $ip,
    			'UpdateTime'	=> $time,
    			'UpdateIP'		=> $ip,
    		];
    		
    		$result = $this->insertMerchantData($request,$data);
    		// var_dump('result: ',$result);
    		if($result === true){
    			$data = [
	    			'IsAuth'		=> 1,
	    			'AuthType'		=> 'merchant',
	    			'AuthTime'		=> $time,
	    			'AuthCode'		=> $code,
	    			'AuthName'		=> $merchant_name,
    			];

    			$role_id = $request->post('role_id',0);
    			if($role_id){
    				$data['RoleID'] = $role_id;
    			}

    			if(!$user->nickname){
    				$data['NickName'] = $contact_name;
    			}

    			if($mobile && !$user->mobile){
    				$data['Mobile'] = $mobile;
    			}

    			if($email && !$user->email){
    				$data['Email'] = $email;
    			}

    			if(!$user->realname){
    				$data['RealName'] = $contact_name;
    			}

    			if(!$user->idcard){
    				$data['Idcard'] = $idcard;
    			}

    			if(!$user->gender){
    				$gender = $this->getGender($idcard);
    				$data['Gender'] = $gender;
    			}

    			if(!$user->birth){
    				$birth = $this->getBirth($idcard);
    				$data['Birth'] = $birth;
    			}

    			if(!$user->age){
    				$age = $this->getAge($birth);
    				$data['Age'] = $age;
    			}

    			if(!$user->zodiac){
    				$zodiac = $this->getZodiac($idcard);
    				$data['Zodiac'] = $zodiac;
    			}

    			if(!$user->constellation){
    				$constellation = $this->getConstellation($idcard);
    				$data['Constellation'] = $constellation;
    			}

    			return $this->updateData($request,$data,$user->uid);
    		}

    		return $result;
    	}

    	private function checkMerchantExists($data,$id = 0){
    		try{
    			$where = [];
    			foreach($data as $k => $v){
    				array_push($where,[$this->convert($k),'=',$v]);
    			}
    			
    			$rows = Db::table('merchant')
    						->where($where)
    						->count();
    			
    			return $rows ? true : false;
    		}catch(\Exception $e){
    			return true;
    		}
    	}

    	private function insertMerchantData(Request $request,$data){
    		try{
    			$keys = array_keys($data);
    			$vals = array_values($data);
    			
    			$val = [];
    			$param = [];

    			$keys = implode(',', $keys);

    			foreach($vals as $item){
    				array_push($val,'?');
    				array_push($param,$item);
    			}

    			$val = implode(',',$val);

    			$sql = "INSERT INTO ".$this->prefix."merchant ({$keys}) VALUES ({$val})";

    			$result = Db::insert($sql,$param);
    			var_dump('result: ',$result);

    			return $result !== false ? true : '商户添加失败';
    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
    	}
	}
?>