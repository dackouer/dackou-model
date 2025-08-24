<?php
	namespace dackou\service\Weather;

	use support\Request;
	use Gai871013\IpLocation\IpLocation;

	class WeatherService{
		public function getWeather(Request $request){
			$ip = $request->getRealIp($safe_mode = true);
			$ipLocation = new IpLocation();
			$result = $ipLocation->getLocation($ip);
			$data = [];
			if(isset($result['country'])){
				$temp = explode('省',$result['country']);
				$province = isset($temp[0]) ? $temp[0].'省' : '';
				$city = isset($temp[1]) ? $temp[1] : '';
				$data = ['province' => $province,'city'	=> $city];

				if($city){
					$city = trim($city,'市');
					$response = \tinywan\Weather::forecastsWeather($city);
					// var_dump($response);
					if($response){
						if($response['status'] == '1' && $response['info'] == 'OK'){
							$item = $response['forecasts'][0]['casts'];
							$date = [];
							for($i=0;$i<count($item);$i++){
								if($item[$i]['date'] == date('Y-m-d')){
									$date = $item[$i];
									break;
								}
							}
							$data['date'] = $date;
						}
					}
				}
			}

			return $data;
		}
	}
?>