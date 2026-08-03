<?php //pr($_SESSION); ?>
<style>
  /* .tableContainer {
    border: 1px solid #ccc;
  } */

  .tableContainer p {
    margin-bottom: 5px;
  }

  .tableContainer table thead {
    background: #fff;
    color: #333;
  }

  .tableContainer .tableHeader {
    padding: 10px;
    /* border-bottom: 1px solid #ccc; */
  }
</style>
<?php
$vendorshipfrom = $this->Comman->vendorgst($users['vendor_id']);
$findvendornames = $this->Comman->findvendornames($users['vendor_id']);


$s = 1;

?>
<div class="tableContainer " style=" border:1px solid #ccc !important;">
  <div class="tableHeader">
    <h3 style="text-align:center;font-size:18px;"><b>GRN Details</b></h3>
    <table style="font-size: 18px !important;">
      <tr>
        <td><b>GRN No. :-</b>
          <?php echo $users['id']; ?>
        </td>
        <td><b>PO No. :-</b>
          <?php echo $users['purchaseorder_id']; ?>
        </td>
      </tr>
      <tr>
        <td><b>Inward Date :-</b>
          <?php echo date("d-m-Y", strtotime($users['inwarddate'])); ?>
        </td>
        <td><b>Bill Date :-</b>
          <?php echo date("d-m-Y", strtotime($users['bill_date'])); ?>
        </td>
      </tr>
      <tr>
        <td><b>Bill No :-</b>
          <?php echo $users['bill_no']; ?>
        </td>
        <td><b>GSTIN NO. :-</b>
          <?php echo $vendorshipfrom['gst_number']; ?>
        </td>
      </tr>
      <tr>
        <td><b>Vendor Name :-</b>
          <?php echo $findvendornames['name']; ?>
        </td>
      </tr>
    </table>
  </div>


  <h3 style="text-align:center;font-size:18px;"><b>Received Products</b></h3>
  <div class="table-responsive" style="padding: 10px;">
  <table class="table-bordered" cellpadding="3">
      <thead>
        <tr>
          <th width="04.00%">S.No.</th>
          <th width="22.12%">Item</th>
          <th width="09.76%">Order Qty.</th>
          <th width="11.76%">Received Qty.</th>
          <th width="09.6%">Rate</th>
          <th width="11.96%">Price (INR)</th>
          <th width="09.60%">Tax Rate(%)</th>
          <th width="09.60%">Tax Amt</th>
          <th width="09.60%">Amount</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($puritems as $value) {

          $getpo = $this->Comman->getpostockitem($value['po_id'], $value['item_id']);
          $gettaxparent = $this->Comman->gettaxnameparent($value['tax_id']);

          if (empty($gettaxparent)) {
            $gettaxparent = $this->Comman->gettaxname2($value['tax_id']);
          }

          $i = 0;
          $taxx = '';
          foreach ($gettaxparent as $hh => $ty) {
            $taxx .= $ty['tax'] ;
            $i++;
          }

          if ($i == 2) {
            $taaxx = $value['tax'] / $i;
            $taxxs = number_format((float) $taaxx, 2, '.', '') . "<br> &nbsp;" . number_format((float) $taaxx, 2, '.', '');
          } else {
            $taxxs = number_format((float) $value['tax'], 2, '.', '');
          }

          ?>
          <tr>
            <td>
              <?php echo $s; ?>.
            </td>
            <td>
              <?php echo Ucfirst(($value['additem']['item_name'])); ?>
            </td>
            <td>
              <?php echo $getpo['item_qty'] . ' ' . $value['additem']['measurementunit']['unit_name']; ?>
            </td>
            <td>
              <?php echo $value['quantity'] . ' ' . $value['additem']['measurementunit']['unit_name']; ?>
            </td>
            <td style ="text-align:right;">
              <?php echo number_format((float) $value['rate'], 2, '.', ''); ?>
            </td>
            <td style ="text-align:right;">
              <?php echo number_format((float) $value['cost_price'], 2, '.', ''); ?>
            </td>
            <td>
              <?php echo $taxx; ?>
            </td>
            <td style ="text-align:right;">
              <?php echo $taxxs; ?>
            </td>
            <td style ="text-align:right;">
              <?php echo number_format((float) $value['amount']); ?>
            </td>
          </tr>
          <?php $s++;
          $totalamaunt += $value['amount'];
          if($value['cost_price'] == $value['amount']){
            $taxstatus = 'Tax Included';
          }else{
            $taxstatus = 'Tax Excluded';
          }
        } ?>
        <tr>
          <td colspan="6" style="text-align:right;"><b><?php echo $taxstatus; ?></b></td>
          <td colspan="3" style="text-align:right;"><b>Total Amount :-</b>
            <?php echo number_format((float) $totalamaunt); ?>
          </td>
        </tr>
      </tbody>
    </table>
  </div>




</div>