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
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(6);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(14);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(14);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(23);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(80);
$objPHPExcel->setActiveSheetIndex(0)
->setCellValue('A1', 'S.No.')
			->setCellValue('B1', 'Sr.No.')
			->setCellValue('C1', 'Pupil Name')
			->setCellValue('D1', 'Father Name')
			->setCellValue('E1', 'SMS Mobile')
			->setCellValue('F1', 'Class')
			->setCellValue('G1', 'Section')
			->setCellValue('H1', 'Template Category')
			->setCellValue('I1', 'SMS Deliverd Date')
			->setCellValue('J1', 'Status')
			->setCellValue('K1', 'SMS Message');
			
			
			$counter=1;
			          if(isset($students) && !empty($students)){ 
              foreach($students as $i=>$work){
			
	$ii = $i+2;
	
	
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$ii, $counter++);
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$ii, $work['Students']['enroll']);
		$name = $work['Students']['fname'].' ';

                if( !empty( $work['Students']['middlename'] ) )
                  $name .= $work['Students']['middlename'].' ';

                 $name .= $work['Students']['lname'];
	$objPHPExcel->getActiveSheet()->setCellValue('C'.$ii, $name);
	
	
	
	$objPHPExcel->getActiveSheet()->setCellValue('D'.$ii, $work['Students']['fathername']);
	
	if($work['smsmobile']){ 
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$ii, $work['smsmobile']); 
		}else{ 
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$ii, $work['Students']['sms_mobile']);
		  }
	
	 $classname=$this->Comman->findclass123($work['Students']['class_id']); 
	 $sectionname=$this->Comman->findsection123($work['Students']['section_id']);
	$objPHPExcel->getActiveSheet()->setCellValue('F'.$ii, $classname['title']);
	$objPHPExcel->getActiveSheet()->setCellValue('G'.$ii, $sectionname['title']);
	$objPHPExcel->getActiveSheet()->setCellValue('H'.$ii, $work['Smsmanager']['category']);
    $objPHPExcel->getActiveSheet()->setCellValue('I'.$ii, date('d-m-Y H:i:s A',strtotime($work['created'])));
     if($work['status']=="Y"){
			$objPHPExcel->getActiveSheet()->setCellValue('J'.$ii, 'Success'); 
	 }else{
			$objPHPExcel->getActiveSheet()->setCellValue('J'.$ii, 'Failure'); 
		 
	 }
    if($work['Smsmanager']['category']=="Absent"){
$name=ucwords(strtolower($name));
	$mesg="Dear Parent, Please be informed that your ward ".$name." is absent from school today.";
$objPHPExcel->getActiveSheet()->setCellValue('K'.$ii, $mesg);
	}else{
		
		$objPHPExcel->getActiveSheet()->setCellValue('K'.$ii, $work['Smsdelivery']['message']);
	}
	

	
	  

}

}

// Rename sheet
//$objPHPExcel->getActiveSheet()->setTitle('Simple');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel2007)
$filename = "ExportSMS_Delivery_Students_".date('d-m-Y',strtotime($students[0]['created'])).".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename='.$filename);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
?>
