 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
   
    <section class="content-header">
      <h1>
          Country Manager
       
       
      </h1>
     <ol class="breadcrumb">
		<li><a href="<?php echo ADMIN_URL;?>dashboards"><i class="fa fa-home"></i>Home</a></li>
<li><a href="<?php echo ADMIN_URL;?>country/index">Manage Country</a></li>
	      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
	<div class="row">
        <div class="col-xs-12">
          
	<div class="box">
            <div class="box-header">
					<?php echo $this->Flash->render(); ?>

          
<h3 class="box-title">Add Country</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
	

<div class="manag-stu">

	 <?php echo $this->Form->create('Country',array('url'=>array('controller'=>'Country','action'=>'add'),'type'=>'file','class'=>'form-horizontal')); ?>


  
  <div class="form-group">
    
       
 <div class="col-sm-12">
 <label>Title<span>*</span></label>
     <?php echo $this->Form->input('name',array('class'=>'form-control','placeholder'=>'Country Name', 'id'=>'title','label' =>false,'required')); ?>
    </div>
    
    

  
   
    
  </div>
  
  <div class="form-group">
    <div class="col-sm-12">

		      
			<?php
				if(isset($department['id'])){
				echo $this->Form->submit(
				    'Update', 
				    array('class' => 'btn btn-info pull-right', 'title' => 'Update')
				); }else{ 
				echo $this->Form->submit(
				    'Add', 
				    array('class' => 'btn btn-info pull-right', 'title' => 'Add')
				);
				}
		       ?>    </div>
  </div>
     <?php
echo $this->Form->end();
?>   
  
</div>
				
				</div>
				
					</div>	</div>	</div>
      <div class="row">
        <div class="col-xs-12">
          
	<div class="box">
            <div class="box-header">
              <h3 class="box-title">Country List</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>#</th>
                  <th>Title</th>
                   <th>Status</th>
     

                  <th>Action</th>	
                </tr>
                </thead>
                <tbody>
		<?php $page = $this->request->params['paging']['classes']['page'];
		$limit = $this->request->params['paging']['classes']['perPage'];
		$counter = ($page * $limit) - $limit + 1;
		if(isset($classes) && !empty($classes)){ 
		foreach($classes as $work){
//pr($work);
		?>
                <tr>
                  <td><?php echo $counter;?></td>
				                   

                  <td><?php if(isset($work['name'])){ echo ucfirst($work['name']);}else{ echo 'N/A';}?></td>
				       <td><?php if($work['status']=='Y'){ 
			echo $this->Html->link('Activate', [
			    'action' => 'status',
			    $work->id,
			     $work['status']	
			],['class'=>'label label-success']);
			
			 }else{ 
				echo $this->Html->link('Deactivate', [
			    'action' => 'status',
			    $work->id,
			     $work['status']
			],['class'=>'label label-primary']);
				
			 } ?>
		</td>
           
                   <td><?php
			echo $this->Html->link('Edit', [
			    'action' => 'add',
			    $work->id
			],['class'=>'btn btn-primary']); ?>
			<?php
			echo $this->Html->link('View', [
			    'action' => 'view',
			    $work->id
			],['class'=>'btn btn-success']); ?>
			<?php
			echo $this->Html->link('Delete', [
			    'action' => 'delete',
			    $work->id
			],['class'=> 'btn btn-danger',"onClick"=>"javascript: return confirm('Are you sure do you want to delete this')"]); ?>
		  </td>
		
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


  <!-- /.content-wrapper -->
