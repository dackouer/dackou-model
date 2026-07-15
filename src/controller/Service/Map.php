<?php
	namespace dackou\controller\Service;

	use support\Request;
	use dackou\Json;
	use dackou\Service as MapService;

	class Map{
		public function latitude(Request $request,$address = ''){
			$address = $address ? $address : $request->post('address','');
			if(!$address || !is_string($address)){
				return Json::show(['code'=>1,'msg'=>'无效的地址']);
			} 

			$lati = MapService::getLatitude($address);
			if(is_array($lati) && isset($lati['latitude']) && isset($lati['longitude'])){
			    $data['latitude'] = $lati['latitude'];
			    $data['longitude'] = $lati['longitude'];

			    return Json::show($data);
			}

			return Json::show($lati);
		}

		public function distance(Request $request,$origin = '', $destination = ''){
			$origin = $origin ? $origin : $request->post('origin','');
			$destination = $destination ? $destination : $request->post('destination','');
			if(!$origin || !is_string($origin)){
				return Json::show(['code'=>1,'msg'=>'无效的起始经纬度']);
			} 
			if(!$destination || !is_string($destination)){
				return Json::show(['code'=>1,'msg'=>'无效的结束经纬度']);
			} 

			$dis = MapService::getRouteDistance($origin, $destination);
            if(is_array($dis) && isset($dis['distance']) && isset($dis['duration'])){
                $data['distance'] = $dis['distance'];
                $data['duration'] = $dis['duration'];

                return Json::show($data);
            }

			return Json::show($dis);
		}
	}
?>