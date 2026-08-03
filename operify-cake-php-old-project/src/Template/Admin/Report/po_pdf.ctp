<?php
class xtcpdf extends TCPDF
{

}

$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false, true);
$pdf->SetPrintHeader(false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->AddPage();
$pdf->SetFont('', '', 9, '', 'false');

$html = '



<table width="100%">
<tr>
<td width="100%" style="border-left:1px solid #000; border-right:1px solid #000; border-top:1px solid #000;  text-align:center; font-size:18px;">
Avinash Kumbhaj & Associates							

</td>
</tr>
<tr>
<td width="100%" style="border-left:1px solid #000; border-right:1px solid #000; border-bottom:none; text-align:center; font-size:9px; font-weight:bold;">
Advocate							

</td>
</tr>

<tr>
<td width="100%" style="border-left:1px solid #000; border-right:1px solid #000; border-bottom:none; text-align:center; font-size:9px;">
   <strong> Off.:</strong> E-273, Nakul Path, Lal Kothi Scheme, Opp. Jyoti Nagar Police Station Jaipur-302015							
							

</td>
</tr>
</table>

<table width="100%">
<tr>
<td width="100%" style="height:0px; border-left:1px solid #000; border-right:1px solid #000;"></td>
</tr>
</table>


<table width="100%">
<tr>
<td width="20%" style="border-left:1px solid #000; height:13px; line-height:13px;">

</td>

<td width="35%">
<table width="100%">
<tr>
<td width="30%" style="font-size:9px; font-weight:bold; height:13px; line-height:13px;">Mobile</td>
<td width="70%" style="font-size:9px; height:13px; line-height:13px;">9414046835     </td>
</tr>
</table>
</td>

<td width="35%">
<table width="100%">
<tr>
<td width="30%" style="font-size:9px; font-weight:bold; height:13px; line-height:13px;">Phone</td>
<td width="70%" style="font-size:9px; height:13px; line-height:13px;">0141-2744701     </td>
</tr>
</table>
</td>

<td width="10%" style="border-right:1px solid #000; height:13px; line-height:13px;"></td>
</tr>
</table>


<table width="100%">
<tr>
<td width="20%" style="border-left:1px solid #000; border-bottom:1px solid #000; height:13px; line-height:13px;">

</td>

<td width="35%" style="border-bottom:1px solid #000; ">
<table width="100%">
<tr>
<td width="30%" style="font-size:9px; font-weight:bold; height:13px; line-height:13px;">Email</td>
<td width="70%" style="font-size:9px; height:13px; line-height:13px;">avinash@kumbhaj.com      </td>
</tr>
</table>
</td>

<td width="35%" style="border-bottom:1px solid #000;">
<table width="100%">
<tr>
<td width="30%" style="font-size:9px; font-weight:bold; height:13px; line-height:13px;">PAN No</td>
<td width="70%" style="font-size:9px; height:13px; line-height:13px;">AICPK5890L     </td>
</tr>
</table>
</td>

<td width="10%" style="border-right:1px solid #000; border-bottom:1px solid #000;"></td>
</tr>
</table>

<table width="100%">
<tr>
<td width="100%" style="height:10px; border-left:1px solid #000; border-right:1px solid #000;"></td>
</tr>
</table>

<table width="100%">
<tr>
<td width="1%" style="border-left:1px solid #000;"></td>
<td width="49%" style="text-align:left; height:20px; line-height:20px;">   <strong>Bill No.</strong> 5736/2018-19 </td>

<td width="48%" style="text-align:right; height:20px; line-height:20px;">   <strong>Date</strong> Mar 27, 2019     </td>
<td width="2%" style="border-right:1px solid #000;"></td>
</tr>
</table>


<table width="100%">
<tr>
<td width="2%" style="height:15px; line-height:15px; border-left:1px solid #000;"></td>
<td width="98%" style="height:15px; line-height:15px; border-right:1px solid #000;">To,</td>
</tr>

<tr>
<td width="2%" style="height:15px; line-height:15px; border-left:1px solid #000;"></td>
<td width="98%" style=" border-right:1px solid #000;">Mr/M/s Anand Rathi Global Finance Ltd.<br>
Express Zone, A-Wing, 8th Floor<br>
Western Express Highway<br>
Goregaon (E), Mumbai-400063
</td>
</tr>
</table>


<table width="100%">
<tr>
<td width="100%" style="height:22px; border-left:1px solid #000; border-right:1px solid #000;"></td>
</tr>
</table>


<table width="100%">
<tr>
<td width="100%" style="text-align:center; font-size:10px; border-left:1px solid #000; border-right:1px solid #000;">
Bill of February-2019
</td>
</tr>

<tr>
<td width="100%" style="text-align:center; font-size:12px; font-weight:bold; border-left:1px solid #000; border-right:1px solid #000;">
Complaint U/s 138 NI Act
</td>
</tr>
</table>

<table width="100%">
<tr>
<td width="100%" style="height:10px; border-left:1px solid #000; border-right:1px solid #000;"></td>
</tr>
</table>
<table width="100%">
<tr>
<td width="4%" style="font-weight:bold; height:12px; line-height:12px; border-top:1px solid #000;border-left:1px solid #000; border-bottom:1px solid #000; text-align:center; border-right:1px solid #000;">S.<br>No.</td>

<td width="15%" style="font-weight:bold; height:12px; line-height:12px; border-top:1px solid #000; border-bottom:1px solid #000; text-align:center; border-right:1px solid #000;">Apac No.</td>

<td width="27%" style="font-weight:bold; height:12px; line-height:12px; border-top:1px solid #000; border-bottom:1px solid #000; text-align:center; border-right:1px solid #000;">Detail</td>

<td width="10%" style="font-weight:bold; height:12px; line-height:12px; border-top:1px solid #000; border-bottom:1px solid #000; text-align:center; border-right:1px solid #000;">Location</td>

<td width="10%" style="font-weight:bold; height:12px; line-height:12px; border-top:1px solid #000; border-bottom:1px solid #000; text-align:center; border-right:1px solid #000;">Chq No.</td>

<td width="10%" style="font-weight:bold; height:12px; line-height:12px; border-top:1px solid #000; border-bottom:1px solid #000; text-align:center; border-right:1px solid #000;">Court</td>

<td width="10%" style="font-weight:bold; height:12px; line-height:12px; border-top:1px solid #000; border-bottom:1px solid #000; text-align:center; border-right:1px solid #000;">Filling Date</td>

<td width="14%" style="font-weight:bold; height:12px; line-height:12px; border-top:1px solid #000; border-bottom:1px solid #000; text-align:center; border-right:1px solid #000;">Amount</td>

</tr>


<tr>
<td width="4%" style=" height:12px; line-height:12px; border-top:1px solid #000;border-left:1px solid #000; border-bottom:1px solid #000; text-align:center; border-right:1px solid #000;">1.</td>

<td width="15%" style=" height:12px; line-height:12px; border-top:1px solid #000; border-bottom:1px solid #000; text-align:center; border-right:1px solid #000;">ARGFL/SME-LAP/DEL/1102
</td>

<td width="27%" style=" height:12px; line-height:12px; border-top:1px solid #000; border-bottom:1px solid #000; text-align:center; border-right:1px solid #000;">"RACHNA UDAR
JANAK MADICOS"
</td>

<td width="10%" style=" height:12px; line-height:12px; border-top:1px solid #000; border-bottom:1px solid #000; text-align:center; border-right:1px solid #000;">Jaipur
</td>

<td width="10%" style=" height:12px; line-height:12px; border-top:1px solid #000; border-bottom:1px solid #000; text-align:center; border-right:1px solid #000;">44</td>

<td width="10%" style=" height:12px; line-height:12px; border-top:1px solid #000; border-bottom:1px solid #000; text-align:center; border-right:1px solid #000;">NI Act 17
</td>

<td width="10%" style=" height:12px; line-height:12px; border-top:1px solid #000; border-bottom:1px solid #000; text-align:center; border-right:1px solid #000;">12-Mar-19
</td>

<td width="14%" style=" height:12px; line-height:12px; border-top:1px solid #000; border-bottom:1px solid #000; text-align:center; border-right:1px solid #000;">3000.00 (50% upon filing)
</td>

</tr>

</table>
	







<table width="100%">
<tr>
<td width="100%" style="height:35px; border-left:1px solid #000; border-right:1px solid #000;"></td>
</tr>
</table>



<table width="100%">


<tr>
<td width="100%" style="text-align:center; font-size:12px; font-weight:bold; border-left:1px solid #000; border-right:1px solid #000;">
Procurement of Summon/Warrants							

</td>
</tr>
</table>

<table width="100%">
<tr>
<td width="100%" style="height:10px; border-left:1px solid #000; border-right:1px solid #000;"></td>
</tr>
</table>
<table width="100%">
<tr>
<td width="4%" style="font-weight:bold; height:12px; line-height:12px; border-top:1px solid #000;border-left:1px solid #000; border-bottom:1px solid #000; text-align:center; border-right:1px solid #000;">S.<br>No.</td>

<td width="15%" style="font-weight:bold; height:12px; line-height:12px; border-top:1px solid #000; border-bottom:1px solid #000; text-align:center; border-right:1px solid #000;">Apac No.</td>

<td width="27%" style="font-weight:bold; height:12px; line-height:12px; border-top:1px solid #000; border-bottom:1px solid #000; text-align:center; border-right:1px solid #000;">Detail</td>

<td width="10%" style="font-weight:bold; height:12px; line-height:12px; border-top:1px solid #000; border-bottom:1px solid #000; text-align:center; border-right:1px solid #000;">Location</td>

<td width="10%" style="font-weight:bold; height:12px; line-height:12px; border-top:1px solid #000; border-bottom:1px solid #000; text-align:center; border-right:1px solid #000;">Case No
</td>

<td width="10%" style="font-weight:bold; height:12px; line-height:12px; border-top:1px solid #000; border-bottom:1px solid #000; text-align:center; border-right:1px solid #000;">Court</td>

<td width="10%" style="font-weight:bold; height:12px; line-height:12px; border-top:1px solid #000; border-bottom:1px solid #000; text-align:center; border-right:1px solid #000;">Filling Date</td>

<td width="14%" style="font-weight:bold; height:12px; line-height:12px; border-top:1px solid #000; border-bottom:1px solid #000; text-align:center; border-right:1px solid #000;">Amount</td>

</tr>


<tr>
<td width="4%" style=" height:12px; line-height:12px; border-top:1px solid #000;border-left:1px solid #000; border-bottom:1px solid #000; text-align:center; border-right:1px solid #000;">1.</td>

<td width="15%" style=" height:12px; line-height:12px; border-top:1px solid #000; border-bottom:1px solid #000; text-align:center; border-right:1px solid #000;">ARGFL/SME-LAP/DEL/1102

</td>

<td width="27%" style=" height:12px; line-height:12px; border-top:1px solid #000; border-bottom:1px solid #000; text-align:center; border-right:1px solid #000;">"RACHNA UDAR
JANAK MADICOS"

</td>

<td width="10%" style=" height:12px; line-height:12px; border-top:1px solid #000; border-bottom:1px solid #000; text-align:center; border-right:1px solid #000;">Jaipur
</td>

<td width="10%" style=" height:12px; line-height:12px; border-top:1px solid #000; border-bottom:1px solid #000; text-align:center; border-right:1px solid #000;">5046/2019
</td>

<td width="10%" style=" height:12px; line-height:12px; border-top:1px solid #000; border-bottom:1px solid #000; text-align:center; border-right:1px solid #000;">NI Act 17

</td>

<td width="10%" style=" height:12px; line-height:12px; border-top:1px solid #000; border-bottom:1px solid #000; text-align:center; border-right:1px solid #000;">12-Mar-19
</td>

<td width="14%" style=" height:12px; line-height:12px; border-top:1px solid #000; border-bottom:1px solid #000; text-align:center; border-right:1px solid #000;">500.00

</td>

</tr>

</table>



<table width="100%">
<tr>
<td width="100%" style="height:30px; border-left:1px solid #000; border-right:1px solid #000;"></td>
</tr>
</table>


<table width="100%">
<tr>

<td width="96%" style="text-align:right; font-size:16px; border-left:1px solid #000; ">(Avinash Kumbhaj)			
</td>
<td width="4%" style="border-right:1px solid #000;"> </td>
</tr>

<tr>
 <td width="70%" style="border-left:1px solid #000;"></td>
<td width="26%" style="text-align:center; font-size:9px; ">          Advocate		
			
</td>
<td width="4%" style="border-right:1px solid #000;"> </td>
</tr>
</table>


<table width="100%">
<tr>
<td width="100%" style="height:30px; border-left:1px solid #000; border-right:1px solid #000; border-bottom:1px solid #000;"></td>
</tr>
</table>

	';
//echo $html;die;

$pdf->WriteHTML($html);
ob_end_clean();

echo $pdf->Output('ADVANCE SALARY');

exit;