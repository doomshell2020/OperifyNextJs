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


canvas.rawmaterialchart {
    height: 500px !important;
    width: 80% !important;
}

.myPlot14,
.myPlot15,
.myPlot16,
.myPlot17 {
    width: 450px !important;
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
                        <p style="text-align:center;font-size:15px;margin-top: 40px;"><b>Contract Cost</b></p>
                        <?php
                    foreach($designsheetitems as $designitem){
                        $indents = $this->Comman->rawindentitemqty($designitem['contract_id'], $designitem['item_id']);
                        foreach($indents as $indentitem){
                            $date = date('Y-m-d', strtotime($indentitem['created']));
                            $grnitem = $this->Comman->reciveitem($indentitem['item_id'],$date);
                            $taxrate = $this->Comman->findtaxname($grnitem['tax_id']);
                            $taxper = 1 .'.' .$taxrate[0]['tax'];
                            $indentitemcost += $indentitem['item_qty'] * $grnitem['rate'] * $taxper;
                        }

                        $reverse = $this->Comman->rawreverseitemqty($designitem['contract_id'], $designitem['item_id']);
                        foreach($reverse as $reverseitem){
                            $date = date('Y-m-d', strtotime($reverseitem['created']));
                            $grnitem = $this->Comman->reciveitem($reverseitem['item_id'],$date);
                            $taxrate = $this->Comman->findtaxname($grnitem['tax_id']);
                            $taxper = 1 .'.' .$taxrate[0]['tax'];
                            $reverseitemcost += $reverseitem['quantity'] * $grnitem['rate'] * $taxper;
                        }
                    }
                    
                    $otherexpen = $contractdetail['labour_cost'] + $contractdetail['operation_cost'];
                    $actualcost = $indentitemcost - $reverseitemcost + $otherexpen;
                    $pendingcost = $contractdetail['cost'] - $actualcost;
                    ?>


                        <div class="col-sm-6">
                            <div class="col-sm-12 chart_pie_style">
                                <canvas id="myPlot14" style="width: 100%;max-width: 700px;display: block;" width="700"
                                    height="325" class="chartjs-render-monitor"></canvas>
                            </div>
                        </div>

                            <div class="col-sm-6">
                            <table style="width: 80%; margin-left: 22%;margin-top: 15%;font-size:15px !important">
                            <tr>
                                <td><b>Total Cost:-</b>
                                    <?php echo sprintf('%.2f', $contractdetail['cost']); ?>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Total Expenditure:-</b>
                                <a  target="_blank"
                                    href="<?php echo SITE_URL; ?>admin/contracts/viewexpenditure/<?php echo $contractdetail['id']; ?>"><?php echo sprintf('%.2f', ($indentitemcost)); ?></a>
                                    
                                </td>
                            </tr>
                            <tr>
                                <td><b>Total Reverse:-</b>
                                <a  target="_blank"
                                    href="<?php echo SITE_URL; ?>admin/contracts/viewreverse/<?php echo $contractdetail['id']; ?>"><?php echo sprintf('%.2f', ($reverseitemcost)); ?></a>
                                    
                                </td>
                            </tr>
                            <tr>
                                <td><b>Other Expenditure:-</b>
                                    <?php echo sprintf('%.2f', $contractdetail['operation_cost'] + $contractdetail['labour_cost']); ?>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Pending Cost:-</b>
                                    <?php echo sprintf('%.2f', $pendingcost); ?>
                                </td>
                            </tr>
                        </table>
                            </div>
                      

                        <script>
                        var xValues = ["Issued", "Pending"];
                        var yValues = [<?php echo sprintf('%.2f', $actualcost); ?>, <?php echo sprintf('%.2f', $pendingcost); ?>];
                        var barColors = [
                            "#198754",
                            "#dc3545",
                        ];
                        var chart = new Chart('myPlot14', {
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
                                    text: "Total Cost"
                                }
                            }
                        });
                        </script>


                    </div>




                    <!--------------------------------Finished Products ----------------------------------->
                    <div class="row">
                        <p style="text-align:center;font-size:15px;margin-top: 40px;"><b>Finished Products</b></p>
                        <?php
                        $i = 1;
                        foreach ($finsheddetails as $finshed) {

                            $contractexists = $this->Comman->checkproduction($contractdetail['id'], $finshed['product_id']);
                            $poexists = $this->Comman->findfinishedqty($contractdetail['id'], $finshed['product_id']);

                            $prepardqty = '';
                            foreach ($contractexists as $outhersheathing) {
                                if ($outhersheathing['productprocess_id'] == 8) {
                                    $prepardqty += $outhersheathing['production_shift_a'] + $outhersheathing['production_shift_b'];
                                }
                            }

                            $plannedqty = '';
                            foreach ($poexists as $itemqty) {
                                $plannedqty += $itemqty['plannedqty'];
                            }

                            $plannedpen = $plannedqty - $prepardqty;
                            $pending = $finshed['quantity'] - $plannedqty;
                            ?>

                        <div class="col-sm-6" style="margin-top: 20px;">
                            <table class="table-bordered" cellpadding="3" width="100%">
                                <thead>
                                    <tr>
                                        <td colspan="3"><b>Product:-</b>
                                            <?php echo $finshed['additem']['item_name']; ?>
                                        </td>
                                        <td colspan="1"><b>Quantity:-</b>
                                            <?php echo sprintf('%.2f', $finshed['quantity']); ?> KM
                                        </td>
                                        <td colspan="1"><b>Planned Qty:-</b>
                                            <?php echo sprintf('%.2f', $plannedqty); ?> KM
                                        </td>
                                        <td colspan="1"><b>Prepared Qty:-</b>
                                            <?php echo sprintf('%.2f', $prepardqty); ?> KM
                                        </td>
                                    </tr>
                                </thead>
                            </table>

                            <div class="col-sm-12 chart_pie_style">
                                <canvas id="myPlot14-<?php echo $i; ?>"
                                    style="width: 100%;max-width: 700px;display: block;" width="700" height="325"
                                    class="chartjs-render-monitor"></canvas>
                            </div>
                        </div>

                        <script>
                        var xValues = ["Prepared", "Planned Pending", "Pending"];
                        var yValues = [<?php echo sprintf('%.2f', $prepardqty); ?>, <?php echo sprintf('%.2f', $plannedpen); ?>, <?php echo sprintf('%.2f', $pending); ?>];
                        var barColors = [
                            "#198754",
                            "#2b5797",
                            "#dc3545",
                        ];
                        var chart = new Chart('myPlot14-<?php echo $i; ?>', {
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
                                    text: "Total Quantity"
                                }
                            }
                        });
                        </script>

                        <?php
                        $i++;
                        }
                        ?>
                    </div>



                    <!--------------------------------Raw Material  ----------------------------------->
                    <div class="row">
                        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                        <p style="text-align:center;font-size:15px;margin-top: 40px;"><b>Raw Material</b></p>
                        <?php
                    $i = 1;
                    foreach ($designs as $designsheet) {
                        $finsitemname = $this->Comman->getitemname($designsheet['item_id']);
                        $designsheetdetails = $this->Comman->getdesignmaterials($designsheet['designsheetno']);
                        $reversedetails = $this->Comman->findreverseindentid($designsheet['contract_id'],$designsheet['item_id']);

                        $items = [];
                        $itemsqty = [];
                        $issuedqty = [];
                        foreach ($designsheetdetails as $item) {
                            $itemname = $this->Comman->getitemname($item['item_id']);
                            $items[] = $itemname['item_name'];

                            $itemsqty[] = $item['item_qty'];

                            $reverseqty = '';
                            foreach($reversedetails as $reverseid){
                                $reverse = $this->Comman->reverseindentdetails($item['item_id'],$reverseid['reverse_id']);
                                $reverseqty += $reverse['quantity'];
                            }

                            $indentqty = $this->Comman->rawitempendingqty($item['item_id'], $designsheet['item_id'], $item['contract_id']);
                            $issuedqty[] = $indentqty['sum'] - $reverseqty;
                        }


                        $itemsname = json_encode($items);
                        $itemquantity = json_encode($itemsqty);
                        $issuedquantity = json_encode($issuedqty);
                        ?>

                        <div class="col-md-6">
                            <table class="table-bordered" cellpadding="3" width="100%">
                                <thead>
                                    <tr>
                                        <td><b>Product:-</b>
                                            <?php echo $finsitemname['item_name']; ?>
                                        </td>
                                        <td><b>Quantity:-</b>
                                            <?php echo sprintf('%.2f', $designsheet['quantity']); ?> KM
                                        </td>
                                    </tr>
                                </thead>
                            </table>
                            <canvas class='rawmaterialchart' id="myChart-<?php echo $i; ?>" width="571"
                                height="571"></canvas>
                            <script>
                            const data<?php echo $i; ?> = {
                                labels: <?php echo $itemsname; ?>,
                                datasets: [{
                                        label: 'Required Qty',
                                        data: <?php echo $itemquantity; ?>,
                                        backgroundColor: '#0d6efd',
                                        borderColor: 'rgba(255, 99, 132, 1)',
                                        borderWidth: 1
                                    },
                                    {
                                        label: 'Issued Qty',
                                        data: <?php echo $issuedquantity; ?>,
                                        backgroundColor: '#212529',
                                        borderColor: 'rgba(54, 162, 235, 1)',
                                        borderWidth: 1
                                    }
                                ]
                            };
                            const config<?php echo $i; ?> = {
                                type: 'bar',
                                data: data<?php echo $i; ?>,
                                options: {
                                    scales: {
                                        y: {
                                            beginAtZero: true
                                        }
                                    }
                                },
                            };
                            const myChart<?php echo $i; ?> = new Chart(
                                document.getElementById('myChart-<?php echo $i; ?>'),
                                config<?php echo $i; ?>
                            );
                            </script>
                        </div>
                        <?php
                    $i++;
                    }
                    ?>
                    </div>






                </div>
            </div>

        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>






