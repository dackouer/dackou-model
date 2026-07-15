<?php
	namespace dackou\controller\Application;

	use support\Request;
	use dackou\Json;

	class Application{
		public function index(Request $request){
			$_class_name = $this->getClassName();
			$service = new $_class_name();
			$result = $service->getList($request);

			return Json::show($result);
		}

		public function home(Request $request){
			$_class_name = $this->getClassName();
			$service = new $_class_name();
			$result = $service->getList($request,'home');

			return Json::show($result);
		}

		public function login(Request $request){
			$_class_name = $this->getClassName();
			$service = new $_class_name();
			$result = $service->getList($request,'login');

			return Json::show($result);
		}

		public function board(Request $request){
			$_class_name = $this->getClassName();
			$service = new $_class_name();
			$result = $service->getList($request,'board');

			return Json::show($result);
		}

		public function admin(Request $request){
			$_class_name = $this->getClassName();
			$service = new $_class_name();
			$result = $service->getList($request,'admin');

			return Json::show($result);
		}

		public function account(Request $request){
			$_class_name = $this->getClassName();
			$service = new $_class_name();
			$result = $service->getList($request,'account');

			return Json::show($result);
		}

		public function mini(Request $request){
			$_class_name = $this->getClassName();
			$service = new $_class_name();
			$result = $service->getList($request,'mini');

			return Json::show($result);
		}

		public function app(Request $request){
			$_class_name = $this->getClassName();
			$service = new $_class_name();
			$result = $service->getList($request,'app');

			return Json::show($result);
		}

		private function getClassName(){
			$_class = '\app\model\Application\ApplicationModel';
			if(class_exists($_class)){
				return $_class;
			}
			return '\dackou\model\Application\ApplicationModel';
		}
	}
?>