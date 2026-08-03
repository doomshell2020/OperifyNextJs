<table class="table table-bordered table-striped" width="100%">
  <thead>
    <tr>
      <th width="08%">Date</th>
      <th width="40%">Description</th>
      <th width="15%">Credit Amount</th>
      <th width="15%">Debit Amount</th>
      <th width="15%">Balance</th>
      <th width="07%">Action</th>
    </tr>
  </thead>
  <tbody>
    <?php $page = $this->request->params['paging']['payments']['page'];
    $limit = $this->request->params['paging']['payments']['perPage'];
    $counter = ($page * $limit) - $limit + 1;
    if (isset($payments) && !empty($payments)) {
      $curbalance = $this->Comman->getvendorbalance($payments[0]['vendor_id'], $date);
      foreach ($payments as $intusr) {
        $vendor_id = $this->Comman->findvendornames($intusr['vendor_id']);
        if ($intusr['store_type'] == '1') {
          $curbalance = $curbalance + $intusr['total_amt'];
        } else {
          $curbalance = $curbalance - $intusr['total_amt'];
        }
        ?>
        <tr>
          <td>
            <?php
            echo date("d-m-Y", strtotime($intusr['bill_date']));
            ?>
          </td>
          <td>
            <?php
            if ($intusr['store_type'] == '1') {
              echo 'Bill No. ' . $intusr['bill_no'] . ' With <br> ' . $intusr['remark'];
            } else {
              echo 'Recipt No. ' . $intusr['receipt_no'] . ' With <br> ' . $intusr['remark'];
            }
            ?>
          </td>
          <td style="text-align:right;">
            <?php
            if ($intusr['store_type'] == '1') {
              echo number_format((float) $intusr['total_amt'], 2, '.', '');
            } else {
              echo '-';
            }
            ?>
          </td>
          <td style="text-align:right;">
            <?php
            if ($intusr['store_type'] == '2') {
              echo number_format((float) $intusr['total_amt'], 2, '.', '');
            } else {
              echo '-';
            }
            ?>
          </td>
          <td style="text-align:right;">
            <?php
            echo number_format((float) $curbalance, 2, '.', '');
            ?>
          </td>
          <td>
            <?php
            if ($intusr['store_type'] == 2) {
              $user_id = $_SESSION['Auth']['User']['id'];
              $controllerName = $this->request->params['controller'];
              $actionName = "index";
              $user_permission = $this->comman->finduserpermisson($user_id, $controllerName, $actionName);

              if ($user_permission['delete'] == 1) {
                echo $this->Html->link('', [
                  'action' => 'delete',
                  $intusr->id
                ], [
                  'class' => 'fas fa-trash-alt',
                  'style' => 'font-size: 16px !important; color:#cd0404; margin-right: !important;',
                  "onClick" => "javascript: return confirm('Are you sure do you want to delete this Payment')"
                ]);
              }
            }
            ?>
          </td>
        </tr>
        <?php $counter++;
      } ?>
      <tr>
        <th style="text-align:right;" colspan="4">Closing Balance</th>
        <th style="text-align:right;">
          <?php echo number_format((float) $curbalance, 2, '.', ''); ?>
        </th>
        <td></td>
      </tr>
    <?php } else { ?>
      <tr>
        <th style="text-align:center;" colspan="6">No Data Found</th>
      </tr>
    <?php } ?>
  </tbody>
</table>
<?php echo $this->element('admin/pagination'); ?>