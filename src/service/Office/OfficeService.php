<?php
	namespace dackou\service\Office;

	use support\Request;
	use support\Response;

	use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
	use PhpOffice\PhpSpreadsheet\Reader\Xls;
	use PhpOffice\PhpSpreadsheet\IOFactory;
	use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
	use PhpOffice\PhpSpreadsheet\Cell\DataType;
	use PhpOffice\PhpSpreadsheet\Style\Fill;
	use PhpOffice\PhpSpreadsheet\Style\Color;
	use PhpOffice\PhpSpreadsheet\Style\Alignment;
	use PhpOffice\PhpSpreadsheet\Style\Border;
	use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

	class OfficeService{

		/**
		 * 导出
		 * @param  Request $request [description]
		 * @param  [type]  $head    [description]
		 * @param  [type]  $data    [description]
		 * @param  string  $file    [description]
		 * @param  array   $option  [description]
		 * @param  string  $type    [description]
		 * @return [type]           [description]
		 */
		public static function exported(Request $request,$head,$data,$file='',$option=[],$type='excel'){
			switch(strtolower($type)){
				case '':
				case 'excel':
					return self::exportExcel($request,$head,$data);
				case 'word':
					return self::exportWord($request,$data,$file);
				case 'pdf':
					return self::exportPDF($request,$data,$file);
				default:
					return false;
			}
		}

		/**
		 * 导入
		 * @param  string      $file      [description]
		 * @param  int|integer $sheet     [description]
		 * @param  int|integer $columnCnt [description]
		 * @param  array       &$options  [description]
		 * @return [type]                 [description]
		 */
		public static function imported(string $file = '', int $sheet = 0, int $columnCnt = 0, &$options = []){
			try{
				// 文件转码
				$file = iconv("utf-8", "gb2312", $file);
				if(empty($file) OR !is_file($file)){
					return "文件不存在";
				}

				/** @var Xlsx $objRead */
        		$objRead = IOFactory::createReader('Xlsx');
        		if(!$objRead->canRead($file)){
        			/** @var Xls $objRead */
            		$objRead = IOFactory::createReader('Xls');
            		if(!$objRead->canRead($file)){
            			return "只支持导入Excel文件";
            		}
        		}
        		/* 如果不需要获取特殊操作，则只读内容，可以大幅度提升读取Excel效率 */
        		empty($options) && $objRead->setReadDataOnly(true);
        		/* 建立excel对象 */
        		$obj = $objRead->load($file);
        		/* 获取指定的sheet表 */
        		$currSheet = $obj->getSheet($sheet);
        		if((isset($options['mergeCells']))){
        			/* 读取合并行列 */
        			$options['mergeCells'] = $currSheet->getMergeCells();
        		}
        		if(0 == $columnCnt){
        			/* 取得最大的列号 */
		            $columnH = $currSheet->getHighestColumn();
		            /* 兼容原逻辑，循环时使用的是小于等于 */
		            $columnCnt = Coordinate::columnIndexFromString($columnH);
        		}
        		/* 获取总行数 */
		        $rowCnt = $currSheet->getHighestRow();
		        $data   = [];
		        /* 读取内容 */
        		for($_row=1;$_row<=$rowCnt;$_row++){
        			$isNull = true;
        			for($_column=1;$_column<=$columnCnt;$_column++){
        				$cellName = Coordinate::stringFromColumnIndex($_column);
        				$cellId   = $cellName . $_row;
                		$cell     = $currSheet->getCell($cellId);
                		if(isset($options['format'])){
		                    /* 获取格式 */
		                    $format = $cell->getStyle()->getNumberFormat()->getFormatCode();
		                    /* 记录格式 */
		                    $options['format'][$_row][$cellName] = $format;
		                }
		                if (isset($options['formula'])) {
                            /* 获取公式，公式均为=号开头数据 */
                            $formula = $currSheet->getCell($cellId)->getValue();

                            if (0 === strpos($formula, '=')) {
                                $options['formula'][$cellName . $_row] = $formula;
                            }
                        }
                        if (isset($format) && 'm/d/yyyy' == $format) {
                            /* 日期格式翻转处理 */
                            $cell->getStyle()->getNumberFormat()->setFormatCode('yyyy/mm/dd');
                        }
                        $data[$_row][$cellName] = trim($currSheet->getCell($cellId)->getFormattedValue());
                        if(!empty($data[$_row][$cellName])){
		                    $isNull = false;
		                }
        			}
        			/* 判断是否整行数据为空，是的话删除该行数据 */
		            if ($isNull) {
		                unset($data[$_row]);
		            }
        		}
        		return $data;
			}catch(\Exception $e){
				return self::getExceptionError($e);
			}
		}

		protected static function exportExcel(Request $request,array $head,array $data,$fileName = 'test'){
			try{
				// 1. 创建Spreadsheet实例
			    $spreadsheet = new Spreadsheet();
			    $sheet = $spreadsheet->getActiveSheet();

			    // 2. 设置整个工作表为文本格式
			    $sheet->getParent()->getDefaultStyle()
			        ->getNumberFormat()
			        ->setFormatCode(NumberFormat::FORMAT_TEXT);
			    
			    // 2. 添加表头（第一行）
			    $sheet->fromArray($head, null, 'A1');
			    
			    // 3. 分块处理数据
			    $chunkSize = 1000; // 每次处理1000行
			    $row = 2; // 从第二行开始
			    
			    for ($i = 0; $i < count($data); $i += $chunkSize) {
			        $chunk = array_slice($data, $i, $chunkSize);
			        
			        foreach ($chunk as $item) {
			            $rowData = [];
			            foreach ($head as $title) {
			                $rowData[] = $item[$title] ?? '';
			            }
			            $sheet->fromArray($rowData, null, 'A' . $row);
			            $row++;
			        }
			    }

			    $lastRow = $sheet->getHighestRow();
			    
			    // 4. 自动调整列宽
			    $lastColumn = $sheet->getHighestColumn();
			    if($lastRow > 0){
			    	$range = 'A1:' . $lastColumn . $lastRow;
			        $sheet->getStyle($range)
			            ->getNumberFormat()
			            ->setFormatCode(NumberFormat::FORMAT_TEXT);
			    }

			    for ($col = 'A'; $col <= $lastColumn; $col++) {
			        $sheet->getColumnDimension($col)->setAutoSize(true);
			    }
			    
			    // 5. 创建临时文件
			    // $tempFile = tempnam(sys_get_temp_dir(), 'excel_') . '.xlsx';
			    $tempFile = runtime_path() . '/temp/' . $fileName . '.xlsx';
			    var_dump('tempFile: '.$tempFile);
			    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
			    $writer->save($tempFile);
			    if(!is_file($tempFile)){
			    	return '临时文件不存在';
			    }
			    
			    // 6. 创建响应
			    $response = new Response(200, [
			        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			        'Content-Disposition' => 'attachment; filename="' . $fileName . '.xlsx"',
			        'Content-Length' => filesize($tempFile),
			        'X-Accel-Redirect' => $tempFile
			    ], file_get_contents($tempFile));
			    
			    // 7. 删除临时文件
			    // unlink($tempFile);
			    
			    return $response;
			}catch(\Exception $e){
				return [
					'code' => $e->getCode() ?? 1,
					'file' => $e->getFile(),
					'line' => $e->getLine(),
					'msg'  => $e->getMessage(),
				];
			}
		}

		protected static function exportExcel2(Request $request,array $title,array $data, $name = '', $remark = '', $down = false): bool{
			$spreadsheet = new Spreadsheet();
			$worksheet = $spreadsheet->getActiveSheet();

			// 设置工作表标题名称
			$worksheet->setTitle('sheet1');
			$name = time();
			var_dump($title);
			var_dump($data[0]);
			foreach($title as $key => $val){
				$worksheet->setCellValueByColumnAndRow($key+1,1,$val);
			}
			$row = 2;
			foreach($data as $item){
				$colum = 1;
				foreach($item as $value){
					$worksheet->setCellValueByColumnAndRow($colum,$row,$value);
					$colum++;
				}
				$row++;
			}

	        // $request->header('Content-Type','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	        // $request->header('Content-Disposition','attachment;filename='.$name);
	        // $request->header('Cache-Control','max-age=0');
	        // $request->header('Cache-Control','cache, must-revalidate');
	        // $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
	        // $writer->save('php://output');
	        // $spreadsheet->disconnectWorksheets();
	        // unset($spreadsheet);
			var_dump('path: '.public_path().'/export/'.$name.'.xlsx');
	        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save(public_path().'/export/'.$name.'.xlsx');
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
	        
	        return true;

	        foreach ($data as $index => $row) {
	            for ($j = 0; $j < $colcounter; $j++) {
	                if ($j >= 26) {
	                    $cell = chr(65 + $j / 26 - 1) . chr(65 + $j % 26);
	                } else {
	                    $cell = chr(65 + $j);
	                }
	                if (is_numeric($row[$keys[$j]]) && strlen($row[$keys[$j]]) > 10) {
	                    $sheet->getCell($cell . ($index + 2))->setValueExplicit($row[$keys[$j]], 's');
	                } else {
	                    $sheet->setCellValue($cell . ($index + 2), $row[$keys[$j]]);
	                }
	            }
	        }

	        for ($k = 0; $k < $colcounter; $k++) {
                if ($k >= 26) {
                    $cell = chr(65 + $k / 26 - 1) . chr(65 + $k % 26);
                } else {
                    $cell = chr(65 + $k);
                }
                $sheet->getColumnDimension($cell)->setAutoSize(true);
            }
            if ($remark) {
                $sheet->setCellValue('A' . ($index + 3), $remark);
                $sheet->getStyle('A' . ($index + 3))->getAlignment()->setWrapText(true);
            }

            $name .= '.xlsx';
            if ($down) {
                $request->header('Content-Type','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		        $request->header('Content-Disposition','attachment;filename='.$name);
		        $request->header('Cache-Control','max-age=0');
		        $request->header('Cache-Control','cache, must-revalidate');

                $writer = PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
                $writer->save('php://output');
                $spreadsheet->disconnectWorksheets();
                unset($spreadsheet);
                
            } else {
                $writer = PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
                $writer->save($name);
                $spreadsheet->disconnectWorksheets();
                unset($spreadsheet);
            }
            return true;
		}

		// 导出Word
		protected static function exportWord(Request $request,$data,$head,$file){
			
		}

		// 导出PDF
		protected static function exportPDF(Request $request,$data,$head,$file){
			
		}

		public static function pdf($html,$footer){
			$config = [
				'mode'=>'utf-8',
				'format'=>'A4',
				'useSubstitutions'=>true,
				'useAdobeCJK'=>true,
				'autoScriptToLang'=>true,
				'autoLangToFont'=>true,
				'mgl'=>15,
				'mgr'=>15,
				'mgt'=>16,
				'mgb'=>16,
				'mgh'=>9,
				'mgf'=>9,
				'orientation'=>'P',
				'tempDir'=>APP_PATH . '/upload/tmp'
			];
			$mpdf=new \Mpdf\Mpdf($config);  
		    $mpdf->WriteHTML($html);
		    $mpdf->SetFooter($footer);
		    $mpdf->Output(); 
		}

		// 获取文件格式
		private static function getFiletype($file){
			if($file){
				$temp = explode('.',$file);
				$ext = $temp ? strtolower(end($temp)) : '';
				return ucfirst($ext);
			}
			return '';
		}

		private static function getExceptionError($e){
			return [
				'code' 	=> $e->getCode() ? $e->getCode() : 1,
				'msg'   => $e->getMessage()
			];
		}
	}
?>