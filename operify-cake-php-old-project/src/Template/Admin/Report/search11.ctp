<div class="table-responsive">
  <table id="" class="table table-bordered table-striped">
<tbody> 
   <tr>
   <td><a id="" style="position: absolute;
top: 124px;
/* right: 0px; */
right: 46px;" class="btn btn-info btn-sm pull-right" href="<?php echo ADMIN_URL ;?>report/user_supportiv11"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel</a></td>
   </tr>
    <tr>
    <th>#</th>
    <th>Student Id</th>
    <th>Student Name</th>
    <th>Class</th>
    <th>Section</th>
    <th>Hostel Name</th>
    <th>Occupied Room</th>
    <th>EPBX</th>
    </tr>
		       
  	<?php 
  	                   $session = $this->request->session();
		               $session->delete($student_hostel); 
			           $session->write('student_hostel',$student_hostel);
      $page = $this->request->params['paging']['Services']['page'];
		$limit = $this->request->params['pagc_c_iding']['Services']['perPage'];
		$counter = ($page * $limit) - $limit + 1;
		$totalhostelamount=0;
		$total_hostel_paidamount=0;
		$discount=0;
		if(isset($student_hostel) && !empty($student_hostel)){
		foreach($student_hostel as $service){ //pr($service);  ?>
				 <tr>
					
			     <td><?php echo $counter;?></td>	
			      <td><?php echo $service['id']; ?></td>       	 	
                 <td><a href="<?php echo SITE_URL;?>admin/students/view/<?php echo $service['id'] ; ?>"><?php echo $service['fname']." ".$service['middlename']." ".$service['lname']; ?></a></td>   
                 <td><?php echo $service['classtitle']; ?></td> 
                 <td> <?php  echo $service['sectiontitle'];  ?></td> 
                 <td> <?php  echo $service['hostelname'];  ?></td> 
                 <td> <?php  echo "Room-".$service['room_no'];  ?></td> 
                 <td> <?php echo $epbx=implode("",$this->Comman->findepbx($service['h_id']));  
             
                 
                 ?></td> 
                          </tr>
		<?php $counter++;} } else {?>
		<td colspan="7" align="center">No Meta Available</td></tr>
                    <?php } ?>
         </table>      
      
      <div>      
    



