<table class="table table-bordered table-striped" width="100%">
   <thead>
      <tr>
         <th width="3%">S.No.</th>
         <th width="15%">Title</th>
         <th width="12%">Supplier Name</th>
         <th width="8%">Cost</th>
         <th width="8%">Issue Date</th>
         <th width="8%">Start Date</th>
         <th width="8%">End Date</th>
         <th width="30%">Description</th>
         <th width="8%">Action</th>
      </tr>
   </thead>
   <tbody>
      <?php $page = $this->request->params['paging']['Contracts']['page'];
      $limit = $this->request->params['paging']['Contracts']['perPage'];
      $counter = ($page * $limit) - $limit + 1;
      if (isset($users) && !empty($users)) {
         foreach ($users as $intusr) {
            $var = $this->Comman->findvendornames($intusr['supplier_id']);
            $designsheetid = $this->Comman->checkdesignsheet($intusr['id']);
      ?>
            <tr>
               <td>
                  <?php echo $counter; ?>.
               </td>
               <td>
                  <a href="<?php echo SITE_URL; ?>admin/production/viewcontractdetail/<?php echo $intusr['id']; ?>"
                     class="viewdetails">
                     <?php echo $intusr['title'] . '(' . $intusr['workorder'] . ')'; ?>
                  </a>

                  <!-- <a style="color: red;" target="_blank"
                     href="<?php // echo SITE_URL; 
                           ?>admin/contracts/viewcontractdetail/<?php // echo $intusr['id']; 
                                                                                          ?>">view</a> -->
               </td>
               <td>
                  <?php echo $var['name']; ?>
               </td>
               <td style="text-align:right;">
                  <?php echo number_format($intusr['cost']); ?>
               </td>
               <td>
                  <?php echo date('d-m-Y', strtotime($intusr['issuedate'])); ?>
               </td>
               <td>
                  <?php echo date('d-m-Y', strtotime($intusr['contract_start_date'])); ?>
               </td>
               <td>
                  <?php echo date('d-m-Y', strtotime($intusr['contract_end_date'])); ?>
               </td>
               <td>
                  <?php
                  $description = h($intusr['description']);
                  $wordArray = explode(' ', strip_tags($description));
                  $wordCount = count($wordArray);

                  $firstPart = implode(' ', array_slice($wordArray, 0, 20));
                  $remainingPart = implode(' ', array_slice($wordArray, 20));
                  ?>

                  <span>
                     <?php echo $firstPart; ?>
                  </span>

                  <?php if ($wordCount > 20): ?>
                     <span id="more-<?php echo $intusr['id']; ?>" style="display: none;">
                        <?php echo ' ' . $remainingPart; ?>
                     </span>

                     <a href="javascript:void(0);" onclick="toggleMessage(<?php echo $intusr['id']; ?>, this)">
                        View more
                     </a>
                  <?php endif; ?>
               </td>

               <script>
                  function toggleMessage(id, linkElement) {
                     const moreText = document.getElementById(`more-${id}`);

                     if (moreText.style.display === "none") {
                        moreText.style.display = "inline";
                        linkElement.textContent = "View less";
                     } else {
                        moreText.style.display = "none";
                        linkElement.textContent = "View more";
                     }
                  }
               </script>

               <td> <strong>
                     <?php

                     if ($designsheetid == '') {

                        $role_permissions = $this->Permission->permissioncheck();
                        $fileurl = "admin/contracts/edit";
                        if (in_array($fileurl, $role_permissions)) {
                           echo $this->Html->link('', [
                              'action' => 'edit',
                              $intusr->id,
                           ], ['class' => 'fas fa-edit', 'style' => 'font-size: 16px !important;']);
                        }

                        $role_permissions = $this->Permission->permissioncheck();
                        $fileurl = "admin/contracts/delete";
                        if (in_array($fileurl, $role_permissions)) {
                           echo $this->Html->link('', [
                              'action' => 'delete',
                              $intusr->id
                           ], [
                              'class' => 'fas fa-trash-alt',
                              'style' => 'font-size: 16px !important; color:#cd0404; margin-right:4px !important;',
                              "onClick" => "javascript: return confirm('Are you sure do you want to delete this Contract')"
                           ]);
                        }
                     }
                     ?>

                     <a title="Download Contract PDF" class="fa fa-download fa-lg text-green"
                        href="<?php echo ADMIN_URL; ?>production/viewcontractdetailspdf/<?php echo $intusr['id']; ?>"
                        download="NewName.pdf"></a>

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
   $('.viewdetails').click(function(e) {
      e.preventDefault();
      $('#editsorts').modal('show').find('.modal-body').load($(this).attr('href'));
   });
</script>

<div class="modal fade" id="editsorts">
   <div class="modal-dialog" style="max-width:900px !important;">
      <div class="modal-content">
         <div class="modal-body" style="background:white;"></div>
      </div>
   </div>
</div>