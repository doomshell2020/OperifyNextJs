<?php
   $session = $this->request->session();
   $role_id = $session->read('Auth.User.role_id');
   ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
   <section class="content-header">
      <h1>
         Fine Manager
      </h1>
      <ol class="breadcrumb">
         <li><a href="<?php echo ADMIN_URL; ?>dashboards"><i class="fa fa-home"></i>Home</a></li>
         <li><a href="<?php echo ADMIN_URL; ?>ReturnRenewBooks/index">Fine Report</a></li>
      </ol>
   </section>
   <!-- Main content -->
   <section class="content">
      <div class="row">
         <div class="col-xs-12">
            <div class="box">
               <div>
                  <?php echo $this->Flash->render(); ?>
               </div>
               <!-- /.box-header -->
               <script>
                  $(document).ready(function() {
                    $("#TaskAdminCustomerForm").bind("submit", function(event) {
                      event.preventDefault();
                      $.ajax({
                        async: false,
                        type: "POST",
                        url: "<?php echo ADMIN_URL; ?>report/finesearch",
                        data: $("#TaskAdminCustomerForm").serialize(),
                        dataType: "html",
                        success: function(data) {
                          // alert(data);
                          $("#example1").html(data);
                        }
                      });
                    });
                  });
               </script>
               <script>
                  $(document).ready(function() {
                    $('#fdatefrom').datepicker({
                      dateFormat: 'yy-mm-dd',
                      onSelect: function(date) {
                        var selectedDate = new Date(date);
                        var endDate = new Date(selectedDate);
                        endDate.setDate(endDate.getDate());
                        $("#fendfrom").datepicker("option", "minDate", endDate);
                        $("#fendfrom").val(date);
                      }
                    });
                    $('#fendfrom').datepicker({
                      dateFormat: 'yy-mm-dd'
                    });
                  });
               </script>
               <div class="box-body">
                  <div class="manag-stu">
                     <?php echo $this->Form->create($fine, array('class' => 'form-horizontal', 'id' => 'TaskAdminCustomerForm')); ?>
                     <div class="form-group" style="margin-bottom:0px;">
                        <div class="col-sm-3">
                           <label>Date From</label>
                           <?php echo $this->Form->input('datefrom', array('class' => 'form-control', 'id' => 'fdatefrom', 'readonly', 'placeholder' => 'Date From', 'label' => false)); ?>
                        </div>
                        <div class="col-sm-3">
                           <label>Date To</label>
                           <?php echo $this->Form->input('dateto', array('class' => 'form-control', 'id' => 'fendfrom', 'readonly', 'placeholder' => 'Date To', 'label' => false)); ?>
                        </div>
                        <div class="col-sm-3">
                           <label>Holder Type</label>
                           <?php $holder_type = ['Student' => 'Student', 'Employee' => 'Employee'];
                            echo $this->Form->input('holder_type_id', array(
                              'class' => 'form-control', 'type' => 'select', 'empty' => 'Select Holder Type', 'id' => 'hol_id',
                              'options' => $holder_type, 'label' => false
                            ));
                          ?>
                        </div>
                        <div class="col-sm-3">
                           <label>Fine Type</label>
                           <?php $fine_type = array('Over due' => 'Over due', 'Book lost' => 'Book lost');  ?>
                           <?php echo $this->Form->input('fine_type', array(
                              'class' => 'form-control fine_type', 'type' => 'select', 'empty' => 'Select Fine Type',
                              'options' => $fine_type, 'label' => false,
                              )); ?>
                        </div>
                        <div class="col-sm-3">
                           <label></label>
                           <input type="submit" style="background-color:#00c0ef;color:#fff;width:100px !important; margin-right: 10px;margin-top: 10px;" class="btn btn4 btn_pdf myscl-btn date" value="Submit">
                           <button type="reset" style="background-color:#333;color:#fff;width:100px !important; margin-right: 10px;margin-top: 10px;" class="btn btn4 btn_pdf myscl-btn date">Reset</button>
                        </div>
                     </div>
                     <?php echo $this->Form->end(); ?>
                  </div>
               </div>
               <div style="text-align:right;">
                  <a id="" style="margin-right: 10px; margin-top: -80px;" class="btn btn-info btn-sm" href="<?php echo ADMIN_URL; ?>report/fine_excel"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel</a>
               </div>
               <div class="box-body" style="padding-top:0px;">
                  <div id="srch-rslt">
                     <div class="table-responsive">
                        <table id="example1" class="table table-bordered table-striped">
                           <thead>
                              <tr>
                                 <th>#</th>
                                 <th>Book Name</th>
                                 <th>Acc. No.</th>
                                 <th>Holder Name</th>
                                 <th>Holder Type</th>
                                 <th>Fine Type</th>
                                 <th>Fine Date</th>
                                 <th>Amount</th>
                              </tr>
                           </thead>
                           <tbody>
                              <?php
                                 $page = $this->request->params['paging']['books']['page'];
                                 $limit = $this->request->params['paging']['books']['perPage'];
                                 $counter = ($page * $limit) - $limit + 1;
                                 if (isset($finede) && !empty($finede)) {
                                   foreach ($finede as $work) { //pr($work);
                                     //pr($work);die;
                                     $hol = explode('-', $work['holder_name']);
                                     $asn = trim($work['asn_no']);
                                     $bn = $this->Comman->findbookname12($asn);
                                     $bname = $bn['name'];
                                     // pr($bn); die;
                                     //pr($hol); die;
                                     $d1 = $this->Time->format($work['sub_date'], 'dd-MM-Y');
                                     //pr($d1); die;
                                 ?>
                              <tr>
                                 <td><?php echo $counter; ?></td>
                                 <td><?php if (isset($bname)) {
                                    echo ucfirst($bname);
                                    } else {
                                    echo 'N/A';
                                    } ?></td>
                                 <td><?php if (isset($work['asn_no'])) {
                                    echo ucfirst($work['asn_no']);
                                    } else {
                                    echo 'N/A';
                                    } ?></td>
                                 <td><?php if (isset($work['holder_name'])) {
                                    echo ucfirst($work['holder_name']);
                                    } else {
                                    echo 'N/A';
                                    } ?></td>
                                 <td><?php if (isset($work['holder_type_id'])) {
                                    echo ucfirst($work['holder_type_id']);
                                    } else {
                                    echo 'N/A';
                                    } ?></td>
                                 <td><?php if (isset($work['fine_type'])) {
                                    echo ucfirst($work['fine_type']);
                                    } else {
                                    echo 'N/A';
                                    } ?></td>
                                 <td><?php if (!empty($d1)) {
                                    echo $d1;
                                    } else {
                                    echo "N/A";
                                    } ?></td>
                                 <td><?php if (isset($work['amount'])) {
                                    echo ucfirst($work['amount']);
                                    } else {
                                    echo 'N/A';
                                    } ?></td>
                              </tr>
                              <?php $am += $work['amount'];
                                 $counter++;
                                 } ?>
                              <tr>
                                 <td colspan="7"><b style="float: right;">Total</b></td>
                                 <td><span class="text-black">₹ </span><?php echo $am; ?></td>
                              </tr>
                              <?php    } else { ?>
                              <tr>
                                 <td colspan="8">NO Data Available</td>
                              </tr>
                              <?php } ?>
                           </tbody>
                        </table>
                     </div>
                  </div>
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
<!-- /.content-wrapper -->
<div class="modal" id="globalModal" tabindex="-1" role="dialog" aria-labelledby="esModalLabel" aria-hidden="true" style="display: none;">
   <div class="modal-dialog">
      <div class="modal-content testeingprogress">
         <div class="modal-body">
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
   $(document).ready(function() {
     //prepare the dialog
     //respond to click event on anything with 'overlay' class
     $(".global1").click(function(event) {
       //load content from href of link
       $('.modal-content').load($(this).attr("href"));
     });
   });
</script>
<script>
   $(document).ready(function() {
     //prepare the dialog
     //respond to click event on anything with 'overlay' class
     $(".global2").click(function(event) {
       //load content from href of link
       $('.testeingprogress').load($(this).attr("href"));
     });
   });
</script>