
<?php echo $this->Flash->render() ?>

<?php echo $this->Form->create('Logins',array(
                       'url'=>array('controller' => 'logins', 'action' => 'index'),
                       'id' => 'login_form',
                       )); ?>


      <div class="form-group has-feedback">
	<?php echo $this->Form->input('email',array('class'=>'form-control','value'=>$email,'placeholder'=>'Email','id'=>'email','label' =>false)); ?>
        
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
<?php echo $this->Form->input('password',array('type'=>'password','class'=>'form-control','placeholder'=>'Password','value'=>$password, 'id'=>'password','label' =>false)); ?>
        
       
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="row">
        <div class="col-xs-8">
          <div class="checkbox icheck">
            <label>

<?php if($remember_me == 1){
		$checked = 'checked';
	}	
 echo $this->Form->input('remember_me',array('type'=>'checkbox', 'checked'=>$checked, 'value'=>1)); 
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
