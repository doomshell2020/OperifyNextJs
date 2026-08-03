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

    .radio-group label {
        margin-right: 20px;
    }

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
            Edit EMD
            <?php
            // pr($item);die;
            ?>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo SITE_URL; ?>admin/emd"><i class="fa fa-home"></i>Home</a></li>
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
                        <h3 class="box-title"><i class="fa fa-plus-square" aria-hidden="true"></i>Edit EMD</h3>
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
                    );
                    $lastremark = $this->Comman->getemdremark($EmdGuarantees['id']);
                    ?>
                    <div class="box-body">
                        <div class="row">

                            <div class="col-md-3">
                                <label class="control-label">BG For</label>
                                <?= $this->Form->input('bg_for', [
                                    'type' => 'select',
                                    'options' => [
                                        'PG-HDFC' => 'PG - HDFC Bank',
                                        'PG-Axis Bank' => 'PG - Axis Bank',
                                        'EMD-HDFC' => 'EMD - HDFC Bank',
                                        'EMD-Axis Bank' => 'EMD - Axis Bank',
                                        'EMD-Cheque/Online' => 'EMD - Cheque/Online',
                                    ],
                                    'label' => false,
                                    'class' => 'form-control',
                                    'empty' => '-- Select Type --',
                                    'id' => 'bg_for',
                                    'required',
                                    'value' => $EmdGuarantees['bg_for'],
                                ]) ?>
                            </div>

                            <div class="col-md-3" id="type-section">
                                <label class="control-label">Type</label>
                                <div class="radio-group custom-radio-spacing">
                                    <?= $this->Form->radio('po_or_rma', [
                                        ['value' => 'PO', 'text' => 'PO'],
                                        ['value' => 'RM', 'text' => 'Raw Material']
                                    ], [
                                        'legend' => false,
                                        'value' => $EmdGuarantees->po_or_rma
                                    ]); ?>
                                </div>
                            </div>

                            <style>
                                .custom-radio-spacing label {
                                    margin-right: 30px;
                                    padding-left: 5px;
                                }

                                .custom-radio-spacing input[type="radio"] {
                                    margin-right: 5px;
                                }
                            </style>




                            <div class="col-md-3">
                                <label for="inputEmail3" class=" control-label" style="text-align: left !important">Bank Guarantee
                                    No.</label>
                                <?php echo $this->Form->input('bankguaranteeno', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'autofocus',  'value' => $EmdGuarantees['bankguaranteeno'], 'autocomplete' => 'off', 'placeholder' => 'Bank Guarantee No.', 'required')); ?>
                            </div>

                            <div class="col-md-3" id="board-name">
                                <label for="inputEmail3" class=" control-label" style="text-align: left !important">Board Name</label>
                                <?php echo $this->Form->input('board_name', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'autofocus', 'value' => $EmdGuarantees['board_name'], 'placeholder' => 'Board Name', 'autocomplete' => 'off')); ?>
                            </div>

                            <div class="col-md-3">
                                <label for="inputEmail3" class=" control-label" style="text-align: left !important">Favour Name</label>
                                <?php echo $this->Form->input('favour_of', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'autofocus', 'value' => $EmdGuarantees['favour_of'],  'placeholder' => 'Favour Name', 'autocomplete' => 'off')); ?>
                            </div>

                            <div class="col-md-3" id="PO-name">
                                <label for="inputEmail3" class=" control-label" style="text-align: left !important">PO.No./Tender No.</label>
                                <?php echo $this->Form->input('po_no', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'autofocus', 'value' => $EmdGuarantees['po_no'],  'placeholder' => 'PO. No. / Tender No.', 'autocomplete' => 'off')); ?>
                            </div>




                            <div class="col-md-3">
                                <label for="inputEmail3" class="control-label" style="text-align: left !important">Amount</label>
                                <div class="input-group">
                                    <?php
                                    echo $this->Form->input('amount', array(
                                        'class' => 'form-control',
                                        'type' => 'text',
                                        'label' => false,
                                        'autofocus',
                                        'placeholder' => 'Amount',
                                        'autocomplete' => 'off',
                                        'style' => 'width: 279px;',
                                        'value' => $EmdGuarantees['amount'],
                                        'required'
                                    ));
                                    ?>
                                    <div class="input-group-append">
                                        <?php
                                        echo $this->Form->select('currency_type', [
                                            'INR' => 'INR',
                                            'USD' => 'USD',
                                            'EUR' => 'EUR',
                                            'GBP' => 'GBP'
                                        ], [
                                            'class' => 'form-control',
                                            'label' => false,
                                            'empty' => false,
                                            'value' => $EmdGuarantees['currency_type'],
                                            'required' => true
                                        ]);
                                        ?>
                                    </div>
                                </div>
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
                                <?= $this->Form->input('datefrom', ['class' => 'form-control', 'placeholder' => 'Date', 'id' => 'datefrom', 'value' => date('d-m-Y', strtotime($EmdGuarantees['datefrom'])), 'required',  'readonly', 'label' => false]); ?>
                            </div>

                            <div class="col-md-3">
                                <label class="control-label">Valid Upto</label>
                                <?= $this->Form->input('validupto', ['class' => 'form-control', 'placeholder' => 'Valid Upto', 'id' => 'validupto', 'required', 'readonly', 'value' => !empty($EmdGuarantees['validupto']) ? date('d-m-Y', strtotime($EmdGuarantees['validupto'])) : '', 'label' => false]); ?>
                            </div>

                            <div class="col-md-3">
                                <label class="control-label">Claim Date</label>
                                <?= $this->Form->input('claim_upto', ['class' => 'form-control', 'placeholder' => 'Valid Upto', 'id' => 'claim_upto', 'required', 'readonly', 'value' => !empty($EmdGuarantees['claim_upto']) ? date('d-m-Y', strtotime($EmdGuarantees['claim_upto'])) : '', 'label' => false]); ?>
                            </div>

                            <div class="col-md-3">
                                <label class="control-label">Extension Upto</label>
                                <?= $this->Form->input('extenstionupto', ['class' => 'form-control', 'placeholder' => 'Extension Upto', 'id' => 'extenstionupto', 'readonly', 'value' => !empty($EmdGuarantees['extenstionupto']) ? date('d-m-Y', strtotime($EmdGuarantees['extenstionupto'])) : '', 'label' => false]); ?>
                            </div>

                            <div class="col-md-3" id="lastdate_section">
                                <label class="control-label">Last Date of Supply</label>
                                <?= $this->Form->input('lastdate', ['class' => 'form-control', 'placeholder' => 'Last Date of Supply', 'id' => 'lastdate', 'readonly', 'value' => !empty($EmdGuarantees['lastdate']) ? date('d-m-Y', strtotime($EmdGuarantees['lastdate'])) : '', 'label' => false]); ?>
                            </div>

                            <div class="col-md-3">
                                <label for="inputEmail3" class=" control-label" style="text-align: left !important">BG Release On/After</label>
                                <?= $this->Form->input('relese_date', ['class' => 'form-control', 'id' => 'relese_date', 'placeholder' => 'BG Release On/After', 'readonly', 'value' => !empty($EmdGuarantees['relese_date']) ? date('d-m-Y', strtotime($EmdGuarantees['relese_date'])) : '', 'label' => false]); ?>
                            </div>





                            <div class="col-md-3">
                                <label for="inputEmail3" class=" control-label" style="text-align: left !important">Contect Person</label>
                                <?php echo $this->Form->input('contect_per', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'autofocus', 'placeholder' => 'Contect Person Name', 'value' => $EmdGuarantees['contect_per'],  'autocomplete' => 'off')); ?>
                            </div>

                            <div class="col-md-3">
                                <label for="invoice_file" class="control-label" style="text-align: left !important">
                                    File
                                </label>
                                <?= $this->Form->input('invoice_file', [
                                    'type' => 'file',
                                    'label' => false,
                                    'accept' => '.pdf,.jpg,.jpeg,.png',
                                    'class' => 'form-control',
                                    'id' => 'invoice_file',
                                    'value' => $EmdGuarantees['invoice_file'],
                                    'style' => 'width: 100%;'
                                ]) ?>

                                <?php if (!empty($EmdGuarantees['invoice_file'])) {
                                    $db = $this->request->session()->read('Auth.User.db');
                                    $filePath = '/images/' . $db . '_image/emd/' . h($EmdGuarantees['invoice_file']);
                                    $fullUrl = $this->Url->build($filePath, ['fullBase' => true]);
                                ?>
                                    <div style="margin-top: 5px;">
                                        <a href="javascript:void(0);" onclick="openFilePopup('<?= $fullUrl ?>')" class="btn btn-sm btn-primary">
                                            View File
                                        </a>
                                    </div>
                                <?php } else { ?>
                                    <div style="margin-top: 5px;">N/A</div>
                                <?php } ?>
                            </div>

                        </div>
                        <script>
                            $(function() {
                                const dateFields = ['#datefrom', '#validupto', '#claim_upto', '#extenstionupto', '#lastdate', '#relese_date'];
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


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const bgForDropdown = document.getElementById('bg_for');
        const typeSection = document.getElementById('type-section');

        function toggleTypeSection() {
            const value = bgForDropdown.value;
            if (value === 'EMD-HDFC' || value === 'EMD-Axis Bank' || value === 'EMD-Cheque/Online') {
                typeSection.style.display = 'none';
            } else {
                typeSection.style.display = 'block';
            }
        }

        toggleTypeSection();

        bgForDropdown.addEventListener('change', toggleTypeSection);
    });
</script>

<script>
    $(document).ready(function() {
        var selectedType = $("input[name='po_or_rma']:checked").val();
        toggleLastDate(selectedType);

        $("input[name='po_or_rma']").on("change", function() {
            toggleLastDate(this.value);
        });

        function toggleLastDate(type) {
            if (type === "RM") {
                $("#lastdate_section").hide();
                $("#lastdate").prop("required", false);
            } else {
                $("#lastdate_section").show();
                $("#lastdate").prop("required", true);
            }
        }
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const bgForDropdown = document.getElementById('bg_for');
        const typeSection = document.getElementById('board-name');
        const poName = document.getElementById('PO-name');

        function toggleTypeSection() {
            const value = bgForDropdown.value;
            if (value === 'EMD-Cheque/Online') {
                typeSection.style.display = 'block';
                poName.style.display = 'none';
            } else {
                typeSection.style.display = 'none';
                poName.style.display = 'block';
            }
        }

        toggleTypeSection();

        bgForDropdown.addEventListener('change', toggleTypeSection);
    });
</script>



<script>
    $(document).ready(function() {
        $("#item_id_pro").on('change', function() {
            var itemid = $(this).val();
            var contractid = $('#contrselectid').val();
            $.ajax({
                type: 'POST',
                url: '<?php echo ADMIN_URL; ?>Designsheet/checkdesignsheetitem',
                data: {
                    'itemid': itemid,
                    'contractid': contractid
                },

                dataType: 'json',
                success: function(data) {
                    $('#kmquantity').val(data.itemqty);

                    if (data.checkdesign) {
                        $('#msg').css('display', 'block');
                        $("#item_id_pro ").val('');
                        $("#kmquantity ").val('');
                    } else {
                        $('#msg').css('display', 'none');
                    }
                },
            });
        });
    });
</script>

<script>
    function openFilePopup(fileUrl) {
        const isPdf = fileUrl.toLowerCase().endsWith('.pdf');
        const popup = window.open('', '_blank', 'width=800,height=600');

        if (isPdf) {
            popup.document.write(`<iframe src="${fileUrl}" width="100%" height="100%" style="border:none;"></iframe>`);
        } else {
            popup.document.write(`<img src="${fileUrl}" style="max-width:100%;max-height:100%;" />`);
        }

        popup.document.title = "View File";
    }
</script>


<script>
$(document).ready(function () {
  $(".form-horizontal").on("submit", function () {
    const submitBtn = $(this).find(":submit");
    submitBtn.prop("disabled", true).text("Submitting...");
  });
});
</script>
