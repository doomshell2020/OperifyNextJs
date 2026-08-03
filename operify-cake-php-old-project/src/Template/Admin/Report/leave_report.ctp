<style>

</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Daily Leave Report

    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo ADMIN_URL; ?>dashboards"><i class="fa fa-home"></i>Home</a></li>
      <li><a href="<?php echo ADMIN_URL; ?>leaves/index">Manage Leaves</a></li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-xs-12">

        <div class="box">
          <div class="box-body">
            <div class="box-header">
              <!--  <h3 class="box-title">Leave List</h3>   -->
            </div>
            <!-- /.box-header -->
            <?php echo $this->Flash->render(); ?>

            <?php echo $this->Form->create('', array('class' => 'form-horizontal', 'id' => 'sevice_form', 'enctype' => 'multipart/form-data')); ?>
           
            <div class="form-group">
            

              <div class="col-sm-3">
                <label>From Date<span style="color:red;">*</span></label>
                <?php echo $this->Form->input('from', array('class' => 'form-control', 'required', 'id' => 'datepicker1', 'value' => date('d-m-Y'), 'label' => false)); ?>
              </div>
              <div class="col-sm-3">
                <label>To Date<span style="color:red;">*</span></label>
                <?php echo $this->Form->input('to', array('class' => 'form-control', 'required', 'id' => 'datepicker2', 'value' => date('d-m-Y'), 'label' => false)); ?>
              </div>
              <script>
              $(document).ready(function() {
                $('#datepicker1').datepicker({
                  dateFormat: 'yy-mm-dd',
                  onSelect: function(date) {

                    var selectedDate = new Date(date);
                    var endDate = new Date(selectedDate);
                    endDate.setDate(endDate.getDate());

                    $("#datepicker2").datepicker("option", "minDate", endDate);
                    $("#datepicker2").val(date);
                  }

                });


                $('#datepicker1').datepicker('setDate', 'today');

                $('#datepicker2').datepicker({
                  dateFormat: 'yy-mm-dd'
                });
                $('#datepicker2').datepicker('setDate', 'today');
              });
              </script>





            </div>
            <div class="form-group">
              <!-- <div class="col-sm-3">
                 <label>Leave Type</label> -->
              <?php //echo $this->Form->input('leave_type_id', array('class' => 'form-control', 'type' => 'select', 'required', 'empty' => 'Select Leave-Type', 'id' => 'emoloyee_id', 'options' => $Leavetype, 'label' => false)); ?>
              <!-- </div> -->



            </div>
            <button type="submit" class="btn btn-success">Submit</button>
            <?php echo $this->Form->end(); ?>

          </div>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </div>
      <!-- /.col -->
    </div>
    <div class="box">
      <div class="box-body">
        <div class="row">
          <div class="col-sm-12">

            <div class="table-responsive" id="updt">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                  <tr style="background-color:#39cccc !important; color:white">
                    <th>#</th>
                    <th>EmpId</th>
                    <th>E-Name</th>
                    <th>E-Mobile</th>

                    <th>Department</th>
                    <th>Designation</th>
                    <th>Leave Type</th>
                    <th>Date</th>
                    <th>Applied By</th>
                    <th>Reason</th>

                  </tr>
                </thead>
                <tbody>
                  <?php $page = $this->request->params['paging']['Services']['page'];
$limit = $this->request->params['paging']['Services']['perPage'];
$counter = ($page * $limit) - $limit + 1;
if (isset($emp_leave) && !empty($emp_leave)) {
    foreach ($emp_leave as $service) {
        ?>
                  <tr>
                    <td><?php echo $counter; ?></td>
                    <td><?php echo $service['emp_id']; ?></td>
                    <td>
                      <?php echo $service['employee']['fname'] . " " . $service['employee']['middlename'] . " " . $service['employee']['lname']; ?>
                    </td>
                    <td>
                      <?php if (isset($service['employee']['mobile'])) {echo $service['employee']['mobile'];} else {echo 'N/A';}?>
                    </td>

                    <td><?php if (isset($service['employee']['department_id'])) {
            $department_id = $this->Comman->finddepartment($service['employee']['department_id']);
            // $designation_id=$this->Comman->finddesignation($employee['designation_id']) ;
            echo $department_id[0]['name'];} else {echo 'N/A';}?></td>
                    <td><?php if (isset($service['employee']['designation_id'])) {
            $designation_id = $this->Comman->finddesignation($service['employee']['designation_id']);
            echo $designation_id[0]['name'];} else {echo 'N/A';}?></td>
                    <td>
                      <?php echo $service['leave_type']; ?>
                    </td>
                    <td>
                      <?php echo $date = date("d-m-Y", strtotime($service['date'])); ?>
                    </td>
                    <td>
                      <?php if ($service['applied_by'] == $service['empoyee_id']) {
            echo $service['employee']['fname'] . " " . $service['employee']['middlename'] . " " . $service['employee']['lname'];
        } else {
            echo "Payroll Manager";
        }?>
                    </td>
                    <td><?php echo $service['narration']; ?></td>

                  </tr>
                  <?php $counter++;}} else {?>
                  <tr>
                    <td>NO Data Available</td>
                  </tr>
                  <?php }?>
                </tbody>

              </table>
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
</div>
<script inline="1">
$(document).ready(function() {

  $("#sevice_form").bind("submit", function(event) {

    //alert();
    $.ajax({
      async: true,
      data: $("#sevice_form").serialize(),
      dataType: "html",
      type: "POST",
      beforeSend: function() {
        //Show image container
        $('#load2').css("display", "block");
      },
      url: "<?php echo ADMIN_URL; ?>report/leave_report_search",

      success: function(data) {
        $("#updt").html(data);
      },

    });

    return false;
  });
});
//]]>
</script>

<!-- /.content-wrapper -->