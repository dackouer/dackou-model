<?php
	namespace dackou\model\User;

	use support\Request;
	use support\Db;
	use Illuminate\Support\Facades\Schema;
	use dackou\Generateion;
	use dackou\Preg;
	use dackou\model\Role\RoleModel;

	class UserModel extends \dackou\Model{
		protected $table = 'User';
		protected $title = '用户';
		protected $primaryKey = 'uid';
		protected $option_key = 'user';
    	protected $truncate = false;
    	protected $gender_value = [0=>'未知',1=>'男',2=>'女'];
    	protected $auth_value = [0=>'未认证',1=>'个人',2=>'企业',3=>'商户'];
    	protected $status_value = [
    		['id'=>-2,'title'=>'已注销','color'=>'gray'],
    		['id'=>-1,'title'=>'已删除','color'=>'red'],
    		['id'=>0,'title'=>'待激活','color'=>'orange'],
    		['id'=>1,'title'=>'正常','color'=>'blue'],
    		['id'=>2,'title'=>'已锁定','color'=>'yellow'],
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
    		['key'=>'del','title'=>'删除','desc'=>'删除会员'],
    		['key'=>'cancel','title'=>'注销','desc'=>'注销会员'],
    		['key'=>'unlock','title'=>'解除','desc'=>'解除锁定'],
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
				$data[$key]['password'] = $this->makePassword($this->getDefaultPassword($data[$key]['idcard']));
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
			$field = [
				'ID as id',
				'AccountID as uid',
				'UserName as username',
				'NickName as nickname',
				'RealName as realname',
				'Mobile as mobile',
				'Email as email',
				'Face as face',
				'OpenidWechat as openid',
				'Gender as gender',
				'Idcard as idcard',
				'Birth as birth',
				'Authentication as password',
				'ParentID as parent_id',
				'DirectShare as direct_share',
				'TotalShare as total_share',
				'Score as score',
				'Coin as coin',
				'Balance as balance',
				'IsAuth as is_auth',
				'AuthType as auth_type',
				'AuthName as auth_name',
				'MerchantID as merchant_id',
				'InviteCode as invite_code',
				'Invite as invite',
				'RoleID as role_id',
				'IpAddress as ip_address',
				'Token as token',
				'Status as status',
				'CreateTime as create_time',
			];

			if($flag === 'key'){
				$keys = [];
				$vals = [];
				foreach($field as $item){
					$itm = \explode(' as ',$item);
					if(isset($itm[0])){
						array_push($keys,$itm[0]);
					}
					if(isset($itm[1])){
						array_push($vals,$itm[1]);
					}
				}
				return [$keys,$vals];
			}else{
				if($flag){
					for($i=0;$i<count($field);$i++){
						$field[$i] = $this->table . "." . $field[$i];
					}
				}

				return $field;
			}
		}

		/**
		 * [getPageList description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		protected function getPageList(Request $request): array
		{
			try{
				$field = $this->getList($request,'field',true);
				array_push($field,'Title as role_name');
				array_push($field,'PID as group_id');
				$where = $this->getWhere($request);

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

				$rows = Db::table($this->table)
							->join('role','RoleID','role.ID')
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
							->join('role','RoleID','role.ID')
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
					foreach($object as $k => $v){
						// $object[$k]->create_time = $this->getDateTime($object[$k]->create_time);

						unset($object[$k]->password);
					}
				}
				return ['rows' => $rows,'data' => $object];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		protected function getListById(Request $request,$id = 0){
			if(!$id){
				return [];
			}

			try{
				$field = $this->getList($request,'field',true);
				array_push($field,'Title as role_name');
				array_push($field,'role.Pic as role_pic');
				array_push($field,'Sign as sign');
				array_push($field,'PID as group_id');
				array_push($field,'IsAdmin as is_admin');
				$where = $this->getWhere($request);
				array_push($where,[$this->table.'.AccountID','=',$id]);

				$object = Db::table($this->table)
								->join('role','RoleID','=','role.ID')
								->select(...$field)
								->where($where)
								->first();
				if($object){
					unset($object->password);
				}
				return $this->getResultData($object);
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		protected function getQueryList(Request $request): array
		{
			$keyword = $request->input('keyword','');
			$dtime = $request->input('dtime',[]);
			$status = $request->input('status','');
			$statusOption = [['type'=>'option','label'=>'-All-','value'=>'']];
			foreach($this->status_value as $item){
				array_push($statusOption,['type'=>'option','label'=>$item['title'],'value'=>(int)$item['id']]);
			}
			$role_id = $request->input('role_id');
			$group_id = $request->input('group_id');

			$service = new RoleModel();
			$role = $service->getList($request,'option');

			$query = [
				['type'=>'input','label'=>'关键词','prop'=>'keyword','value'=>$keyword],
				['type'=>'date-picker','label'=>'注册从','prop'=>'dtime','value'=>$dtime,'attrs'=>['type'=>'daterange','range-separator'=>'到','start-placeholder'=>'开始时间','end-placeholder'=>'结束时间']]
			];
			if(!$role_id && !$group_id && !count($group_id)){
				array_push($query,['type'=>'cascader','label'=>'角色','prop'=>'role_id','value'=>$role_id??[],'attrs'=>['placeholder'=>'选择角色','style'=>['width'=>'80px'],'options'=>$role]]
				);
			}
			array_push($query,['type'=>'select','label'=>'状态','prop'=>'status','value'=>$status,'children'=>$statusOption,'attrs'=>['clearable'=>true,'style'=>['width'=>'100px']]]
			);

			return $query;
		}

		protected function getOptionList(Request $request,mixed $data = [],mixed $field = []): array
		{
			try{
				$field = ['RealName as label','AccountID as value'];
				$where = [
					[$this->table.'.IsDel','=',0],
					[$this->table.'.Status','=',1],
					['role.IsDel','=',0],
				];
				$pidkey = [];
				$inkey = [];
				if($data){
					if(is_array($data)){
						if(count(array_keys($data)) == count(array_values($data))){
							foreach($data as $k => $v){
								array_push($where,[$k,'=',$v]);
							}
						}else{
							foreach($data as $k => $v){
								if((int)$v < 100){
									array_push($pidkey,(int)$v);
								}else{
									array_push($inkey,(int)$v);
								}
							}
						}
					}elseif(is_numeric($data)){
						array_push($where,['RoleID','=',$data]);
					}
				}
				$object = Db::table($this->table)
							->join('role','RoleID','=','role.ID')
							->select(...$field)
							->where($where);
				if($pidkey && count($pidkey)){
					$object = $object->whereIn('role.PID',$pidkey);
				}
				if($inkey && count($inkey)){
					$object = $object->whereIn('RoleID',$pidkey);
				}
				$object = $object->get();

				if($object){
					foreach($object as $k => $v){
						$object[$k]->type = 'option';
						$object[$k]->value = (int)$object[$k]->value;
						$object[$k]->disabled = false;
					}

					return $object->toArray();
				}

				return [];
			}catch(\Exception $e){
				return [];
			}
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
				array_push($where,[$this->table.'.Invite','=',$invite]);

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
				var_dump('error: '.$e->getMessage());
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
					case 'charge':
						$key = $data['charge_type'] === 1 ? "`Score`" : "`Balance`";
						$sql = "UPDATE `".$this->tab."` SET {$key} = {$key} + ? WHERE `AccountID` = ?";
						$param = [$data['amount'],$uid];
						$result = Db::update($sql,$param);
						if($result !== false){
							// 充值记录
							return true;
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
				var_dump('开始更新用户余额');
				$type = \ucfirst($type);
				if(!$mode){
					$sql = "UPDATE `".$this->tab."` SET {$type} = {$type} - ? WHERE `AccountID` = ?";
				}else{
					$sql = "UPDATE `".$this->tab."` SET {$type} = {$type} + ? WHERE `AccountID` = ?";
				}
				var_dump('update user balance sql: '.$sql);
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
			if(!$flag){
				if(isset($data['parent_id']) && $data['parent_id']){
					$this->setIncrement('DirectShare',$data['parent_id']);
				}
				if(isset($data['super_id']) && $data['super_id']){
					$this->setIncrement('TotalShare',$data['super_id']);
				}

			}
			return true;
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
				default:
					$username = trim($request->post('username',''));
					$nickname = trim($request->post('nickname',''));
					$realname = trim($request->post('realname',''));
					$idcard = trim($request->post('idcard',''));
					$face = trim($request->post('face',''));
					$mobile = trim($request->post('mobile',''));
					$email = trim($request->post('email',''));
					$invite = trim($request->post('invite',''));
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

					if($this->getConfig('user_is_invite',true) && !$invite){
						return '邀请码不能为空';
					}

					if($invite){
						if(is_numeric($invite)){
							$resp = $this->getList($request,$invite);
						}else{
							$resp = $this->getList($request,'invite',$invite);
						}
						if(!$resp || !is_object($resp) || !property_exists($resp,$this->primaryKey)){
							return '无效的邀请码';
						}
						$data['parent_id'] = $resp->uid;
						if($resp->parent_id > 0){
							$data['super_id'] = $resp->parent_id;
						}
					}
					if($role_id){
						$role_id = is_array($role_id) ? end($role_id) : $role_id;
						$service = new RoleModel();
						$role = $service->getList($request,$role_id);
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

					$data['username'] = $username;
					$data['nickname'] = $nickname;
					$data['realname'] = $realname;
					$data['idcard'] = $idcard;
					$data['face'] = $face;
					$data['mobile'] = $mobile;
					$data['email'] = $email;
					$data['role_id'] = $role_id;
					$data['ip_address'] = $this->getIPAddress($request);
			}

			return $data;
		}

		protected function getActionList(Request $request,$id = 0): array
		{
			$id = $id ? $id : $request->input('user_id',0);
			if($id){
				$data = $this->getList($request,$id);
			}

			$action_value = $request->input('action','');
			$role_id = $request->input('role_id',0);
			$group_id = $request->input('group_id',0);
			// var_dump('action_value: '.$action_value);
			switch($action_value){
				case 'modpwd':
					$action = [
						['type'=>'input','label'=>'账号ID','prop'=>'uid','value'=>$id,'attrs'=>['disabled'=>true]],
						['type'=>'input','label'=>'action','prop'=>'action','value'=>$action_value,'hidden'=>true,'attrs'=>['disabled'=>true]],
						['type'=>'input','label'=>'原始密码','prop'=>'oldpwd','value'=>'','attrs'=>['type'=>'password'],'rules'=>['required'=>true,'message'=>'请输入原始密码']],
						['type'=>'input','label'=>'新密码','prop'=>'password','value'=>'','attrs'=>['type'=>'password'],'rules'=>['required'=>true,'message'=>'请输入新密码']],
						['type'=>'input','label'=>'确认密码','prop'=>'checkpwd','value'=>'','attrs'=>['type'=>'password'],'rules'=>['required'=>true,'message'=>'请再次输入新密码']],
					];
					break;
				case 'setpwd':
					$action = [
						['type'=>'input','label'=>'账号ID','prop'=>'uid','value'=>$id,'attrs'=>['disabled'=>true]],
						['type'=>'input','label'=>'action','prop'=>'action','value'=>$action_value,'hidden'=>true,'attrs'=>['disabled'=>true]],
						['type'=>'input','label'=>'新密码','prop'=>'password','value'=>'','rules'=>['required'=>true,'message'=>'请输入新密码']],
					];
					break;
				case 'auth':
					$action = [
						['type'=>'input','label'=>'账号ID','prop'=>'uid','value'=>$id,'attrs'=>['disabled'=>true]],
						['type'=>'input','label'=>'action','prop'=>'action','value'=>$action_value,'hidden'=>true,'attrs'=>['disabled'=>true]],
						['type'=>'radio-group','label'=>'认证类型','prop'=>'auth_type','value'=>1,'children'=>[
							['label'=>'个人','value'=>1],['label'=>'企业','value'=>2],['label'=>'商户','value'=>3]
						]],
						['type'=>'input','label'=>'真实姓名','prop'=>'realname','value'=>$id?$data->realname:'','rules'=>['required'=>true,'message'=>'真实姓名不能为空']],
						['type'=>'input','label'=>'身份证号','prop'=>'idcard','value'=>$id?$data->idcard:'','rules'=>['required'=>true,'message'=>'身份证号不能为空']],
					];
					break;
				case 'charge':
					$action = [
						['type'=>'input','label'=>'账号ID','prop'=>'uid','value'=>$id,'attrs'=>['disabled'=>true]],
						['type'=>'input','label'=>'action','prop'=>'action','value'=>$action_value,'hidden'=>true,'attrs'=>['disabled'=>true]],
						['type'=>'radio-group','label'=>'充值类型','prop'=>'charge_type','value'=>1,'children'=>[
							['label'=>'积分','value'=>1],['label'=>'余额','value'=>2]
						]],
						['type'=>'input','label'=>'充值金额','prop'=>'charge_amount','value'=>'','placeholder'=>'0','rules'=>['required'=>true,'message'=>'充值金额不能为空']]
					];
					break;
				case 'email':
					$action = [
						['type'=>'input','label'=>'账号ID','prop'=>'uid','value'=>$id,'attrs'=>['disabled'=>true]],
						['type'=>'input','label'=>'action','prop'=>'action','value'=>$action_value,'hidden'=>true,'attrs'=>['disabled'=>true]],
						['type'=>'input','label'=>'邮件地址','prop'=>'email','value'=>$id?$data->email:'','rules'=>['required'=>true,'message'=>'邮箱地址不能为空']],
						['type'=>'input','label'=>'邮件内容','prop'=>'content','value'=>'','attrs'=>['type'=>'textarea'],'rules'=>['required'=>true,'message'=>'邮件内容不能为空']]
					];
					break;
				case 'sms':
					$action = [
						['type'=>'input','label'=>'账号ID','prop'=>'uid','value'=>$id,'attrs'=>['disabled'=>true]],
						['type'=>'input','label'=>'action','prop'=>'action','value'=>$action_value,'hidden'=>true,'attrs'=>['disabled'=>true]],
						['type'=>'input','label'=>'手机号码','prop'=>'mobile','value'=>$id?$data->mobile:'','rules'=>['required'=>true,'message'=>'手机号码不能为空']],
						['type'=>'input','label'=>'短信内容','prop'=>'content','value'=>'','attrs'=>['type'=>'textarea'],'rules'=>['required'=>true,'message'=>'短信内容不能为空']]
					];
					break;
				case 'lock':
					$action = [
						['type'=>'input','label'=>'账号ID','prop'=>'uid','value'=>$id,'attrs'=>['disabled'=>true]],
						['type'=>'input','label'=>'action','prop'=>'action','value'=>$action_value,'hidden'=>true,'attrs'=>['disabled'=>true]],
						['type'=>'radio-group','label'=>'锁定类型','prop'=>'lock_type','value'=>1,'children'=>[
							['label'=>'临时锁定','value'=>1],['label'=>'永久锁定','value'=>2]
						]],
						['type'=>'form-group','label'=>'锁定时长','prop'=>'locktime','delimiter'=>'-','children'=>[
							['type'=>'input','label'=>'时间','prop'=>'lock_time','value'=>'','placeholder'=>'0','attrs'=>['style'=>['width'=>'180px']],'slot'=>['prefix'=>['value'=>'时间']]],
							['type'=>'select','label'=>'单位','prop'=>'lock_unit','value'=>1,'attrs'=>['style'=>['width'=>'100px']],'children'=>[
								['type'=>'option','label'=>'分','value'=>1],
								['type'=>'option','label'=>'时','value'=>2],
								['type'=>'option','label'=>'天','value'=>3],
								['type'=>'option','label'=>'周','value'=>4],
								['type'=>'option','label'=>'月','value'=>5],
								['type'=>'option','label'=>'年','value'=>6],
							]]
						]],
						['type'=>'input','label'=>'锁定原因','prop'=>'content','value'=>'访问异常','attrs'=>['type'=>'textarea'],'rules'=>['required'=>true,'message'=>'锁定原因不能为空']]
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
						$roleForm = ['type'=>'cascader','label'=>'角色','prop'=>'role_id','value'=>$id?$data->role_id:'','attrs'=>['placeholder'=>'选择角色','options'=>$role],'rules'=>['required'=>true,'message'=>'请选择用户角色']];
					}

					$inviteForm = ['type'=>'input','label'=>'邀请码','prop'=>'invite','value'=>$id?$data->invite:''];
					if($this->getConfig('user_is_invite',true)){
						$inviteForm['rules'] = ['required'=>true,'message'=>'邀请码不能为空'];
					}

					$action = [
						['type'=>'input','label'=>'用户名','prop'=>'username','value'=>$id?$data->username:'','rules'=>['required'=>true,'message'=>'用户名不能为空']],
						['type'=>'input','label'=>'昵称','prop'=>'nickname','value'=>$id?$data->nickname:'','rules'=>['required'=>true,'message'=>'昵称不能为空']],
						['type'=>'input','label'=>'真实姓名','prop'=>'realname','value'=>$id?$data->realname:''],
						['type'=>'input','label'=>'身份证号','prop'=>'idcard','value'=>$id?$data->idcard:''],
						['type'=>'upload','label'=>'头像','prop'=>'face','value'=>$id?$data->face:'','uploadAttrs'=>[
							'type'=>'img','limit'=>1,'size'=>'small'
						]],
						['type'=>'input','label'=>'手机号码','prop'=>'mobile','value'=>$id?$data->mobile:'','rules'=>['required'=>true,'message'=>'手机号码不能为空']],
						['type'=>'input','label'=>'邮箱地址','prop'=>'email','value'=>$id?$data->email:''],
						// ['type'=>'input','label'=>'上级账号ID','prop'=>'parent_id','value'=>$id?$data->parent_id:''],
						$inviteForm,
						$roleForm
					];
			}

			return $action;
		}

		protected function getMapList(Request $request): array
		{
			return [
				['type'=>'int','label'=>'账号ID','prop'=>'uid','align'=>$this->align,'width'=>110],
				['type'=>'varchar','label'=>'用户名','prop'=>'username','align'=>$this->align,],
				['type'=>'varchar','label'=>'昵称','prop'=>'nickname','align'=>$this->align,'width'=>120],
				['type'=>'varchar','label'=>'真实姓名','prop'=>'realname','align'=>$this->align,'width'=>120],
				['type'=>'img','label'=>'头像','prop'=>'face','align'=>$this->align],
				['type'=>'varchar','label'=>'身份证号','prop'=>'idcard','align'=>$this->align,'width'=>200],
				['type'=>'varchar','label'=>'出生日期','prop'=>'birth','align'=>$this->align,'width'=>120],
				['type'=>'map','label'=>'性别','prop'=>'gender','align'=>$this->align,'data'=>$this->gender_value],
				['type'=>'varchar','label'=>'手机号码','prop'=>'mobile','align'=>$this->align,'width'=>150],
				['type'=>'varchar','label'=>'邮箱地址','prop'=>'email','align'=>$this->align,'width'=>180],
				['type'=>'number','label'=>'积分','prop'=>'score','align'=>$this->align,'format'=>['calc'=>'/','value'=>100,'decimal'=>0]],
				['type'=>'number','label'=>'余额','prop'=>'balance','align'=>$this->align,'','format'=>['calc'=>'/','value'=>100,'decimal'=>2]],
				['type'=>'varchar','label'=>'分享人','prop'=>'parent_id','align'=>$this->align],
				['type'=>'varchar','label'=>'分享数','prop'=>'direct_share','align'=>$this->align],
				['type'=>'map','label'=>'认证','prop'=>'is_auth','align'=>$this->align,'data'=>$this->auth_value],
				['type'=>'varchar','label'=>'邀请码','prop'=>'invite_code','align'=>$this->align,'width'=>120],
				['type'=>'varchar','label'=>'角色','prop'=>'role_name','align'=>$this->align,'width'=>130],
				['type'=>'qrcode','label'=>'二维码','prop'=>'qrcode','align'=>$this->align],
				['type'=>'varchar','label'=>'注册IP','prop'=>'ip_address','align'=>$this->align,'width'=>150],
				['type'=>'date','label'=>'注册时间','prop'=>'create_time','format'=>['format'=>'Y-m-d H:i:s'],'align'=>$this->align,'width'=>180],
				['type'=>'map','label'=>'状态','prop'=>'status','align'=>$this->align,'data'=>$this->status_value],
			];
		}
	}
?>