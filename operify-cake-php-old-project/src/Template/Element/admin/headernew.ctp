<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Operify ERP</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>

    <!-- Bootstrap 3.3.6 -->
    <?php //echo $this->Html->css('admin/bootstrap.min.css') 
    ?>

    <!-- Bootstrap 5.2.3 -->
    <?php echo 
   
    $this->Html->css('admin/bootstrap5/css/bootstrap.min.css'); ?>

    <?= $this->Html->meta(
        'img/favicon.ico',
        'img/favicon.ico',
        ['type' => 'icon']
    ); ?>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css"
        integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <!-- Theme style -->
    <?= $this->Html->css('admin/dataTables.bootstrap.css'); ?>
    <?= $this->Html->css('admin/AdminLTE.min.css'); ?>
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <?= $this->Html->css('admin/skins/_all-skins.min.css'); ?>
    <!-- iCheck -->
    <?= $this->Html->css('admin/blue.css'); ?>
    <!-- Morris chart -->
    <?= $this->Html->css('admin/morris.css'); ?>
    <!-- jvectormap -->
    <?= $this->Html->css('admin/jquery-jvectormap-1.2.2.css'); ?>
    <!-- Date Picker -->
    <?= $this->Html->css('admin/datepicker3.css'); ?>
    <!-- Daterange picker -->
    <?= $this->Html->css('admin/daterangepicker.css'); ?>
    <!-- bootstrap wysihtml5 - text editor -->
    <?= $this->Html->css('admin/bootstrap3-wysihtml5.min.css'); ?>
    <?= $this->Html->css('admin/responsive.css'); ?>
    <?= $this->Html->css('admin/style.css'); ?>
    <?= $this->Html->script('admin/jquery-2.2.3.min.js'); ?>
    <?= $this->Html->script('admin/bootstrap.min.js'); ?>
    <?= $this->Html->script('timepicker/bootstrap-timepicker.min.js'); ?>

    <?= $this->Html->css('timepicker/bootstrap-timepicker.min.css'); ?>
    <?php $rolepresent = $this->request->session()->read('Auth.User.role_id');

    if ($rolepresent == '6') { ?>
        <style type="text/css">
            .nav>li>a>img {
                width: 19px !important;
            }

            .nav>li>a {
                font-size: 13px !important;
                padding: 10px 10px !important
            }

            .skin-blue .main-header .logo {
                width: 213px;
            }

            .navbar-nav {
                /* margin-top: 16px !important; */
                height: 58px;
            }
        </style>
    <?php } ?>
    <style>
        .skin-blue .main-header .navbar {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            background-color: #fff;
            box-shadow: 0px 0px 5px 0px #0002;
            height: 58px;
        }

        .skin-blue .main-header .navbar .navbar-custom-menu .nt_menu_align {
            display: flex;
            align-items: center;
        }

        .skin-blue .main-header .navbar .navbar-custom-menu .nt_menu_align li ul {
            margin-left: 0px;
        }

        header .navbar-nav li {
            padding: 0px 3px !important;
            text-align: center;
        }

        ul.nt_menu_align li a span {
            display: block;
            text-align: center;
            font-size: 10px;
            font-weight: 400;
        }

        header img {
            height: 26px;
            width: auto;
        }

        header {
            padding-top: 0;
        }

        header .navbar-nav li a {
            color: #333 !important;
            padding: 0px !important;
        }

        .main-header .navbar {
            min-height: auto !important;
            padding: 5px 0px;
        }

        .skin-blue .main-header .logo:hover {
            background-color: #fff !important;
        }
    </style>
</head>



<body class="hold-transition skin-blue sidebar-collapse">
    <div class="wrapper">
        <header class="main-header">
            <!-- Logo -->
            <script>
                $(document).ready(function() {
                    jQuery.fn.justtext = function() {
                        return $(this).clone()
                            .children()
                            .remove()
                            .end()
                            .text();
                    };
                    var str = $('.content-header').find('h1').justtext();
                    document.title = str;
                });
            </script>


            <?php
            $role_id = $this->request->session()->read('Auth.User.role_id');
            $checked_by = $this->request->session()->read('checked_by');
            if ($role_id == '105' || $checked_by == '105') {
                $get_franchise = $this->Comman->getfranchise();
                $branch = [];
                foreach ($get_franchise as $get) {
                    $branch[] = $get['db'];
                }
            } ?>


            <!-- Header Navbar: style can be found in header.less -->
            <nav class="navbar navbar-static-top">
                <div class="d-flex justify-content-between" style="width: 100%;">
                    <div class="drawerLogo" style="display: flex;">
                        <div>
                            <?php
                            $findlogo = $this->Comman->findlogo();
                            if ($role_id == '105' || $role_id == '1') { ?>
                                <a href="<?php echo SITE_URL; ?>admin/dashboards/overview">
                                <?php } elseif ($role_id == '6') { ?>
                                    <a href="<?php echo SITE_URL; ?>admin/dashboards/overview">
                                    <?php } elseif ($role_id == '101') { ?>
                                        <a href="<?php echo SITE_URL; ?>admin/Sitesettings/index">
                                        <?php }
                                    if ($role_id == '101') { ?>
                                            <img src="<?php echo SITE_URL; ?>images/<?php echo $findlogo['small_logo']; ?>" class="fullLogo" alt="logo">
                                        <?php } else { ?>
                                            <img src="<?php echo SITE_URL . 'images/' . $findlogo['small_logo']; ?>" class="fullLogo" alt="logo">
                                        <?php } ?>
                                        </a>
                        </div>
                        <div>
                            <div style="padding-top: 17px;  font-size: 13px; padding-left: 4px;">
                                <?php echo '<span style="font-weight: 600;">' . $findlogo['alias'] . '</span>'; ?>
                            </div>
                        </div>
                    </div>

                    <?php
                    $role_permissions = $this->Permission->permissioncheck();
                    if ($rolepresent == 16) {
                        $payroll = $this->payroll();
                    }
                    ?>

                    <div class="navbar-custom-menu">

                        <!-- Header menu name -->
                        <ul class="nav navbar-nav nt_menu_align">
                            <li>
                                <ul style="display:flex;">




                                    <?php /*  if ($role_id) { ?>
                                        <li style="padding:5px; text-align:center">
                                            <a title="Required Stock" href="<?php echo SITE_URL; ?>admin/Stockregister/required_stock"
                                                data-toggle="tooltip">
                                                <img src="<?php echo SITE_URL; ?>images/headericons/requiredindex.png"
                                                    height="30px" width="33px" class="" alt="images">
                                                <span>Required Stock</span>
                                            </a>
                                        </li>
                                    <?php   } */ ?>
                                    <?php
                                    $fileurl = "admin/emd/index";
                                    if (in_array($fileurl, $role_permissions)) {
                                    ?>

                                        <li style="padding:5px; text-align:center">
                                            <a title="EMD" href="<?php echo SITE_URL; ?>admin/emd/index"
                                                data-toggle="tooltip">
                                                <img src="<?php echo SITE_URL; ?>images/headericons/inspectionindex.png"
                                                    height="30px" width="33px" class="" alt="images">
                                                <span>EMD</span>
                                            </a>
                                        </li>
                                    <?php } ?>
                                    <?php
                                    $fileurl = "admin/paymentmanager/index";
                                    if (in_array($fileurl, $role_permissions)) {
                                    ?>
                                        <li style="padding:5px; text-align:center">
                                            <a title="Payments" href="<?php echo SITE_URL; ?>admin/paymentmanager/index"
                                                data-toggle="tooltip">
                                                <img src="<?php echo SITE_URL; ?>images/headericons/purchaseorderindex.png"
                                                    height="30px" width="33px" class="" alt="images">
                                                <span>Payments</span>
                                            </a>
                                        </li>
                                    <?php } ?>
                                    <?php

                                    $fileurl = "admin/contracts/index";
                                    if (in_array($fileurl, $role_permissions)) {
                                    ?>
                                        <li style="padding:5px; text-align:center">
                                            <a title="Contract" href="<?php echo SITE_URL; ?>admin/contracts/index"
                                                data-toggle="tooltip">
                                                <img src="<?php echo SITE_URL; ?>images/headericons/contractsindex.png"
                                                    height="30px" width="33px" class="" alt="images">
                                                <span>Contract</span>
                                            </a>
                                        </li>
                                    <?php } ?>
                                    <?php
                                    $fileurl = "admin/designsheet/index";
                                    if (in_array($fileurl, $role_permissions)) {
                                    ?>
                                        <li style="padding:5px; text-align:center"><a title="Desgin Sheet"
                                                href="<?php echo SITE_URL; ?>admin/designsheet/index" data-toggle="tooltip">
                                                <img src="<?php echo SITE_URL; ?>images/headericons/designsheetindex.png"
                                                    height="30px" width="33px" class="" alt="iamges">
                                                <span>Desgin Sheet</span>
                                            </a>
                                        </li>
                                    <?php } ?>

                                    <?php
                                    $fileurl = "admin/Quotation/index";
                                    if (in_array($fileurl, $role_permissions)) {
                                    ?>
                                        <li style="padding:5px; text-align:center">
                                            <a title="Quotation" href="<?php echo SITE_URL; ?>admin/Quotation/index"
                                                data-toggle="tooltip">
                                                <img src="<?php echo SITE_URL; ?>images/headericons/quotationindex.png"
                                                    height="30px" width="33px" class="" alt="images">
                                                <span>Quotation</span>
                                            </a>
                                        </li>
                                    <?php   } ?>

                                    <?php
                                    $fileurl = "admin/purchaseorder/index";
                                    if (in_array($fileurl, $role_permissions)) {
                                    ?>
                                        <li style="padding:5px; text-align:center"><a title="PO"
                                                href="<?php echo SITE_URL; ?>admin/purchaseorder/index"
                                                data-toggle="tooltip">
                                                <img src="<?php echo SITE_URL; ?>images/headericons/purchaseorderindex.png"
                                                    height="30px" width="33px" class="" alt="iamges">
                                                <span>PO</span>
                                            </a>
                                        </li>
                                    <?php } ?>

                                    <?php
                                    $fileurl = "admin/Goodsreceived/grninspection";
                                    if (in_array($fileurl, $role_permissions)) {
                                    ?>
                                        <li style="padding:5px; text-align:center">
                                            <a title="GRN Inspection" href="<?php echo SITE_URL; ?>admin/Goodsreceived/grninspection"
                                                data-toggle="tooltip">
                                                <img src="<?php echo SITE_URL; ?>images/headericons/quotationindex.png"
                                                    height="30px" width="33px" class="" alt="images">
                                                <span>GRN Inspection</span>
                                            </a>
                                        </li>
                                    <?php   } ?>


                                    <?php
                                    $fileurl = "admin/goodsreceived/index";
                                    if (in_array($fileurl, $role_permissions)) {
                                    ?>
                                        <li style="padding:5px; text-align:center"><a title="GRN"
                                                href="<?php echo SITE_URL; ?>admin/goodsreceived/index"
                                                data-toggle="tooltip">
                                                <img src="<?php echo SITE_URL; ?>images/headericons/goodsreceivedindex.png"
                                                    height="30px" width="33px" class="" alt="iamges">
                                                <span>GRN</span>
                                            </a>
                                        </li>
                                    <?php } ?>
                                    <?php
                                    $fileurl = "admin/indentpo/index";
                                    if (in_array($fileurl, $role_permissions)) {
                                    ?>
                                        <li style="padding:5px; text-align:center"><a title="Indents"
                                                href="<?php echo SITE_URL; ?>admin/indentpo/index" data-toggle="tooltip">
                                                <img src="<?php echo SITE_URL; ?>images/headericons/indentpoindex.png"
                                                    height="30px" width="33px" class="" alt="iamges">
                                                <span>Indents</span>
                                            </a>
                                        </li>
                                    <?php } ?>
                                    <?php
                                    $fileurl = "admin/reverseindent/index";
                                    if (in_array($fileurl, $role_permissions)) {
                                    ?>
                                        <li style="padding:5px; text-align:center"><a title="Reverse"
                                                href="<?php echo SITE_URL; ?>admin/reverseindent/index" data-toggle="tooltip">
                                                <img src="<?php echo SITE_URL; ?>images/headericons/reverseindentindex.png"
                                                    height="30px" width="33px" class="" alt="iamges">
                                                <span>Reverse</span>
                                            </a>
                                        </li>
                                    <?php } ?>
                                    <?php
                                    $fileurl = "admin/production/productionorders";
                                    if (in_array($fileurl, $role_permissions)) {
                                    ?>
                                        <li style="padding:5px; text-align:center"><a title="Production"
                                                href="<?php echo SITE_URL; ?>admin/production/productionorders" data-toggle="tooltip">
                                                <img src="<?php echo SITE_URL; ?>images/headericons/productionproductionorders.png"
                                                    height="30px" width="33px" class="" alt="iamges">
                                                <span>Production</span>
                                            </a>
                                        </li>
                                    <?php } ?>
                                    <?php
                                    $fileurl = "admin/production/index";
                                    if (in_array($fileurl, $role_permissions)) {
                                    ?>
                                        <li style="padding:5px; text-align:center"><a title="Daily Sheet"
                                                href="<?php echo SITE_URL; ?>admin/production/index" data-toggle="tooltip">
                                                <img src="<?php echo SITE_URL; ?>images/headericons/productionindex.png"
                                                    height="30px" width="33px" class="" alt="iamges">
                                                <span>Daily Sheet</span>
                                            </a>
                                        </li>
                                    <?php } ?>
                                    <?php
                                    $fileurl = "admin/Inspection/index";
                                    if (in_array($fileurl, $role_permissions)) {
                                    ?>
                                        <li style="padding:5px; text-align:center"><a title="Inspection"
                                                href="<?php echo SITE_URL; ?>admin/Inspection/index" data-toggle="tooltip">
                                                <img src="<?php echo SITE_URL; ?>images/headericons/inspectionindex.png"
                                                    height="30px" width="33px" class="" alt="iamges">
                                                <span>Inspection</span>
                                            </a>
                                        </li>
                                    <?php } ?>
                                    <?php
                                    $fileurl = "admin/maintenance/index";
                                    if (in_array($fileurl, $role_permissions)) {
                                    ?>
                                        <li style="padding:5px; text-align:center"><a title="Maintenance"
                                                href="<?php echo SITE_URL; ?>admin/maintenance/index" data-toggle="tooltip">
                                                <img src="<?php echo SITE_URL; ?>images/headericons/maintenanceindex.png"
                                                    height="30px" width="33px" class="" alt="iamges">
                                                <span>Maintenance</span>
                                            </a>
                                        </li>
                                    <?php } ?>
                                    <?php
                                    $fileurl = "admin/Machine/index";
                                    if (in_array($fileurl, $role_permissions)) {
                                    ?>
                                        <li style="padding:5px; text-align:center"><a title="Machine"
                                                href="<?php echo SITE_URL; ?>admin/Machine/index" data-toggle="tooltip">
                                                <img src="<?php echo SITE_URL; ?>images/headericons/machineindex.png"
                                                    height="30px" width="33px" class="" alt="iamges">
                                                <span>Machine</span>
                                            </a>
                                        </li>
                                    <?php } ?>
                                    <?php
                                    $fileurl = "admin/stockregister/index";
                                    if (in_array($fileurl, $role_permissions)) {
                                    ?>
                                        <li style="padding:5px; text-align:center"><a title="Stock"
                                                href="<?php echo SITE_URL; ?>admin/stockregister/index"
                                                data-toggle="tooltip">
                                                <img src="<?php echo SITE_URL; ?>images/headericons/stockregisterindex.png"
                                                    height="30px" width="33px" class="" alt="iamges">
                                                <span>Stock</span>
                                            </a>
                                        </li>
                                    <?php } ?>
                                    <?php
                                    $role_permissions = $this->Permission->permissioncheck();
                                    $fileurl = "admin/stockregister/dailystock";
                                    if (in_array($fileurl, $role_permissions)) {
                                    ?>
                                        <li style="padding:5px; text-align:center">
                                            <a title="Daily Stock"
                                                href="<?php echo SITE_URL; ?>admin/stockregister/dailystock"
                                                data-toggle="tooltip">
                                                <img src="<?php echo SITE_URL; ?>images/headericons/stockregisterdailystock.png"
                                                    height="30px" width="33px" class="" alt="iamges">
                                                <span>Daily Stock</span>
                                            </a>
                                        </li>
                                    <?php } ?>
                      

                                      <?php
                                    $fileurl = "admin/jobchallan/index";
                                    if (in_array($fileurl, $role_permissions)) { 
                                    ?>
                                        <li style="padding:5px; text-align:center">
                                            <a title="Users" href="<?php echo SITE_URL; ?>admin/jobchallan/index"
                                                data-toggle="tooltip">
                                                <img src="<?php echo SITE_URL; ?>images/headericons/cheque.png"
                                                    height="30px" width="33px" class="" alt="iamges">
                                                <span>JC Challan</span>
                                            </a>
                                        </li>
                                    <?php } ?>
                                                <li class="dropdown" style="padding:5px; text-align:center; list-style:none; position:relative;">

    <a href="javascript:void(0);"
        class="dropdown-toggle"
        data-toggle="dropdown"
       
        style="display:block; cursor:pointer;">

        <img src="<?php echo SITE_URL; ?>images/headericons/setting.png"
            height="30px" width="33px" class="" alt="images">

        <span>Setting</span>
    </a>

   <ul class="dropdown-menu"
    style="
        display:none;
        position:absolute;
        top:100%;
        left:0;
        min-width:120px;
        background:#fff;
        border:1px solid #ddd;
        box-shadow:0 4px 10px rgba(0,0,0,0.10);
        padding:10px 4px;
        z-index:9999;
        list-style:none;
        margin:0;
    ">

    <!-- Categories -->
    <?php
    $fileurl = "admin/itemcategory/index";
    if (in_array($fileurl, $role_permissions)) {
    ?>
    <li style="margin:0; border-bottom:1px solid #eee;">
        <a href="<?php echo SITE_URL; ?>admin/itemcategory/index"
            data-toggle="tooltip"
            style="
                display:flex;
                align-items:center;
                justify-content:flex-start;
                gap:12px;
                padding:12px 18px;
                text-decoration:none;
                color:#333;
                width:100%;
                transition:0.3s;
                box-sizing:border-box;
            "
            onmouseover="this.style.background='#f7f7f7'"
            onmouseout="this.style.background='transparent'">

            <img src="<?php echo SITE_URL; ?>images/headericons/itemcategoryindex.png"
                height="30" width="33" alt="images">

            <span >Categories</span>
        </a>
    </li>
    <?php } ?>


    <!-- Products -->
    <?php
    $fileurl = "admin/additem/index";
    if (in_array($fileurl, $role_permissions)) {
    ?>
    <li style="margin:0; border-bottom:1px solid #eee;">
        <a href="<?php echo SITE_URL; ?>admin/additem/index"
            data-toggle="tooltip"
            style="
                display:flex;
                align-items:center;
                justify-content:flex-start;
                gap:12px;
                padding:12px 18px;
                text-decoration:none;
                color:#333;
                width:100%;
                transition:0.3s;
                box-sizing:border-box;
            "
            onmouseover="this.style.background='#f7f7f7'"
            onmouseout="this.style.background='transparent'">

            <img src="<?php echo SITE_URL; ?>images/headericons/additemindex.png"
                height="30" width="33" alt="images">

            <span >Products</span>
        </a>
    </li>
    <?php } ?>


    <!-- Tax -->
    <?php
    $fileurl = "admin/Taxmaster/index";
    if (in_array($fileurl, $role_permissions)) {
    ?>
    <li style="margin:0; border-bottom:1px solid #eee;">
        <a href="<?php echo SITE_URL; ?>admin/Taxmaster/index"
            data-toggle="tooltip"
            style="
                display:flex;
                align-items:center;
                justify-content:flex-start;
                gap:12px;
                padding:12px 18px;
                text-decoration:none;
                color:#333;
                width:100%;
                transition:0.3s;
                box-sizing:border-box;
            "
            onmouseover="this.style.background='#f7f7f7'"
            onmouseout="this.style.background='transparent'">

            <img src="<?php echo SITE_URL; ?>images/headericons/taxindex.png"
                height="30" width="33" alt="images">

            <span >Tax</span>
        </a>
    </li>
    <?php } ?>


    <!-- Suppliers -->
    <?php
    $fileurl = "admin/vendors/index";
    if (in_array($fileurl, $role_permissions)) {
    ?>
    <li style="margin:0; border-bottom:1px solid #eee;">
        <a href="<?php echo SITE_URL; ?>admin/vendors/index"
            data-toggle="tooltip"
            style="
                display:flex;
                align-items:center;
                justify-content:flex-start;
                gap:12px;
                padding:12px 18px;
                text-decoration:none;
                color:#333;
                width:100%;
                transition:0.3s;
                box-sizing:border-box;
            "
            onmouseover="this.style.background='#f7f7f7'"
            onmouseout="this.style.background='transparent'">

            <img src="<?php echo SITE_URL; ?>images/headericons/suuplierIco.png"
                height="30" width="33" alt="images">

            <span >Suppliers</span>
        </a>
    </li>
    <?php } ?>


    <!-- Users -->
    <?php
    $fileurl = "admin/roles/index";
    if (in_array($fileurl, $role_permissions)) {
    ?>
    <li style="margin:0;">
        <a href="<?php echo SITE_URL; ?>admin/roles/index"
            data-toggle="tooltip"
            style="
                display:flex;
                align-items:center;
                justify-content:flex-start;
                gap:12px;
                padding:12px 18px;
                text-decoration:none;
                color:#333;
                width:100%;
                transition:0.3s;
                box-sizing:border-box;
            "
            onmouseover="this.style.background='#f7f7f7'"
            onmouseout="this.style.background='transparent'">

            <img src="<?php echo SITE_URL; ?>images/headericons/vendorsindex.png"
                height="30" width="33" alt="images">

            <span >Users</span>
        </a>
    </li>
    <?php } ?>

</ul>
</li>

<script>
$(document).ready(function () {
    $(".dropdown").hover(
        function () {
            $(this).find(".dropdown-menu").stop(true, true).slideDown(200);
        },
        function () {
            $(this).find(".dropdown-menu").stop(true, true).slideUp(200);
        }
    );
});
</script>

                                        

                                </ul>
                            </li>

                            <!-- branch select box -->
                            <li>
                                <?php if ($role_id == '105' || $checked_by == '105') {
                                    $db_data_name = $this->request->session()->read('Auth.User.db');
                                ?>

                                    <select class="form-select form-control" aria-label="Default select example"
                                        style="width:80px; border:1px solid #999" onchange="changeGroupSchool(this.value);">

                                        <?php $branch_exps = explode("_", $branch[0]); ?>
                                        <?php foreach ($branch as $key => $value) {
                                            $branch_exp = explode("_", $value); ?>
                                            <option value="<?php echo $value; ?>" <?= ($db_data_name == $value) ? "selected" : ""; ?> id="dbname">
                                                <?php echo ucfirst($branch_exp[1]); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                <?php } ?>
                            </li>

                            <!-- Edit profile menu -->
                            <?php if ($role_id) { ?>
                                <li>
                                    <div class="dropdown user user-menu lgout_drop">
                                        <button class="btn btn-success dropdown-toggle" type="button"
                                            id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-expanded="false">
                                            <a href="#">
                                                <img style="border-radius: 50%;" src="<?php echo SITE_URL; ?>/img/images.jpg" class="user-image">
                                            </a>
                                        </button>
                                        <ul class="dropdown-menu bg-secondary" aria-labelledby="dropdownMenuButton2">
                                            <?php if ($role_id == 105 || $role_id == 1) { ?>
                                                <li>
                                                    <a class="dropdown-item" href="<?php echo $this->Url->build('/admin/sitesettings/add/1'); ?>">Profile</a>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                            <?php } ?>
                                            <li>
                                                <a class="dropdown-item " href="<?php echo $this->Url->build('/admin/users/changepassword'); ?>">Change
                                                    Password</a>
                                            </li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li>
                                                <a class="dropdown-item " href="<?php echo $this->Url->build('/logins/logout'); ?>">Sign out</a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </nav>

        </header>



        <div class="modal" id="globalModalkoi" tabindex="-1" role="dialog" aria-labelledby="esModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content personal">
                    <div class="modal-body">
                        <div class="loader">
                            <div class="es-spinner">
                                <i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            $(document).ready(function() {
                $(".globalModals").click(function(event) {
                    $('.personal').load($(this).attr("href"));
                });
            });
        </script>
        <script>
            $(document).ready(function() {
                $(".chk").click(function() {
                    $(".checkme").slideToggle();
                });
            });
        </script>
        <script>
            $(document).ready(function() {
                $(".ad_hd_tmnu a").click(function() {
                    $(".ad_hd_mnu").slideToggle();
                });
            });
        </script>
        <div class="modal" id="globalModalkjs" tabindex="-1" role="dialog" aria-labelledby="esModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content personal">
                    <div class="modal-body">
                        <div class="loader">
                            <div class="es-spinner">
                                <i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>


        <!----- for change branch  ------>
        <script>
            function changeGroupSchool(id) {
                $.ajax({
                    type: 'POST',
                    url: '<?php echo SITE_URL; ?>app/getbranchdetail',
                    data: {
                        'dbname': id
                    },
                    success: function(data) {
                        obj = JSON.parse(data);
                        var email = obj.email;
                        var password = obj.confirm_pass;
                        erp_login(obj.email, obj.confirm_pass, id);
                    },
                });
            }

            function erp_login(email, password, dbname) {
                var SITE_URL = '<?php echo SITE_URL; ?>';
                $.ajax({
                    type: 'POST',
                    url: '<?php echo SITE_URL; ?>app/erp_login',
                    data: {
                        'email': email,
                        'password': password,
                        'dbname': dbname,
                    },
                    success: function(data) {
                        // alert(data);
                        obj = JSON.parse(data);
                        window.location.replace(SITE_URL + "admin/dashboards/overview")
                    },
                });
            }
        </script>