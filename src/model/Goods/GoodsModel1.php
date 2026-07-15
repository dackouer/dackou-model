<?php
	namespace dackou\model\Goods;

	use support\Request;
	use support\Db;

	class GoodsModel extends \dackou\Model{
		protected $table = 'Goods';
		protected $title = '商品';
		protected $option_key = 'system,goods';
		protected $action = 'action';
		protected $type_value = [1=>'实体商品',2=>'电子卡密',3=>'酒店',4=>'景区',5=>'民宿'];
		protected $delivery_value = [1=>'统一发货',2=>'分拆发货'];
		protected $logistics_value = [1=>'物流快递',2=>'同城配送',3=>'虚拟发货',4=>'用户自提'];
		protected $status_value = [-1=>'仓库',1=>'上架',0=>'下架'];
		protected $spec_form = [
			['type' => 'upload','label' => '图片','prop' => 'pic','value' => '','placeholder'=>'','prefix'=>'','suffix'=>'','viladed'=>false,'required'=>true],
			['type' => 'input','label' => '价格','prop' => 'price','value' => '','placeholder'=>'0.00','prefix'=>'¥','suffix'=>'元','viladed'=>true,'required'=>true],
            ['type' => 'input','label' => '原价','prop' => 'origin_price','value' => '','placeholder'=>'0.00','prefix'=>'¥','suffix'=>'元','viladed'=>true,'required'=>true],
            ['type' => 'input','label' => '成本','prop' => 'cost_price','value' => '','placeholder'=>'0.00','prefix'=>'¥','suffix'=>'元','viladed'=>true,'required'=>true],
            ['type' => 'input','label' => '基数','prop' => 'base_number','value' => 1,'placeholder'=>'1','prefix'=>'下单以','suffix'=>'件的倍数递增','viladed'=>true,'required'=>true],
            ['type' => 'input','label' => '限购','prop' => 'limit_count','value' => '','placeholder'=>'0','prefix'=>'商品限购','suffix'=>'最小基数','viladed'=>true,'required'=>true],
            ['type' => 'input','label' => '限单','prop' => 'limit_order','value' => '','placeholder'=>'0','prefix'=>'订单限购','suffix'=>'次','viladed'=>true,'required'=>true],
            ['type' => 'input','label' => '单位','prop' => 'unit','value' => '','placeholder'=>'','prefix'=>'','suffix'=>'','viladed'=>true,'required'=>true],
            ['type' => 'input','label' => 'SKU','prop' => 'sku','value' => 1,'placeholder'=>'1','prefix'=>'占','suffix'=>'件库存','viladed'=>true,'required'=>true],
            ['type' => 'input','label' => '库存','prop' => 'stock','value' => 0,'placeholder'=>'0','prefix'=>'','suffix'=>'件','viladed'=>true,'required'=>true],
            ['type' => 'input','label' => '规格编码','prop' => 'spec_code','value' => '','placeholder'=>'','prefix'=>'','suffix'=>'','viladed'=>false,'required'=>true],
		];
		protected $showFilter = ['goods.Content as content','goods.Picture as picture'];

		protected function getFieldList(Request $request,mixed $flag = false): array
		{
			// $goods_is_type = isset($this->config['goods_is_type']) ? $this->config['goods_is_type'] : 0;
			// $system_is_merchant = isset($this->config['system_is_merchant']) ? $this->config['system_is_merchant'] : 0;
			// $goods_is_brand = isset($this->config['goods_is_brand']) ? $this->config['goods_is_brand'] : 0;
			// $goods_is_member_price = isset($this->config['goods_is_member_price']) ? $this->config['goods_is_member_price'] : 0;
			// $goods_is_tags = isset($this->config['goods_is_tags']) ? $this->config['goods_is_tags'] : 0;
			// $goods_is_attr = isset($this->config['goods_is_attr']) ? $this->config['goods_is_attr'] : 0;
			// $goods_is_spec = isset($this->config['goods_is_spec']) ? $this->config['goods_is_spec'] : 0;
			// $goods_is_share = isset($this->config['goods_is_share']) ? $this->config['goods_is_share'] : 0;
			// $goods_is_commission = isset($this->config['goods_is_commission']) ? $this->config['goods_is_commission'] : 0;
			// $goods_is_comment = isset($this->config['goods_is_comment']) ? $this->config['goods_is_comment'] : 0;
			// $goods_is_presale = isset($this->config['goods_is_presale']) ? $this->config['goods_is_presale'] : 0;

			$goods_is_type = $this->getConfig('goods_is_type');
			$system_is_merchant = $this->getConfig('system_is_merchant');
			$goods_is_brand = $this->getConfig('goods_is_brand');
			$goods_is_member_price = $this->getConfig('goods_is_member_price');
			$goods_is_tags = $this->getConfig('goods_is_tags');
			$goods_is_attr = $this->getConfig('goods_is_attr');
			$goods_is_spec = $this->getConfig('goods_is_spec');
			$goods_is_share = $this->getConfig('goods_is_share');
			$goods_is_commission = $this->getConfig('goods_is_commission');
			$goods_is_comment = $this->getConfig('goods_is_comment');
			$goods_is_presale = $this->getConfig('goods_is_presale');

			$fields = [
				'id' 				 => 'id',
				'cate_id' 			 => 'CateID',
				'goods_title' 		 => 'GoodsTitle',
				'keyword' 			 => 'Keyword',
				'desc' 				 => 'Description',
				'goods_code' 		 => 'GoodsCode',
				'pic' 				 => 'Pic',
				'picture' 			 => 'Picture',
				'goods_number' 		 => 'GoodsNumber',
				'unit_id' 			 => 'UnitID',
				'free_unit' 		 => 'FreeUnit',
				'freight_amount' 	 => 'FreightAmount',
				'freight_accumulate' => 'FreightAccumulate',
				'free_amount' 		 => 'FreeAmount',
				'delivery_mode' 	 => 'DeliveryMode',
				'logistics_type' 	 => 'LogisticsType',
				'refund_time' 		 => 'RefundTime',
				'delivery_time' 	 => 'DeliveryTime',
				'give_score' 		 => 'GiveScore',
				'deduct_score' 		 => 'DeductScore',
				'hits' 				 => 'Hits',
				'collects' 			 => 'Collects',
				'sales' 			 => 'Sales',
				'content' 			 => 'Content',
				'status' 			 => 'Status',
				'create_time' 		 => 'CreateTime',
			];

			if($goods_is_type){
				$fields['type_id'] = 'TypeID';
			}
			if($system_is_merchant){
				$fields['merchant_id'] = 'MerchantID';
			}
			if($goods_is_brand){
				$fields['brand_id'] = 'BrandID';
			}
			if($goods_is_tags){
				$fields['tags'] = 'Tags';
			}
			if($goods_is_attr){
				$fields['attrs'] = 'Attrs';
			}
			if(!$goods_is_spec){
				$fields['price'] = 'Price';
				$fields['origin_price'] = 'OriginPrice';
				$fields['cost_price'] = 'CostPrice';
				$fields['base_number'] = 'BaseNumber';
				$fields['unit_id'] = 'UnitID';
				$fields['stock'] = 'Stock';
				$fields['alarm_stock'] = 'AlarmStock';
				$fields['limit_count'] = 'LimitCount';
				$fields['limit_order'] = 'LimitOrder';
			}else{
				$fields['specs'] = 'Specs';
				$fields['spec_data'] = 'SpecData';
				$fields['spec_option'] = 'SpecOption';
			}

			if($goods_is_share){
				$fields['is_share'] = 'IsShare';
				$fields['share_title'] = 'ShareTitle';
				$fields['share_pic'] = 'SharePic';
			}

			if($goods_is_commission){
				$fields['is_commission'] = 'IsCommission';
				$fields['commission_value'] = 'CommissionValue';
			}
			if($goods_is_comment){
				$fields['is_comment'] = 'IsComment';
				$fields['comment_number'] = 'CommentNumber';
			}

			if($goods_is_presale){
				$fields['presale'] = 'Presale';
			}

			if($flag === true){
				$field = [];
				foreach($fields as $k => $v){
					array_push($field,$this->table.'.'.$v . ' as ' . $k);
				}
    			return $field;
    		}elseif($flag === false){
				$field = [];
				foreach($fields as $k => $v){
					array_push($field,$v . ' as ' . $k);
				}
    			return $field;
    		}elseif($flag === 'key'){
    			return $fields;
    		}else{
    			return $fields;
    		}
		}

		protected function getPageList(Request $request){
			try{
				// $goods_is_type = isset($this->config['goods_is_type']) ? $this->config['goods_is_type'] : 0;
				// $system_is_merchant = isset($this->config['system_is_merchant']) ? $this->config['system_is_merchant'] : 0;
				// $goods_is_brand = isset($this->config['goods_is_brand']) ? $this->config['goods_is_brand'] : 0;
				// $goods_is_member_price = isset($this->config['goods_is_member_price']) ? $this->config['goods_is_member_price'] : 0;
				// $goods_is_tags = isset($this->config['goods_is_tags']) ? $this->config['goods_is_tags'] : 0;
				// $goods_is_attr = isset($this->config['goods_is_attr']) ? $this->config['goods_is_attr'] : 0;
				// $goods_is_spec = isset($this->config['goods_is_spec']) ? $this->config['goods_is_spec'] : 0;
				// $goods_is_share = isset($this->config['goods_is_share']) ? $this->config['goods_is_share'] : 0;
				// $goods_is_commission = isset($this->config['goods_is_commission']) ? $this->config['goods_is_commission'] : 0;
				// $goods_is_comment = isset($this->config['goods_is_comment']) ? $this->config['goods_is_comment'] : 0;
				// $goods_is_presale = isset($this->config['goods_is_presale']) ? $this->config['goods_is_presale'] : 0;

				$goods_is_type = $this->getConfig('goods_is_type');
				$system_is_merchant = $this->getConfig('system_is_merchant');
				$goods_is_brand = $this->getConfig('goods_is_brand');
				$goods_is_member_price = $this->getConfig('goods_is_member_price');
				$goods_is_tags = $this->getConfig('goods_is_tags');
				$goods_is_attr = $this->getConfig('goods_is_attr');
				$goods_is_spec = $this->getConfig('goods_is_spec');
				$goods_is_share = $this->getConfig('goods_is_share');
				$goods_is_commission = $this->getConfig('goods_is_commission');
				$goods_is_comment = $this->getConfig('goods_is_comment');
				$goods_is_presale = $this->getConfig('goods_is_presale');

				$field = $this->getList($request,'field',true);
				array_push($field,'goods_cate.Title as cate_title');
				array_push($field,'super_cate.ID as super_id');
				array_push($field,'super_cate.Title as super_title');
				array_push($field,'unit.Title as unit_name');
				if($system_is_merchant){
					array_push($field,'MerchantName as merchant_name');
				}
				if($goods_is_brand){
					if($system_is_merchant){
						array_push($field,'merchant_brand.Title as brand_name');
					}else{
						array_push($field,'BrandName as brand_name');
					}
				}
				$where = $this->getWhere($request);
				if($system_is_merchant){
					$merchant_id = $request->input('merchant_id',$request->input('mch_id',0));
					if($merchant_id){
						array_push($where,[$this->table.'.MerchantID','=',$merchant_id]);
					}
				}

				$rows = Db::table($this->table)
							->join('goods_cate',$this->table.'.CateID','=','goods_cate.ID')
							->join('goods_cate as super_cate','goods_cate.PID','=','super_cate.ID')
							->join('unit',$this->table.'.UnitID','=','unit.ID');
				if($system_is_merchant){
					$rows = $rows->join('merchant',$this->table.'.MerchantID','=','merchant.ID');
				}
				if($goods_is_brand){
					if($system_is_merchant){
						$rows = $rows->join('merchant_brand',$this->table.'.BrandID','=','merchant_brand.ID');
					}else{
						$rows = $rows->join('brand',$this->table.'.BrandID','=','brand.ID');
					}
				}
				$rows = $rows->where($where)
							->count();
				[$offset,$limit] = $this->getLimit($request);
				$object = Db::table($this->table)
							->join('goods_cate',$this->table.'.CateID','=','goods_cate.ID')
							->join('goods_cate as super_cate','goods_cate.PID','=','super_cate.ID')
							->join('unit',$this->table.'.UnitID','=','unit.ID');
				if($system_is_merchant){
					$object = $object->join('merchant',$this->table.'.MerchantID','=','merchant.ID');
				}
				if($goods_is_brand){
					if($system_is_merchant){
						$object = $object->join('merchant_brand',$this->table.'.BrandID','=','merchant_brand.ID');
					}else{
						$object = $object->join('brand',$this->table.'.BrandID','=','brand.ID');
					}
				}

				if($this->showFilter){
					foreach($this->showFilter as $filter){
						$filterIndex = \array_search($filter, $field);
						if($filterIndex !== false){
							unset($field[$filterIndex]);
						}
					}
				}
				
				$object = $object->select(...$field)
							->where($where)
							->offset($offset)
							->limit($limit)
							->get();
				if($object){
					foreach($object as $k => $v){
						if($goods_is_type){
							$object[$k]->type_name = $this->type_value[$object[$k]->type_id];
						}
						$object[$k]->cate_title = $object[$k]->super_title . '-' . $object[$k]->cate_title;
						$object[$k]->status_title = $this->status_value[$object[$k]->status];
						$object[$k]->create_time = $this->getDateTime($object[$k]->create_time);
					}
				}
				
				return ['rows' => $rows,'data' => $object];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		protected function getListById(Request $request,$id = 0){
			try{
				if(!$id){
					return 100005;
				}

				// $goods_is_type = isset($this->config['goods_is_type']) ? $this->config['goods_is_type'] : 0;
				// $system_is_merchant = isset($this->config['system_is_merchant']) ? $this->config['system_is_merchant'] : 0;
				// $goods_is_brand = isset($this->config['goods_is_brand']) ? $this->config['goods_is_brand'] : 0;
				// $goods_is_member_price = isset($this->config['goods_is_member_price']) ? $this->config['goods_is_member_price'] : 0;
				// $goods_is_tags = isset($this->config['goods_is_tags']) ? $this->config['goods_is_tags'] : 0;
				// $goods_is_attr = isset($this->config['goods_is_attr']) ? $this->config['goods_is_attr'] : 0;
				// $goods_is_spec = isset($this->config['goods_is_spec']) ? $this->config['goods_is_spec'] : 0;
				// $goods_is_share = isset($this->config['goods_is_share']) ? $this->config['goods_is_share'] : 0;
				// $goods_is_commission = isset($this->config['goods_is_commission']) ? $this->config['goods_is_commission'] : 0;
				// $goods_is_comment = isset($this->config['goods_is_comment']) ? $this->config['goods_is_comment'] : 0;
				// $goods_is_presale = isset($this->config['goods_is_presale']) ? $this->config['goods_is_presale'] : 0;

				$goods_is_type = $this->getConfig('goods_is_type');
				$system_is_merchant = $this->getConfig('system_is_merchant');
				$goods_is_brand = $this->getConfig('goods_is_brand');
				$goods_is_member_price = $this->getConfig('goods_is_member_price');
				$goods_is_tags = $this->getConfig('goods_is_tags');
				$goods_is_attr = $this->getConfig('goods_is_attr');
				$goods_is_spec = $this->getConfig('goods_is_spec');
				$goods_is_share = $this->getConfig('goods_is_share');
				$goods_is_commission = $this->getConfig('goods_is_commission');
				$goods_is_comment = $this->getConfig('goods_is_comment');
				$goods_is_presale = $this->getConfig('goods_is_presale');

				$field = $this->getList($request,'field',true);
				array_push($field,'goods_cate.Title as cate_title');
				array_push($field,'super_cate.ID as super_id');
				array_push($field,'super_cate.Title as super_title');
				array_push($field,'unit.Title as unit_name');
				if($goods_is_brand){
					array_push($field,'BrandName as brand_name');
				}
				$where = $this->getWhere($request);
				array_push($where,[$this->table.'.ID','=',$id]);

				$object = Db::table($this->table)
							->join('goods_cate',$this->table.'.CateID','=','goods_cate.ID')
							->join('goods_cate as super_cate','goods_cate.PID','=','super_cate.ID')
							->join('unit',$this->table.'.UnitID','=','unit.ID');
				if($system_is_merchant){
					$object = $object->join('merchant',$this->table.'.MerchantID','=','merchant.ID');
				}
				if($goods_is_brand){
					$object = $object->join('goods_brand',$this->table.'.BrandID','=','goods_brand.ID');
				}
				$object = $object->select(...$field)
							->where($where)
							->first();
				if($object){
					if(!$goods_is_spec){
						$object->price /= 100;
						$object->origin_price /= 100;
						$object->cost_price /= 100;
					}else{
						$object->spec_option = $this->getDecodeData($object->spec_option);

						$service = new GoodsSpecModel();
						$object->spec_data = $service->getList($request,'goods',$id);
						var_dump($object->spec_data);
					}
					if($goods_is_type){
						$object->type_name = $this->type_value[$object->type_id];
					}
					$object->cate_title = $object->super_title . '-' . $object->cate_title;
					$object->picture = $this->getDecodeData($object->picture);
					if($goods_is_tags){
						$object->tags = $this->getDecodeData($object->tags);
					}
					if($goods_is_attr){
						$object->attrs = $this->getDecodeData($object->attrs);
					}
					if($goods_is_commission){
						$object->commission_value = $this->getDecodeData($object->commission_value);
					}
					$object->status_title = $this->status_value[$object->status];
					$object->create_time = $this->getDateTime($object->create_time);

					if($system_is_merchant){
						$object->merchant = [];
						$_class_name = $this->getClassName('Merchant');
						if($_class_name){
							$service = new $_class_name();
							$object->merchant = $service->getList($request,$object->merchant_id);
						}
					}

					if($goods_is_spec && $object->specs){
						$object->specs = $this->getDecodeData($object->specs);
					}

					$object->min_price = 0;
					$object->max_price = 0;

					if($goods_is_spec && $object->spec_data && count($object->spec_data)){
						$min_price = $object->spec_data[0]->price;
						$max_price = $object->spec_data[0]->price;
						if($object->spec_data){
							for($i=1;$i<count($object->spec_data);$i++){
								if($object->spec_data[$i]->price < $min_price){
									$min_price = $object->spec_data[$i]->price;
								}
								if($object->spec_data[$i]->price > $max_price){
									$max_price = $object->spec_data[$i]->price;
								}
							}
						}

						$object->min_price = $min_price;
						$object->max_price = $max_price;
					}

					if($goods_is_comment && $object->is_comment){
						// $service = new GoodsCommentModel();
						// $object->comment_data = $service->getList($request,'goods',$object->id);
					}
				}

				return $object;
			}catch(\Exception $e){
				var_dump($this->getExceptionError($e));
				return $this->getExceptionError($e);
			}
		}

		protected function setTruncate(Request $request){
			Db::table('goods_spec')->truncate();
			return true;
		}

		protected function setExcute(Request $request,$data,$flag = false){
			return true;
			$service = new GoodsSpecModel();
			return $service->setData($request,$data,$flag);
		}

		protected function validate(Request $request,$id = 0,$obj = null){
			// $goods_is_type = isset($this->config['goods_is_type']) ? $this->config['goods_is_type'] : 0;
			// $system_is_merchant = isset($this->config['system_is_merchant']) ? $this->config['system_is_merchant'] : 0;
			// $goods_is_brand = isset($this->config['goods_is_brand']) ? $this->config['goods_is_brand'] : 0;
			// $goods_is_member_price = isset($this->config['goods_is_member_price']) ? $this->config['goods_is_member_price'] : 0;
			// $goods_is_tags = isset($this->config['goods_is_tags']) ? $this->config['goods_is_tags'] : 0;
			// $goods_is_attr = isset($this->config['goods_is_attr']) ? $this->config['goods_is_attr'] : 0;
			// $goods_is_spec = isset($this->config['goods_is_spec']) ? $this->config['goods_is_spec'] : 0;
			// $goods_is_share = isset($this->config['goods_is_share']) ? $this->config['goods_is_share'] : 0;
			// $goods_is_commission = isset($this->config['goods_is_commission']) ? $this->config['goods_is_commission'] : 0;
			// $goods_is_comment = isset($this->config['goods_is_comment']) ? $this->config['goods_is_comment'] : 0;
			// $goods_is_presale = isset($this->config['goods_is_presale']) ? $this->config['goods_is_presale'] : 0;

			$goods_is_type = $this->getConfig('goods_is_type');
			$system_is_merchant = $this->getConfig('system_is_merchant');
			$goods_is_brand = $this->getConfig('goods_is_brand');
			$goods_is_member_price = $this->getConfig('goods_is_member_price');
			$goods_is_tags = $this->getConfig('goods_is_tags');
			$goods_is_attr = $this->getConfig('goods_is_attr');
			$goods_is_spec = $this->getConfig('goods_is_spec');
			$goods_is_share = $this->getConfig('goods_is_share');
			$goods_is_commission = $this->getConfig('goods_is_commission');
			$goods_is_comment = $this->getConfig('goods_is_comment');
			$goods_is_presale = $this->getConfig('goods_is_presale');

			// 商品类型
			$type_id = $goods_is_type ? $request->post('type_id',1) : 1;
			if($goods_is_type){
				if(!in_array($type_id,[1,2,3,4,5])){
					return '无效的商品类型';
				}
			}

			// 商户
			if($system_is_merchant){
				$merchant_id = $request->input('merchant_id',0);
				if(!$merchant_id){
					return '请选择商户';
				}

				$_class_name = $this->getClassName('Merchant');
				if(!$_class_name){
					return '无效的商户';
				}
				$service = new $_class_name();
				$merchant = $service->getList($request,$merchant_id);
				if(!$merchant || !is_object($merchant)){
					return '无效的商户';
				}

				if($merchant->status !== 1){
					return '商户未通过审核,无法录入商品';
				}

				$data['merchant_id'] = $merchant_id;
			}

			// 商品类别
			$cate_id = $request->post('cate_id');
			if(is_array($cate_id)){
				$cate_id = end($cate_id);
			}
			if(!$cate_id || !is_numeric($cate_id)){
				return '请选择商品类别';
			}
			$service = new GoodsCateModel();
			$cate = $service->getList($request,$cate_id);
			if(!$cate || !is_object($cate)){
				return '无效的商品类别';
			}

			// 商品标题
			$goods_title = trim($request->post('goods_title',''));
			if(!$goods_title){
				return '商品标题不能为空';
			}
			if(strlen($goods_title) < 10 || strlen($goods_title) > 200){
				return '商品标题长度大于10小于200';
			}
			if($this->checkExists(['GoodsTitle'=>$goods_title],$id)){
				return '商品标题已存在,请重新输入';
			}

			// 商品描述
			$desc = trim($request->post('desc',''));
			if(!$desc){
				return '商品描述不能为空';
			}
			if(strlen($desc) < 10 || strlen($desc) > 200){
				return '商品描述长度大于10小于300';
			}
			if($this->checkExists(['Description'=>$desc],$id)){
				return '商品描述已存在,请重新输入';
			}

			// 商品编码
			$goods_code = trim($request->post('goods_code',''));
			if(!$goods_code){
				return '商品编码不能为空';
			}
			if($this->checkExists(['GoodsCode'=>$goods_code],$id)){
				return '商品编码已存在,请重新输入';
			}

			// 主图与轮播图
			$pic = trim($request->post('pic',''));
			$picture = $request->post('picture');
			if(!is_array($picture)){
				$picture = \explode(',',$picture);
			}
			if(!$picture || !count($picture)){
				return '请上图商品轮播图';
			}
			if(count($picture) > 5){
				return '轮播图限制最多上传5张';
			}

			if(!$pic){
				$pic = $picture[0];
			}

			// 商品品牌
			$brand_id = $goods_is_brand ? ($request->post('brand_id',0)) : 0;
			if($goods_is_brand){
				if(!$brand_id){
					return '请选择商品品牌';
				}
				$service = new GoodsBrandModel();
				$brand = $service->getList($request,$brand_id);
				if(!$brand || !is_object($brand)){
					return '无效的商品品牌';
				}

				$data['brand_id'] = $brand_id;
			}else{
				$service = new GoodsBrandModel();
				$brand = $service->getList($request,1);
				if($brand && is_object($brand) && $brand->brand_name == '无'){
					$data['brand_id'] = 1;
				}else{
					$data['brand_id'] = 0;
				}
			}

			// 商品标签
			$tags = $goods_is_tags ? $request->post('tags') : [];

			// 商品单位
			$unit_id = $request->post('unit_id',1);
			if(!$unit_id){
				return '请选择商品基本单位';
			}
			if(!is_numeric($unit_id) || $unit_id <= 0){
				return '无效的商品基本单位';
			}

			$_class_name = $this->getClassName('Unit');
			if(!$_class_name){
				return '无效的商品基本单位';
			}
			$service = new $_class_name();
			$unit = $service->getList($request,$unit_id);
			if(!$unit || !is_object($unit)){
				return '无效的商品基本单位';
			}

			// 商品属性
			if($goods_is_attr){
				$attrs = $request->post('attrs');

				$data['attrs'] = $attrs;
			}else{
				$data['attrs'] = [];
			}

			// 商品规格
			if($goods_is_spec){
				$specs = $request->post('specs');

				$data['specs'] = $specs;
			}else{
				$data['specs'] = [];

				foreach($this->spec_form as $item){
					if($item['viladed']){
						$prop = $item['prop'];
						$item_value = $request->post($prop) ?? $item['value'];
						if(in_array($item_value,['','0','0.0','0.00','0.000','0.000'])){
							$item_value = 0;
						}
						if($item['required']){
							if(!is_numeric($item_value)){
								return $item['label'].'只能填数字';
							}
							if($item_value < 0){
								return $item['label'].'填写不正确';
							}
						}

						$data[$prop] = $item_value;
					}
				}
			}

			// 发货
			$delivery_mode = $request->post('delivery_mode',1);
			$logistics_type = $request->post('logistics_type',1);

			if(!in_array($delivery_mode,[1,2])){
				return '无效的发货方式';
			}

			if(!in_array($logistics_type,[1,2,3,4])){
				return '无效的物流模式';
			}

			// 邮费
			$freight_amount = $request->post('freight_amount',0);
			if($freight_amount == '' || is_null($freight_amount)){
				$freight_amount = 0;
			}
			$freight_accumulate = $request->post('freight_accumulate',0);
			if($freight_accumulate == '' || is_null($freight_accumulate)){
				$freight_accumulate = 0;
			}
			$free_unit = $request->post('free_unit',0);
			if($free_unit == '' || is_null($free_unit)){
				$free_unit = 0;
			}
			$free_amount = $request->post('free_amount',0);
			if($free_amount == '' || is_null($free_amount)){
				$free_amount = 0;
			}

			if(!is_numeric($freight_amount) || $freight_amount < 0){
				return '基础邮费填写有误';
			}
			if(!in_array($freight_accumulate,[0,1])){
				return '无效的运费按下单基数累计值';
			}
			if(!is_numeric($free_unit) || $free_unit < 0){
				return '满件包邮值填写有误';
			}
			if(!is_numeric($free_amount) || $free_amount < 0){
				return '满额包邮值填写有误';
			}


			// 承诺与服务
			$delivery_time = $request->post('delivery_time',0);
			$refund_time = $request->post('refund_time',0);

			if($delivery_time == '' || is_null($delivery_time)){
				$delivery_time = 0;
			}

			if($refund_time == '' || is_null($refund_time)){
				$refund_time = 0;
			}

			if(!is_numeric($delivery_time) || $delivery_time < 0){
				return '承诺发货时间填写有误';
			}

			if(!is_numeric($refund_time) || $refund_time < 0){
				return '承诺无理由退换货时间填写有误';
			}

			// 积分与抵扣
			$give_score = $request->post('give_score',0);
			$deduct_score = $request->post('deduct_score',0);

			if($give_score == '' || is_null($give_score)){
				$give_score = 0;
			}

			if($deduct_score == '' || is_null($deduct_score)){
				$deduct_score = 0;
			}

			if(!is_numeric($give_score) || $give_score < 0){
				return '赠送积分填写有误';
			}

			if(!is_numeric($deduct_score) || $deduct_score < 0){
				return '最大抵扣积分填写有误';
			}

			// 分享
			if($goods_is_share){
				$is_share = $request->post('is_share',0);
				$share_title = trim($request->post('share_title',''));
				$share_pic = trim($request->post('share_pic',''));

				if(!in_array($is_share,[0,1])){
					return '无效的开启分享值';
				}

				$data['is_share'] = $is_share;
				$data['share_title'] = $share_title;
				$data['share_pic'] = $share_pic;
			}

			// 分佣
			if($goods_is_commission){
				$is_commission = $request->post('is_commission',0);
				$commission_value = $request->post('commission_value',[]);

				$data['is_commission'] = $is_commission;
				$data['commission_value'] = $commission_value;
			}

			// 评论
			if($goods_is_comment){
				$is_comment = $request->post('is_comment',0);

				$data['is_comment'] = $is_comment;
			}

			// 预销售
			if($goods_is_presale){
				$presale = $request->post('presale',0);
				if($presale == '' || is_null($presale)){
					$presale = 0;
				}
				if(!is_numeric($presale) || $presale < 0){
					return '预销售值填写有误';
				}

				$data['presale'] = $presale;
			}else{
				$data['presale'] = 0;
			}

			// 状态
			$status = $request->post('status',0);

			// 内容详情
			$content = trim($request->post('content',''));


			$data['cate_id'] = $cate_id;
			$data['goods_title'] = $goods_title;
			$data['desc'] = $desc;
			$data['goods_code'] = $goods_code;
			$data['pic'] = $pic;
			$data['picture'] = $picture;
			$data['tags'] = $tags;
			$data['unit_id'] = $unit_id;
			$data['delivery_mode'] = $delivery_mode;
			$data['logistics_type'] = $logistics_type;
			$data['freight_amount'] = $freight_amount;
			$data['freight_accumulate'] = $freight_accumulate;
			$data['free_unit'] = $free_unit;
			$data['free_amount'] = $free_amount;
			$data['delivery_time'] = $delivery_time;
			$data['refund_time'] = $refund_time;
			$data['give_score'] = $give_score;
			$data['deduct_score'] = $deduct_score;
			$data['status'] = $status;
			$data['content'] = $content;

			return $data;
		}

		protected function getActionList(Request $request,$id = 0): array
		{
			if($id){
				$data = $this->getList($request,$id);
				// var_dump($data);
			}

			$is_merchant = isset($this->config['system_is_merchant'])&&$this->config['system_is_merchant'] ? true : false;
			$merchant_id = $request->input('merchant_id',0);
			// var_dump('is_merchant: '.$is_merchant);
			if($is_merchant){
				if($merchant_id){
					$merchant_form = ['type'=>'input','label'=>'所属商户','prop'=>'merchant_id','value'=>$merchant_id,'hidden'=>true];
				}else{
					$_class_name = $this->getClassName('Merchant');
					$service = new $_class_name();
					$merchant = $service->getList($request,'option');
					$merchant_form = ['type'=>'select','label'=>'所属商户','prop'=>'merchant_id','value'=>$data->merchant_id ?? '','children'=>$merchant,'rules'=>['required'=>true,'message'=>'请选择所属商户']];
				}
			}


			// var_dump($this->config);
			// $goods_is_type = isset($this->config['goods_is_type']) ? $this->config['goods_is_type'] : 0;
			// $system_is_merchant = isset($this->config['system_is_merchant']) ? $this->config['system_is_merchant'] : 0;
			// $goods_is_brand = isset($this->config['goods_is_brand']) ? $this->config['goods_is_brand'] : 0;
			// $goods_is_member_price = isset($this->config['goods_is_member_price']) ? $this->config['goods_is_member_price'] : 0;
			// $goods_is_tags = isset($this->config['goods_is_tags']) ? $this->config['goods_is_tags'] : 0;
			// $goods_is_attr = isset($this->config['goods_is_attr']) ? $this->config['goods_is_attr'] : 0;
			// $goods_is_spec = isset($this->config['goods_is_spec']) ? $this->config['goods_is_spec'] : 0;
			// $goods_is_share = isset($this->config['goods_is_share']) ? $this->config['goods_is_share'] : 0;
			// $goods_is_commission = isset($this->config['goods_is_commission']) ? $this->config['goods_is_commission'] : 0;
			// $goods_is_comment = isset($this->config['goods_is_comment']) ? $this->config['goods_is_comment'] : 0;
			// $goods_is_presale = isset($this->config['goods_is_presale']) ? $this->config['goods_is_presale'] : 0;

			$goods_is_type = $this->getConfig('goods_is_type');
			$system_is_merchant = $this->getConfig('system_is_merchant');
			$goods_is_brand = $this->getConfig('goods_is_brand');
			$goods_is_member_price = $this->getConfig('goods_is_member_price');
			$goods_is_tags = $this->getConfig('goods_is_tags');
			$goods_is_attr = $this->getConfig('goods_is_attr');
			$goods_is_spec = $this->getConfig('goods_is_spec');
			$goods_is_share = $this->getConfig('goods_is_share');
			$goods_is_commission = $this->getConfig('goods_is_commission');
			$goods_is_comment = $this->getConfig('goods_is_comment');
			$goods_is_presale = $this->getConfig('goods_is_presale');

			$typeOption = $this->getFieldOption($this->type_value,'radio');
			$deliveryOption = $this->getFieldOption($this->delivery_value,'radio');
			$logisticsOption = $this->getFieldOption($this->logistics_value,'radio');
			$statusOption = $this->getFieldOption($this->status_value,'radio');

			$service = new GoodsCateModel();
			$cate = $service->getList($request,'option');

			$service = new GoodsBrandModel();
			$brand = $service->getList($request,'option');

			$service = new GoodsTagsModel();
			$tags = $service->getList($request,'option');

			$service = new GoodsAttrsModel();
			$attrs = $service->getList($request);

			$service = new GoodsSpecsModel();
			$spec_option = $service->getList($request);

			$unit = [];
			$_class_name = $this->getClassName('Unit');
			if($_class_name){
				$service = new $_class_name();
				$unit = $service->getList($request,'option');
			}

			$action = [
				// ['type'=>'divider','label'=>'基本信息','prop'=>'divider_base']
			];
			
			if($is_merchant){
				array_push($action,$merchant_form);
			}
			array_push($action,['type'=>'radio-group','label'=>'商品类型','prop'=>'type_id','value'=>$goods_is_type ? ($id ? $data->type_id : 1) : 1,'hidden'=>$goods_is_type ? false : true,'children'=>$typeOption]);
			array_push($action,['type'=>'cascader','label'=>'商品类别','prop'=>'cate_id','value'=>$id ? $data->cate_id : [],'attrs'=>['options'=>$cate],'rules'=>['required'=>true,'message'=>'请选择商品类别']],
				['type'=>'input','label'=>'商品标题','prop'=>'goods_title','value'=>$id ? $data->goods_title : '','rules'=>['required'=>true,'message'=>'商品标题名称不能为空']],
				['type'=>'input','label'=>'商品描述','prop'=>'desc','value'=>$id ? $data->desc : '','attrs'=>['type'=>'textarea'],'rules'=>['required'=>true,'message'=>'商品描述不能为空']],
				['type'=>'input','label'=>'商品编码','prop'=>'goods_code','value'=>$id ? $data->goods_code : '','rules'=>['required'=>true,'message'=>'商品编码不能为空']],
				['type'=>'input','label'=>'商品主图','prop'=>'pic','value'=>$id ? $data->pic : '','hidden'=>true],
				['type'=>'upload','label'=>'轮播图','prop'=>'picture','value'=>$id ? $data->picture : [],'attrs'=>$this->getUploadOptions('card',5,'default','默认第一张图为商品主图,建议尺寸: 750px*750px'),'rules'=>['required'=>true,'message'=>'请上传商品轮播图']],
				['type'=>'select','label'=>'商品品牌','prop'=>'brand_id','value'=>$goods_is_brand ? ($id ? $data->brand_id : '') : 0,'hidden'=>$goods_is_brand ? false : true,'children'=>$brand,'rules'=>['required'=>$goods_is_brand ? true : false,'message'=>'请选择商品品牌']],
				['type'=>'checkbox-group','label'=>'商品标签','prop'=>'tags','value'=>$goods_is_tags ? ($id ? $data->tags : []) : [],'hidden'=>$goods_is_tags ? false : true,'children'=>$tags],
				['type'=>'select','label'=>'基本单位','prop'=>'unit_id','value'=>$id ? $data->unit_id : 1,'children'=>$unit,'rules'=>['required'=>true,'message'=>'请选择商品基本单位']]
			);
			
			array_push($action,['type'=>'divider','label'=>$goods_is_attr&&$goods_is_spec ? '属性与规格' : ($goods_is_attr ? '属性价格与库存' : ($goods_is_spec ? '商品规格' : '价格与库存')),'prop'=>'divider_price']);
			if($goods_is_attr){
				array_push($action,['type'=>'attr','label'=>'商品属性','prop'=>'attrs','value'=>$id ? $data->attrs : [],'attrs'=>['style'=>['background-color'=>'#F8F8F8']],'optionsData'=>$attrs]);
			}
			if(!$goods_is_spec){
				array_push($action,['type'=>'spec','label'=>'商品规格','prop'=>'specs','value'=>$id ? $data->specs : [],'limit'=>2,'length'=>10,'attrs'=>['style'=>['background-color'=>'#F8F8F8']],'specData'=>$data->spec_data ?? [],'specForm'=>$data->spec_form ?? $this->spec_form,'specOptions'=>$data->spec_option ?? $spec_option]);
			}else{
				foreach($this->spec_form as $item){
					if($item['viladed']){
						$prop = $item['prop'];
						$option = ['type'=>$item['type'],'label'=>$item['label'],'prop'=>$prop,'value'=>$id ? $data->$prop : $item['value'],'placeholder'=>$item['placeholder']];
						if($item['prefix'] || $item['suffix']){
							$option['slot'] = ['prefix'=>['value'=>$item['prefix']],'suffix'=>['value'=>$item['suffix']]];
						}
						if($item['required']){
							$option['rules'] = ['required'=>true,'message'=>$item['label'].'不能为空'];
						}

						array_push($action,$option);
					}
				}
			}

			
			array_push($action,['type'=>'divider','label'=>'发货与邮费','prop'=>'divider_free'],
				['type'=>'radio-group','label'=>'发货方式','prop'=>'delivery_mode','value'=>$id ? $data->delivery_mode : 1,'children'=>$deliveryOption],
				['type'=>'radio-group','label'=>'物流模式','prop'=>'logistics_type','value'=>$id ? $data->logistics_type : 1,'children'=>$logisticsOption],
				['type'=>'input','label'=>'基础邮费','prop'=>'freight_amount','value'=>$id ? $data->freight_amount : '','placeholder'=>'0.00','slot'=>['prefix'=>['value'=>'¥'],'suffix'=>['value'=>'元']]],
				['type'=>'switch','label'=>'运费按下单基数累计','prop'=>'freight_accumulate','value'=>$id ? $data->freight_accumulate : 0],
				['type'=>'form-group','label'=>'包邮选项','prop'=>'dis','value'=>'','delimiter'=>'或','children'=>[
					['type'=>'input','label'=>'满件包邮','prop'=>'free_unit','value'=>$id ? $data->free_unit : '','placeholder'=>'0','attrs'=>['style'=>['width'=>'300px']],'slot'=>['prefix'=>['value'=>'满'],'suffix'=>['value'=>'件包邮']]],
					['type'=>'input','label'=>'满额包邮','prop'=>'free_amount','value'=>$id ? $data->free_amount : '','placeholder'=>'0.00','attrs'=>['style'=>['width'=>'300px']],'slot'=>['prefix'=>['value'=>'满¥'],'suffix'=>['value'=>'元包邮']]],
				]],

				['type'=>'divider','label'=>'承诺与服务','prop'=>'divider_service'],
				['type'=>'input','label'=>'承诺发货','prop'=>'delivery_time','value'=>$id ? $data->delivery_time : 24,'slot'=>['prefix'=>['value'=>'下单后'],'suffix'=>['value'=>'小时内发货']],'rules'=>['required'=>true,'message'=>'承诺发货时间不能为空']],
				['type'=>'input','label'=>'无理由退换','prop'=>'refund_time','value'=>$id ? $data->refund_time : 7,'slot'=>['prefix'=>['value'=>'允许'],'suffix'=>['value'=>'天内无理由退换货']],'rules'=>['required'=>true,'message'=>'无理由退换时间不能为空']],

				['type'=>'divider','label'=>'积分赠送与抵扣','prop'=>'divider_score'],
				['type'=>'input','label'=>'赠送积分','prop'=>'give_score','value'=>$id ? $data->give_score : '','placeholder'=>'0','slot'=>['prefix'=>['value'=>'按下单基数累计赠送'],'suffix'=>['value'=>'积分']]],
				['type'=>'input','label'=>'最高抵扣积分','prop'=>'deduct_score','value'=>$id ? $data->deduct_score : '','placeholder'=>'0','slot'=>['prefix'=>['value'=>'最高允许抵扣'],'suffix'=>['value'=>'积分']]]
			);
			if($goods_is_share || $goods_is_commission){
				array_push($action,['type'=>'divider','label'=>$goods_is_share&&$goods_is_commission ? '分享与分佣' : ($goods_is_share ? '分享设置' : '分佣设置')]);
				if($goods_is_share){
					array_push($action,['type'=>'switch','label'=>'开启分享','prop'=>'is_share','value'=>$id ? $data->is_share : 0],
						['type'=>'input','label'=>'分享标题','prop'=>'share_title','value'=>$id ? $data->share_title : ''],
						['type'=>'upload','label'=>'分享图片','prop'=>'share_pic','value'=>$id ? $data->share_pic : '','attrs'=>$this->getUploadOptions('img',1,'small')]
					);
				}
				if($goods_is_commission){
					array_push($action,['type'=>'switch','label'=>'开启分佣','prop'=>'is_commission','value'=>$id ? $data->is_commission : 0],
						['type'=>'form-commission','label'=>'分佣值','prop'=>'commission_value','value'=>$id ? $data->commission_value : [['value'=>'','type'=>0]]]
					);
				}
			}
			array_push($action,['type'=>'divider','label'=>'状态与详情'],
				['type'=>'switch','label'=>'开启评论','prop'=>'is_comment','value'=>$goods_is_comment ? ($id ? $data->is_comment : 0) : 0,'hidden'=>$goods_is_comment ? false : true],
				['type'=>'input','label'=>'预销售','prop'=>'presale','value'=>$goods_is_presale ? ($id ? $data->presale : '') : 0,'placeholder'=>'0','hidden'=>$goods_is_presale ? false : true,'slot'=>['suffix'=>['value'=>'件']]],
				['type'=>'radio-group','label'=>'状态','prop'=>'status','value'=>$id ? $data->status : 0,'children'=>$statusOption],
				['type'=>'editor','label'=>'商品详情','prop'=>'content','value'=>$id ? $data->content : '','attrs'=>[
					'type'=>'wang','image'=>['server'=>$this->host['api'].'upload/editor'],'video'=>['server'=>$this->host['api'].'upload/video']
				]]
			);


			return $action;
		}

		protected function getMapList(Request $request): array
		{
			// $goods_is_type = isset($this->config['goods_is_type']) ? $this->config['goods_is_type'] : 0;
			// $system_is_merchant = isset($this->config['system_is_merchant']) ? $this->config['system_is_merchant'] : 0;
			// $goods_is_brand = isset($this->config['goods_is_brand']) ? $this->config['goods_is_brand'] : 0;
			// $goods_is_member_price = isset($this->config['goods_is_member_price']) ? $this->config['goods_is_member_price'] : 0;
			// $goods_is_tags = isset($this->config['goods_is_tags']) ? $this->config['goods_is_tags'] : 0;
			// $goods_is_attr = isset($this->config['goods_is_attr']) ? $this->config['goods_is_attr'] : 0;
			// $goods_is_spec = isset($this->config['goods_is_spec']) ? $this->config['goods_is_spec'] : 0;
			// $goods_is_share = isset($this->config['goods_is_share']) ? $this->config['goods_is_share'] : 0;
			// $goods_is_commission = isset($this->config['goods_is_commission']) ? $this->config['goods_is_commission'] : 0;
			// $goods_is_comment = isset($this->config['goods_is_comment']) ? $this->config['goods_is_comment'] : 0;
			// $goods_is_presale = isset($this->config['goods_is_presale']) ? $this->config['goods_is_presale'] : 0;

			$goods_is_type = $this->getConfig('goods_is_type');
			$system_is_merchant = $this->getConfig('system_is_merchant');
			$goods_is_brand = $this->getConfig('goods_is_brand');
			$goods_is_member_price = $this->getConfig('goods_is_member_price');
			$goods_is_tags = $this->getConfig('goods_is_tags');
			$goods_is_attr = $this->getConfig('goods_is_attr');
			$goods_is_spec = $this->getConfig('goods_is_spec');
			$goods_is_share = $this->getConfig('goods_is_share');
			$goods_is_commission = $this->getConfig('goods_is_commission');
			$goods_is_comment = $this->getConfig('goods_is_comment');
			$goods_is_presale = $this->getConfig('goods_is_presale');

			$align = $this->align;
			
			$map = [
				['type'=>'id','label'=>'ID','prop'=>'id','align'=>$align],
				['type'=>'varchar','label'=>'商品名称','prop'=>'goods_title','align'=>$align,'width'=>180],
				['type'=>'varchar','label'=>'商品类别','prop'=>'cate_title','align'=>$align,'width'=>150],
				['type'=>'varchar','label'=>'商品编码','prop'=>'goods_code','align'=>$align,'width'=>100],
				['type'=>'img','label'=>'主图','prop'=>'pic','align'=>$align],
				['type'=>'varchar','label'=>'赠送积分','prop'=>'give_score','align'=>$align,'width'=>100],
				['type'=>'switch','label'=>'分享','prop'=>'is_share','align'=>$align],
				['type'=>'switch','label'=>'分佣','prop'=>'is_commission','align'=>$align],
				['type'=>'switch','label'=>'评论','prop'=>'is_comment','align'=>$align],
				['type'=>'varchar','label'=>'点击','prop'=>'hits','align'=>$align],
				['type'=>'varchar','label'=>'收藏','prop'=>'collects','align'=>$align],
				['type'=>'varchar','label'=>'销量','prop'=>'sales','align'=>$align],
				['type'=>'switch','label'=>'置顶','prop'=>'is_top','align'=>$align],
				['type'=>'varchar','label'=>'排序','prop'=>'sort','align'=>$align],
				['type'=>'varchar','label'=>'状态','prop'=>'status_title','align'=>$align],
			];

			return $map;
		}
	}
?>