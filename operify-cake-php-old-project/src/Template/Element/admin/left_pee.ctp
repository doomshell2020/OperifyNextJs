<?php $role_id = $this->request->session()->read('Auth.User.role_id');

if ($role_id == '1') { ?>
  <section class="tabs-menu">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-3">
          <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#home"><i class="fa fa-calendar-o" aria-hidden="true"></i> &nbsp; Academics<i class="fa fa-chevron-right pull-right" aria-hidden="true"></i></a></li>

            <li><a data-toggle="tab" href="#menu121"><i class="fa fa-calendar-o" aria-hidden="true"></i> &nbsp; Primary Examination Portal<i class="fa fa-chevron-right pull-right" aria-hidden="true"></i></a></li>
            <li><a data-toggle="tab" href="#menu1"><i class="fa fa-user" aria-hidden="true"></i> &nbsp; School Staff<i class="fa fa-chevron-right pull-right" aria-hidden="true"></i></a></li>

            <li><a data-toggle="tab" href="#menu11"><i class="fa fa-user" aria-hidden="true"></i> &nbsp; Prospectus Manager<i class="fa fa-chevron-right pull-right" aria-hidden="true"></i></a></li>
            <li><a data-toggle="tab" href="#menu2"><i class="fa fa-users" aria-hidden="true"></i> &nbsp; Student<i class="fa fa-chevron-right pull-right" aria-hidden="true"></i></a></li>

            <li><a data-toggle="tab" href="#menu4"><i class="fa fa-money" aria-hidden="true"></i> &nbsp; Fees<i class="fa fa-chevron-right pull-right" aria-hidden="true"></i></a></li>
            <li><a data-toggle="tab" href="#menu6"><i class="fa fa-line-chart" aria-hidden="true"></i> &nbsp; Reports Center<i class="fa fa-chevron-right pull-right" aria-hidden="true"></i></a></li>

            <li><a data-toggle="tab" href="#menu7"><i class="fa fa-wrench" aria-hidden="true"></i> &nbsp; Administration<i class="fa fa-chevron-right pull-right" aria-hidden="true"></i></a></li>
            <li><a data-toggle="tab" href="#menu8"><i class="fa fa-file-text-o" aria-hidden="true"></i> &nbsp; Document<i class="fa fa-chevron-right pull-right" aria-hidden="true"></i></a></li>
            <li><a data-toggle="tab" href="#menu46"><i class="fa fa-file-text-o" aria-hidden="true"></i> &nbsp; Notifications<i class="fa fa-chevron-right pull-right" aria-hidden="true"></i></a></li>

            <li><a data-toggle="tab" href="#menu9"><i class="fa fa-university" aria-hidden="true"></i> &nbsp; Library<i class="fa fa-chevron-right pull-right" aria-hidden="true"></i></a></li>

            <li><a data-toggle="tab" href="#menu10"><i class="fa fa-cog" aria-hidden="true"></i> &nbsp; Settings<i class="fa fa-chevron-right pull-right" aria-hidden="true"></i></a></li>
          </ul>
        </div>
        <!--col-sm-3-->

        <div class="col-sm-9">
          <div class="tab-content">
            <div id="home" class="tab-pane fade in active">
              <ul class="col-sm-3">
                <li><a href="#"><i class="fa fa-graduation-cap"></i> Class Management</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>classes/index"><i class="fa fa-graduation-cap"></i> Class</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>sections/index"><i class="fa fa-graduation-cap"></i> Section</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>Classections/index"><i class="fa fa-graduation-cap"></i> Class Sections</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>Classections/classteacher"><i class="fa fa-graduation-cap"></i> Class Teacher</a></li>
              </ul>

              <ul class="col-sm-3">
                <li><a href="#"><i class="fa fa-book"></i> Subject Management</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>subjects/index"><i class="fa fa-book"></i> Subject</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>Subjectclass/index"><i class="fa fa-book"></i> Subject Class</a></li>
              </ul>

              <ul class="col-sm-3">
                <li><a href="http://demo.edusec.org/timetable/default/index"><i class="fa fa-calendar-o"></i> Timetable</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>ClasstimeTabs/view"><i class="fa fa-calendar"></i> Class Timetable</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>ClasstimeTabs/stafftimetable"><i class="fa fa-calendar"></i> Staff Timetable</a></li>
                <li class="active"><a href="<?php echo ADMIN_URL; ?>Employeeattendance/substitute"><i class="fa fa-life-bouy"></i> Manage Substitution</a></li>


              </ul>


              <ul class="col-sm-3">
                <li><a href="#"><i class="fa fa-list"></i> Exam</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>examtypes/index"><i class="fa fa-link"></i> Exam Type</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>exams/add"><i class="fa fa-book"></i> Add Exam</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>exams/index"><i class="fa fa-book"></i> Exam</a></li>


                <li><a href="<?php echo ADMIN_URL; ?>studentexamresult/examcontrolview"><i class="fa fa-book"></i> Upload Exam Result</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>studentexamresult/schoolasticview"><i class="fa fa-book"></i>Upload Co-Scholastic Activity Result</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>studentexamresult/schoolexamview"><i class="fa fa-book"></i> Exam Result</a></li>
              </ul>
              <ul class="col-sm-4" style="margin-top: -46px;">
                <li><a href="#"><i class="fa fa-check-square-o"></i> Student Attendance</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>Studattends/attendence"><i class="fa fa-check-square"></i> Manage Student Attendance</a></li>
              </ul>
              <ul class="col-sm-3" style="margin-top: -90px;margin-left: 445px;">
                <li><a href="#"><i class="fa fa-calendar-o"></i> Academics</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>events/index"><i class="fa fa-flag"></i> Event Management</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>assignments/index"><i class="fa fa-object-group"></i> Post home work</a></li>
              </ul>
            </div>

            <div id="menu6" class="tab-pane fade">
              <ul class="col-sm-3">
                <li><a href="#"><i class="fa fa-line-chart"></i> Reports Center</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>report/student"><i class="fa fa-bar-chart"></i> Student</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>report/employee"><i class="fa fa-bar-chart"></i> Employee</a></li>

                <li><a href="<?php echo ADMIN_URL; ?>report/leave"><i class="fa fa-bar-chart"></i> Leave</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>report/sattendance"><i class="fa fa-bar-chart"></i> Student Attendance</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>report/employee_attendance"><i class="fa fa-bar-chart"></i> Employee Attendance</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>report/prospect"><i class="fa fa-bar-chart"></i> Prospectus Selling Report</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>report/prospect"><i class="fa fa-bar-chart"></i> Class Teacher Report </a></li>


              </ul>
              <ul class="col-sm-3">
                <li><a href="#"><i class="fa fa-check-square-o"></i> Fees Reports</a></li>

                <li><a href="<?php echo ADMIN_URL; ?>report/fees"><i class="fa fa-bar-chart"></i> Total Fees</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>report/collectionrecipiet"><i class="fa fa-users"></i> Fee Collection Report</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>report/cancelledrecipiet"><i class="fa fa-users"></i> Cancelled Receipts</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>report/prospectreport"><i class="fa fa-users"></i> Prospectus/Registration Report</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>report/dailyreport"><i class="fa fa-users"></i> Daily Summary Report</a></li>
              </ul>


              <ul class="col-sm-3">
                <li><a href="#"><i class="fa fa-check-square-o"></i>Enquiry</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>report/index"><i class="fa fa-users"></i> Enquiry Report</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>report/followup"><i class="fa fa-users"></i> Follow Up Report</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>report/rtestudent"><i class="fa fa-users"></i> RTE Students</a></li>
              </ul>



              <ul class="col-sm-3">
                <li><a href="#"><i class="fa fa-building-o"></i></i> Transport Report</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>report/transport"><i class="fa fa-bar-chart"></i>Fees Report </a></li>
              </ul>

              <ul class="col-sm-3">
                <li><a href="#"><i class="fa fa-building-o"></i></i> Library Report</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>ReportNew/booksreport"><i class="fa fa-book"></i> Books Report </a></li>
                <li><a href="<?php echo ADMIN_URL; ?>report/issuedbooksreport"><i class="fa fa-book"></i> Issued Books Report </a></li>
              </ul>

            </div>


            <div id="menu1" class="tab-pane fade">
              <ul class="col-sm-4">
                <li><a href="#"><i class="fa fa-user"></i> Employee Management</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>departments/index"><i class="fa fa-sitemap"></i> Department</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>designations/index"><i class="fa fa-signal"></i> Designation</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>employees/add"><i class="fa fa-user-plus"></i> Add Employee</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>employees/index"><i class="fa fa-reorder"></i> Manage Employee</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>employees/addcsv"><i class="fa fa-upload"></i> Import Employee</a></li>
              </ul>
              <ul class="col-sm-3">
                <li><a href="#"><i class="fa fa-check-square-o"></i> Employee Attendance</a></li>
                <li class="active"><a href="<?php echo ADMIN_URL; ?>shift/index"><i class="fa fa-clock-o"></i> Shift</a></li>
                <li class="active"><a href="<?php echo ADMIN_URL; ?>Employeeattendance/index"><i class="glyphicon glyphicon-check"></i> Take Attendance</a></li>
                <li class="active"><a href="<?php echo ADMIN_URL; ?>Employeeattendance/manage"><i class="fa fa-reorder"></i> Manage Attendance</a></li>
              </ul>
              <ul class="col-sm-3">
                <li><a href="#"><i class="fa fa-file-text-o"></i> Leave Management</a></li>
                <li class="active"><a href="<?php echo ADMIN_URL; ?>leavetype/index"><i class="fa fa-life-bouy"></i> Leave Type</a></li>
                <li class="active"><a href="<?php echo ADMIN_URL; ?>leaves/index"><i class="fa fa-life-bouy"></i> Leave </a></li>

              </ul>


            </div>


            <div id="menu121" class="tab-pane fade">
              <ul class="col-sm-4">
                <li><a href="#"><i class="fa fa-user"></i> Primary Report Card</a></li>
                <li><a href="<?php echo SITE_URL; ?>admin/Primarycentral/primaryindex"><i class="fa fa-sitemap"></i> Result</a></li>
                <li><a href="<?php echo SITE_URL; ?>admin/Primarycentral/primarycardmaster"><i class="fa fa-signal"></i> Pre Primary</a></li>
                <li><a href="<?php echo SITE_URL; ?>admin/Primarycentral/wordbankindex"><i class="fa fa-user-plus"></i> Word Bank</a></li>
                <li><a href="<?php echo SITE_URL; ?>admin/Primarycentral/teacherlinkindex"><i class="fa fa-reorder"></i> Teacher-Subject Manager</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>Primarycentral/pendingresultreport"><i class="fa fa-upload"></i> Pending Result Report</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>Primarycentral/attendenceimport"><i class="fa fa-upload"></i> Attendence Import</a></li>
              </ul>



            </div>


            <div id="menu11" class="tab-pane fade ">
              <ul class="col-sm-3">
                <li><a href="#"><i class="fa fa-users"></i> Prospectus</a></li>
                <li> <a id="update-data" href="<?php echo SITE_URL; ?>admin/enquires/prospectus_add" data-target="#globalModalkj" data-toggle="modal" data-modal-size="modal-lg"><i class="fa fa-pencil-square-o"></i> Add Prospectus</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>enquires/prospectus_index"><i class="fa fa-reorder"></i> View All Prospectus</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>students/applicant_add"><i class="fa fa-user-plus"></i> Register Student</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>report/prospect"><i class="fa fa-bar-chart"></i> Invite/Reject/Approved && Admission </a></li>

                <li><a href="<?php echo ADMIN_URL; ?>students/approvedprospect"><i class="fa fa-bar-chart"></i> Approved Registration</a></li>

                <li><a href="<?php echo ADMIN_URL; ?>students/rejectprospect"><i class="fa fa-bar-chart"></i> Rejected Registration</a></li>
              </ul>

            </div>
            <div class="modal" id="globalModalkj" tabindex="-1" role="dialog" aria-labelledby="esModalLabel" aria-hidden="true">
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

            <div id="menu2" class="tab-pane fade ">
              <ul class="col-sm-3">
                <li><a href="#"><i class="fa fa-users"></i> Admissions</a></li>

                <li><a href="<?php echo ADMIN_URL; ?>students/add"><i class="fa fa-user-plus"></i> Add Admission</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>students/index"><i class="fa fa-reorder"></i> Manage Admission</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>students/addcsv"><i class="fa fa-upload"></i> Import Admission</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>students/promote"><i class="fa fa-exchange"></i> Promote Student</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>report/rtestudent"><i class="fa fa-users"></i> RTE Students</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>students/drop"><i class="fa fa-ban"></i> Drop Student</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>students/genratecard"><i class="fa fa-user"></i> Genrate ID Card</a></li>


              </ul>
              <ul class="col-sm-3">
                <li><a href="#"><i class="fa fa-user-plus"></i> Enquiry</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>modes/index"><i class="fa fa-reorder"></i> Mode</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>enquires/index"><i class="fa fa-reorder"></i> View All Enquiry</a></li>

                <li><a href="<?php echo ADMIN_URL; ?>enquires/followenq"><i class="fa fa-reorder"></i> View All FollowUp </a></li>
              </ul>
              <ul class="col-sm-3">
                <li><a href="#"><i class="fa fa-line-chart"></i> Reports Center</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>report/student"><i class="fa fa-bar-chart"></i> Student</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>report/employee"><i class="fa fa-bar-chart"></i> Employee</a></li>

                <li><a href="<?php echo ADMIN_URL; ?>report/leave"><i class="fa fa-bar-chart"></i> Leave</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>report/sattendance"><i class="fa fa-bar-chart"></i> Student Attendance</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>report/employee_attendance"><i class="fa fa-bar-chart"></i> Employee Attendance</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>report/prospect"><i class="fa fa-bar-chart"></i> Prospectus Selling Report</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>report/prospect"><i class="fa fa-bar-chart"></i> Class Teacher Report </a></li>


              </ul>
            </div>

            <div id="menu4" class="tab-pane fade">

              <ul class="col-sm-4">

                <li><a href="#"><i class="fa fa-money"></i> Fee Master</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>banks/index"><i class="fa fa-sitemap"></i> Bank Master</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>feesheads/index"><i class="fa fa-sitemap"></i> Fees Heads</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>classfee"><i class="fa fa-exchange"></i> Class Fee Allocation</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>studentfees/view"><i class="fa fa-exchange"></i> Student Fee Allocation</a></li>
              </ul>

            </div>



            <div id="menu7" class="tab-pane fade">
              <ul class="col-sm-3">
                <li><a href="#"><i class="fa fa-bus"></i> Transport</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>locations/index"><i class="fa fa-map-marker"></i> Transport Location </a></li>
                <li><a href="<?php echo ADMIN_URL; ?>transports/index"><i class="fa fa-bus"></i> Transport</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>transportfees/index"><i class="fa fa-bus"></i> Transport Fees</a></li>
              </ul>
              <ul class="col-sm-3">
                <li><a href="#"><i class="fa fa-building"></i>Hostel</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>hostels/index"><i class="fa fa-building"></i> Manage Hostel</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>hostelrooms/"><i class="fa fa-building-o"></i> Manage Rooms</a></li>
              </ul>

            </div>


            <div id="menu8" class="tab-pane fade">
              <ul class="col-sm-3">
                <li><a href="#"><i class="fa fa-files-o"></i> Manage Documents</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>Documentcategory/index"><i class="fa fa-reorder"></i> Document Category</a></li>
              </ul>

            </div>

            <div id="menu9" class="tab-pane fade">

              <ul class="col-sm-3">

                <li>
                  <a href="#"><i class="fa fa-university"></i>Library
                  </a>
                </li>

                <li>
                  <a href="<?php echo ADMIN_URL; ?>BookCategories/index">
                    <i class="fa fa-sort-alpha-asc"></i> Book Category
                  </a>
                </li>

                <li>
                  <a href="<?php echo ADMIN_URL; ?>CupBoards/index">
                    <i class="glyphicon glyphicon-object-align-bottom"></i> Cup Board
                  </a>
                </li>

                <li>
                  <a href="<?php echo ADMIN_URL; ?>CupBoardShelves/index">
                    <i class="glyphicon glyphicon-equalizer"></i> Cup Board Shelf
                  </a>
                </li>

                <li>
                  <a href="<?php echo ADMIN_URL; ?>BookVendors/index">
                    <i class="fa fa-cart-plus"></i> Book Vendor
                  </a>
                </li>

                <li>
                  <a href="<?php echo ADMIN_URL; ?>BookStatus/index">
                    <i class="glyphicon glyphicon-tag"></i> Book Status
                  </a>
                </li>

                <li>
                  <a href="<?php echo ADMIN_URL; ?>Books/index">
                    <i class="glyphicon glyphicon-book"></i> Books
                  </a>
                </li>

                <li>
                  <a href="<?php echo ADMIN_URL; ?>issuebooks/index">
                    <i class="fa fa-book"></i> Issue Book
                  </a>
                </li>

                <li>
                  <a href="<?php echo ADMIN_URL; ?>ReturnRenewBooks/index">
                    <i class="fa fa-reply-all"></i> Return/Renew Book
                  </a>
                </li>

                <li>
                  <a href="<?php echo ADMIN_URL; ?>Fines/index">
                    <i class="fa fa-eject"></i> Fine
                  </a>
                </li>

              </ul>

            </div>
            <div id="menu46" class="tab-pane fade">
              <ul class="col-sm-3">
                <li><a href="#"><i class="fa fa-cogs"></i> Notifications</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>newsletters/add"><i class="fa fa-globe"></i> Compose</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>newsletters/sent"><i class="fa fa-globe"></i> Sent</a></li>

              </ul>

            </div>
            <div id="menu10" class="tab-pane fade">
              <ul class="col-sm-3">
                <li><a href="#"><i class="fa fa-cogs"></i> Configuration</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>country/index"><i class="fa fa-globe"></i> Country</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>states/index"><i class="fa fa-map-marker"></i> State</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>cities/index"><i class="fa fa-building-o"></i> City</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>houses/index"><i class="fa fa-home"></i> House Manager</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>sitesettings/add"><i class="fa fa-cogs"></i> Site Setting</a></li>
              </ul>
              <ul class="col-sm-3">
                <li><a href="#"><i class="fa fa-user-secret"></i> Page Manager</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>pages/index"><i class="fa fa-plus-square"></i> Page</a></li>

              </ul>

            </div>

          </div>
        </div>
        <!--col-sm-9-->

      </div>
      <!--row-->
    </div>
    <!--container-->
  </section>

<?php } else if ($role_id == '3') { ?>

  <section class="tabs-menu">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-3">
          <ul class="nav nav-tabs">
            <li class="">
              <a data-toggle="tab" href="#academics"><i class="fa fa-calendar-o"></i> Academics</a>
            </li>
            <!-- <li>
            <a data-toggle="tab" href="#hrms"><i class="fa fa-user"></i> School Staff</a>	</li>
            <li>
            <a data-toggle="tab" href="#student"><i class="fa fa-users"></i> Admissions</a>	</li> -->

            <li>
              <a data-toggle="tab" href="#library"><i class="fa fa-university"></i> Library</a>
            </li>
          </ul>

        </div>
        <div class="col-sm-9">
          <div class="tab-content">
            <div id="academics" class="tab-pane fade fade in active">



              <?php $findclasssection = $this->Comman->findclassectionsed();

              $m = date('m');
              if ($m < 03) {
                $d = date('y');
                $current = $d - 1;
                $dsa = '20' . $current;
                $yeard = $dsa . '-' . $d;
                $yeard = $yeard;
              } else {

                $date = date('Y');
                $date1 = date('y');
                $d = $date1 + 1;
                $yeard = $date . "-" . $d;
              } ?>


              <!--  <ul class="col-sm-3">
                  <li>
                  <a href="#"><i class="fa fa-list"></i> Exam </a></li>

                  <li><a href="<?php echo ADMIN_URL; ?>studentexamresult/view/<?php echo $findclasssection['class_id']; ?>/2"><i class="fa fa-upload"></i> Import Exam Result</a></li>
                  <li><a href="<?php echo ADMIN_URL; ?>studentexamresult/schoolasticview/<?php echo $findclasssection['class_id']; ?>/<?php echo $findclasssection['section_id']; ?>/<?php echo $yeard; ?>/Term1"><i class="fa fa-upload"></i> Import Coschoolactivity Result</a></li></ul> -->

              <?php if ($findclasssection['class_id'] || $findclasssection['section_id']) { ?>
                <ul class="col-sm-3">
                  <li><a href="#"><i class="fa fa-check-square-o"></i> Attendance</a></li>


                  <li><a href="<?php echo ADMIN_URL; ?>studattends/attendence/<?php echo $findclasssection['class_id']; ?>/<?php echo $findclasssection['section_id']; ?>/<?php echo $yeard; ?>"><i class="fa fa-check-square"></i> Student Attendance</a></li>
                </ul>
              <?php } ?>
              <ul class="col-sm-3">
                <li><a href="#"><i class="fa fa-calendar-o"></i> Home Work</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>assignments/index"><i class="fa fa-object-group"></i> View home work</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>ClasstimeTabs/teachertimetable"><i class="fa fa-object-group"></i> Add home work</a></li>
              </ul>
            </div>
            <div id="hrms" class="tab-pane">
              <div class="visible-sm visible-xs menu-box-header">
                <button aria-label="Close" class="close" type="button">
                  <span aria-hidden="true">×</span>
                </button>
                <h4><i class="fa fa-user"></i> School Staff</h4>
              </div>
              <div class="row">

                <div class="col-md-3 col-sm-4 col-xs-12">
                  <div class="menu-box">
                    <ul>
                      <li><a href="#"><i class="fa fa-user"></i> Employee Management</a></li>
                      <li><a href="<?php echo ADMIN_URL; ?>employees"><i class="fa fa-reorder"></i> Manage Employee</a></li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
            <!--./tab-pane-->
            <div id="student" class="tab-pane">

              <div class="row">
                <div class="col-md-3 col-sm-4 col-xs-12">
                  <div class="menu-box">
                    <ul>
                      <li><a href="#"><i class="fa fa-users"></i> Student</a></li>

                      <li><a href="<?php echo ADMIN_URL; ?>students/index/<?php echo $findclasssection['class_id']; ?>/<?php echo $findclasssection['section_id']; ?>"><i class="fa fa-reorder"></i> Manage Student</a></li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
            <!--./tab-pane-->

            <div id="library" class="tab-pane">
              <div class="visible-sm visible-xs menu-box-header">
                <button aria-label="Close" class="close" type="button">
                  <span aria-hidden="true">×</span>
                </button>
                <h4><i class="fa fa-university"></i> Library</h4>
              </div>

            </div>
            <!--./tab-pane-->
          </div><!-- end col -->
        </div>



      </div>
      <!--col-sm-3-->

    </div>
    <!--row-->

  </section>

<?php } else if ($role_id == '4') { ?>

  <section class="tabs-menu">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-3">
          <ul class="nav nav-tabs">
            <li class="">
              <a data-toggle="tab" href="#academics"><i class="fa fa-calendar-o"></i> Academics</a>
            </li>

          </ul>

        </div>
        <div class="col-sm-9">
          <div class="tab-content">
            <div id="academics" class="tab-pane fade fade in active">
              <?php $findclasssection = $this->Comman->findclassectionsed();

              $m = date('m');
              if ($m < 03) {
                $d = date('y');
                $current = $d - 1;
                $dsa = '20' . $current;
                $yeard = $dsa . '-' . $d;
                $yeard = $yeard;
              } else {

                $date = date('Y');
                $date1 = date('y');
                $d = $date1 + 1;
                $yeard = $date . "-" . $d;
              } ?>

              <ul class="col-sm-3">
                <li><a href="#"><i class="fa fa-list"></i> Exam</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>examtypes/index"><i class="fa fa-link"></i> Exam Category</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>exams/index"><i class="fa fa-book"></i> Exam</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>studentexamresult/examcontrolview/<?php echo $findclasssection['class_id']; ?>/2"><i class="fa fa-upload"></i> Import Exam Result</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>studentexamresult/schoolasticview/<?php echo $findclasssection['class_id']; ?>/<?php echo $findclasssection['section_id']; ?>/<?php echo $yeard; ?>/Term1"><i class="fa fa-upload"></i> Import Coschoolactivity Result</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>studentexamresult/schoolexamview"><i class="fa fa-book"></i> Exam Result</a></li>
              </ul>

            </div>
          </div><!-- end col -->
        </div>
      </div>
      <!--col-sm-3-->
    </div>
    <!--row-->
  </section>

<?php } else if ($role_id == '5' || $role_id == '8') { ?>

  <section class="tabs-menu">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-3">
          <ul class="nav nav-tabs">
            <li><a data-toggle="tab" href="#menu11"><i class="fa fa-user" aria-hidden="true"></i> &nbsp; Prospectus Manager<i class="fa fa-chevron-right pull-right" aria-hidden="true"></i></a></li>
            <li><a data-toggle="tab" href="#menu2"><i class="fa fa-users" aria-hidden="true"></i> &nbsp; Admission<i class="fa fa-chevron-right pull-right" aria-hidden="true"></i></a></li>
            <li><a data-toggle="tab" href="#menu4"><i class="fa fa-money" aria-hidden="true"></i> &nbsp; Fees Master<i class="fa fa-chevron-right pull-right" aria-hidden="true"></i></a></li>
            <li><a data-toggle="tab" href="#menu6"><i class="fa fa-line-chart" aria-hidden="true"></i> &nbsp; Reports Center<i class="fa fa-chevron-right pull-right" aria-hidden="true"></i></a></li>
            <li><a data-toggle="tab" href="#menu64"><i class="fa fa-users" aria-hidden="true"></i> &nbsp; School Staff<i class="fa fa-chevron-right pull-right" aria-hidden="true"></i></a></li>
          </ul>

        </div>
        <div class="col-sm-9">
          <div class="tab-content">
            <div id="menu11" class="tab-pane fade in active">
              <ul class=" col-sm-6">
                <li><a href="#"><i class="fa fa-users"></i> Prospectus</a></li>
                <li> <a id="update-data" href="<?php echo SITE_URL; ?>admin/enquires/prospectus_add" data-target="#globalModalkj" data-toggle="modal" data-modal-size="modal-lg"><i class="fa fa-pencil-square-o"></i> Add Prospectus</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>enquires/prospectus_index"><i class="fa fa-reorder"></i> View All Prospectus</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>students/applicant_add"><i class="fa fa-user-plus"></i> Register Student</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>report/prospect"><i class="fa fa-search"></i> Invite/Reject/Approved && Admission </a></li>

                <li><a href="<?php echo ADMIN_URL; ?>students/approvedprospect"><i class="fa fa-check"></i> Approved Registration</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>students/rejectprospect"><i class="fa fa-close"></i> Rejected Registration</a></li>
              </ul>
            </div>
            <div class="modal" id="globalModalkj" tabindex="-1" role="dialog" aria-labelledby="esModalLabel" aria-hidden="true">
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

            <div id="menu2" class="tab-pane fade ">
              <ul class="col-sm-3">
                <li><a href="#"><i class="fa fa-users"></i> Admission</a></li>

                <li><a href="<?php echo ADMIN_URL; ?>students/add"><i class="fa fa-user-plus"></i> Add Admission</a></li>
                <li><a target="_blank" href="<?php echo ADMIN_URL; ?>students/index"><i class="fa fa-reorder"></i> Manage Admission</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>students/drop"><i class="fa fa-ban"></i> Drop Out Student</a></li>
                <!--
                <li><a href="<?php echo ADMIN_URL; ?>students/promote"><i class="fa fa-exchange"></i>  Promote Student</a></li>
                -->
                <li><a href="<?php echo ADMIN_URL; ?>students/smsmanager"><i class="fa fa-envelope"></i> SMS Manager</a></li>
                <!--<li><a href="<?php echo ADMIN_URL; ?>students/addcsv"><i class="fa fa-upload"></i>     Import Admission</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>students/promote"><i class="fa fa-exchange"></i>     Promote Student</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>students/drop"><i class="fa fa-ban"></i> Drop Student</a></li>-->
              </ul>

            </div>

            <div id="menu4" class="tab-pane fade">

              <ul class="col-sm-4">

                <li><a href="#"><i class="fa fa-money"></i> Fee Master</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>classfee"><i class="fa fa-exchange"></i> Class Fee Structure</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>feesheads/index"><i class="fa fa-sitemap"></i> Fees Heads</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>DiscountCategory/index"><i class="fa fa-percent"></i> Discount Scheme</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>studentfees/view"><i class="fa fa-inr"></i> Deposit Fee</a></li>

              </ul>

            </div>
            <div id="menu64" class="tab-pane fade">

              <ul class="col-sm-4">

                <li><a href="#"><i class="fa fa-users"></i> School Staff</a></li>

                <li><a href="<?php echo ADMIN_URL; ?>Classections/classteacher"><i class="fa fa-reorder"></i> Class Teacher/Co-Class Teacher</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>employees"><i class="fa fa-reorder"></i> Manage Teachers</a></li>

              </ul>

            </div>


            <div id="menu6" class="tab-pane fade">

              <ul class="col-sm-4">
                <li><a href="#"><i class="fa fa-check-square-o"></i> Fees Reports</a></li>

                <!--  <li><a href="<?php echo ADMIN_URL; ?>report/fees"><i class="fa fa-bar-chart"></i> Total Fees</a></li> -->
                <li><a target="_blank" href="<?php echo ADMIN_URL; ?>report/collectionrecipiet"><i class="fa fa-inr"></i> Fee Collection Report</a></li>

                <li><a target="_blank" href="<?php echo ADMIN_URL; ?>report/students_all"><i class="fa fa-bar-chart"></i> Defaulter Reports</a></li>

                <li><a target="_blank" href="<?php echo ADMIN_URL; ?>report/dailyreport"><i class="fa fa-book"></i> Daily Summary Report</a></li>
                <li><a target="_blank" href="<?php echo ADMIN_URL; ?>report/bankreport"><i class="fa fa-bank"></i> Bank Report </a></li>

                <li><a href="<?php echo ADMIN_URL; ?>enquires/prospectus_index"><i class="fa fa-book"></i> Prospectus Selling Report</a></li>

                <li><a href="<?php echo ADMIN_URL; ?>report/registredstudents"><i class="fa fa-user-plus"></i> Registration Report</a></li>

                <li><a href="<?php echo ADMIN_URL; ?>report/cancelledrecipiet"><i class="fa fa-book"></i> Search Fee Receipt </a></li>

              </ul>
              <ul class="col-sm-4">
                <li><a href="#"><i class="fa fa-line-chart"></i> Academic Reports</a></li>

                <li><a href="<?php echo ADMIN_URL; ?>report/student"><i class="fa fa-users"></i> Student Information</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>report/admitstudent"><i class="fa fa-bar-chart"></i> Student Admission Summary</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>report/dropedstudent"><i class="fa fa-bar-chart"></i> Student Dropped Summary</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>report/document"><i class="fa fa-reorder"></i> Student Document Report</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>report/studentgender"><i class="fa fa-users"></i> Gender Report</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>report/studentgenderhouse"><i class="fa fa-intersex"></i> Gender House Report </a></li>
                <li><a href="<?php echo ADMIN_URL; ?>report/dropoutreport"><i class="fa fa-ban"></i> Drop Out Report </a></li>
              </ul>
              <ul class="col-sm-4">
                <li><a href="#"><i class="fa fa-line-chart"></i> Academic Reports</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>report/smsreport"><i class="fa fa-envelope"></i> SMS Delivery Report </a></li>
                <li><a href="<?php echo ADMIN_URL; ?>report/rtestudent"><i class="fa fa-registered"></i> RTE Students Report</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>report/optionalsubjectlist"><i class="fa fa-book"></i> Optional Subjects Report </a></li>
                <li><a href="<?php echo ADMIN_URL; ?>primarycentral/studentimagereport"><i class="fa fa-bar-chart"></i> Student Image Report</a></li>
                <li><a target="_blank" href="<?php echo ADMIN_URL; ?>report/disabilitys"><i class="fa fa-wheelchair"></i> K.Students Report</a></li>
                <li><a target="_blank" href="<?php echo ADMIN_URL; ?>report/cast"><i class="fa fa-reorder"></i> Caste/ Religion Report <b>(Enrollment)</b></a></li>
                <li><a target="_blank" href="<?php echo ADMIN_URL; ?>report/castrepeaters"><i class="fa fa-reorder"></i> Caste/ Religion Report <b>(Repeaters)</b></a></li>
                <li><a target="_blank" href="<?php echo ADMIN_URL; ?>report/age"><i class="fa fa-child"></i> Age Report</a></li>
                <!--<li><a href="<?php echo ADMIN_URL; ?>report/leave"><i class="fa fa-bar-chart"></i> Leave</a></li>-->
              </ul>

            </div>
          </div><!-- end col -->
        </div>

      </div>
      <!--col-sm-3-->

    </div>
    <!--row-->

  </section>

<?php } ?>
<!--<script>
$(document).ready(function(){
    $(".navbar-toggle").click(function(){
        $(".tabs-menu").toggle();
    });

});
</script>-->