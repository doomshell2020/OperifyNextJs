<div class="table-responsive">
<table id="example1" class="table table-bordered table-striped">
  <thead>
  <tr>
      <td><a id="" style="position: absolute;
top: -83px;
/* right: 0px; */
right: 46px;" class="btn btn-info btn-sm pull-right" target="_blank"
          href="<?php echo ADMIN_URL; ?>report/otherfeesexcel"><i class="fa fa-file-excel-o" aria-hidden="true"></i>
          Export Excel</a></td>
    </tr>
    <tr style="background-color:#39cccc !important; color:white">
      <th>#</th>
      <th>Sr.No.</th>
      <th>Pupil's Name</th>
      <th>Class</th>
   
      <th>Paydate</th>
      <th>Receipt No.</th>
      <th>Che./DD no.</th>
      <th>Ref. No.</th>
      <?php foreach($restitle as $ky=>$item){ ?>
    <th><?php echo ucwords(strtolower($item['title'])); ?></th>
      <?php } ?>
      <th>Disc.Fee</th>
      <th>Total</th>

    </tr>
  </thead>
  <tbody id="example2">
    <?php $page = $this->request->params['paging']['Services']['page'];
$limit = $this->request->params['paging']['Services']['perPage'];
$counter = ($page * $limit) - $limit + 1;
$total = "";
$discount = " ";
$amount = "";
if (isset($res) && !empty($res)) {
    foreach ($res as $service) {//pr($service);
        ?>
    <tr <?php if ($service['status'] == 'N') {?> style="color:red;" <?php }?>>
      <td><?php echo $counter; ?></td>
      <td><?php if (isset($service['s_id']) && !empty($service['s_id'])) {echo $service['s_id'];} else {echo '--'; }?></td>
      <td><?php if (isset($service['pupilname'])) {echo ucfirst($service['pupilname']);} else {echo 'N/A';}?></td>
      <td><?php if (isset($service['class_id']) && !empty($service['class_id'])) {$class = $this->Comman->findclasses($service['class_id']);
                                    echo $class[0]['title'];} else { echo 'N/A';} ?></td>
      
      <td><?php if (isset($service['paydate'])) {echo $date = date("d-m-Y", strtotime($service['paydate']));}?></td>
      </td>
      <td><?php if (isset($service['receipt_no'])) {echo $service['receipt_no'];}?></td>
      <td><?php if (isset($service['cheque_no']) && !empty($service['cheque_no'])) {echo $service['cheque_no'];} else {echo 'N/A'; }?></td>
      <td><?php if (isset($service['ref_no']) && !empty($service['cheque_no'])) {echo $service['ref_no'];} else {echo 'N/A'; }?></td>
      
      <?php foreach($restitle as $ky=>$item){ 
		   ?>
 <td><?php if($service['title']==$item['title']) { if (isset($service['amount'])) { echo $service['amount'];
            $amount += $service['amount'];} else { ?>0<?php } }else{ ?> 0<?php }?></td>
      <?php  } ?>
      
      <td><?php if (isset($service['discount'])) {echo $service['discount'];
            $discount += $service['discount'];} else {?>0<?php }?></td>
      <td><?php if (isset($service['total'])) {echo $service['total'];
            $total += $service['total'];} else {?>0<?php }?></td>

    </tr>
    <?php $counter++;}
    ?>

    <tr>
      <td colspan="8" class="text-bold text-green" style="padding-left:20px;">GRAND TOTAL</td>
            <?php foreach($restitle as $ky=>$item){ 
		   ?>  
		         <td class="text-bold text-green"></td>
		   
		   
		    <?php } ?>
      <td class="text-bold text-green"><span class="text-black">&#8377; </span><?php echo $discount; ?></td>
      <td class="text-bold text-green"><span class="text-black">&#8377; </span><?php echo $total; ?></td>
    </tr>

    <?php
} else {?>
    <tr>
      <td colspan="6">No Data Available</td>
    </tr>
    <?php }?>
  </tbody>

</table>
</div>
