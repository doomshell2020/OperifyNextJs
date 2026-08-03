<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
	<!-- sidebar: style can be found in sidebar.less -->
	<section class="sidebar">
		<!-- Sidebar user panel -->
		<div class="user-panel">

			<div class="pull-left image">

				<br /><br />
				<p></p>
			</div>
			<div class="pull-left info">
				<p>
					<?php echo ucfirst($this->request->session()->read('Auth.User.user_name')); ?>
				</p>
				<a href="#"><i class="fa fa-circle text-success"></i> Online</a>
			</div>
		</div>

		<!-- sidebar menu: : style can be found in sidebar.less -->
		<ul class="sidebar-menu">
			<li class="header">MAIN NAVIGATION</li>
			<li class="<?php if ($this->request->params['controller'] == 'Dashboards') {
				echo 'active';
			} ?> treeview">
				<a href="<?php echo $this->Url->build('/admin/dashboards/'); ?>">
					<i class="fa fa-dashboard"></i> <span>Dashboard</span>

				</a>

			</li>
			<li class="<?php if ($this->request->params['controller'] == 'Sitesettings') {
				echo 'active';
			} ?> treeview">
				<a href="<?php echo $this->Url->build('/admin/sitesettings/add'); ?>">
					<i class="fa fa-files-o"></i>
					<span>Site Setting</span>

				</a>

			</li>

			<li class="<?php if ($this->request->params['controller'] == 'Pages') {
				echo 'active';
			} ?> treeview">
				<a href="#">
					<i class="fa fa-files-o"></i>
					<span>Static Page Manager</span>

				</a>
				<ul class="treeview-menu">

					<li><a href="<?php echo $this->Url->build('/admin/pages/index'); ?>"><i class="fa fa-circle-o"></i>
							List</a></li>
					<li><a href="<?php echo $this->Url->build('/admin/pages/add'); ?>"><i class="fa fa-circle-o"></i>
							Add</a></li>

				</ul>
			</li>

			<?php /* <li class="<?php if($this->request->params['controller'] == 'Testimonials'){ echo 'active';} ?> treeview">
			  <a href="#">
				<i class="fa fa-files-o"></i>
				<span>Testimonial</span>
			   
			  </a>
			  <ul class="treeview-menu">
			 
				<li><a href="<?php echo $this->Url->build('/admin/testimonials/index'); ?>"><i class="fa fa-circle-o"></i> List</a></li>
				<li><a href="<?php echo $this->Url->build('/admin/testimonials/add'); ?>"><i class="fa fa-circle-o"></i> Add</a></li>
				
			  </ul>
			</li>

		<li class="<?php if($this->request->params['controller'] == 'Works'){ echo 'active';} ?> treeview">
			  <a href="#">
				<i class="fa fa-files-o"></i>
				<span>Works</span>
				
			  </a>
			  <ul class="treeview-menu">
			 
				<li><a href="<?php echo $this->Url->build('/admin/works/index'); ?>"><i class="fa fa-circle-o"></i> List</a></li>
				<li><a href="<?php echo $this->Url->build('/admin/works/add'); ?>"><i class="fa fa-circle-o"></i> Add</a></li>
				
			  </ul>
			</li>
		<li class="<?php if($this->request->params['controller'] == 'Services'){ echo 'active';} ?> treeview">
			  <a href="#">
				<i class="fa fa-files-o"></i>
				<span>Services</span>
				
			  </a>
			  <ul class="treeview-menu">
			 
				<li><a href="<?php echo $this->Url->build('/admin/services/index'); ?>"><i class="fa fa-circle-o"></i> List</a></li>
				<li><a href="<?php echo $this->Url->build('/admin/services/add'); ?>"><i class="fa fa-circle-o"></i> Add</a></li>
				
			  </ul>
			</li>
		 <li class="<?php if($this->request->params['controller'] == 'Subscribes'){ echo 'active';} ?> treeview">
			  <a href="<?php echo $this->Url->build('/admin/subscribes/index'); ?>">
				<i class="fa fa-files-o"></i>
				<span>Subscribe List</span>
				
			  </a>
			 
			</li>
		 <li class="<?php if($this->request->params['controller'] == 'Contacts'){ echo 'active';} ?> treeview">
			  <a href="<?php echo $this->Url->build('/admin/contacts/index'); ?>">
				<i class="fa fa-files-o"></i>
				<span>Contact List</span>
				
			  </a>
			 
			</li>

	 <?php */?>

			<li class="<?php if ($this->request->params['controller'] == 'Classections') {
				echo 'active';
			} ?> treeview">
				<a href="<?php echo $this->Url->build('/admin/Classections/index'); ?>">
					<i class="fa fa-files-o"></i>
					<span>Class Sections List</span>

				</a>
				<ul class="treeview-menu">

					<li><a href="<?php echo $this->Url->build('/admin/Classections/index'); ?>"><i
								class="fa fa-circle-o"></i> List</a></li>
					<li><a href="<?php echo $this->Url->build('/admin/Classections/add'); ?>"><i
								class="fa fa-circle-o"></i> Add</a></li>

				</ul>

			</li>



			<li class="<?php if ($this->request->params['controller'] == 'Subjectclass') {
				echo 'active';
			} ?> treeview">
				<a href="<?php echo $this->Url->build('/admin/Subjectclass/index'); ?>">
					<i class="fa fa-files-o"></i>
					<span>Subject Class List</span>

				</a>
				<ul class="treeview-menu">

					<li><a href="<?php echo $this->Url->build('/admin/Subjectclass/index'); ?>"><i
								class="fa fa-circle-o"></i> List</a></li>
					<li><a href="<?php echo $this->Url->build('/admin/Subjectclass/add'); ?>"><i
								class="fa fa-circle-o"></i> Add</a></li>

				</ul>

			</li>
			<li class="<?php if ($this->request->params['controller'] == 'Timestables') {
				echo 'active';
			} ?> treeview">
				<a href="<?php echo $this->Url->build('/admin/Timetables/index'); ?>">
					<i class="fa fa-files-o"></i>
					<span>Time tables List</span>

				</a>
				<ul class="treeview-menu">

					<li><a href="<?php echo $this->Url->build('/admin/Timetables/index'); ?>"><i
								class="fa fa-circle-o"></i> List</a></li>
					<li><a href="<?php echo $this->Url->build('/admin/Timetables/add'); ?>"><i
								class="fa fa-circle-o"></i> Add</a></li>

				</ul>

			</li>
			<li class="<?php if ($this->request->params['controller'] == 'ClasstimeTabs') {
				echo 'active';
			} ?> treeview">
				<a href="<?php echo $this->Url->build('/admin/ClasstimeTabs/index'); ?>">
					<i class="fa fa-files-o"></i>
					<span>Class timetables List</span>

				</a>
				<ul class="treeview-menu">

					<li><a href="<?php echo $this->Url->build('/admin/ClasstimeTabs/index'); ?>"><i
								class="fa fa-circle-o"></i> List</a></li>
					<li><a href="<?php echo $this->Url->build('/admin/ClasstimeTabs/add'); ?>"><i
								class="fa fa-circle-o"></i> Add</a></li>
					<li><a href="<?php echo $this->Url->build('/admin/ClasstimeTabs/view'); ?>"><i
								class="fa fa-circle-o"></i> View Timetable</a></li>

				</ul>

			</li>
			<li class="<?php if ($this->request->params['controller'] == 'Employees') {
				echo 'active';
			} ?> treeview">
				<a href="<?php echo $this->Url->build('/admin/employees/index'); ?>">
					<i class="fa fa-files-o"></i>
					<span>Employee List</span>

				</a>
				<ul class="treeview-menu">

					<li><a href="<?php echo $this->Url->build('/admin/employees/index'); ?>"><i
								class="fa fa-circle-o"></i> List</a></li>
					<li><a href="<?php echo $this->Url->build('/admin/employees/add'); ?>"><i
								class="fa fa-circle-o"></i> Add</a></li>


				</ul>

			</li>
			<li class="<?php if ($this->request->params['controller'] == 'Students') {
				echo 'active';
			} ?> treeview">
				<a href="<?php echo $this->Url->build('/admin/students/index'); ?>">
					<i class="fa fa-files-o"></i>
					<span>Students List</span>

				</a>
				<ul class="treeview-menu">

					<li><a href="<?php echo $this->Url->build('/admin/students/index'); ?>"><i
								class="fa fa-circle-o"></i> List</a></li>
					<li><a href="<?php echo $this->Url->build('/admin/students/add'); ?>"><i class="fa fa-circle-o"></i>
							Add</a></li>

				</ul>

			</li>

			<li class="<?php if ($this->request->params['controller'] == 'Classes') {
				echo 'active';
			} ?> treeview">
				<a href="<?php echo $this->Url->build('/admin/classes/index'); ?>">
					<i class="fa fa-files-o"></i>
					<span>Class List</span>

				</a>
				<ul class="treeview-menu">

					<li><a href="<?php echo $this->Url->build('/admin/classes/index'); ?>"><i
								class="fa fa-circle-o"></i> List</a></li>
					<li><a href="<?php echo $this->Url->build('/admin/classes/add'); ?>"><i class="fa fa-circle-o"></i>
							Add</a></li>
				</ul>

			</li>

			<li class="<?php if ($this->request->params['controller'] == 'Sections') {
				echo 'active';
			} ?> treeview">
				<a href="<?php echo $this->Url->build('/admin/sections/index'); ?>">
					<i class="fa fa-files-o"></i>
					<span>Section List</span>

				</a>
				<ul class="treeview-menu">

					<li><a href="<?php echo $this->Url->build('/admin/sections/index'); ?>"><i
								class="fa fa-circle-o"></i> List</a></li>
					<li><a href="<?php echo $this->Url->build('/admin/sections/add'); ?>"><i class="fa fa-circle-o"></i>
							Add</a></li>

				</ul>

			</li>

			<li class="<?php if ($this->request->params['controller'] == 'Houses') {
				echo 'active';
			} ?> treeview">
				<a href="<?php echo $this->Url->build('/admin/houses/index'); ?>">
					<i class="fa fa-files-o"></i>
					<span>House List</span>

				</a>
				<ul class="treeview-menu">

					<li><a href="<?php echo $this->Url->build('/admin/houses/index'); ?>"><i class="fa fa-circle-o"></i>
							List</a></li>
					<li><a href="<?php echo $this->Url->build('/admin/houses/add'); ?>"><i class="fa fa-circle-o"></i>
							Add</a></li>

				</ul>

			</li>

			<li class="<?php if ($this->request->params['controller'] == 'Subjects') {
				echo 'active';
			} ?> treeview">
				<a href="<?php echo $this->Url->build('/admin/subjects/index'); ?>">
					<i class="fa fa-files-o"></i>
					<span>Subject List</span>

				</a>
				<ul class="treeview-menu">

					<li><a href="<?php echo $this->Url->build('/admin/subjects/index'); ?>"><i
								class="fa fa-circle-o"></i> List</a></li>
					<li><a href="<?php echo $this->Url->build('/admin/subjects/add'); ?>"><i class="fa fa-circle-o"></i>
							Add</a></li>
				</ul>

			</li>

			<li class="<?php if ($this->request->params['controller'] == 'Documentcategory') {
				echo 'active';
			} ?> treeview">
				<a href="<?php echo $this->Url->build('/admin/Documentcategory/index'); ?>">
					<i class="fa fa-files-o"></i>
					<span>Document Category</span>

				</a>
				<ul class="treeview-menu">

					<li><a href="<?php echo $this->Url->build('/admin/Documentcategory/index'); ?>"><i
								class="fa fa-circle-o"></i> List</a></li>
					<li><a href="<?php echo $this->Url->build('/admin/Documentcategory/add'); ?>"><i
								class="fa fa-circle-o"></i> Add</a></li>


				</ul>

			</li>

			<li class="<?php if ($this->request->params['controller'] == 'Departments') {
				echo 'active';
			} ?> treeview">
				<a href="<?php echo $this->Url->build('/admin/departments/index'); ?>">
					<i class="fa fa-files-o"></i>
					<span>Department List</span>

				</a>
				<ul class="treeview-menu">

					<li><a href="<?php echo $this->Url->build('/admin/departments/index'); ?>"><i
								class="fa fa-circle-o"></i> List</a></li>
					<li><a href="<?php echo $this->Url->build('/admin/departments/add'); ?>"><i
								class="fa fa-circle-o"></i> Add</a></li>


				</ul>
			</li>

			<li class="<?php if ($this->request->params['controller'] == 'Country') {
				echo 'active';
			} ?> treeview">
				<a href="<?php echo $this->Url->build('/admin/country/index'); ?>">
					<i class="fa fa-files-o"></i>
					<span>Country List</span>

				</a>
				<ul class="treeview-menu">

					<li><a href="<?php echo $this->Url->build('/admin/country/index'); ?>"><i
								class="fa fa-circle-o"></i> List</a></li>
					<li><a href="<?php echo $this->Url->build('/admin/country/add'); ?>"><i class="fa fa-circle-o"></i>
							Add</a></li>


				</ul>

			</li>

			<li class="<?php if ($this->request->params['controller'] == 'States') {
				echo 'active';
			} ?> treeview">
				<a href="<?php echo $this->Url->build('/admin/states/index'); ?>">
					<i class="fa fa-files-o"></i>
					<span>State List</span>

				</a>
				<ul class="treeview-menu">

					<li><a href="<?php echo $this->Url->build('/admin/states/index'); ?>"><i class="fa fa-circle-o"></i>
							List</a></li>
					<li><a href="<?php echo $this->Url->build('/admin/states/add'); ?>"><i class="fa fa-circle-o"></i>
							Add</a></li>


				</ul>

			</li>
			<li class="<?php if ($this->request->params['controller'] == 'Cities') {
				echo 'active';
			} ?> treeview">
				<a href="<?php echo $this->Url->build('/admin/cities/index'); ?>">
					<i class="fa fa-files-o"></i>
					<span>City List</span>

				</a>
				<ul class="treeview-menu">

					<li><a href="<?php echo $this->Url->build('/admin/cities/index'); ?>"><i class="fa fa-circle-o"></i>
							List</a></li>
					<li><a href="<?php echo $this->Url->build('/admin/cities/add'); ?>"><i class="fa fa-circle-o"></i>
							Add</a></li>


				</ul>

			</li>
			<!--          	 
		  <li class="<?php if ($this->request->params['controller'] == 'Courses') {
			  echo 'active';
		  } ?> treeview">
		  <a href="<?php echo $this->Url->build('/admin/courses/index'); ?>">
			<i class="fa fa-files-o"></i>
			<span>Course List</span>
			
		  </a>
 <ul class="treeview-menu">
		 
			<li><a href="<?php echo $this->Url->build('/admin/courses/index'); ?>"><i class="fa fa-circle-o"></i> List</a></li>
			<li><a href="<?php echo $this->Url->build('/admin/courses/add'); ?>"><i class="fa fa-circle-o"></i> Add</a></li>	
			
			
		  </ul>

		</li> 
		-->

			<li class="<?php if ($this->request->params['controller'] == 'Designations') {
				echo 'active';
			} ?> treeview">
				<a href="<?php echo $this->Url->build('/admin/designations/index'); ?>">
					<i class="fa fa-files-o"></i>
					<span>Designations List</span>

				</a>

				<ul class="treeview-menu">

					<li><a href="<?php echo $this->Url->build('/admin/designations/index'); ?>"><i
								class="fa fa-circle-o"></i> List</a></li>
					<li><a href="<?php echo $this->Url->build('/admin/designations/add'); ?>"><i
								class="fa fa-circle-o"></i> Add</a></li>


				</ul>

			</li>

	</section>
	<!-- /.sidebar -->
</aside>