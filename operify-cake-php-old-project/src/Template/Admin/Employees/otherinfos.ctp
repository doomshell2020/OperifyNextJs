<div class="modal-header">
	<button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
	<h4 class="modal-title">
		            <i class="fa fa-plus-square"></i>Other Info</h4>
		        
</div>





	<?php echo $this->Form->create($classes,array('url'=> array('controller'=>'employees','action'=>'otherinfos'),'class'=>'form-horizontal','enctype' => 'multipart/form-data')); ?>

<div class="modal-body">
	<div class="row">
		<div class="col-sm-12">
		
 <div class="form-group">
    
    <div class="col-sm-4">
    <label for="inputEmail3" class="control-label">Attendance Card ID	<span style="color:red;">*</span></label>
    <?php echo $this->Form->input('aadharno',array('class'=>'form-control','maxlength'=>20,'required'=>true,'placeholder'=>'Attendance Card ID', 'id'=>'title','label' =>false)); ?>
    </div>
    
 <div class="col-sm-4">
 <label for="inputEmail3" class="control-label">Bank Account No<span style="color:red;">*</span></label>
      <?php echo $this->Form->input('accountno',array('class'=>'form-control','maxlength'=>20,'placeholder'=>'Bank Account No','required'=>true, 'id'=>'title','label' =>false)); ?>
    </div>
    
    
 <div class="col-sm-4">
 <label for="inputEmail3" class="control-label">Reference<span style="color:red;">*</span></label>
      <?php echo $this->Form->input('reference',array('class'=>'form-control','maxlength'=>25,'placeholder'=>'Reference', 'id'=>'title','label' =>false)); ?>
    </div>   
       
    
    
  </div>
  
  

  <div class="form-group">
 
 <div class="col-sm-4">
 <label for="inputEmail3" class="control-label">Specialization<span style="color:red;">*</span></label>
   <?php echo $this->Form->input('specialization',array('class'=>'form-control','maxlength'=>40,'placeholder'=>'specialization', 'id'=>'title','label' =>false)); ?>
    </div>   
 
 <div class="col-sm-4">
	 <input type="hidden" id="" name="user_id" value="<?php echo $ids; ?>">
 <label for="inputEmail3" class="control-label">Hobbies</label>
   <?php echo $this->Form->input('hobbies',array('class'=>'form-control','maxlength'=>50,'placeholder'=>'Hobbies', 'id'=>'title','label' =>false)); ?>
    </div>  
     <div class="col-sm-4">
 <label for="inputEmail3" class="control-label">Qualification</label>
   <?php echo $this->Form->input('qualifications',array('class'=>'form-control','maxlength'=>50,'placeholder'=>'Hobbies', 'id'=>'title','label' =>false)); ?>
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
				    array('class' => 'btn btn-info pull-left','style'=>'margin-right: 10px;', 'title' => 'Update')
				); }else{ 
				echo $this->Form->submit(
				    'Add', 
				    array('class' => 'btn btn-info pull-left','style'=>'margin-right: 10px;', 'title' => 'Add')
				);
				}
		       ?>   
	
</div><!--./modal-footer-->
</form>
<script>
 //  alert("hi");
       $('#datepicksd').datepicker({"changeMonth":true,'maxDate':'0',"yearRange":"1980:2018","changeYear":true,"autoSize":true,"autoclose":true,"dateFormat":"dd-mm-yy","todayHighlight":'TRUE'});
        
        $('#datepicksd').datepicker().on('changeDate', function(ev)
{                 
    $('.datepicker').hide();
});
   </script> 
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
