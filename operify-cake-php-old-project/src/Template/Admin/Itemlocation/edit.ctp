<style>
  .input_fields_wrap .form-control{ margin-bottom:15px;}
</style>

<?php $batchdata=$this->Comman->findbatchcode($product['id']); ?>
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>
			Item Location Manager
		</h1>
		<ol class="breadcrumb">
			<li><a href="<?php echo ADMIN_URL; ?>dashboards"><i class="fa fa-home"></i>Home</a></li>

			<?php if(isset($product['id'])){ ?>
				<li class="active"><a href="javascript:void(0)">Edit Item Location </a></li>   
			<?php } else { ?>
				<li class="active"><a href="javascript:void(0)">Add Item Location</a></li>
			<?php } ?>
		</ol>
	</section>

	<!-- Main content -->
	<section class="content">
		<div class="row">

			<!-- right column -->
			<div class="col-md-12">
				<!-- Horizontal Form -->
				<div class="box box-info">
					<?php echo $this->Flash->render(); ?>
					<div class="box-header with-border">
						<h3 class="box-title"><i class="fa fa-plus-square" aria-hidden="true"></i> <?php if(isset($itemlocation['0']['id'])){ echo 'Edit Item Location'; }else{ echo 'Edit Item Location';} ?></h3>
					</div>
					<!-- /.box-header -->
					<!-- form start -->
					<?php echo $this->Form->create($itemlocation, array(
						'class'=>'form-horizontal',
						'enctype' => 'multipart/form-data',
						'validate'
					)); ?>
					<div class="box-body">

						<div class="form-group">
							<label for="inputEmail3" class="col-sm-4 control-label">Item location Name<strong style='color:red;'>*</strong></label>

							<div class="col-sm-6">
								<?php echo $this->Form->input('location_name', array('class' => 'form-control', 'type' => 'text', 'required', 'label' => false, 'placeholder' => 'Enter location name', 'autofocus', 'autocomplete' => 'off')); ?>
							</div>
							</div>

							<div class="form-group">
							<label for="inputEmail3" class="col-sm-4 control-label">Description</label>

							<div class="col-sm-6 ">
								<?php echo $this->Form->input('description', array('class' => 'form-control','type'=>'text','label'=>false,'placeholder'=>'description','autofocus','autocomplete'=>'off')); ?>
							</div>
							
							</div>			
						

				</div>
					<!-- /.box-body -->
					<div class="box-footer">
						<?php
						if(isset($itemlocation['id'])){
							echo $this->Form->submit(
								'Update', 
								array('class' => 'btn btn-info pull-right', 'title' => 'Update')
							); }else{ 
								echo $this->Form->submit(
									'Add', 
									array('class' => 'btn btn-info pull-right', 'title' => 'Add')
								);
							}
						?><?php
							echo $this->Html->link('Back', [
								'action' => 'index'

							],['class'=>'btn btn-default']); ?>
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

	
	