 <!-- Content Wrapper. Contains page content -->

 
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Prospectus/Registration Selling Report
       
      </h1>
           <ol class="breadcrumb">

    <li><a href="<?php echo ADMIN_URL;?>dashboards"><i class="fa fa-home"></i>Home</a></li>
<li><a href="<?php echo ADMIN_URL;?>report/prospect">Prospectus/Registration Report</a></li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
	 <div class="row">
        <div class="col-xs-12">
			
          <?php echo $this->Flash->render(); ?>
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
//<![CDATA[
$(document).ready(function () {
  $("#TaskAdminCustomerFormss").bind("submit", function (event) {
    $.ajax({
      async:true,
      data:$("#TaskAdminCustomerFormss").serialize(),
      dataType:"html", 
      type:"POST", 
     url:"<?php echo ADMIN_URL ;?>report/prospectreportsearch",
      success:function (data) {
    $("#updt").show();   
        $("#updt").html(data); }, 
  });
    return false;
});});
//]]>
</script>

  <?php echo $this->Form->create('Task',array('type'=>'file','inputDefaults'=>array('div'=>false,'label'=>false),'id'=>'TaskAdminCustomerFormss','class'=>'form-horizontal')); ?>

 <div class="form-group">
           
      <script>
  $(function() {      
    $('#enqiry_date').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true,
    changeYear: true });
   $('#to_date').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true,
    changeYear: true });
      });   
      
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
    
    
	$('#fendfrom').datepicker({    
	dateFormat: 'yy-mm-dd'});
			 });
      
      
      
  </script> 

 
    
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<div class="col-sm-2" >
      <label for="inputEmail3" class="control-label">Select Class</label>
        <?php echo $this->Form->input('class_id',array('class'=>'form-control','id'=>'subjt','type'=>'select','empty'=>'Select Class','options'=>$classes,'label' =>false)); ?>  
              </div> 
           <div class="col-sm-2">
<label for="inputEmail3" class="control-label">Start Date<span style="color:red;">*</span></label>
<?php echo $this->Form->input('from',array('class'=>'form-control','placeholder'=>'Start Date','readonly','id'=>'fdatefrom','label' =>false)); ?>
    </div>
              
 <div class="col-sm-2"> 
 <label for="inputEmail3" class="control-label">End Date<span style="color:red;">*</span></label>
<?php echo $this->Form->input('to',array('class'=>'form-control','placeholder'=>'End Date','readonly','id'=>'fendfrom','label' =>false)); ?>
    </div>
  <div class="col-sm-3" >
         <label for="inputEmail3" class="control-label">Report For</label>
         <?php $st=array('5'=>'Prospectus','1'=>'Registration'); ?>
  <?php echo $this->Form->input('s_id',array('class'=>'form-control','required'=>'required','id'=>'subjt','type'=>'select','empty'=>'Select','options'=>$st,'label' =>false)); ?>  
              </div> 
                   
 <div class="col-sm-3"> 
        <label for="inputEmail3" class="control-label" style="padding-top: 48px;"></label>
     
   <input type="submit" style="background-color:#00c0ef;width:100px !important; margin-left: 10px;" class="btn btn4 btn_pdf myscl-btn date" value="Submit">  
   <button type="reset" style="background-color:#f4f4f4;width:100px !important; margin-left: 10px;" class="btn btn4 btn_pdf myscl-btn date">Reset</button>  
     </div><?php
echo $this->Form->end();
?>    

   
            <!-- /.box-header -->
<?php echo $this->Flash->render(); ?>
 
            </div>  
             

	<div id="updt">  
</div>

               
           

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="<?php echo SITE_URL; ?>daterangepicker/daterangepicker.js"></script>
  <link rel="stylesheet" href="<?php echo SITE_URL; ?>daterangepicker/daterangepicker.css">
<script type="text/javascript">
       //Date range picker with time picker
    $('#reservationtime').daterangepicker({timePicker: true, timePickerIncrement: 30, locale: {
            format: 'MM/DD/YYYY h:mm A'
        }   });
</script>

