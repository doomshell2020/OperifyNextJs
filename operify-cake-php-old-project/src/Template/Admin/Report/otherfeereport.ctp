<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<div class="content-wrapper" style="min-height:200px !important;">
  <div class="content-header">
    <h1>Other Fee Collection Report</h1>
  </div>
  <section class="content">
    <div class="box" style="padding-bottom:15px">
      <div class="box-body">
        <?php echo $this->Form->create('', array('class' => '', 'id' => 'sevice_form1', 'enctype' => 'multipart/form-data', 'validate', 'autocomplete' => 'off')); ?>
        <div class="row margin_btm_cl" style="padding-left:1%">
          <div class="col-md-2 col-sm-6 col-xs-12">
            <label>Academic Year</label>
            <?php echo $this->Form->input('acedmicyear', array('type' => 'select', 'options' => $acd, 'empty' => '--Select Academic Year--', 'class' => 'form-control', 'value' => $rolepresentyear, 'label' => false)); ?>
          </div>
          <div class="col-md-2 col-sm-6 col-xs-12">
            <label>From Date</label>
            <?php echo $this->Form->input('from', array('id' => 'datepicker1', 'type' => 'text', 'placeholder' => 'From Date', 'class' => 'form-control', 'label' => false)); ?>
          </div>
          <div class="col-md-2 col-sm-6 col-xs-12">
            <label>To Date</label>
            <?php echo $this->Form->input('to', array('id' => 'datepicker2', 'type' => 'text', 'placeholder' => 'To Date', 'class' => 'form-control', 'label' => false)); ?>
          </div>
         <script>
          $(document).ready(function() {
            $('#datepicker1').datepicker({
              dateFormat: 'yy-mm-dd',
              maxDate:0,
              onSelect: function(date) {

                var selectedDate = new Date(date);
                var endDate = new Date(selectedDate);
                endDate.setDate(endDate.getDate());

                $("#datepicker2").datepicker("option", "minDate", endDate);
                $("#datepicker2").val(date);
              }

            });


            $('#datepicker1').datepicker('setDate', 'today');

            $('#datepicker2').datepicker({
              dateFormat: 'yy-mm-dd',
              maxDate:0
            });
            $('#datepicker2').datepicker('setDate', 'today');
          });
          </script>
           <div class="col-md-2 col-sm-6 col-xs-12">
            <label>Class</label>
            <?php echo $this->Form->input('class_id', array('type' => 'select', 'options' => $class_id, 'empty' => '--Select Class--', 'class' => 'form-control', 'label' => false)); ?>
          </div>
      
			<div class="col-md-4 col-sm-12 col-xs-12 chk-box" >
            <label>Payment Mode:</label>
            <div>
            <label><input type="checkbox" name="mode[]" value="CASH" checked="checked">Cash</label>
            <label><input type="checkbox" name="mode[]" value="CHEQUE" checked="checked">Cheque</label>
            <label><input type="checkbox" name="mode[]" value="DD" checked="checked">DD</label>
            <label><input type="checkbox" name="mode[]" value="NETBANKING" checked="checked">NetBanking</label>
            <label><input type="checkbox" name="mode[]" value="CREDIT CARD/DEBIT CARD" checked="checked">Credit card/ Debit Card</label>
          </div>
          </div>
         
            
        </div>
         <div class="form-group">
           <div style="padding: 5px 5px 0px 19px;">
            <legend style="margin-bottom:0px;!important;">
              <label><input type="checkbox" checked  name="checkAll" id="select_all" value="1"> Select All</label>	</legend>
           </div>
         </div>
          <div class="form-group">	
			  <div class="col-sm-12">
	<b>Fees Heads</b>
	</div> 
	</div>
	<div class="form-group" id="otherfee">
<div class="col-sm-12" style="padding:10px 2%;">
	
<div>


	 <?php  foreach($feesheadstotal as $j=>$el){  ?>
<div class="checkbox"><label><input type="checkbox" name="selectField[]" value="<? echo strtoupper($el); ?>" checked > <? echo ucwords(strtolower($el)); ?></label></div>
<? } ?>
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





</div></div>
</div>

	 <div class="form-group">	

        <div class="row" style="margin-top:20px">
          <div class="col-sm-12 text-xs-center">
            <input type="submit" id="YourbuttonId"
              style="background-color:#00c0ef;width:100px !important; margin-left: 10px;"
              class="btn btn4 btn_pdf myscl-btn date" value="Submit">
            <button type="reset" style="background-color:#f4f4f4;width:100px !important; margin-left: 10px;"
              class="btn btn4 btn_pdf myscl-btn date">Reset</button>
          </div>
        </div>

      </div>
      <?php echo $this->Form->end(); ?>
    </div>

    <div class="box col-sm-12" id="src-rslt" style="display:none;">
      <div class="box-header">
        <h4><i class="fa fa-search" aria-hidden="true"></i>Search Results</h4>
      </div>
      <div class="box-body updt" id="example12">

      </div>
    </div>
  </section>
</div>
<script inline="1">
$(document).ready(function() {

  $("#sevice_form1").bind("submit", function(event) {
    if ($('input[type=checkbox]:checked').length == 0) {
      alert('Please select atleast one checkbox');
      return false;
    }
    //alert();
    $.ajax({
      async: true,
      data: $("#sevice_form1").serialize(),
      dataType: "html",
      type: "POST",
      beforeSend: function() {
        //Show image container
        $("#loader").show();
      },
      url: "<?php echo ADMIN_URL; ?>report/otherfeeselection",

      success: function(data) {
        //alert(data);
        $('#src-rslt').show();

        $(".updt").html(data);
      },

    });

    return false;
  });
});
//]]>
</script>
<script>
$(document).ready(function() {

  $('.modalcancel').on('click', function() {
    //alert();
    // $("#sevice_form1 :input").prop("disabled", true);
    $('.nkid').val('');
    $('.academikid').val('');
    $('.textryu').val('');

    var idn = $(this).data("val");
    $('.nkid').val(idn);
    var recipetn = $(this).data("id");
    //alert(recipetn);
    $('.ert').html(recipetn);
    var academicy = $(this).data("options");
    $('.academikid').val(academicy);

  });
  $('#modalcancel1').on('click', function() {
    // $("#sevice_form1 :input").prop("disabled", true);
    $('.nkid').val('');
    $('.academikid').val('');
    $('.textryu').val('');

    var idn = $(this).data("val");
    $('.nkid').val(idn);
    var recipetn = $(this).data("id");
    //alert(recipetn);
    $('.ert').html(recipetn);
    var academicy = $(this).data("options");
    $('.academikid').val(academicy);

  });
});
</script>
<div class="modal fade" id="myModal" role="dialog">
  <div class="modal-dialog">
    <form method="post" enctype="multipart/form-data" accept-charset="utf-8" class="form-horizontal"
      id="sevice_formtest" validate="validate" action="<?echo ADMIN_URL; ?>studentfees/otherfees_delete">
      <!-- Modal content-->
      <div class="modal-content">


        <div class="modal-header" style="background-color: #3c8dbc;">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Are You Sure ? Do You Want To Cancel This Reference No. <b class="ert"> </b></h4>
        </div>
        <div class="modal-body">

          <textarea type="text" class="textryu" name="reasonforcancelling" required="required" cols="78" rows="5"
            placeholder="Enter Remarks For Cancellation...."></textarea>
          <input type="hidden" name="id" class="nkid">
        </div>
        <div class="modal-footer">

          <div class="submit">
            <input type="submit" class="btn btn-info pull-right" title="Cancel" style="display: block;" value="Submit">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button></div>
        </div>
      </div>
    </form>
  </div>
</div>
