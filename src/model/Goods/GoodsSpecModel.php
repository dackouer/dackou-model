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
				$field = $this->getTableFields();
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

					return $object;
				}

				return 100007;
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

		public function setData(Request $request,$data = [],$flag = false){
			$spec_data = $request->post('spec_data');
			$arr = [];
			
			// var_dump('商品规格数据:');
			// var_dump($spec_data);

			$ip = $request->getRealIp($safe_mode=true);

			if(!$flag){
				// var_dump('新增规格数据：');
				foreach($spec_data as $item){
					$child = [
						'GoodsID'		=> !$flag ? $data['id'] : (isset($data['id']) ? $data['id'] : (isset($data['id']) ? $data['id'] : 0)),
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
					array_push($arr,$child);
				}
				return $this->insertData($request,$arr);
			}else{
				$insertData = [];
				$updateData = [];
				foreach($spec_data as $item){
					$child = [
						'GoodsID'		=> !$flag ? $data['id'] : (isset($data['id']) ? $data['id'] : (isset($data['id']) ? $data['id'] : 0)),
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
					foreach($updateData as $item){
						$this->updateData($request,$item['data'],$item['id']);
					}
				}

				return true;
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
	}
?>