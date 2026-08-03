
<style>
   .paperclip .checkk:checked,
   .paperclip .checkk:not(:checked) {
      position: absolute;
      left: -9999px;
   }

   .paperclip .checkk:checked+label,
   .paperclip .checkk:not(:checked)+label {
      position: relative;
      padding-left: 28px;
      cursor: pointer;
      line-height: 20px;
      display: inline-block;
      color: #666;
   }

   .paperclip .checkk:checked+label:before,
   .paperclip .checkk:not(:checked)+label:before {
      content: '\f0c6';
      font-family: "Font Awesome 5 Free";
      color: #000;
      font-weight: 900;
      position: absolute;
      left: 0;
      top: 0;
      width: 18px;
      height: 18px;
   }

   .paperclip .checkk:checked+label:after,
   .paperclip .checkk:not(:checked)+label:after {
      content: '\f0c6';
      font-family: "Font Awesome 5 Free";
      color: #F00;
      font-weight: 900;
      position: absolute;
      left: 0;
      top: 0;
      width: 18px;
      height: 18px;
   }

   .paperclip .checkk:not(:checked)+label:after {
      opacity: 0;
      -webkit-transform: scale(0);
      transform: scale(0);
   }

   .paperclip .checkk:checked+label:after {
      opacity: 1;
      -webkit-transform: scale(1);
      transform: scale(1);
   }
</style>






<div class="content-wrapper">
   <!-- Content Header (Page header) -->
   <section class="content-header">
      <h1><i class="fa fa-th-list"></i> Manage Permission Module </h1>
      <ol class="breadcrumb">
         <li><a href="<?php echo ADMIN_URL; ?>Permissionmodules"><i class="fa fa-home"></i>Home</a></li>
      </ol>
   </section>

   <!-- Main content -->
   <section class="content">
      <div class="row">
         <div class="col-xs-12">

            <div class="box">

               <!-- --------------user Name-------- -->
               <div class="box-header">
                  <h3 class="box-title">
                     <?php if ($id) { ?>
                        Set Permissions for User- <b style="color:green;"><?php echo $username; ?></b>
                     <?php } else { ?>
                        View Permission Modules List
                     <?php } ?>
                  </h3>
                  <?php echo $this->Flash->render(); ?>
               </div>

               <!-- /.box-header -->
               <?php echo $this->Form->create($classes, array('class' => 'form-horizontal', 'id' => 'sevice_sform', 'enctype' => 'multipart/form-data', 'validate')); ?>



               <div class="box-body">

                     <div style="float:center; width:100%">
                        <div class="title-field-group">

                           <?php 
                           if ($id) { ?>
                              <input type="hidden" name="user_id" value="<?php echo $id; ?>">
                              <input type="hidden" name="naction" value="<?php echo $id; ?>">
                              <script>
                                 $(document).ready(function () {
                                    var emp = '<?php echo $id; ?>';
                                    $.ajax({
                                       type: 'POST',
                                       url: '<?php echo ADMIN_URL; ?>permissionmodules/calculatepermission',
                                       data: {
                                          'empid': emp
                                       },
                                       success: function (data) {
                                          $("#amountrt").html(data);
                                       }

                                    });
                                 });
                              </script>
                           <?php } else { ?>

                              <div class="form-group">
                                 <div class="col-sm-3">
                                    <label>User Email<span style="color:red;">*</span></label>
                                    <?php
                                    echo $this->Form->input('user_id', array('class' => 'form-control', 'type' => 'select', 'id' => 'emp-type', 'empty' => '--Select--', 'options' => $employees, 'label' => false, 'required')); ?>
                                 </div>
                              </div>
                              <!-- .field-group -->
                              <script>
                                 $(document).ready(function () {
                                    $('#emp-type').on('change', function () {
                                       var emp = $('#emp-type').val();
                                       $.ajax({
                                          type: 'POST',
                                          url: '<?php echo ADMIN_URL; ?>permissionmodules/calculatepermission',
                                          data: {
                                             'empid': emp
                                          },
                                          success: function (data) {
                                             $("#amountrt").html(data);
                                          }
                                       });
                                    });
                                 });
                              </script>
                           <?php } ?>



                           
                           <script>
                              $(document).ready(function () {
                                 $(".parenth").each(function (index) {
                                    var group = $(this).data("group");
                                    var parent = $(this);
                                    parent.change(function () {
                                       $(group).prop('checked', parent.prop(
                                          "checked"));
                                    });
                                    $(group).change(function () {
                                       parent.prop('checked', true);
                                       if ($(group + ':checked').length == 0) {
                                          parent.prop('checked', false);
                                       }
                                    });
                                 });
                              });
                           </script>


                           <div class="form-group">
                              <div class="col-sm-6">
                                 <div class="submit">
                                    <input type="submit" class="btn btn-info pull-left" value="Update Rights"
                                       title="Update">
                                 </div>
                              </div>
                              <div class="col-sm-6"></div>
                           <!-- </div> -->


                           <div id="amountrt">
                              <?php if ($id) {
                                 $empid = $id;
                              } ?>





                              <!-- <div class="form-group"> -->
                                 <div class="col-sm-6">
                                    <div class="title-div">


                                       <table width="80%">
                                          <tbody>
                                             <tr>
                                                <td width="30%"><strong></strong></td>
                                             </tr>

                                             <tr style="background:#4993d7;">

                                                <td width="30%" class="paperclip"><input type="checkbox" <?php if (in_array("Store Management", $module)) { ?> checked="checked"
                                                      <?php } ?> name="module1[]" class="parenth" data-group=".group1"
                                                      value="Store Management" id="2_arrgruop">
                                                   <input type="hidden" name="module[]" value="Store Management"><strong
                                                      style="color:#fff;"> Store Management </strong>
                                                </td>

                                                <td width="10%"></td>
                                             </tr> 

                                              <tr>
                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu1[0]"
                                                      class="group1" value="Vendor Manager^vendors^index" <?php if (in_array("Vendor Manager", $menu)) { ?> checked="checked" <?php } ?> id="2_arrgruop">
                                                   Vendor Manager <input type="checkbox" id="test090" class="checkk"
                                                      <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Vendor Manager", "vendors", "index");
                                                      if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?>
                                                      value="1" name="menu1a[0]"> <label for="test090"><small
                                                         style="margin-left: -13px;font-size:62%;">Featured
                                                         on Top Menu</small></label>
                                                </td>

                                                 <td width="10%"></td>
                                             </tr> 

                                             <tr>
                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu1[1]"
                                                      class="group1" value="Tax Manager^taxmaster^index" <?php if (in_array("Tax Manager", $menu)) { ?> checked="checked" <?php } ?>
                                                      id="2_arrgruop"> Tax
                                                   Manager <input type="checkbox" id="test90" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Tax Manager", "taxmaster", "index");
                                                   if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?>
                                                      value="1" name="menu1a[1]"> <label for="test90"><small
                                                         style="margin-left: -13px;font-size:62%;">Featured
                                                         on Top Menu</small></label>
                                                </td>
                                             </tr> 
                                              <tr>
                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu1[2]"
                                                      class="group1" value="Item Category Manager^itemcategory^index"
                                                      <?php if (in_array("Item Category Manager", $menu)) { ?>
                                                         checked="checked" <?php } ?> id="2_arrgruop">
                                                   Item Category Manager <input type="checkbox" id="test91"
                                                      class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Item Category Manager", "itemcategory", "index");
                                                      if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?>
                                                      value="1" name="menu1a[2]"> <label for="test91"><small
                                                         style="margin-left: -13px;font-size:62%;">Featured
                                                         on Top Menu</small></label>
                                                </td>
                                             </tr> 
                                              <tr>
                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu1[3]"
                                                      class="group1"
                                                      value="Measurement Units Manager^measurementunit^index" <?php if (in_array("Measurement Units Manager", $menu)) { ?>
                                                         checked="checked" <?php } ?> id="2_arrgruop">
                                                   Measurement Units Manager <input type="checkbox" id="test92"
                                                      class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Measurement Units Manager", "measurementunit", "index");
                                                      if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?>
                                                      value="1" name="menu1a[3]"> <label for="test92"><small
                                                         style="margin-left: -13px;font-size:62%;">Featured
                                                         on Top Menu</small></label>
                                                </td>
                                             </tr> 
                                              <tr>
                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu1[4]"
                                                      class="group1" value="Size Manager^sizemanager^index" <?php if (in_array("Size Manager", $menu)) { ?> checked="checked" <?php } ?> id="2_arrgruop">
                                                   Size Manager <input type="checkbox" id="test93" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Size Manager", "sizemanager", "index");
                                                   if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?>
                                                      class="checkk" value="1" name="menu1a[4]"> <label
                                                      for="test93"><small
                                                         style="margin-left: -13px;font-size:62%;">Featured
                                                         on Top Menu</small></label>
                                                </td>
                                             </tr>
                                              <tr>
                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu1[5]"
                                                      class="group1" value="Payment Terms Manager^paymentmanager^index"
                                                      <?php if (in_array("Payment Terms Manager", $menu)) { ?>
                                                         checked="checked" <?php } ?> id="2_arrgruop">
                                                   Payment Terms Manager <input type="checkbox" id="test94"
                                                      class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Payment Terms Manager", "paymentmanager", "index");
                                                      if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?>
                                                      value="1" name="menu1a[5]"> <label for="test94"><small
                                                         style="margin-left: -13px;font-size:62%;">Featured
                                                         on Top Menu</small></label>
                                                </td>
                                             </tr>
                                             <tr>
                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu1[6]"
                                                      class="group1" value="Add Item Manager^additem^index" <?php if (in_array("Add Item Manager", $menu)) { ?> checked="checked" <?php } ?> id="2_arrgruop"> Add
                                                   Item Manager <input type="checkbox" id="test96" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Add Item Manager", "additem", "index");
                                                   if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu1a[6]">
                                                   <label for="test96"><small
                                                         style="margin-left: -13px;font-size:62%;">Featured
                                                         on Top Menu</small></label>
                                                </td>
                                             </tr> 
                                            <tr>
                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu1[7]"
                                                      class="group1" value="Add Indent^indent^add" <?php if (in_array("Add Indent", $menu)) { ?> checked="checked" <?php } ?>
                                                      id="2_arrgruop"> Add
                                                   Indent <input type="checkbox" id="test97" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Add Indent", "indent", "add");
                                                   if ($getfeatured['featured'] == '1') { ?>
                                                         checked="checked" <?php } ?> value="1" name="menu1a[7]"> <label
                                                      for="test97"><small
                                                         style="margin-left: -13px;font-size:62%;">Featured
                                                         on Top Menu</small></label>
                                                </td>
                                             </tr> 
                                             <tr>
                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu1[8]"
                                                      class="group1" value="Goods Issue^indent^index" <?php if (in_array("Goods Issue", $menu)) { ?> checked="checked" <?php } ?>
                                                      id="2_arrgruop">
                                                   Goods Issue <input type="checkbox" id="test98" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Goods Issue", "indent", "index");
                                                   if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="menu1a[8]"> <label
                                                      for="test98"><small
                                                         style="margin-left: -13px;font-size:62%;">Featured
                                                         on Top Menu</small></label>
                                                </td>
                                             </tr> 
                                              <tr>
                                                <td width="30%" class="paperclip"><input type="checkbox" name="menu1[9]"
                                                      class="group1" value="PO^purchaseorder^index" <?php if (in_array("PO", $menu)) { ?> checked="checked" <?php } ?>
                                                      id="2_arrgruop"> PO
                                                   <input type="checkbox" id="test99" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "PO", "purchaseorder", "index");
                                                   if ($getfeatured['featured'] == '1') { ?>
                                                         checked="checked" <?php } ?> value="1" name="menu1a[9]"> <label
                                                      for="test99"><small
                                                         style="margin-left: -13px;font-size:62%;">Featured
                                                         on Top Menu</small></label>
                                                </td>
                                             </tr>
                                             <tr>
                                                <td width="30%" class="paperclip"><input type="checkbox"
                                                      name="menu1[10]" class="group1"
                                                      value="Received^goodsreceived^index" <?php if (in_array("Received", $menu)) { ?> checked="checked" <?php } ?>
                                                      id="2_arrgruop">Received <input type="checkbox" id="test100" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Received", "goodsreceived", "index");
                                                      if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?>
                                                      class="checkk" value="1" name="menu1a[10]"> <label
                                                      for="test100"><small
                                                         style="margin-left: -13px;font-size:62%;">Featured
                                                         on Top Menu</small></label>
                                                </td> 
                                             </tr>
                                              <tr>
                                                <td width="30%" class="paperclip"><input type="checkbox"
                                                      name="menu1[11]" class="group1"
                                                      value="Stock Register^stockregister^index" <?php if (in_array("Stock Register", $menu)) { ?> checked="checked" <?php } ?> id="2_arrgruop">
                                                   Stock Register <input type="checkbox" id="test101" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Stock Register", "stockregister", "index");
                                                   if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?>
                                                      class="checkk" value="1" name="menu1a[11]"> <label
                                                      for="test101"><small
                                                         style="margin-left: -13px;font-size:62%;">Featured
                                                         on Top Menu</small></label>
                                                </td>
                                             </tr> 
                                             <tr>
                                                <td width="30%" class="paperclip"><input type="checkbox"
                                                      name="menu1[12]" class="group1"
                                                      value="Company Master^companymaster^index" <?php if (in_array("Company Master", $menu)) { ?> checked="checked" <?php } ?> id="2_arrgruop">
                                                   Company Master <input type="checkbox" id="test101" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Company Master", "companymaster", "index");
                                                   if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?>
                                                      class="checkk" value="1" name="menu1a[12]"> <label
                                                      for="test101"><small
                                                         style="margin-left: -13px;font-size:62%;">Featured
                                                         on Top Menu</small></label>
                                                </td>
                                             </tr> 

                                          </tbody>
                                       </table>

                                    </div>
                                 </div>

                                 
                            <!-- /.box -->
                              </div>
                           </div>
                        </div>
                  </div>
                  <!-- /.box -->
                  </form>
               </div>





               <!-- /.box -->
            </div>
            <!-- /.col -->
         </div>
         <!-- /.row -->
         </div>

   </section>
</div>