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
                  <thead>
                    <tr class="totalColumn">
                      <th width="10%">S.No.</th>
                      <th width="50%">Raw Material</th>
                      <th width="30%">Received Qty</th>
                      <th width="10%">UOM</th>
                      <!-- <th>Action</th> -->
                    </tr>
                  </thead>
                  <tbody id="product_containes">

                    <?php $i = 1;
                    foreach ($reverseindentdetails as $key => $value) {
                      $itemname = $this->comman->getitemcatcom($value['item_id']);
                      ?>
                      <tr class="video_details">
                        <td width="5%">
                          <?php echo $i; ?>
                        </td>
                        <td width="42%">
                          <?php echo $this->Form->input('item_id[]', array('class' => 'form-control', 'type' => 'hidden', 'value' => $value['item_id'], 'label' => false, 'autofocus', 'autocomplete' => 'off')); ?>

                          <?php echo $this->Form->input('item_name[]', array('class' => 'form-control', 'type' => 'text', 'value' => $itemname['item_name'], 'label' => false, 'autofocus', 'autocomplete' => 'off', 'readonly')); ?>
                        </td>
                        <td width="16%"><input type="text" onkeypress='return isNumberKey(event)' name="itemquantity[]"
                            value="<?php echo $value['quantity'] ?>" class="form-control newquan quntt<?php echo $i; ?>"
                            autocomplete='off'></td>
                        <td width="5%">
                          <?php
                          echo $this->Form->input('unit_name[]', array('class' => 'form-control', 'type' => 'text', 'value' => $itemname['measurementunit']['unit_name'], 'label' => false, 'autofocus', 'autocomplete' => 'off', 'readonly')); ?>
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
          if (isset($location['id'])) {
            echo $this->Form->submit(
              'Update',
              array('class' => 'btn btn-info pull-right', 'id' => 'formsubmitbtn', 'title' => 'Update')
            );
          } else {
            echo $this->Form->submit(
              'Save & Finalize',
              array('class' => 'btn btn-info pull-right', 'id' => 'formsubmitbtn', 'title' => 'Save & Finalize')
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
    $('#sevice_form').on('submit', function (e) {
      $("#formsubmitbtn").css("display", "none");
    });
  });
</script>