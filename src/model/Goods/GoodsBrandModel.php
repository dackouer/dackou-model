<?php
	namespace dackou\model\Goods;

	use support\Request;
	use support\Db;

	class GoodsBrandModel extends \dackou\Model{
		protected $table = 'GoodsBrand';
		protected $title = '商品品牌';
		protected $cate_value = [-1=>'已失效',0=>'未申请',1=>'TM标',2=>'R标'];

		protected function getOptionList(Request $request,mixed $data = [],mixed $field = []): array
    	{
    		try{
    			$field = ['ID as value','BrandName as label'];
    			$object = Db::table($this->table)
    						->select(...$field)
    						->where('IsDel',0)
    						->get();
    			if($object){
    				$option = [];
    				foreach($object as $k => $v){
    					array_push($option,['type'=>'option','label'=>$object[$k]->label,'value'=>(int)$object[$k]->value]);
    				}
    				return $option;
    			}

    			return [];
    		}catch(\Exception $e){
    			return $this->getExceptionError($e);
    		}
    	}

		protected function validate(Request $request,$id = 0,$obj = null){
			$merchant_id = $request->post('merchant_id',0);
			if(!$merchant_id || empty($merchant_id)){
				$merchant_id = 0;
			}

			$brand_name = trim($request->post('brand_name',''));
			if(!$brand_name){
				return '品牌名称不能为空';
			}
			if($this->checkExists(['MerchantID'=>$merchant_id,'BrandName'=>$brand_name],$id)){
				return '品牌名称已存在,请重新输入';
			}
			$logo = $request->post('logo');
			if(is_array($logo)){
				$logo = end($logo);
			}
			$cate_id = $request->post('cate_id',0);
			if(!$cate_id || empty($cate_id)){
				$cate_id = 0;
			}
			if(!in_array($cate_id,[-1,0,1,2])){
				return '无效的商品状态';
			}

			$is_valid = $request->post('is_valid',0);
			if(!in_array($is_valid,[0,1])){
				return '无效的失效状态';
			}

			$sort = $request->post('sort',1);
			if(!$sort || empty($sort)){
				$sort = 1;
			}
			if(!is_numeric($sort)){
				return '无效的排序';
			}

			$data['merchant_id'] = $merchant_id;
			$data['brand_name'] = $brand_name;
			$data['logo'] = $logo;
			$data['cate_id'] = $cate_id;
			$data['is_valid'] = (int)$is_valid ? 0 : 1;
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
				['type'=>'input','label'=>'品牌名称','prop'=>'brand_name','value'=>$id ? $data->brand_name : '','rules'=>['required'=>true,'message'=>'品牌名称不能为空']],
				['type'=>'upload','label'=>'品牌Logo','prop'=>'logo','value'=>$id ? $data->logo : '','uploadAttrs'=>[
					'type'=>'img','size'=>'small','limit'=>1,'action'=>$this->host['api'].'upload'
				]],
				['type'=>'radio-group','label'=>'品牌状态','prop'=>'cate_id','value'=>$id ? $data->cate_id : 0,'children'=>[
					['type'=>'option','label'=>'未注册','value'=>0],
					['type'=>'option','label'=>'TM标','value'=>1],
					['type'=>'option','label'=>'R标','value'=>2],
					['type'=>'option','label'=>'已失效','value'=>-1],
				]],
				['type'=>'switch','label'=>'失效','prop'=>'is_valid','value'=>$id ? ($data->is_valid?0:1) : 0],
				['type'=>'input','label'=>'排序','prop'=>'sort','value'=>$id ? $data->sort : $sort,'rules'=>['required'=>true,'message'=>'排序不能为空']],
			];

			return $action;
		}

		protected function getMapList(Request $request): array
		{
			$map = [
				['type'=>'id','label'=>'ID','prop'=>'id'],
				['type'=>'varchar','label'=>'品牌名称','prop'=>'brand_name'],
				['type'=>'img','label'=>'品牌Logo','prop'=>'logo'],
				['type'=>'varchar','label'=>'状态','prop'=>'cate_title'],
				['type'=>'switch','label'=>'有效','prop'=>'is_valid'],
				['type'=>'varchar','label'=>'排序','prop'=>'sort'],
				['type'=>'varchar','label'=>'创建时间','prop'=>'create_time'],
			];

			return $map;
		}
	}
?>