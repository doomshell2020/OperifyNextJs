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
<table width="100%" border="0" >
<tr>
<td width="10%" style="border-right:none; text-align:left">
<img src="images/L_58839.gif" alt="" border="0" style="width: 700%; display:block; ">
</td>
<td align="left" style="border-left:none;" width="70%">
<small style="display:block; color:#000; font-size:10px;">
<b>Sanskar School</b>
</small><br>
<small style="display:block; color:#000; font-size:8px;"> Affiliated to CBSE,Delhi<br>
 Vishwamitra Marg, Defence Colony<br> Sirsi Road, Jaipur(Rajasthan) 302012</small>
<br>
<span style="display:block; color:#000; font-size|:10px;"> Ph. No.:&nbsp;0141 - 2246189,2357844</span><br>


<br>
</td>';


if(isset($classess) && !empty($classess)){ 
		$findstudentrfidclassss=0;
		$findstudentwithrfidclassss=0;
		$findstudentamountcnt=0;
			foreach($classess as $element) {
			
			$s_id=$element['class_id'];
				 $c_id=$element['section_id'];
			 $findstudentrfidclassss +=$this->Comman->findstudentrfidclass($element['class_id'],$element['section_id']);
			 
			  $findstudentwithrfidclassss +=$this->Comman->findstudentcrfidclass($element['class_id'],$element['section_id']);
			  
			  $findstudentamountcnt +=$this->Comman->findstudentcountclass($element['class_id'],$element['section_id']);
			}
			
			
			
			
			}
$html.='<td align="left" style="border-left:none;" width="30%">
<small style="display:block; color:#000; font-size:10px;">
<b>Total Students: '; 

$html.=$findstudentamountcnt;

$html.='</b>
</small><br>
<small style="display:block; color:#000; font-size:10px;">
<b>With Id Card: &nbsp;&nbsp;&nbsp;&nbsp;'; 

$html.=$findstudentwithrfidclassss;

$html.='</b>
</small><br>
<small style="display:block; color:#000; font-size:10px;">
<b>Without Id Card: '; 

$html.=$findstudentrfidclassss;

$html.='</b>
</small>

</td>
</tr>
</table>


<table width="100%" border="1" align="center">
<tr>
 <td align="center" colspan="8" ><b>Strength Report ';
 $html.='</b></td>
    
    </tr>
<tr>
 <th align="center"  width="5%" >&nbsp;<b>S.No</b></th>
 <th align="left" width="25%">&nbsp;<b>Class</b></th>
     <th align="left" width="25%">&nbsp;<b>Section</b></th>
        <th align="left" width="20%">&nbsp;<b>Strength</b></th>
    <th align="left" width="25%">&nbsp;<b>Without ID Card</b></th>
    </tr>';
    
  
$counter=1;
    if(isset($classess) && !empty($classess)){ 
			foreach($classess as $key=>$element) { 
				 $totalsum=0;
 $s_id=$element['class_id'];
				 $c_id=$element['section_id'];
				 $html.='<tr>
			     <td align="center"  >&nbsp;';
			     
			     	 $html.=$counter;
			     	  $html.='</td>';
			     
			      $html.='<td align="left" >&nbsp;';
			
		 $class=$this->Comman->findclasses($s_id);
  $section=$this->Comman->findsections($c_id);
			              //echo $class[0]['title']." ".$section[0]['title'];
			$html.=$class[0]['title'];
			
			 
			  $html.='</td>';
			  
			  
			    $html.='<td align="left" >&nbsp;';
		

			$html.=$section[0]['title'];
			
			 
			  $html.='</td>';
			  $findstudentamount=$this->Comman->findstudentcountclass($element['class_id'],$element['section_id']);  
			  $html.='<td align="left" >&nbsp;';
		

			$html.=$findstudentamount;
			
			 
			  $html.='</td>';
			 
			 
			 $findstudentrfidclass=$this->Comman->findstudentrfidclass($element['class_id'],$element['section_id']); 
			 $html.='<td align="left" >&nbsp;';
		

			$html.=$findstudentrfidclass;
			
			 
			  $html.='</td>';
			 
		
		 $html.='</tr>';
		 $counter++; 
		 }
	  } else { 
	   $html.='<tr>';
	    $html.='<td colspan="8" style="text-align:center;">No Data Available</td>';   
	  $html.='</tr>';
	 } 
	     
	     
	   
$html.='</table>
</body>
</html>';

$pdf->WriteHTML($html, true, false, true, false, '');
ob_end_clean();
echo $pdf->Output('Result');
exit;
?>



?>
