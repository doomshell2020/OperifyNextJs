<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1><i class="fa fa-th-list"></i> Manage Permission Module </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo ADMIN_URL; ?>PermissionModules"><i class="fa fa-home"></i>Home</a></li>
            <li><a href="<?php echo ADMIN_URL; ?>PermissionModules/index">Manage Permission Module</a></li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">
                            <i class="fa fa-search"></i> <?php if ($id) { ?> Set Permissions for User- <b style="color:green;"><?php echo $username; ?></b> <?php } else { ?> View Permission Modules List <?php } ?>
                        </h3>
                        <?php echo $this->Flash->render(); ?>
                    </div>
                    <!-- /.box-header -->
                    <?php echo $this->Form->create($classes, array(

                        'class' => 'form-horizontal',
                        'id' => 'sevice_sform',
                        'enctype' => 'multipart/form-data',
                        'validate',
                    )); ?>
                    <div class="box-body">
                        <div class="manag-stu">
                            <div style="float:center; width:100%">


                                <div class="title-field-group">

                                    <?php if ($id) { ?>

                                        <input type="hidden" name="user_id" value="<?php echo $id; ?>">
                                        <input type="hidden" name="naction" value="<?php echo $id; ?>">
                                        <script>
                                            $(document).ready(function() {

                                                var emp = '<?php echo $id; ?>';
                                                var emp_roleid = '<?php echo $employees_rollid['role_id']; ?>';
                                               
                                                $.ajax({

                                                    type: 'POST',

                                                    url: '<?php echo ADMIN_URL; ?>permissionmodules/calculatepermission',

                                                    data: {
                                                        'empid': emp
                                                    },

                                                    success: function(data) {

                                                        $("#amountrt").html(data);
                                                    }

                                                });




                                            });
                                        </script>

                                    <?php } else { ?>
                                        <div class="form-group">
                                            <div class="col-sm-3">
                                                <label>User Email<span style="color:red;">*</span></label>
                                                <?php
                                                echo $this->Form->input('user_id', array('class' => 'form-control', 'type' => 'select', 'id' => 'emp-type', 'empty' => '--Select--', 'options' => $employees, 'label' => false, 'required')); ?>
                                            </div>
                                        </div> <!-- .field-group -->

                                        <script>
                                            $(document).ready(function() {

                                                //--------------------------------------------

                                                $('#emp-type').on('change', function() {

                                                    var emp = $('#emp-type').val();

                                                    $.ajax({

                                                        type: 'POST',

                                                        url: '<?php echo ADMIN_URL; ?>permissionmodules/calculatepermission',

                                                        data: {
                                                            'empid': emp
                                                        },

                                                        success: function(data) {

                                                            $("#amountrt").html(data);
                                                        }

                                                    });


                                                });

                                            });
                                        </script>
                                    <?php } ?>
                                    <script>
                                        $(document).ready(function() {
                                            $(".parenth").each(function(index) {
                                                var group = $(this).data("group");
                                                var parent = $(this);

                                                parent.change(function() { //"select all" change
                                                    $(group).prop('checked', parent.prop("checked"));
                                                });
                                                $(group).change(function() {
                                                    // parent.prop('checked', false);
                                                    parent.prop('checked', true);
                                                    if ($(group + ':checked').length == 0) {
                                                        parent.prop('checked', false);
                                                    }
                                                });
                                            });
                                        });
                                    </script>




                                    <div class="form-group">
                                        <div class="col-sm-6"></div>

                                        <div class="col-sm-6">
                                            <div class="submit">
                                                <input type="submit" class="btn btn-info pull-left" value="Update Rights" title="Update">
                                            </div>
                                        </div><br>

                                    </div>

                                    <div id="amountrt">
                                        <style>
                                            .paperclip .checkk:checked,
                                            .paperclip .checkk:not(:checked) {
                                                position: absolute;
                                                left: -9999px;
                                            }

                                            .paperclip .checkk:checked+label,
                                            .paperclip .checkk:not(:checked)+label {
                                                position: relative;
                                                padding-left: 28px;
                                                cursor: pointer;
                                                line-height: 20px;
                                                display: inline-block;
                                                color: #666;
                                            }

                                            .paperclip .checkk:checked+label:before,
                                            .paperclip .checkk:not(:checked)+label:before {
                                                content: '\f0c6';
                                                font-family: "Font Awesome 5 Free";
                                                color: #000;
                                                font-weight: 900;
                                                position: absolute;
                                                left: 0;
                                                top: 0;
                                                width: 18px;
                                                height: 18px;
                                            }

                                            .paperclip .checkk:checked+label:after,
                                            .paperclip .checkk:not(:checked)+label:after {
                                                content: '\f0c6';
                                                font-family: "Font Awesome 5 Free";
                                                color: #F00;
                                                font-weight: 900;
                                                position: absolute;
                                                left: 0;
                                                top: 0;
                                                width: 18px;
                                                height: 18px;
                                            }

                                            .paperclip .checkk:not(:checked)+label:after {
                                                opacity: 0;
                                                -webkit-transform: scale(0);
                                                transform: scale(0);
                                            }

                                            .paperclip .checkk:checked+label:after {
                                                opacity: 1;
                                                -webkit-transform: scale(1);
                                                transform: scale(1);
                                            }
                                        </style>




                                        <?php if ($id) {

                                            $empid = $id;
                                        } ?>


                                            <div class="col-sm-6">
                                                <div class="title-div">
                                                    <table width="80%">
                                                        <tbody>
                                                            <tr>
                                                                <td width="30%"><strong style="font-size: 18px;">Module Name :</strong></td>


                                                            </tr>


                                                            <tr style="background:#4993d7;">
                                                                <td width="30%">
                                                                    <input type="checkbox" name="module1[]" class="parenth" data-group=".group1" value="Prospectus" id="2_arrgruop" <?php if (in_array("Prospectus", $module)) { ?> checked="checked" <?php } ?>>
                                                                    <input type="hidden" name="module[]" value="Prospectus"><strong style="color:#fff;"> Prospectus</strong>
                                                                </td>

                                                                <td width="10%"></td>

                                                            </tr>



                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu1[0]" class="group1" value="Add Prospectus^Enquires^prospectus_add" id="2_arrgruop" <?php if (in_array("Add Prospectus", $menu)) { ?> checked="checked" <?php } ?>> Add Prospectus
                                                                    <input type="checkbox" id="test1" name="menu1a[0]" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Add Prospectus", "Enquires", "prospectus_add");
                                                                                                                        if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> class="checkk" value="1">
                                                                    <label for="test1"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>

                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td width="10%" class="paperclip"><input type="checkbox" class="group1" name="menu1[1]" id="2_V" value="View All Prospectus^Enquires^prospectus_index" <?php if (in_array("View All Prospectus", $menu)) { ?> checked="checked" <?php } ?>> View All Prospectus <input type="checkbox" id="test2" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "View All Prospectus", "Enquires", "prospectus_index");
                                                                                                                                                                                                                                                                                                                                                                if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> class="checkk" value="1" name="menu1a[1]">
                                                                    <label for="test2"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td width="10%" class="paperclip"><input type="checkbox" class="group1" name="menu1[2]" id="2_V" value="Register Student^Students^applicant_add" <?php if (in_array("Register Student", $menu)) { ?> checked="checked" <?php } ?>> Register Student <input type="checkbox" id="test3" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Register Student", "Students", "applicant_add");
                                                                                                                                                                                                                                                                                                                                                    if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> class="checkk" value="1" name="menu1a[2]">
                                                                    <label for="test3"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="13%" class="paperclip"><input type="checkbox" class="group1" name="menu1[3]" id="2_V" value="Invite/Reject/Approved/Admission^report^prospect" <?php if (in_array("Invite/Reject/Approved/Admission", $menu)) { ?> checked="checked" <?php } ?>> Invite/Reject/Approved &amp;&amp; Admission <input type="checkbox" id="test4" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Invite/Reject/Approved/Admission", "report", "prospect");
                                                                                                                                                                                                                                                                                                                                                                                                        if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> class="checkk" value="1" name="menu1a[3]">
                                                                    <label for="test4"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="10%" class="paperclip"><input type="checkbox" class="group1" name="menu1[4]" id="2_V" value="Approved Registration^students^approvedprospect" <?php if (in_array("Approved Registration", $menu)) { ?> checked="checked" <?php } ?>> Approved Registration<input type="checkbox" id="test5" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Approved Registration", "students", "approvedprospect");
                                                                                                                                                                                                                                                                                                                                                                                    if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu1a[4]">
                                                                    <label for="test5"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="10%" class="paperclip"><input type="checkbox" class="group1" name="menu1[5]" id="2_V" value="Rejected Registration^students^rejectprospect" <?php if (in_array("Rejected Registration", $menu)) { ?> checked="checked" <?php } ?>> Rejected Registration<input type="checkbox" id="test6" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Rejected Registration", "students", "rejectprospect");
                                                                                                                                                                                                                                                                                                                                                                                    if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu1a[5]">
                                                                    <label for="test6"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="title-div">
                                                    <table width="80%">
                                                        <tbody>
                                                            <tr>
                                                                <td width="30%">&nbsp;</td>


                                                            </tr>
                                                            <tr style="background:#4993d7;">
                                                                <td width="30%"><input type="checkbox" name="module2[]" <?php if (in_array("Admission", $module)) { ?> checked="checked" <?php } ?> class="parenth" data-group=".group2" value="Admission" id="2_arrgruop"><input type="hidden" name="module[]" value="Admission"><strong style="color:#fff;"> Admission</strong></td>

                                                                <td width="10%"></td>

                                                            </tr>



                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu2[0]" class="group2" value="Add Admission^students^add" id="2_arrgruop" <?php if (in_array("Add Admission", $menu)) { ?> checked="checked" <?php } ?>> Add Admission<input type="checkbox" id="test7" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Add Admission", "students", "add");
                                                                                                                                                                                                                                                                                                                                                        if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu2a[0]">
                                                                    <label for="test7"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td width="10%" class="paperclip"><input type="checkbox" class="group2" name="menu2[1]" id="2_V" value="Manage Admission^students^index" <?php if (in_array("Manage Admission", $menu)) { ?> checked="checked" <?php } ?>> Manage Admission<input type="checkbox" id="test8" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Manage Admission", "students", "index");
                                                                                                                                                                                                                                                                                                                                                            if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu2a[1]">
                                                                    <label for="test8"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td width="10%" class="paperclip"><input type="checkbox" class="group2" name="menu2[2]" id="2_V" value="Drop Out Students^students^drop" <?php if (in_array("Drop Out Students", $menu)) { ?> checked="checked" <?php } ?>> Drop Out Students<input type="checkbox" id="test9" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Drop Out Students", "students", "drop");
                                                                                                                                                                                                                                                                                                                                                            if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu2a[2]">
                                                                    <label for="test9"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="10%" class="paperclip"><input type="checkbox" class="group2" name="menu2[3]" id="2_V" value="Promote Students^students^promote" <?php if (in_array("Promote Students", $menu)) { ?> checked="checked" <?php } ?>> Promote Students<input type="checkbox" id="test10" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Promote Students", "students", "promote");
                                                                                                                                                                                                                                                                                                                                                                if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu2a[3]">
                                                                    <label for="test10"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="10%" class="paperclip"><input type="checkbox" <?php if (in_array("SMS Manager", $menu)) { ?> checked="checked" <?php } ?> class="group2" name="menu2[4]" id="2_V" value="SMS Manager^students^smsmanager"> SMS Manager<input type="checkbox" id="test11" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "SMS Manager", "students", "smsmanager");
                                                                                                                                                                                                                                                                                                                                                    if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu2a[4]">
                                                                    <label for="test11"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="10%" class="paperclip"><input type="checkbox" class="group2" name="menu2[5]" id="2_V" value="RTE Students^report^rtestudent" <?php if (in_array("RTE Students", $menu)) { ?> checked="checked" <?php } ?>> RTE Students<input type="checkbox" id="test12" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "RTE Students", "report", "rtestudent");
                                                                                                                                                                                                                                                                                                                                                    if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu2a[5]">
                                                                    <label for="test12"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                                                                </td>
                                                            </tr>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                      
                                            <?php if($employees_rollid['role_id'] == 5 || $employees_rollid['role_id'] == 1){ ?>
                                            <div class="col-sm-6">
                                                <div class="title-div">
                                                    <table width="80%">
                                                        <tbody>
                                                            <tr>
                                                                <td width="30%"><strong>&nbsp;</strong></td>


                                                            </tr>
                                                            <tr style="background:#4993d7;">
                                                                <td width="30%"><input type="checkbox" name="module3[]" <?php if (in_array("Fee Master", $module)) { ?> checked="checked" <?php } ?> class="parenth" data-group=".group3" value="Fee Master" id="2_arrgruop"><input type="hidden" name="module[]" value="Fee Master"><strong style="color:#fff;"> Fee Master</strong></td>

                                                                <td width="10%"></td>

                                                            </tr>



                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu3[0]" class="group3" value="Class Fee Structure^classfee^index" id="2_arrgruop" <?php if (in_array("Class Fee Structure", $menu)) { ?> checked="checked" <?php } ?>> Class Fee Structure <input type="checkbox" id="test1443" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Class Fee Structure", "classfee", "index");
                                                                                                                                                                                                                                                                                                                                                                                if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu3a[0]">
                                                                    <label for="test1443"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td width="10%" class="paperclip"><input type="checkbox" class="group3" name="menu3[1]" id="2_V" value="Fee Heads^feesheads^index" <?php if (in_array("Fee Heads", $menu)) { ?> checked="checked" <?php } ?>> Fee Heads <input type="checkbox" id="test13" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Fee Heads", "feesheads", "index");
                                                                                                                                                                                                                                                                                                                                        if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu3a[1]">
                                                                    <label for="test13"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td width="10%" class="paperclip"><input type="checkbox" class="group3" name="menu3[2]" id="2_V" value="Discounts Scheme^DiscountCategory^index" <?php if (in_array("Discounts Scheme", $menu)) { ?> checked="checked" <?php } ?>> Discounts Scheme <input type="checkbox" id="test14" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Discounts Scheme", "DiscountCategory", "index");
                                                                                                                                                                                                                                                                                                                                                                    if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu3a[2]">
                                                                    <label for="test14"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="13%" class="paperclip"><input type="checkbox" class="group3" name="menu3[3]" id="2_V" value="Deposit Fee^studentfees^view" <?php if (in_array("Deposit Fee", $menu)) { ?> checked="checked" <?php } ?>> Deposit Fee <input type="checkbox" id="test15" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Deposit Fee", "studentfees", "view");
                                                                                                                                                                                                                                                                                                                                                if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu3a[3]">
                                                                    <label for="test15"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td width="13%" class="paperclip"><input type="checkbox" class="group3" name="menu3[4]" id="2_V" value="Other Fee^studentfees^otherfees" <?php if (in_array("Other Fee", $menu)) { ?> checked="checked" <?php } ?>> Other Fee <input type="checkbox" id="test015" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Other Fee", "studentfees", "otherfees");
                                                                                                                                                                                                                                                                                                                                                if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu3a[4]">
                                                                    <label for="test015"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="13%" class="paperclip"><input type="checkbox" class="group3" name="menu3[5]" id="2_V" value="Student Complete Fee Manager^studentfees^studentview" <?php if (in_array("Student Complete Fee Manager", $menu)) { ?> checked="checked" <?php } ?>> Student Complete Fee Manager <input type="checkbox" id="test0015" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Student Complete Fee Manager", "studentfees", "studentview");
                                                                                                                                                                                                                                                                                                                                                                                                            if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu3a[5]">
                                                                    <label for="test0015"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                                                                </td>
                                                            </tr>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <?php } ?>
                                            <div class="col-sm-6">
                                                <div class="title-div">
                                                    <table width="80%">
                                                        <tbody>
                                                            <tr>
                                                                <td width="30%">&nbsp;</td>


                                                            </tr>
                                                            <tr style="background:#4993d7;">
                                                                <td width="30%"><input type="checkbox" name="module4[]" <?php if (in_array("School Staff", $module)) { ?> checked="checked" <?php } ?> class="parenth" data-group=".group4" value="School Staff" id="2_arrgruop"><input type="hidden" name="module[]" value="School Staff"><strong style="color:#fff;"> School Staff</strong></td>

                                                                <td width="10%"></td>

                                                            </tr>




                                                            <tr>
                                                                <td width="10%" class="paperclip"><input type="checkbox" class="group4" name="menu4[0]" id="2_V" value="Add Employee^payroll^emplist" <?php if (in_array("Add Employee", $menu)) { ?> checked="checked" <?php } ?>> Add Employee <input type="checkbox" id="test16" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Add Employee", "payroll", "emplist");
                                                                                                                                                                                                                                                                                                                                                    if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu4a[0]">
                                                                    <label for="test16"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="10%" class="paperclip"><input type="checkbox" class="group4" name="menu4[1]" id="2_V" value="Manage Employee^employees^index" <?php if (in_array("Manage Employee", $menu)) { ?> checked="checked" <?php } ?>> Manage Employee <input type="checkbox" id="test17" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Manage Employee", "employees", "index");
                                                                                                                                                                                                                                                                                                                                                            if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu4a[1]">
                                                                    <label for="test17"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu4[2]" class="group4" value="Class Teachers/Co-Class Teachers^Classections^classteacher" id="2_arrgruop" <?php if (in_array("Class Teachers/Co-Class Teachers", $menu)) { ?> checked="checked" <?php } ?>> Class Teachers/Co-Class Teachers <input type="checkbox" id="test18" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Class Teachers/Co-Class Teachers", "Classections", "classteacher");
                                                                                                                                                                                                                                                                                                                                                                                                                                if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu4a[2]">
                                                                    <label for="test18"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                                                                </td>
                                                            </tr>




                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>


                                        
                                            <div class="col-sm-6">
                                                <div class="title-div">
                                                    <table width="80%">
                                                        <tbody>
                                                            <tr>
                                                                <td width="30%"><strong>&nbsp;</strong></td>


                                                            </tr>
                                                            <tr style="background:#4993d7;">
                                                                <td width="30%"><input type="checkbox" name="module5[]" <?php if (in_array("Class Management", $module)) { ?> checked="checked" <?php } ?> class="parenth" data-group=".group5" value="Class Management" id="2_arrgruop"><input type="hidden" name="module[]" value="Class Managment"><strong style="color:#fff;"> Class Management</strong></td>

                                                                <td width="10%"></td>

                                                            </tr>


                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu5[0]" class="group5" value="Add Class^Classes^add" <?php if (in_array("Add Class", $menu)) { ?> checked="checked" <?php } ?>> Add Class <input type="checkbox" id="test19" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Add Class", "Classes", "add");
                                                                                                                                                                                                                                                                                                                            if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu5a[0]">
                                                                    <label for="test19"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu5[1]" class="group5" value="View Class^Classes^index" <?php if (in_array("View Class", $menu)) { ?> checked="checked" <?php } ?>> View Class<input type="checkbox" id="test20" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "View Class", "Classes", "index");
                                                                                                                                                                                                                                                                                                                                if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu5a[1]">
                                                                    <label for="test20"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu5[2]" class="group5" value="Add Section^Section^add" <?php if (in_array("Add Section", $menu)) { ?> checked="checked" <?php } ?>> Add Section<input type="checkbox" id="test21" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Add Section", "Section", "add");
                                                                                                                                                                                                                                                                                                                                if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu5a[2]">
                                                                    <label for="test21"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="10%" class="paperclip"><input type="checkbox" class="group5" name="menu5[3]" id="2_V" value="View Section^Section^index" <?php if (in_array("View Section", $menu)) { ?> checked="checked" <?php } ?>> View Section<input type="checkbox" id="test22" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "View Section", "Section", "index");
                                                                                                                                                                                                                                                                                                                                                if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu5a[3]">
                                                                    <label for="test22"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="10%" class="paperclip"><input type="checkbox" class="group5" name="menu5[4]" id="2_V" value="Class-Section Relation^Classections^index" <?php if (in_array("Class-Section Relation", $menu)) { ?> checked="checked" <?php } ?>> Class-Section Relation<input type="checkbox" id="test23" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Class-Section Relation", "Classections", "index");
                                                                                                                                                                                                                                                                                                                                                                                    if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu5a[4]">
                                                                    <label for="test23"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="10%" class="paperclip"><input type="checkbox" class="group5" name="menu5[5]" id="2_V" value="Take Student Attendance^students^classattendance" <?php if (in_array("Take Student Attendance", $menu)) { ?> checked="checked" <?php } ?>> Take Student Attendance<input type="checkbox" id="test24" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Take Student Attendance", "students", "classattendance");
                                                                                                                                                                                                                                                                                                                                                                                            if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu5a[5]">
                                                                    <label for="test24"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td width="10%" class="paperclip"><input type="checkbox" class="group5" name="menu5[6]" id="2_V" value="Take Staff Attendance^students^staffattendance" <?php if (in_array("Take Staff Attendance", $menu)) { ?> checked="checked" <?php } ?>> Take Staff Attendance<input type="checkbox" id="test246" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Take Staff Attendance", "students", "staffattendance");
                                                                                                                                                                                                                                                                                                                                                                                        if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu5a[6]">
                                                                    <label for="test246"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                                                                </td>
                                                            </tr>


                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="title-div">
                                                    <table width="80%">
                                                        <tbody>
                                                            <tr>
                                                                <td width="30%">&nbsp;</td>


                                                            </tr>
                                                            <tr style="background:#4993d7;">
                                                                <td width="30%"><input type="checkbox" name="module6[]" <?php if (in_array("Subject Managment", $module)) { ?> checked="checked" <?php } ?> class="parenth" data-group=".group6" value="Subject Managment" id="2_arrgruop"><input type="hidden" name="module[]" value="Subject Managment"><strong style="color:#fff;"> Subject Managment</strong></td>

                                                                <td width="10%"></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="10%" class="paperclip"><input type="checkbox" class="group6" name="menu6[0]" id="2_V" value="Subject^subjects^index" <?php if (in_array("Subject", $menu)) { ?> checked="checked" <?php } ?>> Subject<input type="checkbox" id="test25" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Subject", "subjects", "index");
                                                                                                                                                                                                                                                                                                                    if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> class="checkk" value="1" name="menu6a[0]">
                                                                    <label for="test25"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="10%" class="paperclip"><input type="checkbox" class="group6" name="menu6[1]" id="2_V" value="Subject-Class Relation^Subjectclass^index" <?php if (in_array("Subject-Class Relation", $menu)) { ?> checked="checked" <?php } ?>> Subject-Class Relation
                                                                    <input type="checkbox" id="test26" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Subject-Class Relation", "Subjectclass", "index");
                                                                                                                        if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu6a[1]"> <label for="test26"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                                                                </td>
                                                            </tr>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>


                                            <div class="col-sm-6">
                                                <div class="title-div">
                                                    <table width="80%">
                                                        <tbody>
                                                            <tr>
                                                                <td width="30%"><strong>&nbsp;</strong></td>


                                                            </tr>
                                                            <tr style="background:#4993d7;">
                                                                <td width="30%"><input type="checkbox" <?php if (in_array("Timetable Management", $module)) { ?> checked="checked" <?php } ?> name="module7[]" class="parenth" data-group=".group7" value="Timetable Management" id="2_arrgruop"><input type="hidden" name="module[]" value="Timetable Managment"><strong style="color:#fff;">Timetable Management</strong></td>

                                                                <td width="10%"></td>

                                                            </tr>



                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu7[0]" class="group7" value="Class Timetable^ClasstimeTabs^view" id="2_arrgruop" <?php if (in_array("Class Timetable", $menu)) { ?> checked="checked" <?php } ?>> Class Timetable <input type="checkbox" id="test27" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Class Timetable", "ClasstimeTabs", "view");
                                                                                                                                                                                                                                                                                                                                                                    if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu7a[0]"> <label for="test27"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu7[1]" class="group7" value="All Staff Timetable^ClasstimeTabs^stafftimetable" id="2_arrgruop" <?php if (in_array("All Staff Timetable", $menu)) { ?> checked="checked" <?php } ?>> All Staff Timetable <input type="checkbox" id="test28" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "All Staff Timetable", "ClasstimeTabs", "stafftimetable");
                                                                                                                                                                                                                                                                                                                                                                                            if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu7a[1]"> <label for="test28"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu7[2]" class="group7" value="Teacher Timetable^ClasstimeTabs^teachertimetable" id="2_arrgruop" <?php if (in_array("Teacher Timetable", $menu)) { ?> checked="checked" <?php } ?>> Teacher Timetable <input type="checkbox" id="test29" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Teacher Timetable", "ClasstimeTabs", "teachertimetable");
                                                                                                                                                                                                                                                                                                                                                                                        if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu7a[2]"> <label for="test29"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu7[3]" class="group7" value="Manage Substitution^Employeeattendance^substitute" id="2_arrgruop" <?php if (in_array("Manage Substitution", $menu)) { ?> checked="checked" <?php } ?>> Manage Substitution <input type="checkbox" id="test30" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Manage Substitution", "Employeeattendance", "substitute");
                                                                                                                                                                                                                                                                                                                                                                                            if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu7a[3]"> <label for="test30"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu7[4]" class="group7" value="Datesheet^datesheet^index" id="2_arrgruop" <?php if (in_array("Datesheet", $menu)) { ?> checked="checked" <?php } ?>> Datesheet <input type="checkbox" id="test030" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Datesheet", "datesheet", "index");
                                                                                                                                                                                                                                                                                                                                                if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu7a[4]"> <label for="test030"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="title-div">
                                                    <table width="80%">
                                                        <tbody>
                                                            <tr>
                                                                <td width="30%">&nbsp;</td>


                                                            </tr>
                                                            <tr style="background:#4993d7;">
                                                                <td width="30%"><input type="checkbox" name="module8[]" <?php if (in_array("Assignment Managment", $module)) { ?> checked="checked" <?php } ?> class="parenth" data-group=".group8" value="Assignment Managment" id="2_arrgruop"><input type="hidden" name="module[]" value="Assignment Managment"><strong style="color:#fff;"> Assignment Managment</strong> </td>

                                                                <td width="10%"></td>
                                                            </tr>

                                                            <tr>
                                                                <td width="10%" class="paperclip">
                                                                    <input type="checkbox" class="group8" value="Post Home Work^assignments^index" name="menu8[0]" id="2_V" <?php if (in_array("Post Home Work", $menu)) { ?> checked="checked" <?php } ?>> Post Home Work <input type="checkbox" id="test31" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Post Home Work", "assignments", "index");
                                                                                                                                                                                                                                                                                                                            if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu8a[0]"> <label for="test31"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                                                                </td>
                                                            </tr>





                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                      
                                            <div class="col-sm-6">
                                                <div class="title-div">
                                                    <table width="80%">
                                                        <tbody>
                                                            <tr>
                                                                <td width="30%"><strong>&nbsp;</strong></td>


                                                            </tr>
                                                            <tr style="background:#4993d7;">
                                                                <td width="30%"><input type="checkbox" name="module9[]" <?php if (in_array("Primary  Exam Management (VATIKA-V)", $module)) { ?> checked="checked" <?php } ?> class="parenth" data-group=".group9" value="Primary  Exam Management (VATIKA-V)" id="2_arrgruop"><input type="hidden" name="module[]" value="Primary  Exam Management (VATIKA-V)"><strong style="color:#fff;">Primary Exam Management (VATIKA-V)</strong></td>

                                                                <td width="10%"></td>

                                                            </tr>



                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu9[0]" class="group9" value="Primary Report Card Genration^Primarycentral^primaryindex" id="2_arrgruop" <?php if (in_array("Primary Report Card Genration", $menu)) { ?> checked="checked" <?php } ?>> Primary Report Card Genration <input type="checkbox" id="test32" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Primary Report Card Genration", "Primarycentral", "primaryindex");
                                                                                                                                                                                                                                                                                                                                                                                                                        if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu9a[0]"> <label for="test32"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu9[1]" class="group9" value="Primary Report Card^Primarycentral^primaryexamcontrolview" id="2_arrgruop" <?php if (in_array("Primary Report Card", $menu)) { ?> checked="checked" <?php } ?>> Primary Report Card <input type="checkbox" id="test33" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Primary Report Card", "Primarycentral", "primaryexamcontrolview");
                                                                                                                                                                                                                                                                                                                                                                                                    if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu9a[1]"> <label for="test33"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>

                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu9[2]" class="group9" value="Primary Report Card Layout^Primarycentral^primarycardmaster" <?php if (in_array("Primary Report Card Layout", $menu)) { ?> checked="checked" <?php } ?> id="2_arrgruop"> Primary Report Card Layout <input type="checkbox" id="test34" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Primary Report Card Layout", "Primarycentral", "primarycardmaster");
                                                                                                                                                                                                                                                                                                                                                                                                                    if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu9a[2]"> <label for="test34"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu9[3]" class="group9" value="Primary Word Bank Entry^Primarycentral^wordbankindex" id="2_arrgruop" <?php if (in_array("Primary Word Bank Entry", $menu)) { ?> checked="checked" <?php } ?>> Primary Word Bank Entry <input type="checkbox" id="test35" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Primary Word Bank Entry", "Primarycentral", "wordbankindex");
                                                                                                                                                                                                                                                                                                                                                                                                        if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu9a[3]"> <label for="test35"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu9[4]" class="group9" value="Primary Teacher Subject Assign^Primarycentral^teacherlinkindex" id="2_arrgruop" <?php if (in_array("Primary Teacher Subject Assign", $menu)) { ?> checked="checked" <?php } ?>> Primary Teacher Subject Assign <input type="checkbox" id="test36" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Primary Teacher Subject Assign", "Primarycentral", "teacherlinkindex");
                                                                                                                                                                                                                                                                                                                                                                                                                                if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu9a[4]"> <label for="test36"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu9[5]" class="group9" value="Primary Pending Report Card^Primarycentral^pendingresultreport" id="2_arrgruop" <?php if (in_array("Primary Pending Report Card", $menu)) { ?> checked="checked" <?php } ?>> Primary Pending Report Card <input type="checkbox" id="test37" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Primary Pending Report Card", "Primarycentral", "pendingresultreport");
                                                                                                                                                                                                                                                                                                                                                                                                                        if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu9a[5]"> <label for="test37"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu9[6]" class="group9" value="Primary Attendence Import Exam^Primarycentral^attendenceimport" id="2_arrgruop" <?php if (in_array("Primary Attendence Import Exam", $menu)) { ?> checked="checked" <?php } ?>> Primary Attendence Import Exam <input type="checkbox" id="test38" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Primary Attendence Import Exam", "Primarycentral", "attendenceimport");
                                                                                                                                                                                                                                                                                                                                                                                                                                if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu9a[6]"> <label for="test38"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="title-div">
                                                    <table width="80%">
                                                        <tbody>
                                                            <tr>
                                                                <td width="30%">&nbsp;</td>


                                                            </tr>
                                                            <tr style="background:#4993d7;">
                                                                <td width="30%"><input type="checkbox" name="module10[]" <?php if (in_array("Higher  Exam Mangement (VI-XII)", $module)) { ?> checked="checked" <?php } ?> class="parenth" data-group=".group10" value="Higher  Exam Mangement (VI-XII)" id="2_arrgruop"><input type="hidden" name="module[]" value="Higher  Exam Mangement (VI-XII)"><strong style="color:#fff;"> Higher Exam Mangement (VI-XII)</strong></td>

                                                                <td width="10%"></td>

                                                            </tr>




                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" class="group10" name="menu10[0]" id="2_V" value="Add/Update Exam^exams^index" <?php if (in_array("Add/Update Exam", $menu)) { ?> checked="checked" <?php } ?>> Add/Update Exam <input type="checkbox" id="test39" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Add/Update Exam", "exams", "index");
                                                                                                                                                                                                                                                                                                                                                        if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu10a[0]"> <label for="test39"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>

                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" class="group10" name="menu10[1]" id="2_V" value="Report Card^studentexamresult^examcontrolview2" <?php if (in_array("Report Card", $menu)) { ?> checked="checked" <?php } ?>> Report Card<input type="checkbox" id="test40" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Report Card", "studentexamresult", "examcontrolview2");
                                                                                                                                                                                                                                                                                                                                                                    if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu10a[1]"> <label for="test40"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>
                                                            
                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" class="group10" name="menu10[3]" id="2_V" value="Upload Results^studentexamresult^examcontrolviewsubject" <?php if (in_array("Upload Results", $menu)) { ?> checked="checked" <?php } ?>> Upload Results <input type="checkbox" id="test42" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Upload Results", "studentexamresult", "examcontrolviewsubject");
                                                                                                                                                                                                                                                                                                                                                                                    if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu10a[3]"> <label for="test42"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label> </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" class="group10" name="menu10[4]" id="2_V" value="Upload CoSchoolastic Result^Coactivityresults^addcsv" <?php if (in_array("Upload CoSchoolastic Result", $menu)) { ?> checked="checked" <?php } ?>> Upload CoSchoolastic Result <input type="checkbox" id="test43" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Upload CoSchoolastic Result", "Coactivityresults", "addcsv");
                                                                                                                                                                                                                                                                                                                                                                                                            if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu10a[4]"> <label for="test43"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>





                                                            </tbody>
                                                             </table>
                                                </div>
                                            </div>


                                            <div class="col-sm-6">
                                                <div class="title-div">
                                                    <table width="80%">
                                                        <tbody>
                                                            <tr>
                                                                <td width="30%"><strong>&nbsp;</strong></td>


                                                            </tr>
                                                            <tr style="background:#4993d7;">
                                                                <td width="30%"><input type="checkbox" name="module11[]" class="parenth" data-group=".group11" value="Report Management" <?php if (in_array("Report Management", $module)) { ?> checked="checked" <?php } ?> id="Report Management"><input type="hidden" name="module[]" value="Fees Report"><strong style="color:#fff;"> Report Management</strong></td>

                                                                <td width="10%"></td>

                                                            </tr>




                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu11[0]" class="group11" value="Daily Student Present Report^students^classidcardreport" id="2_arrgruop" <?php if (in_array("Daily Student Present Report", $menu)) { ?> checked="checked" <?php } ?>> Daily Student Present Report <input type="checkbox" id="test44" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Daily Student Present Report", "students", "classidcardreport");
                                                                                                                                                                                                                                                                                                                                                                                                                        if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu11a[0]"> <label for="test44"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu11[1]" class="group11" value="Student Information^report^student" id="2_arrgruop" <?php if (in_array("Student Information", $menu)) { ?> checked="checked" <?php } ?>> Student Information <input type="checkbox" id="test45" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Student Information", "report", "student");
                                                                                                                                                                                                                                                                                                                                                                                if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu11a[1]"> <label for="test45"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu11[2]" class="group11" value="Employee Information^report^employee" id="2_arrgruop" <?php if (in_array("Employee Information", $menu)) { ?> checked="checked" <?php } ?>> Employee Information <input type="checkbox" id="test46" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Employee Information", "report", "employee");
                                                                                                                                                                                                                                                                                                                                                                                    if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu11a[2]"> <label for="test46"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu11[3]" class="group11" value="Student Admission Summary^report^admitstudent" id="2_arrgruop" <?php if (in_array("Student Admission Summary", $menu)) { ?> checked="checked" <?php } ?>> Student Admission Summary <input type="checkbox" id="test47" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Student Admission Summary", "report", "admitstudent");
                                                                                                                                                                                                                                                                                                                                                                                                        if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu11a[3]"> <label for="test47"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu11[4]" class="group11" value="Student Dropped Summary^report^dropedstudent" id="2_arrgruop" <?php if (in_array("Student Dropped Summary", $menu)) { ?> checked="checked" <?php } ?>> Student Dropped Summary <input type="checkbox" id="test48" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Student Dropped Summary", "report", "dropedstudent");
                                                                                                                                                                                                                                                                                                                                                                                                if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu11a[4]"> <label for="test48"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu11[5]" class="group11" value="Student Document Report^report^document" id="2_arrgruop" <?php if (in_array("Student Document Report", $menu)) { ?> checked="checked" <?php } ?>> Student Document Report <input type="checkbox" id="test49" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Student Document Report", "report", "document");
                                                                                                                                                                                                                                                                                                                                                                                            if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu11a[5]"> <label for="test49"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu11[6]" class="group11" value="Gender Report^report^studentgender" id="2_arrgruop" <?php if (in_array("Gender Report", $menu)) { ?> checked="checked" <?php } ?>> Gender Report <input type="checkbox" id="test50" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Gender Report", "report", "studentgender");
                                                                                                                                                                                                                                                                                                                                                                    if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu11a[6]"> <label for="test50"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu11[7]" class="group11" value="Gender House Report^report^studentgenderhouse" id="2_arrgruop" <?php if (in_array("Gender House Report", $menu)) { ?> checked="checked" <?php } ?>> Gender House Report <input type="checkbox" id="test51" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Gender House Report", "report", "studentgenderhouse");
                                                                                                                                                                                                                                                                                                                                                                                            if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu11a[7]"> <label for="test51"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu11[8]" class="group11" value="Drop Out Report^report^dropoutreport" id="2_arrgruop" <?php if (in_array("Drop Out Report", $menu)) { ?> checked="checked" <?php } ?>> Drop Out Report <input type="checkbox" id="test52" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Drop Out Report", "report", "dropoutreport");
                                                                                                                                                                                                                                                                                                                                                                        if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu11a[8]"> <label for="test52"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu11[9]" class="group11" value="SMS Delivery Report^report^smsreport" id="2_arrgruop" <?php if (in_array("SMS Delivery Report", $menu)) { ?> checked="checked" <?php } ?>> SMS Delivery Report <input type="checkbox" id="test53" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "SMS Delivery Report", "report", "smsreport");
                                                                                                                                                                                                                                                                                                                                                                                if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu11a[9]"> <label for="test53"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu11[10]" class="group11" value="RTE Students Report^report^rtestudent" id="2_arrgruop" <?php if (in_array("RTE Students Report", $menu)) { ?> checked="checked" <?php } ?>> RTE Students Report <input type="checkbox" id="test54" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "RTE Students Report", "report", "rtestudent");
                                                                                                                                                                                                                                                                                                                                                                                    if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu11a[10]"> <label for="test54"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu11[11]" class="group11" value="Optional Subjects Report^report^optionalsubjectlist" id="2_arrgruop" <?php if (in_array("Optional Subjects Report", $menu)) { ?> checked="checked" <?php } ?>> Optional Subjects Report <input type="checkbox" id="test55" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Optional Subjects Report", "report", "optionalsubjectlist");
                                                                                                                                                                                                                                                                                                                                                                                                            if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu11a[11]"> <label for="test55"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu11[12]" class="group11" value="Student  ID CARD Report^primarycentral^studentimagereport" id="2_arrgruop" <?php if (in_array("Student  ID CARD Report", $menu)) { ?> checked="checked" <?php } ?>> Student ID CARD Report <input type="checkbox" id="test56" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Student  ID CARD Report", "primarycentral", "studentimagereport");
                                                                                                                                                                                                                                                                                                                                                                                                                if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu11a[12]"> <label for="test56"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>

                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu11[13]" class="group11" value="K.Students Report^report^disabilitys" id="2_arrgruop" <?php if (in_array("K.Students Report", $menu)) { ?> checked="checked" <?php } ?>> K.Students Report <input type="checkbox" id="test57" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "K.Students Report", "report", "disabilitys");
                                                                                                                                                                                                                                                                                                                                                                                if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu11a[13]"> <label for="test57"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu11[14]" class="group11" value="Caste/ Religion Report (Enrollment)^report^cast" id="2_arrgruop" <?php if (in_array("Caste/ Religion Report (Enrollment)", $menu)) { ?> checked="checked" <?php } ?>> Caste/ Religion Report (Enrollment) <input type="checkbox" id="test58" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Caste/ Religion Report (Enrollment)", "report", "cast");
                                                                                                                                                                                                                                                                                                                                                                                                                            if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu11a[14]"> <label for="test58"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu11[15]" class="group11" value="Caste/ Religion Report (Repeaters)^report^castrepeaters" id="2_arrgruop" <?php if (in_array("Caste/ Religion Report (Repeaters)", $menu)) { ?> checked="checked" <?php } ?>> Caste/ Religion Report (Repeaters) <input type="checkbox" id="test59" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Caste/ Religion Report (Repeaters)", "report", "castrepeaters");
                                                                                                                                                                                                                                                                                                                                                                                                                                    if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu11a[15]"> <label for="test59"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu11[16]" class="group11" value="Age Report^report^age" id="2_arrgruop" <?php if (in_array("Age Report", $menu)) { ?> checked="checked" <?php } ?>> Age Report <input type="checkbox" id="test60" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Age Report", "report", "age");
                                                                                                                                                                                                                                                                                                                                                if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu11a[16]"> <label for="test60"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu11[17]" class="group11" value="Books Report^ReportNew^booksreport" id="2_arrgruop" <?php if (in_array("Books Report", $menu)) { ?> checked="checked" <?php } ?>> Books Report <input type="checkbox" id="test61" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Books Report", "ReportNew", "booksreport");
                                                                                                                                                                                                                                                                                                                                                                    if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu11a[17]"> <label for="test61"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu11[18]" class="group11" value="Issue Books Report^report^issuedbooksreport" id="2_arrgruop" <?php if (in_array("Issue Books Report", $menu)) { ?> checked="checked" <?php } ?>> Issue Books Report <input type="checkbox" id="test62" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Issue Books Report", "report", "issuedbooksreport");
                                                                                                                                                                                                                                                                                                                                                                                        if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu11a[18]"> <label for="test62"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu11[19]" class="group11" value="Deposit Books Report^ReturnRenewBooks^depositreportt" id="2_arrgruop" <?php if (in_array("Deposit Books Report", $menu)) { ?> checked="checked" <?php } ?>> Deposit Books Report <input type="checkbox" id="test63" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Deposit Books Report", "ReturnRenewBooks", "depositreportt");
                                                                                                                                                                                                                                                                                                                                                                                    if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> class="checkk" value="1" name="menu11a[19]"> <label for="test63"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>

                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu11[20]" class="group11" value="Fine Report^report^finereport" id="2_arrgruop" <?php if (in_array("Fine Report", $menu)) { ?> checked="checked" <?php } ?>> Fine Report <input type="checkbox" id="test64" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Fine Report", "report", "finereport");
                                                                                                                                                                                                                                                                                                                                            if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> class="checkk" value="1" name="menu11a[20]"> <label for="test64"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>


                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu11[21]" class="group11" value="Enquiry Report^report^index" id="2_arrgruop" <?php if (in_array("Enquiry Report", $menu)) { ?> checked="checked" <?php } ?>> Enquiry Report <input type="checkbox" id="test65" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Enquiry Report", "report", "index");
                                                                                                                                                                                                                                                                                                                                                                if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu11a[21]"> <label for="test65"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>

                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu11[22]" class="group11" value="Follow-Up Report^report^followup" <?php if (in_array("Follow-Up Report", $menu)) { ?> checked="checked" <?php } ?> id="2_arrgruop"> Follow-Up Report <input type="checkbox" id="test66" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Follow-Up Report", "report", "followup");
                                                                                                                                                                                                                                                                                                                                                                        if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu11a[22]"> <label for="test66"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu11[23]" class="group11" value="Substitution Histort Report^Employeeattendance^substitution_report" <?php if (in_array("Substitution Histort Report", $menu)) { ?> checked="checked" <?php } ?> id="2_arrgruop">Substitution History Report <input type="checkbox" id="test67" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Substitution Histort Report", "Employeeattendance", "substitution_report");
                                                                                                                                                                                                                                                                                                                                                                                                                                if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu11a[23]"> <label for="test67"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu11[24]" class="group11" value="Employees Monthly Attendance Report^report^employee_monthly_attn_report" <?php if (in_array("Employees Monthly Attendance Report", $menu)) { ?> checked="checked" <?php } ?> id="2_arrgruop"> Employees Monthly
                                                                    Attendance
                                                                    Report <input type="checkbox" id="test68" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Employees Monthly Attendance Report", "report", "employee_monthly_attn_report");
                                                                                                                if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> class="checkk" value="1" name="menu11a[24]"> <label for="test68"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu11[25]" class="group11" value="Daily Staff Attendance Report^Employeeattendance^report" <?php if (in_array("Daily Staff Attendance Report", $menu)) { ?> checked="checked" <?php } ?> id="2_arrgruop"> Daily Staff Attendance Report <input type="checkbox" id="test69" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Daily Staff Attendance Report", "Employeeattendance", "report");
                                                                                                                                                                                                                                                                                                                                                                                                                        if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu11a[25]"> <label for="test69"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>

                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu11[26]" class="group11" value="Employee  ID CARD Report^primarycentral^studentimagereport" id="2_arrgruop" <?php if (in_array("Employee  ID CARD Report", $menu)) { ?> checked="checked" <?php } ?>> Employee ID CARD Report <input type="checkbox" id="test561" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Employee  ID CARD Report", "primarycentral", "studentimagereport");
                                                                                                                                                                                                                                                                                                                                                                                                                    if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu11a[26]"> <label for="test561"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="title-div">
                                                    <table width="80%">
                                                        <tbody>
                                                            <tr>
                                                                <td width="30%">&nbsp;</td>


                                                            </tr>
                                                            <tr style="background:#4993d7;">
                                                                <td width="30%"><input type="checkbox" <?php if (in_array("Fees Report", $module)) { ?> checked="checked" <?php } ?> name="module12[]" class="parenth" data-group=".group12" value="Fees Report" id="2_arrgruop"><input type="hidden" name="module[]" value="Fees Report"><strong style="color:#fff;"> Fees Report</strong></td>

                                                                <td width="10%"></td>

                                                            </tr>




                                                            <tr>
                                                                <td width="10%" class="paperclip"><input type="checkbox" class="group12" name="menu12[0]" id="2_V" value="Fee Collection Report^report^collectionrecipiet" <?php if (in_array("Fee Collection Report", $menu)) { ?> checked="checked" <?php } ?>> Fee Collection Report <input type="checkbox" id="test70" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Fee Collection Report", "report", "collectionrecipiet");
                                                                                                                                                                                                                                                                                                                                                                                        if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu12a[0]"> <label for="test70"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="10%" class="paperclip"><input type="checkbox" class="group12" name="menu12[1]" id="2_V" value="Defaulter Report^report^students_all" <?php if (in_array("Defaulter Report", $menu)) { ?> checked="checked" <?php } ?>> Defaulter Report <input type="checkbox" id="test71" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Defaulter Report", "report", "students_all");
                                                                                                                                                                                                                                                                                                                                                                    if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu12a[1]"> <label for="test71"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="10%" class="paperclip"><input type="checkbox" class="group12" name="menu12[2]" id="2_V" value="Daily Summary Report^report^dailyreport" <?php if (in_array("Daily Summary Report", $menu)) { ?> checked="checked" <?php } ?>> Daily Summary Report <input type="checkbox" id="test72" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Daily Summary Report", "report", "dailyreport");
                                                                                                                                                                                                                                                                                                                                                                                if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu12a[2]"> <label for="test72"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="10%" class="paperclip"><input type="checkbox" class="group12" name="menu12[3]" id="2_V" value="Monthly Summary Report^report^monthlysummary" <?php if (in_array("Monthly Summary Report", $menu)) { ?> checked="checked" <?php } ?>> Monthly Summary Report <input type="checkbox" id="test73" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Monthly Summary Report", "report", "monthlysummary");
                                                                                                                                                                                                                                                                                                                                                                                        if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu12a[3]"> <label for="test73"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="10%" class="paperclip"><input type="checkbox" class="group12" name="menu12[4]" id="2_V" value="Bank Report^report^bankreport" <?php if (in_array("Bank Report", $menu)) { ?> checked="checked" <?php } ?>> Bank Report <input type="checkbox" id="test74" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Bank Report", "report", "bankreport");
                                                                                                                                                                                                                                                                                                                                    if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> class="checkk" value="1" name="menu12a[4]"> <label for="test74"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>

                                                            <tr>
                                                                <td width="10%" class="paperclip"><input type="checkbox" class="group12" name="menu12[5]" id="2_V" value="Prospectus Selling^enquires^prospectus_index" <?php if (in_array("Prospectus Selling", $menu)) { ?> checked="checked" <?php } ?>> Prospectus Selling <input type="checkbox" id="test75" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Prospectus Selling", "enquires", "prospectus_index");
                                                                                                                                                                                                                                                                                                                                                                                if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu12a[5]"> <label for="test75"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>




                                                            <tr>
                                                                <td width="10%" class="paperclip"><input type="checkbox" class="group12" name="menu12[6]" id="2_V" value="Registration Report^report^registredstudents" <?php if (in_array("Registration Report", $menu)) { ?> checked="checked" <?php } ?>> Registration Report <input type="checkbox" id="test76" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Registration Report", "report", "registredstudents");
                                                                                                                                                                                                                                                                                                                                                                                    if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu12a[6]"> <label for="test76"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="10%" class="paperclip"><input type="checkbox" class="group12" name="menu12[7]" id="2_V" value="Office Today Summary^report^admitstudentcollection" <?php if (in_array("Office Today Summary", $menu)) { ?> checked="checked" <?php } ?>> Office Today Summary <input type="checkbox" id="test075" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Office Today Summary", "report", "admitstudentcollection");
                                                                                                                                                                                                                                                                                                                                                                                            if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu12a[7]"> <label for="test075"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>

                                                            <tr>
                                                                <td width="10%" class="paperclip"><input type="checkbox" class="group12" name="menu12[8]" id="2_V" value="Office Summary Detail Report^report^admitstudentcollectiondetail" <?php if (in_array("Office Summary Detail Report", $menu)) { ?> checked="checked" <?php } ?>> Office Summary Detail Report <input type="checkbox" id="test0075" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Office Summary Detail Report", "report", "admitstudentcollectiondetail");
                                                                                                                                                                                                                                                                                                                                                                                                                            if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu12a[8]"> <label for="test0075"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>


                                                            <tr>
                                                                <td width="10%" class="paperclip"><input type="checkbox" class="group12" name="menu12[9]" id="2_V" value="Search Fee Report^report^cancelledrecipiet" <?php if (in_array("Search Fee Report", $menu)) { ?> checked="checked" <?php } ?>> Search Fee Receipt <input type="checkbox" id="test77" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Search Fee Report", "report", "cancelledrecipiet");
                                                                                                                                                                                                                                                                                                                                                                            if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu12a[9]"> <label for="test77"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>



                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>



                                            <div class="col-sm-6">
                                                <div class="title-div">
                                                    <table width="80%">
                                                        <tbody>
                                                            <tr>
                                                                <td width="30%"><strong>&nbsp;</strong></td>


                                                            </tr>
                                                            <tr style="background:#4993d7;">
                                                                <td width="30%"><input type="checkbox" <?php if (in_array("Library Management", $module)) { ?> checked="checked" <?php } ?> name="module13[]" class="parenth" data-group=".group13" value="Library Management" id="2_arrgruop">
                                                                    <input type="hidden" name="module[]" value="Library Management"><strong style="color:#fff;"> Library Management</strong>
                                                                </td>

                                                                <td width="10%"></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu13[0]" class="group13" value="Issue Book Manager^issuebooks^index" id="2_arrgruop" <?php if (in_array("Issue Book Manager", $menu)) { ?> checked="checked" <?php } ?>> Issue Book Manager <input type="checkbox" id="test78" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Issue Book Manager", "issuebooks", "index");
                                                                                                                                                                                                                                                                                                                                                                                if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu13a[0]"> <label for="test78"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu13[1]" class="group13" value="Periodical Manager^Books^periodicView" id="2_arrgruop" <?php if (in_array("Periodical Manager", $menu)) { ?> checked="checked" <?php } ?>> Periodical Manager<input type="checkbox" id="test79" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Periodical Manager", "Books", "periodicView");
                                                                                                                                                                                                                                                                                                                                                                if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> class="checkk" value="1" name="menu13a[1]"> <label for="test79"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu13[2]" class="group13" value="Deposit Book Manager^ReturnRenewBooks^index" id="2_arrgruop" <?php if (in_array("Deposit Book Manager", $menu)) { ?> checked="checked" <?php } ?>> Deposit Book Manager<input type="checkbox" id="test80" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Deposit Book Manager", "ReturnRenewBooks", "index");
                                                                                                                                                                                                                                                                                                                                                                            if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> class="checkk" value="1" name="menu13a[2]"> <label for="test80"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu13[3]" class="group13" value="Add Book Manager^Books^create" id="2_arrgruop" <?php if (in_array("Add Book Manager", $menu)) { ?> checked="checked" <?php } ?>> Add Book Manager<input type="checkbox" id="test82" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Add Book Manager", "Books", "create");
                                                                                                                                                                                                                                                                                                                                                    if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> class="checkk" value="1" name="menu13a[3]"> <label for="test82"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu13[4]" class="group13" value="Book Language^Language^index" id="2_arrgruop" <?php if (in_array("Book Language", $menu)) { ?> checked="checked" <?php } ?>> Book Language <input type="checkbox" id="test83" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Book Language", "Language", "index");
                                                                                                                                                                                                                                                                                                                                                if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> class="checkk" value="1" name="menu13a[4]"> <label for="test83"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu13[5]" class="group13" value="Book Category^BookCategories^index" id="2_arrgruop" <?php if (in_array("Book Category", $menu)) { ?> checked="checked" <?php } ?>> Book Category<input type="checkbox" id="test84" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Book Category", "BookCategories", "index");
                                                                                                                                                                                                                                                                                                                                                                    if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu13a[5]"> <label for="test84"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu13[6]" class="group13" value="Vendor Manager^BookVendors^index" id="2_arrgruop" <?php if (in_array("Vendor Manager", $menu)) { ?> checked="checked" <?php } ?>> Vendor Manager<input type="checkbox" id="test85" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Vendor Manager", "BookVendors", "index");
                                                                                                                                                                                                                                                                                                                                                                    if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu13a[6]"> <label for="test85"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu13[7]" class="group13" value="Cup Board Manager^CupBoards^index" id="2_arrgruop" <?php if (in_array("Cup Board Manager", $menu)) { ?> checked="checked" <?php } ?>> Cup Board Manager <input type="checkbox" id="test86" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Cup Board Manager", "CupBoards", "index");
                                                                                                                                                                                                                                                                                                                                                                            if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu13a[7]"> <label for="test86"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu13[8]" class="group13" value="Shelf Manager^CupBoardShelves^index" id="2_arrgruop" <?php if (in_array("Shelf Manager", $menu)) { ?> checked="checked" <?php } ?>> Shelf Manager<input type="checkbox" id="test87" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Shelf Manager", "CupBoardShelves", "index");
                                                                                                                                                                                                                                                                                                                                                                    if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu13a[8]"> <label for="test87"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>



                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="title-div">
                                                    <table width="80%">
                                                        <tbody>
                                                            <tr>
                                                                <td width="30%"><strong>&nbsp;</strong></td>


                                                            </tr>
                                                            <tr style="background:#4993d7;">
                                                                <td width="30%"><input type="checkbox" <?php if (in_array("Lead Management", $module)) { ?> checked="checked" <?php } ?> name="module14[]" class="parenth" data-group=".group14" value="Lead Management" id="2_arrgruop">
                                                                    <input type="hidden" name="module[]" value="Lead Management"><strong style="color:#fff;"> Lead Management</strong>
                                                                </td>

                                                                <td width="10%"></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu14[0]" class="group14" value="Enquiry Manager^enquires^index" <?php if (in_array("Enquiry Manager", $menu)) { ?> checked="checked" <?php } ?> id="2_arrgruop"> Enquiry Manager <input type="checkbox" id="test88" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Enquiry Manager", "enquires", "index");
                                                                                                                                                                                                                                                                                                                                                                    if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu14a[0]"> <label for="test88"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu14[1]" class="group14" <?php if (in_array("Follow Up Manager", $menu)) { ?> checked="checked" <?php } ?> value="Follow Up Manager^enquires^followenq" id="2_arrgruop"> Follow Up Manager <input type="checkbox" id="test89" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Follow Up Manager", "enquires", "followenq");
                                                                                                                                                                                                                                                                                                                                                                            if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu14a[1]"> <label for="test89"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu14[2]" class="group14" <?php if (in_array("Visitors", $menu)) { ?> checked="checked" <?php } ?> value="Visitors^Visitors^index" id="2_arrgruop"> Visitors Manager <input type="checkbox" id="test90" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Visitors", "Visitors", "index");
                                                                                                                                                                                                                                                                                                                                                        if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu14a[2]"> <label for="test90"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu14[3]" class="group14" <?php if (in_array("Gatepass", $menu)) { ?> checked="checked" <?php } ?> value="Gatepass^Gatepass^index" id="2_arrgruop"> Gatepass Manager <input type="checkbox" id="test91" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Gatepass", "Gatepass", "index");
                                                                                                                                                                                                                                                                                                                                                        if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu14a[3]"> <label for="test91"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu14[4]" class="group14" <?php if (in_array("Feedbacks", $menu)) { ?> checked="checked" <?php } ?> value="Feedbacks^Feedbacks^index" id="2_arrgruop"> Feedbacks Manager <input type="checkbox" id="test92" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Feedbacks", "Feedbacks", "index");
                                                                                                                                                                                                                                                                                                                                                            if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu14a[4]"> <label for="test92"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>





                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <div class="col-sm-6">
                                                <div class="title-div">

                                                    <table width="80%">
                                                        <tbody>
                                                            <tr>
                                                                <td width="30%"><strong>&nbsp;</strong></td>


                                                            </tr>
                                                            <tr style="background:#4993d7;">
                                                                <td width="30%" class="paperclip"><input type="checkbox" <?php if (in_array("Store Management", $module)) { ?> checked="checked" <?php } ?> name="module15[]" class="parenth" data-group=".group15" value="Store Management" id="2_arrgruop">
                                                                    <input type="hidden" name="module[]" value="Store Management"><strong style="color:#fff;"> Store Management </strong>
                                                                </td>

                                                                <td width="10%"></td>
                                                            </tr>

                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu15[0]" class="group15" value="Vendor Manager^vendors^index" <?php if (in_array("Vendor Manager", $menu)) { ?> checked="checked" <?php } ?> id="2_arrgruop"> Vendor Manager <input type="checkbox" id="test090" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Vendor Manager", "vendors", "index");
                                                                                                                                                                                                                                                                                                                                                                if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu15a[0]"> <label for="test090"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu15[1]" class="group15" value="Tax Manager^taxmaster^index" <?php if (in_array("Tax Manager", $menu)) { ?> checked="checked" <?php } ?> id="2_arrgruop"> Tax Manager <input type="checkbox" id="test90" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Tax Manager", "taxmaster", "index");
                                                                                                                                                                                                                                                                                                                                                        if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu15a[1]"> <label for="test90"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>

                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu15[2]" class="group15" value="Item Category Manager^itemcategory^index" <?php if (in_array("Item Category Manager", $menu)) { ?> checked="checked" <?php } ?> id="2_arrgruop"> Item Category Manager <input type="checkbox" id="test91" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Item Category Manager", "itemcategory", "index");
                                                                                                                                                                                                                                                                                                                                                                                        if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu15a[2]"> <label for="test91"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu15[3]" class="group15" value="Measurement Units Manager^measurementunit^index" <?php if (in_array("Measurement Units Manager", $menu)) { ?> checked="checked" <?php } ?> id="2_arrgruop"> Measurement Units Manager <input type="checkbox" id="test92" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Measurement Units Manager", "measurementunit", "index");
                                                                                                                                                                                                                                                                                                                                                                                                        if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu15a[3]"> <label for="test92"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu15[4]" class="group15" value="Size Manager^sizemanager^index" <?php if (in_array("Size Manager", $menu)) { ?> checked="checked" <?php } ?> id="2_arrgruop"> Size Manager <input type="checkbox" id="test93" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Size Manager", "sizemanager", "index");
                                                                                                                                                                                                                                                                                                                                                if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> class="checkk" value="1" name="menu15a[4]"> <label for="test93"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>


                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu15[5]" class="group15" value="Payment Terms Manager^paymentmanager^index" <?php if (in_array("Payment Terms Manager", $menu)) { ?> checked="checked" <?php } ?> id="2_arrgruop"> Payment Terms Manager <input type="checkbox" id="test94" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Payment Terms Manager", "paymentmanager", "index");
                                                                                                                                                                                                                                                                                                                                                                                            if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu15a[5]"> <label for="test94"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>

                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu15[6]" class="group15" value="Add Item Manager^additem^index" <?php if (in_array("Add Item Manager", $menu)) { ?> checked="checked" <?php } ?> id="2_arrgruop"> Add Item Manager <input type="checkbox" id="test96" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Add Item Manager", "additem", "index");
                                                                                                                                                                                                                                                                                                                                                                    if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu15a[6]"> <label for="test96"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>

                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu15[7]" class="group15" value="Add Indent^indent^add" <?php if (in_array("Add Indent", $menu)) { ?> checked="checked" <?php } ?> id="2_arrgruop"> Add Indent <input type="checkbox" id="test97" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Add Indent", "indent", "add");
                                                                                                                                                                                                                                                                                                                                                if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu15a[7]"> <label for="test97"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>


                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu15[8]" class="group15" value="Goods Issue^indent^index" <?php if (in_array("Goods Issue", $menu)) { ?> checked="checked" <?php } ?> id="2_arrgruop"> Goods Issue <input type="checkbox" id="test98" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Goods Issue", "indent", "index");
                                                                                                                                                                                                                                                                                                                                                    if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu15a[8]"> <label for="test98"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>



                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu15[9]" class="group15" value="PO^purchaseorder^index" <?php if (in_array("PO", $menu)) { ?> checked="checked" <?php } ?> id="2_arrgruop"> PO <input type="checkbox" id="test99" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "PO", "purchaseorder", "index");
                                                                                                                                                                                                                                                                                                                                if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu15a[9]"> <label for="test99"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>

                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu15[10]" class="group15" value="Received^goodsreceived^index" <?php if (in_array("Received", $menu)) { ?> checked="checked" <?php } ?> id="2_arrgruop">Received <input type="checkbox" id="test100" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Received", "goodsreceived", "index");
                                                                                                                                                                                                                                                                                                                                    if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> class="checkk" value="1" name="menu15a[10]"> <label for="test100"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>


                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu15[11]" class="group15" value="Stock Register^stockregister^index" <?php if (in_array("Stock Register", $menu)) { ?> checked="checked" <?php } ?> id="2_arrgruop"> Stock Register <input type="checkbox" id="test101" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Stock Register", "stockregister", "index");
                                                                                                                                                                                                                                                                                                                                                        if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> class="checkk" value="1" name="menu15a[11]"> <label for="test101"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label></td>
                                                            </tr>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <div class="col-sm-6">
                                                <div class="title-div">

                                                    <table width="80%">
                                                        <tbody>
                                                            <tr>
                                                                <td width="30%"><strong>&nbsp;</strong></td>


                                                            </tr>
                                                            <tr style="background:#4993d7;">
                                                                <td width="30%" class="paperclip"><input type="checkbox" <?php if (in_array("Payroll Management", $module)) { ?> checked="checked" <?php } ?> name="module16[]" class="parenth" data-group=".group16" value="Payroll Management" id="2_arrgruop">
                                                                    <input type="hidden" name="module[]" value="Payroll Management"><strong style="color:#fff;"> Payroll Management </strong>
                                                                </td>

                                                                <td width="10%"></td>
                                                            </tr>

                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu16[0]" class="group16" value="Master Manager^leavetype^index" <?php if (in_array("Master Manager", $menu)) { ?> checked="checked" <?php } ?> id="2_arrgruop"> Master Manager
                                                                    <input type="checkbox" id="test102" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Master Manager", "leavetype", "index");
                                                                                                                        if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu16a[0]"> <label for="test102"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                                                                </td>
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu16[1]" class="group16" value="Event Manager^Holiday^index" <?php if (in_array("Event Manager", $menu)) { ?> checked="checked" <?php } ?> id="2_arrgruop"> Event Manager
                                                                    <input type="checkbox" id="test103" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Event Manager", "Holiday", "index");
                                                                                                                        if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu16a[1]"> <label for="test103"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                                                                </td>
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu16[2]" class="group16" value="Salary Summary^payroll^salary_summary" <?php if (in_array("Salary Summary", $menu)) { ?> checked="checked" <?php } ?> id="2_arrgruop"> Salary Summary
                                                                    <input type="checkbox" id="test104" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Salary Summary", "payroll", "salary_summary");
                                                                                                                        if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu16a[2]"> <label for="test104"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                                                                </td>
                                                                </td>
                                                            </tr>


                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu16[3]" class="group16" value="Salary Report Manager^payroll^report" <?php if (in_array("Salary Summary", $menu)) { ?> checked="checked" <?php } ?> id="2_arrgruop"> Salary Report Manager
                                                                    <input type="checkbox" id="test105" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Salary Report Manager", "payroll", "report");
                                                                                                                        if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu16a[3]"> <label for="test105"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                                                                </td>
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu16[4]" class="group16" value="Employee Manager^payroll^emplist" <?php if (in_array("Employee Manager", $menu)) { ?> checked="checked" <?php } ?> id="2_arrgruop"> Employee Manager
                                                                    <input type="checkbox" id="test106" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Employee Manager", "payroll", "emplist");
                                                                                                                        if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu16a[4]"> <label for="test106"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                                                                </td>
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu16[5]" class="group16" value="Advance Salary^payroll^advance_salary" <?php if (in_array("Advance Salary", $menu)) { ?> checked="checked" <?php } ?> id="2_arrgruop"> Advance Salary
                                                                    <input type="checkbox" id="test107" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Advance Salary", "payroll", "advance_salary");
                                                                                                                        if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu16a[5]"> <label for="test107"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                                                                </td>
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu16[6]" class="group16" value="Salary Generate^payroll^salary_generate" <?php if (in_array("Salary Generate", $menu)) { ?> checked="checked" <?php } ?> id="2_arrgruop"> Salary Generate
                                                                    <input type="checkbox" id="test108" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Salary Generate", "payroll", "salary_generate");
                                                                                                                        if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu16a[6]"> <label for="test108"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                                                                </td>
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu16[7]" class="group16" value="Daily Leave Report^payroll^leave_report" <?php if (in_array("Daily Leave Report", $menu)) { ?> checked="checked" <?php } ?> id="2_arrgruop"> Daily Leave Report
                                                                    <input type="checkbox" id="test109" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Daily Leave Report", "payroll", "leave_report");
                                                                                                                        if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu16a[7]"> <label for="test109"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                                                                </td>
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu16[8]" class="group16" value="Employee Absent Report^payroll^emp_absent_report" <?php if (in_array("Employee Absent Report", $menu)) { ?> checked="checked" <?php } ?> id="2_arrgruop"> Employee Absent Report
                                                                    <input type="checkbox" id="test110" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Employee Absent Report", "payroll", "emp_absent_report");
                                                                                                                        if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu16a[8]"> <label for="test110"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                                                                </td>
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu16[9]" class="group16" value="Leave Application^payroll^leaves" <?php if (in_array("Leave Application", $menu)) { ?> checked="checked" <?php } ?> id="2_arrgruop"> Leave Application
                                                                    <input type="checkbox" id="test111" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Leave Application", "payroll", "leaves");
                                                                                                                        if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu16a[9]"> <label for="test111"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                                                                </td>
                                                                </td>
                                                            </tr>

                                                            <td width="30%" class="paperclip"><input type="checkbox" name="menu16[10]" class="group16" value="Staff Attendance Report^payroll^employee_monthly_attn_report" <?php if (in_array("Staff Attendance Report", $menu)) { ?> checked="checked" <?php } ?> id="2_arrgruop"> Staff Attendance Report
                                                                <input type="checkbox" id="test112" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Staff Attendance Report", "payroll", "employee_monthly_attn_report");
                                                                                                                    if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu16a[10]"> <label for="test112"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                                                            </td>
                                                            </td>
                                                            </tr>


                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                    </div>
                                </div>

                            </div>

                        </div>
                        <!-- /.box -->
                        </form>
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->

                <!-- /.content -->
            </div>
    </section>