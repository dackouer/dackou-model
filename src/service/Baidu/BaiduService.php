<?php
	namespace dackou\service\Baidu;

	class BaiduService{
		/**
		 * 百度翻译
		 * @param  [type] $content [description]
		 * @param  string $lang    [description]
		 * @return [type]          [description]
		 */
		public function translate($content,$lang = 'zh',$from = 'auto'){
			$appId = 'your_app_id';
		    $secretKey = 'your_secret_key';

		    // 2. 生成签名
	        $salt = mt_rand();                              // 随机数
	        $signStr = $appId . $text . $salt . $secretKey; // 拼接原始字符串
	        $sign = md5($signStr);                          // MD5 加密

	        // 3. 构建 POST 请求
            $url = 'https://fanyi-api.baidu.com/api/trans/vip/translate';
            $postData = [
                'q'     => $content,
                'from'  => $from,
                'to'    => $to,
                'appid' => $appId,
                'salt'  => $salt,
                'sign'  => $sign
            ];

            // 4. 发送 cURL 请求
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);      // 生产环境保持开启
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/x-www-form-urlencoded'
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            // 5. 解析结果
            if ($httpCode !== 200) {
                return ['code' => 1,'msg' => "HTTP 错误: $httpCode"];
            }
            
            $data = json_decode($response, true);
            
            if (isset($data['error_code'])) {
                return ['code' => 1,'error' => $data['error_msg'] ?? "错误码: {$data['error_code']}"];
            }
            
            return [
                'src' => $data['trans_result'][0]['src'] ?? '',
                'dst' => $data['trans_result'][0]['dst'] ?? '',
                'from' => $data['from'] ?? '',
                'to' => $data['to'] ?? ''
            ];
		}
	}
?>