<style>
   .input_fields_wrap .form-control {
      margin-bottom: 15px;
   }
</style>
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

   .dataTables_wrapper.form-inline.dt-bootstrap.no-footer {
      margin-top: 0px;
   }
</style>


<div class="content-wrapper">
   <section class="content-header">
      <h1>
         Dispatch Manager
      </h1>
      <ol class="breadcrumb">
         <li><a href="#"><i class="fa fa-home"></i>Home</a></li>
         <li><a href="#">Dispatch Manager</a></li>
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
                  <div class="form-group">



                     <div class="row">
                        <div class="col">
                           <label for="inputEmail3" class=" control-label"
                              style="text-align: left !important">Transporter
                              Name</label>
                           <input type="hidden" name="transports_id" id="retail_ids">
                           <?php echo $this->Form->input('transport_id', array('class' => 'form-control secrh-retail', 'id' => 'itemname', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'placeholder' => 'Enter Transporter Name')); ?>
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
                           <input type="submit"
                              style="background-color:#00c0ef; color:#fff;width:100px !important;margin-top:23px;" id=""
                              class="btn btn4 btn_pdf myscl-btn date" value="Search">

                           <a href="<?php echo SITE_URL; ?>admin/Transporter/index" class="excelbtn btn"
                              style="background-color:#00c0ef; !important; margin-top: 23px; color:#fff; padding:6px 18px;">Reset</a>


                        </div>
                        <div class="col">
                           <a href="<?php echo ADMIN_URL; ?>Transporter/add" class="excelbtn btn pull-right btn-success"
                              style=" margin-top: 23px; color:#fff;padding:6px 18px;border-radius: 4px;"><i
                                 class="fa fa-plus"></i>&nbsp;Add</a>

                           <a href="<?php echo ADMIN_URL; ?>Transporter/viewpdf" class="excelbtn btn pull-right "
                              style="padding:0;margin-top: 23px;margin-right: 5px;"><i class="fa fa-file-pdf-o"
                                 style="font-size:28px;"></i></a>
                        </div>
                     </div>
                     <?php echo $this->Form->end(); ?>

                  </div>
               </div>


               <!-- /.box-header -->
               <div class="box-body" id="example23">
                  <table class="table table-bordered table-striped">
                     <thead>

                        <tr>
                           <th>S.No.</th>
                           <th>Date</th>
                           <th>Transporter Name</th>
                           <th>To</th>
                           <th>From</th>
                           <th>Vehicle No.</th>
                           <th>GR No.</th>
                           <th>Weight</th>
                           <th>Freight</th>
                           <th>Action</th>
                        </tr>

                     </thead>
                     <tbody>
                        <?php $page = $this->request->params['paging']['transporter']['page'];
                        $limit = $this->request->params['paging']['transporter']['perPage'];
                        $counter = ($page * $limit) - $limit + 1;
                        if (isset($data) && !empty($data)) {
                           foreach ($data as $value) {
                              ?>
                              <tr>
                                 <td>
                                    <?php echo $counter; ?>
                                 </td>
                                 <td>
                                    <?php echo date("d-m-Y", strtotime($value['datefrom'])); ?>
                                 </td>
                                 <td>
                                    <?php echo ucfirst($value['vendor']['name']); ?>
                                 </td>
                                 <td>
                                    <?php echo ucfirst(strtolower($value['transport_to'])); ?>
                                 </td>
                                 <td>
                                    <?php echo ucfirst(strtolower($value['transport_from'])); ?>
                                 </td>
                                 <td>
                                    <?php echo strtoupper($value['vehicle_no']); ?>
                                 </td>
                                 <td style="text-align:right">
                                    <?php echo $value['gr_no']; ?>
                                 </td>
                                 <td style="text-align:right">
                                    <?php echo $value['weight']; ?>
                                 </td>
                                 <td style="text-align:right">
                                    <?php echo $value['freight']; ?>
                                 </td>
                                 <td> <strong>
                                       <a target="_blank"
                                          href="<?php echo SITE_URL . 'transporterupload/' . $value['upload']; ?>"
                                          title="Download" data-method="post" data-toggle="tooltip"><span
                                             class="fa fa-download fa-lg text-green"></span></a> &nbsp;

                                       <?php
                                       $user_id = $_SESSION['Auth']['User']['id'];
                                       $controllerName = $this->request->params['controller'];
                                       $actionName = $this->request->params['action'];
                                       $user_permission = $this->comman->finduserpermisson($user_id, $controllerName, $actionName);

                                       if ($user_permission['edit'] == '1') {
                                          echo $this->Html->link('', [
                                             'action' => 'edit',
                                             $value->id,
                                          ], ['class' => 'fas fa-edit', 'style' => 'font-size: 16px !important;']);
                                       }
                                       ?>
                                       &nbsp;
                                       <?php
                                       if ($user_permission['delete'] == '1') {
                                          echo $this->Html->link('', [
                                             'action' => 'status',
                                             $value->id,
                                             'N'
                                          ], [
                                             'class' => 'fas fa-trash-alt',
                                             'style' => 'font-size: 16px !important; color:#cd0404; margin-right:4px !important;',
                                             "onClick" => "javascript: return confirm('Are you sure do you want to delete this Transport details')"
                                          ]);
                                       } ?>
                                    </strong></td>
                              </tr>
                              <?php $counter++;
                           }
                        } ?>
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
<!-- content-wrapper -->
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
               url: '<?php echo ADMIN_URL; ?>vendors/gettransoptername',
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
   $(document).ready(function () {
      $("#mysubscription").bind("submit", function (event) {
         $.ajax({
            async: true,
            data: $("#mysubscription").serialize(),
            dataType: "html",
            type: "GET",
            url: "<?php echo ADMIN_URL; ?>transporter/searchitem",

            success: function (data) {
               $("#example23").html(data);
            },

         });
         return false;
      });

      $(document).on('click', '.pagination a', function (e) {
         var target = $(this).attr('href');
         var res = target.replace("/transporter/searchitem", "/transporter");
         window.location = res;
         return false;
      });
   });
</script>