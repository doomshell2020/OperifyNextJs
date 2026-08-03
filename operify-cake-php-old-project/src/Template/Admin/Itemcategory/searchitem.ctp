<table class="table table-bordered table-striped">
  <thead>
    <tr>
      <th width="10%">S.No.</th>
      <th width="40%">Item Category Name</th>
      <th width="40%">Description</th>
      <th width="10%">Action</th>
    </tr>
  </thead>

  <tbody>
    <?php $page = $this->request->params['paging']['']['page'];
    $limit = $this->request->params['paging']['']['perPage'];
    $counter = ($page * $limit) - $limit + 1;
    if (isset($user) && !empty($user)) {
      foreach ($user as $intusr) { //pr($intusr);
    ?>
        <tr>
          <td><?php echo $counter; ?></td>

          <td>
            <?php
            echo ucfirst($intusr['category_name']);
            ?>

          </td>
          <td>
            <?php echo $intusr['description']; ?>
          </td>
      <td>
                                    <?php  $role_permissions = $this->Permission->permissioncheck();

                                    $fileurl = "admin/itemcategory/edit";
                                    if (in_array($fileurl, $role_permissions)) {

                                       echo $this->Html->link('', [
                                          'action' => 'edit',
                                          $intusr->id,
                                       ], ['class' => 'fas fa-edit', 'style' => 'font-size: 16px !important;']);
                                    } ?>
                                    &nbsp;
                                    <?php if ($intusr['status'] == 'Y') {
                                       echo $this->Html->link('', [
                                          'action' => 'status',
                                          $intusr->id,
                                          'Y'
                                       ], ['title' => 'Active', 'class' => 'fas fa-check-circle', 'style' => 'font-size: 16px !important; color: #36cb3c;']);
                                    } else {
                                       echo $this->Html->link('', [
                                          'action' => 'status',
                                          $intusr->id,
                                          'N'
                                       ], ['title' => 'Inactive', 'class' => 'fas fa-times-circle', 'style' => 'font-size: 16px !important; color:#cd0404;']);
                                    }  ?>
                                    &nbsp;
                                    <?php
                                    $fileurl = "admin/itemcategory/delete";
                                    if (in_array($fileurl, $role_permissions)) {
                                       echo $this->Html->link('', [
                                          'action' => 'delete',
                                          $intusr->id
                                       ], [
                                          'class' => 'fas fa-trash-alt',
                                          'style' => 'font-size: 16px !important; color:#cd0404;',
                                          "onClick" => "javascript: return confirm('Are you sure do you want to delete this Item Category')"
                                       ]);
                                    } ?>&nbsp;
                                    <?php if ($intusr['is_print'] == 'Y') {
                                       echo $this->Html->link('', [
                                          'action' => 'printstatus',
                                          $intusr->id,
                                          'Y'
                                       ], ['title' => 'Print Available', 'class' => 'fa fa-print', 'style' => 'font-size: 16px !important; color: #36cb3c;']);
                                    } else {
                                       echo $this->Html->link('', [
                                          'action' => 'printstatus',
                                          $intusr->id,
                                          'N'
                                       ], ['title' => 'Print Not Available', 'class' => 'fa fa-print', 'style' => 'font-size: 16px !important; color: #cd0404;']);
                                    }  ?>


                                 </td>


        </tr>
      <?php $counter++;
      }
    } else { ?>


    <?php } ?>
  </tbody>

</table>