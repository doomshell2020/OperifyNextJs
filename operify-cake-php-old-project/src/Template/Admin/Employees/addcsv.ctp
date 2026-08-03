<!-- Content Wrapper. Contains page content -->

  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
	    <section class="content-header">
		      <h1>
			Employee Manager
		      </h1>
		           <ol class="breadcrumb">
		<li><a href="<?php echo ADMIN_URL;?>dashboards"><i class="fa fa-home"></i>Home</a></li>
<li><a href="<?php echo ADMIN_URL;?>employees/index">Manage Employee</a></li>
<li class="active">Upload CSV </li>
	      </ol>
	    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
       
        <!-- right column -->
        <div class="col-md-12">
          <!-- Horizontal Form -->
          <div class="box box-info">
		    <div class="box-header with-border">
		      <h3 class="box-title">Upload Excel</h3>
		        <a href="<?php echo ADMIN_URL ;?>Employees/employee_excel" style="margin-left: 450px;">Click Here For Download Excel Format</a>
		    </div>
            <!-- /.box-header -->
            <!-- form start -->

		<?php echo $this->Flash->render(); ?>

 
          
		<?php echo $this->Form->create($classes, array('url'=>array('controller'=>'Employees','action'=>'add_excel'),
                       
                       'class'=>'form-horizontal',
			'id' => 'sevice_form',
			'type'=>'file',
                       'enctype' => 'multipart/form-data',
                       'novalidate'
                     	)); ?>
		   
		      <div class="box-body">
				  
		        <div class="form-group">
		          <label for="inputEmail3" class="col-sm-2 control-label">Upload Excel</label>

		          <div class="col-sm-10">
			<?php echo $this->Form->input('file',array('class'=>'form-control','type'=>'file', 'id'=>'title','label' =>false)); ?>
		           
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
				if(isset($classes['id'])){
				echo $this->Form->submit(
				    'Update', 
				    array('class' => 'btn btn-info pull-right', 'title' => 'Upload','style'=>'margin-top: -33px;')
				); }else{ 
				echo $this->Form->submit(
				    'Upload', 
				    array('class' => 'btn btn-info pull-right', 'title' => 'Upload','style'=>'margin-top: -33px;')
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




