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
            <i class="fa fa-money"></i> Total Fees Report        </h1>
                <ol class="breadcrumb">
          		<li><a href="<?php echo ADMIN_URL;?>dashboards"><i class="fa fa-home"></i>Home</a></li>
<li><a href="<?php echo ADMIN_URL;?>report/fees">Manage Fees Report </a></li>  
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
  $("#feesexl").bind("submit", function (event) {
    $.ajax({
      async:true,
      data:$("#feesexl").serialize(),
      dataType:"html", 
      type:"POST", 
     url:"<?php echo ADMIN_URL ;?>report/search5",
      success:function (data) {
		//  alert(data); 
		  
	//	$("#updt").show();   
        $("#updt").html(data); }, 
  });
    return false;
});});
//]]>
</script>

  <?php echo $this->Form->create('Fees',array('type'=>'file','inputDefaults'=>array('div'=>false,'label'=>false),'id'=>'feesexl','class'=>'form-horizontal')); ?>

 <div class="form-group">

<input type="hidden" name="acedmicyear" value="<? echo $acedmic; ?>">
   
    
     <div class="col-sm-3" >
		  <label for="inputEmail3" class="control-label">Select Class</label>
	    	<?php echo $this->Form->input('class_id',array('class'=>'form-control','id'=>'subjt','type'=>'select','empty'=>'Select Class','options'=>$classes,'label' =>false)); ?>  
		          </div>     
		          
	     <div class="col-sm-3" >
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
    
    
	$('#fendfrom').datepicker({    
	dateFormat: 'yy-mm-dd'});
			 });
			   </script>	
		  <label for="inputEmail3" class="control-label">Date From</label>
	    	<?php echo $this->Form->input('datefrom',array('class'=>'form-control','readonly','id'=>'fdatefrom','placeholder'=>'Date From','label' =>false)); ?>  
		          </div>   	   
		            <div class="col-sm-3" >
		 		  <label for="inputEmail3" class="control-label">Date To</label>
	    	<?php echo $this->Form->input('dateto',array('class'=>'form-control','readonly','id'=>'fendfrom','placeholder'=>'Date To','label' =>false)); ?>  
		          </div>   	                      		                                  
		                             		                         
    </div>
   <div class="form-group">  
   <input type="submit" style="background-color:#00c0ef;width:100px !important; margin-left: 10px;" class="btn btn4 btn_pdf myscl-btn date" value="Submit">  
   <button type="reset" style="background-color:#f4f4f4;width:100px !important; margin-left: 10px;" class="btn btn4 btn_pdf myscl-btn date">Reset</button>  
   </div>
  <?php 
echo $this->Form->end();
?>          
            <!-- /.box-header -->
<?php echo $this->Flash->render(); ?>
		</div>
		<div id="updt">  
</div>

