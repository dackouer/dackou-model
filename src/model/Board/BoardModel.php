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

		protected function getDefaultData(Request $request){
			$chart = [
				'data' => [
					'title' => '数据统计图表',
					'type'  => 'line',
					'xaxis' => ['一月','二月','三月','四月','五月'],
					'data'  => [
						['name'=>'一月','type'=>'line','data'=>[128,245,491,40,10,159]],
						['name'=>'二月','type'=>'line','data'=>[78,145,231,90,40,259]],
						['name'=>'三月','type'=>'line','data'=>[68,200,301,120,100,189]],
						['name'=>'四月','type'=>'line','data'=>[98,165,181,80,40,203]],
						['name'=>'五月','type'=>'line','data'=>[108,185,127,74,29,190]],
					],
					'options' => ['width'=>'100%','height'=>'100%']
				]
			];
			$table = [
				'thead' => [
					['type'=>'varchar','label'=>'ID','prop'=>'id'],
                    ['type'=>'varchar','label'=>'用户名','prop'=>'username'],
                    ['type'=>'varchar','label'=>'昵称','prop'=>'nickname'],
                    ['type'=>'varchar','label'=>'角色','prop'=>'role'],
                    ['type'=>'varchar','label'=>'出生日期','prop'=>'birth','width'=>120],
				],
				'data' => [
					['id'=>1,'username'=>'张三','nickname'=>'张三','role'=>'会员1','birth'=>'1990-10-22'],
					['id'=>2,'username'=>'李四','nickname'=>'李四','role'=>'会员2','birth'=>'1987-04-16'],
                    ['id'=>3,'username'=>'王五','nickname'=>'王五','role'=>'会员3','birth'=>'1991-11-12'],
                    ['id'=>4,'username'=>'吴六','nickname'=>'吴六','role'=>'会员2','birth'=>'1988-09-17'],
                    ['id'=>5,'username'=>'周七','nickname'=>'周七','role'=>'会员1','birth'=>'1993-07-21'],
                    ['id'=>6,'username'=>'戴八','nickname'=>'戴八','role'=>'会员3','birth'=>'1985-02-15'],
                    ['id'=>7,'username'=>'唐九','nickname'=>'唐九','role'=>'会员2','birth'=>'1989-10-18'],
                    ['id'=>7,'username'=>'唐九','nickname'=>'唐九','role'=>'会员2','birth'=>'1989-10-18'],
				],
				'options' => ['size'=>'large','showOverflowTooltip'=>true,'maxHeight'=>400]
			];
			$data = [
				['id'=>10,'type'=>'','title'=>'标题一','desc'=>'','tip'=>'','value'=>'','gutter'=>20,'span'=>6,'icon'=>'','padding'=>15,'radius'=>10,'bg_color'=>'white','height'=>0,'text_color'=>'#67C23A','bg_color'=>'#F8F8F8','url'=>'','yesterday'=>'','today'=>'','rate_day'=>'','month'=>'','value_color'=>'','prefix'=>'','suffix'=>'','data'=>'','level'=>1,'pid'=>0,'number'=>4,'sort'=>1],
				['id'=>11,'type'=>'','title'=>'标题二','desc'=>'','tip'=>'','value'=>'','gutter'=>20,'span'=>3,'icon'=>'','padding'=>15,'radius'=>10,'bg_color'=>'white','height'=>0,'text_color'=>'#67C23A','bg_color'=>'#F8F8F8','url'=>'','yesterday'=>'','today'=>'','rate_day'=>'','month'=>'','value_color'=>'','prefix'=>'','suffix'=>'','data'=>'','level'=>1,'pid'=>0,'number'=>8,'sort'=>2],
				['id'=>12,'type'=>'','title'=>'标题三','desc'=>'','tip'=>'','value'=>'','gutter'=>20,'span'=>12,'icon'=>'','padding'=>15,'radius'=>10,'bg_color'=>'white','height'=>400,'text_color'=>'#67C23A','bg_color'=>'#F8F8F8','url'=>'','yesterday'=>'','today'=>'','rate_day'=>'','month'=>'','value_color'=>'','prefix'=>'','suffix'=>'','data'=>'','level'=>1,'pid'=>0,'number'=>2,'sort'=>3],

				['id'=>1001,'type'=>'user','title'=>'本月累计销售额','desc'=>'累计销售额','tip'=>'month','value'=>'8018','gutter'=>0,'span'=>0,'icon'=>'','padding'=>0,'radius'=>0,'bg_color'=>'','height'=>0,'text_color'=>'#67C23A','bg_color'=>'#F8F8F8','url'=>'','yesterday'=>'5449','today'=>'','rate_day'=>'47.14','month'=>'51545.18','value_color'=>'red','prefix'=>'','suffix'=>'元','data'=>'','level'=>2,'pid'=>10,'number'=>0,'sort'=>1],
				['id'=>1002,'type'=>'user','title'=>'本月用户访问量','desc'=>'用户访问量','tip'=>'month','value'=>'288','gutter'=>0,'span'=>0,'icon'=>'','padding'=>0,'radius'=>0,'bg_color'=>'','height'=>0,'text_color'=>'#67C23A','bg_color'=>'#F8F8F8','url'=>'','yesterday'=>'498','today'=>'','rate_day'=>'-40.70','month'=>'2412','value_color'=>'red','prefix'=>'','suffix'=>'Pv','data'=>'','level'=>2,'pid'=>10,'number'=>0,'sort'=>2],
				['id'=>1003,'type'=>'user','title'=>'本月订单量','desc'=>'订单量','tip'=>'month','value'=>'25','gutter'=>0,'span'=>0,'icon'=>'','padding'=>0,'radius'=>0,'bg_color'=>'','height'=>0,'text_color'=>'#67C23A','bg_color'=>'#F8F8F8','url'=>'','yesterday'=>'47','today'=>'','rate_day'=>'-58.28','month'=>'61','value_color'=>'red','prefix'=>'','suffix'=>'单','data'=>'','level'=>2,'pid'=>10,'number'=>0,'sort'=>3],
				['id'=>1004,'type'=>'user','title'=>'本月新增用户','desc'=>'新增用户','tip'=>'month','value'=>'41','gutter'=>0,'span'=>0,'icon'=>'','padding'=>0,'radius'=>0,'bg_color'=>'','height'=>0,'text_color'=>'#67C23A','bg_color'=>'#F8F8F8','url'=>'','yesterday'=>'69','today'=>'','rate_day'=>'-40.57','month'=>'88','value_color'=>'red','prefix'=>'','suffix'=>'人','data'=>'','level'=>2,'pid'=>10,'number'=>0,'sort'=>4],

				['id'=>1101,'type'=>'lnk','title'=>'用户管理','desc'=>'','tip'=>'','value'=>'','gutter'=>0,'span'=>0,'icon'=>'Pear','padding'=>0,'radius'=>0,'bg_color'=>'','height'=>0,'text_color'=>'#67C23A','bg_color'=>'#F8F8F8','url'=>'','yesterday'=>'','today'=>'','rate_day'=>'','month'=>'','value_color'=>'','prefix'=>'','suffix'=>'','data'=>'','level'=>2,'pid'=>11,'number'=>0,'sort'=>1],
				['id'=>1102,'type'=>'lnk','title'=>'系统设置','desc'=>'','tip'=>'','value'=>'','gutter'=>0,'span'=>0,'icon'=>'Magnet','padding'=>0,'radius'=>0,'bg_color'=>'','height'=>0,'text_color'=>'#E6A23C','bg_color'=>'#F8F8F8','url'=>'','yesterday'=>'','today'=>'','rate_day'=>'','month'=>'','value_color'=>'','prefix'=>'','suffix'=>'','data'=>'','level'=>2,'pid'=>11,'number'=>0,'sort'=>2],
				['id'=>1103,'type'=>'lnk','title'=>'商品管理','desc'=>'','tip'=>'','value'=>'','gutter'=>0,'span'=>0,'icon'=>'Goods','padding'=>0,'radius'=>0,'bg_color'=>'','height'=>0,'text_color'=>'#F56C6C','bg_color'=>'#F8F8F8','url'=>'','yesterday'=>'','today'=>'','rate_day'=>'','month'=>'','value_color'=>'','prefix'=>'','suffix'=>'','data'=>'','level'=>2,'pid'=>11,'number'=>0,'sort'=>3],
				['id'=>1104,'type'=>'lnk','title'=>'订单管理','desc'=>'','tip'=>'','value'=>'','gutter'=>0,'span'=>0,'icon'=>'Tickets','padding'=>0,'radius'=>0,'bg_color'=>'','height'=>0,'text_color'=>'#409EFF','bg_color'=>'#F8F8F8','url'=>'','yesterday'=>'','today'=>'','rate_day'=>'','month'=>'','value_color'=>'','prefix'=>'','suffix'=>'','data'=>'','level'=>2,'pid'=>11,'number'=>0,'sort'=>4],
				['id'=>1105,'type'=>'lnk','title'=>'活动管理','desc'=>'','tip'=>'','value'=>'','gutter'=>0,'span'=>0,'icon'=>'ChatLineRound','padding'=>0,'radius'=>0,'bg_color'=>'','height'=>0,'text_color'=>'red','bg_color'=>'#F8F8F8','url'=>'','yesterday'=>'','today'=>'','rate_day'=>'','month'=>'','value_color'=>'','prefix'=>'','suffix'=>'','data'=>'','level'=>2,'pid'=>11,'number'=>0,'sort'=>5],
				['id'=>1106,'type'=>'lnk','title'=>'文章管理','desc'=>'','tip'=>'','value'=>'','gutter'=>0,'span'=>0,'icon'=>'Notebook','padding'=>0,'radius'=>0,'bg_color'=>'','height'=>0,'text_color'=>'orange','bg_color'=>'#F8F8F8','url'=>'','yesterday'=>'','today'=>'','rate_day'=>'','month'=>'','value_color'=>'','prefix'=>'','suffix'=>'','data'=>'','level'=>2,'pid'=>11,'number'=>0,'sort'=>6],
				['id'=>1107,'type'=>'lnk','title'=>'分销管理','desc'=>'','tip'=>'','value'=>'','gutter'=>0,'span'=>0,'icon'=>'Monitor','padding'=>0,'radius'=>0,'bg_color'=>'','height'=>0,'text_color'=>'yellow','bg_color'=>'#F8F8F8','url'=>'','yesterday'=>'','today'=>'','rate_day'=>'','month'=>'','value_color'=>'','prefix'=>'','suffix'=>'','data'=>'','level'=>2,'pid'=>11,'number'=>0,'sort'=>7],
				['id'=>1108,'type'=>'lnk','title'=>'优惠券','desc'=>'','tip'=>'','value'=>'','gutter'=>0,'span'=>0,'icon'=>'Dish','padding'=>0,'radius'=>0,'bg_color'=>'','height'=>0,'text_color'=>'pink','bg_color'=>'#F8F8F8','url'=>'','yesterday'=>'','today'=>'','rate_day'=>'','month'=>'','value_color'=>'','prefix'=>'','suffix'=>'','data'=>'','level'=>2,'pid'=>11,'number'=>0,'sort'=>8],

				['id'=>1201,'type'=>'chart','title'=>'资产统计图表','desc'=>'','tip'=>'','value'=>'','gutter'=>0,'span'=>16,'icon'=>'','padding'=>0,'radius'=>0,'bg_color'=>'','height'=>0,'text_color'=>'','bg_color'=>'','url'=>'','yesterday'=>'','today'=>'','rate_day'=>'','month'=>'','value_color'=>'','prefix'=>'','suffix'=>'','data'=>$this->getJsonData($chart),'level'=>2,'pid'=>12,'number'=>0,'sort'=>1],
				['id'=>1202,'type'=>'table','title'=>'数据表','desc'=>'','tip'=>'','value'=>'','gutter'=>0,'span'=>8,'icon'=>'','padding'=>0,'radius'=>0,'bg_color'=>'','height'=>0,'text_color'=>'','bg_color'=>'','url'=>'','yesterday'=>'','today'=>'','rate_day'=>'','month'=>'','value_color'=>'','prefix'=>'','suffix'=>'','data'=>$this->getJsonData($table),'level'=>2,'pid'=>12,'number'=>0,'sort'=>2],
			];

			return $data;
		}

		// protected function validate(Request $request,$id = 0,$obj = null){
		// 	// var_dump($request->post());
		// 	$type = $request->post('type',1);
		// 	$title = trim($request->post('title',''));
		// 	$desc = trim($request->post('desc',''));
		// 	$value = trim($request->post('value',''));
		// 	if(empty($value)){
		// 		$value = 0;
		// 	}
		// 	$old = trim($request->post('old',''));
		// 	if(empty($old)){
		// 		$old = 0;
		// 	}
		// 	$rate = trim($request->post('rate',''));
		// 	if(empty($rate)){
		// 		$rate = 0;
		// 	}
		// 	$gutter = $request->post('gutter',20);
		// 	if(empty($gutter)){
		// 		$gutter = 0;
		// 	}
		// 	$span = $request->post('span',4);
		// 	if(empty($span)){
		// 		$span = 0;
		// 	}
		// 	$icon = trim($request->post('icon',''));
		// 	$pic = $request->post('pic');
		// 	if(is_array($pic)){
		// 		$pic = end($pic) ?? '';
		// 	}
		// 	$text_size = $request->post('text_size',15);
		// 	if(empty($text_size)){
		// 		$text_size = 0;
		// 	}
		// 	$text_color = trim($request->post('text_color',''));
		// 	$value_size = $request->post('value_size',22);
		// 	if(empty($value_size)){
		// 		$value_size = 0;
		// 	}
		// 	$value_color = trim($request->post('value_color',''));
		// 	$desc_size = $request->post('desc_size',14);
		// 	if(empty($desc_size)){
		// 		$desc_size = 0;
		// 	}
		// 	$desc_color = trim($request->post('desc_color',''));
		// 	$prefix = trim($request->post('prefix',''));
		// 	$suffix = trim($request->post('suffix',''));
		// 	$level = $request->post('level',1);
		// 	$pid = $request->post('pid',0);
		// 	$sort = $request->post('sort',1);


		// 	if(!in_array($type,[1,2,3])){
		// 		return '无效的数据类别';
		// 	}
		// 	if(!$title){
		// 		return '标题不能为空';
		// 	}
		// 	if($this->checkExists(['Title'=>$title,'PID'=>$pid],$id)){
		// 		return '标题已存在';
		// 	}

		// 	if(!is_numeric($gutter) || $gutter < 0){
		// 		return '列间距只能填写数字且大于0';
		// 	}

		// 	if(!is_numeric($value)){
		// 		return '数据只能填写数字';
		// 	}

		// 	if(!is_numeric($old)){
		// 		return '旧数据只能填写数字';
		// 	}

		// 	if(!is_numeric($rate)){
		// 		return '升降率只能填写数字';
		// 	}

		// 	if(!is_numeric($span)){
		// 		return '单行列数只能填写数字';
		// 	}
		// 	if(!in_array($span,[2,3,4,5,6,8,10,12])){
		// 		return '单行列数填写有误';
		// 	}

		// 	$data['type'] = $type;
		// 	$data['title'] = $title;
		// 	$data['desc'] = $desc;
		// 	$data['value'] = $value;
		// 	$data['old'] = $old;
		// 	$data['rate'] = $rate;
		// 	$data['value_color'] = $value_color;
		// 	$data['icon'] = $icon;
		// 	$data['pic'] = $pic ?? '';
		// 	$data['level'] = $level;
		// 	$data['pid'] = $pid;
		// 	$data['sort'] = $sort;
		// 	$data['prefix'] = $prefix;
		// 	$data['suffix'] = $suffix;
		// 	$data['span'] = $span;
		// 	$data['gutter'] = $gutter;
		// 	$data['text_size'] = $text_size;
		// 	$data['text_color'] = $text_color;
		// 	$data['value_size'] = $value_size;
		// 	$data['value_color'] = $value_color;
		// 	$data['desc_size'] = $desc_size;
		// 	$data['desc_color'] = $desc_color;

		// 	return $data;
		// }

		// protected function getActionList(Request $request,$id = 0): array
		// {
		// 	if($id){
		// 		$data = $this->getList($request,$id);
		// 	}

		// 	$typeData = $this->getFieldOption($this->type_value);
		// 	$spanData = [];
		// 	$spanList = [2,3,4,5,6,8,10,12];
		// 	foreach($spanList as $v){
		// 		array_push($spanData,['type'=>'option','label'=>$v.'列','value'=>$v]);
		// 	}
		// 	$chartData = [
		// 		['type'=>'option','label'=>'园区收益图表','value'=>1],
		// 		['type'=>'option','label'=>'资产统计图表','value'=>2],
		// 		['type'=>'option','label'=>'出售统计图表','value'=>3],
		// 		['type'=>'option','label'=>'出租统计图表','value'=>4],
		// 		['type'=>'option','label'=>'园区面积图表','value'=>5],
		// 	];

		// 	$pid = $request->input('pid',0);
		// 	$level = $request->input('level',1);
		// 	$ilevel = [];
		// 	for($i=1;$i<=$this->layer;$i++){
		// 		array_push($ilevel,['type'=>'option','label'=>$i.'级','value'=>$i]);
		// 	}
		// 	if($this->layer == 2){
		// 		$ipid = $this->getList($request,'option',['Title'=>'title','ID'=>'id'],['PID'=>0]);
		// 	}elseif($this->layer == 3){
		// 		$ipid = $this->getList($request,'option',['Title'=>'title','ID'=>'id'],['Level'=>2]);
		// 	}
		// 	$sort = $this->getMaxSort($pid);

		// 	$action = [];
		// 	if(($id && !$data->pid) || (!$id && !$pid)){
		// 		array_push($action,['type'=>'radio-group','label'=>'数据类型','prop'=>'type','value'=>$id ? (int)$data->type : 1,'children'=>$typeData,'rules'=>['required'=>true,'message'=>'请选择数据类型']]);
		// 	}
		// 	array_push($action,['type'=>'input','label'=>'标题','prop'=>'title','value'=>$data->title ?? '','rules'=>['required'=>true,'message'=>'标题不能为空']]);

		// 	if(($id && $data->pid) || (!$id && $pid)){
		// 		array_push($action,['type'=>'input','label'=>'描述','prop'=>'desc','value'=>$data->desc ?? '']);
		// 		array_push($action,['type'=>'input','label'=>'数值','prop'=>'value','value'=>$data->value ?? '']);
		// 		array_push($action,['type'=>'input','label'=>'旧数值','prop'=>'old','value'=>$data->old ?? '']);
		// 		array_push($action,['type'=>'input','label'=>'升降率','prop'=>'rate','value'=>$data->rate ?? '']);
		// 		array_push($action,['type'=>'icon','label'=>'图标','prop'=>'icon','value'=>$data->icon ?? '']);
		// 		array_push($action,['type'=>'upload','label'=>'图片','prop'=>'pic','value'=>$data->pic ?? '']);
		// 		array_push($action,['type'=>'color-picker','label'=>'颜色','prop'=>'value_color','value'=>$data->value_color ?? '']);
		// 		array_push($action,['type'=>'input','label'=>'前缀','prop'=>'prefix','value'=>$data->prefix ?? '']);
		// 		array_push($action,['type'=>'input','label'=>'后缀','prop'=>'suffix','value'=>$data->suffix ?? '']);
		// 	}else{
		// 		array_push($action,['type'=>'select','label'=>'单行列数','prop'=>'span','value'=>$data->span ?? 4,'children'=>$spanData,'rules'=>['required'=>true,'message'=>'请选择列数']]);
		// 		array_push($action,['type'=>'input','label'=>'列间距','prop'=>'gutter','value'=>$data->gutter ?? 20]);
		// 		array_push($action,['type'=>'input','label'=>'标题大小','prop'=>'text_size','value'=>$data->text_size ?? 16]);
		// 		array_push($action,['type'=>'color-picker','label'=>'标题颜色','prop'=>'text_color','value'=>$data->text_color ?? '']);
		// 		array_push($action,['type'=>'input','label'=>'数值大小','prop'=>'value_size','value'=>$data->value_size ?? 22]);
		// 		array_push($action,['type'=>'color-picker','label'=>'数值颜色','prop'=>'value_color','value'=>$data->value_color ?? '']);
		// 		array_push($action,['type'=>'input','label'=>'描述大小','prop'=>'desc_size','value'=>$data->desc_size ?? 14]);
		// 		array_push($action,['type'=>'color-picker','label'=>'描述颜色','prop'=>'desc_color','value'=>$data->desc_color ?? '']);
		// 		array_push($action,['type'=>'select','label'=>'选择图表','prop'=>'value','value'=>$data->value ?? '','hidden'=>true,'children'=>$chartData]);
		// 	}

		// 	array_push($action,['type'=>'select','label'=>'层级','prop'=>'level','value'=>$id ? $data->level : $level,'hidden'=>true,'children'=>$ilevel],
		// 		['type'=>'select','label'=>'父级','prop'=>'pid','value'=>$id ? $data->pid : $pid,'hidden'=>true,'children'=>$ipid],
		// 		['type'=>'input','label'=>'排序','prop'=>'sort','value'=>$id ? $data->sort : $sort]);

		// 	return $action;
		// }

		// protected function getMapList(Request $request): array
		// {
		// 	return [
		// 		['type'=>'varchar','label'=>'ID','prop'=>'id','align'=>'start'],
		// 		['type'=>'varchar','label'=>'标题','prop'=>'title','align'=>'start','width'=>180],
		// 		['type'=>'varchar','label'=>'描述','prop'=>'desc'],
		// 		['type'=>'varchar','label'=>'数值','prop'=>'value'],
		// 		['type'=>'varchar','label'=>'旧数值','prop'=>'old'],
		// 		['type'=>'varchar','label'=>'升降率','prop'=>'rate'],
		// 		['type'=>'icon','label'=>'图标','prop'=>'icon'],
		// 		['type'=>'img','label'=>'图片','prop'=>'pic'],
		// 		['type'=>'varchar','label'=>'列数','prop'=>'span'],
		// 		['type'=>'varchar','label'=>'列间距','prop'=>'gutter'],
		// 		['type'=>'varchar','label'=>'前缀','prop'=>'prefix'],
		// 		['type'=>'varchar','label'=>'后缀','prop'=>'suffix'],
		// 		['type'=>'varchar','label'=>'排序','prop'=>'sort'],
		// 	];
		// }
	}
?>