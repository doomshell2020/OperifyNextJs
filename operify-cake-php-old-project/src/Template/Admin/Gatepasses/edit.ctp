<div class="content-wrapper">
    <section class="content-header">
        <h1>Edit Gate Pass</h1>
    </section>

    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Gate Pass Details (<?= h($gatepass->gatepass_no) ?>)</h3>
            </div>
            
            <?php echo $this->Flash->render(); ?>
            <?= $this->Form->create($gatepass, ['id' => 'gatepassForm']) ?>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Date <span class="text-danger">*</span></label>
                            <input type="date" name="date" class="form-control" value="<?= h($gatepass->date) ? date('Y-m-d', strtotime($gatepass->date)) : '' ?>" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Return Date</label>
                            <input type="date" name="return_date" class="form-control" value="<?= h($gatepass->return_date) ? date('Y-m-d', strtotime($gatepass->return_date)) : '' ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>To Company <span class="text-danger">*</span></label>
                            <?= $this->Form->input('sub_contractor_id', ['options' => $subContractors, 'empty' => 'Select Company', 'class' => 'form-control select2', 'label' => false, 'required' => true]) ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Vehicle No.</label>
                            <?= $this->Form->input('vehicle_no', ['class' => 'form-control', 'label' => false]) ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Status</label>
                            <?= $this->Form->input('status', ['options' => ['Active' => 'Active', 'Cancelled' => 'Cancelled'], 'class' => 'form-control', 'label' => false]) ?>
                        </div>
                    </div>
                    <div class="col-md-9">
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
                        <?php 
                        $i = 0;
                        if (!empty($gatepass->gatepass_items)):
                            foreach ($gatepass->gatepass_items as $item): 
                        ?>
                        <tr>
                            <td>
                                <strong><?= $item->has('additem') ? h($item->additem->item_name) : 'Unknown Item' ?></strong>
                                <input type="hidden" name="items[<?= $i ?>][item_id]" value="<?= h($item->item_id) ?>">
                                <textarea name="items[<?= $i ?>][description]" class="form-control" style="margin-top:5px; height: 60px;" placeholder="Item Description / Calculations (e.g. 1.025 + 1.025)"><?= h($item->description) ?></textarea>
                            </td>
                            <td>
                                <input type="number" step="0.01" name="items[<?= $i ?>][quantity]" class="form-control" value="<?= h($item->quantity) ?>" readonly required>
                            </td>
                            <td>
                                <input type="text" name="items[<?= $i ?>][unit]" class="form-control" value="<?= h($item->unit) ?>" readonly>
                            </td>
                            <td>
                                <input type="text" name="items[<?= $i ?>][remarks]" class="form-control" value="<?= h($item->remarks) ?>">
                            </td>
                        </tr>
                        <?php 
                            $i++;
                            endforeach;
                        else:
                        ?>
                        <tr>
                            <td colspan="4" class="text-center text-danger">No items found in this Gate Pass.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

            </div>
            <div class="box-footer">
                <button type="submit" class="btn btn-primary">Update Gate Pass</button>
                <a href="<?= ADMIN_URL ?>gatepasses" class="btn btn-default">Cancel</a>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </section>
</div>

<script>
$(document).ready(function() {
    // Items are fixed to the Job Challan.
});
</script>
