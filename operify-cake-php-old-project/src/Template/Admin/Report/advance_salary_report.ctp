<style>
.select2.select2-container {
  width: 100% !important;
}

.select2.select2-container .select2-selection {
  border: 1px solid #ccc;
  -webkit-border-radius: 3px;
  -moz-border-radius: 3px;
  border-radius: 3px;
  height: 34px;
  margin-bottom: 15px;
  outline: none;
  transition: all 0.15s ease-in-out;
}

.select2.select2-container .select2-selection .select2-selection__rendered {
  color: #333;
  line-height: 32px;
  padding-right: 33px;
}

.select2.select2-container .select2-selection .select2-selection__arrow {
  background: #f8f8f8;
  border-left: 1px solid #ccc;
  -webkit-border-radius: 0 3px 3px 0;
  -moz-border-radius: 0 3px 3px 0;
  border-radius: 0 3px 3px 0;
  height: 32px;
  width: 33px;
}

.select2.select2-container.select2-container--open .select2-selection.select2-selection--single {
  background: #f8f8f8;
}

.select2.select2-container.select2-container--open .select2-selection.select2-selection--single .select2-selection__arrow {
  -webkit-border-radius: 0 3px 0 0;
  -moz-border-radius: 0 3px 0 0;
  border-radius: 0 3px 0 0;
}

.select2.select2-container.select2-container--open .select2-selection.select2-selection--multiple {
  border: 1px solid #34495e;
}

.select2.select2-container.select2-container--focus .select2-selection {
  border: 1px solid #34495e;
}

.select2.select2-container .select2-selection--multiple {
  height: auto;
  min-height: 34px;
}

.select2.select2-container .select2-selection--multiple .select2-search--inline .select2-search__field {
  margin-top: 0;
  height: 32px;
}

.select2.select2-container .select2-selection--multiple .select2-selection__rendered {
  display: block;
  padding: 0 4px;
  line-height: 29px;
}

.select2.select2-container .select2-selection--multiple .select2-selection__choice {
  background-color: #f8f8f8;
  border: 1px solid #ccc;
  -webkit-border-radius: 3px;
  -moz-border-radius: 3px;
  border-radius: 3px;
  margin: 4px 4px 0 0;
  padding: 0 6px 0 22px;
  height: 24px;
  line-height: 24px;
  font-size: 12px;
  position: relative;
}

.select2.select2-container .select2-selection--multiple .select2-selection__choice .select2-selection__choice__remove {
  position: absolute;
  top: 0;
  left: 0;
  height: 22px;
  width: 22px;
  margin: 0;
  text-align: center;
  color: #e74c3c;
  font-weight: bold;
  font-size: 16px;
}

.select2-container .select2-dropdown {
  background: transparent;
  border: none;
  margin-top: -5px;
}

.select2-container .select2-dropdown .select2-search {
  padding: 0;
}

.select2-container .select2-dropdown .select2-search input {
  outline: none;
  border: 1px solid #34495e;
  border-bottom: none;
  padding: 4px 6px;
}

.select2-container .select2-dropdown .select2-results {
  padding: 0;
}

.select2-container .select2-dropdown .select2-results ul {
  background: #fff;
  border: 1px solid #34495e;
}

.select2-container .select2-dropdown .select2-results ul .select2-results__option--highlighted[aria-selected] {
  background-color: #3498db;
}

.big-drop {
  width: 600px !important;
}

.datepicker1 {
  z-index: 1600 !important;
  /* has to be larger than 1050 */
}
</style>


<div class="content-wrapper">
  <section class="content-header">
    <h1 style="margin-bottom:10px !important"><i class="fa fa-money"></i> Advance Report</h1>

  </section>

  <?php echo $this->Flash->render(); ?>
  <div class="row">
    <div class="col-sm-12">
      <div class="box">

        <?php echo $this->Form->create($enquires, array('class' => '', 'id' => 'sevice_form1', 'enctype' => 'multipart/form-data', 'validate', 'autocomplete' => 'off')); ?>

        <div class="box-body">
          <div class="row">

            <div class="col-sm-3">
              <label>From Date<span style="color:red;">*</span></label>
              <?php echo $this->Form->input('from', array('class' => 'form-control', 'required', 'id' => 'datepicker1', 'value' => date('d-m-Y'), 'label' => false)); ?>
            </div>
            <div class="col-sm-3">
              <label>To Date<span style="color:red;">*</span></label>
              <?php echo $this->Form->input('to', array('class' => 'form-control', 'required', 'id' => 'datepicker2', 'value' => date('d-m-Y'), 'label' => false)); ?>
            </div>
            <script>
            $(document).ready(function() {
              $('#datepicker1').datepicker({
                dateFormat: 'yy-mm-dd',
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
                dateFormat: 'yy-mm-dd'
              });
              $('#datepicker2').datepicker('setDate', 'today');
            });
            </script>

            <?php foreach ($staff as $key => $value) {
    $array[$key] = strtoupper(str_replace(";", " ", $value));
}?>
            <div class="col-sm-3">
              <label>Select Employee<span style="color:red;">*</span></label>
              <?php echo $this->Form->input('employee_id', array('class' => 'form-control', 'id' => 'emp_id', 'type' => 'select', 'options' => $array, 'empty' => "--Select Employee--", 'label' => false, 'class' => "js-select2")); ?>
            </div>

          </div>



          <div class="row">

            <div class="col-sm-3">
              <label>Select Payment Mode<span style="color:red;">*</span></label>
              <?php echo $this->Form->input('pay_mode', array('class' => 'form-control', 'id' => 'emp_id', 'type' => 'select', 'options' => $pay_mode, 'empty' => "--Select Payment Mode--", 'label' => false)); ?>
            </div>
            <div class="col-sm-6" style="padding-top:18px">
              <?php echo $this->Form->submit('SEARCH', array('class' => 'btn btn-info pull-right', 'style' => '', 'title' => 'Submit'));

echo $this->Form->end(); ?>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script type="text/javascript">
  $(document).ready(function() {

    $(".js-select2").select2();

    $(".js-select2-multi").select2();

    $(".large").select2({
      dropdownCssClass: "big-drop",
    });

  });
  </script>

  <div class="row search" id="src-rslt" style="display:none">
    <div class="col-sm-12">
      <div class="box">
        <div class="box-header">
          <i class="fa fa-search" aria-hidden="true"></i>
          <h3 class="box-title">View Details</h3>
        </div>
        <div class="box-body" id="example12">

          <div id="load2" style="display:none;"></div>
          <!-- <div id="example12"> -->
          <div class="table-responsive">
          <table id="example1" class="table table-bordered table-striped">
            <thead>
              <tr style="background-color:#39cccc !important; color:white">
                <th>#</th>
                <th>Employee ID</th>
                <th>Employee Name</th>
                <th>Designation</th>
                <th>Advance Date</th>
                <th>Retutn Date</th>
                <th>Advance amount</th>
                <th>Amount Returned</th>
                <th>Balance</th>


              </tr>
            </thead>
            <tbody id="example2" class="updt">

            </tbody>

          </table>
</div>

          <!-- </div> -->
        </div>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
  //prepare the dialog

  //respond to click event on anything with 'overlay' class
  $("#depform").click(function(event) {

    //$('.modal-content-drop-out').html('');
    //load content from href of link
    $('.modal-content-drop-out').load($(this).attr("href"));

  });
});
</script>
<script inline="1">
$(document).ready(function() {

  $("#sevice_form1").bind("submit", function(event) {

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
      url: "<?php echo ADMIN_URL; ?>report/advance_report_search",

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