<style>
  #poidUL {
    position: relative;
  }
  .control-label {
    display: block;
    margin-top: 10px;
  }
  #poidUL ul {
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
</style>

<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Reverse Manager
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo SITE_URL; ?>admin/reverseindent"><i class="fa fa-home"></i>Home</a></li>
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
              <?php if (isset($location['id'])) {
                echo 'Edit Post New';
              } else {
                echo 'Reverse id : I-' . $newindentid;
              } ?>
            </h3>
          </div>
          <!-- /.box-header -->
          <!-- form start -->
          <?php echo $this->Form->create(
            $location,
            array(
              'class' => 'form-horizontal',
              'enctype' => 'multipart/form-data',
              'id' => 'sevice_form',
              'validate'
            )
          );

          // pr($reverseindentid); ?>
          <input type="hidden" name="token" value=<?php echo uniqid(); ?>>
          <div class="box-body">

            <div class="form-group" style="margin-bottom:0px;">
              <div class="row">
                <div class="col-md-3">
                  <label for="inputEmail3" class=" control-label" style="text-align: left !important">
                    Reverse Id No.<strong style="color:red;">*</strong></label>
                  <?php echo $this->Form->input('reverse_id', array('class' => 'form-control', 'id' => 'purchaseorder', 'type' => 'text', 'value' => $reverseindentid['reverse_id'], 'readonly', 'label' => false, 'placeholder' => 'Reverse id', 'autofocus', 'autocomplete' => 'off')); ?>
                </div>

                <script>
                  $(document).ready(function () {
                    $('#datepicker3').datepicker({
                      dateFormat: 'dd-mm-yy',
                      yearRange: '2018:2025',
                      minDate: '18-03-2024',
                      maxDate: new Date(),
                    });
                    $('#datepicker3').datepicker('setDate', new Date());
                  });
                </script>
                <div class="col-sm-3" style="margin-bottom:15px;">
                  <label for="inputEmail3" class="">Issued Date <strong style="color:red;">*</strong></label>
                  <?php echo $this->Form->input('issue_date', array('class' => 'form-control', 'id' => 'datepicker3','value' =>date('d-m-Y', strtotime($reverseindentid['issue_date'])), 'type' => 'text', '', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'required')); ?>
                </div>

                <div class="col-sm-3">
                  <label for="inputEmail3" class=" control-label" style="text-align: left !important">Contract
                    Name<strong style="color:red;">*</strong></label>

                  <input type="hidden" name="contract_id" id="contrselectid" required
                    value="<?php $reverseindentid['contract_id']; ?>">

                  <?php
                  $contractname = $this->comman->findcontractname($reverseindentid['contract_id']);

                  echo $this->Form->input('contractname', array('class' => 'form-control secrhcontract', 'id' => 'contractnameid', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'readonly', 'required', 'value' => $contractname['title'], 'placeholder' => 'Enter Contract Name')); ?>
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


                <?php $itemname = $this->comman->getitemname($reverseindentid['finishedproduct_id']); ?>
                <div class="col-md-3">
                  <?php echo $this->Form->input('finisheditem_id', array('class' => 'form-control', 'type' => 'hidden', 'value' => $reverseindentid['finishedproduct_id'], 'label' => false, 'autofocus', 'autocomplete' => 'off')); ?>

                  <label for="inputEmail3" class="control-label" style="text-align: left !important">Product<strong
                      style="color:red;">*</strong></label>
                  <?php echo $this->Form->input('finisheditemname', [
                    'class' => 'form-control data_req',
                    'type' => 'text',
                    'label' => false,
                    'autofocus',
                    'readonly',
                    'autocomplete' => 'off',
                    'value' => $itemname['item_name'],
                    'id' => 'item_id_pro'
                  ]); ?>
                </div>


                <div class="col-md-3">
                  <label for="inputEmail3" class=" control-label" style="text-align: left !important">Machine
                    Name<strong style="color:red;">*</strong></label>
                  <input type="hidden" name="machines_id" id="retail_ids" value="<?php echo $machinename['id'] ?>">
                  <?php echo $this->Form->input('machine_id', array('class' => 'form-control secrh-retail', 'id' => 'itemname', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'placeholder' => 'Enter Machine Name','value' => $machinename['machine_name'])); ?>
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
                </div>

                <div class="col-md-3">
                  <label for="inputEmail3" class=" control-label" style="text-align: left !important">
                  Received By<strong style="color:red;">*</strong></label>
                  <?php echo $this->Form->input('received_name', array('class' => 'form-control itemqty', 'type' => 'text', 'label' => false, 'required', 'placeholder' => 'Enter Name', 'autofocus', 'autocomplete' => 'off', '', 'value' => $reverseindentid['received_name'], 'required')); ?>
                </div>


              </div>
            </div>



            <div class="ctpcontent form-group" style="display:block">
              <div class="col-sm-12">
                <label for="inputEmail3" style="margin-bottom:10px;">Items</label>
                <table id="customers">
                  <tbody id="product_containes">

                    <tr class="totalColumn" style="background-color: #e0e0e0;">
                      <th colspan="4">Semi-Finished Product</th>
                    </tr>

                    <?php 
                      // Get received quantity for this product (Stock IN)
                      $received = $this->comman->find_first_query("SELECT SUM(quantity) as total_qty FROM st_stock_register WHERE contract_id = '".$reverseindentid['contract_id']."' AND item_id = '".$reverseindentid['finishedproduct_id']."' AND store_type IN ('0', '1') AND reverse_id != '".$reverseindentid['reverse_id']."'");
                      $received_qty = $received ? $received['total_qty'] : 0;
                      
                      $designsheet = $this->comman->find_first_query("SELECT quantity FROM designsheet WHERE contract_id = '".$reverseindentid['contract_id']."' AND item_id = '".$reverseindentid['finishedproduct_id']."'");
                      $req_qty = $designsheet ? $designsheet['quantity'] : 0;
                      
                      $pending_qty = $req_qty - $received_qty;

                      $itemname = $this->comman->getitemcatcom($reverseindentid['finishedproduct_id']);
                      
                      // The current reverse quantity
                      $current_qty = 0;
                      foreach ($reverseindentdetails as $val) {
                          if ($val['item_id'] == $reverseindentid['finishedproduct_id']) {
                              $current_qty = $val['quantity'];
                          }
                      }
                    ?>
                    <tr class="video_details">
                      <td width="55%">
                        <?php echo $this->Form->input('finisheditem_id', array('class' => 'form-control', 'type' => 'hidden', 'value' => $reverseindentid['finishedproduct_id'], 'label' => false, 'autofocus', 'autocomplete' => 'off')); ?>
                        <?php echo $this->Form->input('item_name', array('class' => 'form-control', 'type' => 'text', 'value' => $itemname['item_name'], 'label' => false, 'autofocus', 'autocomplete' => 'off', 'readonly')); ?>
                      </td>
                      <td width="15%">
                        <?php echo $this->Form->input('unit_name', array('class' => 'form-control', 'type' => 'text', 'value' => $itemname['measurementunit']['unit_name'], 'label' => false, 'autofocus', 'autocomplete' => 'off', 'readonly')); ?>
                      </td>
                      <td width="15%">
                        <input type="text" id="pending_qty" class="form-control" value="<?php echo $pending_qty; ?>" readonly>
                      </td>
                      <td width="15%">
                        <input type="text" onkeypress='return isNumberKey(event)' name="finished_qty" id="receive_qty" value="<?php echo $current_qty; ?>" class="form-control newquan" autocomplete='off' required>
                      </td>
                    </tr>

                    <tr class="totalColumn" style="background-color: #e0e0e0;">
                      <th width="55%">Raw Material</th>
                      <th width="30%" colspan="2">Received Qty</th>
                      <th width="15%">UOM</th>
                    </tr>

                    <?php $i = 1;
                    foreach ($reverseindentdetails as $key => $value) {
                      if ($value['item_id'] == $reverseindentid['finishedproduct_id']) {
                          continue;
                      }
                      $raw_itemname = $this->comman->getitemcatcom($value['item_id']);
                      ?>
                      <tr class="video_details">
                        <td width="55%">
                          <?php echo $this->Form->input('item_id[]', array('class' => 'form-control', 'type' => 'hidden', 'value' => $value['item_id'], 'label' => false, 'autofocus', 'autocomplete' => 'off')); ?>
                          <?php echo $this->Form->input('raw_item_name[]', array('class' => 'form-control', 'type' => 'text', 'value' => $raw_itemname['item_name'], 'label' => false, 'autofocus', 'autocomplete' => 'off', 'readonly')); ?>
                        </td>
                        <td width="30%" colspan="2"><input type="text" onkeypress='return isNumberKey(event)' name="itemquantity[]"
                            value="<?php echo $value['quantity'] ?>" class="form-control newquan quntt<?php echo $i; ?>"
                            autocomplete='off'></td>
                        <td width="15%">
                          <?php
                          echo $this->Form->input('raw_unit_name[]', array('class' => 'form-control', 'type' => 'text', 'value' => $raw_itemname['measurementunit']['unit_name'], 'label' => false, 'autofocus', 'autocomplete' => 'off', 'readonly')); ?>
                        </td>
                      </tr>
                      <?php $i++;
                    } ?>

                  </tbody>

                </table>
              </div>
            </div>





          </div>
        </div>
        <!-- /.box-body -->
        <div class="box-footer">
          <?php
          echo $this->Form->submit(
            'Save & Finalize',
            array('name' => 'action', 'value' => 'finalize', 'class' => 'btn btn-success pull-right', 'id' => 'formsubmitbtn', 'style' => 'margin-left: 10px;', 'title' => 'Save & Finalize')
          );
          echo $this->Form->submit(
            'Save as Draft',
            array('name' => 'action', 'value' => 'draft', 'class' => 'btn btn-warning pull-right', 'id' => 'formdraftbtn', 'title' => 'Save as Draft')
          );
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
   function cllbckretail3(id, cid, sid) {
      $('.secrh-retail').val(id);
      $('#retail_ids').val(cid);
      $('#testUL').hide();
      $('#testUL1').hide();
   }
   $(function () {
      $('.secrh-retail').bind('keyup', function () {
         var pos = $(this).val();
         var check = 3;
         $('#testUL').show();
         $('#retail_ids').val('');
         var count = pos.length;
         if (count > 0) {
            $.ajax({
               type: 'POST',
               url: '<?php echo ADMIN_URL; ?>production/getname',
               data: {
                  'fetch': pos,
                  'check': check
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
  $(document).ready(function () {
    $('#formsubmitbtn').click(function() {
        $('#sevice_form').append('<input type="hidden" name="action" value="finalize">');
    });
    $('#formdraftbtn').click(function() {
        $('#sevice_form').append('<input type="hidden" name="action" value="draft">');
    });

    $('#sevice_form').on('submit', function (e) {
      var receive_qty = parseFloat($('#receive_qty').val());
      var pending_qty = parseFloat($('#pending_qty').val());

      if (isNaN(receive_qty) || receive_qty <= 0) {
          alert('Received Qty must be greater than 0.');
          e.preventDefault();
          return false;
      }
      if (receive_qty > pending_qty) {
          alert('Received Qty cannot exceed Design Sheet Qty.');
          e.preventDefault();
          return false;
      }

      $("#formsubmitbtn").prop("disabled", true);
      $("#formdraftbtn").prop("disabled", true);
    });
  });
</script>