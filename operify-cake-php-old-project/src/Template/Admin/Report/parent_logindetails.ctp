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

$objPHPExcel->setActiveSheetIndex()->setCellValue('A1', 'S.No.');
$objPHPExcel->setActiveSheetIndex()->setCellValue('B1', 'Enroll No.');
$objPHPExcel->setActiveSheetIndex()->setCellValue('C1', 'Student Name');
$objPHPExcel->setActiveSheetIndex()->setCellValue('D1', 'Father Name');
$objPHPExcel->setActiveSheetIndex()->setCellValue('E1', 'Mobile');
$objPHPExcel->setActiveSheetIndex()->setCellValue('F1', 'Password');
$objPHPExcel->setActiveSheetIndex()->setCellValue('G1', 'Class');
$objPHPExcel->setActiveSheetIndex()->setCellValue('H1', 'Section');
$objPHPExcel->setActiveSheetIndex()->setCellValue('I1', 'Academic Year');

$counter = 1;
if (isset($parent_login) && !empty($parent_login)) {
    foreach ($parent_login as $i => $value) { //pr($value); die;          

        $ii = $i + 2;
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $ii, $counter++);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $ii, $value['enroll']);
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $ii, ucfirst(strtolower($value['fname'].''.$value['middlename'].''.$value['lname'])));
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $ii, ucfirst(strtolower($value['fathername'])));
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $ii, $value['mobile']);
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $ii, $value['password']);
        $objPHPExcel->getActiveSheet()->setCellValue('G' . $ii, $value['classtitle']);
        $objPHPExcel->getActiveSheet()->setCellValue('H' . $ii, $value['sectiontitle']);
        $objPHPExcel->getActiveSheet()->setCellValue('I' . $ii, $value['acedmicyear']);
      
    }

}

$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel2007)
$filename = "Parent_Login_Details.xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $filename);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
