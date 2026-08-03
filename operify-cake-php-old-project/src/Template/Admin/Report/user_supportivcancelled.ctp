<?php
class xtcpdf extends TCPDF {
 
}


 //$subject=$this->Comman->findexamsubjectsresult($students['id'],$students['section']['id'],$students['acedmicyear']);

   $this->set('pdf', new TCPDF('L','mm','A4'));
$pdf = new TCPDF("P", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->AddPage();



$pdf->SetFont('', '', 9, '', 'false');

$html.='
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Result</title><link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">';
$html.='</head>
<body style="font-family:"trebuchet MS",Arial,Helvetica,sans-serif;">
<table width="100%" align="left">
<tr>
<td style="text-align:left" width="50%" >
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/logo.png" alt="" border="0" style="display:block;" 
width="130px" height="60px;"><br>
<span style="display:block; color:#000; font-size:8px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<u>(An ISO 
9001:2000 Certified School)<br>Affiliated to CBSE Delhi (Affiliation 
no.1730236)</u></span>
</td>

<td style="text-align:left;" width="50%" align="right"><br><br>
Vishwamitra Marg, Near Hanuman Nagar 
Ext.,<br>&nbsp;&nbsp;&nbsp;&nbsp;Sirsi Road, Jaipur-302012&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br>
Phone 
 : &nbsp;2246189,2357844 &nbsp;&nbsp;&nbsp;&nbsp;Fax :-  
0141-2245602<br>&nbsp;&nbsp;&nbsp;&nbsp;Email 
: <u>
info@sanskarjaipur.com,</u><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Website : www.sanskarjaipur.com
</td>


</tr>




</table><br><br><table width="100%" border="1" align="center">

<tr>
 <td align="center" colspan="9" ><b>'.$status.' Receipts From ';
 if($datefrom && $dateto2 && $datefrom!='1970-01-01' && $dateto2!='1970-01-01'){
	 
	 $datefrom=date('d-m-Y',strtotime($datefrom));
		$dateto2=date('d-m-Y',strtotime($dateto2));
 $html.=$datefrom." To ".$dateto2;
}else{
		 $datefrom="01-04-2018";
		 $dateto2=date('d-m-Y');
	 $html.=$datefrom." TO ".$dateto2;
}
 $html.='</b></td></tr>
<tr>
 <th align="center"  width="5%" >&nbsp;<b>S.No</b></th>
    <th align="left" width="7%" >&nbsp;<b>Recpt.</b></th>
    <th align="left" width="10%" >&nbsp;<b>Date</b></th>
    <th align="left" width="7%">&nbsp;<b>Sr.No.</b></th>
    <th align="left" width="22%">&nbsp;<b>Name of Student</b></th>
    <th align="left" width="22%">&nbsp;<b>Fathers Name</b></th>
    <th align="left" width="10%">&nbsp;<b>Class</b></th>
    <th align="left" width="7%">&nbsp;<b>Amt.</b></th>
    <th align="left" width="10%">&nbsp;<b>Status</b></th>
    </tr>';
    
  
$counter=1;

$total=0;
    if(isset($Classectionsfees) && !empty($Classectionsfees)){ 
			foreach($Classectionsfees as $key=>$element) { 
				 $totalsum=0;
$s_id=$element['student']['class_id'];
		 $c_id=$element['student']['section_id'];
				 $html.='<tr>
			     <td align="center"  >&nbsp;';
			     
			     	 $html.=$counter;
			     	  $html.='</td>';
			     
			    
			   
					 
			     $html.='<td align="left"  >&nbsp;';
			
		

			$html.=$element['recipetno'];
			
			 
			  $html.='</td>';
			  
			  			 
			     $html.='<td align="left"   >&nbsp;';
			
		

			$html.=date('d-m-Y',strtotime($element['paydate']));
			
			 
			  $html.='</td>';
			       $html.='<td align="left"  >&nbsp;';
			
		if($element['type']=="Fee"){

			$html.=$element['student']['enroll'];
		}else {
			if($element['type']=="Prospectus"){
				 $prospect=$this->Comman->findprospectus($element['recipetno'],$element['formno']);  
						$cl=$this->Comman->findclass($prospect['class_id']);
						$html.="Prospectus";
						 
						} 
						if($element['type']=="Registration"){
							$applicant=$this->Comman->findapplicant($element['recipetno'],$element['formno']); $cls=$this->Comman->findclass($applicant['class_id']);
						$html.="Registration"; 
						}
			
		}
			 
			  $html.='</td>';
			      $html.='<td align="left"   >&nbsp;';
			
		
if($element['type']=="Fee"){
			$html.= $element['student']['fname']." ".$element['student']['middlename']." ".$element['student']['lname'];
			
		 }else{
						
						   if($element['type']=="Prospectus"){ 
							   
							   $prospect=$this->Comman->findprospectus($element['recipetno'],$element['formno']);
							    $html.= $prospect['s_name']; 
						} 
						
						if($element['type']=="Registration"){
							 $applicant=$this->Comman->findapplicant($element['recipetno'],$element['formno']); 
							$html.= $applicant['fname']." ".$applicant['middlename']." ".$applicant['lname']; 
							
							}  }
			  $html.='</td>';
			  $html.='<td align="left"   >&nbsp;';
			
		if($element['type']=="Fee"){

			$html.= $element['student']['fathername'];
		}else{
			 if($element['type']=="Prospectus"){ 
							 $prospect=$this->Comman->findprospectus($element['recipetno'],$element['formno']);
							 
							  $html.= $prospect['fee_submittedby']; 
						
						 
						} if($element['type']=="Registration"){
							 $applicant=$this->Comman->findapplicant($element['recipetno'],$element['formno']); $html.= $applicant['f_name']; }
			
		}
			 
			  $html.='</td>';
			  	  $html.='<td align="left" >&nbsp;';
			if($element['type']=="Fee"){
		 $class=$this->Comman->findclasses($s_id);
  $section=$this->Comman->findsections($c_id);
			              //echo $class[0]['title']." ".$section[0]['title'];
			$html.=$class[0]['title']."-".$section[0]['title'];
			
		 }else{
			  if($element['type']=="Prospectus"){
				  
				   $prospect=$this->Comman->findprospectus($element['recipetno'],$element['formno']);  
						$cl=$this->Comman->findclass($prospect['class_id']);
						$html.=$cl['title'];
						 
						} if($element['type']=="Registration"){ $applicant=$this->Comman->findapplicant($element['recipetno'],$element['formno']); $cls=$this->Comman->findclass($applicant['class_id']);
						$html.=$cls['title'];
							 
							  ?></b><?  }
			 
			 
		 }
			  $html.='</td>';
			  $html.='<td align="left">&nbsp;';
		

			$html.=$element['deposite_amt'];
			$total +=intval($element['deposite_amt']);
			 
			  $html.='</td>';
			  
			  	  $html.='<td align="left">&nbsp;';
		
if($element['status']=='N'){
			$html.="Cancelled";
		}else{
			
			$html.="Deposited";
		}
			 
			  $html.='</td>';
			 
			 
		
		 $html.='</tr>';
		 $counter++; 
		 }
	  } else { 
	   $html.='<tr>';
	    $html.='<td colspan="8" style="text-align:center;">No Data Available</td>';   
	  $html.='</tr>';
	 } 
	 
	  $html.='<tr><td colspan="7"  style="text-align: right;
    font-weight: bold;
    color: red;">Grand Total &nbsp;</td><td colspan="3" style="text-align: left;
    font-weight: bold;
    color: red;"> &nbsp;';
    
 setlocale(LC_MONETARY, 'en_IN');
$total = money_format('%!i', $total);  
$html.=$total;

$html.='</td></tr>';
	 
	 
$html.='</table>
</body>
</html>';
$pdf->lastPage();

$pdf->writeHTML($html, true, false, true, false, '');
ob_end_clean();
echo $pdf->Output('RecipetReport.pdf');
exit;
?>
