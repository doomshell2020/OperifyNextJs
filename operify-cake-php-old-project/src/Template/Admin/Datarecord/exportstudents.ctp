<?php
$objPHPExcel = new PHPExcel();
// Set properties
$objSheet = $objPHPExcel->getActiveSheet();
$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
							 ->setLastModifiedBy("Maarten Balliauw")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Test result file");
							 
							 $objPHPExcel->setActiveSheetIndex(0)
							 
								 ->setCellValue('A1', 'Scholar.No.')
								 ->setCellValue('B1', 'Name')
								 ->setCellValue('C1', 'Email')
								 ->setCellValue('D1', 'Gender')
								 ->setCellValue('E1', 'Height')
								 ->setCellValue('F1', 'Weight')
								 ->setCellValue('G1', 'Adaharnumber')
								 ->setCellValue('H1', 'Cast')
								 ->setCellValue('I1', 'Religion')
								 ->setCellValue('J1', 'Category')
								 ->setCellValue('K1', 'Bloodgroup')
								 ->setCellValue('L1', 'Disability')
								 ->setCellValue('M1', 'MotherTounge')
								 ->setCellValue('N1', 'Address')
								 ->setCellValue('O1', 'RF ID')
								 ->setCellValue('P1', 'HEX CODE')
								 ->setCellValue('Q1', 'Mobile')
								 ->setCellValue('R1', 'SMSMobile')
								 ->setCellValue('S1', 'FatherPhone')
								 ->setCellValue('T1', 'MotherPhone')
								 ->setCellValue('U1', 'AdmissionYear')
								 ->setCellValue('V1', 'AcademicYear')
								 ->setCellValue('W1', 'AdmissionDate')
								 ->setCellValue('X1', 'FormNo.')
								 ->setCellValue('Y1', 'Board')
								 ->setCellValue('Z1', 'Admission Class')
								 ->setCellValue('AA1', 'Class')
								 ->setCellValue('AB1', 'Section')
								 ->setCellValue('AC1', 'House')
								 ->setCellValue('AD1', 'Discount')
								 ->setCellValue('AE1', 'IsLearningCenter')
								 ->setCellValue('AF1', 'IsSpecial')
								 ->setCellValue('AG1', 'Old enroll')
								 ->setCellValue('AH1', 'FatherName')
								 ->setCellValue('AI1', 'MotherName')
								 ->setCellValue('AJ1', 'FeeSubmittedBy')
								 ->setCellValue('AK1', 'FatherQualification')
								 ->setCellValue('AL1', 'MotherQualification')
								 ->setCellValue('AM1', 'FatherOccupation')
								 ->setCellValue('AN1', 'MotherOccupation')
								 ->setCellValue('AO1', 'DOB');
							// pr($resul);die;
							 if (isset($resul) && !empty($resul)) {
								 foreach ($resul as $i => $people) {
									 //$emp_sal = $this->Comman->findemplobasic($people['id']);
									 $ii = $i + 2;
							 
									 $objPHPExcel->getActiveSheet()->setCellValue('A' . $ii, $people["enroll"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('B' . $ii, $people["fname"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('C' . $ii, $people["username"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('D' . $ii, $people["gender"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('E' . $ii, $people["height"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('F' . $ii, $people["weight"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('G' . $ii, $people["adaharnumber"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('H' . $ii, $people["cast"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('I' . $ii, $people["religion"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('J' . $ii, $people["category"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('K' . $ii, $people["bloodgroup"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('L' . $ii, $people["disability"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('M' . $ii, $people["mother_tounge"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('N' . $ii, $people["address"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('O' . $ii, $people["rf_id"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('P' . $ii, $people["rfidhexcode"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('Q' . $ii, $people["mobile"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('R' . $ii, $people["sms_mobile"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('S' . $ii, $people["f_phone"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('T' . $ii, $people["m_phone"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('U' . $ii, $people["admissionyear"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('V' . $ii, $people["acedmicyear"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('W' . $ii, $people["-"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('X' . $ii, $people["formno"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('Y' . $ii, $people["board_id"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('Z' . $ii, $people["admissionclass"]);

									 $objPHPExcel->getActiveSheet()->setCellValue('AA1' . $ii, $people["class_id"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('AB1' . $ii, $people["section_id"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('AC1' . $ii, $people["h_id"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('AD1' . $ii, $people["is_discount"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('AE1' . $ii, $people["is_lc"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('AF1' . $ii, $people["is_special"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('AG1' . $ii, $people["oldenroll"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('AH1' . $ii, $people["fathername"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('AI1' . $ii, $people["mothername"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('AJ1' . $ii, $people["fee_submittedby"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('AK1' . $ii, $people["f_qualification"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('AL1' . $ii, $people["m_qualification"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('AM1' . $ii, $people["f_occupation"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('AN1' . $ii, $people["m_occupation"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('AO1' . $ii, $people["dob"]);


									//  if(!empty($people["dob"])){
									// 	 $dob = date("d-m-Y", strtotime($people["dob"]));
									//  }
									//  else{
									// 	 $dob='';
									//  }
									//  $objPHPExcel->getActiveSheet()->setCellValue('F' . $ii, $dob);
									//  if(!empty($people["joiningdate"])){
									// 	 $datejoin = date("d-m-Y", strtotime($people["joiningdate"]));
									//  }
									//  else{
									// 	 $datejoin='';
									//  }
									//  $objPHPExcel->getActiveSheet()->setCellValue('G' . $ii, $datejoin);
									
								 }
							 
							 }
							 


// Rename sheet
//$objPHPExcel->getActiveSheet()->setTitle('Simple');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel2007)
$filename = "ExportStudent_Info_".date('d-m-Y').".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename='.$filename);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
?>
