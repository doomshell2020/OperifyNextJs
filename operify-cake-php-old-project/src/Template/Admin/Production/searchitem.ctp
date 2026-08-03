<table class="table table-bordered table-striped" width="100%">
  <thead>
    <?php $rolepresent = $this->request->session()->read('Auth.User.role_id');
    ?>
    <tr>
      <th rowspan="2" width="2%">No.</th>
      <th rowspan="2" width="6.75%">Date</th>
      <th rowspan="2" width="3.5%">PO No.</th>
      <th rowspan="2" width="12.5%">Contract Name</th>
      <th rowspan="2" width="12.5%">Machine Name</th>
      <th rowspan="2" width="13.5%">Product</th>
      <th rowspan="2" width="8%">Process Name</th>
      <th rowspan="2" width="3.5%">Plan Qty (KM)</th>
      <th colspan="3" width="12%">Production (KM.)</th>
      <th colspan="2" width="8%">Reading</th>
      <th rowspan="2" width="7%">Next Day 08.00 AM </th>
      <th rowspan="2" width="5.5%">Total Man Power</th>
      <?php
      $user_id = $_SESSION['Auth']['User']['id'];
      $role_id = $_SESSION['Auth']['User']['role_id'];

      $controllerName = $this->request->params['controller'];
      $actionName = "index";
      $user_permission = $this->comman->finduserpermisson($user_id, $controllerName, $actionName);

      //if ($user_permission['delete'] == '1' || $user_permission['edit'] == '1') { 
        if ($role_id == '105') {
        ?>
        <th rowspan="2" width="5.5%">Action</th>
      <?php } ?>
    </tr>
    <tr>
      <th style="text-align: center;" width="4%">Shift A</th>
      <th style="text-align: center;" width="4%">Shift B</th>
      <th style="text-align: center;" width="4%">Total</th>
      <th style="text-align: center;" width="4%">08.00 AM</th>
      <th style="text-align: center;" width="4%">08.00 PM</th>
    </tr>
  </thead>
  <tbody>
    <?php $page = $this->request->params['paging']['Production']['page'];
    $limit = $this->request->params['paging']['Production']['perPage'];
    $counter = ($page * $limit) - $limit + 1;
    if (isset($production_data) && !empty($production_data)) {
      foreach ($production_data as $intusr) {
        $itemname = $this->comman->getitemname($intusr['item_id']);
        $contractname = $this->comman->findcontractname($intusr['contract_id']);
        $processname = $this->comman->finishedproductprocess($intusr['productprocess_id']);
        ?>
        <tr>
          <td>
            <?php echo $counter; ?>.
          </td>
          <td>
            <?php echo date("d-m-Y", strtotime($intusr['production_date'])); ?>
          </td>
          <td>
            <a class="viewproductiondetails"
              href="<?php echo SITE_URL; ?>admin/production/viewproductiondetails/<?php echo $intusr['po_id']; ?>"><?php echo $intusr['po_id']; ?></a>
          </td>
          <td>
            <a href="<?php echo SITE_URL; ?>admin/production/viewcontractdetail/<?php echo $intusr['contract_id']; ?>"
              class="viewdetails">
              <?php echo $contractname['title'] . '(' . $contractname['workorder'] . ')'; ?>
            </a>
          </td>
          <td>
            <?php echo $intusr['machinemaster']['machine_name']; ?>
          </td>
          <td>
            <?php echo ucfirst($itemname['item_name']); ?>
          </td>
          <td>
            <?php echo $processname['process_name']; ?>
          </td>
          <td>
            <?php echo $intusr['plan_qty']; ?>
          </td>
          <td>
            <?php echo $intusr['production_shift_a']; ?>
          </td>
          <td>
            <?php echo $intusr['production_shift_b']; ?>
          </td>
          <td>
            <?php echo $intusr['production_shift_a'] + $intusr['production_shift_b']; ?>
          </td>
          <td>
            <?php echo $intusr['reading8am']; ?>
          </td>
          <td>
            <?php echo $intusr['reading8pm']; ?>
          </td>
          <td>
            <?php echo $intusr['nextday8am']; ?>
          </td>

          <td>
            <?php echo $intusr['manpower_night'] + $intusr['manpower_day']; ?>
          </td>

          <?php 
          //if ($user_permission['delete'] == '1' || $user_permission['edit'] == '1') { 
            if ($role_id == '105') {
            ?>
            <td>
              <?php
              // if (date('d-m-Y') == date("d-m-Y", strtotime($intusr['production_date']))) {
                if ($role_id == '105') {
                echo $this->Html->link('', [
                  'action' => 'deletedailysheet',
                  $intusr->id
                ], [
                  'class' => 'fas fa-trash-alt',
                  'style' => 'font-size: 16px !important; color:#cd0404; margin-right:4px !important;',
                  "onClick" => "javascript: return confirm('Are you sure for delete this Daily Sheet')"
                ]);
              }
              if ($role_id == '105') {
                echo $this->Html->link('', [
                  'action' => 'edit',
                  $intusr->id,
                ], ['class' => 'fas fa-edit', 'style' => 'font-size: 16px !important;']);
              }
              // } ?>
            </td>
          <?php } ?>
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
    <div class="modal-content">
      <div class="modal-body"></div>
    </div>
  </div>
</div>


<script>
  $('.viewproductiondetails').click(function (e) {
    e.preventDefault();
    $('#editproductionsorts').modal('show').find('.modal-body').load($(this).attr('href'));
  });
</script>

<div class="modal fade" id="editproductionsorts">
  <div class="modal-dialog" style="max-width:900px !important;">
    <div class="modal-content">
      <div class="modal-body"></div>
    </div>
  </div>
</div>