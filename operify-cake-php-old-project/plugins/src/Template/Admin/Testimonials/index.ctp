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
        <div class="col-xs-12">
          
	<div class="box">
            <div class="box-header">
              <h3 class="box-title">Testimonial List</h3>
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
		<?php $page = $this->request->params['paging']['Testimonials']['page'];
		$limit = $this->request->params['paging']['Testimonials']['perPage'];
		$counter = ($page * $limit) - $limit + 1;
		if(isset($testimonials) && !empty($testimonials)){ 
		foreach($testimonials as $testimonial){
		?>
                <tr>
                  <td><?php echo $counter;?></td>
                  <td><?php if(isset($testimonial['title'])){ echo ucfirst($testimonial['title']);}else{ echo 'N/A';}?></td>
                  <td><?php if(empty($testimonial['image'])){ 
					echo 'N/A';
			}else{ ?>
			<img src="<?php echo  $this->request->webroot .'upload/'.$testimonial['image']; ?>" width="50" height="50" alt="" />
			<?php } ?></td>
                  <td><?php if($testimonial['status']==1){ 
			echo $this->Html->link('Activate', [
			    'action' => 'status',
			    $testimonial->id,
			     $testimonial['status']	
			],['class'=>'label label-success']);
			
			 }else{ 
				echo $this->Html->link('Deactivate', [
			    'action' => 'status',
			    $testimonial->id,
			     $testimonial['status']
			],['class'=>'label label-primary']);
				
			 } ?>
</td>
                   <td><?php
			echo $this->Html->link('Edit', [
			    'action' => 'add',
			    $testimonial->id
			],['class'=>'btn btn-primary']); ?>
			<?php
			echo $this->Html->link('View', [
			    'action' => 'view',
			    $testimonial->id
			],['class'=>'btn btn-success']); ?>
			<?php
			echo $this->Html->link('Delete', [
			    'action' => 'delete',
			    $testimonial->id
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
