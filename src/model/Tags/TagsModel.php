<?php
	namespace dackou\model\Tags;

	use support\Request;
	use support\Db;

	class TagsModel extends \dackou\Model{
		protected $table = 'Tags';
		protected $title = '标签';
    	protected $show_method = 'tree';
		public $layer = 2;
		public $digit = 2;

		protected function getKeyList(Request $request,$key = '',$type = 'option'){
			if(!$key){
				return [];
			}

			$type = $type ? $type : $request->input('type','option');

			$field = $type == 'option' ? ['Title as label','ID as value'] : ['Title as title','ID as id'];
			$where = [['IsDel','=',0],['Status','=',1],['Level','=',2],['Key','=',$key]];

			$object = Db::table($this->table)
						->select(...$field)
						->where($where)
						->get();

			return $object;
		}

		protected function getValueList(Request $request,$value = ''){
			if(!$value){
				return [];
			}
			if(!is_array($value)){
				$value = $this->getDecodeData($value);
			}
			if(!$value || !is_array($value)){
				return [];
			}

			$whereIn = [];
			foreach($value as $item){
				if(is_array($item) && isset($item['id'])){
					array_push($whereIn,$item['id']);
				}elseif(is_numeric($item)){
					array_push($whereIn,$item);
				}
			}

			if(!$whereIn){
				return [];
			}

			$field = ['Title as title','ID as id'];
			$where = [['IsDel','=',0],['Status','=',1]];

			$object = Db::table($this->table)
						->select(...$field)
						->where($where)
						->whereIn('ID',$whereIn)
						->get();

			return $object;
		}

		protected function getOptionList(Request $request,mixed $param = null,array $disabled = [],array $fields = [])
    	{
    		$field = [
    			'Title as title',
    			'ID as id',
    			'Title as label',
    			'ID as value',
    			'Level as level',
    			'PID as pid',
    			'Number as number',
    		];

    		$pid = [];
    		if($param){
    			if(!is_array($param)){
    				$param = explode(',',$param);
    			}
    			$obj = Db::table($this->table)
    						->select('ID as id')
    						->where('Key',$param)
    						->where('Level',1)
    						->get();
    			if($obj){
    				foreach($obj as $item){
    					array_push($pid,$item->id);
    				}
    			}
    		}

    		$object = Db::table($this->table)
    					->select(...$field)
    					->where('IsDel',0);
    		if($pid){
    			if(count($pid) > 1){
    				$object = $object->whereIn('ID',$pid);
    			}
    			$object = $object->whereIn('PID',$pid);
    		}

    		$object = $object->orderBy('Level','asc')
    						 ->orderBy('Sort','asc')
    						 ->get();

    		if($pid){
    			if(count($pid) > 1){
    				return $this->child($object);
    			}else{
    				return $object;
    			}
    		}

    		return $this->child($object);
    	}

    	protected function validate(Request $request,$id = 0,$obj = null){
    		$title = trim($request->post('title',''));
    		$key = trim($request->post('key',''));
    		$pic = $request->post('pic','');
    		$level = $request->post('level');
			$pid = $request->post('pid');
			

    		if(!$title){
    			return '标签名称不能为空';
    		}

    		if($this->checkExists(['Title'=>$title,'PID'=>$pid],$id)){
    			return '同级标签名称已存在';
    		}

    		if($level === 1){
    			if(!$key){
    				return '标签key不能为空';
    			}
    			if($this->checkExists(['Key'=>$key,'Level'=>1],$id)){
    				return '标签key已存在';
    			}
    		}

			if(!$level || $level == ''){
				$level = 1;
			}

			if(!$pid || $pid == ''){
				$pid = 0;
			}

			$data['level'] = $level;
			$data['pid'] = $pid;

			if(!$id){
				if($pid){
					$object = $this->getList($request,$pid);
					if($object){
						$data['key'] = $object->key;
					}
				}

				$status = $request->post('status',1);
				$data['status'] = $status;
			}

			return $data;
    	}
	}
?>