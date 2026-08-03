<!-- Content Wrapper. Contains page content -->

<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      <i class="fa fa-plus-square" aria-hidden="true"></i>
      <?php if (isset($classes['id'])) {
        echo 'Edit Staff';
      } else {
        echo 'Add Staff';
      } ?>
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo ADMIN_URL; ?>dashboards"><i class="fa fa-home"></i>Home</a></li>
      <li><a href="<?php echo ADMIN_URL; ?>employees">Manage Staff</a></li>
      <li class="active">Create New Staff</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <?php

      echo $this->Flash->render(); ?>
      <!-- right column -->
      <div class="col-md-12">
        <!-- Horizontal Form -->
        <div class="box box-info">
          <!-- /.box-header -->
          <!-- form start -->
          <?php $classes['emp_status'] = strtolower($classes['emp_status']);
          echo $this->Form->create($classes, array(
            'class' => 'form-horizontal',
            'id' => 'student_form',
            'enctype' => 'multipart/form-data',
            'validate',

          )); ?>


          <div class="box-body">
            <div class="form-group">
              <div class="row">
                <!-- Database Name -->
                <input type="hidden" id="db" name="db" value="<?php echo ($db); ?>">

                <div class="col-sm-4">
                  <label for="inputEmail3" class="control-label">Departments<span style="color:red;">*</span></label>
                  <?php echo $this->Form->input('p_department', array('class' => 'form-control', 'empty' => 'Select Department', 'type' => 'select', 'options' => $depa, 'id' => 'dep', 'label' => false, 'required')); ?>
                </div>

                <div class="col-sm-4">
                  <label for="inputEmail3" class="control-label">Designation<span style="color:red;">*</span></label>
                  <?php echo $this->Form->input('p_designation', array('class' => 'form-control', 'id' => 'dert', 'empty' => 'Select Designation', 'type' => 'select', 'options' => $desi, 'label' => false, 'required')); ?>
                </div>
                <div class="col-sm-4">
                  <label for="inputEmail3" class="control-label">Employee Status</label>
                  <?php echo $this->Form->input('emp_status', array('class' => 'form-control', 'id' => 'status', 'empty' => array('0' => 'Select Employee Status'), 'type' => 'select', 'options' => array('confirm' => 'Confirmed', 'probation' => 'Probation', 'contract' => 'Contract'), 'label' => false, 'required')); ?>
                </div>
              </div>
            </div>

            <div class="form-group">
              <div class="row">
                <div class="col-sm-4">

                  <label for="inputEmail3" class="control-label">Title<span style="color:red;">*</span></label>
                  <?php $options = array('mr' => "Mr.", 'miss' => "Miss", 'mrs' => "Mrs", 'dr' => "Dr.") ?>
                  <?php echo $this->Form->input('title', array('class' => 'form-control', 'empty' => 'Please Select', 'type' => 'select', 'options' => $options, 'id' => 'title', 'label' => false)); ?>
                </div>

                <div class="col-sm-4">
                  <label for="inputEmail3" class="control-label">First Name<span style="color:red;">*</span></label>
                  <?php echo $this->Form->input('fname', array('class' => 'form-control', 'placeholder' => 'First Name', 'required' => 'required', 'id' => 'title', 'label' => false)); ?>
                </div>

                <div class="col-sm-4">
                  <label for="inputEmail3" class="control-label">Middle Name</label>
                  <?php echo $this->Form->input('middlename', array('class' => 'form-control', 'placeholder' => 'Middle Name', 'id' => 'title', 'label' => false)); ?>
                </div>


                <div class="col-sm-4">
                  <label for="inputEmail3" class="control-label">Last Name<span style="color:red;">*</span></label>
                  <?php echo $this->Form->input('lname', array('class' => 'form-control', 'required' => 'required', 'placeholder' => 'Last Name', 'id' => 'title', 'label' => false)); ?>
                </div>
                <div class="col-sm-4">
                  <label for="inputEmail3" class="control-label">Blood Group</label>
                  <?php $options = array('a+' => "A+", 'b+' => "B+", 'ab+' => "AB+", 'o+' => "O+", 'a-' => "A-", 'b-' => "B-", 'ab-' => "AB-", 'o-' => "O-") ?>
                  <?php echo $this->Form->input('blood_group', array('class' => 'form-control', 'empty' => 'Please Select', 'type' => 'select', 'options' => $options, 'id' => 'title', 'label' => false)); ?>
                </div>
                <div class="col-sm-4">
                  <label for="inputEmail3" class="control-label">Salary</label>
                  <?php echo $this->Form->input('basic_salary', array('class' => 'form-control', 'placeholder' => 'Salary ', 'label' => false, 'type' => 'number')); ?>
                </div>
              </div>
            </div>

            <div class="form-group">
              <div class="row">
                <div class="col-sm-4">
                  <div><label for="inputEmail3" class="control-label">Gender</label></div>
                  <label class="radio-inline">
                    <input type="radio" name="gender" id="inlineRadio1" required checked value="Male"> Male
                  </label>
                  <label for="inputEmail3" class="radio-inline">
                    <input type="radio" name="gender" id="inlineRadio2" required value="Female"> Female
                  </label>
                </div>

                <div class="col-sm-4">
                  <label for="inputEmail3" class="control-label">Email/Login Id</label>
                  <?php echo $this->Form->input('email', array('class' => 'form-control', 'placeholder' => 'Email', 'label' => false)); ?>
                  <span id="dividhere" style="display:none;color:red;">Email Already Exist</span>
                </div>


                <div class="col-sm-4">
                  <label for="inputEmail3" class="control-label">Mobile No<span style="color:red;">*</span></label>


                  <?php echo $this->Form->input('mobile', array('class' => 'form-control', 'required' => 'required', 'minlength' => '10', 'maxlength' => '10', 'onkeypress' => 'return isNumber();', 'placeholder' => 'Mobile No.', 'id' => 'mobile', 'label' => false)); ?>
                  <!-- /.input group -->
                  <span style="color:red;display:none;" id="mobile_exits">Mobile Already Exits </span>
                  <div id="erp" style="display:none;color:red;">Enter Only Number</div>

                </div>
              </div>
            </div>

            <div class="form-group">
              <div class="row">
                <div class="col-sm-4">
                  <label for="inputEmail3" class="control-label">Date of Birth<span style="color:red;">*</span></label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text rounded-0 p-2 bg-transparent"><i class="fa fa-calendar"></i></div>
                    </div>
                    <input type="date" class="form-control" id="inlineFormInputGroupUsername" placeholder="Username">

                  </div>
                  <span style="color:red; display:none;" class="display_errors">New Employee D.O.B. must have 18
                    years old.</span>
                </div>

                <div class="col-sm-4">
                  <label for="inputEmail3" class="control-label">Maritial Status<span style="color:red;">*</span></label>
                  <?php $options = array('married' => "Married", 'unmarried' => "Unmarried") ?>
                  <?php echo $this->Form->input('martial_status', array('class' => 'form-control', 'empty' => 'Maritial Status', 'type' => 'select', 'options' => $options, 'id' => 'title', 'label' => false)); ?>
                </div>

                <div class="col-sm-4">
                  <label for="inputEmail3" class="control-label">Nationality</label>
                  <select class="form-control " name="nationality" style="width: 100%;">

                    <option value="INDIAN" <?php if ($classes['nationality'] == 'INDIAN') { ?> selected="selected" <?php } ?>>INDIAN</option>

                    <option value="AMERICAN" <?php if ($classes['nationality'] == 'OTHERS') { ?> selected="selected" <?php } ?>>OTHERS</option>


                  </select>
                </div>
              </div>
            </div>

            <div class="form-group">
              <div class="row">
                <div class="col-sm-4">
                  <label for="inputEmail3" class="control-label">Father/Husband Name<span style="color:red;">*</span></label>
                  <?php echo $this->Form->input('f_h_name', array('class' => 'form-control', 'placeholder' => 'Father/Husband Name', 'label' => false)); ?>
                </div>
                <div class="col-sm-4">
                  <label for="inputEmail3" class="control-label">Joining Date<span style="color:red;">*</span></label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text rounded-0 p-2 bg-transparent"><i class="fa fa-calendar"></i></div>
                    </div>
                    <input type="date" class="form-control" id="inlineFormInputGroupUsername" placeholder="Username">

                  </div>




                  <!-- <div class="input-group">
                    <div class="input-group-addon border border-dark">
                      <i class="fa fa-calendar"></i>
                    </div>
           

                  </div> -->
                </div>

                <script>
                  function fileValidation() {
                    var fileInput =
                      document.getElementById('file');
                    var filePath = fileInput.value;
                    // Allowing file type
                    var allowedExtensions =
                      /(\.jpg|\.jpeg|\.png|\.gif)$/i;

                    if (!allowedExtensions.exec(filePath)) {
                      $('#image_ext').show('');
                      fileInput.value = '';
                    } else {

                      $('#image_ext').hide('');
                    }

                  }
                </script>
                <div class="col-sm-4">
                  <label for="inputEmail3" class="control-label">Image</label>
                  <?php echo $this->Form->input('file', array('class' => 'form-control', 'type' => 'file', 'label' => false, 'id' => 'file', 'onchange' => 'return fileValidation();')); ?>
                  <span style="color:red;display:none;" id="image_ext">Invalid Image Type</span>
                </div>

                <div class="col-sm-4">
                  <label for="inputEmail3" class="control-label">Description</label>
                  <?php echo $this->Form->input('description', array('class' => 'form-control', 'type' => 'textarea', 'label' => false)); ?>
                </div>
              </div>
            </div>









            <hr>
            <div class="form-group">
              <div class="row">
                <div class="col-sm-4">
                  <label for="inputEmail3" class="control-label">Employee code</label>
                  <?php echo $this->Form->input('emp_code', array('class' => 'form-control', 'placeholder' => 'Employee code', 'label' => false)); ?>
                </div>

                <div class="col-sm-4">
                  <label for="inputEmail3" class="control-label">Alternate number</label>
                  <?php echo $this->Form->input('alternate_mobile', array('class' => 'form-control', 'placeholder' => 'Alternate number', 'maxlength' => '10', 'onkeypress' => 'return isNumber();', 'label' => false)); ?>
                </div>

                <div class="col-sm-4">
                  <label for="inputEmail3" class="control-label">Qualification</label>
                  <?php echo $this->Form->input('qualification', array('class' => 'form-control', 'placeholder' => 'Enter Qualification', 'label' => false)); ?>
                </div>

              </div>
            </div>

            <script>
              $(document).ready(function() {

                $('#dep').change(function() {
                  var id = $(this).val();
                  //alert(id);
                  $.ajax({
                    type: 'POST',
                    url: '<?php echo ADMIN_URL; ?>Employees/finddesignation',
                    data: {
                      'id': id
                    },
                    success: function(data) {
                      $('#dert').html(data);
                    },

                  });

                });

              });
            </script>

            <!-- /.box-body -->
            <div class="box-footer">


              <?php
              if (isset($classes['id'])) {
                echo $this->Form->submit(
                  'Update',
                  array('class' => 'btn btn-info pull-right', 'title' => 'Update','id'=> 'formsubmitbtn')
                );
              } else {
                echo $this->Form->submit(
                  'Add',
                  array('class' => 'btn btn-info pull-right', 'title' => 'Add','id'=> 'formsubmitbtn')
                );
              }
              ?> <?php
                  echo $this->Html->link('Back', [
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

<script>
  $('#mobile').on('change', function() {
    var mobile = $('#mobile').val();
    // alert(mobile)
    $.ajax({
      type: 'POST',
      url: '<?php echo ADMIN_URL; ?>employees/dup_mobile',
      data: {
        'mobile': mobile
      },
      success: function(data) {
        if (data > 0) {
          $('#mobile').val('');
          $('#mobile_exits').show('');
        } else {
          $('#mobile_exits').hide('');
        }

      },

    });
  });


  $(document).ready(function() {
    $('#email').on('change', function() {
      var username = $('#email').val();
      //alert(username);
      $.ajax({
        type: 'POST',
        url: '<?php echo ADMIN_URL; ?>employees/find_email',
        data: {
          'username': username
        },
        success: function(data) { //alert(data);

          if (data > 0) {
            $('#email').val('');
            alert("Enter A Valid Email Address");

          } else {
            //$('#dividhere').hide();
          }
        },

      });
    });


  });


  function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode != 46 && charCode != 45 && charCode > 31 && (charCode < 48 || charCode > 57)) {
      $('#erp').show();
      return false;
    }
    $('#erp').hide();
    return true;
  }

  function validateEmail(emailField) {
    var reg =
      /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

    if (reg.test(emailField.email.value) == false) {
      alert("Enter Valid Email");
      return false;
    }

    return true;

  }
</script>
<script>
    $(document).ready(function () {

        $('#student_form').on('submit', function (e) {
            $("#formsubmitbtn").css("display", "none");
        });
        });
</script>