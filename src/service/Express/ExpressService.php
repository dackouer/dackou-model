<?php
	namespace dackou\service\Express;

	use support\Request;
	use Shopwwi\WebmanExpress\Facade\Express;

	class ExpressService{
		private $default = 'kuaidi100';

		public function track(Request $request){
			$code = trim($request->post('code',''));
			if(!$code){
				return '快递公司编码不能为空';
			}

			$order = trim($request->post('order',''));
			if(!$order){
				return '物流单号不能为空';
			}

			$additional = [];

			switch($this->default){
				case 'kuaidi100':
					$phone = trim($request->post('phone',''));
					if($code == 'shunfeng' && !$phone){
						return '收/寄件人的电话号码不能为空';
					}
					if($phone){
						$additional['phone'] = $phone;
					}
					$from = trim($request->post('from',''));
					if($from){
						$additional['from'] = $from;
					}
					$to = trim($request->post('to',''));
					if($to){
						$additional['to'] = $to;
					}
					break;
				case 'kdniao':
					$order = trim($request->post('order',''));
					if($order){
						$additional['OrderCode'] = $order;
					}
					$customer = trim($request->post('customer',''));
					if($customer){
						$additional['CustomerName'] = $customer;
					}
					break;
				default:


			}

			try{
				$service = Express::make('kuaidi100');
				$result = $service->track($code,$order,$additional);
				var_dump($result);
				return $result;
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		private function getExceptionError($e){
			return [
				'code' => $e->getCode() ? $e->getCode() : 1,
				'msg'  => $e->getMessage()
			];
		}
	}
?>