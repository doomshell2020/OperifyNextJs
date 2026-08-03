<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Edit Item Master
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo ADMIN_URL; ?>admin/dashboards"><i class="fa fa-home"></i>Home</a></li>
      <li><a href="<?php echo ADMIN_URL; ?>admin/jobseeker/index"><i class="fa fa-home"></i>Home</a></li>
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
              <?php if(isset($job['id'])){ echo 'Edit Jobseeker'; }else{ echo 'Edit Jobseeker';} ?>
            </h3>
          </div>
          <!-- /.box-header -->
          <!-- form start -->

          <?php echo $this->Form->create('', array(

            'class' => 'form-horizontal',
            'enctype' => 'multipart/form-data',
            'id' => 'sevice_form',
            'validate'
          ));
          //pr($addeditem); die;
          ?>
          <div class="box-body">
            <div class="box-body">
              <div class="row">

              <div class="col-sm-4">
              <label for="inputEmail3" class="control-label">Name</label>
              <?php echo $this->Form->input('name', array('class' => 'form-control','type'=>'text','label'=>false,'placeholder'=>'Name','autofocus','autocomplete'=>'off','value' => $job['name'])); ?><br>
              <span id="nameError" class="error" style="color: red;"></span>
            </div>


                <div class="col-md-4">
                    <label for="inputEmail3" class="control-label" style="text-align: left !important;"> Mobile <strong
                    style="color:red;"></strong></label>
                <?php echo $this->Form->input('mobile', array('class' => 'form-control', 'type' => 'text', 'required', 'label' => false, 'placeholder' => 'Mobile ', 'autofocus', 'autocomplete' => 'off','value' => $job['mobile'])); ?>

                  </div>

                  
                  
                  <div class="col-sm-4">
            <label for="inputEmail3" class="control-label">Country</label>
            <?php $options = ['India' => 'India', 'Nepal' => 'Nepal', 'Pakisthan' => 'Pakisthan', 'Srilanka' => 'Srilanka','loahor'=> 'lahore'];
           echo $this->Form->select('country', $options, ['class' => 'form-control', 'label' => false,'autofocus', 'autocomplete' => 'off','value' => $job['country'],'empty'=>'---Select---']);  ?>
             </div>

         

                <div class="col-md-4">

                  <label for="inputEmail3" class="col-sm-12 control-label" style="text-align: left !important;">Address
                    </label>

                  <div class="col-md-12">
                    <?php echo $this->Form->input('address', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'placeholder' => 'address', 'autofocus', 'autocomplete' => 'off','value' => $job['address'])); ?>
                  </div>
                </div>


                <div class="col-md-4">
                <label for="inputEmail3" name="gender" class=" control-label"  style="text-align: left !important"> Gender</label> <br>
                
                  <input type="radio" name="gender" class="mode radio-inline checkstr " value="m" id="rawpro"
                  <?php if($job['gender'] == 'm'){
                    echo "checked";
                  } ?>>&nbsp;Male
                </label>
               
                
                  <input type="radio" name="gender" class="mode radio-inline checkstr" value="f" id="finishedpro"
                  <?php if($job['gender'] == 'f'){
                    echo "checked";
                  } ?>>&nbsp; Female
                </label>

                  </div>

                
              <div class="col-md-4">
                <label for="inputEmail3" class=" control-label" style="text-align: left !important;"> Desprition <strong
                    style="color:red;"></strong></label>
                <?php echo $this->Form->input('desprition', array('class' => 'form-control', 'type' => 'text', 'required', 'label' => false, 'placeholder' => 'desprition', 'autofocus', 'autocomplete' => 'off','value' => $job['desprition'])); ?>

              </div>


                <div class="col-md-4">
                <label for="inputEmail3" class=" control-label" style="text-align: left !important;"> Skills <strong
                    style="color:red;"></strong></label>
                    <?php echo $this->Form->input('skills', array('class' => 'form-control', 'type' => 'text', 'required', 'label' => false, 'placeholder' => 'Enter skills', 'autofocus', 'autocomplete' => 'off')); ?>
              </div>
 
             
              
        
                  </div>
                  <!-- <?php pr ($job); ?> -->


              <div class="box-footer">

                <?php
               
                  echo $this->Form->submit(
                    'Update',
                    array('class' => 'btn btn-info pull-right','id'=> 'formsubmitbtn', 'title' => 'Update')
                  );
               
                ?>

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

