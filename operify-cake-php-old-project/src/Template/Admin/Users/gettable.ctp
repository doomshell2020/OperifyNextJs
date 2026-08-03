
<?php if(isset($examname) && !empty($examname)){ ?>

    <?php if($examname == "1"){  ?>
        <?php for($i=0; $i<$arra; $i++){ ?>
            <table width="100%">
  <tbody><tr style="font-size:15px;">
<td width="24%" style="border-top:1px solid #000; border-left:1px solid #000;  height:38px; line-height:38px;  border-right:1px solid #000;  font-weight:bold; " align="center">&nbsp; Group Name</td>
<td width="24%" style="border-top:1px solid #000; border-left:1px solid #000;  height:38px; line-height:38px;  border-right:1px solid #000;  font-weight:bold; " align="center">&nbsp; Term II</td>
</tr>

 <?php   //foreach($grname as $key=>$item){ //pr($item);die;?>

<input type="hidden" name="id[]" value="<?php //echo $item['id']; ?>">
<?php   foreach($group_name as $key=>$group){ //pr($group);die;?>
<tr style="font-size:16px;"> 
<td width="24%" style="border-top:1px solid #000;border-bottom:1px solid #000; border-left:1px solid 
#000; height:15px; line-height:15px; border-right:1px solid #000; " align="center">&nbsp;<input type="hidden" name="group_id[]" value="<?php echo $group['id']; ?>"> <?php echo $group['name']; ?></td>
<?php $gname=$this->Comman->findtemplate($group['id'],2); //pr( $classnme);die; ?>
<td width="24%" style="border-top:1px solid #000;border-bottom:1px solid #000; border-left:1px solid 
#000; height:15px; line-height:15px; border-right:1px solid #000; " align="center">&nbsp;<?php echo $this->Form->input('template', array('class' =>'longinput form-control', 'label' => false, 'type' => 'select', 'options' => $gname, 'empty' => 'Select Template')); ?></td>
    <?php } //}?></table><br>
    
    <?php } ?>
    
 <?php }else if($examname == "2"){?>
    <?php for($i=0; $i<$arra; $i++){ ?>
        <table width="100%">
  <tbody><tr style="font-size:15px;">
<td width="24%" style="border-top:1px solid #000; border-left:1px solid #000;  height:38px; line-height:38px;  border-right:1px solid #000;  font-weight:bold; " align="center">&nbsp; Group Name</td>
<td width="24%" style="border-top:1px solid #000; border-left:1px solid #000;  height:38px; line-height:38px;  border-right:1px solid #000;  font-weight:bold; " align="center">&nbsp; Term I</td>
<td width="24%" style="border-top:1px solid #000; border-left:1px solid #000;  height:38px; line-height:38px;  border-right:1px solid #000;  font-weight:bold; " align="center">&nbsp; Term II</td>
</tr>

<?php //foreach($grname as $key=>$item){ //pr($item);die;?>
    <?php   foreach($group_name as $key=>$group){ //pr($group);die;?>
<tr style="font-size:16px;"> 
<td width="24%" style="border-top:1px solid #000;border-bottom:1px solid #000; border-left:1px solid 
#000; height:15px; line-height:15px; border-right:1px solid #000; " align="center">&nbsp;<input type="hidden" name="group_id[]" value="<?php echo $group['id']; ?>"> <?php echo $group['name']; ?></td>
<?php $gname=$this->Comman->findtemplate($group['id'],1); //pr( $classnme);die; ?>
<td width="24%" style="border-top:1px solid #000;border-bottom:1px solid #000; border-left:1px solid 
#000; height:15px; line-height:15px; border-right:1px solid #000; " align="center">&nbsp;<?php echo $this->Form->input('template', array('class' =>'longinput form-control', 'label' => false, 'type' => 'select', 'options' => $gname, 'empty' => 'Select Template')); ?></td>

<?php $gname=$this->Comman->findtemplate($group['id'],2); //pr( $classnme);die; ?>
<td width="24%" style="border-top:1px solid #000;border-bottom:1px solid #000; border-left:1px solid 
#000; height:15px; line-height:15px; border-right:1px solid #000; " align="center">&nbsp;<?php echo $this->Form->input('template', array('class' =>'longinput form-control', 'label' => false, 'type' => 'select', 'options' => $gname, 'empty' => 'Select Template')); ?></td>
</td>
</tr>


    <?php }?></table><br>
    
    <?php }} //pr($st_attendence);die;?>

<?php  }else{ ?>
             <tr>
              <td>NO Data Available</td>
            </tr>
            <?php } ?>	


</tbody>
</table>
<br>