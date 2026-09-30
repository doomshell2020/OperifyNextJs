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
      Add Item Master
      <?php
      // pr($item);die;
      ?>
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo SITE_URL; ?>admin/additem"><i class="fa fa-home"></i>Home</a></li>
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
                <label for="inputEmail3" class=" control-label" style="text-align: left !important;">Item Name <strong
                    style="color:red;">*</strong></label>
                <?php echo $this->Form->input('item_name', array('class' => 'form-control', 'type' => 'text', 'required', 'label' => false, 'placeholder' => 'Enter Item name', 'autofocus', 'autocomplete' => 'off',)); ?>
                <span id="itemNameError" style="color:red; font-size:12px;"></span>
              </div>
              <div class="col-md-4">
                <label for="inputEmail3" class=" control-label" style="text-align: left !important;">Category<strong
                    style="color:red;"></strong></label>
                <?php echo $this->Form->input('category_id', array('class' => 'form-control', 'id' => 'category_ids', 'type' => 'select', 'options' => $categary, 'empty' => '---- Select Category ----', '', 'label' => false, 'autofocus', 'autocomplete' => 'off')); ?>
              </div>

              <div class="col-md-4">
                <label for="inputEmail3" class=" control-label" style="text-align: left !important;"> HSN No./Item
                  Code<strong style="color:red;"></strong></label>
                <?php echo $this->Form->input('item_isbn', array('class' => 'form-control', 'type' => 'number', '', 'label' => false, 'placeholder' => 'Enter Item HSN Number', 'autofocus', 'autocomplete' => 'off', 'id' => 'item-isbn')); ?>
              </div>
              <div class="col-md-4">
                <label for="inputEmail3" class=" control-label" style="text-align: left !important;">Sale Price</label>
                <?php echo $this->Form->input('sale_price', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'placeholder' => 'Enter Sale Price', 'autofocus', 'autocomplete' => 'off')); ?>
              </div>
              <div class="col-md-4">
                <label for="inputEmail3" class=" control-label" style="text-align: left !important">Discount
                  Amount</label>
                <?php echo $this->Form->input('discount', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'placeholder' => 'Enter Discount Amount', 'autofocus', 'autocomplete' => 'off')); ?>
              </div>
              <input type="hidden" name="cname" value="<?php echo $company[1]; ?>">

              <div class="col-md-4">
                <label for="inputEmail3" class=" control-label" style="text-align: left !important">UOM<strong
                    style="color:red;">*</strong></label>
                <?php echo $this->Form->input('uom', array('class' => 'form-control', 'type' => 'select', 'options' => $units, 'required', 'placeholder' => 'Enter UOM', 'label' => false, 'autofocus', 'empty' => '---- Select UOM ----', 'autocomplete' => 'off')); ?>
              </div>
              <div class="col-md-4">
                <label for="inputEmail3" class=" control-label" style="text-align: left !important;">Weight</label>
                <?php echo $this->Form->input('weight', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'autofocus', 'placeholder' => 'Enter Weight', 'autocomplete' => 'off')); ?>
              </div>
              <div class="col-md-4">
                <label for="inputEmail3" class=" control-label" style="text-align: left !important;">Volume</label>
                <?php echo $this->Form->input('volume', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'autofocus', 'placeholder' => 'Enter Volume', 'autocomplete' => 'off')); ?>
              </div>

              <div class="col-md-4">
                <label for="inputEmail3" class=" control-label" style="text-align: left !important;">Min. Order
                  Qty</label>
                <?php echo $this->Form->input('min_order_qty', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'autofocus', 'placeholder' => 'Enter Min. Order Qty', 'autocomplete' => 'off')); ?>
              </div>


              <!-- <div class="col-md-4">
                <label for="inputEmail3" class=" control-label" style="text-align: left !important">Tax<strong
                    style="color:red;">*</strong></label>
                <?php //echo $this->Form->input('tax', array('class' => 'form-control tax_id', 'type' => 'select', 'options' => $tax, 'required', 'label' => false, 'empty' => '---- Select Tax ----', 'autofocus', 'autocomplete' => 'off')); ?>
              </div> -->

              <div class="col-sm-4" style="margin-bottom:15px;">
                <label for="inputEmail3" class="control-label">Type :</label>
                <label class="radio-inline">
                  <input type="radio" name="itemtype" class="mode radio-inline checkstr " id="rawpro"
                    value="RawMaterial">&nbsp;RawMaterial
                </label>

                <label class="radio-inline">
                  <input type="radio" name="itemtype" id="finishedpro" class="mode radio-inline checkstr"
                    value="FinishedProduct">&nbsp;
                  FinishedProduct
                </label>
              </div>

              <div class="col-sm-4" id="proceesname" style="margin-bottom:15px;display:none;">
                <label for="inputEmail3" class="control-label">Process Name :</label>
                <?php
                foreach ($finishedprocess as $key => $value) { ?>
                  <input type="radio" name="finishedprocess_id" class="mode radio-inline checkstr" id="processname"
                    value="<?php echo $key ?>"> &nbsp;
                  <input type="checkbox" name="productprocess_id[]" value=" <?php echo $key ?>">
                  <?php echo $value ?> &nbsp;<br>
                <?php } ?>
              </div>
              <div class="col-md-12 text-right mt-2">
                <?php
                if (isset($location['id'])) {
                  echo $this->Form->submit(
                    'Update',
                    array('class' => 'btn btn-info', 'id' => 'formsubmitbtn', 'title' => 'Update')
                  );
                } else {
                  echo $this->Form->submit(
                    'Add',
                    array('class' => 'btn btn-info', 'id' => 'formsubmitbtn', 'title' => 'Add')
                  );
                }
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
  $(document).ready(function() {
    $('.tax_id').val(6);

    $('#sevice_form').on('submit', function(e) {
      $("#formsubmitbtn").css("display", "none");
    });
  });
</script>

<script>
  $(function() {
    var $radios = $('input:radio[name=itemtype]');
    if ($radios.is(':checked') === false) {
      $radios.filter('[value=RawMaterial]').prop('checked', true);
    }
  });
</script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const fields = ['min_order_qty', 'sale_price', 'discount'];

    fields.forEach(function(fieldName) {
      const input = document.querySelector(`[name="${fieldName}"]`);
      if (input) {
        input.addEventListener('input', function() {
          this.value = this.value.replace(/[^0-9.]/g, '');
          if (parseFloat(this.value) < 1) {
            this.value = '';
          }
        });
      }
    });
  });
</script>

<script>
  $('#finishedpro').on('change', function() {
    $("#proceesname").css("display", "block");
  });
</script>
<script>
  $('#rawpro').on('change', function() {
    $("#proceesname").css("display", "none");
  });
</script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const itemNameInput = form.querySelector('[name="item_name"]');
    const errorSpan = document.getElementById('itemNameError');

    form.addEventListener('submit', function(e) {
      const trimmedValue = itemNameInput.value.trim();
      if (trimmedValue === '') {
        e.preventDefault();
        errorSpan.textContent = 'Item Name cannot be empty or just spaces.';
        itemNameInput.value = '';
        itemNameInput.focus();
      } else {
        errorSpan.textContent = '';
      }
    });

    // Clear error while typing
    itemNameInput.addEventListener('input', function() {
      if (itemNameInput.value.trim() !== '') {
        errorSpan.textContent = '';
      }
    });
  });
</script>