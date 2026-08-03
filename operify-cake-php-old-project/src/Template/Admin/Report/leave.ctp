 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Leave Report
       
      </h1>
                <ol class="breadcrumb">
          		<li><a href="<?php echo ADMIN_URL;?>dashboards"><i class="fa fa-home"></i>Home</a></li>
<li><a href="<?php echo ADMIN_URL;?>report/leave">Manage Leave Report </a></li>  
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
        


  <?php
 $employee_name=array();
  foreach($employee as $key=>$value) 
 {
	 
	$guardian=$this->Comman->find_guardiannames($value['id']) ;
	$guardian_name=$guardian[0]['fullname'];
	if(!empty($guardian_name))
	{
  $employee_name[$value['id']]=$value['fname']." ".$value['middlename']." ".$value['lname']." S/o ".$guardian_name;
}
else
{
  $employee_name[$value['id']]=$value['fname']." ".$value['middlename']." ".$value['lname'];	
	
}
 
}
?>
              <script inline="1">
//<![CDATA[
$(document).ready(function () {
  $("#leaves").bind("submit", function (event) {
    $.ajax({
      async:true,
      data:$("#leaves").serialize(),
      dataType:"html", 
      type:"POST", 
     url:"<?php echo ADMIN_URL ;?>report/search9",
      success:function (data) {  
        $("#example22").html(data); }, 
  });
    return false;
});});
//]]>
</script>

  <?php echo $this->Form->create('Task',array('type'=>'file','inputDefaults'=>array('div'=>false,'label'=>false),'id'=>'leaves','class'=>'form-horizontal')); ?>
 <div class="form-group">
	 
	  <div class="col-sm-4" >
		  <label for="inputEmail3" class="control-label">Select Employee</label>
	    	<?php echo $this->Form->input('emp_id',array('class'=>'form-control','id'=>'subjt','type'=>'select','empty'=>'All Employee','options'=>$employee_name,'label' =>false)); ?>  
		          </div> 
		         <div class="col-sm-4">
		  <script>
	$(function() {		  
    $('#enqiry_date').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true,
    changeYear: true });
	 $('#to_date').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true,
    changeYear: true, });
			});		
			
  </script>	
		
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<label for="inputEmail3" class="control-label">Start Date<span style="color:red;">*</span></label>
<?php echo $this->Form->input('from',array('class'=>'form-control','placeholder'=>'Start Date','value'=>'','id'=>'enqiry_date','label' =>false)); ?>
    </div>
		          
 <div class="col-sm-4">	
 <label for="inputEmail3" class="control-label">End Date</label>
<?php echo $this->Form->input('response',array('class'=>'form-control','placeholder'=>'End Date','value'=>'','id'=>'to_date','label' =>false)); ?>
    </div>
    
    </div>
 
    
		           
    
   <input type="submit" style="background-color:#00c0ef;width:100px !important; margin-left: 10px;" class="btn btn4 btn_pdf myscl-btn date" value="Submit">  
   <button type="reset" style="background-color:#f4f4f4;width:100px !important; margin-left: 10px;" class="btn btn4 btn_pdf myscl-btn date">Reset</button>  
   <?php
echo $this->Form->end();
?>          
            <!-- /.box-header -->
<?php echo $this->Flash->render(); ?>
 
		        </div>  
		
		<div id="example22">  
</div>

