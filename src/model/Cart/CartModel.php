<?php
	namespace dackou\model\Cart;

	use support\Request;
	use support\Db;

	class CartModel extends \dackou\Model{
		protected $table = 'Cart';
		protected $title = '购物车';
		protected $limit = 50;

		protected function getAllList(Request $request){
			return $this->getList($request,'user');
		}

		protected function getPageList(Request $request){
			return $this->getList($request,'user');
		}

		protected function getUserList(Request $request,$uid = 0){
			$uid = $uid ? $uid : $request->input('uid',0);
			if(!$uid){
				return [];
			}

			$field = [
				$this->table.".ID as id",
				$this->table.".MerchantID as merchant_id",
				$this->table.".GoodsID as goods_id",
				$this->table.".SpecID as spec_id",
				$this->table.".Price as price",
				$this->table.".Quantity as quantity",
				$this->table.".Amount as amount",
				$this->table.".UserID as user_id",
				$this->table.".Checked as checked",
				$this->table.".Status as status",
				$this->table.".CreateTime as create_time",
				$this->table.".CreateIP as create_ip",
				$this->table.".UpdateTime as update_time",
				"goods.GoodsTitle as goods_title",
				"goods.Pic as goods_pic",
				"goods.IsPickup as is_pickup",
				"goods_spec.FirstTitle as first_title",
				"goods_spec.SecondTitle as second_title",
				"goods_spec.Stock as stock",
				"goods_spec.Sales as sales",
			];

			$where = [
				[$this->table.".IsDel",'=',0],
				[$this->table.".UserID",'=',$uid]
			];

			$object = Db::table($this->table)
						->join('goods','GoodsID','=','goods.ID')
						->join('goods_spec','SpecID','=','goods_spec.ID')
						->select(...$field)
						->orderBy($this->table.".Hits",'desc')
						->orderBy($this->table.".UpdateTime",'desc')
						->where($where)
						->get();
			if($object){
				for($i=0;$i<count($object);$i++){
					$object[$i]->price /= 100;
					$object[$i]->amount /= 100;

					if($object[$i]->stock && $object[$i]->stock > $object[$i]->sales){
						$object[$i]->remain = $object[$i]->stock - $object[$i]->sales;
					}else{
						$object[$i]->remain = 0;
					}

					if($object[$i]->remain && $object[$i]->quantity > $object[$i]->remain){
						$object[$i]->quantity = $object[$i]->remain;
					}
				}
				return $object;
			}
			return [];
		}

		public function add(Request $request){
			try{
				if(strtolower($request->method()) !== 'post'){
					return 100000;
				}

				$data = $this->validate($request);
				if(!is_array($data) || !isset($data['user_id']) || !isset($data['goods_id'])){
					return $data;
				}

				$res = $this->checkCartExists($data);
				if($res){
					$quantity = $res->quantity + $data['quantity'];
					$amount = $res->amount + $res->price * $data['quantity'];
					if($this->updateData($request,['Quantity' => $quantity,"Amount" => $amount,'UpdateTime' => time()],$res->id)){
						$res->quantity = $quantity;
						$res->amount = $amount;
						return $res;
					}

					return false;
				}else{
					return $this->insertData($request,$data) ? $data : false;
				}
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}


		public function updateCartStatus(Request $request,$data){
			try{
				var_dump('开始清除已下单的购物车商品:');
				if(is_string($data)){
					$data = $this->getDecodeData($data);
				}
				var_dump($data);
				$arr = [];
				foreach($data as $item){
					array_push($arr,$item['cart_id']);
				}
				var_dump($arr);
				if($arr){
					$result = Db::table($this->table)
								->whereIn('ID',$arr)
								->update(['IsDel' => 1]);
					
					var_dump($result !== false ? '清除成功' : '清除失败');
					return $result !== false ? true : false;
				}

				return true;
			}catch(\Exception $e){
				var_dump('清除购物车商品失败:');
				var_dump($e->getMessage());
				return false;
			}
		}

		private function checkCartExists($data){
			try{
				$field = [
					'ID as id',
					'UserID as user_id',
					'GoodsID as goods_id',
					'SpecID as spec_id',
					'Price as price',
					'Quantity as quantity',
					'Amount as amount'
				];

				$where = [
					'MerchantID'	=> $data['merchant_id'],
					'UserID'		=> $data['user_id'],
					'GoodsID'		=> $data['goods_id'],
					'SpecID'		=> $data['spec_id'],
					'IsDel'			=> 0
				];
				$object = Db::table($this->table)
							->select(...$field)
							->where($where)
							->first();
				return $object ? $object : false;
			}catch(\Exception $e){
				return false;
			}
		}

		private function getUserCount($user_id){
			try{
				$count = Db::table($this->table)
							->where("UserID",$user_id)
							->count();
				return $count ? $count : 0;
			}catch(\Exception $e){
				return 0;
			}
		}

		/**
		 * [validate description]
		 * @param  Request $request [description]
		 * @param  integer $id      [description]
		 * @return [type]           [description]
		 */
		protected function validate(Request $request,$id = 0){
			$merchant_id = $request->post('merchant_id',1);

			$user_id = $request->post('user_id');
			if(!$user_id){
				return 100790;
			}

			$_class_name = $this->getClassName('User');
			$service = new $_class_name();
			$user = $service->getList($request,$user_id);
			if(!$user && !is_object($user)){
				return $user;
			}

			if($this->getUserCount($user_id) >= $this->limit){
				return '购物车最多不超过'.$this->limit.'个商品';
			}

			$goods_id = $request->post('goods_id');
			if(!$goods_id){
				return 100810;
			}

			$_class_name = $this->getClassName('Goods');
			$service = new $_class_name();
			$goods = $service->getList($request,$goods_id);
			if(!$goods && !is_object($goods)){
				return $goods;
			}

			if($goods->status !== 1){
				return 100812;
			}

			if($goods->stock && ($goods->stock - $goods->sales) <= 0){
				return 100817;
			}

			$spec_id = $request->post('spec_id');
			if(!$spec_id || $spec_id == '' || !is_numeric($spec_id)){
				return '无效的商品规格';
			}

			$_class_name = $this->getClassName('GoodsSpec');
			$service = new $_class_name();
			$spec = $service->getList($request,$spec_id);
			if(!$spec || !is_object($spec)){
				return '无效的商品规格';
			}
			// var_dump($spec);
			$price = $request->post('price');
			if(!$price || !is_numeric($price)){
				return '无效的商品价格';
			}

			$quantity = $request->post('quantity');
			if(!$quantity || !is_numeric($quantity)){
				return 100816;
			}

			$amount = (float)$request->post('amount','0.00');
			if(!is_numeric($amount)){
				return 100821;
			}
			if((int)($spec->price * 100 * $quantity) !== (int)($amount * 100)){
				return 100821;
			}


			$data['merchant_id'] = $merchant_id;
			$data['user_id'] = $user_id;
			$data['goods_id'] = $goods_id;
			$data['spec_id'] = $spec_id;
			$data['price'] = $price * 100;
			$data['quantity'] = $quantity;
			$data['amount'] = $amount * 100;
			$data['create_time'] = time();
			$data['create_ip'] = $request->getRealIp($safe_mode = true);

			// var_dump($data);
			return $data;
		}
	}
?>