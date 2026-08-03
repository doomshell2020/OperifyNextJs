 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
    <h1>
            <i class="fa fa-info-circle"></i> Occupied Room        </h1>
                     <ol class="breadcrumb">
		<li><a href="<?php echo ADMIN_URL;?>dashboards"><i class="fa fa-home"></i>Home</a></li>
<li><a href="<?php echo ADMIN_URL;?>report/orooms">Manage Occupied Room Report</a></li>
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
  $("#student_hostel").bind("submit", function (event) {
    $.ajax({
      async:true,
      data:$("#student_hostel").serialize(),
      dataType:"html", 
      type:"POST", 
     url:"<?php echo ADMIN_URL ;?>report/search11",
      success:function (data) {
		//  alert(data); 
		  
	//	$("#updt").show();   
        $("#updt").html(data); }, 
  });
    return false;
});});
//]]>
</script>

  <?php echo $this->Form->create('Student',array('type'=>'file','inputDefaults'=>array('div'=>false,'label'=>false),'id'=>'student_hostel','class'=>'form-horizontal')); ?>
 <div class="form-group">
<div class="col-sm-4" >
		  <label for="inputEmail3" class="text-bold text-green">Hostel</label>
	    	<?php echo $this->Form->input('h_id',array('class'=>'form-control ','id'=>'subjt','type'=>'select','empty'=>'All ','options'=>$hostel,'label' =>false)); ?>  
		          </div> 
</div>
    <div class="form-group"> 
      <div class="col-sm-6" >
   <input type="submit" style="background-color:#00c0ef;width:100px !important; margin-left: 10px;" id="YourbuttonId" class="btn btn4 btn_pdf myscl-btn date" value="Submit"  onclick="return testcheck()">  
   <button type="reset" style="background-color:#f4f4f4;width:100px !important; margin-left: 10px;" class="btn btn4 btn_pdf myscl-btn date">Reset</button>  
   </div>
   </div>
   <?php
echo $this->Form->end();
?>          
            <!-- /.box-header -->
<?php echo $this->Flash->render(); ?>
 
		      
		</div>
	
		<div id="updt">  
</div>

