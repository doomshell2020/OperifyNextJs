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
         $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
         $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
         $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
         $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
         $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
 
         $objPHPExcel->setActiveSheetIndex(0)
             ->setCellValue('A1', 'S.No.')
             ->setCellValue('B1', 'Restoration Date')
             ->setCellValue('C1', 'Restored By')
             ->setCellValue('D1', 'Enroll')
             ->setCellValue('E1', 'Student Name');
         $ii = 2;
         $cnt = 1;
         foreach ($restores as $restore) { 
             $name = '-'; 
             if (isset($restore['student']['fname'])) {
                 $name = $restore['student']['fname'].' '.$restore['student']['middlename'].' '.$restore['student']['lname'];
             } elseif(isset($restore['drop_out_student']['fname'])) {
                 $name = $restore['drop_out_student']['fname'].' '.$restore['drop_out_student']['middlename'].' '.$restore['drop_out_student']['lname'];
             }
             $enroll = '-';
             if (isset($restore['student']['enroll'])) {
                 $enroll = $restore['student']['enroll'];
             } elseif (isset($restore['drop_out_student']['enroll'])) {
                 $enroll = $restore['drop_out_student']['enroll'];
             }
             $objPHPExcel->getActiveSheet()->setCellValue('A' . $ii, $cnt);
             $objPHPExcel->getActiveSheet()->setCellValue('B' . $ii, date('d-M-Y', strtotime($restore['created'])));
             $objPHPExcel->getActiveSheet()->setCellValue('C' . $ii, $restore['user']['email']);
             $objPHPExcel->getActiveSheet()->setCellValue('D' . $ii, $enroll);
             $objPHPExcel->getActiveSheet()->setCellValue('E' . $ii, $name);
             $ii++;
         }
         $objPHPExcel->setActiveSheetIndex(0);
 // Redirect output to a client’s web browser (Excel2007)
         $filename = "Restore Report(" . date('d-M-Y') . ").xlsx";
         header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
         header('Content-Disposition: attachment;filename=' . $filename);
         header('Cache-Control: max-age=0');
         $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
         $objWriter->save('php://output');
         exit;
?>