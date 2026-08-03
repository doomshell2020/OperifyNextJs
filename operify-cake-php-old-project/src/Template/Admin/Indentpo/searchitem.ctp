<table class="table table-bordered table-striped">
  <thead>
    <tr>
      <th>S No.</th>
      <th>Indent Id</th>
      <th>Contract name</th>
      <th>Product</th>
      <th>Machine Name</th>
      <th>Issue By</th>
      <th>Issue Date</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    <?php $page = $this->request->params['paging']['Indentpo']['page'];
    $limit = $this->request->params['paging']['Indentpo']['perPage'];
    // pr( $this->request->params);
    $counter = ($page * $limit) - $limit + 1;
    if (isset($indentpoid) && !empty($indentpoid)) {
      foreach ($indentpoid as $value) {
        $itemname = $this->comman->getitemname($value['finishedproduct_id']);
        $contractname = $this->comman->findcontractname($value['contract_id']);
        $machinename = $this->comman->getMachineName($value['machine_id']);
        ?>
        <tr>
          <td>
            <?php echo ($counter); ?>
          </td>


          <td><a href="<?php echo SITE_URL; ?>admin/indentpo/viewindentpodetail/<?php echo $value['indent_id']; ?>"
              class="viewindentpodetails">
              <?php echo $value['indent_id']; ?>
            </a></td>


          <td><a href="<?php echo SITE_URL; ?>admin/production/viewcontractdetail/<?php echo $value['contract_id']; ?>"
              class="viewdetails">
              <?php echo $contractname['title'] . '(' . $contractname['workorder'] . ')'; ?>
            </a></td>
          <td>
            <?php echo $itemname['item_name']; ?>
          </td>
          <td>
            <?php echo $machinename['machine_name']; ?>
          </td>
          <td>
            <?php echo ucfirst($value['issued_name']); ?>
          </td>
          <td>
            <?php echo date("d-m-Y", strtotime($value['issue_date'])); ?>
          </td>

          <td>
            <?php
            $user_id = $_SESSION['Auth']['User']['id'];
            $controllerName = $this->request->params['controller'];
            $actionName = "index";
            $user_permission = $this->comman->finduserpermisson($user_id, $controllerName, $actionName);
            $userdetails = $this->comman->getuser($user_id);

            if(date('d-m-Y') == date("d-m-Y", strtotime($value['issue_date']))){
              $fileurl = "admin/indentpo/edit";
              if (in_array($fileurl, $role_permissions)) { ?>
              <a href="<?php echo ADMIN_URL; ?>indentpo/edit/<?php echo $value['indent_id']; ?>"
                style="color:#3a6810; margin-right:5px;">
                <i class="far fa-edit" style="font-size: 16px !important;"></i>
              </a>
              <?php
            }
            $fileurl = "admin/indentpo/delete";
            if (in_array($fileurl, $role_permissions)) {
              echo $this->Html->link('', [
                'action' => 'delete',
                $value->indent_id,
              ], [
                'class' => 'fas fa-trash-alt',
                'style' => 'font-size: 16px !important; color:#cd0404; !important;',
                "onClick" => "javascript: return confirm('Are you sure do you want to delete this Indent')"
              ]);
            }
          }
            ?>&nbsp;&nbsp;


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
    <div class="modal-content">
      <div class="modal-body"></div>
    </div>
  </div>
</div>

<script>
  $('.viewindentpodetails').click(function (e) {
    e.preventDefault();
    $('#indentpo').modal('show').find('.modal-body').load($(this).attr('href'));
  });
</script>

<div class="modal fade" id="indentpo">
  <div class="modal-dialog" style="max-width:900px !important;">
    <div class="modal-content">
      <div class="modal-body"></div>
    </div>
  </div>
</div>