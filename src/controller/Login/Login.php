<?php
	namespace dackou\controller\Login;

	use support\Request;
	use dackou\Json;

	class Login{
		public function index(Request $request){
			$_class_name = '\\app\\model\\Login\\LoginModel';
			if(!class_exists($_class_name)){
				$_class_name = '\\app\\model\\User\\LoginModel';
			}
			if(!class_exists($_class_name)){
				$_class_name = '\\dackou\\model\\Login\\LoginModel';
			}
			if(!class_exists($_class_name)){
				$_class_name = '\\dackou\\model\\User\\LoginModel';
			}
			$service = new $_class_name();
			$result = $service->checkLogin($request);

			return Json::show($result);
		}
	}
?>