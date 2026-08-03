<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Operation Reports
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-home"></i>Home</a></li>
            <li><a href="<?php echo SITE_URL; ?>admin/Production/billsofmaterials">
                    Operation Reports
                </a></li>
        </ol>
    </section>
    <!-- content header -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div id="Sale_invoice">
                        <div class="sl_inc_inr">
                            <div style="width: 80%;" class="top_row">



                                <button>Filter</button>

                                <input type="text" placeholder="Search by numbers and comments">




                                <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                                    <input type="text" value="0">

                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                            Edit
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#">Dropdown link</a></li>
                                            <li><a class="dropdown-item" href="#">Dropdown link</a></li>
                                        </ul>
                                    </div>

                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                            Print
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#">Dropdown link</a></li>
                                            <li><a class="dropdown-item" href="#">Dropdown link</a></li>
                                        </ul>
                                    </div>
                                </div>

                                <button><i class="fa fa-cog" aria-hidden="true"></i></button>




                            </div>


                        </div>



                    </div>









                    <!-- /.box-header -->
                    <div class="box-body" id="updt" style="padding-top:0px;">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Date Created</th>
                                    <th>Code</th>
                                    <th>Operaion </th>
                                    <th>BOM</th>
                                    <th>Volume of Production</th>
                                    <th>U.COst</th>
                                    <th>Performed By</th>
                                    <th>Meterials Warehouse</th>
                                    <th>Product Warehouse</th>
                                    <th>Comment </th>

                                    <th scope="col">
                                        <div class="dropdown-center">

                                            <button style="color: white; width:18px !important ; height:18px !important;display: flex;
    align-items: center; justify-content: space-between; " class="btn btn-white dropdown-toggle tbl_btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-cog"></i>
                                            </button>
                                            <ul style="padding: 10px;" class="dropdown-menu">
                                                <li><a href="#">Image<input type="checkbox"></a></li>
                                                <li><a href="#">UOM<input type="checkbox"></a></li>
                                                <li><a href="#">Shipped<input type="checkbox"></a></li>
                                                <li><a href="#">Available<input type="checkbox"></a></li>
                                                <li><a href="#">On Hand<input type="checkbox"></a></li>
                                                <li><a href="#">Committed<input type="checkbox"></a></li>
                                                <li><a href="#">In Transit<input type="checkbox"></a></li>
                                                <li><a href="#">Weight<input type="checkbox"></a></li>
                                                <li><a href="#">Volume<input type="checkbox"></a></li>
                                                <li><a href="#">Tax sum<input type="checkbox"></a></li>

                                            </ul>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>0000001</td>
                                </tr>
                            </tbody>

                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<!-- /.   content-wrapper -->
<div class="modal fade" id="myModal" style="width:51% !important;overflow-y: auto !important;" tabindex="-1" role="dialog" aria-labelledby="esModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width:100% !important;">
        <div class="modal-content personal">
            <div class="loader">
                <div class="es-spinner">
                    <i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $(".globalModals").click(function(event) {
            // alert($(this).attr("href"));
            $('.modal-content').load($(this).attr("href")); //load content from href of link
        });
    });
</script>