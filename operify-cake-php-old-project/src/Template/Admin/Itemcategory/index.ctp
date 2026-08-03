<style>
   .modal-dialog {}

   #load2 {
      width: 100%;
      height: 100%;
      position: fixed;
      z-index: 9999;
      background-color: white !important;
      background: url("<?php echo SITE_URL; ?>images/Preloader_2.gif") no-repeat center center rgba(0, 0, 0, 0.75)
   }
</style>
<script>
   $(document).ready(function() {
      $(".globalModals").click(function(event) {
         // alert($(this).attr("href"));
         $('.modal-content').load($(this).attr("href")); //load content from href of link
      });
   });
</script>
<div class="content-wrapper">
   <section class="content-header">
      <h1>
         Item Category Manager
      </h1>
      <ol class="breadcrumb">
         <li><a href="<?php echo SITE_URL; ?>admin/indent"><i class="fa fa-home"></i>Home</a></li>
         <li><a href="<?php echo SITE_URL; ?>admin/itemcategory">Item Category Manager</a></li>
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
                  <?php
                  $role_permissions = $this->Permission->permissioncheck();
                  $fileurl = "admin/itemcategory/add";
                  if (in_array($fileurl, $role_permissions)) { ?>

                     <a href="<?php echo SITE_URL; ?>admin/itemcategory/add">
                        <button class="btn btn-success m-top10 pull-right " style="margin-top: 24px;">
                           <i class="fa fa-plus" aria-hidden="true"></i>
                           Add Item Category
                        </button>
                     </a>
                  <?php } ?>
                  <script>
                     $(document).ready(function() {
                        $("#Mysubscriptions").bind("submit", function(event) {
                           $('.lds-facebook').show();
                           $.ajax({
                              async: true,
                              data: $("#Mysubscriptions").serialize(),
                              dataType: "html",
                              type: "POST",
                              url: "<?php echo ADMIN_URL; ?>itemcategory/searchitem",

                              beforeSend: function(xhr) {
                                 xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
                                 $('#load2').css("display", "block"); // Show loader
                              },
                              success: function(data) {
                                 $('.lds-facebook').hide();
                                 $("#example2").html(data);
                              },
                              complete: function() {
                                 $('#load2').css("display", "none"); // Hide loader
                              },
                              error: function() {
                                 alert("An error occurred. Please try again.");
                                 $('#load2').css("display", "none"); // Hide loader on error
                              }

                           });
                           return false;
                        });
                     });
                  </script>

                  <?php echo $this->Form->create('Mysubscription', array('type' => 'file', 'inputDefaults' => array('div' => false, 'label' => false), 'id' => 'Mysubscriptions', 'class' => '')); ?>

                  <div class="form-group pull-left" style="display:flex; align-items:flex-end; margin-bottom:0px;">
                     <div style="margin-right:10px;  ">
                        <label for="inputEmail3" class="control-label">Item Category Name</label>
                        <?php echo $this->Form->input('category_name', array('class' => 'form-control', 'label' => false, 'placeholder' => 'Enter Item Category Name', 'autocomplete' => 'off')); ?>
                     </div>
                     <input type="submit" style="background-color:#00c0ef; color:#fff" id="Mysubscriptions"
                        class="btn btn4 btn_pdf myscl-btn date" value="Search">
                     <a href="<?php echo SITE_URL; ?>admin/itemcategory" class="excelbtn btn"
                        style="background-color:#00c0ef; !important; margin-top: 23px; color:#fff; padding:6px 18px; margin-left: 7px;">Reset</a>
                  </div>
                  <div style="clear-both"></div>
               </div>
               <!-- /.box-header -->
               <div id="load2" style="display:none;"></div>
               <div class="box-body" id="example2">
                  <table id="example14" class="table table-bordered table-striped">
                     <thead>
                        <tr>
                           <th width="10%">S.No.</th>
                           <th width="40%">Item Category Name</th>
                           <th width="40%">Description</th>
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
                                 <td><?php echo $counter; ?></td>
                                 <td>
                                    <?php
                                    echo ucfirst($intusr['category_name']);
                                    ?>
                                 </td>
                                 <td>
                                    <?php echo $intusr['description']; ?>
                                 </td>
                                 <td>
                                    <?php

                                    $fileurl = "admin/itemcategory/edit";
                                    if (in_array($fileurl, $role_permissions)) {

                                       echo $this->Html->link('', [
                                          'action' => 'edit',
                                          $intusr->id,
                                       ], ['class' => 'fas fa-edit', 'style' => 'font-size: 16px !important;']);
                                    } ?>
                                    &nbsp;
                                    <?php if ($intusr['status'] == 'Y') {
                                       echo $this->Html->link('', [
                                          'action' => 'status',
                                          $intusr->id,
                                          'Y'
                                       ], ['title' => 'Active', 'class' => 'fas fa-check-circle', 'style' => 'font-size: 16px !important; color: #36cb3c;']);
                                    } else {
                                       echo $this->Html->link('', [
                                          'action' => 'status',
                                          $intusr->id,
                                          'N'
                                       ], ['title' => 'Inactive', 'class' => 'fas fa-times-circle', 'style' => 'font-size: 16px !important; color:#cd0404;']);
                                    }  ?>
                                    &nbsp;
                                    <?php
                                    $fileurl = "admin/itemcategory/delete";
                                    if (in_array($fileurl, $role_permissions)) {
                                       echo $this->Html->link('', [
                                          'action' => 'delete',
                                          $intusr->id
                                       ], [
                                          'class' => 'fas fa-trash-alt',
                                          'style' => 'font-size: 16px !important; color:#cd0404;',
                                          "onClick" => "javascript: return confirm('Are you sure do you want to delete this Item Category')"
                                       ]);
                                    } ?>&nbsp;
                                    <?php if ($intusr['is_print'] == 'Y') {
                                       echo $this->Html->link('', [
                                          'action' => 'printstatus',
                                          $intusr->id,
                                          'Y'
                                       ], ['title' => 'Print Available', 'class' => 'fa fa-print', 'style' => 'font-size: 16px !important; color: #36cb3c;']);
                                    } else {
                                       echo $this->Html->link('', [
                                          'action' => 'printstatus',
                                          $intusr->id,
                                          'N'
                                       ], ['title' => 'Print Not Available', 'class' => 'fa fa-print', 'style' => 'font-size: 16px !important; color: #cd0404;']);
                                    }  ?>


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