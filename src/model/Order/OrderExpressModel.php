<?php
	namespace dackou\model\Order;

	use support\Request;
	use support\Db;

	class OrderExpressModel extends \dackou\Model{
		protected $table = 'OrderExpress';
		protected $title = '订单发货';

		public function appendData(Request $request,$data)
		{
			try{
				var_dump('订单发货回调：',$data);
			}catch(\Exception $e){
				var_dump($this->getExceptionError($e));
				return false;
			}

		}
	}
?>