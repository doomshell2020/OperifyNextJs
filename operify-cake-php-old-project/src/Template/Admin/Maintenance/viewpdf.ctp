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
$date =date("d-m-y");
 

if($contra['assigned']!=''){
  $assigned =$contra['assigned'];
}
if($contra['mstatus']!=''){
  $mstatus =$contra['mstatus'];
}
if($contra['datefrom']!='1970-01-01' && $contra['datefrom']!=''){
  $datefrom =date('d-m-y',strtotime($contra['datefrom']));
}
if($contra['dateto2']!='1970-01-01' && $contra['dateto2']!=''){
  $dateto2 =date('d-m-y',strtotime($contra['dateto2']));
}

// pr($dateto2);die;


  $html .='
  <h3 style="text-align:center;font-size:14px;">Maintenance Details</h3>

  <span style="display: inline-flex;font-size:10px;">
  <span> Assigned By- '.ucfirst($assigned).'</span>
  <span> Maintenance Status-'.ucfirst($mstatus).'</span>
  <span> Date From-'.$datefrom.'</span>
  <span> Date To-'.$dateto2.'</span> 
  </span> 
  <p style="text-align:right;font-size:10px;">Print Date-'.$date.'</p>

<table cellspacing="0" cellpadding="7" border="1px" style="font-size:8px;display:block;">
  <thead>
    <tr>
      <th style="font-size:8px;" width="29px"><strong>No.</strong></th>
      <th style="font-size:8px;" width="55px"><strong>Date</strong></th>
      <th style="font-size:8px;" width="120px"><strong>Machine Name</strong></th>
      <th style="font-size:8px;" width="74px"><strong>Type Of Breakdown</strong></th>
      <th style="font-size:8px;" width="33px"><strong>Time(Hrs)</strong></th>
      <th style="font-size:8px;" width="76px"><strong>Assigned To</strong></th>
      <th style="font-size:8px;" width="76px"><strong>Shift Incharge</strong></th>
      <th style="font-size:8px;" width="76px"><strong>Maintenance Incharge</strong></th>
      <th style="font-size:8px;" width="76px"><strong>Production Head</strong></th>
      <th style="font-size:8px;" width="49px"><strong>Status</strong></th>
      <th style="font-size:8px;" width="140px"><strong>Remark</strong></th>
    </tr>
  </thead>

    <tbody>';
    foreach($data as $value){
          $html .='
          <tr>
            <td width="29px">'.$i.'</td>
            <td width="55px">'.date("d-m-Y", strtotime($value['datefrom'])).'</td>
            <td width="120px">'.ucfirst($value['machinemaster']['machine_name']).'</td>
            <td width="74px">'.ucfirst(strtolower($value['breakdown_type'])).'</td>
            <td width="33px">'.$value['total_time'].'</td>
            <td width="76px">'.ucfirst(strtolower($value['assigned_to'])).'</td>
            <td width="76px">'.ucfirst(strtolower($value['shift_incharge'])).'</td>
            <td width="76px">'.ucfirst(strtolower($value['maintenance_incharge'])).'</td>
            <td width="76px">'.ucfirst(strtolower($value['production_head'])).'</td>
            <td width="49px">'.ucfirst($value['maintenance_status']).'</td>
            <td width="140px">'.ucfirst(strtolower($value['remark'])).'</td>
            </tr>';
      $i++;
    }
    $html .='
      </tbody>
      </table>';


$pdf->writeHTMLCell(0, 0, '', '', utf8_encode($html), 0, 1, 0, true, '', true);
//$pdf->WriteHTML($html, true, false, true, false, '');
ob_end_clean();
echo $pdf->Output('MaintenanceDetails'.$date .'.pdf');
exit;
?>