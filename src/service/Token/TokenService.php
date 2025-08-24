<?php
	namespace dackou\service\Token;

	use Webman\Http\Request;
	use Illuminate\Database\Capsule\Manager as Db;
	use Firebase\JWT\JWT;
	use Firebase\JWT\Key;

	class TokenService{
		private static $config = [];
		private static $is_token = true;
		private static $algorithms = 'HS256';
		private static $token_name = 'Authorization';
		private static $access_key = 'HkneVaxcprlg2099';
		private static $expire_time = 7200;
		private static $refresh_key = 'BmiesHadlqZo2029';
		private static $refresh_time = 604800;
		private static $refresh_disable = false;
		private static $is_single = false;
		private static $visitor_name = 'token';
		private static $key_name = 'AppKey';
		private static $secret_name = 'AppSecret';
		private static $user_controller = '\\app\\model\\User\\UserModel';
		private static $controller = [
			['controller' => 'Index','action' => ['mqtt']],
			['controller' => 'Token','action' => ['index']],
			['controller' => 'Captcha','action' => ['index']],
		];

		// 配置参数
		private static function setConfig(){
			if(!self::$config){
				self::$config = config('token');
				if(isset(self::$config['is_token'])){
					self::$is_token = self::$config['is_token'] ? true : false;
				}
				if(isset(self::$config['algorithms']) && !empty(self::$config['algorithms'])){
					self::$algorithms = self::$config['algorithms'];
				}
				if(isset(self::$config['access_key']) && !empty(self::$config['access_key'])){
					self::$access_key = self::$config['access_key'];
				}
				if(isset(self::$config['expire_time']) && self::$config['expire_time']){
					self::$expire_time = (int)self::$config['expire_time'];
				}
				if(isset(self::$config['refresh_key']) && self::$config['refresh_key']){
					self::$refresh_key = self::$config['refresh_key'];
				}
				if(isset(self::$config['refresh_time']) && self::$config['refresh_time']){
					self::$refresh_time = (int)self::$config['refresh_time'];
				}
				if(isset(self::$config['refresh_disable'])){
					self::$refresh_disable = self::$config['refresh_disable'] ? true : false;
				}
				if(isset(self::$config['is_single'])){
					self::$is_single = self::$config['is_single'] ? true : false;
				}
			}
			if(!class_exists(self::$user_controller)){
				self::$user_controller = '\\dackou\\model\\User\\UserModel';
			}
		}

		/**
		 * 生成token
		 * @param  Request $request [description]
		 * @param  integer $uid     [description]
		 * @return [type]           [description]
		 */
		public static function generateToken(Request $request,$user = null){
			try{	
				self::setConfig();
				// $user = self::getUserInfo($request,$uid);
				if(!$user || !is_object($user) || !property_exists($user,'uid')){
					$user = self::getUserInfo($request);
				}
				if(!$user || !is_object($user) || !property_exists($user,'uid')){
					return 100007;
				}
				$payload = [
					'uid' 		=> $user->uid,
					'token' 	=> $user->token,
					'sign' 		=> $user->sign,
					'rid' 		=> $user->role_id,
					'expire' 	=> date('Y-m-d H:i:s',(time()+24*3600*365*10))
				];
				$token = JWT::encode($payload, self::$access_key, self::$algorithms);
				return ['token' => $token,'expire_time' => $payload['expire']];
			}catch(\Exception $e){
				return self::getExceptionError($e);
			}
		}

		/**
		 * 加密token
		 * @param  [type] $data [description]
		 * @return [type]       [description]
		 */
		public static function encodeToken($payload){
			try{
				self::setConfig();
				return JWT::encode($payload, self::$access_key, self::$algorithms);
			}catch(\Exception $e){
				return self::getExceptionError($e);
			}
		}

		/**
		 * 解密token
		 * @param  [type] $token [description]
		 * @return [type]       [description]
		 */
		public static function decodeToken($token){
			try{
				self::setConfig();
				return JWT::decode($token, new Key(self::$access_key, self::$algorithms));
			}catch(\Exception $e){
				return self::getExceptionError($e);
			}
		}

		/**
		 * 验证token
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public static function checkToken(Request $request){
			// var_dump('get new token: ',self::generateToken($request));
			try{
				self::setConfig();

				$authorization = trim($request->header(self::$token_name,''));
				$visitor = trim($request->header(self::$visitor_name,''));
				$appkey = trim($request->header(self::$key_name,''));
				$appsecret = trim($request->header(self::$secret_name,''));

				// api模式
				if(!empty($appkey) && !empty($appsecret)){
					return 'api token';
				}

				// var_dump('authorization: '.$authorization);
				// 常规模式
				if(!$authorization || $authorization == '' || strlen($authorization) < 32){
					return 100011;		// token不能为空
				}
				$res = self::decodeToken($authorization);
				// var_dump('decode token data:');
				// var_dump($res);
				if(!$res || !is_object($res)){
					return $res;
				}
				if(!property_exists($res,'uid') || !property_exists($res,'token') || !property_exists($res,'sign') || !property_exists($res,'rid') || !property_exists($res,'expire')){
				    return 100012;		// 无效token
				}

				// var_dump('controller: '.$request->controller.' action: '.$request->action);
				if(self::$controller && count(self::$controller)){
					$controller = \explode('\\',$request->controller);
					foreach(self::$controller as $item){
						if(end($controller) == $item['controller'] && in_array($request->action,$item['action'])){
							return true;
						}
					}
				}

				// var_dump('expire_time: '. $res->expire);
				if(strtotime($res->expire) <= time()){
					return 100014;		// token过期
				}
				$uid = $request->input('uid',0);
				if($uid && $res->uid != $uid){
					return 100013;		// token无效 
				}
				$uid = $uid ?: $res->uid;
				return $res;
			}catch(\Exception $e){
				// var_dump($e->getMessage());
				return 100015;
			}
		}

		/**
		 * 获取token原数据
		 * @param  Request $request [description]
		 * @param  string  $key     [description]
		 * @return [type]           [description]
		 */
		public static function getTokenData(Request $request,$key = ''){
			try{
				self::setConfig();

				$authorization = trim($request->header(self::$token_name,''));
				if(!$authorization || empty($authorization)){
					return '';
				}
				$res = self::decodeToken($authorization);
				// var_dump($res);
				if(!$res || !is_object($res)){
					if(is_array($res) && isset($res['msg']) && $res['msg'] == "Incorrect key for this algorithm"){
						$res['msg'] = '非法token';
					}
					return $res;
				}
				if(empty($key) || !$key){
					return $res;
				}
				// var_dump($res);
				return property_exists($res,$key) ? $res->$key : '';
			}catch(\Exception $e){
				return self::getExceptionError($e);
			}
		}

		// 检查用户信息
		private static function getUserInfo(Request $request,$uid = 0){
			if(self::$user_controller){
				$_class = new self::$user_controller();
				return $uid ? $_class->getList($request,$uid) : $_class->getList($request,'api');
			}
			
			try{
				$field = [
					"AccountID as uid",
					"Token as token",
					"Sign as sign",
					"RoleID as role_id",
					"PID as pid",
					"IsAdmin as is_admin",
					"IsValid as is_valid",
					"IsLocked as is_locked",
					"user.IsDel as is_del",
					"Status as status"
				];
				$where = [
					['AccountID','=',$user->uid],
					['Token','=',$user->token],
					['Sign','=',$user->sign],
					['role.IsDel','=',0]
				];

				$user = Db::table('user')
						->join('role','RoleID','=','role.ID')
						->select(...$field)
						->where($where)
						->first();
				
				if(!$user || !is_object($user)){
					return 100012;
				}

				if(!$user->is_valid || $user->is_locked || $user->is_del){
					return 100016;
				}

				return $user;
			}catch(\Exception $e){
				return 100012;
			}
		}

		// 检查商户
		private static function getMerchant(Request $request,$appkey,$appsecret){

		}

		private static function getExceptionError($e){
			return [
				'code'	=> $e->getCode() ? $e->getCode() : 1,
				'file'	=> $e->getFile(),
				'line'	=> $e->getLine(),
				'msg'	=> $e->getMessage()
			];
		}
	}
?>