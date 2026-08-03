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

$suppliername = $this->Comman->findvendornames($contractdetail['supplier_id']);

foreach ($finsheddetails as $finshed) {
  $contractpro = $this->Comman->checkproduction($contractdetail['id'], $finshed['product_id']);
  foreach ($contractpro as $contractpro1) {
    $labour += $contractpro1['manpower_day'] + $contractpro1['manpower_night'];
    $oprational += $contractpro1['nextday8am'] - $contractpro1['reading8am'];
  }
}

$j = 1;
$k = 1;
$l = 1;
?>
<div class="tableContainer " style=" border:1px solid #ccc !important;">
  <a target="_blank"
    href="<?php echo SITE_URL; ?>admin/production/viewcontractdetailspdf/<?php echo $contractdetail['id']; ?>"
    class="btn btn-success pull-right m-top10" style=" margin-top: ; color:#fff; padding:6px 20px;font-size:14px ;"><i
      class="far fa-file-pdf"></i>&nbsp;Print</a>
  <div class="tableHeader">
    <p style="text-align:center;font-size:15px;"><b>Contract Details</b></p>
    <table>
      <tr>
        <td><b>Work Order:-</b>
          <?php echo $contractdetail['workorder']; ?>
        </td>
      </tr>
      <tr>
        <td><b>Title:-</b>
          <?php echo $contractdetail['title']; ?>
        </td>
        <td><b>Issue Date:-</b>
          <?php echo date('d-M-Y', strtotime($contractdetail['issuedate'])); ?>
        </td>
      </tr>
      <tr>
        <td><b>Contract Start Date:-</b>
          <?php echo date('d-M-Y', strtotime($contractdetail['contract_start_date'])); ?>
        </td>
        <td><b>Contract End Date:-</b>
          <?php echo date('d-M-Y', strtotime($contractdetail['contract_end_date'])); ?>
        </td>
      </tr>
      <tr>
        <td><b>Supplier Name:-</b>
          <?php echo $suppliername['name']; ?>
        </td>
        <td><b>Cost:-</b>
          <?php // echo sprintf('%.2f', $contractdetail['cost']); ?>
            <?php echo number_format($contractdetail['cost']); ?>
        </td>
      </tr>
      <tr>
        <td><b>Labour Cost:-</b>
          <?php echo $labour; ?>
        </td>
        <td><b>Operational Cost:-</b>
          <?php echo sprintf('%.2f', $oprational); ?>
        </td>
      </tr>
    </table>
  </div>


  <p style="text-align:center;font-size:15px;"><b>Finished Products</b></p>

  <?php

  foreach ($finsheddetails as $finshed) {
    $contractexists = $this->Comman->checkproduction($contractdetail['id'], $finshed['product_id']);
    $poexists = $this->Comman->findfinishedqty($contractdetail['id'], $finshed['product_id']);

    $prepardqty = '';
    foreach ($contractexists as $outhersheathing) {
      if ($outhersheathing['productprocess_id'] == 8) {
        $prepardqty += $outhersheathing['production_shift_a'] + $outhersheathing['production_shift_b'];
      }
    }

    $plannedqty = '';
    foreach ($poexists as $itemqty) {
      $plannedqty += $itemqty['plannedqty'];
    }
    ?>
    <div class="table-responsive" style="padding: 10px;">
      <table class="table-bordered" cellpadding="3">
        <thead>
          <tr>
            <td><b>Product:-</b>
              <?php echo $finshed['additem']['item_name']; ?>
            </td>
            <td><b>Quantity:-</b>
              <?php echo sprintf('%.2f', $finshed['quantity']); ?> KM
            </td>
            <td><b>Planned Qty:-</b>
              <?php echo sprintf('%.2f', $plannedqty); ?> KM
            </td>
            <td><b>Prepared Qty:-</b>
              <?php echo sprintf('%.2f', $prepardqty); ?> KM
            </td>
            <td><b>Price:-</b>
              <?php echo number_format( $finshed['price']); ?>
            </td>
          </tr>
        </thead>
      </table>

      <table class="table-bordered" cellpadding="3">

        <?php if ($contractexists) { ?>
          <tbody>
            <tr>
              <th width="05%">S.No.</th>
              <th width="19%">Process Name</th>
              <th width="09%">Start Date</th>
              <th width="09%">End Date</th>
              <th width="34%">PO No.</th>
              <th width="12%">Planned Qty(KM)</th>
              <th width="12%">Prep Qty(KM)</th>
            </tr>
            <?php
            $i = 1;
            foreach ($processname as $process) {
              $getdailysheet = $this->Comman->getdailysheet($contractdetail['id'], $finshed['product_id'], $process['id']);

              if (!empty($getdailysheet)) {
                $quantity = '';
                $startdate = '';
                $completedate = '';
                $po_no = [];
                foreach ($getdailysheet as $key => $value) {
                  $quantity += $value['production_shift_a'] + $value['production_shift_b'];
                  $po_no[] = $value['po_id'];
                  if ($key === array_key_first($getdailysheet)) {
                    $startdate = date('d-m-Y', strtotime($value['production_date']));
                  }
                  if ($key === array_key_last($getdailysheet)) {
                    $completedate = date('d-m-Y', strtotime($value['production_date']));
                  }
                }

                $newpo_no = array_unique($po_no);

                $poPlannedqty = '';
                foreach ($newpo_no as $povalue) {
                  $poqty = $this->Comman->findproductionorder($povalue);
                  $poPlannedqty += $poqty['plannedqty'];
                }
                ?>
                <tr>
                  <td>
                    <?php echo $i; ?>.
                  </td>
                  <td>
                    <?php echo $process['process_name']; ?>
                  </td>
                  <td>
                    <?php echo $startdate; ?>
                  </td>
                  <td>
                    <?php echo $completedate; ?>
                  </td>
                  <td>
                    <?php echo implode(',', $newpo_no); ?>
                  </td>
                  <td style="text-align:right;">
                    <?php echo sprintf('%.2f', $poPlannedqty); ?>
                  </td>
                  <td style="text-align:right;">
                    <?php echo sprintf('%.2f', $quantity); ?>
                  </td>
                </tr>
                <?php
                $i++;
              } else {
                continue;
              }
            } ?>
          </tbody>

        <?php } else { ?>
          <td colspan="7" style="text-align:center;">Production Not Started Yet.</td>
        <?php } ?>

      </table>

      <table class="table-bordered" cellpadding="3">
        <thead>
          <tr>
            <th style="text-align:center;">Raw Material</th>
          </tr>
        </thead>
      </table>
      <table class="table-bordered" cellpadding="3">
        <thead>
          <tr>
            <th width="04%">S.No.</th>
            <th width="54%">Item Name</th>
            <th width="14%">Qty(As per Design)</th>
            <th width="14%">Issued Qty</th>
            <th width="14%">Pending Qty</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $designsheetno = $this->Comman->getdesignsheetno($contractdetail['id'], $finshed['product_id']);
          $designitemsdetails = $this->Comman->getdesignmaterials($designsheetno['designsheetno']);
          $k = 1;
          foreach ($designitemsdetails as $designsheet) {
            $getitemname = $this->Comman->getitemname($designsheet['item_id']);

            if($designsheet['is_group'] > 0){
              $categoryName = $this->Comman->getcategorynmae($getitemname['category_id']);
              $itemname = $categoryName['category_name'];
            }else{
              $itemname = $getitemname['item_name'];
            }

            $designitemqty = $this->Comman->getdesignmaterialqty($designsheet['designsheetno'], $designsheet['item_id']);

            $issueitemqty = $this->Comman->rawitempendingqty($designsheet['item_id'], $finshed['product_id'], $contractdetail['id'], $designsheet['is_group']);
            $reverseqty = $this->Comman->rawitemreverseqty($designsheet['item_id'], $finshed['product_id'], $contractdetail['id'], $designsheet['is_group']);
            $pendingqty = $designitemqty['sum'] - $issueitemqty['sum'] + $reverseqty['sum']
              ?>
            <tr>
              <td>
                <?php echo $k; ?>.
              </td>
              <td>
                <?php echo $itemname; ?>
              </td>
              <td style="text-align:right;">
                <?php echo sprintf('%.2f', $designitemqty['sum']); ?>
              </td>

              <td style="text-align:right;">
                <?php echo sprintf('%.2f', $issueitemqty['sum'] - $reverseqty['sum']); ?>
              </td>
              <td style="text-align:right;">
                <?php echo sprintf('%.2f', $pendingqty); ?>
              </td>
            </tr>
            <?php
            if ($designsheet['is_group'] > 0) {
              $categoryItems = $this->Comman->getitembycategory($getitemname['category_id']);

              foreach ($categoryItems as $category) {
                $categoryitemname = $category['item_name'];
                $issuecatItemqty = $this->Comman->rawitempendingqty($category['id'], $finshed['product_id'], $contractdetail['id'], 0);
                $reverseCatqty = $this->Comman->rawitemreverseqty($category['id'], $finshed['product_id'], $contractdetail['id'], 0);
                $actualIssued = $issuecatItemqty['sum'] - $reverseCatqty['sum'];

                if ($actualIssued == 0) {
                  continue;
                }
                ?>
                <tr>
                  <th></th>
                  <td><?php echo $categoryitemname; ?></td>
                  <td style="text-align:right;" colspan="1"></td>
                  <td style="text-align:right;" colspan="1"><?php echo sprintf('%.2f', $actualIssued); ?></td>
                  <td style="text-align:right;" colspan="1"></td>
                </tr>
                <?php
              }
            }
            $k++;
          } ?>
          <tr>
            <td></td>
          <tr>
        </tbody>
      </table>
    </div>

  <?php } ?>

  <p style="text-align:center;font-size:15px;"><b>Production Orders</b></p>
  <div class="table-responsive" style="padding: 10px;">
    <table class="table-bordered" cellpadding="3">
      <thead>
        <tr>
          <th width="6%">PO No.</th>
          <th width="8%">Issue Date</th>
          <th width="44%">Product</th>
          <th width="10%">Planned Qty</th>
          <th width="11%">Prepared Qty</th>
          <th width="8%">Start Date</th>
          <th width="8%">End Date</th>
          <th width="5%">Status</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($podetails as $value) {
          $itemname = $this->comman->getitemname($value['item_id']);
          $checkdailysheet = $this->comman->checkdailysheet($value['po_id'], 8);

          // $checkdailysheet = $this->comman->checkdailysheet($value['po_id'], $value['is_completed']);
          $quantity = '';
          foreach ($checkdailysheet as $details) {
            $quantity += $details['production_shift_a'] + $details['production_shift_b'];
            $completedate = date('d-m-Y', strtotime($details['production_date']));
          }
          ?>
          <tr>
            <td>
              <?php echo $value['po_id'] ?>
            </td>
            <td>
              <?php echo date('d-m-Y', strtotime($value['issuedate'])) ?>
            </td>
            <td>
              <?php echo $itemname['item_name'] ?>
            </td>
            <td style="text-align:right;">
              <?php echo sprintf('%.2f', $value['plannedqty']) ?>
            </td>
            <td style="text-align:right;">
              <?php echo sprintf('%.2f', $quantity ? $quantity : 0); ?>
            </td>
            <td>
              <?php echo date('d-m-Y', strtotime($value['startdate'])) ?>
            </td>
            <td>
              <?php echo date('d-m-Y', strtotime($value['enddate'])) ?>
            </td>
            <!-- <td>
             <?php //if ($value['status'] == 'C') {
             //  echo 'Close';
            //  } else {
           //     echo 'Open';
            //  } ?>
            </td> -->
            <td>
              <?php 
              
              if ($finshed['quantity'] <= $quantity) {
                echo 'Close';
              } else {
                echo 'Open';
              } ?>
            </td>
          </tr>
          <?php
        } ?>
      </tbody>
    </table>
  </div>
  <p style="text-align:center;font-size:15px;"><b>Inspection Report</b></p>
  <div class="table-responsive" style="padding: 10px;">
    <table class="table-bordered" cellpadding="3">
      <thead>
        <tr>
          <th width="15%">S.No</th>
          <th width="55%">Inspector Name</th>
          <th width="30%">Inspection Date</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($inspection as $inspectionreport) {
          $itemname = $this->Comman->getitemname($designsheet['item_id']);
          ?>
          <tr>
            <td>
              <?php echo $l; ?>.
            </td>
            <td>
              <?php echo $inspectionreport['name']; ?>
            </td>
            <td>
              <?php echo date('d-M-Y', strtotime($inspectionreport['inspection_date'])); ?>
            </td>
          </tr>
          <?php $l++;
        } ?>
      </tbody>
    </table>
  </div>


</div>