<style>
  .quatationCheckDv .col-sm-3 {
    height: 100%;
  }

  .quatationCheckDv {
    align-items: stretch;
  }

  .control-label {
    display: block;
    margin-top: 10px;
  }

  .preview {
    margin-right: 15px;
  }

  .input_fields_wrap .form-control {
    margin-bottom: 15px;
  }
</style>

<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Quotation Order Manager
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo SITE_URL; ?>admin/Quotation"><i class="fa fa-home"></i>Home</a></li>
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
          <div class="box-header with-border"></div>
          <!-- /.box-header -->
          <!-- form start -->

          <?php echo $this->Form->create(
            $location,
            array(
              'class' => 'form-horizontal',
              'enctype' => 'multipart/form-data',
              'onsubmit' => "return validateForm()",
              'name' => 'myForm',
              'id' => 'sevice_form',
              'validate'
            )
          ); ?>

          <div class="box-body">

            <div class="form-group" style="margin-top:10px;">
              <!-- Search Box -->
              <div class="col-sm-4" style="margin:10px 0;">
                <input type="text" id="vendorSearch" class="form-control" placeholder="Search Vendor...">
              </div>
              <div class="row quatationCheckDv">

                <!-- Select All -->
                <div class="col-sm-12">
                  <input type="checkbox" id="selectAll" class="form-check-input"> <b>Select All</b>
                </div>


                <!-- Vendor List -->
                <?php
                foreach ($vendornames as $vendor) {
                  if (in_array($vendor['id'], $vendorIdsArray)) {
                    continue;
                  }
                ?>
                  <div class="col-sm-4 vendor-item">
                    <input type="checkbox" class="form-check-input vendor-checkbox"
                      name="vendor_ids[]"
                      value="<?= $vendor['id']; ?>">
                    <?= ucfirst(strtolower($vendor['name'])) . ' <b>(' . $vendor['contact_person'] . ')</b>' ?>
                  </div>
                <?php } ?>
              </div>
            </div>

          </div>
          <div class="box-footer">
            <?php echo $this->Form->submit('Save & Finalize', array('class' => 'btn btn-info pull-right', 'id' => 'formsubmitbtn', 'title' => 'Save & Finalize'));
            echo $this->Html->link('Back', ['action' => 'index'], ['class' => 'btn btn-default']); ?>
          </div>
          <?php echo $this->Form->end(); ?>
        </div>
      </div>
    </div>
  </section>
</div>

<script>
  $(document).ready(function() {
    // Select/Deselect all vendors
    $('#selectAll').on('change', function() {
      $('.vendor-checkbox').prop('checked', this.checked);
    });

    // Check if all vendors are selected -> mark selectAll
    $('.vendor-checkbox').on('change', function() {
      $('#selectAll').prop('checked', $('.vendor-checkbox:checked').length === $('.vendor-checkbox').length);
    });

    // Live Search Filter
    $('#vendorSearch').on('keyup', function() {
      var value = $(this).val().toLowerCase();
      $('.vendor-item').filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
      });
    });
  });
</script>