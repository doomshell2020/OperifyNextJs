 <!-- Content Wrapper. Contains page content -->
 <style>
   .checkbox input[type="checkbox"] {
     opacity: 1;
   }
 </style>
 <div class="content-wrapper">
   <!-- Content Header (Page header) -->
   <section class="content-header">
     <h1>
       <i class="fa fa-money"></i> Fees Collection Session Report
     </h1>
     <ol class="breadcrumb">
       <li><a href="<?php echo ADMIN_URL; ?>report/collectionrecipietmonthly"><i class="fa fa-home"></i>Home</a></li>
       <li><a href="<?php echo ADMIN_URL; ?>report/collectionrecipietmonthly">Manage Fees Report </a></li>
     </ol>
   </section>
   <!-- Main content -->
   <section class="content">
     <div class="row">
       <div class="col-xs-12">
         <style>
           #loader {
             display: none;
             position: absolute;
             left: 45%;
             width: 200px;
             height: 200px;
             padding: 30px 15px 0px;
             border: 3px solid #ababab;
             box-shadow: 1px 1px 10px #ababab;
             border-radius: 20px;
             background-color: white;
             z-index: 1002;
             text-align: center;
             overflow: auto;
           }
         </style>

         <div id="loader">
           <img src="<?php echo SITE_URL; ?>img/loading-gif-loader-v4.gif" class="img-responsive" />
         </div>
         <div class="box manage_feerpt_dv">
           <div class="box-header">
             <div class="modal" id="globalModal" tabindex="-1" role="dialog" aria-labelledby="esModalLabel" aria-hidden="true">
               <div class="modal-dialog">
                 <div class="modal-content">
                   <div class="modal-body">
                     <div class="loader">
                       <div class="es-spinner">
                         <i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i>
                       </div>
                     </div>
                   </div>
                 </div>
               </div>
             </div>

             <script inline="1">
               $(document).ready(function() {
                 $("#YourbuttonId").click(function() {
                   if ($('input[type=checkbox]:checked').length == 0) {
                     alert('Please select atleast one checkbox');
                   }
                 });
               });

               //<![CDATA[
               $(document).ready(function() {
                 $("#feesexl").bind("submit", function(event) {
                   $.ajax({
                     async: true,
                     data: $("#feesexl").serialize(),
                     dataType: "html",
                     type: "POST",
                     url: "<?php echo ADMIN_URL; ?>report/searchcollectionfee",
                     beforeSend: function() {
                       // Show image container
                       $("#loader").show();
                     },
                     success: function(data) {
                       //  alert(data); 

                       //	$("#updt").show();   
                       $("#updt").html(data);
                     },
                     complete: function(data) {
                       // Hide image container
                       $("#loader").hide();
                     },
                   });
                   return false;
                 });
               });
               //]]>
             </script>

             <?php echo $this->Form->create('Fees', array('type' => 'file', 'inputDefaults' => array('div' => false, 'label' => false), 'id' => 'feesexl', 'class' => 'form-horizontal')); ?>

             <div class="form-group">
               <div class="col-sm-2">
                 <label>Acedamic Year<span style="color:red;">*</span></label>
                 <?php
                  echo $this->Form->input(
                    'acedmicyear',
                    array('class' => 'qualified form-control', 'type' => 'select', 'empty' => 'All', 'options' => $acd, 'label' => false, 'id' => 'qualified')
                  );
                  ?>
               </div>
               <div class="col-sm-2">
                 <label for="inputEmail3" class="control-label">Select Class</label>
                 <?php echo $this->Form->input('class_id', array('class' => 'form-control', 'id' => 'subjt', 'type' => 'select', 'empty' => 'Select Class', 'options' => $classes, 'label' => false)); ?>
               </div>
               <div class="col-sm-2">
                 <script>
                   $(document).ready(function() {
                     $('#fdatefrom').datepicker({
                       dateFormat: 'yy-mm-dd',
                       yearRange: '2018:2030',
                       changeMonth: true,
                       changeYear: true,
                       onSelect: function(date) {

                         var selectedDate = new Date(date);
                         var endDate = new Date(selectedDate);
                         endDate.setDate(endDate.getDate());

                         $("#fendfrom").datepicker("option", "minDate", endDate);
                         $("#fendfrom").val(date);
                       }
                     });
                     $('#fdatefrom').datepicker('setDate', 'today');

                     $('#fendfrom').datepicker({
                       dateFormat: 'yy-mm-dd'
                     });
                     $('#fendfrom').datepicker('setDate', 'today');
                   });
                 </script>

                 <label for="inputEmail3" class="control-label">Date From</label>
                 <?php echo $this->Form->input('datefrom', array('class' => 'form-control', 'id' => 'fdatefrom', 'readonly', 'placeholder' => 'Date From', 'label' => false)); ?>
               </div>
               <div class="col-sm-2">
                 <label for="inputEmail3" class="control-label">Date To</label>
                 <?php echo $this->Form->input('dateto', array('class' => 'form-control', 'id' => 'fendfrom', 'readonly', 'placeholder' => 'Date To', 'label' => false)); ?>
               </div>
               <div class="col-sm-4">
                 <label for="inputEmail3" class="control-label">Netbanking Reference No. </label>
                 <?php echo $this->Form->input('ref_no', array('class' => 'form-control', 'placeholder' => 'Enter Reference No.', 'label' => false)); ?>
               </div>
             </div>
             <div class="form-group">
               <div class="col-sm-6">
                 <b style=" display:block;">Mode :</b> <span>&nbsp;&nbsp;&nbsp;&nbsp;<label class="checkbox-inline"><input id="radio1" name="mode[]" checked="checked" value="CASH" onclick="return checks(this);" type="checkbox">Cash</label>

                   <label class="checkbox-inline"><input name="mode[]" id="radio2" onclick="return checks(this);" value="CHEQUE" checked="checked" type="checkbox">Cheque</label>
                   <label class="checkbox-inline"><input id="radio13" name="mode[]" value="DD" onclick="return checks(this);" type="checkbox" checked="checked">Dd</label>
                   <label class="checkbox-inline"><input id="radio14" name="mode[]" value="NETBANKING" onclick="return checks(this);" checked="checked" type="checkbox">Netbanking</label>
                   <label class="checkbox-inline"><input id="radio15" name="mode[]" value="CREDIT CARD/DEBIT CARD" onclick="return checks(this);" checked="checked" type="checkbox">Credit Card/Debit Card</label>
                 </span>
               </div>

             </div>


             <div class="form-group">
               <script>
                 $(document).ready(function() {

                   //select all checkboxes
                   $("#select_all").change(function() {
                     //"select all" change 
                     $(".checkbox input[type='checkbox']").prop('checked', $(this).prop("checked")); //change all ".checkbox" checked status
                   });

                   $(".checkbox input[type='checkbox']").change(function() {
                     //uncheck "select all", if one of the listed checkbox item is unchecked
                     if (false == $(this).prop("checked")) { //if this item is unchecked
                       $("#select_all").prop('checked', false); //change "select all" checked status to false
                     }
                     //check "select all" if all checkbox items are checked
                     if ($('.checkbox:checked').length == $('.checkbox').length) {
                       $("#select_all").prop('checked', true);
                     }
                   });


                 });
               </script>


               <div style="padding: 5px 5px 0px 19px;">
                 <legend style="margin-bottom:0px;!important;">
                   <label><input type="checkbox" checked name="checkAll" id="select_all" value="1"> Select All</label>
                 </legend>
               </div>
             </div>
             <div class="form-group">
               <div class="col-sm-12">
                 <label>Fees Heads</label>
                 <div>


                   <div class="checkbox col-sm-3 col-xs-6"><label><input type="checkbox" name="selectField[]" checked="checked" value="<? echo "Prospectus"; ?>">&nbsp; <? echo "Prospectus"; ?></label></div>
                   <div class="checkbox col-sm-3 col-xs-6"><label><input type="checkbox" name="selectField[]" checked="checked" value="<? echo "Registration"; ?>">&nbsp; <? echo "Registration"; ?></label></div>
                   <div class="checkbox col-sm-3 col-xs-6"><label><input type="checkbox" name="selectField[]" value="<? echo "Prev. Due"; ?>" checked> &nbsp;<? echo ucwords(strtolower("Prev. Due")); ?></label></div>

                   <?php foreach ($feesheadstotal as $j => $el) { ?>
                     <div class="checkbox <? if ($el['name'] == "Bank Cancellation Charge") { ?>col-sm-3 col-xs-6<? } else { ?>col-sm-3 col-xs-6<? } ?>"><label><input type="checkbox" name="selectField[]" value="<? echo $el['name']; ?>" checked>&nbsp; <? echo ucwords(strtolower($el['name'])); ?></label></div>
                   <? } ?>

                   <div class="checkbox col-sm-3 col-xs-6"><label><input type="checkbox" name="selectField[]" value="<? echo "Late Fee"; ?>" checked>&nbsp; <? echo ucwords(strtolower("Late Fee")); ?></label></div>
                   <div class="checkbox col-sm-3 col-xs-6"><label><input type="checkbox" name="selectField[]" value="<? echo "Due Amount"; ?>" checked> &nbsp;<? echo "Due / Access Amount"; ?></label></div>
                   <div class="checkbox col-sm-3 col-xs-6"><label><input type="checkbox" name="selectField[]" value="<? echo "Discount Fee"; ?>" checked>&nbsp; <? echo  ucwords(strtolower("Discount Fee")); ?></label></div>
                   <div class="checkbox col-sm-3 col-xs-6"><label><input type="checkbox" name="selectField[]" value="<? echo "Other Discount"; ?>" checked> &nbsp;<?php echo ucwords(strtolower("Other Discount")); ?></label></div>
                   <div class="checkbox col-sm-3 col-xs-6"><label><input type="checkbox" name="selectField[]" value="<? echo "Transport Fee"; ?>" checked> &nbsp;<?php echo ucwords(strtolower("Transport Fee")); ?></label></div>

                 </div>
               </div>
             </div>


             <div class="form-group text-xs-center">
               <input type="submit" style="background-color:#00c0ef; color:#fff;width:100px !important; margin-left: 15px;" class="btn btn4 btn_pdf myscl-btn date" value="Submit">
               <button type="reset" style="background-color:#333; color:#fff;width:100px !important; margin-left: 15px;" class="btn btn4 btn_pdf myscl-btn date">Reset</button>
             </div>
             <?php
              echo $this->Form->end();
              ?>
             <!-- /.box-header -->
             <?php echo $this->Flash->render(); ?>
           </div>
           <div id="updt">
           </div>