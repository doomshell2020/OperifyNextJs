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
</style>

<div class="content-wrapper">
   <section class="content-header">
      <h1>
         Payments Manager
      </h1>
      <ol class="breadcrumb">
         <li><a href="<?php echo SITE_URL; ?>admin/payments"><i class="fa fa-home"></i>Home</a></li>
         <li><a href="<?php echo SITE_URL; ?>admin/payments"> Payments Manager</a></li>
      </ol>
   </section>
   <!-- content header -->
   <!-- Main content -->
   <section class="content">
      <div class="row">
         <div class="col-xs-12">
            <div class="box">
               <div class="box-header" style="padding-bottom:0px;margin-bottom:5px;">
                  <?php echo $this->Flash->render(); ?>
                  <script>
                     function cllbckretail0(id, cid, sid) {
                        $('.secrh-retail').val(id);
                        $('#retail_ids').val(cid);
                        $('#testUL').hide();
                     }

                     $(function () {
                        $('.secrh-retail').bind('keyup', function () {
                           var pos = $(this).val();
                           var check = 0;
                           $('#testUL').show();
                           $('#retail_ids').val('');
                           var count = pos.length;
                           if (count > 0) {
                              $.ajax({
                                 type: 'POST',
                                 url: '<?php echo ADMIN_URL; ?>vendors/getname',
                                 data: {
                                    'fetch': pos,
                                    'check': check
                                 },
                                 success: function (data) {
                                    console.log(data);
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
                     $(document).ready(function () {
                        $("#Mysubscriptions").bind("submit", function (event) {
                           $.ajax({
                              async: true,
                              data: $("#Mysubscriptions").serialize(),
                              dataType: "html",
                              type: "GET",
                              url: "<?php echo ADMIN_URL; ?>payments/searchitem",
                              success: function (data) {
                                 $("#example23").html(data);
                              },

                           });
                           return false;
                        });

                        $(document).on('click', '.pagination a', function (e) {
                           var target = $(this).attr('href');
                           var res = target.replace("/payments/searchitem", "/payments");
                           window.location = res;
                           return false;
                        });

                     });
                  </script>




                  <?php echo $this->Form->create('Mysubscription', array('type' => 'file', 'inputDefaults' => array('div' => false, 'label' => false), 'id' => 'Mysubscriptions', 'class' => 'form-horizontal')); ?>
                  <div class="form-group">
                     <div class="row">

                        <div class="col">
                           <label for="inputEmail3" class="control-label">Vendor</label>
                           <input type="hidden" required="required" name="vendor_id" id="retail_ids"
                              value="<?php echo $vendor_id['id'] ?>">
                           <?php echo $this->Form->input('nitem', array('class' => 'form-control secrh-retail', 'id' => 'itemname', 'required', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'value' => $vendor_id['name'], 'placeholder' => 'Enter Vendor Name')); ?>
                           <div id="testUL" style="display:none;">
                              <ul></ul>
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
                                    onSelect: function (date) {
                                       var selectedDate = new Date(date);
                                       var endDate = new Date(selectedDate);
                                       endDate.setDate(endDate.getDate());
                                    }
                                 });
                                 $('#fdatefrom').datepicker('setDate', new Date());
                                 $('#fendfrom').datepicker({
                                    dateFormat: 'dd-mm-yy',
                                    yearRange: '2018:2030',
                                    changeMonth: true,
                                    changeYear: true,
                                 });
                                 $('#fendfrom').datepicker('setDate', new Date());
                              });
                           </script>
                           <label for="inputEmail3" class="control-label">Date From</label>
                           <?php echo $this->Form->input('datefrom', array('class' => 'form-control', 'id' => 'fdatefrom', 'readonly', 'placeholder' => 'Date From', 'label' => false)); ?>
                        </div>
                        <div class="col">
                           <label for="inputEmail3" class="control-label">Date To</label>
                           <?php echo $this->Form->input('dateto', array('class' => 'form-control', 'id' => 'fendfrom', 'readonly', 'placeholder' => 'Date To', 'label' => false)); ?>
                        </div>
                        <div class="col">
                           <input type="submit"
                              style="background-color:#00c0ef; !important; margin-top: 23px; color:#fff; padding:6px 18px;"
                              id="Mysubscriptions" class="btn btn4 btn_pdf myscl-btn date" value="Search">

                           <a href="<?php echo SITE_URL; ?>admin/payments/index" class="excelbtn btn"
                              style="background-color:#00c0ef; !important; margin-top: 23px; color:#fff; padding:6px 18px;">Reset</a>

                           <a target="_blank" href="<?php echo SITE_URL; ?>admin/payments/viewpdf" class="excelbtn btn"
                              style="padding:0;margin-top: 23px;"><i class="fa fa-file-pdf-o"
                                 style="font-size:28px;"></i></a>
                        </div>
                        <div class="col">
                           <a href="<?php echo SITE_URL; ?>admin/payments/add"
                              class="btn btn-success pull-right m-top10"
                              style=" margin-top: 23px; color:#fff; padding:6px 10px;"><i
                                 class="fa fa-plus"></i>&nbsp;Add</a>
                        </div>

                     </div>
                  </div>
                  <?php echo $this->Form->end(); ?>

               </div>
               <!--and serching -->
               <!-- /.box-header -->
               <div class="box-body" style="padding-top:0px;" id="example23">
                  <table class="table table-bordered table-striped" width="100%">
                     <thead>
                        <tr>
                           <th width="08%">Date</th>
                           <th width="40%">Description</th>
                           <th width="15%">Credit Amount</th>
                           <th width="15%">Debit Amount</th>
                           <th width="15%">Balance</th>
                           <th width="07%">Action</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php $page = $this->request->params['paging']['payments']['page'];
                        $limit = $this->request->params['paging']['payments']['perPage'];
                        $counter = ($page * $limit) - $limit + 1;
                        if (isset($payments) && !empty($payments)) {
                           // $curbalance = $this->Comman->getvendorbalance($payments[0]['vendor_id'],$date);
                           foreach ($payments as $intusr) {
                              $vendor_id = $this->Comman->findvendornames($intusr['vendor_id']);

                              if ($intusr['store_type'] == '1') {
                                 $curbalance = $curbalance + $intusr['total_amt'];
                              } else {
                                 $curbalance = $curbalance - $intusr['total_amt'];
                              }


                              ?>
                              <tr>
                                 <td>
                                    <?php
                                    echo date("d-m-Y", strtotime($intusr['bill_date']));
                                    ?>
                                 </td>
                                 <td>
                                    <?php
                                    if ($intusr['store_type'] == '1') {
                                       echo 'Bill No. ' . $intusr['bill_no'] . ' With <br> ' . $intusr['remark'];
                                    } else {
                                       echo 'Recipt No. ' . $intusr['receipt_no'] . ' With <br> ' . $intusr['remark'];
                                    }
                                    ?>
                                 </td>
                                 <td style="text-align:right;">
                                    <?php
                                    if ($intusr['store_type'] == '1') {
                                       echo number_format((float) $intusr['total_amt'], 2, '.', '');
                                    } else {
                                       echo '-';
                                    }
                                    ?>
                                 </td>
                                 <td style="text-align:right;">
                                    <?php
                                    if ($intusr['store_type'] == '2') {
                                       echo number_format((float) $intusr['total_amt'], 2, '.', '');
                                    } else {
                                       echo '-';
                                    }
                                    ?>
                                 </td>
                                 <td style="text-align:right;">
                                    <?php
                                    echo number_format((float) $curbalance, 2, '.', '');
                                    ?>
                                 </td>
                                 <td>
                                    <?php
                                    if ($intusr['store_type'] == 2) {
                                       $user_id = $_SESSION['Auth']['User']['id'];
                                       $controllerName = $this->request->params['controller'];
                                       $actionName = $this->request->params['action'];
                                       $user_permission = $this->comman->finduserpermisson($user_id, $controllerName, $actionName);
      
                                       if ($user_permission['delete'] == 1) {
                                       echo $this->Html->link('', [
                                          'action' => 'delete',
                                          $intusr->id
                                       ], [
                                          'class' => 'fas fa-trash-alt',
                                          'style' => 'font-size: 16px !important; color:#cd0404; margin-right: !important;',
                                          "onClick" => "javascript: return confirm('Are you sure do you want to delete this Payment')"
                                       ]);
                                    }
                                 }
                                    ?>
                                 </td>
                              </tr>
                              <?php $counter++;
                           } ?>
                           <tr>
                              <th style="text-align:right;" colspan="4">Closing Balance</th>
                              <th style="text-align:right;">
                                 <?php echo number_format((float) $curbalance, 2, '.', ''); ?>
                              </th>
                              <td></td>
                           </tr>
                        <?php } else { ?>
                           <tr>
                              <td style="text-align:center;" colspan="6">No Data Found</td>
                           </tr>
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
<div class="modal fade" id="globalModalbag" style="width:51% !important;" tabindex="-1" role="dialog"
   aria-labelledby="esModalLabel" aria-hidden="true">
   <div class="modal-dialog" style="width:100% !important;">
      <div class="modal-content personal">
         <div class="modal-body">
            <div class="col-sm-6 col-md-6 col-sm-offset-2 col-md-offset-2">
            </div>
            <div class="loader">
               <div class="es-spinner">
                  <i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<script>
   $('.viewgrndetails').click(function (e) {
      e.preventDefault();
      $('#editsortsgrn').modal('show').find('.modal-body').load($(this).attr('href'));
   });
</script>

<div class="modal fade" id="editsortsgrn">
   <div class="modal-dialog " style="max-width: 900px !important;">
      <div class="modal-content">
         <div class="modal-body"></div>
      </div>
   </div>
</div>