<div class="content-wrapper">
   <section class="content-header">
      <h1>
         Company Master Manager
      </h1>
      <ol class="breadcrumb">
         <li><a href="<?php echo SITE_URL; ?>admin/dashboards"><i class="fa fa-home"></i>Home</a></li>
         <li><a href="<?php echo SITE_URL; ?>admin/companymaster">Company Master Manager</a></li>
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
                  <!-- <a href="<?php echo SITE_URL; ?>admin/sizemanager/add">
                     <button class="btn btn-success pull-right m-top10"><i class="fa fa-plus" aria-hidden="true"></i>
                     Add Size</button></a> -->
                  <?php echo $this->Form->create(
                     $cat,
                     array(
                        'class' => 'form-horizontal',
                        'enctype' => 'multipart/form-data',
                        'controller' => 'CompanyController',
                        'id' => 'sevice_form',
                        'validate'
                     )
                  ); ?>
                  <div class="form-group" style="margin-bottom:0px;">
                     <div class="row">
                        <div class="col-sm-4">
                           <label for="inputEmail3" class="control-label">Company Name<strong
                                 style='color:red;'>*</strong></label>
                           <?php echo $this->Form->input('cname', array('class' => 'form-control', 'type' => 'text', 'required', 'label' => false, 'placeholder' => 'Enter Company Name', 'autofocus', 'autocomplete' => 'off')); ?>
                        </div>
                        <div class="col-sm-4">
                           <label for="inputEmail3" class="control-label">GST No.<strong
                                 style='color:red;'>*</strong></label>
                           <?php echo $this->Form->input('gst', array('class' => 'form-control', 'type' => 'text', 'required', 'label' => false, 'placeholder' => 'Enter Gst No.', 'autofocus', 'autocomplete' => 'off')); ?>
                        </div>
                        <div class="col-sm-4">
                           <label for="inputEmail3" class="control-label">Account No.<strong
                                 style='color:red;'>*</strong></label>
                           <?php echo $this->Form->input('accountno', array('class' => 'form-control', 'type' => 'text', 'required', 'label' => false, 'placeholder' => 'Enter Account No', 'autofocus', 'autocomplete' => 'off')); ?>
                        </div>
                        <div class="col-sm-4">
                           <label for="inputEmail3" class="control-label">Tin Date<strong
                                 style='color:red;'>*</strong></label>
                           <?php echo $this->Form->input('tin_date', array('class' => 'form-control input1', 'label' => false, 'placeholder' => 'From Date', 'id' => 'datepicker1', 'autocomplete' => 'off', 'readonly')); ?>
                        </div>
                        <div class="col-sm-4">
                           <label for="inputEmail3" class="control-label">IFSC Code<strong
                                 style='color:red;'>*</strong></label>
                           <?php echo $this->Form->input('ifsc', array('class' => 'form-control', 'type' => 'text', 'required', 'label' => false, 'placeholder' => 'Enter IFSC Code', 'autofocus', 'autocomplete' => 'off')); ?>
                        </div>
                        <div class="col-sm-12">
                           <label for="inputEmail3" class="control-label">Address<strong
                                 style='color:red;'>*</strong></label>
                           <?php echo $this->Form->textarea('address', array('class' => 'form-control', 'type' => 'text', 'required', 'label' => false, 'placeholder' => 'Enter Description', 'autofocus', 'autocomplete' => 'off')); ?>
                        </div>
                        <div class="col-sm-12" style="margin-top:15px;">
                           <?php
                           if (isset($cat['id'])) {
                              echo $this->Form->submit(
                                 'Update',
                                 array('class' => 'btn btn-info pull-right', 'id' => 'formsubmitbtn', 'title' => 'Update')
                              );
                           } else {
                              echo $this->Form->submit(
                                 'Add',
                                 array('class' => 'btn btn-info', 'id' => 'formsubmitbtn', 'title' => 'Add')
                              );
                           }
                           ?>
                           <?php echo $this->Form->end(); ?>
                        </div>
                     </div>
                  </div>
                  <script>
                     $(document).ready(function () {
                        $("#Mysubscriptions").bind("submit", function (event) {
                           $('.lds-facebook').show();
                           $.ajax({
                              async: true,
                              data: $("#Mysubscriptions").serialize(),
                              dataType: "html",
                              type: "POST",
                              url: "<?php echo ADMIN_URL; ?>Companymaster/searchitem",
                              success: function (data) {
                                 $('.lds-facebook').hide();
                                 $("#example2").html(data);
                              },
                           });
                           return false;
                        });
                     });
                  </script>
                  <!-- /.box-header -->
               </div>
               <div class="box-body" id="example2" style="padding-top:0px;">
                  <div class="container-fluid">
                     <?php echo $this->Form->create('Mysubscription', array('type' => 'file', 'inputDefaults' => array('div' => false, 'label' => false), 'id' => 'Mysubscriptions', 'class' => 'form-horizontal')); ?>
                     <div class="form-group row" style="display:flex; align-items: center; justify-content: flex-end">
                        <div class="col-md-3" style="text-align:right;">
                           <label for="inputEmail3" class="control-label"
                              style="text-align: right !important; padding-top:0px;">Company</label>
                        </div>

                        <div class="col-md-3">
                           <?php echo $this->Form->input('id', array('class' => 'form-control', 'type' => 'select', 'options' => $company, 'label' => false, 'empty' => 'Select Company', 'autofocus', 'autocomplete' => 'off')); ?>
                        </div>

                        <input type="submit" style="background-color:#00c0ef; color:#fff;" id="Mysubscriptions"
                           class="btn btn4 btn_pdf myscl-btn date" value="Search">
                     </div>
                  </div>
                  <table id="example14" class="table table-bordered table-striped">
                     <thead>
                        <tr>
                           <th width="4%">S.No.</th>
                           <th width="16%">Comapany Name</th>
                           <th width="10%">GST No.</th>
                           <th width="10%">Account No.</th>
                           <th width="8%">IFSC Code</th>
                           <th width="8%">Tin Date</th>
                           <th>Address</th>
                           <th width="8%">Action</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php $page = $this->request->params['paging']['']['page'];
                        $limit = $this->request->params['paging']['']['perPage'];
                        $counter = ($page * $limit) - $limit + 1;
                        if (isset($users) && !empty($users)) {
                           foreach ($users as $intusr) { //pr($intusr);
                              ?>
                              <tr>
                                 <td>
                                    <?php echo $counter; ?>
                                 </td>
                                 <td>
                                    <?php echo $intusr['cname']; ?>
                                 <td>
                                    <?php echo $intusr['gst']; ?>
                                 <td>
                                    <?php echo $intusr['accountno']; ?>
                                 <td>
                                    <?php echo $intusr['ifsc']; ?>
                                 <td>
                                    <?php echo date('Y-m-d', strtotime($intusr['tin_date'])); ?>
                                 </td>
                                 <td>
                                    <?php echo $intusr['address']; ?>
                                 <td>
                                    <strong>
                                       <?php
                                       $user_id = $_SESSION['Auth']['User']['id'];
                                       $controllerName = $this->request->params['controller'];
                                       $actionName = $this->request->params['action'];
                                       $user_permission = $this->comman->finduserpermisson($user_id, $controllerName, $actionName);

                                       if ($user_permission['edit'] == 1) {
                                          echo $this->Html->link('', [
                                             'action' => 'edit',
                                             $intusr->id,
                                          ], ['class' => 'fas fa-edit', 'style' => 'font-size: 16px !important;']);
                                       } ?>
                                       <!-- status  -->
                                       <?php
                                       if ($user_permission['delete'] == 1) {
                                          if ($intusr['status'] == 'Y') {
                                             echo $this->Html->link('', [
                                                'action' => 'status',
                                                $intusr->id,
                                                'Y'
                                             ], ['title' => 'Active', 'class' => 'fa fa-check-circle', 'style' => ' font-size: 16px !important;color: #36cb3c;']);
                                          } else {
                                             echo $this->Html->link('', [
                                                'action' => 'status',
                                                $intusr->id,
                                                'N'
                                             ], ['title' => 'Inactive', 'class' => 'fa fa-times-circle-o', 'style' => 'font-size: 16px !important;color:#FF5722;']);
                                          } ?>
                                          <!-- status end -->
                                          <?php

                                          echo $this->Html->link('', [
                                             'action' => 'delete',
                                             $intusr->id
                                          ], [
                                             'class' => 'fas fa-trash-alt',
                                             'style' => 'font-size: 16px !important;color:#c70909;',
                                             "onClick" => "javascript: return confirm('Are you sure do you want to delete this Company Record')"
                                          ]);
                                       } ?>
                                    </strong>
                                 </td>
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
<!-- content-wrapper -->
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
<script src="https://code.jquery.com/ui/1.11.0/jquery-ui.js"></script>
<script>
   $(function () {
      var dateFormat = 'dd-mm-yy',
         from = $("#datepicker1")
            .datepicker({
               dateFormat: 'dd-mm-yy',
               changeMonth: true,
               numberOfMonths: 1
            })
            .on("change", function () {
               to.datepicker("option", "minDate", getDate(this));
            }),
         to = $("#datepicker2").datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            numberOfMonths: 1
         })
            .on("change", function () {
               from.datepicker("option", "maxDate", getDate(this));
            });

      function getDate(element) {
         var date;
         try {
            date = $.datepicker.parseDate(dateFormat, element.value);
         } catch (error) {
            date = null;
         }
         return date;
      }
   });
</script>
<script>
   $(document).ready(function () {
      $('#sevice_form').on('submit', function (e) {
         $("#formsubmitbtn").css("display", "none");
      });
   });
</script>