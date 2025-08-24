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
			if($method){
				$_method = "get".\ucfirst($method)."List";
				if(method_exists($this,$_method)){
					return $this->$_method($request);
				}
			}

			return 100007;
		}

		protected function getHomeList(Request $request){
			try{
				return [];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		protected function getAdminList(Request $request){
			try{
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

			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		protected function getBoardList(Request $request){
			try{
				$notice = [];
				$uid = $request->input('user_id',0);
				if($uid){
					$service = new UserModel();
					$user = $service->getList($request,$uid);

					$service = new NoticeModel();
					$notice = $service->getList($request,'user',$uid);
				}

				$service = new OptionModel();
				$option = $service->getList($request,'key','system');

				$service = new MenuModel();
				$menu = $service->getList($request,'grant');

				// $service = new SkinModel();
				// $skin = $service->getList($request);
				$skin = [
					[
						"id" 					=> 1,
						"title" 				=> "默认",
						"mode"					=> 'ham',
						"is_full_height"		=> 0,
						"logo_text_size"		=> 16,
						"logo_text_color"		=> 'white',
						"aside_width" 			=> 250,
						"aside_heigth" 			=> 0,
						"is_nav" 				=> 0,
						"is_nav_icon" 			=> 0,
						"nav_font_size" 		=> 16,
						"nav_text_color" 		=> "white",
						"nav_hover_color" 		=> "",
						"nav_bg_color" 			=> "#409EFF",
						"nav_hover_bg_color" 	=> "",
						"aside_bg_color" 		=> "",
						"aside_text_color" 		=> "",
						"menu_is_icon" 			=> [1,1,1],
						"menu_font_size" 		=> 16,
						"menu_font_bold" 		=> 0,
						"menu_text_color" 		=> "#333",
						"menu_hover_text_color" => "blue",
						"menu_bg_color" 		=> "#409EFF",
						"menu_hover_bg_color" 	=> "#409EFF",
						"sub_font_size" 		=> 16,
						"sub_font_blod" 		=> 0,
						"sub_text_color" 		=> "#333",
						"sub_hover_text_color" 	=> "blue",
						"sub_bg_color" 			=> "#409EFF",
						"sub_hover_bg_color" 	=> "#409EFF",
						"main_bg_color" 		=> "#F8F8F8"
					]
				];
				$skinIndex = 0;
				$skinData = [
					'index' => $skinIndex,
					'data' => $skin,
					'action' => [
						['type'=>'radio-group','label'=>'布局样式','prop'=>'mode','value'=>$skin[$skinIndex]['mode'],'children'=>[
							['type'=>'option','label'=>'ham','value'=>'ham'],
							['type'=>'option','label'=>'ahm','value'=>'ahm'],
						]],
						['type'=>'switch','label'=>'全屏高度','prop'=>'is_full_height','value'=>$skin[$skinIndex]['is_full_height']],
						['type'=>'input','label'=>'Logo文本大小','prop'=>'logo_text_size','value'=>$skin[$skinIndex]['logo_text_size'],'slot'=>['suffix'=>['value'=>'px']]],
						['type'=>'color-picker','label'=>'Logo文本颜色','prop'=>'logo_text_color','value'=>$skin[$skinIndex]['logo_text_color']],
						['type'=>'switch','label'=>'显示导航栏','prop'=>'is_nav','value'=>$skin[$skinIndex]['is_nav']],
						['type'=>'input','label'=>'导航栏文本大小','prop'=>'nav_font_size','value'=>$skin[$skinIndex]['nav_font_size'],'slot'=>['suffix'=>['value'=>'px']]],
						['type'=>'color-picker','label'=>'导航栏文本颜色','prop'=>'nav_text_color','value'=>$skin[$skinIndex]['nav_text_color']],
						['type'=>'color-picker','label'=>'导航栏背景颜色','prop'=>'nav_bg_color','value'=>$skin[$skinIndex]['nav_bg_color']],
						['type'=>'color-picker','label'=>'导航栏hover文本色','prop'=>'nav_hover_color','value'=>$skin[$skinIndex]['nav_hover_color']],
						['type'=>'color-picker','label'=>'导航栏hover背景色','prop'=>'nav_hover_bg_color','value'=>$skin[$skinIndex]['nav_hover_bg_color']],
						['type'=>'input','label'=>'侧边栏宽度','prop'=>'aside_width','value'=>$skin[$skinIndex]['aside_width'],'slot'=>['suffix'=>['value'=>'px']]],
						['type'=>'input','label'=>'侧边栏高度','prop'=>'aside_heigth','value'=>$skin[$skinIndex]['aside_heigth'],'slot'=>['suffix'=>['value'=>'px']]],
						['type'=>'color-picker','label'=>'侧边栏背景色','prop'=>'aside_bg_color','value'=>$skin[$skinIndex]['aside_bg_color']],
						['type'=>'checkbox-group','label'=>'显示菜单图标','prop'=>'menu_is_icon','value'=>$skin[$skinIndex]['menu_is_icon'],'children'=>[
							['type'=>'option','label'=>'一级','value'=>$skin[$skinIndex]['menu_is_icon'][0]],
							['type'=>'option','label'=>'二级','value'=>$skin[$skinIndex]['menu_is_icon'][1]],
							['type'=>'option','label'=>'三级','value'=>$skin[$skinIndex]['menu_is_icon'][2]],
						]],
						['type'=>'input','label'=>'菜单一级文本大小','prop'=>'menu_font_size','value'=>$skin[$skinIndex]['menu_font_size'],'slot'=>['suffix'=>['value'=>'px']]],
						['type'=>'color-picker','label'=>'菜单一级文本颜色','prop'=>'menu_text_color','value'=>$skin[$skinIndex]['menu_text_color']],
						['type'=>'color-picker','label'=>'菜单一级背景颜色','prop'=>'menu_bg_color','value'=>$skin[$skinIndex]['menu_bg_color']],
						['type'=>'color-picker','label'=>'菜单一级hover文本颜色','prop'=>'menu_hover_text_color','value'=>$skin[$skinIndex]['menu_hover_text_color']],
						['type'=>'color-picker','label'=>'菜单一级hover背景颜色','prop'=>'menu_hover_bg_color','value'=>$skin[$skinIndex]['menu_hover_bg_color']],
						['type'=>'input','label'=>'菜单二级文本大小','prop'=>'sub_font_size','value'=>$skin[$skinIndex]['sub_font_size'],'slot'=>['suffix'=>['value'=>'px']]],
						['type'=>'color-picker','label'=>'菜单二级文本颜色','prop'=>'sub_text_color','value'=>$skin[$skinIndex]['sub_text_color']],
						['type'=>'color-picker','label'=>'菜单二级背景颜色','prop'=>'sub_bg_color','value'=>$skin[$skinIndex]['sub_bg_color']],
						['type'=>'color-picker','label'=>'菜单二级hover文本颜色','prop'=>'sub_hover_text_color','value'=>$skin[$skinIndex]['sub_hover_text_color']],
						['type'=>'color-picker','label'=>'菜单二级hover背景颜色','prop'=>'sub_hover_bg_color','value'=>$skin[$skinIndex]['sub_hover_bg_color']],
						['type'=>'color-picker','label'=>'主体背景颜色','prop'=>'main_bg_color','value'=>$skin[$skinIndex]['main_bg_color']],
					]
				];

				$data = ['config' => $option,'skin' => $skinData,'menu' => $menu,'notice'=>$notice];
				if($uid){
					$data['user'] = $user;
				}

				return $data;
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		protected function getMiniList(Request $request){
			try{
				$tabbar = [];
				$_class_name = $this->getClassName('Tabbar');
				if($_class_name){
					$service = new $_class_name();
					$result = $service->getList($request);
					if($result && isset($result['data']) && $result['data']){
						foreach($result['data'] as $item){
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
				var_dump('_class_name: '.$_class_name);
				if($_class_name){
					$service = new $_class_name();
					$config = $service->getList($request,'key','mini');
				}

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
				return [];
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		protected function getWebList(Request $request){
			try{
				$_class_name = $this->getClassName('Option');
				// var_dump('option class_name: '.$_class_name);
				$service = new $_class_name();
				$config = $service->getList($request,'key','web,decoration');

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