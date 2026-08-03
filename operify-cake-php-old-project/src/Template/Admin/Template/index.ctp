<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>

<style>
  #emailtemplateTable_length {
    display: none;
  }

  #emailtemplateTable_filter {
    display: none;
  }

  #emailtemplateTable_paginate {
    display: none;
  }
</style>

<div class="content-wrapper">
  <section class="content-header">
    <h1>Template Manager</h1>
  </section> <!-- content header -->

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-xs-12">
      <div style = "text-align: right;">
<a href="<?php echo SITE_URL; ?>admin/template/add" class="btn btn-primary  addpopup fa-fa-plus"> <i ></i>Add Template</a>
</div>
        <div class="box">
          <div class="box-header">
            <?php echo $this->Flash->render(); ?>
          </div><!-- /.box-header -->

          <!-- Search filter for User Type
          <div class="row">
            <div class="col-xs-3" style="margin-left: 15px;">
              <label for="userTypeSearch">User Type:</label>
              <?php echo $this->Form->input('role', [
                'class' => 'form-control',
                'id' => 'userTypeSearch',
                'empty' => 'Select UserType',
                'options' => $role,
                'label' => false,
                'autocomplete' => 'off',
                'type' => 'select'
              ]); ?>
            </div>
          </div> -->


          <div class="box-body">
            <table id="emailtemplateTable" class="table table-bordered table-striped" width="100%">
              <thead>
                <tr>
                  <th width="5%">S.No</th>
                  <th width="10%">User Type</th>
                  <th width="8%">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $counter = 1;
                if (isset($emailtemplate) && !empty($emailtemplate)) {
                  foreach ($emailtemplate as $value) {
                  // pr($value);die;
                ?>
                    <tr>
                      <td><?php echo  $counter; ?></td>
                      
                      <td><?php echo $value['type_name']; ?></td>
                      <td>
                        <a href="<?php echo ADMIN_URL ?>template/viewtemplate/<?php echo $value['id']; ?>" data-toggle="modal" class="documentcls" title="View Email Template">
                          <i class="fa fa-eye" aria-hidden="true"></i>
                        </a>

                        <?php if ($value['status'] == 'Y') {
                          echo $this->Html->link('', [
                            'action' => 'status',
                            $value->id,
                            'N'
                          ], ['title' => 'Inactive', 'class' => 'fa fa-check-circle', 'style' => 'font-size: 21px !important; margin-left: 12px; color: #36cb3c;']);
                        } else {
                          echo $this->Html->link('', [
                            'action' => 'status',
                            $value->id,
                            'Y'
                          ], ['title' => 'Active', 'class' => 'fa fa-times-circle-o', 'style' => 'font-size: 21px !important; margin-left: 12px; color:#FF5722;']);
                        } ?>
<!-- 
                        <?php echo $this->Html->link(__(''), ['action' => 'edit', $value->id], array('class' => 'fa fa-pencil-square-o fa-lg', 'title' => 'clone', 'style' => 'font-size: 20px !important; margin-left: 12px;')) ?> -->
                        <a href="<?php echo ADMIN_URL ?>template/edit/<?php echo $value['id']; ?>" class="btn btn-success">Clone</a>
                        <a href="<?php echo ADMIN_URL ?>template/viewpdf/<?php echo $value['id']; ?>" class="btn btn-success">pdf</a>
                      
                      </td>
                    </tr>
                <?php $counter++;
                  }
                } ?>
              </tbody>
            </table>
          </div> <!-- /.box-body -->
        </div> <!-- /.box -->
      </div> <!-- /.col -->
    </div> <!-- /.row -->
  </section> <!-- /.content -->
</div> <!-- /.content-wrapper -->

<div class="modal fade" id="mymodel">
  <div class="modal-dialog" style="max-width: 500px !important;">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Email Template Format</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <!-- Modal body -->
      <div class="modal-body">
        <!-- Your dynamic content will be loaded here -->

        <!-- Close Button in Modal Body -->
        <div class="text-right">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  $(document).ready(function() {
    var table = $('#emailtemplateTable').DataTable({
      "paging": true,
      "searching": true,
      "ordering": true,
      "info": true,
      "pageLength": 100,
    });

    // Custom search filter for User Type
    $('#userTypeSearch').on('change', function() {
      var selectedUserType = $(this).val();
      if (selectedUserType) {
        table.columns(1).search(selectedUserType).draw();
      } else {
        table.columns(1).search('').draw();
      }
    });

    // Modal popup open
    $('.documentcls').click(function(e) {
      e.preventDefault();
      $('#mymodel').modal('show').find('.modal-body').load($(this).attr('href'));
    });
  });
</script>