<div class="content-wrapper">
   <section class="content-header">
      <h1>
         Purchase Order Details
      </h1>
      <ol class="breadcrumb">
         <li><a href="<?php echo SITE_URL; ?>admin/dashboards"><i class="fa fa-home"></i>Home</a></li>
         <li><a href="<?php echo SITE_URL; ?>admin/purchaseorder">Purchase Order Manager</a></li>
      </ol>
   </section>
   <!-- content header -->
   <!-- Main content -->
   <section class="content">
      <div class="row">
         <div class="col-xs-12">
            <div class="box">
               <div class="box-header" style="padding:0px;">
                  <?php echo $this->Flash->render(); ?>
               </div>
               <!-- /.box-header -->
               <style>
                  fieldset {
                  display : block;
                  }
               </style>
               <div class="box-body">
                  <?php   if(count($podetail) >0){  
                     $i=1;
                     
                     foreach ($podetail as $key => $values) {
                           // pr($values);exit;
                     
                     $podetailupdated = $this->Comman->podetailupdated($values['purchaseorder_id'],$values['is_revised']);
                     $pogett = $this->Comman->pogett($values['purchaseorder_id']);
                     
                     
                     ?> 
                  <fieldset>
                  <legend style="font-weight:bold;font-size: 18px;"> PO ID -<?php echo $values['po_id'];  if($values['is_revised']!=0){ echo " <small style='color:red;'>Revised-".$values['is_revised']."</small>";  } ?><a style="font-size: 20px;"  target="_blank" href="<?php echo ADMIN_URL;?>purchaseorder/view/<?php echo $values['po_id']."/".$values['is_revised']."/".$values['purchaseorder_id']; ?>"><i class="fa fa-file-pdf-o" style="
                     font-size: 20px;
                     "></i>&nbsp;</a></legend>
                  <table id="customers" class="table table-bordered w-auto" style="margin-bottom:0px;">
                     <thead>
                        <tr class="totalColumn">
                           <th width="10%">Indent ID</th>
                           <th width="20%">Item</th>
                       
                           <th width="20%">Qty</th>
                           <th width="20%">Unit Price</th>
                           <th width="10%">Total Price</th>
                           <th width="10%">Tax Amount</th>
                           <th width="10%">Total Amount</th>
                        </tr>
                     </thead>
                     <tbody class="product_containes">
                        <?php  if(count($podetailupdated) >0){  
                           $i=1;  
                           
                           foreach ($podetailupdated as $key => $values) {
                           $tquant += $values['quantity'];
                           $tamount += $values['amount'];
                           
                           
                           $lprcost = $this->Comman->lprcost($values['item_id']);
                           if($lprcost==""){
                           $lprcost=0;   
                           }
                           
                           $unitname = $this->Comman->getunitnamepoview($values['additem']['unit_id']); ?>
                        <tr class="video_details">
                           <td><?php echo $values['indent_id']; ?></td>
                           <?php $getsize=$this->Comman->getsizename($values['additem']['size_id']);
                              if($values['additem']['size_id']==6){ ?>
                           <td><?php echo $values['additem']['item_name'];?></td>
                           <?php }else{ ?>
                           <td><?php echo $values['additem']['item_name']."-".$getsize['size_name'];?></td>
                           <?php } ?>
                          
                          
                           <td><?php echo $values['quantity']; ?></td>
                           <td><?php echo $values['rate']; ?></td>
                           <td><?php echo $values['cost_price']; ?></td>
                           <td><?php echo $values['tax']; ?>
                           </td>
                           <td><?php echo $values['amount']; ?>
                           </td>
                        </tr>
                        <?php $i++; } ?>
                        <?php  }   ?>
                     </tbody>
                     <tfoot>
                        <tr class="titlerows" style="">
                           <td colspan="6" class="text-right" style="font-weight:bold;font-size:16px;">Freight Amount (&#x20b9;)</td>
                           <td ><?php echo number_format((float)$pogett['freight'], 2, '.', '');?></td>
                        </tr>
                        <tr class="titlerow" style="">
                           <td colspan="6" class="text-right" style="font-weight:bold;font-size:16px;">Total Amount (&#x20b9;)</td>
                           <td class="totala"><?php 
                              echo  number_format((float)$pogett['freight'], 2, '.', '') + number_format((float)$pogett['total_amt'], 2, '.', ''); ?></td>
                        </tr>
                     </tfoot>
                  </table>
                  <?php } } ?>
               </div>
               <!-- /.box-body -->
            </div>
            <!-- /.box -->
         </div>
         <!-- /.col -->  
      </div>
      <!-- /.row -->      
   </section>
   <!-- /.content -->  
</div>
<!-- /.   content-wrapper -->