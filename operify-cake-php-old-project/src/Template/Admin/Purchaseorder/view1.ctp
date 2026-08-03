<?php 

// Start output buffering
ob_start();

// Creating PDF
$pdf = new TCPDF('P', 'mm', 'A4');
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
if ($co != 0) {
  $amedmentdate = date('d-m-Y', strtotime($users['revised_date']));
}


$logo = WWW_ROOT . "/images/" . $site_details['small_logo'];
$address = $site_details['address1'];
$email = $site_details['email'];
$mobile = $site_details['phone'];
$website = $site_details['website'];
$gst_no = $site_details['gst_no'];
$pan_no = $site_details['pan_number'];
$school_name = $sitesetting['first_name'];
$temp = str_replace(array('{logo}',  '{school_name}', '{address}', '{mobile}', '{email}', '{website}', '{supliername}', '{supaddress}', '{vendorgst}', '{supplierstate}', '{supcontact}','{supemail}','{purchaseorder_id}','{podate}','{delivery_date}'), array($logo,  $school_name, $address, $mobile, $email, $website, $supliername,nl2br($sup['address']),$vendorshipfrom['gst_number'],$sup['state']['name'],$sup['contact_no'],$sup['email'], $users['purchaseorder_id'],$podate,$delivery_date), $template['body']);

// pr($temp);die;
// Prepare HTML content and decode any HTML entities
$html = html_entity_decode($temp ); // Decodes the HTML entities

// echo $html;
// exit;
// Writing HTML content to PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Clean the buffer to avoid sending any unwanted output before PDF
ob_end_clean();

// Final Output
$pdf->Output('test.pdf', 'I');
exit;
?>
