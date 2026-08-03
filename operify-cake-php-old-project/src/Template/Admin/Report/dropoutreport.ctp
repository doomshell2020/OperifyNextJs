<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
  $(document).ready(function() {

    $("#TaskAdminCustomerForm").bind("submit", function(event) {

      $.ajax({

        async: true,

        type: "POST",

        url: "<?php echo ADMIN_URL; ?>report/drop_out_student_search",

        data: $("#TaskAdminCustomerForm").serialize(),

        dataType: "html",
        beforeSend: function() {
          // setting a timeout
          $('#load2').css("display", "block");
        },
        success: function(data) {
          $("#example2").html(data);
        },
        complete: function() {
          $('#load2').css("display", "none");
        },

      });

      return false;

    });

  });
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
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Drop Out Report
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo ADMIN_URL; ?>studentfees/view"><i class="fa fa-home"></i>Home</a></li>
      <li><a href="<?php echo ADMIN_URL; ?>report/dropoutreport">Manage Drop Out Report</a></li>
      <li><a href="#" class="active">Drop Student</a></li>

    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-xs-12">

        <div class="box">
          <div class="box-header">
            <i class="fa fa-search" aria-hidden="true"></i>
            <h3 class="box-title">Search Drop Student</h3>

          </div>
          <!-- /.box-header -->

          <div class="box-body">

            <div class="manag-stu">

              <?php echo $this->Form->create('Task', array('url' => array('controller' => 'students', 'action' => 'search'), 'type' => 'file', 'inputDefaults' => array('div' => false, 'label' => false), 'id' => 'TaskAdminCustomerForm', 'class' => 'form-horizontal')); ?>

              <div class="form-group margin_btmcol">



                <div class="col-md-4 col-sm-6 col-xs-12">
                  <label>Class</label>
                  <select class="form-control" name="class_id">
                    <option value="">Select Class</option>
                    <?php foreach ($classes as $esr => $es) { ?>
                      <option value="<?php echo $esr; ?>"><?php echo $es; ?></option>
                    <?php } ?>
                  </select>
                </div>



                <div class="col-md-4 col-sm-6 col-xs-12">
                  <label>Section</label>
                  <select class="form-control" name="section_id">

                    <option value=""> Select Section </option>
                    <?php foreach ($sections as $er => $e) { ?>
                      <option value="<?php echo $e['id']; ?>"><?php echo $e['title']; ?></option>
                    <?php } ?>
                  </select>
                </div>
                <div class="col-md-4 col-sm-6 col-xs-12">
                  <label>Academic Year</label>
                  <?php echo $this->Form->input('acedmicyear', array('class' => 'form-control', 'type' => 'select', 'options' => $academic1, 'empty' => 'Select Year', 'label' => false)) ?>
                </div>

                <div class="col-md-4 col-sm-6 col-xs-12 datepc1">

                  <label for="inputEmail3" class="control-label">From Dropped Date</label>
                  <?php echo $this->Form->input('d1', array('class' => 'form-control ', 'placeholder' => 'From Date', 'id' => 'date1', 'value' => '', 'label' => false)); ?>
                </div>
                <div class="col-md-4 col-sm-6 col-xs-12 datepc2">
                  <label>To Dropped Date</label>
                  <?php echo $this->Form->input('d2', array('class' => 'form-control ', 'placeholder' => 'To Date', 'id' => 'date2', 'label' => false)); ?>
                </div>
                <script>
                  $('#date1').datepicker({
                    dateFormat: 'yy-mm-dd',
                    minDate: '',
                    maxDate: '+0',
                    onSelect: function(dateStr) {
                      var min = $(this).datepicker('getDate') || new Date(); // Selected date or today if none
                      var max = new Date();

                      //max.setMonth(max.getMonth() + 60); // Add one month
                      $('#date2').datepicker('option', {
                        minDate: min,
                        maxDate: max
                      });
                    },
                  }).attr("readonly", "readonly");
                  $('#date2').datepicker({
                    dateFormat: 'yy-mm-dd',
                    minDate: '',
                    maxDate: '+0',
                    autoclose: true,
                    onSelect: function(dateStr) {
                      var max = $(this).datepicker('getDate') || new Date(); // Selected date or null if none
                      $('#date1').datepicker('option', {
                        maxDate: max
                      });
                    },
                  }).attr("readonly", "readonly");
                </script>
                <div class="col-md-4 col-sm-6 col-xs-12">
                  <label for="inputEmail3" class="control-label">Select Status</label>
                  <div style="display:flex; align-items:center;">
                    <label style="margin-right:10px;"><input type="radio" name="status" checked="checked" value="Both"> Both</label>
                    <label style="margin-right:10px;"><input type="radio" name="status" value="N"> L.W.T.C</label>
                    <label style="margin-right:10px;"><input type="radio" name="status" value="Y"> W.T.C</label>
                  </div>

                </div>
              </div>

              <div class="form-group">
                <div class="col-sm-12">
                  <button type="submit" class="btn btn-success">Search</button>
                  <button type="reset" class="btn btn-primary">Reset</button>
                </div>
              </div>
              <?php
              echo $this->Form->end();
              ?>

            </div>

          </div>

        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-xs-12">

        <div class="box">
          <div class="box-header">
            <i class="fa fa-search" aria-hidden="true"></i>
            <h3 class="box-title">Student List</h3>
          </div>
          <!-- /.box-header -->
          <?php echo $this->Flash->render(); ?>
          <div class="box-body">
            <div id="load2" style="display:none;"></div>
            <div class="table-responsive">
              <table id="" class="table table-bordered table-striped">


                <thead>
                  <tr>
                    <th>#</th>
                    <th>Pupil Name</th>
                    <th>Father Name</th>
                    <th>Last Studied </th>
                    <th>Class</th>
                    <th>Drop Out Date</th>
                    <th>Application Date</th>
                    <th>Issue Date</th>
                    <th> Paid Upto</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody id="example2">
                <!-- <tr>
   	<td><a id="" style="position: absolute;
top: -83px;
/* right: 0px; */
right: 46px;" class="btn btn-info btn-sm pull-right" target="_blank" href="<?php echo ADMIN_URL; ?>report/dropoutreports"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Export Excel</a></td>
   </tr> -->
                  <?php
                  $page = $this->request->params['paging']['DropOutStudent']['page'];
                  $limit = $this->request->params['paging']['DropOutStudent']['perPage'];
                  $counter = ($page * $limit) - $limit + 1;

                  if (isset($students) && !empty($students)) {
                    foreach ($students as $work) {
                      // pr($work);die;
                  ?>

                      <tr>
                        <td><?php echo $counter; ?></td>


                        <td style="font-size: 11px;"><a title="View Drop Out Student" href="<?php echo SITE_URL; ?>admin/students/editdropout/<?php echo $work['id']; ?>">
                            <?php
                            $name = $work['fname'] . ' ';

                            if (!empty($work['middlename']))
                              $name .= $work['middlename'] . ' ';

                            echo $name .= $work['lname'];
                            ?> (<?php echo $work['enroll']; ?>)
                          </a> </td>
                        <td style="font-size: 11px;"><?php echo $work['fathername']; ?></td>

                        <?php $class = $this->Comman->findclass($work['laststudclass']); ?>
                        <td style="font-size: 11px;"><?php echo $class['title']; ?></td>
                        <td style="font-size: 11px;"><?php echo $work['class']['title']; ?>-<?php echo $work['section']['title']; ?></td>

                        <td><?php echo date('d-m-Y', strtotime($work['dropcreated'])); ?></td>
                        <td><?php if (date('d-m-Y', strtotime($work['date_application'])) != '01-01-1970') {
                              echo date('d-m-Y', strtotime($work['date_application']));
                            } else {
                              echo '--';
                            } ?></td>
                        <td><?php if (date('d-m-Y', strtotime($work['date_issue'])) != '01-01-1970') {
                              echo date('d-m-Y', strtotime($work['date_issue']));
                            } else {
                              echo '-';
                            } ?></td>
                        <?php

                        $ec = $this->Comman->findfeesmonth34($work['s_id']);

                        $ec2 = $this->Comman->findfeesmonth342($work['s_id']);
                        // pr($work['s_id']);die;

                        $quuar = unserialize($ec['quarter']);
                        $qra = array();
                        foreach ($quuar as $h => $rt) {
                          $qra[] = $h;
                        }
                        // pr($ec2);die;
                        if (!empty($qra)) {

                          $monthupto = date('M Y', strtotime($ec['paydate']));
                          $monthupto23 = date('M Y', strtotime($ec2['paydate']));
                        } else {
                        }

                        // echo $monthupto23;die;

                        if (in_array('Quater4', $qra) && in_array('Quater3', $qra) && in_array('Quater2', $qra) && in_array('Quater1', $qra)) {

                          $monthuptosa = 'Mar';
                        } else {

                          $monthuptos = date('M', strtotime($ec['paydate']));
                          $monthuptosyy = date('Y', strtotime($ec['paydate']));
                        }
                        $e1 = array('Apr', 'May', 'Jun');
                        $e2 = array('Jul', 'Aug', 'Sep');
                        $e3 = array('Oct', 'Nov', 'Dec');
                        $e4 = array('Jan', 'Feb', 'Mar');

                        // pr($monthuptos);die;


                        if (in_array('Quater4', $qra)) {
                          $monthupto = 'Mar ' . date('Y');
                        } else if (in_array($monthuptos, $e1)) {
                          $monthupto = 'Jun ' . date('Y', strtotime($ec['paydate']));
                          // pr($monthupto);die;
                        } else if (in_array($monthuptos, $e2)) {
                          $monthupto = 'Sep ' . date('Y', strtotime($ec['paydate']));
                        } else if (in_array($monthuptos, $e3)) {
                          $monthupto = 'Dec ' . date('Y', strtotime($ec['paydate']));
                        } else if (in_array($monthuptos, $e4)) {
                          $monthupto = 'Mar ' . date('Y', strtotime($ec['paydate']));
                        } else if ($monthuptosa) {
                          $monthupto = 'Mar ' . date('Y');
                        }
                        ?>
                        <td><?php if ($work['month']) {
                              echo $work['month'];
                            } else if ($monthupto != "Mar 1970") {
                              echo $monthupto;
                            } else if ($monthupto23 != "Mar 1970") {
                              echo $monthupto23;
                            } else {
                              echo "N/A";
                            } ?></td>

                        <td>
                          <? if ($work['status_tc'] == "N") {  ?>

                            <i class="fa fa-clock-o" aria-hidden="true" style="font-size: 22px;color:red;"></i>&nbsp;<b>L.W.T.C</b>

                          <? } else { ?>

                            <i class="fa fa-check-circle" aria-hidden="true" style="font-size: 22px;color:#3c8dbc;"></i>&nbsp;<b>W.T.C</b>

                          <? } ?>
                        </td>

                      </tr>
                    <?php $counter++;
                    }
                  } else { ?>
                    <tr>
                      <td>NO Data Available</td>
                    </tr>

                  <?php } ?>
                </tbody>

              </table>
            </div>
          </div>
          <!-- /.box-body -->
        </div>
      </div>
      <!-- /.box -->
    </div>
    <!-- /.col -->
</div>

<div class="modal" id="globalModal" tabindex="-1" role="dialog" aria-labelledby="esModalLabel" aria-hidden="true" style="display: none;">
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


<div class="modal" id="globalModals" tabindex="-1" role="dialog" aria-labelledby="esModalLabel" aria-hidden="true" style="display: none;">
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

<script>
  $(document).ready(function() {

    $(".global").click(function(event) {
      //load content from href of link
      $('.modal-content').load($(this).attr("href"));
    });

  });
</script>