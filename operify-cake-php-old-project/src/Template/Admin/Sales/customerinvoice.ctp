<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Sales Invoices

        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-home"></i>Home</a></li>
            <li><a href="<?php echo SITE_URL; ?>admin/Sales/customerorder">Sales Invoices</a></li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div id="Sale_invoice">
                        <div class="sl_inc_inr">
                            <div class="top_row">
                                <input type="checkbox">
                                <!-- <h3>Sales Invoices</h3> -->
                                <a href="<?php echo SITE_URL; ?>admin/Sales/addinvoices"><button><i class="fa fa-plus-circle" aria-hidden="true"></i>Invoice</button></a>
                                <button>Order</button>
                                <button>Filter</button>
                                <input type="text" placeholder="Search by numbers and comments">
                                <div class="dropdown-center">
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
                                </div>
                            </div>
                            <div class="btm_row">




                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col"> <input type="checkbox"> No.</th>
                                            <th scope="col">Date Created</th>
                                            <th scope="col">Customer</th>
                                            <th scope="col">My Company</th>
                                            <th scope="col">Warehouse</th>
                                            <th scope="col">Total Due Date</th>
                                            <th scope="col">Paid</th>
                                            <th scope="col">Amount Shipped Comment</th>
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

                                            <td><input type="checkbox"> <a href=""> 00011</a></td>
                                            <td><a href=""> 07/09/2023 11:00</a></td>
                                            <td> <a href=""> Doomshell</a></td>
                                            <td> <a href=""> Doomshell Software</a></td>
                                            <td> <a href=""> Jaipur</a></td>
                                            <td> <a href="">0.00 </a></td>
                                            <td> <a href=""> 0.00</a></td>
                                            <td> <a href=""> 0.00 </a></td>
                                            <td> <a href=""> </a></td>


                                        </tr>

                                    </tbody>
                                </table>



                                <!-- <table>
                                    <tr>
                                        <th> <input type="checkbox"> Customer</th>
                                        <th>My Company</th>
                                        <th>Unpaid</th>
                                        <th>Amount Shipped</th>
                                        <th>Amount Returned</th>
                                        <th>Shipping Address</th>
                                        <th>Sharing</th>
                                        <th>Team Access</th>
                                        <th>Owner</th>
                                        <th>Status</th>
                                        <th>Comments</th>


                                    </tr>

                                    <tr>
                                        <td> <input type="checkbox"> <a href=""> Doomshell Software</a></td>
                                        <td> <a href=""> Doomshell Software</a></td>
                                        <td> <a href=""> 100.00</a></td>
                                        <td> <a href=""> 100.00</a></td>
                                        <td> <a href=""> 0.00</a></td>
                                        <td> <a href=""> </a></td>
                                        <td> <a href=""> </a></td>
                                        <td> <a href=""> Main </a></td>
                                        <td> <a href=""> Xyz </a></td>
                                        <td> <button><a href=""> Xyz </a></button></td>
                                        <td> <a href=""> </a></td>
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