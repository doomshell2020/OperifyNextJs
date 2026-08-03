<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      Site Settings Manager
       
      </h1>
      
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
       <!--/.col (left) -->
        <!-- right column -->
        <div class="col-md-12">
          <!-- Horizontal Form -->
          <div class="box box-info">
		    <div class="box-header with-border">
		      <h3 class="box-title">Add site setting</h3>
		    </div>
            <!-- /.box-header -->
            <!-- form start -->
<?php //pr($page); die; ?>
		<?php echo $this->Flash->render(); ?>

 
            
		<?php echo $this->Form->create($sitesetting , array(
                       
                       'class'=>'form-horizontal',
			'id' => 'sitesetting_form',
                       'enctype' => 'multipart/form-data',
                       'novalidate'
                     	)); ?>
		   
		      <div class="box-body">
		        <div class="form-group">
		          <label for="inputEmail3" class="col-sm-2 control-label">First Name</label>

		          <div class="col-sm-10">
			<?php echo $this->Form->input('first_name',array('class'=>'form-control','placeholder'=>'First Name', 'id'=>'first_name','label' =>false)); ?>
		           
		          </div>
		        </div>
			<div class="form-group">
		          <label for="inputEmail3" class="col-sm-2 control-label">Last Name</label>

		          <div class="col-sm-10">
			<?php echo $this->Form->input('last_name',array('class'=>'form-control','placeholder'=>'Last Name', 'id'=>'last_name','label' =>false)); ?>
		           
		          </div>
		        </div>
			<div class="form-group">
		          <label for="inputEmail3" class="col-sm-2 control-label">Contact Email</label>

		          <div class="col-sm-10">
			<?php echo $this->Form->input('contact_email',array('class'=>'form-control','placeholder'=>'Contact Email', 'id'=>'contact_email','label' =>false)); ?>
		           
		          </div>
		        </div>
			<div class="form-group">
		          <label for="inputEmail3" class="col-sm-2 control-label">Facebook URL</label>

		          <div class="col-sm-10">
			<?php echo $this->Form->input('facebook_url',array('class'=>'form-control','placeholder'=>'Facebook URL', 'id'=>'facebook_url','label' =>false)); ?>
		           
		          </div>
		        </div>
			<div class="form-group">
		          <label for="inputEmail3" class="col-sm-2 control-label">Twitter URL</label>

		          <div class="col-sm-10">
			<?php echo $this->Form->input('twitter_url',array('class'=>'form-control','placeholder'=>'Twitter URL', 'id'=>'twitter_url','label' =>false)); ?>
		           
		          </div>
		        </div>
			<div class="form-group">
		          <label for="inputEmail3" class="col-sm-2 control-label">Site Title</label>

		          <div class="col-sm-10">
			<?php echo $this->Form->input('site_title',array('class'=>'form-control','placeholder'=>'Site Title', 'id'=>'site_title','label' =>false)); ?>
		           
		          </div>
		        </div>
			<div class="form-group">
		          <label for="inputEmail3" class="col-sm-2 control-label">Site Keywords</label>

		          <div class="col-sm-10">
			<?php echo $this->Form->input('site_keywords',array('class'=>'form-control','placeholder'=>'Site Keywords', 'id'=>'site_keyword','label' =>false)); ?>
		           
		          </div>
		        </div>
			<div class="form-group">
		          <label for="inputEmail3" class="col-sm-2 control-label">Site Description</label>

		          <div class="col-sm-10">
			<?php echo $this->Form->textarea('site_description',array('class'=>'form-control','placeholder'=>'Site Description', 'id'=>'site_description','label' =>false)); ?>
		           
		          </div>
		        </div>
			 <div class="form-group">
		          <label for="inputEmail3" class="col-sm-2 control-label">Google Analytics</label>

		          <div class="col-sm-10">
			<?php echo $this->Form->textarea('google_analytics',array('class'=>'form-control','placeholder'=>'Google Analytics', 'id'=>'google_analytics','label' =>false)); ?>
		           
		          </div>
		        </div>
			<div class="form-group">
		          <label for="inputEmail3" class="col-sm-2 control-label pass"> <?php echo $this->Form->input('check', array('type'=>'checkbox','id'=>'checkbox1','label' =>false,'style'=>'vertical-align:middle')); ?></label>
			 <div class="col-sm-10 ">
		         
			 Do you wish to change password
		           </div>
		         
		        </div>
			<div class="passdata" style="display:none;">
			<div class="form-group">
		          <label for="inputEmail3" class="col-sm-2 control-label">Current Password</label>

		          <div class="col-sm-10">
			<?php echo $this->Form->input('current_password',array('class'=>'form-control','placeholder'=>'Current Password', 'id'=>'current_password','label' =>false)); ?>
		           
		          </div>
		        </div>
			<div class="form-group">
		          <label for="inputEmail3" class="col-sm-2 control-label">New Password</label>

		          <div class="col-sm-10">
			<?php echo $this->Form->input('new_password',array('class'=>'form-control','placeholder'=>'New Password', 'id'=>'password','label' =>false)); ?>
		           
		          </div>
		        </div>
			<div class="form-group">
		          <label for="inputEmail3" class="col-sm-2 control-label">Confirm Password</label>

		          <div class="col-sm-10">
			<?php echo $this->Form->input('confirm_pass',array('class'=>'form-control','placeholder'=>'Confirm Password', 'id'=>'confirm_pass','label' =>false)); ?>
		           
		          </div>
		        </div>
			</div>
		        
		        
		      </div>
		      <!-- /.box-body -->
		      <div class="box-footer">
			<?php
			echo $this->Html->link('Cancel', [
			'controller' => 'dashboards',	
			    'action' => 'index'
			   
			],['class'=>'btn btn-default']); ?>
		      
			<?php
				if(isset($work['id'])){
				echo $this->Form->submit(
				    'Update', 
				    array('class' => 'btn btn-info pull-right', 'title' => 'Update')
				); }else{ 
				echo $this->Form->submit(
				    'Add', 
				    array('class' => 'btn btn-info pull-right', 'title' => 'Add')
				);
				}
		       ?>
		      </div>
		      <!-- /.box-footer -->
		  <?php echo $this->Form->end(); ?>
          </div>
         
        </div>
        <!--/.col (right) -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>

<script type="text/javascript">
$('#checkbox1').change(function(){
        if(this.checked)
            $('.passdata').show();
        else
            $('.passdata').hide();

    });
</script>


