<?php
$objPHPExcel = new \PHPExcel();
// Set properties
        $style = array(
            'font' => array(
                'bold' => true,
            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );
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
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', 'S.No.');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', 'Date');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C1', 'Student 1 Name');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D1', 'Student 1 old Enroll');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E1', 'Student 1 new Enroll');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F1', 'Student 2 Name');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G1', 'Student 2 old Enroll');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H1', 'Student 2 new Enroll');
        $objPHPExcel->setActiveSheetIndex(0)->getStyle("A1:H1")->applyFromArray($style);
        $cnt=1;
        $row=2;
        foreach($shuffles as $shuffle){
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$row, $cnt);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$row, date('d-M-Y',strtotime($shuffle['created'])));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$row, $shuffle['student_one']['full_name']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$row, $shuffle['from_enroll']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$row, $shuffle['to_enroll']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$row, $shuffle['student_two']['full_name']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$row, $shuffle['to_enroll']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$row, $shuffle['from_enroll']);
            $cnt++;
            $row++;
        }
        $objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web $colser (Excel2007)
$filename = "Shuffling Report(" . date('d-m-Y') . ").xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $filename);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;