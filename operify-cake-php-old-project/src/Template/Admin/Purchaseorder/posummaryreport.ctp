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
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(40);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(40);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(10);


$objPHPExcel->getActiveSheet()->getStyle('A1:M1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('808080');
$objPHPExcel->getActiveSheet()->getStyle(1)->getFont()->setBold(true);

$objPHPExcel->getActiveSheet()->freezePane('A2'); // Freezes row 1
$objPHPExcel->getActiveSheet()->setAutoFilter('A1:M1');

$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A1', 'PO No')
    ->setCellValue('B1', 'PO Date')
    ->setCellValue('C1', 'Delivery Date')
    ->setCellValue('D1', 'Supplier Name')
    ->setCellValue('E1', 'Mobile No.')
    ->setCellValue('F1', 'GRN No.')
    ->setCellValue('G1', 'GRN Date')
    ->setCellValue('H1', 'Producat Name')
    ->setCellValue('I1', 'Producat Price')
    ->setCellValue('J1', 'PO Qty.')
    ->setCellValue('K1', 'Received Qty.')
    ->setCellValue('L1', 'Balance')
    ->setCellValue('M1', 'Tax');

$date = date('d-m-Y');
$ii = 2;
$balance = 0;

foreach ($podata as $key => $value) { 

    $getpurchaseorder = $this->Comman->getPurchaseOrder($value);

    $purchaseorder_id = ($getpurchaseorder['status'] == 'R') ? ($getpurchaseorder['purchaseorder_id'] . ' R-' . $getpurchaseorder['is_revised']) : $getpurchaseorder['purchaseorder_id'];
    $vendor_id = $this->Comman->findvendornames($getpurchaseorder['vendor_id']);
    $getpurchaseorderdetails = $this->Comman->getpurchaseorderdetails($value, $key);


    foreach ($getpurchaseorderdetails as $val2) { 



      $gettaxparent = $this->Comman->gettaxname2($val2['tax_id']);
    //   pr($gettaxparent); die;


        $itemname = $this->Comman->getitemname($val2['item_id']);
        $find_qty_and_itemname = $this->Comman->find_po_item_name_and_qty($val2['purchaseorder_id'], $val2['item_id']);


        $good_id = array();
        foreach ($find_qty_and_itemname as $idsss) {
            $good_id[] = $idsss['goods_id'];
        }
        $total_received = 0;
        foreach ($find_qty_and_itemname as $idsss) {
            $total_received += $idsss['quantity'];
        }
        $lastgood_id = '';
        foreach ($find_qty_and_itemname as $idsss) {
            $lastgood_id = $idsss['goods_id'];
        }
        $po_item_qty_single = $this->Comman->find_po_item__qty($val['additem']['id'], $val['po_id']);
        $po_item_qty_sum = $this->Comman->poitem_qty($val['po_id']);

        if ($lastgood_id) {
            $grndate = $this->Comman->findgoodsrecivieddate($lastgood_id);
        } else {
            $grndate = '';
        }
        $balance = $val2['item_qty'] - $total_received;

        if ($getpurchaseorder['postatus'] == 'O') {
            if ($balance == 0) {
                continue;
            }
        }
        if ($grndate != '') {
            $grninwarddate = date("d-m-Y", strtotime($grndate['inwarddate']));
        } else {
            $grninwarddate = '';
        }

        $objPHPExcel->getActiveSheet()->setCellValue('A' . $ii, $purchaseorder_id)->getStyle('A' . $ii)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $ii, date("d-m-Y", strtotime($getpurchaseorder['added_time'])))->getStyle('B' . $ii)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $ii, date("d-m-Y", strtotime($getpurchaseorder['delivery_date'])))->getStyle('C' . $ii)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $ii, $vendor_id['name'])->getStyle('D' . $ii)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $ii, $vendor_id['contact_no'])->getStyle('E' . $ii)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $ii, implode(',', $good_id))->getStyle('F' . $ii)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->setCellValue('G' . $ii, $grninwarddate)->getStyle('G' . $ii)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        $objPHPExcel->getActiveSheet()->setCellValue('H' . $ii, $itemname['item_name'])->getStyle('H' . $ii)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);


        $cell = 'I' . $ii;
        $objPHPExcel->getActiveSheet()
            ->setCellValue($cell, (float) $val2['item_amt']);
        $objPHPExcel->getActiveSheet()
            ->getStyle($cell)
            ->getNumberFormat()
            ->setFormatCode('#,##0.00'); // Two decimal places

        //show item price


        // $objPHPExcel->getActiveSheet()->setCellValue('I' . $ii, ((float) $val2['item_amt']))->getStyle('I' . $ii);



        $objPHPExcel->getActiveSheet()->setCellValue('J' . $ii, number_format((float) $val2['item_qty'], 2, '.', ''))->getStyle('J' . $ii)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        $objPHPExcel->getActiveSheet()->setCellValue('K' . $ii, number_format((float) $total_received, 2, '.', ''))->getStyle('K' . $ii)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        $objPHPExcel->getActiveSheet()->setCellValue('L' . $ii, $balance)->getStyle('L' . $ii)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        $objPHPExcel->getActiveSheet()->setCellValue('M' . $ii, $gettaxparent[0]['tax'])->getStyle('L' . $ii)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);


        $ii++;
    }
}


// Rename sheet
//$objPHPExcel->getActiveSheet()->setTitle('Simple');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel2007)
$filename = "PO_GRN_REPORT" . $date . ".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $filename);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
ob_end_clean();
ob_start();
$objWriter->save('php://output');
exit;
