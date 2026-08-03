<?php
// pr($puritems); 
class xtcpdf extends TCPDF
{

}


$this->set('pdf', new TCPDF('L', 'mm', 'A4'));
$pdf = new TCPDF("P", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(true);
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->AddPage();
$pdf->SetFont('', '', 10, '', 'false');
$pdf->SetMargins(10, 10, 10, 0);

$vendorshipfrom = $this->Comman->vendorgst($users['vendor_id']);
$podate = date('d-m-Y', strtotime($users['added_time']));

$delivery_date = date('d-m-Y', strtotime($users['delivery_date']));
$supliername = $sup['name'];
if ($users['is_revised'] != 0) {
  $beforesavepo = $this->Comman->getbeforerevisedpo($users['purchaseorder_id']);
  $amedmentdate = $podate;
  $podate = date('d-m-Y', strtotime($beforesavepo['added_time']));
}

$logo = WWW_ROOT . "/images/" . $site_details['small_logo'];
$address = $site_details['address1'];
$email = $site_details['email'];
$mobile = $site_details['phone'];
$website = $site_details['website'];
$gst_no = $site_details['gst_no'];
$pan_no = $site_details['pan_number'];
$school_name = $sitesetting['first_name'];
$getDeliverydates = $this->Comman->getDeliverydates($users['id']);

$html .= '
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Purchase Order</title><link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">';
$html .= '</head>
<body style="border: 1px solid #000;">
<div style="border: 1px solid #000; ">

<!----------------------------------------------------------------------------------------------------------------------------header start  -->

<table width="100%">
<tr>
<td>
<table width="100%" style="padding: 1px 1px 0px 0px;" align="left">
<tr>
<td style="text-align:left" width="50%" >
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="' . $logo . '" alt="" border="0" style="display:block;" height="62px;"><br>
<span style="display:block; color:#000; font-size:10px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>' . $school_name . '</b></span>
</td>

<td style="text-align:left;" width="50%" align="right">
' . $address . '<br>
<b>Phone</b>
:' . $mobile . '<br>&nbsp;&nbsp;&nbsp;&nbsp;<b>Email</b>
: <u>
' . $email . '</u><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Website</b> :&nbsp;' . $website . '
</td>
</tr>
</table><br><hr>


<table width="100%">
<tr>
<td width="100%" style="height:15px; line-height:18px; color:#000; text-align:center; border-top:1px solid #000; border-bottom:1px solid #000; font-size:14px; font-weight;bold;">Delivery Schedule</td>
</tr>
</table>
<table width="100%">
<tr>
<td width="50%" style="border-right:1px solid #000;">

<table width="100%">

<tr>
<td width="2%" style="height:12px; line-height:12px;"></td>
<td width="25%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">TO</td>
<td width="5%" style="text-align:center; color:#000; font-size:8px; height:12px; line-height:12px;">:</td>
<td width="66%" style=" text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">
<strong style="font-weight:bold; font-size:8px; text-align:left;">' . $supliername . '</strong><br>
' . nl2br($sup['address']) . '
</td>
<td width="2%" style="height:12px; line-height:12px;"></td>
</tr>


<tr>
<td width="2%" style="height:12px; line-height:12px;"></td>
<td width="25%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">GST No.</td>
<td width="5%" style="text-align:center; color:#000; font-size:8px; height:12px; line-height:12px;">:</td>
<td width="66%" style=" text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">
' . $vendorshipfrom['gst_number'] . '
 
</td>
<td width="2%" style="height:12px; line-height:12px;"></td>
</tr>


<tr>
<td width="2%" style="height:12px; line-height:12px;"></td>
<td width="25%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">State</td>
<td width="5%" style="text-align:center; color:#000; font-size:8px; height:12px; line-height:12px;">:</td>
<td width="66%" style=" text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">
' . $sup['state']['name'] . '
 
</td>
<td width="2%" style="height:12px; line-height:12px;"></td>
</tr>


<tr>
<td width="2%" style="height:12px; line-height:12px;"></td>
<td width="25%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">Phone No. </td>
<td width="5%" style="text-align:center; color:#000; font-size:8px; height:12px; line-height:12px;">:</td>
<td width="66%" style=" text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">
' . $sup['contact_no'] . '
 
</td>
<td width="2%" style="height:12px; line-height:12px;"></td>
</tr>

<tr>
<td width="2%" style="height:12px; line-height:12px;"></td>
<td width="25%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">Email</td>
<td width="5%" style="text-align:center; color:#000; font-size:8px; height:12px; line-height:12px;">:</td>
<td width="66%" style=" text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">
' . $sup['email'] . '
 
</td>
<td width="2%" style="height:12px; line-height:12px;"></td>
</tr>
</table>
</td>




<td width="50%">

<table width="100%">

<tr>
<td width="2%" style="height:12px; line-height:12px;"></td>
<td width="30%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">Purchase Order No. </td>
<td width="5%" style="text-align:center; color:#000; font-size:8px; height:12px; line-height:12px;">:</td>
<td width="61%" style=" text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">
' . $users['purchaseorder_id'] . ' 
</td>
<td width="2%" style="height:12px; line-height:12px;"></td>
</tr>



<tr>
<td width="2%" style="height:12px; line-height:12px;"></td>
<td width="30%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">Purchase Order Date</td>
<td width="5%" style="text-align:center; color:#000; font-size:8px; height:12px; line-height:12px;">:</td>
<td width="61%" style=" text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">
' . $podate . '
 
</td>
<td width="2%" style="height:12px; line-height:12px;"></td>
</tr>


<tr>
<td width="2%" style="height:12px; line-height:12px;"></td>
<td width="30%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">Delivery Date</td>
<td width="5%" style="text-align:center; color:#000; font-size:8px; height:12px; line-height:12px;">:</td>
<td width="61%" style=" text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">
' . $delivery_date . '
 
</td>
<td width="2%" style="height:12px; line-height:12px;"></td>
</tr>



<tr>
<td width="2%" style="height:12px; line-height:12px;"></td>
<td width="30%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">Amendment No </td>
<td width="5%" style="text-align:center; color:#000; font-size:8px; height:12px; line-height:12px;">:</td>
<td width="61%" style=" text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">';
if ($users['is_revised'] != 0) {
  $html .= $users['is_revised'] . '&nbsp;(<b>Date : </b>' . $amedmentdate . ' )';
} else {
  $html .= '---';

}
$html .= '</td>
<td width="2%" style="height:12px; line-height:12px;"></td>
</tr>
</table>
</td>

</tr>
</table>

</td>
</tr>
</table>

<!----------------------------------------------------------------------------------------------------------------------------header end  -->


<!----------------------------------------------------------------------------------------------------------------------------Loop start  -->

<table width="100%">
<tr>

<td width = "27%" style="border-top:1px solid #000; border-right:1px solid #000; border-bottom:1px solid #000; color:#000; max-height:12px !important; line-height:9px;font-size:8px; font-weight:bold; text-align:left;"> &nbsp; ITEM</td>
<td width = "07%" style="border-top:1px solid #000; border-right:1px solid #000; border-bottom:1px solid #000; color:#000; max-height:12px !important; line-height:9px;font-size:8px; font-weight:bold; text-align:left;"> &nbsp; PO Qty</td>';

$width = 33 / count($getDeliverydates);
foreach ($getDeliverydates as $dates) {
  $html .= '
  <td width ="' . $width . '%" style="border:1px solid #000; color:#000; height:12px !important; line-height:9px; font-size:8px; text-align:center; font-weight:bold;"> DATE </td>
  <td width ="' . $width . '%" style="border:1px solid #000; color:#000; height:12px !important; line-height:9px; font-size:8px; text-align:center; font-weight:bold;"> QTY </td>';
}
$html .= '
</tr>';

foreach ($puritems as $value) {
  $itemname = $this->Comman->getitemname($value['item_id']);
  $uom = $this->Comman->getitemcatcom($value['item_id']);
  $html .= '<tr>
  <td  style=" border:1px solid #000; color:#000; height:12px !important; line-height:9px; font-size:8px;  text-align:left;"> &nbsp; ' . ucfirst(($itemname['item_name'])) . '</td>
  <td  style=" border:1px solid #000; color:#000; height:12px !important; line-height:9px; font-size:8px;  text-align:left;"> &nbsp; ' . $value['item_qty']. '</td>';

  $td = 0;
  foreach ($getDeliverydates as $dates) {
    $getitemqty = $this->Comman->DeliveritemQty($value['item_id'], $users['id'], date('Y-m-d', strtotime($dates['delivery_date'])));
    $delivery_date = date('d-m-Y', strtotime($dates['delivery_date']));
    $qty = $getitemqty['item_qty'] ? $getitemqty['item_qty'] : 0;
    

    if ($qty != 0) {
      ;
      $html .= '
      <td width = "' . $width . '%" style=" border:1px solid #000; color:#000; height:12px !important; line-height:9px; font-size:8px;  text-align:center;"> ' . $delivery_date . ' &nbsp; </td>
      <td width = "' . $width . '%" style=" border:1px solid #000; color:#000; height:12px !important; line-height:9px; font-size:8px; text-align:center;"> ' . $qty . '</td> ';
    } else {
      $td++;
    }
    
  }

  if ($td > 0) {
    for ($dk = 0; $dk < $td; $dk++) {
      ;
      $html .= '
    <td width = "' . $width . '%" style=" border:1px solid #000; color:#000; height:12px !important; line-height:9px; font-size:8px;  text-align:center;"></td>
    <td width = "' . $width . '%" style=" border:1px solid #000; color:#000; height:12px !important; line-height:9px; font-size:8px; text-align:center;"></td> ';
    }
  }
  $html .= '
</tr>';

}
// die;

$html .= '

</table>

<!----------------------------------------------------------------------------------------------------------------------------Terms and Conditions Start  -->';

// $pdf->SetAutoPageBreak(true);
$html .= 
'<table width="100%" cellpadding="3px" style="border-top:1px solid #000;">
<tr >

<td width="09%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;"> &nbsp;Remarks</td>

<td width="91%" style=" text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">
' . nl2br(ucfirst(strtolower($getDeliverydates[0]['delivery_note']))) . '
 
</td>
</tr>
</table>


<table width="100%">
<tr>
<td>
<table width="100%">
<tr>
<td width="25%" style="text-align:left; border-top:1px solid #000;">
&nbsp;&nbsp; 
</td>
<td width="25%" style="text-align:center; border-top:1px solid #000;"></td>
<td width="50%" style="text-align:right; border-top:1px solid #000;">
For : <b>Tirupati Plastomatics Pvt. Ltd.</b><br><br><br>
Authorised Signatory &nbsp;
</td>
</tr>
</table>
</td>
</tr>
</table>
	<table width="100%" style="border-top:1px solid #000;">
	<tr>
	<td width="100%" style="text-align:center; font-size:8px;font-weight:bold; color:#000; ">Subject to Jaipur Jurisdiction
</td>
	</tr>
	</table>
    </td>
</tr>
</table>

<!----------------------------------------------------------------------------------------------------------------------------Terms and Conditions End  -->

</div>
</body></html>';

// echo $html; die;
$pdf->WriteHTML($html, true, false, true, false, '');
ob_end_clean();
$date = date('d-m-Y');
echo $pdf->Output('PO-' . $users['purchaseorder_id'] . '.pdf');
exit;
