<div class="box-body" id="example23" style="padding:0px; margin-top:10px;">
    <table class="table table-bordered table-striped" width="100%">
        <thead>
            <tr>
                <th>S.No.</th>
                <th style="width: 7%;">Date</th>
                <th style="width: 6%;">BG For</th>
                <th>BG No.</th>
                <th>Favour of</th>
                <th>PO.No./Tender No.</th>
                <th>Amount</th>
                <th style="width: 7%;">Vaild Upto</th>
                <th style="width: 7%;">Last date of supply</th>
                <th>Contect Person</th>
                <th style="width: 6%;">Action</th>

            </tr>
        </thead>
        <tbody>
            <?php
            // pr($this->request->params);exit;
            $page = $this->request->params['paging']['EmdGuarantees']['page'];
            $limit = $this->request->params['paging']['EmdGuarantees']['perPage'];
            $counter = ($page * $limit) - $limit + 1;
            if (isset($EmdGuarantees) && !empty($EmdGuarantees)) {
                foreach ($EmdGuarantees as $bg) {
                    $lastremark = $this->Comman->getemdremark($bg['id']);
            ?>
                    <tr>
                        <td><?php echo $counter; ?></td>
                        <td><?php echo !empty($bg['datefrom']) ? date('d-m-Y', strtotime($bg['datefrom'])) : '-'; ?></td>
                        <td><?php echo h($bg['bg_for']); ?></td>
                        <td><a href="<?php echo SITE_URL; ?>admin/emd/viewremarks/<?php echo $bg['id']; ?>"
                                class="designsheetdetails"><?php echo $bg['bankguaranteeno']; ?></a></td>
                        <td><?php echo !empty($bg['favour_of']) ? $bg['favour_of'] : '-';
                            ?></td>
                        <td><?php echo
                            !empty($bg['po_no']) ? $bg['po_no'] : '-'; ?></td>
                        <td>
                            <?php
                            $symbol = '';
                            if ($bg['currency_type'] == 'USD') {
                                $symbol = '$';
                            } elseif ($bg['currency_type'] == 'INR') {
                                $symbol = '';
                            } elseif ($bg['currencyk_type'] == 'EUR') {
                                $symbol = '€';
                            } elseif ($bg['currency_type'] == 'GBP') {
                                $symbol = '£';
                            }
                            echo h($symbol . $bg['amount']);
                            ?>
                        </td>
                        <td><?php echo !empty($bg['validupto']) ? date('d-m-Y', strtotime($bg['validupto'])) : '-'; ?></td>

                        <td><?php echo !empty($bg['lastdate']) ? date('d-m-Y', strtotime($bg['lastdate'])) : '-'; ?></td>

                        <td><?php echo
                            !empty($bg['contect_per']) ? $bg['contect_per'] : '-'; ?></td>
                        <td>
                            <?php
                            $role_permissions = $this->Permission->permissioncheck();
                            $fileurl = "admin/emd/status";
                            if (in_array($fileurl, $role_permissions)) {
                                if ($bg['status'] == 'Y') {
                                    echo $this->Html->link('', [
                                        'action' => 'status',
                                        $bg->id,
                                        'N'
                                    ], ['title' => 'Completed', 'class' => 'fa fa-check-circle', 'style' => 'font-size: 15px !important;  color: #36cb3c;']);
                                } else {
                                    echo $this->Html->link('', [
                                        'action' => 'status',
                                        $bg->id,
                                        'Y'
                                    ], ['title' => 'Pending', 'class' => 'fa fa-times-circle-o', 'style' => 'font-size: 15px !important;  color:#FF5722;']);
                                }
                            }
                            $fileurl = "admin/emd/edit";
                            if (in_array($fileurl, $role_permissions)) {
                                echo $this->Html->link('', [
                                    'action' => 'edit',
                                    $bg->id
                                ], ['class' => 'fas fa-edit', 'style' => 'font-size: 18px;  padding-left: 6px;padding-right: 6px;']);
                            }
                            $fileurl = "admin/emd/delete";
                            if (in_array($fileurl, $role_permissions)) {
                                echo $this->Html->link('', [
                                    'action' => 'delete',
                                    $bg->id
                                ], ['class' => 'fas fa-trash-alt', 'style' => 'font-size: 18px; color:#c12020;', "onClick" => "javascript: return confirm('Are you sure you want to delete this EMD ?')"]);
                            }
                            ?>
                        </td>
                    </tr>
            <?php
                    $counter++;
                }
            } else {
                echo '<tr><td colspan="15" style="text-align:center;">No Records Found</td></tr>';
            }
            ?>
        </tbody>

    </table>
    <?php echo $this->element('admin/pagination'); ?>
</div>


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