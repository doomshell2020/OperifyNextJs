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


$objPHPExcel->setActiveSheetIndex(0)

    ->setCellValue('A1', 'S.No.')
    ->setCellValue('B1', 'Employee Name*')
    ->setCellValue('C1', 'Father/Husband Name*')
    ->setCellValue('D1', 'DOB*')
    ->setCellValue('E1', 'Department*')
    ->setCellValue('F1', 'Designation*')
    ->setCellValue('G1', 'Contact No*')
    ->setCellValue('H1', 'Emp Status*')
    ->setCellValue('I1', 'Joining Date*')
    ->setCellValue('J1', 'Email*')
    ->setCellValue('K1', 'Experiance')
    ->setCellValue('L1', 'Martial_status')
    ->setCellValue('M1', 'Fixed_Salary*')
    ->setCellValue('N1', 'Basic_Salary*')
    ->setCellValue('O1', 'Grade_pay')
    ->setCellValue('P1', 'DA_Amount*')
    ->setCellValue('Q1', 'HRA_Amount*')
    ->setCellValue('R1', 'CCA_Amount*')
    ->setCellValue('S1', 'Allowance*')
    ->setCellValue('T1', 'LTA_Amount*')
    ->setCellValue('U1', 'Bonus')
    ->setCellValue('V1', 'Total*')
    ->setCellValue('W1', 'PF_Deduction*')
    ->setCellValue('X1', 'ESI_Deduction*')
    ->setCellValue('Y1', 'Netpay*')
    ->setCellValue('Z1', 'Payment_mode*')
    ->setCellValue('AA1', 'Bank_name')
    ->setCellValue('AB1', 'Bank_IFSC')
    ->setCellValue('AC1', 'Bank_account_no')
    ->setCellValue('AD1', 'UAN_no')
    ->setCellValue('AE1', 'PF_number*')
    ->setCellValue('AF1', 'ESI_no*')
    ->setCellValue('AG1', 'TDS_amt*');
   



   

$counter = 1;
//pr($employee);die;
if (isset($employee) && !empty($employee)) {
    foreach ($employee as $i => $people) {
       
        $ii = $i + 2;

        $objPHPExcel->getActiveSheet()->setCellValue('A' . $ii, $counter++);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $ii, ucwords(strtolower($people["fname"] . ' ' . $people["middlename"] . ' ' . $people["lname"])));
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $ii, $people["f_h_name"]);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $ii, $people["dob"]);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $ii, $people["department_id"]);
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $ii, $people["designation_id"]);
        $objPHPExcel->getActiveSheet()->setCellValue('G' . $ii, $people["mobile"]);
        $objPHPExcel->getActiveSheet()->setCellValue('H' . $ii, $people["emp_status"]);
        $objPHPExcel->getActiveSheet()->setCellValue('I' . $ii, $people["joiningdate"]);
        $objPHPExcel->getActiveSheet()->setCellValue('J' . $ii, $people["email"]);
        $objPHPExcel->getActiveSheet()->setCellValue('K' . $ii, $people["experience"]);
        $objPHPExcel->getActiveSheet()->setCellValue('L' . $ii, $people["martial_status"]);
        $objPHPExcel->getActiveSheet()->setCellValue('M' . $ii, $people["employeesalary"]["fsalary"]);
        $objPHPExcel->getActiveSheet()->setCellValue('N' . $ii, $people["employeesalary"]["basic_salary"]);
        $objPHPExcel->getActiveSheet()->setCellValue('O' . $ii, $people["employeesalary"]["grade_pay"]);
        $objPHPExcel->getActiveSheet()->setCellValue('P' . $ii, $people["employeesalary"]["da_amt"]);
        $objPHPExcel->getActiveSheet()->setCellValue('Q' . $ii, $people["employeesalary"]["hra_amt"]);
        $objPHPExcel->getActiveSheet()->setCellValue('R' . $ii, $people["employeesalary"]["cca_amt"]);
        $objPHPExcel->getActiveSheet()->setCellValue('S' . $ii, $people["employeesalary"]["spl_all"]);
        $objPHPExcel->getActiveSheet()->setCellValue('T' . $ii, $people["employeesalary"]["lta_amt"]);
        $objPHPExcel->getActiveSheet()->setCellValue('U' . $ii, $people["employeesalary"]["bonus"]);
        $objPHPExcel->getActiveSheet()->setCellValue('V' . $ii, $people["employeesalary"]["total"]);
        $objPHPExcel->getActiveSheet()->setCellValue('W' . $ii, $people["employeesalary"]["pfdeduction"]);
        $objPHPExcel->getActiveSheet()->setCellValue('X' . $ii, $people["employeesalary"]["esideduction"]);
        $objPHPExcel->getActiveSheet()->setCellValue('Y' . $ii, $people["employeesalary"]["netpay"]);
        $objPHPExcel->getActiveSheet()->setCellValue('Z' . $ii, $people["employeesalary"]["payment_mode"]);
        $objPHPExcel->getActiveSheet()->setCellValue('AA' . $ii, $people["employeesalary"]["bank_name"]);
        $objPHPExcel->getActiveSheet()->setCellValue('AB' . $ii, $people["employeesalary"]["bank_ifsc"]);
        $objPHPExcel->getActiveSheet()->setCellValue('AC' . $ii, $people["employeesalary"]["bank_account_no"]);
        $objPHPExcel->getActiveSheet()->setCellValue('AD' . $ii, $people["employeesalary"]["uan_no"]);
        $objPHPExcel->getActiveSheet()->setCellValue('AE' . $ii, $people["employeesalary"]["pfnumber"]);
        $objPHPExcel->getActiveSheet()->setCellValue('AF' . $ii, $people["employeesalary"]["esi_no"]);
        $objPHPExcel->getActiveSheet()->setCellValue('AG' . $ii, $people["employeesalary"]["tds_amt"]);



        
       
    }

}

// Rename sheet
//$objPHPExcel->getActiveSheet()->setTitle('Simple');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel2007)
$filename = "Teacher Details.xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $filename);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
