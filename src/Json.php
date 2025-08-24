<?php
	namespace dackou;

	use support\Response;

	class Json{
		public static function show($code = 1,$msg = ''){
			if($msg){
				if(is_object($msg) || is_array($msg)){
					return self::json(['code' => $code, 'msg' => 'success', 'data' => $msg]);
				}
				return self::json(['code' => $code, 'msg' => $msg]);
			}

			if(is_object($code) || is_array($code)){
				if((is_array($code) && isset($code['code'])) || (is_object($code) && property_exists($code, 'code'))){
					return self::json($code);
				}
				return self::json(['code' => 0, 'msg' => 'success', 'data' => $code]);
			}

			if(is_bool($code)){
				return self::json($code ? ['code' => 0,'msg' => 'success'] : ['code' => 1,'msg' => 'fail']);
			}

			if(is_numeric($code)){
				return self::json(['code' => $code,'msg' => Lang::get($code)]);
			}

			if(is_string($code)){
				return self::json(['code' => 1,'msg' => $code]);
			}

			if(is_null($code) || empty($code) || $code === 0){
				return self::json(['code' => 0,'msg' => 'success']);
			}

			return self::json(['code' => 1,'msg' => $code]);
		}

		/**
		 * Json response
		 * @param $data
		 * @param int $options
		 * @return Response
		 */
		private static function json($data, int $options = JSON_UNESCAPED_UNICODE): Response
		{
		    return new Response(200, ['Content-Type' => 'application/json'], json_encode($data, $options));
		}
	}
?>