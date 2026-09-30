<div class="content-wrapper">

<section class="content-header">
    <h1>Job Challan Details & Tracking</h1>
</section>

<section class="content">

<div class="box">
<div class="box-header">
    <h3 class="box-title">
        Job Challan: <?= h($jobChallan->challan_no) ?>
        <?php if (!empty($jobChallan->vehicle_no)): ?>
            &nbsp;&nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp;&nbsp; <b>Dispatch Vehicle No:</b> <?= h($jobChallan->vehicle_no) ?>
        <?php endif; ?>
    </h3>
</div>

<div class="box-body">

<h4 style="margin-top:20px; border-bottom:1px solid #ccc; padding-bottom:10px;">Item Receipts History</h4>
<table class="table table-bordered table-striped">
<thead style="background:#3c8dbc; color:#fff;">
<tr>
    <th>#</th>
    <th>Item Name</th>
    <th>Receive Date</th>
    <th>Received Qty</th>
    <th>Vehicle No</th>
    <th>Action</th>
</tr>
</thead>
<tbody>
<?php if(!empty($dispatch_item_details)): ?>
<?php $i=1; foreach($dispatch_item_details as $row): ?>
    <tr style="background:#f5f5f5;">
        <td colspan="6">
            <strong><?= h($row['item_name']) ?> (Dispatch: <?= $row['total_qty'] ?>)</strong>
        </td>
    </tr>
    <?php 
    $hasHistory = false;
    if(!empty($history)):  
        foreach($history as $value):  
            if ($value->additem->item_name == $row['item_name']):
                $hasHistory = true;
    ?>
        <tr>
            <td><?= $i++ ?></td>
            <td><?= h($value->additem->item_name) ?></td>
            <td><?= date('d-m-Y', strtotime($value->receive_date)) ?></td>
            <td><?= $value->received_qty ?></td>
            <td><?= h($value->vehicle_no) ?></td>
            <td>
                <a href="<?= SITE_URL ?>admin/jobchallan/viewreturnpdf/<?= $value->id ?>" target="_blank" class="btn btn-xs btn-danger">
                    <i class="fa fa-file-pdf-o"></i> Print Return Challan
                </a>
            </td>
        </tr>
    <?php   endif;
        endforeach; 
    endif; 
    if (!$hasHistory): ?>
        <tr><td colspan="6" align="center">No Receive Data for this item</td></tr>
    <?php endif; ?>
<?php endforeach; else: ?>
<tr><td colspan="6" align="center">No Data Found</td></tr>
<?php endif; ?>
</tbody>
</table>


<h4 style="margin-top:40px; border-bottom:1px solid #ccc; padding-bottom:10px;">Production & Return Tracking</h4>
<div class="table-responsive">
<table class="table table-bordered table-striped">
<thead style="background:#00a65a; color:#fff;">
<tr>
    <th>Raw Material Item</th>
    <th>Dispatched</th>
    <th>Received</th>
    <th>Consumed (Indents)</th>
    <th>RM Balance</th>
</tr>
</thead>
<tbody>
<?php foreach($dispatch_item_details as $row): 
    $itemName = $row['item_name'];
    $dispatched = $row['total_qty'];
    $received = 0;
    foreach($history as $h) {
        if ($h->additem->item_name == $itemName) {
            $received += $h->received_qty;
        }
    }
    $consumed = isset($tracking[$itemName]) ? $tracking[$itemName]['consumed'] : 0;
    $balance = isset($tracking[$itemName]) ? $tracking[$itemName]['balance'] : 0;
?>
    <tr>
        <td><?= h($itemName) ?></td>
        <td><?= $dispatched ?></td>
        <td><?= $received ?></td>
        <td><?= $consumed ?></td>
        <td><?= $balance ?></td>
    </tr>
<?php endforeach; ?>
</tbody>
</table>
</div>

<h4 style="margin-top:40px; border-bottom:1px solid #ccc; padding-bottom:10px;">Returned Items (Finished Goods / Unused RM)</h4>
<div class="table-responsive">
<table class="table table-bordered table-striped">
<thead style="background:#dd4b39; color:#fff;">
<tr>
    <th>Returned Item</th>
    <th>Return Type</th>
    <th>Production Output (If FG)</th>
    <th>Returned Qty</th>
</tr>
</thead>
<tbody>
<?php if (!empty($returnedItems)): foreach($returnedItems as $itemName => $rData): ?>
    <tr>
        <td><?= h($itemName) ?></td>
        <td><?= h($rData['type']) ?></td>
        <td><?= $rData['output'] ?></td>
        <td><?= $rData['quantity'] ?></td>
    </tr>
<?php endforeach; else: ?>
    <tr><td colspan="4" align="center">No returns initiated yet</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>

<br>
<a href="<?= ADMIN_URL ?>jobchallan/index" class="btn btn-primary">Back</a>

</div>
</div>

</section>
</div>

<script>
    $(document).on('click', '.addsupplier_modal', function(e) {
        e.preventDefault();
        $('#cancelsorts').modal('show').find('.modal-body').load($(this).attr('href'));
    });
</script>
<div class="modal fade" id="cancelsorts">
    <div class="modal-dialog" style="max-width:999px !important;">
        <div class="modal-content">
            <div class="modal-body"></div>
        </div>
    </div>
</div>