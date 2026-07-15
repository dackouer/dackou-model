<?php
	namespace dackou;

	use Closure;
	use Illuminate\Contracts\Pagination\CursorPaginator;
	use Illuminate\Contracts\Pagination\LengthAwarePaginator;
	use Illuminate\Contracts\Pagination\Paginator;
	use Illuminate\Database\Eloquent\Model as BaseModel;
	use Illuminate\Database\Query\Builder;
	use Illuminate\Database\Query\Expression;
	use Illuminate\Database\Query\Grammars\Grammar;
	use Illuminate\Database\Query\Processors\Processor;
	use Illuminate\Support\Collection;
	use Illuminate\Support\LazyCollection;

	use support\Request;
	use support\Response;
	use support\Db;
	use dackou\service\Token\TokenService;


	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
	use PhpOffice\PhpSpreadsheet\Style\Alignment;
	use PhpOffice\PhpSpreadsheet\Style\Border;
	use PhpOffice\PhpSpreadsheet\Style\Fill;

	class Model extends BaseModel
	{
		const CREATED_AT = 'CreateTime';
    	const UPDATED_AT = 'UpdateTime';
    	protected $dateFormat = 'U';
    	public $timestamps = true;
    	protected $dbname = '';
    	protected $prefix = '';
    	protected $tab = '';
    	protected $title = '';
    	protected $primaryKey = 'id';
        protected $show_method = 'page';
        protected $page = true;
        protected $is_schema = false;
    	protected $orderBy = 'asc';
    	protected $is_field = true;
    	protected $is_tabs = false;
    	protected $pagekey = 'page';
    	protected $pagesize_key = 'pagesize';
    	protected $option_label = 'label';
    	protected $option_value = 'value';
    	protected $delete = true;
    	protected $truncate = true;
    	public $layer = 1;
    	public $digit = 1;
    	protected $pagesize = 10;
    	protected $is_size = false;
    	protected $is_total = true;
    	protected $is_jumper = false;
    	protected $action = '';
    	protected $align = 'center';
    	protected $option_key = '';
    	protected $code_length = 8;
    	protected $idzero = false;
    	protected $editor = 'wang';
    	protected $is_ai = true;
    	protected $field = [];
    	protected $fields = [];
    	protected $system = [];
    	protected $config = [];
    	protected $is_cate = false;
    	protected $cate_table = '';
    	protected $config_table = '';
    	protected $default_status_value = 0;
    	protected $min_rating = 0;
    	protected $max_rating = 5;
    	protected $import_file = '';
    	protected $init_data = [];
    	protected $action_value = [];
    	protected $import_field = [];
    	protected $host = [];
    	protected $coin_name = '金币';
    	protected $action_width = 0;
    	protected $color = ['primary'=>'#409EFF','success'=>'#67C23A','warning'=>'#E6A23C','danger'=>'#F56C6C','info'=>'#909399'];
    	protected $status_value = [0 => '关闭',1 => '正常'];
    	protected $gender_value = [0=>'未知',1=>'男',2=>'女'];
    	protected $action_excude = [];
    	protected $map_excude = [];
    	protected $exclude = ['CreateUser','CreateIP','UpdateTime','UpdateIP','DeleteTime','CheckUser','CheckTime','CheckIP','DeleteIP','IsDel'];

    	public function __construct(){
    		$this->setConfig();
    	}

    	// 设置配置项
    	private function setConfig(){
    		if(!empty($this->table)){
    			$this->table = $this->convert($this->table,false);
    			$this->fields = [];
    		}
    		$config = config('database') ?? [];
    		if(isset($config['connections']['mysql']['prefix'])){
    			$this->dbname = $config['connections']['mysql']['database'];
    			$this->prefix = $config['connections']['mysql']['prefix'];
    			if($this->table){
    				$this->tab = $this->prefix . $this->table;
    			}
    		}
    		if(isset($config['host']) && is_array($config['host']) && count($config['host'])){
    			$this->host = $config['host'];
    		}

    		$this->system = $this->getOptionData('system');
    		if(isset($this->system['system_editor']) && $this->system['system_editor']){
    			$this->editor = $this->system['system_editor'];
    		}

    		/*
    		$this->tabdata = $this->getTableOption();
    		if($this->tabdata && is_object($this->tabdata) && property_exists($this->tabdata,'id')){
    			if($this->tabdata->title){
    				$this->title = $this->tabdata->title;
    			}
    			if($this->tabdata->table_name){
    				$this->table = $this->tabdata->table_name;
    			}
    			if($this->tabdata->primary_key){
    				$this->primaryKey = $this->tabdata->primary_key;
    			}
    			if($this->tabdata->layer && is_numeric($this->tabdata->layer) && $this->tabdata->layer > 0){
    				$this->layer = (int)$this->tabdata->layer;
    			}
    			if($this->tabdata->show_method){
    				$this->show_method = $this->tabdata->show_method;
    			}
    			if($this->tabdata->is_page){
    				$this->page = $this->tabdata->is_page ? true : false;
    			}
    			if($this->tabdata->cate_table){
    				$this->cate_table = $this->tabdata->cate_table;
    			}
    			if($this->tabdata->config_table){
    				$this->config_table = $this->tabdata->config_table;
    			}
    			if($this->tabdata->option_key){
    				$this->option_key = $this->tabdata->option_key;
    			}
    			if($this->tabdata->status_value){
    				$this->status_value = $this->getDecodeData($this->tabdata->status_value);
    			}
    			if($this->tabdata->order_by){
    				$this->order_by = $this->tabdata->order_by;
    			}
    			if($this->tabdata->is_qrcode){
    				$this->is_qrcode = $this->tabdata->is_qrcode ? true : false;
    			}
    		}

    		if(!empty($this->config_table)){

    		}

    		$this->fields = $this->getList(\request(),'fields');
			*/

    		if($this->option_key){
    			$this->config = $this->getOptionData($this->option_key);
    		}
    		
			if(method_exists($this,'_init')){
				$this->_init();
			}
    	}

    	/**
    	 * 统一新增入口
    	 * @param Request $request [description]
    	 */
    	public function add(Request $request){
    		try{
    			if(\strtolower($request->method()) !== 'post'){
					return 100000;
				}
				$data = $request->post();
				if(\method_exists($this,'setRequest')){
					$data = \array_merge($data,$this->setRequest($request));
				}
				if(!$data || !is_array($data)){
					return $data;
				}

				if(\method_exists($this,'validate')){
					$res_valid = $this->validate($request);
					if(\is_array($res_valid) && $res_valid){
						$data = \array_merge($data,$res_valid);
					}elseif($res_valid !== true){
						return $res_valid;
					}
				}
				if(!$data || !is_array($data) || !count($data)){
	                return 100000;  //
	            }

	            if(isset($data['callback']) && \method_exists($this,$data['callback'])){
	            	$_callback = $data['callback'];
	            	return $this->$_callback($request,$data);
	            }
	            // var_dump($data);
	            $field = $this->getList($request,'field','key');
	            $param = [];
	            foreach($data as $k => $v){
	            	if(isset($field[$this->convert($k,false)])){
	            		$param[$field[$k]] = \is_array($v) ? $this->getJsonData($v) : $v;
	            	}
	            }

	    		if(!count($param)){
	    			return 100000;
	    		}

	    		if($this->layer > 1 && $this->digit > 1 && isset($param['PID'])){
	    			$maxid = $this->getMaxid($param['PID'],$this->digit);
	    			if($maxid){
		    			$param[$this->convert($this->primaryKey)] = $maxid;
		    		}
	    		}
	    		if(isset($field['create_ip'])){
	    			$param['CreateIP'] = $request->getRealIp($safe_mode = true);
	    		}

	    		foreach($param as $k => $v){
	    			$this->$k = $v;
	    		}
	    		
	    		$result = $this->save();
	    		if($result){
	    			$data = $this->getConvertData($data);
	    			$data['id'] = Db::getPdo()->lastInsertId();
	    			if($this->layer > 1){
                		Db::table($this->table)->where('ID',$data['pid'])->increment('Number');
                	}
                	if(isset($data['sort']) && $this->fieldExists('Sort')){
                		$pid = isset($data['pid']) ? $data['pid'] : -1;
                		$this->adjustSort($request,$data['sort'],$data[$this->primaryKey],$pid);
                	}
	    			if(method_exists($this,'setExcute')){
	    				$result = $this->setExcute($request,$data,$request->input('action',false));
	    				if($result !== false){
	    					return $data;
	    				}
	    				return '数据插入成功但执行回调失败';
	    			}

	    			return $data;
	    		}

	    		return '数据新增失败';
    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
    	}

    	public function mod(Request $request,$id = 0){
    		try{
    			if(strtolower($request->method()) !== 'post'){
					return 100000;
				}

				if($this->table == 'user'){
					if($request->post('action')){
						$id = $request->post('user_id',0);
					}
				}else{
					$id = $id ?: $request->input($this->primaryKey,0);
				}
				
	    		if(!$id){
	    			return 100007;
	    		}
	    		
	    		$data = $this->getList($request,$id);
	    		if(!$data || !is_object($data) || !property_exists($data,$this->primaryKey)){
	    			return '数据不存在或已被删除';
	    		}
	    		$post = $request->post();
				if(method_exists($this,'setRequest')){
		    		$post = \array_merge($post,$this->setRequest($request,$id));
		    	}
	    		if(!$post || !is_array($post)){
	    			return '无效的数据';
	    		}
	    		if(method_exists($this,'validate')){
		    		$res_valid = $this->validate($request,$id,$data);
		    		if(is_array($res_valid) || $res_valid === true){
		    			if(is_array($res_valid)){
		    				$post = \array_merge($post,$res_valid);
		    			}
		    		}else{
		    			return $res_valid;
		    		}
		    		
		    	}
				if(!$post || !is_array($post)){
	    			return '空数据';
	    		}

	            if(isset($post['callback']) && \method_exists($this,$post['callback'])){
	            	$_callback = $post['callback'];
	            	return $this->$_callback($request,$post,$id);
	            }

	    		$field = $this->getList($request,'field','key');
	    		// var_dump($field);
	    		if(!$field){
	    			return '无效的表字段';
	    		}

	    		$param = [];
    			foreach($post as $k => $v){
    				$key = $this->convert($k,false);
	    			if($key !== $this->primaryKey && isset($field[$key])){
	            		$param[$field[$key]] = \is_array($v) ? $this->getJsonData($v) : $v;
	            	}
	    		}
	    		if(!$param || !count($param)){
	    			return '无效的数据参数';
	    		}

	    		if($this->fieldExists('UpdateTime')){
	    			$param['UpdateTime'] = time();
	    		}
	    		if($this->fieldExists('UpdateIP')){
	    			$param['UpdateIP'] = $request->getRealIp($safe_mode = true);
	    		}
	    		
	    		$result = Db::table($this->table)->where($this->convert($this->primaryKey),$id)->update($param);
	    		// var_dump('result: '.$result);
	    		if($result !== false){
	    			$data = $this->getConvertData($post);
	    			$data['id'] = $id;
                	if(isset($param['Sort']) && $this->fieldExists('Sort')){
                		$pid = isset($param['PID']) ? $param['PID'] : -1;
                		$this->adjustSort($request,$param['Sort'],$id,$pid);
                	}

	    			if(method_exists($this,'setExcute')){
	    				$action = isset($post['action']) ? $post['action'] : 'mod';
	    				return $this->setExcute($request,$data,$action);
	    			}
	    			return $data;
	    		}

	    		return '数据更新失败';
    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
    	}

    	/**
    	 * 初始化数据
    	 * @param Request $request [description]
    	 * @param array   $data    [description]
    	 */
    	public function setInitialize(Request $request,$data = []){
    		try{
    			Db::table($this->table)->truncate();
    			if(\method_exists($this,'setTruncate')){
    				$this->setTruncate($request);
    			}

    			if($this->table === 'user'){
    				$sql = "alter table ".$this->prefix."user AUTO_INCREMENT = 1000";
    				Db::select($sql);
    			}

    			$data = $data&&count($data) ? $data : $this->getDefaultData($request);
    			if(!$data || !is_array($data) || !count($data) || !is_array($data[0])){
    				return '无效的初始化数据';
    			}

    			$payload = [];
    			foreach($data as $key => $val){
    				foreach($val as $k => $v){
    					$k = $this->convert($k);
    					$payload[$key][$k] = $v;

						if(!isset($val['create_time']) && !isset($val['CreateTime'])){
							$payload[$key]['CreateTime'] = $this->timestamps ? time() : \date('Y-m-d H:i:s',time());
						}
						if(!isset($val['create_ip']) && !isset($val['CreateIP'])){
							$payload[$key]['CreateIP'] = $request->getRealIp($safe_mode = true);
						}
    				}
    			}
    			// var_dump($payload);
    			$result = Db::table($this->table)->insert($payload);
    			return $result ? count($payload) : 0;
    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
    	}

    	protected function getExportList(Request $request){
    		try{
    			$field = $this->getList($request,'field',true);
    			$where = $this->getWhere($request);

    			$object = Db::table($this->table)
    						->select(...$field)
    						->where($where)
    						->get();
    			return $object;
    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
    	}

    	protected function getImportfileList(Request $request){
    		try{
    			$res = Db::select("SHOW FULL FIELDS FROM `".$this->tab."`");
	    		if($res){
	    			$fields = ['ID','Picture','Idcode','Description','Content','UserID','Status','State','CreateTime','CreateIP','UpdateTime','UpdateIP','DeleteTime','DeleteIP','IsDel'];
	    			$head = [];
	    			foreach($res as $field){
	    				if(!in_array($field->Field,$fields)){
	    					array_push($head,$field->Comment);
	    				}
	    			}
	    			// var_dump($head);
	    			if($head){
	    				\dackou\service\Office\OfficeService::exported($request,$head,[]);
	    			}
	    			return $head;
	    		}
    		}catch(\Exception $e){
    			return '';
    		}
    	}

    	/**
    	 * 获取导入数据的字段
    	 * @param  Request $request [description]
    	 * @return [type]           [description]
    	 */
    	protected function getImportFields(Request $request){
    		if($this->import_field){
    			return $this->import_field;
    		}

    		$res = Db::select("SHOW FULL FIELDS FROM `".$this->tab."`");
    		if($res){
    			$fields = ['ID','Picture','Idcode','Description','Content','UserID','Status','State','CreateTime','CreateIP','UpdateTime','UpdateIP','DeleteTime','DeleteIP','IsDel'];
    			$result = [];
    			foreach($res as $field){
    				if(!in_array($field->Field,$fields)){
    					array_push($result,$field->Field);
    				}
    			}
    			// var_dump($result);
    			return $result;
    		}
    		return [];
    	}

    	protected function getImportList(Request $request){
    		return [];
    	}

    	/**
    	 * 获取导入的模板文件
    	 * @param  Request $request [description]
    	 * @return [type]           [description]
    	 */
    	private function getImportFile(Request $request){
    		$action = $request->input('file',$request->input('type',''));
			$file = ($action && is_array($this->import_file)) ? $this->import_file[$action] : $this->import_file;
			if($file){
				$file = $this->host['api'] . 'import/' . $file;
			}else{
				$file = $this->host['api'] . 'import/' . $this->table . '.xlsx';
				if(!is_file($file)){
					$file = $this->host['api'] . 'import/' . $this->table . '.xls';
				}
			}
			return $file;
    	}

    	/**
    	 * 创建导入的模板文件
    	 * @param  Request $request [description]
    	 * @return [type]           [description]
    	 */
    	protected function createImportFile(Request $request){
    		try{
    			ini_set('memory_limit','512M');
    			// 创建新的Spreadsheet对象
	            $spreadsheet = new Spreadsheet();
	            $sheet = $spreadsheet->getActiveSheet();
	            // 设置工作表标题
            	$sheet->setTitle('用户数据');
            	// 设置表头
	            $headers = ['ID', '用户名', '邮箱', '手机号', '创建时间'];
	            // $headers = $this->getList($request,'importhead');
	            $sheet->fromArray($headers, null, 'A1');
	            // 模拟数据 - 实际应用中可以从数据库获取
                $data = [
                    [1, '张三', 'zhangsan@example.com', '13800138001', '2023-01-01'],
                    [2, '李四', 'lisi@example.com', '13800138002', '2023-01-02'],
                    [3, '王五', 'wangwu@example.com', '13800138003', '2023-01-03'],
                    [4, '赵六', 'zhaoliu@example.com', '13800138004', '2023-01-04'],
                ];
                // $data = $this->getList($request,'import');
                // 填充数据
            	$sheet->fromArray($data, null, 'A2');
            	// 设置表头样式
	            $headerStyle = [
	                'font' => [
	                    'bold' => true,
	                    'color' => ['rgb' => 'FFFFFF']
	                ],
	                'fill' => [
	                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
	                    'startColor' => ['rgb' => '4472C4']
	                ],
	                'alignment' => [
	                    'horizontal' => Alignment::HORIZONTAL_CENTER,
	                ],
	                'borders' => [
	                    'allBorders' => [
	                        'borderStyle' => Border::BORDER_THIN,
	                    ],
	                ],
	            ];
	            $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);
	            // 设置数据区域样式
	            $dataStyle = [
	                'borders' => [
	                    'allBorders' => [
	                        'borderStyle' => Border::BORDER_THIN,
	                    ],
	                ],
	                'alignment' => [
	                    'horizontal' => Alignment::HORIZONTAL_LEFT,
	                ],
	            ];
	            $dataCount = count($data) + 1; // 表头1行 + 数据行数
            	$sheet->getStyle("A1:E{$dataCount}")->applyFromArray($dataStyle);
            	// 自动调整列宽
	            foreach (range('A', 'E') as $column) {
	                $sheet->getColumnDimension($column)->setAutoSize(true);
	            }

	            // 设置文件名
	            $filename = $this->table . date('YmdHis') . '.xlsx';
	            
	            // 设置HTTP头
	            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	            header('Content-Disposition: attachment;filename="' . $filename . '"');
	            header('Cache-Control: max-age=0');
	            header('Cache-Control: max-age=1');
	            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
	            header('Cache-Control: cache, must-revalidate');
	            header('Pragma: public');
	            
	            // 直接输出到浏览器
	            ob_start();
	            $writer = new Xlsx($spreadsheet);
	            $writer->save('php://output');
	            $output = ob_get_clean();

	            // 立即释放内存
	            $spreadsheet->disconnectWorksheets();
	            unset($spreadsheet, $writer);
	            
	            return response($output,200,[
	            	'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
	            	'Content-Disposition' => 'attachment; filename="' . $filename . '"',
	            ]);
    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
    	}

    	/**
    	 * 下载导入模板文件
    	 * @param  Request $request [description]
    	 * @return [type]           [description]
    	 */
    	public function downImportFile(Request $request){
    		try{
    			$file = $this->getImportFile($request);
    			if($file){
    				$filename = $request->input('name',$request->input('filename',''));
    				if(!$filename){
    					$temp = explode('/',$file);
    					$filename = end($temp);
    				}
    				return ['code'=>0,'data'=>['url'=>$file,'name'=>$filename]];
    			}else{
    				return $this->createImportFile($request);
    			}
    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
    	}

    	/**
    	 * 导入数据
    	 * @param Request $request [description]
    	 */
    	public function setImport(Request $request){
    		try{
    			$fields = $this->getImportFields($request);
    			// var_dump('fields:' ,$fields);
    			if(!$fields || !is_array($fields) || !count($fields)){
    				return '无效的导入字段';
    			}

    			$file = $request->post('file');
    			// var_dump('file: ',$file);
	            if(!$file){
	                return '未找到要导入的文件';
	            }
	            $file = (is_array($file) && isset($file['save_path'])) ? $file['save_path'] : $file;
	            if(!$file){
	                return '未找到要导入的文件';
	            }
	            
	            $data = \dackou\service\Office\OfficeService::imported($file);
	            // var_dump($data);
	            if(!$data || !is_array($data) || !count($data)){
		            return '无效的导入数据';
		        }
		        \array_shift($data);
		        if(!$data || !is_array($data) || !count($data)){
		            return '无效的导入数据';
		        }

		        if(count($data[0]) !== count($fields)){
		        	return '导入数据字段不匹配';
		        }

		        if(\method_exists($this,'validate')){
		        	
		        }

		        $success_number = 0;
		        $failed_number = 0;
		        $keys = [];
		        $value = "";
		        $param = [];
		        $ip = $request->getRealIp($safe_mode = true);
		        $is_idcode = $this->fieldExists('Idcode');
		        $is_create_time = $this->fieldExists('CreateTime');
		        $is_create_ip = $this->fieldExists('CreateIP');

		        foreach($fields as $field){
		        	array_push($keys,"`".$this->convert($field)."`");
		        }
		        if($is_idcode){
		        	array_push($keys,"`Idcode`");
		        }
		        if($is_create_time){
		        	array_push($keys,"`CreateTime`");
		        }
		        if($is_create_ip){
		        	array_push($keys,"`CreateIP`");
		        }
		        for($i=0;$i<count($data);$i++){
		        	$value .= "(";
		        	foreach($data[$i] as $k => $v){
		        		$value .= "?,";
		        		array_push($param,$v);
		        	}
		        	if($is_idcode){
			        	$value .= "?,";
			        	array_push($param,$this->createUniqueCode('Idcode',$this->code_length));
			        }
		        	if($is_create_time){
			        	$value .= "?,";
			        	array_push($param,time());
			        }
			        if($is_create_ip){
			        	$value .= "?,";
			        	array_push($param,$ip);
			        }
		        	$value = rtrim($value,",") . "),";
		        }
		        $value = trim($value,",");
		        if(!$keys || !$value || !$param){
		        	return '无效的数据格式';
		        }

		        $sql = "INSERT INTO `".$this->tab."` (".\implode(',',$keys).") VALUES $value";
		        // var_dump('import sql: '.$sql);
		        // var_dump($param);

		        $result = Db::insert($sql,$param);
		        if($result !== false){
		        	return ['success' => count($data),'fail' => 0];
		        }
		        return '导入失败';
    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
    	}

    	protected function getDefaultData(Request $request){
    		return $this->init_data;
    	}

    	/**
    	 * 内容插入数据
    	 * @param  Request $request [description]
    	 * @param  array   $arr     [description]
    	 * @return [type]           [description]
    	 */
    	public function insertData(Request $request,array $arr = []): mixed
    	{
    		try{
    			if(!$arr){
    				return false;
    			}
    			$field = $this->getList($request,'field','key');

    			$time = time();
    			$ip = $request->getRealIp($safe_mode = true);
    			$keys = [];
    			$vals = [];
    			$param = [];
    			$data = [];
    			if($this->isIndexArray($arr)){
    				foreach($arr as $k => $v){
    					$k = $this->convert($k,false);
    					if(isset($field[$k])){
    						$v = is_array($v) ? $this->getJsonData($v) : $v;
    						$data[$field[$k]] = $v;
    						array_push($keys,"`{$field[$k]}`");
    						array_push($vals,'?');
    						array_push($param,$v);
    					}
    				}
    				if($data){
    					if(!isset($data['CreateTime']) || !isset($data['create_time'])){
	    					$data['CreateTime'] = $time;
		    				array_push($keys,"`CreateTime`");
		    				array_push($vals,'?');
	    					array_push($param,$time);
	    				}
	    				if(!isset($data['CreateIP']) || !isset($data['create_ip'])){
	    					$data['CreateIP'] = $ip;
		    				array_push($keys,"`CreateIP`");
		    				array_push($vals,'?');
	    					array_push($param,$ip);
	    				}
	    				$vals = ["(" . \implode(',',$vals) . ")"];
    				}
     			}else{
     				$fieldKeys = array_keys($field);
     				$fieldVals = array_values($field);
     				$children = [];
     				$time = time();
     				$ip = $request->getRealIp($safe_mode = true);
     				foreach($arr as $key => $val){
     					$valstr = "(";
     					foreach($val as $k => $v){
     						$k = $this->convert($k,false);
     						if(isset($field[$k])){
     							if(!$key){
     								array_push($keys,$field[$k]);
     							}
     							$v = is_array($v) ? $this->getJsonData($v) : $v;
     							$valstr .= "?,";
     							array_push($param,$v);
     						}
     					}

 						if(!isset($val['CreateTime']) && !isset($val['create_time'])){
 							$valstr .= "?,";
 							array_push($param,$time);
 						}

 						if(!isset($val['CreateIP']) && !isset($val['create_ip'])){
 							$valstr .= "?,";
 							array_push($param,$ip);
 						}

     					$valstr = rtrim($valstr,",");
     					if($valstr !== "("){
	     					$valstr .= ")";
	     					array_push($vals,$valstr);
	     				}
     				}

					if(!isset($val['CreateTime']) && !isset($val['create_time'])){
						array_push($keys,'CreateTime');
					}
					if(!isset($val['CreateIP']) && !isset($val['create_ip'])){
						array_push($keys,'CreateIP');
					}

     				// var_dump('keys: ',$keys);
     				// var_dump('vals: ',$vals);
     				// var_dump('param: ',$param);

    			}

    			if(!$keys || !$vals){
    				return false;
    			}

    			// var_dump('keys: ',$keys);
    			// var_dump('vals: ',$vals);
    			$keys = \implode(',',$keys);
    			$vals = \implode(',',$vals);
    			$sql = "INSERT INTO `".$this->tab."` ({$keys}) VALUES {$vals}";
    			// var_dump('insert data sql: ' . $sql);
    			$result = Db::insert($sql,$param);
    			var_dump('insert result: ',$result);
    			if($result !== false){
    				return $this->isIndexArray($arr) ? $data : $arr;
    			}

    			return false;
    		}catch(\Exception $e){
    			var_dump('insert data error: ',$this->getExceptionError($e));
    			return false;
    		}
    	}

    	/**
    	 * 内部修改数据
    	 * @param  Request       $request [description]
    	 * @param  array         $arr     [description]
    	 * @param  mixed|integer $id      [description]
    	 * @return [type]                 [description]
    	 */
    	public function updateData(Request $request,array $arr = [],mixed $id = 0): mixed
    	{
    		try{
    			if(!$id){
    				return false;
    			}
    			$keys = "";
    			$param = [];
    			foreach($arr as $key => $val){
    				$keys .= "`".$this->convert($key)."` = ?,";
    				array_push($param,$val);
    			}
    			$keys = rtrim($keys,",");
    			array_push($param,is_array($id) ? implode(',',$id) : $id);
    			if(is_array($id)){
    				$sql = "UPDATE `".$this->tab."` SET {$keys} WHERE `".$this->convert($this->primaryKey)."` IN (?)";
    			}else{
    				$sql = "UPDATE `".$this->tab."` SET {$keys} WHERE `".$this->convert($this->primaryKey)."` = ?";
    			}
    			// var_dump('update data sql: '.$sql);
    			// var_dump($param);
    			$result = Db::update($sql,$param);
    			// var_dump('update data result: '.$result);
    			return $result !== false ? true : false;
    		}catch(\Exception $e){
    			// var_dump($e->getMessage());
    			return false;
    		}
    	}

    	/**
    	 * 统一查询
    	 * @param  Request $request [description]
    	 * @return [type]           [description]
    	 */
    	public function getList(Request $request): mixed
    	{
    		$args = \func_get_args();
            \array_shift($args);

            try{
	            if(!$args || !count($args)){
	                return $this->getAllList($request);
	            }

	            if(\is_numeric($args[0])){
	            	$this->setHits($request,...$args);
	                return $this->getListById($request,...$args);
	            }

	            if(\is_array($args[0]) || \is_object($args[0])){
	                return $this->getAllList($request,...$args);
	            }

	            if(\is_string($args[0])){
	                $sign = \ucfirst(\strtolower($args[0]));
	                $class_name = 'get'.$sign.'List';

	                if(\method_exists($this,$class_name)){
	                	\array_shift($args);
	                    return $this->$class_name($request,...$args);
	                }
	                return 100005;
	            }

	            return $this->getAllList($request,...$args);
	        }catch(\Exception $e){
	        	return $this->getExceptionError($e,true);
	        }
    	}

    	/**
    	 * 通过id获取数据 无Request
    	 * @param  [type] $id [description]
    	 * @return [type]     [description]
    	 */
    	protected function fetch($id = 1,$table = ''){
    		try{
				$arr = [];
    			$field = '*';
    			$table = $table ? $table : $this->table;
    			$object = Db::table($table)
    						->select($field)
    						->where($this->convert($this->primaryKey),$id)
    						->first();
    			if($object){
    				foreach($object as $k => $v){
    					$arr[$this->convert($k,false)] = $v;
    				}
    			}
    			return (object)$arr;
    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
    	}

    	// 点击量
    	protected function setHits(Request $request,$id = 0){
    		try{
    			if($this->fieldExists('hits')){
    				Db::table($this->table)->where($this->convert($this->primaryKey),$id)->increment('Hits');
    			}
    		}catch(\Exception $e){
    			return false;
    		}
    	}

    	/**
    	 * 通过id获取数据 有Request
    	 * @param  Request $request [description]
    	 * @param  [type]  $id      [description]
    	 * @return [type]           [description]
    	 */
    	protected function getListById(Request $request,$id){
    		$field = $this->getList($request,'field');
			$where = $this->getWhere($request);
			array_push($where,[$this->table.'.'.$this->convert($this->primaryKey),'=',$id]);
			$object = Db::table($this->table)
						->select(...$field)
						->where($where)
						->first();
			if($object){
				foreach($object as $k => $v){
					if(is_string($v) && strpos($v,'[') !== false && strpos($v,']') !== false){
						$object->$k = $this->getDecodeData($v);
					}
				}
			}
			// var_dump($object);
			return $object;
    	}

    	/**
    	 * 通过多ID获取多条数据
    	 * @param  Request $request [description]
    	 * @param  array   $id      [description]
    	 * @return [type]           [description]
    	 */
    	protected function getIdsList(Request $request,$id = []){
    		if(!is_array($id)){
    			$id = explode(',',$id);
    		}
    		if(!$id){
    			return [];
    		}

    		$field = $this->getList($request,'field');
			$where = $this->getWhere($request);

			$object = Db::table($this->table)
						->select(...$field)
						->where($where)
						->whereIn($this->convert($this->primaryKey),$id)
						->get();
			if($object){
				foreach($object as $key => $val){
					foreach($val as $k => $v){
						if(strpos($v,'[') !== false && strpos($v,']') !== false){
							$object[$key]->$k = $this->getDecodeData($v);
						}
					}
				}
			}
			// var_dump($object);
			return $object;
    	}

    	/**
    	 * 查询主页数据
    	 * @param  Request $request [description]
    	 * @return [type]           [description]
    	 */
    	protected function getShowList(Request $request){
    		// 获取查询数据列表
    		$datas = $this->getList($request,$this->show_method);
    		// var_dump('get show data: ',$datas);
    		// 获取显示表头
    		$thead = $this->getList($request,'map');
    		// 获取权限
    		$grant = $this->getList($request,'permission');
            // 获取查询表单
            $query = $this->getList($request,'query');
            // 获取token数据
            $token = $this->getList($request,'token');
            
    		$data = isset($datas['data']) ? $datas['data'] : $datas;
    		$rows = isset($datas['rows']) ? (int)$datas['rows'] : ((is_object($datas)||is_array($datas)) ? count($datas) : 0);
    		$title = isset($datas['title']) ? $datas['title'] : $this->title;
    		$layer = isset($datas['layer']) ? (int)$datas['layer'] : (int)$this->layer;

    		foreach($thead as $k => $v){
    			if(!isset($thead[$k]['align']) && $this->align){
    				$thead[$k]['align'] = $this->align;
    			}
    		} 
    		
    		if($this->action_value && count($this->action_value)){
            	if($data && count($data) && !isset($data['code'])){
	            	for($i=0;$i<count($data);$i++){
	            		$primaryKey = $this->primaryKey;
	            		if(property_exists($data[$i],$primaryKey) && $data[$i]->$primaryKey){
	            			$data[$i]->action = $this->action_value;
	            		}else{
	            			$data[$i]->action = '';
	            		}
	            	}
	            }
                array_push($thead,['type' => 'action','prop' => 'action','label' => '操作','align' => 'center','width' => 80]);
            }
            if(in_array('is_modify',$grant)){
                array_push($thead,['type' => 'mod','prop' => 'mod','label' => '编辑','align' => 'center','width' => 80]);
            }
            if(in_array('is_del',$grant)){
                array_push($thead,['type' => 'del','prop' => 'del','label' => '删除','align' => 'center','width' => 80]);
            }

            $options = [
            	'is_size' 		=> $this->is_size,
            	'is_total'		=> 0,
                'is_jumper'		=> $this->is_jumper,
                'callback'		=> false,
            ];
            
            if($this->action_width){
            	$options['action_width'] = $this->action_width;
            }
            
            $result =  [
                'title'         => $title,
                'table'         => $this->table,
                'primaryKey'    => $this->primaryKey,
                'layer'         => $layer,
                'thead'         => $thead,
                'query'         => $query,
                'grant'         => $grant,
                'action'        => $this->action,
                'page'			=> (int)$request->input('page',1),
                'pagesize'		=> $layer > 1 ? $rows : (int)$this->pagesize,
                'prefix'		=> $this->prefix,
                'api'			=> $this->host['api'] ?? '',
                'importFile'    => $this->import_file,
                'rows'          => $rows,
                'data'          => $data,
                'is_size'		=> $this->is_size,
                'is_total'		=> 0,
                'is_jumper'		=> $this->is_jumper,
                'options'		=> $options
            ];
            if($this->is_tabs){
            	$tabs = $this->getList($request,'tabs');
            	if($tabs){
            		$result['tabs'] = $tabs;
            	}
            }

            // var_dump($datas);
            foreach($datas as $k => $v){
            	if(!in_array($k,['rows','data','title','layer'])){
            		$result[$k] = $v;
            	}
            }
            // var_dump($result);
            return $result;
    	}

    	/**
    	 * 查询所有数据
    	 * @param  Request $request [description]
    	 * @return [type]           [description]
    	 */
    	protected function getAllList(Request $request){
    		if($this->page){
    			return $this->getList($request,'page');
    		}
    		
    		$field = $this->getList($request,'field',true);
    		$where = $this->getWhere($request);


    		$object = Db::table($this->table)
    					->select(...$field)
    					->where($where)
    					->orderBy($this->table.'.ID',$this->orderBy)
    					->get();
    		if($object){
    			$object = $this->getResultData($object,true);
    		}

    		return $object;
    	}

    	/**
    	 * 查询分页数据
    	 * @param  Request $request [description]
    	 * @return [type]           [description]
    	 */
    	protected function getPageList(Request $request){
    		$field = $this->getList($request,'field',true);
    		$where = $this->getWhere($request);

    		$is_cate = $this->fieldExists('cate_id');
    		$cate_table = $this->cate_table ?: $this->table . '_cate';
    		if($is_cate && $this->getClassName($cate_table)){
    			array_push($field,$cate_table.'.Title as cate_title');
    		}

    		$keyword = trim($request->input('keyword',''));
    		if($keyword){
    			$k = '';
    			if($this->fieldExists('title')){
    				$k = 'Title';
    			}elseif($this->fieldExists($this->table.'_name')){
    				$k = $this->convert($this->table).'Name';
    			}elseif($this->fieldExists($this->table.'_title')){
    				$k = $this->convert($this->table).'Title';
    			}

    			if($k){
    				array_push($where,[$this->table.'.'.$k,'like',"%".$keyword."%"]);
    			}
    		}
    		
    		$rows = Db::table($this->table);
    		if($is_cate && $this->getClassName($cate_table)){
    			$rows = $rows->join($cate_table,'CateID','=',$cate_table.'.ID');
    		}
    		$rows = $rows->where($where)
    					->count();
    		[$offset,$limit] = $this->getLimit($request);
    		$object = Db::table($this->table);
    		if($is_cate && $this->getClassName($cate_table)){
    			$object = $object->join($cate_table,'CateID','=',$cate_table.'.ID');
    		}
    		
    		$object = $object->select(...$field)
    					->where($where)
    					->orderBy($this->table.'.ID',$this->orderBy)
    					->offset($offset)
    					->limit($limit)
    					->get();
    		if($object){
    			$object = $this->getResultData($object,true);
    		}
    					
    		return ['rows'=> $rows,'data'=>$object];    		
    	}

    	/**
    	 * 获取用户列表
    	 * @param  Request $request [description]
    	 * @param  integer $user_id [description]
    	 * @return [type]           [description]
    	 */
    	protected function getUserList(Request $request,$user_id = 0){
    		$user_id = $user_id ? $user_id : $request->input('user_id',0);
    		if(!$user_id){
    			return 100007;
    		}

    		$field = $this->getList($request,'field');
    		$where = $this->getWhere($request);
    		array_push($where,[$this->table.'.UserID','=',$user_id]);

    		$object = Db::table($this->table)
    					->select(...$field)
    					->where($where)
    					->get();
    		if($object){
    			return count($object) === 1 ? $object[0] : $object;
    		}

    		return [];
    	}

    	protected function getSearchList(Request $request,$flag = false){
    		$field = $this->getList($request,'field');
    		$where = $this->getWhere($request);

    		$post = $request->post();
    		if($post){
    			foreach($post as $k => $v){
    				$k = $this->convert($k);
    				if($this->fieldExists($k)){
    					array_push($where,[$k,'=',$v]);
    				}
    			}
    		}

    		if($flag){
	    		$rows = Db::table($this->table)
	    					->where($where)
	    					->count();
	    		[$offset,$limit] = $this->getLimit($request);
	    		$object = Db::table($this->table)
	    					->select(...$field)
	    					->where($where)
	    					->orderBy($this->convert($this->primaryKey),$this->orderBy)
	    					->offset($offset)
	    					->limit($limit)
	    					->get();

	    		return ['rows'=>$rows,'data'=>$object];
    		}else{
    			$object = Db::table($this->table)
    						->select(...$field)
    						->where($where)
	    					->orderBy($this->convert($this->primaryKey),$this->orderBy)
	    					->get();

	    		return ['rows'=>count($object),'data'=>$object];
    		}
    	}

    	/**
    	 * [getTreeList description]
    	 * @param  Request $request [description]
    	 * @return [type]           [description]
    	 */
    	protected function getTreeList(Request $request): array
    	{
    		$field = $this->getList($request,'field');
			$where = $this->getWhere($request);
			$object = Db::table($this->table)
							->select(...$field)
							->orderBy('Level','asc')
							->orderBy('Sort','asc')
							->where($where)
							->get();
			if($object){
				$object = $this->getResultData($object,true);
				return $this->tree($object);
			}

			return [];
    	}

    	/**
    	 * [getChildList description]
    	 * @param  Request $request [description]
    	 * @return [type]           [description]
    	 */
    	protected function getChildList(Request $request): array
    	{
    		$field = $this->getList($request,'field');
			$where = $this->getWhere($request);
			$object = Db::table($this->table)
							->select(...$field)
							->where($where)
							->get();
			$object = $this->getResultData($object,true);

			return $this->child($object);
    	}

    	/**
    	 * [getParentList description]
    	 * @param  Request $request [description]
    	 * @param  integer $pid     [description]
    	 * @return [type]           [description]
    	 */
    	protected function getParentList(Request $request,$pid = 0){
    		if(!is_numeric($pid) || is_null($pid) || $pid < 0){
				return [];
			}
			$field = $this->getList($request,'field');
			if(!$this->fieldExists('pid')){
				return [];
			}
			$where = $this->getWhere($request);
			if(!is_array($pid)){
				$pid = \explode(',',$pid);
			}
			$object = Db::table($this->table)
						->select(...$field);
			if($this->fieldExists('sort')){
				$object = $object->orderBy('Sort','asc');
			}else{
				$object = $object->orderBy($this->convert($this->primaryKey),'asc');
			}
			$object = $object->where($where)
						->whereIn('PID',$pid)
						->get();
			return $object;
    	}

    	/**
    	 * [getLevelList description]
    	 * @param  Request $request [description]
    	 * @param  integer $level   [description]
    	 * @return [type]           [description]
    	 */
    	protected function getLevelList(Request $request,$level = 1){
    		if(empty($level) || is_null($level) || !is_numeric($level) || $level <= 0){
				return [];
			}
			$field = $this->getList($request,'field');
			if(!$this->fieldExists('level')){
				return [];
			}
			$where = $this->getWhere($request);
			if(!is_array($level)){
				$level = \explode(',',$level);
			}
			$object = Db::table($this->table)
						->select(...$field);
			if($this->fieldExists('sort')){
				$object = $object->orderBy('Sort','asc');
			}else{
				$object = $object->orderBy($this->convert($this->primaryKey),'asc');
			}
			$object = $object->where($where)
						->whereIn('Level',$level)
						->get();
			return $object;
    	}

    	/**
    	 * [getDefaultList description]
    	 * @param  Request $request [description]
    	 * @return [type]           [description]
    	 */
		protected function getDefaultList(Request $request){
			$field = $this->getList($request,'field');
			$where = $this->getWhere($request);
			if($this->fieldExists('IsDefault')){
				array_push($where,[$this->table.'.IsDefault','=',1]);
			}else{
				return [];
			}

			$object = Db::table($this->table)
						->select(...$field)
						->where($where)
						->first();
			return $object;
		}

		protected function getCodeList(Request $request,$code = ''){
			if(!$code){
				return 100007;
			}

			$fieldname = $this->table.'Code';
			if(!$this->fieldExists($fieldname)){
				$fieldname = 'Idcode';
				if(!$this->fieldExists($fieldname)){
					$fieldname = 'Code';
					if(!$this->fieldExists($fieldname)){
						return 100007;
					}
				}
			}
			
			$field = $this->getList($request,'field');
			$where = $this->getWhere($request);
			array_push($where,[$fieldname,'=',$code]);

			$object = Db::table($this->table)
						->select(...$field)
						->where($where)
						->first();
			return $object;
		}

		/**
		 * [getTopList description]
		 * @param  Request $request [description]
		 * @param  integer $num     [description]
		 * @return [type]           [description]
		 */
    	protected function getTopList(Request $request,$num = 1){
    		$num = $num ? $num : $request->input('num',1);
			if(!is_numeric($num) || $num < 1){
				return [];
			}

			$field = $this->getList($request,'field');
			$where = $this->getWhere($request);

    		$args = \func_get_args();
			\array_shift($args);
			\array_shift($args);

			if($args && is_array($args)){
				foreach($args as $k => $v){
					$k = $this->convert($k);
					if($this->fieldExists($k)){
						array_push($where,[$k,'=',$v]);
					}
				}
			}

			$post = $request->post();
			if($post){
				foreach($post as $k => $v){
					$k = $this->convert($k);
					if($this->fieldExists($k)){
						array_push($where,[$k,'=',$v]);
					}
				}
			}
			
			$object = Db::table($this->table)
						->select(...$field)
						->where($where)
						->orderBy($this->convert($this->primaryKey),'desc')
						->limit($num);
			if($num > 1){
				$object = $object->get();

				return $this->getResultData($object,true);
			}else{
				$object = $object->first();
				return $this->getResultData($object);
			}
    	}

    	/**
    	 * 获取查询搜索表单
    	 * @param  Request $request [description]
    	 * @return [type]           [description]
    	 */
    	protected function getQueryList(Request $request): array
    	{
    		$query = [];
    		$fields = $this->getList($request,'fields');
    		if($fields && is_array($fields) && !isset($fields['code'])){
	    		foreach($fields as $field){
	    			if($field['is_search']){
	    				if($this->isField($field['map_name'],'title')){
	    					$value = \trim($request->input('keyword',''));
	    					array_push($query,['type'=>'input','label'=>$field['comment'],'prop'=>'keyword','value'=>$value,'placeholder'=>'关键词...']);
	    				}
	    			}
	    		}
	    	}
	    	if(!$query){
	    		$query = [
    				['type'=>'input','label'=>'关键词','prop'=>'keyword','value'=>trim($request->input('keyword','')),'placeholder'=>'关键词...']
    			];
	    	}
    		return $query;
    	}

        /**
         * 生成H5二维码
         * @param  [type] $url [description]
         * @return [type]      [description]
         */
        protected function createH5Qrcode($request,$data){
            $service = new \dackou\service\Qrcode\QrcodeService();
            $result = $service->createUrl($request,$data);
            return $result->getDataUri();
        }

        /**
         * 生成小程序二维码
         * @param  [type] $url [description]
         * @return [type]      [description]
         */
        protected function createMiniQrcode($request,$data){
            $service = new \dackou\service\Wechat\WechatService();
            // var_dump('mini qrcode data:');
            // var_dump($data);
            $result = $service->createMiniCode($request,$data);
            if(isset($result['code']) && $result['code'] === 0){
                return $result['data'];
            }
            return $result;
        }

    	protected function getQrcodeList(Request $request,$id = 0){
    		if(!$id){
    			return 100007;
    		}

    		$qrcode_router = $this->qrcode_router ? $this->qrcode_router : 'detail';
    		$pathH5 = trim($request->input('h5',$this->table == 'user' ? '/user/info/'.$id : $this->qrcode_host.'?id='.$id));
    		$pathMini = trim($request->input('mini',$this->table == 'user' ? '/pages/index/index?uid='.$id : '/'.$this->table.'/'.$qrcode_router.'?id='.$id));

    		$h5Data = [
    			'url' 	  => $pathH5,
    			'text' 	  => 'H5二维码',
    			'size'	  => 1024,
    			'margin'  => 10,
    			'logo' 	  => ''
    		];
    		// var_dump($h5Data);
    		$miniData = [
    			'path'	=> $pathMini,
    			'width' => 1024
    		];

    		$qrcodeH5 = $this->createH5Qrcode($request,$h5Data);
    		if(is_array($qrcodeH5) && isset($qrcodeH5['code'])){
    			$qrcodeH5 = '生成失败';
    		}
    		$qrcodeMini = $this->createMiniQrcode($request,$miniData);
    		if(is_array($qrcodeMini) && isset($qrcodeMini['code'])){
    			$qrcodeMini = '生成失败';
    		}

    		try{
    			return [
    				'id'	=> $id,
    				'title' => $this->title.'二维码',
    				'h5'	=> ['path'=>$pathH5,'qrcode'=>$qrcodeH5],
    				'mini'	=> ['path'=>$pathMini,'qrcode'=>$qrcodeMini]
    			];
    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
    	}

    	/**
    	 * 获取权限显示列表
    	 * @param  Request $request [description]
    	 * @return [type]           [description]
    	 */
    	protected function getPermissionList(Request $request): array
    	{
    		
    		$path = $this->getRouter($request);
    		$sign = $this->getTokenData($request,'sign');
    		if(!$path || !$sign){
    			return [];
    		}
    		
            $result = [];
    		$field = $this->getList($request,'handle',true);
            $object = Db::table('grant')
                        ->join('role','RoleID','=','role.ID')
                        ->join('menu','MenuID','=','menu.ID')
                        ->select(...$field)
                        ->where([['Sign','=',$sign],['Router','=',$path]])
                        ->first();
            // var_dump($object);
            if($object){
                foreach($object as $key => $val){
                    if($val === 1){
                        array_push($result,$key);
                    }
                }
            }
            
            return $result;
    	}

    	/**
    	 * 获取功能列表
    	 * @param  Request $request [description]
    	 * @param  boolean $flag    [description]
    	 * @return [type]           [description]
    	 */
    	protected function getHandleList(Request $request,$flag = false): array
    	{
    		$object = Db::table('handle')
    					->select('ID as id','Title as title','Key as key')
    					->where('IsDel',0)
    					->orderBy('Sort','asc')
    					->get();
    		// var_dump($object);
    		if($object){
    			if($flag){
    				$result = ['grant.IsShow as is_show'];
    			}else{
    				$result = [
    					[
    						'id' => 0,
	    					'title' => '显示',
	    					'key'	=> 'is_show',
	    					'type'  => '',
	    					'plan'	=> true
    					]
    				];
    			}
    			foreach($object as $k => $v){
    				if($flag){
    					array_push($result,"grant.".$object[$k]->key." as ".$this->convert($object[$k]->key,false));
    				}else{
    					array_push($result,[
    						'id' 	=> $object[$k]->id,
    						'title' => $object[$k]->title,
    						'key'   => $this->convert($object[$k]->key,false),
    						'type'  => '',
    						'plan'  => true
    					]);
    				}
    			}

    			return $result;
    		}


    		$field = [
    			['id'=>1,'title'=>'显示','key'=>'IsShow','type'=>'','plan'=>true],
    			['id'=>2,'title'=>'刷新','key'=>'IsRefresh','type'=>'','plan'=>true],
    			['id'=>3,'title'=>'新增','key'=>'IsAdd','type'=>'','plan'=>true],
    			['id'=>4,'title'=>'修改','key'=>'IsModify','type'=>'','plan'=>true],
    			['id'=>5,'title'=>'查询','key'=>'IsSearch','type'=>'','plan'=>true],
    			['id'=>6,'title'=>'保存','key'=>'IsSave','type'=>'','plan'=>true],
    			['id'=>7,'title'=>'删除','key'=>'IsDel','type'=>'','plan'=>true],
    			['id'=>8,'title'=>'导入','key'=>'IsImport','type'=>'','plan'=>true],
    			['id'=>9,'title'=>'导出','key'=>'IsExport','type'=>'','plan'=>true],
    			['id'=>10,'title'=>'打印','key'=>'IsPrint','type'=>'','plan'=>true],
    			['id'=>11,'title'=>'审核','key'=>'IsChecked','type'=>'','plan'=>true],
    			['id'=>12,'title'=>'核准','key'=>'IsApproved','type'=>'','plan'=>true],
    			['id'=>13,'title'=>'拒绝','key'=>'IsReject','type'=>'','plan'=>true],
    			['id'=>14,'title'=>'初始化','key'=>'IsInit','type'=>'','plan'=>true],
    			['id'=>15,'title'=>'清空','key'=>'IsClear','type'=>'','plan'=>true],
    			['id'=>16,'title'=>'返回','key'=>'IsBack','type'=>'','plan'=>true],
    		];


    		if($flag){
    			$temp = [];
    			foreach($field as $item){
    				array_push($temp,"grant.".$item['key']." as ".$this->convert($item['key'],false));
    			}

    			return $temp;
    		}

    		return $field;
    	}

    	/**
    	 * 获取选项option
    	 * @param  Request    $request  [description]
    	 * @param  mixed|null $param    [description]
    	 * @param  array      $disabled [description]
    	 * @param  array      $fields   [description]
    	 * @return [type]               [description]
    	 */
    	protected function getOptionList(Request $request,mixed $param = null,array $disabled = [],array $fields = [])
    	{
    		$tabFields = $this->getList($request,'field','key');
    		if(!$fields){
    			$title = isset($tabFields['title']) ? 'Title' : (isset($tabFields[$this->table.'_name']) ? $tabFields[$this->table.'_name'] : (isset($tabFields[$this->table.'_title']) ? $tabFields[$this->table.'_title'] : ''));
    			if(!$title){
    				return [];
    			}
    			$fields[$title] = $this->option_label;
    			$fields[$this->convert($this->primaryKey)] = $this->option_value;
    		}
    		$field = [];
    		foreach($fields as $k => $v){
    			\array_push($field,"{$k} as {$v}");
    		}
    		$where = $this->getWhere($request);
    		if($this->fieldExists('is_valid')){
    			array_push($where,['IsValid','=',1]);
    		}
    		if(is_array($param) && $param){
    			foreach($param as $k => $v){
    				if($this->isIndexArray($param)){
	    				\array_push($where,[$k,'=',$v]);
	    			}else{
	    				array_push($where,$v);
	    			}
    			}
    		}
    		
    		if($this->layer > 1){
    			array_push($field,'ID as id');
    			array_push($field,'Level as level');
    			array_push($field,'PID as pid');
    			array_push($field,'Number as number');
    		}
    		
    		$object = Db::table($this->table)
    					->select(...$field)
    					->where($where);
    		if($this->layer > 1){
    			$object = $object->orderBy('Level','asc');
    		}
    		if($this->fieldExists('sort')){
    			$object = $object->orderBy('Sort','asc');
    		}
    		$object = $object->get();

    		if($object){
    			$options = [];
    			foreach($object as $k => $v){
    				$option_value = $this->option_value;
    				$object[$k]->disabled = \in_array($v->$option_value,$disabled) ? true : false;
    			}

    			if($this->layer > 1){
    				$object = $this->child($object);
    			}
    			return $object;
    		}

    		return [];
    	}

    	protected function getFormtypeList(Request $request): array
    	{
    		return $this->getList($request,'form');
    	}

    	/**
    	 * [getFormList description]
    	 * @param  Request $request [description]
    	 * @return [type]           [description]
    	 */
    	protected function getFormList(Request $request): array
    	{
    		return [
				['type'=>'option','label'=>'文本','value'=>'1','children'=>[
					['type'=>'input','label'=>'普通文本','value'=>'input'],
					['type'=>'input','label'=>'加密文本','value'=>'password','attrs'=>['type'=>'password']],
					['type'=>'input','label'=>'文本域','value'=>'textarea','attrs'=>['type'=>'textarea']],
					['type'=>'input-number','label'=>'步进器','value'=>'number'],
					['type'=>'tag','label'=>'标签','value'=>'tag'],
					['type'=>'mention','label'=>'提及','value'=>'mention'],
				]],
				['type'=>'option','label'=>'上传','value'=>'2','children'=>[
					['type'=>'upload','label'=>'上传图片','value'=>'upload_img','attrs'=>[]],
					['type'=>'upload','label'=>'上传Logo','value'=>'upload_logo','attrs'=>[]],
					['type'=>'upload','label'=>'上传头像','value'=>'upload_face','attrs'=>[]],
					['type'=>'upload','label'=>'上传轮播图','value'=>'upload_picture','attrs'=>[]],
					['type'=>'upload','label'=>'上传文件','value'=>'upload_file','attrs'=>[]],
					['type'=>'upload','label'=>'上传证件','value'=>'upload_cert','attrs'=>[]],
					['type'=>'upload','label'=>'上传音频','value'=>'upload_voice','attrs'=>[]],
					['type'=>'upload','label'=>'上传视频','value'=>'upload_video','attrs'=>[]],
				]],
				['type'=>'option','label'=>'选择','value'=>'3','children'=>[
					['type'=>'select','label'=>'下拉单选','value'=>'select'],
					['type'=>'select','label'=>'下拉多选','value'=>'select_multi'],
					['type'=>'cascader','label'=>'级联选择','value'=>'cascader'],
					['type'=>'checkbox-group','label'=>'复选框','value'=>'checkbox'],
					['type'=>'radio-group','label'=>'单选框','value'=>'radio'],
				]],
				['type'=>'option','label'=>'日期时间','value'=>'4','children'=>[
					['type'=>'date','label'=>'年月日','value'=>'datetime','attrs'=>[]],
					['type'=>'date','label'=>'年月日时','value'=>'datetimes','attrs'=>[]],
					['type'=>'date','label'=>'年','value'=>'year','attrs'=>[]],
					['type'=>'date','label'=>'月','value'=>'month','attrs'=>[]],
					['type'=>'date','label'=>'日','value'=>'day','attrs'=>[]],
					['type'=>'date','label'=>'时','value'=>'hour','attrs'=>[]],
				]],
				['type'=>'option','label'=>'组合表单','value'=>5,'children'=>[
					['type'=>'group','label'=>'组合值','value'=>'group'],
					['type'=>'multiple','label'=>'多项组合值','value'=>'multiple'],
					['type'=>'table','label'=>'表格组合值','value'=>'table'],
					['type'=>'description','label'=>'描述组合值','value'=>'description'],
				]],
				['type'=>'option','label'=>'编辑器','value'=>'6','children'=>[
					['type'=>'editor','label'=>'富文本','value'=>'editor'],
				]],
				['type'=>'option','label'=>'商品','value'=>'7','children'=>[
					['type'=>'goods_attr','label'=>'商品属性','value'=>'goods_attr'],
					['type'=>'goods_spec','label'=>'商品规格','value'=>'goods_spec'],
				]],
				['type'=>'option','label'=>'城市','value'=>'8','children'=>[
					['type'=>'city','label'=>'省市区','value'=>'city'],
					['type'=>'city','label'=>'省市区街道','value'=>'district'],
					['type'=>'city','label'=>'省市','value'=>'province_city'],
					['type'=>'city','label'=>'省','value'=>'province'],
				]],
				['type'=>'option','label'=>'其它','value'=>'9','children'=>[
					['type'=>'icon','label'=>'图标','value'=>'icon'],
					['type'=>'switch','label'=>'开关','value'=>'switch'],
					['type'=>'color-picker','label'=>'颜色','value'=>'color-picker'],
					['type'=>'sort','label'=>'排序','value'=>'sort'],
					['type'=>'milut','label'=>'多值表单','value'=>'milut_form'],
					['type'=>'divider','label'=>'分割线','value'=>'divider'],
				]]
    		];
    	}

    	/**
    	 * 获取tags列表
    	 * @param  Request $request [description]
    	 * @return [type]           [description]
    	 */
    	protected function getTabsList(Request $request){
    		$tabs = [];
    		if($this->is_tabs){
    			if($this->cate_value){
    				if($this->isIndexArray($this->cate_value)){
    					foreach($this->cate_value as $k => $v){
    						$tabs[][$this->option_label] = $v;
    						$tabs[][$this->option_value] = (int)$k;
    					}
    				}else{
    					foreach($this->cate_value as $k => $v){
    						if(isset($v[$this->option_label])){
    							$tabs[][$this->option_label] = $v[$this->option_label];
    							$tabs[][$this->option_value] = $v[$this->option_value];
    						}elseif(isset($v['title'])){
    							$tabs[][$this->option_label] = $v['title'];
    							$tabs[][$this->option_value] = $v['id'];
    						}
    					}
    				}
    			}
    		}
    		return $tabs;
    	}

    	protected function getExportAction(Request $request){
    		return [
    			['type'=>'input','label'=>'导出数据','prop'=>'export']
    		];
    	}

    	/**
    	 * 获取表单
    	 * @param  Request $request [description]
    	 * @param  integer $id      [description]
    	 * @return [type]           [description]
    	 */
    	protected function getActionList(Request $request,$id = 0): array
    	{
    		if($id){
    			$data = $this->getList($request,$id);
    			if(!$data || !is_object($data)){
    				return $data;
    			}
    		}

    		$action_value = $request->input('action','');
    		if($action_value){
    			$action_method = 'get'.ucfirst($action_value).'Action';
    			if(\method_exists($this,$action_method)){
    				return $this->$action_method($request,$id);
    			}
    		}

    		$fields = $this->getList($request,'fields');
    		if(!$fields || !is_array($fields) || isset($fields['code'])){
    			return '无效的表单字段';
    		}

    		$action = [];
			$exclude = $this->exclude;
			\array_push($exclude,'ID','Number','CreateTime','Hits','Collects','Comments','UserCount');
			if($this->action_exclude){
				$exclude = array_merge($exclude,$this->action_exclude);
			}
			// var_dump($fields[0]);
    		foreach($fields as $field){
    			if($field['is_action'] && !in_array($field['field_name'],$exclude)){
    				$is_action = 1;
    				$prop = $field['map_name'];
    				$comment = $field['comment'];
    				$prefix = $field['prefix'];
    				$suffix = $field['suffix'];
    				$default_value = $field['default_value'] ? $field['default_value'] : '';
    				$hidden = false;
    				$attrs = [];
    				$slot = [];
    				$rules = [];
    				$children = [];
    				$option = [
    					'type'	=> $field['form_type'],
    					'label'	=> $field['comment'],
    					'prop'	=> $prop,
    					'value'	=> $data->$prop ?? ($default_value ? $default_value : ''),
    				];

    				if(!$field['schema']){
						if($this->isField($prop,'title')){
							$rules = ['required'=>true,'message'=>$comment.'不能为空'];
						}

						switch($prop){
							case 'cate_id':
								if(property_exists($this,'cate_value') && $this->cate_value && count($this->cate_value) < 5){
									$option['type'] = 'radio-group';
									$option['value'] = $id ? (int)$data->$prop : ($default_value?((int)$default_value):0);
								}else{
									$option['type'] = 'select';
								}
								$children = $this->getOption('cate');
								$option['placeholder'] = '选择'.$comment;
								$option['value'] = $id ? $data->$prop : $default_value;
								$rules = ['required'=>true,'message'=>'请选择'.$comment];
								break;
							case 'type':
								$option['type'] = 'select';
								$children = $this->getOption('type');
								$option['placeholder'] = '选择'.$comment;
								$rules = ['required'=>true,'message'=>'请选择'.$comment];
								break;
							case 'keyword':
							case 'desc':
							case 'description':
								$option['type'] = 'input';
								$attrs = ['type'=>'textarea'];
								break;
							case 'icon':
								$option['type'] = 'icon';
								break;
							case 'logo':
							case 'pic':
							case 'img':
								$option['type'] = 'upload';
								$option['attrs'] = $this->getUploadOptions('img');
								if($this->fieldExists('Picture')){
									$option['hidden'] = true;
								}
								break;
							case 'picture':
							case 'swiper':
								$option['type'] = 'upload';
								$option['attrs'] = $this->getUploadOptions('card',5);
								break;
							case 'sort':
								$option['type'] = 'input';
								if($this->fieldExists('pid')){
									$pid = $id ? $data->pid : $request->input('pid',0);
									$sort = $sort = $this->getMaxSort($pid);
								}else{
									$sort = $sort = $this->getMaxSort(0);
								}
								$option['value'] = $id ? $data->sort : $sort;
								$rules = ['required'=>true,'message'=>$comment . '不能为空'];
								break;
							case 'content':
								$option['type'] = 'editor';
								$option['attrs'] = $this->getEditorOptions();
								break;
							case 'status':
								$status_value = $this->getOption('status');
								if(count($status_value) > 4){
									$option['type'] = 'select';
									$children = $status_value;
									$option['placeholder'] = '选择'.$comment;
									$option['value'] = $id ? $data->status : ($default_value ? $default_value : $this->default_status_value);
									$rules = ['required'=>true,'message'=>'请选择'.$comment];
								}else{
									$option['type'] = 'radio-group';
									$children = $status_value;
									$option['value'] = $id ? $data->status : ($default_value ? $default_value : $this->default_status_value);
									$rules = ['required'=>true,'message'=>'请选择'.$comment];
								}
								break;
							default:
						}
					}

					if(substr($prop,0,3) == 'is_' && $field['field_type'] == 'tinyint' && $field['length'] == 1){
						$option['type'] = 'switch';
						$option['value'] = $id&&property_exists($data,$prop) ? $data->$prop : ($default_value ? $default_value : 0);
					}

					if($prop == 'user_id' && $comment = '创建人'){
						$is_action = 0;
					}

					if($prop == 'status' && !$id){
						$is_action = 0;
					}

					if($this->fieldExists('StartDate') && $this->fieldExists('EndDate')){
						if($prop == 'end_date'){
							$is_action = 0;
						}
						if($prop == 'start_date'){
							$option['type'] = 'date-picker';
							$option['prop'] = 'date';
							$option['label'] = $this->title.'日期';
							$option['attrs']['type'] = 'daterange';
							$option['attrs']['range-separator'] = 'To';
							$option['attrs']['start-placeholder'] = '开始日期';
							$option['attrs']['end-placeholder'] = '结束日期';
							$option['attrs']['value-format'] = 'YYYY-MM-DD';
							$rules = ['required'=>true,'message'=>'请选择日期'];
							$option['value'] = $id ? [$data->start_date,$data->end_date] : [];;
						}
					}

					if($this->fieldExists('StartTime') && $this->fieldExists('EndTime')){
						if($prop == 'end_time'){
							$is_action = 0;
						}
						if($prop == 'start_time'){
							$option['type'] = 'date-picker';
							$option['prop'] = 'time';
							$option['label'] = $this->title.'时间';
							$option['attrs']['type'] = 'datetimerange';
							$option['attrs']['range-separator'] = 'To';
							$option['attrs']['start-placeholder'] = '开始时间';
							$option['attrs']['end-placeholder'] = '结束时间';
							$option['attrs']['value-format'] = 'YYYY-MM-DD HH:mm';
							$rules = ['required'=>true,'message'=>'请选择时间'];
							$option['value'] = $id ? [$data->start_time,$data->end_time] : [];
						}
					}

					if($this->fieldExists('PID') && $this->fieldExists('Level')){
						if($prop == 'pid'){
							$level = $id ? $data->level : $request->input('level',1);
							$option['type'] = 'select';
							if($id){
								$children = $this->getList($request,'option',[[$this->table.'.Level','=',$data->level-1]]);
							}else{
								$children = $this->getList($request,'option',[[$this->table.'.Level','<',$level]]);
							}
							$attrs['disabled'] = true;
							$option['value'] = $id ? $data->pid : (int)$request->input('pid',0);
							$option['hidden'] = true;
							$rules = ['required'=>$level>1?true:false,'message'=>'请选择'.$comment];
						}elseif($prop == 'level'){
							$level = [];
							for($i=1;$i<=$this->layer;$i++){
								array_push($level,['label'=>$i.'级','value'=>$i]);
							}
							$option['type'] = 'select';
							$children = $level;
							$attrs['disabled'] = true;
							$option['value'] = $id ? $data->level : $request->input('level',1);
							$option['hidden'] = true;
							$rules = ['required'=>true,'message'=>'请选择'.$comment];
						}
					}

					if($prefix){
						$slot['prefix'] = ['value' => $prefix];
					}

					if($suffix){
						$slot['suffix'] = ['value' => $suffix];
					}

    				if($rules){
    					$option['rules'] = $rules;
    				}
    				if($attrs){
    					$option['attrs'] = $attrs;
    				}
    				if($slot){
    					$option['slot'] = $slot;
    				}
    				if($children){
    					$option['children'] = $children;
    				}

    				if($is_action){
    					array_push($action,$option);
    				}
    			}
    		}

    		return $action;
    	}

    	/**
    	 * 获取显示表头
    	 * @param  Request $request [description]
    	 * @return [type]           [description]
    	 */
    	protected function getMapList(Request $request): array
    	{
    		$fields = $this->getList($request,'fields');
    		if(!$fields || !is_array($fields) || isset($fields['code'])){
    			return [];
    		}

    		$action_value = $request->input('action','');
    		if($action_value){
    			$map_method = 'get'.ucfirst($action_value).'Map';
    			if(\method_exists($this,$map_method)){
    				return $this->$map_method($request);
    			}
    		}

    		$map = [];
			$exclude = $this->exclude;
			array_push($exclude,'Keyword','Desc','Description','SourceUrl','Content','Picture','Swiper','PID','Level','Number');
			if($this->map_exclude){
				$exclude = array_merge($exclude,$this->map_exclude);
			}
    		foreach($fields as $field){
    			if($field['is_show'] && !in_array($field['field_name'],$exclude)){
    				$is_show = 1;
    				$type = $field['show_type'];
    				$prop = $field['map_name'];
    				$comment = $field['comment'];
    				$align = $field['align'];
    				$width = $field['width'];
    				$prefix = isset($field['prefix']) ? $field['prefix'] : '';
    				$suffix = isset($field['suffix']) ? $field['suffix'] : '';
    				$color = isset($field['color']) ? $field['color'] : '';
    				$alias = isset($field['alias']) ? $field['alias'] : '';
    				$callback = isset($field['callback']) ? $field['callback'] : '';
    				$field_data = isset($field['data']) ? $field['data'] : '';
    				$format = isset($field['format']) ? $field['format'] : '';

    				switch($prop){
    					case 'cate_id':
    						if(property_exists($this,'cate_value') && $this->cate_value){
    							$type = 'map';
    							$field_data = $this->getOption('cate');
    						}else{
	    						$is_show = 0;
	    						array_push($map,['type'=>'varchar','label'=>'类别','prop'=>'cate_title','align'=>$align]);
	    					}
    						break;
    					case 'icon':
    						$type = 'icon';
    						break;
    					case 'img':
    					case 'logo':
    					case 'pic':
    						$type = 'img';
    						break;
    					case 'website':
    						$type = 'link';
    						break;
    					case 'status':
    						$type = 'map';
    						$field_data = $this->getOption('status');
    						break;
    					case 'create_time':
    						$type = 'varchar';
    						$width = 180;
    						break;
    					default:
    				}

    				if($this->isField($prop,'title') || $this->isField($prop,'full_name') || $this->isField($prop,'en_name')){
    					$width = $field['length']>150 ? ($field['length']>=250 ? 200 : $field['length']) : 150;
    				}

    				if(substr($prop,0,3) == 'is_' && $field['field_type'] == 'tinyint' && $field['length'] == 1){
						$type = 'switch';
					}

    				$option = [
    					'type'  => $type,
    					'label' => $comment,
    					'prop'  => $prop,
    					'align' => $align,
    				];

    				if($this->layer > 1){
    					if($prop == 'id'){
    						$option['align'] = '';
    					}elseif($this->isField($prop,'title')){
	    					$option['align'] = '';
	    					$width = 50 * $this->layer + 30;
    					}
    				}

    				if($this->fieldExists('StartDate') && $this->fieldExists('EndDate')){
    					if($prop == 'end_date'){
    						$is_show = 0;
    					}
    					if($prop == 'start_date'){
    						$option['type'] = 'concat';
    						$option['label'] = $this->title . '日期';
    						$option['delimiter'] = '-';
    						$option['fields'] = ['end_date'];
    						$option['width'] = 250;
    					}
    				}

    				if($this->fieldExists('StartTime') && $this->fieldExists('EndTime')){
    					if($prop == 'end_time'){
    						$is_show = 0;
    					}
    					if($prop == 'start_time'){
    						$option['type'] = 'concat';
    						$option['label'] = $this->title . '时间';
    						$option['delimiter'] = '-';
    						$option['fields'] = ['end_time'];
    						$option['width'] = 300;
    					}
    				}

    				if($width){
    					$option['width'] = $width;
    				}
    				if($prefix){
    					$option['prefix'] = $prefix;
    				}
    				if($suffix){
    					$option['suffix'] = $suffix;
    				}
    				if($color){
    					$option['color'] = $color;
    				}
    				if($alias){
    					$option['alias'] = $alias;
    				}
    				if($callback){
    					$option['callback'] = $callback;
    				}
    				if($field_data){
    					$option['data'] = $field_data;
    				}
    				if($format){
    					$option['format'] = $format;
    				}

    				if($is_show){
    					array_push($map,$option);
    				}
    			}
    		}

			if($this->is_qrcode){
				array_push($map,[
					'type' => 'qrcode',
					'label' => '二维码',
					'prop'  => 'qrcode'
				]);
			}

    		return $map;
    	}

    	/**
    	 * 自动处理数据
    	 * @param  [type] $object [description]
    	 * @return [type]         [description]
    	 */
    	protected function getResultData($object,$flag = false){
    		if(!is_object($object)){
    			return $object;
    		}

    		if($flag){
    			foreach($object as $k => $v){
    				if(\property_exists($object[$k],'create_time')){
    					$object[$k]->create_time = $this->getDateTime($object[$k]->create_time);
    				}
    				if(\property_exists($object[$k],'update_time')){
    					$object[$k]->update_time = $this->getDateTime($object[$k]->update_time);
    				}
    				if(\property_exists($object[$k],'delete_time')){
    					$object[$k]->delete_time = $this->getDateTime($object[$k]->delete_time);
    				}
    				if(\property_exists($object[$k],'picture')){
    					$object[$k]->picture = $this->getDecodeData($object[$k]->picture);
    				}
    			}
    		}else{
    			if(\property_exists($object,'create_time')){
    				$object->create_time = $this->getDateTime($object->create_time);
    			}
    			if(\property_exists($object,'update_time')){
    				$object->update_time = $this->getDateTime($object->update_time);
    			}
    			if(\property_exists($object,'delete_time')){
    				$object->delete_time = $this->getDateTime($object->delete_time);
    			}
    			if(\property_exists($object,'picture')){
    				$object->picture = $this->getDecodeData($object->picture);
    			}
    		}

    		return $object;
    	}

    	public function getProperty($key = 'default_face'){
    		if(property_exists($this,$key)){
    			return $this->$key;
    		}
    		return '';
    	}

    	/**
    	 * 获取路由
    	 * @param  Request $request [description]
    	 * @return [type]           [description]
    	 */
    	protected function getRouter(Request $request){
    		$path = \trim($request->input('router',''));
    		if(!$path){
	    		$path = \trim($request->path(),'/');
	    		if($path){
	    			$path = \explode('/',$path);
	    			$method = isset($path[1]) ? $path[1] : '';
	    			$router = $path[0];
	    			if($method && !in_array($method,['show','index'])){
	    				$router .= '/' . $method;
	    			}
	    			// var_dump('router: '.$router);
	    			return $router;
	    		}
	    	}else{
    			$path = str_replace('_','/',$path);
	    	}
	    	// var_dump('path: '.$path);
	    	return $path;
    	}

    	/**
    	 * 删除数据
    	 * @param  Request $request [description]
    	 * @param  integer $id      [description]
    	 * @return [type]           [description]
    	 */
    	public function del(Request $request,$id = 0){
    		try{
	    		if(!$id){
	    			return 100007;
	    		}

	    		if(is_string($id) && strpos($id,',') !== false){
	    			$id = explode(',',$id);
	    		}
	    		
	    		if(is_array($id)){
	    			$data = $this->getList($request,'ids',$id);
	    			if(!$data){
	    				return '数据不存在或已被删除';
	    			}
	    			if(is_array($data) && isset($data['code']) && $data['code']){
	    				return $data;
	    			}
	    		}else{
		    		$data = $this->getList($request,$id);
		    		if(!$data || !is_object($data)){
		    			return '数据不存在或已被删除';
		    		}
		    	}

	    		// if(class_exists('\app\model\Role\RoleModel')){
		    	// 	$service = new \app\model\Role\RoleModel();
		    	// 	$role = $service->getSignList($request);
		    	// 	if($role && is_object($role) && !$role->is_admin){
		    	// 		return '无操作权限';
		    	// 	}
		    	// }

	    		if($this->fieldExists('IsDel')){
	    			$result = $this->updateData($request,['IsDel'=>1],$id);
	    			// var_dump('result: '.$result);
	    			if($result !== false){
	    				if(\method_exists($this,'setExcute')){
	    					return $this->setExcute($request,$data,'del');
	    				}
	    				return true;
	    			}
	    			return false;
	    		}else{
	    			if(is_array($id)){
	    				$sql = "DELETE FROM `".$this->tab."` WHERE `".$this->convert($this->primaryKey)."` IN (?)";
	    			}else{
	    				$sql = "DELETE FROM `".$this->tab."` WHERE `".$this->convert($this->primaryKey)."` = ?";
	    			}
	    			$result = Db::select($sql,[is_array($id) ? implode(',',$id) : $id]);
	    			if($result !== false){
	    				if(\method_exists($this,'setExcute')){
	    					return $this->setExcute($request,$data,'delete');
	    				}
	    				return true;
	    			}
	    			return false;
	    		}
	    	}catch(\Exception $e){
	    		return $this->getExceptionError($e);
	    	}
    	}

    	public function clear(Request $request){
    		try{
    			// var_dump('table: '.$this->table);
    			if(!$this->truncate){
    				return '该表数据不允许清空';
    			}
    			$result = Db::table($this->table)->truncate();
                if(method_exists($this,'setTruncate')){
                    return $this->setTruncate($request);
                }
                return true;
    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
    	}

    	// 当排序有冲突时自动调整排序
    	protected function adjustSort(Request $request,$sort,$id,$pid = -1){
    		// var_dump('id: '.$id.' pid: '.$pid.' sort: '.$sort);
    		$where = [[$this->table.'.Sort','=',$sort],[$this->table.'.ID','<>',$id]];
    		if($pid >= 0){
    			array_push($where,[$this->table.'.PID','=',$pid]);
    		}
    		$object = Db::table($this->table)
    					->select('Sort as sort')
    					->where($where)
    					->first();
    		// var_dump($object);
    		if($object){
    			if($pid >= 0){
    				$sql = "UPDATE `".$this->tab."` SET Sort = Sort + 1 WHERE PID = ? AND Sort >= ? AND ID <> ?";
    				$result = Db::select($sql,[$pid,$sort,$id]);
    			}else{
    				$sql = "UPDATE `".$this->tab."` SET Sort = Sort + 1 WHERE Sort >= ? AND ID <> ?";
    				$result = Db::select($sql,[$sort,$id]);
    			}

    			return $result !== false ? true : false;

    		}
    		return true;
    	}

    	// 获取配置参数
    	protected function getConfig($key,$type = 'int'){
    		$value = isset($this->config[$key]) ? $this->config[$key] : false;

    		switch(strtolower($type)){
    			case 'int':
    				$value = $value&&is_numeric($value) ? (int)$value : 0;
    				break;
    			case 'bool':
    			case 'boolean':
    				$value = $value ? true : false;
    				break;
    			case 'arr':
    			case 'array':
    				$value = $this->getDecodeData($value);
    				break;
    			default:
    		}

    		return $value;
    	}

        /**
         * 创建随机字符串
         * @param  integer $num [description]
         * @return [type]       [description]
         */
        protected function createCode(int $len = 8,int $type = 1,bool $flag = false): string
        {
            switch($type){
                case '':
                case 1:
                    $str = '123456789';
                    break;
                case 2:
                    $str = $flag ? 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ' : 'abcdefghijklmnopqrstuvwxyz';
                    break;
                case 3:
                    $str = $flag ? 'abcdefghijklmnpqrstuvwxyz123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ' : 'abcdefghijklmnopqrstuvwxyz123456789';
                    break;
                default:
                    $str = '123456789';
            }

            $str = str_shuffle($str);
            $str = str_shuffle($str);
            $str = str_shuffle($str);
            $str = str_shuffle($str);

            $num = '';
            for($i=0;$i<$len;$i++){
                if($i==0){
                    $str = str_shuffle($str);
                    $num .= $str[mt_rand(0,strlen($str)-1)];
                }else{
                    if($type != 2){
                        $str .= '0';
                    }
                    $str = str_shuffle($str);
                    $num .= $str[mt_rand(0,strlen($str)-1)];
                }
            }
            
            return $num;
        }

        // 创建唯一随机字符串
        protected function createUniqueCode(string $field,int $len = 8,int $type = 1,bool $flag = false): string
        {
        	try{
	        	$code = $this->createCode($len,$type,$flag);
	        	if($this->checkExists([$this->convert($field) => $code],0)){
	        		return $this->createUniqueCode($field,$len,$type,$flag);
	        	}

	        	return $code;
	        }catch(\Exception $e){
	        	return $this->getExceptionError($e);
	        }
        }

        /**
         * 生成随机唯一编码
         * @param  Request $request [description]
         * @return [type]           [description]
         */
    	public function getCodeData(Request $request){
    		$len = $request->input('len',($request->input('length',$this->code_length)));
    		$field = $request->input('field','');
    		if(!$field){
    			$field = $this->convert($this->table).'Code';
    		}else{
    			$field = $this->convert($field);
    		}
    		
    		if(!$field || !$this->fieldExists($field)){
    			return ['code' => 1,'msg'=> '无效的编码字段'];
    		}
    		$type = $request->input('type',1);

    		return $this->createUniqueCode($field,$len,$type);
    	}

    	protected function getMaxSort(int $pid = 0,$flag = true): int
    	{
    		try{
    			$object = $flag ? Db::table($this->table)->where('PID',$pid)->max('Sort') : Db::table($this->table)->max('Sort');
    			return $object ? ($object + 1) : 1;
    		}catch(\Exception $e){
    			return 1;
    		}
    	}

        protected function getMaxidList(Request $request)
        {
            try{
            	if($this->layer < 2 || $this->digit < 2){
            		return [];
            	}

            	$field = ['ID as id'];
            	$object = Db::table($this->table)
            				->select(...$field)
            				->where('IsDel',0)
            				->get();
            	$option = [];
            	if($object){
            		foreach($object as $item){
            			$option[$item->id] = $item->id * (10 ** $this->digit);
            		}
            		if($option){
            			$sql = "SELECT PID as pid,MAX(ID) as max_id FROM `".$this->tab."` WHERE PID > 0 GROUP BY ID";
            			$result = Db::select($sql);
            			if($result){
            				foreach($result as $res){
            					if(isset($option[$res->pid])){
            						$option[$res->pid] = $res->max_id;
            					}
            				}
            			}
            		}
            	}

            	return $option;
            }catch(\Exception $e){
            	// var_dump('get maxid error: ',$this->getExceptionError($e));
                return [];
            }
        }

    	/**
    	 * 获取层级最大ID
    	 * @param  integer $pid   [description]
    	 * @param  integer $digit [description]
    	 * @return [type]         [description]
    	 */
    	protected function getMaxid($pid = 0,$digit = 2){
    		try{
    			$digit = $digit ? $digit : $this->digit;
    			if(!$pid){
		            $first_id = (int)('1'.str_repeat('0', $digit-1));
		        }else{
		            $first_id = ($pid * ((int)('1'.str_repeat('0', $digit)))) + 1;
		        }
    			$object = Db::table($this->table)
    							->select($this->convert($this->primaryKey).' as id')
    							->where('PID',$pid)
    							->orderBy($this->convert($this->primaryKey),'desc')
    							->limit(1)
    							->first();
    			return ($object&&$object->id) ? $object->id+1 : $first_id;
    		}catch(\Exception $e){
    			return 0;
    		}
    	}

    	protected function tree(mixed $data): mixed
    	{
    		if(!$data || $this->layer == 1){
        		return [];
        	}

        	$arr = [];
        	$pid = $data[0]->pid ?? 0;
        	foreach($data as $item){
        		if($item->pid == $pid){
        			array_push($arr,$item);

        			if($item->number){
        				foreach($data as $values){
        					if($values->pid == $item->id){
        						array_push($arr,$values);

        						if($values->number){
        							foreach($data as $value){
        								if($value->pid == $values->id){
        									array_push($arr,$value);

        									if($value->number){
        										foreach($data as $val){
        											if($val->pid == $value->id){
        												array_push($arr,$val);

        												if($val->number){
        													foreach($data as $v){
        														if($v->pid == $val->id){
        															array_push($arr,$v);
        														}
        													}
        												}
        											}
        										}
        									}
        								}
        							}
        						}
        					}
        				}
        			}
        		}
        	}

        	return $arr;
    	}


    	protected function child(mixed $data): mixed
    	{
    		if(!$data || $this->layer == 1){
        		return $data;
        	}

        	$arr = [];
        	$pid = $data[0]->pid ?? 0;

        	for($i=0;$i<count($data);$i++){
        		if(!property_exists($data[$i],'label') && property_exists($data[$i],'title')){
        			$data[$i]->label = $data[$i]->title;
        		}
        		if(!property_exists($data[$i],'value') && property_exists($data[$i],'id')){
        			$data[$i]->value = $data[$i]->id;
        		}
        	}

        	foreach($data as $item){
        		if($item->pid == $pid){

        			if($item->number){
        				$child1 = [];
        				foreach($data as $values){
        					if($values->pid == $item->id){
        						if($values->number){
        							$child2 = [];
        							foreach($data as $value){
        								if($value->pid == $values->id){
        									if($value->number){
        										$child3 = [];
        										foreach($data as $val){
        											if($val->pid == $value->id){
        												if($val->number){
        													$child4 = [];
        													foreach($data as $v){
        														if($v->pid == $val->id){
        															array_push($child4,$v);
        														}
        													}
        													if($child4){
        														$val->children = $child4;
        													}
        												}

        												array_push($child3,$val);
        											}
        										}
        										if($child3){
        											$value->children = $child3;
        										}
        									}

        									array_push($child2,$value);
        								}
        							}
        							if($child2){
        								$values->children = $child2;
        							}

        						}
        						array_push($child1,$values);
        					}
        				}
        				if($child1){
        					$item->children = $child1;
        				}
        			}

        			array_push($arr,$item);
        		}
        	}

        	return $arr;
    	}

    	protected function getTokenData(Request $request,$key = ''){
    		$token = TokenService::getTokenData($request);
    		if($key){
    			if($key === 'is_admin'){
    				$rid = $token->rid;
    				$obj = Db::table('role')->select('IsAdmin')->where('ID',$rid)->first();
    				if($obj && is_object($obj)){
    					return $obj->IsAdmin > 0 ? true : false;
    				}
    				return false;
    			}elseif($key === 'is_super'){
    				$rid = $token->rid;
    				$obj = Db::table('role')->select('IsAdmin')->where('ID',$rid)->first();
    				if($obj && is_object($obj)){
    					return $obj->IsAdmin > 1 ? true : false;
    				}
    				return false;
    			}else{
    				return \property_exists($token,$key) ? $token->$key : false;
    			}
    		}

    		return $token;
    	}

    	/**
    	 * 获取token数据
    	 * @param  Request $request [description]
    	 * @param  string  $key     [description]
    	 * @return [type]           [description]
    	 */
    	protected function getTokenList(Request $request,$key = ''): mixed
    	{
    		return $this->getTokenData($request,$key);
    	}

    	/**
    	 * 获取表字段列表
    	 * @param  Request $request [description]
    	 * @return [type]           [description]
    	 */
    	protected function getFieldsList(Request $request): array
    	{
    		if(!$this->table || !$this->tab){
    			return [];
    		}

    		$fields = [];
    		if($this->is_field){
    			$field = [
    				'field.ID as id',
    				'TableID as table_id',
    				'table.Title as table',
    				'Comment as comment',
    				'FieldName as field_name',
    				'field.MapName as map_name',
    				'Placeholder as placeholder',
    				'FieldType as field_type',
    				'field.Length as length',
    				'IsPrimaryKey as is_primary_key',
    				'IsKey as is_key',
    				'IsUnique as is_unique',
    				'IsMust as is_must',
    				'IsShow as is_show',
    				'IsAdd as is_add',
    				'IsMod as is_mod',
    				'IsSearch as is_search',
    				'IsAction as is_action',
    				'ShowType as show_type',
    				'FormType as form_type',
    				'field.Width as width',
    				'field.Align as align',
    				'DefaultValue as default_value',
    				'Children as children',
    				'Rules as rules',
    				'Prefix as prefix',
    				'Suffix as suffix',
    				'Prompt as prompt',
    				'CallbackField as callback_field',
    				'CallbackKey as callback_key',
    				'CallbackTitle as callback_title',
    				'field.After as after',
    				'field.Status as status',
    				'field.Sort as sort',
    			];
    			$where = [
    				['field.IsDel','=',0],
    				['table.IsDel','=',0],
    				['table.Title','=',$this->table]
    			];
    			$object = Db::table('field')
    						->join('table','TableID','=','table.ID')
    						->select(...$field)
    						->where($where)
    						->get();

    			if($object){
    				foreach($object as $k => $v){
    					$object[$k]->schema = true;
    				}
    				$fields = $object->toArray();
    			}
    		}

    		if(!is_array($this->fields) || !$this->fields){
	    		$object = Db::select("SHOW FULL FIELDS FROM `".$this->tab."`");
	    		if(!$object){
	    			return [];
	    		}

				for($i=0;$i<count($object);$i++){
					$type = $object[$i]->Type;
					$comment = $object[$i]->Comment;
					$placeholder = $object[$i]->Comment;
					$field = $object[$i]->Field;
					$prop = $this->convert($object[$i]->Field,false);
					$length = 0;
					$showType = 'varchar';
					$formType = 'input';
					$is_show = 1;
					$is_add = 1;
					$is_mod = 1;
					$is_must = 0;
					$is_search = 0;
					$is_action = 1;
					$rules = [];
					$children = [];
					if(\strpos($type,'(') !== false){
						$temp = \explode('(',$object[$i]->Type);
						if(isset($temp[0])){
							$type = $temp[0];
						}
						if(isset($temp[1])){
							$length = (int)\substr($temp[1],0,\strlen($temp[1])-1);
						}
					}
					
					array_push($fields,[
						'id' 			 => $i+1,
						'schema'		 => false,
						'table_id' 		 => 0,
						'table' 		 => $this->table,
						'comment' 		 => $comment,
						'field_name' 	 => $field,
						'map_name' 		 => $prop,
						'placeholder' 	 => $placeholder,
						'field_type' 	 => $type,
						'length' 		 => $length,
						'is_primary_key' => $object[$i]->Key == 'PRI' ? 1 : 0,
						'is_key' 		 => $object[$i]->Key == 'MUL' ? 1 : 0,
						'is_unique' 	 => 0,
						'is_must' 		 => $is_must,
						'is_show' 		 => $is_show,
						'is_add' 		 => $is_add,
						'is_mod' 		 => $is_mod,
						'is_search' 	 => $is_search,
						'is_action' 	 => $is_action,
						'show_type' 	 => $showType,
						'form_type' 	 => $formType,
						'width' 		 => 0,
						'align' 		 => $this->align,
						'default_value'  => $object[$i]->Default,
						'rules' 		 => $rules,
						'children' 		 => $children,
						'prefix' 		 => '',
						'suffix' 		 => '',
						'prompt' 		 => '',
						'callback_field' => '',
						'callback_key'   => '',
						'callback_title' => '',
						'after' 		 => '',
						'status' 		 => 1,
						'sort' 		 	 => 1,
					]);
				}

				$this->fields = $fields;
			}
			return $this->fields;
    	}

    	/**
    	 * 获取查询字段
    	 * @param  Request $request [description]
    	 * @return [type]           [description]
    	 */
    	protected function getFieldList(Request $request,$flag = false): array
    	{
    		$fields = $this->getList($request,'fields');
    		// var_dump('get fields: ',$fields);
    		if(!$fields || !is_array($fields) || isset($fields['code'])){
    			return [];
    		}

    		foreach($fields as $k => $v){
    			if(in_array($v['field_name'],$this->exclude)){
    				unset($fields[$k]);
    			}
    		}

    		if($flag === true){
    			$field = [];
    			foreach($fields as $f){
    				if($f['is_show']){
    					array_push($field,$this->table . '.' . $f['field_name'] . ' as ' . $f['map_name']);
    				}
    			}
    			// ['table.Field as field']
    			return $field;
    		}elseif($flag === false){
    			$field = [];
    			foreach($fields as $f){
    				if($f['is_show']){
    					array_push($field,$f['field_name'] . ' as ' . $f['map_name']);
    				}
    			}
    			// ['Field as field']
    			return $field;
    		}elseif($flag === 'key'){
    			$field = [];
    			foreach($fields as $f){
    				$field[$f['map_name']] = $f['field_name'];
    			}
    			// ['field' => 'Field']
    			return $field;
    		}else{
    			return $fields;
    		}
    	}

    	/**
    	 * 获取查询条件参数
    	 * @param  Request $request [description]
    	 * @return [type]           [description]
    	 */
    	protected function getWhere(Request $request): array
    	{
    		$where = [];
    		if($this->fieldExists('IsDel')){
    			array_push($where,[$this->table.'.IsDel','=',0]);
    		}
    		// if($this->fieldExists('Status')){
    		// 	if($this->status_value){
    		// 		for($i=0;$i<$this->status_value;$i++){
    		// 			if(isset($this->status_value[$i]['value']) && $this->status_value[$i]['value'] === 1){
    		// 				array_push($where,[$this->table.'.Status','=',1]);
    		// 				break;
    		// 			}
    		// 		}
    		// 	}
    		// }

    		return $where;
    	}

    	/**
    	 * 获取option选项
    	 * @param  [type] $data [description]
    	 * @param  string $type [description]
    	 * @return [type]       [description]
    	 */
    	protected function getOption($type = 'cate'){
    		$data = [];
    		switch($type){
    			case 'cate':
    				$data = $this->getCateValue();
    				break;
    			case 'type':
    				$data = $this->getTypeValue();
    				break;
    			case 'status':
    				$data = $this->getStatusValue();
    				break;
    			default:
    		}
    		if(!$data){
    			return [];
    		}

    		$options = [];
    		if($this->isIndexArray($data)){
	    		foreach($data as $k => $v){
	    			$option[$this->option_label] = $v;
	    			$option[$this->option_value] = (int)$k;
	    			array_push($options,$option);
	    		}
	    	}else{
	    		foreach($data as $k => $v){
	    			array_push($options,$v);
	    		}
	    	}

	    	return $options;
    	}

    	protected function setIncrement($field,$id = 0,$val = 1){
    		try{
    			if(!$id){
    				return false;
    			}
    			if(is_array($id)){
    				$where = "";
    				foreach($id as $k => $v){
    					$where .= "`{$k}` = {$v} AND ";
    				}
    				$where = rtrim($where," AND ");
    			}else{
    				$where = "`".$this->convert($this->primaryKey)."` = ".$id;
    			}
    			$sql = "UPDATE `".$this->tab."` SET `{$field}` = `{$field}` + ? WHERE ".$where;
    			// var_dump('increment sql: '.$sql);
    			$result = Db::update($sql,[$val]);
    			return $result !== false ? true : false;
    		}catch(\Exception $e){
    			return false;
    		}
    	}

    	protected function setDecrement($field,$id = 0,$val = 1){
    		try{
    			if(!$id){
    				return false;
    			}
    			if(is_array($id)){
    				$where = "";
    				foreach($id as $k => $v){
    					$where .= "`{$k}` = {$v} AND ";
    				}
    				$where = rtrim($where," AND ");
    			}else{
    				$where = "`".$this->convert($this->primaryKey)."` = ".$id;
    			}
    			$sql = "UPDATE `".$this->tab."` SET `{$field}` = `{$field}` - ? WHERE ".$where;
    			// var_dump('decrement sql: '.$sql);
    			$result = Db::update($sql,[$val]);
    			return $result !== false ? true : false;
    		}catch(\Exception $e){
    			return false;
    		}
    	}

    	public function setSwitch(Request $request,$id = 0){
    		try{
    			if(!$id){
    				return 100007;
    			}

    			$key = $request->post('prop',$request->post('prop',''));
    			if(!$key){
    				return '无效的数据字段';
    			}
    			if(!$this->fieldExists($key)){
    				return '无效的数据字段';
    			}

    			$field = $this->convert($key) . ' as ' . $key;
    			$where = [
    				['IsDel','=',0],
    				[$this->convert($this->primaryKey),'=',$id],
    			];
    			$object = Db::table($this->table)
    						->select($field)
    						->where($where)
    						->first();
    			if($object){
    				$sql = "UPDATE `".$this->tab."` SET `".$this->convert($key)."` = ? WHERE `".$this->convert($this->primaryKey)."` = ?";
    				$result = Db::update($sql,[!$object->$key,$id]);
    				if($result !== false){
    					return ['code' => 0,'msg' => 'success'];
    				}

    				return '操作失败';
    			}

    			return '数据不存在或已被删除';
    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
    	}

    	/**
    	 * 编辑单个元素
    	 * @param Request $request [description]
    	 * @param integer $id      [description]
    	 */
    	public function setEditor(Request $request,$id = 0){
    		try{
    			if(!$id){
    				return 100007;
    			}

    			$obj = $this->getList($request,$id);
    			if(!$obj || !is_object($obj)){
    				return '数据不存在或已被删除';
    			}

    			$post = $request->post();
    			if(!$post){
    				return '无效的数据参数';
    			}

    			$data = [];
    			$flag = false;
    			foreach($post as $key => $val){
    				if($this->fieldExists($key)){
    					$data[$this->convert($key)] = $val;
    					if($val != $obj->$key){
    						$flag = true;
    					}
    				}
    			}

    			if(!$flag){
    				return ['code' => 0,'msg' => 'success'];
    			}

    			if(!$data){
    				return '无效的数据参数';
    			}

    			$result = $this->updateData($request,$data,$id);
    			if($result !== false){
    				return ['code' => 0,'msg' => 'success'];
    			}


    			return '数据修改失败';
    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
    	}

    	/**
    	 * 统一评分
    	 * @param Request $request [description]
    	 * @param [type]  $id      [description]
    	 */
    	public function setRating(Request $request,$id){
    		try{
    			$id = $id ? $id : $request->input('id',0);
    			$cate_id = $request->post('cate_id',1);
    			$value = $request->post('value');

    			$table = 'User';
    			if($this->fieldExists('CateID')){
    				if(!$cate_id || !is_numeric($cate_id) || !in_array((int)$cate_id,[1,2])){
    					return '无效的评分类型';
    				}

    				$table = (int)$cate_id === 1 ? 'User' : 'Store';
    			}

    			if(!$id || !is_numeric($id) || $id < 0){
    				return 100007;
    			}

    			$_class_name = $this->getClassName($table);
    			if($_class_name){
    				$service = new $_class_name();
    				$obj = $service->getList($request,$id);
    				if(!$obj || !is_object($obj)){
    					return '无效的评分对象';
    				}
    			}

    			if(is_numeric($value) || $value < $this->min_rating || $value > $this->max_rating){
    				return '无效的评分值';
    			}

    			$data = [
    				'CateID' => $cate_id,
    				'Value'  => $value
    			];
    			if($cate_id === 1){
    				$data['UserID'] = $id;
    			}else{
    				$data['StoreID'] = $id;
    			}

    			return $this->insertData($request,$data);
    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
    	}

    	/**
    	 * 统一点赞
    	 * @param Request $request [description]
    	 * @param integer $id      [description]
    	 */
    	public function setLikes(Request $request,$id = 0){
    		try{
    			$id = $id ? $id : $request->input('id',0);
    			$cate_id = $request->post('cate_id',1);

    			$table = 'User';
    			if($this->fieldExists('CateID')){
    				if(!$cate_id || !is_numeric($cate_id) || !in_array((int)$cate_id,[1,2])){
    					return '无效的点赞类型';
    				}

    				$table = (int)$cate_id === 1 ? 'User' : 'Store';
    			}

    			if(!$id || !is_numeric($id) || $id < 0){
    				return 100007;
    			}

    			$_class_name = $this->getClassName($table);
    			if($_class_name){
    				$service = new $_class_name();
    				$obj = $service->getList($request,$id);
    				if(!$obj || !is_object($obj)){
    					return '无效的点赞对象';
    				}
    			}

    			$data = ['CateID' => $cate_id];
    			if($cate_id === 1){
    				$data['UserID'] = $id;
    			}else{
    				$data['StoreID'] = $id;
    			}

    			$result = $this->insertData($request,$data);
    			if($result !== false){
    				$table = $cate_id === 1 ? 'user' : 'store';
    				Db::table($table)->where('ID',$id)->increment('Likes');

    				return true;
    			}

    			return false;
    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
    	}

    	public function setStatus(Request $request,$id = 0){
    		try{

    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
    	}

    	// 审核
    	public function setChecked(Request $request){
    		try{
    			$id = $request->post('id',0);
    			$value = $request->post('value');
    			$type = $request->post('type','');
    			$content = trim($request->post('content',''));

    			if(!$id || !is_numeric($id) || $id <= 0){
    				return 100007;
    			}
    			$obj = $this->getList($request,$id);
    			if(!$obj || !is_object($obj)){
    				return 100007;
    			}

    			if($obj->status > 0){
    				return '该记录已通过'.($obj->status>1?'核准':'审核').'，无需重复操作!';
    			}

    			if(!in_array($value,[0,1])){
    				return '无效的审核值';
    			}

    			if(!in_array($type,['check','approve'])){
    				return '无效的审核参数';
    			}

    			if(!$this->fieldExists('status')){
    				return '无效的审核字段';
    			}
    			$is_content = $this->fieldExists('content');

    			switch($obj->status){
    				case -2:
    					$value = $value ? 1 : -2;
    					break;
    				case -1:
    					$value = $value ? 1 : -1;
    					break;
    				case 0:
    					$value = $value ? 1 : -1;
    					break;
    				default:
    			}

    			$data = [
    				'Status'  => $value,
    			];
    			if($is_content){
    				$data['Content'] = $content;
    			}

    			$result = $this->updateData($request,$data,$id);
    			$rid = Db::getPdo()->lastInsertId();
    			if($result !== false){
    				if(\method_exists($this,'setChecked')){
    					return $this->setChecked($request,$id);
    				}
    				return false;
    			}
    			return false;

    			return $result !== false ? true : false;
    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
    	}

        protected function getNextValue($val,$data = []){
        	if(!$data){
        		return '';
        	}

        	$result = '';
        	foreach($data as $item){
        		if($item['value'] === $val){
        			$result = isset($item['next']) ? $item['next'] : '';
        			break;
        		}
        	}

        	return $result;
        }

    	protected function getOptionsData($data,$type = 'select'){
    		return $this->getFieldOption($data,$type);
    	}

        protected function getFieldOption($data,$type = 'select'){
            if(!$data || !is_array($data) || !count($data)){
                return [];
            }
            
            if($this->isIndexArray($data)){
            	$options = [];
            	foreach($data as $k => $v){
            		$option[$this->option_label] = $v;
            		$option[$this->option_value] = (int)$k;
            		array_push($options,$option);
            	}
            	return $options;
            }

            return $data;
        }

    	/**
    	 * 获取类别选项值
    	 * @return [type] [description]
    	 */
    	protected function getCateValue(){
    		if(\property_exists($this,'cate_value')){
    			if($this->isIndexArray($this->cate_value)){
    				$options = [];
    				foreach($this->cate_value as $k => $v){
    					array_push($options,['label'=>$v,'value'=>(int)$k]);
    				}
    				return $options;
    			}
    			return $this->cate_value;
    		}

    		$cate_table = $this->cate_table ? $this->cate_table : $this->table . '_cate';
    		$_class_name = $this->getClassName($this->convert($cate_table));
    		if($_class_name){
	    		$service = new $_class_name();
	    		return $service->getList(\request(),'option');
	    	}
	    	return [];
    	}

    	/**
    	 * 获取类型选项值
    	 * @return [type] [description]
    	 */
    	protected function getTypeValue(){
    		if(\property_exists($this,'type_value')){
    			if($this->isIndexArray($this->type_value)){
    				$options = [];
    				foreach($this->type_value as $k => $v){
    					array_push($options,['label'=>$v,'value'=>(int)$k]);
    				}
    				return $options;
    			}
    			return $this->type_value;
    		}
    		return [];
    	}

    	/**
    	 * 获取状态选项值
    	 * @return [type] [description]
    	 */
    	protected function getStatusValue(){
    		if(\property_exists($this,'status_value')){
    			if($this->isIndexArray($this->status_value)){
    				$options = [];
    				foreach($this->status_value as $k => $v){
    					array_push($options,['label'=>$v,'value'=>(int)$k]);
    				}
    				return $options;
    			}
    			return $this->status_value;
    		}
    		return [];
    	}

        protected function getFieldValue($val,$type = 'status',$key = 'label'){
        	if(is_array($type)){
        		$arr = $type;
        	}else{
        		$property = $type.'_value';
        		if(property_exists($this,$property)){
        			$arr = $this->$property;
        		}else{
        			$arr = [];
        		}
        	}
        	
        	if(!$arr){
        		return '';
        	}

        	if($this->isIndexArray($arr)){
            	return isset($arr[$val]) ? $arr[$val] : '未知';
        	}else{
        		foreach($arr as $item){
        			if($item['value'] == $val){
        				return $item[$key];
        				break;
        			}
        		}

        		return '';
        	}
        }

        protected function getPropertyValue(mixed $value = 0,string $key = ''): string
        {
        	if($key && property_exists($this,$key)){
        		if(count($this->$key) === count($this->$key, COUNT_RECURSIVE)){
        			return isset($this->$key[$value]) ? $this->$key[$value] : $value;
        		}else{
        			$val = '';
        			foreach($this->$key as $item){
        				if($item['id'] === $value){
        					if(isset($item['color'])){
        						$val = '<span style="color:'.$item['color'].';">'.$item['title'].'</span>';
        					}else{
        						$val = $item['title'];
        					}
        				}
        			}

        			return $val;
        		}
        	}

        	return $value;
        }

    	// 是否存在某个字段
    	protected function fieldExists(string $field = '',array $data = []): bool
    	{
    		$data = $data ? $data : $this->getList(\request(),'field','key');
    		// var_dump($data);
			if(!$data || !count($data)){
				return false;
			}

			return isset($data[$this->convert($field,false)]) ? true : false;
    	}


		// 检查字段值是否已存在
		public function checkExists($data,$id = 0){
			try{
				$where = [];
				foreach($data as $key => $val){
					array_push($where,[$this->convert($key),'=',$val]);
				}
				if($id){
					array_push($where,[$this->convert($this->primaryKey),'<>',$id]);
				}
				$count = Db::table($this->table)
							->where($where)
							->count();
				return $count ? true : false;
			}catch(\Exception $e){
				return true;
			}
		}

		/**
		 * 核验验证码
		 * @param  Request $request [description]
		 * @param  [type]  $code    [description]
		 * @param  string  $type    [description]
		 * @return [type]           [description]
		 */
		public function checkValidateCode(Request $request,$code,$type = 'numcode'){
			try{
				$captcha = '';
				$key = $this->prefix.$type;
				if(class_exists('Redis') && ($request->host(true) !== 'localhost' || $request->host(true) !== '127.0.0.1')){
					switch($type){
						case 'numcode':
							$token = $request->header('token',$request->input('token'));
							break;
						case 'smscode':
							$token = $request->header('token',$request->input('mobile'));
							break;
						case 'emailcode':
							$token = $request->header('token',$request->input('email'));
							break;
						default:
							$token = $request->header('token',$request->input('token'));
					}
					
					if($token && $token != "[object Promise]"){
						$key = $key . '_' . $token;
						$captcha = \support\Redis::get($key);
						// var_dump('get captcha by redis: '.$captcha);
					}
				}
				if(!$captcha){
					$captcha = $request->session()->get($key);
					// var_dump('get captcha by session: '.$captcha);
				}

				if($captcha){
					// var_dump('code: '.strtolower($code).' captcha: '.strtolower($captcha).' token: '.$token);
					if(!$code || !$captcha || empty($code) || empty($captcha) || strtolower($code) !== strtolower($captcha)){
						return false;
					}

					$request->session()->forget($key);
					if(class_exists('Redis') && ($request->host(true) !== 'localhost' || $request->host(true) !== '127.0.0.1')){
						\support\Redis::del($key);
					}
					return true;
				}
				return false;
			}catch(\Exception $e){
				// var_dump('error: '.$e->getMessage());
				return false;
			}
		}

        public function makePassword($password){
        	return \password_hash($password, PASSWORD_BCRYPT,["cost" => 12]);
        }

        public function checkPassword($password,$checkpwd){
        	return \password_verify($password,$checkpwd);
        }

        public function getIPAddress(Request $request,$flag = true){
        	$result = \dackou\Ip::getIp($request);
        	// var_dump('ip: ',$result);
        	if(!$result || !is_array($result) || isset($result['code']) || !isset($result['country'])){
        		return '';
        	}
        	return $flag ? $result['country'] : $result;
        }

        public function getCity(Request $request){
        	$res = \dackou\Ip::getIp($request);
        	// var_dump($res);
        }

    	/**
    	 * 获取分页参数
    	 * @param  Request $request [description]
    	 * @return [type]           [description]
    	 */
    	protected function getLimit(Request $request,int $page = 1,int $pagesize = 10): array
    	{
    		$page = $request->input($this->pagekey,$page);
            if(empty($page) || !is_numeric($page) || !$page){
                $page = 1;
            }
            
            $pagesize = $request->input($this->pagesize_key,$pagesize);
            if(empty($pagesize) || !is_numeric($pagesize) || !$pagesize){
                $pagesize = 10;
            }
            $offset = ((int)$page - 1) * (int)$pagesize;
            $limit = (int)$pagesize;
            return [$offset,$limit];
    	}

		/**
		 * 获取参数信息
		 * @param  string $key [description]
		 * @return [type]      [description]
		 */
    	protected function getOptionData($key = ''): array
    	{
    		if(!$key || \trim($key) == '' || \is_null($key)){
    			return [];
    		}

    		if(!\is_array($key)){
    			$key = \explode(',',$key);
    		}
    		try{
                $field = [
                    "option.ID as id",
                    "option.Title as title",
                    "option.Key as key",
                    "option.ValueType as value_type",
                    "option.DefaultValue as default_value",
                    "option.Value as value"
                ];             
                $object = Db::table("option")
                                ->select(...$field)
                                ->where([['Level','=',2],['FormType','<>','divider']])
                                ->whereIn('Tag',$key)
                                ->get(); 
                // var_dump($object);
                $result = [];
                if($object){
                    foreach($object as $item){
                    	$value = $item->value;

                    	switch($item->value_type){
                    		case 2:
                    		case 3:
                    			$value = $value ? (int)$value : 0;
                    			break;
                    		case 4:
                    			$value = $value ? \round($value,1) : 0;
                    			break;
                    		case 5:
                    			$value = $value ? \round($value,2) : 0;
                    			break;
                    		case 6:
                    			$value = $value ? \round($value,3) : 0;
                    			break;
                    		case 7:
                    			$value = $value ? \round($value,4) : 0;
                    			break;
                    		case 8:
                    			$value = $value ? ($value ? 1 : 0) : 0;
                    			break;
                    		case 9:
                    			$value = $value ? ($value ? true : false) : false;
                    			break;
                    		case 10:
                    			$value = $value ? $this->getDecodeData($value) : [];
                    			break;
                    		case 11:
                    			$value = $value ? $this->getDecodeData($value,true) : [];
                    			break;
                    		default:
                    	}
                        $result[$item->key] = $value;
                    }
                }

                return $result;
            }catch(\Exception $e){
                return [];
            }
    	}

    	protected function getDiffTime($time,$flag = false){
    		if(!is_numeric($time)){
    			$time = strtotime($time);
    		}

    		// 计算时间差（秒）
		    $current_time = time();
		    $diff_seconds = $current_time - $time;

		    if(!$flag){
		    	return $diff_seconds;
		    }

		    // 如果时间戳是未来的，返回"刚刚"
		    if($diff_seconds < 0){
		        return '刚刚';
		    }

		    // 按从大到小顺序判断（先判断较大的时间段）
		    $time_levels = [
		        63115200 => '2年前',  // 2年（按365.25天计算，更精确）
        		31557600 => '1年前',  // 1年（按365.25天计算）
		        15552000 => '6个月前',
		        12960000 => '5个月前',
		        7776000 => '3个月前',
		        5184000 => '2个月前',
		        2592000 => '1个月前',
		        1296000 => '15天前',
		        864000 => '10天前',
		        432000 => '5天前',
		        259200 => '3天前',
		        172800 => '2天前',
		        86400 => '1天前',
		        36000 => '10小时前',
		        18000 => '5小时前',
		        10800 => '3小时前',
		        7200  => '2小时前',
		        3600  => '1小时前',
		        1800  => '半小时前',
		        600   => '10分钟前',
		        300   => '5分钟前',
		        60    => '1分钟前'
		    ];

		    foreach($time_levels as $seconds => $text) {
		        if ($diff_seconds >= $seconds) {
		            return $text;
		        }
		    }
		    
		    // 小于1分钟的情况
		    if ($diff_seconds < 60) {
		        if ($diff_seconds <= 0) {
		            return '刚刚';
		        }
		        return $diff_seconds . '秒前';
		    }
		    
		    return '刚刚';
    	}

    	protected function getCityName($province_id = 0,$city_id = 0,$district_id = 0,$street_id = 0){
    		$cityname = '';
    		if($province_id){
                $province = Db::table('city')->select('Title as title')->where('ID',$province_id)->first();
                if($province && is_object($province)){
                    $cityname .= $province->title;

                    if($city_id){
                        $city = Db::table('city')->select('Title as title')->where('ID',$city_id)->first();
                        if($city && is_object($city)){
                            $cityname .= $city->title;

                            if($district_id){
                                $district = Db::table('city')->select('Title as title')->where('ID',$district_id)->first();
                                if($district && is_object($district)){
                                    $cityname .= $district->title;

                                    if($street_id){
                                        $street = Db::table('city')->select('Title as title')->where('ID',$street_id)->first();
                                        if($street && is_object($street)){
                                            $cityname .= $street->title;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            return $cityname;
    	}

    	/**
    	 * 判断是否索引数组
    	 * @param  [type]  $arr [description]
    	 * @return boolean      [description]
    	 */
    	protected function isIndexArray($arr){
    		if(!is_array($arr)){
    			return false;
    		}
    		return count($arr) === count($arr, COUNT_RECURSIVE);
    	}

    	/**
    	 * 数组转对象
    	 * @param  [type] $arr [description]
    	 * @return [type]      [description]
    	 */
    	protected function arrayToObject($arr){
    		$object = new \stdClass();
			foreach ($arr as $key => $value) {
			    $object->$key = $value;
			}
			return $object;
    	}

    	/**
    	 * 判读是否主字段
    	 * @param  [type]  $field [description]
    	 * @return boolean        [description]
    	 */
    	protected function isField($field,$type = 'title'){
    		$flag = false;
    		$field = $this->convert($field);
    		$table = $this->convert($this->table);
    		switch($type){
    			case 'title':
    				if($field == 'Title' || $field == $table || $field == $table . 'Name' || $field == $table . 'Title'){
		    			$flag = true;
		    		}
		    		break;
		    	case 'code':
		    		if($field == $table . 'Code'){
		    			$flag = true;
		    		}
		    		break;
		    	case 'full_name':
		    		$flag = \strpos($field,'FullName') !== false ? true : false;
		    		break;
		    	case 'end_name':
		    		$flag = \strpos($field,'EnName') !== false ? true : false;
		    		break;
		    	default:
		    		$flag = $field == $this->convert($type) ? true : false;
    		}
    		
    		return $flag;
    	}

    	// 大小写转
		protected function convert(string $str,bool $flag = true): string
		{
			$map = [
				'AccountID' 	 => 'uid',
				'UserName'  	 => 'username',
				'NickName'  	 => 'nickname',
				'Authentication' => 'password',
				'OrderCode'  	 => 'order_code',
				'Description'    => 'desc',
				'OpenidWechat'   => 'openid',
				'UnionidWechat'  => 'unionid',
				'CreateIP'  	 => 'create_ip',
			];

			foreach($map as $key => $val){
				if($flag && $str == $val){
					return $key;
				}
				if(!$flag && $str == $key){
					return $val;
				}
			}


			$str = trim($str);
			if(!$str){
				return '';
			}

			if($flag){
				if(substr($str,-2) == 'id'){
					if($str == 'uuid' || $str == 'Uuid' || $str == 'UuID'){
						$str = 'Uuid';
					}else{
						$str = substr($str,0,strlen($str)-2).'ID';
					}
				}
				if(substr($str,-2) == 'ip'){
					$str = substr($str,0,strlen($str)-2).'IP';
				}
				if(strpos($str,'_') === false){
					return ucfirst($str);
				}
				$temp = '';
				$arr = explode('_',$str);
				foreach($arr as $val){
					$temp .= ucfirst($val);
				}
				return $temp;
			}

			$regx = '_';
			return strtolower(preg_replace('/((?<=[a-z])(?=[A-Z]))/', $regx, $str));
		}

		// 将数组或对象键名转小写
		protected function getConvertData(mixed $payload): mixed
		{
			if(is_array($payload)){
				$data = [];
				foreach($payload as $key => $val){
					$data[$this->convert($key,false)] = $val;
				}

				return $data;
			}
			if(is_object($payload)){
				foreach($payload as $key => $val){
					$k = $this->convert($key,false);
					if($k != $key){
						$payload->$k = $val;
						unset($payload->$key);
					}
				}
				return $payload;
			}

			return $payload;
		}

    	/**
    	 * 获取编辑器组件options
    	 * @param  string $editor [description]
    	 * @return [type]         [description]
    	 */
    	protected function getEditorOptions($editor = ''){
    		$editor = $editor ? $editor : $this->editor;
    		// var_dump('editor: '.$editor);
			$maxFileSize = 200 * 1024 * 1024;
    		switch($editor){
    			case 'wang':
    				return ['type'=>'wang','mode'=>'default','prefix'=>$this->prefix,'image'=>['server'=>$this->host['api'].'upload/wang'],'video'=>['server'=>$this->host['api'].'upload/wang?path=video','maxFileSize'=> 100 * 1024 * 1024,'chunkSize'=>10 * 1024 * 1024]];
    				// return ['type'=>'wang','prefix'=>$this->prefix,'image'=>['server'=>$this->host['api'].'upload/'.$editor],'video'=>['server'=>$this->host['api'].'upload/chunk','maxFileSize'=>100*1024*1024,'chunkSize'=>3*1024*1024]];
    			case 'umo':
    				return ['type'=>'umo','is_ai'=>$this->is_ai,'action'=>$this->host['api'].'upload/chunk','prefix'=>$this->prefix,'maxFileSize'=>100*1024*1024,'chunkSize'=>3*1024*1024,'video'=>['maxFileSize'=>$maxFileSize]];
    			default:
    				return ['type'=>'wang','prefix'=>$this->prefix,'image'=>['server'=>$this->host['api'].'upload/'.$editor],'video'=>['server'=>$this->host['api'].'upload/chunk','maxFileSize'=>100*1024*1024,'chunkSize'=>3*1024*1024]];
    		}
    	}

    	/**
    	 * 获取上传组件option
    	 * @param  string  $type  [description]
    	 * @param  integer $limit [description]
    	 * @return [type]         [description]
    	 */
    	protected function getUploadOptions($type = 'img',$limit = 1,$size = 'default',$desc = '',$upload = ''){
    		
    		switch($type){
    			case 'img':
    			case 'pic':
    				return ['type'=>'img','limit'=>$limit,'size'=>$size,'prefix'=>$this->prefix,'action'=>$this->host['api'].'upload','desc'=>$desc];
    			case 'card':
    				return ['type'=>'card','limit'=>$limit,'size'=>$size,'prefix'=>$this->prefix,'action'=>$this->host['api'].'upload','desc'=>$desc];
    			case 'file':
    				return ['type'=>'file','limit'=>$limit,'size'=>$size,'prefix'=>$this->prefix,'action'=>$this->host['api'].'upload','desc'=>$desc];
    			case 'cert':
    				return ['type'=>'cert','limit'=>$limit,'size'=>$size,'prefix'=>$this->prefix,'action'=>$this->host['api'].'upload/cert','desc'=>$desc];
    			default: 
    				return ['type'=>'img','limit'=>$limit,'size'=>$size,'prefix'=>$this->prefix,'action'=>$this->host['api'].'upload','desc'=>$desc];
    		}
    	}

    	// 检查用户是否管理员
    	protected function checkIsAdmin(Request $request,$id = 0,$flag = false){
    		if(!$id){
    			$id = $this->getTokenData($request,'rid');
    		}
    		
    		if(!is_numeric($id) || !$id || $id < 0){
    			return false;
    		}

    		$field = $flag ? 'IsSuper as is_admin' : 'IsAdmin as is_admin';
    		if(strlen($id) <= 4){ 			// 角色父ID
    			$object = Db::table('role')->select($field)->where('ID',$id)->first();
    		}else{					// 会员ID
    			$where = [
    				[$this->prefix.'user.IsDel','=',0],
    				[$this->prefix.'user.Status','=',1],
    				['AccountID','=',$id],
    				[$this->prefix.'role.IsDel','=',0]
    			];
    			$object = Db::table('user')
    						->join('role','RoleID','=','role.ID')
    						->select($field)
    						->where($where)
    						->first();
    		}

    		return $object&&$object->is_admin ? true : false;
    	}

    	// protected function toArray($object){
    	// 	return $object;
    	// }

    	// 检查敏感字符
    	protected function checkSensitive($str){
    		$sensitive = [" ","\\","*","\/","\'","?","#","<",">","=","&"];
    		$flag = false;
    		foreach($sensitive as $sen){
    			if(strpos($str,$sen) !== false){
    				$flag = true;
    				break;
    			}
    		}

    		return $flag;
    	}

    	// 数据隐藏
    	protected function getEncodeStr($str,$type = 'mobile'){
    		if(!$str){
    			return $str;
    		}

    		switch($type){
    			case 'mobile':
    				$str = substr($str,0,3) . '****' . substr($str,strlen($str)-4);
    				break;
    			case 'idcard':
    				$str = substr($str,0,4) . '****' . substr($str,strlen($str)-4);
    				break;
    			case 'email':
    				$temp = explode('@',$str);
    				$str = substr($temp[0],0,2) . '***@' . $temp[1]; 
    				break;
    			default:
    		}

    		return $str;
    	}

    	// 获取两日期之间的日期列表
    	protected function getDateInterval($start_date,$end_date,$country = 'CN', $language = 'zh'){
    		$start = \DateTime::createFromFormat('Y-m-d', $start_date);
		    $end = \DateTime::createFromFormat('Y-m-d', $end_date);
		    
		    if (!$start || !$end) {
		        return []; // 日期格式无效返回空数组
		    }
		    
		    if($start > $end){
		        // 如果开始日期大于结束日期，交换两者
		        $temp = $start;
		        $start = $end;
		        $end = $temp;
		    }

		    // 2. 初始化节假日查询器
	        try{
	        	if(class_exists('\Suzunone\DateHolidays\Holidays')){
		            // 设置时区为中国标准时间
		            $holidays = new \Suzunone\DateHolidays\Holidays($country, null, null, [
		                'languages' => $language,
		                'timezone' => 'Asia/Shanghai'
		            ]);
		        }else{
		        	$holidays = null;
		        }
	        }catch(\Exception $e){
	            // 如果不支持该国节假日，继续执行但节假日判断始终为false
	            $holidays = null;
	        }

	        // 3. 遍历日期范围并收集信息
            $result = [];
            $current = clone $start;
            $weekdayMap = ['周日', '周一', '周二', '周三', '周四', '周五', '周六'];

            while ($current <= $end) {
                $dateStr = $current->format('Y-m-d');
                
                // 获取星期几信息（使用getdate获取本地化星期名称）
                $dateInfo = getdate($current->getTimestamp());
                
                // 判断是否为节假日
                $isHoliday = false;
                $holidayName = null;
                
                if ($holidays !== null) {
                    $holidayResult = $holidays->isHoliday($dateStr);
                    if ($holidayResult !== false) {
                        $isHoliday = true;
                        $holidayName = $holidayResult[0]['name'] ?? '节假日';
                    }
                }
                
                // 组装每天的数据
                $result[] = [
                    'date' => $dateStr,
                    'week' => $dateInfo['weekday'],   // 英文星期，如"Monday"
                    'weekname' => $weekdayMap[$dateInfo['wday']], // 中文星期
                    'weekid' => $dateInfo['wday'], // 0(周日)~6(周六)
                    'is_holiday' => $isHoliday ? 1 : 0,
                    'holiday_name' => !is_null($holidayName) ? $holidayName : '',
                ];
                
                $current->modify('+1 day');
            }
            
            return $result;
    	}

        protected function getBirth($idcard){
        	return substr($idcard,6,4).'-'.substr($idcard,10,2).'-'.substr($idcard,12,2);
        }

        protected function getGender($idcard){
        	$num = substr($idcard,16,1);
            return $num % 2 === 0 ? 2 : 1;
        }

        /**
         * [getAge description]
         * @param  [type] $idcard [description]
         * @return [type]         [description]
         */
        protected function getAge($birth){
            // $birth = $this->getBirth($idcard);
            // var_dump($birth);
            if(trim($birth) == '' || !$birth){
                return 0;
            }
            list($birthYear, $birthMonth, $birthDay) = explode('-', $birth);
            list($currentYear, $currentMonth, $currentDay) = explode('-', date('Y-m-d'));
            $age = $currentYear - $birthYear - 1;
            
            if($currentMonth > $birthMonth || $currentMonth == $birthMonth && $currentDay >= $birthDay)

            $age++;

            return $age;
        }

        /**
         * 推算生肖
         * @param  [type] $idcard [description]
         * @return [type]         [description]
         */
        protected function getZodiac($idcard){
        	// 提取出生年份
		    if (strlen($idcard) == 18) {
		        $year = intval(substr($idcard, 6, 4));  // YYYY
		    } elseif (strlen($idcard) == 15) {
		        $year = intval(substr($idcard, 6, 2));  // YY
		        // 15位身份证年份补全（假设1900-1999年出生）
		        $year = ($year < 30 ? 2000 + $year : 1900 + $year);
		    } else {
		        return '';
		    }
		    
		    // 生肖对应地支（以农历新年为界，此处按公历年份简化处理，大多数情况适用）
		    $zodiacs = [
		        '鼠', '牛', '虎', '兔', '龙', '蛇',
		        '马', '羊', '猴', '鸡', '狗', '猪'
		    ];

		    // 以1900年为基准（1900年是鼠年）
	        $index = ($year - 1900) % 12;
	        // 处理负数情况（虽然年份不会小于1900，但做安全处理）
	        if ($index < 0) $index += 12;
	        
	        return $zodiacs[$index];
        }

        /**
         * 推算星座
         * @param  [type] $idcard [description]
         * @return [type]         [description]
         */
        protected function getConstellation($idcard){
        	if (strlen($idcard) == 18) {
		        $birth = substr($idcard, 6, 8);  // YYYYMMDD
		        $month = intval(substr($birth, 4, 2));
		        $day = intval(substr($birth, 6, 2));
		    } elseif (strlen($idcard) == 15) {
		        $birth = substr($idcard, 6, 6);  // YYMMDD
		        $month = intval(substr($birth, 2, 2));
		        $day = intval(substr($birth, 4, 2));
		    } else {
		        return '';
		    }

		    // 星座对应日期
	        $constellations = [
	            ['name' => '摩羯座', 'start' => [1, 1], 'end' => [1, 19]],
	            ['name' => '水瓶座', 'start' => [1, 20], 'end' => [2, 18]],
	            ['name' => '双鱼座', 'start' => [2, 19], 'end' => [3, 20]],
	            ['name' => '白羊座', 'start' => [3, 21], 'end' => [4, 19]],
	            ['name' => '金牛座', 'start' => [4, 20], 'end' => [5, 20]],
	            ['name' => '双子座', 'start' => [5, 21], 'end' => [6, 20]],
	            ['name' => '巨蟹座', 'start' => [6, 21], 'end' => [7, 22]],
	            ['name' => '狮子座', 'start' => [7, 23], 'end' => [8, 22]],
	            ['name' => '处女座', 'start' => [8, 23], 'end' => [9, 22]],
	            ['name' => '天秤座', 'start' => [9, 23], 'end' => [10, 22]],
	            ['name' => '天蝎座', 'start' => [10, 23], 'end' => [11, 21]],
	            ['name' => '射手座', 'start' => [11, 22], 'end' => [12, 21]],
	            ['name' => '摩羯座', 'start' => [12, 22], 'end' => [12, 31]],
	        ];

	        foreach ($constellations as $const) {
		        $startMonth = $const['start'][0];
		        $startDay = $const['start'][1];
		        $endMonth = $const['end'][0];
		        $endDay = $const['end'][1];

		        if (($month == $startMonth && $day >= $startDay) || ($month == $endMonth && $day <= $endDay)) {
		            return $const['name'];
		        }
		    }

		    return '';
        }

        protected function isBase64Image($string) {
		    if (empty($string)) {
		        return false;
		    }
		    
		    // 检查是否为Data URI格式（如data:image/png;base64,...）
		    if (strpos($string, 'data:image') === 0 && strpos($string, 'base64,') !== false) {
		        return true;
		    }
		    
		    // 检查是否为纯Base64字符串（可选）
		    // Base64字符串通常只包含字母、数字、+、/和=
		    $string = str_replace(["\r", "\n", " "], "", $string);
		    if (strlen($string) % 4 !== 0) {
		        return false;
		    }
		    
		    if (!preg_match('/^[a-zA-Z0-9\/\+=]+$/', $string)) {
		        return false;
		    }
		    
		    // 尝试解码验证
		    $decoded = base64_decode($string, true);
		    if ($decoded === false) {
		        return false;
		    }
		    
		    // 检查解码后的数据是否为图片
		    $finfo = finfo_open(FILEINFO_MIME_TYPE);
		    $mime = finfo_buffer($finfo, $decoded);
		    finfo_close($finfo);
		    
		    return strpos($mime, 'image/') === 0;
		}

    	/**
    	 * 格式化图片
    	 * @param  [type] $img  [description]
    	 * @param  string $path [description]
    	 * @return [type]       [description]
    	 */
        protected function getImage($img,$path = ''){
        	if(empty($img) || !$img){
        		return $img;
        	}
            if(strtolower(substr($img,0,4)) != 'http'){
            	$url = isset($this->host['local']) ? $this->host['local'] : \str_replace('api','admin',$this->host['api']);
                $img = $url . !empty($path) ? $path . '/' . $img : $img;
            }
            
            return $img;
        }

        /**
         * 格式化价格
         * @param  [type]  $price   [description]
         * @param  integer $decimal [description]
         * @param  integer $divisor [description]
         * @return [type]           [description]
         */
        protected function formatPrice($price,$decimal = 2,$divisor = 100){
        	return $this->formatAmount($price,$decimal,$divisor);
        }

        /**
         * 格式化数字
         * @param  [type]  $amount  [description]
         * @param  integer $decimal [description]
         * @param  integer $divisor [description]
         * @return [type]           [description]
         */
        protected function formatAmount($amount,$decimal = 2,$divisor = 100){
        	if(!is_numeric($amount) || !$amount){
        		return $amount;
        	}
        	return \round(($amount / $divisor),$decimal);
        }

        /**
         * 格式化日期
         * @param  string $time [description]
         * @return [type]       [description]
         */
        protected function getDateTime($time = '',$format = 'Y-m-d H:i:s'){
        	if(empty($time) || !is_numeric($time) || !$time){
        		return $time;
        	}
        	return \date($format,$time);
        }

    	protected function getWeek($time,$type = 'w'){
    		if(empty($time) || !is_numeric($time) || !$time){
        		return $time;
        	}
    		return \date($type,$time);
    	}

        /**
         * 格式化数组json
         * @param  mixed|string $arr [description]
         * @return [type]            [description]
         */
		protected function getJsonData(mixed $arr = ''): string
		{
			if(is_array($arr)){
				return \json_encode($arr);
			}
			return $arr;
		}

		/**
		 * 格式化json字符串
		 * @param  mixed|string $str  [description]
		 * @param  boolean      $flag [description]
		 * @return [type]             [description]
		 */
		protected function getDecodeData(mixed $str = '',$flag = true): mixed
		{
			if(empty($str) || !$str){
				return $str;
			}
			if(\is_array($str) || !is_string($str)){
				return $str;
			}
			if(\is_array($flag)){
				return $flag;
			}
			try{
				$result = \json_decode($str,$flag);
				if(\json_last_error() === JSON_ERROR_NONE){
					return $result;
				}

				return [$str];
			}catch(\Exception $e){
				// var_dump($e->getMessage());
				return [];
			}
		}

		protected function getUploadData(mixed $arr = [],$flag = true): array
		{
			if(!$arr){
				return [];
			}

			if(is_string($arr)){
				$arr = explode(',',$arr);
			}

			if($arr){
				$data = [];
				foreach($arr as $val){
					if($val){
						$temp = explode('/',$val);
						$name = end($temp);
						array_push($data,['name'=>$name,'url'=>$val]);
					}
				}
				return $data;
			}

			return [];
		}

        /**
         * 设置session
         * @param Request $request [description]
         * @param [type]  $data    [description]
         * @param [type]  $value   [description]
         */
        protected function setSession(Request $request,$data,$value = null){
        	if(!is_null($value) && is_string($data) && $data){
        		$request->session()->set($this->prefix.$data,$value);
        	}
        	if((is_array($data) || is_object($data)) && $data){
        		foreach($data as $key => $val){
        			$request->session()->set($this->prefix.$key,$val);
        		}
        	}
        }

        /**
         * 获取session
         * @param  Request $request [description]
         * @param  string  $key     [description]
         * @return [type]           [description]
         */
        protected function getSession(Request $request,$key = ''){
        	if($key){
        		$key = $this->prefix.$key;
        		$session = $request->session();
        		if($session->exists($key)){
        			return $session->get($key);
        		}
        		return '';
        	}
        	return $request->session();
        }

        /**
         * 删除session
         * @param  Request $request [description]
         * @param  string  $key     [description]
         * @return [type]           [description]
         */
        protected function removeSession(Request $request,$key = ''){
        	if($key){
        		$key = $this->prefix.$key;
        		$request->session()->forget($key);
        	}else{
        		$request->session()->flush();
        	}
        }

        /**
         * 获取类名
         * @param  [type] $class [description]
         * @return [type]        [description]
         */
        protected function getClassName($class){
        	if(\preg_match('/[A-Z]/',\lcfirst($class))){
				$temp = \lcfirst($class);
				$temp = \preg_split('/(?=[A-Z])/', $temp);
				$class = \ucfirst($class);
				$parent = \ucfirst($temp[0]);
				$_class_name = "\\app\\model\\{$parent}\\{$class}Model";
				if(\class_exists($_class_name)){
					return $_class_name;
				}
				$_class_name = "\\dackou\\model\\{$parent}\\{$class}Model";
				if(\class_exists($_class_name)){
					return $_class_name;
				}
				return false;
			}else{
				$class = \ucfirst($class);
				$_class_name = "\\app\\model\\{$class}\\{$class}Model";
				if(\class_exists($_class_name)){
					return $_class_name;
				}
				$_class_name = "\\dackou\\model\\{$class}\\{$class}Model";
				if(\class_exists($_class_name)){
					return $_class_name;
				}
				return false;
			}
        }

        /**
         * 返回异常结果数据
         * @param  [type] $e [description]
         * @return [type]    [description]
         */
        protected function getExceptionError(\Exception $e,$dump = false): array
        {
            $data = [
                'code'  => $e->getCode() ? $e->getCode() : 1,
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
                'method'=> \request()->action,
                'msg'   => $e->getMessage()
            ];
            if($dump){
            	\var_dump('exception error:',$data);
            }
            return $data;
        }
	}
?>