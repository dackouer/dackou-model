<?php
	namespace dackou;

	class Server{
		private static $preg_ip = "/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/";
		private static $preg_mobile = "/^(13[0-9]|14[01456879]|15[0-35-9]|16[2567]|17[0-8]|18[0-9]|19[0-35-9])\d{8}$/";
		private static $preg_phone = "/^(\(?0[0-9]{2,3}\)?-?[0-9]{7,8})$/";
		private static $preg_email = "/^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/";
		private static $preg_url = "/^[1-9]\d{5}(18|19|([23]\d))\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx]$/";
		private static $preg_qq = "/^[1-9][0-9]{4,10}$/";
		private static $preg_rgb = "/^#?([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/";
		private static $preg_weixin = "/^[a-zA-Z]([-_a-zA-Z0-9]{5,19})+$/";
		private static $preg_wechat = "/^[a-zA-Z]([-_a-zA-Z0-9]{5,19})+$/";
		private static $preg_car = "/^[京津沪渝冀豫云辽黑湘皖鲁新苏浙赣鄂桂甘晋蒙陕吉闽贵粤青藏川宁琼使领A-Z]{1}[A-Z]{1}[A-Z0-9]{4}[A-Z0-9挂学警港澳]{1}$/";
		private static $preg_chinese = "/^[\x{4e00}-\x{9fa5}\x{3000}-\x{303f}\x{ff00}-\x{ffef}]+$/u";

		/**
		 * 判断用户名是否合法
		 * @param  [type]  $name [description]
		 * @param  integer $type [description]
		 * @param  integer $min  [description]
		 * @param  integer $max  [description]
		 * @return boolean       [description]
		 */
		public static function isUsername($name,$type = 1,$min = 5,$max = 32){
			if(is_null($name) || !is_string($name) || trim($name) == ''){
				return 'username cannot be empty';
			}

			$name = trim($name);
			$preg_user = [
				"",
				"/^[a-zA-Z0-9_.]{".$min.",".$max."}$/",
				"/^.*(?=.{".$min.",".$max."})(?=.*\d)(?=.*[A-Z])(?=.*[a-z])(?=.*[!@#$%^&*? ]).*$/",
			];
			$preg = isset($preg_user[$type]) ? $preg_user[$type] : $preg_user[1];

			return preg_match($preg,$name) ? true : false;
		}

		public static function isPassword($pass,$type = 1,$min = 6,$max = 32){
			if(is_null($pass) || !is_string($pass) || trim($pass) == ''){
				return 'password cannot be empty';
			}

			$pass = trim($pass);
			$preg_pass = [
				// 无规则
				"",
				// $min-$max个字符，至少1个大写字母，1个小写字母，1个数字和1个特殊字符
				"/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?.&-_+])[A-Za-z\d$@$!%*?.&-_+]{".$min.",".$max."}/",
				// $min-$max个字符（字母，数字，下划线，减号）
				"/^[a-zA-Z0-9_.]{".$min.",".$max."}$/",
				// $min-$max个字符，至少1个大写字母，1个小写字母和1个数字，其他可以是任意字符
				"/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[^]{".$min.",".$max."}$/",
				// $min-$max个字符，至少1个大写字母，1个小写字母和1个数字,不能包含特殊字符（非数字字母）
				"/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{".$min.",".$max."}$/",
				// $min-$max个字符，至少1个字母，1个数字和1个特殊字符
				"/^(?=.*[A-Za-z])(?=.*\d)(?=.*[$@$!%*#?&])[A-Za-z\d$@$!%*#?&]{".$min.",".$max."}$/",
				// $min-$max个字符，至少1个大写字母，1个小写字母和1个数字
				"/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{".$min.",".$max."}$/",
			];
			$preg = isset($preg_pass[$type]) ? $preg_pass[$type] : $preg_pass[1];

			return preg_match($preg,$pass) ? true : false;
		}

		/**
		 * 判断是否电话号码
		 * @param  [type]  $telphone [description]
		 * @return boolean           [description]
		 */
		public static function isTelphone($telphone){
			return preg_match(self::$preg_phone,$telphone) ? true : false;
		}

		/**
		 * 判断是否手机号码
		 * @param  [type]  $mobile [description]
		 * @return boolean         [description]
		 */
		public static function isMobile($mobile){
			return preg_match(self::$preg_mobile,$mobile) ? true : false;
		}

		/**
		 * 判断是否邮箱地址
		 * @param  [type]  $email [description]
		 * @return boolean         [description]
		 */
		public static function isEmail($email){
			return preg_match(self::$preg_email,$email) ? true : false;
		}

		/**
		 * 判断是否身份证号码
		 * @param  [type]  $idcard [description]
		 * @return boolean         [description]
		 */
		public static function isIdcard($idcard){
			$city = array(
				array('id'=>11,'title'=>'北京'),
				array('id'=>12,'title'=>'天津'),
				array('id'=>13,'title'=>'河北'),
				array('id'=>14,'title'=>'山西'),
				array('id'=>15,'title'=>'内蒙古'),
				array('id'=>21,'title'=>'辽宁'),
				array('id'=>22,'title'=>'吉林'),
				array('id'=>23,'title'=>'黑龙江'),
				array('id'=>31,'title'=>'上海'),
				array('id'=>32,'title'=>'江苏'),
				array('id'=>33,'title'=>'浙江'),
				array('id'=>34,'title'=>'安徽'),
				array('id'=>35,'title'=>'福建'),
				array('id'=>36,'title'=>'江西'),
				array('id'=>37,'title'=>'山东'),
				array('id'=>41,'title'=>'河南'),
				array('id'=>42,'title'=>'湖北'),
				array('id'=>43,'title'=>'湖南'),
				array('id'=>44,'title'=>'广东'),
				array('id'=>45,'title'=>'广西'),
				array('id'=>46,'title'=>'海南'),
				array('id'=>50,'title'=>'重庆'),
				array('id'=>51,'title'=>'四川'),
				array('id'=>52,'title'=>'贵州'),
				array('id'=>53,'title'=>'云南'),
				array('id'=>54,'title'=>'西藏'),
				array('id'=>61,'title'=>'陕西'),
				array('id'=>62,'title'=>'甘肃'),
				array('id'=>63,'title'=>'青海'),
				array('id'=>64,'title'=>'宁夏'),
				array('id'=>65,'title'=>'新疆'),
				array('id'=>71,'title'=>'台湾'),
				array('id'=>81,'title'=>'香港'),
				array('id'=>82,'title'=>'澳门'),
				array('id'=>91,'title'=>'国外')
			);

			$preg = "/^[1-9]\d{5}(18|19|([23]\d))\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx]$/";
			if(!preg_match($preg,$idcard)){
				return false;
			}
			$preg = "/^\d{17}(\d|x)$/i";
			if(!preg_match($preg,$idcard)){
				return false;
			}
			$province = array_column($city,'id');
			if(!in_array(substr($idcard,0,2),$province)){
				return false;
			}
			// var_dump($city);
			$birth = substr($idcard,6,4).'-'.substr($idcard,10,2).'-'.substr($idcard,12,2);
			// var_dump($birth);
			$preg = "/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/";
			if(!preg_match($preg,$birth,$parts)){
				// var_dump($parts);
				//检测是否为日期
		        if(!checkdate($parts[2],$parts[3],$parts[1])){
		            return false;
		        }
			}
			return true;
		}

		public static function isBirth($date){
		    //匹配日期格式
		    if (preg_match ("/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/", $date, $parts)){
		        //检测是否为日期
		        if(checkdate($parts[2],$parts[3],$parts[1])){
		            return true;
		        }else{
		        	return false;
		        }
		    }else{
		        return false;
		    }
		}

		/**
		 * 判断是否IP地址
		 * @param  [type]  $ip [description]
		 * @return boolean     [description]
		 */
		public static function isIp($ip){
			return preg_match(self::$preg_ip,$ip) ? true : false;
		}

		/**
		 * 判断是否url
		 * @param  [type]  $url [description]
		 * @return boolean      [description]
		 */
		public static function isUrl($url){
			return preg_match(self::$preg_url,$url) ? true : false;
		}

		/**
		 * 判断是否RGB值
		 * @param  [type]  $rgb [description]
		 * @return boolean      [description]
		 */
		public static function isRgb($rgb){
			return preg_match(self::$preg_rgb,$rgb) ? true : false;
		}

		/**
		 * Date匹配
		 * @param  [type]  $str [description]
		 * @return boolean      [description]
		 */
		public static function isDate($str){
			if(date('Y-m-d H:i:s',strtotime($str)) == $str){
				return true;
			}
			return false;
		}

		/**
		 * Qq匹配
		 * @param  [type]  $str [description]
		 * @return boolean      [description]
		 */
		public static function isQq($str){
			return preg_match(self::$preg_qq,$str) ? true : false;
		}

		/**
		 * 微信Weixin匹配
		 * @param  [type]  $str [description]
		 * @return boolean      [description]
		 */
		public static function isWeixin($str){
			return preg_match(self::$preg_weixin,$str) ? true : false;
		}

		/**
		 * 微信Wechat匹配
		 * @param  [type]  $str [description]
		 * @return boolean      [description]
		 */
		public static function isWechat($str){
			return preg_match(self::$preg_wechat,$str) ? true : false;
		}

		/**
		 * 车牌号码匹配
		 * @param  [type]  $str [description]
		 * @return boolean      [description]
		 */
		public static function isLicenseplate($plateNumber){
			$plateTypes = [
		        'ordinary' 		 => '普通车牌',
		        'new_energy' 	 => '新能源车牌',
		        'armed_police' 	 => '武警车牌',
		        'military' 		 => '军车车牌',
		        'embassy' 		 => '使馆车牌',
		        'consulate' 	 => '领事馆车牌',
		        'hongkong_macao' => '港澳入境车牌'
		    ];
		    $plate = strtoupper(trim($plateNumber));

		    $result = [
		        'valid' 	=> false,
		        'type' 		=> '',
		        'errors' 	=> '',
		        'formatted' => $plate
		    ];

		    // 基本长度检查
		    $length = mb_strlen($plate, 'UTF-8');
		    if($length < 5 || $length > 8){
		        $result['errors'] = "车牌号码长度应在5-8位之间";
        		return $result;
		    }

		    // 检查各类型车牌
		    $platePreg = [
		        'ordinary' 		 => '/^([京津沪渝冀豫云辽黑湘皖鲁新苏浙赣鄂桂甘晋蒙陕吉闽贵粤青藏川宁琼])([A-Z])([A-Z0-9]{4,5})([A-Z0-9挂学警港澳]{0,2})$/u',
		        'new_energy' 	 => '/^([京津沪渝冀豫云辽黑湘皖鲁新苏浙赣鄂桂甘晋蒙陕吉闽贵粤青藏川宁琼])([A-Z])([DF])([A-Z0-9]{5})$/u',
		        'armed_police' 	 => '/^WJ([0-9]{2})([0-9A-Z])([0-9]{4})$/',
		        'military' 		 => '/^([A-Z]{2})([A-Z])([0-9]{4})$/',
		        'embassy' 		 => '/^使([0-9]{3})([0-9]{3})$/u',
		        'consulate' 	 => '/^领([0-9]{3})([0-9]{3})$/u',
		        'hongkong_macao' => '/^粤Z([A-Z0-9]{4})([港澳])$/u'
		    ];

		    foreach ($platePreg as $type => $pattern) {
		        if(preg_match($pattern, $plate, $matches)){
		            $result['valid'] = true;
		            $result['type'] = $plateTypes[$type];
		            $result['details'] = $matches;
		            break;
		        }
		    }

		    if (!$result['valid']) {
		        $result['errors'] = "车牌号码格式不正确";
		        
		        // 提供更具体的错误信息
		        if (!preg_match('/^[京津沪渝冀豫云辽黑湘皖鲁新苏浙赣鄂桂甘晋蒙陕吉闽贵粤青藏川宁琼]/u', $plate)) {
		            $result['errors'] = "必须以正确的省份简称开头";
		        }
		        
		        if (preg_match('/[IO]/', $plate)) {
		            $result['errors'] = "不能包含字母I或O";
		        }
		    }
		    
		    return $result;


			// return preg_match(self::$preg_car,$str) ? true : false;
		}

		/**
		 * 中文匹配
		 * @param  [type]  $str  [description]
		 * @param  integer $type [description]
		 * @return boolean       [description]
		 */
		public static function isChinese($str){
			return preg_match(self::$preg_chinese,$str) ? true : false;
		}

		/**
		 * 获取Ip地址
		 * @param  [type] $ip [description]
		 * @return [type]     [description]
		 */
		public static function getIp($ip){
			if(is_null($ip) || !$ip || !is_string($ip) || trim($ip) == ''){
				return 'Invalid ip address';
			}
			if($ip == '127.0.0.1'){
				return '本机地址';
			}

			if(!preg_match(self::$preg_ip,$ip)){
				return 'Invalid ip address';
			}

			$class_name = '\Linqiao\Addtran\IpRegion';
			if(!self::checkClass($class_name)){
				return 'Invalid cass name,please run: composer require "linqiao/php-addtran:^0.1"';
			}
			$service = new $class_name();
			$result = $service->setIp($ip)->getIpAddress();

			// $result = "中国 湖南省 株洲市";

			return $result;
		}

		/**
		 * 获取经纬度
		 * @param  [type] $address [description]
		 * @return [type]          [description]
		 */
		public static function getLatitude($address){
			try{
				if(!$address || !is_string($address)){
					return '无效的地址';
				}

				$key = '';
				$config = config('plugin.tinywan.weather.app');
				if(isset($config['weather']['key']) && $config['weather']['key']){
					$key = $config['weather']['key'];
				}

				if(!$key){
					return ['code'=>1,'msg'=>'无效的参数'];
				}
				$url = "https://restapi.amap.com/v3/geocode/geo?address={$address}&key={$key}";

				$service = new \Curl\Curl();
				$curl = $service->get($url);
				if($curl->isSuccess()){
					$result = json_decode($curl->response);
					if($result->status === '1' && $result->info === 'OK'){
						if($result->geocodes && $result->geocodes[0]->location){
							$location = explode(',',$result->geocodes[0]->location);
							return [
								'latitude'  => $location[1],
								'longitude' => $location[0]
							];
						}
						return ['latitude' => 0,'longitude' => 0];
					}
					return $result;
				}

				return ['code' => 1,'msg'=>'接口错误'];
			}catch(\Exception $e){
				return [
					'code' => $e->getCode() ?? 1,
					'msg'  => $e->getMessage()
				];
			}
		}

		public static function getRouteDistance($origin, $destination, $type = '1'){
			try{
				$key = '';
				$config = config('plugin.tinywan.weather.app');
				if(isset($config['weather']['key']) && $config['weather']['key']){
					$key = $config['weather']['key'];
				}

				if(!$key){
					return ['code'=>1,'msg'=>'无效的参数'];
				}

				$url = "https://restapi.amap.com/v3/distance";
				// 构建请求参数
			    $params = [
			        'key' => $key,
			        'origins' => $origin,
			        'destination' => $destination,
			        'type' => $type
			    ];
			    
			    // 拼接完整的请求 URL
			    $requestUrl = $url . '?' . http_build_query($params);
			    
			    // 发送 HTTP 请求
			    $response = file_get_contents($requestUrl);
			    
			    // 解析返回的 JSON 数据
			    $data = json_decode($response, true);

			    // 检查请求是否成功
		        if ($data['status'] == '1') {
		            // 获取第一个结果（因为我们只传了一个起点）
		            $result = $data['results'][0];
		            return [
		                'distance' => $result['distance'], // 距离，单位：米
		                'duration' => $result['duration']  // 时间，单位：秒
		            ];
		        } else {
		            // 请求失败，返回错误信息
		            return [
		                'code' => 1,
		                'msg' => $data['info']
		            ];
		        }
			}catch(\Exception $e){
				return [
					'code' => $e->getCode() ?? 1,
					'msg'  => $e->getMessage()
				];
			}
		}

		/**
		 * 获取运营商
		 * @param  [type] $phone [description]
		 * @return [type]        [description]
		 */
		public static function getCarrier($phone){
			if(is_null($phone) || !$phone || !is_string($phone) || trim($phone) == ''){
				return 'Invalid phone number';
			}
			$phone = trim(str_replace(' ','',$phone));
			if(!preg_match(self::$preg_mobile,$phone) && !preg_match(self::$preg_phone,$phone)){
				return 'Invalid phone number format';
			}

			$class_name = '\Linqiao\Addtran\PhoneRegion';
			if(!self::checkClass($class_name)){
				return 'Invalid cass name,please run: composer require "linqiao/php-addtran:^0.1"';
			}
			$service = new $class_name();
			$result = $service->setPhone($phone)->getRegion();

			// $result = [
			// 	'tel_address' => '湖南 邵阳 电信',
			// 	'province'	  => '湖南',
			// 	'city'		  => '邵阳',
			// 	'sp'		  => '电信',
			// ];

			return $result;
		}

		/**
		 * 获取银行卡信息
		 * @param  [type] $card [description]
		 * @return [type]       [description]
		 */
		public static function getBank($card){
			if(is_null($card) || !$card){
				return '';
			}

			$class_name = '\Linqiao\Addtran\BankCardInfo';
			if(!self::checkClass($class_name)){
				return 'Invalid cass name,please run: composer require "linqiao/php-addtran:^0.1"';
			}
			$service = new $class_name();
			return $service->setCartId($card)->getBankCardInfo();
		}

		private static function checkClass($class){
			return class_exists($class);
		}
	}
?>