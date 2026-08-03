<?php $i = $itemname['id'];


$lprcost = $this->Comman->lprcost($itemname['id']);
$tamount = $value['sale_price'] * $value['quantity'];
$tax_find = $value['additem']['taxmaster']['tax'];
$tax_key = $value['additem']['taxmaster']['id'];
$total_tax_amt = $tamount * $tax_find / 100;


$total_final = $tamount + $total_tax_amt; ?>

<tr class="video_details">
  <td width="17%">
    <?php echo $this->Form->input('pitemname[]', array('class' => 'form-control', 'id' => 'pitemname', 'type' => 'hidden', 'value' => $itemname['id'], 'label' => false, 'autofocus', 'autocomplete' => 'off')); ?>
    <?php
    echo $this->Form->input('name[]', array('class' => 'form-control', 'id' => 'pitemname', 'type' => 'text', 'value' => $itemname['item_name'], 'label' => false, 'autofocus', 'autocomplete' => 'off', 'readonly'));
    ?>
    <small style="font-weight:bold;position: relative;"><span style="color:red;"></span></small>
  </td>

  <td width="8%"><input type="text" onkeypress='return isNumberKey(event)' name="pitemquantity[]"
      class="form-control newquan quntt<?php echo $i; ?>" min="0" id="quan" required autocomplete='off'></td>

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
      class="form-control filterme newpitemrate pitraa<?php echo $i; ?>" id="pitemrate" autocomplete="off" required
      onkeypress='return isNumberKey(event)' value="<?php echo $lprcost; ?>">

    <small style="font-weight:bold;position: relative;">LPR: <span style="color:red;">₹</span>&nbsp;&nbsp;
      <?php echo sprintf('%.2f', $lprcost); ?>
      <a title="View item purchase detail" href="<?php echo ADMIN_URL; ?>purchaseorder/viewitemdetail/<?php echo $itemname['id'] ?>"  style="color:#2d95e3;  margin-right:5px;"  class="viewitemdetails"><i class="fa fa-eye" style="margin-left: 5px;font-size: 16px !important;color: #e12828;"></i>
      </a>
    </small>
  </td>


  <td width="10%">
    <input style="text-align: right;" type="text" name="pitemamount[]"
      class="form-control newtamo pitama<?php echo $i; ?>" id="pitemamount" value="" readonly>
  </td>
  <td width="15%">
    <select name="tax_id[]" class="form-control taxamount<?php echo $i; ?> tax_class">
      <option value="">0</option>
      <?php foreach ($tax as $key => $value) { ?>
        <option value="<?php echo $key; ?>">
          <?php echo $value; ?>
        </option>
      <?php }
      ?>
    </select>
  </td>
  <td width="10%">
    <input style="text-align: right;" type="text" name="pitemtax[]" class="form-control  newtaxx pitax<?php echo $i; ?>"
      id="pitax" readonly>
  </td>
  <td width="25%">
    <input style="text-align: right;" type="text" name="totalamount[]"
      class="form-control newtamso totalamount<?php echo $i; ?>" id="totalamount" readonly
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

<!-- old code -->
<!-- 
<script>
  $(document).ready(function () {
    $('.tax_class').val(6);

    //code modify 12-02-2024
    $('.quntt<?php echo $i; ?>').on('keyup', function () {
      var quat = $(this).val();
      var pitra = $('.pitraa<?php echo $i; ?>').val();
      var taxa = $('.taxamount<?php echo $i; ?>').val();
      var tcrate = parseFloat(pitra) * parseFloat(quat);
      tcrate = tcrate.toFixed(2);

      $.ajax({
        type: 'POST',
        url: '<?php echo ADMIN_URL; ?>purchaseorder/gettax',
        data: {
          'fetch': taxa
        },
        success: function (data) {
          var withtax = ((data * tcrate) / 100).toFixed(2);
          var toamountnew = parseFloat(tcrate) + parseFloat(withtax);

          $('.pitama<?php echo $i; ?>').val(tcrate);
          $('.totalamount<?php echo $i; ?>').val(toamountnew);
          $('.pitax<?php echo $i; ?>').val(withtax);
          total();
          total2();
          total3();
        },
      });
    });


    $('.pitraa<?php echo $i; ?>').on('keyup', function () {
      var pitra = $(this).val();
      var quat = $('.quntt<?php echo $i; ?>').val();
      var tcrate = parseFloat(pitra) * parseFloat(quat);
      var taxa = $('.taxamount<?php echo $i; ?>').val();

      $.ajax({
        type: 'POST',
        url: '<?php echo ADMIN_URL; ?>purchaseorder/gettax',
        data: {
          'fetch': taxa
        },
        success: function (data) {
          var withtax = ((data * tcrate) / 100).toFixed(2);
          var toamountnew = parseFloat(tcrate) + parseFloat(withtax);

          $('.pitama<?php echo $i; ?>').val(tcrate.toFixed(2));
          $('.totalamount<?php echo $i; ?>').val(toamountnew.toFixed(2));
          $('.pitax<?php echo $i; ?>').val(withtax);
          total();
          total2();
          total3();
        },
      });

    });


    $(".taxamount<?php echo $i; ?>").on('change', function () {
      var taxa = $(this).val();
      var pitra = $('.pitraa<?php echo $i; ?>').val();
      var quat = $('.quntt<?php echo $i; ?>').val();
      var tcrate = parseFloat(pitra) * parseFloat(quat);
      tcrate = tcrate.toFixed(2);
      var tocost = $('.pitama<?php echo $i; ?>').val();
      var toamount = $('.totala').text();
      var toamountnew;
      var withtax;
      $.ajax({
        type: 'POST',
        url: '<?php echo ADMIN_URL; ?>purchaseorder/gettax',
        data: {
          'fetch': taxa
        },
        success: function (data) {
          var totalget = $('.totala').text();
          withtax = ((data * tcrate) / 100);
          withtax = withtax.toFixed(2);
          toamountnew = parseFloat(tocost) + parseFloat(withtax);
          $('.totalamount<?php echo $i; ?>').val(toamountnew.toFixed(2));
          $('.pitax<?php echo $i; ?>').val(withtax);
          $('.totaltax<?php echo $i; ?>').text(withtax);
          total();
          total2();
        },
      });
    });


  });
</script> -->

<!-- new code -->

<script>
  $(document).ready(function () {
    $('.quntt<?php echo $i; ?>').on('keyup', function () {
        updateRowValues(<?php echo $i; ?>);
    });

    $('.pitraa<?php echo $i; ?>').on('keyup', function () {
        updateRowValues(<?php echo $i; ?>);
    });

    $(".taxamount<?php echo $i; ?>").on('change', function () {
        updateRowValues(<?php echo $i; ?>, true);
    });

    function updateRowValues(rowId, taxChanged = false) {
        var quat = $('.quntt' + rowId).val();
        var pitra = $('.pitraa' + rowId).val();
        var tcrate = parseFloat(pitra) * parseFloat(quat);
        tcrate = tcrate.toFixed(2);
        var taxa = $('.taxamount' + rowId).val();

        if (!taxChanged) {
            taxa = $('.taxamount' + rowId).data('tax-value');
        } else {
            $('.taxamount' + rowId).data('tax-value', taxa); 
        }

        $.ajax({
            type: 'POST',
            url: '<?php echo ADMIN_URL; ?>purchaseorder/gettax',
            data: {
                'fetch': taxa
            },
            success: function (data) {
                var withtax = ((data * tcrate) / 100).toFixed(2);
                var toamountnew = parseFloat(tcrate) + parseFloat(withtax);

                $('.pitama' + rowId).val(tcrate);
                $('.totalamount' + rowId).val(toamountnew);
                $('.pitax' + rowId).val(withtax);
                total();
                total2();
                total3();
            },
        });
    }
});

</script>

<script>
  var totalAmt = 0;
  //show single item  total amount after calculate tax 
  function total() {
    var totals = 0;
    var $dataRows = $("#customers tr:not('.totalColumn, .titlerow , .titlerows')");
    $dataRows.each(function () {
      $(this).find('.newtamso').each(function (i) {
        totals += parseFloat($(this).val());
      });
    });
    $('.totala').html(totals.toFixed(2));
    totalAmt = totals.toFixed(2);
  }

  var total_tax_Amt = 0;
  function total2() {
    var totals2 = 0;
    var $dataRows = $("#customers tr:not('.totalColumn, .titlerow, .titlerows')");
    $dataRows.each(function () {
      $(this).find('.newtaxx').each(function (i) {
        totals2 += parseFloat($(this).val());
      });
    });
    $('.totala2').html(totals2.toFixed(2));
    total_tax_Amt = totals2.toFixed(2);
  }

  function total3() {
    var totals3 = 0;
    var $dataRows = $("#customers tr:not('.totalColumn, .titlerow, .titlerows')");
    $dataRows.each(function () {
      $(this).find('.newtamo').each(function (i) {
        totals3 += parseFloat($(this).val());
      });
    });
    $('.totala1').html(totals3.toFixed(2));
  }
</script>

<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
<script>
  $(document).ready(function () {

    $(".delete-button").on("click", function () {
      var id = $(this).data('id');
      var deletedAmt = $(".totalamount" + id).val();
      var deletedTaxAmt = $(".pitax" + id).val();
      if (!isNaN(deletedAmt) && deletedAmt > 0) {
        var finalAmt = totalAmt - deletedAmt;
        var finalTaxAmt = total_tax_Amt - deletedTaxAmt;
        totalAmt = finalAmt;
        total_tax_Amt = finalTaxAmt;
        $('.totala1').html(finalAmt.toFixed(2));
        $('.totala').html(finalAmt.toFixed(2));
        $('.totala2').html(finalTaxAmt.toFixed(2));
      }
      $(this).closest("tr").remove();
    });
  });

  // Date 02-09-2023 
  // Date 02-09-2023 
  async function getInputValues() {
    let tax_calculate = 0;
    let TotalTaxAmt = 0;
    let per_amt_tax = 0;
    let TotalFinelFooterAmt = 0;
    // Get the table element
    var table = document.getElementById("product_containes");
    var rows = table.getElementsByTagName("tr");
    // alert(rows.length)
    function fetchTaxValue(selectedValue) {
      return new Promise((resolve, reject) => {
        $.ajax({
          type: 'POST',
          url: '<?php echo ADMIN_URL; ?>purchaseorder/gettax',
          data: {
            'fetch': selectedValue
          },
          success: function (data) {
            var tax_value = parseFloat(data);
            resolve(tax_value);
          },
          error: function (error) {
            reject(error);
          }
        });
      });
    }
    async function processRow(i) {
      let TitemQtyPrice = 0;
      let taxTotalAmt = 0;
      let totalAMT = 0;
      var cells = rows[i].getElementsByTagName("td");
      var itemQtyPrice = cells[6].querySelector("input[type='text']");
      TitemQtyPrice = parseFloat(itemQtyPrice.value);

      var selectElement = cells[7].querySelector('select');
      var selectedValue = selectElement.value;
      var texAmt = cells[8].querySelector("input[type='text']");
      taxTotalAmt = parseFloat(texAmt.value);
      var totalAmt = cells[9].querySelector("input[type='text']");
      totalAMT = parseFloat(totalAmt.value);
      try {
        var tax_value = await fetchTaxValue(selectedValue);
        if ($("#taxinclude").is(':checked')) {
          tax_calculate = (TitemQtyPrice - (TitemQtyPrice * (100 / (100 + tax_value))));
          per_amt_tax = TitemQtyPrice;

        } else {
          tax_calculate = (TitemQtyPrice * tax_value / 100);
          per_amt_tax = TitemQtyPrice + tax_calculate;
        }

        totalAmt.value = parseFloat(per_amt_tax.toFixed(2));
        texAmt.value = parseFloat(tax_calculate.toFixed(2));
        TotalTaxAmt += parseFloat(tax_calculate.toFixed(2));
        TotalFinelFooterAmt += parseFloat(per_amt_tax.toFixed(2));
        $('.totala2').html(TotalTaxAmt.toFixed(2));
        $('.totala').html(TotalFinelFooterAmt.toFixed(2));
      } catch (error) {
        console.error(error);
      }
    }

    for (var i = 0; i < rows.length; i++) {
      await processRow(i);
    }

    function deleteRow(row) {
      row.remove();
      if (document.querySelectorAll('table tr').lenght === 0) {
        getInputValues(); // Recalculate values after row deletion
      }
    }
  }
  // Date 01-09-2023 
  // this code is use to calculate tax
  $("#taxinclude").on('change', function () {
    var tax = parseFloat($('.taxamount<?php echo $i; ?>').val());
    $.ajax({
      type: 'POST',
      url: '<?php echo ADMIN_URL; ?>purchaseorder/gettax',
      data: {
        'fetch': tax
      },
      success: function (data) {
        tax_value = parseFloat(data);
        getInputValues();
      },
    });
  });
</script>


<script>
  $('.viewitemdetails').click(function (e) {
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