<?php
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
$header=array();
foreach($resul[0] as $gg=>$tty){
	$header[]=$gg;

}


$cntletters=getNameFromNumber(count($header)-1);
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


$cntletter=getNameFromNumber(count($header)-1);



$myarray=createColumnsArray($cntletter);
$m=1;




for($i=0;$i<count($myarray);$i++){




   $objPHPExcel->getActiveSheet()->getColumnDimension($myarray[$i])
       ->setAutoSize(true);

  if($header[$i]=='enroll') { $header[$i] ="Scholar.No.";  }
      if($header[$i]=='fname') { $header[$i] ="Name";  }
      if($header[$i]=='username') { $header[$i] ="Email";  }

      if($header[$i]=='gender') { $header[$i] ="Gender"; }
      if($header[$i]=='dob') { $header[$i] ="DOB"; }
      if($header[$i]=='height')  { $header[$i] ="Height"; }
  if($header[$i]=='weight')  { $header[$i] ="Weight"; }
  if($header[$i]=='oldenroll')  { $header[$i] ="Old enroll"; }
    if($header[$i]=='adaharnumber')  { $header[$i] ="Adaharnumber"; }
  if($header[$i]=='cast')  { $header[$i] ="Cast"; }
   if($header[$i]=='religion')  { $header[$i] ="Religion"; }


    if($header[$i]=='category')  { $header[$i] ="Category"; }
     if($header[$i]=='bloodgroup')  { $header[$i] ="Bloodgroup"; }
      if($header[$i]=='disability')  { $header[$i] ="Disability"; }
      if($header[$i]=='mother_tounge')  { $header[$i] ="MotherTounge"; }
       if($header[$i]=='address')  { $header[$i] ="Address"; }
       if($header[$i]=='rf_id')  { $header[$i] ="RF ID"; }
       if($header[$i]=='rfidhexcode')  { $header[$i] ="HEX CODE"; }

      if($header[$i]=='mobile') { $header[$i] ="Mobile"; }

      if($header[$i]=='sms_mobile') { $header[$i] ="SMSMobile"; }
      if($header[$i]=='f_phone') { $header[$i] ="FatherPhone"; }
      if($header[$i]=='m_phone') { $header[$i] ="MotherPhone"; }
      if($header[$i]=='admissionyear') { $header[$i] ="AdmissionYear"; }
      if($header[$i]=='acedmicyear') { $header[$i] ="AcademicYear"; }
      if($header[$i]=='created') { $header[$i] ="AdmissionDate"; }
      if($header[$i]=='formno') { $header[$i] ="FormNo."; }



      if($header[$i]=='board_id') { $header[$i] ="Board"; }
      if($header[$i]=='admissionclass')  { $header[$i] ="Admission Class"; }
      if($header[$i]=='class_id')  { $header[$i] ="Class"; }
      if($header[$i]=='section_id') {  $header[$i] ="Section"; }
      if($header[$i]=='h_id') {  $header[$i] ="House"; }

      if($header[$i]=='discountcategory') { $header[$i] ="Discount"; }
      if($header[$i]=='is_lc') { $header[$i] ="IsLearningCenter"; }
      if($header[$i]=='is_special') { $header[$i] ="IsSpecial"; }


      if($header[$i]=='fathername')  { $header[$i] ="FatherName"; }
       if($header[$i]=='mothername')  { $header[$i] ="MotherName"; }
       if($header[$i]=='fee_submittedby')  { $header[$i] ="FeeSubmittedBy"; }
       if($header[$i]=='f_qualification')  { $header[$i] ="FatherQualification"; }
       if($header[$i]=='m_qualification')  {$header[$i] ="MotherQualification"; }
       if($header[$i]=='f_occupation')  { $header[$i] ="FatherOccupation"; }
       if($header[$i]=='m_occupation')  { $header[$i] ="MotherOccupation"; }
       if($header[$i]=='middlename') { $header[$i] ="";  }
       if($header[$i]=='lname') { $header[$i] ="";  }
  $objPHPExcel->setActiveSheetIndex()->setCellValue($myarray[$i].$m, $header[$i]);



}


	$counter=1;
		$header=array();
foreach($resul[0] as $gg=>$tty){
	$header[]=$gg;

}




			          if(isset($resul) && !empty($resul)){

foreach($resul as $yu=>$people) {

	$ii = $yu+2;
		$classewrw=$this->Comman->findclasses($people['class_id']);

				$admissionclassclassewrw=$this->Comman->showadmissionclasstitle($people['admissionclass']);
			$admissioncls=$admissionclassclassewrw['title'];


	 $cls=$classewrw[0]['title'];
			$sect=$this->Comman->findsections($people['section_id']);
			 $sections=$sect[0]['title'];
				 $h_id =$people['h_id'];
					  $hid=$this->Comman->findhouse($h_id);
					    $house_id=$this->Comman->findhouse($people['house_id']);
					 if($hid){  $house=$hid['name']; }else if($house_id){  $house=$house_id['name'];  }





		  for($i=0;$i<count($myarray);$i++){

  if($header[$i]=='enroll') { $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii,$people['enroll']);  }
      if($header[$i]=='fname') { $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii,$people['fname'].' '.$people['middlename'].' '.$people['lname']);  }

      if($header[$i]=='username') { $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii,$people['username']); }
      if($header[$i]=='gender') { $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii,$people['gender']); }
      if($header[$i]=='dob') { $sss =date('d-m-Y',strtotime($people['dob']));  $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii,$sss); }
      if($header[$i]=='height')  {  if($people['height']){ $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii,$people['height']);  }else{ $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii,'--');  } }
  if($header[$i]=='weight')  { if($people['weight']){ $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii,$people['weight']);  }else{ $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii,'--');  } }
  if($header[$i]=='oldenroll')  {  if($people['oldenroll']){ if($people['oldenroll']=="600244"){ $people['oldenroll']="6024";  } $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii,$people['oldenroll']);  }else{ $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii,'--');  } }

    if($header[$i]=='adaharnumber')  { if($people['adaharnumber']){  $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii,$people['adaharnumber']); }else{ $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii,'--');  } }
  if($header[$i]=='cast')  {  if($people['cast']){ $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii,$people['cast']); }else{ $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii,'--');  } }
   if($header[$i]=='religion')  {   if($people['religion']){ $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii,$people['religion']); }else{ $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii,'--');  } }


    if($header[$i]=='category')  { $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii,$people['category']); }
     if($header[$i]=='bloodgroup')  { if($people['bloodgroup']){	$objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii,$people['bloodgroup']); }else{ $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii,'--');  }  }
      if($header[$i]=='disability')  {  if($people['disability'] !='0'){


		 $disablity=$this->Comman->finddisability($people['disability']);

		$ss=$disablity['name'];
		$objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii,$ss);

}else{

$objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii,'--');
} }
      if($header[$i]=='mother_tounge')  {  if($people['mother_tounge']){ $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii,$people['mother_tounge']);  }else{ $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii,'--');  } }
       if($header[$i]=='address')  { if($people['address']){  $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii,$people['address']); }else{ $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii,'--');  } }
      if($header[$i]=='rf_id')  { if($people['rf_id']){  $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii,$people['rf_id']); }else{ $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii,'--');  } }
      if($header[$i]=='rfidhexcode')  { if($people['rfidhexcode']){  $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii,$people['rfidhexcode']); }else{ $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii,'--');  } }

      if($header[$i]=='mobile') {  if($people['mobile']){ $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii,$people['mobile']);  }else{ $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii,'--');  }  }

      if($header[$i]=='sms_mobile') { $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii,$people['sms_mobile']); }
      if($header[$i]=='f_phone') {  if($people['f_phone']){ $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii,$people['f_phone']); }else{ $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii,'--');  } }
      if($header[$i]=='m_phone') {  if($people['m_phone']){ $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii,$people['m_phone']); }else{ $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii,'--');  } }
      if($header[$i]=='admissionyear') { $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii,$people['admissionyear']);  }
      if($header[$i]=='acedmicyear') {  $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii,$people['acedmicyear']);  }
      if($header[$i]=='created') { $rs=date('d-m-Y',strtotime($people['created']));
		   $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii,$rs);  }
      if($header[$i]=='formno') {   $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii,$people['formno']);  }



      if($header[$i]=='board_id') { if($people['board_id']=='1'){

$ress= "CBSE";
 }else if($people['board_id']=='2'){

$ress= "CAMBRIDGE";
 } else if($people['board_id']=='3'){

 $ress= "IBDP";
 }

 $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii, $ress); }
      if($header[$i]=='admissionclass')  { if($people['admissionclass']){ $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii, $admissioncls); }else{ $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii,'--');  } }
      if($header[$i]=='class_id')  {  $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii, $cls); }
      if($header[$i]=='section_id') {  $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii, $sections);  }
      if($header[$i]=='h_id') {  $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii, $house);  }

      if($header[$i]=='discountcategory') { if($people['discountcategory']){ $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii, $people['discountcategory']); }else{ $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii,'--');  } }
      if($header[$i]=='is_lc') { $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii, $people['is_lc']); }
      if($header[$i]=='is_special') { $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii, $people['is_special']); }


      if($header[$i]=='fathername')  { if($people['fathername']){ $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii, $people['fathername']);  }else{ $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii,'--');  } }
       if($header[$i]=='mothername')  { if($people['mothername']){ $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii, $people['mothername']); }else{ $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii,'--');  } }

       if($header[$i]=='fee_submittedby')  {  if($people['fee_submittedby']){ $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii, $people['fee_submittedby']); }else{ $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii,'--');  } }

       if($header[$i]=='f_qualification')  {  if($people['f_qualification']){  $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii, $people['f_qualification']); }else{ $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii,'--');  } }

       if($header[$i]=='m_qualification')  {  if($people['m_qualification']){ $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii, $people['m_qualification']);
		   }else{ $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii,'--');  } }

       if($header[$i]=='f_occupation')  { if($people['f_occupation']){ $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii, $people['f_occupation']);
		   }else{ $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii,'--');  } }

       if($header[$i]=='m_occupation')  {  if($people['m_occupation']){ $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii, $people['m_occupation']);
		   }else{ $objPHPExcel->getActiveSheet()->setCellValue($myarray[$i].$ii,'--');  } }







}



}

}





// Rename sheet
//$objPHPExcel->getActiveSheet()->setTitle('Simple');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel2007)
$filename = "ExportStudent_Info_".date('d-m-Y').".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename='.$filename);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
?>
