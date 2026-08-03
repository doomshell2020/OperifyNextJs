<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>S.No.</th>
            <th>Particular's</th>
            <th>Consignee</th>
            <th>PO No.</th>
            <th style="width: 10%;">Invoice No.</th>
            <th style="width: 15%;">Date</th>
            <th style="width: 10%;">Amount</th>
            <th style="width: 10%;">Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $page = $this->request->params['paging']['Particularpayments']['page'];
        $limit = $this->request->params['paging']['Particularpayments']['perPage'];
        $counter = ($page * $limit) - $limit + 1;
        if (!empty($users)) :
            foreach ($users as $intusr) :
                $receivedAmount = $this->Comman->getReceivedTotalAmount($intusr['id']);
                $pendingAmount = $intusr['amount'] - $receivedAmount;
        ?>
                <tr>
                    <td><?= $counter ?></td>
                    <td><?= $intusr['particular'] ?? '-' ?></td>
                    <td><?= $intusr['consignee'] ?? '-' ?></td>
                    <td><?= $intusr['po_no'] ?? '-' ?></td>
                    <td><?= $intusr['invoice'] ?? '-' ?></td>
                    <td>
                        <?= 'Date: ' . (!empty($intusr['datefrom']) ? date('d-m-Y', strtotime($intusr['datefrom'])) : '-') ?><br>
                        <?= 'Bill Dispatch Date: ' . (!empty($intusr['bill_dis_date']) ? date('d-m-Y', strtotime($intusr['bill_dis_date'])) : '-') ?><br>
                        <br>


                    </td>

                    <td>
                        <div style="display: flex; justify-content: space-between;">
                            <span>Total:</span>
                            <span>
                                <a href="<?= SITE_URL ?>admin/paymentmanager/viewamount/<?= $intusr['id']; ?>" class="designsheetdetails" style="text-decoration: none;">
                                    <?= number_format($intusr['amount']) ?>
                                </a>
                            </span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span>Received:</span>
                            <span><?= number_format($receivedAmount) ?></span>
                        </div>
                        <?php if ($pendingAmount != 0) { ?>
                            <div style="display: flex; justify-content: space-between;">
                                <span>Pending:</span>
                                <span><?= number_format($pendingAmount) ?></span>
                            </div>
                        <?php } ?>
                    </td>

                    <td>

                        <?php
                        if (!empty($intusr['amount']) && isset($receivedAmount) && $intusr['amount'] == $receivedAmount) {
                            // Payment completed
                            echo '<strong style="color:green;">Completed</strong>';
                        } elseif (!empty($intusr['bill_dis_date']) && !empty($intusr['due_period'])) {
                            echo 'Due Period: ' . $intusr['due_period'] . ' Days<br>';

                            $billDate = new DateTime($intusr['bill_dis_date']);
                            $dueDays = (int)$intusr['due_period'];
                            $dueDate = clone $billDate;
                            $dueDate->modify("+$dueDays days");

                            $today = new DateTime();
                            $interval = $today->diff($dueDate);
                            $daysDiff = $interval->days;

                            if ($today > $dueDate) {
                                // Overdue
                                echo '<span style="color:red;">Overdue By: ' . $daysDiff . ' Days</span>';
                            } else {
                                // Remaining time
                                echo 'Remaining Days: ' . $daysDiff;
                            }
                        } else {
                            echo '-';
                        }
                        ?>
                    </td>

                    <td>
                        <strong>
                            <?php
                            $role_permissions = $this->Permission->permissioncheck();
                            $fileurl = "admin/paymentmanager/edit";
                            if (in_array($fileurl, $role_permissions)) {
                            ?>
                                <?= $this->Html->link('', ['action' => 'edit', $intusr->id], ['class' => 'fas fa-edit', 'style' => 'font-size: 21px;']) ?>
                                &nbsp;
                            <?php } ?>
                            <?php
                            $role_permissions = $this->Permission->permissioncheck();
                            $fileurl = "admin/paymentmanager/delete";
                            if (in_array($fileurl, $role_permissions)) {
                            ?>
                                <?= $this->Html->link('', ['action' => 'delete', $intusr->id], [
                                    'class' => 'fas fa-trash-alt',
                                    'style' => 'font-size: 21px; color:#cd0404',
                                    'onClick' => "return confirm('Are you sure do you want to delete this Payment Detail?')"
                                ]) ?>
                            <?php } ?>
                        </strong>
                    </td>
                </tr>

        <?php
                $counter++;
            endforeach;
        endif;
        ?>
    </tbody>
    <?php if (!empty($users) && !empty($bg_for)) { ?>
        <tfoot>
            <tr>
                <td colspan="6" style="text-align:right;"><strong></strong></td>
                <td colspan="3">
                    <?php
                    $totalReceived = 0;
                    if (!empty($users)) {
                        foreach ($users as $users) {
                            $totalReceived += $this->Comman->getReceivedTotalAmount($users['id']);
                            $total_amount += $users['amount'];
                        }
                    }
                    ?>
                    <strong style="display: inline !important;">Total Amount = </strong> <?= $total_amount ?>
                    <strong style="display: inline !important;">Total Received Amount = </strong> <?= $totalReceived ?>
                </td>
            </tr>
        </tfoot>
    <?php } ?>
</table>
<?php echo $this->element('admin/pagination'); ?>


<div class="modal fade" id="globalModalbag" style="width:51% !important;" tabindex="-1" role="dialog" aria-labelledby="esModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width:100% !important;">
        <div class="modal-content personal">
            <div class="modal-body">
                <div class="col-sm-6 col-md-6 col-sm-offset-2 col-md-offset-2">
                </div>
                <div class="loader">
                    <div class="es-spinner">
                        <i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#sevice_form').on('submit', function(e) {
            $("#formsubmitbtn").css("display", "none");
        });
    });
</script>


<script>
    $('.designsheetdetails').click(function(e) {
        e.preventDefault();
        $('#designsorts').modal('show').find('.modal-body').load($(this).attr('href'));
    });
</script>

<div class="modal fade" id="designsorts">
    <div class="modal-dialog" style="max-width:900px !important;">
        <div class="modal-content">
            <div class="modal-body"></div>
        </div>
    </div>
</div>