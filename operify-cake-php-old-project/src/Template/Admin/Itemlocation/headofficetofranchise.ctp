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

<table width="100%">
<tr>
<td style="width:100%; border:1px solid #ddd; height:20px; line-height:20px; font-size:14px; text-align:center;">Tax Invoice</td>
</tr>

<tr>
<td style="width:20%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left; font-weight:bold;">
&nbsp; INVOICE NO. 
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left;">
&nbsp; 3401
</td>

<td style="width:20%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left; font-weight:bold;">
&nbsp; Dated
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left; ">
&nbsp; 22/11/2021
</td>

<td style="width:20%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left; font-weight:bold ">
&nbsp; TRANSPORT MODE
</td>

<td style="width:20%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left;  ">

</td>
</tr>


<tr>
<td style="width:30%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left; font-weight:bold;  ">
&nbsp; REVERSE CHARGE (YES/NO) 
</td>

<td style="width:20%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left;  ">

</td>

<td style="width:30%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left; font-weight:bold;  ">
&nbsp; VEHICLE NO.

</td>

<td style="width:20%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left;  ">

</td>
</tr>



<tr>
<td style="width:30%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left; font-weight:bold;  ">
&nbsp; STATE
</td>

<td style="width:20%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left;  ">
&nbsp; Rajasthan
</td>

<td style="width:30%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left; font-weight:bold;  ">
&nbsp; DATE OF SUPPLY

</td>

<td style="width:20%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left;  ">
&nbsp; 22 Nov, 2021
</td>
</tr>


<tr>
<td style="width:30%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left; font-weight:bold;  ">
&nbsp; PIN CODE
</td>

<td style="width:20%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left;  ">
&nbsp; 302019
</td>

<td style="width:30%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left; font-weight:bold;  ">
&nbsp; PLACE OF SUPPLY

</td>

<td style="width:20%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left;  ">
&nbsp; Jaipur
</td>
</tr>


<tr>
<td style="width:50%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:center; font-weight:bold;  ">
&nbsp; BILL TO PARTY
</td>



<td style="width:50%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:center; font-weight:bold;  ">
&nbsp; SHIP TO PARTY 

</td>


</tr>


<tr>
<td style="width:30%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left; font-weight:bold;  ">
&nbsp; NAME OF PARTY
</td>

<td style="width:20%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left;  ">
&nbsp; First Education

</td>

<td style="width:30%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left; font-weight:bold;  ">
&nbsp; NAME OF PARTY

</td>

<td style="width:20%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left;  ">
&nbsp; First Education

</td>
</tr>



<tr>
<td style="width:30%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left; font-weight:bold;  ">
&nbsp; ADDRESS
</td>

<td style="width:20%; border:1px solid #ddd; height:20px; line-height:16px;  text-align:left;  ">
&nbsp; 98, Mahatma Gandhi
Nagar, DCM, Ajmer
Road, Jaipur - 302006


</td>

<td style="width:30%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left; font-weight:bold;  ">
&nbsp; ADDRESS 

</td>

<td style="width:20%; border:1px solid #ddd; height:20px; line-height:16px;  text-align:left;  ">
&nbsp; 98, Mahatma Gandhi
Nagar, DCM, Ajmer
Road, Jaipur - 302006

</td>
</tr>

<tr>
<td style="width:100%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left;  font-weight:bold; ">
&nbsp; Mobile
</td>
</tr>


<tr>
<td style="width:30%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left; font-weight:bold;  ">
&nbsp; GSTIN 
</td>

<td style="width:20%; border:1px solid #ddd; height:20px; line-height:16px;  text-align:left;  ">
&nbsp; Unregistered


</td>

<td style="width:30%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left; font-weight:bold;  ">
&nbsp; GSTIN  

</td>

<td style="width:20%; border:1px solid #ddd; height:20px; line-height:16px;  text-align:left;  ">
&nbsp; Unregistered

</td>
</tr>


<tr>
<td style="width:30%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left; font-weight:bold;  ">
&nbsp; State 
</td>

<td style="width:20%; border:1px solid #ddd; height:20px; line-height:16px;  text-align:left;  ">
&nbsp; Rajasthan-Code 08


</td>

<td style="width:30%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left; font-weight:bold;  ">
&nbsp; State  

</td>

<td style="width:20%; border:1px solid #ddd; height:20px; line-height:16px;  text-align:left;  ">
&nbsp; Rajasthan-Code 08

</td>
</tr>



<tr>
<td style="width:8%; border:1px solid #ddd; height:20px; line-height:12px;  text-align:left; font-weight:bold;  ">
&nbsp; S.N.
</td>

<td style="width:14%; border:1px solid #ddd; height:20px; line-height:12px;  text-align:left; font-weight:bold;  ">
&nbsp; Item
</td>

<td style="width:8%; border:1px solid #ddd; height:20px; line-height:12px;  text-align:left; font-weight:bold;  ">
&nbsp; HSN <br>&nbsp; Code
</td>


<td style="width:8%; border:1px solid #ddd; height:20px; line-height:12px;  text-align:right; font-weight:bold;  ">
 Unit &nbsp; Price &nbsp;
</td>

<td style="width:12%; border:1px solid #ddd; height:20px; line-height:12px;  text-align:right; font-weight:bold;  ">
Quantity &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:12px;  text-align:right; font-weight:bold;  ">
Discount &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:12px;  text-align:right; font-weight:bold;  ">
GST &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:12px;  text-align:right; font-weight:bold;  ">
Taxable &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:12px;  text-align:right; font-weight:bold;  ">
Tax &nbsp; <br>Amount &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:12px;  text-align:right; font-weight:bold;  ">
Total &nbsp; Invoice &nbsp; 
</td>
</tr>




<tr>
<td style="width:8%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left;   ">
&nbsp; 1.
</td>

<td style="width:14%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left;   ">
&nbsp; DIARY
</td>

<td style="width:8%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left;   ">
&nbsp; 4820
</td>


<td style="width:8%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
89.29 &nbsp;
</td>

<td style="width:12%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
1 &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
13.39 &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
12% &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
75.90 &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
9.11 &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
85.01 &nbsp;
</td>
</tr>



<tr>
<td style="width:8%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left;   ">
&nbsp; 1.
</td>

<td style="width:14%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left;   ">
&nbsp; DIARY
</td>

<td style="width:8%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left;   ">
&nbsp; 4820
</td>


<td style="width:8%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
89.29 &nbsp;
</td>

<td style="width:12%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
1 &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
13.39 &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
12% &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
75.90 &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
9.11 &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
85.01 &nbsp;
</td>
</tr>



<tr>
<td style="width:8%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left;   ">
&nbsp; 1.
</td>

<td style="width:14%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left;   ">
&nbsp; DIARY
</td>

<td style="width:8%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left;   ">
&nbsp; 4820
</td>


<td style="width:8%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
89.29 &nbsp;
</td>

<td style="width:12%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
1 &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
13.39 &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
12% &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
75.90 &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
9.11 &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
85.01 &nbsp;
</td>
</tr>



<tr>
<td style="width:8%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left;   ">
&nbsp; 1.
</td>

<td style="width:14%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left;   ">
&nbsp; DIARY
</td>

<td style="width:8%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left;   ">
&nbsp; 4820
</td>


<td style="width:8%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
89.29 &nbsp;
</td>

<td style="width:12%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
1 &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
13.39 &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
12% &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
75.90 &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
9.11 &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
85.01 &nbsp;
</td>
</tr>



<tr>
<td style="width:8%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left;   ">
&nbsp; 1.
</td>

<td style="width:14%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left;   ">
&nbsp; DIARY
</td>

<td style="width:8%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left;   ">
&nbsp; 4820
</td>


<td style="width:8%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
89.29 &nbsp;
</td>

<td style="width:12%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
1 &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
13.39 &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
12% &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
75.90 &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
9.11 &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
85.01 &nbsp;
</td>
</tr>



<tr>
<td style="width:8%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left;   ">
&nbsp; 1.
</td>

<td style="width:14%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left;   ">
&nbsp; DIARY
</td>

<td style="width:8%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left;   ">
&nbsp; 4820
</td>


<td style="width:8%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
89.29 &nbsp;
</td>

<td style="width:12%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
1 &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
13.39 &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
12% &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
75.90 &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
9.11 &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
85.01 &nbsp;
</td>
</tr>



<tr>
<td style="width:8%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left;   ">
&nbsp; 1.
</td>

<td style="width:14%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left;   ">
&nbsp; DIARY
</td>

<td style="width:8%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left;   ">
&nbsp; 4820
</td>


<td style="width:8%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
89.29 &nbsp;
</td>

<td style="width:12%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
1 &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
13.39 &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
12% &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
75.90 &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
9.11 &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
85.01 &nbsp;
</td>
</tr>



<tr>
<td style="width:8%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left;   ">
&nbsp; 1.
</td>

<td style="width:14%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left;   ">
&nbsp; DIARY
</td>

<td style="width:8%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left;   ">
&nbsp; 4820
</td>


<td style="width:8%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
89.29 &nbsp;
</td>

<td style="width:12%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
1 &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
13.39 &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
12% &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
75.90 &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
9.11 &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
85.01 &nbsp;
</td>
</tr>


<tr>
<td style="width:8%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left;   ">
&nbsp; 1.
</td>

<td style="width:14%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left;   ">
&nbsp; DIARY
</td>

<td style="width:8%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left;   ">
&nbsp; 4820
</td>


<td style="width:8%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
89.29 &nbsp;
</td>

<td style="width:12%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
1 &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
13.39 &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
12% &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
75.90 &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
9.11 &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
85.01 &nbsp;
</td>
</tr>


<tr>
<td style="width:8%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left;   ">
&nbsp; 1.
</td>

<td style="width:14%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left;   ">
&nbsp; DIARY
</td>

<td style="width:8%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left;   ">
&nbsp; 4820
</td>


<td style="width:8%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
89.29 &nbsp;
</td>

<td style="width:12%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
1 &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
13.39 &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
12% &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
75.90 &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
9.11 &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
85.01 &nbsp;
</td>
</tr>


<tr>
<td style="width:8%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left;   ">
&nbsp; 1.
</td>

<td style="width:14%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left;   ">
&nbsp; DIARY
</td>

<td style="width:8%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left;   ">
&nbsp; 4820
</td>


<td style="width:8%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
89.29 &nbsp;
</td>

<td style="width:12%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
1 &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
13.39 &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
12% &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
75.90 &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
9.11 &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
85.01 &nbsp;
</td>
</tr>


<tr>
<td style="width:8%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left;   ">
&nbsp; 1.
</td>

<td style="width:14%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left;   ">
&nbsp; DIARY
</td>

<td style="width:8%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left;   ">
&nbsp; 4820
</td>


<td style="width:8%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
89.29 &nbsp;
</td>

<td style="width:12%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
1 &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
13.39 &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
12% &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
75.90 &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
9.11 &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
85.01 &nbsp;
</td>
</tr>


<tr>
<td style="width:30%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left; font-weight:bold;   ">
&nbsp; Total 
</td>


<td style="width:8%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
3662.30 &nbsp;
</td>

<td style="width:12%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
23 &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
701.24 &nbsp;
</td>



<td style="width:20%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
2961.06 &nbsp;
</td>



<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
163.37 &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
3124.43 &nbsp;
</td>
</tr>



<tr>
<td style="width:90%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right; font-weight:bold;   ">
Total Amount Before tax &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
2960.63 &nbsp;
</td>
</tr>

<tr>
<td style="width:90%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right; font-weight:bold;   ">
Total CGST &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
104.7 &nbsp;
</td>
</tr>

<tr>
<td style="width:90%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right; font-weight:bold;   ">
Total SGST &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
81.69 &nbsp;
</td>
</tr>

<tr>
<td style="width:90%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right; font-weight:bold;   ">
Total Tax Amount &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
163.37 &nbsp;
</td>
</tr>

<tr>
<td style="width:90%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right; font-weight:bold;   ">
Bill Amount &nbsp;
</td>

<td style="width:10%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:right;   ">
3124.00 &nbsp;
</td>
</tr>

</table>
<br>
<br>


<table width="100%">
<tr>
<td style="width:100%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left;   ">
&nbsp; <strong>Account No. :</strong> Axis Bank 916020010927278
</td>
</tr>

<tr>
<td style="width:100%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left;   ">
&nbsp; <strong>Account Holder. :</strong> Ingenious Edu Scholars Pvt. Ltd

</td>
</tr>

<tr>
<td style="width:100%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left;   ">
&nbsp; <strong>IFSC Code :</strong> UTIB0000031

</td>
</tr>

<tr>
<td style="width:100%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left;   ">
&nbsp; <strong>Description :</strong> Payment recived 22nd Nov

</td>
</tr>
</table>

<br>
<br>
<br>
<table width="100%">
<tr>
<td style="width:70%; border:1px solid #ddd; height:20px; line-height:20px;  text-align:left;   ">
<strong>&nbsp; 1. Terms and conditions of Bill :</strong><br>
&nbsp; &nbsp; 1. All Disputes Subject to Jaipur Jurisdiction.<br>
&nbsp; &nbsp; 2. Goods Once sold will not be taken back.<br>
&nbsp; &nbsp; 3. E & O.E.
</td>

<td style="width:30%; border:1px solid #ddd; height:20px; line-height:100px;  text-align:center;   ">
Authorized Signature 
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