<?php 
class xtcpdf extends TCPDF {
 
}


 //$subject=$this->Comman->findexamsubjectsresult($students['id'],$students['section']['id'],$students['acedmicyear']);

   $this->set('pdf', new TCPDF('P','mm','A4'));
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
<td align="left" style="border-left:none;" width="80%">
<small style="display:block; color:#000; font-size:10px;">
<b>Sanskar School</b>
</small><br>
<small style="display:block; color:#000; font-size:8px;"> Affiliated to CBSE,Delhi<br>
 Vishwamitra Marg, Defence Colony<br> Sirsi Road, Jaipur(Rajasthan) 302012</small>
<br>
<span style="display:block; color:#000; font-size|:10px;"> Ph. No.:&nbsp;0141 - 2246189,2357844</span><br>


<br>
</td>
</tr>
</table>


<table width="100%" border="1" align="center">
<tr>

 <td align="center" colspan="8" ><b>PROSPECTUS/REGISTRATION FROM ';
 if($datefrom && $dateto2 && $datefrom!='1970-01-01' && $dateto2!='1970-01-01'){
	 
	 $datefrom=date('d-m-Y',strtotime($datefrom));
		$dateto2=date('d-m-Y',strtotime($dateto2));
 $html.=$datefrom." TO ".$dateto2;
}else{
		 $datefrom="01-04-2018";
		 $dateto2=date('d-m-Y');
	 $html.=$datefrom." TO ".$dateto2;
}
 $html.='</b></td>
    
    </tr>
<tr>
 <th align="center" style="width:5%;">&nbsp;<b>S.No</b></th>
    <th align="left" style="width:7%;" >&nbsp;<b>Rec.No</b>.</th>
    <th align="left" style="width:10%;">&nbsp;<b>Rg/Pro.No</b></th>
    <th align="left" style="width:10%;">&nbsp;<b>Date</b></th>
    <th align="left" style="width:7%;">&nbsp;<b>Session</b></th>
    <th align="left" style="width:20%;">&nbsp;<b>Student Name</b></th>
    <th align="left" style="width:20%;">&nbsp;<b>Fathers Name</b></th>
    <th align="left">&nbsp;<b>Class</b></th>
    <th align="left">&nbsp;<b>Fee</b></th>
    </tr>
    <tr>
 <td colspan="9" align="left">&nbsp;';
 
 	if($stat=='5'){
		  $html.="PROSPECTUS";
		
	}else if($stat=='1'){
		
		  $html.="REGISTRATION";
	}
 
  $html.='</td>
    </tr>';
    
  
$counter=1;
      	if($stat=='5'){
    if(isset($t_enquiry) && !empty($t_enquiry)){ 
    foreach($t_enquiry as $service){  //pr($service);
    
                $html.='<tr><td align="left">&nbsp;';
                
               $html.=$counter;
                
                $html.='</td><td align="left">&nbsp;';
                
                
            if(isset($service['recipietno'])){  $html.=$service['recipietno'];}else{ $html.='N/A'; } 
                
                $html.='</td><td align="left">&nbsp;';
                 if(isset($service['formno'])){ $html.=$service['formno'];}else{ $html.='N/A'; } 
                
                $html.='</td> <td align="left">&nbsp;';
                
          if(isset($service['created'])){ $html.=date('d-m-Y',strtotime($service['created']));}else{ $html.='N/A'; } 
                $html.='</td><td align="left">&nbsp;';
                
               $html.=$acedmic;
                $html.='</td><td align="left">&nbsp;';
                
                if(isset($service['s_name'])){  $html.=ucfirst($service['s_name']);}else{ $html.='N/A'; } 
                $html.='</td><td align="left">&nbsp;';
                if(isset($service['fee_submittedby'])){ $html.=ucfirst($service['fee_submittedby']);}else{ $html.='N/A'; } 
                $html.='</td>';
             
                 $cls=$this->Comman->showclasstitle($service['class_id']);
                  $html.='<td align="left">&nbsp;';
                  
                 if(isset($cls['title'])){ $html.=ucfirst($cls['title']);}else{ $html.='N/A'; }
                  
                  
                  $html.='</td><td align="left">&nbsp;';
                  if(isset($service['p_fees'])){ $html.=$service['p_fees'];}else{ $html.='N/A'; } 
                  
                  $html.='</td></tr>';
$counter++;} }else{ 
		
		
    $html.='<tr>
    <td colspan="10" style="text-align:center;">NO Data Available</td>
    </tr>';
    } }else if($stat=='1'){  if(isset($t_enquiry) && !empty($t_enquiry)){ 
    foreach($t_enquiry as $service){  //pr($service);
   
                 $html.='<tr><td align="left">&nbsp;';
                 $html.=$counter;
                 $html.='</td> <td align="left">&nbsp;';
                 
                 if(isset($service['recipietno'])){  $html.=$service['recipietno']; }else{  $html.='N/A'; } 
                 
                 $html.='</td><td align="left">&nbsp;';
                 
                 if(isset($service['sno'])){ $html.=$service['sno'];
					 
					 }else{ $html.='N/A'; } 
					 
					 $html.='</td><td align="left">&nbsp;';
					 if(isset($service['created'])){ $html.=date('d-m-Y',strtotime($service['created']));
						 }else{ $html.='N/A'; 
							 
							 } 
							 $html.='</td><td align="left">&nbsp;';
							 $html.=$acedmic; 
							 
							 $html.='</td><td align="left">&nbsp;';
							if(isset($service['fname'])){ $html.=ucfirst($service['fname'])." ".ucfirst($service['middlename'])." ".ucfirst($service['lname']);}else{ $html.='N/A'; } 
							
							$html.='</td><td align="left">&nbsp;';
							
							if(isset($service['f_name'])){ $html.=ucfirst($service['f_name']);
								
								
								}else{ $html.='N/A'; } 
								$html.='</td>';
             
                 $cls=$this->Comman->showclasstitle($service['class_id']); 
                  $html.='<td align="left">&nbsp;'; if(isset($cls['title'])){ $html.=ucfirst($cls['title']);
					  
					  
					  }else{ $html.='N/A'; } 
					  
					  $html.='</td><td align="left">&nbsp;';
					  
					  if(isset($service['reg_fee'])){ $html.=$service['reg_fee'];
						  
						  }else{ $html.='N/A'; 
							  
							  
							  } 
							  
							  $html.='</td></tr>';
     $counter++;} }else{
		
    $html.='<tr>
    <td colspan="10" style="text-align:center;">NO Data Available</td>
    </tr>';
   } }  
	     
	   
$html.='</table>
</body>
</html>';

$pdf->WriteHTML($html, true, false, true, false, '');
ob_end_clean();
echo $pdf->Output('Result');
exit;
?>



?>
