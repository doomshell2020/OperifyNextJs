<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Edit Item Master
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo ADMIN_URL; ?>dashboards"><i class="fa fa-home"></i>Home</a></li>
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
              <?php echo 'Edit Item Name'; ?>
            </h3>
          </div>
          <!-- /.box-header -->
          <!-- form start -->

          <?php echo $this->Form->create($addeditem, array(

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


                <div class="col-md-4">

                  <label for="inputEmail3" class="col-sm-12 control-label" style="text-align: left !important;">Item
                    Name <strong style="color:red;">*</strong></label>

                  <div class="col-md-12">
                    <?php echo $this->Form->input('item_name', array('class' => 'form-control', 'type' => 'text', 'required', 'label' => false, 'placeholder' => 'Enter Item name', 'autofocus', 'autocomplete' => 'off', 'style' => 'text-transform: uppercase')); ?>
                  </div>
                  <span id="itemNameError" style="color:red; font-size:12px;"></span>
                </div>

                <div class="col-md-4">

                  <label for="inputEmail3" class="col-md-12 control-label"
                    style="text-align: left !important;">Category<strong style="color:red;"></strong></label>

                  <div class="col-md-12">
                    <?php
                    if ($chechdesignsheet > 0) {
                      echo $this->Form->input('category_id', array('class' => 'form-control', 'id' => 'category_ids', 'type' => 'hidden', '', 'label' => false, 'autofocus', 'autocomplete' => 'off'));
                      echo $this->Form->input('category_id11', array('class' => 'form-control', 'id' => 'category_ids', 'readonly', 'empty' => '---- Select Category ----', 'value' => $categaryname['category_name'], 'type' => 'text', 'required', 'label' => false, 'autofocus', 'autocomplete' => 'off'));
                    } else {
                      echo $this->Form->input('category_id', array('class' => 'form-control', 'id' => 'category_ids', 'type' => 'select', 'empty' => '---- Select Category ----', 'options' => $categary, 'label' => false, 'autofocus', 'autocomplete' => 'off'));
                    }
                    ?>
                  </div>

                </div>



                <div class="col-md-4">

                  <label for="inputEmail3" class="col-sm-12 control-label" style="text-align: left !important;"> HSN
                    No./Item Code<strong style="color:red;"></strong></label>

                  <div class="col-md-12">
                    <!-- <input type="text" name="item_isbn" class="form-control" maxlength="15"  placeholder="ISBN Number" autofocus="autofocus" autocomplete="off" id="item-isbn"> </div> -->
                    <?php echo $this->Form->input('item_isbn', array('class' => 'form-control', 'type' => 'number', '', 'label' => false, 'placeholder' => 'Enter Item HSN Number', 'autofocus', 'autocomplete' => 'off', 'id' => 'item-isbn')); ?>

                  </div>
                </div>

                <div class="col-md-4">

                  <label for="inputEmail3" class="col-sm-12 control-label" style="text-align: left !important;">Sale
                    Price</label>

                  <div class="col-md-12">
                    <?php echo $this->Form->input('sale_price', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'placeholder' => 'Enter Sale Price', 'autofocus', 'autocomplete' => 'off')); ?>
                  </div>
                </div>

                <div class="col-md-4">

                  <label for="inputEmail3" class="col-sm-12 control-label" style="text-align: left !important">Discount
                    Amount</label>

                  <div class="col-md-12">
                    <?php echo $this->Form->input('discount', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'placeholder' => 'Enter Discount Amount', 'autofocus', 'autocomplete' => 'off')); ?>
                  </div>
                </div>

                <input type="hidden" name="cname" value="<?php echo $company[1]; ?>">

                <div class="col-md-4">
                  <label for="inputEmail3" class=" control-label" style="text-align: left !important">UOM<strong
                      style="color:red;">*</strong></label>
                  <?php echo $this->Form->input('uom', array('class' => 'form-control', 'type' => 'select', 'options' => $units, 'required', 'label' => false, 'autofocus', 'empty' => '---- Select UOM ----', 'autocomplete' => 'off')); ?>
                </div>
                <div class="col-md-4">
                  <label for="inputEmail3" class=" control-label" style="text-align: left !important;">Weight</label>
                  <?php echo $this->Form->input('weight', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off')); ?>
                </div>
                <div class="col-md-4">
                  <label for="inputEmail3" class=" control-label" style="text-align: left !important;">Volume</label>
                  <?php echo $this->Form->input('volume', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off')); ?>
                </div>

                <div class="col-md-4">
                  <label for="inputEmail3" class=" control-label" style="text-align: left !important;">Min. Order
                    Qty</label>
                  <?php echo $this->Form->input('min_order_qty', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off')); ?>
                </div>




                <!-- <div class="col-md-4">
                  <label for="inputEmail3" class="col-sm-12 control-label"
                    style="text-align: left !important">Tax<strong style="color:red;">*</strong></label>
                  <div class="col-md-12">
                    <?php ///echo $this->Form->input('tax', array('class' => 'form-control', 'type' => 'select', 'options' => $tax, 'required', 'label' => false, 'empty' => 'Tax', 'autofocus', 'autocomplete' => 'off')); ?>
                  </div>
                </div> -->

                <div class="col-sm-4" style="margin-bottom:15px;">
                  <label for="inputEmail3" class="control-label">Type :</label><br>
                  <label class="radio-inline">
                    <input type="radio" name="itemtype" class="mode radio-inline checkstr " value="RawMaterial" id="rawpro"
                      <?php if ($addeditem['itemtype'] == 'RawMaterial') {
                        echo "checked";
                      } ?>>&nbsp;RawMaterial
                  </label>

                  <label class="radio-inline">
                    <input type="radio" name="itemtype" class="mode radio-inline checkstr" value="FinishedProduct" id="finishedpro"
                      <?php if ($addeditem['itemtype'] == 'FinishedProduct') {
                        echo "checked";
                      } ?>>&nbsp; FinishedProduct
                  </label>
                </div>


                <div class="col-sm-4" id="proceesname" style="margin-bottom:15px; display: none;">
                  <label for="inputEmail3" class="control-label">Process Name :</label><br>
                  <?php
                  $selectprocess = [];
                  $selectprocess = explode(',', $addeditem['productprocess_id']);

                  foreach ($finishedprocess as $key => $value) { ?>
                    <input type="radio" name="finishedprocess_id" class="mode radio-inline checkstr" value="<?php echo $key ?>"
                      <?php if ($addeditem['finishedprocess_id'] == $key) {
                        echo "checked";
                      } ?>> &nbsp;

                    <input type="checkbox" name="productprocess_id[]" value=" <?php echo $key ?>"
                      <?php if (in_array($key, $selectprocess)) {
                        echo "checked";
                      } ?>><?php echo $value ?> &nbsp;<br>
                  <?php } ?>
                </div>
              </div>

              <input type="hidden" name="234" class="checkitem" value="<?php echo $addeditem['itemtype'] ?>" id="checkitemtype">

              <div class="box-footer">

                <?php
                if (isset($addeditem['id'])) {
                  echo $this->Form->submit(
                    'Edit',
                    array('class' => 'btn btn-info pull-right', 'id' => 'formsubmitbtn', 'title' => 'Edit')
                  );
                } else {
                  echo $this->Form->submit(
                    'Add',
                    array('class' => 'btn btn-info pull-right', 'id' => 'formsubmitbtn', 'title' => 'Add')
                  );
                }
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
<script>
  $(document).ready(function() {

    var id = $('#location-name').val();
    if (id) {
      $.ajax({
        type: 'POST',
        url: '<?php echo SITE_URL; ?>/admin/additem/find_sublocation',
        data: {
          'id': id
        },
        success: function(data) {
          $('#sub-location').empty();
          $('#sub-location').html(data);
        },

      });
    }

  });
  $(document).ready(function() {
    $('#location-name').on('change', function() {
      var id = $('#location-name').val();
      if (id) {
        $.ajax({
          type: 'POST',
          url: '<?php echo SITE_URL; ?>/admin/additem/find_sublocation',
          data: {
            'id': id
          },
          success: function(data) {
            $('#sub-location').empty();
            $('#sub-location').html(data);
          },

        });
      }
    });
  });
</script>
<!-- end  -->


<script>
  $(document).ready(function() {
    var chechitemtype = $("#checkitemtype").val();
    if (chechitemtype == 'FinishedProduct') {
      $("#proceesname").css("display", "block");
    }

    // $('#sevice_form').on('submit', function(e) {
    //   $("#formsubmitbtn").css("display", "none");
    // });
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