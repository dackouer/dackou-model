<?php
	namespace dackou\model\Board;

	use support\Request;
	use support\Db;

	class BoardModel extends \dackou\Model{
		protected $table = 'Board';
		protected $title = '面板';
    	protected $show_method = 'tree';
    	protected $truncate = true;
		public $layer = 2;
		public $digit = 2;
		protected $type_value = [1=>'列表',2=>'图表',3=>'表格'];

		protected function validate(Request $request,$id = 0,$obj = null){
			// var_dump($request->post());
			$type = $request->post('type',1);
			$title = trim($request->post('title',''));
			$desc = trim($request->post('desc',''));
			$value = trim($request->post('value',''));
			if(empty($value)){
				$value = 0;
			}
			$old = trim($request->post('old',''));
			if(empty($old)){
				$old = 0;
			}
			$rate = trim($request->post('rate',''));
			if(empty($rate)){
				$rate = 0;
			}
			$gutter = $request->post('gutter',20);
			if(empty($gutter)){
				$gutter = 0;
			}
			$span = $request->post('span',4);
			if(empty($span)){
				$span = 0;
			}
			$icon = trim($request->post('icon',''));
			$pic = $request->post('pic');
			if(is_array($pic)){
				$pic = end($pic) ?? '';
			}
			$text_size = $request->post('text_size',15);
			if(empty($text_size)){
				$text_size = 0;
			}
			$text_color = trim($request->post('text_color',''));
			$value_size = $request->post('value_size',22);
			if(empty($value_size)){
				$value_size = 0;
			}
			$value_color = trim($request->post('value_color',''));
			$desc_size = $request->post('desc_size',14);
			if(empty($desc_size)){
				$desc_size = 0;
			}
			$desc_color = trim($request->post('desc_color',''));
			$prefix = trim($request->post('prefix',''));
			$suffix = trim($request->post('suffix',''));
			$level = $request->post('level',1);
			$pid = $request->post('pid',0);
			$sort = $request->post('sort',1);


			if(!in_array($type,[1,2,3])){
				return '无效的数据类别';
			}
			if(!$title){
				return '标题不能为空';
			}
			if($this->checkExists(['Title'=>$title,'PID'=>$pid],$id)){
				return '标题已存在';
			}

			if(!is_numeric($gutter) || $gutter < 0){
				return '列间距只能填写数字且大于0';
			}

			if(!is_numeric($value)){
				return '数据只能填写数字';
			}

			if(!is_numeric($old)){
				return '旧数据只能填写数字';
			}

			if(!is_numeric($rate)){
				return '升降率只能填写数字';
			}

			if(!is_numeric($span)){
				return '单行列数只能填写数字';
			}
			if(!in_array($span,[2,3,4,5,6,8,10,12])){
				return '单行列数填写有误';
			}

			$data['type'] = $type;
			$data['title'] = $title;
			$data['desc'] = $desc;
			$data['value'] = $value;
			$data['old'] = $old;
			$data['rate'] = $rate;
			$data['value_color'] = $value_color;
			$data['icon'] = $icon;
			$data['pic'] = $pic ?? '';
			$data['level'] = $level;
			$data['pid'] = $pid;
			$data['sort'] = $sort;
			$data['prefix'] = $prefix;
			$data['suffix'] = $suffix;
			$data['span'] = $span;
			$data['gutter'] = $gutter;
			$data['text_size'] = $text_size;
			$data['text_color'] = $text_color;
			$data['value_size'] = $value_size;
			$data['value_color'] = $value_color;
			$data['desc_size'] = $desc_size;
			$data['desc_color'] = $desc_color;

			return $data;
		}

		protected function getActionList(Request $request,$id = 0): array
		{
			if($id){
				$data = $this->getList($request,$id);
			}

			$typeData = $this->getFieldOption($this->type_value);
			$spanData = [];
			$spanList = [2,3,4,5,6,8,10,12];
			foreach($spanList as $v){
				array_push($spanData,['type'=>'option','label'=>$v.'列','value'=>$v]);
			}
			$chartData = [
				['type'=>'option','label'=>'园区收益图表','value'=>1],
				['type'=>'option','label'=>'资产统计图表','value'=>2],
				['type'=>'option','label'=>'出售统计图表','value'=>3],
				['type'=>'option','label'=>'出租统计图表','value'=>4],
				['type'=>'option','label'=>'园区面积图表','value'=>5],
			];

			$pid = $request->input('pid',0);
			$level = $request->input('level',1);
			$ilevel = [];
			for($i=1;$i<=$this->layer;$i++){
				array_push($ilevel,['type'=>'option','label'=>$i.'级','value'=>$i]);
			}
			if($this->layer == 2){
				$ipid = $this->getList($request,'option',['Title'=>'title','ID'=>'id'],['PID'=>0]);
			}elseif($this->layer == 3){
				$ipid = $this->getList($request,'option',['Title'=>'title','ID'=>'id'],['Level'=>2]);
			}
			$sort = $this->getMaxSort($pid);

			$action = [];
			if(($id && !$data->pid) || (!$id && !$pid)){
				array_push($action,['type'=>'radio-group','label'=>'数据类型','prop'=>'type','value'=>$id ? (int)$data->type : 1,'children'=>$typeData,'rules'=>['required'=>true,'message'=>'请选择数据类型']]);
			}
			array_push($action,['type'=>'input','label'=>'标题','prop'=>'title','value'=>$data->title ?? '','rules'=>['required'=>true,'message'=>'标题不能为空']]);

			if(($id && $data->pid) || (!$id && $pid)){
				array_push($action,['type'=>'input','label'=>'描述','prop'=>'desc','value'=>$data->desc ?? '']);
				array_push($action,['type'=>'input','label'=>'数值','prop'=>'value','value'=>$data->value ?? '']);
				array_push($action,['type'=>'input','label'=>'旧数值','prop'=>'old','value'=>$data->old ?? '']);
				array_push($action,['type'=>'input','label'=>'升降率','prop'=>'rate','value'=>$data->rate ?? '']);
				array_push($action,['type'=>'icon','label'=>'图标','prop'=>'icon','value'=>$data->icon ?? '']);
				array_push($action,['type'=>'upload','label'=>'图片','prop'=>'pic','value'=>$data->pic ?? '']);
				array_push($action,['type'=>'color-picker','label'=>'颜色','prop'=>'value_color','value'=>$data->value_color ?? '']);
				array_push($action,['type'=>'input','label'=>'前缀','prop'=>'prefix','value'=>$data->prefix ?? '']);
				array_push($action,['type'=>'input','label'=>'后缀','prop'=>'suffix','value'=>$data->suffix ?? '']);
			}else{
				array_push($action,['type'=>'select','label'=>'单行列数','prop'=>'span','value'=>$data->span ?? 4,'children'=>$spanData,'rules'=>['required'=>true,'message'=>'请选择列数']]);
				array_push($action,['type'=>'input','label'=>'列间距','prop'=>'gutter','value'=>$data->gutter ?? 20]);
				array_push($action,['type'=>'input','label'=>'标题大小','prop'=>'text_size','value'=>$data->text_size ?? 16]);
				array_push($action,['type'=>'color-picker','label'=>'标题颜色','prop'=>'text_color','value'=>$data->text_color ?? '']);
				array_push($action,['type'=>'input','label'=>'数值大小','prop'=>'value_size','value'=>$data->value_size ?? 22]);
				array_push($action,['type'=>'color-picker','label'=>'数值颜色','prop'=>'value_color','value'=>$data->value_color ?? '']);
				array_push($action,['type'=>'input','label'=>'描述大小','prop'=>'desc_size','value'=>$data->desc_size ?? 14]);
				array_push($action,['type'=>'color-picker','label'=>'描述颜色','prop'=>'desc_color','value'=>$data->desc_color ?? '']);
				array_push($action,['type'=>'select','label'=>'选择图表','prop'=>'value','value'=>$data->value ?? '','hidden'=>true,'children'=>$chartData]);
			}

			array_push($action,['type'=>'select','label'=>'层级','prop'=>'level','value'=>$id ? $data->level : $level,'hidden'=>true,'children'=>$ilevel],
				['type'=>'select','label'=>'父级','prop'=>'pid','value'=>$id ? $data->pid : $pid,'hidden'=>true,'children'=>$ipid],
				['type'=>'input','label'=>'排序','prop'=>'sort','value'=>$id ? $data->sort : $sort]);

			return $action;
		}

		protected function getMapList(Request $request): array
		{
			return [
				['type'=>'varchar','label'=>'ID','prop'=>'id','align'=>'start'],
				['type'=>'varchar','label'=>'标题','prop'=>'title','align'=>'start','width'=>180],
				['type'=>'varchar','label'=>'描述','prop'=>'desc'],
				['type'=>'varchar','label'=>'数值','prop'=>'value'],
				['type'=>'varchar','label'=>'旧数值','prop'=>'old'],
				['type'=>'varchar','label'=>'升降率','prop'=>'rate'],
				['type'=>'icon','label'=>'图标','prop'=>'icon'],
				['type'=>'img','label'=>'图片','prop'=>'pic'],
				['type'=>'varchar','label'=>'列数','prop'=>'span'],
				['type'=>'varchar','label'=>'列间距','prop'=>'gutter'],
				['type'=>'varchar','label'=>'前缀','prop'=>'prefix'],
				['type'=>'varchar','label'=>'后缀','prop'=>'suffix'],
				['type'=>'varchar','label'=>'排序','prop'=>'sort'],
			];
		}
	}
?>