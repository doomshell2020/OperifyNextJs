
<div class="box-header">

	<h3 class="box-title"><i class="fa fa-check-square-o"></i> Employee Attendance</h3>
	
	<a style="position: absolute; top: 10px; right: 16px;" class="btn btn-info btn-sm pull-right" 
	href="<?php echo ADMIN_URL ;?>report/excel_export_employee_attendance">
		<i class="fa fa-file-excel-o" aria-hidden="true"></i> Export Excel
	</a>
	
</div>

<div class="box-body table-responsive">
    <table id="example1" class="table table-bordered table-striped">
          
    <thead>

      <tr>
      
        <th>Employee Name</th>

        <?php
					$curr_month = $month;
					$curr_year = $year;
					$no_of_col=0;

					$start_date = "01-".$curr_month."-".$curr_year;
					$start_time = strtotime($start_date);

					$end_time = strtotime("+1 month", $start_time);

					for($i=$start_time; $i<$end_time; $i+=86400, $no_of_col++)
						echo '<th style="color:#074979; text-align:center;">'.date('d D', $i).'</th>';
				?>

      </tr>

    </thead>

    <tbody>
			<?php
			$page = $this->request->params['paging']['Employees']['page'];
			$limit = $this->request->params['paging']['Employees']['perPage'];
			$counter = ($page * $limit) - $limit + 1;

			if(isset($emp_attendance_record) && !empty($emp_attendance_record)){ 
				foreach($emp_attendance_record as $work){
					// pr($emp_attendance_record);die;
			?>
					<tr>

						<td> <?php if( isset( $work['name'] ) ) { echo $work['name']; } else { echo 'N/A'; } ?> </td>

						<?php
							for($i=1; $i<=$no_of_col; $i++)
							{
								if( in_array( $i, $work['present_date'] ) )
									echo '<td class="present" style="text-align:center;">P</td>';
								else
									echo '<td style="text-align:center;">-</td>';
							}	
						?>
						
					</tr>

					<?php 

						$counter++;

						} } else{ 

					?>

			<tr>
				<td>No Data Available</td>
			</tr>

			<?php } ?>

	</tbody>

</table>
</div>
