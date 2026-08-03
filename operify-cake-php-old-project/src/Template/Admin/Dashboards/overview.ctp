<style>
    .headerdata {
        height: 150px;
    }

    .chart_pie_style {
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #myPlot14,
    #myPlot15,
    #myPlot16,
    #myPlot17 {
        width: 480px !important;
        height: auto !important;
    }

    .col-xs-12 h6 {
        color: white !important;
    }

    .col-sm-8 h6 {
        color: Red !important;
    }

    .col-sm-6 h6 {
        color: Red !important;
    }
</style>





<div class="content-wrapper ml-0">
    <section class="content-header">
        <h1>
            Overview
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-home"></i>Home</a></li>
            <li><a href="<?php echo SITE_URL; ?>admin/Production/Overview">
                    Overview
                </a></li>
        </ol>
    </section>
    <section class="content">
        <div class="row ">
            <div class="col-xs-12">
                <div class="box ovrviw">
                    <div class="row align-items-start">

                        <div class="row ">

                            <div class="col">
                                <div class="headerdata card  text-white" style="background:#355c6e;">
                                    <div class="card-header border-white">
                                        <h5>Contract(<?php echo $contractcount; ?>)</h5>
                                    </div>
                                    <div class="card-body">
                                        <h6 class="card-title">Today -
                                            <?php echo $todaycontractcount; ?>
                                        </h6>
                                        <h6 class="card-title">This Week -
                                            <?php echo $weekcontractcount; ?>
                                        </h6>
                                        <h6 class="card-title">This Month -
                                            <?php echo $monthcontractcount; ?>
                                        </h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="headerdata card  text-white" style="background: #d08612;">
                                    <div class="card-header border-white">
                                        <h5>Purchase Order(<?php echo $purchasenordercount; ?>)</h5>
                                    </div>
                                    <div class="card-body">
                                        <h6 class="card-title">Today -
                                            <?php echo $todaypurchasenordercount; ?>
                                        </h6>
                                        <h6 class="card-title">This Week -
                                            <?php echo $weekpurchasenordercount; ?>
                                        </h6>
                                        <h6 class="card-title">This Month -
                                            <?php echo $monthpurchasenordercount; ?>
                                        </h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="headerdata card  text-white" style="background: #3d400b;">
                                    <div class="card-header border-white">
                                        <h5>GRN(<?php echo $totalgrncount; ?>)</h5>
                                    </div>
                                    <div class="card-body">
                                        <h6 class="card-title">Today -
                                            <?php echo $todaylgrncount; ?>
                                        </h6>
                                        <h6 class="card-title">This Week -
                                            <?php echo $weeklgrncount; ?>
                                        </h6>
                                        <h6 class="card-title">This Month -
                                            <?php echo $monthgrncount; ?>
                                        </h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="headerdata card  text-white" style="background:#25559b;">
                                    <div class="card-header border-white">
                                        <h5>Vendors(<?php echo $suppliercount; ?>)</h5>
                                    </div>
                                    <div class="card-body">
                                        <h6 class="card-title">Today -
                                            <?php echo $todaylsuppliercount; ?>
                                        </h6>
                                        <h6 class="card-title">This Week -
                                            <?php echo $weeklsuppliercount; ?>
                                        </h6>
                                        <h6 class="card-title">This Month -
                                            <?php echo $monthsuppliercount; ?>
                                        </h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="headerdata card  text-white" style="background:#2a8754;">
                                    <div class="card-header border-white">
                                        <h5>Maintenance(<?php echo $maintenancecount; ?>) </h5>
                                    </div>
                                    <div class="card-body">
                                        <h6 class="card-title">Today -
                                            <?php echo $todaymaintenancecount; ?>
                                        </h6>
                                        <h6 class="card-title">This Week -
                                            <?php echo $weekmaintenancecount; ?>
                                        </h6>
                                        <h6 class="card-title">This Month -
                                            <?php echo $monthmaintenancecount; ?>
                                        </h6>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!--------------------------------Purchase Order ----------------------------------->

                        <div class="col-sm-8" style="margin-top:45px ;">
                            <h6>Last Five Purchase Order Request</h6>
                            <table id="mainten" class=" table table-bordered table-striped" width="100%">
                                <thead>
                                    <tr>
                                        <th width="07%">PO Id</th>
                                        <th width="10%">Genrated Date</th>
                                        <th width="29%">Vendor</th>
                                        <th width="23%">Contact /Email</th>
                                        <th width="09%">Quantity</th>
                                        <th width="12%">Total Amount (INR)</th>
                                        <th width="10%">Delivery Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $page = $this->request->params['paging']['']['page'];
                                    $limit = $this->request->params['paging']['']['perPage'];
                                    $counter = ($page * $limit) - $limit + 1;
                                    if (isset($podata) && !empty($podata)) {
                                        foreach ($podata as $value) { //pr($value);
                                            $var = $this->Comman->poitemquantity($value['purchaseorder_id'], $value['is_revised'], $value['id']);
                                            $podetail = $this->Comman->podetail($value['purchaseorder_id'], $value['is_revised'], $value['id']);
                                            $vendor_id = $this->Comman->findvendornames($value['vendor_id']);
                                    ?>
                                            <tr>
                                                <td>
                                                    <a class="viewgrndetails" href="<?php echo ADMIN_URL; ?>purchaseorder/viewpodetail/<?php echo $value['purchaseorder_id'] . "/" . $value['is_revised'] . "/" . $value['id']; ?>">
                                                        <?php echo $value['purchaseorder_id']; ?>
                                                    </a>

                                                    <?php if ($value['is_revised'] > 0) { ?>
                                                        <a style="font-size: 20px;" class="viewgrndetails" href="<?php echo ADMIN_URL; ?>purchaseorder/viewpodetail/<?php echo $value['purchaseorder_id'] . "/" . $value['is_revised'] . "/" . $value['id']; ?>">R
                                                            <?php echo "-" . $value['is_revised']; ?>&nbsp;
                                                        </a>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <?php echo date("d-m-Y", strtotime($value['added_time'])); ?>
                                                </td>
                                                <td>
                                                    <?php echo $vendor_id['name']; ?>
                                                </td>
                                                <td>
                                                    <?php $ert = explode(',', $vendor_id['contact_no']); //pr($ert);
                                                    if (isset($vendor_id['contact_no'])) {
                                                        foreach ($ert as $fg) {
                                                            echo $fg . '<br>';
                                                        }
                                                    } else {
                                                        echo 'N/A';
                                                    }
                                                    ?>
                                                    <?php $rty = explode(',', $vendor_id['email']); //pr($rty);
                                                    if (isset($vendor_id['email'])) {
                                                        foreach ($rty as $df) {
                                                            echo $df . '<br>';
                                                        }
                                                    } else {
                                                        echo 'N/A';
                                                    }
                                                    ?>
                                                </td>
                                                <td style="text-align:right;">
                                                    <?php echo $value['total_qty']; ?>
                                                </td>
                                                <td style="text-align:right;">
                                                    <?php echo number_format($value['total_amt']); ?> </td>
                                                <td>
                                                    <?php echo date("d-m-Y", strtotime($value['added_time'])); ?>
                                                </td>
                                            </tr>
                                        <?php $counter++;
                                        }
                                    } else { ?>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="col-sm-4" style="margin-top: 45px;">
                            <div class="col-sm-12 chart_pie_style">
                                <canvas id="myPlot14" style="width: 100%;max-width: 700px;display: block;" width="700" height="350" class="chartjs-render-monitor"></canvas>
                            </div>
                        </div>

                        <!-----------------------------------------Production Orders---------------------------->

                        <div class="col-sm-4" style="margin-bottom: 45px;">
                            <div class="col-sm-12 chart_pie_style">
                                <canvas id="myPlot15" style="width: 100%;max-width: 700px;display: block;" width="700" height="350" class="chartjs-render-monitor"></canvas>
                            </div>
                        </div>

                        <div class="col-sm-8" style="margin-top: 45px;margin-bottom: 45px;">
                            <h6>Last Five Production Orders</h6>
                            <table class="table table-bordered table-striped" width="100%">
                                <thead>
                                    <tr>
                                        <th width="04%">PO NO.</th>
                                        <th width="10%">Date Created</th>
                                        <th width="29%">Contract Name</th>
                                        <th width="29%">Product</th>
                                        <th width="10%">Start Date</th>
                                        <th width="10%">End Date</th>
                                        <th width="08%">Planned Qty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // pr($productionorder);
                                    foreach ($productionorder as $detail) {
                                        $contractname = $this->comman->findcontractname($detail['contract_id']);
                                        $itemname = $this->comman->getitemname($detail['item_id']);
                                    ?>

                                        <tr>
                                            <td>
                                                <?php echo $detail['po_id']; ?>
                                            </td>
                                            <td>
                                                <?php echo date('d-m-Y', strtotime($detail['issuedate'])); ?>
                                            </td>
                                            <td><a href="<?php echo SITE_URL; ?>admin/production/viewcontractdetail/<?php echo $detail['contract_id']; ?>" class="viewdetails">
                                                    <?php echo $contractname['title'] . '(' . $contractname['workorder'] . ')'; ?>
                                                </a></td>
                                            <td>
                                                <?php echo $itemname['item_name']; ?>
                                            </td>
                                            <td>
                                                <?php echo date('d-m-Y', strtotime($detail['startdate'])); ?>
                                            </td>
                                            <td>
                                                <?php echo date('d-m-Y', strtotime($detail['enddate'])); ?>
                                            </td>
                                            <td style="text-align:right;">
                                                <?php echo $detail['plannedqty']; ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <!-----------------------------------------Maintenance---------------------------->


                        <div class="col-sm-8" style="margin-bottom: 45px;">
                            <h6>Last Five Maintenance Request</h6>
                            <table id="mainten" class=" table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="10%">Date</th>
                                        <th width="20%">Machine Name</th>
                                        <th width="15%">Type Of Breakdown</th>
                                        <th width="07%">Time(Hrs)</th>
                                        <th width="12%">Assigned To</th>
                                        <th width="12%">Shift Incharge</th>
                                        <th width="12%">Maintenance Incharge</th>
                                        <th width="12%">Production Head</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $page = $this->request->params['paging']['']['page'];
                                    $limit = $this->request->params['paging']['']['perPage'];
                                    $counter = ($page * $limit) - $limit + 1;
                                    if (isset($maintenancedetails) && !empty($maintenancedetails)) {
                                        foreach ($maintenancedetails as $value) {
                                    ?>
                                            <tr>
                                                <td width="6%">
                                                    <?php echo date("d-m-Y", strtotime($value['datefrom'])); ?>
                                                </td>
                                                <td width="15%">
                                                    <?php echo $value['machinemaster']['machine_name']; ?>
                                                </td>
                                                <td width="8%">
                                                    <?php echo ucfirst(strtolower($value['breakdown_type'])); ?>
                                                </td>
                                                <td width="2%">
                                                    <?php echo $value['total_time']; ?>
                                                </td>
                                                <td width="7%">
                                                    <?php echo ucfirst(strtolower($value['assigned_to'])); ?>
                                                </td>
                                                <td width="7%">
                                                    <?php echo ucfirst(strtolower($value['shift_incharge'])); ?>
                                                </td>
                                                <td width="7%">
                                                    <?php echo ucfirst(strtolower($value['maintenance_incharge'])); ?>
                                                </td>
                                                <td width="7%">
                                                    <?php echo ucfirst(strtolower($value['production_head'])); ?>
                                                </td>
                                            </tr>
                                    <?php $counter++;
                                        }
                                    } ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="col-sm-4" style="margin-bottom: 45px;">
                            <div class="col-sm-12 chart_pie_style">
                                <canvas id="myPlot16" style="width: 100%;max-width: 700px;display: block;" width="700" height="350" class="chartjs-render-monitor"></canvas>
                            </div>
                        </div>
                        <!-----------------------------------------GRN---------------------------->

                        <div class="col-sm-6" style="margin-bottom: 45px;">
                            <h6>Last Five Inspection</h6>
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>Contract Name</th>
                                        <th>Name</th>
                                        <th>Inspection Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $page = $this->request->params['paging']['']['page'];
                                    $limit = $this->request->params['paging']['']['perPage'];
                                    $counter = ($page * $limit) - $limit + 1;
                                    if (isset($inspection) && !empty($inspection)) {
                                        foreach ($inspection as $intusr) {
                                            $contractname = $this->Comman->findcontractname($intusr['work_order_no']);
                                            $bomid = $this->Comman->findbomdetails($intusr['work_order_no']); ?>
                                            <tr>
                                                <td>
                                                    <?php echo $counter; ?>
                                                </td>
                                                <td><a href="<?php echo SITE_URL; ?>admin/production/viewcontractdetail/<?php echo $intusr['work_order_no']; ?>" class="viewdetails">
                                                        <?php echo $contractname['title'] . '(' . $contractname['workorder'] . ')'; ?>
                                                    </a></td>
                                                <td>
                                                    <?php echo $intusr['name']; ?>
                                                </td>
                                                <td>
                                                    <?php echo date('Y-m-d', strtotime($intusr['inspection_date'])); ?>
                                                </td>
                                            </tr>
                                        <?php $counter++;
                                        }
                                    } else { ?>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-sm-6" style="margin-bottom: 45px;">
                            <h6>Last Five GRN(Goods Recived) Request</h6>
                            <table id="mainten" class="table table-bordered table-striped" width="100%">
                                <thead>
                                    <tr>
                                        <th width="08%">GRN No.</th>
                                        <th width="06%">PO Id</th>
                                        <th width="10%">G.R.N. Inward</th>
                                        <th width="10%">Bill Date</th>
                                        <th width="19%">Supplier</th>
                                        <th width="13%">Total Amount (INR)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $page = $this->request->params['paging']['']['page'];
                                    $limit = $this->request->params['paging']['']['perPage'];
                                    $counter = ($page * $limit) - $limit + 1;
                                    if (isset($goodsreceived) && !empty($goodsreceived)) {
                                        foreach ($goodsreceived as $intusr) {
                                            $vendor_id = $this->Comman->findvendornames($intusr['vendor_id']);
                                            $po = $this->Comman->getpoqty($intusr['purchaseorder_id']);
                                            $remain = $this->Comman->goodsrecivied($intusr['purchaseorder_id'], $intusr['id']);
                                            $getpo = $this->Comman->getPurchaseOrder($intusr['purchaseorder_id']);


                                    ?>
                                            <tr>
                                                <td>
                                                    <a class="viewgrndetails" href="<?php echo SITE_URL; ?>admin/goodsreceived/viewgrndetail/<?php echo $intusr['id']; ?>">
                                                        <?php echo $intusr['id']; ?>
                                                    </a>
                                                </td>

                                                <td>
                                                    <a class="viewgrndetails" href="<?php echo ADMIN_URL; ?>purchaseorder/viewpodetail/<?php echo $intusr['purchaseorder_id'] . "/" . $getpo['is_revised'] . "/" . $getpo['id']; ?>">
                                                        <?php echo $intusr['purchaseorder_id']; ?>
                                                    </a>
                                                </td>
                                                <td>
                                                    <?php echo date("d-m-Y", strtotime($intusr['inwarddate'])); ?>
                                                </td>
                                                <td>
                                                    <?php echo date("d-m-Y", strtotime($intusr['bill_date'])); ?>
                                                </td>
                                                <td>
                                                    <?php echo $vendor_id['name']; ?>
                                                </td>
                                                <td style="text-align:right;">

                                                    <?php echo number_format($intusr['total_amt']); ?>
                                                </td>
                                            </tr>
                                        <?php $counter++;
                                        }
                                    } else { ?>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

            </div>
    </section>
</div>




<script>
    var xValues = ["Complete", "Active", "Pending"];
    var yValues = [<?php echo $completepurchasenorder; ?>, <?php echo $activepo; ?>, <?php echo $pendingpo; ?>];
    var barColors = [
        "#198754",
        "#2b5797",
        "#dc3545",
    ];
    var chart = new Chart("myPlot14", {
        type: "pie",
        data: {
            labels: xValues,
            datasets: [{
                backgroundColor: barColors,
                data: yValues
            }]
        },
        options: {
            title: {
                display: true,
                text: "Total Purchase Orders"
            }
        }
    });
</script>


<script>
    var xValues = ["Complete", "Active", "Pending"];
    var yValues = [<?php echo $completeproductionorder; ?>, <?php echo $activeproductionorder; ?>, <?php echo $pendingproductionorder; ?>];
    var barColors = [
        "#1e7145",
        "#dc3545",
    ];
    var chart = new Chart("myPlot15", {
        type: "pie",
        data: {
            labels: xValues,
            datasets: [{
                backgroundColor: barColors,
                data: yValues
            }]
        },
        options: {
            title: {
                display: true,
                text: "Total Production Orders"
            }
        }
    });
</script>

<script>
    var xValues = ["Completed", "Assigned", "Pending"];
    var yValues = [<?php echo $completemaintenancecount; ?>, <?php echo $assignedmaintenancecount; ?>, <?php echo $pendingmaintenancecount; ?>];
    var barColors = [
        "#b91d47",
        "#00aba9",
        "#355c6e"
    ];
    var chart = new Chart("myPlot16", {
        type: "pie",
        data: {
            labels: xValues,
            datasets: [{
                backgroundColor: barColors,
                data: yValues
            }]
        },
        options: {
            title: {
                display: true,
                text: "Total Manitenance"
            }
        }
    });
</script>

<script>
    var xValues = ["Italy", "France", "Spain", "USA"];
    var yValues = [55, 49, 44, 39];
    var barColors = [
        "#b91d47",
        "#00aba9",
        "#2b5797",
        "#e8c3b9",
        "#1e7145"
    ];
    var chart = new Chart("myPlot17", {
        type: "pie",
        data: {
            labels: xValues,
            datasets: [{
                backgroundColor: barColors,
                data: yValues
            }]
        },
        options: {
            title: {
                display: true,
                text: "Production 2018"
            }
        }
    });
</script>

<script>
    $('.viewdetails').click(function(e) {
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

<script>
    $('.viewgrndetails').click(function(e) {
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