 <!-- Content Wrapper. Contains page content -->


  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Student Prospectus Summary Report</h1>
           <ol class="breadcrumb">
 <li><a href="<?php echo ADMIN_URL;?>report/prospectussummary"><i class="fa fa-thumbs-up"></i>Student Prospectus Summary Report</a></li>



        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
	 <div class="row">
        <div class="col-xs-12">

          <?php echo $this->Flash->render(); ?>
  <div class="box">

    <div class="box-header">
		  <style>

         #load2{
    width:100%;
    height:100%;
    position:fixed;
    z-index:9999;
    background-color: white  !important;
    background:url("<? echo SITE_URL; ?>images/Preloader_2.gif") no-repeat center center rgba(0,0,0,0.75)
}

</style>
              <script inline="1">
//<![CDATA[
$(document).ready(function () {
  $("#TaskAdminCustomerFormss").bind("submit", function (event) {
    $.ajax({
      async:true,
      data:$("#TaskAdminCustomerFormss").serialize(),
      dataType:"html",
      beforeSend: function(xhr) {
        xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val())
         $('#load2').css("display", "block");
    },
      type:"POST",
     url:"<?php echo ADMIN_URL ;?>report/prospectussearch",
      success:function (data) {
    $("#updt").show();
        $("#updt").html(data); }, complete: function() {
           $('#load2').css("display", "none");
    },
  });
    return false;
});});
//]]>
</script>
  <script type='text/javascript'>

    $(document).ready(function(){
        $('.txtDate').datepicker({
  dateFormat: 'yy-mm-dd',
  yearRange: '2010:2030',
  changeMonth: true,
     changeYear: true,
        onSelect: function(date){

        var selectedDate = new Date(date);
        var endDate = new Date(selectedDate);
         endDate.setDate(endDate.getDate());

        $(".txtDate1").datepicker( "option", "minDate", endDate );
        $(".txtDate1").val(date);
    }

    });


$('.txtDate').datepicker('setDate', 'today');

	$('.txtDate1').datepicker({
	dateFormat: 'yy-mm-dd',changeMonth: true,
     changeYear: true});
	  $('.txtDate1').datepicker('setDate', 'today');
			 });


</script>
  <?php echo $this->Form->create('Task',array('type'=>'file','inputDefaults'=>array('div'=>false,'label'=>false),'id'=>'TaskAdminCustomerFormss','class'=>'form-horizontal')); ?>

 <div class="form-group">

             <div class="col-md-2 col-sm-4 col-xs-6">
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
 

<label for="inputEmail3" class="control-label">Selling Date From<span style="color:red;">*</span></label>
<?php echo $this->Form->input('from',array('readonly'=>'readonly','autocomplete'=>'off','required'=>'required','class'=>'form-control txtDate','placeholder'=>'Admission From','label' =>false)); ?>

 </div>

 <div class="col-md-2 col-sm-4 col-xs-6">
 <label for="inputEmail3" class="control-label">Selling Date To<span style="color:red;">*</span></label>
 <?php echo $this->Form->input('to',array('readonly'=>'readonly','autocomplete'=>'off','required'=>'required','class'=>'form-control txtDate1','placeholder'=>'Admission To','label' =>false)); ?>


 </div>


     <div class="col-md-2 col-sm-4 col-xs-6" >
      <label for="inputEmail3" class="control-label">Select Acedmicyear Year<span style="color:red;">*</span></label>
<select class="form-control" name="acedmicyear[]"  required="required" >

    <option value="2020-21">+ 2020-21</option>
    <option value="2021-22" selected="selected">2021-22</option>
</select>
              </div>
     <div class="col-md-3 col-sm-4 col-xs-6" >
      <label for="inputEmail3" class="control-label">Select Class<span style="color:red;">*</span></label>
       <select class="form-control" name="class_id[]" required="required" multiple="multiple">

  <?php  foreach($classes as $esr=>$es) { ?>
  <option  value="<?php echo $esr; ?>" selected="selected"><?php echo $es; ?></option>
  <?php } ?>
</select>
              </div>


     <div class="col-md-3 col-sm-4 col-xs-6 text-xs-center"  style="margin-top: 29px;">

 <input type="submit" style="background-color:#00c0ef;width:100px !important; margin-left: 10px;" class="btn btn4 btn_pdf myscl-btn date" value="Submit">
   <button type="reset" style="background-color:#f4f4f4;width:100px !important; margin-left: 10px;" class="btn btn4 btn_pdf myscl-btn date">Reset</button>

         </div>

    </div>




   <?php
echo $this->Form->end();
?>


            <!-- /.box-header -->
<?php echo $this->Flash->render(); ?>

            </div>
                <div id="load2" style="display:none;"></div>

    <div id="updt">





      <div style="clear: both;"></div>

<div class="table-responsive">
          <table id="example1" class="table table-bordered table-striped">
                <thead>



                <tr>


                  </tr>
                </thead>
                <tbody id="example22">


    <tr>
    <td colspan="5" style="text-align:center;color:red;"><b>Please Select Above Criteria !!!!</b></td>
    </tr>






                </tbody>

              </table>
              </div>
               </div>




