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
// $objPHPExcel->getActiveSheet()->getStyle('A:C')->getAlignment()
//     ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

$objPHPExcel->setActiveSheetIndex()->setCellValue('A1', 'S.No.');
$objPHPExcel->setActiveSheetIndex()->setCellValue('B1', 'Item ID');
$objPHPExcel->setActiveSheetIndex()->setCellValue('C1', 'Item Name.');
$objPHPExcel->setActiveSheetIndex()->setCellValue('D1', 'Opening Stock');
$objPHPExcel->setActiveSheetIndex()->setCellValue('E1', 'Goods  Receive');
$objPHPExcel->setActiveSheetIndex()->setCellValue('F1', 'Return  Stock');
//$objPHPExcel->setActiveSheetIndex()->setCellValue('D1', 'Stock Available');
$objPHPExcel->setActiveSheetIndex()->setCellValue('G1', 'Sold Stock');
$objPHPExcel->setActiveSheetIndex()->setCellValue('H1', 'Purchase Return Stock');
$objPHPExcel->setActiveSheetIndex()->setCellValue('I1', 'Current Stock');



$counter = 1;

if (isset($stock_report) && !empty($stock_report)) {
    foreach ($stock_report as $i => $value) { //pr($value);  die;        
  
        $totalstock = $value['opening_stock']+$value['gr_stock'];
        $current_stock = $totalstock-$value['stock_sold'];
        $final_stock = $current_stock - $value['sale_return'];
        $final_result = $final_stock - $value['purchase_return'];
        $ii = $i + 2;
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $ii, $counter++);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $ii, $value['item_id']);
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $ii, $value['item_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $ii, $value['opening_stock']);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $ii, $value['gr_stock']);
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $ii, $value['sale_return']);
        $objPHPExcel->getActiveSheet()->setCellValue('G' . $ii, $value['stock_sold']);
        $objPHPExcel->getActiveSheet()->setCellValue('H' . $ii, $value['purchase_return']);
        $objPHPExcel->getActiveSheet()->setCellValue('I' . $ii, $final_result);

    }
   // die;  
}

$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel2007)
$filename = "Daily_Stock_Report_stock_register.xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $filename);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
