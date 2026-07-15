<?php
	namespace dackou\controller\Reg;

	use support\Request;
	use dackou\Json;

	class Reg{
		public function index(Request $request){
			$_class_name = '\\app\\model\\Reg\\RegModel';
			if(!class_exists($_class_name)){
				$_class_name = '\\app\\model\\User\\RegModel';
			}
			if(!class_exists($_class_name)){
				$_class_name = '\\dackou\\model\\Reg\\RegModel';
			}
			if(!class_exists($_class_name)){
				$_class_name = '\\dackou\\model\\User\\RegModel';
			}
			$service = new $_class_name();
			$result = $service->checkReg($request);

			return Json::show($result);
		}
	}
?>