<style>
   .input_fields_wrap .form-control {
      margin-bottom: 15px;
   }

   .control-label {
      display: block;
      margin-bottom: 10px;
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

   #load2 {
      width: 100%;
      height: 100%;
      position: fixed;
      z-index: 9999;
      background-color: white !important;
      background: url("<?php echo SITE_URL; ?>images/Preloader_2.gif") no-repeat center center rgba(0, 0, 0, 0.75)
   }
</style>

<div class="content-wrapper">
   <section class="content-header">
      <h1>
         Design Sheet
      </h1>
      <ol class="breadcrumb">
         <li><a href="<?php echo SITE_URL; ?>admin/designsheet"><i class="fa fa-home"></i>Home</a></li>
      </ol>
   </section>
   <!-- content header -->
   <!-- Main content -->
   <section class="content">
      <div class="row">
         <div class="col-xs-12">
            <div class="box">
               <div class="box-header">
                  <?php echo $this->Flash->render(); ?>
                  <?php echo $this->Form->create('', array('type' => 'file', 'inputDefaults' => array('div' => false, 'label' => false), 'id' => 'mysubscription', 'class' => 'form-horizontal', 'style' => 'margin-bottom:0px;')); ?>
                  <div class="form-group" style="margin-bottom:0px;">
                     <div class="row">
                        <!-- <div class="col-sm-2">
                           <label for="inputEmail3" class="control-label">Work Order</label>
                           <?php echo $this->Form->input('workorder', array('class' => 'form-control ', 'type' => 'number', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'placeholder' => 'Enter Work Order')); ?>
                        </div> -->
                        <div class="col">
                           <label for="inputEmail3" class=" control-label" style="text-align: left !important">Contract
                              Name</label>
                           <input type="hidden" name="contract_id" id="contrselectid">
                           <?php echo $this->Form->input('contractname', array('class' => 'form-control secrhcontract', 'id' => 'contractnameid', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'placeholder' => 'Enter Contract Name')); ?>
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
                        <div class="col">
                           <script>
                              $(document).ready(function () {
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
                           <label for="inputEmail3" class="control-label">Start Date</label>
                           <?php echo $this->Form->input('datestart', array('class' => 'form-control', 'id' => 'fdatefrom', 'readonly', 'placeholder' => 'Start Date', 'label' => false)); ?>
                        </div>
                        <div class="col">
                           <label for="inputEmail3" class="control-label">End Date</label>
                           <?php echo $this->Form->input('dateto', array('class' => 'form-control', 'id' => 'fendfrom', 'readonly', 'placeholder' => 'End Date', 'label' => false)); ?>
                        </div>
                        <div class="col">
                           <input type="submit"
                              style="background-color:#00c0ef; color:#fff;width:100px !important;margin-top:19px;" id=""
                              class="btn btn4 btn_pdf myscl-btn date" value="Search">

                           <a href="<?php echo SITE_URL; ?>admin/designsheet/index" class="excelbtn btn"
                              style="background-color:#00c0ef; !important; margin-top: 19px; color:#fff; padding:6px 18px;">Reset</a>
                           <?php echo $this->Form->end(); ?>
                        </div>

                        <?php
                        $role_permissions = $this->Permission->permissioncheck();
                        $fileurl = "admin/designsheet/add";
                        if (in_array($fileurl, $role_permissions)) { ?>

                           <div class="col">
                              <a class="btn btn-success pull-right m-top10"
                                 href="<?php echo SITE_URL; ?>admin/designsheet/add"
                                 style="background-color:#2d95e3;color:#fff;margin-top:19px;">
                                 <i class="fa fa-plus" aria-hidden="true"></i>Add</a>
                           </div>
                        <?php } ?>
                     </div>

                  </div>
               </div>
          
         
         <!-- box-header -->
         <div id="load2" style="display:none;"></div>
         <div class="box-body" id="example23" style="padding:0px; margin-top:10px;">
            <table class="table table-bordered table-striped" width="100%">
               <thead>
                  <tr>
                     <th width="3%">S.No.</th>
                     <th width="9%">Design Sheet No.</th>
                     <th width="25%">Contract Name</th>
                     <th width="35%">Type Of Cable</th>
                     <th width="8%">Quantity(in KM)</th>
                     <th width="7%">Issue Date</th>
                     <th width="7%">Design Sheet</th>
                     <th width="6%">Action</th>
                  </tr>
               </thead>
               <tbody>
                  <?php $page = $this->request->params['paging']['Designsheet']['page'];
                  $limit = $this->request->params['paging']['Designsheet']['perPage'];
                  $counter = ($page * $limit) - $limit + 1;
                  if (isset($designs) && !empty($designs)) {
                     foreach ($designs as $intusr) {
                        // pr($intusr);die;
                        $contractname = $this->comman->findcontractname($intusr['contract_id']);
                        $itemname = $this->Comman->getitemname($intusr['item_id']);
                        ?>
                        <tr>
                           <td>
                              <?php echo $counter; ?>
                           </td>
                           <td><a href="<?php echo SITE_URL; ?>admin/designsheet/viewdesignsheet/<?php echo $intusr['designsheetno']; ?>"
                                 class="designsheetdetails"><?php echo $intusr['designsheetno']; ?></a></td>

                           <td><a href="<?php echo SITE_URL; ?>admin/production/viewcontractdetail/<?php echo $intusr['contract_id']; ?>"
                                 class="viewdetails"><?php echo $contractname['title'] . '(' . $contractname['workorder'] . ')'; ?></a>
                           </td>

                           <td>
                              <?php echo $itemname['item_name']; ?>
                           </td>
                           <td>
                              <?php echo $intusr['quantity']; ?>
                           </td>

                           <td>
                              <?php echo date('d-m-Y', strtotime($intusr['datefrom'])); ?>
                           </td>

                           <td>
                              <?php if (!empty($intusr['design_sheet'])) { ?>
                                 <a target="_blank" href="<?php echo SITE_URL . 'designsheet/' . $intusr['design_sheet']; ?>"
                                    title="Design Sheet" data-method="post" data-toggle="tooltip"><span
                                       class="fa fa-download fa-lg text-green"></span></a> &nbsp; &nbsp;
                                 <?php $i = 1;
                                 for ($i = 1; $i < 6; $i++) {
                                    if ($intusr['r' . $i]) { ?>
                                       <a target="_blank" href="<?php echo SITE_URL . 'designsheet/' . $intusr['r' . $i]; ?>"
                                          title="R<?php echo $i ?>" data-method="post" data-toggle="tooltip"><span
                                             class="fa fa-download fa-lg text-green"></span></a> &nbsp; &nbsp; <?php } ?>
                                 <?php }
                              } else {
                                 echo '-';
                              } ?>
                           </td>

                           <td> <strong>
                                 <?php

                                 $checkindentpo = $this->Comman->checkindentpo($intusr['contract_id'], $intusr['item_id']);
                                 $fileurl = "admin/designsheet/edit";
                                 if (in_array($fileurl, $role_permissions)) {
                                    echo $this->Html->link('', [
                                       'action' => 'edit',
                                       $intusr->id,
                                    ], ['class' => 'fas fa-edit', 'style' => 'font-size: 16px !important;']);
                                 }
                                 ?>

                                 <?php
                                 if (empty($checkindentpo)) {
                                    $fileurl = "admin/designsheet/delete";
                                    if (in_array($fileurl, $role_permissions)) {
                                       echo $this->Html->link('', [
                                          'action' => 'delete',
                                          $intusr->id
                                       ], [
                                          'class' => 'fas fa-trash-alt',
                                          'style' => 'font-size: 16px !important; color:#cd0404; margin-right:4px !important;',
                                          "onClick" => "javascript: return confirm('Are you sure do you want to delete this Design Sheet')"
                                       ]);
                                    }
                                 }
                                 ?>

                              </strong>
                           </td>
                        </tr>
                        <?php $counter++;
                     }
                  } else { ?>
                  <?php } ?>
               </tbody>
            </table>
            <?php echo $this->element('admin/pagination'); ?>
         </div>
         <!-- /.box-body -->
      </div>
      <!-- /.box -->
</div>
<!-- /.col -->
</div>
<!-- /.row -->
</section>
<!-- /.content -->
</div>
<!-- /.   content-wrapper -->

<script>
   $(document).ready(function () {
      $("#mysubscription").bind("submit", function (event) {
         $.ajax({
            async: true,
            data: $("#mysubscription").serialize(),
            dataType: "html",
            type: "GET",
            url: "<?php echo ADMIN_URL; ?>designsheet/searchitem",

            // success: function (data) {
            //    console.log(data);
            //    $("#example23").html(data);
            // },
            beforeSend: function (xhr) {
               xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
               $('#load2').css("display", "block"); // Show loader
            },
            success: function (data) {
               $('.lds-facebook').hide();
               $("#example23").html(data);
            },
            complete: function () {
               $('#load2').css("display", "none"); // Hide loader
            },
            error: function () {
               alert("An error occurred. Please try again.");
               $('#load2').css("display", "none"); // Hide loader on error
            }

         });
         return false;
      });

      $(document).on('click', '.pagination a', function (e) {
         var target = $(this).attr('href');
         var res = target.replace("/designsheet/searchitem", "/designsheet");
         window.location = res;
         return false;
      });
   });
</script>

<script>
   $('.viewdetails').click(function (e) {
      e.preventDefault();
      $('#editsorts').modal('show').find('.modal-body').load($(this).attr('href'));
   });
</script>

<div class="modal fade" id="editsorts">
   <div class="modal-dialog" style="max-width:900px !important;">
      <div class="modal-content">
         <div class="modal-body"></div>
      </div>
   </div>
</div>

<script>
   $('.designsheetdetails').click(function (e) {
      e.preventDefault();
      $('#designsorts').modal('show').find('.modal-body').load($(this).attr('href'));
   });
</script>

<div class="modal fade" id="designsorts">
   <div class="modal-dialog" style="max-width:900px !important;">
      <div class="modal-content">
         <div class="modal-body"></div>
      </div>
   </div>
</div>
<script>
   function cllbckretail2(id, cid) {
      $('.secrhcontract').val(id);
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
                     $('#contractUL ul').html(data);
                     $('#contractUL1').hide();
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