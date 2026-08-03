<style>
.isDisabled {
  color: currentColor;
  cursor: not-allowed;
  opacity: 0.5;
  text-decoration: none;
  pointer-events: none;
}

td {
  font-size: 11px;
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
</style>

<div class="box-header col-sm-12">
  <i class="fa fa-search" aria-hidden="true"></i>
  <h4 class="box-title">Search Attendance Report</h4>
  <a href="<?php echo ADMIN_URL; ?>Report/employee_monthly_report_excel/<?php echo $date ?>"
    class="btn btn-info pull-right">Export To
    Excel</a>

</div>
<div class="box-body">
  <div class="example2">
  <div class="table-responsive">
    <table class="table table-bordered table-striped">
      <thead>
        <tr style="background-color:#39cccc !important; color:white">
          <th>#</th>
          <th>Teacher's Name</th>

          <?php $mon = date('m', $date);
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
          <?php $date_range[] = $pre_date;} else {if ($dayOfWeek == 'Sunday') {
        ?>
          <th><span style="color:red">Sun</span><br>
            <?php echo $days; ?>
          </th>
          <?php
$date_range[] = 'sun';

    } else if ($holiday > 0) {
        ?>
          <th><span style="color:red">Hol</span><br>
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

          <th style="color:red">A</th>
          <th style="color:red">H</th>


        </tr>
      </thead>
      <?php $date = date('d-m-Y');
//pr($students);die;?>

      <tbody id="example2">
        <?php $count = 1;

foreach ($dept as $dept_val) {
    $emp = $this->Comman->empByPId($dept_val['id']);
    ?>
        <tr>
          <td colspan="5"><b>
              <?php echo $dept_val['name']; ?></b>
          <td>
        </tr>
        <?php foreach ($emp as $emp_values) {

        $j_day = 0;
        $join_month = date("m-Y", strtotime($emp_values['joiningdate']));

        if ($date <= $join_month) {
            $j_day = date("d", strtotime($emp_values['joiningdate']));
        }

        ?>
        <tr>
          <td><?php echo $count ?></td>
          <td>
            <?php echo strtoupper($emp_values['fname']) . "\x20" . strtoupper($emp_values['middlename']) . "\x20" . strtoupper($emp_values['lname']); ?>
          </td>
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
        foreach ($date_range as $date_val) {
            if ($d_cnt <= $j_day) {?>

          <td>-</td>
          <?php $d_cnt++;
                continue;}
            $p_date = $d_cnt . "-" . $d_date;

            $status = '';
            $emp_atn = '';
            if ($date_val != 'hol' && $date_val != 'sun') {
                //pr($date_val);
                $emp_atn = $this->Comman->findEmp_Att_bydate($emp_values['id'], $date_val);

                //pr($emp_atn);die;

                if (!empty($emp_atn)) {

                    $abst = explode(',', $emp_atn['absent_periods']);
                    //pr($abst);die;
                    if (count($abst) > 4) {
                        $status = 'A';
                        $A += 1;
                    } else if (in_array('A0', $abst) || in_array('A1', $abst) || in_array('A2', $abst) || in_array('A3', $abst)) {
                        $status = "PR";
                        $PR += 0.5;
                    } else if (in_array('A4', $abst) || in_array('A5', $abst) || in_array('A6', $abst) || in_array('A7', $abst)) {
                        $status = "PL";
                        $PL += 0.5;
                    }

                } else {?>
          <td style="color:green">P</td>
          <?php }
                if (!empty($emp_atn)) {
                    $status = $emp_atn['status'];
                    if ($status == 'A') {
                        $A += 1;
                    } else if ($status == 'PR') {
                        $PR += 0.5;
                    } else {
                        $PL += 0.5;
                    }
                } else {?>
          <td style="color:green">P</td>
          <?php }

                if ($status == 'A' || $status == 'PR' || $status == 'PL') {
                    $leave = $this->Comman->leaveStatus($date_val, $emp_values['id']);
                    ?> <td><?php if (!empty($leave)) {if ($leave['leave_type'] == 'paid') {?> <a id="update-data"
              class="<?php echo $emp_values['id'] ?> del_emp_data" href="#" title="Delete Leave" style="color:red"
              data-val="<?php echo $leave['id'] ?>">L(P)</a><?php
if ($status == 'A') {
                        $L += 1;
                    } else {
                        $L += 0.5;
                    }
                    } else {?> <a id="update-data" class="<?php echo $emp_values['id'] ?> del_emp_data" href="#"
              title="Delete Leave" style="color:red" data-val="<?php echo $leave['id'] ?>">L(U)</a><?php }} else {?> <a
              id="update-data" class="<?php echo $emp_values['id'] ?> globalModals11" href="#" title="Add Leave"
              style="color:red" data-id="<?php echo $emp_values['id'] ?>" data-target="#globalModalkoi11"
              data-toggle="modal"
              data-val="<?php echo date('Y-m-d', strtotime($p_date)); ?>"><?php echo $status;} ?></a>
          </td>
          <?php }?>

          <?php } else {
             $payroll=$this->payroll();
             if($payroll['holiday_allow']==1){
                $hol_exist = array();

                $hol_exist = $this->Comman->findhol_allow($emp_values['id'], $p_date);
                if (empty($hol_exist)) {
                    ?>
          <td><a id="update-data" class="<?php echo $emp_values['id'] ?>"
              href="<?php echo SITE_URL; ?>admin/payroll/holiday_allowance/<?php echo $emp_values['id'] ?>/<?php echo $d_cnt . "-" . $d_date; ?>"
              title="HOLIDAY ALLOWANCE" data-target="#globalModalkoi" data-toggle="modal" class="globalModals"
              data-modal-size="modal-lg" style="color:red">H</a></td>
          <?php } else {?> <td><a id="update-data" class="<?php echo $emp_values['id'] ?> del_hol_data" href="#"
              title="DELETE ALLOWANCE" style="color:green" data-val="<?php echo $hol_exist['id']; ?>">H</a></td>
          <?php if ($hol_exist['type'] == 'full') {$t_hol += 1;} else { $t_hol += 0.5;}}
         }
         else{ ?>
          <td> <span style="color:red">H</span> </td>
          <?php }
        }?>

          <?php $d_cnt++;}?>

          <td style="color:red"><?php echo $A + $PR + $PL - $L; ?></td>
          <td style="color:blue"><?php echo $t_hol; ?></td>
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
      $(".globalModals11").click(function(event) {
        //alert();
        var id = $(this).data('id');
        var d_date = $(this).data('val');;
        $('.empId').val(id);
        $('.fromDate').val(d_date);
        $('.toDate').val(d_date);

      });
    });;
    </script>
    <script>
    $(document).ready(function() {
      //alert();
      $(".del_emp_data").click(function() {
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
    });
    //]]>
    </script>

    <script>
    $(document).ready(function() {

      $(".del_hol_data").click(function() {
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