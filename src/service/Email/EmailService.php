<?php
	namespace dackou\service\Email;

	use support\Request;
	use dackou\Preg;
	use yzh52521\mailer\Mailer;

	class EmailService{
		private static $config = [];

		public function sendEmail(Request $request,$data = []){
			$path = config_path('plugin/yzh52521/mailer/app.php');
			if(is_file($path)){
				self::$config = include($path);
			}
			
			$from = isset($data['from']) ? $data['from'] : trim($request->post('from',isset(self::$config['from']['address']) ? self::$config['from']['address'] : ''));
			$to = isset($data['to']) ? $data['to'] : trim($request->post('to',''));
			$subject = isset($data['subject']) ? $data['subject'] : trim($request->post('subject',''));
			$body = isset($data['body']) ? $data['body'] : trim($request->post('body',''));

			if(empty($from)){
				return 110301;
			}

			if(!Preg::isEmail($from)){
				return 110302;
			}

			if(empty($to)){
				return 110303;
			}

			if(!Preg::isEmail($to)){
				return 110304;
			}

			if(empty($subject)){
				return 110305;
			}

			if(empty($body)){
				return 110306;
			}

			try{
				$result = Mailer::setFrom($from)
					->setTo($to)
					->setSubject($subject)
					->setTextBody($body)
					->send();
				// var_dump('send email result: ');
				// var_dump($result);
				return $result;
			}catch(\Exception $e){
				// var_dump($e->getMessage());
				return ['code'=>$e->getCode() ? $e->getCode() : 1,'msg'=>$e->getMessage()];
			}
		}
	}
?>