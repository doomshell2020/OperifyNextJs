<?php

/* ================= CREATE OBJECT ================= */
$objPHPExcel = new PHPExcel();

/* ================= PROPERTIES ================= */
$objPHPExcel->getProperties()
    ->setCreator("CakePHP")
    ->setLastModifiedBy("CakePHP")
    ->setTitle("Last 7 Days Stock Report")
    ->setSubject("Stock Report")
    ->setDescription("Generated using PHPExcel")
    ->setKeywords("excel cakephp phpexcel")
    ->setCategory("Stock");

/* ================= ACTIVE SHEET ================= */
$sheet = $objPHPExcel->getActiveSheet();
$sheet->freezePane('A2');

/* ================= COLUMN WIDTH ================= */
$sheet->getColumnDimension('A')->setWidth(5);
$sheet->getColumnDimension('B')->setWidth(10);
$sheet->getColumnDimension('C')->setWidth(45);
$sheet->getColumnDimension('D')->setWidth(45);

/* ================= HEADER ================= */
$sheet->setCellValue('A1', '#');
$sheet->setCellValue('B1', 'Item ID');
$sheet->setCellValue('C1', 'Product Name');
$sheet->setCellValue('D1', 'Category Name');

/* ================= DYNAMIC DATE HEADERS ================= */
$col = 'E';
$dateArray = [];

for ($i = 0; $i <= 6; $i++) {
    $displayDate = date('d-M-Y', strtotime("-$i days"));
    $dbDate      = date('Y-m-d', strtotime("-$i days"));

    $sheet->setCellValue($col . '1', $displayDate);
    $sheet->getColumnDimension($col)->setWidth(15);


    // pr($dbDate);exit;
    $dateArray[$col] =  $dbDate;
    $col++;
}

/* ================= HEADER STYLE ================= */
$lastCol = chr(ord($col) - 1);
$sheet->getStyle("A1:{$lastCol}1")->getFont()->setBold(true);
$sheet->getStyle("A1:{$lastCol}1")->getAlignment()
    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

/* ================= SAMPLE DATA (REPLACE WITH DB DATA) ================= */

/* ================= DATA ROWS ================= */
$row = 2;
$sno = 1;

foreach ($additem as $productName) { 

    $sheet->setCellValue('A' . $row, $sno++);
    $sheet->setCellValue('B' . $row, $productName['id']);
    $sheet->setCellValue('C' . $row, $productName['item_name']);
    $sheet->setCellValue('D' . $row, $productName['itemcategory']['category_name']);

    foreach ($dateArray as $colKey => $dateVal) {
     


        $openingstocks = $this->comman->todayopeningstock($productName['id'], $dateVal);
        $receivedtock = $this->comman->todayrecivedstock($productName['id'], $dateVal);
        $issuedstock = $this->comman->todayissuedtock($productName['id'], $dateVal);
        // $reversestock = $this->comman->todayreversestock($productName['id'], $dateVal);
        // $returnstock = $this->comman->todayreturnstock($productName['id'], $dateVal);

        $openingstock = ($receivedtock - $issuedstock);
        // $closingstock = $openingstock + $reversestock - $returnstock;

        $closingstock = number_format((float)$openingstock, 2, '.', '');


        $sheet->setCellValue($colKey . $row, $closingstock);
    }

    $row++;
}

/* ================= AUTO FILTER ================= */
$sheet->setAutoFilter('C1:C1');

/* ================= DOWNLOAD ================= */
$filename = "Last_7_Days_Stock_Report.xlsx";

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
ob_end_clean();
ob_start();
$objWriter->save('php://output');
exit;
