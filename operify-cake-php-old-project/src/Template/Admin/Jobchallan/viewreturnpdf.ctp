<?php
class xtcpdf extends TCPDF {}

$pdf = new TCPDF('P', 'mm', 'A4');

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->AddPage();

// Set margins: 5mm margins for maximum printable width and height (consistent with single-page layout)
$pdf->SetMargins(5, 5, 5);
$pdf->SetAutoPageBreak(TRUE, 5);

// Set base font size to 9.0 for readable visibility on a single page
$pdf->SetFont('', '', 9, '', 'true');
TCPDF_FONTS::addTTFfont('../Devanagari/Devanagari.ttf', 'TrueTypeUnicode', "", 32);

$date = date("d-m-Y");

// Resolve To Company (Original JC Issuer) details from sitesettings and sitesettings_details
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

// Resolve From Company (Subcontractor) details
$subcontractor_name = !empty($jc_data['sub_contractor']['name']) ? trim($jc_data['sub_contractor']['name']) : '';
$subcontractor_address = !empty($jc_data['sub_contractor']['address']) ? trim($jc_data['sub_contractor']['address']) : '';
$subcontractor_gst = !empty($jc_data['sub_contractor']['gst_no']) ? trim($jc_data['sub_contractor']['gst_no']) : '';

// Resolve dynamic unit of measurement (UOM)
$uom_name = 'KG';
if (!empty($return_data->item_id)) {
    $conn = \Cake\Datasource\ConnectionManager::get('default');
    $uomRow = $conn->execute("
        SELECT m.unit_name 
        FROM st_additem a 
        LEFT JOIN st_measurementunits m ON a.uom = m.id 
        WHERE a.id = :id 
        LIMIT 1
    ", ['id' => $return_data->item_id])->fetch('assoc');
    if ($uomRow && !empty($uomRow['unit_name'])) {
        $uom_name = $uomRow['unit_name'];
    }
}

// Resolve CGST / SGST / IGST split dynamically based on state code of GSTIN
$supplier_state = substr(preg_replace('/[^0-9]/', '', $supplier_gstin), 0, 2);
if (empty($supplier_state)) {
    $supplier_state = '08';
}
$subcontractor_state = substr(preg_replace('/[^0-9]/', '', $subcontractor_gst), 0, 2);
if (empty($subcontractor_state)) {
    $subcontractor_state = '08';
}

$tax_rate = (float)$return_data->tax_rate;
$tax_amount = (float)$return_data->tax_amount;
$rate = (float)$return_data->rate;
$qty = (float)$return_data->received_qty;
$job_work_charges = $qty * $rate;
$total_amount = $return_data->amount ? (float)$return_data->amount : ($job_work_charges + $tax_amount);

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

$html = '
<table border="0" cellpadding="4" cellspacing="0" width="100%">
    <tr>
        <td width="70%" style="border-top: 1px solid black; border-left: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black; font-size: 10px; line-height: 1.35;">
            <b>GSTIN: ' . h($subcontractor_gst) . '</b><br>
            <b>JOB WORK CHALLAN</b><br>
            <span style="font-size: 7.5px;">Subsidiary Challan For return of Goods in Piecemeals received</span>
        </td>
        <td width="30%" style="border-top: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black; font-size: 9px; line-height: 1.35;">
            Original for Recipient-White<br>
            Duplicate for Transporter-Pink<br>
            Triplicate for Supplier-Green<br>
        </td>
    </tr>
    <tr>
        <td colspan="2" align="center" style="border-left: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black; font-size: 13px; line-height: 1.4; font-weight: bold;">
            ' . h($subcontractor_name) . '<br>
            <span style="font-size: 9px; font-weight: normal;">' . h($subcontractor_address) . '</span>
        </td>
    </tr>
    <tr>
        <td width="50%" style="border-left: 1px solid black; border-bottom: 1px solid black; font-size: 9px; line-height: 1.35;">
            <b>Challan No:</b> RC-' . h($return_data->id) . '
        </td>
        <td width="50%" style="border-right: 1px solid black; border-bottom: 1px solid black; font-size: 9px; line-height: 1.35; text-align: right;">
            <b>Date:</b> ' . date('d-m-Y', strtotime($return_data->receive_date)) . ' &nbsp;&nbsp;&nbsp;&nbsp;
        </td>
    </tr>
    <tr>
        <td width="55%" style="border-left: 1px solid black; border-bottom: 1px solid black; font-size: 9px; line-height: 1.35;">
            <b>M/s.</b> ' . h($supplier_name) . '<br>
            ' . h($supplier_address) . '<br>
            <b>State:</b> Rajasthan &nbsp;&nbsp;&nbsp;&nbsp; <b>State Code:</b> ' . h($supplier_state) . '<br>
            <b>GSTIN:</b> ' . h($supplier_gstin) . '
        </td>
        <td width="45%" style="border-right: 1px solid black; border-bottom: 1px solid black; font-size: 9px; line-height: 1.35;">
            <b>JAIPUR TO:</b> ' . h($supplier_city) . '<br>
            <b>By:</b> .............................................................<br>
            <b>Vehicle No.:</b> ' . h($return_data->vehicle_no) . '<br>
            <b>G.R. No.:</b> .................... <b>Date:</b> ................
        </td>
    </tr>
    <tr style="background-color: #f2f2f2; font-weight: bold; font-size: 9px; text-align: center;">
        <td width="5%" style="border-left: 1px solid black; border-bottom: 1px solid black;">S.No.</td>
        <td width="45%" style="border-left: 1px solid black; border-bottom: 1px solid black;">DESCRIPTION OF GOODS</td>
        <td width="12%" style="border-left: 1px solid black; border-bottom: 1px solid black;">BDLS./<br>Packages</td>
        <td width="13%" style="border-left: 1px solid black; border-bottom: 1px solid black;">HSN/SAC<br>Code</td>
        <td width="10%" style="border-left: 1px solid black; border-bottom: 1px solid black;">SIZE<br>GAUGE</td>
        <td width="15%" style="border-left: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black;">QUANTITY<br>Net Weight</td>
    </tr>
    <tr>
        <td style="border-left: 1px solid black; border-bottom: 1px solid black; font-size: 8.5px; height: 180px; text-align: center;" valign="top">1.</td>
        <td style="border-left: 1px solid black; border-bottom: 1px solid black; font-size: 8.5px;" valign="top">
            <b>' . h($return_data->additem->item_name) . '</b><br>
            JC No: ' . h($jc_data->challan_no) . '<br><br>
            CGST &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Rs. ' . h($cgst_amount) . '<br>
            SGST &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Rs. ' . h($sgst_amount) . '<br>
            IGST &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Rs. ' . h($igst_amount) . '
        </td>
        <td style="border-left: 1px solid black; border-bottom: 1px solid black; font-size: 8.5px; text-align: center;" valign="top">
            ' . (h($return_data->remarks) ? h($return_data->remarks) : '01 Box') . '
        </td>
        <td style="border-left: 1px solid black; border-bottom: 1px solid black; font-size: 8.5px; text-align: center;" valign="top">
            ' . h($return_data->additem->hsn_code) . '
        </td>
        <td style="border-left: 1px solid black; border-bottom: 1px solid black; font-size: 8.5px; text-align: center;" valign="top">-</td>
        <td style="border-left: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black; font-size: 8.5px; text-align: center; font-weight: bold;" valign="top">
            ' . number_format($qty, 2, '.', '') . ' ' . h($uom_name) . '
        </td>
    </tr>
    <tr>
        <td colspan="4" style="border-left: 1px solid black; border-bottom: 1px solid black; font-size: 8.5px; padding: 5px;" valign="top">
            <b>Conversion A/c Not For Sale</b>
            <table border="0" cellpadding="2" cellspacing="0" width="100%" style="font-size: 8px;">
                <tr>
                    <td width="50%">Description: ' . h($return_data->additem->item_name) . '</td>
                    <td width="30%">Challan No: ' . h($jc_data->challan_no) . '</td>
                    <td width="20%">Qty: ' . number_format($qty, 2, '.', '') . '</td>
                </tr>
            </table>
        </td>
        <td colspan="2" style="border-left: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black; padding: 0px;" valign="top">
            <table border="0" cellpadding="4" cellspacing="0" width="100%" style="font-size: 8.5px;">
                <tr>
                    <td width="60%" style="border-bottom: 1px solid #ddd;">(A) Job Work Charges<br><span style="font-size: 7.5px;">' . number_format($qty, 2, '.', '') . ' ' . h($uom_name) . ' @ ' . number_format($rate, 2, '.', '') . '</span></td>
                    <td width="40%" style="border-left: 1px solid black; border-bottom: 1px solid #ddd; text-align: right;">' . number_format($job_work_charges, 2, '.', '') . '</td>
                </tr>
                <tr>
                    <td style="border-bottom: 1px solid #ddd;">(B) GST @ ' . h($tax_rate) . '%</td>
                    <td style="border-left: 1px solid black; border-bottom: 1px solid #ddd; text-align: right;">' . number_format($tax_amount, 2, '.', '') . '</td>
                </tr>
                <tr>
                    <td style="border-bottom: 1px solid #ddd;">(C) GST Amount</td>
                    <td style="border-left: 1px solid black; border-bottom: 1px solid #ddd; text-align: right;">' . number_format($tax_amount, 2, '.', '') . '</td>
                </tr>
                <tr style="font-weight: bold; background-color: #f9f9f9;">
                    <td>Total Amount (A+C)</td>
                    <td style="border-left: 1px solid black; text-align: right;">' . number_format($total_amount, 2, '.', '') . '</td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="6" style="border-left: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black; padding: 5px;">
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td width="50%" style="font-size: 8px; line-height: 1.4;">
                        Received in Order & good Condition.<br><br>
                        <b>Receiver\'s Signature:</b> ....................................................
                    </td>
                    <td width="50%" align="right" valign="bottom" height="40" style="font-size: 8.5px; line-height: 1.3;">
                        For <b>' . h($subcontractor_name) . '</b><br><br><br>
                        Authorised Signatory
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
';

$pdf->writeHTML($html, true, false, true, false, '');
ob_end_clean();
echo $pdf->Output('Return-Challan-RC-' . $return_data->id . '-' . $date . '.pdf');
exit;
