<?php
	namespace dackou\model\Table;

	use support\Request;
	use support\Db;
	use dackou\model\Field\FieldModel;

	class TableModel extends \dackou\Model{
		protected $table = 'Table';
		protected $title = '数据模型表';

		protected function getFieldList(Request $request,mixed $flag = false){
			$field = [
				$this->table.".ID as id",
				"Title as title",
				"TableName as table_name",
				"PrimaryKey as primary_key",
				"IsSnowflake as is_snowflake",
				"IsCate as is_cate",
				"IncrementValue as increment_value",
				"Layer as layer",
				"Digit as digit",
				"ShowMethod as show_method",
				"IsPage as is_page",
				"Pagesize as pagesize",
				"PageGroup as page_group",
				"Align as align",
				"CateTable as cate_table",
				"CateValue as cate_value",
				"OptionKey as option_key",
				"IsStatus as is_status",
				"StatusValue as status_value",
				"OrderBy as order_by",
				"IsQrcode as is_qrcode",
				"IsDelete as is_delete",
				"IsTruncate as is_truncate",
				"FieldGroup as field_group",
				"Handle as handle",
				"Fields as fields",
				"CreateTime as create_time"
			];

			if($flag === false){
				return $field;
			}
			
			if($flag === true){
				$temp = [];
				foreach($field as $item){
					[$key,$val] = explode(" as ",$item);
					$temp[$key] = $val;
				}

				return $temp;
			}
			
			if($flag === 'key'){
				$keys = [];
				$vals = [];
				foreach($field as $item){
					[$key,$val] = \explode(' as ',$item);
					array_push($keys,$key);
					array_push($vals,$val);
				}
				return [$keys,$vals];
			}

			return $field;
		}

		// 默认数据
		protected function getDefaultData(Request $request){
			$data = [
				
			];

			return $data;
		}

		protected function getListById(Request $request,$id = 0){
			try{
				if(!$id){
					return 100005;
				}

				$field = $this->getList($request,'field');
				$where = $this->getWhere($request);
				array_push($where,['ID','=',$id]);
				$object = Db::table($this->table)
							->select(...$field)
							->where($where)
							->first();
				if($object){
					if($object->page_group){
						$object->page_group = $this->getDecodeData($object->page_group);
					}else{
						$object->page_group = [];
					}
					if($object->cate_value){
						$object->cate_value = $this->getDecodeData($object->cate_value);
					}else{
						$object->cate_value = [];
					}
					if($object->option_key){
						$object->option_key = $this->getDecodeData($object->option_key);
					}else{
						$object->option_key = [];
					}
					if($object->status_value){
						$object->status_value = $this->getDecodeData($object->status_value);
					}else{
						$object->status_value = [];
					}
					if($object->field_group){
						$object->field_group = $this->getDecodeData($object->field_group);
					}else{
						$object->field_group = [];
					}
					if($object->handle){
						$object->handle = $this->getDecodeData($object->handle);
					}else{
						$object->handle = [];
					}
					if($object->fields){
						$object->fields = $this->getDecodeData($object->fields);
					}else{
						$object->fields = [
							['name'=>'id','type'=>'int','length'=>1,'default'=>'1','incrent'=>1,'map'=>'id'],
							['name'=>'','type'=>'','length'=>'','default'=>'','incrent'=>0,'map'=>''],
							['name'=>'','type'=>'','length'=>'','default'=>'','incrent'=>0,'map'=>''],
						];
					}
				}
				return $object;
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		/**
		 * [getQueryList description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		protected function getQueryList(Request $request): array
		{
			$data = $this->getList($request);
			if($data){
				foreach($data as $item){

				}
			}

			$table_id = $request->input('table_id',0);
			$table = $this->getList($request,'option');
			$action = [
				['type'=>'select','label'=>'表名','prop'=>'table_id','value'=>$table_id?:'','placeholder'=>'选择表','children'=>$table]
			];

			return $action;
		}

		private function updateTable($data){
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
					if(in_array('code',$field_group)){
						array_push($fields,'Code');
					}
					if(in_array('status',$field_group)){
						array_push($fields,'Status');
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

				$title = $data['title'];
				$table = $this->convert($data['table_name'],false);
				$increment_value = $data['increment_value'];
				$prefix = $this->prefix;
				$sql = "CREATE TABLE IF NOT EXISTS `{$prefix}{$table}` (";
				foreach($fields as $field){
					if($field == 'ID'){
						$temp = $data['fields'][0];
						$type = strtoupper($temp['type']);
						$length = $temp['length'];
						$default = trim($temp['default']);

						$sql .= "\n\t`ID` {$type} NOT NULL AUTO_INCREMENT COMMENT 'ID',";
					}elseif($field == 'Code' && isset($data['fields'][2])){
						$temp = $data['fields'][2];
						$name = $this->convert($temp['name']);
						$type = strtoupper($temp['type']);
						$length = $temp['length'];
						$default = trim($temp['default']);
						
						$sql .= "\n\t`{$name}` {$type}({$length}) NOT NULL DEFAULT '{$default}' COMMENT '编码',";
					}elseif($field == 'Status'){
						$sql .= "\n\t`Status` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '状态',";
					}elseif($field == 'CreateTime'){
						$sql .= "\n\t`CreateTime` INT NOT NULL DEFAULT '0' COMMENT '创建时间',";
					}elseif($field == 'CreateIP'){
						$sql .= "\n\t`CreateIP` VARCHAR(32) DEFAULT '' COMMENT '创建IP',";
					}elseif($field == 'UpdateTime'){
						$sql .= "\n\t`UpdateTime` INT NOT NULL DEFAULT '0' COMMENT '更新时间',";
					}elseif($field == 'UpdateIP'){
						$sql .= "\n\t`UpdateIP` VARCHAR(32) DEFAULT '' COMMENT '更新IP',";
					}elseif($field == 'DeleteTime'){
						$sql .= "\n\t`DeleteTime` INT NOT NULL DEFAULT '0' COMMENT '删除时间',";
					}elseif($field == 'DeleteIP'){
						$sql .= "\n\t`DeleteIP` VARCHAR(32) DEFAULT '' COMMENT '删除IP',";
					}elseif($field == 'IsDel'){
						$sql .= "\n\t`IsDel` TINYINT(1) DEFAULT '0' COMMENT '删除',";
					}else{
						$temp = $data['fields'][1];
						$name = $this->convert($temp['name']);
						$type = strtoupper($temp['type']);
						$length = $temp['length'];
						$default = trim($temp['default']);

						if($type == "VARCHAR"){
							$sql .= "\n\t`{$name}` {$type}({$length}) NOT NULL DEFAULT '{$default}' COMMENT '{$name}',";
						}else{
							$sql .= "\n\t`{$name}` {$type} NOT NULL DEFAULT '{$default}' COMMENT '{$name}',";
						}
					}
				}
				$sql .= "\n\tPRIMARY KEY (`ID`) USING BTREE";
				$sql .= "\n) ENGINE=InnoDB AUTO_INCREMENT={$increment_value} DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='{$title}'";

				$result = Db::select($sql);
				var_dump('result: ',$result);
				return $result !== false ? true : false;
			}catch(\Exception $e){
				var_dump('error: ',$this->getExceptionError($e));
				return false;
			}
		}

		protected function setTruncate(Request $request){
			Db::table('field')->truncate();
			return true;
		}

		protected function setExcute(Request $request,$data,$flag = false){
			try{
				if(!$flag){
					$service = new FieldModel();
					return $service->initField($request,$data);
				}

				return true;
			}catch(\Exception $e){
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
			$title = trim($request->post('title',''));
			if(!$title){
				return '表描述不能为空';
			}
			
			if($this->checkExists(['Title' => $title],$id)){
				return '表描述已存在,请重新输入';
			}
			
			$table_name = trim($request->post('table_name',''));
			if(!$table_name){
				return '表名不能为空';
			}

			if($this->checkExists(['TableName' => $table_name],$id)){
				return '表名已存在,请重新输入';
			}

			$primary_key = trim($request->post('primary_key','id'));
			if(!$table_name){
				return '主键名不能为空';
			}

			$is_snowflake = $request->post('is_snowflake',0);
			if(!is_numeric($is_snowflake) || !in_array($is_snowflake,[0,1])){
				return '无效的雪花ID值';
			}

			$is_cate = $request->post('is_cate',0);
			if(!is_numeric($is_cate) || !in_array($is_cate,[0,1])){
				return '无效的分类表值';
			}

			$increment_value = $request->post('increment_value',0);
			if(!is_numeric($increment_value) || $increment_value < 1){
				return '无效的自增起始值';
			}

			$layer = $request->post('layer',1);
			if(!in_array($layer,[1,2,3,4,5])){
				return '层级只能填数字且大于0';
			}

			$digit = $request->post('digit',1);
			if(!is_numeric($digit) || $digit <= 0){
				return '主键位数只能填数字且大于0';
			}

			$show_method = trim($request->post('show_method','page'));
			if(!$show_method){
				return '默认方法不能为空';
			}
			if(!in_array($show_method,['page','all','tree','child'])){
				return '无效的默认方法';
			}

			$is_page = $request->post('is_page',1);
			if(!in_array($is_page,[0,1])){
				return '分页只能填0或1';
			}

			$pagesize = $request->post('pagesize',0);
			if(empty($pagesize) || !is_numeric($pagesize) || $pagesize <= 0){
				return '每页显示行数只能填整数';
			}
			$page_group = $request->post('page_group',[]);
			$align = $request->post('align','center');

			$cate_table = trim($request->post('cate_table',''));
			$cate_value = $request->post('cate_value',[]);
			// $config_table = trim($request->post('config_table',''));
			$option_key = $request->post('option_key');
			$is_status = $request->post('is_status',0);
			$status_value = $request->post('status_value',[]);
			$order_by = $request->post('order_by',[]);
			$is_qrcode = $request->post('is_qrcode',0);
			$is_delete = $request->post('is_delete',0);
			$is_truncate = $request->post('is_truncate',0);
			$field_group = $request->post('field_group',[]);
			$handle = $request->post('handle',[]);
			// $fields = $request->post('fields');

			// $id_field_type = $request->post('id_field_type');
			// $id_length = $request->post('id_length');
			// $id_default = $request->post('id_default');
			// $id_incrent = $request->post('id_incrent');
			// $id_map = $request->post('id_map');

			// $map_name = $request->post('map_name');
			// $map_field_type = $request->post('map_field_type');
			// $map_length = $request->post('map_length');
			// $map_default = $request->post('map_default');
			// $map_value = $request->post('map_value');

			// $code_name = $request->post('code_name');
			// $code_field_type = $request->post('code_field_type');
			// $code_length = $request->post('code_length');
			// $code_default = $request->post('code_default');
			// $code_value = $request->post('code_value');

			// $fields = [
			// 	['name'=>'ID','type'=>$id_field_type,'length'=>$id_length,'default'=>$id_default,'incrent'=>$id_incrent,'map'=>$id_map],
			// 	['name'=>$map_name,'type'=>$map_field_type,'length'=>$map_length,'default'=>$map_default,'incrent'=>0,'map'=>$map_value],
			// 	['name'=>$code_name,'type'=>$code_field_type,'length'=>$code_length,'default'=>$code_default,'incrent'=>0,'map'=>$code_value],
			// ];
			$fields = [];
			if($field_group){
				if(in_array('id',$field_group)){
					array_push($fields,'ID');
				}
				if(in_array('code',$field_group)){
					array_push($fields,'Code');
				}
				if(in_array('status',$field_group)){
					array_push($fields,'Status');
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

			$data['title'] = $title;
			$data['table_name'] = $table_name;
			$data['primary_key'] = $primary_key;
			$data['is_snowflake'] = $is_snowflake;
			$data['is_cate'] = $is_cate;
			$data['increment_value'] = $increment_value;
			$data['layer'] = $layer;
			$data['digit'] = $digit;
			$data['show_method'] = $show_method;
			$data['is_page'] = $is_page;
			$data['pagesize'] = $pagesize;
			$data['page_group'] = $page_group;
			$data['align'] = $align;
			$data['cate_table'] = $cate_table;
			$data['cate_value'] = $cate_value;
			$data['option_key'] = $option_key;
			$data['is_status'] = $is_status;
			$data['status_value'] = $status_value;
			$data['order_by'] = $order_by;
			$data['is_qrcode'] = $is_qrcode;
			$data['is_delete'] = $is_delete;
			$data['is_truncate'] = $is_truncate;
			$data['field_group'] = $field_group;
			$data['handle'] = $handle;
			$data['fields'] = $fields;

			// var_dump($data);
			// return 'aa';

			return $data;
		}

		protected function getActionList(Request $request,$id = 0): array
		{
			$fields = [['type'=>'option','label'=>'ID','value'=>'id']];
			
			if($id){
				$data = $this->getList($request,$id);
			}
			$data_fields = $id ? $data->fields : [
				['name'=>'id','type'=>'int','length'=>1,'default'=>'1','incrent'=>1,'map'=>'id'],
				['name'=>'','type'=>'','length'=>'','default'=>'','incrent'=>0,'map'=>''],
				['name'=>'','type'=>'','length'=>'','default'=>'','incrent'=>0,'map'=>''],
			];

			$layer = [
				['type'=>'option','label'=>'1层','value'=>1],
				['type'=>'option','label'=>'2层','value'=>2],
				['type'=>'option','label'=>'3层','value'=>3],
				['type'=>'option','label'=>'4层','value'=>4],
			];

			$show_method = [
				['type'=>'option','label'=>'分页','value'=>'page'],
				['type'=>'option','label'=>'全部','value'=>'all'],
				['type'=>'option','label'=>'树型','value'=>'tree'],
			];

			$_class_option = '\\app\\model\\Option\\OptionModel';
			if(!class_exists($_class_option)){
				$_class_option = '\\dackou\\model\\Option\\OptionModel';
			}
			$service = new $_class_option();
			$result = $service->getList($request,'parent');
			$optionKeys = [];
			if($result){
				foreach($result as $item){
					array_push($optionKeys, ['type'=>'option','label'=>$item->title."(".$item->tag.")",'value'=>$item->tag]);
				}
			}

			$_class_handle = '\\app\\model\\Handle\\HandleModel';
			if(!\class_exists($_class_handle)){
				$_class_handle = '\\dackou\\model\\Handle\\HandleModel';
			}
			$hand = [];
			if(\class_exists($_class_handle)){
				$service = new $_class_handle();
				$hand = $service->getList($request,'option');
			}

			$table = [];

			return [
				['type'=>'input','label'=>'表描述','prop'=>'title','value'=>$data->title ?? '','placeholder'=>'表描述','rules'=>['required'=>true,'message'=>'表描述不能为空']],
				['type'=>'input','label'=>'表名','prop'=>'table_name','value'=>$data->table_name ?? '','placeholder'=>'表名','rules'=>['required'=>true,'message'=>'表名不能为空']],
				['type'=>'switch','label'=>'雪花ID','prop'=>'is_snowflake','value'=>$data->is_snowflake ?? 0],
				['type'=>'switch','label'=>'分类表','prop'=>'is_cate','value'=>$data->is_cate ?? 0],
				['type'=>'input','label'=>'自增起始值','prop'=>'increment_value','value'=>$data->increment_value ?? 1,'placeholder'=>'自增起始值','rules'=>['required'=>true,'message'=>'自增起始值不能为空']],
				['type'=>'radio-group','label'=>'层级','prop'=>'layer','value'=>$data->layer ?? 1,'placeholder'=>'层级','children' => $layer],
				['type'=>'input','label'=>'主键位数','prop'=>'digit','value'=>$data->digit ?? 1,'slot'=>['suffix'=>['value'=>'位']]],
				['type'=>'radio-group','label'=>'显示方法','prop'=>'show_method','value'=>$data->show_method ?? 'page','placeholder'=>'显示方法','children'=>$show_method],
				['type'=>'switch','label'=>'分页','prop'=>'is_page','value'=>$data->is_page ?? 1],
				['type'=>'input','label'=>'每页显示','prop'=>'pagesize','value'=>$data->pagesize ?? 10,'slot'=>['suffix'=>['value'=>'行']]],
				['type'=>'checkbox-group','label'=>'分页显示','prop'=>'page_group','value'=>$data->page_group ?? ['rows','pager'],'children'=>[
					['label'=>'总记录数','value'=>'rows'],
					['label'=>'切换行数','value'=>'pager'],
					['label'=>'页面跳转','value'=>'jumper'],
				]],
				['type'=>'radio-group','label'=>'对齐方式','prop'=>'align','value'=>$data->align ?? 'center','children'=>[
					['label'=>'左对齐','value'=>'left'],
					['label'=>'居中对齐','value'=>'center'],
					['label'=>'右对齐','value'=>'right'],
				]],
				['type'=>'select','label'=>'分类表','prop'=>'cate_table','value'=>$data->cate_table ?? '','placeholder'=>'分类表','children'=>$table],
				['type'=>'input','label'=>'分类值','prop'=>'cate_value','value'=>$data->cate_value ?? '','placeholder'=>'分类值','attrs'=>['type'=>'textarea']],
				['type'=>'select','label'=>'选项key','prop'=>'option_key','value'=>$data->option_key ?? '','placeholder'=>'选项key','attrs'=>['multiple'=>true],'children'=>$optionKeys],
				// ['type'=>'switch','label'=>'状态字段','prop'=>'is_status','value'=>$data->is_status ?? 0],
				['type'=>'input','label'=>'排序','prop'=>'order_by','value'=>$data->order_by ?? '','placeholder'=>'排序','attrs'=>['type'=>'textarea']],
				['type'=>'switch','label'=>'二维码','prop'=>'is_qrcode','value'=>$data->is_qrcode ?? 0],
				['type'=>'switch','label'=>'允许真删除','prop'=>'is_delete','value'=>$data->is_delete ?? 0],
				['type'=>'switch','label'=>'允许清空','prop'=>'is_truncate','value'=>$data->is_truncate ?? 0],
				['type'=>'checkbox-group','label'=>'必备字段','prop'=>'field_group','value'=>$data->field_group ?? ['id','create','update','delete'],'children'=>[
					['label'=>'ID','value'=>'id'],
					['label'=>'编码','value'=>'code'],
					['label'=>'状态','value'=>'status'],
					['label'=>'创建','value'=>'create'],
					['label'=>'更新','value'=>'update'],
					['label'=>'删除','value'=>'delete'],
				]],
				// ['type'=>'form-group','label'=>'ID字段','delimiter'=>'-','children'=>[
				// 	['type'=>'select','label'=>'字段类型','prop'=>'id_field_type','value'=>$data_fields[0]['type']??'int','attrs'=>['style'=>['width'=>'100px']],'children'=>[
				// 		['type'=>'option','label'=>'tinyint','value'=>'tinyint'],
				// 		['type'=>'option','label'=>'smallint','value'=>'smallint'],
				// 		['type'=>'option','label'=>'mediumint','value'=>'mediumint'],
				// 		['type'=>'option','label'=>'int','value'=>'int'],
				// 		['type'=>'option','label'=>'bigint','value'=>'bigint'],
				// 	]],
				// 	['type'=>'input','label'=>'长度','prop'=>'id_length','value'=>$data_fields[0]['length'],'attrs'=>['style'=>['width'=>'100px']],'slot'=>['prefix'=>['value'=>'长度']]],
				// 	['type'=>'input','label'=>'默认值','prop'=>'id_default','value'=>$data_fields[0]['length'],'attrs'=>['style'=>['width'=>'100px']],'slot'=>['prefix'=>['value'=>'默认值']]],
				// 	['type'=>'switch','label'=>'自增','prop'=>'id_incrent','value'=>$data_fields[0]['incrent'],'attrs'=>['inline-prompt'=>true,'active-value'=>1,'inactive-value'=>0,'active-text'=>'自增','inactive-text'=>'不自增']],
				// 	['type'=>'input','label'=>'映射','prop'=>'id_map','value'=>$data_fields[0]['map'],'attrs'=>['style'=>['width'=>'100px']],'slot'=>['prefix'=>['value'=>'映射']]]
				// ]],
				// ['type'=>'form-group','label'=>'映射字段','delimiter'=>'-','children'=>[
				// 	['type'=>'input','label'=>'名称','prop'=>'map_name','value'=>$data_fields[1]['name'],'attrs'=>['style'=>['width'=>'95px']],'slot'=>['prefix'=>['value'=>'名称']]],
				// 	['type'=>'select','label'=>'类型','prop'=>'map_field_type','value'=>$data_fields[1]['type'],'attrs'=>['style'=>['width'=>'95px']],'children'=>[
				// 		['type'=>'option','label'=>'tinyint','value'=>'tinyint'],
				// 		['type'=>'option','label'=>'smallint','value'=>'smallint'],
				// 		['type'=>'option','label'=>'mediumint','value'=>'mediumint'],
				// 		['type'=>'option','label'=>'int','value'=>'int'],
				// 		['type'=>'option','label'=>'bigint','value'=>'bigint'],
				// 		['type'=>'option','label'=>'varchar','value'=>'varchar'],
				// 	]],
				// 	['type'=>'input','label'=>'长度','prop'=>'map_length','value'=>$data_fields[1]['length'],'attrs'=>['style'=>['width'=>'95px']],'slot'=>['prefix'=>['value'=>'长度']]],
				// 	['type'=>'input','label'=>'默认','prop'=>'map_default','value'=>$data_fields[1]['default'],'attrs'=>['style'=>['width'=>'95px']],'slot'=>['prefix'=>['value'=>'默认']]],
				// 	['type'=>'input','label'=>'映射','prop'=>'map_value','value'=>$data_fields[1]['map'],'attrs'=>['style'=>['width'=>'95px']],'slot'=>['prefix'=>['value'=>'映射']]]
				// ]],
				// ['type'=>'form-group','label'=>'编码字段','delimiter'=>'-','children'=>[
				// 	['type'=>'input','label'=>'名称','prop'=>'code_name','value'=>$data_fields[2]['name'],'attrs'=>['style'=>['width'=>'95px']],'slot'=>['prefix'=>['value'=>'名称']]],
				// 	['type'=>'select','label'=>'类型','prop'=>'code_field_type','value'=>$data_fields[2]['type'],'attrs'=>['style'=>['width'=>'95px']],'children'=>[
				// 		['type'=>'option','label'=>'tinyint','value'=>'tinyint'],
				// 		['type'=>'option','label'=>'smallint','value'=>'smallint'],
				// 		['type'=>'option','label'=>'mediumint','value'=>'mediumint'],
				// 		['type'=>'option','label'=>'int','value'=>'int'],
				// 		['type'=>'option','label'=>'bigint','value'=>'bigint'],
				// 		['type'=>'option','label'=>'varchar','value'=>'varchar'],
				// 	]],
				// 	['type'=>'input','label'=>'长度','prop'=>'code_length','value'=>$data_fields[2]['length'],'attrs'=>['style'=>['width'=>'95px']],'slot'=>['prefix'=>['value'=>'长度']]],
				// 	['type'=>'input','label'=>'默认','prop'=>'code_default','value'=>$data_fields[2]['default'],'attrs'=>['style'=>['width'=>'95px']],'slot'=>['prefix'=>['value'=>'默认']]],
				// 	['type'=>'input','label'=>'映射','prop'=>'code_value','value'=>$data_fields[2]['map'],'attrs'=>['style'=>['width'=>'95px']],'slot'=>['prefix'=>['value'=>'映射']]]
				// ]],
				['type'=>'form-multiple','label'=>'状态值','prop'=>'status_value','value'=>$data->status_value ?? [],'multiOptions'=>['value'=>'状态值','title'=>'状态名称','color'=>'显示颜色']],
				['type'=>'checkbox-group','label'=>'行为列表','prop'=>'handle','value'=>$data->handle ?? [1,2,3,4,6,7,13,14,15],'children'=>$hand]
			];
		}

		protected function getMapList(Request $request): array
		{
			return [
				['type'=>'int','label'=>'ID','prop'=>'id'],
				['type'=>'varchar','label'=>'表描述','prop'=>'title','width'=>100,'callback'=>'fields'],
				['type'=>'varchar','label'=>'表名','prop'=>'table_name','width'=>120,'callback'=>'fields'],
				['type'=>'varchar','label'=>'主键','prop'=>'primary_key'],
				['type'=>'switch','label'=>'雪花ID','prop'=>'is_snowflake'],
				['type'=>'switch','label'=>'类别表','prop'=>'is_cate'],
				['type'=>'varchar','label'=>'层级','prop'=>'layer','suffix'=>'层'],
				['type'=>'varchar','label'=>'ID位数','prop'=>'digit','suffix'=>'位'],
				['type'=>'varchar','label'=>'显示','prop'=>'show_method'],
				['type'=>'switch','label'=>'分页','prop'=>'is_page'],
				['type'=>'varchar','label'=>'行数','prop'=>'pagesize','suffix'=>'行'],
				['type'=>'varchar','label'=>'对齐','prop'=>'align'],
				['type'=>'varchar','label'=>'分类表','prop'=>'cate_table'],
				['type'=>'varchar','label'=>'参数','prop'=>'option_key','width'=>120],
				['type'=>'switch','label'=>'状态','prop'=>'is_status'],
				['type'=>'switch','label'=>'二维码','prop'=>'is_qrcode'],
				['type'=>'switch','label'=>'可删除','prop'=>'is_delete'],
				['type'=>'switch','label'=>'可清空','prop'=>'is_truncate'],
			];
		}
	}
?>