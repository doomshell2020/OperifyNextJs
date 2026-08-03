<?php 
// pr($books);die;
$page = $this->request->params['paging']['books']['page'];
$limit = $this->request->params['paging']['books']['perPage'];
$counter = ($page * $limit) - $limit + 1;

if(isset($books) && !empty($books)){ 
  foreach($books as $work){ //pr($work);
    //pr($work);
    $cid=$work['class_id'];
    $sid=$work['section_id'];
    $cname=$this->Comman->findclass123($cid);
     $sname=$this->Comman->findsection123($sid);
    // pr($cname);  pr($sname); die;
     $csname=$cname['title'].' - '.$sname['title'];
   // pr($sid); die;

    $d1 = date('d-m-Y',strtotime($work['issue_date']));
    $d2 = date('d-m-Y',strtotime($work['due_date']));
    // pr($d2);die;
    ?>
<tr>
  <td><?php echo $counter;?></td>

  <td><?php if(isset($work['asn_no'])){ echo $work['asn_no']; } else{ echo 'N/A'; } ?></td>

  <td><?php if(isset($work['name'])){ echo ucfirst($work['name']);}else{ echo 'N/A';}?></td>

  <td><?php if(isset($work['ISBN_NO'])){ echo ucfirst($work['ISBN_NO']);}else{ echo 'N/A';}?></td>

  <td>
    <?php if(isset($work['holder_id'])){  if($work['holder_type_id']!='Employee') { $stu=$this->Comman->findstudentname1($work['holder_id'],$work['board']); if($stu) { echo $stu['enroll'].'-'.$stu['fname'].' '.$stu['middlename'].' '.$stu['lname'];  }else{ echo ucfirst($work['holder_name']);   }  }else{  echo ucfirst($work['holder_name']);  } }else{ echo 'N/A';}?>
  </td>

  <td><?php if(isset($work['holder_type_id'])){ echo ucfirst($work['holder_type_id']);}else{ echo 'N/A';}?></td>
  <?php if($work['holder_type_id']!='Employee') { ?>
  <td><?php if(isset($csname)){ echo ucfirst($csname);}else{ echo 'N/A';}?></td>
  <?php } ?>
  <!-- <?php $lasd=$this->Comman->findlang($work['lang']); ?> -->
  <td><?php if(isset($work['lang'])){ echo ucfirst($work['lang']);}else{ echo 'N/A';}?></td>
  <td>
    <?php if($work['holder_type_id']!='Employee'){if(isset($work['sms_mobile'])){ echo $work['sms_mobile'];}else{ echo 'N/A';} }else { if(isset($work['mobile'])){ echo $work['mobile'];}else{ echo 'N/A';} }?>
  </td>
  <td><?php if(!empty($d1)) { echo $d1; } else { echo "N/A"; } ?></td>
  <td>
    <?php if(!empty($d2)) { echo $d2; } else { echo "N/A"; }?>
  </td>

  <td>
    <?php
        if(!isset($work['status'])) {
          if($work['holder_type_id']!='Employee'){  
        if( !empty( $d1 ) && !empty( $d2 ) )
        {
          if( $work['NumberOfDays'] <= 0 )
          { 
            echo '<span class="label label-success" style="font-size:12px">Left: '.abs($work['NumberOfDays']).' day(s)</span>';
          }
          else
          {
            echo '<span class="label label-danger" style="font-size:12px">Overdue: '.$work['NumberOfDays'].' day(s)</span>';
          } 
        }
        else
        {
          echo "N/A";
        } } else{
          echo "-";
        }
	} else {
		echo '<span class="label label-danger" style="font-size:12px"> Lost </span>';
	}
        ?>
  </td>

</tr>
<?php $counter++;} }else{?>
<tr>
  <td colspan="12" style="text-align:center;">NO Data Available</td>
</tr>
<?php } ?>