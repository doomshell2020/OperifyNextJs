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
	$cname = !empty($site_details['ac_holder']) ? $site_details['ac_holder'] : '';
	$logo = !empty($site_details['small_logo']) ? WWW_ROOT . 'images' . DS . $site_details['small_logo'] : '';

if (!empty($item_id)) {
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);

	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:F1');
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

	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A2', '#')
		->setCellValue('B2', 'DATE')
		->setCellValue('C2', 'Description')

		->setCellValue('D2', 'Received Stock')
		->setCellValue('E2', 'Dispatched Stock')
		->setCellValue('F2', 'Closing Stock');

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

	$filename = "Export_Detailed_Stock_Item-" . $itemname . ".xlsx";
} else {
    // Consolidated Excel
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);

	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:H1');
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

    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A2', '#')
        ->setCellValue('B2', 'Date')
        ->setCellValue('C2', 'Product Name')
        ->setCellValue('D2', 'Category')
        ->setCellValue('E2', 'Opening Stock')
        ->setCellValue('F2', 'Received Stock')
        ->setCellValue('G2', 'Dispatched Stock')
        ->setCellValue('H2', 'Closing Stock');

    $cnt = 1;
    $ii = 3;

    if (!empty($consolidatedData)) {
        foreach ($consolidatedData as $row) {
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $ii, $cnt++);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $ii, date("d-m-Y", strtotime($row['date'])));
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $ii, $row['product_name']);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $ii, $row['category']);
            
            $columns = ['E', 'F', 'G', 'H'];
            $values = [$row['opening'], $row['received'], $row['dispatched'], $row['closing']];

            foreach ($columns as $index => $column) {
                $objPHPExcel->getActiveSheet()->setCellValue($column . $ii, $values[$index])
                    ->getStyle($column . $ii)
                    ->getNumberFormat()->setFormatCode('0.00');
                $objPHPExcel->getActiveSheet()->getStyle($column . $ii)
                    ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            }
            $ii++;
        }
    }
    
    $filename = "Export_Consolidated_Stock_Register.xlsx";
}

// Rename sheet
//$objPHPExcel->getActiveSheet()->setTitle('Simple');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $filename);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
ob_end_clean();
ob_start();
$objWriter->save('php://output');
exit;