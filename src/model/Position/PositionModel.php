<?php
	namespace dackou\model\Position;

	use support\Request;

	class PositionModel extends \dackou\Model{
		protected $table = 'Position';
		protected $title = '职位';
		protected $page = false;
		protected $import_file = 'position.xlsx';
		protected $import_field = ['Title'];
	}
?>