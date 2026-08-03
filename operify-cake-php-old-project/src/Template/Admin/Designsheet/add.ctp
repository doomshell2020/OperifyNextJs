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
      Add Design Sheet
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
            <h3 class="box-title"><i class="fa fa-plus-square" aria-hidden="true"></i>Create New Design Sheet</h3>
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
                <?php echo $this->Form->input('designsheetno', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'autofocus', 'value' => $newdesignsheetno, 'autocomplete' => 'off', 'required', 'readonly')); ?>
              </div>

              <div class="col-md-3">
                <label for="inputEmail3" class=" control-label" style="text-align: left !important">Contract
                  Name<strong style="color:red;">*</strong></label>
                <input type="hidden" name="contract_id" id="contrselectid">
                <?php
                echo $this->Form->input('contractname', array('class' => 'form-control secrhcontract', 'id' => 'contractnameid', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'required', 'placeholder' => 'Enter Contract Name')); ?>
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
                <span id="contract_id" style="color: red;font-size:12px;"></span>

              </div>




              <div class="col-md-3">
                <label for="inputEmail3" class="control-label" style="text-align: left !important">Finished
                  Product<strong style="color:red;">*</strong></label>
                <?php echo $this->Form->input('item_id', [
                  'class' => 'form-control data_req',
                  'type' => 'select',
                  'label' => false,
                  'empty' => '-- Select Finished Product--',
                  'autofocus',
                  'required',
                  'autocomplete' => 'off',
                  'id' => 'item_id_pro'
                ]); ?>

                <div style="display: none;" id="msg">
                  <span style="color:red;">This Product Already Exits </span>
                </div>
              </div>


              <div class="col-md-3">
                <label for="inputEmail3" class=" control-label" style="text-align: left !important">Quantity(in
                  KM)<strong style="color:red;">*</strong></label>
                <?php echo $this->Form->input('quantity', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'placeholder' => 'Enter Quantity(in KM)', 'autofocus', 'readonly', 'autocomplete' => 'off', 'required', 'id' => 'kmquantity', 'onkeypress' => 'return isNumberKey11(event)')); ?>
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
                  $('#fdatefrom').datepicker('setDate', 'today');
                });
              </script>

              <div class="col-md-3">
                <label for="inputEmail3" class=" control-label" style="text-align: left !important;">Date<strong
                    style="color:red;">*</strong></label>
                <?php echo $this->Form->input('datefrom', array('class' => 'form-control', 'id' => 'fdatefrom', 'readonly', 'placeholder' => 'Date', 'required', 'label' => false)); ?>
                <span id="start_date" style="color: red;"></span>
              </div>

              <div class="col-md-3">
                <label for="inputEmail3" class=" control-label" style="text-align: left !important;">Upload Design Sheet
                  <strong style="color:red;">*</strong></label>
                <?php echo $this->Form->input('design_sheet', array('class' => 'form-control', 'type' => 'file', 'required', 'id' => 'filename', 'label' => false, 'placeholder' => 'Enter Work Order', 'autofocus', 'autocomplete' => 'off')); ?>
                <strong style="color:red;font-size:12px;">PDF, JPG, JPEG or PNG files only</strong></label>
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
                    
                  </tbody>
                  <tfoot>
                    <tr class="titlerows" style="background-color: #c8c8c8;">
                      <td colspan="11" type="" style="font-weight:bold;font-size:16px;">
                        <?php echo $this->Form->input('item_name', array('class' => 'form-control secrh-retails', 'id' => 'indent', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'required', 'placeholder' => 'Enter Item Name')); ?>
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
                'Add',
                array('class' => 'btn btn-info', 'id' => 'formsubmitbtn', 'title' => 'Add')
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

    var Vendorid = document.forms["myForm"]["contract_id"].value;
    if (Vendorid === "") {
      document.getElementById("contract_id").innerHTML = "Your entered contract does not exists";
      valid = false;
    }

    var fileName = document.forms["myForm"]["filename"].value;
    var ext = fileName.substring(fileName.lastIndexOf('.') + 1);
    if (ext !== "pdf" && ext !== "jpg" && ext !== "jpeg" && ext !== "png") {
      alert("Upload PDF, JPG, JPEG or PNG files only");
      valid = false;
    }
    if (valid) {
      $("#formsubmitbtn").css("display", "none");
    }
    return valid;
  }
</script>


<script>
  function cllbckretail2(id, cid) {
    $('.secrhcontract').val(id);
    $('#contrselectid').val(cid);
    getbomfinshedproduct(cid);
    $('#contractUL').hide();
    $('#contractUL1').hide();
  }

    $('.secrhcontract').on('keyup', function () {
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

</script>

<script>
  function getbomfinshedproduct(contractid) {
    if (contractid) {
      $.ajax({
        type: 'POST',
        url: '<?php echo ADMIN_URL; ?>designsheet/getbomfinshedproduct',
        data: {
          'contractid': contractid,
        },

        dataType: 'json',
        success: function (data) {
          var select = $("#item_id_pro");
          select.empty();
          select.append($('<option>', {
            value: '',
            text: '--Select Finished Product--'
          }));
          data.item.forEach(function (item) {
            select.append($('<option>', {
              value: item.id,
              text: item.item_name,
            }));
          });

        },


      })
    }
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

  var previousIds = [];
  function cllbckretail0(name, id) {
    if (previousIds.includes(id)) {
      alert('This Item Already added');
      $('#test1UL').hide();
      $('.secrh-retails').val('');
    } else {
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
    }
    return;
  }

  function deleteRow(id) {
  $('#row-' + id).remove();
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


<script>
  $(document).ready(function () {
    $("#item_id_pro").on('change', function () {
      var itemid = $(this).val();
      var contractid = $('#contrselectid').val();
      $.ajax({
        type: 'POST',
        url: '<?php echo ADMIN_URL; ?>Designsheet/checkdesignsheetitem',
        data: {
          'itemid': itemid,
          'contractid': contractid
        },

        dataType: 'json',
        success: function (data) {
          $('#kmquantity').val(data.itemqty);

          if (data.checkdesign) {
            $('#msg').css('display', 'block');
            $("#item_id_pro ").val('');
            $("#kmquantity ").val('');
          } else {
            $('#msg').css('display', 'none');
          }
        },
      });
    });
  });
</script>