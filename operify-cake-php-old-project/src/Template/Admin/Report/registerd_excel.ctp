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
$objPHPExcel->setActiveSheetIndex(0)
->setCellValue('A1', 'S.No.')
			->setCellValue('B1', 'Form No')
			->setCellValue('C1', 'Academic Year')
			->setCellValue('D1', 'Pupil Name')
			->setCellValue('E1', 'Father Mobile')
			->setCellValue('F1', 'Mother Mobile')
			->setCellValue('G1', 'Class')
			->setCellValue('H1', 'Board')
			->setCellValue('I1', 'Added On')
			->setCellValue('J1', 'Status');
			
			
			
			$counter=1;
			          if(isset($t_enquiry) && !empty($t_enquiry)){ 
              foreach($t_enquiry as $i=>$people){
			
	$ii = $i+2;
	
	$st1=$people['status'];
	$st2=$people['status_i'];
	$st3=$people['status_r'];
	if($st1=='Y')
{
	$stat="Approved";
}else if($st2=='Y')
{
	$stat="Invited";
}else if($st3=='Y'){
	$stat="Rejected";
}else if($st1=='N' && $st2=='N' && $st3=='N'){
	$stat="Pending";
}

	  $cid=$people["class_id"];
	  $bid=$people["enquire"]["mode1_id"];
	  
	 $class1=$this->Comman->showclasstitle($cid);
	 $bord=$this->Comman->showboardtitle($bid);
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$ii, $counter++);
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$ii, $people["sno"]);
	    $objPHPExcel->getActiveSheet()->setCellValue('C'.$ii, $people["acedmicyear"]);
	    $objPHPExcel->getActiveSheet()->setCellValue('D'.$ii, $people["fname"].' '.$people["middlename"].' '.$people["lname"]);
	    $objPHPExcel->getActiveSheet()->setCellValue('E'.$ii, $people["f_phone"]);
	    $objPHPExcel->getActiveSheet()->setCellValue('F'.$ii, $people["m_phone"]);
	    $objPHPExcel->getActiveSheet()->setCellValue('G'.$ii, $class1["title"]);
	    $objPHPExcel->getActiveSheet()->setCellValue('H'.$ii, $bord["name"]);
	    $objPHPExcel->getActiveSheet()->setCellValue('I'.$ii, date('d-m-Y',strtotime($people["created"])));
	    $objPHPExcel->getActiveSheet()->setCellValue('J'.$ii, $stat);
	
	
	

	
	  

}

}

// Rename sheet
//$objPHPExcel->getActiveSheet()->setTitle('Simple');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel2007)
$filename = "AllRegistrations_Record_Students-".date('d-m-Y').".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename='.$filename);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
?>
