<?php
	namespace dackou\service\Upload;

	use support\Request;

	class UploadLocalService{
		public static function uploadFile(Request $request,$path = ''){
			$root = $path ? $path : $request->input('path','');
			$rename = $request->input('rename',1);

			// var_dump('path: '.$root);

			$file = $request->file('file');
			if(!$file || !$file->isValid()){
				return '无效的文件';
			}

			$ext = $file->getUploadExtension();
			$type = $file->getUploadMimeType();
			$err_code = $file->getUploadErrorCode();
			$filename = $file->getUploadName();
			$size = $file->getSize();
			$path = $file->getPath();
			$temp_path = $file->getRealPath();
			$uniqid = date('Ymd').time().rand(10000,99999);
			$savename = '';

			if($rename){
				$savename = $uniqid . '.'.$ext;
			}

			if(in_array($root,['wechat','alipay'])){
				$file_name = config_path().'/cert/'.$root.'/'. ($rename ? $savename : $filename);
			}else{
				$file_name = public_path().'/'.($root?($root.'/'):''). ($rename ? $savename : $filename);
			}
			// $save_path = $file_name;

			// var_dump('file_name: '.$file_name);

			if(is_file($file_name)){
				unlink($file_name);
			}
			
			$result = $file->move($file_name);
			
			if($result){
				return [
					[
						"key" 			=> "file",
				        "origin_name" 	=> $filename,
				        "save_name" 	=> $rename ? $savename : $filename,
				        "save_path" 	=> $file_name,
				        "url" 			=> "/{$root}/".($rename ? $savename : $filename),
				        "uniqid " 		=> $uniqid,
				        "size" 			=> $size,
				        "mime_type" 	=> $type,
				        "extension" 	=> $ext
					]
				];
			}

			return false;
		}
	}
?>