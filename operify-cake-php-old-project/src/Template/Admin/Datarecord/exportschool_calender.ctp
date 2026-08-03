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


$objPHPExcel->setActiveSheetIndex(0)

    ->setCellValue('A1', 'S.No.')
    ->setCellValue('B1', 'Event Type*')
    ->setCellValue('C1', 'Name*')
    ->setCellValue('D1', 'Description*')
    ->setCellValue('E1', 'Start Date*')
    ->setCellValue('F1', 'End Date*');


   

 
    $counter = 1;
if (isset($school) && !empty($school)) {
    foreach ($school as $i => $people) {
    

        $ii = $i + 2;
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $ii, $counter++);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $ii, $people['title']);
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $ii, $people['eventtype']['name']);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $ii, $people['detail']);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $ii, date('Y-m-d', strtotime($people["starttime"])));
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $ii, date('Y-m-d', strtotime($people["endtime"])));

    }

}

$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel2007)
$filename = "School_Calender_Details.xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $filename);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
