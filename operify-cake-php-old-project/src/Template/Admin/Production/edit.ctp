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
</style>
<script>
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
                    console.log('jsonData');
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
                    <?php
                    echo $this->Form->create(
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
                                    });
                                </script>
                                <div class="col-md-3">
                                    <label for="inputEmail3" class=" control-label "
                                        style="text-align: left !important">Date<strong
                                            style="color:red;">*</strong></label>
                                    <?php echo $this->Form->input('production_date', array('class' => 'form-control', 'id' => 'fdatefrom1', 'readonly', 'placeholder' => 'Date', 'value' => date('d-m-Y', strtotime($productions['production_date'])), 'label' => false, 'readonly')); ?>
                                </div>


                                <div class="col-sm-3">
                                    <label for="inputEmail3" class=" control-label"
                                        style="text-align: left !important">Contract
                                        Name<strong style="color:red;">*</strong></label>

                                    <input type="hidden" name="contract_id" id="contrselectid" required
                                        value="<?php echo $productions['contract_id'] ?>">

                                    <?php
                                    $contractname = $this->comman->findcontractname($productions['contract_id']);

                                    echo $this->Form->input('contractname', array('class' => 'form-control secrhcontract', 'id' => 'contractnameid', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'required', 'readonly', 'placeholder' => 'Enter Contract Name', 'value' => $contractname['title'], )); ?>
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

                                <div class="col-md-3 fpromanual">
                                    <label for="inputEmail3" class="control-label"
                                        style="text-align: left !important">Product<strong
                                            style="color:red;">*</strong></label>
                                    <?php
                                    $itemname = $this->comman->getitemname($productions['item_id']);
                                    echo $this->Form->input('item_id', array('class' => 'form-control ', 'type' => 'hidden', 'label' => false, 'autofocus', 'value' => $productions['item_id'], 'autocomplete' => 'off', 'required'));
                                    
                                    echo $this->Form->input('item_name', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'autofocus', 'value' => $itemname['item_name'], 'autocomplete' => 'off', 'required', 'placeholder' => 'Enter Product', 'readonly')); ?>
                                </div>

                                <div class="col-md-3">
                                    <label for="inputEmail3" class=" control-label "
                                        style="text-align: left !important">Production Order No.<strong
                                            style="color:red;">*</strong></label>
                                    <?php echo $this->Form->input('po_id', array('class' => 'form-control searchpo', 'type' => 'text', 'label' => false, 'autofocus', 'value' => $productions['po_id'], 'autocomplete' => 'off', 'required', 'placeholder' => 'Enter Production Order No.', 'readonly')); ?>
                                </div>

                                <div class="col-md-3 fpromanual">
                                    <label for="inputEmail3" class="control-label"
                                        style="text-align: left !important">Process<strong
                                            style="color:red;">*</strong></label>
                                    <?php
                                    $processname = $this->comman->finishedproductprocess($productions['productprocess_id']);
                                    echo $this->Form->input('productprocess_id', array('class' => 'form-control ', 'type' => 'hidden', 'label' => false, 'autofocus', 'value' => $productions['productprocess_id'], 'autocomplete' => 'off', 'required'));
                                    echo $this->Form->input('process_name', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'autofocus', 'value' => $processname['process_name'], 'autocomplete' => 'off', 'required', 'placeholder' => 'Enter Product', 'readonly')); ?>
                                </div>

                                <div class="col-md-3">
                                    <label for="inputEmail3" class=" control-label"
                                        style="text-align: left !important">Machine Name<strong
                                            style="color:red;">*</strong></label>
                                    <input type="hidden" required="required" name="machines_id" id="retail_ids" 
                                        value="<?php echo $productions['machine_id'] ?>">
                                    <?php echo $this->Form->input('machinename', array('class' => 'form-control secrh-retail', 'id' => 'itemname', 'value' => $machine_name['machine_name'],'readonly', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'required', 'placeholder' => 'Enter Machine Name', '')); ?>
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
                                </div>

                                <div class="col-md-3">
                                    <label for="inputEmail3" class=" control-label"
                                        style="text-align: left !important">Reading(Current day)08.00AM<strong
                                            style="color:red;">*</strong></label>
                                    <?php echo $this->Form->input('reading8am', array('class' => 'form-control', 'type' => 'text', 'value' => $productions['reading8am'], 'required', 'readonly', 'label' => false, 'placeholder' => 'Reading(Current day)08.00AM', 'id' => 'read8am', 'autofocus', 'autocomplete' => 'off', 'onchange' => 'checkmachinereading(this)', 'onkeypress' => 'return isNumberKey(event)')); ?>
                                </div>

                                <div class="col-md-3">
                                    <label for="inputEmail3" class=" control-label"
                                        style="text-align: left !important">Reading(Current day)08.00PM<strong
                                            style="color:red;">*</strong></label>
                                    <?php echo $this->Form->input('reading8pm', array('class' => 'form-control', 'type' => 'text', 'value' => $productions['reading8pm'], 'required', 'label' => false, 'id' => 'read8pm', 'placeholder' => 'Reading(Current day)08.00PM', 'autofocus', 'autocomplete' => 'off', 'onchange' => 'checkmachinereading(this)', 'onkeypress' => 'return isNumberKey(event)')); ?>
                                    <div style="display: none;" id="reading8pm">
                                        <span style="color:red;">Reading at 8.00 PM can't be less than reading at 8.00
                                            AM</span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <label for="inputEmail3" class=" control-label"
                                        style="text-align: left !important">Reading(Next day)08.00AM<strong
                                            style="color:red;">*</strong></label>
                                    <?php echo $this->Form->input('nextday8am', array('class' => 'form-control', 'type' => 'text', 'value' => $productions['nextday8am'], 'required', 'id' => 'next8am', 'label' => false, 'placeholder' => 'Reading(Next day)08.00AM', 'autofocus', 'autocomplete' => 'off', 'onchange' => 'checkmachinereading(this)', 'onkeypress' => 'return isNumberKey(event)')); ?>
                                    <div style="display: none;" id="nextday8am">
                                        <span style="color:red;">Reading Next Day can't be less than reading at 8.00
                                            PM</span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <label for="inputEmail3" class=" control-label" style="text-align: left !important">
                                        Planned Quantity<strong style="color:red;"></strong></label>
                                    <?php echo $this->Form->input('plan_qty', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'placeholder' => 'Planned Quantity', 'autofocus','value' => $productions['plan_qty'], 'autocomplete' => 'off',  'onkeypress' => 'return isNumberKey(event)')); ?>
                                </div>

                                <div class="col-md-3">
                                    <label for="inputEmail3" class=" control-label" style="text-align: left !important">
                                        Production (Shfit A)<strong style="color:red;">*</strong></label>
                                    <?php echo $this->Form->input('production_shift_a', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'placeholder' => 'Production Shfit A', 'value' => $productions['production_shift_a'], 'autofocus', 'required', 'autocomplete' => 'off', 'onkeypress' => 'return isNumberKey(event)')); ?>
                                </div>
                                <div class="col-md-3">
                                    <label for="inputEmail3" class=" control-label" style="text-align: left !important">
                                        Production (Shfit B)<strong style="color:red;">*</strong></label>
                                    <?php echo $this->Form->input('production_shift_b', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'placeholder' => 'Production Shfit B', 'value' => $productions['production_shift_b'], 'autofocus', 'required', 'autocomplete' => 'off', 'onkeypress' => 'return isNumberKey(event)')); ?>
                                </div>

                                <div class="col-md-3">
                                    <label for="inputEmail3" class=" control-label" style="text-align: left !important">
                                        Manpower in Day<strong style="color:red;">*</strong></label>
                                    <?php echo $this->Form->input('manpower_day', array('class' => 'form-control', 'type' => 'text', 'required', 'label' => false, 'value' => $productions['manpower_day'], 'placeholder' => 'Manpower in Day', 'autofocus', 'autocomplete' => 'off', 'onkeypress' => 'return isNumberKey(event)')); ?>
                                </div>
                                <div class="col-md-3">
                                    <label for="inputEmail3" class=" control-label" style="text-align: left !important">
                                        Manpower in Night<strong style="color:red;">*</strong></label>
                                    <?php echo $this->Form->input('manpower_night', array('class' => 'form-control', 'type' => 'text', 'required', 'label' => false, 'value' => $productions['manpower_night'], 'placeholder' => 'Manpower in Night', 'autofocus', 'autocomplete' => 'off', 'onkeypress' => 'return isNumberKey(event)')); ?>
                                </div>
                                <div class="col-md-3">
                                    <label for="inputEmail3" class=" control-label" style="text-align: left !important">
                                        Scrap (Shfit A + B)</label>
                                    <?php echo $this->Form->input('scrap', array('class' => 'form-control', 'type' => 'text', 'label' => false, '', 'value' => $productions['scrap'], 'placeholder' => 'Scrap Shfit A + B', 'autofocus', 'autocomplete' => 'off')); ?>
                                </div>
                                <div class="col-md-12">
                                    <label for="inputEmail3" class=" control-label" style="text-align: left !important">
                                        Remark </label>
                                    <?php echo $this->Form->input('remark', array('class' => 'form-control', 'type' => 'textarea', '', 'label' => false, 'value' => $productions['remark'], 'placeholder' => 'Remark', 'autofocus', 'autocomplete' => 'off')); ?>
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
                        echo $this->Form->submit(
                            'Edit',
                            array('class' => 'btn btn-info pull-right addgen', 'id' => 'formsubmitbtn', 'title' => 'Edit')
                        );
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
        $('#sevice_form').on('submit', function (e) {
            $("#formsubmitbtn").css("display", "none");
        });
    });
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
    function finishedproduct(id) {
        $.ajax({
            type: 'POST',
            url: '<?php echo ADMIN_URL; ?>production/getfinishedproduct',
            data: {
                'po_id': id,
            },
            success: function (data) {
                if (data) {
                    $('.fprojs').show();
                    $('.fpromanual').hide();
                    var select = $("#item_id_pro");
                    select.empty();
                    select.append($('<option>', {
                        value: '',
                        text: '-- Select Product--'
                    }));
                    var dataArray = JSON.parse(data);
                    dataArray.forEach(function (item) {
                        console.log(item);
                        select.append($('<option>', {
                            value: item.id,
                            text: item.item_name,
                        }));
                    });
                }
            },
        });
    }
</script>

<script>
    function cllbckretail2(id) {
        $('.searchpo').val(id);
        $('#porduction_id').val(id);
        finishedproduct(id);
        $('#poidUL').hide();
        $('#poidUL1').hide();
    }
    $(function () {
        $('.searchpo').bind('keyup', function () {
            var pos = $(this).val();
            // alert(pos);
            var check = 2;
            $('#poidUL').show();
            var count = pos.length;
            if (count > 0) {
                $.ajax({
                    type: 'POST',
                    url: '<?php echo ADMIN_URL; ?>production/getpo_id',
                    data: {
                        'fetch': pos,
                        'check': check
                    },
                    success: function (data) {
                        if (data) {
                            // console.log(data);
                            $('#poidUL ul').html(data);
                            $('#poidUL1').hide();
                        } else {
                            $('#poidUL').hide();
                            $('#poidUL1').show();
                        }
                    },
                });
            } else {
                $('#poidUL').hide();
                $('#poidUL1').hide();
            }
        });
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
        // alert(reading8am);
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