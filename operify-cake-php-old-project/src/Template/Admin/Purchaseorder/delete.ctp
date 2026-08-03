<div class="modal-header" style="background-color: #3c8dbc;">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Purchase Order ID-<?php echo $id;?> </h4>
        <h5 class="modal-title" style="color:white;font-weight:bold;"></h5>
      </div>
<div class="modal-body">
<?php echo $this->Form->create('Purchaseorder', array(
            'class'=>'form-horizontal',
            'enctype' => 'multipart/form-data',
            'validate'
          )); ?>

<div class="row">

<!-- right column -->
<div class="col-md-12">
<div class="form-group">
   

     <div class="col-sm-12">

<input type="checkbox" name="issue_vendor" value="Y"  label=false> &nbsp;<b>Check If Issue With Vendor</b>

</div>
</div>
</div>
<div class="col-md-12">
<div class="form-group">
</div>
</div>
<div class="col-md-12">
<div class="form-group">
      

        <div class="col-sm-12">
          <?php echo $this->Form->input('amendment_remarks', array('class' => 'form-control','required', 'id' => 'amendment_remarks', 'type' => 'textarea', 'label' => false, 'placeholder' => 'Enter Amendment Remarks', 'autofocus', 'autocomplete' => 'off','rows'=>3,'cols'=>'50')); ?>
        </div>
      </div> 
      </div>
</div>

<div class="box-footer">
            <?php echo $this->Form->submit(
                  'Delete', 
                  array('class' => 'btn btn-info pull-right', 'title' => 'Delete'));
             
              ?><?php
              echo $this->Html->link('Back', [
                'action' => 'index'

              ],['class'=>'btn btn-default']); ?>
            </div>
<?php echo $this->Form->end(); ?>
</div>
