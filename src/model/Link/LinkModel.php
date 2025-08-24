<?php
	namespace dackou\model\Link;

	use support\Request;

	class LinkModel extends \dackou\Model{
		protected $table = 'Link';
		protected $title = '友情链接';

		protected function getMapList(Request $request): array
		{
			return [
				['type'=>'varchar','label'=>'ID','prop'=>'id'],
				['type'=>'varchar','label'=>'链接名称','prop'=>'title'],
				['type'=>'img','label'=>'图片','prop'=>'pic'],
				['type'=>'link','label'=>'链接地址','prop'=>'url'],
				['type'=>'varchar','label'=>'查看次数','prop'=>'hits'],
				['type'=>'varchar','label'=>'排序','prop'=>'sort'],
				['type'=>'varchar','label'=>'创建时间','prop'=>'create_time'],
			];
		}
	}
?>