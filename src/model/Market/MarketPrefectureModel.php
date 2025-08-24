<?php
	namespace dackou\model\Market;

	use support\Request;
	use support\Db;

	class MarketPrefectureModel extends \dackou\Model{
		protected $table = 'MarketPrefecture';
		protected $title = '营销专区';
		

		protected function getValidList(Request $request){
			try{
				$field = ["ID as id","Title as title","Desc as desc","Pic as pic"];
				$where = [['IsDel','=',0],['Status','=',1]];
				$object = Db::table($this->table)
							->select(...$field)
							->where($where)
							->get();
				return $object ?: [];
			}catch(\Exception $e){
				return [];
			}
		}
	}
?>