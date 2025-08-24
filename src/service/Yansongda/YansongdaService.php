<?php
	namespace dackou\service\Yansongda;

	use support\Request;
	use Webman\Config;
	use Yansongda\Pay\Pay;
	use Yansongda\Pay\Contract\HttpClientInterface;

	class YansongdaService{
		public function pay(Request $request){

		}

		public function sign(Request $request,$order = [],$type = 'mini'){
			$config = Config::get('payment');
			try{
				Pay::config($config);

				switch($type){
					case 'mp':			// 公众号支付
						$result = Pay::wechat()->mp($order);
						break;
					case 'mini':		// 小程序支付
						$result = Pay::wechat()->mini($order);
						break;	
					case 'h5':			// H5支付
						$result = Pay::wechat()->h5($order);
						break;
					case 'app':			// app支付
						$result = Pay::wechat()->app($order);
						break;
					case 'pos':			// 刷卡支付
						$result = Pay::wechat()->pos($order);
						break;
					case 'scan':		// 扫码支付
						$result = Pay::wechat()->scan($order);
						break;
					case 'transfer':	// 转账
						$result = Pay::wechat()->transfer($order);
						break;
					default:
						$result = Pay::wechat()->mini($order);
				}
				return $result;
			}catch(\Exception $e){
				return $this->getErrorMessage($e);
			}
		}

		/**
		 * 接收回调
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function notify(Request $request){
			$config = config('payment');
			try{
				Pay::config($config);
				$result = Pay::wechat()->callback($request->post());
				// var_dump('Yansongda notify result:');
				// var_dump($result);
				return $result;
			}catch(\Exception $e){
				return $this->getErrorMessage($e);
			}
		}

		/**
		 * 申请退款
		 * @param  Request $request [description]
		 * @param  array   $data    [description]
		 * @return [type]           [description]
		 */
		public function refund(Request $request,$data = []){
			$transactionId = isset($data['platform_order']) ? trim($data['platform_order']) : trim($request->post('platform_order',''));
			if(!$transactionId){
				return '无效的订单号';
			}
			$refundNumber = isset($data['order_code']) ? trim($data['order_code']) : trim($request->post('order_code',''));
			if(!$refundNumber){
				return '无效的退款单号';
			}
			$totalFee = isset($data['total_amount']) ? (int)$data['total_amount'] : $request->post('total_amount',0);
			if(!$totalFee){
				return '无效的订单金额';
			}
			$refundFee = isset($data['refund_amount']) ? (int)$data['refund_amount'] : $request->post('refund_amount',0);
			if(!$refundFee || $refundFee > $totalFee){
				return '无效的退款金额';
			}
			$remark = isset($data['remark']) ? trim($data['remark']) : trim($request->post('remark',''));
			$config = [];
			if($remark){
				$config['refund_desc'] = $remark;
			}

			$config = config('payment');
			try{
				$order = [
				    'out_trade_no' 	=> $transactionId,
				    'out_refund_no' => $refundNumber,
				    'amount' => [
				        'refund' 	=> $refundFee,
				        'total' 	=> $totalFee,
				        'currency'  => 'CNY',
				    ],
				    // '_action' => 'jsapi', // jsapi 退款，默认
				    // '_action' => 'app', // app 退款
				    // '_action' => 'combine', // 合单退款
				    // '_action' => 'h5', // h5 退款
				    // '_action' => 'miniapp', // 小程序退款
				    // '_action' => 'native', // native 退款

				];
				
				// var_dump('Yansongda 退款参数:');
				// var_dump($order);
				Pay::config($config);
				$result = Pay::wechat()->refund($order);
				return $result;
			}catch(\Exception $e){
				return $this->getErrorMessage($e);
			}
		}

		/**
		 * [getErrorMessage description]
		 * @param  [type] $e [description]
		 * @return [type]    [description]
		 */
		private function getErrorMessage($e){
			return [
				'code'	=> $e->getCode() ?: 1,
				'msg'	=> $e->getMessage()
			];
		}
	}
?>