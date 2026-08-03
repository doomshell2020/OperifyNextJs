 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
         Subscribe Manager
       
      </h1>
     
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          
	<div class="box">
            <div class="box-header">
              <h3 class="box-title">Subscribe List</h3>
            </div>
            <!-- /.box-header -->
<?php echo $this->Flash->render(); ?>
            <div class="box-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th style="width:10%;">#</th>
                  <th>Email ID</th>
                  	
                </tr>
                </thead>
                <tbody>
		<?php $page = $this->request->params['paging']['Subscribes']['page'];
		$limit = $this->request->params['paging']['Subscribes']['perPage'];
		$counter = ($page * $limit) - $limit + 1;
		if(isset($subscribes) && !empty($subscribes)){ 
		foreach($subscribes as $subscribe){
		?>
                <tr>
                  <td><?php echo $counter;?></td>
                 
                  <td><?php if(isset($subscribe['email'])){ echo ucfirst($subscribe['email']);}else{ echo 'N/A';}?></td>
                 
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
