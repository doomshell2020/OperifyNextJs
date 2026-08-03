 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Page Manager
       
      </h1>
            <ol class="breadcrumb">
      		<li><a href="<?php echo ADMIN_URL;?>dashboards"><i class="fa fa-home"></i>Home</a></li>
<li><a href="<?php echo ADMIN_URL;?>pages/index">Manage Page</a></li>
  </ol> 
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          
	<div class="box">
            <div class="box-header">
              <h3 class="box-title">Pages List</h3>
            </div>
            <!-- /.box-header -->
<?php echo $this->Flash->render(); ?>
            <div class="box-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>#</th>
                  <th>Title</th>
                  <th>Image</th>
                  <th>Status</th>
                  <th>Action</th>	
                </tr>
                </thead>
                <tbody>
		<?php $page = $this->request->params['paging']['Pages']['page'];
		$limit = $this->request->params['paging']['Pages']['perPage'];
		$counter = ($page * $limit) - $limit + 1;
		if(isset($pages) && !empty($pages)){ 
		foreach($pages as $page){
		?>
                <tr>
                  <td><?php echo $counter;?></td>
                  <td><?php if(isset($page['title'])){ echo ucfirst($page['title']);}else{ echo 'N/A';}?></td>
                  <td><?php if(empty($page['image'])){ 
					echo 'N/A';
			}else{ ?>
			<img src="<?php echo  $this->request->webroot .'upload/'.$page['image']; ?>" width="50" height="50" alt="" />
			<?php } ?></td>
                  <td><?php if($page['status']==1){ 
			echo $this->Html->link('Activate', [
			    'action' => 'status',
			    $page->id,
			     $page['status']	
			],['class'=>'label label-success']);
			
			 }else{ 
				echo $this->Html->link('Deactivate', [
			    'action' => 'status',
			    $page->id,
			     $page['status']
			],['class'=>'label label-primary']);
				
			 } ?>
</td>
                   <td><?php
			echo $this->Html->link('Edit', [
			    'action' => 'add',
			    $page->id
			],['class'=>'btn btn-primary']); ?>
			<?php
			echo $this->Html->link('View', [
			    'action' => 'view',
			    $page->id
			],['class'=>'btn btn-success']); ?>
			<?php
			echo $this->Html->link('Delete', [
			    'action' => 'delete',
			    $page->id
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
