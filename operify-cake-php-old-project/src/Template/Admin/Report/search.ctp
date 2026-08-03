   <tr>
   <td><a id="" style="position: absolute;
top: 175px;
/* right: 0px; */
right: 46px;" class="btn btn-info btn-sm pull-right" href="<?php echo ADMIN_URL ;?>report/user_supportiv"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel</a></td>
   </tr>

		       
  	<?php $page = $this->request->params['paging']['Services']['page'];
		$limit = $this->request->params['paging']['Services']['perPage'];
		$counter = ($page * $limit) - $limit + 1;
		if(isset($t_enquiry) && !empty($t_enquiry)){ 
		foreach($t_enquiry as $service){
		?>
                <tr>
                  <td><?php echo $counter;?></td>
                  <td><?php if(isset($service['created'])){ echo date('d-m-Y',strtotime($service['created']));}else{ echo 'N/A'; } ?></td>
                
                  <td><?php if(isset($service['s_name'])){ echo ucfirst($service['s_name']);}else{ echo 'N/A'; } ?></td>
                  <td><?php if(isset($service['mobile'])){ echo ucfirst($service['mobile']);}else{ echo 'N/A'; } ?></td>
                  <td><?php if(isset($service['acedmicyear'])){ echo ucfirst($service['acedmicyear']);}else{ echo 'N/A'; } ?></td>
                  <td><?php if(isset($service['classtitle'])){ echo ucfirst($service['classtitle']);}else{ echo 'N/A'; } ?></td>
                
                  <td><?php if(isset($service['next_followup_date'])){ echo ucfirst($service['next_followup_date']."<br>".$service['enquiry']);}else{ echo 'N/A'; } ?></td>
                  <td><?php if($service['status']=='Y'){ ?><span class="label label-info">Opened</span> <?php }elseif($service['status']=='N') { ?> <span class="label label-danger">Closed</span><?php } else{ ?><span class="label label-success">Completed</span><?php }  ?></td>
                </tr>
		<?php $counter++;} }else{?>
		<tr>
		<td>NO Data Available</td>
		</tr>
		<?php } ?>	

               
      
               
    


