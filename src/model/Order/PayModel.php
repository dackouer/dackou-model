<?php
	namespace dackou\model\Order;

	use support\Request;

	class PayModel extends OrderModel{
		private $pay_value = [
			['id'=>1,'title'=>'微信支付','value'=>'wechat','disabled'=>true],
			['id'=>2,'title'=>'支付宝支付','value'=>'alipay','disabled'=>true],
			['id'=>3,'title'=>'银行卡支付','value'=>'bank','disabled'=>true],
			['id'=>4,'title'=>'余额支付','value'=>'balance','disabled'=>true],
			['id'=>5,'title'=>'积分支付','value'=>'score','disabled'=>true],
		];

		/**
		 * 统一支付
		 * @param Request $request [description]
		 */
    	public function setPay(Request $request){
    		if(isset($this->config['is_pay']) && !$this->config['is_pay']){
    			return '系统关闭支付，请稍后再试'
    		}

    		$pay_type = $request->post('pay_type');
    		if(!$pay_type || !$this->checkPayType($pay_type)){
    			return '无效的支付方式';
    		}

    		$user_id = $request->post('user_id');
    		if(is_null($user_id) || empty($user_id) || !$user_id){
    			return '无效的用户';
    		}
    		$user_class = $this->getClassName('User');
    		if(!$user_class){
    			return '无效的用户';
    		}
    		$service = new $user_class();
    		$user = $service->getList($request,$user_id);
    		if(!$user || !is_object($user)){
    			return $user;
    		}

    		if($user->status == -2){
    			return '用户已注销';
    		}

    		if($user->status == -1){
    			return '用户已删除';
    		}

    		if($user->status == 0){
    			return '用户未激活';
    		}

    		if($user->status == 2){
    			return '用户已锁定';
    		}

    		if($user->status !== 1){
    			return '用户状态无效,禁止操作';
    		}

    		if(!$user->is_auth){
    			return '用户未实名';
    		}
    		if($user->birth && (isset($this->config['min_age']) && $this->config['min_age'])){
    			$age = $this->getAge($user->birth);
    			if($age < $this->config['min_age']){
    				return '用户年龄小于'.$this->config['min_age'].'岁,不允许下单支付';
    			}

    			if(isset($this->config['max_age']) && $this->config['max_age']){
    				if($age > $this->config['max_age']){
	    				return '用户年龄大于'.$this->config['max_age'].'岁,不允许下单支付';
	    			}
    			}
    		}

    		$order_code = $request->post('order');
    		if(is_null($order_code) || empty($order_code) || !$order_code){
    			return '无效的订单';
    		}

    		$order = $this->getList($request,$order_code);
    		if(!$order || !is_object($order)){
    			return '无效的订单';
    		}
    		if($order->user_id !== $user_id){
    			return '无效的用户';
    		}
    		//[0=>'待确认',1=>'待支付',2=>'待发货',3=>'待收货',-1=>'已取消',-2=>'已锁定',10=>'已退款'];
    		if($order->status == 0){
    			return '订单未确认';
    		}
    		if(in_array($order->status,[2,3])){
    			return '订单已支付';
    		}
    		if($order->status == -1){
    			return '订单已取消';
    		}
    		if($order->status == -2){
    			return '订单已锁定';
    		}
    		if($order->status == 10){
    			return '订单已退款';
    		}
    		if($order->status !== 1){
    			return '无效的订单状态';
    		}

    		try{
    			$_method = "setPayBy".ucfirst($type);
    			$result = $this->$_method($request,$order,$user);
    			if($result !== false){
    				return $this->setStatus($request,$order,$user,$type);
    			}
    			return $result;
    		}catch(\Exception $e){
    			return '支付失败: '.$e->getMessage();
    		}
    	}

    	private function checkPayType($pay_type){
    		$flag = false;
    		foreach($this->pay_value as $item){
    			if($item['value'] == $pay_type){
    				$flag = true;
    				break;
    			}
    		}
    		return $flag;
    	}

    	/**
    	 * 微信支付
    	 * @param Request $request [description]
    	 */
    	private function setPayByWechat(Request $request,$order,$user){
    		try{
    			if(isset($this->config['is_pay_wechat']) && !$this->config['is_pay_wechat']){
    				return '系统关闭了微信支付，请稍后再试';
    			}
    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
    	}

    	/**
    	 * 支付宝支付
    	 * @param Request $request [description]
    	 */
    	private function setPayByAlipay(Request $request,$order,$user){
    		try{
    			if(isset($this->config['is_pay_alipay']) && !$this->config['is_pay_alipay']){
    				return '系统关闭了支付宝支付，请稍后再试';
    			}
    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
    	}

    	/**
    	 * 银行支付
    	 * @param Request $request [description]
    	 */
    	private function setPayByBank(Request $request,$order,$user){
    		try{
    			if(isset($this->config['is_pay_bank']) && !$this->config['is_pay_bank']){
    				return '系统关闭了银行卡支付，请稍后再试';
    			}
    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
    	}

    	/**
    	 * 余额支付
    	 * @param Request $request [description]
    	 */
    	private function setPayByBalance(Request $request,$order,$user){
    		try{
    			if(isset($this->config['is_pay_balance']) && !$this->config['is_pay_balance']){
    				return '系统关闭了余额支付，请稍后再试';
    			}

    			if(!$user->balance || ($user->balance*100 < $order->payable_amount*100)){
    				return '用户余额不足';
    			}
    			$amount = $order->payable_amount*100;

    			$data = [
    				'PayAmount' => $amount,
    				'PayTime'	=> time(),
    				'Platform'	=> 'balance',
    				'PayType'	=> 'balance',
    				'PayStatus'	=> 2,
    				'Status'	=> 2
    			];
    			return $this->updateData($request,$data,$order->order);
    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
    	}

    	/**
    	 * 积分支付
    	 * @param Request $request [description]
    	 */
    	private function setPayByScore(Request $request,$order,$user){
    		try{
    			if(isset($this->config['is_pay_score']) && !$this->config['is_pay_score']){
    				return '系统关闭了积分支付，请稍后再试';
    			}
    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
    	}

    	/**
    	 * 统一处理支付后状态
    	 * @param Request $request [description]
    	 * @param [type]  $data    [description]
    	 * @param [type]  $order   [description]
    	 */
    	private function setStatus(Request $request,$order,$user,$type){
    		if($type == 'balance'){
    			var_dump('更新用户余额');
    			$this->updateUserBalance($request,$order,$user);
    		}	
    		var_dump('更新商品库存');
    		$this->updateGoodsStore($request,$order,$user);
    		if($user->parent_id){
    			var_dump('更新会员分佣')
    			$this->updateUserComment($request,$order,$user);
    		}
    	}
	}
?>