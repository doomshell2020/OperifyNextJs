<?php

$i = 1;
foreach ($item as $value) {

  $pendingqty = $this->comman->findfinishedqty($contractid, $value['product_id']);

  // pr($value);
  foreach ($pendingqty as $qty) {
    $proqty += $qty['plannedqty'];
  }
  // pr($proqty);

  if ($proqty) {
    $quantity = $value['quantity'] - $proqty;
  } else {
    $quantity = $value['quantity'];
  }
  //  pr($quantity);


?>
  <tr>
    <td>
      <?php echo $this->Form->input('item_id[]', array('class' => 'form-control', 'value' => $value['product_id'], 'type' => 'hidden', 'readonly', 'label' => false)); ?>
      <?php echo $this->Form->input('itemname', array('class' => 'form-control', 'id' => 'indentid-' . $i, 'value' => $value['additem']['item_name'], 'readonly', 'label' => false)); ?>
    </td>
    <td>
      <?php echo $this->Form->input('reqqty', array('class' => 'form-control', 'value' => ($value['quantity']), 'autocomplete' => 'off', 'readonly', 'label' => false)); ?>
    </td>
    <td>
      <?php echo $this->Form->input('pendingqty', array('class' => 'form-control', 'value' => $quantity, 'autocomplete' => 'off', 'readonly', 'label' => false)); ?>
    </td>


    <td><input type="text" name="plannedqty[]" autocomplete="off" class="form-control newquan quntt<?php echo $i; ?>"
        id="quan-<?php echo $i; ?>" max="<?php echo $quantity; ?>" onkeypress='return isNumberKey(event)'
        onchange="checkQuantity(this, <?php echo $quantity; ?>)"></td>

    <td>
      <?php echo $this->Form->input('uom[]', array('class' => 'form-control', 'readonly', 'value' => $value['additem']['measurementunit']['unit_name'], 'label' => false)); ?>
    </td>

    <td>
      <?php echo $this->Form->input('startdate[]', array('class' => 'form-control', 'id' => 'fdatefrom-' . $i, 'placeholder' => 'Date', '', 'label' => false, 'readonly')); ?>
    </td>
    <td>
      <?php echo $this->Form->input('enddate[]', array('class' => 'form-control', 'id' => 'fendfrom-' . $i, 'placeholder' => 'Date', '', 'label' => false, 'readonly')); ?>
    </td>
    <td>
      <?php echo $this->Form->input('totaldays[]', array('class' => 'form-control', 'id' => 'totaldays-' . $i, 'readonly', 'label' => false)); ?>
      <span id="daysError" class="error" style="color: red;"></span>
    </td>
    <!-- <td>
    <span class="fas fa-trash-alt delete-button" data-id="<?php echo $i; ?>" style="font-size: 21px; color:#cd0404">
    </td> -->
    <!-- <script>

      function calculateday(inputId) {

        if (inputId !== undefined) {
          var idParts = inputId.id.split("-");
          var numberAfterUnderscore = idParts[1];
        }

        $(`#fdatefrom-${numberAfterUnderscore}`).datepicker({
          dateFormat: 'dd-mm-yy',
          yearRange: '2018:2030',
          defaultDate: 'today',
          changeMonth: true,
          changeYear: true,

          onSelect: function (selectedDate) {
            calculateTotalDays();
          }
        });
        $(`#fendfrom-${numberAfterUnderscore}`).datepicker({
          dateFormat: 'dd-mm-yy',
          yearRange: '2018:2030',
          defaultDate: 'today',
          changeMonth: true,
          changeYear: true,
          onSelect: function (selectedDate) {
            calculateTotalDays();
          }
        });

        function calculateTotalDays() {
          var startDate = $(`#fdatefrom-${numberAfterUnderscore}`).datepicker('getDate');
          var endDate = $(`#fendfrom-${numberAfterUnderscore}`).datepicker('getDate');

          if (startDate && endDate) {
            var timeDiff = endDate - startDate;
            var daysDiff = Math.ceil(timeDiff / (1000 * 3600 * 24)) + 1;
            if (daysDiff > 0) {
              $(`#totaldays-${numberAfterUnderscore}`).val(daysDiff);
            } else {
              alert('Total days can not be negative');
              $(`#fdatefrom-${numberAfterUnderscore}`).val() = '';
            }
          }
        }
      }

    </script> -->

    <script>
      $(document).ready(function() {

        $('[id^=fdatefrom-]').each(function() {
          let index = this.id.split('-')[1];

          $(`#fdatefrom-${index}`).datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true,
            onSelect: function() {
              calculateTotalDays(index);
            }
          });

          $(`#fendfrom-${index}`).datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true,
            onSelect: function() {
              calculateTotalDays(index);
            }
          });

        });

      });

      function calculateTotalDays(index) {
        var startDate = $(`#fdatefrom-${index}`).datepicker('getDate');
        var endDate = $(`#fendfrom-${index}`).datepicker('getDate');

        if (startDate && endDate) {
          var timeDiff = endDate - startDate;
          var daysDiff = Math.ceil(timeDiff / (1000 * 3600 * 24)) + 1;

          if (daysDiff > 0) {
            $(`#totaldays-${index}`).val(daysDiff);
          } else {
            alert('Total days cannot be negative');
            $(`#fdatefrom-${index}`).val('');
          }
        }
      }
    </script>

  </tr>

<?php
  $proqty = '';
  $i++;
} ?>

<input type="hidden" value="<?= $itemcount; ?>" id="totalitemCount" name="itemCount">



<script>
  let totalTd = '<?php echo $itemcount; ?>';

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


  $(document).ready(function() {
    $(".delete-button").on("click", function() {
      $(this).closest("tr").remove();
    });
  });
</script>
<script>
  function checkQuantity(input, maxValue) {
    var enteredValue = parseInt(input.value);
    if (enteredValue == 0 && enteredValue < 0) {
      alert("Quantity Can't be equal 0");
      input.value = '';
    } else if (enteredValue > maxValue) {
      alert("Quantity Can't be greater than " + maxValue);
      input.value = '';
    }
  }
</script>