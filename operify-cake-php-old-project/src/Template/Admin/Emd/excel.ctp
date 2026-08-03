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

$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A1', 'Date')
    ->setCellValue('B1', 'DD/Chwqu/BG No.')
    ->setCellValue('C1', 'Favour')
    ->setCellValue('D1', 'PO.No./Tendor No.')
    ->setCellValue('E1', 'Amount')
    ->setCellValue('F1', 'Brand Name')
    ->setCellValue('G1', 'Type')
    ->setCellValue('H1', 'Valid Upto')
    ->setCellValue('I1', 'Claim Date')
    ->setCellValue('J1', 'Extenstion Upto')
    ->setCellValue('K1', 'Last Date of Supply')
    ->setCellValue('L1', 'Release On')
    ->setCellValue('M1', 'Contect Person');


$date = date('d-m-Y');
$ii = 2;
$cnt = 1;


foreach ($EmdGuarantees as $detail) {
    $contractname = $this->comman->findcontractname($detail['contract_id']);


    $objPHPExcel->getActiveSheet()->setCellValue('A' . $ii, date("d-m-Y", strtotime($detail['datefrom'])));
    $objPHPExcel->getActiveSheet()->setCellValue('B' . $ii, !empty($detail['bankguaranteeno']) ? $detail['bankguaranteeno'] : '-');
    $objPHPExcel->getActiveSheet()->setCellValue('C' . $ii, !empty($detail['favour_of']) ? $detail['favour_of'] : '-');
    $objPHPExcel->getActiveSheet()->setCellValue('D' . $ii, !empty($detail['po_no']) ? $detail['po_no'] : '-');
    $currencySymbols = [
        'USD' => '$',
        'INR' => '',
        'EUR' => '€',
        'GBP' => '£'
    ];

    $symbol = isset($currencySymbols[$detail['currency_type']]) ? $currencySymbols[$detail['currency_type']] : '';
    $amountWithSymbol = !empty($detail['amount']) ? $symbol . $detail['amount'] : '-';

    $objPHPExcel->getActiveSheet()->setCellValue('E' . $ii, $amountWithSymbol);
    $objPHPExcel->getActiveSheet()->setCellValue('F' . $ii, !empty($detail['board_name']) ? $detail['board_name'] : '-');
    $objPHPExcel->getActiveSheet()->setCellValue(
        'G' . $ii,
        !empty($detail['po_or_rma'])
            ? ($detail['po_or_rma'] == 'PO'
                ? 'Purchase Order'
                : ($detail['po_or_rma'] == 'RM'
                    ? 'Raw Material'
                    : $detail['po_or_rma']))
            : '-'
    );
    $objPHPExcel->getActiveSheet()->setCellValue('H' . $ii, !empty($detail['validupto']) ? date("d-m-Y", strtotime($detail['validupto'])) : '-');
    $objPHPExcel->getActiveSheet()->setCellValue('I' . $ii, !empty($detail['claim_upto']) ? date("d-m-Y", strtotime($detail['claim_upto'])) : '-');
    $objPHPExcel->getActiveSheet()->setCellValue('J' . $ii, !empty($detail['extenstionupto']) ? date("d-m-Y", strtotime($detail['extenstionupto'])) : '-');
    $objPHPExcel->getActiveSheet()->setCellValue('K' . $ii, !empty($detail['lastdate']) ? date("d-m-Y", strtotime($detail['lastdate'])) : '-');
    $objPHPExcel->getActiveSheet()->setCellValue('L' . $ii, !empty($detail['relese_date']) ? date("d-m-Y", strtotime($detail['relese_date'])) : '-');
    $objPHPExcel->getActiveSheet()->setCellValue('M' . $ii, !empty($detail['contect_per']) ? $detail['contect_per'] : '-');

    $ii++;
}

$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel2007)
$filename = "EMD_Summary_" . $date . ".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $filename);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
ob_end_clean();
ob_start();
$objWriter->save('php://output');
exit;
