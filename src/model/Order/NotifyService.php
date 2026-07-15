<?php
	namespace dackou\model\Order;

	use support\Request;

	class NotifyService{
		private $config = [];

		/**
		 * 统一回调
		 * @param Request $request [description]
		 */
		public function setNotify(Request $request,$type = 'mini'){
			try{
				$service = new \dackou\service\Yansongda\YansongdaService();
				$post = $service->notify($request);
				if(!$post || !isset($post['event_type'])){
					return '无效的回调信息';
				}

				$type = $post['event_type'];

				switch($type){
					case 'TRANSACTION.SUCCESS':					// 支付回调
						return $this->setTransactionNotify($post);
					case 'REFUND.SUCCESS':						// 退款回调
						return $this->setRefundNotify($post);
					default:
						return '无效的回调类型';
				}

			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		/**
		 * 处理支付回调
		 * @param [type] $post [description]
		 */
		private function setTransactionNotify($post){
			try{
				var_dump('收到的是支付回调:');
				$ciphertext = isset($post["resource"]["ciphertext"]) ? $post["resource"]["ciphertext"] : '';
				if($ciphertext && isset($ciphertext["trade_state"]) && $ciphertext["trade_state"] === "SUCCESS"){
					var_dump('支付成功,验证数据:');
					$original_type = $post['resource']['original_type'];
					$mchid = $ciphertext["mchid"];
					$appid = $ciphertext["appid"];
					$trade_type = $ciphertext["trade_type"];
					$openid = $ciphertext["payer"]["openid"];
					$amount = $ciphertext["amount"]["total"];
					[$out_trade_no,$time] = explode("_",$ciphertext["out_trade_no"]);
					$transaction_id = $ciphertext["transaction_id"];

					var_dump('mchid: '.$mchid);
					var_dump('appid: '.$appid);
					var_dump('trade_type: '.$trade_type);
					var_dump('openid: '.$openid);
					var_dump('amount: '.$amount);
					var_dump('out_trade_no: '.$out_trade_no);

					if(!$out_trade_no){
						return '无效的订单号';
					}
					// var_dump($this->config);
					if($mchid !== $this->config['merchant_mch_id']){
						return '商户号错误';
					}
					if($appid !== $this->config['mini_appid']){
						return 'appid错误';
					}

					$_class_name = $this->getClassName();
					$service = new $_class_name();
					$data = $service->getList($request,$out_trade_no);
					// var_dump($data);
					if(!$data || !is_object($data)){
						return '无效的订单';
					}

					if($openid !== $data->openid){
						return '无效的openid';
					}
					
					// $data_total_amout = $data->payable_amount;
					// if(isset($this->config['order_test_pay']) && $this->config['order_test_pay'] && isset($this->config['order_test_pay_fee']) && $this->config['order_test_pay_fee']){
					// 	$data_total_amout = (int)$this->config['order_test_pay_fee'];
					// }
					// if($amount * 100 !== $data_total_amout * 100){
					// 	var_dump('ff');
					// 	var_dump('amount: '.$amount);
					// 	var_dump('total: '.$data->total_amount);
					// 	return '无效的支付金额';
					// }

					if($data->status == -2){
						return '订单锁定';
					}

					if($data->status == -1){
						return '订单取消';
					}

					if($data->status == 2){
						return true;
					}

					if($data->status != 1){
						return '无效的订单状态';
					}
					
					$data->trade_type = $trade_type;
					$data->original_type = $original_type;
					$data->source_order = $ciphertext["out_trade_no"];
					$data->transaction_id = $transaction_id;
					$data->pay_amount = $amount;
					$data->transaction_data = $this->getJsonData($post);

					return $service->setStatus($request,$data);
				}

				return '验证失败';
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		/**
		 * 处理退款回调
		 * @param [type] $post [description]
		 */
		private function setRefundNotify($post){

		}

		private function setConfig(){
			if(!$this->config){
				$config = \config('payment');
				if($config){
					$this->config['merchant_mch_id'] = $config['wechat']['default']['mch_id'];
					$this->config['mini_appid'] = $config['wechat']['default']['mini_app_id'];
				}
			}
		}

		private function getClassName(){
			$_class_name = '\app\model\Order\OrderModel';
			if(\method_exists($_class_name)){
				return $_class_name;
			}
			return '\dackou\model\Order\OrderModel';
		}
	}
?>