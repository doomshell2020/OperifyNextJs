<div class="content-wrapper">
    <section class="content-header">
        <h1>JC Receive List</h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Job Challan Receives</h3>
                        <a href="<?php echo SITE_URL; ?>admin/jobchallan/receiveAdd" class="btn btn-primary pull-right">
                            <i class="fa fa-plus"></i> Add New Receive
                        </a>
                    </div>
                    <div class="box-body">
                        <table id="" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Challan No</th>
                                    <th>From Company</th>
                                    <th>Vehicle No</th>
                                    <th>JC Date</th>
                                    <th>Item Name</th>
                                    <th>Dispatch Qty</th>
                                    <th>Received Qty</th>
                                    <th>Pending Qty</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                // Default to JobChallans, fallback if not set
                                $modelName = isset($this->request->params['paging']['JobChallanReceives']) ? 'JobChallanReceives' : 'JobChallans';
                                $page = isset($this->request->params['paging'][$modelName]['page']) ? $this->request->params['paging'][$modelName]['page'] : 1;
                                $limit = isset($this->request->params['paging'][$modelName]['perPage']) ? $this->request->params['paging'][$modelName]['perPage'] : 10;
                                $counter = ($page * $limit) - $limit + 1;
                                ?>
                                <?php if (!empty($receives)): ?>
                                    <?php foreach ($receives as $row): 
                                        $pending = $row['dispatch_qty'] - $row['total_received'];
                                    ?>
                                        <tr>
                                            <td><?= $counter++ ?></td>
                                            <td>
                                                <a href="<?= SITE_URL ?>admin/jobchallan/jcinfo/<?= h($row['challan_id']) ?>">
                                                    <?= h($row['challan_no']) ?>
                                                </a>
                                            </td>
                                            <td><?= h($row['sender_name']) ?></td>
                                            <td><?= h($row['vehicle_no']) ?></td>
                                            <td><?= !empty($row['jc_date']) ? date('d-m-Y', strtotime($row['jc_date'])) : '' ?></td>
                                            <td><?= h($row['item_name']) ?></td>
                                            <td><?= h($row['dispatch_qty']) ?></td>
                                            <td><?= h($row['total_received']) ?></td>
                                            <td><?= h($pending) ?></td>
                                            <td>
                                                <?php if ($pending > 0): ?>
                                                    <a href="<?= SITE_URL ?>admin/jobchallan/receiveAdd/<?= h($row['challan_id']) . '|' . h($row['sender_db']) ?>" class="btn btn-sm btn-success">Receive</a>
                                                <?php else: ?>
                                                    <span class="label label-primary">Completed</span>
                                                <?php endif; ?>
                                                <a href="<?= SITE_URL ?>admin/jobchallan/viewpdf/<?= h($row['challan_id']) ?>?sender_db=<?= h($row['sender_db']) ?>" target="_blank" style="margin-left: 10px;" title="Download PDF">
                                                    <i class="fa fa-file-pdf-o" style="font-size: 16px; color: #cd0404;"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="10" class="text-center">No JCs available for receive.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="box-footer clearfix">
                        <?php echo $this->element('admin/pagination'); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>


