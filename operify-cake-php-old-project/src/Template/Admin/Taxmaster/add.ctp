<style>
  .input_fields_wrap .form-control{ margin-bottom:15px;}
</style>

<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Tax Manager
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo SITE_URL; ?>admin/taxmaster"><i class="fa fa-home"></i>Home</a></li>
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
            <h3 class="box-title"><i class="fa fa-plus-square" aria-hidden="true"></i> <?php if(isset($company['id'])){ echo 'Edit Post New'; }else{ echo 'Create New Tax';} ?></h3>
          </div>
          <!-- /.box-header -->
          <!-- form start -->
          <?php echo $this->Form->create($category, array(
            'class'=>'form-horizontal',
            'enctype' => 'multipart/form-data',
            'id' => 'sevice_form',
            'validate'
          )); ?>
          <div class="box-body">

          <!-- <div class="form-group">
              <label for="inputEmail3" class="col-sm-4 control-label">Parent Tax</label>

              <div class="col-sm-6">
                <?php //echo $this->Form->input('parent', array('class' => 'form-control', 'type' => 'select', 'options'=>$tax,'required', 'label' => false, 'empty' => 'Select Parent Tax', 'autofocus', 'autocomplete' => 'off')); ?>
              </div>
            </div> -->

            <!-- <div class="form-group">
              <label for="inputEmail3" class="col-sm-4 control-label">Tax Type Name</label>

              <div class="col-sm-6">
                <?php //echo $this->Form->input('tax_name', array('class' => 'form-control', 'type' => 'text', 'required', 'label' => false, 'placeholder' => 'Enter tax type name', 'autofocus', 'autocomplete' => 'off')); ?>
              </div>
            </div> -->

            <div class="form-group">
              <div class="col-sm-4 ">
                <label for="inputEmail3" class="control-label">Tax<strong style='color:red;'>*</strong></label>
                <?php echo $this->Form->input('tax', array('class' => 'form-control','type'=>'number','required','label'=>false,'placeholder'=>'Enter Tax ','autofocus','autocomplete'=>'off')); ?>
              </div>

              <div class="col-sm-4 ">
                <label for="inputEmail3" class="control-label">Description<strong style='color:red;'>*</strong></label>
                <?php echo $this->Form->input('description', array('class' => 'form-control','type'=>'text','required','label'=>false,'placeholder'=>'Enter Description','autofocus','autocomplete'=>'off')); ?>
              </div>
            </div>
          </div>
          <!-- /.box-body -->
          <div class="box-footer">
            <?php
            if(isset($category['id'])){
              echo $this->Form->submit(
                'Update', 
                array('class' => 'btn btn-info pull-right','id'=> 'formsubmitbtn', 'title' => 'Update')
              ); }else{ 
                echo $this->Form->submit(
                  'Add', 
                  array('class' => 'btn btn-info pull-right','id'=> 'formsubmitbtn', 'title' => 'Add')
                );
              }
              ?><?php
              echo $this->Html->link('Back', [
                'action' => 'index'
              ],['class'=>'btn btn-default']); ?>
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
    $(document).ready(function () {
        $('#sevice_form').on('submit', function (e) {
            $("#formsubmitbtn").css("display", "none");
        });
        });
</script>

            




  

