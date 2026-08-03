<style>
  .form-control.category_qty {
    margin-bottom:15px;
  }
</style>
<?php echo $this->Form->create($item, array(
   'class'=>'form-horizontal',
   'enctype' => 'multipart/form-data',
   'validate'
   )); ?>
    <label for="inputEmail3"
        style="text-align: left !important; margin-bottom:10px;">Remark</label>
    <?php echo $this->Form->input('remark', array('class' => 'form-control category_qty',  'type' => 'text','label' => false,  'autofocus', 'autocomplete' => 'off')); ?>
  </div>

  <?php
    if(isset($item['id'])){
      echo $this->Form->submit(
        'Update', 
        array('class' => 'btn btn-info', 'title' => 'Update')
      ); }else{ 
        echo $this->Form->submit(
          'Submit', 
          array('class' => 'btn btn-info', 'title' => 'Add')
        );
      }
  ?>
  </div>
<?php echo $this->Form->end(); ?>