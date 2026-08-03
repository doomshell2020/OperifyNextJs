<table class="table table-bordered table-striped" width="100%">
  <thead>
    <tr>
      <th width="4%">S.No.</th>
      <th width="6%">Unique Id</th>
      <th width="45%">Item Name</th>
      <th width="15%">Category</th>
      <th width="8%">Item Type</th>
      <th width="8%">UOM</th>
      <!-- <th width="8%">Tax(%)</th> -->
      <th width="10%">Current Stock</th>
      <th width="10%">Action</th>
    </tr>
  </thead>
  <tbody>
    <?php $page = $this->request->params['paging']['Additem']['page'];
    $limit = $this->request->params['paging']['Additem']['perPage'];
    $counter = ($page * $limit) - $limit + 1;
    if (isset($users) && !empty($users)) {
      foreach ($users as $intusr) {
        $InhandStock = $this->Comman->InhandStock($intusr['id']);
    ?>
        <tr>
          <td>
            <?php echo $counter; ?>
          </td>
          <td>
            <?php echo $intusr['id']; ?>
          </td>
          <td>
            <?php
            if ($intusr['sizemanager']['size_name']) {
              echo ucfirst($intusr['item_name'] . '(' . $intusr['sizemanager']['size_name'] . ')');
            } else {
              echo ucfirst($intusr['item_name']);
            }
            ?>
          </td>
          <td>
            <?php echo Ucfirst($intusr['itemcategory']['category_name']); ?>
          </td>
          <td>
            <?php echo $intusr['itemtype']; ?>
          </td>
          <td>
            <?php echo $intusr['measurementunit']['unit_name']; ?>
          </td>
          <!-- <td>
            <?php
            if ($intusr['taxmaster']['tax']) {
              echo $intusr['taxmaster']['tax'] . '%';
            } else {
              echo "N/A";
            }
            ?>
          </td> -->
          <td>
            <?php echo $InhandStock ? $InhandStock : 0; ?>
          </td>
          <td> <strong>
              <?php
              $role_permissions = $this->Permission->permissioncheck();
              $fileurl = "admin/additem/edit";
              if (in_array($fileurl, $role_permissions)) {
                echo $this->Html->link('', [
                  'action' => 'edit',
                  $intusr->id,
                ], ['class' => 'fas fa-edit', 'style' => 'font-size: 16px !important;']);
              } ?>
              &nbsp;
              <?php
              $fileurl = "admin/additem/delete";
              if (in_array($fileurl, $role_permissions)) {
                echo $this->Html->link('', [
                  'action' => 'delete',
                  $intusr->id
                ], [
                  'class' => 'fas fa-trash-alt',
                  'style' => 'font-size: 16px !important; color:#cd0404; margin-right:4px !important;',
                  "onClick" => "javascript: return confirm('Are you sure do you want to delete this Item')"
                ]);
              } ?>
              <?php if ($intusr['status'] == 'Y') {
                echo $this->Html->link('', [
                  'action' => 'status',
                  $intusr->id,
                  'Y'
                ], ['title' => 'Active', 'class' => 'fas fa-check-circle', 'style' => 'font-size: 16px !important; margin-left: 12px;     color: #36cb3c;']);
              } else {
                echo $this->Html->link('', [
                  'action' => 'status',
                  $intusr->id,
                  'N'
                ], ['title' => 'Inactive', 'class' => 'fas fa-times-circle', 'style' => 'font-size: 16px !important; margin-left: 12px; color:#cd0404;']);
              }  ?>
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