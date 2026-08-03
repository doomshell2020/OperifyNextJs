<div class="table-responsive">
  <table id="" class="table table-bordered table-striped">
<tbody> 
   <tr>
   <td><a id="" style="position: absolute;
top: 509px;
/* right: 0px; */
right: 46px;" class="btn btn-info btn-sm pull-right" href="<?php echo ADMIN_URL ;?>report/user_supportiv4"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel</a></td>
   </tr>
    <tr>
    <th>#</th>
     <?php if(array_key_exists('id',$employee[0])) { ?> <th>Employee Id</th> <?php } ?>
     <?php if(array_key_exists('fname',$employee[0])) { ?> <th>Fname</th> <?php } ?>
     <?php if(array_key_exists('middlename',$employee[0])) { ?> <th>Middle Name</th><?php } ?>
     <?php if(array_key_exists('lname',$employee[0])) { ?> <th>Last Name</th><?php } ?>
     <?php if(array_key_exists('email',$employee[0])) { ?> <th>Email</th><?php } ?>
     <?php if(array_key_exists('martial_status',$employee[0])) { ?> <th>Martial Status</th><?php } ?>
     <?php if(array_key_exists('gender',$employee[0])) { ?> <th>Gender</th><?php } ?>
     <?php if(array_key_exists('dob',$employee[0])) { ?> <th>Date Of Birth</th><?php } ?>
     <?php if(array_key_exists('mobile',$employee[0])) { ?> <th>Mobile</th><?php } ?>
     <?php if(array_key_exists('hobbies',$employee[0])) { ?> <th>Hobbies</th><?php } ?>
     <?php if(array_key_exists('aadharno',$employee[0])) { ?> <th>Attendance Card ID</th><?php } ?>
     <?php if(array_key_exists('department_id',$employee[0])) { ?> <th>Department</th><?php } ?>
     <?php if(array_key_exists('designation_id',$employee[0])) { ?> <th>Designation</th><?php } ?>
     <?php if(array_key_exists('nationality',$employee[0]))  { ?> <th>Nationality</th><?php } ?>
     <?php if(array_key_exists('joiningdate',$employee[0])) {  ?> <th>Joining Date</th><?php } ?>
     <?php if(array_key_exists('qualifications',$employee[0])) {  ?> <th>Qualification</th><?php } ?>
     <?php if(array_key_exists('specialization',$employee[0])) {  ?> <th>Specialization</th><?php } ?>
     <?php if(array_key_exists('reference',$employee[0])) {  ?> <th>Reference</th><?php } ?>
     <?php if(array_key_exists('accountno',$employee[0])) {  ?> <th>Bank Account No</th><?php } ?>
     <?php if(array_key_exists('c_address',$employee[0])) { ?> <th>Current Address</th><?php } ?>
     <?php if(array_key_exists('c_city_id',$employee[0])) { ?> <th>Current City</th><?php } ?>
     <?php if(array_key_exists('c_s_id',$employee[0]))  { ?> <th>Current State</th><?php } ?>
     <?php if(array_key_exists('c_c_id',$employee[0]))  { ?> <th>Current Country</th><?php } ?>
     <?php if(array_key_exists('c_pincode',$employee[0])) { ?> <th>Pin Code</th><?php } ?>
     <?php if(array_key_exists('p_address',$employee[0]))  { ?> <th>Permanant Address</th><?php } ?>
     <?php if(array_key_exists('p_city_id',$employee[0]))  { ?> <th>Permanant City</th><?php } ?>
     <?php if(array_key_exists('p_s_id',$employee[0])) { ?> <th>Permanant State</th><?php } ?>
     <?php if(array_key_exists('p_c_id',$employee[0])) { ?> <th>Permanant Country</th><?php } ?>
     <?php if(array_key_exists('p_pincode',$employee[0]))  { ?> <th>Pin Code</th><?php } ?>
     <?php if(array_key_exists('fullname',$employee[0]))  { ?> <th>Fullname</th><?php } ?>
     <?php if(array_key_exists('relation',$employee[0]))  { ?> <th>Relation</th><?php } ?>
     <?php if(array_key_exists('qualification',$employee[0]))  { ?> <th>G.Qualification</th><?php } ?>
     <?php if(array_key_exists('occupation',$employee[0]))  { ?> <th>Occupation</th><?php } ?>
     <?php if(array_key_exists('mobileno',$employee[0]))  { ?> <th>G.Mobile No</th><?php } ?>
     <?php if(array_key_exists('emails',$employee[0]))  { ?> <th>G.Email</th><?php } ?>
 
    </tr>
		       
  	<?php 
      $page = $this->request->params['paging']['Services']['page'];
		$limit = $this->request->params['pagc_c_iding']['Services']['perPage'];
		$counter = ($page * $limit) - $limit + 1;
		if(isset($employee) && !empty($employee)){ //pr($personalsd);
		foreach($employee as $service){    ?>
				 <tr>
					
			     <td><?php echo $counter;?></td>
			     	 	
      <?php if(in_array('employees.id',$personalsd)){ ?> <td><?php echo $service['id']; ?></td><?php   } ?>
      <?php if(in_array('fname',$personalsd)){ ?> <td><?php echo $service['fname'];?> </td>   <?php } ?>
      <?php if(in_array('middlename',$personalsd)){ ?> <td><?php echo $service['middlename'];?> </td>   <?php } ?>
      <?php if(in_array('lname',$personalsd)){ ?> <td><?php echo $service['lname'];?> </td>   <?php } ?>
      <?php if(in_array('employees.email',$personalsd)){ ?> <td><?php echo $service['email'];?> </td>   <?php } ?>
      <?php if(in_array('martial_status',$personalsd)){ ?> <td><?php echo $service['martial_status'];?> </td>   <?php } ?>
      <?php if(in_array('gender',$personalsd)){ ?> <td><?php echo $service['gender'];?> </td>   <?php } ?>
      <?php if(in_array('dob',$personalsd)){ ?> <td><?php echo $service['dob'];?> </td>   <?php } ?>
      <?php if(in_array('mobile',$personalsd)){ ?> <td><?php echo $service['mobile'];?> </td>   <?php } ?>
      <?php if(in_array('hobbies',$personalsd)){ ?> <td><?php echo $service['hobbies'];?> </td>   <?php } ?>
      <?php if(in_array('aadharno',$personalsd)){ ?> <td><?php echo $service['aadharno'];?> </td>   <?php } ?>
      <?php if(in_array('department_id',$personalsd)){ ?> <td><?php  $department=$this->Comman->finddepartment($service['department_id']);
               echo $department[0]['name']; ?>                                                      
       </td>   <?php } ?>
      <?php if(in_array('designation_id',$personalsd)){ ?> <td><?php  $designation=$this->Comman->finddesignation($service['designation_id']);
               echo $designation[0]['name']; ?>                                                      
       </td>   <?php } ?>
      <?php if(in_array('nationality',$personalsd)){ ?> <td><?php echo $service['nationality'];?> </td>   <?php } ?>
      <?php if(in_array('joiningdate',$personalsd)){ ?> <td><?php echo $service['joiningdate'];?> </td>   <?php } ?>
      <?php if(in_array('otherinfos.qualifications',$personalsd)){ ?> <td><?php echo $service['qualifications'];?> </td>   <?php } ?>
      <?php if(in_array('specialization',$personalsd)){ ?> <td><?php echo $service['specialization'];?> </td>   <?php } ?>
      <?php if(in_array('reference',$personalsd)){ ?> <td><?php echo $service['reference'];?> </td>   <?php } ?>
      <?php if(in_array('accountno',$personalsd)){ ?> <td><?php echo $service['accountno'];?> </td>   <?php } ?>
      <?php if(in_array('c_address',$personalsd)){ ?> <td><?php echo $service['c_address'];?> </td>   <?php } ?>
      <?php if(in_array('c_city_id',$personalsd)){ ?><td><?php  $cty_id =$service['c_city_id'];
					  $cities=$this->Comman->findcities($cty_id);
					 echo $cities[0]['name']; ?></td> <?php }  ?>                  
     <?php if(in_array('c_s_id',$personalsd)){ ?><td><?php  $st_id =$service['c_s_id'];
					  $states=$this->Comman->findstates($st_id);
					 echo $states[0]['name']; ?></td> <?php } ?>                     
     <?php if(in_array('c_c_id',$personalsd)){ ?><td><?php  $cont =$service['c_c_id'];
					  $country=$this->Comman->findcountries($cont);
					 echo $country[0]['name']; ?></td> <?php  } ?>                    
      <?php  if(in_array('c_pincode',$personalsd)){ ?><td><?php echo $service['c_pincode']; ?></td> <?php  } ?>                   
      <?php if(in_array('p_address',$personalsd)){ ?><td><?php echo $service['p_address']; ?></td> <?php } ?>                    
      <?php if(in_array('p_city_id',$personalsd)){ ?> <td><?php  $p_cty_id =$service['p_city_id'];
					  $p_cities=$this->Comman->findcities($p_cty_id);
					 echo $p_cities[0]['name']; ?></td> <?php } ?>                    
      <?php if(in_array('p_s_id',$personalsd)){ ?><td><?php  $p_st_id =$service['p_s_id'];
					  $p_states=$this->Comman->findstates($p_st_id);
					 echo $p_states[0]['name']; ?></td> <?php }  ?>                     
      <?php if(in_array('p_c_id',$personalsd)){ ?><td><?php  $p_cont =$service['p_c_id'];
					  $p_country=$this->Comman->findcountries($p_cont);
					 echo $p_country[0]['name']; ?></td> <?php } ?>                    
      <?php if(in_array('p_pincode',$personalsd)){?><td><?php echo $service['p_pincode']; ?></td> <?php  } ?>        
      <?php if(in_array('fullname',$personalsd)){?><td><?php echo $service['fullname']; ?></td> <?php } ?>         
      <?php if(in_array('relation',$personalsd)){ ?><td><?php echo $service['relation']; ?></td> <?php }  ?>        
      <?php if(in_array('guardians.qualification',$personalsd)){?><td><?php echo $service['qualification']; ?></td> <?php   } ?>        
      <?php if(in_array('occupation',$personalsd)){ ?><td><?php echo $service['occupation']; ?></td> <?php }  ?>      
      <?php if(in_array('mobileno',$personalsd)){?><td><?php echo $service['mobileno']; ?></td> <?php }  ?>       
      <?php if(in_array('guardians.emails',$personalsd)){ ?><td><?php echo $service['emails']; ?></td> <?php }  ?>        
            
                          </tr>
		<?php $counter++;} } else {?>
		<td colspan="7" align="center">No Meta Available</td></tr>
                    <?php } ?>

         </table>      
      
      <div>      
    


