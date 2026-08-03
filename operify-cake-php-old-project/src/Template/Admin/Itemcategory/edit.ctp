<style>
   .input_fields_wrap .form-control{ margin-bottom:15px;}
</style>
<?php $batchdata=$this->Comman->findbatchcode($product['id']); ?>
<div class="content-wrapper">
   <!-- Content Header (Page header) -->
   <section class="content-header">
      <h1>
         Item Category Manager
      </h1>
      <ol class="breadcrumb">
         <li><a href="<?php echo ADMIN_URL; ?>dashboards"><i class="fa fa-home"></i>Home</a></li>
         <?php if(isset($product['id'])){ ?>
         <li class="active"><a href="javascript:void(0)">Edit Item Category </a></li>
         <?php } else { ?>
         <li class="active"><a href="javascript:void(0)">Add Item Category</a></li>
         <?php } ?>
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
                  <h3 class="box-title"><i class="fa fa-plus-square" aria-hidden="true"></i> <?php if(isset($itemcategory['id'])){ echo 'Edit Item Category'; }else{ echo 'Create New Product';} ?></h3>
               </div>
               <!-- /.box-header -->
               <!-- form start -->
               <?php echo $this->Form->create($itemcategory, array(
                  'class'=>'form-horizontal',
                  'enctype' => 'multipart/form-data',
                  'id' => 'sevice_form',
                  'validate'
                  )); ?>
               <div class="box-body">
                  <div class="form-group row">
                     <div class="col-sm-4">
                        <label for="inputEmail3" class="control-label">Item Category Name<strong style='color:red;'>*</strong></label>
                        <?php echo $this->Form->input('category_name', array('class' => 'form-control', 'type' => 'text', 'required', 'label' => false, 'placeholder' => 'Enter category name', 'autofocus', 'autocomplete' => 'off')); ?>
                     </div>
                     <div class="col-sm-4">
                        <label for="inputEmail3" class="control-label">Description<strong style='color:red;'>*</strong></label>
                        <?php echo $this->Form->input('description', array('class' => 'form-control','type'=>'text','required','label'=>false,'placeholder'=>'description','autofocus','autocomplete'=>'off')); ?>
                     </div>
                  </div>
               </div>
               <!-- /.box-body -->
               <div class="box-footer">
                  <?php
                     if(isset($itemcategory['id'])){
                     	echo $this->Form->submit(
                     		'Edit', 
                     		array('class' => 'btn btn-info pull-right','id'=> 'formsubmitbtn', 'title' => 'Edit')
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
<script type="text/javascript">
   $(document).ready(function() {
   var max_fields      = 10; //maximum input boxes allowed
   var wrapper         = $(".input_fields_wrap"); //Fields wrapper
   var add_button      = $(".add_field_button"); //Add button ID
   
   var x = 1; //initlal text box count
   $(add_button).click(function(e){ //on add input button click
     e.preventDefault();
       if(x < max_fields){ //max input box allowed
           x++; //text box increment
           $(wrapper).append('<div class="form-group input_fields_wrap"><div class="col-sm-2"> <?php echo $this->Form->input('product_id[]', array('class' => 'form-control', 'type' => 'select', 'required', 'label' => false, 'autofocus', 'empty' => 'Select Product', 'options' => $product, 'autocomplete' => 'off')); ?></div><div class="col-sm-2"> <?php echo $this->Form->input('attribute_id[]', array('class' => 'form-control', 'type' => 'select', 'required', 'label' => false, 'autofocus', 'empty' => 'Select Attribute', 'options' => $attributes, 'autocomplete' => 'off'));?></div><div class="col-sm-2"> <?php echo $this->Form->input('value[]', array('class' => 'form-control', 'type' => 'text', 'required', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'placeholder' => 'Enter Value Here ...'));?></div><div class="col-sm-2"> <?php echo $this->Form->input('description[]', array('class' => 'form-control', 'type' => 'text', 'placeholder' => 'Enter Description Here ...', 'required', 'label' => false, 'autofocus', 'autocomplete' => 'off'));?></div><div class="col-sm-2"> <?php echo $this->Form->input('qauntity[]', array('class' => 'form-control', 'type' => 'text', 'placeholder' => 'Enter Quantity Here ...', 'required', 'label' => false, 'autofocus', 'autocomplete' => 'off'));?></div><div class="col-sm-1"> <?php echo $this->Form->input('unit_price[]', array('class' => 'form-control', 'type' => 'text', 'placeholder' => 'Unit Price', 'required', 'label' => false, 'autofocus', 'autocomplete' => 'off'));?></div><a href="#" class="remove_field"><i style="font-size: 26px; padding-top: 4px; color: #dc2020;" class="fa fa-minus-circle"></i></a></div>'); //add input box
       }
   });
   $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
     e.preventDefault(); $(this).parent('div').remove(); x--;
   })
   });
</script>
<script>
   $(document).ready(function() {
   var edit = '<?php echo date('Y-m-d', strtotime($purchase['invoice_date'])); ?>';
   $("#datepicker1").val(edit);
   var edit1 = '<?php echo date('Y-m-d', strtotime($purchase['goods_received_date'])); ?>';
    	$("#datepicker2").val(edit1);
   $('#datepicker1').datepicker({
   dateFormat: 'yy-mm-dd',
      onSelect: function(date) {
   var selectedDate = new Date(date);
         var endDate = new Date(selectedDate);
         endDate.setDate(endDate.getDate());
   
         $("#datepicker2").datepicker("option", "minDate");
         //$("#datepicker2").val(date);
       }
     });
     $('#datepicker1').datepicker('setDate', edit);
     $('#datepicker2').datepicker({
   dateFormat: 'yy-mm-dd',
   });
   
     $('#datepicker2').datepicker('setDate', edit1);
   });
</script>

<script>
    $(document).ready(function () {
        $('#sevice_form').on('submit', function (e) {
            $("#formsubmitbtn").css("display", "none");
        });
        });
</script>