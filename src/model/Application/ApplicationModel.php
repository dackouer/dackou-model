<?php
	namespace dackou\model\Application;

	use support\Request;
	use dackou\Json;
	use dackou\model\User\UserModel;
	use dackou\model\Menu\MenuModel;
	use dackou\model\Option\OptionModel;
	use dackou\model\Notice\NoticeModel;
	use dackou\model\Skin\SkinModel;
	use dackou\model\Goods\GoodsModel;

	class ApplicationModel{
		public function getList(Request $request,$method = 'home'){
			try{
				if($method){
					$_method = "get".\ucfirst($method)."List";
					if(method_exists($this,$_method)){
						return $this->$_method($request);
					}
				}

				return 100007;
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
			
		}

		protected function getHomeList(Request $request){
			return [];
		}

		protected function getLoginList(Request $request){
			$config = [];
			$options = [];
			$menu = [];

			$config = [
				'web_name' => '网站名称',
		        'logo' => '',
		        'copyright' => '国商科技',
		        'icp' => '苏ICP备25689547号',
		        'is_show_time' => 1,
		        'login_width' => 400,
		        'login_aside_x_align' => 'center',
		        'login_aside_y_align' => 'center',
		        'login_bg_img' => '',
		        'login_main_img' => '',
		        'login_main_img_width' => 300,
		        'login_main_img_height' => 350,
		        'login_aside_padding' => 10,
		        'login_main_padding' => 10,
		        'login_main_bg_color' => '',
		        'login_title' => '国商后台管理系统',
		        'login_title_font_size' => 18,
		        'login_title_font_bold' => 1,
		        'login_title_text_color' => '#111',
		        'login_title_align' => 'center',
		        'login_title_margin_top' => 20,
		        'is_socialite' => 1,
			];

			$options = [
				'mode' => [],
		        'logo_width' => 40,
		        'logo_height' => 40,
				'logo_font_size' => 16,
				'logo_text_color' => '#F30',
				'logo_hover_color' => 'red',
		        'bg_color' => '',
		        'bg_img' => '',
		        'is_header' => 1,
		        'is_full_screen' => 0,
		        'is_footer' => 1,
		        'header_height' => 0,
		        'footer_height' => 100,
		        'is_header_fixed' => 1,
		        'is_footer_fixed' => 1,
		        'header_align' => 'center',
		        'header_text_color' => '',
		        'header_bg_color' => '',
		        'footer_align' => 'center',
		        'footer_text_color' => '',
		        'footer_bg_color' => 'white',
		        'menu_font_size' => 15,
		        'menu_text_color' => '#333',
		        'menu_hover_color' => '#ababab',
		        'login_width' => 900,
		        'login_main_width' => 400,
		        'login_border_radius' => 10,
		        'login_text_color' => '#333',
		        'login_bg_color' => '#ddedfe',
		        'login_aside_padding' => 20,
		        'login_main_padding' => 20,
		        'login_aside_x_align' => 'center',
		        'login_aside_y_align' => 'center',
		        'login_main_bg_color' => '#f7fbff',
		        'login_title_font_size' => 28,
		        'login_title_font_bold' => 1,
		        'login_title_text_color' => '#111',
		        'login_title_align' => 'center',
		        'login_title_margin_top' => 20,
		        'is_socialite' => 1,
			];

			$menu = [
				['id'=>1,'title'=> '关于我们','icon'=> '','router'=> ''],
		        ['id'=>2,'title'=> '客户服务','icon'=> '','router'=> ''],
		        ['id'=>3,'title'=> '帮助中心','icon'=> '','router'=> ''],
		        ['id'=>4,'title'=> '开发文档','icon'=> '','router'=> ''],
		        ['id'=>5,'title'=> '联系我们','icon'=> '','router'=> ''],
			];

			return ['config'=>$config,'options'=>$options,'menu'=>$menu];
		}

		protected function getAccountList(Request $request){
			$uid = 0;
			$config = [];
			$skin = [
				"id" 					=> 1,
				"title" 				=> "默认",
				"mode"					=> 'ahm',
				"is_full_height"		=> 0,
				"logo_text_size"		=> 16,
				"logo_text_color"		=> 'white',
				"aside_width" 			=> 220,
				"aside_heigth" 			=> 0,
				"is_nav" 				=> 0,
				"is_nav_icon" 			=> 0,
				"nav_font_size" 		=> 16,
				"nav_text_color" 		=> "white",
				"nav_hover_color" 		=> "",
				"nav_bg_color" 			=> "#FFFFFF",
				"nav_hover_bg_color" 	=> "",
				"aside_bg_color" 		=> "#2599f4",
				"aside_text_color" 		=> "",
				"menu_is_icon" 			=> [1,1,1],
				"menu_font_size" 		=> 16,
				"menu_font_bold" 		=> 0,
				"menu_text_color" 		=> "#333",
				"menu_hover_text_color" => "blue",
				"menu_bg_color" 		=> "none",
				"menu_hover_bg_color" 	=> "#409EFF",
				"sub_font_size" 		=> 16,
				"sub_font_blod" 		=> 0,
				"sub_text_color" 		=> "#333",
				"sub_hover_text_color" 	=> "blue",
				"sub_bg_color" 			=> "#409EFF",
				"sub_hover_bg_color" 	=> "#409EFF",
				"main_bg_color" 		=> "#F8F8F8"
			];
			$menu = [];
			$notice = [];
			$user = [];

			$_class_name = $this->getClassName('Option');
			$service = new $_class_name();
			$config = $service->getList($request,'key','system');

			$_class_name = $this->getClassName('Menu');
			$service = new $_class_name();
			$menu = $service->getList($request,'account');

			$uid = $request->input('user_id',0);
			if($uid){
				$_class_name = $this->getClassName('User');
				$service = new $_class_name();
				$user = $service->getList($request,$uid);
				unset($user->openid);
				unset($user->idcard);
				unset($user->birth);
				unset($user->parent_id);
				unset($user->direct_share);
				unset($user->total_share);
				unset($user->coin);
				unset($user->score);
				unset($user->balance);
				unset($user->auth_type);
				unset($user->auth_name);
				unset($user->invite);
				unset($user->ip_address);
				unset($user->token);
				unset($user->sign);
				unset($user->merchant_id);
				unset($user->role_id);
				unset($user->group_id);
				unset($user->is_admin);
				unset($user->status);

				$_class_name = $this->getClassName('Notice');
				$service = new $_class_name();
				$notice = $service->getList($request,'user',$uid);
			}

			$data = ['config' => $config,'skin' => $skin,'menu' => $menu,'notice'=>$notice];
			if($uid){
				$data['user'] = $user;
			}

			return $data;
		}

		protected function getAdminList(Request $request){
			$_class_name = $this->getClassName('Board');
			$service = new $_class_name();
			$data = $service->getList($request,'child');

			if($data){
				foreach($data as $k => $v){
					if($v->type == '2'){
						switch($v->value){
							case '1':
								$service = new \app\model\Park\ParkModel();
								$data[$k]->children = $service->getList($request,'statsale');
								// $data[$k]->children['options'] = ['width'=>'100%','height'=>'500px','suffix'=>'间','is_hollow'=>true];
								break;
							case '2':
								$service = new \app\model\Park\ParkModel();
								$data[$k]->children = $service->getList($request,'statsale');
								// $data[$k]->children['options'] = ['width'=>'100%','height'=>'500px','suffix'=>'间','is_hollow'=>true];
								break;
							case '3':
								$service = new \app\model\Park\ParkModel();
								$data[$k]->children = $service->getList($request,'statsale');
								// $data[$k]->children['options'] = ['width'=>'100%','height'=>'500px','suffix'=>'间','is_hollow'=>true];
								break;
							case '4':
								$service = new \app\model\Park\ParkModel();
								$data[$k]->children = $service->getList($request,'statsale');
								// $data[$k]->children['options'] = ['width'=>'100%','height'=>'500px','suffix'=>'间','is_hollow'=>true];
								break;
							case '5':
								$service = new \app\model\Park\ParkModel();
								$data[$k]->children = $service->getList($request,'statsale');
								// $data[$k]->children['options'] = ['width'=>'100%','height'=>'500px','suffix'=>'间','is_hollow'=>true];
								break;
							default:
						}
					}
				}
			}

			// var_dump($data);

			$service = new \app\model\Park\ParkModel();
			$park = $service->getList($request,'option');

			$result = [
				'park'	  => $park,
				'data' 	  => $data,
				'options' => ['width'=>'100%','height'=>'500px','suffix'=>'间','is_hollow'=>true]
			];

			return $result;
		}

		protected function getBoardList(Request $request){
			$user = [];
			$config = [];
			$menu = [];
			$action = [];
			$message = [];
			$board = [];

			$uid = $request->input('user_id',$this->getSession($request,'uid'));
			// var_dump('uid: '.$uid);
			if(!$uid || !is_numeric($uid) || $uid < 0){
				return 100007;
			}

			$_class_name = $this->getClassName('User');
			$service = new $_class_name();
			$user = $service->getList($request,$uid);

			$_class_name = $this->getClassName('Notice');
			$service = new $_class_name();
			$notice = $service->getList($request,'user',$uid);

			$_class_name = $this->getClassName('Message');
			if($_class_name){
				$service = new $_class_name();
				$message = $service->getList($request,'user',$uid);
			}

			$_class_name = $this->getClassName('Option');
			$service = new $_class_name();
			$option = $service->getList($request,'key','system');

			$_class_name = $this->getClassName('Menu');
			$service = new $_class_name();
			$menu = $service->getList($request,'grant');
			// var_dump($menu);
			if(!$menu || !is_array($menu) || !count($menu)){
				$menu = [
	                ['id'=>10,'title'=>'控制台','icon'=>'','router'=>'/','pid'=>0,'level'=>1,'children'=>[
	                    ['id'=>1001,'title'=>'控制台','icon'=>'','router'=>'','pid'=>10,'level'=>2,'children'=>[
	                        ['id'=>100101,'title'=>'系统设置','icon'=>'','router'=>'option/system','pid'=>1001,'level'=>3],
	                        ['id'=>100102,'title'=>'菜单管理','icon'=>'','router'=>'menu','pid'=>1001,'level'=>3]
	                    ]]
	                ]]
	            ];
			}

			$_class_name = $this->getClassName('Board');
			if($_class_name){
				$service = new $_class_name();
				$board = $service->getList($request);
				if(isset($board['data'])){
					$board = $board['data'];
				}
			}

			if(!$board || !is_array($board) || !count($board)){
				$board = [
					['title'=>'data1','gutter'=>20,'span'=>6,'padding'=>15,'radius'=>10,'bg_color'=>'white','children'=>[
		                ['type'=>'user','title'=>'本月累计销售额','desc'=>'累计销售额','tip'=>'day','value'=>'8018','span'=>6,'yesterday'=>'5449','today'=>'','rate_day'=>'47.14','month'=>'51545.18','value_color'=>'red','prefix'=>'','suffix'=>'元'],
		                ['type'=>'user','title'=>'本月用户访问量','desc'=>'用户访问量','tip'=>'month','value'=>'288','span'=>6,'yesterday'=>'494','today'=>'','rate_day'=>'-41.70','month'=>'2412','prefix'=>'','suffix'=>'Pv'],
		                ['type'=>'user','title'=>'本月订单量','desc'=>'订单量','tip'=>'day','value'=>'5','span'=>6,'yesterday'=>'17','today'=>'','rate_day'=>'-70.58','month'=>'61','prefix'=>'','suffix'=>'单'],
		                ['type'=>'user','title'=>'本月新增用户','desc'=>'新增用户','tip'=>'day','value'=>'41','span'=>6,'yesterday'=>'69','today'=>'','rate_day'=>'-40.57','month'=>'384','prefix'=>'','suffix'=>'人'],
		            ]],
		            ['title'=>'data2','gutter'=>20,'span'=>3,'padding'=>15,'radius'=>10,'bg_color'=>'white','children'=>[
				        ['type'=>'lnk','title'=>'用户管理','desc'=>'','value'=>'','span'=>3,'icon'=>'Pear','text_color'=>'#67C23A','bg_color'=>'#F8F8F8','url'=>''],
				        ['type'=>'lnk','title'=>'系统设置','desc'=>'','value'=>'','span'=>3,'icon'=>'Magnet','text_color'=>'#E6A23C','bg_color'=>'#F8F8F8','url'=>''],
				        ['type'=>'lnk','title'=>'商品管理','desc'=>'','value'=>'','span'=>3,'icon'=>'Goods','text_color'=>'#F56C6C','bg_color'=>'#F8F8F8','url'=>''],
				        ['type'=>'lnk','title'=>'订单管理','desc'=>'','value'=>'','span'=>3,'icon'=>'Tickets','text_color'=>'#409EFF','bg_color'=>'#F8F8F8','url'=>''],
				        ['type'=>'lnk','title'=>'活动管理','desc'=>'','value'=>'','span'=>3,'icon'=>'ChatLineRound','text_color'=>'red','bg_color'=>'#F8F8F8','url'=>''],
				        ['type'=>'lnk','title'=>'文章管理','desc'=>'','value'=>'','span'=>3,'icon'=>'Notebook','text_color'=>'orange','bg_color'=>'#F8F8F8','url'=>''],
				        ['type'=>'lnk','title'=>'分销管理','desc'=>'','value'=>'','span'=>3,'icon'=>'Monitor','text_color'=>'yellow','bg_color'=>'#F8F8F8','url'=>''],
				        ['type'=>'lnk','title'=>'优惠券','desc'=>'','value'=>'','span'=>3,'icon'=>'Dish','text_color'=>'pink','bg_color'=>'#F8F8F8','url'=>''],
				    ]],
				    ['title'=>'data3','gutter'=>20,'span'=>12,'padding'=>15,'radius'=>10,'bg_color'=>'white','height'=>400,'children'=>[
		                ['type'=>'chart','title'=>'asd','desc'=>'','value'=>'','span'=>16,'yesterday'=>'','today'=>'','rate_day'=>'','data'=>[
		                    'data'=>[
		                    	'type'=>'line',
		                    	'title'=>['text'=> 'Stacked Line'],
		                    	'data'=>[
		                    		'title'=>['text'=> 'Stacked Line'],
                    	            'tooltip'=>['trigger'=> 'axis'],
                    	            'legend'=>['data'=> ['Email', 'Union Ads', 'Video Ads', 'Direct', 'Search Engine']],
                    	            'grid'=>['left'=> '3%','right'=> '4%','bottom'=> '3%','containLabel'=> true],
                    	            'toolbox'=>['feature'=> ['saveAsImage'=> []]],
                    	            'xAxis'=>['type'=>'category','boundaryGap'=> false,'data'=> ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']],
                    	            'yAxis'=>['type'=> 'value'],
                    	            'series'=>[
                    	                ['name'=>'Email','type'=>'line','stack'=>'Total','data'=>[120, 132, 101, 134, 90, 230, 210]],
                    	                ['name'=>'Union Ads','type'=>'line','stack'=>'Total','data'=>[220, 182, 191, 234, 290, 330, 310]],
                    	                ['name'=>'Video Ads','type'=>'line','stack'=>'Total','data'=>[150, 232, 201, 154, 190, 330, 410]],
                    	                ['name'=>'Direct','type'=>'line','stack'=>'Total','data'=>[320, 332, 301, 334, 390, 330, 320]],
                    	                ['name'=>'Search Engine','type'=>'line','stack'=>'Total','data'=>[820, 932, 901, 934, 1290, 1330, 1320]]
                    	            ]
                    	        ],
		                        'options'=>['width'=>'100%','height'=>'100%']
		                    ]
		                ]],
		                ['type'=>'table','title'=>'asd','desc'=>'','value'=>'','span'=>8,'yesterday'=>'','today'=>'','rate_day'=>'','data'=>[
		                    'thead'=>[
		                        ['type'=>'varchar','label'=>'ID','prop'=>'id'],
		                        ['type'=>'varchar','label'=>'用户名','prop'=>'username'],
		                        ['type'=>'varchar','label'=>'昵称','prop'=>'nickname'],
		                        ['type'=>'varchar','label'=>'角色','prop'=>'role'],
		                        ['type'=>'varchar','label'=>'出生日期','prop'=>'birth','width'=>120],
		                    ],
		                    'data'=>[
		                        ['id'=>1,'username'=>'张三','nickname'=>'红三','role'=>'会员1','birth'=>'1990-12-29'],
		                        ['id'=>2,'username'=>'李四','nickname'=>'李四','role'=>'会员2','birth'=>'1987-04-16'],
		                        ['id'=>3,'username'=>'王五','nickname'=>'王五','role'=>'会员3','birth'=>'1991-11-12'],
		                        ['id'=>4,'username'=>'吴六','nickname'=>'吴六','role'=>'会员2','birth'=>'1988-09-17'],
		                        ['id'=>5,'username'=>'周七','nickname'=>'周七','role'=>'会员1','birth'=>'1993-07-21'],
		                        ['id'=>6,'username'=>'戴八','nickname'=>'戴八','role'=>'会员3','birth'=>'1985-02-15'],
		                        ['id'=>7,'username'=>'唐九','nickname'=>'唐九','role'=>'会员2','birth'=>'1989-10-18'],
		                        ['id'=>7,'username'=>'唐九','nickname'=>'唐九','role'=>'会员2','birth'=>'1989-10-18'],
		                    ],
		                    'options'=>['size'=>'large','showOverflowTooltip'=>true,'maxHeight'=>400]
		                ]],
		            ]]
				];
			}
			
			$action = [
				['type'=>'switch','label'=>'菜单收缩图标','prop'=>'is_collapse','value'=>1],
	            ['type'=>'switch','label'=>'菜单收缩','prop'=>'menu_is_collapse','value'=>0],
	            ['type'=>'input-number','label'=>'导航栏字体大小','prop'=>'nav_font_size','value'=>14],
	            ['type'=>'color','label'=>'导航栏文本颜色','prop'=>'nav_text_color','value'=>$user->is_admin ? 'white' : '#333'],
	            ['type'=>'color','label'=>'导航栏悬停文本色','prop'=>'nav_hover_text_color','value'=>'pink'],
	            ['type'=>'color','label'=>'导航栏激活文本色','prop'=>'nav_active_text_color','value'=>'red'],
	            ['type'=>'color','label'=>'导航栏背景颜色','prop'=>'nav_bg_color','value'=>$user->is_admin ? 'white' : 'white'],
	            ['type'=>'color','label'=>'导航栏悬停背景色','prop'=>'nav_hover_bg_color','value'=>''],
	            ['type'=>'color','label'=>'导航栏激活背景色','prop'=>'nav_active_bg_color','value'=>''],
	            ['type'=>'input-number','label'=>'导航栏外边距','prop'=>'margin','value'=>20],
	            ['type'=>'input-number','label'=>'导航栏内边距','prop'=>'padding','value'=>20],
	            ['type'=>'input-number','label'=>'边栏菜单宽度','prop'=>'menu_width','value'=>220],
	            ['type'=>'input-number','label'=>'边栏菜单高度','prop'=>'menu_height','value'=>500],
	            ['type'=>'color','label'=>'边栏背景色','prop'=>'bg_color','value'=>$user->is_admin ? '#409EFF' : '#409EFF'],
	            ['type'=>'color','label'=>'边栏字体颜色','prop'=>'text_color','value'=>$user->is_admin ? '#333' : 'white'],
	            ['type'=>'switch','label'=>'边栏用户头像','prop'=>'is_avatar','value'=>1],
	            ['type'=>'switch','label'=>'菜单右边框','prop'=>'menu_border_right','value'=>0],
	            ['type'=>'switch','label'=>'菜单底边框','prop'=>'menu_border_bottom','value'=>0],
	            ['type'=>'switch','label'=>'导航栏菜单','prop'=>'menu_is_nav','value'=>0],
	            ['type'=>'radio','label'=>'显示导航栏层数','prop'=>'menu_nav_level','value'=>1,'children'=>[
	                ['label'=>'一层','value'=>1],['label'=>'二层','value'=>2],['label'=>'三层','value'=>3]
	            ]],
	            ['type'=>'input-number','label'=>'一级字体大小','prop'=>'menu_font_size','value'=>14],
	            ['type'=>'switch','label'=>'一级字体加粗','prop'=>'menu_font_bold','value'=>0],
	            ['type'=>'color','label'=>'一级字体颜色','prop'=>'menu_text_color','value'=>'#333'],
	            ['type'=>'color','label'=>'一级悬停字体色','prop'=>'menu_hover_text_color','value'=>'blue'],
	            ['type'=>'color','label'=>'一级激活文本颜色','prop'=>'menu_active_text_color','value'=>'#333'],
	            ['type'=>'color','label'=>'一级激活背景颜色','prop'=>'menu_active_bg_color','value'=>''],
	            ['type'=>'color','label'=>'一级背景颜色','prop'=>'menu_bg_color','value'=>''],
	            ['type'=>'color','label'=>'一级悬停背景色','prop'=>'menu_hover_bg_color','value'=>''],
	            ['type'=>'input-number','label'=>'二级字体大小','prop'=>'sub_font_size','value'=>14],
	            ['type'=>'color','label'=>'二级字体颜色','prop'=>'sub_text_color','value'=>'#333'],
	            ['type'=>'color','label'=>'二级悬停字体色','prop'=>'sub_hover_text_color','value'=>'#333'],
	            ['type'=>'color','label'=>'二级激活文本颜色','prop'=>'sub_active_text_color','value'=>'#333'],
	            ['type'=>'color','label'=>'二级激活背景颜色','prop'=>'sub_active_bg_color','value'=>'rgb(121, 187, 255)'],
	            ['type'=>'color','label'=>'二级背景颜色','prop'=>'sub_bg_color','value'=>''],
	            ['type'=>'color','label'=>'二级悬停背景色','prop'=>'sub_hover_bg_color','value'=>''],
	            ['type'=>'color','label'=>'主体背景颜色','prop'=>'main_bg_color','value'=>'#F8F8F8'],
			];

			return [
				'user' => $user,
				'config' => $option,
				'menu' => $menu,
				'action' => $action,
				'skin' => $action,
				'message' =>$message,
				'board' =>$board,
			];
		}

		protected function getMiniList(Request $request){
			try{
				$tabbar = [];
				$_class_name = $this->getClassName('Tabbar');
				if($_class_name){
					$service = new $_class_name();
					$result = $service->getList($request);
					$result = $result ? ((isset($result['data']) && $result['data']) ? $result['data'] : $result) : []; 
					if($result){
						foreach($result as $item){
							if($item->is_valid){
								array_push($tabbar,[
									'id' 		=> $item->id,
									'title'		=> $item->title,
									'pic'		=> $item->pic,
									'active_pic'=> $item->active_pic,
									'url'		=> $item->url
								]);
							}
						}
					}
				}
				if($tabbar){
					$tabbar[0]['url'] = '/pages/index/index';
					$tabbar[1]['url'] = '/pages/goods/index';
					$tabbar[2]['url'] = '/pages/cart/index';
					$tabbar[3]['url'] = '/pages/user/index';
				}

				$config = [];
				$_class_name = $this->getClassName('Option');
				$service = new $_class_name();
				$config = $service->getList($request,'key','system,mini');

				$tabulation = [];
				$_class_name = $this->getClassName('Tabulation');
				if($_class_name){
					$service = new $_class_name();
					$tabulation = $service->getList($request,'key',11);
				}

				$carousel = [];
				$_class_name = $this->getClassName('Carousel');
				if($_class_name){
					$service = new $_class_name();
					$carousel = $service->getList($request,'key','home');
				}

				$notice = [];
				$_class_name = $this->getClassName('Notice');
				if($_class_name){
					$service = new $_class_name();
					$notice = $service->getList($request,'top',5);
				}

				$cate = [];
				$_class_name = $this->getClassName('GoodsCate');
				if($_class_name){
					$service = new $_class_name();
					$cate = $service->getList($request,'parent');
				}

				$goods = [];
				$_class_name = $this->getClassName('Goods');
				if($_class_name){
					$service = new $_class_name();
					$goods = $service->getList($request,'top',20);
				}

				$data = [
					'tabbar'	=> $tabbar,
					'config'	=> $config,
					'carousel'	=> $carousel,
					'tabulation'=> $tabulation,
					'notice'	=> $notice,
					'cate'		=> $cate,
					'goods'		=> $goods,
				];

				if(isset($this->config['app_is_map']) && $this->config['app_is_map']){
					$service = new \app\service\Weather\WeatherService();
					$data['map'] = $service->getWeather($request);
				}

				if(isset($this->config['app_is_market']) && $this->config['app_is_market']){
					if(isset($this->config['app_is_teamwork']) && $this->config['app_is_teamwork']){
						// 开启拼团
						$data['teamwork'] = [];
						$_class_name = $this->getClassName('Market');
						if($_class_name){
							$service = new $_class_name();
							$result = $service->getList($request,'teamwork',true);
							$data['teamwork'] = [];
							if($result && isset($result['data'])){
								$data['teamwork'] = $result['data'];
							}
						}
					}

					if(isset($this->config['app_is_seckill']) && $this->config['app_is_seckill']){
						$data['period'] = [];
						$_class_name = $this->getClassName('MarketPeriod');
						if($_class_name){
							$service = new $_class_name();
							$data['period'] = $service->getList($request,'valid');

							// 开启秒杀
							$service = new \app\model\Market\MarketModel();
							$result = $service->getList($request,'seckill',true);
							$data['seckill'] = [];
							if($result && isset($result['data'])){
								$data['seckill'] = $result['data'];
							}
						}
						$data['seckill'] = [];
						$_class_name = $this->getClassName('Market');
						if($_class_name){
							// 开启秒杀
							$service = new $_class_name();
							$result = $service->getList($request,'seckill',true);
							if($result && isset($result['data'])){
								$data['seckill'] = $result['data'];
							}
						}
					}

					if(isset($this->config['app_is_counter']) && $this->config['app_is_counter']){
						// 开启砍价
						$data['counter'] = [];
						$_class_name = $this->getClassName('Market');
						if($_class_name){
							$service = new $_class_name();
							$result = $service->getList($request,'counter',true);
							if($result && isset($result['data'])){
								$data['counter'] = $result['data'];
							}
						}
					}

					if(isset($this->config['app_is_section']) && $this->config['app_is_section']){
						$data['prefecture'] = [];
						$_class_name = $this->getClassName('MarketPrefecture');
						if($_class_name){
							$service = new $_class_name();
							$data['prefecture'] = $service->getList($request,'valid');
						}

						// 开启专区
						$data['section'] = [];
						$_class_name = $this->getClassName('Market');
						if($_class_name){
							$service = new $_class_name();
							$result = $service->getList($request,'section',true);
							if($result && isset($result['data'])){
								$data['section'] = $result['data'];
							}
						}
					}

					if(isset($this->config['app_is_crowdfunding']) && $this->config['app_is_crowdfunding']){
						// 开启众筹
						$crowdfunding = [];
						$_class_name = $this->getClassName('Market');
						if($_class_name){
							$service = new $_class_name();
							$crowdfunding = $service->getList($request,'crowdfunding',true);
						}
						$data['crowdfunding'] = $crowdfunding;
					}

					if(isset($this->config['app_is_auction']) && $this->config['app_is_auction']){
						// 开启拍卖
						$auction = [];
						$_class_name = $this->getClassName('Market');
						if($_class_name){
							$service = new $_class_name();
							$auction = $service->getList($request,'auction',true);
						}
						$data['auction'] = $auction;
					}

					if(isset($this->config['app_is_blind']) && $this->config['app_is_blind']){
						// 开启盲盒
						$data['blind'] = [];
						$_class_name = $this->getClassName('Market');
						if($_class_name){
							$service = new $_class_name();
							$data['blind'] = $service->getList($request,'blind',true);
						}
					}
				}else{
					$tags = [];
					$_class_name = $this->getClassName('GoodsTags');
					if($_class_name){
						$service = new $_class_name();
						$tags = $service->getList($request);
						// var_dump($tags);
						if($tags && isset($tags['data']) && count($tags['data'])){
							foreach($tags['data'] as $key => $item){
								// var_dump($item);
								$service = new GoodsModel();
								$temp = $service->getList($request,'tags',$item->id);
								if($temp){
									$tags['data'][$key]->goods = $temp;
								}
							}
						}
					}

					$data['tags'] = isset($tags['data']) ? $tags['data'] : [];
				}

				return $data;
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		protected function getAppList(Request $request){
			try{
				return $this->getMiniList($request);
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		protected function getWebList(Request $request){
			try{
				$_class_name = $this->getClassName('Option');
				// var_dump('option class_name: '.$_class_name);
				$service = new $_class_name();
				$config = $service->getList($request,'key','web,decoration,system');

				$_class_name = $this->getClassName('Carousel');
				$service = new $_class_name();
				$carousel = $service->getList($request,'all',['CateID' => 3,'Key' => 'home']);

				$_class_name = $this->getClassName('About');
				$service = new $_class_name();
				$about = $service->getList($request,'top',10);

				$_class_name = $this->getClassName('Trend');
				$service = new $_class_name();
				$trend = $service->getList($request,'top',10);

				$_class_name = $this->getClassName('News');
				$service = new $_class_name();
				$news = $service->getList($request,'top',10);

				$service = new \app\model\Party\PartyModel();
				$party = $service->getList($request,'top',10);

				$_class_name = $this->getClassName('Activity');
				$service = new $_class_name();
				$activity = $service->getList($request,'top',10);

				$_class_name = $this->getClassName('Album');
				$service = new $_class_name();
				$album = $service->getList($request,'top',10);

				$_class_name = $this->getClassName('Notice');
				$service = new $_class_name();
				$notice = $service->getList($request,'top',10);

				$_class_name = $this->getClassName('Link');
				$service = new $_class_name();
				$link = $service->getList($request);

				$_class_name = $this->getClassName('Tabbar');
				$service = new $_class_name();
				$tabbar = $service->getList($request,'child');

				$_class_name = $this->getClassName('Tabulation');
				$service = new $_class_name();
				$tabulation = $service->getList($request,'key',66);

				// $service = new \app\model\Member\MemberModel();
				// $member = $service->getList($request,'top',10);

				return [
					'config' 	=> $config,
					'carousel' 	=> isset($carousel['data']) ? $carousel['data'] : $carousel,
					'about' 	=> isset($about['data']) ? $about['data'] : $about,
					'trend' 	=> isset($trend['data']) ? $trend['data'] : $trend,
					'news' 		=> isset($news['data']) ? $news['data'] : $news,
					'party' 	=> isset($party['data']) ? $party['data'] : $party,
					'activity' 	=> isset($activity['data']) ? $activity['data'] : $activity,
					'album' 	=> isset($album['data']) ? $album['data'] : $album,
					'notice' 	=> isset($notice['data']) ? $notice['data'] : $notice,
					'member' 	=> [],
					'link' 		=> isset($link['data']) ? $link['data'] : $link,
					'tabbar'	=> isset($tabbar['data']) ? $tabbar['data'] : $tabbar,
					'tabulation'=> isset($tabulation['data']) ? $tabulation['data'] : $tabulation
				];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		protected function getSession(Request $request,$key = ''){
			if($key){
				$prefix = '';
				$config = config('database') ?? [];
				if(isset($config['connections']['mysql']['prefix'])){
					$prefix = $config['connections']['mysql']['prefix'];
				}
        		$key = $prefix.$key;
        		$session = $request->session();
        		if($session->exists($key)){
        			return $session->get($key);
        		}
        		return '';
        	}
        	return $request->session();
		}

		protected function getClassName($class){
        	if(\preg_match('/[A-Z]/',\lcfirst($class))){
				$temp = \lcfirst($class);
				$temp = \preg_split('/(?=[A-Z])/', $temp);
				$class = \ucfirst($class);
				$parent = \ucfirst($temp[0]);
				$_class_name = "\\app\\model\\{$parent}\\{$class}Model";
				if(\class_exists($_class_name)){
					return $_class_name;
				}
				$_class_name = "\\dackou\\model\\{$parent}\\{$class}Model";
				if(\class_exists($_class_name)){
					return $_class_name;
				}
				return false;
			}else{
				$class = \ucfirst($class);
				$_class_name = "\\app\\model\\{$class}\\{$class}Model";
				if(\class_exists($_class_name)){
					return $_class_name;
				}
				$_class_name = "\\dackou\\model\\{$class}\\{$class}Model";
				if(\class_exists($_class_name)){
					return $_class_name;
				}
				return false;
			}
        }

		protected function getExceptionError($e){
			$result = [
				'code'	=> $e->getCode() ? $e->getCode() : 1,
				'file'	=> $e->getFile(),
				'line'	=> $e->getLine(),
				'msg'	=> $e->getMessage()
			];
			// var_dump('exception error: ',$result);
			return $result;
		}
	}
?>