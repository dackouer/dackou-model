<?php
	namespace dackou\service\Qrcode;

	use support\Request;
	use support\Response;
	use dackou\Preg;

	use Endroid\QrCode\Builder\Builder;
	use Endroid\QrCode\Encoding\Encoding;
	use Endroid\QrCode\ErrorCorrectionLevel;
	use Endroid\QrCode\Label\LabelAlignment;
	use Endroid\QrCode\Label\Font\OpenSans;
	use Endroid\QrCode\RoundBlockSizeMode;
	use Endroid\QrCode\Writer\PngWriter;
	use Endroid\QrCode\Color\Color;

	use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
	use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
	use Endroid\QrCode\Label\Font\NotoSans;
	use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;


	class QrcodeService{
		private $path = '';

		public function __construct(){
			$this->path = runtime_path() .'/qrcode/';
		}

		/**
		 * 生成文本二维码
		 * @param  Request $request [description]
		 * @param  array   $data    [description]
		 * @return [type]           [description]
		 */
		public function create(Request $request,$data = []){
			// 二维码文本
			$content = isset($data['content']) ? trim($data['content']) : trim($request->input('content',''));
			if(empty($content)){
				return 302001;
			}
			// 二维码下方文字
			$text = isset($data['text']) ? $data['text'] : trim($request->input('text',''));
			// 二维码大小
			$size = isset($data['size']) ? $data['size'] : $request->input('size',1024);
			// 二维码外边距
			$margin = isset($data['margin']) ? $data['margin'] : $request->input('margin',10);
			
			// 二维码中间区域logo图片
			$logo = isset($data['logo']) ? $data['logo'] : trim($request->input('logo',''));

			if(!empty($logo)){
				if(!Preg::isUrl($logo) && !file_exists($logo)){
					return 302003;
				}
			}


			try{
				$builder = new Builder(
				    writer: new PngWriter(),
				    writerOptions: [],
				    validateResult: false,
				    data: $content,
				    encoding: new Encoding('UTF-8'),
				    errorCorrectionLevel: ErrorCorrectionLevel::High,
				    size: $size,
				    margin: $margin,
				    roundBlockSizeMode: RoundBlockSizeMode::Margin,
				    logoPath: $logo,
				    logoResizeToWidth: 50,
				    logoPunchoutBackground: true,
				    labelText: $text,
				    labelFont: new OpenSans(20),
				    labelAlignment: LabelAlignment::Center
				);

				$result = $builder->build();

				return $result;
			}catch(\Exception $e){
				// var_dump($e->getMessage());
				return $this->getExceptionError($e);
			}
		}

		/**
		 * 创建url链接二维码
		 * @param  Request $request [description]
		 * @param  array   $data    [description]
		 * @return [type]           [description]
		 */
		public function createUrl(Request $request,$data = []){
			try{
				$content = isset($data['content']) ? $data['content'] : ($request->input('content',''));
				$url = isset($data['url']) ? $data['url'] : ($request->input('url',''));
				if(!$url){
					return 'url地址不能为空';
				}
				$size = isset($data['size']) ? $data['size'] : (isset($data['width']) ? $data['width'] : $request->input('width',1024));
				$margin = isset($data['margin']) ? $data['margin'] : $request->input('margin',10);
				$logo = isset($data['logo']) ? $data['logo'] : ($request->input('logo',''));

				// $builder = new Builder(
				//     writer: new PngWriter(),
				//     writerOptions: [],
				//     validateResult: false,
				//     data: $url,
				//     encoding: new Encoding('UTF-8'),
				//     errorCorrectionLevel: ErrorCorrectionLevel::High,
				//     size: $size,
				//     margin: $margin,
				//     roundBlockSizeMode: RoundBlockSizeMode::Margin,
				//     logoPath: $logo, // Logo文件路径
				//     logoResizeToWidth: 50,
				//     logoPunchoutBackground: true,
			    //     labelText: $content,
			    //     labelFont: new OpenSans(20),
			    //     labelAlignment: LabelAlignment::Center,
				// );

				// url + logo + text
				$builder = new Builder(
				    writer: new PngWriter(),
				    writerOptions: [],
				    validateResult: false,
				    data: $url,
				    encoding: new Encoding('UTF-8'),
				    errorCorrectionLevel: ErrorCorrectionLevel::High,
				    size: $size,
				    margin: $margin,
				    roundBlockSizeMode: RoundBlockSizeMode::Margin,
				    logoPath: $logo,
				    logoResizeToWidth: 50,
				    logoPunchoutBackground: true,
				    labelText: $content,
				    labelFont: new OpenSans(20),
				    labelAlignment: LabelAlignment::Center
				);

				$result = $builder->build();

				// 输出到浏览器
				// header('Content-Type: '.$result->getMimeType());
				// echo $result->getString();
				
				return $result;
			}catch(\Exception $e){
				return $this->getExceptionError($e);
			}
		}

		public function barcode(Request $request,$data = []){
			// 创建长条形图像（模拟条形码）
			$content = isset($data['content']) ? $data['content'] : $request->input('content','');
			$height = isset($data['height']) ? $data['height'] : $request->input('height',80);
			$filename = isset($data['filename']) ? $data['filename'] : $request->input('filename','barcode');
			$ext = isset($data['ext']) ? $data['ext'] : $request->input('ext','png');



		    $width = strlen($content) * 10 + 40; // 根据数据长度调整宽度
		    $height = $height;
		    
		    // 使用QR码库生成基础图像
		    $qrCode = Builder::create()
		        ->data($content)
		        ->size($width)
		        ->margin(0)
		        ->build();
		    
		    $qrImage = imagecreatefromstring($qrCode->getString());
		    
		    // 裁剪为条形码形状（只保留中间一行）
		    $barcodeImage = imagecreatetruecolor($width, $height);
		    $white = imagecolorallocate($barcodeImage, 255, 255, 255);
		    imagefill($barcodeImage, 0, 0, $white);
		    
		    // 复制QR码的一行并垂直拉伸
		    for ($y = 0; $y < $height; $y++) {
		        imagecopy($barcodeImage, $qrImage, 0, $y, 0, (int)($qrCode->getSize() / 2), $width, 1);
		    }
		    
		    // 保存图像
		    $filePath = public_path() . "barcode/{$filename}.{$ext}";
		    var_dump('file: '.$filePath);
		    
		    switch (strtolower($ext)) {
		        case 'jpg':
		        case 'jpeg':
		            imagejpeg($barcodeImage, $filePath, 90);
		            break;
		        case 'png':
		        default:
		            imagepng($barcodeImage, $filePath);
		    }
		    
		    imagedestroy($qrImage);
		    imagedestroy($barcodeImage);
		    
		    return $filePath;
		}
	}
?>