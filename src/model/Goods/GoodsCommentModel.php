<?php
	namespace dackou\model\Goods;

	use support\Request;
	use support\Db;

	class GoodsCommentModel extends \dackou\Model{
		protected $table = 'GoodsComment';

		protected function getGoodsList(Request $request,$goods_id = 0){
			$goods_id = $goods_id ? $goods_id : $request->input('goods_id',0);

			if(!$goods_id){
				return [];
			}

			try{
				$field = $this->getTableFields();
				$where = $this->getWhere($request,$field);
				array_push($where,['GoodsID','=',$goods_id]);

				$rows = Db::table($this->table)->where($where)->count();
				$limit = $this->getLimit($request);

				$object = Db::table($this->table)
							->select(...$field)
							->orderBy('ID','desc')
							->where($where)
							->offset($limit[0])
							->limit($limit[1])
							->get();
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}
	}
?>