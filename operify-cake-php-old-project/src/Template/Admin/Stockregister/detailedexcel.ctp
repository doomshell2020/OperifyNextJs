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
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);

$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue('A1', '#')
	->setCellValue('B1', 'DATE')
	->setCellValue('C1', 'Description')

	->setCellValue('D1', 'Received Stock')
	->setCellValue('E1', 'Dispatched Stock')
	->setCellValue('F1', 'Closing Stock');

$date = date('d-m-Y');
$ii = 3;

$date_from = strtotime($datefrom);
$date_to = strtotime($dateto2);
$cnt = 1;

// Loop from the start date to end date and output all dates in between  
$toot = 0;
foreach ($stockregister as $key => $items) {
	$totalquant = 0;

	$objPHPExcel->getActiveSheet()->setCellValue('A' . $ii, $cnt++);
	$objPHPExcel->getActiveSheet()->setCellValue('B' . $ii, date("d-m-Y", strtotime($items['created'])));

	if ($items['po_id'] != 0) {
		$PO = "PO-" . $items['po_id'];
		$objPHPExcel->getActiveSheet()->setCellValue('C' . $ii, $PO);
	} else {

		$Indent = "Indent-" . $items['indent_id'];
		$objPHPExcel->getActiveSheet()->setCellValue('C' . $ii, $Indent);


	}

	if ($items['store_type'] != 2) {
		$totalquant += $items['quantity'];
		$toot += intval($items['quantity']);
		$objPHPExcel->getActiveSheet()->setCellValue('D' . $ii, $items['quantity']);
	} else {

		$toot += intval(0);
		$objPHPExcel->getActiveSheet()->setCellValue('D' . $ii, 0);
	}

	if ($items['store_type'] != 1) {
		$totalquant += $items['quantity'];

		$toot -= intval($items['quantity']);
		$objPHPExcel->getActiveSheet()->setCellValue('E' . $ii, $items['quantity']);
	} else {

		$toot -= intval(0);
		$objPHPExcel->getActiveSheet()->setCellValue('E' . $ii, 0);
	}

	$objPHPExcel->getActiveSheet()->setCellValue('F' . $ii, $toot);
	$ii++;
	//	$toot++;
}

$getsize = $this->Comman->getsizename($additem['size_id']);
$itemname = $additem['item_name'];
if ($getsize['id'] != 6) {
	$itemname .= " (" . $getsize['size_name'] . ")";
}

// Rename sheet
//$objPHPExcel->getActiveSheet()->setTitle('Simple');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel2007)
$filename = "Export_Detailed_Stock_Item-" . $itemname . ".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $filename);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
ob_end_clean();
ob_start();
$objWriter->save('php://output');
exit;