<?php
	namespace dackou\controller\Moments;

	use support\Request;
	use dackou\Json;

	class Moments extends \dackou\Controller{
		protected $table = 'Moments';

		public function home(Request $request){
			$_class_name = $this->getClassName($this->table);
			if($_class_name){
				$service = new $_class_name();
				$result = $service->getList($request,'home');

				return Json::show($result);
			}

			return Json::show([]);
		}
	}
?>