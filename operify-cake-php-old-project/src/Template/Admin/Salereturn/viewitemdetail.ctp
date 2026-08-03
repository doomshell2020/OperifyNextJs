<style>
   .input_fields_wrap .form-control {
   margin-bottom: 15px;
   }

   .modal-header {
       background-color: #2d95e3 !important;
       display: flex;
       align-items: center;
   }
   .cash_pay, .cheque_pay, .online_pay {
       background:#21b354;
       padding:5px 15px;
       color:#fff !important;
       margin-left:10px;
       border-radius:3px;
   }
   .control-label {
       margin-bottom:8px !important;
   }

   .btn.btn-primary.pull-left {
       background-color:#21b354;
   }
</style>
<div class="content-wrapper">
   <!-- Content Header (Page header) -->
   <section class="content-header">
      <h1>
      Sale Return
         <?php 
            // pr($item);die;
            ?>
      </h1>
      <ol class="breadcrumb">
         <li><a href="<?php echo SITE_URL; ?>admin/Salereturn"><i class="fa fa-home"></i>Home</a></li>
      </ol>
   </section>
   <!-- Main content -->
   <section class="content">
      <div class="row">
         <!-- right column -->
         <div class="col-md-12">
            <!-- Horizontal Form -->
            <div class="box box-info" style="padding:15px;">
               <!-- /.box-footer -->
               <table class="table table-bordered table-striped">
                  <thead style="background:#333; color:#fff;">
                     <tr>
                        <th>S.No.</th>
                        <!-- <th>Category</th> -->
                       
                        <th>Item Name</th>
                        <th>Stock Available</th>
                        <th>Unit Price</th>
                        <th>Quantity</th>
                        <th>Item Amount</th>
                        <th>Discount</th>
                        <th>Tax</th>
                        <th>Tax Amount</th>
                        <th>Taxable Amount</th>
                        <th>Amount</th>
                        <!-- 
                           <th>Action</th> -->
                     </tr>
                  </thead>
                  <tbody>
                     <style>
                        .stock_aval td{background:#f70200;}
                        </style>
                  <?php $stock_check_data  = 0; ?>
                     <?php $page = $this->request->params['paging']['']['page'];
                        $limit = $this->request->params['paging']['']['perPage'];
                        $counter = ($page * $limit) - $limit + 1;
                       if(isset($requestdetails) && !empty($requestdetails)){ 
                          foreach($requestdetails as $intusr){ //pr($intusr);
                            ?>
                            <?php  $gname=$this->Comman->finditems($intusr['item_id']); 
                              $stock_avail = $this->comman->stockavailable($intusr['additem']['id']);
                              $stock_avail_data = $stock_avail['stock_available'];
                              if($stock_avail_data == "0"){
                                 $stock_check_data = 1;
                              }
                             ?>
                             <?php    if($stock_avail_data == "0"){ ?>
                     <tr class="stock_aval">
                   <?php }else{ ?>
                     <tr class="">
                  <?php } ?>

                        <td><?php echo $counter;?></td>
                        <!-- <td> <?php //echo $intusr['category_name'] ?></td> -->
                        <td> <?php echo ucfirst(strtolower($intusr['additem']['item_name'])); ?></td>
                        
                   <td> <?php echo $stock_avail_data; ?></td> 
                        <td align="right"> ₹ <?php  echo sprintf('%.2f', $intusr['item_amount']);
                        ?></td>
                        <td align="right"> <?php echo $intusr['item_qty']; ?></td>
                        <td align="right"> <?php  $totalitem_amount=$intusr['item_amount']*$intusr['item_qty'];
                           echo  sprintf('%.2f', $totalitem_amount);
                        ?></td>
                        <td align="right">  <?php
                                  if($intusr['discount']){
                                    $discount =$intusr['discount']*$intusr['item_qty'];
                                }else{
                                    $discount = 0;
                                }
                                echo  sprintf('%.2f', $discount); ?></td>
                        <td align="right"> <?php 
                                    $tax = $intusr['item_tax'];
                                    
                              
                               echo sprintf('%.2f', $tax)."%";
                                 ?>
                                 <?php 
                                 $total=$intusr['item_amount']*$intusr['item_qty']- $discount;
                                // echo $total; die;

                                 $total_tax = $total*$tax/100;
                                 //echo $total_tax; die;
                                 ?>
                        </td>
                        <td align="right">₹ <?php echo  sprintf('%.2f', $total_tax); ?></td>
                        <td align="right">₹ <?php echo  sprintf('%.2f', $total); ?></td>
                        <td align="right">₹ <?php echo  sprintf('%.2f', $total+$total_tax); ?></td>
                        <?php  
                           $total_unit += $intusr['additem']['sale_price'];
                           $total_qty += $intusr['item_qty'];
                           $total_amount +=$total+$total_tax ; 
                           $totaltem_amount +=$totalitem_amount;
                           $totaltaxable += $total;
                           $total_taxss += $total_tax;
                           ?>
                        <!-- <td>
                           &nbsp;<?php
                              /*  echo $this->Html->link('', [
                                 'action' => 'delete',
                                 $intusr->id
                               ],['class'=> 'glyphicon glyphicon-remove','style'=>'font-size: 21px;'	
                              ,"onClick"=>"javascript: return confirm('Are you sure do you want to delete this Item')"]); */ ?>
                           </strong></td> -->

                     </tr>
                     <?php $counter++; } ?>
                     <tr>
                     <td></td>
                     <td></td>
                        <td><b>Total</b></td>
                        <td align="right"><b>₹ <?php echo $total_unit; ?></b></td>
                        <td align="right"></i><b><?php echo $total_qty; ?></b></td>
                        <td align="right"></i><b>₹<?php echo $totaltem_amount; ?></b></td>
                        <td> </td>
                        <td></td>
                        <td align="right"></i><b>₹<?php echo sprintf('%.2f',  $total_taxss); ?></b></td>
                        <td align="right"></i><b>₹<?php echo $totaltaxable; ?></b></td>
                        <td align="right"> <b>₹<?php echo sprintf('%.2f',   round($total_amount)); ?></b></td>
                     </tr>
                     <?php }else {   ?>
                     <tr>
                        <td colspan="4" style="text-align:center;">
                           <h4> No Item Added </h4>
                        </td>
                     </tr>
                     <?php } ?>
                  </tbody>
               </table>
               <?php echo $this->Flash->render(); ?>
               <div class="box-header with-border">
                  <!-- <h3 class="box-title"><i class="fa fa-plus-square" aria-hidden="true"></i> <?php //if(isset($location['id'])){ echo 'Edit Post New'; }else{ echo 'Create New Item';} ?></h3> -->
               </div>
               <!-- /.box-header -->
               <!-- form start -->
               <div class="box-body" style="padding:10px 0px;">
                  <?php echo $this->Form->create($item, array(
                     'class'=>'form-horizontal',
                     'enctype' => 'multipart/form-data',
                     'validate'
                     )); ?>
                  <div class="row" style="display: flex; align-items: end; flex-wrap:wrap;">
                  
                     <div class="col-md-4">
                        <label for="inputEmail3" class=" control-label"
                           style="text-align: left !important;">Name</label>
                        <div class="">
                           <?php $customer_data = explode("_",$approve_req['branch_name']);
                                 // pr($customer_data)
                            $cname=$this->Comman->findcompanyname($customer_data[1]); 
                          
                              ?>
                           <?php echo $this->Form->input('customer_name', array('class' => 'form-control category_id','type'=>'text','value'=>$cname[0]['company_name'],'label'=>false,'empty'=>'Select Category','autofocus','autocomplete'=>'off','readonly')); ?>
                        </div>
                     </div>
                     <div class="col-md-4">
                        <label for="inputEmail3" class=" control-label"
                           style="text-align: left !important;">Sale Date</label>
                        <div class="">
                           <?php $current_Date = date('d-m-Y'); ?>
                           <?php echo $this->Form->input('sale_date', array('class' => 'form-control category_id','type'=>'text','value'=>$current_Date,'label'=>false,'empty'=>'Select Category','autofocus','autocomplete'=>'off','id'=>'datepicker1','autocomplete'=>'off','readonly')); ?>
                        </div>
                     </div>
                     <div class="col-md-4">
                        <label for="inputEmail3" class=" control-label"
                           style="text-align: left !important;">Upload Description</label>
                        <div class="">
                           <?php echo $this->Form->input('upload_description', array('class' => 'form-control', 'type' => 'file',   'label' => false,  'autofocus', 'autocomplete' => 'off')); ?> 
                        </div>
                     </div>
                     <div class="col-md-12">
                        <label for="inputEmail3" class=" control-label"
                           style="text-align: left !important;">Remark</label>
                        <div class="">
                           <?php echo $this->Form->input('description', array('class' => 'form-control', 'type' => 'textarea',   'label' => false,  'autofocus', 'autocomplete' => 'off')); ?>
                        </div>
                     </div>
                     <div class="col-md-12" style = "margin-top: 7px;">
                        <!-- <a href = "<?php //echo SITE_URL; ?>admin/branchitemrequest/payamount/<?php //echo $id; ?>" class = "btn btn-success payrequest">Pay</a> -->
                        <?php
                        
                       if($stock_check_data == "0"){
                           if(isset($item['id'])){
                              echo $this->Form->submit(
                              'Update', 
                              array('class' => 'btn btn-info pull-right', 'title' => 'Update')
                              ); }else{ 
                              echo $this->Form->submit(
                              'Submit', 
                              array('class' => 'btn btn-info pull-right', 'title' => 'Add')
                              );
                              }
                           }else{
                              
                           }
                  
                           ?>
                    <!-- <h5> <b> store under maintenance </b> </h> -->
                     </div>
                  </div>
               </div>
               <?php echo $this->Form->end(); ?>
            </div>
         </div>
      </div>
      <!--/.col (right) -->
</div>
<!-- /.row -->
</section>
<!-- /.content -->
</div>
<script>
   $( function() {
     $( "#datepicker1" ).datepicker({
       dateFormat: 'dd-mm-yy',
       changeMonth: true,
       numberOfMonths: 1
     });
     
   
   } );
</script>

</div>
</div>
<!-- Relation Beetween Location and Sublocation  -->
<script>
   $(document).ready(function() {
       $('.category_request').on('click', function(e) {
   
           e.preventDefault();
           var category_id = $('.category_id').val();
           var category_qty = $('.category_qty').val();
   
           $(".error").hide();
   
           var hasError = false;
           if (category_id == '') {
               $(".category_id").after(
                   '<span class="error" style = "color:red;">Select Atleast one category</span>');
               hasError = true;
           }
   
           if (category_qty == '' || category_qty <= 0) {
               $(".category_qty").after('<span class="error" style = "color:red;">Enter Qty </span>');
               hasError = true;
           }
           if (hasError == true) {
               return false;
           }
   
           $.ajax({
               type: 'POST',
               url: '<?php echo SITE_URL;?>/admin/Salereturn/categoryrequest',
               data: {
                   'category_id': category_id,
                   'category_qty': category_qty
               },
               success: function(data) {
                   location.reload();
               },
   
           });
       });
   
   
   
       $('.item_request').on('click', function(e) {
           e.preventDefault();
   
           var item_id = $('.item_id').val();
           var item_qty = $('.item_qty').val();
   
           $(".error").hide();
   
           var hasError = false;
           if (item_id == '') {
               $(".item_id").after(
                   '<span class="error" style = "color:red;">Select Atleast one category</span>');
               hasError = true;
           }
   
           if (item_qty == '' || item_qty <= 0) {
               $(".item_qty").after('<span class="error" style = "color:red;">Enter Qty </span>');
               hasError = true;
           }
           if (hasError == true) {
               return false;
           }
   
           $.ajax({
               type: 'POST',
               url: '<?php echo SITE_URL;?>/admin/Salereturn/itemrequest',
               data: {
                   'item_id': item_id,
                   'item_qty': item_qty
               },
               success: function(data) {
                   location.reload();
               },
   
           });
       });
   
   });
   
   $(document).ready(function() {
       $('#location-name').on('change', function() {
           var id = $('#location-name').val();
           // alert(id);
           $.ajax({
               type: 'POST',
               url: '<?php echo SITE_URL;?>/admin/additem/find_sublocation',
               data: {
                   'id': id
               },
               success: function(data) {
                   $('#sub-location').empty();
                   $('#sub-location').html(data);
               },
   
           });
       });
   });
</script>
<!-- end  -->
<script>
   $(function() {
       $("#imagename").change(function() {
           // alert('hello');
           var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.pdf|.jpg|.png)$/;
           if (regex.test($(this).val().toLowerCase())) {
               return true;
   
           } else {
               $('#imagename').val('');
               alert("Please upload pdf/jpg/png files.");
           }
       });
   });
</script>
<script type="text/javascript">
   $(document).ready(function() {
       $("#category_ids").on('change', function() {
           var id = $(this).val();
           $("#subcategory").find('option').remove();
           //$("#city").find('option').remove();
           if (id) {
               var dataString = id;
               $.ajax({
                   type: "POST",
                   url: '<?php echo SITE_URL;?>/admin/additem/getsubcategory',
                   data: {
                       'dataString': id
                   },
                   cache: false,
                   success: function(html) {
                       //alert(html);
                       $('<option>').val("").text("Select Sub Category").appendTo($(
                           "#subcategory"));
                       $.each(html, function(key, value) {
                           $('<option>').val(key).text(value).appendTo($(
                               "#subcategory"));
                       });
                   }
               });
           }
       });
   });
</script>
<script type="text/javascript">
   $(document).ready(function() {
       $("#location").on('change', function() {
           var id = $(this).val();
           $("#sublocation").find('option').remove();
           //$("#city").find('option').remove();
           if (id) {
               var dataString = id;
               $.ajax({
                   type: "POST",
                   url: '<?php echo SITE_URL;?>/admin/additem/getsublocation',
                   data: {
                       'dataString': id
                   },
                   cache: false,
                   success: function(html) {
                       //alert(html);
                       $('<option>').val("").text("Select Sub Location").appendTo($(
                           "#sublocation"));
                       $.each(html, function(key, value) {
                           $('<option>').val(key).text(value).appendTo($(
                               "#sublocation"));
                       });
                   }
               });
           }
       });
   });
</script>
<script>
   $('#mrp').on('change', function() {
       var amou = $('#saleprice').val();
       if ($(this).val() < amou) {
           alert("Mrp should be greater then sale price");
           $(this).val('');
       }
   });
</script>
<script>
   $('#saleprice').on('change', function() {
       var mrp = $('#mrp').val();
       if ($(this).val() > mrp) {
           alert("Sale Price should be less then mrp");
           $(this).val('');
       }
   });
</script>
<style>
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
   #testUL {
   position: relative;
   }
   #testUL ul li a {
   color: black;
   }
</style>
<script>
   $(function() {
       $('.secrh-retail').bind('keyup', function() {
           var pos = $(this).val();
           //alert(pos);
           var check = 0;
           //var catid=$('#subcategory').val();
           //alert(pos);
           $('#testUL').show();
           $('#retail_ids').val('');
           var count = pos.length;
           if (count > 0) {
               $.ajax({
                   type: 'POST',
                   url: '<?php echo ADMIN_URL; ?>Salereturn/getitemname',
                   data: {
                       'fetch': pos,
                       'check': check
                   },
                   success: function(data) {
                       //alert(data);
                       $('#testUL ul').html(data);
                   },
               });
           } else {
               $('#testUL').hide();
           }
       });
   });
</script>
<script>
   function cllbckretail(name, id) {
       $('.secrh-retail').val(name);
       $('#testUL').hide();
       //alert(cid);
       $.ajax({
           type: 'POST',
           url: '<?php echo ADMIN_URL; ?>storeitems/getitemdetail',
           data: {
               'fetch': id
           },
           success: function(data) {
               //console.log(data);
               var json = $.parseJSON(data);
               //alert(json.sale_price);
               $('#retail_ids').val(json.id);
               $('#sale-price').val(json.sale_price);
           },
       });
   
   }
</script>
<?php $message=$this->Flash->render('pay_request'); ?>
<?php if($message){  ?>
<script>
   $( document ).ready(function() {
       $('#paysorts').modal('show');
   //$('#myModal').modal('show');
   });
</script>
<?php } ?>
<script>
   $('#discount').on('change', function() {
       var discount_amount = $(this).val();
       
      var pay_amount = $('#pay-amount').val();
       if(discount_amount){
         var disocunt_amount_data =  pay_amount-discount_amount;
         var pay_amount = $('#pay-amount').val(disocunt_amount_data);
       }else{
          //alert(discount_amount);
         var pay_amount = $('#pay-amount').val('<?php echo sprintf('%.2f',round($total_amount)); ?>');
       }
     
   });
</script>