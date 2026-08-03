<style>
table td,
table th,
table td span,
table th span,
table td a,
table th button {
    font-size: 11px !important;
}

/* table td i, table th i{
   font-size:14px !important; 
} */

</style>
<div class="content-wrapper">
   <section class="content-header">
      <h1>
         Branch Store Items Request
      </h1>
      <ol class="breadcrumb">
         <li><a href="<?php echo SITE_URL; ?>admin/dashboards"><i class="fa fa-home"></i>Home</a></li>
         <li><a href="<?php echo SITE_URL; ?>admin/Branchitemrequest">Branch Store items Request</a></li>
      </ol>
   </section>
   <!-- content header -->
   <!-- Main content -->
   <section class="content">
      <div class="row">
         <div class="col-xs-12">
            <div class="box">
               <div class="box-header" style="padding-bottom:0px; display:flex; justify-content:flex-end">
                  <?php echo $this->Flash->render(); ?>
                  <?php $role_id = $this->request->session()->read('Auth.User.role_id');?>
                  <?php if($role_id != "105"){ ?>
                  <a style="margin-left:10px;" href="<?php echo SITE_URL ;?>admin/Branchitemrequest/add">
                        <button
                           style="padding:5px 10px; background:#2286d1; display:flex; align-items:center; color:#fff; width:max-content; border-radius:3px; font-weight:normal; margin-right:4px; border:none; margin-left:auto;">
                           <i class="fa fa-plus" aria-hidden="true" style="margin-right:4px;"></i> Add
                        </button>
                        <!-- <button class="btn btn-success pull-right m-top10">
                           <i class="fa fa-plus" aria-hidden="true"></i> Add
                        </button> -->
                  </a>

                  <a style="margin-left:10px;" href="<?php echo SITE_URL ;?>admin/Branchitemrequest/viewmrn">
                        <button
                           style="padding:5px 10px; background:#2286d1; display:flex; align-items:center; color:#fff; width:max-content; border-radius:3px; font-weight:normal; margin-right:4px; border:none; margin-left:auto;">
                           <i class="fa fa-plus" aria-hidden="true" style="margin-right:4px;"></i> View MRN
                        </button>
                        <!-- <button class="btn btn-success pull-right m-top10">
                           <i class="fa fa-plus" aria-hidden="true"></i> Add
                        </button> -->
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
                              url: "<?php echo ADMIN_URL ;?>Branchitemrequest/searchreq",
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
                              <label for="inputEmail3" class="ontrol-label" style="text-align: left !important;">Requisition No. :-<label>
                        </div>         

                        <div style="margin-right:50px;">
                              <?php echo $this->Form->input('req_no', array('class' => 'form-control col-sm-9 ', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off','style'=>'width:200px')); ?>
                        </div>  
                   

                        <?php if($role_id == "105"){ ?>
                        <div style="margin-right:30px">
                              <label for="inputEmail3" class="ontrol-label" style="text-align: left !important;">Branch Name :-<label>
                        </div>   
                      
                        <div style="margin-right:50px;">
                      
            <select name="branch_name" class="form-control" style="width:200px" >
      <option value="">Select Branch Name</option>
        <?php foreach ($branches as $key=> $value) { 
             
          $test= explode("_",$value['branch_name']);
        
           ?>
         
          <option value="<?php echo $value['branch_name']; 
                          ?>"><?php echo ucfirst($test[1]); ?></option>

        <?php  } ?>
      </select> 
                        </div>  
<?php } ?>
                        <div class="" style="text-align:right">
                           <input type="submit" style="background-color:#00c0ef;" id="Mysubscriptions" class="btn btn4 btn_pdf myscl-btn date" style="margin-left:auto;" value="Search">
                        </div>
                     </div> 
                  </div> 

                  <div class="box-body" id="example2">
                     <table  class="table table-bordered table-striped">
                        <thead>
                           <tr>
                              <th width="8%">Requisition No.</th>
                              <th>School</th>
                              <?php if($role_id == "105"){ ?>
                              <th>Branch Name</th>
                              <?php } ?>
                              <th width="16%">Description</th>
                              <th width="12%">Remark</th>
                              <th width="7%">View</th>
                              <th width="8%">Status</th>
                              <th width="15%">Rq.Date</th>
                           </tr>
                        </thead>
                        <tbody>
                           <?php $page = $this->request->params['paging']['']['page'];
                              $limit = $this->request->params['paging']['']['perPage'];
                              $counter = ($page * $limit) - $limit + 1;
                              if(isset($branch_request) && !empty($branch_request)){ 
                              foreach($branch_request as $intusr){ //pr($intusr);
                               //  $branchdata = $this->Comman->branchdataget($intusr['branch_name']);  
                                 //pr($branchdata);
                                 $branch_name = explode("_",$intusr['branch_name']);
                                 //pr($branch_name);
                           ?>
                           <tr>
                              <td><?php echo $intusr['id']; ?></td>
                              <td><?php echo "Canvas International Pre School (<b>".ucfirst($branch_name[1])."</b>) <br> Unit Of Ingenious Edu Scholars Private Limited"; //$branchdata[0]['site_title']; ?></td>
                              <?php if($role_id == "105"){ ?>
                              <td><?php  if($intusr['branch_name']){ echo $intusr['branch_name']; }else{ echo "---"; } ?>
                              </td>
                              <?php } ?>
                              <td><?php  if($intusr['description']){ echo $intusr['description']; }else{ echo "---"; } ?>
                              </td>
                              <td>
                                 <div style="display:flex; align-items: center;">
                                    <span style="flex:1; margin-right:4px;">
                                       <?php  if($intusr['remark']){ echo $intusr['remark']; }else{ echo "---"; } ?>
                                    </span>
                                 </div>
                              </td>
                              <td>
                                 <div style="display:flex; align-items: center;">
                                    <a title="Details"
                                       href="<?php echo SITE_URL; ?>/admin/branchitemrequest/viewdetail/<?php echo $intusr['id']; ?>"
                                       class="viewdetails"
                                       style="padding:5px 10px; background:#2286d1; display:flex; align-items:center; color:#fff; width:max-content; border-radius:3px; font-weight:normal; margin-right:4px;">
                                       <i class="fas fa-info" style="font-size:16px;"></i>
                                    </a>

                                    <?php if($intusr['payamount']  > 0){ ?>
                                    <a title="Bill PDF"
                                       href="<?php echo SITE_URL; ?>admin/branchitemrequest/billgenerate/<?php echo $intusr['id']; ?>"
                                       style="padding:5px; background:#870606; display:flex; align-items:center; color:#fff; width:max-content; border-radius:3px; font-weight:normal; margin-right:4px;"
                                       target="_blank">
                                       <i class="far fa-file-pdf"
                                             style="font-size:16px; margin-right:4px;"></i> <span
                                             style="line-height:1;">Bill</span>
                                    </a><br><br>
                                    <?php if($role_id != "105"){ ?>
                                      <?php  $mrn_check = $this->Comman->mrncheck($intusr['id']);
                                      //pr($mrn_check);
                                      
                                      ?>
                                      <?php if(isset($mrn_check[0]['id'])){ ?>
                                    <a title="MRN"
                                       href="#"
                                       style="padding:5px; background:#872076; display:flex; align-items:center; color:#fff; width:max-content; border-radius:3px; font-weight:normal; margin-right:4px;">
                                       <i class="far fa-clipboard" style="font-size:16px; margin-right:4px;"></i> 
                                       <span style="line-height:1;">MRN Accepted</span>
                                    </a>
                                    <?php }else{  ?>
                                    
                                       <a title="MRN"
                                       href="<?php echo SITE_URL; ?>admin/branchitemrequest/createmrn/<?php echo $intusr['id']; ?>"
                                       style="padding:5px; background:#872076; display:flex; align-items:center; color:#fff; width:max-content; border-radius:3px; font-weight:normal; margin-right:4px;">
                                       <i class="far fa-clipboard" style="font-size:16px; margin-right:4px;"></i> 
                                       <span style="line-height:1;">MRN</span>
                                    </a>
                                    <?php } ?>
                                    <?php } } ?>
                                 </div>
                              </td>
                              <?php if($intusr['status']== "Approved"){
                                          $class_name = "approved_class";
                                          }else if($intusr['status']== "Cancel"){
                                             $class_name = "cancel_class";
                                          }else if($intusr['status']== "Processing"){
                                             $class_name = "processing_class";
                                          }
                                       ?>
                              <?php if($role_id == "105"){ ?>
                              <td>
                                 <?php if ($intusr['status'] == "Cancel"){ ?>
                                    <div class="cancel_head" style = "padding: 5px 8px;
                                             background: #870606;
                                             display: flex;
                                             align-items: center;
                                             color: #fff;
                                             width: max-content;
                                             border-radius: 3px;
                                             font-weight: normal;">
                                             <i class="fas fa-times" style ="margin-right:5px"></i> <?php echo $intusr['status']; ?>
                                 </div>
                                 <?php }else{ ?>
                                 <?php if($intusr['payamount']  > 0){ ?>
                                 <a href="#"
                                    style="padding:5px 8px; background:#555; display:flex; align-items:center; color:#fff; width:max-content; border-radius:3px; font-weight:normal;">
                                    Approved/Isssued</a><br>
                                 <?php }else{ ?>
                                 <a href="<?php echo SITE_URL; ?>/admin/branchitemrequest/viewitemdetail/<?php echo $intusr['id']; ?>"
                                    style="padding:5px 8px; background:#555; display:flex; align-items:center; color:#fff; width:max-content; border-radius:3px; font-weight:normal;"
                                    target="_blank"> Approve/Isssue</a><br>
                                 <?php } ?>
                                 <?php   if($intusr['approved_date']){ echo date('d-m-Y H:i: A', strtotime($intusr['approved_date'])); } ?><br>
                                 <?php  } ?>
                              </td>
                              <?php } else{ ?>
                                 <td>
                                    <span>
                                      

                                       <style>
                                          .approved_class {
                                             padding: 5px 8px;
                                             background: #2b8720;
                                             display: flex;
                                             align-items: center;
                                             color: #fff;
                                             width: max-content;
                                             border-radius: 3px;
                                             font-weight: normal;
                                          }

                                          .approved_class:before {
                                             font-family: "Font Awesome 5 Free";
                                             font-weight: 900;
                                             content: "\f00c";
                                             margin-right: 4px;
                                          }

                                          .cancel_class {
                                             padding: 5px 8px;
                                             background: #870606;
                                             display: flex;
                                             align-items: center;
                                             color: #fff;
                                             width: max-content;
                                             border-radius: 3px;
                                             font-weight: normal;
                                          }

                                          .cancel_class:before {
                                             font-family: "Font Awesome 5 Free";
                                             font-weight: 900;
                                             content: "\f00d";
                                             margin-right: 4px;
                                          }
                                        

                                          .cancel_head:before {
                                             font-family: "Font Awesome 5 Free";
                                             font-weight: 900;
                                             content: "\f00d";
                                             margin-right: 4px;
                                          }

                                          .processing_class {
                                             padding: 5px 8px;
                                             background: #067587;
                                             display: flex;
                                             align-items: center;
                                             color: #fff;
                                             width: max-content;
                                             border-radius: 3px;
                                             font-weight: normal;
                                          }

                                          .processing_class:before {
                                             font-family: "Font Awesome 5 Free";
                                             font-weight: 900;
                                             content: "\f00c";
                                             margin-right: 4px;
                                          }
                                       </style>

                                       <div class="<?php echo $class_name; ?>">
                                          <?php echo $intusr['status']; ?>
                                       </div>
                                    </span>
                                 </td>
                              <?php } ?>
                              <td>
                                 <span><?php echo  date('d-m-Y H:i: A', strtotime($intusr['created'])); ?></span><br><br>
                                 <?php if ($intusr['status'] == "Processing" && $role_id == "105"){ ?>
                                    <a href="<?php echo SITE_URL; ?>/admin/branchitemrequest/cancelrequest/<?php echo $intusr['id']; ?>" class="cancelrequest" style="padding:5px 8px; background:#c50b0b; color:#fff; width:max-content; border-radius:3px; font-weight:normal; margin-top:10px" target="_blank" title="Cancel">
                                       <i class="fas fa-times" style = "margin-right:5px"></i>Cancel
                                    </a>
                                 <?php } ?>
                              </td>
                           </tr>
                           <?php $counter++; } }else{ ?>
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
   <!-- content -->
</div>
<!-- content-wrapper -->
<script>
$('.viewdetails').click(function(e) {
   e.preventDefault();
   $('#editsorts').modal('show').find('.modal-body').load($(this).attr('href'));
});
</script>

<div class="modal fade" id="editsorts">
   <div class="modal-dialog" style="max-width:900px !important;">
      <div class="modal-content">
         <div class="modal-body"></div>
      </div>
   </div>
</div>
<script>
$('.cancelrequest').click(function(e) {
   e.preventDefault();
   $('#cancelsorts').modal('show').find('.modal-body').load($(this).attr('href'));
});
</script>
<div class="modal fade" id="cancelsorts">
   <div class="modal-dialog" style="max-width:600px !important;" >
      <div class="modal-content">
         <div class="modal-body"></div>
      </div>
   </div>
</div>