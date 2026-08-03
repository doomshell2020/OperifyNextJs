 <!-- Content Wrapper. Contains page content -->
  <style>
  
  .checkbox input[type="checkbox"]{
    opacity:1;
}
  
  </style>
  
  
  
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
   Employee Info Report 
      </h1>
           <ol class="breadcrumb">
          		<li><a href="<?php echo ADMIN_URL;?>dashboards"><i class="fa fa-home"></i>Home</a></li>
<li><a href="<?php echo ADMIN_URL;?>report/employee">Manage Employee Report </a></li>  
   </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          
	<div class="box">
    <div class="box-header">
      <div class="modal" id="globalModal" tabindex="-1" role="dialog" aria-labelledby="esModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="loader">
                                <div class="es-spinner">
                                    <i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>   
        
              <script inline="1">
$(document).ready(function(){
$("#YourbuttonId").click(function(){
    if($('input[type=checkbox]:checked').length == 0)
    {
        alert('Please select atleast one checkbox');
    }
});	
});		  
				  
//<![CDATA[
$(document).ready(function () {
  $("#Employeeexl").bind("submit", function (event) {
    $.ajax({
      async:true,
      data:$("#Employeeexl").serialize(),
      dataType:"html", 
      type:"POST", 
     url:"<?php echo ADMIN_URL ;?>report/search4",
      success:function (data) {
		//  alert(data); 
		  
	//	$("#updt").show();   
        $("#updt").html(data); }, 
  });
    return false;
});});
//]]>
</script>

  <?php echo $this->Form->create('Employee',array('type'=>'file','inputDefaults'=>array('div'=>false,'label'=>false),'id'=>'Employeeexl','class'=>'form-horizontal')); ?>

 <div class="form-group">
   <div class="col-sm-4" >
		  <label for="inputEmail3" class="control-label">Select Department</label>
	    	<?php echo $this->Form->input('department_id',array('class'=>'form-control','id'=>'subjt','type'=>'select','empty'=>'Select Department','options'=>$department_name,'label' =>false)); ?>  
		          </div> 
    
     <div class="col-sm-4" >
		  <label for="inputEmail3" class="control-label">Select Designation</label>
	    	<?php echo $this->Form->input('desination_id',array('class'=>'form-control','id'=>'subjt','type'=>'select','empty'=>'Select Designation','options'=>$Designations,'label' =>false)); ?>  
		          </div> 
		          
	   <div class="col-sm-4" >
		     <label for="inputEmail3" class="control-label">Gender</label>
		<?php $gender=array('male'=>"Male",'female'=>"Female") ?>     
		<?php echo $this->Form->input('gender',array('class'=>'form-control','id'=>'subjt','type'=>'select','empty'=>'Select Gender','options'=>$gender,'label' =>false)); ?>  
		          </div> 	 
		   </div>  
		   
     <div class="form-group">
	<script>
$(document).ready(function(){	
		
//select all checkboxes
$("#select_all").change(function(){ 
	 //"select all" change 
    $(".checkbox input[type='checkbox']").prop('checked', $(this).prop("checked")); //change all ".checkbox" checked status
});

 $(".checkbox input[type='checkbox']").change(function(){ 
    //uncheck "select all", if one of the listed checkbox item is unchecked
    if(false == $(this).prop("checked")){ //if this item is unchecked
        $("#select_all").prop('checked', false); //change "select all" checked status to false
    }
    //check "select all" if all checkbox items are checked
    if ($('.checkbox:checked').length == $('.checkbox').length ){
        $("#select_all").prop('checked', true);
    }
});


});
</script>	 
		 
		 
	<div style="padding: 5px 5px 5px 19px;">
    <legend>
<label><input type="checkbox"  name="checkAll" id="select_all" value="1"> Select All</label>	</legend>
</div>
</div>
	 <div class="form-group">	 
<div class="col-sm-2">
	<label>Personal Information</label>	<div>
<div class="checkbox"><label><input type="checkbox" name="selectField[]" value="employees.id"> Employee Id</label></div>
<div class="checkbox"><label><input type="checkbox" name="selectField[]" value="fname"> First Name</label></div>
<div class="checkbox"><label><input type="checkbox" name="selectField[]" value="middlename"> Middle Name</label></div>
<div class="checkbox"><label><input type="checkbox" name="selectField[]" value="lname"> Last Name</label></div>
<div class="checkbox"><label><input type="checkbox" name="selectField[]" value="employees.email"> Email/Login Id</label></div>
<div class="checkbox"><label><input type="checkbox" name="selectField[]" value="martial_status"> Marital Status</label></div>
<div class="checkbox"><label><input type="checkbox" name="selectField[]" value="gender"> Gender</label></div>
<div class="checkbox"><label><input type="checkbox" name="selectField[]" value="dob"> Date of Birth</label></div>
<div class="checkbox"><label><input type="checkbox" name="selectField[]" value="mobile"> Mobile No</label></div>
<div class="checkbox"><label><input type="checkbox" name="selectField[]" value="hobbies"> Hobbies</label></div>

</div></div>
<div class="col-sm-2">
	<label>Other Information</label>	<div>
<div class="checkbox"><label><input type="checkbox" name="selectField[]" value="aadharno"> Attendance Card ID</label></div>
<div class="checkbox"><label><input type="checkbox" name="selectField[]" value="department_id"> Department</label></div>
<div class="checkbox"><label><input type="checkbox" name="selectField[]" value="designation_id"> Designation</label></div>
<div class="checkbox"><label><input type="checkbox" name="selectField[]" value="nationality"> Nationality</label></div>
<div class="checkbox"><label><input type="checkbox" name="selectField[]" value="joiningdate"> Joining Date</label></div>
<div class="checkbox"><label><input type="checkbox" name="selectField[]" value="otherinfos.qualifications"> Qualification</label></div>
<div class="checkbox"><label><input type="checkbox" name="selectField[]" value="specialization"> Specialization</label></div>
<div class="checkbox"><label><input type="checkbox" name="selectField[]" value="reference"> Reference</label></div>
<div class="checkbox"><label><input type="checkbox" name="selectField[]" value="accountno"> Bank Account No</label></div></div></div>
<div class="col-sm-2">
	<label>Current Address</label>	<div>
<div class="checkbox"><label><input type="checkbox" name="selectField[]" value="c_address"> Address</label></div>
<div class="checkbox"><label><input type="checkbox" name="selectField[]" value="c_city_id"> City</label></div>
<div class="checkbox"><label><input type="checkbox" name="selectField[]" value="c_s_id"> State</label></div>
<div class="checkbox"><label><input type="checkbox" name="selectField[]" value="c_c_id"> Country</label></div>
<div class="checkbox"><label><input type="checkbox" name="selectField[]" value="c_pincode"> Pincode</label></div>
</div></div>
<div class="col-sm-2">
	<label>Permanent Address</label>	<div>
<div class="checkbox"><label><input type="checkbox" name="selectField[]" value="p_address"> Address</label></div>
<div class="checkbox"><label><input type="checkbox" name="selectField[]" value="p_city_id"> City</label></div>
<div class="checkbox"><label><input type="checkbox" name="selectField[]" value="p_s_id"> State</label></div>
<div class="checkbox"><label><input type="checkbox" name="selectField[]" value="p_c_id"> Country</label></div>
<div class="checkbox"><label><input type="checkbox" name="selectField[]" value="p_pincode"> Pincode</label></div>
<!--<div class="checkbox"><label><input type="checkbox" name="selectField[]" value="stu_padd_house_no"> House No</label></div>-->
</div></div>
		<div class="col-sm-2">
	<label>Guardian Information</label>	<div>
<div class="checkbox"><label><input type="checkbox" name="selectField[]" value="fullname"> Full Name</label></div>
<div class="checkbox"><label><input type="checkbox" name="selectField[]" value="relation"> Relation</label></div>
<div class="checkbox"><label><input type="checkbox" name="selectField[]" value="guardians.qualification"> Qualification</label></div>
<div class="checkbox"><label><input type="checkbox" name="selectField[]" value="occupation"> Occupation</label></div>
<!--<div class="checkbox"><label><input type="checkbox" name="selectField[]" value="secd_batch"> Batch</label></div> -->
<div class="checkbox"><label><input type="checkbox" name="selectField[]" value="mobileno"> Mobile No</label></div>
<div class="checkbox"><label><input type="checkbox" name="selectField[]" value="guardians.emails"> Email Id</label></div>
</div></div></div>
    <div class="form-group"> 
   <input type="submit" style="background-color:#00c0ef;width:100px !important; margin-left: 10px;" id="YourbuttonId" class="btn btn4 btn_pdf myscl-btn date" value="Submit"  onclick="return testcheck()">  
   <button type="reset" style="background-color:#f4f4f4;width:100px !important; margin-left: 10px;" class="btn btn4 btn_pdf myscl-btn date">Reset</button>  
   <?php
echo $this->Form->end();
?>          
            <!-- /.box-header -->
<?php echo $this->Flash->render(); ?>
 
		      
		</div>
		</div>
		<div id="updt">   
</div>

