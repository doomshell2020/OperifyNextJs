   <tr>
   	<td><a id="" style="position: absolute;
top: -83px;
/* right: 0px; */
right: 46px;" class="btn btn-info btn-sm pull-right" target="_blank" href="<?php echo ADMIN_URL; ?>report/dropoutreports"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Export Excel</a></td>
   </tr>
   <?php
	$page = $this->request->params['paging']['DropOutStudent']['page'];
	$limit = $this->request->params['paging']['DropOutStudent']['perPage'];
	$counter = ($page * $limit) - $limit + 1;
	$session = $this->request->session();


	$session->delete($students);
	$session->write('students', $students);
	if (isset($students) && !empty($students)) {
		foreach ($students as $work) {
	?>

   		<?php //pr($work); die; 
			?>
   		<tr>
   			<td><?php echo $counter; ?></td>



   			<td style="font-size: 11px;"><a title="View Drop Out Student" href="<?php echo SITE_URL; ?>admin/students/editdropout/<?php echo $work['id']; ?>">
   					<?php
						$name = $work['fname'] . ' ';

						if (!empty($work['middlename']))
							$name .= $work['middlename'] . ' ';

						echo $name .= $work['lname'];
						?>(<?php echo $work['enroll']; ?>)</a>
   			</td>
   			<td style="font-size: 11px;"><?php echo $work['fathername']; ?></td>

   			<?php $class = $this->Comman->findclass($work['laststudclass']); ?>
   			<td style="font-size: 11px;"><?php echo $class['title']; ?></td>
   			<td style="font-size: 11px;"><?php echo $work['classtitle']; ?>-<?php echo $work['sectiontitle']; ?></td>

   			<td><?php echo date('d-m-Y', strtotime($work['dropcreated'])); ?></td>
   			<?php if (empty($work['date_application'])) {
					$work['date_application'] = '--';
				} else {
					$work['date_application'] = date('d-m-Y', strtotime($work['date_application']));
				}
				if (empty($work['date_issue'])) {
					$work['date_issue'] = '--';
				} else {
					$work['date_issue'] = date('d-m-Y', strtotime($work['date_issue']));
				} ?>
   			<td><?php echo $work['date_application']; ?></td>
   			<td><?php echo $work['date_issue']; ?></td>
   			<?php

				$ec = $this->Comman->findfeesmonth34($work['s_id']);
				$ec2 = $this->Comman->findfeesmonth342($work['s_id']);

				$quuar = unserialize($ec['quarter']);
				$qra = array();
				foreach ($quuar as $h => $rt) {
					$qra[] = $h;
				}

				$monthupto = date('M Y', strtotime($ec['paydate']));
				$monthupto23 = date('M Y', strtotime($ec2['paydate']));

				if (in_array('Quater4', $qra) && in_array('Quater3', $qra) && in_array('Quater2', $qra) && in_array('Quater1', $qra)) {

					$monthuptosa = 'Mar';
				} else {


					$monthuptos = date('M', strtotime($ec['paydate']));
					$monthuptosyy = date('Y', strtotime($ec['paydate']));
				}

				$e1 = array('Apr', 'May', 'Jun');
				$e2 = array('Jul', 'Aug', 'Sep');
				$e3 = array('Oct', 'Nov', 'Dec');
				$e4 = array('Jan', 'Feb', 'Mar');


				if (in_array('Quater4', $qra)) {

					$monthupto = 'Mar ' . date('Y');
				} else if (in_array($monthuptos, $e1)) {
					$monthupto = 'Jun ' . date('Y', strtotime($ec['paydate']));
				} else if (in_array($monthuptos, $e2)) {
					$monthupto = 'Sep ' . date('Y', strtotime($ec['paydate']));
				} else if (in_array($monthuptos, $e3)) {
					$monthupto = 'Dec ' . date('Y', strtotime($ec['paydate']));
				} else if (in_array($monthuptos, $e4)) {
					$monthupto = 'Mar ' . date('Y', strtotime($ec['paydate']));
				} else if ($monthuptosa) {
					$monthupto = 'Mar ' . date('Y');
				}
				?> <td><?php if ($work['month']) {
					         // echo "1";
							echo $work['month'];
						} else if ($monthupto != "Jan 1970"  && $monthupto != "Mar 1970") {
							// echo "2";
							echo $monthupto;
						} else if ($monthupto23 != "Jan 1970" && $monthupto23 != "Mar 1970") {
							echo $monthupto23;
						//	echo "3";
						} else {
							echo "N/A";
						} ?></td>
   			<td>
   				<? if ($work['status_tc'] == "N") {  ?>

   					<i class="fa fa-clock-o" aria-hidden="true" style="font-size: 22px;color:red;"></i>&nbsp;<b>L.W.T.C</b>

   				<? } else { ?>
   					<i class="fa fa-check-circle" aria-hidden="true" style="font-size: 22px;color:#3c8dbc;"></i>&nbsp;<b>W.T.C</b>
   				<? } ?>
   			</td>

   		</tr>
   	<?php $counter++;
		}
	} else { ?>
   	<tr>
   		<td style="text-align:center;" colspan="9">No Data Available</td>
   	</tr>

   <?php } ?>