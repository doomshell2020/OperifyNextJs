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
<div class="content-wrapper">
   <section class="content-header">
      <h1>
         Purchase Return
      </h1>
      <ol class="breadcrumb">
         <li><a href="<?php echo SITE_URL; ?>admin/dashboards/"><i class="fa fa-home"></i>Home</a></li>
         <li><a href="#">Purchase Return</a></li>
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
                              url: "<?php echo ADMIN_URL; ?>purchasereturn/searchitem",
                              success: function (data) {
                                 $("#example23").html(data);
                              },

                           });
                           return false;
                        });

                        $(document).on('click', '.pagination a', function (e) {
                           var target = $(this).attr('href');
                           var res = target.replace("/purchasereturn/searchitem", "/purchasereturn");
                           window.location = res;
                           return false;
                        });

                     });
                  </script>

                  <?php echo $this->Form->create('Mysubscription', array('type' => 'file', 'inputDefaults' => array('div' => false, 'label' => false), 'id' => 'Mysubscriptions', 'class' => 'form-horizontal')); ?>
                  <div class="form-group">
                     <div class="row">
                        <!-- <div class="col">
                           <label for="inputEmail3" class="control-label">PO ID</label>
                           <?php echo $this->Form->input('purchaseorder_id', array('class' => 'form-control', 'autocomplete' => 'off', 'type' => 'text', 'placeholder' => 'Enter Purchase Order ID', 'label' => false)); ?>
                        </div> -->
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

                           <a href="<?php echo SITE_URL; ?>admin/Purchasereturn/index" class="excelbtn btn"
                              style="background-color:#00c0ef; !important; margin-top: 23px; color:#fff; padding:6px 18px;">Reset</a>

                           <!-- <a href="<?php echo SITE_URL; ?>admin/goodsreceived/grnexcel" class="excelbtn btn" style="padding:0;margin-top: 23px;"><i class="fa fa-file-excel-o" style="font-size:28px;"></i></a> -->
                        </div>
                        <div class="col">
                           <a href="<?php echo SITE_URL; ?>admin/Purchasereturn/add"
                              class="btn btn-success pull-right m-top10"
                              style=" margin-top: 23px; color:#fff; padding:6px 10px;"><i
                                 class="fa fa-plus"></i>&nbsp;Add</a>
                        </div>
                        <br> <br> <br>

                     </div>
                  </div>
                  <?php echo $this->Form->end(); ?>

               </div>

               <div class="box-body" id="example23">
                  <table class="table table-bordered table-striped">
                     <thead>
                        <tr>
                           <th width="6%">Return Id</th>
                           <th width="7%">Return Date</th>
                           <th width="17">Vendor Name</th>
                           <th width="5%">Bill No.</th>
                           <th width="7%">Bill Date</th>
                           <th width="7%">GRN No.</th>
                           <th width="5%">PO Id</th>
                           <th width="7%">Amount</th>
                           <th width="28%">Description</th>
                           <th width="11%">Status</th>
                           <th width="6%">Action</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php $page = $this->request->params['paging']['purchasereturn']['page'];
                        $limit = $this->request->params['paging']['purchasereturn']['perPage'];
                        $counter = ($page * $limit) - $limit + 1;
                        if (isset($purchasereturn) && !empty($purchasereturn)) {
                           foreach ($purchasereturn as $intusr) {
                              $vendor_id = $this->Comman->findvendornames($intusr['vendor_id']);
                              ?>
                              <tr>
                                 <td> <a href="<?php echo SITE_URL; ?>admin/Purchasereturn/view/<?php echo $intusr['id']; ?>"
                                       class="viewpurchasereturn">
                                       <?php echo $intusr['id']; ?> </a></td>

                                 <td> <?php echo date('d-m-Y', strtotime($intusr['retrundate'])); ?> </td>

                                 <td> <a
                                       href="<?php echo SITE_URL; ?>admin/Purchasereturn/viewdetails/<?php echo $vendor_id['name']; ?>"
                                       class="editpurchasedetails">
                                       <?php echo $vendor_id['name']; ?> </a> </td>
                                 <td> <?php echo $intusr['bill_no']; ?> </td>
                                 <td> <?php echo date('d-m-Y', strtotime($intusr['bill_date'])); ?> </td>
                                 <td> <?php echo $intusr['grn_no']; ?> </td>
                                 <td> <?php echo $intusr['purchaseorder_id']; ?> </td>
                                 <td style="text-align:right;"> <?php echo sprintf('%.2f', $intusr['amount']); ?> </td>
                                 <td> <?php echo $intusr['description']; ?> </td>
                                 <td>
                                    <?php
                                    $user_id = $_SESSION['Auth']['User']['id'];
                                    $controllerName = $this->request->params['controller'];
                                    $actionName = $this->request->params['action'];
                                    $user_permission = $this->comman->finduserpermisson($user_id, $controllerName, $actionName);

                                    if ($user_permission['edit'] == '1') {
                                       if ($intusr['status'] == 'Active') {
                                          echo $this->Html->link('', [
                                             'action' => 'status',
                                             $intusr->id,
                                             'InActive'
                                          ], [
                                             'id' => 'Inactive',
                                             'class' => 'fa fa-check-circle',
                                             'style' => 'color: #36cb3c;  
                                                    font-size: 20px !important;'
                                          ]);
                                       } else {
                                          echo $this->Html->link('', [
                                             'action' => 'status',
                                             $intusr->id,
                                             'Active'
                                          ], [
                                             'id' => 'InActive',
                                             'class' => 'fa fa-times-circle-o',
                                             'style' => 'color:#FF5722; 
                                                    font-size: 20px !important;'
                                          ]);
                                       }
                                    } ?>
                                 </td>
                                 <td>
                                    <div style="display:flex; align-items: center;">
                                       <a target="_blank" title="View PDF"
                                          href="<?php echo ADMIN_URL; ?>Purchasereturn/view/<?php echo $intusr['id']; ?>"
                                          style="color:#2d95e3;  margin-right:5px;">
                                          <i class="far fa-file-pdf" style=" font-size: 18px !important;"></i>
                                       </a>
                                       <?php
                                       if ($user_permission['delete'] == '1') {
                                          echo $this->Html->link('', [
                                             'action' => 'delete',
                                             $intusr['id']
                                          ], [
                                             'class' => 'fas fa-trash-alt',
                                             'style' => 'font-size: 18px !important; color:#cd0404; !important;',
                                             "onClick" => "javascript: return confirm('Are you sure do you want to delete this Record')"
                                          ]);
                                       }
                                       ?>
                                    </div>
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
   <!-- content -->
</div>
<!-- content-wrapper -->


<script>
   $('.viewdetails').click(function (e) {
      e.preventDefault();
      $('#editpurchasedetails').modal('show').find('.modal-body').load($(this).attr('href'));
   });
</script>

<div class="modal fade" id="editpurchasedetails">
   <div class="modal-dialog" style="max-width:900px !important;">
      <div class="modal-content">
         <div class="modal-body"></div>
      </div>
   </div>
</div>