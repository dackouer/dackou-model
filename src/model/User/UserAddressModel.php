<?php
	namespace dackou\model\User;

	use support\Request;
	use support\Db;
	use dackou\Preg;

	class UserAddressModel extends \dackou\Model{
		protected $table = 'UserAddress';
		protected $title = '用户地址';

		protected function getDefaultList(Request $request,$user_id = 0,$address_id = 0){
			$service = new UserModel();
			$user = $service->getList($request,$user_id);
			if(!$user || !is_object($user)){
				return $user;
			}
			
			$field = $this->getList($request,'field',true);
			$where = $this->getWhere($request);
			array_push($where,[$this->table.'.UserID','=',$user_id]);
			if($address_id){
				array_push($where,[$this->table.'.ID','=',$address_id]);
			}else{
				array_push($where,[$this->table.'.IsDefault','=',1]);
			}

			$object = Db::table($this->table)
							->select(...$field)
							->where($where)
							->first();

			return ['user'=>$user,'address'=>$object];
		}

		protected function getUserList(Request $request,$user_id = 0){
			if(!$user_id || !is_numeric($user_id) || $user_id < 0){
				return 100007;
			}

			$field = $this->getList($request,'field',true);

			$where = $this->getWhere($request);
			array_push($where,[$this->table.'.UserID','=',$user_id]);

			$object = Db::table($this->table)
						->select(...$field)
						->where($where)
						->get();

			return $object;
		}

		
		protected function updateUserDefault(Request $request,$user_id,$id){
			try{
				var_dump('id: '.$id.' user_id: '.$user_id);
				$sql = "UPDATE `".$this->tab."` SET IsDefault = 0 WHERE UserID = ? AND ID <> ?";
				$result = Db::update($sql,[$user_id,$id]);
				return $result !== false ? true : false;
			}catch(\Exception $e){
				var_dump($this->getExceptionError($e));
				return false;
			}
		}

		protected function setExcute(Request $request,$data,$flag = ''){
			if(isset($data['is_default']) && $data['is_default']){
				return $this->updateUserDefault($request,$user_id,$data['id']);
			}

			return true;
		}

		protected function validate(Request $request,$id = 0,$obj = null){
			$user_id = $request->post('user_id',0);
			$city = $request->post('city',[]);
			$address = trim($request->post('address',''));
			$realname = trim($request->post('realname',''));
			$mobile = trim($request->post('mobile',''));
			$is_default = $request->post('is_default',0);
			$is_default = $is_default ? 1 : 0;

			if(!$user_id){
				return 100007;
			}

			$_class_name = $this->getClassName('User');
			$service = new $_class_name();
			$user = $service->getList($request,$user_id);
			if(!$user || !is_object($user)){
				return $user;
			}

			if(!$city){
				return '请选择城市地址';
			}

			if(count($city) < 2){
				return '无效的城市地址';
			}

			$province_id = 0;
			$city_id = 0;
			$district_id = 0;
			$city_name = '';

			if($city){
				$province_id = $city['province'] ?? $city[0][0];
				$city_id = $city['city'] ?? $city[1][0];
				$district_id = $city['district'] ?? $city[2][0];

				if($province_id){
					$_class_name = $this->getClassName('City');
					$service = new $_class_name();
					$province = $service->getList($request,$province_id);
					if(!$province || !is_object($province)){
						return '无效的城市';
					}

					$city_name .= $province->title;

					if($city_id){
						$_class_name = $this->getClassName('City');
						$service = new $_class_name();
						$citys = $service->getList($request,$city_id);
						if(!$citys || !is_object($citys)){
							return '无效的城市';
						}

						$city_name .= '-' . $citys->title;

						if($district_id){
							$_class_name = $this->getClassName('City');
							$service = new $_class_name();
							$district = $service->getList($request,$district_id);
							if(!$district || !is_object($district)){
								return '无效的城市';
							}

							$city_name .= '-' . $district->title;
						}
					}
				}
			}

			if(!$address){
				return '详细地址不能为空';
			}

			if(!$realname){
				return '姓名不能为空';
			}

			if(!$mobile){
				return '手机号码不能为空';
			}

			if(!Preg::isMobile($mobile)){
				return '手机号码格式不正确';
			}

			if(!$is_default){
				$obj = $this->getList($request,'user',$user_id);
				if(!$obj){
					$is_default = 1;
				}
			}

			$data['province_id'] = $province_id;
			$data['city_id'] = $city_id;
			$data['district_id'] = $district_id;
			// $data['street_id'] = 0;
			$data['city_name'] = $city_name;
			$data['address'] = $address;
			$data['realname'] = $realname;
			$data['mobile'] = $mobile;
			$data['is_default'] = $is_default;

			var_dump($data);

			return $data;
		}

	}
?>