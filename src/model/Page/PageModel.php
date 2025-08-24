<?php
	namespace dackou\model\Page;

	use support\Request;
	use support\Db;

	class PageModel extends \dackou\Model{
		protected $table = 'Page';
		protected $title = '页面';
		public $layer = 2;
		public $digit = 2;
    	protected $show_method = 'tree';
		protected $cate_value = [0=>'其它',1=>'mini',2=>'app',3=>'web'];

		protected function getDefaultData(Request $request){
			$data = [
				['ID'=>1,'CateID'=>0,'Title'=>'无','Url'=>'','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>1,'PID'=>0,'Number'=>0,'Sort'=>0],
				['ID'=>10,'CateID'=>1,'Title'=>'首页','Url'=>'','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>1,'PID'=>0,'Number'=>2,'Sort'=>1],
				['ID'=>11,'CateID'=>1,'Title'=>'设置','Url'=>'','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>1,'PID'=>0,'Number'=>8,'Sort'=>2],
				['ID'=>12,'CateID'=>1,'Title'=>'用户','Url'=>'','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>1,'PID'=>0,'Number'=>5,'Sort'=>3],
				['ID'=>13,'CateID'=>1,'Title'=>'实名','Url'=>'','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>1,'PID'=>0,'Number'=>3,'Sort'=>4],
				['ID'=>14,'CateID'=>1,'Title'=>'商品','Url'=>'','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>1,'PID'=>0,'Number'=>5,'Sort'=>5],
				['ID'=>15,'CateID'=>1,'Title'=>'营销','Url'=>'','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>1,'PID'=>0,'Number'=>7,'Sort'=>6],
				['ID'=>16,'CateID'=>1,'Title'=>'订单','Url'=>'','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>1,'PID'=>0,'Number'=>5,'Sort'=>7],
				['ID'=>17,'CateID'=>1,'Title'=>'新闻','Url'=>'','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>1,'PID'=>0,'Number'=>5,'Sort'=>8],
				['ID'=>18,'CateID'=>1,'Title'=>'文章','Url'=>'','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>1,'PID'=>0,'Number'=>5,'Sort'=>9],
				['ID'=>19,'CateID'=>1,'Title'=>'活动','Url'=>'','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>1,'PID'=>0,'Number'=>5,'Sort'=>10],
				['ID'=>20,'CateID'=>1,'Title'=>'投票','Url'=>'','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>1,'PID'=>0,'Number'=>5,'Sort'=>11],
				['ID'=>21,'CateID'=>1,'Title'=>'通知','Url'=>'','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>1,'PID'=>0,'Number'=>2,'Sort'=>12],
				['ID'=>22,'CateID'=>1,'Title'=>'消息','Url'=>'','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>1,'PID'=>0,'Number'=>2,'Sort'=>13],
				['ID'=>23,'CateID'=>1,'Title'=>'海报','Url'=>'','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>1,'PID'=>0,'Number'=>2,'Sort'=>14],
				['ID'=>24,'CateID'=>1,'Title'=>'抽奖','Url'=>'','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>1,'PID'=>0,'Number'=>3,'Sort'=>15],
				['ID'=>25,'CateID'=>1,'Title'=>'记录','Url'=>'','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>1,'PID'=>0,'Number'=>7,'Sort'=>16],
				['ID'=>26,'CateID'=>1,'Title'=>'直播','Url'=>'','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>1,'PID'=>0,'Number'=>5,'Sort'=>17],
				['ID'=>27,'CateID'=>1,'Title'=>'音乐','Url'=>'','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>1,'PID'=>0,'Number'=>2,'Sort'=>18],
				['ID'=>28,'CateID'=>1,'Title'=>'视频','Url'=>'','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>1,'PID'=>0,'Number'=>2,'Sort'=>19],

				['ID'=>1001,'CateID'=>1,'Title'=>'启动页','Url'=>'/pages/index/lauch','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>2,'PID'=>10,'Number'=>0,'Sort'=>1],
				['ID'=>1002,'CateID'=>1,'Title'=>'首页','Url'=>'/pages/index/index','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>2,'PID'=>10,'Number'=>0,'Sort'=>2],

				['ID'=>1101,'CateID'=>1,'Title'=>'设置首页','Url'=>'/pages/setting/index','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>2,'PID'=>11,'Number'=>0,'Sort'=>1],
				['ID'=>1102,'CateID'=>1,'Title'=>'用户设置','Url'=>'/pages/setting/user','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>2,'PID'=>11,'Number'=>0,'Sort'=>2],
				['ID'=>1103,'CateID'=>1,'Title'=>'关于我们','Url'=>'/pages/setting/about','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>2,'PID'=>11,'Number'=>0,'Sort'=>3],
				['ID'=>1104,'CateID'=>1,'Title'=>'帮助中心','Url'=>'/pages/setting/about','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>2,'PID'=>11,'Number'=>0,'Sort'=>4],
				['ID'=>1105,'CateID'=>1,'Title'=>'隐私协议','Url'=>'/pages/setting/about','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>2,'PID'=>11,'Number'=>0,'Sort'=>5],
				['ID'=>1106,'CateID'=>1,'Title'=>'用户协议','Url'=>'/pages/setting/about','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>2,'PID'=>11,'Number'=>0,'Sort'=>6],
				['ID'=>1107,'CateID'=>1,'Title'=>'客户服务','Url'=>'/pages/setting/about','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>2,'PID'=>11,'Number'=>0,'Sort'=>7],
				['ID'=>1108,'CateID'=>1,'Title'=>'系统版本','Url'=>'/pages/setting/about','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>2,'PID'=>11,'Number'=>0,'Sort'=>8],

				['ID'=>1201,'CateID'=>1,'Title'=>'用户中心','Url'=>'/pages/user/index','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>2,'PID'=>12,'Number'=>0,'Sort'=>1],
				['ID'=>1202,'CateID'=>1,'Title'=>'用户注册','Url'=>'/pages/user/reg','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>2,'PID'=>12,'Number'=>0,'Sort'=>2],
				['ID'=>1203,'CateID'=>1,'Title'=>'用户登录','Url'=>'/pages/user/login','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>2,'PID'=>12,'Number'=>0,'Sort'=>3],
				['ID'=>1204,'CateID'=>1,'Title'=>'收货地址','Url'=>'/pages/user/address','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>2,'PID'=>12,'Number'=>0,'Sort'=>4],
				['ID'=>1205,'CateID'=>1,'Title'=>'用户银行卡','Url'=>'/pages/user/bank','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>2,'PID'=>12,'Number'=>0,'Sort'=>5],

				['ID'=>1301,'CateID'=>1,'Title'=>'实名首页','Url'=>'/pages/auth/index','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>2,'PID'=>13,'Number'=>0,'Sort'=>1],
				['ID'=>1302,'CateID'=>1,'Title'=>'个人实名','Url'=>'/pages/auth/person','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>2,'PID'=>13,'Number'=>0,'Sort'=>2],
				['ID'=>1303,'CateID'=>1,'Title'=>'企业实名','Url'=>'/pages/auth/co','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>2,'PID'=>13,'Number'=>0,'Sort'=>3],

				['ID'=>1401,'CateID'=>1,'Title'=>'商品首页','Url'=>'/pages/goods/index','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>2,'PID'=>14,'Number'=>0,'Sort'=>1],
				['ID'=>1402,'CateID'=>1,'Title'=>'商品分类页','Url'=>'/pages/goods/cate','Param'=>'cate_id','Description'=>'商品分类ID','IsParam'=>1,'Level'=>2,'PID'=>14,'Number'=>0,'Sort'=>2],
				['ID'=>1403,'CateID'=>1,'Title'=>'商品搜索页','Url'=>'/pages/goods/search','Param'=>'keyword','Description'=>'关键词','IsParam'=>1,'Level'=>2,'PID'=>14,'Number'=>0,'Sort'=>3],
				['ID'=>1404,'CateID'=>1,'Title'=>'商品管理页','Url'=>'/pages/goods/manage','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>2,'PID'=>14,'Number'=>0,'Sort'=>4],
				['ID'=>1405,'CateID'=>1,'Title'=>'商品详情页','Url'=>'/pages/goods/detail','Param'=>'id','Description'=>'商品ID','IsParam'=>1,'Level'=>2,'PID'=>14,'Number'=>0,'Sort'=>5],

				['ID'=>1501,'CateID'=>1,'Title'=>'营销首页','Url'=>'/pages/market/index','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>2,'PID'=>15,'Number'=>0,'Sort'=>1],
				['ID'=>1502,'CateID'=>1,'Title'=>'拼团首页','Url'=>'/pages/market/index','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>2,'PID'=>15,'Number'=>0,'Sort'=>2],
				['ID'=>1503,'CateID'=>1,'Title'=>'秒杀首页','Url'=>'/pages/market/index','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>2,'PID'=>15,'Number'=>0,'Sort'=>3],
				['ID'=>1504,'CateID'=>1,'Title'=>'砍价首页','Url'=>'/pages/market/index','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>2,'PID'=>15,'Number'=>0,'Sort'=>4],
				['ID'=>1505,'CateID'=>1,'Title'=>'拍卖首页','Url'=>'/pages/market/index','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>2,'PID'=>15,'Number'=>0,'Sort'=>5],
				['ID'=>1506,'CateID'=>1,'Title'=>'众筹首页','Url'=>'/pages/market/index','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>2,'PID'=>15,'Number'=>0,'Sort'=>6],
				['ID'=>1507,'CateID'=>1,'Title'=>'专区首页','Url'=>'/pages/market/index','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>2,'PID'=>15,'Number'=>0,'Sort'=>7],

				['ID'=>1601,'CateID'=>1,'Title'=>'订单首页','Url'=>'/pages/order/index','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>2,'PID'=>16,'Number'=>0,'Sort'=>1],
				['ID'=>1602,'CateID'=>1,'Title'=>'订单管理页','Url'=>'/pages/order/manage','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>2,'PID'=>16,'Number'=>0,'Sort'=>2],
				['ID'=>1603,'CateID'=>1,'Title'=>'购物车','Url'=>'/pages/order/manage','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>2,'PID'=>16,'Number'=>0,'Sort'=>3],
				['ID'=>1604,'CateID'=>1,'Title'=>'订单详情页','Url'=>'/pages/order/detail','Param'=>'order','Description'=>'订单编号','IsParam'=>1,'Level'=>2,'PID'=>16,'Number'=>0,'Sort'=>4],
				['ID'=>1605,'CateID'=>1,'Title'=>'订单支付页','Url'=>'/pages/order/pay','Param'=>'order','Description'=>'订单编号','IsParam'=>1,'Level'=>2,'PID'=>16,'Number'=>0,'Sort'=>5],

				['ID'=>1701,'CateID'=>1,'Title'=>'新闻首页','Url'=>'/pages/news/index','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>2,'PID'=>17,'Number'=>0,'Sort'=>1],
				['ID'=>1702,'CateID'=>1,'Title'=>'新闻搜索页','Url'=>'/pages/news/search','Param'=>'keyword','Description'=>'关键词','IsParam'=>1,'Level'=>2,'PID'=>17,'Number'=>0,'Sort'=>2],
				['ID'=>1703,'CateID'=>1,'Title'=>'新闻分类页','Url'=>'/pages/news/cate','Param'=>'cate_id','Description'=>'新闻分类ID','IsParam'=>1,'Level'=>2,'PID'=>17,'Number'=>0,'Sort'=>3],
				['ID'=>1704,'CateID'=>1,'Title'=>'新闻管理页','Url'=>'/pages/news/manage','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>2,'PID'=>17,'Number'=>0,'Sort'=>4],
				['ID'=>1705,'CateID'=>1,'Title'=>'新闻详情页','Url'=>'/pages/news/detail','Param'=>'id','Description'=>'新闻ID','IsParam'=>1,'Level'=>2,'PID'=>17,'Number'=>0,'Sort'=>5],

				['ID'=>1801,'CateID'=>1,'Title'=>'文章首页','Url'=>'/pages/article/index','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>2,'PID'=>18,'Number'=>0,'Sort'=>1],
				['ID'=>1802,'CateID'=>1,'Title'=>'文章搜索页','Url'=>'/pages/article/search','Param'=>'keyword','Description'=>'关键词','IsParam'=>1,'Level'=>2,'PID'=>18,'Number'=>0,'Sort'=>2],
				['ID'=>1803,'CateID'=>1,'Title'=>'文章分类页','Url'=>'/pages/article/cate','Param'=>'cate_id','Description'=>'文章分类ID','IsParam'=>1,'Level'=>2,'PID'=>18,'Number'=>0,'Sort'=>3],
				['ID'=>1804,'CateID'=>1,'Title'=>'文章管理页','Url'=>'/pages/article/manage','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>2,'PID'=>18,'Number'=>0,'Sort'=>4],
				['ID'=>1805,'CateID'=>1,'Title'=>'文章详情页','Url'=>'/pages/article/detail','Param'=>'id','Description'=>'文章ID','IsParam'=>1,'Level'=>2,'PID'=>18,'Number'=>0,'Sort'=>5],

				['ID'=>1901,'CateID'=>1,'Title'=>'活动首页','Url'=>'/pages/activity/index','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>2,'PID'=>19,'Number'=>0,'Sort'=>1],
				['ID'=>1902,'CateID'=>1,'Title'=>'活动搜索页','Url'=>'/pages/activity/search','Param'=>'keyword','Description'=>'关键词','IsParam'=>1,'Level'=>2,'PID'=>19,'Number'=>0,'Sort'=>2],
				['ID'=>1903,'CateID'=>1,'Title'=>'活动分类页','Url'=>'/pages/activity/cate','Param'=>'cate_id','Description'=>'活动分类ID','IsParam'=>1,'Level'=>2,'PID'=>19,'Number'=>0,'Sort'=>3],
				['ID'=>1904,'CateID'=>1,'Title'=>'活动管理页','Url'=>'/pages/activity/manage','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>2,'PID'=>19,'Number'=>0,'Sort'=>4],
				['ID'=>1905,'CateID'=>1,'Title'=>'活动详情页','Url'=>'/pages/activity/detail','Param'=>'id','Description'=>'活动ID','IsParam'=>1,'Level'=>2,'PID'=>19,'Number'=>0,'Sort'=>5],

				['ID'=>2001,'CateID'=>1,'Title'=>'投票首页','Url'=>'/pages/vote/index','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>2,'PID'=>20,'Number'=>0,'Sort'=>1],
				['ID'=>2002,'CateID'=>1,'Title'=>'投票搜索页','Url'=>'/pages/vote/search','Param'=>'keyword','Description'=>'关键词','IsParam'=>1,'Level'=>2,'PID'=>20,'Number'=>0,'Sort'=>2],
				['ID'=>2003,'CateID'=>1,'Title'=>'投票分类页','Url'=>'/pages/vote/cate','Param'=>'cate_id','Description'=>'投票分类ID','IsParam'=>1,'Level'=>2,'PID'=>20,'Number'=>0,'Sort'=>3],
				['ID'=>2004,'CateID'=>1,'Title'=>'投票管理页','Url'=>'/pages/vote/manage','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>2,'PID'=>20,'Number'=>0,'Sort'=>4],
				['ID'=>2005,'CateID'=>1,'Title'=>'投票详情页','Url'=>'/pages/vote/detail','Param'=>'id','Description'=>'投票ID','IsParam'=>1,'Level'=>2,'PID'=>20,'Number'=>0,'Sort'=>5],

				['ID'=>2101,'CateID'=>1,'Title'=>'通知首页','Url'=>'/pages/notice/index','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>2,'PID'=>21,'Number'=>0,'Sort'=>1],
				['ID'=>2102,'CateID'=>1,'Title'=>'通知详情页','Url'=>'/pages/notice/detail','Param'=>'id','Description'=>'通知ID','IsParam'=>1,'Level'=>2,'PID'=>21,'Number'=>0,'Sort'=>2],

				['ID'=>2201,'CateID'=>1,'Title'=>'消息首页','Url'=>'/pages/message/index','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>2,'PID'=>22,'Number'=>0,'Sort'=>1],
				['ID'=>2202,'CateID'=>1,'Title'=>'消息详情页','Url'=>'/pages/message/detail','Param'=>'id','Description'=>'消息ID','IsParam'=>1,'Level'=>2,'PID'=>22,'Number'=>0,'Sort'=>2],

				['ID'=>2301,'CateID'=>1,'Title'=>'海报首页','Url'=>'/pages/poster/index','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>2,'PID'=>23,'Number'=>0,'Sort'=>1],
				['ID'=>2302,'CateID'=>1,'Title'=>'海报详情页','Url'=>'/pages/poster/detail','Param'=>'id','Description'=>'海报ID','IsParam'=>1,'Level'=>2,'PID'=>23,'Number'=>0,'Sort'=>2],

				['ID'=>2401,'CateID'=>1,'Title'=>'抽奖首页','Url'=>'/pages/draw/index','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>2,'PID'=>24,'Number'=>0,'Sort'=>1],
				['ID'=>2402,'CateID'=>1,'Title'=>'抽奖详情页','Url'=>'/pages/draw/detail','Param'=>'id','Description'=>'抽奖ID','IsParam'=>1,'Level'=>2,'PID'=>24,'Number'=>0,'Sort'=>2],
				['ID'=>2403,'CateID'=>1,'Title'=>'中奖列表页','Url'=>'/pages/draw/result','Param'=>'id','Description'=>'抽奖ID','IsParam'=>1,'Level'=>2,'PID'=>24,'Number'=>0,'Sort'=>3],

				['ID'=>2501,'CateID'=>1,'Title'=>'记录首页','Url'=>'/pages/record/index','Param'=>'user_id','Description'=>'账号ID','IsParam'=>0,'Level'=>2,'PID'=>25,'Number'=>0,'Sort'=>1],
				['ID'=>2502,'CateID'=>1,'Title'=>'我的余额','Url'=>'/pages/record/balance','Param'=>'user_id','Description'=>'账号ID','IsParam'=>0,'Level'=>2,'PID'=>25,'Number'=>0,'Sort'=>2],
				['ID'=>2503,'CateID'=>1,'Title'=>'我的积分','Url'=>'/pages/record/score','Param'=>'user_id','Description'=>'账号ID','IsParam'=>0,'Level'=>2,'PID'=>25,'Number'=>0,'Sort'=>3],
				['ID'=>2504,'CateID'=>1,'Title'=>'我的收藏','Url'=>'/pages/record/collect','Param'=>'user_id','Description'=>'账号ID','IsParam'=>0,'Level'=>2,'PID'=>25,'Number'=>0,'Sort'=>4],
				['ID'=>2505,'CateID'=>1,'Title'=>'我的足迹','Url'=>'/pages/record/track','Param'=>'user_id','Description'=>'账号ID','IsParam'=>0,'Level'=>2,'PID'=>25,'Number'=>0,'Sort'=>5],
				['ID'=>2506,'CateID'=>1,'Title'=>'我的优惠券','Url'=>'/pages/record/coupon','Param'=>'user_id','Description'=>'账号ID','IsParam'=>0,'Level'=>2,'PID'=>25,'Number'=>0,'Sort'=>6],
				['ID'=>2507,'CateID'=>1,'Title'=>'我的红包','Url'=>'/pages/record/envelope','Param'=>'user_id','Description'=>'账号ID','IsParam'=>0,'Level'=>2,'PID'=>25,'Number'=>0,'Sort'=>7],

				['ID'=>2601,'CateID'=>1,'Title'=>'直播首页','Url'=>'/pages/live/index','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>2,'PID'=>26,'Number'=>0,'Sort'=>1],
				['ID'=>2602,'CateID'=>1,'Title'=>'直播房间页','Url'=>'/pages/live/detail','Param'=>'id','Description'=>'直播ID','IsParam'=>1,'Level'=>2,'PID'=>26,'Number'=>0,'Sort'=>2],
				['ID'=>2603,'CateID'=>1,'Title'=>'粉丝记录页','Url'=>'/pages/live/fans','Param'=>'id','Description'=>'直播ID','IsParam'=>1,'Level'=>2,'PID'=>26,'Number'=>0,'Sort'=>3],
				['ID'=>2604,'CateID'=>1,'Title'=>'直播奖品页','Url'=>'/pages/live/award','Param'=>'id','Description'=>'直播ID','IsParam'=>1,'Level'=>2,'PID'=>26,'Number'=>0,'Sort'=>4],
				['ID'=>2605,'CateID'=>1,'Title'=>'打赏明细页','Url'=>'/pages/live/reward','Param'=>'id','Description'=>'直播ID','IsParam'=>1,'Level'=>2,'PID'=>26,'Number'=>0,'Sort'=>5],

				['ID'=>2701,'CateID'=>1,'Title'=>'音乐首页','Url'=>'/pages/music/index','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>2,'PID'=>27,'Number'=>0,'Sort'=>1],
				['ID'=>2702,'CateID'=>1,'Title'=>'音乐播放页','Url'=>'/pages/music/play','Param'=>'id','Description'=>'音乐ID','IsParam'=>1,'Level'=>2,'PID'=>27,'Number'=>0,'Sort'=>2],

				['ID'=>2801,'CateID'=>1,'Title'=>'视频首页','Url'=>'/pages/video/index','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>2,'PID'=>28,'Number'=>0,'Sort'=>1],
				['ID'=>2802,'CateID'=>1,'Title'=>'视频播放页','Url'=>'/pages/video/play','Param'=>'','Description'=>'','IsParam'=>0,'Level'=>2,'PID'=>28,'Number'=>0,'Sort'=>2],


			];

			return $data;
		}

		protected function getPageList(Request $request){
			try{
				$field = $this->getList($request,'field');
				$where = $this->getWhere($request);
				array_push($where,[$this->table.'.ID','>',1]);

				$cate_id = $request->input('cate_id',1);
				if(in_array($cate_id,[1,2,3])){
					array_push($where,[$this->table.'.CateID','=',$cate_id]);
				}

				$rows = Db::table($this->table)
							->where($where)
							->count();
				[$offset,$limit] = $this->getLimit($request);
				$object = Db::table($this->table)
							->select(...$field)
							->where($where)
							->orderBy($this->table.'.Level','asc')
							->orderBy($this->table.'.Sort','asc')
							->offset($offset)
							->limit($limit)
							->get();

				return ['rows' => $rows,'data' => $object];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		protected function getTreeList(Request $request): array
		{
			try{
				$field = $this->getList($request,'field');
				$where = $this->getWhere($request);
				array_push($where,[$this->table.'.ID','>',1]);
				$object = Db::table($this->table)
							->select(...$field)
							->where($where)
							->orderBy($this->table.'.Level','asc')
							->orderBy($this->table.'.Sort','asc')
							->get();

				return $this->tree($object);
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		protected function validate(Request $request,$id = 0,$obj = null){
			$cate_id = $request->post('cate_id',1);
			if(!in_array($cate_id,[1,2,3])){
				return '无效的类别';
			}
			$level = $request->post('level',1);
			$pid = $request->post('pid',0);
			if($level > 1 && !$pid){
				return '请选择父级';
			}
			$title = trim($request->post('title',''));
			$url = trim($request->post('url',''));
			$param = trim($request->post('param',''));
			$desc = trim($request->post('desc',''));
			$is_param = $request->post('is_param',0);

			if(!$title){
				return '标题名称不能为空';
			}
			if($this->checkExists(['PID'=>$pid,'Title'=>$title],$id)){
				return '同级标题名称已存在,请重新输入';
			}

			$data['cate_id'] = $cate_id;
			$data['level'] = $level;
			$data['pid'] = $pid;
			$data['title'] = $title;
			$data['url'] = $url;
			$data['param'] = $param;
			$data['description'] = $desc;
			$data['is_param'] = $is_param;

			return $data;
		}

		protected function getActionList(Request $request,$id = 0): array
		{
			if($id){
				$data = $this->getList($request,$id);
			}

			$cate_id = $request->input('cate_id',1);
			$cate = [];
			foreach($this->cate_value as $k => $v){
				if($k){
					array_push($cate,['type'=>'option','label'=>$v,'value'=>(int)$k]);
				}
			}
			$level = $request->input('level',1);
			$levelData = [];
			for($i=1;$i<=$this->layer;$i++){
				array_push($levelData,['type'=>'option','label'=>$i.'级','value'=>$i]);
			}
			$pid = $request->input('pid',0);
			$pidData = [];
			$result = $this->getList($request,'parent');
			if($result){
				foreach($result as $item){
					array_push($pidData,['type'=>'option','label'=>$item->title,'value'=>(int)$item->id]);
				}
			}

			$sort = $this->getMaxSort($pid);

			$action = [
				['type'=>'radio-group','label'=>'类别','prop'=>'cate_id','value'=>$id ? $data->cate_id : $cate_id,'hidden'=>true,'children'=>$cate],
				['type'=>'input','label'=>'标题名称','prop'=>'title','value'=>$id ? $data->title : '','rules'=>['required'=>true,'message'=>'标题名称不能为空']],
				['type'=>'select','label'=>'层级','prop'=>'level','value'=>$id ? $data->level : $level,'hidden'=>true,'children'=>$levelData],
				['type'=>'select','label'=>'父级','prop'=>'pid','value'=>$id ? $data->pid : $pid,'hidden'=>true,'children'=>$pidData],
			];

			if((!$id && $pid) || ($id && $data->pid)){
				array_push($action,
					['type'=>'input','label'=>'页面地址','prop'=>'url','value'=>$id ? $data->url : '','slot'=>['required'=>true,'message'=>'页面名称不能为空']],
					['type'=>'input','label'=>'参数名称','prop'=>'param','value'=>$id ? $data->param : ''],
					['type'=>'input','label'=>'参数描述','prop'=>'description','value'=>$id ? $data->desc : ''],
					['type'=>'switch','label'=>'参数必填','prop'=>'is_param','value'=>$id ? $data->is_param : 0],
				);
			}

			array_push($action,['type'=>'input','label'=>'排序','prop'=>'sort','value'=>$id ? $data->sort : $sort,'rules'=>['required'=>true,'message'=>'排列不能为空']]);

			return $action;
		}

		protected function getMapList(Request $request): array
		{
			return [
				['type'=>'id','label'=>'ID','prop'=>'id'],
				['type'=>'varchar','label'=>'标题名称','prop'=>'title'],
				['type'=>'varchar','label'=>'页面地址','prop'=>'url'],
				['type'=>'varchar','label'=>'参数名','prop'=>'param'],
				['type'=>'varchar','label'=>'参数描述','prop'=>'desc'],
				['type'=>'switch','label'=>'参数必填','prop'=>'is_param'],
				['type'=>'varchar','label'=>'排序','prop'=>'sort'],
			];
		}
	}
?>