<?php 
class xtcpdf extends TCPDF {
}
 //$subject=$this->Comman->findexamsubjectsresult($students['id'],$students['section']['id'],$students['acedmicyear']);

   $this->set('pdf', new TCPDF('L','mm','A4'));
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);

// set document information

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->AddPage();
//$pdf->setHeaderMargin(0);

// set margins
//$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
 $pdf->SetAutoPageBreak(TRUE, 25);
//$pdf->SetMargins(5, 0, 5, true);

$pdf->SetFont('', '', 8, '', 'true');
TCPDF_FONTS::addTTFfont('../Devanagari/Devanagari.ttf', 'TrueTypeUnicode', "", 32);
//pr($company_master); die;
$branchdata = $this->Comman->branchdataget($branch_request['branch_name']);  
$branchdata_detail = $this->Comman->branchdataget_detail($branch_request['branch_name']);  
// pr($branchdata_detail); die;

$logo  = SITE_URL."/images/".$site_details['small_logo'];
$address  =  $site_details['address1'];
$address2  =  $company_master['address'];
//pr($address); die;
$company_name  =  $company_master['cname'];
$gst_no  =  $company_master['gst'];
$pan_no  =  $company_master['pan_no'];
$state_check  =  $branchdata[0]['palace_state'];

$accountno  =  $company_master['accountno']; 
$ifsc  =  $company_master['ifsc']; 
$branch_request_id  =  $branch_request['id'];
$pay_Date  =  $branch_request['pay_date'];
$customer_name  =  $branch_request['customer_name'];
$customer_mobile  =  $branch_request['customer_mobile'];


$html='
<table width="100%" style="padding: 1px 1px 0px 0px;" align="left">
  <tr>
    <td style="text-align:left" width="40%" >
      <img src="'.$logo.'" alt="" border="0" style="display:block;" width="80px">
      <!--<span style="display:block; color:#000; font-size:7px; height:12px; line-height:12px;">
        <u><br>Affiliated to CBSE Delhi (Affiliation no.1730236)</u>
      </span>-->
    </td>

    <td style="text-align:left;" width="60%" align="right">
      <br>
     <b> '.ucwords(strtoupper($company_name)).'</b><br>
     
     <b> '.$address2.'</b><br>
      <b>GST No</b>
      :<b>'.$gst_no.'</b>&nbsp;&nbsp;&nbsp;<br>
      <b>PAN No</b>
      :<b>'.$pan_no.'</b>&nbsp;&nbsp;&nbsp;
    </td>
  </tr>
</table>

<table width="100%" cellpadding="3px">
  <tr>
    <td colspan="6" style="width:100%; border:1px solid #ddd;font-size:10px; text-align:center;">Tax Invoice</td>
  </tr>

  <tr>
    <td style="width:20%; border:1px solid #ddd; text-align:left; font-size:7px; font-weight:bold;">
    INVOICE NO. 
    </td>
    <td style="width:10%; border:1px solid #ddd; text-align:left; font-size:7px;">
    '.$branch_request_id.'
    </td>
    <td style="width:20%; border:1px solid #ddd; text-align:left; font-size:7px; font-weight:bold;">
    Dated
    </td>
    <td style="width:10%; border:1px solid #ddd; text-align:left; font-size:7px; ">
    '.date('d-m-Y', strtotime($pay_Date)).'
    </td>
    <td style="width:20%; border:1px solid #ddd; text-align:left; font-size:7px; font-weight:bold ">
    TRANSPORT MODE
    </td>
    <td style="width:20%; border:1px solid #ddd; text-align:left; font-size:7px;  ">
    </td>
  </tr>

  <tr>
    <td style="width:100%; border:1px solid #ddd; text-align:left; font-size:7px; font-weight:bold;"colspan="4">
    VEHICLE NO.
    </td>
  </tr>

  <tr>
   

    <td style="width:30%; border:1px solid #ddd; text-align:left; font-size:7px; font-weight:bold;  ">
    PLACE OF SUPPLY

    </td>

    <td style="width:20%; border:1px solid #ddd; text-align:left; font-size:7px;  ">
    Jaipur
    </td>

    <td style="width:30%; border:1px solid #ddd; text-align:left; font-size:7px; font-weight:bold;  ">
    DATE OF SUPPLY
    </td>

    <td style="width:20%; border:1px solid #ddd; text-align:left; font-size:7px;  ">
    '.date('d-m-Y', strtotime($pay_Date)).'
    </td>
  </tr>



  <tr>
    <td style="width:50%; border:1px solid #ddd; font-size:7px; text-align:center; font-weight:bold;  ">
    BILL TO PARTY
    </td>

    <td style="width:50%; border:1px solid #ddd; font-size:7px; text-align:center; font-weight:bold;  ">
    SHIP TO PARTY 
    </td>
  </tr>

  <tr>
    <td style="width:30%; border:1px solid #ddd; font-size:7px; text-align:left; font-weight:bold;  ">
    NAME OF PARTY
    </td>

    <td style="width:20%; border:1px solid #ddd; font-size:7px; text-align:left;  ">
    '.ucwords(strtolower($customer_name)).'
    </td>

    <td style="width:30%; border:1px solid #ddd; font-size:7px; text-align:left; font-weight:bold;  ">
    NAME OF PARTY
    </td>

    <td style="width:20%; border:1px solid #ddd; font-size:7px; text-align:left;  ">
    '.ucwords(strtolower($customer_name)).'
    </td>
  </tr>

  <tr>
    <td style="width:30%; border:1px solid #ddd; font-size:7px; text-align:left; font-weight:bold;  ">
    ADDRESS
    </td>

    <td style="width:20%; border:1px solid #ddd; font-size:7px; text-align:left;  ">
    '.$branchdata_detail[0]['address'].'
    </td>

    <td style="width:30%; border:1px solid #ddd; font-size:7px; text-align:left; font-weight:bold;  ">
    ADDRESS 
    </td>

    <td style="width:20%; border:1px solid #ddd; font-size:7px; text-align:left;  ">
    '.$branchdata_detail[0]['address'].'
    </td>
  </tr>

  <tr>
    <td style="width:100%; border:1px solid #ddd; font-size:7px; text-align:left;  font-weight:bold; ">
    Mobile : '.$customer_mobile.'
    </td>
  </tr>

  <tr>
    <td style="width:25%; border:1px solid #ddd; font-size:7px; text-align:left; font-weight:bold;  ">
    GSTIN 
    </td>

    <td style="width:27%; border:1px solid #ddd; font-size:7px; text-align:left;  ">
    Unregistered


    </td>

    <td style="width:25%; border:1px solid #ddd; font-size:7px; text-align:left; font-weight:bold;  ">
    GSTIN  

    </td>

    <td style="width:23%; border:1px solid #ddd; font-size:7px; text-align:left;  ">
    Unregistered

    </td>
  </tr>

  <tr>
    <td style="width:5%; border:1px solid #ddd; height:20px; line-height:12px; font-size:7px; text-align:left; font-weight:bold;  ">
    S.N.
    </td>

    <td style="width:20%; border:1px solid #ddd; height:20px; line-height:12px; font-size:7px; text-align:left; font-weight:bold;">
     Item
    </td>

    <td style="width:10%; border:1px solid #ddd; height:20px; line-height:12px; font-size:7px; text-align:left; font-weight:bold;  ">
    HSN Code
    </td>

    <td style="width:9%; border:1px solid #ddd; height:20px; line-height:12px; font-size:7px; text-align:right; font-weight:bold;  ">
    Unit Price
    </td>

    <td style="width:8%; border:1px solid #ddd; height:20px; line-height:12px; font-size:7px; text-align:right; font-weight:bold;  ">
    Quantity
    </td>

    <td style="width:10%; border:1px solid #ddd; height:20px; line-height:12px; font-size:7px; text-align:right; font-weight:bold;  ">
    Discount
    </td>

    <td style="width:5%; border:1px solid #ddd; height:20px; line-height:12px; font-size:7px; text-align:right; font-weight:bold;  ">
    GST
    </td>

    <td style="width:10%; border:1px solid #ddd; height:20px; line-height:12px; font-size:7px; text-align:right; font-weight:bold;  ">
    Taxable
    </td>

    <td style="width:11%; border:1px solid #ddd; height:20px; line-height:12px; font-size:7px; text-align:right; font-weight:bold;  ">
    Tax Amount
    </td>

    <td style="width:12%; border:1px solid #ddd; height:20px; line-height:12px; font-size:7px; text-align:right; font-weight:bold;  ">
    Total Invoice 
    </td>
  </tr>';

    $i=1; foreach($branch_request['branchrequestdetail'] as $key=>$value){ //pr($value); die;
   
    $total_amount =  $value['item_qty'] * $value['item_amount'];
    $total_discount =  $value['item_qty'] * $value['discount'];
    $taxable_amount = $total_amount-$total_discount;
    $tax_amount = $taxable_amount*$value['item_tax']/100;
    $totalamount = $taxable_amount+$tax_amount;
 
    if($value['item_tax'] == 0){ 
      $total_tax =   "N/A"; 
      }else{ 
      $total_tax = $value['item_tax']."%";
      }
      $item_amount +=  $value['item_amount']; 
      $item_qty +=  $value['item_qty']; 
      $discount +=  $value['discount']; 
      $taxable_amountdata += $total_amount-$total_discount;
      $tax_amountdata += $taxable_amount*$value['item_tax']/100;
      $totalamount_data += $taxable_amount+$tax_amount;

// taxable amount
    $itemtax_price=$value['item_amount']*$value['item_tax']/100;
    

      $cgst  = $tax_amountdata/2;
      $sgst   = $tax_amountdata/2;
    $html.='
  <tr>
    <td style="width:5%; border:1px solid #ddd; text-align:left; font-size:7px;">
    '.$i.'
    </td>
    
    <td style="width:20%; border:1px solid #ddd; text-align:left; font-size:7px;">'.ucfirst(strtolower(trim($value['additem']['item_name']))).'</td>

    <td style="width:10%; border:1px solid #ddd; text-align:left; font-size:7px;">
    '.$value['hsncode'].'
    </td>

    <td style="width:9%; border:1px solid #ddd; text-align:right; font-size:7px;">
    '.sprintf('%.2f',$value['additem']['sale_price']).'
    </td>

    <td style="width:8%; border:1px solid #ddd; text-align:right; font-size:7px;">
    '.$value['item_qty'].'
    </td>

    <td style="width:10%; border:1px solid #ddd; text-align:right; font-size:7px;">
    '.sprintf('%.2f',$value['discount']).'
    </td>

    <td style="width:5%; border:1px solid #ddd; text-align:right;font-size:7px;">
    '.round($total_tax,2).'
    </td> 

    <td style="width:10%; border:1px solid #ddd; text-align:right;font-size:7px;">
    '.sprintf('%.2f',$taxable_amount).'
    </td>

    <td style="width:11%; border:1px solid #ddd; text-align:right;font-size:7px;">
    '.sprintf('%.2f',$itemtax_price).'
    </td>

    <td style="width:12%; border:1px solid #ddd; text-align:right;font-size:7px;">
    '.sprintf('%.2f',$totalamount).'
    </td>
  </tr>';
  $i++; }
  $html.='  

  <tr>
  <td style="width:5%; border:1px solid #ddd; height:20px; line-height:12px; font-size:7px; text-align:left; font-weight:bold;"></td>
  <td style="width:20%; border:1px solid #ddd; height:20px; line-height:12px; font-size:7px; text-align:left; font-weight:bold;"></td>
  <td style="width:10%; border:1px solid #ddd; height:20px; line-height:12px; font-size:7px; text-align:left; font-weight:bold;"></td>

  <td style="width:9%; border:1px solid #ddd; height:20px; line-height:12px; font-size:7px; text-align:right; font-weight:bold;  ">
  '.round($item_amount,2).'
  </td>

  <td style="width:8%; border:1px solid #ddd; height:20px; line-height:12px; font-size:7px; text-align:right; font-weight:bold;  ">
  '.$item_qty.'&nbsp;
  </td>

  <td style="width:8%; border:1px solid #ddd; height:20px; line-height:12px; font-size:7px; text-align:right; font-weight:bold;  ">
  '.sprintf('%.2f',$discount).'
  </td>

  <td style="width:7%; border:1px solid #ddd; height:20px; line-height:12px; font-size:7px; text-align:right; font-weight:bold;  ">
  </td>

  <td style="width:10%; border:1px solid #ddd; height:20px; line-height:12px; font-size:7px; text-align:right; font-weight:bold;  ">
  '.round($taxable_amountdata,2).'
  </td>

  <td style="width:11%; border:1px solid #ddd; height:20px; line-height:12px; font-size:7px; text-align:right; font-weight:bold;  ">
  '.round($tax_amountdata,2).'
  </td>

  <td style="width:12%; border:1px solid #ddd; height:20px; line-height:12px; font-size:7px; text-align:right; font-weight:bold;  ">
  '.sprintf('%.2f',round($totalamount_data)).'
  </td>
</tr>

  <tr>
    <td style="width:90%; border:1px solid #ddd; text-align:right; font-weight:bold;   ">
    Total Amount Before tax
    </td>

    <td style="width:10%; border:1px solid #ddd; text-align:right;   ">
    '.round($taxable_amountdata,2).'
    </td>
  </tr>';
if($state_check  == "other"){
  $html.='<tr>
  <td style="width:90%; border:1px solid #ddd; text-align:right; font-weight:bold;">
  Total IGST
  </td>

  <td style="width:10%; border:1px solid #ddd; text-align:right;   ">
  '.sprintf('%.2f',$cgst+$sgst).'
  </td>
</tr>';
}else{
  
  $html.='<tr>
  <td style="width:90%; border:1px solid #ddd; text-align:right; font-weight:bold;">
  Total CGST
  </td>

  <td style="width:10%; border:1px solid #ddd; text-align:right;   ">
  '.sprintf('%.2f',$cgst).'
  </td>
</tr>
  <tr>
    <td style="width:90%; border:1px solid #ddd; text-align:right; font-weight:bold;">
    Total SGST
    </td>

    <td style="width:10%; border:1px solid #ddd; text-align:right;   ">
    '.sprintf('%.2f',$sgst).'
    </td>
  </tr>';


}
$html.='<tr>
    <td style="width:90%; border:1px solid #ddd; text-align:right; font-weight:bold;   ">
    Total Tax Amount
    </td>

    <td style="width:10%; border:1px solid #ddd; text-align:right;   ">
    '.sprintf('%.2f',$tax_amountdata).'
    </td>
  </tr>

  <tr>
    <td style="width:90%; border:1px solid #ddd; text-align:right; font-weight:bold;">
    Bill Amount
    </td>
    <td style="width:10%; border:1px solid #ddd; text-align:right;   ">
    '.sprintf('%.2f',round($totalamount_data)).'
    </td>
  </tr>
</table>
<br>
<br>

<table width="100%" cellpadding="3px">
<tr>
  <td width="100%"  style="border:1px solid #ddd; text-align:center; height:20px; font-weight:bold; font-size:10px;">Tax Detail</td>
</tr>

<tr>
  <td width="16.66%" style="border:1px solid #ddd; font-size:7px; text-align:center;">Sr. No.</td>
  <td width="16.66%" style="border:1px solid #ddd; font-size:7px; text-align:center;">Tax Rate</td>
  <td width="16.66%" style="border:1px solid #ddd; font-size:7px; text-align:center;">Taxable Value</td>';
  if($state_check  == "other"){
    $html.= '<td width="33.32%" style="border:1px solid #ddd; font-size:7px; text-align:center;">IGST</td>';
  }else{ 
  $html.= '<td width="16.66%" style="border:1px solid #ddd; font-size:7px; text-align:center;">CGST</td>
  <td width="16.66%" style="border:1px solid #ddd; font-size:7px; text-align:center;">SGST</td>';
  }
   $html.= '<td width="16.66%" style="border:1px solid #ddd; font-size:7px; text-align:center;">Total Amount</td>
</tr>';

foreach($branch_request['branchrequestdetail'] as $key=>$value){
  if($value['item_tax']){
    $tax_data[] = $value['item_tax']; 
  }else{
    $tax_data[] = 0;
  }

}
$array_Data = array_unique($tax_data);
arsort($array_Data);
//pr($array_Data); die; 

$t =1; foreach($array_Data as $key=>$value){ //pr($value); die;

  $taxableprice = $this->Comman->billtaxdatanew($value,$id); 
  $tax_amountdata = $this->Comman->billgst($value,$id); 
  $total_amountdata = $this->Comman->billtotalamount($value,$id); 

  $taxableprice_total+= $taxableprice; 

  $final_amountdata+= $total_amountdata; 
  $cgst_detail  = $tax_amountdata/2;
  $sgst_detail   = $tax_amountdata/2;

  $cgst_totaldetail+= $tax_amountdata/2;
  $sgst_totaldetail+= $tax_amountdata/2;
  

$html.='<tr>
  <td width="16.66%" style="border:1px solid #ddd; font-size:7px; text-align:center;">'.$t.'</td>';
  if($value  == 0){
    $html.='<td width="16.66%" style="border:1px solid #ddd; font-size:7px; text-align:center;">Exempt</td>';
  }else{
    $html.='<td width="16.66%" style="border:1px solid #ddd; font-size:7px; text-align:center;">'.sprintf('%.2f',$value).'</td>';
  }

  $html.='<td width="16.66%" style="border:1px solid #ddd; font-size:7px; text-align:center;">'.sprintf('%.2f',$taxableprice).'</td>';
  if($state_check  == "other"){
   
    $html.= '<td width="33.32%" style="border:1px solid #ddd; font-size:7px; text-align:center;">'.sprintf('%.2f',$tax_amountdata).'</td>';
  }else{

    $html.= '<td width="16.66%" style="border:1px solid #ddd; font-size:7px; text-align:center;">'.sprintf('%.2f',$cgst_detail).'</td>

  <td width="16.66%" style="border:1px solid #ddd; font-size:7px; text-align:center;">'.sprintf('%.2f',$sgst_detail).'</td>';
  }

  $html.='<td width="16.66%" style="border:1px solid #ddd; font-size:7px; text-align:center;">'.sprintf('%.2f',$total_amountdata).'</td>
</tr>


';
$t++; } 
$last = $t+1;


$html.=
'<tr>
  <td width="33.33%" style="border:1px solid #ddd; font-size:7px; text-align:center; font-weight:bold;">Total</td>
  <td width="16.66%" style="border:1px solid #ddd; font-size:7px; text-align:center; font-weight:bold;">'.round($taxableprice_total,2).'</td>';
  if($state_check  == "other"){
    $html.= '<td width="33.32%" style="border:1px solid #ddd; font-size:7px; text-align:center; font-weight:bold;">'.sprintf('%.2f',$cgst_totaldetail+$sgst_totaldetail).'</td>';
  }else{
    $html.= '<td width="16.66%" style="border:1px solid #ddd; font-size:7px; text-align:center; font-weight:bold;">'.sprintf('%.2f',$cgst_totaldetail).'</td>
    <td width="16.66%" style="border:1px solid #ddd; font-size:7px; text-align:center; font-weight:bold;">'.sprintf('%.2f',$sgst_totaldetail).'</td>';
  }
  
  $html.='<td width="16.66%" style="border:1px solid #ddd; font-size:7px; text-align:center; font-weight:bold;">'.sprintf('%.2f',round($final_amountdata)).'</td>
</tr>
</table>

<table width="100%" cellpadding="4px">
<tr>
<td style="width:100%; border:1px solid #ddd; text-align:left;   ">
<strong>Account No. :</strong> '.$accountno.'
</td>
</tr>

<tr>
<td style="width:100%; border:1px solid #ddd; text-align:left;   ">
<strong>Account Holder. :</strong> '.$company_name.'
</td>
</tr>

<tr>
<td style="width:100%; border:1px solid #ddd; text-align:left;   ">
<strong>IFSC Code :</strong> '.$ifsc.'
</td>
</tr>
</table>

<br>
<br>
<table width="100%">

<tr>
<td width="100%">
<strong> Remark :</strong> 
'.$branch_request['remark'].'
</td>
</tr>


<tr>
<td width="100%">
<strong> Terms and conditions of Bill :</strong><br>
a. All Disputes subject to Jaipur Jurisdiction.<br>
b. Goods Once sold will not be taken back.<br>
c. E & O.E.
</td>
</tr>



</table>

';
 //pr($html);die;

$pdf->writeHTMLCell(0, 0, '', '', utf8_encode($html), 0, 1, 0, true, '', true);
//$pdf->WriteHTML($html, true, false, true, false, '');
ob_end_clean();
echo $pdf->Output('orderlist.pdf');
exit;
?>