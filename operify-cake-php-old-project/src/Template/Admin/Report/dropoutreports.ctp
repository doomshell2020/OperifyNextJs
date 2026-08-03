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
// $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
// $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(6);
// $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
// $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(12);
// $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
// $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(14);
// $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
// $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
// $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
// $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
// $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
// $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(10);
// $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(10);
// $objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setWidth(25);


$objPHPExcel->getActiveSheet()->getStyle(1)->getFont()->setBold(true);
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
$objPHPExcel->getActiveSheet()->getColumnDimension('w')->setAutoSize(true);
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
$objPHPExcel->getActiveSheet()->getColumnDimension('AI')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('AJ')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('AK')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('AL')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('AM')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('AN')->setAutoSize(true);



$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A1', 'S.No.(Book No.)')
    ->setCellValue('B1', 'Sr.No.')
    ->setCellValue('C1', 'Pupil Name')
    ->setCellValue('D1', 'Father Name')
    ->setCellValue('E1', 'Mother Name')
    ->setCellValue('F1', 'Fees Submitted By')
    ->setCellValue('G1', 'Mobile')
    ->setCellValue('H1', 'Gender')
    ->setCellValue('I1', 'Last Studied Class')
    ->setCellValue('J1', 'Class')
    ->setCellValue('K1', 'Section')
    ->setCellValue('L1', 'Category')
    ->setCellValue('M1', 'Cast')
    ->setCellValue('N1', 'Dropout Date')
    ->setCellValue('O1', 'Application Date')
    ->setCellValue('P1', 'Issue Date')
    ->setCellValue('Q1', 'Fees Paid Upto')
    ->setCellValue('R1', 'Status')
    ->setCellValue('S1', 'Nationality')
    ->setCellValue('T1', 'School/Board Annual Examination last taken result')
    ->setCellValue('U1', 'Total no of working days')
    ->setCellValue('V1', 'Total no of working days present')
    ->setCellValue('W1', 'Qualified for promotion to Higher Class')
    ->setCellValue('X1', 'Whether NCC Cadet/Boy Scout/Girl Guide')
    ->setCellValue('Y1', 'Games/Extra curricular activities took part')
    ->setCellValue('Z1', 'Any Fee Concession')
    ->setCellValue('AA1', 'Subject Studied')
    ->setCellValue('AB1', 'First Admission Date')
    ->setCellValue('AC1', 'Admission Class')
    ->setCellValue('AD1', 'Mention achievement level')
    ->setCellValue('AE1', 'General Conduct')
    ->setCellValue('AF1', 'Reason for Leaving')
    ->setCellValue('AG1', 'Any Other Remarks')
    ->setCellValue('AH1', 'Father Occupation')
    ->setCellValue('AI1', 'Mother Occupation')
    ->setCellValue('AJ1', 'Father Qualification')
    ->setCellValue('AK1', 'Mother Qualification')
    ->setCellValue('AL1', 'Father Mobile No.')
    ->setCellValue('AM1', 'Mother Mobile No.')
    ->setCellValue('AN1', 'Address');

$counter = 1;
if (isset($students) && !empty($students)) {
    foreach ($students as $i => $work) {

        $ii = $i + 2;

        $objPHPExcel->getActiveSheet()->setCellValue('A' . $ii, $work['bookno']);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $ii, $work['enroll']);
        $name = $work['fname'] . ' ';

        if (!empty($work['middlename'])) {
            $name .= $work['middlename'] . ' ';
        }
        $class = $this->Comman->findclass($work['laststudclass']);

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

        if (empty($work['date_application'])) {
            $work['date_application'] = '--';
        } else {
            $work['date_application'] = date('d-m-Y', strtotime($work['date_application']));
        }
        if (empty($work['date_issue'])) {
            $work['date_issue'] = '--';
        } else {
            $work['date_issue'] = date('d-m-Y', strtotime($work['date_issue']));
        }


        $name .= $work['lname'];
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $ii, $name);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $ii, $work['fathername']);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $ii, $work['sms_mobile']);
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $ii, $class['title']);
        $objPHPExcel->getActiveSheet()->setCellValue('G' . $ii, $work['class']['title']);
        $objPHPExcel->getActiveSheet()->setCellValue('H' . $ii, $work['section']['title']);
        $objPHPExcel->getActiveSheet()->setCellValue('I' . $ii, date('d-m-Y', strtotime($work['created'])));
        $objPHPExcel->getActiveSheet()->setCellValue('J' . $ii, $work['date_application']);
        $objPHPExcel->getActiveSheet()->setCellValue('K' . $ii, $work['date_issue']);


        if ($work['month']) {

            $objPHPExcel->getActiveSheet()->setCellValue('L' . $ii, $work['month']);
        } else if ($monthupto != "Mar 1970") {
            $objPHPExcel->getActiveSheet()->setCellValue('L' . $ii, $monthupto);
        } else if ($monthupto23 != "Mar 1970") {
            $objPHPExcel->getActiveSheet()->setCellValue('L' . $ii, $monthupto23);
        } else {
            $objPHPExcel->getActiveSheet()->setCellValue('L' . $ii, "N/A");
        }
        if ($work['status_tc'] == "N") {

            $objPHPExcel->getActiveSheet()->setCellValue('M' . $ii, 'L.W.T.C');
        } else {

            $objPHPExcel->getActiveSheet()->setCellValue('M' . $ii, 'T.C');
        }
    }
} else if (isset($studentsaftersearch) && !empty($studentsaftersearch)) {

    foreach ($studentsaftersearch as $i => $work) {
        $ii = $i + 2;

        $str = $work['tcsubject'];
        if (!empty($str)) {
            $data  =  (explode(",", $str));
            $find_subj_name = $this->Comman->findsubjname($data);
        }

        // $subjjj = implode(',', $sub_name);
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $ii, $work['bookno']);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $ii, $work['enroll']);
        $class = $this->Comman->findclass($work['laststudclass']);

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
        $work['class']['title'] = $work['classtitle'];
        $work['section']['title'] = $work['sectiontitle'];
        if (empty($work['date_application'])) {
            $work['date_application'] = '--';
        } else {
            $work['date_application'] = date('d-m-Y', strtotime($work['date_application']));
        }
        if (empty($work['date_issue'])) {
            $work['date_issue'] = '--';
        } else {
            $work['date_issue'] = date('d-m-Y', strtotime($work['date_issue']));
        }


        $name = $work['fname'] . ' ';

        if (!empty($work['middlename'])) {
            $name .= $work['middlename'] . ' ';
        }

        $name .= $work['lname'];
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $ii, $counter);
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $ii, $name);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $ii, $work['fathername']);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $ii, ucfirst($work['mothername']));
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $ii, ucfirst($work['fee_submittedby']));
        $objPHPExcel->getActiveSheet()->setCellValue('G' . $ii, $work['sms_mobile']);
        $objPHPExcel->getActiveSheet()->setCellValue('H' . $ii, $work['gender']);
        $objPHPExcel->getActiveSheet()->setCellValue('I' . $ii, $class['title']);
        $objPHPExcel->getActiveSheet()->setCellValue('J' . $ii, $work['class']['title']);
        $objPHPExcel->getActiveSheet()->setCellValue('K' . $ii, $work['section']['title']);
        $objPHPExcel->getActiveSheet()->setCellValue('L' . $ii, $work['category']);
        $objPHPExcel->getActiveSheet()->setCellValue('M' . $ii, $work['cast']);
        $objPHPExcel->getActiveSheet()->setCellValue('N' . $ii, date('d-m-Y', strtotime($work['dropcreated'])));
        $objPHPExcel->getActiveSheet()->setCellValue('O' . $ii, $work['date_application']);
        $objPHPExcel->getActiveSheet()->setCellValue('P' . $ii, $work['date_issue']);

        if ($work['month']) {

            $objPHPExcel->getActiveSheet()->setCellValue('Q' . $ii, $work['month']);
        } else if ($monthupto != "Mar 1970") {
            $objPHPExcel->getActiveSheet()->setCellValue('Q' . $ii, $monthupto);
        } else if ($monthupto23 != "Mar 1970" && $monthupto23 != "Jan 1970") {
            $objPHPExcel->getActiveSheet()->setCellValue('Q' . $ii, $monthupto23);
        } else {
            $objPHPExcel->getActiveSheet()->setCellValue('Q' . $ii, "N/A");
        }

        if ($work['status_tc'] == "N") {
            $objPHPExcel->getActiveSheet()->setCellValue('R' . $ii, 'L.W.T.C');
        } else {
            $objPHPExcel->getActiveSheet()->setCellValue('R' . $ii, 'T.C');
        }


        $objPHPExcel->getActiveSheet()->setCellValue('S' . $ii, $work['nationality']);
        $objPHPExcel->getActiveSheet()->setCellValue('T' . $ii, $work['school_lastresult']);
        $objPHPExcel->getActiveSheet()->setCellValue('U' . $ii, $work['workingdays']);
        $objPHPExcel->getActiveSheet()->setCellValue('V' . $ii, $work['presentdays']);
        $promoted_classr = $this->Comman->findclass($work['promoted_to_class_id']);
        $objPHPExcel->getActiveSheet()->setCellValue('W' . $ii, $work['qualified_to_promote'] . '/(' . $promoted_classr['title'] . ')');
        $objPHPExcel->getActiveSheet()->setCellValue('X' . $ii, $work['NCC_cadet']);
        $objPHPExcel->getActiveSheet()->setCellValue('Y' . $ii, $work['extra_curricular_activities']);
        $objPHPExcel->getActiveSheet()->setCellValue('Z' . $ii, $work['anyfeeconcession']);
        $objPHPExcel->getActiveSheet()->setCellValue('AA' . $ii, $find_subj_name);
        $objPHPExcel->getActiveSheet()->setCellValue('AB' . $ii, date('d-m-Y', strtotime($work['admissiondate'])));

        $firstadmissionclassss = $this->Comman->showadmissionclasstitle($work['admissionclass']);

        $objPHPExcel->getActiveSheet()->setCellValue('AC' . $ii, $firstadmissionclassss['title']);
        $objPHPExcel->getActiveSheet()->setCellValue('AD' . $ii, $work['achievement_level']);
        $objPHPExcel->getActiveSheet()->setCellValue('AE' . $ii, $work['general_conduct']);
        $objPHPExcel->getActiveSheet()->setCellValue('AF' . $ii, $work['leaving_reason']);
        $objPHPExcel->getActiveSheet()->setCellValue('AG' . $ii, $work['remarks']);
        $objPHPExcel->getActiveSheet()->setCellValue('AH' . $ii, $work['f_occupation']);
        $objPHPExcel->getActiveSheet()->setCellValue('AI' . $ii, $work['m_occupation']);
        $objPHPExcel->getActiveSheet()->setCellValue('AJ' . $ii, $work['f_qualification']);
        $objPHPExcel->getActiveSheet()->setCellValue('AK' . $ii, $work['m_qualification']);
        $objPHPExcel->getActiveSheet()->setCellValue('AL' . $ii, $work['f_phone']);
        $objPHPExcel->getActiveSheet()->setCellValue('AM' . $ii, $work['m_phone']);
        $objPHPExcel->getActiveSheet()->setCellValue('AN' . $ii, $work['address']);

        $counter++;
    }
}

// Rename sheet
//$objPHPExcel->getActiveSheet()->setTitle('Simple');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel2007)
$filename = "ExportDropout_Students.xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $filename);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
