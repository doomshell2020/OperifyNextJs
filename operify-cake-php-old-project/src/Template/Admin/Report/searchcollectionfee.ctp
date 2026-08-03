<div class="table-responsive">
	<table id="" class="table table-bordered table-striped">
		<tbody>
			<tr>
				<td><a id="" style="position: absolute;
            top: 122px;
            /* right: 0px; */
            right: 46px;" class="btn btn-info btn-sm pull-right" href="<?php echo ADMIN_URL; ?>report/user_collectionfee"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Export Excel</a></td>
			</tr>
			<tr>
				<th>S.No</th>
				<th>Sr.No</th>
				<th>Name</th>
				<th>Academic Year</th>
				<th>PayDate</th>
				<th>Rec. No.</th>
				<?php if (in_array("CHEQUE", $mode)) { ?>
					<th>Che./DD No.</th>
				<? } else if (in_array("DD", $mode)) { ?>
					<th>Che./DD No.</th>
				<? }
				if (in_array("NETBANKING", $mode)) { ?>
					<th>Ref. No.</th>
				<? } else if (in_array("CREDIT CARD/DEBIT CARD", $mode)) { ?>
					<th>Ref. No</th>
				<? }
				foreach ($selectField as $j => $el) {
					$el = trim($el);
					$feeheadss = $this->Comman->findfeeheadsaliasfirst($el);  ?>
					<th>
						<? if ($feeheadss['alias'] != '') {
							echo ucwords(strtolower($feeheadss['alias']));
						} else {

							if ($el == "Prospectus") {
								echo "Pros. fee";
							} else if ($el == "Registration") {
								echo "Regi. fee";
							} else if ($el == "Quater1") {
								echo "Qtr1";
							} else if ($el == "Quater2") {
								echo "Qtr2";
							} else if ($el == "Quater3") {
								echo "Qtr3";
							} else if ($el == "Quater4") {
								echo "Qtr4";
							}else if ($el == "Transport5") {
								echo "Trans-Qtr1";
							}else if ($el == "Transport6") {
								echo "Trans-Qtr2";
							}else if ($el == "Transport7") {
								echo "Trans-Qtr3";
							}else if ($el == "Transport8") {
								echo "Trans-Qtr4";
							}else if ($el == "Discount Fee") {
								echo "Disc. fee";
							} else if ($el == "Due Amount") {
								echo "(-)Due/ (+)Access amt.";
							} else if ($el == "Late Fee") {
								echo "Late fee";
							} else {
								echo $el;
							}
						} ?>
					</th>
				<? } ?>
				<th>Total</th>
			</tr>
			<?php if (isset($Classections) && !empty($Classections)) {
				foreach ($Classections as $key => $element) {

					if ($element['type'] == "Fee") {
						$stiiu = $this->Comman->getthisyearstudent($element['student_id'], $element['acedmicyear']);
						$stiiu34 = $this->Comman->gethistoryyeardropstudent($element['student_id'], $element['acedmicyear']);

						if ($stiiu['id']) {

							$class = $this->Comman->findclass123($element['student']['class_id']);

							if ($class['title'] == "") {

								$idds[] = $element['id'];
							}
						} else if ($stiiu34['id']) {

							$class3 = $this->Comman->findclass123($stiiu34['laststudclass']);

							if ($class3['title'] == "") {

								$idds[] = $element['id'];
							}
						} else {

							$stiius = $this->Comman->gethistoryyearstudent($element['student_id'], $element['acedmicyear']);
							if ($stiius['id']) {

								if ($element['student']['class_id']) {

									$classs = $this->Comman->findclass123($element['student']['class_id']);
								} else {

									$classs = $this->Comman->findclass123($element['studentshistory']['class_id']);
								}

								if ($classs['title'] == "") {


									$idds[] = $element['id'];
								}
							}
						}
					} else {  ?><b style="color:green;">
							<? if ($element['type'] == "Prospectus") {
								$prospect = $this->Comman->findprospectus($element['recipetno'], $element['formno']);
								$cl = $this->Comman->findclass($prospect['class_id']);
								if ($cl['title'] == "") {

									$idds[] = $element['id'];
								}
							}
							if ($element['type'] == "Registration") {
								$applicant = $this->Comman->findapplicant($element['recipetno'], $element['formno']);
								$cls = $this->Comman->findclass($applicant['class_id']);
								if ($cls['title'] == "") {


									$idds[] = $element['id'];
								}
							}
						}
					}
				}


				$page = $this->request->params['paging']['Studentfees']['page'];
				$limit = $this->request->params['paging']['Studentfees']['perPage'];
				$counter = '1';
				$total = 0;
				$totalfee = 0;
				$out = 0;
				$total_discount = 0;
				$session = $this->request->session();


				$session->delete($Classections);
				$session->write('Classectionss', $Classections);


				$session->delete($idds);
				$session->write('idds', $idds);
				if ($s_id) {
					$session->delete($s_id);
					$session->write('s_ids', $s_id);
				}
				if ($mode) {
					$session->delete($mode);
					$session->write('modes', $mode);
				}

				if ($selectField) {
					$session->delete($selectField);
					$session->write('selectFields', $selectField);
				}
				if ($datefrom) {
					$session->delete($datefrom);
					$session->write('datefroms', $datefrom);
				}
				if ($dateto2) {

					$session->delete($dateto2);
					$session->write('datetos', $dateto2);
				}
				$totaladmission = 0;
				$totalapplicant = 0;
				$totalprospectus = 0;
				$totalOther = 0;
				$sumtotal = 0;
				$totaladmissions = 0;
				if (isset($Classections) && !empty($Classections)) {
					foreach ($Classections as $key => $element) {
						// pr($element);die;
						if (!in_array($element['id'], $idds)) {
							$totalsum = 0;

							?>
							<tr>
								<td>
									<? echo $counter++; ?>
								</td>
								<td>
									<? if ($element['type'] == "Fee") {
										$stiiu = $this->Comman->getthisyearstudent($element['student_id'], $element['acedmicyear']);
										$stiiu34 = $this->Comman->gethistoryyeardropstudent($element['student_id'], $element['acedmicyear']);

										if ($stiiu['id']) {


											$section = $this->Comman->findsection123($element['student']['section_id']);
											$class = $this->Comman->findclass123($element['student']['class_id']);
											echo $element['student']['enroll'] . " (" . $class['title'] . "-" . $section['title'] . ")";
										} else if ($stiiu34['id']) {

											$section = $this->Comman->findsection123($stiiu34['section_id']);
											$class3 = $this->Comman->findclass123($stiiu34['laststudclass']);
											echo $stiiu34['enroll'] . " (" . $class3['title'] . "-" . $section['title'] . ")";
										} else {


											$stiius = $this->Comman->gethistoryyearstudent($element['student_id'], $element['acedmicyear']);
											if ($stiius['id']) {

												if ($element['student']['class_id']) {

													$classs = $this->Comman->findclass123($element['student']['class_id']);
													$section = $this->Comman->findsection123($element['student']['section_id']);
												} else {

													$classs = $this->Comman->findclass123($element['studentshistory']['class_id']);
													$section = $this->Comman->findsection123($element['studentshistory']['section_id']);
												}
												echo $stiius['enroll'] . " (" . $classs['title'] . "-" . $section['title'] . ")";
											}
										}
									} else if ($element['type'] == "Other") { ?><b style="color:green;">
											<?php
											$ot_detail = $this->Comman->findotherdetails($element['recipetno']);
											if ($ot_detail['s_id']) {
												echo "Other fees. (" . $ot_detail['s_id'] . ")";
											} else {
												echo "Other Fees";
											}
										} else {  ?><b style="color:green;">
												<? if ($element['type'] == "Prospectus") {
													$prospect = $this->Comman->findprospectus($element['recipetno'], $element['formno']);
													$cl = $this->Comman->findclass($prospect['class_id']);
													echo "Pros. (" . $cl['title'] . ")";
												}
												if ($element['type'] == "Registration") {
													$applicant = $this->Comman->findapplicant($element['recipetno'], $element['formno']);
													$cls = $this->Comman->findclass($applicant['class_id']);
													echo "Regi. (" . $cls['title'] . ")";
												}

												?></b>
										<?  } ?>
								</td>
								<td>
									<? if ($element['type'] == "Fee") {
										if ($stiiu['id']) { ?>
											<a href="<?php echo SITE_URL; ?>admin/studentfees/index/<?php echo $element['student']['id']; ?>/<?php echo $element['student']['acedmicyear']; ?>">
												<? echo ucwords(strtolower($element['student']['fname'])) . " " . ucwords(strtolower($element['student']['middlename'])) . " " . ucwords(strtolower($element['student']['lname']));
												if ($element['student']['category'] == "Migration" || $element['student']['oldenroll'] != 0) {
													echo "<span style='color: red;'>(Migr.)*</span>";
												}
											} else if ($stiiu34['id']) { ?>
												<a href="#" <?php if ($stiiu34['status_tc'] == "N") { ?> style="color:orange;" title="L.W.T.C" <?php } else if ($stiiu34['status_tc'] == "Y") { ?> title="W.T.C" style="color:crimson;" <?php } ?>>
													<?php echo ucwords(strtolower($stiiu34['fname'])) . " " . ucwords(strtolower($stiiu34['middlename'])) . " " . ucwords(strtolower($stiiu34['lname']));
													if ($stiiu34['category'] == "Migration" || $stiiu34['oldenroll'] != 0) {
														echo "<span style='color: red;'>(Migr.)*</span>";
													}
												} else {

													if ($stiius['id']) { ?>
														<a href="<?php echo SITE_URL; ?>admin/studentfees/history/<?php echo $stiius['stud_id']; ?>/<?php echo $element['acedmicyear']; ?>">
														<? echo ucwords(strtolower($stiius['fname'])) . " " . ucwords(strtolower($stiius['middlename'])) . " " . ucwords(strtolower($stiius['lname']));
														if ($stiius['category'] == "Migration" || $stiius['oldenroll'] != 0) {
															echo "<span style='color: red;'>(Migr.)*</span>";
														}
													} ?></a>
												<?	}
											} else if ($element['type'] == "Other") {
												echo "<a href=" . SITE_URL . "admin/studentfees/otherfees style='color: #3c8dbc;'>" . ucwords(strtolower($ot_detail['pupilname'])) . "</a>";
											} else {


												if ($element['type'] == "Prospectus") {
													$prospect = $this->Comman->findprospectus($element['recipetno'], $element['formno']);
													echo "<a href='#' style='color: #3c8dbc;'>" . ucwords(strtolower($prospect['s_name'])) . "</a>";
												}
												if ($element['type'] == "Registration") {
													$applicant = $this->Comman->findapplicant($element['recipetno'], $element['formno']);
													echo "<a href='#' style='color: #3c8dbc;'>" . ucwords(strtolower($applicant['fname'])) . " " . ucwords(strtolower($applicant['middlename'])) . " " . ucwords(strtolower($applicant['lname'])) . "</a>";
												}
											} ?>
								</td>
								<td>
									<? echo $element['acedmicyear']; ?>
								</td>
								<td>
									<? echo date('d-m-Y', strtotime($element['paydate'])); ?>
								</td>
								<td>
									<a target="_blank" href="<?php echo SITE_URL; ?>admin/studentfees/printsadmission/<? echo $element['id']; ?>/<? echo $element['acedmicyear']; ?>"><? echo $element['recipetno']; ?></a>
								</td>
								<? if (in_array("CHEQUE", $mode)) { ?>
									<td>
										<? if ($element['cheque_no']) {
											if ($element['bank']) {
												echo $element['cheque_no'] . "<br><b>" . $element['bank'] . "</b>";
											} else {
												echo $element['cheque_no'];
											}
										} else {

											echo "--";
										} ?>
									</td>
								<? } else if (in_array("DD", $mode)) { ?>
									<td>
										<? if ($element['cheque_no']) {
											if ($element['bank']) {
												echo $element['cheque_no'] . "<br><b>" . $element['bank'] . "</b>";
											} else {
												echo $element['cheque_no'];
											}
										} else {

											echo "--";
										} ?>
									</td>
								<? }
								if (in_array("NETBANKING", $mode)) { ?>
									<td>
										<? if ($element['ref_no']) {
											if ($element['bank']) {
												echo $element['ref_no'] . "<br><b>" . $element['bank'] . "</b>";
											} else {
												echo $element['ref_no'];
											}
										} else {

											echo "--";
										} ?>
									</td>
								<? } else if (in_array("CREDIT CARD/DEBIT CARD", $mode)) { ?>
									<td>
										<? if ($element['ref_no']) {
											if ($element['bank']) {
												echo $element['ref_no'] . "<br><b>" . $element['bank'] . "</b>";
											} else {
												echo $element['ref_no'];
											}
										} else {

											echo "--";
										} ?>
									</td>
								<? }
								$quas = array();
								$quas[] = unserialize($element['quarter']);

								$quaf = array();

								foreach ($quas as $h => $vale) {

									$quaf = array_merge($quaf, $vale);
								}

								$qua = array();
								foreach ($quaf as $j => $t) {

									$qua[$j] = $t;
								}
								// pr($qua);
								$i = 0;
								foreach ($selectField as $sj => $el) {  //pr($el);?>
									<td>
										<?php
										$tj = '0';
										$el = trim($el);
										if ($el == "Due Amount") {
											$findpending = $this->Comman->findpendingsfee($element['student_id'], $element['id']);

											if ($findpending[0]['sum']) {

												$tj -= $findpending[0]['sum'];
												if ($findpending[0]['sum'] < 0) {
													$totaladmissions += $findpending[0]['sum'];
												} else {

													$totaladmissions -= $findpending[0]['sum'];
												}
											} else {

												$tj += "0";
												$totaladmissions += 0;
											}
										} else if ($el == "Prospectus") {

											if ($element['type'] == "Fee") {
												$tj += "0";
												$totalprospectus += '0';
											} else if ($element['type'] == "Prospectus") {

												$tj += $element['deposite_amt'];
												$totalprospectus += $element['deposite_amt'];
											}
										} else if ($el == "Registration") {


											if ($element['type'] == "Fee") {
												$tj += "0";
												$totalapplicant += '0';
											} else if ($element['type'] == "Registration") {

												$tj += $element['deposite_amt'];
												$totalapplicant += $element['deposite_amt'];
											}
										} else if ($el == "Quater1") {

											foreach ($qua as $j => $te) {
												if ($j == "Quater1") {
													$tj += $te;

													$totaltution1 += $te;
												}
											}
										}
										else if ($el == "Transport5") {

											foreach ($qua as $j => $te) {
												if ($j == "Transport5") {
													$tj += $te;
													$totaltransport1 += $te;
													// pr($totaltution1);die;
												}
											}
										}
										else if ($el == "Transport6") {

											foreach ($qua as $j => $te) {
												if ($j == "Transport6") {
													$tj += $te;
													$totaltransport2 += $te;
												}
											}
										}else if ($el == "Transport7") {

											foreach ($qua as $j => $te) {
												if ($j == "Transport7") {
													$tj += $te;
													$totaltransport3 += $te;
												}
											}
										}else if ($el == "Transport8") {

											foreach ($qua as $j => $te) {
												if ($j == "Transport8") {
													$tj += $te;
													$totaltransport4 += $te;
												}
											}
										} 
										else if ($el == "Quater2") {

											foreach ($qua as $j => $te) {
												if ($j == "Quater2") {
													$tj += $te;

													$totaltution2 += $te;
												}
											}
										} else if ($el == "Quater3") {

											foreach ($qua as $j => $te) {
												if ($j == "Quater3") {
													$tj += $te;

													$totaltution3 += $te;
												}
											}
										} else if ($el == "Quater4") {

											foreach ($qua as $j => $te) {
												if ($j == "Quater4") {
													$tj += $te;

													$totaltution4 += $te;
												}
											}
										} else if ($el == "Late Fee") {


											$tj += $element['lfine'];
											$totallfine += $element['lfine'];
										} else if ($el == "Prev. Due") {

											foreach ($qua as $j => $te) {

												$iteam['quarter'] = str_replace('"', "", $j);

												$findpendinsg = $this->Comman->findpendingrefrencefees($iteam['quarter'], $te);
												if ($findpendinsg) {

													$tj += $findpendinsg['amt'];
													$totalOther += $findpendinsg['amt'];
												}
											}
										} else if ($el == "Discount Fee") {

											$total2 = '0';
											if ($element['discount'] != '0.00') {
												$quasn = unserialize($element['quarter']);
												foreach ($quasn as $jn => $tn) {

													$quan += $tn;
												}
												//pr($quan);
												$discounts = $element['discount'];

												$tj -= $discounts;
												$totaldiscount += $discounts;
											}
										} else if ($el == "Other Discount") {  
											$adddiscount += $element['addtionaldiscount'];
											$tj -= $element['addtionaldiscount'];
											$totalOtherdiscount += $element['addtionaldiscount'];
										} else {

											$totalas = array();

											$fgj = '';
											$fsgj = '';
											foreach ($qua as $j => $ted) {

												$el = trim($el);
												$j = trim($j);

												if ($el == "Admission Fee" && $element['student']['enroll'] <= '5974' && $this->request->session()->read('Auth.User.db') == "sanskar" && $element['student']['board_id'] == '1') {

													$fsgj = "OLD";
												} else if ($el == "Development Fee" && $element['student']['enroll'] <= '5974' && $this->request->session()->read('Auth.User.db') == "sanskar"  && $element['student']['board_id'] == '1') {

													$fsgj = "OLD";
													//  $element['deposite_amt']=$element['deposite_amt']-$ted;
												} else if ($el == "Caution Money" && $element['student']['enroll'] <= '5974' && $this->request->session()->read('Auth.User.db') == "sanskar"  && $element['student']['board_id'] == '1') {

													$fsgj = "OLD";
													//  $element['deposite_amt']=$element['deposite_amt']-$ted;
												} else if ($element['recipetno'] == '0') {

													$fsgj = "0";
												}

												if (strcasecmp($j, $el) == 0) {

													if ($el == "Admission Fee" && $element['student']['enroll'] <= '5974' && $this->request->session()->read('Auth.User.db') == "sanskar"  && $element['student']['board_id'] == '1') {
														$tj += 0;
														$fgj = "OLD";
														$element['deposite_amt'] = $element['deposite_amt'] - $ted;
														$totalas[$el] = 0;
														$totalass[] = $totalas;
													} else if ($el == "Development Fee" && $element['student']['enroll'] <= '5974' && $this->request->session()->read('Auth.User.db') == "sanskar"  && $element['student']['board_id'] == '1') {
														$tj += 0;
														$fgj = "OLD";
														$element['deposite_amt'] = $element['deposite_amt'] - $ted;
														$totalas[$el] = 0;
														$totalass[] = $totalas;
													} else if ($el == "Caution Money" && $element['student']['enroll'] <= '5974' && $this->request->session()->read('Auth.User.db') == "sanskar"  && $element['student']['board_id'] == '1') {

														$tj += 0;
														$fgj = "OLD";
														$element['deposite_amt'] = $element['deposite_amt'] - $ted;
														$totalas[$el] = 0;
														$totalass[] = $totalas;
													} else if ($element['recipetno'] == '0') {


														$tj += 0;
														$fgj = "0";
														$element['deposite_amt'] = $element['deposite_amt'] - $ted;
														$totalas[$el] = 0;
														$totalass[] = $totalas;
													} else {

														$tj += $ted;

														$totalas[$el] = $ted;
														$totalass[] = $totalas;
													}
												}
											}
											//pr($totalas);

										}

										if ($fgj != '') {
											echo $fgj;
										} else if ($fsgj != '') {
											echo $fsgj;
										} else {
											echo $tj;
										}
										$fgj = 0;
										$fsgj = 0;
										$totaladmission += $tj;
										//$totalsum +=$element['deposite_amt'];
										?>
									</td>
								<? $i++;
								} ?>
								<td><?php
									echo $element['deposite_amt'];

									$sumtotal += $element['deposite_amt'];
									?></td>
								<?php
								?>
							</tr>
					<? }
					}
				} else { ?>
					<tr>
						<td colspan="11" style="text-align:center;">No Collection Available</td>
					</tr>
				<? } ?>
				<tr>
					<?php $iio = 6;
					if (in_array("CHEQUE", $mode)) {
						$iio++;
					} else if (in_array("DD", $mode)) {
						$iio++;
					}

					if (in_array("NETBANKING", $mode)) {
						$iio++;
					} else if (in_array("CREDIT CARD/DEBIT CARD", $mode)) {
						$iio++;
					} ?>
					<td class="text-bold text-green" colspan="<? echo $iio++; ?>" style="align:center;">GRAND TOTAL</td>
					<?php $k = 1;
					$dtotla = 0;
					foreach ($selectField as $j => $el) {   ?>
						<td class="text-bold text-green"><span class="text-black">&#8377; </span>
							<?php
							$el = trim($el);

							if ($el == "Due Amount") {
								$dtotla -= $totaladmissions;
								echo $totaladmissions;
							} else if ($el == "Prospectus") {
								echo $totalprospectus;
								$dtotla += $totalprospectus;
							} else if ($el == "Registration") {
								echo $totalapplicant;
								$dtotla += $totalapplicant;
							} else if ($el == "Quater1") {
								echo $totaltution1;
								$dtotla += $totaltution1;
							} else if ($el == "Quater2") {
								echo $totaltution2;
								$dtotla += $totaltution2;
							} else if ($el == "Quater3") {
								echo $totaltution3;
								$dtotla += $totaltution3;
							} else if ($el == "Quater4") {
								echo $totaltution4;
								$dtotla += $totaltution4;
							}else if ($el == "Transport5") {
								echo $totaltransport1;
								$dtotla += $totaltransport1;
							}else if ($el == "Transport6") {
								echo $totaltransport2;
								$dtotla += $totaltransport2;
							}else if ($el == "Transport7") {
								echo $totaltransport3;
								$dtotla += $totaltransport3;
							}else if ($el == "Transport8") {
								echo $totaltransport4;
								$dtotla += $totaltransport4;
							} else if ($el == "Late Fee") {
								echo $totallfine;
								$dtotla += $totallfine;
							} else if ($el == "Prev. Due") {
								echo $totalOther;
								$dtotla += $totalOther;
							} else if ($el == "Discount Fee") {
								echo "-" . $totaldiscount;
								$dtotla += "-" . $totaldiscount;
							} else if ($el == "Other Discount") {
								echo "-" . $totalOtherdiscount;
								$dtotla += "-" . $totalOtherdiscount;
							} else {

								$res = array();
								$vsk = 0;
								foreach ($totalass as $k => $v) {
									foreach ($v as $ks => $vs) {
										if ($ks == $el) {

											$vsk += $vs;
										}
									}
								}
								echo $vsk;
								$dtotla += $vsk;
							}

							$j++;

							?>
						</td>
					<? }  ?>
					<td class="text-bold text-red"><span class="text-black"><b>&#8377; </span><?php setlocale(LC_MONETARY, 'en_IN'); $dtotlass = money_format('%!i', $sumtotal); echo $dtotlass;  ?>
						</b>
						</td>
						</tr>
	</table>
	<div>