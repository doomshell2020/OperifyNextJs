<?php 
class xtcpdf extends TCPDF {
}

   $this->set('pdf', new TCPDF('L','mm','A4'));
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);

// set document information
$date = date('d-m-Y');
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




  

  $html .='
  <h3 style="text-align:center;font-size:19px;">Products Name</h3>
<table cellspacing="0" cellpadding="4" border="1px" style="font-size:8px;" width = "100%">
  <thead>
    <tr>
      <th width = "10%"><strong>S.No.</strong></th>
      <th width = "70%"><strong>Product Name</strong></th>
      <th width = "20%"><strong>Product Type</strong></th>
      
    </tr>
  </thead>

    <tbody>';
    foreach($users as $value){
          $html .='
          <tr>
            <th width = "10%">'.$i.'</th>
            <td width = "70%">'.$value['item_name'].'</td>
            <td width = "20%">'.$value['itemtype'].'</td>
            </tr>';
      $i++;
    }
    $html .='
      </tbody>
      </table>';


$pdf->writeHTMLCell(0, 0, '', '', utf8_encode($html), 0, 1, 0, true, '', true);
//$pdf->WriteHTML($html, true, false, true, false, '');
ob_end_clean();
echo $pdf->Output('Item-Details_'.$date.'.pdf');
exit;
?>