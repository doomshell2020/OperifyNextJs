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
$objPHPExcel->getActiveSheet()->getStyle(1)->getFont()->setBold(true);

$objPHPExcel->setActiveSheetIndex()->setCellValue('A1', 'S.No.');
$objPHPExcel->setActiveSheetIndex()->setCellValue('B1', 'Name');
$objPHPExcel->setActiveSheetIndex()->setCellValue('C1', 'Mobile No');
$objPHPExcel->setActiveSheetIndex()->setCellValue('D1', 'Password');
$objPHPExcel->setActiveSheetIndex()->setCellValue('E1', 'Email');


$counter = 1;
if (isset($employees) && !empty($employees)) {
    foreach ($employees as $i => $value) { //pr($value); die;          

        $ii = $i + 2;
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $ii, $counter++);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $ii, ucfirst(strtolower($value['fname'].$value['middlename'].$value['lname'])));
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $ii, $value['mobile']);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $ii, $value['user']['confirm_pass']);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $ii, $value['email']);

       
      
    }

}

$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel2007)
$filename = "Employees_Login_Details.xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $filename);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
