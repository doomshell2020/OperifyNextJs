<style>
  .input_fields_wrap .form-control {
    margin-bottom: 15px;
  }

  .control-label {
    display: block;
    margin-bottom: 10px;
  }

  label[for="consumble-y"] {
    width: 47%;
    padding: 4px 8px;
    border: 1px solid #ccc;
    margin-right: 6%;
    border-radius: 3px;
  }

  label[for="consumble-n"] {
    width: 47%;
    padding: 4px 8px;
    border: 1px solid #ccc;
    border-radius: 3px;
  }
</style>
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Jobseeker
      <?php
      // pr($item);die;
      ?>
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo SITE_URL; ?>admin/add"><i class="fa fa-home"></i>Home</a></li>
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
            <h3 class="box-title"><i class="fa fa-plus-square" aria-hidden="true"></i>
              <?php if (isset($location['id'])) {
                echo 'Edit Post New';
              } else {
                echo 'Create New Item';
              } ?>
            </h3>
          </div>
          <!-- /.box-header -->
          <!-- form start -->
          <?php echo $this->Form->create(
            $items,
            array(
              'class' => 'form-horizontal',
              'enctype' => 'multipart/form-data',
              'id' => 'sevice_form',
              'validate'
            )
          ); ?>

          <div class="box-body">
            <div class="row">
              <div class="col-md-4">
                <label for="inputEmail3" class=" control-label" style="text-align: left !important;"> Name <strong
                    style="color:red;"></strong></label>
                <?php echo $this->Form->input('name', array('class' => 'form-control', 'type' => 'text', 'required', 'label' => false, 'placeholder' => 'Enter Name', 'autofocus', 'autocomplete' => 'off')); ?>
              </div>

              <div class="col-md-4">
                <label for="inputEmail3" class=" control-label" style="text-align: left !important ;"> Mobile <strong
                    style="color:red;"></strong></label>
                <?php echo $this->Form->input('mobile', array('class' => 'form-control',  'required', 'label' => false, 
                'placeholder' => 'Enter Mobile ' ,'maxlength' => '10', 'autofocus', 'autocomplete' => 'off')); ?>
              </div>

                      
            <div class="col-sm-4">
            <label for="inputEmail3" class="control-label">Country</label>
            <?php $options = ['India' => 'India', 'Nepal' => 'Nepal', 'Pakisthan' => 'Pakisthan', 'Srilanka' => 'Srilanka','loahor'=> 'lahore'];
           echo $this->Form->select('country', $options, ['class' => 'form-control', 'label' => false,'autofocus', 'autocomplete' => 'off','empty'=>'---Select---']);  ?>
             </div>
           

              <div class="col-md-4">
                <label for="inputEmail3" class=" control-label" style="text-align: left !important;"> Address</label>
                <?php echo $this->Form->input('address', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'placeholder' => 'Enter Address', 'autofocus', 'autocomplete' => 'off')); ?>
              </div>



              <div class="col-md-4">
                <label for="inputEmail3" class=" control-label" style="text-align: left !important"> Gender</label>
                <input type="radio" id="" name="gender" value="m">
                <label for="Male">Male</label>
                <input type="radio" id="" name="gender" value="f">
                 <label for="Female">Female</label>
                
                
              </div>
              
              <div class="col-md-4">
                <label for="inputEmail3" class=" control-label" style="text-align: left !important;"> Desprition <strong
                    style="color:red;"></strong></label>
                <?php echo $this->Form->input('desprition', array('class' => 'form-control', 'type' => 'text', 'required', 'label' => false, 'placeholder' => 'Enter Desprition', 'autofocus', 'autocomplete' => 'off')); ?>
              </div>

           
           
              <div class="col-md-4">
                <label for="inputEmail3" class=" control-label" style="text-align: left !important;"> Skills <strong
                    style="color:red;"></strong></label>
                    <input type="checkbox" id="" name="skills" value="java">
                    <label for="vehicle1"> Java</label>
                   <input type="checkbox" id="" name="skills" value="pyton">
                   <label for="vehicle2"> Pyton</label>
                   <input type="checkbox" id="" name="skills" value="c++">
                   <label for="vehicle3"> C++ </label>
              </div>

             
          
              <div class="col-md-12 text-right mt-2">
                <?php
               
                  echo $this->Form->submit(
                    'Add',
                    array('class' => 'btn btn-info', 'id' => 'formsubmitbtn', 'title' => 'Add')
                  );
                ?>
              </div>
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
<!-- Relation Beetween Location and Sublocation  -->

<!-- end  -->

<script>
function validateForm() {
    var valid = true; // Declare and initialize 'valid' as true

    // Reset any existing error messages
    var errorElements = document.getElementsByClassName("error");
    for (var i = 0; i < errorElements.length; i++) {
        errorElements[i].innerHTML = "";
    }

    // Name validation
    var name = document.forms["myForm"]["name"].value;
    if (name === "") {
        document.getElementById("nameError").innerHTML = "Name must be filled out";
        valid = false;
    } 
    
     //mobile validation
     var mobile = document.forms["myForm"]["mobile"].value;
if (mobile === "") {
    document.getElementById("mobileError").innerHTML = "Mobile number must be filled out";
    valid = false;
} else if (!/^\d{10}$/.test(mobile)) {
    document.getElementById("mobileError").innerHTML = "Invalid mobile number format";
    valid = false;
}

     // country validation
     var country = document.forms["myForm"]["country"].value;
    if (country === "") {
        document.getElementById("countryError").innerHTML = "Country must be filled out";
        valid = false;
    }
    
     // address validation
     var address = document.forms["myForm"]["address"].value;
    if (address === "") {
        document.getElementById("addressError").innerHTML = "Address must be filled out";
        valid = false;
    }
      // description validation

      var gender = document.forms["myForm"]["gender"].value;
    if (gender === "") {
        document.getElementById("genderError").innerHTML = "Gender must be filled out";
        valid = false;
    }

      var description = document.forms["myForm"]["description"].value;
    if (description === "") {
        document.getElementById("descriptionError").innerHTML = "Description must be filled out";
        valid = false;

    } 

     //skillsname validation

     var skillsInputs = document.querySelectorAll('input[name="skills[]"]');
    var skillSelected = false;

    for (var i = 0; i < skillsInputs.length; i++) {
        if (skillsInputs[i].checked) {
            skillSelected = true;
            break; // At least one skill is selected, no need to check further
        }
    }
    if (!skillSelected) {
        document.getElementById("skillsError").innerHTML = "Skills must be selected";
        return false; // Prevent form submission
    }


    if (!valid) {
        return false;
    }
    return valid;
}
    </script>  

<script>
  $(document).ready(function () {
    $('.tax_id').val(6);

    $('#sevice_form').on('submit', function (e) {
      $("#formsubmitbtn").css("display", "none");
    });
  });
</script>



