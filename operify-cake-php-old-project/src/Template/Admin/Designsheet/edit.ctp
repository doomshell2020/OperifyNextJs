<style>
  .input_fields_wrap .form-control {
    margin-bottom: 15px;
  }

  .control-label {
    display: block;
    margin-top: 10px;
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

  #itemtestUL {
    position: relative;
  }

  #itemtestUL ul {
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

  #itemtestUL ul li {
    padding: 5px 8px;
    border: 1px solid lightgray;
  }

  #itemtestUL ul li a {
    color: black;
  }

  #contractUL {
    position: relative;
  }

  #contractUL ul {
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

  #contractUL ul li {
    padding: 5px 8px;
    border: 1px solid lightgray;
    margin-left: 0px !important;
  }

  #contractUL ul li a {
    color: black;
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
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Edit Design Sheet
      <?php
      // pr($item);die;
      ?>
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo SITE_URL; ?>admin/designsheet"><i class="fa fa-home"></i>Home</a></li>
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
            <h3 class="box-title"><i class="fa fa-plus-square" aria-hidden="true"></i>Edit New Design Sheet</h3>
          </div>
          <!-- /.box-header -->
          <!-- form start -->


          <?php echo $this->Form->create(
            $users,
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
            <div class="row">
              <div class="col-md-3">
                <label for="inputEmail3" class=" control-label" style="text-align: left !important">Design Sheet
                  No<strong style="color:red;">*</strong></label>
                <?php echo $this->Form->input('designsheetno', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'autofocus', 'value' => $desheet['designsheetno'], 'autocomplete' => 'off', 'required', 'readonly')); ?>
              </div>

              <div class="col-md-3">
                <label for="inputEmail3" class=" control-label" style="text-align: left !important">Contract
                  Name<strong style="color:red;">*</strong></label>
                <input type="hidden" name="contract_id" id="contrselectid"
                  value="<?php echo $desheet['contract_id'] ?>">
                <?php
                $contractname = $this->comman->findcontractname($desheet['contract_id']);
                echo $this->Form->input('contractname', array('class' => 'form-control secrhcontract', 'id' => 'contractnameid', 'type' => 'text', 'label' => false, 'value' => $contractname['title'], 'autofocus', 'autocomplete' => 'off', 'required', 'placeholder' => 'Enter Contract Name', 'readonly')); ?>
                <div id="contractUL" style="display:none;">
                  <ul></ul>
                </div>
                <div id="contractUL1" style="display:none;">
                  <ul>
                    <li
                      style="padding: 5px 8px;list-style:none;color: black;font-weight: bold;margin-left:-32px; border: 1px solid lightgray;">
                      No Record Found</li>
                  </ul>
                </div>
              </div>

              <?php
              $options = [];
              foreach ($finishitem as $value) {
                $options[] = ['value' => $value['id'], 'text' => $value['item_name']];
              }
              ?>


              <div class="col-md-3">
                <label for="inputEmail3" class="control-label" style="text-align: left !important">Finished
                  Product<strong style="color:red;">*</strong></label>
                <input type="hidden" name="item_id" value="<?php echo $desheet['item_id'] ?>">
                <?php
                $itemname = $this->Comman->getitemname($desheet['item_id']);
                echo $this->Form->input('item_name', array('class' => 'form-control secrhcontract', 'id' => 'contractnameid', 'type' => 'text', 'label' => false, 'value' => $itemname['item_name'], 'autofocus', 'autocomplete' => 'off', 'required', 'placeholder' => 'Enter Contract Name', 'readonly')); ?>
              </div>




              <?php // echo $this->Form->input('item_id', [
              // 'class' => 'form-control data_req',
              // 'type' => 'select',
              // 'label' => false,
              // 'empty' => '-- Select Finished Product--',
              // 'autofocus',
              // 'options' => $options,
              // 'value' => $desheet['item_id'],
              // 'autocomplete' => 'off',
              // 'id' => 'item_id_pro'
              // ]);  ?>





              <div class="col-md-3">
                <label for="inputEmail3" class=" control-label" style="text-align: left !important">Quantity(in
                  KM)<strong style="color:red;">*</strong></label>
                <?php echo $this->Form->input('quantity', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'placeholder' => 'Enter Quantity(in KM)', 'autofocus', 'autocomplete' => 'off', 'required', 'readonly', 'id' => 'kmquantity', 'value' => $desheet['quantity'], 'onkeypress' => 'return isNumberKey11(event)')); ?>
              </div>




              <script>
                $(document).ready(function () {
                  $('#fdatefrom').datepicker({
                    dateFormat: 'dd-mm-yy',
                    yearRange: '2018:2030',
                    changeMonth: true,
                    changeYear: true,
                    autoclose: true,
                    onSelect: function (date) {
                      var selectedDate = new Date(date);
                      var endDate = new Date(selectedDate);
                      endDate.setDate(selectedDate);
                    }
                  });
                });
              </script>

              <div class="col-md-3">
                <label for="inputEmail3" class=" control-label" style="text-align: left !important;">Date<strong
                    style="color:red;">*</strong></label>
                <?php echo $this->Form->input('datefrom', array('class' => 'form-control', 'id' => 'fdatefrom', 'readonly', 'placeholder' => 'Date', 'required', 'value' => date('d-m-Y', strtotime($desheet['datefrom'])), 'label' => false)); ?>
                <span id="start_date" style="color: red;"></span>
              </div>

              <div class="col-md-3">
                <label for="inputEmail3" class=" control-label" style="text-align: left !important;">Upload Design
                  Sheet
                  <strong style="color:red;"></strong></label>
                <?php echo $this->Form->input('design_sheet', array('class' => 'form-control', 'type' => 'file', 'label' => false, 'id' => 'filename', 'autofocus', 'autocomplete' => 'off')); ?>
                <strong style="color:red;font-size:12px;">PDF, JPG, JPEG or PNG files only</strong></label>
              </div>
              <div class="col-md-3">
                <label for="inputEmail3" class=" control-label" style="text-align: left !important;">R1
                  <?php echo $this->Form->input('r1', array('class' => 'form-control', 'type' => 'file', 'label' => false, 'id' => 'r1', 'autofocus', 'autocomplete' => 'off')); ?>
                  <strong style="color:red;font-size:12px;">PDF, JPG, JPEG or PNG files only</strong>
                </label>
              </div>

              <div class="col-md-3">
                <label for="inputEmail3" class=" control-label" style="text-align: left !important;">R2
                  <?php echo $this->Form->input('r2', array('class' => 'form-control', 'type' => 'file', 'label' => false, 'id' => 'r2', 'autofocus', 'autocomplete' => 'off')); ?>
                  <strong style="color:red;font-size:12px;">PDF, JPG, JPEG or PNG files only</strong>
                </label>
              </div>

              <div class="col-md-3">
                <label for="inputEmail3" class=" control-label" style="text-align: left !important;">R3
                  <?php echo $this->Form->input('r3', array('class' => 'form-control', 'type' => 'file', 'label' => false, 'id' => 'r3', 'autofocus', 'autocomplete' => 'off')); ?>
                  <strong style="color:red;font-size:12px;">PDF, JPG, JPEG or PNG files only</strong>
                </label>
              </div>

              <div class="col-md-3">
                <label for="inputEmail3" class=" control-label" style="text-align: left !important;">R4
                  <?php echo $this->Form->input('r4', array('class' => 'form-control', 'type' => 'file', 'label' => false, 'id' => 'r4', 'autofocus', 'autocomplete' => 'off')); ?>
                  <strong style="color:red;font-size:12px;">PDF, JPG, JPEG or PNG files only</strong>
                </label>
              </div>

              <div class="col-md-3">
                <label for="inputEmail3" class=" control-label" style="text-align: left !important;">R5
                  <?php echo $this->Form->input('r5', array('class' => 'form-control', 'type' => 'file', 'label' => false, 'id' => 'r5', 'autofocus', 'autocomplete' => 'off')); ?>
                  <strong style="color:red;font-size:12px;">PDF, JPG, JPEG or PNG files only</strong>
                </label>
              </div>
            </div>


            <div class="ctpcontent form-group" style="display:block">
              <div class="col-sm-12">
                <label for="inputEmail3" style="margin-bottom:10px;">Items</label>
                <table id="customers" width="100%">
                  <thead>
                    <tr class="totalColumn">
                      <!-- <th>Indent</th> -->
                      <th>Item</th>
                      <th></th>
                      <th>Qty(per KM)</th>
                      <th>Total Qty</th>
                      <th>UOM</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody id="product_containes">
                    <!-- Data from AJAX request will be populated here -->
                    <?php foreach ($product as $value) {
                      // pr($value);die;
                      $itemname = $this->Comman->getitemname($value['item_id']);
                      // pr($itemname);
                      $i = $value['item_id'];
                      ?>
                      <tr class="video_details" id="row-<?php echo $value['id']; ?>">
                        <td width="40%">
                          <?php echo $this->Form->input('pitemname11['.$i.']', array('class' => 'form-control',   'id' => 'pitemname' . $itemname['id'], 'type' => 'hidden', 'value' => $itemname['id'], 'label' => false, 'autofocus', 'autocomplete' => 'off', 'required')); ?>
                          <?php
                          echo $this->Form->input('name11['.$i.']', array('class' => 'form-control', 'id' => 'pitemname', 'type' => 'text', 'value' => $itemname['item_name'], 'label' => false, 'autofocus', 'autocomplete' => 'off', 'readonly'));
                          ?>
                          <small style="font-weight:bold;position: relative;"><span style="color:red;"></span></small>
                        </td>

                        <?php $checkindentpo = $this->Comman->checkindentpo($desheet['contract_id'], $desheet['item_id']); ?>
                        <td width="04%">
                        <input type="checkbox" name="is_group[<?php echo $i ?>]" value="1" class="checked"
                        <?php echo ($value['is_group'] == 1 ? 'checked' : '');?> 
                        <?php if(!empty($checkindentpo)) { echo 'disabled'; } ?> >
                        </td>
                        <td width="20%"><input type="text" onkeypress='return isNumberKey11(event)' readonly
                            name="pitemquantity11[<?php echo $i ?>]" required="required" value="<?php echo $value['km_item_qty']; ?>"
                            class="form-control newquan quntt<?php echo $value['item_id']; ?>" min="0"
                            id="perkmqty-<?php echo $value['item_id']; ?>" onkeyup="calculateqty(this)"
                            autocomplete='off'></td>

                        <td width="20%"><input type="text" onkeypress='return isNumberKey11(event)'
                            name="pitemquantity12[<?php echo $i ?>]" readonly required="required"
                            value="<?php echo $value['item_qty']; ?>"
                            class="form-control newquan quntt<?php echo $value['item_id']; ?>" min="0"
                            id="totalqty-<?php echo $value['item_id']; ?>" autocomplete='off'></td>

                        <td width="10%">
                          <?php
                          echo $this->Form->input('unit_name111['.$i.']', array('class' => 'form-control', 'type' => 'text', 'value' => ($value['uom']), 'label' => false, 'autofocus', 'autocomplete' => 'off', 'readonly')); ?>
                        </td>
                        <!-- this use for delete partculer tr -->
                        <td width="10%">
                        <?php  if (empty($checkindentpo)) { ?>
                          <span class="fas fa-trash-alt delete-button11" data-id="<?php echo $value['id']; ?>"
                            onclick="deleteRow(<?php echo $value['id']; ?>)"
                            style="font-size: 21px; color:#cd0404"></span>
                            <?php } ?> 
                        </td>
                      </tr>

                    <?php } ?>
                  </tbody>

                  <tfoot>
                    <tr class="titlerows" style="background-color: #c8c8c8;">
                      <td colspan="11" type="" style="font-weight:bold;font-size:16px;">
                        <?php echo $this->Form->input('item_name', array('class' => 'form-control secrh-retails', 'id' => 'indent', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'placeholder' => 'Enter Item Name')); ?>
                        <div id="test1UL" style="display:none;">
                          <ul></ul>
                        </div>
                      </td>
                    </tr>
                  </tfoot>


                </table>
              </div>
            </div>


            <div class="col-md-12 text-right mt-2">
              <?php
              echo $this->Form->submit(
                'Edit',
                array('class' => 'btn btn-info', 'id' => 'formsubmitbtn', 'title' => 'Edit')
              );
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
  function validateForm() {
    var valid = true;
    var fileName = document.forms["myForm"]["filename"].value;
    if (fileName != '') {
      var ext = fileName.substring(fileName.lastIndexOf('.') + 1);
      if (ext !== "pdf" && ext !== "jpg" && ext !== "jpeg" && ext !== "png") {
        alert("Upload PDF, JPG, JPEG or PNG files only");
        valid = false;
      }
    }
    for (var i = 1; i <= 5; i++) {
      var revised = document.forms["myForm"]["r" + i].value;
      if (revised != '') {
        var ext = revised.substring(revised.lastIndexOf('.') + 1);
        if (ext !== "pdf" && ext !== "jpg" && ext !== "jpeg" && ext !== "png") {
          alert("Upload PDF, JPG, JPEG or PNG files only");
          valid = false;
        }
      }

    }

    if (valid) {
      document.querySelectorAll('input[type="checkbox"].checked').forEach(function(checkbox) {
        checkbox.disabled = false;
      });

      $("#formsubmitbtn").css("display", "none");
    }
    return valid;
  }
</script>

<!-- <script>
  $(".delete-button11").on("click", function () {
    var id = $(this).attr("data-id");
    $.ajax({
      type: 'POST',
      url: '<?php echo ADMIN_URL; ?>designsheet/deletedata',
      data: {
        'fetch': id
      },

      success: function (data) {
        document.getElementById("row-" + id).remove();
      }
    });
  });
</script> -->

<script>
  function cllbckretail2(id, cid) {
    $('.secrhcontract').val(id);
    $('.contractfill').val(id);
    $('#contrselectid').val(cid);
    $('#contractUL').hide();
    $('#contractUL1').hide();
  }
  $(function () {
    $('.secrhcontract').bind('keyup', function () {
      var pos = $(this).val();
      var check = 2;
      $('#contractUL').show();
      $('#contrselectid').val('');
      var count = pos.length;
      if (count > 0) {
        $.ajax({
          type: 'POST',
          url: '<?php echo ADMIN_URL; ?>production/getcontract',
          data: {
            'fetch': pos,
            'check': check
          },
          success: function (data) {
            if (data) {
              console.log(data);
              $('#contractUL ul').html(data);
            } else {
              $('#contractUL').hide();
              $('#contractUL1').show();
            }
          },
        });
      } else {
        $('#contractUL').hide();
        $('#contractUL1').hide();
      }
    });
  });
</script>

<script>
  function isNumberKey11(evt) {
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


<script type="text/javascript">
  function testtt(retailID) {
    $.ajax({
      type: 'POST',
      url: '<?php echo ADMIN_URL; ?>designsheet/indentitems',
      data: {
        'fetch': retailID
      },
      success: function (data) {
        $(".ctpcontent").css("display", "block");
        $("#product_containes").append(data);
      },
    });
  }

  // var previousIds = [$itemname['id']];
  var previousIds = [];

<?php if (!empty($product)): ?>
    <?php foreach ($product as $value): ?>
        <?php $itemname = $this->Comman->getdesignsheetitemname($value['item_id'],$value['designsheet_id']); ?>
        <?php if (!empty($itemname['item_id'])): ?>
            previousIds.push(<?php echo json_encode($itemname['item_id']); ?>);
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>

var itemids = '<?php echo json_encode($rawitem); ?>';

function cllbckretail0(name, id) {
    // alert(previousIds);

    if (previousIds.includes(id)) {
        alert('This Item Already added');
        $('#test1UL').hide();
        $('.secrh-retails').val('');
        return; // Prevent adding duplicate item
    } 

    $('.secrh-retails').val(name);
    $('#retail_id').val(id);
    $('#test1UL').hide();
    testtt(id);
    $.ajax({
        type: 'POST',
        url: '<?php echo ADMIN_URL; ?>Purchaseorder/getitemdetail',
        data: {
            'fetch': id
        },
        success: function (data) {
            $('.secrh-retails').val('');
            $('.secrh-retails').prop('required', false);
        },
    });

    previousIds.push(id);
    return;
}




  function deleteRow(id) {
    $('#row-' + id).remove();

    $.ajax({
      type: 'POST',
      url: '<?php echo ADMIN_URL; ?>designsheet/deletedata',
      data: {
        'fetch': id
      },
    });

    const elementToRemove = String(id);
    const newArray = previousIds.filter(element => element !== elementToRemove);
    previousIds = newArray;
  }






  $(function () {
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
  });
</script>

<script>
  function calculateqty(inputId) {
    var id = inputId.id.split("-");
    var itemidno = id[1];

    var kmqty = $('#kmquantity').val();
    var perkmqty = inputId.value;
    var totalvalue = kmqty * perkmqty;
    document.getElementById(`totalqty-${itemidno}`).value = totalvalue.toFixed(2);
  }
</script>