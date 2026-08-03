<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>IDsPrime-ERP</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <?=$this->Html->css('admin/bootstrap.min.css')?>

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <?=$this->Html->css('admin/AdminLTE.min.css')?>
  <?=$this->Html->meta(
    'favicon.ico',
    'img/favicon.ico',
    ['type' => 'icon']
);?>
  <!-- iCheck -->
  <?=$this->Html->css('admin/blue.css')?>
  <?=$this->Html->css('style.css')?>
  <?=$this->Html->css('admin/validationEngine.jquery.css')?>
  <?=$this->Html->css('admin/template.css')?>

  <?=$this->Html->script('admin/jquery-2.2.3.min.js')?>

  <?=$this->Html->script('admin/bootstrap.min.js')?>
  <?=$this->Html->script('admin/languages/jquery.validationEngine-en.js')?>
  <?=$this->Html->script('admin/jquery.validationEngine.js')?>

  <?=$this->Html->script('admin/custom.js')?>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>


</head>

<body class="hold-transition login-page">
  <div class="login-box">
  
      <?=$this->fetch('content')?>

   
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
          <?php echo $this->Form->create('Logins', array(
    'url' => array('controller' => 'logins', 'action' => 'forgot'),
    'id' => 'forgot_form',
)); ?>
          <div class="form-group has-feedback">
            <?php echo $this->Form->input('emaifsdfdsfdfsl', array('class' => 'form-control validate[required, custom[email]]', 'data-prompt-position' => 'topRight:-38,-15', 'type' => 'text', 'placeholder' => 'Email', 'id' => 'email', 'label' => false)); ?>
            <?php echo $this->Form->input('db', array('type' => 'hidden', 'label' => false, 'value' => $db)); ?>

          </div>
          <div class="col-xs-4">
            <?php
echo $this->Form->submit(
    'Submit',
    array('class' => 'btn btn-primary btn-block btn-flat', 'title' => 'Submit')
); ?>

          </div>
          <div class="col-xs-4">
            <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button> </div>
          <?php echo $this->Form->end(); ?>
        </div>
        <div class="modal-footer">

        </div>
      </div>

    </div>
  </div>

  <?=$this->Html->script('admin/icheck.min.js')?>
</body>

</html>