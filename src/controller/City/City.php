<?php
	namespace dackou\controller\City;

	use support\Request;
	use dackou\Json;

	class City extends \dackou\Controller{
		protected $table = 'City';

		public function index(Request $request,$id = 0){
			$_class_name = $this->getClassName('City');
			$service = new $_class_name();
			if(!$id){
				$result = $service->getList($request,'province');
			}else{
				$result = $service->getList($request,$id);
			}

			return Json::show($result);
		}

		/**
		 * 获取省份列表
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function province(Request $request){
			$_class_name = $this->getClassName('City');
			$service = new $_class_name();
			$result = $service->getList($request,'province');

			return Json::show($result);
		}

		/**
		 * 获取某个省份的城市列表
		 * @param  Request $request [description]
		 * @param  integer $pid     [description]
		 * @return [type]           [description]
		 */
		public function city(Request $request,$pid = 0){
			$pid = $pid ?: $request->input('pid',$request->input('province_id',0));
			$_class_name = $this->getClassName('City');
			$service = new $_class_name();
			$result = $service->getList($request,'city',$pid);

			return Json::show($result);
		}

		/**
		 * 获取某个城市的区县列表
		 * @param  Request $request [description]
		 * @param  integer $pid     [description]
		 * @return [type]           [description]
		 */
		public function district(Request $request,$pid = 0){
			$pid = $pid ?: $request->input('pid',$request->input('city_id',0));
			$_class_name = $this->getClassName('City');
			$service = new $_class_name();
			$result = $service->getList($request,'district',$pid);

			return Json::show($result);
		}

		/**
		 * 获取某个区县的街道列表
		 * @param  Request $request [description]
		 * @param  integer $pid     [description]
		 * @return [type]           [description]
		 */
		public function street(Request $request,$pid = 0){
			$pid = $pid ?: $request->input('pid',$request->input('district_id',0));
			$_class_name = $this->getClassName('City');
			$service = new $_class_name();
			$result = $service->getList($request,'street',$pid);

			return Json::show($result);
		}

		public function api(Request $request){
			$_class_name = $this->getClassName('City');
			$service = new $_class_name();
			$result = $service->getList($request,'api');

			return Json::show($result);
		}

		public function cityname(Request $request){
			$_class_name = $this->getClassName('City');
			$service = new $_class_name();
			$result = $service->getList($request,'cityname');

			return Json::show($result);
		}
	}
?>