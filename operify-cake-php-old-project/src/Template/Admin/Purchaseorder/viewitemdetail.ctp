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
$s = 1;
?>
<div class="tableContainer " style=" border:1px solid #ccc !important;">
  </a>

  <!-- <a target="_blank"
    href="<?php echo ADMIN_URL; ?>purchaseorder/viewitempricedetail/<?php echo $itemdetails[0]['item_id']; ?>"
    class="btn btn-success pull-left m-top10" style=" color:#fff; padding:6px 20px;font-size:14px ;">&nbsp;View All</a> -->

    <a target="_blank"
    href="<?php echo ADMIN_URL; ?>purchaseorder/viewitemdetailpdf/<?php echo $itemdetails[0]['item_id']; ?>/Y"
    class="btn btn-success pull-left m-top10" style=" color:#fff; padding:6px 20px;font-size:14px ;">&nbsp;View All</a>

  <a target="_blank"
    href="<?php echo ADMIN_URL; ?>purchaseorder/viewitemdetailpdf/<?php echo $itemdetails[0]['item_id']; ?>"
    class="btn btn-success pull-right m-top10" style=" color:#fff; padding:6px 20px;font-size:14px ;"><i
      class="far fa-file-pdf"></i>&nbsp;Print</a>

  <div class="tableHeader">
    <p style="text-align:center;font-size:15px;"><b>Last Purchase History</b></p>
  </div>

  <div class="table-responsive" style="padding: 10px;">
    <table class="table-bordered" cellpadding="3">
      <thead>
        <?php

        $itemname = $this->Comman->getitemname($itemdetails[0]['item_id']); ?>
        <tr>
          <td colspan="3"><b>Item Name:-</b>
            <?php echo $itemname['item_name']; ?>
          </td>
          <td colspan="3"><b>Print Date:-</b>
            <?php echo date("d-m-Y"); ?>
          </td>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th width="12%">PO No.</th>
          <th width="12%">PO Date</th>
          <th width="52%">Supplier</th>
          <th width="12%">Quantity</th>
          <th width="12%">Price</th>
        </tr>
        <?php if ($itemdetails) {
          foreach ($itemdetails as $item) {
            $vendorName = $this->Comman->findvendornames($item['vendor_id']);
        ?>
            <tr>
              <td>
                <?php echo $item['purchaseorder_id']; ?>
              </td>
              <td>
                <?php echo date('d-m-Y', strtotime($item['inward_date'])); ?>
              </td>
              <td>
                <?php echo $vendorName['name']; ?>
              </td>
              <td style="text-align:right;">
                <?php echo sprintf('%.2f', $item['item_qty']); ?>
              </td>
              <td style="text-align:right;">
                <?php echo sprintf('%.2f', $item['item_amt']); ?>
              </td>
            </tr>
          <?php } ?>
      </tbody>

    <?php } else { ?>
      <tr style="text-align:center;">
        <th colspan="5">
          No Record Found.
        </th>
      </tr>
    <?php } ?>

    </table>
  </div>
</div>