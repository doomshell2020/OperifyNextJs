<?php
// pr($identpodetails);

foreach ($identpodetails as $value) {
    $itemname = $this->Comman->getitemname($value['item_id']); ?>
    <div class="col-md-3">
        <?php echo $this->Form->input('materialname', array('class' => 'form-control', 'type' => 'hidden', 'value' => $value['item_id'], 'label' => false, 'required', 'placeholder' => 'Raw Material', 'autofocus', 'autocomplete' => 'off', 'readonly')); ?>

        <?php echo $this->Form->input('materialname', array('class' => 'form-control', 'type' => 'text', 'value' => $itemname['item_name'], 'label' => false, 'required', 'placeholder' => 'Raw Material', 'autofocus', 'autocomplete' => 'off', 'readonly')); ?>
    </div>

    <div class="col-md-3">
        <label for="inputEmail3" class=" control-label" style="text-align: left !important">
            Material(As per Design) <strong style="color:red;">*</strong></label>
        <?php echo $this->Form->input('material_desgin', array('class' => 'form-control', 'type' => 'text', 'value' => $newindentid, 'required', 'label' => false, 'placeholder' => ' Material(As per Design)', 'autofocus', 'autocomplete' => 'off', 'readonly')); ?>
    </div>
    
    <div class="col-md-3">
        <label for="inputEmail3" class=" control-label" style="text-align: left !important">
            Material Issued(As per Indent)<strong style="color:red;">*</strong></label>
        <?php echo $this->Form->input('material_issued', array('class' => 'form-control', 'type' => 'text', 'value' => $value['item_qty'], 'required', 'label' => false, 'placeholder' => ' Material Issued', 'autofocus', 'autocomplete' => 'off', 'readonly')); ?>
    </div>

    <div class="col-md-3">
        <label for="inputEmail3" class=" control-label" style="text-align: left !important">
            Material Consumed <strong style="color:red;">*</strong></label>
        <?php echo $this->Form->input('material_consumed', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'placeholder' => ' Material Consumed', 'required', 'autofocus', 'autocomplete' => 'off')); ?>
    </div>

<?php };die; ?>