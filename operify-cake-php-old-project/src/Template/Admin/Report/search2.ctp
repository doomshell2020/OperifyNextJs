   <tr>
   <td><a id="" style="position: absolute;
top: 187px;
/* right: 0px; */
right: 46px;" class="btn btn-info btn-sm pull-right" href="<?php echo ADMIN_URL ;?>report/user_supportiv2"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel</a></td>
   </tr>

		       
  	<?php
  		   $session = $this->request->session();
  	   $session->delete($f_enquiry); 
	  $session->write('f_enquiry',$f_enquiry); 
  
  	 $page = $this->request->params['paging']['Services']['page'];
		$limit = $this->request->params['paging']['Services']['perPage'];
		$counter = ($page * $limit) - $limit + 1;
		if(isset($f_enquiry) && !empty($f_enquiry)){ 
		foreach($f_enquiry as $service){
		?>
                <tr>
                  <td><?php echo $counter;?></td>
                  <td><?php if(isset($service['f_date'])){ echo  date('d-m-Y',strtotime($service['f_date']));}else{ echo 'N/A'; } ?></td>
                  <td><?php if(isset($service['l_follow_date'])){ echo date('d-m-Y',strtotime($service['l_follow_date']))."<br>".$service['f_responce'];}else{ echo 'N/A'; } ?></td>
                  <td><?php if(isset($service['add_date'])){ echo  date('d-m-Y',strtotime($service['add_date']));}else{ echo 'N/A'; } ?></td>
            
                  <td><?php if(isset($service['s_name'])){ echo ucfirst($service['s_name']);}else{ echo 'N/A'; } ?></td>
                  <td><?php if(isset($service['mobile'])){ echo ucfirst($service['mobile']);}else{ echo 'N/A'; } ?></td>
                  <td><?php if(isset($service['class_id'])){ $class_name=$this->Comman->findclass($service['class_id']);
                  echo $class_name['title'];
                  } ?></td>
                  <td><?php if($service['active']=='Y'){ ?><span class="label label-info">Opened</span> <?php }elseif($service['active']=='N') { ?> <span class="label label-danger">Closed</span><?php } else{ ?><span class="label label-success">Completed</span><?php }  ?></td>
                </tr>
		<?php $counter++;} }else{?>
		<tr>
		<td>NO Data Available</td>
		</tr>
		<?php } ?>	

               
      
               
    


