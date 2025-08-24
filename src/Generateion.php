<?php
	namespace dackou;

	class Generateion{
		private static $model = '\\app\\model\\User\\UserModel';


		public static function create($len = 8){
			if(!class_exists(self::$model)){
				self::$model = '\\dackou\\model\\User\\UserModel';
			}

			$uuid   = self::createUuid();
			$uid    = self::createAccount($uuid,$len);
			$token  = self::createToken($uuid);
			$idcard = self::createIdcard();
			$invite = self::createInviteCode();

			return ['uuid'=>$uuid,'uid'=>$uid,'token'=>$token,'idcard'=>$idcard,'invite'=>$invite];
		}

		/**
		 * 生成uuid
		 * @return [type] [description]
		 */
		public static function createUuid(){
			$chars = md5(uniqid(mt_rand(), true));
	        $uuid = substr ( $chars, 0, 8 ) . '-'
	            	. substr ( $chars, 8, 4 ) . '-'
	            	. substr ( $chars, 12, 4 ) . '-'
	            	. substr ( $chars, 16, 4 ) . '-'
	            	. substr ( $chars, 20, 12 );

   			if(self::checkExists('Uuid',$uuid)){
   				$uuid = self::createUuid();
   			}

	        return $uuid ;
		}

		/**
		 * 生成accountid
		 * @param  [type]  $uuid   [description]
		 * @param  integer $length [description]
		 * @return [type]          [description]
		 */
		public static function createAccount($uuid,$length = 8){
			$arr = [str_replace("-","",$uuid)];
   			for($i=0;$i<100;$i++){
   				array_push($arr,md5(uniqid(md5(microtime(true)),true)));
   			}
   			
   			$temp = [];
   			foreach($arr as $k => $v){
   				if($v > 1){
   					$ten = '';
   					for($i=1;$i<=strlen($v);$i++){
   						$char = substr($v,0-$i,1);//反向获取单个字符
   						$ten .= is_numeric($char) ? $char : ord($char);
   					}

   					array_push($temp,$ten);
   				}
   			}
        	
   			$str = str_replace(0,mt_rand(1,9),$temp[0]);
   			$temp_val = $temp[array_rand($temp)];
   			$temp_str = str_shuffle($str);
   			$num = $temp_str[mt_rand(0,strlen($str)-1)];
   			for($i=0;$i<$length-1;$i++){
	   			$num .= $temp_val[mt_rand(0,strlen($temp_val)-1)];
   			}

   			if(self::checkExists('AccountID',$num)){
   				$num = self::createAccount($uuid,$length);
   			}

   			return $num;
		}

		/**
		 * 生成token
		 * @param  [type] $uuid [description]
		 * @return [type]       [description]
		 */
		public static function createToken($uuid){
			$token = self::generateToken($uuid);

			if(self::checkExists('Token',$token)){
				$token = self::createToken($uuid);
			}

			return $token;
		}

		/**
		 * 生成身份证号
		 * @return [type] [description]
		 */
		public static function createIdcard(){
			$idcard = '';
			$area = [11,12,13,14,15,21,22,23,31,32,33,34,35,36,37,41,42,43,44,45,46,50,51,52,53,54,61,62,63,64,65,71,81,82];
			$year = [1970,2000];
			$month = [1,12];
			$day = [1,30];

			$idcard .= $area[\mt_rand(0,count($area)-1)];
			$idcard .= \str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
			$idcard .= \mt_rand($year[0],$year[1]);
			$m = \mt_rand($month[0],$month[1]);
			$d = \mt_rand($day[0],$day[1]);
			$idcard .= $m > 9 ? $m : '0'.$m;
			$idcard .= $d > 9 ? $d : '0'.$d;
			$idcard .= \str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

			return $idcard;
		}

		/**
		 * 生成邀请码
		 * @param  integer $len       [description]
		 * @param  integer $type      [description]
		 * @param  boolean $is_supper [description]
		 * @return [type]             [description]
		 */
		public static function createInviteCode($len = 8,$type = 3,$is_supper = true){
			$invite = self::createCode($len,$type,$is_supper);

			if(self::checkExists('InviteCode',$invite)){
				$invite = self::createInviteCode($len,$type,$is_supper);
			}

			return strtoupper($invite);
		}

		// 生成token
		private static function generateToken($str = ''){
			$hashids = new \Hashids\Hashids();
			return $hashids->encodeHex(str_replace("-","",$str));
		}

		/**
         * 创建随机数
         * @param  integer $num [description]
         * @return [type]       [description]
         */
        public static function createCode($len = 8,$type = 1,$is_supper = false){
            switch($type){
                case '':
                case 1:
                    $str = '123456789';
                    break;
                case 2:
                    $str = $is_supper ? 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ' : 'abcdefghijklmnopqrstuvwxyz';
                    break;
                case 3:
                    $str = $is_supper ? 'abcdefghijklmnpqrstuvwxyz123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ' : 'abcdefghijklmnopqrstuvwxyz123456789';
                    break;
                default:
                    $str = '123456789';
            }

            $str = str_shuffle($str);
            $str = str_shuffle($str);
            $str = str_shuffle($str);
            $str = str_shuffle($str);

            $num = '';
            for($i=0;$i<$len;$i++){
                if($i==0){
                    $str = str_shuffle($str);
                    $num .= $str[mt_rand(0,strlen($str)-1)];
                }else{
                    if($type != 2){
                        $str .= '0';
                    }
                    $str = str_shuffle($str);
                    $num .= $str[mt_rand(0,strlen($str)-1)];
                }
            }
            
            return $num;
        }

		// 检验是否已存在
		private static function checkExists($key,$val){
			try{
				if(class_exists(self::$model)){
					$model = self::$model;
				}else{
					$model = '\\dackou\\model\\User\\UserModel';
				}
				$user = $model::where($key,'=',$val)->limit(1)->first();
				// var_dump($user);
				return $user && is_object($user) ? true : false;
			}catch(\Exception $e){
				return true;
			}
		}
	}
?>