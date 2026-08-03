<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th width="6%">Return Id</th>
            <th width="7%">Return Date</th>
            <th width="17">Vendor Name</th>
            <th width="5%">Bill No.</th>
            <th width="7%">Bill Date</th>
            <th width="7%">GRN No.</th>
            <th width="5%">PO Id</th>
            <th width="7%">Amount</th>
            <th width="28%">Description</th>
            <th width="11%">Status</th>
            <th width="6%">Action</th>
        </tr>
    </thead>
    <tbody>
        <?php $page = $this->request->params['paging']['purchasereturn']['page'];
        $limit = $this->request->params['paging']['purchasereturn']['perPage'];
        $counter = ($page * $limit) - $limit + 1;
        if (isset($purchasereturn) && !empty($purchasereturn)) {
            foreach ($purchasereturn as $intusr) {
                $vendor_id = $this->Comman->findvendornames($intusr['vendor_id']);
                ?>
                <tr>
                    <td> <a href="<?php echo SITE_URL; ?>admin/Purchasereturn/view/<?php echo $intusr['id']; ?>"
                            class="viewpurchasereturn">
                            <?php echo $intusr['id']; ?> </a></td>

                    <td> <?php echo date('d-m-Y', strtotime($intusr['retrundate'])); ?> </td>

                    <td> <a href="<?php echo SITE_URL; ?>admin/Purchasereturn/viewdetails/<?php echo $vendor_id['name']; ?>"
                            class="editpurchasedetails">
                            <?php echo $vendor_id['name']; ?> </a> </td>
                    <td> <?php echo $intusr['bill_no']; ?> </td>
                    <td> <?php echo date('d-m-Y', strtotime($intusr['bill_date'])); ?> </td>
                    <td> <?php echo $intusr['grn_no']; ?> </td>
                    <td> <?php echo $intusr['purchaseorder_id']; ?> </td>
                    <td style="text-align:right;"> <?php echo sprintf('%.2f', $intusr['amount']); ?> </td>
                    <td> <?php echo $intusr['description']; ?> </td>
                    <td>
                        <?php
                        $user_id = $_SESSION['Auth']['User']['id'];
                        $controllerName = $this->request->params['controller'];
                        $actionName = "index";
                        $user_permission = $this->comman->finduserpermisson($user_id, $controllerName, $actionName);

                        if ($user_permission['edit'] == '1') {
                            if ($intusr['status'] == 'Active') {
                                echo $this->Html->link('', [
                                    'action' => 'status',
                                    $intusr->id,
                                    'InActive'
                                ], [
                                    'id' => 'Inactive',
                                    'class' => 'fa fa-check-circle',
                                    'style' => 'color: #36cb3c;  
                                                    font-size: 20px !important;'
                                ]);
                            } else {
                                echo $this->Html->link('', [
                                    'action' => 'status',
                                    $intusr->id,
                                    'Active'
                                ], [
                                    'id' => 'InActive',
                                    'class' => 'fa fa-times-circle-o',
                                    'style' => 'color:#FF5722; 
                                                    font-size: 20px !important;'
                                ]);
                            }
                        } ?>
                    </td>
                    <td>
                        <div style="display:flex; align-items: center;">
                            <a target="_blank" title="View PDF"
                                href="<?php echo ADMIN_URL; ?>Purchasereturn/view/<?php echo $intusr['id']; ?>"
                                style="color:#2d95e3;  margin-right:5px;">
                                <i class="far fa-file-pdf" style=" font-size: 18px !important;"></i>
                            </a>
                            <?php
                            if ($user_permission['delete'] == '1') {
                                echo $this->Html->link('', [
                                    'action' => 'delete',
                                    $intusr['id']
                                ], [
                                    'class' => 'fas fa-trash-alt',
                                    'style' => 'font-size: 18px !important; color:#cd0404; !important;',
                                    "onClick" => "javascript: return confirm('Are you sure do you want to delete this Record')"
                                ]);
                            }
                            ?>
                        </div>
                    </td>
                </tr>
                <?php $counter++;
            }
        } else { ?>
        <?php } ?>
    </tbody>
</table>
<?php echo $this->element('admin/pagination'); ?>