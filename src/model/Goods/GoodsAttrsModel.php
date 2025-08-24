<?php
	namespace dackou\model\Goods;

	use support\Request;

	class GoodsAttrsModel extends \dackou\Model{
		protected $table = 'GoodsAttrs';
		protected $title = '商品属性参数';
		protected $page = false;

		protected function validate(Request $request,$id = 0,$obj = null){
			$level = $request->post('level',1);
			if(empty($level) || !$level){
				$level = 1;
			}
			$pid = $request->post('pid',0);
			if(empty($pid) || !is_numeric($pid)){
				$pid = 0;
			}

			$title = trim($request->post('title',''));
			if(empty($title)){
				return '属性名称不能为空';
			}
			if($this->checkExists(['Title'=>$title,'PID'=>$pid],$id)){
				return '属性名称已存在,请重新输入';
			}

			$field_name = trim($request->post('field_name',''));
			if(empty($field_name)){
				return '字段名称不能为空';
			}
			if($this->checkExists(['FieldName'=>$field_name],$id)){
				return '字段名称已存在,请重新输入';
			}
			
			$field_type = $request->post('field_type');
			if(!$field_type || empty($field_type)){
				$field_type = 1;
			}
			$field_length = $request->post('field_length');
			if(!$field_length || empty($field_length)){
				$field_length = 0;
			}
			
			$default_value = trim($request->post('default_value',''));
			if(!$default_value || empty($default_value)){
				$default_value = $field_type === 1 ? '' : 0;
			}
			$is_required = $request->post('is_required',0);
			$sort = $request->post('sort',1);
			if(!is_numeric($sort)){
				return '排序只能填数字';
			}

			$data['title'] = $title;
			$data['field_name'] = $field_name;
			$data['field_type'] = $field_type;
			$data['field_length'] = $field_length;
			$data['level'] = $level;
			$data['pid'] = $pid;
			$data['default_value'] = $default_value;
			$data['is_required'] = $is_required;
			$data['sort'] = $sort;

			return $data;
		}

		protected function getActionList(Request $request,$id = 0): array
		{
			if($id){
				$data = $this->getList($request,$id);
			}

			$level = $request->input('level',1);
			$levelData = [];
			for($i=1;$i<=$this->layer;$i++){
				array_push($levelData,['type'=>'option','label'=>$i.'级','value'=>$i]);
			}
			$pid = $request->input('pid',0);
			$pidData = [];
			$result = $this->getList($request,'parent');
			if($result){
				foreach($result as $item){
					array_push($pidData,['type'=>'option','label'=>$item->title,'value'=>(int)$item->id]);
				}
			}

			$typeData = [
				['type'=>'option','label'=>'varchar','value'=>1],
				['type'=>'option','label'=>'int','value'=>2],
			];

			$sort = $this->getMaxSort($pid);

			$action = [
				['type'=>'input','label'=>'属性名称','prop'=>'title','value'=>$id ? $data->title : '','rules'=>['required'=>true,'message'=>'属性名称不能为空']],
				['type'=>'input','label'=>'字段名称','prop'=>'field_name','value'=>$id ? $data->field_name : '','rules'=>['required'=>true,'message'=>'字段名称不能为空']],
				['type'=>'select','label'=>'层级','prop'=>'level','value'=>$id ? $data->level : $level,'hidden'=>true,'children'=>$levelData],
				['type'=>'select','label'=>'父级','prop'=>'pid','value'=>$id ? $data->pid : $pid,'hidden'=>true,'children'=>$pidData],
				['type'=>'select','label'=>'字段类型','prop'=>'field_type','value'=>$id ? $data->field_type : 1,'children'=>$typeData,'rules'=>['required'=>true,'message'=>'请选择字段类型']],
				['type'=>'input','label'=>'字段长度','prop'=>'field_length','value'=>$id ? $data->field_length : ''],
				['type'=>'switch','label'=>'是否必填','prop'=>'is_required','value'=>$id ? $data->is_required : 0],
				['type'=>'input','label'=>'默认值','prop'=>'default_value','value'=>$id ? $data->default_value : ''],
				['type'=>'input','label'=>'排序','prop'=>'sort','value'=>$id ? $data->sort : $sort,'rules'=>['required'=>true,'message'=>'排序不能为空']],
			];

			return $action;
		}

		protected function getMapList(Request $request): array
		{
			return [
				['type'=>'id','label'=>'ID','prop'=>'id'],
				['type'=>'varchar','label'=>'属性名称','prop'=>'title'],
				['type'=>'varchar','label'=>'字段名称','prop'=>'field_name'],
				['type'=>'varchar','label'=>'字段类型','prop'=>'field_type'],
				['type'=>'varchar','label'=>'字段长度','prop'=>'field_length'],
				['type'=>'switch','label'=>'是否必填','prop'=>'is_required'],
				['type'=>'varchar','label'=>'默认值','prop'=>'default_value'],
				['type'=>'varchar','label'=>'排序','prop'=>'sort'],
			];
		}
	}
?>