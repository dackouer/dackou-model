<?php
	namespace dackou\controller\Service;

	use support\Request;
	use dackou\Json;
	use dackou\service\Socialite\SocialiteService;

	class Socialite{
		/**
		 * [qq description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function qq(Request $request){
			$redirect = SocialiteService::create($request,'qq');
        	return redirect($redirect);
		}

		/**
		 * [wechat description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function wechat(Request $request){
			$redirect = SocialiteService::create($request,'wechat','img');
        	return Json::show($redirect);
		}
		
		/**
		 * [weibo description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function weibo(Request $request){
			$redirect = SocialiteService::create($request,'weibo');
        	return redirect($redirect);
		}
		
		/**
		 * [taobao description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function taobao(Request $request){
			$redirect = SocialiteService::create($request,'taobao');
        	return redirect($redirect);
		}
		
		/**
		 * [alipay description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function alipay(Request $request){
			$redirect = SocialiteService::create($request,'alipay');
        	return redirect($redirect);
		}
		
		/**
		 * [coding description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function coding(Request $request){
			$redirect = SocialiteService::create($request,'coding');
        	return redirect($redirect);
		}
		
		/**
		 * [dingtalk description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function dingtalk(Request $request){
			$redirect = SocialiteService::create($request,'dingtalk');
        	return redirect($redirect);
		}
		
		/**
		 * [baidu description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function baidu(Request $request){
			$redirect = SocialiteService::create($request,'baidu');
        	return redirect($redirect);
		}
		
		/**
		 * [azure description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function azure(Request $request){
			$redirect = SocialiteService::create($request,'azure');
        	return redirect($redirect);
		}
		
		/**
		 * [douban description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function douban(Request $request){
			$redirect = SocialiteService::create($request,'douban');
        	return redirect($redirect);
		}
		
		/**
		 * [douyin description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function douyin(Request $request){
			$redirect = SocialiteService::create($request,'douyin');
        	return redirect($redirect);
		}
		
		/**
		 * [facebook description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function facebook(Request $request){
			$redirect = SocialiteService::create($request,'facebook');
        	return redirect($redirect);
		}
		
		/**
		 * [feishu description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function feishu(Request $request){
			$redirect = SocialiteService::create($request,'feishu');
        	return redirect($redirect);
		}
		
		/**
		 * [figma description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function figma(Request $request){
			$redirect = SocialiteService::create($request,'figma');
        	return redirect($redirect);
		}
		
		/**
		 * [gitee description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function gitee(Request $request){
			$redirect = SocialiteService::create($request,'gitee');
        	return redirect($redirect);
		}
		
		/**
		 * [github description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function github(Request $request){
			$redirect = SocialiteService::create($request,'github');
        	return redirect($redirect);
		}
		
		/**
		 * [google description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function google(Request $request){
			$redirect = SocialiteService::create($request,'google');
        	return redirect($redirect);
		}
		
		/**
		 * [lark description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function lark(Request $request){
			$redirect = SocialiteService::create($request,'lark');
        	return redirect($redirect);
		}
		
		/**
		 * [line description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function line(Request $request){
			$redirect = SocialiteService::create($request,'line');
        	return redirect($redirect);
		}
		
		/**
		 * [linkedin description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function linkedin(Request $request){
			$redirect = SocialiteService::create($request,'linkedin');
        	return redirect($redirect);
		}
		
		/**
		 * [openwework description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function openwework(Request $request){
			$redirect = SocialiteService::create($request,'openwework');
        	return redirect($redirect);
		}
		
		/**
		 * [outlook description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function outlook(Request $request){
			$redirect = SocialiteService::create($request,'outlook');
        	return redirect($redirect);
		}
		
		/**
		 * [qcloud description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function qcloud(Request $request){
			$redirect = SocialiteService::create($request,'qcloud');
        	return redirect($redirect);
		}
		
		/**
		 * [tapd description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function tapd(Request $request){
			$redirect = SocialiteService::create($request,'tapd');
        	return redirect($redirect);
		}
		
		/**
		 * [toutiao description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function toutiao(Request $request){
			$redirect = SocialiteService::create($request,'toutiao');
        	return redirect($redirect);
		}
		
		/**
		 * [wework description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function wework(Request $request){
			$redirect = SocialiteService::create($request,'wework');
        	return redirect($redirect);
		}
		
		/**
		 * [xigua description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function xigua(Request $request){
			$redirect = SocialiteService::create($request,'xigua');
        	return redirect($redirect);
		}
	}
?>