<?php
	namespace dackou\model\Market;

	use support\Request;
	use support\Db;
	use zjkal\TimeHelper;
	use dackou\model\Goods\GoodsModel;

	class MarketModel extends \dackou\Model{
		protected $table = 'Market';
		protected $option_key = 'market';
		protected $type_value = [1=>'商城',2=>'拼团',3=>'秒杀',4=>'砍价',5=>'专区',6=>'众筹',7=>'拍卖',8=>'盲盒'];
		
		protected function getAllList(Request $request){
			return $this->getList($request,'page');
		}
		
		protected function getPageList(Request $request){
			$type = trim($request->post('type',''));
			if(!$type){
				return [];
			}

			$_class_method = "get".ucfirst($type)."List";
			if(method_exists($this,$_class_method)){
				return $this->$_class_method($request);
			}

			return [];
		}

		protected function getListById(Request $request,$id = 0){
			if(!$id){
				return '数据不存在或已被删除';
			}

			$type = $request->post('type');

			try{
				$field = [
					$this->table.".ID as id",
					$this->table.".Type as type",
					"PeriodID as period_id",
					"PrefectureID as prefecture_id",
					"GoodsID as goods_id",
					$this->table.".Specs as specs",
					"MarketNumber as market_number",
					"MarketTime as market_time",
					"BaseAmount as base_amount",
					"FirstAmount as first_amount",
					"CounterType as counter_type",
					"MinAmount as min_amount",
					"MaxAmount as max_amount",
					"FixAmount as fix_amount",
					$this->table.".StartTime as start_time",
					$this->table.".EndTime as end_time",
					"MarketStock as market_stock",
					"MarketSales as market_sales",
					$this->table.".ShareTitle as share_title",
					$this->table.".ShareDesc as share_desc",
					"BuyType as buy_type",
					$this->table.".Status as status",
					$this->table.".CreateTime as create_time",
				];
				$where = [[$this->table.'.IsDel','=',0],[$this->table.'.ID','=',$id]];
				$object = Db::table($this->table)
							->select(...$field)
							->where($where)
							->first();
				if($object){
					$object->create_time = $this->getDateTime($object->create_time);
					$object->specs = $this->getDecodeData($object->specs);
					$service = new GoodsModel();
					$object->goods = $service->getList($request,$object->goods_id);

					return $object;
				}

				return [];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		/**
		 * 获取拼团数据列表
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		protected function getTeamworkList(Request $request,$flag = false){
			if(!isset($this->config['market_is_teamwork']) || !$this->config['market_is_teamwork']){
				return [];
			}
			$this->title = '拼团商品';
			$type = 2;
			
			try{
				$field = [
					$this->table.'.ID as id',
					$this->table.'.GoodsID as goods_id',
					'MarketPrice as market_price',
					'MarketNumber as market_number',
					'MarketTime as market_time',
					$this->table.'.StartTime as start_time',
					$this->table.'.EndTime as end_time',
					$this->table.'.MarketStock as market_stock',
					$this->table.'.MarketSales as market_sales',
					$this->table.'.ShareTitle as share_title',
					$this->table.'.ShareDesc as share_desc',
					$this->table.'.Specs as specs',
					$this->table.'.Status as status',
					$this->table.'.CreateTime as create_time',
					'goods.GoodsTitle as goods_title',
					'goods.Pic as goods_pic',
					'goods.Picture as goods_picture',
					'goods.Stock as goods_stock',
					'goods.Presale as presale',
					'goods.Sales as goods_sales',
					'goods.IsShare as is_share',
					'goods.ShareTitle as goods_share_title',
					'goods.SharePic as goods_share_pic',
				];

				$where = [
					[$this->table.'.IsDel','=',0],
					[$this->table.'.Type','=',$type],
					['goods.IsDel','=',0]
				];
				if($flag){
					array_push($where,[$this->table.'.Status','=',1]);
				}
				// var_dump($where);
				$rows = Db::table($this->table)
							->join('goods',$this->table.'.GoodsID','=','goods.ID')
							->where($where)
							->count();
				$limit = $this->getLimit($request);

				$object = Db::table($this->table)
							->join('goods',$this->table.'.GoodsID','=','goods.ID')
							->select(...$field)
							->orderBy($this->table.'.CreateTime','desc')
							->where($where)
							->offset($limit[0])
							->limit($limit[1])
							->get();
				if($object){
					for($i=0;$i<count($object);$i++){
						$object[$i]->start_time = $this->getDateTime($object[$i]->start_time);
						$object[$i]->end_time = $this->getDateTime($object[$i]->end_time);
						$object[$i]->status_title = $object[$i]->status ? '开启' : '关闭';
						$object[$i]->specs = $this->getDecodeData($object[$i]->specs);
						$object[$i]->goods_picture = $this->getDecodeData($object[$i]->goods_picture);
						
						$min_price = $object[$i]->specs['spec_data'][0]['price'];
						$max_price = $object[$i]->specs['spec_data'][0]['price'];
						$min_mprice = $object[$i]->specs['spec_data'][0]['market_price'] / 100;
						$max_mprice = $object[$i]->specs['spec_data'][0]['market_price'] / 100;
						
						if(count($object[$i]->specs['spec_data']) > 1){
							for($k=1;$k<count($object[$i]->specs['spec_data']);$k++){
								if($object[$i]->specs['spec_data'][$k]['price'] < $min_price){
									$min_price = $object[$i]->specs['spec_data'][$k]['price'];
								}
								if($object[$i]->specs['spec_data'][$k]['price'] > $max_price){
									$max_price = $object[$i]->specs['spec_data'][$k]['price'];
								}
								if($object[$i]->specs['spec_data'][$k]['market_price'] / 100 < $min_mprice){
									$min_mprice = $object[$i]->specs['spec_data'][$k]['market_price'] / 100;
								}
								if($object[$i]->specs['spec_data'][$k]['market_price'] / 100 > $max_mprice){
									$max_mprice = $object[$i]->specs['spec_data'][$k]['market_price'] / 100;
								}
							}
							$object[$i]->origin_price = '¥' . $min_price . ' - ¥' . $max_price;
							$object[$i]->current_price = '¥' . $min_mprice . ' - ¥' . $max_mprice;
						}else{
							$object[$i]->origin_price = '¥' . $min_price;
							$object[$i]->current_price = '¥' . $min_mprice;
						}

					}
					// var_dump($object);
					return ['rows' => $rows,'data' => $object];
				}

				return [];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		/**
		 * 获取秒杀数据列表
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		protected function getSeckillList(Request $request,$flag = false){
			if(!isset($this->config['market_is_seckill']) || !$this->config['market_is_seckill']){
				return [];
			}

			$this->title = '秒杀商品';
			$type = 3;

			try{
				$field = [
					$this->table.'.ID as id',
					$this->table.'.GoodsID as goods_id',
					'PeriodID as period_id',
					'MarketPrice as market_price',
					'market_period.StartTime as start_time',
					'market_period.EndTime as end_time',
					$this->table.'.MarketStock as market_stock',
					$this->table.'.MarketSales as market_sales',
					$this->table.'.ShareTitle as share_title',
					$this->table.'.ShareDesc as share_desc',
					$this->table.'.Specs as specs',
					$this->table.'.Status as status',
					$this->table.'.CreateTime as create_time',
					'goods.GoodsTitle as goods_title',
					'goods.Pic as goods_pic',
					'goods.Picture as goods_picture',
					'goods.Stock as goods_stock',
					'goods.Presale as presale',
					'goods.Sales as goods_sales',
					'goods.IsShare as is_share',
					'goods.ShareTitle as goods_share_title',
					'goods.SharePic as goods_share_pic',
				];

				$where = [
					[$this->table.'.IsDel','=',0],
					[$this->table.'.Type','=',$type],
					['goods.IsDel','=',0]
				];
				if($flag){
					array_push($where,[$this->table.'.Status','=',1]);
					array_push($where,['market_period.Status','=',1]);
				}

				$rows = Db::table($this->table)
							->join('goods',$this->table.'.GoodsID','=','goods.ID')
							->join('market_period',$this->table.'.PeriodID','=','market_period.ID')
							->where($where)
							->count();
				$limit = $this->getLimit($request);

				$object = Db::table($this->table)
							->join('goods',$this->table.'.GoodsID','=','goods.ID')
							->join('market_period',$this->table.'.PeriodID','=','market_period.ID')
							->select(...$field)
							->orderBy($this->table.'.CreateTime','desc')
							->where($where)
							->offset($limit[0])
							->limit($limit[1])
							->get();
				if($object){
					for($i=0;$i<count($object);$i++){
						$object[$i]->period_title = $object[$i]->start_time . '-' . $object[$i]->end_time;
						$object[$i]->status_title = $object[$i]->status ? '开启' : '关闭';
						$object[$i]->specs = $this->getDecodeData($object[$i]->specs);
						$object[$i]->goods_picture = $this->getDecodeData($object[$i]->goods_picture);

						$min_price = $object[$i]->specs['spec_data'][0]['price'];
						$max_price = $object[$i]->specs['spec_data'][0]['price'];
						$min_mprice = $object[$i]->specs['spec_data'][0]['market_price'] / 100;
						$max_mprice = $object[$i]->specs['spec_data'][0]['market_price'] / 100;
						// var_dump('count:'.count($object[$i]->specs['spec_data']));
						if(count($object[$i]->specs['spec_data']) > 1){
							for($k=1;$k<count($object[$i]->specs['spec_data']);$k++){
								if($object[$i]->specs['spec_data'][$k]['price'] < $min_price){
									$min_price = $object[$i]->specs['spec_data'][$k]['price'];
								}
								if($object[$i]->specs['spec_data'][$k]['price'] > $max_price){
									$max_price = $object[$i]->specs['spec_data'][$k]['price'];
								}
								if($object[$i]->specs['spec_data'][$k]['market_price'] / 100 < $min_mprice){
									$min_mprice = $object[$i]->specs['spec_data'][$k]['market_price'] / 100;
								}
								if($object[$i]->specs['spec_data'][$k]['market_price'] / 100 > $max_mprice){
									$max_mprice = $object[$i]->specs['spec_data'][$k]['market_price'] / 100;
								}
							}
							$object[$i]->origin_price = '¥' . $min_price . ' - ¥' . $max_price;
							$object[$i]->current_price = '¥' . $min_mprice . ' - ¥' . $max_mprice;
						}else{
							$object[$i]->origin_price = '¥' . $min_price;
							$object[$i]->current_price = '¥' . $min_mprice;
						}
					}
					
					$service = new MarketPeriodModel();
					$period = $service->getList($request,'valid');
					return ['rows' => $rows,'period'=>$period,'data' => $object];
				}

				return [];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		/**
		 * 获取砍价数据列表
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		protected function getCounterList(Request $request,$flag = false){
			if(!isset($this->config['market_is_counter']) || !$this->config['market_is_counter']){
				return [];
			}

			$this->title = '砍价商品';
			$type = 4;

			try{
				$field = [
					$this->table.'.ID as id',
					$this->table.'.GoodsID as goods_id',
					'MarketTime as market_time',
					'CounterType as counter_type',
					'MinAmount as min_amount',
					'MaxAmount as max_amount',
					'FixAmount as fix_amount',
					$this->table.'.StartTime as start_time',
					$this->table.'.EndTime as end_time',
					$this->table.'.MarketStock as market_stock',
					$this->table.'.MarketSales as market_sales',
					$this->table.'.ShareTitle as share_title',
					$this->table.'.ShareDesc as share_desc',
					$this->table.'.Specs as specs',
					$this->table.'.Status as status',
					$this->table.'.CreateTime as create_time',
					'goods.GoodsTitle as goods_title',
					'goods.Pic as goods_pic',
					'goods.Picture as goods_picture',
					'goods.Stock as goods_stock',
					'goods.Presale as presale',
					'goods.Sales as goods_sales',
					'goods.IsShare as is_share',
					'goods.ShareTitle as goods_share_title',
					'goods.SharePic as goods_share_pic',
					'BuyType as buy_type',
				];

				$where = [
					[$this->table.'.IsDel','=',0],
					[$this->table.'.Type','=',$type],
					['goods.IsDel','=',0]
				];
				if($flag){
					array_push($where,[$this->table.'.Status','=',1]);
				}

				$rows = Db::table($this->table)
							->join('goods',$this->table.'.GoodsID','=','goods.ID')
							->where($where)
							->count();
				$limit = $this->getLimit($request);

				$object = Db::table($this->table)
							->join('goods',$this->table.'.GoodsID','=','goods.ID')
							->select(...$field)
							->orderBy($this->table.'.CreateTime','desc')
							->where($where)
							->offset($limit[0])
							->limit($limit[1])
							->get();
				if($object){
					for($i=0;$i<count($object);$i++){
						$object[$i]->start_time = $this->getDateTime($object[$i]->start_time);
						$object[$i]->end_time = $this->getDateTime($object[$i]->end_time);
						$object[$i]->status_title = $object[$i]->status ? '开启' : '关闭';
						$object[$i]->specs = $this->getDecodeData($object[$i]->specs);
						$object[$i]->goods_picture = $this->getDecodeData($object[$i]->goods_picture);
						$object[$i]->buy_title = $object[$i]->buy_type ? '砍到底价' : '任意金额';
						if($object[$i]->fix_amount){
							$object[$i]->fix_amount /= 100;
						}
						if($object[$i]->min_amount){
							$object[$i]->min_amount /= 100;
						}
						if($object[$i]->max_amount){
							$object[$i]->max_amount /= 100;
						}
						$object[$i]->counter_title = $object[$i]->counter_type ? '¥'.$object[$i]->fix_amount : "¥{$object[$i]->min_amount} - ¥{$object[$i]->max_amount}";

						
						$min_price = $object[$i]->specs['spec_data'][0]['price'];
						$max_price = $object[$i]->specs['spec_data'][0]['price'];
						$min_bamount = $object[$i]->specs['spec_data'][0]['base_amount'] / 100;
						$max_bamount = $object[$i]->specs['spec_data'][0]['base_amount'] / 100;
						$min_famount = $object[$i]->specs['spec_data'][0]['first_amount'] / 100;
						$max_famount = $object[$i]->specs['spec_data'][0]['first_amount'] / 100;
						
						if(count($object[$i]->specs['spec_data']) > 1){
							for($k=1;$k<count($object[$i]->specs['spec_data']);$k++){
								if($object[$i]->specs['spec_data'][$k]['price'] < $min_price){
									$min_price = $object[$i]->specs['spec_data'][$k]['price'];
								}
								if($object[$i]->specs['spec_data'][$k]['price'] > $max_price){
									$max_price = $object[$i]->specs['spec_data'][$k]['price'];
								}

								if($object[$i]->specs['spec_data'][$k]['base_amount'] / 100 < $min_bamount){
									$min_bamount = $object[$i]->specs['spec_data'][$k]['base_amount'] / 100;
								}
								if($object[$i]->specs['spec_data'][$k]['base_amount'] / 100 > $max_bamount){
									$max_bamount = $object[$i]->specs['spec_data'][$k]['base_amount'] / 100;
								}
								
								if($object[$i]->specs['spec_data'][$k]['first_amount'] / 100 < $min_famount){
									$min_famount = $object[$i]->specs['spec_data'][$k]['first_amount'] / 100;
								}
								if($object[$i]->specs['spec_data'][$k]['first_amount'] / 100 > $max_famount){
									$max_famount = $object[$i]->specs['spec_data'][$k]['first_amount'] / 100;
								}
							}
							$object[$i]->origin_price = '¥' . $min_price . ' - ¥' . $max_price;
							$object[$i]->base_amount = '¥' . $min_bamount . ' - ¥' . $max_bamount;
							$object[$i]->first_amount = '¥' . $min_famount . ' - ¥' . $max_famount;
						}else{
							$object[$i]->origin_price = '¥' . $min_price;
							$object[$i]->base_amount = '¥' . $min_bamount;
							$object[$i]->first_amount = '¥' . $min_famount;
						}
					}
					return ['rows' => $rows,'data' => $object];
				}

				return [];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		/**
		 * 获取专区数据列表
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		protected function getSectionList(Request $request,$flag = false){
			if(!isset($this->config['market_is_section']) || !$this->config['market_is_section']){
				return [];
			}

			$this->title = '营销专区';
			$type = 5;

			try{
				$field = [
					$this->table.'.ID as id',
					$this->table.'.GoodsID as goods_id',
					'PrefectureID as prefecture_id',
					'MarketPrice as market_price',
					'market_prefecture.Title as prefecture_title',
					$this->table.'.MarketStock as market_stock',
					$this->table.'.MarketSales as market_sales',
					$this->table.'.ShareTitle as share_title',
					$this->table.'.ShareDesc as share_desc',
					$this->table.'.Specs as specs',
					$this->table.'.Status as status',
					$this->table.'.CreateTime as create_time',
					'goods.GoodsTitle as goods_title',
					'goods.Pic as goods_pic',
					'goods.Picture as goods_picture',
					'goods.Stock as goods_stock',
					'goods.Presale as presale',
					'goods.Sales as goods_sales',
					'goods.IsShare as is_share',
					'goods.ShareTitle as goods_share_title',
					'goods.SharePic as goods_share_pic',
				];

				$where = [
					[$this->table.'.IsDel','=',0],
					[$this->table.'.Type','=',$type],
					['goods.IsDel','=',0]
				];
				if($flag){
					array_push($where,[$this->table.'.Status','=',1]);
					array_push($where,['market_prefecture.Status','=',1]);
				}

				$prefecture_id = $request->post('prefecture_id',0);
				// var_dump('prefecture_id: '.$prefecture_id);
				if($prefecture_id){
					array_push($where,['PrefectureID','=',$prefecture_id]);
				}

				$rows = Db::table($this->table)
							->join('goods',$this->table.'.GoodsID','=','goods.ID')
							->join('market_prefecture',$this->table.'.PrefectureID','=','market_prefecture.ID')
							->where($where)
							->count();
				$limit = $this->getLimit($request);

				$object = Db::table($this->table)
							->join('goods',$this->table.'.GoodsID','=','goods.ID')
							->join('market_prefecture',$this->table.'.PrefectureID','=','market_prefecture.ID')
							->select(...$field)
							->orderBy($this->table.'.CreateTime','desc')
							->where($where)
							->offset($limit[0])
							->limit($limit[1])
							->get();
				if($object){
					for($i=0;$i<count($object);$i++){
						$object[$i]->status_title = $object[$i]->status ? '开启' : '关闭';
						$object[$i]->specs = $this->getDecodeData($object[$i]->specs);
						$object[$i]->goods_picture = $this->getDecodeData($object[$i]->goods_picture);
						
						$min_price = $object[$i]->specs['spec_data'][0]['price'];
						$max_price = $object[$i]->specs['spec_data'][0]['price'];
						$min_mprice = $object[$i]->specs['spec_data'][0]['market_price'] / 100;
						$max_mprice = $object[$i]->specs['spec_data'][0]['market_price'] / 100;
						
						if(count($object[$i]->specs['spec_data']) > 1){
							for($k=1;$k<count($object[$i]->specs['spec_data']);$k++){
								if($object[$i]->specs['spec_data'][$k]['price'] < $min_price){
									$min_price = $object[$i]->specs['spec_data'][$k]['price'];
								}
								if($object[$i]->specs['spec_data'][$k]['price'] > $max_price){
									$max_price = $object[$i]->specs['spec_data'][$k]['price'];
								}
								if($object[$i]->specs['spec_data'][$k]['market_price'] / 100 < $min_mprice){
									$min_mprice = $object[$i]->specs['spec_data'][$k]['market_price'] / 100;
								}
								if($object[$i]->specs['spec_data'][$k]['market_price'] / 100 > $max_mprice){
									$max_mprice = $object[$i]->specs['spec_data'][$k]['market_price'] / 100;
								}
							}
							$object[$i]->origin_price = '¥' . $min_price . ' - ¥' . $max_price;
							$object[$i]->current_price = '¥' . $min_mprice . ' - ¥' . $max_mprice;
						}else{
							$object[$i]->origin_price = '¥' . $min_price;
							$object[$i]->current_price = '¥' . $min_mprice;
						}
					}
					return ['rows' => $rows,'data' => $object];
				}

				return [];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		protected function validate(Request $request,$id = 0,$obj = null){
			// var_dump($request->post());
			$type = trim($request->post('type',''));
			if(!$type || empty($type)){
				return '无效的参数';
			}

			$goods_id = $request->post('goods_id');
			if(empty($goods_id) || !$goods_id){
				return '请先选择商品';
			}

			if($type == 'teamwork'){
				$specs = $request->post('specs');
				if(!$specs || !isset($specs['spec_data']) || !$specs['spec_data'] || !count($specs['spec_data'])){
					if($goods_id){
						return '请填写拼团价格';
					}else{
						return '请先选择商品';
					}
				}

				if($specs['id'] != $goods_id){
					return '无效的商品';
				}

				$service = new GoodsModel();
				$goods = $service->getList($request,$goods_id);
				if(!$goods || !is_object($goods)){
					return '无效的商品';
				}

				if($this->checkExists(['GoodsID'=>$goods_id,'Type'=>2],$id)){
					return '该商品已存在';
				}

				// var_dump($specs['spec_data']);
				for($i=0;$i<count($specs['spec_data']);$i++){
					$market_price = $specs['spec_data'][$i]['market_price'];
					if(!$market_price || empty($market_price) || !is_numeric($market_price)){
						return "规格:".$specs['spec_data'][$i]['first_title'].$specs['spec_data'][$i]['second_title']."拼团价格填写有误";
					}
					if($market_price * 100 > $specs['spec_data'][$i]['price'] * 100){
						return '拼团价格不能大于商品销价';
					}
					$specs['spec_data'][$i]['market_price'] = $market_price * 100;
				}

				$market_number = $request->post('market_number',0);
				if(empty($market_number) || !is_numeric($market_number)){
					return '拼团人数填写错误';
				}
				if(!$market_number || $market_number < 1){
					return '拼团人数不正确';
				}

				$market_time = $request->post('market_time',0);
				if(empty($market_time) || !is_numeric($market_time)){
					return '拼团时效填写错误';
				}

				if($market_time <= 0){
					return '拼团时效大于0';
				}

				$datetime = $request->post('datetime');
				if(!$datetime || !is_array($datetime) || !count($datetime) || count($datetime) < 2){
					return '拼团时间不能为空';
				}

				$start_time = $datetime[0];
				$end_time = $datetime[1];
				if(!$start_time){
					return '开始时间不能为空';
				}
				if(!$end_time){
					return '结束时间不能为空';
				}
				if($end_time <= $start_time){
					return '结束时间必须大于开始时间';
				}

				$data['type'] = 2;
				$data['goods_id'] = $goods_id;
				$data['specs'] = $this->getJsonData($specs);
				$data['market_number'] = $market_number;
				$data['market_time'] = $market_time;
				$data['start_time'] = TimeHelper::toTimestamp($start_time);
				$data['end_time'] = TimeHelper::toTimestamp($end_time);
			}

			if($type == 'seckill'){
				$specs = $request->post('specs');
				if(!$specs || !isset($specs['spec_data']) || !$specs['spec_data'] || !count($specs['spec_data'])){
					if($goods_id){
						return '请填写秒杀价格';
					}else{
						return '请先选择商品';
					}
				}

				if($specs['id'] != $goods_id){
					return '无效的商品';
				}

				$service = new GoodsModel();
				$goods = $service->getList($request,$goods_id);
				if(!$goods || !is_object($goods)){
					return '无效的商品';
				}

				// var_dump($specs['spec_data']);
				for($i=0;$i<count($specs['spec_data']);$i++){
					$market_price = $specs['spec_data'][$i]['market_price'];
					if(!$market_price || empty($market_price) || !is_numeric($market_price)){
						return "规格:".$specs['spec_data'][$i]['first_title'].$specs['spec_data'][$i]['second_title']."秒杀价格填写有误";
					}
					if($market_price * 100 > $specs['spec_data'][$i]['price'] * 100){
						return '秒杀价格不能大于商品销价';
					}
					$specs['spec_data'][$i]['market_price'] = $market_price * 100;
				}

				$period_id = $request->post('period_id',0);
				if(!$period_id){
					return '请选择秒杀时段';
				}

				$service = new MarketPeriodModel();
				$period = $service->getList($request,$period_id);
				if(!$period || !is_object($period)){
					return '无效的秒杀时段';
				}
				if(!$period->status){
					return '秒杀时段已关闭';
				}

				if($this->checkExists(['GoodsID'=>$goods_id,'Type'=>3,'PeriodID'=>$period_id],$id)){
					return '该时段商品已存在';
				}

				$data['type'] = 3;
				$data['goods_id'] = $goods_id;
				$data['specs'] = $this->getJsonData($specs);
				$data['period_id'] = $period_id;
			}

			if($type == 'counter'){
				$specs = $request->post('specs');
				if(!$specs || !isset($specs['spec_data']) || !$specs['spec_data'] || !count($specs['spec_data'])){
					if($goods_id){
						return '请填写砍价价格';
					}else{
						return '请先选择商品';
					}
				}

				if($specs['id'] != $goods_id){
					return '无效的商品';
				}

				$service = new GoodsModel();
				$goods = $service->getList($request,$goods_id);
				if(!$goods || !is_object($goods)){
					return '无效的商品';
				}

				if($this->checkExists(['GoodsID'=>$goods_id,'Type'=>4],$id)){
					return '该商品已存在';
				}

				// var_dump($specs['spec_data']);
				for($i=0;$i<count($specs['spec_data']);$i++){
					if(!isset($specs['spec_data'][$i]['base_amount'])){
						return '请填写砍价底价';
					}
					$base_amount = $specs['spec_data'][$i]['base_amount'];
					if(!$base_amount || empty($base_amount) || !is_numeric($base_amount)){
						return "规格:".$specs['spec_data'][$i]['first_title'].$specs['spec_data'][$i]['second_title']."砍价底价填写有误";
					}
					if($base_amount * 100 > $specs['spec_data'][$i]['price'] * 100){
						return '砍价底价不能大于商品销价';
					}
					$specs['spec_data'][$i]['base_amount'] = $base_amount * 100;

					if(!isset($specs['spec_data'][$i]['first_amount'])){
						return '请填写砍价首刀价';
					}
					$first_amount = $specs['spec_data'][$i]['first_amount'];
					if(!$first_amount || empty($first_amount) || !is_numeric($first_amount)){
						return "规格:".$specs['spec_data'][$i]['first_title'].$specs['spec_data'][$i]['second_title']."砍价首刀价填写有误";
					}
					if($first_amount < 0){
						return '砍价首刀价必须大于零';
					}
					if($first_amount * 100 >= $specs['spec_data'][$i]['price'] * 100){
						return '砍价首刀价不能大于或等于商品销价';
					}
					$specs['spec_data'][$i]['first_amount'] = $first_amount * 100;
				}

				$market_time = $request->post('market_time',0);
				if(empty($market_time) || !is_numeric($market_time)){
					return '砍价有效时长填写错误';
				}

				if($market_time <= 0){
					return '砍价有效时长大于0';
				}

				$datetime = $request->post('datetime');
				if(!$datetime || !is_array($datetime) || !count($datetime) || count($datetime) < 2){
					return '砍价活动时间不能为空';
				}

				$start_time = $datetime[0];
				$end_time = $datetime[1];
				if(!$start_time){
					return '开始时间不能为空';
				}
				if(!$end_time){
					return '结束时间不能为空';
				}
				if($end_time <= $start_time){
					return '结束时间必须大于开始时间';
				}

				$counter_type = $request->post('counter_type',0);
				if($counter_type){
					$fix_amount = $request->post('fix_amount',0);
					if(empty($fix_amount) || !is_numeric($fix_amount)){
						return '固定金额填写错误';
					}
					if(!$fix_amount){
						return '固定金额必须大于零';
					}
					$data['fix_amount'] = $fix_amount * 100;
					$data['min_amount'] = 0;
					$data['max_amount'] = 0;
				}else{
					$min_amount = $request->post('min_amount',0);
					if(empty($min_amount) || !is_numeric($min_amount)){
						return '最小金额填写错误';
					}
					if(!$min_amount){
						return '最小金额必须大于零';
					}
					$max_amount = $request->post('max_amount',0);
					if(empty($max_amount) || !is_numeric($max_amount)){
						return '最大金额填写错误';
					}
					if(!$max_amount){
						return '最大金额必须大于零';
					}
					$data['fix_amount'] = 0;
					$data['min_amount'] = $min_amount * 100;
					$data['max_amount'] = $max_amount * 100;
				}

				$data['type'] = 4;
				$data['goods_id'] = $goods_id;
				$data['specs'] = $this->getJsonData($specs);
				$data['market_time'] = $market_time;
				$data['start_time'] = TimeHelper::toTimestamp($start_time);
				$data['end_time'] = TimeHelper::toTimestamp($end_time);

			}

			if($type == 'section'){
				$specs = $request->post('specs');
				if(!$specs || !isset($specs['spec_data']) || !$specs['spec_data'] || !count($specs['spec_data'])){
					if($goods_id){
						return '请填写专区价格';
					}else{
						return '请先选择商品';
					}
				}

				if($specs['id'] != $goods_id){
					return '无效的商品';
				}

				$service = new GoodsModel();
				$goods = $service->getList($request,$goods_id);
				if(!$goods || !is_object($goods)){
					return '无效的商品';
				}

				// var_dump($specs['spec_data']);
				for($i=0;$i<count($specs['spec_data']);$i++){
					$market_price = $specs['spec_data'][$i]['market_price'];
					if(!$market_price || empty($market_price) || !is_numeric($market_price)){
						return "规格:".$specs['spec_data'][$i]['first_title'].$specs['spec_data'][$i]['second_title']."专区价格填写有误";
					}
					if($market_price * 100 > $specs['spec_data'][$i]['price'] * 100){
						return '专区价格不能大于商品销价';
					}
					$specs['spec_data'][$i]['market_price'] = $market_price * 100;
				}

				$prefecture_id = $request->post('prefecture_id',0);
				if(!$prefecture_id){
					return '请选择营销专区';
				}

				$service = new MarketPrefectureModel();
				$prefecture = $service->getList($request,$prefecture_id);
				if(!$prefecture || !is_object($prefecture)){
					return '无效的营销专区';
				}
				if(!$prefecture->status){
					return '营销专区已关闭';
				}

				if($this->checkExists(['GoodsID'=>$goods_id,'Type'=>5,'PrefectureID'=>$prefecture_id],$id)){
					return '该营销专区商品已存在';
				}

				$data['type'] = 5;
				$data['goods_id'] = $goods_id;
				$data['specs'] = $this->getJsonData($specs);
				$data['prefecture_id'] = $prefecture_id;
			}
			
			return isset($data) ? $data : true;
		}

		// protected function getTypeValue($value){
		// 	$k = 0;
		// 	foreach($this->type_value as $key => $val){
		// 		if($value == $val){
		// 			$k = $key;
		// 			break;
		// 		}
		// 	}
		// 	return $k;
		// }

		/**
		 * [getActionList description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		protected function getActionList(Request $request,$id = 0): array
		{
			$type = trim($request->post('type',''));
			// var_dump('type: '.$type);
			$action = [];

			$goods = [];
			$time = [];
			if($id){
				$data = $this->getList($request,$id);
				$goods = $this->getDecodeData($data->specs);
				if(isset($goods['spec_data'])){
					for($i=0;$i<count($goods['spec_data']);$i++){
						if(isset($goods['spec_data'][$i]['market_price'])){
							$goods['spec_data'][$i]['market_price'] /= 100;
						}
						if(isset($goods['spec_data'][$i]['base_amount'])){
							$goods['spec_data'][$i]['base_amount'] /= 100;
						}
						if(isset($goods['spec_data'][$i]['first_amount'])){
							$goods['spec_data'][$i]['first_amount'] /= 100;
						}
					}
				}
				$time = [date('Y-m-d H:i:s',$data->start_time),date('Y-m-d H:i:s',$data->end_time)];

				array_push($action,['type'=>'input','label'=>'商品ID','prop'=>'goods_id','value'=>$data->goods_id,'hidden'=>true]);
			}

			if($type == 'teamwork'){
				array_push($action,
					['type'=>'form-goods','label'=>'选择商品','prop'=>'specs','value'=>$goods,'attrs'=>['type'=>'拼团','style'=>['background-color'=>'#F8F8F8']]],
					['type'=>'input','label'=>'拼团人数','prop'=>'market_number','value'=>$id?$data->market_number:'','slot'=>['suffix'=>['value'=>'人']],'rules'=>['required'=>true,'message'=>'拼团人数不能为空']],
					['type'=>'input','label'=>'拼团时效','prop'=>'market_time','value'=>$id?$data->market_time:'','slot'=>['suffix'=>['value'=>'小时']],'rules'=>['required'=>true,'message'=>'拼团时效不能为空']],
					['type'=>'date-picker','label'=>'拼团活动时间','prop'=>'datetime','value'=>$time,'attrs'=>['style'=>['width'=>'700px'],'type'=>'datetimerange','range-separator'=>'To','start-placeholder'=>'开始时间','end-placeholder'=>'结束时间'],'rules'=>['required'=>true,'message'=>'活动时间不能为空']],
					['type'=>'input','label'=>'分享标题','prop'=>'share_title','value'=>$id?$data->share_title:''],
					['type'=>'input','label'=>'分享简介','prop'=>'share_desc','value'=>$id?$data->share_desc:'','attrs'=>['type'=>'textarea']],
					['type'=>'radio-group','label'=>'拼团状态','prop'=>'status','value'=>$id?$data->status:0,'children'=>[
						['type'=>'radio','label'=>'开启','value'=>1],
						['type'=>'radio','label'=>'关闭','value'=>0],
					]]
				);
			}

			if($type == 'seckill'){
				$service = new MarketPeriodModel();
				$period = $service->getList($request,'option');
				
				array_push($action,
					['type'=>'form-goods','label'=>'选择商品','prop'=>'specs','value'=>$goods,'attrs'=>['type'=>'秒杀','style'=>['background-color'=>'#F8F8F8']]],
					['type'=>'select','label'=>'秒杀时段','prop'=>'period_id','value'=>$id?$data->period_id:'','placeholder'=>'秒杀时段','children'=>$period,'attrs'=>['style'=>['width'=>'500px']],'rules'=>['required'=>true,'message'=>'请选择秒杀时段']],
					['type'=>'radio-group','label'=>'秒杀状态','prop'=>'status','value'=>$id?$data->status:0,'children'=>[
						['type'=>'radio','label'=>'开启','value'=>1],
						['type'=>'radio','label'=>'关闭','value'=>0],
					]]
				);
			}

			if($type == 'counter'){
				$rand_hidden = false;
				$fix_hidden = true;
				if($id){
					if($data->counter_type){
						$fix_hidden = false;
						$rand_hidden = true;
					}else{
						$rand_hidden = false;
						$fix_hidden = true;
					}
				}

				array_push($action,
					['type'=>'form-goods','label'=>'选择商品','prop'=>'specs','value'=>$goods,'attrs'=>['type'=>'砍价','style'=>['background-color'=>'#F8F8F8']]],
					// ['type'=>'input','label'=>'拼团人数','prop'=>'market_number','value'=>$id?$data->market_number:'','slot'=>['suffix'=>['value'=>'人']],'rules'=>['required'=>true,'message'=>'拼团人数不能为空']],
					['type'=>'input','label'=>'砍价有效时长','prop'=>'market_time','value'=>$id?$data->market_time:'','slot'=>['suffix'=>['value'=>'小时']],'rules'=>['required'=>true,'message'=>'砍价有效时长不能为空']],
					['type'=>'date-picker','label'=>'砍价活动时间','prop'=>'datetime','value'=>$time,'attrs'=>['style'=>['width'=>'700px'],'type'=>'datetimerange','range-separator'=>'To','start-placeholder'=>'开始时间','end-placeholder'=>'结束时间'],'rules'=>['required'=>true,'message'=>'活动时间不能为空']],
					['type'=>'radio-group','label'=>'购买方式','prop'=>'buy_type','value'=>$id?$data->buy_type:1,'children'=>[
						['type'=>'radio','label'=>'砍到底价','value'=>1],
						['type'=>'radio','label'=>'任意金额','value'=>0],
					]],
					['type'=>'radio-group','label'=>'每刀金额','prop'=>'counter_type','value'=>$id?$data->counter_type:0,'children'=>[
						['type'=>'radio','label'=>'随机金额','value'=>0],
						['type'=>'radio','label'=>'固定金额','value'=>1],
					]],
					['type'=>'form-group','label'=>'每刀随机金额','prop'=>'randamount','delimiter'=>'-','hidden'=>$rand_hidden,'children'=>[
						['type'=>'input','label'=>'最小金额','prop'=>'min_amount','value'=>$id?$data->min_amount:'','placeholder'=>'最小金额','slot'=>['suffix'=>['value'=>'元']],'attrs'=>['style'=>['width'=>'200px']]],
						['type'=>'input','label'=>'最大金额','prop'=>'max_amount','value'=>$id?$data->max_amount:'','placeholder'=>'最大金额','slot'=>['suffix'=>['value'=>'元']],'attrs'=>['style'=>['width'=>'200px']]],
					]],
					['type'=>'input','label'=>'每刀固定金额','prop'=>'fix_amount','value'=>$id?$data->fix_amount:'','placeholder'=>'固定金额','hidden'=>$fix_hidden,'slot'=>['suffix'=>['value'=>'元']],'attrs'=>['style'=>['width'=>'200px']]],
					['type'=>'input','label'=>'分享标题','prop'=>'share_title','value'=>$id?$data->share_title:''],
					['type'=>'input','label'=>'分享简介','prop'=>'share_desc','value'=>$id?$data->share_desc:'','attrs'=>['type'=>'textarea']],
					['type'=>'radio-group','label'=>'砍价状态','prop'=>'status','value'=>$id?$data->status:0,'children'=>[
						['type'=>'radio','label'=>'开启','value'=>1],
						['type'=>'radio','label'=>'关闭','value'=>0],
					]]
				);
			}

			if($type == 'section'){
				$service = new MarketPrefectureModel();
				$period = $service->getList($request,'option');
				
				array_push($action,
					['type'=>'form-goods','label'=>'选择商品','prop'=>'specs','value'=>$goods,'attrs'=>['type'=>'专区','style'=>['background-color'=>'#F8F8F8']]],
					['type'=>'select','label'=>'营销专区','prop'=>'prefecture_id','value'=>$id?$data->prefecture_id:'','placeholder'=>'营销专区','children'=>$period,'attrs'=>['style'=>['width'=>'500px']],'rules'=>['required'=>true,'message'=>'请选择营销专区']],
					['type'=>'radio-group','label'=>'专区状态','prop'=>'status','value'=>$id?$data->status:0,'children'=>[
						['type'=>'radio','label'=>'开启','value'=>1],
						['type'=>'radio','label'=>'关闭','value'=>0],
					]]
				);
			}

			return $action;
		}

		protected function getMapList(Request $request): array
		{
			$type = trim($request->post('type',''));

			$map = [
				['type'=>'id','label'=>'ID','prop'=>'id'],
				['type'=>'varchar','label'=>'商品名称','prop'=>'goods_title','width'=>200],
				['type'=>'img','label'=>'图片','prop'=>'goods_pic'],
			];

			if($type == 'teamwork'){
				array_push($map,['type'=>'varchar','label'=>'商品原价','prop'=>'origin_price','width'=>120]);
				array_push($map,['type'=>'varchar','label'=>'拼团价','prop'=>'current_price','width'=>120]);
				array_push($map,['type'=>'int','label'=>'拼团人数','prop'=>'market_number','suffix'=>'人','width'=>100]);
				array_push($map,['type'=>'int','label'=>'拼团时效','prop'=>'market_time','suffix'=>'小时','width'=>100]);
				array_push($map,['type'=>'varchar','label'=>'开始时间','prop'=>'start_time','width'=>180]);
				array_push($map,['type'=>'varchar','label'=>'结束时间','prop'=>'end_time','width'=>180]);
			}

			if($type == 'seckill'){
				array_push($map,['type'=>'varchar','label'=>'秒杀时段','prop'=>'period_title']);
				array_push($map,['type'=>'varchar','label'=>'商品原价','prop'=>'origin_price']);
				array_push($map,['type'=>'varchar','label'=>'秒杀价','prop'=>'current_price']);
			}

			if($type == 'counter'){
				array_push($map,['type'=>'varchar','label'=>'商品原价','prop'=>'origin_price','width'=>120]);
				array_push($map,['type'=>'varchar','label'=>'砍价底价','prop'=>'base_amount','width'=>120]);
				array_push($map,['type'=>'varchar','label'=>'首刀金额','prop'=>'first_amount','width'=>120]);
				array_push($map,['type'=>'int','label'=>'砍价时效','prop'=>'market_time','suffix'=>'小时','width'=>100]);
				array_push($map,['type'=>'varchar','label'=>'每刀金额','prop'=>'counter_title','width'=>120]);
				array_push($map,['type'=>'varchar','label'=>'购买方式','prop'=>'buy_title','width'=>100]);
				array_push($map,['type'=>'varchar','label'=>'开始时间','prop'=>'start_time','width'=>180]);
				array_push($map,['type'=>'varchar','label'=>'结束时间','prop'=>'end_time','width'=>180]);
			}

			if($type == 'section'){
				array_push($map,['type'=>'varchar','label'=>'营销专区','prop'=>'prefecture_title']);
				array_push($map,['type'=>'varchar','label'=>'商品原价','prop'=>'origin_price']);
				array_push($map,['type'=>'varchar','label'=>'专区价','prop'=>'current_price']);
			}

			array_push($map,['type'=>'switch','label'=>'状态','prop'=>'status']);

			return $map;
		}
	}
?>