<?php
	namespace dackou;

	class Route{
		public static function get(){
			$routes = \Webman\Route::getRoutes();
			$ignore_list = [];
			foreach ($routes as $tmp_route) {
			    $ignore_list[$tmp_route->getPath()] = 0;
			}

			$routes = self::getRouters();
			$route = [];
			foreach($routes as $item){
				$temp = explode('\\',$item['class']);
				foreach($item['method'] as $method){
					$method = $method === "{id:\d+}" ? 'index' : $method;
					$controller = '/'. strtolower(preg_replace('/((?<=[a-z])(?=[A-Z]))/', '_', end($temp)));
					if($method !== 'index'){
						$controller .= '/'.$method;
					}

					if(!isset($ignore_list[$controller]) && class_exists($item['class'])){
						$class = new \ReflectionClass($item['class']);
						// var_dump('route controller: '.$controller.' class: '.$class->name.' method: '.$method);
						\Webman\Route::any($controller,[$class->name,$method]);
					}
				}
			}
			return $route;
		}

		// 获取app router
		private static function getAppRouters(){
			$routes = \Webman\Route::getRoutes();
			$ignore_list = [];
			foreach ($routes as $tmp_route) {
			    $ignore_list[$tmp_route->getPath()] = 0;
			}

			$suffix = config('app.controller_suffix', '');
			$suffix_length = strlen($suffix);

			// 递归遍历目录查找控制器自动设置路由
			$dir_iterator = new \RecursiveDirectoryIterator(app_path());
			$iterator = new \RecursiveIteratorIterator($dir_iterator);

			$route = [];
			foreach($iterator as $file){

				// 忽略目录和非php文件
				if (is_dir($file) || $file->getExtension() != 'php') {
				    continue;
				}

				$file_path = str_replace('\\', '/',$file->getPathname());
				// 文件路径里不带controller的文件忽略
				if (strpos(strtolower($file_path), '/controller/') === false) {
				    continue;
				}

				// 只处理带 controller_suffix 后缀的
				if ($suffix_length && substr($file->getBaseName('.php'), -$suffix_length) !== $suffix) {
				    continue;
				}

			    // 根据文件路径计算uri
			    $uri_path = str_replace(['/controller/', '/Controller/'], '/', substr(substr($file_path, strlen(app_path())), 0, - (4 + $suffix_length)));
			    // 根据文件路径是被类名
	    		$class_name = str_replace('/', '\\',substr(substr($file_path, strlen(base_path())), 0, -4));
	    		
			    if (!class_exists($class_name)) {
			        echo "Class $class_name not found, skip route for it\n";
			        continue;
			    }

			    // 通过反射找到这个类的所有共有方法作为action
			    $class = new \ReflectionClass($class_name);
			    $class_name = $class->name;
			    $methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);
			    $properties = $class->getProperties(\ReflectionProperty::IS_PROTECTED);
			    // var_dump('properties: ',$properties);
			    
			    $name = [];
			    $method = [];
			    if($methods){
			    	foreach($methods as $item){
		    			$action = $item->name;
		    		    if(!in_array($action, ['__construct', '__destruct'])){
		    		        array_push($method,$action);
		    		    }
			    	}

			    	if($properties){
			    		foreach($properties as $item){
			    			if($item->name === 'table'){
			    				// array_push($method);
			    			}
			    		}
			    	}
			    }

			    if($method){
			    	$tmp = explode('\\',$class_name);
			    	$route_name = end($tmp);
			    	array_push($name,$route_name);
			    	array_push($route,['name'=>$route_name,'class'=>$class_name,'method'=>$method]);
			    }
			}
			
			return ['name'=> $name,'route' => $route];
		}

		private static function getRouters(){
			$action = ['index','show','all','page','user','top','action','key','code','create','add','mod','save','fimport','import','export','switch','editor','qrcode','init','initial','check','del','clear'];
			$action_cate = ['cate'];
			$action_layer = ['tree','child','leval','parent'];
			$action_option = ['key','option','options'];
			$default = [
				['name'=>'About','class'=>'\dackou\controller\About\About','method'=>$action],
				['name'=>'Activity','class'=>'\dackou\controller\Activity\Activity','method'=>$action],
				['name'=>'ActivityCate','class'=>'\dackou\controller\Activity\ActivityCate','method'=>$action],
				['name'=>'ActivityUser','class'=>'\dackou\controller\Activity\ActivityUser','method'=>$action],
				['name'=>'Application','class'=>'\dackou\controller\Application\Application','method'=>['index','home','login','board','admin','account','mini','app']],
				['name'=>'Article','class'=>'\dackou\controller\Article\Article','method'=>$action],
				['name'=>'ArticleCate','class'=>'\dackou\controller\Article\ArticleCate','method'=>$action],
				['name'=>'Bank','class'=>'\dackou\controller\Bank\Bank','method'=>$action],
				['name'=>'Board','class'=>'\dackou\controller\Board\Board','method'=>$action],
				['name'=>'Brand','class'=>'\dackou\controller\Brand\Brand','method'=>$action],
				['name'=>'Carousel','class'=>'\dackou\controller\Carousel\Carousel','method'=>$action],
				['name'=>'Cart','class'=>'\dackou\controller\Cart\Cart','method'=>$action],
				['name'=>'City','class'=>'\dackou\controller\City\City','method'=>['index','province','city','district','street','api','show','add','mod','del']],
				['name'=>'Coupon','class'=>'\dackou\controller\Coupon\Coupon','method'=>$action],
				['name'=>'Currency','class'=>'\dackou\controller\Currency\Currency','method'=>$action],
				['name'=>'Express','class'=>'\dackou\controller\Express\Express','method'=>$action],
				['name'=>'Field','class'=>'\dackou\controller\Field\Field','method'=>$action],
				['name'=>'Goods','class'=>'\dackou\controller\Goods\Goods','method'=>$action],
				['name'=>'GoodsAttr','class'=>'\dackou\controller\Goods\GoodsAttr','method'=>$action],
				['name'=>'GoodsAttrs','class'=>'\dackou\controller\Goods\GoodsAttrs','method'=>$action],
				['name'=>'GoodsBrand','class'=>'\dackou\controller\Goods\GoodsBrand','method'=>$action],
				['name'=>'GoodsCate','class'=>'\dackou\controller\Goods\GoodsCate','method'=>$action],
				['name'=>'GoodsSpec','class'=>'\dackou\controller\Goods\GoodsSpec','method'=>$action],
				['name'=>'GoodsSpecs','class'=>'\dackou\controller\Goods\GoodsSpecs','method'=>$action],
				['name'=>'GoodsTags','class'=>'\dackou\controller\Goods\GoodsTags','method'=>$action],
				['name'=>'GoodsType','class'=>'\dackou\controller\Goods\GoodsType','method'=>$action],
				['name'=>'Grant','class'=>'\dackou\controller\Grant\Grant','method'=>$action],
				['name'=>'Handle','class'=>'\dackou\controller\Handle\Handle','method'=>$action],
				['name'=>'Help','class'=>'\dackou\controller\Help\Help','method'=>$action],
				['name'=>'Log','class'=>'\dackou\controller\Log\Log','method'=>$action],
				['name'=>'Login','class'=>'\dackou\controller\Login\Login','method'=>['index']],
				['name'=>'Logout','class'=>'\dackou\controller\Logout\Logout','method'=>['index']],
				['name'=>'Menu','class'=>'\dackou\controller\Menu\Menu','method'=>$action],
				['name'=>'Merchant','class'=>'\dackou\controller\Merchant\Merchant','method'=>$action],
				['name'=>'News','class'=>'\dackou\controller\News\News','method'=>$action],
				['name'=>'NewsCate','class'=>'\dackou\controller\News\NewsCate','method'=>$action],
				['name'=>'Notice','class'=>'\dackou\controller\Notice\Notice','method'=>$action],
				['name'=>'Option','class'=>'\dackou\controller\Option\Option','method'=>$action],
				['name'=>'Order','class'=>'\dackou\controller\Order\Order','method'=>$action],
				['name'=>'Page','class'=>'\dackou\controller\Page\Page','method'=>$action],
				['name'=>'Payment','class'=>'\dackou\controller\Payment\Payment','method'=>$action],
				['name'=>'Position','class'=>'\dackou\controller\Position\Position','method'=>$action],
				['name'=>'Poster','class'=>'\dackou\controller\Poster\Poster','method'=>$action],
				['name'=>'Purchase','class'=>'\dackou\controller\Purchase\Purchase','method'=>$action],
				['name'=>'Record','class'=>'\dackou\controller\Record\Record','method'=>$action],
				['name'=>'Reg','class'=>'\dackou\controller\Reg\Reg','method'=>['index']],
				['name'=>'Role','class'=>'\dackou\controller\Role\Role','method'=>$action],
				['name'=>'Store','class'=>'\dackou\controller\Store\Store','method'=>$action],
				['name'=>'Tabbar','class'=>'\dackou\controller\Tabbar\Tabbar','method'=>$action],
				['name'=>'Tabulation','class'=>'\dackou\controller\Tabulation\Tabulation','method'=>$action],
				['name'=>'Unit','class'=>'\dackou\controller\Unit\Unit','method'=>$action],
				['name'=>'User','class'=>'\dackou\controller\User\User','method'=>$action],

				['name'=>'Cache','class'=>'\dackou\controller\Service\Cache','method'=>['index']],
				['name'=>'Captcha','class'=>'\dackou\controller\Service\Captcha','method'=>['index']],
				['name'=>'Download','class'=>'\dackou\controller\Service\Download','method'=>['index','down']],
				['name'=>'Query','class'=>'\dackou\controller\Service\Query','method'=>['index']],
				['name'=>'Reload','class'=>'\dackou\controller\Service\Reload','method'=>['index']],
				['name'=>'Sms','class'=>'\dackou\controller\Service\Sms','method'=>['index','send']],
				['name'=>'Token','class'=>'\dackou\controller\Service\Token','method'=>['index']],
				['name'=>'Upload','class'=>'\dackou\controller\Service\Upload','method'=>['index','local','cert','wang','umo']],
				['name'=>'Wechat','class'=>'\dackou\controller\Service\Wechat','method'=>['auth','login','sign','phone']],
				['name'=>'Callback','class'=>'\dackou\controller\Service\Callback','method'=>['qq','wechat','weibo','taobao','alipay','coding','dingtalk','baidu','azure','douban','douyin','facebook','figma','feishu','gitee','github','google']],
				['name'=>'Socialite','class'=>'\dackou\controller\Service\Socialite','method'=>['qq','wechat','weibo','taobao','alipay','coding','dingtalk','baidu','azure','douban','douyin','facebook','figma','feishu','gitee','github','google']],
			];

			$routes = self::getAppRouters();
			$name = $routes['name'];
			$route = $routes['route'];
			foreach($default as $item){
				if(!in_array($item['name'],$name)){
					array_push($route,$item);
				}
			}

			// var_dump('route: ',$route);
			return $route;
		}

		private static function arrayToObject($arr){
			return json_decode(json_encode($arr));
		}
	}
?>