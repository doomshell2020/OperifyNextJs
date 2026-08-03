<style>
  .input_fields_wrap .form-control{ margin-bottom:15px;}
</style>

<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Item Category Manager
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo SITE_URL; ?>admin/itemcategory"><i class="fa fa-home"></i>Home</a></li>

      
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
            <h3 class="box-title"><i class="fa fa-plus-square" aria-hidden="true"></i> <?php if(isset($category['id'])){ echo 'Edit Post New'; }else{ echo 'Create New Item Category';} ?></h3>
          </div>
          <!-- /.box-header -->
          <!-- form start -->
          <?php echo $this->Form->create($itemcategory, array(
            'class'=>'form-horizontal',
            'enctype' => 'multipart/form-data',
            'validate'
          )); ?>
          <div class="box-body">

          <div class="form-group">
              <label for="inputEmail3" class="col-sm-4 control-label">Select Parent Category</label>

              <div class="col-sm-6">
                <?php echo $this->Form->input('parent', array('class' => 'form-control', 'type' => 'select', 'required', 'label' => false, 'options'=>$categary, 'empty'=>'select category')); ?>
              </div>
            </div>

            <div class="form-group">
              <label for="inputEmail3" class="col-sm-4 control-label">Item Category Name</label>

              <div class="col-sm-6">
                <?php echo $this->Form->input('category_name', array('class' => 'form-control', 'type' => 'text', 'required', 'label' => false, 'placeholder' => 'Enter category name', 'autofocus', 'autocomplete' => 'off')); ?>
              </div>
            </div>

            <div class="form-group">
              <label for="inputEmail3" class="col-sm-4 control-label">Description</label>

              <div class="col-sm-6 ">
                <?php echo $this->Form->input('description', array('class' => 'form-control','type'=>'text','required','label'=>false,'placeholder'=>'description','autofocus','autocomplete'=>'off')); ?>
              </div>
            
            </div>
            
            
          </div>
          <!-- /.box-body -->
          <div class="box-footer">
            <?php
            if(isset($category['id'])){
              echo $this->Form->submit(
                'Update', 
                array('class' => 'btn btn-info pull-right', 'title' => 'Update')
              ); }else{ 
                echo $this->Form->submit(
                  'Add', 
                  array('class' => 'btn btn-info pull-right', 'title' => 'Add')
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



            




  

