
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        City Manager
       
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
		      <h3 class="box-title">View City</h3>
		    </div>
            <!-- /.box-header -->
            <!-- form start -->
                   <div class="box-body">
					   	        <div class="form-group">
		          <label for="inputEmail3" class="col-sm-4 control-label">Country</label>
				  <td><?php  if(isset($classes['country']['name'])){ echo ucfirst($classes['country']['name']);}else{ echo 'N/A'; } ?></td>
               
			</div>
					        <div class="form-group">
		          <label for="inputEmail3" class="col-sm-4 control-label">State</label>
				  <td><?php  if(isset($classes['state']['name'])){ echo ucfirst($classes['state']['name']);}else{ echo 'N/A'; } ?></td>
               
			</div>
		        <div class="form-group">
		          <label for="inputEmail3" class="col-sm-4 control-label">City</label>
				  <td><?php if(isset($classes['name'])){ echo ucfirst($classes['name']);}else{ echo 'N/A'; } ?></td>
               
			</div>
  
			
		
		        
		      </div>
		      <!-- /.box-body -->
		      <div class="box-footer">
			<?php
			echo $this->Html->link('Back', [
			    'action' => 'index'
			   
			],['class'=>'btn btn-default']); ?>
		      
			
		      </div>
		      <!-- /.box-footer -->
		  
          </div>
         
        </div>
        <!--/.col (right) -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>




