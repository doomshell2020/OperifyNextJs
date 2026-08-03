<!-- Content Wrapper. Contains page content -->

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Registered Student Report</h1>
        <ol class="breadcrumb">
            <li>
                <a href="<?php echo ADMIN_URL;?>students/approvedprospect"><i class="fa fa-thumbs-up"></i>Approved Applicants</a>
            </li>
            <li>
                <a href="<?php echo ADMIN_URL;?>students/rejectprospect"><i class="fa fa-home"></i>Rejected Applicants</a>
            </li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <?php echo $this->Flash->render(); ?>
                <div class="box">
                    <div class="box-header">
                        <div class="modal" id="globalModal" tabindex="-1" role="dialog" aria-labelledby="esModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
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
                        <style>
                            #load2 {
                                width: 100%;
                                height: 100%;
                                position: fixed;
                                z-index: 9999;
                                background-color: white !important;
                                background: url("<? echo SITE_URL; ?>images/Preloader_2.gif") no-repeat center center rgba(0, 0, 0, 0.75);
                            }
                        </style>
                        <script inline="1">
                            //<![CDATA[
                            $(document).ready(function () {
                                $("#TaskAdminCustomerFormss").bind("submit", function (event) {
                                    $.ajax({
                                        async: true,
                                        data: $("#TaskAdminCustomerFormss").serialize(),
                                        dataType: "html",
                                        beforeSend: function () {
                                            // setting a timeout
                                            $("#load2").css("display", "block");
                                        },
                                        type: "POST",
                                        url: "<?php echo ADMIN_URL ;?>report/registerdsearch",
                                        success: function (data) {
                                            $("#updt").show();
                                            $("#updt").html(data);
                                        },
                                        complete: function () {
                                            $("#load2").css("display", "none");
                                        },
                                    });
                                    return false;
                                });
                            });
                            //]]>
                        </script>

                        <?php echo $this->Form->create('Task',array('type'=>'file','inputDefaults'=>array('div'=>false,'label'=>false),'id'=>'TaskAdminCustomerFormss','class'=>'form-horizontal')); ?>

                        <div class="form-group registered_studntfrm">
                            <div class="col-md-4 col-sm-4 col-xs-6">
                                <script>
                                    $(function () {
                                        $("#enqiry_date").datepicker({
                                            dateFormat: "dd-mm-yy",
                                            changeMonth: true,
                                            onSelect: function (date) {
                                                var selectedDate = new Date(date);
                                                var endDate = new Date(selectedDate);
                                                endDate.setDate(endDate.getDate());

                                                $("#to_date").datepicker("option", "minDate", endDate);
                                                $("#to_date").val(date);
                                            },
                                            changeYear: true,
                                        });
                                        $("#to_date").datepicker({ dateFormat: "dd-mm-yy", changeMonth: true, changeYear: true });
                                    });
                                </script>

                                <script type="text/javascript">
                                    $(document).ready(function () {
                                        // Check or Uncheck All checkboxes
                                        $("#checkall").change(function () {
                                            var checked = $(this).is(":checked");
                                            if (checked) {
                                                $(".checkbox").each(function () {
                                                    $(this).prop("checked", true);
                                                });
                                            } else {
                                                $(".checkbox").each(function () {
                                                    $(this).prop("checked", false);
                                                });
                                            }
                                        });

                                        $(".checkbox").click(function () {
                                            if ($(".checkbox").length == $(".checkbox:checked").length) {
                                                $("#checkall").prop("checked", true);
                                            } else {
                                                $("#checkall").removeAttr("checked");
                                            }
                                        });
                                    });
                                </script>

                                <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
                                <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
                                <label for="inputEmail3" class="control-label">Registered Date From<span style="color: red;">*</span></label>
                                <?php echo $this->Form->input('from',array('class'=>'form-control','placeholder'=>'Registered Date From','id'=>'enqiry_date','readonly','autocomplete'=>'off','label' =>false)); ?>
                            </div>

                            <div class="col-md-4 col-sm-4 col-xs-6">
                                <label for="inputEmail3" class="control-label">Registered Date To<span style="color: red;">*</span></label>
                                <?php echo $this->Form->input('to',array('class'=>'form-control','placeholder'=>'Registered Date To','id'=>'to_date','readonly','autocomplete'=>'off','label' =>false)); ?>
                            </div>

                            <div class="col-md-4 col-sm-4 col-xs-6">
                                <label for="inputEmail3" class="control-label">Select Class</label>
                                <?php echo $this->Form->input('class_id',array('class'=>'form-control','id'=>'subjt','type'=>'select','empty'=>'Select Class','options'=>$classes,'label' =>false)); ?>
                            </div>

                            <div class="col-md-4 col-sm-4 col-xs-6">
                                <label for="inputEmail3" class="control-label">Pupil's Name</label>
                                <?php echo $this->Form->input('name',array('class'=>'form-control','placeholder'=>'Name','id'=>'enqiry_date','label' =>false)); ?>
                            </div>

                            <div class="col-md-4 col-sm-4 col-xs-6">
                                <label>Academic Year</label>
                                <?php echo $this->Form->input('acedmicyear', array('class' => 'form-control', 'type' => 'select', 'empty' => 'Select Academic Year', 'options' => $acd, 'label' => false, 'value' => $rolepresentyear)); ?>
                            </div>

                            <div class="col-md-4 col-sm-4 col-xs-6 sbmt_btndv text-xs-center">
                                <input type="submit" style="background-color: #00c0ef; color:#fff; width: 100px !important; margin-left: 10px;" class="btn btn4 btn_pdf myscl-btn date" value="Submit" />
                                <button type="reset" style="background-color: #333333; color:#fff; width: 100px !important; margin-left: 10px;" class="btn btn4 btn_pdf myscl-btn date">Reset</button>
                            </div>
                        </div>
                        <?php echo $this->Form->end(); ?>

                        <!-- /.box-header -->
                        <?php echo $this->Flash->render(); ?>
                    </div>
                    <div id="load2" style="display: none;"></div>
                    <div class="box-body" style="padding-top:0px;">
                      <div id="updt">
                          <div style="clear: both;"></div>
                          <div>
                              <a id="" style="margin-top: 0px;" class="btn btn-info btn-sm pull-right" href="<?php echo ADMIN_URL ;?>report/registerd_excel">
                                  <i class="fa fa-file-excel-o" aria-hidden="true"></i> Export Excel
                              </a>
                          </div>
                          <div style="clear: both;"></div>

                          <div class="table-responsive">
                              <table id="example1" class="table table-bordered table-striped">
                                  <thead>
                                      <tr>
                                          <th>#</th>
                                          <th>Form No.</th>
                                          <th>Academic Year</th>
                                          <th>Pupil's Name</th>
                                          <th>Father Mobile</th>
                                          <th>Mother Mobile</th>
                                          <th>Class</th>
                                          <th>Added On</th>
                                      </tr>
                                  </thead>
                                  <tbody id="example22">
                                      <?php $page = $this->request->params['paging']['Services']['page']; $limit = $this->request->params['paging']['Services']['perPage']; $counter = ($page * $limit) - $limit + 1; if(isset($t_enquiry) &&
                                      !empty($t_enquiry)){ foreach($t_enquiry as $service){ //pr($service); ?>
                                      <tr>
                                          <td><?php echo $counter;?></td>
                                          <td><?php if(isset($service['id'])){ echo $service['sno'];}else{ echo 'N/A'; } ?></td>
                                          <td><?php if(isset($service['acedmicyear'])){ echo $service['acedmicyear'];}else{ echo 'N/A'; } ?></td>
                                          <td><?php if(isset($service['fname'])){ echo ucfirst($service['fname']).'&nbsp;'.ucfirst($service['middlename']).'&nbsp;'.ucfirst($service['lname']);}else{ echo 'N/A'; } ?></td>
                                          <td><?php if(isset($service['f_phone'])){ echo ucfirst($service['f_phone']);}else{ echo 'N/A'; } ?></td>
                                          <td><?php if(isset($service['m_phone'])){ echo ucfirst($service['m_phone']);}else{ echo 'N/A'; } ?></td>
                                          <?php $cls=$this->Comman->showclasstitle($service['class_id']); //pr($cls); ?>
                                          <td><?php if(isset($cls['title'])){ echo ucfirst($cls['title']);}else{ echo 'N/A'; } ?></td>
                                          <?php $bls=$this->Comman->showboardtitle($service['enquire']['mode1_id']); //pr($cls); ?>
                                          <td><?php if(isset($service['created'])){ echo date('d-M-Y',strtotime($service['created']));}else{ echo 'N/A'; } ?></td>
                                      </tr>
                                      <?php $counter++;} }else{?>
                                      <tr>
                                          <td colspan="10" style="text-align: center;">NO Data Available</td>
                                      </tr>
                                      <?php } ?>
                                  </tbody>
                              </table>
                          </div>
                      </div>
                    </div>
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
                    <script src="<?php echo SITE_URL; ?>daterangepicker/daterangepicker.js"></script>
                    <link rel="stylesheet" href="<?php echo SITE_URL; ?>daterangepicker/daterangepicker.css" />
                    <script type="text/javascript">
                        //Date range picker with time picker
                        $("#reservationtime").daterangepicker({
                            timePicker: true,
                            timePickerIncrement: 30,
                            locale: {
                                format: "MM/DD/YYYY h:mm A",
                            },
                        });
                    </script>
                </div>
            </div>
        </div>
    </section>
</div>
