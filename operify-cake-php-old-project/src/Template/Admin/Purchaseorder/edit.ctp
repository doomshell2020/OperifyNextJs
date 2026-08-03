<style>
  #test1UL {
    position: relative;
  }
  .control-label {
    display: block;
    margin-top: 10px;
  }

  #test1UL ul {
    position: absolute;
    z-index: 999;
    overflow: scroll;
    height: 100px;
    top: 100%;
    left: 0px;
    right: 0px;
    list-style-type: none;
    background-color: white;
    padding-left: 0px;
  }

  #test1UL ul li {
    padding: 5px 8px;
    border: 1px solid lightgray;
  }

  #test1UL ul li a {
    color: black;
  }

  .preview {
    margin-right: 15px;
  }

  .input_fields_wrap .form-control {
    margin-bottom: 15px;
  }
</style>
<style>
  #customers {
    font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
    border-collapse: collapse;
    width: 100%;
    margin-bottom: 20px;
  }

  #customers td,
  #customers th {
    border: 1px solid #ddd;
    padding: 8px;
  }

  #customers tr:nth-child(even) {
    background-color: #f2f2f2;
  }

  #customers tr:hover {
    background-color: #ddd;
  }

  #customers th {
    padding-top: 12px;
    padding-bottom: 12px;
    text-align: left;
    background-color: #c8c8c8;
    color: #333333;
  }

  #testUL,
  #testULs {
    position: relative;
    display: none;
  }

  #testUL ul,
  #testULs ul {
    position: absolute;
    max-height: 140px;
    overflow: scroll;
    z-index: 999;
    top: 100%;
    left: 0px;
    right: 0px;
    list-style-type: none;
    background-color: white;
    padding-left: 0px;
  }

  #testUL ul li,
  #testULs ul li {
    padding: 5px 8px;
    border: 1px solid lightgray;
  }

  #testUL ul li a,
  #testULs ul li a {
    color: black;
  }
</style>


<?php

function formatCurrency($amount)
{
  return ($amount == floor($amount))
    ? number_format($amount, 0, '.', ',') // no decimals if .00
    : number_format($amount, 2, '.', ','); // 2 decimals if not .00
}

function formatDecimal($value)
{
  $value = floatval($value);
  if (floor($value) == $value) {
    return (string)(int)$value;
  }
  return number_format($value, 2, '.', '');
}


?>


<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Revised Purchase Order Manager
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo SITE_URL; ?>admin/purchaseorder"><i class="fa fa-home"></i>Home</a></li>
    </ol>
  </section>
  <!-- Main content -->
  <section class="content">
    <div class="row">
      <!-- right column -->
      <div class="col-md-12">
        <!-- Horizontal Form -->
        <div class="box box-info">
          <?php echo $this->Flash->render(); ?>
          <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-plus-square" aria-hidden="true"></i>
              <?php echo 'Revised Purchase Order id : ' . $revised['purchaseorder_id']; ?>
            </h3>
          </div>
          <!-- /.box-header -->
          <!-- form start -->
          <?php echo $this->Form->create(
            $revised,
            array(
              'controller' => 'purchaseorder',
              'class' => 'form-horizontal',
              'enctype' => 'multipart/form-data',
              'id' => 'sevice_form',
              'validate'
            )
          ); ?>
          <div class="box-body">
            <?php echo $this->Form->input('purchaseorder_id', array('class' => 'form-control', 'id' => 'purchaseorder', 'type' => 'hidden', 'value' => $revised['purchaseorder_id'], 'readonly', 'label' => false, 'placeholder' => 'purchaseorder id', 'autofocus', 'autocomplete' => 'off')); ?>
            <div class="form-group" style="margin-bottom:0px;">
              <div class="row">
                <div class="col-sm-4">
                  <label for="inputEmail3" class=" control-label">Supplier <strong style="color:red;">*</strong></label>
                  <input type="hidden" name="vendor_id" id="retail_ids" value="<?php echo $revised['vendor_id']; ?>">
                  <?php echo $this->Form->input('vendorname', array('class' => 'form-control', 'id' => 'supplier', 'type' => 'text', 'readonly', 'value' => $vendorname['name'], 'label' => false, 'autofocus', 'autocomplete' => 'off', 'required', 'placeholder' => 'Enter Supplier Name')); ?>
                </div>
                <script>
                  $(document).ready(function () {
                    $(".readonly").on('keyup', function (e) {
                      $(".readonly").val('');
                    });
                  });
                </script>
                <div class="col-sm-4">
                  <label for="inputEmail3" class=" control-label">Expected Delivery Date<strong
                      style="color:red;">*</strong></label>
                  <?php echo $this->Form->input('delivery_date', array('class' => 'form-control', 'id' => '', 'readonly', 'type' => 'text', 'label' => false, 'placeholder' => 'Delivery Date', 'value' => $vendorname['delivery_date'], 'autofocus', 'autocomplete' => 'off', 'required')); ?>
                </div>

                <div class="col-md-4">
                  <label for="inputEmail3" class=" control-label">Contract</label>
                  <?php echo $this->Form->input('contract', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'placeholder' => 'Enter Contract Name')); ?>
                </div>
                <div class="col-md-4">
                  <label for="inputEmail3" class=" control-label">Project</label>
                  <?php echo $this->Form->input('project', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'placeholder' => 'Enter Project Name')); ?>
                </div>
              </div>
            </div>

            <div class="ctpcontent form-group" style="display:block">
              <div class="col-sm-12">
                <label for="inputEmail3" style="margin-bottom:10px;">Items</label>
                <table id="customers">
                  <thead>
                    <tr class="totalColumn">

                      <th>Item</th>
                      <th>Qty</th>
                      <th>UOM</th>
                      <th>Weight</th>
                      <th>Volume</th>
                      <th>Unit Price</th>
                      <th>Total Price</th>
                      <th>Tax Rate</th>
                      <th>Tax Amount</th>
                      <th>Total Amount</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody id="product_containes">
                    <?php
                    $i = 1;
                    foreach ($poitems as $value) {
                      // pr($value);
                      $getitemname = $this->Comman->getitemname($value['item_id']);
                      $gettaxname = $this->Comman->gettaxname($value['tax_id']);

                      $qty = $this->Comman->stockregisteritems($value['purchaseorder_id'], $value['item_id']);
                      $result = ['sum' => round($qty->sum, 2)];
                      $qsum = $result['sum'];
                      // pr($qsum);
                      ?>


                      <tr class="video_details">
                        <td width="17%">
                          <?php echo $this->Form->input('pitemname[]', array('class' => 'form-control', 'id' => 'pitemname', 'type' => 'hidden', 'value' => $value['item_id'], 'label' => false, 'autofocus', 'autocomplete' => 'off')); ?>
                          <?php
                          echo $this->Form->input('name[]', array('class' => 'form-control', 'id' => 'pitemname', 'type' => 'text', 'value' => $getitemname['item_name'], 'label' => false, 'autofocus', 'autocomplete' => 'off', 'readonly'));
                          ?>
                          <small style="font-weight:bold;position: relative;"><span style="color:red;"></span></small>
                        </td>

                        <td width="8%"><input type="text" onkeypress='return isNumberKey(event)' name="pitemquantity[]"
                            class="form-control newquan quntt<?php echo $i; ?>"  id="quan" autocomplete='off' required
                            min="<?php echo $qsum; ?>" onchange="checkQuantity(this, <?php echo $qsum; ?>)"
                            value="<?php echo sprintf('%.2f', $value['item_qty']); ?>"></td>

                        <td width="6%">
                          <?php
                          echo $this->Form->input('unit_name[]', array('class' => 'form-control', 'type' => 'text', 'value' => $value['uom'], 'label' => false, 'autofocus', 'autocomplete' => 'off', 'readonly')); ?>
                        </td>
                        <td width="6%">
                          <?php
                          echo $this->Form->input('weight[]', array('class' => 'form-control', 'type' => 'text', 'value' => $value['weight'], 'label' => false, 'autofocus', 'autocomplete' => 'off', 'readonly')); ?>
                        </td>
                        <td width="6%">
                          <?php
                          echo $this->Form->input('volume[]', array('class' => 'form-control', 'type' => 'text', 'value' => $value['volume'], 'label' => false, 'autofocus', 'autocomplete' => 'off', 'readonly')); ?>
                        </td>
                        <td width="10%"><input style="text-align: right;" type="text" readonly maxlength="10"
                            name="pitemrate[]" class="form-control filterme newpitra pitraa<?php echo $i; ?>"
                            id="pitemrate" autocomplete="off" value="<?php echo sprintf('%.2f', $value['item_amt']); ?>">
                        </td>

                        <?php
                        $costprice = $value['item_qty'] * $value['item_amt'];
                        ?>
                        <td width="10%">
                          <input style="text-align: right;" type="text" name="pitemamount[]"
                            class="form-control newtamo pitama<?php echo $i; ?>" id="pitemamount<?php echo $i ?>"
                            value="<?php echo $costprice; ?>" readonly>
                        </td>

                        <td width="15%">
                          <input type="text" name="tax_value[]" class="form-control taxamount<?php echo $i; ?>"
                            value="<?php echo $gettaxname['tax']; ?>" readonly>
                          <input type="hidden" name="tax_id[]" class="form-control "
                            value="<?php echo $value['tax_id']; ?>" readonly>
                        </td>


                        <td width="10%">
                          <input style="text-align: right;" type="text" name="pitemtax[]"
                            value="<?php echo $value['item_tax_amt']; ?>"
                            class="form-control  newtaxx pitax<?php echo $i; ?>" id="pitax<?php echo $i; ?>" readonly>
                        </td>

                        <td width="25%">
                          <input style="text-align: right;" type="text" name="totalamount[]" readonly
                            class="form-control newtamso totalamount<?php echo $i; ?>" id="totalamount<?php echo $i; ?>"
                            value="<?php echo sprintf('%.2f', $value['item_total_amount']); ?>">
                        </td>
                        <td></td>
                      </tr>

                      <script>
                        $('.quntt<?php echo $i; ?>').on('input', function () {

                          var quantity = $('.quntt<?php echo $i; ?>').val();
                          var unitPrice = $('.pitraa<?php echo $i; ?>').val();
                          var taxRate = $('.taxamount<?php echo $i; ?>').val();
                          $('.pitama<?php echo $i; ?>').val((quantity * unitPrice).toFixed(2));
                          var tax_amt = quantity * unitPrice * taxRate / 100;
                          $('.pitax<?php echo $i; ?>').val(tax_amt.toFixed(2));
                          var totamt = tax_amt + quantity * unitPrice;
                          subtot = $('.totalamount<?php echo $i; ?>').val(totamt.toFixed(2));
                        });
                      </script>

                      <?php $i++;
                      $cost += $costprice;
                      $tax += $value['item_tax_amt'];
                      $amt += $value['item_total_amount'];

                    } ?>
                  </tbody>

                  <tfoot>
                    <!-- <tr class="titlerows" style="background-color: #c8c8c8;">
                      <td colspan="11" type="" style="font-weight:bold;font-size:16px;">
                        <?php echo $this->Form->input('item_name', array('class' => 'form-control secrh-retails', 'id' => 'indent', 'type' => 'text', 'label' => false, 'required', 'autofocus', 'autocomplete' => 'off', 'placeholder' => 'Enter Item Name')); ?>
                        <div id="test1UL" style="display:none;">
                          <ul></ul>
                        </div>
                      </td>
                    </tr> -->
                    <tr class="titlerows" style="background-color: #c8c8c8;">
                      <!-- <td colspan="9" type="" class="text-right" style="font-weight:bold;font-size:16px;">Tax Included
                      </td>
                      <td colspan="5">
                        <input type="checkbox" name="tax_cal" id="taxinclude" class="retail_idsss">
                      </td> -->
                    </tr>
                    <tr class="titlerow" style="background-color: #c8c8c8;">
                      <td colspan="6" class="text-right" style="font-weight:bold;font-size:16px;">Net Amount (&#x20b9;)
                      </td>
                      <td style="font-weight: bold; text-align: right;" class="totala1">
                        <input style="text-align: right;" type="text" name="cost" class="form-control newtamso cost"
                          value="<?php echo sprintf('%.2f', $cost); ?>" readonly>
                      </td>
                      <td></td>
                      <td style="font-weight: bold; text-align: right;" class="totala2">
                        <input style="text-align: right;" type="text" name="tax" class="form-control newtamso tax"
                          value="<?php echo sprintf('%.2f', $tax); ?>" readonly>
                      </td>
                      <td style="font-weight: bold; text-align: right;" class="totala">
                        <input style="text-align: right;" type="text" name="amount" class="form-control newtamso amount"
                          value="<?php echo sprintf('%.2f', $amt); ?>" readonly>
                      </td>
                      <td></td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>


            <div class="row">

              <div class="col-sm-6">
                <label for="inputEmail3" style="margin-bottom:10px;">Payment Term<strong
                    style="color:red;"></strong></label>
                <?php echo $this->Form->input('payment_term', array('class' => 'form-control', 'type' => 'textarea', 'label' => false, 'placeholder' => 'Enter Payment Terms', 'autofocus', 'autocomplete' => 'off', 'readonly', '')); ?>
              </div>
              <div class="col-sm-6">
                <label for="inputEmail3" style="margin-bottom:10px;">Remark<strong style="color:red;"></strong></label>
                <?php echo $this->Form->input('remark', array('class' => 'form-control', 'id' => 'remark', 'type' => 'textarea', 'label' => false, 'placeholder' => 'Remark', 'autofocus', 'autocomplete' => 'off', 'readonly', '')); ?>
              </div>

            </div>
          </div>
          <!-- /.box-body -->
          <div class="box-footer">
            <?php
            if (isset($location['id'])) {
              echo $this->Form->submit(
                'Update',
                array('class' => 'btn btn-info pull-right', 'id' => 'formsubmitbtn', 'title' => 'Update')
              );
            } else {
              echo $this->Form->submit(
                'Revised && Finalize',
                array('class' => 'btn btn-info pull-right', 'id' => 'formsubmitbtn', 'title' => 'Revised && Finalize')
              );
            }
            ?>
            <?php
            echo $this->Html->link('Back', [
              'action' => 'index'

            ], ['class' => 'btn btn-default']); ?>
          </div>
          <!-- /.box-footer -->
          <?php echo $this->Form->end(); ?>
        </div>
      </div>
      <!--/.col (right) -->
    </div>
    <!-- /.row -->
  </section>
  <!-- /.content -->
</div>



<!-- <script>
  $(document).on('input', '.itemQtyCount, .itemUnitPrice, .itemTaxPercentage', function() {
    var $row = $(this).closest('tr');
    updateRowValues($row);
  });

  // Delete row and update totals
  $(document).on('click', '.delete-button', function() {
    $(this).closest('tr').remove();
    calculateTableFooterTotals();
  });

  function updateRowValues($row) {
    // Parse values safely, fallback to 0 if null, empty, or non-numeric
    var qty = parseFloat($row.find('.itemQtyCount').val()) || 0;
    var price = parseFloat($row.find('.itemUnitPrice').val()) || 0;

    var taxText = $row.find('.itemTaxPercentage option:selected').text().trim();
    var taxPercentage = parseFloat(taxText) || 0;

    // Calculate values
    var totalBase = qty * price;
    var taxAmount = (totalBase * taxPercentage) / 100;
    var totalWithTax = totalBase + taxAmount;

    // Update row values with formatDecimal applied
    $row.find('.totalBasePrice').val(formatDecimal(totalBase)).addClass('newtamo');
    $row.find('.newtaxx').val(formatDecimal(taxAmount));
    $row.find('.totalProductAmount').val(formatDecimal(totalWithTax)).addClass('newtamso');

    // Update footer totals
    calculateTableFooterTotals();
  }

  function formatDecimal(value) {
    value = parseFloat(value);
    return Number.isInteger(value) ?
      value.toString() // show as whole number, e.g., "100"
      :
      value.toFixed(2); // show with decimals, e.g., "100.25"
  }

  function formatCurrency(value) {
    value = parseFloat(value);
    if (Number.isInteger(value)) {
      return value.toLocaleString(); // no decimals
    } else {
      return value.toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      });
    }
  }

  // Unified total calculation function
  function calculateTableFooterTotals() {
    var totalAmountWithTax = 0;
    var totalTaxAmount = 0;
    var totalBaseAmount = 0;

    var $dataRows = $("#customers tr:not('.totalColumn, .titlerow, .titlerows')");

    $dataRows.each(function() {
      var totalProductAmountWithTaxes = parseFloat($(this).find('.totalProductAmount ').val()) || 0; // get the totalProductAmount
      var totalBasePrice = parseFloat($(this).find('.totalBasePrice').val()) || 0; // get the totalProductAmount
      var tax = parseFloat($(this).find('.newtaxx').val()) || 0; // Tax amount

      totalAmountWithTax += totalProductAmountWithTaxes;
      totalBaseAmount += totalBasePrice;
      totalTaxAmount += tax;
    });

    $('.totala').html(formatCurrency(totalAmountWithTax));
    $('.totala1').html(formatCurrency(totalBaseAmount));
    $('.totala2').html(formatCurrency(totalTaxAmount));
  }
</script> -->

<script>
  $(document).ready(function () {

    $('#datepicker1').datepicker({
      dateFormat: 'yy-mm-dd'
    });


  });
</script>
<script>
  function cllbckretail(id, cid) {
    $('.secrh-retail').val(id);
    $('#retail_ids').val(cid);
    var retail_id = $('#retail_ids').val();
    $('#testULs').show();
    $('#ship_id').val('');
    $.ajax({
      type: 'POST',
      url: '<?php echo ADMIN_URL; ?>purchaseorder/getvendorshipaddressall',
      data: {
        'retail_id': retail_id
      },
      success: function (data) {
        $('#testULs ul').html(data);
      },
    });
    $.ajax({
      type: 'POST',
      url: '<?php echo ADMIN_URL; ?>purchaseorder/getvendorissue',
      data: {
        'retail_id': retail_id
      },
      success: function (data) {
        $('.issuewithvendor').html('');
        $('.issuewithvendor').html(data);
      },
    });
    $('#testUL').hide();
  }

  function cllbckretails(id, cids) {
    $('.ship-retail').val(id);
    $('#ship_id').val(cids);
    $('#testULs').hide();
  }

  $(function () {
    $('.secrh-retail').bind('keyup', function () {
      $('#ship_id').val('');
      $('.ship-retail').val('');
      var pos = $(this).val();
      //alert(pos);
      $('#testUL').show();
      $('#retail_ids').val('');
      var count = pos.length;
      if (count > 0) {
        $.ajax({
          type: 'POST',
          url: '<?php echo ADMIN_URL; ?>purchaseorder/getvendorname',
          data: {
            'fetch': pos
          },
          success: function (data) {
            $('#testUL ul').html(data);
          },
        });
      } else {
        $('#testUL').hide();
      }
    });
  });
</script>
<script type="text/javascript">
  function testtt(retailID) {

    $.ajax({
      type: 'POST',
      url: '<?php echo ADMIN_URL; ?>purchaseorder/indentitems',
      data: {
        'fetch': retailID
      },
      success: function (data) {
        // console.log(data);
        $(".ctpcontent").css("display", "block");
        $("#product_containes").append(data); // Append received data to tbody
      },
    });
  }

  //item name
  function cllbckretail0(id, cid, sid) {
    $('.secrh-retails').val(id);
    $('#retail_id').val(cid);
    $('.retail_idsss').val(cid);
    $('#test1UL').hide();
    testtt(cid);
    $.ajax({
      type: 'POST',
      url: '<?php echo ADMIN_URL; ?>Purchaseorder/getitemdetail',
      data: {
        'fetch': cid
      },
      success: function (data) {
        $('.secrh-retails').val('');
        $('.secrh-retails').prop('required', false);
      },
    });
  }
  //get item name
  $(function () {
    $('.secrh-retails').bind('keyup', function () {
      var pos = $(this).val();
      var check = 0;
      $('#test1UL').show();
      $('#retail_id').val('');
      var count = pos.length;
      if (count > 0) {
        $.ajax({
          type: 'POST',
          url: '<?php echo ADMIN_URL; ?>Purchaseorder/getitemname',
          data: {
            'fetch': pos,
            'check': check
          },
          success: function (data) {
            $('#test1UL ul').html(data);

          },
        });
      } else {
        $('#test1UL').hide();
      }
    });
  });
</script>
<script>
  $(document).ready(function () {
    $('#sevice_form').on('submit', function (e) {
      $("#formsubmitbtn").css("display", "none");
    });
  });
</script>

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
  $('.newquan').change(function () {
    let a = <?php echo $i; ?> - 1;
    let totamt = 0;
    let tottax = 0;

    for (let i = 1; i <= a; i++) {
      totamt += parseFloat($('#pitemamount' + i).val());
    }

    for (let i = 1; i <= a; i++) {
      tottax += parseFloat($('#pitax' + i).val());
    }

    $('.cost').val(totamt.toFixed(2));
    $('.tax').val(tottax.toFixed(2));
    subamt = totamt + tottax;
    $('.amount').val(subamt.toFixed(2));

  });
</script>

<script>
  function checkQuantity(input, minValue) {
    var enteredValue = parseFloat(input.value);
    if (enteredValue < minValue) {
      alert("Quantity Can't be less than " + minValue);
      input.value = '';
    }
  }
</script>






<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>