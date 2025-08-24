<?php
	namespace dackou\service\Sms;

	use support\Request;
	use dackou\Preg;

	class SmsService{
		private $default = 'aliyun';

		/**
		 * 统一发送入口
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function send(Request $request,$data = []){	
			$mode = isset($data['mode']) ? $data['mode'] : trim($request->post('mode',$this->default));
			// var_dump('mode: '.$mode);
			if(in_array($mode,['register','login'])){
				$result = self::sendAliyun($request,$data);
			}else{
				switch(strtolower($mode)){
					case 'aliyun':
						$result = self::sendAliyun($request,$data);
						break;
					case 'qcloud':
					case 'tencent':
						$result = self::sendTencent($request,$data);
						break;
					case 'qiniu':
						$result = self::sendQiniu($request,$data);
						break;
					case 'huawei':
						$result = self::sendHuawei($request,$data);
						break;
					default:
						$result = 100000;
				}
			}

			return $result;
		}

		/**
		 * 通过阿里云发送短信
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		private static function sendAliyun(Request $request,$data = []){
			$service = new \dackou\service\Aliyun\AliyunService();
			return $service->sendSms($request,$data);
		}

		/**
		 * 通过腾讯云发送短信
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		private static function sendTencent(Request $request,$data = []){
			$service = new \dackou\service\Tencent\TencentService();
			return $service->sendSms($request,$data);
		}

		/**
		 * 通过七牛云发送短信
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		private static function sendQiniu(Request $request,$data = []){
			$service = new \dackou\service\Qiniu\QiniuService();
			return $service->sendSms($request,$data);
		}

		/**
		 * 通过华为云发送短信
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		private static function sendHuawei(Request $request,$data = []){
			$service = new \dackou\service\Huawei\HuaweiService();
			return $service->sendSms($request,$data);
		}
	}
?>