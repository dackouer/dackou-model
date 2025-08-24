<?php
	namespace dackou\model\Order;

	use support\Request;
	use support\Db;

	class OrderModel extends \dackou\Model{
		protected $table = 'Order';
		protected $title = '订单';
		protected $primaryKey = 'order';
		protected $option_key = 'system,order';
		protected $is_cate = true;
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
		protected $status_value = [0=>'待确认',1=>'待支付',2=>'待发货',3=>'待收货',-1=>'已取消',-2=>'已锁定',10=>'已退款'];
		protected $action_value = [
    		['key'=>'charge','title'=>'充值','desc'=>'会员充值'],
    		['key'=>'modpwd','title'=>'改密','desc'=>'修改用户密码'],
    		['key'=>'setpwd','title'=>'设密','desc'=>'设置密码'],
    		['key'=>'auth','title'=>'认证','desc'=>'实名认证'],
    		['key'=>'email','title'=>'邮件','desc'=>'发送邮件'],
    		['key'=>'sms','title'=>'短信','desc'=>'发送短信'],
    		['key'=>'active','title'=>'激活','desc'=>'激活会员'],
    		['key'=>'lock','title'=>'锁定','desc'=>'锁定会员'],
    		['key'=>'del','title'=>'删除','desc'=>'删除会员'],
    		['key'=>'cancel','title'=>'注销','desc'=>'注销会员'],
    		['key'=>'unlock','title'=>'解除','desc'=>'解除锁定'],
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
							  ->orWhere($this->table.'.RealName','like','%'.$keyword.'%')
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
							  ->orWhere($this->table.'.RealName','like','%'.$keyword.'%')
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
    			}

    			return ['rows' => $rows,'data'=>$object];
    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
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
    		try{
    			$field = $this->getList($request,'field');
    			$where = $this->getWhere($request);
				array_push($where,[$this->table.'.OrderCode','=',$id]);

    			$object = Db::table($this->table)
    						->select(...$field)
    						->where($where)
    						->first();
    			if($object){
					$object = $this->getResultData($object);
    			}

    			return $object;
    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
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
					$object[$k]->unLocked_time = $this->getDateTime($object[$k]->unLocked_time);
					$object[$k]->create_time = $this->getDateTime($object[$k]->create_time);
					$object[$k]->update_time = $this->getDateTime($object[$k]->update_time);
					$object[$k]->delete_time = $this->getDateTime($object[$k]->delete_time);
					$object[$k]->cate_title = $this->cate_value[$object[$k]->type];
					$object[$k]->status_title = $this->status_value[$object[$k]->status];
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
				$object->unLocked_time = $this->getDateTime($object->unLocked_time);
				$object->create_time = $this->getDateTime($object->create_time);
				$object->update_time = $this->getDateTime($object->update_time);
				$object->delete_time = $this->getDateTime($object->delete_time);
				$object->cate_title = $this->cate_value[$object->type];
				$object->status_title = $this->status_value[$object->status];
    		}

    		return $object;
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
    		if(!$flag){
    			$service = new OrderDetailModel();
    			return $service->updateData($request,$data);
    		}

    		return true;
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
    		switch($action){
    			case 'sendout':			// 发货
    				$data = [];
    				break;
    			case 'refund':			// 退款
    				$data = [];
    				break;
    			case '':
    			default:
    				$data = [];
    		}

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
    		$action_value = trim($request->input('action',''));
    		switch($action_value){
    			case 'sendout':			// 发货
    				$action = [];
    				break;
    			case 'refund':			// 退款
    				$action = [];
    				break;
    			case '':
    			default:
    				$action = [];

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
    			['type'=>'varchar','label'=>'订单编号','prop'=>'order'],
    			['type'=>'varchar','label'=>'订单类型','prop'=>'cate_title'],
    			['type'=>'varchar','label'=>'下单用户','prop'=>'realname'],
    			['type'=>'varchar','label'=>'下单用户','prop'=>'realname'],
    			['type'=>'varchar','label'=>'总数量','prop'=>'total_count'],
    			['type'=>'varchar','label'=>'总金额','prop'=>'total_amount'],
    			['type'=>'varchar','label'=>'积分抵扣','prop'=>'sort'],
    			['type'=>'varchar','label'=>'运费','prop'=>'express_amount'],
    			['type'=>'varchar','label'=>'应付金额','prop'=>'payable_amount'],
    			['type'=>'varchar','label'=>'支付平台','prop'=>'platform'],
    			['type'=>'varchar','label'=>'实付金额','prop'=>'platform'],
    			['type'=>'varchar','label'=>'支付时间','prop'=>'pay_time'],
    			['type'=>'varchar','label'=>'下单时间','prop'=>'create_time'],
    			['type'=>'varchar','label'=>'状态','prop'=>'status_title'],
    		];

    		return $map;
    	}
	}
?>