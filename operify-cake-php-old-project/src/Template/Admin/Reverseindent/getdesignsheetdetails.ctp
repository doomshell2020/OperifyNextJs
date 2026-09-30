<?php
$itemname = $item['item_name'];
$item_id = $item['id'];
?>
<tr class="totalColumn" style="background-color: #e0e0e0;">
  <th colspan="4">Semi-Finished Product</th>
</tr>
<tr class="video_details">
    <td width="55%">
        <?php echo $this->Form->input('finisheditem_id', array('class' => 'form-control', 'type' => 'select', 'options' => $semiFinishedList, 'empty' => '-- Select Semi-Finished Product --', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'required' => true)); ?>
    </td>
    <td width="15%">
        <?php echo $this->Form->input('unit_name', array('class' => 'form-control', 'type' => 'text', 'value' => $uom, 'label' => false, 'autofocus', 'autocomplete' => 'off', 'readonly')); ?>
    </td>
    <td width="15%">
        <input type="text" id="pending_qty" class="form-control" value="<?php echo $pending_qty; ?>" readonly>
    </td>
    <td width="15%">
        <input type="text" onkeypress='return isNumberKey(event)' name="finished_qty" id="receive_qty" class="form-control newquan" autocomplete='off' required>
    </td>
</tr>

<tr class="totalColumn" style="background-color: #e0e0e0;">
  <th width="55%">Raw Material</th>
  <th width="30%" colspan="2">Received Qty</th>
  <th width="15%">UOM</th>
</tr>

<?php $i = 1;

foreach ($designsheetdetail as $key => $value) {
  $raw_itemname = $this->comman->getitemname($value['item_id']);
  ?>
  <tr class="video_details">

    <?php if ($value['is_group'] == 1) { ?>
        <td width="55%">
          <?php
          $categoryItems = $this->comman->getitembycategory($raw_itemname['category_id']);
          $categoryName = $this->comman->getcategorynmae($raw_itemname['category_id']);
          $options = [];
          foreach ($categoryItems as $itm) {
            $options[$itm['id']] = $itm['item_name'];
          }
          echo $this->Form->select('categ', $options, array('class' => 'form-control category_item', 'id' => 'category-' . $i, 'empty' => '-- ' . $categoryName['category_name'] . '--', 'label' => false, 'autofocus', 'autocomplete' => 'off')); ?>
        </td>

      <?php } else { ?>
        <td width="55%">
          <?php echo $this->Form->input('item_id[]', array('class' => 'form-control', 'type' => 'hidden', 'value' => $value['item_id'], 'label' => false, 'autofocus', 'autocomplete' => 'off')); ?>
          <?php echo $this->Form->input('raw_item_name[]', array('class' => 'form-control', 'type' => 'text', 'value' => $raw_itemname['item_name'], 'label' => false, 'autofocus', 'autocomplete' => 'off', 'readonly')); ?>
        </td>
      <?php } ?>
      
      <?php if ($value['is_group'] == 1) { ?>
        <td width="30%" colspan="2">
          <input type="text" value="--" class="form-control" autocomplete='off' readonly>
        </td>
      <?php } else { ?>
        <td width="30%" colspan="2"><input type="text" onkeypress='return isNumberKey(event)' name="itemquantity[]"
            class="form-control newquan quntt<?php echo $i; ?>" autocomplete='off'></td>
      <?php } ?>

      <td width="15%">
        <?php
        echo $this->Form->input('raw_unit_name[]', array('class' => 'form-control', 'type' => 'text', 'value' => $value['uom'], 'label' => false, 'autofocus', 'autocomplete' => 'off', 'readonly')); ?>
      </td>

  </tr>

  <?php $i++;
} ?>

<tr cellpadding="0">
    <td  colspan="6" style="padding:0px; border:none;">
    <table class="category_row" width="100%">

    </table>
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

      if (itemid) {
        if (previousIds.includes(itemid)) {
          alert('This Item Already added');
        } else {
          $.ajax({
            type: 'POST',
            url: '<?php echo ADMIN_URL; ?>reverseindent/getcategoryindent',
            data: {
              'itemid': itemid,
              'reqQty': reqQty,
              'pendQty': pendQty,
            },
            success: function (data) {
            $(".category_row").append(data);
          },
          });
          previousIds.push(itemid);
        }
      }
    });
  });
</script>