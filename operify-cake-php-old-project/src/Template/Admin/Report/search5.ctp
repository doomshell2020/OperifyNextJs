<div class="table-responsive">
  <table id="" class="table table-bordered table-striped">
<tbody> 
   <tr>
   <td><!--<a id="" style="position: absolute;
top: 122px;
/* right: 0px; */
right: 46px;" class="btn btn-info btn-sm pull-right" href="<?php echo ADMIN_URL ;?>report/user_supportiv5"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel</a>--></td>
   </tr>
    <tr>
    <th>S.No</th>
    <th>Class</th>
    <th>Sections</th>
    <th>Tution Fees</th>
    <th>Paid Fees</th>
    <th>Pending Fees</th>
    <th>Discount</th>
    <th>Outstanding</th>
    
    </tr>
		       
  	<?php 
      $page = $this->request->params['paging']['Services']['page'];
		$limit = $this->request->params['pagc_c_iding']['Services']['perPage'];
		$counter = ($page * $limit) - $limit + 1;
	   $total=0;
	   $totalfee=0;
	   $out=0;
	   $total_discount=0;
       $session = $this->request->session();
       
      
       $session->delete($Classections); 
       $session->write('Classections',$Classections); 
        if($s_id){
      $session->delete($s_id); 
       $session->write('s_id',$s_id); 
       
       }
       $session->delete($acedmicyear); 
       $session->write('acedmicyear',$acedmicyear);      
         $session->delete($datefrom); 
       $session->write('datefrom',$datefrom);   
            $session->delete($dateto); 
       $session->write('dateto',$dateto);      
		if(isset($Classections) && !empty($Classections)){ 
			foreach($Classections as $key=>$element) { $s_id=$element['class_id'];
			$service=$element['section_id'];?>
				 <tr>
			     <td><?php echo $counter;?></td>
			      <td><?php $class=$this->Comman->findclasses($s_id);
			          
			              echo $class[0]['title'];
			     ?>    </td>
			     <td><?php $section=$this->Comman->findsections($service);
			          
			              echo $section[0]['title'];
			     ?>    
			     </td>
			    <?php  $amount=$this->Comman->findamount($s_id,$acedmicyear); ?>
			     <td><span class="text-black">&#8377; </span><?php   
			        $totalstudentcount=$this->Comman->findstudentcount($s_id,$acedmicyear,$section[0]['id']);
			         echo (($amount[0]['qu1_fees']+ $amount[0]['qu2_fees']+ $amount[0]['qu3_fees']+ $amount[0]['qu4_fees'])*$totalstudentcount); 
			         $total+= (($amount[0]['qu1_fees']+ $amount[0]['qu2_fees']+ $amount[0]['qu3_fees']+ $amount[0]['qu4_fees'])*$totalstudentcount); 
			     ?></td>
			
                  <td><span class="text-black">&#8377; </span><?php
                  if(isset($datefrom))
                  {
                   $paidamount=$this->Comman->findpaidamounts($acedmicyear,$datefrom,$dateto);
			   }
			   else
			   {
				   
				 $paidamount=$this->Comman->findpaidamount($acedmicyear);   
			   }
                  $total1=0;
    
                  
                     foreach($paidamount as $key=>$value)
                     {
						// pr($value);
						 if($value['student']['class_id']==$s_id && $value['student']['section_id']==$section[0]['id'] && $value['student']['acedmicyear']==$acedmicyear)
						 {
							 
						$total1 +=$value['deposite_amt'];
						$totalfee +=$value['deposite_amt'];
						
						 }
						
					 }
					 echo $total1;
					 
                  ?></td> 
                  <td>
                  <span class="text-black">&#8377; </span><?php
                  if(isset($datefrom))
                  {
                   $paidamount=$this->Comman->findpaidamounts($acedmicyear,$datefrom,$dateto);
			   }
			   else
			   {
				   
				 $paidamount=$this->Comman->findpaidamount($acedmicyear);   
			   }
                  $totalamt1=0;
             // pr($paidamount); 
                  
                     foreach($paidamount as $key=>$value)
                     {
						// pr($value);
						 if($value['student']['class_id']==$s_id && $value['student']['section_id']==$section[0]['id'] && $value['student']['acedmicyear']==$acedmicyear)
						 {
						 
					$findpending=$this->Comman->findpendingsfee($value['student']['id']);	 
						
						$totalamt1 +=$findpending[0]['sum'];
						$totalfeeamt +=$findpending[0]['sum'];
						
						 }
						
					 }
					 echo $totalamt1; ?>
                  
                  </td>   
                  
                  <td>  <span class="text-black">&#8377; </span>
                  	<?php
                  	
                  	
                  	 if(isset($datefrom)){
                   $paidamount=$this->Comman->findpaidamounts($acedmicyear,$datefrom,$dateto);
			   }else{
				 $paidamount=$this->Comman->findpaidamount($acedmicyear);   
			   }
                   $qua=0;
    
                  
                     foreach($paidamount as $key=>$value)
                     {
					
						 if($value['student']['class_id']==$s_id && $value['student']['section_id']==$section[0]['id'] && $value['student']['acedmicyear']==$acedmicyear)
						 {
							
							$quas=unserialize($value['quarter']);
							
									
						
						$rt=array();	
						
			
						foreach($quas as $j=>$t){
					
					$qua+=$t;
				}	
					 }
						
					 }
                  	
                 

                  	 $total2 = 0;






                     foreach($paidamount as $key=>$value)
                     {
						// pr($value);
						 if($value['student']['class_id']==$s_id && $value['student']['section_id']==$section[0]['id'])
						 {
							if($value['discount']!='0.00')
							{
$quasn=unserialize($value['quarter']);
							
									
						
						$rt=array();	
						
						
						foreach($quasn as $jn=>$tn){
							
								  $quan+=$tn;
								
							}
						
$discounts=$value['discount'];


    $addtionaldiscount=$value['addtionaldiscount'];
if($addtionaldiscount>0){
	$total2+=$discounts+$addtionaldiscount;
	
}else{
		$total2+=$discounts;
	
}
								//$total2 +=$value['amount']-$value['fee'];
							 
							}
						 }
						
					 }
					echo $total2;
					 $total_discount += $total2;
					 
                  ?></td>

                  <td><span class="text-black">&#8377; </span><?php 
                  
                  $t=$total1+$total2;
          echo ((($amount[0]['qu1_fees']+ $amount[0]['qu2_fees']+ $amount[0]['qu3_fees']+ $amount[0]['qu4_fees'])*$totalstudentcount)-($t)); 
                  
                  $out+=(($amount[0]['qu1_fees']+ 
                  $amount[0]['qu2_fees']+ $amount[0]['qu3_fees']+ $amount[0]['qu4_fees'])*$totalstudentcount)-($t); 
                  ?> </td>    
                          </tr>
		<?php $counter++;}
	  } else { ?>
	  
	  <td colspan="7">No Data Available</td>   
	  
	  <?	} ?>
                    <tr>
                     <td></td>  
             <td class="text-bold text-green">GRAND TOTAL</td>   
              <td class="text-bold text-green"></td>      
             <td class="text-bold text-green"><span class="text-black">&#8377; </span><?php echo $total; ?></td>       
             <td class="text-bold text-green"><span class="text-black">&#8377; </span><?php echo $totalfee; ?></td>   
             <td class="text-bold text-green"><span class="text-black">&#8377; </span><?php echo $totalfeeamt; ?></td>    
             <td class="text-bold text-green"><span class="text-black">&#8377; </span><?php echo $total_discount; ?></td>       
             <td class="text-bold text-green"><span class="text-black">&#8377; </span><a href="<?php echo SITE_URL;?>admin/report/students_all/<?php echo $acedmicyear;?>" ><?php echo $out; ?></a></td>       
    </tr>
         </table>      
      
      <div>      
    


