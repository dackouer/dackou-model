<?php
	namespace dackou\controller\Service;

	use support\Request;
	use dackou\Json;
	use dackou\service\Query\QueryService;
	
	class Query{
		public function index(Request $request){
			$service = new QueryService();
			$result = $service->setQuery($request);

			return Json::show($result);
		}
	}
?>