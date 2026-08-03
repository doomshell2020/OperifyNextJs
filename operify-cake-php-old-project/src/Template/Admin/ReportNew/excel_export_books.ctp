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
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);


$objPHPExcel->setActiveSheetIndex(0)

    ->setCellValue('A1', 'S.No.')
    ->setCellValue('B1', 'ASN No')
    ->setCellValue('C1', 'ISBN No')
    ->setCellValue('D1', 'Bill No.')
    ->setCellValue('E1', 'Bill Date')
    ->setCellValue('F1', 'Book Name')
    ->setCellValue('G1', 'Category')
    ->setCellValue('H1', 'Cupboard')
    ->setCellValue('I1', 'Cupboard Shelf')
    ->setCellValue('J1', 'Language')
    ->setCellValue('K1', 'Author')
    ->setCellValue('L1', 'Status');
    

$counter = 1;

if (isset($books_data) && !empty($books_data)) {
    foreach ($books_data as $i =>$service) {
		if($service['typ']=='1'){
        $percat = $this->Comman->findperiodicalmaster($service['periodic_category_id']);

        
	}
        $ii = $i + 2;
      $objPHPExcel->getActiveSheet()->setCellValue('A' . $ii, $counter++);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $ii, $service['accsnno']);
        if($service['typ']=='1'){
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $ii, $percat['ISBN_NO']);
	}else{
		 $objPHPExcel->getActiveSheet()->setCellValue('C' . $ii, $service['ISBN_NO']);
		
	}
	
			 $objPHPExcel->getActiveSheet()->setCellValue('D' . $ii, $service['bilno']);
			 $objPHPExcel->getActiveSheet()->setCellValue('E' . $ii, date('d-m-Y',strtotime($service['bildt'])));
		 $objPHPExcel->getActiveSheet()->setCellValue('F' . $ii, str_replace(',', ' ', $service['b_name']));
	
	
	if($service['typ']=='1'){
				 $objPHPExcel->getActiveSheet()->setCellValue('G' . $ii, $percat['ISBN_NO']);
		
		}else {
				 $objPHPExcel->getActiveSheet()->setCellValue('G' . $ii, $service['b_category']);
			
		}
		
			 $objPHPExcel->getActiveSheet()->setCellValue('H' . $ii, $service['cupboard']);
			 $objPHPExcel->getActiveSheet()->setCellValue('I' . $ii, $service['shelf']);
		
        
        
        if($service['typ']=='1'){
			    $lasd = $this->Comman->findlang($percat['blang']);
			 $objPHPExcel->getActiveSheet()->setCellValue('J' . $ii, $lasd['language']);
			     
				
			
				}else {
				
			 $lasd = $this->Comman->findlang($service['blang']);
			 $objPHPExcel->getActiveSheet()->setCellValue('J' . $ii, $lasd['language']);
				}
				
						if($service['typ']=='1'){
									 $objPHPExcel->getActiveSheet()->setCellValue('K' . $ii, $percat['author']);
			
			}else {
				
					 $objPHPExcel->getActiveSheet()->setCellValue('K' . $ii, $service['author']);
			
}

if($srch_status != 'ALL') {
			if($srch_status == 'Overdue')
			{
				$resulover="Overdue: ".$service['NumberOfDays']." day(s)";
				 $objPHPExcel->getActiveSheet()->setCellValue('L' . $ii, $resulover);
				
			}
			else
			{
				 $objPHPExcel->getActiveSheet()->setCellValue('L' . $ii, $service['status']);
			
			}
		}
				
       

    }

}

// Rename sheet
//$objPHPExcel->getActiveSheet()->setTitle('Simple');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel2007)
$filename = "Books-Report_".date('d-m-Y').".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $filename);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;