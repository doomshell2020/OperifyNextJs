 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
     <!-- Content Header (Page header) -->
     <section class="content-header">
         <h1>
             Spam IP
         </h1>
         <ol class="breadcrumb">
             <li><a href="<?php echo ADMIN_URL; ?>spam/index"><i class="fa fa-home"></i>Home</a></li>
             <li><a href="<?php echo ADMIN_URL; ?>spam">Manage Spam IP</a></li>
         </ol>
     </section>

     <!-- Main content -->
     <section class="content">
         <div class="row">
             <div class="col-xs-12">

                 <div class="box">
                     <div class="box-header">
                         <a href="<?php echo ADMIN_URL; ?>spam/addiprange" class="btn btn-success pull-right">Add </a>


                         <!-- /.box-header -->
                         <?php echo $this->Flash->render(); ?>
                     </div>
                     <div class="box-body">
                         <table id="example1" class="table table-bordered table-striped">
                             <thead>
                                 <tr>
                                     <th>S.No.</th>
                                     <th>Date</th>
                                     <th>IP Range</th>
                                     <th>Action</th>

                                 </tr>
                             </thead>
                             <tbody>
                                 <?php $page = $this->request->params['paging']['Services']['page'];
                                    $limit = $this->request->params['paging']['Services']['perPage'];
                                    $counter = ($page * $limit) - $limit + 1;
                                    if (isset($ips) && !empty($ips)) {
                                        foreach ($ips as $key => $service) {  ?>
                                         <tr>
                                             <td><?php echo $counter; ?></td>
                                             <td><?php echo date('d-M-Y', strtotime($service['created_at'])); ?>
                                             <td><?php echo $service['start_ip'] . ' - ' . $service['end_ip']; ?>
                                             <td> <?php
                                                    echo $this->Html->link('', [
                                                        'action' => 'delete',
                                                        $service->id,
                                                    ], [
                                                        'class' => 'fas fa-trash-alt',
                                                        'style' => 'font-size: 21px; color:#cd0404;',
                                                        "onClick" => "javascript: return confirm('Are you sure do you want to delete this IP Range')"
                                                    ]); ?></td>

                                             </td>

                                         </tr>
                                     <?php $counter++;
                                        }
                                        // die;
                                    } else { ?>
                                     <tr>
                                         <td>No Data Available</td>
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