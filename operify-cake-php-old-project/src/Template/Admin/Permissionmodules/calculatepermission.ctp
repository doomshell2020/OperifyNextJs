<script>
   $(".parenth").each(function (index) {
      var group = $(this).data("group");
      var parent = $(this);

      parent.change(function () { //"select all" change
         $(group).prop('checked', parent.prop("checked"));
      });
      $(group).change(function () {
         // parent.prop('checked', false);
         parent.prop('checked', true);
         if ($(group + ':checked').length == 0) {
            parent.prop('checked', false);
         }
      });
   });
</script>
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
<?php //pr($module);die; ?>

<div class="form-group">
   <div class="row">

<!-- Store Management -->
      <div class="col-sm-6">
         <div class="title-div">
            <table width="80%">
               <tbody>
                  <tr>
                     <td width="30%"><strong>&nbsp;</strong></td>
                  </tr>
                  <tr style="background:#4993d7;">
                     <td width="30%" class="paperclip"><input type="checkbox" <?php if (in_array("Store Management", $module)) { ?> checked="checked" <?php } ?> name="module1[]" class="parenth"
                           data-group=".group1" value="Store Management" id="2_arrgruop">
                        <input type="hidden" name="module[]" value="Store Management"><strong style="color:#fff;">
                           Store Management </strong>
                     </td>
                     <td width="10%"></td>
                  </tr>


                  <!-- PO  -->
                  <tr>
                     <td width="30%" class="paperclip">
                        <input type="checkbox" name="menu1[3]" class="group1"value="PO^purchaseorder^index" <?php if (in_array("PO", $menu)) { ?> checked="checked" <?php } ?> id="2_arrgruop"> PO <input type="checkbox" id="test53" class="checkk" 
                        <?php $getfeatured = $this->Comman->findstatuspermission($empid, "PO", "purchaseorder", "index");
                              if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1"  name="featured1[3]"> 
                              <label for="test53"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                        <input type="checkbox" name="permission1[3]" <?php if ($getfeatured['edit'] == '1') { ?>
                              checked="checked" <?php } ?> value="1" id="test53"> edit &nbsp;
                        <input type="checkbox" name="permission_delete1[3]" <?php if ($getfeatured['delete'] == '1') { ?>
                              checked="checked" <?php } ?> value="1" id="test53"> delete
                              <input type="hidden" name="sort1[3]" value="18">
                              <input type="hidden" name="featuredno1[3]" value="18">
                     </td>
                  </tr>

                  <!-- GRN -->
                  <tr>
                     <td width="30%" class="paperclip"><input type="checkbox" name="menu1[4]" class="group1"
                           value="GRN^goodsreceived^index" <?php if (in_array("GRN", $menu)) { ?> checked="checked"
                           <?php } ?> id="2_arrgruop"> GRN <input type="checkbox" id="test54" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "GRN", "goodsreceived", "index");
                              if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> class="checkk" value="1"
                           name="featured1[4]"> <label for="test54"><small
                              style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                        <input type="checkbox" name="permission1[4]" <?php if ($getfeatured['edit'] == '1') { ?>
                              checked="checked" <?php } ?> value="1" id="test54"> edit &nbsp;<input type="checkbox"
                           name="permission_delete1[4]" <?php if ($getfeatured['delete'] == '1') { ?> checked="checked"
                           <?php } ?> value="1" id="test54"> delete
                           <input type="hidden" name="sort1[4]" value="20">
                           <input type="hidden" name="featuredno1[4]" value="20">
                     </td>
                  </tr>

                               
                <!-- Indent -->
                <tr>
                     <td width="30%" class="paperclip"><input type="checkbox" name="menu1[1]" class="group1"
                           value="Indents^indentpo^index" <?php if (in_array("Indents", $menu)) { ?> checked="checked"
                           <?php } ?> id="2_arrgruop"> Indents <input type="checkbox" id="test51" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Indents", "indentpo", "index");
                              if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="featured1[1]">
                        <label for="test51"><small style="margin-left: -13px;font-size:62%;">Featured on Top
                              Menu</small></label>
                        <input type="checkbox" name="permission1[1]" <?php if ($getfeatured['edit'] == '1') { ?>
                              checked="checked" <?php } ?> value="1" id="test51"> edit &nbsp;
                        <input type="checkbox" name="permission_delete1[1]" <?php if ($getfeatured['delete'] == '1') { ?>
                              checked="checked" <?php } ?> value="1" id="test51"> delete
                              <input type="hidden" name="sort1[1]" value="24">
                              <input type="hidden" name="featuredno1[1]" value="24">
                     </td>
                  </tr>

                  <!--reverse  -->
                  <tr>
                     <td width="30%" class="paperclip">
                        <input type="checkbox" name="menu1[2]" class="group1" value="Reverse^reverseindent^index" <?php if (in_array("Reverse", $menu)) { ?> checked="checked" <?php } ?> id="2_arrgruop"> Reverse

                        <input type="checkbox" id="test52" class="checkk" 
                        <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Reverse", "reverseindent", "index");
                        if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="featured1[2]">

                        <label for="test52"><small style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                        <input type="checkbox" name="permission1[2]" <?php if ($getfeatured['edit'] == '1') { ?>
                              checked="checked" <?php } ?> value="1" id="test52"> edit &nbsp;

                        <input type="checkbox" name="permission_delete1[2]" <?php if ($getfeatured['delete'] == '1') { ?>
                              checked="checked" <?php } ?> value="1" id="test52"> delete
                              <input type="hidden" name="sort1[2]" value="26">
                              <input type="hidden" name="featuredno1[2]" value="26">
                     </td>
                  </tr>

                   <!--  Return -->
                    <tr>
                     <td width="30%" class="paperclip"><input type="checkbox" name="menu1[5]" class="group1"
                           value="Return^purchasereturn^index" <?php if (in_array("Return", $menu)) { ?>
                              checked="checked" <?php } ?> id="2_arrgruop"> Return <input type="checkbox" id="test55"
                           class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Return", "purchasereturn", "index");
                           if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?>
                           value="1" name="featured1[5]"> <label for="test55"><small
                              style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                        <input type="checkbox" name="permission1[5]" <?php if ($getfeatured['edit'] == '1') { ?>
                              checked="checked" <?php } ?> value="1" id="test55"> edit &nbsp;<input type="checkbox"
                           name="permission_delete1[5]" <?php if ($getfeatured['delete'] == '1') { ?> checked="checked"
                           <?php } ?> value="1" id="test55"> delete
                           <input type="hidden" name="sort1[5]" value="38">
                           <input type="hidden" name="featuredno1[5]" value="38">
                     </td>
                  </tr>

               </tbody>
            </table>
         </div>
      </div>
   
      <!-- Inventory Management  -->
      <div class="col-sm-6">
         <div class="title-div">
            <table width="80%">
               <tbody>
                  
                  <tr>
                     <td width="30%"><strong>&nbsp;</strong></td>
                  </tr>
                  
                  <tr style="background:#4993d7;">
                     <td width="30%" class="paperclip"><input type="checkbox" <?php if (in_array("Inventory", $module)) { ?> checked="checked" <?php } ?> name="module2[]" class="parenth"
                     data-group=".group2" value="Inventory" id="2_arrgruop">
                     <input type="hidden" name="module[]" value="Inventory"><strong style="color:#fff;">
                        Inventory </strong>
                     </td>
                     <td width="10%"></td>
                  </tr>

                  <!-- Products -->
                  <tr>
                     <td width="30%" class="paperclip"><input type="checkbox" name="menu2[1]" class="group2"
                           value="Products^additem^index" <?php if (in_array("Products", $menu)) { ?> checked="checked"
                           <?php } ?> id="2_arrgruop"> Products <input type="checkbox" id="test61" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Products", "additem", "index");
                              if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="featured2[1]">
                        <label for="test61"><small style="margin-left: -13px;font-size:62%;">Featured on Top
                              Menu</small></label>
                        <input type="checkbox" name="permission2[1]" <?php if ($getfeatured['edit'] == '1') { ?>
                              checked="checked" <?php } ?> value="1" id="test61"> edit &nbsp;
                        <input type="checkbox" name="permission_delete2[1]" <?php if ($getfeatured['delete'] == '1') { ?>
                              checked="checked" <?php } ?> value="1" id="test61"> delete
                              <input type="hidden" name="sort2[1]" value="40">
                              <input type="hidden" name="featuredno2[1]" value="40">
                     </td>
                  </tr>

                  <!-- categories -->
                  <tr>
                     <td width="30%" class="paperclip"><input type="checkbox" name="menu2[2]" class="group2"
                           value="Categories^itemcategory^index" <?php if (in_array("Categories", $menu)) { ?>
                              checked="checked" <?php } ?> id="2_arrgruop"> Categories <input type="checkbox" id="test62"
                           class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Categories", "itemcategory", "index");
                           if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="featured2[2]"> <label for="test62"><small
                              style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                        <input type="checkbox" name="permission2[2]" <?php if ($getfeatured['edit'] == '1') { ?>
                              checked="checked" <?php } ?> value="1" id="test62"> edit &nbsp;
                        <input type="checkbox" name="permission_delete2[2]" value="1" <?php if ($getfeatured['delete'] == '1') { ?> checked="checked" <?php } ?> id="test62"> delete
                        <input type="hidden" name="sort2[2]" value="42">
                        <input type="hidden" name="featuredno2[2]" value="42">
                     </td>
                  </tr>

                  <!-- Tax -->
                      <tr>
                         <td width="30%" class="paperclip"><input type="checkbox" name="menu2[3]" class="group2"
                               value="Taxs ^taxmaster^index" <?php if (in_array("Taxs ", $menu)) { ?> checked="checked"
                               <?php } ?> id="2_arrgruop"> Taxs <input type="checkbox" id="test63" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Taxs ", "taxmaster", "index");
                                  if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="featured2[3]">
                            <label for="test63"><small style="margin-left: -13px;font-size:62%;">Featured on Top
                                  Menu</small></label>
                            <input type="checkbox" name="permission2[3]" <?php if ($getfeatured['edit'] == '1') { ?>
                                  checked="checked" <?php } ?> value="1" id="test63"> edit &nbsp;
                            <input type="checkbox" name="permission_delete2[3]" value="1" <?php if ($getfeatured['delete'] == '1') { ?> checked="checked" <?php } ?> id="test63"> delete
                            <input type="hidden" name="sort2[3]" value="44">
                            <input type="hidden" name="featuredno2[3]" value="44">
                         </td>
                      </tr>

                           <!-- UOM -->
                           <tr>
                         <td width="30%" class="paperclip"><input type="checkbox" name="menu2[4]" class="group2"
                               value="UOM ^measurementunit^index" <?php if (in_array("UOM ", $menu)) { ?> checked="checked"
                               <?php } ?> id="2_arrgruop"> UOM <input type="checkbox" id="test64" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "UOM ", "measurementunit", "index");
                                  if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> value="1" name="featured2[4]">
                            <label for="test64"><small style="margin-left: -13px;font-size:62%;">Featured on Top
                                  Menu</small></label>
                            <input type="checkbox" name="permission2[4]" <?php if ($getfeatured['edit'] == '1') { ?>
                                  checked="checked" <?php } ?> value="1" id="test64"> edit &nbsp;
                            <input type="checkbox" name="permission_delete2[4]" value="1" <?php if ($getfeatured['delete'] == '1') { ?> checked="checked" <?php } ?> id="test64"> delete
                            <input type="hidden" name="sort2[4]" value="46">
                            <input type="hidden" name="featuredno2[4]" value="46">
                         </td>
                      </tr>

                  <!-- suppliers -->
                  <tr>
                     <td width="30%" class="paperclip"><input type="checkbox" name="menu2[5]" class="group2"
                           value="Suppliers^vendors^index" <?php if (in_array("Suppliers", $menu)) { ?>
                              checked="checked" <?php } ?> id="2_arrgruop"> Suppliers <input type="checkbox" id="test65"
                           class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Suppliers", "vendors", "index");
                           if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?>
                           value="1" name="featured2[5]"> <label for="test65"><small
                              style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                        <input type="checkbox" name="permission2[5]" value="1" <?php if ($getfeatured['edit'] == '1') { ?>
                              checked="checked" <?php } ?> id="test65"> edit &nbsp;
                        <input type="checkbox" name="permission_delete2[5]" value="1" <?php if ($getfeatured['delete'] == '1') { ?> checked="checked" <?php } ?> id="test65"> delete
                        <input type="hidden" name="sort2[5]" value="48">
                        <input type="hidden" name="featuredno2[5]" value="48">
                     </td>
                     </td>
                  </tr>

          <!-- Company -->
                  <tr>
                     <td width="30%" class="paperclip"><input type="checkbox" name="menu2[6]" class="group2"
                           value="Company^companymaster^index" <?php if (in_array("Company", $menu)) { ?>
                              checked="checked" <?php } ?> id="2_arrgruop"> Company <input type="checkbox" id="test66"
                           <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Company", "companymaster", "index");
                           if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?> class="checkk" value="1" name="featured2[6]"> <label for="test66"><small
                              style="margin-left: -13px;font-size:62%;">Featured on Top
                              Menu</small></label>
                        <input type="checkbox" name="permission2[6]" <?php if ($getfeatured['edit'] == '1') { ?>
                              checked="checked" <?php } ?> value="1" id="test66"> edit &nbsp;<input type="checkbox"
                           name="permission_delete2[6]" <?php if ($getfeatured['delete'] == '1') { ?> checked="checked"
                           <?php } ?> value="1" id="test66"> delete
                           <input type="hidden" name="sort2[6]" value="50">
                           <input type="hidden" name="featuredno2[6]" value="50">
                     </td>
                  </tr>

               </tbody>
            </table>
         </div>
      </div>

   <!-- Production Manager -->
      <div class="col-sm-6">
         <div class="title-div">
            <table width="80%">
               <tbody>
                  <tr>
                     <td width="30%"><strong>&nbsp;</strong></td>
                  </tr>
                  <tr style="background:#4993d7;">
                     <td width="30%" class="paperclip"><input type="checkbox" <?php if (in_array("Production", $module)) { ?> checked="checked" <?php } ?> name="module3[]" class="parenth" data-group=".group3"
                           value="Production" id="2_arrgruop">
                        <input type="hidden" name="module[]" value="Production"><strong style="color:#fff;">
                           Production </strong>
                     </td>
                     <td width="10%"></td>
                  </tr>
                  
                  <!-- BOM -->
                  <tr>
                     <td width="30%" class="paperclip"><input type="checkbox" name="menu3[1]" class="group3"
                           value="BOM^production^billsofmaterials" <?php if (in_array("BOM", $menu)) { ?>
                              checked="checked" <?php } ?> id="2_arrgruop"> BOM <input type="checkbox" id="test71"
                           class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "BOM", "production", "billsofmaterials");
                           if ($getfeatured['featured'] == '1') { ?> checked="checked"
                           <?php } ?> value="1" name="featured3[1]"> <label for="test71"><small
                              style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                        <input type="checkbox" name="permission3[1]" <?php if ($getfeatured['edit'] == '1') { ?>
                              checked="checked" <?php } ?> value="1" id="test71"> edit &nbsp;<input type="checkbox"
                           name="permission_delete3[1]" <?php if ($getfeatured['delete'] == '1') { ?> checked="checked"
                           <?php } ?> value="1" id="test71"> delete
                           <input type="hidden" name="sort3[1]" value="12">
                           <input type="hidden" name="featuredno3[1]" value="12">
                     </td>
                  </tr>
 
                  <!-- Contract -->
                  <tr>
                     <td width="30%" class="paperclip"><input type="checkbox" name="menu3[2]" class="group3"
                           value="Contract^contracts^index" <?php if (in_array("Contract", $menu)) { ?>
                              checked="checked" <?php } ?> id="2_arrgruop"> Contract <input type="checkbox" id="test72"
                           class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Contract", "contracts", "index");
                           if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?>
                           value="1" name="featured3[2]"> <label for="test72"><small
                              style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                        <input type="checkbox" name="permission3[2]" <?php if ($getfeatured['edit'] == '1') { ?>
                              checked="checked" <?php } ?> value="1" id="test72"> edit &nbsp;<input type="checkbox"
                           name="permission_delete3[2]" <?php if ($getfeatured['delete'] == '1') { ?> checked="checked"
                           <?php } ?> value="1" id="test72"> delete
                           <input type="hidden" name="sort3[2]" value="14">
                           <input type="hidden" name="featuredno3[2]" value="14">
                     </td>
                  </tr>

                 <!-- Desgin Sheet-->
                  <tr>
                     <td width="30%" class="paperclip"><input type="checkbox" name="menu3[3]" class="group3"
                           value="Desgin Sheet^designsheet^index" <?php if (in_array("Desgin Sheet", $menu)) { ?>
                              checked="checked" <?php } ?> id="2_arrgruop"> Desgin Sheet <input type="checkbox"
                           id="test73" class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Desgin Sheet", "designsheet", "index");
                           if ($getfeatured['featured'] == '1') { ?>
                              checked="checked" <?php } ?> value="1" name="featured3[3]"> <label for="test73"><small
                              style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                        <input type="checkbox" name="permission3[3]" <?php if ($getfeatured['edit'] == '1') { ?>
                              checked="checked" <?php } ?> value="1" id="test73"> edit &nbsp;<input type="checkbox"
                           name="permission_delete3[3]" <?php if ($getfeatured['delete'] == '1') { ?> checked="checked"
                           <?php } ?> value="1" id="test73"> delete
                           <input type="hidden" name="sort3[3]" value="16">
                           <input type="hidden" name="featuredno3[3]" value="16">
                     </td>
                  </tr>

                  <!-- Production Order -->
                  <tr>
                     <td width="30%" class="paperclip"><input type="checkbox" name="menu3[4]" class="group3"
                           value="Production^production^productionorders" <?php if (in_array("Production", $menu)) { ?>
                              checked="checked" <?php } ?> id="2_arrgruop"> Production <input type="checkbox" id="test74"
                           class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Production", "production", "productionorders");
                           if ($getfeatured['featured'] == '1') { ?> checked="checked"
                           <?php } ?> value="1" name="featured3[4]"> <label for="test74"><small
                              style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label><input
                           type="checkbox" name="permission3[4]" <?php if ($getfeatured['edit'] == '1') { ?>
                              checked="checked" <?php } ?> value="1" id="test74"> edit &nbsp;<input type="checkbox"
                           name="permission_delete3[4]" <?php if ($getfeatured['delete'] == '1') { ?> checked="checked"
                           <?php } ?> value="1" id="test74"> delete
                           <input type="hidden" name="sort3[4]" value="22">
                           <input type="hidden" name="featuredno3[4]" value="22">
                     </td>
                  </tr>

                  <!-- DailySheet -->
                  <tr>
                     <td width="30%" class="paperclip"><input type="checkbox" name="menu3[5]" class="group3"
                           value="Daily Sheet^production^index" <?php if (in_array("Daily Sheet", $menu)) { ?>
                              checked="checked" <?php } ?> id="2_arrgruop"> Daily Sheet <input type="checkbox" id="test75"
                           class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Daily Sheet", "production", "index");
                           if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?>
                           value="1" name="featured3[5]"> <label for="test75"><small
                              style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                        <input type="checkbox" name="permission3[5]" <?php if ($getfeatured['edit'] == '1') { ?>
                              checked="checked" <?php } ?> value="1" id="test75"> edit &nbsp;<input type="checkbox"
                           name="permission_delete3[5]" <?php if ($getfeatured['delete'] == '1') { ?> checked="checked"
                           <?php } ?> value="1" id="test75"> delete
                           <input type="hidden" name="sort3[5]" value="28">
                           <input type="hidden" name="featuredno3[5]" value="28">
                     </td>
                  </tr>

                      <!-- Machines -->
                      <tr>
                     <td width="30%" class="paperclip"><input type="checkbox" name="menu3[6]" class="group3"
                           value="Machines^machine^index" <?php if (in_array("Machines", $menu)) { ?>
                              checked="checked" <?php } ?> id="2_arrgruop"> Machines <input type="checkbox" id="test76"
                           class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Machines", "machine", "index");
                           if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?>
                           value="1" name="featured3[6]"> <label for="test76"><small
                              style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                        <input type="checkbox" name="permission3[6]" <?php if ($getfeatured['edit'] == '1') { ?>
                              checked="checked" <?php } ?> value="1" id="test76"> edit &nbsp;<input type="checkbox"
                           name="permission_delete3[6]" <?php if ($getfeatured['delete'] == '1') { ?> checked="checked"
                           <?php } ?> value="1" id="test76"> delete
                           <input type="hidden" name="sort3[6]" value="52">
                           <input type="hidden" name="featuredno3[6]" value="52">
                     </td>
                  </tr>

                  <!-- Maintenance -->
                  <tr>
                     <td width="30%" class="paperclip"><input type="checkbox" name="menu3[7]" class="group3"
                           value="Maintenance^maintenance^index" <?php if (in_array("Maintenance", $menu)) { ?>
                              checked="checked" <?php } ?> id="2_arrgruop"> Maintenance <input type="checkbox" id="test77"
                           class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Maintenance", "maintenance", "index");
                           if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?>
                           value="1" name="featured3[7]"> <label for="test77"><small
                              style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                        <input type="checkbox" name="permission3[7]" <?php if ($getfeatured['edit'] == '1') { ?>
                              checked="checked" <?php } ?> value="1" id="test77"> edit &nbsp;<input type="checkbox"
                           name="permission_delete3[7]" <?php if ($getfeatured['delete'] == '1') { ?> checked="checked"
                           <?php } ?> value="1" id="test77"> delete
                           <input type="hidden" name="sort3[7]" value="30">
                           <input type="hidden" name="featuredno3[7]" value="30">
                     </td>
                  </tr>

               </tbody>
            </table>
         </div>
      </div>


   <!-- Stock Manager -->
      <div class="col-sm-6">
         <div class="title-div">
            <table width="80%">
               <tbody>
                  <tr>
                     <td width="30%"><strong>&nbsp;</strong></td>
                  </tr>
                  <tr style="background:#4993d7;">
                     <td width="30%" class="paperclip"><input type="checkbox" <?php if (in_array("Stock", $module)) { ?> checked="checked" <?php } ?> name="module4[]" class="parenth"
                           data-group=".group4" value="Stock" id="2_arrgruop">
                        <input type="hidden" name="module[]" value="Stock"><strong style="color:#fff;">
                           Stock</strong>
                     </td>
                     <td width="10%"></td>
                  </tr>

                  <!--  Daily Stock -->
                  <tr>
                     <td width="30%" class="paperclip"><input type="checkbox" name="menu4[1]" class="group4"
                           value="Daily Stock^stockregister^dailystock" <?php if (in_array("Daily Stock", $menu)) { ?>
                              checked="checked" <?php } ?> id="2_arrgruop"> Daily Stock <input type="checkbox" id="test81"
                           class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Daily Stock", "stockregister", "dailystock");
                           if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?>
                           value="1" name="featured4[1]"> <label for="test81"><small
                              style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                        <input type="checkbox" name="permission4[1]" <?php if ($getfeatured['edit'] == '1') { ?>
                              checked="checked" <?php } ?> value="1" id="test81"> edit &nbsp;<input type="checkbox"
                           name="permission_delete4[1]" <?php if ($getfeatured['delete'] == '1') { ?> checked="checked"
                           <?php } ?> value="1" id="test81"> delete
                           <input type="hidden" name="sort4[1]" value="32">
                           <input type="hidden" name="featuredno4[1]" value="32">
                     </td>
                  </tr>

     
               <!--  Stock -->
                  <tr>
                     <td width="30%" class="paperclip"><input type="checkbox" name="menu4[2]" class="group4"
                           value="Stock^stockregister^index" <?php if (in_array("Stock", $menu)) { ?>
                              checked="checked" <?php } ?> id="2_arrgruop"> Stock <input type="checkbox" id="test82"
                           class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Stock", "stockregister", "index");
                           if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?>
                           value="1" name="featured4[2]"> <label for="test82"><small
                              style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                        <input type="checkbox" name="permission4[2]" <?php if ($getfeatured['edit'] == '1') { ?>
                              checked="checked" <?php } ?> value="1" id="test82"> edit &nbsp;<input type="checkbox"
                           name="permission_delete4[2]" <?php if ($getfeatured['delete'] == '1') { ?> checked="checked"
                           <?php } ?> value="1" id="test82"> delete
                           <input type="hidden" name="sort4[2]" value="34">
                           <input type="hidden" name="featuredno4[2]" value="34">
                     </td>
                  </tr>

                  <!--  Stock Report -->
                  <tr>
                     <td width="30%" class="paperclip"><input type="checkbox" name="menu4[3]" class="group4"
                           value="Stock Report^stockregister^daily_stockreport" <?php if (in_array("Stock Report", $menu)) { ?>
                              checked="checked" <?php } ?> id="2_arrgruop"> Stock Report <input type="checkbox" id="test83"
                           class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Stock Report", "stockregister", "daily_stockreport");
                           if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?>
                           value="1" name="featured4[3]"> <label for="test83"><small
                              style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                        <input type="checkbox" name="permission4[3]" <?php if ($getfeatured['edit'] == '1') { ?>
                              checked="checked" <?php } ?> value="1" id="test83"> edit &nbsp;<input type="checkbox"
                           name="permission_delete4[3]" <?php if ($getfeatured['delete'] == '1') { ?> checked="checked"
                           <?php } ?> value="1" id="test83"> delete
                           <input type="hidden" name="sort4[3]" value="36">
                           <input type="hidden" name="featuredno4[3]" value="36">
                     </td>
                  </tr>

               </tbody>
            </table>
         </div>
      </div>


   <!-- Inpection Manager -->
      <div class="col-sm-6">
         <div class="title-div">
            <table width="80%">
               <tbody>
                  <tr>
                     <td width="30%"><strong>&nbsp;</strong></td>
                  </tr>
                  <tr style="background:#4993d7;">
                     <td width="30%" class="paperclip"><input type="checkbox" <?php if (in_array("Inspections", $module)) { ?> checked="checked" <?php } ?> name="module5[]" class="parenth"
                           data-group=".group5" value="Inspections" id="2_arrgruop">
                        <input type="hidden" name="module[]" value="Inspections"><strong style="color:#fff;">
                           Inspections</strong>
                     </td>
                     <td width="10%"></td>
                  </tr>
                  <tr>
                     <td width="30%" class="paperclip"><input type="checkbox" name="menu5[0]" class="group5"
                           value="Inspections^inspection^index" <?php if (in_array("Inspections", $menu)) { ?>
                              checked="checked" <?php } ?> id="2_arrgruop"> Inspections <input type="checkbox" id="test91"
                           class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Inspections", "inspection", "index");
                           if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?>
                           value="1" name="featured5[0]"> <label for="test91"><small
                              style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label>
                        <input type="checkbox" name="permission5[0]" <?php if ($getfeatured['edit'] == '1') { ?>
                              checked="checked" <?php } ?> value="1" id="test91"> edit &nbsp;<input type="checkbox"
                           name="permission_delete5[0]" <?php if ($getfeatured['delete'] == '1') { ?> checked="checked"
                           <?php } ?> value="1" id="test91"> delete
                           <input type="hidden" name="sort5[0]" value="56">
                           <input type="hidden" name="featuredno5[0]" value="56">
                     </td>
                     </td>
                  </tr>
               </tbody>
            </table>
         </div>
      </div>

<!-- Dispatch manager -->
      <div class="col-sm-6">
         <div class="title-div">
            <table width="80%">
               <tbody>
                  <tr>
                     <td width="30%"><strong>&nbsp;</strong></td>
                  </tr>
                  <tr style="background:#4993d7;">
                     <td width="30%" class="paperclip"><input type="checkbox" <?php if (in_array("Dispatch", $module)) { ?> checked="checked" <?php } ?> name="module6[]" class="parenth" data-group=".group6"
                           value="Dispatch" id="2_arrgruop">
                        <input type="hidden" name="module[]" value="Dispatch"><strong style="color:#fff;">
                           Dispatch</strong>
                     </td>
                     <td width="10%"></td>
                  </tr>
                  <tr>
                     <td width="30%" class="paperclip"><input type="checkbox" name="menu6[0]" class="group6"
                           value="Dispatch^transporter^index" <?php if (in_array("Dispatch", $menu)) { ?>
                              checked="checked" <?php } ?> id="2_arrgruop"> Dispatch <input type="checkbox" id="test95"
                           class="checkk" <?php $getfeatured = $this->Comman->findstatuspermission($empid, "Dispatch", "transporter", "index");
                           if ($getfeatured['featured'] == '1') { ?> checked="checked" <?php } ?>
                           value="1" name="featured6[0]"> <label for="test95"><small
                              style="margin-left: -13px;font-size:62%;">Featured on Top Menu</small></label> <input
                           type="checkbox" name="permission6[0]" <?php if ($getfeatured['edit'] == '1') { ?>
                              checked="checked" <?php } ?> value="1" id="test95"> edit &nbsp;<input type="checkbox"
                           name="permission_delete6[0]" <?php if ($getfeatured['delete'] == '1') { ?> checked="checked"
                           <?php } ?> value="1" id="test95"> delete
                           <input type="hidden" name="sort6[0]" value="58">
                           <input type="hidden" name="featuredno6[0]" value="58">
                     </td>
                     </td>
                  </tr>
               </tbody>
            </table>
         </div>
      </div>

</div>

</div>







