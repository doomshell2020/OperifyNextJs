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

<div class="content-wrapper">
   <section class="content-header">
      <h1>
         Goods/Material Received Note
      </h1>
      <ol class="breadcrumb">
         <li><a href="<?php echo SITE_URL; ?>admin/Goodsreceived"><i class="fa fa-home"></i>Home</a></li>
         <li><a href="<?php echo SITE_URL; ?>admin/Goodsreceived">Goods Received Manager</a></li>
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
                  <script>
                     function cllbckretail0(id, cid, sid) {
                        $('.secrh-retail').val(id);
                        $('#retail_ids').val(cid);
                        $('#testUL').hide();
                     }

                     $(function () {
                        $('.secrh-retail').bind('keyup', function () {
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
                                 success: function (data) {
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
                     $(document).ready(function () {
                        $("#Mysubscriptions").bind("submit", function (event) {
                           $.ajax({
                              async: true,
                              data: $("#Mysubscriptions").serialize(),
                              dataType: "html",
                              type: "GET",
                              url: "<?php echo ADMIN_URL; ?>goodsreceived/searchitem",
                              // success: function (data) {
                              //    $("#example23").html(data);
                              // },
                              beforeSend: function (xhr) {
                                 xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
                                 $('#load2').css("display", "block"); // Show loader
                              },
                              success: function (data) {
                                 $('.lds-facebook').hide();
                                 $("#example23").html(data);
                              },
                              complete: function () {
                                 $('#load2').css("display", "none"); // Hide loader
                              },
                              error: function () {
                                 alert("An error occurred. Please try again.");
                                 $('#load2').css("display", "none"); // Hide loader on error
                              }


                           });
                           return false;
                        });

                        $(document).on('click', '.pagination a', function (e) {
                           var target = $(this).attr('href');
                           var res = target.replace("/goodsreceived/searchitem", "/goodsreceived");
                           window.location = res;
                           return false;
                        });

                     });
                  </script>




                  <?php echo $this->Form->create('Mysubscription', array('type' => 'file', 'inputDefaults' => array('div' => false, 'label' => false), 'id' => 'Mysubscriptions', 'class' => 'form-horizontal')); ?>
                  <div class="form-group">
                     <div class="row">
                        <div class="col">
                           <label for="inputEmail3" class="control-label">PO ID</label>
                           <?php echo $this->Form->input('purchaseorder_id', array('class' => 'form-control', 'autocomplete' => 'off', 'type' => 'text', 'placeholder' => 'Enter Purchase Order ID', 'label' => false)); ?>
                        </div>
                        <div class="col">
                           <label for="inputEmail3" class="control-label">Vendor</label>
                           <input type="hidden" required="required" name="vendor_id" id="retail_ids">
                           <?php echo $this->Form->input('nitem', array('class' => 'form-control secrh-retail', 'id' => 'itemname', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'placeholder' => 'Enter Vendor Name')); ?>
                           <div id="testUL" style="display:none;">
                              <ul></ul>
                           </div>
                        </div>
                        <div class="col">
                           <script>
                              $(document).ready(function () {
                                 $('#fdatefrom').datepicker({
                                    dateFormat: 'dd-mm-yy',
                                    yearRange: '2018:2030',
                                    changeMonth: true,
                                    changeYear: true,
                                    onSelect: function (date) {
                                       var selectedDate = new Date(date);
                                       var endDate = new Date(selectedDate);
                                       endDate.setDate(endDate.getDate());
                                       $("#fendfrom").datepicker("option", endDate);
                                       $("#fendfrom").val();
                                    }
                                 });
                                 $('#fdatefrom').datepicker('setDate', '');
                                 $('#fendfrom').datepicker({
                                    dateFormat: 'dd-mm-yy',
                                    yearRange: '2018:2030',
                                    changeMonth: true,
                                    changeYear: true,
                                 });
                                 $('#fendfrom').datepicker('setDate', '');
                              });
                           </script>
                           <label for="inputEmail3" class="control-label">Date From</label>
                           <?php echo $this->Form->input('datefrom', array('class' => 'form-control', 'id' => 'fdatefrom', 'readonly', 'placeholder' => 'Date From', 'label' => false)); ?>
                        </div>
                        <div class="col">
                           <label for="inputEmail3" class="control-label">Date To</label>
                           <?php echo $this->Form->input('dateto', array('class' => 'form-control', 'id' => 'fendfrom', 'readonly', 'placeholder' => 'Date To', 'label' => false)); ?>
                        </div>
                        <div class="col">
                           <input type="submit"
                              style="background-color:#00c0ef; !important; margin-top: 23px; color:#fff; padding:6px 18px;"
                              id="Mysubscriptions" class="btn btn4 btn_pdf myscl-btn date" value="Search">

                           <a href="<?php echo SITE_URL; ?>admin/goodsreceived/index" class="excelbtn btn"
                              style="background-color:#00c0ef; !important; margin-top: 23px; color:#fff; padding:6px 18px;">Reset</a>
                        </div>

                        <div class="col">
                           <?php
                           $role_permissions = $this->Permission->permissioncheck();
                           $fileurl = "admin/goodsreceived/add";
                           // if (in_array($fileurl, $role_permissions)) { ?>
                              <a href="<?php echo SITE_URL; ?>admin/goodsreceived/add"
                                 class="btn btn-success pull-right m-top10"
                                 style=" margin-top: 23px; color:#fff; padding:6px 10px;"><i
                                    class="fa fa-plus"></i>&nbsp;Add</a>
                           <?php// } ?>
                           <a href="<?php echo SITE_URL; ?>admin/goodsreceived/grnexcel" class="excelbtn btn pull-right"
                              style="padding:0;margin-top: 23px;margin-right:5px;"><i class="fa fa-file-excel-o"
                                 style="font-size:28px;"></i></a>
                        </div>

                     </div>
                  </div>
                  <?php echo $this->Form->end(); ?>

               </div>
               <!--and serching -->
               <!-- /.box-header -->
               <div id="load2" style="display:none;"></div>
               <div class="box-body" style="padding-top:0px;" id="example23">
                  <table class="table table-bordered table-striped">
                     <thead>
                        <tr>
                           <th>GRN No.</th>
                           <th>PO Id</th>
                           <th>G.R.N. Inward</th>
                           <th>Bill No.</th>
                           <th>Bill Date</th>
                           <th>Supplier</th>
                           <th>Total Qty.</th>
                           <th>Total Received Qty.</th>
                           <th>Total Amount (INR)</th>
                           <th>Action</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php $page = $this->request->params['paging']['Goodsreceived']['page'];
                        $limit = $this->request->params['paging']['Goodsreceived']['perPage'];
                        $counter = ($page * $limit) - $limit + 1;
                        if (isset($goodsreceived) && !empty($goodsreceived)) {
                           foreach ($goodsreceived as $intusr) {

                              $vendor_id = $this->Comman->findvendornames($intusr['vendor_id']);
                              $po = $this->Comman->getpoqty($intusr['purchaseorder_id']);
                              $remain = $this->Comman->goodsrecivied($intusr['purchaseorder_id'], $intusr['id']);
                              $getpo = $this->Comman->getPurchaseOrder($intusr['purchaseorder_id']);

                              ?>
                              <tr>
                                 <td>
                                    <a class="viewgrndetails"
                                       href="<?php echo SITE_URL; ?>admin/goodsreceived/viewgrndetail/<?php echo $intusr['id']; ?>">
                                       <?php echo $intusr['id']; ?>
                                    </a>
                                 </td>

                                 <td>
                                    <a class="viewgrndetails"
                                       href="<?php echo ADMIN_URL; ?>purchaseorder/viewpodetail/<?php echo $intusr['purchaseorder_id'] . "/" . $getpo['is_revised'] . "/" . $getpo['id']; ?>">
                                       <?php echo $intusr['purchaseorder_id']; ?>
                                    </a>
                                 </td>

                                 <td>
                                    <?php echo date("d-m-Y", strtotime($intusr['inwarddate'])); ?>
                                 </td>
                                 <td>
                                    <?php echo $intusr['bill_no']; ?>
                                 </td>
                                 <td>
                                    <?php echo date("d-m-Y", strtotime($intusr['bill_date'])); ?>
                                 </td>
                                 <td>
                                    <?php echo $vendor_id['name']; ?>
                                 </td>
                                 <td style="text-align:right;">
                                    <?php echo $po['total_qty']; ?>
                                 </td>
                                 <td style="text-align:right;">
                                    <?php echo $intusr['total_qty']; ?>
                                 </td>
                                 <!-- <td><? php // echo $remn=$po['total_qty']-$remain[0]['quantity']; 
                                          ?></td> -->
                                 <td style="text-align:right;">
                                    <?php //echo $intusr['total_amt']; ?>
                                      <?php echo number_format($intusr['total_amt']); ?>
                                 </td>
                                 <!-- <td>
                                    <?php if ($intusr['status'] == 'O') {
                                       echo "<strong style='color:red;'>Open</strong>";
                                    } else {
                                       echo "<strong style='color:green;'>Close</strong>";
                                    } ?>
                                 </td> -->
                                 <td><a title="View PDF" target="_blank"
                                       href="<?php echo ADMIN_URL; ?>goodsreceived/view/<?php echo $intusr['id']; ?>"><i
                                          class="fa fa-file-pdf-o" style="
                              font-size: 20px; color:#c51515;
                              "></i>&nbsp;</a>
                                    <a title="Download" class="fa fa-download fa-lg text-green"
                                       href="<?php echo ADMIN_URL; ?>goodsreceived/view/<?php echo $intusr['id']; ?>"
                                       download="NewName.pdf"></a>
                                 </td>
                              </tr>
                              <?php $counter++;
                           }
                        } else { ?>
                        <?php } ?>
                     </tbody>
                  </table>
                  <?php echo $this->element('admin/pagination'); ?>
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
<div class="modal fade" id="globalModalbag" style="width:51% !important;" tabindex="-1" role="dialog"
   aria-labelledby="esModalLabel" aria-hidden="true">
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

<script>
   $('.viewgrndetails').click(function (e) {
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