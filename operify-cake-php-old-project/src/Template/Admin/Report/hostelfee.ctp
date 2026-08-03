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
            <i class="fa fa-money"></i> Hostel Fees Report        </h1>
                          <ol class="breadcrumb">
		<li><a href="<?php echo ADMIN_URL;?>dashboards"><i class="fa fa-home"></i>Home</a></li>
<li><a href="<?php echo ADMIN_URL;?>report/hostelfee">Manage Hostel Fees Report</a></li>
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
  $("#hostelfeexl").bind("submit", function (event) {
    $.ajax({
      async:true,
      data:$("#hostelfeexl").serialize(),
      dataType:"html", 
      type:"POST", 
     url:"<?php echo ADMIN_URL ;?>report/search8",
      success:function (data) {
		//  alert(data); 
		  
	//	$("#updt").show();   
        $("#updt").html(data); }, 
  });
    return false;
});});
//]]>
</script>

  <?php echo $this->Form->create('Student',array('type'=>'file','inputDefaults'=>array('div'=>false,'label'=>false),'id'=>'hostelfeexl','class'=>'form-horizontal')); ?>
 <div class="form-group">
<div class="col-sm-4" >
		  <label for="inputEmail3" class="text-bold text-green">Hostel</label>
	    	<?php echo $this->Form->input('h_id',array('class'=>'form-control ','id'=>'subjt','type'=>'select','empty'=>'All','options'=>$hostel,'label' =>false)); ?>  
		          </div> 
</div>


 <div class="form-group">
 <div class="col-sm-4">	
  <label for="inputEmail3" class="control-label">Acedamic Year</label>
      <select class="form-control" name="acedmicyear" id="ac">
  <option value="">Select Acedamic Year</option>
 <?= $year=date("Y");  $year2=$year-1; $exyear=$year+3; ?>

  <?php for ($i = $year2; $i <= $exyear; $i++) :  $rt=$i+1; $rt= substr($rt,2);$st=$i.'-'.$rt?> 
        <option <?php if($i==$year){ ?>selected <?php } ?> value="<?php echo $i; ?>-<?php echo  $rt;?>" <?php if($st==$exams['acedamicyear']){ echo "selected";  } ?> ><?php echo $i; ?>-<?php echo  $rt;?></option>
    <?php endfor; ?>
</select>
    </div>
    
     <div class="col-sm-4" >
		  <label for="inputEmail3" class="control-label">Select Class</label>
	    	<?php echo $this->Form->input('class_id',array('class'=>'form-control','id'=>'cls','type'=>'select','empty'=>'Select Class','options'=>$classes,'label' =>false)); ?>  
		          </div> 
		          
		      <div class="col-sm-4" >
				  	  <label for="inputEmail3" class="control-label">Select Section</label>
		   <select class="form-control" name="section_id" id="section-id"> 
   <option value="">Select Section</option>
</select>
	    	     <script> 
$(document).ready(function(){
$('#cls').on('change',function(){
var id = $('#cls').val();
 $.ajax({ 
        type: 'POST', 
        url: '<?php echo ADMIN_URL ;?>report/find_section',
        data: {'id':id}, 
        success: function(data){  
//alert(data);
 $('#section-id').empty();
  $('#section-id').html(data);

  
        }, 
        
    });  
});
});
</script>	
		          </div>          
		      </div>  
		       
		 <div class="form-group">      
	   <div class="col-sm-4" >
		     <label for="inputEmail3" class="control-label">First/Last Name/ Email </label>
	<?php echo $this->Form->input('name',array('class'=>'form-control','placeholder'=>'First/Last Name/ Email','id'=>'endate','label' =>false)); ?>
		          </div> 	 
		
		    <div class="form-group">    
		    <div class="col-sm-4" >
		     <label for="inputEmail3" class="control-label">Student Id </label>
	<?php echo $this->Form->input('ids',array('class'=>'form-control','placeholder'=>'Student Id','id'=>'edte','label' =>false)); ?>
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
		</div>
		<div id="updt">  
</div>

