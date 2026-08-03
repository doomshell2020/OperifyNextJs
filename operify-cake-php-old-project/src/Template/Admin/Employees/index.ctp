<style>
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
</style>
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
<!-- <script src="plugins/datatables/jquery.dataTables.min.js" type="text/javascript"></script> -->
<script src="plugins/datatables/dataTables.bootstrap.min.js" type="text/javascript"></script>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <div id="load2" style="display:none;"></div>
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Employees Manager </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo ADMIN_URL; ?>employees"><i class="fa fa-home"></i>Home</a></li>
            <li><a href="<?php echo ADMIN_URL; ?>employees">Manage Employees</a></li>
        </ol>
    </section><?php $role_id = $this->request->session()->read('Auth.User.role_id');
                ?>
    <!-- Main content -->

    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box"><?php echo $this->Form->create('', array('id' => 'form123', 'action' => 'staff_sms', 'method' => 'post'));

                                    ?><div class="box-header " style="display: flex;
                          align-items: center;"><i class="fa fa-search" aria-hidden="true"></i>
                        <h3 class="box-title" style="flex:1">Teachers List </h3>

                        <a href="<?php echo SITE_URL; ?>admin/report/employees_login_details" class="btn btn-primary">
                            <i class="fa fa-file-excel-o" aria-hidden="true"></i> Export Login Details </a>

                        <a href="<?php echo SITE_URL; ?>admin/report/employees_login_details_pdf" class="btn btn-primary"> <i class="fa fa-file-pdf-o" aria-hidden="true"></i> Export Login
                            Details Pdf </a>

                        <div class="submit" style="float:right;">

                            <?php if ($role_id == '5' || $role_id == '105' || $role_id == '8' || $role_id == '1' || $role_id == '6') { ?> <a target="_blank" class="btn btn-info add_employee" href="<?php echo ADMIN_URL ?>employees/export_det"><b>Export</b></a>

                                <input type="button" style="margin-right:10px;" class="btn btn-info inv btn4 btn_pdf myscl-btn date inv globalModals" href="<?php echo SITE_URL; ?>admin/Employees/sendsms_staff" data-target="#globalModal" data-toggle="modal" name="Invite" value="Send SMS">

                                <a target="_blank" class="btn btn-info add_employee" href="<?php echo ADMIN_URL ?>employees/add"><b><i class="fa fa-plus " aria-hidden="true"></i>Add
                                        Teacher</b></a><?php } ?>
                        </div>


                        <div class="modal" id="globalModal" tabindex="-1" role="dialog" aria-labelledby="esModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content modal-content2">
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
                        <?php if ($role_id == '1') {
                        ?><a href="<?php echo SITE_URL; ?>admin/employees/addcsv" class="btn btn-success pull-right m-top10"><i class="fa fa-plus" aria-hidden="true"></i>Add CSV</a><?php } ?>
                    </div>
                    <!-- Horizontal Form -->
                    <div class="box box-info" id="updt">
                        <div class="box-body"><?php echo $this->Flash->render(); ?>
                            <div class="table-responsive">
                                <table id="emp_att" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <?php if ($role_id == '5' || $role_id == '8' || $role_id == '6') {
                                            ?><th width="30px"><input type="checkbox" id="selectall"></th> <?php } ?>
                                            <th>S.No.</th>
                                            <th>Image</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <!-- <th>Password</th> -->

                                            <th>Mobile</th>
                                            <th>Father/Husband Name</th>
                                            <th>DOB</th>
                                            <th>Joiningdate</th>

                                            <?php if ($role_id == '5' || $role_id == '8' || $role_id == '6' || $role_id == '1' || $role_id == '105') {
                                            ?><th>Action</th><?php } ?>
                                        </tr>
                                    </thead>
                                    <style>
                                        #emp_att thead tr th:first-child:after {
                                            display: none;
                                        }
                                    </style>
                                    <tbody><?php $page = $this->request->params['paging']['Students']['page'];
                                            $limit = $this->request->params['paging']['Students']['perPage'];
                                            $counter = ($page * $limit) - $limit + 1;

                                            if (isset($students) && !empty($students)) {
                                                foreach ($students as $work) { //pr($work); die;

                                            ?><?php $att_exist = $this->Comman->findAttendance($work['id']);
                                                    //pr($att_exist);

                                                    //pr($period);die;
                                                ?>
                                        <tr <?php if (!empty($att_exist['id'])) { ?>style="color:red" <?php } ?>>


                                            <?php if ($role_id == '5' || $role_id == '8' || $role_id == '6') {
                                            ?> <td><input type="checkbox" class="emp_id checkboxs" name="id[]" value=<?php echo $work['id'];
                                                                                                                        ?>></td> <?php } ?>
                                            <td><?php echo $counter; ?></td>

                                            <?php $db = $this->request->session()->read('Auth.User.db'); ?>
                                            <td>
                                                <?php if ($work['file']) {  ?>
                                                    <img src="<?php echo SITE_URL; ?><?php echo $db . '_image'; ?>/employees/<?php echo $work['file']; ?>" height="100px" width="100px">
                                                <?php } else { ?>
                                                    <img src="<?php echo SITE_URL; ?>images/NOIMAGE.JPG" height="100px" width="100px">
                                                <?php } ?>

                                            </td>

                                            <td <?php if ($att_exist > 0) { ?> class="tooltips" <?php } ?>>
                                                <?php if ($att_exist > 0) { ?>
                                                    <span>Absent on &nbsp;
                                                        <?php echo $att_exist['absent_periods']; ?>&nbsp;Periods</span>
                                                <?php } ?>
                                                <?php if ($role_id == '1' || $role_id == '5' || $role_id == '8') {
                                                ?><?php echo ucfirst(strtolower($work['fname']));
                                                    ?>&nbsp;<?php echo ucfirst(strtolower($work['middlename']));
                                                            ?>&nbsp;<?php echo ucfirst(strtolower($work['lname']));
                                                                    ?><?php
                                                    } else {
                                                ?><?php echo strtoupper($work['fname']);
                                                    ?>&nbsp;<?php echo strtoupper($work['middlename']);
                                                            ?><?php echo strtoupper($work['lname']);
                                                ?><?php
                                                    }

                                                ?>
                                            </td>
                                            <td><?php echo $work['email']; ?></td>
                            
                                            <td><?php echo $work['mobile'];
                                                ?></td>
                                            <td><?php echo $work['f_h_name'];
                                                ?></td>
                                            <td>
                                                <?php if (date('d-m-Y', strtotime($work['dob'])) == "01-01-1970") {
                                                        echo "--";
                                                    } else {
                                                        echo date('d-m-Y', strtotime($work['dob']));
                                                    }
                                                ?>
                                            </td>
                                            <td><?php echo date('d-m-Y', strtotime($work['joiningdate'])); ?></td>
                                            <td>
                                                <?php if ($role_id == '1' || $role_id == '5' || $role_id == '8' || $role_id == '6' || $role_id == '105') {
                                                ?>
                                                    <a href="<?php echo SITE_URL; ?>admin/employees/emp_edit/<?php echo $work['id']; ?>"><i class="fas fa-edit" style="font-size: 16px !important;" aria-hidden="true"></i></a>
                                                    <a class="drop" data-id="<?php echo $work['id']; ?>" data-name="<?php echo ucfirst($work['fname'] . ' ' . $work['middlename'] . ' ' . $work['lname']); ?>" title="Drop out"><i class="fa fa-ban fa-1x" style="font-size: 16px !important;" aria-hidden="true"></i></a>
                                                <?php
                                                    }
                                                ?>
                                                <?php
                                                    echo $this->Html->link('', ['action' => 'delete', $work->id], ['class' => 'fas fa-trash-alt', 'style' => 'font-size: 16px !important; color:#cd0404; margin-right:4px !important;', "onClick" => "javascript: return confirm('Are you sure you want to delete this Employee')"]);
                                                ?>
                                            </td>
                                        </tr><?php $counter++;
                                                }
                                            } else {
                                                ?><tr>
                                        <td colspan="10" style="text-align:center;">NO Data Available</td>
                                    </tr><?php
                                            }

                                            ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.box --><?php echo $this->Form->end();

                                        ?>
                    </div>
                    <!-- /.box-body -->
                </div>
            </div>
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<script>
    $(document).ready(function() {
            //alert();

            $("#selectall").click(function() {
                    if ($(this).is(':checked')) {
                        $('.emp_id').prop('checked', true);
                    } else {
                        $('.emp_id').prop('checked', false);
                    }
                }
            );
            return false;
        }
    );
</script>

<!-- /.content-wrapper -->
<script type="text/javascript">
    $('.inv').click(function() {
        var sd = $(".checkboxs:checked").length;
        if (sd == 0) {
            alert("Please Select Atleast one Staff")
            return false;
        } else {
            return true;
        }
    });
</script>

<div class="modal" id="absentModalkoi11" role="dialog" aria-labelledby="esModalLabel" aria-hidden="true" style="display:none">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <?php echo $this->Form->create($enquires, array('id' => 'attn_form', 'enctype' => 'multipart/form-data')); ?>
            <div class="modal-header" style="padding:0px; padding-left:15px; padding-right:15px;  line-height:20px;">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Drop <span id="empName"></span>?</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" name="empId" class="empId">
                <div class="row">
                    <div class="col-sm-3">
                        <label><b> Drop Date:</b></label>
                    </div>
                    <div class="col-sm-6">
                        <?php echo $this->Form->input("drop_date", array('class' => 'form-control abdatepicker', 'id' => 'datepicker', 'label' => false, 'placeholder' => 'Select dates', 'required')); ?>
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
<script>
    $(document).ready(function() {
        $('#sal_table23').DataTable({
            "paging": false,
            "ordering": false,
        });
        $(document).on("click", ".drop", function() {
            if (confirm('Are you sure you want to drop out this employee')) {
                var id = $(this).data('id');
                var name = $(this).data('name');
                $('#empName').html(name);
                $('.empId').val(id);
                $("#absentModalkoi11").modal('show');
            }
        });
        $("#datepicker").datepicker();
        $("#attn_form").bind("submit", function(event) {
            $.ajax({
                async: true,
                data: $("#attn_form").serialize(),
                dataType: "html",
                type: "POST",
                beforeSend: function() {
                    //Show image container
                    $("#load2").show();
                },
                url: "<?php echo ADMIN_URL; ?>Employees/dropsubmit",

                success: function(data) {
                    //alert(data);
                    window.location.href = '<?php echo ADMIN_URL; ?>employees/index';
                },

            });

            return false;
        });
    });
</script>