<?php
	namespace dackou\controller\Service;

	use support\Request;
	use dackou\Json;
	use dackou\service\Download\DownloadService;

	class Download{
		public function index(Request $request){
			$service = new DownloadService();
			$result = $service->down($request);
			if(is_array($result) && isset($result['code'])){
				return Json::show($result);
			}
			return $result;
		}

		public function down(Request $request){
			$service = new DownloadService();
			$result = $service->down($request);
			if(is_array($result) && isset($result['code'])){
				return Json::show($result);
			}
			return $result;
		}
	}
?>