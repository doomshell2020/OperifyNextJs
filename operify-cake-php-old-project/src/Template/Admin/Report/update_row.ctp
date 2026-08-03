
<?php 
$emp_values=$this->Comman->findemployee($id); 
// pr($emp_values); die;
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
    if ($dayOfWeek != 'Sunday' && $holiday == 0) {
      $date_range[] = $pre_date;
    } else {
      if ($dayOfWeek == 'Sunday') {
        
$date_range[] = 'sun';

    } else if ($holiday > 0) {
        
//$date_range[] = 'hol';
        $date_range[] = 'hol';

    }

    }

    $days++;
  }
?>

<td><?php echo $sno ?></td>
                <td><a id="absent-data" style="text-decoration: none;" class="absent <?php echo $emp_values['id'] ?>"  data-d_mon="<?php echo $mon; ?>" data-d_year="<?php echo $year; ?>" data-sno="<?php echo $sno; ?>"
                    href="javascript:void(0)" title="Mark Absent" data-id="<?php echo $emp_values['id'] ?>"><b>
                      <?php echo strtoupper($emp_values['fname']) . "\x20" . strtoupper($emp_values['middlename']) . "\x20" . strtoupper($emp_values['lname']); ?>
                    </b> </a> </td>
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
        // pr($date_range);die;
        $i = 1;
        foreach ($date_range as $date_val) {
            if ($d_cnt < $j_day) {?>

                <td>-</td>
                <?php $d_cnt++;
                continue;}
            $p_date = $d_cnt . "-" . $date;
        // pr($p_date); die;
            if (strtotime($p_date) > strtotime(date('d-m-Y'))) {?>
                <td>-</td>
                <?php $d_cnt++;
                continue;
            }
            $status = '';
            $emp_atn = '';
            if ($date_val != 'hol' && $date_val != 'sun') {
                //pr($date_val);
                $emp_atn = $this->Comman->findEmp_Att_bydate($emp_values['id'], $date_val);

                //pr($emp_atn);die;

                if (!empty($emp_atn)) {
                  
                    $status = $emp_atn['status'];
                    if ($status == 'A') {
                        $A += 1;
                    } else if ($status == 'HF') {
                        $PR += 0.5;
                    } else if ($status == 'SL') {
                        $PL += 0.25;
                    }
                } else {?>
                <td style="color:green"><a id="absent-data" class="absent <?php echo $emp_values['id'] ?>" href="javascript:void(0)"
                    title="Mark Absent" style="color:green" data-id="<?php echo $emp_values['id'] ?>"
                    data-val="<?php echo date('d-m-Y', strtotime($p_date)); ?>"  data-sno="<?php echo $sno; ?>">P</a></td>
                <?php }

                if ($status == 'A' || $status == 'HF' || $status == 'SL') {
                    $leave = $this->Comman->leaveStatus($date_val, $emp_values['id']);
                     
                    ?> <td><?php if (!empty($leave)) {if (in_array($leave['leave_type'],$leave_id)) {?> <a id="update-data"
                    class="del_emp_data <?php echo $emp_values['id'] ?>" href="javascript:void(0)" title="Delete Leave" style="color:red"
                    data-val="<?php echo $leave['id']; ?>"><?php echo $leave['leavetype']['short_name']; ?></a><?php
if ($status == 'A') {
                        $L += 1;
                    } else {
                        $L += 0.5;
                    }
                    } else {?> <a id="update-data" class="del_emp_data <?php echo $emp_values['id'] ?>" href="javascript:void(0)"
                    title="Delete Leave" style="color:red"
                    data-val="<?php echo $leave['id']; ?>"><?php echo $status; ?></a><?php }} else {?>
                  <a id="update-data" data-ab="<?php echo $emp_atn['id']; ?>"
                    class="<?php echo $emp_values['id'] ?> globalModals11" href="javascript:void(0)" title="Add Leave" style="color:red"
                    data-id="<?php echo $emp_values['id'] ?>" data-target="#globalModalkoi11" data-toggle="modal"
                    data-val="<?php echo date('Y-m-d', strtotime($p_date)); ?>" data-sno="<?php echo $sno; ?>"><?php echo $status;} ?></a>
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
                    data-modal-size="modal-lg" style="color:red">H </a></td>
                <?php } else {?> <td><a id="update-data" class="<?php echo $emp_values['id'] ?> del_hol_data" href="javascript:void(0)"
                    title="DELETE ALLOWANCE" style="color:green" data-val="<?php echo $hol_exist['id']; ?>">H(<?php if ($hol_exist['type'] == 'full') {echo 1;
                        $t_hol += 1;} else {echo 0.5;
                        $t_hol += 0.5;}?>)</a></td>
                <?php //if ($hol_exist['type'] == 'full') {$t_hol += 1;} else { $t_hol += 0.5;}
                    }
                  }
                  else{ ?>
                   <td> <span style="color:red">H</span> </td>
                   <?php }
                    }?>

                <?php $d_cnt++;
            $i++;
        }?>

                <td style="color:red"><?php echo $A + $PR + $PL - $L; ?></td>
                <td style="color:blue"> <a id="update-data" data-ab="<?php echo $emp_atn['id']; ?>"
                    class="<?php echo $emp_values['id'] ?> LWPModal" href="javascript:void(0)" title="ADD LWP" style="color:red"
                    data-id="<?php echo $emp_values['id'] ?>" data-target="#LWPModal" data-toggle="modal"
                    data-d_mon="<?php echo $mon; ?>" data-d_year="<?php echo $year; ?>" data-sno="<?php echo $sno; ?>"><?php echo $this->Comman->LWP($emp_values['id'],$mon,$year); ?></a></td>
             