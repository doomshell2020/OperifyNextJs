<table id="example14" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>S.No</th>
            <th>Contract Name</th>
            <th>Name</th>
            <th>Remark</th>
            <th>Inspection Date</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php $page = $this->request->params['paging']['']['page'];
        $limit = $this->request->params['paging']['']['perPage'];
        $counter = ($page * $limit) - $limit + 1;
        if (isset($users) && !empty($users)) {
            foreach ($users as $intusr) {
                $contractname = $this->Comman->findcontractname($intusr['work_order_no']);
                $bomid = $this->Comman->findbomdetails($intusr['work_order_no']);
                ?>
                <tr>
                    <td>
                        <?php echo $counter; ?>
                    </td>
                    <td><a href="<?php echo SITE_URL; ?>admin/production/viewcontractdetail/<?php echo $bomid['id']; ?>/<?php echo $intusr['work_order_no']; ?>"
                            class="viewdetails">
                            <?php echo $contractname['title'] . '(' . $contractname['workorder'] . ')'; ?>
                        </a></td>
                    <td>
                        <?php echo $intusr['name']; ?>
                    </td>
                    <td>
                        <?php echo $intusr['remark']; ?>
                    </td>
                    <td>
                        <?php echo date('Y-m-d', strtotime($intusr['inspection_date'])); ?>
                    </td>
                    <td><a target="_blank" href="<?php echo SITE_URL . 'InspectionReport/' . $intusr['file']; ?>"
                            title="Download Inspection Report"><span class="fa fa-download fa-lg text-green"></span></a>
                        &nbsp;
                        <?php
                        echo $this->Html->link('', [
                            'action' => 'delete',
                            $intusr->id
                        ], [
                            'class' => 'fas fa-trash-alt', 'style' => 'font-size: 16px !important; color:#cd0404; margin-right:4px !important;', "onClick" => "javascript: return confirm('Are you sure do you want to delete this Inspection Report')"
                        ]); ?>


                    </td>

                </tr>
                <?php $counter++;
            }
        } else { ?>
        <?php } ?>
    </tbody>
</table>

<script>
    $('.viewdetails').click(function (e) {
        e.preventDefault();
        $('#editsorts').modal('show').find('.modal-body').load($(this).attr('href'));
    });
</script>

<div class="modal fade" id="editsorts">
    <div class="modal-dialog" style="max-width:900px !important;">
        <div class="modal-content">
            <div class="modal-body"></div>
        </div>
    </div>
</div>