<?php
//pr($date);die;
// $d_date = $date;
// $mon = date('m', $date);
// $date1 = explode('-', $date);
// $mon = $date1[0];
// $year = $date1[1];
// $monthName = date('F', mktime(0, 0, 0, $mon, 10));

// $days = 1;
// $startdate = $year . '-' . $mon . '-01';
// $start_time = strtotime($startdate);
// $end_time = strtotime("+1 month", $start_time);
// $date_range = array();
// $alph = "C";
// $hol = 0;
// $objPHPExcel = new PHPExcel();
// // Set properties
// $style = array(
//     'font' => array(
//         'bold' => true,
//     ),
//     'alignment' => array(
//         'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
//     ),
// );
// $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
//     ->setLastModifiedBy("Maarten Balliauw")
//     ->setTitle("Office 2007 XLSX Test Document")
//     ->setSubject("Office 2007 XLSX Test Document")
//     ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
//     ->setKeywords("office 2007 openxml php")
//     ->setCategory("Test result file");
// // Miscellaneous glyphs, UTF-8

// $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
// $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
// $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
// $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
// $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
// $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:E1');
// $objPHPExcel->setActiveSheetIndex(0)->getStyle("A1:E1")->applyFromArray($style);
// $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', 'LWP list for ' . $monthName . '-' . $year);
// $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A3', 'SR.No.')
//     ->setCellValue('B3', 'Emp. ID')
//     ->setCellValue('C3', 'Name')
//     ->setCellValue('D3', 'Department')
//     ->setCellValue('E3', 'No of Absent(Days)');


// $mon = date('m', $date);
// $d_date = $date;
// $date1 = explode('-', $date);
// $mon = $date1[0];
// $year = $date1[1];
// $days = 1;
// $startdate = $year . '-' . $mon . '-01';
// $start_time = strtotime($startdate);
// $acd = substr($acd, 0, 4);
// $acdfrom = $acd . "-04-01";
// // echo $acdfrom; die;
// $end_time = strtotime("+1 month", $start_time);
// $date_range = array();
// for ($i = $start_time; $i < $end_time; $i += 86400) {
//     $dayOfWeek = date("l", $i);
//     $pre_date = date('Y-m-d', $i);
//     $holiday = $this->Comman->findholidaymonth($pre_date);
//     //pr($holiday);
//     if ($dayOfWeek != 'Sunday' && $holiday == 0) {
//         // echo $days; 
//         $date_range[] = $pre_date;
//     } else {
//         if ($dayOfWeek == 'Sunday') {
//             // echo $days; 
//             $date_range[] = 'sun';
//         } else if ($holiday > 0) {
//             // echo $days;

//             //$date_range[] = 'hol';
//             $date_range[] = 'hol';
//         }
//     }

//     $days++;
// }


// $count = 1;

// $row = 4;
// foreach ($dept as $dept_val) {
//     $emp = $this->Comman->empByPId($dept_val['id'],$mon,$year);
//     // $emp = $this->Comman->empByPId($dept_val['id']);
//     foreach ($emp as $emp_values) {
//         $P = 0;
//         $PR = 0;
//         $PL = 0;
//         $A = 0;
//         $d_cnt = 1;
//         $t_hol = 0;
//         $L = 0;
//         $d_cnt = 1;
//         //pr($date_range);die;
//         $i = 1;
//         foreach ($date_range as $date_val) {
//             if ($d_cnt <= $j_day) {
//                 $d_cnt++;
//                 continue;
//             }

//             $status = '';
//             $emp_atn = '';
// if ($date_val != 'hol' && $date_val != 'sun') {
//     //pr($date_val);
//     $emp_atn = $this->Comman->findEmp_Att_bydate($emp_values['id'], $date_val);
//     // pr($emp_atn);die;
//     if (!empty($emp_atn)) {
//         $status = $emp_atn['status'];
//         if ($status == 'A') {
//             $A += 1;
//         } else if ($status == 'HF') {
//             $PR += 0.5;
//         } else if ($status == 'SL') {
//             $PL += 0.25;
//         }
//     } else {
//         $count;
//     }
// }
//         }
//         $LWP = $A + $PR + $PL;
//         if (!empty($emp_values)) {

//             $objPHPExcel->setActiveSheetIndex(0)
//                 ->setCellValue('A' . $row, $count)
//                 ->setCellValue('B' . $row, $emp_values['id'])
//                 ->setCellValue('C' . $row, strtoupper($emp_values['fname']) . "\x20" . strtoupper($emp_values['middlename']) . "\x20"
//                     . strtoupper($emp_values['lname']))
//                 ->setCellValue('D' . $row, $dept_val['name'])
//                 ->setCellValue('E' . $row, $LWP);
//             $count++;
//             $row++;
//         }
//     }
// }

// // Rename sheet
// //$objPHPExcel->getActiveSheet()->setTitle('Simple');
// // Set active sheet index to the first sheet, so Excel opens this as the first sheet
// $objPHPExcel->setActiveSheetIndex(0);
// // Redirect output to a client’s web $colser (Excel2007)
// $filename = "Monthly_Attendance_report(" . $date . ").xlsx";
// header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
// header('Content-Disposition: attachment;filename=' . $filename);
// header('Cache-Control: max-age=0');
// $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
// $objWriter->save('php://output');
// exit;



// pr($date);die;
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
    )
);

$style2 = array(
    'font' => array(
        'bold' => true,
    ),
);

$hollyday_color =    array(
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb' => 'FF0000')
    )
);

$paresent_color =    array(
    'font' => array(
        'bold' => true,
        'color' => array('rgb' => '008000')
    ),
    // 'fill' => array(
    //     'type' => PHPExcel_Style_Fill::FILL_SOLID,
    //     'color' => array('rgb' => '00FF00')
    // )
);

$out_time_color = array(
    'font' => array(
        'bold' => true,
        'color' => array('rgb' => '6262c9')
    ),
);

$Half_day_color = array(
    'font' => array(
        'bold' => true,
        'color' => array('rgb' => 'ff0000')
    ),
);

$objPHPExcel->getActiveSheet()
    ->getStyle('A1:AG1')
    ->getAlignment()
    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


$total_row = 1;
foreach ($dept as $dept_val) {
    $emp = $this->Comman->empByPId($dept_val['id'], $mon, $year);
    foreach ($emp as $emp_values) {
        $total_row += 1;
    }
}
// pr($total_row); die;
$styleArray = array(
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        )
    )
);
$objPHPExcel->getActiveSheet()->getStyle('A1:AMJ' . $total_row)->getFont()->setSize(9);
$objPHPExcel->getActiveSheet()->getStyle('A1:AMJ' . $total_row)->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()
    ->getStyle('A1:AMJ' . $total_row)
    ->getAlignment()
    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$objPHPExcel->setActiveSheetIndex(0)->getStyle("A1:AH1")->applyFromArray($style2);

$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
    ->setLastModifiedBy("Maarten Balliauw")
    ->setTitle("Office 2007 XLSX Test Document")
    ->setSubject("Office 2007 XLSX Test Document")
    ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
    ->setKeywords("office 2007 openxml php")
    ->setCategory("Test result file");
// Miscellaneous glyphs, UTF-8

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(3);
// $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);

$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(13);
// $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:G1');
// $objPHPExcel->setActiveSheetIndex(0)->getStyle("A1:G1")->applyFromArray($style);
// $objPHPExcel->setActiveSheetIndex(0)
//     ->setCellValue('A1', 'Attendance list for ' . $monthName . '-' . $year);
$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A1', 'No')
    ->setCellValue('B1', 'Name');

$no = 1;
for ($i = 'C'; $i <= 'Z'; $i++) {
    // pr($no); die;
    $attn_datee = $no . '-' . $date;
    $dayy =  date("D", strtotime($attn_datee));
    $objPHPExcel->getActiveSheet()->getColumnDimension($i)->setWidth(3);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($i . '1', $no);
    if ($dayy == "Sun") {
        $colmn[] = $i;
        // $objPHPExcel->setActiveSheetIndex(0)->removeColumn($i);
    }
    $no++;
    if ($no > $total_days) {
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue((++$i) . '1', "Total AB");
        $A_col_num = $i;
        //$objPHPExcel->setActiveSheetIndex(0)->setCellValue((++$i).'1', "Total HF");
        //$HF_col_num = $i;
        break;
    }
}

// pr($colmn); die;

$count = 1;
$row = 2;
$department = null;

foreach ($dept as $dept_val) {

    $emp = $this->Comman->empByPId($dept_val['id'], $mon, $year);
    $noo = 0;
    foreach ($emp as $emp_values) {
        // pr($emp_values);die;
        if($dept_val['id']==2){
        $department = "*";
        }else{
            $department = null;
        }

        $fullname = ucwords(strtolower($emp_values['fname'])) . ' ' . ucwords(strtolower($emp_values['lname'])).$department;
        // pr($fullname);die;

        $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(30);

        if (empty($emp_values['id'])) {
            
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $row, $count)->setCellValue('B' . $row, $fullname . '*');
        } else {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $row, $count)->setCellValue('B' . $row, $fullname);
        }

        $noo = 1;


        $P = 0;
        $PR = 0;
        $PL = 0;
        $A = 0;
        $d_cnt = 1;
        $t_hol = 0;
        $L = 0;
        for ($j = 'C'; $j <= 'Z'; $j++) {
            $attn_date = $noo . '-' . $date;
            $day =  date("D", strtotime($attn_date));
            // pr($day);die;
            // if($day=="Sun"){
            //     pr($day);die;
            // }

            $status = null;
            $emp_atn = null;
            if ($day != 'Sun' && $day != 'Hol') {
                $emp_atn = $this->Comman->findEmp_Att_bydate($emp_values['id'], $attn_date);
                if (!empty($emp_atn)) {
                    $status = $emp_atn['status'];
                    if ($status == 'A') {
                        $A += 1;
                    } else if ($status == 'HF') {
                        $PR += 0.5;
                    } else if ($status == 'SL') {
                        $PL += 0.25;
                    }
                } else {
                    $count;
                    $objPHPExcel->setActiveSheetIndex(0)->getStyle($j . $row)->applyFromArray($paresent_color);
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($j . $row, 'P');
                }

                if ($status == 'A' || $status == 'HF' || $status == 'SL') {
                    $objPHPExcel->setActiveSheetIndex(0)->getStyle($j . $row)->applyFromArray($Half_day_color);
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($j . $row, $status);
                }
            } else {

                $objPHPExcel->setActiveSheetIndex(0)->getStyle($j . $row)->applyFromArray($hollyday_color);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($j . $row, 'H');
            }

            $noo++;

            $objPHPExcel->setActiveSheetIndex(0)->getStyle($A_col_num . $row)->applyFromArray($Half_day_color);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($A_col_num . $row, $A + $PR + $PL);

            if ($mon == date('m') && $year == date('Y')) {
                if ($noo > date('d')) {
                    break;
                }
            } else {
                if ($noo > $total_days) {
                    break;
                }
            }
        }
        $count++;
        $row++;
    }
}

$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web $colser (Excel2007)
$downloadDate = date('d-m-Y');
$filename = $monthName . '-' . $year . "-Attendance-Report" . ".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $filename);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
