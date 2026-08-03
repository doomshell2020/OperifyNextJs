<?php 
class xtcpdf extends TCPDF {
}

   $this->set('pdf', new TCPDF('P','mm','A4'));
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);

// set document information

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->AddPage();
//$pdf->setHeaderMargin(0);

// set margins
//$//pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
 $pdf->SetAutoPageBreak(TRUE, 20);
//$pdf->SetMargins(0, 25, 0, true);

$pdf->SetFont('', '', 8, '', 'true');
TCPDF_FONTS::addTTFfont('../Devanagari/Devanagari.ttf', 'TrueTypeUnicode', "", 32);

$logo = WWW_ROOT . "/images/" . $site_details['small_logo'];
$address  =  $site_details['address1'];
$email  =  $site_details['email'];
$mobile  =  $site_details['phone'];
$website  =  $site_details['website'];
$gst_no  =  $site_details['gst_no'];
$pan_no  =  $site_details['pan_number'];
$school_name  =  $sitesetting['first_name'];


$html .='


<table border="1px">
  <tr>
    <td>
      <table width="100%" style="padding: 1px 1px 0px 0px;" align="left">
      <tbody>
         <tr>
            <td style="text-align:left" width="50%">
               &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="'.$logo.'" alt="" border="0" style="display:block;" height="62px;"><br>
               <span style="display:block; color:#000; font-size:10px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>'.$school_name.'</b></span>
            </td>
            <td style="text-align:left;" width="50%" align="right">
            '.$address.'<br>
               <b>Phone</b>
               :'.$mobile.'<br>&nbsp;&nbsp;&nbsp;&nbsp;<b>Email</b>
               : <u>
               '.$email.'</u><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Website</b> :&nbsp;'.$website.'
            </td>
         </tr>
      </tbody>
      </table>
      <h3 style="text-align:center;font-size:10px; border-top:1px solid #000;">Last Purchase History</h3>
      ';

  $itemname = $this->Comman->getitemname($itemdetails[0]['item_id']);
  $html .='
      <table cellspacing="0" cellpadding="3" border="1px" style="font-size:8px;">
      <thead>
        <tr>
        <td colspan="4" width="73%"><strong>Item Name:-</strong>'.$itemname['item_name'].'</td>
        <td colspan="1" width="27%"><strong>Print Date:-</strong>'.date("d-m-Y").'</td>
        </tr>
        <tr>
        <th width="12%" ><strong>PO No</strong></th>
        <th width="12%" ><strong>PO Date</strong></th>
        <th width="49%" ><strong>Supplier</strong></th>
        <th width="12%" ><strong>Quantity</strong></th>
        <th width="15%" ><strong>Price</strong></th>
        </tr>
      </thead>
  
      <tbody>';
  
       if ($itemdetails) {
            foreach ($itemdetails as $item) {
              $vendorName = $this->Comman->findvendornames($item['vendor_id']);
            $html .='
            <tr>
              <th width="12%" >'.$item['purchaseorder_id'].'</th>
              <td width="12%" >'.date('d-m-Y', strtotime($item['inward_date'])).'</td>
              <td width="49%" >'.$vendorName['name'].'</td>
              <td width="12%" style ="text-align:right;">'.sprintf('%.2f', $item['item_qty']).'</td>
              <td width="15%" style ="text-align:right;">'.sprintf('%.2f', $item['item_amt']).'</td>
              </tr>';
      }
      $html .='
        </tbody>';
  
      } else {
        $html .='
        <tr style="text-align:center;">
        <th colspan="5">
          No Record Found.
        </th>
      </tr>';
      }
      $html .='
      </table>
    </td>
  </tr>
</table>';



   


    $date = date('d-m-y');
$pdf->writeHTMLCell(0, 0, '', '', utf8_encode($html), 0, 1, 0, true, '', true);
//$pdf->WriteHTML($html, true, false, true, false, '');
ob_end_clean();
echo $pdf->Output('Item_Purchase_Details'. $date.'.pdf');
exit;
?>