<div class="table-responsive">
<?php setlocale(LC_MONETARY, 'en_IN'); ?>
<table width="100%" class="table table-bordered table-striped">
<tr>
<td colspan="4"><a id=""  class="btn btn-info btn-sm pull-right" target="_blank" href="<?php echo ADMIN_URL ;?>report/monthlysum_pdf"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Export PDF</a></td>
</tr>

  <tr>
    <th>Fees collection from <?php echo date('d-m-Y',strtotime($datefrom));?> To <?php  echo date('d-m-Y',strtotime($to)); ?></th>
    <th>CBSE</th>
    <th>INT</th>
    <th>Total</th>
  </tr>
  <?php foreach($total as $key => $value){?>
  <tr>
    <td><?php echo date('M-Y',strtotime($key)); ?></td>
    <td><?php echo $value['cbse'];  $cbse_total+=$value['cbse'];?></td>
    <td><?php echo $value['int']; $int_total+=$value['int'];?></td>
    <td><?php $amt=money_format('%!i', $value['cbse']+$value['int']); echo $amt; $net_total+=($value['cbse']+$value['int']); ?></td>
  </tr>
  <?php } ?>
  <tr>
  <th style="color:green">Net Total</th>
  <th><?php echo money_format('%!i', $cbse_total)?></th>
  <th><?php echo money_format('%!i', $int_total)?></th>
  <th style="color:green"><?php echo money_format('%!i', $net_total); ?></th>
  </tr>
</table>
</div>
