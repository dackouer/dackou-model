<?php
	namespace dackou\controller\Service;

	use support\Request;
	use dackou\Json;
	use dackou\service\Token\TokenService;
	use dackou\model\User\UserModel;

	class Token{
		public function index(Request $request){
			try{
				$authorization = trim($request->header('Authorization',$request->input('apikey','')));
			    $apikey = trim($request->input('apikey',''));
			    $appsecret = trim($request->input('appsecret',''));

			    if(!$authorization){
			    	return $this->showResult(100011);
			    }

			    $res = TokenService::decodeToken($authorization);
			    // var_dump('收到token数据：');
			    // var_dump($res);
			    // var_dump('当前时间：',date('Y-m-d H:i:s',time() - 600));
			    if(!$res || !is_object($res)){
			    	return $this->showResult(100010);
			    }
			    if(!property_exists($res,'uid') || !property_exists($res,'token') || !property_exists($res,'rid') || !property_exists($res,'sign') || !property_exists($res,'expire')){
    			    return $this->showResult(100007);		// 无效token
    			}

    			if(strtotime($res->expire) <= (time() - 600)){
    				// var_dump('已过期');
    				$uid = $request->input('uid',0);
    				if($uid && $uid != $res->uid){
    				    return $this->showResult(100007);
    				}
    				
    				$service = new UserModel();
    				$user = $service->getList($request,$res->uid);
    				// var_dump('token user:');
    				// var_dump($user);
    				if(!$user || !is_object($user) || !property_exists($user,'uid')){
    				    return $this->showResult(100007);
    				}
    				$result = TokenService::generateToken($request,$user);		// token过期
    				return $this->showResult($result);
    			}

    			return $this->showResult(['token' => $authorization,'expire_time' => $res->expire]);

    			/*
    			$uid = $request->input('uid',0);
    			if($uid && $uid != $res->uid){
    			    return $this->showResult(100007);
    			}
    			
    			$service = new UserModel();
    			$user = $service->getList($request,$res->uid);
    			var_dump('token user:');
    			var_dump($user);
    			if(!$user || !is_object($user) || !property_exists($user,'uid')){
    			    return $this->showResult(100007);
    			}
    			if($res->expire <= time()){
    				$result = TokenService::generateToken($request,$user);		// token过期
    				return $this->showResult($result);
    			}else{
    			    return $this->showResult(['token' => $authorization,'expire_time' => $res->expire]);
    			}*/
			}catch(\Exception $e){
				return 100015;
				// return $this->getExceptionError($e);
			}
		}

		private function showResult($code){
			return Json::show($code);
		}

		private function getExceptionError($e){
			return [
				'code' => $e->getCode() ? $e->getCode() : 1,
				'file' => $e->getFile(),
				'line' => $e->getLine(),
				'msg'  => $e->getMessage()
			];
		}
	}
?>