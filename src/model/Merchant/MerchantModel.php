<?php
	namespace dackou\model\Merchant;

	use support\Request;
	use support\Db;
	use dackou\Preg;
	use dackou\model\User\UserModel;

	class MerchantModel extends \dackou\Model{
		protected $table = 'Merchant';
		protected $title = '商户';
		protected $option_key = 'system';
		protected $status_value = [-1=>'已停用',0=>'待审核',1=>'正常'];
		protected $type_value = [0=>'形象店',1=>'旗舰店',2=>'专营店'];

		protected function getPageList(Request $request){
			try{
				$field = $this->getList($request,'field',true);
				array_push($field,'RealName as realname');
				array_push($field,'province.Title as province');
				array_push($field,'city.Title as city');
				array_push($field,'district.Title as district');
				$is_merchant = isset($this->config['system_is_merchant'])&&$this->config['system_is_merchant'] ? true : false;
				if($is_merchant){
					array_push($field,DB::raw('(SELECT COUNT(ID) FROM '.$this->prefix.'goods WHERE MerchantID = '.$this->tab.'.ID AND IsDel = 0) as goods_number'));
					array_push($field,DB::raw('(SELECT COUNT(ID) FROM '.$this->prefix.'order WHERE MerchantID = '.$this->tab.'.ID AND Status = 2) as order_count'));
				}
				$where = $this->getWhere($request);

				$rows = Db::table($this->table)
							->join('user',$this->table.'.UserID','=','user.AccountID')
							->join('city as province','ProvinceID','=','province.ID')
							->join('city as city','CityID','=','city.ID')
							->join('city as district','DistrictID','=','district.ID')
							->where($where)
							->count();
				[$offset,$limit] = $this->getLimit($request);
				$object = Db::table($this->table)
							->join('user',$this->table.'.UserID','=','user.AccountID')
							->join('city as province','ProvinceID','=','province.ID')
							->join('city as city','CityID','=','city.ID')
							->join('city as district','DistrictID','=','district.ID')
							->select(...$field)
							->where($where)
							->offset($offset)
							->limit($limit)
							->get();
				if($object){
					foreach($object as $k => $v){
						$object[$k]->create_time = $this->getDateTime($object[$k]->create_time);
						$object[$k]->city = $object[$k]->province . '-' . $object[$k]->city . '-' . $object[$k]->district;
						$object[$k]->status_title = $this->status_value[$object[$k]->status];

						unset($object[$k]->province);
						unset($object[$k]->district);
					}
				}

				return ['rows'=>$rows,'data'=>$object];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		protected function getUserList(Request $request,$user_id = 0){
			if(!$user_id || !is_numeric($user_id) || $user_id <= 0){
				return 100007;
			}

			try{
				$field = $this->getList($request,'field');
				$where = $this->getWhere($request);
				array_push($where,[$this->table.'.UserID','=',$user_id]);

				$object = Db::table($this->table)
							->select(...$field)
							->where($where)
							->get();
				return $object;
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		// 审核核准
		public function setChecked(Request $request,$id = 0){
			$id = $id ? $id : $request->post('id',0);
			if(!$id || !is_numeric($id) || $id <= 0){
				return 100007;
			}

			$data = $this->getList($request,$id);
			if(!$data || !is_object($data)){
				return 100007;
			}

			if($data->status === 1){
				return '无效的操作';
			}

			$user_id = $request->post('user_id',0);
			if(!$user_id || !is_numeric($user_id) || $user_id <= 0){
				return '无效的审核员';
			}

			$service = new UserModel();
			$user = $service->getList($request,$user_id);
			if(!$user || !is_object($user)){
				return '无效的审核员';
			}

			if($user->status !== 1){
				return '无效的审核员';
			}

			$type = $request->post('type');
			if(!in_array($type,['check'])){
				return '无效的审核参数';
			}
			$value = $request->post('value');
			if(!in_array($value,[0,1])){
				return '无效的审核值';
			}
			$value = $value === 1 ? 1 : -1;
			$content = trim($request->post('content',''));

			$sql = "UPDATE `".$this->tab."` SET Status = ? WHERE ID = ?";
			$param = [$value,$id];

			$result = Db::update($sql,$param);
			if($result !== false){
				if($value === 1){
					// 更新认证用户
					$service = new UserModel();
					$service->updateData($request,['IsAuth'=>1,'AuthTime'=>time(),'AuthType'=>'merchant','AuthName'=>$data->merchant_name,'AuthCode'=>$data->merchant_code],$data->user_id);
				}
				$service = new RecordMerchantModel();
				$service->insertData($request,['MerchantID'=>$id,'UserID'=>$user_id,'Value'=>$value,'Content'=>$content]);

				return ['code' => 0,'msg' => 'success'];
			}
			return '操作失败';
		}

		protected function validate(Request $request,$id = 0,$obj = null){
			$user_id = $request->post('user_id',0);
			$merchant_name = trim($request->post('merchant_name',''));
			$merchant_code = trim($request->post('merchant_code',''));
			$logo = $request->post('logo','');
			if(is_array($logo)){
				$logo = isset($logo[0]) ? $logo[0] : '';
			}
			$picture = $request->post('picture',[]);
			$legal_name = trim($request->post('legal_name',''));
			$credit_code = trim($request->post('credit_code',''));
			$contact_name = trim($request->post('contact_name',''));
			$telphone = trim($request->post('telphone',''));
			$mobile = trim($request->post('mobile',''));
			$hotline = trim($request->post('hotline',''));
			$email = trim($request->post('email',''));
			$website = trim($request->post('website',''));
			$city = $request->post('city',[]);
			$address = trim($request->post('address',''));
			$content = trim($request->post('content',''));

			if(!$user_id || !is_numeric($user_id) || $user_id <= 0){
				return '无效的用户';
			}
			$service = new UserModel();
			$user = $service->getList($request,$user_id);
			if(!$user || !is_object($user)){
				return '无效的用户';
			}
			if(!in_array($user->group_id,[14,15])){
				return '该用户不允许创建商户';
			}
			if($user->is_auth && $user->is_auth == 3){
				return '该用户已认证商户';
			}

			$merchant = $this->getList($request,'user',$user_id);
			if($merchant && is_array($merchant) && count($merchant)){
				return '该用户已认证商户';
			}

			if(!$merchant_name){
				return '商户名称不能为空';
			}
			if($this->checkExists(['MerchantName'=>$merchant_name],$id)){
				return '商户名称已存在,请重新输入';
			}

			if(!$merchant_code){
				$merchant_code = $this->createUniqueCode('MerchantCode',12);
			}
			if($this->checkExists(['MerchantCode'=>$merchant_code],$id)){
				return '商户编码已存在,请重新输入';
			}
			if(!$logo){
				// return '请上传商户logo';
			}
			if(!$mobile){
				return '手机号码不能为空';
			}
			if(!Preg::isMobile($mobile)){
				// return '手机号码格式不正确';
			}
			if(!$hotline){
				return '客服热线不能为空';
			}
			if(!$city){
				return '请选择商户所在城市';
			}
			if(!$address){
				return '详细地址不能为空';
			}

			if($id && $obj->status < 0){
				$data['status'] = 0;
			}

			$data['merchant_name'] = $merchant_name;
			$data['merchant_code'] = $merchant_code;
			$data['logo'] = $logo;
			$data['picture'] = $picture;
			$data['legal_name'] = $legal_name;
			$data['credit_code'] = $credit_code;
			$data['contact_name'] = $contact_name;
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
			$data['user_id'] = $user_id;	

			return $data;
		}

		protected function getActionList(Request $request,$id = 0): array
		{
			if($id){
				$data = $this->getList($request,$id);
			}

			$user_id = $request->input('user_id',0);

			if($user_id){
				$userForm = ['type'=>'input','label'=>'认证用户','prop'=>'user_id','value'=>$user_id,'hidden'=>true];
			}else{
				$service = new UserModel();
				$user = $service->getList($request,'option');

				$userForm = ['type'=>'select','label'=>'认证用户','prop'=>'user_id','value'=>$data->user_id ?? '','children'=>$user,'rules'=>['required'=>true,'message'=>'请选择认证用户']];
			}

			$action = [
				$userForm,
				['type'=>'input','label'=>'商户名称','prop'=>'merchant_name','value'=>$data->merchant_name ?? '','rules'=>['required'=>true,'message'=>'商户名称不能为空']],
				// ['type'=>'input','label'=>'商户编码','prop'=>'merchant_code','value'=>$data->merchant_code ?? '','slot'=>['suffix'=>['value'=>'自动生成','color'=>'#409EFF','callback'=>'code']],'rules'=>['required'=>true,'message'=>'商户编码不能为空']],
				['type'=>'upload','label'=>'商户Logo','prop'=>'logo','value'=>$data->logo ?? [],'uploadAttrs'=>[
					'type'=>'img','limit'=>1,'size'=>'small','action'=>$this->host['api'].'upload'
				],'rules'=>['required'=>false,'message'=>'请上传商户Logo']],
				['type'=>'upload','label'=>'商户图片','prop'=>'picture','value'=>$data->picture ?? [],'uploadAttrs'=>[
					'type'=>'card','limit'=>5,'size'=>'default','action'=>$this->host['api'].'upload'
				]],
				['type'=>'switch','label'=>'直营','prop'=>'is_direct','value'=>$data->is_direct ?? 0],
				['type'=>'radio-group','label'=>'授权类型','prop'=>'type','value'=>$data->type ?? 0,'children'=>[
					['type'=>'option','label'=>'形象店','value'=>0],
					['type'=>'option','label'=>'旗舰店','value'=>1],
					['type'=>'option','label'=>'专营店','value'=>2],
				]],
				['type'=>'input','label'=>'法人代表','prop'=>'legal_name','value'=>$data->legal_name ?? ''],
				['type'=>'input','label'=>'统一信用代码','prop'=>'credit_code','value'=>$data->credit_code ?? ''],
				['type'=>'input','label'=>'联系人','prop'=>'contact_name','value'=>$data->contact_name ?? ''],
				['type'=>'input','label'=>'公司电话','prop'=>'telphone','value'=>$data->telphone ?? ''],
				['type'=>'input','label'=>'手机号码','prop'=>'mobile','value'=>$data->mobile ?? '','rules'=>['required'=>true,'message'=>'手机号码不能为空']],
				['type'=>'input','label'=>'客服热线','prop'=>'hotline','value'=>$data->hotline ?? '','rules'=>['required'=>true,'message'=>'客服热线不能为空']],
				['type'=>'input','label'=>'邮箱地址','prop'=>'email','value'=>$data->email ?? ''],
				['type'=>'input','label'=>'企业网址','prop'=>'website','value'=>$data->website ?? ''],
				['type'=>'city','label'=>'所在城市','prop'=>'city','value'=>$id ? ['province'=>$data->province_id,'city'=>$data->city_id,'district'=>$data->district_id] : [],'rules'=>['required'=>true,'message'=>'请选择所在城市']],
				['type'=>'input','label'=>'详细地址','prop'=>'address','value'=>$data->address ?? '','rules'=>['required'=>true,'message'=>'详细地址不能为空']],
				['type'=>'editor','label'=>'商户详情','prop'=>'content','value'=>$data->content ?? '','editorOptions'=>[
					'type'=>'wang','image'=>['server'=>$this->host['api'].'upload/editor'],'video'=>['server'=>$this->host['api'].'upload/video']
				]]
			];

			// if($id && $data->status < 0){
			// 	array_unshift($action,['type'=>'input','label'=>'拒绝原因','prop'=>'status','value'=>'asdf']);
			// }

			return $action;
		}

		protected function getMapList(Request $request): array
		{
			$is_merchant = isset($this->config['system_is_merchant'])&&$this->config['system_is_merchant'] ? true : false;
			$map = [
				['type'=>'id','label'=>'ID','prop'=>'id'],
				['type'=>'varchar','label'=>'商户名称','prop'=>'merchant_name','width'=>180],
				['type'=>'varchar','label'=>'商户编码','prop'=>'merchant_code','width'=>150],
				['type'=>'img','label'=>'Logo','prop'=>'logo'],
				['type'=>'switch','label'=>'直营','prop'=>'is_direct'],
				['type'=>'map','label'=>'类型','prop'=>'type','data'=>$this->type_value],
				['type'=>'varchar','label'=>'法人代表','prop'=>'legal_name','width'=>100],
				['type'=>'varchar','label'=>'手机号码','prop'=>'mobile','width'=>130],
				['type'=>'varchar','label'=>'客服热线','prop'=>'hotline','width'=>140],
				['type'=>'varchar','label'=>'网址','prop'=>'website'],
				['type'=>'varchar','label'=>'所在城市','prop'=>'city','width'=>200],
				['type'=>'varchar','label'=>'认证用户','prop'=>'realname'],
			];
			if($is_merchant){
				array_push($map,['type'=>'varchar','label'=>'商品数','prop'=>'goods_number','callback'=>'goods']);
				array_push($map,['type'=>'varchar','label'=>'订单数','prop'=>'order_count','callback'=>'order']);
			}
			array_push($map,['type'=>'varchar','label'=>'入驻时间','prop'=>'create_time','width'=>180],
				['type'=>'map','label'=>'状态','prop'=>'status','data'=>[['label'=>'拒绝','value'=>-1,'color'=>'#b00101','callback'=>'resubmit'],['label'=>'待审核','value'=>0,'color'=>'#F56C6C','callback'=>'check'],['label'=>'正常','value'=>1,'color'=>'blue']]]);

			return $map;
		}
	}
?>