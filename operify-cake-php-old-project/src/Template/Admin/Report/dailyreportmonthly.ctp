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
            <i class="fa fa-money"></i> Daily Summary Collection Report        </h1>
                <ol class="breadcrumb">
          		<li><a href="<?php echo ADMIN_URL;?>report/dailyreport"><i class="fa fa-home"></i>Home</a></li>
<li><a href="<?php echo ADMIN_URL;?>report/dailyreport">Daily Summary Collection Report </a></li>  
   </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <style>
          #loader {
    display: none;
    position: absolute;
    top: 5%;
    left: 45%;
    width: 200px;
    height: 200px;
    padding:30px 15px 0px;
    border: 3px solid #ababab;
    box-shadow:1px 1px 10px #ababab;
    border-radius:20px;
    background-color: white;
    z-index: 1002;
    text-align:center;
    overflow: auto;
}
          
          </style>
          
          <div id="loader" >
  <img src="<?php echo SITE_URL; ?>img/loading-gif-loader-v4.gif" class="img-responsive" />
</div>
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
    if($('input[name=mode[]]:checked').length == 0)
    {
        alert('Please select atleast one checkbox');
        
        return false;
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
     url:"<?php echo ADMIN_URL ;?>report/searchdailyreportmonthly",
      beforeSend: function(){
    // Show image container
    $("#loader").show();
   },
      success:function (data) {
		//  alert(data); 
		  
	//	$("#updt").show();   
        $("#updt").html(data); }, 
        complete:function(data){
    // Hide image container
    $("#loader").hide();
   },
  });
    return false;
});
});
//]]>
</script>

  <?php echo $this->Form->create('Fees',array('type'=>'file','inputDefaults'=>array('div'=>false,'label'=>false),'id'=>'feesexl','class'=>'form-horizontal')); ?>

 <div class="form-group">

<input type="hidden" name="acedmicyear" value="<? echo $acedmic; ?>">
   
    
    
		          
	     <div class="col-sm-2" >
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
    $('#fdatefrom').datepicker('setDate', 'today');
    
	$('#fendfrom').datepicker({    
	dateFormat: 'yy-mm-dd'});
	 $('#fendfrom').datepicker('setDate', 'today');
			 });
			   </script>	
		  <label for="inputEmail3" class="control-label">Date From</label>
	    	<?php echo $this->Form->input('datefrom',array('class'=>'form-control','readonly','id'=>'fdatefrom','placeholder'=>'Date From','label' =>false)); ?>  
		          </div>   	   
		            <div class="col-sm-2" >
		 		  <label for="inputEmail3" class="control-label">Date To</label>
	    	<?php echo $this->Form->input('dateto',array('class'=>'form-control','readonly','id'=>'fendfrom','placeholder'=>'Date To','label' =>false)); ?>  
		          </div>   
		           <div class="col-sm-6" >
		 			 <b style=" display:block;">Mode :</b>    <span>&nbsp;&nbsp;&nbsp;&nbsp;<label class="radio-inline"><input  id="radio1" name="mode[]" checked="checked" value="CASH"  type="checkbox">Cash</label>

									 <label class="radio-inline"><input name="mode[]" checked="checked" id="radio2" value="CHEQUE" type="checkbox">Cheque</label>
									 <label class="radio-inline"><input id="radio1" checked="checked" name="mode[]" value="DD"  type="checkbox">Dd</label>
									  <label class="radio-inline"><input  id="radio1" checked="checked" name="mode[]" value="NETBANKING"   type="checkbox">Netbanking</label>
									 <label class="radio-inline"><input  id="radio1"  checked="checked" name="mode[]" value="CREDIT CARD/DEBIT CARD"  type="checkbox">Credit Card/Debit Card</label>
									 </span>   </div>   		                      		                                  
		                             		                         
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
		 
		 
	<div style="padding: 5px 5px 0px 19px;">
    <legend  style="margin-bottom:0px;!important;">
<label><input type="checkbox"  name="checkAll" id="select_all" checked="checked" value="1"> Select All</label>	</legend>
</div>
</div>
	 <div class="form-group">	 
<div class="col-sm-12">
	<label>Fees Heads</label>
<div>

	<div class="checkbox col-sm-2"><label><input type="checkbox" name="selectField[]" checked="checked" value="<? echo "Registration"; ?>"> <? echo "Registration"; ?></label></div>
	<div class="checkbox col-sm-2"><label><input type="checkbox" name="selectField[]" checked="checked" value="<? echo "Prospectus"; ?>"> <? echo "Prospectus"; ?></label></div>
		<div class="checkbox col-sm-2"><label><input type="checkbox" name="selectField[]" checked="checked" value="<? echo "Prev. Due"; ?>"> <? echo "Prev. Due"; ?></label></div>
		<div class="checkbox col-sm-2"><label><input type="checkbox" name="selectField[]" checked="checked" value="<? echo "Late Fee"; ?>"> <? echo "Late Fee"; ?></label></div>

	 <?php foreach($feesheadstotal as $j=>$el){ ?>
<div class="checkbox col-sm-2"><label><input type="checkbox" name="selectField[]" checked="checked" value="<? echo $el['name']; ?>"> <? echo ucwords(strtolower($el['name'])); ?></label></div>
<? } ?>





<div class="checkbox col-sm-2"><label><input type="checkbox" name="selectField[]" checked   value="<? echo "Due Amount"; ?>"> <? echo "Due Amount"; ?></label></div>
<div class="checkbox col-sm-2"><label><input type="checkbox" name="selectField[]" checked    value="<? echo "Access Amount"; ?>"> <? echo "Access Amount"; ?></label></div>

<div class="checkbox col-sm-2"><label><input type="checkbox" name="selectField[]" checked   value="<? echo "Prev. Access Amount"; ?>"> <? echo "Prev. Access Amount"; ?></label></div>

<div class="checkbox col-sm-2"><label><input type="checkbox" checked="checked"  disabled name="selectField[]" value="<? echo "Discount Fee"; ?>"> 
<input type="hidden" checked="checked"   name="selectField[]" value="<? echo "Discount Fee"; ?>"><? echo "Discount Fee"; ?></label></div>

<div class="checkbox col-sm-2"><label><input type="checkbox"  checked="checked" disabled readonly name="selectField[]" value="<? echo "Other Discount"; ?>"> <input type="hidden"  checked="checked"   name="selectField[]" value="<? echo "Other Discount"; ?>"> <? echo "Other Discount"; ?></label></div>




</div></div>
</div>


   <div class="form-group">  
   <input type="submit" id="YourbuttonId" style="background-color:#00c0ef;width:100px !important; margin-left: 10px;" class="btn btn4 btn_pdf myscl-btn date" value="Submit">  
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

