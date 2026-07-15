<?php
	namespace dackou\controller\Service;

	use support\Request;
	use dackou\Json;
	use dackou\service\Sms\SmsService;

	class Sms{
		public function index(Request $request){
			$service = new SmsService();
			$result = $service->send($request);

			return Json::show($result);
		}

		public function send(Request $request){
			$service = new SmsService();
			$result = $service->send($request);

			return Json::show($result);
		}
	}
?>