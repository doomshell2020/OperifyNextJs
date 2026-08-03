   <tr>
   <td><a id="" style="position: absolute;
top: -83px;
/* right: 0px; */
right: 46px;" class="btn btn-info btn-sm pull-right" target="_blank" href="<?php echo ADMIN_URL ;?>report/detainedreports"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Export Excel</a></td>
   </tr>
<?php
 
    
            $page = $this->request->params['paging']['Studentshistory']['page'];
            $limit = $this->request->params['paging']['Studentshistory']['perPage'];
            $counter = ($page * $limit) - $limit + 1; 

            if(isset($students) && !empty($students)){ 
              foreach($students as $work){
                // pr($work);die;
            ?>
            
            <tr>
             <td><?php echo $counter;?></td>
             <td><?php echo $work['enroll']; ?></td>

            
             <td >
              <?php 
                $name = $work['fname'].' ';

                if( !empty( $work['middlename'] ) )
                  $name .= $work['middlename'].' ';

                echo $name .= $work['lname'];
              ?> 
             </td>
                    <td ><?php echo $work['fathername']; ?></td>
                    <td><?php echo $work['mothername']; ?></td>
                  
                   
          
            
             <td style="font-size: 11px;"><?php echo $work['class']['title']; ?></td>
             <td style="font-size: 11px;"><?php echo $work['section']['title']; ?></td>
   
            <td><?php echo $work['acedmicyear']; ?></td>
                    <td><?php echo $work['admissionyear']; ?></td>
              <td><?php echo $work['sms_mobile']; ?></td>
     		</tr>
     <?php $counter++;} }else{ ?>
     <tr>
      <td>NO Data Available</td>
    </tr>
    
    <?php } ?>	
