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
               <td width="10%">
                  <?php echo ucfirst(strtolower($value['remark'])); ?>
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
                     echo $this->Form->input('maintenancestatus', array('class' => 'form-control mstatus', 'type' => 'select', 'options' => $options, 'label' => false, 'data-id' => $value['id'], 'value' => $value['maintenance_status'])); ?>
                  </strong>
               </td>
            </tr>
            <?php $counter++;
         }
      } ?>
   </tbody>
</table>
<?php echo $this->element('admin/pagination'); ?>

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

<!-- modal call when status complete   -->
<div id="completeModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="completeModalLabel"
   aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title" id="completeModalLabel">Complete Maintenance</h4>
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
         </div>
         <div class="modal-body">
            <?php echo $this->Form->create(null, ['url' => ['action' => 'completeMaintenance']]); ?>
            <?php // echo $this->Form->hidden('maintenance_id', ['value' => $maintenance_id]); ?>

            <div class="form-group">
               <?php echo $this->Form->input('maintenance_id', [
                  'type' => 'text',
                  'class' => 'form-control',
                  'id' => 'maintenance_id',
                  'hidden' => true,
               ]); ?>
            </div>


            <div class="form-group">
               <?php echo $this->Form->input('completion_date', [
                  'type' => 'text',
                  'class' => 'form-control',
                  'id' => 'complete_date',
                  'readonly' => true,
               ]); ?>
            </div>
            <div class="form-group">
               <?php echo $this->Form->input('completion_time', [
                  'type' => 'text',
                  'class' => 'form-control',
                  'id' => 'complete_time',
                  'label' => 'Completion Time',
                  'placeholder' => 'Enter completion time',
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