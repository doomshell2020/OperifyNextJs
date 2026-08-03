<head>

  <style>
    .ui-autocomplete {
      max-height: 100px;
      overflow-y: auto;
      /* prevent horizontal scrollbar */
      overflow-x: hidden;
    }

    /* IE 6 doesn't support max-height
     * we use height instead, but this forces the menu to always be this tall
     */
    * html .ui-autocomplete {
      height: 100px;
    }
  </style>

</head>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

  <section class="content-header">
    <h1>
      <i class="fa fa-book"></i>
      Issued Books Report
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo ADMIN_URL; ?>dashboards"><i class="fa fa-home"></i>Home</a></li>
      <li><a href="<?php echo ADMIN_URL; ?>report/issuedbooksreport">Manage Issued Books Report</a></li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">

    <!-- start -->
    <div class="row">
      <div class="col-xs-12">

        <div class="box">

          <div class="box-header">
            <h3 class="box-title">
              <i class="fa fa-search"></i> Search Book
            </h3>
          </div>
          <!-- /.box-header -->

          <script type="text/javascript">
            $(document).ready(function() {
              $('#hol_id').on('change', function() {
                var d = $(this).val();
                //alert(d);
                if (d == 'Student') {
                  $('.fg').show();


                } else {
                  $('.fg').hide();
                }

              });

            });
          </script>

          <div class="box-body">


            <div class="manag-stu">

              <?php echo $this->Form->create(null, array('class' => 'form-horizontal', 'id' => 'TaskAdminCustomerForm')); ?>

              <div class="form-group">

                <div class="col-sm-3">
                  <label>ISBN No.</label>
                  <?php echo $this->Form->input('isbn_no', array('class' => 'form-control', 'label' => false)); ?>
                </div>

                <div class="col-sm-3">
                  <label>Book Name</label>
                  <?php echo $this->Form->input('b_name', array('class' => 'form-control', 'id' => 'tags', 'label' => false)); ?>
                </div>

                <div class="col-sm-3">
                  <label>Holder Type</label>
                  <?php
                  echo $this->Form->input('holder_type_id', array(
                    'class' => 'form-control', 'type' => 'select', 'id' => 'hol_id',
                    'options' => $holder_type, 'value' => 'Student', 'label' => false
                  ));
                  ?>
                </div>

                <div class="col-sm-3">
                  <label>Language</label>

                  <?php echo $this->Form->input('langu', array('class' => 'form-control f', 'type' => 'select', 'options' => $lahu, 'empty' => 'Select Language', 'label' => false)); ?>
                </div>

              </div>

              <script>
                $(document).ready(function() {
                  $('#tags').on('keyup', function() {


                    var b_name = $('#tags').val();
                    //alert(b_name);
                    //alert(h_type);
                    $.ajax({

                      type: 'POST',

                      url: '<?php echo ADMIN_URL; ?>Issuebooks/autobookfinder',

                      data: {
                        'b_name': b_name
                      },

                      dataType: "json",

                      success: function(data) {
                        //alert(data);
                        $("#tags").autocomplete({

                          source: data,

                          change: function(event, ui) {
                            val = $(this).val();
                            exists = $.inArray(val, data);
                            if (exists < 0) {
                              $(this).val("");
                              return false;
                            }
                          }

                        });
                      },

                    });

                  });

                  $('#hol_id').on('change', function() {

                    $("#klo").val('');
                    //alert($("#tags").val());

                    var h_type = $('#hol_id').val();
                    //alert(h_type);
                    $.ajax({

                      type: 'POST',

                      url: '<?php echo ADMIN_URL; ?>Issuebooks/autocompleteList',

                      data: {
                        'h_type': h_type
                      },

                      dataType: "json",

                      success: function(data) {
                        //  alert(data);
                        $("#klo").autocomplete({

                          source: data,

                          change: function(event, ui) {
                            val = $(this).val();
                            exists = $.inArray(val, data);
                            if (exists < 0) {
                              $(this).val("");
                              return false;
                            }
                          }

                        });
                      },

                    });

                  });

                  var h_type = $('#hol_id').val();
                  $.ajax({
                    type: 'POST',
                    url: '<?php echo ADMIN_URL; ?>Issuebooks/autocompleteList',
                    data: {
                      'h_type': h_type
                    },
                    dataType: "json",
                    success: function(data) {
                      //alert(data);
                      $("#klo").autocomplete({

                        source: data,

                        change: function(event, ui) {
                          val = $(this).val();
                          exists = $.inArray(val, data);
                          if (exists < 0) {
                            $(this).val("");
                            return false;
                          }
                        }

                      });
                    },

                  });


                });
              </script>
              </script>
              <style>
                #load2 {
                  width: 100%;
                  height: 100%;
                  position: fixed;
                  z-index: 9999;
                  background-color: white !important;
                  background: url("<? echo SITE_URL; ?>images/Preloader_2.gif") no-repeat center center rgba(0, 0, 0, 0.75)
                }
              </style>
              <div class="form-group">

                <div class="col-sm-3">
                  <label>Holder Name</label>
                  <?php
                  echo $this->Form->input('holder_name', array('class' => 'form-control', 'placeholder' => 'Enter Name/ID', 'id' => 'klo', 'label' => false));
                  ?>
                </div>


                <div class="col-sm-3">
                  <label>Issue Date</label>
                  <?php echo $this->Form->input('issue_date', array('class' => 'form-control', 'placeholder' => 'Issue Date', 'id' => 'fendfrom0', 'label' => false)); ?>
                </div>

                <div class="col-sm-3">
                  <label>Due Date</label>
                  <?php echo $this->Form->input('due_date', array('class' => 'form-control', 'placeholder' => 'Due Date', 'id' => 'fendfrom01', 'label' => false)); ?>
                </div>

                <div class="col-sm-3">
                  <label>Book Type</label>

                  <?php $df = array('1' => 'Periodical');
                  echo $this->Form->input(
                    'type',
                    array(
                      'class' => 'form-control f', 'type' => 'select', 'empty' => 'Select Book Type',
                      'options' => $df, 'label' => false
                    )
                  );
                  ?>
                </div>


              </div>

              <div class="form-group">

                <div class="col-sm-3 fg">
                  <label>Select Class</label>
                  <?php echo $this->Form->input('class_id', array('class' => 'form-control', 'type' => 'select', 'empty' => 'Select Class', 'options' => $classes, 'label' => false)); ?>
                </div>
                <div class="col-sm-3 fg">
                  <label>Select Section</label>
                  <?php echo $this->Form->input('section_id', array('class' => 'form-control', 'type' => 'select', 'empty' => 'Select Section', 'options' => $sectionslist, 'label' => false)); ?>
                </div>
                <div class="col-sm-3">
                  <label>Status</label>
                  <?php
                  echo $this->Form->input(
                    'status',
                    array(
                      'class' => 'form-control f', 'type' => 'select', 'empty' => 'Select Book Status',
                      'options' => $b_status, 'label' => false
                    )
                  );
                  ?>
                </div>

              </div>

              <div class="form-group">

                <div class="col-sm-3">

                  <script>
                    $(document).ready(function() {
                      $('#fdatefrom').datepicker({
                        dateFormat: 'yy-mm-dd',
                        yearRange: '2018:2030',
                        changeMonth: true,
                        changeYear: true,
                        onSelect: function(date) {

                          var selectedDate = new Date(date);
                          var endDate = new Date(selectedDate);
                          endDate.setDate(endDate.getDate());

                          $("#fendfrom").datepicker("option", "minDate", endDate);
                          $("#fendfrom").val(date);
                        }

                      });


                      $('#fdatefrom').datepicker('setDate', 'today');

                      $('#fendfrom').datepicker({
                        dateFormat: 'yy-mm-dd'
                      });

                      $('#fendfrom0').datepicker({
                        dateFormat: 'yy-mm-dd'
                      });
                      $('#fendfrom01').datepicker({
                        dateFormat: 'yy-mm-dd'
                      });
                      $('#fendfrom').datepicker('setDate', 'today');
                    });
                  </script>
                  <label>Issue From</label>
                  <?php echo $this->Form->input('from', array('class' => 'form-control', 'placeholder' => 'Issue Date From', 'id' => 'fdatefrom', 'label' => false)); ?>
                </div>
                <div class="col-sm-3">
                  <label>Issue Date To</label>
                  <?php echo $this->Form->input('to', array('class' => 'form-control', 'placeholder' => 'Issue Date To', 'id' => 'fendfrom', 'label' => false)); ?>
                </div>


              </div>

              <div class="form-group">

                <div class="col-sm-12">

                  <button type="submit" class="btn btn-success sear">Search</button>&nbsp;
                  <button type="reset" class="btn btn-primary">Reset</button>

                </div>

              </div>

              <?php echo $this->Form->end(); ?>

            </div>

          </div>

        </div>
      </div>
    </div>

    <script>
      $(document).ready(function() {
        $('#class-id').on('change', function() {
          var id = $('#class-id').val();
          //alert(id);
          $.ajax({
            type: 'POST',
            url: '<?php echo ADMIN_URL; ?>Report/find_section',
            data: {
              'id': id
            },
            success: function(data) {

              $('#section-id').empty();
              $('#section-id').html(data);
            },

          });
        });
      });
    </script>
    <!-- end -->
    <script type="text/javascript">
      $(document).ready(function() {
        $('.sear').click(function() {

          var id = $('#class-id').val();
          var sid = $('#section-id').val();
          if (id == '') {
            return true;
          } else {

            if (sid == '') {
              alert("Please select section.")
              return false;
            } else {
              return true;
            }

          }


        });

      });
    </script>

    <div class="row">

      <div class="col-xs-12">

        <div class="box" style="display: none;" id="appear-box">

          <div class="box-header">

            <h3 class="box-title"><i class="fa fa-eye"></i> View Books</h3>

            <a id='ck' style="position: absolute; top: 10px; right: 16px;" class="btn btn-info btn-sm pull-right" href="<?php echo ADMIN_URL; ?>report/excelExportIssuedBooks">
              <i class="fa fa-file-excel-o" aria-hidden="true"></i> Export Excel
            </a>

          </div>
          <!-- /.box-header -->
          <div id="load2" style="display:none;"></div>
          <div class="box-body" style="overflow-x:auto;">
            <div class="table-responsive">
              <table class="table table-bordered table-striped">

                <thead>

                  <tr>
                    <th>#</th>
                    <th>Acc. No.</th>
                    <th>Book Name</th>
                    <th>ISBN No.</th>
                    <th style="width: 108px;">Holder Name</th>
                    <th>Holder Type</th>
                    <th class="fg">Class-Section</th>
                    <th>Language</th>
                    <th>Contact No.</th>
                    <th>Issue Date</th>
                    <th>Due Date</th>
                    <th>Duration</th>
                  </tr>

                </thead>

                <tbody id="srch-rslt">
                  <!-- search result will be loaded here using AJAX -->
                </tbody>

              </table>
            </div>
          </div>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </div>
      <!-- /.col -->

    </div>
    <!-- /.row -->

  </section>
  <!-- /.content -->
</div>

<!-- /.content-wrapper -->


<!-- custom search script: start -->

<script>
  $(document).ready(function() {

    $("#TaskAdminCustomerForm").bind("submit", function(event) {
      $.ajax({
        async: true,
        type: "POST",
        url: "<?php echo ADMIN_URL; ?>Report/searchIssuedBook",
        data: $("#TaskAdminCustomerForm").serialize(),
        dataType: "html",
        beforeSend: function() {
          // setting a timeout
          $('#load2').css("display", "block");
        },
        success: function(data) {
          // alert(data);
          $("#srch-rslt").html(data);
          $("#appear-box").show();

        },
        complete: function() {
          $('#load2').css("display", "none");
        },

      });

      return false;

    });

  });
</script>

<!-- custom search script: end -->


<!-- Holder name autocomplete script: start -->

<script>
  $(document).ready(function() {

    $('#holder-type-id').on('change', function() {

      $("#holder-name").val('');

      var h_type = $('#holder-type-id').val();
      //alert(h_type);

      $.ajax({

        type: 'POST',

        url: '<?php echo ADMIN_URL; ?>Report/autocompleteList',

        data: {
          'h_type': h_type
        },

        dataType: "json",

        success: function(data) {
          // alert(data);
          $("#holder-name").autocomplete({

            source: data,

            change: function(event, ui) {
              val = $(this).val();
              exists = $.inArray(val, data);
              if (exists < 0) {
                $(this).val("");
                return false;
              }
            }

          });
        }

      });

    });

  });
</script>

<!-- Holder name autocomplete script: end -->