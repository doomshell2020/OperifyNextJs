<script type="text/javascript">
$("#checkall").change(function(){
     var checked = $(this).is(':checked');
     if(checked){
       $(".checkbox").each(function(){
         $(this).prop("checked",true);
       });
     }else{
       $(".checkbox").each(function(){
         $(this).prop("checked",false);
       });
     }
   });
</script>
<?php echo $this->Form->create('Interaction',array('url'=>array('controller'=>'Report','action'=>'prospect_interaction'),'onsubmit'=>'return inputsHaveDuplicateValues();','type'=>'file','inputDefaults'=>array('div'=>false,'label'=>false),'id'=>'stud-attendance-form','class'=>'')); ?> 
     
       
             <div class="col-sm-12" align="right">
              <label></label>
<input type="button" style="background-color:#00c0ef;color:white;width:100px !important; margin-left: 10px;" class="btn btn4 btn_pdf myscl-btn date" name="Invite" value="Invite" id="miv">
        <input type="submit" style="background-color:green;color:white;width:100px !important; margin-left: 10px;" class="btn btn4 btn_pdf myscl-btn date inv" name="Approve" value="Approve"> 
        <input type="submit" style="background-color:red;color:white;width:100px !important; margin-left: 10px;" class="btn btn4 btn_pdf myscl-btn date inv" name="Reject" value="Reject"> 
             </div>
           

      <div style="clear: both;"></div>
       <div><a id="" style="margin-right: 20px; margin-top: 10px;" class="btn btn-info btn-sm pull-right" href="<?php echo ADMIN_URL ;?>report/prospect_excel"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Export Excel</a></div>
 <div style="clear: both;"></div>
            <div class="box-body">
 
<script type="text/javascript">
  
        $('.inv').click(function(){
        var sd= $(".checkbox:checked").length;
        if(sd==0){
          alert("Please Select One Prospectus Atleast.")
          return false;
        }else{
          return true;
        }
        });

        $('#miv').click(function(){
          var sd= $(".checkbox:checked").length;
        if(sd==0){
          alert("Please Select One Prospectus Atleast.")
          return false;
        }else{

         $('#myModal').modal('toggle');
        }

     });
 
  
</script>
     <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 style="text-align: center;">Inteaction Time</h4>
        </div>
        <div class="modal-body">
          <div class="form-group">
        <div id="g1" >
             <div class="col-sm-4" align="right">
<label for="inputEmail3" class="control-label g1">Interaction Time<span style="color:red;">*</span></label>

             </div>
             <div class="col-sm-4">
              <?php echo $this->Form->input('inter_time',array('class'=>'form-control g1','placeholder'=>'Time','id'=>'reservationtime','label' =>false,'required','style'=>'width: 318px')); ?>
             </div>
</div>
</div>
        </div>
        <div class="modal-footer">
          <input type="submit" style="background-color:#00c0ef;width:100px !important; margin-left: 10px;" class="btn btn4 btn_pdf myscl-btn date inv" name="Invite" value="Invite"> 
        </div>
      </div>
      
    </div>
  </div>
     
      
<div class="table-responsive">
                <table id="example1" class="table table-bordered table-striped">
                <thead>

        
        
                <tr>
                   <th width="10%"><input type="checkbox" id='checkall' /> Select All</th>
                  <th>#</th>
                  <th>Form No.</th>
                  <th>Academic Year</th>
                  <th>Pupil's Name</th>
                  <th>Father Mobile</th>
                  <th>Mother Mobile</th>
                  <th>Class</th>
                  
              
                   <th>Added On</th>
                   <th>Receipt/ Edit</th>
                  <th>Status</th>
                  <th>Action</th>
                 
                </tr>
                </thead>
                <tbody id="example22">

  
         

   

           
    <?php $page = $this->request->params['paging']['Services']['page'];
    $limit = $this->request->params['paging']['Services']['perPage'];
    $counter = ($page * $limit) - $limit + 1;
    if(isset($t_enquiry) && !empty($t_enquiry)){ 
    foreach($t_enquiry as $service){ // pr($service);
    ?>
                <tr>
                  <td>
					  <?php if($service['status']!='Y') { ?>
					  <input  type="checkbox" class='checkbox'  name="p_id[]" value=<?php echo $service['sno']; ?> />
					  
					   <? } ?> </td>
                  <td><?php echo $counter;?></td>
                   <td><?php if(isset($service['id'])){ echo $service['sno'];}else{ echo 'N/A'; } ?></td>
                        <td><?php if(isset($service['acedmicyear'])){ echo $service['acedmicyear'];}else{ echo 'N/A'; } ?></td>
                  <td><?php if(isset($service['fname'])){ echo ucfirst($service['fname']).'&nbsp;'.ucfirst($service['middlename']).'&nbsp;'.ucfirst($service['lname']);}else{ echo 'N/A'; } ?></td>
             <td><?php if(isset($service['f_phone'])){ echo ucfirst($service['f_phone']);}else{ echo 'N/A'; } ?></td>
                  <td><?php if(isset($service['m_phone'])){ echo ucfirst($service['m_phone']);}else{ echo 'N/A'; } ?></td>
                  <?php $cls=$this->Comman->showclasstitle($service['class_id']); //pr($cls); ?>
                  <td><?php if(isset($cls['title'])){ echo ucfirst($cls['title']);}else{ echo 'N/A'; } ?></td>
                  <?php $bls=$this->Comman->showboardtitle($service['enquire']['mode1_id']); //pr($cls); ?>

                  <td><?php if(isset($service['created'])){ echo date('d-M-Y',strtotime($service['created']));}else{ echo 'N/A'; } ?></td>
                    <td> <a title="Print Registration Receipt" id="s<?php echo $service['id']; ?>" target="_blank" href="<?php echo SITE_URL; ?>admin/Students/applicant_recipt/<?php echo $service['id']; ?>"><i class="fa fa-file-text-o"></i></a>&nbsp;
                   <?php if($service['status_c']=='Y'){ ?>
					   
					   <a title="Edit Registration" href="<? echo SITE_URL; ?>admin/students/applicant_edit/<?php echo $service['id']; ?>"> <img src="<? echo SITE_URL; ?>images/edit.png" style="width: 18px;"></a>&nbsp;
                 
							 
							 
                    <a style="font-size: 19px;" title="Cancel Registration"  href="<?php echo SITE_URL; ?>admin/students/applicant_status/<?php echo $service['id']; ?>/N"  onclick="javascript: return confirm('Are you sure do you want to deactive this registration');"><i class="fa fa-check"></i></a> 
                    
                    <?php }else{ ?>
						
						        <a style="font-size: 19px;" title="Activate Registration"  href="<?php echo SITE_URL; ?>admin/students/applicant_status/<?php echo $service['id']; ?>/Y"  onclick="javascript: return confirm('Are you sure do you want to active this registration');"><i class="fa fa-ban"></i></a> 
						<?php } ?>
						 </td>

                  <td style="<?php if($service['status_c']=='N'){ ?> color:red; <?php }elseif($service['status_i']=='Y'){ ?> color:green; <?php } else{ ?> color:red;<?php } ?>" ><?php if($service['status_c']=='N'){ echo "Canceled"; }else if($service['status_i']=='Y'){ echo "Invited";}else{ echo 'Pending'; } ?></td>
                  <td>
					         <?php $rolepresent = $this->request->session()->read('Auth.User.role_id'); 
                   if($service['status_c']=='Y' && $rolepresent!=15){ ?>
                  <?php 
			echo $this->Html->link('Admission ?', [
			    'controller' => 'Report','action' => 'approvedprospectus',
			   $service['enquire']['formno']	
			],['target'=>'_blank','class'=>'label label-success']);
			
			 }?></td>
                   
        
                  
                </tr>
    <?php $counter++;} }else{?>
    <tr>
    <td colspan="9" style="text-align:center;">NO Data Available</td>
    </tr>
    <?php } ?>  

 

         <script type='text/javascript'>

  // Changing state of CheckAll checkbox 
  $(".checkbox").click(function(){

    if($(".checkbox").length == $(".checkbox:checked").length) {
      $("#checkall").prop("checked", true);
    } else {
      $("#checkall").removeAttr("checked");
    }

  });

</script>      
      
               
    


  
                </tbody>
               
              </table> 
              </div>
               </div>  
              <?php echo $this->Form->end(); ?> 

              <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="<?php echo SITE_URL; ?>daterangepicker/daterangepicker.js"></script>
  <link rel="stylesheet" href="<?php echo SITE_URL; ?>daterangepicker/daterangepicker.css">
<script type="text/javascript">
       //Date range picker with time picker
    $('#reservationtime').daterangepicker({timePicker: true, timePickerIncrement: 30, locale: {
            format: 'MM/DD/YYYY h:mm A'
        }   });
</script>
