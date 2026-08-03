 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        City Manager
       
      </h1>
                    <ol class="breadcrumb">
		<li><a href="<?php echo ADMIN_URL; ?>dashboards "><i class="fa fa-home"></i>Home</a></li>
<li><a href="#">City</a></li>
<li><a href="#" class="active">Manage City</a></li>

	      </ol>
    </section>

    <!-- Main content -->

    <!-- Main content -->
    <section class="content">
		    <div class="row">
        <div class="col-xs-12">
          
	<div class="box">
            <div class="box-header">
         <!--     <i class="fa fa-search" aria-hidden="true"></i> --> 
         <?php echo $this->Flash->render(); ?>
<h3 class="box-title">Add City</h3>
            </div>
  
            <!-- /.box-header -->

            <div class="box-body">
		<!--		<div class="clearfix">

<button class="btn btn-success pull-right m-top10"><i class="fa fa-plus" aria-hidden="true"></i>
Add</button>

</div> -->

<div class="manag-stu">
  <div class="form-group">
  		<?php echo $this->Form->create($city,array('url'=>array('controller'=>'city','action'=>'add'),
                       
                       'class'=>'form-horizontal',
			'id' => 'sevice_form',
                       'enctype' => 'multipart/form-data',
                       'validate'
                     	)); ?>
		   
		      <div class="box-body">
				  	   <div class="form-group">
		          <label for="inputEmail3" class="col-sm-2 control-label">Select Country</label>

		          <div class="col-sm-10">
			<?php echo $this->Form->input('c_id',array('class'=>'form-control','required', 'empty'=>'Select Country','type' => 'select','options'=>$Country,'id'=>'title','label' =>false)); ?>
			<?php //echo $this->Ajax->observeField('title',array('url' => array('controller' =>'city','action'=>'getstate'),'update' => 'suncrts2')); ?>
		           
		          </div>
		        </div>
				     <div class="form-group">
		          <label for="inputEmail3" class="col-sm-2 control-label">Select State</label>

		          <div class="col-sm-10">
			<?php echo $this->Form->input('s_id',array('class'=>'form-control','required', 'empty'=>'Select State','type' => 'select','options'=>$State_data,'id'=>'title','label' =>false)); ?>
		           
		          </div>
		        </div>
		        <div class="form-group">
		          <label for="inputEmail3" class="col-sm-2 control-label">City</label>

		          <div class="col-sm-10">
			<?php echo $this->Form->input('name',array('class'=>'form-control','placeholder'=>'City', 'id'=>'title','required','label' =>false)); ?>
		           
		          </div>
		        </div>
  
  <div class="form-group">
    <div class="col-sm-12" style="margin-left: 236px;">
         <button type="submit" class="btn btn-info pull-left">Add</button>
       <button type="reset" class="btn btn-primary" id="res"   style="margin-left: 13px;">Reset</button>
    </div>
  </div>
     <?php
echo $this->Form->end();
?>   
  
</div>
				
				</div>
				
					</div>	</div>	</div>
		
          
	<div class="box">
            <div class="box-header">
              <h3 class="box-title">City List</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>#</th>
                   <th>Country</th>
                    <th>State</th>
                  <th>City</th>
                 
                
                  <th>Status</th>
		  <th>Action</th>	
                </tr>
                </thead>
                <tbody>
		<?php $page = $this->request->params['paging']['Services']['page'];
		$limit = $this->request->params['paging']['Services']['perPage'];
		$counter = ($page * $limit) - $limit + 1;
		//pr($classes); 
		if(isset($classes) && !empty($classes)){ 
		foreach($classes as $service){
		?>
                <tr>
                  <td><?php echo $counter;?></td>
                   <td><?php if(isset($service['country']['name'])){ echo ucfirst($service['country']['name']);}else{ echo 'N/A'; } ?></td>
                   <td><?php if(isset($service['state']['name'])){ echo ucfirst($service['state']['name']);}else{ echo 'N/A'; } ?></td>
                    <td><?php if(isset($service['name'])){ echo ucfirst($service['name']);}else{ echo 'N/A'; } ?></td>
                  
                
           
                
                  <td><?php if($service['status']=='Y'){ 
			echo $this->Html->link('Activate', [
			    'action' => 'status',
			    $service->id,
			     $service['status']	
			],['class'=>'label label-success']);
			
			 }else{ 
				echo $this->Html->link('Deactivate', [
			    'action' => 'status',
			    $service->id,
			     $service['status']
			],['class'=>'label label-primary']);
				
			 } ?>
		</td>
		<td><?php
			echo $this->Html->link('Edit', [
			    'action' => 'add',
			    $service->id
			],['class'=>'btn btn-primary']); ?>
			<?php
			echo $this->Html->link('View', [
			    'action' => 'view',
			    $service->id
			],['class'=>'btn btn-success']); ?>
			<?php
			echo $this->Html->link('Delete', [
			    'action' => 'delete',
			    $service->id
			],['class'=> 'btn btn-danger',"onClick"=>"javascript: return confirm('Are you sure do you want to delete this')"]); ?></td>
                </tr>
		<?php $counter++;} }else{?>
		<tr>
		<td>NO Data Available</td>
		</tr>
		<?php } ?>	
                </tbody>
               
              </table>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>

<script>
$(document).ready(function(){
$('.sort').on('change',function(){
var sort = $(this).val();
var id = $(this).attr('data-val');
 $.ajax({ 
        type: 'POST', 
        url: '<?php echo $this->Url->build('/admin/services/sort'); ?>',
        data: {'id':id,'sort':sort}, 
        success: function(data){  
           $('#sort').val(data);
        }, 
        
    });  
});
});

$(document).ready(function(){
$('#res').on('click',function(){
///alert("hi");
 $('#title').reset();
 
});
});

</script>
  <!-- /.content-wrapper -->
