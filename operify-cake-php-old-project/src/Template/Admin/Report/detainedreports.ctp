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
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(6);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(14);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(10);

$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A1', 'S.No.')
    ->setCellValue('B1', 'Sr.No.')
    ->setCellValue('C1', 'Pupil Name')
    ->setCellValue('D1', 'Father Name')
    ->setCellValue('E1', 'Mother Name')
    ->setCellValue('F1', 'Class')
    ->setCellValue('G1', 'Section')
    ->setCellValue('H1', 'Academic Year')
    ->setCellValue('I1', 'Admission Year')
    ->setCellValue('J1', 'Mobile')
    ->setCellValue('K1', 'Status')
;

$counter = 1;
if (isset($students) && !empty($students)) {
    foreach ($students as $i => $work) {
        //pr($work);die;
        $ii = $i + 2;

        $objPHPExcel->getActiveSheet()->setCellValue('A' . $ii, $counter++);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $ii, $work['enroll']);
        $name = $work['fname'] . ' ';

        if (!empty($work['middlename'])) {
            $name .= $work['middlename'] . ' ';
        }
        

        $name .= $work['lname'];
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $ii, $name);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $ii, $work['fathername']);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $ii, $work['mothername']);
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $ii, $work['class']['title']);
        $objPHPExcel->getActiveSheet()->setCellValue('G' . $ii, $work['section']['title']);
        $objPHPExcel->getActiveSheet()->setCellValue('H' . $ii, $work['acedmicyear']);
        $objPHPExcel->getActiveSheet()->setCellValue('I' . $ii, $work['admissionyear']);
        $objPHPExcel->getActiveSheet()->setCellValue('J' . $ii, $work['sms_mobile']);
        $objPHPExcel->getActiveSheet()->setCellValue('K' . $ii, 'Detained');
        

    }

}

// Rename sheet
//$objPHPExcel->getActiveSheet()->setTitle('Simple');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel2007)
$filename = "Export_Detained_Students_".$students[0]['acedmicyear'].".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $filename);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
