<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Gate Pass No</th>
            <th>Date</th>
            <th>Company</th>
            <th>Vehicle No</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $page = $this->request->params['paging']['Gatepasses']['page'];
        $limit = $this->request->params['paging']['Gatepasses']['perPage'];
        $counter = ($page * $limit) - $limit + 1;
        foreach ($gatepasses as $gatepass): ?>
        <tr>
            <td><?= $counter++ ?></td>
            <td><a href="<?= ADMIN_URL ?>gatepasses/view/<?= $gatepass->id ?>"><?= h($gatepass->gatepass_no) ?></a></td>
            <td><?= h($gatepass->date) ? date('d-m-Y', strtotime($gatepass->date)) : '' ?></td>
            <td><?= $gatepass->has('sub_contractor') ? h($gatepass->sub_contractor->name) : '' ?></td>
            <td><?= h($gatepass->vehicle_no) ?></td>
            <td><span class="label label-info"><?= h($gatepass->status) ?></span></td>
            <td class="actions">
                <a href="<?= ADMIN_URL ?>gatepasses/view/<?= $gatepass->id ?>" class="btn btn-primary btn-sm" title="View">View</a>
                <a href="<?= ADMIN_URL ?>gatepasses/edit/<?= $gatepass->id ?>" class="fas fa-edit" style="font-size: 16px !important;" title="Edit"></a>
                <a href="<?= ADMIN_URL ?>gatepasses/gatepasspdf/<?= $gatepass->id ?>" target="_blank" class="btn btn-default btn-sm" title="Print Gate Pass"><i class="fa fa-file-pdf-o"></i></a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if($gatepasses->isEmpty()): ?>
        <tr>
            <td colspan="7" class="text-center">No gate passes found.</td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>
<div class="box-footer clearfix">
    <?php echo $this->element('admin/pagination'); ?>
</div>
