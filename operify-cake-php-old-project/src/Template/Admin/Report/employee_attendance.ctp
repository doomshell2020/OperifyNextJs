<head>
	<style>
		.present {
			color:green;
		   	font-weight: bold;
		}
	</style>
</head>

 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">

 	<section class="content-header">
 		<h1>
 			<i class="fa fa-check-square"></i> Employee Attendance Report
 		</h1>
      <ol class="breadcrumb">
		<li><a href="<?php echo ADMIN_URL;?>dashboards"><i class="fa fa-home"></i>Home</a></li>
<li><a href="<?php echo ADMIN_URL;?>report/employee_attendance">Manage Employee Attendance Report</a></li>
	      </ol>
 	</section>

 	<!-- Main content -->
 	<section class="content">
 <!-- start -->
 		<div class="row">
 			<div class="col-xs-12">

 				<?php echo $this->Flash->render(); ?>

 				<div class="box">
 					<div class="box-header">
 						<h3 class="box-title">
 							<i class="fa fa-search"></i> Search
 						</h3>
 					</div>
 					<!-- /.box-header -->

 					<div class="box-body">


 						<div class="manag-stu">

 							<?php echo $this->Form->create(null, array('class'=>'form-horizontal', 'id'=>'TaskAdminCustomerForm')); ?>

 							<div class="form-group">

                <div class="col-sm-4">
                  <label>Department</label>
                  <?php
                  echo $this->Form->input('department', array('class'=>'form-control','type'=>'select', 'empty'=>'Select Department',
                   'options'=>$departments, 'label' =>false)
                  );
                  ?>
                </div>

                <div class="col-sm-4">
                  <label>Designation</label>
                  <?php
                  echo $this->Form->input('designation', array('class'=>'form-control','type'=>'select', 'empty'=>'Select Designation',
                   'options'=>$designations, 'label' =>false)
                  );
                  ?>
                </div>

                <div class="col-sm-4">
                  <label>Employee Id</label>
                  <?php echo $this->Form->input('e_id',array('class'=>'form-control','placeholder'=>'Enter id','type'=>'text',
                  'label' =>false)); ?>
                </div>

              </div>

              <div class="form-group">

                <div class="col-sm-4">
                  <label>Employee Name</label>
                  <?php echo $this->Form->input('e_name',array('class'=>'form-control','placeholder'=>'Enter name','label' =>false)); ?>
                </div>

                <div class="col-sm-4">
								  <label>Month<span style="color: red;"> *</span></label>
								  <?php
								  echo $this->Form->input('month', array('class'=>'form-control','type'=>'select', 'empty'=>'--- Select Month ---',
								   'options'=>$month_list, 'default'=>date('m'), 'label' =>false, 'required')
								  );
								  ?>
								</div>

								<div class="col-sm-4">
								  <label>Year<span style="color: red;"> *</span></label>
								  <?php
								  echo $this->Form->input('year', array('class'=>'form-control','type'=>'select', 'empty'=>'--- Select Year ---',
								   'options'=>$year_list, 'default'=>date('Y'), 'label' =>false, 'required')
								  );
								  ?>
								</div>

              </div>

              <div class="form-group">

               <div class="col-sm-12">   

                <button type="submit" class="btn btn-success">Search</button>&nbsp;
                <button type="reset" class="btn btn-primary">Reset</button>

              </div>

            </div>

            <?php echo $this->Form->end(); ?>   

          </div>

        </div>

      </div>	</div>	</div>  
      <!-- end -->

      <div class="row">

          <div class="col-xs-12">

            <div class="box" style="display: none;" id="appear-box">

             <!-- Content will be loaded here using AJAX -->
                
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


<!-- custom search script: start -->
<script>

  $(document).ready(function () {

    $("#TaskAdminCustomerForm").bind("submit", function (event) {

      $.ajax({

        async:false,

        type:"POST", 
        
        url:"<?php echo ADMIN_URL ;?>report/employee_attendance_search",
        
        data:$("#TaskAdminCustomerForm").serialize(),
        
        dataType:"html", 

        success:function (data) {
          // alert(data);
          $("#appear-box").html(data);
          $("#appear-box").show();
        }

      });

      return false;

    });

  });

</script>
<!-- custom search script: end -->
