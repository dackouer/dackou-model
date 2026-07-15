<?php
	namespace dackou\controller\Google;

	use dackou\service\Google\GoogleService;
	use dackou\Json;

	class Google{
		public function translate($content,$lang = ''){
			$service = new GoogleService();
			$result = $service->translate($content,$lang);

			return Json::show(is_array($result)&&isset($result['code']) ? $result : ['code' => 0,'msg' => 'success','data' => $result]);
		}
	}
?>