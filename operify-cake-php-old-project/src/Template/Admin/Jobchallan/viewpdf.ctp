<?php
class xtcpdf extends TCPDF {}

$pdf = new TCPDF('P', 'mm', 'A4');

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->AddPage();

// Set margins: 5mm margins for maximum printable width and height
$pdf->SetMargins(5, 5, 5);
$pdf->SetAutoPageBreak(TRUE, 5);

// Set base font size to 9.0 for readable visibility on a single page
$pdf->SetFont('', '', 9, '', 'true');
TCPDF_FONTS::addTTFfont('../Devanagari/Devanagari.ttf', 'TrueTypeUnicode', "", 32);

$date = date("d-m-Y");

// Resolve dynamic supplier details from sitesettings and sitesettings_details
$supplier_name = '';
if (!empty($sitesetting)) {
    $supplier_name = !empty($sitesetting->first_name) ? trim($sitesetting->first_name) : '';
}
if (empty($supplier_name) && !empty($site_details)) {
    $supplier_name = !empty($site_details->company_name) ? trim($site_details->company_name) : '';
}
if (empty($supplier_name)) {
    $supplier_name = 'Tirupati Plastomatics (P) Ltd.';
}

$supplier_address = '';
if (!empty($site_details)) {
    $addr_parts = [];
    if (!empty($site_details->address1)) $addr_parts[] = trim($site_details->address1);
    if (!empty($site_details->address2)) $addr_parts[] = trim($site_details->address2);
    if (empty($addr_parts) && !empty($site_details->address)) $addr_parts[] = trim($site_details->address);
    $supplier_address = implode(', ', $addr_parts);
}
if (empty($supplier_address)) {
    $supplier_address = 'Plot No. B-141-A, Road No. 9-D, V.K.I Area, Jaipur - 302013';
}

$supplier_gstin = '';
if (!empty($site_details)) {
    $supplier_gstin = !empty($site_details->gst_no) ? trim($site_details->gst_no) : (!empty($site_details->gst) ? trim($site_details->gst) : '');
}
if (empty($supplier_gstin) || $supplier_gstin == '00') {
    $supplier_gstin = '08AAACT5317J1ZA'; // Fallback
}

$supplier_pan = '';
if (!empty($site_details)) {
    $supplier_pan = !empty($site_details->pan_number) ? trim($site_details->pan_number) : (!empty($site_details->pan_no) ? trim($site_details->pan_no) : '');
}
if (empty($supplier_pan)) {
    $supplier_pan = 'AAACT5317J'; // Fallback
}

$supplier_city = 'Jaipur';
if (!empty($supplier_address)) {
    if (stripos($supplier_address, 'jaipur') !== false) {
        $supplier_city = 'Jaipur';
    }
}

// Resolve dynamic unit of measurement (UOM)
$uom_name = 'KG';
if (!empty($jc_data['job_challan_items'])) {
    $itemId = $jc_data['job_challan_items'][0]['item_id'];
    $conn = \Cake\Datasource\ConnectionManager::get('default');
    $uomRow = $conn->execute("
        SELECT m.unit_name 
        FROM st_additem a 
        LEFT JOIN st_measurementunits m ON a.uom = m.id 
        WHERE a.id = :id 
        LIMIT 1
    ", ['id' => $itemId])->fetch('assoc');
    if ($uomRow && !empty($uomRow['unit_name'])) {
        $uom_name = $uomRow['unit_name'];
    }
}

// Resolve CGST / SGST / IGST split dynamically based on state code of GSTIN
$supplier_state = substr(preg_replace('/[^0-9]/', '', $supplier_gstin), 0, 2);
if (empty($supplier_state)) {
    $supplier_state = '08';
}
$subcontractor_gst = !empty($jc_data['sub_contractor']['gst_no']) ? trim($jc_data['sub_contractor']['gst_no']) : '';
$subcontractor_state = substr(preg_replace('/[^0-9]/', '', $subcontractor_gst), 0, 2);
if (empty($subcontractor_state)) {
    $subcontractor_state = '08';
}

$tax_rate = (float)$jc_data['job_challan_items'][0]['tax_rate'];
$tax_amount = (float)$jc_data['job_challan_items'][0]['tax_amount'];

$cgst_rate = '';
$cgst_amount = '';
$sgst_rate = '';
$sgst_amount = '';
$igst_rate = '';
$igst_amount = '';

if ($supplier_state === $subcontractor_state) {
    $cgst_rate = number_format($tax_rate / 2, 1, '.', '') . '%';
    $cgst_amount = number_format($tax_amount / 2, 2, '.', '');
    $sgst_rate = number_format($tax_rate / 2, 1, '.', '') . '%';
    $sgst_amount = number_format($tax_amount / 2, 2, '.', '');
    $igst_rate = '-';
    $igst_amount = '-';
} else {
    $cgst_rate = '-';
    $cgst_amount = '-';
    $sgst_rate = '-';
    $sgst_amount = '-';
    $igst_rate = number_format($tax_rate, 1, '.', '') . '%';
    $igst_amount = number_format($tax_amount, 2, '.', '');
}

// Style shortcuts (Adjusted for single-page budget)
$left_col_style = 'border-left: 1px solid black; font-size: 8.5px; line-height: 1.35;';
$right_col_style = 'border-right: 1px solid black; font-size: 8.5px; line-height: 1.35;';

$html = '
<table border="0" cellpadding="4" cellspacing="0" width="100%">
    <tr>
        <td width="70%" style="border-top: 1px solid black; border-left: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black; font-size: 10px; line-height: 1.35;">
            <b>ANNEXURE</b><br>
            <b>JOB CHALLAN</b><br>
            <span style="font-size: 7.5px;">(For Movement of Inputs or partially processed goods from one Factory to another Factory for further Processing/Operation)</span>
        </td>
        <td width="30%" style="border-top: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black; font-size: 9px; line-height: 1.35;">
            Original : Pink<br>
            Duplicate : Green<br>
            Triplicate : White<br><br>
            <b>S. L. No: ' . h($jc_data['challan_no']) . '</b>
        </td>
    </tr>
    <tr>
        <td width="40%" style="border-left: 1px solid black; border-bottom: 1px solid black; font-size: 9px; line-height: 1.35;">
            <b>Name and address of the Suppliers/Manufacturer:</b>
        </td>
        <td width="60%" style="border-right: 1px solid black; border-bottom: 1px solid black; font-size: 9px; line-height: 1.35;">
            ' . h($supplier_name) . '<br>
            ' . h($supplier_address) . '<br>
            <b>GSTIN:</b> ' . h($supplier_gstin) . ' &nbsp;&nbsp;&nbsp;&nbsp; <b>PAN No:</b> ' . h($supplier_pan) . '
        </td>
    </tr>
    <tr>
        <td colspan="2" align="center" style="background-color: #f2f2f2; border-left: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black; font-weight: bold; font-size: 9px; line-height: 1.25;">PART - I</td>
    </tr>
    <tr>
        <td width="60%" style="' . $left_col_style . '">1. Description of Goods</td>
        <td width="40%" style="' . $right_col_style . '">' . h($jc_data['job_challan_items'][0]['item_name']) . '</td>
    </tr>
    <tr>
        <td style="' . $left_col_style . '">2. Identification marks and numbers if any</td>
        <td style="' . $right_col_style . '">' . (h($jc_data['work_description']) && h($jc_data['work_description']) != 'test' ? h($jc_data['work_description']) : '02 Drum') . '</td>
    </tr>
    <tr>
        <td style="' . $left_col_style . '">3. Quantity (Nos./Weight/Litre/Metre)</td>
        <td style="' . $right_col_style . '">' . number_format((float)$jc_data['job_challan_items'][0]['quantity'], 2, '.', '') . ' ' . h($uom_name) . '</td>
    </tr>
    <tr>
        <td style="' . $left_col_style . '">4. HSN/SAC</td>
        <td style="' . $right_col_style . '">' . h($jc_data['job_challan_items'][0]['hsn_code']) . '</td>
    </tr>
    <tr>
        <td style="' . $left_col_style . '">5. Estimated value of inputs</td>
        <td style="' . $right_col_style . '">' . number_format((float)$jc_data->estimated_values, 2, '.', '') . '</td>
    </tr>
    <tr>
        <td style="' . $left_col_style . '" valign="top">6. GST<br>
            &nbsp;&nbsp;&nbsp;&nbsp;(A). CGST<br>
            &nbsp;&nbsp;&nbsp;&nbsp;(B). SGST<br>
            &nbsp;&nbsp;&nbsp;&nbsp;(C). IGST
        </td>
        <td style="' . $right_col_style . '" valign="top"><br>
            @ ' . h($cgst_rate) . ' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Rs. ' . h($cgst_amount) . '<br>
            @ ' . h($sgst_rate) . ' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Rs. ' . h($sgst_amount) . '<br>
            @ ' . h($igst_rate) . ' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Rs. ' . h($igst_amount) . '
        </td>
    </tr>
    <tr>
        <td style="' . $left_col_style . '">7. Total amount of GST</td>
        <td style="' . $right_col_style . '">Rs. ' . number_format((float)$jc_data['job_challan_items'][0]['tax_amount'], 2, '.', '') . '</td>
    </tr>
    <tr>
        <td style="' . $left_col_style . '">8. Date and time of issue</td>
        <td style="' . $right_col_style . '">' . date('d-m-Y', strtotime($jc_data->jc_date)) . ' ' . date('H:i \H\r\s', strtotime($jc_data->created)) . '</td>
    </tr>
    <tr>
        <td style="' . $left_col_style . '">9. Nature of processing/manufacturing required to be done</td>
        <td style="' . $right_col_style . '">' . ucwords(strtolower($jc_data->processing_type)) . '</td>
    </tr>
    <tr>
        <td style="' . $left_col_style . ' line-height: 1.3;">10. Factory/Place of processing/Manufacturing</td>
        <td style="' . $right_col_style . ' line-height: 1.3;"><b>' . ucwords(strtolower($jc_data['sub_contractor']['name'])) . '</b><br>' . nl2br(h($jc_data['sub_contractor']['address'])) . '<br><b>GSTIN:</b> ' . h($jc_data['sub_contractor']['gst_no']) . '</td>
    </tr>
    <tr>
        <td style="' . $left_col_style . '">11. Expected duration of processing/manufacturing</td>
        <td style="' . $right_col_style . '">' . h($jc_data->expected_days) . ' Day' . ($jc_data->expected_days > 1 ? 's' : '') . '</td>
    </tr>
    <tr>
        <td style="' . $left_col_style . '">12. Vehicle No.</td>
        <td style="' . $right_col_style . '">' . h($jc_data->vehicle_no) . '</td>
    </tr>
    <tr>
        <td colspan="2" style="border-left: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black; padding: 4px;">
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td width="55%" style="font-size: 8.5px; line-height: 1.35;">
                        <b>Place:</b> ' . h($supplier_city) . '<br>
                        <b>Date:</b> ' . date('d-m-Y', strtotime($jc_data->jc_date)) . '
                    </td>
                    <td width="45%" align="right" valign="bottom" height="35" style="font-size: 8px; line-height: 1.25;">
                        Signature of manufacturer/Authorised Signatory
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="2" align="center" style="background-color: #f2f2f2; border-left: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black; font-weight: bold; font-size: 9px; line-height: 1.25;">PART - II</td>
    </tr>
    <tr>
        <td width="60%" style="border-left: 1px solid black; font-size: 8px; line-height: 1.3;">1. Date and time of despatch of finished goods to parent factory/another manufacturer and entry No. and date of Factory receipt in processing</td>
        <td width="40%" style="border-right: 1px solid black; font-size: 8px; vertical-align: bottom;">........................................................................<br>........................................................................</td>
    </tr>
    <tr>
        <td style="border-left: 1px solid black; font-size: 8px; line-height: 1.3;">2. Quantity Despatched (Nos./Weight/Litre/Metre) and entered in Account</td>
        <td style="border-right: 1px solid black; font-size: 8px; vertical-align: bottom;">........................................................................</td>
    </tr>
    <tr>
        <td style="border-left: 1px solid black; font-size: 8px; line-height: 1.3;">3. Nature of processing/manufacturing done</td>
        <td style="border-right: 1px solid black; font-size: 8px; vertical-align: bottom;">........................................................................</td>
    </tr>
    <tr>
        <td style="border-left: 1px solid black; font-size: 8px; line-height: 1.3;">4. Quantity of waste material to be returned to the parent factory or cleared for home consumption. Invoice No. and date. Quantum of GST Paid (Both figures and words.)</td>
        <td style="border-right: 1px solid black; font-size: 8px; vertical-align: bottom;">........................................................................<br>........................................................................</td>
    </tr>
    <tr>
        <td colspan="2" style="border-left: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black; padding: 4px;">
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td width="55%" style="font-size: 8px; line-height: 1.35;">
                        <b>Place:</b> ............................<br>
                        <b>Date:</b> &nbsp;............................
                    </td>
                    <td width="45%" align="right" valign="bottom" height="35" style="font-size: 8px; line-height: 1.25;">
                        Signature of processor/Name of factory, Address
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="2" align="center" style="background-color: #f2f2f2; border-left: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black; font-weight: bold; font-size: 9px; line-height: 1.25;">PART - III</td>
    </tr>
    <tr>
        <td colspan="2" style="border-left: 1px solid black; border-right: 1px solid black; font-size: 8px; line-height: 1.4; text-align: justify;">
            <b>To be filled by parent factory in duplicate copy of challan on receipt of the same from the processing factory.</b><br>
            Certified that I/We have received the goods removed under the above challan, on .............................................. and have taken credit of the amount vide Entry No. .............................................. Dated ..............................................
        </td>
    </tr>
    <tr>
        <td colspan="2" style="border-left: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black; padding: 4px;">
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td width="55%" style="font-size: 8px; line-height: 1.35;">
                        <b>Place:</b> ............................<br>
                        <b>Date:</b> &nbsp;............................
                    </td>
                    <td width="45%" align="right" valign="bottom" height="35" style="font-size: 8px; line-height: 1.25;">
                        Signature of manufacturer/Authorised Signatory
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
';

$pdf->writeHTML($html, true, false, true, false, '');
ob_end_clean();
echo $pdf->Output('JC-' . $jc_data['challan_no'] . '-' . $date . '.pdf');
exit;
