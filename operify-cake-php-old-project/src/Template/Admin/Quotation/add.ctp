<style>
  #test1UL {
    position: relative;
  }

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

            <div class="form-group" style="margin-bottom:0px;">
              <div class="row">
                <!--Enter PO Number  -->
                <div class="col-sm-3">
                  <label for="inputEmail3" class=" control-label">Quotation No<strong style="color:red;">*</strong> </label>

                  <?php echo $this->Form->input('quotation_no', array('class' => 'form-control contactnum mobile', 'value' => $quotationNo, 'required', 'autocomplete' => 'off', 'id' => 'dup_mobile', 'placeholder' => 'Enter Quotation Number', 'label' => false, 'readonly'));
                  ?>
                  <div style="display: none;" id="msg">
                    <span style="color:red;">Po No Already Exits </span>
                  </div>
                </div>
                <script>
                  $(document).ready(function() {
                    $('#datepicker3').datepicker({
                      dateFormat: 'dd-mm-yy',
                      yearRange: '2018:2030',
                      changeMonth: true,
                      changeYear: true,
                      autoclose: true,
                      maxDate: new Date(),
                      onSelect: function(date) {
                        var selectedDate = new Date(date);
                        var endDate = new Date(selectedDate);
                        endDate.setDate(selectedDate);
                      }
                    });
                    $('#datepicker3').datepicker('setDate', 'today');
                  });
                </script>

                <div class="col-sm-3">
                  <label for="inputEmail3" class="">Generated Date <strong style="color:red;">*</strong></label>
                  <?php echo $this->Form->input('inwarddate', array('class' => 'form-control', 'id' => 'datepicker3', 'type' => 'text', 'readonly', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'required')); ?>
                </div>


                <div class="col-sm-3">
                  <label for="inputEmail3" class=" control-label">Expected Delivery Date<strong style="color:red;">*</strong></label>
                  <?php echo $this->Form->input('delivery_date', array('class' => 'form-control readonly', 'id' => 'datepicker1', 'type' => 'text', 'label' => false, 'placeholder' => 'Expected Delivery Date', 'autofocus', 'autocomplete' => 'off', 'required')); ?>
                </div>

                <div class="col-sm-3">
                  <label for="inputEmail3" class=" control-label">Quotation Acceptance Date<strongstyle="color:red;">*</strong></label>
                  <?php echo $this->Form->input('acceptance_date', array('class' => 'form-control', 'readonly', 'type' => 'text', 'label' => false,'placeholder' => 'Quotation Acceptance Date','required','id' => 'datepicker122')); ?>
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


          </div>
          <!-- /.box-body -->
          <div class="box-footer">
            <?php echo $this->Form->submit('Save & Finalize', array('class' => 'btn btn-info pull-right', 'id' => 'formsubmitbtn', 'title' => 'Save & Finalize'));
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
  $(document).ready(function() {
    $('#datepicker1').datepicker({
      dateFormat: 'dd-mm-yy',
    });
  });
</script>

<script>
  $(document).ready(function() {
    $('#datepicker122').datepicker({
      dateFormat: 'dd-mm-yy',
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
<script type="text/javascript">
  function testtt(retailID) {

    $.ajax({
      type: 'POST',
      url: '<?php echo ADMIN_URL; ?>purchaseorder/indentitems',
      data: {
        'fetch': retailID
      },
      success: function(data) {
        $(".ctpcontent").css("display", "block");
        $("#product_containes").append(data);
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
      // $('.retail_idsss').val(cid);
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
      previousIds.push(cid)
    }

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

  function deleteRow(id) {
    const elementToRemove = String(id);
    const newArray = previousIds.filter(element => element !== elementToRemove);
    previousIds = newArray;
  }
</script>



<script>
  $(document).ready(function() {
    $("#dup_mobile").on('change', function() {
      var stval = $(this).val();
      $.ajax({
        type: 'POST',
        url: '<?php echo ADMIN_URL; ?>Purchaseorder/checkpono',
        data: {
          'po_ids': stval
        },
        success: function(data) {
          var obj = JSON.parse(data);
          if (obj) {
            $('#msg').css('display', 'block');
            $(".contactnum ").val('');
          } else {
            $('#msg').css('display', 'none');
          }
        },
      });
    });
  });
</script>

<script>
  function validateForm() {
    var valid = true;
    $('.newpitemrate').each(function() {
      var pricevalue = (this.value);
      if (pricevalue == 0) {
        alert('Unit price can not be equal 0');
        valid = false;
      }
    })
    if (valid) {
      $("#formsubmitbtn").css("display", "none");
    }
    return valid;
  }
</script>

<script>
  $('.addsupplier_modal').click(function(e) {
    e.preventDefault();
    $('#cancelsorts').modal('show').find('.modal-body').load($(this).attr('href'));
  });
</script>



<div class="modal fade" id="cancelsorts">
  <div class="modal-dialog" style="max-width:999px !important;">
    <div class="modal-content">
      <div class="modal-body"></div>
    </div>
  </div>
</div>

<script>
  $('.viewgrndetails').click(function(e) {
    e.preventDefault();
    $('#editsortsgrn').modal('show').find('.modal-body').load($(this).attr('href'));
  });
</script>