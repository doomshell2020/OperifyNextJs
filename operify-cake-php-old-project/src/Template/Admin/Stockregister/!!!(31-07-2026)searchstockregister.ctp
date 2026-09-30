<tr>
    <td><a id="" style="position: absolute; top:-56px; right: 201px;" class="btn btn-info btn-sm pull-right" target="_blank" href="<?php echo ADMIN_URL; ?>Stockregister/summaryexcel/<?php echo $datefrom; ?>/<?php echo $dateto2; ?>/<?php echo $item_id; ?>"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Export Summary Excel</a></td>
    <td><a id="" style="position: absolute; top:-56px; right: 46px;" class="btn btn-info btn-sm pull-right" target="_blank" href="<?php echo ADMIN_URL; ?>Stockregister/detailedexcel/<?php echo $datefrom; ?>/<?php echo $dateto2; ?>/<?php echo $item_id; ?>"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Export Detailed Excel</a></td>
</tr>

<?php
$date_from = strtotime($datefrom);
$date_to = strtotime($dateto2);
$cnt = 1;
$previousClosingStock = 0;

for ($i = $date_from; $i <= $date_to; $i += 86400) {
    // OpeningStock stock
    $openingStock = $previousClosingStock;

    if ($i == $date_from) {
        $openingbal = $this->Comman->stockregisteropening2(date('Y-m-d', $i), $item_id);
        $openingStock = $openingbal ?? 0;
    }
    // Received stock
    $reciviedbal = $this->Comman->stockregisteropeningrecivied(date("Y-m-d", $i), $item_id);
    $receivedStock = $reciviedbal[0]['sum'] ?? 0;

    // Dispatched stock
    $dispatchedbal = $this->Comman->stockregisteropeningdispatched(date("Y-m-d", $i), $item_id);
    $dispatchedStock = $dispatchedbal[0]['sum'] ?? 0;

    // Calculate total quantity
    $totalquant = $openingStock + $receivedStock - $dispatchedStock;
    $previousClosingStock = $totalquant;
?>

    <tr>
        <td><?php echo $cnt++; ?></td>
        <td><?php echo date("d-m-Y", $i); ?></td>
        <td><?php echo number_format($openingStock, 2); ?></td>
        <td>
            <?php if ($receivedStock > 0) { ?>
                <a target="_blank" href="<?php echo SITE_URL; ?>admin/stockregister/receivedstock/<?php echo date("Y-m-d", $i); ?>/<?php echo $item_id; ?>">
                    <?php echo number_format($receivedStock, 2); ?>
                </a>
            <?php } else { ?>
                0
            <?php } ?>
        </td>
        <td>
            <?php if ($dispatchedStock > 0) { ?>
                <a target="_blank" href="<?php echo SITE_URL; ?>admin/stockregister/dispatchedstock/<?php echo date("Y-m-d", $i); ?>/<?php echo $item_id; ?>">
                    <?php echo number_format($dispatchedStock, 2); ?>
                </a>
            <?php } else { ?>
                0
            <?php } ?>
        </td>
        <td><?php echo number_format($totalquant, 2); ?></td>
    </tr>

<?php }  ?>