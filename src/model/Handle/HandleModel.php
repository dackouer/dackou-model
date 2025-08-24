<?php
	namespace dackou\model\Handle;

	use support\Request;

	class HandleModel extends \dackou\Model{
		protected $table = 'Handle';
		protected $title = '行为';

		protected function getDefaultData(Request $request){
			$data = [
				['id'=>1,'title'=>'刷新','key'=>'IsRefresh','sort'=>1],
				['id'=>2,'title'=>'新增','key'=>'IsAdd','sort'=>2],
				['id'=>3,'title'=>'修改','key'=>'IsModify','sort'=>3],
				['id'=>4,'title'=>'查询','key'=>'IsSearch','sort'=>4],
				['id'=>5,'title'=>'保存','key'=>'IsSave','sort'=>5],
				['id'=>6,'title'=>'删除','key'=>'IsDel','sort'=>6],
				['id'=>7,'title'=>'导入','key'=>'IsImport','sort'=>7],
				['id'=>8,'title'=>'导出','key'=>'IsExport','sort'=>8],
				['id'=>9,'title'=>'打印','key'=>'IsPrint','sort'=>9],
				['id'=>10,'title'=>'审核','key'=>'IsChecked','sort'=>10],
				['id'=>11,'title'=>'核准','key'=>'IsApproved','sort'=>11],
				['id'=>12,'title'=>'拒绝','key'=>'IsReject','sort'=>12],
				['id'=>13,'title'=>'初始化','key'=>'IsInit','sort'=>13],
				['id'=>14,'title'=>'清空','key'=>'IsClear','sort'=>14],
				['id'=>15,'title'=>'返回','key'=>'IsBack','sort'=>15],
			];

			return $data;
		}
	}
?>