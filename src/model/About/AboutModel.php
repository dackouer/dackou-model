<?php
	namespace dackou\model\About;

	use support\Request;

	class AboutModel extends \dackou\Model{
		protected $table = 'About';
		protected $title = '关于我们';

		protected function getActionList(Request $request,$id = 0): array
		{
			if($id){
				$data = $this->getList($request,$id);
			}

			$action = [
				['type'=>'input','label'=>'标题','prop'=>'title','value'=>$data->title ?? '','rules'=>['required'=>true,'message'=>'标题不能为空']],
				['type'=>'input','label'=>'关键词','prop'=>'keyword','value'=>$data->keyword ?? '','attrs'=>['type'=>'textarea']],
				['type'=>'upload','label'=>'图片','prop'=>'pic','value'=>$data->pic ?? '','uploadAttrs'=>$this->getUploadOptions('img')],
                ['type'=>'editor','label'=>'内容','prop'=>'content','value'=>$data->content ?? '','editorOptions'=>$this->getEditorOptions('umo'),'rules'=>['required'=>true,'message'=>$this->title.'内容不能为空']],
			];

			return $action;
		}

		protected function getMapList(Request $request): array
		{
			return [
				['type'=>'id','label'=>'ID','prop'=>'id'],
				['type'=>'varchar','label'=>'标题','prop'=>'title'],
				['type'=>'img','label'=>'图片','prop'=>'pic'],
				['type'=>'varchar','label'=>'查看次数','prop'=>'hits'],
				['type'=>'varchar','label'=>'排序','prop'=>'sort'],
				['type'=>'varchar','label'=>'创建时间','prop'=>'create_time'],
			];
		}
	}
?>