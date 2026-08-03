<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      House Manager
       
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
		      <h3 class="box-title"><?php if(isset($work['id'])){ echo 'Edit House'; }else{ echo 'Add House';} ?></h3>
		    </div>
            <!-- /.box-header -->
            <!-- form start -->
<?php //pr($page); die; ?>
		<?php echo $this->Flash->render(); ?>

 
            
		<?php echo $this->Form->create($work , array(
                       
                       'class'=>'form-horizontal',
			'id' => 'work_form',
                       'enctype' => 'multipart/form-data',
                       'novalidate'
                     	)); ?>
		   
		      <div class="box-body">
		        <div class="form-group">
		          <label for="inputEmail3" class="col-sm-2 control-label">Title</label>

		          <div class="col-sm-10">
			<?php echo $this->Form->input('name',array('class'=>'form-control','placeholder'=>'Title', 'id'=>'title','label' =>false)); ?>
		           
		          </div>
		        </div>
			 
			
		        
		      </div>
		      <!-- /.box-body -->
		      <div class="box-footer">
			<?php
			echo $this->Html->link('Back', [
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




