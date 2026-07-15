<?php
	namespace dackou\model\Express;

	class ExpressModel extends \dackou\Model{
		protected $table = 'Express';
		protected $title = '快递物流';
		protected $cate_value = [1=>'快递',2=>'物流',0=>'其它'];
	}
?>