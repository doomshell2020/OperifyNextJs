<style>
   #testUL {
      position: relative;
   }

   #testUL ul {
      position: absolute;
      z-index: 999;
      overflow: scroll;
      height: 100px;
      top: 100%;
      left: 0px;
      right: 0px;
      list-style-type: none;
      background-color: white;
      padding-left: 0px;
   }

   #testUL ul li {
      padding: 5px 8px;
      border: 1px solid lightgray;
   }

   #testUL ul li a {
      color: black;
   }

   .preview {
      margin-right: 15px;
   }

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
         Bids
      </h1>
      <ol class="breadcrumb">
         <li><a href="<?php echo SITE_URL; ?>admin/quotation"><i class="fa fa-home"></i>Home</a></li>
         <li><a href="<?php echo SITE_URL; ?>admin/quotation/view_received_quotation">Bids</a></li>
      </ol>
   </section>
   <!-- content header -->
   <!-- Main content -->
   <section class="content">
      <div class="row">
         <div class="col-xs-12">
            <div class="box">
               <div class="box-header" style="padding-bottom:0px;margin-bottom:5px;">

                  <?php // pr($parms);
                  echo $this->Flash->render(); ?>
                  <script>
                     function cllbckretail0(id, cid, sid) {
                        $('.secrh-retail').val(id);
                        $('#retail_ids').val(cid);
                        $('#testUL').hide();
                     }

                     $(function() {
                        $('.secrh-retail').bind('keyup', function() {
                           var pos = $(this).val();
                           var check = 0;
                           $('#testUL').show();
                           $('#retail_ids').val('');
                           var count = pos.length;
                           if (count > 0) {
                              $.ajax({
                                 type: 'POST',
                                 url: '<?php echo ADMIN_URL; ?>vendors/getname',
                                 data: {
                                    'fetch': pos,
                                    'check': check
                                 },
                                 success: function(data) {
                                    console.log(data);
                                    $('#testUL ul').html(data);
                                 },
                              });
                           } else {
                              $('#testUL').hide();
                           }
                        });
                     });
                  </script>

                  <script inline="1">
                     $(document).ready(function() {
                        $("#vendorsdetails").bind("submit", function(event) {
                           $.ajax({
                              async: true,
                              data: $("#vendorsdetails").serialize(),
                              dataType: "html",


                              beforeSend: function(xhr) {
                                 xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
                                 $('#load2').css("display", "block"); // Show loader
                              },
                              success: function(data) {
                                 $("#example23").html(data);
                              },
                              complete: function() {
                                 $('#load2').css("display", "none"); // Hide loader
                              },
                              error: function() {
                                 alert("An error occurred. Please try again.");
                                 $('#load2').css("display", "none"); // Hide loader on error
                              }

                              type: "get",
                              url: "<?php echo ADMIN_URL; ?>purchaseorder/searchitem"
                           });
                           return false;
                        });

                     });

                     $(document).on('click', '.pagination a', function(e) {
                        var target = $(this).attr('href');
                        var res = target.replace("/purchaseorder/searchitem", "/purchaseorder");
                        window.location = res;
                        return false;
                     });
                  </script>



                  <?php echo $this->Form->create('Purchaseorder', array('type' => 'file', 'inputDefaults' => array('div' => false, 'label' => false), 'id' => 'vendorsdetails', 'class' => 'form-horizontal')); ?>
                  <div class="form-group">
                     <div class="row">

                     </div>

                  </div>
               </div>
               <?php echo $this->Form->end(); ?>
            </div>
            <!-- /.box-header -->
            <div class="box-body" style="padding-top:0px;" id="example23">
               <div id="load2" style="display:none;"></div>
               <table class="table table-bordered table-striped" width="100%">
                  <thead>
                     <tr>
                        <th width="10%">Quotation No</th>
                        <th width="13%">Quotation Date</th>
                        <th width="13%">Bid No</th>
                        <th width="13%">Bid Date</th>
                        <th width="23%">Vendor</th>
                        <th width="09%">Quotation Amount</th>
                        <th width="09%">Bid Amount</th>
                        <th width="13%">Action</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php
                     $page = $this->request->params['paging']['Purchaseorder']['page'];
                     $limit = $this->request->params['paging']['Purchaseorder']['perPage'];
                     $counter = ($page * $limit) - $limit + 1;

                     if (isset($quotationVendor) && !empty($quotationVendor)) {
                        foreach ($quotationVendor as $value) { //pr($value);exit;

                           $vendor_id = $this->Comman->findvendornames($value['vendor_id']);
                           $bidDetails = $this->Comman->getBidDetails($value['vendor_id'], $value['quotation_id']);
                           $getQuotationDetails = $this->Comman->getQuotation($value['quotation_id']);
                           // $isAwarded = $this->Comman->isAwarded($value['quotation_id']);
                           // pr($getQuotationDetails['vendor_id']);exit;
                           // pr($value['quotation_id']);
                           // pr($getQuotationDetails['selected_bid_id']);



                     ?>

                           <tr>
                              <td>
                                 <a class="viewgrndetails" href="<?php echo ADMIN_URL; ?>quotation/viewquotationdetail/<?php echo $value['quotation_id']; ?>">
                                    <?php echo $value['quotation_id']; ?></a>
                              </td>

                              <td><?php echo date("d-m-Y", strtotime($getQuotationDetails['added_time'])); ?></td>

                              <td>
                                 <a class="viewgrndetails" href="<?php echo ADMIN_URL; ?>quotation/viewvendorquotation/<?php echo $bidDetails['id']; ?>">
                                    <?php echo $bidDetails['id']; ?></a>
                              </td>
                              <?php
                              if ($bidDetails) { ?>
                                 <td>
                                    <?php echo date("d-m-Y", strtotime($bidDetails['created']));  ?>
                                 </td>
                              <?php  } else { ?>
                                 <th style="color:red;">Not Bid Yet</th>
                              <?php   } ?>
                              <td><?php echo $vendor_id['name']; ?></td>
                              <td style="text-align:right;"><?php echo number_format((float) $getQuotationDetails['total_amt'], 2, '.', ''); ?></td>
                              <td style="text-align:right;"><?php echo number_format((float) $bidDetails['total_amt_bid'], 2, '.', ''); ?></td>

                              <td style="text-align:center">
                                 <?php
                                 if ($value['vendor_id'] == $getQuotationDetails['vendor_id']) {
                                    echo "Awarded";
                                 } elseif (empty($getQuotationDetails['selected_bid_id'])) {
                                    if (!empty($bidDetails['id'])) {
                                 ?>
                                       <a href="<?php echo SITE_URL; ?>admin/purchaseorder/award_quotation/<?php echo $bidDetails['id']; ?>"
                                          class="btn btn-success" style="color:#fff; padding:6px 10px;">
                                          Award
                                       </a>
                                 <?php
                                    }
                                 }
                                 ?>

                              </td>
                           </tr>
                        <?php $counter++;
                        }
                     } else { ?>
                      <tr>
                              <td colspan="8" style="text-align: center;">Bid Not Available</td>
                           </tr>
                     <?php } ?>
                  </tbody>
               </table>
               <?php // echo $this->element('admin/pagination'); 
               ?>
               <?php // echo $this->element('admin/custompagination'); 
               ?>
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
<div class="modal fade" id="myModal" style="width:51% !important;overflow-y: auto !important;" tabindex="-1"
   role="dialog" aria-labelledby="esModalLabel" aria-hidden="true">
   <div class="modal-dialog" style="width:100% !important;">
      <div class="modal-content personal">
         <div class="loader">
            <div class="es-spinner">
               <i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i>
            </div>
         </div>
      </div>
   </div>
</div>

<script>
   $(document).ready(function() {
      $(".globalModals").click(function(event) {
         alert($(this).attr("href"));
         $('.modal-content').load($(this).attr("href"));
      });
   });
</script>

<!-- for delivery note -->
<script>
   $('.delivery_note').click(function(e) {
      e.preventDefault();
      $('#cancelsorts').modal('show').find('.modal-body').load($(this).attr('href'));
   });
</script>
<div class="modal fade" id="cancelsorts">
   <div class="modal-dialog" style="max-width:999px !important;">
      <div class="modal-content">
         <div class="modal-body purc_mdl_body"></div>
      </div>
   </div>
</div>




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