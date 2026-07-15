<?php
	namespace dackou\service\Upload;

	use support\Request;

	class UploadLocalService{
		private static $url = '';
		private static $is_random = true;

		private static function setConfig(Request $request){
			$config = config('plugin.tinywan.storage.app.storage.local') ?? [];
			if(isset($config['domain']) && $config['domain']){
				self::$url = rtrim($config['domain'],'/') . '/';
				if(isset($config['uri']) && $config['uri'] && $config['uri'] !== '/'){
					$config['uri'] = trim($config['uri'],'/') . '/';
					self::$url = self::$url . $config['uri'];
				}
			}else{
				$host = $request->host();
				$http = $request->header('x-forwarded-proto');
				if(!$http){
					$http = (strpos($host,'localhost') !== false || strpos($host,'127') !== false) ? 'http' : 'https';
				}

				self::$url = $http . '://' . $host;
			}

			// var_dump('url: ' . self::$url);
		}

		public static function uploadFile(Request $request,$path = '',$filename = ''){
			self::setConfig($request);
			$file = $request->file('file');
			// var_dump('file: ',$file);
			if(!$file || !$file->isValid()){
				return '无效的文件';
			}
			$err_code = $file->getUploadErrorCode();
			if($err_code){
				return ['code' => 1,'msg' => $err_code]; 
			}
			$path = $path ?: $request->input('path','');
			$origin_name = $file->getUploadName();
			$ext = $file->getUploadExtension();
			if(!$ext){
				$temp = explode('.',$origin_name);
				$ext = end($temp);
			}
			$uniqid = date('Ymd').time().rand(10000,99999);
			$savename = self::getFilename($filename,$origin_name,$uniqid,$ext);


			$type = $file->getUploadMimeType();
			$filename = $file->getUploadName();
			$size = $file->getSize();
			// $path = $file->getPath();
			$temp_path = $file->getRealPath();

			if(in_array($path,['wechat','alipay'])){
				$file_name = config_path() . "/cert/{$path}/{$savename}";
				$url = $file_name;
			}else{
				if($path){
					$file_name = public_path() . "/upload/{$path}/{$savename}";
				}else{
					$file_name = public_path() . "/upload/{$savename}";
				}
				$url = $path ? self::$url . "{$path}/{$savename}" : self::$url . $savename;
			}
			// var_dump('url: '.$url);
			if(is_file($file_name)){
				unlink($file_name);
			}
			
			$result = $file->move($file_name);
			
			if($result){
				return [
					[
						"key" 			=> "file",
				        "origin_name" 	=> $origin_name,
				        "save_name" 	=> $savename,
				        "save_path" 	=> $file_name,
				        "url" 			=> $url,
				        "uniqid " 		=> $uniqid,
				        "size" 			=> $size,
				        "mime_type" 	=> $type,
				        "extension" 	=> $ext
					]
				];
			}

			return false;
		}

		private static function getFilename($filename,$origin_name,$uniqid,$ext){
			if(!$filename){
				if(self::$is_random){
					return $uniqid . '.' .$ext;
				}

				return $origin_name;
			}
			return $filename;
		}
	}
?>