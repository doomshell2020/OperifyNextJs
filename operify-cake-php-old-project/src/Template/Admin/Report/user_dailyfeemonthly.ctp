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



$pdf->SetFont('', '', 10, '', 'false');

$rolepresents=$this->request->session()->read('Auth.User.role_id');


 if($rolepresents=='5'){ 
	 $bordd="CBSE Fee";
	 
 }else if($rolepresents=='8'){ 
	 $bordd="International Fee";
 }else{
	 
	 $bordd="CBSE-INTERNATIONAL";
 }





 $temp = str_replace(array('{logo}', '{sitetitle}', '{subtitle1}', '{subtitle2}', '{address1}', '{address2}', '{phone}', '{fax}', '{email}', '{website}'), array($sitesetting['logo'], $sitesetting['sitetitle'], $sitesetting['subtitle1'], $sitesetting['subtitle2'], $sitesetting['address1'], $sitesetting['address2'], $sitesetting['phone'], $sitesetting['fax'], $sitesetting['email'], $sitesetting['website']), $det['template']);
 $html .= '
 <!DOCTYPE HTML>
 <html>
 <head>
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
 <title>Result</title><link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">';
 $html .= '</head>
 <body>';
 $html .= $temp;
 $html .= '<hr><br><br><br><table width="100%" align="center">
<tr>
<td style="text-align:left" width="15%" >


</td>

<td style="text-align:center;" width="70%" align="center">
<span style="display:block; color:#000; font-size:12px;"><b> <u>Daily Summary '.$bordd.' Report :- '.$acedmicyear.'</u></b></span><br>
<span style="display:block; color:#000; font-size:12px;"><b><i><mark style="background-color: yellow;
  color: black;">'; if($datefrom && $dateto && $datefrom!='1970-01-01' && $dateto!='1970-01-01'){
	
	  $datefromh=$datefrom;
	 
       	
		 $dateto2h=$dateto;
 $html.="From ".date('d-m-Y',strtotime($datefrom))." To ".date('d-m-Y',strtotime($dateto2h));
}else{
		 $datefromh=date('Y')."-04-01";
		 $dateto2h=date('d-m-Y');
	
       	   
	 $html.="From 01-04-".date('Y')." To ".$dateto;
	$dateto2h=date('Y-m-d',strtotime($dateto));
	

}


 $html.='</mark></i></b></span>
</td>

<td style="text-align:right; line-height:60px;" width="15%" align="right">

</td>


</tr>

</table>


<table width="100%" border="1" align="center"><tbody>';
 $html.='<tr><td width="30%"><b>Fee Collection Description</b></td>';

 foreach($mode as $k =>$rt){ 
	 $html.='<td align="center" width="14%"><b>&nbsp;&nbsp;'.strtoupper($rt).'</b></td>';
	
	}  


 $html.='</tr>';
      
        
           
			   foreach($selectField as $j=>$el){
		 $el=trim($el); 
		
		
		

	
	 $html.='<tr>';
 if($el=="Discount Fee") { 
	
   $html.='<td align="left">';
  
    $html.='&nbsp;(-) '.ucwords(strtolower($el));	
 	  $html.='</td>'; 
   
   
		 }else if($el=="Other Discount"){
			 
		
	 $html.='<td align="left">';
  
    $html.='&nbsp;(-) '.ucwords(strtolower($el));	
 	  $html.='</td>'; 
   
 	 
			 
	}else if($el=="Prev. Access Amount"){
			 
		
	 $html.='<td align="left">';
  
    $html.='&nbsp;'.ucwords(strtolower($el));	
 	  $html.='</td>'; 
   
 	 
			 
	}else if($el=="Due Amount"){
			 
		
 $html.='<td align="left">';
  
    $html.='&nbsp;(-) '.ucwords(strtolower($el));	
 	  $html.='</td>'; 
   
 	 
			 
	}else{
		
 $html.='<td align="left">&nbsp;';
	 $html.=ucwords(strtolower($el));	
	 $html.='</td>'; 
	}
	
	
	
	 foreach($mode as $k =>$rt){ 
		   $tot=0;
		
	  
		if($el=="Registration" && $rt=="CASH" ){ 
			 $findrecipiet=$this->Comman->checkregistration($datefromh,$dateto2h);
		$html.='<td>'; if($findrecipiet[0]['regfee']){  $tot +=$findrecipiet[0]['regfee']; $aet +=$findrecipiet[0]['regfee'];  
			
			 $html.=$findrecipiet[0]['regfee'].'&nbsp;';
			 
			  }else{ $html.='0';} 
			  
			  $html.='</td>';
	}else if($el=="Registration" && $rt!="CASH" ){ 
		
$html.='<td>';

  $html.='0'; 
  $html.='</td>';
  
  
	}else if($el=="Prospectus" && $rt=="CASH"){
		 $findprospectus=$this->Comman->checkprospectus($datefromh,$dateto2h);
		$html.='<td>';  if($findprospectus[0]['p_fees']) { 
			
			
			$tot +=$findprospectus[0]['p_fees']; $aet +=$findprospectus[0]['p_fees']; 
			
			$html.= $findprospectus[0]['p_fees'].'&nbsp;'; 
			 }else{ $html.='0';  }
			 $html.='</td>';
	    }else if($el=="Prospectus" && $rt!="CASH"){ 
$html.='<td>'; $html.='0'; 

$html.='</td>';
	    }else if($el=="Due Amount") { 
	 $html.='<td>';
	        $paidamounts=$this->Comman->findpaidamountsmodety($acedmicyear,$datefromh,$dateto2h,$rt);
	        
	          $paidamounts2=$this->Comman->findpaidamountsmode2y($acedmicyear,$datefromh,$dateto2h,$rt);
	        $totd=0;
	      foreach($paidamounts as $keyd=>$valuef){

	 
		$findpendingdues=$this->Comman->findpendingsfee2($valuef['student']['id'],$valuef['id']);
	
		  if($findpendingdues[0]['sum']){ $tot -=$findpendingdues[0]['sum']; $aet +=$findpendingdues[0]['sum']; $totd +=$findpendingdues[0]['sum'];  }else{ $tot -=0; $totd +=0; } }
		  
		  if(!empty($paidamounts2)){
			foreach($paidamounts2 as $keyd=>$valuef){


		$findpendingdues=$this->Comman->findpendingsfee2($valuef['student']['s_id'],$valuef['id']);
	

	

		?>
<?  if($findpendingdues[0]['sum']){ $tot -=$findpendingdues[0]['sum']+$findpendingdues2[0]['sum']; 
			$aet +=$findpendingdues[0]['sum']+$findpendingdues2[0]['sum']; $totd +=$findpendingdues[0]['sum']+$findpendingdues2[0]['sum'];  
			}else{ $tot -=0; $totd +=0; } } 
			
		}
		  
		  
		   $html.=$totd; $html.='</td>';
	   }else if($el=="Access Amount") { 
	 $html.='<td>';
	        $paidamounts=$this->Comman->findpaidamountsmodety($acedmicyear,$datefromh,$dateto2h,$rt);
	          $paidamounts2s=$this->Comman->findpaidamountsmode24s($acedmicyear,$datefromh,$dateto2h,$rt);
	        $totde=0;
	      foreach($paidamounts as $keyd=>$valuef){

	 
		$findacces=$this->Comman->findpendingsfeess2($valuef['student']['id'],$valuef['id']);
	 $findacces[0]['sum'] = preg_replace('/\D/', '', $findacces[0]['sum']);
		 if($findacces[0]['sum']){ $tot +=$findacces[0]['sum']; $aets +=$findacces[0]['sum']; $totde +=$findacces[0]['sum'];  }else{ $tot +=0; $totde +=0; } }
		 
		 
		  if(!empty($paidamounts2s)){
		   foreach($paidamounts2s as $keyd=>$valuef){

	 
		$findacces=$this->Comman->findpendingsfeess2($valuef['student']['s_id'],$valuef['id']);
	$findacces[0]['sum'] = preg_replace('/\D/', '', $findacces[0]['sum']);
		  if($findacces[0]['sum']){ $tot +=$findacces[0]['sum']; $aets +=$findacces[0]['sum']; $totde +=$findacces[0]['sum'];  }else{ $tot +=0; $totde +=0; } } 
		  
	  }
		 
		  $html.=$totde;  
		
		$html.='</td>';
		 }else{ 
		   
		     $paidamount=$this->Comman->findpaidamountsmodety($acedmicyear,$datefromh,$dateto2h,$rt);
		      $paidamount23s=$this->Comman->findpaidamountsmode24s($acedmicyear,$datefromh,$dateto2h,$rt);
//pr($paidamount);
		    
		  
		 $fees=0;
		 $cfees=0;
		 $dfees=0;
		 $qfees=0;
		 $tj=0;
		  $totalfine=0;
		  $totalOther=0;
		    $totalOther236=0;
		  $totaldiscount=0;
		  $adddiscount=0;
		   foreach($paidamount as $key=>$value){
						 
					if($rt==$value['mode']){
						 
						 	$quas=unserialize($value['quarter']);
						
						 	foreach($quas as $iteam['quarter']=>$iteam['amount']){
								 if($el=="Admission Fee") {
			if($iteam['quarter']=='Admission Fee'  && $value['student']['enroll']<='5974' && $this->request->session()->read('Auth.User.db')=="sanskar"  && $value['student']['board_id']=='1'){
	 
	 
	  $fees +='0';
	  
	 
	 }else if($iteam['quarter']=='Admission Fee'  && $value['recipetno']=='0'){
           
             
          $fees +='0';
          
          
               }else if($iteam['quarter']=='Admission Fee'){
	 
	 
	  $fees +=$iteam['amount'];
	  
	 
	 }
								
							}else if($el=="Caution Money") {
								if($iteam['quarter']=='Caution Money' && $value['student']['enroll']<='5974' && $this->request->session()->read('Auth.User.db')=="sanskar"  && $value['student']['board_id']=='1'){

	 
	 $cfees +='0';
	 //$cfees +=$iteam['amount'];
	 
	 }else if($iteam['quarter']=='Caution Money'  && $value['recipetno']=='0'){
           
             
         $cfees +='0';
          
          
               }else if($iteam['quarter']=='Caution Money'){
	 
	 
	  $cfees +=$iteam['amount'];
	  
	 
	 }
								
							}else if($el=="Development Fee") {
									if($iteam['quarter']=='Development Fee' && $value['student']['enroll']<='5974' && $this->request->session()->read('Auth.User.db')=="sanskar"  && $value['student']['board_id']=='1'){
	 
	 
	  $dfees +='0';
	  
	 
	 }else if($iteam['quarter']=='Development Fee'  && $value['recipetno']=='0'){
           
             
           $dfees +='0';
          
          
               }else if($iteam['quarter']=='Development Fee'){
	 
	 
	  $dfees +=$iteam['amount'];
	  
	 
	 }
								
							}else if($el=="Tution Fee") {
								
								
								if($iteam['quarter']=='Quater1' || $iteam['quarter']=='Quater2' || $iteam['quarter']=='Quater3' || $iteam['quarter']=='Quater4'){
	 
	 
	 $qfees +=$iteam['amount'];
	  
	 
	 }
								
							}else{
								
								      $totalas=array();
					
		
			           
	 $el= trim($el);  
	 $j= trim($iteam['quarter']); 
	
				if(strcasecmp($j, $el) == 0){
						
					
						 $tj +=$iteam['amount'];
						$totalas[$i]=$ted;
						$totalass[]=$totalas;
					
					}
					
					
	
	

	
	
					
								
							}
							
							
						 }
						 
						 if($el=="Late Fee"){
						 $el= trim($el);  
	
					
						 $totalfine += $value['lfine'];  
					
							
						 
						
						}else if($el=="Prev. Due"){
							
								foreach($quas as $j=>$te){
							
						
						  $iteam['quarter']=str_replace('"', "", $j);
						
					

 
 $findpendinsg=$this->Comman->findpendingrefrencefees235($iteam['quarter'],$te); 
 
 if($findpendinsg){

	   
	   	  $totalOther +=$findpendinsg['amt'];
						
				}	}
						}else if($el=="Prev. Access Amount"){
							
								foreach($quas as $j=>$te){
							
						
						  $iteam['quarter']=str_replace('"', "", $j);
						
					

 
 $findpendinsgs=$this->Comman->findpendingrefrencefees236($iteam['quarter'],$te); 

 if($findpendinsgs){

	   
	   	  $totalOther236 +=$findpendinsgs['amt'];
						
				}	}
						}else if($el=="Discount Fee"){
						
						 $total2='0';
						 $discounts='0';
						 $quan='0';
							if($value['discount']!='0.00')
							{

							
					
						foreach($quas as $jn=>$tn){
							
								  $quan+=$tn;
								
							}
						
$discounts=$value['discount'];



	
	 $totaldiscount +=$discounts;

						}
						
						
						}else if($el=="Other Discount"){
						
						
						 
			    	$adddiscount +=$value['addtionaldiscount'];
		 
						}
						 
						 
						 
						} 
					 }
					 
					 
					 
					  if(!empty($paidamount23s)){
					  foreach($paidamount23s as $key=>$value){
						 
					if($rt==$value['mode']){
						 
						 	$quas=unserialize($value['quarter']);
						
						 	foreach($quas as $iteam['quarter']=>$iteam['amount']){
								 if($el=="Admission Fee") {
			if($iteam['quarter']=='Admission Fee'  && $value['student']['enroll']<='5974' && $this->request->session()->read('Auth.User.db')=="sanskar"  && $value['student']['board_id']=='1'){
	 
	 
	  $fees +='0';
	  
	 
	 }else if($iteam['quarter']=='Admission Fee'  && $value['recipetno']=='0'){
           
             
          $fees +='0';
          
          
               }else if($iteam['quarter']=='Admission Fee'){
	 
	 
	  $fees +=$iteam['amount'];
	  
	 
	 }
								
							}else if($el=="Caution Money") {
								if($iteam['quarter']=='Caution Money' && $value['student']['enroll']<='5974' && $this->request->session()->read('Auth.User.db')=="sanskar"  && $value['student']['board_id']=='1'){

	 
	 $cfees +='0';
	 //$cfees +=$iteam['amount'];
	 
	 }else if($iteam['quarter']=='Caution Money'  && $value['recipetno']=='0'){
           
             
         $cfees +='0';
          
          
               }else if($iteam['quarter']=='Caution Money'){
	 
	 
	  $cfees +=$iteam['amount'];
	  
	 
	 }
								
							}else if($el=="Development Fee") {
									if($iteam['quarter']=='Development Fee' && $value['student']['enroll']<='5974' && $this->request->session()->read('Auth.User.db')=="sanskar"  && $value['student']['board_id']=='1'){
	 
	 
	  $dfees +='0';
	  
	 
	 }else if($iteam['quarter']=='Development Fee'  && $value['recipetno']=='0'){
           
             
           $dfees +='0';
          
          
               }else if($iteam['quarter']=='Development Fee'){
	 
	 
	  $dfees +=$iteam['amount'];
	  
	 
	 }
								
							}else if($el=="Tution Fee") {
								
								
								if($iteam['quarter']=='Quater1' || $iteam['quarter']=='Quater2' || $iteam['quarter']=='Quater3' || $iteam['quarter']=='Quater4'){
	 
	 
	 $qfees +=$iteam['amount'];
	  
	 
	 }
								
							}else{
								
								      $totalas=array();
					
		
			           
	 $el= trim($el);  
	 $j= trim($iteam['quarter']); 
	
				if(strcasecmp($j, $el) == 0){
						
					
						 $tj +=$iteam['amount'];
						$totalas[$i]=$ted;
						$totalass[]=$totalas;
					
					}
					
					
	
	

	
	
					
								
							}
							
							
						 }
						 
						 if($el=="Late Fee"){
						 $el= trim($el);  
	
					
						 $totalfine += $value['lfine'];  
					
							
						 
						
						}else if($el=="Prev. Due"){
							
								foreach($quas as $j=>$te){
							
						
						  $iteam['quarter']=str_replace('"', "", $j);
						
					

 
 $findpendinsg=$this->Comman->findpendingrefrencefees235($iteam['quarter'],$te); 
 
 if($findpendinsg){

	   
	   	  $totalOther +=$findpendinsg['amt'];
						
				}	}
						}else if($el=="Prev. Access Amount"){
							
								foreach($quas as $j=>$te){
							
						
						  $iteam['quarter']=str_replace('"', "", $j);
						
					

 
 $findpendinsgs=$this->Comman->findpendingrefrencefees236($iteam['quarter'],$te); 

 if($findpendinsgs){

	 

	   	  $totalOther236s +=$findpendinsgs['amt'];
						
				}	}
						}else if($el=="Discount Fee"){
						
						 $total2='0';
						 $discounts='0';
						 $quan='0';
							if($value['discount']!='0.00')
							{

							
					
						foreach($quas as $jn=>$tn){
							
								  $quan+=$tn;
								
							}
						
$discounts=$value['discount'];



	
	 $totaldiscount +=$discounts;

						}
						
						
						}else if($el=="Other Discount"){
						
						
						 
			    	$adddiscount +=$value['addtionaldiscount'];
		 
						}
						 
						 
						 
						} 
					 }
				
				 }
					 
				
	
					 
					 if($el=="Admission Fee") {
					
$html.='<td>';   $tot +=$fees; $aet +=$fees; $html.=$fees.'&nbsp;'; 
$html.='</td>';
  
		  }else if($el=="Caution Money") {
					
$html.='<td>';  $tot +=$cfees;  $html.=$cfees.'&nbsp;'; 
$html.='</td>';	   
		   }else if($el=="Development Fee") {
					
$html.='<td>';   $tot +=$dfees;  $html.=$dfees.'&nbsp;'; 
$html.='</td>';		   
		   }else if($el=="Tution Fee") {
					
$html.='<td>';  $tot +=$qfees; $tty +=$qfees.'&nbsp;'; 

$html.=$qfees;
$html.='</td>';   
		  }else if($el=="Late Fee") {
					
$html.='<td>'; $tot +=$totalfine; $html.=$totalfine.'&nbsp;'; 
$html.='</td>';		   
		  }else if($el=="Prev. Due") {
					
$html.='<td>';  $tot +=$totalOther;  $html.=$totalOther.'&nbsp;'; 

$html.='</td>';		   
		 }else if($el=="Prev. Access Amount") {
					
$html.='<td>';  $tot -=$totalOther236;  		   $str = preg_replace('/\D/', '', $totalOther236);

				 
 if($str!='0'){			 
$html.="-".$str; }else{
	
	$html.=$str;
	}   	   $html.='</td>';
		   }else if($el=="Discount Fee") {
					
$html.='<td>'; 
  $tot -=$totaldiscount; $html.=$totaldiscount.'&nbsp;'; 
  $html.='</td>';		   
		  }else if($el=="Other Discount"){
						
		
$html.='<td>';

 $tot -=$adddiscount; $html.=$adddiscount.'&nbsp;'; 
 
 $html.='</td>';		   
		 }else{
				 
$html.='<td>'; 
 $tot +=$tj; $html.=$tj.'&nbsp;'; 
 $html.='</td>';		   
		   } 
			
		
		
		} 
		
		} 
	
	   
		
		
    $html.='</tr>';
   
     }  
      $html.='<tr><td align="left" ><strong style="color:green;"> &nbsp;Net Received </strong></td>';	
     $ttys=0;
    foreach($mode as $k =>$rt){   $tot=0;
		  foreach($selectField as $j=>$el){
		 $el=trim($el);
		
	
		if($el=="Registration" && $rt=="CASH" ){ 
			$findrecipiet=$this->Comman->checkregistration($datefromh,$dateto2h);
		 if($findrecipiet[0]['regfee']){  $tot +=$findrecipiet[0]['regfee'];    }else{  } 
	}else if($el=="Registration" && $rt!="CASH" ){ 
		
		 
		 	}else if($el=="Prospectus" && $rt=="CASH"){ 
				
				 $findprospectus=$this->Comman->checkprospectus($datefromh,$dateto2h);
		
		 if($findprospectus[0]['p_fees']) { $tot +=$findprospectus[0]['p_fees'];     }else{   }
		 
		  }else if($el=="Prospectus" && $rt!="CASH"){ 
			  
			   
			    
			     }else if($el=="Due Amount") { 
	 
	        $paidamounts=$this->Comman->findpaidamountsmodety($acedmicyear,$datefromh,$dateto2h,$rt);
	         $paidamounts2=$this->Comman->findpaidamountsmode2y($acedmicyear,$datefromh,$dateto2h,$rt);
	        $totd=0;
	        
	        
	      foreach($paidamounts as $keyd=>$valuef){


		$findpendingdues=$this->Comman->findpendingsfee2($valuef['student']['id'],$valuef['id']);
	

	

		?>
<?  if($findpendingdues[0]['sum']){ $tot -=$findpendingdues[0]['sum']+$findpendingdues2[0]['sum']; 
			$aet +=$findpendingdues[0]['sum']+$findpendingdues2[0]['sum']; $totd +=$findpendingdues[0]['sum']+$findpendingdues2[0]['sum'];  
			}else{ $tot -=0; $totd +=0; } } 
			
			
			if(!empty($paidamounts2)){
			foreach($paidamounts2 as $keyd=>$valuef){


		$findpendingdues=$this->Comman->findpendingsfee2($valuef['student']['s_id'],$valuef['id']);
	

	

		?>
<?  if($findpendingdues[0]['sum']){ $tot -=$findpendingdues[0]['sum']+$findpendingdues2[0]['sum']; 
			$aet +=$findpendingdues[0]['sum']+$findpendingdues2[0]['sum']; $totd +=$findpendingdues[0]['sum']+$findpendingdues2[0]['sum'];  
			}else{ $tot -=0; $totd +=0; } } 
			
		}
	 }else if($el=="Access Amount") { 
	 
	        $paidamounts=$this->Comman->findpaidamountsmodety($acedmicyear,$datefromh,$dateto2h,$rt);
	         $paidamounts2s=$this->Comman->findpaidamountsmode24s($acedmicyear,$datefromh,$dateto2h,$rt);
	        $totde=0;
	      foreach($paidamounts as $keyd=>$valuef){

	 
		$findacces=$this->Comman->findpendingsfeess2($valuef['student']['id'],$valuef['id']);
	$findacces[0]['sum'] = preg_replace('/\D/', '', $findacces[0]['sum']);
		  if($findacces[0]['sum']){ $tot +=$findacces[0]['sum']; $aets +=$findacces[0]['sum']; $totde +=$findacces[0]['sum'];  }else{ $tot +=0; $totde +=0; } }
		  
		    if(!empty($paidamounts2s)){
		   foreach($paidamounts2s as $keyd=>$valuef){

	 
		$findacces=$this->Comman->findpendingsfeess2($valuef['student']['s_id'],$valuef['id']);
	$findacces[0]['sum'] = preg_replace('/\D/', '', $findacces[0]['sum']);
		  if($findacces[0]['sum']){ $tot +=$findacces[0]['sum']; $aets +=$findacces[0]['sum']; $totde +=$findacces[0]['sum'];  }else{ $tot +=0; $totde +=0; } } 
		  
	  }
		  
		  
		  
		  
		   
	   }else{ 
		   
		     $paidamount=$this->Comman->findpaidamountsmodety($acedmicyear,$datefromh,$dateto2h,$rt);
		     
		       $paidamount23s=$this->Comman->findpaidamountsmode24s($acedmicyear,$datefromh,$dateto2h,$rt);
//pr($paidamount);
		    
		  
		 $fees=0;
		 $cfees=0;
		 $dfees=0;
		 $qfees=0;
		 $tj=0;
		  $totalfine=0;
		  $totalOther=0;
		   $totalOther236s=0;
		  $totaldiscount=0;
		  $adddiscount=0;
		   foreach($paidamount as $key=>$value){
						 
					if($rt==$value['mode']){
						 
						 	$quas=unserialize($value['quarter']);
						
						 	foreach($quas as $iteam['quarter']=>$iteam['amount']){
								 if($el=="Admission Fee") {
			if($iteam['quarter']=='Admission Fee'  && $value['student']['enroll']<='5974' && $this->request->session()->read('Auth.User.db')=="sanskar"  && $value['student']['board_id']=='1'){
	 
	 
	  $fees +='0';
	  
	 
	 }else if($iteam['quarter']=='Admission Fee'  && $value['recipetno']=='0'){
           
             
          $fees +='0';
          
          
               }else if($iteam['quarter']=='Admission Fee'){
	 
	 
	  $fees +=$iteam['amount'];
	  
	 
	 }
								
							}else if($el=="Caution Money") {
								if($iteam['quarter']=='Caution Money' && $value['student']['enroll']<='5974' && $this->request->session()->read('Auth.User.db')=="sanskar"  && $value['student']['board_id']=='1'){

	 
	 $cfees +='0';
	 //$cfees +=$iteam['amount'];
	 
	 }else if($iteam['quarter']=='Caution Money'  && $value['recipetno']=='0'){
           
             
         $cfees +='0';
          
          
               }else if($iteam['quarter']=='Caution Money'){
	 
	 
	  $cfees +=$iteam['amount'];
	  
	 
	 }
								
							}else if($el=="Development Fee") {
									if($iteam['quarter']=='Development Fee' && $value['student']['enroll']<='5974' && $this->request->session()->read('Auth.User.db')=="sanskar"  && $value['student']['board_id']=='1'){
	 
	 
	  $dfees +='0';
	  
	 
	 }else if($iteam['quarter']=='Development Fee'  && $value['recipetno']=='0'){
           
             
           $dfees +='0';
          
          
               }else if($iteam['quarter']=='Development Fee'){
	 
	 
	  $dfees +=$iteam['amount'];
	  
	 
	 }
								
							}else if($el=="Tution Fee") {
								
								
								if($iteam['quarter']=='Quater1' || $iteam['quarter']=='Quater2' || $iteam['quarter']=='Quater3' || $iteam['quarter']=='Quater4'){
	 
	 
	 $qfees +=$iteam['amount'];
	  
	 
	 }
								
							}else{
								
								      $totalas=array();
					
		
			           
	 $el= trim($el);  
	 $j= trim($iteam['quarter']); 
	
				if(strcasecmp($j, $el) == 0){
						
					
						 $tj +=$iteam['amount'];
						$totalas[$i]=$ted;
						$totalass[]=$totalas;
					
					}
					
					
	
	

	
	
					
								
							}
							
							
						 }
						 
						 if($el=="Late Fee"){
						 $el= trim($el);  
	
					
						 $totalfine += $value['lfine'];  
					
							
						 
						
						}else if($el=="Prev. Due"){
							
								foreach($quas as $j=>$te){
							
						
						  $iteam['quarter']=str_replace('"', "", $j);
						
					

 
 $findpendinsg=$this->Comman->findpendingrefrencefees235($iteam['quarter'],$te); 
 
 if($findpendinsg){

	   
	   	  $totalOther +=$findpendinsg['amt'];
						
				}	}
						}else if($el=="Prev. Access Amount"){
							
								foreach($quas as $j=>$te){
							
						
						  $iteam['quarter']=str_replace('"', "", $j);
						
					

 
 $findpendinsgs=$this->Comman->findpendingrefrencefees236($iteam['quarter'],$te); 

 if($findpendinsgs){

	 

	   	  $totalOther236s +=$findpendinsgs['amt'];
						
				}	}
						}else if($el=="Discount Fee"){
						
						 $total2='0';
						 $discounts='0';
						 $quan='0';
							if($value['discount']!='0.00')
							{

							
					
						foreach($quas as $jn=>$tn){
							
								  $quan+=$tn;
								
							}
						
$discounts=$value['discount'];



	
	 $totaldiscount +=$discounts;

						}
						
						
						}else if($el=="Other Discount"){
						
						
						 
			    	$adddiscount +=$value['addtionaldiscount'];
		 
						}
						 
						 
						 
						} 
					 }
				
				 if(!empty($paidamount23s)){
					  foreach($paidamount23s as $key=>$value){
						 
					if($rt==$value['mode']){
						 
						 	$quas=unserialize($value['quarter']);
						
						 	foreach($quas as $iteam['quarter']=>$iteam['amount']){
								 if($el=="Admission Fee") {
			if($iteam['quarter']=='Admission Fee'  && $value['student']['enroll']<='5974' && $this->request->session()->read('Auth.User.db')=="sanskar"  && $value['student']['board_id']=='1'){
	 
	 
	  $fees +='0';
	  
	 
	 }else if($iteam['quarter']=='Admission Fee'  && $value['recipetno']=='0'){
           
             
          $fees +='0';
          
          
               }else if($iteam['quarter']=='Admission Fee'){
	 
	 
	  $fees +=$iteam['amount'];
	  
	 
	 }
								
							}else if($el=="Caution Money") {
								if($iteam['quarter']=='Caution Money' && $value['student']['enroll']<='5974' && $this->request->session()->read('Auth.User.db')=="sanskar"  && $value['student']['board_id']=='1'){

	 
	 $cfees +='0';
	 //$cfees +=$iteam['amount'];
	 
	 }else if($iteam['quarter']=='Caution Money'  && $value['recipetno']=='0'){
           
             
         $cfees +='0';
          
          
               }else if($iteam['quarter']=='Caution Money'){
	 
	 
	  $cfees +=$iteam['amount'];
	  
	 
	 }
								
							}else if($el=="Development Fee") {
									if($iteam['quarter']=='Development Fee' && $value['student']['enroll']<='5974' && $this->request->session()->read('Auth.User.db')=="sanskar"  && $value['student']['board_id']=='1'){
	 
	 
	  $dfees +='0';
	  
	 
	 }else if($iteam['quarter']=='Development Fee'  && $value['recipetno']=='0'){
           
             
           $dfees +='0';
          
          
               }else if($iteam['quarter']=='Development Fee'){
	 
	 
	  $dfees +=$iteam['amount'];
	  
	 
	 }
								
							}else if($el=="Tution Fee") {
								
								
								if($iteam['quarter']=='Quater1' || $iteam['quarter']=='Quater2' || $iteam['quarter']=='Quater3' || $iteam['quarter']=='Quater4'){
	 
	 
	 $qfees +=$iteam['amount'];
	  
	 
	 }
								
							}else{
								
								      $totalas=array();
					
		
			           
	 $el= trim($el);  
	 $j= trim($iteam['quarter']); 
	
				if(strcasecmp($j, $el) == 0){
						
					
						 $tj +=$iteam['amount'];
						$totalas[$i]=$ted;
						$totalass[]=$totalas;
					
					}
					
					
	
	

	
	
					
								
							}
							
							
						 }
						 
						 if($el=="Late Fee"){
						 $el= trim($el);  
	
					
						 $totalfine += $value['lfine'];  
					
							
						 
						
						}else if($el=="Prev. Due"){
							
								foreach($quas as $j=>$te){
							
						
						  $iteam['quarter']=str_replace('"', "", $j);
						
					

 
 $findpendinsg=$this->Comman->findpendingrefrencefees235($iteam['quarter'],$te); 
 
 if($findpendinsg){

	   
	   	  $totalOther +=$findpendinsg['amt'];
						
				}	}
						}else if($el=="Prev. Access Amount"){
							
								foreach($quas as $j=>$te){
							
						
						  $iteam['quarter']=str_replace('"', "", $j);
						
					

 
 $findpendinsgs=$this->Comman->findpendingrefrencefees236($iteam['quarter'],$te); 

 if($findpendinsgs){

	 

	   	  $totalOther236s +=$findpendinsgs['amt'];
						
				}	}
						}else if($el=="Discount Fee"){
						
						 $total2='0';
						 $discounts='0';
						 $quan='0';
							if($value['discount']!='0.00')
							{

							
					
						foreach($quas as $jn=>$tn){
							
								  $quan+=$tn;
								
							}
						
$discounts=$value['discount'];



	
	 $totaldiscount +=$discounts;

						}
						
						
						}else if($el=="Other Discount"){
						
						
						 
			    	$adddiscount +=$value['addtionaldiscount'];
		 
						}
						 
						 
						 
						} 
					 }
				
				 }
					 
					 if($el=="Admission Fee") {
					 
  $tot +=$fees;  		   
		  }else if($el=="Caution Money") {
					   $tot +=$cfees;   	   
		 }else if($el=="Development Fee") {
					
  $tot +=$dfees;  
  
    }else if($el=="Tution Fee") {
					 
					  $tot +=$qfees; 
					   }else if($el=="Late Fee") {
					
					 $tot +=$totalfine; 
					  }else if($el=="Prev. Due") {
					 
					 $tot +=$totalOther;  
					   }else if($el=="Prev. Access Amount") {
						   
						   $str = preg_replace('/\D/', '', $totalOther236s);

				 
 $tot -=$str; 
  	   
		   }else if($el=="Discount Fee") {
					 
					   $tot -=$totaldiscount;  
					     }else if($el=="Other Discount"){
						
		
		 $tot -=$adddiscount;  
		  }else{
				
					 
					  $tot +=$tj;  
			
		} 
		
		
		
		
		}  
    
    } 
    	
		$html.='<td><strong style="color:green;">';
	
setlocale(LC_MONETARY, 'en_IN');


	$srtt=$this->Comman->findfeemonth($rt,$datefromh,$dateto2h); 
		$tyys +=$srtt;

$amount = money_format('%!i', $srtt);

		$html.=$amount; $html.='*&nbsp;</strong></td>';	
		 }  $html.='</tr>';   
    $html.='<tr><td align="left"><strong style="color:green;">&nbsp;Grand Total</strong> : <b>';  
		
		
$amounttyys = money_format('%!i', $tyys); $html.=$amounttyys;
$html.='</b></td></tr>  
</tbody></table>';

 $rolepresent=$this->request->session()->read('Auth.User.role_id');


 if($rolepresent=='5'){ 
$html.='<br><br>
<table width="100%" border="1" align="center"><tbody>';
 $html.='<tr><td width="30%"><b>Description</b></td>';

 foreach($mode as $k =>$rt){ 
	 $html.='<td align="center" width="14%"><b>&nbsp;&nbsp;'.strtoupper($rt).'</b></td>';
	
	}  


 $html.='</tr>';
      
 $html.='<tr>';
 
$html.='<td align="left" style="color:red;">';
  
    $html.="(+) Other Fees Collection";	
 	  $html.='</td>'; 
   
 	
	
	 foreach($mode as $k =>$rt){ 
		  
		   
			
	$otherfee=$this->Comman->findofcashdate($datefromh,$dateto2h,$rt);			
		$html.='<td>'; 
		if($otherfee[0]['sum']){
		 $html.=$otherfee[0]['sum'].'&nbsp;'; 
		 $html.='</td>';
	}else{
			$tot +=0;  $html.='0&nbsp;';  $html.='</td>';
	}
		   
		 }
	   
		$html.='</tr>';
		 $html.='<tr><td align="left" ><strong style="color:green;"> &nbsp;Net Received </strong></td>';	
     $ttysk=0;
    foreach($mode as $k =>$rt){   
		
			
	$otherfee=$this->Comman->findofcashdate($datefromh,$dateto2h,$rt);			

		if($otherfee[0]['sum']){
			
	$html.='<td style="color:green;font-weight:bold;">'; 		
		 $html.=$otherfee[0]['sum']; 
		$ttysk +=$otherfee[0]['sum']; 
			 $html.='</td>';
		
		
	}else{
		$html.='<td style="color:green;font-weight:bold;">0</td>'; 	
			$ttysk +=0;  
	} 

		   
		 
		
		
	}
	
	
	
		$html.='</tr>';
		
		 $html.='<tr><td align="left"><strong style="color:green;">&nbsp;Grand Total</strong> : <b>';  
		
		
$amousnttyys = money_format('%!i', $ttysk); $html.=$amousnttyys;
$html.='</b></td></tr></tbody></table>';
}
$html.='</body>
</html>';

$pdf->WriteHTML($html, true, false, true, false, '');
ob_end_clean();
$date=date('d-m-Y');
echo $pdf->Output('Daily-Summary-'.$bordd.'-'.$date.'.pdf');
exit;
?>