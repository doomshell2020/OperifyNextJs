<div class="content-wrapper">
    <section class="content-header">
        <h1>JC Receive</h1>
    </section>
    <section class="content">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">Receive Add</h3>
            </div>
            <div class="box-body">
                <?= $this->Form->create(null, ['id' => 'jcReceiveForm']) ?>
                
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label>Created JC <span class="text-danger">*</span></label>
                        <?= $this->Form->input('challan_id', [
                            'type' => 'select',
                            'options' => $eligibleJCs,
                            'empty' => '[ Select Created JC ]',
                            'label' => false,
                            'class' => 'form-control',
                            'id' => 'challan_id',
                            'required' => true
                        ]) ?>
                    </div>
                </div>

                <!-- JC Details Section -->
                <div id="jc_details_section" style="display:none; margin-top: 20px; border: 1px solid #ddd; padding: 15px; background: #f9f9f9;">
                    <h4>JC Details</h4>
                    <hr>
                    <div class="row">
                        <div class="col-md-4"><p><strong>JC Number:</strong> <span id="lbl_challan_no"></span></p></div>
                        <div class="col-md-4"><p><strong>JC Date:</strong> <span id="lbl_jc_date"></span></p></div>
                        <div class="col-md-4"><p><strong>From Company:</strong> <span id="lbl_from_company"></span></p></div>
                    </div>

                    <div style="margin-top:15px; overflow-x:auto;">
                        <table class="table table-bordered table-striped" id="jc_items_table">
                            <thead>
                                <tr style="background:#3c8dbc; color:#fff;">
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th>Original Qty</th>
                                    <th>Received Qty</th>
                                    <th>Pending Qty</th>
                                    <th style="width: 150px;">Receive Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>


                </div>

                <div class="row" style="margin-top: 20px;">
                    <div class="col-md-12 text-right">
                        <button type="submit" class="btn btn-primary" id="btnSubmit" style="display:none;">Save JC Receive</button>
                    </div>
                </div>

                <?= $this->Form->end() ?>
            </div>
        </div>
    </section>
</div>

<script>
$(document).ready(function() {
    <?php if (!empty($preselect_id)): ?>
        // Pre-select: find the option whose value starts with the preselect_id
        $('#challan_id option').each(function() {
            if ($(this).val().indexOf('<?= h($preselect_id) ?>') === 0) {
                $(this).prop('selected', true);
                $('#challan_id').trigger('change');
                return false;
            }
        });
    <?php endif; ?>

    $('#challan_id').change(function() {
        var combinedKey = $(this).val(); // format: "challan_id|sender_db"
        if (combinedKey) {
            $.ajax({
                url: '<?= SITE_URL ?>admin/jobchallan/getJcDetails',
                type: 'GET',
                data: { challan_id: combinedKey },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        $('#lbl_challan_no').text(res.data.challan_no);
                        $('#lbl_jc_date').text(res.data.jc_date);
                        $('#lbl_from_company').text(res.data.from_company);
                        
                        var tbody = $('#jc_items_table tbody');
                        tbody.empty();
                        
                        if (res.data.items && res.data.items.length > 0) {
                            $.each(res.data.items, function(i, item) {
                                var tr = $('<tr>');
                                tr.append('<td>' + item.item_name + '<input type="hidden" name="items[' + i + '][item_id]" value="' + item.item_id + '"><input type="hidden" name="items[' + i + '][item_name]" value="' + item.item_name + '"><input type="hidden" name="items[' + i + '][dispatch_qty]" value="' + item.original_qty + '"></td>');
                                tr.append('<td>' + item.category + '</td>');
                                tr.append('<td>' + item.original_qty + ' ' + item.unit + '</td>');
                                tr.append('<td>' + item.received_qty + ' ' + item.unit + '</td>');
                                tr.append('<td>' + item.pending_qty + ' ' + item.unit + '</td>');
                                tr.append('<td><input type="number" step="0.01" name="items[' + i + '][receive_qty]" class="form-control receive_qty_input" data-pending="' + item.pending_qty + '" data-unit="' + item.unit + '" min="0" max="' + item.pending_qty + '" placeholder="0"><div class="text-danger error-msg" style="display:none; font-size: 11px; margin-top: 4px;"></div></td>');
                                tbody.append(tr);
                            });
                        } else {
                            tbody.append('<tr><td colspan="6" class="text-center text-danger">No pending items found for this JC.</td></tr>');
                        }

                        $('#jc_details_section').slideDown();
                        $('#btnSubmit').show();
                    } else {
                        alert(res.message);
                        resetForm();
                    }
                }
            });
        } else {
            resetForm();
        }
    });

    function resetForm() {
        $('#jc_details_section').slideUp();
        $('#btnSubmit').hide();
        $('#jc_items_table tbody').empty();
    }

    $(document).on('input', '.receive_qty_input', function() {
        var val = $(this).val();
        var receive = parseFloat(val);
        var pending = parseFloat($(this).data('pending'));
        var unit = $(this).data('unit');
        var errorMsg = $(this).siblings('.error-msg');

        if (val !== '' && !isNaN(receive) && receive > pending) {
            $(this).val('');
            $(this).css('border-color', 'red');
            errorMsg.text('Receive Qty cannot be greater than Pending Qty (' + pending + ' ' + unit + ').').show();
        } else {
            $(this).css('border-color', '');
            errorMsg.hide();
        }
        
        var hasError = false;
        $('.receive_qty_input').each(function() {
            var r = parseFloat($(this).val());
            var p = parseFloat($(this).data('pending'));
            if (!isNaN(r) && r > p) {
                hasError = true;
            }
        });
        
        $('#btnSubmit').prop('disabled', hasError);
    });

    // Frontend validation for Receive Qty
    $('#jcReceiveForm').submit(function(e) {
        var hasValidEntry = false;
        var hasError = false;

        $('.receive_qty_input').each(function() {
            var val = $(this).val();
            if (val && parseFloat(val) > 0) {
                hasValidEntry = true;
                var receive = parseFloat(val);
                var pending = parseFloat($(this).data('pending'));
                
                if (receive > pending) {
                    hasError = true;
                    $(this).css('border-color', 'red');
                } else {
                    $(this).css('border-color', '');
                }
            }
        });

        if (!hasValidEntry) {
            e.preventDefault();
            alert("Please enter a receive quantity for at least one item.");
            return false;
        }

        if (hasError) {
            e.preventDefault();
            alert("One or more receive quantities exceed the pending quantity.");
            return false;
        }
    });
});
</script>
