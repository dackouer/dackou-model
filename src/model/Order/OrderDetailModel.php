<?php
	namespace dackou\model\Order;

	class OrderDetailModel extends \dackou\Model{
		protected $table = 'OrderDetail';
		protected $title = '订单详情';

		public function updateData(Request $request,array $arr = [],mixed $id = 0): mixed
		{

		}
	}
?>