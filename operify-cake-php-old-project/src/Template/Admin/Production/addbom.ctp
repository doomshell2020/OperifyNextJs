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
    margin-left:0px !important;
  }

  #contractUL ul li a {
    color: black;
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
                    <?php echo $this->Form->create($category, array(
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
                                <input type="hidden" name="contract_id" id="contrselectid">
                                <?php echo $this->Form->input('contractname', array('class' => 'form-control secrhcontract', 'id' => 'contractnameid', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'required', 'placeholder' => 'Enter Contract Name')); ?>
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
                            <!-- <div class="col-sm-4 ">
                                <label for="inputEmail3" class="control-label">Routing<strong style='color:red;'>*</strong></label>
                                <?php echo $this->Form->input('routing', array('class' => 'form-control', 'type' => 'select', 'required', 'empty' => '----Select Routing----', 'label' => false, 'autofocus', 'autocomplete' => 'off')); ?>
                            </div>

                            <div class="col-sm-4 ">
                                <label for="inputEmail3" class="control-label">Folder</label>
                                <?php echo $this->Form->input('folder', array('class' => 'form-control', 'type' => 'select', 'empty' => '----Select Folder----', 'label' => false, 'autofocus', 'autocomplete' => 'off')); ?>
                            </div> -->

                            <div class="col-sm-4 ">
                                <label for="inputEmail3" class="control-label">Comment</label>
                                <?php echo $this->Form->input('comment', array('class' => 'form-control', 'type' => 'textarea', 'label' => false, 'autofocus', 'autocomplete' => 'off')); ?>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <?php
                        // if (isset($category['id'])) {
                        //     echo $this->Form->submit(
                        //         'Update',
                        //         array('class' => 'btn btn-info pull-right', 'title' => 'Update','id'=> 'formsubmitbtn')
                        //     );
                        // } else {
                        //     echo $this->Form->submit(
                        //         'Save',
                        //         array('class' => 'btn btn-info pull-right', 'title' => 'Save','id'=> 'formsubmitbtn')
                        //     );
                        // }
                        ?>
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
                                        <th>Qty</th>
                                        <th>Price</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="product_containes">
                                    <!-- Data from AJAX request will be populated here -->
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" style="text-align:center;">
                                            Please save Contract first
                                        </td>
                                        <!-- <td>
                                            <input type="hidden" required="required" name="item_id" id="retail_id">
                                            <?php echo $this->Form->input('item_name', array('class' => 'form-control secrh-retails', 'id' => 'indent', 'type' => 'text', 'label' => false, 'required', 'autofocus', 'autocomplete' => 'off', 'placeholder' => 'Enter Item Name')); ?>
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
                                        <th>Operation</th>
                                        <th>Material</th>
                                        <th>Qty</th>
                                        <th>Price</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tbody id="product_containes1">
                                    <!-- Data from AJAX request will be populated here -->
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <td colspan="5" style="text-align:center;">
                                            Please save Contract first
                                        </td>
                                        <!--   <td><input type="hidden" name="item_id" required="required" id="retail_ids">
                                            <?php echo $this->Form->input('item_name', array('class' => 'form-control secrh-retail item_id', 'type' => 'text', 'required', 'label' => false, 'placeholder' => 'Enter Item name', 'autofocus', 'autocomplete' => 'off', 'id' => 'itemname')); ?>
                                            <div id="testUL" class="test1" style="display:none;">
                                                <ul></ul>
                                            </div>
                                        </td>-->
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
                                        <td colspan="3" style="text-align:center;">
                                            Please save Contract first
                                        </td>
                                        <!-- <td>Main operation</td>
                                        <td><input type="text" required="required" class="numbe" autocomplete="off"
                                                style="text-align:end;" name="operation_cost" id=""></td>
                                        <td><input type="text" required="required" class="numbe" autocomplete="off"
                                                style="text-align:end;" name="labour_cost" id=""></td> -->
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
                                        <th>Contract Name</th>
                                        <th>Type Of Cable</th>
                                        <th>Qty</th>
                                        <th>Issue Date</th>
                                        <th>Design Sheet</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="7" style="text-align:center;">
                                            Please save Contract first
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>

                    <!-- /.box-body -->
                    <div class="box-footer">
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
        $(".numbe").val(0);

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
        $('.secrh-retails').val(name);
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
                    url: '<?php echo ADMIN_URL; ?>Purchaseorder/getitemname',
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

</script>
<!-- Raw Materials item name -->
<script>

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
    $(document).ready(function () {
        $('#sevice_form').on('submit', function (e) {
            $("#formsubmitbtn").css("display", "none");
        });
        });
</script>