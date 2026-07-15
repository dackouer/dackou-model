<?php
	namespace dackou\model\Order;

	use support\Request;
	use support\Db;

	class OrderModel extends \dackou\Model{
		protected $table = 'Order';
		protected $title = '订单';
		protected $primaryKey = 'order_code';
		protected $option_key = 'system,order';
		protected $is_cate = true;
    	protected $action_width = 1200;
		protected $cate_value = [
			1 	=> '商城下单',
			2 	=> '自提订单',
			3 	=> '拼团',
			4 	=> '秒杀',
			5 	=> '众筹',
			9 	=> '会员升级',
			10 	=> '会员充值',
			11 	=> '会员提现',
			100 => '退款'
		];
		protected $status_value = [
			// 0=>'待确认',1=>'待支付',2=>'待发货',3=>'待收货',4=>'已完成',-1=>'已取消',-2=>'已锁定',10=>'已退款'
			['label'=>'已锁定','value'=>-2,'color'=>'','desc'=>''],
			['label'=>'已取消','value'=>-1,'color'=>'','desc'=>''],
			['label'=>'待确认','value'=>0,'color'=>'','desc'=>''],
			['label'=>'待支付','value'=>1,'color'=>'','desc'=>''],
			['label'=>'待发货','value'=>2,'color'=>'','desc'=>''],
			['label'=>'待收货','value'=>3,'color'=>'','desc'=>''],
			['label'=>'已完成','value'=>4,'color'=>'','desc'=>''],
			['label'=>'待售后','value'=>5,'color'=>'','desc'=>''],
			['label'=>'已完成','value'=>6,'color'=>'','desc'=>''],
		];
		protected $action = 'custom';
		protected $action_value = [
    		['key'=>'amend','title'=>'改价','desc'=>'修改价格'],
    		['key'=>'cancel','title'=>'取消','desc'=>'取消订单'],
    		['key'=>'confirm','title'=>'确认','desc'=>'确认订单'],
    		['key'=>'pay','title'=>'支付','desc'=>'支付订单'],
    		['key'=>'deliver','title'=>'发货','desc'=>'订单发货'],
    		['key'=>'refund','title'=>'退款','desc'=>'订单退款'],
    		['key'=>'lock','title'=>'锁定','desc'=>'锁定订单'],
    		['key'=>'unlock','title'=>'解除','desc'=>'解除锁定'],
    		['key'=>'del','title'=>'删除','desc'=>'删除订单'],
    	];
    	protected $osname_value = [
    		['label'=>'小程序','value'=>'mini'],
    		['label'=>'APP','value'=>'app'],
    		['label'=>'Web商城','value'=>'web'],
    		['label'=>'线下交易','value'=>'offline'],
    	];
    	protected $pay_type_value = [
    		['value'=>'wechat','label'=>'微信','desc'=>'微信支付','pic'=>''],
    		['value'=>'alipay','label'=>'支付宝','desc'=>'支付宝支付','pic'=>''],
    		['value'=>'bank','label'=>'银行卡','desc'=>'银行卡支付','pic'=>''],
    		['value'=>'cash','label'=>'现金','desc'=>'现金支付','pic'=>''],
    	];
    	protected $payee_value = [
    		['label'=>'微信商户号','value'=>'merchant'],
    		['label'=>'支付宝商户号','value'=>'alipay'],
    		['label'=>'线下收款','value'=>'offline'],
    	];
    	protected $delivery_value = [
    		['label'=>'无需发货','value'=>0],
    		['label'=>'物流快递','value'=>1],
    		['label'=>'同城配送','value'=>2],
    		['label'=>'用户自提','value'=>3],
    		['label'=>'虚拟发货','value'=>4],
    	];

    	/**
    	 * 获取分页订单数据
    	 * @param  Request $request [description]
    	 * @return [type]           [description]
    	 */
    	protected function getPageList(Request $request){
    		try{
    			$field = $this->getList($request,'field');
    			$where = $this->getWhere($request);

    			$keyword = trim($request->input('keyword',''));
    			$status = $request->input('status');
    			if(is_numeric($status) && in_array($status, [-2,-1,0,1,2,3,4,10])){
    				array_push($where, [$this->table.'.Status','=',$status]);
    			}
    			$itime = $request->input('itime',[]);
    			$start_time = 0;
    			$end_time = 0;
    			if($itime && is_array($itime)){
    				if(isset($itime[0])){
    					$start_time = strtotime($itime[0]);
    				}
    				if(isset($itime[1])){
    					$end_time = strtotime($itime[1]);
    				}
    			}
    			if($start_time){
    				array_push($where,[$this->table.'.CreateTime','>=',$start_time]);
    			}
    			if($end_time){
    				array_push($where,[$this->table.'.CreateTime','<=',$end_time]);
    			}

    			$user_id = $request->input('user_id',0);
    			if($user_id){
    				array_push($where,[$this->table.'.UserID','=',$user_id]);
    			}

    			$cate_id = $request->input('cate_id','');
    			if(is_numeric($cate_id) && isset($this->cate_value[$cate_id])){
    				array_push($where,[$this->table.'.OrderType','=',$cate_id]);
    			}

    			$status = $request->input('status','');
    			if(is_numeric($status) && isset($this->status_value[$status])){
    				array_push($where,[$this->table.'.Status','=',$status]);
    			}

    			$system_is_merchant = isset($this->config['system_is_merchant']) ? $this->config['system_is_merchant'] : 0;
    			if($system_is_merchant){
					$merchant_id = $request->input('merchant_id',$request->input('mch_id',0));
					if($merchant_id){
						array_push($where,[$this->table.'.MerchantID','=',$merchant_id]);
					}
				}

    			$rows = Db::table($this->table)
    						->where($where);
    			if(!empty($keyword) && $keyword){
					$rows = $rows->where(function($query) use ($keyword){
						$query->where($this->table.'.OrderCode','like','%'.$keyword.'%')
							  ->orWhere($this->table.'.UserID','like','%'.$keyword.'%')
							  ->orWhere($this->table.'.Realname','like','%'.$keyword.'%')
							  ->orWhere($this->table.'.Idcard','like','%'.$keyword.'%')
							  ->orWhere($this->table.'.Mobile','like','%'.$keyword.'%');
					});
				}
    			$rows = $rows->count();
    			[$offset,$limit] = $this->getLimit($request);
    			$object = Db::table($this->table)
    						->select(...$field)
    						->where($where);
    			if(!empty($keyword) && $keyword){
					$object = $object->where(function($query) use ($keyword){
						$query->where($this->table.'.OrderCode','like','%'.$keyword.'%')
							  ->orWhere($this->table.'.UserID','like','%'.$keyword.'%')
							  ->orWhere($this->table.'.Realname','like','%'.$keyword.'%')
							  ->orWhere($this->table.'.Idcard','like','%'.$keyword.'%')
							  ->orWhere($this->table.'.Mobile','like','%'.$keyword.'%');
					});
				}
    			$object = $object->orderBy($this->table.'.ID','desc')
    						->offset($offset)
    						->limit($limit)
    						->get();
    			if($object){
    				$object = $this->getResultData($object,true);

    				$statistical = $this->getList($request,'statistical',$keyword,$where);
    				if($statistical && is_object($statistical)){
    					$object->push($statistical);
    				}
    			}

    			return ['rows' => $rows,'data'=>$object];
    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
    	}

    	protected function getStatisticalList(Request $request,$keyword,$where = []){
    		$field = [
    			'SUM(TotalCount) as total_count',
    			'SUM(TotalAmount) as total_amount',
    			'SUM(Freight) as freight',
    			'SUM(PayableAmount) as payable_amount',
    			'SUM(PayAmount) as pay_amount',
    		];

    		$object = Db::table($this->table)
    					->selectRaw(implode(',', $field))
    					->where($where);
    		if($keyword){
    			$object = $object->where(function($query) use ($keyword){
					$query->where($this->table.'.OrderCode','like','%'.$keyword.'%')
						  ->orWhere($this->table.'.UserID','like','%'.$keyword.'%')
						  ->orWhere($this->table.'.Realname','like','%'.$keyword.'%')
						  ->orWhere($this->table.'.Idcard','like','%'.$keyword.'%')
						  ->orWhere($this->table.'.Mobile','like','%'.$keyword.'%');
				});
    		}
    		$object = $object->first();
    		if($object){
    			$object->order_type = '';
    			$object->discount = 0;
    			$object->reduce = 0;
    			$object->score_amount = 0;
    			$object->mobile = '总计';
     			$object->total_amount = $this->formatAmount($object->total_amount);
    			$object->freight = $this->formatAmount($object->freight);
    			$object->payable_amount = $this->formatAmount($object->payable_amount);
    			$object->pay_amount = $this->formatAmount($object->pay_amount);
    		}
    		return $object;
    	}

    	/**
    	 * 获取单个订单数据
    	 * @param  Request $request [description]
    	 * @param  integer $id      [description]
    	 * @return [type]           [description]
    	 */
    	protected function getListById(Request $request,$id = 0){
    		if(!$id){
    			return 100005;
    		}
    		$field = $this->getList($request,'field');
			$where = $this->getWhere($request);
			array_push($where,[$this->table.'.OrderCode','=',$id]);

			$object = Db::table($this->table)
						->select(...$field)
						->where($where)
						->first();
			
			if($object){
				$object->waitime = 0;
				if($object->expire_time){
					$object->waitime = $object->expire_time - time();
				}
				
				$object->status_color = $this->getFieldValue($object->status,$this->status_value,'color');
				$object->status_title = $this->getFieldValue($object->status,$this->status_value);
				$object->goods = $this->getDecodeData($object->goods);
				$object = $this->getResultData($object);
			}
			// var_dump($object);
			return $object;
    	}

    	/**
    	 * 获取用户订单列表
    	 * @param  Request $request [description]
    	 * @param  integer $user_id [description]
    	 * @return [type]           [description]
    	 */
    	protected function getUserList(Request $request,$user_id = 0){
    		if(!$user_id){
    			return 100007;
    		}

    		try{
    			$field = $this->getList($request,'field');
    			$where = $this->getWhere($request);
    			array_push($where,[$this->table.'.UserID','=',$user_id]);

    			$keyword = trim($request->input('keyword',''));
    			$status = $request->input('status');
    			if(is_numeric($status) && in_array($status, [-2,-1,0,1,2,3,4,10])){
    				array_push($where, [$this->table.'.Status','=',$status]);
    			}
    			$itime = $request->input('itime',[]);
    			$start_time = 0;
    			$end_time = 0;
    			if($itime && is_array($itime)){
    				if(isset($itime[0])){
    					$start_time = strtotime($itime[0]);
    				}
    				if(isset($itime[1])){
    					$end_time = strtotime($itime[1]);
    				}
    			}
    			if($start_time){
    				array_push($where,[$this->table.'.CreateTime','>=',$start_time]);
    			}
    			if($end_time){
    				array_push($where,[$this->table.'.CreateTime','<=',$end_time]);
    			}

    			$rows = Db::table($this->table)
    						->where($where);
    			if(!empty($keyword) && $keyword){
					$rows = $rows->where(function($query) use ($keyword){
						$query->where($this->table.'.OrderCode','like','%'.$keyword.'%');
					});
				}
    			$rows = $rows->count();
    			[$offset,$limit] = $this->getLimit($request);
    			$object = Db::table($this->table)
    						->select(...$field)
    						->where($where);
    			if(!empty($keyword) && $keyword){
					$object = $object->where(function($query) use ($keyword){
						$query->where($this->table.'.OrderCode','like','%'.$keyword.'%');
					});
				}
    			$object = $object->orderBy($this->table.'.ID','desc')
    						->offset($offset)
    						->limit($limit)
    						->get();
    			if($object){
    				$object = $this->getResultData($object,true);
    			}

    			return ['rows' => $rows,'data'=>$object];
    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
    	}

    	protected function getResultData($object,$flag = false){
    		if($flag){
    			foreach($object as $k => $v){
					$object[$k]->total_amount /= 100;
					$object[$k]->reduce /= 100;
					$object[$k]->score_amount /= 100;
					$object[$k]->freight /= 100;
					$object[$k]->payable_amount /= 100;
					$object[$k]->payment /= 100;
					$object[$k]->receivable /= 100;
					$object[$k]->pay_amount /= 100;
					$object[$k]->refund_amount /= 100;
					$object[$k]->send_balance /= 100;
					$object[$k]->pay_time = $this->getDateTime($object[$k]->pay_time);
					$object[$k]->store_time = $this->getDateTime($object[$k]->store_time);
					$object[$k]->pickup_time = $this->getDateTime($object[$k]->pickup_time);
					$object[$k]->express_time = $this->getDateTime($object[$k]->express_time);
					$object[$k]->refund_time = $this->getDateTime($object[$k]->refund_time);
					$object[$k]->locked_time = $this->getDateTime($object[$k]->locked_time);
					$object[$k]->un_locked_time = $this->getDateTime($object[$k]->un_locked_time);
					$object[$k]->create_time = $this->getDateTime($object[$k]->create_time);
					$object[$k]->update_time = $this->getDateTime($object[$k]->update_time);
					$object[$k]->delete_time = $this->getDateTime($object[$k]->delete_time);
					$object[$k]->cate_title = $this->cate_value[$object[$k]->order_type];
					$object[$k]->status_title = $this->getFieldValue($object[$k]->status,$this->status_value);
				}
    		}else{
    			$object->total_amount /= 100;
				$object->reduce /= 100;
				$object->score_amount /= 100;
				$object->freight /= 100;
				$object->payable_amount /= 100;
				$object->payment /= 100;
				$object->receivable /= 100;
				$object->pay_amount /= 100;
				$object->refund_amount /= 100;
				$object->send_balance /= 100;
				$object->pay_time = $this->getDateTime($object->pay_time);
				$object->store_time = $this->getDateTime($object->store_time);
				$object->pickup_time = $this->getDateTime($object->pickup_time);
				$object->express_time = $this->getDateTime($object->express_time);
				$object->refund_time = $this->getDateTime($object->refund_time);
				$object->locked_time = $this->getDateTime($object->locked_time);
				$object->un_locked_time = $this->getDateTime($object->un_locked_time);
				$object->create_time = $this->getDateTime($object->create_time);
				$object->update_time = $this->getDateTime($object->update_time);
				$object->delete_time = $this->getDateTime($object->delete_time);
				$object->cate_title = $this->cate_value[$object->order_type];
				$object->status_title = $this->getFieldValue($object->status,$this->status_value);
    		}

    		return $object;
    	}

    	protected function getQueryList(Request $request): array
    	{
    		$keyword = trim($request->input('keyword',''));
    		$itime = $request->input('itime',[]);
    		$cate_id = $request->input('cate_id','');
    		$status = $request->input('status','');

    		$cateValue = $this->getCateValue();
    		array_unshift($cateValue, ['label'=>'-All-','value'=>'']);
    		$statusValue = $this->getStatusValue();
    		array_unshift($statusValue, ['label'=>'-All-','value'=>'']);

    		$query = [
    			['type'=>'input','label'=>'关键词','prop'=>'keyword','value'=>$keyword,'placeholder'=>'订单号/用户名/ID/手机号/身份证'],
    			['type'=>'date-picker','label'=>'日期','prop'=>'itime','value'=>$itime,'attrs'=>['type'=>'daterange','range-separator'=>'To','start-placeholder'=>'开始日期','end-placeholder'=>'结束日期','value-format'=>'YYYY-MM-DD']],
    			['type'=>'select','label'=>'订单类型','prop'=>'cate_id','value'=>$cate_id,'children'=>$cateValue,'attrs'=>['style'=>['width'=>'110px']]],
    			['type'=>'select','label'=>'状态','prop'=>'status','value'=>$status,'children'=>$statusValue,'attrs'=>['style'=>['width'=>'100px']]],
    		];

    		return $query;
    	}

    	/**
    	 * 创建订单
    	 * @param  Request $request [description]
    	 * @param  integer $user_id [description]
    	 * @return [type]           [description]
    	 */
    	public function create(Request $request,$user_id = 0){
    		try{
    			if(!$user_id){
    				$user_id = $request->post('user_id',0);
    			}
    			if(!$user_id || !is_numeric($user_id) || !$user_id < 0){
    				return 100007;
    			}

    			$address_id = $request->post('address_id',0);

    			$_class_name = $this->getClassName('UserAddress');
    			$service = new $_class_name();
    			$res = $service->getList($request,'default',$user_id,$address_id);
    			if(!$res || !isset($res['user'])){
    				return $res;
    			}

    			$goods = $request->post('goods',[]);
    			if(is_string($goods)){
    				$goods = $this->getDecodeData($goods);
    			}
    			if(!$goods || !is_array($goods)){
    				return '无效的商品';
    			}

    			$_class_name = $this->getClassName('Goods');
    			$service = new $_class_name();
    			$data = $service->getList($request,'order',$goods);

    			$quantity = 0;
    			$amount = 0;
    			$freight = 0;
    			$score = 0;

    			if($data){
    				foreach($data as $items){
    					if($items){
    						foreach($items as $item){
    							$quantity += $item->quantity;
    							$amount += $item->amount*100;
    							$freight += $item->freight;
    							$score += $item->score;
    						}
    					}
    				}
    			}

    			if($amount){
    				$amount /= 100;
    			}

    			return ['quantity'=>$quantity,'amount'=>$amount,'freight'=>$freight,'score'=>$score,'user'=>$res['user'],'address'=>$res['address'],'data'=>$data];

    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
    	}

    	/**
    	 * 统一签名
    	 * @param Request $request [description]
    	 * @param string  $type    [description]
    	 */
    	public function setSign(Request $request,$type = 'mini'){
    		$type = $request->input('type',$type);
    		$service = new SignService();
    		return $service->setSign($request,$type);
    	}

    	/**
    	 * 统一支付
    	 * @param Request $request [description]
    	 */
    	public function setPay(Request $request,$type = 'mini'){
    		$type = $request->input('type',$type);
    		$service = new PayService();
    		return $service->setPay($request,$type);
    	}

    	/**
    	 * 统一回调
    	 * @param Request $request [description]
    	 */
    	public function setNotify(Request $request,$type = 'mini'){
    		$type = $request->input('type',$type);
    		$service = new NotifyService();
    		return $service->setNotify($request,$type);
    	}

    	/**
    	 * 清空订单详情数据
    	 * @param Request $request [description]
    	 */
    	protected function setTruncate(Request $request){
    		Db::table('order_detail')->truncate();
    		return true;
    	}

    	/**
    	 * [setExcute description]
    	 * @param Request $request [description]
    	 * @param [type]  $data    [description]
    	 * @param boolean $flag    [description]
    	 */
    	protected function setExcute(Request $request,$data,$flag = false){
    		var_dump('flag: '.$flag);
    		switch($flag){
    			case '':
    			case 'add':
    			case false:
    				$service = new OrderDetailModel();
    				return $service->appendData($request,$data);
    			case 'sendout':
    			case 'deliver':		// 发货
    				$service = new OrderExpressModel();
    				return $service->appendData($request,$data);
    			default:
    				return true;

    		}

    		if(!$flag){
    			$service = new OrderDetailModel();
    			return $service->updateData($request,$data);
    		}

    		return true;
    	}

    	/**
    	 * 计算应付金额
    	 * @param  [type]  $amount   [description]
    	 * @param  integer $discount [description]
    	 * @param  integer $reduce   [description]
    	 * @param  integer $freight  [description]
    	 * @return [type]            [description]
    	 */
    	private function getPayableAmount($amount,$discount = 100,$reduce = 0,$freight = 0,$dscore = 0,$dbalance = 0,$dcoupon = 0, $multiplier = 100){
    		$amount *= $multiplier;
    		if($discount < 100 && $discount >= 0){
    			$amount *= ($discount/100);
    		}
    		if($reduce){
    			$amount -= $reduce*$multiplier;
    		}
    		if($freight){
    			$amount += $freight*$multiplier;
    		}
    		if($dscore){
    			$amount -= $dscore*$multiplier;
    		}
    		if($dbalance){
    			$amount -= $dbalance*$multiplier;
    		}
    		if($dcoupon){
    			$amount -= $dcoupon*$multiplier;
    		}

    		return $amount;
    	}

    	protected function getUsersList(Request $request,$user_id = 0,$status = []){
    		$user_id = $user_id ? $user_id : $request->input('user_id',0);
    		if(!$user_id || !is_numeric($user_id) || $user_id < 0){
    			return [];
    		}

    		$field = ["Status as status",DB::raw('COUNT(ID) as count')];
    		$where = $this->getWhere($request);
    		array_push($where,[$this->table.'.UserID','=',$user_id]);

    		$object = Db::table($this->table)
    					->select(...$field)
    					->where($where)
    					->groupBy('status')
    					->get()
    					->keyBy('status');

    		$result = [];
    		foreach($this->status_value as $item){
    			$options = [
    				'label' => $item['label'],
    				'value' => $item['value'],
    				'count' => isset($object[$item['value']]) ? $object[$item['value']]->count : 0
    			];
    			array_push($result,$options);
    		}
    		
    		return $result;
    	}

    	/**
    	 * 统一验证数据
    	 * @param  Request $request [description]
    	 * @param  integer $id      [description]
    	 * @param  [type]  $obj     [description]
    	 * @return [type]           [description]
    	 */
    	protected function validate(Request $request,$id = 0,$obj = null){

    		$action = trim($request->input('action',''));

    		$user_key = $action ? 'uid' : 'user_id';
    		if($action === 'add'){
    			$users = $request->post('user',[]);
    			if(!$users || !isset($users['user_id'])){
    				return '请选择下单用户';
    			}
    			$user_id = $users['user_id'];
    		}else{
				$user_id = $request->post($user_key,0);
			}
			
			if(!$user_id){
				return 100790;
			}	

			$_class_name = $this->getClassName('User');
			$service = new $_class_name();
			$user = $service->getList($request,$user_id);
			// var_dump($user);
			if(!$user || !is_object($user)){
				return $user;
			}
			
    		switch($action){
    			case 'amend':			// 改价
    				if(!in_array($obj->status,[0,1])){
    					return '订单状态不支持改价';
    				}

                    $amount = $request->post('amount',0);						// 商品金额
                    $payable_amount = $request->post('payable_amount',0);		// 应付金额
    				$discount = $request->post('discount',100);					// 折扣
                    $reduce = $request->post('reduce',0);						// 优惠金额
                    $score_amount = $request->post('score_amount',0);			// 积分抵扣
                    $balance_amount = $request->post('balance_amount',0);		// 余额抵扣
                    $coupon_amount = $request->post('coupon_amount',0);			// 代金券抵扣
                    $freight = $request->post('freight',0);						// 运费

    				if(!is_numeric($amount) || $amount < 0){
    					return '商品金额有误';
    				}
    				
    				if($amount*100 !== $obj->total_amount*100){
    					return '商品金额错误';
    				}

    				if(!is_numeric($payable_amount) || $payable_amount < 0){
    					return '应付金额有误';
    				}

    				if(!is_numeric($discount) || $discount < 0 || $discount > 100){
    					return '折扣填写有误';
    				}

    				if(!is_numeric($reduce) || $reduce < 0 || $reduce*100 > $amount*100){
    					return '优惠金额填写有误';
    				}

    				if(!is_numeric($score_amount) || $score_amount < 0 || $score_amount*100 > $amount*100){
    					return '积分抵扣金额填写有误';
    				}

    				if(!is_numeric($balance_amount) || $balance_amount < 0 || $balance_amount*100 > $amount*100){
    					return '余额抵扣金额填写有误';
    				}

    				if(!is_numeric($coupon_amount) || $coupon_amount < 0 || $coupon_amount*100 > $amount*100){
    					return '代金券抵扣金额填写有误';
    				}

    				if(!is_numeric($freight) || $freight < 0){
    					return '运费填写有误';
    				}

    				$payable = $amount * $discount - $reduce*100 - $score_amount*100 - $balance_amount*100 - $coupon_amount*100 + $freight*100;
    				if($payable !== $payable_amount*100){
    					return '应付金额计算有误';
    				}
				
    				$data['discount'] = $discount;
    				$data['reduce'] = $reduce * 100;
    				$data['score_amount'] = $score_amount * 100;
    				$data['balance_amount'] = $balance_amount * 100;
    				$data['coupon_amount'] = $coupon_amount * 100;
    				$data['freight'] = $freight * 100;
    				$data['payable_amount'] = $payable_amount * 100;

    				break;
    			case 'cancel':			// 取消订单
    				if($obj->status === -1){
    					return '订单已取消,请勿重复操作!';
    				}
    				if($obj->status !== 0 && $obj->status !== 1){
    					return '订单状态不支持取消';
    				}
    				$data['status'] = -1;
    				break;
    			case 'confirm':			// 确认订单
    				if($obj->status === 1){
    					return '订单已确认,请勿重复操作!';
    				}
    				if($obj->status !== 0){
    					return '订单已确认或状态不支持';
    				}
    				$data['status'] = 1;
    				break;
    			case 'sendout':			// 发货
    			case 'deliver':			// 发货
    				if($obj->status === -2){
    					return '订单已锁定';
    				}
    				if($obj->status === -1){
    					return '订单已取消';
    				}
    				if($obj->status === 0){
    					return '订单未确认';
    				}
    				if($obj->status === 1){
    					return '订单未支付';
    				}
    				if($obj->status > 3){
    					return '订单状态不支持发货';
    				}
    				$delivery_mode = $request->post('delivery_mode',1);
    				$express = $request->post('express',[]);

    				if(!in_array($delivery_mode,[0,1,2,3,4])){
    					return '无效的发货模式';
    				}

    				if(in_array($delivery_mode,[1,2])){
    					if(!$express){
    						return '请填写发货快递信息';
    					}
    				}

    				$data['delivery_mode'] = $delivery_mode;
    				$data['express'] = $express;
    				break;
    			case 'refund':			// 退款
    				$refund_amount = $request->post('refund_amount',0);
    				if($refund_amount == '' || !$refund_amount){
    					return '退款金额为空则无需退款';
    				}
    				if(!is_numeric($refund_amount) || $refund_amount < 0){
    					return '无效的退款金额';
    				}
    				if($refund_amount*100 > $obj->payable_amount*100){
    					return '退款金额不能大于退款金额';
    				}

    				$send_coupon = $request->post('send_coupon','');
    				$refund_goods = $request->post('refund_goods',[]);



    				$data['refund_amount'] = $refund_amount;
    				$data['send_coupon'] = $send_coupon;
    				$data['refund_goods'] = $refund_goods;
    				break;
    			case 'add':				// 后台下单
    				if(!$this->getConfig('order_is_order')){
						return '系统关闭下单，请稍后再试';
					}
					$type = $request->post('type',1);
					if($type !== 1){
						return '无效的下单类型';
					}

					if($this->config['order_is_auth'] && !$user->is_auth){
						return '用户未实名,请完成实名后重新下单';
					}

					$users = $request->post('user',[]);
					$address_id = isset($users['address_id']) ? $users['address_id'] : 0;
					if($address_id){
						$_class_name = $this->getClassName('UserAddress');
						if($_class_name){
							$service = new $_class_name();
							$address = $service->getList($request,$address_id);
							if(!$address || $user_id != $address->user_id){
								return '无效的收件地址';
							}
						}
					}
					
					$goods = $request->post('goods',[]);
					// var_dump('goods: ',$goods);
					if(!$goods || !is_array($goods)){
						return '请选择商品';
					}

					$count = 0;
					$fee = 0;
					$data['goods_title'] = '';
					foreach($goods as $key => $item){
						if(!isset($item['goods_id']) || !$item['goods_id']){
							return 100810;
						}

						$_class_name = $this->getClassName('Goods');
						$service = new $_class_name();
						$res_goods = $service->getList($request,$item['goods_id']);
						// var_dump($res_goods);
						if(!$res_goods || !is_object($res_goods)){
							return $res_goods;
						}

						if($key === 0){
							$data['goods_title'] = $res_goods->goods_title;
						}
						
						if($res_goods->status !== 1){
							return '商品:'.$res_goods->goods_title.'未上架';
						}
						if($res_goods->stock && ($res_goods->stock - $res_goods->sales <= 0)){
							return '商品:'.$res_goods->goods_title.'库存不足';
						}

						$spec_data = $res_goods->specs;
						
						foreach($spec_data as $spec_item){
							if($spec_item->id == $item['spec_id']){
								if($spec_item->stock && ($spec_item->stock - $spec_item->sales) < $item['quantity']){
									return '商品:'.$res_goods->goods_title.'库存不足';
								}
							}
						}


						if($res_goods->limit_count){
							// 商品限够
							$user_count = $this->getGoodsBuyByUser($item['goods_id'],$user_id);
							if($user_count >= $res_goods->limit_count){
								return '超过商品:'.$res_goods->goods_title.'限购数量';
							}
						}

						if($res_goods->limit_order){
							// 订单限够
							$user_order = $this->getOrderBuyByUser($item['goods_id'],$user_id);
							if($user_order >= $res_goods->limit_order){
								return '超过商品:'.$res_goods->goods_title.'订单限购数量';
							}
						}

						if(!isset($item['spec_id']) || !$item['spec_id']){
							return '无效的商品规格';
						}

						$_class_name = $this->getClassName('GoodsSpec');
						$service = new $_class_name();
						$res_spec = $service->getList($request,$item['spec_id']);
						if(!$res_spec || !is_object($res_spec)){
							return $res_spec;
						}

						// var_dump('item price: '.$item['price']*100);
						// var_dump('res price: '.$res_spec->price*100);
						// if(!isset($item['price']) || !is_numeric($item['price'])){
						// 	return '无效的商品价格';
						// }

						if((int)($item['price']*100) !== (int)($res_spec->price*100)){
							return '无效的商品价格';
						}

						if(!isset($item['quantity']) || !is_numeric($item['quantity']) || !$item['quantity']){
							return '无效的下单数量';
						}

						// if(!isset($item['amount']) || !is_numeric($item['amount'])){
						// 	return '无效的下单金额';
						// }

						if((int)($item['price'] * 100 * $item['quantity']) !== (int)($item['amount']*100)){
							return '无效的下单金额';
						}

						if((int)($res_spec->price * 100 * $item['quantity']) !== (int)($item['amount']*100)){
							return '无效的下单金额';
						}

						$goods[$key]['cate_id'] = $res_goods->cate_id;
						$goods[$key]['stock_code'] = $res_goods->stock_code ?? '';
						$goods[$key]['goods_title'] = $res_goods->goods_title;
						$goods[$key]['goods_pic'] = $res_goods->pic;
						$goods[$key]['first_title'] = $res_spec->first_title;
						$goods[$key]['second_title'] = $res_spec->second_title;

						$count += $item['quantity'];
						$fee += $item['amount'];
					}

					$description = $request->post('description',[]);
					if(!$description){
						return '请填写折扣与优惠';
					}
					$discount = isset($description['discount']) ? $description['discount'] : 100;
					$reduce = isset($description['reduce']) ? $description['reduce'] : 0;
					$score_amount = isset($description['score_amount']) ? $description['score_amount'] : 0;
					$balance_amount = isset($description['balance_amount']) ? $description['balance_amount'] : 0;
					$coupon_amount = isset($description['coupon_amount']) ? $description['coupon_amount'] : 0;
					$freight = isset($description['freight']) ? $description['freight'] : 0;
					$total_amount = isset($description['total_amount']) ? $description['total_amount'] : 0;
					$payable_amount = isset($description['payable_amount']) ? $description['payable_amount'] : 0;

					if(!is_numeric($discount) || $discount < 0){
						return '商品折扣填写有误';
					}

					if(!is_numeric($reduce) || $reduce < 0){
						return '商品优惠金额填写有误';
					}

					if(!is_numeric($score_amount) || $score_amount < 0){
						return '商品积分抵扣金额填写有误';
					}

					if(!is_numeric($balance_amount) || $balance_amount < 0){
						return '商品余额抵扣金额填写有误';
					}

					if(!is_numeric($coupon_amount) || $coupon_amount < 0){
						return '商品优惠券抵扣金额填写有误';
					}

					if(!is_numeric($freight) || $freight < 0){
						return '运费填写有误';
					}

					if(!is_numeric($total_amount) || $total_amount < 0){
						return '商品总金额填写有误';
					}

					if(!is_numeric($payable_amount) || $payable_amount < 0){
						return '商品应付金额填写有误';
					}

					$osname = $request->post('osname','mini');
					$status = $request->post('status',0);
					$pay_type = $request->post('pay_type','wechat');
					$payee = $request->post('payee','merchant');
					
					$order_length = 20;
					if(isset($this->config['order_length'])){
						$order_length = $this->config['order_length'];
					}

					$data['order_type'] = $type;
					$data['order_code'] = $this->createUniqueCode('OrderCode',$order_length);
					$data['user_id'] = $user_id;
					$data['address_id'] = $address_id;
					$data['goods'] = $goods;
					$data['discount'] = $discount;
					$data['reduce'] = $reduce * 100;
					$data['score_amount'] = $score_amount * 100;
					$data['balance_amount'] = $balance_amount * 100;
					$data['coupon_amount'] = $coupon_amount * 100;
					$data['freight'] = $freight * 100;
					$data['total_count'] = $count;
					$data['total_amount'] = $total_amount * 100;
					$data['payable_amount'] = $payable_amount * 100;
					$data['osname'] = $osname;
					$data['pay_type'] = $pay_type;
					$data['payee'] = $payee;
					$data['status'] = $status;
		    		$data['realname'] = $user->realname;
		    		$data['mobile'] = $user->mobile;
		    		$data['face'] = $user->face;
		    		$data['idcard'] = $user->idcard;
		    		$data['openid'] = $user->openid;
		    		$data['expire_time'] = time() + $this->config['order_wait_time'] * 60;

    				break;
    			case '':
    				// var_dump($this->config);
    				if(!$this->getConfig('order_is_order')){
						return '系统关闭下单，请稍后再试';
					}
					$type = $request->post('type',1);
					if($type !== 1){
						return '无效的下单类型';
					}

					if($this->config['order_is_auth'] && !$user->is_auth){
						return '用户未实名,请完成实名后重新下单';
					}

					$address_id = $request->post('address_id');
					if(!$address_id || !is_numeric($address_id)){
						return '无效的收件地址';
					}

					$_class_name = $this->getClassName('UserAddress');
					if($_class_name){
					$service = new $_class_name();
						$address = $service->getList($request,$address_id);
						// var_dump('收件地址');
						// var_dump($address);
						if(!$address || $user_id != $address->user_id){
							return '无效的收件地址';
						}
					}

					$total_count = $request->post('total_count',$request->post('quantity',0));
					if(!$total_count || !is_numeric($total_count)){
						return '无效的下单数量';
					}

					$total_amount = $request->post('total_amount',$request->post('amount',0));
					if(!$total_amount || !is_numeric($total_amount)){
						return '无效的下单金额';
					}

					$discount = $request->post('discount',100);
					if(!is_numeric($discount) || $discount < 0 || $discount > 100){
						return '无效的商品折扣';
					}

					$reduce = $request->post('reduce',0);
					if(!is_numeric($reduce)){
						return '无效的优惠金额';
					}

					$score = $request->post('score',0);
					if(!is_numeric($score)){
						return '无效的积分';
					}

					if($score > $user->score){
						return '使用积分不能大于用户的可用积分';
					}

					$score_amount = $request->post('score_amount',0);
					if($score){
						if(!$score_amount){
							return '无效的积分抵扣金额';
						}
						if(round($score / $this->config['goods_score_exchange_amount'],2) * 100 !== $score_amount * 100){
							return '积分抵扣金额错误';
						}
					}

					$freight = $request->post('freight',0);
					if(empty($freight)){
						$freight = 0;
					}
					if(!is_numeric($freight)){
						return '运费只能填数字';
					}

					$goods = $request->post('goods');
					if(is_string($goods)){
						$goods = $this->getDecodeData($goods);
					}
					// var_dump($goods);
					if(!$goods || !count($goods)){
						return '无效的商品列表';
					}

					$count = 0;
					$fee = $freight;

					// var_dump('freight: '.$freight);

					$data['goods_title'] = '';
					foreach($goods as $key => $item){
						if(!isset($item['goods_id']) || !$item['goods_id']){
							return 100810;
						}

						$_class_name = $this->getClassName('Goods');
						$service = new $_class_name();
						$res_goods = $service->getList($request,$item['goods_id']);
						if(!$res_goods || !is_object($res_goods)){
							return $res_goods;
						}

						if($key === 0){
							$data['goods_title'] = $res_goods->goods_title;
						}
						
						if($res_goods->status !== 1){
							return '商品未上架';
						}

						// if(!in_array($user_id,[59000941,19860059,17091010])){
						// 	if($res_goods->role_pid){
						// 		if(is_array($res_goods->role_pid)){
						// 			if(!in_array($user->group_id,$res_goods->role_pid)){
						// 				return '商品: '.$res_goods->goods_title.'需要升级会员权限方可购买';
						// 			}
						// 		}else{
						// 			if((int)$user->group_id !== (int)$res_goods->role_pid){
						// 				return "需要升级会员权限方可购买";
						// 			}
						// 		}
						// 	}
						// }

						if($res_goods->stock && ($res_goods->stock - $res_goods->sales <= 0)){
							return '商品库存不足';
						}

						$spec_data = $res_goods->specs;
						
						foreach($spec_data as $spec_item){
							if($spec_item->id == $item['spec_id']){
								if($spec_item->stock && ($spec_item->stock - $spec_item->sales) < $item['quantity']){
									return '商品库存不足';
								}
							}
						}


						if($res_goods->limit_count){
							// 商品限够
							$user_count = $this->getGoodsBuyByUser($item['goods_id'],$user_id);
							if($user_count >= $res_goods->limit_count){
								return '超过商品限购数量';
							}
						}

						if($res_goods->limit_order){
							// 订单限够
							$user_order = $this->getOrderBuyByUser($item['goods_id'],$user_id);
							if($user_order >= $res_goods->limit_order){
								return '超过商品订单限购数量';
							}
						}

						if($res_goods->deduct_score && $score > $res_goods->deduct_score){
							return '使用积分不能大于商品限定的最高抵扣积分';
						}

						if(!isset($item['spec_id']) || !$item['spec_id']){
							return '无效的商品规格';
						}

						$_class_name = $this->getClassName('GoodsSpec');
						$service = new $_class_name();
						$res_spec = $service->getList($request,$item['spec_id']);
						if(!$res_spec || !is_object($res_spec)){
							return $res_spec;
						}

						// var_dump('item price: '.$item['price']*100);
						// var_dump('res price: '.$res_spec->price*100);
						// if(!isset($item['price']) || !is_numeric($item['price'])){
						// 	return '无效的商品价格';
						// }

						if((int)($item['price']*100) !== (int)($res_spec->price*100)){
							return '无效的商品价格';
						}

						if(!isset($item['quantity']) || !is_numeric($item['quantity']) || !$item['quantity']){
							return '无效的下单数量';
						}

						// if(!isset($item['amount']) || !is_numeric($item['amount'])){
						// 	return '无效的下单金额';
						// }

						if((int)($item['price'] * 100 * $item['quantity']) !== (int)($item['amount']*100)){
							return '无效的下单金额';
						}

						if((int)($res_spec->price * 100 * $item['quantity']) !== (int)($item['amount']*100)){
							return '无效的下单金额';
						}

						$goods[$key]['cate_id'] = $res_goods->cate_id;
						$goods[$key]['stock_code'] = $res_goods->stock_code ?? '';
						$goods[$key]['goods_title'] = $res_goods->goods_title;
						$goods[$key]['goods_pic'] = $res_goods->pic;
						$goods[$key]['first_title'] = $res_spec->first_title;
						$goods[$key]['second_title'] = $res_spec->second_title;

						$count += $item['quantity'];
						$fee += $item['amount'];
					}

					// var_dump('count: '.$count.' total: '.$total_count);
					// var_dump('amount: '.$fee.' total: '.$total_amount);
					if((int)$count !== (int)$total_count){
						return '订单总数量有误';
					}
					// var_dump('fee: '.$fee*100);
					// var_dump('total_amount: '.$total_amount*100);
					if((int)$fee*100 !== (int)$total_amount*100){
						return '订单总金额有误';
					}

					$store_id = $request->post('store_id',0);
					if(empty($store_id) || !is_numeric($store_id)){
						$store_id = 0;
					}

					if($store_id){
						$service = new StoreModel();
						$store = $service->getList($request,$store_id);
						if(!$store || !is_object($store)){
							return '无效的自提点';
						}

						$data['store_pid'] = $store->province_id;
						$data['store_cid'] = $store->city_id;
						$data['store_did'] = $store->district_id;
						if($store->street_id){
							$data['store_sid'] = $store->street_id;
						}
					}

					$remark = trim($request->post('remark',''));

					$expire_time = time() + $this->config['order_wait_time'] * 60;

					$order_length = 20;
					if(isset($this->config['order_length'])){
						$order_length = $this->config['order_length'];
					}

					$invoice_special = $request->post('invoice_special',0);
					if(empty($invoice_special)){
						$invoice_special = 0;
					}

					$invoice_enterprise = $request->post('invoice_enterprise',0);
					if(empty($invoice_enterprise)){
						$invoice_enterprise = 0;
					}

					$invoice_is_detail = $request->post('invoice_is_detail',1);
					if(empty($invoice_is_detail)){
						$invoice_is_detail = 1;
					}

					$is_invoice = 0;
					$invoice_title = trim($request->post('invoice_title',''));
					if($invoice_title){
						$is_invoice = 1;
					}
					$invoice_code = trim($request->post('invoice_code',''));
					$invoice_address = trim($request->post('invoice_address',''));
					$invoice_telphone = trim($request->post('invoice_telphone',''));
					$invoice_bank_name = trim($request->post('invoice_bank_name',''));
					$invoice_account = trim($request->post('invoice_account',''));
					$invoice_mobile = trim($request->post('invoice_mobile',''));
					$invoice_email = trim($request->post('invoice_email',''));

					$data['is_invoice'] = $is_invoice;
					$data['invoice_special'] = $invoice_special;
					$data['invoice_enterprise'] = $invoice_enterprise;
					$data['invoice_is_detail'] = $invoice_is_detail;
					$data['invoice_title'] = $invoice_title;
					$data['invoice_code'] = $invoice_code;
					$data['invoice_address'] = $invoice_address;
					$data['invoice_telphone'] = $invoice_telphone;
					$data['invoice_bank_name'] = $invoice_bank_name;
					$data['invoice_account'] = $invoice_account;
					$data['invoice_mobile'] = $invoice_mobile;
					$data['invoice_email'] = $invoice_email;

					$data['order_type'] = $type;
					$data['order_code'] = $this->createUniqueCode('OrderCode',$order_length);
					$data['user_id'] = $user_id;
					$data['realname'] = $user->realname;
					$data['face'] = $user->face;
					$data['mobile'] = $user->mobile;
					$data['idcard'] = $user->idcard;
					$data['openid'] = $user->openid;
					$data['user_address_id'] = $address_id;
					$data['total_count'] = $total_count;
					$data['freight'] = $freight * 100;
					$data['total_amount'] = $total_amount * 100;
					$data['discount'] = $discount;
					$data['reduce'] = $reduce * 100;
					$data['score'] = $score;
					$data['score_amount'] = $score_amount * 100;
					$data['payable_amount'] = ($data['total_amount'] * ($discount/100)) - $data['reduce'] - $data['score_amount'];
					$data['store_id'] = $store_id;
					$data['remark'] = $remark;
					$data['expire_time'] = $expire_time;
					$data['goods'] = $goods;

					break;
    			default:
    				$data = [];
    		}



    		// var_dump($data);
    		// return $action . ' aa';
    		
    		return $data;
    	}

    	/**
    	 * 统一获取表单
    	 * @param  Request $request [description]
    	 * @param  integer $id      [description]
    	 * @return [type]           [description]
    	 */
    	protected function getActionList(Request $request,$id = 0): array
    	{
    		if($id){
    			$data = $this->getList($request,$id);
    		}
    		
    		$action_value = trim($request->input('action',''));
    		switch($action_value){
    			case 'amend':			// 改价
    				$action = [
    					['type'=>'input','label'=>'action','prop'=>'action','value'=>$action_value,'hidden'=>true,'attrs'=>['disabled'=>true]],
    					['type'=>'input','label'=>'订单编号','prop'=>'ordercode','value'=>$data->order_code,'attrs'=>['disabled'=>true]],
    					['type'=>'input','label'=>'下单用户','prop'=>'uname','value'=>$data->realname,'attrs'=>['disabled'=>true]],
    					['type'=>'table','label'=>'商品列表','prop'=>'goods','value'=>$data->goods,'options'=>[],'children'=>[
				            ['type'=>'varchar','label'=>'商品名称','prop'=>'goods_title'],
				            ['type'=>'varchar','label'=>'规格名称','prop'=>'first_title','fields'=>['second_title']],
				            ['type'=>'varchar','label'=>'数量','prop'=>'quantity','suffix'=>'件'],
				            ['type'=>'varchar','label'=>'单价','prop'=>'price','prefix'=>'￥','suffix'=>'元'],
				            ['type'=>'varchar','label'=>'金额','prop'=>'amount','prefix'=>'￥','suffix'=>'元'],
				        ]],
				        ['type'=>'description','label'=>'折扣与优惠','prop'=>'description','value'=>[],'options'=>['column'=>2,'label_width'=>80],'children'=>[
				            ['type'=>'input','label'=>'商品折扣','prop'=>'discount','value'=>$data->discount,'suffix'=>'%','attrs'=>['disabled'=>false]],
				            ['type'=>'input','label'=>'优惠金额','prop'=>'reduce','value'=>$data->reduce,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>false]],
				            ['type'=>'input','label'=>'积分抵扣','prop'=>'score_amount','value'=>$data->score_amount,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>false]],
				            ['type'=>'input','label'=>'余额抵扣','prop'=>'balance_amount','value'=>$data->balance_amount,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>false]],
				            ['type'=>'input','label'=>'代金券','prop'=>'coupon_amount','value'=>$data->coupon_amount,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>false]],
				            ['type'=>'input','label'=>'运费','prop'=>'freight','value'=>$data->freight,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>false]],
				            ['type'=>'input','label'=>'商品金额','prop'=>'total_amount','value'=>$data->total_amount,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				            ['type'=>'input','label'=>'应付金额','prop'=>'payable_amount','value'=>$data->payable_amount,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				        ]],
    					['type'=>'input','label'=>'备注','prop'=>'remark','value'=>$data->remark ?? ''],
    				];
    				break;
    			case 'cancel':			// 取消
    				$action = [
    					['type'=>'input','label'=>'action','prop'=>'action','value'=>$action_value,'hidden'=>true,'attrs'=>['disabled'=>true]],
    					['type'=>'input','label'=>'订单编号','prop'=>'ordercode','value'=>$data->order_code,'attrs'=>['disabled'=>true]],
    					['type'=>'input','label'=>'下单用户','prop'=>'uname','value'=>$data->realname,'attrs'=>['disabled'=>true]],
    					['type'=>'table','label'=>'商品列表','prop'=>'goods','value'=>$data->goods,'options'=>[],'children'=>[
				            ['type'=>'varchar','label'=>'商品名称','prop'=>'goods_title'],
				            ['type'=>'varchar','label'=>'规格名称','prop'=>'first_title','fields'=>['second_title']],
				            ['type'=>'varchar','label'=>'数量','prop'=>'quantity','suffix'=>'件'],
				            ['type'=>'varchar','label'=>'单价','prop'=>'price','prefix'=>'￥'],
				            ['type'=>'varchar','label'=>'金额','prop'=>'amount','prefix'=>'￥'],
				        ]],
				        ['type'=>'description','label'=>'折扣与优惠','prop'=>'description','value'=>[],'options'=>['column'=>2,'label_width'=>80],'children'=>[
				            ['type'=>'input','label'=>'商品折扣','prop'=>'discount','value'=>$data->discount,'suffix'=>'%','attrs'=>['disabled'=>true]],
				            ['type'=>'input','label'=>'优惠金额','prop'=>'reduce','value'=>$data->reduce,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				            ['type'=>'input','label'=>'积分抵扣','prop'=>'score_amount','value'=>$data->score_amount,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				            ['type'=>'input','label'=>'余额抵扣','prop'=>'balance_amount','value'=>$data->balance_amount,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				            ['type'=>'input','label'=>'代金券','prop'=>'coupon_amount','value'=>$data->coupon_amount,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				            ['type'=>'input','label'=>'运费','prop'=>'freight','value'=>$data->freight,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				            ['type'=>'input','label'=>'商品金额','prop'=>'total_amount','value'=>$data->total_amount,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				            ['type'=>'input','label'=>'应付金额','prop'=>'payable_amount','value'=>$data->payable_amount,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				        ]],
    					['type'=>'input','label'=>'备注','prop'=>'remark','value'=>$data->remark ?? ''],
    				];
    				break;
    			case 'confirm':				// 确认
    				$action = [
    					['type'=>'input','label'=>'action','prop'=>'action','value'=>$action_value,'hidden'=>true,'attrs'=>['disabled'=>true]],
    					['type'=>'input','label'=>'订单编号','prop'=>'ordercode','value'=>$data->order_code,'attrs'=>['disabled'=>true]],
    					['type'=>'input','label'=>'下单用户','prop'=>'uname','value'=>$data->realname,'attrs'=>['disabled'=>true]],
    					['type'=>'table','label'=>'商品列表','prop'=>'goods','value'=>$data->goods,'options'=>[],'children'=>[
				            ['type'=>'varchar','label'=>'商品名称','prop'=>'goods_title'],
				            ['type'=>'varchar','label'=>'规格名称','prop'=>'first_title','fields'=>['second_title']],
				            ['type'=>'varchar','label'=>'数量','prop'=>'quantity','suffix'=>'件'],
				            ['type'=>'varchar','label'=>'单价','prop'=>'price','prefix'=>'￥'],
				            ['type'=>'varchar','label'=>'金额','prop'=>'amount','prefix'=>'￥'],
				        ]],
				        ['type'=>'description','label'=>'折扣与优惠','prop'=>'description','value'=>[],'options'=>['column'=>2,'label_width'=>80],'children'=>[
				            ['type'=>'input','label'=>'商品折扣','prop'=>'discount','value'=>$data->discount,'suffix'=>'%','attrs'=>['disabled'=>true]],
				            ['type'=>'input','label'=>'优惠金额','prop'=>'reduce','value'=>$data->reduce,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				            ['type'=>'input','label'=>'积分抵扣','prop'=>'score_amount','value'=>$data->score_amount,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				            ['type'=>'input','label'=>'余额抵扣','prop'=>'balance_amount','value'=>$data->balance_amount,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				            ['type'=>'input','label'=>'代金券','prop'=>'coupon_amount','value'=>$data->coupon_amount,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				            ['type'=>'input','label'=>'运费','prop'=>'freight','value'=>$data->freight,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				            ['type'=>'input','label'=>'商品金额','prop'=>'total_amount','value'=>$data->total_amount,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				            ['type'=>'input','label'=>'应付金额','prop'=>'payable_amount','value'=>$data->payable_amount,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				        ]],
    					['type'=>'input','label'=>'备注','prop'=>'remark','value'=>$data->remark ?? ''],
    				];
    				break;
    			case 'pay':				// 支付
    				$action = [
    					['type'=>'input','label'=>'action','prop'=>'action','value'=>$action_value,'hidden'=>true,'attrs'=>['disabled'=>true]],
    					['type'=>'input','label'=>'订单编号','prop'=>'ordercode','value'=>$data->order_code,'attrs'=>['disabled'=>true]],
    					['type'=>'input','label'=>'下单用户','prop'=>'uname','value'=>$data->realname,'attrs'=>['disabled'=>true]],
    					['type'=>'table','label'=>'商品列表','prop'=>'goods','value'=>$data->goods,'options'=>[],'children'=>[
				            ['type'=>'varchar','label'=>'商品名称','prop'=>'goods_title'],
				            ['type'=>'varchar','label'=>'规格名称','prop'=>'first_title','fields'=>['second_title']],
				            ['type'=>'varchar','label'=>'数量','prop'=>'quantity','suffix'=>'件'],
				            ['type'=>'varchar','label'=>'单价','prop'=>'price','prefix'=>'￥'],
				            ['type'=>'varchar','label'=>'金额','prop'=>'amount','prefix'=>'￥'],
				        ]],
				        ['type'=>'description','label'=>'折扣与优惠','prop'=>'description','value'=>[],'options'=>['column'=>2,'label_width'=>80],'children'=>[
				            ['type'=>'input','label'=>'商品折扣','prop'=>'discount','value'=>$data->discount,'suffix'=>'%','attrs'=>['disabled'=>true]],
				            ['type'=>'input','label'=>'优惠金额','prop'=>'reduce','value'=>$data->reduce,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				            ['type'=>'input','label'=>'积分抵扣','prop'=>'score_amount','value'=>$data->score_amount,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				            ['type'=>'input','label'=>'余额抵扣','prop'=>'balance_amount','value'=>$data->balance_amount,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				            ['type'=>'input','label'=>'代金券','prop'=>'coupon_amount','value'=>$data->coupon_amount,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				            ['type'=>'input','label'=>'运费','prop'=>'freight','value'=>$data->freight,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				            ['type'=>'input','label'=>'商品金额','prop'=>'total_amount','value'=>$data->total_amount,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				            ['type'=>'input','label'=>'应付金额','prop'=>'payable_amount','value'=>$data->payable_amount,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				        ]],
    					['type'=>'input','label'=>'备注','prop'=>'remark','value'=>$data->remark ?? ''],
				        ['type'=>'radio-group','label'=>'支付方式','prop'=>'pay_type','value'=>$data->pay_type ?? 'wechat','children'=>$this->pay_type_value],
    					['type'=>'radio-group','label'=>'收款账户','prop'=>'payee','value'=>$data->payee ?? 'merchant','children'=>$this->payee_value],
    				];
    				break;
    			case 'deliver':			// 发货
    				$_class_name = $this->getClassName('Express');
    				$service = new $_class_name();
    				$express = $service->getList($request,'option');

    				$action = [
    					['type'=>'input','label'=>'action','prop'=>'action','value'=>$action_value,'hidden'=>true,'attrs'=>['disabled'=>true]],
    					['type'=>'input','label'=>'订单编号','prop'=>'ordercode','value'=>$data->order_code,'attrs'=>['disabled'=>true]],
    					['type'=>'table','label'=>'商品列表','prop'=>'goods','value'=>$data->goods,'options'=>[],'children'=>[
				            ['type'=>'varchar','label'=>'商品名称','prop'=>'goods_title'],
				            ['type'=>'varchar','label'=>'规格名称','prop'=>'first_title','fields'=>['second_title']],
				            ['type'=>'varchar','label'=>'数量','prop'=>'quantity','suffix'=>'件'],
				            ['type'=>'varchar','label'=>'单价','prop'=>'price','prefix'=>'￥'],
				            ['type'=>'varchar','label'=>'金额','prop'=>'amount','prefix'=>'￥'],
				        ]],
				        ['type'=>'description','label'=>'折扣与优惠','prop'=>'description','value'=>[],'options'=>['column'=>2,'label_width'=>80],'children'=>[
				            ['type'=>'input','label'=>'商品折扣','prop'=>'discount','value'=>$data->discount,'suffix'=>'%','attrs'=>['disabled'=>true]],
				            ['type'=>'input','label'=>'优惠金额','prop'=>'reduce','value'=>$data->reduce,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				            ['type'=>'input','label'=>'积分抵扣','prop'=>'score_amount','value'=>$data->score_amount,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				            ['type'=>'input','label'=>'余额抵扣','prop'=>'balance_amount','value'=>$data->balance_amount,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				            ['type'=>'input','label'=>'代金券','prop'=>'coupon_amount','value'=>$data->coupon_amount,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				            ['type'=>'input','label'=>'运费','prop'=>'freight','value'=>$data->freight,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				            ['type'=>'input','label'=>'商品金额','prop'=>'total_amount','value'=>$data->total_amount,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				            ['type'=>'input','label'=>'应付金额','prop'=>'payable_amount','value'=>$data->payable_amount,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				        ]],
    					['type'=>'input','label'=>'备注','prop'=>'remark','value'=>$data->remark ?? ''],
				        ['type'=>'radio-group','label'=>'发货方式','prop'=>'delivery_mode','value'=>$data->delivery_mode ?? 1,'children'=>$this->delivery_value],
				        ['type'=>'multiple','label'=>'快递信息','prop'=>'express','value'=>$data->express ?? [],'delimiter'=>'-','children'=>[
				        	['type'=>'select','label'=>'快递公司','prop'=>'coname','value'=>'','width'=>250,'children'=>$express],
	        	            ['type'=>'input','label'=>'快递单号','prop'=>'code','value'=>'','placeholder'=>'填写快递单号','width'=>250],
	        	            ['type'=>'input','label'=>'快递费用','prop'=>'amount','value'=>'','placeholder'=>'0.00','width'=>250,'prefix'=>'快递费￥','suffix'=>'元'],
				        ],'required'=>true],
				        ['type'=>'switch','label'=>'同步小程序','prop'=>'is_wx','value'=>1]
    				];
    				break;
    			case 'refund':			// 退款
    				// var_dump($data->goods);
    				array_push($data->goods,['index'=>count($data->goods)+1,'goods_id'=>0,'goods_title'=>'运费','first_title'=>'运费','second_title'=>'','quantity'=>1,'price'=>$data->freight,'amount'=>$data->freight]);
    				$action = [
    					['type'=>'input','label'=>'action','prop'=>'action','value'=>$action_value,'hidden'=>true,'attrs'=>['disabled'=>true]],
    					['type'=>'input','label'=>'订单编号','prop'=>'ordercode','value'=>$data->order_code,'attrs'=>['disabled'=>true]],
    					['type'=>'table','label'=>'选择退货商品','prop'=>'goods','value'=>$data->goods,'options'=>['is_select'=>true,'is_index'=>false],'children'=>[
				            ['type'=>'varchar','label'=>'商品名称','prop'=>'goods_title'],
				            ['type'=>'varchar','label'=>'规格名称','prop'=>'first_title','fields'=>['second_title']],
				            ['type'=>'varchar','label'=>'数量','prop'=>'quantity','suffix'=>'件'],
				            ['type'=>'varchar','label'=>'单价','prop'=>'price','prefix'=>'￥'],
				            ['type'=>'varchar','label'=>'金额','prop'=>'amount','prefix'=>'￥'],
				        ]],
				        ['type'=>'description','label'=>'折扣与优惠','prop'=>'description','value'=>[],'options'=>['column'=>2,'label_width'=>80],'children'=>[
				            ['type'=>'input','label'=>'商品金额','prop'=>'total_amount','value'=>$data->total_amount,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				            ['type'=>'input','label'=>'应付金额','prop'=>'payable_amount','value'=>$data->payable_amount,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				        ]],
				        ['type'=>'input','label'=>'退款商品','prop'=>'refund_goods','value'=>'','hidden'=>true],
				        ['type'=>'input','label'=>'退款金额','prop'=>'refund_amount','value'=>'','prefix'=>'￥','suffix'=>'元','required'=>true],
				        ['type'=>'select','label'=>'赠送代金券','prop'=>'send_coupon','value'=>'','children'=>[]],
    				];
    				break;
    			case 'lock':			// 锁定
    				$action = [
    					['type'=>'input','label'=>'action','prop'=>'action','value'=>$action_value,'hidden'=>true,'attrs'=>['disabled'=>true]],
    					['type'=>'input','label'=>'订单编号','prop'=>'ordercode','value'=>$data->order_code,'attrs'=>['disabled'=>true]],
    					['type'=>'table','label'=>'商品列表','prop'=>'goods','value'=>$data->goods,'options'=>[],'children'=>[
				            ['type'=>'varchar','label'=>'商品名称','prop'=>'goods_title'],
				            ['type'=>'varchar','label'=>'规格名称','prop'=>'first_title','fields'=>['second_title']],
				            ['type'=>'varchar','label'=>'数量','prop'=>'quantity','suffix'=>'件'],
				            ['type'=>'varchar','label'=>'单价','prop'=>'price','prefix'=>'￥'],
				            ['type'=>'varchar','label'=>'金额','prop'=>'amount','prefix'=>'￥'],
				        ]],
				        ['type'=>'description','label'=>'折扣与优惠','prop'=>'description','value'=>[],'options'=>['column'=>2,'label_width'=>80],'children'=>[
				            ['type'=>'input','label'=>'商品折扣','prop'=>'discount','value'=>$data->discount,'suffix'=>'%','attrs'=>['disabled'=>true]],
				            ['type'=>'input','label'=>'优惠金额','prop'=>'reduce','value'=>$data->reduce,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				            ['type'=>'input','label'=>'积分抵扣','prop'=>'score_amount','value'=>$data->score_amount,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				            ['type'=>'input','label'=>'余额抵扣','prop'=>'balance_amount','value'=>$data->balance_amount,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				            ['type'=>'input','label'=>'代金券','prop'=>'coupon_amount','value'=>$data->coupon_amount,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				            ['type'=>'input','label'=>'运费','prop'=>'freight','value'=>$data->freight,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				            ['type'=>'input','label'=>'商品金额','prop'=>'total_amount','value'=>$data->total_amount,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				            ['type'=>'input','label'=>'应付金额','prop'=>'payable_amount','value'=>$data->payable_amount,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				        ]],
				        ['type'=>'input','label'=>'锁定原因','prop'=>'lock_reason','value'=>'','attrs'=>['type'=>'textarea','rows'=>3],'required'=>true],
    				];
    				break;
    			case 'unlock':			// 解锁
    				$action = [
    					['type'=>'input','label'=>'action','prop'=>'action','value'=>$action_value,'hidden'=>true,'attrs'=>['disabled'=>true]],
    					['type'=>'input','label'=>'订单编号','prop'=>'ordercode','value'=>$data->order_code,'attrs'=>['disabled'=>true]],
    					['type'=>'table','label'=>'商品列表','prop'=>'goods','value'=>$data->goods,'options'=>[],'children'=>[
				            ['type'=>'varchar','label'=>'商品名称','prop'=>'goods_title'],
				            ['type'=>'varchar','label'=>'规格名称','prop'=>'first_title','fields'=>['second_title']],
				            ['type'=>'varchar','label'=>'数量','prop'=>'quantity','suffix'=>'件'],
				            ['type'=>'varchar','label'=>'单价','prop'=>'price','prefix'=>'￥'],
				            ['type'=>'varchar','label'=>'金额','prop'=>'amount','prefix'=>'￥'],
				        ]],
				        ['type'=>'description','label'=>'折扣与优惠','prop'=>'description','value'=>[],'options'=>['column'=>2,'label_width'=>80],'children'=>[
				            ['type'=>'input','label'=>'商品折扣','prop'=>'discount','value'=>$data->discount,'suffix'=>'%','attrs'=>['disabled'=>true]],
				            ['type'=>'input','label'=>'优惠金额','prop'=>'reduce','value'=>$data->reduce,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				            ['type'=>'input','label'=>'积分抵扣','prop'=>'score_amount','value'=>$data->score_amount,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				            ['type'=>'input','label'=>'余额抵扣','prop'=>'balance_amount','value'=>$data->balance_amount,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				            ['type'=>'input','label'=>'代金券','prop'=>'coupon_amount','value'=>$data->coupon_amount,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				            ['type'=>'input','label'=>'运费','prop'=>'freight','value'=>$data->freight,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				            ['type'=>'input','label'=>'商品金额','prop'=>'total_amount','value'=>$data->total_amount,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				            ['type'=>'input','label'=>'应付金额','prop'=>'payable_amount','value'=>$data->payable_amount,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				        ]],
				        ['type'=>'input','label'=>'锁定原因','prop'=>'lock_reason','value'=>'','attrs'=>['type'=>'textarea','rows'=>3,'disabled'=>true]],
    				];
    				break;
    			case 'del':				// 删除
    				$action = [
    					['type'=>'input','label'=>'action','prop'=>'action','value'=>$action_value,'hidden'=>true,'attrs'=>['disabled'=>true]],
    					['type'=>'input','label'=>'订单编号','prop'=>'ordercode','value'=>$data->order_code,'attrs'=>['disabled'=>true]],
    					['type'=>'table','label'=>'商品列表','prop'=>'goods','value'=>$data->goods,'options'=>[],'children'=>[
				            ['type'=>'varchar','label'=>'商品名称','prop'=>'goods_title'],
				            ['type'=>'varchar','label'=>'规格名称','prop'=>'first_title','fields'=>['second_title']],
				            ['type'=>'varchar','label'=>'数量','prop'=>'quantity','suffix'=>'件'],
				            ['type'=>'varchar','label'=>'单价','prop'=>'price','prefix'=>'￥'],
				            ['type'=>'varchar','label'=>'金额','prop'=>'amount','prefix'=>'￥'],
				        ]],
				        ['type'=>'description','label'=>'折扣与优惠','prop'=>'description','value'=>[],'options'=>['column'=>2,'label_width'=>80],'children'=>[
				            ['type'=>'input','label'=>'商品折扣','prop'=>'discount','value'=>$data->discount,'suffix'=>'%','attrs'=>['disabled'=>true]],
				            ['type'=>'input','label'=>'优惠金额','prop'=>'reduce','value'=>$data->reduce,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				            ['type'=>'input','label'=>'积分抵扣','prop'=>'score_amount','value'=>$data->score_amount,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				            ['type'=>'input','label'=>'余额抵扣','prop'=>'balance_amount','value'=>$data->balance_amount,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				            ['type'=>'input','label'=>'代金券','prop'=>'coupon_amount','value'=>$data->coupon_amount,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				            ['type'=>'input','label'=>'运费','prop'=>'freight','value'=>$data->freight,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				            ['type'=>'input','label'=>'商品金额','prop'=>'total_amount','value'=>$data->total_amount,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				            ['type'=>'input','label'=>'应付金额','prop'=>'payable_amount','value'=>$data->payable_amount,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				        ]],
    				];
    				break;
    			case '':				// 下单
    			default:
    				$cate = $this->getOption('cate');
    				$status = $this->getOption('status');

    				$address = [];
    				if($id){
    					$_class_name = $this->getClassName('User');
    					$service = new $_class_name();
    					$address = $service->getList($request,'address','option');
    				}

    				$_class_name = $this->getClassName('User');
    				$service = new $_class_name();
    				$user = $service->getList($request,'option','clients,members,leaders');
    				// var_dump('user: ',$user);
    				
    				$discount = [];
    				for($i=1;$i<=100;$i++){
    					array_push($discount,['label'=>$i.'%','value'=>$i]);
    				}

    				
    				$action = [
    					['type'=>'input','label'=>'action','prop'=>'action','value'=>'add','required'=>true],
    					['type'=>'select','label'=>'下单类型','prop'=>'type','value'=>1,'children'=>$cate,'required'=>true],
    					['type'=>'description','label'=>'下单用户','prop'=>'user','value'=>[],'options'=>['column'=>2,'label_width'=>80],'children'=>[
    						['type'=>'select','label'=>'下单用户','prop'=>'user_id','value'=>$data->user_id ?? 0,'children'=>$user ?? [],'rules'=>['required'=>true,'message'=>'请选择下单用户']],
    						['type'=>'select','label'=>'收货地址','prop'=>'address_id','value'=>$data->address_id ?? 0,'children'=>$address ?? [['label'=>'无需发货','value'=>1]],'rules'=>['required'=>true,'message'=>'请选择收货地址']],
    					],'required'=>false],
    					['type'=>'alert','label'=>'收货地址','prop'=>'address','hidden'=>true,'value'=>''],
    					['type'=>'order','label'=>'商品列表','prop'=>'goods','value'=>[],'required'=>false],
				        ['type'=>'description','label'=>'折扣与优惠','prop'=>'description','value'=>[],'options'=>['column'=>2,'label_width'=>80],'children'=>[
				            ['type'=>'input','label'=>'商品折扣','prop'=>'discount','value'=>$data->discount ?? 100,'suffix'=>'%','attrs'=>['disabled'=>false]],
				            ['type'=>'input','label'=>'优惠金额','prop'=>'reduce','value'=>$data->reduce ?? 0,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>false]],
				            ['type'=>'input','label'=>'积分抵扣','prop'=>'score_amount','value'=>$data->score_amount ?? 0,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>false]],
				            ['type'=>'input','label'=>'余额抵扣','prop'=>'balance_amount','value'=>$data->balance_amount ?? 0,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>false]],
				            ['type'=>'input','label'=>'代金券','prop'=>'coupon_amount','value'=>$data->coupon_amount ?? 0,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>false]],
				            ['type'=>'input','label'=>'运费','prop'=>'freight','value'=>$data->freight ?? 0,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>false]],
				            ['type'=>'input','label'=>'商品金额','prop'=>'total_amount','value'=>$data->total_amount ?? 0,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				            ['type'=>'input','label'=>'应付金额','prop'=>'payable_amount','value'=>$data->payable_amount ?? 0,'prefix'=>'￥','suffix'=>'元','attrs'=>['disabled'=>true]],
				        ],'required'=>false],
    					['type'=>'radio-group','label'=>'发票','prop'=>'is_invoice','value'=>$data->is_invoice ?? 0,'children'=>[
    						['label'=>'无需发票','value'=>0],
    						['label'=>'普票','value'=>1],
    						['label'=>'专票','value'=>2],
    					]],
    					['type'=>'input','label'=>'备注','prop'=>'remark','value'=>$data->remark ?? ''],
    					['type'=>'radio-group','label'=>'平台','prop'=>'osname','value'=>$data->osname ?? 'mini','children'=>$this->osname_value],
    					['type'=>'radio-group','label'=>'订单状态','prop'=>'status','value'=>$data->status ?? 0,'children'=>$status],
    					['type'=>'radio-group','label'=>'支付方式','prop'=>'pay_type','value'=>$data->pay_type ?? 'wechat','children'=>$this->pay_type_value],
    					['type'=>'radio-group','label'=>'收款账户','prop'=>'payee','value'=>$data->payee ?? 'merchant','children'=>$this->payee_value],
    				];

    		}

    		return $action;
    	}

    	/**
    	 * 获取显示数据
    	 * @param  Request $request [description]
    	 * @return [type]           [description]
    	 */
    	protected function getMapList(Request $request): array
    	{
    		$map = [
    			['type'=>'id','label'=>'ID','prop'=>'id'],
				['type'=>'varchar','label'=>'订单编号','prop'=>'order_code','width'=>200,'callback'=>'order'],
				['type'=>'map','label'=>'订单类型','prop'=>'order_type','data'=>$this->getCateValue(),'width'=>100],
				['type'=>'varchar','label'=>'下单用户','prop'=>'realname','width'=>100],
				['type'=>'varchar','label'=>'手机号码','prop'=>'mobile','width'=>150],
				['type'=>'varchar','label'=>'总数量','prop'=>'total_count','suffix'=>'件'],
				['type'=>'varchar','label'=>'总金额','prop'=>'total_amount','prefix'=>'¥','width'=>120],
				['type'=>'varchar','label'=>'折扣','prop'=>'discount','suffix'=>'%'],
				['type'=>'varchar','label'=>'优惠金额','prop'=>'reduce','prefix'=>'¥','width'=>100],
				['type'=>'varchar','label'=>'积分抵扣','prop'=>'score_amount','prefix'=>'¥','width'=>100],
				['type'=>'varchar','label'=>'运费','prop'=>'freight','prefix'=>'¥','width'=>100],
				['type'=>'varchar','label'=>'应付金额','prop'=>'payable_amount','prefix'=>'¥','width'=>120],
				['type'=>'varchar','label'=>'支付平台','prop'=>'pay_type','width'=>100],
				['type'=>'varchar','label'=>'实付金额','prop'=>'pay_amount','prefix'=>'¥','width'=>120],
				['type'=>'varchar','label'=>'支付时间','prop'=>'pay_time','width'=>180],
				['type'=>'varchar','label'=>'下单时间','prop'=>'create_time','width'=>180],
				['type'=>'varchar','label'=>'状态','prop'=>'status_title'],
    		];

    		return $map;
    	}
	}
?>