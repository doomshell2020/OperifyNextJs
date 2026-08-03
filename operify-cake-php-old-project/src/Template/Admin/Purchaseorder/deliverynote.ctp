<style>
  #test1UL {
    position: relative;
  }

  #test1UL ul {
    position: absolute;
    z-index: 999;
    overflow: scroll;
    height: 100px;
    top: 100%;
    left: 0px;
    right: 0px;
    list-style-type: none;
    background-color: white;
    padding-left: 0px;
  }

  #test1UL ul li {
    padding: 5px 8px;
    border: 1px solid lightgray;
  }

  #test1UL ul li a {
    color: black;
  }

  .preview {
    margin-right: 15px;
  }

  .input_fields_wrap .form-control {
    margin-bottom: 15px;
  }
</style>
<style>
  #customers {
    font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
    border-collapse: collapse;
    width: 100%;
    margin-bottom: 20px;
  }

  #customers td,
  #customers th {
    border: 1px solid #ddd;
    padding: 8px;
  }

  #customers tr:nth-child(even) {
    background-color: #f2f2f2;
  }

  #customers tr:hover {
    background-color: #ddd;
  }

  #customers th {
    padding-top: 12px;
    padding-bottom: 12px;
    text-align: left;
    background-color: #c8c8c8;
    color: #333333;
  }

  #testUL,
  #testULs {
    position: relative;
    display: none;
  }

  #testUL ul,
  #testULs ul {
    position: absolute;
    max-height: 140px;
    overflow: scroll;
    z-index: 999;
    top: 100%;
    left: 0px;
    right: 0px;
    list-style-type: none;
    background-color: white;
    padding-left: 0px;
  }

  #testUL ul li,
  #testULs ul li {
    padding: 5px 8px;
    border: 1px solid lightgray;
  }

  #testUL ul li a,
  #testULs ul li a {
    color: black;
  }
</style>
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Delivery Note
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo SITE_URL; ?>admin/purchaseorder"><i class="fa fa-home"></i>Home</a></li>
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
              <?php echo 'Delivery Note For Purchase Order id : ' . $revised['purchaseorder_id']; ?>
            </h3>
          </div>
          <!-- /.box-header -->
          <!-- form start -->
          <?php echo $this->Form->create(
            $revised,
            array(
              'controller' => 'purchaseorder',
              'action' => 'deliverynote',
              'class' => 'form-horizontal',
              'enctype' => 'multipart/form-data',
              'id' => 'sevice_form',
              'validate'
            )
          ); ?>
          <!-- <input type="hidden" name="token" value=<?php echo uniqid(); ?>> -->
          <div class="box-body">
            <?php echo $this->Form->input('purchaseorder_id', array('class' => 'form-control', 'id' => 'purchaseorder', 'type' => 'hidden', 'value' => $revised['purchaseorder_id'], 'readonly', 'label' => false, 'placeholder' => 'purchaseorder id', 'autofocus', 'autocomplete' => 'off')); ?>
            <div class="form-group" style="margin-bottom:0px;">
              <div class="row">

                <div class="col-sm-3" style="margin-bottom:15px;">
                  <label for="inputEmail3" class="">PO Date <strong style="color:red;">*</strong></label>
                  <?php echo $this->Form->input('inwarddate', array('class' => 'form-control', 'type' => 'text', 'readonly', 'label' => false, 'autofocus', 'value' => date('d-m-Y', strtotime($revised['added_time'])), 'autocomplete' => 'off', 'required')); ?>
                </div>

                <div class="col-sm-3" style="margin-bottom:15px;">
                  <label for="inputEmail3" class="">Expected Delivery Date<strong style="color:red;">*</strong></label>
                  <?php echo $this->Form->input('inwarddate', array('class' => 'form-control', 'type' => 'text', 'readonly', 'label' => false, 'autofocus', 'value' => date('d-m-Y', strtotime($revised['delivery_date'])), 'autocomplete' => 'off', 'required')); ?>
                </div>

                <div class="col-sm-3">
                  <label for="inputEmail3" class=" control-label">Supplier <strong style="color:red;">*</strong></label>
                  <input type="hidden" name="vendor_id" id="retail_ids" value="<?php echo $revised['vendor_id']; ?>">
                  <?php echo $this->Form->input('vendorname', array('class' => 'form-control', 'id' => 'supplier', 'type' => 'text', 'readonly', 'value' => $vendorname['name'], 'label' => false, 'autofocus', 'autocomplete' => 'off', 'required', 'placeholder' => 'Enter Supplier Name')); ?>
                </div>

              </div>
            </div>


            <div class="ctpcontent form-group" style="display:block">
              <div class="col-sm-12">
                <label for="inputEmail3" style="margin-bottom:10px;">Items</label>
                <table id="customers">
                  <thead>
                    <tr class="totalColumn">
                      <th>Item</th>
                      <th>PO Qty</th>
                      <?php
                      $getDeliverydates = $this->Comman->getDeliverydates($revised['id']);
                      if (!empty($getDeliverydates)) {
                        $no = 1;
                        foreach ($getDeliverydates as $dates) { ?>
                          <th>Date<?php echo $no; ?></th>
                          <th>Qty</th>
                          <?php
                          $no++;
                        }

                        if ($no < 5) {
                          for ($con = $no; $con < 5; $con++) { ?>
                            <th>Date<?php echo $con; ?></th>
                            <th>Qty</th>
                        <?php }
                        }
                      } else { ?>
                        <th>Date1</th>
                        <th>Qty</th>
                        <th>Date2</th>
                        <th>Qty</th>
                        <th>Date3</th>
                        <th>Qty</th>
                        <th>Date4</th>
                        <th>Qty</th>
                      <?php } ?>
                    </tr>
                  </thead>
                  <tbody id="product_containes">
                    <?php
                    foreach ($poitems as $value) {
                      $i = $value['item_id'];
                      $getitemname = $this->Comman->getitemname($value['item_id']);
                    ?>

                      <tr class="video_details ">
                        <td width="19%">
                          <?php echo $this->Form->input('pitemname[]', array('class' => 'form-control', 'id' => 'pitemname', 'type' => 'hidden', 'value' => $value['item_id'], 'label' => false, 'autofocus', 'autocomplete' => 'off')); ?>

                          <?php echo $this->Form->input('name[]', array('class' => 'form-control', 'id' => 'pitemname', 'type' => 'text', 'value' => $getitemname['item_name'], 'label' => false, 'autofocus', 'autocomplete' => 'off', 'readonly')); ?>
                          <small style="font-weight:bold;position: relative;"><span style="color:red;"></span></small>
                        </td>

                        <td width="09%">
                          <input type="text" onkeypress='return isNumberKey(event)' name="pitemquantity[]"
                            class="form-control newquan quntt<?php echo $i; ?>" id="quan" autocomplete='off' required
                            readonly value="<?php echo sprintf('%.2f', $value['item_qty']); ?>">
                        </td>

                        <script>
                          $(document).ready(function() {
                            for (let i = 1; i <= 4; i++) {
                              $(`.datepicker${i}`).datepicker({
                                dateFormat: 'dd-mm-yy',
                                yearRange: '2018:2027',
                                minDate: '<?php echo date('d-m-Y', strtotime($revised['added_time'])) ?>',
                                maxDate: '<?php echo date('d-m-Y', strtotime($revised['delivery_date'])) ?>',
                                onSelect: function(dateText) {
                                  let isDuplicate = false;

                                  for (let j = 1; j <= 4; j++) {
                                    if (i == j) {
                                      continue;
                                    }
                                    let date = $(`.datepicker${j}`).val();
                                    if (date === dateText) {
                                      isDuplicate = true;
                                      break;
                                    }
                                  }

                                  if (isDuplicate) {
                                    alert("Each date must be unique. Please select different dates.");
                                    $(`.datepicker${i}`).val('');
                                  } else {
                                    $(`.datepicker${i}`).val(dateText);
                                  }
                                }
                              });
                            }
                          });
                        </script>


                        <?php

                        // for Edit delivery schedule
                        $counter = 1;
                        if (!empty($getDeliverydates)) {
                          foreach ($getDeliverydates as $dates) {
                            $getitemqty = $this->Comman->DeliveritemQty($value['item_id'], $value['poprimary_id'], date('Y-m-d', strtotime($dates['delivery_date'])));
                            $delivery_date = date('d-m-Y', strtotime($dates['delivery_date']));
                            $qty = $getitemqty['item_qty'] ? $getitemqty['item_qty'] : 0; ?>

                            <td width="09%">
                              <?php echo $this->Form->input("inwarddate[$counter]", array('class' => "form-control datepicker$counter  inwarddate-$i", 'type' => 'text', 'readonly', 'label' => false, 'autofocus', 'value' => $delivery_date, 'autocomplete' => 'off')); ?>

                            <td width="09%">
                              <?php echo $this->Form->input("qty[$i][$counter]", array('class' => "form-control qty-$i", 'onkeypress' => 'return isNumberKey(event)', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'value' => $qty, 'required', 'readonly' => ($getitemqty['status'] == 'N'))); ?>
                            </td>
                            <?php
                            $counter++;
                          }

                          if ($counter < 5) {
                            for ($con = $counter; $con < 5; $con++) { ?>
                              <td width="09%">
                                <?php echo $this->Form->input("inwarddate[$con]", array('class' => "form-control datepicker$con  inwarddate-$i", 'type' => 'text', 'readonly', 'label' => false, 'autofocus', 'autocomplete' => 'off')); ?>
                              </td>

                              <td width="09%">
                                <?php echo $this->Form->input("qty[$i][$con]", array('class' => "form-control qty-$i", 'onkeypress' => 'return isNumberKey(event)', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'value' => '0', 'required')); ?>
                              </td>
                            <?php }
                          }
                        } else {
                          // for Add delivery schedule
                          for ($k = 1; $k < 5; $k++) { ?>
                            <td width="09%">
                              <?php echo $this->Form->input("inwarddate[$k]", array('class' => "form-control datepicker$k  inwarddate-$i", 'type' => 'text', 'readonly', 'label' => false, 'autofocus', 'autocomplete' => 'off')); ?>
                            </td>
                            <td width="09%">
                              <?php echo $this->Form->input("qty[$i][$k]", array('class' => "form-control qty-$i", 'onkeypress' => 'return isNumberKey(event)', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'value' => '0', 'required')); ?>
                            </td>
                        <?php }
                        } ?>
                      </tr>

                      <script>
                        $(document).ready(function() {
                          $('.qty-<?php echo $i; ?>').on('input', function() {
                            var quat = parseFloat($(this).val()) || 0;
                            var totalQty = parseFloat($('.quntt<?php echo $i; ?>').val()) || 0;

                            let sum = 0;
                            $('.qty-<?php echo $i; ?>').each(function() {
                              let value = parseFloat($(this).val()) || 0;
                              sum += value;
                            });
                            if (totalQty < sum) {
                              alert(`Total schedule quantity can not be greater then ${totalQty}`);
                              maxQty = totalQty - (sum - quat);
                              $(this).val(maxQty);
                            }
                          });
                        });
                      </script>

                    <?php
                    } ?>
                  </tbody>
                </table>
              </div>
            </div>


            <div class="form-group">
              <div class="col-sm-12">
                <label for="inputEmail3" style="margin-bottom:10px;">Remark<strong style="color:red;"></strong></label>
                <?php echo $this->Form->input('remark', array('class' => 'form-control', 'id' => 'remark', 'type' => 'textarea', 'label' => false, 'placeholder' => 'Remark', 'autofocus', 'autocomplete' => 'off', '')); ?>
              </div>
            </div>

          </div>

          <!-- /.box-body -->
          <div class="box-footer">
            <?php echo $this->Form->submit('Submit', array('class' => 'btn btn-info pull-right', 'id' => 'formsubmitbtn', 'title' => 'Submit'));

            echo $this->Html->link('Back', ['action' => 'index'], ['class' => 'btn btn-default']); ?>
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
  function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    var inputValue = evt.target.value;
    var hasDecimal = inputValue.includes('.');
    if (charCode === 46) {
      if (hasDecimal) {
        return false;
      }
    } else if (charCode > 31 && (charCode < 48 || charCode > 57)) {
      return false;
    }
    if (hasDecimal) {
      var decimalIndex = inputValue.indexOf('.');
      var decimalPart = inputValue.substring(decimalIndex + 1);
      if (decimalPart.length >= 2) {
        return false;
      }
    }
    return true;
  }
</script>



<script>
  $(document).ready(function() {
    $('#sevice_form').on('submit', function(e) {
      e.preventDefault();

      var items = <?php echo json_encode($poitems); ?>;
      let formValid = true;

      $.each(items, function(index, item) {
        var itemId = item.item_id;
        var itempoQty = parseFloat($(`.quntt${itemId}`).val()) || 0;

        let sum = 0;
        let dateValid = true;


        $(`.qty-${itemId}`).each(function(i) {
          var date = $(`.datepicker${i + 1}`).val();
          let value = parseFloat($(this).val()) || 0;

          if (value > 0 && date === '') {
            alert(`Date${i + 1} cannot be blank`);
            dateValid = false;
            formValid = false;
            return false;
          }
          sum += value;
        });


        if (dateValid && sum < itempoQty) {
          alert(`Total quantity cannot be less than ${itempoQty}`);
          formValid = false;
          return false;
        }
      });

      if (formValid) {
        $("#formsubmitbtn").css("display", "none");
        this.submit();
      }
    });
  });
</script>