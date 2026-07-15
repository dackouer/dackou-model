<?php
	namespace dackou\controller\Service;

	use support\Request;
	use dackou\Json;
	use dackou\service\EasyWechat\EasyWechatService;
	use dackou\service\Yansongda\YansongdaService;

	class Wechat{
		public function auth(Request $request,$type = 'mini'){
			$service = new EasyWechatService();
			$result = $service->auth($request,$type);

			return Json::show($result);
		}

		/**
		 * 登录授权
		 * @param  Request $request [description]
		 * @param  string  $type    [description]
		 * @return [type]           [description]
		 */
		public function login(Request $request,$type = 'mini'){
			$service = new EasyWechatService();
			$result = $service->setLogin($request,$type);

			return Json::show($result);
		}

		/**
		 * 签名
		 * @param  Request $request [description]
		 * @param  string  $type    [description]
		 * @return [type]           [description]
		 */
		public function sign(Request $request,$type = 'mini'){
			$service = new YansongdaService();
			$result = $service->sign($request,$type);

			return Json::show($result);
		}

		/**
		 * 获取手机号码
		 * @param  Request $request [description]
		 * @param  string  $type    [description]
		 * @return [type]           [description]
		 */
		public function phone(Request $request,$type = 'mini'){
			$service = new EasyWechatService();
			$result = $service->getPhoneNumber($request,$type);

			return Json::show($result);
		}
	}
?>