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
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
  <h1>
      Purchase Order Manager
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo SITE_URL; ?>admin/quotation/view_received_quotation"><i class="fa fa-home"></i>View Bids</a></li>
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

            <div class="form-group" style="margin-bottom:0px;">

              <div class="row">
                <!--Enter PO Number  -->
                <div class="col-sm-3">
                  <label for="inputEmail3" class=" control-label">PO NO <strong style="color:red;">*</strong> </label>
                  <?php
                  echo $this->Form->input('pono', array('class' => 'form-control contactnum mobile', 'value' => $newpurchaseordertemp, 'required', 'autocomplete' => 'off', 'id' => 'dup_mobile', 'placeholder' => 'Enter PO Number', 'readonly', 'label' => false));
                  ?>
                </div>

                <div class="col-sm-3">
                  <label for="inputEmail3" class="">Generated Date <strong style="color:red;">*</strong></label>
                  <?php echo $this->Form->input('inwarddate', array('class' => 'form-control', 'id' => 'datepicker3', 'type' => 'text', 'readonly', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'value' => date('d-m-Y', strtotime($quotationReceived['quotation_date'])), 'required')); ?>
                </div>

                <div class="col-sm-3">
                  <label for="inputEmail3" class=" control-label">Supplier <strong style="color:red;">*</strong></label>
                  <input type="hidden" name="vendor_id" id="retail_ids" value="<?php echo $quotationReceived['vendor_id'] ?>">
                  <?php echo $this->Form->input('vendorname', array('class' => 'form-control secrh-retail', 'id' => 'supplier', 'type' => 'text', 'label' => false, 'autofocus', 'readonly', 'autocomplete' => 'off', 'required', 'placeholder' => 'Enter Supplier Name', 'value' => $vendorname['name'],)); ?>
                  <div id="testUL">
                    <ul></ul>
                  </div>
                </div>

                <div class="col-sm-3">
                  <label for="inputEmail3" class=" control-label">Expected Delivery Date<strong style="color:red;">*</strong></label>
                  <?php echo $this->Form->input('delivery_date', array('class' => 'form-control ', 'id' => '', 'readonly', 'type' => 'text', 'label' => false, 'placeholder' => 'Expected Delivery Date', 'autofocus', 'value' => date('d-m-Y', strtotime($quotationReceived['delivery_date'])), 'autocomplete' => 'off', 'required')); ?>
                </div>

              </div>
            </div>





            <div class="ctpcontent form-group" style="display:block">
              <div class="col-sm-12">
                <label for="inputEmail3" style="margin-bottom:10px;">Items</label>
                <table id="customers">
                  <thead>
                  <tr>
                      <th colspan="8" style="background-color: #9c9999;text-align:center;font-size: 14px !important;"><b>Quotation Details </b></th>
                      <th colspan="4" style="background-color: #c8c8c8;text-align:center;font-size: 14px !important;"><b>Bid Details </b></th>
                    </tr>
                    <tr class="totalColumn">
                      <th style="background-color: #9c9999; color:white;">Item</th>
                      <th style="background-color: #9c9999; color:white;">Qty</th>
                      <th style="background-color: #9c9999; color:white;">UOM</th>
                      <th style="background-color: #9c9999; color:white;">Unit Price</th>
                      <th style="background-color: #9c9999; color:white;">Total Price</th>
                      <th style="background-color: #9c9999; color:white;">Tax Rate</th>
                      <th style="background-color: #9c9999; color:white;">Tax Amount</th>
                      <th style="background-color: #9c9999; color:white;">Total Amount</th>
                      <th style="background-color: #c8c8c8;">Bid Unit Price</th>
                      <th style="background-color: #c8c8c8;">Bid Total Price</th>
                      <th style="background-color: #c8c8c8;">Bid Tax Amount</th>
                      <th style="background-color: #c8c8c8;">Bid Total</th>
                    </tr>
                  </thead>
                  <tbody id="product_containes">
                    <?php
                    $i = 1;
                    foreach ($quotationReceivedDetails as $value) {

                      $getitemname = $this->Comman->getitemname($value['item_id']);
                      $gettaxname = $this->Comman->gettaxname($value['tax_id']);

                      $qty = $this->Comman->stockregisteritems($value['purchaseorder_id'], $value['item_id']);
                      $result = ['sum' => round($qty->sum, 2)];
                      $qsum = $result['sum'];
                    ?>


                      <tr class="video_details">
                        <td width="17%">
                          <?php echo $this->Form->input('pitemname[]', array('class' => 'form-control', 'id' => 'pitemname', 'type' => 'hidden', 'value' => $value['item_id'], 'label' => false, 'autofocus', 'autocomplete' => 'off')); ?>

                          <?php echo $this->Form->input('name[]', array('class' => 'form-control', 'id' => 'pitemname', 'type' => 'text', 'value' => $getitemname['item_name'], 'label' => false, 'autofocus', 'autocomplete' => 'off', 'readonly')); ?>
                          <small style="font-weight:bold;position: relative;"><span style="color:red;"></span></small>

                        </td>

                        <td width="8%">
                          <input type="text" onkeypress='return isNumberKey(event)' name="pitemquantity[]"
                            class="form-control quantity quntt<?php echo $i; ?>" id="quantity_<?php echo $i; ?>" autocomplete='off' required
                            min="<?php echo $qsum; ?>" value="<?php echo ($value['item_qty']); ?>">
                        </td>

                        <td width="6%">
                          <?php echo $this->Form->input('unit_name[]', array('class' => 'form-control', 'type' => 'text', 'value' => $value['uom'], 'label' => false, 'autofocus', 'autocomplete' => 'off', 'readonly')); ?>
                        </td>





                        <td width="8%">
                          <input style="text-align: right;" type="text" readonly maxlength="10"
                            name="pitemrate[]" class="form-control unit_price newpitra pitraa<?php echo $i; ?>"
                            id="unitprice_<?php echo $i; ?>" autocomplete="off" value="<?php echo ($value['item_price']); ?>">
                        </td>

                        <?php $costprice = $value['item_qty'] * $value['item_price'];  ?>
                        <td width="8%">
                          <input style="text-align: right;" type="text" name="pitemamount[]"
                            class="form-control itemcost pitama<?php echo $i; ?>" id="itemcost_<?php echo $i ?>"
                            value="<?php echo $costprice; ?>" readonly>
                        </td>

                        <td width="6%">
                          <input type="text" name="tax_value[]" class="form-control " id="taxrate_<?php echo $i; ?>" value="<?php echo ($gettaxname['tax'] != '') ? $gettaxname['tax'] : 0; ?> " readonly>
                          <input type="hidden" name="tax_id[]" value="<?php echo $value['tax_id']; ?>">
                        </td>


                        <td width="6%">
                          <input style="text-align: right;" type="text" name="pitemtax[]" value="<?php echo $value['item_tax_amt']; ?>"
                            class="form-control  taxamount" id="taxamount_<?php echo $i; ?>" readonly>
                        </td>

                        <td width="8%">
                          <input style="text-align: right;" type="text" name="totalamount[]" readonly
                            class="form-control newtamso totalamount" id="totalamount_<?php echo $i; ?>"
                            value="<?php echo ($value['item_total_amount']); ?>">
                        </td>





                        <td width="8%">
                          <input style="text-align: right;" type="text" name="bid_unit_price[]"
                            class="form-control bid_unit_price pitama<?php echo $i; ?>" id="bidunitprice_<?php echo $i ?>"
                            value="<?php echo ($value['item_price_bid']); ?>" autocomplete="off" readonly>
                        </td>
                        <td width="8%">
                          <input style="text-align: right;" type="text" name="bid_total_price[]"
                            class="form-control bidtotalprice pitama<?php echo $i; ?>" id="bidtotalprice_<?php echo $i ?>"
                            value="<?php echo $value['item_amt_bid']; ?>" readonly>
                        </td>
                        <td width="8%">
                          <input style="text-align: right;" type="text" name="bid_tax[]"
                            class="form-control bidtax pitama<?php echo $i; ?>" id="bidtax_<?php echo $i ?>"
                            value="<?php echo $value['item_tax_amt_bid']; ?>" readonly>
                        </td>
                        <td width="8%">
                          <input style="text-align: right;" type="text" name="bid_total_amount[]"
                            class="form-control bidAmount pitama<?php echo $i; ?>" id="bidtotalamount_<?php echo $i ?>"
                            value="<?php echo ($value['item_total_amount_bid']); ?>" readonly>
                        </td>
                      </tr>


                    <?php $i++;
                      $cost += $costprice;
                      $bidCost += $value['item_amt_bid'];
                      $tax += $value['item_tax_amt'];
                      $amt += $value['item_total_amount'];
                    } ?>
                  </tbody>

                  <tfoot>
                    <tr class="titlerows"></tr>

                    <tr class="titlerow">
                      <td colspan="4" class="text-right" style="font-weight:bold;font-size:16px;background-color: #9c9999;color:white;">Net Amount (&#x20b9;) </td>
                      <td style="font-weight: bold; text-align: right;background-color: #9c9999;" class="totala1">
                        <input style="text-align: right;" type="text" name="cost" class="form-control newtamso totalcost"
                          value="<?php echo ($cost); ?>" readonly>
                      </td>
                      <td style="background-color: #9c9999;"></td>
                      <td style="font-weight: bold; text-align: right;background-color: #9c9999;" class="totala2">
                        <input style="text-align: right;" type="text" name="tax" class="form-control totalTax tax"
                          value="<?php echo ($tax); ?>" readonly>
                      </td>
                      <td style="font-weight: bold; text-align: right;background-color: #9c9999;" class="totala">
                        <input style="text-align: right;" type="text" name="amount" class="form-control totalItemAmount amount"
                          value="<?php echo ($amt); ?>" readonly>
                      </td>

                      <td style="background-color: #c8c8c8;"></td>
                      <td style="font-weight: bold; text-align: right;background-color: #c8c8c8;" class="bidTotal1">
                        <input style="text-align: right;" type="text" name="bidCost" class="form-control newtamso bidCostTotal"
                          value="<?php echo ($bidCost); ?>" readonly>
                      </td>
                      <td style="font-weight: bold; text-align: right;background-color: #c8c8c8;" class="bidTotal2">
                        <input style="text-align: right;" type="text" name="bidTax" class="form-control newtamso bidTaxTotal"
                          value="<?php echo ($quotationReceived['total_tax_bid']); ?>" readonly>
                      </td>
                      <td style="font-weight: bold; text-align: right;background-color: #c8c8c8;" class="bidTotal">
                        <input style="text-align: right;" type="text" name="bidAmount" class="form-control newtamso bidAmountTotal"
                          value="<?php echo ($quotationReceived['total_amt_bid']); ?>" readonly>
                      </td>

                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>



            <div class="form-group">
              <div class="row">
                <div class="col-sm-6">
                  <label for="inputEmail3" style="margin-bottom:10px;">Payment Term<strong style="color:red;"></strong></label>
                  <?php echo $this->Form->input('payment_terms', array('class' => 'form-control', 'id' => '', 'type' => 'textarea', 'label' => false, 'placeholder' => 'Payment Term', 'autofocus', 'autocomplete' => 'off', 'rows' => 3)); ?>
                </div>
                <div class="col-sm-6">
                  <label for="inputEmail3" style="margin-bottom:10px;">Remark<strong style="color:red;"></strong></label>
                  <?php echo $this->Form->input('remark', array('class' => 'form-control', 'id' => 'remark', 'type' => 'textarea', 'label' => false, 'placeholder' => 'Remark', 'autofocus', 'autocomplete' => 'off', 'rows' => 3)); ?>
                </div>
              </div>
            </div>
          </div>
          <!-- /.box-body -->
          <div class="box-footer">
            <?php echo $this->Form->submit('Save && Finalize', array('class' => 'btn btn-info pull-right', 'id' => 'formsubmitbtn', 'title' => 'Save && Finalize')); ?>
            <?php echo $this->Html->link('Back', ['action' => 'index'], ['class' => 'btn btn-default']); ?>
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

<script>
  $(document).ready(function() {
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
      success: function(data) {
        $('#testULs ul').html(data);
      },
    });
    $.ajax({
      type: 'POST',
      url: '<?php echo ADMIN_URL; ?>purchaseorder/getvendorissue',
      data: {
        'retail_id': retail_id
      },
      success: function(data) {
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

  $(function() {
    $('.secrh-retail').bind('keyup', function() {
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
          success: function(data) {
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
      success: function(data) {
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
      success: function(data) {
        $('.secrh-retails').val('');
        $('.secrh-retails').prop('required', false);
      },
    });
  }
  //get item name
  $(function() {
    $('.secrh-retails').bind('keyup', function() {
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
          success: function(data) {
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
  $(document).ready(function() {
    $('#sevice_form').on('submit', function(e) {
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
  function totalcalculation() {
    var totalCost = 0,
      totalTax = 0,
      totalItemAmount = 0;
    var bidCostTotal = 0,
      bidTaxTotal = 0,
      bidAmountTotal = 0;

    $('.itemcost').each(function() {
      totalCost += parseFloat($(this).val()) || 0;
    });
    $('.taxamount').each(function() {
      totalTax += parseFloat($(this).val()) || 0;
    });
    $('.totalamount').each(function() {
      totalItemAmount += parseFloat($(this).val()) || 0;
    });

    $('.bidtotalprice').each(function() {
      bidCostTotal += parseFloat($(this).val()) || 0;
    });
    $('.bidtax').each(function() {
      bidTaxTotal += parseFloat($(this).val()) || 0;
    });
    $('.bidAmount').each(function() {
      bidAmountTotal += parseFloat($(this).val()) || 0;
    });

    $('.totalcost').val(totalCost);
    $('.totalTax').val(totalTax);
    $('.totalItemAmount').val(totalItemAmount);
    $('.bidCostTotal').val(bidCostTotal);
    $('.bidTaxTotal').val(bidTaxTotal);
    $('.bidAmountTotal').val(bidAmountTotal);
  }

  $('.quantity').on('input', function() {
    var inputId = $(this).attr('id');
    var parts = inputId.split('_');
    var idCount = parts[1];

    var quantity = parseFloat($(this).val()) || 0;
    var taxRate = parseFloat($(`#taxrate_${idCount}`).val()) || 0;
    var itemPrice = parseFloat($(`#unitprice_${idCount}`).val()) || 0;
    var bidPrice = parseFloat($(`#bidunitprice_${idCount}`).val()) || 0;

    var cost = quantity * itemPrice;
    var bidCost = quantity * bidPrice;
    var taxAmount = cost * (taxRate / 100);
    var bidTaxAmount = bidCost * (taxRate / 100);
    var totalAmount = taxAmount + cost;
    var bidTotalAmount = bidTaxAmount + bidCost;

    $(`#itemcost_${idCount}`).val(cost);
    $(`#taxamount_${idCount}`).val(taxAmount);
    $(`#totalamount_${idCount}`).val(totalAmount);
    $(`#bidtotalprice_${idCount}`).val(bidCost);
    $(`#bidtax_${idCount}`).val(bidTaxAmount);
    $(`#bidtotalamount_${idCount}`).val(bidTotalAmount);

    totalcalculation(); // Call function to update totals
  });
</script>






<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>