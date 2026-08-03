<div class="row">
  <div class="col-sm-3" style="margin-bottom:15px;">
    <label for="inputEmail3" class="">Inward Date</label>

    <?php echo $this->Form->input('inwarddate', array('class' => 'form-control', 'label' => false, 'value' => date('d-m-Y', strtotime($purchaseorder['inwarddate'])), 'readonly')); ?>
  </div>
  <div class="col-sm-3" style="margin-bottom:15px;">
    <?php echo $this->Form->input('bill_no', array('class' => 'form-control', 'value' => $purchaseorder['bill_no'], 'readonly')); ?>
  </div>
  <script>
    $(document).ready(function() {
      $(".readonly").on('keyup', function(e) {
        $(".readonly").val('');
      });
    });
  </script>
  <div class="col-sm-3" style="margin-bottom:15px;">
    <?php echo $this->Form->input('bill_date', array('class' => 'form-control', 'value' => date('d-m-Y', strtotime($purchaseorder['inwarddate'])), 'readonly')); ?>
  </div>

  <input type="hidden" name="purchaseorder_id" class="form-control" readonly="readonly"
    value="<?php echo $purchaseorder['po_id']; ?>">

  <input type="hidden" name="vendor_id" class="form-control" readonly="readonly"
    value="<?php echo $purchaseorder['vendor_id']; ?>">
</div>



<div class="form-group">
  <label for="inputEmail3" class="col-sm-2">Items<strong style="color:red;">*</strong></label>
  <div class="col-sm-12">
    <table id="customers">
      <thead>
        <tr class="totalColumn">
          <th colspan="2">Item</th>
          <th>Received Qty</th>
          <th>Unit Price</th>
          <th>Total Price</th>
          <th>Tax Rate</th>
          <th>Tax Amount</th>
          <th>Total Amount</th>

        </tr>
      </thead>
      <tbody class="product_containes" id="product_containes">
      </tbody>
      <tfoot>

        <?php $i = 1;
        $role_id = $this->request->session()->read('Auth.User.role_id');

        foreach ($stockitems as $key => $value) {
          $delivery_not_qty = $this->Comman->DelivernoteQty($value['item_id'], $value['purchaseorder_id']);

          $tquant += $value['item_qty'];
          $tamount += $value['amount'];
          $tax_find = $value['taxmaster']['tax'];
          $tax_amount = $value['item_tax_amt'];
          $tax_key = $value['taxmaster']['id'];
          $lprcost = $this->Comman->lprcost($value['item_id']);
          if ($lprcost == "") {
            $lprcost = 0;
          }

          $unitname = $this->Comman->getunitnamepoview($value['additem']['unit_id']);
          $InhandStock = $this->Comman->InhandStock($value['item_id']);
        ?>
          <?php $qty = $this->Comman->stockregisteritems($value['purchaseorder_id'], $value['item_id']);
          $result = ['sum' => round($qty->sum, 2)];

          $qsum = $tquant - $result['sum'];
          $defaultqty = 0;
          $grn_qty = ($delivery_not_qty['item_qty']) ? $delivery_not_qty['item_qty'] : $defaultqty; ?>

          <tr class="video_details">
            <input type="hidden" name="po_item_qty" class="poItemQty<?php echo $i; ?>" value="<?php echo $value['item_qty']; ?>">
  <input type="hidden" name="delivery_schedule_id[]" value="<?php echo  $delivery_not_qty['id']; ?>">

            <td colspan="2" width="30%">
              <?php echo $this->Form->input('indent_id[]', array('class' => 'form-control', 'id' => 'indentid', 'type' => 'hidden', 'value' => $value['indent_id'], 'label' => false)); ?>

              <?php echo $this->Form->input('pitemname[]', array('class' => 'form-control', 'id' => 'pitemname', 'type' => 'hidden', 'value' => $value['item_id'], 'label' => false, 'autofocus', 'autocomplete' => 'off')); ?>

              <?php $getsize = $this->Comman->getsizename($value['additem']['size_id']);
              if ($value['additem']['size_id'] == 6) {
                echo $this->Form->input('name[]', array('class' => 'form-control', 'id' => 'pitemname', 'type' => 'text', 'value' => $value['additem']['item_name'], 'label' => false, 'autofocus', 'autocomplete' => 'off', 'readonly'));
              } else {
                echo $this->Form->input('name[]', array('class' => 'form-control', 'id' => 'pitemname', 'type' => 'text', 'value' => $value['additem']['item_name'] . "-" . $getsize['size_name'], 'label' => false, 'autofocus', 'autocomplete' => 'off', 'readonly'));
              } ?>
            </td>




            <input type="hidden" name="pendingqty[]" class="form-control" readonly="readonly"
              value="<?php echo $result['sum']; ?>">


            <td width="10%"><input type="text" name="pitemquantity[]" autocomplete='off'
                class="form-control newquan quntt<?php echo $i; ?>" id="quan" min="0" max="<?php echo $qsum; ?>"
                onkeyup="checkQuantity(this, <?php echo $qsum; ?>)" onblur="checkQuantity(this, <?php echo $qsum; ?>)"
                value="<?php echo $value['quantity']; ?>" readonly></td>



            <td width="10%"><input style="text-align: right;" type="text" name="pitemrate[]"
                class="form-control filterme newpitra pitra<?php echo $i; ?>" id="pitemrate"
                value="<?php echo number_format((float) $value['rate'], 2, '.', ''); ?>" readonly></td>


            <td width="11%">
              <input style="text-align: right;" type="text" name="pitemamount[]"
                class="form-control newtamo pitama<?php echo $i; ?>" id="pitemamount" value="<?php echo $value['cost_price']; ?>"
                readonly>
            </td>

            <td width="9%">
              <input type="text" name="tax_value[]" class="form-control" value="<?php echo ($tax_find) ? $tax_find : 0; ?>" readonly>

              <input type="hidden" name="tax_id[]" class="form-control taxamount<?php echo $i; ?>" value="<?php echo $value['tax_id']; ?>"
                readonly>
            </td>

            <td width="8%" align="right">
              <span class="totaltax totaltax<?php echo $i; ?> value-span"></span>
              <input type="text" name="pitemtax[]" class="form-control update_taxAmt<?php echo $i; ?> newtaxx pitax<?php echo $i; ?>" id="pitax" value="<?php echo $value['tax']; ?>" readonly>
            </td>

            <td width="20%" align="right">
              <input type="hidden" readonly name="totalamount[]" class="form-control totalamount<?php echo $i; ?>"
                id="totalamount" value="<?php echo $value['amount']; ?>">
              <span class="newtamso pitamas<?php echo $i; ?> value-totalmt">
                <?php echo $value['amount']; ?>
              </span>

            </td>
          </tr>

        <?php
          $totalAmount +=  $value['amount'];
        } ?>



        <tr class="titlerow" style="background-color: #c8c8c8;">
          <td colspan="6" class="text-right" style="font-weight:bold;font-size:16px;">Net Amount
            (&#x20b9;)</td>
          <td class="totala2" style="text-align: right;"></td>
          <td class="totala" style="text-align: right;"><?php echo $totalAmount ?></td>
          <input type="hidden" name="tqty" class="tqty" value="<?php echo $totalAmount ?>">
        </tr>


      </tfoot>
    </table>
  </div>
</div>

<div class="form-group">
  <div class="col-sm-12">
    <label for="inputEmail3">Remark</label> <strong style="color:red;">*</strong>
    <?php echo $this->Form->input('remark', array('class' => 'form-control', 'id' => 'remark', 'type' => 'textarea', 'label' => false, 'placeholder' => 'Remark', 'autofocus', 'autocomplete' => 'off', 'required')); ?>
  </div>
</div>