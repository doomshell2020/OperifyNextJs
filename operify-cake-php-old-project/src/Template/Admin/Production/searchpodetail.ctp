<table id="" class="table table-bordered table-striped" width="100%">
  <thead>
    <tr>
      <th width="05%">PO NO.</th>
      <th width="06%">Date Created</th>
      <th width="17%">Contract Name</th>
      <th width="25%">Product</th>
      <th width="06%">Start Date</th>
      <th width="06%">End Date</th>
      <th width="06%">Planned Qty</th>
      <th width="06%">Prepared Qty</th>
      <th width="08%">Action</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $role_permissions = $this->Permission->permissioncheck();
    foreach ($productionorder as $detail) {
      $contractname = $this->comman->findcontractname($detail['contract_id']);
      $itemname = $this->comman->getitemname($detail['item_id']);
      $checkdailysheet = $this->comman->checkdailysheet($detail['po_id'], 8);
      $checkdproductionstart = $this->comman->checkdproductionstart($detail['po_id']);
      $quantity = '';
      foreach ($checkdailysheet as $value) {
        $quantity += $value['production_shift_a'] + $value['production_shift_b'];
        $completedate = date('d-m-Y', strtotime($value['production_date']));
      }
      $status = '';
      if ($quantity >= $detail['plannedqty']) {
        $status = 'C';
      }

    ?>

      <tr>
        <td>
          <a class="viewproductiondetails"
            href="<?php echo SITE_URL; ?>admin/production/viewproductiondetails/<?php echo $detail['po_id']; ?>"><?php echo $detail['po_id']; ?></a>
        </td>

        <td>
          <?php echo date('d-m-Y', strtotime($detail['issuedate'])); ?>
        </td>
        <td><a href="<?php echo SITE_URL; ?>admin/production/viewcontractdetail/<?php echo $detail['contract_id']; ?>"
            class="viewdetails">
            <?php echo $contractname['title'] . '(' . $contractname['workorder'] . ')'; ?>
          </a></td>
        <td>
          <?php echo $itemname['item_name']; ?>
        </td>
        <td>
          <?php echo date('d-m-Y', strtotime($detail['startdate'])); ?>
        </td>
        <td>
          <?php
          if ($detail['status'] == 'C') {
            echo date('d-m-Y', strtotime($detail['enddate'])) . '/<br>' .  date('d-m-Y', strtotime($detail['complete_date']));
          } else {
            echo date('d-m-Y', strtotime($detail['enddate']));
          } ?>
        </td>
        <td>
          <?php echo sprintf('%.2f', $detail['plannedqty']); ?>
        </td>
        <td>
          <?php echo ($detail['status'] == 'C') ? (sprintf('%.2f', $detail['plannedqty'])) : '0.00'; ?>
        </td>
        <td>
          <strong>
            <?php
            $user_id = $_SESSION['Auth']['User']['id'];
            $controllerName = $this->request->params['controller'];
            $actionName = $this->request->params['action'];
            $user_permission = $this->comman->finduserpermisson($user_id, $controllerName, $actionName);

            $fileurl = "admin/production/status";
            if (in_array($fileurl, $role_permissions)) {
              if ($detail['status'] == 'C') {
                echo $this->Html->link(
                  '',
                  ['action' => 'status', $detail->id, 'O'],
                  [
                    'class' => 'fa fa-check-circle',
                    'style' => 'font-size: 20px !important; color:red;margin-right:4px !important;',
                    "onClick" => "javascript: return confirm('Are you sure do you want to Open this Production Order')"
                  ]
                );
              } else {
                echo $this->Html->link('', [
                  'action' => 'status',
                  $detail->id,
                  'C'
                ], [
                  'class' => 'fa fa-check-circle',
                  'style' => 'font-size: 20px !important; color:green; margin-right:4px !important;',
                  "onClick" => "javascript: return confirm('Are you sure do you want to close this Production Order')"
                ]);
              }
            }
            $fileurl = "admin/production/delete";
            if (in_array($fileurl, $role_permissions)) {
              if (empty($checkdproductionstart) && $detail['status'] = 'C') {
                echo $this->Html->link('', [
                  'action' => 'delete',
                  $detail->id
                ], [
                  'class' => 'fas fa-trash-alt',
                  'style' => 'font-size: 16px !important; color:#cd0404; margin-right:4px !important;',
                  "onClick" => "javascript: return confirm('Are you sure do you want to delete this Production Order')"
                ]);
              }
            }
            ?>
          </strong>
        </td>
      </tr>
    <?php } ?>
  </tbody>
</table>