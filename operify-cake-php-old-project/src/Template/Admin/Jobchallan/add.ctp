  <style>
      .preview {
          margin-right: 15px;
      }

      #load2 {
          width: 100%;
          height: 100%;
          position: fixed;
          z-index: 9999;
          background-color: white !important;
          background: url("<?php echo SITE_URL; ?>images/Preloader_2.gif") no-repeat center center rgba(0, 0, 0, 0.75)
      }
  </style>

  <style>
      .testUL {
          position: relative;
      }

      .testUL ul {
          position: absolute;
          z-index: 999;
          max-height: 150px;
          overflow-y: auto;
          top: 100%;
          left: 0;
          right: 0;
          list-style: none;
          background: white;
          padding: 0;
          margin: 0;
          border: 1px solid #ccc;
      }

      .testUL ul li {
          padding: 6px 10px;
          cursor: pointer;
      }

      .testUL ul li:hover {
          background: #f1f1f1;
      }
  </style>

  <div class="content-wrapper">

      <section class="content-header">
          <h1>Job Challan</h1>
      </section>

      <section class="content">

          <div class="box">
              <div class="box-header">
                  <h3 class="box-title">Add Job Challan</h3>
              </div>

              <div class="box-body">

                  <?= $this->Form->create($entity) ?>

                  <!-- TOP FIELDS -->
                  <div class="row">

                      <div class="form-group col-sm-3">
                          <label>Date</label>
                          <?php echo $this->Form->input('jc_dates', array('class' => 'form-control', 'id' => 'fdatefrom', 'readonly', 'placeholder' => 'Date From', 'label' => false)); ?>
                      </div>

                      <div class="form-group col-sm-3">
                          <label>Sub Contractor</label>
                          <?= $this->Form->input('sub_contractors_id', [
                                'options' => $subContractors,
                                'empty' => 'Select Sub Contractor',
                                'label' => false,
                                'class' => 'form-control',
                                'id' => 'vendor_id'
                            ]) ?>
                      </div>

                      <div class="form-group col-sm-3">
                          <label>GST No.</label>
                          <?= $this->Form->input('gst_no', [
                                'type' => 'text',
                                'label' => false,
                                'class' => 'form-control',
                                'id' => "gst_no",
                                'readonly'
                            ]) ?>
                      </div>

                      <div class="form-group col-sm-3">
                          <label>Processing / Manufacturing Type</label>
                          <?= $this->Form->input('processing_type', [
                                'type' => 'text',
                                'label' => false,
                                'class' => 'form-control'
                            ]) ?>
                      </div>

                      <div class="form-group col-sm-3">
                          <label>Estimated Value</label>
                          <?= $this->Form->input('estimated_values', [
                                'type' => 'number',
                                'label' => false,
                                'class' => 'form-control'
                            ]) ?>
                      </div>

                      <div class="form-group col-sm-3">
                          <label>Expected Days</label>
                          <?= $this->Form->input('expected_days', [
                                'type' => 'number',
                                'label' => false,
                                'class' => 'form-control'
                            ]) ?>
                      </div>

                      <!-- <div class="form-group col-sm-3">
                          <label>Identification Marks and Numbers if any</label>
                          <?php /* $this->Form->input('expected_days', [
                                'type' => 'text',
                                'label' => false,
                                'class' => 'form-control'
                            ]) */?>
                      </div> -->

                  </div>

                  <!-- WORK DESCRIPTION -->
                  <div class="row">
                      <div class="form-group col-sm-4">
                          <label>Work Description</label>
                          <?= $this->Form->input('work_description', [
                                'type' => 'textarea',
                                'label' => false,
                                'class' => 'form-control'
                            ]) ?>
                      </div>
                  </div>

                  <hr>

                  <div class="table-responsive">

                      <table class="table table-bordered">

                          <thead style="background:#3c8dbc; color:#fff;">
                              <tr>
                                  <th>Item Name</th>
                                  <th>In Hand Quantity</th>
                                  <th>Quantity</th>
                                  <th>HSN/SAC</th>
                                  <th>Rate</th>
                                  <th>Tax</th>
                                  <th>Tax Amount</th>
                                  <th>Total Amount</th>
                              </tr>
                          </thead>

                          <tbody>
                              <tr>

                                  <td>
                                      <input type="text" name="job_challan_items[0][item_name]" class="form-control secrh-retail">
                                      <div class="testUL" style="display:none;">
                                          <ul></ul>
                                      </div>
                                      <input type="hidden" name="job_challan_items[0][item_id]" class="retail_ids">
                                  </td>

                                  <td><input type="text" name="job_challan_items[0][in_hand_qty]" class="form-control inhand_qty" readonly></td>
                                  <td><input type="text" name="job_challan_items[0][quantity]" class="form-control qty"></td>

                                  <td><input type="text" name="job_challan_items[0][hsn_code]" class="form-control"></td>

                                  <td><input type="text" name="job_challan_items[0][rate]" class="form-control rate"></td>

                                  <!-- GST % dropdown -->
                                  <td>
                                      <?= $this->Form->input('job_challan_items.0.tax_rate', [
                                            'options' => $taxMaster, // ex: [5=>'5%',12=>'12%',18=>'18%']
                                            'empty' => '-- Select Tax --',
                                            'label' => false,
                                            'class' => 'form-control tax'
                                        ]) ?>
                                  </td>

                                  <td><input type="text" name="job_challan_items[0][tax_amount]" class="form-control tax_amount" readonly></td>

                                  <td><input type="text" name="job_challan_items[0][total_amount]" class="form-control amount" readonly></td>

                              </tr>

                          </tbody>

                      </table>

                  </div>

                  <br>
                  <!-- BUTTONS -->
                  <div class="row">
                      <div class="form-group col-sm-12">

                          <button type="submit" class="btn btn-success">Save</button>
                          <button type="reset" class="btn btn-primary">Reset</button>

                      </div>
                  </div>

                  <?= $this->Form->end() ?>

              </div>
          </div>

      </section>
  </div>
  <script>
      /* AUTO AMOUNT */
      $(document).on('keyup change', '.qty, .rate, .tax', function() {

          let row = $(this).closest('tr');

          let qty = parseFloat(row.find('.qty').val()) || 0;
          let rate = parseFloat(row.find('.rate').val()) || 0;
          let gst = parseFloat(row.find('.tax').val()) || 0;

          let inhand = parseFloat(row.find('.inhand_qty').val()) || 0;

          // 🔥 VALIDATION
          if (qty > inhand) {

              alert('Quantity cannot be greater than available stock (' + inhand + ')');

              // clear qty + related fields
              row.find('.qty').val('');
              row.find('.tax_amount').val('');
              row.find('.amount').val('');

              return false;
          }


          // Basic Amount
          let amount = qty * rate;

          // GST Amount
          let gst_amount = (amount * gst) / 100;

          // Total
          let total = amount + gst_amount;

          // Set values
          row.find('.tax_amount').val(gst_amount.toFixed(2));
          row.find('.amount').val(total.toFixed(2));

      });


      /* PRODUCT SEARCH */
      $(document).on('keyup', '.secrh-retail', function() {

          let input = $(this);
          let value = input.val();
          let dropdown = input.closest('td').find('.testUL');

          if (value.length > 0) {

              $.ajax({
                  type: 'POST',
                  url: '<?php echo ADMIN_URL; ?>Jobchallan/getitemname',
                  data: {
                      fetch: value,
                      check: 0
                  },
                  success: function(data) {
                      dropdown.show();
                      dropdown.find('ul').html(data);
                  }
              });

          } else {
              dropdown.hide();
          }
      });


      /* SELECT ITEM */
      //   $(document).on('click', '.testUL ul li', function() {

      //       let text = $(this).text();
      //       let id = $(this).attr('data-id');

      //       let row = $(this).closest('td');

      //       row.find('.secrh-retail').val(text);
      //       row.find('.retail_ids').val(id);
      //       row.find('.testUL').hide();

      //   });

      $(document).on('click', '.testUL ul li', function() {

          let text = $(this).text();
          let id = $(this).attr('data-id');

          let row = $(this).closest('td');

          // set values
          row.find('.secrh-retail').val(text);
          row.find('.retail_ids').val(id);
          row.find('.testUL').hide();

          // 🔥 AJAX CALL USING ITEM ID
          if (id != '') {

              $.ajax({
                  type: 'POST',
                  url: '<?php echo ADMIN_URL; ?>Jobchallan/getItemInHandStock',
                  data: {
                      item_id: id
                  },

                  success: function(response) {

                      // assume JSON response
                      //   let data = JSON.parse(response);
                      $('.inhand_qty').val(response);

                  }
              });

          }

      });
  </script>


  <script>
      $(document).on('change', '#vendor_id', function() {

          var vendor_id = $(this).val();

          if (vendor_id != '') {

              $.ajax({
                  type: 'POST',
                  url: '<?php echo ADMIN_URL; ?>Jobchallan/getVendorGst',
                  data: {
                      vendor_id: vendor_id
                  },

                  success: function(response) {
                      $('#gst_no').val(response);
                  }
              });

          } else {
              $('#gst_no').val('');
          }

      });
  </script>

  <script>
      $(document).ready(function() {
          $('#fdatefrom').datepicker({
              dateFormat: 'dd-mm-yy',
              yearRange: '2018:2030',
              changeMonth: true,
              changeYear: true,
          });

      });
  </script>