<div class="content-wrapper" style="min-height: 410px;">
  <section class="content-header">
    <h1>
      Stock Register
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo SITE_URL; ?>admin/dashboards"><i class="fa fa-home"></i>Home</a></li>
      <li><a href="<?php echo SITE_URL; ?>admin/stockregister">Goods Received Manager</a></li>
    </ol>
  </section>
  <!-- content header -->
  <style>
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

    .preview {
      margin-right: 15px;
    }

    #load2 {
      width: 100%;
      height: 100%;
      position: fixed;
      z-index: 9999;
      background-color: white !important;
      background: url("<?php echo SITE_URL; ?>images/Preloader_2.gif") no-repeat center center rgba(0, 0, 0, 0.75)
    }
  </style>
  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          <div class="box-header">
            <script>
              function cllbckretail(id, cid, sid) {
                $('.secrh-retail').val(id);
                $('#retail_ids').val(cid);
                $('#size').val(sid);
                $('#testUL').hide();
                //alert(cid);
                $.ajax({
                  type: 'POST',
                  url: '<?php echo ADMIN_URL; ?>indent/getitemdetail',
                  data: {
                    'fetch': cid
                  },
                  success: function(data) {
                    console.log(data);
                    //alert(data);
                    $('#unitna').val(data);
                  },
                });
              }
              $(function() {
                $('.secrh-retail').bind('keyup', function() {
                  var pos = $(this).val();
                  var check = 0;
                  $('#testUL').show();
                  $('#retail_ids').val('');
                  var count = pos.length;
                  if (count > 0) {
                    $.ajax({
                      type: 'POST',
                      url: '<?php echo ADMIN_URL; ?>stockregister/getitemname',
                      data: {
                        'fetch': pos,
                        'check': check
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

            <script inline="1">
              $(document).ready(function() {
                $("#mysubscription").bind("submit", function(event) {
                  $.ajax({
                    async: true,
                    data: $("#mysubscription").serialize(),
                    dataType: "html",
                    type: "GET",
                    url: "<?php echo ADMIN_URL; ?>stockregister/search_required_stock",
                    // beforeSend: function() {},
                    // success: function(data) {
                    //   $("#updt").html(data);
                    // },
                    // complete: function(data) {},



                    beforeSend: function(xhr) {
                      xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
                      $('#load2').css("display", "block"); // Show loader
                    },
                    success: function(data) {
                      $("#updt").html(data);
                    },
                    complete: function() {
                      $('#load2').css("display", "none"); // Hide loader
                    },
                    error: function() {
                      alert("An error occurred. Please try again.");
                      $('#load2').css("display", "none"); // Hide loader on error
                    }
                  });
                  return false;
                });


                $(document).on('click', '.pagination a', function(e) {
                  var target = $(this).attr('href');
                  var res = target.replace("/stockregister/search_required_stock", "/stockregister/required_stock");
                  window.location = res;
                  return false;
                });
              });
            </script>

            <?php echo $this->Form->create('', array('type' => 'file', 'inputDefaults' => array('div' => false, 'label' => false), 'id' => 'mysubscription', 'class' => 'form-horizontal', 'style' => 'margin-bottom:0px;')); ?>
            <div class="form-group" style="margin-bottom:0px;">
              <div class="row">

                <!-- <div class="col-sm-3"> -->
                <script>
                  $(document).ready(function() {
                    $('#fdatefrom').datepicker({
                      dateFormat: 'dd-mm-yy',
                      yearRange: '2018:2030',
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

                <div class="col-sm-3">
                  <label for="inputEmail3" class="control-label">Product<strong style="color:red;">*</strong></label>
                  <input type="hidden" required="required" name="item_id" id="retail_ids">
                  <?php echo $this->Form->input('nitem', array('class' => 'form-control secrh-retail', 'id' => 'itemname', 'type' => 'text', 'label' => false, 'required', 'autofocus', 'autocomplete' => 'off', 'placeholder' => 'Enter Item Name')); ?>
                  <div id="testUL" style="display:none;">
                    <ul></ul>
                  </div>
                </div>


                <div class="col-sm-3">
                  <label for="inputEmail3" class="control-label">Date From </label>
                  <input type="text" name="datefrom" class="form-control" placeholder="Date From" id="fdatefrom">
                </div>



                <div class="col-sm-3">
                  <label for="inputEmail3" class="control-label">Date To </label>
                  <input type="text" name="dateto" class="form-control" placeholder="Date To" id="fendfrom">
                </div>


                <div class="col-sm-3">
                  <label for="inputEmail3" class="control-label"></label>
                  <input type="submit"
                    style="background-color:#00c0ef; color:#fff; margin-top: 23px;"
                    id="Mysubscriptions" class="btn btn4 btn_pdf myscl-btn date" value="Search">
                  <a href="<?php echo SITE_URL; ?>admin/stockregister/required_stock" class="excelbtn btn"
                    style="background-color:#00c0ef; !important; margin-top: 23px; color:#fff; padding:6px 18px; margin-left: 7px;">Reset</a>

                </div>
              </div>
            </div>
            <?php echo $this->Form->end(); ?>
          </div>



          <div id="load2" style="display:none;"></div>
          <div class="box-body" id="updt">
            <table id="" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th style="width: 16%;">S.No</th>
                  <th style="width: 16%;">Product Name</th>
                  <?php
                  foreach ($contracts as $contractsIds) {
                    $getContractName = $this->comman->findcontractname($contractsIds['contract_id']); ?>
                    <th><?= $getContractName['title']; ?></th>
                  <?php  }
                  ?>
                  <th>Total Available</th>
                  <th>Required</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <?php
                  $page = $this->request->params['paging']['Additem']['page'];
                  $limit = $this->request->params['paging']['Additem']['perPage'];
                  $count = ($page * $limit) - $limit + 1;

                  foreach ($products as $productsName) {

                    $totalReqiredStock = 0;

                    $todaydate = date('Y-m-d');
                    $openingstock = $this->comman->todayopeningstock($productsName['id'], $todaydate);
                    $receivedtock = $this->comman->todayrecivedstock($productsName['id'], $todaydate);
                    $issuedstock = $this->comman->todayissuedtock($productsName['id'], $todaydate);
                    $reversestock = $this->comman->todayreversestock($productsName['id'], $todaydate);
                    $returnstock = $this->comman->todayreturnstock($productsName['id'], $todaydate);
                    $closingstock = $openingstock + $receivedtock - $issuedstock + $reversestock - $returnstock;
                    $closingstock = number_format((float)$closingstock, 2, '.', '');

                  ?>

                    <td><?= $count; ?></td>
                    <td><?= $productsName['item_name']; ?></td>
                    <?php
                    foreach ($contracts as $contractsIds) {
                      $stockCount = 0;

                      $checkRawmaterial = $this->comman->checkRawmaterial($contractsIds['contract_id'], $productsName['id']);

                      if ($checkRawmaterial) {
                        $getContractFinished = $this->comman->getContractFinished($contractsIds['contract_id'], $startDate, $endDate);


                        foreach ($getContractFinished as $finishedProduct) {
                          $getDesignsheet = $this->comman->getdesignsheetno($contractsIds['contract_id'], $finishedProduct['item_id']);
                          $getDesignSheetDetails = $this->comman->getdesignsheetitemname($productsName['id'], $getDesignsheet['id']);
                          $stockCount += $getDesignSheetDetails['km_item_qty'] * $finishedProduct['plannedqty'];
                        }
                      } else {
                        $stockCount = 0;
                      }

                      $totalReqiredStock += $stockCount;
                    ?>
                      <td><?= $stockCount; ?></td>
                    <?php  }
                    ?>
                    <td><?= $closingstock; ?></td>
                    <td><?= (($totalReqiredStock - $closingstock) > 0) ? $totalReqiredStock - $closingstock : 0; ?></td>
                </tr>
              <?php
                    $count++;
                  }
              ?>

              </tbody>
            </table>
            <?php echo $this->element('admin/pagination'); ?>
          </div>
          <!-- /.box-body -->
        </div>
      </div>
    </div>
  </section>
  <!-- /.content -->
</div>