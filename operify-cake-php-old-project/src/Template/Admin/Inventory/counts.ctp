<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Inventory Counts
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-home"></i>Home</a></li>
            <li><a href="<?php echo SITE_URL; ?>admin/Sales/customerorder">Inventory Counts</a></li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div id="Sale_invoice">
                        <div class="sl_inc_inr">
                            <div style="width: 90%;" class="top_row">
                                <input type="checkbox">
                                <!-- <h3>Shipment</h3> -->
                                <a href="<?php echo SITE_URL; ?>admin/inventory/addcounts"><button><i class="fa fa-plus-circle" aria-hidden="true"></i>Inventory Counts</button></a>
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
                                            Status
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

                                <!-- <div class="dropdown-center">
                                    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Edit
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#">Sort by name</a></li>
                                        <li><a class="dropdown-item" href="#">Sort by SKU</a></li>
                                        <li><a class="dropdown-item" href="#"><input type="checkbox">In an order of folders</a></li>
                                    </ul>
                                </div>
                                <div class="dropdown-center">
                                    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Status
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#">Sort by name</a></li>
                                        <li><a class="dropdown-item" href="#">Sort by SKU</a></li>
                                        <li><a class="dropdown-item" href="#"><input type="checkbox">In an order of folders</a></li>
                                    </ul>
                                </div>
                                <div class="dropdown-center">
                                    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Print
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#">Sort by name</a></li>
                                        <li><a class="dropdown-item" href="#">Sort by SKU</a></li>
                                        <li><a class="dropdown-item" href="#"><input type="checkbox">In an order of folders</a></li>
                                    </ul>
                                </div> -->


                            </div>
                            <div class="btm_row">

                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col"> <input type="checkbox"> No.</th>
                                            <th scope="col">Date Created</th>
                                            <th scope="col">Warehouse</th>
                                            <th scope="col">My Company</th>
                                            <th scope="col">Sharing </th>
                                            <th scope="col">Owner </th>
                                            <th scope="col">Mailing </th>
                                            <th scope="col">Printed Out </th>
                                            <th scope="col">Comment </th>
                                            <th scope="col">Date Modified </th>
                                            <th scope="col">Modified By </th>
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
                                    <tbody class="table-group-divider">
                                        <tr>
                                            <td> <a href=""> <input type="checkbox"> 0001</a></td>
                                            <td> <a href="">07/09/2023 04:00 PM </a></td>
                                            <td> <a href="">Jaipur </a></td>
                                            <td> <a href="">Doomshell Software </a></td>
                                            <td> <a href="">0.00 </a></td>
                                            <td> <a href=""> </a></td>
                                        </tr>
                                    </tbody>
                                </table>



                                <!-- <table>
                                    <tr>
                                        <th> <input type="checkbox"> Name</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Customer aboutc...</th>
                                        <th>Groups</th>
                                        <th>Place/Source of Supply </th>
                                        <th>Last Sale</th>
                                        <th>Sale N..</th>
                                        <th><i class="fa fa-cog" aria-hidden="true"></i> </th>
                                    </tr>

                                    <tr>
                                        <td> <input type="checkbox"> <a href=""> Customer</a></td>
                                        <td> <a href=""> </a></td>
                                        <td> <a href=""> </a></td>
                                        <td> <a href=""> </a></td>
                                        <td> <a href=""> </a></td>
                                        <td> <a href=""> Karnataka</a></td>
                                        <td> <a href=""> </a></td>
                                        <td> <a href="">0 </a></td>
                                        <td> <a href=""> </a></td>


                                    </tr>
                                </table> -->
                            </div>

                        </div>



                    </div>
                </div>
            </div>
        </div>
    </section>
</div>