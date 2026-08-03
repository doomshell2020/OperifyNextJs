<!-- Content Wrapper. Contains page content -->

  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
	    <section class="content-header">
		      <h1>
			Department Manager
		      </h1>
	    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
       
        <!-- right column -->
        <div class="col-md-12">
          <!-- Horizontal Form -->
          <div class="box box-info">
		    <div class="box-header with-border">
		      <h3 class="box-title"><?php if(isset($department['id'])){ echo 'Edit Department'; }else{ echo 'Add Department';} ?></h3>
		    </div>
            <!-- /.box-header -->
            <!-- form start -->

		<?php echo $this->Flash->render(); ?>

 
          
		<?php echo $this->Form->create($department, array(
                       
                       'class'=>'form-horizontal',
			'id' => 'sevice_form',
                       'enctype' => 'multipart/form-data',
                       'novalidate'
                     	)); ?>
		   
		      <div class="box-body">
		        <div class="form-group">
		          <label for="inputEmail3" class="col-sm-2 control-label">Title</label>

		          <div class="col-sm-10">
			<?php echo $this->Form->input('name',array('class'=>'form-control','placeholder'=>'Title','required', 'id'=>'title','label' =>false)); ?>
		           
		          </div>
		        </div>
		        
		         <div class="box-body">
		        <div class="form-group">
		          <label for="inputEmail3" class="col-sm-2 control-label">Alias</label>

		          <div class="col-sm-10">
		
    
           <?php echo $this->Form->input('n_name',array('class'=>'form-control','placeholder'=>'Alias','required'=>1,'label' =>false)); ?>
		           
		          </div>
		        </div>

		     
			 
		      <!-- /.form group -->
		        
		      </div>
		      <!-- /.box-body -->
		      <div class="box-footer">
			<?php
			echo $this->Html->link('Back', [
			    'action' => 'index'
			   
			],['class'=>'btn btn-default']); ?>
		      
			<?php
				if(isset($department['id'])){
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




