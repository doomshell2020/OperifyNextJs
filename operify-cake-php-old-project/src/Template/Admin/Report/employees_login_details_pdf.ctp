<?php 
class xtcpdf extends TCPDF {
}
 //$subject=$this->Comman->findexamsubjectsresult($students['id'],$students['section']['id'],$students['acedmicyear']);

   $this->set('pdf', new TCPDF('L','mm','A4'));
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);

// set document information

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->AddPage();
//$pdf->setHeaderMargin(0);

// set margins
//$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
 $pdf->SetAutoPageBreak(TRUE, 15);
//$pdf->SetMargins(5, 0, 5, true);

$pdf->SetFont('', '', 8, '', 'true');
TCPDF_FONTS::addTTFfont('../Devanagari/Devanagari.ttf', 'TrueTypeUnicode', "", 32);

$i=1;




$html .='
  <h3 style="text-align:center;">Employees Login Details</h3>
<table cellspacing="0" cellpadding="5" style="font-size:9px;">
  <thead>
    <tr>
      <th width="10%" style="border:1px solid #333; border-right:none;"><strong>S.No</strong></th>
      <th width="20%" style="border:1px solid #333; border-right:none;"><strong>Name</strong></th>
      <th width="20%" style="border:1px solid #333; border-right:none;"><strong>Mobile</strong></th>
      <th width="20%" style="border:1px solid #333; border-right:none;"><strong>Password</strong></th>
      <th width="30%" style="border:1px solid #333;"><strong>Email</strong></th>
    </tr>
    <tr >
      <th></th>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
    </tr>
  </thead>
  <tbody>';
  foreach($employees as $value){
    //pr($value); die;
  $html .='
  <tbody>
    <tr>
      <th width="10%" style="border:1px solid #333; border-right:none;">'.$i.'</th>
      <td width="20%" style="border:1px solid #333; border-right:none;">'.ucfirst(strtolower($value['fname'].$value['middlename'].$value['lname'])).'</td>
      <td width="20%" style="border:1px solid #333; border-right:none;">'.$value['mobile'].'</td>
      <td width="20%" style="border:1px solid #333; border-right:none;">'.$value['user']['confirm_pass'].'</td>
      <td width="30%" style="border:1px solid #333;">'.$value['email'].'</td>
    </tr>
    <tr >
      <th></th>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
    </tr>
    </tbody>
    ';
    $i++;
   }

$html .='
  </tbody>
</table>';




$pdf->writeHTMLCell(0, 0, '', '', utf8_encode($html), 0, 1, 0, true, '', true);
//$pdf->WriteHTML($html, true, false, true, false, '');
ob_end_clean();
echo $pdf->Output('Employee Login Details.pdf');
exit;
?>