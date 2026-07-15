<?php
	namespace dackou\model\Goods;

	use support\Request;
	use support\Db;

	class GoodsModel extends \dackou\Model{
		protected $table = 'Goods';
		protected $title = '商品';
		protected $action = 'action';
		protected $type_value = [1=>'实体商品',2=>'电子卡密',3=>'酒店',4=>'景区',5=>'民宿'];
		protected $delivery_value = [1=>'统一发货',2=>'分拆发货'];
		protected $logistics_value = [1=>'物流快递',2=>'同城配送',3=>'虚拟发货',4=>'用户自提'];
		protected $status_value = [-1=>'仓库',1=>'上架',0=>'下架'];
		protected $is_type = false;						// 开启商品类型
		protected $is_merchant = false;					// 开启多商户
		protected $is_brand = false;					// 开启品牌
		protected $is_member_price = false;				// 开启会员价
		protected $is_tags = false;						// 开启商品标签
		protected $is_attr = false;						// 开启商品属性
		protected $is_spec = true;						// 开启商品多规格
		protected $is_share = false;					// 开启商品分享
		protected $is_commission = false;				// 开启商品分佣
		protected $is_comment = false;					// 开启商品评价
		protected $is_presale = false;					// 开启商品预售
		protected $formOptions = [
			// ['type'=>'upload','label'=>'图片','prop'=>'pic','field'=>"'pic',Pic",'value'=>'','placeholder'=>'','prefix'=>'','suffix'=>'','viladed'=>false,'required'=>true],
			['type'=>'input','label'=>'价格','prop'=>'price','field'=>"'price',Price/100",'value'=>'','placeholder'=>'0.00','prefix'=>'¥','suffix'=>'元','viladed'=>true,'is_batch'=>1,'required'=>true],
            ['type'=>'input','label'=>'原价','prop'=>'origin_price','field'=>"'origin_price',OriginPrice/100",'value'=>'','placeholder'=>'0.00','prefix'=>'¥','suffix'=>'元','viladed'=>true,'is_batch'=>1,'required'=>true],
            ['type'=>'input','label'=>'成本','prop'=>'cost_price','field'=>"'cost_price',CostPrice/100",'value'=>'','placeholder'=>'0.00','prefix'=>'¥','suffix'=>'元','viladed'=>true,'is_batch'=>1,'required'=>true],
            ['type'=>'input','label'=>'基数','prop'=>'base_number','field'=>"'base_number',BaseNumber",'value'=>1,'placeholder'=>'1','prefix'=>'下单以','suffix'=>'件的倍数递增','viladed'=>true,'is_batch'=>1,'required'=>true],
            ['type'=>'input','label'=>'限购','prop'=>'limit_count','field'=>"'limit_count',LimitCount",'value'=>'','placeholder'=>'0','prefix'=>'商品限购','suffix'=>'最小基数','viladed'=>true,'is_batch'=>1,'required'=>true],
            ['type'=>'input','label'=>'限单','prop'=>'limit_order','field'=>"'limit_order',LimitOrder",'value'=>'','placeholder'=>'0','prefix'=>'订单限购','suffix'=>'次','viladed'=>true,'is_batch'=>1,'required'=>true],
            // ['type'=>'input','label'=>'单位','prop'=>'unit','field'=>"'unit',Unit",'value'=>'','placeholder'=>'','prefix'=>'','suffix'=>'','viladed'=>true,'is_batch'=>1,'required'=>true],
            ['type'=>'input','label'=>'SKU','prop'=>'sku','field'=>"'sku',Sku",'value'=>1,'placeholder'=>'1','prefix'=>'占','suffix'=>'件库存','viladed'=>true,'is_batch'=>1,'required'=>true],
            ['type'=>'input','label'=>'库存','prop'=>'stock','field'=>"'stock',Stock",'value'=>0,'placeholder'=>'0','prefix'=>'','suffix'=>'件','viladed'=>true,'is_batch'=>1,'required'=>true],
            ['type'=>'input','label'=>'规格编码','prop'=>'spec_code','field'=>"'spec_code',SpecCode",'value'=>'','placeholder'=>'','prefix'=>'','suffix'=>'','viladed'=>false,'is_batch'=>0,'required'=>true],
		];

		protected function _init(){
			$config = $this->getOptionData('system,goods');
			// var_dump($config);
			if(isset($config['goods_is_type']) && $config['goods_is_type']){
				$this->is_type = true;
			}
			if(isset($config['system_is_merchant']) && $config['system_is_merchant']){
				$this->is_merchant = true;
			}
			if(isset($config['goods_is_brand']) && $config['goods_is_brand']){
				$this->is_brand = true;
			}
			if(isset($config['goods_is_member_price']) && $config['goods_is_member_price']){
				$this->is_member_price = true;
			}
			if(isset($config['goods_is_tags']) && $config['goods_is_tags']){
				$this->is_tags = true;
			}
			if(isset($config['goods_is_attr']) && $config['goods_is_attr']){
				$this->is_attr = true;
			}
			if(isset($config['goods_is_spec']) && !$config['goods_is_spec']){
				// $this->is_spec = false;
			}
			if(isset($config['goods_is_share']) && $config['goods_is_share']){
				$this->is_share = true;
			}
			if(isset($config['goods_is_commission']) && $config['goods_is_commission']){
				$this->is_commission = true;
			}
			if(isset($config['goods_is_comment']) && $config['goods_is_comment']){
				$this->is_comment = true;
			}
			if(isset($config['goods_is_presale']) && $config['goods_is_presale']){
				$this->is_presale = true;
			}
		}

		protected function getFieldList(Request $request,$flag = false): array
		{
			$fields = [
				'ID' => 'id',									// id
				// 'TypeID' => 'type_id',							// 商品类型
				// 'MerchantID' => 'merchant_id',					// 商户ID
				'CateID' => 'cate_id',							// 商品类别
				'GoodsTitle' => 'goods_title',					// 商品名称
				'Keyword' => 'keyword',							// 关键词
				'Description' => 'desc',						// 商品描述
				'GoodsCode' => 'goods_code',					// 商品编码
				'Pic' => 'pic',									// 主图
				'Picture' => 'picture',							// 轮播图
				'BrandID' => 'brand_id',						// 品牌
				// 'Tags' => 'tags',								// 标签
				// 'Price' => 'price',								// 单价
				// 'OriginPrice' => 'origin_price',					// 原价
				// 'CostPrice' => 'cost_price',						// 成本价
				'CurrencyID' => 'currency_id',					// 币种
				'BaseNumber' => 'base_number',					// 商品下单基数
				'UnitID' => 'unit_id',							// 单位
				'Stock' => 'stock',								// 库存
				'AlarmStock' => 'alarm_stock',					// 报警库存
				'GoodsNumber' => 'goods_number',				// 商品货号
				'BatchCode' => 'batch_code',					// 批次号
				'RoleID' => 'role_id',						// 下单权限组
				'Barcode' => 'barcode',							// 条形码编码
				'IsInternal' => 'is_internal',					// 内部
				'InternalType' => 'internal_type',				// 内部属性
				// 'Attrs' => 'attrs',								// 属性值
				// 'Specs' => 'specs',								// 规格值
				'LimitCount' => 'limit_count',					// 商品限购
				'LimitOrder' => 'limit_order',					// 订单限购
				'LimitBalance' => 'limit_balance',				// 余额限购
				'LimitCoin' => 'limit_coin',					// 平台币限购
				'FreeUnit' => 'free_unit',						// 满件包邮
				'FreeAmount' => 'free_amount',					// 满额包邮
				'FreightAmount' => 'freight_amount',			// 基础邮费
				'FreightAccumulate' => 'freight_accumulate',	// 运费按件累计
				'DeliveryMode' => 'delivery_mode',				// 发货方式
				'LogisticsType' => 'logistics_type',			// 物流模式
				'RefundTime' => 'refund_time',					// 无理由退换
				'DeliveryTime' => 'delivery_time',				// 发货时间
				'GiveScore' => 'give_score',					// 赠送积分
				'DeductScore' => 'deduct_score',				// 最高积分抵扣
				// 'IsShare' => 'is_share',							// 开启分享
				// 'ShareTitle' => 'share_title',					// 分享标题
				// 'SharePic' => 'share_pic',						// 分享图片
				// 'IsCommission' => 'is_commission',				// 开启分佣
				// 'CommissionValue' => 'commission_value',			// 分佣参数值
				// 'IsComment' => 'is_comment',						// 开启评论
				// 'CommentNumber' => 'comment_number',				// 评论数
				'Hits' => 'hits',								// 点击量
				'Collects' => 'collects',						// 收藏次数
				// 'Presale' => 'presale',							// 预售值
				'IsPickup' => 'is_pickup',						// 开启自提
				'Sales' => 'sales',								// 总销量
				'IsTop' => 'is_top',							// 置顶
				'Sort' => 'sort',								// 排序
				'StartTime' => 'start_time',					// 预售开始时间
				'EndTime' => 'end_time',						// 预售结束时间
				// 'Content' => 'content',							// 商品详情
				'Status' => 'status',							// 商品状态
				'CreateTime' => 'create_time',					// 创建时间
				'CreateIP' => 'create_ip',						// 创建IP
				'IsDel' => 'is_del',							// 删除
			];

			if($this->is_type){
				$field['TypeID'] = 'type_id';
			}
			if($this->is_merchant){
				$field['MerchantID'] = 'merchant_id';
				$field['MerchantName'] = 'merchant_name';
			}
			if($this->is_tags){
				$field['Tags'] = 'tags';
			}
			if($this->is_attr){
				$field['Attrs'] = 'attrs';
			}
			if($this->is_share){
				$field['IsShare'] = 'is_share';
				$field['ShareTitle'] = 'share_title';
				$field['SharePic'] = 'share_pic';
			}
			if($this->is_commission){
				$field['IsCommission'] = 'is_commission';
				$field['CommissionValue'] = 'commission_value';
			}
			if($this->is_comment){
				$field['IsComment'] = 'is_comment';
				$field['CommentNumber'] = 'comment_number';
			}
			if($this->is_presale){
				$field['Presale'] = 'presale';
			}

			$field = [];
			foreach($fields as $key => $val){
				if($flag === 'key'){
					$field[$val] = $key;
				}else{
					if($flag === true){
						$key = $this->table . '.' . $key;
					}
					array_push($field,"{$key} as $val");
				}
			}
			array_push($field,'goods_cate.Title as cate_title');
			array_push($field,'super_cate.ID as super_id');
			array_push($field,'super_cate.Title as super_title');
			array_push($field,'unit.Title as unit_name');

			return $field;
		}

		protected function getPageList(Request $request){
			$field = $this->getList($request,'field',true);
			$tab = $this->tab;
			if($this->is_spec){
				$spec_field = "'id',ID,'goods_id',GoodsID,'first_id',FirstID,'first_title',FirstTitle,'second_id',SecondID,'second_title',SecondTitle,'base_number',BaseNumber";
				foreach($this->formOptions as $item){
					$spec_field .= "," . $item['field'];
				}
				$spec_field .= ",'sales',Sales,'status',Status";
				$spec_table = $this->prefix . 'goods_spec';
				array_push($field,Db::raw("(SELECT JSON_ARRAYAGG(JSON_OBJECT($spec_field)) FROM {$spec_table} WHERE GoodsID = {$tab}.ID AND {$spec_table}.Status = 1) as specs"));
				array_push($field,Db::raw("(SELECT COUNT(ID) FROM {$spec_table} WHERE GoodsID = {$tab}.ID AND {$spec_table}.Status = 1) as spec_number"));
			}
    		$where = $this->getWhere($request);

    		$keyword = trim($request->input('keyword',''));
    		if($keyword){
    			array_push($where,[$this->table.'.GoodsTitle','like','%'.$keyword.'%']);
    		}

    		if($this->is_merchant){
    			$merchant_id = $request->input('merchant_id',0);
    			if($merchant_id){
    				array_push($where,[$this->table.'.MerchantID','=',$merchant_id]);
    			}
    		}

    		$status = $request->input('status','');
			if(is_numeric($status)){
				array_push($where,[$this->table.'.Status','=',$status]);
			}

    		$rows = Db::table($this->table)
						->join('goods_cate',$this->table.'.CateID','=','goods_cate.ID')
						->join('goods_cate as super_cate','goods_cate.PID','=','super_cate.ID')
						->join('unit',$this->table.'.UnitID','=','unit.ID');
			if($this->is_merchant){
				$rows = $rows->join('merchant',$this->table.'.MerchantID','=','merchant.ID');
			}
			$rows = $rows->where($where)
						->count();

			[$offset,$limit] = $this->getLimit($request);
			$object = Db::table($this->table)
						->join('goods_cate',$this->table.'.CateID','=','goods_cate.ID')
						->join('goods_cate as super_cate','goods_cate.PID','=','super_cate.ID')
						->join('unit',$this->table.'.UnitID','=','unit.ID');
			if($this->is_merchant){
				$object = $object->join('merchant',$this->table.'.MerchantID','=','merchant.ID');
			}
				
			$object = $object->select(...$field)
						->where($where)
						->offset($offset)
						->limit($limit)
						->get();
			if($object){
				foreach($object as $k => $v){
					$object[$k]->create_time = $this->getDateTime($object[$k]->create_time);

					if(property_exists($object[$k], 'picture') && $object[$k]->picture){
	    				$object[$k]->picture = $this->getDecodeData($object[$k]->picture);
	    			}
					if(property_exists($object[$k], 'tags') && $object[$k]->tags){
	    				$object[$k]->tags = $this->getDecodeData($object[$k]->tags);
	    			}
					if(property_exists($object[$k], 'commission_value') && $object[$k]->commission_value){
	    				$object[$k]->commission_value = $this->getDecodeData($object[$k]->commission_value);
	    			}
	    			if(property_exists($object[$k], 'specs') && $object[$k]->specs){
	    				$object[$k]->specs = $this->getDecodeData($object[$k]->specs);
	    			}
	    			if(property_exists($object[$k], 'user') && $object[$k]->user){
	    				$object[$k]->user = $this->getDecodeData($object[$k]->user);
	    			}
				}
			}
			return ['rows' => $rows,'data' => $object];
		}

		protected function getListById(Request $request,$id = 0){
			if(!$id || !is_numeric($id) || $id < 0){
				return 100007;
			}
			
			$field = $this->getList($request,'field',true);
			array_push($field,$this->table.'.Picture as picture');
			array_push($field,$this->table.'.Content as content');

			$tab = $this->tab;
			if($this->is_spec){
				$spec_field = "'id',ID,'goods_id',GoodsID,'first_id',FirstID,'first_title',FirstTitle,'second_id',SecondID,'second_title',SecondTitle,'base_number',BaseNumber";
				foreach($this->formOptions as $item){
					$spec_field .= "," . $item['field'];
				}
				$spec_field .= ",'sales',Sales,'status',Status";
				$spec_table = $this->prefix . 'goods_spec';
				array_push($field,Db::raw("(SELECT JSON_ARRAYAGG(JSON_OBJECT($spec_field)) FROM {$spec_table} WHERE GoodsID = {$tab}.ID AND {$spec_table}.Status = 1) as specs"));
			}

			$user_id = $request->input('user_id',0);
			// if($user_id){
			// 	$order_table = $this->prefix . 'order';
			// 	array_push($field,Db::raw("(SELECT JSON_ARRAYAGG(JSON_OBJECT('order_amount',SUM(TotalAmount),'order_number',COUNT(ID))) FROM {$order_table} WHERE GoodsID = {$tab}.ID AND {$order_table}.Status IN (2,3)) as user"));
			// }
    		$where = $this->getWhere($request);
    		array_push($where,[$this->table.'.ID','=',$id]);

    		$object = Db::table($this->table)
						->join('goods_cate',$this->table.'.CateID','=','goods_cate.ID')
						->join('goods_cate as super_cate','goods_cate.PID','=','super_cate.ID')
						->join('unit',$this->table.'.UnitID','=','unit.ID');
			if($this->is_merchant){
				$object = $object->join('merchant',$this->table.'.MerchantID','=','merchant.ID');
			}
    		$object = $object->select(...$field)
    					->where($where)
    					->first();
    		if($object){
    			if(property_exists($object, 'picture') && $object->picture){
    				$object->picture = $this->getDecodeData($object->picture);
    			}
    			if(property_exists($object, 'tags') && $object->tags){
    				$object->tags = $this->getDecodeData($object->tags,false);
    			}
    			if(property_exists($object, 'commission_value') && $object->commission_value){
    				$object->commission_value = $this->getDecodeData($object->commission_value);
    			}
    			if(property_exists($object, 'specs') && $object->specs){
    				$object->specs = $this->getDecodeData($object->specs,false);
    			}
    			if(property_exists($object, 'user') && $object->user){
    				$object->user = $this->getDecodeData($object->user,false);
    			}
    		}
    		// var_dump($object);
    		return $object;
		}

		protected function getOrderList(Request $request,$goods = []){
			if(!$goods){
				return [];
			}

			$user_id = $request->post('user_id',0);
			if(!$user_id || !is_numeric($user_id) || $user_id < 0){
				return 100007;
			}

			$_class_name = $this->getClassName('User');
			$service = new $_class_name();
			$user = $service->getList($request,$user_id);
			if(!$user || !is_object($user)){
				return $user;
			}
			
			$goodsIds = array_column($goods, 'goods_id');
			$goodsIds = array_unique($goodsIds);
			
			$lists = [];
			foreach($goodsIds as $goods_id){
				$child['goods_id'] = $goods_id;
				$child['spec'] = collect($goods)->where('goods_id', $goods_id);
				array_push($lists,$child);
			}
			
			$data = [];
			$_class_name = $this->getClassName('GoodsSpec');
			foreach($lists as $list){
				$service = new $_class_name();
				$object = $service->getList($request,'multiple',$list,$user);
				array_push($data,$object);
			}
			var_dump($data);
			return $data;

			/*
    		$specIds = array_column($goods, 'spec_id');
    		// var_dump('goodsids: ',$goodsIds);
    		// var_dump('specIds: ',$specIds);
			$field = [
				$this->table.'.ID as id',
				'GoodsTitle as goods_title',
				$this->table.'.Pic as goods_pic',
				'super_cate.ID as super_id',
				'super_cate.Title as supre_title',
				$this->table.'.CateID as cate_id',
				'goods_cate.Title as cate_title'
			];
			$where = $this->getWhere($request);

			$ids = implode(',', $goodsIds);
			$object = Db::table($this->table)
						->join('goods_cate',$this->table.'.CateID','=','goods_cate.ID')
						->join('goods_cate as super_cate','goods_cate.PID','=','super_cate.ID')
						->select(...$field)
						->where($where)
						->whereIn($this->table.'.ID',$goodsIds)
						// ->orderByRaw("FIELD(".$this->tab.".ID, {$ids})")
						->get();
			// var_dump('count obj: '.count($object));
			if($object){
				$field = [
					'ID as id',
					'GoodsID as goods_id',
					'FirstID as first_id',
					'FirstTitle as first_title',
					'SecondID as second_id',
					'SecondTitle as second_title',
					'BaseNumber as base_number',
					'Price as price',
					'OriginPrice as origin_price',
					'CostPrice as cost_price',
					'Sales as sales',
					'Status as status'
				];
				$obj = Db::table('goods_spec')
							->select(...$field)
							->whereIn('ID',$specIds)
							->get();
				if($obj){
					foreach($obj as $k => $v){
						$obj[$k]->price /= 100;
						$obj[$k]->origin_price /= 100;
						$obj[$k]->cost_price /= 100;
					}
					foreach($object as $k => $v){
						$quantity = isset($goods[$k]['quantity']) ? (int)$goods[$k]['quantity'] : 1;
						$spec = collect($obj)->firstWhere('goods_id', $v->id);
						$object[$k]->first_id = $spec->first_id;
						$object[$k]->first_title = $spec->first_title;
						$object[$k]->second_id = $spec->second_id;
						$object[$k]->second_title = $spec->second_title;
						$object[$k]->base_number = $spec->base_number;
						$object[$k]->quantity = $quantity;
						$object[$k]->price = $spec->price;
						$object[$k]->origin_price = $spec->origin_price;
						$object[$k]->cost_price = $spec->cost_price;
						$object[$k]->sales = $spec->sales;
						$object[$k]->amount = ($quantity * $spec->price*100) / 100;
						$object[$k]->freight = 0;
						$object[$k]->score = 0;
					}
				}

				$cateIds = [];

				// var_dump('count: '.count($object));
				foreach($object as $obj){
					array_push($cateIds,$obj->cate_id);
				}
				$result = [];
				foreach($cateIds as $cateid){
					$collect = [];
					foreach($object as $obj){
						var_dump('cate_id: '.$obj->cate_id);
						if($obj->cate_id === $cateid){
							var_dump('cate_id: '.$cateid.' goods: '.$obj->goods_title);
							array_push($collect,$obj);
						}
					}
					array_push($result,$collect);
				}

				// var_dump('result: ',$result);
				return $result;
			}
			return $object;
			*/
		}

		protected function getTopList(Request $request,$num = 1){
			$field = $this->getList($request,'field',true);
			$tab = $this->tab;
			if($this->is_spec){
				$spec_field = "'id',ID,'goods_id',GoodsID,'first_id',FirstID,'first_title',FirstTitle,'second_id',SecondID,'second_title',SecondTitle,'base_number',BaseNumber";
				foreach($this->formOptions as $item){
					$spec_field .= "," . $item['field'];
				}
				$spec_field .= ",'sales',Sales,'status',Status";
				$spec_table = $this->prefix . 'goods_spec';
				array_push($field,Db::raw("(SELECT JSON_ARRAYAGG(JSON_OBJECT($spec_field)) FROM {$spec_table} WHERE GoodsID = {$tab}.ID AND {$spec_table}.Status = 1) as specs"));
			}
    		$where = $this->getWhere($request);

    		$object = Db::table($this->table)
						->join('goods_cate',$this->table.'.CateID','=','goods_cate.ID')
						->join('goods_cate as super_cate','goods_cate.PID','=','super_cate.ID')
						->join('unit',$this->table.'.UnitID','=','unit.ID');
			if($this->is_merchant){
				$object = $object->join('merchant',$this->table.'.MerchantID','=','merchant.ID');
			}
				
			$object = $object->select(...$field)
						->where($where)
						->orderBy($this->table.'.Sales','desc')
						->limit($num)
						->get();
			if($object){
				foreach($object as $k => $v){
					$object[$k]->create_time = $this->getDateTime($object[$k]->create_time);

					if(property_exists($object[$k], 'picture') && $object[$k]->picture){
	    				$object[$k]->picture = $this->getDecodeData($object[$k]->picture);
	    			}
					if(property_exists($object[$k], 'tags') && $object[$k]->tags){
	    				$object[$k]->tags = $this->getDecodeData($object[$k]->tags);
	    			}
					if(property_exists($object[$k], 'commission_value') && $object[$k]->commission_value){
	    				$object[$k]->commission_value = $this->getDecodeData($object[$k]->commission_value);
	    			}
	    			if(property_exists($object[$k], 'specs') && $object[$k]->specs){
	    				$object[$k]->specs = $this->getDecodeData($object[$k]->specs);
	    			}
	    			if(property_exists($object[$k], 'user') && $object[$k]->user){
	    				$object[$k]->user = $this->getDecodeData($object[$k]->user);
	    			}
				}
			}
			return $object;
		}

		protected function setExcute(Request $request,$data,$action = ''){
			switch($action){
				case '':
				case 'add':
				case 'mod':
					$service = new GoodsSpecModel();
					return $service->setData($request,$data,$action);
				case 'del':
					$service = new GoodsSpecModel();
					return $service->setDel($request,$data);
				case 'delete':
					$service = new GoodsSpecModel();
					return $service->setDelete($request,$data);
				default:
					return true;
			}
		}

		protected function validate(Request $request,$id = 0,$obj = null){
			$type_id = $this->is_type ? $request->post('type_id',0) : 0;
			$cate_id = $request->post('cate_id',[]);
			$goods_title = trim($request->post('goods_title',''));
			$desc = trim($request->post('desc',''));
			$goods_code = trim($request->post('goods_code',''));
			$picture = $request->post('picture',[]);
			$brand_id = $request->post('brand_id',0);
			$tags = $request->post('tags',[]);
			$unit_id = $request->post('unit_id',0);
			$attrs = $this->is_attr ? $request->post('attrs',[]) : [];
			$specs = $this->is_spec ? $request->post('specs',[]) : [];
			$delivery_mode = $request->post('delivery_mode',1);
			$logistics_type = $request->post('logistics_type',1);
			$freight_amount = $request->post('freight_amount',0);
			$freight_accumulate = $request->post('freight_accumulate',0);
			$dis = $request->post('dis',[]);
			if(!$dis || !isset($dis['free_unit']) || !isset($dis['free_amount'])){
				return '包邮选项填写有误';
			}
			if($dis['free_unit'] == '' || $dis['free_unit'] == '0' || $dis['free_unit'] == '0.0' || $dis['free_unit'] == '0.00'){
				$dis['free_unit'] == 0;
			}
			if(!is_numeric($dis['free_unit']) || $dis['free_unit'] < 0){
				return '满件包邮填写有误';
			}
			if($dis['free_amount'] == '' || $dis['free_amount'] == '0' || $dis['free_amount'] == '0.0' || $dis['free_amount'] == '0.00'){
				$dis['free_amount'] == 0;
			}
			if(!is_numeric($dis['free_amount']) || $dis['free_amount'] < 0){
				return '满额包邮填写有误';
			}


			$delivery_time = $request->post('delivery_time',48);
			$refund_time = $request->post('refund_time',7);
			$give_score = $request->post('give_score','');
			$deduct_score = $request->post('deduct_score','');

			if($this->is_share){
				$is_share = $request->post('is_share',0);
				$share_title = trim($request->post('share_title',''));
				$share_pic = trim($request->post('share_pic',''));

				$data['is_share'] = $is_share;
				$data['share_title'] = $share_title;
				$data['share_pic'] = $share_pic;
			}
			if($this->is_commission){
				$is_commission = $request->post('is_commission',0);
				$commission_value = $request->post('commission_value',[]);

				$data['is_commission'] = $is_commission;
				$data['commission_value'] = $commission_value;
			}
			if($this->is_comment){
				$is_comment = $request->post('is_comment',0);

				$data['is_comment'] = $is_comment;
			}
			if($this->is_presale){
				$presale = $request->post('presale',0);

				$data['presale'] = $presale;
			}
			$status = $request->post('status',0);
			$content = trim($request->post('content',''));

			$data['type_id'] = $type_id;
			$data['cate_id'] = is_array($cate_id) ? end($cate_id) : $cate_id;
			$data['goods_title'] = $goods_title;
			$data['desc'] = $desc;
			$data['goods_code'] = $goods_code;
			$data['pic'] = $picture[0];
			$data['picture'] = $picture;
			$data['brand_id'] = $brand_id;
			$data['tags'] = $tags;
			$data['unit_id'] = $unit_id;
			$data['attrs'] = $attrs;
			$data['specs'] = $specs;
			$data['delivery_mode'] = $delivery_mode;
			$data['logistics_type'] = $logistics_type;
			$data['freight_amount'] = $freight_amount;
			$data['freight_accumulate'] = $freight_accumulate;
			$data['free_unit'] = $dis['free_unit'];
			$data['free_amount'] = $dis['free_amount'];
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
			}

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

			$_class_name = $this->getClassName('Role');
			$service = new $_class_name();
			$role = $service->getList($request,'option',[17]);

			$unit = [];
			$_class_name = $this->getClassName('Unit');
			if($_class_name){
				$service = new $_class_name();
				$unit = $service->getList($request,'option');
			}

			$action = [];
			if($this->is_type){
				array_push($action,['type'=>'radio-group','label'=>'商品类型','prop'=>'type_id','value'=>$data->type_id ?? 1,'children'=>$typeOption]);
			}
			array_push($action,['type'=>'cascader','label'=>'商品类别','prop'=>'cate_id','value'=>$id ? [$data->super_id,$data->cate_id] : [],'children'=>$cate,'attrs'=>['options'=>$cate],'rules'=>['required'=>true,'message'=>'请选择商品类别']],
				['type'=>'input','label'=>'商品标题','prop'=>'goods_title','value'=>$id ? $data->goods_title : '','rules'=>['required'=>true,'message'=>'商品标题名称不能为空']],
				['type'=>'input','label'=>'商品描述','prop'=>'desc','value'=>$id ? $data->desc : '','attrs'=>['type'=>'textarea'],'rules'=>['required'=>true,'message'=>'商品描述不能为空']],
				['type'=>'input','label'=>'商品编码','prop'=>'goods_code','value'=>$id ? $data->goods_code : '','rules'=>['required'=>true,'message'=>'商品编码不能为空']],
				// ['type'=>'input','label'=>'商品主图','prop'=>'pic','value'=>$id ? $data->pic : '','hidden'=>true],
				['type'=>'upload','label'=>'轮播图','prop'=>'picture','value'=>$id ? $data->picture : [],'attrs'=>$this->getUploadOptions('card',5,'default','默认第一张图为商品主图,建议尺寸: 750px*750px'),'rules'=>['required'=>true,'message'=>'请上传商品轮播图']],
				['type'=>'select','label'=>'商品品牌','prop'=>'brand_id','value'=>$this->is_brand ? ($id ? $data->brand_id : '') : 0,'hidden'=>$this->is_brand ? false : true,'children'=>$brand,'rules'=>['required'=>$this->is_brand ? true : false,'message'=>'请选择商品品牌']],
				['type'=>'checkbox-group','label'=>'商品标签','prop'=>'tags','value'=>$this->is_tags ? ($id ? $data->tags : []) : [],'hidden'=>$this->is_tags ? false : true,'children'=>$tags],
				['type'=>'select','label'=>'基本单位','prop'=>'unit_id','value'=>$id ? $data->unit_id : 1,'children'=>$unit,'rules'=>['required'=>true,'message'=>'请选择商品基本单位']],
				['type'=>'select','label'=>'会员商品','prop'=>'role_id','value'=>$id ? $data->role_id : [],'children'=>$role,'attrs'=>['multiple'=>true]]
			);
			if($this->is_attr){
				array_push($action,['type'=>'attr','label'=>'商品属性','prop'=>'attrs','value'=>$id ? $data->attrs : [],'attrs'=>['style'=>['background-color'=>'#F8F8F8']]]);
			}
			if($this->is_spec){
				array_push($action,['type'=>'spec','label'=>'商品规格','prop'=>'specs','value'=>$id ? $data->specs : [],'attrs'=>['style'=>['background-color'=>'#F8F8F8'],'formOptions'=>$this->formOptions]]);
			}

			array_push($action,
				['type'=>'radio-group','label'=>'发货方式','prop'=>'delivery_mode','value'=>$id ? $data->delivery_mode : 1,'children'=>$deliveryOption],
				['type'=>'radio-group','label'=>'物流模式','prop'=>'logistics_type','value'=>$id ? $data->logistics_type : 1,'children'=>$logisticsOption],
				['type'=>'input','label'=>'基础邮费','prop'=>'freight_amount','value'=>$id ? $data->freight_amount : '','placeholder'=>'0.00','prefix'=>'¥','suffix'=>'元'],
				['type'=>'switch','label'=>'运费按下单基数累计','prop'=>'freight_accumulate','value'=>$id ? $data->freight_accumulate : 0],
				['type'=>'group','label'=>'包邮选项','prop'=>'dis','value'=>['free_unit'=>$data->free_unit ?? '','free_amount'=>$data->free_amount ?? ''],'delimiter'=>'或','children'=>[
					['type'=>'input','label'=>'满件包邮','prop'=>'free_unit','value'=>$id ? $data->free_unit : '','placeholder'=>'0','prefix'=>'满','suffix'=>'件包邮','width'=>300],
					['type'=>'input','label'=>'满额包邮','prop'=>'free_amount','value'=>$id ? $data->free_amount : '','placeholder'=>'0.00','prefix'=>'满¥','suffix'=>'元包邮','width'=>300],
				]],

				// ['type'=>'divider','label'=>'承诺与服务','prop'=>'divider_service'],
				['type'=>'input','label'=>'承诺发货','prop'=>'delivery_time','value'=>$id ? $data->delivery_time : 24,'prefix'=>'下单后','suffix'=>'小时内发货','rules'=>['required'=>true,'message'=>'承诺发货时间不能为空']],
				['type'=>'input','label'=>'无理由退换','prop'=>'refund_time','value'=>$id ? $data->refund_time : 7,'prefix'=>'允许','suffix'=>'天内无理由退换货','rules'=>['required'=>true,'message'=>'无理由退换时间不能为空']],

				// ['type'=>'divider','label'=>'积分赠送与抵扣','prop'=>'divider_score'],
				['type'=>'input','label'=>'赠送积分','prop'=>'give_score','value'=>$id ? $data->give_score : '','placeholder'=>'0','prefix'=>'按下单基数累计赠送','suffix'=>'积分'],
				['type'=>'input','label'=>'最高抵扣积分','prop'=>'deduct_score','value'=>$id ? $data->deduct_score : '','placeholder'=>'0','prefix'=>'最高允许抵扣','suffix'=>'积分']
			);

			if($this->is_share){
				array_push($action,['type'=>'switch','label'=>'开启分享','prop'=>'is_share','value'=>$id ? $data->is_share : 0],
					['type'=>'input','label'=>'分享标题','prop'=>'share_title','value'=>$id ? $data->share_title : ''],
					['type'=>'upload','label'=>'分享图片','prop'=>'share_pic','value'=>$id ? $data->share_pic : '','attrs'=>$this->getUploadOptions('img',1,'small')]
				);
			}
			if($this->is_commission){
				array_push($action,['type'=>'switch','label'=>'开启分佣','prop'=>'is_commission','value'=>$id ? $data->is_commission : 0],
					['type'=>'form-commission','label'=>'分佣值','prop'=>'commission_value','value'=>$id ? $data->commission_value : [['value'=>'','type'=>0]]]
				);
			}
			array_push($action,
				['type'=>'switch','label'=>'开启评论','prop'=>'is_comment','value'=>$this->is_comment ? ($id ? $data->is_comment : 0) : 0,'hidden'=>$this->is_comment ? false : true],
				['type'=>'input','label'=>'预销售','prop'=>'presale','value'=>$this->is_presale ? ($id ? $data->presale : '') : 0,'placeholder'=>'0','hidden'=>$this->is_presale ? false : true,'suffix'=>'件'],
				['type'=>'radio-group','label'=>'状态','prop'=>'status','value'=>$id ? $data->status : 0,'children'=>$statusOption],
				['type'=>'editor','label'=>'商品详情','prop'=>'content','value'=>$id ? $data->content : '','attrs'=>$this->getEditorOptions('wang')]
			);

			return $action;
		}

		protected function getMapList(Request $request): array
		{
			return [
				['type'=>'id','label'=>'ID','prop'=>'id'],
				['type'=>'varchar','label'=>'商品名称','prop'=>'goods_title','width'=>180],
				['type'=>'varchar','label'=>'商品类别','prop'=>'cate_title','width'=>150],
				['type'=>'varchar','label'=>'商品编码','prop'=>'goods_code','width'=>100],
				['type'=>'img','label'=>'主图','prop'=>'pic'],
				['type'=>'varchar','label'=>'规格','prop'=>'spec_number'],
				['type'=>'varchar','label'=>'赠送积分','prop'=>'give_score','width'=>100],
				['type'=>'switch','label'=>'分享','prop'=>'is_share'],
				['type'=>'switch','label'=>'分佣','prop'=>'is_commission'],
				['type'=>'switch','label'=>'评论','prop'=>'is_comment'],
				['type'=>'varchar','label'=>'点击','prop'=>'hits'],
				['type'=>'varchar','label'=>'收藏','prop'=>'collects'],
				['type'=>'varchar','label'=>'销量','prop'=>'sales'],
				['type'=>'switch','label'=>'置顶','prop'=>'is_top'],
				['type'=>'varchar','label'=>'排序','prop'=>'sort'],
				['type'=>'varchar','label'=>'状态','prop'=>'status_title'],
			];
		}
	}
?>