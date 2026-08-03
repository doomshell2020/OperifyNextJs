<script>
   $(document).ready(function () {
      $(".globalModals").click(function (event) {
         $('.modal-content').load($(this).attr("href"));
      });
   });
</script>

<div class="content-wrapper">
   <section class="content-header">
      <h1>
         Process Master
      </h1>
      <ol class="breadcrumb">
         <li><a href="<?php echo SITE_URL; ?>admin/indent"><i class="fa fa-home"></i>Home</a></li>
         <li><a href="<?php echo SITE_URL; ?>admin/Process">Process Manager</a></li>
      </ol>
   </section>
   <!-- content header -->
   <!-- Main content -->
   <section class="content">
      <div class="row">
         <div class="col-xs-12">
            <div class="box">

               <!-- /.box-header -->
               <?php
               // $role_permissions = $this->Permission->permissioncheck();
               // $fileurl = "admin/roles/add";
               // if (in_array($fileurl, $role_permissions)) { ?>

               <div class="box-header">


                  <?php echo $this->Flash->render(); ?>
                  <?php echo $this->Form->create($company, array('class' => '', 'id' => 'sevice_form1', 'enctype' => 'multipart/form-data', 'validate', 'autocomplete' => 'off')); ?>

                  <div class="box-body">
                     <div class="row">

                        <div class="col">
                           <label for="inputEmail3" class=" control-label">Process<span
                                 style="color:red;">*</span></label>
                           <?php echo $this->Form->input('process_name', array('class' => 'form-control', 'type' => 'text', 'label' => false)); ?>
                        </div>


                        <div class="col">
                           <label></label>
                           <?php if (isset($company['id'])) {
                              echo $this->Form->submit('Update', array('class' => 'btn btn-info pull-right', 'style' => '', 'title' => 'Submit', 'id' => 'formsubmitbtn'));
                           } else { ?>
                              <?php echo $this->Form->submit('Add', array('class' => 'btn btn-info pull-right', 'style' => '', 'title' => 'Submit', 'id' => 'formsubmitbtn'));
                           } ?>
                        </div>

                     </div>
                  </div>

                  <?php echo $this->Form->end(); ?>
               </div>
               <?php // } ?>


               <div class="box-body">
                  <table class="table table-bordered table-striped">
                     <thead>
                        <tr>
                           <th width="10%">S.No.</th>
                           <th width="20%">Process Name</th>
                           <th width="10%">Action</th>
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
                                 <td>
                                    <?php echo $counter; ?>
                                 </td>

                                 <td>
                                    <?php
                                    ?>
                                    <?php
                                    echo $intusr['process_name']; ?>
                                 </td>

                                 </td>
                                 <td>
                                    <?php


                                    echo $this->Html->link('', [
                                       'action' => 'index',
                                       $intusr->id,
                                    ], ['class' => 'fas fa-edit', 'style' => 'font-size: 21px;']);


                                    echo $this->Html->link('', [
                                       'action' => 'delete',
                                       $intusr->id
                                    ], [
                                       'class' => 'fas fa-trash-alt',
                                       'style' => 'font-size: 21px; color:#cd0404',
                                       "onClick" => "javascript: return confirm('Are you sure do you want to delete this tax record')"
                                    ]);
                                    ?>

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
<!-- content-wrapper -->

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