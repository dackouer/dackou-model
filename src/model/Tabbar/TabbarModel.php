<?php
	namespace dackou\model\Tabbar;

	use support\Request;
	use support\Db;
	use dackou\model\Page\PageModel;

	class TabbarModel extends \dackou\Model{
		protected $table = 'Tabbar';
		protected $title = '导航';
		protected $page = false;
		protected $cate_value = [0=>'其它',1=>'mini',2=>'app',3=>'web'];

		protected function getAllList(Request $request){
			$field = $this->getList($request,'field',true);
			array_push($field,'page.Title as page_title');
			array_push($field,'page.Url as page_url');
			array_push($field,'page.Param as param');
			array_push($field,'page.IsParam as is_param');
			$where = $this->getWhere($request);

			$cate_id = $request->input('cate_id',1);
			if(in_array($cate_id,[1,2,3])){
				array_push($where,[$this->table.'.CateID','=',$cate_id]);
			}

			$object = Db::table($this->table)
						->join('page','PageID','=','page.ID')
						->select(...$field)
						->where($where)
						->orderBy($this->table.'.Level','asc')
						->orderBy($this->table.'.Sort','asc')
						->get();
			if($object){
				foreach($object as $k => $v){
					$object[$k]->url = $object[$k]->page_url;
					if($object[$k]->is_param && $object[$k]->param_value){
						$object[$k]->url .= '?'.$object[$k]->param.'='.$object[$k]->param_value;
					}
				}
			}
			return $object;
		}

		protected function getPageList(Request $request){
			$field = $this->getList($request,'field',true);
			array_push($field,'page.Title as page_title');
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

			return ['rows' => $rows,'data' => $object];
		}

		protected function validate(Request $request,$id = 0,$obj = null){

			$cate_id = $request->input('cate_id',1);
			if(!in_array($cate_id,[1,2,3])){
				return '无效的类别';
			}
			$level = $request->post('level',1);
			$pid = $request->post('pid',0);

			$title = trim($request->post('title',''));
			if(!$title){
				return '标题名称不能为空';
			}
			if($this->checkExists(['Title'=>$title],$id)){
				return '标题名称已存在,请重新输入';
			}
			$pic = $request->post('pic');
			if(is_array($pic) && isset($pic[0])){
				$pic = $pic[0];
			}
			$active_pic = $request->post('active_pic');
			if(is_array($active_pic) && isset($active_pic[0])){
				$active_pic = $active_pic[0];
			}

			if(!$pic){
				return '请上传图片';
			}
			if(!$active_pic){
				return '请上传激活图片';
			}

			$color = trim($request->post('color',''));
			$active_color = trim($request->post('active_color',''));

			$link_type = $request->post('link_type',1);
			$page_id = $request->post('page_id');
			if(is_array($page_id)){
				$page_id = end($page_id);
			}

			if(!$page_id){
				return '请选择链接';
			}

			$is_valid = $request->post('is_valid');
			$sort = $request->post('sort');
			if(!is_numeric($sort)){
				return '排序只能填数字';
			}

			$data['cate_id'] = $cate_id;
			$data['level'] = $level;
			$data['pid'] = $pid;
			$data['title'] = $title;
			$data['pic'] = $pic;
			$data['active_pic'] = $active_pic;
			$data['color'] = $color;
			$data['active_color'] = $active_color;
			$data['link_type'] = $link_type;
			$data['page_id'] = $page_id;
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
				['type'=>'input','label'=>'标题名称','prop'=>'title','value'=>$id ? $data->title : '','required'=>true],
				['type'=>'select','label'=>'层级','prop'=>'level','value'=>$id ? $data->level : $level,'hidden'=>true,'children'=>$levelData],
				['type'=>'select','label'=>'父级','prop'=>'pid','value'=>$id ? $data->pid : $pid,'hidden'=>true,'children'=>$pidData],
				['type'=>'upload','label'=>'静态图','prop'=>'pic','value'=>$id ? $data->pic : '','attrs'=>$this->getUploadOptions('img',1,'small'),'required'=>true],
				['type'=>'upload','label'=>'激活图片','prop'=>'active_pic','value'=>$id ? $data->active_pic : '','attrs'=>$this->getUploadOptions('img',1,'small'),'required'=>true],
				['type'=>'group','label'=>'文本颜色','prop'=>'colors','delimiter'=>'-','children'=>[
					['type'=>'color','label'=>'静态文本色','prop'=>'color','value'=>$id ? $data->color : ''],
					['type'=>'color','label'=>'激活文本色','prop'=>'active_color','value'=>$id ? $data->active_color : ''],
				]],
				['type'=>'radio-group','label'=>'跳转方式','prop'=>'link_type','value'=>$id ? $data->link_type : 1,'hidden'=>true,'children'=>[
					['type'=>'option','label'=>'内部页面','value'=>1],
					['type'=>'option','label'=>'外部小程序','value'=>2],
				]],
				['type'=>'cascader','label'=>'链接','prop'=>'page_id','value'=>$id?$data->page_id:[],'children'=>$page,'required'=>true],
			];

			array_push($action,['type'=>'switch','label'=>'有效','prop'=>'is_valid','value'=>$id ? $data->is_valid : 1],
				['type'=>'input','label'=>'排序','prop'=>'sort','value'=>$id ? $data->sort : $sort]);

			return $action;
		}

		protected function getMapList(Request $request): array
		{
			return [
				['type'=>'id','label'=>'ID','prop'=>'id'],
				['type'=>'varchar','label'=>'标题','prop'=>'title'],
				['type'=>'img','label'=>'静态图','prop'=>'pic'],
				['type'=>'img','label'=>'激活图','prop'=>'active_pic'],
				['type'=>'varchar','label'=>'链接','prop'=>'page_title'],
				['type'=>'switch','label'=>'有效','prop'=>'is_valid','is_switch'=>true],
				['type'=>'varchar','label'=>'排序','prop'=>'sort'],
			];
		}
	}
?>