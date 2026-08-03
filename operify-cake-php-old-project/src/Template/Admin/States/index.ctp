 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        State Manager
       
      </h1>
      
          <ol class="breadcrumb">
		<li><a href="<?php echo ADMIN_URL;?>dashboards"><i class="fa fa-home"></i>Home</a></li>
<li><a href="<?php echo ADMIN_URL;?>states/index">Manage State</a></li>
	      </ol> 
      
     <?php echo $this->Flash->render(); ?>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          
	<div class="box">
            <div class="box-header">
              <h3 class="box-title">State List</h3>
               
		   
		      <div class="box-body">
				  <?php echo $this->Form->create($classes,array('url'=> array('controller'=>'states','action'=>'add'),'class'=>'form-horizontal','enctype' => 'multipart/form-data')); ?>
		      
		        <div class="form-group">
    
       
 <div class="col-sm-6">
 <label>Select Country<span style="color:red;">*</span></label>
     <?php echo $this->Form->input('c_id',array('class'=>'form-control','type'=>'select','empty'=>'Select Country','options'=>$country, 'id'=>'title','label' =>false,'required')); ?>
    </div>
    
    
 <div class="col-sm-6">
 <label>State Name<span style="color:red;">*</span></label>
   	<?php echo $this->Form->input('name',array('class'=>'form-control','placeholder'=>'State Name', 'id'=>'title','label' =>false,'required')); ?>
    </div>   
  
   
    
  </div>
		                    
		      <div class="form-group">
             <div class="col-sm-12">
      <button type="submit" class="btn btn-success">Submit</button>
       <button type="reset" class="btn btn-primary">Reset</button>
    </div>
  </div>
     <?php
echo $this->Form->end();
?>   
            </div>
            <!-- /.box-header -->

 <div class="row">
        <div class="col-xs-12">
	<div class="box">
            <div class="box-header">
              <h3 class="box-title">State List</h3>
            </div>
            <div class="box-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>#</th>
                  <th>Country Name</th>
                  <th>State Name</th>
                
                  <th>Status</th>
		              <th>Action</th>	
                </tr>
                </thead>
                <tbody>
		<?php $page = $this->request->params['paging']['Services']['page'];
		$limit = $this->request->params['paging']['Services']['perPage'];
		$counter = ($page * $limit) - $limit + 1;
		if(isset($classes) && !empty($classes)){ 
		foreach($classes as $service){
		?>
                <tr>
                  <td><?php echo $counter;?></td>
                  <td><?php if(isset($service['country']['name'])){ echo ucfirst($service['country']['name']);}else{ echo 'N/A'; } ?></td>
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

</script>
  <!-- /.content-wrapper -->
