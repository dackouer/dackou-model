<?php
	namespace dackou\model\Record;

	use support\Request;
	use support\Db;
	use dackou\Token;

	class RecordModel extends \dackou\Model{
		protected $table = 'Record';
		protected $title = '记录';
		protected static $prex = '';
		protected static $tabname = 'record';
		protected $cate_value = [
			1 => '积分',
			2 => '金币',
			3 => '签到',
			4 => '余额',
			5 => '订单',
			6 => '短信',
			7 => '邮件',
			8 => '分享',
			9 => '充值',
			10 => '分佣',
			11 => '提现',
			12 => '足迹',
			13 => '收藏',
			14 => '优惠券'
		];

		public static function setConfig(){
			if(!self::$prex){
				$config = config('database') ?? [];
	    		if(isset($config['connections']['mysql']['prefix'])){
	    			self::$prex = $config['connections']['mysql']['prefix'];
	    		}
	    	}
		}

		public static function log(Request $request,array $data = [],int $cate_id = 0): mixed
		{
			try{
				if(!$data){
					return false;
				}
				self::setConfig();

				if($cate_id && !isset($data['CateID'])){
					$data['CateID'] = $cate_id;
				}
				if(!isset($data['UserID'])){
					$uid = Token::getTokenData($request,'uid');
					if($uid && is_numeric($uid)){
						$data['UserID'] = $uid;
					}
				}
				if(!isset($data['CreateTime'])){
					$data['CreateTime'] = time();
				}
				if(!isset($data['CreateIP'])){
					$data['CreateIP'] = $request->getRealIp($safe_mode = true);
				}
				if(!isset($data['Status'])){
					$data['Status'] = 1;
				}

				$keys = [];
				$vals = [];
				$param = [];
				foreach($data as $k => $v){
					array_push($keys,"`{$k}`");
					array_push($vals,'?');
					array_push($param,$v);
				}
				$keys = \implode(',',$keys);
				$vals = "(".(\implode(',',$vals)).")";
				$sql = "INSERT INTO `".self::$prex.self::$tabname."` ({$keys}) VALUES {$vals}";
				// var_dump('record sql: '.$sql);
				$result = Db::insert($sql,$param);
				return $result !== false ? true : false;
			}catch(\Exception $e){
				return [
					'code' => $e->getCode() ? $e->getCode() : 1,
					'file' => $e->getFile(),
					'line' => $e->getLine(),
					'msg'  => $e->getMessage()
				];
			}
		}

		protected function getPageList(Request $request){
			try{
				$field = $this->getList($request,'field',true);
				$where = $this->getWhere($request);

				$cate_id = $request->input('cate_id',0);
				if($cate_id && $this->checkCateID($cate_id)){
					$this->title = $this->cate_value[$cate_id].'记录';
					array_push($where,[$this->table.'.CateID','=',$cate_id]);
				}

				$user_id = $request->input('user_id',0);
				if($user_id){
					array_push($where,[$this->table.'.UserID','=',$user_id]);
				}

				$source_id = $request->input('source_id',0);
				if($source_id){
					array_push($where,[$this->table.'.SourceID','=',$source_id]);
				}

				$rows = Db::table($this->table)
							->where($where)
							->count();
				[$offset,$limit] = $this->getLimit($request);
				$object = Db::table($this->table)
							->select(...$field)
							->where($where)
							->orderBy($this->table.'.ID','desc')
							->offset($offset)
							->limit($limit)
							->get();
				if($object){
					foreach($object as $k => $v){
						$object[$k]->cate_name = $this->cate_value[$object[$k]->cate_id];
						$object[$k]->create_time = $this->getDateTime($object[$k]->create_time);
					}
				}

				return ['rows' => $rows,'data' => $object];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		protected function getCateList(Request $request,$cate_id = 0){
			try{
				if(!$cate_id || !$this->checkCateID($cate_id)){
					return 100005;
				}
				$field = $this->getList($request,'field',true);
				$where = $this->getWhere($request);
				array_push($where,[$this->table.'.CateID','=',$cate_id]);

				$user_id = $request->input('user_id',0);
				if($user_id){
					array_push($where,[$this->table.'.UserID','=',$user_id]);
				}

				$rows = Db::table($this->table)
							->where($where)
							->count();
				[$offset,$limit] = $this->getLimit($request);
				$object = Db::table($this->table)
							->select(...$field)
							->where($where)
							->orderBy($this->table.'.ID','desc')
							->offset($offset)
							->limit($limit)
							->get();
				if($object){
					foreach($object as $k => $v){
						$object[$k]->create_time = $this->getDateTime($object[$k]->create_time);
					}
				}

				return ['rows' => $rows,'data' => $object];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		protected function getUserList(Request $request,$user_id = 0){
			try{
				if(!$user_id){
					return 100005;
				}
				$field = $this->getList($request,'field',true);
				$where = $this->getWhere($request);
				array_push($where,[$this->table.'.UserID','=',$user_id]);

				$cate_id = $request->input('cate_id',0);
				if($cate_id && $this->checkCateID($cate_id)){
					array_push($where,[$this->table.'.CateID','=',$cate_id]);
				}

				$rows = Db::table($this->table)
							->where($where)
							->count();
				[$offset,$limit] = $this->getLimit($request);
				$object = Db::table($this->table)
							->select(...$field)
							->where($where)
							->orderBy($this->table.'.ID','desc')
							->offset($offset)
							->limit($limit)
							->get();
				if($object){
					foreach($object as $k => $v){
						$object[$k]->create_time = $this->getDateTime($object[$k]->create_time);
					}
				}

				return ['rows' => $rows,'data' => $object];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		/**
		 * 获取积分记录
		 * @param  Request $request [description]
		 * @param  integer $user_id [description]
		 * @return [type]           [description]
		 */
		protected function getScoreList(Request $request,$user_id = 0){
			try{
				$field = $this->getList($request,'field',true);
				$where = $this->getWhere($request);
				array_push($where,[$this->table.'.CateID','=',1]);

				$user_id = $user_id ? $user_id : $request->input('user_id',0);
				if($user_id){
					array_push($where,[$this->table.'.UserID','=',$user_id]);
				}

				$rows = Db::table($this->table)
							->where($where)
							->count();
				[$offset,$limit] = $this->getLimit($request);
				$object = Db::table($this->table)
							->select(...$field)
							->where($where)
							->orderBy($this->table.'.ID','desc')
							->offset($offset)
							->limit($limit)
							->get();
				if($object){
					foreach($object as $k => $v){
						$object[$k]->create_time = $this->getDateTime($object[$k]->create_time);
					}
				}

				return ['rows' => $rows,'data' => $object];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		/**
		 * 获取金币记录
		 * @param  Request $request [description]
		 * @param  integer $user_id [description]
		 * @return [type]           [description]
		 */
		protected function getCoinList(Request $request,$user_id = 0){
			try{
				$field = $this->getList($request,'field',true);
				$where = $this->getWhere($request);
				array_push($where,[$this->table.'.CateID','=',2]);

				$user_id = $user_id ? $user_id : $request->input('user_id',0);
				if($user_id){
					array_push($where,[$this->table.'.UserID','=',$user_id]);
				}

				$rows = Db::table($this->table)
							->where($where)
							->count();
				[$offset,$limit] = $this->getLimit($request);
				$object = Db::table($this->table)
							->select(...$field)
							->where($where)
							->orderBy($this->table.'.ID','desc')
							->offset($offset)
							->limit($limit)
							->get();
				if($object){
					foreach($object as $k => $v){
						$object[$k]->create_time = $this->getDateTime($object[$k]->create_time);
					}
				}

				return ['rows' => $rows,'data' => $object];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		/**
		 * 获取签到记录
		 * @param  Request $request [description]
		 * @param  integer $user_id [description]
		 * @return [type]           [description]
		 */
		protected function getSignList(Request $request,$user_id = 0){
			try{
				$field = $this->getList($request,'field',true);
				$where = $this->getWhere($request);
				array_push($where,[$this->table.'.CateID','=',3]);

				$user_id = $user_id ? $user_id : $request->input('user_id',0);
				if($user_id){
					array_push($where,[$this->table.'.UserID','=',$user_id]);
				}

				$rows = Db::table($this->table)
							->where($where)
							->count();
				[$offset,$limit] = $this->getLimit($request);
				$object = Db::table($this->table)
							->select(...$field)
							->where($where)
							->orderBy($this->table.'.ID','desc')
							->offset($offset)
							->limit($limit)
							->get();
				if($object){
					foreach($object as $k => $v){
						$object[$k]->create_time = $this->getDateTime($object[$k]->create_time);
					}
				}

				return ['rows' => $rows,'data' => $object];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		/**
		 * 获取余额记录
		 * @param  Request $request [description]
		 * @param  integer $user_id [description]
		 * @return [type]           [description]
		 */
		protected function getBalanceList(Request $request,$user_id = 0){
			try{
				$field = $this->getList($request,'field',true);
				$where = $this->getWhere($request);
				array_push($where,[$this->table.'.CateID','=',4]);

				$user_id = $user_id ? $user_id : $request->input('user_id',0);
				if($user_id){
					array_push($where,[$this->table.'.UserID','=',$user_id]);
				}

				$rows = Db::table($this->table)
							->where($where)
							->count();
				[$offset,$limit] = $this->getLimit($request);
				$object = Db::table($this->table)
							->select(...$field)
							->where($where)
							->orderBy($this->table.'.ID','desc')
							->offset($offset)
							->limit($limit)
							->get();
				if($object){
					foreach($object as $k => $v){
						$object[$k]->create_time = $this->getDateTime($object[$k]->create_time);
					}
				}

				return ['rows' => $rows,'data' => $object];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		/**
		 * 获取订单记录
		 * @param  Request $request [description]
		 * @param  integer $user_id [description]
		 * @return [type]           [description]
		 */
		protected function getOrderList(Request $request,$user_id = 0){
			try{
				$field = $this->getList($request,'field',true);
				$where = $this->getWhere($request);
				array_push($where,[$this->table.'.CateID','=',5]);

				$user_id = $user_id ? $user_id : $request->input('user_id',0);
				if($user_id){
					array_push($where,[$this->table.'.UserID','=',$user_id]);
				}

				$rows = Db::table($this->table)
							->where($where)
							->count();
				[$offset,$limit] = $this->getLimit($request);
				$object = Db::table($this->table)
							->select(...$field)
							->where($where)
							->orderBy($this->table.'.ID','desc')
							->offset($offset)
							->limit($limit)
							->get();
				if($object){
					foreach($object as $k => $v){
						$object[$k]->create_time = $this->getDateTime($object[$k]->create_time);
					}
				}

				return ['rows' => $rows,'data' => $object];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		/**
		 * 获取短信记录
		 * @param  Request $request [description]
		 * @param  integer $user_id [description]
		 * @return [type]           [description]
		 */
		protected function getSmsList(Request $request,$user_id = 0){
			try{
				$field = $this->getList($request,'field',true);
				$where = $this->getWhere($request);
				array_push($where,[$this->table.'.CateID','=',6]);

				$user_id = $user_id ? $user_id : $request->input('user_id',0);
				if($user_id){
					array_push($where,[$this->table.'.UserID','=',$user_id]);
				}

				$rows = Db::table($this->table)
							->where($where)
							->count();
				[$offset,$limit] = $this->getLimit($request);
				$object = Db::table($this->table)
							->select(...$field)
							->where($where)
							->orderBy($this->table.'.ID','desc')
							->offset($offset)
							->limit($limit)
							->get();
				if($object){
					foreach($object as $k => $v){
						$object[$k]->create_time = $this->getDateTime($object[$k]->create_time);
					}
				}

				return ['rows' => $rows,'data' => $object];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		/**
		 * 获取邮件记录
		 * @param  Request $request [description]
		 * @param  integer $user_id [description]
		 * @return [type]           [description]
		 */
		protected function getEmailList(Request $request,$user_id = 0){
			try{
				$field = $this->getList($request,'field',true);
				$where = $this->getWhere($request);
				array_push($where,[$this->table.'.CateID','=',7]);

				$user_id = $user_id ? $user_id : $request->input('user_id',0);
				if($user_id){
					array_push($where,[$this->table.'.UserID','=',$user_id]);
				}

				$rows = Db::table($this->table)
							->where($where)
							->count();
				[$offset,$limit] = $this->getLimit($request);
				$object = Db::table($this->table)
							->select(...$field)
							->where($where)
							->orderBy($this->table.'.ID','desc')
							->offset($offset)
							->limit($limit)
							->get();
				if($object){
					foreach($object as $k => $v){
						$object[$k]->create_time = $this->getDateTime($object[$k]->create_time);
					}
				}

				return ['rows' => $rows,'data' => $object];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		/**
		 * 获取分享记录
		 * @param  Request $request [description]
		 * @param  integer $user_id [description]
		 * @return [type]           [description]
		 */
		protected function getShareList(Request $request,$user_id = 0){
			try{
				$field = $this->getList($request,'field',true);
				$where = $this->getWhere($request);
				array_push($where,[$this->table.'.CateID','=',8]);

				$user_id = $user_id ? $user_id : $request->input('user_id',0);
				if($user_id){
					array_push($where,[$this->table.'.UserID','=',$user_id]);
				}

				$rows = Db::table($this->table)
							->where($where)
							->count();
				[$offset,$limit] = $this->getLimit($request);
				$object = Db::table($this->table)
							->select(...$field)
							->where($where)
							->orderBy($this->table.'.ID','desc')
							->offset($offset)
							->limit($limit)
							->get();
				if($object){
					foreach($object as $k => $v){
						$object[$k]->create_time = $this->getDateTime($object[$k]->create_time);
					}
				}

				return ['rows' => $rows,'data' => $object];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		/**
		 * 获取充值记录
		 * @param  Request $request [description]
		 * @param  integer $user_id [description]
		 * @return [type]           [description]
		 */
		protected function getChargeList(Request $request,$user_id = 0){
			try{
				$field = $this->getList($request,'field',true);
				$where = $this->getWhere($request);
				array_push($where,[$this->table.'.CateID','=',9]);

				$user_id = $user_id ? $user_id : $request->input('user_id',0);
				if($user_id){
					array_push($where,[$this->table.'.UserID','=',$user_id]);
				}

				$rows = Db::table($this->table)
							->where($where)
							->count();
				[$offset,$limit] = $this->getLimit($request);
				$object = Db::table($this->table)
							->select(...$field)
							->where($where)
							->orderBy($this->table.'.ID','desc')
							->offset($offset)
							->limit($limit)
							->get();
				if($object){
					foreach($object as $k => $v){
						$object[$k]->create_time = $this->getDateTime($object[$k]->create_time);
					}
				}

				return ['rows' => $rows,'data' => $object];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		/**
		 * 获取分佣记录
		 * @param  Request $request [description]
		 * @param  integer $user_id [description]
		 * @return [type]           [description]
		 */
		protected function getCommentList(Request $request,$user_id = 0){
			try{
				$field = $this->getList($request,'field',true);
				$where = $this->getWhere($request);
				array_push($where,[$this->table.'.CateID','=',10]);

				$user_id = $user_id ? $user_id : $request->input('user_id',0);
				if($user_id){
					array_push($where,[$this->table.'.UserID','=',$user_id]);
				}

				$rows = Db::table($this->table)
							->where($where)
							->count();
				[$offset,$limit] = $this->getLimit($request);
				$object = Db::table($this->table)
							->select(...$field)
							->where($where)
							->orderBy($this->table.'.ID','desc')
							->offset($offset)
							->limit($limit)
							->get();
				if($object){
					foreach($object as $k => $v){
						$object[$k]->create_time = $this->getDateTime($object[$k]->create_time);
					}
				}

				return ['rows' => $rows,'data' => $object];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		/**
		 * 获取提现记录
		 * @param  Request $request [description]
		 * @param  integer $user_id [description]
		 * @return [type]           [description]
		 */
		protected function getCashList(Request $request,$user_id = 0){
			try{
				$field = $this->getList($request,'field',true);
				$where = $this->getWhere($request);
				array_push($where,[$this->table.'.CateID','=',11]);

				$user_id = $user_id ? $user_id : $request->input('user_id',0);
				if($user_id){
					array_push($where,[$this->table.'.UserID','=',$user_id]);
				}

				$rows = Db::table($this->table)
							->where($where)
							->count();
				[$offset,$limit] = $this->getLimit($request);
				$object = Db::table($this->table)
							->select(...$field)
							->where($where)
							->orderBy($this->table.'.ID','desc')
							->offset($offset)
							->limit($limit)
							->get();
				if($object){
					foreach($object as $k => $v){
						$object[$k]->create_time = $this->getDateTime($object[$k]->create_time);
					}
				}

				return ['rows' => $rows,'data' => $object];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		/**
		 * 获取足迹记录
		 * @param  Request $request [description]
		 * @param  integer $user_id [description]
		 * @return [type]           [description]
		 */
		protected function getTrackList(Request $request,$user_id = 0){
			try{
				$field = $this->getList($request,'field',true);
				$where = $this->getWhere($request);
				array_push($where,[$this->table.'.CateID','=',12]);

				$user_id = $user_id ? $user_id : $request->input('user_id',0);
				if($user_id){
					array_push($where,[$this->table.'.UserID','=',$user_id]);
				}

				$rows = Db::table($this->table)
							->where($where)
							->count();
				[$offset,$limit] = $this->getLimit($request);
				$object = Db::table($this->table)
							->select(...$field)
							->where($where)
							->orderBy($this->table.'.ID','desc')
							->offset($offset)
							->limit($limit)
							->get();
				if($object){
					foreach($object as $k => $v){
						$object[$k]->create_time = $this->getDateTime($object[$k]->create_time);
					}
				}

				return ['rows' => $rows,'data' => $object];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		/**
		 * 获取收藏记录
		 * @param  Request $request [description]
		 * @param  integer $user_id [description]
		 * @return [type]           [description]
		 */
		protected function getCollectList(Request $request,$user_id = 0){
			try{
				$field = $this->getList($request,'field',true);
				$where = $this->getWhere($request);
				array_push($where,[$this->table.'.CateID','=',13]);

				$user_id = $user_id ? $user_id : $request->input('user_id',0);
				if($user_id){
					array_push($where,[$this->table.'.UserID','=',$user_id]);
				}

				$rows = Db::table($this->table)
							->where($where)
							->count();
				[$offset,$limit] = $this->getLimit($request);
				$object = Db::table($this->table)
							->select(...$field)
							->where($where)
							->orderBy($this->table.'.ID','desc')
							->offset($offset)
							->limit($limit)
							->get();
				if($object){
					foreach($object as $k => $v){
						$object[$k]->create_time = $this->getDateTime($object[$k]->create_time);
					}
				}

				return ['rows' => $rows,'data' => $object];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		/**
		 * 获取优惠券记录
		 * @param  Request $request [description]
		 * @param  integer $user_id [description]
		 * @return [type]           [description]
		 */
		protected function getCouponList(Request $request,$user_id = 0){
			try{
				$field = $this->getList($request,'field',true);
				$where = $this->getWhere($request);
				array_push($where,[$this->table.'.CateID','=',14]);

				$user_id = $user_id ? $user_id : $request->input('user_id',0);
				if($user_id){
					array_push($where,[$this->table.'.UserID','=',$user_id]);
				}

				$rows = Db::table($this->table)
							->where($where)
							->count();
				[$offset,$limit] = $this->getLimit($request);
				$object = Db::table($this->table)
							->select(...$field)
							->where($where)
							->orderBy($this->table.'.ID','desc')
							->offset($offset)
							->limit($limit)
							->get();
				if($object){
					foreach($object as $k => $v){
						$object[$k]->create_time = $this->getDateTime($object[$k]->create_time);
					}
				}

				return ['rows' => $rows,'data' => $object];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		private function checkCateID($cate_id){
			$vals = array_values($this->cate_value);
			if(in_array($cate_id,$vals)){
				return true;
			}
			return false;
		}

		protected function getMapList(Request $request): array
		{
			return [
				['type'=>'id','label'=>'ID','prop'=>'id'],
				['type'=>'varchar','label'=>'类别','prop'=>'cate_name'],
				['type'=>'varchar','label'=>'用户','prop'=>'realname'],
				['type'=>'varchar','label'=>'来源','prop'=>'source_name'],
				['type'=>'varchar','label'=>'金额','prop'=>'value','prefix'=>'¥'],
				['type'=>'varchar','label'=>'备注','prop'=>'content'],
				['type'=>'varchar','label'=>'创建时间','prop'=>'create_time'],
			];
		}
	}
?>