<!-- <script>
   $(document).ready(function() {
      $(".globalModals").click(function(event) {
         // alert($(this).attr("href"));
         $('.modal-content').load($(this).attr("href")); //load content from href of link
      });
   });
</script> -->

<div class="content-wrapper">

   <section class="content-header">
      <h1>
         Machine Manager
      </h1>
      <ol class="breadcrumb">
         <li><a href="#"><i class="fa fa-home"></i>Home</a></li>
         <li><a href="#">Machine Manager</a></li>
      </ol>
   </section>
   <!-- content header -->
   <!-- Main content -->
   <section class="content">
      <div class="row">
         <div class="col-xs-12">
            <div class="box">
               <div class="box-header">
                  <?php echo $this->Flash->render(); ?>
                  <a style="font-size: 20px;" target="_blank" href="<?php echo ADMIN_URL; ?>machine/viewpdf"><i
                        class="fa fa-file-pdf-o" style="font-size: 20px;"></i></a>&nbsp;
                  <a href="<?php echo SITE_URL; ?>admin/machine/add">
                     <button class="btn btn-success pull-right m-top10"><i class="fa fa-plus" aria-hidden="true"></i>
                        Add Machine</button></a>
                  <a href="<?php echo SITE_URL; ?>admin/process/index">
                     <button class="btn btn-success pull-right m-top10" style="margin-right: 10px;">
                       View Process</button></a>
               </div>
               <!-- /.box-header -->
               <div class="box-body">
                  <table id="" class="table table-bordered table-striped">
                     <thead>

                        <tr>
                           <th>S.No.</th> 
                           <th>Machine Name</th>
                           <th>Status</th>
                           <th>Created</th>
                           <th>Action</th>
                        </tr>

                     </thead>
                     <tbody>
                        <?php $page = $this->request->params['paging']['Machinemaster']['page'];
                        $limit = $this->request->params['paging']['Machinemaster']['perPage'];
                        // pr($this->request->params);exit;
                        $counter = ($page * $limit) - $limit + 1;
                        if (isset($machine_data) && !empty($machine_data)) {
                           foreach ($machine_data as $intusr) {
                              ?>
                              <tr>
                                 <td>
                                    <?php echo $counter; ?>
                                 </td>
                                 <td>
                                    <?php echo ucfirst($intusr['machine_name']); ?>
                                 </td>
                                 <td>
                                    <?php
                                    // $user_id = $_SESSION['Auth']['User']['id'];
                                    // $controllerName = $this->request->params['controller'];
                                    // $actionName = $this->request->params['action'];
                                    // $user_permission = $this->comman->finduserpermisson($user_id, $controllerName, $actionName);
                              
                                    // if ($user_permission['edit'] == '1') {
                                    if ($intusr['status'] == 'Y') {
                                       echo $this->Html->link('', [
                                          'action' => 'status',
                                          $intusr->id,
                                          'N'
                                       ], ['title' => 'Active', 'class' => 'fa fa-check-circle', 'style' => 'color: #36cb3c;']);
                                    } else {
                                       echo $this->Html->link('', [
                                          'action' => 'status',
                                          $intusr->id,
                                          'Y'
                                       ], ['title' => 'Inactive', 'class' => 'fa fa-times-circle-o', 'style' => 'color:#FF5722;']);
                                    }
                                    // } ?>
                                 </td>
                                 <td>
                                    <?php echo date("d-m-Y", strtotime($intusr['created'])); ?>
                                 </td>
                                 <td>
                                    <?php
                                    // if ($user_permission['edit'] == '1') {
                                    echo $this->Html->link('', [
                                       'action' => 'edit',
                                       $intusr->id,
                                    ], ['class' => 'fas fa-edit', 'style' => 'font-size: 16px !important;']);
                                    // }
                                    ?>
                                 </td>
                              </tr>
                              <?php $counter++;
                           }
                        } else { ?>
                        <?php } ?>
                     </tbody>
                  </table>
                  <?php echo $this->element('admin/pagination'); ?>
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
<!-- content-wrapper -->