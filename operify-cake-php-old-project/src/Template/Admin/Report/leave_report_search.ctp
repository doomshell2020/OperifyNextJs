<div class="table-responsive">
<table id="example1" class="table table-bordered table-striped">
  <thead>
    <tr style="background-color:#39cccc !important; color:white">
      <th>#</th>
      <th>Date</th>
      <th>EmpId</th>
      <th>E-Name</th>
      <th>E-Mobile</th>

      <th>Department</th>
      <th>Designation</th>
      <th>Leave Type</th>

    </tr>
  </thead>
  <tbody>
    <?php $page = $this->request->params['paging']['Services']['page'];
$limit = $this->request->params['paging']['Services']['perPage'];
$counter = ($page * $limit) - $limit + 1;
//pr($Leaves);die;
if (isset($Leaves) && !empty($Leaves)) {
    foreach ($Leaves as $service) {?>
    <tr>

      <td><?php echo $counter; ?></td>
      <td>
        <?php if (isset($service['date'])) {echo $date = date("d-m-Y", strtotime($service['date']));}?>
      </td>
      <td><?php if (isset($service['emp_id'])) {echo $service['emp_id'];} else {echo 'N/A';}?></td>
      <td>
        <?php if (isset($service['employee']['fname'])) {echo $service['employee']['fname'] . " " . $service['employee']['middlename'] . " " . $service['employee']['lname'];} else {echo 'N/A';}?>
      </td>
      <td>
        <?php if (isset($service['employee']['mobile'])) {echo $service['employee']['mobile'];} else {echo 'N/A';}?>
      </td>

      <td><?php if (isset($service['employee']['p_department'])) {
        $department_id = $this->Comman->payrolldepartment($service['employee']['p_department']);
        // $designation_id=$this->Comman->finddesignation($employee['designation_id']) ;
        echo $department_id['name'];} else {echo 'N/A';}?></td>
      <td><?php if (isset($service['employee']['p_designation'])) {
        $designation_id = $this->Comman->payrolldesgination($service['employee']['p_designation']);
        echo $designation_id['name'];} else {echo 'N/A';}?></td>
      <td>
        <?php echo ucfirst($service['leave_type']); ?>
      </td>
    </tr>
    <?php $counter++;?>

    <?php }} else {?>
    <tr>
      <td colspan="13">No Data Available</td>
    </tr>
    <?php }?>
  </tbody>

</table>
    </div>