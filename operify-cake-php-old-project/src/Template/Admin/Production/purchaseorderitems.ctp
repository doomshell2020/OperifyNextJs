<?php $i = 1;
$role_id = $this->request->session()->read('Auth.User.role_id');


foreach ($stockitems as $key => $value) { 

  // pr($value);die;

  $delivery_not_qty = $this->Comman->DelivernoteQty($value['item_id'], $value['purchaseorder_id']);
  // pr($delivery_not_qty);exit;
 


  $tquant += $value['item_qty'];
  $tamount += $value['amount'];
  $tax_find = $value['taxmaster']['tax'];
  $tax_amount = $value['item_tax_amt'];
  $tax_key = $value['taxmaster']['id'];
  $lprcost = $this->Comman->lprcost($value['item_id']);
  if ($lprcost == "") {
    $lprcost = 0;
  }
  // pr($tquant);

  $unitname = $this->Comman->getunitnamepoview($value['additem']['unit_id']);

  $InhandStock = $this->Comman->InhandStock($value['item_id']);

?>

  <?php $qty = $this->Comman->stockregisteritems($value['purchaseorder_id'], $value['item_id']);
  $result = ['sum' => round($qty->sum, 2)];
  // pr($result);

  $qsum =  $tquant - $result['sum'];
  $defaultqty = 0;
  $grn_qty = ($delivery_not_qty['item_qty']) ? $delivery_not_qty['item_qty'] : $defaultqty; ?>
  <tr class="video_details">


    <input type="hidden" name="po_item_qty" class="poItemQty<?php echo $i; ?>" value="<?php echo $value['item_qty']; ?>">

    <td width="20%"><?php echo $this->Form->input('indent_id[]', array('class' => 'form-control', 'id' => 'indentid', 'type' => 'hidden', 'value' => $value['indent_id'], 'label' => false)); ?>

      <?php echo $this->Form->input('pitemname[]', array('class' => 'form-control', 'id' => 'pitemname', 'type' => 'hidden', 'value' => $value['item_id'], 'label' => false, 'autofocus', 'autocomplete' => 'off')); ?>

      <?php $getsize = $this->Comman->getsizename($value['additem']['size_id']);

      if ($value['additem']['size_id'] == 6) {
        echo $this->Form->input('name[]', array('class' => 'form-control', 'id' => 'pitemname', 'type' => 'text', 'value' => $value['additem']['item_name'], 'label' => false, 'autofocus', 'autocomplete' => 'off', 'readonly'));
      } else {
        echo $this->Form->input('name[]', array('class' => 'form-control', 'id' => 'pitemname', 'type' => 'text', 'value' => $value['additem']['item_name'] . "-" . $getsize['size_name'], 'label' => false, 'autofocus', 'autocomplete' => 'off', 'readonly'));
      } ?>
    </td>
    <td width="8%"><input type="text" class="form-control" readonly="readonly" value="<?php  echo $value['item_qty']; ?>"></td>

    <input type="hidden" name= "pendingqty[]" class="form-control" readonly="readonly" value="<?php  echo $result['sum']; ?>">

    <!-- <?php if ($role_id == 105) { ?>
      <td width="15%"><input type="text" name="pitemquantity[]" autocomplete = 'off' class="form-control newquan quntt<?php echo $i; ?>" id="quan" min="0" max="<?php echo $qsum; ?>" onkeyup="checkQuantity(this, <?php echo $qsum; ?>)" onblur="checkQuantity(this, <?php echo $qsum; ?>)" value="<?php echo $grn_qty; ?>"></td>
    <?php } else { ?>
      <td width="8%"><input type="text" name="pitemquantity[]" autocomplete = 'off' class="form-control newquan quntt<?php echo $i; ?>" id="quan" min="0" max="<?php echo $qsum; ?>" onkeyup="checkQuantity(this, <?php echo $qsum; ?>)" onblur="checkQuantity(this, <?php echo $qsum; ?>)" value="<?php echo $grn_qty; ?>" readonly></td>
    <?php } ?> -->

    <?php if ($role_id == 105) { ?>
      <td width="10%"><input type="text" name="pitemquantity[]" autocomplete = 'off' class="form-control newquan quntt<?php echo $i; ?>" id="quan" min="0" max="<?php echo $qsum; ?>" onkeyup="checkQuantity(this, <?php echo $qsum; ?>)" onblur="checkQuantity(this, <?php echo $qsum; ?>)" value="<?php echo $grn_qty; ?>"></td>
    <?php } else { ?>
      <td width="8%"><input type="text" name="pitemquantity[]" autocomplete = 'off' class="form-control newquan quntt<?php echo $i; ?>" id="quan" min="0" max="<?php echo $qsum; ?>" onkeyup="checkQuantity(this, <?php echo $qsum; ?>)" onblur="checkQuantity(this, <?php echo $qsum; ?>)" value="<?php echo $grn_qty; ?>" readonly></td>
    <?php } ?>

    <td width="6%">
      <?php
      echo $this->Form->input('unit_name[]', array('class' => 'form-control', 'type' => 'text', 'value' => isset($value['uom']) ? $value['uom'] : "--", 'label' => false, 'autofocus', 'autocomplete' => 'off', 'readonly')); ?>
    </td>

    <td width="6%">
      <span><?php echo $InhandStock['sum']; ?> </span>
    </td>


    <td width="10%"><input style="text-align: right;" type="text" name="pitemrate[]" class="form-control filterme newpitra pitra<?php echo $i; ?>" id="pitemrate" value="<?php echo number_format((float)$value['item_amt'], 2, '.', ''); ?>" readonly></td>

    


    <td width="11%">
      <input style="text-align: right;" type="text" name="pitemamount[]" class="form-control newtamo pitama<?php echo $i; ?>" id="pitemamount" value="<?php echo $value['cost_price']; ?>" readonly>
    </td>

    <td width="9%">


      <input type="text" name="tax_value[]" class="form-control" value="<?php echo $tax_find;  ?>" readonly>
      <input type="hidden" name="tax_id[]" class="form-control taxamount<?php echo $i; ?>" value="<?php echo $tax_key;  ?>" readonly>

    </td>
    <td width="8%" align="right">

      <span class="totaltax totaltax<?php echo $i; ?> value-span"></span>
      <input type="hidden" name="pitemtax[]" class="form-control update_taxAmt<?php echo $i; ?> newtaxx pitax<?php echo $i; ?>" id="pitax" value="" readonly>
    </td>
    <td width="20%" align="right">
      <input type="hidden" readonly  name="totalamount[]" class="form-control totalamount<?php echo $i; ?>" id="totalamount" value="<?php echo $value['amount']; ?>">
      <span class="newtamso pitamas<?php echo $i; ?> value-totalmt"><?php echo $value['amount']; ?></span>

    </td>
  </tr>


  <script>
    $(document).ready(function() {

      // var freight = '<?php // echo $purchaseorder['freight']; 
                        ?>';
      // $('#freight').val(00);
      // var total_tax = '<?php // echo $purchaseorder['total_tax']; 
                          ?>';
      // $('.totala2').text(total_tax);
      var vendor_id = '<?php echo $purchaseorder['vendor_id']; ?>';
      $('#vendor_id').val(vendor_id);
      var estimateddevlierydate = '<?php echo date('d-m-Y', strtotime($purchaseorder['delivery_date'])); ?>';
      $('#estimateddevlierydate').text("Estimated Delivery Date is:-" + estimateddevlierydate);
    });

    function total() {

      var totals = 0;
      var $dataRows = $("#customers tr:not('.totalColumn, .titlerow, .titlerows')");
      $dataRows.each(function() {
        $(this).find('.newtamso').each(function(i) {
          totals += parseFloat($(this).html());
        });
      });
      if ($('#freight').val() != '') {
        totals += parseFloat($('#freight').val());
      }
      $('.totala').html(totals.toFixed(2));
      $('.tqty').val(totals.toFixed(2));

    }

    function total2() {
      var totals2 = 0;
      var $dataRows = $("#customers tr:not('.totalColumn, .titlerow, .titlerows')");
      $dataRows.each(function() {
        $(this).find('.totaltax').each(function(i) {
          totals2 += parseFloat($(this).text());
        });
      });
      $('.totala2').html(totals2.toFixed(2));

    }

    function total3() {
      var totals3 = 0;
      var $dataRows = $("#customers tr:not('.totalColumn, .titlerow, .titlerows')");
      $dataRows.each(function() {
        $(this).find('.newtamo').each(function(i) {
          totals3 += parseFloat($(this).val());
        });
      });
      $('.totala1').html(totals3.toFixed(2));

    }

    
      // Function to calculate and update values based on quantity
      // let totalFtaxAmt = 0;
      // let Tottal = 0;

      // function calculateAndUpdateValues(i) {
      //   let finel_qty_base_taxAmt = 0;

      //   let quat = parseFloat($('.quntt' + i).val());
      //   let POItemQty = parseFloat($('.poItemQty' + i).val());
      //   let pitra = parseFloat($('.pitra' + i).val());
      //   total_tax = '<?php echo $tax_find; ?>'

      //   let tcrate = pitra * quat;
      //   let total_taxss = tcrate*total_tax / 100;
     
      //   // let tax_cal = total_tax / POItemQty;
      //   finel_qty_base_taxAmt += total_taxss;

      //   let total_AMT = finel_qty_base_taxAmt+tcrate;
       

      //   $('.pitama' + i).val(tcrate.toFixed(2));
      //   // $('.pitamas' + i).text(tcrate.toFixed(2));
      //   // $('.totalamount' + i).val(tcrate.toFixed(2));
      //   $('.pitamas' + i).text(total_AMT.toFixed(2));
      //  $('.totalamount' + i).val(total_AMT.toFixed(2));
      //   $('.totaltax' + i).text(finel_qty_base_taxAmt.toFixed(2));
      //   $('.update_taxAmt' + i).val(finel_qty_base_taxAmt.toFixed(2));
    

      // }


$(document).ready(function() {
    function calculateAndUpdateValues(i) {
        let quat = parseFloat($('.quntt' + i).val());
        let POItemQty = parseFloat($('.poItemQty' + i).val());
        let pitra = parseFloat($('.pitra' + i).val());
        total_tax = parseFloat('<?php echo $tax_find; ?>');
        totalamt = parseFloat('<?php echo $tax_amount; ?>');
        totalqty = parseFloat('<?php echo $tquant; ?>');


        let totalcost = pitra * totalqty;
        
        let tcrate = pitra * quat;
        let totax = (totalcost * total_tax) / 100;
        let applytax = (tcrate * total_tax) / 100;
        let total_taxss, total_AMT;
        // alert(totalcost);

        if (totalamt == totax) {
            total_taxss = applytax;
            total_AMT = tcrate + total_taxss;
        } else {
            total_taxss = total_tax * (tcrate / (100 + total_tax));
            total_AMT = tcrate;
        }

        $('.pitama' + i).val(tcrate.toFixed(2));
        $('.pitamas' + i).text(total_AMT.toFixed(2));
        $('.totalamount' + i).val(total_AMT.toFixed(2));
        $('.totaltax' + i).text(total_taxss.toFixed(2));
        $('.update_taxAmt' + i).val(total_taxss.toFixed(2));
    }

    // Event handler for the change event on quantity elements
    $('.quntt<?php echo $i; ?>').on('change', function() {
        calculateAndUpdateValues(<?php echo $i; ?>);
        total();
        total3();
        total2();
        // $('.totala2').text(Tottal.toFixed(2));
    });

    // Initial calculation
    calculateAndUpdateValues(<?php echo $i; ?>);
    total();
    total3();
    total2();
});

  </script>

<?php $i++;
  // }
  $tquant = '';
} ?>
<script>
  function checkQuantity(input, maxValue) {
    var enteredValue = parseFloat(input.value);
    if (enteredValue > maxValue) {
      alert("Quantity Can't be greater than " + maxValue);
      input.value = 0;
    }
  }
</script>