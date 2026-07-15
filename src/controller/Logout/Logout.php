<?php
	namespace dackou\controller\Logout;

	use support\Request;
	use dackou\Json;

	class Logout{
		public function index(Request $request){
			$session = $request->session();
			$session->forget(['uid','username','nickname','face','token']);
			$request->session()->flush();

			return Json::show(true);
		}
	}
?>