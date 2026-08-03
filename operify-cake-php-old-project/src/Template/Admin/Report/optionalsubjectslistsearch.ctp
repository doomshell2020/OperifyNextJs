
<div class="table-responsive">
<?php  if($opt_subject){  ?>
<a id="" style="margin-right: 20px; margin-top: 0px;" class="btn btn-info btn-sm pull-right" href="<?php echo ADMIN_URL ;?>report/
optstudent_excel/<?php echo $students[0]['class_id']; ?>/<?php echo $students[0]['section_id']; ?>/<?php echo $opt_subject; ?>"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Export Excel</a>
<?php }else{ ?>
  <a id="" style="margin-right: 20px; margin-top: 0px;" class="btn btn-info btn-sm pull-right" href="<?php echo ADMIN_URL ;?>report/
optstudent_excel/<?php echo $students[0]['class_id']; ?>/<?php echo $students[0]['section_id']; ?>"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Export Excel</a>

<?php } ?>
<table id="example1" class="table table-bordered table-striped">
  
  
  <thead>
   <tr>
     <th>#</th>

     <th>Sr. No.</th>
     <th>Name</th>
     <th>Father's Name</th>
     <th>Class</th>
     <th>Section</th>
     <th>Subject 1</th>
     <th>Subject 2</th>
     <th>Subject 3</th>
     <th>Subject 4</th>
     <th>Subject 5</th>
     <th>Opt 1</th>
     <th>Opt 2</th>
     <th>Opt 3</th>

    <!-- <th>Admission Year</th>-->
     
        
   </tr>
 </thead>
 <tbody id="example2">
   <?php $page = $this->request->params['paging']['Students']['page'];
   $limit = $this->request->params['paging']['Students']['perPage'];
   $counter = ($page * $limit) - $limit + 1; 
   if(isset($students) && !empty($students)){ 
     foreach($students as $work){
       ?>
       <tr>
        <td><?php echo $counter;?></td>

        <td><?php echo $work['enroll']; ?></td>
        
        <td><?php if($role_id=='1'){ ?>
  <a href="<?php echo SITE_URL; ?>admin/students/view/<?php echo $work['id']; ?>"><?php echo $work['fname']; ?> <?php echo $work['middlename']; ?><?php echo $work['lname']; ?></a>
  
  <?php }else{ ?> <?php echo $work['fname']; ?> <?php echo $work['middlename']; ?> <?php echo $work['lname']; ?><?php } ?></td>
        <td><?php echo $work['fathername']; ?></td>
        <td><?php $class=$this->Comman->findclass($work['class_id']); echo $class['title']; ?></td>
        <td><?php $section=$this->Comman->findsecti($work['section_id']); echo $section['title'];?>
        <td><?php if($work['comp_sid']){
            $rt=array();
           $rt=explode(',',$work['comp_sid']);
          
          $subject=$this->Comman->findsubjectsubs2($rt[0]);
          echo $subject['name']; 
        } ?></td>
            <td><?php if($work['comp_sid']){
                  $rt=array();
           $rt=explode(',',$work['comp_sid']);
            
          $subject2=$this->Comman->findsubjectsubs2($rt[1]);
          echo $subject2['name']; 
        } ?></td>
                <td><?php if($work['comp_sid']){
                      $rt=array();
           $rt=explode(',',$work['comp_sid']);
            
          $subject3=$this->Comman->findsubjectsubs2($rt[2]);
          echo $subject3['name']; 
        } ?></td>
                    <td><?php if($work['comp_sid']){
                          $rt=array();
           $rt=explode(',',$work['comp_sid']);
            
          $subject4=$this->Comman->findsubjectsubs2($rt[3]);
          echo $subject4['name']; 
        } ?></td>  <td><?php if($work['comp_sid']){
                          $rt=array();
           $rt=explode(',',$work['comp_sid']);
            
          $subject5=$this->Comman->findsubjectsubs2($rt[4]);
          echo $subject5['name']; 
        } ?></td>
                          <td><?php if($work['opt_sid']){
            $rts=array();
           $rts=explode(',',$work['opt_sid']);
          
          $subject5=$this->Comman->findsubjectsubs2($rts[0]);
          echo $subject5['name']; 
        } ?></td>
            <td><?php if($work['opt_sid']){
                  $rts=array();
           $rts=explode(',',$work['opt_sid']);
            
          $subject6=$this->Comman->findsubjectsubs2($rts[1]);
          echo $subject6['name']; 
        } ?></td>
                <td><?php if($work['opt_sid']){
                      $rts=array();
           $rts=explode(',',$work['opt_sid']);
            
          $subject7=$this->Comman->findsubjectsubs2($rts[2]);
          echo $subject7['name']; 
        } ?></td>
                   
        </td>
    

  

</tr>
<?php $counter++;} }else{ ?>
<tr>
<td>NO Data Available</td>
</tr>

<?php } ?>  



</tbody>

</table>

</div>

