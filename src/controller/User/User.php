<?php
	namespace dackou\controller\User;

	use support\Request;
	use dackou\Json;

	class User extends \dackou\Controller{
		protected $table = 'User';

		public function auth(Request $request){
			$_class_name = $this->getClassName('Auth');
			if(!$_class_name){
				$_class_name = '\app\model\User\AuthModel';
				if(!class_exists($_class_name)){
					$_class_name = '\dackou\model\User\AuthModel';
				}
			}
			
			$service = new $_class_name();
			$result = $service->checkAuth($request);
			
			return Json::show($result);
		}
		
		public function nearby(Request $request){
			$_class_name = $this->getClassName('User');
			$service = new $_class_name();
			$result = $service->getList($request,'nearby');
			
			return Json::show($result);
		}

		public function verify(Request $request,$uid = 0){
			$_class_name = $this->getClassName($this->table);
			$service = new $_class_name();
			$result = $service->checkUser($request,$uid);

			return Json::show($result);
		}
	}
?>