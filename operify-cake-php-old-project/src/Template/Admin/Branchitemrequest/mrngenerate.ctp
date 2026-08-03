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

<h3 style="color:#333; font-size:14px; font-weight:bold; margin-bottom:10px; text-align:center;">Material Receipt Note</h3>

<table cellpadding="5">
  <tr>
    <th style="font-weight:bold; border:1px solid #999; background-color:#ccc;">MRN Detail</th>
    <th style="font-weight:bold; border:1px solid #999; background-color:#ccc;">Bill Detail</th>
    <th style="font-weight:bold; border:1px solid #999; background-color:#ccc;">Supplier</th>
  </tr>

  <tr>
    <td style="border:1px solid #999;">MRN No. - '.$mrn_request['id'].'<br>MRN Date : '.date('d-m-Y', strtotime($mrn_request['mrn_date'])).'</td>
    <td style="border:1px solid #999;">Bill No. - '.$mrn_request['id'].'<br>Bill Date : '.date('d-m-Y', strtotime($mrn_request['bill_challan_date'])).'</td>
    <td style="border:1px solid #999;">Canvas International Pre School (Unit of Ingenious Edu Scholars Private Limited)</td>
  </tr>
</table>

<br>
<br>
<br>

<table cellpadding="5">
  <tr>
    <th width="6%" style="font-weight:bold; border:1px solid #999; background-color:#ccc;">S.No</th>
    <th width="43%" style="font-weight:bold; border:1px solid #999; background-color:#ccc;">Item (Unit)</th>
    <th width="12%" style="font-weight:bold; border:1px solid #999; background-color:#ccc;">Qty.</th>
    <th width="15%" style="font-weight:bold; border:1px solid #999; background-color:#ccc;">Item Amount</th>
   <!-- <th width="15%" style="font-weight:bold; border:1px solid #999; background-color:#ccc;">Whole Sale Price</th>-->
    <th width="12%" style="font-weight:bold; border:1px solid #999; background-color:#ccc;">Tax Amount</th>
    <th width="12%" style="font-weight:bold; border:1px solid #999; background-color:#ccc;">Amount</th>
  </tr>';

  
  
$i=1; foreach($branch_request['branchrequestdetail'] as $value){ //pr($value); die;
  $total_item_qty += $value['item_qty'];
  $total_item_amt +=$value['item_amount']*$value['item_qty'];

  $discount =$value['discount']*$value['item_qty'];
  $total_item_discount +=$value['item_amount']*$value['item_qty']- $discount;

  $tax = $value['item_tax'];
  $total=$value['item_amount']*$value['item_qty']- $discount;
  $total_tax = $total*$tax/100;
  $total_tax_amount += $total*$tax/100;

  $total_amount_Data +=$total+$total_tax ; 
  $html.='<tr>
    <td style="border:1px solid #999;">'.$i.'</td>
    <td style="border:1px solid #999;">'.ucfirst(strtolower($value['additem']['item_name'])).'</td>
    <td style="border:1px solid #999; text-align:right;">'.$value['item_qty'].'</td>
    <td style="border:1px solid #999; text-align:right;">'.sprintf('%.2f',$value['item_amount']).'</td>
    <td style="border:1px solid #999; text-align:right;">'.sprintf('%.2f',$total_tax).'</td>
    <td style="border:1px solid #999; text-align:right;">'.sprintf('%.2f',round($total+$total_tax)).'</td>
  </tr>';
 
$i++; }
  
  $html.='</table>



<table cellpadding="5">
 
  <tr>
    <td width="49%" colspan="2" style="border:1px solid #999; text-align:right; font-weight:bold;">Total</td>
    <td width="12%" style="border:1px solid #999; text-align:right; font-weight:bold;">'.round($total_item_qty,2).'</td>
    <td  width="15%" style="border:1px solid #999; text-align:right; font-weight:bold;">'.sprintf('%.2f',$total_item_amt).'</td>
    <td  width="12%" style="border:1px solid #999; text-align:right; font-weight:bold;">'.sprintf('%.2f',$total_tax_amount).'</td>
    <td width="12%" style="border:1px solid #999; text-align:right; font-weight:bold;">'.sprintf('%.2f',round($total_amount_Data)).'</td>
  </tr>

  <tr>
    <td colspan="2" style="border:1px solid #999; text-align:right; font-weight:bold;">Net Total</td>
    <td colspan="4" style="border:1px solid #999; text-align:right; font-weight:bold;">'.sprintf('%.2f',round($total_amount_Data)).'</td>
  </tr>
</table>

<p style="font-size:10px;">
<b style="font-size:12px; font-weight:bold; margin-bottom:0px;">Made By</b>
<br>
'.$sitesetting['site_title'].'</p>

  ';

  $pdf->writeHTMLCell(0, 0, '', '', utf8_encode($html), 0, 1, 0, true, '', true);
  //$pdf->WriteHTML($html, true, false, true, false, '');
  ob_end_clean();
  echo $pdf->Output('orderlist.pdf');
  exit;
  ?>