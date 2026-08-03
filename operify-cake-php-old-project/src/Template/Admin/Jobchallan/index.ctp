<div class="content-wrapper">

    <section class="content-header">
        <h1>Job Challan Report</h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo SITE_URL; ?>"><i class="fa fa-home"></i>Home</a></li>
        </ol>
    </section>

    <section class="content">

        <div class="row">
            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <?php echo $this->Flash->render(); ?>
                        <h3 class="box-title">Search Job Challan</h3>
                    </div>

                    <div class="box-body">

                        <?php echo $this->Form->create(null, ['type' => 'get']); ?>

                        <div class="row">

                            <div class="form-group col-sm-4">
                                <label>From Date</label>
                                <input type="date" name="from_date" class="form-control"
                                    value="<?= $this->request->query('from_date') ?>">
                            </div>

                            <div class="form-group col-sm-4">
                                <label>To Date</label>
                                <input type="date" name="to_date" class="form-control"
                                    value="<?= $this->request->query('to_date') ?>">
                            </div>

                            <div class="form-group col-sm-4">
                                <label>Vendor</label>
                                <?= $this->Form->select('vendor_id', $vendors, [
                                    'empty' => 'All',
                                    'class' => 'form-control',
                                    'value' => $this->request->query('vendor_id')
                                ]) ?>
                            </div>

                        </div>

                        <div class="row">

                            <div class="form-group col-sm-4">
                                <label>Status</label>
                                <?= $this->Form->select('status', [
                                    'Created' => 'Created',
                                    'Dispatched' => 'Dispatched',
                                    'In Progress' => 'In Progress',
                                    'Returned' => 'Returned',
                                    'Closed' => 'Closed'
                                ], [
                                    'empty' => 'All',
                                    'class' => 'form-control',
                                    'value' => $this->request->query('status')
                                ]) ?>
                            </div>

                            <div class="form-group col-sm-4">
                                <label>Challan No</label>
                                <input type="text" name="challan_no" class="form-control"
                                    placeholder="Enter Challan No"
                                    value="<?= $this->request->query('challan_no') ?>">
                            </div>

                            <div class="form-group col-sm-4">
                                <label>&nbsp;</label><br>

                                <button type="submit" class="btn btn-success">
                                    Search
                                </button>

                                <a href="<?= ADMIN_URL ?>Jobchallan/index" class="btn btn-primary">
                                    Reset
                                </a>

                                <a href="<?= ADMIN_URL ?>Jobchallan/add" class="btn btn-primary">
                                    Add
                                </a>
                            </div>



                        </div>

                        <?php echo $this->Form->end(); ?>

                    </div>
                </div>

            </div>
        </div>

        <!-- TABLE -->
        <div class="row">
            <div class="col-xs-12">

                <div class="box">
                    <div class="box-body">

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">

                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Challan No</th>
                                        <th>Date</th>
                                        <th>Vendor</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    <?php $i = 1; ?>
                                    <?php if (!empty($jobChallans)): ?>

                                        <?php foreach ($jobChallans as $row):  ?>
                                            <tr>

                                                <td><?= $i ?></td>
                                                <td><a href="<?= ADMIN_URL ?>jobchallan/jcinfo/<?= $row->id ?>"><?= $row->challan_no ?></a></td>
                                                <td><?= date('d-m-Y', strtotime($row->jc_date)) ?></td>
                                                <td><?= $row->sub_contractor->name ?></td>

                                                <td>
                                                    <span class="label label-info"><?= $row->status ?></span>
                                                </td>

                                                <td>

                                                    <a href="<?= ADMIN_URL ?>jobchallan/view/<?= $row->id ?>" class="btn btn-primary btn-sm">View</a>

                                                    <a href="<?= ADMIN_URL ?>jobchallan/viewpdf/<?= $row->id ?>" target="_blank">
                                                        <i class="fa fa-file-pdf-o"></i>
                                                    </a>

                                                    <a title="Job Challan Item Received" href="<?php echo ADMIN_URL; ?>jobchallan/itemreceived/<?= $row->id ?>/<?= $row->job_challan_items[0]->item_id ?>"
                                                        style="color:#2d95e3;  margin-right:5px;" class="addsupplier_modal">
                                                        <i class="fa fa-plus" style=" font-size: 16px !important;"></i>
                                                    </a>
                                                    <?php
                                                    echo $this->Html->link('', [
                                                        'action' => 'delete',
                                                        $row->id
                                                    ], [
                                                        'class' => 'fas fa-trash-alt',
                                                        'style' => 'font-size: 16px !important; color:#cd0404; margin-right:4px !important;',
                                                        "onClick" => "javascript: return confirm('Are you sure do you want to delete this Item')"
                                                    ]); ?>


                                                </td>

                                            </tr>
                                        <?php $i++;
                                        endforeach; ?>

                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" align="center">No Data Available</td>
                                        </tr>
                                    <?php endif; ?>

                                </tbody>

                            </table>
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </section>
</div>

<script>
    $('.addsupplier_modal').click(function(e) {
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