<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>School ERP</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
	<?= $this->Html->css('admin/bootstrap.min.css') ?>
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
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
 <?= $this->Html->css('admin/style.css') ?>
<?= $this->Html->script('admin/jquery-2.2.3.min.js') ?>
<?= $this->Html->script('admin/bootstrap.min.js') ?>
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <header class="main-header">
    <!-- Logo -->
	    <a href="<?php echo $this->Url->build('/admin/dashboards'); ?>" class="logo">
	      <!-- mini logo for sidebar mini 50x50 pixels -->
	      <span class="logo-mini"><b>School ERP</b></span>
	      <!-- logo for regular state and mobile devices -->
	      <span class="logo-lg"><b>School ERP</b></span>
	    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
		    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
		      <img src="<?php echo SITE_URL;?>/img/user2-160x160.jpg" class="user-image" alt="User Image">
		      <span class="hidden-xs"><?php echo ucfirst($this->request->session()->read('Auth.User.user_name'));?></span>
		    </a>
		    <ul class="dropdown-menu">
		      <!-- User image -->
		      
		      <li class="user-header">
		       <p>
		         <?php echo ucfirst($this->request->session()->read('Auth.User.user_name'));?>
		        </p>
		      </li>
		     <!-- Menu Footer-->
		      <li class="user-footer">
		        <div class="pull-left">
		          <a href="<?php echo $this->Url->build('/admin/sitesettings/add'); ?>" class="btn btn-default btn-flat">Profile</a>
		        </div>
		        <div class="pull-right">
		          <a href="<?php echo $this->Url->build('/logins/logout'); ?>" class="btn btn-default btn-flat" >Sign out</a>
		        </div>
		      </li>
		    </ul>
          </li>
         
        </ul>
      </div>
    </nav>
  </header>
