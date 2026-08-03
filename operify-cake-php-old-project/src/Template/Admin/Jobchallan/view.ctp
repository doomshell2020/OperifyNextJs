<div class="content-wrapper">

<section class="content-header">
    <h1>Job Challan Details</h1>
</section>

<section class="content">

<div class="box">
<div class="box-header">
    <h3 class="box-title">Challan Info</h3>
</div>

<div class="box-body">

<div class="row">

    <div class="col-sm-4">
        <label>Challan No</label>
        <p><?= h($data->challan_no) ?></p>
    </div>

    <div class="col-sm-4">
        <label>Date</label>
        <p><?= date('d-m-Y', strtotime($data->jc_date)) ?></p>
    </div>

    <div class="col-sm-4">
        <label>Vendor</label>
        <p><?= h($data->sub_contractor->name) ?></p>
    </div>

</div>

<div class="row">

    <div class="col-sm-4">
        <label>Expected Days</label>
        <p><?= h($data->expected_days) ?> Days</p>
    </div>

    <div class="col-sm-4">
        <label>Status</label>
        <p>
            <span class="label label-info"><?= h($data->status) ?></span>
        </p>
    </div>

</div>

<div class="row">
    <div class="col-sm-12">
        <label>Work Description</label>
        <p><?= h($data->work_description) ?></p>
    </div>
</div>

<hr>

<!-- ITEMS TABLE -->
<div class="table-responsive">

<table class="table table-bordered">

<thead style="background:#3c8dbc; color:#fff;">
<tr>
    <th>#</th>
    <th>Item Name</th>
    <th>Quantity</th>
    <th>Rate</th>
    <th>Tax</th>
    <th>Tax Amount</th>
    <th>Total Amount</th>
</tr>
</thead>

<tbody>

<?php $i=1; $total=0; ?>

<?php foreach($data->job_challan_items as $item): ?>

<tr>
    <td><?= $i ?></td>
    <td><?= h($item->item_name) ?></td>
    <td><?= number_format((float)$item->quantity, 2, '.', ''); ?></td>
    <td><?= number_format((float)$item->rate, 2, '.', ''); ?></td>
    <td><?= h($item->tax_rate) . '%' ?></td>
    <td><?= number_format((float)$item->tax_amount, 2, '.', ''); ?></td>
    <td><?= number_format((float)$item->total_amount, 2, '.', ''); ?></td>
</tr>

<?php 
$total = number_format((float)$item->total_amount, 2, '.', '');
$i++; 
?>

<?php endforeach; ?>

</tbody>

<tfoot>
<tr>
    <th colspan="6" style="text-align:right;">Total</th>
    <th><?= $total ?></th>
</tr>
</tfoot>

</table>

</div>

<br>

<!-- BUTTONS -->
<a href="<?= ADMIN_URL ?>jobchallan/index" class="btn btn-primary">Back</a>

</div>
</div>

</section>
</div>