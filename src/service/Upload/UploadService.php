<?php
	namespace dackou\service\Upload;

	use support\Request;
	use support\Response;
    use Tinywan\Storage\Storage;
    use dackou\service\Qiniu\QiniuService;

	class UploadService{
		private static $config = ['default' => 'qiniu'];

		public function __construct(){
			// 初始化。 默认为本地存储：local，阿里云：oss，腾讯云：cos，七牛：qiniu 服务器：server
			$path = config_path('plugin/tinywan/storage/app.php');
			if(is_file($path)){
				$config = include($path);
				if($config && isset($config['storage'])){
					self::$config = $config['storage'];
				}
			}
		}

		/**
		 * 统一上传文件
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		public function upload(Request $request,$type = ''){
			try{
				$type = $type ?: $request->input('type',self::$config['default']);
				
				$_method = "uploadBy" . \ucfirst(\strtolower($type));
				// var_dump('method: '.$_method);
				if(\method_exists($this,$_method)){
					return $this->$_method($request);
				}
				return $this->getErrorMessage(100000);
			}catch(\Exception $e){
				return $this->getExceptionMessage($e);
			}
		}

		private function uploadByChunkLocak(Request $request){
			$action = trim($request->post('action',''));

			if($action == 'init'){
				return [
					'code' => 0,
					'msg'  => 'success',
					'data' => [
						'uploadId' => uniqid()
					]
				];
			}elseif($action == 'chunk'){
				$key = trim($request->post('key',''));
				$chunkIndex = (int)$request->post('chunkIndex',0);
				$partNumber = (int)$request->post('partNumber',1);
				$chunkSize = (int)$request->post('chunkSize',0);
				$fileSize = (int)$request->post('fileSize',0);
				$uploadId = trim($request->post('uploadId',''));
				$file = $request->file('file');

				$ext = $file->getUploadExtension();
				if(!$ext){
					$temp = explode('.',$key);
					$ext = end($temp);
				}
				$uploadName = $file->getUploadName();
				$fileName = rtrim($key,".{$ext}") . "_chunk_" . sprintf("%05d",$partNumber) . '.' . $ext;
				$service = new UploadLocalService();
				$result = UploadLocalService::uploadFile($request,'chunk',$fileName);
				if(count($result) == 1){
					// var_dump($result[0]);
					$result[0]['partNumber'] = $partNumber;
					$result[0]['uploadId'] = $uploadId;
					$result[0]['etag'] = $result[0]['uniqid'] ?? $result[0]['uniqid '];

					return [
						'code' => 0,
						'msg'  => 'success',
						'data' => $result[0]

					];
				}elseif(count($result) > 1){
					return $result;
				}else{
					return $this->getErrorMessage(111101);
				}
			}else{
				$key = trim($request->post('key',''));
				// var_dump('complete key: '.$key);
				$uploadId = trim($request->post('uploadId',''));
				$parts = $request->post('parts',[]);
				if(!is_array($parts)){
					$parts = json_decode($parts,true);
				}
				// var_dump('parts: ',$parts);

				try{
					$outputFile = public_path() . '/chunk/' . $key;
					var_dump('outputFile: '.$outputFile);
					if(!file_exists($outputFile)){
						fopen($outputFile,'w');
					}else{
						unlink($outputFile);
					}
					$output = fopen($outputFile, 'wb');
					foreach($parts as $part){
						$part_path = public_path() . '/chunk/' . $part['path'];
						$chunk = fopen($part_path,'rb');
						\stream_copy_to_stream($chunk, $output);
						fclose($chunk);
						unlink($part_path);
					}
					fclose($output);

					$result = $this->uploadByServer($request,$outputFile);
					var_dump('result: ',$result);
					if(is_array($result) && isset($result['url'])){
						// unlink($outputFile);
						return [
							'code' => 0,
							'msg'  => 'success',
							'data' => $result
						];
					}

					return $result;

					// return [
					// 	'code' => 0,
					// 	'msg'  => 'success',
					// 	'data' => [
					// 		'url'  => 'http://localhost:8989/chunk/' .$key, 
					// 		'prompt' => $key
					// 	]
					// ];
				}catch(\Exception $e){
					return $this->getErrorMessage($e->getMessage());
				}
			}
		}

		private function uploadByUniapp(Request $request){
			// var_dump($request->post());
		    $result = Storage::uploadFile();
		    // var_dump($result);
		    if(count($result) == 1){
		        return $result[0];
		    }elseif(count($result) > 1){
		    	return $result;
		    }else{
		    	return $this->getErrorMessage(111101);
			}
		}

		/**
		 * [uploadByQiniu description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		private function uploadByQiniu(Request $request){
		    Storage::disk('qiniu');
		    $result = Storage::uploadFile();
		    // var_dump($result);
		    if(count($result) == 1){
		        return $result[0];
		    }elseif(count($result) > 1){
		    	return $result;
		    }else{
		    	return $this->getErrorMessage(111101);
			}
		}

		/**
		 * [uploadByLocal description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		private function uploadByLocal(Request $request){
			$service = new UploadLocalService();
			$result = UploadLocalService::uploadFile($request);
			// var_dump('upload result: ',$result);
			if($result && is_array($result)){
				if(count($result) == 1){
					return $result[0];
				}elseif(count($result) > 1){
					return $result;
				}else{
					
					return $this->getErrorMessage(111101);
				}
			}elseif(is_string($result)){
				return $result;
			}

			
			// try{  
			// 	Storage::disk('local');
            //     $result = Storage::uploadFile();
            //     var_dump('get local upload data:',$result);
            //     if(count($result) == 1){
            //         return $result[0];
            //     }elseif(count($result) > 1){
            //     	return $result;
            //     }else{
            //     	return $this->getErrorMessage(111101);
            // 	}
            // }catch(\Exception $e){
            //     return $this->getExceptionMessage($e);
            // }
		}

		/**
		 * [uploadByServer description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		private function uploadByServer(Request $request,$file = ''){
			$serverFile = $file ?: $request->input('file','');
			if(!$serverFile){
				return $this->getErrorMessage('无效的上传文件');
			}
			$result = Storage::disk(Storage::MODE_QINIU, false)->uploadServerFile($serverFile);
			if(count($result) == 1){
                return $result[0];
            }elseif(count($result) > 1){
            	return $result;
            }else{
            	return $this->getErrorMessage(111101);
        	}



			$filename = $request->input('file');
            // 第二个参数要设置为 `false`
            Storage::disk(Storage::MODE_OSS, false);
            // 本地文件绝对路径
            $localFile = empty($this->serverPath) ? runtime_path() . DIRECTORY_SEPARATOR . $filename : $this->serverPath;
            $result = Storage::uploadServerFile($localFile);
            if(count($result) == 1){
                return $result[0];
            }elseif(count($result) > 1){
            	return $result;
            }else{
            	return $this->getErrorMessage(111101);
        	}
		}

		/**
		 * [uploadByCert description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		private function uploadByCert(Request $request){
			$result = UploadLocalService::uploadFile($request);
                
            if($result){
                if(count($result) == 1){
                    return $result[0];
                }elseif(count($result) > 1){
                	return $result;
                }else{
                	return $this->getErrorMessage(111101);
            	}
            }else{
            	return $this->getErrorMessage(111101);
            }
		}

		/**
		 * [uploadByAliyun description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		private function uploadByAliyun(Request $request){
			Storage::disk('oss');
            $result = Storage::uploadFile();
            // var_dump($result);
            if(count($result) == 1){
                return $result[0];
            }elseif(count($result) > 1){
            	return $result;
            }else{
            	return $this->getErrorMessage(111101);
        	}
		}

		/**
		 * [uploadByOss description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		private function uploadByOss(Request $request){
			return $this->uploadByAliyun($request);
		}

		/**
		 * [uploadByWechat description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		private function uploadByWechat(Request $request){
			Storage::disk('cos');
            $result = Storage::uploadFile();
            // var_dump($result);
            if(count($result) == 1){
                return $result[0];
            }elseif(count($result) > 1){
            	return $result;
            }else{
            	return $this->getErrorMessage(111101);
        	}
		}

		/**
		 * [uploadByCos description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		private function uploadByCos(Request $request){
			return $this->uploadByWechat($request);
		}

		/**
		 * [uploadByWeixin description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		private function uploadByWeixin(Request $request){
			return $this->uploadByWechat($request);
		}

		/**
		 * [uploadByTencent description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		private function uploadByTencent(Request $request){
			return $this->uploadByWechat($request);
		}

		/**
		 * [uploadByBase64 description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		private function uploadByBase64(Request $request){
			$base64 = $request->post('base64');
		    // 第一个参数为存储方式。第二个参数为是否本地文件（默认是）
		    Storage::disk(Storage::MODE_OSS, false);
		    $result = Storage::uploadBase64($base64);
		    if(count($result) == 1){
                return $result[0];
            }elseif(count($result) > 1){
            	return $result;
            }else{
            	return $this->getErrorMessage(111101);
        	}
		}

		/**
		 * [uploadByEditor description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		private function uploadByEditor(Request $request){
			Storage::disk(self::$config['default']);
            $result = Storage::uploadFile();
            
            // var_dump($result);
            if(count($result) == 1){
            	return [
                    'errno' => 0,
                    'data'  => [
                        'url'   => $result[0]['url'],
                        'alt'   => $result[0]['origin_name'],
                        'href'  => $result[0]['url']
                    ]
                ];
            }elseif(count($result) > 1){
            	$temp = [];
            	foreach($result as $val){
            		$option = [
            			'errno' => 0,
            			'data'  => [
            			    'url'   => $val['url'],
            			    'alt'   => $val['origin_name'],
            			    'href'  => $val['url']
            			]
            		];
            		array($temp,$option);
            	}
            	return $temp;
            }else{
            	return $this->getErrorMessage(111101);
        	}
		}

		private function uploadByChunk(Request $request){
			$action = trim($request->post('action',''));
			var_dump('action: '.$action);
			$key = trim($request->post('key',''));
			if(!$key){
				return $this->getErrorMessage('文件key不能为空');
			}
			var_dump('key: '.$key);

			if($action == 'init'){
				var_dump('upload chunk init');
				$uploader = new QiniuService(true);
            	return $uploader->initMultipartUpload($key);
			}elseif($action == 'chunk'){
				$file = $request->file();
				if(!$file){
					return $this->getErrorMessage('无效的上传文件');
				}
				$chunkIndex = (int)($request->post('chunkIndex',0));
            	$partNumber = (int)($request->post('partNumber',($chunkIndex + 1)));
            	var_dump("上传第{$chunkIndex}/{$partNumber}个分片");
            	$chunkData = $file['file']->getUploadName();
            	if(!$chunkData){
            		return $this->getErrorMessage('读取文件失败');
            	}
				var_dump('upload chunk upload partNumber: '.$partNumber);

            	$fileName = $file['file']->getUploadName();
            	$uploadId = trim($request->post('uploadId',''));
            	if(!$uploadId){
            		return $this->getErrorMessage('无效的uploadId');
            	}
            	$chunkSize = $request->post('chunkSize');
            	$fileSize = $request->post('fileSize');
            	$uploader = new QiniuService(true);
            	$result = $uploader->uploadChunk($key, $chunkData,$chunkSize, $uploadId, $partNumber);
            	if(!$result || !is_array($result) || (isset($result['code']) && $result['code'])){
            		return $result;
            	}
            	var_dump('upoad chunk result: ',$result);
	            return [
	            	'code' 	   => 0,
	            	'msg'  	   => 'success',
	            	'data'	   => [
	            		'partNumber' => $partNumber,
	            		'etag' 		 => $result['data']['etag'],
	            		'chunkKey' 	 => $key,
	            		'fsize' 	 => $chunkSize,
	            	]
	            ];
			}elseif($action == 'complete'){
				var_dump('upload chunk complete');
				$uploadId = $request->post('uploadId','');
				if(!$uploadId){
					return $this->getErrorMessage('无效的uploadId');
				}
				$parts = $request->post('parts',[]);
				if(!$parts){
					return $this->getErrorMessage('没有分片信息');
				}

				$uploader = new QiniuService(true);
				$result = $uploader->mergeChunks($key, $parts, $uploadId);
				var_dump('upoad chunk complete result: ',$result);
	            return $result;
			}
		}

		/**
		 * [uploadByVideo description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		private function uploadByVideo(Request $request){
			$finalKey = 'video/89045d76daa2101a485c0beff5775582.mp4';
			$chunkKeys = [
				'video/d191b8f3c326330db50833195cbaf9e7.mp4',
				'video/89045d76daa2101a485c0beff5775582.mp4',
			];
			$options = [];

			// $resp = $this->mergeChunks('4c88c5cff64116dbf2c1b109cc432c75',$finalKey,$chunkKeys,$options);


			$service = new QiniuService();
        	$resp = $service->mergeChunks($finalKey, $chunkKeys, $options);
        	var_dump('合并结果：',$resp);
        	if(!$resp && is_array($resp) && isset($resp['success']) && $resp['success']){
        		if(count($result) == 1){
        			$result[0]['poster'] = '';
            		return [
            			'errno' => 0,
            			'data'  => $result[0]
            		];
            	}else{
            		$temp = [];
            		foreach($result as $val){
            			$val['poster'] = '';
            			array_push($temp,['errno'=>0,'data'=>$val]);
            		}

            		return $temp;
            	}
        	}

        	return $resp;
		}

		/**
		 * [uploadByGrace description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		private function uploadByGrace(Request $request){
			// 初始化。 默认为本地存储：local，阿里云：oss，腾讯云：cos，七牛：qiniu
            Storage::disk(self::$config['default']);
            $result = Storage::uploadFile();
            // var_dump($result);
            if(count($result) == 1){
                return [
                	'status' => 'ok',
                	'data' 	 => $result[0]['url'],
                	'result' => $result[0]
                ];
            }elseif(count($result) > 1){
            	$temp = [];
            	foreach($result as $val){
            		$option = [
            			'status'  => 'ok',
                    	'data' 	  => $val['url'],
                    	'result'  => $val
            		];
            		array($temp,$option);
            	}
            	return $temp;
            }else{
            	return $this->getErrorMessage(111101);
        	}
		}

		private function uploadByUmo(Request $request){
			var_dump('umo begin');
			$action = $request->post('action','');

			if($action == 'init'){
				return [
					'code' => 0,
					'msg'  => 'success',
					'data' => [
						'uploadId' => uniqid()
					]
				];
			}elseif($action == 'chunk'){
				$key = trim($request->post('key',''));
				$chunkIndex = (int)$request->post('chunkIndex',0);
				$partNumber = (int)$request->post('partNumber',1);
				$chunkSize = (int)$request->post('chunkSize',0);
				$fileSize = (int)$request->post('fileSize',0);
				$uploadId = trim($request->post('uploadId',''));
				$file = $request->file('file');

				$ext = $file->getUploadExtension();
				if(!$ext){
					$temp = explode('.',$key);
					$ext = end($temp);
				}
				$uploadName = $file->getUploadName();
				$fileName = rtrim($key,".{$ext}") . "_chunk_" . sprintf("%05d",$partNumber) . '.' . $ext;
				$service = new UploadLocalService();
				$result = UploadLocalService::uploadFile($request,'chunk',$fileName);
				if(count($result) == 1){
					// var_dump($result[0]);
					$result[0]['partNumber'] = $partNumber;
					$result[0]['uploadId'] = $uploadId;
					$result[0]['etag'] = $result[0]['uniqid'] ?? $result[0]['uniqid '];

					return [
						'code' => 0,
						'msg'  => 'success',
						'data' => $result[0]

					];
				}elseif(count($result) > 1){
					return $result;
				}else{
					return $this->getErrorMessage(111101);
				}
			}elseif($action == 'complete'){
				$key = trim($request->post('key',''));
				// var_dump('complete key: '.$key);
				$uploadId = trim($request->post('uploadId',''));
				$parts = $request->post('parts',[]);
				if(!is_array($parts)){
					$parts = json_decode($parts,true);
				}
				// var_dump('parts: ',$parts);

				try{
					$outputFile = public_path() . '/chunk/' . $key;
					// var_dump('outputFile: '.$outputFile);
					if(!file_exists($outputFile)){
						fopen($outputFile,'w');
					}else{
						unlink($outputFile);
					}
					$output = fopen($outputFile, 'wb');
					foreach($parts as $part){
						$part_path = public_path() . '/chunk/' . $part['path'];
						$chunk = fopen($part_path,'rb');
						\stream_copy_to_stream($chunk, $output);
						fclose($chunk);
						unlink($part_path);
					}
					fclose($output);

					$result = $this->uploadByServer($request,$outputFile);
					if(is_array($result) && isset($result['url'])){
						unlink($outputFile);
						$result['id'] = isset($result['uniqid ']) ? $result['uniqid '] : (isset($result['unique_id']) ? $result['unique_id'] : '');
						return [
							'code' => 0,
							'msg'  => 'success',
							'data' => $result
						];
					}

					return $result;
				}catch(\Exception $e){
					return $this->getErrorMessage($e->getMessage());
				}
			}else{
				Storage::disk(self::$config['default']);
	            $result = Storage::uploadFile();
	            var_dump('result umo: ',$result);
	            if(count($result) == 1){
	                return [
	                	'code' => 0,
	                	'msg'  => 'success',
	                	'data' => [
		                	'id' 	=> $this->generateId(),
		                	'url' 	=> $result[0]['url']
		                ]
	                ];
	            }elseif(count($result) > 1){
	            	$temp = [];
	            	foreach($result as $key => $val){
	            		$option = [
	            			'id'  => $this->generateId(),
	                    	'url' => $val['url']
	            		];
	            		array($temp,$option);
	            	}
	            	return [
	                	'code' => 0,
	                	'msg'  => 'success',
	            		'data' => $temp
	            	];
	            }else{
	            	return $this->getErrorMessage(111101);
	        	}
	        }
		}

		private function generateId($length = 20){
			$bytes = random_int(10, 20); // 生成10到20个字节的随机数
			$randomString = bin2hex(random_bytes($bytes));
			return $randomString;
		}

		private function getErrorMessage($msg){
			return [
				'code' => 1,
				'msg'  => $msg
			];
		}

		/**
		 * [getExceptionMessage description]
		 * @param  [type] $e [description]
		 * @return [type]    [description]
		 */
		private function getExceptionMessage($e){
			$result = [
				'code' 	=> $e->getCode() ? $e->getCode() : 1,
				'file'	=> $e->getFile(),
				'line'	=> $e->getLine(),
				'msg'	=> $e->getMessage()
			];

			var_dump('upoad error: ',$result);

			return $result;
		}
	}
?>