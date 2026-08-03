
 <thead>
    <tr>
      <th>#</th>
      <th>&nbsp;</th>
      <th>Student ER No.</th>
    
      <th>Name</th>
      <th>Mobile</th>
      <th>Academic Year</th>
      <th>Class</th>
      <th>Section</th>
          <th>Form No.</th>
      <th>Profile Status</th>
      <th>Action</th>
    </tr>
 </thead>
                <tbody>
		<?php $page = $this->request->params['paging']['Students']['page'];
		$limit = $this->request->params['paging']['Students']['perPage'];
		$counter = ($page * $limit) - $limit + 1; 
		if(isset($students) && !empty($students)){ 
		foreach($students as $work){
		?>
                <tr>
               <td><?php echo $counter;?></td>
      <td><img src="<?php  echo $this->request->webroot;?>img/studentlist_img.png"</td>
      <td><?php echo $work['id']; ?></td>
 
      <td><a href="<?php echo SITE_URL; ?>admin/students/view/<?php echo $work['id']; ?>"><?php echo $work['fname']; ?></a></td>
       <td><?php echo $work['mobile']; ?></td>
      <td><?php echo $work['acedmicyear']; ?></td>
      <td><?php echo $work['class']['title']; ?></td>
       <td><?php echo $work['section']['title']; ?></td>
            <td><?php echo $work['formno']; ?></td>
                  <td><?php if($work['status']==1){ 
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
                   <td><?php /*
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
			],['class'=> 'btn btn-danger',"onClick"=>"javascript: return confirm('Are you sure do you want to delete this')"]);  */ ?>
			<a href="<?php  echo SITE_URL;?>admin/students/delete/<?php echo $work->id; ?>" class="" onclick="javascript: return confirm('Are you sure do you want to delete this')"><span class="fa fa-trash"></span></a>
		  </td>
		
                </tr>
		<?php $counter++;} }else{ ?>
		<tr>
		<td colspan="11" style="text-align:center;">NO Data Available</td>
		</tr>
		<?php } ?>	
                </tbody>
