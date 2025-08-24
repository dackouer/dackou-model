<?php
	namespace dackou\service\Ip;

	use support\Request;
	use Gai871013\IpLocation\IpLocation;

	class IpService{
		public function getIp(Request $request,$flag = false){
			$ip = $request->getRealIp($safe_mode=true);
			if(!$ip){
				return '';
			}
			try{
				$ipLocation = new IpLocation();
				$result = $ipLocation->getLocation($ip);
				if(is_array($result) && isset($result['country'])){
					return !$flag ? $result : $result['country'];
				}
				return '';
			}catch(IpAttributionException $e){
				return $this->getErrorMessage($e);
			}
		}

		/**
		 * [getErrorMessage description]
		 * @param  [type] $e [description]
		 * @return [type]    [description]
		 */
		private function getErrorMessage($e){
			return [
				'code' 	=> $e->getCode() ? $e->getCode() : 1,
				'file'	=> $e->getFile(),
				'line'	=> $e->getLine(),
				'msg'	=> $e->getMessage()
			];
		}
	}
?>