<?php
	namespace dackou\model\User;

	use support\Request;

	class AuthModel extends \dackou\Model{
		protected $table = 'User';
		protected $title = '用户';
		protected $primaryKey = 'uid';
		protected $option_key = 'user';
    	protected $truncate = false;

    	/**
    	 * 统一认证入口
    	 * @param  Request $request [description]
    	 * @return [type]           [description]
    	 */
    	public function checkAuth(Request $request){
    		$type = trim($request->input('type','person'));
    		$user_id = $request->post('user_id',0);
    		if(!$user_id || !is_numeric($user_id) || $user_id <= 0){
    			return '无效的认证用户';
    		}

    		$service = new UserModel();
    		$user = $service->getList($request,$user_id);
    		if(!$user || !is_object($user)){
    			return '无效的认证用户';
    		}

    		if($user->group_id < 14){
    			return '非法认证';
    		}

    		if($user->status !== 1){
    			return '无效的用户状态';
    		}

    		if($user->is_auth && $user->auth_type == $type){
    			return '用户已完成该认证';
    		}

    		$_method = 'checkAuthBy' . \ucfirst($type);
    		if(\method_exists($this,$_method)){
    			return $this->$_method($request,$user);
    		}

    		return 100007;
    	}

    	// 个人认证
    	private function checkAuthByPerson(Request $request,$user){

    	}

    	// 企业认证
    	private function checkAuthByEnterprise(Request $request,$user){

    	}

    	// 商户认证
    	private function checkAuthByMerchant(Request $request,$user){

    	}
	}
?>