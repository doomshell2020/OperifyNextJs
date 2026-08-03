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




$s = 1;
?>
<div class="tableContainer " style=" border:1px solid #ccc !important;">


  <!-- <a target="_blank"
    href="<?php echo ADMIN_URL; ?>purchaseorder/viewpodetailspdf/<?php echo $users['purchaseorder_id'] . "/" . $users['is_revised'] . "/" . $users['id']; ?>"
    class="btn btn-success pull-right m-top10" style=" margin-top: ; color:#fff; padding:6px 20px;font-size:14px ;"><i
      class="far fa-file-pdf"></i>&nbsp;Print</a> -->

  <div class="tableHeader">
    <p style="text-align:center;font-size:15px;"><b>Quotation Details</b></p>
    <table>
      <tr>
        <td><b>Quotation No. :-</b>
          <?php echo $quotation['quotation_id']; ?>
      </tr>
      <tr>
        <td><b>Quotation Date :-</b>
          <?php echo date("d-m-Y", strtotime($quotation['added_time'])); ?>
        </td>
        <td><b>Delivery Date :-</b>
          <?php echo date("d-m-Y", strtotime($quotation['delivery_date'])); ?>
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
          <th width="07%">Rate</th>
          <th width="10%">Price (INR)</th>
          <th width="04%">Tax</th>
          <th width="09%">Tax Amt</th>
          <th width="10%">Amount</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($quotationItemDeatails as $value) {
          $itemDetails = $this->Comman->getitemcatcom( $value['item_id']);



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
          $taxx = ($taxx) ? $taxx: '0';

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
              <?php echo Ucfirst(($itemDetails['item_name'])); ?>
            </td>
            <td>
              <?php echo $value['item_qty'] . ' ' . $value['uom']; ?>
            </td>
            <td style="text-align:right;">
              <?php echo number_format((float) $value['item_amt'], 2, '.', ''); ?>
            </td>
            <td style="text-align:right;">
              <?php echo number_format((float) $costprice, 2, '.', ''); ?>
            </td>
            <td>
              <?php echo $taxx . '%'; ?>
            </td>
            <td style="text-align:right;">
              <?php echo number_format((float) $value['item_tax_amt'], 2, '.', ''); ?>
            </td>
            <td style="text-align:right;">
              <?php echo number_format((float) $value['item_total_amount'], 2, '.', ''); ?>
            </td>
          </tr>
        <?php $s++;
          $totalamaunt += $value['item_total_amount'];
        } ?>
        <tr>
          <td colspan="5" style="text-align:right;"><b></b></td>
          <td colspan="4" style="text-align:right;"><b>Total Amount :-</b>
            <?php echo number_format((float) $totalamaunt, 2, '.', ''); ?>
          </td>
        </tr>
      </tbody>
    </table>
  </div>


</div>