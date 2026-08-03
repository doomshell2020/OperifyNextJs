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
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);

$objPHPExcel->setActiveSheetIndex(0)

    ->setCellValue('A1', 'S.No.')
    ->setCellValue('B1', 'Name')
    ->setCellValue('C1', 'Email')
    ->setCellValue('D1', 'Mobile')
    ->setCellValue('E1', 'F/Husband Name')
    ->setCellValue('F1', 'DOB')
    ->setCellValue('G1', 'Joining Date');
   

$counter = 1;
//pr($employee);die;
if (isset($employee) && !empty($employee)) {
    foreach ($employee as $i => $people) {
        $emp_sal = $this->Comman->findemplobasic($people['id']);
        $ii = $i + 2;

        $objPHPExcel->getActiveSheet()->setCellValue('A' . $ii, $counter++);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $ii, ucwords(strtolower($people["fname"] . ' ' . $people["middlename"] . ' ' . $people["lname"])));
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $ii, $people["username"]);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $ii, $people["mobile"]);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $ii, $people["f_h_name"]);
        if(!empty($people["dob"])){
            $dob = date("d-m-Y", strtotime($people["dob"]));
        }
        else{
            $dob='';
        }
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $ii, $dob);
        if(!empty($people["joiningdate"])){
            $datejoin = date("d-m-Y", strtotime($people["joiningdate"]));
        }
        else{
            $datejoin='';
        }
        $objPHPExcel->getActiveSheet()->setCellValue('G' . $ii, $datejoin);
       
    }

}

// Rename sheet
//$objPHPExcel->getActiveSheet()->setTitle('Simple');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel2007)
$filename = "Teacher Details.xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $filename);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
