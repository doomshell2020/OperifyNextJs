<style>
   #testUL {
      position: relative;
   }

   #testUL ul {
      position: absolute;
      z-index: 999;
      overflow: scroll;
      height: 100px;
      top: 100%;
      left: 0px;
      right: 0px;
      list-style-type: none;
      background-color: white;
      padding-left: 0px;
   }

   #testUL ul li {
      padding: 5px 8px;
      border: 1px solid lightgray;
   }

   #testUL ul li a {
      color: black;
   }

   .preview {
      margin-right: 15px;
   }
</style>
<div class="content-wrapper">
   <section class="content-header">
      <h1>
         Add Jobseeker
      </h1>
      <ol class="breadcrumb">
         <li><a href="<?php echo SITE_URL; ?>admin/Jobseeker/index"><i class="fa fa-home"></i>Home</a></li>
          <li><a href="<?php echo SITE_URL; ?>admin/Jobseeker/index">Add Jobseeker</a></li> 
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


               
                  <?php echo $this->Form->create('Mysubscription', array('type' => 'file', 'inputDefaults' => array('div' => false, 'label' => false), 'id' => 'Mysubscriptions', 'class' => 'form-horizontal')); ?>

                  <div class="form-group" >



                  



                        <div class="col">
                           <a href="<?php echo ADMIN_URL; ?>Jobseeker/add" class="excelbtn btn pull-right btn-success"
                              style=" margin-top: 23px; color:#fff;padding:6px 18px;border-radius: 4px;"><i
                                 class="fa fa-plus"></i>&nbsp;Add </a>

                           <!-- <a href="<?php echo ADMIN_URL; ?>Additem/view" class="excelbtn btn pull-right " target ="_blank"
                              style="padding:0;margin-top: 23px;margin-right: 5px;"><i class="fa fa-file-pdf-o"
                                 style="font-size:28px;"></i></a>

                                 <a href="<?php echo ADMIN_URL; ?>Jobseeker/edit" class="excelbtn btn pull-right "
                              style="padding:0;margin-top: 23px;margin-right: 5px;"><i class="fa fa-file-excel-o"
                                 style="font-size:28px;"></i></a> -->
                        </div>
                     </div>
                     <?php echo $this->Form->end(); ?>



                  </div>

               </div>
            </div>




         </div>
         <!-- </div>box-header -->
         <div class="box-body" style="padding:0px; margin-top:10px;" id="example23">
            <table class="table table-bordered table-striped" width="100%">
               <thead>
                  <tr>
                     <th width="4%">Id</th>
                     <th width="15%">Name</th>
                     <th width="10%">Mobile.no</th>
                     <th width="8%">Country</th>
                     <th width="8%">Address</th>
                     <th width="10%">Gender</th>
                     <th width="10%">Desprition</th>
                     <th width="10%">Skills</th> 
                     <th width="10%">Action</th>
                  </tr>
               </thead>
               <tbody>
                  <?php $page = $this->request->params['paging']['Additem']['page'];
                  $limit = $this->request->params['paging']['Additem']['perPage'];
                  $counter = ($page * $limit) - $limit + 1;
                  if (isset($job) && !empty($job)) {
                     foreach ($job as $service) {
                        ?>
                        <tr>
                           <td>
                              <?php echo $counter; ?>
                           </td>
                           <td>
                              <?php
                             echo ucfirst($service ['name']);?>
                           </td>
                           <td>
                              <?php
                             echo $service['mobile']; ?>
                           </td>
                           <td>
                              <?php
                             echo $service['country']; ?>
                           </td>
                           <td>
                              <?php
                             echo ucfirst($service['address']); ?>
                           </td>
                           <td>
                              <?php if($service['gender']== 'm')
                              {
                                 echo 'Male';
                                 
                              }
                              else 
                              {echo 'Female'; };
                              ?>

                           </td>
                           <td>
                              <?php
                             echo ucfirst($service['desprition']); ?>
                           </td>
                        
                        
                           <td>
                              <?php
                             echo $service['skills']; ?>
                           </td>
                           


                           <td> <strong>
                                 <?php
                                 echo $this->Html->link(' ', [
                                    'action' => 'edit',
                                    $service->id
                                 ], ['class' => 'fas fa-edit', 'style' => 'font-size: 16px !important;']);

                              ?>
                                 &nbsp;
                                 <?php
                                
                                 echo $this->Html->link('', [ 
                                    'action' => 'delete',
                                    $service->id
                                 ], [
                                    'class' => 'fas fa-trash-alt',
                                    'style' => 'font-size: 16px !important; color:#cd0404; margin-right:4px !important;',
                                    "onClick" => "javascript: return confirm('Are you sure do you want to delete this Item')"
                                 ]); ?>
                              </strong>
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
<!-- /.   content-wrapper -->
