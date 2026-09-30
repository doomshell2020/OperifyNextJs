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
// Miscellaneous glyphs, UTF-8



if ($searchdate[0] != '') {
	$todaydate = date('Y-m-d', strtotime($searchdate[0]));
} else {
	$todaydate = date('Y-m-d');
}


$cname = !empty($site_details['ac_holder']) ? $site_details['ac_holder'] : '';
$logo = !empty($site_details['small_logo']) ? WWW_ROOT . 'images' . DS . $site_details['small_logo'] : '';

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);

$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:I1');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $cname);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(50);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(14);

if (file_exists($logo) && !is_dir($logo)) {
	$objDrawing = new PHPExcel_Worksheet_Drawing();
	$objDrawing->setName('Logo');
	$objDrawing->setDescription('Logo');
	$objDrawing->setPath($logo);
	$objDrawing->setCoordinates('A1');
	$objDrawing->setHeight(40);
	$objDrawing->setOffsetX(10);
	$objDrawing->setOffsetY(5);
	$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
}

$objPHPExcel->getActiveSheet()->freezePane('A4');

$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B2', 'Date:' .  date('d-m-Y', strtotime($searchdate[0])) . '')->getStyle('B2')->applyFromArray([
	'font' => ['bold' => true],
]);
$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue('A3', 'ID')
	->setCellValue('B3', 'Product Name')
	->setCellValue('C3', 'Category')
	->setCellValue('D3', 'Opening Stock')
	->setCellValue('E3', 'Received Stock')
	->setCellValue('F3', 'Issued Stock')
	->setCellValue('G3', 'Reverse Stock')
	->setCellValue('H3', 'Return Stock')
	->setCellValue('I3', 'Closing Stock');


$ii = 4;

$cnt = 1;


$toot = 0;

foreach ($categortyname as $categorty) {

	// pr($categorty);
	$additem = $this->comman->getitembycategory($categorty['id']);


	foreach ($additem as $items) {
		// pr( $items);die;
		$openingstocks = $this->comman->todayopeningstock($items['id'], $todaydate);
		$receivedtock = $this->comman->todayrecivedstock($items['id'], $todaydate);
		$issuedstock = $this->comman->todayissuedtock($items['id'], $todaydate);
		$reversestock = $this->comman->todayreversestock($items['id'], $todaydate);
		$returnstock = $this->comman->todayreturnstock($items['id'], $todaydate);
		// $closingstock = $openingstock + $receivedtock - $issuedstock + $reversestock - $returnstock;
		// pr($openingstock.'$openingstock');
		// pr($receivedtock.'$receivedtock');
		// pr($issuedstock.'$issuedstock');
		// pr($closingstock.'$closingstock');exit;
		$openingstock = ($receivedtock - $issuedstock);
		// $closingstock = $openingstock + $reversestock - $returnstock;

		$closingstocks = $receivedtock - $issuedstock;

		$closingstock = number_format((float)$closingstocks, 2, '.', '');
		if ($searchdate[1] == '') {
			if ($receivedtock == 0 && $issuedstock == 0 && $reversestock == 0 && $returnstock == 0) {
				continue;
			}
		}

		$objPHPExcel->getActiveSheet()->setCellValue('A' . $ii, $items['id']);
		$objPHPExcel->getActiveSheet()->setCellValue('B' . $ii, $items['item_name']);
		$objPHPExcel->getActiveSheet()->setCellValue('C' . $ii, $categorty['category_name']);
		$objPHPExcel->getActiveSheet()->setCellValue('D' . $ii, $openingstock);
		$objPHPExcel->getActiveSheet()->setCellValue('E' . $ii, $receivedtock);
		$objPHPExcel->getActiveSheet()->setCellValue('F' . $ii, $issuedstock);
		$objPHPExcel->getActiveSheet()->setCellValue('G' . $ii, $reversestock);
		$objPHPExcel->getActiveSheet()->setCellValue('H' . $ii, $returnstock);
		$objPHPExcel->getActiveSheet()->setCellValue('I' . $ii, $closingstock);
		$ii++;
	}
}


// Rename sheet
//$objPHPExcel->getActiveSheet()->setTitle('Simple');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel2007)
$filename = "Stock_report-" . $todaydate . ".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $filename);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
ob_end_clean();
ob_start();
$objWriter->save('php://output');
exit;
