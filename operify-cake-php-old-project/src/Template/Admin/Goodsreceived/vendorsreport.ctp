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
</style>
<?php
if ($ids3) {

   ?>
   <a href="<?php echo ADMIN_URL; ?>purchaseorder/view/<?php echo $ids5 . "/" . $ids4 . "/" . $ids3; ?>" target="_blank"
      id="redicaution"></a>
   <script type="text/javascript">
      $('#redicaution')[0].click();
   </script>
<?php } ?>
<div class="content-wrapper">
   <section class="content-header">
      <h1>
         Vendors Report
      </h1>
      <ol class="breadcrumb">
         <li><a href="<?php echo SITE_URL; ?>admin/goodsreceived/vendorsreport"><i class="fa fa-home"></i>Home</a>
         </li>
         <li><a href="<?php echo SITE_URL; ?>admin/goodsreceived/vendorsreport">Vendors Report</a></li>
      </ol>
   </section>
   <!-- content header -->
   <!-- Main content -->
   <section class="content">
      <div class="row">
         <div class="col-xs-12">
            <div class="box">
               <div class="box-header" style="padding-bottom:0px;">
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
                        $("#vendorsdetails").bind("submit", function (event) {
                           $.ajax({
                              async: true,
                              data: $("#vendorsdetails").serialize(),
                              dataType: "html",
                              type: "get",
                              url: "<?php echo ADMIN_URL; ?>goodsreceived/searchstock",
                              beforeSend: function () { },
                              success: function (data) {
                                 $("#example23").html(data);
                              },
                              complete: function (data) { },
                           });
                           return false;
                        });
                        $(document).on('click', '.pagination a', function (e) {
                           var target = $(this).attr('href');
                           var res = target.replace("/goodsreceived/searchstock",
                              "/goodsreceived");
                           window.location = res;
                           return false;
                        });
                     });
                  </script>
                  <?php echo $this->Form->create('Purchaseorder', array('type' => 'file', 'inputDefaults' => array('div' => false, 'label' => false), 'id' => 'vendorsdetails', 'class' => 'form-horizontal')); ?>
                  <div class="form-group">
                     <div class="row" style="margin-bottom: 20px;">
                        <div class="col-sm-2">
                           <label for="inputEmail3" class="control-label">Vendor</label>
                           <input type="hidden" required="required" name="vendor_id" id="retail_ids">
                           <?php echo $this->Form->input('nitem', array('class' => 'form-control secrh-retail', 'id' => 'itemname', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'placeholder' => 'Enter Vendor Name')); ?>
                           <div id="testUL" style="display:none;">
                              <ul></ul>
                           </div>
                        </div>


                        <div class="col-sm-2">
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
                                       $("#fendfrom").val(date);
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



                           <label for="inputEmail3" class="control-label">Date From <strong
                                 style="color:red;">*</strong></label>
                           <?php echo $this->Form->input('datefrom', array('class' => 'form-control', 'id' => 'fdatefrom', 'readonly', 'placeholder' => 'Date From', 'label' => false)); ?>
                        </div>

                        <div class="col-sm-2">
                           <label for="inputEmail3" class="control-label">Date To <strong
                                 style="color:red;">*</strong></label>
                           <?php echo $this->Form->input('dateto', array('class' => 'form-control', 'id' => 'fendfrom', 'readonly', 'placeholder' => 'Date To', 'label' => false)); ?>
                        </div>

                        <div class="col-sm-6">
                           <input type="submit"
                              style="background-color:#00c0ef;width:100px !important; margin-top: 23px; color:#fff; padding:6px 10px;"
                              id="Mysubscriptions" class="btn btn4 btn_pdf myscl-btn date" value="Search">

                           <a href="<?php echo SITE_URL; ?>admin/goodsreceived/vendorsreport" class="excelbtn btn"
                              style="background-color:#00c0ef; !important; margin-top: 23px; color:#fff; padding:6px 18px;">Reset</a>


                           <a href="<?php echo SITE_URL; ?>admin/goodsreceived/viewpdf" class="excelbtn btn" target="_blank"
                              style="padding:0;margin-top: 23px;;margin-right: 5px;"><i class="fa fa-file-pdf-o"
                                 style="font-size:28px;"></i></a>

                           <a id="" style=" margin-top: 23px;margin-left:40px;padding:6px 18px;"
                              class="btn btn-info btn-sm pull-right" target="_blank"
                              href="<?php echo ADMIN_URL; ?>goodsreceived/summaryexcel"><i class="fa fa-file-excel-o"
                                 aria-hidden="true"></i> Export Summary Excel</a>
                        </div>
                     </div>
                  </div>
                  <?php echo $this->Form->end(); ?>

               </div>
               <!-- /.box-header -->
               <div class="box-body" id="example23" style="padding-top:0px;">
                  <table class="table table-bordered table-striped" width="100%">
                     <thead>
                        <tr>
                           <th width="5%">S.No.</th>
                           <th width="10%">GRN Inward Date</th>
                           <th width="10%">GRN No.</th>
                           <th width="10%">PO No.</th>
                           <th width="10%">Bill No.</th>
                           <th width="20%">Vendor</th>
                           <th width="10%">Total Amount</th>
                        </tr>

                     </thead>
                     <tbody>
                        <?php $page = $this->request->params['paging']['Goodsreceived']['page'];
                        $limit = $this->request->params['paging']['Goodsreceived']['perPage'];
                        $counter = ($page * $limit) - $limit + 1;
                        if (isset($goodsreceived) && !empty($goodsreceived)) {
                           foreach ($goodsreceived as $value) {
                              $vendor_id = $this->Comman->findvendornames($value['vendor_id']);
                              $getpo = $this->Comman->getPurchaseOrder($value['purchaseorder_id']);

                              ?>
                              <tr>
                                 <td>
                                    <?php echo $counter; ?>.
                                 </td>
                                 <td>
                                    <?php echo date("d-m-Y", strtotime($value['inwarddate'])); ?>
                                 </td>
                                 <td>
                                    <a class="viewgrndetails"
                                       href="<?php echo SITE_URL; ?>admin/goodsreceived/viewgrndetail/<?php echo $value['id']; ?>">
                                       <?php echo $value['id']; ?>
                                    </a>
                                 </td>
                                 <td>
                                    <a class="viewgrndetails"
                                       href="<?php echo ADMIN_URL; ?>purchaseorder/viewpodetail/<?php echo $value['purchaseorder_id'] . "/" . $getpo['is_revised'] . "/" . $getpo['id']; ?>">
                                       <?php echo $value['purchaseorder_id']; ?>
                                    </a>
                                 </td> 
                                 <td>
                                    <?php echo $value['bill_no']; ?>
                                 </td>
                                 <td>
                                    <?php echo $vendor_id['name']; ?>
                                 </td>

                                 <td style="text-align:right;">
                                    <?php echo number_format((float) $value['total_amt'], 2, '.', ''); ?>
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
   $(document).ready(function () {
      $(".globalModals").click(function (event) {
         alert($(this).attr("href"));
         $('.modal-content').load($(this).attr("href")); //load content from href of link
      });
   });
</script>
<script>
   $('.delivery_note').click(function (e) {
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