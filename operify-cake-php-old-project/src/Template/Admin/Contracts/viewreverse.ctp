<style>
    .headerdata {
        height: 150px;
    }
</style>


<div class="content-wrapper ml-0">
    <section class="content-header">
        <h1>
            Contract Dashboard
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-home"></i>Home</a></li>
            <li><a href="<?php echo SITE_URL; ?>admin/Contracts/index">
                    Contract
                </a></li>
        </ol>
    </section>
    <!-- content header -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box ovrviw">

                    <?php
                    $suppliername = $this->Comman->findvendornames($contractdetail['supplier_id']);
                    ?>
                    <div class="tableHeader">
                        <p style="text-align:center;font-size:15px;"><b>Contract Details</b></p>
                        <table style="width: 80%; margin-left: 22%;">
                            <tr>
                                <td><b>Work Order:-</b>
                                    <?php echo $contractdetail['workorder']; ?>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Title:-</b>
                                    <?php echo $contractdetail['title']; ?>
                                </td>
                                <td><b>Issue Date:-</b>
                                    <?php echo date('d-M-Y', strtotime($contractdetail['issuedate'])); ?>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Contract Start Date:-</b>
                                    <?php echo date('d-M-Y', strtotime($contractdetail['contract_start_date'])); ?>
                                </td>
                                <td><b>Contract End Date:-</b>
                                    <?php echo date('d-M-Y', strtotime($contractdetail['contract_end_date'])); ?>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Supplier Name:-</b>
                                    <?php echo $suppliername['name']; ?>
                                </td>
                                <td><b>Cost:-</b>
                                    <?php echo sprintf('%.2f', $contractdetail['cost']); ?>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Labour Cost:-</b>
                                    <?php echo sprintf('%.2f', $contractdetail['labour_cost']); ?>
                                </td>
                                <td><b>Operational Cost:-</b>
                                    <?php echo sprintf('%.2f', $contractdetail['operation_cost']); ?>
                                </td>
                            </tr>
                        </table>
                    </div>


                    <!-------------------------------- Costing ----------------------------------->

                    <div class="row">
                        <p style="text-align:center;font-size:15px;margin-top: 40px;"><b>Contract Expenditure
                                Details</b></p>

                        <table class="table table-bordered table-striped" width="100%">
                            <thead>
                                <tr>
                                    <th width="8%">Reverse Id</th>
                                    <th width="10%">Date</th>
                                    <th width="30%">Product</th>
                                    <th width="10%">Quantity</th>
                                    <th width="10%">Reference GRN</th>
                                    <th width="10%">GRN Date</th>
                                    <th width="10%">Rate</th>
                                    <th width="12%">Cost</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($designsheetitems as $designitem) {
                                    $reverse = $this->Comman->rawreverseitemqty($designitem['contract_id'], $designitem['item_id']);
                                    foreach ($reverse as $reverseitem) {
                                        $itemname = $this->Comman->getitemname($reverseitem['item_id']);
                                        $date = date('Y-m-d', strtotime($reverseitem['created']));
                                        $grnitem = $this->Comman->reciveitem($reverseitem['item_id'], $date);
                                        $taxrate = $this->Comman->findtaxname($grnitem['tax_id']);
                                        $taxper = 1 . '.' . $taxrate[0]['tax'];
                                        $reverseitemcost = $reverseitem['quantity'] * $grnitem['rate'] * $taxper;
                                        $totalreversecost += $reverseitemcost;
                                        ?>
                                <tr>
                                    <td>
                                        <?php echo $reverseitem['reverse_id']; ?>
                                    </td>
                                    <td>
                                        <?php echo date('d-M-Y', strtotime($reverseitem['created'])) ?>
                                    </td>
                                    <td>
                                        <?php echo $itemname['item_name']; ?>
                                    </td>
                                    <td style="text-align:right;">
                                        <?php echo sprintf('%.2f', $reverseitem['quantity']); ?>
                                    </td>
                                    <td><a class="viewgrndetails"
                                            href="<?php echo SITE_URL; ?>admin/goodsreceived/viewgrndetail/<?php echo $grnitem['goods_id']; ?>">
                                            <?php echo $grnitem['goods_id']; ?>
                                        </a></td>
                                    <td>
                                        <?php echo date('d-M-Y', strtotime($grnitem['issue_date'])); ?>
                                    </td>
                                    <td style="text-align:right;">
                                        <?php echo sprintf('%.2f', $grnitem['rate'] * $taxper); ?>
                                    </td>
                                    <td style="text-align:right;">
                                        <?php echo sprintf('%.2f', $reverseitemcost); ?>
                                    </td>
                                </tr>
                                <?php $counter++;
                                    }
                                }
                                ?>
                                <tr>
                                    <th colspan="7" style="text-align:right;">Total</th>
                                    <th style="text-align:right;"><?php echo sprintf('%.2f', $totalreversecost); ?></th>
                                </tr>
                            </tbody>
                        </table>
                        <?php
                        ?>
                    </div>


                </div>
            </div>

        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>

<script>
    $('.viewgrndetails').click(function (e) {
        e.preventDefault();
        $('#editsortsgrn').modal('show').find('.modal-body').load($(this).attr('href'));
    });
</script>

<div class="modal fade" id="editsortsgrn">
    <div class="modal-dialog " style="max-width: 900px !important;">
        <div class="modal-content">
            <div class="modal-body"></div>
        </div>
    </div>
</div>