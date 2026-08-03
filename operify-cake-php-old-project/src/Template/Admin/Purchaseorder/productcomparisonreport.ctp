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

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(6);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(35);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(40);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(35);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(12);


$objPHPExcel->getActiveSheet()->getStyle('A1:M1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('808080');
$objPHPExcel->getActiveSheet()->getStyle(1)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->setAutoFilter('A1:M1');

$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A1', 'S.No.')
    ->setCellValue('B1', 'PO Date')
    ->setCellValue('C1', 'PO No')
    ->setCellValue('D1', 'PO Vendor Name')
    ->setCellValue('E1', 'Item Name')
    ->setCellValue('F1', 'Price1')
    ->setCellValue('G1', 'Price2')
    ->setCellValue('H1', 'Price3')
    ->setCellValue('I1', 'Price4')
    ->setCellValue('J1', 'Price5')
    ->setCellValue('K1', 'Vendor Name')
    ->setCellValue('L1', 'PO No.')
    ->setCellValue('M1', 'PO Date.');

$date = date('d-m-Y');
$ii = 2;
$counter = 1;
$balance = 0;

// // for item base excel
// foreach ($itemName as $key => $value) {
//     $getlastprice = $this->Comman->lastitemcost($value['id']);

//     $lowestPrice = null;
//     $lowestPrice_poId = null;
//     foreach ($getlastprice as $itemkey => $price) {
//         $cost = $price->item_amt;
//         if ($cost == 0) {
//             continue;
//         }
//         if ($lowestPrice === null || $cost < $lowestPrice) {
//             $lowestPrice = $price->item_amt;
//             $lowestPrice_poId = $price->purchaseorder_id;
//             $itemindex = $itemkey;
//         }
//     }

//     $getpurchaseorder = $this->Comman->getPurchaseOrder($lowestPrice_poId);
//     $vendor_id = $this->Comman->findvendornames($getpurchaseorder['vendor_id']);
//     $date = (!empty($getlastprice)) ? date("d-m-Y", strtotime($getlastprice[0]['inward_date'])) : '';
//     $lowestdate = (!empty($getpurchaseorder)) ? date("d-m-Y", strtotime($getpurchaseorder['added_time'])) : '';

//     $objPHPExcel->getActiveSheet()->setCellValue('A' . $ii, $counter)->getStyle('A' . $ii)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
//     $objPHPExcel->getActiveSheet()->setCellValue('B' . $ii, $value['item_name'])->getStyle('B' . $ii)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
//     $objPHPExcel->getActiveSheet()->setCellValue('C' . $ii, $getlastprice[0]['purchaseorder_id'])->getStyle('C' . $ii)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
//     $objPHPExcel->getActiveSheet()->setCellValue('D' . $ii, $date)->getStyle('D' . $ii)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

//     $secondColumnIndex = 4;
//     for ($i = 0; $i < 5; $i++) {
//         $columnLetter = PHPExcel_Cell::stringFromColumnIndex($secondColumnIndex);
//         $cell = $objPHPExcel->getActiveSheet()->setCellValue($columnLetter . $ii, $getlastprice[$i]['item_amt']);
//         if ($i == $itemindex) {
//             $objPHPExcel->getActiveSheet()->getStyle($columnLetter . $ii)->applyFromArray(['font' => ['bold' => true, 'color' => ['rgb' => 'FF0000'],],]);
//         }
//         $cell->getStyle($columnLetter . $ii)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
//         $secondColumnIndex++;
//     }

//     $objPHPExcel->getActiveSheet()->setCellValue('J' . $ii, $vendor_id['name'])->getStyle('J' . $ii)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
//     $objPHPExcel->getActiveSheet()->setCellValue('K' . $ii, $getpurchaseorder['purchaseorder_id'])->getStyle('K' . $ii)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
//     $objPHPExcel->getActiveSheet()->setCellValue('L' . $ii, $lowestdate)->getStyle('K' . $ii)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

//     $ii++;
//     $counter++;

// }


// for item base excel
foreach ($podata as $key => $value1) {
    $getpurchaseorderdetails = $this->Comman->getpurchaseorderdetails($value1['purchaseorder_id'], $value1['id']);
    $date1 = date("Y-m-d", strtotime($value1['added_time']));
    $vendorName = $this->Comman->findvendornames($value1['vendor_id']);

    $value1['purchaseorder_id'] = ($value1['status'] == 'R') ? ($value1['purchaseorder_id'] . ' R-' . $value1['is_revised']) : $value1['purchaseorder_id'];
    foreach ($getpurchaseorderdetails as $value) {
        $getlastprice = $this->Comman->lastitemcost($value['item_id'], $date1);
        $getitemname = $this->Comman->getitemname($value['item_id']);

        $lowestPrice = null;
        $lowestPrice_poId = null;
        foreach ($getlastprice as $itemkey => $price) {
            $cost = $price->item_amt;
            if ($cost == 0) {
                continue;
            }
            if ($lowestPrice === null || $cost < $lowestPrice) {
                $lowestPrice = $price->item_amt;
                $lowestPrice_poId = $price->purchaseorder_id;
                $itemindex = $itemkey;
            }
        }

        $getpurchaseorder = $this->Comman->getPurchaseOrder($lowestPrice_poId);
        $vendor_id = $this->Comman->findvendornames($getpurchaseorder['vendor_id']);
        $lowestdate = (!empty($getpurchaseorder)) ? date("d-m-Y", strtotime($getpurchaseorder['added_time'])) : '';
        $date = (!empty($value1)) ? date("d-m-Y", strtotime($value1['added_time'])) : '';

        $getpurchaseorder['purchaseorder_id'] = ($getpurchaseorder['status'] == 'R') ? ($getpurchaseorder['purchaseorder_id'] . ' R-' . $getpurchaseorder['is_revised']) : $getpurchaseorder['purchaseorder_id'];

        $objPHPExcel->getActiveSheet()->setCellValue('A' . $ii, $counter)->getStyle('A' . $ii)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $ii, $date)->getStyle('B' . $ii)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $ii, $value1['purchaseorder_id'])->getStyle('C' . $ii)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $ii, $vendorName['name'])->getStyle('D' . $ii)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $ii, $getitemname['item_name'])->getStyle('E' . $ii)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        $secondColumnIndex = 5;
        for ($i = 0; $i < 5; $i++) {
            $columnLetter = PHPExcel_Cell::stringFromColumnIndex($secondColumnIndex);
            $cell = $objPHPExcel->getActiveSheet()->setCellValue($columnLetter . $ii, $getlastprice[$i]['item_amt']);
            if ($i == $itemindex) {
                $objPHPExcel->getActiveSheet()->getStyle($columnLetter . $ii)->applyFromArray(['font' => ['bold' => true, 'color' => ['rgb' => 'FF0000'],],]);
            }
            $cell->getStyle($columnLetter . $ii)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $secondColumnIndex++;
        }

        $objPHPExcel->getActiveSheet()->setCellValue('K' . $ii, $vendor_id['name'])->getStyle('K' . $ii)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->setCellValue('L' . $ii, $getpurchaseorder['purchaseorder_id'])->getStyle('L' . $ii)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->setCellValue('M' . $ii, $lowestdate)->getStyle('M' . $ii)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        $ii++;
        $counter++;
    }

}




// Rename sheet
//$objPHPExcel->getActiveSheet()->setTitle('Simple');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel2007)
$filename = "LPR_Report" . $date . ".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $filename);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
ob_end_clean();
ob_start();
$objWriter->save('php://output');
exit;
