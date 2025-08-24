<?php
	namespace dackou\model\Contact;

	use support\Request;

	class ContactModel extends \dackou\Model{
		protected $table = 'Contact';
		protected $title = '联系我们';
		protected $page = true;

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