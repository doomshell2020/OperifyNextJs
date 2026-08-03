<?php 
class xtcpdf extends TCPDF {
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

$i=1;
// pr($data);die;
$date =date("d-m-y");


if($contra['datefrom']!='1970-01-01' && $contra['datefrom']!=''){
  $datefrom =date('d-m-y',strtotime($contra['datefrom']));
}
if($contra['dateto2']!='1970-01-01' && $contra['dateto2']!=''){
  $dateto2 =date('d-m-y',strtotime($contra['dateto2']));
}



  

  $html .='
  <h3 style="text-align:center;font-size:14px;">Dispatch Details</h3>
  <span style="display: inline-flex;font-size:10px;">
  <span> Date From-'.$datefrom.'</span>
  <span> Date To-'.$dateto2.'</span> 
  </span> 
  <p style="text-align:right;font-size:10px;">Print Date-'.$date.'</p>
  
  <table cellspacing="0" cellpadding="7" border="1px" style="font-size:8px;">
  <thead>
    <tr>
      <th style="font-size:8px;" width="35px"><strong>S.No.</strong></th>
      <th style="font-size:8px;" width="60px"><strong>Date</strong></th>
      <th style="font-size:8px;" width="200px"><strong>Transporter Name</strong></th>
      <th style="font-size:8px;" width="80px"><strong>To</strong></th>
      <th style="font-size:8px;" width="80px"><strong>From</strong></th>
      <th style="font-size:8px;" width="80px"><strong>Vehicle No.</strong></th>
      <th style="font-size:8px;" width="80px"><strong>GR No.</strong></th>
      <th style="font-size:8px;" width="80px"><strong>Weight</strong></th>
      <th style="font-size:8px;" width="80px"><strong>Freight</strong></th>
    </tr>
  </thead>

    <tbody>';
    foreach($data as $value){
          $html .='
          <tr>
            <td width="35px">'.$i.'</td>
            <td width="60px">'.date("d-m-Y", strtotime($value['datefrom'])).'</td>
            <td width="200px">'.$value['vendor']['name'].'</td>
            <td width="80px">'.ucfirst(strtolower($value['transport_to'])).'</td>
            <td width="80px">'.ucfirst(strtolower($value['transport_from'])).'</td>
            <td width="80px">'.strtoupper($value['vehicle_no']).'</td>
            <td style="text-align:right" width="80px">'.$value['gr_no'].'</td>
            <td style="text-align:right" width="80px">'.$value['weight'].'</td>
            <td style="text-align:right" width="80px">'.$value['freight'].'</td>
            </tr>';
      $i++;
    }
    $html .='
      </tbody>
      </table>';


$pdf->writeHTMLCell(0, 0, '', '', utf8_encode($html), 0, 1, 0, true, '', true);
//$pdf->WriteHTML($html, true, false, true, false, '');
ob_end_clean();
echo $pdf->Output('Dispatchdetails'.$date.'.pdf');
exit;
?>