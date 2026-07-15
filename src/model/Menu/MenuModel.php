<?php
	namespace dackou\model\Menu;

	use support\Request;
	use support\Db;

	class MenuModel extends \dackou\Model{
		protected $table = 'Menu';
		protected $title = '菜单';
    	protected $show_method = 'tree';
    	protected $truncate = true;
    	protected $action_exclude = ['ModuleID'];
		public $layer = 3;
		public $digit = 2;

		protected function getDefaultData(Request $request){
			$data = [
				['id'=>10,'title'=>'控制台','icon'=>'Setting','router'=>'','level'=>1,'pid'=>0,'number'=>4,'sort'=>1],
				['id'=>11,'title'=>'会员','icon'=>'User','router'=>'','level'=>1,'pid'=>0,'number'=>4,'sort'=>2],
				['id'=>12,'title'=>'文章','icon'=>'Mug','router'=>'','level'=>1,'pid'=>0,'number'=>4,'sort'=>3],
				['id'=>13,'title'=>'商品','icon'=>'Handbag','router'=>'','level'=>1,'pid'=>0,'number'=>6,'sort'=>4],
				['id'=>14,'title'=>'订单','icon'=>'Notification','router'=>'','level'=>1,'pid'=>0,'number'=>2,'sort'=>5],
				['id'=>15,'title'=>'小程序','icon'=>'Ship','router'=>'','level'=>1,'pid'=>0,'number'=>2,'sort'=>6],
				['id'=>16,'title'=>'网站','icon'=>'Eleme','router'=>'','level'=>1,'pid'=>0,'number'=>2,'sort'=>7],

				['id'=>1001,'title'=>'系统设置','icon'=>'Setting','router'=>'','level'=>2,'pid'=>10,'number'=>3,'sort'=>1],
				['id'=>1002,'title'=>'数据表管理','icon'=>'Notification','router'=>'','level'=>2,'pid'=>10,'number'=>3,'sort'=>2],
				['id'=>1003,'title'=>'基础管理','icon'=>'Football','router'=>'','level'=>2,'pid'=>10,'number'=>5,'sort'=>3],
				['id'=>1004,'title'=>'帮助中心','icon'=>'Odometer','router'=>'','level'=>2,'pid'=>10,'number'=>1,'sort'=>4],

				['id'=>1101,'title'=>'用户管理','icon'=>'User','router'=>'','level'=>2,'pid'=>11,'number'=>4,'sort'=>1],
				['id'=>1102,'title'=>'商户管理','icon'=>'SuitcaseLine','router'=>'','level'=>2,'pid'=>11,'number'=>1,'sort'=>2],
				['id'=>1103,'title'=>'日志管理','icon'=>'HotWater','router'=>'','level'=>2,'pid'=>11,'number'=>5,'sort'=>3],
				['id'=>1104,'title'=>'权限管理','icon'=>'Key','router'=>'','level'=>2,'pid'=>11,'number'=>2,'sort'=>4],

				['id'=>1201,'title'=>'新闻管理','icon'=>'Handbag','router'=>'','level'=>2,'pid'=>12,'number'=>2,'sort'=>1],
				['id'=>1202,'title'=>'文章管理','icon'=>'Brush','router'=>'','level'=>2,'pid'=>12,'number'=>2,'sort'=>2],
				['id'=>1203,'title'=>'活动管理','icon'=>'Bell','router'=>'','level'=>2,'pid'=>12,'number'=>1,'sort'=>3],
				['id'=>1204,'title'=>'公告管理','icon'=>'CreditCard','router'=>'','level'=>2,'pid'=>12,'number'=>1,'sort'=>4],

				['id'=>1301,'title'=>'商品设置','icon'=>'SetUp','router'=>'','level'=>2,'pid'=>13,'number'=>6,'sort'=>1],
				['id'=>1302,'title'=>'商品管理','icon'=>'Goods','router'=>'','level'=>2,'pid'=>13,'number'=>2,'sort'=>2],
				['id'=>1303,'title'=>'营销管理','icon'=>'Reading','router'=>'','level'=>2,'pid'=>13,'number'=>6,'sort'=>3],
				['id'=>1304,'title'=>'优惠券管理','icon'=>'Money','router'=>'','level'=>2,'pid'=>13,'number'=>2,'sort'=>4],

				['id'=>1401,'title'=>'订单设置','icon'=>'SetUp','router'=>'','level'=>2,'pid'=>14,'number'=>1,'sort'=>1],
				['id'=>1402,'title'=>'订单管理','icon'=>'ChatDotSquare','router'=>'','level'=>2,'pid'=>14,'number'=>2,'sort'=>2],

				['id'=>1501,'title'=>'小程序设置','icon'=>'Setting','router'=>'','level'=>2,'pid'=>15,'number'=>3,'sort'=>1],
				['id'=>1502,'title'=>'小程序管理','icon'=>'Notification','router'=>'','level'=>2,'pid'=>15,'number'=>3,'sort'=>2],

				['id'=>1601,'title'=>'网站设置','icon'=>'Setting','router'=>'','level'=>2,'pid'=>16,'number'=>3,'sort'=>1],
				['id'=>1602,'title'=>'网站管理','icon'=>'Notification','router'=>'','level'=>2,'pid'=>16,'number'=>3,'sort'=>2],

				['id'=>100101,'title'=>'系统设置','icon'=>'','router'=>'option/system','level'=>3,'pid'=>1001,'number'=>0,'sort'=>1],
				['id'=>100102,'title'=>'参数字段','icon'=>'','router'=>'option/field','level'=>3,'pid'=>1001,'number'=>0,'sort'=>2],
				['id'=>100103,'title'=>'菜单管理','icon'=>'','router'=>'menu','level'=>3,'pid'=>1001,'number'=>0,'sort'=>3],

				['id'=>100201,'title'=>'数据模型','icon'=>'','router'=>'table','level'=>3,'pid'=>1002,'number'=>0,'sort'=>1],
				['id'=>100202,'title'=>'数据字段','icon'=>'','router'=>'field','level'=>3,'pid'=>1002,'number'=>0,'sort'=>2],
				['id'=>100203,'title'=>'数据行为','icon'=>'','router'=>'handle','level'=>3,'pid'=>1002,'number'=>0,'sort'=>3],

				['id'=>100301,'title'=>'单位管理','icon'=>'','router'=>'unit','level'=>3,'pid'=>1003,'number'=>0,'sort'=>1],
				['id'=>100302,'title'=>'皮肤管理','icon'=>'','router'=>'skin','level'=>3,'pid'=>1003,'number'=>0,'sort'=>2],
				['id'=>100303,'title'=>'城市管理','icon'=>'','router'=>'city','level'=>3,'pid'=>1003,'number'=>0,'sort'=>3],
				['id'=>100304,'title'=>'快递公司','icon'=>'','router'=>'express','level'=>3,'pid'=>1003,'number'=>0,'sort'=>4],

				['id'=>100401,'title'=>'帮助中心','icon'=>'','router'=>'help','level'=>3,'pid'=>1004,'number'=>0,'sort'=>1],

				['id'=>110101,'title'=>'用户管理','icon'=>'','router'=>'user','level'=>3,'pid'=>1101,'number'=>0,'sort'=>1],
				['id'=>110102,'title'=>'管理员管理','icon'=>'','router'=>'user/admin','level'=>3,'pid'=>1101,'number'=>0,'sort'=>2],
				['id'=>110103,'title'=>'员工管理','icon'=>'','router'=>'user/worker','level'=>3,'pid'=>1101,'number'=>0,'sort'=>3],
				['id'=>110104,'title'=>'会员管理','icon'=>'','router'=>'user/member','level'=>3,'pid'=>1101,'number'=>0,'sort'=>4],

				['id'=>110201,'title'=>'商户管理','icon'=>'','router'=>'merchant','level'=>3,'pid'=>1102,'number'=>0,'sort'=>1],

				['id'=>110301,'title'=>'用户日志','icon'=>'','router'=>'log/user','level'=>3,'pid'=>1103,'number'=>0,'sort'=>1],
				['id'=>110302,'title'=>'登录日志','icon'=>'','router'=>'log/login','level'=>3,'pid'=>1103,'number'=>0,'sort'=>2],
				['id'=>110303,'title'=>'注册日志','icon'=>'','router'=>'log/reg','level'=>3,'pid'=>1103,'number'=>0,'sort'=>3],
				['id'=>110304,'title'=>'余额日志','icon'=>'','router'=>'log/balance','level'=>3,'pid'=>1103,'number'=>0,'sort'=>4],
				['id'=>110305,'title'=>'积分日志','icon'=>'','router'=>'log/score','level'=>3,'pid'=>1103,'number'=>0,'sort'=>5],
				['id'=>110306,'title'=>'短信日志','icon'=>'','router'=>'log/sms','level'=>3,'pid'=>1103,'number'=>0,'sort'=>6],

				['id'=>110401,'title'=>'角色管理','icon'=>'','router'=>'role','level'=>3,'pid'=>1104,'number'=>0,'sort'=>1],
				['id'=>110402,'title'=>'会员管理','icon'=>'','router'=>'vip','level'=>3,'pid'=>1104,'number'=>0,'sort'=>2],
				['id'=>110403,'title'=>'权限控制','icon'=>'','router'=>'grant','level'=>3,'pid'=>1104,'number'=>0,'sort'=>3],

				['id'=>120101,'title'=>'新闻管理','icon'=>'','router'=>'news','level'=>3,'pid'=>1201,'number'=>0,'sort'=>1],
				['id'=>120102,'title'=>'新闻类别','icon'=>'','router'=>'news/cate','level'=>3,'pid'=>1201,'number'=>0,'sort'=>2],

				['id'=>120201,'title'=>'文章管理','icon'=>'','router'=>'article','level'=>3,'pid'=>1202,'number'=>0,'sort'=>1],
				['id'=>120202,'title'=>'文章类别','icon'=>'','router'=>'article/cate','level'=>3,'pid'=>1202,'number'=>0,'sort'=>2],

				['id'=>120301,'title'=>'活动管理','icon'=>'','router'=>'activity','level'=>3,'pid'=>1203,'number'=>0,'sort'=>1],
				['id'=>120302,'title'=>'活动类别','icon'=>'','router'=>'activity/cate','level'=>3,'pid'=>1203,'number'=>0,'sort'=>2],

				['id'=>120401,'title'=>'公告管理','icon'=>'','router'=>'notice','level'=>3,'pid'=>1204,'number'=>0,'sort'=>1],

				['id'=>130101,'title'=>'商品设置','icon'=>'','router'=>'option/goods','level'=>3,'pid'=>1301,'number'=>0,'sort'=>1],
				['id'=>130102,'title'=>'商品属性','icon'=>'','router'=>'goods/attrs','level'=>3,'pid'=>1301,'number'=>0,'sort'=>2],
				['id'=>130103,'title'=>'商品规格','icon'=>'','router'=>'goods/spec','level'=>3,'pid'=>1301,'number'=>0,'sort'=>3],
				['id'=>130104,'title'=>'商品标签','icon'=>'','router'=>'goods/tags','level'=>3,'pid'=>1301,'number'=>0,'sort'=>4],
				['id'=>130105,'title'=>'商品品牌','icon'=>'','router'=>'goods/tags','level'=>3,'pid'=>1301,'number'=>0,'sort'=>5],
				['id'=>130106,'title'=>'商品类别','icon'=>'','router'=>'goods/cate','level'=>3,'pid'=>1301,'number'=>0,'sort'=>6],

				['id'=>130201,'title'=>'商品管理','icon'=>'','router'=>'goods','level'=>3,'pid'=>1302,'number'=>0,'sort'=>1],

				['id'=>130301,'title'=>'时段管理','icon'=>'','router'=>'market/period','level'=>3,'pid'=>1303,'number'=>0,'sort'=>1],
				['id'=>130302,'title'=>'专区管理','icon'=>'','router'=>'market/prefecture','level'=>3,'pid'=>1303,'number'=>0,'sort'=>2],

				['id'=>130401,'title'=>'优惠券管理','icon'=>'','router'=>'coupon','level'=>3,'pid'=>1304,'number'=>0,'sort'=>1],
				['id'=>130402,'title'=>'优惠券记录','icon'=>'','router'=>'record/coupon','level'=>3,'pid'=>1304,'number'=>0,'sort'=>2],

				['id'=>140101,'title'=>'订单设置','icon'=>'','router'=>'option/order','level'=>3,'pid'=>1401,'number'=>0,'sort'=>1],

				['id'=>140201,'title'=>'订单管理','icon'=>'','router'=>'order','level'=>3,'pid'=>1402,'number'=>0,'sort'=>1],
				['id'=>140202,'title'=>'待确认订单','icon'=>'','router'=>'order/confirm','level'=>3,'pid'=>1402,'number'=>0,'sort'=>2],
				['id'=>140203,'title'=>'待支付订单','icon'=>'','router'=>'order/pay','level'=>3,'pid'=>1402,'number'=>0,'sort'=>3],
				['id'=>140204,'title'=>'待发货订单','icon'=>'','router'=>'order/senout','level'=>3,'pid'=>1402,'number'=>0,'sort'=>4],
				['id'=>140205,'title'=>'待完成订单','icon'=>'','router'=>'order/finish','level'=>3,'pid'=>1402,'number'=>0,'sort'=>5],
				['id'=>140206,'title'=>'已取消订单','icon'=>'','router'=>'order/cancel','level'=>3,'pid'=>1402,'number'=>0,'sort'=>6],
				['id'=>140207,'title'=>'已退款订单','icon'=>'','router'=>'order/refund','level'=>3,'pid'=>1402,'number'=>0,'sort'=>7],

				['id'=>150101,'title'=>'小程序设置','icon'=>'','router'=>'option/mini','level'=>3,'pid'=>1501,'number'=>0,'sort'=>1],
				['id'=>150102,'title'=>'小程序页面','icon'=>'','router'=>'mini/page','level'=>3,'pid'=>1501,'number'=>0,'sort'=>2],
				['id'=>150103,'title'=>'页面设置','icon'=>'','router'=>'mini/config','level'=>3,'pid'=>1501,'number'=>0,'sort'=>3],

				['id'=>150201,'title'=>'底部导航栏','icon'=>'','router'=>'mini/tabbar','level'=>3,'pid'=>1502,'number'=>0,'sort'=>1],
				['id'=>150202,'title'=>'轮播图管理','icon'=>'','router'=>'mini/carousel','level'=>3,'pid'=>1502,'number'=>0,'sort'=>2],
				['id'=>150203,'title'=>'列表组件','icon'=>'','router'=>'mini/tabulation','level'=>3,'pid'=>1502,'number'=>0,'sort'=>3],

				['id'=>160101,'title'=>'网站设置','icon'=>'','router'=>'option/web','level'=>3,'pid'=>1601,'number'=>0,'sort'=>1],
				['id'=>160102,'title'=>'网站页面','icon'=>'','router'=>'web/page','level'=>3,'pid'=>1601,'number'=>0,'sort'=>2],
				['id'=>160103,'title'=>'页面设置','icon'=>'','router'=>'web/config','level'=>3,'pid'=>1601,'number'=>0,'sort'=>3],

				['id'=>160201,'title'=>'导航栏管理','icon'=>'','router'=>'web/tabbar','level'=>3,'pid'=>1602,'number'=>0,'sort'=>1],
				['id'=>160202,'title'=>'轮播图管理','icon'=>'','router'=>'web/carousel','level'=>3,'pid'=>1602,'number'=>0,'sort'=>2],
				['id'=>160203,'title'=>'列表组件','icon'=>'','router'=>'web/tabulation','level'=>3,'pid'=>1602,'number'=>0,'sort'=>3],
			];

			return $data;
		}

		protected function getGrantList(Request $request){
			try{
				$role_id = $this->getTokenData($request,'rid');
				// var_dump('role_id: '.$role_id);
				if(!$role_id || (int)($role_id/100) == 11){
					// return 100009;
				}

				$field = $this->getList($request,'field',true);
				$where = [[$this->table.'.IsDel','=',0],['grant.RoleID','=',$role_id],['grant.IsShow','=',1]];
				$object = Db::table($this->table)
							->join('grant','grant.MenuID','=','menu.ID')
							->select(...$field)
							->where($where)
							->orderBy('Level','asc')
							->orderBy('Sort','asc')
							->get();
				if($object){
					return $this->child($object);
				}
				// var_dump($object);
				return [];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		protected function getAccountList(Request $request){
			$data = [
				['id'=>10,'title'=>'系统设置','icon'=>'Setting','pid'=>0,'level'=>1,'number'=>2,'router'=>'','children'=>[
					['id'=>1001,'title'=>'系统设置','icon'=>'','pid'=>10,'level'=>2,'number'=>0,'router'=>'option/system'],
					['id'=>1002,'title'=>'活动设置','icon'=>'','pid'=>10,'level'=>2,'number'=>0,'router'=>'option/activity'],
					['id'=>1003,'title'=>'活动类别','icon'=>'','pid'=>10,'level'=>2,'number'=>0,'router'=>'activity/cate'],
				]],
				['id'=>11,'title'=>'会员管理','icon'=>'User','pid'=>0,'level'=>1,'number'=>2,'router'=>'','children'=>[
					['id'=>1101,'title'=>'员工管理','icon'=>'','pid'=>11,'level'=>2,'number'=>0,'router'=>'user/worker'],
					['id'=>1102,'title'=>'会员管理','icon'=>'','pid'=>11,'level'=>2,'number'=>0,'router'=>'user/member']
				]],
				['id'=>12,'title'=>'活动管理','icon'=>'DataBoard','pid'=>0,'level'=>1,'number'=>1,'router'=>'','children'=>[
					['id'=>1201,'title'=>'活动管理','icon'=>'','pid'=>12,'level'=>2,'number'=>0,'router'=>'activity'],
				]],
			];

			return $data;

			$data = $this->getList($request,'grant');
			if(!$data || (is_array($data) && isset($data['code']))){
				return $data;
			}

			if($this->layer < 3){
				return $data;
			}

			$menu = [];
			foreach($data as $val){
				if($val->level === 2){
					array_push($menu, $val);
				}
			}

			var_dump('account menu: ',$menu);

			return $menu;
		}

		protected function validate(Request $request,$id = 0,$obj = null){
			$level = $request->post('level',1);
			$pid = $request->post('pid',0);
			if($level > 1 && !$pid){
				return '请选择父级';
			}
			$title = trim($request->post('title',''));
			if(!$title){
				return '菜单名称不能为空';
			}
			if($this->checkExists(['Title'=>$title,'PID'=>$pid],$id)){
				return '同级菜单名称已存在';
			}
			$icon = trim($request->post('icon',''));
			$router = trim($request->post('router',''));
			if($level == $this->layer && !$router){
				return '链接路由不能为空';
			}
			$sort = $request->post('sort',1);
			if(!is_numeric($sort) || $sort <= 0){
				return '排序只能填数字且大于0';
			}

			$data['title'] = $title;
			$data['level'] = $level;
			$data['pid'] = $pid;
			$data['icon'] = $icon;
			$data['router'] = $router;
			$data['sort'] = $sort;

			return $data;
		}

		// protected function getMapList(Request $request): array
		// {
		// 	return [
		// 		['type'=>'varchar','label'=>'ID','prop'=>'id','align'=>'left'],
		// 		['type'=>'varchar','label'=>'菜单名称','prop'=>'title','align'=>'left'],
		// 		['type'=>'icon','label'=>'图标','prop'=>'icon'],
		// 		['type'=>'varchar','label'=>'模块','prop'=>'module_id'],
		// 		['type'=>'varchar','label'=>'链接','prop'=>'router'],
		// 		['type'=>'varchar','label'=>'排序','prop'=>'sort'],
		// 	];
		// }
	}
?>