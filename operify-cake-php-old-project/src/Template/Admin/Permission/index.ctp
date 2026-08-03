<style>
  .permissions-table {
    width: 100%;
    border-collapse: collapse;
  }

  .permissions-table th,
  .permissions-table td {
    border: 1px solid #ddd;
    padding: 8px;
  }

  .permissions-table th {
    background-color: #383737;
  }

  .permissions-table .controller-row {
    font-weight: bold;
    background-color: #f9f9f9;
  }
</style>

<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>Permission Manager</h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo ADMIN_URL; ?>PermissionModules"><i class="fa fa-home"></i> Home</a></li>
      <li><a href="<?php echo ADMIN_URL; ?>PermissionModules/index">Permission Manager</a></li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          <div class="box-header">
            <?php echo $this->Flash->render(); ?>
          </div>

          <div class="box-body">
            <div class="manag-stu">
              <div style="width:100%">

                <!-- First Form -->
                <?php echo $this->Form->create(null, [
                  'class' => 'form-horizontal',
                  'id' => 'service_form',
                  'enctype' => 'multipart/form-data',
                  'validate' => true,
                  'url' => ['action' => 'index'] // Specify your controller and action here
                ]); ?>

                <div class="row">
                  <div class="col-sm-3">
                    <label>Manager Name<span style="color:red;">*</span></label>
                    <?php
                    echo $this->Form->control('manager_id', [
                      'class' => 'form-control',
                      'type' => 'select',
                      'id' => 'manager-name',
                      'empty' => '--Select Manager--',
                      'options' => $manager_list,
                      'label' => false,
                      'required' => true
                    ]); ?>
                  </div>

                  <div class="col-sm-3">
                    <label>Label Name<span style="color:red;">*</span></label>
                    <?php
                    echo $this->Form->control('label_name', [
                      'class' => 'form-control',
                      'type' => 'text',
                      'placeholder' => 'Enter Label Name',
                      'label' => false,
                      'required' => true
                    ]); ?>
                  </div>

                  <div class="col-sm-4">
                    <label>URL<span style="color:red;">*</span></label>
                    <?php
                    echo $this->Form->control('url', [
                      'class' => 'form-control',
                      // 'type' => 'url',
                      'placeholder' => 'Enter URL',
                      'label' => false,
                      'required' => true
                    ]); ?>
                  </div>

                  <div class="col-sm-2">
                    <div class="submit">
                      <input type="submit" class="btn btn-info" value="Submit" title="Submit" style="margin-top: 27px;">
                    </div>
                  </div>
                </div>

                <?php echo $this->Form->end(); ?>
                <!-- End First Form -->

                <br>
                <hr>
                <br><br>
                <!-- Second Form -->
                <?php echo $this->Form->create($classes, [
                  'class' => 'form-horizontal',
                  'id' => 'update_form',
                  'enctype' => 'multipart/form-data',
                  'validate' => true,
                  'url' => ['action' => 'updaterights'] // Specify your controller and action here
                ]); ?>

                <div class="row">
                  <div class="col-sm-3">
                    <label>User Role<span style="color:red;">*</span></label>
                    <?php
                    echo $this->Form->control('role_id', [
                      'class' => 'form-control',
                      'type' => 'select',
                      'id' => 'emp-type',
                      'empty' => '--Select--',
                      'options' => $users_role,
                      'label' => false,
                      'required' => true,
                      'onchange' => 'fetchPermissions(this.value)' // Added onchange event
                    ]); ?>
                  </div>

                  <div class="col-sm-7">
                    <div class="submit" style="width: 100%;display: flex;justify-content: end;">
                      <input type="submit" class="btn btn-info pull-left" value="Grant Permission"
                        title="Grant Permission" style="margin-top: 27px;">
                    </div>
                  </div>
                  <div class="col-sm-2">

                    <a href="<?php echo SITE_URL; ?>admin/Permission/add" class="btn btn-primary  addpopup fa-fa-plus"
                      style="margin-top:27px;" data-toggle="modal" data-target="#exampleModal" data-whatever="@mdo"
                      aria-hidden="true"> <i class="fa fa-plus view-details" ?></i>Add Manager</a>

                  </div>

                </div>
                <!-- End Second Form -->

                <br><br>

                <div id="amountrt">
                  <!-- Permissions Table -->
                  <table class="permissions-table">
                    <thead>
                      <tr>
                        <th>MANAGERS NAME</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($groupedModules as $manager_id => $actions) {
                        $managers = $this->Permission->managerName($manager_id);
                        
                        $manager_name = $managers['name'];
                        ?>
                        <tr class="controller-row">
                          <td><?php echo ucfirst($manager_name); ?>
                          </td>
                        </tr>
                        <tr>
                          <td>
                            <table>
                              <tr>
                                <td>
                                  <input type="checkbox" class="select-all"
                                    data-controller="<?php echo htmlspecialchars($manager_id); ?>" /> Select All
                                </td>
                                <?php foreach ($actions as $action) { ?>
                                  <td>
                                    <input type="checkbox"
                                      name="permissions[<?php echo $manager_id; ?>][<?php echo $action; ?>]"
                                      class="permission-checkbox"
                                      data-controller="<?php echo htmlspecialchars($manager_id); ?>" />
                                    <?php echo ucfirst($action); ?>
                                  </td>
                                <?php } ?>
                              </tr>
                            </table>
                          </td>
                        </tr>
                      <?php } ?>
                    </tbody>
                  </table>
                </div>

                <?php echo $this->Form->end(); ?>

                <script>
                  function fetchPermissions(roleId) {
                    if (!roleId) return;

                    $.ajax({
                      url: '<?php echo $this->Url->build(['action' => 'search']); ?>',
                      type: 'POST',
                      data: {
                        role_id: roleId
                      },
                      success: function (response) {
                        $('#amountrt').html(response);
                      },
                      error: function () {
                        alert('Error fetching permissions. Please try again.');
                      }
                    });
                  }

                  document.querySelectorAll('.select-all').forEach(selectAllCheckbox => {
                    selectAllCheckbox.addEventListener('change', function () {
                      const isChecked = this.checked;
                      const controller = this.getAttribute('data-controller');
                      const permissionCheckboxes = document.querySelectorAll(`.permission-checkbox[data-controller="${controller}"]`);

                      permissionCheckboxes.forEach(permissionCheckbox => {
                        permissionCheckbox.checked = isChecked;
                      });
                    });
                  });
                </script>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>


<script>
  $('.addpopup').click(function (e) {
    e.preventDefault();
    $('#addpopup').modal('show').find('.modal-body').load($(this).attr('href'));
  });
</script>

<div class="modal fade" id="addpopup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-body"></div>
      <div class="modal-footer">
      </div>
    </div>
  </div>
</div>