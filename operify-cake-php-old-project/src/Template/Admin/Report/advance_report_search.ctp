<?php $page = $this->request->params['paging']['Services']['page'];
$limit = $this->request->params['paging']['Services']['perPage'];
$counter = ($page * $limit) - $limit + 1;
$total_adv = 0;
$total_dep = 0;?>
<tr>
  <td><a id="" style="position: absolute;
top: -83px;
/* right: 0px; */
right: 46px;" class="btn btn-info btn-sm pull-right" target="_blank"
      href="<?php echo ADMIN_URL; ?>report/advance_pdf"><i class="fa fa-file-excel-o" aria-hidden="true"></i>
      Export</a></td>
</tr>
<?php
if (isset($advance_det) && !empty($advance_det)) {
    foreach ($advance_det as $service) { //pr($service);die;

        ?>
<tr>
  <?php $emp_det = $this->Comman->findemployeename($service['employee_id']);
        //pr($emp_det);die;
        $adv_ret = $this->Comman->findadvancereturn($service['id']);
        //pr($adv_ret);die;
        $desg = $this->Comman->finddesignation($emp_det['designation_id']);
        //pr($desg);die;
        ?>
  <td><?php echo $counter; ?></td>
  <td><?php echo $service['employee_id']; ?></td>
  <td>
    <?php echo strtoupper($emp_det['fname']) . "\x20" . strtoupper($emp_det['middlename']) . strtoupper($emp_det['lname']); ?>
  </td>
  <td><?php echo $desg[0]['name']; ?></td>
  <td>
    <?php if ($service['paydate'] != "") {echo date('d-m-Y', strtotime($service['paydate']));} else {echo "-";}?>
  </td>
  <td>
    <?php if ($service['deposit_date'] != "") {echo date('d-m-Y', strtotime($service['deposit_date']));} else {echo "-";}?>
  </td>
  <td><?php if (!empty($service['amount'])) {echo $service['amount'];} else {echo "-";}?></td>
  <td><?php if (!empty($service['deposit_amount'])) {echo $service['deposit_amount'];} else {
            echo "-";
        }?></td>
  <td></td>

</tr>
<?php
$total_adv += $service['amount'];
        $ret_det = $this->Comman->findadvancereturndet($service['id']);
        $dep_sum = 0;

        ?>
<!-- <tr>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td><b><?php echo $service['amount'] ?></b></td>
  <td><b><?php echo $dep_sum ?></b></td>
  <td style="color:red"><b><?php echo $service['amount'] - $dep_sum ?></b></td>
</tr> -->
<?php $counter++;
        $total_dep += $service['deposit_amount'];

    }?>
<tr>

  <td colspan="6" style="text-align:center"><b>Total</b></td>
  <td style="display: none"></td>
  <td style="display: none"></td>
  <td style="display: none"></td>
  <td style="display: none"></td>
  <td style="color:green"><b><?php echo $total_adv ?></b></td>
  <td style="color:green"><b><?php echo $total_dep ?></b></td>
  <td style="color:red"><b><?php echo $total_adv - $total_dep; ?></b></td>
</tr>
<?php } else {?>
<tr>
  <td colspan="6">No Data Available</td>
</tr>
<?php }?>