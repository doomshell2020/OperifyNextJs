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
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);

$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A1', 'PO NO.')
    ->setCellValue('B1', 'Date Created')
    ->setCellValue('C1', 'Contract Name')
    ->setCellValue('D1', 'Product')
    ->setCellValue('E1', 'Start Date')
    ->setCellValue('F1', 'End Date')
    ->setCellValue('G1', 'Planned Qty')
    ->setCellValue('H1', 'Prepared Qty')
    ->setCellValue('I1', 'Pending Qty')
    ->setCellValue('J1', 'Status');


$date = date('d-m-Y');
$ii = 2;
$cnt = 1;

foreach ($productionorder as $detail) {
    $contractname = $this->comman->findcontractname($detail['contract_id']);
    $itemname = $this->comman->getitemname($detail['item_id']);
    $checkdailysheet = $this->comman->checkdailysheet($detail['po_id'], 8);

    $quantity = '';
    $completedate = '';
    foreach ($checkdailysheet as $value) {
        $quantity += $value['production_shift_a'] + $value['production_shift_b'];
        $completedate = date('d-m-Y', strtotime($value['production_date']));
    }

    $status = '';
    $cdate = '';
    if ($detail['status'] == 'C') {
        $status = 'Close';
    }else{
        $status = 'Open';
    }

    $objPHPExcel->getActiveSheet()->setCellValue('A' . $ii, $detail['po_id'] );
    $objPHPExcel->getActiveSheet()->setCellValue('B' . $ii, date("d-m-Y", strtotime($detail['issuedate'])));
    $objPHPExcel->getActiveSheet()->setCellValue('C' . $ii, $contractname['title'] . '(' . $contractname['workorder'] . ')');
    $objPHPExcel->getActiveSheet()->setCellValue('D' . $ii, $itemname['item_name']);
    $objPHPExcel->getActiveSheet()->setCellValue('E' . $ii, date("d-m-Y", strtotime($detail['startdate'])));
    $objPHPExcel->getActiveSheet()->setCellValue('F' . $ii, date("d-m-Y", strtotime($detail['enddate'])).$cdate);
    $objPHPExcel->getActiveSheet()->setCellValue('G' . $ii, number_format((float) $detail['plannedqty'], 2, '.', ''));
    $objPHPExcel->getActiveSheet()->setCellValue('H' . $ii, number_format((float) $quantity, 2, '.', ''));
    $objPHPExcel->getActiveSheet()->setCellValue('I' . $ii, number_format((float) $detail['plannedqty'] - $quantity, 2, '.', ''));
    $objPHPExcel->getActiveSheet()->setCellValue('J' . $ii, $status);

    $ii++;
}

// Rename sheet
//$objPHPExcel->getActiveSheet()->setTitle('Simple');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel2007)
$filename = "Production_order_" . $date . ".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $filename);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
ob_end_clean();
ob_start();
$objWriter->save('php://output');
exit;