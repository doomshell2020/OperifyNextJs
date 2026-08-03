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
       
        <!-- right column -->
        <div class="col-md-12">
          <!-- Horizontal Form -->
          <div class="box box-info">
		    <div class="box-header with-border">
		      <h3 class="box-title"><?php if(isset($classes['id'])){ echo 'Edit City'; }else{ echo 'Add City';} ?></h3>
		    </div>
            <!-- /.box-header -->
            <!-- form start -->

		<?php echo $this->Flash->render(); ?>

 
          
		<?php echo $this->Form->create($classes, array(
                       
                       'class'=>'form-horizontal',
			'id' => 'sevice_form',
                       'enctype' => 'multipart/form-data',
                       'novalidate'
                     	)); ?>
		   
		      <div class="box-body">
		        <div class="form-group">
		          <label for="inputEmail3" class="col-sm-2 control-label">Select Country</label>

		          <div class="col-sm-10">
			<?php echo $this->Form->input('c_id',array('class'=>'form-control','type'=>'select','empty'=>'Select Country','options'=>$country,'label' =>false)); ?>  
		          </div>
		        </div>
		        
		
		        <div class="form-group" >
					<div id="update">
		          <label for="inputEmail3" class="col-sm-2 control-label">Select State</label>

		          <div class="col-sm-10">
		
             
                	<?php echo $this->Form->input('s_id',array('class'=>'form-control','type'=>'select','options'=>$State,'empty'=>'Select State','label' =>false)); ?> 
           
		           
		          </div>
		          </div>
		        </div>
                  <div class="form-group">
		          <label for="inputEmail3" class="col-sm-2 control-label">City Name</label>

		          <div class="col-sm-10">
		
             
                	<?php echo $this->Form->input('name',array('class'=>'form-control','placeholder'=>'City Name', 'id'=>'title','label' =>false,'required')); ?>
           
		           
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
$(document).ready(function(){

$('#c-id').on('change',function(){
var id = $('#c-id').val();
//alert(id);
 $.ajax({ 
        type: 'POST', 
        url: '<?php echo ADMIN_URL ;?>cities/find_state',
        data: {'id':id}, 
        success: function(data){  
alert(data);
 $('#s-id').empty();
  $('#s-id').html(data);
        }, 
        
    });  
});
});

</script>
