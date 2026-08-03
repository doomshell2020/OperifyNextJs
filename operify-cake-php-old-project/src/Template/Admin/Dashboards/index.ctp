<head>
	<script src="<?php echo SITE_URL; ?>js/admin/Chart.min.js"></script>

	<script>

		$(function () {

			'use strict';

		  //-------------
		  //- PIE CHART -
		  //-------------
		  // Get context with jQuery - using jQuery's .get() method.
		  var pieChartCanvas = $("#pieChart").get(0).getContext("2d");
		  var pieChart = new Chart(pieChartCanvas);
		  var PieData = [
		  {
		  	value: <?php echo $report_data['paid_fees']; ?>,
		  	color: "#00a65a",
		  	highlight: "#00a65a",
		  	label: "Paid"
		  },
		  {
		  	value: <?php echo $report_data['due_fees']; ?>,
		  	color: "#f56954",
		  	highlight: "#f56954",
		  	label: "Due"
		  },
		  {
		  	value: <?php echo $report_data['discount_given']; ?>,
		  	color: "#00c0ef",
      		highlight: "#00c0ef",
		  	label: "Discount Given in"
		  },
		  {
		  	value: <?php echo $report_data['outstanding_fees']; ?>,
		  	color: "#f39c12",
		  	highlight: "#f39c12",
		  	label: "Outstanding"
		  }
		  ];
		  var pieOptions = {
		    //Boolean - Whether we should show a stroke on each segment
		    segmentShowStroke: true,
		    //String - The colour of each segment stroke
		    segmentStrokeColor: "#fff",
		    //Number - The width of each segment stroke
		    segmentStrokeWidth: 1,
		    //Number - The percentage of the chart that we cut out of the middle
		    percentageInnerCutout: 50, // This is 0 for Pie charts
		    //Number - Amount of animation steps
		    animationSteps: 100,
		    //String - Animation easing effect
		    animationEasing: "easeOutBounce",
		    //Boolean - Whether we animate the rotation of the Doughnut
		    animateRotate: true,
		    //Boolean - Whether we animate scaling the Doughnut from the centre
		    animateScale: false,
		    //Boolean - whether to make the chart responsive to window resizing
		    responsive: true,
		    // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
		    maintainAspectRatio: false,
		    //String - A legend template
		    legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>",
		    //String - A tooltip template
		    tooltipTemplate: "Rs.<%=value %> <%=label%> Fees"
		};
		  //Create pie or douhnut chart
		  // You can switch between pie and douhnut using the method below.
		  pieChart.Doughnut(PieData, pieOptions);
		  //-----------------
		  //- END PIE CHART -
		  //-----------------

		});

	</script>

	<!-- ****************************************************************************** -->

	<style type="text/css">
		.calendar-part{
			float: right;
		}
		
		.pi-text{
			width: 20%;
		}
	</style>

	<link href='<?php echo SITE_URL; ?>fullcalendar-3.2.0/fullcalendar.min.css' rel='stylesheet' />
	<link href='<?php echo SITE_URL; ?>fullcalendar-3.2.0/fullcalendar.print.min.css' rel='stylesheet' media='print' />
	<script src='<?php echo SITE_URL; ?>fullcalendar-3.2.0/lib/moment.min.js'></script>
	<script src='<?php echo SITE_URL; ?>fullcalendar-3.2.0/lib/jquery.min.js'></script>
	<script src='<?php echo SITE_URL; ?>fullcalendar-3.2.0/fullcalendar.min.js'></script> 

	<script>
		$(document).ready(function () {

			$('#w1').fullCalendar({"fixedWeekCount":false,"weekNumbers":true,"editable":true,"selectable":true,"eventLimit":false,"eventLimitText":"more Events","selectHelper":true,"displayEventTime":false,"displayEventTitle":false,"aspectRatio":1,"header":{"center":"title","left":"prev,next today","right":"month,agendaWeek,agendaDay"},"eventRender": function (event, element) {

			},"timeFormat":"hh(:mm) A","events":'<?php echo SITE_URL; ?>admin/events/viewevent'});

			$('#w1').fullCalendar('option', 'aspectRatio', 1.8);
		});
	</script>
</head>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>
			Dashboard
			<small>Control panel</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="active">Dashboard</li>
		</ol>
	</section>

	<!-- Main content -->
	<section class="content">
		<div>
			<?php echo $this->Flash->render(); ?>
		</div>
		<!-- Info boxes -->
		<div class="row">
			<div class="col-md-3 col-sm-6 col-xs-12">
				<div class="info-box">
					<span class="info-box-icon bg-aqua"><i class="fa fa-users" aria-hidden="true"></i></span>

					<div class="info-box-content">
						<span class="info-box-text">
							Total Students &nbsp;
							<a href="<?php echo $this->Url->build('/admin/students/index'); ?>" class="small-box-footer">
								<i class="fa fa-arrow-circle-right"></i>
							</a>
						</span>
						<span class="info-box-number"><?php echo $data_count['student_count']; ?></span>
					</div>
					<!-- /.info-box-content -->
				</div>
				<!-- /.info-box -->
			</div>
			<!-- /.col -->
			<div class="col-md-3 col-sm-6 col-xs-12">
				<div class="info-box">
					<span class="info-box-icon bg-red"><i class="fa fa-graduation-cap" aria-hidden="true"></i></span>

					<div class="info-box-content">
						<span class="info-box-text">
							Total Faculties &nbsp;
							<a href="<?php echo $this->Url->build('/admin/employees/index'); ?>" class="small-box-footer">
								<i class="fa fa-arrow-circle-right"></i>
							</a>
						</span>
						<span class="info-box-number"><?php echo $data_count['faculty_count']; ?></span>
					</div>
					<!-- /.info-box-content -->
				</div>
				<!-- /.info-box -->
			</div>
			<!-- /.col -->

			<!-- fix for small devices only -->
			<div class="clearfix visible-sm-block"></div>

			<div class="col-md-3 col-sm-6 col-xs-12">
				<div class="info-box">
					<span class="info-box-icon bg-green"><i class="ion ion-ios-people-outline"></i></span>

					<div class="info-box-content">
						<span class="info-box-text">
							Total Non-Teaching Staff &nbsp;
							<a href="<?php echo $this->Url->build('/admin/employees/index'); ?>" class="small-box-footer">
								<i class="fa fa-arrow-circle-right"></i>
							</a>
						</span>
						<span class="info-box-number"><?php echo $data_count['non_teaching_staff_count']; ?></span>
					</div>
					<!-- /.info-box-content -->
				</div>
				<!-- /.info-box -->
			</div>
			<!-- /.col -->
			<div class="col-md-3 col-sm-6 col-xs-12">
				<div class="info-box">
					<span class="info-box-icon bg-yellow"><i class="fa fa-building" aria-hidden="true"></i></span>

					<div class="info-box-content">
						<span class="info-box-text">
							Total Classes &nbsp;
							<a href="<?php echo $this->Url->build('/admin/classes/index'); ?>" class="small-box-footer">
								<i class="fa fa-arrow-circle-right"></i>
							</a>
						</span>
						<span class="info-box-number"><?php echo $data_count['class_count']; ?></span>
					</div>
					<!-- /.info-box-content -->
				</div>
				<!-- /.info-box -->
			</div>
			<!-- /.col -->
		</div>
		<!-- /.row --><div class="row">
<?php if($this->request->session()->read('Auth.User.role_id')=='1'){ ?>
		

			<!-- Pie-Chart: start -->

			<div class="col-md-6">
				<div class="box box-default">
					<div class="box-header with-border">
						<h3 class="box-title">Session Fees Report</h3>

						<div class="box-tools pull-right">
							<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
							<div class="btn-group">
								<button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown"><i class="fa fa-wrench"></i></button>
								<ul class="dropdown-menu" role="menu">
									<li><a href="<?php echo $this->Url->build('/admin/report/fees'); ?>">More info</a></li>
								</ul>
							</div>
							<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
						</div>
					</div>
					<!-- /.box-header -->
					<div class="box-body" style="padding: 26px 10px;">
						<div class="row">
							<div class="col-md-8">
								<div class="chart-responsive">
									<canvas id="pieChart" height="150"></canvas>
								</div>
								<!-- ./chart-responsive -->
							</div>
							<!-- /.col -->
							<div class="col-md-4">
								<ul class="chart-legend clearfix">
									<!-- <li><i class="fa fa-circle-o text-blue"></i> Total Fees</li> -->
									<li><i class="fa fa-circle-o text-green"></i> Paid Fees</li>
									<li><i class="fa fa-circle-o text-red"></i> Due Fees</li>
									<li><i class="fa fa-circle-o text-aqua"></i> Discount Given</li>
									<li><i class="fa fa-circle-o text-yellow"></i> Outstanding Fees</li>
								</ul>
							</div>
							<!-- /.col -->
						</div>
						<!-- /.row -->
					</div>
					<!-- /.box-body -->
					<?php setlocale(LC_MONETARY, 'en_IN'); ?>
					<div class="box-footer no-padding">
						<ul class="nav nav-pills nav-stacked">
							<li><a href="#">Total Fees:<span class="pull-right text-blue pi-text"><span class="text-black">&#8377; </span>
								<b><?php echo money_format('%!i', $report_data['total_fees']); ?></b></span></a></li>

								<li><a href="#">Paid Fees:<span class="pull-right text-green pi-text"><span class="text-black">&#8377; </span>
									<b><?php echo money_format('%!i', $report_data['paid_fees']); ?></b></span></a></li>

									<li><a href="#">Due Fees:<span class="pull-right text-red pi-text"><span class="text-black">&#8377; </span>
										<b><?php echo money_format('%!i', $report_data['due_fees']); ?></b></span></a></li>

									<li><a href="#">Discount Given:<span class="pull-right text-aqua pi-text"><span class="text-black">&#8377; 
										</span><b><?php echo money_format('%!i', $report_data['discount_given']); ?></b></span></a></li>

										<li><a href="#">Outstanding Fees:<span class="pull-right text-yellow pi-text"><span class="text-black">&#8377; </span>
											<b><?php echo money_format('%!i', $report_data['outstanding_fees']); ?></b></span></a></li>
										</ul>
									</div>
									<!-- /.footer -->
								</div>
								<!-- /.box -->
							</div>
<?php }else{ $role= $this->request->session()->read('Auth.User.role_id'); 
 $username= $this->request->session()->read('Auth.User.email');  ?>

			<div class="col-md-7">
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Timetable</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
       
		      <div class="" id="resizable-tables">
                   <table class="table table-bordered table-striped">
            <thead>

            <p class="text-right btn-view-group">
  <a class="btn btn-primary" href="<?php echo SITE_URL;?>admin/ClasstimeTabs/pdf_teacher/<?php echo $classectionid; ?>" target="blank"><i class="fa fa-file-pdf-o"></i> Export PDF</a>
  </p>
  
  <?php $role =$this->request->session()->read("Auth.User.role_id"); //echo $role; ?>

              <tr><th class="text-center bg-teal color-palette">Class Timing</th>
                                  <th class="text-center bg-teal color-palette">Monday</th>
                                  <th class="text-center bg-teal color-palette">Tuesday</th>
                                  <th class="text-center bg-teal color-palette">Wednesday</th>
                                  <th class="text-center bg-teal color-palette">Thursday</th>
                              <th class="text-center bg-teal color-palette">Friday</th>
                              <th class="text-center bg-teal color-palette">Saturday</th></tr>
            </thead>
            <tbody>
          <?php     if($classectionid) { if(isset($timetabledata) && !empty($timetabledata)){  
    foreach($timetabledata as $work){  
             
  $getdata='0';  if($work['is_break'] != 1) { ?>
                            <tr>

                               <!--  ---------------Monday-------------------- -->


                            <td class="text-center text-bold"><?php echo $work['name'] ?></td>
                                        <td class="text-center" style=" word-break: keep-all;"><?php if (strpos($work['weekday'], "Monday") !== false) {
                      $getdata= $this->Comman->gettimetableteacher($work['id'],"Monday",$classectionid); //pr($getdata);

                      $a =array();
                       foreach ($getdata as $key => $value2) {
                                              $a[]=$value2['class_id'];

                                                }





                       $clsssection=$this->Comman->find_alls($getdata[0]['class_id']);  $class=$clsssection['class_id'];  

                       $section=$clsssection['section_id'];  $classtitle=$clsssection['Classes']['title']; 
                       $sectiontitle=$clsssection['Sections']['title'];


                       $emp=explode(',',$getdata[0]['employee_id']); 
                      $sub=explode(',',$getdata[0]['subject_id']); 
                      //pr($emp); pr($sub); pr($classectionid);
                      $subjectname=array();
                      foreach ($emp as $k => $value) {
                      foreach ($sub as $s => $val) {
                      	//pr($value);  pr($val);
                        $vbn=array();
                        if($k==$s && $value==$classectionid){
                        //pr($val); pr($classectionid); 
 //pr($getteac);
                                               $subj= $this->Comman->findclassubject($val);
                                              // pr($subj);
                                               $subjectname[$val]=$subj['alias'];
                                               
                                                
                        }
                      }
                      } 

                       ?>


                       <span ><?php if(!empty($getdata)){ //pr($getdata); die; ?>

                        <?php  $b=array_unique($a); 

  foreach($subjectname as $ko=>$bhu){ 
    echo '<span style="color:green;">'.$bhu.'</span>'; $data=0; $data=base64_encode ($work['id'].'/Monday/'.$classectionid.'/'.$ko);

                      if($role!='1' && $role!='6'){  ?> <a class="text-green" href="<?php echo SITE_URL;?>admin/assignments/add/<?php echo $data; ?>" title="Add Assignment" data-toggle="tooltip" style="padding-left:5px;font-size:16px"><i class="fa fa-thumb-tack"></i></a><br>

                      <?php  } } ?>
                      <?php
foreach ($b as $key => $va) {
  $sdf= $this->Comman->findclasssectionid($va);

  $sec=$sdf['section_id'];
  $cls=$sdf['class_id'];
  $cl1= $this->Comman->findclass123($cls);
  $sl2= $this->Comman->findsection123($sec);

     echo $cl1['title'].'('.$sl2['title'].')<br>';



 }   
   

  ?>
                  
                    

                     <?php  }else{ echo "-"; } }?> </span> </td>


                                      <!--  ---------------Tuesday-------------------- -->

                      <td class="text-center" style=" word-break: keep-all;"><?php if (strpos($work['weekday'], "Tuesday") !== false) {

                       $getdata= $this->Comman->gettimetableteacher($work['id'],"Tuesday",$classectionid);

                      $a =array();
                       foreach ($getdata as $key => $value2) {
                                              $a[]=$value2['class_id'];

                                                }





                       $clsssection=$this->Comman->find_alls($getdata[0]['class_id']);  $class=$clsssection['class_id'];  

                       $section=$clsssection['section_id'];  $classtitle=$clsssection['Classes']['title']; 
                       $sectiontitle=$clsssection['Sections']['title'];


                       $emp=explode(',',$getdata[0]['employee_id']); 
                      $sub=explode(',',$getdata[0]['subject_id']); //pr($emp); pr($sub); pr($classectionid);
                      $subjectname=array();
                      foreach ($emp as $k => $value) {
                      foreach ($sub as $s => $val) {
                      	//pr($value);  pr($val);
                        $vbn=array();
                        if($k==$s && $value==$classectionid){
                        //pr($val); pr($classectionid); 
 //pr($getteac);
                                               $subj= $this->Comman->findclassubject($val);
                                              // pr($subj);
                                               $subjectname[$val]=$subj['alias'];
                                               
                                                
                        }
                      }
                      } 

                       ?>


                       <span ><?php if(!empty($getdata)){ //pr($getdata); die; ?>

                        <?php  $b=array_unique($a); 

  foreach($subjectname as $ko=>$bhu){ 
    echo '<span style="color:green;">'.$bhu.'</span>'; $data=0; $data=base64_encode ($work['id'].'/Tuesday/'.$classectionid.'/'.$ko);

                      if($role!='1' && $role!='6'){  ?> <a class="text-green" href="<?php echo SITE_URL;?>admin/assignments/add/<?php echo $data; ?>" title="Add Assignment" data-toggle="tooltip" style="padding-left:5px;font-size:16px"><i class="fa fa-thumb-tack"></i></a><br>

                      <?php  } } ?>
                      <?php
foreach ($b as $key => $va) {
  $sdf= $this->Comman->findclasssectionid($va);

  $sec=$sdf['section_id'];
  $cls=$sdf['class_id'];
  $cl1= $this->Comman->findclass123($cls);
  $sl2= $this->Comman->findsection123($sec);

     echo $cl1['title'].'('.$sl2['title'].')<br>';



 }   
   

  ?>
                  
                    

                     <?php  }else{ echo "-"; } }?> </span></td>


                        <!--  ---------------Wednesday-------------------- -->


                       <td class="text-center" style=" word-break: keep-all;"><?php if (strpos($work['weekday'], "Wednesday") !== false) { 

                        $getdata= $this->Comman->gettimetableteacher($work['id'],"Wednesday",$classectionid);

                          $a =array();
                       foreach ($getdata as $key => $value2) {
                                              $a[]=$value2['class_id'];

                                                }





                       $clsssection=$this->Comman->find_alls($getdata[0]['class_id']);  $class=$clsssection['class_id'];  

                       $section=$clsssection['section_id'];  $classtitle=$clsssection['Classes']['title']; 
                       $sectiontitle=$clsssection['Sections']['title'];


                       $emp=explode(',',$getdata[0]['employee_id']); 
                      $sub=explode(',',$getdata[0]['subject_id']); //pr($emp); pr($sub); pr($classectionid);
                        $subjectname=array();
                      foreach ($emp as $k => $value) {
                      foreach ($sub as $s => $val) {
                      	//pr($value);  pr($val);
                        $vbn=array();
                        if($k==$s && $value==$classectionid){
                        //pr($val); pr($classectionid); 
 //pr($getteac);
                                               $subj= $this->Comman->findclassubject($val);
                                              // pr($subj);
                                               $subjectname[$val]=$subj['alias'];
                                               
                                                
                        }
                      }
                      } 

                       ?>


                       <span ><?php if(!empty($getdata)){ //pr($getdata); die; ?>

                        <?php  $b=array_unique($a); 

  foreach($subjectname as $ko=>$bhu){ 
    echo '<span style="color:green;">'.$bhu.'</span>'; $data=0; $data=base64_encode ($work['id'].'/Wednesday/'.$classectionid.'/'.$ko);

                      if($role!='1' && $role!='6'){  ?> <a class="text-green" href="<?php echo SITE_URL;?>admin/assignments/add/<?php echo $data; ?>" title="Add Assignment" data-toggle="tooltip" style="padding-left:5px;font-size:16px"><i class="fa fa-thumb-tack"></i></a><br>

                      <?php  } } ?>
                      <?php
foreach ($b as $key => $va) {
  $sdf= $this->Comman->findclasssectionid($va);

  $sec=$sdf['section_id'];
  $cls=$sdf['class_id'];
  $cl1= $this->Comman->findclass123($cls);
  $sl2= $this->Comman->findsection123($sec);

     echo $cl1['title'].'('.$sl2['title'].')<br>';



 }   
   

  ?>
                  
                    

                     <?php  }else{ echo "-"; } }?> </span></td>


                      <!--  ---------------Thursday-------------------- -->


                         <td class="text-center" style=" word-break: keep-all;"><?php if (strpos($work['weekday'], "Thursday") !== false) {
                          $getdata= $this->Comman->gettimetableteacher($work['id'],"Thursday",$classectionid);
                          $a =array();
                       foreach ($getdata as $key => $value2) {
                                              $a[]=$value2['class_id'];

                                                }





                       $clsssection=$this->Comman->find_alls($getdata[0]['class_id']);  $class=$clsssection['class_id'];  

                       $section=$clsssection['section_id'];  $classtitle=$clsssection['Classes']['title']; 
                       $sectiontitle=$clsssection['Sections']['title'];


                       $emp=explode(',',$getdata[0]['employee_id']); 
                      $sub=explode(',',$getdata[0]['subject_id']); //pr($emp); pr($sub); pr($classectionid);
                       $subjectname=array();
                      foreach ($emp as $k => $value) {
                      foreach ($sub as $s => $val) {
                      	//pr($value);  pr($val);
                        $vbn=array();
                        if($k==$s && $value==$classectionid){
                        //pr($val); pr($classectionid); 
 //pr($getteac);
                                               $subj= $this->Comman->findclassubject($val);
                                              // pr($subj);
                                               $subjectname[$val]=$subj['alias'];
                                               
                                                
                        }
                      }
                      } 

                       ?>


                       <span ><?php if(!empty($getdata)){ //pr($getdata); die; ?>

                        <?php  $b=array_unique($a); 

  foreach($subjectname as $ko=>$bhu){ 
    echo '<span style="color:green;">'.$bhu.'</span>'; $data=0; $data=base64_encode ($work['id'].'/Thursday/'.$classectionid.'/'.$ko);

                      if($role!='1' && $role!='6'){  ?> <a class="text-green" href="<?php echo SITE_URL;?>admin/assignments/add/<?php echo $data; ?>" title="Add Assignment" data-toggle="tooltip" style="padding-left:5px;font-size:16px"><i class="fa fa-thumb-tack"></i></a><br>

                      <?php  } } ?>
                      <?php
foreach ($b as $key => $va) {
  $sdf= $this->Comman->findclasssectionid($va);

  $sec=$sdf['section_id'];
  $cls=$sdf['class_id'];
  $cl1= $this->Comman->findclass123($cls);
  $sl2= $this->Comman->findsection123($sec);

     echo $cl1['title'].'('.$sl2['title'].')<br>';



 }   
   

  ?>
                  
                    

                     <?php  }else{ echo "-"; } }?> </span> </td>

                        <!--  ---------------Friday-------------------- -->

                           <td class="text-center" style=" word-break: keep-all;">  <?php if (strpos($work['weekday'], "Friday") !== false) {
                            $getdata= $this->Comman->gettimetableteacher($work['id'],"Friday",$classectionid);
                              $a =array();
                       foreach ($getdata as $key => $value2) {
                                              $a[]=$value2['class_id'];

                                                }





                       $clsssection=$this->Comman->find_alls($getdata[0]['class_id']);  $class=$clsssection['class_id'];  

                       $section=$clsssection['section_id'];  $classtitle=$clsssection['Classes']['title']; 
                       $sectiontitle=$clsssection['Sections']['title'];


                       $emp=explode(',',$getdata[0]['employee_id']); 
                      $sub=explode(',',$getdata[0]['subject_id']); //pr($emp); pr($sub); pr($classectionid);
                      $subjectname=array();
                      foreach ($emp as $k => $value) {
                      foreach ($sub as $s => $val) {
                      	//pr($value);  pr($val);
                        $vbn=array();
                        if($k==$s && $value==$classectionid){
                        //pr($val); pr($classectionid); 
 //pr($getteac);
                                               $subj= $this->Comman->findclassubject($val);
                                              // pr($subj);
                                               $subjectname[$val]=$subj['alias'];
                                               
                                                
                        }
                      }
                      } 

                       ?>


                       <span ><?php if(!empty($getdata)){ //pr($getdata); die; ?>

                        <?php  $b=array_unique($a); 

  foreach($subjectname as $ko=>$bhu){ 
    echo '<span style="color:green;">'.$bhu.'</span>'; $data=0; $data=base64_encode ($work['id'].'/Friday/'.$classectionid.'/'.$ko);

                      if($role!='1' && $role!='6'){  ?> <a class="text-green" href="<?php echo SITE_URL;?>admin/assignments/add/<?php echo $data; ?>" title="Add Assignment" data-toggle="tooltip" style="padding-left:5px;font-size:16px"><i class="fa fa-thumb-tack"></i></a><br>

                      <?php  } } ?>
                      <?php
foreach ($b as $key => $va) {
  $sdf= $this->Comman->findclasssectionid($va);

  $sec=$sdf['section_id'];
  $cls=$sdf['class_id'];
  $cl1= $this->Comman->findclass123($cls);
  $sl2= $this->Comman->findsection123($sec);

     echo $cl1['title'].'('.$sl2['title'].')<br>';



 }   
   

  ?>
                  
                    

                     <?php  }else{ echo "-"; } }?> </span> </td>


                        <!--  ---------------Saturday-------------------- -->

                             <td class="text-center" style=" word-break: keep-all;"> <?php if (strpos($work['weekday'], "Saturday") !== false) {
                              $getdata= $this->Comman->gettimetableteacher($work['id'],"Saturday",$classectionid);
                             $a =array();
                                          foreach ($getdata as $key => $value2) {
                                              $a[]=$value2['class_id'];

                                                }





                       $clsssection=$this->Comman->find_alls($getdata[0]['class_id']);  $class=$clsssection['class_id'];  

                       $section=$clsssection['section_id'];  $classtitle=$clsssection['Classes']['title']; 
                       $sectiontitle=$clsssection['Sections']['title'];


                       $emp=explode(',',$getdata[0]['employee_id']); 
                      $sub=explode(',',$getdata[0]['subject_id']); //pr($emp); pr($sub); pr($classectionid);
                      $subjectname=array();
                      foreach ($emp as $k => $value) {
                      foreach ($sub as $s => $val) {
                      	//pr($value);  pr($val);
                        $vbn=array();
                        if($k==$s && $value==$classectionid){
                        //pr($val); pr($classectionid); 
 //pr($getteac);
                                               $subj= $this->Comman->findclassubject($val);
                                              // pr($subj);
                                               $subjectname[$val]=$subj['alias'];
                                               
                                                
                        }
                      }
                      } 

                       ?>


                       <span ><?php if(!empty($getdata)){ //pr($getdata); die; ?>

                        <?php  $b=array_unique($a); 

  foreach($subjectname as $ko=>$bhu){ 
    echo '<span style="color:green;">'.$bhu.'</span>'; $data=0; $data=base64_encode ($work['id'].'/Saturday/'.$classectionid.'/'.$ko);

                      if($role!='1' && $role!='6'){  ?> <a class="text-green" href="<?php echo SITE_URL;?>admin/assignments/add/<?php echo $data; ?>" title="Add Assignment" data-toggle="tooltip" style="padding-left:5px;font-size:16px"><i class="fa fa-thumb-tack"></i></a><br>

                      <?php  } } ?>
                      <?php
foreach ($b as $key => $va) {
  $sdf= $this->Comman->findclasssectionid($va);

  $sec=$sdf['section_id'];
  $cls=$sdf['class_id'];
  $cl1= $this->Comman->findclass123($cls);
  $sl2= $this->Comman->findsection123($sec);

     echo $cl1['title'].'('.$sl2['title'].')<br>';



 }   
   

  ?>
                  
                    

                     <?php  }else{ echo "-"; } }?> </span> </td></tr>


                             <?php } if($work['is_break']) {   if($work['time_from']) { ?><tr><td class="text-center text-bold"><?php echo $work['name'] ?></td><td class="text-center" style=" word-break: keep-all;"><span title="Break" data-toggle="tooltip" style="color:red;"  > Break</span></td>
                                                            <td class="text-center" style=" word-break: keep-all;"><span title="Break" data-toggle="tooltip" style="color:red;" >Break</span></td>
                                                             <td class="text-center" style=" word-break: keep-all;"><span title="Break" data-toggle="tooltip" style="color:red;" >Break</span></td>
                                                            <td class="text-center" style=" word-break: keep-all;"><span title="Break" data-toggle="tooltip" style="color:red;" >Break</span></td>
                                                       <td class="text-center" style=" word-break: keep-all;"><span title="Break" data-toggle="tooltip" style="color:red;" >Break</span></td>
                                                              <td class="text-center" style=" word-break: keep-all;"><span title="Break" data-toggle="tooltip" style="color:red;" >Break</span></td>
                                                </tr>   
                                                
                                       
                                            
                              <?php  } } } }   ?>
                          
                              <?php  }else{  ?>
                            <tr><td class="text-center text-bold" colspan="7">No Data Selected</td>
                              </tr><?php } ?>
                          </tbody>
        </table>
    
    		<script>

    //prepare the dialog

    //respond to click event on anything with 'overlay' class
    $(".globalModals").click(function(event){

 
     
        $('.modal-content').load($(this).attr("href"));  //load content from href of link

        });
    
    
    
    
    
      $(".globalModalss").click(function(event){

 
     
        $('.modal-content').load($(this).attr("href"));  //load content from href of link

        });
    
    
    
    
</script>
    		 
		
		        

		        
		    
		      <!-- /.box-body -->
            <!-- /.box-body -->
           
            <!-- /.box-footer -->
          </div>
          <!-- /.box -->
        </div>
        </div>
    
        <!-- /.col -->
<?php } ?>
							<!-- Pie-Chart: end -->
<?php if($this->request->session()->read('Auth.User.role_id')=='1'){ ?>
							<div class="col-md-6 calendar-part">
								
								
								<?php }else{ ?>
								
										<div class="col-md-5 calendar-part">
								<?php } ?>
								<div class="box box-primary">
									<div class="box-body no-padding">
										<!-- THE CALENDAR -->
										<div id="w1" class="fullcalendar fc fc-unthemed fc-ltr" language="en" data-plugin-name="fullCalendar">

										</div>
									</div><!-- /.box-body -->
								</div><!-- /. box -->
							</div>
							<!-- /.col -->

						</div>
						
						<!-- /.row -->
						    <!-- TABLE: LATEST ORDERS -->
						    		<!-- Pie-Chart: start -->

						<!-- Info boxes -->
						<div class="row">
							<div class="col-md-3 col-sm-6 col-xs-12">
								<div class="info-box">
									<span class="info-box-icon bg-aqua"><i class="fa fa-building-o" aria-hidden="true"></i></span>

									<div class="info-box-content">
										<span class="info-box-text">
											Total Sections &nbsp;
											<a href="<?php echo $this->Url->build('/admin/Classections/index'); ?>" class="small-box-footer">
												<i class="fa fa-arrow-circle-right"></i>
											</a>
										</span>
										<span class="info-box-number"><?php echo $data_count['section_count']; ?></span>
									</div>
									<!-- /.info-box-content -->
								</div>
								<!-- /.info-box -->
							</div>
							<!-- /.col -->
							<div class="col-md-3 col-sm-6 col-xs-12">
								<div class="info-box">
									<span class="info-box-icon bg-red"><i class="ion-android-person-add"></i></span>

									<div class="info-box-content">
										<span class="info-box-text">
											Total New Admissions &nbsp;
											<a href="<?php echo $this->Url->build('/admin/students/index'); ?>" class="small-box-footer">
												<i class="fa fa-arrow-circle-right"></i>
											</a>
											<br><small>(<?php echo date('Y').'-'.(date('y')+1); ?>)</small>
										</span>
										<span class="info-box-number"><?php echo $data_count['new_admission_count']; ?></span>
									</div>
									<!-- /.info-box-content -->
								</div>
								<!-- /.info-box -->
							</div>
							<!-- /.col -->

							<!-- fix for small devices only -->
							<div class="clearfix visible-sm-block"></div>

							<div class="col-md-3 col-sm-6 col-xs-12">
								<div class="info-box">
									<span class="info-box-icon bg-green"><i class="fa fa-book" aria-hidden="true"></i></span>

									<div class="info-box-content">
										<span class="info-box-text">
											Total Books In Library &nbsp;
											<a href="<?php echo $this->Url->build('/admin/Books/index'); ?>" class="small-box-footer">
												<i class="fa fa-arrow-circle-right"></i>
											</a>
										</span>
										<span class="info-box-number"><?php echo $data_count['book_count']; ?></span>
									</div>
									<!-- /.info-box-content -->
								</div>
								<!-- /.info-box -->
							</div>
							<!-- /.col -->
							<div class="col-md-3 col-sm-6 col-xs-12">
								<div class="info-box">
									<span class="info-box-icon bg-yellow"><i class="fa fa-calendar" aria-hidden="true"></i></span>

									<div class="info-box-content">
										<span class="info-box-text">
											Total Events &nbsp;
											<a href="<?php echo $this->Url->build('/admin/Holiday/index'); ?>" class="small-box-footer">
												<i class="fa fa-arrow-circle-right"></i>
											</a>
										</span>
										<span class="info-box-number"><?php echo $data_count['event_count']; ?></span>
									</div>
									<!-- /.info-box-content -->
								</div>
								<!-- /.info-box -->
							</div>
							<!-- /.col -->
						</div>
						<!-- /.row -->

					</section>
				</div>
