<?php
class xtcpdf extends TCPDF {}

$this->set('pdf', new TCPDF('P', 'mm', 'A4'));
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



function formatCurrency($amount)
{
  return ($amount == floor($amount))
    ? number_format($amount, 0, '.', ',') // no decimals if .00
    : number_format($amount, 2, '.', ','); // 2 decimals if not .00
}

function formatDecimal($value)
{
  $value = floatval($value);
  if (floor($value) == $value) {
    return (string)(int)$value;
  }
  return number_format($value, 2, '.', '');
}


// pr($designs);die;
$logo = WWW_ROOT . "/images/" . $site_details['small_logo'];
$address = $site_details['address1'];
$email = $site_details['email'];
$mobile = $site_details['phone'];
$website = $site_details['website'];
$gst_no = $site_details['gst_no'];
$pan_no = $site_details['pan_number'];
$school_name = $sitesetting['first_name'];

$checkgrn = $this->Comman->checkgrn($users['purchaseorder_id'], $users['id']);

$vendorshipfroms = $this->Comman->vendorshipfromdetail($users['vendor_id']);
$vendorshipfrom = $this->Comman->vendorgst($users['vendor_id']);


$postatus = $this->Comman->findgoodsrecivied($users['purchaseorder_id']);

$vendorbilltodetail = $this->Comman->vendorbilltodetail($users['vendor_id']);
$podate = date('d-m-Y', strtotime($users['added_time']));

$delivery_date = date('d-m-Y', strtotime($users['delivery_date']));
$supliername = $sup['name'];

if ($co != 0) {
  $amedmentdate = date('d-m-Y', strtotime($users['revised_date']));
}

if ($users['postatus'] == 'O') {
  $status = "Open";
} else {
  $status = "Close";
}

if ($co != 0) {
  $amedmentdate = $co . '&nbsp;(<b>Date : </b>' . $amedmentdate . ' )';
} else {
  $amedmentdate = '---';
}


$s = 1;
$html .= '

<table border="1px">
  <tr>
    <td>
      <table width="100%" style="padding: 1px 1px 0px 0px;" align="left">
               <tbody>
                  <tr>
                     <td style="text-align:left" width="50%">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="' . $logo . '" alt="" border="0" style="display:block;" height="62px;"><br>
                        <span style="display:block; color:#000; font-size:10px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>' . $school_name . '</b></span>
                     </td>
                     <td style="text-align:left;" width="50%" align="right">
                     ' . $address . '<br>
                        <b>Phone</b>
                        :' . $mobile . '<br>&nbsp;&nbsp;&nbsp;&nbsp;<b>Email</b>
                        : <u>
                        ' . $email . '</u><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Website</b> :&nbsp;' . $website . '
                     </td>
                  </tr>
               </tbody>
            </table>
      <h3 style="text-align:center;font-size:10px; border-top:1px solid #000; border-bottom:1px solid #000;">Purchase Order Details</h3>
      <table cellspacing="0" cellpadding="5" border="0px" style="font-size:8px;">
        <thead>
      
        <tr>
          <td><b>Purchase Order No. :-</b>
           ' . $users['purchaseorder_id'] . '
          </td>
          <td><b>Amendment No :-</b>
           ' . $amedmentdate . '
          </td>
        </tr>
        <tr>
          <td><b>Purchase Order Date :-</b>
           ' . $podate . '
          </td>
          <td><b>Delivery Date :-</b>
           ' . date("d-m-Y", strtotime($delivery_date)) . '
          </td>
        </tr>
        <tr>
          <td><b>GSTIN NO. :-</b>
           ' . $vendorshipfrom['gst_number'] . '
          </td>
          <td><b>Vendor Name :-</b>
           ' . $supliername . '
          </td>
        </tr>
        <tr>
          <td><b>Status :-</b>
           ' . $status . '
          </td>
        </tr>
        </thead>
      
        </table>
    </td>
  </tr>
</table>';

// for po
$html .= '
<h6 style="text-align:center;font-size:10px;">Products</h6>
<table cellspacing="0" cellpadding="3" border="1px" style="font-size:8px;">
    <thead>
      <tr>
      <th width="04%"><strong>No.</strong></th>
      <th width="35%"><strong>Item</strong></th>
      <th width="10%"><strong>Order Qty</strong></th>
      <th width="10%"><strong>Pending Qty</strong></th>
      <th width="07%"><strong>Rate.</strong></th>
      <th width="10%"><strong>Price (INR)</strong></th>
      <th width="05%"><strong>Tax</strong></th>
      <th width="09%"><strong>Tax Amt</strong></th>
      <th width="10%"><strong>Amount</strong></th>
      </tr>
    </thead>

    <tbody>';
foreach ($puritems as $value) {
  $PurchaseDetails = $this->Comman->PurchaseOrderDetails($value['po_id'], $value['item_id']);

  $qty = $this->Comman->stockregisteritems($value['purchaseorder_id'], $value['item_id']);
  $result = ['sum' => round($qty->sum, 2)];
  $gettaxparent = $this->Comman->gettaxnameparent($value['tax_id']);
  if (empty($gettaxparent)) {
    $gettaxparent = $this->Comman->gettaxname2($value['tax_id']);
  }

  $i = 0;
  $taxx = '';

  foreach ($gettaxparent as $hh => $ty) {
    $taxx .= $ty['tax'];
    $i++;
  }
  $taxx = ($taxx) ? $taxx : '0';
  if ($i == 2) {
    $taaxx = $value['tax'] / $i;
    $taxxs = number_format((float) $taaxx, 2, '.', '') . " &nbsp;" . number_format((float) $taaxx, 2, '.', '');
  } else {
    $taxxs = number_format((float) $value['tax'], 2, '.', '');
  }
  $costprice = $value['item_qty'] * $value['item_amt'];
  if ($checkgrn) {
    $pendingqty = $value['item_qty'] - $result['sum'];
  } else {
    $pendingqty = $value['item_qty'];
  };
  $html .= '
          <tr>
          <td  width="04%">' . $s . '</td>
          <td  width="35%">' . Ucfirst(($value['additem']['item_name'])) . '</td>
          <td  width="10%">' . $value['item_qty'] . ' ' . $value['uom'] . '</td>
          <td  width="10%">' . formatCurrency($pendingqty) . ' ' . $value['uom'] . '</td>
          <td  width="07%" style="text-align:right;">' . formatCurrency($value['item_amt']) . '</td>
          <td  width="10%" style="text-align:right;">' . formatCurrency($costprice) . '</td>
          <td  width="05%">' . $taxx . '%' . '</td>
          <td  width="09%" style="text-align:right;">' . formatCurrency($value['item_tax_amt']) . '</td>
          <td  width="10%" style="text-align:right;">' . formatCurrency($value['item_total_amount']) . '</td>
          </tr>';
  $s++;
  $totalamaunt += $value['item_total_amount'];
  if ($costprice == $value['item_total_amount']) {
    $taxstatus = 'Tax Included';
  } else {
    $taxstatus = 'Tax Excluded';
  }
}
$html .= '
<tr>
<td colspan="5" style="text-align:right;"><b></b></td>

 <td colspan="4" style="text-align:right;"><b>Total Amount : </b>
' . formatCurrency($totalamaunt) . '
</td>
</tr>
</tbody>
</table>';


// for grn
if ($checkgrn) {
  $html .= '
<h6 style="text-align:center;font-size:10px;">Goods Received Note</h6>';
  $grnDetails = $this->Comman->findgoodsrecivied($users['purchaseorder_id']);
  foreach ($grnDetails as $grnvalue) {

    $html .= '

  <table cellspacing="0" cellpadding="3" border="1px" style="font-size:8px;margin-bottom:20px; display:table;">

  <thead>
  <tr>
  <td width="54.64%"><b>GRN No. :-</b>
   ' . $grnvalue['id'] . '
  </td>
  <td width="45.36%"><b>Bill No :-</b>
  ' . $grnvalue['bill_no'] . '
  </td>
</tr>
<tr>
<td width="54.64%"><b>Inward Date :-</b>
   ' . date("d-m-Y", strtotime($grnvalue['inwarddate'])) . '
  </td>
  <td width="45.36%"><b>Bill Date :-</b>
   ' . date("d-m-Y", strtotime($grnvalue['inwarddate'])) . '
  </td>
</tr>
  </thead>

  <tbody>
  <tr>
      <th width="04.00%"><strong>No.</strong></th>
      <th width="29.12%"><strong>Item</strong></th>
      <th width="09.76%"><strong>Order Qty</strong></th>
      <th width="11.76%"><strong>Received Qty</strong></th>
      <th width="09.6%"><strong>Rate</strong></th>
      <th width="11.96%"><strong>Price (INR)</strong></th>
      <th width="04.60%"><strong>Tax</strong></th>
      <th width="09.60%"><strong>Tax Amt</strong></th>
      <th width="09.60%"><strong>Amount</strong></th>
  </tr>';
    $stockDetails = $this->Comman->findstock($grnvalue['id']);
    // pr($stockDetails);die;
    $z = 1;
    foreach ($stockDetails as $value) {
      $getpo = $this->Comman->getpostockitem($value['po_id'], $value['item_id']);

      if ($value['additem']['measurementunit']['unit_name']) {
        $uom = $value['additem']['measurementunit']['unit_name'];
      } else {
        $uom = '--';
      }


      $gettaxparent = $this->Comman->gettaxnameparent($value['tax_id']);
      if (empty($gettaxparent)) {
        $gettaxparent = $this->Comman->gettaxname2($value['tax_id']);
      }
      $i = 0;
      $taxx = '';
      foreach ($gettaxparent as $hh => $ty) {
        $taxx .= $ty['tax'] . '%';
        $i++;
      }

      if ($i == 2) {
        $taaxx = $value['tax'] / $i;
        $taxxs = number_format((float) $taaxx, 2, '.', '') . " &nbsp;" . number_format((float) $taaxx, 2, '.', '');
      } else {
        $taxxs = number_format((float) $value['tax'], 2, '.', '');
      };


      $html .= '
<tr>
      <td width="04.00%">' . $z . '</td>
      <td width="29.12%">' . Ucfirst(($value['additem']['item_name'])) . '</td>
      <td width="09.76%">' . $getpo['item_qty'] . ' ' . $uom . '</td>
      <td width="11.76%">' . $value['quantity'] . ' ' . $uom . '.</td>
      <td width="09.6%" style="text-align:right;">' . formatCurrency($value['rate']) . '</td>
      <td width="11.96%" style="text-align:right;">' . formatCurrency($value['cost_price']) . '</td>
      <td width="04.60%">' . $taxx . '</td>
      <td width="09.60%">' . $taxxs . '</td>
      <td width="09.60%" style="text-align:right;">' . formatCurrency($value['amount']) . '</td>
</tr>


';
      $z++;
      $totalamaunt1 += $value['amount'];
    }

    $html .= '
<tr>
<td colspan="5" style="text-align:right;"><b></b></td>

 <td colspan="4" style="text-align:right;"><b>Total Amount : </b>
' . formatCurrency($totalamaunt1) . '
</td>
</tr>
</tbody>
</table>
<p> </p>
';
    $totalamaunt1 = '';
  }
}



// for delivery schedule
$getDeliverydates = $this->Comman->getDeliverydates($users['id']);
if ($getDeliverydates) {
  $html .= '
  <h6 style="text-align:center;font-size:10px;">Delivery Schedule</h6>';

  $grnDetails = $this->Comman->findgoodsrecivied($users['purchaseorder_id']);
  $width = 35 / count($getDeliverydates);

  $html .= '
  <table cellspacing="0" cellpadding="3" border="1" style="font-size:8px;margin-bottom:20px; display:table;">
  <thead>
      <tr>
          <th width = "30%"><strong>Item</strong></th>';
  foreach ($getDeliverydates as $dates) {
    $html .= '
          <th width ="' . $width . '%"><strong>DATE</strong></th>
          <th width ="' . $width . '%"><strong>QTY</strong></th>';
  }

  $html .= '
      </tr>
  </thead>
  <tbody>';

  foreach ($puritems as $value) {
    $itemname = $this->Comman->getitemname($value['item_id']);
    $html .= '
      <tr>
          <td width = "30%">' . ucfirst($itemname['item_name']) . '</td>';

    $td = 0;
    foreach ($getDeliverydates as $dates) {
      $getitemqty = $this->Comman->DeliveritemQty($value['item_id'], $users['id'], date('Y-m-d', strtotime($dates['delivery_date'])));
      $delivery_date = date('d-m-Y', strtotime($dates['delivery_date']));
      $qty = $getitemqty['item_qty'] ? $getitemqty['item_qty'] : 0;
      $uom = $this->Comman->getitemcatcom($value['item_id']);

      if ($qty != 0) {;
        $html .= '
        <td width = "' . $width . '%" style=" border:1px solid #000; color:#000; height:12px !important; line-height:9px; font-size:8px;  text-align:center;"> ' . $delivery_date . ' &nbsp; </td>
        <td width = "' . $width . '%" style=" border:1px solid #000; color:#000; height:12px !important; line-height:9px; font-size:8px; text-align:center;"> ' . $qty . ' ' . $uom['measurementunit']['unit_name'] . '</td> ';
      } else {
        $td++;
      }
    }

    if ($td > 0) {
      for ($dk = 0; $dk < $td; $dk++) {;
        $html .= '
      <td width = "' . $width . '%" style=" border:1px solid #000; color:#000; height:12px !important; line-height:9px; font-size:8px;  text-align:center;"></td>
      <td width = "' . $width . '%" style=" border:1px solid #000; color:#000; height:12px !important; line-height:9px; font-size:8px; text-align:center;"></td> ';
      }
    }

    $html .= '
      </tr>';
  }

  $html .= '
  </tbody>
  </table>';
}

// echo $html;die;
$pdf->writeHTMLCell(0, 0, '', '', utf8_encode($html), 0, 1, 0, true, '', true);
//$pdf->WriteHTML($html, true, false, true, false, '');
ob_end_clean();
// !empty($unitname) ? $unitname : $empty
//<td width="20%" >'.empty($value['additem']['measurementunit']['unit_name']) ? 1 : $value['additem']['measurementunit']['unit_name'].'</td>
echo $pdf->Output('PO_details_' . $users['purchaseorder_id'] . '.pdf');
exit;
