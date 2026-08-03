<table class="table table-bordered table-striped" width="100%">
  <thead>
    <tr>
      <th width="10%">PO Id</th>
      <th width="08%">PO Date</th>
      <th width="23%">Vendor</th>
      <th width="10%">Contact No.</th>
      <th width="09%">Order Qty</th>
      <th width="09%">Received Qty</th>
      <th width="09%">Total(INR)</th>
      <th width="08%">Delivery Date</th>
      <th width="13%">Action</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $page = $this->request->params['paging']['Purchaseorder']['page'];
    $limit = $this->request->params['paging']['Purchaseorder']['perPage'];
    $counter = ($page * $limit) - $limit + 1;
    $role_permissions = $this->Permission->permissioncheck();

    if (isset($podata) && !empty($podata)) {
      foreach ($podata as $value) { //pr($value);
        $var = $this->Comman->poitemquantity($value['purchaseorder_id'], $value['is_revised'], $value['id']);
        $podetail = $this->Comman->podetail($value['purchaseorder_id'], $value['is_revised'], $value['id']);
        $vendor_id = $this->Comman->findvendornames($value['vendor_id']);
        $getgrn = $this->Comman->checkgrn($value['purchaseorder_id'], $value['id']);
        $pendingQty = '';
        foreach ($getgrn as $value1) {
          $pendingQty += $value1['quantity'];
        }

        $user_id = $_SESSION['Auth']['User']['id'];
        $controllerName = $this->request->params['controller'];
        $actionName = $this->request->params['action'];
        $user_permission = $this->comman->finduserpermisson($user_id, $controllerName, $actionName);
        $podetail = $this->Comman->findgoodsrecivied($value['purchaseorder_id']);
        $porevised = $this->Comman->findrevisedno($value['purchaseorder_id']);

    ?>
        <tr>
          <td>
            <a class="viewgrndetails"
              href="<?php echo ADMIN_URL; ?>purchaseorder/viewpodetail/<?php echo $value['purchaseorder_id'] . "/" . $value['is_revised'] . "/" . $value['id']; ?>">
              <?php echo $value['purchaseorder_id']; ?>
            </a>
            <?php if ($value['is_revised'] > 0) { ?>
              <a class="viewgrndetails" style="font-size: 20px;"
                href="<?php echo ADMIN_URL; ?>purchaseorder/viewpodetail/<?php echo $value['purchaseorder_id'] . "/" . $value['is_revised'] . "/" . $value['id']; ?>">R
                <?php echo "-" . $value['is_revised']; ?>&nbsp;
              </a>
            <?php } ?>
          </td>
          <td>
            <?php echo date("d-m-Y", strtotime($value['added_time'])); ?>
          </td>
          <td>
            <a class="viewvendordetails"
              href="<?php echo ADMIN_URL; ?>vendors/viewdetail/<?php echo $value['vendor_id']; ?>">
              <?php echo ucfirst(strtolower($vendor_id['name'])); ?>
            </a>
            <?php if ($user_permission['edit'] == '1') { ?>
              <a target="_blank" href="<?php echo ADMIN_URL; ?>vendors/add/<?php echo $value['vendor_id'] ?>"
                class="fas fa-edit pull-right" style="color:#303c30;">
              </a>
            <?php } ?>
          </td>
          <td>
            <?php $ert = explode(',', $vendor_id['contact_no']);
            if (isset($vendor_id['contact_no'])) {
              foreach ($ert as $fg) {
                echo $fg;
              }
            } else {
              echo 'N/A';
            }
            ?>
          </td>
          <td style="text-align:right;">
            <?php echo number_format((float) $value['total_qty'], 2, '.', ''); ?>
          </td>
          <td style="text-align:right;">
            <?php echo number_format((float) $pendingQty, 2, '.', ''); ?>
          </td>
          <td style="text-align:right;">
            <?php echo number_format($value['total_amt']); ?>
          </td>
          <td>
            <?php echo date("d-m-Y", strtotime($value['delivery_date'])); ?>
          </td>

          <td style="text-align:center">
            <button class="btn btn-primary dropdown-toggle dropppss dropdown" type="button" id="dropdownMenuButton"
              data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Action
            </button>

            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
              <?php
              // edit or revised
              if ($porevised['is_revised'] == $value['is_revised']) {
                $postatus = $value['postatus'];
                if ($podetail) {
                  if ($postatus != 'C') {
                    $fileurl = "admin/purchaseorder/edit";
                    if (in_array($fileurl, $role_permissions)) { ?>
                      <a href="<?php echo ADMIN_URL; ?>purchaseorder/edit/<?php echo $value['id']; ?>/<?php echo $value['purchaseorder_id']; ?>"
                        class="dropdown-item hover">
                        <b>Edit PO</b>
                      </a>
                    <?php }
                  }
                } else {
                  $fileurl = "admin/purchaseorder/revised";
                  if (in_array($fileurl, $role_permissions)) { ?>
                    <a href="<?php echo ADMIN_URL; ?>purchaseorder/revised/<?php echo $value['purchaseorder_id']; ?>/<?php echo $value['id']; ?>"
                      class="dropdown-item hover">
                      <b>Revise PO</b>
                    </a>
                  <?php }
                }

                // delete

                $fileurl = "admin/purchaseorder/delete";
                if (in_array($fileurl, $role_permissions)) {
                  if ($value['status'] != 'N' && $podetail == null) { ?>
                    <a href="<?php echo ADMIN_URL; ?>purchaseorder/delete/<?php echo $value['id'] ?>" style="color:red;"
                      class="dropdown-item hover"
                      onClick="javascript:return confirm('Are you sure do you want to delete this Purchase Order')"><b>Delete</b>
                    </a>
                  <?php }
                }

                // add delivery note 
                $fileurl = "admin/purchaseorder/deliverynote";
                if (in_array($fileurl, $role_permissions)) {
                  $getDeliverydates = $this->Comman->getDeliverydates($value['id']);
                  if ($postatus != 'C' && empty($getDeliverydates)) { ?>
                    <a href="<?php echo ADMIN_URL; ?>purchaseorder/deliverynote/<?php echo $value['id'] ?>"
                      class="dropdown-item hover"><b>Add Delivery Note</b> </a>
                  <?php } else { ?>
                    <a href="<?php echo ADMIN_URL; ?>purchaseorder/deliverynote/<?php echo $value['id'] ?>" style="color:green"
                      class="dropdown-item hover"><b>Edit Delivery Note</b> </a>
              <?php }
                }
              } ?>

              <!-- // print current Po Pdf -->
              <a class="dropdown-item hover" target="_blank" style="color:blue"
                href="<?php echo ADMIN_URL; ?>purchaseorder/view/<?php echo $value['purchaseorder_id'] . "/" . $value['is_revised'] . "/" . $value['id'] ?>"><b>Print
                  PO <?php echo $value['purchaseorder_id'];
                      if ($value['is_revised'] > 0) { ?>
                    <?php echo "R-" . $value['is_revised']; ?>&nbsp;</b>
              </a>
            <?php }
                      // print all po Pdf
                      if ($value['is_revised'] > 0) { ?>
              <a class="dropdown-item hover" target="_blank" style="color:blue"
                href="<?php echo ADMIN_URL; ?>purchaseorder/printallpo/<?php echo $value['purchaseorder_id'] ?>"><b>Print
                  Revised PO</b>
              </a>
            <?php }

                      // print Delivery schedule
                      if ($getDeliverydates) { ?>
              <a class="dropdown-item hover" target="_blank" style="color:blue"
                href="<?php echo ADMIN_URL; ?>purchaseorder/printdeliveryschedule/<?php echo $value['id'] ?>"><b>Print
                  Delivery Schedule</b></a>
            <?php } ?>
            </div>
          </td>
        </tr>
      <?php $counter++;
      }
    } else { ?>
    <?php } ?>
  </tbody>
</table>

<!-- <?php // echo $this->element('admin/pagination'); 
      ?> -->
<?php echo $this->element('admin/custompagination'); ?>

<script>
  $('.viewgrndetails').click(function(e) {
    e.preventDefault();
    $('#editsortsgrn').modal('show').find('.modal-body').load($(this).attr('href'));
  });
</script>

<div class="modal fade" id="editsortsgrn">
  <div class="modal-dialog " style="max-width: 900px !important;">
    <div class="modal-content">
      <div class="modal-body" style="background:white;"></div>
    </div>
  </div>
</div>

<script>
  $('.delivery_note').click(function(e) {
    e.preventDefault();
    $('#cancelsorts').modal('show').find('.modal-body').load($(this).attr('href'));
  });
</script>
<div class="modal fade" id="cancelsorts">
  <div class="modal-dialog" style="max-width:999px !important;">
    <div class="modal-content">
      <div class="modal-body purc_mdl_body"></div>
    </div>
  </div>
</div>

<!-- for view Vendor details -->
<script>
  $('.viewvendordetails').click(function(e) {
    e.preventDefault();
    $('#editsortsvendor').modal('show').find('.modal-body').load($(this).attr('href'));
  });
</script>

<div class="modal fade" id="editsortsvendor">
  <div class="modal-dialog " style="max-width: 900px !important;">
    <div class="modal-content">
      <div class="modal-body"></div>
    </div>
  </div>
</div>