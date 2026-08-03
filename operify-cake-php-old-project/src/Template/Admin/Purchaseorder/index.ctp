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

   #test1UL {
      position: relative;
   }

   #test1UL ul {
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

   #test1UL ul li {
      padding: 5px 8px;
      border: 1px solid lightgray;
   }

   #test1UL ul li a {
      color: black;
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

   <a href="<?php echo ADMIN_URL; ?>purchaseorder/view/<?php echo $ids5 . "/" . $ids4 . "/" . $ids3; ?>" target="_blank"
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
         Purchase Order

      </h1>
      <ol class="breadcrumb">
         <li><a href="<?php echo SITE_URL; ?>admin/indent"><i class="fa fa-home"></i>Home</a></li>
         <li><a href="<?php echo SITE_URL; ?>admin/purchaseorder">Purchase Order</a></li>
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
                           // alert('sdgdfg'),
                           $.ajax({
                              async: true,
                              data: $("#vendorsdetails").serialize(),
                              dataType: "html",
                              type: "get",
                              url: "<?php echo ADMIN_URL; ?>purchaseorder/searchitem",

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



                  <?php echo $this->Form->create('Purchaseorder', array('type' => 'file', 'inputDefaults' => array('div' => false, 'label' => false), 'id' => 'vendorsdetails')); ?>
                  <div class="form-group">
                     <div class="row">
                        <div class="col">
                           <label for="inputEmail3" class="control-label">Type</label>
                           <?php $type = array('po' => 'PO', 'deli' => 'Delivery Schedule', 'comp' => 'PO Comparison ');
                           echo $this->Form->input('type', array('class' => 'form-control type', 'type' => 'select', 'options' => $type, 'placeholder' => 'Status', 'label' => false, 'value' => $typeselect)); ?>
                        </div>
                        <div class="col poId">
                           <label for="inputEmail3" class="control-label ">PO ID</label>
                           <?php echo $this->Form->input('purchaseorder_id', array('class' => 'form-control', 'autocomplete' => 'off', 'type' => 'text', 'placeholder' => 'Enter Purchase Order ID', 'label' => false)); ?>
                        </div>

                        <div class="col itemname">
                           <label for="inputEmail3" class="control-label">Vendor</label>
                           <input type="hidden" required="required" name="vendor_id" id="retail_ids">
                           <?php echo $this->Form->input('nitem', array('class' => 'form-control secrh-retail', 'id' => 'itemname', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'placeholder' => 'Enter Vendor Name')); ?>
                           <div id="testUL" style="display:none;">
                              <ul></ul>
                           </div>
                        </div>

                        <div class="col">
                           <label for="inputEmail3" class=" control-label" style="text-align: left !important">Product
                              Name</label>
                           <input type="hidden" required="required" name="item_id" id="retail_id">
                           <?php echo $this->Form->input('item_name', array('class' => 'form-control secrh-retails', 'id' => 'indent', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'placeholder' => 'Enter Product Name')); ?>

                           <div id="test1UL" style="display:none;">
                              <ul></ul>
                           </div>
                           <div id="test1UL1" style="display:none;">
                              <ul>
                                 <li
                                    style="padding: 5px 8px;list-style:none;color: black;font-weight: bold;margin-left:-32px; border: 1px solid lightgray;">
                                    No Record Found</li>
                              </ul>
                           </div>
                        </div>
                        <div class="col">
                           <script>
                              $(document).ready(function() {
                                 $('#fdatefrom').datepicker({
                                    dateFormat: 'dd-mm-yy',
                                    yearRange: '2018:2030',
                                    changeMonth: true,
                                    changeYear: true,
                                    onSelect: function(date) {
                                       var selectedDate = new Date(date);
                                       var endDate = new Date(selectedDate);
                                       endDate.setDate(endDate.getDate());

                                       // Set the minimum date for the end date picker
                                       $("#fendfrom").datepicker("option", "minDate", endDate);
                                       $("#fendfrom").val();
                                    }
                                 });


                                 $('#fendfrom').datepicker({
                                    dateFormat: 'dd-mm-yy',
                                    yearRange: '2018:2030',
                                    changeMonth: true,
                                    changeYear: true,
                                 });

                                 // Set the initial value if available
                                 <?php if (!empty($datefrom)): ?>
                                    $('#fdatefrom').datepicker('setDate', '<?= $datefrom; ?>');
                                 <?php endif; ?>

                                 <?php if (!empty($dateto)): ?>
                                    $('#fendfrom').datepicker('setDate', '<?= $dateto; ?>');
                                 <?php endif; ?>
                              });
                           </script>

                           <label for="inputEmail3" class="control-label">Date From</label>
                           <?php echo $this->Form->input('datefrom', array('class' => 'form-control', 'id' => 'fdatefrom', 'readonly' => true, 'placeholder' => 'Date From', 'label' => false, 'value' => $datefrom)); ?>
                        </div>

                        <div class="col">
                           <label for="inputEmail3" class="control-label">Date To</label>
                           <?php echo $this->Form->input('dateto', array('class' => 'form-control', 'id' => 'fendfrom', 'readonly' => true, 'placeholder' => 'Date To', 'label' => false, 'value' => $dateto)); ?>
                        </div>

                        <div class="col status">
                           <label for="inputEmail3" class="control-label">Status</label>
                           <?php $stats = array('' => 'All', 'O' => 'Open', 'C' => 'Close');
                           echo $this->Form->input('status', array('class' => 'form-control', 'type' => 'select', 'options' => $stats, 'placeholder' => 'Status', 'label' => false, 'value' => $status)); ?>
                        </div>
                        <div class="col-sm-2">
                           <input type="submit"
                              style=" margin-top: 23px; color:#fff; padding:6px 17px;"
                              id="Mysubscriptions" class="btn btn4 btn_pdf myscl-btn date" value="Search">

                           <a href="<?php echo SITE_URL; ?>admin/purchaseorder/index" class="excelbtn btn"
                              style=" margin-top: 23px; color:#fff; padding:6px 17px;">Reset</a>
                        </div>

                        <div class="col-sm-2">

                           <?php
                           $role_permissions = $this->Permission->permissioncheck();
                           $fileurl = "admin/purchaseorder/add";
                           if (in_array($fileurl, $role_permissions)) { ?>
                              <a href="<?php echo SITE_URL; ?>admin/purchaseorder/add"
                                 class="btn btn-success pull-right m-top10"
                                 style=" margin-top: 23px; color:#fff; padding:6px 10px;"><i
                                    class="fa fa-plus"></i>&nbsp;Add</a>
                           <?php } ?>


                           <a href="<?php echo SITE_URL; ?>admin/purchaseorder/posummaryreport"
                              class="excelbtn btn  pull-right poreport" title='PO GRN Report'
                              style="padding:0;margin-top: 23px;;margin-right: 5px;"><i class="fa fa-file-excel-o"
                                 style="font-size:28px;"></i></a>

                           <a href="<?php echo SITE_URL; ?>admin/purchaseorder/productcomparisonreport"
                              title='PO Comparison Report' title='Item Comparison' class="excelbtn btn  pull-right comp"
                              style="padding:0;margin-top: 23px;;margin-right: 5px;display:none;"><i
                                 class="fa fa-file-excel-o" style="font-size:28px;color:red;"></i></a>

                           <a href="<?php echo SITE_URL; ?>admin/purchaseorder/deliveryreport"
                              title='Delivery Schedule Report' class="excelbtn btn  pull-right deli"
                              style="padding:0;margin-top: 23px;;margin-right: 5px;display:none;"><i
                                 class="fa fa-file-excel-o" style="font-size:28px;color:blue;"></i></a>
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
                           <th width="10%">PO Id</th>
                           <th width="08%">PO Date</th>
                           <th width="23%">Vendor</th>
                           <th width="10%">Contact No.</th>
                           <th width="09%">Order Qty</th>
                           <th width="09%">Received Qty</th>
                           <th width="09%">Total(INR)</th>
                           <th width="08%">Delivery Date</th>
                           <th width="13%">Action</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php
                        $page = $this->request->params['paging']['Purchaseorder']['page'];
                        $limit = $this->request->params['paging']['Purchaseorder']['perPage'];
                        $counter = ($page * $limit) - $limit + 1;

                        if (isset($podata) && !empty($podata)) {
                           foreach ($podata as $value) { //pr($value);
                              $var = $this->Comman->poitemquantity($value['purchaseorder_id'], $value['is_revised'], $value['id']);
                              $podetail = $this->Comman->podetail($value['purchaseorder_id'], $value['is_revised'], $value['id']);
                              $vendor_id = $this->Comman->findvendornames($value['vendor_id']);
                              $getgrn = $this->Comman->checkgrn($value['purchaseorder_id'], $value['id']);
                              $pendingQty = '';
                              foreach ($getgrn as $value1) {
                                 $pendingQty += $value1['quantity'];
                              }

                              $user_id = $_SESSION['Auth']['User']['id'];
                              $controllerName = $this->request->params['controller'];
                              $actionName = $this->request->params['action'];
                              $user_permission = $this->comman->finduserpermisson($user_id, $controllerName, $actionName);
                              $podetail = $this->Comman->findgoodsrecivied($value['purchaseorder_id']);
                              $porevised = $this->Comman->findrevisedno($value['purchaseorder_id']);

                        ?>
                              <tr>
                                 <td>
                                    <a class="viewgrndetails"
                                       href="<?php echo ADMIN_URL; ?>purchaseorder/viewpodetail/<?php echo $value['purchaseorder_id'] . "/" . $value['is_revised'] . "/" . $value['id']; ?>">
                                       <?php echo $value['purchaseorder_id']; ?>
                                    </a>
                                    <?php if ($value['is_revised'] > 0) { ?>
                                       <a class="viewgrndetails" style="font-size: 20px;"
                                          href="<?php echo ADMIN_URL; ?>purchaseorder/viewpodetail/<?php echo $value['purchaseorder_id'] . "/" . $value['is_revised'] . "/" . $value['id']; ?>">R
                                          <?php echo "-" . $value['is_revised']; ?>&nbsp;
                                       </a>
                                    <?php } ?>
                                 </td>
                                 <td>
                                    <?php echo date("d-m-Y", strtotime($value['added_time'])); ?>
                                 </td>
                                 <td>
                                    <a class="viewvendordetails"
                                       href="<?php echo ADMIN_URL; ?>vendors/viewdetail/<?php echo $value['vendor_id']; ?>">
                                       <?php echo ucfirst(strtolower($vendor_id['name'])); ?>
                                    </a>
                                    <?php $fileurl = "admin/vendors/add";
                                    if (in_array($fileurl, $role_permissions)) { ?>
                                       <a target="_blank"
                                          href="<?php echo ADMIN_URL; ?>vendors/add/<?php echo $value['vendor_id'] ?>"
                                          class="fas fa-edit pull-right" style="color:#303c30;">
                                       </a>
                                    <?php } ?>
                                 </td>
                                 <td>
                                    <?php $ert = explode(',', $vendor_id['contact_no']);
                                    if (isset($vendor_id['contact_no'])) {
                                       foreach ($ert as $fg) {
                                          echo $fg;
                                       }
                                    } else {
                                       echo 'N/A';
                                    }
                                    ?>
                                 </td>
                                 <td style="text-align:right;">
                                    <?php echo number_format((float) $value['total_qty'], 2, '.', ''); ?>
                                 </td>
                                 <td style="text-align:right;">
                                    <?php echo number_format((float) $pendingQty, 2, '.', ''); ?>
                                 </td>
                                 <td style="text-align:right;">
                                    <?php echo number_format($value['total_amt']); ?>
                                 </td>

                                 <td>
                                    <?php echo date("d-m-Y", strtotime($value['delivery_date'])); ?>
                                 </td>

                                 <td style="text-align:center">
                                    <button class="btn btn-primary dropdown-toggle dropppss dropdown" type="button"
                                       id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                       aria-expanded="false">
                                       Action
                                    </button>

                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                       <?php
                                       // edit or revised
                                       if ($porevised['is_revised'] == $value['is_revised']) {
                                          $postatus = $value['postatus'];
                                          /*
                                          if ($podetail) {
                                             if ($postatus != 'C') {
                                                $fileurl = "admin/purchaseorder/edit";
                                                if (in_array($fileurl, $role_permissions)) { ?>
                                                   <a href="<?php echo ADMIN_URL; ?>purchaseorder/edit/<?php echo $value['id']; ?>/<?php echo $value['purchaseorder_id']; ?>"
                                                      class="dropdown-item hover">
                                                      <b>Edit PO</b>
                                                   </a>
                                                <?php }
                                             }
                                          } else {
                                           */
                                          // if ($postatus != 'C') {
                                          $fileurl = "admin/purchaseorder/revised";
                                          if (in_array($fileurl, $role_permissions)) { ?>
                                             <a href="<?php echo ADMIN_URL; ?>purchaseorder/revised/<?php echo $value['purchaseorder_id']; ?>/<?php echo $value['id']; ?>"
                                                class="dropdown-item hover">
                                                <b>Revise PO</b>
                                             </a>
                                             <?php }
                                          // }

                                          // delete
                                          $fileurl = "admin/purchaseorder/delete";
                                          if (in_array($fileurl, $role_permissions)) {
                                             if ($value['status'] != 'N' && $podetail == null) { ?>
                                                <a href="<?php echo ADMIN_URL; ?>purchaseorder/delete/<?php echo $value['id'] ?>"
                                                   style="color:red;" class="dropdown-item hover"
                                                   onClick="javascript:return confirm('Are you sure do you want to delete this Purchase Order')"><b>Delete</b>
                                                </a>
                                             <?php }
                                          }

                                          // add delivery note  
                                          $fileurl = "admin/purchaseorder/deliverynote";
                                          if (in_array($fileurl, $role_permissions)) {
                                             $getDeliverydates = $this->Comman->getDeliverydates($value['id']);
                                             if ($postatus != 'C' && empty($getDeliverydates)) { ?>
                                                <a href="<?php echo ADMIN_URL; ?>purchaseorder/deliverynote/<?php echo $value['id'] ?>"
                                                   class="dropdown-item hover"><b>Add Delivery Note</b> </a>
                                             <?php } else { ?>
                                                <a href="<?php echo ADMIN_URL; ?>purchaseorder/deliverynote/<?php echo $value['id'] ?>"
                                                   style="color:green" class="dropdown-item hover"><b>Edit Delivery Note</b> </a>
                                       <?php }
                                          }
                                       } ?>

                                       <!-- // print current Po Pdf -->
                                       <a class="dropdown-item hover" target="_blank" style="color:blue"
                                          href="<?php echo ADMIN_URL; ?>purchaseorder/view/<?php echo $value['purchaseorder_id'] . "/" . $value['is_revised'] . "/" . $value['id'] ?>"><b>Print
                                             PO <?php echo $value['purchaseorder_id'];
                                                if ($value['is_revised'] > 0) { ?>
                                                <?php echo "R-" . $value['is_revised']; ?>&nbsp;</b>
                                       </a>
                                    <?php }
                                                // print all po Pdf
                                                if ($value['is_revised'] > 0) { ?>
                                       <a class="dropdown-item hover" target="_blank" style="color:blue"
                                          href="<?php echo ADMIN_URL; ?>purchaseorder/printallpo/<?php echo $value['purchaseorder_id'] ?>"><b>Print
                                             Revised PO</b>
                                       </a>
                                    <?php }

                                                // print Delivery schedule
                                                if ($getDeliverydates) { ?>
                                       <a class="dropdown-item hover" target="_blank" style="color:blue"
                                          href="<?php echo ADMIN_URL; ?>purchaseorder/printdeliveryschedule/<?php echo $value['id'] ?>"><b>Print
                                             Delivery Schedule</b></a>
                                    <?php } ?>
                                    </div>
                                 </td>
                              </tr>
                           <?php $counter++;
                           }
                        } else { ?>
                        <?php } ?>
                     </tbody>
                  </table>
                  <?php // echo $this->element('admin/pagination'); 
                  ?>
                  <?php echo $this->element('admin/custompagination'); ?>
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
         $('.modal-content').load($(this).attr("href")); //load content from href of link
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

<!-- for view Vendor details -->
<script>
   $('.viewvendordetails').click(function(e) {
      e.preventDefault();
      $('#editsortsvendor').modal('show').find('.modal-body').load($(this).attr('href'));
   });
</script>

<div class="modal fade" id="editsortsvendor">
   <div class="modal-dialog " style="max-width: 900px !important;">
      <div class="modal-content">
         <div class="modal-body"></div>
      </div>
   </div>
</div>

<script>
   $(document).ready(function() {
      function handleTypeChange() {
         var value = $(".type").val();
         if (value == 'comp') {
            $(".comp").css("display", "block");
            $(".deli").css("display", "none");
            $(".poreport").css("display", "none");
            $(".status").css("display", "none");
            $(".itemname").css("display", "none");
            $(".poId").css("display", "none");
         } else if (value == 'deli') {
            $(".deli").css("display", "block");
            $(".comp").css("display", "none");
            $(".poreport").css("display", "none");
            $(".status").css("display", "none");
            $(".itemname").css("display", "none");
            $(".poId").css("display", "none");
         } else {
            $(".poreport").css("display", "block");
            $(".comp").css("display", "none");
            $(".deli").css("display", "none");
            $(".status").css("display", "block");
            $(".itemname").css("display", "block");
            $(".poId").css("display", "block");
         }
      }

      handleTypeChange();
      $(".type").change(function() {
         handleTypeChange();
      });
   });
</script>


<style>
   .open .dropdown-menu {
      display: block;
   }
</style>



<script type="text/javascript">
   function cllbckretail2(name, id) {
      $('.secrh-retails').val(name);
      $('#retail_id').val(id);
      $('#test1UL').hide();
      $('#test1UL1').hide();
   }

   $(function() {
      $('.secrh-retails').bind('keyup', function() {
         var pos = $(this).val();
         var check = 0;
         $('#test1UL').show();
         $('#retail_id').val('');
         var count = pos.length;
         if (count > 0) {
            $.ajax({
               type: 'POST',
               url: '<?php echo ADMIN_URL; ?>Purchaseorder/getallitemname',
               data: {
                  'fetch': pos,
                  'check': check
               },
               success: function(data) {
                  if (data) {
                     $('#test1UL ul').html(data);
                     $('#test1UL1').hide();
                  } else {
                     $('#test1UL').hide();
                     $('#test1UL1').show();
                  }
               },
            });
         } else {
            $('#test1UL').hide();
            $('#test1UL1').hide();
         }
      });
   });
</script>