<div class="table-responsive">
  <table id="" class="table table-bordered table-striped">
<tbody>
   <tr>
   <td><a id="" style="position: absolute;
top: 369px;
/* right: 0px; */
right: 46px;" class="btn btn-info btn-sm pull-right" href="<?php echo ADMIN_URL ;?>report/user_supportiv3"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel</a></td>
   </tr>
    <tr>
    <th>#</th>
     <?php if(array_key_exists('enroll',$s_enquiry[0])) { ?> <th>Scholar.No.</th> <?php } ?>
     <?php if(array_key_exists('fname',$s_enquiry[0])) { ?> <th>Name</th> <?php } ?>
     <?php if(array_key_exists('username',$s_enquiry[0])) { ?> <th>Email</th> <?php } ?>
     <?php if(array_key_exists('gender',$s_enquiry[0])) { ?> <th>Gender</th><?php } ?>
     <?php if(array_key_exists('dob',$s_enquiry[0])) { ?> <th>Date Of Birth</th><?php } ?>
     <?php if(array_key_exists('height',$s_enquiry[0]))  { ?> <th>Height</th><?php } ?>
 <?php if(array_key_exists('weight',$s_enquiry[0]))  { ?> <th>Weight</th><?php } ?>
 <?php if(array_key_exists('oldenroll',$s_enquiry[0]))  { ?> <th>Old Enroll</th><?php } ?>
   <?php if(array_key_exists('adaharnumber',$s_enquiry[0]))  { ?> <th>Adahar number</th><?php } ?>
 <?php if(array_key_exists('cast',$s_enquiry[0]))  { ?> <th>Cast</th><?php } ?>
  <?php if(array_key_exists('religion',$s_enquiry[0]))  { ?> <th>Religion</th><?php } ?>


   <?php if(array_key_exists('category',$s_enquiry[0]))  { ?> <th>Category</th><?php } ?>
    <?php if(array_key_exists('bloodgroup',$s_enquiry[0]))  { ?> <th>Bloodgroup</th><?php } ?>
     <?php if(array_key_exists('disability',$s_enquiry[0]))  { ?> <th>Disability</th><?php } ?>
     <?php if(array_key_exists('mother_tounge',$s_enquiry[0]))  { ?> <th>Mother Tounge</th><?php } ?>
      <?php if(array_key_exists('address',$s_enquiry[0]))  { ?> <th>Address</th><?php } ?>
      <?php if(array_key_exists('rf_id',$s_enquiry[0]))  { ?> <th>RF ID</th><?php } ?>
      <?php if(array_key_exists('rfidhexcode',$s_enquiry[0]))  { ?> <th>RF ID HEX CODE</th><?php } ?>

     <?php if(array_key_exists('mobile',$s_enquiry[0])) { ?> <th>Mobile</th><?php } ?>

     <?php if(array_key_exists('sms_mobile',$s_enquiry[0])) { ?> <th>SMS Mobile</th><?php } ?>
     <?php if(array_key_exists('f_phone',$s_enquiry[0])) { ?> <th>Father Phone</th><?php } ?>
     <?php if(array_key_exists('m_phone',$s_enquiry[0])) { ?> <th>Mother Phone</th><?php } ?>
     <?php if(array_key_exists('admissionyear',$s_enquiry[0]))  { ?> <th>Admission Year</th><?php } ?>
     <?php if(array_key_exists('acedmicyear',$s_enquiry[0]))  { ?> <th>Academic Year</th><?php } ?>
     <?php if(array_key_exists('created',$s_enquiry[0])) { ?> <th>Admission Date</th><?php } ?>
     <?php if(array_key_exists('formno',$s_enquiry[0])) { ?> <th>Form No.</th><?php } ?>



     <?php if(array_key_exists('board_id',$s_enquiry[0])) { ?> <th>Board</th><?php } ?>

     <?php if(array_key_exists('admissionclass',$s_enquiry[0]))  { ?> <th>Admission Class</th><?php } ?>
     <?php if(array_key_exists('class_id',$s_enquiry[0]))  { ?> <th>Class</th><?php } ?>
     <?php if(array_key_exists('section_id',$s_enquiry[0])) {  ?> <th>Section</th><?php } ?>
     <?php if(array_key_exists('h_id',$s_enquiry[0])) {  ?> <th>House</th><?php } ?>

     <?php if(array_key_exists('discountcategory',$s_enquiry[0])) { ?> <th> Discount</th><?php } ?>
     <?php if(array_key_exists('is_lc',$s_enquiry[0])) { ?> <th> Is Learning Center</th><?php } ?>
     <?php if(array_key_exists('is_special',$s_enquiry[0])) { ?> <th> Is Special</th><?php } ?>

     <?php if(array_key_exists('fathername',$s_enquiry[0]))  { ?> <th>Father Name</th><?php } ?>
      <?php if(array_key_exists('mothername',$s_enquiry[0]))  { ?> <th>Mother Name</th><?php } ?>
      <?php if(array_key_exists('fee_submittedby',$s_enquiry[0]))  { ?> <th>Fee Submitted By</th><?php } ?>
      <?php if(array_key_exists('f_qualification',$s_enquiry[0]))  { ?> <th>Father Qualification</th><?php } ?>
      <?php if(array_key_exists('m_qualification',$s_enquiry[0]))  { ?> <th>Mother Qualification</th><?php } ?>
      <?php if(array_key_exists('f_occupation',$s_enquiry[0]))  { ?> <th>Father Occupation</th><?php } ?>
      <?php if(array_key_exists('m_occupation',$s_enquiry[0]))  { ?> <th>Mother Occupation</th><?php } ?>

    </tr>

  	<?php
      $page = $this->request->params['paging']['Services']['page'];
		$limit = $this->request->params['pagc_c_iding']['Services']['perPage'];
		$counter = ($page * $limit) - $limit + 1;


		if(isset($s_enquiry) && !empty($s_enquiry)){
		foreach($s_enquiry as $service){  ?>
				 <tr>
			     <td><?php echo $counter;?></td>

      <?php if(in_array('s.enroll',$allarray)){ ?> <td><?php echo $service['enroll']; ?></td><?php } ?>
      <?php if(in_array('s.fname',$allarray)){ ?><td><?php echo $service['fname']." ".$service['middlename']." ".$service['lname']; ?></td> <?php }?>

      <?php if(in_array('s.username',$allarray)){?><td><?php echo $service['username']; ?></td> <?php } ?>
      <?php if(in_array('s.gender',$allarray)){?><td><?php echo $service['gender']; ?></td> <?php } ?>
      <?php if(in_array('s.dob',$allarray)){ ?><td><?php echo date('d-m-Y',strtotime($service['dob'])); ?></td> <?php } ?>
     <?php if(in_array('s.height',$allarray)){ ?><td><?php if($service['height']){ echo $service['height']; }else{ echo "--"; } ?></td> <?php } ?>
     <?php if(in_array('s.weight',$allarray)){ ?><td><?php if($service['weight']){ echo $service['weight']; }else{ echo "--"; } ?></td> <?php } ?>
     <?php  if(in_array('s.oldenroll',$allarray)){ ?><td><?php  if($service['oldenroll']=="600244"){ $service['oldenroll']="6024";  } if($service['oldenroll']){  echo $service['oldenroll']; }else{ echo "--"; } ?></td> <?php } ?>
     <?php if(in_array('s.adaharnumber',$allarray)){ ?><td><?php if($service['adaharnumber']){ echo $service['adaharnumber']; }else{ echo "--"; } ?></td> <?php } ?>
     <?php if(in_array('s.cast',$allarray)){ ?><td><?php if($service['cast']){ echo $service['cast']; }else{ echo "--"; } ?></td> <?php } ?>
     <?php if(in_array('s.religion',$allarray)){ ?><td><?php if($service['religion']){ echo $service['religion']; }else{ echo "--"; } ?></td> <?php } ?>
     <?php if(in_array('s.category',$allarray)){ ?><td><?php if($service['category']){ echo $service['category']; }else{ echo "--"; } ?></td> <?php } ?>
     <?php if(in_array('s.bloodgroup',$allarray)){ ?><td><?php if($service['bloodgroup']){ echo $service['bloodgroup']; }else{ echo "--"; } ?></td> <?php } ?>
     <?php if(in_array('s.disability',$allarray)){ ?><td><?php if($service['disability']){ $disablity=$this->Comman->finddisability($service['disability']); echo $disablity['name']; }else{ echo "--"; } ?></td> <?php } ?>
     <?php if(in_array('s.mother_tounge',$allarray)){ ?><td><?php if($service['mother_tounge']){ echo $service['mother_tounge']; }else{ echo "--"; } ?></td> <?php } ?>
     <?php if(in_array('s.address',$allarray)){ ?><td><?php if($service['address']){ echo $service['address']; }else{ echo "--"; } ?></td> <?php } ?>
     <?php if(in_array('s.rf_id',$allarray)){ ?><td><?php if($service['rf_id']){ echo $service['rf_id']; }else{ echo "--"; } ?></td> <?php } ?>
     <?php if(in_array('s.rfidhexcode',$allarray)){ ?><td><?php if($service['rfidhexcode']){ echo $service['rfidhexcode']; }else{ echo "--"; } ?></td> <?php } ?>

      <?php if(in_array('s.mobile',$allarray)){ ?><td><?php echo $service['mobile']; ?></td> <?php } ?>
        <?php if(in_array('s.sms_mobile',$allarray)){ ?><td><?php echo $service['sms_mobile']; ?></td> <?php } ?>
        <?php if(in_array('s.f_phone',$allarray)){ ?><td><?php if($service['f_phone']){ echo $service['f_phone'];  }else{  echo "--"; }?></td> <?php } ?>
        <?php if(in_array('s.m_phone',$allarray)){ ?><td><?php if($service['m_phone']){ echo $service['m_phone'];  }else{  echo "--"; }?></td> <?php } ?>

        <?php if(in_array('s.admissionyear',$allarray)){ ?><td><?php echo $service['admissionyear']; ?></td> <?php } ?>
             <?php if(in_array('s.acedmicyear',$allarray)){ ?><td><?php echo $service['acedmicyear']; ?></td> <?php } ?>



      <?php if(in_array('s.created',$allarray)){ ?><td><?php echo date('d-m-Y',strtotime($service['created'])); ?></td> <?php } ?>
          <?php if(in_array('s.formno',$allarray)){ ?><td><?php if($service['formno']){ echo $service['formno'];  }else{  echo "--"; }?></td> <?php } ?>



       <?php if(in_array('s.board_id',$allarray)){ ?><td><?php if($service['board_id']=='1'){ echo "CBSE"; }else if($service['board_id']=='2'){ echo "CAMBRIDGE"; } else if($service['board_id']=='3'){ echo "IBDP"; } ?></td> <?php } ?>

                  <?php if(in_array('s.admissionclass',$allarray)){?><td><?php  $cs_id =$service['admissionclass'];
					  $admissionclass=$this->Comman->showadmissionclasstitle($cs_id);
					 echo $admissionclass['title']; ?></td> <?php } ?>
      <?php if(in_array('s.class_id',$allarray)){?><td><?php  $c_id =$service['class_id'];
					  $class=$this->Comman->findclasses($c_id);
					 echo $class[0]['title']; ?></td> <?php } ?>


      <?php if(in_array('s.section_id',$allarray)){?><td><?php  $sec_id =$service['section_id'];
					  $section=$this->Comman->findsections($sec_id);
					 echo $section[0]['title']; ?></td> <?php } ?>
	  <?php if(in_array('s.h_id',$allarray)){?><td><?php  $h_id =$service['h_id'];
					  $hid=$this->Comman->findhouse($h_id);
					    $house_id=$this->Comman->findhouse($service['house_id']);
					 if($hid){ echo $hid['name']; }else if($house_id){  echo $house_id['name'];  }?></td> <?php } ?>
					<?php if(in_array('s.discountcategory',$allarray)){ ?><td><?php if($service['discountcategory']){ echo $service['discountcategory'];  }else{  echo "--"; }?></td> <?php } ?>
					<?php if(in_array('s.is_lc',$allarray)){ ?><td><?php if($service['is_lc']){ echo $service['is_lc'];  }else{  echo "--"; }?></td> <?php } ?>
					<?php if(in_array('s.is_special',$allarray)){ ?><td><?php if($service['is_special']){ echo $service['is_special'];  }else{  echo "--"; }?></td> <?php } ?>




       <?php if(in_array('s.fathername',$allarray)){ ?><td><?php echo $service['fathername']; ?></td> <?php } ?>

              <?php if(in_array('s.mothername',$allarray)){ ?><td><?php echo $service['mothername']; ?></td> <?php } ?>

              		<?php if(in_array('s.fee_submittedby',$allarray)){ ?><td><?php if($service['fee_submittedby']){ echo $service['fee_submittedby'];  }else{  echo "--"; }?></td> <?php } ?>
              		<?php if(in_array('s.f_qualification',$allarray)){ ?><td><?php if($service['f_qualification']){ echo $service['f_qualification'];  }else{  echo "--"; }?></td> <?php } ?>
              		      		<?php if(in_array('s.m_qualification',$allarray)){ ?><td><?php if($service['m_qualification']){ echo $service['m_qualification'];  }else{  echo "--"; }?></td> <?php } ?>
              		<?php if(in_array('s.f_occupation',$allarray)){ ?><td><?php if($service['f_occupation']){ echo $service['f_occupation'];  }else{  echo "--"; }?></td> <?php } ?>
              		<?php if(in_array('s.m_occupation',$allarray)){ ?><td><?php if($service['m_occupation']){ echo $service['m_occupation'];  }else{  echo "--"; }?></td> <?php } ?>



                          </tr>
		<?php $counter++;} } else {?>
		<td colspan="7" align="center">No Student Information Available</td></tr>
                    <?php } ?>

         </table>

      <div>
