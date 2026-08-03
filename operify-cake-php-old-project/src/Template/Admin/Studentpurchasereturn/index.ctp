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
       Student Purchase Return
      </h1>
      <ol class="breadcrumb">
         <li><a href="<?php echo SITE_URL; ?>admin/dashboards"><i class="fa fa-home"></i>Home</a></li>
         <li><a href="#">Purchase Return</a></li>
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
                  <a style="margin-left:10px;" href="<?php echo SITE_URL ;?>admin/Studentpurchasereturn/add">
                        <button
                           style="padding:5px 10px; background:#2286d1; display:flex; align-items:center; color:#fff; width:max-content; border-radius:3px; font-weight:normal; margin-right:4px; border:none; margin-left:auto;">
                           <i class="fa fa-plus" aria-hidden="true" style="margin-right:4px;"></i> Add
                        </button>
                        <!-- <button class="btn btn-success pull-right m-top10">
                           <i class="fa fa-plus" aria-hidden="true"></i> Add
                        </button> -->
                  </a>

                  <?php } ?>
               </div>

               <script>
                  $(function() {
                     $("#datepicker1").datepicker({
                           dateFormat: 'dd-mm-yy',
                           changeMonth: true,
                           numberOfMonths: 1
                     });
                  });
               </script>
                  <div class="box-body">
                     <table id="" class="table table-bordered table-striped">
                        <thead>
                           <tr>
                              <th width="10%">Student PurchaseReturn No.</th>
                              <th width="20">Student Name</th>
                              <th width="30%">Description</th>
                              <th width="15%">View</th>
                              <th width="15%">Student PurchaseReturn Date</th>
                           </tr>
                        </thead>
                        <tbody>
                           <?php $page = $this->request->params['paging']['']['page'];
                              $limit = $this->request->params['paging']['']['perPage'];
                              $counter = ($page * $limit) - $limit + 1;
                              if(isset($purches) && !empty($purches)){ 
                              foreach($purches as $intusr){ //pr($intusr); die;
                        
                           ?>
                           <tr>
                              <td><?php echo $intusr['id']; ?></td>
                              <td><?php echo ucfirst($intusr['student']['fname'].''.$intusr['student']['lname']); ?></td>
                              <td><?php  if($intusr['description']){ echo ucfirst($intusr['description']); }else{ echo "---"; } ?>
                              </td>
                              <td>
                                 <div style="display:flex; align-items: center;">
                                    <a title="Details"
                                       href="<?php echo SITE_URL; ?>/admin/Studentpurchasereturn/viewdetail/<?php echo $intusr['id']; ?>"
                                       class="viewdetails"
                                       style="padding:5px 10px; background:#2286d1; display:flex; align-items:center; color:#fff; width:max-content; border-radius:3px; font-weight:normal; margin-right:4px;">
                                       <i class="fas fa-info" style="font-size:16px;"></i>
                                    </a>

                                    <a title="Bill PDF"
                                       href="<?php echo SITE_URL; ?>admin/Studentpurchasereturn/billgenerate/<?php echo $intusr['id']; ?>"
                                       style="padding:5px; background:#870606; display:flex; align-items:center; color:#fff; width:max-content; border-radius:3px; font-weight:normal; margin-right:4px;"
                                       target="_blank">
                                       <i class="far fa-file-pdf"
                                             style="font-size:16px; margin-right:4px;"></i> <span
                                             style="line-height:1;">Bill</span>
                                    </a>
                                 </div>
                              </td>
                              <td>
                                 <span><?php echo  date('d-m-Y H:i: A', strtotime($intusr['created'])); ?></span><br><br>
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