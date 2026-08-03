<table class="table table-bordered table-striped">
   <thead>
      <tr>
         <th width="3%">S.No.</th>
         <th width="6%">Design Sheet No.</th>
         <th width="25%">Contract Name</th>
         <th width="34.25%">Type Of Cable</th>
         <th width="6%">Quantity(in KM)</th>
         <th width="6.75%">Issue Date</th>
         <th width="12%">Design Sheet</th>
         <th width="6%">Action</th>
      </tr>
   </thead>
   <tbody>
      <?php
      $role_permissions = $this->Permission->permissioncheck();
      $page = $this->request->params['paging']['Designsheet']['page'];
      $limit = $this->request->params['paging']['Designsheet']['perPage'];
      $counter = ($page * $limit) - $limit + 1;
      if (isset($designs) && !empty($designs)) {
         foreach ($designs as $intusr) {
            $contractname = $this->comman->findcontractname($intusr['contract_id']);
            $itemname = $this->Comman->getitemname($intusr['item_id']);
            ?>
            <tr>
               <td>
                  <?php echo $counter; ?>
               </td>
               <td><a href="<?php echo SITE_URL; ?>admin/designsheet/viewdesignsheet/<?php echo $intusr['designsheetno']; ?>"
                     class="designsheetdetails"><?php echo $intusr['designsheetno']; ?></a></td>
               <td><a href="<?php echo SITE_URL; ?>admin/production/viewcontractdetail/<?php echo $intusr['contract_id']; ?>"
                     class="viewdetails">
                     <?php echo $contractname['title'] . '(' . $contractname['workorder'] . ')'; ?>
                  </a></td>
               <td>
                  <?php echo $itemname['item_name']; ?>
               </td>
               <td>
                  <?php echo $intusr['quantity']; ?>
               </td>

               <td>
                  <?php echo date('d-m-Y', strtotime($intusr['datefrom'])); ?>
               </td>

               <td>
                  <?php if (!empty($intusr['design_sheet'])) { ?>
                     <a target="_blank" href="<?php echo SITE_URL . 'designsheet/' . $intusr['design_sheet']; ?>"
                        title="Design Sheet" data-method="post" data-toggle="tooltip"><span
                           class="fa fa-download fa-lg text-green"></span></a> &nbsp; &nbsp;
                     <?php $i = 1;
                     for ($i = 1; $i < 6; $i++) {
                        if ($intusr['r' . $i]) { ?>
                           <a target="_blank" href="<?php echo SITE_URL . 'designsheet/' . $intusr['r' . $i]; ?>"
                              title="R<?php echo $i ?>" data-method="post" data-toggle="tooltip"><span
                                 class="fa fa-download fa-lg text-green"></span></a> &nbsp; &nbsp; <?php } ?>
                     <?php }
                  } else {
                     echo '-';
                  } ?>
               </td>

               <td> <strong>
                     <?php
                     $user_id = $_SESSION['Auth']['User']['id'];
                     $controllerName = $this->request->params['controller'];
                     $actionName = "index";
                     $user_permission = $this->comman->finduserpermisson($user_id, $controllerName, $actionName);

                     $checkindentpo = $this->Comman->checkindentpo($intusr['contract_id'], $intusr['item_id']);
                     $fileurl = "admin/designsheet/edit";
                     if (in_array($fileurl, $role_permissions)) {
                        echo $this->Html->link('', [
                           'action' => 'edit',
                           $intusr->id,
                        ], ['class' => 'fas fa-edit', 'style' => 'font-size: 16px !important;']);
                     }
                     ?>

                     <?php
                     if (empty($checkindentpo)) {
                        $fileurl = "admin/designsheet/delete";
                        if (in_array($fileurl, $role_permissions)) {
                           echo $this->Html->link('', [
                              'action' => 'delete',
                              $intusr->id
                           ], [
                              'class' => 'fas fa-trash-alt',
                              'style' => 'font-size: 16px !important; color:#cd0404; margin-right:4px !important;',
                              "onClick" => "javascript: return confirm('Are you sure do you want to delete this Design Sheet')"
                           ]);
                        }
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
<?php echo $this->element('admin/pagination'); ?>
<script>
   $('.viewdetails').click(function (e) {
      e.preventDefault();
      $('#editsorts').modal('show').find('.modal-body').load($(this).attr('href'));
   });
</script>

<div class="modal fade" id="editsorts">
   <div class="modal-dialog" style="max-width:900px !important;">
      <div class="modal-content" style="background:white;">
         <div class="modal-body"></div>
      </div>
   </div>
</div>

<script>
   $('.designsheetdetails').click(function (e) {
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