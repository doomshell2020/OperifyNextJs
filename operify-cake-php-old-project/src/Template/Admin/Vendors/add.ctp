<style>
   .input_fields_wrap .form-control {
   margin-bottom: 15px;
   }
</style>
<div class="content-wrapper">
   <!-- Content Header (Page header) -->
   <section class="content-header">
      <h1>
         Vendor Manager
      </h1>
      <ol class="breadcrumb">
         <li><a href="<?php echo SITE_URL; ?>admin/vendors"><i class="fa fa-home"></i>Home</a></li>
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
               <div class="box-header with-border">
                  <h3 class="box-title"><i class="fa fa-plus-square" aria-hidden="true"></i> <?php if (isset($location['id'])) {
                     echo 'Edit Vendor Name';
                     } else {
                     echo 'Create New Vendor ';
                     } ?></h3>
               </div>
               <!-- /.box-header -->
               <!-- form start -->
               <?php echo $this->Form->create($vendor, array('class' => 'form-horizontal','id' => 'sevice_form'));
                  // pr($vendor); die;
                  ?>
               <div class="box-body">
                  <div class="form-group">
                     <div class="row">
                        <div class="col-sm-4" style="margin-bottom:15px;">
                           <label>Vendor Name</label> <strong style="color:red;">*</strong>
                           <?php echo $this->Form->input('name', array('class' => 'form-control', 'id' => 'title', 'placeholder' => 'Company Name', 'label' => false, 'required','style'=> 'text-transform: uppercase', 'autocomplete' => 'off')); ?>
                        </div>
                        <div class="col-sm-4" style="margin-bottom:15px;">
                           <label>Contact No</label><strong style="color:red;">*</strong>
                           <?php echo $this->Form->input('contact_no', array('class' => 'form-control', 'type' => 'number', 'placeholder' => 'Contact Number', 'label' => false, 'required','maxlength'=>'10' ,'onkeypress'=>'return isNumber(event);', 'type' => 'text', )); ?>
                        </div>
                        <div class="col-sm-4" style="margin-bottom:15px;">
                           <label>Contact Person Name</label><strong style="color:red;">*</strong>
                           <?php echo $this->Form->input('contactperson', array('class' => 'form-control', 'type' => 'text', 'placeholder' => 'Contact Person Name', 'label' => false, 'required', 'value' => $vendor['contact_person'])); ?>
                        </div>
                        <div class="col-sm-4" style="margin-bottom:15px;">
                           <label>Email Id</label><strong style="color:red;">*</strong>
                           <?php echo $this->Form->input('email', array('class' => 'form-control', 'type' => 'email', 'placeholder' => 'Email', 'id' => 'title', 'label' => false, 'required','style'=> 'text-transform: lowercase', 'autocomplete' => 'off')); ?>
                        </div>
                        <div class="col-sm-4" style="margin-bottom:15px;">
                           <label>PAN NO.</label><strong style="color:red;">*</strong>
                           <?php echo $this->Form->input('pancard_number', array('class' => 'form-control pancard', 'type' => 'text', 'maxlength' => '10', 'minlength' => '10','label' => false, 'placeholder' => 'PAN No.' ,'required', 'autocomplete' => 'off')); ?>
                        </div>
                        <div class="col-sm-4" style="margin-bottom:15px;">
                           <label>Tin NO.</label><strong style="color:red;"></strong>
                           <?php echo $this->Form->input('tin_no', array('class' => 'form-control pancard', 'type' => 'text', 'maxlength' => '15', 'label' => false, 'placeholder' => 'Tin No.', '', 'autocomplete' => 'off')); ?>
                        </div>
                        <?php if (date('Y-m-d', strtotime($vendor['tin_date'])) == "1970-01-01") {
                           $date_tin = '';
                           } else {
                           $date_tin = date('Y-m-d', strtotime($vendor['tin_date']));
                           } ?>
                        <div class="col-sm-4" style="margin-bottom:15px;">
                           <label for="inputEmail3" class="control-label" style="padding-top: 0px;">Tin Date</label><strong style="color:red;"></strong>
                           <?php echo $this->Form->input('tin_dated', array('class' => 'form-control input1', 'label' => false, 'placeholder' => 'From Date', 'id' => 'datepicker1', 'autocomplete' => 'off', 'readonly', 'value' => $date_tin)); ?>
                        </div>
                        <div class="col-sm-4" style="margin-bottom:15px;">
                           <label>State</label><strong style="color:red;">*</strong>
                           <?php echo $this->Form->input('billtostate_id', array('class' => 'form-control state', 'id' => 'billto_state_ids', 'type' => 'select', 'options' => $state, 'empty' => 'Select State', 'label' => false, 'required', 'value' => $vendor['state_id'])); ?>
                        </div>
                        <div class="col-sm-4" style="margin-bottom:15px;">
                           <label>GST No.</label><strong style="color:red;">*</strong>
                           <?php echo $this->Form->input('billtogst_number', array('class' => 'form-control gst', 'type' => 'text', 'maxlength' => '15', 'label' => false, 'placeholder' => 'GST No.', 'autocomplete' => 'off', 'value' => $vendor['gst_number'])); ?>
                        </div>
                        <div class="col-sm-4" style="margin-bottom:15px;">
                           <label>Address</label><strong style="color:red;">*</strong>
                           <?php echo $this->Form->textarea('billtoaddress', array('rows' => '2', 'class' => 'form-control ', 'placeholder' => 'Address', 'label' => false, 'required', 'value' => $vendor['address'])); ?>
                        </div>
                        <div class="col-sm-4" style="margin-bottom:15px;">
                           <label>Description</label>
                           <?php echo $this->Form->textarea('description', array('rows' => '2', 'class' => 'form-control', 'label' => false, 'placeholder' => 'Enter Description', 'autocomplete' => 'off')); ?>
                        </div>
                       
                        <div class="col-sm-4" style="margin-bottom:15px;">
                           <label for="inputEmail3" class="control-label">Type :</label><br>
                           <label class="radio-inline">
                           <input type="radio" name="vendortype" class="mode radio-inline checkstr" value="Vendor" <?php if ($vendor['type'] == "Vendor") {
                              echo "checked";
                              } ?>>&nbsp;Vendor
                           </label>
                           <label class="radio-inline">
                           <input type="radio" name="vendortype" class="mode radio-inline checkstr" value="Customer" <?php if ($vendor['type'] == "Customer") {
                              echo "checked";
                              } ?>>&nbsp; Customer
                           </label>
                           <label class="radio-inline">
                           <input type="radio" name="vendortype" class="mode radio-inline checkstr" value="Transporter" <?php if ($vendor['type'] == "Transporter") {
                              echo "checked";
                              } ?>>&nbsp; Transporter
                           </label>
                        </div>
                     </div>
                     <!-- <div class="col-sm-4">
                        <label class="control-lable">TDS :</label>   <br>
                          <input type="checkbox" name="tds" value="1" <? php // if($vendor['tds'] != 0){ echo 'checked' ;} 
                           ?> label=false>
                        </div> -->
                     <div class="col-sm-12">
                        <?php
                           if (isset($vendor['id']) && !empty($vendor['id'])) {
                             echo '<button type="submit" name="button" value="update" class="btn btn-success pull-right" id="formsubmitbtn" >Edit</button> ';
                           } else {
                             echo '<button type="submit" class="btn btn-success pull-right" id="formsubmitbtn" >Create</button> ';
                           }
                           ?>
                     </div>
                     <br>
                     <!-- <div class="all_vendorsdetails">
                        <h4 style="font-weight:bold;">Bill To <strong style="color:red;">*</strong></h4>
                        <? php // if(empty($vendor['id'])){ 
                           ?>
                        <div class="form-group">
                        <div class="col-sm-2 billtos">
                        <label>State</label>
                        </div>
                        <div class="col-sm-2 billtoc">
                        <label>City</label>
                          <?php //echo $this->Form->input('billtocity_id[]',array('class'=>'form-control city', 'id'=>'billto_city_ids', 'type'=>'select', 'empty'=>'Select City', 'label' =>false,'required')); 
                           ?>
                        </div>
                        <div class="col-sm-2">
                        <label>GST NO.</label>
                          <? php // echo $this->Form->input('billtogst_number[]', array('class' => 'form-control gst','type'=>'text','maxlength'=>'15','label'=>false,'placeholder'=>'GST No.','autocomplete'=>'off','required')); 
                           ?>
                        </div>
                        <div class="col-sm-4">
                          <label>Address</label>
                          <?php //echo $this->Form->textarea('billtoaddress[]', array('rows'=>'2', 'class'=>'form-control address','placeholder'=>'Address', 'label' =>false,'required')); 
                           ?>
                        </div>
                        <div class="col-sm-2">
                          <label class="control-lable">Same As Copy</label>  <br>
                            <input type="checkbox" name="copy"  id="sameascopy" value="1" label=false> -->
                     <script type="text/javascript">
                        $(document).ready(function() {
                          $("#sameascopy").on('change', function() {
                            if ($(this).prop("checked") == true) {
                              var ss = $(this).closest('.all_vendorsdetails').find('.state option:selected').val();
                              $(this).closest('.all_vendorsdetails').find('.shipstate').val(ss);
                              var cs = $(this).closest('.all_vendorsdetails').find('.city option:selected').val();
                              $(this).closest('.all_vendorsdetails').find('.shipcity').val(cs);
                              var gst = $(this).closest('.all_vendorsdetails').find('.gst').val();
                              $(this).closest('.all_vendorsdetails').find('.shipgst').val(gst);
                              var shipaddress = $(this).closest('.all_vendorsdetails').find('.address').val();
                              $(this).closest('.all_vendorsdetails').find('.shipaddress').val(shipaddress);
                            } else if ($(this).prop("checked") == false) {
                              $(this).closest('.all_vendorsdetails').find('.shipstate option[value=""]').prop("selected", true);
                              $(this).closest('.all_vendorsdetails').find('.shipcity option[value=""]').prop("selected", true);
                              $(this).closest('.all_vendorsdetails').find('.shipgst').val('');
                              $(this).closest('.all_vendorsdetails').find('.shipaddress').text('');
                            }
                          });
                        });
                     </script>
                  </div>
               </div>
               <!-- <h4 style="font-weight:bold;">Ship From<strong style="color:red;">*</strong></h4>
                  <div class="form-group">
                  <div class="col-sm-2 shiptos">
                  <label>State</label>
                    <?php //echo $this->Form->input('shipfromstate_id[]',array('class'=>'form-control shipstate', 'id'=>'shipto_state_ids', 'type'=>'select', 'options'=>$state, 'empty'=>'Select State', 'label' =>false,'required')); 
                     ?>
                  </div>
                  <div class="col-sm-2 shiptoc">
                    <label>City</label>
                    <?php //echo $this->Form->input('shipfromcity_id[]',array('class'=>'form-control shipcity', 'id'=>'shipto_city_ids','type'=>'select', 'empty'=>'Select City', 'options'=>$city,'label' =>false,'required')); 
                     ?>
                  </div>
                  <div class="col-sm-2">
                  <label>GST NO.</label>
                    <?php //echo $this->Form->input('shipfromgst_number[]', array('class' => 'form-control shipgst','type'=>'text','maxlength'=>'15','label'=>false,'placeholder'=>'GST No.','autocomplete'=>'off','required')); 
                     ?>
                  </div>
                  <div class="col-sm-4">
                    <label>Address</label>
                    <?php //echo $this->Form->textarea('shipfromaddress[]', array('rows'=>'2', 'class'=>'form-control shipaddress','placeholder'=>'Address', 'label' =>false,'required')); 
                     ?>
                  </div>
                  <div class="col-sm-2">
                    <a href="javascript:void(0)" class="add-billto-fields" style="font-size:30px; margin-top: 20px; display: inline-block;"><i class="fa fa-plus-circle" aria-hidden="true"></i></a>
                  </div> -->
               <!-- </div>
                  </div> -->
               <div class="billto_product_containes">
               </div>
               <? php // }else{  $cntt=0;   
                  ?>
               <!-- <?php //foreach($Vendorbillto as $ky=>$val){  
                  ?>
                  <div class="form-group">
                  <div class="col-sm-2 billtos">
                  <label>State</label>
                    <? php // echo $this->Form->input('billtostate_id[]',array('class'=>'form-control state', 'id'=>'billto_state_ids', 'type'=>'select', 'options'=>$state, 'default'=>$val['state_id'],'empty'=>'Select State', 'label' =>false,'required')); 
                     ?>
                  </div>
                  <div class="col-sm-2 billtoc">
                  <label>City</label>
                    <? php // echo $this->Form->input('billtocity_id[]',array('class'=>'form-control city', 'id'=>'billto_city_ids', 'type'=>'select', 'empty'=>'Select City','options'=>$city, 'default'=>$val['city_id'],'label' =>false,'required')); 
                     ?>
                  </div>
                  <div class="col-sm-2">
                  <label>GST NO.</label>
                    <?php //echo $this->Form->input('billtogst_number[]', array('class' => 'form-control gst','type'=>'text','maxlength'=>'15','label'=>false,'placeholder'=>'GST No.','default'=>$val['gst_number'],'autocomplete'=>'off','required')); 
                     ?>
                  </div>
                  <div class="col-sm-4">
                    <label>Address</label>
                    <?php //echo $this->Form->textarea('billtoaddress[]', array('rows'=>'2', 'class'=>'form-control address','placeholder'=>'Address', 'default'=>$val['address'],'label' =>false,'required')); 
                     ?>
                  </div>
                  </div> -->
               <?php //} 
                  ?>
               <!--
                  <h4 style="font-weight:bold;">Ship From<strong style="color:red;">*</strong></h4>
                  <?php //foreach($Vendorshipfrom as $ky=>$vals){  
                     ?>
                  <div class="form-group">
                  <div class="col-sm-2 shiptos">
                  <label>State</label>
                    <?php //echo $this->Form->input('shipfromstate_id[]',array('class'=>'form-control shipstate', 'id'=>'shipto_state_ids', 'type'=>'select', 'options'=>$state, 'empty'=>'Select State','default'=>$vals['state_id'], 'label' =>false,'required')); 
                     ?>
                  </div>
                  <div class="col-sm-2 shiptoc">
                    <label>City</label>
                    <?php //echo $this->Form->input('shipfromcity_id[]',array('class'=>'form-control shipcity', 'id'=>'shipto_city_ids','type'=>'select', 'empty'=>'Select City', 'options'=>$city,'default'=>$vals['city_id'],'label' =>false,'required')); 
                     ?>
                  </div>
                  <div class="col-sm-2">
                  <label>GST NO.</label>
                    <? php // echo $this->Form->input('shipfromgst_number[]', array('class' => 'form-control shipgst','type'=>'text','maxlength'=>'15','label'=>false,'placeholder'=>'GST No.','default'=>$vals['gst_number'],'autocomplete'=>'off','required')); 
                     ?>
                  </div>
                  <div class="col-sm-4">
                    <label>Address</label>
                    <?php //echo $this->Form->textarea('shipfromaddress[]', array('rows'=>'2', 'class'=>'form-control shipaddress','placeholder'=>'Address', 'label' =>false,'required','default'=>$vals['address'])); 
                     ?>
                  </div>
                  <?php //if($cntt==0){ 
                     ?>
                  <div class="col-sm-2">
                    <a href="javascript:void(0)" class="add-billto-fields" style="font-size:30px; margin-top: 20px; display: inline-block;"><i class="fa fa-plus-circle" aria-hidden="true"></i></a>
                  </div> -->
               <?php //} 
                  ?>
            </div>
            <?php //$cntt++; } 
               ?>
         </div>
         <div class="billto_product_containes">
         </div>
         <?php // } 
            ?>
         <div class="form-group">
            <div class="col-sm-12">
               <!-- <?php
                  // if( isset( $vendor[id] ) && !empty( $vendor[id] ) )
                  // {
                  //echo '<button type="submit" name="button" value="update" class="btn btn-success pull-right">Update</button> ';
                  //  }
                  // else
                  //{
                  // echo '<button type="submit" class="btn btn-success pull-right">Create</button> ';
                  ///  }
                  // 
                  ?> -->
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
   $(document).ready(function() {
     $('#datepicker1').datepicker({
       dateFormat: 'dd-mm-yy',
     });
     //$('#datepicker1').datepicker('setDate', 'today');
   });
</script>
<script type="text/javascript">
   $("#myemail").change(function() {
     //alert('hello');
     var txt = $('#myemail').val();
     var testCases = [txt];
     var test = testCases;
     if (isValidEmailAddress(test) != true) {
       $('#mailvalid').css('display', 'block');
       $('#myemail').val('');
     } else {
       $('#mailvalid').css('display', 'none');
     }
   });
</script>
<script>
   function isValidEmailAddress(emailAddress) {
     var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
     return pattern.test(emailAddress);
   };
   
   function isNumber(evt) {
     evt = (evt) ? evt : window.event;
     var charCode = (evt.which) ? evt.which : evt.keyCode;
     if (charCode > 31 && (charCode < 46 || charCode > 57 || charCode == 47)) {
       alert("Please Enter Only Numeric Characters!!!!");
       return false;
     }
     return true;
   }
   
   function checkLength() {
     var textbox = document.getElementById("addnum");
     if (textbox.value.length >= 10 && textbox.value.length <= 12) {
       return true;
     } else {
       alert("Enter minimum 10 digits of your mobile number");
       $('#addnum').val('');
       return false;
     }
   }
</script>
<script type="text/javascript">
   $(document).ready(function() {
     $(".billtos #billto_state_ids").on('change', function() {
       var id = $(this).val();
       $(".billtoc #billto_city_ids").find('option').remove();
       //$("#city").find('option').remove();
       if (id) {
         var dataString = id;
         $.ajax({
           type: "POST",
           url: '<?php echo SITE_URL; ?>/admin/vendors/getcity',
           data: {
             'dataString': id
           },
           cache: false,
           success: function(html) {
             //alert(html);
             $('<option>').val("").text("Select City").appendTo($(".billtoc #billto_city_ids"));
             $.each(html, function(key, value) {
               $('<option>').val(key).text(value).appendTo($(".billtoc #billto_city_ids"));
             });
           }
         });
       }
     });
     $(".shiptos #shipto_state_ids").on('change', function() {
       var id = $(this).val();
       $(".shiptoc #shipto_city_ids").find('option').remove();
       //$("#city").find('option').remove();
       if (id) {
         var dataString = id;
         $.ajax({
           type: "POST",
           url: '<?php echo SITE_URL; ?>/admin/vendors/getcity',
           data: {
             'dataString': id
           },
           cache: false,
           success: function(html) {
             //alert(html);
             $('<option>').val("").text("Select City").appendTo($(".shiptoc #shipto_city_ids"));
             $.each(html, function(key, value) {
               $('<option>').val(key).text(value).appendTo($(".shiptoc #shipto_city_ids"));
             });
           }
         });
       }
     });
   });
</script>
<script>
   $(document).ready(function() {
     $(".add-billto-fields").click(function() {
       var numItems = $('.billto_video_details').length;
       numItems++;
       $.ajax({
         type: "POST",
         url: '<?php echo SITE_URL; ?>admin/vendors/billto',
         data: {
           'srno': numItems,
         },
         cache: false,
         success: function(html) {
           //alert(html);
           $(".billto_product_containes").append(html);
         }
       });
     });
     $("body").on("click", ".billto_remove", function() {
       $(this).closest('.all_vendorsdetails').remove();
     });
     $(".add-shipto-fields").click(function() {
       var numItems = $('.shipto_video_details').length;
       numItems++;
       $.ajax({
         type: "POST",
         url: '<?php echo SITE_URL; ?>admin/vendors/shipfrom',
         data: {
           'srno': numItems,
         },
         cache: false,
         success: function(html) {
           //alert(html);
           $(".shipto_product_containes").append(html);
   
         }
       });
     });
     $("body").on("click", ".shipto_remove", function() {
       $(this).closest('.shipto_video_details').remove();
     });
   });
</script>
<script src="https://code.jquery.com/ui/1.11.0/jquery-ui.js"></script>
<script>
   $(function() {
     var dateFormat = 'dd-mm-yy',
       from = $("#datepicker1")
       .datepicker({
         dateFormat: 'dd-mm-yy',
         changeMonth: true,
         numberOfMonths: 1
       })
       .on("change", function() {
         to.datepicker("option", "minDate", getDate(this));
       }),
       to = $("#datepicker2").datepicker({
         dateFormat: 'dd-mm-yy',
         changeMonth: true,
         numberOfMonths: 1
       })
       .on("change", function() {
         from.datepicker("option", "maxDate", getDate(this));
       });
   
     function getDate(element) {
       var date;
       try {
         date = $.datepicker.parseDate(dateFormat, element.value);
       } catch (error) {
         date = null;
       }
       return date;
     }
   });
   $(function() {
     var $radios = $('input:radio[name=vendortype]');
     if ($radios.is(':checked') === false) {
       $radios.filter('[value=Vendor]').prop('checked', true);
     }
   });
</script>
<script>
    $(document).ready(function () {
        $('#sevice_form').on('submit', function (e) {
            $("#formsubmitbtn").css("display", "none");
        });
        });
</script>