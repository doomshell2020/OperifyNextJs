<script>
$(document).ready(function() {
    $(".globalModals").click(function(event){
// alert($(this).attr("href"));
        $('.modal-content').load($(this).attr("href"));  //load content from href of link
        });
    });  
</script>  
 
 <div class="content-wrapper">
   <section class="content-header">
    <h1>
     Goods Received Note
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo SITE_URL; ?>admin/dashboards"><i class="fa fa-home"></i>Home</a></li>
      <li><a href="<?php echo SITE_URL; ?>admin/purchaseorder">Goods Received Manager</a></li>
    </ol> 
  </section> <!-- content header -->

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-xs-12">    
        <div class="box">
          <div class="box-header">
            <?php echo $this->Flash->render(); ?>
            <a href="<?php echo SITE_URL; ?>admin/goodsreceived/add">
              <button class="btn btn-success pull-right m-top10"><i class="fa fa-plus" aria-hidden="true"></i>
              Goods Received Note</button></a>
            </div><!-- /.box-header -->
            <div class="box-body">    
              
              <table id="example1" class="table table-bordered table-striped w-auto">
                <thead>
                <tr>
      
                     <th>GRN No.</th>
                     <th>PO Id</th>
                    <th>G.R.N. Inward</th>
                       <th>Bill No.</th>              
                       <th>Bill Date</th>              
                       <th>Vendor</th>              
                       <th>Total Qty.</th>              
                   <th>Total Received Qty.</th>
                    <th>Pending Qty.</th>
                  <th>Total Amount (INR)</th>                    
                    <th>Status</th>
                    <th>Action</th>
                    
                  </tr>
                </thead>

                <tbody>
                  <?php $page = $this->request->params['paging']['']['page'];
                  $limit = $this->request->params['paging']['']['perPage'];
                  $counter = ($page * $limit) - $limit + 1;
                  if(isset($goodsreceived) && !empty($goodsreceived)){ 
                    foreach($goodsreceived as $intusr){ //pr($intusr);
          
                       $vendor_id = $this->Comman->findvendornames($intusr['vendor_id']);
                       $po = $this->Comman->getpoqty($intusr['purchaseorder_id']);
                     $remain = $this->Comman->goodsrecivied($intusr['purchaseorder_id'],$intusr['id']);
                

                      ?>
                      <tr>
                                           
                        
                            <td><?php echo $intusr['id'];?></td>   
                              <td><?php echo $intusr['purchaseorder_id'];?></td>                    
                        <td><?php echo date("d-m-Y", strtotime($intusr['inwarddate']));?></td>                         
               <td><?php echo $intusr['bill_no'];?></td>                       
               <td><?php echo date("d-m-Y", strtotime($intusr['bill_date']));?></td>                       
                        <td><?php echo $vendor_id['name']; ?></td>  
                          <td><?php echo $po['total_qty']; ?></td>                         
                        <td><?php echo $intusr['total_qty']; ?></td>  
                          <td><?php echo $remn=$po['total_qty']-$remain[0]['quantity']; ?></td>  
                                               
                        <td><?php echo $intusr['total_amt']; ?></td>                         
                                          
                                <td><?php if($remn!=0){ echo "<strong style='color:red;'>Open</strong>"; }else{ echo "<strong style='color:green;'>Close</strong>"; } ?></td>                          
                        <td><a  target="_blank" href="<?php echo ADMIN_URL;?>goodsreceived/view/<?php echo $intusr['id']; ?>"><i class="fa fa-file-pdf-o" style="
    font-size: 30px;
"></i>&nbsp;</a></td>
                        </tr>
                        <?php $counter++; } }else{ ?>


                        <?php } ?>  
      </tbody>

    </table>
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
<div class="modal fade" id="globalModalbag" style="width:51% !important;" tabindex="-1" role="dialog" aria-labelledby="esModalLabel" aria-hidden="true">
                <div class="modal-dialog" style="width:100% !important;">
                    <div class="modal-content personal">
                        <div class="modal-body">
                   <div class="col-sm-6 col-md-6 col-sm-offset-2 col-md-offset-2">
                          </div>
                            <div class="loader">
                                <div class="es-spinner">
                                    <i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i>
                                </div>
                            </div>
                        </div>
                        
                    </div>
              
                </div>
            </div>
