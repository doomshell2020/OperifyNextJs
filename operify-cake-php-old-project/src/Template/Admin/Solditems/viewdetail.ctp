<?php //pr($_SESSION); ?>
Requester: <?php echo $_SESSION['Auth']['User']['db']; ?></br></br>
Requisition No: <?php echo $id; ?>
<table class="table">
  <thead class="thead-dark">
    <tr>
      <th scope="col">S.No</th>
      <th scope="col">Item (Unit)</th>

      <th scope="col">Item Amount</th>
      <th scope="col">Qty</th>
      <th scope="col">Total Amount</th>
    </tr>
  </thead>
  <tbody>

  <?php $i = 1 ; foreach($requestdetails as $key=>$value){ //pr($value); ?>
    <tr>
      <th scope="row"><?php echo $i; ?></th>
      <td><?php echo $value['additem']['item_name']; ?></td>

      <td align="right"><i class="fas fa-rupee-sign"></i><?php echo $value['item_amount']; ?></td>
      <td align="right"><?php echo $value['item_qty']; ?></td>
      <td align="right"><i class="fas fa-rupee-sign"></i> <?php echo $value['item_qty']*$value['item_amount']; ?>
     
     <?php  $totalamount +=  $value['item_qty']*$value['item_amount']; ?>
    </td>
    </tr>
    <?php $i++;  } ?>
    <tr>
    <td></td>
    <td><?php echo "Total Amount"; ?></td>
    <td></td>
    <td></td>
    <td colspan = "4"><i class="fas fa-rupee-sign"></i> <?php echo $totalamount; ?></td>
  </tr>
  </tbody>
</table>

