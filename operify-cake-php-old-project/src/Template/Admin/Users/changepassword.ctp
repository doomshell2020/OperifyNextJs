<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>

			Profile Manager
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-home"></i>Home</a></li>
			<li><a href="#">Manage Profile</a></li>
		</ol>
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

						<h3 class="box-title">Change Profile Setting</h3>


					</div>
					<!-- /.box-header -->
					<!-- form start -->
					<?php //pr($page); die; ?>
					<?php echo $this->Flash->render(); ?>



					<?php echo $this->Form->create($users, array(

						'class' => 'form-horizontal',
						'id' => 'sitesetting_form',
						'enctype' => 'multipart/form-data',
						'novalidate'
					)); ?>

					<div class="box-body">



						<div class="form-group">
							<label for="inputEmail3" class="col-sm-2 control-label">New Password</label>

							<div class="col-sm-5">
								<?php echo $this->Form->password('new_password', array('class' => 'form-control', 'placeholder' => 'New Password', 'maxlength' => '40', 'id' => 'password', 'label' => false)); ?>

								<input type="hidden" name="userssid" value="<? echo $userssid; ?>">

							</div>
						</div>
						<div class="form-group">
							<label for="inputEmail3" class="col-sm-2 control-label">Confirm Password</label>

							<div class="col-sm-5">
								<?php echo $this->Form->input('confirm_pass', array('class' => 'form-control', 'placeholder' => 'Confirm Password', 'value' => '', 'maxlength' => '40', 'id' => 'confirm_pass', 'label' => false)); ?>

							</div>
						</div>
					
						<div class="box-footer">

							<div class="col-sm-8">
								<?php
								if (isset($work['id'])) {
									echo $this->Form->submit(
										'Update',
										array('class' => 'btn btn-info pull-right', 'title' => 'Update')
									);
								} else {
									echo $this->Form->submit(
										'Submit',
										array('class' => 'btn btn-info pull-right', 'title' => 'Add')
									);
								}
								?>
							</div>
						</div>
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
	$('#checkbox1').change(function () {
		if (this.checked)
			$('.passdata').show();
		else
			$('.passdata').hide();

	});
</script>