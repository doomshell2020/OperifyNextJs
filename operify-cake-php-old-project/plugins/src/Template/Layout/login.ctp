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
  <?= $this->Html->css('admin/AdminLTE.min.css') ?>	
 
  <!-- iCheck -->
  <?= $this->Html->css('admin/blue.css') ?>
  <?= $this->Html->css('style.css') ?>
<?= $this->Html->css('admin/validationEngine.jquery.css') ?>
<?= $this->Html->css('admin/template.css') ?>

<?= $this->Html->script('admin/jquery-2.2.3.min.js') ?>

<?= $this->Html->script('admin/bootstrap.min.js') ?>
 <?= $this->Html->script('admin/languages/jquery.validationEngine-en.js') ?>
<?= $this->Html->script('admin/jquery.validationEngine.js') ?>

<?= $this->Html->script('admin/custom.js') ?>
<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>

  <![endif]-->
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="<?php echo $this->Url->build('/admin'); ?>"><b>School </b>ERP</a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg">Sign in to start your session</p>
	<?= $this->fetch('content') ?>
    

    
    <!-- /.social-auth-links -->
	
    <a style="cursor:pointer;"  data-toggle="modal" data-target="#myModal">I forgot my password</a><br>
   

  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->


<?php echo $user; ?>



  <!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Forgot Password</h4>
        </div>
        <div class="modal-body">
         <?php echo $this->Form->create('Logins',array(
                       'url'=>array('controller' => 'logins', 'action' => 'forgot'),
                       'id' => 'forgot_form',
                       )); ?>
	 <div class="form-group has-feedback">
	<?php echo $this->Form->input('email',array('class'=>'form-control validate[required, custom[email]]','data-prompt-position'=>'topRight:-38,-15','placeholder'=>'Email','id'=>'email','label' =>false)); ?>
        
      </div>	
	 <div class="col-xs-4">
	<?php
	echo $this->Form->submit(
	    'Submit', 
	    array('class' => 'btn btn-primary btn-block btn-flat', 'title' => 'Submit')
	); ?>
         
        </div>

	 <?php echo $this->Form->end(); ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>
 
<?= $this->Html->script('admin/icheck.min.js') ?>









</body>
</html>
