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

$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A1', 'Sr.No.')
    ->setCellValue('B1', 'Student Name')
    ->setCellValue('C1', 'Fathers Name')
    ->setCellValue('D1', 'Class')
    ->setCellValue('E1', 'Section')
    ->setCellValue('F1', 'Disc. Scheme')
;

$counter = 1;
if (isset($order) && !empty($order)) {
    foreach ($order as $i => $work) { //pr($work); die;
        $ii = $i + 2;
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $ii, $work['enroll']);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $ii, $work['fname']);
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $ii, $work['fathername']);
if($work['class']['title']){
    $objPHPExcel->getActiveSheet()->setCellValue('D' . $ii, $work['class']['title']);
    $objPHPExcel->getActiveSheet()->setCellValue('E' . $ii, $work['section']['title']);
}else{
    $rte_class = $this->Comman->rte_class_get($work['class_id']);
    $rte_section =$this->Comman->rte_section_get($work['section_id']);
    $objPHPExcel->getActiveSheet()->setCellValue('D' . $ii, $rte_class['title']);
    $objPHPExcel->getActiveSheet()->setCellValue('E' . $ii, $rte_section['title']);
}
       

        $objPHPExcel->getActiveSheet()->setCellValue('F' . $ii, $work['category']);

} 
}


$objPHPExcel->setActiveSheetIndex(0);
$filename = "RTE_Students.xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $filename);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
