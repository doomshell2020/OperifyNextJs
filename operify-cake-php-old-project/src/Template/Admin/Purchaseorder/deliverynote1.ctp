<style>
  h4 {
    margin-top: 0px !important;
    margin-bottom: 0px !important;
  }

  table,
  tr,
  th,
  td {
    border: 1px solid black;
    margin-top: 10px;
  }
</style>

<div class="modal-header" style="background:#3399CC;">
  <h4>Delivery Note</h4>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>


<?php echo $this->Form->create(
  $enquires,
  array(
    'url' => array('controller' => 'Purchaseorder', 'action' => 'deliverynote'),
    'class' => '',
    'id' => 'sevice_form',
    'enctype' => 'multipart/form-data',
    'validate',
    'autocomplete' => 'off'

  )
); ?>

<div class="modal-body prchc_ord_popup">

  <div class="row">
    <div class="col-sm-6 align-self-center">
      <label><b>PO Id</b></label>
      <?php echo $this->Form->input('po_id', array('class' => 'form-control', 'type' => 'text', 'value' => $id, 'label' => false, 'readonly')); ?>
    </div>
    <div class="col-sm-6 align-self-center">
      <label><b>Delivery Date</b></label>
    <div class="col-sm-6 align-self-center">
    <input type="date" class="" id="css" name="delivery_date" required="required">
      <?php // echo $this->Form->input('delivery_date', array('class' => 'form-control', 'id' => 'datepicker1', 'readonly' => 'readonly', 'type' => 'text', 'label' => false, 'placeholder' => 'Expected Delivery Date', 'autofocus', 'autocomplete' => 'off', 'required' => 'required', 'readonly')); ?>
    </div>
    </div>

    <div class="col-sm-12 align-self-center">
      <table class="table table-bordered table-striped">
        <thead class="thead-dark">
          <tr>
            <th style="width: 70%;">Name</th>
            <th style="width: 15%;">Order Qty</th>
            <th style="width: 15%;">Required Qty</th>
          </tr>
        </thead>
        <tbody>
          <?php if (count($PurchaseOrderDetails) > 0) {
            $cnt = 1; ?>
            <?php foreach ($PurchaseOrderDetails as $val) {
              $pending_qty = $this->Comman->CheckdeliverynoteQty($val['purchaseorder_id'], $val['item_id']);
              $qty = $this->Comman->stockregisteritems($val['purchaseorder_id'], $val['item_id']);
              $remning = $val['item_qty'] - $pending_qty['item_qty'] - $qty['sum']; ?>

              <tr>
                <th>
                  <?php echo $this->Form->input('po_primary', array('class' => 'form-control', 'type' => 'hidden', 'value' => $val['poprimary_id'], 'label' => false, 'readonly')); ?>
                  <?php echo $val['additem']['item_name']; ?>
                </th>
                <th class="po-qty">
                  <?php echo $val['item_qty']; ?>
                </th>
                <th>
                  <?php $maxValue = isset($val['item_qty']) ? $val['item_qty'] : 0; ?>

                  <input class="form-control" type="text" name="item_qty[<?php echo $val['item_id']; ?>]" id="item_qty"
                    min="0" max="<?php echo $remning; ?>" onkeyup="checkQuantity(this, <?php echo $remning; ?>)"
                    onblur="checkQuantity(this, <?php echo $remning; ?>)" label="false" required>

                  <script>
                    function checkQuantity(input, remning) {
                      var enteredValue = parseInt(input.value);
                      if (enteredValue > remning) {
                        alert("Quantity Can't be greater than " + remning);
                        input.value = remning;
                      }
                    }
                  </script>

                </th>
              </tr>
            <?php }
          } else { ?>
            <tr>
              <td colspan="4" align="center">No Data Available</td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>

    <div class="col-sm-12">
      <label><b>Delivery Note</b></label>
      <?php echo $this->Form->input('delivery_note', array('class' => 'form-control ', 'type' => 'textarea', 'label' => false, 'required')); ?>
    </div>

    <?php echo $this->Form->submit('Submit', array('class' => 'btn btn-info pull-left submitbtn', 'style' => 'margin: 10px 0px;', 'title' => 'Submit')); ?>

  </div>
  <div class="col-sm-12 popup_btm_tbl">

    <table class="table table-bordered table-striped" id="followdetails">
      <thead class="thead-dark">
        <tr>
          <th style="">S.No.</th>
          <th style="">PO Id</th>
          <th style="">Date</th>
          <th style="">Remark</th>
          <th style="">Status</th>
          <th style="">View Items</th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($DeliverNote_data) > 0) {
          $cnt = 1;
          foreach ($DeliverNote_data as $value) {
            $delivery_date = date('Y-m-d', strtotime($value['delivery_date']));
            $item_details = $this->Comman->deliverydata($value['poprimary_id'],$delivery_date);
            ?>
            <tr>
              <td>
                <?php echo $cnt++; ?>
              </td>
              <td>
                <?php echo $value['po_id']; ?>
              </td>
              <td>
                <?php echo date('d-m-Y', strtotime($value['delivery_date'])); ?>
              </td>
              <td>
                <?php echo $value['delivery_note']; ?>
              </td>
              <td>
                <?php 
                echo ($item_details[0]['status']== 'N')?'Received':'Not Received';
                ?>
              </td>
              <td align="center">
                <div class="dropdown-center">
                  <button class="btn btn-white dropdown-toggle tbl_btn del_nt_pop" type="button" data-bs-toggle="dropdown" aria-expanded="false"> <i class="fa fa-eye" aria-hidden="true"></i></button>
                  <ul style="padding: 10px;" class="dropdown-menu">
                    <?php foreach ($item_details as $vll) { ?>
                      <li>
                        <?php echo $vll['additem']['item_name']; ?>&nbsp; : &nbsp;<b>
                          <?php echo $vll['item_qty']; ?>
                        </b>
                      <li>
                      <?php } ?>
                  </ul>
                </div>
              </td>
            </tr>
            <?php $cnt++;
          }
        } else { ?>
          <tr>
            <td colspan="5" align="center">No Data Available</td>
          </tr>
        <?php } ?>
      </tbody>

    </table>
  </div>

  <!--./modal-footer-->
  </form>

  <script>
    $(document).ready(function () {
      $('#datepicker1').datepicker({
        dateFormat: 'dd-mm-yy',
        yearRange: '2022:2026',
      });
      $('#datepicker1').datepicker('setDate', 'today');
    });
  </script>
  <script>
    $(document).ready(function () {
      $('.popover-dismiss').popover({
        trigger: 'focus'
      })
    });
  </script>