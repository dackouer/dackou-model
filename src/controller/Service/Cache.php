<?php
	namespace dackou\controller\Service;

	use support\Request;
	use dackou\Json;
	use dackou\service\System\SystemService;

	class Cache{
		public function index(Request $request){
			$service = new SystemService();
			$result = $service->cache($request);

			return Json::show($result);
		}
	}
?>