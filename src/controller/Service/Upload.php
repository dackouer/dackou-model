<?php
	namespace dackou\controller\Service;

	use support\Request;
	use dackou\Json;
	use dackou\service\Upload\UploadService;

	class Upload{
		public function index(Request $request){
			$service = new UploadService();
			$result = $service->upload($request);

			return Json::show($result);
		}

		public function local(Request $request){
			$service = new UploadService();
			$result = $service->upload($request,'local');

			return Json::show($result);
		}

		public function cert(Request $request){
			$service = new UploadService();
			$result = $service->upload($request,'cert');

			return Json::show($result);
		}

		public function wang(Request $request){
			$service = new UploadService();
			$result = $service->upload($request,'editor');

			return json($result);
		}

		public function umo(Request $request){
			$service = new UploadService();
			$result = $service->upload($request,'umo');

			return json($result);
		}

		public function uniapp(Request $request){
			$service = new UploadService();
			$result = $service->upload($request,'uniapp');

			return json($result);
		}
	}
?>