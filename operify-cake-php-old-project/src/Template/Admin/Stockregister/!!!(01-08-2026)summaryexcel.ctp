<?php

$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
    ->setLastModifiedBy("Maarten Balliauw")
    ->setTitle("Stock Register Report")
    ->setSubject("Stock Register Report")
    ->setDescription("Report for Stock Register, generated using PHP classes.")
    ->setKeywords("stock register excel php")
    ->setCategory("Report file");

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);

$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A1', '#')
    ->setCellValue('B1', 'DATE')
    ->setCellValue('C1', 'Opening Stock')
    ->setCellValue('D1', 'Received Stock')
    ->setCellValue('E1', 'Dispatched Stock')
    ->setCellValue('F1', 'Closing Stock');

$date_from = strtotime($datefrom);
$date_to = strtotime($dateto2);
$cnt = 1;
$previousClosingStock = 0;
$ii = 2;

for ($i = $date_from; $i <= $date_to; $i += 86400) {
    // Opening Stock
    $openingStock = $previousClosingStock;
    if ($i == $date_from) {
        $openingbal = $this->Comman->stockregisteropening2(date('Y-m-d', $i), $item_id);
        $openingStock = $openingbal ?? 0;
    }

    // Received Stock
    $reciviedbal = $this->Comman->stockregisteropeningrecivied(date("Y-m-d", $i), $item_id);
    $receivedStock = $reciviedbal[0]['sum'] ?? 0;

    // Dispatched Stock
    $dispatchedbal = $this->Comman->stockregisteropeningdispatched(date("Y-m-d", $i), $item_id);
    $dispatchedStock = $dispatchedbal[0]['sum'] ?? 0;

    // Calculate Closing Stock
    $totalquant = $openingStock + $receivedStock - $dispatchedStock;
    $previousClosingStock = $totalquant;

    // Populate Excel rows
    $objPHPExcel->getActiveSheet()->setCellValue('A' . $ii, $cnt++);
    $objPHPExcel->getActiveSheet()->setCellValue('B' . $ii, date("d-m-Y", $i));


    $columns = ['C', 'D', 'E', 'F'];
    $values = [$openingStock, $receivedStock, $dispatchedStock, $totalquant];

    foreach ($columns as $index => $column) {
        $objPHPExcel->getActiveSheet()->setCellValue($column . $ii, $values[$index])
            ->getStyle($column . $ii)
            ->getNumberFormat()->setFormatCode('0.00');
        $objPHPExcel->getActiveSheet()->getStyle($column . $ii)
            ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    }


    $ii++;
}

// Item details
$getsize = $this->Comman->getsizename($additem['size_id']);
$itemname = $additem['item_name'];
if ($getsize['id'] != 6) {
    $itemname .= " (" . $getsize['size_name'] . ")";
}

// Export Excel file
$filename = "Export_Summary_Stock_Item-" . $itemname . ".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $filename);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
ob_end_clean();
ob_start();
$objWriter->save('php://output');
exit;
