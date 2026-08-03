<style>
   .input_fields_wrap .form-control {
   margin-bottom: 15px;
   }
</style>
<div class="content-wrapper">
   <!-- Content Header (Page header) -->
   <section class="content-header">
      <h1>
      Student Purchase Return
         <?php 
            // pr($item);die;
            ?>
      </h1>
      <ol class="breadcrumb">
         <li><a href="<?php echo SITE_URL; ?>admin/Studentpurchasereturn"><i class="fa fa-home"></i>Home</a></li>
      </ol>
   </section>
   <!-- Main content -->
   <section class="content">
      <div class="row">
         <!-- right column -->
         <div class="col-md-12">
            <!-- Horizontal Form -->
            <div class="box box-info">
               <?php echo $this->Flash->render(); ?>
               <div class="box-header with-border" style="padding:0px; border-bottom:0px;">
                  <!-- <h3 class="box-title"><i class="fa fa-plus-square" aria-hidden="true"></i> <?php //if(isset($location['id'])){ echo 'Edit Post New'; }else{ echo 'Create New Item';} ?></h3> -->
               </div>
               <!-- /.box-header -->
               <!-- form start -->
               <div class="box-body" style="padding:0px; margin-bottom:10px;">
                    
                    <?php echo $this->Form->create($item, array(
                        'class'=>'form-horizontal',
                        'enctype' => 'multipart/form-data',
                        'validate'
                        )); ?>
                    <div class="container-fluid">
                        <div class="row" style="display: flex; align-items: flex-end;">
                            <div class="col-md-4">
                                <label for="inputEmail3" class="control-label" style="text-align: left !important;">Category</label>



                                <div class="">

                                <select name="category_name" class="form-control category_id" autofocus="autofocus" autocomplete="off" id="category-name">
                                <option value="">Select Category</option>
                                <?php foreach($categary as $val){ ?>
                                <option value="<?php echo $val['category_id']; ?>"><?php echo $val['itemcategory']['category_name']; ?></option>
                                <?php } ?>
                                </select>

                               

                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="inputEmail3" class="control-label"
                                style="text-align: left !important;">Quantity</label>
                                <div class="">
                                <?php echo $this->Form->input('quantity', array('class' => 'form-control category_qty', 'type' => 'number','label' => false,  'autofocus', 'autocomplete' => 'off','maxlength'=>3)); ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <a href  = "" class="btn btn-danger category_request">Add</a>
                            </div>
                        </div>
                    </div>
               </div>
               <div class="box-body" style="padding:0px; margin-bottom:10px;">
               <div class="container-fluid">
                  <div class="row" style="display: flex; align-items: end; flex-wrap:wrap;">
                     <div class="col-md-4">
                        <label for="inputEmail3" class="control-label" style="text-align: left !important;">Item Name</label>
                        <div class="">
                           <input type="hidden" name="item_id" id="retail_idsd">
                           <?php echo $this->Form->input('item_name', array('class' => 'form-control secrh-retail item_id', 'type' => 'text', 'label' => false, 'placeholder' => 'Enter Item name', 'autofocus', 'autocomplete' => 'off','id'=>'itemname')); ?>
                           <div id="testUL" style="display:none;">
                              <ul></ul>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-4">
                        <label for="inputEmail3" class="control-label"
                           style="text-align: left !important;">Quantity</label>
                        <div class="">
                           <?php echo $this->Form->input('quantity', array('class' => 'form-control item_qty ', 'type' => 'number',   'label' => false,  'autofocus', 'autocomplete' => 'off','maxlength'=>3)); ?>
                        </div>
                     </div>
                     <div class="col-md-4">
                        <a href  = "" class="btn btn-danger item_request">Add</a>
                     </div>

                         <div class="col-md-4">
                            
                                <label for="inputEmail3" class="control-label"
                                 style="text-align: left !important;">Student Name</label>
                                    <input type="hidden" name="stu_name" id="retail_id" value="id">

                                    <?php echo $this->Form->input('name', array('class' => 'form-control secrh-students stu_name', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'placeholder' => 'Enter Student Name', 'required')); ?>
                                    <div id="test" style="display:none;">
                                        <ul></ul>
                                    </div>
                            </div>
                            <div class="col-md-4">
                            
                            <label for="inputEmail3" class="control-label"
                             style="text-align: left !important;">Invoice No</label>
                
                                <?php echo $this->Form->input('invoice_no', array('class' => 'form-control ', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'placeholder' => 'Enter Invoice No', 'required')); ?>
                               
                                </div>
                    


                     <div class="col-md-12" style="margin-top:10px;">
                        <label for="inputEmail3" class=" control-label" style="text-align: left !important;">Description</label>
                        <div class="">
                           <?php echo $this->Form->input('description', array('class' => 'form-control', 'type' => 'textarea',   'label' => false,  'autofocus', 'autocomplete' => 'off','required')); ?>
                        </div>
                     </div>
                  </div>
                </div>
              


                  <div class="col-md-12" style="margin-top:10px;">
                     <?php
                        if(isset($item['id'])){
                          echo $this->Form->submit(
                            'Update', 
                            array('class' => 'btn btn-info pull-left', 'title' => 'Update')
                          ); }else{ 
                            echo $this->Form->submit(
                              'Submit', 
                              array('class' => 'btn btn-info pull-left', 'title' => 'Add')
                            );
                          }
                          ?>
                  </div>
             
               </div>
               <!-- /.box-footer -->
               <div class="table-responsive" style="margin:0px 15px;">
               <table class="table table-bordered table-striped">
                  <thead style= "background:#333; color:#fff;">
                     <tr>
                        <th>S.No.</th>
                        <!-- <th>Category</th> -->
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>Action</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php $page = $this->request->params['paging']['']['page'];
                        $limit = $this->request->params['paging']['']['perPage'];
                        $counter = ($page * $limit) - $limit + 1;
                        if(isset($temp_item) && !empty($temp_item)){ 
                          foreach($temp_item as $intusr){ //pr($intusr); die;
                            ?>
                            <?php  $gname=$this->Comman->finditems($intusr['item_id']); 
                               //pr($gname); die;
                             ?>
                     <tr>
                        <td><?php echo $counter;?></td>
                      
                     
                        <td>    <div style ="display:flex;align-items:center"> <?php echo ucfirst($gname[0]['item_name']); ?>
          
                                </div>
                    
                    </td>
                        <td> <?php echo $intusr['quantity']; ?></td>
                        <td> 
                           &nbsp;<?php
                              echo $this->Html->link('', [
                                'action' => 'delete',
                                $intusr->id
                              ],['class'=> 'fas fa-trash-alt','style'=>'font-size: 21px; color:#cf1212;'	
                              ,"onClick"=>"javascript: return confirm('Are you sure do you want to delete this Item')"]); ?>
                           </strong>
                        </td>
                     </tr>
                     <?php $counter++; } ?>

                     <input type= "hidden" name = "category_id" value = "<?php echo $temp_item[0]['category_id']; ?>">
                     <?php // TOP ?>
                  <?php if($temp_item_top){ ?>
                   
                        <tr>
                        <td>   <?php echo $counter; ?></td>
                        <td style = "width:75%">
                            <strong style="text-align:left !important; display:block !important">TOP </strong>
                            <div style = "display:flex;flex-wrap:wrap">

                             <?php for ($x = 1; $x <= $temp_item[0]['quantity']; $x++) { ?> 
                            <select class ="form-control" style = "width:170px; margin-right:10px; margin-bottom:10px" name= "top_product[]" required>
                            <option value = "">--Select--</option>

                            <?php foreach($temp_item_top as $topval){ //pr($topval); 
                                 $gnamess=$this->Comman->findtopitems($topval['item_id']); 
                                 //pr($gnamess);
                                ?>
                                            <option value = "<?php echo $topval['id']; ?>"><?php echo $gnamess[0]['item_name']; ?></option>
                                            <?php } ?>
                     </select>  
                     
                     <?php } ?>  
                            </div>
                    
                    </td>
                     <td>   <?php echo $temp_item[0]['quantity']; ?></td>
                     <td> 
                           &nbsp;<?php
                              echo $this->Html->link('', [
                                'action' => 'topitemdelete',
                                $topval->id
                              ],['class'=> 'fas fa-trash-alt','style'=>'font-size: 21px; color:#cf1212;'	
                              ,"onClick"=>"javascript: return confirm('Are you sure do you want to delete this Item')"]); ?>
                           </strong>
                        </td>
                     </tr>
                     <?php } ?>
                    
                     <?php // BOTTOM ?>
                     <?php if($temp_item_bottom) {?>

                     <tr>
                        <td>   <?php echo $counter+1; ?></td>
                        <td>
                        <strong style="text-align:left !important; display:block !important">Bottom </strong>
                        <div style = "display:flex;flex-wrap:wrap">
                             <?php for ($x = 1; $x <= $temp_item[0]['quantity']; $x++) { ?> 

                            <select class ="form-control" style = "width:170px; margin-right:10px; margin-bottom:10px" name= "bottom_product[]" required>
                            <option value = "">--Select--</option>
                            <?php foreach($temp_item_bottom as $topval){ //pr($topval); 
                                
                                $item_bottom=$this->Comman->findbottomitems($topval['item_id']); 


                                ?>
                                            <option value = "<?php echo $topval['id']; ?>"><?php echo $item_bottom[0]['item_name']; ?></option>
                                            <?php } ?>
                     </select>  
                                
                     <?php } ?>   
                     </div>
                    </td>
                     <td>   <?php echo $temp_item[0]['quantity']; ?></td>
                     <td> 
                           &nbsp;<?php
                              echo $this->Html->link('', [
                                'action' => 'bottomitemdelete',
                                $topval->id
                              ],['class'=> 'fas fa-trash-alt','style'=>'font-size: 21px; color:#cf1212;'	
                              ,"onClick"=>"javascript: return confirm('Are you sure do you want to delete this Item')"]); ?>
                           </strong>
                        </td>
                     </tr>
                     <?php } ?>


                     <?php // Socks ?>
                    
                  <?php   if($temp_item_socks){ ?>
                   
                        <tr>
                        <td>   <?php echo $counter+2; ?></td>
                        <td style = "width:75%">
                            <strong style="text-align:left !important; display:block !important">Socks </strong>
                            <div style = "display:flex;flex-wrap:wrap">

                             <?php for ($x = 1; $x <= $temp_item[0]['quantity']; $x++) { ?> 
                            <select class ="form-control" style = "width:170px; margin-right:10px; margin-bottom:10px" name= "socks_product[]" required>
                            <option value = "">--Select--</option>

                            <?php foreach($temp_item_socks as $topval){ //pr($topval); 
                                
                                $item_socks=$this->Comman->findsocksitems($topval['item_id']); 

                                ?>
                                            <option value = "<?php echo $topval['id']; ?>"><?php echo $item_socks[0]['item_name']; ?></option>
                                            <?php } ?>
                     </select>  
                     
                     <?php } ?>  
                            </div>
                    
                    </td>
                     <td>   <?php echo $temp_item[0]['quantity']; ?></td>
                     <td> 
                           &nbsp;<?php
                              echo $this->Html->link('', [
                                'action' => 'socksitemdelete',
                                $topval->id
                              ],['class'=> 'fas fa-trash-alt','style'=>'font-size: 21px; color:#cf1212;'	
                              ,"onClick"=>"javascript: return confirm('Are you sure do you want to delete this Item')"]); ?>
                           </strong>
                        </td>
                     </tr>
                     <?php } ?>

                   <?php   }else {   ?>
                     <tr>
                        <td colspan = "4" style= "text-align:center;">
                           <h4> No Item Added </h4>
                        </td>
                     </tr>
                     <?php } ?>
                  </tbody>
               </table>
               </div>
            </div>
         </div>
         <!--/.col (right) -->
      </div>
      <!-- /.row -->
   </section>
   <!-- /.content -->
</div>
<?php echo $this->Form->end(); ?>
<!-- Relation Beetween Location and Sublocation  -->
<script>
   $(document).ready(function() {
       $('.category_request').on('click', function(e) {
           
         e.preventDefault();
           var category_id = $('.category_id').val();
           var category_qty = $('.category_qty').val();
   
           $(".error").hide();
   
         var hasError = false;
         if(category_id == '')
       {
           $(".category_id").after('<span class="error" style = "color:red;">Select Atleast one category</span>');
           hasError = true;
       }
   
       if(category_qty == '' || category_qty <= 0 )
       {
           $(".category_qty").after('<span class="error" style = "color:red;">Enter Qty </span>');
           hasError = true;
       }
           if(hasError == true)
       {
       return false;
       }
//var //branch_name = '<?php //echo $this->request->session()->read('Auth.User.db'); ?>';

          $.ajax({
               type: 'POST',
               url: '<?php echo SITE_URL;?>/admin/Studentpurchasereturn/categoryrequest',
               data: {
                   'category_id': category_id,'category_qty': category_qty
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
         var item = $('#retail_idsd').val();
        //  alert(item);
         $(".error").hide();
           var hasError = false;
           if(item_id == '')
           {
               $(".item_id").after('<span class="error" style = "color:red;">Select Atleast One Item</span>');
               hasError = true;
           }
   
           if(item_qty == '' || item_qty <= 0 )
           {
               $(".item_qty").after('<span class="error" style = "color:red;">Enter Qty </span>');
               hasError = true;
           }
           if(hasError == true)
           {
            return false;
           }
   
           $.ajax({
               type: 'POST',
               url: '<?php echo SITE_URL;?>/admin/Studentpurchasereturn/itemrequest',
               data: {'item_id': item_id,'item_qty': item_qty,'item': item},
               success: function(data) {
                console.log(data);
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
   function cllbckretail(name, id) {
       $('.secrh-retail').val(name);
       $('#testUL').hide();
       //alert(cid);
       $.ajax({
           type: 'POST',
           url: '<?php echo ADMIN_URL; ?>Studentpurchasereturn/getitemdetail',
           data: {
               'fetch': id
           },
           success: function(data) {
            //    console.log(data);
               var json = $.parseJSON(data);
            //   console.log(json.id);
               $('#retail_idsd').val(json.id);
               $('#sale-price').val(json.sale_price);
           },
       });
   
   }
</script>
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
           if(id){
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

<style>
            #test {
                position: relative;
            }

            #test ul {
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

            #test ul li {
                padding: 5px 8px;
                border: 1px solid lightgray;
            }

            #test ul li a {
                color: black;
            }

            .preview {
                margin-right: 15px;
            }

            .dataTables_wrapper.form-inline.dt-bootstrap.no-footer {
                margin-top: 0px;
            }
        </style>


        <script>
            function cllbckretail0(id, cid, sid) {
                $('.secrh-students').val(id);
                $('#retail_id').val(cid);
                $('#test').hide();
            }
            $(function() {
                $('.secrh-students').bind('keyup', function() {
                    var pos = $(this).val();
                    //alert(pos);
                    var check = 0;
                    //var catid=$('#subcategory').val();
                    //alert(pos);
                    $('#test').show();
                    $('#retail_id').val('');
                    var count = pos.length;
                    if (count > 0) {
                        $.ajax({
                            type: 'POST',
                            url: '<?php echo ADMIN_URL; ?>Solditems/getstudentname',
                            data: {
                                'fetch': pos,
                                'check': check,

                            },
                            success: function(data) {
                                /// alert(data);
                                console.log(data);
                                $('#test ul').html(data);
                            },
                        });
                    } else {
                        $('#test').hide();
                    }
                });
            });
        </script>