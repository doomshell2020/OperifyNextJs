<?php 
//pr($users); die; 
class xtcpdf extends TCPDF
{

}
define("MAJOR", 'Rupees Only');
define("MINOR", '');
class toWords
{
    var $pounds;
    var $pence;
    var $major;
    var $minor;
    var $words = '';
    var $number;
    var $magind;
       var $units = array('', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine');
    var $teens = array('Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen');
    var $tens = array('', 'Ten', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety');
    var $mag = array('', 'Thousand', 'Million', 'Billion', 'Trillion');

    function toWords($amount, $major = MAJOR, $minor =MINOR )
    {
        $this->__toWords__((int)($amount), $major);
        $whole_number_part = $this->words;
        #$right_of_decimal = (int)(($amount-(int)$amount) * 100);
        $strform = number_format($amount,2);
        $right_of_decimal = (int)substr($strform, strpos($strform,'.')+1);
        $this->__toWords__($right_of_decimal, $minor);
        $this->words = $whole_number_part . ' ' . $this->words;
    }

    function __toWords__($amount, $major)
    {
        $this->major  = $major;
        #$this->minor  = $minor;
        $this->number = number_format($amount, 2);
        list($this->pounds, $this->pence) = explode('.', $this->number);
        $this->words = " $this->major";
        if ($this->pounds == 0)
            $this->words = "$this->words";
        else {
            $groups = explode(',', $this->pounds);
            $groups = array_reverse($groups);
            for ($this->magind = 0; $this->magind < count($groups); $this->magind++) {
                if (($this->magind == 1) && (strpos($this->words, 'Hundred') === false) && ($groups[0] != '000'))
                    $this->words = ' And ' . $this->words;
                $this->words = $this->_build($groups[$this->magind]) . $this->words;
            }
        }
    }

    function _build($n)
    {
        $res = '';
        $na  = str_pad("$n", 3, "0", STR_PAD_LEFT);
        if ($na == '000')
            return '';
        if ($na{0} != 0)
            $res = ' ' . $this->units[$na{0}] . ' Hundred';
        if (($na{1} == '0') && ($na{2} == '0'))
            return $res . ' ' . $this->mag[$this->magind];
        $res .= $res == '' ? '' : ' And';
        $t = (int) $na{1};
        $u = (int) $na{2};
        switch ($t) {
            case 0:
                $res .= ' ' . $this->units[$u];
                break;
            case 1:
                $res .= ' ' . $this->teens[$u];
                break;
            default:
                $res .= ' ' . $this->tens[$t] . ' ' . $this->units[$u];
                break;
        }
        $res .= ' ' . $this->mag[$this->magind];
        return $res;
    }
}
//$subject=$this->Comman->findexamsubjectsresult($students['id'],$students['section']['id'],$students['acedmicyear']);

$this->set('pdf', new TCPDF('L', 'mm', 'A4'));
$pdf = new TCPDF("L", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->AddPage();
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->SetFont('', '', 10, '', 'false');
$vendorshipfrom=$this->Comman->vendorshipfromdetail($users['vendor_id']);
$findvendornames=$this->Comman->findvendornames($users['vendor_id']);
$podate = date('d-m-Y',strtotime($users['added_time']));
$delivery_date = date('d-m-Y',strtotime($users['delivery_date']));
$supliername = $sup['name'];   
if($co!=0){
	$beforesavepo=$this->Comman->getbeforerevisedpo($users['purchaseorder_id']);
	$amedmentdate=$podate;
	$podate=date('d-m-Y',strtotime($beforesavepo['added_time']));
}
if($users['status']=='O'){
	
$status="Open";
}else{
	
$status="Close";
	
}

$html .= '
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>GOOD RECEIPT NOTE (GRN)</title><link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">';
$html.='</head>
<body>
<div style="border: 1px solid #000;">
<table width="100%" style="padding: 1px 1px 0px 0px;" align="left">
<tr>
<td style="text-align:left" width="50%" >
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/logo.png" alt="" border="0" style="display:block;"
width="130px" height="60px;"><br>
<span style="display:block; color:#000; font-size:8px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<u><br>Affiliated to CBSE Delhi (Affiliation
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
</table><br>
<table width="100%;">
<hr>
<tr>
<td width="100%">

<div style=" line-height:8px; text-align:center;">

<h5 style="text-align:center; font-size:12px; line-height:8px; color:#000; margin-bottom:0px;">GOOD RECEIPT NOTE (GRN)
</h5>

<hr>
</div>
</td>
</tr>

</table>

<table width="100%">
<tr>
<td width="50%">

<table width="100%">

<tr>
<td width="2%" style="height:20px; line-height:25px;"></td>
<td width="25%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:20px; line-height:25px;">Status </td>
<td width="5%" style="text-align:center; color:#000; font-size:8px; height:20px; line-height:25px;">:</td>
<td width="66%" style=" text-align:left; color:#000; font-size:8px; height:20px; line-height:25px;">
'.$status.'
</td>
<td width="2%" style="height:20px; line-height:25px;"></td>
</tr>


<tr>
<td width="2%" style="height:20px; line-height:25px;"></td>
<td width="25%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:20px; line-height:25px;">GRN No. </td>
<td width="5%" style="text-align:center; color:#000; font-size:8px; height:12px; line-height:25px;">:</td>
<td width="66%" style=" text-align:left; color:#000; font-size:8px; height:20px; line-height:25px;">
 '.$users['id'].'

 
</td>
<td width="2%" style="height:20px; line-height:25px;"></td>
</tr>


<tr>
<td width="2%" style="height:20px; line-height:25px;"></td>
<td width="25%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:20px; line-height:25px;">Inward Date</td>
<td width="5%" style="text-align:center; color:#000; font-size:8px; height:20px; line-height:25px;">:</td>
<td width="66%" style=" text-align:left; color:#000; font-size:8px; height:20px; line-height:25px;">
 '.date("d-m-Y", strtotime($users['inwarddate'])).'
 
</td>
<td width="2%" style="height:20px; line-height:25px;"></td>
</tr>


<tr>
<td width="2%" style="height:20px; line-height:25px;"></td>
<td width="25%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:20px; line-height:25px;">Bill Date</td>
<td width="5%" style="text-align:center; color:#000; font-size:8px; height:20px; line-height:25px;">:</td>
<td width="66%" style=" text-align:left; color:#000; font-size:8px; height:20px; line-height:25px;"> '.date("d-m-Y", strtotime($users['bill_date'])).'

 
</td>
<td width="2%" style="height:20px; line-height:25px;"></td>
</tr>

<tr>
<td width="2%" style="height:20px; line-height:25px;"></td>
<td width="25%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:20px; line-height:25px;">Bill No </td>
<td width="5%" style="text-align:center; color:#000; font-size:8px; height:20px; line-height:25px;">:</td>
<td width="66%" style=" text-align:left; color:#000; font-size:8px; height:20px; line-height:25px;">'.$users['bill_no'].'

 
</td>
<td width="2%" style="height:20px; line-height:25px;"></td>
</tr>
</table>
</td>




<td width="50%">

<table width="100%">

<tr>
<td width="2%" style="height:20px; line-height:25px;"></td>
<td width="30%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:20px; line-height:25px;">GSTIN NO. </td>
<td width="5%" style="text-align:center; color:#000; font-size:8px; height:20px; line-height:25px;">:</td>
<td width="61%" style=" text-align:left; color:#000; font-size:8px; height:20px; line-height:25px;">
'.$vendorshipfrom[0]['gst_number'].' 
</td>
<td width="2%" style="height:20px; line-height:25px;"></td>
</tr>


<tr>
<td width="2%" style="height:20px; line-height:25px;"></td>
<td width="30%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:20px; line-height:25px;">Vendor Name</td>
<td width="5%" style="text-align:center; color:#000; font-size:8px; height:20px; line-height:25px;">:</td>
<td width="61%" style=" text-align:left; color:#000; font-size:8px; height:20px; line-height:25px;">
'.$findvendornames['name'].'
 
</td>
<td width="2%" style="height:20px; line-height:25px;"></td>
</tr>





<tr>
<td width="2%" style="height:20px; line-height:25px;"></td>
<td width="30%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:20px; line-height:25px;">PO No. </td>
<td width="5%" style="text-align:center; color:#000; font-size:8px; height:20px; line-height:25px;">:</td>
<td width="61%" style=" text-align:left; color:#000; font-size:8px; height:32px; line-height:25px;">'.$users['purchaseorder_id'].' 
</td>
<td width="2%" style="height:20px; line-height:25px;"></td>
</tr>
</table>
</td>

</tr>
</table>

<table width="100%">
<tr>
<td width="4%" style="border-top:1px solid #000; border-right:1px solid #000; border-bottom:1px solid #000; color:#000; height:12px; line-height:12px; font-size:8px; font-weight:bold; text-align:left;"> S.No</td>
<td width="9.6%" style="border-top:1px solid #000; border-right:1px solid #000; border-bottom:1px solid #000; color:#000; height:12px; line-height:12px;font-size:8px; font-weight:bold; text-align:left;"> &nbsp; ITEM</td>
<td width="9.6%" style="border-top:1px solid #000; border-right:1px solid #000; border-bottom:1px solid #000; color:#000; height:12px; line-height:12px;font-size:8px; font-weight:bold; text-align:left;"> &nbsp; SIZE</td>
<td width="9.6%" style="border-top:1px solid #000; border-right:1px solid #000; border-bottom:1px solid #000; color:#000; height:12px; line-height:12px;font-size:8px; font-weight:bold; text-align:left;"> &nbsp; UNITS</td>
<td width="9.6%" style="border-top:1px solid #000; border-right:1px solid #000; border-bottom:1px solid #000; color:#000;height:12px; line-height:12px; font-size:8px; font-weight:bold; text-align:left;"> &nbsp;ORDER QTY.</td>
<td width="9.6%" style="border-top:1px solid #000; border-right:1px solid #000; border-bottom:1px solid #000; color:#000;height:12px; line-height:12px; font-size:8px; font-weight:bold; text-align:left;"> &nbsp; RECEIVED QTY.</td>
<td width="9.6%" style="border-top:1px solid #000; border-right:1px solid #000; border-bottom:1px solid #000; color:#000; height:12px; line-height:12px;font-size:8px; font-weight:bold; text-align:left;"> &nbsp; RATE</td>
<td width="9.6%" style="border-top:1px solid #000; border-right:1px solid #000; border-bottom:1px solid #000; color:#000; height:12px; line-height:12px;font-size:8px; font-weight:bold; text-align:left;"> &nbsp; PRICE (INR)</td>
<td width="9.6%" style="border-top:1px solid #000; border-right:1px solid #000; border-bottom:1px solid #000; color:#000;height:12px; line-height:12px; font-size:8px; font-weight:bold; text-align:left;"> &nbsp; TAX RATE</td>
<td width="9.6%" style="border-top:1px solid #000; border-right:1px solid #000; border-bottom:1px solid #000; color:#000;height:12px; line-height:12px; font-size:8px; font-weight:bold; text-align:left;"> &nbsp; TAX AMT</td>
<td width="9.6%" style="border-top:1px solid #000;  border-bottom:1px solid #000; color:#000; height:12px; line-height:12px;font-size:8px; font-weight:bold; text-align:left;"> &nbsp; AMOUNT</td></tr>';



$s = 1;
foreach($puritems as $value){  //pr($value); die;
$unitname = $this->Comman->getunitnamepoview($value['additem']['unit_id']);
$sizename = $this->Comman->getsizename($value['additem']['size_id']);
$getpo = $this->Comman->getpostockitem($value['po_id'],$value['item_id']);
$gettaxparent = $this->Comman->gettaxnameparent($value['tax_id']);
if(empty($gettaxparent)){
	
	$gettaxparent = $this->Comman->gettaxname2($value['tax_id']);
}
$i=0;
$taxx='';

foreach($gettaxparent as $hh=>$ty){
	$taxx.=$ty['tax_name'].'<br> &nbsp;';
	$i++;
	
}

if($i==2){
	
	$taaxx=$value['tax'] / $i;
	$taxxs=number_format((float)$taaxx, 2, '.', '')."<br> &nbsp;".number_format((float)$taaxx, 2, '.', '');
}else{
	$taxxs=number_format((float)$value['tax'], 2, '.', '');
	
}


$html.='<tr>
<td width="4%" style=" border-right:1px solid #000; border-bottom:1px solid #000; color:#000;height:12px; line-height:10px; font-size:8px;  text-align:left;"> '.$s.'.</td>

<td width="9.6%" style=" border-right:1px solid #000; border-bottom:1px solid #000; color:#000;height:12px; line-height:10px; font-size:8px;  text-align:left;"> &nbsp; '.$value['additem']['item_name'].'</td>
<td width="9.6%" style=" border-right:1px solid #000; border-bottom:1px solid #000; color:#000;height:12px; line-height:10px; font-size:8px;  text-align:left;"> &nbsp; '.$sizename['size_name'].'</td>
<td width="9.6%" style=" border-right:1px solid #000; border-bottom:1px solid #000; color:#000;height:12px; line-height:10px; font-size:8px;  text-align:left;"> &nbsp; '.$unitname['unit_name'].'</td>

<td width="9.6%" style=" border-right:1px solid #000; border-bottom:1px solid #000; color:#000;height:12px; line-height:10px; font-size:8px;text-align:left;"> &nbsp; '.$getpo['quantity'].'</td>
<td width="9.6%" style=" border-right:1px solid #000; border-bottom:1px solid #000; color:#000;height:12px; line-height:10px; font-size:8px;text-align:left;"> &nbsp; '.$value['quantity'].'</td>
<td width="9.6%" style=" border-right:1px solid #000; border-bottom:1px solid #000; color:#000;height:12px; line-height:10px; font-size:8px;  text-align:left;"> &nbsp; '.number_format((float)$value['rate'], 2, '.', '').' </td>
<td width="9.6%" style=" border-right:1px solid #000; border-bottom:1px solid #000; color:#000;height:12px; line-height:10px; font-size:8px;  text-align:left;"> &nbsp; '.number_format((float)$value['cost_price'], 2, '.', '').' </td>
<td width="9.6%" style=" border-right:1px solid #000; border-bottom:1px solid #000; color:#000;height:12px; line-height:10px; font-size:8px; text-align:left;"> &nbsp;'.$taxx.'</td>

<td width="9.6%" style=" border-right:1px solid #000; border-bottom:1px solid #000; color:#000; height:12px; line-height:10px; font-size:8px;  text-align:left;"> &nbsp;'.$taxxs.'</td>

<td width="9.6%" style="  border-bottom:1px solid #000; color:#000;height:12px; line-height:10px; font-size:9px;  text-align:left;"> &nbsp; '.number_format((float)$value['amount'], 2, '.', '').'</td>
</tr>';

$totalrate += $value['rate'];
$totalqua += $value['quantity'];
$totaltax += $value['tax'];
$totalamaunt += $value['amount']; 
$s++;
}



$netamt=$totalamaunt+$users->freight;
$obj    = new toWords($netamt);
$html.='<tr>
<td width="63%" style=" border-bottom:1px solid #000; color:#000;height:12px; line-height:12px; font-size:8px;  text-align:center;"></td>
<td width="28%" style="font-weight:bold;  border-bottom:1px solid #000; color:#000;height:12px; line-height:12px; font-size:8px;  text-align:left;"> &nbsp;  Amount</td>
<td width="9%" style="  border-bottom:1px solid #000; color:#000;height:12px; line-height:12px; font-size:8px;  text-align:left;"> &nbsp;&nbsp;&nbsp;'.number_format((float)$totalamaunt, 2, '.', '').'</td>
</tr>
</table>



<table width="100%">
<tr>
<td width="64%" style="border-top:1px solid #000; border-right:1px solid #000;">

<table width="100%">
<tr>

<td width="30%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;"> </td>

<td width="70%" style=" text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">&nbsp;

 
</td>

</tr>
</table>
</td>
<td width="36%">
<table width="100%">
<tr>

<td width="77%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">&nbsp;Freight Charges</td>


<td width="23%" style=" text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">
&nbsp;&nbsp;'.number_format((float)$users->freight, 2, '.', '').'
 
</td>

</tr>

</table>
</td>
</tr>
</table>



<table width="100%">
<tr>
<td width="64%" style="border-top:1px solid #000; border-right:1px solid #000;">

<table width="100%">
<tr>

<td width="30%" style="font-weight:bold; text-align:left;border-bottom:1px solid #000;  color:#000; font-size:8px; height:12px; line-height:12px;"> &nbsp;&nbsp;&nbsp;Amount (In Words)</td>

<td width="70%" style=" text-align:left; color:#000; border-bottom:1px solid #000; font-size:8px; height:12px; line-height:12px;">
'.$obj->words.' 
</td>

</tr>
</table>
</td>
<td width="36%">
<table width="100%">
<tr>

<td width="77%" style="font-weight:bold; text-align:left;border-bottom:1px solid #000;  color:#000; font-size:8px; height:12px; line-height:12px;">&nbsp;Total Amount</td>


<td width="23%" style="border-bottom:1px solid #000;  text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">
'.number_format((float)$netamt, 2, '.', '').'
 
</td>

</tr>

</table>
</td>
</tr>
</table>


<br><br><br>
<table width="100%">
<tr >

<td width="20%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;"> &nbsp;&nbsp;&nbsp;Remarks</td>

<td width="80%" style=" text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">
'.ucfirst(strtolower($users->remark)).'
 
</td>
</tr>
</table>



<table width="100%">
<tr>
<td width="100%" style=""></td>
</tr>
</table>


<table width="100%">
<tr>
<td width="15%" style="height:30px; text-align:center; font-size:8px; border-top:1px solid #000; height:30px; line-height:16px;">For Sanskar School Office </td>
<td width="15%" style="height:30px; text-align:center; font-size:8px; border-top:1px solid #000; height:30px; line-height:16px;">Inspected By  </td>
<td width="15%" style="height:30px; text-align:center; font-size:8px; border-top:1px solid #000; height:30px; line-height:16px;">Store Incharge </td>
<td width="15%" style="height:30px; text-align:center; font-size:8px; border-top:1px solid #000; height:30px; line-height:16px;">Checked by </td>

<td width="40%" style="height:30px; text-align:center; font-size:8px; border-top:1px solid #000; height:30px; line-height:16px;"> Signature Authority </td>
</tr>


</table>
</div>

</body></html>';
//echo $html; die;
$pdf->WriteHTML($html, true, false, true, false, '');
ob_end_clean();
$date = date('d-m-Y');
echo $pdf->Output('Daily-Summary-' . $bordd . '-' . $date . '.pdf');
exit;
?>
