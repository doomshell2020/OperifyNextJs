<?php
class xtcpdf extends TCPDF {}

$pdf = new TCPDF('P', 'mm', 'A4');

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->AddPage();

$pdf->SetMargins(8, 8, 8);
$pdf->SetAutoPageBreak(TRUE, 10);

$pdf->SetFont('', '', 8, '', 'true');
TCPDF_FONTS::addTTFfont('../Devanagari/Devanagari.ttf', 'TrueTypeUnicode', "", 32);

$i = 1;
$date = date("d-m-Y");
$html = '

<table border="1" cellpadding="4" cellspacing="0" width="100%">

<tr>
    <td width="70%">
        <b>ANNEXURE</b><br>
        <b>JOB CHALLAN</b><br>
        <span style="font-size:10px;">
        (Movement of inputs or partially processed goods from one factory to another factory for further processing)
        </span>
    </td>
    <td width="30%" align="right">
        Original : Pink<br>
        Duplicate : Green<br>
        Triplicate : White<br><br>
        <b>S. No: 60</b>
    </td>
</tr>

<tr>
    <td colspan="2">
        <b>Name & Address of Supplier:</b><br>
        Tirupati Plastomatics (P) Ltd.<br>
        Plot No. B-141-A, Road No. 9-D,<br>
        V.K.I Area, Jaipur - 302013<br>
        GSTIN: 08AAACT5317J1ZA
    </td>
</tr>

<tr>
    <td colspan="2"><b>PART - I</b></td>
</tr>

<tr>
    <td width="60%">1. Description of Goods</td>
    <td width="40%">'.$jc_data['job_challan_items'][0]['item_name'].'</td>
</tr>
<tr>
    <td>2. Identification marks</td>
    <td>02 Drum</td>
</tr>
<tr>
    <td>3. Quantity</td>
    <td>'.number_format((float)$jc_data['job_challan_items'][0]->quantity, 2, '.', '').'</td>
</tr>
<tr>
    <td>4. HSN/SAC</td>
    <td>'.$jc_data['job_challan_items'][0]['hsn_code'].'</td>
</tr>
<tr>
    <td>5. Estimated value</td>
    <td>'.number_format((float)$jc_data->estimated_values, 2, '.', '').'</td>
</tr>

<tr>
    <td>6. Total GST</td>
    <td>'.number_format((float)$jc_data['job_challan_items'][0]['tax_amount'], 2, '.', '').'</td>
</tr>
<tr>
    <td>7. Date & Time</td>
    <td>'.date('d-m-Y', strtotime($jc_data->jc_date)).'</td>
</tr>
<tr>
    <td>8. Nature of Processing</td>
    <td>'.ucwords(strtolower($jc_data->processing_type)).'</td>
</tr>
<tr>
    <td>9. Factory</td>
    <td>'.ucwords(strtolower($jc_data['sub_contractor']['name'])).'</td>
</tr>
<tr>
    <td>10. Duration</td>
    <td>'.$jc_data['expected_days'].' Day</td>
</tr>

<tr>
    <td>
    <b>Place:</b> Jaipur
    </td>
    <td align="right">
        <b>Date:</b> 16-04-2026
    </td>
</tr>

<tr>
    <td colspan="2" align="right" height="40">
        Signature
    </td>
</tr>



</table>
';

// $pdf->writeHTMLCell(0, 0, '', '', utf8_encode($html), 0, 1, 0, true, '', true);
$pdf->writeHTML($html, true, false, true, false, '');
ob_end_clean();
echo $pdf->Output('JC-001- ' . $date . '.pdf');
exit;
