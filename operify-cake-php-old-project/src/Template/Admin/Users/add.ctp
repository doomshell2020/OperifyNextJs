<style>
	.grid-cointainer {
		display: grid;
		grid-template-columns: 1fr 1fr;
		grid-gap: 10px;
	}

	.grid-item {}
</style>

<script>
	$(document).ready(function() {

		$(".exam").change(function() {
			var val = $(this).val();
			var board = $('#boards').val();
			// alert(bb)
			$.ajax({

				type: 'POST',

				url: '<?php echo ADMIN_URL; ?>users/gettable',

				data: {
					// 'etype_id': etype_id,
					// 'student_id': stuid,
					// 'acedmic': acedmic,
					// 'classid': classid,
					// 'exams_id': exams_id,
					// 'section_id': section_id,
					'sch_bord': board,
					'examname': val
				},

				success: function(data) {

					$("#callmarks").html(data);
					// $(".globaldiscountdss").show();

				}

			});


		});

	});
</script>

<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>
			Operify Setup
		</h1>
	</section>
	<div class="box">
		<?php echo $this->Flash->render();  ?>
		<!-- Main content -->
		<section class="content">
			<?php echo $this->Form->create($school, array('class' => 'form-horizontal', 'id' => 'sevice_form', 'enctype' => 'multipart/form-data')); ?>
			<div class="grid-cointainer">
				<div class="grid-item">
					<label class="col-sm-3 control-label">Company Name</label>
					<div class="field col-sm-6">
						<?php echo $this->Form->input('school_name', array('class' =>
						'longinput form-control', 'required', 'placeholder' => 'Company Name', 'required', 'label' => false)); ?></div>
				</div>

				<div class="grid-item">
					<label class="col-sm-3 control-label">State</label>
					<div class="field col-sm-6">
						<?php echo $this->Form->input('state', array('class' =>
						'longinput form-control', 'placeholder' => 'State', 'label' => false, 'type' => 'select', 'options' => $states, 'empty' => 'Select State')); ?></div>
				</div>

				<div class="grid-item">
					<label class="col-sm-3 control-label">City</label>
					<div class="field col-sm-6">
						<?php echo $this->Form->input('city', array('class' =>
						'longinput form-control', 'required', 'placeholder' => 'Company City', 'required', 'label' => false)); ?></div>
				</div>
				<div class="grid-item">
					<label class="col-sm-3 control-label">Company Address</label>
					<div class="field col-sm-6">
						<?php echo $this->Form->input('school_address', array('class' =>
						'longinput form-control', 'required', 'placeholder' => 'Company Address', 'required', 'label' => false)); ?></div>
				</div>
				<div class="grid-item">
					<label class="col-sm-3 control-label">Company Contact</label>
					<div class="field col-sm-6">
						<?php echo $this->Form->input('school_contact', array('class' =>
						'longinput form-control', 'required', 'placeholder' => 'Company Contact', 'required', 'label' => false)); ?></div>
				</div>
				<div class="grid-item">
					<label class="col-sm-3 control-label">Database</label>
					<div class="field col-sm-6">
						<?php echo $this->Form->input('school_database', array('class' =>
						'longinput form-control', 'required', 'placeholder' => 'Database', 'required', 'label' => false)); ?></div>
				</div>
				<div class="grid-item">
					<label class="col-sm-3 control-label">Username</label>
					<div class="field col-sm-6">
						<?php echo $this->Form->input('username', array('class' =>
						'longinput form-control', 'required', 'placeholder' => 'Username', 'required', 'label' => false)); ?></div>
				</div>
				<div class="grid-item">
					<label class="col-sm-3 control-label">Email</label>
					<div class="field col-sm-6">
						<?php echo $this->Form->input('email', array('class' =>
						'longinput form-control', 'required', 'placeholder' => 'Email', 'required', 'label' => false)); ?></div>
				</div>
				<div class="grid-item">
					<label class="col-sm-3 control-label">Password</label>
					<div class="field col-sm-6">
						<?php echo $this->Form->input('password', array('class' =>
						'longinput form-control', 'required', 'placeholder' => 'Password', 'required', 'label' => false)); ?></div>
				</div>
				<div class="grid-item">
					<label class="col-sm-3 control-label">Franchise/Parent Company</label>
					<div class="field col-sm-6">
						<?php echo $this->Form->input('franchise_school', array('class' =>
						'longinput form-control', 'placeholder' => 'Password', 'label' => false, 'type' => 'select', 'options' => $franchise_schools, 'empty' => 'Parent Company')); ?></div>
				</div>
				<!-- <div class="grid-item">
					<label class="col-sm-3 control-label">Boards</label>
					<div class="field col-sm-6">
						<?php echo $this->Form->input('boards[]', array('type' => 'select', 'multiple', 'class' =>
						'longinput form-control board', 'required', 'empty' => 'Select Boards', 'options' => $boards, 'required', 'label' => false, 'value' => $school->boards)); ?></div>
				</div> -->

				<div class="grid-item">
					<label class="col-sm-3 control-label">Clone Database</label>
					<div class="field col-sm-6">
						<?php echo $this->Form->input('clondedbname', array('class' =>
						'longinput form-control', 'placeholder' => 'Password', 'label' => false, 'type' => 'select', 'options' => $allfranchise_schools, 'empty' => 'Parent Company')); ?></div>
				</div>


				<div class="grid-item">
					<label class="col-sm-3 control-label">Company Type</label>
					<div class="field col-sm-6">
						<label class="radio-inline">
							<input type="radio" id="" name="franchise_school_type" value="normal">Normal
						</label>
						<label class="radio-inline">
							<input type="radio" name="franchise_school_type" id="" value="franchise">Franchise

						</label>
					</div>
				</div>






				<!-- <div class="grid-item">
					<label class="col-sm-3 control-label">Transport</label>
					<div class="field col-sm-6">
						<label class="radio-inline">
							<input type="radio" name="is_transport" id="" value="Y" <?php if ($school->is_transport == "Y") { ?> checked <?php } ?>>Y
						</label>
						<label class="radio-inline">
							<input type="radio" name="is_transport" id="" value="N" <?php if ($school->is_transport != "Y") { ?> checked <?php } ?>>N
						</label>
					</div>
				</div> 

				 <div class="grid-item">
					<label class="col-sm-3 control-label">Payroll</label>
					<div class="field col-sm-6">
						<label class="radio-inline">
							<input type="radio" name="is_payroll" id="" value="Y" <?php if ($school->is_payroll == "Y") { ?> checked <?php } ?>>Y
						</label>
						<label class="radio-inline">
							<input type="radio" name="is_payroll" id="" value="N" <?php if ($school->is_payroll != "Y") { ?> checked <?php } ?>>N
						</label>
					</div>

				</div>  -->

				<!-- <div class="grid-item">
					<label class="col-sm-3 control-label">Store</label>
					<div class="field col-sm-6">
						<label class="radio-inline">
							<input type="radio" name="is_store" id="" value="Y" <?php if ($school->is_store == "Y") { ?> checked <?php } ?>>Y
						</label>
						<label class="radio-inline">
							<input type="radio" name="is_store" id="" value="N" <?php if ($school->is_store != "Y") { ?> checked <?php } ?>>N
						</label>
					</div>

				</div>  -->

				<!-- <div class="grid-item">
					<label class="col-sm-3 control-label">Hostel</label>
					<div class="field col-sm-6">
						<label class="radio-inline">
							<input type="radio" name="is_hostel" id="" value="Y" <? php // if ($school->is_hostel == "Y") { 
																					?> checked <? php // } 
																								?>>Y
						</label>
						<label class="radio-inline">
							<input type="radio" name="is_hostel" id="" value="N" <?php //if ($school->is_hostel != "Y") { 
																					?> checked <? php // } 
																								?>>N
						</label>
					</div>

				</div>  -->

				<!-- <div class="grid-item">
					<label class="col-sm-3 control-label">Select Exam</label>
					<div class="field col-sm-6">
						<label class="radio-inline">
							<input type="radio" name="exam" class="exam" value="1">Anual
						</label>
						<label class="radio-inline">
							<input type="radio" name="exam" class="exam" value="2" >Both
						</label>
					</div>

				</div>  -->

				<div id="callmarks">

				</div>

				<div class="grid-item">
				</div>
				<div class="grid-item">
					<div class="field col-sm-6">
						<?php echo $this->Form->submit('Submit', array('class' => 'btn btn-info pull-right', 'title' => 'Create')); ?></div>
				</div>


				<?php $this->Form->end(); ?>
		</section>
	</div>
	<div class="box">
		<div class="box-body">



			<table id="example1" class="table table-bordered table-striped">
				<thead>
					<tr>
						<th>#</th>
						<th>Name </th>
						<th>Address</th>
						<th>Contact</th>
						<th>Database</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>

					<?php $counter = 1;
					if (count($companies) > 0) { //pr($events);
					?>
						<?php foreach ($companies as $key => $value) { ?>
							<?php //pr($value);die;
							?>
							<tr>
								<td><?php echo $counter; ?></td>
								<td><?php echo $value['school_name']; ?></td>
								<td> <?php echo $value['school_address']; ?></td>
								<td><?php echo $value['school_contact']; ?></td>
								<td><?php echo $value['school_database']; ?></td>
								<td>

								<a href="<?php echo ADMIN_URL ?>Users/clonedatatables" class="btn btn-success">Clone Datatables</a>

									<?php if ($value['status'] == 'Y') {  ?>
										<a href="<?php echo ADMIN_URL ?>Users/school_status/<?php echo $value['id']; ?>/<?php echo $value['status']; ?>" class="btn btn-success">Active</a>
									<?php } else { ?>
										<a href="<?php echo ADMIN_URL ?>Users/school_status/<?php echo $value['id']; ?>/<?php echo $value['status']; ?>" class="btn btn-danger">Inactive</a>
									<?php  } ?>
									<!-- <a href="<?php // echo ADMIN_URL ?>Users/add/<?php // echo $value['id']; ?>"
										class="btn btn4 fa fa-pencil" style="font-size:24px;"></a>
									<a title='Delete'
										href="<?php // echo SITE_URL; ?>admin/users/delete/<?php // echo $value->id; ?>" class=""
										onclick="javascript: return confirm('Are you sure do you want to delete')"><span
											class="fa fa-trash" style="font-size: 16px !important; color: red;"></span></a> -->
									<!-- <a href="<?php // echo ADMIN_URL ?>Users/delete/<?php // echo $value['id']; ?>"
										class="btn btn4 btn_trash_a"
										onClick="javascript: return confirm('Are you sure you want to delete this?')"><img
											src="<?php // echo SITE_URL; ?>/images/trash.png"></a>
									<a href="<?php // echo ADMIN_URL ?>Users/add/<?php // echo $value['id']; ?>"
										class="btn btn4 fa fa-pencil" style="font-size:24px;"></a>  -->
								</td>
							</tr>

						<?php $counter++;
						} ?>
					<?php } else { ?>
						<tr>
							<td colspan="7" align="center">No Data Available</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>

	</div>
</div>