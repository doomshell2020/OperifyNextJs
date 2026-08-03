<?php //pr($_SESSION);   ?>
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
    }
</style>
<?php

$contractname = $this->comman->findcontractname($productionorder['contract_id']);
$finishedproduct = $this->comman->getitemname($productionorder['item_id']);
$prepareddailyqty = $this->comman->checkdailysheet($productionorder['po_id'], 8);

$totalorderqty = $this->Comman->getdesignsheetno($productionorder['contract_id'], $productionorder['item_id']);
$preparedqty = '';
foreach ($prepareddailyqty as $outhersheathing) {
    $preparedqty += $outhersheathing['production_shift_a'] + $outhersheathing['production_shift_b'];
}
// pr($preparedqty);die;
$i = 1;
$j = 1;
?>
<div class="tableContainer " style=" border:1px solid #ccc !important;">
    <a target="_blank"
        href="<?php echo SITE_URL; ?>admin/production/viewproductionpdf/<?php echo $productionorder['po_id']; ?>"
        class="btn btn-success pull-right m-top10"
        style=" margin-top: ; color:#fff; padding:6px 20px;font-size:14px ;"><i
            class="far fa-file-pdf"></i>&nbsp;Print</a>
    <div class="tableHeader">
        <p style="text-align:center;font-size:15px;"><b>Production Order Details</b></p>
        <table>
            <tr>
                <td><b>Productuion Order No.:-</b>
                    <?php echo $productionorder['po_id']; ?>
                </td>
                <td><b>Issue Date:-</b>
                    <?php echo date('d-M-Y', strtotime($productionorder['issuedate'])); ?>
                </td>
            </tr>
            <tr>
                <td><b>Product:-</b>
                    <?php echo $finishedproduct['item_name']; ?>
                </td>
                <td><b>Contract Name:-</b>
                    <?php echo $contractname['title'] . '(' . $contractname['workorder'] . ')'; ?>
                </td>
            </tr>
            <tr>
                <td><b>Quantity:-</b>
                    <?php echo $totalorderqty['quantity']; ?> KM
                </td>
                <td><b>Start Date:-</b>
                    <?php echo date('d-M-Y', strtotime($productionorder['startdate'])); ?>
                </td>
            </tr>
            <tr>
                <td><b>Start Date:-</b>
                    <?php echo date('d-M-Y', strtotime($productionorder['startdate'])); ?>
                </td>
            </tr>
        </table>
    </div>

    <div class="tableHeader">
    <table class="table-bordered " cellpadding="3">
        <thead>
            <tr>
                <td><b>Planned Qty:-</b> <?php echo sprintf('%.2f', $productionorder['plannedqty']); ?> KM</td>
                <td><b>Prepared Qty:-</b><?php echo sprintf('%.2f', $preparedqty); ?> KM</td>
                <td><b>Pending Qty:-</b><?php echo sprintf('%.2f', $productionorder['plannedqty'] - $preparedqty); ?> KM</td>
            </tr>
        </thead>
    </table>
    </div>

    <p style="text-align:center;font-size:15px;"><b>Process Details</b></p>
    <div class="table-responsive" style="padding: 10px;">
        <table class="table-bordered" cellpadding="3">
            <thead>
                <tr>
                    <th width="05%">S.No.</th>
                    <th width="50%">Process Name</th>
                    <th width="15%">Start Date</th>
                    <th width="15%">End Date</th>
                    <th width="15%">Prepared Qty(KM)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($processname as $process) {
                    $checkdailysheet = $this->comman->checkdailysheet($productionorder['po_id'], $process['id']);

                    if (!empty($checkdailysheet)) {
                        $quantity = '';
                        $startdate = '';
                        $completedate = '';
                        foreach ($checkdailysheet as $key => $value) {
                            $quantity += $value['production_shift_a'] + $value['production_shift_b'];

                            if ($key === array_key_first($checkdailysheet)) {
                                $startdate = date('d-m-Y', strtotime($value['production_date']));
                            }
                            if ($key === array_key_last($checkdailysheet)) {
                                $completedate = date('d-m-Y', strtotime($value['production_date']));
                            }
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
        </table>
    </div>


    <p style="text-align:center;font-size:15px;"><b>Raw Material</b></p>
    <div class="table-responsive" style="padding: 10px;">
        <table class="table-bordered" cellpadding="3">
            <thead>
                <tr>
                    <th width="05%">S.No.</th>
                    <th width="51%">Item Name</th>
                    <th width="22%">Required Qty</th>
                    <th width="22%">Available Qty</th>
                </tr>
            </thead>
            <tbody>
                <?php
                 $designsheetno = $this->Comman->getdesignsheetno($productionorder['contract_id'], $productionorder['item_id']);
                 $designitemsdetails = $this->Comman->getdesignmaterials($designsheetno['designsheetno']);
                foreach ($designitemsdetails as $designsheet) {
                    $itemname = $this->Comman->getitemname($designsheet['item_id']);
                    $designitemqty = $this->Comman->getdesignmaterialqty($designsheet['designsheetno'], $designsheet['item_id']);
                    $perkmQty = $designitemqty['sum']/$designsheetno['quantity'];
                    $reqQty = $perkmQty * $productionorder['plannedqty'];

                    $designitemqty = $this->Comman->todayopeningstock($designsheet['item_id'],$productionorder['issuedate']);
                        ?>
                        <tr>
                            <td>
                                <?php echo $j; ?>.
                            </td>
                            <td>
                                <?php echo $itemname['item_name']; ?>
                            </td>
                            <td style="text-align:right;">
                                <?php echo sprintf('%.2f', $reqQty); ?>
                            </td>
                            <td style="text-align:right;">
                                <?php echo sprintf('%.2f', $designitemqty); ?>
                            </td>
                        </tr>
                        <?php
                        $j++;
                  
                } ?>

            </tbody>
        </table>
    </div>
</div>