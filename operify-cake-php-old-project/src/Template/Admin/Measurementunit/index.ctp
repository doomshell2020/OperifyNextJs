<style>
   .modal-dialog {}
</style>
<script>
   $(document).ready(function () {
      $(".globalModals").click(function (event) {
         // alert($(this).attr("href"));
         $('.modal-content').load($(this).attr("href")); //load content from href of link
      });
   });
</script>
<div class="content-wrapper">
   <section class="content-header">
      <h1>Measurement Units Manager</h1>
      <ol class="breadcrumb">
         <li><a href="<?php echo SITE_URL; ?>admin/indent"><i class="fa fa-home"></i>Home</a></li>
         <li><a href="<?php echo SITE_URL; ?>admin/measurementunit">Measurement Units Manager</a></li>
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
                  <!-- <a href="<?php echo SITE_URL; ?>admin/measurementunit/add">
                     <button class="btn btn-success pull-right m-top10"><i class="fa fa-plus" aria-hidden="true"></i>
                     Add Measurement Unit</button></a> -->
                  <?php echo $this->Form->create($cat, array(
                     'class' => 'form-horizontal',
                     'enctype' => 'multipart/form-data',
                     'id' => 'sevice_form',
                     'validate'
                  )
                  ); ?>
                  <div class="form-group" style="margin-bottom:0px; display:flex; align-items:flex-end; gap: 8px;">
                     <div class=" col-sm-4">
                        <label for="inputEmail3" class="control-label">Unit Name<strong
                              style='color:red;'>*</strong></label>
                        <?php echo $this->Form->input('unit_name', array('class' => 'form-control', 'type' => 'text', 'required', 'label' => false, 'placeholder' => 'Enter unit name', 'autofocus', 'autocomplete' => 'off')); ?>
                     </div>
                     <div class="col-sm-4">
                        <label for="inputEmail3" class="control-label">Description<strong
                              style='color:red;'>*</strong></label>
                        <?php echo $this->Form->input('description', array('class' => 'form-control', 'type' => 'text', 'required', 'label' => false, 'placeholder' => 'Enter Description', 'autofocus', 'autocomplete' => 'off')); ?>
                     </div>
                     <dv class="col-sm-4">
                        <?php
                        if (isset($cat['id'])) {
                           echo $this->Form->submit(
                              'Edit',
                              array('class' => 'btn btn-info', 'id' => 'formsubmitbtn', 'title' => 'Edit')
                           );
                        } else {
                           echo $this->Form->submit(
                              'Add',
                              array('class' => 'btn btn-info', 'id' => 'formsubmitbtn', 'title' => 'Add')
                           );
                        }
                        ?>
                     </dv>
                  </div>

                  <?php echo $this->Form->end(); ?>
               </div>
               <!-- /.box-header -->
               <div class="box-body" style="padding-top:0px;">
                  <table  class="table table-bordered table-striped">
                     <thead>
                        <tr>
                           <th>S.No.</th>
                           <th>Unit Name</th>
                           <th>Unit Description</th>
                           <th>Action</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php $page = $this->request->params['paging']['']['page'];
                        $limit = $this->request->params['paging']['']['perPage'];
                        $counter = ($page * $limit) - $limit + 1;
                        if (isset($users) && !empty($users)) {
                           foreach ($users as $intusr) { //pr($intusr);
                              ?>
                              <tr>
                                 <td><?php echo $counter; ?></td>
                                 <td><?php echo $intusr['unit_name']; ?>
                                 <td><?php echo $intusr['description']; ?>
                                 <td> <strong><?php
                               
 
                                 //  if ($user_permission['edit'] == '1') {
                                 echo $this->Html->link('', [
                                    'action' => 'index',
                                    $intusr->id,
                                 ], ['class' => 'fas fa-edit', 'style' => 'font-size: 16px ! important;']);
                              // }
                                 ?>
                                       &nbsp;<?php
                                       //  if ($user_permission['delete'] == '1') {
                                       echo $this->Html->link('', [
                                          'action' => 'delete',
                                          $intusr->id
                                       ], [
                                          'class' => 'fas fa-trash-alt',
                                          'style' => 'font-size: 16px ! important; color:#cd0404;',
                                          "onClick" => "javascript: return confirm('Are you sure do you want to delete this Measurement Units Record')"
                                       ]);
                                    //  } ?>
                                    </strong>
                                 </td>
                              </tr>
                              <?php $counter++;
                           }
                        } else { ?>
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
<!-- /.   content-wrapper -->
<div class="modal fade" id="globalModalbag" style="width:51% !important;" tabindex="-1" role="dialog"
   aria-labelledby="esModalLabel" aria-hidden="true">
   <div class="modal-dialog" style="width:100% !important;">
      <div class="modal-content personal">
         <div class="modal-body">
            <div class="col-sm-6 col-md-6 col-sm-offset-2 col-md-offset-2">
            </div>
            <div class="loader">
               <div class="es-spinner">
                  <i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<script>
   $(document).ready(function () {
      $('#sevice_form').on('submit', function (e) {
         $("#formsubmitbtn").css("display", "none");
      });
   });
</script>