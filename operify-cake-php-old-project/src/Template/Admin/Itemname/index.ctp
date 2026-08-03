 <div class="content-wrapper">
   <section class="content-header">
    <h1>
      Item Name Manager
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo SITE_URL; ?>admin/dashboards"><i class="fa fa-home"></i>Home</a></li>
      <li><a href="<?php echo SITE_URL; ?>admin/itemname">Item Name Manager</a></li>
    </ol> 
  </section> <!-- content header -->

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-xs-12">    
        <div class="box">
          <div class="box-header">
            <?php echo $this->Flash->render(); ?>
            <a href="<?php echo SITE_URL; ?>admin/itemname/add">
              <button class="btn btn-success pull-right m-top10"><i class="fa fa-plus" aria-hidden="true"></i>
              Item Name Manager</button></a>
            </div><!-- /.box-header -->
            <div class="box-body">    
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>S.No.</th>
                    <th>Item Name</th>     
                    <th>Category (Sub ctgr.)</th>
                    <th>Location (Sub loc.)</th>
                    <th>Unit</th>
                    <th>Sale Price (Unit Price)</th>
                    <th>Tax</th>
                    <th>Company</th>
                    <th>Status</th>
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
                        <td> <?php echo ucfirst($intusr['item_name']); ?></td>
                        <td> <?php echo $intusr['Maincategory']['category_name']." (".$intusr['Subcategory']['category_name'].")"; ?></td>
                        <td> <?php echo $intusr['Mainlocation']['location_name']." (".$intusr['Sublocation']['location_name'].")"; ?></td>
                        <td> <?php echo $intusr['measurementunit']['unit_name']; ?></td>
                        <td> <?php echo $intusr['sale_price']." (0.00)"; ?></td></td>
                        <td> 
                          <?php 
                            foreach ($taxvalue as $key => $value) {
                              //pr($value);
                              echo  $value['tax_name']." ".$value['tax']."%</br>";
                            }
                            
                          ?>            
                          </td>
                          <td> <?php echo $intusr['companymaster']['name']; ?></td></td>

                      
                        
                      <td><?php if($intusr['status']=='Y'){ 
                          echo $this->Html->link('Activate', [
                            'action' => 'status',
                            $intusr->id,
                            $intusr['status']	
                            ],['class'=>'label label-success']);

                        }else{ 
                          echo $this->Html->link('Deactivate', [
                          'action' => 'status',
                          $intusr->id,
                          $intusr['status']
                          ],['class'=>'label label-primary']);

                        } ?>
                      </td>
                    
                          <td> <strong><?php
                         
                            echo $this->Html->link('', [
                                'action' => 'edit',
                                $intusr->id,
                            ], ['class' => 'glyphicon glyphicon-edit', 'style' => 'font-size: 21px;']);

                          ?>
                             &nbsp;<?php
                            echo $this->Html->link('', [
                              'action' => 'delete',
                              $intusr->id
                            ],['class'=> 'glyphicon glyphicon-remove','style'=>'font-size: 21px;'	
                ,"onClick"=>"javascript: return confirm('Are you sure do you want to delete this')"]); ?>

                
                          </strong></td>
                          
                        
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
<!-- /.content -->  

</div>     
<!-- /.   content-wrapper -->  
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




