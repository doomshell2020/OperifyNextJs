<?php

$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
    ->setLastModifiedBy("Maarten Balliauw")
    ->setTitle("Stock Register Report")
    ->setSubject("Stock Register Report")
    ->setDescription("Report for Stock Register, generated using PHP classes.")
    ->setKeywords("stock register excel php")
    ->setCategory("Report file");

    $cname = !empty($site_details['ac_holder']) ? $site_details['ac_holder'] : '';
    $logo = !empty($site_details['small_logo']) ? WWW_ROOT . 'images' . DS . $site_details['small_logo'] : '';

    if (!empty($item_id)) {
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);

        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:G1');
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
            ->setCellValue('B2', 'Item ID')
            ->setCellValue('C2', 'DATE')
            ->setCellValue('D2', 'Opening Stock')
            ->setCellValue('E2', 'Received Stock')
            ->setCellValue('F2', 'Dispatched Stock')
            ->setCellValue('G2', 'Closing Stock');

        $date_from = strtotime($datefrom);
        $date_to = strtotime($dateto2);
        $cnt = 1;
        $previousClosingStock = 0;
        $ii = 3;

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
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $ii, $item_id);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $ii, date("d-m-Y", $i));

            $columns = ['D', 'E', 'F', 'G'];
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

        $filename = "Export_Summary_Stock_Item-" . $itemname . ".xlsx";
    } else {
      
        // Consolidated Excel
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
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

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A2', '#')
            ->setCellValue('B2', 'Item ID')
            ->setCellValue('C2', 'Date')
            ->setCellValue('D2', 'Product Name')
            ->setCellValue('E2', 'Category')
            ->setCellValue('F2', 'Opening Stock')
            ->setCellValue('G2', 'Received Stock')
            ->setCellValue('H2', 'Dispatched Stock')
            ->setCellValue('I2', 'Closing Stock');

        $cnt = 1;
        $ii = 3;
   

        if (!empty($consolidatedData)) {
            // print_r($consolidatedData);die;
         
            foreach ($consolidatedData as $row) { 
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $ii, $cnt++);
                $objPHPExcel->getActiveSheet()->setCellValue('B' . $ii, $row['item_id']);
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $ii, date("d-m-Y", strtotime($row['date'])));
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $ii, $row['product_name']);
                $objPHPExcel->getActiveSheet()->setCellValue('E' . $ii, $row['category']);
                
                $columns = ['F', 'G', 'H', 'I'];
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

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $filename);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
ob_end_clean();
ob_start();
$objWriter->save('php://output');
exit;
