<?php $i = $value['item_id'];
$itemname = $this->comman->getitemname($value['item_id']);
?>
<tr class="video_details">
  <td width="55%">
    <?php echo $this->Form->input('item_id[]', array('class' => 'form-control', 'type' => 'hidden', 'value' => $value['item_id'], 'label' => false, 'autofocus', 'autocomplete' => 'off')); ?>
    <?php echo $this->Form->input('item_name[]', array('class' => 'form-control', 'type' => 'text', 'value' => $itemname['item_name'], 'label' => false, 'autofocus', 'autocomplete' => 'off', 'readonly')); ?>
  </td>
  <td width="30%"><input type="text" onkeypress='return isNumberKey(event)' name="itemquantity[]"
      class="form-control newquan quntt<?php echo $i; ?>" autocomplete='off'>
  </td>
  <td width="15%">
    <input type="text" name="uom[]" class="form-control " autocomplete='off' value = <?php echo  $value['uom'] ;?> >
  </td>
  <td></td>
</tr>