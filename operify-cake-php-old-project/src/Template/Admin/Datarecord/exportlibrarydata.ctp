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
							 
								 ->setCellValue('A1', 'S.NO')                          
								 ->setCellValue('B1', 'Book_type')
								 ->setCellValue('C1', 'Book_Category*')
								 ->setCellValue('D1', 'Accsnno*')
								 ->setCellValue('E1', 'ISBN_NO*')
								 ->setCellValue('F1', 'Langauge')
								 ->setCellValue('G1', 'Book Name*')
								 ->setCellValue('H1', 'Sub_title*')
								 ->setCellValue('I1', 'Author*')
								 ->setCellValue('J1', 'Publisher')
								 ->setCellValue('K1', 'Publishyear')
								 ->setCellValue('L1', 'Edition')
								 ->setCellValue('M1', 'Vol')
								 ->setCellValue('N1', 'Copy')
								 ->setCellValue('O1', 'Book cost*')
								 ->setCellValue('P1', 'Remarks')
								 ->setCellValue('Q1', 'Cup_board*')
								 ->setCellValue('R1', 'Cup_board_shelf*')
								 ->setCellValue('S1', 'Bilno')
								 ->setCellValue('T1', 'Bill_date')
								 ->setCellValue('U1', 'Vendor')
								 ->setCellValue('V1', 'Added Date*');
								 
							// pr($employee);die;
							 if (isset($employee) && !empty($employee)) {
								 foreach ($employee as $i => $people) {
									 //$emp_sal = $this->Comman->findemplobasic($people['id']);
									 $ii = $i + 2;
                                     $cnt = 1;{
							 
									 $objPHPExcel->getActiveSheet()->setCellValue('A' . $ii, $cnt++);
									 $objPHPExcel->getActiveSheet()->setCellValue('B' . $ii, $people["book_type"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('C' . $ii, $people["book_category_id"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('D' . $ii, $people["accsnno"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('E' . $ii, $people["ISBN_NO"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('F' . $ii, $people["lang"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('G' . $ii, $people["name"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('H' . $ii, $people["sub_title"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('I' . $ii, $people["author"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('J' . $ii, $people["publisher"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('K' . $ii, $people["pbyr"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('L' . $ii, $people["edition"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('M' . $ii, $people["vol"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('N' . $ii, $people["copy"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('O' . $ii, $people["book_cost"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('P' . $ii, $people["remarks"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('Q' . $ii, $people["cup_board_id"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('R' . $ii, $people["cup_board_shelf_id"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('S' . $ii, $people["bilno"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('T' . $ii, date('Y-m-d', strtotime($people['bildt'])));        
									 $objPHPExcel->getActiveSheet()->setCellValue('U' . $ii, $people["vndr"]);
									 $objPHPExcel->getActiveSheet()->setCellValue('V' . $ii, date('Y-m-d', strtotime($people['created'])));   
									

									
									
								 }
                                }
							 }
							 


// Rename sheet
//$objPHPExcel->getActiveSheet()->setTitle('Simple');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel2007)
$filename = "ExportLibrary_Data".date('d-m-Y').".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename='.$filename);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
?>
