<?php
	namespace dackou\model\Suggest;

	use support\Request;

	class SuggestModel extends \dackou\Model{
		protected $table = 'Suggest';
		protected $title = '投诉建议';
		protected $cate_value = [
			1 => '投诉',
			2 => '建议'
		];
	}
?>