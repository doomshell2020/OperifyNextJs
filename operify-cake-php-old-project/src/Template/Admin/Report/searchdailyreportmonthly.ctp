<div class="table-responsive">


  <table id="" class="table table-bordered table-striped">
    <tbody>
      <tr>
        <td><a id="" style="position: absolute;top: 122px;right: 46px;" class="btn btn-info btn-sm pull-right"
            target="_blank" href="<?php echo ADMIN_URL ;?>report/user_dailyfee"><i class="fa fa-file-excel-o"
              aria-hidden="true"></i> Export PDF</a></td>
      </tr>






      <?php 
         $session = $this->request->session(); 
          if($mode){
      $session->delete($mode); 
       $session->write('mode',$mode); 
       
       }
          
     
     
        if($selectField){
      $session->delete($selectField); 
       $session->write('selectField',$selectField); 
       
       }
           
         
       ?>

      <tr>
        <td><b> Fee Collection
            <? if($datefrom && $dateto && $datefrom!='1970-01-01' && $dateto2!='1970-01-01'){
	
	  $datefromh=$datefrom;
	   $session = $this->request->session();    
	   
	   $session->delete($datefromh); 
       $session->write('datefrom',$datefromh); 
       	   $session->delete($dateto2); 
       $session->write('dateto',$dateto2);
		 $dateto2h=$dateto2;
 echo "From ".date('d-m-Y',strtotime($datefrom))." To ".date('d-m-Y',strtotime($dateto2));
}else{
		 $datefromh=date('Y')."-04-01";
		 $dateto2h=date('d-m-Y');
		   $session = $this->request->session(); 		
		  $session->delete($datefromh); 
       $session->write('datefrom',$datefromh); 
       	   
	echo "From 01-04-".date('Y')." To ".$dateto2h;
	$dateto2h=date('Y-m-d',strtotime($dateto2h));
	
	$session->delete($dateto2h); 
       $session->write('dateto',$dateto2h);
} ?></b></td>

        <?php foreach($mode as $k =>$rt){ 
	echo '<td ><b>'.strtoupper($rt).'</b></td>';
	
	} ?>


      </tr>


      <?  
			   foreach($selectField as $j=>$el){
		 $el=trim($el); 
		
		
		?>


      <tr>
        <? if($el=="Discount Fee") { ?>

        <?
  				
	 echo $html='<td>';
  
   echo $html='(-) '.ucwords(strtolower($el));	
 	echo  $html='</td>'; 
   
   
		 }else if($el=="Due Amount"){
			 
		
	echo $html='<td>';
  
   echo $html='(-) '.ucwords(strtolower($el));	
 	echo  $html='</td>'; 
   
 	 
			 
	}else if($el=="Prev. Access Amount"){
			 
		
	echo $html='<td>';
  
   echo $html=''.ucwords(strtolower($el));	
 	echo  $html='</td>'; 
   
 	 
			 
	}else if($el=="Other Discount"){
			 
		
	echo $html='<td>';
  
   echo $html='(-) '.ucwords(strtolower($el));	
 	echo  $html='</td>'; 
   
 	 
			 
	}else{
		
echo $html='<td align="left">';
	echo $html=ucwords(strtolower($el));	
	echo $html='</td>'; 
	}
	
	
	
	 foreach($mode as $k =>$rt){ 
		   $tot=0;
		
	  
		if($el=="Registration" ){ ?>

        <? $findrecipiet=$this->Comman->checkregistration($datefromh,$dateto2h);
				$paidamount=$this->Comman->findpaidmode($datefromh,$dateto2h,$rt,$el);
		?>
        <td>
          <?  if($findrecipiet[0]['regfee'] && !empty($paidamount)){  $tot +=$findrecipiet[0]['regfee']; $aet +=$findrecipiet[0]['regfee'];  echo $findrecipiet[0]['regfee']; }else{ echo '0';} ?>
        </td>
        <?	}else  if($el=="Prospectus"){ ?>
        <? $findprospectus=$this->Comman->checkprospectus($datefromh,$dateto2h);
								$paidamount=$this->Comman->findpaidmode($datefromh,$dateto2h,$rt,$el); 
		?>
        <td>
          <?  if($findprospectus[0]['p_fees'] && !empty($paidamount)) { $tot +=$findprospectus[0]['p_fees']; $aet +=$findprospectus[0]['p_fees']; echo $findprospectus[0]['p_fees'];  }else{ echo '0';  }?>
        </td>
        <? }else if($el=="Due Amount") { 
	 ?>
        <td>
          <?
	        $paidamounts=$this->Comman->findpaidamountsmodety($acedmicyear,$datefromh,$dateto2h,$rt);
	        $paidamounts2=$this->Comman->findpaidamountsmode2y($acedmicyear,$datefromh,$dateto2h,$rt);
	        $totd=0;
	      foreach($paidamounts as $keyd=>$valuef){

	 
		$findpendingdues=$this->Comman->findpendingsfee2($valuef['student']['id'],$valuef['id']);
	
		?>
          <?  if($findpendingdues[0]['sum']){ $tot -=$findpendingdues[0]['sum']; $aet +=$findpendingdues[0]['sum']; $totd +=$findpendingdues[0]['sum'];  }else{ $tot -=0; $totd +=0; } }
		
		
		if(!empty($paidamounts2)){
			foreach($paidamounts2 as $keyd=>$valuef){


		$findpendingdues=$this->Comman->findpendingsfee2($valuef['student']['s_id'],$valuef['id']);
	

	

		?>
          <?  if($findpendingdues[0]['sum']){ $tot -=$findpendingdues[0]['sum']+$findpendingdues2[0]['sum']; 
			$aet +=$findpendingdues[0]['sum']+$findpendingdues2[0]['sum']; $totd +=$findpendingdues[0]['sum']+$findpendingdues2[0]['sum'];  
			}else{ $tot -=0; $totd +=0; } } 
			
		}
		
		
		 echo $totd;  ?>
        </td>
        <? }else if($el=="Access Amount") { 
	 ?>
        <td>
          <?
	        $paidamounts=$this->Comman->findpaidamountsmodety($acedmicyear,$datefromh,$dateto2h,$rt);
	        $paidamounts2s=$this->Comman->findpaidamountsmode24s($acedmicyear,$datefromh,$dateto2h,$rt);
	        $totde=0;
	      foreach($paidamounts as $keyd=>$valuef){

	 
		$findacces=$this->Comman->findpendingsfeess2($valuef['student']['id'],$valuef['id']);
	 $findacces[0]['sum'] = preg_replace('/\D/', '', $findacces[0]['sum']);
		?>
          <?  if($findacces[0]['sum']){ $tot +=$findacces[0]['sum']; $aets +=$findacces[0]['sum']; $totde +=$findacces[0]['sum'];  }else{ $tot +=0; $totde +=0; } }
		
		 if(!empty($paidamounts2s)){
		   foreach($paidamounts2s as $keyd=>$valuef){

	 
		$findacces=$this->Comman->findpendingsfeess2($valuef['student']['s_id'],$valuef['id']);
	$findacces[0]['sum'] = preg_replace('/\D/', '', $findacces[0]['sum']);
		  if($findacces[0]['sum']){ $tot +=$findacces[0]['sum']; $aets +=$findacces[0]['sum']; $totde +=$findacces[0]['sum'];  }else{ $tot +=0; $totde +=0; } } 
		  
	  }
		
		
		
		 echo $totde;  ?>
        </td>
        <? }else{ 
		   
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
								
							}else if($el=="Annual Charges") {
								if($iteam['quarter']=='Annual Charges' && $value['student']['enroll']<='5974' && $this->request->session()->read('Auth.User.db')=="sanskar"  && $value['student']['board_id']=='1'){
 
 
	$dfees +='0';
	
 
 }else if($iteam['quarter']=='Annual Charges'  && $value['recipetno']=='0'){
				 
					 
				 $dfees +='0';
				
				
						 }else if($iteam['quarter']=='Annual Charges'){
 
 
	$dfees +=$iteam['amount'];
	
 
 }
							
						}else if($el=="Tution Fee") {

								$collect= array('April'=>'April','May'=>'May','June'=>'June','July'=>'July','August'=>'August','September'=>'September','October'=>'October','November'=>'November','December'=>'December','January'=>'January','February'=>'February','March'=>'March');
								if (in_array($iteam['quarter'], $collect)) {
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
								
							}else if($el=="Annual Charges") {
								if($iteam['quarter']=='Annual Charges' && $value['student']['enroll']<='5974' && $this->request->session()->read('Auth.User.db')=="sanskar"  && $value['student']['board_id']=='1'){
 
 
	$dfees +='0';
	
 
 }else if($iteam['quarter']=='Annual Charges'  && $value['recipetno']=='0'){
				 
					 
				 $dfees +='0';
				
				
						 }else if($iteam['quarter']=='Annual Charges'){
 
 
	$dfees +=$iteam['amount'];
	
 
 }
							
						}else if($el=="Tution Fee") {
								
								$collect= array('April'=>'April','May'=>'May','June'=>'June','July'=>'July','August'=>'August','September'=>'September','October'=>'October','November'=>'November','December'=>'December','January'=>'January','February'=>'February','March'=>'March');
								if (in_array($iteam['quarter'], $collect)) {
	 
	 
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
					 
					 
					 
					 
				
	
					 
					  if($el=="Late Fee") {
					 ?>
        <td>
          <?  $tot +=$totalfine; echo $totalfine; ?>
        </td>
        <?   }else if($el=="Admission Fee") {
					 ?>
        <td>
          <?   $tot +=$fees; $aet +=$fees; echo $fees; ?>
        </td>
        <?   }else if($el=="Caution Money") {
					 ?>
        <td>
          <?  $tot +=$cfees;  echo $cfees; ?>
        </td>
        <?   }else if($el=="Development Fee") {
					 ?>
        <td>
          <?   $tot +=$dfees;  echo $dfees; ?>
        </td>
        <?   }else if($el=="Annual Charges") {
					 ?>
        <td>
          <?   $tot +=$dfees;  echo $dfees; ?>
        </td>
        <?   }else if($el=="Tution Fee") {
					 ?>
        <td>
          <?  $tot +=$qfees; $tty +=$qfees; echo $qfees; ?>
        </td>
        <?   }else if($el=="Prev. Due") {
					 ?>
        <td>
          <?  $tot +=$totalOther;  echo $totalOther; ?>
        </td>
        <?   }else if($el=="Prev. Access Amount") {
					 ?>
        <td>
          <?  $tot -=$totalOther236;  		   $str = preg_replace('/\D/', '', $totalOther236);

	if($str!='0'){			 
echo "-".$str; }else{
	
	echo $str;
	} ?>
        </td>
        <?   }else if($el=="Discount Fee") {
					 ?>
        <td>
          <?   $tot -=$totaldiscount; echo $totaldiscount; ?>
        </td>
        <?   }else if($el=="Other Discount"){
						
		 ?>
        <td>
          <? $tot -=$adddiscount; echo $adddiscount; ?>
        </td>
        <? }else{
				 ?>
        <td>
          <? $tot +=$tj; echo $tj; ?>
        </td>
        <?   } 
			
		
		
		} 
		
		} 
	?>



      </tr>
      <? 
     }  ?>
      <tr>
        <td><strong style="color:green;">Net Received </strong></td>
        <?
   $ttys=0;
    foreach($mode as $k =>$rt){   $tot=0;
		  foreach($selectField as $j=>$el){
		 $el=trim($el);
		
	
		if($el=="Registration" && $rt=="CASH" ){ ?>

        <? $findrecipiet=$this->Comman->checkregistration($datefromh,$dateto2h);
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
								
							}else if($el=="Annual Charges") {
								if($iteam['quarter']=='Annual Charges' && $value['student']['enroll']<='5974' && $this->request->session()->read('Auth.User.db')=="sanskar"  && $value['student']['board_id']=='1'){
 
 
	$dfees +='0';
	
 
 }else if($iteam['quarter']=='Annual Charges'  && $value['recipetno']=='0'){
				 
					 
				 $dfees +='0';
				
				
						 }else if($iteam['quarter']=='Annual Charges'){
 
 
	$dfees +=$iteam['amount'];
	
 
 }
							
						}else if($el=="Tution Fee") {
								
							$collect= array('April'=>'April','May'=>'May','June'=>'June','July'=>'July','August'=>'August','September'=>'September','October'=>'October','November'=>'November','December'=>'December','January'=>'January','February'=>'February','March'=>'March');
							if (in_array($iteam['quarter'], $collect)) {
	 
	 
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
	  
	 
	 }else if($iteam['quarter']=='Admission Fee'   && $value['recipetno']=='0'){
           
             
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
								
							}else if($el=="Annual Charges") {
								if($iteam['quarter']=='Annual Charges' && $value['student']['enroll']<='5974' && $this->request->session()->read('Auth.User.db')=="sanskar"  && $value['student']['board_id']=='1'){
 
 
	$dfees +='0';
	
 
 }else if($iteam['quarter']=='Annual Charges'  && $value['recipetno']=='0'){
				 
					 
				 $dfees +='0';
				
				
						 }else if($iteam['quarter']=='Annual Charges'){
 
 
	$dfees +=$iteam['amount'];
	
 
 }
							
						} else if($el=="Tution Fee") {
								
							$collect= array('April'=>'April','May'=>'May','June'=>'June','July'=>'July','August'=>'August','September'=>'September','October'=>'October','November'=>'November','December'=>'December','January'=>'January','February'=>'February','March'=>'March');
							if (in_array($iteam['quarter'], $collect)) {
	 
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
  
    }else if($el=="Annual Charges") {
					
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
    
    } ?>

        <td><strong style="color:green;">
            <? setlocale(LC_MONETARY, 'en_IN');
		
		
		
		$srtt=$this->Comman->findfeemonth($rt,$datefromh,$dateto2h); 
		
	
		$tyys +=$srtt;
		
$amount = money_format('%!i', $srtt); echo $amount; ?>*</strong></td>
        <? } ?>
      </tr>

      <tr>
        <td><strong style="color:green;">Grand Total</strong> : <b>
            <?  
		
		
$amounttyys = money_format('%!i', $tyys); echo $amounttyys;?></b></td>
      </tr>

  </table>

  <br><br>
  <?
 $rolepresent=$this->request->session()->read('Auth.User.role_id');


 if($rolepresent=='5'){ 
  ?>
  <table id="" class="table table-bordered table-striped">


    <tbody>

      <tr>
        <td><b>Other Fees Collection
            <? if($datefrom && $dateto && $datefrom!='1970-01-01' && $dateto2!='1970-01-01'){
	
	  $datefromh=$datefrom;
	   $session = $this->request->session();    
	   
	   $session->delete($datefromh); 
       $session->write('datefrom',$datefromh); 
       	   $session->delete($dateto2); 
       $session->write('dateto',$dateto2);
		 $dateto2h=$dateto2;
 echo "From ".date('d-m-Y',strtotime($datefrom))." To ".date('d-m-Y',strtotime($dateto2));
}else{
		 $datefromh=date('Y')."-04-01";
		 $dateto2h=date('d-m-Y');
		   $session = $this->request->session(); 		
		  $session->delete($datefromh); 
       $session->write('datefrom',$datefromh); 
       	   
	echo "From 01-04-".date('Y')." To ".$dateto2h;
	$dateto2h=date('Y-m-d',strtotime($dateto2h));
	
	$session->delete($dateto2h); 
       $session->write('dateto',$dateto2h);
} ?></b></td>

        <?php foreach($mode as $k =>$rt){ 
	echo '<td ><b>'.strtoupper($rt).'</b></td>';
	
	} ?>


      </tr>



      <tr>
        <?  
			 
		
	echo $html='<td style="color:red;">';
  
   echo $html="(+) Other Fees Collection";	
 	echo  $html='</td>'; 
   
 	 
			 
	
	
	
	
	 foreach($mode as $k =>$rt){ 
		  $tothh=0;


			
	$otherfee=$this->Comman->findofcashdate($datefromh,$dateto2h,$rt);			
		 ?>
        <td>
          <? if($otherfee[0]['sum']){ $tothh +=$otherfee[0]['sum'];   
	echo $otherfee[0]['sum']; }else{  
		$tot +=0;   
		echo '0';   }?>
        </td>
        <? 
      
  } ?>

      </tr>


      <tr>
        <td><strong style="color:green;">Net Received </strong></td>
        <?
   $ttys=0;
   $tothh=0;
    foreach($mode as $k =>$rt){ 
		  

     
			
	$otherfee=$this->Comman->findofcashdate($datefromh,$dateto2h,$rt);			
		 ?>
        <td style="color:green;font-weight:bold;">
          <?  if($otherfee[0]['sum']){  $tothh +=$otherfee[0]['sum'];  echo $otherfee[0]['sum']; }else{  $tothh +=0;   echo '0';   }?>
        </td>
        <? 
      
  } ?>

      </tr>
      <tr>
        <td><strong style="color:green;">Grand Total</strong> : <b>
            <?  
		
		
$amounttysys = money_format('%!i', $tothh); echo $amounttysys;?></b></td>
      </tr>

    </tbody>


    <?php } ?>