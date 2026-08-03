<style>
   #testUL {
      position: relative;
   }

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

   #testUL ul li {
      padding: 5px 8px;
      border: 1px solid lightgray;
   }

   #testUL ul li a {
      color: black;
   }

   .preview {
      margin-right: 15px;
   }
</style>

<style>
   #load2 {
      width: 100%;
      height: 100%;
      position: fixed;
      z-index: 9999;
      background-color: white !important;
      background: url("<?php echo SITE_URL; ?>images/Preloader_2.gif") no-repeat center center rgba(0, 0, 0, 0.75)
   }
</style>
<div class="content-wrapper">
   <section class="content-header">
      <h1>
         Add Item Master
      </h1>
      <ol class="breadcrumb">
         <li><a href="<?php echo SITE_URL; ?>admin/indent"><i class="fa fa-home"></i>Home</a></li>
         <li><a href="<?php echo SITE_URL; ?>admin/additem">Add New Item</a></li>
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


                  <script>
                     $(document).ready(function() {
                        $("#Mysubscriptions").bind("submit", function(event) {
                           event.preventDefault(); // Prevent default form submission
                           $('.lds-facebook').show(); // Ensure this loader class is valid

                           $.ajax({
                              async: true,
                              data: $("#Mysubscriptions").serialize(),
                              dataType: "html",
                              type: "get",
                              url: "<?php echo ADMIN_URL; ?>additem/searchitem",
                              beforeSend: function(xhr) {
                                 xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
                                 $('#load2').css("display", "block"); // Show loader
                              },
                              success: function(data) {
                                 $('.lds-facebook').hide();
                                 $("#example23").html(data);
                              },
                              complete: function() {
                                 $('#load2').css("display", "none"); // Hide loader
                              },
                              error: function() {
                                 alert("An error occurred. Please try again.");
                                 $('#load2').css("display", "none"); // Hide loader on error
                              }
                           });
                           return false;
                        });

                        $(document).on('click', '.pagination a', function(e) {
                           e.preventDefault(); // Prevent default link behavior
                           var target = $(this).attr('href');
                           var res = target.replace("/additem/searchitem", "/additem");
                           window.location.href = res; // Redirect to new URL
                        });
                     });
                  </script>
                  <?php echo $this->Form->create('Mysubscription', array('type' => 'file', 'inputDefaults' => array('div' => false, 'label' => false), 'id' => 'Mysubscriptions', 'class' => 'form-horizontal')); ?>

                  <div class="form-group">
                     <div class="row">
                        <!-- <div class="col">
                           <label for="inputEmail3" class="control-label">Search Item</label>
                           <?php echo $this->Form->input('item_name', array('class' => 'form-control', 'label' => false, 'placeholder' => 'Enter Item Name', 'autocomplete' => 'off')); ?>
                        </div> -->

                        <div class="col-sm-3">
                           <label for="inputEmail3" class="control-label">Product</label>
                           <input type="hidden" required="required" name="item_id" id="retail_ids">
                           <?php echo $this->Form->input('item_name', array('class' => 'form-control secrh-retail', 'id' => 'itemname', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'placeholder' => 'Enter Item Name')); ?>
                           <div id="testUL" style="display:none;">
                              <ul></ul>
                           </div>
                        </div>
                        <?php $itemtype = ['RawMaterial' => 'RawMaterial', 'FinishedProduct' => 'FinishedProduct'] ?>
                        <div class="col">
                           <label for="inputEmail3" class="control-label" style="text-align: left !important;">Select
                              Product Type
                           </label>
                           <?php echo $this->Form->input('itemtype', array('class' => 'form-control', 'type' => 'select', 'options' => $itemtype, 'label' => false, 'empty' => 'Select Product Type', 'autofocus', 'autocomplete' => 'off')); ?>
                        </div>

                        <div class="col">
                           <label for="inputEmail3" class="control-label" style="text-align: left !important;">Category
                           </label>
                           <?php echo $this->Form->input('category_id', array('class' => 'form-control', 'type' => 'select', 'options' => $categary, 'label' => false, 'empty' => 'Select Category', 'autofocus', 'autocomplete' => 'off')); ?>
                        </div>
                        <div class="col">
                           <label for="inputEmail3" class="control-label"
                              style="text-align: left !important;">Status</label>
                           <?php $status = ['Y' => 'Active', 'N' => 'Deactive']; ?>
                           <?php echo $this->Form->input('status', array('class' => 'form-control', 'type' => 'select', 'options' => $status, 'label' => false,  'autofocus', 'autocomplete' => 'off'));
                           ?>
                        </div>


                        <div class="col" style="    width: auto !important;
    flex: inherit;">
                           <input type="submit" style="background-color:#00c0ef; color:#fff; margin-top: 23px;"
                              id="Mysubscriptions" class="btn btn4 btn_pdf myscl-btn date" value="Search">

                           <a href="<?php echo SITE_URL; ?>admin/Additem/index" class="excelbtn btn"
                              style=" margin-top: 23px; color:#fff; padding:6px 18px;">Reset</a>


                        </div>

                        <div class="col">

                           <?php
                           $role_permissions = $this->Permission->permissioncheck();
                           $fileurl = "admin/additem/add";
                           if (in_array($fileurl, $role_permissions)) { ?>
                              <a href="<?php echo ADMIN_URL; ?>Additem/add" class="excelbtn btn pull-right btn-success"
                                 style=" margin-top: 23px; color:#fff;padding:6px 18px;border-radius: 4px;"><i
                                    class="fa fa-plus"></i>&nbsp;Add Item</a>
                           <?php } ?>

                           <!-- <a href="<?php // echo ADMIN_URL; 
                                          ?>Additem/view" class="excelbtn btn pull-right "
                              target="_blank" style="padding:0;margin-top: 23px;margin-right: 5px;"><i
                                 class="fa fa-file-pdf-o" style="font-size:28px;"></i></a> -->

                           <a href="<?php echo ADMIN_URL; ?>Additem/viewitemexcel"
                              style="padding:0; display:block;"><i class="fa fa-file-excel-o"
                                 style=" font-size: 28px;"></i></a>

                        </div>
                     </div>
                     <?php echo $this->Form->end(); ?>
                  </div>
               </div>






               <!-- </div>box-header -->
               <div id="load2" style="display:none;"></div>
               <div class="box-body" style="padding:0px; margin-top:10px;" id="example23">

                  <table class="table table-bordered table-striped" width="100%">
                     <thead>
                        <tr>
                           <th width="4%">S.No.</th>
                           <th width="6%">Unique Id</th>
                           <th width="45%">Item Name</th>
                           <th width="15%">Category</th>
                           <th width="8%">Item Type</th>
                           <th width="8%">UOM</th>
                           <!-- <th width="8%">Tax(%)</th> -->
                           <th width="10%">Current Stock</th>
                           <th width="10%">Action</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php
                        $page = $this->request->params['paging']['Additem']['page'];
                        $limit = $this->request->params['paging']['Additem']['perPage'];
                        $counter = ($page * $limit) - $limit + 1;
                        if (isset($users) && !empty($users)) {
                           foreach ($users as $intusr) {
                              $InhandStock = $this->Comman->InhandStock($intusr['id']);
                        ?>
                              <tr>
                                 <td>
                                    <?php echo $counter; ?>
                                 </td>
                                 <td>
                                    <?php echo $intusr['id']; ?>
                                 </td>
                                 <td>
                                    <?php
                                    if ($intusr['sizemanager']['size_name']) {
                                       echo ucfirst($intusr['item_name'] . '(' . $intusr['sizemanager']['size_name'] . ')');
                                    } else {
                                       echo ucfirst($intusr['item_name']);
                                    }
                                    ?>
                                 </td>
                                 <td>
                                    <?php echo Ucfirst($intusr['itemcategory']['category_name']); ?>
                                 </td>
                                 <td>
                                    <?php echo $intusr['itemtype']; ?>
                                 </td>
                                 <td>
                                    <?php echo $intusr['measurementunit']['unit_name']; ?>
                                 </td>
                                 <!-- <td>
                                    <?php
                                    if ($intusr['taxmaster']['tax']) {
                                       echo $intusr['taxmaster']['tax'] . '%';
                                    } else {
                                       echo "N/A";
                                    }
                                    ?>
                                 </td> -->
                                 <td>
                                    <?php echo $InhandStock ? $InhandStock : 0; ?>
                                 </td>
                                 <td> <strong>
                                       <?php
                                       $fileurl = "admin/additem/edit";
                                       if (in_array($fileurl, $role_permissions)) {
                                          echo $this->Html->link('', [
                                             'action' => 'edit',
                                             $intusr->id,
                                          ], ['class' => 'fas fa-edit', 'style' => 'font-size: 16px !important;']);
                                       } ?>
                                       &nbsp;
                                       <?php
                                       $fileurl = "admin/additem/delete";
                                       if (in_array($fileurl, $role_permissions)) {
                                          echo $this->Html->link('', [
                                             'action' => 'delete',
                                             $intusr->id
                                          ], [
                                             'class' => 'fas fa-trash-alt',
                                             'style' => 'font-size: 16px !important; color:#cd0404; margin-right:4px !important;',
                                             "onClick" => "javascript: return confirm('Are you sure do you want to delete this Item')"
                                          ]);
                                       } ?>
                                       <?php if ($intusr['status'] == 'Y') {
                                          echo $this->Html->link('', [
                                             'action' => 'status',
                                             $intusr->id,
                                             'Y'
                                          ], ['title' => 'Active', 'class' => 'fas fa-check-circle', 'style' => 'font-size: 16px !important; margin-left: 12px;     color: #36cb3c;']);
                                       } else {
                                          echo $this->Html->link('', [
                                             'action' => 'status',
                                             $intusr->id,
                                             'N'
                                          ], ['title' => 'Inactive', 'class' => 'fas fa-times-circle', 'style' => 'font-size: 16px !important; margin-left: 12px; color:#cd0404;']);
                                       }  ?>
                                    </strong>
                                 </td>
                              </tr>
                           <?php $counter++;
                           }
                        } else { ?>
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
   <!-- /.content -->
</div>
<!-- /.   content-wrapper -->
<script>
   $(document).ready(function() {
      $(".add-batch-fields").click(function() {
         $.ajax({
            type: "POST",
            url: '<?php echo SITE_URL; ?>admin/additem/add',
            cache: false,
            success: function(html) {
               //alert(html);   
               $(".product_containes").append(html);
            }
         });
      });

      $("body").on("click", ".remove", function() {
         //alert('hello');
         $(this).closest('.formdetails').remove();
      });
   });


   function cllbckretail(id, cid, sid) {
      $('.secrh-retail').val(id);
      $('#retail_ids').val(cid);
      $('#size').val(sid);
      $('#testUL').hide();
      //alert(cid);
      $.ajax({
         type: 'POST',
         url: '<?php echo ADMIN_URL; ?>indent/getitemdetail',
         data: {
            'fetch': cid
         },
         success: function(data) {
            console.log(data);
            //alert(data);
            $('#unitna').val(data);
         },
      });
   }
   $(function() {
      $('.secrh-retail').bind('keyup', function() {
         var pos = $(this).val();
         var check = 0;
         $('#testUL').show();
         $('#retail_ids').val('');
         var count = pos.length;
         if (count > 0) {
            $.ajax({
               type: 'POST',
               url: '<?php echo ADMIN_URL; ?>stockregister/getitemname',
               data: {
                  'fetch': pos,
                  'check': check
               },
               success: function(data) {
                  $('#testUL ul').html(data);

               },
            });
         } else {
            $('#testUL').hide();
         }
      });
   });
</script>