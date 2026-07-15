<?php
	namespace dackou;

	use support\Request;
	use support\Response;
	use dackou\Json;

	class Controller{
		protected $table = '';
    	private $_class_name;
    	private $map = [];

    	// 构造函数
    	public function __construct(){
    		if($this->table){
    			if($this->map){
	    			foreach($this->map as $item){
	    				if(in_array($this->table,$item['controller'])){
	    					$this->_class_name = "\\app\\model\\{$item['model']}\\{$this->table}Model";
	    					break;
	    				}
	    			}
	    		}
    			if(!$this->_class_name){
		    		$table = preg_replace('/((?<=[a-z])(?=[A-Z]))/', '_', $this->table);
		    		if(strpos($table,'_')){
		    			$this->_class_name = '\\app\\model\\'.ucfirst(explode('_',$table)[0]).'\\'.ucfirst($this->table).'Model';
		    		}else{
		    			$this->_class_name = '\\app\\model\\'.ucfirst($this->table).'\\'.ucfirst($this->table).'Model';
		    		}

		    		if($this->_class_name && !class_exists($this->_class_name)){
		    			if(strpos($table,'_')){
		    				$this->_class_name = '\\dackou\\model\\'.ucfirst(explode('_',$table)[0]).'\\'.ucfirst($this->table).'Model';
		    			}else{
		    				$this->_class_name = '\\dackou\\model\\'.ucfirst($this->table).'\\'.ucfirst($this->table).'Model';
		    			}
		    		}
		    	}
	    	}

	    	// var_dump('_class_name: '.$this->_class_name);
    	}

    	/**
    	 * [index description]
    	 * @param  Request $request [description]
    	 * @param  integer $id      [description]
    	 * @return [type]           [description]
    	 */
		public function index(Request $request,$id = 0){
			if(class_exists($this->_class_name)){
    			$service = new $this->_class_name();
    			$result = $id ? $service->getList($request,$id) : $service->getList($request);

    			return Json::show($result);
    		}

			return Json::show(100000);
		}

		/**
		 * [show description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function show(Request $request){
			if(class_exists($this->_class_name)){
    			$service = new $this->_class_name();
    			$result = $service->getList($request,'show');

    			return Json::show($result);
    		}

			return Json::show(100000);
		}

		public function all(Request $request){
			if(class_exists($this->_class_name)){
    			$service = new $this->_class_name();
    			$result = $service->getList($request,'all');

    			return Json::show($result);
    		}

			return Json::show(100000);
		}

		public function page(Request $request){
			if(class_exists($this->_class_name)){
    			$service = new $this->_class_name();
    			$result = $service->getList($request,'page');

    			return Json::show($result);
    		}

			return Json::show(100000);
		}

		public function user(Request $request,$user_id = 0){
			if(class_exists($this->_class_name)){
    			$service = new $this->_class_name();
    			$result = $service->getList($request,'user',$user_id);

    			return Json::show($result);
    		}

			return Json::show(100000);
		}

		public function cate(Request $request,$cate_id = 0){
			if(class_exists($this->_class_name)){
    			$service = new $this->_class_name();
    			$result = $service->getList($request,'cate',$cate_id);

    			return Json::show($result);
    		}

			return Json::show(100000);
		}

		public function tree(Request $request){
			if(class_exists($this->_class_name)){
    			$service = new $this->_class_name();
    			$result = $service->getList($request,'tree');

    			return Json::show($result);
    		}

			return Json::show(100000);
		}

		public function child(Request $request){
			if(class_exists($this->_class_name)){
    			$service = new $this->_class_name();
    			$result = $service->getList($request,'child');

    			return Json::show($result);
    		}

			return Json::show(100000);
		}

		public function option(Request $request){
			if(class_exists($this->_class_name)){
    			$service = new $this->_class_name();
    			$result = $service->getList($request,'option');

    			return Json::show($result);
    		}

			return Json::show(100000);
		}

		public function options(Request $request){
			return $this->option($request);
		}

		public function top(Request $request,$num = 1){
			if(class_exists($this->_class_name)){
    			$service = new $this->_class_name();
    			$result = $service->getList($request,'top',$num);

    			return Json::show($result);
    		}

			return Json::show(100000);
		}

		public function level(Request $request,$level = 1){
			if(class_exists($this->_class_name)){
    			$service = new $this->_class_name();
    			$result = $service->getList($request,'level',$level);

    			return Json::show($result);
    		}

			return Json::show(100000);
		}

		public function parent(Request $request,$pid = 0){
			if(class_exists($this->_class_name)){
    			$service = new $this->_class_name();
    			$result = $service->getList($request,'parent',$pid);

    			return Json::show($result);
    		}

			return Json::show(100000);
		}

		public function action(Request $request,$id = 0){
			if(class_exists($this->_class_name)){
    			$service = new $this->_class_name();
    			$result = $service->getList($request,'action',$id);

    			return Json::show($result);
    		}

			return Json::show(100000);
		}

		public function key(Request $request,$key = ''){
			if(class_exists($this->_class_name)){
    			$service = new $this->_class_name();
    			$result = $service->getList($request,'key',$key);

    			return Json::show($result);
    		}

			return Json::show(100000);
		}

		public function code(Request $request){
			if(class_exists($this->_class_name)){
    			$service = new $this->_class_name();
    			$result = $service->getCodeData($request);
    			if(is_string($result)){
    				$result = ['code'=>0,'data'=>$result];
    			}
    			return Json::show($result);
    		}

			return Json::show(100000);
		}

		public function create(Request $request){
			if(class_exists($this->_class_name)){
    			$service = new $this->_class_name();
    			$result = $service->create($request);

    			return Json::show($result);
    		}

			return Json::show(100000);
		}

		public function add(Request $request){
			if(class_exists($this->_class_name)){
    			$service = new $this->_class_name();
    			$result = $service->add($request);
    			
    			if(is_array($result) && !isset($result['msg'])){
    				$result = ['code'=>0,'msg'=>'success','data'=>$result];
    			}

    			return Json::show($result);
    		}

			return Json::show(100000);
		}

		public function mod(Request $request,$id = 0){
			if(class_exists($this->_class_name)){
    			$service = new $this->_class_name();
    			$result = $service->mod($request,$id);
    			
    			if(is_array($result) && !isset($result['msg'])){
    				$result = ['code'=>0,'msg'=>'success','data'=>$result];
    			}
    			return Json::show($result);
    		}

			return Json::show(100000);
		}

		public function save(Request $request,$id = 0){
			if(class_exists($this->_class_name)){
    			$service = new $this->_class_name();
    			$result = $id ? $service->mod($request,$id) : $service->add($request);

    			return Json::show($result);
    		}

			return Json::show(100000);
		}

		public function rating(Request $request,$id = 0){
			if(class_exists($this->_class_name)){
    			$service = new $this->_class_name();
    			$result = $service->setRating($request,$id);

    			return Json::show($result);
    		}

			return Json::show(100000);
		}

		public function like(Request $request,$id = 0){
			if(class_exists($this->_class_name)){
    			$service = new $this->_class_name();
    			$result = $service->setLikes($request,$id);

    			return Json::show($result);
    		}

			return Json::show(100000);
		}

		/**
		 * 下载导入模板文件
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function fimport(Request $request){
			if(class_exists($this->_class_name)){
    			$service = new $this->_class_name();
    			$result = $service->downImportFile($request);
    			// var_dump($result);
    			if(is_array($result)){
    				return Json::show($result);

    			}

    			return $result;
    		}

			return Json::show(100000);
		}

		/**
		 * 导入模板数据
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function import(Request $request){
			if(class_exists($this->_class_name)){
    			$service = new $this->_class_name();
    			$result = $service->setImport($request);

    			return Json::show($result);
    		}

			return Json::show(100000);
		}

		public function export(Request $request){
			if(class_exists($this->_class_name)){
    			$service = new $this->_class_name();
    			$result = $service->setExport($request);

    			return Json::show($result);
    		}

			return Json::show(100000);
		}

		public function switch(Request $request,$id = 0){
			if(class_exists($this->_class_name)){
				$id = $id ?: $request->input('id',0);
    			$service = new $this->_class_name();
    			$result = $service->setSwitch($request,$id);

    			return Json::show($result);
    		}

			return Json::show(100000);
		}

		public function editor(Request $request,$id = 0){
			if(class_exists($this->_class_name)){
				$id = $id ?: $request->input('id',0);
    			$service = new $this->_class_name();
    			$result = $service->setEditor($request,$id);

    			return Json::show($result);
    		}

			return Json::show(100000);
		}

		public function qrcode(Request $request,$id = 0){
			if(class_exists($this->_class_name)){
				$id = $id ?: $request->input('id',0);
    			$service = new $this->_class_name();
    			$result = $service->getList($request,'qrcode',$id);

    			return Json::show($result);
    		}

			return Json::show(100000);
		}

		public function init(Request $request){
			return $this->initial($request);
		}

		public function initial(Request $request){
			if(class_exists($this->_class_name)){
    			$service = new $this->_class_name();
    			$result = $service->setInitialize($request);

    			if(is_array($result) && isset($result['code'])){
    				return Json::show($result);
    			}
    			return $result ? Json::show(['code'=>0,'msg'=>'success','data'=>$result]) : Json::show(['code'=>1,'msg'=>'初始化失败']);
    		}

			return Json::show(100000);
		}

		public function check(Request $request,$id = 0){
			if(class_exists($this->_class_name)){
    			$service = new $this->_class_name();
    			$result = $service->setChecked($request,$id);

    			return Json::show($result);
    		}

			return Json::show(100000);
		}

		public function del(Request $request,$id = 0){
			$id = $id ? $id : $request->post('id',0);
			// var_dump('id: ',$id);
			if(!$id){
				return Json::show(100007);
			}
			if(class_exists($this->_class_name)){
    			$service = new $this->_class_name();
    			$result = $service->del($request,$id);

    			return Json::show($result);
    		}

			return Json::show(100000);
		}

		// 清空数据
		public function clear(Request $request){
			if(class_exists($this->_class_name)){
    			$service = new $this->_class_name();
    			$result = $service->clear($request);
    			// var_dump('result: '.$result);
    			if(is_string($result)){
    				return Json::show(['code'=>1,'msg'=>$result]);
    			}elseif($result === true){
    				return Json::show(['code'=>0,'msg'=>'数据清空成功']);
    			}elseif($result === false){
    				return Json::show(['code'=>1,'msg'=>'数据清空失败']);
    			}else{
	    			return Json::show($result);
	    		}
    		}

			return Json::show(100000);
		}

        /**
         * 获取类名
         * @param  [type] $class [description]
         * @return [type]        [description]
         */
        protected function getClassName($class = ''){
        	if(!$class){
        		$class = $this->table;
        	}
        	if(!$class){
        		return false;
        	}

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

	}
?>