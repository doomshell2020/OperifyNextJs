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
<div class="content-wrapper">
   <section class="content-header">
      <h1>
         Payments Manager
      </h1>
      <ol class="breadcrumb">
         <li><a href="<?php echo SITE_URL; ?>admin/dashboards"><i class="fa fa-home"></i>Home</a></li>
         <li><a href="<?php echo SITE_URL; ?>admin/paymentmanager">Payments Manager</a></li>
      </ol>
   </section>
   <!-- content header -->
   <!-- Main content -->
   <section class="content">
      <div class="row">
         <div class="col-xs-12">
            <div class="box">
               <div class="box-header">
                  <?php echo $this->Flash->render(); ?>
                  <?php echo $this->Form->create('', array('type' => 'file', 'inputDefaults' => array('div' => false, 'label' => false), 'id' => 'mysubscription',  'style' => 'margin-bottom:0px;')); ?>
                  <div class="form-group" style="margin-bottom:0px;">
                     <div class="row">
                    
                        <div class="col">
                           <label class="control-label">Particulars</label>
                           <?= $this->Form->control('particular', [
                              'type' => 'select',
                              'options' => $particularList,
                              'empty' => 'Select Particular',
                              'label' => false,
                              'class' => 'form-control',
                              'value' => $this->request->query('particular')
                           ]) ?>
                        </div>



                        <div class="col">
                           <label for="fdatefrom" class="control-label">Due From</label>
                           <?= $this->Form->input('datefrom', [
                              'type' => 'text',
                              'class' => 'form-control',
                              'id' => 'fdatefrom',
                              'readonly' => true,
                              'placeholder' => 'From Date',
                              'label' => false,
                              'value' => $this->request->query('datefrom')
                           ]) ?>
                        </div>

                        <div class="col">
                           <label for="fdateto" class="control-label">Due To</label>
                           <?= $this->Form->input('dateto', [
                              'type' => 'text',
                              'class' => 'form-control',
                              'id' => 'fdateto',
                              'readonly' => true,
                              'placeholder' => 'To Date',
                              'label' => false,
                              'value' => $this->request->query('dateto')
                           ]) ?>
                        </div>


                        <script>
                           $(document).ready(function() {
                              var dateFormat = 'dd-mm-yy';

                              $('#fdatefrom').datepicker({
                                 dateFormat: dateFormat,
                                 changeMonth: true,
                                 changeYear: true,
                                 yearRange: '2018:2030',
                                 onClose: function(selectedDate) {
                                    $('#fdateto').datepicker('option', 'minDate', selectedDate);
                                 }
                              });

                              $('#fdateto').datepicker({
                                 dateFormat: dateFormat,
                                 changeMonth: true,
                                 changeYear: true,
                                 yearRange: '2018:2030',
                                 onClose: function(selectedDate) {
                                    $('#fdatefrom').datepicker('option', 'maxDate', selectedDate);
                                 }
                              });
                           });
                        </script>


                        <div class="col">
                           <label for="status" class="control-label">Payment Status</label>
                           <?= $this->Form->control('status', [
                              'type' => 'select',
                              'options' => [
                                 'P' => 'Pending',
                                 'C' => 'Completed',
                                 '' => 'All'
                              ],
                              'label' => false,
                              'class' => 'form-control',
                              'value' => $this->request->query('status')
                           ]) ?>
                        </div>

                        <div class="col">
                           <input type="submit"
                              style="background-color:#00c0ef; color:#fff;width:100px !important;margin-top:19px;" id=""
                              class="btn btn4 btn_pdf myscl-btn date" value="Search">

                           <a href="<?php echo SITE_URL; ?>admin/paymentmanager/index" class="excelbtn btn"
                              style="background-color:#00c0ef; !important; margin-top: 19px; color:#fff; padding:6px 18px;">Reset</a>

                        </div>

                        <div class="col">
                           <label for="sortby" class="control-label">Sort By</label>
                           <?= $this->Form->control('sortby', [
                              'type' => 'select',
                              'options' => [
                                 'D' => 'Dues',
                                 'L' => 'Latest Entry',
                                 'N' => 'Not Dispatched Yet',
                              ],
                              'label' => false,
                              'class' => 'form-control',
                              'value' => $this->request->query('sortby'),
                              'id' => 'sortby'
                           ]) ?>
                        </div>
                        <?php echo $this->Form->end(); ?>
                        <div class="col">

                           <?php
                           $role_permissions = $this->Permission->permissioncheck();
                           $fileurl = "admin/paymentmanager/add";
                           if (in_array($fileurl, $role_permissions)) {
                           ?>
                              <a class="btn btn-success pull-right m-top10"
                                 href="<?php echo SITE_URL; ?>admin/paymentmanager/add"
                                 style="background-color:#2d95e3;color:#fff;margin-top:19px;">
                                 <i class="fa fa-plus" aria-hidden="true"></i>Add</a>
                           <?php } ?>
                           <a href="<?php echo SITE_URL; ?>admin/paymentmanager/excel" class="excelbtn btn pull-right" style="padding:0;margin-top: 23px;"><i class="fa fa-file-excel-o" style="font-size:28px; margin-right:10px;"></i></a>



                           <!-- <div class="row">
                              <div class="col-md-6">
                                 <?= $this->Form->create(null, ['type' => 'file', 'url' => ['action' => 'excel']]) ?>
                                 <div class="form-group">
                                    <?= $this->Form->control('excel_file', [
                                       'type' => 'file',
                                       'label' => 'Upload Excel File',
                                       'class' => 'form-control',
                                       'required' => true
                                    ]) ?>
                                 </div>
                                 <div class="form-group">
                                    <?= $this->Form->button('Import Excel', ['class' => 'btn btn-success']) ?>
                                 </div>
                                 <?= $this->Form->end() ?>
                              </div>
                           </div> -->


                        </div>

                     </div>

                  </div>
               </div>
               <!-- /.box-header -->
               <div id="load2" style="display:none;"></div>
               <div class="box-body" style="padding-top:0px;" id="example23">
                  <table class="table table-bordered table-striped">
                     <thead>
                        <tr>
                           <th>S.No.</th>
                           <th>Particular's</th>
                           <th>Consignee</th>
                           <th>PO No.</th>
                           <th style="width: 10%;">Invoice No.</th>
                           <th style="width: 15%;">Date</th>
                           <th style="width: 10%;">Amount</th>
                           <th style="width: 10%;">Status</th>
                           <th>Action</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php
                        $page = $this->request->params['paging']['Particularpayments']['page'];
                        $limit = $this->request->params['paging']['Particularpayments']['perPage'];
                        $counter = ($page * $limit) - $limit + 1;
                        if (!empty($users)) :
                           foreach ($users as $intusr) :
                              $receivedAmount = $this->Comman->getReceivedTotalAmount($intusr['id']);
                              $pendingAmount = $intusr['amount'] - $receivedAmount;
                        ?>
                              <tr>
                                 <td><?= $counter ?></td>
                                 <td><?= $intusr['particular'] ?? '-' ?></td>
                                 <td><?= $intusr['consignee'] ?? '-' ?></td>
                                 <td><?= $intusr['po_no'] ?? '-' ?></td>
                                 <td><?= $intusr['invoice'] ?? '-' ?></td>
                                 <td>
                                    <?= 'Date: ' . (!empty($intusr['datefrom']) ? date('d-m-Y', strtotime($intusr['datefrom'])) : '-') ?><br>
                                    <?= 'Bill Dispatch Date: ' . (!empty($intusr['bill_dis_date']) ? date('d-m-Y', strtotime($intusr['bill_dis_date'])) : '-') ?><br>
                                    <br>
                                 </td>

                                 <td>
                                    <div style="display: flex; justify-content: space-between;">
                                       <span>Total:</span>
                                       <span>
                                          <a href="<?= SITE_URL ?>admin/paymentmanager/viewamount/<?= $intusr['id']; ?>" class="designsheetdetails" style="text-decoration: none;">
                                             <?= number_format($intusr['amount']) ?>
                                          </a>
                                       </span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between;">
                                       <span>Received:</span>
                                       <span><?= number_format($receivedAmount) ?></span>
                                    </div>
                                    <?php if ($pendingAmount != 0) { ?>
                                       <div style="display: flex; justify-content: space-between;">
                                          <span>Pending:</span>
                                          <span><?= number_format($pendingAmount) ?></span>
                                       </div>
                                    <?php } ?>
                                 </td>


                                 <td>

                                    <?php
                                    if (!empty($intusr['amount']) && isset($receivedAmount) && $intusr['amount'] == $receivedAmount) {
                                       // Payment completed
                                       echo '<strong style="color:green;">Completed</strong>';
                                    } elseif (!empty($intusr['bill_dis_date']) && !empty($intusr['due_period'])) {
                                       echo 'Due Period: ' . $intusr['due_period'] . ' Days<br>';

                                       $billDate = new DateTime($intusr['bill_dis_date']);
                                       $dueDays = (int)$intusr['due_period'];
                                       $dueDate = clone $billDate;
                                       $dueDate->modify("+$dueDays days");

                                       $today = new DateTime();
                                       $interval = $today->diff($dueDate);
                                       $daysDiff = $interval->days;

                                       if ($today > $dueDate) {
                                          // Overdue
                                          echo '<span style="color:red;">Overdue By: ' . $daysDiff . ' Days</span>';
                                       } else {
                                          // Remaining time
                                          echo 'Remaining Days: ' . $daysDiff;
                                       }
                                    } else {
                                       echo '-';
                                    }
                                    ?>
                                 </td>

                                 <td>
                                    <strong>
                                       <?php
                                       $role_permissions = $this->Permission->permissioncheck();
                                       $fileurl = "admin/paymentmanager/edit";
                                       if (in_array($fileurl, $role_permissions)) {
                                       ?>
                                          <?= $this->Html->link('', ['action' => 'edit', $intusr->id], ['class' => 'fas fa-edit', 'style' => 'font-size: 21px;']) ?>
                                          &nbsp;
                                       <?php } ?>
                                       <?php
                                       $role_permissions = $this->Permission->permissioncheck();
                                       $fileurl = "admin/paymentmanager/delete";
                                       if (in_array($fileurl, $role_permissions)) {
                                       ?>
                                          <?= $this->Html->link('', ['action' => 'delete', $intusr->id], [
                                             'class' => 'fas fa-trash-alt',
                                             'style' => 'font-size: 21px; color:#cd0404',
                                             'onClick' => "return confirm('Are you sure do you want to delete this Payment Detail?')"
                                          ]) ?>
                                       <?php } ?>
                                    </strong>
                                 </td>
                              </tr>

                        <?php
                              $counter++;
                           endforeach;
                        endif;
                        ?>
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
<script>
   $(document).ready(function() {
      $('#sevice_form').on('submit', function(e) {
         $("#formsubmitbtn").css("display", "none");
      });
   });



   $(document).ready(function() {
      $('#sortby').on('change', function() {
         $('#mysubscription').submit();
      });
   });
</script>

<script>
   $(document).ready(function() {
      $("#mysubscription").bind("submit", function(event) {
         $.ajax({
            async: true,
            data: $("#mysubscription").serialize(),
            dataType: "html",
            type: "GET",
            url: "<?php echo ADMIN_URL; ?>paymentmanager/searchitem",

            // success: function (data) {
            //    console.log(data);
            //    $("#example23").html(data);
            // },
            beforeSend: function(xhr) {
               xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
               $('#load2').css("display", "block"); // Show loader
            },
            success: function(data) {
               $('.lds-facebook').hide();
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

      $(document).on('click', '.pagination a', function(e) {
         var target = $(this).attr('href');
         var res = target.replace("/paymentmanager/searchitem", "/paymentmanager");
         window.location = res;
         return false;
      });
   });
</script>

<script>
   $('.designsheetdetails').click(function(e) {
      e.preventDefault();
      $('#designsorts').modal('show').find('.modal-body').load($(this).attr('href'));
   });
</script>

<div class="modal fade" id="designsorts">
   <div class="modal-dialog" style="max-width:900px !important;">
      <div class="modal-content">
         <div class="modal-body"></div>
      </div>
   </div>
</div>