<style>
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
         Reverse Manager
      </h1>
      <ol class="breadcrumb">
         <li><a href="<?php echo SITE_URL; ?>admin/reverseindent"><i class="fa fa-home"></i>Home</a></li>
         <li><a href="<?php echo SITE_URL; ?>admin/reverseindent">Reverse Manager</a></li>
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
                           <label for="inputEmail3" class=" control-label" style="text-align: left !important">Product
                              Name</label>
                           <input type="hidden" required="required" name="item_id" id="retail_id">
                           <?php echo $this->Form->input('item_name', array('class' => 'form-control secrh-retails', 'id' => 'indent', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'placeholder' => 'Enter Product Name')); ?>

                           <div id="test1UL" style="display:none;">
                              <ul></ul>
                           </div>
                           <div id="test1UL1" style="display:none;">
                              <ul>
                                 <li
                                    style="padding: 5px 8px;list-style:none;color: black;font-weight: bold;margin-left:-32px; border: 1px solid lightgray;">
                                    No Record Found</li>
                              </ul>
                           </div>
                        </div>
                        <div class="col">
                           <label for="inputEmail3" class=" control-label" style="text-align: left !important">Machine
                              Name</label>
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
                           <?php echo $this->Form->input('datefrom', array('class' => 'form-control', 'id' => 'fdatefrom', 'readonly', 'placeholder' => 'Start Date', 'label' => false)); ?>
                        </div>
                        <div class="col">
                           <label for="inputEmail3" class="control-label">End Date</label>
                           <?php echo $this->Form->input('dateto', array('class' => 'form-control', 'id' => 'fendfrom', 'readonly', 'placeholder' => 'End Date', 'label' => false)); ?>
                        </div>
                        <div class="col">
                           <input type="submit" style="background-color:#00c0ef; color:#fff;margin-top:23px;" id=""
                              class="btn btn4 btn_pdf myscl-btn date" value="Search">

                           <a href="<?php echo SITE_URL; ?>admin/reverseindent/index" class="excelbtn btn"
                              style="background-color:#00c0ef; !important; margin-top: 23px; color:#fff; padding:6px 18px;">Reset</a>
                        </div>

                        <div class="col">
                           <?php
                           $role_permissions = $this->Permission->permissioncheck();
                           $fileurl = "admin/reverseindent/add";
                           if (in_array($fileurl, $role_permissions)) { ?>
                              <a href="<?php echo SITE_URL; ?>admin/reverseindent/add"
                                 class="btn btn-success pull-right m-top10" style="margin-top: 23px;margin-bottom:10px;"><i
                                    class="fa fa-plus" aria-hidden="true"></i>Add</a>
                           <?php } ?>
                           <a href="<?php echo SITE_URL; ?>admin/reverseindent/excel" class="excelbtn btn pull-right"
                              style="padding:0;margin-top: 23px;"><i class="fa fa-file-excel-o"
                                 style="font-size:28px; margin-right:10px;"></i></a>
                        </div>

                     </div>
                  </div>
                  <?php echo $this->Form->end(); ?>

               </div>
               <!-- /.box-header -->
               <div id="load2" style="display:none;"></div>
               <div class="box-body" style="padding-top:0px;" id="updt">
                  <table class="table table-bordered table-striped">
                     <thead>
                        <tr>
                           <th>S No.</th>
                           <th>Reverse Id</th>
                           <th>Contract name</th>
                           <th>Product</th>
                           <th>Machine Name</th>
                           <th>Received By</th>
                           <th>Received Date</th>
                           <th>Action</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php $page = $this->request->params['paging']['Reverseindent']['page'];

                        $limit = $this->request->params['paging']['Reverseindent']['perPage'];
                        // pr( $this->request->params);
                        $counter = ($page * $limit) - $limit + 1;
                        if (isset($reverseindentid) && !empty($reverseindentid)) {
                           foreach ($reverseindentid as $value) {
                              $itemname = $this->comman->getitemname($value['finishedproduct_id']);
                              $contractname = $this->comman->findcontractname($value['contract_id']);
                              $machinename = $this->comman->getMachineName($value['machine_id']);
                              ?>
                              <tr>
                                 <td>
                                    <?php echo ($counter); ?>
                                 </td>


                                 <td><a href="<?php echo SITE_URL; ?>admin/reverseindent/viewreverseindent/<?php echo $value['reverse_id']; ?>"
                                       class="viewreverseindentdetails">
                                       <?php echo $value['reverse_id']; ?>
                                    </a></td>


                                 <td><a href="<?php echo SITE_URL; ?>admin/production/viewcontractdetail/<?php echo $value['contract_id']; ?>"
                                       class="viewdetails">
                                       <?php echo $contractname['title'] . '(' . $contractname['workorder'] . ')'; ?>
                                    </a></td>
                                 <td>
                                    <?php echo $itemname['item_name']; ?>
                                 </td>
                                 <td>
                                    <?php echo $machinename['machine_name']; ?>
                                 </td>
                                 <td>
                                    <?php echo ucfirst($value['received_name']); ?>
                                 </td>
                                 <td>
                                    <?php echo date("d-m-Y", strtotime($value['issue_date'])); ?>
                                 </td>

                                 <td>
                                    <?php
                                    $user_id = $_SESSION['Auth']['User']['id'];
                                    $controllerName = $this->request->params['controller'];
                                    $actionName = $this->request->params['action'];
                                    $user_permission = $this->comman->finduserpermisson($user_id, $controllerName, $actionName);
                                    $userdetails = $this->comman->getuser($user_id);

                                    if (date('d-m-Y') == date("d-m-Y", strtotime($value['issue_date']))) {
                                       $fileurl = "admin/reverseindent/edit";
                                       if (in_array($fileurl, $role_permissions)) { ?>
                                          <a href="<?php echo ADMIN_URL; ?>reverseindent/edit/<?php echo $value['reverse_id']; ?>"
                                             style="color:#3a6810; margin-right:5px;">
                                             <i class="far fa-edit" style="font-size: 16px !important;"></i>
                                          </a>
                                          <?php
                                       }
                                       $fileurl = "admin/reverseindent/delete";
                                       if (in_array($fileurl, $role_permissions)) {
                                          echo $this->Html->link('', [
                                             'action' => 'delete',
                                             $value->reverse_id,
                                          ], [
                                             'class' => 'fas fa-trash-alt',
                                             'style' => 'font-size: 16px !important; color:#cd0404; !important;',
                                             "onClick" => "javascript: return confirm('Are you sure do you want to delete this Indent')"
                                          ]);
                                       }
                                    }
                                    ?>&nbsp;&nbsp;


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
<div class="modal fade" id="myModal" style="width:51% !important;overflow-y: auto !important;" tabindex="-1"
   role="dialog" aria-labelledby="esModalLabel" aria-hidden="true">
   <div class="modal-dialog" style="width:100% !important;">
      <div class="modal-content personal">
         <div class="loader">
            <div class="es-spinner">
               <i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i>
            </div>
         </div>
      </div>
   </div>
</div>
<script>
   $(document).ready(function () {
      $(".globalModals").click(function (event) {
         alert($(this).attr("href"));
         $('.modal-content').load($(this).attr("href")); //load content from href of link
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
   $('.viewreverseindentdetails').click(function (e) {
      e.preventDefault();
      $('#reverseindent').modal('show').find('.modal-body').load($(this).attr('href'));
   });
</script>

<div class="modal fade" id="reverseindent">
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

<script>
   $(document).ready(function () {
      $("#mysubscription").bind("submit", function (event) {
         $.ajax({
            async: true,
            data: $("#mysubscription").serialize(),
            dataType: "html",
            type: "GET",
            url: "<?php echo ADMIN_URL; ?>reverseindent/searchitem",


            beforeSend: function (xhr) {
               xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
               $('#load2').css("display", "block"); // Show loader
            },
            success: function (data) {
               $("#updt").html(data);
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
         var res = target.replace("/reverseindent/searchitem", "/reverseindent");
         window.location = res;
         return false;
      });
   });
</script>

<script type="text/javascript">
   function cllbckretail0(name, id) {
      $('.secrh-retails').val(name);
      $('#retail_id').val(id);
      $('#test1UL').hide();
      $('#test1UL1').hide();
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
               url: '<?php echo ADMIN_URL; ?>Purchaseorder/getfinisheditemname',
               data: {
                  'fetch': pos,
                  'check': check
               },
               success: function (data) {
                  if (data) {
                     $('#test1UL ul').html(data);
                     $('#test1UL1').hide();
                  } else {
                     $('#test1UL').hide();
                     $('#test1UL1').show();
                  }
               },
            });
         } else {
            $('#test1UL').hide();
            $('#test1UL1').hide();
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