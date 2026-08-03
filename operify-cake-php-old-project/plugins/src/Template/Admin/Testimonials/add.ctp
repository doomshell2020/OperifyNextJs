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
		      <h3 class="box-title"><?php if(isset($testimonial['id'])){ echo 'Edit Testimonial'; }else{ echo 'Add Testimonial';} ?></h3>
		    </div>
            <!-- /.box-header -->
            <!-- form start -->
<?php //pr($page); die; ?>
		<?php echo $this->Flash->render(); ?>

 
            
		<?php echo $this->Form->create($testimonial , array(
                       
                       'class'=>'form-horizontal',
			'id' => 'page_form',
                       'enctype' => 'multipart/form-data',
                       'novalidate'
                     	)); ?>
		   
		      <div class="box-body">
		        <div class="form-group">
		          <label for="inputEmail3" class="col-sm-2 control-label">Title</label>

		          <div class="col-sm-10">
			<?php echo $this->Form->input('title',array('class'=>'form-control','placeholder'=>'Title', 'id'=>'title','label' =>false)); ?>
		           
		          </div>
		        </div>
			 
			<div class="form-group">
		          <label for="inputEmail3" class="col-sm-2 control-label">Image</label>

		          <div class="col-sm-10">
		<img src="<?php echo  $this->request->webroot .'upload/'.$testimonial['image']; ?>" width="100" height="100" alt="" />	
		<?php echo $this->Form->input('image',array('type'=>'file', 'id'=>'image','label' =>false)); ?>
		           
		          </div>
		        </div>
		        <div class="form-group">
		          <label for="inputPassword3" class="col-sm-2 control-label">Description</label>

		          <div class="col-sm-10">
			<?php echo $this->Form->textarea('description',array('rows'=>'10', 'id'=>'editor1',  'cols'=>'80','label' =>false)); ?>	
		            
		          </div>
		        </div>
			 <div class="form-group">
		        <label for="inputEmail3" class="col-sm-2 control-label">Date:</label>

		        <div class="col-sm-10 date">
		<?php echo $this->Form->input('add_date',array('type'=>'text', 'id'=>'datepicker','class'=>'form-control pull-right','label' =>false)); ?>		 
	
		          	
		        </div>
		        <!-- /.input group -->
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
				if(isset($testimonial['id'])){
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


<script>
  $(function () {
    // Replace the <textarea id="editor1"> with a CKEditor
    // instance, using default configuration.
    CKEDITOR.replace('editor1');
    //bootstrap WYSIHTML5 - text editor
    //$(".textarea").wysihtml5();
	$('#datepicker').datepicker({
	 autoclose: true
     
    });
	
  });
</script>

