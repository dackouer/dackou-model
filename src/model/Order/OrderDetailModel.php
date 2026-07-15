<?php
	namespace dackou\model\Order;

	use support\Request;
	use support\Db;

	class OrderDetailModel extends \dackou\Model{
		protected $table = 'OrderDetail';
        protected $primaryKey = 'order_code';
		protected $title = '订单详情';

		public function appendData(Request $request,$data)
		{
			try{
				// var_dump($data);

				$type = $data['order_type'];
				$order = $data['order_code'];
				$user_id = $data['user_id'];
				$time = time();
				$ip = $request->getRealIp($safe_mode = true);

				$goods = $data['goods'];
				$good = [];
				foreach($goods as $item){
					array_push($good,[
						'OrderType' 	=> $type,
						'OrderCode' 	=> $order,
						'UserID'		=> $user_id,
						'MerchantID'	=> $item['merchant_id'] ?? 0,
						'MerchantName'	=> $item['merchant_name'] ?? '',
						'MerchantPic'	=> $item['merchant_pic'] ?? '',
						'StoreID'		=> $item['store_id'] ?? 0,
						'StoreName'		=> $item['store_name'] ?? '',
						'StorePic'		=> $item['store_pic'] ?? '',
						'GoodsID'		=> (int)$item['goods_id'],
						'GoodsTitle'	=> $item['goods_title'] ?? '',
						'GoodsPic'		=> $item['goods_pic'] ?? '',
						'GoodsDuration'	=> $item['goods_duration'] ?? 0,
						'CateID'		=> $item['cate_id'] ?? 0,
						'StockCode'		=> $item['stock_code'] ?? 0,
						'SpecID'		=> $item['spec_id'] ?? 0,
						'FirstTitle'	=> $item['first_title'] ?? '',
						'SecondTitle'	=> $item['second_title'] ?? '',
						'Quantity'		=> $item['quantity'],
						'Price'			=> $item['price'] * 100,
						'Amount'		=> $item['amount'] * 100,
						'CreateTime'	=> $time,
						'CreateIP'		=> $ip,
						'State'			=> 1,
					]);
				}

				if($good){
					$keys = [];
					$vals = [];
					$param = [];

					foreach($good as $key => $val){
						$value = "(";
						foreach($val as $k => $v){
							if(!$key){
								array_push($keys,"`{$k}`");
							}
							$value .= '?,';
							array_push($param,$v);
						}
						$value = rtrim($value,',') . ")";
						array_push($vals,$value);
					}

					$keys = implode(',', $keys);
					$vals = implode(',', $vals);

					$sql = "INSERT INTO `".$this->tab."` ({$keys}) VALUES {$vals}";
					$object = Db::insert($sql,$param);

					return $object !== false ? true : false;
				}

				return false;
			}catch(\Exception $e){
				// var_dump($this->getExceptionError($e));
				return false;
			}
		}

		protected function getOrderList(Request $request,$order = ''){
			$field = $this->getList($request,'field');
			$where = [[$this->table.'.OrderCode','=',$order]];

			$object = Db::table($this->table)
						->select(...$field)
						->where($where)
						->get();
			if($object){
				foreach($object as $k => $v){
					$object[$k]->goods_id = (int)$object[$k]->goods_id;
					$object[$k]->price /= 100;
					$object[$k]->amount /= 100;
				}
			}
			return $object;
		}
	}
?>