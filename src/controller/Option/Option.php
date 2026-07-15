<?php
	namespace dackou\controller\Option;

	use support\Request;
	use dackou\Json;

	class Option extends \dackou\Controller{
		protected $table = 'Option';

		public function key(Request $request,$key = ''){
			$_class_name = $this->getClassName('Option');
			$service = new $_class_name();
			$result = $service->getList($request,'keys',$key);

			$service = new \dackou\service\Google\GoogleService();
			$lang = $service->translate('i am chinese');

			return Json::show($result);
		}

		public function field(Request $request,$field = ''){
			$field = $field ? $field : $request->input('field','');
			$_class_name = $this->getClassName('Option');
			$service = new $_class_name();
			$result = $service->getList($request,'fieldname',$field);

			return Json::show($result);
		}
	}
?>