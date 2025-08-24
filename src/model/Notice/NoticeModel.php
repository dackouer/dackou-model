<?php
	namespace dackou\model\Notice;

	use support\Request;
	use support\Db;

	class NoticeModel extends \dackou\Model{
		protected $table = 'Notice';
		protected $title = '通知';
		protected $orderBy = 'desc';
		protected $cate_value = [1=>'通知',2=>'公告',0=>'其它'];

		protected function getUserList(Request $request,$user_id = 0){
			try{
				$user_id = $user_id ? $user_id : $request->input('user_id',0);
				if(!$user_id){
					return [];
				}

				$group_id = [];
				$service = new NoticeDetailModel();
				$result = $service->getList($request,'user',$user_id);
				if($result){
					foreach($result as $res){
						array_push($group_id,(int)$res->notice_id);
					}
				}

				$field = $this->getList($request,'field');
				$where = $this->getWhere($request);

				$object = Db::table($this->table)
							->select(...$field)
							->where($where);
				if($group_id){
					$object = $object->whereNotIn('ID',$group_id);
				}
				$object = $object->get();

				return $object;
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		protected function setExcute(Request $request,$data,$flag = false){
			if(!$flag){
				$cate_id = $data['cate_id'];
				if($cate_id){
					$role_id = $data['role_id'];
					if($role_id && is_array($role_id)){
						// var_dump('开始推送'.$this->cate_value[$cate_id]);
						// var_dump($role_id);
					}
				}
			}

			return true;
		}

		protected function validate(Request $request,$id = 0,$obj = null){
			$cate_id = $request->post('cate_id',1);
			$title = trim($request->post('title',''));
			$role_id = $request->post('role_id',[]);
			$content = trim($request->post('content',''));

			if(!$title){
				return '标题不能为空';
			}

			if($this->checkExists(['Title'=>$title],$id)){
				return '标题已存在,请重新输入';
			}

			if(!$content){
				return '内容不能为空';
			}

			$data['cate_id'] = $cate_id;
			$data['title'] = $title;
			$data['role_id'] = $cate_id ? $role_id : [];
			$data['content'] = $content;

			return $data;
		}

		protected function getActionList(Request $request,$id = 0): array
		{
			if($id){
				$data = $this->getList($request,$id);
			}

			$cate = $this->getFieldOption($this->cate_value);

			$_class_name = $this->getClassName('Role');
			$service = new $_class_name();
			$role = $service->getList($request,'option',[13,14,15]);

			$action = [
				['type'=>'radio-group','label'=>'类型','prop'=>'cate_id','value'=>$data->cate_id ?? 1,'children'=>$cate],
				['type'=>'input','label'=>'标题','prop'=>'title','value'=>$data->title ?? '','rules'=>['required'=>true,'message'=>'标题不能为空']],
				['type'=>'cascader','label'=>'推送用户','prop'=>'role_id','value'=>$data->role_id ?? [],'attrs'=>['placeholder'=>'选择推送用户','props'=>['multiple'=>true],'options'=>$role]],
				['type'=>'input','label'=>'内容','prop'=>'content','value'=>$data->content ?? '','attrs'=>['type'=>'textarea','rows'=>3],'rules'=>['required'=>true,'message'=>'内容不能为空']],
			];

			return $action;
		}

		protected function getMapList(Request $request): array
		{
			return [
				['type'=>'id','label'=>'ID','prop'=>'id'],
				['type'=>'map','label'=>'类型','prop'=>'cate_id','data'=>$this->getFieldOption($this->cate_value)],
				['type'=>'varchar','label'=>'标题','prop'=>'title'],
				['type'=>'varchar','label'=>'创建时间','prop'=>'create_time'],
			];
		}
	}
?>