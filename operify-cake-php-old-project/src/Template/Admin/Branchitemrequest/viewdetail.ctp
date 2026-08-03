<?php //pr($_SESSION); ?>
<style>
  .tableContainer {
    border: 1px solid #ccc;
  }
  .tableContainer p {
    margin-bottom:5px;
  }
  .tableContainer table thead {
    background:#fff;
    color:#333;
  }
  .tableContainer .tableHeader {
    padding:10px;
    border-bottom:1px solid #ccc;
  }
</style>

<div class="tableContainer">
  <div class="tableHeader">
    <p>Requester: <?php 
    $requ = explode("_",$requestdetails[0]['branchrequest']['branch_name']);
     echo ucfirst($requ[1]); 
    ?></p>
    <p>Requisition No: <?php echo $id; ?></p>
    <p>Requisition Date: <?php echo date('d-m-Y H:i: A', strtotime($branch_request['created'])); ?></p>


  </div>

  <div class="table-responsive" style="padding: 10px; height: 75vh !important; overflow: auto;">
  <table class="table table-bordered">
    <thead>
      <tr>
        <th width="100%" colspan="5" style="text-align:center" >Items Detail</th>
      </tr>
      <tr>
        <th width="5%" scope="col">S.No</th>

      <th width="25%" scope="col">Item Name</th>
      <th width="10%" scope="col">Unit Price</th>
      <th width="5%" scope="col">Quantity</th>
      <th  width="10%" scope="col">Item Amount</th>
      <th  width="10%" scope="col">Discount</th>
      <th  width="5%" scope="col">Tax</th>
      <th width="10%" >Tax Amount</th>
      <th  width="10%" scope="col">Taxable Amount</th>
      <th  width="10%" scope="col">Amount</th>

       
      </tr>
    </thead>
    <tbody>

    <?php $i = 1 ; foreach($requestdetails as $key=>$intusr){ //pr($intusr); ?>
      <tr>
        <th scope="row"><?php echo $i; ?></th>
        <td> <?php echo ucfirst(strtolower($intusr['additem']['item_name'])); ?></td>
                        <td align="right"> ₹ <?php  echo sprintf('%.2f', $intusr['item_amount']);
                        ?></td>
                        <td align="right"> <?php echo $intusr['item_qty']; ?></td>
                        <td align="right"> <?php  $totalitem_amount=$intusr['item_amount']*$intusr['item_qty'];
                           echo  sprintf('%.2f', $totalitem_amount);
                        ?></td>
                        <td align="right">  <?php
                                  if($intusr['discount']){
                                    $discount =$intusr['discount']*$intusr['item_qty'];
                                }else{
                                    $discount = 0;
                                }
                                echo  sprintf('%.2f', $discount); ?></td>
                        <td align="right"> <?php 
                                    $tax = $intusr['item_tax'];
                                    
                              
                               echo sprintf('%.2f', $tax)."%";
                                 ?>
                                 <?php 
                                 $total=$intusr['item_amount']*$intusr['item_qty']- $discount;
                                // echo $total; die;

                                 $total_tax = $total*$tax/100;
                                 //echo $total_tax; die;
                                 ?>
                        </td>

                        <td align="right">₹ <?php echo  sprintf('%.2f', $total_tax); ?></td>
                        <td align="right">₹ <?php echo  sprintf('%.2f', $total); ?></td>
                        <td align="right">₹ <?php echo  sprintf('%.2f', $total+$total_tax); ?></td>
      </tr>

      <?php  
                           $total_unit += $intusr['additem']['sale_price'];
                           $total_qty += $intusr['item_qty'];
                           $total_amount +=$total+$total_tax ; 
                           $totaltem_amount +=$totalitem_amount;
                           $totaltaxable += $total;
                           $total_taxss += $total_tax;
                           ?>
      <?php $i++;  } ?>
      <tr>
                        <td></td>
                        <td><b>Total</b></td>
                        <td align="right"><b>₹ <?php echo $total_unit; ?></b></td>
                        <td align="right"></i><b><?php echo $total_qty; ?></b></td>
                        <td align="right"></i><b><?php echo $totaltem_amount; ?></b></td>
                        <td> </td>
                        <td></td>
                        <td align="right"></i><b>₹<?php echo sprintf('%.2f',  $total_taxss); ?></b></td>
                        <td align="right"></i><b><?php echo $totaltaxable; ?></b></td>
                        <td align="right">₹ <b><?php echo sprintf('%.2f',   round($total_amount)); ?></b></td>
                     </tr>
    </tbody>
  </table>
  </div>
</div>
