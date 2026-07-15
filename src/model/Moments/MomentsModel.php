<?php
	namespace dackou\model\Moments;

	use support\Request;
	use support\Db;

	class MomentsModel extends \dackou\Model{
		protected $table = 'Moments';
		protected $title = '朋友圈';
		protected $cate_value = [
			['label'=>'个人','value'=>1],
			['label'=>'门店','value'=>2],
			['label'=>'商户','value'=>3],
		];
		protected $allow_value = [
			['label'=>'公开','desc'=>'所有人可见。','value'=>1],
			['label'=>'隐私','desc'=>'仅自己可见。','value'=>2],
			['label'=>'标签可见','desc'=>'仅所选标签人群可见。','value'=>3],
			['label'=>'指定人可见','desc'=>'仅所选账号可见。','value'=>4],
			['label'=>'仅关注可见','desc'=>'兼容旧关注规则，只有关注当前发布身份的人可见','value'=>5],
			['label'=>'仅好友可见','desc'=>'兼容旧好友规则，仅双方互相关注的好友可见','value'=>6],
		];
		protected $comment_value = [
			['label'=>'关闭','desc'=>'关闭评论','value'=>0],
			['label'=>'任何人','desc'=>'所有能看到这条动态的人都可以评论','value'=>1],
			['label'=>'仅好友','desc'=>'只有双方互相关注的好友可以评论','value'=>2],
		];

		protected function getPageList(Request $request){
			$field = $this->getList($request,'field',true);
			array_push($field,'NickName as nickname');
			array_push($field,'RealName as realname');
			array_push($field,'role.Title as rolename');
			array_push($field,'role.Pic as role_pic');
			$where = $this->getWhere($request);

			$user_id = $request->input('user_id',0);
			if($user_id){
				array_push($where,['UserID','=',$user_id]);
			}

			$rows = Db::table($this->table)
						->join('user','UserID','=','user.AccountID')
						->join('role','RoleID','=','role.ID')
						->where($where)
						->count();

			[$offset,$limit] = $this->getLimit($request);

			$object = Db::table($this->table)
						->join('user','UserID','=','user.AccountID')
						->join('role','RoleID','=','role.ID')
						->select(...$field)
						->where($where)
						->orderBy($this->table.'.ID','desc')
						->offset($offset)
						->limit($limit)
						->get();
			if($object){
				foreach($object as $k => $v){
					$object[$k]->time = $this->getDiffTime($object[$k]->create_time,true);
					$object[$k]->pic = '';
					if($object[$k]->picture){
						$object[$k]->picture = $this->getDecodeData($object[$k]->picture);
						$object[$k]->pic = $object[$k]->picture[0] ?? '';
					}
					$object[$k]->create_time = $this->getDateTime($object[$k]->create_time);
				}
			}

			return ['rows' => $rows,'data' => $object];
		}

		protected function getListById(Request $request,$id = 0){
			if(!$id || !is_numeric($id) || !$id < 0){
				return 100007;
			}

			$field = $this->getList($request,'field',true);
			array_push($field,'NickName as nickname');
			array_push($field,'RealName as realname');
			array_push($field,'role.Title as rolename');
			array_push($field,'role.Pic as role_pic');
			$where = $this->getWhere($request);
			array_push($where,[$this->table.'.ID','=',$id]);

			$object = Db::table($this->table)
						->join('user','UserID','=','user.AccountID')
						->join('role','RoleID','=','role.ID')
						->select(...$field)
						->where($where)
						->first();
			if($object){
				$object->time = $this->getDiffTime($object->create_time,true);
				if($object->picture){
					$object->picture = $this->getDecodeData($object->picture);
				}
				if($object->reminder){
					$object->reminder = $this->getDecodeData($object->reminder);
				}
				if($object->allow_list){
					$object->allow_list = $this->getDecodeData($object->allow_list);
				}
			}

			return $object;
		}

		protected function getStoreList(Request $request,$store_id = 0){
			if(!$store_id || !is_numeric($store_id) || $store_id < 0){
				return [];
			}

			$_class_name = $this->getClassName('StoreUser');
			$service = new $_class_name();
			$arters = $service->getList($request,'arters',$store_id);
			$arters = $arters['data'] ?? $arters;
			if(!$arters){
				return [];
			}

			$arterList = [];
			foreach($arters as $item){
				array_push($arterList,$item->user_id);
			}

			if(!$arterList){
				return [];
			}

			$field = $this->getList($request,'field',true);
			array_push($field,'NickName as nickname');
			array_push($field,'RealName as realname');
			array_push($field,'role.Title as rolename');
			array_push($field,'role.Pic as role_pic');
			$where = $this->getWhere($request);

			$rows = Db::table($this->table)
						->join('user','UserID','=','user.AccountID')
						->join('role','RoleID','=','role.ID')
						->where($where)
						->whereIn($this->table.'.UserID',$arterList)
						->count();

			[$offset,$limit] = $this->getLimit($request);

			$object = Db::table($this->table)
						->join('user','UserID','=','user.AccountID')
						->join('role','RoleID','=','role.ID')
						->select(...$field)
						->where($where)
						->whereIn($this->table.'.UserID',$arterList)
						->orderBy($this->table.'.ID','desc')
						->offset($offset)
						->limit($limit)
						->get();
			if($object){
				foreach($object as $k => $v){
					$object[$k]->time = $this->getDiffTime($object[$k]->create_time,true);
					$object[$k]->pic = '';
					if($object[$k]->picture){
						$object[$k]->picture = $this->getDecodeData($object[$k]->picture);
						$object[$k]->pic = $object[$k]->picture[0] ?? '';
					}
					$object[$k]->create_time = $this->getDateTime($object[$k]->create_time);
				}
			}

			return ['rows' => $rows,'data' => $object];
		}

		// 获取某个用户的朋友圈
		protected function getUserList(Request $request,$user_id = 0){
			if(!$user_id || !is_numeric($user_id) || !$user_id < 0){
				return 100007;
			}

			$field = $this->getList($request,'field',true);
			array_push($field,'NickName as nickname');
			array_push($field,'RealName as realname');
			array_push($field,'user.IsAuth as is_auth');
			array_push($field,'role.Title as rolename');
			array_push($field,'role.Pic as role_pic');
			$where = $this->getWhere($request);
			array_push($where,['UserID','=',$user_id]);

			$rows = Db::table($this->table)
						->join('user','UserID','=','user.AccountID')
						->join('role','RoleID','=','role.ID')
						->where($where)
						->count();

			[$offset,$limit] = $this->getLimit($request);

			$object = Db::table($this->table)
						->join('user','UserID','=','user.AccountID')
						->join('role','RoleID','=','role.ID')
						->select(...$field)
						->where($where)
						->orderBy($this->table.'.ID','desc')
						->offset($offset)
						->limit($limit)
						->get();
			if($object){
				foreach($object as $k => $v){
					$object[$k]->time = $this->getDiffTime($object[$k]->create_time,true);
					$object[$k]->pic = '';
					if($object[$k]->picture){
						$object[$k]->picture = $this->getDecodeData($object[$k]->picture);
						$object[$k]->pic = $object[$k]->picture[0] ?? '';
					}
					$object[$k]->create_time = $this->getDateTime($object[$k]->create_time);
				}
			}

			return ['rows' => $rows,'data' => $object];
		}

		// 获取某个用户好友的朋友圈
		protected function getFriendList(Request $request,$user_id = 0){
			if(!$user_id || !is_numeric($user_id) || !$user_id < 0){
				return 100007;
			}

			$whereIn = [];

			$_class_name = $this->getClassName('Book');
			if($_class_name){
				$service = new $_class_name();
				$user = $service->getList($request,'friend',$user_id);
				if($user){
					$book = $user['data'] ?? $user;
					if($book){
						foreach($book as $item){
							array_push($whereIn,$item->friend_id);
						}
					}
				}
			}
			
			if(!$whereIn){
				return [];
			}

			$field = $this->getList($request,'field',true);
			array_push($field,'NickName as nickname');
			array_push($field,'RealName as realname');
			array_push($field,'user.IsAuth as is_auth');
			array_push($field,'role.Title as rolename');
			array_push($field,'role.Pic as role_pic');
			$where = $this->getWhere($request);


			$rows = Db::table($this->table)
						->join('user','UserID','=','user.AccountID')
						->join('role','RoleID','=','role.ID')
						->where($where)
						->whereIn($this->table.'.UserID',$whereIn)
						->count();

			[$offset,$limit] = $this->getLimit($request);

			$object = Db::table($this->table)
						->join('user','UserID','=','user.AccountID')
						->join('role','RoleID','=','role.ID')
						->select(...$field)
						->where($where)
						->whereIn($this->table.'.UserID',$whereIn)
						->orderBy($this->table.'.ID','desc')
						->offset($offset)
						->limit($limit)
						->get();

			if($object){
				foreach($object as $k => $v){
					
					$object[$k]->time = $this->getDiffTime($object[$k]->create_time,true);
					$object[$k]->pic = '';
					if($object[$k]->picture){
						$object[$k]->picture = $this->getDecodeData($object[$k]->picture);
						$object[$k]->pic = $object[$k]->picture[0] ?? '';
					}
					$object[$k]->create_time = $this->getDateTime($object[$k]->create_time);
				}
			}
			
			return ['rows' => $rows,'data' => $object];
		}

		// 获取某个用户附近的朋友圈
		protected function getNearbyList(Request $request,$user_id = 0){
			if(!$user_id || !is_numeric($user_id) || !$user_id < 0){
				return 100007;
			}

			$whereIn = [];

			if(!$whereIn){
				return [];
			}
			
			$field = $this->getList($request,'field',true);
			array_push($field,'NickName as nickname');
			array_push($field,'RealName as realname');
			array_push($field,'user.IsAuth as is_auth');
			array_push($field,'role.Title as rolename');
			array_push($field,'role.Pic as role_pic');
			$where = $this->getWhere($request);

			$rows = Db::table($this->table)
						->join('user','UserID','=','user.AccountID')
						->join('role','RoleID','=','role.ID')
						->where($where)
						->whereIn($this->table.'.UserID',$whereIn)
						->count();

			[$offset,$limit] = $this->getLimit($request);

			$object = Db::table($this->table)
						->join('user','UserID','=','user.AccountID')
						->join('role','RoleID','=','role.ID')
						->select(...$field)
						->where($where)
						->whereIn($this->table.'.UserID',$whereIn)
						->orderBy($this->table.'.ID','desc')
						->offset($offset)
						->limit($limit)
						->get();
			if($object){
				foreach($object as $k => $v){
					$object[$k]->time = $this->getDiffTime($object[$k]->create_time,true);
					$object[$k]->pic = '';
					if($object[$k]->picture){
						$object[$k]->picture = $this->getDecodeData($object[$k]->picture);
						$object[$k]->pic = $object[$k]->picture[0] ?? '';
					}
					$object[$k]->create_time = $this->getDateTime($object[$k]->create_time);
				}
			}

			return ['rows' => $rows,'data' => $object];
		}

		protected function validate(Request $request,$id = 0,$obj = null){
			$cate_id = $request->post('cate_id',1);
			$user_id = $request->post('user_id',0);
			$picture = $request->post('picture',[]);
			$content = trim($request->post('content',''));
			$is_comment = $request->post('is_comment',0);

			if(!$user_id || !is_numeric($user_id) || $user_id < 0){
				return '无效的用户';
			}

			$_class_name = $this->getClassName('User');
			$service = new $_class_name();
			$user = $service->getList($request,$user_id);
			if(!$user || !is_object($user)){
				return '无效的用户';
			}

			if(!$content){
				return '内容不能为空';
			}

			if(!in_array($is_comment,[0,1])){
				return '无效的开启评论值';
			}

			$data['cate_id'] = $cate_id;
			$data['user_id'] = $user_id;
			$data['picture'] = $picture;
			$data['content'] = $content;
			$data['is_comment'] = $is_comment;

			return $data;
		}

		protected function getActionList(Request $request,$id = 0): array
		{
			if($id){
				$data = $this->getList($request,$id);
			}

			$_class_name = $this->getClassName('User');
			$service = new $_class_name();
			$arter = $service->getList($request,'option','arters');

			$action = [
				// ['type'=>'radio-group','label'=>'类别','prop'=>'cate_id','value'=>$cate_id ?? 1,'children'=>$this->cate_value],
				['type'=>'select','label'=>'用户','prop'=>'user_id','value'=>$data->user_id ?? 0,'children'=>$arter],
				['type'=>'upload','label'=>'图片','prop'=>'picture','value'=>$data->picture ?? [],'attrs'=>$this->getUploadOptions('card',5)],
				['type'=>'input','label'=>'内容','prop'=>'content','value'=>$data->content ?? '','attrs'=>['type'=>'textarea','rows'=>5]],
				['type'=>'switch','label'=>'开启评论','prop'=>'is_comment','value'=>$data->is_comment ?? 0]
			];

			return $action;
		}

		protected function getMapList(Request $request): array
		{
			return [
				['type'=>'varchar','label'=>'ID','prop'=>'id'],
				['type'=>'varchar','label'=>'用户','prop'=>'nickname'],
				['type'=>'varchar','label'=>'角色','prop'=>'rolename'],
				['type'=>'img','label'=>'图片','prop'=>'pic'],
				['type'=>'varchar','label'=>'内容','prop'=>'content','width'=>300],
				['type'=>'varchar','label'=>'位置','prop'=>'address','width'=>200],
				['type'=>'varchar','label'=>'提醒谁看','prop'=>'reminder'],
				['type'=>'map','label'=>'谁可以看','prop'=>'allow','data'=>$this->allow_value],
				['type'=>'map','label'=>'允许评论','prop'=>'is_comment','data'=>$this->comment_value],
				['type'=>'number','label'=>'评论数','prop'=>'comments','suffix'=>'条'],
				['type'=>'map','label'=>'状态','prop'=>'status'],
				['type'=>'varchar','label'=>'发布时间','prop'=>'create_time'],
			];
		}
	}
?>