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

       Student Info Report
      </h1>
      <ol class="breadcrumb">
          		<li><a href="<?php echo ADMIN_URL;?>report/student"><i class="fa fa-home"></i>Home</a></li>
<li><a href="<?php echo ADMIN_URL;?>report/student">Manage Student Report </a></li>
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
          <style>

         #load2{
    width:100%;
    height:100%;
    position:fixed;
    z-index:9999;
    top: 42%;
    background-color: white  !important;
    background:url("<? echo SITE_URL; ?>images/Preloader_2.gif") no-repeat center center rgba(0,0,0,0.75)
}

</style>
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
  $("#Studentexl").bind("submit", function (event) {
    $.ajax({
      async:true,
      data:$("#Studentexl").serialize(),
      dataType:"html",
      type:"POST",
        beforeSend: function() {
        // setting a timeout
         $('#load2').css("display", "block");
    },
     url:"<?php echo ADMIN_URL ;?>report/search3",
      success:function (data) {
		//  alert(data);

	//	$("#updt").show();
        $("#updt").html(data); },
        complete: function() {
           $('#load2').css("display", "none");
    },
  });
    return false;
});});
//]]>
</script>

  <?php echo $this->Form->create('Student',array('type'=>'file','inputDefaults'=>array('div'=>false,'label'=>false),'id'=>'Studentexl','class'=>'form-horizontal')); ?>

 <div class="form-group studnt_inforpt_group">


     <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6" >
		  <label for="inputEmail3" class="control-label">Select Class</label>
	    	<?php echo $this->Form->input('class_id',array('class'=>'form-control','id'=>'subjt','type'=>'select','multiple'=>'multiple','empty'=>'Select Class','options'=>$classes,'label' =>false)); ?>
		          </div>
		            <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6">
		  <label for="inputEmail3" class="control-label">Select Section</label>
	    	<?php echo $this->Form->input('section_id',array('class'=>'form-control','id'=>'subjt','type'=>'select','multiple'=>'multiple','empty'=>'Select Section','options'=>$sections,'label' =>false)); ?>
		          </div>
		           <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6">
		  <label for="inputEmail3" class="control-label">Select House</label>
	    	<?php echo $this->Form->input('h_id',array('class'=>'form-control','id'=>'st','type'=>'select','multiple'=>'multiple','empty'=>'Select House','options'=>$houses,'label' =>false)); ?>
		          </div>
		            <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6">

		  <label for="inputEmail3" class="control-label">Select Discount</label>
	    	<div class="input select">
				<select name="discountcategory[]" multiple="multiple" class="form-control" id="susbjt">
					<option value="">Select Discount</option>
				  <?php foreach($discountcate as $key=>$value){  ?>
				<option value="<? echo $value; ?>"><? echo ucwords(strtolower($value)); ?></option>

				<? } ?>
				</select></div>

		          </div>
	   <div class="col-lg-4 col-md-3 col-sm-4 col-xs-6" >
		     <label for="inputEmail3" class="control-label">Pupil's First/ Last Name/ Email/ Mobile No.</label>
	<?php echo $this->Form->input('name',array('class'=>'form-control','placeholder'=>'First/ Last Name/ Email/ Mobile No.','id'=>'endate','label' =>false)); ?>
		          </div>

		    <div class="col-lg-4 col-md-3 col-sm-4 col-xs-6" >
		     <label for="inputEmail3" class="control-label">Student Scholar No. </label>
	<?php echo $this->Form->input('ids',array('class'=>'form-control','placeholder'=>'Scholar No.','id'=>'edte','label' =>false)); ?>
		          </div>
		            <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6" >
		          	  			   	   <script>
    $(document).ready(function(){
        $('#fdatefrom').datepicker({
  dateFormat: 'yy-mm-dd',
        onSelect: function(date){

        var selectedDate = new Date(date);
        var endDate = new Date(selectedDate);
         endDate.setDate(endDate.getDate());

        $("#fendfrom").datepicker( "option", "minDate", endDate );
        $("#fendfrom").val(date);
    }

    });


//$('#fdatefrom').datepicker('setDate', 'today');

	$('#fendfrom').datepicker({
	dateFormat: 'yy-mm-dd'});
	    //$('#fendfrom').datepicker('setDate', 'today');
			 });
         </script>

		  <label for="inputEmail3" class="control-label">Admission Date From</label>
	    	<?php echo $this->Form->input('datefrom',array('class'=>'form-control','id'=>'fdatefrom','readonly','placeholder'=>'Date From','label' =>false)); ?>
		          </div>
		            <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6" >
		 		  <label for="inputEmail3" class="control-label">Admission Date To</label>
	    	<?php echo $this->Form->input('dateto',array('class'=>'form-control','id'=>'fendfrom','readonly','placeholder'=>'Date To','label' =>false)); ?>
		          </div>
		           <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6">
 <label>Academic Year</label>
   <select class="form-control" name="acedmicyear" >
  <option value=""> Year</option>
  <?php foreach($acd as $kty=>$rtt){  ?>
<option <?php if($rolepresentyear==$rtt) { ?> selected="selected" <? } ?>  value="<?php echo $rtt; ?>"><?php echo $rtt; ?></option>
  <?php } ?>

    </select>
    </div>

     <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6">
 <label>Admission Year</label>


   <select class="form-control" name="admissionyear">
  <option selected="selected" value=""> Year</option>
  <?php foreach($acd as $kty=>$rtt){  ?>
<option value="<?php echo $rtt; ?>"><?php echo $rtt; ?></option>
  <?php } ?>
    </select>
    </div>
    <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6">
 <label>Is Special Student</label>
   <select class="form-control" name="is_special">
  <option value=""> Select</option>

<option value="Y">Yes</option>
<option value="N">No</option>
    </select>
    </div>

    <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6">
 <label>Is RTE Student</label>
   <select class="form-control" name="category">
  <option value=""> Select</option>

<option value="RTE">Yes</option>


    </select>
    </div>
    </div>

     <div>
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


	<div style="padding: 5px 5px 5px 19px;" class="pl-xs-0">
    <legend>
<label><input type="checkbox"  name="checkAll" checked="checked" id="select_all" value="1"> Select All</label>	</legend>
</div>
</div>
	 <div class="form-group student_infopgselect_aldv">
<div class="col-sm-12">
	<label>Personal Information</label>
	<div>
<div class="checkbox"><label><input type="checkbox" checked="checked" name="selectField[]" value="s.enroll"> &nbsp;Scholar No.</label></div>

<div class="checkbox"><label><input type="checkbox" checked="checked" name="selectField[]" value="s.fname"> &nbsp; Name</label></div>
<div class="checkbox"><label><input type="checkbox" checked="checked" name="selectField[]" value="s.username"> &nbsp; Email</label></div>

<div class="checkbox"><label><input type="checkbox" checked="checked" name="selectField[]" value="s.gender"> &nbsp;Gender</label></div>
<div class="checkbox"><label><input type="checkbox" checked="checked" name="selectField[]" value="s.dob"> &nbsp;Date of Birth</label></div>

<div class="checkbox"><label><input type="checkbox" checked="checked" name="selectField[]" value="s.height"> &nbsp;Height</label></div>

<div class="checkbox"><label><input type="checkbox" checked="checked" name="selectField[]" value="s.weight"> &nbsp;Weight</label></div>

</div></div>

<div class="col-sm-12">
	<label>Additional Information</label>
	<div>
<div class="checkbox"><label><input type="checkbox" checked="checked" name="selectField[]" value="s.adaharnumber"> &nbsp;Adaharnumber</label></div>
<div class="checkbox"><label><input type="checkbox" checked="checked" name="selectField[]" value="s.cast"> &nbsp;Cast</label></div>
<div class="checkbox"><label><input type="checkbox" checked="checked" name="selectField[]" value="s.religion"> &nbsp;Religion</label></div>
<div class="checkbox"><label><input type="checkbox" checked="checked" name="selectField[]" value="s.category"> &nbsp;Category</label></div>
<div class="checkbox"><label><input type="checkbox" checked="checked" name="selectField[]" value="s.bloodgroup"> &nbsp;Bloodgroup</label></div>
<div class="checkbox"><label><input type="checkbox" checked="checked" name="selectField[]" value="s.disability"> &nbsp;Disability</label></div>
<div class="checkbox"><label><input type="checkbox" checked="checked" name="selectField[]" value="s.mother_tounge"> &nbsp;Mother tounge</label></div>
<div class="checkbox"><label><input type="checkbox" checked="checked" name="selectField[]" value="s.address"> &nbsp;Address</label></div>
<div class="checkbox"><label><input type="checkbox" checked="checked" name="selectField[]" value="s.rf_id"> &nbsp;RF ID</label></div>
<div class="checkbox"><label><input type="checkbox" checked="checked" name="selectField[]" value="s.rfidhexcode"> &nbsp;RF ID HEX CODE</label></div>

</div></div>
<div class="col-sm-12">
	<label>Other Information</label>
	<div>


<div class="checkbox"><label><input type="checkbox" checked="checked" name="selectField[]" value="s.mobile"> &nbsp;Mobile No</label></div>
<div class="checkbox"><label><input type="checkbox" checked="checked" name="selectField[]" value="s.sms_mobile"> &nbsp;SMS Mobile No</label></div>

<div class="checkbox"><label><input type="checkbox" checked="checked" name="selectField[]" value="s.f_phone"> &nbsp;Father Phone </label></div>
<div class="checkbox"><label><input type="checkbox" checked="checked" name="selectField[]" value="s.m_phone"> &nbsp;Mother Phone </label></div>
</div></div>


<div class="col-sm-12">
	<label>Academic Information</label>	<div>

<div class="checkbox"><label><input type="checkbox" checked="checked" name="selectField[]" value="s.admissionyear"> &nbsp;Admission Year</label></div>
<div class="checkbox"><label><input type="checkbox" checked="checked" name="selectField[]" value="s.acedmicyear"> &nbsp;Academic Year</label></div>
<div class="checkbox"><label><input type="checkbox" checked="checked" name="selectField[]" value="s.created"> &nbsp;Admission Date</label></div>
<div class="checkbox"><label><input type="checkbox" checked="checked" name="selectField[]" value="s.formno"> &nbsp;Form No.</label></div>
<div class="checkbox"><label><input type="checkbox" checked="checked" name="selectField[]" value="s.board_id"> &nbsp;Board</label></div>

<div class="checkbox"><label><input type="checkbox" checked="checked" name="selectField[]" value="s.admissionclass"> &nbsp;Admission Class</label></div>
<div class="checkbox"><label><input type="checkbox" checked="checked" name="selectField[]" value="s.class_id"> &nbsp;Current Class</label></div>
<!--<div class="checkbox"><label><input type="checkbox" name="selectField[]" value="secd_batch"> Batch</label></div> -->
<div class="checkbox"><label><input type="checkbox" checked="checked" name="selectField[]" value="s.section_id"> &nbsp;Section</label></div>
<div class="checkbox"><label><input type="checkbox" checked="checked" name="selectField[]" value="s.h_id"> &nbsp;House</label></div>
<div class="checkbox"><label><input type="checkbox" checked="checked" name="selectField[]" value="s.discountcategory"> &nbsp;Discount</label></div>
<div class="checkbox"><label><input type="checkbox" checked="checked" name="selectField[]" value="s.is_lc"> &nbsp;Is Learning Center</label></div>
<div class="checkbox"><label><input type="checkbox" checked="checked" name="selectField[]" value="s.is_special"> &nbsp;Is Special</label></div>
<div class="checkbox"><label><input type="checkbox" checked="checked" name="selectField[]" value="s.oldenroll"> &nbsp;Old Enroll</label></div>
<!--<div class="checkbox"><label><input type="checkbox" name="selectField[]" value="sec_graduate_year"> Graduate Year</label></div> -->
</div></div>


		<div class="col-sm-12">
	<label>Guardian Information</label>	<div>
<div class="checkbox"><label><input type="checkbox" checked="checked" name="selectField[]" value="s.fathername"> &nbsp; Father Name</label></div>
<div class="checkbox"><label><input type="checkbox" checked="checked" name="selectField[]" value="s.mothername">&nbsp; Mother Name</label></div>
<div class="checkbox"><label><input type="checkbox" checked="checked" name="selectField[]" value="s.fee_submittedby">&nbsp; Fee Submitted By</label></div>
<div class="checkbox"><label><input type="checkbox" checked="checked" name="selectField[]" value="s.f_qualification">&nbsp; Father Qualification</label></div>
<div class="checkbox"><label><input type="checkbox" checked="checked" name="selectField[]" value="s.m_qualification">&nbsp; Mother Qualification</label></div>
<div class="checkbox"><label><input type="checkbox" checked="checked" name="selectField[]" value="s.f_occupation">&nbsp; Father Occupation</label></div>
<div class="checkbox"><label><input type="checkbox" checked="checked" name="selectField[]" value="s.m_occupation">&nbsp; Mother Occupation</label></div>

</div></div>






</div>
 <div id="load2" style="display:none;"></div>

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

