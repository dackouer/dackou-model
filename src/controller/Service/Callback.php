<?php
	namespace dackou\controller\Service;

	use support\Request;
	use dackou\service\Socialite\SocialiteService;

	class Callback{
		/**
		 * [qq description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function qq(Request $request){
			// $redirect = SocialiteService::qq($request);
			$redirect = SocialiteService::callback($request,'qq');
        	return redirect($redirect);
		}

		/**
		 * [wechat description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function wechat(Request $request){
			// $redirect = SocialiteService::wechat($request);
			$redirect = SocialiteService::callback($request,'wechat');
        	return Json::show($redirect);
		}
		
		/**
		 * [weibo description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function weibo(Request $request){
			// $redirect = SocialiteService::weibo($request);
			$redirect = SocialiteService::callback($request,'weibo');
        	return redirect($redirect);
		}
		
		/**
		 * [taobao description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function taobao(Request $request){
			// $redirect = SocialiteService::taobao($request);
			$redirect = SocialiteService::callback($request,'taobao');
        	return redirect($redirect);
		}
		
		/**
		 * [alipay description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function alipay(Request $request){
			// $redirect = SocialiteService::alipay($request);
			$redirect = SocialiteService::callback($request,'alipay');
        	return redirect($redirect);
		}
		
		/**
		 * [coding description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function coding(Request $request){
			// $redirect = SocialiteService::coding($request);
			$redirect = SocialiteService::callback($request,'coding');
        	return redirect($redirect);
		}
		
		/**
		 * [dingtalk description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function dingtalk(Request $request){
			// $redirect = SocialiteService::dingtalk($request);
			$redirect = SocialiteService::callback($request,'dingtalk');
        	return redirect($redirect);
		}
		
		/**
		 * [baidu description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function baidu(Request $request){
			// $redirect = SocialiteService::baidu($request);
			$redirect = SocialiteService::callback($request,'baidu');
        	return redirect($redirect);
		}
		
		/**
		 * [azure description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function azure(Request $request){
			// $redirect = SocialiteService::azure($request);
			$redirect = SocialiteService::callback($request,'azure');
        	return redirect($redirect);
		}
		
		/**
		 * [douban description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function douban(Request $request){
			// $redirect = SocialiteService::douban($request);
			$redirect = SocialiteService::callback($request,'douban');
        	return redirect($redirect);
		}
		
		/**
		 * [douyin description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function douyin(Request $request){
			// $redirect = SocialiteService::douyin($request);
			$redirect = SocialiteService::callback($request,'douyin');
        	return redirect($redirect);
		}
		
		/**
		 * [facebook description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function facebook(Request $request){
			// $redirect = SocialiteService::facebook($request);
			$redirect = SocialiteService::callback($request,'facebook');
        	return redirect($redirect);
		}
		
		/**
		 * [feishu description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function feishu(Request $request){
			// $redirect = SocialiteService::feishu($request);
			$redirect = SocialiteService::callback($request,'feishu');
        	return redirect($redirect);
		}
		
		/**
		 * [figma description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function figma(Request $request){
			// $redirect = SocialiteService::figma($request);
			$redirect = SocialiteService::callback($request,'figma');
        	return redirect($redirect);
		}
		
		/**
		 * [gitee description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function gitee(Request $request){
			// $redirect = SocialiteService::gitee($request);
			$redirect = SocialiteService::callback($request,'gitee');
        	return redirect($redirect);
		}
		
		/**
		 * [github description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function github(Request $request){
			// $redirect = SocialiteService::github($request);
			$redirect = SocialiteService::callback($request,'github');
        	return redirect($redirect);
		}
		
		/**
		 * [google description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function google(Request $request){
			// $redirect = SocialiteService::google($request);
			$redirect = SocialiteService::callback($request,'google');
        	return redirect($redirect);
		}
		
		/**
		 * [lark description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function lark(Request $request){
			// $redirect = SocialiteService::lark($request);
			$redirect = SocialiteService::callback($request,'lark');
        	return redirect($redirect);
		}
		
		/**
		 * [line description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function line(Request $request){
			// $redirect = SocialiteService::line($request);
			$redirect = SocialiteService::callback($request,'line');
        	return redirect($redirect);
		}
		
		/**
		 * [linkedin description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function linkedin(Request $request){
			// $redirect = SocialiteService::linkedin($request);
			$redirect = SocialiteService::callback($request,'linkedin');
        	return redirect($redirect);
		}
		
		/**
		 * [openwework description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function openwework(Request $request){
			// $redirect = SocialiteService::openwework($request);
			$redirect = SocialiteService::callback($request,'openwework');
        	return redirect($redirect);
		}
		
		/**
		 * [outlook description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function outlook(Request $request){
			// $redirect = SocialiteService::outlook($request);
			$redirect = SocialiteService::callback($request,'outlook');
        	return redirect($redirect);
		}
		
		/**
		 * [qcloud description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function qcloud(Request $request){
			// $redirect = SocialiteService::qcloud($request);
			$redirect = SocialiteService::callback($request,'qcloud');
        	return redirect($redirect);
		}
		
		/**
		 * [tapd description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function tapd(Request $request){
			// $redirect = SocialiteService::tapd($request);
			$redirect = SocialiteService::callback($request,'tapd');
        	return redirect($redirect);
		}
		
		/**
		 * [toutiao description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function toutiao(Request $request){
			// $redirect = SocialiteService::toutiao($request);
			$redirect = SocialiteService::callback($request,'toutiao');
        	return redirect($redirect);
		}
		
		/**
		 * [wework description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function wework(Request $request){
			// $redirect = SocialiteService::wework($request);
			$redirect = SocialiteService::callback($request,'wework');
        	return redirect($redirect);
		}
		
		/**
		 * [xigua description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function xigua(Request $request){
			// $redirect = SocialiteService::xigua($request);
			$redirect = SocialiteService::callback($request,'xigua');
        	return redirect($redirect);
		}
	}
?>