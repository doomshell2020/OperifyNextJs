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
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetAutoPageBreak(TRUE, 0);
//$pdf->SetMargins(5, 0, 5, true);

$pdf->SetFont('', '', 8, '', 'true');
TCPDF_FONTS::addTTFfont('../Devanagari/Devanagari.ttf', 'TrueTypeUnicode', "", 32);
$html='
<table width="100%" style="padding: 1px 1px 0px 0px;" align="left">
<tr>
<td style="text-align:left" width="50%" >
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/logo.png" alt="" border="0" style="display:block;"
width="100px">
<span style="display:block; color:#000; font-size:8px; height:12px; line-height:12px;"><u><br>Affiliated to CBSE Delhi (Affiliation
no.1730236)</u></span>
</td>

<td style="text-align:left;" width="50%" align="right"><br><br>
Vishwamitra Marg, Near Hanuman Nagar
Ext.<br>&nbsp;&nbsp;&nbsp;&nbsp;Sirsi Road, Jaipur-302012&nbsp;&nbsp;<br>
<b>Phone</b>
: &nbsp;2246189 ,2357844 &nbsp;&nbsp;&nbsp;&nbsp;<b>Fax </b>:-  
0141-2245602<br>&nbsp;&nbsp;&nbsp;&nbsp;<b>Email</b>
: <u>
info@sanskarjaipur.com</u><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Website</b> : www.sanskarjaipur.com
</td>
</tr>
</table>
<br>
<br>

<table style="100%">
<tr>
<td style="width:100%; border:1px solid #ddd; height:20px; line-height:20px;"><strong>Bill No.:</strong> 878 <strong>Date:</strong> 23 Nov, 2021
</td>
</tr>

<tr>
<td style="width:100%; border:1px solid #ddd; height:20px; line-height:20px;"><strong>Issue To:</strong><br>
<strong>Issue Date:</strong> 23 Nov, 2021</td>
</tr>

<tr>
<td style="width:100%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center;">Sale Bill ( Parent Copy )
</td>
</tr>



<tr>
<td style="width:12%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; font-weight:bold; text-align:left;">
S.N.
</td>

<td style="width:70%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; font-weight:bold; text-align:left;">
Item
</td>

<td style="width:18%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; font-weight:bold; text-align:right;">Qty.</td>
</tr>

<tr>
<td style="width:12%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:left; ">
1.
</td>

<td style="width:70%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:left; ">
NOT BOOK MATHS 
</td>

<td style="width:18%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:right; ">1</td>
</tr>

<tr>
<td style="width:12%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:left; ">
1.
</td>

<td style="width:70%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:left; ">
NOT BOOK MATHS 
</td>

<td style="width:18%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:right; ">1</td>
</tr>


<tr>
<td style="width:12%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:left; ">
1.
</td>

<td style="width:70%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:left; ">
NOT BOOK MATHS 
</td>

<td style="width:18%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:right; ">1</td>
</tr>


<tr>
<td style="width:12%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:left; ">
1.
</td>

<td style="width:70%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:left; ">
NOT BOOK MATHS 
</td>

<td style="width:18%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:right; ">1</td>
</tr>


<tr>
<td style="width:12%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:left; ">
1.
</td>

<td style="width:70%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:left; ">
NOT BOOK MATHS 
</td>

<td style="width:18%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:right; ">1</td>
</tr>


<tr>
<td style="width:12%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:left; ">
1.
</td>

<td style="width:70%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:left; ">
NOT BOOK MATHS 
</td>

<td style="width:18%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:right; ">1</td>
</tr>


<tr>
<td style="width:12%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:left; ">
1.
</td>

<td style="width:70%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:left; ">
NOT BOOK MATHS 
</td>

<td style="width:18%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:right; ">1</td>
</tr>


<tr>
<td style="width:12%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:left; ">
1.
</td>

<td style="width:70%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:left; ">
NOT BOOK MATHS 
</td>

<td style="width:18%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:right; ">1</td>
</tr>


<tr>
<td style="width:12%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:left; ">
1.
</td>

<td style="width:70%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:left; ">
NOT BOOK MATHS 
</td>

<td style="width:18%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:right; ">1</td>
</tr>


<tr>
<td style="width:12%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:left; ">
1.
</td>

<td style="width:70%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:left; ">
NOT BOOK MATHS 
</td>

<td style="width:18%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:right; ">1</td>
</tr>


<tr>
<td style="width:12%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:left; ">
1.
</td>

<td style="width:70%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:left; ">
NOT BOOK MATHS 
</td>

<td style="width:18%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:right; ">1</td>
</tr>


<tr>
<td style="width:82%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:right; ">Total</td>

<td style="width:18%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:right; ">11</td>
</tr>

<tr>
<td style="width:82%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:right; ">Pay Amount</td>

<td style="width:18%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:right; ">1090.00</td>
</tr>

<tr>
<td style="width:82%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:right; ">Total Paid</td>

<td style="width:18%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:right; ">1090.00</td>
</tr>

</table>
<br>
<br>

<table>
<tr>
<td style="width:100%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:right; ">
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
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/logo.png" alt="" border="0" style="display:block;"
width="100px">
<span style="display:block; color:#000; font-size:8px; height:12px; line-height:12px;"><u><br>Affiliated to CBSE Delhi (Affiliation
no.1730236)</u></span>
</td>

<td style="text-align:left;" width="50%" align="right"><br><br>
Vishwamitra Marg, Near Hanuman Nagar
Ext.<br>&nbsp;&nbsp;&nbsp;&nbsp;Sirsi Road, Jaipur-302012&nbsp;&nbsp;<br>
<b>Phone</b>
: &nbsp;2246189 ,2357844 &nbsp;&nbsp;&nbsp;&nbsp;<b>Fax </b>:-  
0141-2245602<br>&nbsp;&nbsp;&nbsp;&nbsp;<b>Email</b>
: <u>
info@sanskarjaipur.com</u><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Website</b> : www.sanskarjaipur.com
</td>
</tr>
</table>
<br>
<br>

<table style="100%">
<tr>
<td style="width:100%; border:1px solid #ddd; height:20px; line-height:20px;"><strong>Bill No.:</strong> 878 <strong>Date:</strong> 23 Nov, 2021
</td>
</tr>

<tr>
<td style="width:100%; border:1px solid #ddd; height:20px; line-height:20px;"><strong>Issue To:</strong><br>
<strong>Issue Date:</strong> 23 Nov, 2021</td>
</tr>

<tr>
<td style="width:100%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center;">Sale Bill ( Office Copy )
</td>
</tr>



<tr>
<td style="width:12%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; font-weight:bold; text-align:left;">
S.N.
</td>

<td style="width:70%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; font-weight:bold; text-align:left;">
Item
</td>

<td style="width:18%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; font-weight:bold; text-align:right;">Qty.</td>
</tr>

<tr>
<td style="width:12%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:left; ">
1.
</td>

<td style="width:70%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:left; ">
NOT BOOK MATHS 
</td>

<td style="width:18%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:right; ">1</td>
</tr>

<tr>
<td style="width:12%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:left; ">
1.
</td>

<td style="width:70%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:left; ">
NOT BOOK MATHS 
</td>

<td style="width:18%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:right; ">1</td>
</tr>


<tr>
<td style="width:12%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:left; ">
1.
</td>

<td style="width:70%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:left; ">
NOT BOOK MATHS 
</td>

<td style="width:18%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:right; ">1</td>
</tr>


<tr>
<td style="width:12%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:left; ">
1.
</td>

<td style="width:70%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:left; ">
NOT BOOK MATHS 
</td>

<td style="width:18%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:right; ">1</td>
</tr>


<tr>
<td style="width:12%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:left; ">
1.
</td>

<td style="width:70%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:left; ">
NOT BOOK MATHS 
</td>

<td style="width:18%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:right; ">1</td>
</tr>


<tr>
<td style="width:12%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:left; ">
1.
</td>

<td style="width:70%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:left; ">
NOT BOOK MATHS 
</td>

<td style="width:18%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:right; ">1</td>
</tr>


<tr>
<td style="width:12%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:left; ">
1.
</td>

<td style="width:70%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:left; ">
NOT BOOK MATHS 
</td>

<td style="width:18%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:right; ">1</td>
</tr>


<tr>
<td style="width:12%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:left; ">
1.
</td>

<td style="width:70%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:left; ">
NOT BOOK MATHS 
</td>

<td style="width:18%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:right; ">1</td>
</tr>


<tr>
<td style="width:12%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:left; ">
1.
</td>

<td style="width:70%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:left; ">
NOT BOOK MATHS 
</td>

<td style="width:18%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:right; ">1</td>
</tr>


<tr>
<td style="width:12%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:left; ">
1.
</td>

<td style="width:70%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:left; ">
NOT BOOK MATHS 
</td>

<td style="width:18%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:right; ">1</td>
</tr>


<tr>
<td style="width:12%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:left; ">
1.
</td>

<td style="width:70%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:left; ">
NOT BOOK MATHS 
</td>

<td style="width:18%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:right; ">1</td>
</tr>


<tr>
<td style="width:82%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:right; ">Total</td>

<td style="width:18%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:right; ">11</td>
</tr>

<tr>
<td style="width:82%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:right; ">Pay Amount</td>

<td style="width:18%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:right; ">1090.00</td>
</tr>

<tr>
<td style="width:82%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:right; ">Total Paid</td>

<td style="width:18%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:right; ">1090.00</td>
</tr>

</table>
<br>
<br>

<table>
<tr>
<td style="width:100%; border:1px solid #ddd; height:20px; line-height:20px; text-align:center; text-align:right; ">
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