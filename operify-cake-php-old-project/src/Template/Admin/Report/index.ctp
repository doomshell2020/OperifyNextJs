<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>Enquiry Report</h1>
   <ol class="breadcrumb">
      <li><a href="<?php echo ADMIN_URL;?>dashboards"><i class="fa fa-home"></i>Home</a></li>
      <li><a href="<?php echo ADMIN_URL;?>report/index">Manage Enquiry Report</a></li>
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
      //<![CDATA[
      $(document).ready(function () {
        $("#TaskAdminCustomerFormss").bind("submit", function (event) {
          $.ajax({
            async:true,
            data:$("#TaskAdminCustomerFormss").serialize(),
            dataType:"html", 
            type:"POST", 
           url:"<?php echo ADMIN_URL ;?>report/search",
            success:function (data) {
      		$("#updt").show();   
              $("#example22").html(data); }, 
        });
          return false;
      });});
      //]]>
   </script>
   <?php echo $this->Form->create('Task',array('type'=>'file','inputDefaults'=>array('div'=>false,'label'=>false),'id'=>'TaskAdminCustomerFormss','class'=>'form-horizontal')); ?>
   <div class="form-group" style="display:flex; align-items: flex-end; flex-wrap:wrap;">
      <div class="col-sm-4">
         <script>
            $(function() {		  
               $('#enqiry_date').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true,
               changeYear: true });
             $('#to_date').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true,
               changeYear: true });
            		});		
         </script>	
         <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
         <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
         <label for="inputEmail3" class="control-label">Start Date<span style="color:red;">*</span></label>
         <?php echo $this->Form->input('from',array('class'=>'form-control','placeholder'=>'Start Date','required','id'=>'enqiry_date','label' =>false)); ?>
      </div>
      <div class="col-sm-4">	
         <label for="inputEmail3" class="control-label">End Date<span style="color:red;">*</span></label>
         <?php echo $this->Form->input('to',array('class'=>'form-control','placeholder'=>'End Date','id'=>'to_date','required','label' =>false)); ?>
      </div>
      <div class="col-sm-4" >
         <label for="inputEmail3" class="control-label">Select Class</label>
         <?php echo $this->Form->input('class_id',array('class'=>'form-control','id'=>'subjt','type'=>'select','empty'=>'Select Class','options'=>$classes,'label' =>false)); ?>  
      </div>
      <div class="col-sm-4" >
         <label for="inputEmail3" class="control-label">Name  </label>
         <?php echo $this->Form->input('name',array('class'=>'form-control','placeholder'=>'Enter Name','id'=>'enqiry_date','label' =>false)); ?>
      </div>
      <div class="col-sm-4" >
         <label for="inputEmail3" class="control-label">Status</label>
         <?php $status=array('Y'=>'Open Enquiry','N'=>'Closed Enquiry')?>
         <?php echo $this->Form->input('status',array('class'=>'form-control','empty'=>'Select Status','options'=>$status,'label' =>false)); ?>
      </div>
      <div class="col-sm-4">
        <input type="submit" style="background-color:#00c0ef;color:#fff;width:100px !important; margin-right: 10px;" class="btn btn4 btn_pdf myscl-btn date" value="Submit">  
        <button type="reset" style="background-color:#333333;color:#fff;width:100px !important; margin-right: 10px;" class="btn btn4 btn_pdf myscl-btn date">Reset</button>  
      </div>
   </div>
   <?php
      echo $this->Form->end();
      ?>          
   <!-- /.box-header -->
   <?php echo $this->Flash->render(); ?>
</div>
<div id="updt" style="display:none">
<div class="box-body">
   <div class="table-responsive">
      <table id="" class="table table-bordered table-striped">
         <thead>
            <tr>
               <th>#</th>
               <th>Enquiry Date</th>
               <th>Name</th>
               <th>Mobile</th>
               <th>Academic Year</th>
               <th>Class</th>
               <th>Last Follow-up Status</th>
               <th>Enquiry Status</th>
            </tr>
         </thead>
         <tbody id="example22">	
         </tbody>
      </table>
   </div>
</div>