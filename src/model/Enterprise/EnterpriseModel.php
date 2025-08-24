<?php
	namespace dackou\model\Enterprise;

	use support\Request;
	use support\Db;
	use dackou\Preg;

	class EnterpriseModel extends \dackou\Model{
		protected $table = 'Enterprise';
		protected $title = '认证企业';
		protected $code_length = 12;
		protected $member_id = 1601;
		protected $role_id = 1501;
		protected $auth_value = 2;
		protected $status_value = [-1=>'已取消',0=>'待认证',1=>'正常'];

		protected function getPageList(Request $request){
			try{
				$field = $this->getList($request,'field',true);
				array_push($field,'user.RealName as realname');
				array_push($field,'province.Title as province');
				array_push($field,'city.Title as city');
				array_push($field,'district.Title as district');
				$where = $this->getWhere($request);

				$rows = Db::table($this->table)
							->join('user','UserID','=','user.AccountID')
							->join('city as province','ProvinceID','=','province.ID')
							->join('city','CityID','=','city.ID')
							->join('city as district','DistrictID','=','district.ID')
							->where($where)
							->count();
				[$offset,$limit] = $this->getLimit($request);
				$object = Db::table($this->table)
							->join('user','UserID','=','user.AccountID')
							->join('city as province','ProvinceID','=','province.ID')
							->join('city','CityID','=','city.ID')
							->join('city as district','DistrictID','=','district.ID')
							->select(...$field)
							->where($where)
							->offset($offset)
							->limit($limit)
							->get();

				return ['rows' => $rows,'data' => $object];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		protected function setExcute(Request $request,$data,$flag = false){
			if(!$flag){
				$_class_name = $this->getClassName('User');
				$service = new $_class_name();
				return $service->setAuth($request,$data,$this->role_id,$this->auth_value);
			}

			return true;
		}

		protected function validate(Request $request,$id = 0,$obj = null){
			$user_id = $request->post('user_id',0);
			$enterprise_name = trim($request->post('enterprise_name',''));
			$short_name = trim($request->post('short_name',''));
			// $idcode = trim($request->post('idcode',''));
			$desc = trim($request->post('desc',''));
			$picture = $request->post('picture',[]);
			$legal_name = trim($request->post('legal_name',''));
			$idcard = trim($request->post('idcard',''));
			$license = $request->post('license');
			if(is_array($license)){
				$license = end($license) ?? '';
			}
			$credit_code = trim($request->post('credit_code',''));
			$grantor_name = trim($request->post('grantor_name',''));
			$grantor_idcard = trim($request->post('grantor_idcard',''));
			$telphone = trim($request->post('telphone',''));
			$mobile = trim($request->post('mobile',''));
			$hotline = trim($request->post('hotline',''));
			$email = trim($request->post('email',''));
			$website = trim($request->post('website',''));
			$city = $request->post('city',[]);
			$address = trim($request->post('address',''));
			$content = trim($request->post('content',''));

			if(!is_numeric($user_id) || $user_id <= 0){
				return '请选择认证会员';
			}
			$_class_name = $this->getClassName('User');
			$service = new $_class_name();
			$user = $service->getList($request,$user_id);
			if(!$user || !is_object($user)){
				return '无效的认证会员';
			}
			if($user->status !== 1){
				return '会员被锁定或已删除';
			}

			if($user->is_auth == $this->auth_value){
				return '该会员已认证';
			}
			// if($user->role_id > $this->role_id){
			// 	return '该会员已完成更高级认证,无需降级认证';
			// }
			if(!$user->realname){
				$data['is_realname'] = false;
			}
			if(!$user->idcard){
				$data['is_idcard'] = false;
			}
			if(!$user->mobile && $mobile){
				$data['is_mobile'] = false;
			}


			$data['user_id'] = $user_id;
			$data['enterprise_name'] = $enterprise_name;
			$data['short_name'] = $short_name;
			$data['idcode'] = $this->createUniqueCode('Idcode',$this->code_length);
			$data['desc'] = $desc;
			$data['picture'] = $picture;
			$data['pic'] = isset($picture[0]) ? $picture[0] : '';
			$data['legal_name'] = $legal_name;
			$data['idcard'] = $idcard;
			$data['license'] = $license;
			$data['credit_code'] = $credit_code;
			$data['grantor_name'] = $grantor_name;
			$data['grantor_idcard'] = $grantor_idcard;
			$data['telphone'] = $telphone;
			$data['mobile'] = $mobile;
			$data['hotline'] = $hotline;
			$data['email'] = $email;
			$data['website'] = $website;
			$data['province_id'] = $city['province'] ?? $city[0][0];
			$data['city_id'] = $city['city'] ?? $city[1][0];
			$data['district_id'] = $city['district'] ?? $city[2][0];
			$data['address'] = $address;
			$data['content'] = $content;
			$data['status'] = 1;

			return $data;
		}

		protected function getActionList(Request $request,$id = 0): array
		{
			if($id){
				$data = $this->getList($request,$id);
			}

			$user_id = $request->input('user_id',0);

			$action = [];
			if($user_id){
				array_push($action,['type'=>'input','label'=>'认证会员','prop'=>'user_id','value'=>$user_id,'hidden'=>true,'rules'=>['required'=>true,'message'=>'认证会员不能为空']]);
			}else{
				$_class_name = $this->getClassName('User');
				$service = new $_class_name();
				$user = $service->getList($request,'option',$this->member_id);

				array_push($action,['type'=>'select','label'=>'认证会员','prop'=>'user_id','value'=>$data->user_id ?? '','children'=>$user,'rules'=>['required'=>true,'message'=>'请选择认证会员']]);
			}
			array_push($action,
				['type'=>'input','label'=>'单位名称','prop'=>'enterprise_name','value'=>$data->enterprise_name ?? '','rules'=>['required'=>true,'message'=>'单位名称不能为空']],
				['type'=>'input','label'=>'单位简称','prop'=>'short_name','value'=>$data->short_name ?? '','rules'=>['required'=>true,'message'=>'单位简称不能为空']],
				// ['type'=>'input','label'=>'单位编码','prop'=>'idcode','value'=>$data->idcode ?? '','attrs'=>['type'=>'idcode'],'slot'=>['suffix'=>['value'=>'点击生成','url'=>$this->table.'/code']],'rules'=>['required'=>true,'message'=>'单位编码不能为空']],
				['type'=>'upload','label'=>'图片','prop'=>'picture','value'=>$data->picture ?? [],'uploadAttrs'=>[
					'type'=>'card','limit'=>5,'size'=>'default','action'=>$this->host['api'].'upload'
				]],
				['type'=>'input','label'=>'法人代表','prop'=>'legal_name','value'=>$data->legal_name ?? '','rules'=>['required'=>true,'message'=>'法人代表不能为空']],
				['type'=>'input','label'=>'法人身份证号码','prop'=>'idcard','value'=>$data->idcard ?? '','rules'=>['required'=>true,'message'=>'法人身份证号码不能为空']],
				['type'=>'upload','label'=>'营业执照','prop'=>'license','value'=>$data->license ?? '','uploadAttrs'=>[
					'type'=>'img','limit'=>1,'size'=>'default','action'=>$this->host['api'].'upload'
				]],
				['type'=>'input','label'=>'统一信用代码','prop'=>'credit_code','value'=>$data->credit_code ?? '','rules'=>['required'=>true,'message'=>'统一信用代码不能为空']],
				['type'=>'input','label'=>'授权人','prop'=>'grantor_name','value'=>$data->grantor_name ?? '','rules'=>['required'=>true,'message'=>'授权人不能为空']],
				['type'=>'input','label'=>'授权人身份证号','prop'=>'grantor_idcard','value'=>$data->grantor_idcard ?? '','rules'=>['required'=>true,'message'=>'授权人身份证号不能为空']],
				['type'=>'input','label'=>'单位座机号码','prop'=>'telphone','value'=>$data->telphone ?? '','rules'=>['required'=>false,'message'=>'单位座机号码不能为空']],
				['type'=>'input','label'=>'单位手机号码','prop'=>'mobile','value'=>$data->mobile ?? '','rules'=>['required'=>false,'message'=>'单位手机号码不能为空']],
				['type'=>'input','label'=>'单位热线电话','prop'=>'hotline','value'=>$data->hotline ?? '','rules'=>['required'=>false,'message'=>'单位热线电话不能为空']],
				['type'=>'input','label'=>'单位邮箱地址','prop'=>'email','value'=>$data->email ?? '','rules'=>['required'=>false,'message'=>'单位邮箱地址不能为空']],
				['type'=>'input','label'=>'单位官网','prop'=>'website','value'=>$data->website ?? '','rules'=>['required'=>false,'message'=>'单位官网不能为空']],
				['type'=>'city','label'=>'所在城市','prop'=>'city','value'=>$id ? ['province'=>$data->province_id,'city'=>$data->city_id,'district'=>$data->district_id] : [],'rules'=>['required'=>true,'message'=>'请选择单位所在城市']],
				['type'=>'input','label'=>'详细地址','prop'=>'address','value'=>$data->address ?? '','rules'=>['required'=>true,'message'=>'详细地址不能为空']],
				['type'=>'editor','label'=>'单位介绍','prop'=>'content','value'=>$data->content ?? '','editorOptions'=>[
                    'type'=>'wang','image'=>['server'=>$this->host['api'].'upload/editor'],'video'=>['server'=>$this->host['api'].'upload/video']
                ],'rules'=>['required'=>true,'message'=>'单位介绍内容不能为空']],
			);

			return $action;
		}

		protected function getMapList(Request $request): array
		{
			return [
				['type'=>'varchar','label'=>'ID','prop'=>'id'],
				['type'=>'replace','label'=>'企业名称','prop'=>'enterprise_name','fields'=>['short_name'],'width'=>180],
				['type'=>'varchar','label'=>'企业编码','prop'=>'idcode','width'=>120],
				['type'=>'img','label'=>'图片','prop'=>'pic'],
				['type'=>'varchar','label'=>'法人代表','prop'=>'legal_name'],
				['type'=>'varchar','label'=>'身份证号','prop'=>'idcard'],
				['type'=>'varchar','label'=>'座机号码','prop'=>'telphone'],
				['type'=>'varchar','label'=>'手机号码','prop'=>'mobile'],
				['type'=>'varchar','label'=>'邮箱地址','prop'=>'email'],
				['type'=>'varchar','label'=>'客服热线','prop'=>'hotline'],
				['type'=>'link','label'=>'官网','prop'=>'website'],
				['type'=>'concat','label'=>'所在城市','prop'=>'province','fields'=>['city','district']],
				['type'=>'varchar','label'=>'认证用户','prop'=>'realname'],
				['type'=>'map','label'=>'状态','prop'=>'status','data'=>$this->status_value],
				['type'=>'date','label'=>'创建时间','prop'=>'create_time','format'=>['format'=>'Y-m-d H:i:s'],'width'=>180],
			];
		}
	}
?>