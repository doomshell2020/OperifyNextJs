<table class="table table-bordered table-striped" width="100%">
    <thead>
        <tr>
            <th width="3%">S.No.</th>
            <th width="20%">Contract Name</th>
            <th width="10%">Project Cost</th>
            <th width="10%">Operational Cost</th>
            <th width="10%">Labour Cost</th>
            <th width="30%">Comment</th>
            <th width="9%">Generated Date</th>
            <th width="9%">Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $i = 1;

        foreach ($users as $value) {
            //   pr($value);die;
        

            $contractname = $this->comman->findcontractname($value['contract_id']);
            ?>
            <tr>
                <td><?php echo $i; ?>.</td>
                <td><a href="<?php echo SITE_URL; ?>admin/production/viewcontractdetail/<?php echo $value['contract_id']; ?>"
                        class="viewdetails"><?php echo $value['title'] . '(' . $value['workorder'] . ')'; ?></a>
                </td>

                <td style="text-align:end;"><?php echo sprintf('%.2f', $value['cost']); ?>
                </td>
                <td style="text-align:end;"><?php echo sprintf('%.2f', $value['operation_cost']); ?>
                </td>
                <td style="text-align:end;"><?php echo sprintf('%.2f', $value['labour_cost']); ?>
                </td>
                <td><?php echo $value['description']; ?></td>
                <td><?php echo date("d-m-Y", strtotime($value['issuedate'])); ?></td>
                <td>

                    <?php
                    $getpro = $this->comman->checkproductionorder($value['contract_id']);

                    echo $this->Html->link('', [
                        'action' => 'editaddbom',
                        $value->id,
                    ], ['class' => 'fas fa-edit', 'style' => 'font-size: 16px !important;']);
                    ?>
                </td>
            </tr>
            <?php
            $i++;
        } ?>
    </tbody>
</table>
<?php echo $this->element('admin/pagination'); ?>

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