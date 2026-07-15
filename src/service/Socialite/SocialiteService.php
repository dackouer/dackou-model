<?php
	namespace dackou\service\Socialite;

	use support\Request;
	use dackou\service\Qrcode\QrcodeService;
	use Shopwwi\WebmanSocialite\Facade\Socialite;
	
	class SocialiteService{
		/**
		 * [create description]
		 * @param  Request $request [description]
		 * @param  string  $mode    [description]
		 * @param  string  $type    [description]
		 * @return [type]           [description]
		 */
		public static function create(Request $request,$mode = '',$type = ''){
			if(!$mode){
				return '';
			}

			$redirect = Socialite::driver($mode)->redirect();
			if($type && $type == 'img'){
				$service = new QrcodeService();
				$qrcode = $service->create($request,['content' => $redirect]);

				return ['qrcode' => $qrcode->getDataUri()];
			}

			return $redirect;

		}

		/**
		 * [callback description]
		 * @param  Request  $request [description]
		 * @param  string   $mode    [description]
		 * @return function          [description]
		 */
		public static function callback(Request $request,$mode = ''){
			$code = $request->input('code');
			$obj = Socialite::driver($mode)->userFromCode($code);

			$data = [
				'openid_'.$mode 		=> $obj->id,
				// 'name' 				=> $obj->name,
				'nickname' 				=> $obj->nickname,
				'email' 				=> $obj->email,
				'face' 					=> $obj->avatar,
				'gender'				=> $obj->raw['gender_type'],
				'access_token_'.$mode 	=> $obj->access_token,
				'refresh_token_'.$mode  => $obj->refresh_token,
			];

			// var_dump($obj->raw);
			$result = self::updateUser($request,$data,$mode);

			return redirect('https://admin.gulanfood.com');
		}

		private static function updateUser(Request $request,$data,$mode){
			try{
				$_class_name = '\app\model\User\UserModel';
				if(!class_exists($_class_name)){
					$_class_name = '\dackou\model\User\UserModel';
				}

				$service = new $_class_name();
				return $service->updateSocialite($request,$data,$mode);
			}catch(\Exception $e){
				return [
					'code' => $e->getCode() ?: 1,
					'file' => $e->getFile(),
					'line' => $e->getLine(),
					'msg'  => $e->getMessage()
				];
			}
		}
	}
?>