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
$t = 1;
?>
<div class="tableContainer " style=" border:1px solid #ccc !important;">


  <!-- <a target="_blank"
    href="<?php echo ADMIN_URL; ?>purchaseorder/viewpodetailspdf/<?php echo $users['purchaseorder_id'] . "/" . $users['is_revised'] . "/" . $users['id']; ?>"
    class="btn btn-success pull-right m-top10" style=" margin-top: ; color:#fff; padding:6px 20px;font-size:14px ;"><i
      class="far fa-file-pdf"></i>&nbsp;Print</a> -->

  <div class="tableHeader">
    <p style="text-align:center;font-size:15px;"><b>Bid Received Vendors</b></p>
    <table>

      <tr>
        <td><b>Quotation No. :-</b>
          <?php echo $bidVendorsDetails[0]['quotation_id']; ?>
        </td>
        <td><b>Quotation Date :-</b>
          <?php echo date("d-m-Y", strtotime($bidVendorsDetails[0]['quotation_date'])); ?>
        </td>
      </tr>

      <tr>
        <td><b>Delivery Date :-</b>
          <?php echo date("d-m-Y", strtotime($bidVendorsDetails[0]['delivery_date'])); ?>
        </td>
        <td><b>Quotaion Amount :-</b>
          <?php echo number_format((float) $bidVendorsDetails[0]['total_amt'], 2, '.', ''); ?>
        </td>
      </tr>

    </table>
  </div>


  <!-- po details -->
  <div class="table-responsive" style="padding: 10px;">
    <table class="table-bordered" cellpadding="3">
      <thead>
        <tr>
          <th width="04%">S.No.</th>
          <th width="35%">Vendor</th>
          <th width="10%">Bid Date</th>
          <th width="07%">Bid Amount</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if (!empty($bidVendorsDetails)) {

          foreach ($bidVendorsDetails as $value) {
            $vendor_id = $this->Comman->findvendornames($value['vendor_id']);
        ?>

            <tr>
              <td>
                <?php echo $s; ?>.
              </td>
              <td>
                <?php echo Ucfirst(($vendor_id['name'])); ?>
              </td>
              <td>
                <?php echo date("d-m-Y", strtotime($value['created'])); ?>
              </td>
              <td style="text-align:right;">
                <?php echo number_format((float) $value['total_amt_bid'], 2, '.', ''); ?>
              </td>
            </tr>
          <?php $s++;
          }
        } else { ?>
          <tr>
            <td colspan="4" style="text-align:center;">No vendor bid yet.</td>
          </tr>
        <?php    } ?>
      </tbody>
    </table>
  </div>


</div>