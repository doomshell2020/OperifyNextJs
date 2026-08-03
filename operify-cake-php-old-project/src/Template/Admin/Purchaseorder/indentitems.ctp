<?php
$i = $itemname['id'];
$lprcost = $this->Comman->lprcost($itemname['id']);
$tamount = $value['sale_price'] * $value['quantity'];
$tax_find = $value['additem']['taxmaster']['tax'];
$tax_key = $value['additem']['taxmaster']['id'];
$total_tax_amt = $tamount * $tax_find / 100;
$total_final = $tamount + $total_tax_amt;
?>

<tr class="video_details">
  <td width="17%">
    <?php echo $this->Form->input('pitemname[]', array('class' => 'form-control', 'id' => 'pitemname', 'type' => 'hidden', 'value' => $itemname['id'], 'label' => false, 'autofocus', 'autocomplete' => 'off')); ?>
    <?php
    echo $this->Form->input('name[]', array('class' => 'form-control', 'id' => 'pitemname', 'type' => 'text', 'value' => $itemname['item_name'], 'label' => false, 'autofocus', 'autocomplete' => 'off', 'readonly'));
    ?>
    <small style="font-weight:bold;position: relative;"><span style="color:red;"></span></small>
  </td>

  <td width="8%"><input type="text" onkeypress='return isNumberKey(event)' name="pitemquantity[]"
      class="form-control itemQtyCount newquan quntt<?php echo $i; ?>" min="0" id="quan" required autocomplete='off'></td>

  <td width="6%">
    <?php
    echo $this->Form->input('unit_name[]', array('class' => 'form-control', 'type' => 'text', 'value' => isset($itemname['measurementunit']['unit_name']) ? $itemname['measurementunit']['unit_name'] : "--", 'label' => false, 'autofocus', 'autocomplete' => 'off', 'readonly')); ?>
  </td>
  <td width="6%">
    <?php
    echo $this->Form->input('weight[]', array('class' => 'form-control', 'type' => 'text', 'value' => isset($itemname['weight']) ? $itemname['weight'] : "0", 'label' => false, 'autofocus', 'autocomplete' => 'off', 'readonly')); ?>
  </td>
  <td width="6%">
    <?php
    echo $this->Form->input('volume[]', array('class' => 'form-control', 'type' => 'text', 'value' => isset($itemname['volume']) ? $itemname['volume'] : "0", 'label' => false, 'autofocus', 'autocomplete' => 'off', 'readonly')); ?>
  </td>


  <td width="10%">
    <input style="text-align: right;" type="text" maxlength="10" name="pitemrate[]"
      class="form-control itemUnitPrice  filterme newpitemrate pitraa<?php echo $i; ?>" id="pitemrate<?php echo $i; ?>" autocomplete="off" required
      onkeypress='return isNumberKey(event)' value="<?php echo $lprcost; ?>">

    <small style="font-weight:bold;position: relative;">LPR: <span style="color:red;">₹</span>&nbsp;&nbsp;
      <?php echo sprintf('%.2f', $lprcost); ?>
      <a title="View item purchase detail" href="<?php echo ADMIN_URL; ?>purchaseorder/viewitemdetail/<?php echo $itemname['id'] ?>" style="color:#2d95e3;  margin-right:5px;" class="viewitemdetails"><i class="fa fa-eye" style="margin-left: 5px;font-size: 16px !important;color: #e12828;"></i>
      </a>
    </small>
  </td>


  <td width="10%">
    <input style="text-align: right;" type="text" name="pitemamount[]"
      class="form-control totalBasePrice newtamo pitama<?php echo $i; ?>" id="pitemamount" value="" readonly>
  </td>
  <td width="15%">

    <?php
    echo $this->Form->input('tax_id[]', array('class' => 'form-control itemTaxPercentage taxamount' . $i, 'type' => 'select', 'value' => $value['tax_id'], 'options' => $taxMaster, 'label' => false, 'autofocus', 'autocomplete' => 'off', 'readonly')); ?>
  </td>
  <td width="10%">
    <input style="text-align: right;" type="text" name="pitemtax[]" class="form-control  newtaxx pitax<?php echo $i; ?>"
      id="pitax" readonly>
  </td>
  <td width="25%">
    <input style="text-align: right;" type="text" name="totalamount[]"
      class="form-control totalProductAmount newtamso totalamount<?php echo $i; ?>" id="totalamount" readonly
      value="<?php echo sprintf('%.2f', $total_final); ?>">
  </td>
  <!-- this use for delete partculer tr -->
  <td>
    <span class="fas fa-trash-alt delete-button" data-id="<?php echo $i; ?>" id="deletebtn-<?php echo $i; ?>"
      onclick="deleteRow(<?php echo $i; ?>)" style="font-size: 21px; color:#cd0404"></span>
  </td>
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
</script>

<script>
  $('.viewitemdetails').click(function(e) {
    e.preventDefault();
    $('#cancelsorts').modal('show').find('.modal-body').load($(this).attr('href'));
  });
</script>
<div class="modal fade" id="cancelsorts">
  <div class="modal-dialog" style="max-width:999px !important;">
    <div class="modal-content">
      <div class="modal-body"></div>
    </div>
  </div>
</div>