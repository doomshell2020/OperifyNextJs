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
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize( true );
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize( true );
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize( true );
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize( true );
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize( true );
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize( true );
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize( true );
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize( true );
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize( true );
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize( true );
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize( true );
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize( true );
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize( true );
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize( true );
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize( true );
$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize( true );
$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize( true );
$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize( true );
$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setAutoSize( true );
$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setAutoSize( true );
$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setAutoSize( true );
$objPHPExcel->getActiveSheet()->getColumnDimension('V')->setAutoSize( true );
$objPHPExcel->getActiveSheet()->getColumnDimension('W')->setAutoSize( true );
$objPHPExcel->getActiveSheet()->getColumnDimension('X')->setAutoSize( true );
$objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setAutoSize( true );
$objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setAutoSize( true );





$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A1', 'S.No.')
    ->setCellValue('B1', 'Enroll')
    ->setCellValue('C1', 'TC S.No')
    ->setCellValue('D1', 'Academic Year')
    ->setCellValue('E1', 'Name')
    ->setCellValue('F1', 'Father\'s Name')
    ->setCellValue('G1', 'Mother\'s Name')
    ->setCellValue('H1', 'Last Studied Class')
    ->setCellValue('I1', 'Promoted To Class')
    ->setCellValue('J1', 'Application Date')
    ->setCellValue('K1', 'Issue Date')
    ->setCellValue('L1', 'Roll Struck Off Date')
    ->setCellValue('M1', 'General Conduct')
    ->setCellValue('N1', 'Leaving Reason')
    ->setCellValue('O1', 'Working Days')
    ->setCellValue('P1', 'Present Days')
    ->setCellValue('Q1', 'Qualified To Promote')
    ->setCellValue('R1', 'School Last Result')
    ->setCellValue('S1', 'Extra urricular Activities')
    ->setCellValue('T1', 'Achievement Level')
    ->setCellValue('U1', 'NCC Cadet')
    ->setCellValue('V1', 'Remarks')
    ->setCellValue('W1', 'Subjects')
    ->setCellValue('X1', 'FeeSubmittedBy')
    ->setCellValue('Y1', 'Board')
    ->setCellValue('Z1', 'SMS Mobile')
    ->setCellValue('AA1', 'Gender')
    ->setCellValue('AB1', 'Admission Year')
    ->setCellValue('AC1', 'Admission Class')
    ->setCellValue('AD1', 'Admission Date')
;


if (isset($students) && !empty($students)) {
    $ii = 2;
    $count = 1;
    foreach ($students as $data) {
  
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $ii, $count);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $ii, $data['enroll']);
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $ii, $data['bookNo']);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $ii, $data['academicYear']);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $ii, $data['name']);
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $ii, $data['fatherName']);
        $objPHPExcel->getActiveSheet()->setCellValue('G' . $ii, $data['motherName']);
        $objPHPExcel->getActiveSheet()->setCellValue('H' . $ii, $data['lastStudiedClass']);
        $objPHPExcel->getActiveSheet()->setCellValue('I' . $ii, $data['promotedToClass']);
        $objPHPExcel->getActiveSheet()->setCellValue('J' . $ii, $data['applicationDate']);
        $objPHPExcel->getActiveSheet()->setCellValue('K' . $ii, $data['issueDate']);
        $objPHPExcel->getActiveSheet()->setCellValue('L' . $ii, $data['struckOffDate']);
        $objPHPExcel->getActiveSheet()->setCellValue('M' . $ii, $data['generalConduct']);
        $objPHPExcel->getActiveSheet()->setCellValue('N' . $ii, $data['leavingReason']);
        $objPHPExcel->getActiveSheet()->setCellValue('O' . $ii, $data['workingDays']);
        $objPHPExcel->getActiveSheet()->setCellValue('P' . $ii, $data['presentDays']);
        $objPHPExcel->getActiveSheet()->setCellValue('Q' . $ii, $data['qualifiedToPromote']);
        $objPHPExcel->getActiveSheet()->setCellValue('R' . $ii, $data['schoolLastresult']);
        $objPHPExcel->getActiveSheet()->setCellValue('S' . $ii, $data['extraCurricularActivities']);
        $objPHPExcel->getActiveSheet()->setCellValue('T' . $ii, $data['achievementLevel']);
        $objPHPExcel->getActiveSheet()->setCellValue('U' . $ii, $data['NCCcadet']);
        $objPHPExcel->getActiveSheet()->setCellValue('V' . $ii, $data['remarks']);
        $objPHPExcel->getActiveSheet()->setCellValue('W' . $ii, $data['subjects']);
        $objPHPExcel->getActiveSheet()->setCellValue('X' . $ii, $data['fee_submittedby']);
        if($data['board_id']==1){
            $board="CBSE";
            $objPHPExcel->getActiveSheet()->setCellValue('Y' . $ii, $board);

        }else if($data['board_id']==2){
            $board="INTERNATIONAL";
            $objPHPExcel->getActiveSheet()->setCellValue('Y' . $ii, $board);

        }else if($data['board_id']==3){
            $board="INTERNATIONAL";
            $objPHPExcel->getActiveSheet()->setCellValue('Y' . $ii, $board);

        }
        
        $objPHPExcel->getActiveSheet()->setCellValue('Z' . $ii, $data['sms_mobile']);
        $objPHPExcel->getActiveSheet()->setCellValue('AA' . $ii, $data['gender']);
        $objPHPExcel->getActiveSheet()->setCellValue('AB' . $ii, $data['admissionyear']);
        $objPHPExcel->getActiveSheet()->setCellValue('AC' . $ii, $data['admissionclass']);
        $objPHPExcel->getActiveSheet()->setCellValue('AD' . $ii, $data['admissiondate']);

        $count++;
        $ii++;
    
    }} else {
    $objPHPExcel->getActiveSheet()->mergeCells('B2:W2');
    $objPHPExcel->getActiveSheet()->setCellValue('B2', 'No Data Available');
}

// Rename sheet
//$objPHPExcel->getActiveSheet()->setTitle('Simple');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel2007)
$filename = "TC Report(".date('d-M-Y').").xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $filename);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;