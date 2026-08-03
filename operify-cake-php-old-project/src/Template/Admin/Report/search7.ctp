<div class="table-responsive">
  <table id="" class="table table-bordered table-striped">
<tbody> 
   <tr>
   <td><a id="" style="position: absolute;
top: 123px;
/* right: 0px; */
right: 46px;" class="btn btn-info btn-sm pull-right" href="<?php echo ADMIN_URL ;?>report/user_supportiv7"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel</a></td>
   </tr>
    <tr>
    <th>#</th>
    <th>Student Name</th>
    <th>Class</th>
    <th>Section</th>
    <th>Location</th>
     <th>ROUTE</th>
    <th>Bus No</th>
    <th>Driver Name</th>
    <th>Driver Mobile No</th>
    <th>Total Amount</th>
    <th>Paid Amount</th>
    </tr>
		       
  	<?php 
  	                   $session = $this->request->session();
		               $session->delete($students); 
			           $session->write('students',$students); 
			           $session->delete($location); 
			           $session->write('location',$location); 
			           
  	
      $page = $this->request->params['paging']['Services']['page'];
		$limit = $this->request->params['pagc_c_iding']['Services']['perPage'];
		$counter = ($page * $limit) - $limit + 1;
		$totaltransportamount=0;
		$total_transport_paidamount=0;
		if(isset($students) && !empty($students)){ //pr($personalsd);
		foreach($students as $service){    ?>
				 <tr>
					
			     <td><?php echo $counter;?></td>	     	 	
                 <td><a href="<?php echo SITE_URL;?>admin/students/view/<?php echo $service['id'] ; ?>"><?php echo $service['fname']." ".$service['middlename']." ".$service['lname']; ?></a></td> 
                        <td> <?php  echo $service['class']['title'];  ?></td> 
                         <td> <?php  echo $service['section']['title'];  ?></td>   
                 <td><?php echo $location['name']; ?></td>  
                       <td><?php  $route=$this->Comman->findroute($location['id']); 
                       $rty=explode(',',$route[0]['route']); $i=0;
                       foreach($rty as $kew=>$value){
						  $dfg= $this->Comman->findlocation($value); 
						  if($i=='0'){
						
						  echo $dfg['name'];
					  }else{
						  
						  echo "</br>".$dfg['name'];  
					  }
					$i++;
						   
						   } ?></td>
                 <td><?php $route=$this->Comman->findroute($location['id']) ;
                    echo $route[0]['vechical_no'];
                 ?></td> 
                 <td> <?php  echo $route[0]['driver_name'];  ?></td> 
                 <td> <?php  echo $route[0]['driver_mobile'];  ?></td> 
              
                 <td><span class="text-black">&#8377; </span><?php $amount=$this->Comman->findtransportamount($location['id'],$service['acedmicyear']); 
                  echo $total= $amount[0]['qu1_fees']+ $amount[0]['qu2_fees']+ $amount[0]['qu3_fees']+ $amount[0]['qu4_fees'];
                    $totaltransportamount+=$amount[0]['qu1_fees']+ $amount[0]['qu2_fees']+ $amount[0]['qu3_fees']+ $amount[0]['qu4_fees'];
                 ?>
                 </td>  
                
                  <td><span class="text-black">&#8377; </span><?php $paidamount=$this->Comman->findpaidtransportamount($service['id'],$service['acedmicyear']);
                 $total1=0;
                 foreach($paidamount as $key=>$value)
                     {
                 	 
						$total1 +=$value['fee'];
						$total_transport_paidamount +=$value['fee'];
    }
           echo $total1;
		
		 
                  ?></td> 
                 <td><a href="<?php echo SITE_URL;?>admin/studentfees/index/<?php echo $service['id'] ; ?>/<?php echo $service['acedmicyear'] ; ?>/?id=academic"<span class="fa fa-history fa-lg"></span></a></td>
                          </tr>
		<?php $counter++;} } else {?>
		<td colspan="7" align="center">No Meta Available</td></tr>
                    <?php } ?>
                 <tr>
                 <td></td>     
                <td></td>     
                <td></td>     
                <td></td>     
                <td></td>
                  <td></td>  
                    <td></td>       
                <td class="text-bold text-green">Total Amount</td>  
                <td class="text-bold text-green"><span class="text-black">&#8377; </span><?php echo $totaltransportamount;    ?></td>  
                <td class="text-bold text-green"><span class="text-black">&#8377; </span><?php echo $total_transport_paidamount;    ?></td>  
                </tr>
         </table>      
      
      <div>      
    



