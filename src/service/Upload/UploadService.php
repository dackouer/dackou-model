<?php
	namespace dackou\service\Upload;

	use support\Request;
    use Tinywan\Storage\Storage;

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
		 * [upload description]
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
				return 100000;
			}catch(\Exception $e){
				return $this->getErrorMessage($e);
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
		    	return 111101;
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
			if(count($result) == 1){
				return $result[0];
			}elseif(count($result) > 1){
				return $result;
			}else{
				return 111101;
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
            //     	return 111101;
            // 	}
            // }catch(\Exception $e){
            //     return $this->getErrorMessage($e);
            // }
		}

		/**
		 * [uploadByServer description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		private function uploadByServer(Request $request){
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
            	return 111101;
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
                	return 111101;
            	}
            }else{
            	return 111101;
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
            	return 111101;
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
            	return 111101;
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
            	return 111101;
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
            	return 111101;
        	}
		}

		/**
		 * [uploadByVideo description]
		 * @param  Request $request [description]
		 * @return [type]           [description]
		 */
		private function uploadByVideo(Request $request){
			Storage::disk(self::$config['default']);
            $result = Storage::uploadFile();
            // var_dump($result);
            if(count($result) == 1){
            	return [
                    'errno' => 0,
                    'data'  => [
                        'url'     => $result[0]['url'],
                        'poster'  => ''
                    ]
                ];
            }elseif(count($result) > 1){
            	$temp = [];
            	foreach($result as $val){
            		$option = [
            			'errno' => 0,
            			'data'  => [
            			    'url'     => $val['url'],
            			    'poster'  => ''
            			]
            		];
            		array($temp,$option);
            	}
            	return $temp;
            }else{
            	return 111101;
        	}
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
            	return 111101;
        	}
		}

		private function uploadByUmo(Request $request){
			Storage::disk(self::$config['default']);
            $result = Storage::uploadFile();
            var_dump('result umo: ',$result);
            if(count($result) == 1){
                return [
                	'id' 	=> $this->generateId(),
                	'url' 	=> $result[0]['url']
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
            	return $temp;
            }else{
            	return 111101;
        	}
		}

		private function generateId($length = 20){
			$bytes = random_int(10, 20); // 生成10到20个字节的随机数
			$randomString = bin2hex(random_bytes($bytes));
			return $randomString;
		}

		/**
		 * [getErrorMessage description]
		 * @param  [type] $e [description]
		 * @return [type]    [description]
		 */
		private function getErrorMessage($e){
			return [
				'code' 	=> $e->getCode() ? $e->getCode() : 1,
				'file'	=> $e->getFile(),
				'line'	=> $e->getLine(),
				'msg'	=> $e->getMessage()
			];
		}
	}
?>