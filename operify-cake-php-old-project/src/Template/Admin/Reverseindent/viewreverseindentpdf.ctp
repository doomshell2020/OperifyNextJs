<?php 
class xtcpdf extends TCPDF {
}

   $this->set('pdf', new TCPDF('P','mm','A4'));
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);

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

$logo = WWW_ROOT . "/images/" . $site_details['small_logo'];
$address  =  $site_details['address1'];
$email  =  $site_details['email'];
$mobile  =  $site_details['phone'];
$website  =  $site_details['website'];
$gst_no  =  $site_details['gst_no'];
$pan_no  =  $site_details['pan_number'];
$school_name  =  $sitesetting['first_name'];

$j=1;
$itemname = $this->comman->getitemname($reverseindentid['finishedproduct_id']);
$contractname = $this->comman->findcontractname($reverseindentid['contract_id']);
$machineName = $this->comman->getMachineName($reverseindentid['machine_id']);
if($reverseindentid['updated'] != ''){
   $updatedate = date("d-m-Y", strtotime($reverseindentid['updated']));
}

$html .='
<table border="1px">
  <tr>
    <td>
      <table width="100%" style="padding: 1px 1px 0px 0px;" align="left">
               <tbody>
                  <tr>
                     <td style="text-align:left" width="50%">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="'.$logo.'" alt="" border="0" style="display:block;" height="62px;"><br>
                        <span style="display:block; color:#000; font-size:10px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>'.$school_name.'</b></span>
                     </td>
                     <td style="text-align:left;" width="50%" align="right">
                     '.$address.'<br>
                        <b>Phone</b>
                        :'.$mobile.'<br>&nbsp;&nbsp;&nbsp;&nbsp;<b>Email</b>
                        : <u>
                        '.$email.'</u><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Website</b> :&nbsp;'.$website.'
                     </td>
                  </tr>
               </tbody>
            </table>
      <h3 style="text-align:center;font-size:10px; border-top:1px solid #000; border-bottom:1px solid #000;">Reverse Indent Details</h3>
      <table cellspacing="0" cellpadding="3" border="0px" style="font-size:8px;">
        <thead>
          <tr>
          <td><b>Reverse Id :-</b>
           '. $reverseindentid['reverse_id'] .'
          </td>
          <td><b>Contract name :-</b>
          '.  $contractname['title'] . '(' . $contractname['workorder'] . ')
          </td>
        </tr>
        <tr>
        <td><b>Product :-</b>
           '.  $itemname['item_name'].'
          </td>
          <td><b>Machine Name :-</b>
          '.  $machineName['machine_name'].'
         </td>
        </tr>
        <tr>
        <td><b>Received By :-</b>
           '. $reverseindentid['received_name'] .'
          </td>
        <td><b>Received Date :-</b>
           '. date("d-m-Y", strtotime($reverseindentid['issue_date'])) .'
          </td>
          </tr>
          <tr>
          <td><b>Last Updated Date :-</b>
           '. $updatedate .'
          </td>
        </tr>
        </thead>
        </table>
    </td>
  </tr>
</table>';
  

  $html .='
<h6 style="text-align:center;font-size:10px;">Raw Material</h6>
<table cellspacing="0" cellpadding="3" border="1px" style="font-size:8px;">
<thead>
<tr>
<th width="08%" ><strong>S.No.</strong></th>
<th width="62%" ><strong>Item</strong></th>
<th width="20%" ><strong>Received Qty</strong></th>
<th width="10%" ><strong>UOM</strong></th>
</tr>
</thead>

    <tbody>';
    foreach ($reverseindentdetails as $value) {
      $itemname1 = $this->comman->getitemcatcom($value['item_id']);
  ;
          $html .='
          <tr>
            <th width="08%" >'.$j.'.</th>
            <td width="62%" >'.ucfirst($itemname1['item_name']).'</td>
            <td width="20%" style ="text-align:right;">'.sprintf('%.2f',$value['quantity']).'</td>
            <td width="10%" >'.$itemname1['measurementunit']['unit_name'].'</td>
            </tr>';
      $j++;
    }
    $html .='
      </tbody>
      </table>
      <br>
      <br>
      <br>
      <br>
      
      
            <table cellspacing="0" cellpadding="3" border="0px" style="font-size:8px;">
        <thead>
        <tr>
        <th width="33%" ><strong>INDENTER</strong></th>
        <td width="33%" style ="text-align:center;"><strong>ISSUED BY</strong></td>
        <td width="34%" style ="text-align:right;"><strong>RECEIVED BY</strong></td>
        </tr>
          </thead>
            </table>
      ';

// echo $html;die;

$date = date('d-m-y');
$pdf->writeHTMLCell(0, 0, '', '', utf8_encode($html), 0, 1, 0, true, '', true);
//$pdf->WriteHTML($html, true, false, true, false, '');
ob_end_clean();
echo $pdf->Output('Reverse_Indent_'.$reverseindentid['reverse_id'].'_'.$date.'.pdf');
exit;
?>
 