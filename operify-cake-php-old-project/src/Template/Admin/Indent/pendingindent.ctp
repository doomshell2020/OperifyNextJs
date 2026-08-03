<script>
$(document).ready(function() {
    $(".globalModals").click(function(event){
        $('.modal-content').load($(this).attr("href"));  //load content from href of link
        });
    });  
</script>  
 
 <div class="content-wrapper">
   <section class="content-header">
    <h1>
     Pending Purchase Requisition 
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo SITE_URL; ?>admin/dashboards"><i class="fa fa-home"></i>Home</a></li>
      <li><a href="<?php echo SITE_URL; ?>admin/pendingindent">Pending Purchase Requisition </a></li>
    </ol> 
  </section> <!-- content header -->

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-xs-12">    
        <div class="box">
          <div class="box-header">
            <?php echo $this->Flash->render(); ?>
       
            </div><!-- /.box-header -->
            <div class="box-body">    
              <table id="example1" class="table table-bordered table-striped w-auto">
                <thead>
                  <tr>
                    <th>Sr.No.</th>
                    <th>Indent Id</th>
                    <th>Requested Items</th>                                        
                    <th>Requested Quantity</th>  
                    <th>Indent Status</th>     
                    <th>Genrated Date</th>                  
                    <th>Goods Issue</th>
                  </tr>
                </thead>

                <tbody>                  
                  <?php $page = $this->request->params['paging']['']['page'];
                  $limit = $this->request->params['paging']['']['perPage'];
                  $counter = ($page * $limit) - $limit + 1;
                  if(isset($users) && !empty($users)){ 
                    $counter = 1;
                    foreach($users as $intusr){ //pr($intusr);
                      $var = $this->Comman->indentitemquantity($intusr['indent_id']);                      
                     //pr($var); die;
                      ?>
                      <tr>
                      <thead>
                        <td><?php echo $counter; ?></td>
                        <td><?php echo $intusr['indent_id'];?></td>                        
                        <td>
                          <table class="table table-bordered">
                            <thead>
                              <tr>
                                <th>Name</th>
                                
                                <th>Quantity</th>
                              </tr>
                            </thead>
                            <tbody>
                            <?php 
                              $indentdetail = $this->Comman->indentdetail($intusr['indent_id']);
                     $totl=0;
                              foreach($indentdetail as $value){    
                                $unitname = $this->Comman->getunitnamepoview($value['additem']['unit_id']);   
                                $remain=$value['quantity']-$value['return_qty'];                   if($remain>0){ 
                                  $totl +=$remain;
                              ?>
                              <tr>
                                <td><?php echo $value['additem']['item_name']; ?></td>
                              
                                <td><?php echo $value['quantity']-$value['return_qty']; ?></td>
                              </tr>
                            <?php } } ?>
                            </tbody>
                          </table>
                        </td>  
                        <td><?php echo $totl;?></td>  
                        <td><?php echo "Pending";?></td>  
                        <td><?php echo date("d-m-Y", strtotime($intusr['added_time']));?></td>                       
                        <td><a target="_blank" href="<?php echo ADMIN_URL;?>indent/view/<?php echo $intusr['indent_id']; ?>"><i class="fa fa-file-pdf-o" style="font-size: 30px;"></i>&nbsp;</a>&nbsp; <a target="_blank" href="<?php echo ADMIN_URL;?>goodsissue/add/<?php echo $intusr['indent_id']; ?>"><i class="fa fa-mail-reply-all" style="font-size: 30px;" aria-hidden="true"></i>&nbsp;</a></td>
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




