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

if($issued_books['0']['holder_type_id']=='Student'){
$objPHPExcel->setActiveSheetIndex(0)

    ->setCellValue('A1', 'S.No.')
    ->setCellValue('B1', 'ASN No')
    ->setCellValue('C1', 'Book Name')
    ->setCellValue('D1', 'ISBN No')
    ->setCellValue('E1', 'Holder Name')
    ->setCellValue('F1', 'Holder Type.')
    ->setCellValue('G1', 'Class-Section')
    ->setCellValue('H1', 'Language')
    ->setCellValue('I1', 'Contact No.')
    ->setCellValue('J1', 'Issue Date')
    ->setCellValue('K1', 'Due Date')
    ->setCellValue('L1', 'Duration');
    
}else{
	$objPHPExcel->setActiveSheetIndex(0)

    ->setCellValue('A1', 'S.No.')
    ->setCellValue('B1', 'ASN No')
    ->setCellValue('C1', 'Book Name')
    ->setCellValue('D1', 'ISBN No')
    ->setCellValue('E1', 'Holder Name')
    ->setCellValue('F1', 'Holder Type.')
    ->setCellValue('G1', 'Language')
    ->setCellValue('H1', 'Contact No.')
    ->setCellValue('I1', 'Issue Date')
    ->setCellValue('J1', 'Due Date')
    ->setCellValue('K1', 'Duration');

	
	
}
$counter = 1;

if (isset($issued_books) && !empty($issued_books)) {
    foreach ($issued_books as $i =>$service) {
		
        $ii = $i + 2;
        
     
       
      $objPHPExcel->getActiveSheet()->setCellValue('A' . $ii, $counter++);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $ii, $service['asn_no']);
       
		 $objPHPExcel->getActiveSheet()->setCellValue('C' . $ii,str_replace(',', ' ', $service['name']));
		 $objPHPExcel->getActiveSheet()->setCellValue('D' . $ii, trim($service['ISBN_NO']));
		 
		 if(isset($service['holder_id'])){ 
			 if($service['holder_type_id']!='Employee') {
			
			    $stu=$this->Comman->getthisstudent($service['holder_id'],$service['board']);
			    
	
			  if($stu) { 
			  $result= $stu['enroll'].'-'.$stu['fname'].' '.$stu['middlename'].' '.$stu['lname'];
			    }else{ 
			    $result= ucfirst($service['holder_name']);  
			     }  }else{ 
			     
			      $result=ucfirst($service['holder_name']);  
			      } }else{ 
			      $result='N/A';}
		 $objPHPExcel->getActiveSheet()->setCellValue('E' . $ii, $result);
		 $objPHPExcel->getActiveSheet()->setCellValue('F' . $ii, $service['holder_type_id']);
		$lan=$this->Comman->findlang($service['lang']);
    	$d1 = date('d-m-Y',strtotime($service['issue_date']));
	    	$d2 = date('d-m-Y',strtotime($service['due_date']));
	if($service['holder_type_id']=='Student'){
		   $cid=$service['class_id'];
    $sid=$service['section_id'];
    $cname=$this->Comman->findclass123($cid);
    $sname=$this->Comman->findsection123($sid);
     $csname=$cname['title'].' - '.$sname['title'];
			 $objPHPExcel->getActiveSheet()->setCellValue('G' . $ii, $csname);
			 $objPHPExcel->getActiveSheet()->setCellValue('H' . $ii, $lan['language']);
			 $objPHPExcel->getActiveSheet()->setCellValue('I' . $ii, $service['sms_mobile']);
			 $objPHPExcel->getActiveSheet()->setCellValue('J' . $ii, $d1);
			 $objPHPExcel->getActiveSheet()->setCellValue('K' . $ii, $d2);
			 	if(!isset($service['status'])) {
			if( !empty( $d1 ) && !empty( $d2 ) )
			{
				if( $service['NumberOfDays'] <= 0 )
				{ 
					$result2="Left: ".abs($service['NumberOfDays'])." day(s)";
				}
				else
				{
					$result2="Overdue: ".$service['NumberOfDays']." day(s)";
				} 
			}
			else
			{
				$result2="N/A";
			}
		} else {
			$result2="LOST";
			
		}
			 $objPHPExcel->getActiveSheet()->setCellValue('L' . $ii, $result2);

		}else{
			
				
    
			
			 $objPHPExcel->getActiveSheet()->setCellValue('G' . $ii, $lan['language']);
			 $objPHPExcel->getActiveSheet()->setCellValue('H' . $ii, $service['mobile']);
			 $objPHPExcel->getActiveSheet()->setCellValue('I' . $ii, $d1);
			 $objPHPExcel->getActiveSheet()->setCellValue('J' . $ii, $d2);
			 	if(!isset($service['status'])) {
			if( !empty( $d1 ) && !empty( $d2 ) )
			{
				if( $service['NumberOfDays'] <= 0 )
				{ 
					$result2="Left: ".abs($service['NumberOfDays'])." day(s)";
				}
				else
				{
					$result2="Overdue: ".$service['NumberOfDays']." day(s)";
				} 
			}
			else
			{
				$result2="N/A";
			}
		} else {
			$result2="LOST";
			
		}
			 $objPHPExcel->getActiveSheet()->setCellValue('K' . $ii, $result2);
			
			
			
		}
	
			
				
       

    }

}

// Rename sheet
//$objPHPExcel->getActiveSheet()->setTitle('Simple');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel2007)
$filename = "Issuebookreport_".date('d-m-Y').".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $filename);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
