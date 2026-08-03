<?php
// pr($podata);die;
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
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);

$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A1', 'Id')
    ->setCellValue('B1', 'Vendor Name')
    ->setCellValue('C1', 'Pan No')
    ->setCellValue('D1', 'Contact')
    ->setCellValue('E1', 'Email')
    ->setCellValue('F1', 'Address')
    ->setCellValue('G1', 'Contact Person')
    ->setCellValue('H1', 'Type');


$date = date('d-m-Y');
$ii = 2;
$cnt = 1;

foreach ($vendors as $value) {
   
    $objPHPExcel->getActiveSheet()->setCellValue('A' . $ii, $value['id']);
    $objPHPExcel->getActiveSheet()->setCellValue('B' . $ii, $value['name']);
    $objPHPExcel->getActiveSheet()->setCellValue('C' . $ii, $value['pancard_number']);
    $objPHPExcel->getActiveSheet()->setCellValue('D' . $ii, $value['contact_no']);
    $objPHPExcel->getActiveSheet()->setCellValue('E' . $ii, $value['email']);
    $objPHPExcel->getActiveSheet()->setCellValue('F' . $ii, $value['address']);
    $objPHPExcel->getActiveSheet()->setCellValue('G' . $ii, $value['contact_person']);
    $objPHPExcel->getActiveSheet()->setCellValue('H' . $ii, $value['type']);

    $ii++;
}

// Rename sheet
//$objPHPExcel->getActiveSheet()->setTitle('Simple');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel2007)
$filename = "Export_Summary_VENDOR-" . $itemname . ".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $filename);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
ob_end_clean();
ob_start();
$objWriter->save('php://output');
exit;