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
$objPHPExcel->setActiveSheetIndex()->setCellValue('B1', 'Requisition No.');
// $objPHPExcel->setActiveSheetIndex()->setCellValue('C1', 'School');
$objPHPExcel->setActiveSheetIndex()->setCellValue('C1', 'Branch Name');
$objPHPExcel->setActiveSheetIndex()->setCellValue('D1', 'Description');
$objPHPExcel->setActiveSheetIndex()->setCellValue('E1', 'Remark');
$objPHPExcel->setActiveSheetIndex()->setCellValue('F1', 'Status');
$objPHPExcel->setActiveSheetIndex()->setCellValue('G1', 'Requisition Date');

$counter = 1;
if (isset($sold_data_ho) && !empty($sold_data_ho)) {
    foreach ($sold_data_ho as $i => $value) { //pr($value); die;          
        $school = explode("_",$value['branch_name']); 
        // pr($school); die;
   
        $ii = $i + 2;
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $ii, $counter++);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $ii, $value['req_no']);
        // $objPHPExcel->getActiveSheet()->setCellValue('C' . $ii, "Canvas International Pre School (".ucfirst($school[1]). ") Unit Of Ingenious Edu Scholars Private Limited");
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $ii, ucfirst($school[1]));
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $ii, $value['description']);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $ii, $value['remark']);
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $ii, $value['status']);
        $objPHPExcel->getActiveSheet()->setCellValue('G' . $ii, date('Y-m-d', strtotime($value['approved_date'])));
    }

}

$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel2007)
$filename = "Daily_Solditem_Collection.xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $filename);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
