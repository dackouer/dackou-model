<?php
	namespace dackou\controller\Role;

	use support\Request;
	use dackou\Json;

	class Role extends \dackou\Controller{
		protected $table = 'Role';

		public function auth(Request $request){
			$_class_name = $this->getClassName($this->table);
			$service = new $_class_name();
			$result = $service->getList($request,'auth');

			return Json::show($result);
		}

		public function commission(Request $request){
			$_class_name = $this->getClassName($this->table);
			$service = new $_class_name();
			$result = $service->getList($request,'commission');

			return Json::show($result);
		}
	}
?>