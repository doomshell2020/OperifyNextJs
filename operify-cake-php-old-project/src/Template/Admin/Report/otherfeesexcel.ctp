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

$header[]="S.No.";
$header[]="Sr.No.";
$header[]="Class";
$header[]="Pupil Name";
$header[]="Paydate";
$header[]="Receipt No.";
$header[]="Che./DD No.";
$header[]="Ref no.";


     foreach($restitle as $ky=>$item){ 
	$header[]=ucwords(strtolower($item['title']));	  
		   } 
$header[]="Disc.Fee";
$header[]="Total";
$header[]="Remarks";





$cntletters=getNameFromNumber(count($header)); 							 
$objPHPExcel->getActiveSheet()->getProtection()->setSort(true);
$objPHPExcel->getActiveSheet()->getProtection()->setInsertRows(true);
$objPHPExcel->getActiveSheet()->getProtection()->setFormatCells(true);					 
						 

function createColumnsArray($end_column, $first_letters = '')
{
 $columns = array();
 $length = strlen($end_column);
 $letters = range('A', 'Z');

 // Iterate over 26 letters.
 foreach ($letters as $letter) {
     // Paste the $first_letters before the next.
     $column = $first_letters . $letter;

     // Add the column to the final array.
     $columns[] = $column;

     // If it was the end column that was added, return the columns.
     if ($column == $end_column)
         return $columns;
 }

 // Add the column children.
 foreach ($columns as $column) {
     // Don't itterate if the $end_column was already set in a previous itteration.
     // Stop iterating if you've reached the maximum character length.
     if (!in_array($end_column, $columns) && strlen($column) < $length) {
         $new_columns = createColumnsArray($end_column, $column);
         // Merge the new columns which were created with the final columns array.
         $columns = array_merge($columns, $new_columns);
     }
 }

 return $columns;
}						 
function getNameFromNumber($num) {
    $numeric = $num % 26;
    $letter = chr(65 + $numeric);
    $num2 = intval($num / 26);
    if ($num2 > 0) {
        return getNameFromNumber($num2 - 1) . $letter;
    } else {
        return $letter;
    }
}


$cntletter=getNameFromNumber(count($header)); 



$myarray=createColumnsArray($cntletter);
$m=1;




for($i=0;$i<count($myarray);$i++){


	
		
   $objPHPExcel->getActiveSheet()->getColumnDimension($myarray[$i])
       ->setAutoSize(true);
     
       
  $objPHPExcel->setActiveSheetIndex()->setCellValue($myarray[$i].$m, $header[$i]);     
  

       
}
$totaldis=0;
$grandtotal=0;
$counter=1;
     $result=array();
if (isset($res) && !empty($res)) {
    foreach ($res as $i => $work) {
 
     $resultf=array();
      $resultf[]=$counter++;
        if (isset($work['s_id']) && !empty($work['s_id'])) {
            $s_id = $work['s_id'];
        } else {
            $s_id = '--';
        }
        if (isset($work['class_id']) && !empty($work['class_id'])) { 
			$class = $this->Comman->findclasses($work['class_id']); 
			$c_id = $class[0]['title'];
			} else { $c_id = "--";}
       $resultf[]=$s_id;
       $resultf[]=$c_id;
       $resultf[]=$work['pupilname'];
       $resultf[]=date('d-m-Y', strtotime($work['paydate']));
     
        $resultf[]=$work['receipt_no'];
    
        
        if (isset($work['cheque_no']) && !empty($work['cheque_no'])) {
            $cheque_no = $work['cheque_no'];
        } else {
            $cheque_no = '--';
        }
        
            $resultf[]=$cheque_no;

        if (isset($work['ref_no']) && !empty($work['ref_no'])) {
            $ref_no = $work['ref_no'];
        } else {
            $ref_no = '--';
        }
        
        
          
        $totaldis += $work['discount'];
        $grandtotal += $work['total'];
         $resultf[]=$ref_no;
         foreach($restitle as $ky=>$item){ 
		
 if($work['title']==$item['title']) {
	 $resultf[]=$work['amount']; 
	 
	 
 }else{
	 
		 $resultf[]=0; 
	 } }
        
         $resultf[]=$work['discount'];
         $resultf[]=$work['total'];
         
         if($work['remarks']){
         $resultf[]=$work['remarks'];
  }else{
         $resultf[]='--';	  
  }
      $result[]=$resultf;
    }
    

}


	$counter=1;
			          if(isset($result) && !empty($result)){ 
              foreach($result as $yu=>$people){
			
	$ii = $yu+2;
 for($i=0;$i<count($people);$i++){
	 if($i==4){
		
		 $rs=date('d-m-Y',strtotime($people[$i]));
		  $objPHPExcel->setActiveSheetIndex()->setCellValue($myarray[$i].$ii, $rs);  	 
		 
	 }else{
		 
	  $objPHPExcel->setActiveSheetIndex()->setCellValue($myarray[$i].$ii, $people[$i]);   
  }
	
} 


}



$gt = $ii + 1;
    $objPHPExcel->getActiveSheet()->mergeCells('B' . $gt . ':I' . $gt);
    $objPHPExcel->getActiveSheet()->setCellValue('B' . $gt, 'Grand Total -'.$grandtotal);
 

    }
// Rename sheet
//$objPHPExcel->getActiveSheet()->setTitle('Simple');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel2007)
$filename = "OtherFees_report_".date('d-m-Y').".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $filename);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
