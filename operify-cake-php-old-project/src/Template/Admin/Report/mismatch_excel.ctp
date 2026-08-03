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
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);

$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A1', 'S.No.')
    ->setCellValue('B1', 'Date')
    ->setCellValue('C1', 'Sr.No.')
    ->setCellValue('D1', 'Pupil Name')
    ->setCellValue('E1', 'Class')
    ->setCellValue('F1', 'Section')
;

$counter = 1;
$totalamt = '';
$totaldis = '';
$grandtotal = '';
if (isset($res) && !empty($res)) {
    $ii = 2;
    $count = 1;foreach ($res as $value) {
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $ii, $count);

        $objPHPExcel->getActiveSheet()->setCellValue('B' . $ii, date('d-m-Y', strtotime($value['date'])));
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $ii, $value['stud_id']);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $ii, $value['student']['fname'] . "\x20" . $value['student']['middlename'] . $value['student']['lname']);

        $class = $this->comman->findclass123($value['class_id']);

        $objPHPExcel->getActiveSheet()->setCellValue('E' . $ii, $class['title']);
        $section = $this->comman->findsection123($value['section_id']);

        $objPHPExcel->getActiveSheet()->setCellValue('F' . $ii, $section['title']);

        $count++;
        $ii++;
    }} else {
    $objPHPExcel->getActiveSheet()->mergeCells('B2:H2');
    $objPHPExcel->getActiveSheet()->setCellValue('B2', 'No Data Available');
}

// Rename sheet
//$objPHPExcel->getActiveSheet()->setTitle('Simple');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel2007)
$filename = "Mismatch Report.xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $filename);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;