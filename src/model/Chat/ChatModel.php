<?php
	namespace dackou\model\Chat;

	use support\Request;
	use support\Db;

	class ChatModel extends \dackou\Model{
		protected $table = 'Chat';
		protected $title = '聊天';
		protected $cate_value = [
			['label'=>'私聊','value'=>1],
			['label'=>'消息','value'=>2],
			['label'=>'群聊','value'=>3],
		];

		protected function getPageList(Request $request){
			$field = $this->getList($request,'field');
			var_dump('field: ',$field);
			$where = $this->getWhere($request);

			$cate_id = $request->input('cate_id',0);
			if($cate_id && is_numeric($cate_id) && in_array($cate_id,[1,2,3])){
				array_push($where,[$this->table.'.CateID','=',$cate_id]);
			}

			$client_id = $request->input('client_id',0);
			if($client_id && is_numeric($client_id) && $client_id > 0){
				array_push($where,[$this->table.'.ClientID','=',$client_id]);
			}

			$user_id = $request->input('user_id',0);
			if($user_id && is_numeric($user_id) && $user_id > 0){
				array_push($where,[$this->table.'.UserID','=',$user_id]);
			}

			$keyword = trim($request->input('keyword',''));
			if($keyword){
				array_push($where,[$this->table.'.Content','like','%'.$keyword.'%']);
			}

			$rows = Db::table($this->table)->where($where)->count();

			[$offset,$limit] = $this->getLimit($request);

			$object = Db::table($this->table)
						->select(...$field)
						->where($where)
						->orderBy($this->table.'.ID','desc')
						->offset($offset)
						->limit($limit)
						->get();

			return ['rows'=>$rows,'data'=>$object];
		}

		protected function validate(Request $request,$id = 0,$obj = null){
			$cate_id = $request->post('cate_id',0);
			$cate_name = trim($request->post('cate_name',''));
			$client_id = $request->post('client_id',0);
			$client_name = trim($request->post('client_name',''));
			$avatar = trim($request->post('avatar',''));
			$role_id = $request->post('role_id',0);
			$role_name = trim($request->post('role_name',''));
			$role_pic = trim($request->post('role_pic',''));
			$user_id = $request->post('user_id',0);
			$nickname = trim($request->post('nickname',''));
			$face = trim($request->post('face',''));
			$hand_id = $request->post('hand_id',0);
			$hand_name = trim($request->post('hand_name',''));
			$hand_pic = trim($request->post('hand_pic',''));
			$content = trim($request->post('content',''));

			if(!$cate_id || !is_numeric($cate_id) || !in_array($cate_id,[1,2,3])){
				return '无效的消息类型';
			}

			if(!$client_id || !is_numeric($client_id) || $client_id < 0){
				return '无效的发送人';
			}

			$_class_name = $this->getClassName('User');
			$service = new $_class_name();
			$client = $service->getList($request,$client_id);
			if(!$client_id || !is_object($client_id)){
				return '无效的发送人';
			}
			
			if($client->status < 0){
				return '无效的发送人ID';
			}
			if($client->status === 0){
				return '发送人未激活';
			}
			if($client->status > 1){
				return '发送人被锁定，请联系管理员';
			}
			if($client->status !== 1){
				return '发送人状态无效，请联系管理员';
			}

			if(!$user_id || !is_numeric($user_id) || $user_id < 0){
				return '无效的接收人';
			}

			$_class_name = $this->getClassName('User');
			$service = new $_class_name();
			$user = $service->getList($request,$user_id);
			if(!$user_id || !is_object($user_id)){
				return '无效的接收人';
			}
			
			if($user->status < 0){
				return '无效的接收人ID';
			}
			if($user->status === 0){
				return '接收人未激活';
			}
			if($user->status > 1){
				return '接收人被锁定，请联系管理员';
			}
			if($user->status !== 1){
				return '接收人状态无效，请联系管理员';
			}

			$data['cate_id'] = $cate_id;
			$data['cate_name'] = $cate_name;
			$data['client_id'] = $client_id;
			$data['client_name'] = $client_name;
			$data['avatar'] = $avatar;
			$data['role_id'] = $role_id;
			$data['role_name'] = $role_name;
			$data['role_pic'] = $role_pic;
			$data['user_id'] = $user_id;
			$data['nickname'] = $nickname;
			$data['face'] = $face;
			$data['hand_id'] = $hand_id;
			$data['hand_name'] = $hand_name;
			$data['hand_pic'] = $hand_pic;
			$data['content'] = $content;

			return $data;
		}
	}
?>