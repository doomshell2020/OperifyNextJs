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

			->setCellValue('A1', 'Sr.No.')
			->setCellValue('B1', 'Student Name')
			->setCellValue('C1', 'Fathers Name')
			->setCellValue('D1', 'Mobile')
			->setCellValue('E1', 'Class')
			->setCellValue('F1', 'Section')
			->setCellValue('G1', 'Subject1')
			->setCellValue('H1', 'Subject2')
			->setCellValue('I1', 'Subject3')
			->setCellValue('J1', 'Subject4')
			->setCellValue('K1', 'Subject5')
			->setCellValue('L1', 'Opt1')
			->setCellValue('M1', 'Opt2')
			->setCellValue('N1', 'Opt3');

			
			
			
			$counter=1;
			          if(isset($student_data) && !empty($student_data)){ 
              foreach($student_data as $i=>$people){
			
	$ii = $i+2;
	
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$ii, $people['enroll']);
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$ii, $people['fname'].' '.$people['middlename'].' '.$people['lname']);
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$ii, $people['fathername']);
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$ii, $people['sms_mobile']);

	$objPHPExcel->getActiveSheet()->setCellValue('E'.$ii, $people["class"]["title"]);
	$objPHPExcel->getActiveSheet()->setCellValue('F'.$ii, $people["section"]["title"]);


	if ($people['comp_sid']) {
		$rt = array();
		$rt = explode(',', $people['comp_sid']);

		$subject = $this->Comman->findsubjectsubs2($rt[0]);

		if($subject['name']){
	   	$objPHPExcel->getActiveSheet()->setCellValue('G'.$ii, $subject['name']); 
		}else{
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$ii, '-'); 
		}
}
if ($people['comp_sid']) {
		$rt = array();
		$rt = explode(',', $people['comp_sid']);

		$subject1 = $this->Comman->findsubjectsubs2($rt[1]);

		if($subject1['name']){
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$ii, $subject1['name']); 
	 }else{
		 $objPHPExcel->getActiveSheet()->setCellValue('H'.$ii, '-'); 
	 }

}

if ($people['comp_sid']) {
		$rt = array();
		$rt = explode(',', $people['comp_sid']);

		$subject2 = $this->Comman->findsubjectsubs2($rt[2]);
		if($subject2['name']){
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$ii, $subject2['name']); 
	 }else{
		 $objPHPExcel->getActiveSheet()->setCellValue('I'.$ii, '-'); 
	 }
}
if ($people['comp_sid']) {
		$rt = array();
		$rt = explode(',', $people['comp_sid']);

		$subject3 = $this->Comman->findsubjectsubs2($rt[3]);

		if($subject3['name']){
			$objPHPExcel->getActiveSheet()->setCellValue('J'.$ii, $subject3['name']); 
	 }else{
		 $objPHPExcel->getActiveSheet()->setCellValue('J'.$ii, '-'); 
	 }
}

if ($people['comp_sid']) {
		$rt = array();
		$rt = explode(',', $people['comp_sid']);

		$subject4 = $this->Comman->findsubjectsubs2($rt[4]);

		if($subject4['name']){
			$objPHPExcel->getActiveSheet()->setCellValue('K'.$ii, $subject4['name']); 
	 }else{
		 $objPHPExcel->getActiveSheet()->setCellValue('K'.$ii, '-'); 
	 }
}

if ($people['opt_sid']) {
	$rts = array();
	$rts = explode(',', $people['opt_sid']);

	$subject5 = $this->Comman->findsubjectsubs2($rts[0]);
	if($subject5['name']){
		$objPHPExcel->getActiveSheet()->setCellValue('L'.$ii, $subject5['name']); 
 }else{
	 $objPHPExcel->getActiveSheet()->setCellValue('L'.$ii, '-'); 
 }
}

if ($people['opt_sid']) {
	$rts = array();
	$rts = explode(',', $people['opt_sid']);

	$subject6 = $this->Comman->findsubjectsubs2($rts[1]);
	if($subject6['name']){
		$objPHPExcel->getActiveSheet()->setCellValue('M'.$ii, $subject6['name']); 
 }else{
	 $objPHPExcel->getActiveSheet()->setCellValue('M'.$ii, '-'); 
 }
}

if ($people['opt_sid']) {
	$rts = array();
	$rts = explode(',', $people['opt_sid']);

	$subject7 = $this->Comman->findsubjectsubs2($rts[2]);
	if($subject7['name']){
		$objPHPExcel->getActiveSheet()->setCellValue('N'.$ii, $subject7['name']); 
 }else{
	 $objPHPExcel->getActiveSheet()->setCellValue('N'.$ii, '-'); 
 }
}

	
	
	

}

}

// Rename sheet
//$objPHPExcel->getActiveSheet()->setTitle('Simple');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel2007)
$filename = "Optional_Subject_Studentslist'.xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename='.$filename);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
?>
