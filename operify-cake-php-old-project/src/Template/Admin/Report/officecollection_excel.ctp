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
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(10);
$objPHPExcel->getActiveSheet()->mergeCells('C1:G1');
$objPHPExcel->getActiveSheet()->mergeCells('H1:L1');
$objPHPExcel->getActiveSheet()->mergeCells('M1:N1');
$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A1', '#')
    ->setCellValue('B1', 'DATE')
    ->setCellValue('C1', 'CBSE')
    ->setCellValue('H1', 'INTERNATIONAL')
    ->setCellValue('M1', 'OTHER FEES')
;
$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A2', '')
    ->setCellValue('B2', '')
    ->setCellValue('C2', 'Prospectus Sale')
    ->setCellValue('D2', 'Registration Sale')
    ->setCellValue('E2', 'Admission Total')
    ->setCellValue('F2', 'Fees Cash')
    ->setCellValue('G2', 'Fees Others Mode')
    ->setCellValue('H2', 'Prospectus Sale')
    ->setCellValue('I2', 'Registration Sale')
    ->setCellValue('J2', 'Admission Total')
    ->setCellValue('K2', 'Fees Cash')
    ->setCellValue('L2', 'Fees Others Mode')
    ->setCellValue('M2', 'Fees Cash')
    ->setCellValue('N2', 'Fees Others Mode')
;
$datefrom = strtotime($datefrom);
$dateto2 = strtotime($dateto2);
$prospecttotalcbse = 0;
$registrationtotalcbse = 0;
$admissionntotalcbse = 0;
$feecashtotalcbse = 0;
$feeotherstotalcbse = 0;

$prospecttotalinter = 0;
$registrationtotalinter = 0;
$admissionntotalinter = 0;
$feecashtotalinter = 0;
$feeotherstotalinter = 0;
$ofcashtotal = 0;
$ofnotcashtotal = 0;
$ofcashdaily = 0;
$ofnotcashdaily = 0;
$date = date('d-m-Y');
$ii = 3;
$cnt = 1;


for ($i=$datefrom; $i<=$dateto2; $i+=86400) {
	
	
    $objPHPExcel->getActiveSheet()->setCellValue('A' . $ii, $cnt++);
    $objPHPExcel->getActiveSheet()->setCellValue('B' . $ii, date("Y-m-d", $i));

    $prospectcbse = $this->Comman->findprospectusstudents2stodaydetail2r('1',$academic, date("Y-m-d", $i));
    $prospecttotalcbse += $prospectcbse;
    $objPHPExcel->getActiveSheet()->setCellValue('C' . $ii, $prospectcbse);

    $registrationcbse = $this->Comman->findregistrationtudents2stodaydetail2r('1',$academic, date("Y-m-d", $i));
    $registrationtotalcbse += $registrationcbse;
    $objPHPExcel->getActiveSheet()->setCellValue('D' . $ii, $registrationcbse);

    $admissioncbse = $this->Comman->findacedemicstudents2stodaydetailhis('1', $academic,date("Y-m-d", $i));
    $dropoutcbse = $this->Comman->findacedemicstudents2stodayoutdrop('1', $academic,date("Y-m-d", $i));
    $admissionntotalcbse += $admissioncbse + $dropoutcbse;
    $objPHPExcel->getActiveSheet()->setCellValue('E' . $ii, $admissioncbse + $dropoutcbse);

    $feecashcbse = $this->Comman->findcollectiontudents2stodaydetail2r('CASH', $academic, date("Y-m-d", $i));
    $feecashcbse2 = $this->Comman->findcollectiontudents2stodaydetaildroppp2r('CASH',$academic, date("Y-m-d", $i));
    if ($feecashcbse[0]['sum'] || $feecashcbse2[0]['sum']) {$dd = $feecashcbse[0]['sum'] + $feecashcbse2[0]['sum'];
        $feecashtotalcbse += $feecashcbse[0]['sum'] + $feecashcbse2[0]['sum'];} else { $dd = "0";}
    $objPHPExcel->getActiveSheet()->setCellValue('F' . $ii, $dd);
    $feeotherscbse = $this->Comman->findcollectiontudents2stoday2detail2r('CASH', $academic,date("Y-m-d", $i));
    $feeotherscbse2s = $this->Comman->findcollectiontudents2stoday2detaildropp2r('CASH',$academic, date("Y-m-d", $i));

    if ($feeotherscbse[0]['sum'] || $feecashcbse2s[0]['sum']) {$rrt = $feeotherscbse[0]['sum'] + $feecashcbse2s[0]['sum'];

        $feeotherstotalcbse += $rrt;} else { $rrt = "0";}

    $objPHPExcel->getActiveSheet()->setCellValue('G' . $ii, $rrt);

    $prospectusinter = $this->Comman->findprospectusstudents2stodaydetailint2r('1',$academic, date("Y-m-d", $i));
    $prospecttotalinter += $prospectusinter;
    $objPHPExcel->getActiveSheet()->setCellValue('H' . $ii, $prospectusinter);

    $registrationinter = $this->Comman->findregistrationtudents2stodaydetailint2r('1',$academic, date("Y-m-d", $i));
    $registrationtotalinter += $registrationinter;
    $objPHPExcel->getActiveSheet()->setCellValue('I' . $ii, $registrationinter);

    $admissioninter = $this->Comman->findacedemicstudents2stodayintdetailhis('1', $academic,date("Y-m-d", $i));
    $dropoutinter = $this->Comman->findacedemicstudents2stodayoutintdetaildrop('1',$academic,date("Y-m-d", $i));
    $admissionntotalinter += $admissioninter + $dropoutinter;
    $objPHPExcel->getActiveSheet()->setCellValue('J' . $ii, $admissioninter + $dropoutinter);

    $feecashint = $this->Comman->findcollectiontudents2stodayoutdetail2r('CASH', $academic,date("Y-m-d", $i));
    $feecashint2 = $this->Comman->findcollectiontudents2stodayoutdetaildropp2r('CASH',$academic, date("Y-m-d", $i));
    if ($feecashint[0]['sum'] || $feecashint2[0]['sum']) {$rrtd = $feecashint[0]['sum'] + $feecashint2[0]['sum'];
        $feecashtotalinter += $rrtd;} else { $rrtd = "0";}
    $objPHPExcel->getActiveSheet()->setCellValue('K' . $ii, $rrtd);

    $feeothersint = $this->Comman->findcollectiontudents2stoday2outdetail2r('CASH',$academic, date("Y-m-d", $i));
    $feeothersint2s = $this->Comman->findcollectiontudents2stoday2outdetaildropp2r('CASH',$academic, date("Y-m-d", $i));
    if ($feeothersint[0]['sum'] || $feeothersint2s[0]['sum']) {$rrrrr = $feeothersint[0]['sum'] + $feeothersint2s[0]['sum'];
        $feeotherstotalinter += $rrrrr;} else { $rrrrr = "0";}
    $objPHPExcel->getActiveSheet()->setCellValue('L' . $ii, $rrrrr);

    $ofcash = $this->Comman->findofcashdaily('CASH', date("Y-m-d", $i));
    if ($ofcash[0]['sum'] || $ofcash[0]['sum']) {
        $ofcashdaily = $ofcash[0]['sum'];
    } else { $ofcashdaily = "0";}
    $ofcashtotal += $ofcashdaily;

    $objPHPExcel->getActiveSheet()->setCellValue('M' . $ii, $ofcashdaily);

    $ofother = $this->Comman->findofnotcashdaily('CASH', date("Y-m-d", $i));
    if ($ofother[0]['sum'] || $ofother[0]['sum']) {
        $ofnotcashdaily = $ofother[0]['sum'];
    } else { $ofnotcashdaily = "0";}
    $ofnotcashtotal += $ofnotcashdaily;
    $objPHPExcel->getActiveSheet()->setCellValue('N' . $ii, $ofnotcashdaily);
    $ii++;
}


$gt = $ii + 1;
$objPHPExcel->getActiveSheet()->mergeCells('A' . $gt . ':B' . $gt);
$objPHPExcel->getActiveSheet()->setCellValue('A' . $gt, 'TOTAL');
$objPHPExcel->getActiveSheet()->setCellValue('C' . $gt, $prospecttotalcbse);
$objPHPExcel->getActiveSheet()->setCellValue('D' . $gt, $registrationtotalcbse);
$objPHPExcel->getActiveSheet()->setCellValue('E' . $gt, $admissionntotalcbse);
$objPHPExcel->getActiveSheet()->setCellValue('F' . $gt, $feecashtotalcbse);
$objPHPExcel->getActiveSheet()->setCellValue('G' . $gt, $feeotherstotalcbse);
$objPHPExcel->getActiveSheet()->setCellValue('H' . $gt, $prospecttotalinter);
$objPHPExcel->getActiveSheet()->setCellValue('I' . $gt, $registrationtotalinter);
$objPHPExcel->getActiveSheet()->setCellValue('J' . $gt, $admissionntotalinter);
$objPHPExcel->getActiveSheet()->setCellValue('K' . $gt, $feecashtotalinter);
$objPHPExcel->getActiveSheet()->setCellValue('L' . $gt, $feeotherstotalinter);
$objPHPExcel->getActiveSheet()->setCellValue('M' . $gt, $ofcashtotal);
$objPHPExcel->getActiveSheet()->setCellValue('N' . $gt, $ofnotcashtotal);

// Rename sheet
//$objPHPExcel->getActiveSheet()->setTitle('Simple');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel2007)
$filename = "Office_Fee_Collection_Report.xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $filename);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
ob_end_clean();
ob_start();
$objWriter->save('php://output');
exit;
