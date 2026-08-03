<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      User Management</b>
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo ADMIN_URL; ?>roles"><i class="fa fa-home"></i>Home</a></li>
      <li><a href="<?php echo ADMIN_URL; ?>roles/index">Manage Sections</a></li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <div>
      <div class="row">
        <div class="col-xs-12">

          <div class="box">
            <?php
            $role_permissions = $this->Permission->permissioncheck();
            $fileurl = "admin/roles/add";
            if (in_array($fileurl, $role_permissions)) { ?>

              <div class="box-header">



                <!-- <h3 class="box-title"><?php if (isset($rolesnew['id'])) { ?>Edit User <?php } else { ?>Add User <?php } ?>
                </h3> -->
                <?php echo $this->Flash->render(); ?>
                <?php echo $this->Form->create($rolesnew, array('class' => '', 'id' => 'sevice_form1', 'enctype' => 'multipart/form-data', 'validate', 'autocomplete' => 'off')); ?>

                <div class="box-body">
                  <div class="row">

                    <div class="col">
                      <label for="inputEmail3" class=" control-label">Select Role<span style="color:red;">*</span></label>
                      <?php echo $this->Form->input('role_id', array('class' => 'form-control', 'required', 'type' => 'select', 'options' => $roles, 'empty' => "--Select Role--", 'label' => false)); ?>
                    </div>


                    <div class="col">
                      <label for="inputEmail3" class=" control-label">Username<span style="color:red;">*</span></label>
                      <?php echo $this->Form->input('user_name', array('class' => 'form-control', 'type' => 'text', 'label' => false)); ?>
                    </div>

                    <div class="col">
                      <label for="inputEmail3" class=" control-label">User Email<span style="color:red;">*</span></label>
                      <?php echo $this->Form->input('email', array('class' => 'form-control', 'type' => 'email', 'label' => false)); ?>
                    </div>


                    <div class="col">
                      <label for="inputEmail3" class=" control-label">Mobile<span style="color:red;">*</span></label>
                      <?php echo $this->Form->input('mobile', array('class' => 'form-control', 'required', 'maxlength' => 10, 'id' => 'dup_mobile', 'placeholder' => 'Mobile', 'onkeypress' => 'return isNumber(event);', 'label' => false)); ?>
                      <script>
                        function isNumber(evt) {
                          evt = (evt) ? evt : window.event;
                          var charCode = (evt.which) ? evt.which : evt.keyCode;
                          if (charCode != 46 && charCode != 45 && charCode > 31 && (charCode < 48 || charCode > 57)) {
                            alert("Please Enter Valid Value");
                            return false;
                          }
                          return true;
                        }
                      </script>
                    </div>

                    <?php if (isset($rolesnew['id'])) { ?>
                      <input type="hidden" name="id" value="<?php echo $rolesnew['id']; ?>">

                      <div class="col">
                        <label for="inputEmail3" class=" control-label">Password<span style="color:red;">*</span></label>
                        <?php echo $this->Form->input('password', array('class' => 'form-control', 'type' => 'password', 'value' => $rolesnew['confirm_pass'], 'label' => false)); ?>
                      </div>

                      <div class="col">
                        <label for="inputEmail3" class=" control-label">Confirm Password<span
                            style="color:red;">*</span></label>
                        <?php echo $this->Form->input('confirm_pass', array('class' => 'form-control', 'type' => 'password', 'value' => $rolesnew['confirm_pass'], 'label' => false)); ?>
                      </div>

                    <?php } else { ?>

                      <div class="col">
                        <label for="inputEmail3" class=" control-label">Password<span style="color:red;">*</span></label>
                        <?php echo $this->Form->input('password', array('class' => 'form-control', 'type' => 'password', 'label' => false)); ?>
                      </div>

                      <div class="col">
                        <label for="inputEmail3" class=" control-label">Confirm Password<span
                            style="color:red;">*</span></label>
                        <?php echo $this->Form->input('confirm_pass', array('class' => 'form-control', 'type' => 'password', 'label' => false)); ?>
                      </div>

                    <?php } ?>



                    <div class="col">
                      <label></label>
                      <?php if (isset($rolesnew['id'])) {
                        echo $this->Form->submit('Edit User', array('class' => 'btn btn-info pull-right', 'style' => '', 'title' => 'Submit', 'id' => 'formsubmitbtn'));
                      } else { ?>
                        <?php echo $this->Form->submit('Add User', array('class' => 'btn btn-info pull-right', 'style' => '', 'title' => 'Submit', 'id' => 'formsubmitbtn'));
                      } ?>
                    </div>

                  </div>
                </div>

                <?php echo $this->Form->end(); ?>
              </div>
            <?php } ?>

            <!-- /.box-header -->
            <style>
              #example1_wrapper #example1 tr td:last-child a {
                display: inline-block;
              }
            </style>
            <div class="box-body">
              <table id="" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Role</th>
                    <th>Username</th>
                    <th>User Email</th>
                    <th>Mobile</th>
                    <th>Created</th>
                    <th>Action</th>
                    <!--<th>Action</th>-->
                  </tr>
                </thead>
                <tbody>
                  <?php $page = $this->request->params['paging']['Users']['page'];
                  $limit = $this->request->params['paging']['Users']['perPage'];
                  $counter = ($page * $limit) - $limit + 1;
                  if (isset($allusers) && !empty($allusers)) {
                    foreach ($allusers as $work) {
                      $user_role = $this->comman->findrolename($work['role_id']);
                      // pr($work['role_id']);die;
                      ?>
                      <tr>
                        <td><?php echo $counter; ?></td>
                        <td><?php if (isset($work['role_id'])) {
                          echo ucfirst($user_role['name']);
                        } else {
                          echo 'N/A';
                        } ?></td>
                        <td><?php if (isset($work['user_name'])) {
                          echo ucfirst($work['user_name']);
                        } else {
                          echo 'N/A';
                        } ?></td>
                        <td><?php if (isset($work['email'])) {
                          echo $work['email'];
                        } else {
                          echo 'N/A';
                        } ?></td>
                        <td><?php if (isset($work['mobile'])) {
                          echo ucfirst($work['mobile']);
                        } else {
                          echo 'N/A';
                        } ?></td>
                        <td><?php if (isset($work['created'])) {
                          echo date('d-m-Y', strtotime($work['created']));
                        } else {
                          echo 'N/A';
                        } ?></td>
                        <td>
                          <?php
                          $role_permissions = $this->Permission->permissioncheck();
                          $fileurl = "admin/roles/delete";
                          if (in_array($fileurl, $role_permissions)) {
                            ?>
                            <a title="Delete User"
                              onClick="javascript: return confirm('Are you sure do you want to delete this')"
                              href="<?php echo SITE_URL; ?>admin/roles/delete/<?php echo $work->id; ?>"><i
                                class="fa fa-trash fa-2x" aria-hidden="true"></i></a>

                          <?php } ?>
                          <?php
                          $role_permissions = $this->Permission->permissioncheck();
                          $fileurl = "admin/roles/add";
                          if (in_array($fileurl, $role_permissions)) {
                            ?>
                            <a title="Edit User" href="<?php echo SITE_URL; ?>admin/roles/index/<?php echo $work->id; ?>"><i
                                class="fas fa-edit" aria-hidden="true"></i></a>
                          <?php } ?>


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
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
  </section>
  <!-- /.content -->
</div>


<!-- /.content-wrapper -->
<script>
  $(document).ready(function () {

    $('#sevice_form1').on('submit', function (e) {
      $("#formsubmitbtn").css("display", "none");
    });
  });
</script>