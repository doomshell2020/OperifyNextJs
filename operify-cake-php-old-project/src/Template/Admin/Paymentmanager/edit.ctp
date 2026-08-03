<style>
    .input_fields_wrap .form-control {
        margin-bottom: 15px;
    }

    .control-label {
        display: block;
        margin-top: 10px;
    }

    label[for="consumble-y"] {
        width: 47%;
        padding: 4px 8px;
        border: 1px solid #ccc;
        margin-right: 6%;
        border-radius: 3px;
    }

    /* .radio-group label {
        margin-right: 20px;
    } */

    label[for="consumble-n"] {
        width: 47%;
        padding: 4px 8px;
        border: 1px solid #ccc;
        border-radius: 3px;
    }

    #itemtestUL {
        position: relative;
    }

    #itemtestUL ul {
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

    #itemtestUL ul li {
        padding: 5px 8px;
        border: 1px solid lightgray;
    }

    #itemtestUL ul li a {
        color: black;
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
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Edit Payment
            <?php
            // pr($item);die;
            ?>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo SITE_URL; ?>admin/paymentmanager"><i class="fa fa-home"></i>Home</a></li>
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
                        <h3 class="box-title"><i class="fa fa-plus-square" aria-hidden="true"></i>Edit Payment</h3>
                    </div>
                    <!-- /.box-header -->
                    <!-- form start -->


                    <?php echo $this->Form->create(
                        $users,
                        array(
                            'class' => 'form-horizontal',
                            'enctype' => 'multipart/form-data',
                            'onsubmit' => "return validateForm()",
                            'name' => 'myForm',
                            'id' => 'sevice_form',
                            'validate'
                        )
                    ); ?>
                    <div class="box-body">
                        <div class="row">





                            <div class="col-md-3">
                                <label for="inputEmail3" class=" control-label" style="text-align: left !important">Particular Name</label>
                                <?php echo $this->Form->input('particular', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'autofocus', 'value' => $EmdGuarantees['particular'], 'autocomplete' => 'off', 'placeholder' => 'Particular Name', 'required')); ?>
                            </div>
                            <div class="col-md-3" id="board-name">
                                <label for="inputEmail3" class=" control-label" style="text-align: left !important">Consignee</label>
                                <?php echo $this->Form->input('consignee', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'autofocus', 'value' => $EmdGuarantees['consignee'], 'placeholder' => 'Consignee', 'autocomplete' => 'off')); ?>
                            </div>

                            <div class="col-md-3">
                                <label for="inputEmail3" class=" control-label" style="text-align: left !important">PO No.</label>
                                <?php echo $this->Form->input('po_no', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'autofocus', 'value' => $EmdGuarantees['po_no'], 'placeholder' => 'PO No.', 'autocomplete' => 'off')); ?>
                            </div>

                            <div class="col-md-3" id="PO-name">
                                <label for="inputEmail3" class=" control-label" style="text-align: left !important">Invoice No.</label>
                                <?php echo $this->Form->input('invoice', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'autofocus', 'value' => $EmdGuarantees['invoice'], 'placeholder' => 'Invoice No.', 'autocomplete' => 'off')); ?>
                            </div>

                            <div class="col-md-3">
                                <label class="control-label">Due Period</label>
                                <?php
                                echo $this->Form->control('due_period', [
                                    'type' => 'select',
                                    'label' => false,
                                    'options' => [
                                        '45' => '45 Days',
                                        '60' => '60 Days',
                                        '90' => '90 Days',
                                    ],
                                    'empty' => 'Select Due Period',
                                    'class' => 'form-control',
                                    'value' => $EmdGuarantees['due_period'],
                                ]);
                                ?>
                            </div>

                            <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                var amountInput = document.querySelector('[name="amount"]');

                                amountInput.addEventListener('input', function() {
                                    amountInput.value = amountInput.value.replace(/[^0-9]/g, '');
                                    if (parseFloat(amountInput.value) < 1) {
                                        amountInput.value = '';
                                    }
                                });
                            });
                        </script>




                            <div class="col-md-3">
                                <label class="control-label">Date</label>
                                <?= $this->Form->input('datefrom', ['class' => 'form-control', 'placeholder' => 'Date', 'id' => 'datefrom', 'value' => !empty($EmdGuarantees['datefrom']) ? date('d-m-Y', strtotime($EmdGuarantees['datefrom'])) : '', 'required', 'label' => false]); ?>
                            </div>

                            <div class="col-md-3">
                                <label class="control-label">Bill Dispatch Date</label>
                                <?= $this->Form->input('bill_dis_date', ['class' => 'form-control', 'readonly', 'placeholder' => 'Bill Dispatch Date', 'id' => 'validupto',  'value' => !empty($EmdGuarantees['bill_dis_date']) ? date('d-m-Y', strtotime($EmdGuarantees['bill_dis_date'])) : '', 'label' => false]); ?>
                            </div>


                            <div class="col-md-3">
                                <label for="inputEmail3" class=" control-label" style="text-align: left !important">Bill Amount</label>
                                <?php echo $this->Form->input('amount', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'autofocus', 'value' => $EmdGuarantees['amount'],  'placeholder' => 'Bill Amount', 'autocomplete' => 'off', 'required')); ?>
                            </div>

                        </div>
                        <script>
                            $(function() {
                                const dateFields = ['#datefrom', '#validupto', '#extenstionupto', '#lastdate', '#relese_date'];
                                dateFields.forEach(id => {
                                    $(id).datepicker({
                                        dateFormat: 'dd-mm-yy',
                                        changeMonth: true,
                                        changeYear: true,
                                        yearRange: '2018:2030',
                                        autoclose: true
                                    });
                                });
                            });
                        </script>

                        <div class="col-md-12 text-right mt-2">
                            <?php
                            echo $this->Form->submit(
                                'Edit',
                                array('class' => 'btn btn-info', 'id' => 'formsubmitbtn', 'title' => 'Edit')
                            );
                            ?>
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