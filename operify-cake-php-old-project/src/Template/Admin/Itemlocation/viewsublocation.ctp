<style>
  .modal-dialog {
   
  } 
 </style>
 
 <script>
$(document).ready(function() {
    $(".globalModals").click(function(event){
// alert($(this).attr("href"));
        $('.modal-content').load($(this).attr("href"));  //load content from href of link
        });
    });  
</script>
 
 
 
 <div class="content-wrapper">
   <section class="content-header">
    <h1>
    Sub Item Location Manager
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo SITE_URL; ?>admin/itemlocation"><i class="fa fa-home"></i>Home</a></li>
      <li><a href="<?php echo SITE_URL; ?>admin/itemlocation">Item Location Manager</a></li>
    </ol> 
  </section> <!-- content header -->

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-xs-12">    
        <div class="box">
          <div class="box-header">
            <?php echo $this->Flash->render(); ?>
            <!-- <a href="<?php// echo SITE_URL; ?>admin/itemlocation/addsublocation">
              <button class="btn btn-success pull-right m-top10"><i class="fa fa-plus" aria-hidden="true"></i>
              Add Sub Item Location</button></a> -->
            </div><!-- /.box-header -->
            <div class="box-body">    
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>S.No.</th>
                    <th>Item Sub Location Name</th>     
                    <th>Description</th>
                    <th>Action</th>
                  </tr>
                </thead>

                <tbody>
                  <?php $page = $this->request->params['paging']['']['page'];
                  $limit = $this->request->params['paging']['']['perPage'];
                  $counter = ($page * $limit) - $limit + 1;
                  $parent_id=$users[0]['parent'];
      
                  if(isset($users) && !empty($users)){ 
                    foreach($users as $intusr){ //pr($intusr);
                      ?>
                      <tr>
                        <td><?php echo $counter;?></td>
                        
                        <td>
                        <?php                          
                          echo ucfirst($intusr['location_name']); 
                        ?>



                        </td>
                        <td> 
                          <?php echo $intusr['description']; ?>
                        </td>    
                        
                          <td> <strong><?php

                          if($intusr['parent']==0){
                            echo $this->Html->link('', [
                                'action' => 'editsublocation',
                                $intusr->id,
                            ], ['class' => 'glyphicon glyphicon-edit', 'style' => 'font-size: 21px;']);

                          }else{
                            echo $this->Html->link('', [
                                'action' => 'editsublocation',
                                $intusr->id,'p_id'=>$parent_id
                            ], ['class' => 'glyphicon glyphicon-edit', 'style' => 'font-size: 21px;']);

                          } ?>

              <?php if($intusr['status']=='Y'){ 
                        echo $this->Html->link('', [
                          'action' => 'substatus',
                          $intusr->id,'Y'
                        ],['title'=>'Active','class'=>'fa fa-check-circle','style'=>'font-size: 21px !important; margin-left: 12px;     color: #36cb3c;']);
                        
                      }else{ 
                        echo $this->Html->link('', [
                          'action' => 'substatus',$intusr->id,'N'
                        ],['title'=>'Inactive','class'=>'fa fa-times-circle-o','style'=>'font-size: 21px !important; margin-left: 12px; color:#FF5722;']);
                        
                      } ?>


                             &nbsp;<?php
                            echo $this->Html->link('', [
                              'action' => 'subdelete',
                              $intusr->id,$id
                            ],['class'=> 'glyphicon glyphicon-remove','style'=>'font-size: 21px;'	
                ,"onClick"=>"javascript: return confirm('Are you sure do you want to delete this sub location record')"]); ?>


                          </strong></td>
                          <td> 

                          &nbsp;<?php
                            if($intusr['parent']==0){
                                echo $this->Html->link('Add Sublocation', [
                                  'action' => 'addsublocation',
                                  $intusr->id
                                ],['class'=>'label label-success']); 
                              }
                              ?>
                           <!-- &nbsp;<?php
                            if($intusr['parent']==0){
                                echo $this->Html->link('View Sublocation', [
                                  'action' => 'viewsublocation',
                                  $intusr->id, 
                                ],['class'=>'label label-success']); 
                              }
                              ?> -->
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




