<?php
//pr($date);die;
$d_date = $date;
$mon = date('m', $date);
$date1 = explode('-', $date);
$mon = $date1[0];
$year = $date1[1];
$monthName = date('F', mktime(0, 0, 0, $mon, 10));

$days = 1;
$startdate = $year . '-' . $mon . '-01';
$start_time = strtotime($startdate);
$end_time = strtotime("+1 month", $start_time);
$date_range = array();
$alph = "C";
$hol = 0;
$objPHPExcel = new PHPExcel();
// Set properties
$style = array(
    'font' => array(
        'bold' => true,
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    ),
);
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
// $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:D1');
$objPHPExcel->setActiveSheetIndex(0)->getStyle("A1:D1")->applyFromArray($style);
$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A1', 'Staff_Attendance list for ' . $monthName . '-' . $year);
$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A3', 'SR.No.')
    ->setCellValue('B3', 'Emp. ID')
    ->setCellValue('C3', 'Name')
    // ->setCellValue('D3', 'Department')
    ->setCellValue('D3', 'Total Absent (DAYS)');

$count = 1;
$row = 4;


// pr($date);
$mon = date('m', $date);

$d_date = $date;
// pr($d_date);die;
$date1 = explode('-', $date);
$mon = $date1[0];
$year = $date1[1];
$days = 1;
$startdate = $year . '-' . $mon . '-01';
$start_time = strtotime($startdate);
$acd = substr($acd, 0, 4);
$acdfrom = $acd . "-04-01";
// echo $acdfrom; die;
$end_time = strtotime("+1 month", $start_time);
$date_range = array();
for ($i = $start_time; $i < $end_time; $i += 86400) {
    $dayOfWeek = date("l", $i);
    $pre_date = date('Y-m-d', $i);

    $holiday = $this->Comman->findholidaymonth($pre_date);
    // pr($dayOfWeek);die;
    if ($dayOfWeek != 'Sunday' && $holiday == 0) {

        $date_range[] = $pre_date;
    } else {
        if ($dayOfWeek == 'Sunday') {
            $date_range[] = 'sun';
        } else if ($holiday > 0) {

            $date_range[] = 'hol';
        }
    }

    $days++;
}


foreach ($dept as $dept_val) {
    // pr($dept_val);die;
    $emp = $this->Comman->empByPId($dept_val['id'], $mon, $year);
    // pr($emp);die;

    foreach ($emp as $emp_values) {
        $LWP = 0;
        $emp_atn = "";

        $P = 0;
        $PR = 0;
        $PL = 0;
        $A = 0;
        $d_cnt = 1;
        $t_hol = 0;
        $L = 0;
        $d_cnt = 1;
        // pr($date_range);die;
        $i = 1;
        foreach ($date_range as $date_val) {
            if ($d_cnt <= $j_day) {
                $d_cnt++;
                continue;
            }
            $p_date = $d_cnt . "-" . $d_date;

            if (strtotime($p_date) > strtotime(date('d-m-Y'))) {

                $d_cnt++;
                continue;
            }
            if ($drop_day > 0 && $d_cnt > $drop_day) {

                $d_cnt++;
                $A += 1;
                continue;
            }

            $status = '';
            $emp_atn = '';
            if ($date_val != 'hol' && $date_val != 'sun') {
                // pr($date_val);die;
                $emp_atn = $this->Comman->findEmp_Attendance_bydate($emp_values['id'], $date_val);
                // pr($emp_atn);
                if (!empty($emp_atn)) {

                    $status = $emp_atn['status'];

                    if ($status == 'A') {

                        $A += 1;

                    } else if ($status == 'P') {
                        
                        // $P += 1;
                    }
                } else {

                    $A += 1;
                }
            } else {

                $payroll = $this->payroll();

                  if ($payroll['holiday_allow'] != 1) {

                }
            }

            $d_cnt++;
            $i++;
        }
        // pr($P);die;
        // pr($mon);die;
        // $emp_atn = $this->Comman->findEmp_Att_lwp($emp_values['id'], $mon, $year);

        // if (!empty($emp_atn)) {
        // pr($ab);die;
        // foreach ($emp_atn as $ab) {
        //     $date_val = date('Y-m-d', strtotime($ab['date']));
        //     $holiday = $this->Comman->findholidaymonth($date_val);
        //     //pr($date_val);die;
        //     $lev_status = 0;
        //     $lev_status = $this->Comman->leaveStatus($date_val, $emp_values['id']);
        //     if ($holiday == 0) {
        //         if ($ab['status'] == 'A' && (empty($lev_status) || $lev_status['leave_type'] == "unpaid")) {
        //             $LWP += 1;

        //         } else if (($ab['status'] == 'PR' || $ab['status'] == 'PL') && (empty($lev_status) || $lev_status['leave_type'] == "unpaid")) {
        //             $LWP += 0.5;
        //         }
        //     }

        // }
        // if ($LWP == 0) {
        //     continue;
        // }


        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $row, $count)
            ->setCellValue('B' . $row, $emp_values['id'])
            ->setCellValue('C' . $row, strtoupper($emp_values['fname']) . "\x20" . strtoupper($emp_values['middlename']) . "\x20" . strtoupper($emp_values['lname']))
            // ->setCellValue('D' . $row, $dept_val['name'])
            ->setCellValue('D' . $row, $A);
        $count++;
        $row++;

        // }

    }
}

// Rename sheet
//$objPHPExcel->getActiveSheet()->setTitle('Simple');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web $colser (Excel2007)
$filename = "Monthly_Attendance_report(" . $date . ").xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $filename);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
