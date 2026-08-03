 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">

 	<section class="content-header">
 		<h1>
 			<i class="fa fa-book"></i>
 			Books Report
 		</h1>
 	                               <ol class="breadcrumb">
		<li><a href="<?php echo ADMIN_URL;?>dashboards"><i class="fa fa-home"></i>Home</a></li>
<li><a href="<?php echo ADMIN_URL;?>report/booksreport">Manage Books Report</a></li>
	      </ol>
 	</section>

 	<!-- Main content -->
 	<section class="content">

 		<!-- start -->
 		<div class="row">
 			<div class="col-xs-12">

 				<div class="box">

 					<div class="box-header">
 						<h3 class="box-title">
 							<i class="fa fa-search"></i> Search Book
 						</h3>
 					</div>
 					<!-- /.box-header -->

 					<div class="box-body">

 						<div class="manag-stu">

 							<?php echo $this->Form->create(null, array('class'=>'form-horizontal', 'id'=>'TaskAdminCustomerForm')); ?>

 							<div class="form-group">

 								<div class="col-sm-4">
 									<label>Acc. No.</label>
 									<?php echo $this->Form->input('asn_no',array('class'=>'form-control', 'label' =>false)); ?>
 								</div>

 								<div class="col-sm-4">
 									<label>ISBN No.</label>
 									<?php echo $this->Form->input('isbn_no',array('class'=>'form-control', 'label' =>false)); ?>
 								</div>

 								<div class="col-sm-4">
 									<label>Book Name</label>
 									<?php echo $this->Form->input('b_name',array('class'=>'form-control','label' =>false)); ?>
 								</div>

 							</div>

 							<div class="form-group">

 								<div class="col-sm-4">
 									<label>Book Category</label>
 									<?php
 									echo $this->Form->input('b_category', array('class'=>'form-control','type'=>'select', 'empty'=>'Select Book Category',
 										'options'=>$b_category, 'label' =>false)
 									);
 									?>
 								</div>

 								<div class="col-sm-4">
 									<label>Author</label>
 									<?php echo $this->Form->input('author',array('class'=>'form-control','label' =>false)); ?>
 								</div>

 								<div class="col-sm-4">
 									<label>Status</label>
 									<?php
 									echo $this->Form->input('status', array('class'=>'form-control','type'=>'select', 'empty'=>'Select Book Status',
 										'options'=>$b_status, 'label' =>false)
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

 							<div class="box-header">
 								
 								<h3 class="box-title"><i class="fa fa-eye"></i> View Books</h3>

 								<a id='ck' style="position: absolute; top: 10px; right: 16px;" class="btn btn-info btn-sm pull-right" 
 								href="<?php echo ADMIN_URL ;?>report/excelExportBooks">
 									<i class="fa fa-file-excel-o" aria-hidden="true"></i> Export Excel
 								</a>

 							</div>
 							<!-- /.box-header -->

 						<div class="box-body table-responsive">

 							<table id="example1" class="table table-bordered table-striped">

 								<thead>

 									<tr>
 										<th>#</th>
 										<th>ASN No.</th>
 										<th>ISBN No.</th>
 										<th>Book Name</th>
 										<th>Book Category</th>
 										<th>Cupboard</th>
 										<th>Cupboard Shelf</th>
 										<th>Author</th>
 										<th>Status</th>
 									</tr>

 								</thead>

 								<tbody id="srch-rslt">
 									<!-- search result will be loaded here using AJAX -->
 								</tbody>

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


 	<!-- custom search script: start -->

 	<script>

 		$(document).ready(function () {

 			$("#TaskAdminCustomerForm").bind("submit", function (event) {

 				$.ajax({

 					async:false,

 					type:"POST", 

 					url:"<?php echo ADMIN_URL ;?>report/searchBook",

 					data:$("#TaskAdminCustomerForm").serialize(),

 					dataType:"html", 

 					success:function (data) {
          // alert(data);
          $("#srch-rslt").html(data);
          $("#appear-box").show();
        }

      });

 				return false;

 			});

 		});

 	</script>

	<!-- custom search script: end -->
