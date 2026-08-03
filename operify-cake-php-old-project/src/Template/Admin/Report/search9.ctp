<div class="table-responsive">
  <table id="" class="table table-bordered table-striped">
<tbody> 
   <tr>
   <td><a id="" style="position: absolute;
top: 109px;
/* right: 0px; */
right: 46px;" class="btn btn-info btn-sm pull-right" href="<?php echo ADMIN_URL ;?>report/user_supportiv9"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel</a></td>
   </tr>
    <tr>
    <th>#</th>
    <th>Emp-Id</th>
    <th>E-Name</th>
    <th>E-Mobile</th>
    <th>Father</th>
    <th>Department</th>
    <th>Designation</th>
    <th>Leave Type</th>
    <th>Start Date</th>
    <th>End Date</th>
    <th>Total Days</th>
    </tr>
		       
  	<?php 
  	                   $session = $this->request->session();
		               $session->delete($leav); 
			           $session->write('leav',$leav);
      $page = $this->request->params['paging']['Services']['page'];
		$limit = $this->request->params['pagc_c_iding']['Services']['perPage'];
		$counter = ($page * $limit) - $limit + 1;
		$totalhostelamount=0;
		$total_hostel_paidamount=0;
		$discount=0;
		if(isset($leav) && !empty($leav)){
		foreach($leav as $service){     ?>
				 <tr>
					
			     <td><?php echo $counter;?></td>	
			      <td><?php echo $service['id']; ?></td>       	 	
                 <td><?php echo $service['fname']; ?></td> 
                 <td> <?php  echo $service['mobile'];  ?></td> 
               <td><?php $guardian=$this->Comman->find_guardiannames($service['id']) ;
                      
                          $guardian_name=$guardian[0]['fullname']; 
                          if(!empty($guardian_name))
                          {
                       echo   $guardian_name;
					  }
					  else
					  {
						  
						  echo "-";
					  }
                          ?></td>
             <td><?php if(isset($service['department_id'])){ 
					  $department_id=$this->Comman->finddepartment($service['department_id']) ; 
                     // $designation_id=$this->Comman->finddesignation($employee['designation_id']) ;
					  echo $department_id[0]['name'];}else{ echo 'N/A'; } ?></td>
                
                  <td><?php if(isset($service['designation_id'])){ 
					  $designation_id=$this->Comman->finddesignation($service['designation_id']) ; 
					  echo $designation_id[0]['name'];}else{ echo 'N/A'; } ?></td>
       
	             <td><?php echo $service['name']; ?></td> 
	             <td><?php echo $service['date_from']; ?></td> 
                 <td> <?php  echo $service['date_to'];  ?></td> 
                 <td> <?php  echo $service['t_days'];  ?></td> 
                
                          </tr>
		<?php $counter++;} } else {?>
		<td colspan="7" align="center">No Meta Available</td></tr>
                    <?php } ?>
         </table>      
      
      <div>      
    



