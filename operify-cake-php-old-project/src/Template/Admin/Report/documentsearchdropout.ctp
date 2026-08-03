 <?php
            $page = $this->request->params['paging']['DropOutStudent']['page'];
            $limit = $this->request->params['paging']['DropOutStudent']['perPage'];
            $counter = ($page * $limit) - $limit + 1; 

            if(isset($students) && !empty($students)){ 
              foreach($students as $work){//pr($work);
               
            ?>
            
            <tr>
             <td><?php echo $counter;?></td>
		<td><?php echo $work['student']['enroll']; ?></td>
             <td><?php echo $work['student']['fname'].' '.$work['student']['middlename'].' '.$work['student']['lname']; ?></td>
                   <td><?php echo $work['student']['fathername']; ?></td>
                          <td><?php  $classname=$this->Comman->findclass123($work['student']['class_id']); echo $classname['title'];?></td>
             <td><?php  $sectionname=$this->Comman->findsection123($work['student']['section_id']); echo $sectionname['title'];?></td>
              <td><?php echo $work['documentcategory']['categoryname']; ?></td>
               <td><?php echo $work['description']; ?></td>
                <td><?php echo date('d-m-Y',strtotime($work['created'])); ?></td>
                <td><?php if(isset($value['photo'])) { ?><a download="Document.<?php echo $work['ext']; ?>" href="<?php echo SITE_URL ; ?>webroot/img/<?php echo $work['photo']; ?>" class="btn btn-default btn-sm" target="_blank"><i class="fa fa-download" aria-hidden="true"></i></a> <?php } else { echo "N\A"; } ?><br>

<td>
            
                   
             
             
     		</tr>
     <?php $counter++;} }else{ ?>
     <tr>
      <td colspan="9" style="text-align:center;"><b>NO Documents Deposited Yet..</b></td>
    </tr>
    
    <?php } ?>	
