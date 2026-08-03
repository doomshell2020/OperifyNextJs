<style>
   table td, table th, table td span, table th span, table td a, table th button {
      font-size:11px !important; 
   }
   /* table td i, table th i{
      font-size:14px !important; 
   } */
</style>
<div class="content-wrapper">
   <section class="content-header">
      <h1>
        MRN (Material Receipt Note)
      </h1>
      <ol class="breadcrumb">
         <li><a href="<?php echo SITE_URL; ?>admin/Branchitemrequest"><i class="fa fa-home"></i>Home</a></li>
         <li><a href="<?php echo SITE_URL; ?>admin/Branchitemrequest">Branch Store items Request</a></li>
      </ol>
   </section>
   <!-- content header -->
   <!-- Main content -->
   <section class="content">
      <div class="row">
         <div class="col-xs-12">
            <div class="box">
               <div class="box-header" style="padding-bottom:0px;">
                  <?php echo $this->Flash->render(); ?>
                  <?php $role_id = $this->request->session()->read('Auth.User.role_id');?>
                  
               </div>
               <script>          
                  $(document).ready(function () { 
                  $("#Mysubscriptions").bind("submit", function (event) {
                  $('.lds-facebook').show();
                  $.ajax({
                        async:true,
                        data:$("#Mysubscriptions").serialize(),
                        dataType:"html",
                        type:"POST",
                        url:"<?php echo ADMIN_URL ;?>Branchitemrequest/searchitem",
                        success:function (data) {
                        $('.lds-facebook').hide();   
                        $("#example2").html(data); },
                        });
                        return false;
                     });
                  });
               </script>

               <script>
                  $( function() {
                    $( "#datepicker1" ).datepicker({
                      dateFormat: 'dd-mm-yy',
                      changeMonth: true,
                      numberOfMonths: 1
                    });
                  
                  } );
               </script>

               <!-- <?php  //echo $this->Form->create('Mysubscription',array('type'=>'file','inputDefaults'=>array('div'=>false,'label'=>false),'id'=>'Mysubscriptions','class'=>'form-horizontal')); ?>
               <div class="form-group" >
                     <div class="col-md-3">
                     <label for="inputEmail3" class="col-sm-12 control-label" style="text-align: left !important;">Category </label>
                     <div class="col-md-12">
                     <?php //echo $this->Form->input('category_id', array('class' => 'form-control', 'type' => 'select', 'options'=>$categary,'label' => false, 'empty' => 'Select Category', 'autofocus', 'autocomplete' => 'off')); ?>
                     </div> 
                  </div>
                  
                  <div class="col-md-3">
                  <label for="inputEmail3" class="col-sm-12 control-label" style="text-align: left !important;">Date<label>
                     <div class="col-md-12">
                     <?php //echo $this->Form->input('date', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off','id'=>'datepicker1')); ?>
                     </div> 
                     </div> 
                     <input type="submit" style="background-color:#00c0ef;" id="Mysubscriptions" class="btn btn4 btn_pdf myscl-btn date" value="Search">
               </div> -->
               <!-- <script>
               </div> box-header -->

               <div class="box-body">
                  <table id="" class="table table-bordered table-striped">
                     <thead>
                        <tr>
                           <th width="8%">MRN No.</th>
                           <th>Bill/Challan No</th>
                           <th width="16%">MRN Date</th>
                           <th width="12%">Purchase Order No </th>
                           <th width="7%">MRN Type</th>
                           <th width="7%">View</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php $page = $this->request->params['paging']['']['page'];
                           $limit = $this->request->params['paging']['']['perPage'];
                           $counter = ($page * $limit) - $limit + 1;
                           if(isset($st_mrn) && !empty($st_mrn)){ 
                           foreach($st_mrn as $intusr){ //pr($intusr);die; 
                        ?>
                        <tr>
                           <td><?php echo $intusr['id']; ?></td>
                           <td><?php echo $intusr['bill_challan_no']; ?></td>
                           <td><?php echo  date('d-m-Y H:i: A', strtotime($intusr['created'])); ?></td>
                           <td><?php echo $intusr['purchase_order_no']; ?></td>

                           <td><?php echo $intusr['bill_type']; ?></td>
                           <td> 
                              <div style="display:flex; align-items: center;">
                              <a title = "MRN" href = "<?php echo SITE_URL; ?>admin/branchitemrequest/mrngenerate/<?php echo $intusr['id']; ?>" style="padding:5px; background:#872076; display:flex; align-items:center; color:#fff; width:max-content; border-radius:3px; font-weight:normal;" target = "_blank">
                              <i class="far fa-clipboard" style="font-size:16px; margin-right:4px;"></i> <span style="line-height:1;"> MRN</span>
                                    </a>

                                 
                              </div>
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
         <div class="modal-body"></div>
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
   <div class="modal-dialog" style="max-width:400px !important;">
      <div class="modal-content">
         <div class="modal-body"></div>
      </div>
   </div>
</div>





