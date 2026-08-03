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
      Revised Purchase Order Manager
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
              <?php echo 'Revised Purchase Order id : ' . $revised['purchaseorder_id']; ?>
            </h3>
          </div>
          <!-- /.box-header -->
          <!-- form start -->
          <?php echo $this->Form->create(
            $revised,
            array(
              'controller' => 'purchaseorder',
              'action' => 'revised',
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
                <script>
                  $(document).ready(function () {

                    $('#datepicker3').datepicker({
                      dateFormat: 'dd-mm-yy',
                      yearRange: '2018:2030',
                      changeMonth: true,
                      changeYear: true,
                      autoclose: true,
                      maxDate: new Date(),
                      onSelect: function (date) {
                        var selectedDate = new Date(date);
                        var endDate = new Date(selectedDate);
                        endDate.setDate(selectedDate);
                      }
                    });

                    $('#datepicker1').datepicker({
                      dateFormat: 'dd-mm-yy',
                      yearRange: '2018:2027',
                      // maxDate: new Date(),
                    });
                  });
                </script>
                <div class="col-sm-3" style="margin-bottom:15px;">
                  <label for="inputEmail3" class="">Generated Date <strong style="color:red;">*</strong></label>
                  <?php echo $this->Form->input('inwarddate', array('class' => 'form-control', 'type' => 'text', 'readonly', 'label' => false, 'autofocus','value' =>  date("d-m-Y"), 'autocomplete' => 'off', 'required')); ?>
                </div>
                <div class="col-sm-3">
                  <label for="inputEmail3" class=" control-label">Supplier <strong style="color:red;">*</strong></label>
                  <input type="hidden" name="vendor_id" id="retail_ids" value="<?php echo $revised['vendor_id']; ?>">
                  <?php echo $this->Form->input('vendorname', array('class' => 'form-control', 'id' => 'supplier', 'type' => 'text', 'readonly', 'value' => $vendorname['name'], 'label' => false, 'autofocus', 'autocomplete' => 'off', 'required', 'placeholder' => 'Enter Supplier Name')); ?>
                </div>
                <div class="col-sm-3">
                  <label for="inputEmail3" class=" control-label">Expected Delivery Date<strong
                      style="color:red;">*</strong></label>
                  <?php echo $this->Form->input('delivery_date', array('class' => 'form-control', 'id' => 'datepicker1', 'type' => 'text', 'label' => false, 'placeholder' => 'Delivery Date', 'value' =>  date("d-m-Y", strtotime($revised['delivery_date'])), 'autofocus', 'autocomplete' => 'off', 'required')); ?>
                </div>

                <div class="col-sm-3">
                  <label for="inputEmail3" class=" control-label">Contract</label>
                  <?php echo $this->Form->input('contract', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'placeholder' => 'Enter Contract Name')); ?>
                </div>
                <!-- <div class="col-sm-3">
                  <label for="inputEmail3" class=" control-label">Project</label>
                  <?php //echo $this->Form->input('project', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'placeholder' => 'Enter Project Name'));     ?>
                </div> -->
              </div>
            </div>



            <div class="ctpcontent form-group" style="display:block">
              <div class="col-sm-12">
                <label for="inputEmail3" style="margin-bottom:10px;">Items</label>
                <table id="customers">
                  <thead>
                    <tr class="totalColumn">
                      <th>Item</th>
                      <th>Qty</th>
                      <th>UOM</th>
                      <th>Weight</th>
                      <th>Volume</th>
                      <th>Unit Price</th>
                      <th>Total Price</th>
                      <th>Tax Rate</th>
                      <th>Tax Amount</th>
                      <th>Total Amount</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody id="product_containes">
                    <?php
                    foreach ($poitems as $value) {
                      $i = $value['item_id'];
                      $getitemname = $this->Comman->getitemname($value['item_id']);
                      ?>

                      <tr class="video_details ">
                        <td width="17%">
                          <?php echo $this->Form->input('pitemname[]', array('class' => 'form-control', 'id' => 'pitemname', 'type' => 'hidden', 'value' => $value['item_id'], 'label' => false, 'autofocus', 'autocomplete' => 'off')); ?>
                          <?php
                          echo $this->Form->input('name[]', array('class' => 'form-control', 'id' => 'pitemname', 'type' => 'text', 'value' => $getitemname['item_name'], 'label' => false, 'autofocus', 'autocomplete' => 'off', 'readonly'));
                          ?>
                          <small style="font-weight:bold;position: relative;"><span style="color:red;"></span></small>
                        </td>

                        <td width="8%"><input type="text" onkeypress='return isNumberKey(event)' name="pitemquantity[]"
                            class="form-control newquan quntt<?php echo $i; ?>" id="quan" autocomplete='off' required
                            value="<?php echo sprintf('%.2f', $value['item_qty']); ?>"></td>

                        <td width="6%">
                          <?php
                          echo $this->Form->input('unit_name[]', array('class' => 'form-control', 'type' => 'text', 'value' => $value['uom'], 'label' => false, 'autofocus', 'autocomplete' => 'off', 'readonly')); ?>
                        </td>
                        <td width="6%">
                          <?php
                          echo $this->Form->input('weight[]', array('class' => 'form-control', 'type' => 'text', 'value' => $value['weight'], 'label' => false, 'autofocus', 'autocomplete' => 'off', 'readonly')); ?>
                        </td>
                        <td width="6%">
                          <?php
                          echo $this->Form->input('volume[]', array('class' => 'form-control', 'type' => 'text', 'value' => $value['volume'], 'label' => false, 'autofocus', 'autocomplete' => 'off', 'readonly')); ?>
                        </td>
                        <td width="10%"><input style="text-align: right;" type="text" maxlength="10" name="pitemrate[]"
                            class="form-control filterme newpitra pitraa<?php echo $i; ?>" id="pitemrate"
                            autocomplete="off" value="<?php echo sprintf('%.2f', $value['item_amt']); ?>">
                        </td>

                        <?php
                        $costprice = $value['item_qty'] * $value['item_amt'];
                        ?>
                        <td width="10%">
                          <input style="text-align: right;" type="text" name="pitemamount[]"
                            class="form-control newtamo pitama<?php echo $i; ?>" id="pitemamount<?php echo $i ?>"
                            value="<?php echo $costprice; ?>" readonly>
                        </td>

                        <td width="15%">

                          <?php
                          $options = [2 => 1,3 => 5, 4 => 6,5 => 12, 6 => 18, 7 => 28 ];
                          echo $this->Form->input('tax_id[]', array('class' => 'form-control taxamount' . $i, 'type' => 'select', 'value' => $value['tax_id'], 'options' => $options, 'label' => false, 'autofocus', 'autocomplete' => 'off', 'readonly')); ?>
                        </td>

                        <td width="10%">
                          <input style="text-align: right;" type="text" name="pitemtax[]"
                            value="<?php echo $value['item_tax_amt']; ?>"
                            class="form-control  newtaxx pitax<?php echo $i; ?>" id="pitax<?php echo $i; ?>" readonly>
                        </td>
                        <td width="25%">
                          <input style="text-align: right;" type="text" name="totalamount[]" readonly
                            class="form-control newtamso totalamount<?php echo $i; ?>" id="totalamount<?php echo $i; ?>"
                            value="<?php echo sprintf('%.2f', $value['item_total_amount']); ?>">
                        </td>
                        <td>
                          <span class="fas fa-trash-alt delete-button" data-id="<?php echo $i; ?>"
                            id="deletebtn-<?php echo $i; ?>" onclick="deleteRow(<?php echo $i; ?>)"
                            style="font-size: 21px; color:#cd0404"></span>
                        </td>
                      </tr>


                      <script>
                        $(document).ready(function () {
                          $('.quntt<?php echo $i; ?>').on('keyup', function () {
                            var quat = $(this).val();
                            var pitra = $('.pitraa<?php echo $i; ?>').val();
                            var taxa = $('.taxamount<?php echo $i; ?>').val();
                            var tcrate = parseFloat(pitra) * parseFloat(quat);
                            tcrate = tcrate.toFixed(2);
                            $.ajax({
                              type: 'POST',
                              url: '<?php echo ADMIN_URL; ?>purchaseorder/gettax',
                              data: {
                                'fetch': taxa
                              },
                              success: function (data) {
                                var withtax = (data * (tcrate / 100)).toFixed(2);
                                var toamountnew = parseFloat(tcrate) + parseFloat(withtax);

                                $('.pitama<?php echo $i; ?>').val(tcrate);
                                $('.totalamount<?php echo $i; ?>').val(toamountnew);
                                $('.pitax<?php echo $i; ?>').val(withtax);
                                total();
                                total2();
                                total3();
                              },
                            });
                          });

                          $('.pitraa<?php echo $i; ?>').on('keyup', function () {
                            var pitra = $(this).val();
                            var quat = $('.quntt<?php echo $i; ?>').val();
                            var tcrate = parseFloat(pitra) * parseFloat(quat);
                            var taxa = $('.taxamount<?php echo $i; ?>').val();
                            $.ajax({
                              type: 'POST',
                              url: '<?php echo ADMIN_URL; ?>purchaseorder/gettax',
                              data: {
                                'fetch': taxa
                              },
                              success: function (data) {
                                var withtax = (data * (tcrate / 100)).toFixed(2);
                                var toamountnew = parseFloat(tcrate) + parseFloat(withtax);

                                $('.pitama<?php echo $i; ?>').val(tcrate.toFixed(2));
                                $('.totalamount<?php echo $i; ?>').val(toamountnew.toFixed(2));
                                $('.pitax<?php echo $i; ?>').val(withtax);
                                total();
                                total2();
                                total3();
                              },
                            });
                          });

                          $(".taxamount<?php echo $i; ?>").on('change', function () {
                            var taxa = $(this).val();
                            var pitra = $('.pitraa<?php echo $i; ?>').val();
                            var quat = $('.quntt<?php echo $i; ?>').val();
                            var tcrate = parseFloat(pitra) * parseFloat(quat);
                            tcrate = tcrate.toFixed(2);
                            var tocost = $('.pitama<?php echo $i; ?>').val();
                            var toamount = $('.totala').text();
                            var toamountnew;
                            var withtax;
                            $.ajax({
                              type: 'POST',
                              url: '<?php echo ADMIN_URL; ?>purchaseorder/gettax',
                              data: {
                                'fetch': taxa
                              },
                              success: function (data) {
                                var totalget = $('.totala').text();
                                withtax = data * (tcrate / 100);
                                withtax = withtax.toFixed(2);
                                toamountnew = parseFloat(tocost) + parseFloat(withtax);
                                $('.totalamount<?php echo $i; ?>').val(toamountnew.toFixed(2));
                                $('.pitax<?php echo $i; ?>').val(withtax);
                                $('.totaltax<?php echo $i; ?>').text(withtax);
                                total();
                                total2();
                              },
                            });
                          });
                        });
                      </script>

                      <script>
                        var totalAmt = 0;
                        //show single item  total amount after calculate tax 
                        function total() {
                          var totals = 0;
                          var $dataRows = $("#customers tr:not('.totalColumn, .titlerow , .titlerows')");
                          $dataRows.each(function () {
                            $(this).find('.newtamso').each(function (i) {
                              totals += parseFloat($(this).val());
                            });
                          });
                          $('.totala').html(totals.toFixed(2));
                          totalAmt = totals.toFixed(2);
                        }

                        var total_tax_Amt = 0;
                        function total2() {
                          var totals2 = 0;
                          var $dataRows = $("#customers tr:not('.totalColumn, .titlerow, .titlerows')");
                          $dataRows.each(function () {
                            $(this).find('.newtaxx').each(function (i) {
                              totals2 += parseFloat($(this).val());
                            });
                          });
                          $('.totala2').html(totals2.toFixed(2));
                          total_tax_Amt = totals2.toFixed(2);
                        }

                        function total3() {
                          var totals3 = 0;
                          var $dataRows = $("#customers tr:not('.totalColumn, .titlerow, .titlerows')");
                          $dataRows.each(function () {
                            $(this).find('.newtamo').each(function (i) {
                              totals3 += parseFloat($(this).val());
                            });
                          });
                          $('.totala1').html(totals3.toFixed(2));
                        }
                      </script>

                      <script>
                        $(document).ready(function () {

                          $(".delete-button").on("click", function () {
                            var id = $(this).data('id');
                            var deletedAmt = $(".totalamount" + id).val();
                            var deletedTaxAmt = $(".pitax" + id).val();
                            if (!isNaN(deletedAmt) && deletedAmt > 0) {
                              var finalAmt = totalAmt - deletedAmt;
                              var finalTaxAmt = total_tax_Amt - deletedTaxAmt;
                              totalAmt = finalAmt;
                              total_tax_Amt = finalTaxAmt;
                              $('.totala1').html(finalAmt.toFixed(2));
                              $('.totala').html(finalAmt.toFixed(2));
                              $('.totala2').html(finalTaxAmt.toFixed(2));
                            }
                            $(this).closest("tr").remove();
                          });
                        });

                        // Date 02-09-2023 
                        async function getInputValues() {
                          let tax_calculate = 0;
                          let TotalTaxAmt = 0;
                          let per_amt_tax = 0;
                          let TotalFinelFooterAmt = 0;
                          // Get the table element
                          var table = document.getElementById("product_containes");
                          var rows = table.getElementsByTagName("tr");
                          // alert(rows.length)
                          function fetchTaxValue(selectedValue) {
                            return new Promise((resolve, reject) => {
                              $.ajax({
                                type: 'POST',
                                url: '<?php echo ADMIN_URL; ?>purchaseorder/gettax',
                                data: {
                                  'fetch': selectedValue
                                },
                                success: function (data) {
                                  var tax_value = parseFloat(data);
                                  resolve(tax_value);
                                },
                                error: function (error) {
                                  reject(error);
                                }
                              });
                            });
                          }
                          async function processRow(i) {
                            let TitemQtyPrice = 0;
                            let taxTotalAmt = 0;
                            let totalAMT = 0;
                            var cells = rows[i].getElementsByTagName("td");
                            var itemQtyPrice = cells[6].querySelector("input[type='text']");
                            TitemQtyPrice = parseFloat(itemQtyPrice.value);

                            var selectElement = cells[7].querySelector('select');
                            var selectedValue = selectElement.value;
                            var texAmt = cells[8].querySelector("input[type='text']");
                            taxTotalAmt = parseFloat(texAmt.value);
                            var totalAmt = cells[9].querySelector("input[type='text']");
                            totalAMT = parseFloat(totalAmt.value);
                            try {
                              var tax_value = await fetchTaxValue(selectedValue);
                              if ($("#taxinclude").is(':checked')) {
                                tax_calculate = (TitemQtyPrice - (TitemQtyPrice * (100 / (100 + tax_value))));
                                per_amt_tax = TitemQtyPrice;

                              } else {
                                tax_calculate = (TitemQtyPrice * tax_value / 100);
                                per_amt_tax = TitemQtyPrice + tax_calculate;
                              }

                              totalAmt.value = parseFloat(per_amt_tax.toFixed(2));
                              texAmt.value = parseFloat(tax_calculate.toFixed(2));
                              TotalTaxAmt += parseFloat(tax_calculate.toFixed(2));
                              TotalFinelFooterAmt += parseFloat(per_amt_tax.toFixed(2));
                              $('.totala2').html(TotalTaxAmt.toFixed(2));
                              $('.totala').html(TotalFinelFooterAmt.toFixed(2));
                            } catch (error) {
                              console.error(error);
                            }
                          }

                          for (var i = 0; i < rows.length; i++) {
                            await processRow(i);
                          }

                          function deleteRow(row) {
                            row.remove();
                            if (document.querySelectorAll('table tr').lenght === 0) {
                              getInputValues(); // Recalculate values after row deletion
                            }
                          }
                        }
                        // Date 01-09-2023 
                        // this code is use to calculate tax
                        $("#taxinclude").on('change', function () {
                          var tax = parseFloat($('.taxamount<?php echo $i; ?>').val());
                          $.ajax({
                            type: 'POST',
                            url: '<?php echo ADMIN_URL; ?>purchaseorder/gettax',
                            data: {
                              'fetch': tax
                            },
                            success: function (data) {
                              tax_value = parseFloat(data);
                              getInputValues();
                            },
                          });
                        });
                      </script>
                      <?php
                    } ?>

                    <!-- Data from AJAX request will be populated here -->
                  </tbody>
                  <tfoot>
                    <tr class="titlerows" style="background-color: #c8c8c8;">
                      <td colspan="11" type="" style="font-weight:bold;font-size:16px;">
                        <?php echo $this->Form->input('item_name', array('class' => 'form-control secrh-retails', 'id' => 'indent', 'type' => 'text', 'label' => false, '', 'autofocus', 'autocomplete' => 'off', 'placeholder' => 'Enter Item Name')); ?>
                        <div id="test1UL" style="display:none;">
                          <ul></ul>
                        </div>
                      </td>
                    </tr>
                    <!-- <tr class="titlerows" style="background-color: #c8c8c8;">
                      <td colspan="9" type="" class="text-right" style="font-weight:bold;font-size:16px;">Tax Included
                      </td>
                      <td colspan="5">
                        <input type="checkbox" name="tax_cal" id="taxinclude" class="retail_idsss">
                      </td>
                    </tr> -->
                    <tr class="titlerow" style="background-color: #c8c8c8;">
                      <td colspan="6" class="text-right" style="font-weight:bold;font-size:16px;">Net Amount (&#x20b9;)
                      </td>
                      <td style="font-weight: bold; text-align: right;" class="totala1">
                        <?php echo sprintf('%.2f', $unit_price_total); ?>
                      </td>
                      <td></td>
                      <td style="font-weight: bold; text-align: right;" class="totala2">
                        <?php echo sprintf('%.2f', $totaltxxx); ?>
                      </td>
                      <td style="font-weight: bold; text-align: right;" class="totala">
                        <?php echo sprintf('%.2f', $total_amt_final + $totaltxxx); ?>
                      </td>
                      <td></td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>



            <div class="form-group">

              <div class="col-sm-7">
                <label for="inputEmail3" style="margin-bottom:10px;">Payment Term<strong
                    style="color:red;"></strong></label>
                <?php echo $this->Form->input('payment_term', array('class' => 'form-control', 'type' => 'textarea', 'label' => false, 'placeholder' => 'Enter Payment Terms', 'autofocus', 'autocomplete' => 'off', '')); ?>
              </div>
              <div class="col-sm-7">
                <label for="inputEmail3" style="margin-bottom:10px;">Remark<strong style="color:red;"></strong></label>
                <?php echo $this->Form->input('remark', array('class' => 'form-control', 'id' => 'remark', 'type' => 'textarea', 'label' => false, 'placeholder' => 'Remark', 'autofocus', 'autocomplete' => 'off', '')); ?>
              </div>

            </div>
          </div>
          <!-- /.box-body -->
          <div class="box-footer">
            <?php
            if (isset($location['id'])) {
              echo $this->Form->submit(
                'Update',
                array('class' => 'btn btn-info pull-right', 'id' => 'formsubmitbtn', 'title' => 'Update')
              );
            } else {
              echo $this->Form->submit(
                'Revised && Finalize',
                array('class' => 'btn btn-info pull-right', 'id' => 'formsubmitbtn', 'title' => 'Revised && Finalize')
              );
            }
            ?>
            <?php
            echo $this->Html->link('Back', [
              'action' => 'index'

            ], ['class' => 'btn btn-default']); ?>
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
  function cllbckretail(id, cid) {
    $('.secrh-retail').val(id);
    $('#retail_ids').val(cid);
    var retail_id = $('#retail_ids').val();
    $('#testULs').show();
    $('#ship_id').val('');
    $.ajax({
      type: 'POST',
      url: '<?php echo ADMIN_URL; ?>purchaseorder/getvendorshipaddressall',
      data: {
        'retail_id': retail_id
      },
      success: function (data) {
        $('#testULs ul').html(data);
      },
    });
    $.ajax({
      type: 'POST',
      url: '<?php echo ADMIN_URL; ?>purchaseorder/getvendorissue',
      data: {
        'retail_id': retail_id
      },
      success: function (data) {
        $('.issuewithvendor').html('');
        $('.issuewithvendor').html(data);
      },
    });
    $('#testUL').hide();
  }

  function cllbckretails(id, cids) {
    $('.ship-retail').val(id);
    $('#ship_id').val(cids);
    $('#testULs').hide();
  }

  $(function () {
    $('.secrh-retail').bind('keyup', function () {
      $('#ship_id').val('');
      $('.ship-retail').val('');
      var pos = $(this).val();
      $('#testUL').show();
      $('#retail_ids').val('');
      var count = pos.length;
      if (count > 0) {
        $.ajax({
          type: 'POST',
          url: '<?php echo ADMIN_URL; ?>purchaseorder/getvendorname',
          data: {
            'fetch': pos
          },
          success: function (data) {
            $('#testUL ul').html(data);
          },
        });
      } else {
        $('#testUL').hide();
      }
    });
  });
</script>



<script type="text/javascript">
  function testtt(retailID) {
    $.ajax({
      type: 'POST',
      url: '<?php echo ADMIN_URL; ?>purchaseorder/indentitems',
      data: {
        'fetch': retailID
      },
      success: function (data) {
        $(".ctpcontent").css("display", "block");
        $("#product_containes").append(data); // Append received data to tbody
      },
    });
  }

  //item name
  var previousIds = [];
  function cllbckretail0(id, cid, sid) {
    if (previousIds.includes(cid)) {
      $('.secrh-retails').val('');
      $('#test1UL').hide();
      alert('This Item Already added');
    } else {
    $('.secrh-retails').val(id);
    $('#retail_id').val(cid);
    $('.retail_idsss').val(cid);
    $('#test1UL').hide();
    testtt(cid);
    $.ajax({
      type: 'POST',
      url: '<?php echo ADMIN_URL; ?>Purchaseorder/getitemdetail',
      data: {
        'fetch': cid
      },
      success: function (data) {
        $('.secrh-retails').val('');
        $('.secrh-retails').prop('required', false);
      },
    });
  }
  previousIds.push(cid)
  }

  function deleteRow(id) {
    const elementToRemove = String(id);
    const newArray = previousIds.filter(element => element !== elementToRemove);
    previousIds = newArray;
  }

  //get item name
    $('.secrh-retails').bind('keyup', function () {
      var pos = $(this).val();
      var check = 0;
      $('#test1UL').show();
      $('#retail_id').val('');
      var count = pos.length;
      if (count > 0) {
        $.ajax({
          type: 'POST',
          url: '<?php echo ADMIN_URL; ?>Purchaseorder/getitemname',
          data: {
            'fetch': pos,
            'check': check
          },
          success: function (data) {
            $('#test1UL ul').html(data);

          },
        });
      } else {
        $('#test1UL').hide();
      }
    });
</script>

<script>
  $(document).ready(function () {
    $('#sevice_form').on('submit', function (e) {
      $("#formsubmitbtn").css("display", "none");
    });
  });
</script>

