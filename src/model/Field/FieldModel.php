<?php
	namespace dackou\model\Field;

	use support\Request;
	use support\Db;
	use dackou\model\Table\TableModel;

	class FieldModel extends \dackou\Model{
		protected $table = 'Field';
		protected $is_schema = false;
		protected $status_value = [0 => '未导入',1 => '已导入'];

		protected function getFieldList(Request $request,mixed $flag = false): array
		{
			$fields = [
				'id' 				=> 'ID',
				'table_id' 			=> 'TableID',
				'comment' 			=> 'Comment',
				'field_name' 		=> 'FieldName',
				'map_name' 			=> 'MapName',
				'field_type' 		=> 'FieldType',
				'length' 			=> 'Length',
				'is_primary_key' 	=> 'IsPrimaryKey',
				'is_key' 			=> 'IsKey',
				'is_unique' 		=> 'IsUnique',
				'is_must' 			=> 'IsMust',
				'is_show' 			=> 'IsShow',
				'is_add' 			=> 'IsAdd',
				'is_mod' 			=> 'IsMod',
				'is_search' 		=> 'IsSearch',
				'show_type' 		=> 'ShowType',
				'form_type' 		=> 'FormType',
				'width' 			=> 'Width',
				'align' 			=> 'Align',
				'default_value' 	=> 'DefaultValue',
				'rules' 			=> 'Rules',
				'prefix' 			=> 'Prefix',
				'suffix' 			=> 'Suffix',
				'prompt' 			=> 'Prompt',
				'callback_field' 	=> 'CallbackField',
				'callback_key' 		=> 'CallbackKey',
				'callback_title' 	=> 'CallbackTitle',
				'after' 			=> 'After',
				'status' 			=> 'Status',
				'sort' 				=> 'Sort',
				'create_time' 		=> 'CreateTime',
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

		// 默认数据
		protected function getDefaultData(Request $request){
			$arr = [
				'table'			=> 1,
				'field'			=> 2,
				'user'			=> 3,
				'role'			=> 4,
				'option'		=> 5,
				'city'			=> 6,
				'menu'			=> 7,
				'unit'			=> 8,
			];

			$data = [];
			foreach($arr as $key => $val){
				$id = $val;
				$table = $this->prefix.$key;

				$sql = "SHOW FULL FIELDS FROM `{$table}`";
				$obj = Db::select($sql);
				foreach($obj as $item){
					$rules = json_encode(in_array($item->Field,['Title']) ? [['required'=>true,'message'=>$item->Comment.'不能为空']] : []);
					array_push($data,[
						'TableID'		=> $id,
						'Comment'		=> $item->Comment ?: '',
						'FieldName'		=> $item->Field,
						'MapName'		=> $this->convert($item->Field,false),
						'FieldType'		=> 'varchar',
						'Length'		=> '',
						'IsPrimaryKey'	=> $item->Key == 'PRI' ? 1 : 0,
						'IsKey'			=> ($item->Key && $item->Key != 'PRI') ? 1 : 0,
						'IsUnique'		=> 1,
						'IsMust'		=> 0,
						'IsShow'		=> 1,
						'IsAdd'			=> !in_array($item->Field,['CreateTime','UpdateTime','IsDel']) ? 1 : 0,
						'IsMod'			=> !in_array($item->Field,['CreateTime','UpdateTime','IsDel']) ? 1 : 0,
						'IsSearch'		=> in_array($item->Field,['Title']) ? 1 : 0,
						'ShowType'		=> 'int',
						'FormType'		=> ($item->Type == "tinyint(1)" && substr($item->Field,0,2) == 'Is') ? 'switch' : 'input',
						'Width'			=> 0,
						'Align'			=> '',
						'DefaultValue'	=> $item->Default ?: '',
						'Rules'			=> $rules,
						'Prefix'		=> '',
						'Suffix'		=> '',
						'Prompt'		=> '',
						'CallbackField'	=> '',
						'CallbackKey'	=> '',
						'CallbackTitle'	=> '',
						'After'			=> '',
						'Status'		=> 1,
					]);
				}


				// foreach($data as $item){
				// 	$this->insertData($request,$item);
				// }

				// $sql = "UPDATE `".$this->prefix."table` SET `Fields` = ? WHERE ID = ?";
				// $param = [json_encode($data),$id];
				// if(Db::update($sql,$param)){
				// 	var_dump($key . ' okokok');
				// }else{
				// 	var_dump($key. ' nonono');
				// }
			}

			return $data;
		}

		protected function getPageList(Request $request){
			try{
				$field = $this->getList($request,'field',true);
				$where = $this->getWhere($request);
				$tab_id = $request->input('tab_id',0);
				if($tab_id){
					array_push($where,[$this->table.'.TableID','=',$tab_id]);
				}
				// var_dump($where);
				$rows = Db::table($this->table)
							->join('table','TableID','=','table.ID')
							->where($where)
							->count();
				[$offset,$limit] = $this->getLimit($request);
				$object = Db::table($this->table)
							->join('table','TableID','=','table.ID')
							->select(...$field)
							->orderBy($this->table.'.TableID','asc')
							->orderBy($this->table.'.Sort','asc')
							->where($where);
				if(!$tab_id){
					$object = $object->offset($offset)
							->limit($limit);
				}
				$object = $object->get();
				if($object){
					foreach($object as $key => $val){
						$object[$key]->status_title = $this->status_value[$object[$key]->status];
					}
				}

				if($tab_id){
					$this->pagesize = $rows;
				}

				return ['rows' => $rows,'data' => $object];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		protected function getOptionList(Request $request,mixed $param = null,array $disabled = [],array $fields = [])
		{
			try{
				$field = ["ID as value","FieldName as label"];
				$param = $param ? $param : ['TableID' => 1];
				$where = [['IsDel','=',0]];
				if($param && is_array($param)){
					foreach($param as $key => $val){
						array_push($where,[$key,'=',$val]);
					}
				}
				$object = Db::table($this->table)
							->select(...$field)
							->where($where)
							->orderBy('Sort','asc')
							->get();

				if($object){
					$option = [];
					foreach($object as $key => $val){
						array_push($option,['type'=>'option','label'=>$val->label,'value'=>$val->value]);
					}
					return $option;
				}
				return [];
			}catch(\Exception $e){
				return [];
			}
		}

		protected function getQueryList(Request $request): array
		{
			try{	
				$tab_id = $request->input('tab_id','');
				if($tab_id){
					$tab_id = (int)$tab_id;
				}

				$service = new TableModel();
				$option = $service->getList($request,'option');
				// array_unshift($option,['type'=>'option','label'=>'-All-','value'=>'']);

				$action = [
					['type'=>'select','label'=>'数据表','prop'=>'tab_id','value'=>$tab_id,'children'=>$option]
				];

				return $action;
			}catch(\Exception $e){
				return [];
			}
		}

		public function initField(Request $request,$data){
			try{
				$fields = $data['fields'];
				if($fields){
					$title = $data['title'];
					$prefix = $this->prefix;
					$tab = $this->convert($data['table_name']);
					$table = $this->convert($data['table_name'],false);
					$increment_value = $data['increment_value'] ?? 1;
					$sql = "CREATE TABLE IF NOT EXISTS `{$prefix}{$table}` (";
					$insert_data = [];
					$ip = $request->getRealIp($safe_mode = true);
					foreach($fields as $field){
						if($field == 'ID'){
							$sql .= "\n\t`ID` INT NOT NULL AUTO_INCREMENT COMMENT 'ID',";
							array_push($insert_data,[
								'TableID' => $data['id'],
								'Comment' => 'ID',
								'FieldName' => 'ID',
								'MapName' => 'id',
								'FieldType' => 'int',
								'Length' => 10,
								'IsPrimaryKey' => 1,
								'IsKey' => 1,
								'IsUnique' => 1,
								'IsMust' => 1,
								'IsShow' => 1,
								'IsAdd' => 0,
								'IsMod' => 0,
								'IsSearch' => 0,
								'ShowType' => 'id',
								'FormType' => 'input',
								'Width' => 0,
								'Align' => $data['align'],
								'DefaultValue' => 1,
								'Rules' => '',
								'Prefix' => '',
								'Suffix' => '',
								'Prompt' => '',
								'CallbackField' => '',
								'CallbackKey' => '',
								'CallbackTitle' => '',
								'After' => '',
								'Status' => 1,
								'Sort' => $data['id']*100+1,
								'CreateTime' => time(),
								'CreateIP' => $ip,
								'UpdateTime' => time()
							]);
						}elseif($field == 'Code'){
							$sql .= "\n\t`{$tab}Code` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '{$title}编码',";
							array_push($insert_data,[
								'TableID' => $data['id'],
								'Comment' => $title.'编码',
								'FieldName' => $tab.'Code',
								'MapName' => $table.'_code',
								'FieldType' => 'varchar',
								'Length' => 32,
								'IsPrimaryKey' => 0,
								'IsKey' => 1,
								'IsUnique' => 0,
								'IsMust' => 1,
								'IsShow' => 1,
								'IsAdd' => 1,
								'IsMod' => 0,
								'IsSearch' => 1,
								'ShowType' => 'varchar',
								'FormType' => 'input',
								'Width' => 0,
								'Align' => $data['align'],
								'DefaultValue' => '',
								'Rules' => '',
								'Prefix' => '',
								'Suffix' => '',
								'Prompt' => '',
								'CallbackField' => '',
								'CallbackKey' => '',
								'CallbackTitle' => '',
								'After' => '',
								'Status' => 1,
								'Sort' => $data['id']*100+3,
								'CreateTime' => time(),
								'CreateIP' => $ip,
								'UpdateTime' => time()
							]);
						}elseif($field == 'Status'){
							$sql .= "\n\t`Status` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '状态',";
							array_push($insert_data,[
								'TableID' => $data['id'],
								'Comment' => '状态',
								'FieldName' => 'Status',
								'MapName' => 'status',
								'FieldType' => 'varchar',
								'Length' => 1,
								'IsPrimaryKey' => 0,
								'IsKey' => 1,
								'IsUnique' => 0,
								'IsMust' => 1,
								'IsShow' => 1,
								'IsAdd' => 0,
								'IsMod' => 1,
								'IsSearch' => 2,
								'ShowType' => 'varchar',
								'FormType' => 'input',
								'Width' => 0,
								'Align' => $data['align'],
								'DefaultValue' => 0,
								'Rules' => '',
								'Prefix' => '',
								'Suffix' => '',
								'Prompt' => '',
								'CallbackField' => '',
								'CallbackKey' => '',
								'CallbackTitle' => '',
								'After' => '',
								'Status' => 1,
								'Sort' => $data['id']*100+92,
								'CreateTime' => time(),
								'CreateIP' => $ip,
								'UpdateTime' => time()
							]);
						}elseif($field == 'CreateTime'){
							$sql .= "\n\t`CreateTime` INT NOT NULL DEFAULT '0' COMMENT '创建时间',";
							array_push($insert_data,[
								'TableID' => $data['id'],
								'Comment' => '创建时间',
								'FieldName' => 'CreateTime',
								'MapName' => 'create_time',
								'FieldType' => 'int',
								'Length' => 10,
								'IsPrimaryKey' => 0,
								'IsKey' => 0,
								'IsUnique' => 0,
								'IsMust' => 0,
								'IsShow' => 1,
								'IsAdd' => 0,
								'IsMod' => 0,
								'IsSearch' => 0,
								'ShowType' => 'varchar',
								'FormType' => 'input',
								'Width' => 0,
								'Align' => $data['align'],
								'DefaultValue' => 0,
								'Rules' => '',
								'Prefix' => '',
								'Suffix' => '',
								'Prompt' => '',
								'CallbackField' => '',
								'CallbackKey' => '',
								'CallbackTitle' => '',
								'After' => '',
								'Status' => 1,
								'Sort' => $data['id']*100+93,
								'CreateTime' => time(),
								'CreateIP' => $ip,
								'UpdateTime' => time()
							]);
						}elseif($field == 'CreateIP'){
							$sql .= "\n\t`CreateIP` VARCHAR(32) DEFAULT '' COMMENT '创建IP',";
							array_push($insert_data,[
								'TableID' => $data['id'],
								'Comment' => '创建IP',
								'FieldName' => 'CreateIP',
								'MapName' => 'create_ip',
								'FieldType' => 'varchar',
								'Length' => 32,
								'IsPrimaryKey' => 0,
								'IsKey' => 0,
								'IsUnique' => 0,
								'IsMust' => 0,
								'IsShow' => 0,
								'IsAdd' => 0,
								'IsMod' => 0,
								'IsSearch' => 0,
								'ShowType' => 'varchar',
								'FormType' => 'input',
								'Width' => 0,
								'Align' => $data['align'],
								'DefaultValue' => '',
								'Rules' => '',
								'Prefix' => '',
								'Suffix' => '',
								'Prompt' => '',
								'CallbackField' => '',
								'CallbackKey' => '',
								'CallbackTitle' => '',
								'After' => '',
								'Status' => 1,
								'Sort' => $data['id']*100+94,
								'CreateTime' => time(),
								'CreateIP' => $ip,
								'UpdateTime' => time()
							]);
						}elseif($field == 'UpdateTime'){
							$sql .= "\n\t`UpdateTime` INT NOT NULL DEFAULT '0' COMMENT '更新时间',";
							array_push($insert_data,[
								'TableID' => $data['id'],
								'Comment' => '修改时间',
								'FieldName' => 'UpdateTime',
								'MapName' => 'update_time',
								'FieldType' => 'int',
								'Length' => 10,
								'IsPrimaryKey' => 0,
								'IsKey' => 0,
								'IsUnique' => 0,
								'IsMust' => 0,
								'IsShow' => 0,
								'IsAdd' => 0,
								'IsMod' => 0,
								'IsSearch' => 0,
								'ShowType' => 'varchar',
								'FormType' => 'input',
								'Width' => 0,
								'Align' => $data['align'],
								'DefaultValue' => 0,
								'Rules' => '',
								'Prefix' => '',
								'Suffix' => '',
								'Prompt' => '',
								'CallbackField' => '',
								'CallbackKey' => '',
								'CallbackTitle' => '',
								'After' => '',
								'Status' => 1,
								'Sort' => $data['id']*100+95,
								'CreateTime' => time(),
								'CreateIP' => $ip,
								'UpdateTime' => time()
							]);
						}elseif($field == 'UpdateIP'){
							$sql .= "\n\t`UpdateIP` VARCHAR(32) DEFAULT '' COMMENT '更新IP',";
							array_push($insert_data,[
								'TableID' => $data['id'],
								'Comment' => '修改IP',
								'FieldName' => 'UpdateIP',
								'MapName' => 'update_ip',
								'FieldType' => 'varchar',
								'Length' => 32,
								'IsPrimaryKey' => 0,
								'IsKey' => 0,
								'IsUnique' => 0,
								'IsMust' => 0,
								'IsShow' => 0,
								'IsAdd' => 0,
								'IsMod' => 0,
								'IsSearch' => 0,
								'ShowType' => 'varchar',
								'FormType' => 'input',
								'Width' => 0,
								'Align' => $data['align'],
								'DefaultValue' => '',
								'Rules' => '',
								'Prefix' => '',
								'Suffix' => '',
								'Prompt' => '',
								'CallbackField' => '',
								'CallbackKey' => '',
								'CallbackTitle' => '',
								'After' => '',
								'Status' => 1,
								'Sort' => $data['id']*100+96,
								'CreateTime' => time(),
								'CreateIP' => $ip,
								'UpdateTime' => time()
							]);
						}elseif($field == 'DeleteTime'){
							$sql .= "\n\t`DeleteTime` INT NOT NULL DEFAULT '0' COMMENT '删除时间',";
							array_push($insert_data,[
								'TableID' => $data['id'],
								'Comment' => '删除时间',
								'FieldName' => 'DeleteTime',
								'MapName' => 'delete_time',
								'FieldType' => 'int',
								'Length' => 10,
								'IsPrimaryKey' => 0,
								'IsKey' => 0,
								'IsUnique' => 0,
								'IsMust' => 0,
								'IsShow' => 0,
								'IsAdd' => 0,
								'IsMod' => 0,
								'IsSearch' => 0,
								'ShowType' => 'varchar',
								'FormType' => 'input',
								'Width' => 0,
								'Align' => $data['align'],
								'DefaultValue' => 0,
								'Rules' => '',
								'Prefix' => '',
								'Suffix' => '',
								'Prompt' => '',
								'CallbackField' => '',
								'CallbackKey' => '',
								'CallbackTitle' => '',
								'After' => '',
								'Status' => 1,
								'Sort' => $data['id']*100+97,
								'CreateTime' => time(),
								'CreateIP' => $ip,
								'UpdateTime' => time()
							]);
						}elseif($field == 'DeleteIP'){
							$sql .= "\n\t`DeleteIP` VARCHAR(32) DEFAULT '' COMMENT '删除IP',";
							array_push($insert_data,[
								'TableID' => $data['id'],
								'Comment' => '删除IP',
								'FieldName' => 'DeleteIP',
								'MapName' => 'delete_ip',
								'FieldType' => 'varchar',
								'Length' => 32,
								'IsPrimaryKey' => 0,
								'IsKey' => 0,
								'IsUnique' => 0,
								'IsMust' => 0,
								'IsShow' => 0,
								'IsAdd' => 0,
								'IsMod' => 0,
								'IsSearch' => 0,
								'ShowType' => 'varchar',
								'FormType' => 'input',
								'Width' => 0,
								'Align' => $data['align'],
								'DefaultValue' => '',
								'Rules' => '',
								'Prefix' => '',
								'Suffix' => '',
								'Prompt' => '',
								'CallbackField' => '',
								'CallbackKey' => '',
								'CallbackTitle' => '',
								'After' => '',
								'Status' => 1,
								'Sort' => $data['id']*100+98,
								'CreateTime' => time(),
								'CreateIP' => $ip,
								'UpdateTime' => time()
							]);
						}elseif($field == 'IsDel'){
							$sql .= "\n\t`IsDel` TINYINT(1) DEFAULT '0' COMMENT '删除',";
							array_push($insert_data,[
								'TableID' => $data['id'],
								'Comment' => '删除',
								'FieldName' => 'IsDel',
								'MapName' => 'is_del',
								'FieldType' => 'tinyint',
								'Length' => 1,
								'IsPrimaryKey' => 0,
								'IsKey' => 0,
								'IsUnique' => 0,
								'IsMust' => 0,
								'IsShow' => 0,
								'IsAdd' => 0,
								'IsMod' => 0,
								'IsSearch' => 0,
								'ShowType' => 'varchar',
								'FormType' => 'input',
								'Width' => 0,
								'Align' => $data['align'],
								'DefaultValue' => 0,
								'Rules' => '',
								'Prefix' => '',
								'Suffix' => '',
								'Prompt' => '',
								'CallbackField' => '',
								'CallbackKey' => '',
								'CallbackTitle' => '',
								'After' => '',
								'Status' => 1,
								'Sort' => $data['id']*100+99,
								'CreateTime' => time(),
								'CreateIP' => $ip,
								'UpdateTime' => time()
							]);
						}
					}
					$sql .= "\n\tPRIMARY KEY (`ID`) USING BTREE";
					$sql .= "\n) ENGINE=InnoDB AUTO_INCREMENT={$increment_value} DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='{$title}'";
					// var_dump('init field sql:' . $sql);
					$result = Db::select($sql);
					// var_dump('result: ',$result);
					if($result !== false){
						if($insert_data){
							$this->insertData($request,$insert_data);
						}
						return true;
					}
					return false;
				}

				return true;
			}catch(\Exception $e){
				var_dump('error: ',$this->getExceptionError($e));
				return false;
			}

			try{
				$field_group = $data['field_group'];
				$fields = [];
				if($field_group){
					if(in_array('id',$field_group)){
						array_push($fields,'ID');
					}
					if(!in_array('is_key',$field_group)){
						$temp = $data['fields'][1];
						if($temp['name'] && !in_array($temp,['id','ID'])){
							array_push($fields,$this->convert($temp['name']));
						}
					}
					if(in_array('create',$field_group)){
						array_push($fields,'CreateTime','CreateIP');
					}
					if(in_array('update',$field_group)){
						array_push($fields,'UpdateTime','UpdateIP');
					}
					if(in_array('delete',$field_group)){
						array_push($fields,'DeleteTime','DeleteIP','IsDel');
					}
				}

				$sqls = [];
				$table = $data['table_name'];
				$field = $this->getList($request,'field','key',$table);
				if($field && $fields){
					$keys = \array_keys($field);
					foreach($fields as $fied){
						if(!in_array($fied,$keys)){
							if($fied == 'ID'){
								$temp = $data['fields'][0];
								array_push($sqls,"ALTER TABLE `".$this->prefix.$table."` ADD `{$fied}` ".$temp['type']."(".$temp['length'].") NOT NULL DEFAULT '".$temp['default']."' AUTO_INCREMENT PRIMARY KEY COMMENT 'ID'");
							}elseif($fied == 'CreateTime'){
								array_push($sqls,"ALTER TABLE `".$this->prefix.$table."` ADD `CreateTime` INT DEFAULT UNIX_TIMESTAMP() COMMENT '创建时间'");
							}elseif($fied == 'CreateIP'){
								array_push($sqls,"ALTER TABLE `".$this->prefix.$table."` ADD `CreateIP` VARCHAR(32) DEFAULT '' COMMENT '创建IP'");
							}elseif($fied == 'UpdateTime'){
								array_push($sqls,"ALTER TABLE `".$this->prefix.$table."` ADD `UpdateTime` INT DEFAULT UNIX_TIMESTAMP() COMMENT '更新时间'");
							}elseif($fied == 'UpdateIP'){
								array_push($sqls,"ALTER TABLE `".$this->prefix.$table."` ADD `UpdateIP` VARCHAR(32) DEFAULT '' COMMENT '更新IP'");
							}elseif($fied == 'DeleteTime'){
								array_push($sqls,"ALTER TABLE `".$this->prefix.$table."` ADD `DeleteTime` INT DEFAULT UNIX_TIMESTAMP() COMMENT '删除时间'");
							}elseif($fied == 'DeleteIP'){
								array_push($sqls,"ALTER TABLE `".$this->prefix.$table."` ADD `DeleteIP` VARCHAR(32) DEFAULT '' COMMENT '删除IP'");
							}elseif($fied == 'IsDel'){
								array_push($sqls,"ALTER TABLE `".$this->prefix.$table."` ADD `IsDel` TINYINT(1) DEFAULT '0' COMMENT '删除'");
							}else{
								$temp = $data['fields'][1];
								array_push($sqls,"ALTER TABLE `".$this->prefix.$table."` ADD `".$temp['name']."` ".$temp['type']."(".$temp['length'].") NOT NULL DEFAULT '".$temp['default']."' COMMENT '".$temp['name']."'");
							}


						}
					}
				}
				// var_dump('sqls: ',$sqls);
				return false;
			}catch(\Exception $e){
				// var_dump('error: ',$this->getExceptionError($e));
				return false;
			}
		}

		private function isExists($field = '',$table = ''){
			try{
				$fields = Db::select("SHOW FULL FIELDS FROM `".$this->prefix.$table."`");
				if($fields){
					$field = $this->convert($field);
					$flag = false;
					foreach($fields as $item){
						if($item->Field == $field){
							$flag = true;
							break;
						}
					}
					return $flag;
				}
				return false;
			}catch(\Exception $e){
				return false;
			}
		}

		protected function setExcute(Request $request,$data,$flag = false){
			try{
				if(!$flag){
					$id = $data['id'];
					$field_name = $data['field_name'];
					$table_name = $this->convert($data['table_name'],false);
					if($this->isExists($field_name,$table_name)){
						$this->updateData($request,['Status'=>1],$id);
						return true;
					}
					$type = \strtoupper($data['field_type']);
					$default = $data['default_value'];
					$comment = $data['comment'];

					$sql = "ALTER TABLE `".$this->prefix.$table_name."` ADD `{$field_name}` {$type}";
					if($data['length']){
						$sql .= "(".$data['length'].")";
					}
					$sql .= " NOT NULL DEFAULT '{$default}' COMMENT '{$comment}'";
					if($data['after']){
						$sql .= " AFTER `".$data['after']."`";	
					}

					$result = Db::select($sql);

					return $this->updateData($request,['Status'=>1],$id);
				}
				return true;
			}catch(\Exception $e){
				// var_dump('error: ',$this->getExceptionError($e));
				return false;
			}
		}

		/**
		 * [validate description]
		 * @param  Request $request [description]
		 * @param  integer $id      [description]
		 * @return [type]           [description]
		 */
		protected function validate(Request $request,$id = 0,$obj = null){
			// var_dump($request->post());
			$table_id = $request->post('table_id',0);
			if(!$table_id){
				return '请选择表名';
			}

			$service = new TableModel();
			$table = $service->getList($request,$table_id);
			if(!$table || !is_object($table)){
				return '无效的表名';
			}

			$sort = $this->getMaxSort($table_id);

			$comment = trim($request->post('comment',''));
			if(!$comment){
				return '字段描述不能为空';
			}

			if($this->checkExists(['TableID' => $table_id,'Comment' => $comment],$id)){
				return '字段描述已存在,请重新输入';
			}

			$field_name = trim($request->post('field_name',''));
			if(!$field_name){
				return '字段名不能为空';
			}

			if($this->checkExists(['TableID' => $table_id,'FieldName' => $field_name],$id)){
				return '表字段已存在,请重新输入';
			}

			$map_name = trim($request->post('map_name',''));
			if(!$map_name){
				return '字段映射名不能为空';
			}

			if($this->checkExists(['TableID' => $table_id,'MapName' => $map_name],$id)){
				return '表映射字段已存在,请重新输入';
			}

			$is_primary_key = $request->post('is_primary_key',0);

			$is_key = $request->post('is_key',0);

			$is_unique = $request->post('is_unique',0);
			$is_must = $request->post('is_must',0);

			$is_show = $request->post('is_show',0);

			$is_add = $request->post('is_add',0);

			$is_mod = $request->post('is_mod',0);

			$is_search = $request->post('is_search',0);

			$show_type = trim($request->post('show_type','varchar'));

			$form_type = $request->post('form_type');
			if(is_array($form_type)){
				$form_type = end($form_type);
			}

			$length = $request->post('length','');
			if(trim($length) == '' || !is_numeric($length)){
				$length = 0;
			}

			$width = $request->post('width','');
			if(trim($width) == '' || !is_numeric($width)){
				$width = 0;
			}
			$align = trim($request->post('align',''));

			$default_value = trim($request->post('default_value',''));

			$rules = $request->post('rules',[]);

			$prefix = trim($request->post('prefix',''));

			$suffix = trim($request->post('suffix',''));

			$prompt = trim($request->post('prompt',''));
			$callback_field = trim($request->post('callback_field',''));
			$callback_key = trim($request->post('callback_key',''));
			$callback_title = trim($request->post('callback_title',''));
			$after = $request->post('after');

			$data['table_id'] = $table_id;
			$data['table_name'] = $table->table_name;
			$data['comment'] = $comment;
			$data['field_name'] = $field_name;
			$data['map_name'] = $map_name;
			$data['is_primary_key'] = $is_primary_key;
			$data['is_key'] = $is_key;
			$data['is_unique'] = $is_unique;
			$data['is_must'] = $is_must;
			$data['is_show'] = $is_show;
			$data['is_add'] = $is_add;
			$data['is_mod'] = $is_mod;
			$data['is_search'] = $is_search;
			$data['show_type'] = $show_type;
			$data['form_type'] = $form_type;
			$data['length'] = $length;
			$data['width'] = $width;
			$data['align'] = $align;
			$data['default_value'] = $default_value;
			$data['rules'] = $rules;
			$data['prefix'] = $prefix;
			$data['suffix'] = $suffix;
			$data['prompt'] = $prompt;
			$data['callback_field'] = $callback_field;
			$data['callback_key'] = $callback_key;
			$data['callback_title'] = $callback_title;
			$data['after'] = $after;
			$data['sort'] = $sort;

			// var_dump($data);
			// return 'aa';
			return $data;
		}

		protected function getMaxSort(int $table_id = 0, $flag = true): int
		{
			if(empty($table_id) || !$table_id){
				return 1;
			}
			try{
				$where = [
					['TableID','=',$table_id],
					['Sort','<',($table_id*100+92)]
				];
				$object = Db::table($this->table)
							->where($where)
							->max('Sort');
				// var_dump($object);
				return $object ? $object+1 : $table_id*100+1;
			}catch(\Exception $e){
				// var_dump('error: ',$this->getExceptionError($e));
				return $table_id*100+1;
			}
		}

		/**
		 * [getActionList description]
		 * @param  Request $request [description]
		 * @param  integer $id      [description]
		 * @return [type]           [description]
		 */
		protected function getActionList(Request $request,$id = 0): array
		{
			if($id){
				$data = $this->getList($request,$id);
			}

			$table = [];
			$table_id = $request->input('table_id','');
			if($table_id){
				$service = new TableModel();
				$tab = $service->getList($request,(int)$table_id);
				if($tab){
					array_push($table,['type'=>'option','label'=>$tab->title,'value'=>(int)$table_id]);
				}
			}else{
				$service = new TableModel();
				$table = $service->getList($request,'option');
			}

			$tabfield = [];

			$field_type = [
				['type'=>'option','label'=>'varchar','value'=>'varchar'],
				['type'=>'option','label'=>'int','value'=>'int'],
				['type'=>'option','label'=>'tinyint','value'=>'tinyint'],
				['type'=>'option','label'=>'text','value'=>'text']
			];

			$show_type = [
				['type'=>'option','label'=>'文本','value'=>'varchar'],
				['type'=>'option','label'=>'数字','value'=>'number'],
				['type'=>'option','label'=>'图片','value'=>'img'],
				['type'=>'option','label'=>'开关','value'=>'switch'],
				['type'=>'option','label'=>'勾选','value'=>'check'],
			];

			$form_type = $this->getList($request,'formtype');

			$align = [
				['type'=>'option','label'=>'左对齐','value'=>'left'],
				['type'=>'option','label'=>'居中对齐','value'=>'center'],
				['type'=>'option','label'=>'右对齐','value'=>'right'],
			];

			$field_list = $this->getList($request,'option',[],['TableID'=>($table_id?$table_id:1)]);
			var_dump($field_list);
			$iafter = 'ID';
			if($field_list){
				for($i=0;$i<count($field_list);$i++){
					$field_list[$i]['value'] = $field_list[$i]['label'];
					if(!in_array($field_list[$i]['label'],['CreateTime','CreateIP','UpdateTime','UpdateIP','DeleteTime','DeleteIP','IsDel'])){
						$iafter = $field_list[$i]['label'];
					}
				}
			}
			var_dump($field_list);
			$sort = $this->getMaxSort($table_id);

			$action = [
				['type'=>'select','label'=>'表名','prop'=>'table_id','value'=>$data->table_id??($table_id?$table_id:''),'hidden'=>$table_id?true:false,'placeholder'=>'选择表','children'=>$table,'rules'=>['required'=>true,'message'=>'请选择表']],
				['type'=>'input','label'=>'字段描述','prop'=>'comment','value'=>$data->comment??'','placeholder'=>'字段描述','rules'=>['required'=>true,'message'=>'字段描述不能为空']],
				['type'=>'input','label'=>'旧字段名','prop'=>'old_field','value'=>$data->field_name??'','hidden'=>true,'placeholder'=>'旧字段名'],
				['type'=>'input','label'=>'字段名','prop'=>'field_name','value'=>$data->field_name??'','placeholder'=>'字段名','rules'=>['required'=>true,'message'=>'字段名不能为空']],
				['type'=>'input','label'=>'字段映射名','prop'=>'map_name','value'=>$data->map_name??'','placeholder'=>'字段映射名','rules'=>['required'=>true,'message'=>'字段映射名不能为空']],
				['type'=>'select','label'=>'字段类型','prop'=>'field_type','value'=>$data->field_type??'varchar','placeholder'=>'字段类型','children'=>$field_type,'rules'=>['required'=>true,'message'=>'请选择字段类型']],
				['type'=>'input','label'=>'字段长度','prop'=>'length','value'=>$data->length??'','placeholder'=>'字段长度'],
				['type'=>'form-group','label'=>'主键索引','children'=>[
					['type'=>'switch','label'=>'主键','prop'=>'is_primary_key','value'=>$data->is_primary_key??0,'attrs'=>['active-value'=>1,'inactive-value'=>0,'inline-prompt'=>true,'active-text'=>'主键','inactive-text'=>'主键']],
					['type'=>'switch','label'=>'索引','prop'=>'is_key','value'=>$data->is_key??0,'attrs'=>['active-value'=>1,'inactive-value'=>0,'inline-prompt'=>true,'active-text'=>'索引','inactive-text'=>'索引']],
					['type'=>'switch','label'=>'唯一','prop'=>'is_unique','value'=>$data->is_unique??0,'attrs'=>['active-value'=>1,'inactive-value'=>0,'inline-prompt'=>true,'active-text'=>'唯一','inactive-text'=>'唯一']],
					['type'=>'switch','label'=>'必填','prop'=>'is_must','value'=>$data->is_must??0,'attrs'=>['active-value'=>1,'inactive-value'=>0,'inline-prompt'=>true,'active-text'=>'必填','inactive-text'=>'必填']],
				]],
				['type'=>'form-group','label'=>'表单项','children'=>[
					['type'=>'switch','label'=>'显示','prop'=>'is_show','value'=>$data->is_show??1,'attrs'=>['active-value'=>1,'inactive-value'=>0,'inline-prompt'=>true,'active-text'=>'显示','inactive-text'=>'显示']],
					['type'=>'switch','label'=>'新增','prop'=>'is_add','value'=>$data->is_add??1,'attrs'=>['active-value'=>1,'inactive-value'=>0,'inline-prompt'=>true,'active-text'=>'新增','inactive-text'=>'新增']],
					['type'=>'switch','label'=>'修改','prop'=>'is_mod','value'=>$data->is_mod??1,'attrs'=>['active-value'=>1,'inactive-value'=>0,'inline-prompt'=>true,'active-text'=>'修改','inactive-text'=>'修改']],
					// ['type'=>'switch','label'=>'查询','prop'=>'is_search','value'=>$data->is_search??0,'attrs'=>['active-value'=>1,'inactive-value'=>0,'inline-prompt'=>true,'active-text'=>'查询','inactive-text'=>'查询']],
				]],
				['type'=>'radio-group','label'=>'查询','prop'=>'is_search','value'=>$data->is_search??0,'children'=>[
					['label'=>'无查询','value'=>0],
					['label'=>'关键词查询','value'=>1],
					['label'=>'独立查询','value'=>2]
				]],
				['type'=>'select','label'=>'显示类型','prop'=>'show_type','value'=>$data->show_type??'varchar','placeholder'=>'选择显示类型','children'=>$show_type,'rules'=>['required'=>true,'message'=>'请选择显示类型']],
				['type'=>'cascader','label'=>'表单类型','prop'=>'form_type','value'=>$data->form_type??'input','attrs'=>['placeholder'=>'选择表单类型','options'=>$form_type],'rules'=>['required'=>true,'message'=>'请选择表单类型']],
				['type'=>'input','label'=>'宽度','prop'=>'width','value'=>$data->width??'','placeholder'=>'宽度','slot'=>['suffix'=>['value'=>'px']]],
				['type'=>'radio-group','label'=>'对齐方式','prop'=>'align','value'=>$data->align??'left','placeholder'=>'选择对齐方式','children'=>$align],
				['type'=>'input','label'=>'默认值','prop'=>'default_value','value'=>$data->default_value??'','placeholder'=>'默认值'],
				['type'=>'input','label'=>'验证规则','prop'=>'rules','value'=>$data->rules??'','placeholder'=>'验证规则','attrs'=>['type'=>'textarea']],
				['type'=>'form-group','label'=>'前后缀','delimiter'=>'-','children'=>[
					['type'=>'input','label'=>'前缀','prop'=>'prefix','value'=>$data->prefix??'','placeholder'=>'前缀','attrs'=>['style'=>['width'=>'180px']]],
					['type'=>'input','label'=>'后缀','prop'=>'suffix','value'=>$data->suffix??'','placeholder'=>'后缀','attrs'=>['style'=>['width'=>'180px']]],
				]],
				['type'=>'input','label'=>'提示词','prop'=>'prompt','value'=>$data->prompt??'','placeholder'=>'提示词','attrs'=>['type'=>'textarea']],
				['type'=>'form-group','label'=>'回调参数','delimiter'=>'-','children'=>[
					['type'=>'select','label'=>'回调字段','prop'=>'callback_field','value'=>$data->callback_field??'','placeholder'=>'回调字段','attrs'=>['style'=>['width'=>'112px']],'children'=>$field_list],
					['type'=>'input','label'=>'回调标题','prop'=>'callback_title','value'=>$data->callback_title??'','placeholder'=>'回调标题','attrs'=>['style'=>['width'=>'112px']]],
					['type'=>'input','label'=>'回调Key','prop'=>'callback_key','value'=>$data->callback_key??'','placeholder'=>'回调Key','attrs'=>['style'=>['width'=>'112px']]],
				]],
				['type'=>'select','label'=>'插队字段','prop'=>'after','value'=>$data->after??$iafter,'placeholder'=>'插队字段','children'=>$field_list]
			];

			if($id){
				array_push($action,['type'=>'switch','label'=>'状态','prop'=>'status','value'=>$data->status??0,'attrs'=>['active-value'=>1,'inactive-value'=>0]]);
			}
			array_push($action,['type'=>'input','label'=>'排序','prop'=>'sort','value'=>$data->sort ?? $sort,'rules'=>['required'=>true,'message'=>'排序不能为空']]);

			return $action;
		}

		/**
		 * [getMapList description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		protected function getMapList(Request $request): array
		{
			return [
				['type'=>'int','label'=>'ID','prop'=>'id'],
				// ['type'=>'varchar','label'=>'表名','prop'=>'table_id'],
				['type'=>'varchar','label'=>'字段描述','prop'=>'comment','width'=>120],
				['type'=>'varchar','label'=>'字段名','prop'=>'field_name','width'=>120],
				['type'=>'varchar','label'=>'字段类型','prop'=>'field_type','width'=>100],
				['type'=>'varchar','label'=>'字段长度','prop'=>'length','width'=>100],
				['type'=>'varchar','label'=>'映射名','prop'=>'map_name','width'=>120],
				['type'=>'switch','label'=>'主键','prop'=>'is_primary_key'],
				['type'=>'switch','label'=>'索引','prop'=>'is_key'],
				['type'=>'switch','label'=>'唯一','prop'=>'is_unique'],
				['type'=>'switch','label'=>'必填','prop'=>'is_must'],
				['type'=>'switch','label'=>'显示','prop'=>'is_show'],
				['type'=>'switch','label'=>'新增','prop'=>'is_add'],
				['type'=>'switch','label'=>'修改','prop'=>'is_mod'],
				['type'=>'switch','label'=>'查询','prop'=>'is_search'],
				['type'=>'varchar','label'=>'显示类型','prop'=>'show_type','width'=>100],
				['type'=>'varchar','label'=>'表单类型','prop'=>'form_type','width'=>100],
				['type'=>'varchar','label'=>'默认值','prop'=>'default_value'],
				['type'=>'switch','label'=>'状态','prop'=>'status'],
				['type'=>'varchar','label'=>'排序','prop'=>'sort'],
			];
		}
	}
?>