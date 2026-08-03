<script type="text/javascript"
  src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>
<link rel="stylesheet"
  href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/css/bootstrap-datepicker3.css" />
<style>
  #load2 {
  width: 100%;
  height: 100%;
  position: fixed;
  z-index: 9999;
  top: 32%;
  background-color: white !important;
  background: url("<?echo SITE_URL; ?>images/Preloader_2.gif") no-repeat center center rgba(0, 0, 0, 0.75)
  }
  .isDisabled {
  color: currentColor;
  cursor: not-allowed;
  opacity: 0.5;
  text-decoration: none;
  pointer-events: none;
  }
  td {
  font-size: 12px;
  }
  td a {
  font-size: 11px;
  text-decoration: underline;
  font-weight: 400;
  }
  #load2 {
  width: 100%;
  height: 100%;
  position: fixed;
  z-index: 9999;
  background-color: white !important;
  background: url("<?echo SITE_URL; ?>images/Preloader_2.gif") no-repeat center center rgba(0, 0, 0, 0.75)
  }
  .table td {
  font-size: 11px;
  }
  .table th {
  font-size: 11px;
  }
</style>
<div class="content-wrapper">
  <section class="content-header">
    <h1 style="margin-bottom:10px !important">Staff Attendance Report </h1>
    <?php echo $this->Flash->render(); ?>
  </section>
  <?php if (empty($date)) {$date = date('m-Y', strtotime("-0 months"));}
  // if (empty($date)) {$date = date('m-Y', strtotime("-1 months"));}
    $month = $date;
    ?>
  <div id="load2" style="display:none;"></div>
  <section class="content">
    <div class="row">
      <div class=" col-sm-12">
        <div class="box">
          <div class="box-header">
            <h4 class="box-title" style="margin:0px !important">Report Search</h4>
          </div>
          <div class="box-body">
            <?php echo $this->Form->create('', array('class' => '', 'id' => 'sevice_form1', 'enctype' => 'multipart/form-data', 'validate', 'autocomplete' => 'off')); ?>
            <div class="row">
              <div class="col-sm-4">
                <label class="control-label">Date:<span style="color:red;">*</span></label>
                <?php echo $this->Form->input('date', array('class' => 'longinput form-control from', 'maxlength' => '20', 'placeholder' => 'Year', 'id' => 'dt-frm', 'label' => false, 'required', 'value' => $date, 'data-date-end-date' => '1m')); ?>
              </div>
              <script>
                $('.from').datepicker({
                    autoclose: true,
                    minViewMode: 1,
                    format: 'mm-yyyy'
                });
              </script>
                <?php echo $this->Form->submit('Submit', array('class' => 'btn btn-info', 'style' => 'margin-top: 25px;', 'id' => 'emp_attn_sub', 'title' => 'Submit'));
            echo $this->Form->end(); ?>
          </div>
        </div>

        <div id="src-rslt">
            <div id="updt">
              
              <div class="box-body">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <h4 class="box-title">Search Attendance Report(<?php echo $date ?>)</h4>
                    <a href="<?php echo ADMIN_URL; ?>Report/employee_monthly_report/<?php echo $date ?>"
                      class="btn btn-info"><i class="fas fa-file-excel"></i> Export To Excel</a>
                </div>
                <div class="table-responsive">
                  <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                          <th>#</th>
                          <th>Teacher's Name</th>
                          <th style="color:red">CL</th>
                          <th style="color:red">PL</th>
                          <?php  $mon = date('m', $date);
                              $d_date = $date;
                              $date1 = explode('-', $date);
                              $mon = $date1[0];
                              $year = $date1[1];
                              $days = 1;
                              $startdate = $year . '-' . $mon . '-01';
                              $start_time = strtotime($startdate);
                              $end_time = strtotime("+1 month", $start_time);
                              $date_range = array();
                              for ($i = $start_time; $i < $end_time; $i += 86400) {
                                $dayOfWeek = date("l", $i);
                                $pre_date = date('Y-m-d', $i);
                                $holiday = $this->Comman->findholidaymonth($pre_date);
                                //pr($holiday);
                                if ($dayOfWeek != 'Sunday' && $holiday == 0) {?>
                          <th> <?php echo $days; ?></th>
                          <?php $date_range[] = $pre_date;} else {if ($dayOfWeek == 'Sunday') { ?>
                          <th><span style="color:red">S</span><br>
                              <?php echo $days; ?>
                          </th>
                          <?php
                              $date_range[] = 'sun';
                              } else if ($holiday > 0) {
                              ?>
                          <th><span style="color:red">H</span><br>
                              <?php echo $days; ?>
                          </th>
                          <?php
                              //$date_range[] = 'hol';
                                $date_range[] = 'hol';
                                }
                              }
                              ?>
                          <?php $days++;}
                              //pr($date_range);
                              ?>
                          <th style="color:red">AB.</th>
                          <th style="color:red">LWP</th>
                        </tr>
                    </thead>
                    <?php //$date = date('d-m-Y');
                        //pr($students);die;?>
                    <tbody id="example2">
                      <?php $count = 1;
                        foreach ($dept as $dept_val) {
                          $emp = $this->Comman->empByPId($dept_val['id'],$mon,$year);
                          // pr($dept_val['id']);die;
                      ?>
                      <tr>
                        <td colspan="5"><b>
                            <?php echo $dept_val['name']; ?></b>
                        <td>
                      </tr>
                      <?php foreach ($emp as $emp_values) {
                        $leave_count=$this->Comman->leavecount($emp_values['id'],$acdfrom,date('Y-m-t',$start_time));
                        $j_day = 0;
                        $drop_day=0;
                        $join_month = date("m-Y", strtotime($emp_values['joiningdate']));

                        if (strtotime('1-' . $date) == strtotime('1-' . $join_month)) {
                            $j_day = date("d", strtotime($emp_values['joiningdate']));
                        } else if (strtotime('1-' . $date) <= strtotime('1-' . $join_month)) {
                            $j_day = cal_days_in_month(CAL_GREGORIAN, $mon, $year);
                        }
                        $drop_date=date('m-Y',strtotime($emp_values['drop_date']));
                        if(strtotime('1-' . $date)==strtotime('1-' . $drop_date)){
                          $drop_day=date('d',strtotime($emp_values['drop_date']));
                        }
                      ?>
                      <tr class="row<?php echo $emp_values['id']?>">
                        <td><?php echo $count ?></td>
                        <td>
                            <a id="absent-data" style="text-decoration: none;"
                              class="absent <?php echo $emp_values['id'] ?>"
                              data-d_mon="<?php echo $mon; ?>"
                              data-d_year="<?php echo $year; ?>"
                              data-sno="<?php echo $count; ?>" href="javascript:void(0)"
                              title="Mark Absent"
                              data-id="<?php echo $emp_values['id'] ?>"><b>
                            <?php echo strtoupper($emp_values['fname']) . "\x20" . strtoupper($emp_values['middlename']) . "\x20" . strtoupper($emp_values['lname']); ?></b></a>
                        </td>
                        <td><?php echo $emp_values['CL']-$emp_values['CL_avail']; ?></td>
                        <td><?php echo $emp_values['PL']-$emp_values['PL_avail']; ?></td>
                        <?php
                            $sal_exist = $this->Comman->salExist($emp_values['id'], $mon, $year);
                            $P = 0;
                            $PR = 0;
                            $PL = 0;
                            $A = 0;
                            $d_cnt = 1;
                            $t_hol = 0;
                            $L = 0;
                            $d_cnt = 1;
                            //pr($date_range);die;
                            $i = 1;
                            foreach ($date_range as $date_val) {
                              // pr($date_val);
                                if ($d_cnt <= $j_day) {?>
                        <td>-</td>
                        <?php $d_cnt++;
                            continue;}
                            $p_date = $d_cnt . "-" . $d_date;
                            if (strtotime($p_date) > strtotime(date('d-m-Y'))) {?>
                        <td>-</td>
                        <?php $d_cnt++;
                            continue;
                            }
                            if($drop_day>0 && $d_cnt>$drop_day){ ?>
                        <td>-</td>
                        <?php $d_cnt++;
                            $A += 1;
                            continue;
                            }
                            $status = '';
                            $emp_atn = '';
                            if ($date_val != 'hol' && $date_val != 'sun') {
                            // pr($emp_values['id']);
                            $emp_atn = $this->Comman->findEmp_Att_bydate($emp_values['id'],$date_val);
                            // $emp_atn = $this->Comman->findEmp_Attendance_bydate($emp_values['id'],$date_val);                           

                            if (!empty($emp_atn)) {
                              $status = $emp_atn['status'];
                              if ($status == 'A') {
                                  $A += 1;
                              } else if ($status == 'HF') {
                                  $PR += 0.5;
                              } else if ($status == 'SL') {
                                  $PL += 0.25;
                              }
                            } else { ?>

                        <td style="color:green"><a id="absent-data"
                            class="absent <?php echo $emp_values['id'] ?>"
                            href="javascript:void(0)" title="Mark Absent"
                            style="color:green" data-id="<?php echo $emp_values['id'] ?>"
                            data-val="<?php echo date('d-m-Y', strtotime($p_date)); ?>"
                            data-sno="<?php echo $count; ?>">P</a>
                        </td>

                        <?php } if ($status == 'A' || $status == 'HF' || $status == 'SL') {
                                $leave = $this->Comman->leaveStatus($date_val, $emp_values['id']); ?> 

                        <td><?php if (!empty($leave)) {if (in_array($leave['leave_type'],$leave_id)) { ?> <a
                            id="update-data"
                            class="del_emp_data <?php echo $emp_values['id'] ?>"
                            href="javascript:void(0)" title="Delete Leave" style="color:red"
                            data-val="<?php echo $leave['id']; ?>"><?php echo $leave['leavetype']['short_name']; ?></a><?php
                            if ($status == 'A') {
                                  $L += 1;
                              } else {
                                  $L += 0.5;
                              }
                            } else { ?> <a id="update-data" class="del_emp_data <?php echo $emp_values['id'] ?>"
                            href="javascript:void(0)" title="Delete Leave" style="color:red"
                            data-val="<?php echo $leave['id']; ?>"><?php echo $status; ?></a><?php } } else { ?>

                            <a id="update-data" data-ab="<?php echo $emp_atn['id']; ?>"
                               class="del_emp_leave <?php echo $emp_values['id'] ?>"
                            href="javascript:void(0)" title="Delete Leave" style="color:red"
                              data-val="<?php echo $emp_atn['id'];?>"
                              data-sno="<?php echo $count; ?>"><?php echo $status; } ?>
                            </a>

                            <!-- <a id="update-data" data-ab="<?php //echo $emp_atn['id']; ?>"
                            class="<?php //echo $emp_values['id'] ?> globalModals11"
                            href="javascript:void(0)" title="Add Leave" style="color:red"
                            data-id="<?php //echo $emp_values['id'] ?>"
                            data-target="#globalModalkoi11" data-toggle="modal"
                            data-val="<?php //echo date('Y-m-d', strtotime($p_date)); ?>"
                            data-sno="<?php //echo $count; ?>"><?php //echo $status; } ?>
                            </a> -->

                        </td>

                        <?php } } else {
                            $payroll=$this->payroll();
                            if($payroll['holiday_allow']==1){
                              $hol_exist = array();

                              $hol_exist = $this->Comman->findhol_allow($emp_values['id'], $p_date);
                              if (empty($hol_exist)) { ?>

                        <td><a id="update-data" class="<?php echo $emp_values['id'] ?>"
                            href="<?php echo SITE_URL; ?>admin/payroll/holiday_allowance/<?php echo $emp_values['id'] ?>/<?php echo $d_cnt . "-" . $d_date; ?>"
                            title="HOLIDAY ALLOWANCE" data-target="#globalModalkoi"
                            data-toggle="modal" class="globalModals"
                            data-modal-size="modal-lg" style="color:red">H </a>                          
                        </td>

                        <?php } else {?>
                          
                        <td><a id="update-data"
                            class="<?php echo $emp_values['id'] ?> del_hol_data"
                            href="javascript:void(0)" title="DELETE ALLOWANCE"
                            style="color:green" data-val="<?php echo $hol_exist['id']; ?>">H(<?php if ($hol_exist['type'] == 'full') {echo 1;
                            $t_hol += 1;} else {echo 0.5;
                            $t_hol += 0.5;}?>)</a></td>
                        <?php //if ($hol_exist['type'] == 'full') {$t_hol += 1;} else { $t_hol += 0.5;}
                            }
                            } else{ ?>
                        <td> <span style="color:red">H</span> </td>
                        <?php }
                            }?>
                        <?php $d_cnt++;
                            $i++;
                            }?>
                        <td style="color:red"><?php echo $A + $PR + $PL; ?></td>
                        <td style="color:blue"> <a id="update-data"
                            data-ab="<?php echo $emp_atn['id']; ?>"
                            class="<?php echo $emp_values['id'] ?> LWPModal"
                            href="javascript:void(0)" title="ADD LWP" style="color:red"
                            data-id="<?php echo $emp_values['id'] ?>"
                            data-target="#LWPModal" data-toggle="modal"
                            data-d_mon="<?php echo $mon; ?>"
                            data-d_year="<?php echo $year; ?>"
                            data-sno="<?php echo $count; ?>"><?php echo $A + $PR + $PL - $L + $this->Comman->LWP($emp_values['id'],$mon,$year); ?></a>
                        </td>
                      </tr>
                      <script>
                        $(document).ready(function() {
                            var sal = '<?php echo $sal_exist; ?>';
                            var id = '<?php echo $emp_values['id']; ?>';
                            if (sal == 1) {
                                $('.' + id).addClass('isDisabled');
                            }
                        });
                      </script>
                      <?php $count++;}}?>
                    </tbody>
                  </table>
                </div>

                <script>
                  $(document).ready(function() {
                    //alert();
                    $(document).on("click", ".globalModals11", function() {
                      // $(".globalModals11").click(function(event) {
                      // alert('Rupam');
                      var id = $(this).data('id');
                      var d_date = $(this).data('val');
                      var ab = $(this).data('ab');
                      var sno = $(this).data('sno');
                      $('.empId').val(id);
                      $('.fromDate').val(d_date);
                      $('.toDate').val(d_date);
                      $('.attn_id').val(ab);
                      $('.lwpsno').val(sno);
                    });
                  });;
                </script>

                <script>
                  $(document).ready(function() {
                    //alert();
                    $(document).on("click", ".LWPModal", function() {
                      // $(".LWPModal").click(function(event) {
                      //alert();
                      var id = $(this).data('id');
                      var d_mon = $(this).data('d_mon');
                      var d_year = $(this).data('d_year');
                      var sno = $(this).data('sno');
                      var date = "<?php echo $d_date; ?>";
                      $.ajax({
                        async: true,
                        data: {
                          'id': id,
                          'date': date,
                        },
                        type: "post",
                        url: "<?php echo ADMIN_URL; ?>leaves/all_leavesavail",
                        success: function(data) {
                          //alert(data);
                          $('.leave_det').html(data);
                        },
                      });
                      $('.empId').val(id);
                      $('.lwpmon').val(d_mon);
                      $('.lwpyear').val(d_year);
                      $('.lwpsno').val(sno);
                    });
                  });;
                </script>

                <script>
                  $(document).ready(function() {
                    //alert();
                    $(document).on("click", ".del_emp_data", function() {
                      // $(".del_emp_data").click(function() {
                      //alert($(this).data("val"));
                      var id = $(this).data("val");
                      $.ajax({
                        async: true,
                        data: {
                          id
                        },
                        type: "post",
                        beforeSend: function() {
                          var r = confirm("Delete Leave");
                          if (r == false) {
                              return false;
                          }
                          $('#load2').css("display", "block");
                        },
                        url: "<?php echo ADMIN_URL; ?>leaves/delete_leave",
                        success: function(data) {
                          //alert(data);
                          $('#load2').css("display", "none");
                          $('#emp_attn_sub').trigger('click');
                        },
                      });
                      return false;
                    });

                    // new delete functionality integrate by rupam 26/05/2022
                    $(document).on("click", ".del_emp_leave", function() {
                      // alert($(this).data("val"));

                      var id = $(this).data("val");

                      $.ajax({
                        async: true,
                        data: {
                          id
                        },
                        type: "post",
                        beforeSend: function() {
                          var r = confirm("Delete Leave");
                          if (r == false) {
                              return false;
                          }
                          $('#load2').css("display", "block");
                        },
                        url: "<?php echo ADMIN_URL; ?>leaves/del_emp_leave",
                        success: function(data) {
                          // alert(data);
                          $('#load2').css("display", "none");
                          $('#emp_attn_sub').trigger('click');
                        },
                      });

                      return false;

                    });

                  });
                  //]]>
                </script>

                <script>
                  $(document).ready(function() {
                    $(document).on("click", ".del_hol_data", function() {
                      // $(".del_hol_data").click(function() {
                      //alert($(this).data("val"));
                      var id = $(this).data("val");
                      $.ajax({
                        async: true,
                        data: {
                          id
                        },
                        type: "post",
                        beforeSend: function() {
                          var r = confirm("Delete Leave");
                          if (r == false) {
                            return false;
                          }
                          $('#load2').css("display", "block");
                        },
                        url: "<?php echo ADMIN_URL; ?>payroll/delete_holiday_allowance",
                        success: function(data) {
                          //alert(data);
                          $('#load2').css("display", "none");
                          $('#emp_attn_sub').trigger('click');
                        },
                      });
                      return false;
                    });
                  });
                  //]]>
                </script>
              </div>
            </div>
        </div>
      </div>
    </div>
  </section>

  <div class="modal" id="LWPModal" role="dialog" aria-labelledby="esModalLabel" aria-hidden="true"
    style="display:none">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <?php echo $this->Form->create($enquires, array('class' => '', 'id' => 'lwp_form', 'enctype' => 'multipart/form-data')); ?>
        <div class="modal-header" style="padding:0px; padding-left:15px; padding-right:15px;  line-height:20px;">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">LWP</h4>
        </div>

        <div class="modal-body">
          <input type="hidden" name="emp_id" class="empId">
          <input type="hidden" name="month" class="lwpmon">
          <input type="hidden" name="year" class="lwpyear">
          <input type="hidden" name="sno" class="lwpsno">
          <div class="leave_det">
          </div>
          <div class="row" style="margin-bottom:5px">
            <div class="col-sm-3">
              <label><b>Remarks:</b></label>
            </div>
            <div class="col-sm-6">
              <textarea name="remarks" placeholder="remarks" id="req"></textarea>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-default pull-left">Submit</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
        <?php echo $this->Form->end(); ?>
      </div>
    </div>
  </div>

  <div class="modal" id="globalModalkoi11" role="dialog" aria-labelledby="esModalLabel" aria-hidden="true" style="display:none">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <?php echo $this->Form->create($enquires, array('url' => array(
            'controller' => 'leaves', 'action' => 'add'), 'class' => '', 'id' => 'leav_form', 'enctype' => 'multipart/form-data')); ?>
        <div class="modal-header" style="padding:0px; padding-left:15px; padding-right:15px;  line-height:20px;">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">MANAGE LEAVE</h4>
        </div>

        <div class="modal-body">
          <input type="hidden" name="emp_id" class="empId empIdleav">
          <input type="hidden" name="date" class="fromDate fromDateleav">
          <input type="hidden" name="from" value="emp_attn">
          <input type="hidden" name="id" class="attn_id">
          <input type="hidden" name="sno" class="lwpsno lwpsnoleav">
          <div class="row" style="margin-bottom:5px">
            <div class="col-sm-3">
              <label><b> Action:</b></label>
            </div>
            <div class="col-sm-6">
              <input type="radio" name="action" value="leave" class="present" checked>Add Leave
              <input type="radio" name="action" value="present" class="present">Present
            </div>
          </div>
          <div class="row" id="pre_lev" style="margin-bottom:5px">
            <div class="col-sm-3">
              <label><b> Leave Type:</b></label>
            </div>
            <div class="col-sm-6">
              <?php echo $this->Form->input("leave_type", array('class' => '', 'class' => 'leavetypes', 'label' => false,'type'=>'select', 'options' => $leave_type, 'empty'=>'Select leavetype','required')); ?>
            </div>
          </div>
          <div class="row" style="margin-bottom:5px">
            <div class="col-sm-3">
              <label><b>Remarks:</b></label>
            </div>
            <div class="col-sm-6">
              <textarea name="remarks" placeholder="remarks" id="req" class="reqleav"></textarea>
            </div>
          </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-default pull-left">Submit</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
        <?php echo $this->Form->end(); ?>
      </div>
    </div>
  </div>

  <div class="modal" id="absentModalkoi11" role="dialog" aria-labelledby="esModalLabel" aria-hidden="true"
    style="display:none">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
          <?php echo $this->Form->create($enquires, array('class' => '', 'id' => 'attn_form', 'enctype' => 'multipart/form-data')); ?>
          <div class="modal-header"
              style="padding:0px; padding-left:15px; padding-right:15px;  line-height:20px;">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Attendance</h4>
          </div>
          <div class="modal-body">
              <input type="hidden" name="oid[]" class="empId">
              <div class="row">
                <div class="col-sm-3">
                    <label><b> Select Date:</b></label>
                </div>
                <div class="col-sm-6">
                    <?php echo $this->Form->input("abs_date[m_atn]", array('class' => 'form-control abdatepicker', 'id' => 'datepicker', 'label' => false, 'placeholder' => 'Select dates', 'required')); ?>
                </div>
              </div>
              <input type="hidden" name="date" class="fromDate">
              <input type="hidden" name="from" class="emp_attn">
              <div class="row" style="margin-bottom:5px">
                <div class="col-sm-3">
                    <label><b> Leave Type:</b></label>
                </div>
                <div class="col-sm-6">
                    <input type="hidden" name="sno" class="lwpsno">
                    <input type="radio" name="status" value="A" class="present1" checked>Full Day
                    <input type="radio" name="status" value="HF" class="present1">Half Day
                    <input type="radio" name="status" value="SL" class="present1">Short Leave
                </div>
              </div>
              <div class="row" id="pre_lev1" style="margin-bottom:5px">
                <div class="col-sm-3">
                    <label><b> Leave Type:</b></label>
                </div>
                <div class="col-sm-6">
                    <?php echo $this->Form->input("leave_type", array('class' => '', 'class' => 'leavetypes1', 'label' => false,'type'=>'select', 'options' => $leave_type, 'empty'=>'Select leavetype')); ?>
                </div>
              </div>
              <div class="row" style="margin-bottom:5px">
                <div class="col-sm-3">
                    <label><b>Remarks:</b></label>
                </div>
                <div class="col-sm-6">
                    <textarea name="remarks" palceholder="remarks"></textarea>
                </div>
              </div>
          </div>
          <div class="modal-footer">
              <button type="submit" class="btn btn-default pull-left">Submit</button>
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
          <?php echo $this->Form->end(); ?>
        </div>
    </div>
  </div>
</div>
<script>
  var date = new Date(<?php echo strtotime("1-" . $month) * 1000; ?>);
  var startDate = new Date(date.getFullYear(), date.getMonth(), 1);
  var endDate = new Date(date.getFullYear(), date.getMonth() + 1, 0);
  startDate.setDate(startDate.getDate() + 0);
  endDate.setDate(endDate.getDate() + 0);
  $(".abdatepicker").datepicker({
      startDate: startDate,
      endDate: endDate,
      multidate: true,
      format: 'dd-mm-yyyy',
      orientation: 'top',
  });
</script>

<!-- <script inline="1">
  $(document).ready(function() {
    $("#sevice_form1").bind("submit", function(event) {
      $.ajax({
        async: true,
        data: $("#sevice_form1").serialize(),
        dataType: "html",
        type: "POST",
        beforeSend: function() {
          $("#rmv").remove();
          $('#load2').css("display", "block");
        },
        url: "<?php //echo ADMIN_URL; ?>report/employee_monthly_search_report",
        success: function(data) {
          //alert(data);
          $('#load2').css("display", "none");
          $('#src-rslt1').hide();
          $('#src-rslt').show();
          $('#report').hide();
          $("#updt").html(data);
        },
      });
      return false;
    });
  });
  //]]>
</script> -->
<script inline="1">
  $(document).ready(function() {
      $("#lwp_form").bind("submit", function(event) {
          //alert(); 
          $.ajax({
              async: true,
              data: $("#lwp_form").serialize(),
              dataType: "html",
              type: "POST",
              url: "<?php echo ADMIN_URL; ?>leaves/add_lwp",
              success: function(data) {
                  //alert(data);
                  updaterow(data);
              },
          });
          return false;
      });
  });
  //]]>
</script>

<script inline="1">
  $(document).ready(function() {
      // $('#absentModalkoi11').on('hidden.bs.modal', function () {
      //   $(this).find('form').trigger('reset');
      // })
      $("#leav_form").bind("submit", function(event) {
          var rad = $('.present:checked').val();
          // alert(rad);
          if (rad == 'leave') {
              $.ajax({
                  async: false,
                  data: $("#leav_form").serialize(),
                  dataType: "html",
                  type: "POST",
                  beforeSend: function() {
                      $('#load2').css("display", "block");
                  },
                  url: "<?php echo ADMIN_URL; ?>leaves/add",
                  success: function(data) {
                      console.log(data);
                      $('#load2').css("display", "none");
                      $('#globalModalkoi11').modal('hide');
                      if (data == 1) {
                          alert('Insufficient Leave Balance');
                      } else if (data == 2) {
                          alert('Leave Cannot Club with other Leaves');
                      } else {
                          updaterow(data);
                      }
                      // $('#load2').css("display", "none");
                      // $(".modal").modal('hide');
                      // $('#emp_attn_sub').trigger('click');
                  },
              });
          } else {
              var ab = $('.attn_id').val();
              var rem = $('.reqleav').val();
              var empId = $('.empIdleav').val();
              var date = $('.fromDateleav').val();
              var sno = $('.lwpsnoleav').val();
              // alert(rem);
              $.ajax({
                  async: true,
                  data: {
                      id: ab,
                      remarks: rem,
                      date: date,
                      sno: sno,
                      empId: empId
                  },
                  dataType: "html",
                  type: "POST",
                  beforeSend: function() {
                      $('#load2').css("display", "block");
                  },
                  url: "<?php echo ADMIN_URL; ?>Employees/delete_atn",
                  success: function(data) {
                      //alert(data);
                      $('#load2').css("display", "none");
                      $(".modal").modal('hide');
                      updaterow(data);
                  },
              });
          }
          return false;
      });
      $('.present').change(function() {
          var pre = $('.present:checked').val();
          // alert(pre);
          if (pre == 'leave' || pre == 'A') {
              $('#pre_lev').show();
              $('.leavetypes').prop('required', true);
          } else {
              $("#req").prop('required', true);
              $('#pre_lev').hide();
              $('.leavetypes').prop('required', false);
          }
      });
      $('.present1').change(function() {
          var pre = $('.present1:checked').val();
          // alert(pre);
          if (pre == 'A') {
              $('#pre_lev1').show();
              $('.leavetypes1').prop('required', false);
          } else {
              $('#pre_lev1').hide();
              $("#req").prop('required', true);
              $('.leavetypes1').prop('required', false);
          }
      });
  });
  //]]>
</script>

<script inline="1">
  $(document).ready(function() {
    $("#hol_form").bind("submit", function(event) {
      $.ajax({
        async: true,
        data: $("#hol_form").serialize(),
        dataType: "html",
        type: "POST",
        beforeSend: function() {
            $('#load2').css("display", "block");
        },
        url: "<?php echo ADMIN_URL; ?>payroll/holiday_allowance",
        success: function(data) {
            //alert(data);
            $('#load2').css("display", "none");
            $(".modal").modal('hide');
            $('#emp_attn_sub').trigger('click');
        },
      });
      return false;
    });
  });
  //]]>
</script>

<script>
  $(document).on("click", ".absent", function() {
    var sno = $(this).data('sno');
    // alert(sno)
      var id = $(this).data('id');
      var d_date = $(this).data('val');
      $('.empId').val(id);
      $('.fromDate').val(d_date);
      $("#absentModalkoi11").modal('show');
      $('.abdatepicker').val(d_date);
      $('.lwpsno').val(sno);
  
  });

  $("#attn_form").bind("submit", function(event) {
  
      $.ajax({
          async: true,
          data: $("#attn_form").serialize(),
          dataType: "html",
          type: "POST",
          beforeSend: function() {
              $(".modal").modal('hide');
              $('#load2').css("display", "block");
          },
          url: "<?php echo ADMIN_URL; ?>employees/take_other_attendance",
  
          success: function(data) {
              //alert(data);
              updaterow(data);
              $('#load2').css("display", "none");
  
              // $('#load2').css("display", "none");
              // $('#emp_attn_sub').trigger('click');
              $('#emp_attn_sub').trigger('click');
  
          },
  
      });
  
      return false;
  });
</script>

<script>
  function updaterow(datas) {
  
      data = JSON.parse(datas);
      $.ajax({
          async: true,
          data: {
              'id': data.empId,
              'date': data.date,
              'sno': data.sno
          },
          type: "POST",
          url: "<?php echo ADMIN_URL; ?>report/update_row",
  
          success: function(result) {
              //alert(data);
              $('#LWPModal').modal('hide');
              $('.row' + data.empId).html(result)
              return true;
          },
  
      });
  
  }
</script>