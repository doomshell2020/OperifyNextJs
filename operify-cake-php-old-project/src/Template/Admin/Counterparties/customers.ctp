<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Customer

        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-home"></i>Home</a></li>
            <li><a href="<?php echo SITE_URL; ?>admin/Sales/customerorder">Customer</a></li>
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
                                <a href="<?php echo SITE_URL; ?>admin/Sales/addcounterparty"><button><i class="fa fa-plus-circle" aria-hidden="true"></i>Counterparty</button></a>
                                <button>Filter</button>
                                <input type="text" placeholder="Name, phone, email, event, comment">




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
                                <button>Import</button>
                                <button>Export</button>
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
                                            <th scope="col"> <input type="checkbox"> Name</th>
                                            <th scope="col">Phone</th>
                                            <th scope="col">Email</th>
                                            <th scope="col">Customer about c...</th>
                                            <th scope="col">Groups </th>
                                            <th scope="col">Place/Source od Supply</th>
                                            <th scope="col">last Sale</th>
                                            <th scope="col">Sale N..</th>
                                            <th scope="col">
                                                <div class="dropdown-center">

                                                    <button style="color: white;" class="btn btn-white dropdown-toggle tbl_btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
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
                                            <td> <a href=""> <input type="checkbox"> Customer</a></td>
                                            <td> <a href=""> </a></td>
                                            <td> <a href=""> </a></td>
                                            <td> <a href=""> </a></td>
                                            <td> <a href=""> </a></td>
                                            <td> <a href="">Jaipur </a></td>
                                            <td> <a href=""> </a></td>
                                            <td> <a href="">0 </a></td>
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