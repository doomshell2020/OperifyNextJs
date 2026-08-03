<?php $i = $value['item_id'];
$itemname = $this->comman->getitemname($value['item_id']);
?>
<tr class="video_details">
  <td width="40%">
    <?php echo $this->Form->input('item_id[]', array('class' => 'form-control', 'type' => 'hidden', 'value' => $value['item_id'], 'label' => false, 'autofocus', 'autocomplete' => 'off')); ?>
    <?php echo $this->Form->input('item_name[]', array('class' => 'form-control', 'type' => 'text', 'value' => $itemname['item_name'], 'label' => false, 'autofocus', 'autocomplete' => 'off', 'readonly')); ?>
  </td>
  <td width="15%"><input type="text" onkeypress='return isNumberKey(event)' name="itemqty[]" readonly
      class="form-control newquan quntt<?php echo $i; ?>" autocomplete='off' value="<?php echo $value['reqQty'] ?>">
  </td>
  <td width="15.1%"><input type="text" onkeypress='return isNumberKey(event)' name="pendingqty[]"
      value="<?php echo $value['pendQty']; ?>" class="form-control newquan quntt<?php echo $i; ?>" autocomplete='off'
      readonly>
  </td>
  <td width="15%">
    <input type="text" onkeypress='return isNumberKey(event)' name="currrentstock[]"
      value="<?php echo $value['current_stock']; ?>" class="form-control" id="inhand-<?php echo $i; ?>"
      autocomplete='off' readonly>
  </td>
  <?php if ($stock_update  = 'Y') { ?>
    <td width="15%"><input type="text" onkeypress='return isNumberKey(event)' name="itemquantity[]"
        class="form-control newquan quntt<?php echo $i; ?>" autocomplete='off'
        oninput="checkStockQuantity(this, <?php echo $value['current_stock']; ?>)">
    </td>
  <?php } else { ?>
    <td width="15%"><input type="text" onkeypress='return isNumberKey(event)' name="itemquantity[]"
        class="form-control newquan quntt<?php echo $i; ?>" autocomplete='off'>
    </td>
  <?php } ?>
</tr>


<script>
  function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    var inputValue = evt.target.value;

    var hasDecimal = inputValue.includes('.');

    if (charCode === 46) {
      if (hasDecimal) {
        return false;
      }
    } else if (charCode > 31 && (charCode < 48 || charCode > 57)) {
      return false;
    }

    if (hasDecimal) {
      var decimalIndex = inputValue.indexOf('.');
      var decimalPart = inputValue.substring(decimalIndex + 1);

      if (decimalPart.length >= 2) {
        return false;
      }
    }

    return true;
  }

  function checkStockQuantity(inputElement, stock) {
    let value = parseInt(inputElement.value);
    // If value is not a number or greater than the stock, reset it to stock value
    if (value > stock) {

      inputElement.value = stock;
      alert('Cannot add Issue Quantity greater than Inhand Stock');
    }
  }
</script>

