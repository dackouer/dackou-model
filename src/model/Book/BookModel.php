<?php
	namespace dackou\model\Book;

	use support\Request;
	use support\Db;

	class BookModel extends \dackou\Model{
		protected $table = 'Book';
		protected $title = '通讯录';
		protected $status_value = [
			['label'=>'拉黑','value'=>-1],
			['label'=>'取关','value'=>0],
			['label'=>'关注','value'=>1],
			['label'=>'互关','value'=>2],
		];

        protected function getPageList(Request $request){
            $field = $this->getList($request,'field',true);
            array_push($field,"user.RealName as realname");
            array_push($field,"friend.RealName as friendname");

            $where = $this->getWhere($request);
            $user_id = $request->input('user_id',0);
            if($user_id){
            	array_push($where,[$this->table.'.UserID','=',$user_id]);
            }

            // var_dump($where);
            $rows = Db::table($this->table)
                        ->join('user','UserID','=','user.AccountID')
                        ->join('user as friend','FriendID','=','friend.AccountID')
                        ->where($where)
                        ->count();
            $limit = $this->getLimit($request);

            $object = Db::table($this->table)
                        ->join('user','UserID','=','user.AccountID')
                        ->join('user as friend','FriendID','=','friend.AccountID')
                        ->select(...$field)
                        ->where($where)
                        ->offset($limit[0])
                        ->limit($limit[1])
                        ->get();
            if($object){
                foreach($object as $k => $v){
                    $object[$k]->create_time = $this->getDateTime($object[$k]->create_time);
                }
            }

            return ['rows' => $rows,'data' => $object];
        }

        private function updateFrient(Request $request,$data){
        	$user_id = $data['user_id'];
        	$frient_id = $data['frient_id'];
        	var_dump('update frient user_id: '.$user_id);
        	var_dump('update frient frient_id: '.$frient_id);
        	return Db::table($this->table)->where([['UserID','=',$friend_id],['FriendID','=',$user_id]])->update(['Status' => 2]);
        }

        protected function setExcute(Request $request,$data,$type = false){
        	if(!$type){
        		Db::table('user')->where('AccountID',$data['frient_id'])->increment('Follower');
        		if($data['status'] === 2){
        			$this->updateFrient($request,$data);
        		}
        	}elseif($type === 'unfollow'){
        		$object = $this->getList($request,'friend',$data['friend_id'],$data['user_id']);
        		if($object && is_object($object) && $object->status === 2){
        			$this->updateData($request,['Status'=>1],$object->id);
        		}
        	}

        	return true;
        }

		protected function validate(Request $request,$id = 0,$obj = null){
			$action_value = $request->input('action','');

			$user_id = $request->post('user_id',0);
			$friend_id = $request->post('friend_id',0);

			if(!$user_id || !is_numeric($user_id) || $user_id < 0){
				return '无效的关注用户';
			}

			$_class_name = $this->getClassName('User');
			$service = new $_class_name();
			$user = $service->getList($request,$user_id);
			if(!$user || !is_object($user)){
				return '无效的关注用户';
			}

			if(!$friend_id || !is_numeric($friend_id) || $friend_id < 0){
				return '无效的被关注用户';
			}

			$_class_name = $this->getClassName('User');
			$service = new $_class_name();
			$friend = $service->getList($request,$friend_id);
			if(!$friend || !is_object($friend)){
				return '无效的被关注用户';
			}

			if($action_value === 'unfollow'){
				$obj = $this->getList($request,'friend',$user_id,$friend_id);
				if(!$obj || !is_object($obj)){
					return '非法操作';
				}
				
				if($object->status === 0){
					return '已取消关注,无需重复操作';
				}
			}


			$obj = $this->getList($request,'friend',$user_id,$friend_id);
			if($obj && is_object($obj)){
				if($obj->status > 0){
					return '已关注，无需重复关注';
				}elseif($obj->status < 0){
					return '关注无效';
				}else{
					$object = $this->getList($request,'friend',$friend_id,$user_id);
					if($obj && is_object($obj) && $obj->status === 1){
						$data['status'] = 2;
					}else{
						$data['status'] = 1;
					}
				}
			}

			$data['user_id'] = $user_id;
			$data['friend_id'] = $friend_id;
			if(!$id){
				$data['status'] = 1;
			}else{
				if($action_value === 'unfollow'){
					$data['status'] = 0;
				}
			}

			return $data;
		}

		protected function getFriendList(Request $request,$user_id,$friend_id = 0){
			$field = $this->getList($request,'field');
			$where = $this->getWhere($request);
			array_push($where,['UserID','=',$user_id]);

			if($friend_id){
				array_push($where,['FriendID','=',$friend_id]);

				$object = Db::table($this->table)
						->select(...$field)
						->where($where)
						->first();
				return $object;
			}

			$rows = Db::table($this->table)->where($where)->count();
			[$offset,$limit] = $this->getLimit($request);
			$object = Db::table($this->table)
					->select(...$field)
					->where($where)
					->offset($offset)
					->limit($limit)
					->get();

			return ['rows'=>$rows,'data'=>$object];
		}

		protected function getActionList(Request $request,$id = 0): array
		{
			if($id){
				$data = $this->getList($request,$id);
			}

			$_class_name = $this->getClassName('User');
			$service = new $_class_name();
			$user = $service->getList($request,'option','arters');
			$friend = $service->getList($request,'option','members');

			$action = [
				['type'=>'select','label'=>'技师','prop'=>'user_id','value'=>$data->user_id ?? '','children'=>$user,'required'=>true],
				['type'=>'select','label'=>'会员','prop'=>'friend_id','value'=>$data->friend_id ?? '','children'=>$friend,'required'=>true],
			];

			if($id){
				array_push($action,['type'=>'radio-group','label'=>'状态','prop'=>'status','value'=>$data->status,'children'=>$this->status_value]);
			}

			return $action;
		}

		protected function getMapList(Request $request): array
		{
			return [
				['type'=>'varchar','label'=>'ID','prop'=>'id'],
				['type'=>'varchar','label'=>'关注者','prop'=>'realname'],
				['type'=>'varchar','label'=>'关注者ID','prop'=>'user_id'],
				['type'=>'varchar','label'=>'被关注者','prop'=>'friendname'],
				['type'=>'varchar','label'=>'被关注者ID','prop'=>'friend_id'],
				['type'=>'map','label'=>'状态','prop'=>'status','data'=>$this->status_value],
				['type'=>'varchar','label'=>'创建时间','prop'=>'create_time'],
			];
		}
	}
?>