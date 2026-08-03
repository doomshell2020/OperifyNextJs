     <tr>
   <td><a id="" style="position: absolute;
top: -163px;
/* right: 0px; */
right: 46px;" class="btn btn-info btn-sm pull-right" target="_blank" href="<?php echo ADMIN_URL ;?>report/smsdeliverdreports"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Export Excel</a></td>
   </tr>
<?php
  $page = $this->request->params['paging']['DropOutStudent']['page'];
  $limit = $this->request->params['paging']['DropOutStudent']['perPage'];
  $counter = ($page * $limit) - $limit + 1; 
 $session = $this->request->session();
       
      
       $session->delete($studentsgh); 
       $session->write('studentsgh',$students);
  if(isset($students) && !empty($students)){ 
    foreach($students as $work){
  ?>
  
  
          <tr>
             <td><?php echo $counter;?></td>

             <td><?php echo $work['Students']['enroll']; ?></td>
             
             <td><a href="#">
              <?php 
                $name = $work['Students']['fname'].' ';

                if( !empty( $work['Students']['middlename'] ) )
                  $name .= $work['Students']['middlename'].' ';

                echo $name .= $work['Students']['lname'];
              ?></a>
            </td>
                    <td><?php echo $work['Students']['fathername']; ?></td>
             <td><?php if($work['smsmobile']){ echo $work['smsmobile'];   }else{ echo $work['Students']['sms_mobile']; } ?></td>
       
             <td><?php  $classname=$this->Comman->findclass123($work['Students']['class_id']); echo $classname['title'];?></td>
             <td><?php  $sectionname=$this->Comman->findsection123($work['Students']['section_id']); echo $sectionname['title'];?></td>
            
             <td><b><?php echo $work['Smsmanager']['category']; ?></b></td>
             <td><?php echo date('d-m-Y H:i:s A',strtotime($work['created'])); ?></td>
             
             
     		</tr>
<?php $counter++;} }else{ ?>
<tr>
<td style="text-align:center;" colspan="9">NO SMS Delivered Yet in Template Category !!!</td>
</tr>

<?php } ?>
