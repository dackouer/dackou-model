<?php
	namespace dackou\model\Log;

	use support\Request;
	use support\Db;

	class LogModel extends \dackou\Model{
		protected $table = 'Log';
		protected $title = '接口日志';
		protected $cate_value = [
			1 => '用户',
			2 => '注册',
			3 => '登录',
		];

		protected function getPageList(Request $request){
			try{
				$field = $this->getList($request,'field',true);
				$where = $this->getWhere($request);
				$cate_id = $request->input('cate_id',0);
				if(!$cate_id || !$this->checkCateID($cate_id)){
					$cate_id = 1;
				}
				if($cate_id == 2){
					$this->title = '注册日志';
				}
				if($cate_id == 3){
					$this->title = '登录日志';
				}
				array_push($where,[$this->table.'.CateID','=',$cate_id]);

				$rows = Db::table($this->table)
							->where($where)
							->count();
				[$offset,$limit] = $this->getLimit($request);
				$object = Db::table($this->table)
							->select(...$field)
							->where($where)
							->orderBy($this->table.'.ID','desc')
							->offset($offset)
							->limit($limit)
							->get();
				if($object){
					foreach($object as $k => $v){
						$object[$k]->create_time = $this->getDateTime($object[$k]->create_time);
					}
				}

				return ['rows' => $rows,'data' => $object];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		protected function getCateList(Request $request,$cate_id = 0){
			try{
				if(!$cate_id || !$this->checkCateID($cate_id)){
					return 100005;
				}
				$field = $this->getList($request,'field',true);
				$where = $this->getWhere($request);
				array_push($where,[$this->table.'.CateID','=',$cate_id]);

				$rows = Db::table($this->table)
							->where($where)
							->count();
				[$offset,$limit] = $this->getLimit($request);
				$object = Db::table($this->table)
							->select(...$field)
							->where($where)
							->orderBy($this->table.'.ID','desc')
							->offset($offset)
							->limit($limit)
							->get();
				if($object){
					foreach($object as $k => $v){
						$object[$k]->create_time = $this->getDateTime($object[$k]->create_time);
					}
				}

				return ['rows' => $rows,'data' => $object];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		/**
		 * 获取注册日志
		 * @param  Request $request [description]
		 * @param  integer $user_id [description]
		 * @return [type]           [description]
		 */
		protected function getRegList(Request $request){
			try{
				$field = $this->getList($request,'field',true);
				$where = $this->getWhere($request);
				array_push($where,[$this->table.'.CateID','=',2]);

				$rows = Db::table($this->table)
							->where($where)
							->count();
				[$offset,$limit] = $this->getLimit($request);
				$object = Db::table($this->table)
							->select(...$field)
							->where($where)
							->orderBy($this->table.'.ID','desc')
							->offset($offset)
							->limit($limit)
							->get();
				if($object){
					foreach($object as $k => $v){
						$object[$k]->create_time = $this->getDateTime($object[$k]->create_time);
					}
				}

				return ['rows' => $rows,'data' => $object];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		/**
		 * 获取登录日志
		 * @param  Request $request [description]
		 * @param  integer $user_id [description]
		 * @return [type]           [description]
		 */
		protected function getLoginList(Request $request){
			try{
				$field = $this->getList($request,'field',true);
				$where = $this->getWhere($request);
				array_push($where,[$this->table.'.CateID','=',3]);

				$rows = Db::table($this->table)
							->where($where)
							->count();
				[$offset,$limit] = $this->getLimit($request);
				$object = Db::table($this->table)
							->select(...$field)
							->where($where)
							->orderBy($this->table.'.ID','desc')
							->offset($offset)
							->limit($limit)
							->get();
				if($object){
					foreach($object as $k => $v){
						$object[$k]->create_time = $this->getDateTime($object[$k]->create_time);
					}
				}

				return ['rows' => $rows,'data' => $object];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		private function checkCateID($cate_id){
			$vals = array_values($this->cate_value);
			if(in_array($cate_id,$vals)){
				return true;
			}
			return false;
		}
	}
?>