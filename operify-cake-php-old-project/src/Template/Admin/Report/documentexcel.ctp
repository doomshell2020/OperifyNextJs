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

 
$objPHPExcel->setActiveSheetIndex(0)

			->setCellValue('A1', 'SR.No.')
			->setCellValue('B1', 'Student Name')
			->setCellValue('C1', 'Student Name')
			->setCellValue('D1', 'Class')
			->setCellValue('E1', 'Section')
			->setCellValue('F1', 'Category')
			->setCellValue('G1', 'Document Detail')
			->setCellValue('H1', 'Submitted Date');

			
			
			
			$counter=1;
			          if(isset($students) && !empty($students)){ 
              foreach($students as $i=>$work){
			
	$ii = $i+2;
	
	
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$ii, $work['student']['enroll']);
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$ii, $work['student']['fname'].' '.$work['student']['middlename'].' '.$work['student']['lname']);
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$ii, $work['student']['fathername']);
	$classname=$this->Comman->findclass123($work['student']['class_id']);
	$sectionname=$this->Comman->findsection123($work['student']['section_id']);
	$objPHPExcel->getActiveSheet()->setCellValue('D'.$ii, $classname['title']);
	
	
	
	$objPHPExcel->getActiveSheet()->setCellValue('E'.$ii, $sectionname['title']);
	
	
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$ii, $work['documentcategory']['categoryname']); 
	
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$ii, $work['description']);
	
	

	$objPHPExcel->getActiveSheet()->setCellValue('H'.$ii, date('d-m-Y',strtotime($work['created'])));

  
	

	
	  

}

}

// Rename sheet
//$objPHPExcel->getActiveSheet()->setTitle('Simple');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel2007)
$filename = "Document_Report.xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename='.$filename);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
?>
