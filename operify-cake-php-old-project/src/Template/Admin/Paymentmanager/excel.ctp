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

// Set column widths
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(30);

// Set header row
$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A1', 'Particular')
    ->setCellValue('B1', 'Consignee')
    ->setCellValue('C1', 'PO.No.')
    ->setCellValue('D1', 'Invoice No.')
    ->setCellValue('E1', 'Date')
    ->setCellValue('F1', 'Bills Dispatch Date')
    ->setCellValue('G1', 'Due Period')
    ->setCellValue('H1', 'Bill Amount')
    ->setCellValue('I1', 'Received Amount')
    ->setCellValue('J1', 'Pending Amount');

$styleArray = [
    'alignment' => [
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    ],
];

$objPHPExcel->getActiveSheet()->getStyle("A{$start}:A{$end}")->applyFromArray($styleArray);



$groupedData = [];
foreach ($Particularpayments as $row) {
    $key = strtolower(trim(preg_replace('/\s+/', ' ', $row->particular ?? '-')));
    $groupedData[$key][] = $row;
}


$date = date('d-m-Y');
$ii = 2;
$totalReceived = 0;
$totalAmount = 0;
$totalPending = 0;
foreach ($groupedData as $group => $records) {
    $startRow = $ii;

    // Group by PO No within this particular group
    $poGrouped = [];
    foreach ($records as $record) {
        $poKey = strtolower(trim($record->po_no ?? '-'));
        $poGrouped[$poKey][] = $record;
    }

    foreach ($poGrouped as $poGroup => $poRecords) {
        $poStartRow = $ii;
        foreach ($poRecords as $record) {
            $receivedAmount = $this->Comman->getReceivedTotalAmount($record->id);
            $pendingAmount = $record->amount - $receivedAmount;

            $totalReceived += $receivedAmount;
            $totalAmount += $record->amount;
            $totalPending += $pendingAmount;

            // Leave column A and C for merged cells
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $ii, $record->consignee ?? '-');
            // Column C will be filled once after merge
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $ii, $record->invoice ?? '-');
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $ii, !empty($record->datefrom) ? $record->datefrom->format('d-m-Y') : '-');
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $ii, !empty($record->bill_dis_date) ? $record->bill_dis_date->format('d-m-Y') : '-');
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $ii, $record->due_period ? $record->due_period . ' Days' : '-');
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $ii, $record->amount ?? '-');
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $ii, $receivedAmount ?: '-');
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $ii, $pendingAmount ?: '-');

            $ii++;
        }
        $poEndRow = $ii - 1;

        // Merge PO No cells in column C
        if ($poStartRow !== $poEndRow) {
            $objPHPExcel->getActiveSheet()->mergeCells("C{$poStartRow}:C{$poEndRow}");
        }
        $objPHPExcel->getActiveSheet()->setCellValue("C{$poStartRow}", $poRecords[0]->po_no ?? '-');
    }

    $endRow = $ii - 1;

    // Merge Particular cells in column A
    if ($startRow !== $endRow) {
        $objPHPExcel->getActiveSheet()->mergeCells("A{$startRow}:A{$endRow}");
    }
    $objPHPExcel->getActiveSheet()->setCellValue("A{$startRow}", ucwords($records[0]->particular));
}

// Add totals row
$objPHPExcel->getActiveSheet()->setCellValue('G' . $ii, 'Total');
$objPHPExcel->getActiveSheet()->setCellValue('H' . $ii, $totalAmount);
$objPHPExcel->getActiveSheet()->setCellValue('I' . $ii, $totalReceived);
$objPHPExcel->getActiveSheet()->setCellValue('J' . $ii, $totalPending);

// Bold the total row
$boldStyle = [
    'font' => ['bold' => true]
];
$objPHPExcel->getActiveSheet()->getStyle("G$ii:J$ii")->applyFromArray($boldStyle);

// Set active sheet index
$objPHPExcel->setActiveSheetIndex(0);

// Send to browser
$filename = "Payments_Summary_" . $date . ".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $filename);
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
ob_end_clean(); // prevent corrupt output
ob_start();
$objWriter->save('php://output');
exit;
