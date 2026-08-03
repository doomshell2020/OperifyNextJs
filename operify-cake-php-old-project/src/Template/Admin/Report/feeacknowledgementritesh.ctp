<?php 
class xtcpdf extends TCPDF {
 
}

$this->set('pdf', new TCPDF('P','mm','A4'));
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->AddPage();

 $pdf->SetAutoPageBreak(TRUE, 0);

define("MAJOR", 'Rupees Only');
define("MINOR", '');
class toWords
{
    var $pounds;
    var $pence;
    var $major;
    var $minor;
    var $words = '';
    var $number;
    var $magind;
       var $units = array('', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine');
    var $teens = array('Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen');
    var $tens = array('', 'Ten', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety');
    var $mag = array('', 'Thousand', 'Million', 'Billion', 'Trillion');

    function toWords($amount, $major = MAJOR, $minor = MINOR)
    {
        $this->__toWords__((int)($amount), $major);
        $whole_number_part = $this->words;
        #$right_of_decimal = (int)(($amount-(int)$amount) * 100);
        $strform = number_format($amount,2);
        $right_of_decimal = (int)substr($strform, strpos($strform,'.')+1);
        $this->__toWords__($right_of_decimal, $minor);
        $this->words = $whole_number_part . ' ' . $this->words;
    }

    function __toWords__($amount, $major)
    {
        $this->major  = $major;
        #$this->minor  = $minor;
        $this->number = number_format($amount, 2);
        list($this->pounds, $this->pence) = explode('.', $this->number);
        $this->words = " $this->major";
        if ($this->pounds == 0)
            $this->words = "$this->words";
        else {
            $groups = explode(',', $this->pounds);
            $groups = array_reverse($groups);
            for ($this->magind = 0; $this->magind < count($groups); $this->magind++) {
                if (($this->magind == 1) && (strpos($this->words, 'Hundred') === false) && ($groups[0] != '000'))
                    $this->words = ' And ' . $this->words;
                $this->words = $this->_build($groups[$this->magind]) . $this->words;
            }
        }
    }

    function _build($n)
    {
        $res = '';
        $na  = str_pad("$n", 3, "0", STR_PAD_LEFT);
        if ($na == '000')
            return '';
        if ($na{0} != 0)
            $res = ' ' . $this->units[$na{0}] . ' Hundred';
        if (($na{1} == '0') && ($na{2} == '0'))
            return $res . ' ' . $this->mag[$this->magind];
        $res .= $res == '' ? '' : ' And';
        $t = (int) $na{1};
        $u = (int) $na{2};
        switch ($t) {
            case 0:
                $res .= ' ' . $this->units[$u];
                break;
            case 1:
                $res .= ' ' . $this->teens[$u];
                break;
            default:
                $res .= ' ' . $this->tens[$t] . ' ' . $this->units[$u];
                break;
        }
        $res .= ' ' . $this->mag[$this->magind];
        return $res;
    }
}





$html.='
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Result</title><link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">';
$html.='</head>
<body>
<table width="100%" align="left">
  <tr>
    <td style="text-align:left" width="50%" ><img src="images/logo.png" alt="" border="0" style="display:block;" width="130px" height="60px;"><br>
      <span style="display:block; color:#000; font-size:8px;"><u>Affiliated to CBSE Delhi (Affiliation no.1730236)</u></span> <br></td>
    <td style="text-align:rigth;" width="50%" align="right"><br>
      <br>
      Vishwamitra Marg, Near Hanuman Nagar 
      Ext.,<br>
      Sirsi Road, Jaipur-302012<br>
      Phone : &nbsp;2246189,2357844 &nbsp;&nbsp;&nbsp;&nbsp;
      Fax : &nbsp;0141-2245602 <br>
      Email : &nbsp;<u>info@sanskarjaipur.com,</u> <br>
      Website : &nbsp;www.sanskarjaipur.com <br></td>
  </tr>
</table>


<table  border="0"  align="center">
<hr>
<tr style="line-height:60px;">

<td style="text-align:left" width="50%" >

</td>
<td style="text-align:center;" width="20%" align="center"></td>
<td style="text-align:right;" width="30%" align="right"><b>Date: 
- '.date('d-m-Y').'</b></td>

   </tr> 
  
<tr style="line-height:50px;">

 <td align="center" colspan="8" style="font-size:14px;" ><b><u>TO WHOM IT MAY CONCERN</u></b></td>
 
   </tr> 

    <tr>
   <p align="left" style="font-size:13px;">This is to certify that <b>';
   $html.=ucwords(strtolower($student['fname']." 
   ".$student['middlename']." ".$student['lname']));
   
   if($student['gender']=="Male"){
    $html.="</b> S/o ";
   }else{
   
    $html.="</b> D/o ";
   
   }
  
   
   $html.="<b>".ucwords(strtolower($student['fee_submittedby']))."</b>";

   $classssr=$this->Comman->findclass($student['class_id']); 
  $datse=date('Y-m-d');
   $studentfees=$this->Comman->findpaidamountsack23($student['id'],$datse,$student['acedmicyear']);
 $quas=array();
 $todeposit=0;
 $addtionaldiscount=0;
 $findsum=0;
						  foreach($studentfees as $k=>$value){
							$quas[]=unserialize($value['quarter']);
							if($value['addtionaldiscount'] !='0' && $value['addtionaldiscount'] !='0.00'){
							$addtionaldiscount +=$value['addtionaldiscount'];
							
						}
							} 
							
							
						$quaf=array();
						
							foreach($quas as $h=>$vale){
						
								$quaf=array_merge($quaf,$vale);
								
								}
						$rt=array();
						
						
				foreach($quaf as $j=>$t){
					
					$qua[]=$j;
					$qua2[$j]=$t;
				}
				
				  $findamountmonth=$this->Comman->findamountallmonth($student['class_id'],$student['acedmicyear']);
     
      
      		$findsum= $findamountmonth['qu4_fees']+$findamountmonth['qu3_fees']+$findamountmonth['qu2_fees']+$findamountmonth['qu1_fees']; 
if(in_array("Quater1", $qua)  && in_array("Quater2", $qua) && 
in_array("Quater3", $qua) && in_array("Quater4", $qua) ){
	foreach($qua2 as $jkk=>$rty){
		if($jkk=="Quater1"){
			$q1=$rty;
			
		}
		
	
		if($jkk=="Quater2"){
			
			$q2=$rty;
		}
		if($jkk=="Quater3"){
			
			$q3=$rty;
		}
		if($jkk=="Quater4"){
			
			$q4=$rty;
		}
		
		
	}
	
	
	
	
	$tec=0;	 
	$tec2=0;	 
	$tec3=0;	 
	$tec4=0;	 
	if($student['discountcategory']!=''){
									 $calui=0;
									 
									 $calui2=0;
									 
									 $calui=$this->Comman->finddiscountqtr($student['discountcategory']);
									 
									 if($calui ==0){
										$calui2=$this->Comman->finddiscountqtr2($student['discountcategory']); 
									 }
								 
									  if($calui){
					  $tak=$calui*$q1/100;
					  $tak2=$calui*$q2/100;
					  $tak3=$calui*$q3/100;
					  $tak4=$calui*$q4/100;
					  
              $tec=floor($tak); 
              $tec2=floor($tak2); 
              $tec3=floor($tak3); 
              $tec4=floor($tak4); 
              
		  }else{
			  $tec=$calui2; 
			  $tec2=$calui2; 
			  $tec3=$calui2; 
			  $tec4=$calui2; 
			
		  }
									   }else{
										   if($q1){
	 $studentfeesq1=$this->Comman->findpaidamountsackquater23($student['stud_id'],$datse,'Quater1',$student['acedmicyear']); 
 $calui=0;
									 
									 $calui2=0;
									 
									 $calui=$this->Comman->finddiscountqtr($studentfeesq1['discountcategory']);
									 
									 if($calui ==0){
										$calui2=$this->Comman->finddiscountqtr2($studentfeesq1['discountcategory']); 
									 }
								 
									  if($calui){
										  
								  
										  
					  $tak=$calui*$q1/100;
					
					  
              $tec=floor($tak); 
             
              
		  }else{
			  $tec=$calui2; 
			
		  }
						
					}	
					
					
					  if($q2){
	 $studentfeesq2=$this->Comman->findpaidamountsackquater23($student['stud_id'],$datse,'Quater2',$student['acedmicyear']); 
 $calui=0;
									 
									 $calui2=0;
									 
									 $calui=$this->Comman->finddiscountqtr($studentfeesq2['discountcategory']);
									 
									 if($calui ==0){
										$calui2=$this->Comman->finddiscountqtr2($studentfeesq2['discountcategory']); 
									 }
								 
									  if($calui){
										  
								  
										  
					
					  $tak2=$calui*$q2/100;
					 
					  
           
              $tec2=floor($tak2); 
            
              
		  }else{
			 
			 $tec2=$calui2; 
			
			
		  }
						
					}	
					
					
					
					  if($q3){
	 $studentfeesq3=$this->Comman->findpaidamountsackquater23($student['stud_id'],$datse,'Quater3',$student['acedmicyear']); 
 $calui=0;
									 
									 $calui2=0;
									 
									 $calui=$this->Comman->finddiscountqtr($studentfeesq3['discountcategory']);
									 
									 if($calui ==0){
										$calui2=$this->Comman->finddiscountqtr2($studentfeesq3['discountcategory']); 
									 }
								 
									  if($calui){
										  
								  
										  
					
					  $tak3=$calui*$q3/100;
					 
              $tec3=floor($tak3); 
            
              
		  }else{
			  
			  $tec3=$calui2; 
			
			
		  }
						
					}	
					
					
					if($q4){
	 $studentfeesq4=$this->Comman->findpaidamountsackquater23($student['stud_id'],$datse,'Quater4',$student['acedmicyear']); 
 $calui=0;
									 
									 $calui2=0;
									 
									 $calui=$this->Comman->finddiscountqtr($studentfeesq4['discountcategory']);
									 
									 if($calui ==0){
										$calui2=$this->Comman->finddiscountqtr2($studentfeesq4['discountcategory']); 
									 }
								 
									  if($calui){
										  
								  
										  
				
					  $tak4=$calui*$q4/100;
					  
              
              $tec4=floor($tak4); 
              
		  }else{
			
			 $tec4=$calui2; 
			
		  }
						
					}	   
										   
									   }
									   $remain=0;
									   $remain2=0;
									   $remain3=0;
									   $remain4=0;
							$tgain=0;		   
					if($tec !='0'){   $remain1=$q1-$tec; 
						$tgain +=$remain1; }else{ $tgain +=$q1;  }
					if($tec2 !='0'){   $remain2=$q2-$tec2;
						$tgain +=$remain2; }else{ $tgain +=$q2;  }
					 if($tec3 !='0'){   $remain3=$q3-$tec3;
						 $tgain +=$remain3; }else{ $tgain +=$q3;  }
					if($tec4 !='0'){   $remain4=$q4-$tec4;
						 $tgain +=$remain4; }else{ $tgain +=$q4;  }
						 
						 $feeack=$this->Comman->gethistoryyearstudent($student['id'],$student['acedmicyear']); 
							if($feeack['id']){
								$monthy="April, 2019 to March, 2020";
							}else{  
								$monthy="April, 2019 to March, 2020";
								
								}
						 
						 if($addtionaldiscount !='0'){
							$tyu=$tgain-$addtionaldiscount;
							
						$objss    = new toWords($tyu);
$wsss=$objss->words;								  			   
		$html.=" studying in Class 
   <b>".$classssr['title']."</b> in 
   our school. The school fee (Tution Fee) for the period ".$monthy." is Rs. 
   ".$tyu."/- (".$wsss.") received.<br><br><br><br><br>Authorized 
   Signatory<br><br><br>(Accounts)</td>";		
							
						 }else{
							 $objss    = new toWords($tgain);
$wsss=$objss->words;								  			   
		$html.=" studying in Class 
   <b>".$classssr['title']."</b> in 
   our school. The school fee (Tution Fee) for the period ".$monthy." is Rs. 
   ".$tgain."/- (".$wsss.") received.<br><br><br><br><br>Authorized 
   Signatory<br><br><br>(Accounts)</td>";	
							 
						 }
								   
								   
	
}else{
	
			$tec=0;	
			 $remain=0; 	
if($student['discountcategory']!=''){
									 $calui=0;
									 
									 $calui2=0;
								
									 $calui=$this->Comman->finddiscountqtr($student['discountcategory']);
									 
									 if($calui ==0){
										$calui2=$this->Comman->finddiscountqtr2($student['discountcategory']); 
									 }
								 
									  if($calui){
					  $tak=$calui*$findamountmonth['qu1_fees']/100;
					  
              $tec=floor($tak); 
              
		  }else{
			  $tec=$calui2; 
		  }
									   }   if($tec !='0'){   
										   
										  
										 
										   $remain=$findamountmonth['qu1_fees']-$tec;
										   
									   }else{
										  
										   
										   $remain=$findamountmonth['qu1_fees'];
									   }
	
	
	$findsum=$remain*4;
  
   $obj    = new toWords($findsum);
$w=$obj->words;

   $objs    = new toWords($remain);
$ws=$objs->words;
   $gh=0;
   foreach($stk as $ty=>$yuu){
   
   $gh +=$yuu['deposite_amt'];
   
   } 
   
      if($student['gender']=="Male"){
		  $geng="His";
	  }else{
		    $geng="Her";
		  
	  }
	  
   $html.=" is studying in Class 
   <b>".$classssr['title']."</b> in 
   our school.".$geng." Tution Fee for the whole year (".$student['acedmicyear'].") is Rs. 
   ".$findsum."/- (".$w.") and this amount is to be paid on 
   quarterly basis, Rs. ".$remain."/- (".$ws.") for each 
   quarter.<br><br><br><br><br>Authorized 
   Signatory<br><br><br>(Accounts)</td>";
}
    $html.="</tr> 
    </table>
    
    
   
</body>
</html>";
$pdf->SetFont('times', '', 10, '', 'false');
$pdf->WriteHTML($html, true, false, true, false, '');
ob_end_clean();

echo $pdf->Output('FeeAcknowledge_'.$student['fname'].'.pdf');
exit;
?>
