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


$objPHPExcel->setActiveSheetIndex(0)

    ->setCellValue('A1', 'S.No.')
    ->setCellValue('B1', 'Class*')
    ->setCellValue('C1', 'Section*')
    ->setCellValue('D1', 'Class_Teacher*')
    ->setCellValue('E1', 'Co-Class_Teacher*');
  
    
$counter = 1;
     

if (isset($class) && !empty($class)) {
    foreach ($class as $i => $people) {
       //pr($people); die;
        $ii = $i + 2;

        $objPHPExcel->getActiveSheet()->setCellValue('A' . $ii, $counter++);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $ii, $people['class']["title"]);
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $ii, $people['section']["title"]);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $ii, $people['employee']["fname"]);

        $co_class_teacher_type = "2";
        $co_classteacher =$this->Comman->co_class_teacherfind($people['class_id'],$people['section_id'], $co_class_teacher_type);

        $co_classteacher_name =$this->Comman->co_class_teachername($co_classteacher['teach_id']);
      //  pr($co_classteacher_name); die;

        $objPHPExcel->getActiveSheet()->setCellValue('E' . $ii, $co_classteacher_name['fname']);
      
    }

}

// Rename sheet
//$objPHPExcel->getActiveSheet()->setTitle('Simple');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel2007)
$filename = "Class_Co_Class Details.xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $filename);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
