<?php
$objPHPExcel = new PHPExcel();
// Set properties
$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
    ->setLastModifiedBy("Maarten Balliauw")
    ->setTitle("Office 2007 XLSX Test Document")
    ->setSubject("Office 2007 XLSX Test Document")
    ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
    ->setKeywords("office 2007 openxml php")
    ->setCategory("Test result file");
// Miscellaneous glyphs, UTF-8
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('V')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('W')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('X')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('AB')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('AD')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('AE')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('AF')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('AG')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('AH')->setAutoSize(true);

$objPHPExcel->setActiveSheetIndex(0)

    ->setCellValue('A1', 'S.No.')
    ->setCellValue('B1', 'Eid*')
    ->setCellValue('C1', 'Employee Name*')
    ->setCellValue('D1', 'Father/Husband Name*')
    ->setCellValue('E1', 'DOB*')
    ->setCellValue('F1', 'Department*')
    ->setCellValue('G1', 'Designation*')
    ->setCellValue('H1', 'Contact No*')
    ->setCellValue('I1', 'Gender')
    ->setCellValue('J1', 'Maritial Status')
    ->setCellValue('K1', 'Emp Status*')
    ->setCellValue('L1', 'Joining Date*')
    ->setCellValue('M1', 'Fixed Salary*')
    ->setCellValue('N1', 'Basic')
    ->setCellValue('O1', 'DA')
    ->setCellValue('P1', 'HRA')
    ->setCellValue('Q1', 'CCA')
    ->setCellValue('R1', 'SPL. ALL.')
    ->setCellValue('S1', 'LTA')
    ->setCellValue('T1', 'OTHER ALL')
    ->setCellValue('U1', 'Grade Pay')
    ->setCellValue('V1', 'G.Total*')
    ->setCellValue('W1', 'Employers Bank')
    ->setCellValue('X1', 'Employee Bank Account No.')
    ->setCellValue('Y1', 'Employee Bank Name')
    ->setCellValue('Z1', 'Employee Bank IFSC')
    ->setCellValue('AA1', 'UAN No.')
    ->setCellValue('AB1', 'ESI No.')
    ->setCellValue('AC1', 'PF(0/1)')
    ->setCellValue('AD1', 'PF')
    ->setCellValue('AE1', 'ESI(0/1)')  
    ->setCellValue('AF1', 'ESI') 
    ->setCellValue('AG1', 'SD')
    ->setCellValue('AH1', 'SD %')
    ->setCellValue('AI1', 'Email');
$objPHPExcel->setActiveSheetIndex(0)

    ->setCellValue('A2', 'sample')
    ->setCellValue('B2', 'new/1111')
    ->setCellValue('C2', 'fname middlename lname')
    ->setCellValue('D2', 'Father/Husband Name*')
    ->setCellValue('E2', 'dd-mm-yyyy')
    ->setCellValue('F2', 'Admin')
    ->setCellValue('G2', 'manager')
    ->setCellValue('H2', '1234567892*')
    ->setCellValue('I2', 'male/female')
    ->setCellValue('J2', 'married/unmarried')
    ->setCellValue('K2', 'confirm')
    ->setCellValue('L2', 'dd-mm-yyyy')
    ->setCellValue('M2', '300000')
    ->setCellValue('N2', '10000')
    ->setCellValue('O2', '3000')
    ->setCellValue('P2', '3000')
    ->setCellValue('Q2', '3000')
    ->setCellValue('R2', '3000')
    ->setCellValue('S2', '3000')
    ->setCellValue('T2', '5000')
    ->setCellValue('U2', '0')
    ->setCellValue('V2', '30000')
    ->setCellValue('W2', 'AXIS Bank')
    ->setCellValue('X2', '9876543210123')
    ->setCellValue('Y2', 'AXIS Bank')
    ->setCellValue('Z2', 'AXIX000011')
    ->setCellValue('AA2', '123456780')
    ->setCellValue('AB2', '1234567890')
    ->setCellValue('AC2', '0/1')
    ->setCellValue('AD2', '100')
    ->setCellValue('AE2', '0/1')
    ->setCellValue('AF2', '100')
    ->setCellValue('AG2', '0/1')
    ->setCellValue('AH2', 'in %')
    ->setCellValue('AI2', 'email address');
    $objPHPExcel->getActiveSheet() ->getStyle('E1:E10000') ->getNumberFormat() ->setFormatCode('DD-MM-YYYY');
    $objPHPExcel->getActiveSheet() ->getStyle('L1:L10000') ->getNumberFormat() ->setFormatCode('DD-MM-YYYY');

$counter = 1;
//pr($employee);die;
if (isset($employee) && !empty($employee)) {
    foreach ($employee as $i => $people) {
        $emp_sal = $this->Comman->findemplobasic($people['id']);
        $ii = $i + 3;

        $objPHPExcel->getActiveSheet()->setCellValue('A' . $ii, $counter++);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $ii, $people["id"]);
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $ii, ucwords(strtolower($people["fname"] . ' ' . $people["middlename"] . ' ' . $people["lname"])));
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $ii, ucwords($people["f_h_name"]));
        $dob = date("d-m-Y", strtotime($people["dob"]));
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $ii,PHPExcel_Shared_Date::PHPToExcel($dob));
        $dept = $this->finddeptmode($people["p_department"]);
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $ii, $dept["name"]);
        $desg = $this->finddesgmode($people["p_designation"]);
        $objPHPExcel->getActiveSheet()->setCellValue('G' . $ii, $desg["name"]);
        $objPHPExcel->getActiveSheet()->setCellValue('H' . $ii, $people["mobile"]);
        $objPHPExcel->getActiveSheet()->setCellValue('I' . $ii, $people["gender"]);
        $objPHPExcel->getActiveSheet()->setCellValue('J' . $ii, $people["martial_status"]);
        $objPHPExcel->getActiveSheet()->setCellValue('K' . $ii, $people["emp_status"]);
        $datejoin = date("d-m-Y", strtotime($people["joiningdate"]));
        $objPHPExcel->getActiveSheet()->setCellValue('L' . $ii, PHPExcel_Shared_Date::PHPToExcel($datejoin));
        $objPHPExcel->getActiveSheet()->setCellValue('M' . $ii, $emp_sal["fsalary"]);
        $objPHPExcel->getActiveSheet()->setCellValue('N' . $ii, $emp_sal["basic_salary"]);
        $objPHPExcel->getActiveSheet()->setCellValue('O' . $ii, $emp_sal["da_amt"]);
        $objPHPExcel->getActiveSheet()->setCellValue('P' . $ii, $emp_sal["hra_amt"]);
        $objPHPExcel->getActiveSheet()->setCellValue('Q' . $ii, $emp_sal["cca_amt"]);
        $objPHPExcel->getActiveSheet()->setCellValue('R' . $ii, $emp_sal["spl_all"]);
        $objPHPExcel->getActiveSheet()->setCellValue('S' . $ii, $emp_sal["lta_amt"]);
        $objPHPExcel->getActiveSheet()->setCellValue('T' . $ii, $emp_sal["bonus"]);
        $objPHPExcel->getActiveSheet()->setCellValue('U' . $ii, $emp_sal["grade_pay"]);
        $objPHPExcel->getActiveSheet()->setCellValue('V' . $ii, $emp_sal["total"]);
        $bank = $this->findpaymentmode($emp_sal["payment_mode"]);
        $objPHPExcel->getActiveSheet()->setCellValue('W' . $ii, $bank['name']);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('X' . $ii, "'" . $emp_sal["bank_account_no"]);
        $objPHPExcel->getActiveSheet()->setCellValue('Y' . $ii, $emp_sal["bank_name"]);
        $objPHPExcel->getActiveSheet()->setCellValue('Z' . $ii, $emp_sal["bank_ifsc"]);
        $objPHPExcel->getActiveSheet()->setCellValue('AA' . $ii, $emp_sal["uan_no"]);
        $objPHPExcel->getActiveSheet()->setCellValue('AB' . $ii, $emp_sal["esi_no"]);
        $objPHPExcel->getActiveSheet()->setCellValue('AC' . $ii, $emp_sal["PF"]);
        $objPHPExcel->getActiveSheet()->setCellValue('AD' . $ii, $emp_sal["pfdeduction"]);
        $objPHPExcel->getActiveSheet()->setCellValue('AE' . $ii, $emp_sal["ESI_choice"]);
        $objPHPExcel->getActiveSheet()->setCellValue('AF' . $ii, $emp_sal["esideduction"]);
        $objPHPExcel->getActiveSheet()->setCellValue('AG' . $ii, $emp_sal["sd"]);
        $objPHPExcel->getActiveSheet()->setCellValue('AH' . $ii, $emp_sal["sd_perc"]);
        $objPHPExcel->getActiveSheet()->setCellValue('AI' . $ii, $people["email"]);

    }

}

// Rename sheet
//$objPHPExcel->getActiveSheet()->setTitle('Simple');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel2007)
$filename = "EmployeePayroll_Settings.xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $filename);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
