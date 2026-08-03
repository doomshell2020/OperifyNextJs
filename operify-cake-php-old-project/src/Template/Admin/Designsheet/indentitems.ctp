<?php $i = $itemname['id'];
$cat_id = $itemname['category_id'];

?>
<tr class="video_details ?>" id="row-<?php echo $i; ?>">
  <td width="41%">
    <?php echo $this->Form->input('pitemname[' . $i . ']', array('class' => 'form-control', 'id' => 'pitemname', 'type' => 'hidden', 'value' => $itemname['id'], 'label' => false, 'autofocus', 'autocomplete' => 'off')); ?>
    <?php
    echo $this->Form->input('name[' . $i . ']', array('class' => 'form-control', 'id' => 'pitemname', 'type' => 'text', 'value' => $itemname['item_name'], 'label' => false, 'autofocus', 'autocomplete' => 'off', 'readonly'));
    ?>
    <small style="font-weight:bold;position: relative;"><span style="color:red;"></span></small>
  </td>
  <td width="04%">
    <input type="checkbox" name="is_group[<?php echo $i ?>]" value="1"
      onchange="updateCategoryStatus(<?php echo $cat_id; ?>, this.checked)">
  </td>

  <td width="16%"><input type="text" onkeypress='return isNumberKey(event)' name="km_item_qty[<?php echo $i ?>]"
      required class="form-control newquan quntt<?php echo $i; ?>" min="0" id="perkmqty-<?php echo $i; ?>"
      autocomplete='off' onkeyup="calculateqty(this)"></td>

  <td width="16%"><input type="text" onkeypress='return isNumberKey(event)' name="pitemquantity[<?php echo $i ?>]"
      readonly class="form-control newquan totalquan<?php echo $i; ?>" min="0" id="totalqty-<?php echo $i; ?>"
      autocomplete='off'></td>


  <td width="08%">
    <?php
    echo $this->Form->input('unit_name[' . $i . ']', array('class' => 'form-control', 'type' => 'text', 'value' => isset($itemname['measurementunit']['unit_name']) ? $itemname['measurementunit']['unit_name'] : "--", 'label' => false, 'autofocus', 'autocomplete' => 'off', 'readonly')); ?>
  </td>

  <!-- this use for delete partculer tr -->
  <td width="15%">
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




  var checkedCategoryIds = [];

  function updateCategoryStatus(catId, isChecked) {
    if (isChecked) {
      if (checkedCategoryIds.includes(catId)) {
        alert('This category has already been selected. You cannot select the same category again.');
        $('#row-').hide();
        $('.secrh-retails').val('');

        // $('input[name="is_group[' + catId + ']"]').prop('checked', false);
        return;
      }

      checkedCategoryIds.push(catId);
      $.ajax({
        type: 'POST',
        url: '<?php echo ADMIN_URL; ?>designsheet/getitemcatg',
        data: { 'fetch': catId },
        success: function (response) {
          const data = JSON.parse(response);
          addItemToList(data);
        },
        error: function (error) {
          console.error('Error fetching category items:', error);
        }
      });
    } else {

      removeItemsFromList(catId);

      const index = checkedCategoryIds.indexOf(catId);

      if (index !== -1) {
        checkedCategoryIds.splice(index, 1);
      }
    }
  }


  function addItemToList(data) {

    data.items.forEach(item => {
      $('#item-list').append('<li>' + item.name + '</li>');
    });
  }



</script>