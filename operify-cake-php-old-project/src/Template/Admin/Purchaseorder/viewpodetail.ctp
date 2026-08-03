<style>
  .tableContainer p {
    margin-bottom: 5px;
  }

  .tableContainer table thead {
    background: #fff;
    color: #333;
  }

  .tableContainer .tableHeader {
    padding: 10px;
  }
</style>
<?php
$checkgrn = $this->Comman->checkgrn($users['purchaseorder_id'], $users['id']);

$vendorshipfroms = $this->Comman->vendorshipfromdetail($users['vendor_id']);
$vendorshipfrom = $this->Comman->vendorgst($users['vendor_id']);

$postatus = $this->Comman->findgoodsrecivied($users['purchaseorder_id']);

$vendorbilltodetail = $this->Comman->vendorbilltodetail($users['vendor_id']);
$podate = date('d-m-Y', strtotime($users['added_time']));

$delivery_date = date('d-m-Y', strtotime($users['delivery_date']));
$supliername = $sup['name'];

if ($co != 0) {
  $amedmentdate = date('d-m-Y', strtotime($users['revised_date']));
}

if ($users['postatus'] == 'O') {
  $status = "Open";
} else {
  $status = "Close";
}

$s = 1;
?>

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


<div class="tableContainer " style=" border:1px solid #ccc !important;">


  <a target="_blank"
    href="<?php echo ADMIN_URL; ?>purchaseorder/viewpodetailspdf/<?php echo $users['purchaseorder_id'] . "/" . $users['is_revised'] . "/" . $users['id']; ?>"
    class="btn btn-success pull-right m-top10" style=" margin-top: ; color:#fff; padding:6px 20px;font-size:14px ;"><i
      class="far fa-file-pdf"></i>&nbsp;Print</a>

  <div class="tableHeader">
    <p style="text-align:center;font-size:15px;"><b>Purchase Order Details</b></p>
    <table>
      <tr>
        <td><b>Purchase Order No. :-</b>
          <?php echo $users['purchaseorder_id']; ?>
        </td>
        <td><b>Amendment No :-</b>
          <?php if ($co != 0) {
            echo $co . '&nbsp;(<b>Date : </b>' . $amedmentdate . ' )';
          } else {
            echo '---';
          } ?>
        </td>
      </tr>
      <tr>
        <td><b>Purchase Order Date :-</b>
          <?php echo $podate; ?>
        </td>
        <td><b>Delivery Date :-</b>
          <?php echo date("d-m-Y", strtotime($delivery_date)); ?>
        </td>
      </tr>
      <tr>
        <td><b>GSTIN NO. :-</b>
          <?php echo $vendorshipfrom['gst_number']; ?>
        </td>
        <td><b>Vendor Name :-</b>
          <?php echo $supliername; ?>
        </td>
      </tr>
      <tr>
        <td><b>Status :-</b>
          <?php echo $status; ?>
        </td>
      </tr>
    </table>
  </div>

  <!-- po details -->
  <p style="text-align:center;font-size:15px;"><b>Products</b></p>
  <div class="table-responsive" style="padding: 10px;">
    <table class="table-bordered" cellpadding="3">
      <thead>
        <tr>
          <th width="04%">S.No.</th>
          <th width="35%">Item</th>
          <th width="10%">Order Qty.</th>
          <th width="10%">Pending Qty.</th>
          <th width="07%">Rate</th>
          <th width="10%">Price (INR)</th>
          <th width="04%">Tax</th>
          <th width="09%">Tax Amt</th>
          <th width="10%">Amount</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($puritems as $value) {
          // pr($value);
          // exit;
          $PurchaseDetails = $this->Comman->PurchaseOrderDetails($value['po_id'], $value['item_id']);

          $qty = $this->Comman->stockregisteritems($value['purchaseorder_id'], $value['item_id']);
          $result = ['sum' => round($qty->sum, 2)];

          $sizename = $this->Comman->getsizename($value['additem']['size_id']);
          $gettaxparent = $this->Comman->gettaxnameparent($value['tax_id']);
          if (empty($gettaxparent)) {
            $gettaxparent = $this->Comman->gettaxname2($value['tax_id']);
          }
          $i = 0;
          $taxx = '';

          foreach ($gettaxparent as $hh => $ty) {
            $taxx .= $ty['tax'];
            $i++;
          }
          $taxx = ($taxx) ? $taxx : '0';
          $result1 = $value['item_qty'] - $result['sum'];
          if ($i == 2) {
            $taaxx = $value['tax'] / $i;
            $taxxs = number_format((float) $taaxx, 2, '.', '') . " &nbsp;" . number_format((float) $taaxx, 2, '.', '');
          } else {
            $taxxs = number_format((float) $value['tax'], 2, '.', '');
          }
          $costprice = $value['item_qty'] * $value['item_amt'];
        ?>
          <tr>
            <td>
              <?php echo $s; ?>.
            </td>
            <td>
              <?php echo Ucfirst(($value['additem']['item_name'])); ?>
            </td>
            <td>
              <?php echo $value['item_qty'] . ' ' . $value['uom']; ?>
            </td>
            <td>
              <?php
              if ($checkgrn) {
                echo formatCurrency($result1) . ' ' . $value['uom'];
              } else {
                echo $value['item_qty'] . ' ' . $value['uom'];
              }
              ?>
            </td>
            <td style="text-align:right;">
              <?php echo formatCurrency($value['item_amt']); ?>
            </td>
            <td style="text-align:right;">
              <?php echo formatCurrency($costprice); ?>
            </td>
            <td>
              <?php echo $taxx . '%'; ?>
            </td>
            <td style="text-align:right;">
              <?php echo formatCurrency($value['item_tax_amt']); ?>
            </td>
            <td style="text-align:right;">
              <?php echo formatCurrency($value['item_total_amount']); ?>
            </td>
          </tr>
        <?php $s++;
          $totalamaunt += $value['item_total_amount'];
          if ($costprice == $value['item_total_amount']) {
            $taxstatus = 'Tax Included';
          } else {
            $taxstatus = 'Tax Excluded';
          }
        } ?>
        <tr>
          <td colspan="5" style="text-align:right;"><b>
              <?php echo $taxstatus; ?>
            </b></td>
          <td colspan="4" style="text-align:right;"><b>Total Amount : </b>
            <?php echo formatCurrency($totalamaunt); ?>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- GRN details -->
  <?php
  if ($checkgrn) { ?>
    <p style="text-align:center;font-size:15px;"><b>Goods Received Note</b></p>
    <?php
    $grnDetails = $this->Comman->findgoodsrecivied($users['purchaseorder_id']);
    foreach ($grnDetails as $grnvalue) { ?>

      <div class="table-responsive" style="padding: 10px;">
        <table class="table-bordered" cellpadding="3">
          <tr>
            <td colspan="4"><b>GRN No. :-</b>
              <?php echo $grnvalue['id']; ?>
            </td>
            <td colspan="5"><b>Bill No :-</b>
              <?php echo $grnvalue['bill_no']; ?>
            </td>
          </tr>
          <tr>
            <td colspan="4"><b>Inward Date :-</b>
              <?php echo date("d-m-Y", strtotime($grnvalue['inwarddate'])); ?>
            </td>
            <td colspan="5"><b>Bill Date :-</b>
              <?php echo date("d-m-Y", strtotime($grnvalue['bill_date'])); ?>
            </td>
          </tr>

          <tr>
            <th width="04.00%">S.No.</th>
            <th width="29.12%">Item</th>
            <th width="09.76%">Order Qty.</th>
            <th width="11.76%">Received Qty.</th>
            <th width="09.6%">Rate</th>
            <th width="11.96%">Price (INR)</th>
            <th width="04.60%">Tax</th>
            <th width="09.60%">Tax Amt</th>
            <th width="09.60%">Amount</th>
          </tr>
          <?php
          $stockDetails = $this->Comman->findstock($grnvalue['id']);
          $z = 1;
          foreach ($stockDetails as $value) {
            $getpo = $this->Comman->getpostockitem($value['po_id'], $value['item_id']);
            $gettaxparent = $this->Comman->gettaxnameparent($value['tax_id']);

            if (empty($gettaxparent)) {
              $gettaxparent = $this->Comman->gettaxname2($value['tax_id']);
            }

            $i = 0;
            $taxx = '';
            foreach ($gettaxparent as $hh => $ty) {
              $taxx .= $ty['tax'] . '%';
              $i++;
            }

            if ($i == 2) {
              $taaxx = $value['tax'] / $i;
              $taxxs = number_format((float) $taaxx, 2, '.', '') . " &nbsp;" . number_format((float) $taaxx, 2, '.', '');
            } else {
              $taxxs = number_format((float) $value['tax'], 2, '.', '');
            }

            if ($value['additem']['measurementunit']['unit_name']) {
              $uom = $value['additem']['measurementunit']['unit_name'];
            } else {
              $uom = '--';
            }
          ?>
            <tr>
              <td>
                <?php echo $z; ?>.
              </td>
              <td>
                <?php echo Ucfirst(($value['additem']['item_name'])); ?>
              </td>
              <td>
                <?php echo $getpo['item_qty'] . ' ' . $uom; ?>
              </td>
              <td>
                <?php echo $value['quantity'] . ' ' . $uom; ?>
              </td>
              <td style="text-align:right;">
                <?php echo formatCurrency($value['rate']); ?>
              </td>
              <td style="text-align:right;">
                <?php echo formatCurrency($value['cost_price']); ?>
              </td>
              <td>
                <?php echo $taxx; ?>
              </td>
              <td style="text-align:right;">
                <?php echo $taxxs; ?>
              </td>
              <td style="text-align:right;">
                <?php echo formatCurrency($value['amount']); ?>
              </td>
            </tr>
          <?php $z++;
            $totalamaunt1 += $value['amount'];
            if ($value['cost_price'] == $value['amount']) {
              $taxstatus = 'Tax Included';
            } else {
              $taxstatus = 'Tax Excluded';
            }
          } ?>
          <tr>
            <td colspan="5" style="text-align:right;"><b>
                <?php echo $taxstatus; ?>
              </b></td>
            <td colspan="4" style="text-align:right;"><b>Total Amount : </b>
              <?php echo formatCurrency($totalamaunt1); ?>
            </td>
          </tr>
        </table>
      </div>
  <?php
      $totalamaunt1 = '';
    }
  } ?>



  <!-- Delivery Schedule details -->
  <?php
  $getDeliverydates = $this->Comman->getDeliverydates($users['id']);
  if ($getDeliverydates) { ?>
    <p style="text-align:center;font-size:15px;"><b>Delivery Schedule</b></p>

    <div class="table-responsive" style="padding: 10px;">

      <table class="table-bordered" cellpadding="3">
        <tr>
          <th>Item</th>
          <?php
          foreach ($getDeliverydates as $value) { ?>
            <th>Date</th>
            <th>Qty</th>
          <?php } ?>
        </tr>

        <?php $A = 1;
        foreach ($puritems as $value) { ?>
          <tr>
            <td>
              <?php echo Ucfirst(($value['additem']['item_name'])); ?>
            </td>
            <?php foreach ($getDeliverydates as $dates) {
              $getitemqty = $this->Comman->DeliveritemQty($value['item_id'], $users['id'], date('Y-m-d', strtotime($dates['delivery_date'])));
              $delivery_date = date('d-m-Y', strtotime($dates['delivery_date']));
              $qty = $getitemqty['item_qty'] ? $getitemqty['item_qty'] : 0;
              $uom = $this->Comman->getitemcatcom($value['item_id']);
            ?>
              <td>
                <?php echo $delivery_date; ?>
              </td>
              <td>
                <?php echo $qty . ' ' . $uom['measurementunit']['unit_name']; ?>
              </td>
            <?php } ?>
          </tr>
        <?php } ?>
      </table>

    </div>
  <?php
    $totalamaunt1 = '';
  } ?>
</div>