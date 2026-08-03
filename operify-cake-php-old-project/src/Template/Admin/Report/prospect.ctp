<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
      <b>Registered Student-</b> Invite/Reject/Approved && Admission
  </h1>
  <ol class="breadcrumb">
      <li><a href="<?php echo ADMIN_URL;?>students/approvedprospect"><i class="fa fa-thumbs-up"></i>Approved
        Applicants</a>
      </li>
      <li><a href="<?php echo ADMIN_URL;?>students/rejectprospect"><i class="fa fa-home"></i>Rejected Applicants</a>
      </li>
      <!-- <li><a href="<?php echo ADMIN_URL;?>report/prospect"><i class="fa fa-home"></i>Home</a></li> -->
      <li><a href="<?php echo ADMIN_URL;?>report/prospect">Prospectus Report</a></li>
  </ol>
</section>
<!-- Main content -->
<section class="content">
<div class="row">
<div class="col-xs-12">
<?php echo $this->Flash->render(); ?>
<div class="box">
<div class="box-header">
  <div class="add_applicantbtndv">
      <a id="update-data" class="mb-2 btn btn-primary" href="<? echo ADMIN_URL; ?>students/applicant_add">
        <i class="fa fa-pencil-square-o"></i>
        ADD APPLICANT
      </a>
  </div>
  <div class="modal" id="globalModal" tabindex="-1" role="dialog" aria-labelledby="esModalLabel"
      aria-hidden="true">
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
      background: url("<? echo SITE_URL; ?>images/Preloader_2.gif") no-repeat center center rgba(0, 0, 0, 0.75)
      }
  </style>
  <script inline="1">
      //<![CDATA[
      $(document).ready(function() {
        $("#TaskAdminCustomerFormss").bind("submit", function(event) {
          $.ajax({
            async: true,
            data: $("#TaskAdminCustomerFormss").serialize(),
            dataType: "html",
            beforeSend: function(xhr) {
            xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val())
              $('#load2').css("display", "block");
            },
            type: "POST",
            url: "<?php echo ADMIN_URL ;?>report/prospectsearch",
            success: function(data) {
              $("#updt").show();
              $("#updt").html(data);
            },
            complete: function() {
              $('#load2').css("display", "none");
            },
          });
          return false;
        });
      });
      //]]>
  </script>
  <?php echo $this->Form->create('Task',array('type'=>'file','inputDefaults'=>array('div'=>false,'label'=>false),'id'=>'TaskAdminCustomerFormss','class'=>'form-horizontal')); ?>
  <div class="form-group prospect_pgr_frm" style="display:flex; align-items:flex-end; flex-wrap:wrap; margin-bottom:0px; position:relative;">
      <div class="col-lg-3 col-md-4 col-sm-4 col-xs-6">
        <script>
            $(function() {
              $('#enqiry_date').datepicker({
                dateFormat: 'dd-mm-yy',
                changeMonth: true,
                onSelect: function(date) {

                  var selectedDate = new Date(date);
                  var endDate = new Date(selectedDate);
                  endDate.setDate(endDate.getDate());

                  $("#to_date").datepicker("option", "minDate", endDate);
                  $("#to_date").val(date);
                },
                changeYear: true
              });
              $('#to_date').datepicker({
                dateFormat: 'dd-mm-yy',
                changeMonth: true,
                changeYear: true
              });
            });
        </script>
        <script type='text/javascript'>
            $(document).ready(function() {
              // Check or Uncheck All checkboxes
              $("#checkall").change(function() {
                var checked = $(this).is(':checked');
                if (checked) {
                  $(".checkbox").each(function() {
                    $(this).prop("checked", true);
                  });
                } else {
                  $(".checkbox").each(function() {
                    $(this).prop("checked", false);
                  });
                }
              });

              $(".checkbox").click(function() {

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
        <label for="inputEmail3" class="control-label">Registered Date From<span
            style="color:red;">*</span></label>
        <?php echo $this->Form->input('from',array('class'=>'form-control','placeholder'=>'Registered Date From','id'=>'enqiry_date','label' =>false)); ?>
      </div>
      <div class="col-lg-3 col-md-4 col-sm-4 col-xs-6">
        <label for="inputEmail3" class="control-label">Registered Date To<span
            style="color:red;">*</span></label>
        <?php echo $this->Form->input('to',array('class'=>'form-control','placeholder'=>'Registered Date To','id'=>'to_date','label' =>false)); ?>
      </div>
      <div class="col-lg-3 col-md-4 col-sm-4 col-xs-6">
        <label for="inputEmail3" class="control-label">Select Class</label>
        <?php echo $this->Form->input('class_id',array('class'=>'form-control','id'=>'subjt','type'=>'select','empty'=>'Select Class','options'=>$classes,'label' =>false)); ?>
      </div>
      <div class="col-lg-3 col-md-4 col-sm-4 col-xs-6">
        <label for="inputEmail3" class="control-label">Pupil's Name</label>
        <?php echo $this->Form->input('name',array('class'=>'form-control','placeholder'=>'Name','id'=>'enqiry_date','label' =>false)); ?>
      </div>
      <div class="col-lg-3 col-md-4 col-sm-4 col-xs-6">
        <label>Academic Year</label>
        <?php
            echo
            $this->Form->input('acedmicyear', array('class' => 'form-control', 'type' => 'select', 'empty' => 'Select Academic Year', 'options' => $acd, 'label' => false, 'value' => $rolepresentyear));
            ?>
      </div>
      <div class="col-lg-3 col-md-4 col-sm-4 col-xs-6">
        <label for="inputEmail3" class="control-label">Status</label>
        <?php $st=array('5'=>'All','1'=>'Approved','2'=>'Pending','3'=>'Invited','4'=>'Rejected'); ?>
        <?php echo $this->Form->input('s_id',array('class'=>'form-control','id'=>'subjt','type'=>'select','empty'=>'Status','options'=>$st,'label' =>false)); ?>
      </div>
      <div class="col-lg-3 col-md-4 col-sm-4 col-xs-6">
        <div class="prospect_pgr_frm_center">
          <input type="submit" style="background-color:#00c0ef;color:#fff;width:100px !important; margin-left: 10px;"
            class="btn btn4 btn_pdf myscl-btn date" value="Submit">
          <button type="reset" style="background-color:#333;color:#fff;width:100px !important; margin-left: 10px;"
            class="btn btn4 btn_pdf myscl-btn date">Reset</button>
        </div>
      </div>
  </div>

  <?php echo $this->Form->end(); ?>
  <?php echo $this->Flash->render(); ?>
</div>
<div id="load2" style="display:none;"></div>
<div id="updt" style="position:relative">
  <?php echo $this->Form->create('Interaction',array('url'=>array('controller'=>'Report','action'=>'prospect_interaction'),'onsubmit'=>'return inputsHaveDuplicateValues();','type'=>'file','inputDefaults'=>array('div'=>false,'label'=>false),'id'=>'stud-attendance-form','class'=>'')); ?>
  <div class="col-sm-12" align="right" style="width: max-content; position: absolute; right: 0px; top: -55px;">
      <label></label>
      <input type="button" style="background-color:#00c0ef; color:white; width:max-content !important ; padding-right:10px; padding-left:10px; margin-left: 10px;"
        class="btn btn4 btn_pdf myscl-btn date" name="Invite" value="Invite" id="miv">
      <!--  <input type="submit" style="background-color:green;color:white;width:max-content !important ; padding-right:10px; padding-left:10px; margin-left: 10px;"
        class="btn btn4 btn_pdf myscl-btn date inv" name="Approve" value="Approve">-->
      <input type="submit" style="background-color:red;color:white;width:max-content !important ; padding-right:10px; padding-left:10px; margin-left: 10px;"
        class="btn btn4 btn_pdf myscl-btn date inv" name="Reject" value="Reject">

        <a id="" style="margin-left: 10px; margin-top: 0px;" class="btn btn-info btn-sm"
      href="<?php echo ADMIN_URL ;?>report/prospect_excel"><i class="fa fa-file-excel-o"
      aria-hidden="true"></i> Export Excel</a>
  </div>
  <div style="clear: both;"></div>

  <div style="clear: both;"></div>
  <div class="box-body">
      <script type="text/javascript">
        $('.inv').click(function() {
          var sd = $(".checkbox:checked").length;
          if (sd == 0) {
            alert("Please Select One Prospectus Atleast.")
            return false;
          } else {
            return true;
          }
        });
        $(document).ready(function() {
          $('#miv').click(function() {
            var sd = $(".checkbox:checked").length;
            if (sd == 0) {
              alert("Please Select One Prospectus Atleast.")
              return false;
            } else {

              $('#myModal').modal('toggle');
            }

          });
        });
      </script>
      <div class="modal fade" id="myModal" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 style="text-align: center;">Interaction Time</h4>
              </div>
              <div class="modal-body">
                  <div class="form-group">
                    <div id="g1">
                        <div class="col-sm-4" align="right">
                          <label for="inputEmail3" class="control-label g1">Interaction Time<span
                              style="color:red;">*</span></label>
                        </div>
                        <div class="col-sm-4">
                          <?php echo $this->Form->input('inter_time',array('class'=>'form-control g1','placeholder'=>'Time','id'=>'reservationtime','label' =>false,'required','style'=>'width: 318px')); ?>
                        </div>
                    </div>
                  </div>
              </div>
              <div class="modal-footer">
                  <input type="submit" style="background-color:#00c0ef;width:100px !important; margin-left: 10px;"
                    class="btn btn4 btn_pdf myscl-btn date inv" name="Invite" value="Invite">
              </div>
            </div>
        </div>
      </div>
      <div class="table-responsive">
        <table id="example1" class="table table-bordered table-striped">
            <thead>
              <tr>
                  <th width="10%"><input type="checkbox" id='checkall' /> Select All</th>
                  <th>#</th>
                  <th>Form No.</th>
                  <th>Academic Year</th>
                  <th>Pupil's Name</th>
                  <th>Father Mobile</th>
                  <th>Mother Mobile</th>
                  <th>Class</th>
                  <th>Added On</th>
                  <th>Receipt</th>
                  <th>Status</th>
                  <th>Action</th>
              </tr>
            </thead>
            <tbody id="example22">
              <?php $page = $this->request->params['paging']['Services']['page'];
                  $limit = $this->request->params['paging']['Services']['perPage'];
                  $counter = ($page * $limit) - $limit + 1;
                  if(isset($t_enquiry) && !empty($t_enquiry)){
                  foreach($t_enquiry as $service){  //pr($service);
                  ?>
              <tr>
                  <td><input type="checkbox" class='checkbox' name="p_id[]"
                    value="<?php echo $service['sno']; ?>" /> </td>
                  <td><?php echo $counter;?></td>
                  <td><?php if(isset($service['id'])){ echo $service['sno'];}else{ echo 'N/A'; } ?></td>
                  <td>
                    <?php if(isset($service['acedmicyear'])){ echo $service['acedmicyear'];}else{ echo 'N/A'; } ?>
                  </td>
                  <td>
                    <?php if(isset($service['fname'])){ echo ucfirst($service['fname']).'&nbsp;'.ucfirst($service['middlename']).'&nbsp;'.ucfirst($service['lname']);}else{ echo 'N/A'; } ?>
                  </td>
                  <td>
                    <?php if(isset($service['f_phone'])){ echo ucfirst($service['f_phone']);}else{ echo 'N/A'; } ?>
                  </td>
                  <td>
                    <?php if(isset($service['m_phone'])){ echo ucfirst($service['m_phone']);}else{ echo 'N/A'; } ?>
                  </td>
                  <?php $cls=$this->Comman->showclasstitle($service['class_id']); //pr($cls); ?>
                  <td><?php if(isset($cls['title'])){ echo ucfirst($cls['title']);}else{ echo 'N/A'; } ?></td>
                  <?php $bls=$this->Comman->showboardtitle($service['enquire']['mode1_id']); //pr($cls); ?>
                  <td>
                    <?php if(isset($service['created'])){ echo date('d-M-Y',strtotime($service['created']));}else{ echo 'N/A'; } ?>
                  </td>
                  <td> <a title="Print Registration Receipt" id="s<?php echo $service['id']; ?>" target="_blank"
                    href="<?php echo SITE_URL; ?>admin/Students/applicant_recipt/<?php echo $service['id']; ?>"><i
                    class="fa fa-file-text-o"></i></a>&nbsp;
                    <?php if($service['status_c']=='Y'){ ?><a title="Edit Registration"
                        href="<? echo SITE_URL; ?>admin/students/applicant_edit/<?php echo $service['id']; ?>"> <img
                        src="<? echo SITE_URL; ?>images/edit.png" style="width: 18px;"></a>&nbsp;
                    <a style="font-size: 19px;" title="Cancel Registration"
                        href="<?php echo SITE_URL; ?>admin/students/applicant_status/<?php echo $service['id']; ?>/N"
                        onclick="javascript: return confirm('Are you sure do you want to deactive this registration');"><i
                        class="fa fa-check"></i></a>
                    <?php }else{ ?>
                    <a style="font-size: 19px;" title="Activate Registration"
                        href="<?php echo SITE_URL; ?>admin/students/applicant_status/<?php echo $service['id']; ?>/Y"
                        onclick="javascript: return confirm('Are you sure do you want to active this registration');"><i
                        class="fa fa-ban"></i></a>
                    <?php } ?>
                  </td>
                  <td
                    style="<?php if($service['status_c']=='N'){ ?> color:red; <?php }elseif($service['status_i']=='Y'){ ?> color:green; <?php } else{ ?> color:red;<?php } ?>">
                    <?php if($service['status_c']=='N'){ echo "Canceled"; }else if($service['status_i']=='Y'){ echo "Invited";}else{ echo 'Pending'; } ?>
                  </td>
                  <td>
                    <?php  $rolepresent = $this->request->session()->read('Auth.User.role_id'); if($service['status_c']=='Y' && $rolepresent!=15){ ?>
                    <?php
                        echo $this->Html->link('Admission ?', [
                            'controller' => 'Report','action' => 'approvedprospectus',
                          $service['enquire']['formno']
                        ],['target'=>'_blank','class'=>'label label-success']);

                        }?>
                  </td>
              </tr>
              <?php $counter++;} }else{?>
              <tr>
                  <td colspan="10" style="text-align:center;">NO Data Available</td>
              </tr>
              <?php } ?>
              <script type='text/javascript'>
                  // Changing state of CheckAll checkbox
                  $(".checkbox").click(function() {

                    if ($(".checkbox").length == $(".checkbox:checked").length) {
                      $("#checkall").prop("checked", true);
                    } else {
                      $("#checkall").removeAttr("checked");
                    }

                  });
              </script>
            </tbody>
        </table>
      </div>
  </div>
  <?php echo $this->Form->end(); ?>
</div>
<?php
  if($ids){ ?>
<script type="text/javascript">
  // alert();
  $('#s<?php echo $ids; ?>')[0].click();
</script>
<?php } ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="<?php echo SITE_URL; ?>daterangepicker/daterangepicker.js"></script>
<link rel="stylesheet" href="<?php echo SITE_URL; ?>daterangepicker/daterangepicker.css">
<script type="text/javascript">
  //Date range picker with time picker
  $('#reservationtime').daterangepicker({
    timePicker: true,
    timePickerIncrement: 30,
    locale: {
      format: 'MM/DD/YYYY h:mm A'
    }
  });
</script>