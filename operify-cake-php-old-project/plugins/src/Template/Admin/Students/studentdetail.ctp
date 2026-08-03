 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Student Manager
       
      </h1>
     <ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-home"></i>Home</a></li>
<li><a href="#">Student</a></li>
<li><a href="#" class="active">Manage Student</a></li>

	      </ol>
    </section>




    <!-- Main content -->
    <section class="content">
		    <div class="row">
        <div class="col-xs-12">
          
	<div class="box">
            <div class="box-header">
              <i class="fa fa-search" aria-hidden="true"></i>
<h3 class="box-title">Search Student</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
			
				
					</div>
						</div>
						</div>
		</div>
		
		
		
		
		
      <div class="row" >
        <div class="col-xs-12">
          
	<div class="box" id="Mycitys">
            <div class="box-header">
              <i class="fa fa-search" aria-hidden="true"></i>
<h3 class="box-title">Student List</h3>
            </div>
            <!-- /.box-header -->
<?php echo $this->Flash->render(); ?>
            <div class="box-body">
              <table id="example1" class="table table-bordered table-striped">
                
  
 <thead>
    <tr>
      <th>#</th>
      <th>&nbsp;</th>
      <th>Student ER No.</th>
    
      <th>Name</th>
      <th>Mobile</th>
      <th>Academic Year</th>
      <th>Class</th>
      <th>Section</th>
          <th>Form No.</th>
      <th>Profile Status</th>
      <th>Action</th>
    </tr>
 </thead>
                <tbody>
		
               
              </table>
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
