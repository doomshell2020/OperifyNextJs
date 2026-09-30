<style>
    .preview { margin-right: 15px; }
    #load2 {
        width: 100%; height: 100%;
        position: fixed; z-index: 9999;
        background-color: white !important;
        background: url("<?php echo SITE_URL; ?>images/Preloader_2.gif") no-repeat center center rgba(0,0,0,0.75);
    }

    /* Floating item-search dropdown — appended to body, escapes all overflow clipping */
    #jc-item-dropdown {
        display: none;
        position: fixed;
        z-index: 999999;
        min-width: 280px;
        max-height: 240px;
        overflow-y: auto;
        background: #fff;
        border: 1px solid #aaa;
        border-radius: 4px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        list-style: none;
        padding: 0;
        margin: 0;
    }
    #jc-item-dropdown li {
        padding: 9px 14px;
        cursor: pointer;
        font-size: 13px;
        border-bottom: 1px solid #f0f0f0;
        color: #333;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    #jc-item-dropdown li:hover { background: #e8f4fd; color: #1a6fa8; }
    #jc-item-dropdown li.no-result { color: #999; cursor: default; font-style: italic; }

    .btn-remove-row { padding: 4px 8px; font-size: 13px; }
</style>

<div class="content-wrapper">

    <section class="content-header">
        <h1>Job Challan</h1>
    </section>

    <section class="content">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">Add Job Challan</h3>
            </div>

            <div class="box-body">

                <?= $this->Form->create($entity) ?>

                <!-- TOP FIELDS -->
                <div class="row">

                    <div class="form-group col-sm-3">
                        <label>JC No. <strong style="color:red;">*</strong></label>
                        <?= $this->Form->input('challan_no', [
                            'type'        => 'text',
                            'label'       => false,
                            'class'       => 'form-control',
                            'required'    => true,
                            'placeholder' => 'Enter JC No.',
                            'onkeyup'     => "this.value = this.value.replace(/[^0-9]/g, '')",
                            'onchange'    => "this.value = this.value.replace(/[^0-9]/g, '')"
                        ]) ?>
                    </div>

                    <div class="form-group col-sm-3">
                        <label>Date</label>
                        <?php echo $this->Form->input('jc_dates', ['class' => 'form-control', 'id' => 'fdatefrom', 'readonly', 'placeholder' => 'Select Date', 'label' => false]); ?>
                    </div>

                    <div class="form-group col-sm-3">
                        <label>Sub Contractor</label>
                        <?= $this->Form->input('sub_contractors_id', [
                            'options' => $subContractors,
                            'empty'   => 'Select Sub Contractor',
                            'label'   => false,
                            'class'   => 'form-control',
                            'id'      => 'vendor_id'
                        ]) ?>
                    </div>

                    <div class="form-group col-sm-3">
                        <label>GST No.</label>
                        <?= $this->Form->input('gst_no', [
                            'type'  => 'text',
                            'label' => false,
                            'class' => 'form-control',
                            'id'    => 'gst_no',
                            'readonly'
                        ]) ?>
                    </div>

                    <div class="form-group col-sm-3">
                        <label>Estimated Value</label>
                        <?= $this->Form->input('estimated_values', [
                            'type'  => 'number',
                            'label' => false,
                            'class' => 'form-control'
                        ]) ?>
                    </div>

                    <div class="form-group col-sm-3">
                        <label>Expected Days</label>
                        <?= $this->Form->input('expected_days', [
                            'type'  => 'number',
                            'label' => false,
                            'class' => 'form-control'
                        ]) ?>
                    </div>

                </div>

                <!-- WORK DESCRIPTION -->
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label>Work Description</label>
                        <?= $this->Form->input('work_description', [
                            'type'  => 'textarea',
                            'label' => false,
                            'class' => 'form-control'
                        ]) ?>
                    </div>
                </div>

                <!-- PROCESS TYPE -->
                <div class="row">
                    <div class="form-group col-sm-12">
                        <label><strong>Process Type</strong> &nbsp;&nbsp;
                            <label style="font-weight:normal; margin-right:16px;">
                                <input type="radio" name="processing_type" value="Manufacturing" id="pt_manufacturing" checked> &nbsp;Manufacturing
                            </label>
                            <label style="font-weight:normal;">
                                <input type="radio" name="processing_type" value="In Progress" id="pt_inprogress"> &nbsp;In Progress
                            </label>
                        </label>
                    </div>
                </div>

                <!-- ITEM TABLE (shown for Manufacturing only) -->
                <div id="manufacturing_section">
                    <hr>

                    <!-- Shared floating dropdown — appended outside any overflow:auto container -->
                    <ul id="jc-item-dropdown"></ul>

                    <div style="overflow-x: auto;">
                        <table class="table table-bordered" id="items_table" style="min-width:900px;">
                            <thead style="background:#3c8dbc; color:#fff;">
                                <tr>
                                    <th>Item Name</th>
                                    <th>In Hand Qty</th>
                                    <th>Quantity</th>
                                    <th>HSN/SAC</th>
                                    <th>Rate</th>
                                    <th>Tax %</th>
                                    <th>Tax Amount</th>
                                    <th>Total Amount</th>
                                    <th style="width:50px;"></th>
                                </tr>
                            </thead>
                            <tbody id="items_tbody">
                                <!-- Row 0 — template row (always present) -->
                                <tr class="item-row">
                                    <td>
                                        <div class="item-search-wrap">
                                            <input type="text" name="job_challan_items[0][item_name]" class="form-control secrh-retail" autocomplete="off" placeholder="Search item...">
                                            <ul class="item-dropdown"></ul>
                                        </div>
                                        <input type="hidden" name="job_challan_items[0][item_id]" class="retail_ids">
                                    </td>

                                    <td><input type="text" name="job_challan_items[0][in_hand_qty]" class="form-control inhand_qty" readonly></td>
                                    <td><input type="text" name="job_challan_items[0][quantity]" class="form-control qty"></td>
                                    <td><input type="text" name="job_challan_items[0][hsn_code]" class="form-control"></td>
                                    <td><input type="text" name="job_challan_items[0][rate]" class="form-control rate"></td>
                                    <td>
                                        <?= $this->Form->input('job_challan_items.0.tax_rate', [
                                            'options' => $taxMaster,
                                            'empty'   => '-- Tax --',
                                            'label'   => false,
                                            'class'   => 'form-control tax'
                                        ]) ?>
                                    </td>
                                    <td><input type="text" name="job_challan_items[0][tax_amount]" class="form-control tax_amount" readonly></td>
                                    <td><input type="text" name="job_challan_items[0][total_amount]" class="form-control amount" readonly></td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-remove-row btn-xs" style="display:none;" title="Remove Row">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- ADD ROW BUTTON -->
                    <div class="row">
                        <div class="col-sm-12">
                            <button type="button" id="btn_add_row" class="btn btn-info btn-sm">
                                <i class="fa fa-plus"></i> Add Item
                            </button>
                        </div>
                    </div>
                    <br>
                </div><!-- /#manufacturing_section -->

                <!-- IN PROGRESS - SEMI-FINISHED PRODUCT SECTION -->
                <div id="inprogress_section" style="display:none;">
                    <hr>
                    <h4 style="color: #d81b60; font-weight: bold; margin-bottom: 15px;">Semi-Finished Product</h4>
                    <div class="row">
                        <div class="col-sm-6 form-group">
                            <label>Product Name <strong style="color:red;">*</strong></label>
                            <div class="item-search-wrap">
                                <input type="text" name="semi_finished_item_name" class="form-control secrh-retail" autocomplete="off" placeholder="Search semi-finished product...">
                                <ul class="item-dropdown"></ul>
                            </div>
                            <input type="hidden" name="semi_finished_item_id" class="retail_ids">
                        </div>
                        <div class="col-sm-3 form-group">
                            <label>Quantity <strong style="color:red;">*</strong></label>
                            <input type="text" name="semi_finished_quantity" class="form-control qty">
                        </div>
                        <div class="col-sm-3 form-group">
                            <label>In Hand Qty</label>
                            <input type="text" name="semi_finished_in_hand_qty" class="form-control inhand_qty" readonly>
                        </div>
                    </div>
                </div>

                <!-- BUTTONS -->
                <div class="row">
                    <div class="form-group col-sm-12">
                        <button type="submit" class="btn btn-success">Save</button>
                        <button type="reset" class="btn btn-primary">Reset</button>
                    </div>
                </div>

                <?= $this->Form->end() ?>

            </div>
        </div>
    </section>
</div>

<script>
/* =========================================================
   PROCESS TYPE TOGGLE
   ========================================================= */
$(document).ready(function () {
    $('input[name="processing_type"]').on('change', function () {
        var val = $(this).val();
        if (val === 'Manufacturing') {
            $('#inprogress_section').hide();
        } else {
            $('#inprogress_section').show();
        }
        // Raw material section is always visible now.
    });

    // Init datepicker
    $('#fdatefrom').datepicker({
        dateFormat: 'dd-mm-yy',
        yearRange: '2018:2030',
        changeMonth: true,
        changeYear: true,
    });
});


/* =========================================================
   ADD ROW
   ========================================================= */
$('#btn_add_row').on('click', function () {
    var rowCount = $('#items_tbody .item-row').length;
    var newIndex = rowCount;

    // Build Tax options HTML
    var taxOptions = '<option value="">-- Tax --</option>';
    <?php foreach ($taxMaster as $tv => $tl): ?>
    taxOptions += '<option value="<?= $tv ?>"><?= $tl ?></option>';
    <?php endforeach; ?>

    var newRow = '<tr class="item-row">' +
        '<td>' +
            '<div class="item-search-wrap">' +
                '<input type="text" name="job_challan_items[' + newIndex + '][item_name]" class="form-control secrh-retail" autocomplete="off" placeholder="Search item...">' +
                '<ul class="item-dropdown"></ul>' +
            '</div>' +
            '<input type="hidden" name="job_challan_items[' + newIndex + '][item_id]" class="retail_ids">' +
        '</td>' +
        '<td><input type="text" name="job_challan_items[' + newIndex + '][in_hand_qty]" class="form-control inhand_qty" readonly></td>' +
        '<td><input type="text" name="job_challan_items[' + newIndex + '][quantity]" class="form-control qty"></td>' +
        '<td><input type="text" name="job_challan_items[' + newIndex + '][hsn_code]" class="form-control"></td>' +
        '<td><input type="text" name="job_challan_items[' + newIndex + '][rate]" class="form-control rate"></td>' +
        '<td><select name="job_challan_items[' + newIndex + '][tax_rate]" class="form-control tax">' + taxOptions + '</select></td>' +
        '<td><input type="text" name="job_challan_items[' + newIndex + '][tax_amount]" class="form-control tax_amount" readonly></td>' +
        '<td><input type="text" name="job_challan_items[' + newIndex + '][total_amount]" class="form-control amount" readonly></td>' +
        '<td><button type="button" class="btn btn-danger btn-remove-row btn-xs" title="Remove Row"><i class="fa fa-times"></i></button></td>' +
    '</tr>';

    $('#items_tbody').append(newRow);
    updateRemoveButtons();
});


/* =========================================================
   REMOVE ROW
   ========================================================= */
$(document).on('click', '.btn-remove-row', function () {
    if ($('#items_tbody .item-row').length > 1) {
        $(this).closest('tr').remove();
        reindexRows();
        updateRemoveButtons();
    }
});

function reindexRows() {
    $('#items_tbody .item-row').each(function (i) {
        $(this).find('input, select').each(function () {
            var name = $(this).attr('name');
            if (name) {
                name = name.replace(/job_challan_items\[\d+\]/, 'job_challan_items[' + i + ']');
                $(this).attr('name', name);
            }
        });
    });
}

function updateRemoveButtons() {
    if ($('#items_tbody .item-row').length <= 1) {
        $('.btn-remove-row').hide();
    } else {
        $('.btn-remove-row').show();
    }
}


/* =========================================================
   AUTO AMOUNT CALCULATION
   ========================================================= */
$(document).on('keyup change', '.qty, .rate, .tax', function () {
    var row = $(this).closest('tr');
    var qty = parseFloat(row.find('.qty').val()) || 0;
    var rate = parseFloat(row.find('.rate').val()) || 0;
    var gst = parseFloat(row.find('.tax').val()) || 0;
    var inhand = parseFloat(row.find('.inhand_qty').val()) || 0;

    // Front-end validation
    if (qty > inhand && inhand > 0) {
        alert('Quantity cannot be greater than available stock (' + inhand + ')');
        row.find('.qty').val('');
        row.find('.tax_amount').val('');
        row.find('.amount').val('');
        return false;
    }

    var amount = qty * rate;
    var gst_amount = (amount * gst) / 100;
    var total = amount + gst_amount;

    row.find('.tax_amount').val(gst_amount.toFixed(2));
    row.find('.amount').val(total.toFixed(2));
});


/* =========================================================
   PRODUCT SEARCH — floating dropdown fixed to viewport
   ========================================================= */
var $jcDropdown = $('#jc-item-dropdown');
var $activeSearchInput = null; // track which input is active

$(document).on('keyup', '.secrh-retail', function () {
    var input = $(this);
    var value = input.val().trim();

    $activeSearchInput = input;

    if (value.length > 0) {
        // Position the dropdown relative to the input using fixed coordinates
        var rect = input[0].getBoundingClientRect();
        $jcDropdown.css({
            top:  rect.bottom + 2,
            left: rect.left,
            width: Math.max(rect.width, 280)
        });

        $.ajax({
            type: 'POST',
            url: '<?php echo ADMIN_URL; ?>Jobchallan/getitemname',
            data: { 
                fetch: value, 
                check: 0,
                process_type: input.closest('#inprogress_section').length > 0 ? 'Semi-Finished' : 'Manufacturing'
            },
            success: function (data) {
                var html = data.trim();
                if (html === '') {
                    $jcDropdown.html('<li class="no-result">No items found</li>').show();
                } else {
                    $jcDropdown.html(html).show();
                }
            }
        });
    } else {
        $jcDropdown.empty().hide();
    }
});

// Close dropdown when clicking outside the input or dropdown
$(document).on('click', function (e) {
    if (!$(e.target).closest('.secrh-retail, #jc-item-dropdown').length) {
        $jcDropdown.empty().hide();
        $activeSearchInput = null;
    }
});


/* =========================================================
   SELECT ITEM — per row, scoped inhand_qty
   ========================================================= */
$(document).on('click', '#jc-item-dropdown li', function () {
    if ($(this).hasClass('no-result')) return;

    var id   = $(this).data('id');
    var text = $(this).text();

    if ($activeSearchInput) {
        var row = $activeSearchInput.closest('tr');
        if (row.length === 0) {
            row = $activeSearchInput.closest('.row');
        }
        
        $activeSearchInput.val(text);
        row.find('.retail_ids').val(id);
        $jcDropdown.empty().hide();
        $activeSearchInput = null;

        if (id) {
            $.ajax({
                type: 'POST',
                url: '<?php echo ADMIN_URL; ?>Jobchallan/getItemInHandStock',
                data: { item_id: id },
                dataType: 'json',
                success: function (response) {
                    row.find('.inhand_qty').val(response.inhand_qty);
                    row.find('input[name*="[hsn_code]"]').val(response.hsn_code);
                    row.find('.tax').val(response.tax_id).trigger('change');
                }
            });
        }
    }
});


/* =========================================================
   VENDOR GST FETCH
   ========================================================= */
$(document).on('change', '#vendor_id', function () {
    var vendor_id = $(this).val();
    if (vendor_id) {
        $.ajax({
            type: 'POST',
            url: '<?php echo ADMIN_URL; ?>Jobchallan/getVendorGst',
            data: { vendor_id: vendor_id },
            success: function (response) { $('#gst_no').val(response); }
        });
    } else {
        $('#gst_no').val('');
    }
});
</script>