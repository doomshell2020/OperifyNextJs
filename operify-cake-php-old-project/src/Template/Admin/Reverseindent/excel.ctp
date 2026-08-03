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
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);

$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A1', 'Issued Date')
    ->setCellValue('B1', 'Received Id.')
    ->setCellValue('C1', ' Contract')
    ->setCellValue('D1', 'Finished Product')
    ->setCellValue('E1', 'Machine Name')
    ->setCellValue('F1', 'Raw Material')
    ->setCellValue('G1', 'Received Qty')
    ->setCellValue('H1', 'Received By');


$date = date('d-m-Y');
$ii = 2;
$cnt = 1;


foreach ($reverseindentid as $detail) {
    $contractname = $this->comman->findcontractname($detail['contract_id']);
    $itemname = $this->comman->getitemname($detail['finishedproduct_id']);
    $machineName = $this->comman->getMachineName($detail['machine_id']);
    $indentdetails = $this->comman->reverseindent($detail['reverse_id']);
    foreach ($indentdetails as $value) {
        $rawitemname = $this->comman->getitemname($value['item_id']);

        $objPHPExcel->getActiveSheet()->setCellValue('A' . $ii, date("d-m-Y", strtotime($detail['issue_date'])));
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $ii, $detail['reverse_id'])->getStyle('B' . $ii)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $ii, $contractname['title'] . '(' . $contractname['workorder'] . ')');
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $ii, $itemname['item_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $ii, $machineName['machine_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $ii, $rawitemname['item_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('G' . $ii, $value['quantity']);
        $objPHPExcel->getActiveSheet()->setCellValue('H' . $ii, ucfirst($detail['received_name']));

        $ii++;
    }

}

// Rename sheet
//$objPHPExcel->getActiveSheet()->setTitle('Simple');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel2007)
$filename = "Reverse_Indent_Summary_" . $date . ".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $filename);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
ob_end_clean();
ob_start();
$objWriter->save('php://output');
exit;