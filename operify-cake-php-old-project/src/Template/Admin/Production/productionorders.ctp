<style>
    #contractUL {
        position: relative;
    }

    #contractUL ul {
        position: absolute;
        z-index: 999;
        overflow: scroll;
        height: 100px;
        top: 100%;
        left: 0px;
        right: 0px;
        list-style-type: none;
        background-color: white;
        padding-left: 0px;
    }

    #contractUL ul li {
        padding: 5px 8px;
        border: 1px solid lightgray;
        margin-left: 0px !important;
    }

    #contractUL ul li a {
        color: black;
    }

    .dot {
        display: flex;
        width: 15px;
        height: 15px;
        border-radius: 50%;
        margin-right: 5px;
    }

    #load2 {
        width: 100%;
        height: 100%;
        position: fixed;
        z-index: 9999;
        background-color: white !important;
        background: url("<?php echo SITE_URL; ?>images/Preloader_2.gif") no-repeat center center rgba(0, 0, 0, 0.75)
    }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Production Orders
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-home"></i>Home</a></li>
            <li><a href="<?php echo SITE_URL; ?>admin/Production/billsofmaterials">
                    Production Orders
                </a></li>
        </ol>
    </section>
    <!-- content header -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">

                        <?php echo $this->Flash->render(); ?>
                        <?php echo $this->Form->create('', array('type' => 'file', 'inputDefaults' => array('div' => false, 'label' => false), 'id' => 'mysubscription', 'class' => 'form-horizontal', 'style' => 'margin-bottom:0px;')); ?>
                        <div class="form-group" style="margin-bottom:0px;">
                            <div class="row">
                                <div class="col">
                                    <label for="inputEmail3" class=" control-label"
                                        style="text-align: left !important">Contract Name</label>

                                    <input type="hidden" name="contract_id" id="contrselectid">

                                    <?php echo $this->Form->input('contractname', array('class' => 'form-control secrhcontract', 'id' => 'contractnameid', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'placeholder' => 'Enter Contract Name')); ?>
                                    <div id="contractUL" style="display:none;">
                                        <ul></ul>
                                    </div>
                                    <div id="contractUL1" style="display:none;">
                                        <ul>
                                            <li
                                                style="padding: 5px 8px;list-style:none;color: black;font-weight: bold;margin-left:-32px; border: 1px solid lightgray;">
                                                No Record Found</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col">
                                    <label for="inputEmail3" class="control-label">Select Status</label>
                                    <?php
                                    $options = array(
                                        "O" => "Pending",
                                        "C" => "Completed"
                                    );
                                    echo $this->Form->input('status', array('class' => 'form-control ', 'type' => 'select', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'empty' => '-- Select Status--', 'options' => $options)); ?>
                                </div>

                                <div class="col">
                                    <script>
                                        $(document).ready(function() {
                                            $('#fdatefrom').datepicker({
                                                dateFormat: 'dd-mm-yy',
                                                yearRange: '2018:2030',
                                                changeMonth: true,
                                                changeYear: true,
                                            });
                                            $('#fendfrom').datepicker({
                                                dateFormat: 'dd-mm-yy',
                                                yearRange: '2018:2030',
                                                changeMonth: true,
                                                changeYear: true,
                                            });
                                        });
                                    </script>
                                    <label for="inputEmail3" class="control-label">From</label>
                                    <?php echo $this->Form->input('datefrom', array('class' => 'form-control', 'id' => 'fdatefrom', 'readonly', 'placeholder' => 'Completed Date From', 'label' => false)); ?>
                                </div>
                                <div class="col">
                                    <label for="inputEmail3" class="control-label">To</label>
                                    <?php echo $this->Form->input('dateto', array('class' => 'form-control', 'id' => 'fendfrom', 'readonly', 'placeholder' => 'Completed Date To', 'label' => false)); ?>
                                </div>
                                <div class="col">
                                    <input type="submit" style="background-color:#00c0ef; color:#fff;margin-top:23px;"
                                        id="" class="btn btn4 btn_pdf myscl-btn date" value="Search">

                                    <a href="<?php echo SITE_URL; ?>admin/production/productionorders"
                                        class="excelbtn btn"
                                        style="background-color:#00c0ef; !important; margin-top: 23px; color:#fff; padding:6px 18px;">Reset</a>
                                </div>

                                <div class="col">
                                    <?php
                                    $role_permissions = $this->Permission->permissioncheck();
                                    $fileurl = "admin/production/addproductionorders";
                                    if (in_array($fileurl, $role_permissions)) { ?>
                                        <a href="<?php echo SITE_URL; ?>admin/production/addproductionorders"
                                            class="btn btn-success pull-right m-top10"
                                            style="margin-top: 23px;margin-bottom:10px;"><i class="fa fa-plus"
                                                aria-hidden="true"></i>Add</a>
                                    <?php } ?>
                                    <a href="<?php echo SITE_URL; ?>admin/production/productionorderexcel"
                                        class="excelbtn btn pull-right" style="padding:0;margin-top: 23px;"><i
                                            class="fa fa-file-excel-o"
                                            style="font-size:28px; margin-right:10px;"></i></a>
                                </div>

                            </div>
                        </div>
                        <?php echo $this->Form->end(); ?>

                    </div>


                    <!-- /.box-header -->
                    <div class="box-body" id="updt" style="padding-top:0px;">
                        <div id="load2" style="display:none;"></div>
                        <table id="" class="table table-bordered table-striped" width="100%">
                            <thead>
                                <tr>
                                    <th width="05%">PO NO.</th>
                                    <th width="06%">Date Created</th>
                                    <th width="17%">Contract Name</th>
                                    <th width="25%">Product</th>
                                    <th width="06%">Start Date</th>
                                    <th width="06%">End Date</th>
                                    <th width="06%">Planned Qty</th>
                                    <th width="06%">Prepared Qty</th>
                                    <th width="08%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($productionorder as $detail) {
                                    $contractname = $this->comman->findcontractname($detail['contract_id']);
                                    $itemname = $this->comman->getitemname($detail['item_id']);
                                    $checkdailysheet = $this->comman->checkdailysheet($detail['po_id'], 8);
                                    $checkdproductionstart = $this->comman->checkdproductionstart($detail['po_id']);
                                    $quantity = '';
                                    foreach ($checkdailysheet as $value) {
                                        $quantity += $value['production_shift_a'] + $value['production_shift_b'];
                                        $completedate = date('d-m-Y', strtotime($value['production_date']));
                                    }
                                    $status = '';
                                    if ($quantity >= $detail['plannedqty']) {
                                        $status = 'C';
                                    }

                                ?>

                                    <tr>
                                        <td>
                                            <a class="viewproductiondetails"
                                                href="<?php echo SITE_URL; ?>admin/production/viewproductiondetails/<?php echo $detail['po_id']; ?>"><?php echo $detail['po_id']; ?></a>
                                        </td>

                                        <td>
                                            <?php echo date('d-m-Y', strtotime($detail['issuedate'])); ?>
                                        </td>
                                        <td><a href="<?php echo SITE_URL; ?>admin/production/viewcontractdetail/<?php echo $detail['contract_id']; ?>"
                                                class="viewdetails">
                                                <?php echo $contractname['title'] . '(' . $contractname['workorder'] . ')'; ?>
                                            </a></td>
                                        <td>
                                            <?php echo $itemname['item_name']; ?>
                                        </td>
                                        <td>
                                            <?php echo date('d-m-Y', strtotime($detail['startdate'])); ?>
                                        </td>
                                        <td>
                                            <?php
                                            if ($detail['status'] == 'C') {
                                                echo date('d-m-Y', strtotime($detail['enddate'])) . '/<br>' .  date('d-m-Y', strtotime($detail['complete_date']));
                                            } else {
                                                echo date('d-m-Y', strtotime($detail['enddate']));
                                            } ?>
                                        </td>
                                        <td>
                                            <?php echo sprintf('%.2f', $detail['plannedqty']); ?>
                                        </td>
                                        <td>
                                            <?php echo ($detail['status'] == 'C') ? (sprintf('%.2f', $detail['plannedqty'])) : '0.00'; ?>
                                        </td>
                                        <td>
                                            <strong>
                                                <?php
                                                $user_id = $_SESSION['Auth']['User']['id'];
                                                $controllerName = $this->request->params['controller'];
                                                $actionName = $this->request->params['action'];
                                                $user_permission = $this->comman->finduserpermisson($user_id, $controllerName, $actionName);

                                                $fileurl = "admin/production/status";
                                                if (in_array($fileurl, $role_permissions)) {
                                                    if ($detail['status'] == 'C') {
                                                        echo $this->Html->link(
                                                            '',
                                                            ['action' => 'status', $detail->id, 'O'],
                                                            [
                                                                'class' => 'fa fa-check-circle',
                                                                'style' => 'font-size: 20px !important; color:red;margin-right:4px !important;',
                                                                "onClick" => "javascript: return confirm('Are you sure do you want to Open this Production Order')"
                                                            ]
                                                        );
                                                    } else {
                                                        echo $this->Html->link('', [
                                                            'action' => 'status',
                                                            $detail->id,
                                                            'C'
                                                        ], [
                                                            'class' => 'fa fa-check-circle',
                                                            'style' => 'font-size: 20px !important; color:green; margin-right:4px !important;',
                                                            "onClick" => "javascript: return confirm('Are you sure do you want to close this Production Order')"
                                                        ]);
                                                    }
                                                }
                                                $fileurl = "admin/production/delete";
                                                if (in_array($fileurl, $role_permissions)) {
                                                    if (empty($checkdproductionstart) && $detail['status'] != 'C') {
                                                        echo $this->Html->link('', [
                                                            'action' => 'delete',
                                                            $detail->id
                                                        ], [
                                                            'class' => 'fas fa-trash-alt',
                                                            'style' => 'font-size: 16px !important; color:#cd0404; margin-right:4px !important;',
                                                            "onClick" => "javascript: return confirm('Are you sure do you want to delete this Production Order')"
                                                        ]);
                                                    }
                                                }
                                                ?>
                                            </strong>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <?php echo $this->element('admin/pagination'); ?>
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
<!-- /.   content-wrapper -->
<div class="modal fade" id="myModal" style="width:51% !important;overflow-y: auto !important;" tabindex="-1"
    role="dialog" aria-labelledby="esModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width:100% !important;">
        <div class="modal-content personal">
            <div class="loader">
                <div class="es-spinner">
                    <i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $('.viewdetails').click(function(e) {
        e.preventDefault();
        $('#editsorts').modal('show').find('.modal-body').load($(this).attr('href'));
    });
</script>

<div class="modal fade" id="editsorts">
    <div class="modal-dialog" style="max-width:900px !important;">
        <div class="modal-content">
            <div class="modal-body"></div>
        </div>
    </div>
</div>

<script>
    $('.viewproductiondetails').click(function(e) {
        e.preventDefault();
        $('#editproductionsorts').modal('show').find('.modal-body').load($(this).attr('href'));
    });
</script>

<div class="modal fade" id="editproductionsorts">
    <div class="modal-dialog" style="max-width:900px !important;">
        <div class="modal-content">
            <div class="modal-body"></div>
        </div>
    </div>
</div>

<script>
    function cllbckretail2(id, cid) {
        $('.secrhcontract').val(id);
        $('#contrselectid').val(cid);
        $('#contractUL').hide();
        $('#contractUL1').hide();
    }
    $(function() {
        $('.secrhcontract').bind('keyup', function() {
            var pos = $(this).val();
            var check = 2;
            $('#contractUL').show();
            $('#contrselectid').val('');
            var count = pos.length;
            if (count > 0) {
                $.ajax({
                    type: 'POST',
                    url: '<?php echo ADMIN_URL; ?>production/getcontract',
                    data: {
                        'fetch': pos,
                        'check': check
                    },
                    success: function(data) {
                        if (data) {
                            console.log(data);
                            $('#contractUL ul').html(data);
                            $('#contractUL1').hide();
                        } else {
                            $('#contractUL').hide();
                            $('#contractUL1').show();
                        }
                    },
                });
            } else {
                $('#contractUL').hide();
                $('#contractUL1').hide();
            }
        });
    });
</script>

<script>
    $(document).ready(function() {
        $("#mysubscription").bind("submit", function(event) {
            $.ajax({
                async: true,
                data: $("#mysubscription").serialize(),
                dataType: "html",
                type: "GET",
                url: "<?php echo ADMIN_URL; ?>Production/searchpodetail",

                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
                    $('#load2').css("display", "block"); // Show loader
                },
                success: function(data) {
                    $("#updt").html(data);
                },
                complete: function() {
                    $('#load2').css("display", "none"); // Hide loader
                },
                error: function() {
                    alert("An error occurred. Please try again.");
                    $('#load2').css("display", "none"); // Hide loader on error
                }

            });
            return false;
        });

        $(document).on('click', '.pagination a', function(e) {
            var target = $(this).attr('href');
            var res = target.replace("/Production/searchpodetail", "/Production");
            window.location = res;
            return false;
        });
    });
</script>