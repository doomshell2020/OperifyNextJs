<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Production Orders
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo SITE_URL; ?>admin/production/productionorders"><i class="fa fa-home"></i>Home</a>
            </li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <style>
                #customers {
                    font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
                    border-collapse: collapse;
                    width: 100%;
                    margin-bottom: 20px;
                }

                #customers td,
                #customers th {
                    border: 1px solid #ddd;
                    padding: 8px;
                }

                #customers tr:nth-child(even) {
                    background-color: #f2f2f2;
                }

                #customers tr:hover {
                    background-color: #ddd;
                }

                #customers th {
                    padding-top: 12px;
                    padding-bottom: 12px;
                    text-align: left;
                    background-color: #c8c8c8;
                    color: #333333;
                }

                #testUL,
                #testULs {
                    position: relative;
                    display: none;
                }

                #testUL ul,
                #testULs ul {
                    position: absolute;
                    max-height: 140px;
                    overflow: scroll;
                    z-index: 999;
                    top: 100%;
                    left: 0px;
                    right: 0px;
                    list-style-type: none;
                    background-color: white;
                    padding-left: 0px;
                }

                #testUL ul li,
                #testULs ul li {
                    padding: 5px 8px;
                    border: 1px solid lightgray;
                }

                #testUL ul li a,
                #testULs ul li a {
                    color: black;
                }

                .select2.select2-container .select2-selection {
                    margin-bottom: 0px !important;
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
                    margin-left: 0px !important;
                }

                #contractUL ul li a {
                    color: black;
                }
            </style>
            <!-- right column -->
            <div class="col-md-12">
                <!-- Horizontal Form -->
                <div class="box box-info">
                    <?php echo $this->Flash->render(); ?>

                    <!-- /.box-header -->
                    <!-- form start -->
                    <?php echo $this->Form->create(
                        '',
                        array(
                            'class' => 'form-horizontal',
                            'enctype' => 'multipart/form-data',
                            'onsubmit' => "return validateForm()",
                            'name' => 'myForm',
                            'id' => 'sevice_form',
                        )
                    ); ?>
                    <div class="box-body">
                        <div class="form-group" style="margin-bottom:0px;">
                            <div class="row">

                                <div class="col-sm-3" style="margin-bottom:15px;">
                                    <label for="inputEmail3">Production Order No. <strong
                                            style="color:red;">*</strong></label>
                                    <?php echo $this->Form->input('po_id', array('class' => 'form-control', 'id' => 'bill_no', 'type' => 'text', 'label' => false, 'autofocus', 'empty' => 'Production Order No.', 'autocomplete' => 'off', 'required', 'value' => $newproductorderid, 'readonly')); ?>
                                </div>
                                <script>
                                    $(document).ready(function () {
                                        $('#fdatefrom1').datepicker({
                                            dateFormat: 'dd-mm-yy',
                                            yearRange: '2018:2030',
                                            changeMonth: true,
                                            changeYear: true,
                                            autoclose: true,
                                            onSelect: function (date) {
                                                var selectedDate = new Date(date);
                                                var endDate = new Date(selectedDate);
                                                endDate.setDate(selectedDate);
                                            }
                                        });
                                        $('#fdatefrom1').datepicker('setDate', 'today');
                                    });
                                </script>
                                <div class="col-md-3">
                                    <label for="inputEmail3" class="control-label">Date </label>
                                    <?php echo $this->Form->input('issuedate', array('class' => 'form-control', 'id' => 'fdatefrom1', 'readonly', 'placeholder' => 'Date', 'label' => false, 'readonly')); ?>
                                </div>


                                <div class="col-sm-3">
                                    <label for="inputEmail3" class=" control-label"
                                        style="text-align: left !important">Contract
                                        Name<strong style="color:red;">*</strong></label>

                                    <input type="hidden" name="contract_id" id="contrselectid" required>

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

                                <div class="col-md-3">
                                    <label for="inputEmail3" class="control-label"
                                        style="text-align: left !important">Product<strong
                                            style="color:red;">*</strong></label>
                                    <?php echo $this->Form->input('item_id', [
                                        'class' => 'form-control data_req',
                                        'type' => 'select',
                                        'label' => false,
                                        'empty' => '-- Select Product--',
                                        'autofocus',
                                        'required',
                                        'autocomplete' => 'off',
                                        'id' => 'item_id_pro'
                                    ]); ?>
                                </div>


                                <div class="col-sm-8" style="margin:auto;">
                                    <span class="showdata"></span>
                                </div>
                                <div class="col-sm-3" style="margin-bottom:15px;">
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="ctpcontent form-group" style="display:none">
                        <div class="col-sm-12">
                            <table id="customers" width = "100%">
                                <thead>
                                    <tr class="totalColumn">
                                        <th width = "30%">Product</th>
                                        <th width = "10%">Order Qty</th>
                                        <th width = "10%">Pending Qty</th>
                                        <th width = "10%">Planned Qty</th>
                                        <th width = "05%">UOM</th>
                                        <th width = "08%">Start Date</th>
                                        <th width = "08%">End Date</th>
                                        <th width = "08%">Total Days</th>
                                        <!-- <th width = "10%">Action</th> -->
                                    </tr>
                                </thead>
                                <tbody class="product_containes" id="product_containes">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                    <?php
                    if (isset($location['id'])) {
                        echo $this->Form->submit(
                            'Update',
                            array('class' => 'btn btn-info pull-right', 'id' => 'formsubmitbtn', 'title' => 'Update')
                        );
                    } else {
                        echo $this->Form->submit(
                            'Submit',
                            array('class' => 'btn btn-info pull-right', 'id' => 'formsubmitbtn', 'title' => 'Add')
                        );
                    }
                    ?>
                    <?php
                    echo $this->Html->link('Back', [
                        'action' => 'productionorders'
                    ], ['class' => 'btn btn-default']); ?>
                </div>
                <!-- /.box-footer -->
                <?php echo $this->Form->end(); ?>

                <div class="poctpcontent" style="display:none">
                        <div >
                            <table class="table table-bordered table-striped" id="customerspo" width = "100%">
                                <thead>
                                    <tr class="totalColumn1">
                                        <th width = "10%">Production Order No.</th>
                                        <th width = "10%">Issue Date</th>
                                        <th width = "40%">Product</th>
                                        <th width = "10%">Planned Qty</th>
                                        <th width = "10%">Start Date</th>
                                        <th width = "10%">End Date</th>
                                        <th width = "10%">Total Days</th>
                                    </tr>
                                </thead>
                                <tbody class="po_product_containes" id="po_product_containes">
                                </tbody>
                            </table>
                        </div>
                    </div>




            </div>
        </div>
        <!--/.col (right) -->
</div>
<!-- /.row -->
</section>
<!-- /.content -->
</div>


<!-- <script type="text/javascript">

    function finisheditems(contractid) {
        $('.product_containes').html('');
        if (contractid != "") {
            $.ajax({
                type: 'POST',
                url: '<?php echo ADMIN_URL; ?>production/finisheditems',
                data: {
                    'id': contractid
                },
                success: function (data) {
                    $(".ctpcontent").css("display", "block");
                    $('.product_containes').html(data);
                },
            });
        } else {
            $(".ctpcontent").css("display", "none");
        }
    };

</script> -->

<script type="text/javascript">

    function getcompletepo(contractid) {
        $('.po_product_containes').html('');
        if (contractid != "") {
            $.ajax({
                type: 'POST',
                url: '<?php echo ADMIN_URL; ?>production/getcompletepo',
                data: {
                    'contractid': contractid
                },
                success: function (data) {
                    $(".poctpcontent").css("display", "block");
                    $('.po_product_containes').html(data);
                },
            });
        } else {
            $(".poctpcontent").css("display", "none");
        }
    };
</script>

<script>
    $('#item_id_pro').on('change', function () {
        var productid = $(this).val();
        var contractid =  $('#contrselectid').val();
       
        $('.product_containes').html('');
        if (productid != "") {
            $.ajax({
                type: 'POST',
                url: '<?php echo ADMIN_URL; ?>production/finisheditems',
                data: {
                    'id': contractid,
                    'productid': productid
                },
                success: function (data) {
                    $(".ctpcontent").css("display", "block");
                    $('.product_containes').html(data);
                },
            });
        } else {
            $(".ctpcontent").css("display", "none");
        }

    });
</script>



<script>
    
    function getcontractfinished(contractid) {
        if (contractid != "") {
            $.ajax({
                type: 'POST',
                url: '<?php echo ADMIN_URL; ?>production/getcontractfinished',
                data: {
                    'contract_id': contractid,

                },

                success: function (data) {
                    if (data) {
                        var select = $("#item_id_pro");
                        select.empty();
                        select.append($('<option>', {
                            value: '',
                            text: '-- Select Product--'
                        }));
                        var dataArray = JSON.parse(data);
                        dataArray.forEach(function (item) {
                            select.append($('<option>', {
                                value: item.id,
                                text: item.item_name,
                            }));
                        });
                    }
                },

            });

        };

    };



    function cllbckretail2(id, cid) {
        $('.secrhcontract').val(id);
        $('#contrselectid').val(cid);
        getcontractfinished(cid);
        getcompletepo(cid);
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
    function validateForm() {
        var valid = true;

        var itemcount = document.getElementById("totalitemCount").value;
        
        for (var j = 1; j <= itemcount; j++) {
            var itemqty = document.getElementById("quan-" + j).value;

            if (itemqty !== '') {
                var itemname = document.getElementById("indentid-" + j).value;
                var totaldays = document.getElementById("totaldays-" + j).value;
                if (totaldays === '') {
                    alert("Total days must be filled out for item " + itemname);
                    valid = false;
                    break;
                }
            }
        };


        if (valid) {
            $("#formsubmitbtn").css("display", "none");
        }

        return valid;
    }
</script>


