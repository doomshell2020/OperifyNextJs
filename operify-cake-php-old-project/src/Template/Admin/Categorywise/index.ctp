
<div class="content-wrapper" style="min-height: 410px;">

  <section class="content-header">
      <h1>
      Category Wise Items
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo SITE_URL; ?>admin/categorywise"><i class="fa fa-home"></i>Home</a></li>
     
      </ol>
  </section>
 
  <section class="content">
      <div class="row">
         <div class="col-xs-12">
           
            <div class="box">
               <div class="box-header">
                  <?php echo $this->Flash->render(); ?>
                  <script>          
                  $(document).ready(function () { 
                  $("#Mysubscriptions").bind("submit", function (event) {
                  $('.lds-facebook').show();
                  $.ajax({
                  async:true,
                  data:$("#Mysubscriptions").serialize(),
                  dataType:"html",
                  type:"POST",
                  url:"<?php echo ADMIN_URL ;?>categorywise/searchitem",
                  success:function (data) {
                   $('.lds-facebook').hide();   
                       $("#example2").html(data); },
                       });
                       return false;
                     });
                   });
               </script>

               <?php  echo $this->Form->create('Mysubscription',array('type'=>'file','inputDefaults'=>array('div'=>false,'label'=>false),'id'=>'Mysubscriptions','class'=>'')); ?>

               <div class="form-group pull-left" style="display:flex; align-items:flex-end; margin-bottom:0px;" >
                  <div style="margin-right:10px;">
                    <label for="inputEmail3" class="control-label">Category Name</label>
                     <?php echo $this->Form->input('category_name',array('class'=>'form-control','label' =>false,'placeholder'=>'Enter Category Name','autocomplete'=>'off')); ?>  
                  </div>
                  <input type="submit" style="background-color:#00c0ef; color:#fff" id="Mysubscriptions" class="btn btn4 btn_pdf myscl-btn date" value="Search">      
               </div>
               <?php echo $this->Form->end(); ?>
               <div style="clear-both"></div>
              
                  <a href="<?php echo SITE_URL; ?>admin/categorywise/add">
                  <button class="btn btn-success pull-right m-top10"><i class="fa fa-plus" aria-hidden="true"></i>
                  Add Category Wise Items</button></a>
               </div>
              <div class="box-body" id="example2">
                  <table id="" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                          <th>Sr.No</th>
                          <!-- <th>Group Id</th> -->
                          <th>Name</th>
                        
                          <th>Action</th>
                        </tr>
                    </thead>
                    <?php $page = $this->request->params['paging']['']['page'];
                  $limit = $this->request->params['paging']['']['perPage'];
                  $counter = ($page * $limit) - $limit + 1;
                  if(isset($category_wise) && !empty($category_wise)){ 
                    foreach($category_wise as $intusr){ //pr($intusr);
                      ?>
                      <tr>
                      <td><?php echo $counter;?></td>                        
                      <!-- <td><?php //echo $intusr['id'];?></td>                         -->
                      <td> <?php echo ucfirst($intusr['itemcategory']['category_name']); ?></td>
                   
    
                          <td> <strong><?php
                         
                            echo $this->Html->link('', [
                                'action' => 'edit',
                                $intusr->id,
                            ], ['class' => 'glyphicon glyphicon-edit', 'style' => 'font-size: 21px;']);

                          ?>
                            <?php
                            echo $this->Html->link('', [
                              'action' => 'delete',
                              $intusr->category_id,
                            ],['class'=> 'fas fa-trash-alt','style'=>'font-size: 21px; color:#cd0404;'
                ,"onClick"=>"javascript: return confirm('Are you sure do you want to delete this Item')"]); ?>

                          </strong></td>
                          
                        
                        </tr>
                        <?php $counter++; } }else{ ?>


                        <?php } ?>  
      </tbody>
                  </table>
              </div>
              <!-- /.box-body -->
            </div>
        </div>
      </div>
  </section>
  <!-- /.content -->
</div>