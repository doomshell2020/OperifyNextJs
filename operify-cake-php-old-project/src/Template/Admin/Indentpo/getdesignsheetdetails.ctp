<?php
if ($message != '') { ?>
  <th colspan="6" style="text-align:center; color:red;">
    <?php echo $message ?>
  </th>
<?php } else {

  foreach ($designsheetdetail as $key => $value) {
    $i = $value['item_id'];
    $itemname = $this->comman->getitemcatcom($value['item_id']);

    $itemname = $this->comman->getitemname($value['item_id']);
    $rawitempendingqty = $this->comman->rawitempendingqty($value['item_id'], $itemid, $contractid, $value['is_group']);
    $InhandStock = $this->Comman->InhandStock($value['item_id']);

    $result = ['sum' => round($rawitempendingqty->sum, 2)];
    $qsum = $value['item_qty'] - $result['sum'];
    ?>
    <tr class="video_details">
      <?php if ($value['is_group'] == 1) { ?>
        <td width="40%">
          <?php
          $categoryItems = $this->comman->getitembycategory($itemname['category_id']);
          $categoryName = $this->comman->getcategorynmae($itemname['category_id']);
          $options = [];
          foreach ($categoryItems as $item) {
            $options[$item['id']] = $item['item_name'];
          }
          echo $this->Form->select('categ', $options, array('class' => 'form-control category_item', 'id' => 'category-' . $i, 'empty' => '-- ' . $categoryName['category_name'] . '--', 'label' => false, 'autofocus', 'autocomplete' => 'off')); ?>
        </td>

      <?php } else { ?>
        <td width="40%">
          <?php echo $this->Form->input('item_id[]', array('class' => 'form-control', 'type' => 'hidden', 'value' => $value['item_id'], 'label' => false, 'autofocus', 'autocomplete' => 'off')); ?>
          <?php echo $this->Form->input('item_name[]', array('class' => 'form-control', 'type' => 'text', 'value' => $itemname['item_name'], 'label' => false, 'autofocus', 'autocomplete' => 'off', 'readonly')); ?>
        </td>
      <?php } ?>

      <td width="15%"><input type="text" onkeypress='return isNumberKey(event)' name="itemqty[]" readonly
          class="form-control newquan quntt<?php echo $i; ?>" id="reqQty-<?php echo $i; ?>" autocomplete='off'
          value="<?php echo $value['item_qty'] ?>">
      </td>

      <td width="15%"><input type="text" onkeypress='return isNumberKey(event)' name="pendingqty[]"
          value="<?php echo $qsum; ?>" class="form-control newquan quntt<?php echo $i; ?>" id="pendQty-<?php echo $i; ?>"
          autocomplete='off' readonly></td>

      <?php if ($value['is_group'] == 1) { ?>
        <td width="15%">
          <input type="text" value="--" class="form-control" autocomplete='off' readonly>
        </td>
      <?php } else { ?>
        <td width="15%">
          <input type="text" onkeypress='return isNumberKey(event)' name="currrentstock[]"
            value="<?php echo $InhandStock ? $InhandStock : 0; ?>" class="form-control" id="inhand-<?php echo $i; ?>"
            autocomplete='off' readonly>
        </td>
      <?php } ?>

      <?php if ($value['is_group'] == 1) { ?>
        <td width="15%">
          <input type="text" value="--" class="form-control" autocomplete='off' readonly>
        </td>
      <?php } else {
        if ($stock_update == 'Y') { ?>
          <td width="15%"><input type="text" onkeypress='return isNumberKey(event)' name="itemquantity[]"
              class="form-control newquan quntt<?php echo $i; ?>" autocomplete='off'
              oninput="checkStockQuantity(this, <?php echo $InhandStock; ?>)"></td>
        <?php } else { ?>
          <td width="15%"><input type="text" onkeypress='return isNumberKey(event)' name="itemquantity[]"
              class="form-control newquan quntt<?php echo $i; ?>" autocomplete='off'></td>
        <?php }
      } ?>

    </tr>
    <?php $i++;
  } ?>
  <tr cellpadding="0">
    <td colspan="6" style="padding:0px; border:none;">
      <table class="category_row" width="100%">

      </table>
    </td>
  </tr>

  </div>

<?php } ?>

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

<script type="text/javascript">
  var previousIds = [];
  $(function () {
    $('.category_item').on('change', function () {
      var itemid = $(this).val();
      var inputId = $(this).attr('id');
      var splitParts = inputId.split('-');
      var categoryId = splitParts[1];

      var reqQty = $('#reqQty-' + categoryId).val();
      var pendQty = $('#pendQty-' + categoryId).val();

      var selectedOptionText = $(this).find('option:selected').text();
      if (itemid) {
        if (previousIds.includes(itemid)) {
          alert('This Item Already added');
        } else {
          $.ajax({
            type: 'POST',
            url: '<?php echo ADMIN_URL; ?>indentpo/getcategoryindent',
            data: {
              'itemid': itemid,
              'reqQty': reqQty,
              'pendQty': pendQty,
            },
            success: function (data) {
              $(".category_row").append(data);
              select.val(''); // Reset the value to an empty string
              select.prop('selectedIndex', selectedIndex); // Re-select the previously selected option
            },
          });
          previousIds.push(itemid);
        }
      }
    });
  });
</script>