	<?php $role_id=$this->request->session()->read('Auth.User.role_id');  ?>
		<?php  $counter=1;
		if(isset($students) && !empty($students)){ 
		foreach($students as $work){
		?>
                <tr>
               <td><?php echo $counter;?></td>
              
  <!--   <td><img src="<?php  //echo $this->request->webroot;?>img/studentlist_img.png"</td>  -->
      <td>
		  <?php if($role_id=='1'  || $role_id=='5' || $role_id=='8'){ ?>
		  
			  <?php echo ucfirst(strtolower($work['fname'])); ?> <?php echo ucfirst(strtolower($work['middlename'])); ?> <?php echo ucfirst(strtolower($work['lname'])); ?>
		  
		  <?php }else{  ?>   	  <?php echo ucfirst(strtolower($work['fname'])); ?> <?php echo ucfirst(strtolower($work['middlename'])); ?> <?php echo ucfirst(strtolower($work['lname'])); ?><?php } ?></td>
		     <td><?php echo $work['username']; ?></td>
		         <td><?php if(isset($work['id'])){  $dt=$this->Comman->findapass($work['id']); echo $dt['confirm_pass']; }else{ echo 'N/A'; } ?></td>
       <td><?php echo $work['mobile']; ?></td>
      <td><?php echo $work['f_h_name']; ?></td>
      
       <td><?php echo date('d-m-Y',strtotime($work['dob'])); ?></td>
       <td><?php echo date('d-m-Y',strtotime($work['joiningdate'])); ?></td>
     

                   <td><?php 
			echo $this->Html->link('Edit', [
			    'action' => 'add',
			    $work['id']
			],['class'=>'btn btn-primary']); ?>
			<?php /*
			echo $this->Html->link('View', [
			    'action' => 'view',
			    $work->id
			],['class'=>'btn btn-success']); ?>
			<?php 
			echo $this->Html->link('Delete', [
			    'action' => 'delete',
			    $work->id
			],['class'=> 'btn btn-danger',"onClick"=>"javascript: return confirm('Are you sure do you want to delete this')"]);  */ ?>
			<?php if($role_id=='1'){ ?><a href="<?php  echo SITE_URL;?>admin/employees/delete/<?php echo $work['id']; ?>" class="" onclick="javascript: return confirm('Are you sure do you want to delete this')"><span class="fa fa-trash"></span></a><?php } ?>
		  </td> 
		
                </tr>
		<?php $counter++;} }else{ ?>
		<tr>
		<td colspan="10" style="text-align:center;">NO Data Available</td>
		</tr>
		<?php } ?>	
