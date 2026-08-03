    <?php $role_id=$this->request->session()->read('Auth.User.role_id');  ?>
    <?php
            $page = $this->request->params['paging']['DropOutStudent']['page'];
            $limit = $this->request->params['paging']['DropOutStudent']['perPage'];
            $counter = ($page * $limit) - $limit + 1; 

            if(isset($students) && !empty($students)){ 
               foreach ($students as $work) {

        ?><?php $att_exist = $this->Comman->findAttendance($work['id']);
        //pr($att_exist);

        //pr($period);die;?>
                  <tr <?php if (!empty($att_exist['id'])) {?>style="color:red" <?php }?>>


                  
                    <td><?php echo $counter; ?></td>
                    <!-- <td><img src="<?php //echo $this->request->webroot;?>img/studentlist_img.png" </td>-->
                    <td > 
                  <?php if($role_id=='5' || $role_id=='8'){  ?><a title="View Student Detail" href="<?php echo ADMIN_URL; ?>employees/viewhistory/<?php echo $work['id']; ?>"><?php echo strtoupper($work['fname']);
            ?>&nbsp;<?php echo strtoupper($work['middlename']);
            ?>&nbsp;<?php echo strtoupper($work['lname']);
            ?></a> <?php } ?></td>
                    <td><?php echo $work['username'];

        ?></td>
                    
                    <td><?php echo $work['mobile'];
        ?></td>
                    <td><?php echo $work['f_h_name'];
        ?></td>
                    <td><?php echo date('d-m-Y', strtotime($work['dob']));
        ?></td>
                    <td><?php echo date('d-m-Y', strtotime($work['joiningdate']));
        ?></td>
                     <td><?php if ($role_id == '5' || $role_id == '8') {
				 if($work['nodues'] !=''){ ?> 
            <a href="<?php echo SITE_URL;?>admin/employees/relieving_certificate_info/<?php echo $work['id']; ?>" title="Relieving Certificate" data-target="#globalModal"  data-toggle="modal" class="global" style="color: blue;">
                    <i class="fa fa-file-pdf-o"></i>
                  </a>&nbsp; <a download="nodues-<?php echo $work['username']; ?>" href="<?php echo SITE_URL;?>images/<?php echo $work['nodues']; ?>" title="NO DUES" style="color: green;"><i class="fa fa-file-pdf-o"></i></a> &nbsp;
                  <a href="<?php echo SITE_URL;?>admin/employees/restore/<?php echo $work['id']; ?>" title="Restore Employee" style="color: maroon;"><img src="https://use.fontawesome.com/releases/v5.0.13/svgs/solid/undo.svg" width="15" height="15"></a> 
                
                  <?php }else{   ?>  
<a  class="uploadconh uploadtawrd-222">
<input type="file" title="Upload No Dues" id="contractdoc-<?php echo $work['id']; ?>" data-type="<?php echo $work['id']; ?>" name="contractdoc" class="fa fa-paperclip upload form-control" style="width: 20px;"></a>
<a target="_blank" href="<?php echo SITE_URL;?>admin/employees/nodues_pdf/<?php echo $work['id']; ?>" title="NO DUES Form" style="color: green;"><i class="fa fa-file-pdf-o"></i></a> &nbsp;
<a href="<?php echo SITE_URL;?>admin/employees/restore/<?php echo $work['id']; ?>" title="Restore Employee" style="color: maroon;"><img src="https://use.fontawesome.com/releases/v5.0.13/svgs/solid/undo.svg" width="15" height="15"></a> 
                

			 <?php } ?>
                   
                 <?php } ?></td> 
                  </tr><?php $counter++;
    } }else{ ?>
     <tr>
  <td colspan="8" align="center"><b style="color:red;">NO Data Available</b></td>
    </tr>
    
    <?php } ?>	
