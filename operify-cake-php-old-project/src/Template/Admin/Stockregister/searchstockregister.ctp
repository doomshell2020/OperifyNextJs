<tr style="display: none;">
    <td colspan="10">
<?php
$exportHtml = '<div id="dyn-exports" style="text-align:right; margin-bottom: 10px;">';
$exportHtml .= '<form action="' . ADMIN_URL . 'Stockregister/summaryexcel" method="POST" target="_blank" style="margin:0;">';
$exportHtml .= '<input type="hidden" name="datefrom" value="' . $datefrom . '">';
$exportHtml .= '<input type="hidden" name="dateto" value="' . $dateto2 . '">';
$exportHtml .= '<input type="hidden" name="item_id" value="' . $item_id . '">';
if (!empty($category_ids)) {
    foreach ($category_ids as $cid) {
        $exportHtml .= '<input type="hidden" name="category_id[]" value="' . $cid . '">';
    }
}
$exportHtml .= '<button type="submit" class="btn btn-info btn-sm"><i class="fa fa-file-excel-o"></i> Export Summary Excel</button>';
$exportHtml .= '</form></div>';
?>
        <script>
            $('#dyn-exports').remove();
            $('#example2').prepend('<?php echo $exportHtml; ?>');
        </script>

        <?php if (!empty($item_id)): ?>
            <script>
            $("table thead").html(
                "<tr><th style='width: 16%;'>S.No</th><th style='width: 16%;'>Date</th><th>Opening Stock</th><th>Received Stock </th><th>Dispatched Stock </th><th>Closing Stock </th></tr>"
            );
            </script>
        <?php else: ?>
            <script>
            $("table thead").html(
                "<tr><th>S.No</th><th>Date</th><th>Product Name</th><th>Category</th><th>Opening Stock</th><th>Received Stock</th><th>Dispatched Stock</th><th>Closing Stock</th></tr>"
            );
            </script>
        <?php endif; ?>
    </td>
</tr>

<?php if (!empty($item_id)): ?>
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

<?php } ?>
<?php else: ?>
<?php 
$cnt = 1;
if (!empty($consolidatedData)) {
    foreach ($consolidatedData as $row) { 
?>
    <tr>
        <td><?php echo $cnt++; ?></td>
        <td><?php echo date("d-m-Y", strtotime($row['date'])); ?></td>
        <td><?php echo $row['product_name']; ?></td>
        <td><?php echo $row['category']; ?></td>
        <td><?php echo number_format($row['opening'], 2); ?></td>
        <td>
            <?php if ($row['received'] > 0) { ?>
                <a target="_blank" href="<?php echo SITE_URL; ?>admin/stockregister/receivedstock/<?php echo date("Y-m-d", strtotime($row['date'])); ?>/<?php echo $row['item_id']; ?>">
                    <?php echo number_format($row['received'], 2); ?>
                </a>
            <?php } else { ?>
                0
            <?php } ?>
        </td>
        <td>
            <?php if ($row['dispatched'] > 0) { ?>
                <a target="_blank" href="<?php echo SITE_URL; ?>admin/stockregister/dispatchedstock/<?php echo date("Y-m-d", strtotime($row['date'])); ?>/<?php echo $row['item_id']; ?>">
                    <?php echo number_format($row['dispatched'], 2); ?>
                </a>
            <?php } else { ?>
                0
            <?php } ?>
        </td>
        <td><?php echo number_format($row['closing'], 2); ?></td>
    </tr>
<?php 
    } 
} else {
?>
    <tr>
        <td colspan="10" align="center">No Record Found</td>
    </tr>
<?php 
}
endif; 
?>