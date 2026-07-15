<?php
	namespace dackou\model\Order;

	use support\Request;

	class SignService{
		public function setSign(Request $request,$type = 'mini'){
			try{
    			$user_id = $request->post('user_id');
				if(!$user_id){
					return '无效的用户';
				}

				$_class_name = $this->getClassName('User');
				$service = new $_class_name();
				$user = $service->getList($request,$user_id);
				if(!$user || !is_object($user)){
					return $user;
				}
				
				$order = $request->post('order');
				if(!$order){
					return 100007;
				}

				$res = $this->getList($request,$order);
				if(!$res || !is_object($res)){
					return $res;
				}
				// var_dump('签名订单：');
				// var_dump($res);
				if($res->user_id != $user_id){
					return '无效的用户';
				}

				if($res->status == -2){
					return '订单已锁定';
				}
				if($res->status == -1){
					return '订单已取消';
				}
				if($res->status == 0){
					return '订单未确认';
				}
				if($res->status == 2){
					return '订单已支付';
				}
				if($res->status !== 1){
					return '无效的订单状态';
				}

				if($res->order_type < 100 && !$res->goods){
					return '无效的商品';
				}

				$openid = $res->openid;
				if(!$openid){
					return '无效的openid';
				}
				$total_amount = $res->payable_amount ? $res->payable_amount * 100 : $res->total_amount * 100;
				var_dump('sign total_amount: '.$total_amount);
				if($this->config['order_test_pay'] && $this->config['order_test_pay_fee']){
					$total_amount = (int)$this->config['order_test_pay_fee'];
				}
				// var_dump('total_amount: '.$total_amount);
				
				$description = trim($request->post('desc','商城会员订单支付'));
				if($res->order_type < 100){
					foreach($res->goods as $item){
						// $description .= $item->goods_title . $item->first_title;
						// if($item->second_title){
						// 	$description .= $item->second_title;
						// }
					}
				}else{
					if($res->order_type == 100){
						$description = '会员退款';
					}

					if($res->order_type == 10){
						$description = '会员充值';
					}
				}
				var_dump('原total_amount: '.$total_amount);
				$total_amount = (string)$total_amount;
				$data = [
				    'out_trade_no' => $order . '_' . time(),
				    'description'  => $description,
				    'amount' => [
				        'total' 	=> (int)$total_amount,
				        'currency'  => 'CNY',
				    ],
				    'payer' => [
				        'openid' => $openid,
				    ]
				];
				var_dump('传递的签名数据：');
				var_dump($data);
				$service = new \dackou\service\Yansongda\YansongdaService();
				$result = $service->sign($request,$data,$type);
				var_dump('order sign result:');
				var_dump($result);
				return $result;
    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
		}
	}
?>