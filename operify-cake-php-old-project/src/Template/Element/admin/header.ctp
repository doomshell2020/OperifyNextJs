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
    <?php echo $this->Html->css('admin/bootstrap5/css/bootstrap.min.css') ?>

    <?= $this->Html->meta(
        'favicon.ico',
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
    <?= $this->Html->css('admin/dataTables.bootstrap.css') ?>
    <?= $this->Html->css('admin/AdminLTE.min.css') ?>
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <?= $this->Html->css('admin/skins/_all-skins.min.css') ?>
    <!-- iCheck -->
    <?= $this->Html->css('admin/blue.css') ?>
    <!-- Morris chart -->
    <?= $this->Html->css('admin/morris.css') ?>
    <!-- jvectormap -->
    <?= $this->Html->css('admin/jquery-jvectormap-1.2.2.css') ?>
    <!-- Date Picker -->
    <?= $this->Html->css('admin/datepicker3.css') ?>
    <!-- Daterange picker -->
    <?= $this->Html->css('admin/daterangepicker.css') ?>
    <!-- bootstrap wysihtml5 - text editor -->
    <?= $this->Html->css('admin/bootstrap3-wysihtml5.min.css') ?>
    <?= $this->Html->css('admin/responsive.css') ?>
    <?= $this->Html->css('admin/style.css') ?>
    <?= $this->Html->script('admin/jquery-2.2.3.min.js') ?>
    <?= $this->Html->script('admin/bootstrap.min.js') ?>
    <?= $this->Html->script('timepicker/bootstrap-timepicker.min.js') ?>

    <?= $this->Html->css('timepicker/bootstrap-timepicker.min.css') ?>
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
            padding: 0px 10px !important;
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
                // $franchise_db = $get_franchise[0]['franchise_db'];
                // $branch = explode(",", $franchise_db);
                $branch = [];
                foreach ($get_franchise as $get) {

                    $branch[] = $get['db'];
                    // $branch[] = explode("_", $b_name);
                }
            } ?>


            <!-- Header Navbar: style can be found in header.less -->
            <nav class="navbar navbar-static-top">

                <div class="d-flex justify-content-between" style="width: 100%;">

                    <div class="drawerLogo">
                        <?php if ($role_id == '105' || $role_id == '1') { ?>
                            <a href="<?php echo SITE_URL; ?>admin/dashboards/overview">
                            <?php } elseif ($role_id == '6') { ?>
                                <a href="<?php echo SITE_URL; ?>admin/dashboards/overview">
                                <?php } elseif ($role_id == '101') { ?>
                                    <a href="<?php echo SITE_URL; ?>admin/Sitesettings/index">
                                    <?php } else { ?>
                                        <a href="#">

                                        <?php }
                                          $findlogo = $this->Comman->findlogo();
                                          // pr($findlogo);die;
                                        ?>
                                        <?php if ($role_id == '101') { ?>
                                            <img src="<?php echo SITE_URL; ?>images/<?php echo $findlogo['small_logo']; ?>"
                                                class="fullLogo" alt="logo">

                                        <?php } else { ?>
                                            <img src="<?php echo SITE_URL . 'images/' . $findlogo['small_logo']; ?>"
                                                class="fullLogo" alt="logo">


                                        <?php } ?>
                                        </a>
                    </div>
                    <?php if ($role_id != '101') { ?>
                        <div style="padding-top: 17px;  font-size: 13px;">
                            <?php echo $findlogo['alias']; ?>
                        </div>
                    <?php } ?>
                    <?php
                    if ($rolepresent == 16) {
                        $payroll = $this->payroll();
                    }
                    ?>

                    <div class="navbar-custom-menu">

                        <!-- Header menu name -->
                        <ul class="nav navbar-nav nt_menu_align">
                            <li>
                                <ul style="display:flex;">


                                    <li style="padding:8px; text-align:center">
                                        <a title="Companies" href="<?php echo SITE_URL; ?>admin/users/add"
                                            data-toggle="tooltip">
                                            <img src="<?php echo SITE_URL; ?>images/headericons/vendorsindex.png"
                                                height="30px" width="33px" class="" alt="iamges">
                                            <span>Companies</span>
                                        </a>
                                    </li>

                                    <li style="padding:8px; text-align:center">
                                        <a title="Template" href="<?php echo SITE_URL; ?>admin/template/index"
                                            data-toggle="tooltip">
                                            <img src="<?php echo SITE_URL; ?>images/headericons/contractsindex.png"
                                                height="30px" width="33px" class="" alt="iamges">
                                            <span>Template</span>
                                        </a>
                                    </li>

                                    <li style="padding:8px; text-align:center">
                                        <a title="Permission" href="<?php echo SITE_URL; ?>admin/Permission/index"
                                            data-toggle="tooltip">
                                            <img src="<?php echo SITE_URL; ?>images/headericons/productionproductionorders.png"
                                                height="30px" width="33px" class="" alt="iamges">
                                            <span>Permission</span>
                                        </a>
                                    </li>


                                    <li style="padding:8px; text-align:center">
                                        <a title="Demo Request" href="<?php echo SITE_URL; ?>admin/Demorequest/index"
                                            data-toggle="tooltip">
                                            <img src="<?php echo SITE_URL; ?>images/headericons/productionproductionorders.png"
                                                height="30px" width="33px" class="" alt="iamges">
                                            <span>Demo Request</span>
                                        </a>
                                    </li>

                                    <li style="padding:8px; text-align:center">
                                        <a title="Demo Request" href="<?php echo SITE_URL; ?>admin/Spam/index"
                                            data-toggle="tooltip">
                                            <img src="<?php echo SITE_URL; ?>images/headericons/productionproductionorders.png"
                                                height="30px" width="33px" class="" alt="iamges">
                                            <span>Spam</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <li>
                                <div class="dropdown user user-menu lgout_drop">
                                    <button class="btn btn-success dropdown-toggle" type="button"
                                        id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-expanded="false">
                                        <a href="#">
                                            <img style="border-radius: 50%;"
                                                src="<?php echo SITE_URL; ?>/img/images.jpg" class="user-image">
                                            <span class="hidden-xs" style=" display: inline-block; color:white; ">
                                                <?php echo ucfirst($this->request->session()->read('Auth.User.user_name')); ?>
                                            </span>
                                        </a>
                                    </button>
                                    <ul class="dropdown-menu bg-secondary" aria-labelledby="dropdownMenuButton2">
                                        <?php if ($role_id == 105 || $role_id == 6) { ?>
                                            <li><a class="dropdown-item "
                                                    href="<?php echo $this->Url->build('/admin/sitesettings/add/1'); ?>">Profile</a>
                                            </li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                        <?php } ?>

                                        <li><a class="dropdown-item "
                                                href="<?php echo $this->Url->build('/admin/users/changepassword'); ?>">Change
                                                Password</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item "
                                                href="<?php echo $this->Url->build('/logins/logout'); ?>">Sign out</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

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
                        obj = JSON.parse(data);
                        if (obj.role_id == '6') {
                            window.location.replace(SITE_URL + "admin/dashboards/overview")
                        } else {
                            window.location.replace(SITE_URL + "admin/dashboards/headbranch")
                        }
                    },
                });
            }
        </script>