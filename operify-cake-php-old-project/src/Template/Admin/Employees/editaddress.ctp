
<div class="modal-header">
	<button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
	<h4 class="modal-title" style="
    margin-top: -2px !important;
    margin-bottom: -7px !important;
">
		            <i class="fa fa-plus-square"></i> Edit Personal Detail</h4>
		        
</div>
<script>


    var ckbox = $('#checkboxsd');

    $(ckbox).on('click',function () {
        if (ckbox.is(':checked')) {
           
            var caddress= $('#c-address').val();
              $('#p-address').val(caddress);
                    var caddress= $('#c-c-id').val();
              $('#p-c-id').val(caddress);
              
                  var caddress= $('#c-s-id').val();
              $('#p-s-id').val(caddress);
                  var caddress= $('#c-city-id').val();
              $('#p-city-id').val(caddress);
              var caddress= $('#c-pincode').val();
              $('#p-pincode').val(caddress);
              
        } else {
  
              $('#p-address').val('');
                   
              $('#p-c-id').val('');
              
                
              $('#p-s-id').val('');
           
              $('#p-city-id').val('');
             
              $('#p-pincode').val('');
        }
    });

</script>
<script>




$('#c-c-id').on('change',function(){

var id = $('#c-c-id').val();

 $.ajax({ 
        type: 'POST', 
        url: '<?php echo SITE_URL ;?>admin/cities/find_state',
        data: {'id':id}, 
        success: function(data){  

 $('#c-s-id').empty();
 $('#c-city-id').empty();

  $('#c-s-id').html(data);
        }, 
        
    });  
});

$('#c-s-id').on('change',function(){
	//alert();
var id = $('#c-s-id').val();

 $.ajax({ 
        type: 'POST', 
        url: '<?php echo SITE_URL ;?>admin/cities/find_cities',
        data: {'id':id}, 
        success: function(data){  
//alert(data);
 $('#c-city-id').empty();
  $('#c-city-id').html(data);
        }, 
        
    });  
});







$('#p-c-id').on('change',function(){
	//alert();
var id = $('#p-c-id').val();

 $.ajax({ 
        type: 'POST', 
        url: '<?php echo SITE_URL ;?>admin/cities/find_state',
        data: {'id':id}, 
        success: function(data){  
//alert(data);
 $('#p-s-id').empty();
 $('#p_city_id').empty();
  $('#p-s-id').html(data);
        }, 
        
    });  
    
    
    
    
});

$('#p-s-id').on('change',function(){
	//alert();
var id = $('#p-s-id').val();

 $.ajax({ 
        type: 'POST', 
        url: '<?php echo SITE_URL ;?>admin/cities/find_cities',
        data: {'id':id}, 
        success: function(data){  
//alert(data);
 $('#p-city-id').empty();
  $('#p-city-id').html(data);
  
        }, 
        
    });  
    
   
});


</script>

<?php echo $this->Form->create($address, array(
                       
                       'class'=>'',
			'id' => 'sevice_form',
                       'enctype' => 'multipart/form-data',
                       'validate'
                     	)); ?>


	
<div class="modal-body" style="max-height:600px">
	
	<?php echo $this->Flash->render(); ?>


	<div class="row">
	
		<div class="col-sm-12">		<h5>
		 Current Address	</h5>
			<div class="form-group field-empaddress-emp_cadd">
<label class="control-label" for="empaddress-emp_cadd">Address</label>
<?php echo $this->Form->input('c_address',array('class'=>'form-control','type'=>'text','required'=>'required','label' =>false,'placeholder'=>'Enter Address')); ?>  



</div>		</div>
	</div>
	<div class="row">
		<div class="col-sm-6">
			<div class="form-group field-empaddress-emp_cadd_country">
<label class="control-label" for="empaddress-emp_cadd_country">Country</label>
<?php echo $this->Form->input('c_c_id',array('class'=>'form-control country','type'=>'select','empty'=>'Select Country','required'=>'required','options'=>$country,'value'=>'5','label' =>false)); ?>  



</div>		</div>
		<div class="col-sm-6">
			 <div class="form-group field-empaddress-emp_cadd_state">
<label class="control-label" for="empaddress-emp_cadd_state">State/Province</label>
<?php echo $this->Form->input('c_s_id',array('class'=>'form-control','type'=>'select','empty'=>'Select State','required'=>'required','options'=>$states,'label' =>false)); ?>  



</div>		</div>
	</div>
	<div class="row">
		<div class="col-sm-6">
			<div class="form-group field-empaddress-emp_cadd_city">
<label class="control-label" for="empaddress-emp_cadd_city">City/Town</label>
<?php echo $this->Form->input('c_city_id',array('class'=>'form-control','type'=>'select','empty'=>'Select State','required'=>'required','options'=>$cities,'label' =>false)); ?>  

</div>
		</div>
		<div class="col-sm-6">
			<div class="form-group field-empaddress-emp_cadd_pincode">
<label class="control-label" for="empaddress-emp_cadd_pincode">Pincode</label>
<?php echo $this->Form->input('c_pincode',array('class'=>'form-control','type'=>'text','required'=>'required','label' =>false,'maxlength'=>'6')); ?>  


</div>		</div>
	</div>
	

	<!--Start permanent address block-->

Same as Current Address <input id="checkboxsd" type="checkbox" />
<div class="row">
			
		<div class="col-sm-12">
			<h5>
		 Permanent Address	</h5>
			<div class="form-group field-empaddress-emp_cadd">
<label class="control-label" for="empaddress-emp_cadd">Address</label>
<?php echo $this->Form->input('p_address',array('class'=>'form-control','type'=>'text','required'=>'required','label' =>false,'placeholder'=>'Enter Address')); ?>  



</div>		</div>
	</div>
<div class="row">
		<div class="col-sm-6">
			<div class="form-group field-empaddress-emp_cadd_country">
<label class="control-label" for="empaddress-emp_cadd_country">Country</label>
<?php echo $this->Form->input('p_c_id',array('class'=>'form-control','type'=>'select','empty'=>'Select Country','required'=>'required','options'=>$country,'value'=>'5','label' =>false)); ?>  



</div>		</div>
		<div class="col-sm-6">
			 <div class="form-group field-empaddress-emp_cadd_state">
<label class="control-label" for="empaddress-emp_cadd_state">State/Province</label>
<?php echo $this->Form->input('p_s_id',array('class'=>'form-control','type'=>'select','empty'=>'Select State','required'=>'required','options'=>$states,'label' =>false)); ?>  



</div>		</div>
	</div>
	<div class="row">
		<div class="col-sm-6">
			<div class="form-group field-empaddress-emp_cadd_city">
<label class="control-label" for="empaddress-emp_cadd_city">City/Town</label>
<input type="hidden" id="" name="user_id" value="<?php echo $ids; ?>">
<?php echo $this->Form->input('p_city_id',array('class'=>'form-control','type'=>'select','empty'=>'Select State','required'=>'required','options'=>$cities,'label' =>false)); ?>  

</div>
		</div>
		<div class="col-sm-6">
			<div class="form-group field-empaddress-emp_cadd_pincode">
<label class="control-label" for="empaddress-emp_cadd_pincode">Pincode</label>
<?php echo $this->Form->input('p_pincode',array('class'=>'form-control','type'=>'text','required'=>'required','label' =>false)); ?>  


</div>		</div>
	</div>

<!--./modal-body-->
<div class="modal-footer">
	<button type="submit" class="btn btn-info pull-left"><i class="fa fa-upload"></i> Update</button>	<button data-dismiss="modal" class="btn btn-default pull-right" type="button">Close</button>
</div><!--./modal-footer-->
</form>




