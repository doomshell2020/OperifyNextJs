<div class="content-wrapper">
   <section class="content-header">
      <h1>
         Size Manager
      </h1>
      <ol class="breadcrumb">
         <li><a href="<?php echo SITE_URL; ?>admin/dashboards"><i class="fa fa-home"></i>Home</a></li>
         <li><a href="<?php echo SITE_URL; ?>admin/sizemanager">Size Manager</a></li>
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
                  <!-- <a href="<?php echo SITE_URL; ?>admin/sizemanager/add">
                     <button class="btn btn-success pull-right m-top10"><i class="fa fa-plus" aria-hidden="true"></i>
                     Add Size</button></a> -->
                  <?php echo $this->Form->create($cat, array(
                     'class'=>'form-horizontal',
                     'enctype' => 'multipart/form-data',
                     'validate'
                     )); ?>   
                  <div class="form-group" style="display:flex; align-items:flex-end;margin-bottom:0px; ">
                     <div class="col-sm-4">
                        <label for="inputEmail3" class="control-label">Size Name<strong style='color:red;'>*</strong></label>
                        <?php echo $this->Form->input('size_name', array('class' => 'form-control', 'type' => 'text', 'required', 'label' => false, 'placeholder' => 'Enter Size Name', 'autofocus', 'autocomplete' => 'off')); ?>
                     </div>
                     <div class="col-sm-4">
                        <label for="inputEmail3" class="control-label">Description<strong style='color:red;'>*</strong></label>
                        <?php echo $this->Form->input('description', array('class' => 'form-control','type'=>'text','required','label'=>false,'placeholder'=>'Enter Description','autofocus','autocomplete'=>'off')); ?>
                     </div>
                     <div class="col-sm-4">
                        <?php
                           if(isset($category['id'])){
                             echo $this->Form->submit(
                               'Update', 
                               array('class' => 'btn btn-info', 'title' => 'Update')
                             ); }else{ 
                               echo $this->Form->submit(
                                 'Add', 
                                 array('class' => 'btn btn-info', 'title' => 'Add')
                               );
                             }
                           ?>
                     </div>
                  </div>
                  <?php echo $this->Form->end(); ?>
               </div>
               <!-- /.box-header -->
               <div class="box-body" style="padding-top:0px;">
                  <table id="example14" class="table table-bordered table-striped">
                     <thead>
                        <tr>
                           <th>S.No.</th>
                           <th>Size Name</th>
                           <th>Description</th>
                           <th>Action</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php $page = $this->request->params['paging']['']['page'];
                           $limit = $this->request->params['paging']['']['perPage'];
                           $counter = ($page * $limit) - $limit + 1;
                           if(isset($users) && !empty($users)){ 
                             foreach($users as $intusr){ //pr($intusr);
                               ?>
                        <tr>
                           <td><?php echo $counter;?></td>
                           <td><?php echo $intusr['size_name']; ?>
                           <td><?php echo $intusr['description']; ?>
                           <td> <strong><?php
                              echo $this->Html->link('', [
                                  'action' => 'index',
                                  $intusr->id,
                              ], ['class' => 'fas fa-edit', 'style' => 'font-size: 21px;']);
                              
                              ?>
                              &nbsp;<?php
                                 echo $this->Html->link('', [
                                   'action' => 'delete',
                                   $intusr->id
                                 ],['class'=> 'fas fa-trash-alt','style'=>'font-size: 21px; color:#cd0404'	
                                 ,"onClick"=>"javascript: return confirm('Are you sure do you want to delete this Size')"]); ?>
                              </strong>
                           </td>
                        </tr>
                        <?php $counter++; } }else{ ?>
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
   <!-- content -->  
</div>
<!-- content-wrapper -->  
<div class="modal fade" id="globalModalbag" style="width:51% !important;" tabindex="-1" role="dialog" aria-labelledby="esModalLabel" aria-hidden="true">
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