<?php
	namespace dackou\controller\Service;

	use support\Request;
	use dackou\Json;
	use dackou\service\Captcha\CaptchaService;

	class Captcha{
		public function index(Request $request){
			$service = new CaptchaService();
			$img = $service->createCaptcha($request);

			$type = trim($request->input('type','base64'));
			if($type === 'base64'){
				$base64Img = "data:image/png;base64,".base64_encode($img);

				$data = ['code' => 0,'msg' => 'success','data' => $base64Img];
				// var_dump('data: ',$data);
				$userAgent = $request->header('User-Agent');
				if($userAgent === 'apifox'){
					$number = $service->getCode($request);
					$data = ['code' => 0,'msg' => 'success','number' => $number,'data' => $base64Img];
				}
				return Json::show($data);
			}
			return response($img,200,['Content-Type' => 'image/jpeg']);
		}

		public function check(Request $request){
			$service = new CaptchaService();
			$result = $service->check($request);

			return Json::show($result);
		}
	}
?>