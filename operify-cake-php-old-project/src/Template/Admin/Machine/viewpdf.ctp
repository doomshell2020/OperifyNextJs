<?php
class xtcpdf extends TCPDF
{
}

$pdf = new TCPDF('P', 'mm', 'A4');

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->AddPage();


$pdf->SetAutoPageBreak(TRUE, 20);

$pdf->SetFont('', '', 8, '', 'true');
TCPDF_FONTS::addTTFfont('../Devanagari/Devanagari.ttf', 'TrueTypeUnicode', "", 32);

$i = 1;
$date = date("d-m-Y");



  $html .= '
  <h3 style="text-align:center;font-size:14px;">Machine Name</h3>

  <p style="text-align:right;font-size:10px;"><strong> Print Date-</strong>' . $date . '</p>

<table cellspacing="0" cellpadding="5" border="1px" style="font-size:8px;display:block;">
  <thead>
    <tr>
      <th width="15%"  style="font-size:8px;"><strong>S.No.</strong></th>
      <th width="60%" style="font-size:8px;"><strong>Machine Name</strong></th>
      <th width="25%" style="font-size:8px;"><strong>Date</strong></th>
      
    </tr>
  </thead>

    <tbody>';
foreach ($machine_data as $value) {
  $html .= '
          <tr>
            <td width="15%" >' . $i . '.</td>
            <td width="60%" >' . $value['machine_name']. '</td>
            <td width="25%" >' . date("d-m-Y", strtotime($value['created'])) . '</td>
            </tr>';
  $i++;
}
$html .= '
      </tbody>
      </table>';



// echo $html;die;

$pdf->writeHTMLCell(0, 0, '', '', utf8_encode($html), 0, 1, 0, true, '', true);
ob_end_clean();
echo $pdf->Output('Machine' . $date . '.pdf');
exit;
?>