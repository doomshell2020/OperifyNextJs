<style>
    .input_fields_wrap .form-control {
        margin-bottom: 15px;
    }

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

    .testUL {
        position: relative;
    }

    .testUL ul {
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

    .testUL ul li {
        padding: 5px 8px;
        border: 1px solid lightgray;
    }

    .testUL ul li a {
        color: black;
    }

    .preview {
        margin-right: 15px;
    }

    .dataTables_wrapper.form-inline.dt-bootstrap.no-footer {
        margin-top: 0px;
    }

    .contractUL {
        position: relative;
    }

    .contractUL ul {
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

    .contractUL ul li {
        padding: 5px 8px;
        border: 1px solid lightgray;
        margin-left: 0px !important;
    }

    .contractUL ul li a {
        color: black;
    }
</style>


<script>
    function machinereading(id) {
        $.ajax({
            type: 'POST',
            url: '<?php echo ADMIN_URL; ?>production/getmachinereading',
            data: {
                'fetch': id,
            },
            success: function(data) {
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


    // ------------------- Production (Km.) required validtion------------//

    $(document).ready(function() {
        $("#planneddatafill").on('change', function(e) {
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
            Daily Sheet <?= date('d-m-Y'); ?>
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
                                <div class="col">
                                    <label for="inputEmail3" class="control-label" style="text-align: left !important">Machine Name</label>
                                </div>
                                <div class="col">
                                    <label for="inputEmail3" class="control-label" style="text-align: left !important">Contract Name</label>
                                </div>
                                <div class="col">
                                    <label for="inputEmail3" class="control-label" style="text-align: left !important">Product Name</label>
                                </div>
                                <div class="col">
                                    <label for="inputEmail3" class="control-label" style="text-align: left !important">Production Order No.</label>
                                </div>
                                <div class="col">
                                    <label for="inputEmail3" class="control-label" style="text-align: left !important">Process Name</label>
                                </div>
                                <div class="col">
                                    <label for="inputEmail3" class="control-label" style="text-align: left !important">Planned Quantity</label>
                                </div>
                            </div>

                            <?php
                            foreach ($machineNames as $machines) {
                            ?>
                                <div class="row mt-1">
                                    <div class="col">
                                        <input type="hidden" name="machines_id[]" id="retail_ids" value="<?= $machines['id'] ?>">
                                        <?php echo $this->Form->input('machine_id', array('class' => 'form-control', 'id' => 'itemname', 'type' => 'text', 'label' => false, 'readonly', 'autofocus', 'autocomplete' => 'off', 'placeholder' => 'Enter Machine Name', 'value' => $machines['machine_name'])); ?>
                                    </div>


                                    <div class="col">
                                        <input type="hidden" name="contract_id[]" id="contrselectid_<?= $machines['id']; ?>">
                                        <?php echo $this->Form->input('contractname', array('class' => 'form-control secrhcontract', 'id' => 'contractnameid_' . $machines['id'], 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'placeholder' => 'Enter Contract Name')); ?>
                                        <div id="contractUL_<?= $machines['id']; ?>" class="contractUL" style="display:none;">
                                            <ul></ul>
                                        </div>
                                        <div id="contractUL1_<?= $machines['id']; ?>" class="contractUL1" style="display:none;">
                                            <ul>
                                                <li
                                                    style="padding: 5px 8px;list-style:none;color: black;font-weight: bold;margin-left:-32px; border: 1px solid lightgray;">
                                                    No Record Found</li>
                                            </ul>
                                        </div>
                                    </div>


                                    <div class="col">
                                        <?php echo $this->Form->input('item_id[]', [
                                            'class' => 'form-control item_id_pro',
                                            'type' => 'select',
                                            'label' => false,
                                            'empty' => '-- Select Product--',
                                            'autofocus',
                                            'autocomplete' => 'off',
                                            'id' => 'item_id_pro_' . $machines['id']
                                        ]); ?>
                                    </div>

                                    <div class="col">
                                        <?php echo $this->Form->input('po_id[]', [
                                            'class' => 'form-control data_req',
                                            'type' => 'select',
                                            'label' => false,
                                            'empty' => '-- Select Production Order No.--',
                                            'autofocus',
                                            'autocomplete' => 'off',
                                            'id' => 'po_pro_' . $machines['id']
                                        ]); ?>
                                    </div>

                                    <div class="col">
                                        <?php echo $this->Form->input('productprocess_id[]', array('class' => 'form-control', 'id' => 'itemname', 'type' => 'select', 'empty' => '-- Select Process --', 'options' => $processoptions, 'label' => false, 'autofocus', 'autocomplete' => 'off', 'placeholder' => 'Enter Machine Name')); ?>
                                    </div>


                                    <div class="col">
                                        <?php echo $this->Form->input('plan_qty[]', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'placeholder' => 'Planned Quantity', 'autofocus', 'autocomplete' => 'off', 'onkeypress' => 'return isNumberKey(event)')); ?>
                                    </div>

                                </div>
                            <?php } ?>
                            <br>
                            <div>
                                <button type="button" class="btn btn-success mt-2" id="addRow">Add Row</button>
                            </div>
                            <div id="machineRows"></div>
                        </div>
                    </div>


                    <script>
                        $(document).ready(function() {
                            $('#sevice_form').submit(function(event) {
                                $('.addgen').hide();
                            });
                        });
                    </script>

                    <!-- /.box-body -->
                    <div class="box-footer">
                        <?php echo $this->Form->submit('Add', array('class' => 'btn btn-info pull-right addgen', 'id' => 'formsubmitbtn', 'title' => 'Add')); ?>
                        <?php echo $this->Html->link('Back', ['action' => 'index'], ['class' => 'btn btn-default']); ?>

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

<!-------- Data Date picker !---->
<script>
    $(document).ready(function() {
        let rowCounter = 1000; // Start ID from 1000

        $("#addRow").click(function() {
            rowCounter++; // Increment the counter for a unique ID

            let newRow = `<div class="row mt-1 single-row">
            <div class="col">
                <input type="hidden" name="machines_id[]" id="machineId_${rowCounter}">
   
                <input type="text" name="machinename" class="form-control secrh-retail" id="machineName_${rowCounter}" autocomplete="off" placeholder="Machine Name">
                <div class ="testUL" id="testUL_${rowCounter}" style="display:none;">
                    <ul></ul>
                </div>
                <div class ="testUL1" id="testUL1_${rowCounter}" style="display:none;">
                    <ul>
                        <li style="padding: 5px 8px; list-style:none; color: black; font-weight: bold; margin-left:-32px; border: 1px solid lightgray;">
                            No Record Found
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col">
                <input type="hidden" name="contract_id[]" id="contrselectid_${rowCounter}">
                <input type="text" name="contractname" class="form-control secrhcontract" id="contractnameid_${rowCounter}" autocomplete="off" placeholder="Enter Contract Name">
                <div id="contractUL_${rowCounter}" class="contractUL" style="display:none;">
                    <ul></ul>
                </div>
                <div id="contractUL1_${rowCounter}" class="contractUL1" style="display:none;">
                    <ul>
                        <li style="padding: 5px 8px; list-style:none; color: black; font-weight: bold; margin-left:-32px; border: 1px solid lightgray;">
                            No Record Found
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col">
                <select name="item_id[]" class="form-control item_id_pro" id="item_id_pro_${rowCounter}">
                    <option value="">-- Select Product--</option>
                </select>
            </div>

            <div class="col">
                <select name="po_id[]" class="form-control data_req" id="po_pro_${rowCounter}">
                    <option value="">-- Select Production Order No.--</option>
                </select>
            </div>

            <div class="col">
                <select name="productprocess_id[]" class="form-control">
                    <option value="">-- Select Process --</option>
                    <?php foreach ($processoptions as $key => $value) { ?>
                        <option value="<?= $key ?>"><?= $value ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="col">
                <input type="text" name="plan_qty[]" class="form-control" placeholder="Planned Quantity" onkeypress="return isNumberKey(event)">
            </div>

            <div class="col">
                <button type="button" class="btn btn-danger removeRow">Delete</button>
            </div>
        </div>`;

            $("#machineRows").append(newRow);
        });

        $(document).on("click", ".removeRow", function() {
            $(this).closest(".single-row").remove();
        });
    });
</script>





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
    $(document).ready(function() {
        $('#fdatefrom').datepicker({
            dateFormat: 'dd-mm-yy',
            yearRange: '2018:2030',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            onSelect: function(date) {

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






<script type="text/javascript">
    $(document).on('change', '.item_id_pro', function() {
        var id = $(this).attr('id');
        var idParts = id.split('_');

        var item_id = $(this).val();
        var contractid = $(`#contrselectid_${idParts[3]}`).val();

        var select = $("#po_pro_" + idParts[3]);
        select.empty();
        select.append($('<option>', {
            value: '',
            text: '--- Select Production Order No.---'
        }));

        if (item_id !== "") {
            $.ajax({
                type: 'POST',
                url: '<?php echo ADMIN_URL; ?>production/getproductionorder',
                data: {
                    'contractid': contractid,
                    'item_id': item_id
                },
                success: function(data) {
                    var select = $("#po_pro_" + idParts[3]);
                    select.empty();
                    select.append($('<option>', {
                        value: '',
                        text: '--- Select Production Order No.---'
                    }));

                    try {
                        var dataArray = JSON.parse(data);
                        dataArray.forEach(function(item) {
                            select.append($('<option>', {
                                value: item.po_id,
                                text: item.po_id
                            }));
                        });
                    } catch (e) {
                        console.error("Invalid JSON response:", data);
                    }
                }
            });
        }
    });



    function getcontractfinished(contractid, inputId) {
        if (contractid != "") {
            $.ajax({
                type: 'POST',
                url: '<?php echo ADMIN_URL; ?>production/getcontractfinished',
                data: {
                    'contract_id': contractid,
                },

                success: function(data) {
                    if (data) {
                        var select = $("#item_id_pro_" + inputId);
                        select.empty();
                        select.append($('<option>', {
                            value: '',
                            text: '-- Select Product--'
                        }));
                        var dataArray = JSON.parse(data);
                        dataArray.forEach(function(item) {
                            select.append($('<option>', {
                                value: item.id,
                                text: item.item_name,
                            }));
                        });
                    } else {
                        var select = $("#item_id_pro_" + inputId);
                        select.empty();
                    }
                },

            });

        };

    };


    function cllbckretail2(id, cid, inputId) {
        $(`#contractnameid_${inputId}`).val(id);
        $(`#contrselectid_${inputId}`).val(cid);

        getcontractfinished(cid, inputId);
        $(`#contractUL_${inputId}`).hide();
        $(`#contractUL1_${inputId}`).hide();
    }

    $(function() {
        $(document).on('keyup', '.secrhcontract', function() {
            var id = $(this).attr('id');
            var idParts = id.split('_');

            var select = $("#item_id_pro_" + idParts[1]);
            select.empty();
            select.append($('<option>', {
                value: '',
                text: '-- Select Product--'
            }));

            var select = $("#po_pro_" + idParts[1]);
            select.empty();
            select.append($('<option>', {
                value: '',
                text: '--- Select Production Order No.---'
            }));


            var pos = $(this).val();
            var check = 2;
            $(`#contractUL_${idParts[1]}`).show();
            $(`#contrselectid_${idParts[1]}`).val('');
            var count = pos.length;
            if (count > 0) {
                $.ajax({
                    type: 'POST',
                    url: '<?php echo ADMIN_URL; ?>production/getcontractfordailysheet',
                    data: {
                        'fetch': pos,
                        'check': check,
                        'inputId': idParts[1]
                    },
                    success: function(data) {
                        if (data) {
                            $(`#contractUL_${idParts[1]} ul`).html(data);
                            $(`#contractUL1_${idParts[1]}`).hide();
                        } else {
                            $(`#contractUL_${idParts[1]}`).hide();
                            $(`#contractUL1_${idParts[1]}`).show();
                        }
                    }
                });
            } else {
                $(`#contractUL_${idParts[1]}`).hide();
                $(`#contractUL1_${idParts[1]}`).hide();
            }
        });
    });



    function cllbckretail0(id, cid, sid, inputId) {
        $(`#machineName_${inputId}`).val(id);
        // machinereading(cid);
        $(`#machineId_${inputId}`).val(cid);
        $(`#testUL_${inputId}`).hide();
    }
    $(function() {
        $(document).on('keyup', '.secrh-retail', function() {
            var id = $(this).attr('id');
            var idParts = id.split('_');

            var pos = $(this).val();
            var check = 0;
            $(`#testUL_${idParts[1]}`).show();
            $(`#machineId_${idParts[1]}`).val('');
            var count = pos.length;
            if (count > 0) {
                $.ajax({
                    type: 'POST',
                    url: '<?php echo ADMIN_URL; ?>production/getname',
                    data: {
                        'fetch': pos,
                        'check': check,
                        'inputId': idParts[1]
                    },
                    success: function(data) {
                        if (data) {
                            $(`#testUL_${idParts[1]} ul`).html(data);
                            $(`#testUL1_${idParts[1]}`).hide();
                        } else {
                            $(`#testUL_${idParts[1]}`).hide();
                            $(`#testUL1`).show();
                        }
                    },
                });
            } else {
                $(`#testUL_${idParts[1]}`).hide();
                $(`#testUL1_${idParts[1]}`).hide();
            }
        });
    });
</script>