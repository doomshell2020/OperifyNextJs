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
$contractname = $this->comman->findcontractname($designsheet['contract_id']);
$finisheditem = $this->Comman->getitemname($designsheet['item_id']);
$k = 1;
?>
<div class="tableContainer " style=" border:1px solid #ccc !important;">

<a target = "_blank" href="<?php echo SITE_URL; ?>admin/designsheet/viewdesignsheetpdf/<?php echo $designsheet['designsheetno']; ?>" class="btn btn-success pull-right m-top10"
    style=" margin-top: ; color:#fff; padding:6px 20px;font-size:14px ;"><i class="far fa-file-pdf"></i>&nbsp;Print</a>

  <div class="tableHeader">
    <p style="text-align:center;font-size:15px;"><b>Design Sheet Details</b></p>
    <table>
      
      <tr>
        <td><b>Design Sheet No:-</b>
          <?php echo $designsheet['designsheetno']; ?>
        </td>
        <td><b>Issue Date:-</b>
          <?php echo date('d-M-Y', strtotime($designsheet['datefrom'])); ?>
        </td>
      </tr>
      <tr>
        <td><b>Contract:-</b>
        <?php echo  $contractname['title'] . '(' . $contractname['workorder'] . ')'; ?>
        </td>
        <td><b>Finished Product:-</b>
        <?php echo $finisheditem['item_name']; ?>
        </td>
      </tr>
      <tr>
        <td><b>Quantity:-</b>
          <?php echo $designsheet['quantity']; ?> KM
        </td>
        
      </tr>
    </table>
  </div>


  <p style="text-align:center;font-size:15px;"><b>Raw Material</b></p>
  <div class="table-responsive" style="padding: 10px;">
    <table class="table-bordered" cellpadding="3">
      <thead>
        <tr>
          <th width="05%">S.No.</th>
          <th width="61%">Item Name</th>
          <th width="13%">Qty(Per KM)</th>
          <th width="11%">Total Qty</th>
          <th width="10%">UOM</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($designsheetdetails as $designsheet) {
          $itemname = $this->Comman->getitemname($designsheet['item_id']);
            ?>
          <tr>
            <td>
              <?php echo $k; ?>.
            </td>
            <td>
              <?php echo $itemname['item_name']; ?>
            </td>
            <td style="text-align:right;">
              <?php echo sprintf('%.2f',$designsheet['km_item_qty']); ?>
            </td>

            <td style="text-align:right;">
              <?php echo sprintf('%.2f',$designsheet['item_qty']); ?>
            </td>
            <td>
              <?php echo $designsheet['uom']; ?>
            </td>
          </tr>
          <?php $k++;
        } ?>
      </tbody>
    </table>
  </div>




</div>