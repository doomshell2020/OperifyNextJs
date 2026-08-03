<div class="table-responsive">
  <table id="" class="table table-bordered table-striped">
<tbody> 
   <tr>
   <td><a id="" style="position: absolute;
top: 286px;
/* right: 0px; */
right: 46px;" class="btn btn-info btn-sm pull-right" href="<?php echo ADMIN_URL ;?>report/user_supportiv8"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel</a></td>
   </tr>
    <tr>
    <th>#</th>
    <th>Student Id</th>
    <th>Student Name</th>
    <th>Class</th>
    <th>Section</th>
    <th>Room</th>
    <th>Total Amount</th>
    <th>Discount</th>
    <th>Paid Amount</th>
    </tr>
		       
  	<?php 
  	                   $session = $this->request->session();
		               $session->delete($hostel); 
			           $session->write('hostel',$hostel);
      $page = $this->request->params['paging']['Services']['page'];
		$limit = $this->request->params['pagc_c_iding']['Services']['perPage'];
		$counter = ($page * $limit) - $limit + 1;
		$totalhostelamount=0;
		$total_hostel_paidamount=0;
		$discount=0;
		if(isset($hostel) && !empty($hostel)){
		foreach($hostel as $service){    ?>
				 <tr>
					
			     <td><?php echo $counter;?></td>	
			      <td><?php echo $service['id']; ?></td>       	 	
                 <td><a href="<?php echo SITE_URL;?>admin/students/view/<?php echo $service['id'] ; ?>"><?php echo $service['fname']." ".$service['middlename']." ".$service['lname']; ?></a></td>   
                 <td><?php echo $service['classtitle']; ?></td> 
                 <td> <?php  echo $service['sectiontitle'];  ?></td> 
                 <td> <?php  echo "Room-".$service['room_no'];  ?></td> 
 
              
               <td><span class="text-black">&#8377; </span><?php $amount=$this->Comman->findhostelamount($service['id'],$service['acedmicyear'],$service['h_id']); 
                 
                 $totalhostelamount +=$amount[0]['amount']; 
                 
                   echo $amount[0]['amount']; 
                 ?>
                 </td>  
                <td><span class="text-black">&#8377; </span><?php  echo $amount[0]['amount']-$amount[0]['fee'];  
                 $discount+=$amount[0]['amount']-$amount[0]['fee'];  
                
                 ?></td>
                <td><span class="text-black">&#8377; </span><?php  echo $amount[0]['fee'];   
                $total_hostel_paidamount  += $amount[0]['fee'];   
                
                ?></td>
	
                 <td><a href="<?php echo SITE_URL;?>admin/studentfees/index/<?php echo $service['id'] ; ?>/<?php echo $service['acedmicyear'] ; ?>?id=guardians"<span class="fa fa-history fa-lg"></span></a></td> 
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
                <td class="text-bold text-green">Total Amount</td>  
                <td class="text-bold text-green"><span class="text-black">&#8377; </span><?php echo $totalhostelamount;    ?></td>  
                <td class="text-bold text-green"><span class="text-black">&#8377; </span><?php echo $discount;    ?></td>  
                <td class="text-bold text-green"><span class="text-black">&#8377; </span><?php echo $total_hostel_paidamount;    ?></td>  
                </tr>
         </table>      
      
      <div>      
    



