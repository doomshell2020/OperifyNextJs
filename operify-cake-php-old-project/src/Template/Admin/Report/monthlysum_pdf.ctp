<?php
class xtcpdf extends TCPDF
{

}
setlocale(LC_MONETARY, 'en_IN');
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false, true);
$pdf->SetPrintHeader(false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->AddPage();
$pdf->SetFont('', '', 9, '', 'false');
$keys = array_keys($total);

if (date('m-Y', strtotime($keys[0])) == date('m-Y', strtotime(end($keys)))) {
    $to = date('t-m-Y', strtotime($keys[0]));
}
if (date('m-Y') == date('m-Y', strtotime(end($keys)))) {
    $to = date('d-m-Y');
}

$temp = str_replace(array('{logo}', '{sitetitle}', '{subtitle1}', '{subtitle2}', '{address1}', '{address2}', '{phone}', '{fax}', '{email}', '{website}'), array($sitesetting['logo'], $sitesetting['sitetitle'], $sitesetting['subtitle1'], $sitesetting['subtitle2'], $sitesetting['address1'], $sitesetting['address2'], $sitesetting['phone'], $sitesetting['fax'], $sitesetting['email'], $sitesetting['website']), $det['template']);
$html .= '
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Result</title><link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">';
$html .= '</head>
<body>';
$html .= $temp;
$html .= '<hr>
<br>
	<h2 style="text-align:center; margin-bottom:0; border:none;">Monthly Summary Collection Report(' . date('d-m-Y', strtotime($keys[0])) . ' to ' . date('d-m-Y', strtotime($to)) . ')</h2>';

$html .= '<table  border="1" align="center" width="100%" cellpadding="2" cellspacing="0" >
<tr>
<th style="width:40%;"><b>Month</b></th>
<th style="width:20%;"><b>CBSE</b></th>
<th style="width:20%;"><b>INT</b></th>
<th style="width:20%;"><b>TOTAL</b></th>
</tr>';
foreach ($total as $key => $value) {
    $date = date('M-Y', strtotime($key));
    $cbse = $value['cbse'];
    $int = $value['int'];
    $total = $value['cbse'] + $value['int'];
    $cbse_total += $value['cbse'];
    $int_total += $value['int'];
    $html .= '<tr>
  <td style="text-align:center;">' . $date . '</td>
  <td style="text-align:center;">' . $cbse . '</td>
  <td style="text-align:center;">' . $int . '</td>
  <td style="text-align:center;">' . money_format('%!i', $total) . '</td>
</tr>';
    $net_total += ($value['cbse'] + $value['int']);
}
$html .= '<tr>
<td style="color:green">Net Total</td>
<td style="color:green">' . money_format('%!i', $cbse_total) . '</td>
<td style="color:green">' . money_format('%!i', $int_total) . '</td>
<td style="color:green">' . money_format('%!i', $net_total) . '</td>
</tr></table>';
// echo $html;die;

$pdf->WriteHTML($html);
ob_end_clean();

echo $pdf->Output('Monthly Summary Collection Report(' . date('d-m-Y', strtotime($keys[0])) . ' to ' . date('d-m-Y', strtotime($to)) . ').pdf');

exit;
