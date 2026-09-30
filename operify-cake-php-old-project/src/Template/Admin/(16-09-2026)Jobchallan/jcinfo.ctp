<div class="content-wrapper">

<section class="content-header">
    <h1>Job Challan Item History</h1>
</section>

<section class="content">

<div class="box">
<div class="box-header">
    <h3 class="box-title">Item Receive Report</h3>
</div>

<div class="box-body">

<table class="table table-bordered table-striped">

<thead style="background:#3c8dbc; color:#fff;">
<tr>
    <th>#</th>
    <th>Item Name</th>
    <th>Receive Date</th>
    <th>Received Qty</th>
</tr>
</thead>

<tbody>

<?php if(!empty($dispatch_item_details)): ?>

<?php $i=1; foreach($dispatch_item_details as $row): ?>

    <!-- ITEM HEADER -->
    <tr style="background:#f5f5f5;">
        <td colspan="4">
            <strong><?= h($row['item_name']) ?> (Dispatch: <?= $row['total_qty'] ?>)</strong>
        </td>
    </tr>

    <!-- HISTORY -->
    <?php if(!empty($history)):  ?>

        <?php foreach($history as $value):  ?>
        <tr>
            <td><?= $i++ ?></td>
            <td><?= $value->additem->item_name ?></td>
            <td><?= date('d-m-Y', strtotime($value->receive_date)) ?></td>
            <td><?= $value->received_qty ?></td>
        </tr>
        <?php endforeach; ?>

    <?php else: ?>

        <tr>
            <td colspan="3" align="center">No Receive Data</td>
        </tr>

    <?php endif; ?>

<?php endforeach; ?>

<?php else: ?>

<tr>
    <td colspan="3" align="center">No Data Found</td>
</tr>

<?php endif; ?>

</tbody>

</table>

<br>

<a href="<?= ADMIN_URL ?>jobchallan/index" class="btn btn-primary">
    Back
</a>

</div>
</div>

</section>
</div>