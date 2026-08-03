<style>
  .input_fields_wrap .form-control{ margin-bottom:15px;}
</style>

<?php $batchdata=$this->Comman->findbatchcode($product['id']); ?>
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>
			Item name Manager
		</h1>
		<ol class="breadcrumb">
			<li><a href="<?php echo ADMIN_URL; ?>dashboards"><i class="fa fa-home"></i>Home</a></li>

			<?php if(isset($product['id'])){ ?>
				<li class="active"><a href="javascript:void(0)">Edit Item name </a></li>   
			<?php } else { ?>
				<li class="active"><a href="javascript:void(0)">Add Item name</a></li>
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
						<h3 class="box-title"><i class="fa fa-plus-square" aria-hidden="true"></i> <?php if(isset($itemname['0']['id'])){ echo 'Edit Item name'; }else{ echo 'Edit Item Name';} ?></h3>
					</div>
					<!-- /.box-header -->
					<!-- form start -->
					<?php echo $this->Form->create($itemnamedetail, array(
						'class'=>'form-horizontal',
						'enctype' => 'multipart/form-data',
						'validate'
					)); ?>
					<div class="box-body">

						<div class="form-group">
					<label for="inputEmail3" class="col-sm-4 control-label">Item Name</label>

					<div class="col-sm-6">
						<?php echo $this->Form->input('item_name', array('class' => 'form-control', 'type' => 'text', 'required', 'label' => false, 'placeholder' => 'Enter Item name', 'autofocus', 'autocomplete' => 'off')); ?>
					</div>
					</div>

					<div class="form-group">
					<label for="inputEmail3" class="col-sm-4 control-label">Category</label>

					<div class="col-sm-6 ">
						<?php echo $this->Form->input('main_category_id', array('class' => 'form-control','id'=>'category_ids','type'=>'select','options'=>$categary,'required','label'=>false,'empty'=>'Select Category','autofocus','autocomplete'=>'off')); ?>
					</div>
					
					</div>

					<div class="form-group">
					<label for="inputEmail3" class="col-sm-4 control-label">Sub Category </label>

					<div class="col-sm-6 ">
						<?php echo $this->Form->input('category_id', array('class' => 'form-control','id'=>'subcategory','type'=>'select','options'=>$subcategary,'required','label'=>false,'empty'=>'Select Sub Category','autofocus','autocomplete'=>'off')); ?>
					</div>
					
					</div>

					<div class="form-group">
					<label for="inputEmail3" class="col-sm-4 control-label">Location </label>

					<div class="col-sm-6 ">
						<?php echo $this->Form->input('main_location_id', array('class' => 'form-control', 'id' => 'location', 'type' => 'select', 'options' => $locations, 'required', 'label' => false, 'empty' => 'Select Location', 'autofocus', 'autocomplete' => 'off')); ?>
					</div>
					
					</div>

					<div class="form-group">
					<label for="inputEmail3" class="col-sm-4 control-label">Sub Location </label>

					<div class="col-sm-6 ">
						<?php echo $this->Form->input('location_id', array('class' => 'form-control', 'id' => 'sublocation', 'type' => 'select', 'options' => $sublocation, 'required', 'label' => false, 'empty' => 'Select Sub location', 'autofocus', 'autocomplete' => 'off')); ?>

					</div>
					
					</div>

					<div class="form-group">
					<label for="inputEmail3" class="col-sm-4 control-label">Unit </label>

					<div class="col-sm-6 ">
						<?php echo $this->Form->input('unit_id', array('class' => 'form-control', 'type' => 'select', 'options' => $units, 'required', 'label' => false, 'empty' => 'Select Unit', 'autofocus', 'autocomplete' => 'off')); ?>
					</div>
					
					</div>

					<div class="form-group">
					<label for="inputEmail3" class="col-sm-4 control-label">Sale Price </label>

					<div class="col-sm-6 ">
						<?php echo $this->Form->input('sale_price', array('class' => 'form-control','type'=>'text','required','label'=>false,'placeholder'=>'Sale Price','autofocus','autocomplete'=>'off')); ?>
					</div>
					
					</div>

					<div class="form-group">
					<label for="inputEmail3" class="col-sm-4 control-label">Cpmpany </label>

					<div class="col-sm-6 ">
						<?php echo $this->Form->input('company_id', array('class' => 'form-control','type'=>'select','options'=>$companys,'required','label'=>false,'empty'=>'Select Company','autofocus','autocomplete'=>'off')); ?>
					</div>
					
					</div>

					<div class="form-group"> 
					<label for="inputEmail3" class="col-sm-4 control-label">Tax </label> 
					<div class="col-sm-6 ">            
					<?php 
					//$tax = array();
					
					
					$taxes = explode(',',$itemnamedetail['tax'] );
					//pr($taxes); die;
					foreach ($taxs as $key => $value) { //pr($value);  ?> 
					<div class="col-sm-2">           
						<input type="checkbox" name="tax[]" value="<?php echo $value['id']; ?>" <?php if (in_array($value['id'], $taxes)){ ?> checked="checked" <?php } ?>> <?php echo $value['tax_name']." ".$value['tax']."%"; ?>
					</div>
					<?php }  ?>
					
					</div>
					</div>		
						

				</div>
					<!-- /.box-body -->
					<div class="box-footer">
						<?php
						if(isset($itemlocation['id'])){
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

	<script type="text/javascript">
$(document).ready(function() {
  $("#category_ids").on('change',function() {
    var id = $(this).val();
    $("#subcategory").find('option').remove();
    //$("#city").find('option').remove();
    if (id) {
      var dataString =id;
      $.ajax({
        type: "POST",
        url: '<?php echo SITE_URL;?>/admin/itemname/getsubcategory',
        data: {'dataString':id},
        cache: false,
        success: function(html) {
          //alert(html);
          $('<option>').val("").text("Select Sub Category").appendTo($("#subcategory"));
          $.each(html, function(key, value) {        
            $('<option>').val(key).text(value).appendTo($("#subcategory"));
          });
        }
      });
    }
  });
});
</script>

<script type="text/javascript">
$(document).ready(function() {
  $("#location").on('change',function() {
    var id = $(this).val();
    $("#sublocation").find('option').remove();
    //$("#city").find('option').remove();
    if (id) {
      var dataString =id;
      $.ajax({
        type: "POST",
        url: '<?php echo SITE_URL;?>/admin/itemname/getsublocation',
        data: {'dataString':id},
        cache: false,
        success: function(html) {
          //alert(html);
          $('<option>').val("").text("Select Sub Location").appendTo($("#sublocation"));
          $.each(html, function(key, value) {        
            $('<option>').val(key).text(value).appendTo($("#sublocation"));
          });
        }
      });
    }
  });
});
</script>

	
	