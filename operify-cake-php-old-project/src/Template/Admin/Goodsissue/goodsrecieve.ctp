<?php 
//pr($puritems); 
class xtcpdf extends TCPDF
{

}

//$subject=$this->Comman->findexamsubjectsresult($students['id'],$students['section']['id'],$students['acedmicyear']);

$this->set('pdf', new TCPDF('L', 'mm', 'A4'));
$pdf = new TCPDF("P", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->AddPage();

$pdf->SetFont('', '', 10, '', 'false');


$html .= '
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Result</title><link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">';
$html.='</head>
<body>

<table>
<tr>
<td style="width:70%; text-align:left;">Sample Goods Received Note</td>
<td style="width:30%; text-align:left;"> GRN Number: </td>

</tr>
</table>

<br>
<br>
<br>

<table border="1" cellpadding="8">
<tr>
<td>
<h1 style="text-align:center;">Goods Received Note</h1>
</td>
</tr>
</table>

<br>
<br>
<br>

<table>
<tr>
<td>Supplier................................................... <span style="border-bottom: 1px dotted black;"></span></td>
<td>Date........................................................</td>
<td>Advice Note Number..............................</td>
</tr>

<br>

<tr>
<td>Order Number.........................................<span style="border-bottom: 1px dotted black;"></span></td>
<td>Delivery Location....................................</td>
<td>Cost-Centre............................................</td>
</tr>
</table>

<br>
<br>
<br>

<table border="1" cellpadding="8">
<tr>
<td width="6%"  style="text-align:center;"></td>
<td width="22%" style="text-align:center;">Goods</td>
<td width="10%" style="text-align:center;">Pack Size</td>
<td width="8%"  style="text-align:center;">Price</td>
<td width="10%" style="text-align:center;">Order Quantity</td>
<td width="12%" style="text-align:center;">Delivered Quantity</td>
<td width="32%" style="text-align:center;">Comments</td>
</tr>
<tr>
<td width="6%"  style="text-align:center;">1</td>
<td width="22%" style="text-align:center;"></td>
<td width="10%" style="text-align:center;"></td>
<td width="8%"  style="text-align:center;"></td>
<td width="10%" style="text-align:center;"></td>
<td width="12%" style="text-align:center;"></td>
<td width="32%" style="text-align:center;"></td>
</tr>
<tr>
<td width="6%"  style="text-align:center;">2</td>
<td width="22%" style="text-align:center;"></td>
<td width="10%" style="text-align:center;"></td>
<td width="8%"  style="text-align:center;"></td>
<td width="10%" style="text-align:center;"></td>
<td width="12%" style="text-align:center;"></td>
<td width="32%" style="text-align:center;"></td>
</tr>
<tr>
<td width="6%"  style="text-align:center;">3</td>
<td width="22%" style="text-align:center;"></td>
<td width="10%" style="text-align:center;"></td>
<td width="8%"  style="text-align:center;"></td>
<td width="10%" style="text-align:center;"></td>
<td width="12%" style="text-align:center;"></td>
<td width="32%" style="text-align:center;"></td>
</tr>
<tr>
<td width="6%"  style="text-align:center;">4</td>
<td width="22%" style="text-align:center;"></td>
<td width="10%" style="text-align:center;"></td>
<td width="8%"  style="text-align:center;"></td>
<td width="10%" style="text-align:center;"></td>
<td width="12%" style="text-align:center;"></td>
<td width="32%" style="text-align:center;"></td>
</tr>
<tr>
<td width="6%"  style="text-align:center;">5</td>
<td width="22%" style="text-align:center;"></td>
<td width="10%" style="text-align:center;"></td>
<td width="8%"  style="text-align:center;"></td>
<td width="10%" style="text-align:center;"></td>
<td width="12%" style="text-align:center;"></td>
<td width="32%" style="text-align:center;"></td>
</tr>
<tr>
<td width="6%"  style="text-align:center;">6</td>
<td width="22%" style="text-align:center;"></td>
<td width="10%" style="text-align:center;"></td>
<td width="8%"  style="text-align:center;"></td>
<td width="10%" style="text-align:center;"></td>
<td width="12%" style="text-align:center;"></td>
<td width="32%" style="text-align:center;"></td>
</tr>
<tr>
<td width="6%"  style="text-align:center;">7</td>
<td width="22%" style="text-align:center;"></td>
<td width="10%" style="text-align:center;"></td>
<td width="8%"  style="text-align:center;"></td>
<td width="10%" style="text-align:center;"></td>
<td width="12%" style="text-align:center;"></td>
<td width="32%" style="text-align:center;"></td>
</tr>
<tr>
<td width="6%"  style="text-align:center;">8</td>
<td width="22%" style="text-align:center;"></td>
<td width="10%" style="text-align:center;"></td>
<td width="8%"  style="text-align:center;"></td>
<td width="10%" style="text-align:center;"></td>
<td width="12%" style="text-align:center;"></td>
<td width="32%" style="text-align:center;"></td>
</tr>
<tr>
<td width="6%"  style="text-align:center;">9</td>
<td width="22%" style="text-align:center;"></td>
<td width="10%" style="text-align:center;"></td>
<td width="8%"  style="text-align:center;"></td>
<td width="10%" style="text-align:center;"></td>
<td width="12%" style="text-align:center;"></td>
<td width="32%" style="text-align:center;"></td>
</tr>
<tr>
<td width="6%"  style="text-align:center;">10</td>
<td width="22%" style="text-align:center;"></td>
<td width="10%" style="text-align:center;"></td>
<td width="8%"  style="text-align:center;"></td>
<td width="10%" style="text-align:center;"></td>
<td width="12%" style="text-align:center;"></td>
<td width="32%" style="text-align:center;"></td>
</tr>
</table>

<br>
<br>
<br>
<br>

<table>
<tr>
<td>Received by............................................................................</td>
<td>Checked by.............................................................................</td>
</tr>
</table>

<br>
<br>
<br>

<table>
<tr>
<td>1. Accounts/Finance Dept. Copy</td>
</tr>
<tr>
<td>2. Supplier Copy</td>
</tr>
<tr>
<td>3. Stores/Goods Inwards Copy</td>
</tr>


</table>

</body></html>';

$pdf->WriteHTML($html, true, false, true, false, '');
ob_end_clean();
$date = date('d-m-Y');
echo $pdf->Output('Daily-Summary-' . $bordd . '-' . $date . '.pdf');
exit;
?>