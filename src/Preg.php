<?php
	namespace dackou;

	class Preg{

		/**
		 * 用户名匹配
		 * @param  [type]  $str  [description]
		 * @param  integer $type [description]
		 * @param  integer $min  [description]
		 * @param  integer $max  [description]
		 * @return boolean       [description]
		 */
		public static function isUsername($str,$min = 5,$max = 16,$type = 1){
			$preg = '';
			switch($type){
				case '':
				case null:
				case 1:
					// 4到16位（字母，数字，下划线，减号）
					$preg = "/^[a-zA-Z0-9_.]{".$min.",".$max."}$/";
					break;
				case 2:
					// 最少6位，包括至少1个大写字母，1个小写字母，1个数字，1个特殊字符
					$preg = "/^.*(?=.{".$min.",".$max."})(?=.*\d)(?=.*[A-Z])(?=.*[a-z])(?=.*[!@#$%^&*? ]).*$/";
					break;
				default:
			}
			return preg_match($preg,$str) ? true : false;
		}

		/**
		 * 密码匹配
		 * @param  [type]  $str  [description]
		 * @param  integer $type [description]
		 * @param  integer $min  [description]
		 * @param  integer $max  [description]
		 * @return boolean       [description]
		 */
		public static function isPassword($str,$type = 1,$min = 6,$max = 32){
			$preg = '';
			switch($type){
				case '':
				case null:
				case 1:
					// $min-$max个字符，至少1个大写字母，1个小写字母，1个数字和1个特殊字符
					$preg = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?.&-_+])[A-Za-z\d$@$!%*?.&-_+]{".$min.",".$max."}/";
					break;
				case 2:
					// $min-$max个字符（字母，数字，下划线，减号）
					$preg = "/^[a-zA-Z0-9_.]{".$min.",".$max."}$/";
					break;
				case 3:
					// $min-$max个字符，至少1个大写字母，1个小写字母和1个数字，其他可以是任意字符
					$preg = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[^]{".$min.",".$max."}$/";
					break;
				case 4:
					// $min-$max个字符，至少1个大写字母，1个小写字母和1个数字,不能包含特殊字符（非数字字母）
					$preg = "/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{".$min.",".$max."}$/";
					break;
				case 5:
					// $min-$max个字符，至少1个字母，1个数字和1个特殊字符
					$preg = "/^(?=.*[A-Za-z])(?=.*\d)(?=.*[$@$!%*#?&])[A-Za-z\d$@$!%*#?&]{".$min.",".$max."}$/";
					break;
				case 6:
					// $min-$max个字符，至少1个大写字母，1个小写字母和1个数字
					$preg = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{".$min.",".$max."}$/";
					break;
				default:
			}
			return preg_match($preg,$str) ? true : false;
		}

		/**
		 * 整数匹配
		 * @param  [type]  $str  [description]
		 * @param  integer $type [description]
		 * @return boolean       [description]
		 */
		public static function isInt($str,$type = 1){
			$preg = '';
			switch($type){
				case '':
				case null:
				case 1:
					// 正整数
					$preg = "/^\d+$/";
					break;
				case 2:
					// 负整数
					$preg = "/^-\d+$/";
					break;
				case 3:
					// 整数
					$preg = "/^-?\d+$/";
					break;
				default:
			}
			return preg_match($preg,$str) ? true : false;
		}

		/**
		 * 数字匹配
		 * @param  [type]  $str  [description]
		 * @param  integer $type [description]
		 * @return boolean       [description]
		 */
		public static function isFloat($str,$type = 1){
			$preg = '';
			switch($type){
				case '':
				case null:
				case 1:
					// 正数
					$preg = "/^\d*\.?\d+$/";
					break;
				case 2:
					// 负数
					$preg = "/^-\d*\.?\d+$/";
					break;
				case 3:
					// 数字
					$preg = "/^-?\d*\.?\d+$/";
					break;
				default:
			}
			return preg_match($preg,$str) ? true : false;
		}

		/**
		 * 邮件匹配
		 * @param  [type]  $str [description]
		 * @return boolean      [description]
		 */
		public static function isEmail($str){
			$preg = "/^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/";
			return preg_match($preg,$str) ? true : false;
		}

		/**
		 * 座机号码匹配
		 * @param  [type]  $str [description]
		 * @return boolean      [description]
		 */
		public static function isTelphone($str){
			$preg = "/^(\(?0[0-9]{2,3}\)?-?[0-9]{7,8})$/";
			return preg_match($preg,$str) ? true : false;
		}

		/**
		 * 手机号码匹配
		 * @param  [type]  $str [description]
		 * @return boolean      [description]
		 */
		public static function isMobile($str){
			$preg = "/^(13[0-9]|14[01456879]|15[0-35-9]|16[2567]|17[0-8]|18[0-9]|19[0-35-9])\d{8}$/";
			return preg_match($preg,$str) ? true : false;
		}

		/**
		 * 身份证号码匹配
		 * @param  [type]  $str [description]
		 * @return boolean      [description]
		 */
		public static function isIdcard($str){
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
			if(!preg_match($preg,$str)){
				return false;
			}
			$preg = "/^\d{17}(\d|x)$/i";
			if(!preg_match($preg,$str)){
				return false;
			}
			$province = array_column($city,'id');
			if(!in_array(substr($str,0,2),$province)){
				return false;
			}
			// var_dump($city);
			$birth = substr($str,6,4).'-'.substr($str,10,2).'-'.substr($str,12,2);
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

		// 判断身份证号
		// public static function isIdcard($str){

		// 	$iSum=0 ;
		// 	$info="" ;
		// 	if(!/^\d{17}(\d|x)$/i.test(sId)) return "你输入的身份证长度或格式错误";
		// 	sId=sId.replace(/x$/i,"a");
		// 	if(aCity[parseInt(sId.substr(0,2))]==null) return "你的身份证地区非法";
		// 	sBirthday=sId.substr(6,4)+"-"+Number(sId.substr(10,2))+"-"+Number(sId.substr(12,2));
		// 	var d=new Date(sBirthday.replace(/-/g,"/")) ;
		// 	if(sBirthday!=(d.getFullYear()+"-"+ (d.getMonth()+1) + "-" + d.getDate()))return "身份证上的出生日期非法";

		// 	for(var i = 17;i>=0;i --) iSum += (Math.pow(2,i) % 11) * parseInt(sId.charAt(17 - i),11) ;
		// 	if(iSum%11!=1) return "你输入的身份证号非法";
		// 	//aCity[parseInt(sId.substr(0,2))]+","+sBirthday+","+(sId.substr(16,1)%2?"男":"女");//此次还可以判断出输入的身份证号的人性别
		// 	return true;
		// }
		
		/**
		 * URL匹配
		 * @param  [type]  $str [description]
		 * @return boolean      [description]
		 */
		public static function isUrl($str){
			$preg = "/^[1-9]\d{5}(18|19|([23]\d))\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx]$/";
			return preg_match($preg,$str) ? true : false;
		}

		/**
		 * IP匹配
		 * @param  [type]  $str [description]
		 * @return boolean      [description]
		 */
		public static function isIp($str){
			$preg = "/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/";
			return preg_match($preg,$str) ? true : false;
		}

		/**
		 * RGB匹配
		 * @param  [type]  $str [description]
		 * @return boolean      [description]
		 */
		public static function isRgb($str){
			$preg = "/^#?([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/";
			return preg_match($preg,$str) ? true : false;
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

			// $preg = "/^(?:(?!0000)[0-9]{4}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1[0-9]|2[0-8])|(?:0[13-9]|1[0-2])-(?:29|30)|(?:0[13578]|1[02])-31)|(?:[0-9]{2}(?:0[48]|[2468][048]|[13579][26])|(?:0[48]|[2468][048]|[13579][26])00)-02-29)$/";
			// return preg_match($preg,$str) ? true : false;
		}

		/**
		 * Qq匹配
		 * @param  [type]  $str [description]
		 * @return boolean      [description]
		 */
		public static function isQq($str){
			$preg = "/^[1-9][0-9]{4,10}$/";
			return preg_match($preg,$str) ? true : false;
		}

		/**
		 * 微信Weixin匹配
		 * @param  [type]  $str [description]
		 * @return boolean      [description]
		 */
		public static function isWeixin($str){
			$preg = "/^[a-zA-Z]([-_a-zA-Z0-9]{5,19})+$/";
			return preg_match($preg,$str) ? true : false;
		}

		/**
		 * 微信Wechat匹配
		 * @param  [type]  $str [description]
		 * @return boolean      [description]
		 */
		public static function isWechat($str){
			$preg = "/^[a-zA-Z]([-_a-zA-Z0-9]{5,19})+$/";
			return preg_match($preg,$str) ? true : false;
		}

		/**
		 * 车牌号码匹配
		 * @param  [type]  $str [description]
		 * @return boolean      [description]
		 */
		public static function isCar($str){
			$preg = "/^[京津沪渝冀豫云辽黑湘皖鲁新苏浙赣鄂桂甘晋蒙陕吉闽贵粤青藏川宁琼使领A-Z]{1}[A-Z]{1}[A-Z0-9]{4}[A-Z0-9挂学警港澳]{1}$/";
			return preg_match($preg,$str) ? true : false;
		}

		/**
		 * 中文匹配
		 * @param  [type]  $str  [description]
		 * @param  integer $type [description]
		 * @return boolean       [description]
		 */
		public static function isChinese($str,$type = 1){
			$preg = '';
			switch($type){
				case '':
				case null:
				case 1:
					$preg = "/[\u4E00-\u9FA5]/";
					break;
				case 2:
					$preg = "/[\u4E00-\u9FA5]/";
					break;
				default:
			}
			return preg_match($preg,$str) ? true : false;
		}


		/*
			\d : 0-9	一位
			\D : 非0-9	一位
			\w : [0-9a-zA-Z]
			\s : 空字符
			\f : 分页
			\n : 换行
			\t : 制表符
			\S : 非空字符
			\r : 回车
			\v : 垂直制表
			[..] : 条件或
			i : 区分大小写
			s : 
			^ : 取反,不是
			() : 原子组
			| : 或
			<> : 都有
			\1 : 第一次匹配到的内容
			{num} : 只出现num次
			?:  : 不临时缓存
			?#  : 注释
			* 	: 0-多个   $preg = /\w\*\/;
	
			preg_match()
			preg_match_all()
			preg_replace($preg,$str,$arr);

		 */
	}
?>