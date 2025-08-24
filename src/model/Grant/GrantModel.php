<?php
	namespace dackou\model\Grant;

	use support\Request;
	use support\Db;
	use dackou\model\Menu\MenuModel;
	use dackou\model\Role\RoleModel;

	class GrantModel extends \dackou\Model{
		protected $table = 'Grant';
		protected $title = '授权';
    	protected $truncate = true;

    	/**
    	 * [getAllList description]
    	 * @param  Request $request [description]
    	 * @return [type]           [description]
    	 */
    	protected function getAllList(Request $request){
    		try{
    			$role_id = $request->input('role_id',0);
    			$field = $this->getList($request,'field');
    			$object = Db::table($this->table)
    						->select(...$field)
    						->get();
    			return $object;
    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
    	}

		protected function getDataList(Request $request,$role_id,$menu_id){
			try{
				$field = $this->getList($request,'field');
				$where = [
					['RoleID','=',$role_id],
					['MenuID','=',$menu_id]
				];
				$object = Db::table($this->table)
							->select(...$field)
							->where($where)
							->first();
				return $object ? $object : [];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		protected function getRoleList(Request $request,$role_id){
			try{
				$field = $this->getList($request,'field');
				$where = [['RoleID','=',$role_id]];

				$object = Db::table($this->table)
							->select(...$field)
							->where($where)
							->get();

				return $object ?: [];
			}catch(\Exception $e){
				return [];
			}
		}

		private function checkValue(Request $request,$res,$data){
			$flag = true;
			$handle = $this->getList($request,'handle');
			// var_dump($res,$data);
			foreach($handle as $item){
				$key = $item['key'];
				$val = $this->convert($item['key'],false);
				if($res->$val != $data[$key]){
					$flag = false;
					break;
				}
			}
			return $flag;

			// if($res->is_show == $data['IsShow'] && $res->is_refresh == $data['IsRefresh'] && $res->is_add == $data['IsAdd'] && $res->is_modify == $data['IsModify'] && $res->is_search == $data['IsSearch'] && $res->is_save == $data['IsSave'] && $res->is_del == $data['IsDel'] && $res->is_import == $data['IsImport'] && $res->is_export == $data['IsExport'] && $res->is_print == $data['IsPrint'] && $res->is_checked == $data['IsChecked'] && $res->is_approved == $data['IsApproved'] && $res->is_reject == $data['IsReject'] && $res->is_init == $data['IsInit'] && $res->is_clear == $data['IsClear'] && $res->is_back == $data['IsBack']){
			// 	return true;
			// }

			// return false;
		}

		public function add(Request $request): mixed
		{
			try{
				$posts = $request->post();
				if(!$posts || !count($posts)){
					return 100007;
				}

				$handle = $this->getList($request,'handle');
				$update = 0;
				$insert = 0;
				$keys = "`RoleID`,`MenuID`";
				foreach($handle as $item){
					$keys .= ",`".$item['key']."`";
				}
				$vals = "";
				$param = [];

				foreach($posts as $post){
					$role_id 	 = isset($post['role_id']) ? $post['role_id'] : 0;
					$menu_id 	 = isset($post['menu_id']) ? $post['menu_id'] : 0;
					
					if(!$role_id || !$menu_id){
						return '无效的数据';
					}

					$data = [];
					foreach($handle as $item){
						$val = $this->convert($item['key'],false);
						$data[$item['key']] = isset($post[$val]) ? $post[$val] : 0;
					}

					// $data = [
					// 	'IsShow' 	 => isset($post['is_show']) ? $post['is_show'] : 0,
					// 	'IsRefresh'  => isset($post['is_refresh']) ? $post['is_refresh'] : 0,
					// 	'IsAdd' 	 => isset($post['is_add']) ? $post['is_add'] : 0,
					// 	'IsModify' 	 => isset($post['is_modify']) ? $post['is_modify'] : 0,
					// 	'IsSearch' 	 => isset($post['is_search']) ? $post['is_search'] : 0,
					// 	'IsSave' 	 => isset($post['is_save']) ? $post['is_save'] : 0,
					// 	'IsDel' 	 => isset($post['is_del']) ? $post['is_del'] : 0,
					// 	'IsImport' 	 => isset($post['is_import']) ? $post['is_import'] : 0,
					// 	'IsExport' 	 => isset($post['is_export']) ? $post['is_export'] : 0,
					// 	'IsPrint' 	 => isset($post['is_print']) ? $post['is_print'] : 0,
					// 	'IsChecked'  => isset($post['is_checked']) ? $post['is_checked'] : 0,
					// 	'IsApproved' => isset($post['is_approved']) ? $post['is_approved'] : 0,
					// 	'IsReject' => isset($post['is_reject']) ? $post['is_reject'] : 0,
					// 	'IsInit' 	 => isset($post['is_init']) ? $post['is_init'] : 0,
					// 	'IsClear' 	 => isset($post['is_clear']) ? $post['is_clear'] : 0,
					// 	'IsBack'	 => isset($post['is_back']) ? $post['is_back'] : 0,
					// ];

					$res = $this->getList($request,'data',$role_id,$menu_id);
					if(is_array($res) && isset($res['code']) && isset($res['msg'])){
						return $res;
					}

					if(is_object($res) && property_exists($res, 'role_id')){
						if(!$this->checkValue($request,$res,$data)){
							$this->updateData($request,$data,$res->id);
						}
						$update++;
					}else{
						$vals .= "(?,?,";
						array_push($param,$role_id,$menu_id);
						foreach($data as $k => $v){
							$vals .= "?,";
							array_push($param,$v);
						}
						$vals = rtrim($vals,",") . "),";
						// array_push($vals,$vals_str);
						$insert++;
					}
				}
				$vals = rtrim($vals,",");
				if($keys && $vals && $param){
					// var_dump($vals);
					$sql = "INSERT INTO `".$this->tab."` ({$keys}) VALUES {$vals}";
					$result = Db::insert($sql,$param);
				}

				return ['insert'=>$insert,'update'=>$update];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

    	protected function getActionList(Request $request,$id = 0): array
    	{
    		try{
				$handle = $this->getList($request,'handle');
				$fields = [];
				foreach($handle as $item){
					$key = $this->convert($item['key'],false);
					array_push($fields,['title'=>$item['title'],'key'=>$key,'value'=>0]);
				}
    			// $fields = [
				// 	['title' => '显示','key' => 'is_show','value' => 0],
				// 	['title' => '刷新','key' => 'is_refresh','value' => 0],
				// 	['title' => '新增','key' => 'is_add','value' => 0],
				// 	['title' => '修改','key' => 'is_modify','value' => 0],
				// 	['title' => '查询','key' => 'is_search','value' => 0],
				// 	['title' => '保存','key' => 'is_save','value' => 0],
				// 	['title' => '删除','key' => 'is_del','value' => 0],
				// 	['title' => '导入','key' => 'is_import','value' => 0],
				// 	['title' => '导出','key' => 'is_export','value' => 0],
				// 	['title' => '打印','key' => 'is_print','value' => 0],
				// 	['title' => '审核','key' => 'is_checked','value' => 0],
				// 	['title' => '核准','key' => 'is_approved','value' => 0],
				// 	['title' => '拒绝','key' => 'is_reject','value' => 0],
				// 	['title' => '初始化','key' => 'is_init','value' => 0],
				// 	['title' => '清空','key' => 'is_clear','value' => 0],
				// 	['title' => '返回','key' => 'is_back','value' => 0],
				// ];

				$role_id = $request->input('role_id',0);

				$service = new RoleModel();
				$role = $service->getList($request,'option');

				$service = new MenuModel();
				$menu = $service->getList($request,'tree');
				if($menu){
					for($i=0;$i<count($menu);$i++){
						if(!$menu[$i]->number){
							$menu[$i]->children = $fields;
						}else{
							$menu[$i]->children = [['title' => '显示','key' => 'is_show','value' => 0]];
						}

						unset($menu[$i]->icon);
						unset($menu[$i]->is_open);
						unset($menu[$i]->level);
						// unset($menu[$i]->pid);
						// unset($menu[$i]->number);
						unset($menu[$i]->sort);
						unset($menu[$i]->super_id);
						unset($menu[$i]->module_id);
						unset($menu[$i]->pvalue);
						unset($menu[$i]->cvalue);
						unset($menu[$i]->svalue);
						unset($menu[$i]->router);
						unset($menu[$i]->is_del);
						unset($menu[$i]->create_time);
						unset($menu[$i]->update_time);
						unset($menu[$i]->delete_time);
					}
				}

				$grant = [];
				if($role_id){
					$grant = $this->getList($request,'role',$role_id);
					if($grant && $menu){
						for($i=0;$i<count($menu);$i++){
							$menu[$i]->children = $this->getGrantValue($grant,$fields,$menu[$i]->id);
						}
					}
				}

				return ['role_id'=>$role_id,'role'=>$role,'menu'=>$menu,'grant'=>$grant];
    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
    	}

		private function getGrantValue($grant,$fields,$menu_id){
			// $children = [];
			if($grant){
				foreach($grant as $item){
					if($item->menu_id == $menu_id){
						// var_dump($item);
						foreach($fields as $k => $field){
							$key = $field['key'];
							if($item->$key){
								$fields[$k]['value'] = 1;
							}
						}
					}
				}
			}

			return $fields;
		}
	}
?>