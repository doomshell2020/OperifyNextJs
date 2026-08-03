<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Employee Manager

    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo ADMIN_URL; ?>dashboards"><i class="fa fa-home"></i>Home</a></li>
      <li><a href="<?php echo ADMIN_URL; ?>employees">Manage Employee</a></li>
      <li class="active"><?php echo ucfirst($employees['fname']); ?> <?php echo $employees['middlename']; ?></li>
    </ol>
  </section>

  <script>
  $(document).ready(function() {
    //prepare the dialog

    //respond to click event on anything with 'overlay' class
    $(".globalModals").click(function(event) {


      $('.modal-content').load($(this).attr("href")); //load content from href of link

    });
  });
  </script>

  <?php if ($selectid) {?>

  <script>
  var id = '<?php echo $selectid; ?>';
  $(document).ready(function() {


    $('#personal-tab').removeClass('active');
    $('.tab-pane').removeClass('active');
    $('#' + id + '-tab').addClass('active');
    $('#' + id).addClass('active');

  });
  </script>



  <?php }?>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <!--/.col (left) -->
      <!-- right column -->
      <div class="col-md-12">
        <!-- Horizontapr($students); die;l Form -->
        <div class="box box-info">

          <!-- /.box-header -->
          <!-- form start -->
          <div class="box-body">
            <?php echo $this->Flash->render(); ?>
            <!--<section class="content-header container-fluid">

        <h3 class="col-sm-4">
            <i class="fa fa-eye"></i> View Employee | <small><?php echo ucfirst($employees['fname']); ?> <?php echo $employees['middlename']; ?></small>        </h3>
    </section>-->
            <section class="content">




              <!---Start display student profile header with photo--->
              <div class="row">
                <div class="col-sm-12 col-xs-12">
                  <div class="well well-sm panel panel-default">
                    <div class="panel-body">
                      <div class="row">
                        <div class="col-xs-12 col-sm-4 text-center edusecArLangCss">

                          <?php if (!empty($employees['file'])) {?>
                          <img class="center-block img-circle img-thumbnail profile-img"
                            src="<?php echo SITE_URL; ?>webroot/uploads/<?php echo $employees['file']; ?>">
                          <div class="photo-edit-admin">

                            <?php } else {?>
                            <img class="center-block img-circle img-thumbnail profile-img"
                              src="<?php echo SITE_URL; ?>webroot/uploads/no-images.png">
                            <div class="photo-edit-admin">
                              <?php }?>
                              <?php if (empty($employees['file'])) {?>
                              <a class="photo-edit-icon-admin"
                                href="<?php echo SITE_URL; ?>admin/employees/employeesimage/<?php echo $employees['id'] ?>"
                                title="Add Profile Picture"><i class="fa fa-pencil"> </i></a>
                              <?php } else {?>
                              <a class="photo-edit-icon-admin"
                                href="<?php echo SITE_URL; ?>admin/employees/employeesimage/<?php echo $employees['id'] ?>"
                                title="Edit Profile Picture"><i class="fa fa-pencil"> </i></a>
                              <?php }?> </div>
                            <h3 class="text-primary">
                              <b> <?php echo ucfirst($employees['fname']); ?> <?php echo $employees['middlename']; ?>
                                <?php echo $employees['lname']; ?></b>
                            </h3>

                            <!---display profile completion status--->
                            <?php /*<div class="clearfix">
<span class="pull-left">Profile Completion</span>
<small class="pull-right"><? if($students['aadharno']){  echo "100%"; }elseif(count($doc_img)>0){ echo "80%"; }else if(!empty($employees['file'])){  echo "60%"; }else if($address['c_address']){ echo "40%"; }elseif($classessss['fullname']){  echo "20%"; }elseif($employees['fname']){  echo "10%"; }

?>
                            </small>
                          </div>
                          <div class="progress sm" style="background-color:#dadada">
                            <div style=<? if($students['aadharno']){ echo "width:100%" ; }elseif(count($doc_img)>0){
                              echo "width:80%"; }else if(!empty($employees['file'])){ echo "width:60%"; }else
                              if($address['c_address']){ echo "width:40%"; }elseif($classessss['fullname']){ echo
                              "width:20%"; }elseif($employees['fname']){ echo "width:10%"; }

                              ?> class="progress-bar progress-bar-green"></div>
                          </div>*/?>

                        </div>
                        <!--/col-->

                        <!--<div class="col-sm-4 teacher_about">
                    <h5>About Me</h5>
                    <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, </p>
                    </div>-->
                        <div class="col-xs-12 col-sm-8 edusecArLangCss teacher_about">
                          <h5>Contact Info</h5>

                          <p>
                            <strong>Teacher Id : </strong>
                            <?php echo $employees['id']; ?> </p>
                          <p>
                            <strong>Email/Login Id : </strong>
                            <?php echo $employees['email']; ?> <a class="photo-edit-icon-admin bg-aqua globalModals"
                              href="<?php echo SITE_URL; ?>admin/employees/change_email/<?php echo $employees['id'] ?>"
                              title="Change Email/Login ID" data-target="#globalModal" data-toggle="modal"><i
                                class="fa fa-pencil"></i></a> </p>
                          <p>
                            <strong>Mobile No : </strong>
                            <?php echo $employees['mobile']; ?> </p>

                          <p><strong>Status :</strong>
                            <?php if ($employees['status'] == 'Y') {?> <span class="label label-success"> Active</span>
                            <?php } else {?> <span class="label label-primary"> In-Active</span><?php }?> </p>
                        </div>
                        <!--/col-->
                        <div class="col-xs-12 col-sm-3 edusecArLangCss text-right">

                          <!--			<a class="btn btn-app" href="" target="_blank"><i class="fa fa-hand-o-up"></i> Attendance</a> <br>
						<a class="btn btn-app" href="" target="_blank"><i class="fa fa-calendar-o"></i> Timetable</a>	-->
                        </div>
                      </div>
                      <!--/row-->
                    </div>
                    <!--/panel-body-->
                  </div>
                  <!--/panel-->
                </div>
                <!--/col-->
              </div>
              <!--/row-->

              <div class="row edusec-user-profile">
                <div class="col-sm-12">
                  <ul class="nav nav-tabs responsive hidden-xs hidden-sm" id="profileTab">
                    <li class="active" id="personal-tab"><a
                        href="http://demo.edusec.org/student/stu-master/view?id=38#personal" data-toggle="tab"><i
                          class="fa fa-street-view"></i> Personal</a></li>
                    <!--
			<li id="guardians-tab"><a href="http://demo.edusec.org/student/stu-master/view?id=38#guardians" data-toggle="tab"><i class="fa fa-user"></i> Guardians</a></li>
			<li id="address-tab"><a href="http://demo.edusec.org/student/stu-master/view?id=38#address" data-toggle="tab"><i class="fa fa-home"></i> Address</a></li>
-->
                    <li id="documents-tab"><a href="http://demo.edusec.org/student/stu-master/view?id=38#documents"
                        data-toggle="tab"><i class="fa fa-file-text"></i> Documents</a></li>
                    <?php if ($role_id == '16') {?> <li id="Salary-tab"><a
                        href="http://demo.edusec.org/student/stu-master/view?id=38#payroll" data-toggle="tab"><i
                          class="fa fa-file-text"></i>Payroll</a></li>
                    <li id="leaves-tab"><a href="http://demo.edusec.org/student/stu-master/view?id=38#leaves"
                        data-toggle="tab"><i class="fa fa-file-text"></i>Leaves</a></li>
                    <li id="advance-tab"><a href="http://demo.edusec.org/student/stu-master/view?id=38#advance"
                        data-toggle="tab"><i class="fa fa-file-text"></i>Advance</a></li>
                    <li id="advance-tab"><a href="http://demo.edusec.org/student/stu-master/view?id=38#salary_history"
                        data-toggle="tab"><i class="fa fa-file-text"></i>Salary History</a></li>
                    <?php }?>

                    <!--
			<li id="history-tab"><a href="http://demo.edusec.org/student/stu-master/view?id=38#history" data-toggle="tab"><i class="fa fa-history"></i> Other Info</a></li>
-->
                  </ul>
                  <div id="content" class="tab-content responsive hidden-xs hidden-sm">
                    <div class="tab-pane active" id="personal">

                      <h3 class="page-header edusec-border-bottom-primary">
                        <i class="fa fa-info-circle"></i> Personal Details <div class="pull-right">
                          <a class="btn btn-primary btn-sm"
                            href="<?php echo SITE_URL; ?>admin/employees/add/<?php echo $employees['id'] ?>"
                            target="_blank"><i class="fas fa-pen"></i></a></div>
                      </h3>

                      <div class="box box-solid">
                        <div class="box-body no-padding table-responsive">
                          <table class="table tbl-profile">
                            <colgroup>
                              <col style="width:15%">
                              <col style="width:35%">
                              <col style="width:15%">
                              <col style="width:35%">
                            </colgroup>


                            <tbody>
                              <tr>
                                <td class="profile-label">Department</td>
                                <td><?php echo $employees['payroll_department']['name'] ?></td>
                                <td class="profile-label">Designation</td>
                                <td><?php echo $employees['payroll_designation']['name']; ?></td>
                              </tr>
                              <tr>
                                <td class="profile-label">Name</td>
                                <td><?php echo ucfirst($employees['fname']); ?>
                                  <?php echo ucfirst($employees['middlename']); ?>
                                  <?php echo ucfirst($employees['lname']); ?></td>
                                <td class="profile-label">Gender</td>
                                <td><?php echo $employees['gender']; ?></td>
                              </tr>
                              <tr>
                                <td class="profile-label">Date of Birth</td>
                                <td><?php echo date('d-m-Y', strtotime($employees['dob'])); ?></td>
                                <td class="profile-label">Nationality</td>
                                <td>Indian</td>
                              </tr>
                              <tr>
                                <td class="profile-label">Maritial Status</td>
                                <td><?php echo $employees['martial_status']; ?></td>
                                <td class="profile-label">Father/Husband Name</td>
                                <td><?php echo $employees['f_h_name']; ?></td>
                              </tr>
                              <tr>
                                <td class="profile-label">Slab Teachers</td>
                                <td><?php echo $employees['slab_type']; ?></td>
                                <td class="profile-label">Joining date</td>
                                <td><?php echo date('d-m-Y', strtotime($employees['joiningdate'])); ?></td>
                              </tr>


                            </tbody>
                          </table>
                        </div>
                        <!--/box-body-->
                      </div>
                      <!--/box-->
                    </div>
                    <div class="tab-pane" id="guardians">

                      <h3 class="page-header edusec-border-bottom-primary">
                        <i class="fa fa-files-o"></i> Guardians Detail<div class="pull-right edusecRtlPullLeft">
                          <?php if (empty($classessss['id'])) {?>
                          <a class="btn btn-primary btn-sm globalModals"
                            href="<?php echo SITE_URL; ?>admin/employees/addguardian?id=<?php echo $employees['id'] ?>"
                            data-target="#globalModal" data-toggle="modal">Add Guardian</a>
                          <?php } else {?>

                          <a class="btn btn-primary btn-sm globalModals"
                            href="<?php echo SITE_URL; ?>admin/employees/addguardian/<?php echo $classessss['id'] ?>"
                            data-target="#globalModal" data-toggle="modal">Edit Guardian </a>
                          <?php }?>

                        </div>
                      </h3>

                      <div class="box box-solid">
                        <div class="box-body no-padding table-responsive">

                          <table class="table tbl-profile">
                            <colgroup>
                              <col style="width:15%">
                              <col style="width:35%">
                              <col style="width:15%">
                              <col style="width:35%">
                            </colgroup>
                            <tbody>

                              <tr>
                                <td class="profile-label">Full Name</td>
                                <td><?php echo ucfirst($classessss['fullname']); ?></td>
                                <td class="profile-label">Relation</td>
                                <td><?php echo ucfirst($classessss['relation']); ?></td>
                              </tr>
                              <tr>
                                <td class="profile-label">Qualification</td>
                                <td><?php echo ucfirst($classessss['qualification']); ?></td>
                                <td class="profile-label">Occupation</td>
                                <td><?php echo $classessss['occupation']; ?></td>
                              </tr>
                              <tr>
                                <td class="profile-label">Total Income</td>
                                <td><?php echo $classessss['total_Income']; ?></td>
                                <td class="profile-label">Mobile No</td>
                                <td><?php echo $classessss['mobileno']; ?></td>
                              </tr>
                              <tr>
                                <td class="profile-label">Email Id</td>
                                <td><?php echo $classessss['emails']; ?></td>
                                <td class="profile-label">Address</td>
                                <td><?php echo $classessss['address']; ?></td>
                              </tr>

                            </tbody>
                          </table>
                        </div>
                        <!--/box-body-->
                      </div>
                      <!--/box-->
                      <!--/box-->
                    </div>
                    <div class="tab-pane" id="address">

                      <h3 class="page-header edusec-border-bottom-primary">
                        <i class="fa fa-info-circle"></i> Address Info <div class="pull-right">

                          <?php if ($address) {?>
                          <a id="update-data" class="btn btn-primary btn-sm globalModals"
                            href="<?php echo SITE_URL; ?>admin/employees/editaddress/<?php echo $address['id']; ?>"
                            data-target="#globalModal" data-toggle="modal" data-modal-size="modal-lg"><i
                              class="fa fa-pencil-square-o"></i> Edit</a>

                          <?php } else {?>
                          <a id="update-data" class="btn btn-primary btn-sm globalModals"
                            href="<?php echo SITE_URL; ?>admin/employees/editaddress?ids=<?php echo $ids; ?>"
                            data-target="#globalModal" data-toggle="modal" data-modal-size="modal-lg"><i
                              class="fa fa-pencil-square-o"></i> Add</a>



                          <?php }?> </div>
                      </h3>

                      <!---Start Current Address Block--->
                      <h4 class="edusec-border-bottom-warning page-header with-button profile-sub-header">
                        Current Address</h4>
                      <div class="box box-solid">
                        <div class="box-body no-padding table-responsive">
                          <table class="table tbl-profile">
                            <colgroup>
                              <col style="width:200px">
                              <col style="width:300px">
                              <col style="width:200px">
                              <col style="width:300px">
                            </colgroup>
                            <tbody>
                              <tr>
                                <td class="profile-label">Address</td>
                                <td><?php echo $address['c_address']; ?></td>
                                <td class="profile-label">Country</td>
                                <td><?php echo $address['CurCountry']['name']; ?></td>
                              </tr>
                              <tr>
                                <td class="profile-label">State</td>
                                <td><?php echo $address['CurStates']['name']; ?></td>
                                <td class="profile-label">City</td>
                                <td><?php echo $address['CurCity']['name']; ?></td>

                              </tr>

                              <tr>
                                <td class="profile-label">Pincode</td>
                                <td><?php echo $address['c_pincode']; ?></td>

                              </tr>
                            </tbody>
                          </table>
                        </div>
                        <!--/box-body-->
                      </div>
                      <!--/box-->

                      <!---Start Permenant Address Block--->
                      <h4 class="edusec-border-bottom-warning page-header with-button profile-sub-header">
                        Permanent Address</h4>

                      <div class="box box-solid">
                        <div class="box-body no-padding table-responsive">
                          <table class="table tbl-profile">
                            <colgroup>
                              <col style="width:200px">
                              <col style="width:300px">
                              <col style="width:200px">
                              <col style="width:300px">
                            </colgroup>
                            <tbody>
                              <tr>
                                <td class="profile-label">Address</td>
                                <td><?php echo $address['p_address']; ?></td>
                                <td class="profile-label">Country</td>
                                <td><?php echo $address['PerCountry']['name']; ?></td>
                              </tr>
                              <tr>
                                <td class="profile-label">State</td>
                                <td><?php echo $address['PerStates']['name']; ?></td>
                                <td class="profile-label">City</td>
                                <td><?php echo $address['PerCity']['name']; ?></td>

                              </tr>

                              <tr>
                                <td class="profile-label">Pincode</td>
                                <td><?php echo $address['p_pincode']; ?></td>

                              </tr>
                            </tbody>
                          </table>
                        </div>
                        <!--/box-body-->
                      </div>
                      <!--/box-->
                    </div>
                    <div class="tab-pane" id="documents">

                      <!---Display document upload title-->
                      <h4 class="page-header edusec-border-bottom-primary">
                        <i class="fa fa-files-o"></i> Uploaded Documents <div class="pull-right edusecRtlPullLeft">
                          <a class="btn btn-primary btn-sm globalModals"
                            href="<?php echo SITE_URL; ?>admin/employees/addocument?did=<?php echo $employees['id']; ?>"
                            data-target="#globalModal" data-toggle="modal">Add </a> </div>
                      </h4>

                      <div class="box box-solid">
                        <div class="box-body no-padding ">
                          <div id="w4" class="grid-view">
                            <table class="table table-striped table-bordered">
                              <thead>
                                <tr>
                                  <th>#</th>
                                  <th>Category</th>
                                  <th>Document Details</th>
                                  <th>Submited Date</th>

                                  <th>Download</th>

                                  <th class="action-column">Action</th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php $cnt = '1';if (count($doc_img) > 0) {foreach ($doc_img as $value) {?>
                                <tr>
                                  <td><?php echo $cnt++; ?></td>
                                  <td><?php echo $value['documentcategory']['categoryname']; ?></td>

                                  <td><?php echo $value['description']; ?></td>

                                  <td><?php echo date('d M Y', strtotime($value['created'])); ?></td>


                                  <td><a download="Document.<?php echo $value['ext']; ?>"
                                      href="<?php echo SITE_URL; ?>webroot/img/<?php echo $value['photo']; ?>"
                                      class="btn btn-default btn-sm" target="_blank"><i class="fa fa-download"
                                        aria-hidden="true"></i></a><br>


                                  <td>
                                    <div class="btn-group"><button id="w2"
                                        class="btn-primary btn-xs btn dropdown-toggle" data-toggle="dropdown"><i
                                          class="fa fa-gear"></i> <span class="caret"></span></button>

                                      <ul id="w3" class="dropdown-menu" style="left:-73px;min-width:50px">

                                        <li><a class="text-green globalModals"
                                            href="<?php echo SITE_URL; ?>admin/employees/addocument/<?php echo $value->id; ?>"
                                            data-placement="top" tabindex="-1" data-target="#globalModal"
                                            data-toggle="modal"><i class="fa fa-pencil-square-o"></i>EDIT</a></li>
                                        <li><a class="text-green"
                                            href="<?php echo SITE_URL; ?>admin/employees/deletedocument/<?php echo $value->id; ?>"
                                            data-toggle="tooltip" data-placement="top"
                                            data-confirm="Are you sure you want to delete this student"
                                            data-method="post" tabindex="-1"><i
                                              class="fa fa-pencil-square-o"></i>Delete</a></li>
                                      </ul>
                                    </div>
                                  </td>
                                  <?php }} else {?>


                                  <td colspan="8">
                                    <div class="empty">No results found.</div>
                                  </td>

                                  <?php }?>
                                </tr>


                              </tbody>
                            </table>
                          </div>
                        </div>
                        <!--/box-body-->
                      </div>
                      <!--/box-->
                    </div>
                    <div class="tab-pane" id="payroll">

                      <!---Display document upload title-->
                      <h4 class="page-header edusec-border-bottom-primary">
                        <i class="fa fa-files-o"></i> Payroll Details <div class="pull-right edusecRtlPullLeft">
                          <a class="btn btn-primary btn-sm"
                            href="<?php echo SITE_URL; ?>admin/employees/add/<?php echo $employees['id'] ?>"
                            target="_blank"><i class="fas fa-pen"></i></a>
                      </h4>

                      <div class="box box-solid">
                        <div class="box-body no-padding ">
                          <div id="w4" class="grid-view">
                            <table class="table table-striped table-bordered">
                              <colgroup>
                                <col style="width:15%">
                                <col style="width:35%">
                                <col style="width:15%">
                                <col style="width:35%">
                              </colgroup>


                              <tbody>
                                <tr>
                                  <td class="profile-label">Basic</td>
                                  <td><?php echo $salary['basic_salary']; ?></td>
                                  <td class="profile-label">DA</td>
                                  <td><?php echo $salary['da_amt']; ?></td>
                                </tr>
                                <tr>
                                  <td class="profile-label">HRA</td>
                                  <td><?php echo $salary['hra_amt']; ?></td>
                                  <td class="profile-label">CCA</td>
                                  <td><?php echo $salary['cca_amt']; ?></td>
                                </tr>
                                <tr>
                                  <td class="profile-label">Grade Pay</td>
                                  <td><?php echo $salary['grade_pay']; ?></td>
                                  <td class="profile-label">Special Allowance</td>
                                  <td><?php echo $salary['spl_all']; ?></td>
                                </tr>

                                <tr>
                                  <td class="profile-label">Total Salary</td>
                                  <td><?php echo $salary['total']; ?></td>
                                  <td class="profile-label">Bank Name</td>
                                  <td><?php echo $salary['bank_name']; ?></td>
                                </tr>
                                <tr>
                                  <td class="profile-label">Bank IFSC Code</td>
                                  <td><?php echo $salary['bank_ifsc']; ?></td>
                                  <td class="profile-label">Bank Account Number</td>
                                  <td><?php echo $salary['bank_account_no']; ?></td>
                                </tr>
                                <tr>
                                  <td class="profile-label">Payment mode</td>
                                  <td><?php $mode = $this->Comman->findpaymentmode($salary['payment_mode']);
echo $mode['name'];?></td>
                                  <td class="profile-label">ESI No.</td>
                                  <td><?php echo $salary['esi_no']; ?></td>
                                </tr>
                                <tr>
                                  <td class="profile-label">UAN No.</td>
                                  <td><?php echo $salary['uan_no']; ?></td>

                                </tr>




                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="tab-pane" id="leaves">

                      <!---Display document upload title-->
                      <h4 class="page-header edusec-border-bottom-primary">
                        <i class="fa fa-files-o"></i> Leaves Details <div class="pull-right edusecRtlPullLeft">
                          <a class="btn btn-primary btn-sm"
                            href="<?php echo SITE_URL; ?>admin/employees/add/<?php echo $employees['id'] ?>"
                            target="_blank"><i class="fas fa-pen"></i></a>
                      </h4>

                      <div class="box box-solid">
                        <div class="box-body no-padding ">
                          <div id="w4" class="grid-view">
                            <table id="example4" class="table table-striped table-bordered">
                              <thead>
                                <tr>
                                  <th>#</th>
                                  <th>Date</th>
                                  <th>Leave Type</th>
                                  <th>Reason</th>

                                </tr>
                              </thead>
                              <tbody>
                                <?php $page = $this->request->params['paging']['Services']['page'];
$limit = $this->request->params['paging']['Services']['perPage'];
$counter = ($page * $limit) - $limit + 1;
if (isset($leaves) && !empty($leaves)) {

    foreach ($leaves as $service) {
        ?>
                                <tr>
                                  <td><?php echo $counter; ?></td>
                                  <td> <?php echo $date = date("d-m-Y", strtotime($service['date'])); ?></td>
                                  <td> <?php echo $service['leave_type']; ?>
                                  </td>

                                  <td><?php echo $service['narration']; ?></td>

                                </tr>
                                <?php $counter++;}} else {?>
                                <tr>
                                  <td colspan="4">NO Data Available</td>
                                </tr>
                                <?php }?>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="tab-pane" id="advance">

                      <!---Display document upload title-->
                      <h4 class="page-header edusec-border-bottom-primary">
                        <i class="fa fa-files-o"></i> Advance <div class="pull-right edusecRtlPullLeft">
                          <a class="btn btn-primary btn-sm"
                            href="<?php echo SITE_URL; ?>admin/employees/add/<?php echo $employees['id'] ?>"
                            target="_blank"><i class="fas fa-pen"></i></a>
                          <a href="<?php echo SITE_URL; ?>admin/payroll/advance_deposit/<?php echo $employees['id']; ?>"
                            title="Advance Deposit" style="color:white" data-toggle="modal"
                            data-target=".global-drop-out" id="depform"> <i class="fa fa-reply-all"
                              aria-hidden="true"></i></a>
                      </h4>

                      <div class="box box-solid">
                        <div class="box-body no-padding ">
                          <div id="w4" class="grid-view">
                            <table class="table table-striped table-bordered">
                              <thead>
                                <tr>
                                  <th>#</th>
                                  <th>Advance Date</th>
                                  <th>Advance amount</th>
                                  <th>Amount Returned</th>
                                  <th>Balance </th>


                                </tr>
                              </thead>
                              <tbody id="example2">
                                <?php $page = $this->request->params['paging']['Services']['page'];
$limit = $this->request->params['paging']['Services']['perPage'];
$counter = ($page * $limit) - $limit + 1;
$adv_amt = 0;
$dep_amt = 0;
$bal = 0;
if (isset($advance_det) && !empty($advance_det)) {
    foreach ($advance_det as $service) { //pr($service);die;

        ?>
                                <tr>
                                  <?php $emp_det = $this->Comman->findemployeename($service['employee_id']);
        //pr($emp_det);die;

        //pr($adv_ret);die;
        $desg = $this->Comman->finddesignation($emp_det['designation_id']);
        //pr($desg);die;
        ?>
                                  <td><?php echo $counter; ?></td>

                                  <td>
                                    <?php if ($service['paydate'] != "") {echo date('d-m-Y', strtotime($service['paydate']));} else {echo date('d-m-Y', strtotime($service['deposit_date']));}?>
                                  </td>
                                  <td>
                                    <?php if (!empty($service['amount'])) {echo $service['amount'];
            $adv_amt += $service['amount'];} else {echo "-";}?>
                                  </td>
                                  <td>
                                    <?php if (!empty($service['deposit_amount'])) {echo $service['deposit_amount'];
            $dep_amt += $service['deposit_amount'];} else {echo '-';}?>
                                  </td>
                                  <td><?php echo $bal = $adv_amt - $dep_amt; ?></td>

                                  <!-- <a title="Cancel" class="modalcancel" style="margin-left:10px;" data-toggle="modal"
                    data-val="<?php echo $service['id']; ?>" data-target="#delete_Modal"><i
                      class="fa fa-remove"></i></a> -->
                                  <!-- <a href="<?php echo SITE_URL; ?>admin/payroll/delete_advance/<?php echo $service['id']; ?>"><i class="fa fa-trash" aria-hidden="true"></i></a></td> -->
                                </tr>
                                <?php $counter++;

    }?>
                                <tr>
                                  <td colspan="2"><b>Total</b></td>
                                  <td><b><?php echo $adv_amt; ?></b></td>
                                  <td><b><?php echo $dep_amt; ?></b></td>
                                  <td><b><?php echo $bal; ?></b></td>
                                </tr>
                                <?php } else {?>
                                <tr>
                                  <td colspan="6">No Data Available</td>
                                </tr>
                                <?php }?>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="tab-pane" id="salary_history">

                      <!---Display document upload title-->
                      <h4 class="page-header edusec-border-bottom-primary">
                        <i class="fa fa-files-o"></i> Payroll Details <div class="pull-right edusecRtlPullLeft">
                          <a class="btn btn-primary btn-sm"
                            href="<?php echo SITE_URL; ?>admin/employees/add/<?php echo $employees['id'] ?>"
                            target="_blank"><i class="fas fa-pen"></i></a>
                      </h4>

                      <div class="box box-solid">
                        <div class="box-body no-padding ">
                          <div id="w4" class="grid-view">
                            <table class="table table-striped table-bordered" id="example1">
                              <thead>
                                <th>S.No.</th>
                                <th>Salary Period</th>
                                <th>Fixed Salary</th>
                                <th>Actual Days</th>
                                <th>Earnings</th>
                                <th>Deductions</th>
                                <th>Net pay</th>
                                <th>By Cash</th>
                                <th>Advance</th>
                                <th>By Mode</th>
                                <th></th>
                              </thead>
                              <tbody>
                                <?php
$sal_cnt = 1;
foreach ($salary_det as $sal_value) {
    //pr($sal_value);die;
    $monthNum = $sal_value['month'];
    $monthName = date("F", mktime(0, 0, 0, $monthNum, 10));
    // Output: May
    ?>

                                <tr>
                                  <td><?php echo $sal_cnt; ?></td>
                                  <td><?php echo $monthName; ?>-<?php echo $sal_value['year'] ?></td>
                                  <td><?php echo $sal_value['fixed_salary']; ?></td>
                                  <td><?php echo $sal_value['actual_days']; ?></td>
                                  <td><?php echo $sal_value['total_earnings']; ?></td>
                                  <td><?php echo $sal_value['E_PF'] + $sal_value['ESIC']; ?></td>
                                  <td><?php echo $sal_value['net_salary']; ?></td>
                                  <td><?php echo $sal_value['payment_by_cash']; ?></td>
                                  <td><?php echo $sal_value['advance']; ?></td>
                                  <td><?php echo $sal_value['payment_by_mode']; ?></td>
                                  <td><a
                                      href="<?php echo ADMIN_URL; ?>payroll/salary_slip/<?php echo $sal_value['id']; ?>"
                                      title="Download Salary Slip" style="color:red; font-size:18px;"><i
                                        class="far fa-file-pdf"></i>
                                    </a></td>

                                </tr>
                                <?php $sal_cnt++;}?>

                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="tab-pane" id="history">
                      <h3 class="page-header edusec-border-bottom-primary">
                        <i class="fa fa-info-circle"></i> Other Info <div class="pull-right edusecRtlPullLeft">

                          <?php if ($students) {?>
                          <a id="update-data" class="btn btn-primary btn-sm globalModals"
                            href="<?php echo SITE_URL; ?>admin/employees/otherinfos/<?php echo $students['id']; ?>"
                            data-target="#globalModal" data-toggle="modal" data-modal-size="modal-lg"><i
                              class="fa fa-pencil-square-o"></i> Edit</a>

                          <?php } else {?>
                          <a id="update-data" class="btn btn-primary btn-sm globalModals"
                            href="<?php echo SITE_URL; ?>admin/employees/otherinfos?idss=<?php echo $ids; ?>"
                            data-target="#globalModal" data-toggle="modal" data-modal-size="modal-lg"><i
                              class="fa fa-pencil-square-o"></i> Add</a>



                          <?php }?> </div>
                      </h3>

                      <div class="box box-solid">
                        <div class="box-body no-padding table-responsive">
                          <table class="table tbl-profile">
                            <colgroup>
                              <col style="width:15%">
                              <col style="width:35%">
                              <col style="width:15%">
                              <col style="width:35%">
                            </colgroup>


                            <tbody>
                              <tr>
                                <td class="profile-label">Attendance Card ID</td>
                                <td><?php echo ucfirst($students['aadharno']); ?></td>
                                <td class="profile-label">Bank Account No</td>
                                <td><?php echo ucfirst($students['accountno']); ?></td>
                              </tr>
                              <tr>
                                <td class="profile-label">Reference</td>
                                <td><?php echo ucfirst($students['reference']); ?></td>
                                <td class="profile-label">Specialization</td>
                                <td><?php echo $students['specialization']; ?></td>
                              </tr>
                              <tr>
                                <td class="profile-label">Hobbies</td>
                                <td><?php echo $students['hobbies']; ?></td>
                              </tr>




                            </tbody>
                          </table>
                        </div>
                        <!--/box-body-->
                      </div>
                      <!--/box-->
                    </div>

            </section>

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



          </div>
          <!-- /.box-body -->
          <div class="box-footer">
            <?php
echo $this->Html->link('Back', [
    'action' => 'index',

], ['class' => 'btn btn-default']); ?>


          </div>
          <!-- /.box-footer -->

        </div>

      </div>
      <!--/.col (right) -->
    </div>
    <!-- /.row -->
  </section>
  <!-- /.content -->
</div>
<div class="modal fade global-drop-out" role="dialog" data-backdrop="true">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content modal-content-drop-out">

    </div>

  </div>
</div>
<script>
$(document).ready(function() {
  //prepare the dialog

  //respond to click event on anything with 'overlay' class
  $("#depform").click(function(event) {

    //$('.modal-content-drop-out').html('');
    //load content from href of link
    $('.modal-content-drop-out').load($(this).attr("href"));

  });
});
</script>
