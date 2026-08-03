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
$itemname = $this->comman->getitemname($reverseindentid['finishedproduct_id']);
$contractname = $this->comman->findcontractname($reverseindentid['contract_id']);
$machineName = $this->comman->getMachineName($reverseindentid['machine_id']);
$s = 1;

?>
<div class="tableContainer " style=" border:1px solid #ccc !important;">

<a target = "_blank" href="<?php echo SITE_URL; ?>admin/reverseindent/viewreverseindentpdf/<?php echo $reverseindentid['reverse_id'] ?>" class="btn btn-success pull-right m-top10"
    style=" margin-top: ; color:#fff; padding:6px 20px;font-size:14px ;"><i class="far fa-file-pdf"></i>&nbsp;Print</a>

  <div class="tableHeader">
    <h3 style="text-align:center;font-size:18px;"><b>Reverse Indent Details</b></h3>
    <table style="font-size: 18px !important;"  cellpadding="3">
      <tr>
        <td><b>Indent Id :-</b>
          <?php echo $reverseindentid['reverse_id']; ?>
        </td>
        <td><b>Contract name :-</b>
          <?php
          echo $contractname['title'] . '(' . $contractname['workorder'] . ')'
            ?>
        </td>
      </tr>
      <tr>
        <td><b>Product :-</b>
          <?php echo $itemname['item_name']; ?>
        </td>
        <td><b>Machine Name :-</b>
          <?php echo $machineName['machine_name']; ?>
        </td>
      </tr>
      <tr>
      <td><b>Received By :-</b>
          <?php echo $reverseindentid['received_name']; ?>
        </td>
        <td><b>Received Date :-</b>
          <?php echo  date("d-m-Y", strtotime($reverseindentid['issue_date'])); ?>
        </td>
        </tr>
      <tr>
        <?php if($reverseindentid['updated'] != ''){ ?>
          <td><b>Last Updated Date :-</b>
          <?php echo  date("d-m-Y", strtotime($reverseindentid['updated'])); ?>
        </td>
        <?php } ?>
      </tr>
    </table>
  </div>


  <h3 style="text-align:center;font-size:18px;"><b>Raw Material</b></h3>
  <div class="table-responsive" style="padding: 10px;">
    <table class="table-bordered" style="font-size: 18px !important;"  cellpadding="3">
      <thead>
        <tr>
          <th width="8%">S.No.</th>
          <th width="62%">Item</th>
          <th width="20%">Received Qty</th>
          <th width="10%">UOM</th>
        </tr>
      </thead>
      <tbody>

        <?php foreach ($reverseindentdetails as $value) {
          $itemname1 = $this->comman->getitemcatcom($value['item_id']);
          ?>
          <tr>
            <td>
              <?php echo $s; ?>.
            </td>
            <td >
              <?php echo $itemname1['item_name']; ?>
            </td>
            <td style="text-align:right;">
              <?php echo sprintf('%.2f',$value['quantity']); ?>
            </td>
            
            <td>
              <?php echo $itemname1['measurementunit']['unit_name']; ?>
            </td>
            
            
          </tr>
          <?php $s++;
          
        } ?>
        <tr>
         
          
        </tr>
      </tbody>
    </table>
  </div>




</div>