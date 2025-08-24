<?php
	namespace dackou\model\Tabulation;

	use support\Request;
	use support\Db;
	use dackou\model\Page\PageModel;

	class TabulationModel extends \dackou\Model{
		protected $table = 'Tabulation';
		protected $title = '列表组件';
		public $layer = 2;
		protected $cate_value = [0=>'其它',1=>'mini',2=>'app',3=>'web'];

		protected function getPageList(Request $request){
			try{
				$field = $this->getList($request,'field');
				$where = $this->getWhere($request);

				$cate_id = $request->input('cate_id',1);
				if(in_array($cate_id,[1,2,3])){
					array_push($where,[$this->table.'.CateID','=',$cate_id]);
				}

				$rows = Db::table($this->table)
							->where($where)
							->count();
				[$offset,$limit] = $this->getLimit($request);
				$object = Db::table($this->table)
							->select(...$field)
							->where($where)
							->orderBy($this->table.'.Level','asc')
							->orderBy($this->table.'.Sort','asc')
							->offset($offset)
							->limit($limit)
							->get();

				return ['rows' => $rows,'data' => $object];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		protected function validate(Request $request,$id = 0,$obj = null){
			$cate_id = $request->post('cate_id',1);
			if(!in_array($cate_id,[1,2,3])){
				return '无效的类别';
			}

			$level = $request->post('level',1);
			$pid = $request->post('pid',0);
			$title = trim($request->post('title',''));
			$desc = trim($request->post('desc',''));
			$page_id = $request->post('page_id');
			if(!$page_id){
				$page_id = 0;
			}
			if(is_array($page_id)){
				$page_id = end($page_id);
			}


			if((!$id && !$pid) || ($id && !$obj->pid)){
				$is_admin = $request->post('is_admin',0);
				$is_login = $request->post('is_login',0);
				$is_title = $request->post('is_title',0);
				$is_more = $request->post('is_more',0);
				$more_id = $request->post('more_id',0);
				if(!$more_id || empty($more_id)){
					$more_id = 0;
				}
				if(is_array($more_id)){
					$more_id = end($more_id);
				}
				$direction = $request->post('direction');
				$span = $request->post('span',0);
				$is_border = $request->post('is_border',0);
				$bg_color = $request->post('bg_color','');
				$border_radius = $request->post('border_radius',0);
				if(!$border_radius || empty($border_radius)){
					$border_radius = 0;
				}
				$padding_y = $request->post('padding_y',0);
				if(!$padding_y || empty($padding_y)){
					$padding_y = 0;
				}
				$padding_x = $request->post('padding_x',0);
				if(!$padding_x || empty($padding_x)){
					$padding_x = 0;
				}
				$width = $request->post('width',0);
				if(!$width || empty($width)){
					$width = 0;
				}
				$height = $request->post('height',0);
				if(!$height || empty($height)){
					$height = 0;
				}
				$radius = $request->post('radius',0);
				if(!$radius || empty($radius)){
					$radius = 0;
				}
				$text_size = $request->post('text_size',1);
				if(empty($text_size)){
					$text_size = 0;
				}
				$text_color = $request->post('text_color','');
				$desc_size = $request->post('desc_size');
				if(empty($desc_size)){
					$desc_size = 0;
				}
				$desc_color = $request->post('desc_color','');

				if(!$page_id){
					return '请选择关联页面';
				}
				$service = new PageModel();
				$page = $service->getList($request,$page_id);
				if(!$page || !is_object($page)){
					return '无效的关联页面';
				}

				if(!in_array($is_admin,[0,1])){
					return '无效的管理员权限选项';
				}

				if(!in_array($is_login,[0,1])){
					return '无效的登录权限选项';
				}

				if(!in_array($is_title,[0,1])){
					return '无效的显示标题选项';
				}

				if(!in_array($is_more,[0,1])){
					return '无效的显示更多选项';
				}

				if($is_more){
					if(!$more_id){
						return '请选择显示更多的跳转页面';
					}

					$service = new PageModel();
					$page = $service->getList($request,$more_id);
					if(!$page || !is_object($page)){
						return '无效的更多跳转页面';
					}
				}

				$data['page_id'] = $page_id;
				$data['is_admin'] = $is_admin;
				$data['is_login'] = $is_login;
				$data['is_title'] = $is_title;
				$data['is_more'] = $is_more;
				$data['more_id'] = $more_id;
				$data['direction'] = $direction;
				$data['span'] = $span;
				$data['is_border'] = $is_border;
				$data['bg_color'] = $bg_color;
				$data['border_radius'] = $border_radius;
				$data['padding_y'] = $padding_y;
				$data['padding_x'] = $padding_x;
				$data['width'] = $width;
				$data['height'] = $height;
				$data['radius'] = $radius;
				$data['text_size'] = $text_size;
				$data['text_color'] = $text_color;
				$data['desc_size'] = $desc_size;
				$data['desc_color'] = $desc_color;
			}else{
				$pic = $request->post('pic');
				if(is_array($pic)){
					$pic = end($pic);
				}
				$param_value = trim($request->post('param_value',''));
				$value_field = trim($request->post('value_field',''));

				$data['pic'] = $pic;
				$data['page_id'] = $page_id;
				$data['param_value'] = $param_value;
				$data['value_field'] = $value_field;
			}

			$data['cate_id'] = $cate_id;
			$data['level'] = $level;
			$data['pid'] = $pid;
			$data['title'] = $title;
			$data['desc'] = $desc;

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
			$spans = [
				['type'=>'option','label'=>'横排列数','value'=>0],
				['type'=>'option','label'=>'1列','value'=>1],
				['type'=>'option','label'=>'2列','value'=>2],
				['type'=>'option','label'=>'3列','value'=>3],
				['type'=>'option','label'=>'4列','value'=>4],
				['type'=>'option','label'=>'5列','value'=>5]
			];

			$service = new PageModel();
			$pageData = $service->getList($request,'option');

			$sort = $this->getMaxSort($pid);

			$action = [
				['type'=>'radio-group','label'=>'类别','prop'=>'cate_id','value'=>$id ? $data->cate_id : $cate_id,'hidden'=>true,'children'=>$cate],
				['type'=>'input','label'=>'组件名称','prop'=>'title','value'=>$id ? $data->title : '','rules'=>['required'=>true,'message'=>'组件名称不能为空']],
				['type'=>'input','label'=>'组件描述','prop'=>'desc','value'=>$id?$data->desc:'','placeholder'=>'组件描述'],
				['type'=>'select','label'=>'层级','prop'=>'level','value'=>$id ? $data->level : $level,'hidden'=>true,'children'=>$levelData],
				['type'=>'select','label'=>'父级','prop'=>'pid','value'=>$id ? $data->pid : $pid,'hidden'=>true,'children'=>$pidData],
			];

			if((!$id && !$pid) || ($id && !$data->pid)){
				array_push($action,
					['type'=>'cascader','label'=>'关联页面','prop'=>'page_id','value'=>$id?$data->page_id:'','attrs'=>['placeholder'=>'关联页面','options'=>$pageData],'rules'=>['required'=>true,'message'=>'请选择关联页面']]);

				array_push($action,['type'=>'checkbox-multiple','label'=>'显示选项','prop'=>'cmultiple','value'=>[],'children'=>[
					['type'=>'checkbox','label'=>'管理员权限','prop'=>'is_admin','value'=>$id?$data->is_admin:0],
					['type'=>'checkbox','label'=>'登录权限','prop'=>'is_login','value'=>$id?$data->is_login:0],
					['type'=>'checkbox','label'=>'显示标题','prop'=>'is_title','value'=>$id?$data->is_title:0],
					['type'=>'checkbox','label'=>'显示更多','prop'=>'is_more','value'=>$id?$data->is_more:0]
				]]);
				array_push($action,['type'=>'cascader','label'=>'更多链接','prop'=>'more_id','value'=>$id?$data->more_id:'','hidden'=>($id&&$data->is_more)?false:true,'attrs'=>['placeholder'=>'更多链接','options'=>$pageData],'rules'=>['required'=>false,'message'=>'请选择更多的跳转链接']],
				);
				array_push($action,
					['type'=>'radio-group','label'=>'排列方式','prop'=>'direction','value'=>$id?$data->direction:0,'children'=>[
						['type'=>'radio','label'=>'横排','value'=>0],
						['type'=>'radio','label'=>'竖排','value'=>1] 
					]],
					['type'=>'select','label'=>'横排列数','prop'=>'span','value'=>$id?$data->span:0,'placeholder'=>'横排列数','children'=>$spans,'rules'=>['required'=>true,'message'=>'请选择横排列数']],
					['type'=>'switch','label'=>'开启边框','prop'=>'is_border','value'=>$id?$data->is_border:0],
					['type'=>'color-picker','label'=>'组件背景色','prop'=>'bg_color','value'=>$id?$data->bg_color:''],
					['type'=>'input','label'=>'组件圆角','prop'=>'border_radius','value'=>$id&&$data->border_radius?$data->border_radius:'','placeholder'=>'组件圆角大小','slot'=>['suffix'=>['value'=>'px']]],
					['type'=>'form-group','label'=>'组件内边距','prop'=>'padding','delimiter'=>'-','children'=>[
						['type'=>'input','label'=>'上下边距','prop'=>'padding_y','value'=>$id&&$data->padding_y?$data->padding_y:'','placeholder'=>'上下边距','attrs'=>['style'=>['width'=>'182px']],'slot'=>['suffix'=>['value'=>'px']]],
						['type'=>'input','label'=>'左右边距','prop'=>'padding_x','value'=>$id&&$data->padding_x?$data->padding_x:'','placeholder'=>'左右边距','attrs'=>['style'=>['width'=>'182px']],'slot'=>['suffix'=>['value'=>'px']]]
					]],
					['type'=>'form-group','label'=>'图片宽高','prop'=>'picwh','value'=>'','delimiter'=>'-','children'=>[
						['type'=>'input','label'=>'图片宽度','prop'=>'width','value'=>$id?$data->width:'','placeholder'=>'0','attrs'=>['style'=>['width'=>'180px']],'slot'=>['prefix'=>['value'=>'宽度'],'suffix'=>['value'=>'px']],'rules'=>['required'=>true,'message'=>'图片宽度不能为空']],
						['type'=>'input','label'=>'图片高度','prop'=>'height','value'=>$id?$data->height:'','placeholder'=>'0','attrs'=>['style'=>['width'=>'180px']],'slot'=>['prefix'=>['value'=>'高度'],'suffix'=>['value'=>'px']],'rules'=>['required'=>true,'message'=>'图片高度不能为空']],
					],'rules'=>['required'=>true,'message'=>'图片宽高度不能为空']],
					['type'=>'input','label'=>'图片圆角','prop'=>'radius','value'=>$id&&$data->radius?$data->radius:'','placeholder'=>'圆角大小','slot'=>['suffix'=>['value'=>'px']]],
					['type'=>'radio-group','label'=>'文本字体大小','prop'=>'text_size','value'=>$id?$data->text_size:1,'children'=>[
						['type'=>'radio','label'=>'大','value'=>2],
						['type'=>'radio','label'=>'中','value'=>1],
						['type'=>'radio','label'=>'小','value'=>0],
					]],
					['type'=>'color-picker','label'=>'文本颜色','prop'=>'text_color','value'=>$id?$data->text_color:'','placeholder'=>'文本颜色'],
					['type'=>'input','label'=>'描述字体大小','prop'=>'desc_size','value'=>$id?$data->desc_size:'','placeholder'=>'描述字体大小','slot'=>['suffix'=>['value'=>'px']]],
					['type'=>'color-picker','label'=>'描述颜色','prop'=>'desc_color','value'=>$id?$data->desc_color:'','placeholder'=>'描述颜色'],
				);
			}else{
				array_push($action,
					['type'=>'upload','label'=>'图片','prop'=>'pic','value'=>$id?$data->pic:'','uploadAttrs'=>[
						'type'=>'img','size'=>'small','limit'=>1,'action'=>$this->host['api'].'upload'
					],'rules'=>['required'=>true,'message'=>'请上传图片']],
					['type'=>'cascader','label'=>'链接地址','prop'=>'page_id','value'=>$id?$data->page_id:'','attrs'=>['placeholder'=>'链接地址','options'=>$pageData],'rules'=>['required'=>true,'message'=>'请选择链接地址']],
					['type'=>'input','label'=>'参数值','prop'=>'param_value','value'=>$id?$data->param_value:'','hidden'=>$id&&$data->param_value ? false : true,'placeholder'=>'参数值(通常为相对应的ID)'],
					['type'=>'select','label'=>'关联字段','prop'=>'value_field','value'=>$id?$data->value_field:'','hidden'=>$id&&in_array($data->page_id,[29,30,31,36,37,38,39,40,41]) ? false : true,'placeholder'=>'选择关联字段','children'=>[
						['type'=>'option','label'=>'请选择','value'=>''],
						['type'=>'option','label'=>'余额','value'=>'balance'],
						['type'=>'option','label'=>'积分','value'=>'score'],
						['type'=>'option','label'=>'优惠券','value'=>'conpon'],
						['type'=>'option','label'=>'红包','value'=>'red'],
						['type'=>'option','label'=>'收藏','value'=>'favorite'],
						['type'=>'option','label'=>'足迹','value'=>'track'],
						['type'=>'option','label'=>'订单量','value'=>'order'],
					]],
					// ['type'=>'switch','label'=>'需要登录','prop'=>'is_login','value'=>$id?$data->is_login:0],
				);
			}

			return $action;
		}

		protected function getMapList(Request $request): array
		{
			return [
				['type'=>'id','label'=>'ID','prop'=>'id'],
				['type'=>'varchar','label'=>'组件名称','prop'=>'title','width'=>120],
				['type'=>'varchar','label'=>'组件描述','prop'=>'desc'],
				['type'=>'img','label'=>'图片','prop'=>'pic'],
				['type'=>'varchar','label'=>'列数','prop'=>'span'],
				['type'=>'varchar','label'=>'图片宽','prop'=>'width'],
				['type'=>'varchar','label'=>'图片高','prop'=>'height'],
				['type'=>'varchar','label'=>'圆角','prop'=>'height'],
				['type'=>'varchar','label'=>'链接','prop'=>'page_id'],
				['type'=>'switch','label'=>'管理员','prop'=>'is_admin'],
				['type'=>'switch','label'=>'有效','prop'=>'is_valid'],
				['type'=>'varchar','label'=>'排序','prop'=>'sort'],
			];
		}
	}
?>