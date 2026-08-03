
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
    
      
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
       <!--/.col (left) -->
        <!-- right column -->
        <div class="col-md-12">
          <!-- Horizontal Form -->
          <div class="box box-info">
		    <div class="box-header with-border">
		      <h3 class="box-title">Student Manager</h3>
		    </div>
            <!-- /.box-header -->
            <!-- form start -->
                   <div class="box-body">
		        <section id="">



     
    <section class="content-header container-fluid">
        <h3 class="col-sm-4">
            <i class="fa fa-eye"></i> View Student | <small><?php  echo ucfirst($students['fname']);?> <?php  echo $students['middlename'];?> </small>        </h3>
        <ul class="breadcrumb col-sm-8"><li><a href="#"><i class="fa fa-home"></i>Home</a></li>
<li><a href="#">Student</a></li>
<li><a href="#">Manage Student</a></li>
<li class="active"><?php  echo ucfirst($students['fname']);?> <?php  echo $students['middlename'];?> </li>
</ul>    </section>
    <section class="content">
        

<p class="text-right btn-view-group">
	<a class="btn btn-primary" href="#" target="blank"><i class="fa fa-file-pdf-o"></i> Profile PDF</a></p>

<!---Start display student profile header with photo--->
<div class="row">
	<div class="col-sm-12 col-xs-12">
		<div class="well well-sm panel panel-default">
			<div class="panel-body">
				<div class="row">
					<div class="col-xs-12 col-sm-3 text-center edusecArLangCss">
						<img class="center-block img-circle img-thumbnail profile-img" src="images/image.png">						<div class="photo-edit-admin">
															<a class="photo-edit-icon-admin" href="#" title="Change Profile Picture" data-target="#globalModal" data-toggle="modal"><i class="fa fa-pencil"></i></a>													</div>
						<!---display profile completion status--->
						<div class="clearfix">
							<span class="pull-left">Profile Completion</span>
							<small class="pull-right">10%</small>
						</div>
						<div class="progress sm" style="background-color:#dadada">
							<div style="width: 10%;" class="progress-bar progress-bar-green"></div>
						</div>
					</div><!--/col-->
					<div class="col-xs-12 col-sm-6 edusecArLangCss">
						<h3 class="text-primary">
							<b><span class="glyphicon glyphicon-user"></span> <?php  echo ucfirst($students['fname']);?> <?php  echo $students['middlename'];?> <?php  echo $students['lname'];?> </b>
						</h3>
						<p>
							<strong>Student Id : </strong>
							<?php  echo $students['id'];?>						</p>
						<p>
							<strong>Email/Login Id : </strong>
							<?php  echo $students['username'];?>															<a class="photo-edit-icon-admin bg-aqua" href="#" title="Change Email/Login ID" data-target="#globalModal" data-toggle="modal"><i class="fa fa-pencil"></i></a>													</p>
						<p>
							<strong>Mobile No : </strong>
						<?php  echo $students['mobile'];?>						</p>
						<p>
							<strong>Status :</strong>
							<span class="label label-success">ACTIVE</span>						</p>
					</div><!--/col-->
					<div class="col-xs-12 col-sm-3 edusecArLangCss text-right">
						<a class="btn btn-app" href="#" target="_blank"><i class="fa fa-money"></i> Fees</a> <br>
						<a class="btn btn-app" href="#" target="_blank"><i class="fa fa-hand-o-up"></i> Attendance</a> <br>
						<a class="btn btn-app" href="#" target="_blank"><i class="fa fa-calendar-o"></i> Timetable</a>					</div>
				</div><!--/row-->
			</div><!--/panel-body-->
		</div><!--/panel-->
	</div><!--/col-->
</div><!--/row-->

<div class="row edusec-user-profile">
	<div class="col-sm-12">
		<ul class="nav nav-tabs responsive hidden-xs hidden-sm" id="profileTab">
			<li class="active" id="personal-tab"><a href="http://demo.edusec.org/student/stu-master/view?id=38#personal" data-toggle="tab"><i class="fa fa-street-view"></i> Personal</a></li>
			<li id="academic-tab"><a href="http://demo.edusec.org/student/stu-master/view?id=38#academic" data-toggle="tab"><i class="fa fa-graduation-cap"></i> Academic</a></li>
			<li id="guardians-tab"><a href="http://demo.edusec.org/student/stu-master/view?id=38#guardians" data-toggle="tab"><i class="fa fa-user"></i> Guardians</a></li>
			<li id="address-tab"><a href="http://demo.edusec.org/student/stu-master/view?id=38#address" data-toggle="tab"><i class="fa fa-home"></i> Address</a></li>
			<li id="documents-tab"><a href="http://demo.edusec.org/student/stu-master/view?id=38#documents" data-toggle="tab"><i class="fa fa-file-text"></i> Documents</a></li>
			<li id="history-tab"><a href="http://demo.edusec.org/student/stu-master/view?id=38#history" data-toggle="tab"><i class="fa fa-history"></i> History</a></li>
		</ul>
		<div id="content" class="tab-content responsive hidden-xs hidden-sm">
			<div class="tab-pane active" id="personal">
				
<h3 class="page-header edusec-border-bottom-primary">
	<i class="fa fa-info-circle"></i> Personal Details	<div class="pull-right">
					<a id="update-data" class="btn btn-primary btn-sm" href="http://demo.edusec.org/student/stu-master/update-personal?id=38&amp;tab=personal" oncontextmenu="return false;" data-target="#globalModal" data-toggle="modal" data-modal-size="modal-lg"><i class="fa fa-pencil-square-o"></i> Edit</a>			</div>
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
			<tbody><tr>
				<td class="profile-label">Title</td>
				<td colspan="3">Mrs.</td>
			</tr>
			<tr>
				<td class="profile-label">First Name</td>
				<td><?php  echo $students['fname'];?>	</td>
				<td class="profile-label">Last Name</td>
				<td><?php  echo $students['lname'];?></td>
			</tr>
			<tr>
				<td class="profile-label">Middle Name</td>
				<td><?php  echo $students['middlename'];?></td>
				<td class="profile-label">Gender</td>
				<td><?php  echo $students['gender'];?></td>
			</tr>
			<tr>
				<td class="profile-label">Date of Birth</td>
				<td><?php  echo $students['dob'];?></td>
				<td class="profile-label">Nationality</td>
				<td><?php  echo $students['nationality'];?></td>
			</tr>
			<tr>
				<td class="profile-label">Admission Category</td>
				<td>Not Set</td>
				<td class="profile-label">Religion</td>
				<td></td>
			</tr>
			<tr>
				<td class="profile-label">Bloodgroup</td>
				<td>Unknown</td>
				<td class="profile-label">Birthplace</td>
				<td></td>
			</tr>
			<tr>
				<td class="profile-label">Languages</td>
				<td></td>
			</tr>
		</tbody></table>
	</div><!--/box-body-->
</div><!--/box-->
			</div>
			<div class="tab-pane" id="academic">
				
<h3 class="page-header edusec-border-bottom-primary">
	<i class="fa fa-info-circle"></i> Academic Details	<div class="pull-right">
					<a class="btn btn-primary btn-sm" href="http://demo.edusec.org/student/stu-enrol-course/create?id=38" data-target="#globalModal" data-toggle="modal"><i class="fa fa-plus-square"></i> New Enroll</a>			</div>
</h3>

<div class="box box-solid">
	<div class="box-body no-padding">
		<div id="w1" class="grid-view"><div class="summary">Showing <b>1-1</b> of <b>1</b> item.</div>
<table class="table table-striped table-bordered"><thead>
<tr><th>#</th><th>Action</th><th>GR No.</th><th>Academic Year</th><th>Course</th><th>Batch</th><th>Section</th><th>Completion <br> Status</th><th>Status</th><th class="text-center" style="width:100px">Action</th></tr>
</thead>
<tbody>
<tr data-key="44"><td>1</td><td>ENROL</td><td></td><td>2016-17</td><td>Primary</td><td>Grade2</td><td>Grade2-Sec1</td><td><span class="label label-primary">In Progress</span></td><td><span class="label label-success">Active</span></td><td class="text-center" style="font-weight:bold"><div class="btn-group">
<button id="w2" class="btn-primary btn-xs btn dropdown-toggle" title="Select Apply Action" data-toggle="dropdown"><i class="fa fa-gear"></i> <span class="caret"></span></button>

<ul id="w3" class="dropdown-menu" style="left:-73px;min-width:50px"><li><a class="btn-modal text-yellow" href="http://demo.edusec.org/student/stu-enrol-course/apply-dropout?id=44" title="Apply Dropout" data-toggle="modal" data-placement="top" data-modal-size="modal-lg" data-target="#globalModal" tabindex="-1"><i class="fa fa-ban"></i>DROPOUT</a></li>
<li><a class="text-red" href="http://demo.edusec.org/student/stu-enrol-course/unenroll?id=44" title="" data-toggle="tooltip" data-placement="top" data-confirm="Are you sure you want to unenrolled this student into Primary ?" data-method="post" tabindex="-1" data-original-title="Apply unenroll into this course"><i class="fa fa-minus-circle"></i>UNENROLL</a></li></ul>
</div> <a href="http://demo.edusec.org/student/stu-enrol-course/view?id=44" title="" data-toggle="tooltip" data-placement="top" target="_blank" data-original-title="More Information">&nbsp;<i class="fa fa-info-circle fa-lg"></i> <br></a></td></tr>
</tbody></table>
</div>	</div><!--/box-body-->
</div><!--/box-->
			</div>
			<div class="tab-pane" id="guardians">
				
<h3 class="page-header edusec-border-bottom-primary">
	<i class="fa fa-info-circle"></i> Guardian Details	<div class="pull-right">
					<a id="update-guard-data" class="btn-sm btn btn-primary" href="http://demo.edusec.org/student/stu-master/addguardian?id=38" data-target="#globalModal" data-toggle="modal" data-modal-size="modal-lg"><i class="fa fa-user-plus"></i> Add Guardian</a>			</div>
</h3>

	<div class="alert alert-warning">
		<i class="icon fa fa-warning"></i>No guardians records found.	</div>
			</div>
			<div class="tab-pane" id="address">
				
<h3 class="page-header edusec-border-bottom-primary">
	<i class="fa fa-info-circle"></i> Address Info	<div class="pull-right">
					<a id="update-data" class="btn btn-primary btn-sm" href="http://demo.edusec.org/student/stu-master/update-address?id=38&amp;tab=address" data-target="#globalModal" data-toggle="modal" data-modal-size="modal-lg"><i class="fa fa-pencil-square-o"></i> Edit</a>			</div>
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
			<tbody><tr>
				<td class="profile-label">Address</td>
				<td colspan="3">-</td>
			</tr>
			<tr>
				<td class="profile-label">City</td>
				<td>-</td>
				<td class="profile-label">State</td>
				<td>-</td>
			</tr>
			<tr>
				<td class="profile-label">Country</td>
				<td>-</td>
				<td class="profile-label">House No</td>
				<td>-</td>
			</tr>
			<tr>
				<td class="profile-label">Pincode</td>
				<td>-</td>
				<td class="profile-label">Phone No</td>
				<td>-</td>
		</tr></tbody></table>
	</div><!--/box-body-->
</div><!--/box-->

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
			<tbody><tr>
				<td class="profile-label">Address</td>
				<td colspan="3">-</td>
			</tr>
			<tr>
				<td class="profile-label">City</td>
				<td>-</td>
				<td class="profile-label">State</td>
				<td>-</td>
			</tr>
			<tr>
				<td class="profile-label">Country</td>
				<td>-</td>
				<td class="profile-label">House No</td>
				<td>-</td>
			</tr>
			<tr>
				<td class="profile-label">Pincode</td>
				<td>-</td>
				<td class="profile-label">Phone No</td>
				<td>-</td>
			</tr>
		</tbody></table>
	</div><!--/box-body-->
</div><!--/box-->
			</div>
			<div class="tab-pane" id="documents">
				
<!---Display document upload title-->
<h4 class="page-header edusec-border-bottom-primary">
	<i class="fa fa-files-o"></i> Uploaded Documents	<div class="pull-right edusecRtlPullLeft">
					<a class="btn btn-primary btn-sm" href="http://demo.edusec.org/student/stu-master/upload-documents?id=38" data-target="#globalModal" data-toggle="modal"><i class="fa fa-plus-square"></i> Add</a>			</div>
</h4>

<div class="box box-solid">
	<div class="box-body no-padding table-responsive">
	<div id="w4" class="grid-view">
<table class="table table-striped table-bordered"><thead>
<tr><th>#</th><th>Category</th><th>Document Details</th><th>Submited Date</th><th>Status</th><th>Download</th><th>Approved/Disapproved</th><th class="action-column">Action</th></tr>
</thead>
<tbody>
<tr><td colspan="8"><div class="empty">No results found.</div></td></tr>
</tbody></table>
</div>	</div><!--/box-body-->
</div><!--/box-->
			</div>
			<div class="tab-pane" id="history">
				<h4 class="page-header edusec-border-bottom-primary">
	<i class="fa fa fa-history"></i> Student History</h4>

<div class="box box-solid">
	<div class="box-body no-padding">
		<div id="w5" class="grid-view"><div class="summary">Showing <b>1-1</b> of <b>1</b> item.</div>
<table class="table table-striped table-bordered"><thead>
<tr><th>#</th><th>Action</th><th>GR No.</th><th>Academic Year</th><th>Course</th><th>Batch</th><th>Section</th><th>Completion Status</th><th>Status</th><th class="text-center" style="width:100px">Action</th></tr>
</thead>
<tbody>
<tr data-key="44"><td>1</td><td><label>ENROL</label></td><td></td><td>2016-17</td><td>Primary</td><td>Grade2</td><td>Grade2-Sec1</td><td><span class="label label-primary">In Progress</span></td><td><span class="label label-success">Active</span></td><td class="text-center" style="font-weight:bold"> <a href="http://demo.edusec.org/student/stu-enrol-course/view?id=44" title="" data-toggle="tooltip" data-placement="top" target="_blank" data-original-title="More Information">&nbsp;<i class="fa fa-info-circle fa-lg"></i> <br></a></td></tr>
</tbody></table>
</div>	</div><!--/box-body-->
</div><!--/box-->
			</div>
		</div><div class="panel-group responsive visible-xs visible-sm" id="collapse-profileTab"><div class="panel panel-default"><div class="panel-heading"><h4 class="panel-title"><a class="accordion-toggle" data-toggle="collapse" data-parent="#collapse-profileTab" href="http://demo.edusec.org/student/stu-master/view?id=38#collapse-personal" aria-expanded="true"><i class="fa fa-street-view"></i> Personal</a></h4></div><div id="collapse-personal" class="panel-collapse collapse in" aria-expanded="true"></div></div><div class="panel panel-default"><div class="panel-heading"><h4 class="panel-title"><a class="accordion-toggle" data-toggle="collapse" data-parent="#collapse-profileTab" href="http://demo.edusec.org/student/stu-master/view?id=38#collapse-academic"><i class="fa fa-graduation-cap"></i> Academic</a></h4></div><div id="collapse-academic" class="panel-collapse collapse"></div></div><div class="panel panel-default"><div class="panel-heading"><h4 class="panel-title"><a class="accordion-toggle" data-toggle="collapse" data-parent="#collapse-profileTab" href="http://demo.edusec.org/student/stu-master/view?id=38#collapse-guardians"><i class="fa fa-user"></i> Guardians</a></h4></div><div id="collapse-guardians" class="panel-collapse collapse"></div></div><div class="panel panel-default"><div class="panel-heading"><h4 class="panel-title"><a class="accordion-toggle" data-toggle="collapse" data-parent="#collapse-profileTab" href="http://demo.edusec.org/student/stu-master/view?id=38#collapse-address"><i class="fa fa-home"></i> Address</a></h4></div><div id="collapse-address" class="panel-collapse collapse"></div></div><div class="panel panel-default"><div class="panel-heading"><h4 class="panel-title"><a class="accordion-toggle" data-toggle="collapse" data-parent="#collapse-profileTab" href="http://demo.edusec.org/student/stu-master/view?id=38#collapse-documents"><i class="fa fa-file-text"></i> Documents</a></h4></div><div id="collapse-documents" class="panel-collapse collapse"></div></div><div class="panel panel-default"><div class="panel-heading"><h4 class="panel-title"><a class="accordion-toggle" data-toggle="collapse" data-parent="#collapse-profileTab" href="http://demo.edusec.org/student/stu-master/view?id=38#collapse-history"><i class="fa fa-history"></i> History</a></h4></div><div id="collapse-history" class="panel-collapse collapse"></div></div></div><!--/tab-content-->

	</div><!--/col-->
</div><!--/row-->

    </section>




                

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
        

<script src="js/responsive-tabs.js.download"></script>
<script src="js/all-086bbfb7d04424036b1a91df8f15469b.js.download"></script>
<!--<script src="js/all-086bbfb7d04424036b1a91df8f15469b.js.download"></script>-->
<script type="text/javascript">(function($) {
      fakewaffle.responsiveTabs(['xs', 'sm']);
  })(jQuery);</script>
<script type="text/javascript">jQuery(document).ready(function () {
jQuery('.alert-auto-hide').fadeTo(7500, 500, function() {
			$(this).slideUp('slow', function() {
				$(this).remove();
			});
		});
jQuery('#w0-success-0').alert();
jQuery('#w1').yiiGridView({"filterUrl":"\/student\/stu-master\/view?id=38","filterSelector":"#w1-filters input, #w1-filters select, select[name=\u0027per-page\u0027]"});
jQuery('#w2').button();
jQuery('#w4').yiiGridView({"filterUrl":"\/student\/stu-master\/view?id=38","filterSelector":"#w4-filters input, #w4-filters select, select[name=\u0027per-page\u0027]"});
jQuery('#w5').yiiGridView({"filterUrl":"\/student\/stu-master\/view?id=38","filterSelector":"#w5-filters input, #w5-filters select, select[name=\u0027per-page\u0027]"});
});</script> 







</section>

		
		        
		      </div>
		      <!-- /.box-body -->
		      <div class="box-footer">
			<?php
			echo $this->Html->link('Back', [
			    'action' => 'index'
			   
			],['class'=>'btn btn-default']); ?>
		      
			
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




