<?php
	namespace dackou\model\Activity;

	use support\Request;
	use support\Db;
	use dackou\Server;

	class ActivityUserModel extends \dackou\Model{
		protected $table = 'ActivityUser';
        protected $title = '活动用户';
        protected $import_file = 'activity_user.xlsx';
        protected $import_field = ['ActivityID','Realname','Idcard','Mobile','EnterpriseName','Position','Seat','ManagerID'];

		protected function getAllList(Request $request){
			$activity_id = $request->input('activity_id',$request->input('aid',0));
			if(!$activity_id){
				return $this->getList($request,'page');
			}

			$field = $this->getList($request,'field',true);
			array_push($field,'activity.Title as activity_title');
			$user = $this->prefix . 'user';
			array_push($field,Db::raw("(SELECT RealName FROM {$user} WHERE AccountID = ManagerID LIMIT 1) as manager"));

			$where = $this->getWhere($request);
			array_push($where,[$this->table.'.ActivityID','=',$activity_id]);

			$object = Db::table($this->table)
						->join('activity','ActivityID','=','activity.ID')
						->select(...$field)
						->where($where)
						->orderBy($this->table.'.ID','desc')
						->get();

			return $object;
		}

		protected function getPageList(Request $request){
			$field = $this->getList($request,'field',true);
			array_push($field,'activity.Title as activity_title');
			$user = $this->prefix . 'user';
			array_push($field,Db::raw("(SELECT RealName FROM {$user} WHERE AccountID = ManagerID LIMIT 1) as manager"));

			$where = $this->getWhere($request);
			$activity_id = $request->input('activity_id',$request->input('aid',0));
			if($activity_id){
				array_push($where,[$this->table.'.ActivityID','=',$activity_id]);
			}

			$rows = Db::table($this->table)
						->join('activity','ActivityID','=','activity.ID')
						->where($where)
						->count();

			[$offset,$limit] = $this->getLimit($request);

			$object = Db::table($this->table)
						->join('activity','ActivityID','=','activity.ID')
						->select(...$field)
						->where($where)
						->orderBy($this->table.'.ID','desc')
						->offset($offset)
						->limit($limit)
						->get();

			return ['rows' => $rows,'data' => $object];
		}

		protected function getUserList(Request $request,$user_id = 0,$aid = 0){
			if(!$aid || !is_numeric($aid) || $aid < 0 || !$user_id || !is_numeric($user_id) || $user_id < 0){
				return [];
			}

			if(!Server::isMobile($user_id)){
				$_class_name = $this->getClassName('User');
				$service = new $_class_name();
				$user = $service->getList($request,$user_id);
				if($user && is_object($user)){
					$user_id = $user->mobile;
				}
			}
			// var_dump('user_id: '.$user_id);
			$field = [
				$this->table.'.ID as id', 
				'AccountID as uid',
				$this->table.'.Realname as realname',
				$this->table.'.Mobile as mobile',
				$this->table.'.IsSign as is_sign',
				'Seat as seat'
			];
			$where = $this->getWhere($request);
			array_push($where,[$this->table.'.Mobile','=',$user_id]);
			array_push($where,[$this->table.'.ActivityID','=',$aid]);
			// array_push($where,[$this->table.'.Status','=',1]);
			array_push($where,['user.IsDel','=',0]);
			array_push($where,['user.Status','=',1]);

			$object = Db::table($this->table)
						->join('user',$this->table.'.Mobile','=','user.Mobile')
						->select(...$field)
						->where($where)
						->first();
			
			return $object;
		}

		protected function getMobileList(Request $request,$mobile,$aid){

			$field = $this->getList($request,'field');
			$where = $this->getWhere($request);
			array_push($where,[$this->table.'.Mobile','=',$mobile]);
			array_push($where,[$this->table.'.ActivityID','=',$aid]);

			return Db::table($this->table)
						->select(...$field)
						->where($where)
						->first();
		}

    	public function setImport(Request $request){
    		try{
    			$fields = $this->getImportFields($request);
    			if(!$fields || !is_array($fields) || !count($fields)){
    				return '无效的导入字段';
    			}

    			$aid = $request->input('aid',$request->input('activity_id',0));
    			// var_dump('aid: '.$aid);
    			$file = $request->post('file');
    			// var_dump('file: ',$file);
	            if(!$file){
	                return '未找到要导入的文件';
	            }
	            $file = (is_array($file) && isset($file['save_path'])) ? $file['save_path'] : $file;
	            if(!$file){
	                return '未找到要导入的文件';
	            }
	            
	            $data = \dackou\service\Office\OfficeService::imported($file);
	            // var_dump($data);
	            if(!$data || !is_array($data) || !count($data)){
		            return '无效的导入数据';
		        }
		        \array_shift($data);
		        if(!$data || !is_array($data) || !count($data)){
		            return '无效的导入数据';
		        }

		        if($aid){
		        	for($i=0;$i<count($data);$i++){
		        		$data[$i]['aid'] = $aid;
		        	}
		        }

		        if(count($data[0]) !== count($fields)){
		        	return '导入数据字段不匹配';
		        }

		        if(\method_exists($this,'validate')){
		        	
		        }
		        var_dump($data);
		        
		        $success_number = 0;
		        $failed_number = 0;
		        $keys = [];
		        $value = "";
		        $param = [];
		        $time = time();
		        $ip = $request->getRealIp($safe_mode = true);

		        foreach($fields as $field){
		        	array_push($keys,"`".$this->convert($field)."`");
		        }
		        array_push($keys,"`CreateTime`");
		        array_push($keys,"`CreateIP`");

		        // var_dump('keys: ',$keys);
		        for($i=0;$i<count($data);$i++){
		        	$value .= "(";
		        	foreach($data[$i] as $k => $v){
		        		$value .= "?,";
		        		$v = $v ? $v : ($k=='H' ? 0 : '');
		        		array_push($param,$v);
		        	}
		        	$value .= "?,?";
		        	array_push($param,$time,$ip);
		        	$value = rtrim($value,",") . "),";
		        }
		        $value = trim($value,",");
		        if(!$keys || !$value || !$param){
		        	return '无效的数据格式';
		        }

		        $sql = "INSERT INTO `".$this->tab."` (".\implode(',',$keys).") VALUES $value";
		        // var_dump('import sql: '.$sql);
		        // var_dump($param);

		        // return 'aa';
		        $result = Db::insert($sql,$param);
		        if($result !== false){
		        	return ['success' => count($data),'fail' => 0];
		        }
		        return '导入失败';
    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
    	}

    	protected function validate(Request $request,$id = 0,$obj = null){
    		if(!$id){
    			$activity_id = $request->post('activity_id',0);
    			if(!$activity_id){
    				return '请选择活动';
    			}

    			$data['activity_id'] = $activity_id;
    		}
    		$enterprise_name = trim($request->post('enterprise_name',''));
    		$position = trim($request->post('position',''));
    		$idcard = trim($request->post('idcard',''));
    		$mobile = trim($request->post('mobile',''));
    		if(!$mobile){
    			return '手机号码不能为空';
    		}

    		if(!Server::isMobile($mobile)){
    			return '手机号码格式有误';
    		}

    		$data['mobile'] = $mobile;
    		$data['enterprise_name'] = $enterprise_name;
    		$data['position'] = $position;
    		$data['idcard'] = $idcard;

    		if($id){
    			$is_sign = $request->post('is_sign',0);
    			if($is_sign){
    				$data['sign_time'] = time();
    				$data['sign_ip'] = $request->getRealIp($safe_mode = true);
    			}
    			$data['is_sign'] = $is_sign;
    		}


    		return $data;
    	}

    	protected function getActionList(Request $request,$id = 0): array
    	{
    		if($id){
    			$data = $this->getList($request,$id);
    		}

    		$action = [
    			['type'=>'input','label'=>'姓名','prop'=>'realname','value'=>$data->realname ?? ''],
    			['type'=>'input','label'=>'身份证号','prop'=>'idcard','value'=>$data->idcard ?? ''],
    			['type'=>'input','label'=>'手机号码','prop'=>'mobile','value'=>$data->mobile ?? '','required'=>true],
    			['type'=>'input','label'=>'企业名称','prop'=>'enterprise_name','value'=>$data->enterprise_name ?? ''],
    			['type'=>'input','label'=>'职位','prop'=>'position','value'=>$data->position ?? ''],
    			['type'=>'input','label'=>'座次号','prop'=>'seat','value'=>$data->seat ?? '','required'=>true],
    		];

    		if($id){
    			array_push($action,['type'=>'switch','label'=>'签到','prop'=>'is_sign','value'=>$data->is_sign ?? 0]);
    		}else{
    			$_class_name = $this->getClassName('Activity');
    			$service = new $_class_name();
    			$activity = $service->getList($request,'option');
    			array_unshift($action, ['type'=>'select','label'=>'活动','prop'=>'activity_id','value'=>$data->activity_id ?? '','children'=>$activity,'required'=>true]);
    		}

    		return $action;
    	}

		protected function getMapList(Request $request): array
		{
			return [
				['type'=>'varchar','label'=>'ID','prop'=>'id'],
				['type'=>'varchar','label'=>'活动','prop'=>'activity_title'],
				['type'=>'varchar','label'=>'姓名','prop'=>'realname'],
				['type'=>'varchar','label'=>'手机号码','prop'=>'mobile'],
				['type'=>'varchar','label'=>'企业名称','prop'=>'enterprise_name'],
				['type'=>'varchar','label'=>'职位','prop'=>'position'],
				['type'=>'varchar','label'=>'座位号','prop'=>'seat'],
				['type'=>'switch','label'=>'签到','prop'=>'is_sign'],
				['type'=>'varchar','label'=>'业务员','prop'=>'manager'],
				// ['type'=>'varchar','label'=>'签到时间','prop'=>'sign_time','width'=>180],
			];
		}
    }
?>