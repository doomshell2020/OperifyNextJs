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


$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setAutoFilter('B1:H1');

$objPHPExcel->getActiveSheet()->getStyle(1)->getFont()->setBold(true);

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setAutoSize(true);

$objPHPExcel->setActiveSheetIndex()->setCellValue('A1', 'Enroll No.');
$objPHPExcel->setActiveSheetIndex()->setCellValue('B1', 'Student Name');
$objPHPExcel->setActiveSheetIndex()->setCellValue('C1', 'Class-Section');
$objPHPExcel->setActiveSheetIndex()->setCellValue('D1', 'Father');
$objPHPExcel->setActiveSheetIndex()->setCellValue('E1', 'Mobile');
$objPHPExcel->setActiveSheetIndex()->setCellValue('F1', 'Admission Fee');
$objPHPExcel->setActiveSheetIndex()->setCellValue('G1', 'Caution Money');
$objPHPExcel->setActiveSheetIndex()->setCellValue('H1', 'Quarter1');
$objPHPExcel->setActiveSheetIndex()->setCellValue('I1', 'Quarter2');
$objPHPExcel->setActiveSheetIndex()->setCellValue('J1', 'Quarter3');
$objPHPExcel->setActiveSheetIndex()->setCellValue('K1', 'Quarter4');
$objPHPExcel->setActiveSheetIndex()->setCellValue('L1', 'TransportQ1');
$objPHPExcel->setActiveSheetIndex()->setCellValue('M1', 'TransportQ2');
$objPHPExcel->setActiveSheetIndex()->setCellValue('N1', 'TransportQ3');
$objPHPExcel->setActiveSheetIndex()->setCellValue('O1', 'TransportQ4');
$objPHPExcel->setActiveSheetIndex()->setCellValue('P1', 'Total Transport Fee');
$objPHPExcel->setActiveSheetIndex()->setCellValue('Q1', 'Pending Transport Fees');
$objPHPExcel->setActiveSheetIndex()->setCellValue('R1', 'Package(Tution+Add.+Trans.)');
$objPHPExcel->setActiveSheetIndex()->setCellValue('S1', 'Total Deposite Amount');
$objPHPExcel->setActiveSheetIndex()->setCellValue('T1', 'Total Pending Fees');
$objPHPExcel->setActiveSheetIndex()->setCellValue('U1', 'Discount');
// $objPHPExcel->setActiveSheetIndex()->setCellValue('O1', 'TransportFees3');
// $objPHPExcel->setActiveSheetIndex()->setCellValue('P1', 'TransportFees4');



$counter = 1;
$total_deposite = 0;
$discount = 0;
$allrecords =count($student_rec_all)+3;
if (isset($student_rec_all) && !empty($student_rec_all)) {
    foreach ($student_rec_all as $i => $value) { 
        // pr($value);exit;        
        $ii = $i + 2;
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $ii, $value['enrollno']);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $ii, $value['studentname']);
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $ii, $value['classtitle']);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $ii, $value['fathername']);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $ii, $value['mobile']);
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $ii, $value['admissionfee']);
        $objPHPExcel->getActiveSheet()->setCellValue('G' . $ii, $value['caution_money']);
        $objPHPExcel->getActiveSheet()->setCellValue('H' . $ii, $value['qu1_fees']);
        $objPHPExcel->getActiveSheet()->setCellValue('I' . $ii, $value['qu2_fees']);
        $objPHPExcel->getActiveSheet()->setCellValue('J' . $ii, $value['qu3_fees']);
        $objPHPExcel->getActiveSheet()->setCellValue('K' . $ii, $value['qu4_fees']);
        $objPHPExcel->getActiveSheet()->setCellValue('L' . $ii, $value['transport1_fees']);
        $objPHPExcel->getActiveSheet()->setCellValue('M' . $ii, $value['transport2_fees']);
        $objPHPExcel->getActiveSheet()->setCellValue('N' . $ii, $value['transport3_fees']);
        $objPHPExcel->getActiveSheet()->setCellValue('O' . $ii, $value['transport4_fees']);
        $objPHPExcel->getActiveSheet()->setCellValue('P' . $ii, $value['totaltransportfees']);
        $objPHPExcel->getActiveSheet()->setCellValue('Q' . $ii, $value['pending_transport_fees']);
        $objPHPExcel->getActiveSheet()->setCellValue('R' . $ii, $value['totalfees']);
        $objPHPExcel->getActiveSheet()->setCellValue('S' . $ii, $value['total_deposite_amount']);
        $objPHPExcel->getActiveSheet()->setCellValue('T' . $ii, $value['pending_fees']);
        $objPHPExcel->getActiveSheet()->setCellValue('U' . $ii, $value['discount']);
        $tojtal_pendingfees += $value['pending_fees'];
        $total_deposite += $value['total_deposite_amount'];    
        $total_fees += $value['totalfees'];
        $discount +=$value['discount'];
        $counter ++;
    }
    
    $objPHPExcel->getActiveSheet()->getStyle('Q'.$allrecords)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('R'.$allrecords)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('S'.$allrecords)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('T'.$allrecords)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('U'.$allrecords)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->setCellValue('Q'.$allrecords, "Total"); 
    $objPHPExcel->getActiveSheet()->setCellValue('R'.$allrecords, $total_fees);
    $objPHPExcel->getActiveSheet()->setCellValue('S'.$allrecords, $total_deposite);
    $objPHPExcel->getActiveSheet()->setCellValue('T'.$allrecords, $tojtal_pendingfees);
    $objPHPExcel->getActiveSheet()->setCellValue('U'.$allrecords, $discount);
    
}


$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel2007)
$filename = "Fees_Details_".date('d-m-Y').'.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $filename);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
