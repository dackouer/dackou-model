<?php
	namespace dackou\model\Cert;

	use support\Request;
	use support\Db;
	use dackou\Preg;

	class CertModel extends \dackou\Model{
		protected $table = 'Cert';
		protected $title = '证书';
		protected $option_key = 'cert';
		protected $import_field = [
			'CertCode','Realname','Mobile','Idcard','Jobname','Pic','Workplace','Organization','IssueDate','ExpireDate'
		];

		protected function getSearchList(Request $request, $flag = false){
			$idcard = trim($request->post('idcard',$request->post('idNumber','')));
			$code = trim($request->post('code',$request->post('certificateNumber',$request->post('cert_code',''))));
			$realname = trim($request->post('realname',$request->post('username',$request->post('name',''))));
			
			if(!$idcard){
				return '请输入身份证号查询';
			}
			if(!Preg::isIdcard($idcard)){
				return '身份号格式不正确,请重新输入';
			}
			if($this->checkSensitive($idcard)){
				return '无效的字符输入';
			}
			if($this->checkSensitive($code)){
				return '无效的字符输入';
			}
			if($this->checkSensitive($realname)){
				return '无效的字符输入';
			}

			$field = $this->getList($request,'field');
			$where = $this->getWhere($request);
			array_push($where,['Idcard','=',$idcard]);
			if($code){
				array_push($where,['CertCode','=',$code]);
			}
			if($realname){
				array_push($where,['Realname','=',$realname]);
			}

			$object = Db::table($this->table)
						->select(...$field)
						->where($where)
						->get();
			
			return $object;
		}

		protected function getDownloadList(Request $request,$id = 0){
			$data = $this->getList($request,$id);
			$template = $this->config['cert_draft'];
			$code = $data->cert_code;
			$realname = $data->realname;
			$idcard = $data->idcard;
			$avatar = $data->pic;
			$name = $data->workplace;
			$date = $data->issue_date;
			$is_content = $this->getConfig('cert_is_content','boolean');
			$content = $this->config['cert_content'];
			$h5Data = [
    			'url' 	  => 'https://zs.zlqp.org.cn/#/qrcode?id='.$id,
    			'text' 	  => 'H5二维码',
    			'size'	  => 1024,
    			'margin'  => 10,
    			'logo' 	  => ''
    		];
			$qrcode = $this->createH5Qrcode($request,$h5Data);
			$is_local = ($request->host(true) === 'localhost') ? true : false;
			$options = [
				'x_code' => $is_local ? 460 : 500,    // 证书编号X坐标
		        'y_code' => $is_local ? 370 : 395,    // 证书编号Y坐标
		        'x_realname' => $is_local ? 150 : 180, // 姓名X坐标
		        'y_realname' => $is_local ? 548 : 585,// 姓名Y坐标
		        'x_idcard' => $is_local ? 230 : 260,  // 身份证号X坐标
		        'y_idcard' => $is_local ? 610 : 650,  // 身份证号Y坐标
		        'x_name' => $is_local ? 200 : 230,    // 单位名称X坐标
		        'y_name' => $is_local ? 672 : 720,    // 单位名称Y坐标
		        'x_date' => $is_local ? 550 : 590,    // 日期X坐标
		        'y_date' => $is_local ? 1275 : 1370,   // 日期Y坐标
		        'x_avatar' => $is_local ? 722 : 778,    // 证件照X坐标
		        'y_avatar' => $is_local ? 500 : 560,   // 证件照Y坐标
		        'x_qrcode' => $is_local ? 136 : 150,  // 二维码X坐标
		        'y_qrcode' => $is_local ? 1115 : 1198, // 二维码Y坐标
		        'x_content' => $is_local ? 100 : 115,  // 内容X坐标
		        'y_content' => $is_local ? 850 : 930, // 内容Y坐标
		        'avatar_width' => 175, // 二维码宽度
		        'avatar_height' => 230,// 二维码高度
		        'qrcode_width' => 95, // 二维码宽度
		        'qrcode_height' => 95,// 二维码高度
		        'angle' => 0,       // 文字旋转角度
		        // 自动换行配置
		        'max_width' => null,            // 全局最大宽度
		        'max_width_realname' => null,   // 姓名最大宽度
		        'max_width_name' => 400,        // 单位名称最大宽度（常用）
		        'max_width_other' => null,      // 其他文本最大宽度
		        'line_width' => 800,			// 行宽度
		        'line_height' => 50,			// 行高度
		        'alignment' => 'left',          // 对齐方式：left, center, right
		        'word_wrap' => true,            // 是否启用自动换行
		        'auto_break_chars' => true,     // 是否在字符边界处断行
		        'break_long_words' => false,    // 是否允许截断长单词
			];
			$output = public_path() . '/cert.jpg';

			// $fonts = $this->getCommonFontPaths();
			// var_dump('font: '. __DIR__ . '/font/regular.ttf');
			// 默认字体配置
		    $font = [
		        'path' => __DIR__ . '/font/simhei.ttf', // 默认中文字体路径
		        'size_code' => 20,     // 证书编号字体大小
		        'size_name' => 20,     // 姓名字体大小
		        'size_text' => 20,     // 其他文字字体大小
		        'line_height' => 50,
		        'color' => [0, 0, 0],  // 默认黑色
		    ];
		    // $font = array_merge($defaultFont, $font);



			// 获取图片信息
		    $imageInfo = getimagesize($template);
		    if (!$imageInfo) {
		        throw new Exception("无法读取模板图片");
		    }

			// 根据图片类型创建图像资源
		    $imageType = $imageInfo[2];
		    switch ($imageType) {
		        case IMAGETYPE_JPEG:
		            $image = imagecreatefromjpeg($template);
		            break;
		        case IMAGETYPE_PNG:
		            $image = imagecreatefrompng($template);
		            break;
		        case IMAGETYPE_GIF:
		            $image = imagecreatefromgif($template);
		            break;
		        default:
		            throw new Exception("不支持的图片格式");
		    }
		    
		    if (!$image) {
		        throw new Exception("无法创建图像资源");
		    }

		    $textColor = imagecolorallocate($image, 0,0,0);
		    $useTTF = true;
    
		    // 添加证书编号
		    if ($code && isset($options['x_code']) && isset($options['y_code'])) {
		        if ($useTTF) {
		            imagettftext($image, $font['size_code'], $options['angle'], 
		                $options['x_code'], $options['y_code'], 
		                $textColor, $font['path'], $code);
		        } else {
		            imagestring($image, 5, $options['x_code'], $options['y_code'] - 10, 
		                $code, $textColor);
		        }
		    }
    
		    // 添加姓名
		    if ($realname && isset($options['x_realname']) && isset($options['y_realname'])) {
		        if ($useTTF) {
		            // 处理中文字符
		            $realname = iconv('UTF-8', 'UTF-8', $realname);
		            imagettftext($image, $font['size_name'], $options['angle'], 
		                $options['x_realname'], $options['y_realname'], 
		                $textColor, $font['path'], $realname);
		        } else {
		            imagestring($image, 5, $options['x_realname'], $options['y_realname'] - 10, 
		                $realname, $textColor);
		        }
		    }
		    
		    // 添加身份证号
		    if ($idcard && isset($options['x_idcard']) && isset($options['y_idcard'])) {
		        if ($useTTF) {
		            imagettftext($image, $font['size_text'], $options['angle'], 
		                $options['x_idcard'], $options['y_idcard'], 
		                $textColor, $font['path'], $idcard);
		        } else {
		            imagestring($image, 5, $options['x_idcard'], $options['y_idcard'] - 10, 
		                $idcard, $textColor);
		        }
		    }
		    
		    // 添加单位名称
		    if ($name && isset($options['x_name']) && isset($options['y_name'])) {
		        if ($useTTF) {
		            $name = iconv('UTF-8', 'UTF-8', $name);
		            imagettftext($image, $font['size_text'], $options['angle'], 
		                $options['x_name'], $options['y_name'], 
		                $textColor, $font['path'], $name);
		        } else {
		            imagestring($image, 5, $options['x_name'], $options['y_name'] - 10, 
		                $name, $textColor);
		        }
		    }
		    
		    // 添加内容
		    if ($is_content && $content && isset($options['x_content']) && isset($options['y_content'])) {
		    	$maxWidth = $options['line_width'] ?? 800;
		    	$content = str_replace("</p>","",$content);
		    	$content = str_replace("&nbsp;","",$content);
		    	$content = str_replace('<p style="text-align: left;">',"",$content);
		    	$content = ltrim($content,"<p style=\"text-align: left;\"> &nbsp; &nbsp;");
		    	
		    	var_dump($content);
    	        // 检查文本中是否包含手动换行符
    	        if (strpos($content, "\n") !== false || strpos($content,'<br>') !== false) {
    	            // 手动换行
    	            $lines = strpos($content, "\n") !== false ? explode("\n", $content) : explode('<br>',$content);
    	            var_dump($lines);

    	            foreach($lines as $key => $line){
    	            	$x_content = $key ? $options['x_content'] : ($options['x_content'] + 50);
    	            	$y_content = $options['y_content'] + $options['line_height'] * $key;
    	            	imagettftext($image, 18, $options['angle'],$x_content, $y_content,$textColor, $font['path'], $line);
    	            }
    	            // $this->drawTextLines($image, $lines, $font['path'], $font['size_text'], 
    	            //     $options['x_content'], $options['y_content'], $textColor, 
    	            //     $font['line_height'], $options['alignment'], $options['angle']);
    	        } elseif ($options['word_wrap'] && $maxWidth) {
    	            // 自动换行
    	            $lines = $this->wrapText($content, $font['path'], $font['size_text'], $maxWidth, 
    	                $options['auto_break_chars'], $options['break_long_words']);
    	            $this->drawTextLines($image, $lines, $font['path'], $font['size_text'], 
    	                $options['x_content'], $options['y_content'], $textColor, 
    	                $font['line_height'], $options['alignment'], $options['angle']);
    	        } else {
    	            // 不换行
    	            imagettftext($image, $font['size_text'], $options['angle'], 
    	                $options['x_content'], $options['y_content'], 
    	                $textColor, $font['path'], $content);
    	        }




		        // if ($useTTF) {
		        //     imagettftext($image, $font['size_text'], $options['angle'], 
		        //         $options['x_content'], $options['y_content'], 
		        //         $textColor, $font['path'], $content);
		        // } else {
		        //     imagestring($image, 5, $options['x_content'], $options['y_content'] - 10, 
		        //         $content, $textColor);
		        // }
		    }
		    
		    // 添加颁证日期
		    if ($date && isset($options['x_date']) && isset($options['y_date'])) {
		        if ($useTTF) {
		            imagettftext($image, $font['size_text'], $options['angle'], 
		                $options['x_date'], $options['y_date'], 
		                $textColor, $font['path'], $date);
		        } else {
		            imagestring($image, 5, $options['x_date'], $options['y_date'] - 10, 
		                $date, $textColor);
		        }
		    }
		    
		    // 添加证件照
		    if ($avatar && isset($options['x_avatar']) && isset($options['y_avatar'])) {
		        
		        $avatarInfo = getimagesize($avatar);
		        if ($avatarInfo) {
		            $avatarType = $avatarInfo[2];
		            $avatarImage = null;
		            
		            switch ($avatarType) {
		                case IMAGETYPE_JPEG:
		                    $avatarImage = imagecreatefromjpeg($avatar);
		                    break;
		                case IMAGETYPE_PNG:
		                    $avatarImage = imagecreatefrompng($avatar);
		                    break;
		                case IMAGETYPE_GIF:
		                    $avatarImage = imagecreatefromgif($avatar);
		                    break;
		            }
		            
		            if ($avatarImage) {
		                $qrWidth = $options['avatar_width'] ?? imagesx($avatarImage);
		                $qrHeight = $options['avatar_height'] ?? imagesy($avatarImage);
		                
		                // 将证件照合并到证书图片上
		                imagecopyresampled($image, $avatarImage, 
		                    $options['x_avatar'], $options['y_avatar'], 
		                    0, 0, 
		                    $qrWidth, $qrHeight, 
		                    imagesx($avatarImage), imagesy($avatarImage));
		                
		                imagedestroy($avatarImage);
		            }
		        }
		    }
		    
		    // 添加二维码
		    if ($qrcode && isset($options['x_qrcode']) && isset($options['y_qrcode'])) {
		        if($this->isBase64Image($qrcode)){
		        	// 处理Data URI格式（去掉前面的部分）
				    if (strpos($qrcode, 'base64,') !== false) {
				        $qrcode = substr($qrcode, strpos($qrcode, 'base64,') + 7);
				    }
    
				    // 解码Base64
				    $qrData = base64_decode($qrcode);
				    if ($qrData === false) {
				        error_log("Base64解码失败");
				        return;
				    }
    
				    // 将二进制数据保存到临时文件
				    $tempFile = tempnam(sys_get_temp_dir(), 'qrcode_');
				    file_put_contents($tempFile, $qrData);
    
				    // 从临时文件创建图像
				    $qrImage = $this->createImageFromData($qrData);
				    if (!$qrImage) {
				        // 如果直接创建失败，尝试从临时文件创建
				        $qrImage = $this->createImageFromFile($tempFile);
				    }
    
				    if ($qrImage) {
				        // 获取二维码尺寸
				        $qrWidth = imagesx($qrImage);
				        $qrHeight = imagesy($qrImage);
				        
				        // 使用指定的尺寸或原始尺寸
				        $targetWidth = $options['qrcode_width'] ?? $qrWidth;
				        $targetHeight = $options['qrcode_height'] ?? $qrHeight;
				        
				        // 将二维码合并到证书图片上
				        imagecopyresampled($image, $qrImage, 
				            $options['x_qrcode'], $options['y_qrcode'], 
				            0, 0, 
				            $targetWidth, $targetHeight, 
				            $qrWidth, $qrHeight);
				        
				        imagedestroy($qrImage);
				    }
				    
				    // 删除临时文件
				    if (file_exists($tempFile)) {
				        unlink($tempFile);
				    }


		        }else{
			        $qrcodeInfo = getimagesize($qrcode);
			        if ($qrcodeInfo) {
			            $qrcodeType = $qrcodeInfo[2];
			            $qrImage = null;
			            
			            switch ($qrcodeType) {
			                case IMAGETYPE_JPEG:
			                    $qrImage = imagecreatefromjpeg($qrcode);
			                    break;
			                case IMAGETYPE_PNG:
			                    $qrImage = imagecreatefrompng($qrcode);
			                    break;
			                case IMAGETYPE_GIF:
			                    $qrImage = imagecreatefromgif($qrcode);
			                    break;
			            }
			            
			            if ($qrImage) {
			                $qrWidth = $options['qrcode_width'] ?? imagesx($qrImage);
			                $qrHeight = $options['qrcode_height'] ?? imagesy($qrImage);
			                
			                // 将二维码合并到证书图片上
			                imagecopyresampled($image, $qrImage, 
			                    $options['x_qrcode'], $options['y_qrcode'], 
			                    0, 0, 
			                    $qrWidth, $qrHeight, 
			                    imagesx($qrImage), imagesy($qrImage));
			                
			                imagedestroy($qrImage);
			            }
			        }
			    }
		    }
    
		    // 输出或保存图片
		    if($output){
		    	ob_start();
		    	imagejpeg($image, null, 90);
            	$mime = 'image/jpeg';
            	$imageData = ob_get_clean();
    			imagedestroy($image);

    			$base64 = base64_encode($imageData);

    			return ['code'=>0,'data'=>'data:' . $mime . ';base64,' . $base64];


		    	// 根据扩展名决定保存格式
		    	$extension = strtolower(pathinfo($output, PATHINFO_EXTENSION));
    	        switch ($extension) {
    	            case 'jpg':
    	            case 'jpeg':
    	                imagejpeg($image, $output, 90);
    	                break;
    	            case 'png':
    	                imagepng($image, $output, 9);
    	                break;
    	            case 'gif':
    	                imagegif($image, $output);
    	                break;
    	            default:
    	                imagejpeg($image, $output, 90);
    	        }
    	        
    	        imagedestroy($image);
    	        return true;
		    }else{
		    	// 直接输出到浏览器
		        header('Content-Type: image/jpeg');
		        imagejpeg($image, null, 90);
		        imagedestroy($image);
		        return true;
		    }

		}

		/**
		 * 从二进制数据创建图像资源
		 * @param  [type] $imageData [description]
		 * @return [type]            [description]
		 */
		private function createImageFromData($imageData) {
		    // 使用imagecreatefromstring尝试创建图像
		    $image = @imagecreatefromstring($imageData);
		    if ($image !== false) {
		        return $image;
		    }
		    
		    // 如果失败，尝试根据MIME类型创建
		    $finfo = finfo_open(FILEINFO_MIME_TYPE);
		    $mime = finfo_buffer($finfo, $imageData);
		    finfo_close($finfo);
		    
		    switch ($mime) {
		        case 'image/jpeg':
		        case 'image/jpg':
		            return imagecreatefromstring($imageData);
		        case 'image/png':
		            $image = imagecreatefromstring($imageData);
		            if ($image) {
		                imagesavealpha($image, true);
		                imagealphablending($image, true);
		            }
		            return $image;
		        case 'image/gif':
		            return imagecreatefromstring($imageData);
		        default:
		            return false;
		    }
		}

		/**
		 * 从文件添加二维码
		 * @param [type] $image      [description]
		 * @param [type] $qrcodePath [description]
		 * @param [type] $options    [description]
		 */
		private function addQRCodeFromFile($image, $qrcodePath, $options) {
		    if (!file_exists($qrcodePath)) {
		        return;
		    }
		    
		    $qrImage = createImageFromFile($qrcodePath);
		    if (!$qrImage) {
		        return;
		    }
		    
		    $qrWidth = imagesx($qrImage);
		    $qrHeight = imagesy($qrImage);
		    
		    $targetWidth = $options['qrcode_width'] ?? $qrWidth;
		    $targetHeight = $options['qrcode_height'] ?? $qrHeight;
		    
		    imagecopyresampled(
		        $image, $qrImage,
		        $options['x_qrcode'], $options['y_qrcode'],
		        0, 0,
		        $targetWidth, $targetHeight,
		        $qrWidth, $qrHeight
		    );
		    
		    imagedestroy($qrImage);
		}

		private function drawTextLines($image, $lines, $fontPath, $fontSize, $x, $y, $color, 
                      $lineHeight = 1.5, $alignment = 'left', $angle = 0) {
    
		    if (empty($lines)) {
		        return;
		    }
		    
		    // 计算行高
		    $bbox = imagettfbbox($fontSize, 0, $fontPath, 'Aj');
		    $lineHeightPx = ($bbox[1] - $bbox[7]) * $lineHeight;
		    
		    foreach ($lines as $index => $line) {
		        $currentY = $y + ($index * $lineHeightPx);
		        
		        // 根据对齐方式调整X坐标
		        $currentX = $x;
		        if ($alignment === 'center' || $alignment === 'right') {
		            $bbox = imagettfbbox($fontSize, $angle, $fontPath, $line);
		            $textWidth = $bbox[2] - $bbox[0];
		            
		            if ($alignment === 'center') {
		                $currentX = $x - ($textWidth / 2);
		            } elseif ($alignment === 'right') {
		                $currentX = $x - $textWidth;
		            }
		        }
		        
		        imagettftext($image, $fontSize, $angle, $currentX, $currentY, $color, $fontPath, $line);
		    }
		}

		private function wrapText($text, $fontPath, $fontSize, $maxWidth, $breakAtChar = true, $breakLongWords = false) {
		    $lines = [];
		    $words = [];
		    
		    if (empty($text)) {
		        return [$text];
		    }
		    
		    // 如果不需要换行或最大宽度为0，直接返回
		    if ($maxWidth <= 0) {
		        return [$text];
		    }
		    
		    // 中文文本处理：按字符分割
		    if ($breakAtChar && preg_match('/[\x{4e00}-\x{9fa5}]/u', $text)) {
		        // 中文字符，按字符分割
		        $chars = preg_split('/(?<!^)(?!$)/u', $text);
		        $currentLine = '';
		        
		        foreach ($chars as $char) {
		            $testLine = $currentLine . $char;
		            $bbox = imagettfbbox($fontSize, 0, $fontPath, $testLine);
		            $lineWidth = $bbox[2] - $bbox[0];
		            
		            if ($lineWidth <= $maxWidth) {
		                $currentLine = $testLine;
		            } else {
		                if (!empty($currentLine)) {
		                    $lines[] = $currentLine;
		                }
		                $currentLine = $char;
		            }
		        }
		        
		        if (!empty($currentLine)) {
		            $lines[] = $currentLine;
		        }
		        
		        return $lines;
		    }
		    
		    // 英文文本处理：按单词分割
		    $words = explode(' ', $text);
		    $currentLine = '';
		    
		    foreach ($words as $word) {
		        $testLine = $currentLine === '' ? $word : $currentLine . ' ' . $word;
		        $bbox = imagettfbbox($fontSize, 0, $fontPath, $testLine);
		        $lineWidth = $bbox[2] - $bbox[0];
		        
		        if ($lineWidth <= $maxWidth) {
		            $currentLine = $testLine;
		        } else {
		            if (!empty($currentLine)) {
		                $lines[] = $currentLine;
		            }
		            
		            // 处理单个单词过长的情况
		            $bbox = imagettfbbox($fontSize, 0, $fontPath, $word);
		            $wordWidth = $bbox[2] - $bbox[0];
		            
		            if ($wordWidth > $maxWidth) {
		                if ($breakLongWords) {
		                    // 截断长单词
		                    $splitWord = splitLongWord($word, $fontPath, $fontSize, $maxWidth);
		                    foreach ($splitWord as $part) {
		                        $lines[] = $part;
		                    }
		                    $currentLine = '';
		                } else {
		                    $currentLine = $word;
		                }
		            } else {
		                $currentLine = $word;
		            }
		        }
		    }
		    
		    if (!empty($currentLine)) {
		        $lines[] = $currentLine;
		    }
		    
		    return $lines;
		}


		protected function validate(Request $request,$id = 0,$obj = null){
			$cert_code = trim($request->post('cert_code',''));
			$realname = trim($request->post('realname',''));
			$mobile = trim($request->post('mobile',''));
			$idcard = trim($request->post('idcard',''));
			$jobname = trim($request->post('jobname',''));
			$pic = trim($request->post('pic',''));
			$workplace = trim($request->post('workplace',''));
			$organization = trim($request->post('organization',''));
			$issue_date = trim($request->post('issue_date',''));
			$expire_date = trim($request->post('expire_date',''));
			$file = trim($request->post('file',''));

			if(!$cert_code){
				return '证书编号不能为空';
			}
			if($this->checkExists(['CertCode' => $cert_code],$id)){
				return '证书编号已存在,请重新输入';
			}

			if(!$realname){
				return '姓名不能为空';
			}

			if(!$mobile){
				return '手机号码不能为空';
			}

			if(!Preg::isMobile($mobile)){
				// return '手机号码格式不正确,请重新输入';
			}

			if(!$idcard){
				return '身份证号码不能为空';
			}

			if(!Preg::isIdcard($idcard)){
				// return '身份证号码格式不正确,请重新输入';
			}

			if(!$jobname){
				return '职务名称不能为空';
			}

			if(!$pic){
				return '请上传证件照';
			}

			if(!$workplace){
				return '工作单位不能为空';
			}

			if(!$organization){
				return '培训机构不能为空';
			}

			if(!$issue_date){
				return '颁证日期不能为空';
			}
			if(strpos($issue_date,'-') !== false){
				$temp = explode('-', $issue_date);
				$issue_date = $temp[0].'年'.$temp[1].'月'.$temp[2].'日';
			}

			if(!$expire_date){
				return '过期时间不能为空';
			}
			if(strpos($expire_date,'-') !== false){
				$temp = explode('-', $expire_date);
				$expire_date = $temp[0].'年'.$temp[1].'月'.$temp[2].'日';
			}

			$data['cert_code'] = $cert_code;
			$data['realname'] = $realname;
			$data['mobile'] = $mobile;
			$data['idcard'] = $idcard;
			$data['jobname'] = $jobname;
			$data['pic'] = $pic;
			$data['workplace'] = $workplace;
			$data['organization'] = $organization;
			$data['issue_date'] = $issue_date;
			$data['expire_date'] = $expire_date;
			$data['file'] = $file;

			return $data;
		}
		
		protected function getActionList(Request $request,$id = 0): array
		{
			if($id){
				$data = $this->getList($request,$id);
			}

			$action = [
				['type'=>'input','label'=>'证书编号','prop'=>'cert_code','value'=>$data->cert_code ?? '','required'=>true],
				['type'=>'input','label'=>'姓名','prop'=>'realname','value'=>$data->realname ?? '','required'=>true],
				['type'=>'input','label'=>'手机号码','prop'=>'mobile','value'=>$data->mobile ?? '','required'=>true],
				['type'=>'input','label'=>'身份证号码','prop'=>'idcard','value'=>$data->idcard ?? '','required'=>true],
				['type'=>'input','label'=>'职务名称','prop'=>'jobname','value'=>$data->jobname ?? '','required'=>true],
				['type'=>'upload','label'=>'证件照','prop'=>'pic','value'=>$data->pic ?? '','required'=>true,'attrs'=>$this->getUploadOptions('img')],
				['type'=>'input','label'=>'工作单位','prop'=>'workplace','value'=>$data->workplace ?? '','required'=>true],
				['type'=>'input','label'=>'培训机构','prop'=>'organization','value'=>$data->organization ?? '','required'=>true],
				['type'=>'date','label'=>'颁证日期','prop'=>'issue_date','value'=>$data->issue_date ?? '','attrs'=>['format'=>'YYYY年MM月DD日','value-format'=>'YYYY年MM月DD日'],'required'=>true],
				['type'=>'date','label'=>'过期时间','prop'=>'expire_date','value'=>$data->expire_date ?? '','attrs'=>['format'=>'YYYY年MM月DD日','value-format'=>'YYYY年MM月DD日'],'required'=>true],
				['type'=>'upload','label'=>'证书文件','prop'=>'file','value'=>$data->file ?? '','attrs'=>$this->getUploadOptions('file')],
			];

			return $action;
		}

		protected function getMapList(Request $request): array
		{
			return [
				['type'=>'varchar','label'=>'ID','prop'=>'id'],
				['type'=>'varchar','label'=>'证书编号','prop'=>'cert_code'],
				['type'=>'varchar','label'=>'姓名','prop'=>'realname'],
				['type'=>'varchar','label'=>'手机号码','prop'=>'mobile'],
				['type'=>'varchar','label'=>'职务','prop'=>'jobname'],
				['type'=>'varchar','label'=>'身份证号码','prop'=>'idcard'],
				['type'=>'img','label'=>'证件照','prop'=>'pic'],
				['type'=>'varchar','label'=>'工作单位','prop'=>'workplace'],
				['type'=>'varchar','label'=>'培训机构','prop'=>'organization'],
				['type'=>'varchar','label'=>'颁证日期','prop'=>'issue_date'],
				['type'=>'varchar','label'=>'过期时间','prop'=>'expire_date'],
			];
		}
	}
?>