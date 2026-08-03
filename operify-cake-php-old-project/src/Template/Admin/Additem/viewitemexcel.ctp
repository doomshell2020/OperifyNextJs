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
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);

$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A1', 'Unique Id')
    ->setCellValue('B1', 'Product Name')
    ->setCellValue('C1', 'Category')
    ->setCellValue('D1', 'Item Type')
    ->setCellValue('E1', 'UOM')
    ->setCellValue('F1', 'Current Stock');


$date = date('d-m-Y');
$ii = 2;
$cnt = 1;
foreach ($users as $value) {


    $InhandStock = $this->Comman->InhandStock($value['id']);

    $objPHPExcel->getActiveSheet()->setCellValue('A' . $ii, $value['id']);
    $objPHPExcel->getActiveSheet()->setCellValue('B' . $ii, $value['item_name']);
    $objPHPExcel->getActiveSheet()->setCellValue('C' . $ii, Ucfirst($value['itemcategory']['category_name']));
    $objPHPExcel->getActiveSheet()->setCellValue('D' . $ii, Ucfirst($value['itemtype']));
    $objPHPExcel->getActiveSheet()->setCellValue('E' . $ii, $value['measurementunit']['unit_name']);
    $objPHPExcel->getActiveSheet()->setCellValue('F' . $ii, $InhandStock ? $InhandStock : 0);

    $ii++;
}

// Rename sheet
//$objPHPExcel->getActiveSheet()->setTitle('Simple');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel2007)
$filename = "Items_Summary_" . $date . ".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $filename);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
ob_end_clean();
ob_start();
$objWriter->save('php://output');
exit;
