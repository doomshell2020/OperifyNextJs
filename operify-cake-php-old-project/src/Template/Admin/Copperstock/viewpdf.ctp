<?php
class xtcpdf extends TCPDF
{
}

$pdf = new TCPDF('L', 'mm', 'A4');
// set document information

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->AddPage();
//$pdf->setHeaderMargin(0);

// set margins
//$//pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetAutoPageBreak(TRUE, 20);
//$pdf->SetMargins(0, 25, 0, true);

$pdf->SetFont('', '', 8, '', 'true');
TCPDF_FONTS::addTTFfont('../Devanagari/Devanagari.ttf', 'TrueTypeUnicode', "", 32);

$i = 1;
$date = date("d-M-Y");

if ($contra != '1970-01-01' && $contra != '') {
  $datefrom = date('d-M-Y', strtotime($contra));
} else {
  $datefrom = $date;
}


$html .= '
  <h3 style="text-align:center;font-size:14px;">Copper Stock Details</h3>
  <p style="font-size:10px;">Date-' . $datefrom . '</p>
<table cellspacing="0" cellpadding="7" border="1px" style="font-size:8px;" >
<thead>
<tr>
   <th style="font-size:10px;" width="06%" ><strong>S No.</strong></th>
   <th style="font-size:10px;" width="34%" ><strong>Product Name</strong></th>
   <th style="font-size:10px;" width="20%" ><strong>Type</strong></th>
   <th style="font-size:10px;" width="20%" ><strong>TPPL</strong></th>
   <th style="font-size:10px;" width="20%" ><strong>KCPL</strong></th>
</tr>
</thead>
<tbody>';
// if(isset($data)){
foreach ($data as $value) {

  $html .= '
          <tr>
            <th width="06%">' . $i . '.</th>
            <td width="34%">' . $value['additem']['item_name'] . '</td>
            <td width="20%">' . $value['type'] . '</td>
            <td width="20%">' . $value['tppl'] . '</td>
            <td width="20%">' . $value['kcpl'] . '</td>
            </tr>';
  $i++;
}

$html .= '
      </tbody>
      </table>';


$pdf->writeHTMLCell(0, 0, '', '', utf8_encode($html), 0, 1, 0, true, '', true);
//$pdf->WriteHTML($html, true, false, true, false, '');
ob_end_clean();
echo $pdf->Output('Copperstock' . $date . '.pdf');
exit;
