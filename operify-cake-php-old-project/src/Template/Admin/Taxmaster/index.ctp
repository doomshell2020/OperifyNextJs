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
      <h1>
         Tax Master
      </h1>
      <ol class="breadcrumb">
         <li><a href="<?php echo SITE_URL; ?>admin/indent"><i class="fa fa-home"></i>Home</a></li>
         <li><a href="<?php echo SITE_URL; ?>admin/taxmaster">Tax Manager</a></li>
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
                  <a href="<?php echo SITE_URL; ?>admin/taxmaster/add">
                     <button class="btn btn-success pull-right m-top10"><i class="fa fa-plus" aria-hidden="true"></i>
                        Add Tax</button></a>
                  <a href="<?php echo SITE_URL; ?>admin/Measurementunit/index">
                     <button class="btn btn-success pull-right m-top10" style = "margin-right: 10px;">
                        View UOM</button></a>
               </div>
               <!-- /.box-header -->
               <div class="box-body">
                  <table  class="table table-bordered table-striped">
                     <thead>
                        <tr>
                           <th width="10%">S.No.</th>
                           <!-- <th>Parent Tax</th>
                              <th>Tax Type Name</th>      -->
                           <th width="20%">Tax</th>
                           <th width="60%">Description</th>
                           <th width="10%">Action</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php $page = $this->request->params['paging']['']['page'];
                        $limit = $this->request->params['paging']['']['perPage'];
                        $counter = ($page * $limit) - $limit + 1;
                        if (isset($users) && !empty($users)) {
                           foreach ($users as $intusr) { //pr($intusr);
                              $var = $this->Comman->gettaxname($intusr['parent']);

                              ?>
                              <tr>
                                 <td>
                                    <?php echo $counter; ?>
                                 </td>
                                 <!-- <td><?php //if($var['tax_name']){ echo $var['tax_name']; }else{ echo "--";} 
                                       ?></td>
                              <td><?php // echo $intusr['tax_name']; 
                                    ?></td> -->
                                 <td>
                                    <?php //if(substr_count($intusr['tax']) == 0){ echo $intusr['tax']." %"; }else{ echo $intusr['tax']; } 
                                          ?>
                                    <?php
                                    echo $intusr['tax']; ?>
                                 </td>
                                 <td>
                                    <?php echo $intusr['description']; ?>
                                 </td>
                                 </td>
                                 <td>
                                    <?php

                                   
                                    // if ($user_permission['edit'] == 1) {
                                       echo $this->Html->link('', [
                                          'action' => 'edit',
                                          $intusr->id,
                                       ], ['class' => 'fas fa-edit', 'style' => 'font-size: 21px;']);
                                    // } ?>
                                    &nbsp;
                                    <?php

                                    // if ($user_permission['delete'] == 1) {
                                       echo $this->Html->link('', [
                                          'action' => 'delete',
                                          $intusr->id
                                       ], [
                                          'class' => 'fas fa-trash-alt',
                                          'style' => 'font-size: 21px; color:#cd0404',
                                          "onClick" => "javascript: return confirm('Are you sure do you want to delete this tax record')"
                                       ]);
                                    // } ?>

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