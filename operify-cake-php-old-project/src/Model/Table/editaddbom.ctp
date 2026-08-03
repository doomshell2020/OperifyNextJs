<style>
    .input_fields_wrap .form-control {
        margin-bottom: 15px;
    }
</style>

<style>
    #test1UL {
        position: relative;
    }

    #test1UL ul {
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

    #test1UL ul li {
        padding: 5px 8px;
        border: 1px solid lightgray;
    }

    #test1UL ul li a {
        color: black;
    }

    .preview {
        margin-right: 15px;
    }

    .input_fields_wrap .form-control {
        margin-bottom: 15px;
    }
</style>

<style>
    #finished_products,
    #raw_materials,
    #operations_costs,
    #designsheet {
        opacity: 1;
    }

    #testUL ul {
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

    #testUL {
        position: relative;
    }

    #testUL ul li a {
        color: black;
    }

    .active .nav-link {
        background-color: #e3e5e3;
    }
</style>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Bill of Materials
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo SITE_URL; ?>admin/Production/billsofmaterials"><i class="fa fa-home"></i>Home</a>
            </li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">

            <!-- right column -->
            <div class="col-md-12">
                <!-- Horizontal Form -->
                <div class="box box-info">
                    <?php echo $this->Flash->render(); ?>
                    <div class="box-header with-border">
                    </div>
                    <!-- /.box-header -->
                    <!-- form start -->
                    <?php echo $this->Form->create('', array(
                        'class' => 'form-horizontal',
                        'enctype' => 'multipart/form-data',
                        'id' => 'sevice_form',
                        'validate'
                    )); ?>
                    <div class="box-body">
                        <div class="form-group">
                            <div class="col-sm-4">
                                <label for="inputEmail3" class=" control-label"
                                    style="text-align: left !important">Contract
                                    Name<strong style="color:red;">*</strong></label>
                                <input type="hidden" name="contract_id" id="contrselectid"
                                    value="<?php echo $user['contract_id']; ?>">
                                <?php
                                $contractname = $this->comman->findcontractname($user['contract_id']);
                                echo $this->Form->input('contractname', array('class' => 'form-control secrhcontract', 'id' => 'contractnameid', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'required', 'value' => $contractname['title'], 'readonly')); ?>
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

                            <div class="col-sm-4 ">
                                <label for="inputEmail3" class="control-label">Comment</label>
                                <?php echo $this->Form->input('comment', array('class' => 'form-control', 'type' => 'textarea', 'label' => false, 'autofocus', 'readonly', 'autocomplete' => 'off', 'value' => $user['comment'])); ?>
                            </div>
                        </div>
                    </div>
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link" id="finished_products-tab" data-toggle="tab" href="#finished_products"
                                role="tab" aria-controls="finished_products" aria-selected="true">Finished Products</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="raw_materials-tab" data-toggle="tab" href="#raw_materials"
                                role="tab" aria-controls="raw_materials" aria-selected="false">Raw Materials
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="operations_costs-tab" data-toggle="tab" href="#operations_costs"
                                role="tab" aria-controls="operations_costs" aria-selected="false">Operations Costs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="designsheet-tab" data-toggle="tab" href="#designsheet" role="tab"
                                aria-controls="designsheet" aria-selected="false">Design Sheet
                            </a>

                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade " id="finished_products" role="tabpanel"
                            aria-labelledby="finished_products-tab">
                            <table class="table table-bordered table-striped" id="customFields">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Quota</th>
                                        <th>Price</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="product_containes">
                                    <!-- Data from AJAX request will be populated here -->
                                    <?php
                                    foreach ($product as $value) {
                                        $itemname = $this->Comman->getitemname($value['product_id']);
                                        ?>

                                        <tr id="producttr<?php echo $value['id']; ?>">
                                            <td>
                                                <input type="hidden" name="123" value="<?php echo $value['id'] ?>">
                                                <input type="hidden" name="finished_pro_123"
                                                    value="<?php echo $value['product_id'] ?>">
                                                <input type="text" name="produ123" readonly
                                                    value="<?php echo $itemname['item_name'] ?>">
                                            </td>
                                            <td>
                                                <input type="text" name="qua123" class="numbe" autocomplete="off"
                                                    value="<?php echo $value['quantity'] ?>" required="required" readonly>
                                                <?php echo (" " . $unitname['unit_name']) ?>
                                            </td>
                                            <td>
                                                <input type="text" name="123" class="numbe" required="required"
                                                    style="text-align:end;" autocomplete="off" readonly
                                                    value="<?php echo $value['price'] ?>">
                                            </td>
                                            <td>
                                                <span class="fas fa-trash-alt delete-button"
                                                    data-id="<?php echo $value['id']; ?>"
                                                    style="font-size: 21px; color:#cd0404"></span>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td>
                                            <input type="hidden" required="required" name="item_id" id="retail_id">
                                            <?php echo $this->Form->input('item_name', array('class' => 'form-control secrh-retails', 'id' => 'indent', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'placeholder' => 'Enter Item Name')); ?>
                                            <div id="test1UL" style="display:none;">
                                                <ul></ul>
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>

                            </table>
                        </div>

                        <div class="tab-pane fade" id="raw_materials" role="tabpanel"
                            aria-labelledby="raw_materials-tab">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Operation</th>
                                        <th>Material</th>
                                        <th>Qty Required</th>
                                        <th>Price</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tbody id="product_containes1">
                                    <!-- Data from AJAX request will be populated here -->
                                    <?php foreach ($raw as $value) {
                                        $itemname = $this->Comman->getitemname($value['product_id']);
                                        ?>
                                        <tr id=rawtr<?php echo $value['id']; ?>>
                                            <td>Main operation</td>
                                            <td>
                                                <input type="hidden" name="raw_materials_i123"
                                                    value="<?php echo $value['product_id'] ?>">
                                                <input type="text" name="produ123" readonly
                                                    value="<?php echo $itemname['item_name'] ?>">
                                            </td>
                                            <td>
                                                <input type="text" name="qua123" class="numbe" autocomplete="off"
                                                    value="<?php echo $value['quantity'] ?>" readonly required="required">
                                                <?php echo (" " . $unitname['unit_name']) ?>
                                            </td>
                                            <td>
                                                <input type="text" name="123" class="numbe" required="required"
                                                    style="text-align:end;" readonly autocomplete="off"
                                                    value="<?php echo $value['price'] ?>">
                                            </td>
                                            <td>
                                                <span class="fas fa-trash-alt delete-button1"
                                                    data-id="<?php echo $value['id']; ?>"
                                                    style="font-size: 21px; color:#cd0404"></span>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <td><input type="hidden" name="item_id" required="required" id="retail_ids">
                                            <?php echo $this->Form->input('item_name', array('class' => 'form-control secrh-retail item_id', 'type' => 'text', 'label' => false, 'placeholder' => 'Enter Item name', 'autofocus', 'autocomplete' => 'off', 'id' => 'itemname')); ?>
                                            <div id="testUL" class="test1" style="display:none;">
                                                <ul></ul>
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>

                            </table>
                        </div>

                        <div class="tab-pane fade" id="operations_costs" role="tabpanel"
                            aria-labelledby="operations_costs-tab">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Operation</th>
                                        <th>Operational Cost</th>
                                        <th>Labour Cost</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Main operation</td>
                                        <td><input type="text" class="numbe" required="required" autocomplete="off"
                                                style="text-align:end;" name="operation_cost"
                                                value="<?php echo $user['operation_cost'] ?>"></td>
                                        <td><input type="text" class="numbe" required="required" autocomplete="off"
                                                style="text-align:end;" name="labour_cost"
                                                value="<?php echo $user['labour_cost'] ?>"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="tab-pane fade" id="designsheet" role="tabpanel" aria-labelledby="designsheet-tab">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>S.No.</th>
                                        <th>Design Sheet No.</th>
                                        <th>Type Of Cable</th>
                                        <th>Quantity</th>
                                        <th>Issue Date</th>
                                        <th>Design Sheet</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php
                                    // $contractdetails = $this->comman->findcontractdetails($user['contract_id']);
                                    $counter = 1;
                                    foreach ($contractdetails as $contra) {
                                        $itemname = $this->Comman->getitemname($contra['item_id']); ?>
                                        <tr>
                                            <td>
                                                <?php echo $counter; ?>
                                            </td>
                                            <td>
                                                <?php echo $contra['designsheetno']; ?>
                                            </td>
                                            <td>
                                                <?php echo $itemname['item_name']; ?>
                                            </td>
                                            <td>
                                                <?php echo $contra['quantity']; ?>
                                            </td>

                                            <td>
                                                <?php echo date('d-m-Y', strtotime($contra['datefrom'])); ?>
                                            </td>
                                            <td>
                                                <a target="_blank"
                                                    href="<?php echo SITE_URL . 'designsheet/' . $contra['design_sheet']; ?>"
                                                    title="Design Sheet" data-method="post" data-toggle="tooltip"><span
                                                        class="fa fa-download fa-lg text-green"></span></a> &nbsp; &nbsp;
                                                <?php $i = 1;
                                                for ($i = 1; $i < 6; $i++) {
                                                    if ($contra['r' . $i]) { ?>
                                                        <a target="_blank"
                                                            href="<?php echo SITE_URL . 'designsheet/' . $contra['r' . $i]; ?>"
                                                            title="R<?php echo $i ?>" data-method="post" data-toggle="tooltip"><span
                                                                class="fa fa-download fa-lg text-green"></span></a> &nbsp; &nbsp;
                                                    <?php } ?>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <?php $counter++;
                                    }
                                    ?>

                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- /.box-body -->
                    <div class="box-footer">
                        <?php
                        if (isset($category['id'])) {
                            echo $this->Form->submit(
                                'Update',
                                array('class' => 'btn btn-info pull-right', 'title' => 'Update', 'id' => 'formsubmitbtn')
                            );
                        } else {
                            echo $this->Form->submit(
                                'Add',
                                array('class' => 'btn btn-info pull-right', 'title' => 'Add', 'id' => 'formsubmitbtn')
                            );
                        }
                        ?>
                        <?php
                        echo $this->Html->link('Back', [
                            'action' => 'index'
                        ], ['class' => 'btn btn-default']); ?>
                    </div>
                    <!-- /.box-footer -->
                    <?php echo $this->Form->end(); ?>
                </div>
            </div>
            <!--/.col (right) -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>

<script>
    $(document).ready(function () {
        $(".delete-button").on("click", function () {
            var id = $(this).attr("data-id");
            // alert(id)
            $.ajax({
                type: 'POST',
                url: '<?php echo ADMIN_URL; ?>production/deletedata',
                data: {
                    'fetch': id
                },

                success: function (data) {
                    document.getElementById("producttr" + id).remove();
                }
            });
        });



        $(".delete-button1").on("click", function () {
            var id = $(this).attr("data-id");
            // alert(id)
            $.ajax({
                type: 'POST',
                url: '<?php echo ADMIN_URL; ?>production/deletedata1',
                data: {
                    'fetch': id
                },

                success: function (data) {
                    document.getElementById("rawtr" + id).remove();
                }
            });
        });


        $('.numbe').keypress(function (eve) {
            if ((eve.which != 46 || $(this).val().indexOf('.') != -1) && (eve.which < 48 || eve.which > 57) || (eve.which == 46 && $(this).caret().start == 0)) {
                eve.preventDefault();
            }
            $('.numbe').keyup(function (eve) {
                if ($(this).val().indexOf('.') == 0) {
                    $(this).val($(this).val().substring(1));
                }
            });
        });
    });
</script>

<script type="text/javascript">

    function testtt(retailID) {
        $.ajax({
            type: 'POST',
            url: '<?php echo ADMIN_URL; ?>production/getitemsname',
            data: {
                'fetch': retailID
            },
            success: function (data) {
                // console.log(data);
                $("#product_containes").append(data); // Append received data to tbody
            },
        });
    }

    //item name
    function cllbckretail0(name, id) {
        // $('.secrh-retails').val(name);
        $('#test1UL').hide();
        testtt(id);
        $.ajax({
            type: 'POST',
            url: '<?php echo ADMIN_URL; ?>Purchaseorder/getitemdetail',
            data: {
                'fetch': id
            },
            success: function (data) {
                $('.secrh-retails').val('');
                $('.secrh-retails').prop('required', false);
            },
        });
    }


    //get item name
    $(function () {
        $('.secrh-retails').bind('keyup', function () {
            var pos = $(this).val();
            var check = 0;
            $('#test1UL').show();
            $('#retail_id').val('');
            var count = pos.length;
            if (count > 0) {
                $.ajax({
                    type: 'POST',
                    url: '<?php echo ADMIN_URL; ?>Purchaseorder/getfinisheditemname',
                    data: {
                        'fetch': pos,
                        'check': check
                    },
                    success: function (data) {
                        $('#test1UL ul').html(data);

                    },
                });
            } else {
                $('#test1UL').hide();
            }
        });
    });
</script>


<!-- Raw Materials item name -->
<script>

    function testtt1(retailID) {

        $.ajax({
            type: 'POST',
            url: '<?php echo ADMIN_URL; ?>production/getmaterialname',
            data: {
                'fetch': retailID
            },

            success: function (data) {
                console.log(data);
                $("#product_containes1").append(data); // Append received data to tbody

            },
        });
    }


    function cllbckretail(name, id) {
        $('.secrh-retail').val(name);
        $('#testUL').hide();
        testtt1(id);
        $.ajax({
            type: 'POST',
            url: '<?php echo ADMIN_URL; ?>Additem/getitemdetail',
            data: {
                'fetch': id
            },
            success: function (data) {
                $('.secrh-retail').val('');
                $('.secrh-retail').prop('required', false);
            },
        });
    }


    $(function () {
        $('.secrh-retail').bind('keyup', function () {
            var pos = $(this).val();
            var check = 0;
            $('#testUL').show();
            $('#retail_ids').val('');
            var count = pos.length;
            if (count > 0) {
                $.ajax({
                    type: 'POST',
                    url: '<?php echo ADMIN_URL; ?>Additem/getitemname',
                    data: {
                        'fetch': pos,
                        'check': check
                    },
                    success: function (data) {
                        $('#testUL ul').html(data);
                    },
                });
            } else {
                $('#testUL').hide();
            }
        });
    });
</script>

<!-- Contract name -->
<script>
    function cllbckretail2(id, cid) {
        $('.secrhcontract').val(id);
        $('#contrselectid').val(cid);
        $('#contractUL').hide();
        $('#contractUL1').hide();
    }
    $(function () {
        $('.secrhcontract').bind('keyup', function () {
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
                    success: function (data) {
                        if (data) {
                            console.log(data);
                            $('#contractUL ul').html(data);
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
    $(document).ready(function () {
        $('#sevice_form').on('submit', function (e) {
            $("#formsubmitbtn").css("display", "none");
        });
    });
</script>