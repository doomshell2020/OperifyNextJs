<?php 
class xtcpdf extends TCPDF {
}
 //$subject=$this->Comman->findexamsubjectsresult($students['id'],$students['section']['id'],$students['acedmicyear']);

   $this->set('pdf', new TCPDF('1','mm','A4'));
$pdf = new TCPDF("L", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);

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


<table width="100%" cellspacing="0">
<tr>
<td width="50%">
<table width="100%" cellspacing="0">
<tr>
<td width="1%"> </td>
<td width="98%">
<table width="100%" cellspacing="0">
<tr>
<td width="100%" align="center">
<img src="images/logo.png" alt="" border="0" style="display:block;"
width="100px">
<h6 style="text-decoration:underline; font-size:8px;"> CBSE Delhi Senior Secondary English Medium Public School
</h6>
</td>
</tr>

<br>
<tr>
<td width="100%" align="center">
<div>Sumer Nagar Vistar, Major I.J. Sinha Marg, New Sanganer Road, Mansarover, Jaipur -302020 (Raj.) India</div>
<div style="height:8px; line-height:8px;"> Phone : +91-9425012920, 9549841880</div>
<div style="height:8px; line-height:8px;"> Email : kidsclubschoolindia@gmail.com</div>
<div style="height:8px; line-height:8px;"> Website : www.kidsclubschool.org</div>

</td>
</tr>

</table>
<br>
<br>
<br>
<table width="100%" cellspacing="0">
<tr>
<td width="100%">
<table width="100% " cellspacing="0">
<tr>
<td width="100%" style="border:1px  solid #000; font-size:12px; text-transform:uppercase; text-align:center; height:28px; line-height:28px;">
RECEIPT SESSION: 2021-22
</td>
</tr>

<tr>
<td width="100%" style="border:1px solid #000;">
<table width="100%">
<tr>

<td width="25%" style="height:12px; line-height:12px;">&nbsp; Receipt No.</td>
<td width="25%" style="height:12px; line-height:12px;">: 17591 </td>

<td width="25%" style="height:12px; line-height:12px;">Date.</td>
<td width="25%" style="height:12px; line-height:12px;">: 23-10-2021 &nbsp;</td>
</tr>

<tr>
<td width="25%" style="height:12px; line-height:12px;">&nbsp; Father/Mothers Name.</td>
<td width="25%" style="height:12px; line-height:12px;">: TEST </td>

<td width="25% " style="height:12px; line-height:12px;">Class.</td>
<td width="25%" style="height:12px; line-height:12px;">: VII-A &nbsp;</td>
</tr>

<tr>
<td width="25%" style="height:12px; line-height:12px;">&nbsp; Pupils Name.</td>
<td width="25%" style="height:12px; line-height:12px;">: SNEHA BUNDWAL </td>

<td width="25%" style="height:12px; line-height:12px;">Sr.No.</td>
<td width="25%" style="height:12px; line-height:12px;">: 1902 &nbsp;</td>
</tr>

</table>
</td>

</tr>
</table>
</td>
</tr>
</table>

<table width="100%" cellspacing="0">
<tr>

<td width="7%" style="border:1px solid #000; height:15px; line-height:15px; ">&nbsp; S.No.</td>
<td width="75%" style="border:1px solid #000; height:15px; line-height:15px; ">&nbsp; Particulars</td>
<td width="18%" style="border:1px solid #000; height:15px; line-height:15px; text-align:right;"> Amount &nbsp;</td>

</tr>



<tr>
<td width="7%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px;">&nbsp; 1.</td>
<td width="75%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px;">&nbsp; Admission Fee </td>
<td width="18%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px; text-align:right;">6,000.00 &nbsp;</td>

</tr>


<tr>
<td width="7%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px;">&nbsp; </td>
<td width="75%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px;">&nbsp;  </td>
<td width="18%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px; text-align:right;"> &nbsp;</td>

</tr>


<tr>
<td width="7%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px;">&nbsp; </td>
<td width="75%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px;">&nbsp;  </td>
<td width="18%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px; text-align:right;"> &nbsp;</td>

</tr>


<tr>
<td width="7%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px;">&nbsp; </td>
<td width="75%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px;">&nbsp;  </td>
<td width="18%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px; text-align:right;"> &nbsp;</td>

</tr>


<tr>
<td width="7%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px;">&nbsp; </td>
<td width="75%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px;">&nbsp;  </td>
<td width="18%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px; text-align:right;"> &nbsp;</td>

</tr>


<tr>
<td width="7%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px;">&nbsp; </td>
<td width="75%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px;">&nbsp;  </td>
<td width="18%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px; text-align:right;"> &nbsp;</td>

</tr>


<tr>
<td width="7%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px;">&nbsp; </td>
<td width="75%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px;">&nbsp;  </td>
<td width="18%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px; text-align:right;"> &nbsp;</td>

</tr>


<tr>
<td width="7%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px;">&nbsp; </td>
<td width="75%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px;">&nbsp;  </td>
<td width="18%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px; text-align:right;"> &nbsp;</td>

</tr>
<tr>
<td width="7%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px;">&nbsp; </td>
<td width="75%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px;">&nbsp;  </td>
<td width="18%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px; text-align:right;"> &nbsp;</td>

</tr>
<tr>
<td width="7%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px;">&nbsp; </td>
<td width="75%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px;">&nbsp;  </td>
<td width="18%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px; text-align:right;"> &nbsp;</td>

</tr>
<tr>
<td width="7%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px;">&nbsp; </td>
<td width="75%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px;">&nbsp;  </td>
<td width="18%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px; text-align:right;"> &nbsp;</td>

</tr>
<tr>
<td width="7%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px;">&nbsp; </td>
<td width="75%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px;">&nbsp;  </td>
<td width="18%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px; text-align:right;"> &nbsp;</td>

</tr>



</table>

<table width="100%">
<tr>
<td width="82%" style=" border-left:1px solid #000; border-right:1px solid #000; border-top:1px solid #000; height:15px; line-height:15px; text-align:right;">
Total Fees Rs.: &nbsp;
</td>

<td width="18%" style=" border-left:1px solid #000; border-right:1px solid #000; border-top:1px solid #000; height:15px; line-height:15px; text-align:right;">
6,000.00 &nbsp;
</td>
</tr>

<tr>
<td width="82%" style=" border-left:1px solid #000; border-right:1px solid #000; border-top:1px solid #000; height:15px; line-height:15px; text-align:right; font-weight:bold;">
Net Deposited Rs.: &nbsp;
</td>

<td width="18%" style=" border-left:1px solid #000; border-right:1px solid #000; border-top:1px solid #000; height:15px; line-height:15px; text-align:right;">
6,000.00 &nbsp;
</td>
</tr>
</table>


<table width="100%">
<tr>

<td width="100%" style="border:1px solid #000; height:15px; line-height:15px;">
&nbsp; Six Thousand Rupees Only

</td>

</tr>


<tr>

<td width="100%" style="border:1px solid #000; ">
 <div style="height:10px; line-height:7px;"> &nbsp; Paid by Cash Dt: 23-10-2021</div>
 <div style="height:10px; line-height:7px;"> &nbsp; Amount once deposited will not be refunded</div>
 <div style="height:10px; line-height:7px;"> &nbsp; Remarks</div>
 <div style="height:10px; line-height:4px;"> </div>


</td>

</tr>

<tr>

<td width="100%" style="height:15px; line-height:15px; text-align:right; font-size:10px;">Principal/Accountant Signature &nbsp;</td>

</tr>
</table>

</td>
<td width="1%"> </td>
</tr>
</table>
</td>



<td width="50%">
<table width="100%" cellspacing="0">
<tr>
<td width="1%"> </td>
<td width="98%">
<table width="100%" cellspacing="0">
<tr>
<td width="100%" align="center">
<img src="images/logo.png" alt="" border="0" style="display:block;"
width="100px">
<h6 style="text-decoration:underline; font-size:8px;"> CBSE Delhi Senior Secondary English Medium Public School
</h6>
</td>
</tr>

<br>
<tr>
<td width="100%" align="center">
<div>Sumer Nagar Vistar, Major I.J. Sinha Marg, New Sanganer Road, Mansarover, Jaipur -302020 (Raj.) India</div>
<div style="height:8px; line-height:8px;"> Phone : +91-9425012920, 9549841880</div>
<div style="height:8px; line-height:8px;"> Email : kidsclubschoolindia@gmail.com</div>
<div style="height:8px; line-height:8px;"> Website : www.kidsclubschool.org</div>

</td>
</tr>

</table>
<br>
<br>
<br>
<table width="100%" cellspacing="0">
<tr>
<td width="100%">
<table width="100% " cellspacing="0">
<tr>
<td width="100%" style="border:1px  solid #000; font-size:12px; text-transform:uppercase; text-align:center; height:28px; line-height:28px;">
RECEIPT SESSION: 2021-22
</td>
</tr>

<tr>
<td width="100%" style="border:1px solid #000;">
<table width="100%">
<tr>

<td width="25%" style="height:12px; line-height:12px;">&nbsp; Receipt No.</td>
<td width="25%" style="height:12px; line-height:12px;">: 17591 </td>

<td width="25%" style="height:12px; line-height:12px;">Date.</td>
<td width="25%" style="height:12px; line-height:12px;">: 23-10-2021 &nbsp;</td>
</tr>

<tr>
<td width="25%" style="height:12px; line-height:12px;">&nbsp; Father/Mothers Name.</td>
<td width="25%" style="height:12px; line-height:12px;">: TEST </td>

<td width="25% " style="height:12px; line-height:12px;">Class.</td>
<td width="25%" style="height:12px; line-height:12px;">: VII-A &nbsp;</td>
</tr>

<tr>
<td width="25%" style="height:12px; line-height:12px;">&nbsp; Pupils Name.</td>
<td width="25%" style="height:12px; line-height:12px;">: SNEHA BUNDWAL </td>

<td width="25%" style="height:12px; line-height:12px;">Sr.No.</td>
<td width="25%" style="height:12px; line-height:12px;">: 1902 &nbsp;</td>
</tr>

</table>
</td>

</tr>
</table>
</td>
</tr>
</table>

<table width="100%" cellspacing="0">
<tr>

<td width="7%" style="border:1px solid #000; height:15px; line-height:15px; ">&nbsp; S.No.</td>
<td width="75%" style="border:1px solid #000; height:15px; line-height:15px; ">&nbsp; Particulars</td>
<td width="18%" style="border:1px solid #000; height:15px; line-height:15px; text-align:right;"> Amount &nbsp;</td>

</tr>



<tr>
<td width="7%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px;">&nbsp; 1.</td>
<td width="75%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px;">&nbsp; Admission Fee </td>
<td width="18%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px; text-align:right;">6,000.00 &nbsp;</td>

</tr>


<tr>
<td width="7%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px;">&nbsp; </td>
<td width="75%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px;">&nbsp;  </td>
<td width="18%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px; text-align:right;"> &nbsp;</td>

</tr>


<tr>
<td width="7%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px;">&nbsp; </td>
<td width="75%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px;">&nbsp;  </td>
<td width="18%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px; text-align:right;"> &nbsp;</td>

</tr>


<tr>
<td width="7%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px;">&nbsp; </td>
<td width="75%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px;">&nbsp;  </td>
<td width="18%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px; text-align:right;"> &nbsp;</td>

</tr>


<tr>
<td width="7%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px;">&nbsp; </td>
<td width="75%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px;">&nbsp;  </td>
<td width="18%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px; text-align:right;"> &nbsp;</td>

</tr>


<tr>
<td width="7%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px;">&nbsp; </td>
<td width="75%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px;">&nbsp;  </td>
<td width="18%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px; text-align:right;"> &nbsp;</td>

</tr>


<tr>
<td width="7%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px;">&nbsp; </td>
<td width="75%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px;">&nbsp;  </td>
<td width="18%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px; text-align:right;"> &nbsp;</td>

</tr>


<tr>
<td width="7%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px;">&nbsp; </td>
<td width="75%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px;">&nbsp;  </td>
<td width="18%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px; text-align:right;"> &nbsp;</td>

</tr>
<tr>
<td width="7%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px;">&nbsp; </td>
<td width="75%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px;">&nbsp;  </td>
<td width="18%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px; text-align:right;"> &nbsp;</td>

</tr>
<tr>
<td width="7%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px;">&nbsp; </td>
<td width="75%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px;">&nbsp;  </td>
<td width="18%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px; text-align:right;"> &nbsp;</td>

</tr>
<tr>
<td width="7%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px;">&nbsp; </td>
<td width="75%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px;">&nbsp;  </td>
<td width="18%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px; text-align:right;"> &nbsp;</td>

</tr>
<tr>
<td width="7%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px;">&nbsp; </td>
<td width="75%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px;">&nbsp;  </td>
<td width="18%" style=" border-left:1px solid #000; border-right:1px solid #000; border-left:1px solid #000; height:15px; line-height:15px; text-align:right;"> &nbsp;</td>

</tr>



</table>

<table width="100%">
<tr>
<td width="82%" style=" border-left:1px solid #000; border-right:1px solid #000; border-top:1px solid #000; height:15px; line-height:15px; text-align:right;">
Total Fees Rs.: &nbsp;
</td>

<td width="18%" style=" border-left:1px solid #000; border-right:1px solid #000; border-top:1px solid #000; height:15px; line-height:15px; text-align:right;">
6,000.00 &nbsp;
</td>
</tr>

<tr>
<td width="82%" style=" border-left:1px solid #000; border-right:1px solid #000; border-top:1px solid #000; height:15px; line-height:15px; text-align:right; font-weight:bold;">
Net Deposited Rs.: &nbsp;
</td>

<td width="18%" style=" border-left:1px solid #000; border-right:1px solid #000; border-top:1px solid #000; height:15px; line-height:15px; text-align:right;">
6,000.00 &nbsp;
</td>
</tr>
</table>


<table width="100%">
<tr>

<td width="100%" style="border:1px solid #000; height:15px; line-height:15px;">
&nbsp; Six Thousand Rupees Only

</td>

</tr>


<tr>

<td width="100%" style="border:1px solid #000; ">
 <div style="height:10px; line-height:7px;"> &nbsp; Paid by Cash Dt: 23-10-2021</div>
 <div style="height:10px; line-height:7px;"> &nbsp; Amount once deposited will not be refunded</div>
 <div style="height:10px; line-height:7px;"> &nbsp; Remarks</div>
 <div style="height:10px; line-height:4px;"> </div>


</td>

</tr>

<tr>

<td width="100%" style="height:15px; line-height:15px; text-align:right; font-size:10px;">Principal/Accountant Signature &nbsp;</td>

</tr>
</table>

</td>
<td width="1%"> </td>
</tr>
</table>
</td>


</tr>
</table>



';
//echo "hello" die;
// echo $html; die;
$pdf->writeHTMLCell(0, 0, '', '', utf8_encode($html), 0, 1, 0, true, '', true);

//$pdf->WriteHTML($html, true, false, true, false, '');
ob_end_clean();
echo $pdf->Output('newfeesrecipt.pdf');
exit;
?>