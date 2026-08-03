<div class="modal-header">
	<button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
	<h4 class="modal-title">
		            <i class="fa fa-plus-square"></i> Edit Personal Detail</h4>
		        
</div>





	<?php echo $this->Form->create($classes,array('url'=> array('controller'=>'employees','action'=>'edit'),'class'=>'form-horizontal','enctype' => 'multipart/form-data')); ?>

<div class="modal-body">
	<div class="row">
		<div class="col-sm-12">
		
 <div class="form-group">
    
    <div class="col-sm-4">
    <label for="inputEmail3" class="control-label">First Name<span style="color:red;">*</span></label>
    <?php echo $this->Form->input('fname',array('class'=>'form-control','placeholder'=>'First Name', 'id'=>'title','label' =>false)); ?>
    </div>
    
 <div class="col-sm-4">
 <label for="inputEmail3" class="control-label">Middle Name</label>
      <?php echo $this->Form->input('middlename',array('class'=>'form-control','placeholder'=>'Middle Name', 'id'=>'title','label' =>false)); ?>
    </div>
    
    
 <div class="col-sm-4">
 <label for="inputEmail3" class="control-label">Last Name<span style="color:red;">*</span></label>
      <?php echo $this->Form->input('lname',array('class'=>'form-control','placeholder'=>'Last Name', 'id'=>'title','label' =>false)); ?>
    </div>   
       
    
    
  </div>
  
  
  
  <div class="form-group">
    
    <div class="col-sm-4">
    <div><label for="inputEmail3" class="control-label">Gender</label></div>
      <label class="radio-inline">
  <input type="radio" name="gender" id="inlineRadio1" checked value="Male"> Male
</label>
<label  for="inputEmail3" class="radio-inline">
  <input type="radio" name="gender" id="inlineRadio2" value="Female"> Female
</label>
    </div>

     <script>

        $('#datepicksdd').datepicker({ 	beforeShow: function() {
        setTimeout(function(){
            $('.ui-datepicker').css('z-index', 99999999999999);
        }, 0);
    },
		"changeMonth":false, defaultDate: '<?php echo date('m/d/Y',strtotime($classes['dob']));  ?>', maxDate:'<?php echo date('m/d/Y',strtotime($classes['dob']));  ?>',"yearRange":"1980:2018","changeYear":false,"autoSize":true});
   </script> 
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
     <div class="col-sm-4">
    <label for="inputEmail3" class="control-label">Date of Birth<span style="color:red;">*</span></label>
    <div class="input-group">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <?php echo $this->Form->input('dob',array('class'=>'form-control','placeholder'=>'Date Of Birth', 'id'=>'datepicksdd','label' =>false)); ?>
                </div>
    </div>
 <div class="col-sm-4">
 <label for="inputEmail3" class="control-label">Mobile No<span style="color:red;">*</span></label>
 
 <script>
 $('#mobile').on('change',function(){
var mobile = $('#mobile').val();
var e_id=<? echo $classes['id'] ?>;
 $.ajax({ 
        type: 'POST', 
        url: '<?php echo ADMIN_URL ;?>employees/edit_dup_mobile',
        data: {'mobile':mobile,'e_id':e_id}, 
        success: function(data){  
  if(data == 1)
{
 $('#mobile').html(data);
}  
  else
  {
 $.ajax({ 
        type: 'POST', 
        url: '<?php echo ADMIN_URL ;?>employees/dup_mobile',
        data: {'mobile':mobile}, 
        success: function(data){  
if(data > 0 )
{
 $('#mobile_exits').val('');
 $('#mobile_exits').show('');
}

        }, 
        
    }); 
  }
        }, 
        
    });  
});

</script>

     <?php echo $this->Form->input('mobile',array('class'=>'form-control','maxlength'=>'10','placeholder'=>'Mobile No.', 'id'=>'mobile','onkeypress'=>'return isNumber();','label' =>false)); ?>
            
                <!-- /.input group -->     
  </div>
  <span style="color:red;display:none;" id="mobile_exits">Mobile Already Exits </span>
<div  id="erp" style="display:none;color:red;margin-left: 415px;">Enter Only Number</div>
</div>

  <div class="form-group">
 
 <div class="col-sm-4">
 <label for="inputEmail3" class="control-label">Maritial Status</label>
 <?php $options= array('married'=>"Married",'unmarried'=>"Unmarried") ?>
      <?php echo $this->Form->input('martial_status',array('class'=>'form-control','empty'=>'Maritial Status','type'=>'select','options'=>$options, 'id'=>'title','label' =>false)); ?>
    </div>   
 
 <div class="col-sm-4">
 <label for="inputEmail3" class="control-label">Nationality</label>
    <select class="form-control " name="nationality" style="width: 100%;">
                 
                  <option  value="INDIAN" <?php  if ($classes['nationality']=='INDIAN') { ?> selected="selected" <?php } ?>>INDIAN</option>
              
                  <option value="OTHERS" <?php  if ($classes['nationality']=='OTHERS') { ?> selected="selected" <?php } ?>>OTHERS</option>
                 

                </select>
    </div>   
       
    
    
  </div>
  <div class="form-group">
    
    <div class="col-sm-4">
    <label for="inputEmail3" class="control-label">Father/Husband Name</span></label>
                  <?php echo $this->Form->input('f_h_name',array('class'=>'form-control','placeholder'=>'Father/Husband Name','label' =>false)); ?>
                </div>
    </div>
   
  
  
  <h4><i class="fa fa-info-circle" aria-hidden="true"></i>
Academic Details</h4>
<div class="form-group">

    <div class="col-sm-4">	
 <label for="inputEmail3" class="control-label">Select Department<span style="color:red;">*</span></label>
    <?php echo $this->Form->input('department_id',array('class'=>'form-control','required'=>true,'type'=>'select','empty'=>'Select Department','options'=>$Departments,'label' =>false)); ?> 
    </div>
    
             <div class="col-sm-4">
 <label for="inputEmail3" class="control-label">Select Experience<span style="color:red;">*</span></label>
 <select class="form-control" name="experience">
<option value="0">Fresher</option>
 <?php for($i=1;$i<=10;$i++) { ?>
    <option value="<?php echo $i; ?>"><?php echo $i."$nbsp year"; ?></option>
    <?php } ?>
    <option value="10+">10+</option>
    </select>
    </div>

           <div class="col-sm-4">
    <label for="inputEmail3" class="control-label">Joining Date<span style="color:red;">*</span></label>
    <div class="input-group">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <?php echo $this->Form->input('joiningdate',array('class'=>'form-control','placeholder'=>'Joining Date', 'id'=>'joindate','label' =>false)); ?>
         
   
   </div> </div></div>
<div class="form-group">
    

     <div class="col-sm-4">
 <label >Designation</label>
    <?php echo $this->Form->input('designation_id',array('class'=>'form-control','required'=>true,'type'=>'select','empty'=>'Select Designation','options'=>$Designations,'label' =>false)); ?> 
    </div>
 

  </div>  

<hr>
            <div class="form-group">

<div class="col-sm-4">
  <label for="inputEmail3" class="control-label">Employee code<span
      style="color:red;">*</span></label>
  <?php echo $this->Form->input('emp_code', array('class' => 'form-control', 'placeholder' => 'Employee code', 'label' => false)); ?>
</div>



<div class="col-sm-4">
  <label for="inputEmail3" class="control-label">Alternate number<span
      style="color:red;">*</span></label>
  <?php echo $this->Form->input('alternate_mobile', array('class' => 'form-control','maxlength'=>'10','onkeypress'=>'return isNumber();', 'placeholder' => 'Alternate number', 'label' => false)); ?>
</div>

<div class="col-sm-4">
  <label for="inputEmail3" class="control-label">Qualification<span
      style="color:red;">*</span></label>
  <?php echo $this->Form->input('qualification', array('class' => 'form-control', 'placeholder' => 'Enter Qualification', 'label' => false)); ?>
</div>


</div>
   </div> 
    </div>
     </div>
  </div>
 </div>
</div><!--./modal-body-->
<div class="modal-footer">
	<button data-dismiss="modal" class="btn btn-default pull-right" type="button">Close</button>
		<?php
				if(isset($classes['id'])){
				echo $this->Form->submit(
				    'Update', 
				    array('class' => 'btn btn-info pull-left','style'=>'', 'title' => 'Update')
				); }else{ 
				echo $this->Form->submit(
				    'Add', 
				    array('class' => 'btn btn-info pull-left','style'=>'', 'title' => 'Add')
				);
				}
		       ?>   
	
</div><!--./modal-footer-->
</form>
<script>

function isNumber(evt) {
evt = (evt) ? evt : window.event;
var charCode = (evt.which) ? evt.which : evt.keyCode;
if (charCode != 46 && charCode != 45 && charCode > 31 && (charCode < 48 || charCode > 57)) {

	$('#erp').show();
return false;
}
$('#erp').hide();
return true;
}

</script>
<script>
    function fileValidation() {
      var fileInput =
        document.getElementById('file');
      var filePath = fileInput.value;
      // Allowing file type
      var allowedExtensions =
          /(\.jpg|\.jpeg|\.png|\.gif)$/i;
      
      if (!allowedExtensions.exec(filePath)) {
        $('#image_ext').show('');
        fileInput.value = '';
      }else{
        $('#image_ext').hide('');
      }
      
        }
  </script>