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
                    <?php echo $this->Form->create(
                        '',
                        array(
                            'class' => 'form-horizontal',
                            'enctype' => 'multipart/form-data',
                            'id' => 'sevice_form',
                            'validate'
                        )
                    ); ?>
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
                                role="tab" aria-controls="raw_materials" aria-selected="false">Indent Details
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
                                        <th>Qty</th>
                                        <th>Price</th>
                                        <!-- <th>Action</th> -->
                                    </tr>
                                </thead>
                                <tbody id="product_containes">
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
                                            <!-- <td>
                                                <span class="fas fa-trash-alt delete-button"
                                                    data-id="<?php echo $value['id']; ?>"
                                                    style="font-size: 21px; color:#cd0404"></span>
                                            </td> -->
                                        </tr>
                                    <?php } ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <!-- <td>
                                            <input type="hidden" required="required" name="item_id" id="retail_id">
                                            <?php echo $this->Form->input('item_name', array('class' => 'form-control secrh-retails', 'id' => 'indent', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'placeholder' => 'Enter Item Name')); ?>
                                            <div id="test1UL" style="display:none;">
                                                <ul></ul>
                                            </div>
                                        </td> -->
                                    </tr>
                                </tfoot>

                            </table>
                        </div>

                        <div class="tab-pane fade" id="raw_materials" role="tabpanel"
                            aria-labelledby="raw_materials-tab">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>S No.</th>
                                        <th>Indent Id</th>
                                        <th>Product</th>
                                        <th>Issue By</th>
                                        <th>Issue Date</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php
                                    $z = 1;
                                    if (isset($indentpoid) && !empty($indentpoid)) {
                                        foreach ($indentpoid as $value) {
                                            $itemname = $this->comman->getitemname($value['finishedproduct_id']);
                                            $contractname = $this->comman->findcontractname($value['contract_id']);
                                            ?>
                                            <tr>
                                                <td>
                                                    <?php echo $z; ?>.
                                                </td>
                                                <td><a href="<?php echo SITE_URL; ?>admin/indentpo/viewindentpodetail/<?php echo $value['indent_id']; ?>"
                                                        class="viewindentpodetails">
                                                        <?php echo $value['indent_id']; ?>
                                                    </a></td>
                                                <td>
                                                    <?php echo $itemname['item_name']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $value['issued_name']; ?>
                                                </td>
                                                <td>
                                                    <?php echo date("d-m-Y", strtotime($value['created'])); ?>
                                                </td>
                                            </tr>
                                            <?php $z++;
                                        }

                                    } else { ?>
                                    <?php } ?>
                                </tbody>


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
                                                readonly style="text-align:end;" name="operation_cost"
                                                value="<?php echo $user['operation_cost'] ?>"></td>
                                        <td><input type="text" class="numbe" required="required" autocomplete="off"
                                                readonly style="text-align:end;" name="labour_cost"
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
                                        <th>Qty</th>
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
                        // if (isset($category['id'])) {
                        //     echo $this->Form->submit(
                        //         'Update',
                        //         array('class' => 'btn btn-info pull-right', 'title' => 'Update', 'id' => 'formsubmitbtn')
                        //     );
                        // } else {
                        //     echo $this->Form->submit(
                        //         'Add',
                        //         array('class' => 'btn btn-info pull-right', 'title' => 'Add', 'id' => 'formsubmitbtn')
                        //     );
                        // }
                        ?>
                        <?php
                        echo $this->Html->link('Back', [
                            'action' => 'billsofmaterials'
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

<script>
    $('.viewindentpodetails').click(function (e) {
        e.preventDefault();
        $('#indentpo').modal('show').find('.modal-body').load($(this).attr('href'));
    });
</script>

<div class="modal fade" id="indentpo">
    <div class="modal-dialog" style="max-width:900px !important;">
        <div class="modal-content">
            <div class="modal-body"></div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#sevice_form').on('submit', function (e) {
            $("#formsubmitbtn").css("display", "none");
        });
    });
</script>