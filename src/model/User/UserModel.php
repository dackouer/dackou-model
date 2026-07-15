<?php
	namespace dackou\model\User;

	use support\Request;
	use support\Db;
	use Illuminate\Support\Facades\Schema;
	use dackou\Generateion;
	use dackou\Preg;
	use dackou\service\Token\TokenService;
	use dackou\model\Role\RoleModel;

	class UserModel extends \dackou\Model{
		protected $table = 'User';
		protected $title = '用户';
		protected $primaryKey = 'uid';
		protected $option_key = 'user';
    	protected $truncate = false;
    	protected $default_face = '';
    	protected $gender_value = [
    		['label'=>'未知','value'=>0],
    		['label'=>'男','value'=>1],
    		['label'=>'女','value'=>2]
    	];
    	protected $auth_value = [
    		['key'=>'','label'=>'未认证','value'=>0],
    		['key'=>'person','label'=>'个人','value'=>1],
    		['key'=>'enterprise','label'=>'企业','value'=>2],
    		['key'=>'merchant','label'=>'商户','value'=>3]
    	];
    	protected $status_value = [
    		['value'=>-2,'label'=>'已注销','color'=>'#909399'],
    		['value'=>-1,'label'=>'已删除','color'=>'red'],
    		['value'=>0,'label'=>'待激活','color'=>'#E6A23C'],
    		['value'=>1,'label'=>'正常','color'=>'#409EFF'],
    		['value'=>2,'label'=>'已锁定','color'=>'#F56C6C'],
    	];
    	protected $action_value = [
    		['key'=>'charge','title'=>'充值','desc'=>'会员充值'],
    		['key'=>'modpwd','title'=>'改密','desc'=>'修改用户密码'],
    		['key'=>'setpwd','title'=>'设密','desc'=>'设置密码'],
    		['key'=>'auth','title'=>'认证','desc'=>'实名认证'],
    		['key'=>'email','title'=>'邮件','desc'=>'发送邮件'],
    		['key'=>'sms','title'=>'短信','desc'=>'发送短信'],
    		['key'=>'active','title'=>'激活','desc'=>'激活会员'],
    		['key'=>'lock','title'=>'锁定','desc'=>'锁定会员'],
    		['key'=>'unlock','title'=>'解除','desc'=>'解除锁定'],
    		['key'=>'del','title'=>'删除','desc'=>'删除会员'],
    		['key'=>'cancel','title'=>'注销','desc'=>'注销会员'],
    	];

		protected function getDefaultData(Request $request){
			$data = [
				['id'=>100,'uid'=>0,'username'=>'admins','nickname'=>'admins','realname'=>'admins','face'=>'','idcard'=>'492817199205225271','birth'=>'1992-06-22','gender'=>1,'mobile'=>13977889988,'email'=>'admins@admins.com','role_id'=>1001,'is_auth'=>1,'auth_type'=>1,'auth_time'=>time(),'status'=>1],
				['id'=>101,'uid'=>0,'username'=>'admin','nickname'=>'admin','realname'=>'admin','face'=>'','idcard'=>'492817199106225272','birth'=>'1991-06-22','gender'=>1,'mobile'=>13877889988,'email'=>'admin@admin.com','role_id'=>1101,'is_auth'=>1,'auth_type'=>1,'auth_time'=>time(),'status'=>1],
				['id'=>102,'uid'=>0,'username'=>'apier','nickname'=>'apier','realname'=>'apier','face'=>'','idcard'=>'492817199007225273','birth'=>'1990-06-22','gender'=>1,'mobile'=>13577889988,'email'=>'apier@apier.com','role_id'=>1201,'is_auth'=>1,'auth_type'=>1,'auth_time'=>time(),'status'=>1]
			];
			foreach($data as $key => $val){
				$gener = Generateion::create();
				$data[$key]['uuid'] = $gener['uuid'];
				$data[$key]['uid'] = $gener['uid'];
				$data[$key]['token'] = $gener['token'];
				$data[$key]['invite_code'] = $gener['invite'];
				// $password = isset($this->config['default_password']) ? $this->config['default_password'] : substr($data[$key]['idcard'], 12,6);
				$data[$key]['password'] = $key === 1 ? $this->makePassword('Admin.'.date('Y')) : $this->makePassword($this->getDefaultPassword($data[$key]['idcard']));
				$data[$key]['create_ip'] = $request->getRealIp($safe_mode=true);
				$data[$key]['ip_address'] = $this->getIPAddress($request);
				if(!$data[$key]['idcard']){
					$data[$key]['idcard'] = $gener['idcard'];
				}
			}
			return $data;
		}

		protected function getDefaultPassword($idcard = ''){
			return isset($this->config['user_default_password']) ? $this->config['user_default_password'] : ($idcard ? substr($idcard, 12,6) : '');
		}

		protected function getDefaultRoleID(Request $request,$flag = false){
			$role_id = $this->getConfig('user_default_reg_role_id',true);
			if(!$role_id){
				$_class_name = $this->getClassName('Role');
				$service = new $_class_name();
				$obj = $service->getList($request,'default');
				if(!$obj || !is_object($obj)){
					return '无效的角色';
				}
				return $flag ? $obj : $obj->id;
			}else{
				if(!$flag){
					return $role_id;
				}
				$_class_name = $this->getClassName('Role');
				$service = new $_class_name();
				$obj = $service->getList($request,$role_id);
				if(!$obj || !is_object($obj)){
					return '无效的角色';
				}
				return $obj;
			}
		}

		/**
		 * [getFieldList description]
		 * @param  Request       $request [description]
		 * @param  mixed|boolean $flag    [description]
		 * @return [type]                 [description]
		 */
		protected function getFieldList(Request $request,mixed $flag = false): array
		{
			$fields = [
				'id' => 'ID',
				'uid' => 'AccountID',
				'username' => 'UserName',
				'nickname' => 'NickName',
				'realname' => 'RealName',
				'mobile' => 'Mobile',
				'email' => 'Email',
				'face' => 'Face',
				'desc' => 'Desc',
				'qq' => 'QQ',
				'openid' => 'OpenidWechat',
				'gender' => 'Gender',
				'idcard' => 'Idcard',
				'birth' => 'Birth',
				'age' => 'Age',
				'height' => 'Height',
				'weight' => 'Weight',
				'nation' => 'Nation',
				'zodiac' => 'Zodiac',
				'constellation' => 'Constellation',
				'intro' => 'Intro',
				'password' => 'Authentication',
				'province_id' => 'ProvinceID',
				'city_id' => 'CityID',
				'district_id' => 'DistrictID',
				'address' => 'Address',
				'city_name' => 'CityName',
				'longitude' => 'Longitude',
				'latitude' => 'Latitude',
				'hobby' => 'Hobby',
				'super_id' => 'SuperID',
				'parent_id' => 'ParentID',
				'direct_share' => 'DirectShare',
				'total_share' => 'TotalShare',
				'score' => 'Score',
				'coin' => 'Coin',
				'balance' => 'Balance',
				'total_charge' => 'TotalCharge',
				'total_order' => 'TotalOrder',
				'total_consume' => 'TotalConsume',
				'role_id' => 'RoleID',
				'is_auth' => 'IsAuth',
				'auth_type' => 'AuthType',
				'auth_name' => 'AuthName',
				'auth_code' => 'AuthCode',
				'merchant_id' => 'MerchantID',
				'store_id' => 'StoreID',
				'invite_code' => 'InviteCode',
				'invite' => 'Invite',
				'fans' => 'Fans',
				'followers' => 'Followers',
				'is_private' => 'IsPrivate',
				'language' => 'Language',
				'payment' => 'Payment',
				'activity_id' => 'ActivityID',
				'ip_address' => 'IpAddress',
				'reg_os' => 'RegOs',
				'is_mobile_reg' => 'IsMobileReg',
				'visitorid' => 'Visitorid',
				'last_login_time' => 'LastLoginTime',
				'last_login_ip' => 'LastLoginIp',
				'login_count' => 'LoginCount',
				'is_online' => 'IsOnline',
				'online_time' => 'OnlineTime',
				'status' => 'Status',
				'token' => 'Token',
				'create_time' => 'CreateTime',
			];

			if($flag === true){
				$field = [];
				foreach($fields as $k => $v){
					array_push($field,$this->table.'.'.$v . ' as ' . $k);
				}
    			return $field;
    		}elseif($flag === false){
				$field = [];
				foreach($fields as $k => $v){
					array_push($field,$v . ' as ' . $k);
				}
    			return $field;
    		}elseif($flag === 'key'){
    			return $fields;
    		}else{
    			return $fields;
    		}
		}

		/**
		 * [getPageList description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		protected function getPageList(Request $request): array
		{
			$role_id = $request->input('role_id',0);
			$group_id = $request->input('group_id',[]);
			if($role_id || $group_id){
				$this->action_value = [];
			}

			$field = $this->getList($request,'field',true);
			array_push($field,'role.Title as role_name');
			array_push($field,'role.PID as group_id');

			$where = $this->getWhere($request);
			array_push($where,['role.PID','<>',10]);
			array_push($where,['role.PID','<>',12]);

			$role_id = $request->input('role_id');
			if(is_array($role_id)){
				$role_id = end($role_id);
			}
			if($role_id){
				array_push($where,[$this->table.'.RoleID','=',$role_id]);
			}

			$parent_id = $request->input('parent_id',0);
			if($parent_id){
				array_push($where,[$this->table.'.ParentID','=',$parent_id]);
			}

			$status = $request->input('status');
			if(is_numeric($status) && in_array($status,[-2,-1,0,1,2])){
				array_push($where,[$this->table.'.Status','=',$status]);
			}

			$dtime = $request->input('dtime');
			if($dtime){
				$start_time = 0;
				$end_time = 0;
				if(is_string($dtime)){
					$start_time = \strtotime($dtime);
				}elseif(is_array($dtime)){
					if(isset($dtime[0])){
						$start_time = \strtotime($dtime[0]);
					}
					if(isset($dtime[1])){
						$end_time = \strtotime($dtime[1]);
					}
				}
				if($start_time){
					array_push($where,[$this->table.'.CreateTime','>=',$start_time]);
				}
				if($end_time){
					array_push($where,[$this->table.'.CreateTime','<=',$end_time]);
				}
			}

			$group_id = $request->input('group_id');
			if(!empty($group_id) && $group_id){
				if(is_string($group_id)){
					$group_id = \explode(',',$group_id);
				}
			}
			$keyword = trim($request->input('keyword',''));

			$invite = $request->input('invite','');
			if($invite){
				array_push($where,[$this->table.'.ParentID','=',$invite]);
			}

			$rows = Db::table($this->table)
						->join('role','RoleID','=','role.ID')
						->where($where);
			if($group_id && count($group_id)){
				$rows = $rows->whereIn('role.PID',$group_id);
			}
			if(!empty($keyword) && $keyword){
				$rows = $rows->where(function($query) use ($keyword){
					$query->where($this->table.'.AccountID','like','%'.$keyword.'%')
						  ->orWhere($this->table.'.UserName','like','%'.$keyword.'%')
						  ->orWhere($this->table.'.NickName','like','%'.$keyword.'%')
						  ->orWhere($this->table.'.Idcard','like','%'.$keyword.'%')
						  ->orWhere($this->table.'.Mobile','like','%'.$keyword.'%')
						  ->orWhere($this->table.'.Email','like','%'.$keyword.'%');
				});
			}
			$rows = $rows->count();

			[$offset,$limit] = $this->getLimit($request);
			$object = Db::table($this->table)
						->join('role','RoleID','=','role.ID')
						->select(...$field)
						->where($where);
			if($group_id && count($group_id)){
				$object = $object->whereIn('role.PID',$group_id);
			}
			if(!empty($keyword) && $keyword){
				$object = $object->where(function($query) use ($keyword){
					$query->where($this->table.'.AccountID','like','%'.$keyword.'%')
						  ->orWhere($this->table.'.UserName','like','%'.$keyword.'%')
						  ->orWhere($this->table.'.NickName','like','%'.$keyword.'%')
						  ->orWhere($this->table.'.Idcard','like','%'.$keyword.'%')
						  ->orWhere($this->table.'.Mobile','like','%'.$keyword.'%')
						  ->orWhere($this->table.'.Email','like','%'.$keyword.'%');
				});
			}
			$object = $object->offset($offset)
						->limit($limit)
						->get();
			if($object){
				foreach($object as $key => $val){
					$object[$key]->create_time = $this->getDateTime($object[$key]->create_time);
					$object[$key]->score /= 100;
					$object[$key]->balance /= 100;
					$object[$key]->coin /= 100;

					if(!$object[$key]->age && $object[$key]->birth){
						$object[$key]->age = $this->getAge($object[$key]->birth);
					}

					if(!$object[$key]->constellation && $object[$key]->idcard){
						$object[$key]->constellation = $this->getConstellation($object[$key]->idcard);
					}

					if(!$object[$key]->zodiac && $object[$key]->idcard){
						$object[$key]->zodiac = $this->getZodiac($object[$key]->idcard);
					}

					if(!$this->checkIsAdmin($request)){
						$object[$key]->mobile = $this->getEncodeStr($object[$key]->mobile,'mobile');
						$object[$key]->idcard = $this->getEncodeStr($object[$key]->idcard,'idcard');
						$object[$key]->email = $this->getEncodeStr($object[$key]->email,'email');
					}

					$object[$key]->auth_type = $this->getAuthType($object[$key]->auth_type);

					unset($object[$key]->password);
					unset($object[$key]->token);
					unset($object[$key]->sign);
				}
			}
			return ['rows' => $rows,'data' => $object];
		}

		private function getAuthType($type){
			$str = '';
			foreach($this->auth_value as $item){
				if($item['key'] === $type){
					$str = $item['label'];
					break;
				}
			}
			return $str;
		}

		protected function getListById(Request $request,$id = 0){
			if(!$id){
				return [];
			}
			
			$field = $this->getList($request,'field',true);
			array_push($field,'role.Title as role_name');
			array_push($field,'role.Pic as role_pic');
			array_push($field,'role.Sign as sign');
			array_push($field,'role.PID as group_id');
			array_push($field,'role.IsAdmin as is_admin');
			array_push($field,'role.IsOrder as is_order');
			$where = $this->getWhere($request);
			array_push($where,[$this->table.'.AccountID','=',$id]);

			$object = Db::table($this->table)
							->join('role','RoleID','=','role.ID')
							->select(...$field)
							->where($where)
							->first();
			if($object){
				unset($object->password);
				unset($object->token);
				unset($object->sign);
				
				$object->create_time = $this->getDateTime($object->create_time);
				$object->score /= 100;
				$object->balance /= 100;
				$object->coin /= 100;

				if(!$object->age && $object->birth){
					$object->age = $this->getAge($object->birth);
				}

				if(!$object->constellation && $object->idcard){
					$object->constellation = $this->getConstellation($object->idcard);
				}

				if(!$object->zodiac && $object->idcard){
					$object->zodiac = $this->getZodiac($object->idcard);
				}

				$userAgent = $request->header('User-Agent');
				if($userAgent === 'apifox'){
					$object->mobile = $this->getEncodeStr($object->mobile,'mobile');
					$object->idcard = $this->getEncodeStr($object->idcard,'idcard');
					$object->email = $this->getEncodeStr($object->email,'email');
				}

				// $object->book_status = 0;
				$friend_id = $request->post('friend_id',0);
				if($friend_id){
					$_class_name = $this->getClassName('Book');
					if($_class_name){
						$service = new $_class_name();
						$result = $service->getList($request,'friend',$friend_id,$id);
						// var_dump('get '.$friend_id.' book user '.$id.': ',$result);
						if($result && is_object($result)){
							$object->book_status = $result->status;
						}
					}
				}

				var_dump('aa');
				$object->order = [];
				$_class_name = $this->getClassName('Order');
				$service = new $_class_name();
				$result = $service->getList($request,'status',$id);
				var_dump('result: ',$result);
				if($result){
					$object->order = $result;
				}
			}
			return $object;
		}

		/**
		 * 根据手机号查用户
		 * @param  Request $request [description]
		 * @param  integer $mobile  [description]
		 * @return [type]           [description]
		 */
		protected function getMobileList(Request $request,$mobile = 0){
			if(!$mobile){
				return [];
			}

			$field = $this->getList($request,'field',true);
			array_push($field,'Title as role_name');
			array_push($field,'role.Pic as role_pic');
			array_push($field,'Sign as sign');
			array_push($field,'PID as group_id');
			array_push($field,'IsAdmin as is_admin');
			$where = $this->getWhere($request);
			array_push($where,[$this->table.'.Mobile','=',$mobile]);

			$object = Db::table($this->table)
							->join('role','RoleID','=','role.ID')
							->select(...$field)
							->where($where)
							->first();
			if($object){
				unset($object->password);
			}
			return $object;
		}

		protected function getQueryList(Request $request): array
		{
			$keyword = $request->input('keyword','');
			$dtime = $request->input('dtime',[]);
			$status = $request->input('status','');
			$statusOption = [['type'=>'option','label'=>'-All-','value'=>'']];
			foreach($this->status_value as $item){
				array_push($statusOption,['type'=>'option','label'=>$item['label'],'value'=>(int)$item['value']]);
			}
			$role_id = $request->input('role_id');
			$group_id = $request->input('group_id');

			$service = new RoleModel();
			$role = $service->getList($request,'option');

			$query = [
				['type'=>'input','label'=>'关键词','prop'=>'keyword','value'=>$keyword],
				['type'=>'date-picker','label'=>'注册从','prop'=>'dtime','value'=>$dtime,'attrs'=>['type'=>'daterange','range-separator'=>'到','start-placeholder'=>'开始时间','end-placeholder'=>'结束时间']]
			];
			// var_dump('role_id: ',$group_id);
			if(!$role_id && !$group_id){
				array_push($query,['type'=>'cascader','label'=>'角色','prop'=>'role_id','value'=>$role_id??[],'attrs'=>['placeholder'=>'选择角色','style'=>['width'=>'80px'],'options'=>$role]]
				);
			}
			array_push($query,['type'=>'select','label'=>'状态','prop'=>'status','value'=>$status,'children'=>$statusOption,'attrs'=>['clearable'=>true,'style'=>['width'=>'100px']]]
			);

			return $query;
		}

		protected function getAddressList(Request $request,$user_id,$type = 'option'){
			if(!$user_id){
				return [];
			}
		}
    	
		protected function getOptionList(Request $request,mixed $param = null,array $disabled = [],array $fields = [])
		{
			if(!$param || is_null($param)){
				return [];
			}

			$field = ['NickName as nickname','RealName as realname','Mobile as mobile','Email as email','AccountID as uid','ProvinceID as province_id','CityID as city_id','DistrictID as district_id','Address as address'];
			$where = [[$this->table.'.Status','=',1],[$this->table.'.IsDel','=',0],['role.IsDel','=',0]];
			if($disabled){
				foreach($disabled as $k => $v){
					$k = $this->convert($k);
					array_push($where,[$this->table.$k,'=',$v]);
				}
			}

			$whereIn = [];
			$wherePID = [];
			$symbol = [];
			$symbols = [];

			if(is_numeric($param)){
				if($param > 100){
					array_push($where,['RoleID','=',$param]);
				}else{
					array_push($where,['PID','=',$param]);
				}
			}elseif(is_array($param)){
				foreach($param as $val){
					if(is_numeric($val)){
						if($val > 100){
							array_push($whereIn,$val);
						}else{
							array_push($wherePID,$val);
						}
					}elseif(is_string($val)){
						if(substr($val,-1) === 's'){
							array_push($symbols,$val);
						}else{
							array_push($symbol,$val);
						}
					}
				}
			}elseif(is_string($param)){
				if(strpos($param,',') !== false){
					$param = explode(',',$param);
					foreach($param as $val){
						if(is_numeric($val)){
							if($val > 100){
								array_push($whereIn,$val);
							}else{
								array_push($wherePID,$val);
							}
						}elseif(is_string($val)){
							if(substr($val,-1) === 's'){
								array_push($symbols,$val);
							}else{
								array_push($symbol,$val);
							}
						}
					}
				}else{
					if(substr($param,-1) === 's'){
						array_push($symbols,$param);
					}else{
						array_push($symbol,$param);
					}
				}
			}else{
				return [];
			}

			if($symbol){
				$obj = Db::table('role')->select('ID as id')->whereIn('Symbol',$symbol)->get();
				if($obj){
					foreach($obj as $item){
						array_push($whereIn,$item->id);
					}
				}
			}

			if($symbols){
				$obj = Db::table('role')->select('ID as id')->whereIn('Symbol',$symbols)->get();
				if($obj){
					foreach($obj as $item){
						array_push($wherePID,$item->id);
					}
				}
			}
			
			$object = Db::table($this->table)
						->join('role','RoleID','=','role.ID')
						->select(...$field)
						->where($where);
			if($whereIn){
				$object = $object->whereIn('RoleID',$whereIn);
			}
			if($wherePID){
				$object = $object->whereIn('PID',$wherePID);
			}
			$object = $object->get();

			if($object){
				$result = [];
				foreach($object as $item){
					$label = $item->nickname ?? $item->realname ?? $item->mobile ?? $item->email ?? $item->uid ?? '';
					$value = (int)$item->uid;
					array_push($result,['label'=>$label,'value'=>$value,'realname'=>$item->realname,'mobile'=>$item->mobile,'email'=>$item->email,'province_id'=>$item->province_id,'city_id'=>$item->city_id,'district_id'=>$item->district_id,'address'=>$item->address]);
				}

				return $result;
			}

			return [];
		}

		/**
		 * [getApiList description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		protected function getApiList(Request $request,$pid = 12): mixed
		{
			try{
				$field = $this->getList($request,'field',true);
				$field = [
					$this->table.'.ID as id',
					'AccountID as uid',
					$this->table.'.Token as token',
					'RoleID as role_id',
					'Sign as sign',
					'PID as group_id',
					'IsAdmin as is_admin',
				];
				$where = [
					[$this->table.'.IsDel','=',0],
					['role.PID','=',$pid]
				];

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

		/**
		 * 通过role_id获取用户
		 * @param  Request $request [description]
		 * @param  integer $role_id [description]
		 * @return [type]           [description]
		 */
		protected function getRoleList(Request $request,$role_id = 0){
			if(!$role_id || is_null($role_id)){
				return 100007;
			}

			$field = $this->getList($request,'field',true);
			$where = $this->getWhere($request);

			if(is_numeric($role_id) || (is_string($role_id) && ctype_digit($role_id))){
				if($role_id > 100){
					array_push($where,['PID','=',$role_id]);
				}else{
					array_push($where,['RoleID','=',$role_id]);
				}

				$object = Db::table($this->table)
							->join('role','RoleID','=','role.ID')
							->select(...$field)
							->where($where)
							->get();
			}elseif(is_string($role_id)){
				if(preg_match('/^[a-zA-Z]+$/', $role_id)){
					if(substr($role_id,-1) === 's'){
						$object = [];
						$obj = Db::table('role')
									->select('ID as id')
									->where('Symbol',$role_id)
									->first();
						if($obj){
							array_push($where,['PID','=',$obj->id]);
							$object = Db::table($this->table)
										->join('role','RoleID','=','role.ID')
										->select(...$field)
										->where($where)
										->get();
						}
					}else{
						array_push($where,['Symbol','=',$role_id]);
					}

					$object = Db::table($this->table)
							->join('role','RoleID','=','role.ID')
							->select(...$field)
							->where($where)
							->get();
				}else{
					$role_id = explode(',',$role_id);
					$object = Db::table($this->table)
							->join('role','RoleID','=','role.ID')
							->select(...$field)
							->where($where)
							->whereIn('RoleID',$role_id)
							->get();
				}
			}elseif(is_array($role_id)){
				$object = Db::table($this->table)
							->join('role','RoleID','=','role.ID')
							->select(...$field)
							->where($where)
							->whereIn('RoleID',$role_id)
							->get();
			}

			return $object;
		}

		/**
		 * 通过邀请码获取用户
		 * @param  Request $request [description]
		 * @param  string  $invite  [description]
		 * @return [type]           [description]
		 */
		protected function getInviteList(Request $request,$invite = ''){
			try{
				if(!$invite){
					return [];
				}

				$field = $this->getList($request,'field',true);
				array_push($field,'Title as role_name');
				array_push($field,'role.Pic as role_pic');
				array_push($field,'Sign as sign');
				array_push($field,'PID as group_id');
				array_push($field,'IsAdmin as is_admin');
				$where = $this->getWhere($request);
				array_push($where,[$this->table.'.InviteCode','=',$invite]);

				$object = Db::table($this->table)
								->join('role','RoleID','=','role.ID')
								->select(...$field)
								->where($where)
								->first();
				return $this->getResultData($object);
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		/**
		 * 直接注册用户
		 * @param  Request $request [description]
		 * @param  [type]  $data    [description]
		 * @return [type]           [description]
		 */
		public function register(Request $request,$data){
			try{
				$resp = $this->setRequest($request);
				if($resp){
					$data = array_merge($data,$resp);
				}
				if(!$data){
					return ['code'=>1,'msg'=>'无效的数据'];
				}
				$res = $this->insertData($request,$data);
				if($res){
					$user = [
						'uid' 	  => $data['uid'],
						'token'   => $data['token'],
						'sign' 	  => $data['sign'],
						'role_id' => $data['role_id'],
					];
					$token = TokenService::generateToken($request,$this->arrayToObject($user));
					$result = [
						'uid' => $data['uid'],
						'nickname' => isset($data['nickname'])&&$data['nickname'] ? $data['nickname'] : (isset($data['username'])&&$data['username'] ? $data['username'] : (isset($data['mobile'])&&$data['mobile'] ? $data['mobile'] : $data['uid'])),
						'face' => isset($data['face'])&&$data['face'] ? $data['face'] : $this->default_face,
					];
					if(isset($data['mobile'])){
						$result['mobile'] = $data['mobile'];
					}
					$result['expire_time'] = $token['expire_time'];
					$result['token'] = $token['token'];
					
					return $this->arrayToObject($result);
				}
				return ['code'=>1,'msg'=>'用户注册失败'];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		public function setAuth(Request $request,$data,$role_id,$auth_value = 1){
			try{
				$arr = [
					'RoleID' 	 => $role_id,
					'IsAuth' 	 => $auth_value,
					'AuthType'   => $this->auth_value[$auth_value],
					'AuthCode'	 => isset($data['idcode']) ? $data['idcode'] : '',
					'AuthName'   => $auth_value === 1 ? $data['realname'] : ($auth_value === 2 ? $data['enterprise_name'] : $data['merchant_name']),
					'AuthTime'   => time()
				];
				if(isset($data['is_realname']) && !$data['is_realname']){
					$arr['RealName'] = $auth_value === 1 ? $data['realname'] : $data['legal_name'];
				}
				if(isset($data['is_idcard']) && !$data['is_idcard']){
					$arr['Idcard'] = $data['idcard'];
				}
				if(isset($data['is_mobile']) && !$data['is_mobile']){
					$arr['Mobile'] = $data['mobile'];
				}
				if($auth_value === 3){
					$arr['MerchantID'] = $data['id'];
				}
				$result = $this->updateData($request,$arr,$data['user_id']);

				return $result !== false ? true : false;
			}catch(\Exception $e){
				// var_dump('error: '.$e->getMessage());
				return false;
			}
		}

		/**
		 * [updateUserData description]
		 * @param  Request $request [description]
		 * @param  [type]  $data    [description]
		 * @return [type]           [description]
		 */
		public function updateUserData(Request $request,$data,$uid = 0){
			try{
				$action = $request->input('action',(isset($data['action']) ? $data['action'] : ''));
				switch($action){
					case '':
					case 'add':
						$share_score = $data['share_score'];
						$share_balance = $data['share_balance'];
						$share_coin = $data['share_coin'];
						$param = [];
						$sql = " UPDATE `".$this->tab."` SET ";
						if($share_score){
							$sql .= "`Score` = `Score` + ?,";
							array_push($param,$share_score);
						}
						if($share_balance){
							$sql .= "`Balance` = `Balance` + ?,";
							array_push($param,$share_balance);
						}
						if($share_coin){
							$sql .= "`Coin` = `Coin` + ?,";
							array_push($param,$share_coin);
						}

						$sql .= "`UpdateTime` = ?,`UpdateTime` = ? WHERE `AccountID` = ?";
						var_dump('score: '.$share_score.' balance: '.$share_balance.' coin: '.$share_coin);
						var_dump('update user add sql: '.$sql);
						array_push($param,time());
						array_push($param,$this->getRealIp($safe_mode = true));
						array_push($param,$uid);

						$result = Db::update($sql,$param);
						if($result !== false){
							if($share_score){
								$arr = [
									'cate_id'   => 1,
									'user_id'   => $uid,
									'source_id' => isset($data['source_id']) ? $data['source_id'] : 0,
									'value'	    => $share_score/100,
									'content'   => '分享赠送'
								];
								$_class_name = $this->getClassName('Record');
								$service = new $_class_name();
								$service->insertData($request,$arr);
							}
							if($share_balance){
								$arr = [
									'cate_id'   => 2,
									'user_id'   => $uid,
									'source_id' => isset($data['source_id']) ? $data['source_id'] : 0,
									'value'	    => $share_balance/100,
									'content'   => '分享赠送'
								];
								$_class_name = $this->getClassName('Record');
								$service = new $_class_name();
								$service->insertData($request,$arr);
							}
							if($share_coin){
								$arr = [
									'cate_id'   => 3,
									'user_id'   => $uid,
									'source_id' => isset($data['source_id']) ? $data['source_id'] : 0,
									'value'	    => $share_coin/100,
									'content'   => '分享赠送'
								];
								$_class_name = $this->getClassName('Record');
								$service = new $_class_name();
								$service->insertData($request,$arr);
							}

							return true;
						}
						return '更新失败';
						break;
					case 'charge':
						$key = $data['charge_type'] === 1 ? "`Score`" : ($data['charge_type']===2 ? "`Balance`" : "`Coin`");
						$sql = "UPDATE `".$this->tab."` SET {$key} = {$key} + ? WHERE `AccountID` = ?";
						$param = [$data['amount'],$uid];
						$result = Db::update($sql,$param);
						if($result !== false){
							$arr = [
								'cate_id'=>$data['charge_type'],
								'user_id'=>$uid,
								'value'=>$data['amount']/100,
								'content'=>$data['charge_type']===1 ? '积分充值' : ($data['charge_type']===1 ? '余额充值' : $this->coin_name.'充值')
							];
							$_class_name = $this->getClassName('Record');
							$service = new $_class_name();
							return $service->insertData($request,$arr);
						}
						return '充值失败';
						break;
					default:
						return 100007;
				}
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		public function updateBalance(Request $request,$value,$user_id,$type = 'balance',$mode = false){
			try{
				// var_dump('开始更新用户余额');
				$type = \ucfirst($type);
				if(!$mode){
					$sql = "UPDATE `".$this->tab."` SET {$type} = {$type} - ? WHERE `AccountID` = ?";
				}else{
					$sql = "UPDATE `".$this->tab."` SET {$type} = {$type} + ? WHERE `AccountID` = ?";
				}
				// var_dump('update user balance sql: '.$sql);
				$result = Db::update($sql,[$value,$user_id]);
				return $result !== false ? true : false;
			}catch(\Exception $e){
				return false;
			}
		}

		protected function sendEmail(Request $request,$data,$id = 0){
			try{
				return '邮件参数配置有误';
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		protected function sendSms(Request $request,$data,$id = 0){
			try{
				return '短信参数配置有误';
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		/**
		 * 检查用户
		 * @param  Request $request [description]
		 * @param  integer $uid     [description]
		 * @return [type]           [description]
		 */
		public function checkUser(Request $request,$uid = 0){
			try{
				$user = $this->getTokenData($request);
				// var_dump('check user: ',$user);
				if($user->rid === 1201){
					return 100009;
				}
				
				if(!$uid || !is_numeric($uid) || $uid < 0){
					return 100007;
				}

				$res = $this->getList($request,$uid);
				// var_dump($res);
				if(!$res || !is_object($res)){
					return $res;
				}
				
				if(!$res->status){
					return '用户未激活';
				}
				if($res->status === 2){
					return '用户已锁定';
				}
				if($res->status !== 1){
					return '用户状态异常';
				}

				$expire_time = $user->expire;
				$new_token = \dackou\service\Token\TokenService::getToken($request,true);

				if(strtotime($expire_time) <= time() - 100000){
					$token = TokenService::generateToken($request,$res);
					$expire_time = $token['expire_time'];
					$new_token = $token['token'];
				}
				
				// var_dump('token: ',$token);
				return [
					'uid' 	   	  => $uid,
					'nickname' 	  => $res->nickname,
					'invite'   	  => $res->invite_code,
					'token'	   	  => $new_token,
					'expire_time' => $expire_time
				];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		protected function setRequest(Request $request,$id = 0){
			if(!$id){
				$gener = Generateion::create();
				$data['uuid'] = $gener['uuid'];
				$data['uid'] = $gener['uid'];
				$data['token'] = $gener['token'];
				$data['invite_code'] = $gener['invite'];
				$password = isset($this->config['default_password']) ? $this->config['default_password'] : 'F135246';
				$data['password'] = $this->makePassword($password);
				$data['create_time'] = time();
				$data['create_ip'] = $request->getRealIp($safe_mode=true);
				$data['ip_address'] = $this->getIPAddress($request);
				$data['status'] = 1;
				$role_id = $request->input('role_id',0);
				if(!$role_id){
					$role = $this->getDefaultRoleID($request,true);
					if($role && is_object($role)){
						$data['role_id'] = $role->id;
						$data['sign'] = $role->sign;
					}
				}
				$invite = $request->input('invite',0);
				if($invite){
					$resp = is_numeric($invite) ? $this->getList($request,$invite) : $resp = $this->getList($request,'invite',$invite);
					if($resp && is_object($resp)){
						$data['invite'] = $invite;
						$data['parent_id'] = $resp->uid;
						if($resp->parent_id){
							$data['super_id'] = $resp->parent_id;
						}
					}
				}
				$aid = $request->input('aid',$request->input('acitvity_id',0));
				if($aid){
					$data['activity_id'] = $aid;
				}
			}else{
				$data['update_time'] = time();
			}

			return $data;
		}

		/**
		 * 清空会员用户
		 * @param Request $request [description]
		 */
		public function setClean(Request $request){
			try{
				$group_id = $this->getTokenData($request,'group_id');
				if(!$group_id || $group_id > 11){
					return '非法操作';
				}

				$data = Db::table($this->table)
							->join('role','RoleID','=','role.ID')
							->select($this->table.'.ID as id')
							->where([['PID','<',13]])
							->get();
				if(!$data || !count($data)){
					return false;
				}
				$max_id = $data[count($data)-1]->id;
				
				$sql = "DELETE FROM `".$this->tab."` WHERE ID > ?";
				$result = Db::delete($sql,[$max_id]);
				if($result !== false){
					Db::select("ALTER TABLE `".$this->tab."` AUTO_INCREMENT = ".($max_id+1).";");
					return true;
				}

				return false;
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		protected function setExcute(Request $request,$data,$flag = false){
			var_dump('flag: '.$flag);
			var_dump($data);
			switch($flag){
				case '':
				case 'add':
					$uid = $data['uid'];
					$score = isset($data['score']) ? $data['score'] : 0;
					$balance = isset($data['balance']) ? $data['balance'] : 0;
					$coin = isset($data['coin']) ? $data['coin'] : 0;
					var_dump('uid: '.$uid);
					var_dump('score: '.$score);
					var_dump('balance: '.$balance);
					var_dump('coin: '.$coin);
					if($score){
						$arr = [
							'cate_id'   => 1,
							'user_id'   => $uid,
							'source_id' => 0,
							'value'	    => $score/100,
							'content'   => '注册赠送'
						];
						$_class_name = $this->getClassName('Record');
						$service = new $_class_name();
						$service->insertData($request,$arr);
					}

					if($balance){
						$arr = [
							'cate_id'   => 2,
							'user_id'   => $uid,
							'source_id' => 0,
							'value'	    => $balance/100,
							'content'   => '注册赠送'
						];
						$_class_name = $this->getClassName('Record');
						$service = new $_class_name();
						$service->insertData($request,$arr);
					}

					if($coin){
						$arr = [
							'cate_id'   => 3,
							'user_id'   => $uid,
							'source_id' => 0,
							'value'	    => $coin/100,
							'content'   => '注册赠送'
						];
						$_class_name = $this->getClassName('Record');
						$service = new $_class_name();
						$service->insertData($request,$arr);
					}

					if(isset($data['parent_id']) && $data['parent_id']){
						$this->setIncrement('DirectShare',$data['parent_id']);

						$share_score = $this->getConfig('user_share_send_score');
						$share_balance = $this->getConfig('user_share_send_balance');
						$share_coin = $this->getConfig('user_share_send_coin');
						if($share_score || $share_balance || $share_coin){
							$data['share_score'] = $share_score*100;
							$data['share_balance'] = $share_balance*100;
							$data['share_coin'] = $share_coin*100;
							$data['source_id'] = $data['uid'];
							$this->updateUserData($request,$data,$data['parent_id']);
						}

						if(isset($data['super_id']) && $data['super_id']){
							$share_score = $this->getConfig('user_share_super_score');
							$share_balance = $this->getConfig('user_share_super_balance');
							$share_coin = $this->getConfig('user_share_super_coin');
							if($share_score || $share_balance || $share_coin){
								$data['share_score'] = $share_score*100;
								$data['share_balance'] = $share_balance*100;
								$data['share_coin'] = $share_coin*100;
								$data['source_id'] = $data['parent_id'];
								$this->updateUserData($request,$data,$data['super_id']);
							}
						}
					}
					return true;
				case 'mod':
					if(isset($data['invite']) && $data['invite'] && isset($data['parent_id']) && $data['parent_id']){
						$this->setIncrement('DirectShare',$data['parent_id']);
					}
					return true;
				case 'charge':
					$arr = ['cate_id'=>1,'user_id'=>$data['user_id'],'source_id'=>$uid,'value'=>$data['amount']/100,'content'=>'积分充值'];
					$_class_name = $this->getClassName('Record');
					$service = new $_class_name();
					return $service->insertData($request,$arr);
				case 'auth':
					break;
				case 'email':
					break;
				case 'sms':
					break;
				default:
					return true;
			}
			
		}

		protected function validate(Request $request,$id = 0,$user = null)
		{
			$action = $request->input('action','');
			$data = [];
			switch($action){
				case 'charge':
					$charge_type = $request->post('charge_type');
					if(!$charge_type || !in_array($charge_type,[1,2])){
						return '无效的充值类型';
					}
					$charge_amount = $request->post('charge_amount');
					if(!is_numeric($charge_amount) || $charge_amount <= 0){
						return '无效的充值'.($charge_type === 1 ? '积分' : '金额');
					}
					$data['charge_type'] = $charge_type;
					$data['amount'] = $charge_amount*100;
					$data['callback'] = 'updateUserData';
					break;
				case 'modpwd':
					$oldpwd = trim($request->post('oldpwd',''));
					$password = trim($request->post('password',''));
					$checkpwd = trim($request->post('checkpwd',''));
					if(!$oldpwd){
						return '原始密码不能为空';
					}
					if(!$this->checkPassword($oldpwd,$user->password)){
						return '原始密码不正确';
					}
					if(!$password){
						return '新密码不能为空';
					}
					if(!$checkpwd){
						return '确认密码不能为空';
					}
					if($checkpwd !== $password){
						return '密码确认有误';
					}
					$data['password'] = $this->makePassword($password);
					break;
				case 'setpwd':
					$password = trim($request->post('password',''));
					if(!$password){
						return '新密码不能为空';
					}
					if(strlen($password) < 5 || strlen($password) > 32){
						return '新密码长度大于5小于32';
					}
					$data['password'] = $this->makePassword($password);
					break;
				case 'active':
					$status = $request->post('status');
					if(!$status || !is_numeric($status) || $status !== 1){
						return '无效的参数';
					}

					$data['status'] = 1;
					break;
				case 'auth':
					$auth_type = $request->post('auth_type');
					if(!$auth_type || !in_array($auth_type,[1,2,3])){
						return '无效的认证类型';
					}
					$realname = trim($request->post('realname',''));
					$idcard = trim($request->post('idcard',''));
					if(!$realname){
						return '请填写真实姓名';
					}
					if(!$idcard){
						return '请填写身份证号码';
					}
					$data['is_auth'] = 1;
					$data['auth_type'] = $auth_type;
					$data['realname'] = $realname;
					$data['idcard'] = $idcard;
					$data['auth_time'] = time();
					break;
				case 'email':
					$email = trim($request->post('email',''));
					$content = trim($request->post('content',''));
					if(!$email){
						return '邮件地址不能为空';
					}
					if(!Preg::isEmail($email)){
						return '邮件地址格式不正确';
					}
					if(!$content){
						return '邮件内容不能为空';
					}

					$data['email'] = $email;
					$data['content'] = $content;
					$data['callback'] = 'sendEmail';
					break;
				case 'sms':
					$mobile = trim($request->post('mobile',''));
					$content = trim($request->post('content',''));
					if(!$mobile){
						return '手机号码不能为空';
					}
					if(!Preg::isMobile($mobile)){
						return '手机号码格式不正确';
					}
					if(!$content){
						return '短信内容不能为空';
					}

					$data['mobile'] = $mobile;
					$data['content'] = $content;
					$data['callback'] = 'sendSms';
					break;
				case 'lock':
					$lock_type = $request->post('lock_type');
					if(empty($lock_type) || !in_array($lock_type,[1,2])){
						return '无效的锁定类型';
					}
					$lock_time = $request->post('lock_time');
					if(empty($lock_time) || !is_numeric($lock_time) || $lock_time <= 0){
						return '无效的锁定时间';
					}
					$lock_unit = $request->post('lock_unit');
					if(empty($lock_unit) || !in_array($lock_unit,[1,2,3,4,5,6])){
						return '无效的单位';
					}
					$content = trim($request->post('content',''));
					if(!$content){
						return '无效的锁定原因';
					}
					$data['lock_type'] = $lock_type;
					$data['lock_time'] = $lock_time;
					$data['lock_unit'] = $lock_unit;
					$data['content'] = $content;
					break;
				case 'avatar':
				case 'face':
					$face = trim($request->post('face',$request->post('avatar','')));
					if(!$face){
						return '请上传头像';
					}

					$data['face'] = $face;

					break;
				case 'nickname':
					$nickname = trim($request->post('nickname',''));
					if(!$nickname){
						return '昵称不能为空';
					}

					$data['nickname'] = $nickname;

					break;
				case 'info':
					$nickname = trim($request->post('nickname',''));
					$face = trim($request->post('face',''));
					$height = $request->post('height',0);
					$age = $request->post('age',0);
					$intro = trim($request->post('intro',''));
					$language = $request->post('language',[]);

					if($nickname && $nickname !== $user->nickname){
						$data['nickname'] = $nickname;
					}

					if($face && $face !== $user->face){
						$data['face'] = $face;
					}

					if(!is_numeric($height) || !$height < 0){
						return '无效的身高';
					}
					if($height && $height !== $user->height){
						$data['height'] = $height;
					}

					if(!is_numeric($age) || !$age < 0){
						return '无效的年龄';
					}
					if($age && $age !== $user->age){
						$data['age'] = $age;
					}

					if($intro && $intro !== $user->intro){
						$data['intro'] = $intro;
					}

					$languages = [];
					if($language && is_array($language)){
						foreach($language as $lang){
							if($lang['selected']){
								array_push($languages,$lang['value']);
							}
						}
					}

					if($languages && $languages != $user->language){
						$data['language'] = $languages;
					}
					var_dump('data: ', ($data ?? 'null'));
					break;
				default:
					$username = trim($request->post('username',''));
					$nickname = trim($request->post('nickname',''));
					$realname = trim($request->post('realname',''));
					$idcard = trim($request->post('idcard',''));
					$face = trim($request->post('face',''));
					$mobile = trim($request->post('mobile',''));
					$email = trim($request->post('email',''));
					$city = $request->post('city',[]);
					$invite = trim($request->post('inviter',''));
					$role_id = $request->post('role_id',0);

					if(!$username){
						return '用户名不能为空';
					}

					if($this->checkExists(['Username' => $username],$id)){
						return '用户名已存在';
					}

					if(!$nickname){
						return '昵称不能为空';
					}

					if($this->checkExists(['Nickname' => $nickname],$id)){
						return '昵称已存在';
					}

					if($idcard && !Preg::isIdcard($idcard)){
						return '身份证号码格式不正确';
					}
					if($idcard){
						$data['birth'] = $this->getBirth($idcard);
						$data['gender'] = $this->getGender($idcard);
					}

					if(!$face){
						$face = '';
					}

					if(!$mobile){
						return '手机号码不能为空';
					}
					if(!Preg::isMobile($mobile)){
						return '手机号码格式不正确';
					}

					if($this->checkExists(['Mobile' => $mobile],$id)){
						return '手机号码已存在';
					}
					if($email && !Preg::isEmail($email)){
						return '邮件地址格式不正确';
					}

					if($email && $this->checkExists(['Email' => $email],$id)){
						return '邮箱地址已存在';
					}
					if(empty($parent_id) || !is_numeric($parent_id) || $parent_id <= 0){
						$parent_id = 0;
					}

					

					$province_id = 0;
					$city_id = 0;
					$district_id = 0;
					$city_name = '';

					if($city){
						$province_id = $city['province'] ?? $city['province_id'] ?? $city[0] ?? $city[0][0];
						$city_id = $city['city'] ?? $city['city_id'] ?? $city[1] ?? $city[1][0];
						$district_id = $city['district'] ?? $city['district_id'] ?? $city[2] ?? $city[2][0];

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

					$data['province_id'] = $province_id;
					$data['city_id'] = $city_id;
					$data['district_id'] = $district_id;
					$data['city_name'] = $city_name;

					if($this->getConfig('user_is_invite',true) && !$invite){
						return '邀请码不能为空';
					}

					if($invite && (!$user || !$user->invite)){
						if(is_numeric($invite)){
							$resp = $this->getList($request,$invite);
						}else{
							$resp = $this->getList($request,'invite',$invite);
						}
						if(!$resp || !is_object($resp) || !property_exists($resp,$this->primaryKey)){
							return '无效的邀请码';
						}
						$data['invite'] = $invite;
						$data['parent_id'] = $resp->uid;
						if($resp->parent_id > 0){
							$data['super_id'] = $resp->parent_id;
						}
					}
					if($role_id){
						$role_id = is_array($role_id) ? end($role_id) : $role_id;
						$service = new RoleModel();
						$role = $service->getList($request,$role_id);
						// var_dump('role:',$role);
						if(!$role || !is_object($role) || !property_exists($role,'id')){
							return '无效的角色';
						}
					}else{
						if(isset($this->config['default_role_id']) && $this->config['default_role_id']){
							$role_id = $this->config['default_role_id'];
						}else{
							$service = new RoleModel();
							$role = $service->getList($request,'register');
							if($role && is_object($role)){
								$role_id = $role->id;
							}else{
								$role_id = 1501;
							}
						}
					}

					if(isset($this->config['user_is_active']) && $this->config['user_is_active']){
						$data['status'] = 1;
					}

					if(isset($this->config['user_reg_send_score']) && $this->config['user_reg_send_score']){
						$data['score'] = $this->config['user_reg_send_score'] * 100;
					}

					if(isset($this->config['user_reg_send_balance']) && $this->config['user_reg_send_balance']){
						$data['balance'] = $this->config['user_reg_send_balance'] * 100;
					}

					if(isset($this->config['user_reg_send_coin']) && $this->config['user_reg_send_coin']){
						$data['coin'] = $this->config['user_reg_send_coin'] * 100;
					}

					$data['username'] = $username;
					$data['nickname'] = $nickname;
					$data['realname'] = $realname;
					$data['idcard'] = $idcard;
					$data['face'] = $face;
					$data['mobile'] = $mobile;
					$data['email'] = $email;
					$data['role_id'] = $role_id;
					$data['ip_address'] = $this->getIPAddress($request);

					// var_dump('add data: ',$data);
					// return 'aa';
			}

			return $data;
		}

		protected function getActionList(Request $request,$id = 0): array
		{
			$id = $id ? $id : $request->input('user_id',0);
			if($id){
				$data = $this->getList($request,$id);
			}

			$action_name = $request->input('action','');
			$role_id = $request->input('role_id',0);
			$group_id = $request->input('group_id',[]);
			
			// var_dump('action_value: '.$action_name);
			switch($action_name){
				case 'active':
					$action = [
						['type'=>'input','label'=>'action','prop'=>'action','value'=>$action_name,'hidden'=>true,'attrs'=>['disabled'=>true]],
						['type'=>'input','label'=>'账号ID','prop'=>'user_id','value'=>$id,'attrs'=>['disabled'=>true]],
						['type'=>'input','label'=>'用户名','prop'=>'username','value'=>$data->username ?? '','attrs'=>['disabled'=>true]],
						['type'=>'input','label'=>'昵称','prop'=>'nickname','value'=>$data->nickname ?? '','attrs'=>['disabled'=>true]],
						['type'=>'input','label'=>'手机号码','prop'=>'mobile','value'=>$data->mobile ?? '','attrs'=>['disabled'=>true]],
						['type'=>'input','label'=>'邮箱地址','prop'=>'email','value'=>$data->email ?? '','attrs'=>['disabled'=>true]],
						['type'=>'input','label'=>'真实姓名','prop'=>'realname','value'=>$data->realname ?? '','attrs'=>['disabled'=>true]],
					];
					break;
				case 'modpwd':
					$action = [
						['type'=>'input','label'=>'账号ID','prop'=>'user_id','value'=>$id,'attrs'=>['disabled'=>true]],
						['type'=>'input','label'=>'真实姓名','prop'=>'realname','value'=>$data->realname ?? '','attrs'=>['disabled'=>true]],
						['type'=>'input','label'=>'action','prop'=>'action','value'=>$action_name,'hidden'=>true,'attrs'=>['disabled'=>true]],
						['type'=>'input','label'=>'原始密码','prop'=>'oldpwd','value'=>'','attrs'=>['type'=>'password'],'required'=>true],
						['type'=>'input','label'=>'新密码','prop'=>'password','value'=>'','attrs'=>['type'=>'password'],'required'=>true],
						['type'=>'input','label'=>'确认密码','prop'=>'checkpwd','value'=>'','attrs'=>['type'=>'password'],'required'=>true],
					];
					break;
				case 'setpwd':
					$action = [
						['type'=>'input','label'=>'账号ID','prop'=>'user_id','value'=>$id,'attrs'=>['disabled'=>true]],
						['type'=>'input','label'=>'真实姓名','prop'=>'realname','value'=>$data->realname ?? '','attrs'=>['disabled'=>true]],
						['type'=>'input','label'=>'action','prop'=>'action','value'=>$action_name,'hidden'=>true,'attrs'=>['disabled'=>true]],
						['type'=>'input','label'=>'新密码','prop'=>'password','value'=>'','required'=>true],
					];
					break;
				case 'auth':
					$action = [
						['type'=>'input','label'=>'账号ID','prop'=>'user_id','value'=>$id,'attrs'=>['disabled'=>true]],
						['type'=>'input','label'=>'真实姓名','prop'=>'realname','value'=>$data->realname ?? '','attrs'=>['disabled'=>true]],
						['type'=>'input','label'=>'action','prop'=>'action','value'=>$action_name,'hidden'=>true,'attrs'=>['disabled'=>true]],
						['type'=>'radio-group','label'=>'认证类型','prop'=>'auth_type','value'=>1,'children'=>[
							['label'=>'个人','value'=>1],['label'=>'企业','value'=>2],['label'=>'商户','value'=>3]
						],'required'=>true],
						['type'=>'input','label'=>'真实姓名','prop'=>'realname','value'=>$id?$data->realname:'','required'=>true],
						['type'=>'input','label'=>'身份证号','prop'=>'idcard','value'=>$id?$data->idcard:'','required'=>true],
					];
					break;
				case 'charge':
					$action = [
						['type'=>'input','label'=>'账号ID','prop'=>'user_id','value'=>$id,'attrs'=>['disabled'=>true]],
						['type'=>'input','label'=>'真实姓名','prop'=>'realname','value'=>$data->realname ?? '','attrs'=>['disabled'=>true]],
						['type'=>'input','label'=>'action','prop'=>'action','value'=>$action_name,'hidden'=>true,'attrs'=>['disabled'=>true]],
						['type'=>'radio-group','label'=>'充值类型','prop'=>'charge_type','value'=>1,'children'=>[
							['label'=>'积分','value'=>1],['label'=>'余额','value'=>2],['label'=>'金币','value'=>3]
						],'required'=>true],
						['type'=>'input','label'=>'充值金额','prop'=>'charge_amount','value'=>'','placeholder'=>'0','required'=>true]
					];
					break;
				case 'email':
					$action = [
						['type'=>'input','label'=>'账号ID','prop'=>'user_id','value'=>$id,'attrs'=>['disabled'=>true]],
						['type'=>'input','label'=>'真实姓名','prop'=>'realname','value'=>$data->realname ?? '','attrs'=>['disabled'=>true]],
						['type'=>'input','label'=>'action','prop'=>'action','value'=>$action_name,'hidden'=>true,'attrs'=>['disabled'=>true]],
						['type'=>'input','label'=>'邮件地址','prop'=>'email','value'=>$id?$data->email:'','required'=>true,'attrs'=>['disabled'=>true]],
						['type'=>'input','label'=>'邮件内容','prop'=>'content','value'=>'','attrs'=>['type'=>'textarea'],'required'=>true]
					];
					break;
				case 'sms':
					$template = [
						['label'=>'测试','value'=>'test'],
						['label'=>'登录','value'=>'login'],
						['label'=>'注册','value'=>'register'],
						['label'=>'认证','value'=>'auth'],
						['label'=>'通知','value'=>'nofitication'],
					];
					$action = [
						['type'=>'input','label'=>'账号ID','prop'=>'user_id','value'=>$id,'attrs'=>['disabled'=>true]],
						['type'=>'input','label'=>'真实姓名','prop'=>'realname','value'=>$data->realname ?? '','attrs'=>['disabled'=>true]],
						['type'=>'input','label'=>'action','prop'=>'action','value'=>$action_name,'hidden'=>true,'attrs'=>['disabled'=>true]],
						['type'=>'input','label'=>'手机号码','prop'=>'mobile','value'=>$id?$data->mobile:'','required'=>true,'attrs'=>['disabled'=>true]],
						['type'=>'select','label'=>'短信模板','prop'=>'template','value'=>'','children'=>$template,'required'=>true],
						['type'=>'input','label'=>'短信内容','prop'=>'content','value'=>'','attrs'=>['type'=>'textarea'],'required'=>true],
					];
					break;
				case 'lock':
					$action = [
						['type'=>'input','label'=>'账号ID','prop'=>'user_id','value'=>$id,'attrs'=>['disabled'=>true]],
						['type'=>'input','label'=>'真实姓名','prop'=>'realname','value'=>$data->realname ?? '','attrs'=>['disabled'=>true]],
						['type'=>'input','label'=>'action','prop'=>'action','value'=>$action_name,'hidden'=>true,'attrs'=>['disabled'=>true]],
						['type'=>'radio-group','label'=>'锁定类型','prop'=>'lock_type','value'=>1,'children'=>[
							['label'=>'临时锁定','value'=>1],['label'=>'永久锁定','value'=>2]
						],'required'=>true],
						['type'=>'group','label'=>'锁定时长','prop'=>'locktime','delimiter'=>'-','children'=>[
							['type'=>'input','label'=>'时间','prop'=>'lock_time','value'=>'','placeholder'=>'0','width'=>180,'prefix'=>'时间'],
							['type'=>'select','label'=>'单位','prop'=>'lock_unit','value'=>1,'width'=>100,'children'=>[
								['type'=>'option','label'=>'分','value'=>1],
								['type'=>'option','label'=>'时','value'=>2],
								['type'=>'option','label'=>'天','value'=>3],
								['type'=>'option','label'=>'周','value'=>4],
								['type'=>'option','label'=>'月','value'=>5],
								['type'=>'option','label'=>'年','value'=>6],
							]]
						],'required'=>true],
						['type'=>'input','label'=>'锁定原因','prop'=>'content','value'=>'访问异常','attrs'=>['type'=>'textarea'],'required'=>true]
					];
					break;
				case 'unlock':
					$action = [
						['type'=>'input','label'=>'账号ID','prop'=>'user_id','value'=>$id,'attrs'=>['disabled'=>true]],
						['type'=>'input','label'=>'真实姓名','prop'=>'realname','value'=>$data->realname ?? '','attrs'=>['disabled'=>true]],
						['type'=>'input','label'=>'action','prop'=>'action','value'=>$action_name,'hidden'=>true,'attrs'=>['disabled'=>true]],
						['type'=>'radio-group','label'=>'锁定类型','prop'=>'lock_type','value'=>1,'children'=>[
							['label'=>'临时锁定','value'=>1],['label'=>'永久锁定','value'=>2]
						],'attrs'=>['disabled'=>true],'required'=>true],
						['type'=>'group','label'=>'锁定时长','prop'=>'locktime','delimiter'=>'-','children'=>[
							['type'=>'input','label'=>'时间','prop'=>'lock_time','value'=>'','placeholder'=>'0','width'=>180,'prefix'=>'时间','attrs'=>['disabled'=>true]],
							['type'=>'select','label'=>'单位','prop'=>'lock_unit','value'=>1,'width'=>100,'children'=>[
								['type'=>'option','label'=>'分','value'=>1],
								['type'=>'option','label'=>'时','value'=>2],
								['type'=>'option','label'=>'天','value'=>3],
								['type'=>'option','label'=>'周','value'=>4],
								['type'=>'option','label'=>'月','value'=>5],
								['type'=>'option','label'=>'年','value'=>6],
							],'attrs'=>['disabled'=>true]]
						],'attrs'=>['disabled'=>true],'required'=>true],
						['type'=>'input','label'=>'锁定原因','prop'=>'content','value'=>'访问异常','attrs'=>['type'=>'textarea','disabled'=>true],'required'=>true]
					];
					break;
				case 'del':
					$action = [
						['type'=>'input','label'=>'action','prop'=>'action','value'=>$action_name,'hidden'=>true,'attrs'=>['disabled'=>true]],
						['type'=>'input','label'=>'账号ID','prop'=>'user_id','value'=>$id,'attrs'=>['disabled'=>true]],
						['type'=>'input','label'=>'用户名','prop'=>'username','value'=>$data->username ?? '','attrs'=>['disabled'=>true]],
						['type'=>'input','label'=>'昵称','prop'=>'nickname','value'=>$data->nickname ?? '','attrs'=>['disabled'=>true]],
						['type'=>'input','label'=>'手机号码','prop'=>'mobile','value'=>$data->mobile ?? '','attrs'=>['disabled'=>true]],
						['type'=>'input','label'=>'邮箱地址','prop'=>'email','value'=>$data->email ?? '','attrs'=>['disabled'=>true]],
						['type'=>'input','label'=>'真实姓名','prop'=>'realname','value'=>$data->realname ?? '','attrs'=>['disabled'=>true]],
					];
					break;
				case 'cancel':
					$action = [
						['type'=>'input','label'=>'action','prop'=>'action','value'=>$action_name,'hidden'=>true,'attrs'=>['disabled'=>true]],
						['type'=>'input','label'=>'账号ID','prop'=>'user_id','value'=>$id,'attrs'=>['disabled'=>true]],
						['type'=>'input','label'=>'用户名','prop'=>'username','value'=>$data->username ?? '','attrs'=>['disabled'=>true]],
						['type'=>'input','label'=>'昵称','prop'=>'nickname','value'=>$data->nickname ?? '','attrs'=>['disabled'=>true]],
						['type'=>'input','label'=>'手机号码','prop'=>'mobile','value'=>$data->mobile ?? '','attrs'=>['disabled'=>true]],
						['type'=>'input','label'=>'邮箱地址','prop'=>'email','value'=>$data->email ?? '','attrs'=>['disabled'=>true]],
						['type'=>'input','label'=>'真实姓名','prop'=>'realname','value'=>$data->realname ?? '','attrs'=>['disabled'=>true]],
					];
					break;
				case 'export':
					$field = [
						['type'=>'option','label'=>'账号ID','value'=>'Uid'],
						['type'=>'option','label'=>'用户名','value'=>'UserName'],
						['type'=>'option','label'=>'真实姓名','value'=>'RealName'],
						['type'=>'option','label'=>'身份证号','value'=>'Idcard'],
						['type'=>'option','label'=>'手机号码','value'=>'Mobile'],
						['type'=>'option','label'=>'邮箱地址','value'=>'Email'],
					];

					$action = [
    					['type'=>'radio-group','label'=>'导出数据类型','prop'=>'cate_id','value'=>1,'children'=>[
    						['type'=>'radio','label'=>'原始数据','value'=>1],
    						['type'=>'radio','label'=>'关联数据','value'=>2],
    					]],
    					['type'=>'radio-group','label'=>'导出数据字段','prop'=>'is_all','value'=>1,'children'=>[
    						['type'=>'radio','label'=>'全部字段','value'=>1],
    						['type'=>'radio','label'=>'部分字段','value'=>2],
    					]],
    					['type'=>'checkbox-group','label'=>'选择字段','prop'=>'field','value'=>[],'hidden'=>true,'children'=>$field],
    					['type'=>'radio-group','label'=>'导出数量','prop'=>'count','value'=>1,'children'=>[
    						['type'=>'radio','label'=>'全部数据','value'=>1],
    						['type'=>'radio','label'=>'分段数据','value'=>2],
    					]],
    					['type'=>'form-group','label'=>'数据从','prop'=>'part','value'=>'','hidden'=>true,'delimiter'=>'-','children'=>[
    						['type'=>'input','label'=>'开始条数','prop'=>'start','value'=>1,'attrs'=>['style'=>['width'=>'150px']],'slot'=>['prefix'=>['value'=>'第'],'suffix'=>['value'=>'条']]],
    						['type'=>'input','label'=>'结束条数','prop'=>'end','value'=>10,'attrs'=>['style'=>['width'=>'150px']],'slot'=>['prefix'=>['value'=>'第'],'suffix'=>['value'=>'条']]],
    					]],
    				];
					break;
				default:
					if($role_id){
						$roleForm = ['type'=>'input','label'=>'角色','prop'=>'role_id','value'=>$role_id,'hidden'=>true,'attrs'=>['disabled'=>true,'realonly'=>true]];
					}else{
						if($group_id && is_array($group_id) && count($group_id)){
							$service = new RoleModel();
							$role = $service->getList($request,'option',$group_id);
						}else{
							$service = new RoleModel();
							$role = $service->getList($request,'option');
						}
						
						$roleForm = ['type'=>'cascader','label'=>'角色','prop'=>'role_id','value'=>$id?$data->role_id:[15,1501],'placeholder'=>'选择角色','children'=>$role,'required'=>true];
					}

					$inviteForm = ['type'=>'input','label'=>'邀请码','prop'=>'inviter','value'=>$id?$data->invite:''];
					if($this->getConfig('user_is_invite',true)){
						$inviteForm['required'] = true;
					}
					if($id && $data->invite){
						$inviteForm['attrs'] = ['disabled' => true];
					}

					$city = [];
					if($id){
						$city = [
							'province_id' => $data->province_id,
							'city_id' => $data->city_id,
							'district_id' => $data->district_id,
						]; 
					}

					$action = [
						['type'=>'input','label'=>'用户名','prop'=>'username','value'=>$id?$data->username:'','required'=>true],
						['type'=>'input','label'=>'昵称','prop'=>'nickname','value'=>$id?$data->nickname:'','required'=>true],
						['type'=>'input','label'=>'真实姓名','prop'=>'realname','value'=>$id?$data->realname:''],
						['type'=>'input','label'=>'身份证号','prop'=>'idcard','value'=>$id?$data->idcard:''],
						['type'=>'upload','label'=>'头像','prop'=>'face','value'=>$id?$data->face:'','attrs'=>$this->getUploadOptions('img',1,'small')],
						['type'=>'input','label'=>'手机号码','prop'=>'mobile','value'=>$id?$data->mobile:'','required'=>true],
						['type'=>'input','label'=>'邮箱地址','prop'=>'email','value'=>$id?$data->email:''],
						['type'=>'city','label'=>'所在城市','prop'=>'city','value'=>$city,'width'=>200],
						// ['type'=>'input','label'=>'上级账号ID','prop'=>'parent_id','value'=>$id?$data->parent_id:''],
						$roleForm,
						$inviteForm,
					];
			}

			return $action;
		}

		protected function getMapList(Request $request): array
		{
			return [
				['type'=>'int','label'=>'账号ID','prop'=>'uid','width'=>110],
				['type'=>'router','label'=>'用户名','prop'=>'username','router'=>'/user/info'],
				['type'=>'varchar','label'=>'昵称','prop'=>'nickname','width'=>120],
				['type'=>'varchar','label'=>'真实姓名','prop'=>'realname','width'=>120],
				['type'=>'img','label'=>'头像','prop'=>'face','align'=>$this->align],
				['type'=>'varchar','label'=>'身份证号','prop'=>'idcard','width'=>200],
				['type'=>'varchar','label'=>'出生日期','prop'=>'birth','width'=>120],
				['type'=>'map','label'=>'性别','prop'=>'gender','data'=>$this->gender_value],
				// ['type'=>'map','label'=>'年龄','prop'=>'age'],
				// ['type'=>'map','label'=>'身高','prop'=>'height'],
				// ['type'=>'map','label'=>'体重','prop'=>'weight'],
				// ['type'=>'map','label'=>'民族','prop'=>'nation'],
				['type'=>'varchar','label'=>'手机号码','prop'=>'mobile','width'=>150],
				['type'=>'varchar','label'=>'邮箱地址','prop'=>'email','width'=>180],
				['type'=>'varchar','label'=>'所在城市','prop'=>'city_name','width'=>180],
				['type'=>'router','label'=>'积分','prop'=>'score','align'=>$this->align,'router'=>'/record/score'],
				['type'=>'router','label'=>'余额','prop'=>'balance','prefix'=>'¥','router'=>'/record/balance'],
				['type'=>'router','label'=>'金币','prop'=>'coin','prefix'=>'¥','router'=>'/record/coin'],
				['type'=>'varchar','label'=>'分享人','prop'=>'parent_id','align'=>$this->align],
				['type'=>'router','label'=>'分享数','prop'=>'direct_share','align'=>$this->align,'router'=>'/user/share'],
				['type'=>'varchar','label'=>'认证','prop'=>'auth_type','router'=>'/user/auth'],
				['type'=>'varchar','label'=>'邀请码','prop'=>'invite_code','width'=>120],
				['type'=>'varchar','label'=>'角色','prop'=>'role_name','width'=>130],
				['type'=>'qrcode','label'=>'二维码','prop'=>'qrcode','align'=>$this->align],
				['type'=>'varchar','label'=>'注册IP','prop'=>'ip_address','width'=>150],
				['type'=>'date','label'=>'注册时间','prop'=>'create_time','width'=>180],
				['type'=>'map','label'=>'状态','prop'=>'status','data'=>$this->status_value],
			];
		}
	}
?>