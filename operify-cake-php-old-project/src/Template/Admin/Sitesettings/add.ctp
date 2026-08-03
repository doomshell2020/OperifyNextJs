<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
   <!-- Content Header (Page header) -->
   <section class="content-header">
      <h1>
         Profile Manager
      </h1>
      <ol class="breadcrumb">
         <li><a href="<?php echo ADMIN_URL; ?>sitesettings"><i class="fa fa-home"></i>Home</a></li>
         <li><a href="<?php echo ADMIN_URL; ?>sitesettings/add">Manage Profile</a></li>
      </ol>
   </section>
   <!-- Main content -->
   <section class="content">
      <div class="row">
         <!--/.col (left) -->
         <!-- right column -->
         <div class="col-md-12">
            <!-- Horizontal Form -->
            <div class="box box-info">

               <!-- /.box-header -->
               <!-- form start -->
               <?php echo $this->Flash->render();
               echo $this->Form->create($sitesetting, array(

                  'class' => 'form-horizontal',
                  'id' => 'sitesetting_form',
                  'enctype' => 'multipart/form-data',
                  'novalidate',
               )); ?>
               <div class="box-body">
                  <div class="row">
                     <div class="col-sm-3">
                        <label for="inputEmail3" class="col-sm-12 control-label">First Name</label>
                        <?php echo $this->Form->input('first_name', array('class' => 'form-control', 'placeholder' => 'First Name', 'id' => 'first_name', 'required' => 'required', 'label' => false)); ?>
                     </div>
                     <div class="col-sm-3">
                        <label for="inputEmail3" class="col-sm-12 control-label">Last Name</label>
                        <?php echo $this->Form->input('last_name', array('class' => 'form-control', 'placeholder' => 'Last Name', 'id' => 'last_name', 'label' => false, 'required' => 'required')); ?>
                     </div>
                     <!-- <div class="col-sm-3">
                     <label for="inputEmail3" class="col-sm-12 control-label">Mobile</label>
                     <?php echo $this->Form->input('mobile', array('class' => 'form-control', 'maxlength' => '10', 'placeholder' => 'Mobile', 'label' => false, 'required' => 'required')); ?>
                  </div> -->
                     <div class="col-sm-3">
                        <label for="inputEmail3" class="col-sm-12 control-label">Phone</label>
                        <?php echo $this->Form->input('phone', array('type' => 'number', 'class' => 'form-control', 'placeholder' => 'Phone', 'label' => false, 'value' => $sitesetting['sitesettings_detail']['phone'], 'id' => 'phone_no')); ?>
                        <small id="phoneError" style="color: red; display: none;">Please enter a valid phone
                           number.</small>
                     </div>

                     <script>
                        document.getElementById('phone_no').addEventListener('input', function () {
                           var phoneNo = this.value;
                           var phoneRegex = /^(?:\+?\d{1,3}[-.\s]?)?(?:\(?\d{1,4}\)?[-.\s]?)?\d{3}[-.\s]?\d{4}$/;

                           if (!phoneRegex.test(phoneNo)) {
                              document.getElementById('phoneError').style.display = 'inline';
                              this.style.borderColor = 'red';
                           } else {
                              document.getElementById('phoneError').style.display = 'none';
                              this.style.borderColor = 'green';
                           }
                        });
                     </script>

                     <!-- <div class="col-sm-3">
                     <label for="inputEmail3" class="col-sm-12 control-label">Contact Email</label>
                     <?php echo $this->Form->input('contact_email', array('class' => 'form-control', 'placeholder' => 'Contact Email', 'id' => 'contact_email', 'required' => 'required', 'label' => false)); ?>
                  </div> -->
                     <div class="col-sm-3">
                        <label for="inputEmail3" class="col-sm-12 control-label">Email</label>
                        <?php echo $this->Form->input('email', array('class' => 'form-control', 'placeholder' => 'Email', 'label' => false, 'value' => $sitesetting['sitesettings_detail']['email'])); ?>
                     </div>
                  </div>
                  <!-- <div class="form-group">
                  <label for="inputEmail3" class="col-sm-2 control-label"></label>
                  <div class="col-sm-10 ">
                     <h4 style="font-weight:bold;color:red;"> Do you wish to change password ?</h4>
                  </div>
               </div> -->
                  <div class="passdata" style="display:block;">
                     <!-- <div class="row">
                     <div class="col-sm-3">
                        <label for="inputEmail3" class="col-sm-12 control-label">Current Password</label>
                        <? php// echo $this->Form->input('current_password', array('class' => 'form-control', 'placeholder' => 'Current Password', 'id' => 'current_password', 'value' => $user['confirm_pass'], 'required' => 'required', 'label' => false)); ?>
                     </div>
                     <div class="col-sm-3">
                        <label for="inputEmail3" class="col-sm-12 control-label">New Password</label>
                        <? php// echo $this->Form->input('new_password', array('class' => 'form-control', 'placeholder' => 'New Password', 'id' => 'password', 'label' => false)); ?>
                     </div>
                     <div class="col-sm-3">
                        <label for="inputEmail3" class="col-sm-12 control-label">Confirm Password</label>
                        <? php// echo $this->Form->input('confirm_pass', array('class' => 'form-control', 'placeholder' => 'Confirm Password', 'id' => 'confirm_pass', 'label' => false)); ?>
                     </div>
                  </div> -->

                     <!-- <hr> -->

                     <div class="row">


                        <div class="col-sm-3">
                           <label for="inputEmail3" class="col-sm-12 control-label">Address Line 1</label>
                           <?php echo $this->Form->input('address1', array('class' => 'form-control', 'placeholder' => 'Address', 'label' => false, 'value' => $sitesetting['sitesettings_detail']['address1'])); ?>
                        </div>
                        <div class="col-sm-3">
                           <label for="inputEmail3" class="col-sm-12 control-label">Address Line 2</label>
                           <?php echo $this->Form->input('address2', array('class' => 'form-control', 'placeholder' => 'Address', 'label' => false, 'value' => $sitesetting['sitesettings_detail']['address2'])); ?>
                        </div>

                        <div class="col-sm-3">
                           <label for="inputEmail3" class="col-sm-12 control-label">Fax</label>
                           <?php echo $this->Form->input('fax', array('class' => 'form-control', 'placeholder' => 'Fax', 'label' => false, 'value' => $sitesetting['sitesettings_detail']['fax'])); ?>
                        </div>

                        <div class="col-sm-3">
                           <label for="inputEmail3" class="col-sm-12 control-label">Web Site</label>
                           <?php echo $this->Form->input('website', array('class' => 'form-control', 'placeholder' => 'Web Site', 'label' => false, 'value' => $sitesetting['sitesettings_detail']['website'])); ?>
                        </div>


                        <div class="col-sm-3">
                           <label for="inputEmail3" class="col-sm-12 control-label">Small Logo</label>
                           <?php
                           ?>
                           <?php echo $this->Form->input('small_logo', array('class' => 'form-control', 'type' => 'file', 'id' => '', 'label' => false)); ?>
                           <span style="color:red; font-size: 13px;">Note:- Please select image size
                              100*100px</span><br>
                           <img
                              src="<?php echo SITE_URL; ?>images/<?php echo $sitesetting['sitesettings_detail']['small_logo']; ?>"
                              alt="" height="50" width="50" />
                        </div>

                        <!-- <div class="col-sm-3">

                           <label for="inputEmail3" class="col-sm-12 control-label">Header Logo</label>
                           <?php echo $this->Form->input('header_logo', array('class' => 'form-control', 'type' => 'file', 'id' => '', 'label' => false)); ?>
                           <span style="color:red; font-size: 13px;">Note:- Please select image size
                              504*136px</span><br>
                           <img
                              src="<?php echo SITE_URL; ?>images/<?php echo $sitesetting['sitesettings_detail']['header_logo']; ?>"
                              alt="" height="50" width="50" />
                        </div> -->
                        <div class="col-md-4">
                           <label class="control-label">Stock Update</label>
                           <div class="field" style="margin-top: 10px;">
                              <label class="radio-inline" style="margin-right: 15px;">
                                 <input type="radio" name="stock_update" value="Y" <?php echo ($sitesetting['sitesettings_detail']['stock_update'] == "Y") ? 'checked' : ''; ?>>Y
                              </label>

                              <label class="radio-inline">
                                 <input type="radio" name="stock_update" value="N" <?php echo ($sitesetting['sitesettings_detail']['stock_update'] == "N") ? 'checked' : ''; ?>>N
                              </label>
                           </div>
                        </div>

                     </div>
                     <label style="margin-top:20px;">Taxsection</label>
                     <div style="border:1px solid #999; padding:20px; padding-bottom:0px;">
                        <div class="form-group">
                           <div class="row">
                              <div class="col-sm-4">
                                 <label for="inputEmail3" class="col-sm-12 control-label">Company Name</label>
                                 <?php echo $this->Form->input('cname', array('class' => 'form-control', 'placeholder' => 'Company Name', 'label' => false, 'value' => $sitesetting['sitesettings_detail']['ac_holder'])); ?>
                              </div>
                              <div class="col-sm-4" style="margin-bottom:10px;">
                                 <label for="inputEmail3" class="col-sm-12 control-label">Pan No</label>
                                 <?php echo $this->Form->input('pan_no', array('class' => 'form-control', 'placeholder' => 'Pan No', 'label' => false, 'value' => $sitesetting['sitesettings_detail']['pan_number'], 'id' => 'pan_no')); ?>
                                 <small id="panError" style="color: red; display: none;">Please enter a valid Pan number
                                    (Format: AAAAA9999A).</small>
                              </div>

                              <script>
                                 document.getElementById('pan_no').addEventListener('blur', function () {
                                    var panNo = this.value;
                                    var panRegex = /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/;

                                    if (!panRegex.test(panNo)) {
                                       document.getElementById('panError').style.display = 'inline';
                                       this.style.borderColor = 'red';
                                    } else {
                                       document.getElementById('panError').style.display = 'none';
                                       this.style.borderColor = 'green';
                                    }
                                 });
                              </script>

                              <div class="col-sm-4" style="margin-bottom:10px;">
                                 <label for="inputEmail3" class="col-sm-12 control-label">GST No</label>
                                 <?php echo $this->Form->input('gst_no', array('class' => 'form-control', 'placeholder' => 'GST No', 'label' => false, 'value' => $sitesetting['sitesettings_detail']['gst_no'])); ?>
                              </div>
                              <div class="col-sm-4" style="margin-bottom:10px;">
                                 <label for="inputEmail3" class="col-sm-12 control-label">Tin Date</label>
                                 <?php echo $this->Form->input('tin_date', array('class' => 'form-control input1', 'label' => false, 'placeholder' => 'From Date', 'id' => 'datepicker1', 'autocomplete' => 'off', 'readonly', 'value' => date('Y-m-d', strtotime($sitesetting['sitesettings_detail']['tin_date'])))); ?>
                              </div>
                              <div class="col-sm-4" style="margin-bottom:10px;">
                                 <label for="inputEmail3" class="col-sm-12 control-label">Account No</label>
                                 <?php echo $this->Form->input('account_number', array('class' => 'form-control', 'placeholder' => 'Account No', 'label' => false, 'value' => $sitesetting['sitesettings_detail']['account_number'])); ?>
                              </div>
                              <div class="col-sm-4" style="margin-bottom:10px;">
                                 <label for="inputEmail3" class="col-sm-12 control-label">IFSC Code</label>
                                 <?php echo $this->Form->input('ifsc', array('class' => 'form-control', 'placeholder' => 'IFSC Code', 'label' => false, 'value' => $sitesetting['sitesettings_detail']['ifsc'])); ?>
                              </div>
                              <div class="col-sm-4" style="margin-bottom:10px;">
                                 <label for="inputEmail3" class="col-sm-12 control-label">Address </label>
                                 <?php echo $this->Form->input('address', array('class' => 'form-control', 'placeholder' => 'Address', 'label' => false, 'value' => $sitesetting['sitesettings_detail']['address'])); ?>
                              </div>
                              <div class="col-sm-4" style="margin-bottom:10px;">
                                 <label for="inputEmail3" class="col-sm-12 control-label">Company Number </label>
                                 <?php echo $this->Form->input('cmobile_no', array('class' => 'form-control', 'placeholder' => 'Company Number', 'label' => false, 'value' => $sitesetting['sitesettings_detail']['cmobile_no'])); ?>
                              </div>
                              <div class="col-sm-4" style="margin-bottom:10px;">
                                 <label for="inputEmail3" class="col-sm-12 control-label">Alias</label>
                                 <?php echo $this->Form->input('alias', array('class' => 'form-control', 'placeholder' => 'Company Number', 'label' => false, 'value' => $sitesetting['sitesettings_detail']['alias'])); ?>
                              </div>
                              <div style="clear:both"></div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <!-- /.box-body -->
                  <div class="box-footer">
                     <?php
                     if (isset($sitesetting['id'])) {
                        echo $this->Form->submit(
                           'Update',
                           array('class' => 'btn btn-info pull-right', 'title' => 'Update')
                        );
                     } else {
                        echo $this->Form->submit(
                           'Add',
                           array('class' => 'btn btn-info pull-right', 'title' => 'Add')
                        );
                     }
                     ?><?php
                     echo $this->Html->link('Cancel', [
                        'controller' => 'dashboards',
                        'action' => 'index',

                     ], ['class' => 'btn btn-default']); ?>
                  </div>
                  <!-- /.box-footer -->
                  <?php echo $this->Form->end(); ?>
               </div>
            </div>
            <!--/.col (right) -->
         </div>
         <!-- /.row -->
   </section>
   <!-- /.content -->
</div>
<script type="text/javascript">
   $('#checkbox1').change(function () {
      if (this.checked)
         $('.passdata').show();
      else
         $('.passdata').hide();

   });
</script>
<script src="https://code.jquery.com/ui/1.11.0/jquery-ui.js"></script>
<script>
   $(function () {
      var dateFormat = 'dd-mm-yy',
         from = $("#datepicker1")
            .datepicker({
               dateFormat: 'dd-mm-yy',
               changeMonth: true,
               numberOfMonths: 1
            })
            .on("change", function () {
               to.datepicker("option", "minDate", getDate(this));
            }),
         to = $("#datepicker2").datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            numberOfMonths: 1
         })
            .on("change", function () {
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
</script>