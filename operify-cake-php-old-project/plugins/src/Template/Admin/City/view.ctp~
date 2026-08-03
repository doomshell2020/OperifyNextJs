
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Class Manager
       
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
		      <h3 class="box-title">View Class</h3>
		    </div>
            <!-- /.box-header -->
            <!-- form start -->
                   <div class="box-body">
		        <div class="form-group">
		          <label for="inputEmail3" class="col-sm-4 control-label">Title</label>
				<?php if(isset($classes['title'])){ echo ucfirst($classes['title']); }else{ echo 'N/A';} ?>
			</div>
   <div class="form-group">
		          <label for="inputEmail3" class="col-sm-4 control-label">Type</label>
				<?php if(isset($classes['type'])){ if($classes['type']=='0'){ echo "Pre";}else if($classes['type']=='1'){ echo "Art"; }else if($classes['type']=='2'){ echo "Commerce";}else{ echo "Science";} }else{ echo 'N/A';}?>
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




