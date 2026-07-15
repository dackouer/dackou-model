<?php
    namespace dackou\service\Qiniu;

    use Qiniu\Auth;
    use Qiniu\Config;
    use Qiniu\Http\Client;
    use Qiniu\Storage\UploadManager;
    use Qiniu\Storage\BucketManager;
    use Qiniu\Storage\ResumeUploader;

    class QiniuService{
        private $config = [];
        private $accessKey = '';
        private $secretKey = '';
        private $bucket = '';
        private $dirname = '';
        private $auth;
        private $uploadManager;
        private $bucketManager;
        private $host = 'http://up-z2.qiniup.com';

        public function __construct($storage = false){
            $this->setConfig();

            if($storage){
                $this->auth = new Auth($this->accessKey, $this->secretKey);
                $this->uploadManager = new UploadManager();
                $this->bucketManager = new BucketManager($this->auth);
            }
        }

        private function setConfig(){
            $config = config('plugin.tinywan.storage.app');
            if($config && isset($config['storage']['qiniu'])){
                $this->config = $config['storage']['qiniu'];
                $this->accessKey = $config['storage']['qiniu']['accessKey'];
                $this->secretKey = $config['storage']['qiniu']['secretKey'];
                $this->bucket = $config['storage']['qiniu']['bucket'];
                $this->dirname = $config['storage']['qiniu']['dirname'];
            }

        }

        /**
         * [sendSms description]
         * @param  Request $request [description]
         * @param  array   $data    [description]
         * @return [type]           [description]
         */
        public function sendSms(Request $request,$data = []){

        }

        /**
         * 初始化分片上传V2
         * @param  [type] $fileName [description]
         * @return [type]           [description]
         */
        public function initMultipartUpload($key){
            try{
                $encodeKey = $this->base64_urlSafeEncode($key);
                $path = "/buckets/{$this->bucket}/objects/{$encodeKey}/uploads";
                $headers = [
                    'Authorization' => $this->getToken()
                ];

                return $this->post($path,null,$headers);
            }catch(\Exception $e){
                return $this->getExceptionError($e);
            }
        }

        /**
         * 上传分片V2
         * @param  [type] $key        [description]
         * @param  [type] $chunkIndex [description]
         * @param  [type] $chunkData  [description]
         * @param  [type] $chunkSize  [description]
         * @param  [type] $fileSize   [description]
         * @param  [type] $uploadId   [description]
         * @param  [type] $partNumber [description]
         * @return [type]             [description]
         */
        public function uploadChunk($key,$chunkData,$chunkSize,$uploadId,$partNumber){
            try{
                $encodeKey = $this->base64_urlSafeEncode($key);
                $path = "/buckets/{$this->bucket}/objects/{$encodeKey}/uploads/{$uploadId}/{$partNumber}";
                
                $body = $chunkData;

                $headers = [
                    'Authorization'  => $this->getToken(),
                    'Content-Type'   => 'application/octet-stream'
                ];
                
                return $this->put($path,$body,$headers);
            }catch(\Exception $e){
                return $this->getExceptionError($e);
            }
        }

        /**
         * 合并分片V2
         * @param  [type] $key      [description]
         * @param  [type] $parts    [description]
         * @param  [type] $uploadId [description]
         * @return [type]           [description]
         */
        public function mergeChunks($key, $parts, $uploadId){
            try{
                $encodekKey = $this->base64_urlSafeEncode($key);
                $path = "/buckets/{$this->bucket}/objects/{$encodekKey}/uploads/{$uploadId}";
                if(!is_array($parts)){
                    $parts = json_decode($parts,true);
                }
                if(!$parts || !isset($parts[0]['partNumber']) || !isset($parts[0]['etag'])){
                    return [
                        'code' => 1,
                        'msg'  => '无效的切片数据格式'
                    ];
                }

                usort($parts, function($a, $b) {
                    return (int)$a['partNumber'] - (int)$b['partNumber'];
                });

                // var_dump('parts: ',$parts);
                $body = json_encode(['parts'=>$parts]);

                $headers = [
                    'Authorization' => $this->getToken(),
                    'Content-Type'  => 'application/json'
                ];

                $result = $this->post($path,$body,$headers);
                if(!is_array($result) || $result['code']){
                    return $result;
                }

                $resp = $this->abortUpload($key,$uploadId);
                if(!is_array($resp) || $resp['code']){
                    return $resp;
                }

                return $result;
            }catch(\Exception $e){
                return $this->getExceptionError($e);
            }
        }

        /**
         * 终止上传
         * @param  [type] $key      [description]
         * @param  [type] $uploadId [description]
         * @return [type]           [description]
         */
        public function abortUpload($key,$uploadId){
            try{
                $encodeKey = $this->base64_urlSafeEncode($key);
                $path = "/buckets/{$this->bucket}/objects/{$encodeKey}/uploads/{$uploadId}";

                $headers = [
                    'Authorization' => $this->getToken()
                ];

                return $this->del($path,null,$headers);
            }catch(\Exception $e){
                return $this->getExceptionError($e);
            }
        }

        /**
         * 删除文件
         * @param  string $key [description]
         * @return [type]      [description]
         */
        public function deleteFile($key = ''){
            try{
                $result = $this->bucketManager->delete($this->bucket, $key);
                return [
                    'code' => 0,
                    'msg'  => 'success',
                    'data' => $result
                ];
            }catch(\Exception $e){
                return $this->getExceptionError($e);
            }
        }


        /**
         * 获取token
         * @param  [type] $chunkKey [description]
         * @return [type]           [description]
         */
        private function getToken($chunkKey = '',$type = 'upload'){
            $token = '';
            switch($type){
                case 'upload':
                    $token = 'UpToken ' . ($chunkKey ? $this->auth->uploadToken($this->bucket, $chunkKey) : $this->auth->uploadToken($this->bucket));
                    break;
                default:
            }

            return $token;
        }

        /**
         * 生成分片的临时key
         * @param  [type] $finalKey   [description]
         * @param  [type] $partNumber [description]
         * @return [type]             [description]
         */
        private function getChunkKey($finalKey, $partNumber)
        {
            $extension = pathinfo($finalKey, PATHINFO_EXTENSION);
            $baseName = pathinfo($finalKey, PATHINFO_FILENAME);
            // var_dump('key: '.$finalKey.' number: '.$partNumber);
            return $this->dirname . '/' . $baseName . '_part_' . str_pad($partNumber, 5, '0', STR_PAD_LEFT) . 
                   ($extension ? '.' . $extension : '');
        }

        /**
         * 获取headers
         * @param  [type] $url    [description]
         * @param  [type] $body   [description]
         * @param  string $type   [description]
         * @param  string $method [description]
         * @return [type]         [description]
         */
        private function generateQiniuAuth($path,$body = null,$type = 'application/json',$method = 'POST'){
            return $this->auth->authorizationV2($this->host.$path,$method,$body,$type);
        }
    
        /**
         * URL安全的base64编码
         */
        private function base64_urlSafeEncode($data)
        {
            $find = array('+', '/');
            $replace = array('-', '_');
            return str_replace($find, $replace, base64_encode($data));
        }

        /**
         * post
         * @param  [type] $path    [description]
         * @param  [type] $body    [description]
         * @param  [type] $headers [description]
         * @return [type]          [description]
         */
        private function post($path,$body,$headers,$msg = ''){
            $url = $this->host . $path;
            $response = Client::post($url,$body,$headers);

            if(!$response->ok()){
                var_dump($msg.' result: HTTP ' . $response->statusCode . ' body: ' . $response->body);
                return [
                    'code' => 1,
                    'msg'  => $msg . '失败： HTTP ' . $response->statusCode . ' body: ' . $response->body
                ];
            }
            $result = json_decode($response->body, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return [
                    'code' => 1,
                    'msg'  => 'JSON解析失败：' . json_last_error_msg(),
                ];
            }
            var_dump('result ok: ',$result);
            return ['code'=>0,'data'=>$result];
        }

        /**
         * [put description]
         * @param  [type] $path    [description]
         * @param  [type] $body    [description]
         * @param  [type] $headers [description]
         * @param  string $msg     [description]
         * @return [type]          [description]
         */
        private function put($path,$body,$headers,$msg = ''){
            $url = $this->host . $path;
            $response = Client::PUT($url,$body,$headers);
            
            if(!$response->ok()){
                var_dump($msg.' result: HTTP ' . $response->statusCode . ' body: ' . $response->body);
                return [
                    'code' => 1,
                    'msg'  => $msg . '失败： HTTP ' . $response->statusCode . ' body: ' . $response->body
                ];
            }
            $result = json_decode($response->body, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return [
                    'code' => 1,
                    'msg'  => 'JSON解析失败：' . json_last_error_msg(),
                ];
            }
            var_dump('result ok: ',$result);
            return ['code'=>0,'data'=>$result];
        }

        /**
         * get
         * @param  [type] $path    [description]
         * @param  [type] $body    [description]
         * @param  [type] $headers [description]
         * @return [type]          [description]
         */
        private function get($path,$body,$headers,$msg = ''){
            $url = $this->host . $path;
            $response = Client::get($url,$body,$headers);

            if(!$response->ok()){
                var_dump($msg.' result: HTTP ' . $response->statusCode . ' body: ' . $response->body);
                return [
                    'code' => 1,
                    'msg'  => $msg . '失败： HTTP ' . $response->statusCode . ' body: ' . $response->body
                ];
            }
            $result = json_decode($response->body, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return [
                    'code' => 1,
                    'msg'  => 'JSON解析失败：' . json_last_error_msg(),
                ];
            }
            var_dump('result ok: ',$result);
            return ['code'=>0,'data'=>$result];
        }

        /**
         * del
         * @param  [type] $path    [description]
         * @param  [type] $body    [description]
         * @param  [type] $headers [description]
         * @return [type]          [description]
         */
        private function del($path,$body,$headers,$msg = ''){
            $url = $this->host . $path;
            $response = Client::delete($url,$body,$headers);

            if(!$response->ok()){
                var_dump($msg.' result: HTTP ' . $response->statusCode . ' body: ' . $response->body);
                return [
                    'code' => 1,
                    'msg'  => $msg . '失败： HTTP ' . $response->statusCode . ' body: ' . $response->body
                ];
            }
            $result = json_decode($response->body, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return [
                    'code' => 1,
                    'msg'  => 'JSON解析失败：' . json_last_error_msg(),
                ];
            }
            var_dump('result ok: ',$result);
            return ['code'=>0,'data'=>$result];
        }

        /**
         * [getExceptionMessage description]
         * @param  [type] $e [description]
         * @return [type]    [description]
         */
        private function getExceptionError($e){
            $result = [
                'code'  => $e->getCode() ? $e->getCode() : 1,
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
                'msg'   => $e->getMessage()
            ];

            var_dump('upoad error: ',$result);

            return $result;
        }
    }
?>