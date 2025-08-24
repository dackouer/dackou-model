<?php
	namespace dackou\service\Query;

	use support\Request;
	use support\Db;

	class QueryService{
		public function setQuery(Request $request){
			$sql = trim($request->post('sql',''));

			if(empty($sql)){
				return 1065;
			}

			$temp = explode(" ",$sql);
			$type = isset($temp[0]) ? strtolower($temp[0]) : '';
			$skey = ['select','insert','update','alter','create','delete','desc','show','drop','truncate'];
			if(empty($type) || !in_array($type,$skey)){
				return 1065;
			}

			switch($type){
				case 'select':
					$result = $this->select($sql);
					break;
				case 'insert':
					$result = $this->insert($sql);
					break;
				case 'update':
					$result = $this->update($sql);
					break;
				case 'alter':
					$result = $this->alter($sql);
					break;
				case 'create':
					$result = $this->create($sql);
					break;
				case 'delete':
					$result = $this->delete($sql);
					break;
				case 'desc':
					$result = $this->desc($sql);
					break;
				case 'show':
					$result = $this->show($sql);
					break;
				case 'drop':
					$result = $this->drop($sql);
					break;
				case 'truncate':
					$result = $this->truncate($sql);
					break;
				default:
					$result = ['code' => 1,'msg' => 'fail','data' => ''];
			}


			return $result;
		}

		// 查询语句
		private function select($sql){
			try{
				$result = Db::select($sql);
				if($result !== false){
					return ['code' => 0,'msg' => 'success','data' => $result];
				}
				return ['code' => 1,'msg' => 'fail','data' => ''];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		// 插入语句
		private function insert($sql){
			try{
				$result = Db::insert($sql);
				if($result){
					return ['code' => 0,'msg' => '插入数据成功，id: '.$result,'data' => ''];
				}
				return ['code' => 1,'msg' => 'fail','data' => ''];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		// 更新语句
		private function update($sql){
			try{
				$result = Db::update($sql);
				if($result !== false){
					return ['code' => 0,'msg' => '更新数据成功','data' => ''];
				}
				return ['code' => 1,'msg' => 'fail','data' => ''];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		// alter
		private function alter($sql){
			try{
				$result = Db::select($sql);
				if($result !== false){
					return ['code' => 0,'msg' => '修改数据表成功','data' => ''];
				}
				return ['code' => 1,'msg' => 'fail','data' => ''];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		// create
		private function create($sql){
			try{
				$result = Db::select($sql);
				if($result !== false){
					return ['code' => 0,'msg' => '创建数据表成功','data' => ''];
				}
				return ['code' => 1,'msg' => 'fail','data' => ''];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		// delete
		private function delete($sql){
			try{
				$result = Db::select($sql);
				if($result !== false){
					return ['code' => 0,'msg' => '删除数据成功','data' => ''];
				}
				return ['code' => 1,'msg' => 'fail','data' => ''];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		// desc
		private function desc($sql){
			try{
				$result = Db::select($sql);
				if($result !== false){
					return ['code' => 0,'msg' => 'success','data' => $result];
				}
				return ['code' => 1,'msg' => 'fail','data' => ''];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		// show
		private function show($sql){
			try{
				$result = Db::select($sql);
				if($result !== false){
					return ['code' => 0,'msg' => 'success','data' => $result];
				}
				return ['code' => 1,'msg' => 'fail','data' => ''];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		// drop
		private function drop($sql){
			try{
				$result = Db::select($sql);
				if($result !== false){
					return ['code' => 0,'msg' => '删除表成功','data' => ''];
				}
				return ['code' => 1,'msg' => 'fail','data' => ''];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		// truncate
		private function truncate($sql){
			try{
				$result = Db::select($sql);
				if($result !== false){
					return ['code' => 0,'msg' => '数据表清空成功','data' => ''];
				}
				return ['code' => 1,'msg' => 'fail','data' => ''];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		private function getExceptionError($e){
			return [
				'code' => $e->getCode() ? $e->getCode() : 1,
				'msg'  => $e->getMessage(),
				'data' => 'fail'
			];
		}
	}
?>