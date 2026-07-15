<?php
	namespace dackou\model\Goods;

	use support\Request;

	class GoodsCateModel extends \dackou\Model{
		protected $table = 'GoodsCate';
		protected $title = '商品类别';
    	protected $show_method = 'tree';
    	protected $truncate = true;
		public $layer = 2;
		public $digit = 2;

		protected function getEveryList(Request $request){
			$cate = $this->getList($request,'tree');
			$service = new GoodsModel();
			$goods = $service->getList($request,'every');

			$data = [];

			foreach($cate as $items){
				if($items->pid == 0){
					if($this->layer == 2 && $items->number){
						$children = [];
						foreach($cate as $item){
							if($item->pid == $items->id){
								if($this->layer == 3 && $item->number){
									$child = [];
									foreach($cate as $itm){
										if($itm->pid == $item->id){
											array_push($child,$itm);
										}
									}
									if($child){
										$item->children = $child;
									}
								}

								$children_goods = [];
								foreach($goods as $value){
									if($value->cate_id == $item->id){
										array_push($children_goods,$value);
									}
								}
								if($children_goods){
									$item->goods = $children_goods;
								}

								array_push($children,$item);
							}
						}
						if($children){
							$items->children = $children;
						}
					}

					array_push($data,$items);
				}
			}

			return $data;
		}

		protected function validate(Request $request,$id = 0,$obj = null){
			$level = $request->post('level');
			if(!$level || $level == ''){
				$level = 1;
			}

			$pid = $request->post('pid');
			if(!$pid || $pid == ''){
				$pid = 0;
			}

			$data['level'] = $level;
			$data['pid'] = $pid;

			return $data;
		}

		protected function getActionList(Request $request,$id = 0): array
		{
			if($id){
				$data = $this->getList($request,$id);
			}

			$level = $request->input('level',1);
			$pid = $request->input('pid',0);

			$levels = [];
			for($i=1;$i<=$this->layer;$i++){
				array_push($levels,['type'=>'option','label'=>$i.'级','value'=>$i]);
			}

			$pids = $this->getList($request,'option',$id ? $data->level : $level);

			$sortNum = $this->getMaxSort($pid);

			$title = ['type'=>'input','label'=>'类别名称','prop'=>'title','value'=>$id ? $data->title : '','rules'=>['required'=>true,'message'=>'类别名称不能为空']];
			$sign = ['type'=>'input','label'=>'标识符','prop'=>'sign','value'=>$id ? $data->sign : '','placeholder'=>'标识符','rules'=>['required'=>true,'message'=>'标识符不能为空']];
			$pic = ['type'=>'upload','label'=>'图片','prop'=>'pic','value'=>$id ? $data->pic : '','attrs'=>[
				'type'=>'img','size'=>'small','limit'=>1,'action'=>$this->host['api'].'upload'
			],'rules'=>['required'=>true,'message'=>'请上传图片']];
			$lev = ['type'=>'select','label'=>'层级','prop'=>'level','value'=>$id ? $data->level : $level,'hidden'=>true,'children'=>$levels];
			$pi = ['type'=>'select','label'=>'父级','prop'=>'pid','value'=>$id ? $data->pid : $pid,'hidden'=>true,'children'=>$pids];
			$sort = ['type'=>'input','label'=>'排序','prop'=>'sort','value'=>$id ? $data->sort : $sortNum,'placeholder'=>'排序'];
			// $is_internal = ['type'=>'switch','label'=>'只对内','prop'=>'is_internal','value'=>$id? $data->is_internal : 0,'hidden'=>true];
			$valid = ['type'=>'switch','label'=>'有效','prop'=>'is_valid','value'=>$id? $data->is_valid : 0];

			$action = [$title,$sign,$pic,$lev,$pi,$sort];

			if($id){
				array_push($action,$valid);
			}

			return $action;
		}

		protected function getMapList(Request $request): array
		{
			return [
				['type'=>'id','label'=>'ID','prop'=>'id'],
				['type'=>'varchar','label'=>'类别名称','prop'=>'title'],
				['type'=>'varchar','label'=>'标识符','prop'=>'sign'],
				['type'=>'img','label'=>'图片','prop'=>'pic'],
				['type'=>'switch','label'=>'有效','prop'=>'is_valid'],
				['type'=>'varchar','label'=>'排序','prop'=>'sort'],
				['type'=>'varchar','label'=>'创建时间','prop'=>'create_time','width'=>180],
			];
		}
	}
?>