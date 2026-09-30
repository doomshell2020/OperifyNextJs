<?php
class xtcpdf extends TCPDF {}
$pdf = new TCPDF('L', 'mm', 'A5');

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->AddPage();

// Set margins
$pdf->SetMargins(5, 5, 5);
$pdf->SetAutoPageBreak(TRUE, 15);

// Set base font size to 8.0 for readable visibility
$pdf->SetFont('', '', 8, '', 'true');
TCPDF_FONTS::addTTFfont('../Devanagari/Devanagari.ttf', 'TrueTypeUnicode', "", 32);

$date = date("d-m-Y");

// Resolve dynamic supplier details
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
if (!empty($sitesetting)) {
    $supplier_address = !empty($sitesetting->address) ? trim($sitesetting->address) : '';
}
if (empty($supplier_address) && !empty($site_details)) {
    $supplier_address = !empty($site_details->address) ? trim($site_details->address) : '';
}
if (empty($supplier_address)) {
    $supplier_address = 'B-141 A, ROAD NO 9 D, V K I AREA, Jaipur, Rajasthan';
}

$logo = WWW_ROOT . "images" . DS . (isset($site_details->small_logo) ? $site_details->small_logo : '');
$address = isset($site_details->address1) ? $site_details->address1 : '';
$email = isset($site_details->email) ? $site_details->email : '';
$mobile = isset($site_details->phone) ? $site_details->phone : '';
$website = isset($site_details->website) ? $site_details->website : '';
$school_name = isset($sitesetting->first_name) ? $sitesetting->first_name : (isset($site_details->company_name) ? $site_details->company_name : 'TIRUPATI PLASTOMATICS PVT. LTD.');

// Extract single JC No if applicable for legacy gatepasses
$single_jc = '';
if (!empty($gatepass->jc_id)) {
    $jc_ids = explode(',', $gatepass->jc_id);
    if (count($jc_ids) == 1 && $gatepass->has('job_challan')) {
        $single_jc = $gatepass->job_challan->challan_no;
    }
} else if ($gatepass->has('job_challan')) {
    $single_jc = $gatepass->job_challan->challan_no;
}

// Format items
$items_html = '';
$sl_no = 1;
if (!empty($gatepass->gatepass_items)) {
    foreach ($gatepass->gatepass_items as $item) {
        $desc = h($item->description);
        if ($single_jc && strpos($desc, 'JC No') === false) {
            $desc .= ($desc ? "\n" : '') . 'JC No: ' . $single_jc;
        }

        $items_html .= '
        <tr>
            <td width="6%" align="center" style="border-right: 1px solid #000;">' . $sl_no . '</td>
            <td width="14%" align="center" style="border-right: 1px solid #000; font-weight:bold;">' . h($item->item_id) . '</td>
            <td width="40%" align="left" style="border-right: 1px solid #000;"><strong>' . ($item->has('additem') ? h($item->additem->item_name) : '') . '</strong>' . (!empty($desc) ? '<br><span>' . nl2br($desc) . '</span>' : '') . '</td>
            <td width="10%" align="center" style="border-right: 1px solid #000; font-weight:bold;">' . h($item->quantity) . '</td>
            <td width="10%" align="center" style="border-right: 1px solid #000; font-weight:bold;">' . h($item->unit) . '</td>
            <td width="20%" align="left" style="font-weight:bold;">' . nl2br(h($item->remarks)) . '</td>
        </tr>';
        $sl_no++;
    }
}

// Remarks combination
$remarks = h($gatepass->remarks);
if (!empty($gatepass->jc_id)) {
    $jc_ids = explode(',', $gatepass->jc_id);
    if (count($jc_ids) > 1) {
        $jcs = \Cake\ORM\TableRegistry::get('JobChallans')->find('all')->where(['id IN' => $jc_ids])->extract('challan_no')->toArray();
        if ($jcs) {
            if ($remarks != '') $remarks .= '<br>';
            $remarks .= 'JC No: ' . implode(', ', $jcs);
        }
    } else {
        if ($gatepass->has('job_challan')) {
            if ($remarks != '') $remarks .= '<br>';
            $remarks .= 'JC No: ' . h($gatepass->job_challan->challan_no);
        }
    }
} else {
    if ($gatepass->has('job_challan')) {
        if ($remarks != '') $remarks .= '<br>';
        $remarks .= 'JC No: ' . h($gatepass->job_challan->challan_no);
    }
}

$html = '
<table width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td style="border: 1px solid #000; border-bottom: none; padding: 10px;">
            <table width="100%" style="padding: 1px 1px 0px 0px;" align="left">
               <tbody>
                  <tr>
                     <td style="text-align:left" width="50%">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="' . $logo . '" alt="" border="0" style="display:block;" height="62px;"><br>
                        <span style="display:block; color:#000; font-size:10px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>' . h($school_name) . '</b></span>
                     </td>
                     <td style="text-align:left;" width="50%" align="right">
                     ' . h($address) . '<br>
                        <b>Phone</b>
                        :' . h($mobile) . '<br>&nbsp;&nbsp;&nbsp;&nbsp;<b>Email</b>
                        : <u>
                        ' . h($email) . '</u><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Website</b> :&nbsp;' . h($website) . '
                     </td>
                  </tr>
               </tbody>
            </table>
            <h3 style="text-align:center;font-size:10px; letter-spacing: 0.5px; border-top:1px solid #000; border-bottom:1px solid #000; margin: 15px 0 15px 0;">RETURNABLE GATE PASS</h3>
            
            <table width="100%" cellpadding="3" cellspacing="0" style="margin-top: 5px;">
                <tr>
                    <td width="10%">No.</td>
                    <td width="50%"><b>' . h($gatepass->gatepass_no) . '</b></td>
                    <td width="15%" align="right">Date </td>
                    <td width="25%"><b>' . (h($gatepass->date) ? date('d/m/Y', strtotime($gatepass->date)) : '') . '</b></td>
                </tr>
            </table>

            <table width="100%" cellpadding="3" cellspacing="0">
                <tr>
                    <td width="10%">M/s.</td>
                    <td width="90%">
                        ' . ($gatepass->has('sub_contractor') ? h($gatepass->sub_contractor->name) : '') . '
                    </td>
                </tr>
            </table>

            <table width="100%" cellpadding="3" cellspacing="0">
                <tr>
                    <td width="10%"></td>
                    <td width="90%" style="height:20px;">
                        ' . ($gatepass->has('sub_contractor') ? h($gatepass->sub_contractor->address) : '') . '
                    </td>
                </tr>
            </table>

            <table width="100%" cellpadding="3" cellspacing="0">
                <tr>
                    <td width="12%">Through</td>
                    <td width="48%">' . h($gatepass->vehicle_no) . '</td>
                    <td width="15%" align="right">Date of Return </td>
                    <td width="25%">' . (h($gatepass->return_date) ? date('d/m/Y', strtotime($gatepass->return_date)) : '') . '</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<table width="100%" cellpadding="3" cellspacing="0" style="border-left: 1px solid #000; border-right: 1px solid #000;">
    <thead>
        <tr>
            <th width="6%" align="center" style="border-top: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000; font-weight:bold;">S.No.</th>
            <th width="14%" align="center" style="border-top: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000; font-weight:bold;">Item Code</th>
            <th width="40%" align="center" style="border-top: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000; font-weight:bold;">Description of Item</th>
            <th width="10%" align="center" style="border-top: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000; font-weight:bold;">Qty.</th>
            <th width="10%" align="center" style="border-top: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000; font-weight:bold;">Unit</th>
            <th width="20%" align="center" style="border-top: 1px solid #000; border-bottom: 1px solid #000; font-weight:bold;">Remarks</th>
        </tr>
    </thead>
    <tbody>
        ' . $items_html . '
    </tbody>
</table>

<table width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td style="border: 1px solid #000; border-top: 1px solid #000; padding: 10px;">
            <table width="100%" cellpadding="5" cellspacing="0">
                <tr>
                    <td width="33%" align="left"><br><br><br>Head Store</td>
                    <td width="33%" align="center"><br><br><br>Authorised Signatory</td>
                    <td width="33%" align="right"><br><br><br>Received by</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
';

$pdf->writeHTML($html, true, false, true, false, '');
ob_end_clean();
echo $pdf->Output($gatepass->gatepass_no . '.pdf', 'I');
exit;
