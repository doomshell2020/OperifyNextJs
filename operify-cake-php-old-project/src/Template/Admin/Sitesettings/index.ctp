<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Admin User Management</b>
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo ADMIN_URL; ?>Sitesettings"><i class="fa fa-home"></i>Home</a></li>
      <li><a href="<?php echo ADMIN_URL; ?>Sitesettings/index">Manage Sections</a></li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <div>
      <div class="row">
        <div class="col-xs-12">

         
            <div class="box-body">
              <table id="" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>#</th>
                    <!-- <th>Role</th> -->
                    <th>Username</th>
                    <th>User Email</th>
                    <th>Mobile</th>
                    <th>Database</th>
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
                      ?>
                      <tr>
                        <td><?php echo $counter; ?></td>
                        <!-- <td><?php if (isset($work['role_id'])) {
                          echo ucfirst($work['role']['name']);
                        } else {
                          echo 'N/A';
                        } ?></td> -->
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
                         <td><?php if (isset($work['db'])) {
                          echo ucfirst($work['db']);
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
                          $user_id = $_SESSION['Auth']['User']['id'];
                          $controllerName = $this->request->params['controller'];
                          $actionName = $this->request->params['action'];
                          $user_permission = $this->comman->finduserpermisson($user_id, $controllerName, $actionName);
                           ?>
                          <a title="Edit User" href="<?php echo SITE_URL; ?>admin/sitesettings/edit/<?php echo $work->db; ?>"><i
                          class="fa fa-pencil fa-2x" aria-hidden="true"></i></a>
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