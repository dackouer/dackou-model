<?php
	namespace Dackou;

	use Webman\Http\Request;
	use Illuminate\Database\Capsule\Manager as Db;
	use Firebase\JWT\JWT;
	use Firebase\JWT\Key;

	class Token{
		private static $key = '';
		private static $type = 'HS256';
		private static $token_name = 'Authorization';
		private static $visitor_name = 'token';
		private static $key_name = 'AppKey';
		private static $secret_name = 'AppSecret';

		// 配置参数
		private static function setConfig(){
			if(!self::$key){
				self::$key = 'lasdkewewm';
			}
		}

		/**
		 * 加密token
		 * @param  [type] $data [description]
		 * @return [type]       [description]
		 */
		public static function encodeToken($data){
			self::setConfig();
			return JWT::encode($data, self::$key, self::$type);
		}

		/**
		 * 解密token
		 * @param  [type] $token [description]
		 * @return [type]       [description]
		 */
		public static function decodeToken($token){
			self::setConfig();
			return JWT::decode($token, new Key(self::$key, self::$type));
		}

		/**
		 * 验证token
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public static function checkToken(Request $request){
			self::setConfig();

			$authorization = trim($request->header(self::$token_name,''));
			$visitor = trim($request->header(self::$visitor_name,''));
			$appkey = trim($request->header(self::$key_name,''));
			$appsecret = trim($request->header(self::$secret_name,''));

			if(!$authorization || (!$appkey && !$appSecret)){
				return 100007;
			}

			// api模式
			if(!empty($appkey) && !empty($appsecret)){

			}

			// 常规模式
			if(!$authorization || $authorization == '' || strlen($authorization) < 32){
				return 100011;		// token不能为空
			}

			try{
				$res = self::decodeToken($authorization);
				if(!property_exists($res,'uid') || !property_exists($res,'token') || !property_exists($res,'pid') || !property_exists($res,'rid') || !property_exists($res,'sign') || !property_exists($res,'expire')){
				    return 100012;		// 无效token
				}

				if($res->expire <= time()){
					return 100014;		// token过期
				}
				$uid = $request->input('uid',0);
				if($uid && $res->uid != $uid){
					return 100013;		// token无效 
				}
				$uid = $uid ?: $res->uid;

				return self::getUserInfo($res);
			}catch(\Exception $e){
				return 100015;
			}
		}

		
		public static function getTokenData(Request $request,$key = ''){
			self::setConfig();

			$authorization = trim($request->header(self::$token_name,''));
			$res = self::decodeToken($authorization);
			if(empty($key) || !$key){
				return $res;
			}

			return isset($res->$key) ? $res->$key : '';
		}

		// 检查用户信息
		private static function getUserInfo($user){
			try{
				$field = [
					"AccountID as uid",
					"Token as token",
					"Sign as sign",
					"RoleID as role_id",
					"PID as pid",
					"IsSuper as is_super",
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
	}
?>