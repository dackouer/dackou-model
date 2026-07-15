<?php
	namespace dackou\service\Wechat;

	use support\Request;
	use support\Http;
	use EasyWeChat\Pay\Application;
	use dackou\model\User\UserModel;
	use dackou\model\Order\OrderModel;

	class WechatService{
		private $mp_appid = '';
		private $mp_appsecret = '';
		private $appid = '';
		private $appsecret = '';
		private $token = '';
		private $mchid = 0;
		private $keyv2 = '';
		private $keyv3 = '';
		private $private_key = '';
		private $certificate = '';
		private $config = [];
		private $notify = '';

		public function __construct($appid = '',$appsecret = ''){
			if($appid){
				$this->appid = $appid;
			}
			if($appsecret){
				$this->appsecret = $appsecret;
			}
			$this->setConfig();
		}

		// 设置参数
		private function setConfig(){
			$config = config('payment');
			if($config && isset($config['wechat']) && isset($config['wechat']['default'])){
				if(isset($config['wechat']['default']['mp_app_id'])){
					$this->mp_appid = $config['wechat']['default']['mp_app_id'];
				}
				if(isset($config['wechat']['default']['mp_app_secret'])){
					$this->mp_appsecret = $config['wechat']['default']['mp_app_secret'];
				}
				if(isset($config['wechat']['default']['mini_app_id'])){
					$this->appid = $config['wechat']['default']['mini_app_id'];
				}
				if(isset($config['wechat']['default']['mini_app_secret'])){
					$this->appsecret = $config['wechat']['default']['mini_app_secret'];
				}
				if(isset($config['wechat']['default']['mp_token'])){
					$this->token = $config['wechat']['default']['mp_token'];
				}
				if(isset($config['wechat']['default']['mch_id'])){
					$this->mchid = $config['wechat']['default']['mch_id'];
				}
				if(isset($config['wechat']['default']['mch_secret_key_v2'])){
					$this->keyv2 = $config['wechat']['default']['mch_secret_key_v2'];
				}
				if(isset($config['wechat']['default']['mch_secret_key'])){
					$this->keyv3 = $config['wechat']['default']['mch_secret_key'];
				}
				if(isset($config['wechat']['default']['mch_secret_cert'])){
					$this->private_key = $config['wechat']['default']['mch_secret_cert'];
				}
				if(isset($config['wechat']['default']['mch_public_cert_path'])){
					$this->certificate = $config['wechat']['default']['mch_public_cert_path'];
				}
			}

			$this->config = [
				'mch_id' => $this->mchid,

				// 商户证书
			    'private_key' => $this->private_key,
			    'certificate' => $this->certificate,

			     // v3 API 秘钥
			    'secret_key' => $this->keyv3,

			    // v2 API 秘钥
			    'v2_secret_key' => $this->keyv2,

			    // 平台证书：微信支付 APIv3 平台证书，需要使用工具下载
			    // 下载工具：https://github.com/wechatpay-apiv3/CertificateDownloader
			    'platform_certs' => [
			        // 请使用绝对路径
			        // '/path/to/wechatpay/cert.pem',
			    ],

			    /**
			     * 接口请求相关配置，超时时间等，具体可用参数请参考：
			     * https://github.com/symfony/symfony/blob/5.3/src/Symfony/Contracts/HttpClient/HttpClientInterface.php
			     */
			    'http' => [
			        'throw'  => true, // 状态码非 200、300 时是否抛出异常，默认为开启
			        'timeout' => 5.0,
			        // 'base_uri' => 'https://api.mch.weixin.qq.com/', // 如果你在国外想要覆盖默认的 url 的时候才使用，根据不同的模块配置不同的 uri
			    ],
			];
		}

		/**
		 * [codeToSession description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function codeToSession(Request $request){
			$code = trim($request->input('code',''));
			if(!$code){
				return 100007;
			}
			
			$appId = $this->appid;
			$appSecret = $this->appsecret;

			$url = "https://api.weixin.qq.com/sns/jscode2session?appid={$appId}&secret={$appSecret}&js_code={$code}&grant_type=authorization_code";

			$http = new Http();
			$result = $http->get($url);

			return $result;
		}

		/**
		 * 获取用户
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function getUserInfo(Request $request){
			$code = trim($request->input('code',''));
			if(!$code){
				return 100007;
			}

			$appId = $this->appid;
			$appSecret = $this->appsecret;

			// 向微信服务器发送请求，获取openid
			$url = "https://api.weixin.qq.com/sns/jscode2session?appid={$appId}&secret={$appSecret}&js_code={$code}&grant_type=authorization_code";

			$http = new Http();
			$result = $http->get($url);

			return $result;
		}

		/**
		 * 微信授权
		 * @param Request $request [description]
		 */
		public function setAuth(Request $request){
			$config = [
			    'app_id' => $this->appid,
			    'secret' => $this->appsecret,
			    'token' => $this->token,
			    'aes_key' => '......'
			  //...
			];

			$app = new \EasyWeChat\OfficialAccount\Application($config);
			$oauth = $app->getOauth();

			//callback_url 是授权回调的URL
			if (empty($_SESSION['wechat_user'])) {
			  $_SESSION['intend_url'] = 'user/profile';
			  //生成完整的授权URL
			  $redirectUrl = $oauth->redirect('callback_url');

			  return \redirect($redirectUrl);
			} else {
			  // 已经登录过，则从 session 中取授权者信息
			  $user = $_SESSION['wechat_user'];

			  // ...
			}

			$redirectUrl = $oauth->scopes(['snsapi_userinfo'])->redirect();
			// 指定回调 URL，比如设置回调 URL 为当前页面
			$redirectUrl = $oauth->scopes(['snsapi_userinfo'])->redirect($request->fullUrl());

			return \redirect($redirectUrl);
		}

		/**
		 * 获取授权回调
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function getAuth(Request $request){
			$config = [
			    'app_id' => $this->appid,
			    'secret' => $this->appsecret,
			    'token' => $this->token,
			    'aes_key' => '......'
			  //...
			];


			$app = new Application($config);

			$oauth = $app->getOauth();

			// 获取 OAuth 授权用户信息
			$user = $oauth->userFromCode($_GET['code']);

			$_SESSION['wechat_user'] = $user->toArray();

			$targetUrl = empty($_SESSION['intend_url']) ? '/' : $_SESSION['intend_url'];

			header('Location:'. $targetUrl); // 跳转回授权前的目标页面：user/profile
		}

		/**
		 * 统一下单
		 */
		public function setOrder(Request $request,$data = null){
			$check = $this->checkToken($request);

			if(!is_array($check) || !isset($check['code']) || $check['code'] != 'success'){
				return $check;		// token 验证失败
			}

			$type 		= trim($request->post('type','jsapi'));			// 下单方式
			$user_id 	= $request->post('user_id');					// 用户
			$title 		= trim($request->post('title',''));				// 标题
			$order 		= trim($request->post('order',''));				// 客户订单号
			$notify 	= trim($request->post('notify',$this->notify));	// 回调地址
			$amout  	= $request->post('amout',0);					// 下单金额
			$openid 	= trim($request->post('openid',''));			// openid
			$token 		= trim($request->post('token',''));				// 订单token

			if(empty($type)){
				return 101813;		// 无效的下单方式
			}

			$type = strtolower($type);

			if(!in_array($type,['jsapi','native'])){
				return 101813;		// 无效的下单方式
			}

			if(empty($user_id) || !is_numeric($user_id) || !$user_id){
				return 101800;		// 用户id不能为空
			}

			if($user_id != $check['uid']){
				return 101801;		// 无效的用户id
			}

			$service = new UserModel();
			$user = $service->getList($request,$user_id);
			if(!$user || !is_array($user)){
				return 101802;		// 无效的用户
			}

			if(!$user['is_valid'] || $user['is_locked'] || $user['is_del']){
				return 101803;		// 用户无权限操作
			}

			if(empty($title)){
				return 101804;		// 标题不能为空
			}

			if(empty($order)){
				return 101805;		// 客户订单号不能为空
			}

			$service = new OrderModel();
			$resp = $service->getList($request,'code',$order);
			if(!$resp || !is_array($resp)){
				return 101806;		// 无效的订单
			}

			if(empty($notify)){
				return 101808;		// 无效的回调地址
			}

			if(empty($amout) || !is_numeric($amout) || !$amout){
				return 101809;		// 下单金额不能为空且大于0
			}

			if($amout != $resp['amout']){
				return 101809;		// 无效的下单金额
			}

			if($amout < 10){
				return 101809;		// 无效的下单金额
			}

			if($type == 'jsapi' && empty($openid)){
				return 101810;		// 未授权的用户
			}

			if(empty($token)){
				return 101811;		// token不能为空
			}

			if(!$this->validToken($token,$resp['token'])){
				return 101812;		// 无效的token
			}

			$body = [
				"mchid" 		=> $this->config['mch_id'], 
				"out_trade_no" 	=> "native{$order}".rand(1,100000),
				"appid" 		=> $this->config['appid'], 
				"description" 	=> $title,
				"notify_url" 	=> $notify,
				"amount" 		=> ["total" => $amout,"currency" => "CNY"]
			];

			if($type == 'jsapi'){
				$body['payer'] = ["openid" => $openid];
			}

			$app = new Application($this->config);
			$response = $app->getClient()->postJson("v3/pay/transactions/{$type}", $body);
			$result = $response->toArray(false);

			var_dump($result);
			return $result;
		}

		/**
		 * 统一查询订单
		 * @param Request $request [description]
		 */
		public function setSearch(Request $request){
			$type = trim($request->post('type','id'));
			$order = trim($request->post('order',''));

			if(empty($type) || !in_array($type,['id','out-trade-no'])){
				return 101805;
			}

			if(empty($order)){
				return 101805;
			}

			$url = $type == 'id' ? "pay/transactions/id/{$order}" : "v3/pay/transactions/out-trade-no/{$order}";

			$app = new Application($this->config);
			// 查询商户订单号 / 查询微信订单号
			$response = $app->getClient()->get($url, [
			    'query'=>[
			        'mchid' =>  $app->getMerchant()->getMerchantId()
			    ]
			]);

			print_r($response->toArray());
		}

		/**
		 * 支付统一入口
		 * @param Request $request [description]
		 */
		public function setPay(Request $request){
			$check = $this->checkToken($request);

			if(!is_array($check) || !isset($check['code']) || $check['code'] != 'success'){
				return $check;		// token 验证失败
			}

			$user_id 	= $request->post('user_id');					// 用户
			$realname 	= $request->post('realname');					// 用户姓名
			$title 		= trim($request->post('title',''));				// 标题
			$order 		= trim($request->post('order',''));				// 客户订单号
			$amout  	= $request->post('amout',0);					// 下单金额
			$openid 	= trim($request->post('openid',''));			// openid
			$token 		= trim($request->post('token',''));				// 订单token


			$app = new Application($this->config);

			$body = [
				'body' => [
					'mch_appid' 		=> $app->getConfig()['app_id'],     // 注意在配置文件中加上app_id
					'mchid' 			=> $app->getConfig()['mch_id'],     // 商户号
					'partner_trade_no' 	=> $order,  						// 商户订单号，需保持唯一性(只能是字母或者数字，不能包含有符号)
					'openid' 			=> $openid,     					// 用户openid
					'check_name' 		=> 'FORCE_CHECK',                  	// NO_CHECK：不校验真实姓名, FORCE_CHECK：强校验真实姓名
					're_user_name'		=> $realname,               		// 如果 check_name 设置为 FORCE_CHECK 则必填用户真实姓名
					'amount' 			=> $amout,                          // 金额
					'desc' 				=> $title,                          // 企业付款操作说明信息。必填
				],
				'local_cert' => $app->getConfig()['certificate'], //v2证书绝对路径
			    'local_pk' => $app->getConfig()['private_key'],   //v2证书密钥绝对路径
			];

			$response = $api->post('/mmpaymkttransfers/promotion/transfers', $body);

			print_r($response->toArray());
		}

		/**
		 * 生成小程序码
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function createMiniCode(Request $request,$data = [],$flag = false){
			$path = isset($data['path']) ? trim($data['path']) : trim($request->input('path',''));
			$width = isset($data['width']) ? $data['width'] : $request->input('width',430);

			if(!$path){
				return 100007;
			}

			if(substr($path,0,6) != '/pages'){
				$path = substr($path,0,1) == '/' ? '/pages' . $path : '/pages/' . $path;
			}
			// var_dump('path: '.$path);

			$access_token = $this->getAccessToken();
			// var_dump('access_token: ',$access_token);
			if(is_array($access_token) && isset($access_token['code']) && $access_token['code']){
				return $access_token;
			}
			$url = "https://api.weixin.qq.com/wxa/getwxacode?access_token={$access_token}";
	        $data = array(
	            'path'  => $path,
	            'width' => $width
	        );

	        // $http = new Http();
	        // $result = $http->setBaseUrl($url)->post('', $data);
	        $data = json_encode($data);
	        $result = $this->httpRequest($url,$data);
	        // var_dump($result);
	        $base64Img = "data:image/png;base64,".base64_encode($result);
	        if($flag){
	        	return response($result,200,['Content-Type' => 'image/jpeg']);
	        }
	        return ['code' => 0,'msg' => 'success','data' => $base64Img];
		}

		/**
		 * 获取公众号文章列表
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function getArticle(Request $request){
			$access_token = $this->getAccessToken('mp');
			if(is_array($access_token) && isset($access_token['code'])){
				return $access_token;
			}
			// var_dump('access_token: '.$this->access_token);
			
			$url = "https://api.weixin.qq.com/cgi-bin/material/get_materialcount?access_token={$access_token}";
			$response = $this->httpRequest($url);
		    $result = json_decode($response, true);
		    $rows = $result['news_count'] ?? 0;  // 图文素材总条数
		    // var_dump('rows: '.$rows);

			$page = $request->input('page',1);
			$pagesize = $request->input('pagesize',10);
			$offset = $pagesize * ($page - 1);

			// 获取已成功发布的消息列表
			// $url = "https://api.weixin.qq.com/cgi-bin/freepublish/batchget?access_token={$access_token}";

			// 获取已发布的图文信息
			// $url = "https://api.weixin.qq.com/cgi-bin/freepublish/getarticle?access_token={$access_token}";

			$url = "https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token={$access_token}";
			$data = [
				'type'   => 'news',
		        'offset' => $offset,
		        'count'  => $pagesize,
		        'no_content' => 0,
		    ];

		    $data = json_encode($data);
		    $response = $this->httpRequest($url,$data);
		    $result = json_decode($response, true);
		    var_dump('get article: ',$result);
	        if(isset($result['errcode']) && $result['errcode'] != 0){
	            return ['code'=> 1,'msg' => $result['errmsg']];
	        }
	        
	        // $rows = $result['total_count'] ?? 0;
	        $articles = [];
            if(isset($result['item']) && is_array($result['item']) && $result['item']){
            	var_dump('get news item:',$result['item'][0]);
                foreach ($result['item'] as $item) {
                    $media_id = $item['media_id'] ?? $item['article_id'] ?? '';
                    $create_time = $item['content']['create_time'] ?? $item['update_time'] ?? 0;
                    $update_time = $item['content']['update_time'] ?? $item['update_time'] ?? 0;
                    // 图文素材可能包含多个子图文（多图文）
                    $news_items = $item['content']['news_item'] ?? [];
                    foreach ($news_items as $news) {
                    	// if(!$news['is_deleted']){
	                        $articles[] = [
	                            'media_id'            => $media_id,
	                            'title'               => $news['title'],
	                            'author'              => $news['author'],
	                            'digest'              => $news['digest'],
	                            'content'             => $news['content'] ?? '',          // HTML 内容
	                            'content_source_url'  => $news['content_source_url'] ?? '',
	                            'thumb_url'           => $news['thumb_url'],
	                            'url'                 => $news['url'],             // 微信永久链接
	                            'show_cover_pic'      => $news['show_cover_pic'] ?? '',
	                            'thumb_media_id'      => $news['thumb_media_id'] ?? '',
	                            'create_time'		  => $create_time,
	                            'update_time'		  => $update_time,
	                        ];
	                    // }
                    }
                }
            }

		    return ['code' => 0,'rows' => $rows,'data' => $articles];
		}


	    private function httpRequest($url, $data = []) {
	        $ch = curl_init();
	        curl_setopt($ch, CURLOPT_URL, $url);
	        curl_setopt($ch, CURLOPT_POST, 1);
	        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	        $result = curl_exec($ch);
	        curl_close($ch);
	        return $result;
	    }

		// 获取AccessToken
	    private function getAccessToken($type = 'mini') {
	    	$appid = $type === 'mini' ? $this->appid : $this->mp_appid;
	    	$appsecret = $type === 'mini' ? $this->appsecret : $this->mp_appsecret;
	        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$appsecret}";
	        $result = file_get_contents($url);
	        // var_dump($result);
	        $result = json_decode($result, true);
	        if(is_array($result) && isset($result['access_token'])){
	        	return $result['access_token'];
	    	}
	    	return [
	    		'code'=> isset($result['errcode']) ? $result['errcode'] : 1,
	    		'msg' => isset($result['errmsg']) ? $result['errmsg'] : 'fail'];
	    }

		private function checkToken(Request $request){
			return ['code' => 'success'];
		}
	}
?>