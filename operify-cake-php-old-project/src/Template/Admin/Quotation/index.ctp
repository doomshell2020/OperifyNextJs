<style>
   #load2 {
      width: 100%;
      height: 100%;
      position: fixed;
      z-index: 9999;
      background-color: white !important;
      background: url("<?php echo SITE_URL; ?>images/Preloader_2.gif") no-repeat center center rgba(0, 0, 0, 0.75)
   }
</style>

<?php
if ($ids3) {
?>

   <a href="<?php echo ADMIN_URL; ?>Quotation/view/<?php echo $ids5 . "/" . $ids4 . "/" . $ids3; ?>" target="_blank"
      id="redicaution"></a>
   <script type="text/javascript">
   </script>
<?php } ?>
<?php
$parms = $_REQUEST;
$typeselect = $parms['type'];
$purchaseorder_id = $parms['purchaseorder_id'];
$datefrom = $parms['datefrom'];
$dateto = $parms['dateto'];
$status = $parms['status'];
?>
<div class="content-wrapper">
   <section class="content-header">
      <h1>
         Quotations
      </h1>
      <ol class="breadcrumb">
         <li><a href="<?php echo SITE_URL; ?>admin/quotation"><i class="fa fa-home"></i>Home</a></li>
         <li><a href="<?php echo SITE_URL; ?>admin/quotation">Quotations</a></li>
      </ol>
   </section>
   <!-- content header -->
   <!-- Main content -->
   <section class="content">
      <div class="row">
         <div class="col-xs-12">
            <div class="box">
               <div class="box-header" style="padding-bottom:0px;margin-bottom:5px;">

                  <?php echo $this->Flash->render(); ?>

                  <div class="form-group">
                     <div class="row">

                        <div class="col-sm-12">
                           <?php
                           $role_permissions = $this->Permission->permissioncheck();
                           // pr($role_permissions);exit;
                           $fileurl = "admin/quotation/add";
                           if (in_array($fileurl, $role_permissions)) { ?>
                              <a href="<?php echo SITE_URL; ?>admin/quotation/add"
                                 class="btn btn-success pull-right m-top10"
                                 style=" margin-top: 23px; color:#fff; padding:6px 10px;"><i
                                    class="fa fa-plus"></i>&nbsp;Add</a>
                           <?php } ?>


                        </div>

                     </div>
                  </div>
               </div>


               <!-- /.box-header -->
               <div class="box-body" style="padding-top:0px;" id="example23">
                  <div id="load2" style="display:none;"></div>
                  <table class="table table-bordered table-striped" width="100%">
                     <thead>
                        <tr>
                           <th width="10%">Quotation No</th>
                           <th width="13%">Quotation Date</th>
                           <th width="23%">Received/Send Quotations</th>
                           <!-- <th width="09%">Order Qty</th>
                           <th width="09%">Total(INR)</th> -->
                           <th width="13%">Delivery Date</th>
                           <th width="13%">Action</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php
                        $page = $this->request->params['paging']['Purchaseorder']['page'];
                        $limit = $this->request->params['paging']['Purchaseorder']['perPage'];
                        $counter = ($page * $limit) - $limit + 1;

                        if (isset($quotationDeatails) && !empty($quotationDeatails)) {
                           foreach ($quotationDeatails as $value) {
                              $quotationSendCount = $this->Comman->quotationSendCount($value['quotation_id']);
                              $quotationReceivedCount = $this->Comman->quotationReceivedCount($value['quotation_id']);

                              $vendor_id = $this->Comman->findvendornames($value['vendor_id']);
                        ?>
                              <tr>
                                 <td>
                                    <a title="View Quotation" class="viewgrndetails" href="<?php echo ADMIN_URL; ?>quotation/viewquotationdetail/<?php echo $value['quotation_id']; ?>">
                                       <?php echo $value['quotation_id']; ?></a>
                                 </td>
                                 <td><?php echo date("d-m-Y", strtotime($value['added_time'])); ?></td>

                                 <td>
                                    <?php
                                    if ($quotationReceivedCount > 0) { ?>
                                       <a title="View Bidded Vendors" class="viewbidvendors" href="<?php echo ADMIN_URL; ?>quotation/viewbiddedvendors/<?php echo $value['quotation_id']; ?>">
                                          <?php echo $quotationReceivedCount . '/' . $quotationSendCount; ?></a>
                                    <?php  } else {
                                       echo $quotationReceivedCount . '/' . $quotationSendCount;
                                    }  ?>



                                    <a href="<?php echo SITE_URL; ?>admin/quotation/view_received_quotation/<?php echo $value['quotation_id']; ?>"
                                       class="btn btn-success ms-3" style="  color:#fff; padding:6px 10px;">&nbsp;View Bids</a>
                                 </td>


                                 <!-- <td style="text-align:right;"><?php //echo number_format((float) $value['total_qty'], 2, '.', ''); ?></td>
                                 <td style="text-align:right;"><?php // echo number_format((float) $value['total_amt'], 2, '.', ''); ?></td> -->
                                 <td><?php echo date("d-m-Y", strtotime($value['delivery_date'])); ?></td>

                                 <td style="text-align:center">
                                    <?php if ($value['is_award'] == "Y") {
                                       echo "Awarded";
                                    } else { ?>
                                       <a href="<?php echo SITE_URL; ?>admin/quotation/send_quotations/<?php echo $value['quotation_id'] ?>"
                                          class="btn btn-success" style="color:#fff; padding:6px 10px;">Invite Vendors</a>
                                       </a>
                                    <?php } ?>
                                 </td>
                              </tr>
                           <?php $counter++;
                           }
                        } else { ?>
                           <tr>
                              <td colspan="7" style="text-align: center;">No quotations available.</td>
                           </tr>
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



<!-- for view po details -->
<script>
   $('.viewgrndetails').click(function(e) {
      e.preventDefault();
      $('#editsortsgrn').modal('show').find('.modal-body').load($(this).attr('href'));
   });
</script>


<div class="modal fade" id="editsortsgrn">
   <div class="modal-dialog " style="max-width: 900px !important;">
      <div class="modal-content">
         <div class="modal-body"></div>
      </div>
   </div>
</div>
<script>
   $('.viewbidvendors').click(function(e) {
      e.preventDefault();
      $('#vendorsortsgrn').modal('show').find('.modal-body').load($(this).attr('href'));
   });
</script>
<div class="modal fade" id="vendorsortsgrn">
   <div class="modal-dialog " style="max-width: 900px !important;">
      <div class="modal-content">
         <div class="modal-body"></div>
      </div>
   </div>
</div>