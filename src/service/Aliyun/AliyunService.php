<?php
	namespace dackou\service\Aliyun;

	use support\Request;
	use support\Http;
	use dackou\model\Option\OptionModel;
	use dackou\Preg;

	class AliyunService{
		private $appkey = '';
		private $appsecret = '';
		private $appcode = '';
		private $prefix = '';
		private $key = 'smscode';
		private $is_redis = true;

		public function __construct(){
			if(!$this->prefix){
				$config = config('database') ?? [];
	    		if(isset($config['connections']['mysql']['prefix'])){
	    			$this->prefix = $config['connections']['mysql']['prefix'];
	    		}
			}
		}

		/**
		 * [sendSms description]
		 * @param  Request $request [description]
		 * @param  array   $data    [description]
		 * @return [type]           [description]
		 */
		public function sendSms(Request $request,$data = []){
			$mobile = isset($data['mobile']) ? $data['mobile'] : trim($request->post('mobile',''));
			if(empty($mobile)){
				return 100120;
			}
			if(!Preg::IsMobile($mobile)){
				return 100121;
			}

			$action = isset($data['action']) ? $data['action'] : trim($request->post('action',$request->post('mode','register')));
			if(!in_array($action,['register','login','auth','modpwd','information','notification','orderinfo'])){
				return '无效的短信模板';
			}

			if($action == 'orderinfo'){
				$params = [
					'order'   => $data['order'],
					'name'    => $data['name'],
					'title'   => $data['title'],
					'count'   => $data['count'],
					'amount'  => $data['amount'],
					'address' => $data['address']
				];
			}elseif($action == 'notification'){
				$day = isset($data['day']) ? $data['day'] : $request->post('day',60);
				$content = isset($data['content']) ? $data['content'] : $request->post('content','');
				if(!$day){
					return '无效的欠费天数';
				}
				if(!$content){
					return '短信内容不能为空';
				}
				$params = [
					'day'	  => $day,
					'content' => $content
				];
			}else{
				$length = isset($data['length']) ? $data['length'] : $request->post('length',6);	
				$type = isset($data['type']) ? $data['type'] : $request->post('type',1);	
				$code = isset($data['code']) ? $data['code'] : $this->createCode($length,$type);
				$params = ['code' => $code];							// 参数
			}
	        $areacode = "86";										// 国际区号,腾讯云选传,其他不传
	        $sms = new \Hzdad\Wbsms\Wbsms('aliyun');				// 传入短信服务商名称, 腾讯云 qcloud , 阿里云 aliyun, 七牛 qiniu, 华为 huawei
	        $result = $sms->sendsms($action,$mobile,$params,$areacode);
	        // var_dump('send sms result: ',$result);
	        if($result && isset($result['code']) && $result['code'] == 200){
	        	if(!in_array($action,['orderinfo','notification','information'])){
	        		$this->setSession($request,$code);
	        	}
	            return ['code' => 0,'msg' => 'success'];
	        }else{
	            return ['code' => $result['code'],'msg' => $result['msg']];
	        }
		}

		/**
		 * 检验身份认证二要素
		 * @param  Request $request [description]
		 * @param  array   $data    [description]
		 * @return [type]           [description]
		 */
		public function checkIdcardByTwoElement(Request $request,$data = []){
			$realname = isset($data['realname']) ? trim($data['realname']) : trim($request->post('realname',''));
			if(!$realname){
				return '真实姓名不能空';
			}
			$idcard = isset($data['idcard']) ? trim($data['idcard']) : trim($request->post('idcard',''));
			if(!$idcard){
				return '身份证号不能为空';
			}
			if(!Preg::isIdcard($idcard)){
				return '身份证号填写不正确';
			}

			$config = $this->getConfig($request,'idcard_two');
			if(!$config || !$config['idcard_two_appkey'] || !$config['idcard_two_app_secret'] || !$config['idcard_two_app_code']){
				return '参数配置有误';
			}

			try{
				$host = "https://eid.shumaidata.com";
			    $path = "/eid/check";
			    $param = ['idcard' => $idcard,'name' => $realname];
				$headers = ['Authorization' => "APPCODE ".$config['idcard_two_app_code']];

				$http = new Http($host);
				$result = $http->setHeader($headers)
						    ->post($path,$param);
				return $result;
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		/**
		 * 快递地址解析
		 * @param  Request $request [description]
		 * @param  array   $data    [description]
		 * @return [type]           [description]
		 */
		public function textParse(Request $request,$data = []){
			$text = isset($data['text']) ? trim($data['text']) : trim($request->post('text',''));
			if(!$text){
				return '文本内容不能为空';
			}

			$config = $this->getConfig($request,'aliyun');
			if(!$config || !$config['aliyun_app_code']){
				return '参数配置有误';
			}

			try{
				$host = "https://foparse01.market.alicloudapi.com";
    			$path = "/s/api/ocr/iofoParse";
			    $param = ['text' => $text];
				$headers = [
					"Authorization" => "APPCODE ".$config['idcard_two_app_code'],
					"Content-Type"  => "application/x-www-form-urlencoded; charset=UTF-8"
				];
				$http = new Http($host);
				$result = $http->setHeader($headers)
						    ->post($path,$param);
				return $result;
    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
		}

		/**
		 * [setSession description]
		 * @param Request $request [description]
		 * @param [type]  $code    [description]
		 * @param string  $token   [description]
		 */
		private function setSession(Request $request,$code){
			$key = $this->prefix . $this->key;
			$request->session()->set($key,$code);
			// var_dump('set sms session: '.$request->session()->get($key));
		}

		/**
		 * [createCode description]
		 * @param  integer $length [description]
		 * @param  integer $type   [description]
		 * @return [type]          [description]
		 */
		private function createCode($length = 6,$type = 1){
			$num = [0,1,2,3,4,5,6,7,8,9];
			$letter = ['a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z'];
			switch(strtolower($type)){
				case '1':
					$arr = $num;
					break;
				case '2':
					$arr = $letter;
					break;
				case '3':
					$arr = array_merge($num,$letter);
					break;
				default:
					$arr = $num;
			}

			shuffle($arr);

			$temp = $arr;
			array_splice($temp,array_search(0, $arr),1);

			$code = $temp[mt_rand(0,count($temp)-1)];
			for($i=1;$i<$length;$i++){
				$code .= $arr[mt_rand(0,count($arr)-1)];
			}

			return $code;
		}

		private function getConfig(Request $request,$key = ''){
			$service = new OptionModel();
			return $service->getList($request,'key',$key);
		}

		private function getExceptionError($e){
			return [
				'code' => $e->getCode() ? $e->getCode() : 1,
				'msg'  => $e->getMessage()
			];
		}
	}
?>