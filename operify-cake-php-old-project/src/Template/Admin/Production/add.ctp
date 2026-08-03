<style>
    .input_fields_wrap .form-control {
        margin-bottom: 15px;
    }
</style>
<style>
    #poidUL {
        position: relative;
    }

    #poidUL ul {
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

    #testUL ul li {
        padding: 5px 8px;
        border: 1px solid lightgray;
    }

    #testUL ul li a {
        color: black;
    }

    .preview {
        margin-right: 15px;
    }

    .dataTables_wrapper.form-inline.dt-bootstrap.no-footer {
        margin-top: 0px;
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

<script>
    // function checkmachinentry(id) {
    //     $.ajax({
    //         type: 'POST',
    //         url: '<?php echo ADMIN_URL; ?>Production/checkmachinentry',
    //         data: {
    //             'machineid': id
    //         },
    //         success: function (data) {
    //             var obj = JSON.parse(data);
    //             if (obj) {
    //                 $('#msg').css('display', 'block');
    //                 $(".secrh-retail ").val('');
    //                 $("#retail_ids").val('');
    //             } else {
    //                 $('#msg').css('display', 'none');
    //             } 
//    Ma'am
//    Ma'am, I will join from April 7th.
// Please send me the offer letter.
// Also, kindly share the list of documents required for joining.
    //         },
    //     });
    // };

    function machinereading(id) {
        $.ajax({
            type: 'POST',
            url: '<?php echo ADMIN_URL; ?>production/getmachinereading',
            data: {
                'fetch': id,
            },
            success: function (data) {
                var jsonData = JSON.parse(data);
                if (jsonData.nextday8am != null) {
                    $('#read8am').val(jsonData.nextday8am);
                    $('#read8am').prop('readonly', true);
                    $('#read8pm').val('');
                    $('#next8am').val('');
                } else {
                    $('#read8am').val('');
                    $('#read8pm').val('');
                    $('#next8am').val('');
                    $('#read8am').prop('readonly', false);
                }
            },

        });
    }

    function cllbckretail0(id, cid, sid) {
        $('.secrh-retail').val(id);
        machinereading(cid);
        $('#retail_ids').val(cid);
        $('#testUL').hide();
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
                    url: '<?php echo ADMIN_URL; ?>production/getname',
                    data: {
                        'fetch': pos,
                        'check': check
                    },
                    success: function (data) {
                        if (data) {
                            $('#testUL ul').html(data);
                            $('#testUL1').hide();
                        } else {
                            $('#testUL').hide();
                            $('#testUL1').show();
                        }
                    },
                });
            } else {
                $('#testUL').hide();
                $('#testUL1').hide();
            }
        });
    });

    // ------------------- Production (Km.) required validtion------------//

    $(document).ready(function () {
        $("#planneddatafill").on('change', function (e) {
            var value = e.target.value;
            if (e.target.value == '') {
                $('.data_req').prop('required', false);
            } else {
                $('.data_req').prop('required', true);

            }
        });
    });

    //----------------------End Production (Km.) requred validtion-------------//

    //-----------------------Only float value accept----------------------------------------
    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode != 46 && charCode > 31 &&
            (charCode < 48 || charCode > 57))
            return false;

        return true;
    }
</script>


<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Daily Sheet
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo SITE_URL; ?>admin/production"><i class="fa fa-home"></i>Home</a></li>
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
                        $productions,
                        array(
                            'class' => 'form-horizontal',
                            'enctype' => 'multipart/form-data',
                            'id' => 'sevice_form',
                            'name' => 'myForm',
                            'validate'
                        )
                    ); ?>
                    <div class="box-body">

                        <div class="form-group">
                            <div class="row">
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
                                    <label for="inputEmail3" class=" control-label "
                                        style="text-align: left !important">Date<strong
                                            style="color:red;">*</strong></label>
                                    <?php echo $this->Form->input('production_date', array('class' => 'form-control', 'id' => 'fdatefrom1', 'readonly', 'placeholder' => 'Date', 'label' => false, 'readonly')); ?>
                                </div>





                                <div class="col-sm-3">
                                    <label for="inputEmail3" class=" control-label"
                                        style="text-align: left !important">Contract
                                        Name<strong style="color:red;">*</strong></label>

                                    <input type="hidden" name="contract_id" id="contrselectid" required>

                                    <?php echo $this->Form->input('contractname', array('class' => 'form-control secrhcontract', 'id' => 'contractnameid', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'required', 'placeholder' => 'Contract Name')); ?>
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
                                    <span id="contract_id" style="color: red;font-size:12px;"></span>
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


                                <div class="col-md-3">
                                    <label for="inputEmail3" class=" control-label "
                                        style="text-align: left !important">Production Order No.<strong
                                            style="color:red;">*</strong></label>

                                    <?php echo $this->Form->input('po_id', [
                                        'class' => 'form-control data_req',
                                        'type' => 'select',
                                        'label' => false,
                                        'empty' => '-- Select Production Order No.--',
                                        'autofocus',
                                        'required',
                                        'autocomplete' => 'off',
                                        'id' => 'po_pro'
                                    ]); ?>
                                </div>

                                <div class="col-md-3">
                                    <label for="inputEmail3" class="control-label" style="text-align: left !important">
                                        Process<strong style="color:red;">*</strong>
                                    </label>
                                    <select id="process" name="productprocess_id" class="form-control">
                                        <option value="" disabled selected>-- Select Process --</option>
                                        <!-- Default option -->
                                        <?php foreach ($process_id as $value) { ?>
                                            <option value="<?php echo $value['id']; ?>">
                                                <?php echo $value['process_name']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <div>
                                        <span id="msgprocess" style="color:red;"></span>
                                    </div>
                                </div>


                                <div class="col-md-3">
                                    <label for="inputEmail3" class=" control-label"
                                        style="text-align: left !important">Machine Name<strong
                                            style="color:red;">*</strong></label>
                                    <input type="hidden" required="required" name="machines_id" id="retail_ids">
                                    <?php echo $this->Form->input('machinename', array('class' => 'form-control secrh-retail', 'id' => 'itemname', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'required', 'placeholder' => 'Machine Name')); ?>
                                    <div id="testUL" style="display:none;">
                                        <ul></ul>
                                    </div>
                                    <div id="testUL1" style="display:none;">
                                        <ul>
                                            <li
                                                style="padding: 5px 8px;list-style:none;color: black;font-weight: bold;margin-left:-32px; border: 1px solid lightgray;">
                                                No Record Found</li>
                                        </ul>
                                    </div>

                                    <div style="display: none;" id="msg">
                                        <span style="color:red;">This Machine Already Exits </span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <label for="inputEmail3" class=" control-label"
                                        style="text-align: left !important">Reading(Current day)08.00AM<strong
                                            style="color:red;">*</strong></label>
                                    <?php echo $this->Form->input('reading8am', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'required', 'placeholder' => 'Reading(Current day)08.00AM', 'id' => 'read8am', 'autofocus', 'onkeypress' => 'return isNumberKey(event)', 'autocomplete' => 'off', 'onchange' => 'checkmachinereading(this)')); ?>
                                </div>

                                <div class="col-md-3">
                                    <label for="inputEmail3" class=" control-label"
                                        style="text-align: left !important">Reading(Current day)08.00PM<strong
                                            style="color:red;">*</strong></label>
                                    <?php echo $this->Form->input('reading8pm', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'required', 'placeholder' => 'Reading(Current day)08.00PM', 'autofocus', 'autocomplete' => 'off', 'onkeypress' => 'return isNumberKey(event)', 'id' => 'read8pm', 'onchange' => 'checkmachinereading(this)')); ?>
                                    <div style="display: none;" id="reading8pm">
                                        <span style="color:red;">Reading at 8.00 PM can't be less than reading at 8.00
                                            AM</span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <label for="inputEmail3" class=" control-label"
                                        style="text-align: left !important">Reading(Next day)08.00AM<strong
                                            style="color:red;">*</strong></label>
                                    <?php echo $this->Form->input('nextday8am', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'required', 'placeholder' => 'Reading(Next day)08.00AM', 'autofocus', 'autocomplete' => 'off', 'onkeypress' => 'return isNumberKey(event)', 'id' => 'next8am', 'onchange' => 'checkmachinereading(this)')); ?>
                                    <div style="display: none;" id="nextday8am">
                                        <span style="color:red;">Reading Next Day can't be less than reading at 8.00
                                            PM</span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <label for="inputEmail3" class=" control-label" style="text-align: left !important">
                                        Planned Quantity<strong style="color:red;"></strong></label>
                                    <?php echo $this->Form->input('plan_qty', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'placeholder' => 'Planned Quantity', 'autofocus', 'autocomplete' => 'off', 'onkeypress' => 'return isNumberKey(event)')); ?>
                                </div>

                                <div class="col-md-3">
                                    <label for="inputEmail3" class=" control-label" style="text-align: left !important">
                                        Production (Shfit A)<strong style="color:red;">*</strong></label>
                                    <?php echo $this->Form->input('production_shift_a', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'placeholder' => 'Production Shfit A', 'autofocus', 'autocomplete' => 'off', 'required', 'onkeypress' => 'return isNumberKey(event)')); ?>
                                </div>

                                <div class="col-md-3">
                                    <label for="inputEmail3" class=" control-label" style="text-align: left !important">
                                        Production (Shfit B)<strong style="color:red;">*</strong></label>
                                    <?php echo $this->Form->input('production_shift_b', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'placeholder' => 'Production Shfit B', 'autofocus', 'autocomplete' => 'off', 'required', 'onkeypress' => 'return isNumberKey(event)')); ?>
                                </div>

                                <div class="col-md-3">
                                    <label for="inputEmail3" class=" control-label" style="text-align: left !important">
                                        Manpower in Day<strong style="color:red;">*</strong></label>
                                    <?php echo $this->Form->input('manpower_day', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'required', 'placeholder' => 'Manpower in Day', 'autofocus', 'onkeypress' => 'return isNumberKey(event)', 'autocomplete' => 'off')); ?>
                                </div>

                                <div class="col-md-3">
                                    <label for="inputEmail3" class=" control-label" style="text-align: left !important">
                                        Manpower in Night<strong style="color:red;">*</strong></label>
                                    <?php echo $this->Form->input('manpower_night', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'placeholder' => 'Manpower in Night', 'required', 'onkeypress' => 'return isNumberKey(event)', 'autofocus', 'autocomplete' => 'off')); ?>
                                </div>

                                <div class="col-md-3">
                                    <label for="inputEmail3" class=" control-label" style="text-align: left !important">
                                        Scrap (Shfit A + B)</label>
                                    <?php echo $this->Form->input('scrap', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'placeholder' => 'Scrap Shfit A + B', '', 'autofocus', 'autocomplete' => 'off')); ?>
                                </div>



                                <div class="col-md-3">
                                    <label for="inputEmail3" class="control-label">Completed :</label>
                                    <label class="radio-inline" style="margin-right: 15px;">
                                        <input type="radio" name="completed" class="mode radio-inline checkstr"
                                            value="Y">YES
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="completed" class="mode radio-inline checkstr"
                                            value="N"> NO
                                    </label>

                                </div>


                                <div class="col-md-12">
                                    <label for="inputEmail3" class=" control-label" style="text-align: left !important">
                                        Remark</label>
                                    <?php echo $this->Form->input('remark', array('class' => 'form-control', 'type' => 'textarea', 'label' => false, 'placeholder' => 'Remark', '', 'autofocus', 'autocomplete' => 'off')); ?>
                                </div>

                            </div>
                        </div>
                    </div>

                    <script>
                        $(document).ready(function () {
                            $('#sevice_form').submit(function (event) {
                                $('.addgen').hide();
                            });
                        });
                    </script>

                    <!-- /.box-body -->
                    <div class="box-footer">
                        <?php
                        if (isset($category['id'])) {
                            echo $this->Form->submit(
                                'Update',
                                array('class' => 'btn btn-info pull-right ', 'title' => 'Update')
                            );
                        } else {
                            echo $this->Form->submit(
                                'Add',
                                array('class' => 'btn btn-info pull-right addgen', 'id' => 'formsubmitbtn', 'title' => 'Add')
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

                    <div class="ctpcontent form-group" style="display:none">
                        <div class="col-sm-12">
                            <table class="table table-bordered table-striped" id="customers" width="100%">
                                <thead>
                                    <tr class="totalColumn">
                                        <th width="05%">S.No.</th>
                                        <th width="35%">Process Name</th>
                                        <th width="15%">Start Date</th>
                                        <th width="15%">End Date</th>
                                        <th width="15%">Planned Qty(KM)</th>
                                        <th width="15%">Prepared Qty(KM)</th>
                                    </tr>
                                </thead>
                                <tbody class="product_containes" id="product_containes">
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

<!-------- Data Date picker !---->
<script>


    function validateForm() {
        var valid = true;
        var Vendorid = document.forms["myForm"]["contract_id"].value;
        if (Vendorid === "") {
            document.getElementById("contract_id").innerHTML = "Your entered contract does not exists";
            valid = false;
        }
        if (valid) {
            $("#formsubmitbtn").css("display", "none");
        }
        return valid;
    }

</script>


<script>
    $(document).ready(function () {
        $('#fdatefrom').datepicker({
            dateFormat: 'dd-mm-yy',
            yearRange: '2018:2030',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            onSelect: function (date) {

                var selectedDate = new Date(date);
                var endDate = new Date(selectedDate);
                endDate.setDate(selectedDate);

                $("#fendfrom").datepicker("option", "minDate", endDate);
                $("#fendfrom").val(date);
            }
        });
        $('#fdatefrom').datepicker('setDate', 'today');
        $('#fendfrom').datepicker({
            dateFormat: 'dd-mm-yy'
        });
        $('#fendfrom').datepicker('setDate', 'today');
    });
</script>





<script>
    function getvaluebyId(id) {
        return parseFloat(document.getElementById(id).value);
    }


    // function checkmachinentry(id) {
    //     $.ajax({
    //         type: 'POST',
    //         url: '<?php echo ADMIN_URL; ?>Production/checkmachinentry',
    //         data: {
    //             'machineid': id
    //         },
    //         success: function (data) {
    //             var obj = JSON.parse(data);
    //             if (obj) {
    //                 $('#msg').css('display', 'block');
    //                 $(".secrh-retail ").val('');
    //                 $("#retail_ids").val('');
    //             } else {
    //                 $('#msg').css('display', 'none');
    //             }
    //         },
    //     });
    // };


    function checkmachinereading() {
        const reading8am = getvaluebyId('read8am');
        const reading8pm = getvaluebyId('read8pm');
        const next8am = getvaluebyId('next8am');
        if (reading8am > reading8pm) {
            $('#reading8pm').css('display', 'block');
            $("#read8pm").val('');
        } else {
            $('#reading8pm').css('display', 'none');
        }
        if (reading8am > next8am) {
            $('#nextday8am').css('display', 'block');
            $("#next8am").val('');
        } else {
            $('#nextday8am').css('display', 'none');
        }
        if (reading8pm > next8am) {
            $('#nextday8am').css('display', 'block');
            $("#next8am").val('');
        } else {
            $('#nextday8am').css('display', 'none');
        }
    }
</script>



<script>
    $('#po_pro').on('change', function () {
        var po_id = $(this).val();
        $('.product_containes').html('');
        if (po_id != "") {
            $.ajax({
                type: 'POST',
                url: '<?php echo ADMIN_URL; ?>production/getdailysheet',
                data: {
                    'po_id': po_id,
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



<!-- <script>
    $('#process_pro').on('change', function () {
        var processid = $(this).val();
        var po_id = $('#po_pro').val();
        var product_id = $('#item_id_pro').val();
        if (po_id != "") {
            $.ajax({
                type: 'POST',
                url: '<?php echo ADMIN_URL; ?>production/checkprocesscompletion',
                data: {
                    'processid': processid,
                    'po_id': po_id,
                    'product_id': product_id
                },

                success: function (data) {
                    var obj = JSON.parse(data);
                    if (obj) {
                        document.getElementById("msgprocess").innerHTML = "This Process Already Completed.";
                        $("#process_pro").val('');
                    } else {
                        document.getElementById("msgprocess").innerHTML = "";
                    }
                },

            });
        } else {
            document.getElementById("msgprocess").innerHTML = "Choose first Production Order.";
            $("#process_pro").val('');
        };

    });
</script> -->

<script>
    $('#item_id_pro').on('change', function () {
        var productid = $(this).val();
        if (productid != "") {
            $.ajax({
                type: 'POST',
                url: '<?php echo ADMIN_URL; ?>production/getproductprocess',
                data: {
                    'productid': productid,
                },

                success: function (data) {
                    if (data) {
                        var select = $("#process_pro");
                        select.empty();
                        select.append($('<option>', {
                            value: '',
                            text: '-- Select Process--'
                        }));
                        var dataArray = JSON.parse(data);
                        dataArray.forEach(function (item) {
                            select.append($('<option>', {
                                value: item.id,
                                text: item.process_name,
                            }));
                        });
                    }
                },

            });

        };

    });
</script>


<script type="text/javascript">

    $("#item_id_pro").on('change', function () {
        var item_id = $(this).val();
        var contractid = $('#contrselectid').val();

        if (item_id != "") {
            $.ajax({
                type: 'POST',
                url: '<?php echo ADMIN_URL; ?>production/getproductionorder',
                data: {
                    'contractid': contractid,
                    'item_id': item_id
                },
                success: function (data) {
                    var select = $("#po_pro");
                    select.empty();
                    select.append($('<option>', {
                        value: '',
                        text: '--- Select Production Order No.---'
                    }));
                    var dataArray = JSON.parse(data);
                    dataArray.forEach(function (item) {
                        select.append($('<option>', {
                            value: item.po_id,
                            text: item.po_id,
                        }));
                    });
                }
            });
        }
    });


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
                    }
                });
            } else {
                $('#contractUL').hide();
                $('#contractUL1').hide();
            }
        });
    });
</script>