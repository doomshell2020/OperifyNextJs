<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
<div class="box">
            <div class="box-header">
              <i class="fa fa-search" aria-hidden="true"></i>
<h3 class="box-title">Optional Subjects Update Manager</h3>
      
            </div>
              
     <style>
         
         #load{
    width:100%;
    height:100%;
    position:fixed;
    z-index:9999;
    background-color: white  !important;
    background:url("<?php echo SITE_URL; ?>images/Preloader_2.gif") no-repeat center center rgba(0,0,0,0.75)
}

</style>
 <style>
         
         #load2{
    width:100%;
    height:100%;
    position:fixed;
    z-index:9999;
    background-color: white  !important;
    background:url("<?php echo SITE_URL; ?>images/Preloader_2.gif") no-repeat center center rgba(0,0,0,0.75)
}

</style>

            <!-- /.box-header -->
<?php echo $this->Flash->render(); ?>
            <div class="box-body">
		

<div class="manag-stu">
	
<script inline="1">
//<![CDATA[
$(document).ready(function () {
$("#TaskAdminCustomerForm").bind("submit", function (event) {
    $.ajax({

async:false,

type:"POST", 

url:"<?php echo ADMIN_URL ;?>Report/optionalsubjectsedit",

data:$("#TaskAdminCustomerForm").serialize(),

dataType:"html", 

success:function (data) {
  //alert(data);
  //console.log(data);
  $("#srch-rslt").html(data);
}

});
return false;
});
});
//]]>
</script>
	 <?php echo $this->Form->create('Task',array('type'=>'file','inputDefaults'=>array('div'=>false,'label'=>false),'id'=>'TaskAdminCustomerForm','class'=>'form-horizontal')); ?>
<div class="form-group">
  
    <script>
$(document).ready(function(){
$('#class-ids').on('change',function(){
var id = $('#class-ids').val();
//alert(id);
 $.ajax({ 
        type: 'POST', 
        url: '<?php echo ADMIN_URL ;?>ClasstimeTabs/find_section',
        data: {'id':id}, 
        success: function(data){  

 $('#section-id').empty();
  $('#section-id').html(data);
        }, 
        
    });  
});
});

</script>


 
    
      <div class="col-sm-3">
 <label>Select Class</label>
   	<?php 
		echo 
		$this->Form->input('class_id',array('class'=>'form-control','type'=>'select','id'=>'class-ids','empty'=>'Select Class','options'=>$classes,'label' =>false, 'required'));
		 ?>
    </div>  

   <div class="col-sm-3">
 <label>Select Section</label>
   	<?php 

  
		 echo $this->Form->input('section_id',array('class'=>'form-control','type'=>'select','empty'=>'Select Section','options'=>$sectionslist,'label' =>false, 'required')); 
		 
		  ?>  
    </div>   <div class="col-sm-3" style="top: 22px;">
      <button type="submit" class="btn btn-success">Search</button>
  
       <button type="reset" class="btn btn-primary">Reset</button>
    </div>
  
     <?php
echo $this->Form->end();
?>   
</div>
  

				
				</div>
				
					</div>
  </section>
  <section class="content">

    
      
      <div id="srch-rslt">
      <P>Please Select Class and Section</p>





      </div>
      
     
      </section>

  </div>