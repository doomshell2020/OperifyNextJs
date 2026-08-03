<table class="table table-bordered table-striped">
  <thead>
    <tr>
      <th>S.No.</th>
      <th>Comapany Name</th>
      <th>GST No.</th>
      <th>Account No.</th>
      <th>IFSC Code</th>
      <th>Tin Date</th>

      <th>Address</th>
      <th>Action</th>
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
              $actionName = "index";
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