
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
            <div class="box-header with-border">
               <h3 class="box-title">Edit Profile setting</h3>
               <a class="pull-right btn btn-success" href="<?php echo ADMIN_URL; ?>Measurementunit" style="margin-left:5px">Measurementunits</a>
               <a class="pull-right btn btn-success" href="<?php echo ADMIN_URL; ?>Paymentmanager" style="margin-left:5px">Payment Terms
               </a>
               <a class="pull-right btn btn-success" href="<?php echo ADMIN_URL; ?>taxmaster" style="margin-left:5px">Tax Master</a>
               <a class="pull-right btn btn-success" href="<?php echo ADMIN_URL; ?>Roles" style="margin-left:5px">Roles
               Manager</a>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <?php echo $this->Flash->render();
               echo $this->Form->create('', array(
               
                 'class' => 'form-horizontal',
                 'id' => 'sitesetting_form',
                 'enctype' => 'multipart/form-data',
                 'novalidate',
               )); ?>
            <div class="box-body">
               <div class="row">
               <?php echo $this->Form->input('id', array(
               'type' => 'hidden',
                 'value' => $sitesetting['id'] // Replace with the appropriate ID value
                  )); ?>
                  <div class="col-sm-3">
                     <label for="inputEmail3" class="col-sm-12 control-label">First Name</label>
                     <?php echo $this->Form->input('first_name', array('class' => 'form-control', 'placeholder' => 'First Name', 'id' => 'first_name', 'required' => 'required','value' =>$sitesetting['first_name'], 'label' => false)); ?>
                     
                  </div>
                  <div class="col-sm-3">
                     <label for="inputEmail3" class="col-sm-12 control-label">Last Name</label>
                     <?php echo $this->Form->input('last_name', array('class' => 'form-control', 'placeholder' => 'Last Name', 'id' => 'last_name', 'label' => false,'value' =>$sitesetting['last_name'], 'required' => 'required')); ?>
                  </div>
                  <div class="col-sm-3">
                     <label for="inputEmail3" class="col-sm-12 control-label">Mobile</label>
                     <?php echo $this->Form->input('mobile', array('class' => 'form-control', 'maxlength' => '10', 'placeholder' => 'Mobile', 'label' => false, 'value' =>$sitesetting['mobile'],'required' => 'required')); ?>
                  </div>
                  <div class="col-sm-3">
                     <label for="inputEmail3" class="col-sm-12 control-label">Contact Email</label>
                     <?php echo $this->Form->input('contact_email', array('class' => 'form-control', 'placeholder' => 'Contact Email', 'id' => 'contact_email',  'value' =>$sitesetting['contact_email'],'required' => 'required', 'label' => false)); ?>
                  </div>
                  <div class="col-sm-3">
                     <label for="inputEmail3" class="col-sm-12 control-label">Facebook URL</label>
                     <?php echo $this->Form->input('facebook_url', array('class' => 'form-control', 'placeholder' => 'Facebook URL', 'id' => 'facebook_url', 'value' =>$sitesetting['facebook_url'], 'label' => false)); ?>
                  </div>
                  <div class="col-sm-3">
                     <label for="inputEmail3" class="col-sm-12 control-label">Twitter URL</label>
                     <?php echo $this->Form->input('twitter_url', array('class' => 'form-control', 'placeholder' => 'Twitter URL', 'id' => 'twitter_url', 'value' =>$sitesetting['twitter_url'], 'label' => false)); ?>
                  </div>
                  <div class="col-sm-3">
                     <label for="inputEmail3" class="col-sm-12 control-label">Site Title</label>
                     <?php echo $this->Form->input('site_title', array('class' => 'form-control', 'placeholder' => 'Site Title', 'id' => 'site_title', 'value' =>$sitesetting['site_title'], 'label' => false)); ?>
                  </div>
                  <div class="col-sm-3">
                     <label for="inputEmail3" class="col-sm-12 control-label">Site Keywords</label>
                     <?php echo $this->Form->input('site_keywords', array('class' => 'form-control', 'placeholder' => 'Site Keywords',  'value' =>$sitesetting['site_keywords'],'id' => 'site_keyword', 'label' => false)); ?>
                  </div>
                  <div class="col-sm-6">
                     <label for="inputEmail3" class="col-sm-12 control-label">Site Description</label>
                     <?php echo $this->Form->textarea('site_description', array('class' => 'form-control', 'placeholder' => 'Site Description', 'value' =>$sitesetting['site_description'], 'id' => 'site_description', 'label' => false)); ?>
                  </div>
                  <div class="col-sm-6">
                     <label for="inputEmail3" class="col-sm-12 control-label">Google Analytics</label>
                     <?php echo $this->Form->textarea('google_analytics', array('class' => 'form-control', 'placeholder' => 'Google Analytics',  'value' =>$sitesetting['google_analytics'],'id' => 'google_analytics', 'label' => false)); ?>
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
                        <?php// echo $this->Form->input('current_password', array('class' => 'form-control', 'placeholder' => 'Current Password', 'id' => 'current_password', 'value' => $user['confirm_pass'], 'required' => 'required', 'label' => false)); ?>
                     </div>
                     <div class="col-sm-3">
                        <label for="inputEmail3" class="col-sm-12 control-label">New Password</label>
                        <?php// echo $this->Form->input('new_password', array('class' => 'form-control', 'placeholder' => 'New Password', 'id' => 'password', 'label' => false)); ?>
                     </div>
                     <div class="col-sm-3">
                        <label for="inputEmail3" class="col-sm-12 control-label">Confirm Password</label>
                        <?php// echo $this->Form->input('confirm_pass', array('class' => 'form-control', 'placeholder' => 'Confirm Password', 'id' => 'confirm_pass', 'label' => false)); ?>
                     </div>
                  </div> -->
                 
                  <hr>
                  <div class="row">
                     <div class="col-sm-3">
                        <label for="inputEmail3" class="col-sm-12 control-label">Small Logo</label>
                        <?php // pr($sitesetting); die;
                           ?>
                        <?php echo $this->Form->input('small_logo', array('class' => 'form-control', 'type' => 'file', 'id' => '', 'label' => false)); ?>
                        <span style="color:red">Note:- Please select image size 100*100px</span><br>
                        <img src="<?php echo SITE_URL; ?>images/<?php echo $sitesetting['small_logo']; ?>" alt="" height="50" width="50" />
                     </div>
                     
                     <div class="col-sm-3">
                        <label for="inputEmail3" class="col-sm-12 control-label">Header Logo</label>
                        <?php echo $this->Form->input('header_logo', array('class' => 'form-control', 'type' => 'file', 'id' => '', 'label' => false)); ?>
                        <span style="color:red">Note:- Please select image size 504*136px</span><br>
                        <img src="<?php echo SITE_URL; ?>images/<?php echo $sitesetting['header_logo']; ?>" alt="" height="50" width="50" />
                     </div>
                     
                  </div>
                  <div class="row">
                     
                     
                     <div class="col-sm-3">
                        <label for="inputEmail3" class="col-sm-12 control-label">Address Line 1</label>
                        <?php echo $this->Form->input('address1', array('class' => 'form-control', 'placeholder' => 'Address', 'label' => false, 'value' => $sitesetting['address1'])); ?>
                     </div>
                     <div class="col-sm-3">
                        <label for="inputEmail3" class="col-sm-12 control-label">Address Line 2</label>
                        <?php echo $this->Form->input('address2', array('class' => 'form-control', 'placeholder' => 'Address', 'label' => false, 'value' => $sitesetting['address2'])); ?>
                     </div>
                     <div class="col-sm-3">
                        <label for="inputEmail3" class="col-sm-12 control-label">Phone</label>
                        <?php echo $this->Form->input('phone', array('class' => 'form-control', 'placeholder' => 'Phone', 'label' => false, 'value' => $sitesetting['phone'])); ?>
                     </div>
                     <div class="col-sm-3">
                        <label for="inputEmail3" class="col-sm-12 control-label">Fax</label>
                        <?php echo $this->Form->input('fax', array('class' => 'form-control', 'placeholder' => 'Fax', 'label' => false, 'value' => $sitesetting['fax'])); ?>
                     </div>
                     <div class="col-sm-3">
                        <label for="inputEmail3" class="col-sm-12 control-label">Email</label>
                        <?php echo $this->Form->input('email', array('class' => 'form-control', 'placeholder' => 'Email', 'label' => false, 'value' => $sitesetting['email'])); ?>
                     </div>
                     <div class="col-sm-3">
                        <label for="inputEmail3" class="col-sm-12 control-label">Web Site</label>
                        <?php echo $this->Form->input('website', array('class' => 'form-control', 'placeholder' => 'Web Site', 'label' => false, 'value' => $sitesetting['website'])); ?>
                     </div>
                     <div class="col-sm-3">
                        <label for="inputEmail3" class="col-sm-12 control-label">Subtitle 1</label>
                        <?php echo $this->Form->input('subtitle1', array('class' => 'form-control', 'placeholder' => 'Web Site', 'label' => false, 'value' => $sitesetting['subtitle1'])); ?>
                     </div>
                     <div class="col-sm-3">
                        <label for="inputEmail3" class="col-sm-12 control-label">Subtitle 2</label>
                        <?php echo $this->Form->input('subtitle2', array('class' => 'form-control', 'placeholder' => 'Web Site', 'label' => false, 'value' => $sitesetting['subtitle2'])); ?>
                     </div>
                  </div>
          
                  <label style="margin-top:20px;">Taxsection</label>
                  <div style="border:1px solid #999; padding:20px; padding-bottom:0px;">
                     <div class="form-group">
                        <div class="row">
                           <div class="col-sm-4">
                              <label for="inputEmail3" class="col-sm-12 control-label">Company Name</label>
                              <?php echo $this->Form->input('cname', array('class' => 'form-control', 'placeholder' => 'Company Name', 'label' => false, 'value' => $sitesetting['ac_holder'])); ?>
                           </div>
                           <div class="col-sm-4" style="margin-bottom:10px;">
                              <label for="inputEmail3" class="col-sm-12 control-label">Pan No</label>
                              <?php echo $this->Form->input('pan_no', array('class' => 'form-control', 'placeholder' => 'Pan No', 'label' => false, 'value' => $sitesetting['pan_number'])); ?>
                           </div>
                           <div class="col-sm-4" style="margin-bottom:10px;">
                              <label for="inputEmail3" class="col-sm-12 control-label">GST No</label>
                              <?php echo $this->Form->input('gst_no', array('class' => 'form-control', 'placeholder' => 'GST No', 'label' => false, 'value' => $sitesetting['gst_no'])); ?>
                           </div>
                           <div class="col-sm-4" style="margin-bottom:10px;">
                              <label for="inputEmail3" class="col-sm-12 control-label">Tin Date</label>
                              <?php echo $this->Form->input('tin_date', array('class' => 'form-control input1', 'label' => false, 'placeholder' => 'From Date', 'id' => 'datepicker1', 'autocomplete' => 'off', 'readonly', 'value' => date('Y-m-d', strtotime($sitesetting['tin_date'])))); ?>
                           </div>
                           <div class="col-sm-4" style="margin-bottom:10px;">
                              <label for="inputEmail3" class="col-sm-12 control-label">Account No</label>
                              <?php echo $this->Form->input('account_number', array('class' => 'form-control', 'placeholder' => 'Account No', 'label' => false, 'value' => $sitesetting['account_number'])); ?>
                           </div>
                           <div class="col-sm-4" style="margin-bottom:10px;">
                              <label for="inputEmail3" class="col-sm-12 control-label">IFSC Code</label>
                              <?php echo $this->Form->input('ifsc', array('class' => 'form-control', 'placeholder' => 'IFSC Code', 'label' => false, 'value' => $sitesetting['ifsc'])); ?>
                           </div>
                           <div class="col-sm-4" style="margin-bottom:10px;">
                              <label for="inputEmail3" class="col-sm-12 control-label">Address </label>
                              <?php echo $this->Form->input('address', array('class' => 'form-control', 'placeholder' => 'Address', 'label' => false, 'value' => $sitesetting['address'])); ?>
                           </div>
                           <div class="col-sm-4" style="margin-bottom:10px;">
                              <label for="inputEmail3" class="col-sm-12 control-label">Company Number </label>
                              <?php echo $this->Form->input('cmobile_no', array('class' => 'form-control', 'placeholder' => 'Company Number', 'label' => false, 'value' => $sitesetting['cmobile_no'])); ?>
                           </div>
                           <div class="col-sm-4" style="margin-bottom:10px;">
                              <label for="inputEmail3" class="col-sm-12 control-label">Alias</label>
                              <?php echo $this->Form->input('alias', array('class' => 'form-control', 'placeholder' => 'Company Number', 'label' => false, 'value' => $sitesetting['alias'])); ?>
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
   $('#checkbox1').change(function() {
     if (this.checked)
       $('.passdata').show();
     else
       $('.passdata').hide();
   
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
</script>