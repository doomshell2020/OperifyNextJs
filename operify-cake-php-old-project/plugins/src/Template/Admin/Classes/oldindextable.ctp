 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Service Manager
       
      </h1>
      
    </section>

    <!-- Main content -->
    <section class="content">
     <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Service List</h3>

		     
            </div>
            <!-- /.box-header -->
		<?php echo $this->Flash->render(); ?>
           	 <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tr>
                  <th style="width:10%;">#</th>
                  <th style="width:20%;">Title</th>
                  <th style="width:17%;">Image</th>
		 <th style="width:14%;">Sort</th>	
                  <th style="width:16%;">Status</th>
                  <th style="width:20%;">Action</th>
                </tr>
		<?php 
		$page = $this->request->params['paging']['Services']['page'];
		$limit = $this->request->params['paging']['Services']['perPage'];
		$counter = ($page * $limit) - $limit + 1;
		if(isset($services) && !empty($services)){ 
		foreach($services as $service){
		?>
                <tr>
                  <td><?php echo $counter;?></td>
                  <td><?php if(isset($service['title'])){ echo ucfirst($service['title']);}else{ echo 'N/A';}?></td>
                  <td> <img src="<?php echo  $this->request->webroot .'upload/'.$service['image']; ?>" width="50" height="50" alt="" />
</td>
		<td>
			

<?php echo $this->Form->input('sort',array('class'=>'form-control sort','placeholder'=>'Sort','data-val'=>$service['id'], 'id'=>'sort','value'=>$service['sort'],'label' =>false)); ?>

			
</td>
                  <td><?php if($service['status']==1){ 
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
			],['class'=> 'btn btn-danger',"onClick"=>"javascript: return confirm('Are you sure do you want to delete this')"]); ?>
		  </td>
                </tr>
               <?php $counter++;} }else{?>
		<tr>
		<td>NO Data Available</td>
		</tr>
		<?php } ?>
               
               
              </table>
		<p><?= $this->Paginator->counter(['format' => 'Page {{page}} of {{pages}}, showing {{current}} records out of{{count}}'])?></p>
		<ul class = "pagination">
                 <?= $this->Paginator->prev('« Previous',array('class' => 'disabled')) ?>
                 <?= $this->Paginator->numbers() ?>
                 <?= $this->Paginator->next('Next »',array('class' => 'disabled')) ?>
               </ul>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
      </div>
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
