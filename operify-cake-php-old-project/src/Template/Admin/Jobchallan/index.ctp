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

                        <?php echo $this->Form->create(null, ['type' => 'get', 'id' => 'ajaxSearchForm']); ?>

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
                                    'Pending' => 'Pending',
                                    'Partially Returned' => 'Partially Returned',
                                    'Completed' => 'Completed',
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

                                <button type="button" id="btnAjaxSearch" class="btn btn-success">
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
                    <div class="box-body" id="tableContainer">
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
                    </div>
                </div>

            </div>
        </div>

    </section>
</div>

<script>
$(document).ready(function() {
    function loadTable(url) {
        var data = $('#ajaxSearchForm').serialize();
        $.ajax({
            url: url,
            type: 'GET',
            data: data,
            success: function(response) {
                $('#tableContainer').html(response);
            }
        });
    }

    $('#btnAjaxSearch').click(function(e) {
        e.preventDefault();
        loadTable('<?= ADMIN_URL ?>jobchallan/index');
    });

    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        loadTable(url);
    });

    $('.addsupplier_modal').click(function(e) {
        e.preventDefault();
        $('#cancelsorts').modal('show').find('.modal-body').load($(this).attr('href'));
    });
});
</script>
<div class="modal fade" id="cancelsorts">
    <div class="modal-dialog" style="max-width:999px !important;">
        <div class="modal-content">
            <div class="modal-body"></div>
        </div>
    </div>
</div>