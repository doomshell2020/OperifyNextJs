<?php
// pr($goodsreceived);die;
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
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);

$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A1', 'S.No.')
    ->setCellValue('B1', 'GRN No.')
    ->setCellValue('C1', 'PO No.')
    ->setCellValue('D1', 'Vendor')
	->setCellValue('E1', 'GRN Inward Date')
	->setCellValue('F1', 'Total Amount');


$date = date('d-m-Y');
$ii = 2;
$cnt=1;

foreach($goodsreceived as $value){
    $vendor_id = $this->Comman->findvendornames($value['vendor_id']);
    // pr($vendor_id);die;

    $objPHPExcel->getActiveSheet()->setCellValue('A' . $ii, $cnt++);
	$objPHPExcel->getActiveSheet()->setCellValue('B' . $ii, $value['id']);
    $objPHPExcel->getActiveSheet()->setCellValue('C' . $ii, $value['purchaseorder_id']);
    $objPHPExcel->getActiveSheet()->setCellValue('D' . $ii, $vendor_id['name']);
    $objPHPExcel->getActiveSheet()->setCellValue('E' . $ii, date("d-m-Y", strtotime($value['inwarddate'])));
    $objPHPExcel->getActiveSheet()->setCellValue('F' . $ii, number_format((float) $value['total_amt'], 2, '.', ''));

	$ii++;
} 

// Rename sheet
//$objPHPExcel->getActiveSheet()->setTitle('Simple');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel2007)
$filename = "Vendors_Summary".$date.".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $filename);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
ob_end_clean();
ob_start();
$objWriter->save('php://output');
exit;