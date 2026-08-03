<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Contracts

        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-home"></i>Home</a></li>
            <li><a href="<?php echo SITE_URL; ?>admin/Sales/customerorder">Contracts</a></li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div id="Sale_invoice">
                        <div class="sl_inc_inr">
                            <div style="width: 75%;" class="top_row">
                                <input type="checkbox">
                                <!-- <h3>Shipment</h3> -->
                                <a href="<?php echo SITE_URL; ?>admin/Sales/addcounterparty"><button><i class="fa fa-plus-circle" aria-hidden="true"></i>Contracts</button></a>
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
                                            <th scope="col"> <input type="checkbox"> number</th>
                                            <th scope="col">Code</th>
                                            <th scope="col">Date Created</th>
                                            <th scope="col">Counterparty</th>
                                            <th scope="col">My Company </th>
                                            <th scope="col">Total</th>
                                            <th scope="col">Paid</th>
                                            <th scope="col">Completed Comment</th>
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
                                            <td> <a href=""> <input type="checkbox"> </a></td>
                                            <td> <a href=""> </a></td>
                                            <td> <a href=""> </a></td>
                                            <td> <a href=""> </a></td>
                                            <td> <a href=""> </a></td>
                                            <td> <a href="">0.00 </a></td>
                                            <td> <a href=""> 0.00</a></td>
                                            <td> <a href="">0.00 </a></td>
                                            <td> <a href=""> </a></td>
                                        </tr>
                                    </tbody>
                                </table>







                                <!-- <table cellpadding="10px">
                                    <tr>
                                        <th> <input type="checkbox"> Number</th>
                                        <th>Code</th>
                                        <th>Date Created</th>
                                        <th>Countryparty</th>
                                        <th>My Company</th>
                                        <th>Total</th>
                                        <th>Paid</th>
                                        <th>Completed Comment</th>
                                        <th>
                                            <div class="dropdown-center">

                                                <button class="btn btn-white dropdown-toggle tbl_btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
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

                                    <tr>
                                        <td> <input type="checkbox"> <a href=""> </a></td>
                                        <td> <a href=""> </a></td>
                                        <td> <a href=""> </a></td>
                                        <td> <a href=""> </a></td>
                                        <td> <a href=""> </a></td>
                                        <td> <a href=""> 0.00</a></td>
                                        <td> <a href="">0.00 </a></td>
                                        <td> <a href="">0.00 </a></td>
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