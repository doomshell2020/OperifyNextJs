   <?php
    if (empty($stockregister)) { ?>


     <style>
       .modal-dialog {}
     </style>

     <script>
       $(document).ready(function() {
         $(".globalModals").click(function(event) {
           // alert($(this).attr("href"));
           $('.modal-content').load($(this).attr("href")); //load content from href of link
         });
       });
     </script>



     <div class="content-wrapper">
       <section class="content-header">
         <h1>
           Received Item Name-<?php $getsize = $this->Comman->getsizename($additem['size_id']);
                              echo "<b>" . $additem['item_name'] . "</b>"; ?> </h1>
         <ol class="breadcrumb">
           <li><a href="<?php echo SITE_URL; ?>admin/dashboards"><i class="fa fa-home"></i>Home</a></li>

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
                 <table id="example14" class="table table-bordered table-striped">
                   <thead>
                     <tr>
                       <th>S.No.</th>
                       <th>JC No</th>
                       <th>Vendor Detail</th>
                       <th>Received Qty</th>
                       <th>JC Date</th>
                     </tr>
                   </thead>

                   <tbody>
                     <?php $page = $this->request->params['paging']['']['page'];
                      $limit = $this->request->params['paging']['']['perPage'];
                      $counter = ($page * $limit) - $limit + 1;
                      if (isset($jcstockregister) && !empty($jcstockregister)) {
                        foreach ($jcstockregister as $intusr) {

                          // $indent = $this->Comman->indentdetails($intusr['indent_id'], $intusr['item_id']);


                          $subContractorsDetail = $this->Comman->subContractorsDetails($intusr['sub_contractors_id']);

                          // pr($subContractorsDetail);exit;
                          // $indent_id  =    (!empty($indent['indent_id'])) ? $indent['indent_id'] : $intusr['indent_id'];



                          $issue_date =  (!empty($intusr['issue_date']) ? $intusr['issue_date']->format('d-m-Y') : '');

                      ?>
                         <tr>
                           <td><?php echo $counter; ?></td>
                           <td><?php echo $counter; ?></td>
                           <td><?php echo $subContractorsDetail['name'] . "<br><b>Contact No. </b>" . $subContractorsDetail['mobile'] . "<br><b>Email </b>" . $subContractorsDetail['email']; ?></td>
                           <td><?php echo $intusr['quantity'];  ?></td>
                           <td><?php echo $issue_date;  ?></td>








                         </tr>
                       <?php $counter++;
                        }
                      } else { ?>


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

   <?php  } else {  ?>
     <style>
       .modal-dialog {}
     </style>

     <script>
       $(document).ready(function() {
         $(".globalModals").click(function(event) {
           // alert($(this).attr("href"));
           $('.modal-content').load($(this).attr("href")); //load content from href of link
         });
       });
     </script>



     <div class="content-wrapper">
       <section class="content-header">
         <h1>
           Received Item Name-<?php $getsize = $this->Comman->getsizename($additem['size_id']);
                              echo "<b>" . $additem['item_name'] . "</b>"; ?> </h1>
         <ol class="breadcrumb">
           <li><a href="<?php echo SITE_URL; ?>admin/dashboards"><i class="fa fa-home"></i>Home</a></li>

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
                 <table id="example14" class="table table-bordered table-striped">
                   <thead>
                     <tr>
                       <th>S.No.</th>
                       <th>PO ID / Genrated Date</th>
                       <th>Vendor Detail</th>
                       <th>Inward Date</th>
                       <th>Bill No. / Bill Date</th>
                     </tr>
                   </thead>

                   <tbody>
                     <?php $page = $this->request->params['paging']['']['page'];
                      $limit = $this->request->params['paging']['']['perPage'];
                      $counter = ($page * $limit) - $limit + 1;
                      if (isset($stockregister) && !empty($stockregister)) {
                        foreach ($stockregister as $intusr) {
                      ?>
                         <tr>
                           <td><?php echo $counter; ?></td>
                           <td> <?php echo $intusr['po_id']; ?> / <?php echo date('d-m-Y', strtotime($intusr['purchaseorder']['added_time']));
                                                                  if ($intusr['purchaseorder']['is_revised'] != 0) {
                                                                    echo " <small style='color:red;'>Revised-" . $intusr['purchaseorder']['is_revised'] . "</small>";
                                                                  } ?> <a style="font-size: 20px;" target="_blank" href="<?php echo ADMIN_URL; ?>purchaseorder/view/<?php echo $intusr['po_id'] . "/" . $intusr['purchaseorder']['is_revised'] . "/" . $intusr['purchaseorder_id']; ?>"><i class="fa fa-file-pdf-o" style="
    font-size: 20px;
"></i>&nbsp;</a></td>
                           <td> <?php $vendors = $this->Comman->findvendornames($intusr['goodsreceived']['vendor_id']);
                                echo $vendors['name'] . "<br><b>Contact No. </b>" . $vendors['contact_no'] . "<br><b>Email </b>" . $vendors['email']; ?></td>
                           <td> <?php echo date('d-m-Y', strtotime($intusr['goodsreceived']['inwarddate'])); ?></td>
                           <td> <?php echo $intusr['goodsreceived']['bill_no'] . ' /' . date('d-m-Y', strtotime($intusr['goodsreceived']['inwarddate'])); ?></td>




                         </tr>
                       <?php $counter++;
                        }
                      } else { ?>


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

   <?php }  ?>