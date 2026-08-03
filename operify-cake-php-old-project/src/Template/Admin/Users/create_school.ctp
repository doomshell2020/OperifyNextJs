<style>
.grid-cointainer{
	display:grid;
	grid-template-columns:1fr 1fr;
	grid-gap: 10px;
}
.grid-item{
	
}
</style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
	    <section class="content-header">
		      <h1>
		IDSPrime School Setup
		      </h1>
	    </section>
		<div class="box">
	<?php echo $this->Flash->render();  ?>
    <!-- Main content -->
    <section class="content">
<?php echo $this->Form->create($school, array('class'=>'form-horizontal','id' => 'sevice_form','enctype' => 'multipart/form-data'));?>
<div class="grid-cointainer">
	
           	 <div class="grid-item">
					<label class="col-sm-3 control-label">Database</label>
					<div class="field col-sm-6">
					<?php echo $this->Form->input('database_name', array('class' => 
					'longinput form-control','required','placeholder'=>'Database','required','label'=>false)); ?></div>
             </div>
           	

           	 <div class="grid-item">
					<div class="field col-sm-6">
					<?php echo $this->Form->submit('Add',array('class' => 'btn btn-info pull-right', 'title' => 'Create')); ?></div>
             </div>
             

<?php $this->Form->end(); ?>
</section>
</div>

</div>
