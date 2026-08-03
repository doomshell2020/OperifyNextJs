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
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);


$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A1', 'S.No.')
    ->setCellValue('B1', 'GRN No.')
    ->setCellValue('C1', 'PO No.')
    ->setCellValue('D1', 'GRN Inward Date')
    ->setCellValue('E1', 'Bill No.')
    ->setCellValue('F1', 'Bill Date')
    ->setCellValue('G1', 'Product Name')
    ->setCellValue('H1', 'Vendor Name')
    ->setCellValue('I1', 'Total Qty')
    ->setCellValue('J1', 'Total Recived Qty')
    ->setCellValue('K1', 'Scheduled Qty')
    ->setCellValue('L1', 'Scheduled Date')
    ->setCellValue('M1', 'GRN Total Amount');




$date = date('d-m-Y');
$ii = 2;
$cnt = 1;

foreach ($goodsreceived as $value) {
    $vendor_id = $this->Comman->findvendornames($value['vendor_id']);
    $po = $this->Comman->getpoqty($value['purchaseorder_id']);
    $getpo = $this->Comman->getgrBaseddata($value['purchaseorder_id']);

    foreach ($getpo as $val) {


        $find_deliveryschedule_data = $this->Comman->getdeliveryscheduledata($value['purchaseorder_id'], $val['delivery_schedule_id']);

        $total_qty = ($find_deliveryschedule_data['item_qty']) ? $find_deliveryschedule_data['item_qty'] : 'N/A';

        $createdDate = strtotime($value['inwarddate']);  // timestamp
        $deliveryDate = !empty($find_deliveryschedule_data['delivery_date'])
            ? strtotime($find_deliveryschedule_data['delivery_date'])
            : null;

        $objPHPExcel->getActiveSheet()->setCellValue('A' . $ii, $cnt++);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $ii, $value['id']);
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $ii, $value['purchaseorder_id']);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $ii, date("d-m-Y", strtotime($value['inwarddate'])));
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $ii, $value['bill_no']);
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $ii, date("d-m-Y", strtotime($value['bill_date'])));
        $objPHPExcel->getActiveSheet()->setCellValue('G' . $ii, $val['additem']['item_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('H' . $ii, $vendor_id['name']);
        $objPHPExcel->getActiveSheet()->setCellValue('I' . $ii, number_format((float) $po['total_qty'], 2, '.', ''));
        $objPHPExcel->getActiveSheet()->setCellValue('J' . $ii, number_format((float) $val['quantity'], 2, '.', ''));
        $objPHPExcel->getActiveSheet()->setCellValue('K' . $ii, $total_qty);


        $objPHPExcel->getActiveSheet()->setCellValue('L' . $ii, $deliveryDate ? date('d-m-Y', $deliveryDate) : 'N/A');


        // Condition check => If createdDate > deliveryDate => Highlight column L
        if ($deliveryDate && $createdDate > $deliveryDate) {
            $objPHPExcel->getActiveSheet()->getStyle('L' . $ii)->applyFromArray(
                array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'FF0000') // Red background
                    ),
                    'font' => array(
                        'color' => array('rgb' => 'FFFFFF') // White text
                    )
                )
            );
        }



        $objPHPExcel->getActiveSheet()->setCellValue('M' . $ii, number_format((float) $value['total_amt'], 2, '.', ''));

        $ii++;
    }
}

// exit;

// Rename sheet
//$objPHPExcel->getActiveSheet()->setTitle('Simple');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel2007)
$filename = "GRN_Summary-" . $date . ".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $filename);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
ob_end_clean();
ob_start();
$objWriter->save('php://output');
exit;
