<div class="content-wrapper">
  <section class="content-header">
      <h1>
        Sold items
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo SITE_URL; ?>admin/dashboards"><i class="fa fa-home"></i>Home</a></li>
        <li><a href="<?php echo SITE_URL; ?>admin/Solditems">Sold items</a> </li>
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
                <?php $role_id = $this->request->session()->read('Auth.User.role_id');?>
                <?php if($role_id != "105"){ ?>
                  <a href="<?php echo SITE_URL ;?>admin/Solditems/add">
                    <button class="btn btn-success pull-right m-top10">
                      <i class="fa fa-plus" aria-hidden="true"></i>Add
                    </button>
                  </a>
                <?php } ?>

             
              
              </div>
              <script>
                  $(document).ready(function() {
                     $("#Mysubscriptions").bind("submit", function(event) {
                           $('.lds-facebook').show();
                           $.ajax({
                              async: true,
                              data: $("#Mysubscriptions").serialize(),
                              dataType: "html",
                              type: "POST",
                              url: "<?php echo ADMIN_URL ;?>Solditems/searchreq",
                              success: function(data) {
                                 $('.lds-facebook').hide();
                                 $("#example2").html(data);
                              },
                           });
                           return false;
                     });
                  });
               </script>

<?php  echo $this->Form->create('Mysubscription',array('type'=>'file','inputDefaults'=>array('div'=>false,'label'=>false),'id'=>'Mysubscriptions','class'=>'form-horizontal')); ?>
                  <div class="" style="padding-bottom:10px; padding-left:15px; padding-right:15px; display:flex; align-items:center; justify-content:space-start;" >
                

                       
                        <div style="margin-right:30px">
                              <label for="inputEmail3" class="ontrol-label" style="text-align: left !important;">Sale Id :-<label>
                        </div>         

                        <div style="margin-right:50px;">
                              <?php echo $this->Form->input('sale_id', array('class' => 'form-control col-sm-9 ', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off','style'=>'width:200px')); ?>
                        </div>  
                   

                        
                        <div style="margin-right:30px">
                              <label for="inputEmail3" class="ontrol-label" style="text-align: left !important;"> Student Name :-<label>
                        </div>   
                      
                        <div style="margin-right:50px;">
                        <?php echo $this->Form->input('stu_name', array('class' => 'form-control col-sm-9 ', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off','style'=>'width:200px')); ?>
         
                        </div>  

                        <div class="" style="text-align:right">
                           <input type="submit" style="background-color:#00c0ef;" id="Mysubscriptions" class="btn btn4 btn_pdf myscl-btn date" style="margin-left:auto;" value="Search">
                        </div>
                     </div> 
                  </div> 
              <div class="box-body" id="example2">
                  <table id="" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                          <th>Sale Id</th>
                          <th>Name</th>
                          <th>Description</th>
                          <th>Sale Date</th>
                          <th>View</th>
                          <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $page = $this->request->params['paging']['']['page'];
                          $limit = $this->request->params['paging']['']['perPage'];
                          $counter = ($page * $limit) - $limit + 1;
                          if(isset($sold_item) && !empty($sold_item)){
                            foreach($sold_item as $intusr){ //pr($intusr);
                              ?>
                        <tr>
                          <td><?php echo $intusr['id']; ?></td>
                          <td><?php echo $intusr['customer_name']; ?></td>
                          <td><?php echo $intusr['description']; ?></td>
                          <td><?php echo date('Y-m-d',strtotime(($intusr['pay_date']))); ?></td>

                          <td>
                              <?php if ($intusr['status'] != "Cancel"){ ?>
                              <div style= "display:flex";>
                                <a  title = "Bill PDF" href = "<?php echo SITE_URL; ?>admin/solditems/billgenerate/<?php echo $intusr['id']; ?>" style="padding:5px; background:#870606; display:flex; align-items:center; color:#fff; width:max-content; border-radius:3px; font-weight:normal; margin-right:5px;" target = "_blank">
                                <i class="far fa-file-pdf" style="font-size:16px; margin-right:4px;"></i> <span style="line-height:1;">School Receipt</span>
                                </a>
                                <a title = "Bill PDF" href = "<?php echo SITE_URL; ?>admin/solditems/salebillgenerate/<?php echo $intusr['id']; ?>" style="padding:5px; background:#06875d; display:flex; align-items:center; color:#fff; width:max-content; border-radius:3px; font-weight:normal;" target = "_blank">
                                <i class="far fa-file-pdf" style="font-size:16px; margin-right:4px;"></i> <span style="line-height:1;">Parent Receipt</span>
                                </a>
                              </div>
                              <?php }else{ ?>
                                <?php if ($intusr['status'] == "Cancel"  ){ ?>
<a   style="padding:5px 8px; background:#c50b0b; color:#fff; width:max-content; border-radius:3px; font-weight:normal;" target = "_blank" title = "Cancelled"><i class="fas fa-times"></i> Cancelled</a>
                               <?php   
                               } else { 

                                echo     $intusr['status'];
                                } ?>
                              <?php } ?>
                          </td>
                          <td> 
                        <?php 
                        if  ($intusr['status'] != "Cancel"  &&  date('Y-m-d',strtotime(($intusr['saledate']))) > '2022-03-29'){ ?> 
                            <a href = "<?php echo SITE_URL; ?>/admin/solditems/cancelrequest/<?php echo $intusr['id']; ?>" class = "cancelrequest" style="padding:5px 8px; background:#c50b0b; color:#fff; width:max-content; border-radius:3px; font-weight:normal;"  title = "Cancel"><i class="fas fa-times"></i> Cancel</a>
                              <?php } ?>
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
<script>
  $('.viewdetails').click(function(e){
    e.preventDefault();
    $('#editsorts').modal('show').find('.modal-body').load($(this).attr('href'));
  });
</script>
<div class="modal fade" id="editsorts">
  <div class="modal-dialog">
      <div class="modal-content">
        <!-- Modal Header -->
        <!-- Modal body -->
        <div class="modal-body">
        </div>
      </div>
  </div>
</div>
<script>
  $('.cancelrequest').click(function(e){
    e.preventDefault();
    $('#cancelsorts').modal('show').find('.modal-body').load($(this).attr('href'));
  });
</script>
<div class="modal fade" id="cancelsorts">
  <div class="modal-dialog">
      <div class="modal-content">
        <!-- Modal Header -->
        <!-- Modal body -->
        <div class="modal-body">
        </div>
      </div>
  </div>
</div>