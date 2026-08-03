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
</style>for
      Purchase Order Manager
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
            <h3 class="box-title"><i class="fa fa-plus-square" aria-hidden="true"></i> <?php if (isset($location['id'])) {
                                                                                          echo 'Edit Post New';
                                                                                        } else {
                                                                                          echo 'Generate Purchase Order id : ' . $newpurchaseordertemp;
                                                                                        } ?></h3>
          </div>
          <!-- /.box-header -->
          <!-- form start -->
          <?php echo $this->Form->create($location, array(
            'class' => 'form-horizontal',
            'enctype' => 'multipart/form-data',
            'validate'
          )); ?>
          <input type="hidden" name="token" value=<?php echo uniqid(); ?>>
          <div class="box-body">
            <?php echo $this->Form->input('purchaseorder_id', array('class' => 'form-control', 'id' => 'purchaseorder', 'type' => 'hidden', 'value' => $newpurchaseordertemp, 'readonly', 'label' => false, 'placeholder' => 'purchaseorder id', 'autofocus', 'autocomplete' => 'off')); ?>
            <div class="form-group" style="margin-bottom:0px;">
              <div class="row">
                <div class="col-sm-4">
                  <label for="inputEmail3" class=" control-label">Supplier <strong style="color:red;">*</strong></label>
                  <input type="hidden" name="vendor_id" id="retail_ids">
                  <?php echo $this->Form->input('vendorname', array('class' => 'form-control secrh-retail', 'id' => 'supplier', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'required', 'placeholder' => 'Enter Supplier Name')); ?>
                  <div id="testUL">
                    <ul></ul>
                  </div>
                </div>
                <script>
                  $(document).ready(function() {
                    $(".readonly").on('keyup', function(e) {
                      $(".readonly").val('');
                    });
                  });
                </script>
                <div class="col-sm-4">
                  <label for="inputEmail3" class=" control-label">Expected Delivery Date<strong style="color:red;">*</strong></label>
                  <?php echo $this->Form->input('delivery_date', array('class' => 'form-control readonly', 'id' => 'datepicker1', 'type' => 'text', 'label' => false, 'placeholder' => 'Expected Delivery Date', 'autofocus', 'autocomplete' => 'off', 'required' => 'required')); ?>
                </div>
                <!-- </div> -->
                <!-- <div class="form-group">  -->
                <div class="col-md-4">
                  <label for="inputEmail3" class=" control-label">Contract</label>
                  <?php echo $this->Form->input('contract', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'placeholder' => 'Enter Contract Name')); ?>
                </div>
                <div class="col-md-4">
                  <label for="inputEmail3" class=" control-label">Project</label>
                  <?php echo $this->Form->input('project', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'placeholder' => 'Enter Project Name')); ?>
                </div>
              </div>

            </div>
            <?php foreach ($indent_data as $val) { ///pr($val);
              $qty = $val['quantity'];
              $unit_price_total += $val['sale_price'];
              $total_amt_base  = $val['sale_price'] * $qty;
              $total_amt_final += $val['sale_price'] * $qty;

              $total_tax_base  =  $total_amt_base * $val['additem']['taxmaster']['tax'] / 100;
              $totaltxxx += $total_tax_base;
            } ?>


            <div class="ctpcontent form-group" style="display:block">
              <div class="col-sm-12">
                <label for="inputEmail3" style="margin-bottom:10px;">Items</label>
                <table id="customers">
                  <thead>
                    <tr class="totalColumn">
                      <!-- <th>Indent</th> -->
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
                      for </tr>
                  </thead>





                  <tbody id="product_containes">
                    <!-- Data from AJAX request will be populated here -->
                  </tbody>


                  <tfoot>
                    <tr class="titlerows" style="background-color: #c8c8c8;">
                      <td colspan="11" type="" style="font-weight:bold;font-size:16px;">
                        <?php echo $this->Form->input('item_name', array('class' => 'form-control secrh-retails', 'id' => 'indent', 'type' => 'text', 'label' => false, 'required', 'autofocus', 'autocomplete' => 'off', 'placeholder' => 'Enter Item Name')); ?>
                        <div id="test1UL" style="display:none;">
                          <ul></ul>
                        </div>
                      </td>
                    </tr>



                    <tr class="titlerows" style="background-color: #c8c8c8;">
                      <td colspan="9" type="" class="text-right" style="font-weight:bold;font-size:16px;">Tax Included</td>
                      <td colspan="5">
                        <input type="checkbox" name="tax_cal" id="taxinclude" class="retail_idsss">
                      </td>
                    </tr>
                    <tr class="titlerow" style="background-color: #c8c8c8;">
                      <td colspan="6" class="text-right" style="font-weight:bold;font-size:16px;">Net Amount (&#x20b9;)</td>
                      <td style="font-weight: bold; text-align: right;" class="totala1"><?php echo sprintf('%.2f', $unit_price_total); ?> </td>
                      <td></td>
                      <td style="font-weight: bold; text-align: right;" class="totala2"><?php echo sprintf('%.2f', $totaltxxx); ?> </td>
                      <td style="font-weight: bold; text-align: right;" class="totala"><?php echo sprintf('%.2f', $total_amt_final + $totaltxxx); ?> </td>
                      <td></td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
            <div class="form-group">

            <div class="col-sm-6">
                <label for="inputEmail3" style="margin-bottom:10px;">Payment Term<strong style="color:red;">*</strong></label>
                <?php echo $this->Form->input('payment_term', array('class' => 'form-control', 'type' => 'textarea', 'label' => false, 'placeholder' => 'Enter Payment Terms', 'autofocus', 'autocomplete' => 'off', 'required')); ?>
              </div>
              <div class="col-sm-6">
                <label for="inputEmail3" style="margin-bottom:10px;">Remark<strong style="color:red;">*</strong></label>
                <?php echo $this->Form->input('remark', array('class' => 'form-control', 'id' => 'remark', 'type' => 'textarea', 'label' => false, 'placeholder' => 'Remark', 'autofocus', 'autocomplete' => 'off', 'required')); ?>
              </div>

            </div>
          </div>
          <!-- /.box-body -->
          <div class="box-footer">
            <?php
            if (isset($location['id'])) {
              echo $this->Form->submit(
                'Update',
                array('class' => 'btn btn-info pull-right', 'title' => 'Update')
              );
            } else {
              echo $this->Form->submit(
                'Save & Finalize',
                array('class' => 'btn btn-info pull-right', 'title' => 'Save & Finalize')
              );
            }
            ?><?php
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
  $(document).ready(function() {
    $('#datepicker1').datepicker({
      dateFormat: 'dd-mm-yy',
      yearRange: '2018:2025',
      minDate: new Date(),
      onSelect: function(date) {
        var selectedDate = new Date(date);
        var endDate = new Date(selectedDate);
        endDate.setDate(endDate.getDate());
        $("#datepicker2").datepicker("option", "minDate", endDate);
        $("#datepicker2").val(date);
      }
    });
    $('#datepicker2').datepicker({
      dateFormat: 'yy-mm-dd'
    });
  });
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
      success: function(data) {
        $('#testULs ul').html(data);
      },
    });
    $.ajax({
      type: 'POST',
      url: '<?php echo ADMIN_URL; ?>purchaseorder/getvendorissue',
      data: {
        'retail_id': retail_id
      },
      success: function(data) {
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

  $(function() {
    $('.secrh-retail').bind('keyup', function() {
      $('#ship_id').val('');
      $('.ship-retail').val('');
      var pos = $(this).val();
      //alert(pos);
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
          success: function(data) {
            $('#testUL ul').html(data);
          },
        });
      } else {
        $('#testUL').hide();
      }
    });
  });
</script>


for



<script type="text/javascript">
  function testtt(retailID) {

    $.ajax({
      type: 'POST',
      url: '<?php echo ADMIN_URL; ?>purchaseorder/indentitems',
      data: {
        'fetch': retailID
      },
      success: function(data) {
        // console.log(data);
        $("#product_containes").append(data); // Append received data to tbody
      },
    });
  }



  //item name
  function cllbckretail0(id, cid, sid) {
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

      success: function(data) {
        $('.secrh-retails').val('');
        $('.secrh-retails').prop('required', false);
      },
    });
  }
  //get item name
  $(function() {
    $('.secrh-retails').bind('keyup', function() {
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
          success: function(data) {
            $('#test1UL ul').html(data);

          },
        });
      } else {
        $('#test1UL').hide();
      }
    });
  });
</script>