<?php
	namespace dackou\model\Option;

	use support\Request;
	use support\Db;

	class OptionModel extends \dackou\Model{
		protected $table = 'Option';
    	protected $show_method = 'tree';
    	protected $action_width = 800;
    	protected $truncate = true;
		public $layer = 2;
		public $digit = 2;

		protected function getDefaultData(Request $request){
			$data = [
				['id'=>10,'title'=>'系统设置','key'=>'SYSTEM_OPTION','form_type'=>'','value_type'=>0,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>1,'pid'=>0,'number'=>12,'tag'=>'system','hidden'=>0,'sort'=>1],
				['id'=>11,'title'=>'基础设置','key'=>'BASE_OPTION','form_type'=>'','value_type'=>0,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>1,'pid'=>0,'number'=>1,'tag'=>'base','hidden'=>0,'sort'=>2],
				['id'=>12,'title'=>'隐私政策','key'=>'PRIVACY_OPTION','form_type'=>'','value_type'=>0,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>1,'pid'=>0,'number'=>2,'tag'=>'privacy','hidden'=>0,'sort'=>3],
				['id'=>13,'title'=>'关于我们','key'=>'ABOUT_OPTION','form_type'=>'','value_type'=>0,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>1,'pid'=>0,'number'=>1,'tag'=>'about','hidden'=>0,'sort'=>4],
				['id'=>14,'title'=>'邮件设置','key'=>'EMAIL_OPTION','form_type'=>'','value_type'=>0,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>1,'pid'=>0,'number'=>8,'tag'=>'email','hidden'=>0,'sort'=>5],
				['id'=>15,'title'=>'短信设置','key'=>'SMS_OPTION','form_type'=>'','value_type'=>0,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>1,'pid'=>0,'number'=>18,'tag'=>'sms','hidden'=>0,'sort'=>6],
				['id'=>16,'title'=>'存储设置','key'=>'STORE_OPTION','form_type'=>'','value_type'=>0,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>1,'pid'=>0,'number'=>20,'tag'=>'store','hidden'=>0,'sort'=>7],
				['id'=>17,'title'=>'用户设置','key'=>'USER_OPTION','form_type'=>'','value_type'=>0,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>1,'pid'=>0,'number'=>24,'tag'=>'user','hidden'=>0,'sort'=>8],
				['id'=>18,'title'=>'实名认证','key'=>'AUTH_OPTION','form_type'=>'','value_type'=>0,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>1,'pid'=>0,'number'=>6,'tag'=>'auth','hidden'=>0,'sort'=>9],
				['id'=>19,'title'=>'商品设置','key'=>'GOODS_OPTION','form_type'=>'','value_type'=>0,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>1,'pid'=>0,'number'=>13,'tag'=>'goods','hidden'=>0,'sort'=>10],
				['id'=>20,'title'=>'营销设置','key'=>'MARKET_OPTION','form_type'=>'','value_type'=>0,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>1,'pid'=>0,'number'=>12,'tag'=>'market','hidden'=>0,'sort'=>11],
				['id'=>21,'title'=>'订单设置','key'=>'ORDER_OPTION','form_type'=>'','value_type'=>0,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>1,'pid'=>0,'number'=>22,'tag'=>'order','hidden'=>0,'sort'=>12],
				['id'=>22,'title'=>'快递设置','key'=>'EXPRESS_OPTION','form_type'=>'','value_type'=>0,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>1,'pid'=>0,'number'=>3,'tag'=>'express','hidden'=>0,'sort'=>13],
				['id'=>23,'title'=>'微信设置','key'=>'WECHAT_OPTION','form_type'=>'','value_type'=>0,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>1,'pid'=>0,'number'=>22,'tag'=>'wechat','hidden'=>0,'sort'=>14],
				['id'=>24,'title'=>'登录设置','key'=>'LOGIN_OPTION','form_type'=>'','value_type'=>0,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>1,'pid'=>0,'number'=>47,'tag'=>'login','hidden'=>0,'sort'=>15],
				['id'=>25,'title'=>'支付宝设置','key'=>'ALIPAY_OPTION','form_type'=>'','value_type'=>0,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>1,'pid'=>0,'number'=>5,'tag'=>'alipay','hidden'=>0,'sort'=>16],
				['id'=>26,'title'=>'网站设置','key'=>'WEB_OPTION','form_type'=>'','value_type'=>0,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>1,'pid'=>0,'number'=>1,'tag'=>'web','hidden'=>0,'sort'=>17],
				['id'=>27,'title'=>'小程序设置','key'=>'MINI_OPTION','form_type'=>'','value_type'=>0,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>1,'pid'=>0,'number'=>1,'tag'=>'mini','hidden'=>0,'sort'=>18],
				['id'=>28,'title'=>'APP设置','key'=>'APP_OPTION','form_type'=>'','value_type'=>0,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>1,'pid'=>0,'number'=>1,'tag'=>'app','hidden'=>0,'sort'=>19],
				['id'=>29,'title'=>'网站装修','key'=>'WEB_DECORATION','form_type'=>'','value_type'=>0,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>1,'pid'=>0,'number'=>1,'tag'=>'decoration','hidden'=>0,'sort'=>20],

				['id'=>1001,'title'=>'系统名称','key'=>'system_name','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>10,'number'=>0,'tag'=>'system','hidden'=>0,'sort'=>1],
				['id'=>1002,'title'=>'系统英文名称','key'=>'system_en_name','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>10,'number'=>0,'tag'=>'system','hidden'=>0,'sort'=>2],
				['id'=>1003,'title'=>'系统Logo','key'=>'system_logo','form_type'=>'upload_logo','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>10,'number'=>0,'tag'=>'system','hidden'=>0,'sort'=>3],
				['id'=>1004,'title'=>'主域名','key'=>'system_domain','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>10,'number'=>0,'tag'=>'system','hidden'=>0,'sort'=>4],
				['id'=>1005,'title'=>'多商户','key'=>'system_is_merchant','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>10,'number'=>0,'tag'=>'system','hidden'=>1,'sort'=>5],
				['id'=>1006,'title'=>'ICP备案号','key'=>'system_icp','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>10,'number'=>0,'tag'=>'system','hidden'=>0,'sort'=>6],
				['id'=>1007,'title'=>'ICP链接网址','key'=>'system_icp_link','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'https://beian.miit.gov.cn','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>10,'number'=>0,'tag'=>'system','hidden'=>0,'sort'=>7],
				['id'=>1008,'title'=>'公安备案号','key'=>'system_psr','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>10,'number'=>0,'tag'=>'system','hidden'=>0,'sort'=>8],
				['id'=>1009,'title'=>'公安链接网址','key'=>'system_psr_link','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>10,'number'=>0,'tag'=>'system','hidden'=>0,'sort'=>9],
				['id'=>1010,'title'=>'版权所有','key'=>'system_copyright','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>10,'number'=>0,'tag'=>'system','hidden'=>0,'sort'=>10],
				['id'=>1011,'title'=>'技术支持','key'=>'system_support','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>10,'number'=>0,'tag'=>'system','hidden'=>0,'sort'=>11],
				['id'=>1012,'title'=>'系统状态','key'=>'system_status','form_type'=>'select','value_type'=>2,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'{"正常":1,"关闭":0,"维护":2}','prefix'=>'','suffix'=>'','level'=>2,'pid'=>10,'number'=>0,'tag'=>'system','hidden'=>0,'sort'=>12],
				['id'=>1013,'title'=>'系统消息','key'=>'system_message','form_type'=>'editor','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>10,'number'=>0,'tag'=>'system','hidden'=>0,'sort'=>13],

				['id'=>1101,'title'=>'基础设置','key'=>'base_name','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>11,'number'=>0,'tag'=>'base','hidden'=>0,'sort'=>1],

				['id'=>1201,'title'=>'用户协议','key'=>'user_agreement','form_type'=>'editor','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>12,'number'=>0,'tag'=>'privacy','hidden'=>0,'sort'=>1],
				['id'=>1202,'title'=>'隐私政策','key'=>'privacy_olicy','form_type'=>'editor','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>12,'number'=>0,'tag'=>'privacy','hidden'=>0,'sort'=>2],

				['id'=>1301,'title'=>'关于我们','key'=>'about_us','form_type'=>'editor','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>13,'number'=>0,'tag'=>'about','hidden'=>0,'sort'=>1],

				['id'=>1401,'title'=>'开启邮件','key'=>'email_enable','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>1,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>14,'number'=>0,'tag'=>'email','hidden'=>0,'sort'=>1],
				['id'=>1402,'title'=>'SCHEME','key'=>'email_scheme_type','form_type'=>'radio-group','value_type'=>1,'rows'=>0,'default_value'=>1,'size'=>'default','limit'=>0,'options'=>'{"SMTP":1,"SMTPS":2}','prefix'=>'','suffix'=>'','level'=>2,'pid'=>14,'number'=>0,'tag'=>'email','hidden'=>0,'sort'=>2],
				['id'=>1403,'title'=>'服务器地址','key'=>'email_host','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>14,'number'=>0,'tag'=>'email','hidden'=>0,'sort'=>3],
				['id'=>1404,'title'=>'服务器商品号','key'=>'email_port','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>465,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>14,'number'=>0,'tag'=>'email','hidden'=>0,'sort'=>4],
				['id'=>1405,'title'=>'用户名','key'=>'email_username','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>14,'number'=>0,'tag'=>'email','hidden'=>0,'sort'=>5],
				['id'=>1406,'title'=>'用户密码','key'=>'email_password','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>14,'number'=>0,'tag'=>'email','hidden'=>0,'sort'=>6],
				['id'=>1407,'title'=>'发件人地址','key'=>'email_sender_address','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>14,'number'=>0,'tag'=>'email','hidden'=>0,'sort'=>7],
				['id'=>1408,'title'=>'发件人名称','key'=>'email_sender_name','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>14,'number'=>0,'tag'=>'email','hidden'=>0,'sort'=>8],

				['id'=>1501,'title'=>'开启短信','key'=>'sms_enable','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>1,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>15,'number'=>0,'tag'=>'sms','hidden'=>0,'sort'=>1],
				['id'=>1502,'title'=>'默认短信平台','key'=>'sms_platform','form_type'=>'select','value_type'=>1,'rows'=>0,'default_value'=>'aliyun','size'=>'default','limit'=>0,'options'=>'{"阿里云":"aliyun","腾讯云":"qcloud","七牛云":"qiniu","华为云":"huawei"}','prefix'=>'','suffix'=>'','level'=>2,'pid'=>15,'number'=>0,'tag'=>'sms','hidden'=>0,'sort'=>2],
				['id'=>1503,'title'=>'阿里云access_key','key'=>'sms_aliyun_access_key','form_type'=>'password','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>15,'number'=>0,'tag'=>'sms','hidden'=>0,'sort'=>3],
				['id'=>1504,'title'=>'阿里云access_secret','key'=>'sms_aliyun_access_secret','form_type'=>'password','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>15,'number'=>0,'tag'=>'sms','hidden'=>0,'sort'=>4],
				['id'=>1505,'title'=>'阿里云签名名称','key'=>'sms_aliyun_sign_name','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>15,'number'=>0,'tag'=>'sms','hidden'=>0,'sort'=>5],
				['id'=>1506,'title'=>'阿里云短信模板','key'=>'sms_aliyun_template','form_type'=>'milut_form','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'{"title":"模板标识","actions_name":"模板名称","template_id":"模板CODE"}','prefix'=>'','suffix'=>'','level'=>2,'pid'=>15,'number'=>0,'tag'=>'sms','hidden'=>0,'sort'=>6],
				['id'=>1507,'title'=>'腾讯云appid','key'=>'sms_qcloud_appid','form_type'=>'password','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>15,'number'=>0,'tag'=>'sms','hidden'=>0,'sort'=>7],
				['id'=>1508,'title'=>'腾讯云appkey','key'=>'sms_qcloud_appkey','form_type'=>'password','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>15,'number'=>0,'tag'=>'sms','hidden'=>0,'sort'=>8],
				['id'=>1509,'title'=>'腾讯云签名名称','key'=>'sms_qcloud_sign_name','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>15,'number'=>0,'tag'=>'sms','hidden'=>0,'sort'=>9],
				['id'=>1510,'title'=>'腾讯云短信模板','key'=>'sms_qcloud_template','form_type'=>'milut_form','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'{"title":"模板标识","actions_name":"模板名称","template_id":"模板CODE"}','prefix'=>'','suffix'=>'','level'=>2,'pid'=>15,'number'=>0,'tag'=>'sms','hidden'=>0,'sort'=>10],
				['id'=>1511,'title'=>'七牛云AccessKey','key'=>'sms_qiniu_access_key','form_type'=>'password','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>15,'number'=>0,'tag'=>'sms','hidden'=>0,'sort'=>11],
				['id'=>1512,'title'=>'七牛云SecretKey','key'=>'sms_qiniu_secret_key','form_type'=>'password','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>15,'number'=>0,'tag'=>'sms','hidden'=>0,'sort'=>12],
				['id'=>1513,'title'=>'七牛云短信模板','key'=>'sms_qiniu_template','form_type'=>'milut_form','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'{"title":"模板标识","actions_name":"模板名称","template_id":"模板CODE"}','prefix'=>'','suffix'=>'','level'=>2,'pid'=>15,'number'=>0,'tag'=>'sms','hidden'=>0,'sort'=>13],
				['id'=>1514,'title'=>'华为云appKey','key'=>'sms_huawei_app_key','form_type'=>'password','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>15,'number'=>0,'tag'=>'sms','hidden'=>0,'sort'=>14],
				['id'=>1515,'title'=>'华为云appSecret','key'=>'sms_huawei_app_secret','form_type'=>'password','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>15,'number'=>0,'tag'=>'sms','hidden'=>0,'sort'=>15],
				['id'=>1516,'title'=>'华为云签名名称','key'=>'sms_huawei_sign_name','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>15,'number'=>0,'tag'=>'sms','hidden'=>0,'sort'=>16],
				['id'=>1517,'title'=>'华为云短信模板','key'=>'sms_huawei_template','form_type'=>'milut_form','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'{"title":"模板标识","actions_name":"模板名称","template_id":"模板CODE"}','prefix'=>'','suffix'=>'','level'=>2,'pid'=>15,'number'=>0,'tag'=>'sms','hidden'=>0,'sort'=>17],
				['id'=>1518,'title'=>'开启短信日志','key'=>'sms_is_log','form_type'=>'switch','value_type'=>1,'rows'=>0,'default_value'=>1,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>15,'number'=>0,'tag'=>'sms','hidden'=>0,'sort'=>18],

				['id'=>1601,'title'=>'开启存储','key'=>'store_enable','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>1,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>16,'number'=>0,'tag'=>'store','hidden'=>0,'sort'=>1],
				['id'=>1602,'title'=>'默认存储平台','key'=>'store_platform','form_type'=>'select','value_type'=>1,'rows'=>0,'default_value'=>'qiniu','size'=>'default','limit'=>0,'options'=>'{"本地":"local","七牛云":"qiniu","阿里云":"aliyun","腾讯云":"qcloud"}','prefix'=>'','suffix'=>'','level'=>2,'pid'=>16,'number'=>0,'tag'=>'store','hidden'=>0,'sort'=>2],
				['id'=>1603,'title'=>'本地存储路径','key'=>'store_local_path','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>16,'number'=>0,'tag'=>'store','hidden'=>0,'sort'=>3],
				['id'=>1604,'title'=>'本地访问域名','key'=>'store_local_domain','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>16,'number'=>0,'tag'=>'store','hidden'=>0,'sort'=>4],
				['id'=>1605,'title'=>'本地访问uri','key'=>'store_local_uri','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>16,'number'=>0,'tag'=>'store','hidden'=>0,'sort'=>5],
				['id'=>1606,'title'=>'七牛云accessKey','key'=>'store_qiniu_access_key','form_type'=>'password','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>16,'number'=>0,'tag'=>'store','hidden'=>0,'sort'=>6],
				['id'=>1607,'title'=>'七牛云secretKey','key'=>'store_qiniu_secret_key','form_type'=>'password','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>16,'number'=>0,'tag'=>'store','hidden'=>0,'sort'=>7],
				['id'=>1608,'title'=>'七牛云空间名称','key'=>'store_qiniu_bucket','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>16,'number'=>0,'tag'=>'store','hidden'=>0,'sort'=>8],
				['id'=>1609,'title'=>'七牛云存储子目录','key'=>'store_qiniu_dirname','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>16,'number'=>0,'tag'=>'store','hidden'=>0,'sort'=>9],
				['id'=>1610,'title'=>'七牛云域名','key'=>'store_qiniu_domain','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>16,'number'=>0,'tag'=>'store','hidden'=>0,'sort'=>10],
				['id'=>1611,'title'=>'阿里云accessKeyId','key'=>'store_aliyun_access_key_id','form_type'=>'password','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>16,'number'=>0,'tag'=>'store','hidden'=>0,'sort'=>11],
				['id'=>1612,'title'=>'阿里云accessKeySecret','key'=>'store_aliyun_access_key_secret','form_type'=>'password','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>16,'number'=>0,'tag'=>'store','hidden'=>0,'sort'=>12],
				['id'=>1613,'title'=>'阿里云空间名称','key'=>'store_aliyun_bucket','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>16,'number'=>0,'tag'=>'store','hidden'=>0,'sort'=>13],
				['id'=>1614,'title'=>'阿里云空间子目录','key'=>'store_aliyun_dirname','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>16,'number'=>0,'tag'=>'store','hidden'=>0,'sort'=>14],
				['id'=>1615,'title'=>'阿里云空间域名','key'=>'store_aliyun_domain','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>16,'number'=>0,'tag'=>'store','hidden'=>0,'sort'=>15],
				['id'=>1616,'title'=>'腾讯云secretId','key'=>'store_cos_secret_id','form_type'=>'password','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>16,'number'=>0,'tag'=>'store','hidden'=>0,'sort'=>16],
				['id'=>1617,'title'=>'腾讯云secretKey','key'=>'store_cos_secret_key','form_type'=>'password','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>16,'number'=>0,'tag'=>'store','hidden'=>0,'sort'=>17],
				['id'=>1618,'title'=>'腾讯云空间名称','key'=>'store_cos_bucket','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>16,'number'=>0,'tag'=>'store','hidden'=>0,'sort'=>18],
				['id'=>1619,'title'=>'腾讯云空间子目录','key'=>'store_cos_dirname','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>16,'number'=>0,'tag'=>'store','hidden'=>0,'sort'=>19],
				['id'=>1620,'title'=>'腾讯云空间域名','key'=>'store_cos_domain','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>16,'number'=>0,'tag'=>'store','hidden'=>0,'sort'=>20],

				['id'=>1701,'title'=>'开启注册','key'=>'user_reg_enable','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>1,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>17,'number'=>0,'tag'=>'user','hidden'=>0,'sort'=>1],
				['id'=>1702,'title'=>'开启登录','key'=>'user_login_enable','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>1,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>17,'number'=>0,'tag'=>'user','hidden'=>0,'sort'=>2],
				['id'=>1703,'title'=>'手机号自动注册','key'=>'user_auto_reg_mobile','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>1,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>17,'number'=>0,'tag'=>'user','hidden'=>0,'sort'=>3],
				['id'=>1704,'title'=>'用户自动激活','key'=>'user_is_active','form_type'=>'switch','value_type'=>2,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>17,'number'=>0,'tag'=>'user','hidden'=>0,'sort'=>04],
				['id'=>1705,'title'=>'账号ID默认长度','key'=>'user_account_length','form_type'=>'input','value_type'=>2,'rows'=>0,'default_value'=>8,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'位','level'=>2,'pid'=>17,'number'=>0,'tag'=>'user','hidden'=>0,'sort'=>5],
				['id'=>1706,'title'=>'邀请码默认长度','key'=>'user_invite_length','form_type'=>'input','value_type'=>2,'rows'=>0,'default_value'=>8,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'位','level'=>2,'pid'=>17,'number'=>0,'tag'=>'user','hidden'=>0,'sort'=>6],
				['id'=>1707,'title'=>'Token过期时间','key'=>'user_token_expire_time','form_type'=>'input','value_type'=>2,'rows'=>0,'default_value'=>7200,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'秒','level'=>2,'pid'=>17,'number'=>0,'tag'=>'user','hidden'=>0,'sort'=>7],
				['id'=>1708,'title'=>'Token刷新时间','key'=>'user_token_refresh_time','form_type'=>'input','value_type'=>2,'rows'=>0,'default_value'=>7200,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'秒','level'=>2,'pid'=>17,'number'=>0,'tag'=>'user','hidden'=>0,'sort'=>8],
				['id'=>1709,'title'=>'默认注册密码','key'=>'user_default_password','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>17,'number'=>0,'tag'=>'user','hidden'=>0,'sort'=>9],
				['id'=>1710,'title'=>'注册赠送积分','key'=>'user_reg_send_score','form_type'=>'input','value_type'=>2,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>17,'number'=>0,'tag'=>'user','hidden'=>0,'sort'=>10],
				['id'=>1711,'title'=>'注册赠送余额','key'=>'user_reg_send_balance','form_type'=>'input','value_type'=>2,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>17,'number'=>0,'tag'=>'user','hidden'=>0,'sort'=>11],
				['id'=>1712,'title'=>'注册赠送平台币','key'=>'user_reg_send_coin','form_type'=>'input','value_type'=>2,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>17,'number'=>0,'tag'=>'user','hidden'=>0,'sort'=>12],
				['id'=>1713,'title'=>'签到赠送积分','key'=>'user_sign_send_score','form_type'=>'input','value_type'=>2,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>17,'number'=>0,'tag'=>'user','hidden'=>0,'sort'=>13],
				['id'=>1714,'title'=>'连续签到设置','key'=>'user_continual_sign','form_type'=>'milut_form','value_type'=>1,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'{"title":"第几天","days":"连续天数","value":"赠送积分"}','prefix'=>'','suffix'=>'','level'=>2,'pid'=>17,'number'=>0,'tag'=>'user','hidden'=>0,'sort'=>14],
				['id'=>1715,'title'=>'分享赠送积分','key'=>'user_share_send_score','form_type'=>'input','value_type'=>2,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>17,'number'=>0,'tag'=>'user','hidden'=>0,'sort'=>15],
				['id'=>1716,'title'=>'分享赠送余额','key'=>'user_share_send_balance','form_type'=>'input','value_type'=>2,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>17,'number'=>0,'tag'=>'user','hidden'=>0,'sort'=>16],
				['id'=>1717,'title'=>'分享赠送平台币','key'=>'user_share_send_coin','form_type'=>'input','value_type'=>2,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>17,'number'=>0,'tag'=>'user','hidden'=>0,'sort'=>17],
				['id'=>1718,'title'=>'分享赠送二级积分','key'=>'user_share_super_score','form_type'=>'input','value_type'=>2,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>17,'number'=>0,'tag'=>'user','hidden'=>0,'sort'=>18],
				['id'=>1719,'title'=>'分享赠送二级余额','key'=>'user_share_super_balance','form_type'=>'input','value_type'=>2,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>17,'number'=>0,'tag'=>'user','hidden'=>0,'sort'=>19],
				['id'=>1720,'title'=>'分享赠送二级平台币','key'=>'user_share_super_coin','form_type'=>'input','value_type'=>2,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>17,'number'=>0,'tag'=>'user','hidden'=>0,'sort'=>20],
				['id'=>1721,'title'=>'开启注册日志','key'=>'user_is_log_reg','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>17,'number'=>0,'tag'=>'user','hidden'=>0,'sort'=>21],
				['id'=>1722,'title'=>'开启登录日志','key'=>'user_is_log_login','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>17,'number'=>0,'tag'=>'user','hidden'=>0,'sort'=>22],
				['id'=>1723,'title'=>'网页操作间隔','key'=>'user_hand_interval','form_type'=>'input','value_type'=>2,'rows'=>0,'default_value'=>30,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'秒','level'=>2,'pid'=>17,'number'=>0,'tag'=>'user','hidden'=>0,'sort'=>23],
				['id'=>1724,'title'=>'注册登录操作间隔','key'=>'user_login_interval','form_type'=>'input','value_type'=>2,'rows'=>0,'default_value'=>180,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'秒','level'=>2,'pid'=>17,'number'=>0,'tag'=>'user','hidden'=>0,'sort'=>24],
				['id'=>1725,'title'=>'登录错误次数','key'=>'user_login_error_number','form_type'=>'input','value_type'=>2,'rows'=>0,'default_value'=>5,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'次','level'=>2,'pid'=>17,'number'=>0,'tag'=>'user','hidden'=>0,'sort'=>25],
				['id'=>1726,'title'=>'邀请码必填','key'=>'user_is_invite','form_type'=>'switch','value_type'=>2,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>17,'number'=>0,'tag'=>'user','hidden'=>0,'sort'=>26],
				['id'=>1727,'title'=>'实名认证短信必填','key'=>'user_auth_is_smscode','form_type'=>'switch','value_type'=>2,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>17,'number'=>0,'tag'=>'user','hidden'=>0,'sort'=>27],
				['id'=>1728,'title'=>'默认注册角色ID','key'=>'user_default_reg_role_id','form_type'=>'input','value_type'=>2,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>17,'number'=>0,'tag'=>'user','hidden'=>0,'sort'=>28],
				['id'=>1729,'title'=>'用户名敏感词','key'=>'user_sensitive_word','form_type'=>'textarea','value_type'=>1,'rows'=>0,'default_value'=>'admin,root,manage,master,administrator','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>17,'number'=>0,'tag'=>'user','hidden'=>0,'sort'=>29],
				['id'=>1730,'title'=>'默认头像','key'=>'user_default_face','form_type'=>'upload_face','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>17,'number'=>0,'tag'=>'user','hidden'=>0,'sort'=>30],

				['id'=>1801,'title'=>'开启实名认证','key'=>'auth_enable','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>1,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>18,'number'=>0,'tag'=>'auth','hidden'=>0,'sort'=>1],
				['id'=>1802,'title'=>'认证平台','key'=>'auth_platform','form_type'=>'radio-group','value_type'=>1,'rows'=>0,'default_value'=>1,'size'=>'default','limit'=>0,'options'=>'{"阿里云":1,"腾讯云":2,"华为云":3}','prefix'=>'','suffix'=>'','level'=>2,'pid'=>18,'number'=>0,'tag'=>'auth','hidden'=>0,'sort'=>2],
				['id'=>1803,'title'=>'认证方式','key'=>'auth_type','form_type'=>'radio-group','value_type'=>1,'rows'=>0,'default_value'=>1,'size'=>'default','limit'=>0,'options'=>'{"身份证二要素":1,"身份证三要素":2,"身份证OCR识别":3}','prefix'=>'','suffix'=>'','level'=>2,'pid'=>18,'number'=>0,'tag'=>'auth','hidden'=>0,'sort'=>3],
				['id'=>1804,'title'=>'阿里云AppCode','key'=>'auth_aliyun_appcode','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>18,'number'=>0,'tag'=>'auth','hidden'=>0,'sort'=>4],
				['id'=>1805,'title'=>'阿里云AppKey','key'=>'auth_aliyun_appkey','form_type'=>'password','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>18,'number'=>0,'tag'=>'auth','hidden'=>0,'sort'=>5],
				['id'=>1806,'title'=>'阿里云AppSecret','key'=>'auth_aliyun_appsecret','form_type'=>'password','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>18,'number'=>0,'tag'=>'auth','hidden'=>0,'sort'=>6],

				['id'=>1901,'title'=>'开启商品类型','key'=>'goods_is_type','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>19,'number'=>0,'tag'=>'goods','hidden'=>0,'sort'=>1],
				['id'=>1902,'title'=>'开启属性','key'=>'goods_is_attr','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>19,'number'=>0,'tag'=>'goods','hidden'=>0,'sort'=>2],
				['id'=>1903,'title'=>'属性层级','key'=>'goods_attr_level','form_type'=>'select','value_type'=>2,'rows'=>0,'default_value'=>1,'size'=>'default','limit'=>0,'options'=>'{"一级":1,"二级":2}','prefix'=>'','suffix'=>'','level'=>2,'pid'=>19,'number'=>0,'tag'=>'goods','hidden'=>0,'sort'=>3],
				['id'=>1904,'title'=>'开启多规格','key'=>'goods_is_spec','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>1,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>19,'number'=>0,'tag'=>'goods','hidden'=>0,'sort'=>4],
				['id'=>1905,'title'=>'规格最大数','key'=>'goods_spec_max_number','form_type'=>'input','value_type'=>2,'rows'=>0,'default_value'=>2,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>19,'number'=>0,'tag'=>'goods','hidden'=>0,'sort'=>5],
				['id'=>1906,'title'=>'开启分享','key'=>'goods_is_share','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>19,'number'=>0,'tag'=>'goods','hidden'=>0,'sort'=>6],
				['id'=>1907,'title'=>'开启分佣','key'=>'goods_is_commission','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>19,'number'=>0,'tag'=>'goods','hidden'=>0,'sort'=>7],
				['id'=>1908,'title'=>'开启评论','key'=>'goods_is_comment','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>19,'number'=>0,'tag'=>'goods','hidden'=>0,'sort'=>8],
				['id'=>1909,'title'=>'积分兑换金额','key'=>'goods_score_exchange_amount','form_type'=>'input','value_type'=>2,'rows'=>0,'default_value'=>100,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'积分=1元','level'=>2,'pid'=>19,'number'=>0,'tag'=>'goods','hidden'=>0,'sort'=>9],
				['id'=>1910,'title'=>'开启商品品牌','key'=>'goods_is_brand','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>19,'number'=>0,'tag'=>'goods','hidden'=>0,'sort'=>10],
				['id'=>1911,'title'=>'开启标签','key'=>'goods_is_tags','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>19,'number'=>0,'tag'=>'goods','hidden'=>0,'sort'=>11],
				['id'=>1912,'title'=>'开启预售值','key'=>'goods_is_presale','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>19,'number'=>0,'tag'=>'goods','hidden'=>0,'sort'=>12],
				['id'=>1913,'title'=>'开启商品自提','key'=>'goods_is_pickup','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>19,'number'=>0,'tag'=>'goods','hidden'=>0,'sort'=>13],
				['id'=>1914,'title'=>'显示原价','key'=>'goods_is_original_price','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>1,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>19,'number'=>0,'tag'=>'goods','hidden'=>0,'sort'=>14],
				['id'=>1915,'title'=>'显示销量','key'=>'goods_is_sale_volume','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>1,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>19,'number'=>0,'tag'=>'goods','hidden'=>0,'sort'=>15],
				['id'=>1916,'title'=>'开启会员价','key'=>'goods_is_member_price','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>19,'number'=>0,'tag'=>'goods','hidden'=>0,'sort'=>16],

				['id'=>2001,'title'=>'开启拼团','key'=>'market_is_teamwork','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>20,'number'=>0,'tag'=>'market','hidden'=>0,'sort'=>1],
				['id'=>2002,'title'=>'开启秒杀','key'=>'market_is_seckill','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>20,'number'=>0,'tag'=>'market','hidden'=>0,'sort'=>2],
				['id'=>2003,'title'=>'开启砍价','key'=>'market_is_counter','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>20,'number'=>0,'tag'=>'market','hidden'=>0,'sort'=>3],
				['id'=>2004,'title'=>'开启活动专区','key'=>'market_is_section','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>20,'number'=>0,'tag'=>'market','hidden'=>0,'sort'=>4],
				['id'=>2005,'title'=>'开启众筹','key'=>'market_is_crowdfunding','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>20,'number'=>0,'tag'=>'market','hidden'=>0,'sort'=>5],
				['id'=>2006,'title'=>'开启拍卖','key'=>'markte_is_auction','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>20,'number'=>0,'tag'=>'market','hidden'=>0,'sort'=>6],
				['id'=>2007,'title'=>'开启盲盒','key'=>'market_is_blind','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>20,'number'=>0,'tag'=>'market','hidden'=>0,'sort'=>7],
				['id'=>2008,'title'=>'开启空投','key'=>'market_is_airdrop','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>20,'number'=>0,'tag'=>'market','hidden'=>0,'sort'=>8],
				['id'=>2009,'title'=>'开启转赠','key'=>'market_is_transfer','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>20,'number'=>0,'tag'=>'market','hidden'=>0,'sort'=>9],
				['id'=>2010,'title'=>'开启转售','key'=>'market_is_resale','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>20,'number'=>0,'tag'=>'market','hidden'=>0,'sort'=>10],
				['id'=>2011,'title'=>'开启优先购','key'=>'market_is_priority','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>20,'number'=>0,'tag'=>'market','hidden'=>0,'sort'=>11],
				['id'=>2012,'title'=>'开启二级市场','key'=>'market_is_second_market','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>20,'number'=>0,'tag'=>'market','hidden'=>0,'sort'=>12],

				['id'=>2101,'title'=>'开启订单','key'=>'order_is_order','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>1,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>21,'number'=>0,'tag'=>'order','hidden'=>0,'sort'=>1],
				['id'=>2102,'title'=>'开启实名下单','key'=>'order_is_auth','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>1,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>21,'number'=>0,'tag'=>'order','hidden'=>0,'sort'=>2],
				['id'=>2103,'title'=>'开启积分','key'=>'order_is_score','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>1,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>21,'number'=>0,'tag'=>'order','hidden'=>0,'sort'=>3],
				['id'=>2104,'title'=>'开启分佣','key'=>'order_is_commission','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>1,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>21,'number'=>0,'tag'=>'order','hidden'=>0,'sort'=>4],
				['id'=>2105,'title'=>'订单查询年限','key'=>'order_show_time','form_type'=>'input','value_type'=>2,'rows'=>0,'default_value'=>2,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'年','level'=>2,'pid'=>21,'number'=>0,'tag'=>'order','hidden'=>0,'sort'=>5],
				['id'=>2106,'title'=>'订单长度','key'=>'order_length','form_type'=>'input','value_type'=>2,'rows'=>0,'default_value'=>12,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'年','level'=>2,'pid'=>21,'number'=>0,'tag'=>'order','hidden'=>0,'sort'=>6],
				['id'=>2107,'title'=>'开启支付','key'=>'order_is_pay','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>1,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'年','level'=>2,'pid'=>21,'number'=>0,'tag'=>'order','hidden'=>0,'sort'=>7],
				['id'=>2108,'title'=>'开启余额支付','key'=>'order_balance_pay','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>1,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'年','level'=>2,'pid'=>21,'number'=>0,'tag'=>'order','hidden'=>0,'sort'=>8],
				['id'=>2109,'title'=>'开启微信支付','key'=>'order_wechat_pay','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>1,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'年','level'=>2,'pid'=>21,'number'=>0,'tag'=>'order','hidden'=>0,'sort'=>9],
				['id'=>2110,'title'=>'开启支付宝支付','key'=>'order_alipay_pay','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>1,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'年','level'=>2,'pid'=>21,'number'=>0,'tag'=>'order','hidden'=>0,'sort'=>10],
				['id'=>2111,'title'=>'开启网银支付','key'=>'order_bank_pay','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>1,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'年','level'=>2,'pid'=>21,'number'=>0,'tag'=>'order','hidden'=>0,'sort'=>11],
				['id'=>2112,'title'=>'支付可使用余额','key'=>'order_pay_is_balance','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'年','level'=>2,'pid'=>21,'number'=>0,'tag'=>'order','hidden'=>0,'sort'=>12],
				['id'=>2113,'title'=>'开启积分商城','key'=>'order_is_score_shop','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'年','level'=>2,'pid'=>21,'number'=>0,'tag'=>'order','hidden'=>0,'sort'=>13],
				['id'=>2114,'title'=>'开启测试支付','key'=>'order_test_pay','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'年','level'=>2,'pid'=>21,'number'=>0,'tag'=>'order','hidden'=>0,'sort'=>14],
				['id'=>2115,'title'=>'测试支付金额','key'=>'order_test_pay_amount','form_type'=>'input','value_type'=>2,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'分','level'=>2,'pid'=>21,'number'=>0,'tag'=>'order','hidden'=>0,'sort'=>15],
				['id'=>2116,'title'=>'测试支付会员ID','key'=>'order_test_pay_user','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>21,'number'=>0,'tag'=>'order','hidden'=>0,'sort'=>16],
				['id'=>2117,'title'=>'开启订单自动确认','key'=>'order_is_confirm','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>1,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>21,'number'=>0,'tag'=>'order','hidden'=>0,'sort'=>17],
				['id'=>2118,'title'=>'订单超时时间','key'=>'order_wait_time','form_type'=>'input','value_type'=>2,'rows'=>0,'default_value'=>30,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'分钟','level'=>2,'pid'=>21,'number'=>0,'tag'=>'order','hidden'=>0,'sort'=>18],
				['id'=>2119,'title'=>'订单自动收货时间','key'=>'order_take_time','form_type'=>'input','value_type'=>2,'rows'=>0,'default_value'=>7,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'天','level'=>2,'pid'=>21,'number'=>0,'tag'=>'order','hidden'=>0,'sort'=>19],
				['id'=>2120,'title'=>'开启小程序收货','key'=>'order_is_wechat_take','form_type'=>'switch','value_type'=>2,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'天','level'=>2,'pid'=>21,'number'=>0,'tag'=>'order','hidden'=>0,'sort'=>20],
				['id'=>2121,'title'=>'订单短信通知','key'=>'order_send_mobile','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>21,'number'=>0,'tag'=>'order','hidden'=>0,'sort'=>21],
				['id'=>2122,'title'=>'短信通知商户','key'=>'order_is_send_merchant','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>21,'number'=>0,'tag'=>'order','hidden'=>0,'sort'=>22],

				['id'=>2201,'title'=>'开启快递','key'=>'express_enable','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>1,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>22,'number'=>0,'tag'=>'express','hidden'=>0,'sort'=>1],
				['id'=>2202,'title'=>'默认接口平台','key'=>'express_platform','form_type'=>'select','value_type'=>1,'rows'=>0,'default_value'=>'kuaidi100','size'=>'default','limit'=>0,'options'=>'{"快递100":"kuaidi100","快递鸟":"kdniao","万维易源":"showapi"}','prefix'=>'','suffix'=>'','level'=>2,'pid'=>22,'number'=>0,'tag'=>'express','hidden'=>0,'sort'=>2],
				['id'=>2203,'title'=>'快递100授权Key','key'=>'express_kuaidi100_app_id','form_type'=>'password','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>22,'number'=>0,'tag'=>'express','hidden'=>0,'sort'=>3],
				['id'=>2204,'title'=>'快递100Customer','key'=>'express_kuaidi100_app_key','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>22,'number'=>0,'tag'=>'express','hidden'=>0,'sort'=>4],
				['id'=>2205,'title'=>'快递鸟AppID','key'=>'express_kdniao_app_id','form_type'=>'password','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>22,'number'=>0,'tag'=>'express','hidden'=>0,'sort'=>5],
				['id'=>2206,'title'=>'快递鸟AppKey','key'=>'express_kdniao_app_key','form_type'=>'password','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>22,'number'=>0,'tag'=>'express','hidden'=>0,'sort'=>6],
				['id'=>2207,'title'=>'万维易源AppID','key'=>'express_showapi_app_id','form_type'=>'password','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>22,'number'=>0,'tag'=>'express','hidden'=>0,'sort'=>7],
				['id'=>2208,'title'=>'万维易源AppKey','key'=>'express_showapi_app_key','form_type'=>'password','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>22,'number'=>0,'tag'=>'express','hidden'=>0,'sort'=>8],

				['id'=>2301,'title'=>'公众号设置','key'=>'wechat_for_wechat','form_type'=>'divider','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>23,'number'=>0,'tag'=>'wechat','hidden'=>0,'sort'=>1],
				['id'=>2302,'title'=>'公众号名称','key'=>'wechat_name','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>23,'number'=>0,'tag'=>'wechat','hidden'=>0,'sort'=>2],
				['id'=>2303,'title'=>'AppID','key'=>'wechat_appid','form_type'=>'password','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>23,'number'=>0,'tag'=>'wechat','hidden'=>0,'sort'=>3],
				['id'=>2304,'title'=>'AppSecret','key'=>'wechat_appsecret','form_type'=>'password','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>23,'number'=>0,'tag'=>'wechat','hidden'=>0,'sort'=>4],
				['id'=>2305,'title'=>'Token','key'=>'wechat_token','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>23,'number'=>0,'tag'=>'wechat','hidden'=>0,'sort'=>5],
				['id'=>2306,'title'=>'小程序设置','key'=>'wechat_for_mini','form_type'=>'divider','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>23,'number'=>0,'tag'=>'wechat','hidden'=>0,'sort'=>6],
				['id'=>2307,'title'=>'小程序名称','key'=>'mini_name','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>23,'number'=>0,'tag'=>'wechat','hidden'=>0,'sort'=>7],
				['id'=>2308,'title'=>'原始id','key'=>'mini_original_id','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>23,'number'=>0,'tag'=>'wechat','hidden'=>0,'sort'=>8],
				['id'=>2309,'title'=>'小程序AppID','key'=>'mini_appid','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>23,'number'=>0,'tag'=>'wechat','hidden'=>0,'sort'=>9],
				['id'=>2310,'title'=>'小程序AppSecret','key'=>'mini_appsecret','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>23,'number'=>0,'tag'=>'wechat','hidden'=>0,'sort'=>10],
				['id'=>2311,'title'=>'开放平台设置','key'=>'wechat_for_open','form_type'=>'divider','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>23,'number'=>0,'tag'=>'wechat','hidden'=>0,'sort'=>11],
				['id'=>2312,'title'=>'应用名称','key'=>'wechat_open_app_name','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>23,'number'=>0,'tag'=>'wechat','hidden'=>0,'sort'=>12],
				['id'=>2313,'title'=>'AppID','key'=>'wechat_open_appid','form_type'=>'password','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>23,'number'=>0,'tag'=>'wechat','hidden'=>0,'sort'=>13],
				['id'=>2314,'title'=>'AppSecret','key'=>'wechat_open_appsecret','form_type'=>'password','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>23,'number'=>0,'tag'=>'wechat','hidden'=>0,'sort'=>14],
				['id'=>2315,'title'=>'商户号设置','key'=>'wechat_for_mch','form_type'=>'divider','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>23,'number'=>0,'tag'=>'wechat','hidden'=>0,'sort'=>15],
				['id'=>2316,'title'=>'商户号ID','key'=>'merchant_mchid','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>23,'number'=>0,'tag'=>'wechat','hidden'=>0,'sort'=>16],
				['id'=>2317,'title'=>'Api私钥V2','key'=>'merchant_api_v2_key','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>23,'number'=>0,'tag'=>'wechat','hidden'=>0,'sort'=>17],
				['id'=>2318,'title'=>'Api私钥V3','key'=>'merchant_api_v3_key','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>23,'number'=>0,'tag'=>'wechat','hidden'=>0,'sort'=>18],
				['id'=>2319,'title'=>'Apicert证书','key'=>'merchant_api_cert','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>23,'number'=>0,'tag'=>'wechat','hidden'=>0,'sort'=>19],
				['id'=>2320,'title'=>'Apikeys证书','key'=>'merchant_api_keys_certificate','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>23,'number'=>0,'tag'=>'wechat','hidden'=>0,'sort'=>20],
				['id'=>2321,'title'=>'回调地址','key'=>'merchant_mch_callback','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>23,'number'=>0,'tag'=>'wechat','hidden'=>0,'sort'=>21],
				['id'=>2322,'title'=>'返回地址','key'=>'merchant_mch_backurl','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>23,'number'=>0,'tag'=>'wechat','hidden'=>0,'sort'=>22],

				['id'=>2401,'title'=>'QQ登录设置','key'=>'login_for_qq','form_type'=>'divider','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>24,'number'=>0,'tag'=>'login','hidden'=>0,'sort'=>1],
				['id'=>2402,'title'=>'AppID','key'=>'login_qq_appid','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>24,'number'=>0,'tag'=>'login','hidden'=>0,'sort'=>2],
				['id'=>2403,'title'=>'AppSecret','key'=>'login_qq_appsecret','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>24,'number'=>0,'tag'=>'login','hidden'=>0,'sort'=>3],
				['id'=>2404,'title'=>'返回地址','key'=>'login_qq_backurl','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>24,'number'=>0,'tag'=>'login','hidden'=>0,'sort'=>4],
				['id'=>2405,'title'=>'微信登录设置','key'=>'login_for_wechat','form_type'=>'divider','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>24,'number'=>0,'tag'=>'login','hidden'=>0,'sort'=>5],
				['id'=>2406,'title'=>'AppID','key'=>'login_wechat_appid','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>24,'number'=>0,'tag'=>'login','hidden'=>0,'sort'=>6],
				['id'=>2407,'title'=>'AppSecret','key'=>'login_wechat_appsecret','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>24,'number'=>0,'tag'=>'login','hidden'=>0,'sort'=>7],
				['id'=>2408,'title'=>'返回地址','key'=>'login_wechat_backurl','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>24,'number'=>0,'tag'=>'login','hidden'=>0,'sort'=>8],
				['id'=>2409,'title'=>'开放平台AppID','key'=>'login_open_appid','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>24,'number'=>0,'tag'=>'login','hidden'=>0,'sort'=>9],
				['id'=>2410,'title'=>'开放平台AccessToken','key'=>'login_open_access_token','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>24,'number'=>0,'tag'=>'login','hidden'=>0,'sort'=>10],
				['id'=>2411,'title'=>'微博登录设置','key'=>'login_for_weibo','form_type'=>'divider','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>24,'number'=>0,'tag'=>'login','hidden'=>0,'sort'=>11],
				['id'=>2412,'title'=>'AppID','key'=>'login_weibo_appid','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>24,'number'=>0,'tag'=>'login','hidden'=>0,'sort'=>12],
				['id'=>2413,'title'=>'AppSecret','key'=>'login_weibo_appsecret','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>24,'number'=>0,'tag'=>'login','hidden'=>0,'sort'=>13],
				['id'=>2414,'title'=>'返回地址','key'=>'login_weibo_backurl','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>24,'number'=>0,'tag'=>'login','hidden'=>0,'sort'=>14],
				['id'=>2415,'title'=>'淘宝登录设置','key'=>'login_for_taobao','form_type'=>'divider','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>24,'number'=>0,'tag'=>'login','hidden'=>0,'sort'=>15],
				['id'=>2416,'title'=>'AppID','key'=>'login_taobao_appid','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>24,'number'=>0,'tag'=>'login','hidden'=>0,'sort'=>16],
				['id'=>2417,'title'=>'AppSecret','key'=>'login_taobao_appsecret','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>24,'number'=>0,'tag'=>'login','hidden'=>0,'sort'=>17],
				['id'=>2418,'title'=>'返回地址','key'=>'login_taobao_backurl','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>24,'number'=>0,'tag'=>'login','hidden'=>0,'sort'=>18],
				['id'=>2419,'title'=>'支付宝登录设置','key'=>'login_for_alipay','form_type'=>'divider','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>24,'number'=>0,'tag'=>'login','hidden'=>0,'sort'=>19],
				['id'=>2420,'title'=>'AppID','key'=>'login_alipay_appid','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>24,'number'=>0,'tag'=>'login','hidden'=>0,'sort'=>20],
				['id'=>2421,'title'=>'私钥地址','key'=>'login_alipay_rsa_private_key','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>24,'number'=>0,'tag'=>'login','hidden'=>0,'sort'=>21],
				['id'=>2422,'title'=>'返回地址','key'=>'login_alipay_backurl','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>24,'number'=>0,'tag'=>'login','hidden'=>0,'sort'=>22],
				['id'=>2423,'title'=>'Coding登录设置','key'=>'login_for_coding','form_type'=>'divider','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>24,'number'=>0,'tag'=>'login','hidden'=>0,'sort'=>23],
				['id'=>2424,'title'=>'团队URL','key'=>'login_coding_team_url','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>24,'number'=>0,'tag'=>'login','hidden'=>0,'sort'=>24],
				['id'=>2425,'title'=>'AppID','key'=>'login_coding_appid','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>24,'number'=>0,'tag'=>'login','hidden'=>0,'sort'=>25],
				['id'=>2426,'title'=>'AppSecret','key'=>'login_coding_appsecret','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>24,'number'=>0,'tag'=>'login','hidden'=>0,'sort'=>26],
				['id'=>2427,'title'=>'返回地址','key'=>'login_coding_backurl','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>24,'number'=>0,'tag'=>'login','hidden'=>0,'sort'=>27],
				['id'=>2428,'title'=>'钉钉登录设置','key'=>'login_for_dingtalk','form_type'=>'divider','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>24,'number'=>0,'tag'=>'login','hidden'=>0,'sort'=>28],
				['id'=>2429,'title'=>'AppID','key'=>'login_dingtalk_appid','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>24,'number'=>0,'tag'=>'login','hidden'=>0,'sort'=>29],
				['id'=>2430,'title'=>'AppSecret','key'=>'login_dingtalk_appsecret','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>24,'number'=>0,'tag'=>'login','hidden'=>0,'sort'=>30],
				['id'=>2431,'title'=>'返回地址','key'=>'login_dingtalk_backurl','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>24,'number'=>0,'tag'=>'login','hidden'=>0,'sort'=>31],
				['id'=>2432,'title'=>'百度登录设置','key'=>'login_for_baidu','form_type'=>'divider','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>24,'number'=>0,'tag'=>'login','hidden'=>0,'sort'=>32],
				['id'=>2433,'title'=>'AppID','key'=>'login_baidu_appid','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>24,'number'=>0,'tag'=>'login','hidden'=>0,'sort'=>33],
				['id'=>2434,'title'=>'AppSecret','key'=>'login_baidu_appsecret','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>24,'number'=>0,'tag'=>'login','hidden'=>0,'sort'=>34],
				['id'=>2435,'title'=>'返回地址','key'=>'login_baidu_backurl','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>24,'number'=>0,'tag'=>'login','hidden'=>0,'sort'=>35],
				['id'=>2436,'title'=>'亚马逊登录设置','key'=>'login_for_azure','form_type'=>'divider','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>24,'number'=>0,'tag'=>'login','hidden'=>0,'sort'=>36],
				['id'=>2437,'title'=>'AppID','key'=>'login_azure_appid','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>24,'number'=>0,'tag'=>'login','hidden'=>0,'sort'=>37],
				['id'=>2438,'title'=>'AppSecret','key'=>'login_azure_appsecret','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>24,'number'=>0,'tag'=>'login','hidden'=>0,'sort'=>38],
				['id'=>2439,'title'=>'返回地址','key'=>'login_azure_backurl','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>24,'number'=>0,'tag'=>'login','hidden'=>0,'sort'=>39],
				['id'=>2440,'title'=>'豆瓣登录设置','key'=>'login_for_douban','form_type'=>'divider','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>24,'number'=>0,'tag'=>'login','hidden'=>0,'sort'=>40],
				['id'=>2441,'title'=>'AppID','key'=>'login_douban_appid','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>24,'number'=>0,'tag'=>'login','hidden'=>0,'sort'=>41],
				['id'=>2442,'title'=>'AppSecret','key'=>'login_douban_appsecret','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>24,'number'=>0,'tag'=>'login','hidden'=>0,'sort'=>42],
				['id'=>2443,'title'=>'返回地址','key'=>'login_douban_backurl','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>24,'number'=>0,'tag'=>'login','hidden'=>0,'sort'=>43],
				['id'=>2444,'title'=>'抖音登录设置','key'=>'login_for_douyin','form_type'=>'divider','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>24,'number'=>0,'tag'=>'login','hidden'=>0,'sort'=>44],
				['id'=>2445,'title'=>'AppID','key'=>'login_douyin_appid','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>24,'number'=>0,'tag'=>'login','hidden'=>0,'sort'=>45],
				['id'=>2446,'title'=>'AppSecret','key'=>'login_douyin_appsecret','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>24,'number'=>0,'tag'=>'login','hidden'=>0,'sort'=>46],
				['id'=>2447,'title'=>'返回地址','key'=>'login_douyin_backurl','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>24,'number'=>0,'tag'=>'login','hidden'=>0,'sort'=>47],

				['id'=>2501,'title'=>'支付宝AppID','key'=>'alipay_app_id','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>25,'number'=>0,'tag'=>'alipay','hidden'=>0,'sort'=>1],
				['id'=>2502,'title'=>'应用私钥','key'=>'alipay_app_secret_cert','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>25,'number'=>0,'tag'=>'alipay','hidden'=>0,'sort'=>2],
				['id'=>2503,'title'=>'应用公钥证书','key'=>'alipay_app_public_cert_path','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>25,'number'=>0,'tag'=>'alipay','hidden'=>0,'sort'=>3],
				['id'=>2504,'title'=>'支付宝公钥证书','key'=>'alipay_public_cert_path','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>25,'number'=>0,'tag'=>'alipay','hidden'=>0,'sort'=>4],
				['id'=>2505,'title'=>'支付宝根证书','key'=>'alipay_root_cert_path','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>25,'number'=>0,'tag'=>'alipay','hidden'=>0,'sort'=>5],

				['id'=>2601,'title'=>'网站名称','key'=>'web_name','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>26,'number'=>0,'tag'=>'web','hidden'=>0,'sort'=>1],

				['id'=>2701,'title'=>'基础设置','key'=>'mini_for_base','form_type'=>'divider','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>27,'number'=>0,'tag'=>'mini','hidden'=>0,'sort'=>1],
				['id'=>2702,'title'=>'小程序名称','key'=>'mini_name','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>27,'number'=>0,'tag'=>'mini','hidden'=>0,'sort'=>2],
				['id'=>2703,'title'=>'小程序Slogan','key'=>'mini_slogan','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>27,'number'=>0,'tag'=>'mini','hidden'=>0,'sort'=>3],
				['id'=>2704,'title'=>'小程序Logo','key'=>'mini_logo','form_type'=>'upload_logo','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'small','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>27,'number'=>0,'tag'=>'mini','hidden'=>0,'sort'=>4],
				['id'=>2705,'title'=>'客服电话','key'=>'mini_service_phone','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>27,'number'=>0,'tag'=>'mini','hidden'=>0,'sort'=>5],
				['id'=>2706,'title'=>'首页设置','key'=>'mini_for_index','form_type'=>'divider','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>27,'number'=>0,'tag'=>'mini','hidden'=>0,'sort'=>6],
				['id'=>2707,'title'=>'全屏模式','key'=>'mini_is_screen','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>1,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>27,'number'=>0,'tag'=>'mini','hidden'=>0,'sort'=>7],
				['id'=>2708,'title'=>'首页背景图','key'=>'mini_index_bg_img','form_type'=>'upload_img','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>27,'number'=>0,'tag'=>'mini','hidden'=>0,'sort'=>6],
				['id'=>2709,'title'=>'标题栏对齐方式','key'=>'mini_header_align','form_type'=>'radio','value_type'=>1,'rows'=>0,'default_value'=>'center','size'=>'default','limit'=>0,'options'=>'{"左对齐":"start","居中对齐":"center","右对齐":"end"}','prefix'=>'','suffix'=>'','level'=>2,'pid'=>27,'number'=>0,'tag'=>'mini','hidden'=>0,'sort'=>9],
				['id'=>2710,'title'=>'标题栏显示Logo','key'=>'mini_header_is_logo','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>1,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>27,'number'=>0,'tag'=>'mini','hidden'=>0,'sort'=>10],
				['id'=>2711,'title'=>'标题栏显示Slogan','key'=>'mini_header_is_slogan','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>1,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>27,'number'=>0,'tag'=>'mini','hidden'=>0,'sort'=>11],
				['id'=>2712,'title'=>'标题栏文本颜色','key'=>'mini_text_color','form_type'=>'color-picker','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>27,'number'=>0,'tag'=>'mini','hidden'=>0,'sort'=>12],
				['id'=>2713,'title'=>'标题栏文本大小','key'=>'mini_text_size','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'px','level'=>2,'pid'=>27,'number'=>0,'tag'=>'mini','hidden'=>0,'sort'=>13],
				['id'=>2714,'title'=>'标题栏背景颜色','key'=>'mini_bg_color','form_type'=>'color-picker','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>27,'number'=>0,'tag'=>'mini','hidden'=>0,'sort'=>14],
				['id'=>2715,'title'=>'开启搜索','key'=>'mini_index_search','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>27,'number'=>0,'tag'=>'mini','hidden'=>0,'sort'=>15],
				['id'=>2716,'title'=>'开启轮播图','key'=>'mini_index_is_carousel','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>27,'number'=>0,'tag'=>'mini','hidden'=>0,'sort'=>16],
				['id'=>2717,'title'=>'开启公告','key'=>'mini_index_is_notice','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>27,'number'=>0,'tag'=>'mini','hidden'=>0,'sort'=>17],
				['id'=>2718,'title'=>'公告滚动方式','key'=>'mini_notice_vertical','form_type'=>'radio','value_type'=>1,'rows'=>0,'default_value'=>'vertical','size'=>'default','limit'=>0,'options'=>'{"横向滚动":"horizontal","竖向滚动":"vertical"}','prefix'=>'','suffix'=>'','level'=>2,'pid'=>27,'number'=>0,'tag'=>'mini','hidden'=>0,'sort'=>18],
				['id'=>2719,'title'=>'开启功能列表','key'=>'mini_index_is_quick','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>27,'number'=>0,'tag'=>'mini','hidden'=>0,'sort'=>19],
				['id'=>2720,'title'=>'开启广告列表','key'=>'mini_index_is_advert','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>27,'number'=>0,'tag'=>'mini','hidden'=>0,'sort'=>20],
				['id'=>2721,'title'=>'开启会员列表','key'=>'mini_index_is_vip','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>27,'number'=>0,'tag'=>'mini','hidden'=>0,'sort'=>21],
				['id'=>2722,'title'=>'开启推荐商品','key'=>'mini_index_is_goods','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>27,'number'=>0,'tag'=>'mini','hidden'=>0,'sort'=>22],
				['id'=>2723,'title'=>'商品推荐数量','key'=>'mini_index_goods_rows','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>20,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'显示','suffix'=>'pcs','level'=>2,'pid'=>27,'number'=>0,'tag'=>'mini','hidden'=>0,'sort'=>23],
				['id'=>2724,'title'=>'显示商品原价','key'=>'mini_index_is_original','form_type'=>'switch','value_type'=>1,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>27,'number'=>0,'tag'=>'mini','hidden'=>0,'sort'=>24],
				['id'=>2725,'title'=>'显示商品销量','key'=>'mini_index_is_sales','form_type'=>'switch','value_type'=>1,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>27,'number'=>0,'tag'=>'mini','hidden'=>0,'sort'=>25],
				['id'=>2726,'title'=>'开启推荐新闻','key'=>'mini_index_is_news','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>0,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>27,'number'=>0,'tag'=>'mini','hidden'=>0,'sort'=>26],
				['id'=>2727,'title'=>'推荐新闻数量','key'=>'mini_index_news_rows','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>10,'size'=>'default','limit'=>0,'options'=>'','prefix'=>'显示','suffix'=>'条','level'=>2,'pid'=>27,'number'=>0,'tag'=>'mini','hidden'=>0,'sort'=>27],
				['id'=>2728,'title'=>'商品页设置','key'=>'mini_for_goods','form_type'=>'divider','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>27,'number'=>0,'tag'=>'mini','hidden'=>0,'sort'=>28],
				['id'=>2729,'title'=>'瀑布流布局','key'=>'mini_goods_is_waterfall','form_type'=>'switch','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>27,'number'=>0,'tag'=>'mini','hidden'=>0,'sort'=>29],
				['id'=>2730,'title'=>'详情页全屏模式','key'=>'mini_goods_detail_is_screen','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>27,'number'=>0,'tag'=>'mini','hidden'=>0,'sort'=>30],
				['id'=>2731,'title'=>'详情页主图高度','key'=>'mini_goods_detail_height','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'px','level'=>2,'pid'=>27,'number'=>0,'tag'=>'mini','hidden'=>0,'sort'=>31],
				['id'=>2732,'title'=>'详情页留白','key'=>'mini_goods_detail_margin','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'px','level'=>2,'pid'=>27,'number'=>0,'tag'=>'mini','hidden'=>0,'sort'=>32],
				['id'=>2733,'title'=>'显示商户信息','key'=>'mini_goods_detail_is_marchant','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>27,'number'=>0,'tag'=>'mini','hidden'=>0,'sort'=>33],
				['id'=>2734,'title'=>'显示评论','key'=>'mini_goods_detail_is_comment','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>27,'number'=>0,'tag'=>'mini','hidden'=>0,'sort'=>34],
				['id'=>2735,'title'=>'显示购买榜单','key'=>'mini_goods_detail_is_buyer','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>27,'number'=>0,'tag'=>'mini','hidden'=>0,'sort'=>35],
				['id'=>2736,'title'=>'用户中心设置','key'=>'mini_for_mch','form_type'=>'divider','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>27,'number'=>0,'tag'=>'mini','hidden'=>0,'sort'=>36],
				['id'=>2737,'title'=>'开启全屏模式','key'=>'mini_user_is_screen','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>27,'number'=>0,'tag'=>'mini','hidden'=>0,'sort'=>37],
				['id'=>2738,'title'=>'用户中心背景图','key'=>'mini_user_bg_img','form_type'=>'upload_img','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>27,'number'=>0,'tag'=>'mini','hidden'=>0,'sort'=>38],
				['id'=>2739,'title'=>'默认头像','key'=>'mini_user_default_face','form_type'=>'upload_face','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'small','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>27,'number'=>0,'tag'=>'mini','hidden'=>0,'sort'=>39],
				['id'=>2740,'title'=>'显示分享','key'=>'mini_user_is_share','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>27,'number'=>0,'tag'=>'mini','hidden'=>0,'sort'=>40],
				['id'=>2741,'title'=>'显示消息','key'=>'mini_user_is_message','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>27,'number'=>0,'tag'=>'mini','hidden'=>0,'sort'=>41],
				['id'=>2742,'title'=>'显示会员条','key'=>'mini_user_is_vip','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>27,'number'=>0,'tag'=>'mini','hidden'=>0,'sort'=>42],
				['id'=>2743,'title'=>'显示广告','key'=>'mini_user_is_advert','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>27,'number'=>0,'tag'=>'mini','hidden'=>0,'sort'=>43],

				['id'=>2801,'title'=>'APP名称','key'=>'app_name','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>28,'number'=>0,'tag'=>'app','hidden'=>0,'sort'=>1],

				['id'=>2901,'title'=>'网站标题','key'=>'decoration_title','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>29,'number'=>0,'tag'=>'decoration','hidden'=>0,'sort'=>1],
				['id'=>2902,'title'=>'客服电话','key'=>'decoration_telphone','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>29,'number'=>0,'tag'=>'decoration','hidden'=>0,'sort'=>2],
				['id'=>2903,'title'=>'网站LOGO','key'=>'decoration_logo','form_type'=>'upload_logo','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'small','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>29,'number'=>0,'tag'=>'decoration','hidden'=>0,'sort'=>3],
				['id'=>2904,'title'=>'显示顶部栏','key'=>'decoration_is_show_top','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>29,'number'=>0,'tag'=>'decoration','hidden'=>0,'sort'=>4],
				['id'=>2905,'title'=>'开启搜索','key'=>'decoration_is_search','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>29,'number'=>0,'tag'=>'decoration','hidden'=>0,'sort'=>5],
				['id'=>2906,'title'=>'开启轮播图','key'=>'decoration_is_carousel','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>29,'number'=>0,'tag'=>'decoration','hidden'=>0,'sort'=>6],
				['id'=>2907,'title'=>'显示顶部栏城市','key'=>'decoration_is_show_cityname','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>29,'number'=>0,'tag'=>'decoration','hidden'=>0,'sort'=>7],
				['id'=>2908,'title'=>'开启公告','key'=>'decoration_is_notice','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>29,'number'=>0,'tag'=>'decoration','hidden'=>0,'sort'=>8],
				['id'=>2909,'title'=>'显示顶部栏天气信息','key'=>'decoration_is_show_weather','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>29,'number'=>0,'tag'=>'decoration','hidden'=>0,'sort'=>9],
				['id'=>2910,'title'=>'开启友情链接','key'=>'decoration_is_link','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>29,'number'=>0,'tag'=>'decoration','hidden'=>0,'sort'=>10],
				['id'=>2911,'title'=>'显示顶部栏日期','key'=>'decoration_is_show_date','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>29,'number'=>0,'tag'=>'decoration','hidden'=>0,'sort'=>11],
				['id'=>2912,'title'=>'公众号二维码','key'=>'decoration_wechat_qrcode','form_type'=>'upload_img','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>29,'number'=>0,'tag'=>'decoration','hidden'=>0,'sort'=>12],
				['id'=>2913,'title'=>'小程序二维码','key'=>'decoration_mini_qrcode','form_type'=>'upload_img','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>29,'number'=>0,'tag'=>'decoration','hidden'=>0,'sort'=>13],
				['id'=>2914,'title'=>'顶部栏背景图','key'=>'decoration_bg_img','form_type'=>'upload_img','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>29,'number'=>0,'tag'=>'decoration','hidden'=>0,'sort'=>14],
				['id'=>2915,'title'=>'开启登录注册','key'=>'decoration_is_login','form_type'=>'switch','value_type'=>8,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>29,'number'=>0,'tag'=>'decoration','hidden'=>0,'sort'=>15],
				['id'=>2916,'title'=>'网页主色调','key'=>'decoration_main_color','form_type'=>'color-picker','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>29,'number'=>0,'tag'=>'decoration','hidden'=>0,'sort'=>16],
				['id'=>2917,'title'=>'网站辅色','key'=>'decoration_assist_color','form_type'=>'color-picker','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>29,'number'=>0,'tag'=>'decoration','hidden'=>0,'sort'=>17],
				['id'=>2918,'title'=>'导航栏文本色','key'=>'decoration_navbar_text_color','form_type'=>'color-picker','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>29,'number'=>0,'tag'=>'decoration','hidden'=>0,'sort'=>18],
				['id'=>2919,'title'=>'导航栏二级文本色','key'=>'decoration_sub_text_color','form_type'=>'color-picker','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>29,'number'=>0,'tag'=>'decoration','hidden'=>0,'sort'=>19],
				['id'=>2920,'title'=>'导航栏激活文本色','key'=>'decoration_navbar_text_active_color','form_type'=>'color-picker','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>29,'number'=>0,'tag'=>'decoration','hidden'=>0,'sort'=>20],
				['id'=>2921,'title'=>'底部背景色','key'=>'decoration_bottom_bg_color','form_type'=>'color-picker','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>29,'number'=>0,'tag'=>'decoration','hidden'=>0,'sort'=>21],
				['id'=>2922,'title'=>'底部文本色','key'=>'decoration_bottom_text_color','form_type'=>'color-picker','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>29,'number'=>0,'tag'=>'decoration','hidden'=>0,'sort'=>22],
				['id'=>2923,'title'=>'联系人','key'=>'decoration_contact','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>29,'number'=>0,'tag'=>'decoration','hidden'=>0,'sort'=>23],
				['id'=>2924,'title'=>'邮箱地址','key'=>'decoration_email','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>29,'number'=>0,'tag'=>'decoration','hidden'=>0,'sort'=>24],
				['id'=>2925,'title'=>'联系地址','key'=>'decoration_address','form_type'=>'input','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>29,'number'=>0,'tag'=>'decoration','hidden'=>0,'sort'=>25],
				['id'=>2926,'title'=>'登录页背景图','key'=>'decoration_login_bg_img','form_type'=>'upload_img','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>29,'number'=>0,'tag'=>'decoration','hidden'=>0,'sort'=>26],
				['id'=>2927,'title'=>'登录页装饰图','key'=>'decoration_login_pic','form_type'=>'upload_img','value_type'=>1,'rows'=>0,'default_value'=>'','size'=>'default','limit'=>0,'options'=>'','prefix'=>'','suffix'=>'','level'=>2,'pid'=>29,'number'=>0,'tag'=>'decoration','hidden'=>0,'sort'=>27],
				['id'=>2928,'title'=>'登录方式','key'=>'system_status','form_type'=>'checkbox','value_type'=>2,'rows'=>0,'default_value'=>'[1,2]','size'=>'default','limit'=>0,'options'=>'{"账号登录":1,"短信登录":2,"微信登录":3}','prefix'=>'','suffix'=>'','level'=>2,'pid'=>29,'number'=>0,'tag'=>'decoration','hidden'=>0,'sort'=>28],
			];

			for($i=0;$i<count($data);$i++){
				$data[$i]['value'] = $data[$i]['default_value'];
			}

			return $data;
		}

		protected function getKeyList(Request $request,$key = ''){
			try{
				if(empty($key) || !$key){
					return [];
				}
				if(is_string($key)){
					$key = \explode(',',$key);
				}
				// var_dump('key: ',$key);
	    		$field = ['ID as id','Title as title','Key as key','Value as value','FormType as form_type','ValueType as value_type','DefaultValue as default_value','Limit as limit'];
	    		$object = Db::table($this->table)
	    					->select(...$field)
	    					->where([['Level','=',2],['FormType','<>','divider']])
	    					->whereIn('Tag',$key)
	    					->get();
	    		// var_dump($object);
	    		if($object){
	    			$data = [];
	    			foreach($object as $item){
	    				$value = $item->value;

                    	switch($item->value_type){
                    		case 2:
                    		case 3:
                    			$value = $value ? (int)$value : 0;
                    			break;
                    		case 4:
                    			$value = $value ? \round($value,1) : 0;
                    			break;
                    		case 5:
                    			$value = $value ? \round($value,2) : 0;
                    			break;
                    		case 6:
                    			$value = $value ? \round($value,3) : 0;
                    			break;
                    		case 7:
                    			$value = $value ? \round($value,4) : 0;
                    			break;
                    		case 8:
                    			$value = $value ? ($value ? 1 : 0) : 0;
                    			break;
                    		case 9:
                    			$value = $value ? ($value ? true : false) : false;
                    			break;
                    		case 10:
                    			$value = $value ? $this->getDecodeData($value) : [];
                    			break;
                    		case 11:
                    			$value = $value ? $this->getDecodeData($value,true) : [];
                    			break;
                    		default:
                    	}
	    				$data[$item->key] = $value;
	    			}
	    			return $data;
	    		}
	    		return [];
			}catch(\Exception $e){
				return [];
			}
		}

		protected function getKeysList(Request $request,$key = ''){
			try{
				if(empty($key) || !$key){
					return [];
				}
				if(is_string($key)){
					$key = \explode(',',$key);
				}
	    		$field = ['ID as id','Title as title','Key as key','Value as value','FormType as form_type','ValueType as value_type','DefaultValue as default_value','Limit as limit'];
	    		$object = Db::table($this->table)
	    					->select(...$field)
	    					->where('Level',2)
	    					->whereIn('Tag',$key)
	    					->get();
	    		return $object;
			}catch(\Exception $e){
				return [];
			}
		}

		protected function getFieldnameList(Request $request,$field = ''){
			try{
				if(!$field){
					return 100007;
				}

				if(!is_array($field)){
					$field = \explode(',',$field);
				}
				var_dump('field: ',$field);
				// $fields = [];
				// foreach($field as $item){
				// 	if($this->fieldExists($item)){
				// 		array_push($fields,$this->convert($item) . ' as ' . $this->convert($item,false));
				// 	}
				// }
				var_dump('fields: ',$field);
				if(!$field){
					return 100007;
				}

				$object = Db::table($this->table)
							->select(['Key as key','Value as value'])
							->whereIn('Key',$field)
							->get();

				var_dump($object);
				$result = [];
				if($object){
					foreach($object as $item){
						$result[$item->key] = $item->value;
					}
				}
				var_dump($result);
				return $result;
			}catch(\Exception $e){
				var_dump($this->getExceptionError($e));
				return $this->getExceptionError($e);
			}
		}

		protected function setExcute(Request $request,$data,$flag = ''){
			// var_dump($data);
			switch($flag){
				case '':
				case 'add':
					return true;
				default:
					return true;
			}
		}

		// public function add(Request $request): mixed
		// {
		// 	return '非法操作';
		// }

		public function mod(Request $request,$id = 0): mixed
		{
			try{
				var_dump('mod id: ',$id);
				var_dump('data: ',$request->post());
				if(strtolower($request->method()) !== 'post'){
					return 100000;
				}
				$key = $request->post('key',$request->post('id',''));
				if(empty($key) || !$key){
					return '无效的数据1';
				}
	    		
	    		$post = $request->post();
	    		if(!$post || !is_array($post) || !count($post)){
	    			return '无效的数据2';
	    		}

	    		$data = $this->getList($request,'keys',$key);
	    		if(!$data){
	    			return '无效的数据3';
	    		}

	    		$num = 0;
	    		$count = 0;
	    		foreach($data as $k => $v){
	    			if(isset($post[$v->key])){
	    				$value = is_array($post[$v->key]) ? $this->getJsonData($post[$v->key]) : $post[$v->key];
	    				// $value = is_array($post[$v->key]) ? $this->getJsonData($post[$v->key]) : $post[$v->key];
	    				// var_dump('value: ',$value);
	    				$sql = "UPDATE `".$this->tab."` SET `Value` = ? WHERE `ID` = ?";
	    				$result = Db::update($sql,[$value,$data[$k]->id]);
	    				if($result !== false){
	    					$num++;
	    				}else{
	    					$count++;
	    				}
	    			}
	    		}

	    		if(\method_exists($this, 'setExcute')){
	    			if(!$this->setExcute($request,$data,'mod')){
	    				return '数据修改成功但回调失败';
	    			}
	    		}
	    		// var_dump($data);
	    		// return 'aa';
	    		return ['success' => $num,'fail'=> $count];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		protected function getTagList(Request $request,$tag = []){
			if(!$tag){
				$tag = $request->input('id','');
				if(!$tag){
					return [];
				}
				if(!is_array($tag)){
					$tag = explode(',',$tag);
				}
			}

			$field = $this->getList($request,'field');
			$object = Db::table($this->table)
						->select(...$field)
						->whereIn("Tag",$tag)
						->orderBy('Level','asc')
						->orderBy('Sort','asc')
						->get();
			return $object ? $this->child($object) : [];
		}

		protected function validate(Request $request,$id = 0,$option = null){
			$level = $request->post('level',1);
			$pid = $request->post('pid',0);

			if($level > 1 && !$pid){
				return '请选择父级';
			}
			if($pid){
				$parent = $this->getList($request,$pid);
				if(!$parent || !is_object($parent) || !property_exists($parent,'id')){
					return '无效的父级';
				}
			}

			$title = trim($request->post('title',''));
			$key = trim($request->post('key',''));
			$tag = trim($request->post('tag',''));
			$sort = $request->post('sort',1);

			if(!$title){
				return ($pid?'参数名称':'标题类别').'不能为空';
			}
			if($this->checkExists(['PID'=>$pid,'Title'=>$title],$id)){
				return '同级'.($pid?'参数名称':'标题类别').'已存在';
			}
			if(!$key){
				return '字段值不能为空';
			}
			if($this->checkExists(['Key'=>$key],$id)){
				return '字段值已存在,请重新输入!';
			}

			if($level > 1){
				$type = $request->post('form_type',[]);
				$type = is_array($type) ? end($type) : $type;
				$value_type = $request->post('value_type',[]);
				if(is_array($value_type)){
					$value_type = end($value_type);
				}
				if(!$value_type){
					return '请选择数值类型';
				}
				$defaults = $request->post('defaults',[]);
				$default_value = isset($defaults['default_value']) ? $defaults['default_value'] : (isset($defaults[1]) ? $defaults[1] : '');
				$rows = $request->post('rows',0);
				if(empty($rows)){
					$rows = 0;
				}
				if(!is_numeric($rows) || $rows < 0){
					return '行数只能填数字';
				}
				$size = isset($defaults['size']) ? $defaults['size'] : (isset($defaults[0]) ? $defaults[0] : '');
				$limit = $request->post('limit',1);
				if(empty($limit)){
					$limit = 1;
				}
				if(!is_numeric($limit) || $limit < 0){
					return '上传限制数量只能填数字';
				}
				$options = trim($request->post('options',''));
				$puffix = $request->post('puffix',[]);
				$prefix = isset($puffix['prefix']) ? $puffix['prefix'] : (isset($puffix[0]) ? $puffix[0] : '');
				$suffix = isset($puffix['suffix']) ? $puffix['suffix'] : (isset($puffix[1]) ? $puffix[1] : '');

				$data['form_type'] = $type;
				$data['value_type'] = $value_type;
				$data['default_value'] = $default_value;
				$data['rows'] = $rows;
				$data['size'] = $size;
				$data['limit'] = $limit;
				$data['options'] = $options;
				$data['prefix'] = $prefix;
				$data['suffix'] = $suffix;

			}

			if(!$pid &&!$tag){
				return '标签名称不能为空';
			}
			if($this->checkExists(['PID'=>$pid,'Tag'=>$tag],$id)){
				return '同级标签名称已存在';
			}
			if(empty($sort) || !is_numeric($sort) || $sort < 1){
				return '排序只能填数字且大于0';
			}

			$data['title'] = $title;
			$data['level'] = $level;
			$data['pid']   = $pid;
			$data['tag']   = $pid ? $parent->tag : $tag;
			$data['sort']  = $sort;

			// var_dump($data);
			// return 'aa';

			return $data;
		}

		protected function getValuetypeList(Request $request){
			return [
				['label'=>'字符','value'=>11,'children'=>[
					['label'=>'字符串','value'=>1]
				]],
				['label'=>'整数','value'=>12,'children'=>[
					['label'=>'正整数','value'=>2],
					['label'=>'有符号','value'=>3],
				]],
				['label'=>'小数','value'=>12,'children'=>[
					['label'=>'1位精度','value'=>4],
					['label'=>'2位精度','value'=>5],
					['label'=>'3位精度','value'=>6],
					['label'=>'4位精度','value'=>7],
				]],
				['label'=>'布尔','value'=>13,'children'=>[
					['label'=>'01值','value'=>8],
					['label'=>'真假值','value'=>9],
				]],
				['label'=>'数组','value'=>13,'children'=>[
					['label'=>'字符数组','value'=>10],
					['label'=>'整数数组','value'=>11],
					['label'=>'对象数组','value'=>12],
				]],
			];
		}

		private function getChildrenOptions($option,$type = 'select'){
			if(is_string($option)){
        		$option = $this->getDecodeData($option);
        	}

        	if(!$option){
        		return [];
        	}

        	$temp = [];
        	foreach($option as $key => $val){
        		if(in_array($type,['radio','checkbox'])){
        			array_push($temp,['label'=>$key,'value' => $val]);
        		}else{
        			array_push($temp,['type'=>'option','label'=>$key,'value' => $val]);
        		}
        	}

        	return $temp;
		}

		protected function getActionList(Request $request,$id = 0): array
		{
			$action_name = $request->input('action','');
			if(!$action_name){
				if($id){
					$data = $this->getList($request,$id);
				}

				$level = $request->input('level',1);
				$pid = $request->input('pid',0);
				$sort = $this->getMaxSort($pid);
				$formtype = $this->getList($request,'formtype');
				$valuetype = $this->getList($request,'valuetype');

				$levelOptions = [
					['label'=>'1层','value'=>1],
					['label'=>'2层','value'=>2],
				];
				$pidOptions = $this->getList($request,'option',['Level'=>1]);
				$flag = ((!$id && !$pid) || ($id && !$data->pid)) ? false : true;

				if($flag){
					$action = [
						['type'=>'input','label'=>'参数名称','prop'=>'title','value'=>$data->title ?? '','required'=>true],
						['type'=>'input','label'=>'字段名称','prop'=>'key','value'=>$data->key ?? '','required'=>true],
						['type'=>'select','label'=>'层级','prop'=>'level','value'=>$data->level ?? $level,'hidden'=>true,'disabled'=>true,'children'=>$levelOptions],
						['type'=>'select','label'=>'父级','prop'=>'pid','value'=>$data->pid ?? $pid,'hidden'=>true,'disabled'=>true,'children'=>$pidOptions],
						['type'=>'cascader','label'=>'表单类型','prop'=>'form_type','value'=>$data->form_type ?? [],'children'=>$formtype,'required'=>true],
						['type'=>'cascader','label'=>'值类型','prop'=>'value_type','value'=>$data->value_type ?? [],'children'=>$valuetype,'required'=>true],
						['type'=>'group','label'=>'前后缀','prop'=>'puffix','value'=>$id ? ['prefix'=>$data->prefix,'suffix'=>$data->suffix] : [],'delimiter'=>'-','children'=>[
							['type'=>'input','label'=>'前缀','prop'=>'prefix','value'=>$data->prefix ?? '','prefix'=>'前缀','placeholder'=>' ','width'=>300],
							['type'=>'input','label'=>'后缀','prop'=>'suffix','value'=>$data->suffix ?? '','prefix'=>'后缀','placeholder'=>' ','width'=>300],
						]],
						['type'=>'group','label'=>'长度默认值','prop'=>'defaults','value'=>$id ? ['prefix'=>$data->prefix,'suffix'=>$data->suffix] : [],'delimiter'=>'-','children'=>[
							['type'=>'input','label'=>'长度','prop'=>'size','value'=>$data->size ?? '','prefix'=>'长度','suffix'=>'位','placeholder'=>' ','width'=>300],
							['type'=>'input','label'=>'默认值','prop'=>'default_value','value'=>$data->default_value ?? '','prefix'=>'默认值','placeholder'=>' ','width'=>300],
						]],
						['type'=>'input','label'=>'选项值','prop'=>'options','value'=>$data->options ?? '','attrs'=>['type'=>'textarea','rows'=>3]],
						['type'=>'input-number','label'=>'限制量','prop'=>'limit','value'=>$data->limit ?? 1,'width'=>400,'attrs'=>['min'=>1,'controls-position'=>'right']],
						['type'=>'switch','label'=>'管理员权限','prop'=>'is_admin','value'=>$data->is_admin ?? 0],
						['type'=>'switch','label'=>'隐藏','prop'=>'hidden','value'=>$data->hidden ?? 0],
						['type'=>'input','label'=>'排序','prop'=>'sort','value'=>$data->sort ?? $sort,'required'=>true],
					];
				}else{
					$action = [
						['type'=>'input','label'=>'标题名称','prop'=>'title','value'=>$data->title ?? '','required'=>true],
						['type'=>'input','label'=>'字段值','prop'=>'key','value'=>$data->key ?? '','required'=>true],
						['type'=>'input','label'=>'标签名','prop'=>'tag','value'=>$data->tag ?? '','required'=>true],
						['type'=>'input','label'=>'排序','prop'=>'sort','value'=>$data->sort ?? $sort,'required'=>true],
					];
				}

				return $action;
			}elseif($action_name === 'key'){
				$data = $this->getList($request,'tag');
				if(!$data){
					return [];
				}
				// var_dump($data[0]);
				$action = [];
				$user_is_admin = $this->checkIsAdmin($request);
				foreach($data as $items){
			        $children = [];
			        if(property_exists($items,'children') && $items->children){
			            foreach($items->children as $item){
			            	$type = $item->form_type;
			            	$label = $item->label ?? $item->title;
			            	$prop = $item->key;
			            	$value = $item->value;
			            	if($type === 'switch' || in_array($item->value_type, [2,3,8])){
			            		$value = (int)$value;
			            	}
			            	$width = $item->width ?? 0;
			            	$placeholder = $item->placeholder ?? '';
			            	$prefix = $item->prefix;
			            	$suffix = $item->suffix;
			            	$min = $item->min ?? 1;
			            	$max = $item->max ?? 0;
			            	$delimiter = '';
			            	$hidden = $item->hidden ?? false;
			            	$is_admin = $item->is_admin ?? false;
			            	$attrs = [];
			            	$child = [];

			            	switch($type){
			            		case 'password':
			            			$type = 'input';
			            			$attrs['type'] = 'password';
			            			break;
			            		case 'textarea':
			            			$type = 'input';
			            			$attrs['type'] = 'textarea';
			            			break;
			            		case 'number':
			            			$type = 'input-number';
			            			$attrs['min'] = $min;
			            			if($max){
			            				$attrs['max'] = $max;
			            			}
			            			$attrs['controls-position'] = 'right';
			            			break;
			            		case 'upload_img':
			            		case 'upload_logo':
			            		case 'upload_face':
			            			$type = 'upload';
			            			$attrs = $this->getUploadOptions();
			            			break;
			            		case 'upload_picture':
			            			$type = 'upload';
			            			$attrs = $this->getUploadOptions('card',5,$item->size ?? 'default');
			            			break;
			            		case 'upload_file':
			            			$type = 'upload';
			            			$attrs = $this->getUploadOptions('file');
			            			break;
			            		case 'upload_cert':
			            			$type = 'upload';
			            			$attrs = $this->getUploadOptions('cert');
			            			break;
			            		case 'upload_voice':
			            			$type = 'upload';
			            			$attrs = $this->getUploadOptions('voice');
			            			break;
			            		case 'upload_video':
			            			$type = 'upload';
			            			$attrs = $this->getUploadOptions('video');
			            			break;
			            		case 'select_multi':
			            			$type = 'select';
			            			$attrs['multiple'] = true;
			            			break;
			            		case 'checkbox':
			            			$type = 'checkbox-group';
			            			$child = $this->getChildrenOptions($item->options,'checkbox');
			            			break;
			            		case 'radio':
			            			$type = 'radio-group';
			            			$child = $this->getChildrenOptions($item->options,'radio');
			            			break;
			            		case 'datetime':
			            		case 'datetimes':
			            			$type = 'date';
			            			break;
			            		case 'year':
			            			$type = 'date';
			            			break;
			            		case 'month':
			            			$type = 'date';
			            			break;
			            		case 'day':
			            			$type = 'date';
			            			break;
			            		case 'hour':
			            			$type = 'date';
			            			break;
			            		case 'group':
			            		case 'multiple':
			            		case 'table':
			            		case 'description':
			            			$delimiter = '-';
			            			$child = $this->getChildrenOptions($item->options);
			            			break;
			            		case 'milut_form':
			            			$type = 'multiple';
			            			$delimiter = '-';
			            			$child = $this->getChildrenOptions($item->options);
			            			break;
			            		case 'editor':
			            			$attrs = $this->getEditorOptions();
			            			break;
			            		case 'district':
			            		case 'province_city':
			            		case 'province':
			            			$type = 'city';
			            			break;
			            		default:
			            	}


			            	$option = ['type'=>$type,'label'=>$label,'prop'=>$prop,'value'=>$value];
			            	if($width){
			            		$option['width'] = $width;
			            	}
			            	if($placeholder){
			            		$option['placeholder'] = $placeholder;
			            	}
			            	if($prefix){
			            		$option['prefix'] = $prefix;
			            	}
			            	if($suffix){
			            		$option['suffix'] = $suffix;
			            	}
			            	if($delimiter){
			            		$option['delimiter'] = $delimiter;
			            	}
			            	if($attrs){
			            		$option['attrs'] = $attrs;
			            	}
			            	if($child){
			            		$option['children'] = $child;
			            	}
			            	if($hidden){
			            		$option['hidden'] = $hidden;
			            	}
			            	// var_dump('options: ',$option);
			            	if(!$is_admin || ($is_admin && $user_is_admin)){
			            		array_push($children,$option);
			            	}
			            }
			        }

			        $items->children = $children;
			        array_push($action,$items);
			    }
			    
			    return $action;
			}
		}

		public function reload(Request $request){
			$output = [];
			$return_var = 0;
			$path = $request->post('path',dirname(dirname(dirname(dirname(dirname(__DIR__))))) . '/dackou-webman-gt');
			if(is_file($path.'/start.php')){
				$command = "php {$path}/start.php reload";
				\exec($command, $output, $return_var);

				return true;
			}
			return false;
		}

		protected function getMapList(Request $request): array
		{
			return [
				['type'=>'id','label'=>'ID','prop'=>'id','align'=>'start'],
				['type'=>'varchar','label'=>'标题','prop'=>'title','align'=>'start'],
				['type'=>'varchar','label'=>'名称','prop'=>'key'],
				['type'=>'varchar','label'=>'表单类型','prop'=>'form_type'],
				['type'=>'varchar','label'=>'值类型','prop'=>'value_type_name'],
				['type'=>'varchar','label'=>'默认值','prop'=>'default_value','width'=>120],
				// ['type'=>'varchar','label'=>'选项值','prop'=>'options','width'=>200],
				['type'=>'varchar','label'=>'标签','prop'=>'tag'],
				['type'=>'varchar','label'=>'排序','prop'=>'sort'],
			];
		}
	}
?>