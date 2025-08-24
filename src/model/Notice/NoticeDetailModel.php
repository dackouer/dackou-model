<?php
	namespace dackou\model\Notice;

	use support\Request;
	use support\Db;

	class NoticeDetailModel extends \dackou\Model{
		protected $table = 'NoticeDetail';
		protected $title = '通知记录';

		protected function getUserList(Request $request,$user_id = 0){
			try{
				$user_id = $user_id ? $user_id : $request->input('user_id',0);
				if(!$user_id){
					return [];
				}

				$field = $this->getList($request,'field');
				$where = $this->getWhere($request);
				array_push($where,['UserID','=',$user_id]);

				$object = Db::table($this->table)
							->select(...$field)
							->where($where)
							->get();

				return $object;
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}
	}
?>