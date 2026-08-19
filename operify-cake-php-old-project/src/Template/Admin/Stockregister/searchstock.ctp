<?php
$exportHtml = '<form action="' . ADMIN_URL . 'Stockregister/dailystockexcel" method="POST" target="_blank" style="margin:0;">';
$exportHtml .= '<input type="hidden" name="datefrom" value="' . (isset($datefrom) ? $datefrom : '') . '">';
if (!empty($category_ids)) {
    foreach ($category_ids as $cid) {
        $exportHtml .= '<input type="hidden" name="category_ids[]" value="' . $cid . '">';
    }
}
$exportHtml .= '<button type="submit" class="btn btn-link" style="padding:0; margin-top: 23px;"><i class="fa fa-file-excel-o" style="font-size:28px !important; margin-right:10px; color:#333;"></i></button>';
$exportHtml .= '</form>';
?>
<div style="text-align: right; margin-bottom: 10px;">
    <?php echo $exportHtml; ?>
</div>

<table class="table table-bordered table-striped" width="100%">
    <thead>
        <tr>
            <th>S.No</th>
            <th>Item Name</th>
            <th>Category</th>
            <th>Opening Stock</th>
            <th>Received Stock</th>
            <th>Issued Stock</th>
            <th>Reverse Stock</th>
            <th>Return Stock</th>
            <th>Closing Stock</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $cnt = 1;
        if (!empty($dailyStockData)): 
            foreach ($dailyStockData as $row): 
        ?>
            <tr>
                <td><?php echo $cnt++; ?></td>
                <td><?php echo h($row['item_name']); ?></td>
                <td><?php echo h($row['category_name']); ?></td>
                <td><?php echo $row['opening_stock']; ?></td>
                <td><?php echo $row['received_stock']; ?></td>
                <td><?php echo $row['issued_stock']; ?></td>
                <td><?php echo $row['reverse_stock']; ?></td>
                <td><?php echo $row['return_stock']; ?></td>
                <td><?php echo $row['closing_stock']; ?></td>
            </tr>
        <?php 
            endforeach;
        else: 
        ?>
            <tr>
                <td colspan="9" align="center">No Record Found</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<script>
    // The AJAX response replaces the inner HTML of #updt.
    // However, the original #updt already contains an empty table wrapper.
    // If we return the whole table here, we should remove the empty one from dailystock.ctp, 
    // or we can just replace the inner contents of #updt by keeping this table.
    // To be safe, we just return the full table HTML and let it replace everything in #updt.
</script>