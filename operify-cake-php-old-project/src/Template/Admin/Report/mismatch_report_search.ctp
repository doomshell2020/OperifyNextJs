<div style="clear: both;"></div>

<div class="table-responsive">
<table id="example11" class="table table-bordered table-striped">
  <thead>
    <th>#</th>

    <th>Date</th>
    <th>SR. No.</th>
    <th>Students Name</th>
    <th>Class </th>
    <th>Section</th>


  </thead>
  <tbody>
    <tr>
      <td><a id="" style="position: absolute;
top: -80px;
/* right: 0px; */
right: 46px;" class="btn btn-info btn-sm pull-right" target="_blank"
          href="<?php echo ADMIN_URL; ?>report/mismatch_excel"><i class="fa fa-file-excel-o" aria-hidden="true"></i>
          Export Excel</a></td>
    </tr>

    <?php
if (!empty($res)) {
    $count = 1;foreach ($res as $value) {
        ?> <tr>
      <td><?php echo $count; ?></td>
      <td><?php echo date('d-m-Y', strtotime($value['date'])); ?></td>
      <td><?php echo $value['stud_id']; ?></td>
      <td><?php echo $value['student']['fname'] . "\x20" . $value['student']['middlename'] . $value['student']['lname'];
        ?></td>
      <td><?php $class = $this->comman->findclass123($value['class_id']);
        echo $class['title'];?></td>
      <td><?php $section = $this->comman->findsection123($value['section_id']);
        echo $section['title'];?></td>

    </tr><?php $count++;

    }} else {?>
      <tr>
      <td colspan="6">No Data Available</td>
      </tr>
    <?php }
?>


  </tbody>

</table>
    </div>
<script>
$("#example11").DataTable();
</script>