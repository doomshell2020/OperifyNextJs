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
    ->setCellValue('A1', 'PO Id')
    ->setCellValue('B1', 'Genrated Date')
    ->setCellValue('C1', 'Vendor')
    ->setCellValue('D1', 'Contact')
    ->setCellValue('E1', 'Email')
    ->setCellValue('F1', 'Quantity')
    ->setCellValue('G1', 'Total Amount (INR)')
    ->setCellValue('H1', 'Delivery Date');


$date = date('d-m-Y');
$ii = 2;
$cnt = 1;

foreach ($podata as $value) {
    $var = $this->Comman->poitemquantity($value['purchaseorder_id'], $value['is_revised'], $value['id']);
    $podetail = $this->Comman->podetail($value['purchaseorder_id'], $value['is_revised'], $value['id']);
    $vendor_id = $this->Comman->findvendornames($value['vendor_id']);

    $revisedvalue = '';
    if ($value['is_revised'] > 0) {
        $revisedvalue = 'R-'.$value['is_revised'];
    }

    $objPHPExcel->getActiveSheet()->setCellValue('A' . $ii, $value['purchaseorder_id'].$revisedvalue );
    $objPHPExcel->getActiveSheet()->setCellValue('B' . $ii, date("d-m-Y", strtotime($value['added_time'])));
    $objPHPExcel->getActiveSheet()->setCellValue('C' . $ii, $vendor_id['name']);
    $objPHPExcel->getActiveSheet()->setCellValue('D' . $ii, $vendor_id['contact_no']);
    $objPHPExcel->getActiveSheet()->setCellValue('E' . $ii, $vendor_id['email']);
    $objPHPExcel->getActiveSheet()->setCellValue('F' . $ii, number_format((float) $value['total_qty'], 2, '.', ''));
    $objPHPExcel->getActiveSheet()->setCellValue('G' . $ii, number_format((float) $value['total_amt'], 2, '.', ''));
    $objPHPExcel->getActiveSheet()->setCellValue('H' . $ii, date("d-m-Y", strtotime($value['delivery_date'])));

    $ii++;
}

// Rename sheet
//$objPHPExcel->getActiveSheet()->setTitle('Simple');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel2007)
$filename = "Export_Summary_Stock_Item-" . $itemname . ".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $filename);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
ob_end_clean();
ob_start();
$objWriter->save('php://output');
exit;