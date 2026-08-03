<?php 
//~ pr($result2);
//~ pr($resultf); die;
$objPHPExcel = new PHPExcel();
// Set properties
$objSheet = $objPHPExcel->getActiveSheet();
$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
							 ->setLastModifiedBy("Maarten Balliauw")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Test result file");
// Miscellaneous glyphs, UTF-8

$cntletters=getNameFromNumber(count($headerRow2)-1); 							 
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


$cntletter=getNameFromNumber(count($headerRow2)-1); 



$myarray=createColumnsArray($cntletter);
$m=1;

//~ pr($myarray); die;


for($i=0;$i<count($myarray);$i++){


	
		
   $objPHPExcel->getActiveSheet()->getColumnDimension($myarray[$i])
       ->setAutoSize(true);
     
       
  $objPHPExcel->setActiveSheetIndex()->setCellValue($myarray[$i].$m, $headerRow2[$i]);     
  

       
}




	$counter=1;
			          if(isset($result) && !empty($result)){ 
              foreach($result as $yu=>$people){
			
	$ii = $yu+2;
 for($i=0;$i<count($people);$i++){
	 
	  $objPHPExcel->setActiveSheetIndex()->setCellValue($myarray[$i].$ii, $people[$i]);   
  
	
}


}

}



	
			

// Rename sheet
//$objPHPExcel->getActiveSheet()->setTitle('Simple');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel2007)
$dates=date('d-m-Y');
$filename = "Bank_Report_".$dates.".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename='.$filename);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
?>
