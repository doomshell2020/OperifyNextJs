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

  #testUL {
    position: relative;
  }

  #testUL ul {
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

  #testUL ul li {
    padding: 5px 8px;
    border: 1px solid lightgray;
  }

  #testUL ul li a {
    color: black;
  }

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
</style>
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Add Contract
      <?php
      // pr($item);die;
      ?>
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo SITE_URL; ?>admin/contracts"><i class="fa fa-home"></i>Home</a></li>
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
            <h3 class="box-title"><i class="fa fa-plus-square" aria-hidden="true"></i>Create New Contract</h3>
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
                <label for="inputEmail3" class=" control-label" style="text-align: left !important;">Supplier Name
                  <strong style="color:red;">*</strong></label>
                <input type="hidden" required="required" name="vendor_id" id="retail_ids">
                <?php echo $this->Form->input('supplier_id', array('class' => 'form-control secrh-retail', 'id' => 'itemname', 'type' => 'text', 'required', 'label' => false, 'placeholder' => 'Enter Supplier Name', 'autofocus', 'autocomplete' => 'off')); ?>
                <div id="testUL" style="display:none;">
                  <ul></ul>
                </div>
                <div id="testUL1" style="display:none;">
                  <ul>
                    <li
                      style="padding: 5px 8px;list-style:none;color: black;font-weight: bold;margin-left:-32px; border: 1px solid lightgray;">
                      No Record Found</li>
                  </ul>
                </div>
                <span id="vendor_id" style="color: red;font-size:12px;"></span>

              </div>
              <div class="col-md-3">
                <label for="inputEmail3" class=" control-label" style="text-align: left !important;">Title <strong
                    style="color:red;">*</strong></label>
                <?php echo $this->Form->input('title', array('class' => 'form-control', 'type' => 'text', 'required', 'label' => false, 'placeholder' => 'Enter Title', 'autofocus', 'autocomplete' => 'off')); ?>
              </div>
              <div class="col-md-3">
                <label for="inputEmail3" class=" control-label" style="text-align: left !important;">Work Order <strong
                    style="color:red;">*</strong></label>
                <?php echo $this->Form->input('workorder', array('class' => 'form-control', 'type' => 'text', 'required', 'label' => false, 'placeholder' => 'Enter Work Order', 'autofocus', 'autocomplete' => 'off')); ?>
              </div>
              <script>
                $(document).ready(function () {
                  $('#fissuedate').datepicker({
                    dateFormat: 'dd-mm-yy',
                    yearRange: '2018:2030',
                    defaultDate: 'today',
                    changeMonth: true,
                    changeYear: true,
                  });

                });
              </script>
              <div class="col-md-3">
                <label for="inputEmail3" class=" control-label" style="text-align: left !important;">Issue Date<strong
                    style="color:red;">*</strong></label>
                <?php echo $this->Form->input('issuedate', array('class' => 'form-control', 'id' => 'fissuedate', 'readonly', 'placeholder' => 'Date From', 'required', 'label' => false)); ?>
                <span id="issue_date" style="color: red;font-size:12px;"></span>
              </div>
              <div class="col-md-3">
                <label for="inputEmail3" class=" control-label" style="text-align: left !important;">Cost <strong
                    style="color:red;">*</strong></label>
                <?php echo $this->Form->input('cost', array('class' => 'form-control', 'type' => 'text', 'required', 'label' => false, 'placeholder' => 'Enter Cost', 'autofocus', 'autocomplete' => 'off', 'onkeypress' => 'return isNumberKey(event)')); ?>
              </div>
              <div class="col-md-3">
                <label for="inputEmail3" class=" control-label" style="text-align: left !important;">Operational Cost
                  <strong style="color:red;">*</strong></label>
                <?php echo $this->Form->input('operation_cost', array('class' => 'form-control', 'type' => 'text', 'required', 'label' => false, 'placeholder' => 'Enter Operational Cost', 'autofocus', 'autocomplete' => 'off', 'onkeypress' => 'return isNumberKey(event)')); ?>
              </div>
              <div class="col-md-3">
                <label for="inputEmail3" class=" control-label" style="text-align: left !important;">Labour Cost <strong
                    style="color:red;">*</strong></label>
                <?php echo $this->Form->input('labour_cost', array('class' => 'form-control', 'type' => 'text', 'required', 'label' => false, 'placeholder' => 'Enter Labour Cost', 'autofocus', 'autocomplete' => 'off', 'onkeypress' => 'return isNumberKey(event)')); ?>
              </div>
              <script>
                $(document).ready(function () {
                  $('#fdatefrom').datepicker({
                    dateFormat: 'dd-mm-yy',
                    yearRange: '2018:2030',
                    defaultDate: 'today',
                    changeMonth: true,
                    changeYear: true,
                  });
                  $('#fendfrom').datepicker({
                    dateFormat: 'dd-mm-yy',
                    yearRange: '2018:2030',
                    changeMonth: true,
                    changeYear: true,
                  });
                });
              </script>

              <div class="col-md-3">
                <label for="inputEmail3" class=" control-label" style="text-align: left !important;">Start Date<strong
                    style="color:red;">*</strong></label>
                <?php echo $this->Form->input('contract_start_date', array('class' => 'form-control', 'id' => 'fdatefrom', 'readonly', 'placeholder' => 'Date From', 'required', 'label' => false)); ?>
                <span id="start_date" style="color: red;font-size:12px;"></span>
              </div>

              <div class="col-md-3">
                <label for="inputEmail3" class=" control-label" style="text-align: left !important;">End Date<strong
                    style="color:red;">*</strong></label>
                <?php echo $this->Form->input('contract_end_date', array('class' => 'form-control', 'id' => 'fendfrom', 'readonly', 'placeholder' => 'Date To', 'required', 'label' => false)); ?>
                <span id="end_date" style="color: red;font-size:12px;"></span>
              </div>


              <div class="col-md-12" style="margin-top:5px;">

                <div class="" id="finished_products">
                  <table class="table table-bordered table-striped" id="customFields">
                    <thead>
                      <tr>
                        <th>Finished Product</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody id="product_containes">
                      <!-- Data from AJAX request will be populated here -->

                    </tbody>
                    <tfoot>
                      <tr>
                        <td>
                          <input type="hidden" required="required" name="item_id" id="retail_id">
                          <?php echo $this->Form->input('item_name', array('class' => 'form-control secrh-retails', 'id' => 'indent', 'type' => 'text', 'required', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'placeholder' => 'Enter Item Name')); ?>
                          <div id="test1UL" style="display:none;">
                            <ul></ul>
                          </div>
                        </td>
                      </tr>
                    </tfoot>

                  </table>
                </div>


              </div>



              <div class="col-md-12">
                <label for="inputEmail3" class=" control-label" style="text-align: left !important;">Description<strong
                    style="color:red;"></strong></label>
                <?php echo $this->Form->textarea('description', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'placeholder' => 'Enter Description', 'autofocus', '', 'autocomplete' => 'off')); ?>
              </div>
              <div class="col-md-12 text-right mt-2">
                <?php
                echo $this->Form->submit(
                  'Add',
                  array('class' => 'btn btn-info', 'title' => 'Add', 'id' => 'formsubmitbtn')
                );
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
<script>
  function cllbckretail(name, id) {
    $('.secrh-retail').val(name);
    $('#retail_ids').val(id);
    $('#testUL').hide();
    $('#testUL1').hide();
  }

  $(document).ready(function () {
    $('.secrh-retail').bind('keyup', function () {
      var pos = $(this).val();
      $('#testUL').show();
      $('#retail_ids').val('');
      var count = pos.length;
      if (count > 0) {
        $.ajax({
          type: 'POST',
          url: '<?php echo ADMIN_URL; ?>vendors/getname',
          data: {
            'fetch': pos,
          },

          success: function (data) {
            if (data) {
              console.log(data);
              $('#testUL ul').html(data);
            } else {
              $('#testUL').hide();
              $('#testUL1').show();
            }
          },
        });
      } else {
        $('#testUL').hide();
        $('#testUL1').hide();
      }
    });
  });
</script>

<script>
  function validateForm() {
    var valid = true;

    var Idate = document.forms["myForm"]["issuedate"].value;
    if (Idate === "") {
      document.getElementById("issue_date").innerHTML = "Please select date";
      valid = false;
    }

    var Sdate = document.forms["myForm"]["contract_start_date"].value;
    if (Sdate === "") {
      document.getElementById("start_date").innerHTML = "Please select date";
      valid = false;
    }

    var Edate = document.forms["myForm"]["contract_end_date"].value;
    if (Edate === "") {
      document.getElementById("end_date").innerHTML = "Please select date";
      valid = false;
    }
    var Vendorid = document.forms["myForm"]["vendor_id"].value;
    if (Vendorid === "") {
      document.getElementById("vendor_id").innerHTML = "Your entered supplier does not exists";
      valid = false;
    }

    if (valid) {
      $("#formsubmitbtn").css("display", "none");
    }
    return valid;
  }
</script>


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

<script type="text/javascript">

  function testtt(retailID) {
    $.ajax({
      type: 'POST',
      url: '<?php echo ADMIN_URL; ?>production/getitemsname',
      data: {
        'fetch': retailID
      },
      success: function (data) {
        $("#product_containes").append(data); // Append received data to tbody
      },
    });
  }

  //item name
  var previousIds = [];
  function cllbckretail0(name, id) {
    if (previousIds.includes(id)) {
      alert('This Item Already added');
      $('#test1UL').hide();
      $('.secrh-retails').val('');
    }else{
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
  }


  function deleteRow(id) {
  $('#row-' + id).remove();
  const elementToRemove = String(id); 
  const newArray = previousIds.filter(element => element !== elementToRemove);
  previousIds = newArray; 
}

  //get item name
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
          url: '<?php echo ADMIN_URL; ?>Purchaseorder/getfinisheditemname',
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