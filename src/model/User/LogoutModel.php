<?php
	namespace dackou\model\User;

	use support\Request;

	class LogoutModel extends \dackou\Model{
		protected $table = 'User';
		protected $title = '用户';
		protected $primaryKey = 'uid';
		protected $option_key = 'user';

		public function setLogout(Request $request){
			$uid = $this->getSession($request,'uid');
			if($uid){
				// $result = $this->updateData($request,['IsOnline'=>0],$uid);
				$result = $this->updateData($request,['IsOnline' => 0],$uid);
				if($result !== false){
					$this->removeSession($request);
					return true;
				}
			}
			$this->removeSession($request);
			return true;
		}
	}
?>