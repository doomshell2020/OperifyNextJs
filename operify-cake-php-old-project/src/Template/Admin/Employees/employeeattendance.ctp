<style>
ul.atn_tab {
  margin: 0px;
  padding: 0px;
  list-style: none;
}

ul.atn_tab li {
  background: none;
  color: #222;
  display: inline-block;
  padding: 10px 15px;
  cursor: pointer;
  border: 1px solid #CCC;
  border-radius: 3px;
}

ul.atn_tab li.current {
  background: #00c0ef;
  color: #fff;
}

.tab-content1 {
  display: none;
  background: #fbfbfb;
  padding: 15px;
}

.tab-content1.current {
  display: inherit;
}

td.tooltips {
  position: relative;
  display: inline;
}

td.tooltips span {
  position: absolute;
  width: 300px;
  color: green;
  background: #D9FFF5;
  height: 30px;
  line-height: 30px;
  text-align: center;
  visibility: hidden;
  border-radius: 6px;
}

td.tooltips span:after {
  content: '';
  position: absolute;
  top: 100%;
  left: 50%;
  margin-left: -8px;
  width: 0;
  height: 0;
  border-top: 8px solid #D9FFF5;
  border-right: 8px solid transparent;
  border-left: 8px solid transparent;
}

td:hover.tooltips span {
  visibility: visible;
  opacity: 1.0;
  bottom: 30px;
  left: 50%;
  margin-left: -76px;
  z-index: 999;
}

.add_employee {
  margin: 0px 8px 0px 8px;
}


#datepicker>span:hover {
  cursor: pointer;
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


<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.css" rel="stylesheet"
  type="text/css" />
<script src="plugins/datatables/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="plugins/datatables/dataTables.bootstrap.min.js" type="text/javascript"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.js"></script>

<script>
$(function() {
  $("#monyr").datepicker({
    format: "mm-yyyy",
    startView: "months",
    minViewMode: "months",
    autoclose: true,

  });
  $("#emp_attn_sub").on('click', function(ev) {
    var month = $("#monyr").val();

    $.ajax({
      type: "POST",
      async: true,
      data: {
        month: month,
      },
      //dataType: "html",
      beforeSend: function() {
        $("#load2").show();
      },

      url: "<?php echo ADMIN_URL; ?>Employees/employeeattendance_search",
      success: function(data) {
        $("#load2").hide();
        $("#upmon").html(data);
      },

    });
  });
});
</script>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>Staff Manager </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo ADMIN_URL; ?>"><i class="fa fa-home"></i>Home</a></li>
      <li><a href="<?php echo ADMIN_URL; ?>employees">Manage Staff</a></li>
    </ol>
  </section><?php $role_id = $this->request->session()->read('Auth.User.role_id');
?>
  <!-- Main content -->
  <form style="margin-top:10px; margin-bottom:40px">
    <div class="col-sm-1" style="text-align:right;padding-right:0px">
      <label>Select Month:</label>
    </div>
    <div class="col-sm-2">
      <input type="text" id="monyr" value="<?php echo date('m-Y'); ?>" class="form-control" data-date-end-date="0m">
    </div>
    <div class="col-sm-1">
      <button type="button" class="btn btn-info" id="emp_attn_sub">Search</button>
    </div>

  </form>
  <section class="content" id="upmon">
    <div class="row">
      <div class="col-xs-12">

        <div class="box">
          <ul class="atn_tab">
            <li class="tab-link current" data-tab="tab-1">Teacher Attendance</li>
            <li class="tab-link" data-tab="tab-2">Other Staff Attendance</li>
          </ul>

          <div id="tab-1" class="tab-content1 current" style="padding:0px">
            <?php echo $this->Form->create('', array('id' => 'form1234', 'action' => 'take_attendance', 'method' => 'post'));

?>
            <div class="box-header">
              <i class="fa fa-search" aria-hidden="true"></i>
              <h3 class="box-title">Teachers List </h3>
              <div class="submit" style="float:right;">
                <script>
                $(document).ready(function() {

                    $("#absentbtn").click(function() {
                        if ($('.emp_id').filter(':checked').length < 1) {
                          alert('Please select atleast one');
                          return false;
                        } else {
                          $('#myModal').modal('show');
                        }
                      }

                    );
                  }

                );
                </script>



                <input type="button" class="btn btn-info validate" id="absentbtn" value="Absent"
                  style="font-weight:800">
                <a target="_blank" class="btn btn-info add_employee" href="<?php echo ADMIN_URL ?>employees/add"><b><i
                      class="fa fa-plus " aria-hidden="true"></i>Add
                    Staff</b></a>
                <?php /* ?> <a href="<?php echo SITE_URL; ?>admin/employeeattendance/attendance_csv"
                  class="btn btn-success  m-top10"><i class="fa fa-plus" aria-hidden="true"></i>Import Attendance
                </a> <?php */?>
                <a href="<?php echo SITE_URL; ?>admin/employees/addcsv" class="btn btn-success  m-top10"><i
                    class="fa fa-plus" aria-hidden="true"></i>Import Payroll Entry
                </a>
              </div>
              <script>
              $(document).ready(function() {
                  $('#modalcancel').click(function() {
                      $('.absent_check').prop('checked', true);
                      // $('#ab_selectall').prop('checked', false);
                    }

                  );


                }

              );
              </script>
              <div class="modal fade" id="myModal" role="dialog">
                <div class="modal-dialog">

                  <div class="modal-content" style="background-color:#ecf0f5;">
                    <div class="modal-header" style="background-color: #3c8dbc;"><button type="button" class="close"
                        data-dismiss="modal">&times;
                      </button>
                      <h4>Select Absent Periods</h4>
                    </div>
                    <div class="modal-body" style="width:50%; margin:auto; background-color:#FFF;">
                      <div class=" row">
                        <div class="form-group">
                          <div class="col-sm-12">
                            <div>
                              <div class="col-sm-12">
                                <label>Status</label>
                                <select name="status">
                                  <option value="A">Absent</option>
                                  <option value="PR">Pre-Lunch Absent</option>
                                  <option value="PL">Post-Lunch Absent</option>
                                </select>
                              </div>
                            </div>
                          </div>
                        </div>

                        <div class="form-group">
                          <div class="col-sm-12"><label>Remarks</label><textarea name="remark"
                              style="width:100%"></textarea></div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer" style="border-top:1px solid #FFF;background-color:#ecf0f5;">
                    <div class="submit">
                      <!-- <input type="submit" class="btn btn-info pull-right" id="absnt_chk" title="ABSENT" style="display: block;" name="status" value="Absent"> -->
                      <input type="submit" style="margin-right:10px;" class="btn btn-info">
                      <button type="button" class="btn btn-default pull-left" id="modalcancel"
                        data-dismiss="modal">Close</button></div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Horizontal Form -->
            <div id="load2" style="display:none;"></div>
            <div class="box box-info" id="updt" style="padding:10px">

              <?php echo $this->Flash->render(); ?>
              <table id="emp_att" class="table table-bordered table-striped" style="padding:0px">
                <thead>
                  <tr>
                    <th><input type="checkbox" id="selectall"></th>
                    <th>S.No.</th>
                    <th>Name</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Mobile</th>
                    <?php if ($rolepresent == 16) {?>
                    <th>Salary Management</th>
                    <?php }?>

                    <th>Joining Date</th><?php if ($role_id == '1' || $role_id == '5' || $role_id == '8' || $role_id == '16') {
    ?><th>Action</th><?php
}

?>
                  </tr>
                </thead>
                <tbody><?php $page = $this->request->params['paging']['Students']['page'];
$limit = $this->request->params['paging']['Students']['perPage'];
$counter = ($page * $limit) - $limit + 1;
$date = date('m-Y');
if (isset($students) && !empty($students)) {
    foreach ($students as $work) {

        $emp_sal_det = $this->Comman->findemplobasic($work['id']);
        $salary_mode = $this->Comman->findpaymentmode($emp_sal_det['payment_mode']);
        ?><?php $att_exist = $this->Comman->findAttendancemon($work['id'], $date);
        // pr($work);

        //pr($period);die;?>
                  <tr <?php if (!empty($att_exist['id'])) {?>style="color:red" <?php }?>>


                    <td><input type="checkbox" class="emp_id" name="id[]" value=<?php echo $work['id'];
        ?>></td>
                    <td><?php echo $counter; ?></td>
                    <td <?php if ($att_exist > 0) {?> class="tooltips" <?php }?>><?php if (!empty($att_exist)) {?>
                      <span>Absent on &nbsp; <?php echo $att_exist; ?>&nbsp;</span> <?php }?>

                      <a title="View Staff Detail"
                        href="<?php echo ADMIN_URL; ?>employees/view/<?php echo $work['id']; ?>"><?php echo strtoupper($work['fname']);
        ?>&nbsp;<?php echo strtoupper($work['middlename']);
        ?>&nbsp;<?php echo strtoupper($work['lname']);
        ?></a></td>
                    <td>

                      <?php echo $this->Form->input("abs_date[" . $work['id'] . "]", array('class' => 'form-control abdatepicker abdate date' . $work['id'], 'id' => 'datepicker', 'disabled', 'label' => false, 'placeholder' => 'Select dates')); ?>

                    </td>
                    <td><?php echo ucfirst($work['emp_status']); ?></td>
                    <td><?php echo $work['mobile']; ?></td>
                    <?php if ($rolepresent == 16) {?>
                    <td> <?php if ($emp_sal_det) {?>
                      <table>
                        <thead>
                          <?php if ($work['emp_status'] != 'contract') {?>
                          <tr>
                            <td><b>Basic Salary :</b> </td>
                            <td><?php echo number_format($emp_sal_det['basic_salary'], 2); ?></td>
                          </tr>
                          <tr>
                            <td><b>DA :</b> </td>
                            <td> <?php echo number_format($emp_sal_det['da_amt'], 2); ?></td>
                          </tr>
                          <tr>
                            <td><b>HRA :</b> </td>
                            <td> <?php echo number_format($emp_sal_det['hra_amt'], 2); ?></td>
                          </tr>
                          <tr>
                            <td><b>CCA :</b> </td>
                            <td><?php echo number_format($emp_sal_det['cca_amt'], 2); ?></td>
                          </tr>
                          <tr>
                            <td><b>LTA :</b> </td>
                            <td> <?php echo number_format($emp_sal_det['lta_amt'], 2); ?></td>
                          </tr>
                          <tr>
                            <td><b>Spl All :</b> </td>
                            <td> <?php echo number_format($emp_sal_det['spl_all'], 2); ?></td>
                          </tr>
                          <?php }?>

                          <tr>
                            <td style="font-weight:bold;color:green;font-size: 14px;">Gross Total * :</td>
                            <td> <?php echo number_format($emp_sal_det['total'], 2); ?></td>
                          </tr>
                        </thead>
                      </table>

                      <?php }?>
                    </td>
                    <?php }?>
                    <td><?php echo date('d-m-Y', strtotime($work['joiningdate'])); ?></td>
                    <?php if ($role_id == '1' || $role_id == '5' || $role_id == '8' || $role_id == '16') {
            ?><td><?php echo $this->Html->link('Edit', ['action' => 'add', $work->id], ['class' => 'btn btn-primary']);if (!empty($att_exist['id'])) { ?><a
                        href="<?php echo SITE_URL; ?>admin/employees/delete_atn/<?php echo $work->id; ?>" class=""
                        onclick="javascript: return confirm('Are you sure do you want to delete this')"
                        style="margin-left:10px"><span class="fa fa-times"></span></a>
                      <?php }if ($role_id == '1') {
                ?><a href="<?php echo SITE_URL; ?>admin/employees/delete/<?php echo $work->id; ?>" class=""
                        onclick="javascript: return confirm('Are you sure do you want to delete this')"><span
                          class="fa fa-trash"></span></a><?php
}

            ?></td> <?php }?>
                  </tr><?php $counter++;
    }
} else {
    ?><tr>
                    <td colspan="10" style="text-align:center;">NO Data Available</td>
                  </tr><?php
}

?></tbody>
              </table>
              <?php echo $this->Form->end(); ?>
            </div>
            <!-- /.box -->


          </div>

          <div id="tab-2" class="tab-content1" style="padding:0px">
            <?php echo $this->Form->create('', array('id' => 'form12', 'action' => 'take_other_attendance', 'method' => 'post')); ?>
            <div class="box-header"><i class="fa fa-search" aria-hidden="true"></i>
              <h3 class="box-title">Other Staff List </h3>
              <div class="submit" style="float:right;">

                <!-- <input type="submit" style="margin-right:10px;" class="btn btn-info" id="YourbuttonIds" name="status"
                  value="Submit Attendance"> -->
                <input type="button" class="btn btn-info validate" id="absentbtn_oth" value="Absent"
                  style="font-weight:800">
                <a target="_blank" class="btn btn-info add_employee"
                  href="<?php echo ADMIN_URL ?>employees/add_staff"><b><i class="fa fa-plus " aria-hidden="true"></i>Add
                    Staff</b></a>
                <a href="<?php echo SITE_URL; ?>admin/employeeattendance/attendance_csv"
                  class="btn btn-success  m-top10"><i class="fa fa-plus" aria-hidden="true"></i>Import Attendance
                </a>
                <a href="<?php echo SITE_URL; ?>admin/employees/addcsv" class="btn btn-success  m-top10"><i
                    class="fa fa-plus" aria-hidden="true"></i>Import Payroll Entry
                </a>
              </div>
            </div>
            <div class="box box-info" id="updt" style="padding:10px">

              <?php echo $this->Flash->render();

?><table id="emp_att1" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th><input type="checkbox" id="selectall_oth"></th>
                    <th>S.No.</th>
                    <th>Name</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Mobile</th>
                    <th>Salary Management</th>

                    <th>Joining Date</th>
                    <?php if ($role_id == '1' || $role_id == '5' || $role_id == '8' || $role_id = '16') {
    ?><th>Action</th><?php
}

?>

                  </tr>
                </thead>
                <tbody><?php $page = $this->request->params['paging']['Students']['page'];
$limit = $this->request->params['paging']['Students']['perPage'];
$counter = ($page * $limit) - $limit + 1;

if (isset($staff) && !empty($staff)) {
    //}

    foreach ($staff as $work) {

        $emp_sal_det = $this->Comman->findemplobasic($work['id']);
        $salary_mode = $this->Comman->findpaymentmode($emp_sal_det['payment_mode']);
        //pr($emp_sal_det);die;
        ?><?php $att_exist = $this->Comman->findAttendancemon($work['id'], $date);
        //pr($att_exist);

        //pr($period);die;
        ?>
                  <tr <?php if (!empty($att_exist['id'])) {?>style="color:red" <?php }?>>
                    <td><input type="checkbox" class="oemp_id" name="oid[]" value=<?php echo $work['id'];
        ?>></td>
                    <td><?php echo $counter; ?></td>
                    <!-- <td><img src="<?php //echo $this->request->webroot;?>img/studentlist_img.png" </td>-->

                    <td <?php if ($att_exist > 0) {?> class="tooltips" <?php }?>><?php if (!empty($att_exist)) {?>
                      <span>Absent on &nbsp; <?php echo $att_exist; ?>&nbsp;</span> <?php }?>
                      <a title="View Staff Detail"
                        href="<?php echo ADMIN_URL; ?>employees/view/<?php echo $work['id']; ?>"><?php echo strtoupper($work['fname']);
        ?>&nbsp;<?php echo strtoupper($work['middlename']);
        ?>&nbsp;<?php echo strtoupper($work['lname']);
        ?></a><?php

        ?></td>
                    <td>

                      <?php echo $this->Form->input("abs_date[" . $work['id'] . "]", array('class' => 'form-control abdatepicker abdate date' . $work['id'], 'id' => 'datepicker', 'disabled', 'label' => false, 'placeholder' => 'Select dates')); ?>

                    </td>
                    <td><?php echo ucfirst($work['emp_status']); ?></td>
                    <td><?php echo $work['mobile'];
        ?></td>
                    <td> <?php if ($emp_sal_det) {?>
                      <table>
                        <thead>

                          <tr>
                            <td><b>Basic Salary :</b> </td>
                            <td> <?php echo number_format($emp_sal_det['basic_salary'], 2); ?></td>
                          </tr>
                          <tr>
                            <td><b>DA :</b> </td>
                            <td> <?php echo number_format($emp_sal_det['da_amt'], 2); ?></td>
                          </tr>
                          <tr>
                            <td><b>HRA :</b> </td>
                            <td> <?php echo number_format($emp_sal_det['hra_amt'], 2); ?></td>
                          </tr>
                          <tr>
                            <td><b>CCA :</b> </td>
                            <td><?php echo number_format($emp_sal_det['cca_amt'], 2); ?></td>
                          </tr>
                          <tr>
                            <td><b>LTA :</b> </td>
                            <td> <?php echo number_format($emp_sal_det['lta_amt'], 2); ?></td>
                          </tr>
                          <tr>
                            <td><b>Grade Pay :</b> </td>
                            <td> <?php echo number_format($emp_sal_det['grade_pay'], 2); ?></td>
                          </tr>
                          <tr>
                            <td><b>Spl all :</b> </td>
                            <td> <?php echo number_format($emp_sal_det['spl_all'], 2); ?></td>
                          </tr>

                          <tr>
                            <td style="font-weight:bold;color:green;font-size: 14px;">Gross Total * :</td>
                            <td> <?php echo number_format($emp_sal_det['total'], 2); ?></td>
                          </tr>
                        </thead>
                      </table>

                      <?php }?>
                    </td>

                    <td><?php echo date('d-m-Y', strtotime($work['joiningdate']));
        ?></td>
                    <?php if ($role_id == '1' || $role_id == '5' || $role_id == '8' || $role_id == '16') {
            ?><td><?php echo $this->Html->link('Edit', ['action' => 'add',
                $work->id,
            ], ['class' => 'btn btn-primary']);
            if (!empty($att_exist['id'])) { ?><a
                        href="<?php echo SITE_URL; ?>admin/employees/delete_atn/<?php echo $work->id; ?>" class=""
                        onclick="javascript: return confirm('Are you sure do you want to delete this')"
                        style="margin-left:10px"><span class="fa fa-times"></span></a>
                      <?php }if ($role_id == '1') {
                ?><a href="<?php echo SITE_URL; ?>admin/employees/delete/<?php echo $work->id; ?>" class=""
                        onclick="javascript: return confirm('Are you sure do you want to delete this')"><span
                          class="fa fa-trash"></span></a><?php
}

            ?></td> <?php }?>


                  </tr><?php $counter++;

    }
} else {
    ?><tr>
                    <td colspan="10" style="text-align:center;">NO Data Available</td>
                  </tr><?php
}

?></tbody>
              </table>

            </div>
            <script>
            $(document).ready(function() {

                $("#absentbtn_oth").click(function() {
                    if ($('.oemp_id').filter(':checked').length < 1) {
                      alert('Please select atleast one');
                      return false;
                    } else {
                      $('#otherModal').modal('show');
                    }

                  }

                );
              }

            );
            </script>
            <div class="modal fade" id="otherModal" role="dialog">
              <div class="modal-dialog">

                <div class="modal-content" style="background-color:#ecf0f5;">
                  <div class="modal-header" style="background-color: #3c8dbc;"><button type="button" class="close"
                      data-dismiss="modal">&times;
                    </button>
                    <h4>Select Absent Periods</h4>
                  </div>
                  <div class="modal-body" style="width:50%; margin:auto; background-color:#FFF;">
                    <div class=" row">
                      <div class="form-group">
                        <div class="col-sm-12">
                          <div>
                            <div class="col-sm-12">
                              <label>Status</label>
                              <select name="status">
                                <option value="A">Absent</option>
                                <option value="PR">Pre-Lunch Absent</option>
                                <option value="PL">Post-Lunch Absent</option>
                              </select>
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="form-group">
                        <div class="col-sm-12"><label>Remarks</label><textarea name="remark"
                            style="width:100%"></textarea></div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #FFF;background-color:#ecf0f5;">
                  <div class="submit">
                    <!-- <input type="submit" class="btn btn-info pull-right" id="absnt_chk" title="ABSENT" style="display: block;" name="status" value="Absent"> -->
                    <input type="submit" style="margin-right:10px;" class="btn btn-info">
                    <button type="button" class="btn btn-default pull-left" id="modalcancel"
                      data-dismiss="modal">Close</button></div>
                </div>
              </div>
            </div>
          </div>


          <!-- /.box-body -->
        </div>
        <?php echo $this->Form->end(); ?>
      </div>
    </div>
    <!-- /.row -->

    <script>
    $(function() {
      var date = new Date();
      var startDate = new Date(date.getFullYear(), date.getMonth(), 1);
      var endDate = new Date(date.getFullYear(), date.getMonth() + 1, 0);
      startDate.setDate(startDate.getDate() + 0);
      endDate.setDate(endDate.getDate() + 0);
      $(".abdatepicker").datepicker({
        startDate: startDate,
        endDate: endDate,
        multidate: true,
        format: 'dd-mm-yyyy'
      });
    });
    </script>
</div>
</section>
<!-- /.content -->
</div>
<script>
$(document).ready(function() {
    //alert();

    $("#selectall").click(function() {

        //alert();
        if ($(this).is(':checked')) {
          //alert();;
          $('.emp_id').prop('checked', true);
          $('.abdate').prop('disabled', false);
        } else {
          $('.emp_id').prop('checked', false);
          $('.abdate').prop('disabled', true);


        }

        //$(this).toggleClass('emp_id');
      }

    );

    return false;
  }

);
</script>
<script>
$(document).ready(function() {
    //alert();

    $("#selectall_oth").click(function() {

        //alert();
        if ($(this).is(':checked')) {
          //alert();;
          $('.oemp_id').prop('checked', true);
          $('.abdate').prop('disabled', false);
        } else {
          $('.oemp_id').prop('checked', false);
          $('.abdate').prop('disabled', true);

        }

        //$(this).toggleClass('emp_id');
      }

    );

    return false;
  }

);
</script>
<script>
$(document).ready(function() {
    //alert();

    $(".emp_id,.oemp_id").click(function() {
        var id = $(this).val();

        if ($(this).is(':checked')) {

          $('.date' + id).prop('disabled', false);
          $('.date' + id).prop('required', true);

        } else {
          $('.date' + id).val();
          $('.date' + id).prop('disabled', true);
        }

        //$(this).toggleClass('emp_id');
      }

    );

    return false;
  }

);
</script>
<script>
$(document).ready(function() {
    $('#YourbuttonIds').click(function() {

        //alert();
        if ($('.emp_id').filter(':checked').length < 1) {
          alert('Please select atleast one');
          return false;
        }

        //console.log("ok");
      }

    );

    $('#absnt_chk').click(function() {

        //alert();
        if ($('.absent_check').filter(':checked').length < 1) {
          alert('Please select atleast one');
          return false;
        }

        //console.log("ok");
      }

    );

  }

);
</script>
<!-- /.content-wrapper -->



<!-- container -->
<script>
$(document).ready(function() {

  $('ul.atn_tab li').click(function() {
    var tab_id = $(this).attr('data-tab');

    $('ul.atn_tab li').removeClass('current');
    $('.tab-content1').removeClass('current');

    $(this).addClass('current');
    $("#" + tab_id).addClass('current');
  })

})
</script>
<script>
$(document).ready(function() {

  $("#form1234").bind("submit", function(event) {


    $.ajax({
      async: true,
      data: $("#form1234").serialize(),
      dataType: "html",
      type: "POST",
      beforeSend: function() {
        $("#load2").show();
        $(".modal").modal('hide');
      },
      url: "<?php echo ADMIN_URL; ?>employees/take_attendance",

      success: function(data) {
        //alert(data);
        $("#load2").hide();


        $('#emp_attn_sub').trigger('click');

      },

    });

    return false;
  });
});
</script>
<script>
$(document).ready(function() {

  $("#form12").bind("submit", function(event) {


    $.ajax({
      async: true,
      data: $("#form12").serialize(),
      dataType: "html",
      type: "POST",
      beforeSend: function() {
        $(".modal").modal('hide')
        $("#load2").show();
      },
      url: "<?php echo ADMIN_URL; ?>employees/take_other_attendance",

      success: function(data) {
        //alert(data);
        $("#load2").hide();;

        $('#emp_attn_sub').trigger('click');

      },

    });

    return false;
  });
});
</script>
