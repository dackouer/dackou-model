<?php
	namespace dackou\service\Token;

	use Webman\Http\Request;
	use Illuminate\Database\Capsule\Manager as Db;
	use Firebase\JWT\JWT;
	use Firebase\JWT\Key;

	class TokenService{
		private static $config = [];
		private static $is_token = true;
		private static $token_type = 'Bearer';
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
		private static $group_id = 12;
		private static $controller = [
			['controller' => 'Index','action' => ['view']],
			['controller' => 'Logout','action' => ['index']],
			['controller' => 'Token','action' => ['index']],
			['controller' => 'Captcha','action' => ['index']],
			['controller' => 'Application','action' => ['login','app']],
			['controller' => 'Socialite','action' => ['*']],
			['controller' => 'Callback','action' => ['*']],
			['controller' => 'Upload','action' => ['local']],
			['controller' => 'Activity','action' => ['fimport']],
			['controller' => 'Tabbar','action' => ['index']],
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
				if(isset(self::$config['token_type']) && !empty(self::$config['token_type'])){
					self::$token_type = self::$config['token_type'];
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
				if(isset(self::$config['user_controller']) && \class_exists(self::$config['user_controller'])){
					self::$user_controller = self::$config['user_controller'];
				}
				if(isset(self::$config['group_id']) && is_numeric(self::$config['group_id'])){
					self::$group_id = (int)self::$config['group_id'];
				}

				if(isset(self::$config['controller']) && self::$config['controller']){
					self::$controller = \array_merge(self::$controller,self::$config['controller']);
				}

				// var_dump(self::$controller);
				// var_dump('access_key: '.self::$access_key);
				// var_dump('refresh_key: '.self::$refresh_key);
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
				// var_dump('generate token begin:',$user);
				self::setConfig();
				// $user = self::getUserInfo($request);
				if(!$user || !is_object($user) || !property_exists($user,'uid')){
					$user = self::getUserInfo($request);
				}
				// var_dump('user: ',$user);
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
				// var_dump($payload);
				$token = JWT::encode($payload, self::$access_key, self::$algorithms);
				if(self::$token_type){
					$token = self::$token_type . ' ' . $token;
				}
				return ['token' => $token,'expire_time' => $payload['expire']];
			}catch(\Exception $e){
				return self::getExceptionError($e);
			}
		}

		public static function createToken($payload){
			try{
				self::setConfig();
				$token = JWT::encode($payload, self::$access_key, self::$algorithms);
				if(self::$token_type){
					$token = self::$token_type . ' ' . $token;
				}
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
				if(self::$token_type){
					$token = substr($token,strlen(self::$token_type)+1);
				}
				// var_dump('decode token: '.$token);
				$headers = new \stdClass();
				return JWT::decode($token, new Key(self::$access_key, self::$algorithms), $headers);
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
			try{

				// var_dump('controller: '.$request->controller.' action: '.$request->action);
				if(self::$controller && count(self::$controller)){
					$controller = \explode('\\',$request->controller);
					foreach(self::$controller as $item){
						if(end($controller) == $item['controller'] && ($item['action'] == '*' || $item['action'][0] == '*' || in_array($request->action,$item['action']))){
							return true;
						}
					}
				}

				self::setConfig();

				$authorization = trim($request->header(self::$token_name,''));
				$visitor = trim($request->header(self::$visitor_name,''));
				$appkey = trim($request->header(self::$key_name,''));
				$appsecret = trim($request->header(self::$secret_name,''));

				// api模式
				if(!empty($appkey) && !empty($appsecret)){
					return 'api token';
				}

				// var_dump($request->controller .' action: '.$request->action . ' authorization source: '.$authorization);
				// var_dump('token_name: '.self::$token_name);
				// 常规模式
				if(!$authorization || $authorization == '' || (self::$token_type && strpos($authorization, self::$token_type.' ') !== 0) || strlen($authorization) < 32){
					return 100011;		// token不能为空
				}
				
				$res = self::decodeToken($authorization);
				if(!$res || !is_object($res)){
					return $res;
				}
				if(!property_exists($res,'uid') || !property_exists($res,'token') || !property_exists($res,'sign') || !property_exists($res,'rid') || !property_exists($res,'expire')){
				    return 100012;		// 无效token
				}

				// var_dump('expire_time: '. $res->expire);
				if(strtotime($res->expire) <= time()){
					return 100014;		// token过期
				}
				$uid = $request->input('uid',0);
				// var_dump('controller: '.$request->controller.' action: '.$request->action.' uid: '.$uid.' token uid: '.$res->uid);
				if($uid && $res->uid != $uid){
					return 100013;		// token无效 
				}
				$uid = $uid ?: $res->uid;

				$group_id = floor($res->rid / 100);
				if($group_id === 12){
					$flag = true;
					if(self::$controller && count(self::$controller)){
						$controller = \explode('\\',$request->controller);
						foreach(self::$controller as $item){
							if(end($controller) == $item['controller'] && ($item['action'] == '*' || $item['action'][0] == '*' || in_array($request->action,$item['action']))){
								$flag = false;
							}
						}
					}
					if($flag){
						return '未登录或登录超时';
					}
				}

				

				return $res;
			}catch(\Exception $e){
				// var_dump($e->getMessage());
				return 100015;
			}
		}

		public static function getToken(Request $request,$flag = false){
			self::setConfig();

			$authorization = trim($request->header(self::$token_name,''));
			if(!$authorization || empty($authorization)){
				return '';
			}
			if($flag){
				return $authorization;
			}

			$authorization = substr($authorization,strlen(self::$token_type)+1);
			return $authorization;
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
				if($key == 'role_id'){
					return $res->rid ?? false;
				}elseif($key == 'group_id'){
					return (int)($res->rid / 100);
				}
				return property_exists($res,$key) ? $res->$key : '';
			}catch(\Exception $e){
				return self::getExceptionError($e);
			}
		}

		private static function getUser(){
			self::setConfig();
			$authorization = trim(request()->header(self::$token_name,''));
			if(!$authorization || empty($authorization)){
				return '';
			}
			return self::decodeToken($authorization);
		}

		private static function getUserID(){
			$user = self::getUser();
			return $user->uid;
		}

		private static function getRoleID(){
			$user = self::getUser();
			return $user->role_id;
		}

		private static function getGroupID(){
			$user = self::getUser();
			$role_id = $user->role_id;
			return floor($role_id/100);
		}

		// 检查用户信息
		private static function getUserInfo(Request $request,$uid = 0){
			if(self::$user_controller){
				$_class = new self::$user_controller();
				return $uid ? $_class->getList($request,$uid) : $_class->getList($request,'api',self::$group_id);
			}
			return self::getUserData($request);
		}

		private static function getUserData(Request $request){
			try{
				$field = [
					"AccountID as uid",
					"Token as token",
					"Sign as sign",
					"RoleID as role_id",
					"PID as pid",
					"IsAdmin as is_admin",
					"user.Status as status"
				];
				$where = [
					['user.IsDel','=',0],
					['role.IsDel','=',0],
					['role.PID','=',12]
				];

				$user = Db::table('user')
						->join('role','RoleID','=','role.ID')
						->select(...$field)
						->where($where)
						->first();
				// var_dump('user: ',$user);
				if(!$user || !is_object($user)){
					return 100012;
				}

				if($user->status !== 1){
					return 100016;
				}

				return $user;
			}catch(\Exception $e){
				// var_dump($this->getExceptionError($e));
				return 100012;
			}
		}

		public static function generateNewToken(Request $request){
			try{
				$user = self::getUserData($request);
				if(!$user || !is_object($user)){
					return $user;
				}

				self::setConfig();

				$payload = [
					'uid' 		=> $user->uid,
					'token' 	=> $user->token,
					'sign' 		=> $user->sign,
					'rid' 		=> $user->role_id,
					'expire' 	=> date('Y-m-d H:i:s',(time()+24*3600*365*10))
				];
				// var_dump($payload);
				$token = JWT::encode($payload, self::$access_key, self::$algorithms);
				if(self::$token_type){
					$token = self::$token_type . ' ' . $token;
				}
				return ['token' => $token,'expire_time' => $payload['expire']];
			}catch(\Exception $e){
				// var_dump($this->getExceptionError($e));
				return '';
			}
		}

		// 检查商户
		private static function getMerchant(Request $request,$appkey,$appsecret){

		}

		private static function getExceptionError($e){
			$msg = [
				'code'	=> $e->getCode() ? $e->getCode() : 1,
				'file'	=> $e->getFile(),
				'line'	=> $e->getLine(),
				'msg'	=> $e->getMessage()
			];
			// var_dump($msg);

			return $msg;
		}
	}
?>