<?php 
$objPHPExcel = new PHPExcel();
// Set properties
$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
							 ->setLastModifiedBy("Maarten Balliauw")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Test result file");
// Miscellaneous glyphs, UTF-8
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);

 
$objPHPExcel->setActiveSheetIndex(0)

			->setCellValue('A1', 'S.No.')
			->setCellValue('B1', 'Form No')
			->setCellValue('C1', 'Pupil Name')
			->setCellValue('D1', 'Mobile')
			->setCellValue('E1', 'Type')
			->setCellValue('F1', 'Class')
			->setCellValue('G1', 'Academic Year')
			->setCellValue('H1', 'Selling Date')
			->setCellValue('I1', 'Status');

			
			
			
			$counter=1;
			          if(isset($enquires) && !empty($enquires)){ 
              foreach($enquires as $i=>$service){
			
	$ii = $i+2;
	
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$ii, $counter++);
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$ii, $service['formno']);
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$ii, $service['s_name']);
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$ii, $service['mobile']);

	$objPHPExcel->getActiveSheet()->setCellValue('E'.$ii, 'Prospectus');
	
	
	
	$objPHPExcel->getActiveSheet()->setCellValue('F'.$ii, $service['class']['title']);
	
	
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$ii, $service['acedmicyear']); 
	$date=date("d-m-Y", strtotime($service['created'] ));
		$objPHPExcel->getActiveSheet()->setCellValue('H'.$ii, $date);
		
		if($service['status']=='Y'){
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$ii, 'Activate'); 
			
		}else{
					$objPHPExcel->getActiveSheet()->setCellValue('I'.$ii, 'Deactivate'); 
			
		}

}

}

// Rename sheet
//$objPHPExcel->getActiveSheet()->setTitle('Simple');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel2007)
$filename = "Prospectus_Record-".date('d-m-Y').".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename='.$filename);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
?>
