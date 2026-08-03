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
			IDSPrime School Data Clone
		</h1>
	</section>
	<div class="box">
		<?php echo $this->Flash->render();  ?>
		<!-- Main content -->
		<section class="content">
			<?php echo $this->Form->create($school, array('class' => 'form-horizontal', 'id' => 'sevice_form', 'enctype' => 'multipart/form-data')); ?>
				<div style="display:flex; flex-wrap:wrap;">
					<div style="width:50%; padding-left:15px; padding-right:15px; box-sizing:border-box; margin-bottom:15px">
						<label class="control-label">Clone From</label>
						<div class="field">
							<select name="clonefrom" class="longinput form-control" placeholder="clonedb" id="clonedb" onchange="clonefromdb(this.value);">
								<option value = "">--Select Database--</option>
								<?php foreach($results as $val){ //pr($val);?>
									<option value = "<?php echo $val['Database']; ?>"><?php echo $val['Database']; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>

					<div style="width:50%; padding-left:15px; padding-right:15px; box-sizing:border-box; margin-bottom:15px">
						<label class="control-label">Clone To</label>
						<div class="field">
							<select name="cloneto" class="longinput form-control" placeholder="clonedb" id="clonedb" >
								<option value = "">--Select Database--</option>
								<?php foreach($results as $val){ //pr($val);?>
									<option value = "<?php echo $val['Database']; ?>"><?php echo $val['Database']; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>

					<div style="width:100%; padding-left:15px; padding-right:15px; box-sizing:border-box; margin-bottom:15px">
					<style>
						#tabledata {
							display: flex;
							flex-wrap: wrap;
							align-items: center;
						}
						
					</style>
						<div id="tabledata"></div>
					</div>
					
					<div style="width:100%; padding-left:15px; padding-right:15px; box-sizing:border-box; margin-bottom:15px; display:flex;">
						<div class="field">
							<?php echo $this->Form->submit('Submit', array('class' => 'btn btn-info pull-right', 'title' => 'Create')); ?>
						</div>
					</div>
				</div>
			<?php $this->Form->end(); ?>
		</section>
	</div>

</div>
<script>
	
         function clonefromdb(id) {
            //alert(id);
             $.ajax({
                 type: 'POST',
                 url: '<?php echo SITE_URL; ?>admin/users/tablesfetcheddata',
                 data: {
                     'dbname': id
                 },
                 success: function(data) {
					  $('#tabledata').html(data);
                 },
         
             });
         }
	
</script>	
