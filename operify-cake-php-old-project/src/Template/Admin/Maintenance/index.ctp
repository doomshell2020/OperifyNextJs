<style>
   .input_fields_wrap .form-control {
      margin-bottom: 15px;
   }
</style>
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

   .dataTables_wrapper.form-inline.dt-bootstrap.no-footer {
      margin-top: 0px;
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
         Maintenance Manager
      </h1>
      <ol class="breadcrumb">
         <li><a href="#"><i class="fa fa-home"></i>Home</a></li>
         <li><a href="#">Maintenance Manager</a></li>
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
                  <?php echo $this->Form->create('', array('type' => 'file', 'inputDefaults' => array('div' => false, 'label' => false), 'id' => 'mysubscription', 'class' => 'form-horizontal', 'style' => 'margin-bottom:0px;')); ?>
                  <div class="form-group" style="margin-bottom:15px;">
                     <div class="row">
                        <div class="col">
                           <label for="inputEmail3" class=" control-label" style="text-align: left !important">Machine
                              Name</label>
                           <input type="hidden" name="machines_id" id="retail_ids">
                           <?php echo $this->Form->input('machine_id', array('class' => 'form-control secrh-retail', 'id' => 'itemname', 'type' => 'text', 'label' => false, 'autofocus', 'autocompleted' => 'off', 'placeholder' => 'Enter Machine Name')); ?>
                           <div id="testUL" style="display:none;">
                              <ul></ul>
                           </div>
                           <div id="testUL1" style="display:none;">
                              <ul>
                                 <li
                                    style="padding: 5px 8px;list-style:none;color: black;font-weight: bold;margin-left:-32px; border: 1px solid lightgray;">
                                    No Record Found</li>
                              </ul>
                           </div>
                        </div>

                        <div class="col">
                           <label for="inputEmail3" class="control-label">Assigned To</label>
                           <?php echo $this->Form->input('assigned_to', array('class' => 'form-control', 'type' => 'text', 'autocompleted' => 'off', 'placeholder' => 'Assigned To', 'label' => false)); ?>
                        </div>
                        <div class="col">
                           <label for="inputEmail3" class="control-label">Status</label>
                           <?php $options = ['pending' => 'Pending', 'assigned' => 'Assigned', 'completed' => 'Completed'] ?>

                           <?php echo $this->Form->input('m_status', array('class' => 'form-control', 'type' => 'select', 'autocompleted' => 'off', 'placeholder' => 'Assigned To', 'empty' => '---- Select ----', 'options' => $options, 'label' => false)); ?>
                        </div>

                        <div class="col">
                           <script>
                              $(document).ready(function () {
                                 $('#fdatefrom').datepicker({
                                    dateFormat: 'dd-mm-yy',
                                    yearRange: '2018:2030',
                                    changeMonth: true,
                                    changeYear: true,
                                 });
                                 $('#fendfrom').datepicker({
                                    dateFormat: 'dd-mm-yy',
                                    yearRange: '2018:2030',
                                    changeMonth: true,
                                    changeYear: true
                                 });
                              });
                           </script>
                           <label for="inputEmail3" class="control-label">Start Date</label>
                           <?php echo $this->Form->input('datefrom', array('class' => 'form-control', 'id' => 'fdatefrom', 'readonly', 'placeholder' => 'Start Date', 'label' => false)); ?>
                        </div>
                        <div class="col">
                           <label for="inputEmail3" class="control-label">End Date</label>
                           <?php echo $this->Form->input('dateto', array('class' => 'form-control', 'id' => 'fendfrom', 'readonly', 'placeholder' => 'End Date', 'label' => false)); ?>
                        </div>

                        <!-- <div class="col">
                           <input type="submit" style="background-color:#00c0ef; color:#fff;width:100px !important;margin-top:25px;" class="btn btn4 btn_pdf myscl-btn date" value="Search">
                           <a style="font-size: 20px; margin-top:25px!important;" target="_blank" href="<?php echo ADMIN_URL; ?>maintenance/viewpdf"><i class="fa fa-file-pdf-o" style="font-size: 20px;"></i></a>
                        </div> -->

                        <div class="col-sm-2">
                           <input type="submit"
                              style="background-color:#00c0ef; !important; margin-top: 23px; color:#fff; padding:6px 18px;"
                              class="btn btn4 btn_pdf myscl-btn date" value="Search">

                           <a href="<?php echo SITE_URL; ?>admin/maintenance/index" class="excelbtn btn"
                              style="background-color:#00c0ef; !important; margin-top: 23px; color:#fff; padding:6px 18px;">Reset</a>


                        </div>
                        <div class="col">
                           <a href="<?php echo ADMIN_URL; ?>maintenance/add" class="excelbtn btn pull-right"
                              style="background-color:#2b89e9; !important; margin-top: 23px; color:#fff;padding:6px 18px;border-radius: 4px;"><i
                                 class="fa fa-plus"></i>&nbsp;Add</a>

                           <a href="<?php echo ADMIN_URL; ?>maintenance/viewpdf" target="_blank"
                              class="excelbtn btn pull-right" style="padding:0;margin-top: 23px;margin-right: 5px;"><i
                                 class="fa fa-file-pdf-o" style="font-size:28px;"></i></a>
                        </div>
                     </div>
                     <?php echo $this->Form->end(); ?>



                  </div>
                  <!-- /.box-header -->
                  <div id="load2" style="display:none;"></div>
                  <div class="box-body" style="padding-top:0px;" id="example23">
                     <div class="alert alert-success alert-dismissible" style="display:none;" role="alert"> <button
                           type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                              aria-hidden="true">&times;</span></button>Maintenance Status Updated Successfully.</div>

                     <table class="table table-bordered table-striped" width="100%">
                        <thead>
                           <tr>
                              <th width="3%">No.</th>
                              <th width="6%">Date</th>
                              <th width="10%">Machine Name</th>
                              <th width="8%">Type Of Breakdown</th>
                              <th width="2%">Time(Hrs)</th>
                              <th width="7%">Assigned To</th>
                              <th width="7%">Shift Incharge</th>
                              <th width="7%">Maintenance Incharge</th>
                              <th width="7%">Production Incharge</th>
                              <th width="10%">Remark</th>
                              <th width="10%">Action</th>
                           </tr>
                        </thead>
                        <tbody>
                           <?php $page = $this->request->params['paging']['Maintenance']['page'];
                           $limit = $this->request->params['paging']['Maintenance']['perPage'];
                           $counter = ($page * $limit) - $limit + 1;
                           if (isset($data) && !empty($data)) {
                              foreach ($data as $value) {
                                 // if ($value['maintenance_status'] == 'pending') {
                           
                                 ?>
                                 <tr>
                                    <td width="3%">
                                       <?php echo $counter; ?>
                                    </td>
                                    <td width="6%">
                                       <?php echo date("d-m-Y", strtotime($value['datefrom'])); ?>
                                    </td>
                                    <td width="10%">
                                       <?php echo $value['machinemaster']['machine_name']; ?>
                                    </td>
                                    <td width="8%">
                                       <?php echo ucfirst(strtolower($value['breakdown_type'])); ?>
                                    </td>
                                    <td width="2%">
                                       <?php echo $value['total_time']; ?>
                                    </td>
                                    <td width="7%">
                                       <?php
                                       $assigned_to = $this->comman->getuser($value['assigned_to']);
                                       echo ucfirst(strtolower($assigned_to['user_name']));
                                       // echo ucfirst(strtolower($value['assigned_to'])); 
                                       ?>
                                    </td>
                                    <td width="7%">
                                       <?php $assigned_to = $this->comman->getuser($value['shift_incharge']);
                                       echo ucfirst(strtolower($assigned_to['user_name']));
                                       ?>
                                    </td>
                                    <td width="7%">
                                       <?php $assigned_to = $this->comman->getuser($value['maintenance_incharge']);
                                       echo ucfirst(strtolower($assigned_to['user_name']));
                                       ?>

                                    </td>
                                    <td width="7%">
                                       <?php $assigned_to = $this->comman->getuser($value['production_head']);
                                       echo ucfirst(strtolower($assigned_to['user_name']));
                                       ?>

                                    </td>
                                    <td width="7%">

                                       <?php echo ucfirst(strtolower($value['remark'])); ?>
                                    </td>
                                    </td>
                                    <td width="10%"> <strong>
                                          <?php
                                          $user_id = $_SESSION['Auth']['User']['id'];
                                          $controllerName = $this->request->params['controller'];
                                          $actionName = $this->request->params['action'];
                                          $user_permission = $this->comman->finduserpermisson($user_id, $controllerName, $actionName);

                                          if ($user_permission['edit'] == 1) {
                                             echo $this->Html->link('', [
                                                'action' => 'edit',
                                                $value->id,
                                             ], ['class' => 'fas fa-edit', 'style' => 'font-size: 16px !important;']);
                                          } ?>
                                          <?php
                                          if ($user_permission['delete'] == 1) {
                                             echo $this->Html->link('', [
                                                'action' => 'status',
                                                $value->id,
                                                'N'
                                             ], [
                                                'class' => 'fas fa-trash-alt ',
                                                'style' => 'font-size: 16px !important; color:#cd0404; margin-right:4px !important;',
                                                "onClick" => "javascript: return confirm('Are you sure do you want to delete this Maintenance details')"
                                             ]);
                                          }
                                          ?>

                                          <?php
                                          $options = ['pending' => 'Pending', 'assigned' => 'Assigned', 'completed' => 'Completed'];
                                          echo $this->Form->input('maintenancestatus', array('class' => 'form-control mstatus', 'type' => 'select', 'options' => $options, 'label' => false, 'data-id' => $value['id'], 'value' => $value['maintenance_status']));
                              }
                              ;
                              ?>
                                    </strong>
                                 </td>
                              </tr>
                              <?php $counter++;
                           }
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
<!-- content-wrapper -->





<script>
   function cllbckretail0(id, cid, sid) {
      $('.secrh-retail').val(id);
      $('#retail_ids').val(cid);
      $('#testUL').hide();
      $('#testUL1').hide();
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
               url: '<?php echo ADMIN_URL; ?>production/getname',
               data: {
                  'fetch': pos,
                  'check': check
               },
               success: function (data) {
                  if (data) {
                     console.log(data);
                     $('#testUL ul').html(data);
                  } else {
                     $('#testUL').hide();
                     $('#testUL1').show();
                  }
               },
            });
         } else {
            $('#testUL').hide();
            $('#testUL1').hide();
         }
      });
   });
</script>


<script>
   $(document).ready(function () {
      $("#mysubscription").bind("submit", function (event) {
         $.ajax({
            async: true,
            data: $("#mysubscription").serialize(),
            dataType: "html",
            type: "get",
            url: "<?php echo ADMIN_URL; ?>maintenance/searchitem",


            beforeSend: function (xhr) {
               xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
               $('#load2').css("display", "block"); // Show loader
            },
            success: function (data) {
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
         var res = target.replace("/maintenance/searchitem", "/maintenance");
         window.location = res;
         return false;
      });
   });
</script>



<script>
   $(document).ready(function () {
      $(".mstatus").change(function () {
         var maintenanceId = $(this).attr("data-id");
         var status = $(this).val();

         if (status === 'completed') {
            var currentDate = new Date().toISOString().split('T')[0];
            $('#complete_date').val(currentDate);

            $('#maintenance_id').val(maintenanceId);

            $('#completeModal').modal('show');

         } else {
            $.ajax({
               async: true,
               data: {
                  'status': status,
                  'id': maintenanceId
               },
               dataType: "html",
               type: "POST",
               url: "<?php echo ADMIN_URL; ?>maintenance/maintenancestatus",

               success: function (data) {
                  location.reload();
                  alert(data)
                  $('.alert-success').text(data).show();
                  setTimeout(function () {
                     $('.alert-success').hide();
                  }, 100000);
               },
            });

         }

      });
   });
</script>



<!-- Modal for Completion -->
<div id="completeModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="completeModalLabel"
   aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title" id="completeModalLabel">Complete Maintenance</h4>
            <button type="button" id="closemdd" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
         </div>
         <div class="modal-body">
            <!-- Form inside modal -->
            <?php echo $this->Form->create(null, ['url' => ['action' => 'completeMaintenance']]); ?>
            <!-- <?php echo $this->Form->hidden('maintenance_id', ['value' => $maintenance_id]); ?> -->

            <div class="form-group">
               <!-- <label for="complete_date">Completion Date</label> -->
               <?php echo $this->Form->input('maintenance_id', [
                  'type' => 'text',
                  'class' => 'form-control',
                  'id' => 'maintenance_id',
                  'hidden' => true, // Make it read-only
               ]); ?>
            </div>


            <div class="form-group">
               <!-- <label for="complete_date">Completion Date</label> -->
               <?php echo $this->Form->input('completion_date', [
                  'type' => 'text',
                  'class' => 'form-control',
                  'id' => 'complete_date',
                  'readonly' => true, // Make it read-only
               ]); ?>
            </div>
            <div class="form-group">
               <!-- Completion Time Field -->
               <?php echo $this->Form->input('completion_time', [
                  'type' => 'text', // Simple text input
                  'class' => 'form-control',
                  'id' => 'complete_time',
                  'label' => 'Completion Time',
                  'placeholder' => 'Enter completion time', // Optional placeholder
               ]); ?>
            </div>


            <div class="form-group">
               <label for="remarks">Remarks</label>
               <?php echo $this->Form->textarea('remarks', [
                  'class' => 'form-control',
                  'id' => 'remarks',
                  'placeholder' => 'Enter remarks',
                  'required' => true
               ]); ?>
            </div>
            <br>
            <div class="form-group">
               <?php echo $this->Form->button('Submit', ['class' => 'btn btn-primary']); ?>
            </div>

            <?php echo $this->Form->end(); ?>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
         </div>
      </div>
   </div>
</div>