
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Testimonial Manager
       
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
		      <h3 class="box-title">Details</h3>
		    </div>
            <!-- /.box-header -->
            <!-- form start -->
                   <div class="box-body">
		        <div class="form-group">
		          <label for="inputEmail3" class="col-sm-4 control-label">Title</label>
				<?php if(isset($testimonial['title'])){ echo ucfirst($testimonial['title']); }else{ echo 'N/A';} ?>
			</div>
			<div class="form-group">
		          <label for="inputEmail3" class="col-sm-4 control-label">Image</label>
			<?php 	 if($testimonial['image'] == ''){
			     echo 'N/A'; 
			}else{ ?>
			<img src="<?php echo  $this->request->webroot .'upload/'.$testimonial['image']; ?>" width="100" height="100" alt="" />	  <?php } ?>

			</div>
		        <div class="form-group">
		          <label for="inputPassword3" class="col-sm-4 control-label">Description</label>

		         <?php if(isset($testimonial['description'])){ echo ucfirst($testimonial['description']); }else{ echo 'N/A';} ?>
			
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




