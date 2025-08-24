<?php
	namespace dackou\model\Goods;

	use support\Request;
	use support\Db;

	class GoodsTagsModel extends \dackou\Model{
		protected $table = 'GoodsTags';
		protected $title = '商品标签';

		protected function getOptionList(Request $request,mixed $data = [],mixed $field = []): array
		{
			try{
				$res = Db::table($this->table)
							->select('ID as id','TagsName as tags_name')
							->where('IsDel',0)
							->get();
				
				if($res){
					$data = [];
					foreach($res as $key => $item){
						array_push($data,['type'=>'checkbox','label'=>$item->tags_name,'value'=>$item->id]);
					}
					
					return $data;
				}

				return [];
			}catch(\Exception $e){
				return [];
			}
		}

		protected function validate(Request $request,$id = 0,$obj = null){
			$tags_name = trim($request->post('tags_name',''));
			$pic = $request->post('pic');
			if(is_array($pic)){
				$pic = end($pic);
			}
			$is_valid = $request->post('is_valid',0);
			$sort = $request->post('sort',0);

			if(!$tags_name){
				return '标签名称不能为空';
			}
			if($this->checkExists(['TagsName'=>$tags_name],$id)){
				return '标签名称已存在，请重新输入';
			}
			if(!$is_valid || empty($is_valid)){
				$is_valid = 0;
			}
			if(!is_numeric($sort)){
				return '排序只能填数字';
			}

			$data['tags_name'] = $tags_name;
			$data['pic'] = $pic;
			$data['is_valid'] = $is_valid ? 0 : 1;
			$data['sort'] = $sort;

			return $data;
		}

		protected function getActionList(Request $request,$id = 0): array
		{
			if($id){
				$data = $this->getList($request,$id);
			}

			$sort = $this->getMaxSort(0,false);

			$action = [
				['type'=>'input','label'=>'标签名称','prop'=>'tags_name','value'=>$id ? $data->tags_name : '','rules'=>['required'=>true,'message'=>'标签名称不能为空']],
				['type'=>'upload','label'=>'图片','prop'=>'pic','value'=>$id ? $data->pic : '','uploadAttrs'=>[
					'type'=>'img','size'=>'small','limit'=>1,'action'=>$this->host['api'].'upload'
				]],
				['type'=>'switch','label'=>'失效','prop'=>'is_valid','value'=>$id ? !$data->is_valid : 0],
				['type'=>'input','label'=>'排序','prop'=>'sort','value'=>$id ? $data->sort : $sort,'rules'=>['required'=>true,'message'=>'排序不能为空']],
			];

			return $action;
		}

		protected function getMapList(Request $request): array
		{
			return [
				['type'=>'id','label'=>'ID','prop'=>'id'],
				['type'=>'varchar','label'=>'标签名称','prop'=>'tags_name'],
				['type'=>'img','label'=>'图片','prop'=>'pic'],
				['type'=>'switch','label'=>'有效','prop'=>'is_valid'],
				['type'=>'varchar','label'=>'排序','prop'=>'sort'],
				['type'=>'varchar','label'=>'创建时间','prop'=>'create_time'],
			];
		}
	}
?>