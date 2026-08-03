     <tr>
   <td><a id="" style="position: absolute;
top: -163px;
/* right: 0px; */
right: 46px;" class="btn btn-info btn-sm pull-right" target="_blank" href="<?php echo ADMIN_URL ;?>report/smsstaffdeliverdreports"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Export Excel</a></td>
   </tr>
<?php
  $page = $this->request->params['paging']['DropOutStudent']['page'];
  $limit = $this->request->params['paging']['DropOutStudent']['perPage'];
  $counter = ($page * $limit) - $limit + 1; 
 $session = $this->request->session();
       
      
       $session->delete($employees); 
       $session->write('employeesty',$employees);
  if(isset($employees) && !empty($employees)){ 
    foreach($employees as $work){
  ?>
  
  
          <tr>
             <td><?php echo $counter;?></td>

             <td><?php echo $work['Employees']['id']; ?></td>
             
             <td><a href="#">
              <?php 
                $name = $work['Employees']['fname'].' ';

                if( !empty( $work['Employees']['middlename'] ) )
                  $name .= $work['Employees']['middlename'].' ';

                echo $name .= $work['Employees']['lname'];
              ?></a>
            </td>
                    <td><?php echo $work['Employees']['f_h_name']; ?></td>
             <td><?php if($work['smsmobile']){ echo $work['smsmobile'];   }else{ echo $work['Employees']['mobile']; } ?></td>
       
            
            
             <td><b><?php echo $work['Smsmanager']['category']; ?></b></td>
             <td><?php echo date('d-m-Y H:i:s A',strtotime($work['created'])); ?></td>
             
             
     		</tr>
<?php $counter++;} }else{ ?>
<tr>
<td style="text-align:center;" colspan="9">NO SMS Delivered Yet in Template Category !!!</td>
</tr>

<?php } ?>
