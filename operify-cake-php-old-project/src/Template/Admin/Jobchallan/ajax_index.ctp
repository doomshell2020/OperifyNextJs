<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Challan No</th>
                <th>Date</th>
                <th>Vendor</th>
                <th>Vehicle No</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php 
        $page = $this->request->params['paging']['JobChallans']['page'];
        $limit = $this->request->params['paging']['JobChallans']['perPage'];
        $counter = ($page * $limit) - $limit + 1;
        ?>
            <?php if (!empty($jobChallans) && !$jobChallans->isEmpty()): ?>
                <?php foreach ($jobChallans as $row):  ?>
                    <tr>
                        <td><?= $counter++ ?></td>
                        <td><a href="<?= ADMIN_URL ?>jobchallan/jcinfo/<?= $row->id ?>"><?= $row->challan_no ?></a></td>
                        <td><?= date('d-m-Y', strtotime($row->jc_date)) ?></td>
                        <td><?= $row->sub_contractor->name ?></td>
                        <td><?= h($row->vehicle_no) ?></td>
                        <td>
                            <span class="label label-info"><?= $row->status ?></span>
                        </td>
                        <td>
                            <a href="<?= ADMIN_URL ?>jobchallan/view/<?= $row->id ?>" class="btn btn-primary btn-sm">View</a>
                            <a href="<?= ADMIN_URL ?>jobchallan/viewpdf/<?= $row->id ?>" target="_blank" class="btn btn-default btn-sm" title="Print JC">
                                <i class="fa fa-file-pdf-o"></i>
                            </a>
                            <?php
                            echo $this->Form->postLink('', [
                                'action' => 'delete',
                                $row->id
                            ], [
                                'class' => 'fas fa-trash-alt',
                                'style' => 'font-size: 16px !important; color:#cd0404; margin-right:4px !important;',
                                "confirm" => "Are you sure you want to delete this Item?"
                            ]); ?>
                        </td>
                    </tr>
                <?php 
                endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" align="center">No Data Available</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="box-footer clearfix">
    <?php echo $this->element('admin/pagination'); ?>
</div>
