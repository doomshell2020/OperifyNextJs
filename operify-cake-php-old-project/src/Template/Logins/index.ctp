<style>
    /* New Login */

    .d-flex {
        display: flex;
    }

    .login-box {
        width: auto;
        margin: 0px;
    }

    .login-box-body {
        background: none;
        padding: 0px;
        border-top: 0;
        color: #666;
        width: 350px;
    }

    .login-box {
        width: auto;
        margin: 0px;
        height: 100vh;
        background: #fff;
    }

    .loginLeft {
        flex: 1;
        height: 100vh;
        background: #fff;
        align-items: center;
        justify-content: center;
        display: flex;
        background-size: cover;
    }

    .rightPart .overlay {
        height: 100%;
        width: 100%;
        /* background: rgb(0 82 111 / 80%); */
        background: rgb(0 145 197);
        align-items: center;
        justify-content: center;
        display: flex;
        flex-direction: column;
    }

    .rightPart {
        flex: 1;
        height: 100vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: url(../images/loginBg.jpg) no-repeat center;
    }

    .login-box-msg {
        font-size: 16px;
        color: #333;
        font-weight: 700;
    }

    .form-control-feedback {
        left: 0;
        right: inherit;
    }

    .login-box-body .form-control {
        border: none;
        background: #eeeeee;
        padding: 10px;
        color: #333;
        height: auto;
        /* border-bottom: 1px solid #015579; */
    }

    .login-box-body .form-control::placeholder {
        color: #333;
    }

    .has-feedback .form-control {
        padding-left: 40px;
    }

    .login-box-body i.fa {
        position: absolute;
        left: 13px;
        top: 11px;
        color: #333;
        font-size: 20px;
    }

    .login-box-body .checkbox,
    .radio {
        position: relative;
        display: block;
        margin-top: 0px;
        margin-bottom: 0px;
    }

    .login-box-body .checkbox label {
        padding-left: 10px;
    }

    .login-box-body .submit .btn {
        /* background: #0091c5 !important;
        outline: none !important; */
        background: #e8f0fe !important;
        margin-top: 20px;
        padding: 10px;
        outline: none !important;
        color: #000;
    }

    .center {
        position: absolute;
        left: 0px;
        right: 0px;
        top: 40%;
        margin: auto;
        border-radius: 50%;
        box-shadow: 0px 0px 20px 0px #0002;
    }
</style>

<div class="d-flex">
    <img src="<?php echo SITE_URL; ?>images/loginIcon.png" class="center" />
    <div class="loginLeft">
        <lottie-player src="https://assets5.lottiefiles.com/private_files/lf30_fw6h59eu.json" background="transparent"
            speed="1" style="width: 100%; height: 450px;" loop autoplay></lottie-player>

    </div>
    <div class="rightPart">
        <div class="overlay">
            <div class="login-logo">
                <?php $filename2 = '/var/www/html/idsprime/webroot/images/' . $db . 'logo.png';
                if (file_exists($filename2)) { ?>
                    <a href="#">
                        <img src="<?php echo SITE_URL; ?>images/<?php echo $db; ?>logo.png" <?php if ($db == "kidsclub") { ?>
                            <?php } ?> class="user-image" alt="User Image">
                    </a>

                <?php } else { ?>
                    <a href="#" style="background:none; width:300px; margin-bottom:15px;filter: brightness(0) invert(1);max-width: 90%;"><img
                            src="<?php echo SITE_URL; ?>images/logo.png" class="user-image" alt="User Image"></a>
                <?php } ?>
            </div>
            <!-- /.login-logo -->
            <div class="login-box-body">
                <p class="login-box-msg"
                    style="font-size:20px; color:#fff; text-align:left !important; padding:0px; margin-bottom:10px; font-weight:400;">
                    Sign In To Start Your Session</p>
                <?php echo $this->Flash->render() ?>

                <?php echo $this->Form->create('Logins', array(
                    'url' => array('controller' => 'logins', 'action' => 'newindex'),
                    'id' => 'login_form',
                    'autocomplete' => 'off'
                )); ?>

                <div class="form-group has-feedback">
                    <!-- <span class="glyphicon glyphicon-envelope form-control-feedback"></span> -->
                    <i class="fa fa-user" aria-hidden="true"></i>
                    <?php echo $this->Form->input('mobile', array('class' => 'form-control', 'type' => 'number', 'value' => $email, 'placeholder' => 'Enter Mobile No', 'id' => 'mobile', 'label' => false)); ?>
                </div>

                <div class="form-group has-feedback">
                    <!-- <span class="glyphicon glyphicon-lock form-control-feedback"></span> -->
                    <i class="fa fa-lock" aria-hidden="true"></i>
                    <?php echo $this->Form->input('password', array('type' => 'password', 'class' => 'form-control', 'placeholder' => 'Password', 'value' => $password, 'autocomplete' => "on", 'label' => false)); ?>
                    <?php echo $this->Form->input('db', array('type' => 'hidden', 'id' => 'db-name', 'value' => $db, 'label' => false)); ?>
                </div>




                <div class="checkbox icheck" style="margin-top:20px;">
                    <label style="color:#fff;">
                        <?php if ($remember_me == 1) {
                            $checked = 'checked';
                        }
                        echo $this->Form->input('remember_me', array('type' => 'checkbox', 'checked' => $checked, 'style' => 'margin-right:-60px!important; width:auto;', 'value' => 1));

                        ?>
                    </label>
                </div>

                <?php
                echo $this->Form->submit(
                    'Sign In',
                    array('class' => 'btn btn-primary btn-block btn-flat', 'style' => 'font-weight:700;', 'title' => 'Sign In')
                ); ?>

                <?php echo $this->Form->end(); ?>
            </div>
        </div>
    </div>
</div>

<style>
    input[type='radio']:checked:after {
        width: 15px;
        height: 15px;
        border-radius: 15px;
        top: -2px;
        left: -1px;
        position: relative;
        background-color: #ffa500;
        content: '';
        display: inline-block;
        visibility: visible;
        border: 2px solid white;
    }

    input[type='checkbox']:checked:after {
        width: 15px;
        height: 15px;
        border-radius: 1px;
        top: -2px;
        left: -1px;
        position: relative;
        /* background-color: #ffa500; */
        content: '';
        display: inline-block;
        visibility: visible;
        border: 2px solid white;
    }
</style>
<script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>

<?php  /* ?>
<div class="login-logo">
 <?php  $filename2='/var/www/html/idsprime/webroot/images/' . $db . 'logo.png';

if (file_exists($filename2)) { ?>
<a href="#"><img src="<?php echo SITE_URL; ?>images/<?php echo $db; ?>logo.png"
<?php if($db=="kidsclub"){ ?>  <?php } ?> class="user-image" alt="User Image"></a>

<?php }else{ ?>
<a href="#"><img src="<?php echo SITE_URL; ?>images/idsprimelogo.png"
     class="user-image" alt="User Image"></a>
<?php } ?>

</div>
<!-- /.login-logo -->
<div class="login-box-body">
 <p class="login-box-msg">Sign In To Start Your Session</p>
<?php echo $this->Flash->render() ?>

<?php echo $this->Form->create('Logins', array(
'url' => array('controller' => 'logins', 'action' => 'index'),
'id' => 'login_form',
)); ?>


 <div class="form-group has-feedback">
<?php echo $this->Form->input('email', array('class' => 'form-control', 'type' => 'text', 'value' => $email, 'placeholder' => 'Enter Email', 'id' => 'email', 'label' => false)); ?>

   <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
 </div>
 <div class="form-group has-feedback">
<?php echo $this->Form->input('password', array('type' => 'password', 'class' => 'form-control', 'placeholder' => 'Enter Password', 'value' => $password, 'id' => 'password', 'label' => false)); ?>
<?php echo $this->Form->input('db', array('type' => 'hidden', 'value' => $db, 'label' => false)); ?>


   <span class="glyphicon glyphicon-lock form-control-feedback"></span>
 </div>
 <div class="row">
   <div class="col-xs-8">
     <div class="checkbox icheck">
       <label>

<?php if ($remember_me == 1) {
$checked = 'checked';
}
echo $this->Form->input('remember_me', array('type' => 'checkbox', 'checked' => $checked, 'style' => 'margin-left:-64px!important;', 'value' => 1));
?>
       </label>
     </div>
   </div>

   <!-- /.col -->
   <div class="col-xs-4">


<?php
echo $this->Form->submit(
'Sign In',
array('class' => 'btn btn-primary btn-block btn-flat', 'title' => 'Sign In')
); ?>

   </div>
   <!-- /.col -->
 </div>
<?php echo $this->Form->end(); ?>
</div>

<?php */ ?>