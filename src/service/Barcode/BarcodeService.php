<?php
	namespace dackou\service\Barcode;

	use support\Request;
	use Picqer\Barcode\BarcodeGeneratorPNG;
	use Picqer\Barcode\BarcodeGeneratorHTML;
	use Picqer\Barcode\BarcodeGeneratorSVG;
	use Picqer\Barcode\BarcodeGeneratorJPG;

	class BarcodeService{
		public function create(Request $request,$data = []){
			try{
				$content = isset($data['content']) ? $data['content'] : $request->input('content','');
				if(!$content){
					return '条形码内容不能为空';
				}
				$width = isset($data['width']) ? $data['width'] : $request->input('width',strlen($content) * 10 + 40);
				if(!$width || !is_numeric($width) || $width < 0){
					return '无效的条形码宽度';
				}
				$height = isset($data['height']) ? $data['height'] : $request->input('height',80);
				if(!$height || !is_numeric($height) || $height < 0){
					return '无效的条形码高度';
				}
				$filename = isset($data['filename']) ? $data['filename'] : $request->input('filename','barcode');
				$ext = isset($data['ext']) ? $data['ext'] : $request->input('ext','png');
				$url = isset($data['url']) ? $data['url'] : $request->input('url','');
				if($url && !filter_var($url, FILTER_VALIDATE_URL)){
					return '无效的url格式';
				}
				$type = isset($data['type']) ? $data['type'] : $request->input('type','TYPE_CODE_128');


				$options = [
			        'widthFactor' 	  => 2,
	                'height' 		  => $height,
	                'textSize' 		  => 3,
	                'textMargin' 	  => 5,
	                'foregroundColor' => [0, 0, 0],
	                'textColor' 	  => [0, 0, 0],
	                'backgroundColor' => [255, 255, 255],
	                'fontScale'		  => 1,
	                'filename' 		  => public_path() . '/barcode/' . $filename . '.' . $ext
				];

			    // 根据输出类型选择生成器
			    switch (strtolower($ext)) {
			        case 'jpg':
			        case 'jpeg':
			            $generator = new BarcodeGeneratorJPG();
			            break;
			        case 'svg':
			            $generator = new BarcodeGeneratorSVG();
			            break;
			        case 'html':
			            $generator = new BarcodeGeneratorHTML();
			            break;
			        case 'png':
			        default:
			            $generator = new BarcodeGeneratorPNG();
			    }

			    // 生成条形码
			    $barcodeData = $generator->getBarcode(
			        $url,
			        $generator::TYPE_CODE_128,
			        $options['widthFactor'],
			        $options['height'],
			        $options['foregroundColor']
			    );

			    // 创建图像资源
		        $barcodeImg = imagecreatefromstring($barcodeData);
		        $barcodeWidth = imagesx($barcodeImg);
		        $barcodeHeight = imagesy($barcodeImg);

		        // 准备文字（如果没有指定则显示缩短的URL）
	            $displayText = $content ?: shortenUrlForDisplay($url);
			    $font = 5; // 使用GD库内置字体5（最大号内置字体）
			    $textWidth = imagefontwidth($font) * strlen($displayText) * 2;
			    $textHeight = imagefontheight($font);

			    // 创建最终图像（增加文字区域高度）
			    $finalHeight = $barcodeHeight + $textHeight + $options['textMargin'];
			    $finalImg = imagecreatetruecolor($barcodeWidth, $finalHeight);

			    // 设置颜色
			    // 设置颜色
			    $bgColor = imagecolorallocate($finalImg, ...$options['backgroundColor']);
			    $textColor = imagecolorallocate($finalImg, ...$options['textColor']);

			    // 填充背景
			    imagefill($finalImg, 0, 0, $bgColor);

			    // 合并条形码图像
			    imagecopy($finalImg, $barcodeImg, 0, 0, 0, 0, $barcodeWidth, $barcodeHeight);

			    // 添加文字（居中显示）
			    $textX = (int)(($barcodeWidth - $textWidth) / 2);
			    $textY = $barcodeHeight + $options['textMargin'];
			    imagestring($finalImg, $font, $textX, $textY, $displayText, $textColor);

			    // 输出或保存图像
			    if ($options['filename']) {
			        $outputFunction = ($ext === 'jpg') ? 'imagejpeg' : 'imagepng';
			        $outputFunction($finalImg, $options['filename']);
			        $result = 'barcode/' . $filename . '.' . $ext;
			    } else {
			        ob_start();
			        ($ext === 'jpg') ? imagejpeg($finalImg) : imagepng($finalImg);
			        $result = ob_get_clean();
			    }

			    // 释放资源
			    imagedestroy($barcodeImg);
			    imagedestroy($finalImg);

			    return $result;
			}catch(\Exception $e){
				return [
					'code' => $e->getCode() ?? 1,
					'file' => $e->getFile(),
					'line' => $e->getLine(),
					'msg'  => $e->getMessage()
				];
			}
		}
	}
?>