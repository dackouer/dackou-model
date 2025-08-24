<?php
	namespace dackou\model\Role;

	use support\Request;
	use support\Db;

	class RoleModel extends \dackou\Model{
		protected $table = 'Role';
		protected $title = '角色';
    	protected $show_method = 'tree';
    	protected $truncate = true;
		protected $option_key = 'user';
		public $layer = 2;
		public $digit = 2;
		protected $vipid = [14,15,16];
		protected $type_value = [0=>'无',1=>'个人',2=>'企业',3=>'商户'];

		protected function getDefaultData(Request $request){
			$data = [
				['ID'=>10,'Title'=>'超级管理员','Sign'=>'','Level'=>1,'PID'=>0,'IsAdmin'=>2,'IsLogin'=>1,'IsDefault'=>0,'IsCommission'=>0,'Number'=>1,'Sort'=>1],
				['ID'=>11,'Title'=>'管理员','Sign'=>'','Level'=>1,'PID'=>0,'IsAdmin'=>1,'IsLogin'=>1,'IsDefault'=>0,'IsCommission'=>0,'Number'=>1,'Sort'=>2],
				['ID'=>12,'Title'=>'开发组','Sign'=>'','Level'=>1,'PID'=>0,'IsAdmin'=>0,'IsLogin'=>0,'IsDefault'=>0,'IsCommission'=>0,'Number'=>1,'Sort'=>3],
				['ID'=>13,'Title'=>'企业组','Sign'=>'','Level'=>1,'PID'=>0,'IsAdmin'=>0,'IsLogin'=>1,'IsDefault'=>0,'IsCommission'=>0,'Number'=>2,'Sort'=>4],
				['ID'=>14,'Title'=>'其它组','Sign'=>'','Level'=>1,'PID'=>0,'IsAdmin'=>0,'IsLogin'=>1,'IsDefault'=>0,'IsCommission'=>0,'Number'=>2,'Sort'=>5],
				['ID'=>15,'Title'=>'会员组','Sign'=>'','Level'=>1,'PID'=>0,'IsAdmin'=>0,'IsLogin'=>1,'IsDefault'=>0,'IsCommission'=>0,'Number'=>2,'Sort'=>6],
				['ID'=>1001,'Title'=>'超级管理员','Sign'=>'','Level'=>2,'PID'=>10,'IsAdmin'=>2,'IsLogin'=>1,'IsDefault'=>0,'IsCommission'=>0,'Number'=>0,'Sort'=>1],
				['ID'=>1101,'Title'=>'管理员','Sign'=>'','Level'=>2,'PID'=>11,'IsAdmin'=>1,'IsLogin'=>1,'IsDefault'=>0,'IsCommission'=>0,'Number'=>0,'Sort'=>1],
				['ID'=>1201,'Title'=>'developer','Sign'=>'','Level'=>2,'PID'=>12,'IsAdmin'=>0,'IsLogin'=>0,'IsDefault'=>0,'IsCommission'=>0,'Number'=>0,'Sort'=>1],
				['ID'=>1301,'Title'=>'总经理','Sign'=>'','Level'=>2,'PID'=>13,'IsAdmin'=>0,'IsLogin'=>1,'IsDefault'=>0,'IsCommission'=>0,'Number'=>0,'Sort'=>1],
				['ID'=>1302,'Title'=>'业务经理','Sign'=>'','Level'=>2,'PID'=>13,'IsAdmin'=>0,'IsLogin'=>1,'IsDefault'=>0,'IsCommission'=>0,'Number'=>0,'Sort'=>2],
				['ID'=>1401,'Title'=>'供应商','Sign'=>'','Level'=>2,'PID'=>14,'IsAdmin'=>0,'IsLogin'=>1,'IsDefault'=>0,'IsCommission'=>0,'Number'=>0,'Sort'=>1],
				['ID'=>1402,'Title'=>'客户','Sign'=>'','Level'=>2,'PID'=>14,'IsAdmin'=>0,'IsLogin'=>1,'IsDefault'=>0,'IsCommission'=>0,'Number'=>0,'Sort'=>2],
				['ID'=>1501,'Title'=>'普通会员','Sign'=>'','Level'=>2,'PID'=>15,'IsAdmin'=>0,'IsLogin'=>1,'IsDefault'=>1,'IsCommission'=>0,'Number'=>0,'Sort'=>1],
				['ID'=>1502,'Title'=>'VIP会员','Sign'=>'','Level'=>2,'PID'=>15,'IsAdmin'=>0,'IsLogin'=>1,'IsDefault'=>0,'IsCommission'=>1,'Number'=>0,'Sort'=>2]
			];

			foreach($data as $key => $val){
				foreach($val as $k => $v){
					if($k == 'Sign'){
						$data[$key][$k] = $this->createUniqueCode('Sign',32,3,true);
					}
				}
			}

			return $data;
		}

		protected function getListById(Request $request,$id = 0){
			if(!$id || !is_numeric($id) || $id <= 0){
				return 100007;
			}

			try{
				$field = $this->getList($request,'field');
				$where = $this->getWhere($request);
				array_push($where,[$this->table.'.ID','=',$id]);

				$object = Db::table($this->table)
							->select(...$field)
							->where($where)
							->first();
				if($object){
					$object->amount /= 100;
					$object->create_time = $this->getDateTime($object->create_time);
				}
				return $object;
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		protected function getTreeList(Request $request): array
		{
			try{
				$field = $this->getList($request,'field');
				$where = $this->getWhere($request);
				$action = $request->input('action');
				if($action === 'vip'){
					array_push($where,[$this->table.'.ID','>',13]);
				}

				$object = Db::table($this->table)
							->select(...$field)
							->where($where)
							->orWhere('PID','>',13)
							->orderBy('Level','asc')
							->orderBy('Sort','asc')
							->get();
				if($object){
					foreach($object as $k => $v){
						$object[$k]->amount = $this->formatAmount($object[$k]->amount);
						$object[$k]->create_time = $this->getDateTime($object[$k]->create_time);
					}
					$object = $this->tree($object);
				}

				return ['rows'=>count($object),'data'=>$object];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		protected function getAuthList(Request $request){
			try{
				$field = $this->getList($request,'field');
				$where = $this->getWhere($request);
				array_push($where,['IsAuth','=',1]);

				$object = Db::table($this->table)
							->select(...$field)
							->where($where)
							->get();
				return $object;
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		protected function getOptionList(Request $request,mixed $data = [],mixed $field = []): array
		{
			try{
				$field = ['ID as value','Title as label','Number as number','PID as pid'];
				$where = [['IsDel','=',0]];
				$pinWhere = [];
				$inWhere = [];

				if($data){
					if(!is_array($data)){
						$data = \explode(',',$data);
					}

					if(count(array_keys($data)) == count(array_values($data))){
						foreach($data as $k => $v){
							if((int)$v < 100){
								array_push($pinWhere,$v);
							}else{
								array_push($inWhere,$v);
							}
						}
					}else{

					}
				}
				$object = Db::table($this->table)
							->select(...$field)
							->where($where);
				if($pinWhere){
					$object = $object->whereIn('PID',$pinWhere);
					$object = $object->orWhereIn('ID',$pinWhere);
				}
				if($inWhere){
					$object = $object->whereIn('ID',$inWhere);
				}
				$object = $object->get();
				if($object){
					$options = [];
					foreach($object as $k => $v){
						if(!$object[$k]->pid){
							$option = ['type'=>'option','label'=>$object[$k]->label,'value'=>(int)$object[$k]->value];
							if($object[$k]->number){
								$children = [];
								foreach($object as $key => $val){
									if($object[$key]->pid == $object[$k]->value){
										array_push($children,['type'=>'option','label'=>$object[$key]->label,'value'=>(int)$object[$key]->value]);
									}
								}
								if($children){
									$option['children'] = $children;
								}
							}
							array_push($options,$option);
						}
					}
					return $options;
				}
				return [];
			}catch(\Exception $e){
				return [];
			}
		}

		protected function validate(Request $request,$id = 0,$obj = null){
			$action = $request->input('action','');

			if($action == 'default'){
				if($id && !$obj->PID){
					return '无效的设置参数';
				}
				$is_default = $request->post('is_default',0);
				if(!is_numeric($is_default) || !in_array($is_default,[0,1])){
					return '无效的设置参数';
				}

				$data['is_default'] = $is_default;
				return $data;
			}

			if($action == 'vip'){
				$title = trim($request->post('title',''));
				$pic = $request->post('pic');
				if(is_array($pic) && isset($pic[0])){
					$pic = $pic[0];
				}
				$is_commission = $request->post('is_commission',0);
				$amount = $request->post('amount');
				if($amount == '' || $amount == '0.00' || $amount == '0.0'){
					$amount = 0;
				}
				$keyword = trim($request->post('keyword',''));
				$desc = trim($request->post('desc',''));
				$sort = $request->post('sort',1);
				$level = $request->post('level',1);
				$pid = $request->post('pid',0);

				if(!$title){
					return '会员名称不能为空';
				}
				if($this->checkExists(['Title'=>$title,'PID'=>$pid],$id)){
					return '会员名称已存在，请重新输入';
				}

				if(!is_numeric($amount) || $amount < 0){
					return '无效的会员升级金额';
				}

				$data['title'] = $title;
				$data['sign'] = $this->createUniqueCode('Sign',32,3,true);
				$data['pic'] = $pic;
				$data['is_commission'] = $is_commission;
				$data['amount'] = $amount * 100;
				$data['keyword'] = $keyword;
				$data['desc'] = $desc;
				$data['sort'] = $sort;
				$data['level'] = $level;
				$data['PID'] = $pid;

				return $data;
			}

			$level = $request->post('level',1);
			$pid = $request->post('pid',0);
			if($level > 1 && !$pid){
				return '请选择父级';
			}
			$title = trim($request->post('title',''));
			if(!$title){
				return '角色名称不能为空';
			}
			if($this->checkExists(['Title'=>$title,'PID'=>$pid],$id)){
				return '角色名称已存在';
			}

			$pic = $request->post('pic','');
			$is_admin = $request->post('is_admin',0);
			$is_login = $request->post('is_login',0);
			$is_default = $request->post('is_default',0);
			$sort = $request->post('sort',1);

			if(!$id){
				$sign = $this->createUniqueCode('Sign',32,3,true);
				if(is_array($sign) && isset($sign['code']) && $sign['code']){
					return $sign;
				}
				$data['sign'] = $sign;
			}

			$data['title'] = $title;
			$data['sign'] = $this->createUniqueCode('Sign',32,3,true);
			$data['pic'] = $pic;
			$data['level'] = $level;
			$data['pid'] = $pid;
			$data['is_admin'] = $is_admin;
			$data['is_login'] = $is_login;
			$data['is_default'] = $is_default;
			$data['sort'] = $sort;
			// var_dump($data);
			return $data;
		}

		protected function getActionList(Request $request,$id = 0): array
		{
			// var_dump('id: '.$id);
			try{
				$act = $request->input('action');

				if($id){
					$data = $this->getList($request,$id);
				}

				$pid = $request->input('pid',0);
				$level = $request->input('level',1);
				$ilevel = [];
				for($i=1;$i<=$this->layer;$i++){
					array_push($ilevel,['type'=>'option','label'=>$i.'级','value'=>$i]);
				}
				$ipid = $this->getList($request,'option',['Title'=>'title','ID'=>'id'],['Level'=>1]);
				$sort = $this->getMaxSort($pid);

				switch($act){
					case 'vip':
						$authType = $this->getFieldOption($this->type_value);

						$action = [
							['type'=>'input','label'=>'会员名称','prop'=>'title','value'=>$id ? $data->title : '','rules'=>['required'=>true,'message'=>'会员名称不能为空']],
							['type'=>'select','label'=>'层级','prop'=>'level','value'=>$id ? $data->level : $level,'hidden'=>true,'children'=>$ilevel],
							['type'=>'select','label'=>'父级','prop'=>'pid','value'=>$id ? $data->pid : $pid,'hidden'=>true,'children'=>$ipid],
							['type'=>'input','label'=>'升级金额','prop'=>'amount','value'=>$id ? $data->amount : '','placeholder'=>'0.00','slot'=>['prefix'=>['value'=>'￥'],'suffix'=>['value'=>'元']],'rules'=>['required'=>true,'message'=>'金额不能为空']],
							['type'=>'upload','label'=>'马甲','prop'=>'pic','value'=>$id ? $data->pic : '','uploadAttrs'=>[
								'type'=>'img','size'=>'small','limit'=>1,'action'=>$this->host['api'].'upload'
							]],
							['type'=>'upload','label'=>'背景图','prop'=>'bg_img','value'=>$id ? $data->bg_img : '','uploadAttrs'=>[
								'type'=>'img','size'=>'small','limit'=>1,'action'=>$this->host['api'].'upload'
							]],
							['type'=>'input','label'=>'关键词','prop'=>'keyword','value'=>$id ? $data->keyword : ''],
							['type'=>'input','label'=>'会员描述','prop'=>'desc','value'=>$id ? $data->desc : '','attrs'=>['type'=>'textarea','rows'=>3]],
							['type'=>'switch','label'=>'开启分佣','prop'=>'is_commission','value'=>$id ? $data->is_commission : 0],
							['type'=>'switch','label'=>'开启认证','prop'=>'is_auth','value'=>$id ? $data->is_auth : 0],
							['type'=>'radio-group','label'=>'认证类型','prop'=>'auth_type','value'=>$id ? $data->auth_type : 1,'children'=>$authType],
							['type'=>'input','label'=>'排序','prop'=>'sort','value'=>$id ? $data->sort : $sort],
						];
						break;
					case 'auth':
						$user_auth_is_smscode = $this->getConfig('user_auth_is_smscode',true);
						$auth_type = $request->input('auth_type',1);
						$role_id = $request->input('role_id',0);
						$user_id = $request->input('user_id',0);
						$_class_name = $this->getClassName('User');
						$service = new $_class_name();
						$user = $service->getList($request,$user_id);
						switch($auth_type){
							case 1:
								$action = [
									['type'=>'input','label'=>'角色','prop'=>'role_id','value'=>$role_id,'hidden'=>true],
									['type'=>'input','label'=>'真实姓名','prop'=>'realname','value'=>'','rules'=>['required'=>true,'message'=>'真实姓名不能为空']],
									['type'=>'input','label'=>'身份证号','prop'=>'idcard','value'=>'','rules'=>['required'=>true,'message'=>'身份证号不能为空']],
								];
								if($user_auth_is_smscode){
									array_push($action,['type'=>'input','label'=>'手机号码','prop'=>'mobile','value'=>$user->mobile,'rules'=>['required'=>true,'message'=>'手机号码不能为空']],
										['type'=>'input','label'=>'短信验证码','prop'=>'smscode','value'=>'','attrs'=>['type'=>'smscode'],'slot'=>['suffix'=>['value'=>'获取验证码','color'=>'blue','action'=>'abcd']],'rules'=>['required'=>true,'message'=>'短信验证码不能为空']]);
								}
								break;
							case 2:
								$_class_name = $this->getClassName('Enterprise');
								$service = new $_class_name();
								$action = $service->getList($request,'action');
								break;
							case 3:
								$_class_name = $this->getClassName('Merchant');
								$service = new $_class_name();
								$action = $service->getList($request,'action');
								break;
							default:
								$action = [];
						}
						return $action;
						break;
					default:
						$action = [
							['type'=>'input','label'=>'角色名称','prop'=>'title','value'=>$id ? $data->title : '','rules'=>['required'=>true,'message'=>'角色名称不能为空']],
							['type'=>'icon','label'=>'图标','prop'=>'pic','value'=>$id ? $data->pic : ''],
							['type'=>'select','label'=>'层级','prop'=>'level','value'=>$id ? $data->level : $level,'hidden'=>true,'children'=>$ilevel],
							['type'=>'select','label'=>'父级','prop'=>'pid','value'=>$id ? $data->pid : $pid,'hidden'=>true,'children'=>$ipid],
							['type'=>'radio-group','label'=>'管理员权限','prop'=>'is_admin','value'=>$id ? $data->is_admin : 0,'children'=>[
								['label'=>'非管理员','value'=>0],['label'=>'管理员','value'=>1],['label'=>'超级管理员','value'=>2],
							]],
							['type'=>'switch','label'=>'允许登录','prop'=>'is_login','value'=>$id ? $data->is_login : 0],
							['type'=>'switch','label'=>'默认注册','prop'=>'is_default','value'=>$id ? $data->is_default : 0],
							['type'=>'switch','label'=>'开启分佣','prop'=>'is_commission','value'=>$id ? $data->is_commission : 0],
							['type'=>'input','label'=>'排序','prop'=>'sort','value'=>$id ? $data->sort : $sort],
						];
				}

				return $action;
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		protected function getMapList(Request $request): array
		{
			$action = $request->input('action');
			switch($action){
				case 'vip':
					$map = [
						['type'=>'id','label'=>'ID','prop'=>'id','align'=>'start'],
						['type'=>'varchar','label'=>'会员名称','prop'=>'title','align'=>'start','width'=>150],
						['type'=>'icon','label'=>'马甲','prop'=>'pic','align'=>$this->align],
						['type'=>'switch','label'=>'默认','prop'=>'is_default','align'=>$this->align],
						['type'=>'switch','label'=>'分佣','prop'=>'is_commission','align'=>$this->align],
						['type'=>'switch','label'=>'认证','prop'=>'is_auth','align'=>$this->align],
						['type'=>'map','label'=>'认证类型','prop'=>'auth_type','align'=>$this->align,'data'=>$this->getFieldOption($this->type_value)],
						['type'=>'varchar','label'=>'升级金额','prop'=>'amount','prefix'=>'￥','suffix'=>'元','align'=>$this->align,'width'=>180],
						['type'=>'varchar','label'=>'排序','prop'=>'sort','align'=>$this->align],
						// ['type'=>'varchar','label'=>'创建时间','prop'=>'create_time','align'=>$this->align,'width'=>180],
					];
					break;
				default:
					$map = [
						['type'=>'id','label'=>'ID','prop'=>'id','align'=>'start'],
						['type'=>'varchar','label'=>'角色名称','prop'=>'title','align'=>'start','width'=>180],
						['type'=>'icon','label'=>'图标','prop'=>'pic','align'=>$this->align],
						['type'=>'switch','label'=>'权限','prop'=>'is_admin','align'=>$this->align],
						['type'=>'switch','label'=>'登录','prop'=>'is_login','align'=>$this->align],
						['type'=>'switch','label'=>'注册','prop'=>'is_default','align'=>$this->align],
						['type'=>'switch','label'=>'分佣','prop'=>'is_commission','align'=>$this->align],
						['type'=>'varchar','label'=>'排序','prop'=>'sort','align'=>$this->align],
					];
			}
			return $map;
		}
	}
?>