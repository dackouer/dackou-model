<?php
	namespace dackou\service\Map;

	use support\Request;

	class MapService{
		private $key = '';

		/**
		 * 获取地址的经纬度
		 * @param  Request $request [description]
		 * @param  string  $address [description]
		 * @return [type]           [description]
		 */
		public function getLocationCoordinates(Request $request,$address = ''){
			$address = $address ? urlencode($address) : urlencode(trim($request->input('address',''))); // 将地址进行 URL 编码

			$key = $this->key;
		    $url = "https://restapi.amap.com/v3/geocode/geo?address=$address&output=json&key=$key";

		    $ch = curl_init($url);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		    $response = curl_exec($ch);
		    curl_close($ch);

		    $data = json_decode($response, true);

		    if ($data['status'] == 1 && isset($data['geocodes'][0]['location'])) {
		        $location = $data['geocodes'][0]['location'];
		        list($longitude, $latitude) = explode(',', $location);
		        return array('latitude' => $latitude, 'longitude' => $longitude);
		    } else {
		        return "Error occurred: " . $data['info'];
		    }
		}

		/**
		 * 获取两个经纬度位置之间的距离
		 * @param  Request $request [description]
		 * @param  array   $data    [description]
		 * @return [type]           [description]
		 */
		public function calculateDistance(Request $request,$data = []){
			$origin = isset($data['origin']) ? $data['origin'] : $request->post('origin','');
			$destination = isset($data['destination']) ? $data['destination'] : $request->post('destination','');
			$key = $this->key;

			$url = "https://restapi.amap.com/v3/distance?origins=$origin&destination=$destination&output=json&key=$key";
    
		    $ch = curl_init($url);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		    $response = curl_exec($ch);
		    curl_close($ch);
		    
		    $data = json_decode($response, true);
		    
		    if ($data['status'] == 1) {
		        $distance = $data['results'][0]['distance'];
		        return $distance;
		    } else {
		        return "Error occurred: " . $data['info'];
		    }
		}

		private function setConfig(){
			
		}
	}
?>