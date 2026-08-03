<!-- Content Wrapper. Contains page content -->

  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
	    <section class="content-header">
		      <h1>
			State Manager
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
		      <h3 class="box-title"><?php if(isset($state['id'])){ echo 'Edit State'; }else{ echo 'Add State';} ?></h3>
		    </div>
            <!-- /.box-header -->
            <!-- form start -->

		<?php echo $this->Flash->render(); ?>

 
          
		<?php echo $this->Form->create($state, array(
                       
                       'class'=>'form-horizontal',
			'id' => 'sevice_form',
                       'enctype' => 'multipart/form-data',
                       'novalidate'
                     	)); ?>
		   
		      <div class="box-body">
				    <div class="form-group">
		          <label for="inputEmail3" class="col-sm-2 control-label">Select Country</label>

		          <div class="col-sm-10">
			<?php echo $this->Form->input('c_id',array('class'=>'form-control','required', 'empty'=>'Select Country','type' => 'select','options'=>$Country,'id'=>'title','label' =>false)); ?>
		           
		          </div>
		        </div>
		        <div class="form-group">
		          <label for="inputEmail3" class="col-sm-2 control-label">State</label>

		          <div class="col-sm-10">
			<?php echo $this->Form->input('name',array('class'=>'form-control','placeholder'=>'State','required', 'id'=>'title','label' =>false)); ?>
		           
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
				if(isset($state['id'])){
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




