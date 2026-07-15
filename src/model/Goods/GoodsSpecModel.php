<?php
	namespace dackou\model\Goods;

	use support\Request;
	use support\Db;

	class GoodsSpecModel extends \dackou\Model{
		protected $table = 'GoodsSpec';

		protected function getListById(Request $request,$id = 0){
			if(!$id){
				return 100007;
			}

			try{
				$field = $this->getList($request,'field');
				$where = [['IsDel','=',0],['ID','=',$id]];

				$object = Db::table($this->table)
							->select(...$field)
							->where($where)
							->first();
				if($object){
					$object->price /= 100;
					$object->origin_price /= 100;
					$object->cost_price /= 100;
					$object->remnant = $object->stock > 0 ? $object->stock - $object->sales : 0;
				}

				return $object;
			}catch(\Exception $e){
				return 100007;
			}
		}

		protected function getGoodsList(Request $request,$goods_id = 0){
			if(!$goods_id){
				return [];
			}

			try{
				$field = $this->getList($request,'field');
				$where = [['IsDel','=',0],['GoodsID','=',$goods_id]];

				$object = Db::table($this->table)
							->select(...$field)
							->orderBy('FirstID','asc')
							->orderBy('SecondID','asc')
							->where($where)
							->get();

				if($object){
					for($i=0;$i<count($object);$i++){
						$object[$i]->price /= 100;
						$object[$i]->origin_price /= 100;
						$object[$i]->cost_price /= 100;

						$object[$i]->remnant = $object[$i]->stock > 0 ? $object[$i]->stock - $object[$i]->sales : 0;
					}
					return $object;
				}

				return [];

			}catch(\Exception $e){
				return [];
			}
		}

		protected function getOptionList(Request $request,mixed $param = null,array $disabled = [],array $fields = [])
		{
			$goods_id = $request->input('goods_id',0);

			if($goods_id){
				$field = [
					DB::raw("CONCAT(FirstTitle, IF(SecondTitle,'+',''), SecondTitle) as label"),
					'ID as value',
					'Price/100 as price',
					'Amount/100 as amount'
				];
				$where = [[$this->table.'.IsDel','=',0],[$this->table.'.Status','=',1],['GoodsID','=',$goods_id]];

				$object = Db::table($this->table)
							->select(...$field)
							->where($where)
							->get();
				// var_dump($object);
				return $object;
			}


			$goods = $this->prefix . 'goods';
			$field = [
				'GoodsTitle as '.$this->option_label,
				'ID as '.$this->option_value,
				Db::raw('(SELECT JSON_ARRAYAGG(JSON_OBJECT("label",CONCAT(FirstTitle," ",SecondTitle),"value",ID,"price",Price/100,"amount",Amount/100)) from '.$this->tab.' where GoodsID = '.$goods.'.ID) as children')
			];
			$where = [['IsDel','=',0],['Status','=',1]];
			$object = Db::table('goods')
						->select(...$field)
						->where($where)
						->get();
			if($object){
				foreach($object as $k => $v){
					$object[$k]->children = $this->getDecodeData($v->children);
				}
			}
			// var_dump($object);
			return $object;
		}

		protected function getMultipleList(Request $request,$goods,$user){
			$goods_id = $goods['goods_id'];
			$spec_id = [];
			foreach($goods['spec'] as $item){
				array_push($spec_id,$item['spec_id']);
			}

			$field = [
				$this->table.'.GoodsID as id',
				$this->table.'.ID as spec_id',
				'FirstID as first_id',
				'FirstTitle as first_title',
				'SecondID as second_id',
				'SecondTitle as second_title',
				$this->table.'.SpecCode as spec_code',
				$this->table.'.MemberPrice as member_price',
				$this->table.'.Price as price',
				$this->table.'.OriginPrice as origin_price',
				$this->table.'.CostPrice as cost_price',
				$this->table.'.UnitID as unit_id',
				$this->table.'.BaseNumber as base_number',
				$this->table.'.LimitCount as limit_count',
				$this->table.'.LimitOrder as limit_order',
				$this->table.'.Sku as sku',
				$this->table.'.Stock as stock',
				$this->table.'.Sales as sales',
				$this->table.'.Amount as amount',
				$this->table.'.Status as status',

				'GoodsTitle as goods_title',
				'goods.Pic as goods_pic',
				'goods.CateID as cate_id',
				'goods.LogisticsType as logistics_type',
				'goods.FreeUnit as free_unit',
				'goods.FreeAmount as free_amount',
				'goods.FreightAmount as freight_amount',
				'goods.FreightAccumulate as freight_accumulate',
				'goods.DeductScore as deduct_score',
				'super.Title as supre_title',
				'goods_cate.Title as cate_title',
			];

			$where = $this->getWhere($request);
			array_push($where,['GoodsID','=',$goods_id]);

			$object = Db::table('goods_spec')
							->join('goods','GoodsID','=','goods.ID')
							->join('goods_cate','goods.CateID','=','goods_cate.ID')
							->join('goods_cate as super','goods_cate.PID','=','super.ID')
							->select(...$field)
							->where($where)
							->whereIn($this->table.'.ID',$spec_id)
							->get();
			if($object){
				foreach($object as $k => $obj){
					$object[$k]->quantity = $this->getQuantityValue($object[$k],$goods['spec']);
					$object[$k]->amount = $object[$k]->quantity * $object[$k]->price;
					$object[$k]->freight = $this->getFreightValue($object[$k]);
					$object[$k]->score = $this->getScoreValue($object[$k],$user);

					$object[$k]->member_price /= 100;
					$object[$k]->price /= 100;
					$object[$k]->origin_price /= 100;
					$object[$k]->cost_price /= 100;
					$object[$k]->amount /= 100;

					unset($object[$k]->free_unit);
					unset($object[$k]->free_amount);
					unset($object[$k]->freight_amount);
					unset($object[$k]->freight_accumulate);
					unset($object[$k]->deduct_score);
				}
			}
			return $object;
		}

		// 匹配下单数量
		private function getQuantityValue($obj,$specs){
			$spec = [];
			foreach($specs as $item){
				if($item['spec_id'] === $obj->spec_id){
					$spec = $item;
					break;
				}
			}
			if(!$spec){
				return 0;
			}

			return isset($spec['quantity']) ? $spec['quantity'] : 0;
		}

		// 计算运费
		private function getFreightValue($obj){
			if(!in_array($obj->logistics_type,[1,2])){
				return 0;
			}

			if(!$obj->free_unit && !$obj->free_amount){
				return 0;
			}

			$quantity = $obj->quantity;
			$amount = $obj->amount;

			if($obj->free_unit && $quantity >= $obj->free_unit){
				// 满足满件包邮
				return 0;
			}

			if($obj->free_amount && $amount*100 >= $obj->free_amount*100){
				// 满足满额包邮
				return 0;
			}

			if(!$obj->freight_amount){
				return 0;
			}

			$freight = $obj->freight_amount;
			$is_accumulate = $obj->freight_accumulate;
			$base_number = $obj->base_number ?? 1; 

			if(!$is_accumulate){
				return $freight;
			}

			return (floor($quantity/$base_number) * $freight*100) / 100;
		}

		// 计算可抵扣积分
		private function getScoreValue($obj,$user){
			if(!$user->score){
				return 0;
			}

			if(!$obj->deduct_score){
				return 0;
			}

			if($user->score <= $obj->deduct_score){
				return $user->score;
			}

			return $obj->deduct_score;
		}

		public function setData(Request $request,$data,$flag = ''){
			$goods_id = $data['id'];
			$spec_data = $data['specs'];
			$specs = $this->getList($request,'goods',$goods_id);
			$ip = $request->getRealIp($safe_mode=true);
			
			$insertData = [];
			$updateData = [];
			foreach($spec_data as $item){
				$child = [
					'GoodsID'		=> $goods_id,
					'FirstID'		=> isset($item['first_id']) ? $item['first_id'] : 0,
					'SecondID'		=> isset($item['second_id']) ? $item['second_id'] : 0,
					'FirstTitle'	=> isset($item['first_title']) ? $item['first_title'] : '',
					'SecondTitle'	=> isset($item['second_title']) ? $item['second_title'] : '',
					'SpecCode'		=> isset($item['spec_code']) ? $item['spec_code'] : '',
					'Pic'			=> isset($item['pic']) ? $item['pic'] : '',
					'MemberPrice'	=> isset($item['member_price']) ? $item['member_price'] * 100 : 0,
					'Price'			=> isset($item['price']) ? $item['price'] * 100 : 0,
					'OriginPrice'	=> isset($item['origin_price']) ? $item['origin_price'] * 100 : 0,
					'CostPrice'		=> isset($item['cost_price']) ? $item['cost_price'] * 100 : '',
					'BaseNumber'	=> isset($item['base_number']) ? $item['base_number'] : 0,
					'LimitCount'	=> isset($item['limit_count']) ? $item['limit_count'] : 0,
					'LimitOrder'	=> isset($item['limit_order']) ? $item['limit_order'] : 0,
					'UnitID'		=> isset($item['unit_id']) ? $item['unit_id'] : 0,
					'Sku'			=> isset($item['sku']) ? $item['sku'] : 0,
					'Stock'			=> isset($item['stock']) ? $item['stock'] : 0,
					'CreateTime'	=> time(),
					'CreateIP'		=> $ip,
				];
				if(isset($item['id']) && $item['id']){
					array_push($updateData,['id'=>$item['id'],'data'=>$child]);
				}else{
					array_push($insertData,$child);
				}
			}

			if($insertData){
				$this->insertData($request,$insertData);
			}

			if($updateData){
				$ids = [];
				foreach($updateData as $item){
					array_push($ids,$item['id']);
					$this->updateData($request,$item['data'],$item['id']);
				}

				$removeID = [];
				foreach($specs as $spec){
					if(!in_array($spec->id,$ids)){
						array_push($removeID,$spec->id);
					}
				}
				// var_dump('removeID: ',$removeID);
				if($removeID){
					Db::table($this->table)->whereIn('ID',$removeID)->update(['Status' => 0]);
				}
			}

			return true;
		}

		private function setValidData($id){
			$sql = "UPDATE `".$this->tab."` SET `IsDel` = 1 WHERE `GoodsID` = ?";
			$result = Db::update($sql,[$id]);
			return $result !== false ? true : false;
		}

		private function checkSpecExists($data){
			try{
				$where = [];
				array_push($where,['GoodsID','=',$data['GoodsID']]);
				if($data['FirstID']){
					array_push($where,['FirstID','=',$data['FirstID']]);
					array_push($where,['FirstTitle','=',$data['FirstTitle']]);
				}
				if($data['SecondID']){
					array_push($where,['SecondID','=',$data['SecondID']]);
					array_push($where,['SecondTitle','=',$data['SecondTitle']]);
				}

				$object = Db::table($this->table)
							->select("ID as id")
							->where($where)
							->first();

				return $object ? $object->id : 0;
			}catch(\Exception $e){
				return false;
			}
		}

		public function setDel(Request $request,$id){
			return true;
		}

		public function setDelete(Request $request,$id){
			return true;
		}
	}
?>