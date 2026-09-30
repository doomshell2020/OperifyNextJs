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
                echo 'Generate Reverse id : I-' . $newindentid;
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
          ); ?>
          <input type="hidden" name="token" value=<?php echo uniqid(); ?>>
          <div class="box-body">

            <div class="form-group" style="margin-bottom:0px;">
              <div class="row">
                <div class="col-md-3">
                  <label for="inputEmail3" class=" control-label" style="text-align: left !important">
                  Reverse Id No.<strong style="color:red;">*</strong></label>
                  <?php echo $this->Form->input('reverse_id', array('class' => 'form-control', 'id' => 'indent_id', 'type' => 'text', 'value' => $newindentid, 'readonly', 'label' => false, 'placeholder' => 'Reverse id', 'autofocus', 'autocomplete' => 'off')); ?>
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
                  <?php echo $this->Form->input('issue_date', array('class' => 'form-control', 'id' => 'datepicker3', 'type' => 'text', '', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'required')); ?>
                </div>


                <div class="col-sm-3">
                  <label for="inputEmail3" class=" control-label" style="text-align: left !important">Contract
                    Name<strong style="color:red;">*</strong></label>

                  <input type="hidden" name="contract_id" id="contrselectid" required>

                  <?php echo $this->Form->input('contractname', array('class' => 'form-control secrhcontract', 'id' => 'contractnameid', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'required', 'placeholder' => 'Enter Contract Name')); ?>
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

                <div class="col-md-3">
                  <label for="inputEmail3" class="control-label" style="text-align: left !important">Product<strong
                      style="color:red;">*</strong></label>
                  <?php echo $this->Form->input('finisheditem_id', [
                    'class' => 'form-control data_req',
                    'type' => 'select',
                    'label' => false,
                    'empty' => '-- Select Product--',
                    'autofocus',
                    'required',
                    'autocomplete' => 'off',
                    'id' => 'item_id_pro'
                  ]); ?>
                </div>

                <div class="col-md-3">
                  <label for="inputEmail3" class=" control-label" style="text-align: left !important">Machine
                    Name<strong style="color:red;">*</strong></label>
                  <input type="hidden" name="machines_id" id="retail_ids">
                  <?php echo $this->Form->input('machine_id', array('class' => 'form-control secrh-retail', 'id' => 'itemname', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'placeholder' => 'Enter Machine Name')); ?>
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
                  <?php echo $this->Form->input('received_name', array('class' => 'form-control itemqty', 'type' => 'text', 'label' => false, 'required', 'placeholder' => 'Enter Name', 'autofocus', 'autocomplete' => 'off', 'required')); ?>
                </div>


              </div>
            </div>



            <div class="ctpcontent form-group" style="display:block">
              <div class="col-sm-12">
                <label for="inputEmail3" style="margin-bottom:10px;">Items</label>
                <table id="customers">
                  <thead>
                    <tr class="totalColumn">
                      <!-- <th width = "10%">S.No.</th> -->
                      <th width = "55%">Raw Material</th>
                      <th width = "30%">Received Qty</th>
                      <th width = "15%">UOM</th>
                      <!-- <th>Action</th> -->
                    </tr>
                  </thead>
                  <tbody id="product_containes">
                    <!-- Data from AJAX request will be populated here -->
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
  $(document).ready(function () {
    $('#sevice_form').on('submit', function (e) {
      $("#formsubmitbtn").css("display", "none");
    });
  });
</script>







<script>
  $(document).ready(function () {
    $(function () {
      $('#item_id_pro').on('change', function () {
        var itemid = $(this).val();
        var contractid = $('#contrselectid').val();
 

        if (itemid != "") {
          $.ajax({
            type: 'POST',
            url: '<?php echo ADMIN_URL; ?>reverseindent/getdesignsheetdetails',
            data: {
              'itemid': itemid,
              'contractid': contractid,
            },

            success: function (data) {
              $(".ctpcontent").css("display", "block");
              $('#product_containes').html(data);
            },
          });
        } else {
          $(".ctpcontent").css("display", "none");
        }
      });
    });
  });

</script>

<script type="text/javascript">
  function getcontractfinished(contract_id) {
    $.ajax({
      type: 'POST',
      url: '<?php echo ADMIN_URL; ?>production/getcontractfinished',
      data: {
        'contract_id': contract_id,
      },
      success: function (data) {
        if (data) {
          var select = $("#item_id_pro");
          select.empty();
          select.append($('<option>', {
            value: '',
            text: '-- Select Product--'
          }));
          var dataArray = JSON.parse(data);
          dataArray.forEach(function (item) {
            // console.log(item);
            select.append($('<option>', {
              value: item.id,
              text: item.item_name,
            }));
          });
        }
      },
    });
  }



    function cllbckretail2(id, cid) {
        $('.secrhcontract').val(id);
        $('#contrselectid').val(cid);
        getcontractfinished(cid);
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
                            // console.log(data);
                            $('#contractUL ul').html(data);
                            $('#contractUL1').hide();
                        } else {
                            $('#contractUL').hide();
                            $('#contractUL1').show();
                        }
                    }
                });
            } else {
                $('#contractUL').hide();
                $('#contractUL1').hide();
            }
        });
    });
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