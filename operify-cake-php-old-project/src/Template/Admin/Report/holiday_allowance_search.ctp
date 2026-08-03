<?php $page = $this->request->params['paging']['Services']['page'];
$limit = $this->request->params['paging']['Services']['perPage'];
$counter = ($page * $limit) - $limit + 1;
$total_adv = 0;
$total_dep = 0;?>
<?php
if (isset($res) && !empty($res)) {
    $count = 1;
    //pr($res);die;
    foreach ($res as $value) {
        $emp_name = $this->Comman->findemployeename($value['employee_id']);
        ?>
<tr>
  <td><?php echo $count; ?></td>
  <td><?php echo $value['employee_id']; ?></td>
  <td>
    <?Php echo strtoupper($emp_name['fname']) . strtoupper($emp_name['middlename']) . strtoupper($emp_name['lname']) ?>
  </td>
  <td><?php echo date('d-m-Y',strtotime($value['date'])); ?></td>

  <td><a href="<?php echo SITE_URL; ?>admin/payroll/delete_holiday_allowance/<?php echo $value['id']; ?>" onclick="return confirm('Are you sure?')"><i
        class="fa fa-trash" aria-hidden="true"></i></a></td>
  </td>

</tr>


<?php $count++;}
}
?>
