<?php
	namespace dackou\service\System;

	use support\Request;

	class SystemService{
		/**
		 * 重启服务
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function reload(Request $request){
			$path = base_path() . '/start.php';
			if(is_file($path)){
				$output = [];
				$return_var = 0;
				$cmd = "php {$path} reload";
				// var_dump('cmd: '.$cmd);
				\exec($cmd, $output, $return_var);
				return ['output' => $output,'return' => $return_var];
			}
			return false;
		}

		/**
		 * 清空缓存
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function cache(Request $request){
			$path = runtime_path() . '/logs';
			if(is_dir($path)){
				$output = [];
				$return_var = 0;
				$cmd = "rm -rf {$path}/*";
				\exec($cmd, $output, $return_var);
				return ['output' => $output,'return' => $return_var];
			}

			return false;
		}
	}
?>