<?php
// pr($item);
// die;
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
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(18);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);

$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A1', 'Date')
    ->setCellValue('B1', 'Machine Name')
    ->setCellValue('C1', 'Contract Name')
    ->setCellValue('D1', 'Finished Product')
    ->setCellValue('E1', 'PO NO.')
    ->setCellValue('F1', 'Process Name')
    ->setCellValue('G1', 'Planned Qty(KM)')
    ->setCellValue('H1', 'Day Production(KM)')
    ->setCellValue('I1', 'Night Production(KM)')
    ->setCellValue('J1', 'Total Production(KM)')
    ->setCellValue('K1', 'Reading 8.00AM(Current Day)')
    ->setCellValue('L1', 'Reading 8.00PM(Current Day)')
    ->setCellValue('M1', 'Reading 8.00AM(Next Day)')
    ->setCellValue('N1', 'Total Manpower');


$date = date('d-m-Y');
$ii = 2;
$cnt = 1;

foreach ($item as $detail) {
    $contractname = $this->comman->findcontractname($detail['contract_id']);
    $itemname = $this->comman->getitemname($detail['item_id']);
    $processname = $this->comman->finishedproductprocess($detail['productprocess_id']);
    $totalmanpower = $detail['manpower_night'] + $detail['manpower_day'];

    $objPHPExcel->getActiveSheet()->setCellValue('A' . $ii, date("d-m-Y", strtotime($detail['production_date'])));
    $objPHPExcel->getActiveSheet()->setCellValue('B' . $ii, $detail['machinemaster']['machine_name']);
    $objPHPExcel->getActiveSheet()->setCellValue('C' . $ii, $contractname['title'] . '(' . $contractname['workorder'] . ')');
    $objPHPExcel->getActiveSheet()->setCellValue('D' . $ii, $itemname['item_name']);
    $objPHPExcel->getActiveSheet()->setCellValue('E' . $ii, $detail['po_id']);
    $objPHPExcel->getActiveSheet()->setCellValue('F' . $ii, $processname['process_name']);
    $objPHPExcel->getActiveSheet()->setCellValue('G' . $ii, number_format((float) $detail['plan_qty'], 2, '.', ''));
    $objPHPExcel->getActiveSheet()->setCellValue('H' . $ii, number_format((float) $detail['production_shift_a'], 2, '.', ''));
    $objPHPExcel->getActiveSheet()->setCellValue('I' . $ii, number_format((float) $detail['production_shift_b'], 2, '.', ''));
    $objPHPExcel->getActiveSheet()->setCellValue('J' . $ii, number_format((float) $detail['production_shift_a'] + $detail['production_shift_b'], 2, '.', ''));
    $objPHPExcel->getActiveSheet()->setCellValue('K' . $ii, number_format((float) $detail['reading8am'], 2, '.', ''));
    $objPHPExcel->getActiveSheet()->setCellValue('L' . $ii, number_format((float) $detail['reading8pm'], 2, '.', ''));
    $objPHPExcel->getActiveSheet()->setCellValue('M' . $ii, number_format((float) $detail['nextday8am'], 2, '.', ''));
    $objPHPExcel->getActiveSheet()->setCellValue('N' . $ii, $totalmanpower);

    $ii++;
}

// Rename sheet
//$objPHPExcel->getActiveSheet()->setTitle('Simple');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel2007)
$filename = "Daily_Sheet_" . $date . ".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $filename);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
ob_end_clean();
ob_start();
$objWriter->save('php://output');
exit;