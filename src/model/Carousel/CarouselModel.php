<?php
	namespace dackou\model\Carousel;

	use support\Request;
	use support\Db;
	use dackou\model\Page\PageModel;

	class CarouselModel extends \dackou\Model{
		protected $table = 'Carousel';
		protected $title = '轮播图';
		public $layer = 2;
		protected $cate_value = [0=>'其它',1=>'mini',2=>'app',3=>'web'];
        protected $map_exclude = ['CreateTime','LinkUrl','CateID'];
        protected $key_value = [
        	'mini' => [
        		['label'=>'首页(home)','value'=>'home'],
        		['label'=>'新闻页(news)','value'=>'news'],
        		['label'=>'文章页(article)','value'=>'article'],
        		['label'=>'活动页(activity)','value'=>'activity'],
        		['label'=>'商品页(goods)','value'=>'goods'],
        	],
        	'app' => [
        		['label'=>'首页(home)','value'=>'home'],
        		['label'=>'新闻页(news)','value'=>'news'],
        		['label'=>'文章页(article)','value'=>'article'],
        		['label'=>'活动页(activity)','value'=>'activity'],
        		['label'=>'商品页(goods)','value'=>'goods'],
        	],
        	'web' => [
        		['label'=>'首页(home)','value'=>'home'],
        		['label'=>'新闻页(news)','value'=>'news'],
        		['label'=>'文章页(article)','value'=>'article'],
        		['label'=>'活动页(activity)','value'=>'activity'],
        		['label'=>'商品页(goods)','value'=>'goods'],
        	]
        ];

		protected function getAllList(Request $request){
			try{
				$field = $this->getList($request,'field');
				$where = $this->getWhere($request);

				$args = \func_get_args();
				\array_shift($args);
				if($args && isset($args[0]) && is_array($args[0])){
					foreach($args[0] as $k => $v){
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
							->orderBy($this->table.'.Level','asc')
							->orderBy($this->table.'.Sort','asc')
							->get();
				if($object){
					$object = $this->child($object);
				}
				return $object;
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		protected function getPageList(Request $request){
			try{
				$field = $this->getList($request,'field',true);
				array_push($field,'page.Title as page_title');
				array_push($field,'page.Url as page_url');
				$where = $this->getWhere($request);

				$cate_id = $request->input('cate_id',1);
				if(in_array($cate_id,[1,2,3])){
					array_push($where,[$this->table.'.CateID','=',$cate_id]);
				}

				$rows = Db::table($this->table)
							->join('page','PageID','=','page.ID')
							->where($where)
							->count();
				[$offset,$limit] = $this->getLimit($request);
				$object = Db::table($this->table)
							->join('page','PageID','=','page.ID')
							->select(...$field)
							->where($where)
							->orderBy($this->table.'.Level','asc')
							->orderBy($this->table.'.Sort','asc')
							->offset($offset)
							->limit($limit)
							->get();
				if($object){
					$object = $this->tree($object);
				}

				return ['rows' => $rows,'data' => $object];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		protected function getKeyList(Request $request,$key = ''){
			if(!$key || empty($key)){
				return [];
			}

			$field = $this->getList($request,'field',true);
			array_push($field,'page.Url as page_url');
			$where = $this->getWhere($request);
			array_push($where,[$this->table.'.IsValid','=',1]);
			array_push($where,[$this->table.'.Key','=',$key]);
			$cate_id = $request->input('cate_id',1);
			if(in_array($cate_id,[1,2,3])){
				array_push($where,[$this->table.'.CateID','=',$cate_id]);
			}

			$object = Db::table($this->table)
						->join('page','PageID','=','page.ID')
						->select(...$field)
						->where($where)
						->orderBy($this->table.'.Sort','asc')
						->get();
			if($object){
				$data = [];
				$child = [];
				foreach($object as $k => $v){
					if(!$v->pid){
						$data = [
							'id'		=>$v->id,
							'title'		=>$v->title,
							'pic'		=>$v->pic,
							'width'		=>$v->width,
							'height'	=>$v->height,
							'radius'	=>$v->radius,
							'spacing'	=>$v->spacing,
							'padding'	=>$v->padding,
							'url'		=>$v->page_url,
						];
					}else{
						array_push($child,[
							'id'		=>$v->id,
							'title'		=>$v->title,
							'pic'		=>$v->pic,
							'width'		=>$v->width,
							'height'	=>$v->height,
							'radius'	=>$v->radius,
							'spacing'	=>$v->spacing,
							'padding'	=>$v->padding,
							'url'		=>$v->page_url,
						]);
					}
				}
				$data['children'] = $child;

				return $data;
			}

			return [];


			
			// try{
			// 	if(is_string($key) && \strpos($key, ',') === false){
			// 		$field = $this->getList($request,'field');
			// 		$where = $this->getWhere($request);
			// 		array_push($where,[$this->table.'.IsValid','=',1]);
			// 		array_push($where,[$this->table.'.IsDel','=',0]);
			// 		array_push($where,[$this->table.'.Key','=',$key]);
			// 		$object = Db::table($this->table)
			// 					->select(...$field)
			// 					->where($where)
			// 					->orderBy('Sort','asc')
			// 					->get();
			// 		if($object){
			// 			foreach($object as $k => $v){
			// 				unset($object[$k]->create_time);
			// 				unset($object[$k]->create_ip);
			// 				unset($object[$k]->update_time);
			// 				unset($object[$k]->update_ip);
			// 				unset($object[$k]->delete_time);
			// 				unset($object[$k]->delete_ip);
			// 				unset($object[$k]->cate_id);
			// 				unset($object[$k]->is_del);
			// 				unset($object[$k]->key);
			// 				unset($object[$k]->is_valid);
			// 				unset($object[$k]->sort);
			// 			}
			// 			$object = $this->child($object);
			// 			return $object[0];
			// 		}
			// 		return [];
			// 	}else{

			// 	}
			// }catch(\Exception $e){
			// 	return $this->getExceptionError($e);
			// }
		}

		protected function validate(Request $request,$id = 0,$obj = null){
			$cate_id = $request->post('cate_id',1);
			if(!in_array($cate_id,[1,2,3])){
				return '无效的类别';
			}
			$level = $request->post('level',1);
			$pid = $request->post('pid',0);
			if($level <= 1){
				$pid = 0;
			}else{
				if(!$pid){
					return '请选择父级';
				}
			}

			if($pid){
				$parent = $this->getList($request,$pid);
				if(!$parent || !is_object($parent)){
					return '无效的父级';
				}

				$data['key'] = $parent->key;
			}

			$title = trim($request->post('title',''));

			if(!$title){
				return '标题名称不能为空';
			}
			if($this->checkExists(['PID'=>$pid,'Title'=>$title],$id)){
				return '同级标题名称已存在,请重新输入';
			}

			if((!$id && !$pid) || ($id && !$obj->pid)){
				$key = trim($request->post('key',''));
				$width = $request->post('width',0);
				$height = $request->post('height',0);
				$spacing = $request->post('spacing',0);
				$padding = $request->post('padding',0);
				$radius = $request->post('radius',0);

				if(!$key){
					return '标识key不能为空';
				}
				if($this->checkExists(['PID'=>$pid,'Key'=>$key],$id)){
					return '标识key已存在,请重新输入';
				}

				if(is_null($width) || empty($width)){
					$width = 0;
				}
				if(!is_numeric($width)){
					return '宽度只能填数字';
				}

				if(is_null($height) || empty($height)){
					$height = 0;
				}
				if(!is_numeric($height)){
					return '高度只能填数字';
				}

				if(is_null($spacing) || empty($spacing)){
					$spacing = 0;
				}
				if(!is_numeric($spacing)){
					return '外间距只能填数字';
				}

				if(is_null($padding) || empty($padding)){
					$padding = 0;
				}
				if(!is_numeric($padding)){
					return '内间距只能填数字';
				}

				if(is_null($radius) || empty($radius)){
					$radius = 0;
				}
				if(!is_numeric($radius)){
					return '圆角只能填数字';
				}

				$data['key'] = $key;
				$data['width'] = $width;
				$data['height'] = $height;
				$data['spacing'] = $spacing;
				$data['padding'] = $padding;
				$data['radius'] = $radius;
				$data['page_id'] = 1;
			}else{
				$pic = $request->post('pic');
				if(is_array($pic)){
					$pic = end($pic);
				}
				$page_id = $request->post('page_id');
				if(is_array($page_id)){
					$page_id = end($page_id);
				}

				if(!$page_id){
					return '请选择链接';
				}

				if(!$pic){
					// return '请上传图片';
				}

				$data['pic'] = $pic;
				$data['page_id'] = $page_id;
				if(!$id && $pid){
					$parent = $this->getList($request,$pid);
					$data['key'] = $parent->key;
				}
			}

			$is_valid = $request->post('is_valid',1);
			$sort = $request->post('sort',1);
			if(!in_array($is_valid,[0,1])){
				return '无效的有效值';
			}
			if(!$sort){
				return '排序不能为空';
			}
			if(!is_numeric($sort)){
				return '排序只能填数字';
			}

			$data['title'] = $title;
			$data['cate_id'] = $cate_id;
			$data['level'] = $level;
			$data['pid'] = $pid;
			$data['is_valid'] = $is_valid;
			$data['sort'] = $sort;

			return $data;
		}

		protected function getActionList(Request $request,$id = 0): array
		{
			if($id){
				$data = $this->getList($request,$id);
			}

			$cate_id = $request->input('cate_id',1);
			$cate = [];
			foreach($this->cate_value as $k => $v){
				if($k){
					array_push($cate,['type'=>'option','label'=>$v,'value'=>(int)$k]);
				}
			}
			$level = $request->input('level',1);
			$levelData = [];
			for($i=1;$i<=$this->layer;$i++){
				array_push($levelData,['type'=>'option','label'=>$i.'级','value'=>$i]);
			}
			$pid = $request->input('pid',0);
			$pidData = [];
			$result = $this->getList($request,'parent');
			if($result){
				foreach($result as $item){
					array_push($pidData,['type'=>'option','label'=>$item->title,'value'=>(int)$item->id]);
				}
			}

			$service = new PageModel();
			$page = $service->getList($request,'option');

			$sort = $this->getMaxSort($pid);

			$action = [
				['type'=>'radio-group','label'=>'类别','prop'=>'cate_id','value'=>$id ? $data->cate_id : $cate_id,'hidden'=>true,'children'=>$cate],
				['type'=>'input','label'=>'标题名称','prop'=>'title','value'=>$id ? $data->title : '','rules'=>['required'=>true,'message'=>'标题名称不能为空']],
				['type'=>'select','label'=>'层级','prop'=>'level','value'=>$id ? $data->level : $level,'hidden'=>true,'children'=>$levelData],
				['type'=>'select','label'=>'父级','prop'=>'pid','value'=>$id ? $data->pid : $pid,'hidden'=>true,'children'=>$pidData],
			];
			if((!$id && !$pid) || ($id && !$data->pid)){
				array_push($action,['type'=>'select','label'=>'标识key','prop'=>'key','value'=>$id ? $data->key : '','children'=>$this->key_value[$this->cate_value[$cate_id]],'rules'=>['required'=>true,'message'=>'标识key不能为空']]);
				array_push($action,['type'=>'group','label'=>'宽高','prop'=>'fgroup','delimiter'=>'-','children'=>[
					['type'=>'input','label'=>'宽度','prop'=>'width','value'=>$id ? $data->width : '','placeholder'=>'0','attrs'=>['style'=>['width'=>'180px']],'slot'=>['prefix'=>['value'=>'宽度'],'suffix'=>['value'=>'px']]],
					['type'=>'input','label'=>'高度','prop'=>'height','value'=>$id ? $data->height : '','placeholder'=>'0','attrs'=>['style'=>['width'=>'180px']],'slot'=>['prefix'=>['value'=>'高度'],'suffix'=>['value'=>'px']]],
				]]);
				array_push($action,['type'=>'group','label'=>'内外间距','prop'=>'fspacing','delimiter'=>'-','children'=>[
					['type'=>'input','label'=>'外间距','prop'=>'spacing','value'=>$id ? $data->spacing : '','placeholder'=>'0','attrs'=>['style'=>['width'=>'180px']],'slot'=>['prefix'=>['value'=>'外距'],'suffix'=>['value'=>'px']]],
					['type'=>'input','label'=>'内间距','prop'=>'padding','value'=>$id ? $data->padding : '','placeholder'=>'0','attrs'=>['style'=>['width'=>'180px']],'slot'=>['prefix'=>['value'=>'内距'],'suffix'=>['value'=>'px']]],
				]]);
				array_push($action,['type'=>'input','label'=>'圆角','prop'=>'radius','value'=>$id ? $data->radius : '','placeholder'=>'0','slot'=>['suffix'=>['value'=>'px']]]);
			}else{
				array_push($action,['type'=>'upload','label'=>'图片','prop'=>'pic','value'=>$id ? $data->pic : '','attrs'=>$this->getUploadOptions('img'),'required'=>true]);
				array_push($action,['type'=>'cascader','label'=>'链接','prop'=>'page_id','value'=>$id?$data->page_id:[],'children'=>$page,'required'=>true]);
			}

			array_push($action,['type'=>'switch','label'=>'有效','prop'=>'is_valid','value'=>$id ? $data->is_valid : 1],
				['type'=>'input','label'=>'排序','prop'=>'sort','value'=>$id ? $data->sort : $sort]);

			return $action;
		}
	}
?>