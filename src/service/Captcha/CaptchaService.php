<?php
	namespace dackou\service\Captcha;

	use support\Request;
	use support\Redis;
	use Webman\Captcha\CaptchaBuilder;

	class CaptchaService{
		private static $prefix = '';
		private $key = 'numcode';
		private $is_redis = true;

		public function __construct(){
			if(empty(self::$prefix)){
				self::$prefix = $this->getPrefix();
			}
		}

		/**
		 * [createCaptcha description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function createCaptcha(Request $request){
			$builder = new CaptchaBuilder();
			// 生成验证码
			$builder->build();
			// 获取code
			$code = $builder->getPhrase();
			$this->setSession($request,$code);
			// 获取图片二进制数据
			$img = $builder->get();

			$type = trim($request->input('type',''));
			return $type === 'img' ? response($img,200,['Content-Type' => 'image/jpeg']) : $img;
			// var_dump($img);
			// $base64Img = "data:image/png;base64,".base64_encode($img);
			// return ['code' => 0,'msg' => 'success','data' => $base64Img];
			// return response($img,200,['Content-Type' => 'image/jpeg']);
		}

		public function getCode(Request $request){
			$key = self::$prefix.$this->key;
			$session_code = $request->session()->get($key);

			return $session_code;
		}

		/**
		 * [check description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function check(Request $request){
			$numcode = trim($request->input($this->key,$request->input('code')));
			$key = self::$prefix.$this->key;
			$session_code = $request->session()->get($key);
			// var_dump('check session_code: '.$session_code);
			if(!$numcode || !$session_code || strtolower($numcode) !== strtolower($session_code)){
				return false;
			}
			return true;
		}

		private function getPrefix(){
			$config = config('database') ?? [];
			if(isset($config['connections']['mysql']['prefix'])){
    			return $config['connections']['mysql']['prefix'];
    		}
    		return '';
		}

		/**
		 * [setSession description]
		 * @param Request $request [description]
		 * @param [type]  $code    [description]
		 */
		private function setSession(Request $request,$code){
			if(empty(self::$prefix)){
				self::$prefix = $this->getPrefix();
			}
			$key = self::$prefix.$this->key;
			// var_dump('token: '. $request->header('token',$request->input('token')));
			if($this->is_redis && class_exists('Redis') && ($request->host(true) !== 'localhost' || $request->host(true) !== '127.0.0.1')){
				$token = $request->header('token',$request->input('token'));
				if($token){
					$key = $key . '_' . $token;
					Redis::set($key,$code);
					// var_dump('captcha create redis: ' . $key . ': '. Redis::get($key));
				}

			}
			$request->session()->set($key,$code);
			// var_dump('captcha create session '.($key).': '.$request->session()->get($key));
		}
	}
?>