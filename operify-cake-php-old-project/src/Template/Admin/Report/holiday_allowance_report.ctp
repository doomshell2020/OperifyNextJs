<script type="text/javascript"
  src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>
<link rel="stylesheet"
  href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/css/bootstrap-datepicker3.css" />

<div class="content-wrapper" style="min-height:943px">
  <section class="content-header">
    <h1 style="margin-bottom:10px !important"><i class="fa fa-money"></i>Holiday Allowance Report</h1>

  </section>

  <?php echo $this->Flash->render(); ?>
  <div class="row">
    <div class="col-sm-12">
      <div class="box">

        <?php echo $this->Form->create($enquires, array('class' => '', 'id' => 'sevice_form1', 'enctype' => 'multipart/form-data', 'validate', 'autocomplete' => 'off')); ?>

        <div class="box-body">
          <div class="row">

            <div class="col-sm-2">
              <label>Date<span style="color:red;">*</span></label>
              <?php echo $this->Form->input('fdate', array('class' =>
    'longinput form-control from', 'value' => date('m/Y', strtotime('-1 month')), 'placeholder' => 'Year', 'id' => 'dt-frm', 'label' => false, 'style' => array('margin-bottom:0px'))); ?>

              <script>
              $('.from').datepicker({
                autoclose: true,
                minViewMode: 1,
                format: 'mm/yyyy'
              }).on('changeDate', function(selected) {
                startDate = new Date(selected.date.valueOf());
                startDate.setDate(startDate.getDate(new Date(selected.date.valueOf())));
                $('.to').datepicker('setStartDate', startDate);
              });
              </script>


            </div>



            <div class="row">

              <div class="col-sm-6" style="padding-top:18px">
                <?php echo $this->Form->submit('SEARCH', array('class' => 'btn btn-info pull-right', 'style' => '', 'title' => 'Submit'));

echo $this->Form->end(); ?>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>


    <div class="search" id="src-rslt" style="display:none">
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
                  <th> Date</th>
                  <th>Action</th>

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
        url: "<?php echo ADMIN_URL; ?>report/holiday_allowance_search",

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
