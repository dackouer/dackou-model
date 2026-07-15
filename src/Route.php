<?php
    namespace dackou;

    use Webman\Route as WebmanRoute;

    class Route{
        public static function run(){
            self::setAppRoute();
            self::setDackouRoute();
        }

        // 设置app路由
        private static function setAppRoute(){
            // 已经设置过路由的uri则忽略
            $routes = WebmanRoute::getRoutes();
            $ignore_list = [];
            foreach ($routes as $tmp_route) {
                $ignore_list[$tmp_route->getPath()] = 0;
            }

            $default_app = config('plugin.webman.auto-route.app.default_app');

            $suffix = config('app.controller_suffix', '');
            $suffix_length = strlen($suffix);

            // 递归遍历目录查找控制器自动设置路由
            $dir_iterator = new \RecursiveDirectoryIterator(app_path());
            $iterator = new \RecursiveIteratorIterator($dir_iterator);
            foreach ($iterator as $file) {
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

                // 默认应用
                $is_default_app = false;
                if (is_string($default_app) && !empty($default_app)) {
                    $seg = explode('/', $uri_path);
                    if ($seg[1] == $default_app) {
                        $uri_path = str_replace($default_app . '/', '', $uri_path);
                        $is_default_app = true;
                    }
                }

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

                $route = function ($uri, $cb) use ($ignore_list) {
                    if (isset($ignore_list[strtolower($uri)])) {
                        return;
                    }
                    WebmanRoute::any($uri, $cb);
                    if ($uri !== '') {
                        WebmanRoute::any($uri . '/', $cb);
                    }
                    $lower_uri = strtolower($uri);
                    if ($lower_uri !== $uri) {
                        WebmanRoute::any($lower_uri, $cb);
                        WebmanRoute::any($lower_uri . '/', $cb);
                    }
                };

                if(strpos($uri_path,'/') !== false){
                    $temp = explode('/',$uri_path);
                    if($temp){
                        $uri_path = '/'.strtolower(preg_replace('/((?<=[a-z])(?=[A-Z]))/', '_', end($temp)));
                    }
                }

                // 设置路由
                foreach ($methods as $item) {
                    $action = $item->name;
                    if (in_array($action, ['__construct', '__destruct'])) {
                        continue;
                    }
                    // action为index时uri里末尾/index可以省略
                    if ($action === 'index') {
                        // controller也为index时uri里可以省略/index/index
                        if (strtolower(substr($uri_path, -6)) === '/index') {
                            if ($is_default_app) {
                                $route('/', [$class_name, $action]);
                            }
                            $route(substr($uri_path, 0, -6), [$class_name, $action]);
                        }
                        $route($uri_path, [$class_name, $action]);
                        $route($uri_path."/{id:\d+}", [$class_name, $action]);
                    }
                    $route($uri_path.'/'.$action, [$class_name, $action]);

                    self::setParams($item,$route,$uri_path,$action,$class_name);
                }
            }
        }

        private static function setDackouRoute(){
            // 已经设置过路由的uri则忽略
            $routes = WebmanRoute::getRoutes();
            $ignore_list = [];
            foreach ($routes as $tmp_route) {
                $ignore_list[$tmp_route->getPath()] = 0;
            }

            $default_app = config('plugin.webman.auto-route.app.default_app');

            $suffix = config('app.controller_suffix', '');
            $suffix_length = strlen($suffix);

            // 递归遍历目录查找控制器自动设置路由
            $dackou_path = __DIR__ . '/controller';
            $dir_iterator = new \RecursiveDirectoryIterator($dackou_path);
            $iterator = new \RecursiveIteratorIterator($dir_iterator);
            foreach ($iterator as $file) {
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

                // 默认应用
                $is_default_app = false;
                if (is_string($default_app) && !empty($default_app)) {
                    $seg = explode('/', $uri_path);
                    if ($seg[1] == $default_app) {
                        $uri_path = str_replace($default_app . '/', '', $uri_path);
                        $is_default_app = true;
                    }
                }

                // 根据文件路径是被类名
                $class_name = str_replace('/', '\\',substr(substr($file_path, strlen(base_path())), 0, -4));
                [$a,$c] = explode('src\\',$class_name);
                $class_name = '\\dackou\\'.$c;
                // var_dump('class_name: '.$class_name);
                if (!class_exists($class_name)) {
                    echo "Class $class_name not found, skip route for it\n";
                    continue;
                }

                // 通过反射找到这个类的所有共有方法作为action
                $class = new \ReflectionClass($class_name);
                $class_name = $class->name;
                $methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);

                $route = function ($uri, $cb) use ($ignore_list) {
                    if (isset($ignore_list[strtolower($uri)])) {
                        return;
                    }
                    WebmanRoute::any($uri, $cb);
                    if ($uri !== '') {
                        WebmanRoute::any($uri . '/', $cb);
                    }
                    $lower_uri = strtolower($uri);
                    if ($lower_uri !== $uri) {
                        WebmanRoute::any($lower_uri, $cb);
                        WebmanRoute::any($lower_uri . '/', $cb);
                    }
                };

                if(strpos($uri_path,'/') !== false){
                    $temp = explode('/',$uri_path);
                    if($temp){
                        $uri_path = '/'.strtolower(preg_replace('/((?<=[a-z])(?=[A-Z]))/', '_', end($temp)));
                    }
                }

                // 设置路由
                foreach ($methods as $item) {
                    $action = $item->name;
                    if (in_array($action, ['__construct', '__destruct'])) {
                        continue;
                    }
                    // action为index时uri里末尾/index可以省略
                    if ($action === 'index') {
                        // controller也为index时uri里可以省略/index/index
                        if (strtolower(substr($uri_path, -6)) === '/index') {
                            if ($is_default_app) {
                                $route('/', [$class_name, $action]);
                            }
                            $route(substr($uri_path, 0, -6), [$class_name, $action]);
                        }
                        $route($uri_path, [$class_name, $action]);
                        $route($uri_path."/{id:\d+}", [$class_name, $action]);
                    }
                    $route($uri_path.'/'.$action, [$class_name, $action]);

                    self::setParams($item,$route,$uri_path,$action,$class_name);
                }
            }
        }

        private static function setParams($method,$route,$uri_path,$action,$class_name){
            // 获取方法参数列表
            $params = $method->getParameters();
            if($params && isset($params[0]) && $params[0]->name == 'request'){
                \array_shift($params);
            }
            if($params){
                $temp = "";
                foreach($params as $param){
                    $temp .= "/{".$param->name."}";
                }
                if(!empty($temp)){
                    $route($uri_path.'/'.$action.$temp, [$class_name, $action]);
                }
            }
        }
    }
?>