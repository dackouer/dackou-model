<?php
	namespace dackou\service\Download;

	use support\Request;
	use support\Response;
	use GuzzleHttp\Client;

	class DownloadService{
		public function down(Request $request){
			$url = trim($request->input('url',$request->input('file','')));
			if(!$url){
				return '无效的下载地址';
			}

			$filename = trim($request->input('filename',$request->input('name','')));
			if(!$filename){
				$temp = explode('/',$url);
				$filename = end($temp);
			}
			// var_dump('filename: '.$filename);
			$ext = trim($request->input('ext',''));
			if(!$ext){
				$temp = explode('.',$filename);
				$ext = end($temp);
			}

			if(strtolower(substr($url,0,4)) === 'http'){
				return $this->downRemote($request,$url,$filename,$ext);
			}
			return response()->download($url, $filename);
		}

		/**
		 * 下载远程文件
		 * @param  Request $request  [description]
		 * @param  string  $url      [description]
		 * @param  string  $filename [description]
		 * @param  string  $ext      [description]
		 * @return [type]            [description]
		 */
		public function downRemote(Request $request,$url = '',$filename = '',$ext = ''){
			try{
				$client = new Client([
	                'timeout' => 30,
	                'verify' => false,
	                'stream' => true // 使用流式传输
	            ]);

				ob_start();
				$response = $client->get($url);
				// 获取响应信息
	            $contentType = $response->getHeaderLine('Content-Type');
	            $contentLength = $response->getHeaderLine('Content-Length');

	            // 清理输出缓冲区
            	ob_clean();

	            // 创建响应
                $downloadResponse = response($response->getBody())
                    ->withHeaders([
                        'Content-Type' => $contentType,
                        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                        'Content-Length' => $contentLength,
                        'Cache-Control' => 'no-cache, no-store, must-revalidate',
                        'Pragma' => 'no-cache',
                        'Expires' => '0',
                    ]);

                return $downloadResponse;
			}catch(\Exception $e){
				return [
					'code' => $e->getCode() ?: 1,
					'msg'  => $e->getMessage()
				];
			}
		}
	}
?>