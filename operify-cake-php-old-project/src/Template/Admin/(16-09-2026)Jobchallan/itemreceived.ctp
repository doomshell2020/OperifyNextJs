<style>
    .modal-header {
        background: #3399CC;
        color: #fff;
    }

    .modal-footer {
        border-top: 1px solid #ddd;
        padding: 10px;
        text-align: right;
    }

    .col-sm-4 {
        position: relative;
    }

    .testUL {
        position: absolute;
        width: 100%;
        z-index: 9999;
        display: none;
    }

    .testUL ul {
        position: absolute;
        z-index: 99999;
        max-height: 150px;
        overflow-y: auto;
        top: 100%;
        left: 0;
        right: 0;
        list-style: none;
        background: white;
        padding: 0;
        margin: 0;
        border: 1px solid #ccc;
    }

    .testUL ul li {
        padding: 6px 10px;
        cursor: pointer;
    }

    .testUL ul li:hover {
        background: #f1f1f1;
    }
</style>

<div class="modal-header">
    <h4>Received Item Job Challan Entry</h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<?= $this->Form->create($entity, [
    'id' => 'jobForm',
    'autocomplete' => 'off'
]) ?>

<div class="modal-body">

    <div class="row item-row">

        <div class="col-sm-4">
            <label>Item Name</label>
            <input type="text" name="job_challan_items[0][item_name]" class="form-control secrh-retail">

            <div class="testUL"><ul></ul></div>

            <input type="hidden" name="job_challan_items[0][item_id]" class="retail_ids">
        </div>

        <div class="col-sm-2">
            <label>Qty</label>
            <input type="text" name="job_challan_items[0][quantity]" class="form-control qty">
        </div>

        <div class="col-sm-2">
            <label>Received Date</label>
            <input type="text" name="job_challan_items[0][received_date]" class="form-control received_date" readonly>
        </div>

        <div class="col-sm-2">
            <label>Rate</label>
            <input type="text" name="job_challan_items[0][rate]" class="form-control rate">
        </div>

        <div class="col-sm-2">
            <label>GST %</label>
            <?= $this->Form->input('job_challan_items.0.tax_rate', [
                'options' => $taxMaster,
                'empty' => '-- Select Tax --',
                'label' => false,
                'class' => 'form-control tax'
            ]) ?>
        </div>

        <div class="col-sm-2">
            <label>Tax Amt</label>
            <input type="text" name="job_challan_items[0][tax_amount]" class="form-control tax_amount" readonly>
        </div>

        <div class="col-sm-2">
            <label>Total</label>
            <input type="text" name="job_challan_items[0][amount]" class="form-control amount" readonly>
        </div>

        <div class="col-sm-2 d-flex align-items-end">
            <button type="button" class="btn btn-danger removeRow">Remove</button>
        </div>

    </div>

    <button type="button" class="btn btn-primary mt-2" id="addRow">+ Add More</button>

</div>

<div class="modal-footer">
    <button type="submit" class="btn btn-success">Save</button>
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>

<?= $this->Form->end() ?>


<!-- ✅ ADD ROW -->
<!-- <script>
$('#addRow').click(function () {

    let row = $('.item-row:first').clone();

    row.find('input').val('');
    row.find('select').val('');

    // clean datepicker
    row.find('.received_date')
        .removeClass('hasDatepicker')
        .removeAttr('id')
        .off();

    row.find('.testUL').hide().find('ul').html('');

    $('.item-row:last').after(row);
});
</script> -->
<script>
let rowIndex = 1;

$('#addRow').click(function () {

    let row = $('.item-row:first').clone();

    row.find('input').val('');
    row.find('select').val('');

    // 🔥 update index properly
    row.find('input, select').each(function () {

        let name = $(this).attr('name');

        if (name) {
            let newName = name.replace(/\[\d+\]/, '[' + rowIndex + ']');
            $(this).attr('name', newName);
        }
    });

    // clean datepicker
    row.find('.received_date')
        .removeClass('hasDatepicker')
        .removeAttr('id')
        .off();

    row.find('.testUL').hide().find('ul').html('');

    $('.item-row:last').after(row);

    rowIndex++;
});
</script>

<!-- ✅ REMOVE + RESET INDEX -->
<script>
function resetIndexes() {
    $('.item-row').each(function (i) {
        $(this).find('input, select').each(function () {
            let name = $(this).attr('name');
            if (name) {
                let newName = name.replace(/\[\d+\]/, '[' + i + ']');
                $(this).attr('name', newName);
            }
        });
    });
}

$(document).on('click', '.removeRow', function () {

    if ($('.item-row').length === 1) {
        alert('At least one row is required');
        return;
    }

    $(this).closest('.item-row').remove();
    resetIndexes();
});
</script>


<!-- ✅ GST -->
<script>
$(document).on('input change', '.qty, .rate, .tax', function () {

    let row = $(this).closest('.item-row');

    let qty = parseFloat(row.find('.qty').val()) || 0;
    let rate = parseFloat(row.find('.rate').val()) || 0;
    let gst = parseFloat(row.find('.tax').val()) || 0;

    let amount = qty * rate;
    let taxAmount = (amount * gst) / 100;

    row.find('.tax_amount').val(taxAmount.toFixed(2));
    row.find('.amount').val((amount + taxAmount).toFixed(2));
});
</script>


<!-- ✅ AUTOCOMPLETE -->
<script>
$(document).on('keyup', '.secrh-retail', function() {

    let input = $(this);
    let value = input.val();
    let parent = input.closest('.col-sm-4');
    let dropdown = parent.find('.testUL');

    if (value.length > 0) {
        $.ajax({
            type: 'POST',
            url: '<?php echo ADMIN_URL; ?>Jobchallan/getitemname',
            data: { fetch: value, check: 0 },
            success: function(data) {
                dropdown.show();
                dropdown.find('ul').html(data);
            }
        });
    } else {
        dropdown.hide();
    }
});

$(document).on('click', '.testUL ul li', function() {

    let parent = $(this).closest('.col-sm-4');

    parent.find('.secrh-retail').val($(this).text());
    parent.find('.retail_ids').val($(this).attr('data-id'));
    parent.find('.testUL').hide();
});
</script>


<!-- ✅ FINAL DATEPICKER (NO MULTIPLE ISSUE) -->
<script>
$(document).on('focus', '.received_date', function () {

    let el = $(this);

    if (el.hasClass('hasDatepicker')) {
        el.datepicker('destroy');
    }

    el.datepicker({
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true
    }).datepicker('show');
});
</script>