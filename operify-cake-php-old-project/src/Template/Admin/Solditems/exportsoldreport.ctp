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


$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->getStyle(1)->getFont()->setBold(true);

$objPHPExcel->setActiveSheetIndex()->setCellValue('A1', 'S.No.');
$objPHPExcel->setActiveSheetIndex()->setCellValue('B1', 'Date');
$objPHPExcel->setActiveSheetIndex()->setCellValue('C1', 'Item Name*');
$objPHPExcel->setActiveSheetIndex()->setCellValue('D1', 'Payment Mode*');
$objPHPExcel->setActiveSheetIndex()->setCellValue('E1', 'Bill Type');
$objPHPExcel->setActiveSheetIndex()->setCellValue('F1', 'Sale/Return No.');
$objPHPExcel->setActiveSheetIndex()->setCellValue('G1', 'Unit Price');
$objPHPExcel->setActiveSheetIndex()->setCellValue('H1', 'Quantity');
$objPHPExcel->setActiveSheetIndex()->setCellValue('I1', 'Item Amount');
$objPHPExcel->setActiveSheetIndex()->setCellValue('J1', 'Discount');
$objPHPExcel->setActiveSheetIndex()->setCellValue('K1', 'Exempt');
$objPHPExcel->setActiveSheetIndex()->setCellValue('L1', '5%');
$objPHPExcel->setActiveSheetIndex()->setCellValue('M1', '12%');
$objPHPExcel->setActiveSheetIndex()->setCellValue('N1', '18%');
$objPHPExcel->setActiveSheetIndex()->setCellValue('O1', 'Taxable Amount');
$objPHPExcel->setActiveSheetIndex()->setCellValue('P1', 'Gross Total');
$objPHPExcel->setActiveSheetIndex()->setCellValue('Q1', 'Sale To');
$objPHPExcel->setActiveSheetIndex()->setCellValue('R1', 'Class');
$objPHPExcel->setActiveSheetIndex()->setCellValue('S1', 'Status');
//pr($branch_request_detail); die;    
$counter = 1;
if (isset($branch_request_detail) && !empty($branch_request_detail)) {
    foreach ($branch_request_detail as $i => $value) { //pr($branch_request_detail); die;          


        $ii = $i + 2;
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $ii, $counter++);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $ii, date('Y-m-d', strtotime($value['solditem']['pay_date'])));
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $ii, $value['item_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $ii, $value['solditem']['mode_payment']);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $ii, "Sale");
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $ii, $value['solditem']['id']);

        $objPHPExcel->getActiveSheet()->setCellValue('G' . $ii, $value['item_amount']);
        $objPHPExcel->getActiveSheet()->setCellValue('H' . $ii, $value['item_qty']);
        $item_amt = $value['item_amount'] * $value['item_qty'];
        $disc = $value['discount'] * $value['item_qty'];
        $objPHPExcel->getActiveSheet()->setCellValue('I' . $ii, $item_amt);

        if ($value['discount']) {
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $ii, $disc);
        } else {
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $ii, "0");
        }

        $total = $item_amt - $disc;

        if ($value['item_tax']  == 0) {
             $tax_amount= $total * $value['item_tax'] / 100;
            $tax_amount_zero += $total * $value['item_tax'] / 100;
            $objPHPExcel->getActiveSheet()->setCellValue('K' . $ii, sprintf('%.2f', $tax_amount));
        } else {
            $objPHPExcel->getActiveSheet()->setCellValue('K' . $ii, sprintf('%.2f', "0"));
        }

        if ($value['item_tax']  == 5) {
            $tax_amount_five += $total * $value['item_tax'] / 100;
            $tax_amount= $total * $value['item_tax'] / 100;
            $objPHPExcel->getActiveSheet()->setCellValue('L' . $ii, sprintf('%.2f', $tax_amount));
        } else {
            $objPHPExcel->getActiveSheet()->setCellValue('L' . $ii, sprintf('%.2f', "0"));
        }

        if ($value['item_tax']  == 12) {
            $tax_amount= $total * $value['item_tax'] / 100;
            $tax_amount_twelve += $total * $value['item_tax'] / 100;
            $objPHPExcel->getActiveSheet()->setCellValue('M' . $ii, sprintf('%.2f', $tax_amount));
        } else {
            $objPHPExcel->getActiveSheet()->setCellValue('M' . $ii, sprintf('%.2f', "0"));
        }

        if ($value['item_tax'] == 18) {
            $tax_amount_eighteen += $total * $value['item_tax'] / 100;
            $tax_amount= $total * $value['item_tax'] / 100;
            $objPHPExcel->getActiveSheet()->setCellValue('N' . $ii, sprintf('%.2f', $tax_amount));
        } else {
            $objPHPExcel->getActiveSheet()->setCellValue('N' . $ii, sprintf('%.2f', "0"));
        }

      

        // if($value['additem']['taxmaster']['tax']){
        // $objPHPExcel->getActiveSheet()->setCellValue('K' . $ii, $value['additem']['taxmaster']['tax']);
        // }else{
        // $objPHPExcel->getActiveSheet()->setCellValue('K' . $ii, "0");    
        // }

        $objPHPExcel->getActiveSheet()->setCellValue('O' . $ii, sprintf('%.2f', $total));
        $objPHPExcel->getActiveSheet()->setCellValue('P' . $ii, sprintf('%.2f', $total + $tax_amount));
        $objPHPExcel->getActiveSheet()->setCellValue('Q' . $ii, $value['solditem']['customer_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('R' . $ii, $value['solditem']['student']['class']['title']);
        $objPHPExcel->getActiveSheet()->setCellValue('S' . $ii, $value['solditem']['status']);

        $item_amount += $value['item_amount']; //Unit Price        G
        $total_item += $item_amt; //Item Amount                     I
        $total_disc += $disc; //Item disc tax_amount                J
       
        $total_tax_amount_twelve += $tax_amount_twelve; //Item tax_amount_five tax_amount_twelve    N
        $total_tax_amount_eighteen += $tax_amount_eighteen; //Item tax_amount_five tax_amount_eighteen P
        $total_tax_amounttwentyei += $tax_amounttwentyei; //Item tax_amount_five tax_amount_eighteen  O
        // $total_tot_tax_amon += $total + $tax_amount; //Item total_tax_amount   L 
        $P_Column_total += $total; //Item O Column total $total + $tax_amount
        $Q_Column_total += $total + $tax_amount; //Item O Column total $total + $tax_amount
    }

    $totalcol = $counter + 1;
    


    // $objPHPExcel->getActiveSheet()->getStyle('A' . $totalcol)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('G' . $totalcol)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('I' . $totalcol)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('J' . $totalcol)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('L' . $totalcol)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('M' . $totalcol)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('N' . $totalcol)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('P' . $totalcol)->getFont()->setBold(true);
    // $objPHPExcel->getActiveSheet()->getStyle('Q' . $totalcol)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('O' . $totalcol)->getFont()->setBold(true);


    // $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $totalcol, 'Total');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $totalcol, sprintf('%.2f', $item_amount));
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I' . $totalcol, sprintf('%.2f', $total_item));
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J' . $totalcol, sprintf('%.2f', $total_disc));
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L' . $totalcol, sprintf('%.2f', $tax_amount_five));
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('M' . $totalcol, sprintf('%.2f', $tax_amount_twelve));
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('N' . $totalcol, sprintf('%.2f', $tax_amount_eighteen));
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('O' . $totalcol, sprintf('%.2f', $P_Column_total));
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('P' . $totalcol, sprintf('%.2f', $Q_Column_total));



    //pr($comnn); die;
}

$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel2007)
$filename = "Daily_Solditem_Collection.xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $filename);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
