<table class="table table-bordered table-striped">
  <thead>
    <tr>
      <th>#</th>
      <th>Vendor Name</th>
      <th>Pan No</th>
      <th>Contact </th>
      <th>Email</th>
      <th>Contact Person</th>
      <th>Type</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    <?php $page = $this->request->params['paging']['Vendors']['page'];
    $limit = $this->request->params['paging']['Vendors']['perPage'];
    $counter = ($page * $limit) - $limit + 1;
    // pr($this->request->params);die;
    if (isset($vendors) && !empty($vendors)) {
      foreach ($vendors as $work) {

        ?>
        <tr>
          <td>
            <?php echo $counter; ?>
          </td>
          <td>
            <?php echo $work['name'] ?>
          </td>
          <td>
            <?php echo $work['pancard_number'] ?>
          </td>
          <td>
            <?php echo $work['contact_no'] ?>
          </td>
          <td>
            <?php echo $work['email'] ?>
          </td>
          <td>
            <?php echo $work['contact_person'] ?>
          </td>
          <td>
            <?php echo $work['type'] ?>
          </td>
          <td>
            <?php
           $role_permissions = $this->Permission->permissioncheck();
           $fileurl = "admin/vendors/add";
           if (in_array($fileurl, $role_permissions)) { 
              echo $this->Html->link('', [
                'action' => 'add',
                $work->id
              ], ['class' => 'fas fa-edit', 'style' => 'font-size: 18px;']);
            } ?>
            <?php
             $fileurl = "admin/vendors/delete";
             if (in_array($fileurl, $role_permissions)) { 
              echo $this->Html->link('', [
                'action' => 'delete',
                $work->id
              ], ['class' => 'fas fa-trash-alt', 'style' => 'font-size: 18px; color:#c12020;', "onClick" => "javascript: return confirm('Are you sure you want to delete this Supplier ?')"]);
            } ?>
          </td>

        </tr>
        <?php $counter++;
      }
    } else { ?>
      <tr>
        <td>NO Data Available</td>
      </tr>
    <?php } ?>
  </tbody>

</table>
<?php echo $this->element('admin/pagination'); ?>