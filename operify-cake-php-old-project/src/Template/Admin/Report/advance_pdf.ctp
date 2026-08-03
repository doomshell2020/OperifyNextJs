<?php
class xtcpdf extends TCPDF
{

}

$pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false, true);
$pdf->SetPrintHeader(false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->AddPage();
$pdf->SetFont('', '', 9, '', 'false');

$html = '
	<h2 style="text-align:center; margin-bottom:0; border:none;">Advance Salary  Report</h2>';

$html .= '<table border="1" align="center" width="100%" cellpadding="2" cellspacing="0" >
<tr>
<th style="width:5%;"><b> S.No.</b></th>
<th style="width:8%;"><b>Employee ID</b></th>
<th style="width:20%;"><b>Employee Name</b></th>
<th style="width:12%;"><b>Designation</b></th>
<th style="width:10%;"><b>Date</b></th>
<th style="width:10%;"><b>Advance amount</b></th>
<th style="width:10%;"><b>Amount Returned</b></th>
<th style="width:10%;"><b>Balance </b></th>

</tr>';

$cnt = 1;
$total_adv = 0;
$total_dep = 0;
foreach ($pdfreport as $key => $service) {
    $emp_det = $this->Comman->findemployeename($service['employee_id']);
    //pr($emp_det);die;
    $adv_ret = $this->Comman->findadvancereturn($service['id']);
    //pr($adv_ret);die;
    $desg = $this->Comman->finddesignation($emp_det['designation_id']);

    $html .= '<tr>
	<td>' . $cnt++ . '</td>';
    $html .= '<td style="text-align:right;">' . $service['employee_id'] . '</td>
	<td style="text-align:center;">' . strtoupper($emp_det['fname']) . "\x20" . strtoupper($emp_det['middlename']) . strtoupper($emp_det['lname']) . '</td>
	<td style="text-align:center;">' . $desg[0]['name'] . '</td>';

    $html .= '<td style="text-align:right;">' . date('d-m-Y', strtotime($service['paydate'])) . '</td>';

    $html .= '<td style="text-align:right;">' . $service['amount'] . '</td>';

    $html .= '<td style="text-align:right;"></td>';

    $html .= '<td style="text-align:right;"></td></tr>';
    $total_adv += $service['amount'];
    $ret_det = $this->Comman->findadvancereturndet($service['id']);
    $dep_sum = 0;
    if (!empty($ret_det)) {
        foreach ($ret_det as $ret_val) {
            $dep_sum += $ret_val['dep_amount'];

            $html .= '<tr>
          <td></td>';
            $html .= '<td style="text-align:right;">' . $service['employee_id'] . '</td>
          <td style="text-align:center;">' . strtoupper($emp_det['fname']) . "\x20" . strtoupper($emp_det['middlename']) . strtoupper($emp_det['lname']) . '</td>
          <td style="text-align:center;">' . $desg[0]['name'] . '</td>';

            $html .= '<td style="text-align:right;">' . date('d-m-Y', strtotime($service['paydate'])) . '</td>';

            $html .= '<td style="text-align:right;"></td>';

            $html .= '<td style="text-align:right;">' . $ret_val['dep_amount'] . '</td>';

            $html .= '<td style="text-align:right;"></td></tr>';
        }
    }
    $total_dep += $dep_sum;
    $html .= '<tr>
          <td></td>';
    $html .= '<td style="text-align:right;"></td>
          <td style="text-align:center;"></td>
          <td style="text-align:center;"></td>';

    $html .= '<td style="text-align:right;"></td>';

    $html .= '<td style="text-align:right;"><b>' . $service['amount'] . '</b></td>';

    $html .= '<td style="text-align:right;"><b>' . $dep_sum . '</b></td>';

    $html .= '<td style="text-align:right;"><b>' . ($service['amount'] - $dep_sum) . '</b></td></tr>';

}
$html .= '<tr>
    <td></td>';
$html .= '<td style="text-align:center;" colspan="4"><b>Total</b></td>';

$html .= '<td style="text-align:right;"><b>' . $total_adv . '</b></td>';

$html .= '<td style="text-align:right;"><b>' . $total_dep . '</b></td>';

$html .= '<td style="text-align:right;"><b>' . ($total_adv - $total_dep) . '</b></td></tr></table>';
//echo $html;die;

$pdf->WriteHTML($html);
ob_end_clean();

echo $pdf->Output('ADVANCE SALARY');

exit;