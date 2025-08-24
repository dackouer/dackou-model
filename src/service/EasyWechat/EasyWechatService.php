<?php
	namespace dackou\service\EasyWechat;

	use support\Request;
	use dackou\model\Option\OptionModel;
	use EasyWeChat\Factory;
	use EasyWeChat\MiniApp\Application;

	class EasyWechatService{
		private $config = [];
		private $cate_value = ['sign','pay'];
		private $type_value = ['wechat','mini','app','pc'];
		private $wxurl = 'https://api.weixin.qq.com';

		public function setLogin(Request $request,$type = 'mini'){
			$type = $type ? $type : $request->input('type','mini');

			$code = trim($request->post('code',''));
			if(!$code){
				return 100007;
			}

			try{
				$this->config = $this->getConfig($request,$type);
				
				$app = new Application($this->config);
				$utils = $app->getUtils();
				$res = $utils->codeToSession($code);
				var_dump('code to session:');
				var_dump($res);

				return $res;
			}catch(\Exception $e){
				return $this->getErrorMessage($e);
			}

		}

		public function setAuth(Request $request,$data = []){
			$code = isset($data['code']) ? $data['code'] : $request->post('code','');
			if(!$code){
				return 100007;
			}
			$type = $request->input('type','mini');
			$this->config = $this->getConfig($request,$type);

			try{
				$app = new Application($this->config);
				$utils = $app->getUtils();
				$res = $utils->codeToSession($code);
				
				return $res;
			}catch(\Exception $e){
				return $this->getErrorMessage($e);
			}
		}

		/**
		 * 统一用户授权
		 * @param  Request $request [description]
		 * @param  string  $type    [description]
		 * @return [type]           [description]
		 */
		public function auth(Request $request,$type = 'mini'){
			var_dump('接收到的授权数据：');
			var_dump($request->post());

			$code = trim($request->post('code',''));
			if(!$code){
				return 100007;
			}
			$encrypted_data = trim($request->post('encrypted_data',$request->post('encryptedData','')));
			if(!$encrypted_data){
				return 100007;
			}
			$iv = trim($request->post('iv',''));
			if(!$iv){
				return 100007;
			}

			$this->config = $this->getConfig($request,$type);

			try{
				$app = new Application($this->config);
				$utils = $app->getUtils();
				$res = $utils->codeToSession($code);
				var_dump('code to session:');
				var_dump($res);
				// $res = [
				// 	"session_key"	=> "UAa4gLoDmKQf/OKRFtlZdQ=="
  				// 	"openid"		=> "otu5F4wVTLcfCmqBNxLpjHn6islg"
  				// ];
  				if($res && isset($res['session_key']) && isset($res['openid'])){
  					$session = $utils->decryptSession($res['session_key'], $iv, $encrypted_data);
  					
  					if($session && isset($session['nickName'])){
  						$session['type'] = $type;
  						$session['openId'] = $res['openid'];
  						$user = $this->authUser($request,$session);
  						var_dump('user success: ');
  						var_dump($user);
  						if($user && is_array($user)){
  							return $user;	// 成功
  						}
  						return '用户更新失败';
  					}else{
  						return '解密失败';
  					}
  				}else{
  					return 'session获取失败';
  				}

  				return false;

			}catch(\Exception $e){
				// var_dump($e->getMessage());
				return $this->getErrorMessage($e);
			}
		}

		/**
		 * [getPhoneNumber description]
		 * @param  Request $request [description]
		 * @param  string  $type    [description]
		 * @return [type]           [description]
		 */
		public function getPhoneNumber(Request $request,$type = 'mini'){
			$login_code = trim($request->post('login_code',''));
			$code = trim($request->post('code',''));
			if(!$login_code || !$code){
				return 100007;
			}

			$session = $this->setAuth($request,['code' => $login_code]);
			var_dump('get auth session result:');
			var_dump($session);
			if($session && isset($session['openid'])){
				var_dump('openid: '.$session['openid']);
				$this->config = $this->getConfig($request,$type);

				$data = json_encode(['code' => $code]);

				try{
					$app = new Application($this->config);
					$access = $app->getAccessToken();
					$accessToken = $access->getToken();
					$url = 'https://api.weixin.qq.com/wxa/business/getuserphonenumber?access_token='.$accessToken;

					$curl = new \Curl\Curl();
					$curl->post($url,$data);
					$curl->close();
					$result = json_decode($curl->response,true);
					var_dump('easy wechat get phone number result:');
					var_dump($result);
					if($result['errcode'] === 0 && isset($result['phone_info'])){
						$result['phone_info']['openid'] = $session['openid'];

						return $result['phone_info'];
					}
					return ['code' => $result['errcode'],'msg' => $result['errmsg']];
					// return ($result['errcode'] === 0 && isset($result['phone_info'])) ? $result['phone_info'] : ['code' => $result['errcode'],'msg' => $result['errmsg']];
				}catch(\Exception $e){
					return $this->getErrorMessage($e);
				}
			}else{
				return '授权失败';
			}

			
		}

		public function getAuthData(Request $request,$type = 'mini'){
			$code = trim($request->post('code',''));
			if(!$code){
				return false;
			}
			$encrypted_data = trim($request->post('encrypted_data',''));
			if(!$encrypted_data){
				return false;
			}
			$iv = trim($request->post('iv',''));
			if(!$iv){
				return false;
			}

			$this->config = $this->getConfig($request,$type);

			try{
				$app = new Application($this->config);
				$utils = $app->getUtils();
				$res = $utils->codeToSession($code);
				// var_dump($res);
				// $res = [
				// 	"session_key"	=> "UAa4gLoDmKQf/OKRFtlZdQ=="
  				// 	"openid"		=> "otu5F4wVTLcfCmqBNxLpjHn6islg"
  				// ];
  				if($res && isset($res['session_key']) && isset($res['openid'])){
  					$session = $utils->decryptSession($res['session_key'], $iv, $encrypted_data);
  					// var_dump($session);
  					if($session && isset($session['nickName'])){
  						$session['type'] = $type;
  						$session['openId'] = $res['openid'];
  						return $session;
  					}else{
  						return false;
  					}
  				}else{
  					return false;
  				}

  				return false;

			}catch(\Exception $e){
				// var_dump($e->getMessage());
				return false;
			}
		}

		/**
		 * 签名
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function sign(Request $request,$type = 'mini'){
			$config = [
			    // 前面的appid什么的也得保留哦
			    'app_id'             => 'wxfd8e23d4dad07f15',
			    'mch_id'             => '1618670072',
			    'key'                => 'weiteng',
			    'cert_path'          => config_path() . '/cert/wechat/apiclient_cert.pem', // XXX: 绝对路径！！！！
			    'key_path'           => config_path() . '/cert/wechat/apiclient_key.pem',      // XXX: 绝对路径！！！！
			    'notify_url'         => 'https://api.gulanfood.com/pay/notify',     // 你也可以在下单时单独设置来想覆盖它
			    // 'device_info'     => '013467007045764',
			    // 'sub_app_id'      => '',
			    // 'sub_merchant_id' => '',
			    // ...
			];

			$payment = Factory::payment($config);
			var_dump($payment);
			// $prepayId = $payment['prepayId'];
			// $config = $jssdk->sdkConfig($prepayId); 
			// var_dump($config);
			// return $config;
		}

		/**
		 * 统一支付
		 * @param  Request $request [description]
		 * @param  string  $type    [description]
		 * @return [type]           [description]
		 */
		public function pay(Request $request,$type = 'mini'){
			$this->config = $this->getConfig($request,$type);
			// var_dump($config);
			
			try{
				$app = new Application($this->config);

				$response = $app->getClient()->postJson("v3/pay/transactions/jsapi", [
				   "mchid" => '1618670072', // <---- 请修改为您的商户号
				   "out_trade_no" => "23032796000660302041_".time(),
				   "appid" => 'wxfd8e23d4dad07f15', // <---- 请修改为服务号的 appid
				   "description" => "测试商品001",
				   "notify_url" => "https://api.vet168.cn/pay/notify",
				   "amount" => [
				        "total" => 1,
				        "currency" => "CNY"
				    ],
				    "payer" => [
				        "openid" => "o9KnD64oP-OPwq5JTFyBM_Bzqays" // <---- 请修改为服务号下单用户的 openid
				    ]
				]);

				var_dump($response);
				return $response;
			}catch(\Exception $e){
				return $this->getErrorMessage($e);
			}
		}

		/**
		 * 申请退款
		 * @param  Request $request [description]
		 * @param  array   $data    [description]
		 * @return [type]           [description]
		 */
		public function refund(Request $request,$data = []){
			$transactionId = isset($data['platform_order']) ? trim($data['platform_order']) : trim($request->post('platform_order',''));
			if(!$transactionId){
				return '无效的订单号';
			}
			$refundNumber = isset($data['order_code']) ? trim($data['order_code']) : trim($request->post('order_code',''));
			if(!$refundNumber){
				return '无效的退款单号';
			}
			$totalFee = isset($data['total_amount']) ? (int)$data['total_amount'] : $request->post('total_amount',0);
			if(!$totalFee){
				return '无效的订单金额';
			}
			$refundFee = isset($data['refunc_amount']) ? (int)$data['refunc_amount'] : $request->post('refunc_amount',0);
			if(!$refundFee || $refundFee > $totalFee){
				return '无效的退款金额';
			}
			$remark = isset($data['remark']) ? trim($data['remark']) : trim($request->post('remark',''));
			$config = [];
			if($remark){
				$config['refund_desc'] = $remark;
			}

			try{
				$result = $app->refund->byTransactionId($transactionId,$refundNumber,$totalFee,$refundFee,$config);
				return $result;
			}catch(\Exception $e){
				return $this->getErrorMessage($e);
			}

		}

		/**
		 * 企业付款到用户
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function payToWallet(Request $request,$data = []){
			$type = isset($data['pay_type']) ? $data['pay_type'] : $request->post('pay_type',0);
			//微信零钱
			$order = isset($data['order_code']) ? $data['order_code'] : $request->post('order_code','');
			$openid = isset($data['openid']) ? $data['openid'] : $request->post('openid','');
			$check_name = 'FORCE_CHECK';
			$realname = isset($data['user_realname']) ? $data['user_realname'] : $request->post('user_realname','');
			$amount = isset($data['amount']) ? $data['amount'] : $request->post('amount',0);
			$desc = isset($data['desc']) ? $data['desc'] : $request->post('desc','提现');

			// 银行卡
			$enc_bank_no = isset($data['enc_bank_no']) ? $data['enc_bank_no'] : $request->post('enc_bank_no','');
			$bank_code = isset($data['bank_code']) ? $data['bank_code'] : $request->post('bank_code','');

			switch($type){
				case 1: 	// 银行卡
					$option = [
						'partner_trade_no'	=> $order,
						'enc_bank_no'		=> $enc_bank_no,
						'enc_true_name'		=> $realname,
						'bank_code'			=> $bank_code,
						'amount'			=> $amount,
						'desc'				=> $desc
					];
					break;
				case 2: 	// 支付宝
					break;
				case 3:
					$option = [
						'partner_trade_no'	=> $order,
						'openid'			=> $openid,
						'check_name'		=> $check_name,
						're_user_name'		=> $realname,
						'amount'			=> $amount,
						'desc'				=> $desc
					];
					break;
				default: 
					return ['errcode'=>1, 'msg'=>'无效的提现方式'];

			}

			try{
				$this->config = $this->getConfig($request,'mini');
				
				$app = new Application($this->config);
				switch($type){
					case 1:
						$result = $app->transfer->toBankCard($option);
						break;
					case 2:
						$result = ['errcode'=>1, 'msg'=>'不支持该提现方式'];
						break;
					case 3:
						$result = $app->transfer->toBalance($option);
						break;
					default:
						$result = ['errcode'=>1, 'msg'=>'无效的提现方式'];
				}

				return $result;

			}catch(\Exception $e){
				var_dump('小程序提现error:'.'file:'.$e->getFile().' line:'.$e->getLine().' msg:'.$e->getMessage());
				return ['errcode' => 1,'errmsg' => 'file:'.$e->getFile().' line:'.$e->getLine().' msg:'.$e->getMessage()];
			}
			

		}

		/**
		 * 小程序发货
		 * @param Request $request [description]
		 * @param array   $data    [description]
		 */
		public function setSendout(Request $request,$data = []){
			$type = $request->input('type','mini');

			$order_number_type = isset($data['order_number_type']) ? $data['order_number_type'] : 2;						// 订单单号类型, 1，使用下单商户号和商户侧单号；枚举值2，使用微信支付单号。
			$transaction_id = $data['transaction_id'];	// 微信订单号
			$logistics_type = isset($data['logistics_type']) ? $data['logistics_type'] : 1;						// 物流模式 1、实体物流配送采用快递公司进行实体物流配送形式 2、同城配送 3、虚拟商品，虚拟商品，例如话费充值，点卡等，无实体配送形式 4、用户自提
			$delivery_mode = isset($data['delivery_mode']) ? $data['delivery_mode'] : 1;							// 发货模式，发货模式枚举值：1、UNIFIED_DELIVERY（统一发货）2、SPLIT_DELIVERY（分拆发货） 示例值: UNIFIED_DELIVERY
			$tracking_no = isset($data['tracking_no']) ? $data['tracking_no'] : '';			// 物流单号
			$express_company = isset($data['express_company']) ? $data['express_company'] : '';	// 物流公司编码，快递公司ID
			$item_desc = isset($data['item_desc']) ? $data['item_desc'] : '会员充值';				// 商品名称
			$consignor_contact = isset($data['consignor_contact']) ? $data['consignor_contact'] : '';						// 寄件人联系方式
			$receiver_contact = isset($data['receiver_contact']) ? $data['receiver_contact'] : '';							// 收件人联系方式
			$upload_time = date('Y-m-d\TH:i:sP',time());
			$openid = $data['openid'];

			try{
				$this->config = $this->getConfig($request,$type);
				
				$app = new Application($this->config);

				$accessToken = $app->getAccessToken();

				$url = '/wxa/sec/order/upload_shipping_info?access_token='.$accessToken->getToken();
				$options = [
					'order_key' => [
						'order_number_type' => $order_number_type,
						'transaction_id' => $transaction_id
					],
					'logistics_type' => $logistics_type,
					'delivery_mode'	=> $delivery_mode,
					'shipping_list' => [
						[
							'tracking_no' => $tracking_no,
							'express_company' => $express_company,
							'item_desc' => $item_desc,
							'contact' => [
								'consignor_contact' => $consignor_contact
							]
						]
					],
					'upload_time' => $upload_time,
					'payer' => ['openid' => $openid]
				];

				// var_dump('发货数据：');
				// var_dump($options);

				$response = $app->getClient()->postJson($url,$options);

				// $curl = new \Curl\Curl();
				// $curl->post($this->wxurl.$url,json_encode($options,JSON_UNESCAPED_UNICODE));
				// $curl->close();
				// $response = $curl->response;
				// var_dump('小程序发货result: ');
				// var_dump($response);
				if($response){
					$response = json_decode($response,true);
					// var_dump($response);
					return $response;
				}else{
					return ['errcode' => 1, 'errmsg' => '发货接口失败'];
				}

				// $response = $app->getClient()->postJson($url,$options);

				/*
				$uri = '/wxa/sec/order/upload_shipping_info?access_token='.$accessToken->getToken();
				$options = [
					'order_key' => [
						'order_number_type' => $order_number_type,
						'transaction_id' => $transaction_id
					],
					'logistics_type' => $logistics_type,
					'delivery_mode'	=> $delivery_mode,
					'shipping_list' => [
						'tracking_no' => $tracking_no,
						'express_company' => $express_company,
						'item_desc' => $item_desc,
						'contact' => [
							'consignor_contact' => $consignor_contact,
							'receiver_contact' => $receiver_contact
						]
					],
					'upload_time' => $upload_time,
					'payer' => ['openid' => $openid]
				];
				$response = $app->getClient()->post($uri,$options);
				// $response = ['errcode' => 0,'errmsg' => 'ok'];
				return $response;
				*/
			}catch(\Exception $e){
				var_dump('小程序发货error:'.'file:'.$e->getFile().' line:'.$e->getLine().' msg:'.$e->getMessage());
				return ['errcode' => 1,'errmsg' => 'file:'.$e->getFile().' line:'.$e->getLine().' msg:'.$e->getMessage()];
			}
		}

		public function getExpressList(Request $request){
			try{
				$type = $request->input('type','mini');
				$this->config = $this->getConfig($request,$type);
				
				$app = new Application($this->config);

				$accessToken = $app->getAccessToken();
				var_dump('accessToken: '.$accessToken->getToken());
				$url = '/product/delivery/get_company_list?access_token='.$accessToken->getToken();
				$response = $app->getClient()->post($url);
				$response = json_decode($response,true);
				var_dump($response);
				return $response;
			}catch(\Exception $e){
				return $this->getErrorMessage($e);
			}
		}

		/**
		 * 授权后更新用户
		 * @param  Request $request [description]
		 * @param  [type]  $session [description]
		 * @return [type]           [description]
		 */
		private function authUser(Request $request,$session){
			$service = new \app\model\User\LoginModel();
			return $service->checkLogin($request,$session,'wechat');
		}

		/**
		 * 从数据库获取配置项
		 * @param  Request $request [description]
		 * @param  string  $type    [description]
		 * @return [type]           [description]
		 */
		private function getConfig(Request $request,$type = 'mini'){
			$service = new OptionModel();
			if(in_array($type, ['wechat','mini'])){
				$key = 'wechat';
			}elseif(in_array($type,['alipay'])){
				$key = 'alipay';
			}else{
				$key = 'bank';
			}
			$result = $service->getList($request,'key',$key);
			if($result){
				switch($type){
					case 'wechat':
					case 'pc':
						$config = [
							'app_id' => $result['wechat_appid'],
						    'secret' => $result['wechat_appsecret'],
						    'token' => $result['wechat_token'],
						    'aes_key' => 'EncodingAESKey', // 明文模式请勿填写 EncodingAESKey
						    'oauth' => [
						        'scopes'   => ['snsapi_userinfo'],
						        'redirect_url' => $result['merchant_mch_backurl'],
						    ],

							'mch_id' => $result['merchant_mch_id'],
							// 商户证书
						    'private_key' => config_path() . '/cert/wechat/apiclient_key.pem',
						    'certificate' => config_path() . '/cert/wechat/apiclient_cert.pem',
						    // v3 API 秘钥
			    			'secret_key' => $result['merchant_api_v3_key'],
			    			// v2 API 秘钥
			    			'v2_secret_key' => $result['merchant_api_key'],
			    			'platform_certs' => [],
			    			'http' => [
						        'throw'  => true, // 状态码非 200、300 时是否抛出异常，默认为开启
						        'timeout' => 5.0,
						        'retry' => true,
						    ],
						];
						break;
					case 'mini':
						$config = [
							'app_id' => $result['mini_appid'],
						    'secret' => $result['mini_appsecret'],
						    'token' => $result['wechat_token'],
						    'aes_key' => '',

							'mch_id' => $result['merchant_mch_id'],
							// 商户证书
						    'private_key' => config_path() . '/cert/wechat/apiclient_key.pem',
						    'certificate' => config_path() . '/cert/wechat/apiclient_cert.pem',
						    // v3 API 秘钥
			    			'secret_key' => $result['merchant_api_v3_key'],
			    			// v2 API 秘钥
			    			'v2_secret_key' => $result['merchant_api_key'],
			    			'platform_certs' => [],
			    			'http' => [
						        'throw'  => true, // 状态码非 200、300 时是否抛出异常，默认为开启
						        'timeout' => 5.0,
						        'retry' => true,
						    ],
						];
						break;
					case 'app':
						$config = [
							'app_id' => $result['wechat_open_appid'],
						    'secret' => $result['wechat_open_appsecret'],
						    'token' => $result['wechat_token'],
						    'aes_key' => '',
							'mch_id' => $result['merchant_mch_id'],
							// 商户证书
						    'private_key' => config_path() . '/cert/wechat/apiclient_key.pem',
						    'certificate' => config_path() . '/cert/wechat/apiclient_cert.pem',
						    // v3 API 秘钥
			    			'secret_key' => $result['merchant_api_v3_key'],
			    			// v2 API 秘钥
			    			'v2_secret_key' => $result['merchant_api_key'],
			    			'platform_certs' => [],
			    			'http' => [
						        'throw'  => true, // 状态码非 200、300 时是否抛出异常，默认为开启
						        'timeout' => 5.0,
						        'retry' => true,
						    ],
						];
						break;
					default:
						$config = false;
				}
				return $config;
			}

			return false;
		}

		/**
		 * [getErrorMessage description]
		 * @param  [type] $e [description]
		 * @return [type]    [description]
		 */
		private function getErrorMessage($e){
			return [
				'code'	=> $e->getCode() ?: 1,
				'msg'	=> $e->getMessage()
			];
		}
	}
?>