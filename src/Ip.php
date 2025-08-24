<?php
	namespace dackou;

	use support\Request;
	use Gai871013\IpLocation\IpLocation;

	class Ip{
		private static $config = [];

		private static function setConfig(){
			if(!self::$config){
				self::$config = config('ip');
			}
		}

		public static function getIp(Request $request,$ip = '',$type = ''){
			try{
				self::setConfig();
				$ip = $ip ? $ip : $request->getRealIp($safe_mode=true);
				if(!$ip){
					return '';
				}
				$ipLocation = new IpLocation();
				return $ipLocation->getLocation($ip);
			}catch(IpAttributionException $e){
				return self::getErrorMessage($e);
			}
		}

		private static function getExceptionError($e){
			return [
				'code' => $e->getCode() ? $e->getCode() : 1,
				'file' => $e->getFile(),
				'line' => $e->getLine(),
				'msg'  => $e->getMessage()
			];
		}
	}
?>