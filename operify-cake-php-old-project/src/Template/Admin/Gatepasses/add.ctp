<div class="content-wrapper">
    <section class="content-header">
        <h1>Add Gate Pass</h1>
    </section>

    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Gate Pass Details</h3>
            </div>
            
            <?php echo $this->Flash->render(); ?>
            <?= $this->Form->create($gatepass, ['id' => 'gatepassForm']) ?>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Select Job Challan <span class="text-danger">*</span></label>
                            <?= $this->Form->input('jc_id', ['options' => $jcs, 'empty' => 'Select JC', 'multiple' => true, 'class' => 'form-control select2', 'id' => 'jc_id', 'label' => false, 'required' => true]) ?>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Date <span class="text-danger">*</span></label>
                            <input type="date" name="date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Return Date</label>
                            <input type="date" name="return_date" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>To Company <span class="text-danger">*</span></label>
                            <?= $this->Form->input('sub_contractor_id', ['options' => $subContractors, 'empty' => 'Select Company', 'class' => 'form-control select2', 'id' => 'sub_contractor_id', 'label' => false, 'required' => true]) ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Vehicle No.</label>
                            <?= $this->Form->input('vehicle_no', ['class' => 'form-control', 'id' => 'vehicle_no', 'label' => false]) ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Remarks</label>
                            <?= $this->Form->input('remarks', ['class' => 'form-control', 'type' => 'textarea', 'rows' => 2, 'label' => false]) ?>
                        </div>
                    </div>
                </div>

                <h4>Items</h4>
                <table class="table table-bordered" id="itemTable">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th width="15%">Quantity</th>
                            <th width="15%">Unit</th>
                            <th width="30%">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="4" class="text-center text-muted">Please select a Job Challan to load items.</td>
                        </tr>
                    </tbody>
                </table>

            </div>
            <div class="box-footer">
                <button type="submit" class="btn btn-primary">Save Gate Pass</button>
                <a href="<?= ADMIN_URL ?>gatepasses" class="btn btn-default">Cancel</a>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </section>
</div>

<script>
$(document).ready(function() {
    $('#jc_id').change(function() {
        var jc_id = $(this).val();
        if (jc_id && jc_id.length > 0) {
            $.ajax({
                url: '<?= SITE_URL ?>admin/gatepasses/getJcData',
                type: 'GET',
                data: { jc_id: jc_id },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        $('#sub_contractor_id').val(res.data.sub_contractor_id).trigger('change');
                        $('#vehicle_no').val(res.data.vehicle_no);
                        
                        // Clear existing rows
                        $('#itemTable tbody').empty();
                        
                        if (res.data.items && res.data.items.length > 0) {
                            $.each(res.data.items, function(i, item) {
                                addRow(item.item_id, item.item_name, item.quantity, item.jc_no);
                            });
                        } else {
                            $('#itemTable tbody').html('<tr><td colspan="4" class="text-center text-danger">No items found for this Job Challan.</td></tr>');
                        }
                    }
                }
            });
        } else {
            $('#itemTable tbody').html('<tr><td colspan="4" class="text-center text-muted">Please select a Job Challan to load items.</td></tr>');
            $('#sub_contractor_id').val('').trigger('change');
            $('#vehicle_no').val('');
        }
    });

    var rowIdx = 1;
    function addRow(item_id, item_name, qty, jc_no) {
        var jcPrefix = jc_no ? 'JC No: ' + jc_no + '\n' : '';
        var tr = '<tr>' +
            '<td><strong>' + item_name + '</strong>' +
            '<input type="hidden" name="items[' + rowIdx + '][item_id]" value="' + item_id + '">' +
            '<input type="hidden" name="items[' + rowIdx + '][jc_no]" value="' + (jc_no ? jc_no : '') + '">' +
            '<br><textarea name="items[' + rowIdx + '][description]" class="form-control" style="margin-top:5px; height: 60px;" placeholder="Item Description / Calculations (e.g. 1+2=3 KM)"></textarea></td>' +
            '<td><input type="number" step="0.01" name="items[' + rowIdx + '][quantity]" class="form-control" value="' + qty + '" readonly required></td>' +
            '<td><input type="text" name="items[' + rowIdx + '][unit]" class="form-control" value="KG" readonly></td>' +
            '<td><input type="text" name="items[' + rowIdx + '][remarks]" class="form-control"></td>' +
        '</tr>';
        $('#itemTable tbody').append(tr);
        rowIdx++;
    }
});
</script>
