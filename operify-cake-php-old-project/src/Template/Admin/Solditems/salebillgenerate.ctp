<?php 
class xtcpdf extends TCPDF {
}
 //$subject=$this->Comman->findexamsubjectsresult($students['id'],$students['section']['id'],$students['acedmicyear']);

   $this->set('pdf', new TCPDF('1','mm','A4'));
$pdf = new TCPDF("H", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);

// set document information

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->AddPage();
$pdf->setHeaderMargin(0);

// set margins

$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(TRUE, 0);
//$pdf->SetMargins(5, 0, 5, true);

$pdf->SetFont('', '', 7, '', 'true');
TCPDF_FONTS::addTTFfont('../Devanagari/Devanagari.ttf', 'TrueTypeUnicode', "", 32);

$logo  = SITE_URL."/images/".$site_details['small_logo'];


$sold_item_id = $solditem['id'];
$sold_pay_date =  date('d-m-Y', strtotime($solditem['pay_date']));
$issue_to = $solditem['customer_name'];
$pay_amount = $solditem['payamount'];

$other_amt = $solditem['other_amt'];

$total_pay_amount = $pay_amount + $other_amt;

$totalamount = $solditem['totalamount'];
$dis = $solditem['discount'];
if($dis){
  $discount = $solditem['discount'];
}else{
  $discount = $solditem['discount'];
}
$html='
<table width="100%" style="padding: 1px 1px 0px 0px;" align="left">
<tr>
<td style="text-align:left" width="50%" >
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="'.$logo.'" alt="" border="0" style="display:block;"
width="100px">
<!--<span style="display:block; color:#000; font-size:7px; height:12px; line-height:12px;"><u><br>Affiliated to CBSE Delhi (Affiliation
no.1730236)</u></span>-->
</td>

<td style="text-align:left;" width="50%" align="right"><br><br>
'.$sitesetting['site_title'].'<br> '.$sitesetting['site_keywords'].'
</td>
</tr>
</table>

<table style="100%">
<tr>
<td style="width:100%; border:1px solid #ddd; height:16px; line-height:16px;"><strong>Bill No.:</strong> '.$sold_item_id.' <strong>Date:</strong> 23 Nov, 2021
</td>
</tr>

<tr>

<td style="width:50%; border:1px solid #ddd; height:16px; line-height:16px;"><strong>Issue To:</strong>'.$issue_to.'</td>

<td style="width:50%; border:1px solid #ddd; height:16px; line-height:16px; text-align:right;"><strong>Issue Date:</strong> '.$sold_pay_date.'</td>

</tr>

<tr>
<td style="width:100%; border:1px solid #ddd; height:16px; line-height:16px; text-align:center;">Sale Bill ( Parent Copy )
</td>
</tr>


<tr>
<td style="width:12%; border:1px solid #ddd; height:16px; line-height:16px; text-align:center; font-weight:bold; text-align:left;">
S.N.
</td>

<td style="width:70%; border:1px solid #ddd; height:16px; line-height:16px; text-align:center; font-weight:bold; text-align:left;">
Item
</td>

<td style="width:18%; border:1px solid #ddd; height:16px; line-height:16px; text-align:center; font-weight:bold; text-align:right;">Qty.</td>
</tr>';

   
$s= 1; foreach($solditem['solditemdetails'] as $value){
 $total_qty  +=$value['item_qty'];
  $html.='<tr>
  <td style="width:12%; border:1px solid #ddd; height:16px; line-height:16px; text-align:center; text-align:left; ">
  '.$s.'
  </td>
  
  <td style="width:70%; border:1px solid #ddd; height:16px; line-height:16px; text-align:center; text-align:left; ">
  '.$value['item_name'].'
  </td>
  
  <td style="width:18%; border:1px solid #ddd; height:16px; line-height:16px; text-align:center; text-align:right; ">'.$value['item_qty'].'</td>
  </tr>';
  
  $s++; }
  



$total_amt = $totalamount + $other_amt;

$html.='<tr>
<td style="width:82%; border:1px solid #ddd; height:16px; line-height:16px; text-align:center; text-align:right; ">Total Items</td>

<td style="width:18%; border:1px solid #ddd; height:16px; line-height:16px; text-align:center; text-align:right; ">'. $total_qty.'</td>
</tr>
<tr>
<td style="width:82%; border:1px solid #ddd; height:16px; line-height:16px; text-align:center; text-align:right; ">Total Amount</td>



<td style="width:18%; border:1px solid #ddd; height:16px; line-height:16px; text-align:center; text-align:right; ">'.sprintf('%.2f',round($total_amt)).'</td>
</tr>


<tr>
<td style="width:82%; border:1px solid #ddd; height:16px; line-height:16px; text-align:center; text-align:right; ">Discount Amount</td>

<td style="width:18%; border:1px solid #ddd; height:16px; line-height:16px; text-align:center; text-align:right; "> -'.$discount.'</td>
</tr>



<tr>
<td style="width:82%; border:1px solid #ddd; height:16px; line-height:16px; text-align:center; text-align:right; ">Paid Amount</td>

<td style="width:18%; border:1px solid #ddd; height:16px; line-height:16px; text-align:center; text-align:right; ">'.sprintf('%.2f',round($total_pay_amount)).'</td>
</tr>

</table>
<br>
<br>

<table>
<tr>
<td style="width:100%; border:1px solid #ddd; height:16px; line-height:16px; text-align:center; text-align:right; ">
<table width="100%">
<tr>
<td width="2%"></td>

<td width="40%" style="text-align:left">
<strong>1. Terms and conditions of Bill :</strong><br>
&nbsp; &nbsp; a. All Disputes subject to Jaipur Jurisdiction.<br>
&nbsp; &nbsp; b. Goods Once sold will not be taken back.<br>
&nbsp; &nbsp; c. E & O.E
</td>

<td width="28%" style="text-align:center; line-height:100px; height:40px;">(Authorized Sign.)
</td>
<td width="28%" style="text-align:center; line-height:100px; height:40px;">(Student Sign.)
</td>
<td width="2%"></td>
</tr>
</table>

</td>
</tr>
</table>
<br pagebreak="true" />
<table width="100%" style="padding: 1px 1px 0px 0px;" align="left">
<tr>
<td style="text-align:left" width="50%" >
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="'.$logo.'" alt="" border="0" style="display:block;"
width="100px">
<!--<span style="display:block; color:#000; font-size:8px; height:12px; line-height:12px;"><u><br>Affiliated to CBSE Delhi (Affiliation
no.1730236)</u></span>-->
</td>

<td style="text-align:left;" width="50%" align="right"><br><br>
'.$sitesetting['site_title'].'
</td>
</tr>
</table>
<br>
<br>

<table style="100%">
<tr>
<td style="width:100%; border:1px solid #ddd; height:16px; line-height:16px;"><strong>Bill No.:</strong>  '.$sold_item_id.' <strong>Date:</strong>  '.$sold_pay_date.'
</td>
</tr>

<tr>

<td style="width:50%; border:1px solid #ddd; height:16px; line-height:16px;"><strong>Issue To:</strong>'.$issue_to.'</td>

<td style="width:50%; border:1px solid #ddd; height:16px; line-height:16px; text-align:right;"><strong>Issue Date:</strong> '.$sold_pay_date.'</td>

</tr>

<tr>
<td style="width:100%; border:1px solid #ddd; height:16px; line-height:16px; text-align:center;">Sale Bill ( Office Copy )
</td>
</tr>



<tr>
<td style="width:12%; border:1px solid #ddd; height:16px; line-height:16px; text-align:center; font-weight:bold; text-align:left;">
S.No.
</td>

<td style="width:70%; border:1px solid #ddd; height:16px; line-height:16px; text-align:center; font-weight:bold; text-align:left;">
Item
</td>

<td style="width:18%; border:1px solid #ddd; height:16px; line-height:16px; text-align:center; font-weight:bold; text-align:right;">Qty.</td>
</tr>';
$s= 1; foreach($solditem['solditemdetails'] as $value){
$html.='<tr>
<td style="width:12%; border:1px solid #ddd; height:16px; line-height:16px; text-align:center; text-align:left; ">
'.$s.'
</td>

<td style="width:70%; border:1px solid #ddd; height:16px; line-height:16px; text-align:center; text-align:left; ">
'.$value['item_name'].'
</td>

<td style="width:18%; border:1px solid #ddd; height:16px; line-height:16px; text-align:center; text-align:right; ">'.$value['item_qty'].'</td>
</tr>';

$s++; }






  $html.='<tr>
  <td style="width:82%; border:1px solid #ddd; height:16px; line-height:16px; text-align:center; text-align:right; ">Total Items</td>

  <td style="width:18%; border:1px solid #ddd; height:16px; line-height:16px; text-align:center; text-align:right; ">'. $total_qty.'</td>
  </tr>
  <tr>
  <td style="width:82%; border:1px solid #ddd; height:16px; line-height:16px; text-align:center; text-align:right; ">Total Amount</td>
  
  <td style="width:18%; border:1px solid #ddd; height:16px; line-height:16px; text-align:center; text-align:right; ">'.sprintf('%.2f',round($total_amt)).'</td>
  </tr>


  <tr>
  <td style="width:82%; border:1px solid #ddd; height:16px; line-height:16px; text-align:center; text-align:right; ">Discount Amount</td>
  
  <td style="width:18%; border:1px solid #ddd; height:16px; line-height:16px; text-align:center; text-align:right; "> -'.$discount.'</td>
  </tr>

 

<tr>
<td style="width:82%; border:1px solid #ddd; height:16px; line-height:16px; text-align:center; text-align:right; ">Paid Amount</td>

<td style="width:18%; border:1px solid #ddd; height:16px; line-height:16px; text-align:center; text-align:right; ">'.sprintf('%.2f',round($total_pay_amount)).'</td>
</tr>


</table>
<br>
<br>

<table>
<tr>
<td style="width:100%; border:1px solid #ddd; height:16px; line-height:16px; text-align:center; text-align:right; ">
<table width="100%">
<tr>
<td width="2%"></td>

<td width="40%" style="text-align:left">
<strong>1. Terms and conditions of Bill :</strong><br>
&nbsp; &nbsp; a. All Disputes subject to Jaipur Jurisdiction.<br>
&nbsp; &nbsp; b. Goods Once sold will not be taken back.<br>
&nbsp; &nbsp; c. E & O.E
</td>

<td width="28%" style="text-align:center; line-height:100px; height:40px;">(Authorized Sign.)
</td>
<td width="28%" style="text-align:center; line-height:100px; height:40px;">(Student Sign.)
</td>
<td width="2%"></td>
</tr>
</table>

</td>
</tr>
</table>
';

$pdf->writeHTMLCell(0, 0, '', '', utf8_encode($html), 0, 1, 0, true, '', true);
//$pdf->WriteHTML($html, true, false, true, false, '');
ob_end_clean();
echo $pdf->Output('orderlist.pdf');
exit;
?>