<div class="content-wrapper">
    <section class="content-header">
        <h1>Gate Passes</h1>
        <ol class="breadcrumb">
            <li><a href="<?= ADMIN_URL ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Gate Passes</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <?php echo $this->Flash->render(); ?>
                        <h3 class="box-title">Gate Pass List</h3>
                        <div class="pull-right">
                            <a href="<?= ADMIN_URL ?>gatepasses/add" class="btn btn-primary">Add Gate Pass</a>
                        </div>
                    </div>
                    <div class="box-body" style="padding-bottom: 0;">
                        <form id="ajaxSearchForm">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Company</label>
                                        <?= $this->Form->input('sub_contractor_id', ['type' => 'select', 'options' => $subContractors, 'empty' => 'All Companies', 'class' => 'form-control select2', 'label' => false, 'value' => $this->request->query('sub_contractor_id')]) ?>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>From Date</label>
                                        <input type="date" name="from_date" class="form-control" value="<?= $this->request->query('from_date') ?>">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>To Date</label>
                                        <input type="date" name="to_date" class="form-control" value="<?= $this->request->query('to_date') ?>">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Limit</label>
                                        <?= $this->Form->input('limit', ['type' => 'select', 'options' => [20 => '20', 50 => '50', 100 => '100', 500 => '500'], 'class' => 'form-control', 'label' => false, 'value' => $this->request->query('limit') ?: 20]) ?>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group" style="margin-top: 25px;">
                                        <button type="button" class="btn btn-primary" id="btnAjaxSearch">Search</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="box-body table-responsive" id="tableContainer">
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
                            </tbody>
                        </table>
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
    if($('.select2').length) {
        $('.select2').select2();
    }
    
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
        loadTable('<?= ADMIN_URL ?>gatepasses/index');
    });

    // Handle pagination links
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        loadTable(url);
    });
});
</script>
