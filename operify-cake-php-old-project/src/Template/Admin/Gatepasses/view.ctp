<div class="content-wrapper">
    <section class="content-header">
        <h1>Gate Pass Details</h1>
        <ol class="breadcrumb">
            <li><a href="<?= ADMIN_URL ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?= ADMIN_URL ?>gatepasses">Gate Passes</a></li>
            <li class="active">View</li>
        </ol>
    </section>

    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= h($gatepass->gatepass_no) ?></h3>
                <div class="pull-right">
                    <a href="<?= ADMIN_URL ?>gatepasses/gatepasspdf/<?= $gatepass->id ?>" target="_blank" class="btn btn-warning"><i class="fa fa-ticket"></i> Print PDF</a>
                    <a href="<?= ADMIN_URL ?>gatepasses/edit/<?= $gatepass->id ?>" class="btn btn-primary"><i class="fa fa-pencil"></i> Edit</a>
                </div>
            </div>
            
            <?php echo $this->Flash->render(); ?>
            <div class="box-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="20%">Gate Pass No</th>
                        <td width="30%"><?= h($gatepass->gatepass_no) ?></td>
                        <th width="20%">Date</th>
                        <td width="30%"><?= h($gatepass->date) ? date('d-m-Y', strtotime($gatepass->date)) : '' ?></td>
                    </tr>
                    <tr>
                        <th>To Company</th>
                        <td><?= $gatepass->has('sub_contractor') ? h($gatepass->sub_contractor->name) : '' ?></td>
                        <th>Vehicle No</th>
                        <td><?= h($gatepass->vehicle_no) ?></td>
                    </tr>
                    <tr>
                        <th>Job Challan Ref</th>
                        <td><?= $gatepass->has('job_challan') ? h($gatepass->job_challan->challan_no) : '' ?></td>
                        <th>Return Date</th>
                        <td><?= h($gatepass->return_date) ? date('d-m-Y', strtotime($gatepass->return_date)) : '' ?></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td><?= h($gatepass->status) ?></td>
                        <th>Remarks</th>
                        <td><?= h($gatepass->remarks) ?></td>
                    </tr>
                </table>

                <h4 style="margin-top: 20px;">Items</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>S.No.</th>
                            <th>Item Code</th>
                            <th>Description of Item</th>
                            <th>Quantity</th>
                            <th>Unit</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Extract single JC No if applicable for legacy gatepasses
                        $single_jc = '';
                        if (!empty($gatepass->jc_id)) {
                            $jc_ids = explode(',', $gatepass->jc_id);
                            if (count($jc_ids) == 1 && $gatepass->has('job_challan')) {
                                $single_jc = $gatepass->job_challan->challan_no;
                            }
                        } else if ($gatepass->has('job_challan')) {
                            $single_jc = $gatepass->job_challan->challan_no;
                        }
                        
                        $sl = 1;
                        if (!empty($gatepass->gatepass_items)):
                            foreach ($gatepass->gatepass_items as $item): 
                                $desc = h($item->description);
                                if ($single_jc && strpos($desc, 'JC No') === false) {
                                    $desc .= ($desc ? "\n" : '') . 'JC No: ' . $single_jc;
                                }
                        ?>
                        <tr>
                            <td><?= $sl++ ?></td>
                            <td><?= h($item->item_id) ?></td>
                            <td>
                                <strong><?= $item->has('additem') ? h($item->additem->item_name) : '' ?></strong>
                                <?php if (!empty($desc)): ?>
                                    <br><span class="text-muted"><?= nl2br($desc) ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?= h($item->quantity) ?></td>
                            <td><?= h($item->unit) ?></td>
                            <td><?= h($item->remarks) ?></td>
                        </tr>
                        <?php 
                            endforeach;
                        else:
                        ?>
                        <tr>
                            <td colspan="6" class="text-center">No items found.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="box-footer">
                <a href="<?= ADMIN_URL ?>gatepasses" class="btn btn-default">Back to List</a>
            </div>
        </div>
    </section>
</div>
