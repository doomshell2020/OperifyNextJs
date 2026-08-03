<?php
// pr($users); die; 
class xtcpdf extends TCPDF
{
}
// define("MAJOR", 'Rupees Only');
// define("MINOR", '');

class toWords
{

  public function __construct($amount)
  {
    $this->amount = $amount;
    $this->hasPaisa = false;
    $arr = explode(".", $this->amount);
    $this->rupees = $arr[0];
    if (isset($arr[1]) && ((int) $arr[1]) > 0) {
      if (strlen($arr[1]) > 2) {
        $arr[1] = substr($arr[1], 0, 2);
      }
      $this->hasPaisa = true;
      $this->paisa = $arr[1];
    }
  }

  public function get_words()
  {
    $w = "";
    $crore = (int) ($this->rupees / 10000000);
    $this->rupees = $this->rupees % 10000000;
    $w .= $this->single_word($crore, "Crore");
    $lakh = (int) ($this->rupees / 100000);
    $this->rupees = $this->rupees % 100000;
    $w .= $this->single_word($lakh, "Lakh ");
    $thousand = (int) ($this->rupees / 1000);
    $this->rupees = $this->rupees % 1000;
    $w .= $this->single_word($thousand, "Thousand  ");
    $hundred = (int) ($this->rupees / 100);
    $w .= $this->single_word($hundred, "Hundred ");
    $ten = $this->rupees % 100;
    $w .= $this->single_word($ten, "");
    $w .= "Rupees ";
    if ($this->hasPaisa) {
      if ($this->paisa[0] == "0") {
        $this->paisa = (int) $this->paisa;
      } else if (strlen($this->paisa) == 1) {
        $this->paisa = $this->paisa * 10;
      }
      $w .= " and " . $this->single_word($this->paisa, " Paisa");
    }
    return $w . " Only";
  }

  private function single_word($n, $txt)
  {
    $t = "";
    if ($n <= 19) {
      $t = $this->words_array($n);
    } else {
      $a = $n - ($n % 10);
      $b = $n % 10;
      $t = $this->words_array($a) . " " . $this->words_array($b);
    }
    if ($n == 0) {
      $txt = "";
    }
    return $t . " " . $txt;
  }

  private function words_array($num)
  {
    $n = [0 => "", 1 => "One", 2 => "Two", 3 => "Three", 4 => "Four", 5 => "Five", 6 => "Six", 7 => "Seven", 8 => "Eight", 9 => "Nine", 10 => "Ten", 11 => "Eleven", 12 => "Twelve", 13 => "Thirteen", 14 => "Fourteen", 15 => "Fifteen", 16 => "Sixteen", 17 => "Seventeen", 18 => "Eighteen", 19 => "Nineteen", 20 => "Twenty", 30 => "Thirty", 40 => "Forty", 50 => "Fifty", 60 => "Sixty", 70 => "Seventy", 80 => "Eighty", 90 => "Ninety", 100 => "Hundred",];
    return $n[$num];
  }
}


// class toWords
// {
//     var $pounds;
//     var $pence;
//     var $major;
//     var $minor;
//     var $words = '';
//     var $number;
//     var $magind;
//        var $units = array('', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine');
//     var $teens = array('Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen');
//     var $tens = array('', 'Ten', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety');
//     var $mag = array('', 'Thousand', 'Lakh', 'Crore', 'Arab');

//     function toWords($amount, $major = MAJOR, $minor =MINOR )
//     {
//         $this->__toWords__((int)($amount), $major);
//         $whole_number_part = $this->words;
//         #$right_of_decimal = (int)(($amount-(int)$amount) * 100);
//         $strform = number_format($amount,2);
//         $right_of_decimal = (int)substr($strform, strpos($strform,'.')+1);
//         $this->__toWords__($right_of_decimal, $minor);
//         $this->words = $whole_number_part . ' ' . $this->words;
//     }

//     function __toWords__($amount, $major)
//     {
//         $this->major  = $major;
//         #$this->minor  = $minor;
//         $this->number = number_format($amount, 2);
//         list($this->pounds, $this->pence) = explode('.', $this->number);
//         $this->words = " $this->major";
//         if ($this->pounds == 0)
//             $this->words = "$this->words";
//         else {
//             $groups = explode(',', $this->pounds);
//             $groups = array_reverse($groups);
//             for ($this->magind = 0; $this->magind < count($groups); $this->magind++) {
//                 if (($this->magind == 1) && (strpos($this->words, 'Hundred') === false) && ($groups[0] != '000'))
//                     $this->words = ' And ' . $this->words;
//                 $this->words = $this->_build($groups[$this->magind]) . $this->words;
//             }
//         }
//     }

//     function _build($n)
//     {
//         $res = '';
//         $na  = str_pad("$n", 3, "0", STR_PAD_LEFT);
//         if ($na == '000')
//             return '';
//         if ($na{0} != 0)
//             $res = ' ' . $this->units[$na{0}] . ' Hundred';
//         if (($na{1} == '0') && ($na{2} == '0'))
//             return $res . ' ' . $this->mag[$this->magind];
//         $res .= $res == '' ? '' : ' And';
//         $t = (int) $na{1};
//         $u = (int) $na{2};
//         switch ($t) {
//             case 0:
//                 $res .= ' ' . $this->units[$u];
//                 break;
//             case 1:
//                 $res .= ' ' . $this->teens[$u];
//                 break;
//             default:
//                 $res .= ' ' . $this->tens[$t] . ' ' . $this->units[$u];
//                 break;
//         }
//         $res .= ' ' . $this->mag[$this->magind];
//         return $res;
//     }
// }
//$subject=$this->Comman->findexamsubjectsresult($students['id'],$students['section']['id'],$students['acedmicyear']);

$this->set('pdf', new TCPDF('P', 'mm', 'A4'));
$pdf = new TCPDF("P", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->AddPage();
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->SetFont('', '', 10, '', 'false');
$vendorshipfroms = $this->Comman->vendorshipfromdetail($users['vendor_id']);
$vendorshipfrom = $this->Comman->vendorgst($users['vendor_id']);
// pr($vendorshipfrom); die;

$findvendornames = $this->Comman->findvendornames($users['vendor_id']);
$podate = date('d-m-Y', strtotime($users['added_time']));
$delivery_date = date('d-m-Y', strtotime($users['delivery_date']));
$supliername = $sup['name'];
if ($co != 0) {
  $beforesavepo = $this->Comman->getbeforerevisedpo($users['purchaseorder_id']);
  $amedmentdate = $podate;
  $podate = date('d-m-Y', strtotime($beforesavepo['added_time']));
}
if ($users['status'] == 'O') {

  $status = "Open";
} else {

  $status = "Close";
}


$logo = WWW_ROOT . "/images/" . $site_details['small_logo'];
$address = $site_details['address1'];
$email = $site_details['email'];
$mobile = $site_details['phone'];
$school_name = $sitesetting['first_name'];
$website = $site_details['website'];

$html .= '
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>GOOD RECEIPT NOTE (GRN)</title><link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">';
$html .= '</head>
<body>
<div style="border: 1px solid #000; width:50%; margin:auto;">
<table width="100%" style="padding: 1px 1px 0px 0px;" align="left">
<tr>
<td style="text-align:left" width="50%" >
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="' . $logo . '" alt="" border="0" style="display:block;" height="62px;"><br>
<span style="display:block; color:#000; font-size:10px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>' . $school_name . '</b></span><br>
</td>

<td style="text-align:left;" width="50%" align="right"><br><br>
' . $address . '
<br>
<b>Phone</b>
:&nbsp; ' . $mobile . ' <br><b>Email</b>
: <u>
' . $email . '</u><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Website</b> : &nbsp;' . $website . '
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
<td width="25%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:20px; line-height:25px;">GRN No. </td>
<td width="5%" style="text-align:center; color:#000; font-size:8px; height:12px; line-height:25px;">:</td>
<td width="66%" style=" text-align:left; color:#000; font-size:8px; height:20px; line-height:25px;">
 ' . $users['id'] . '

 
</td>
<td width="2%" style="height:20px; line-height:25px;"></td>
</tr>


<tr>
<td width="2%" style="height:20px; line-height:25px;"></td>
<td width="25%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:20px; line-height:25px;">Inward Date</td>
<td width="5%" style="text-align:center; color:#000; font-size:8px; height:20px; line-height:25px;">:</td>
<td width="66%" style=" text-align:left; color:#000; font-size:8px; height:20px; line-height:25px;">
 ' . date("d-m-Y", strtotime($users['inwarddate'])) . '
 
</td>
<td width="2%" style="height:20px; line-height:25px;"></td>
</tr>


<tr>
<td width="2%" style="height:20px; line-height:25px;"></td>
<td width="25%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:20px; line-height:25px;">Bill Date</td>
<td width="5%" style="text-align:center; color:#000; font-size:8px; height:20px; line-height:25px;">:</td>
<td width="66%" style=" text-align:left; color:#000; font-size:8px; height:20px; line-height:25px;"> ' . date("d-m-Y", strtotime($users['bill_date'])) . '

 
</td>
<td width="2%" style="height:20px; line-height:25px;"></td>
</tr>

<tr>
<td width="2%" style="height:20px; line-height:25px;"></td>
<td width="25%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:20px; line-height:25px;">Bill No </td>
<td width="5%" style="text-align:center; color:#000; font-size:8px; height:20px; line-height:25px;">:</td>
<td width="66%" style=" text-align:left; color:#000; font-size:8px; height:20px; line-height:25px;">' . $users['bill_no'] . '

 
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
' . $vendorshipfrom['gst_number'] . ' 
</td>
<td width="2%" style="height:20px; line-height:25px;"></td>
</tr>


<tr>
<td width="2%" style="height:20px; line-height:25px;"></td>
<td width="30%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:20px; line-height:25px;">Vendor Name</td>
<td width="5%" style="text-align:center; color:#000; font-size:8px; height:20px; line-height:25px;">:</td>
<td width="61%" style=" text-align:left; color:#000; font-size:8px; height:20px; line-height:25px;">
' . $findvendornames['name'] . '
 
</td>
<td width="2%" style="height:20px; line-height:25px;"></td>
</tr>





<tr>
<td width="2%" style="height:20px; line-height:25px;"></td>
<td width="30%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:20px; line-height:25px;">PO No. </td>
<td width="5%" style="text-align:center; color:#000; font-size:8px; height:20px; line-height:25px;">:</td>
<td width="61%" style=" text-align:left; color:#000; font-size:8px; height:32px; line-height:25px;">' . $users['purchaseorder_id'] . ' 
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
<td width="18.12%" style="border-top:1px solid #000; border-right:1px solid #000; border-bottom:1px solid #000; color:#000; height:12px; line-height:12px;font-size:8px; font-weight:bold; text-align:left;"> &nbsp; ITEM</td>
<td width="11.76%" style="border-top:1px solid #000; border-right:1px solid #000; border-bottom:1px solid #000; color:#000;height:12px; line-height:12px; font-size:8px; font-weight:bold; text-align:center;"> &nbsp;ORDER QTY.</td>
<td width="11.76%" style="border-top:1px solid #000; border-right:1px solid #000; border-bottom:1px solid #000; color:#000;height:12px; line-height:12px; font-size:8px; font-weight:bold; text-align:center;"> &nbsp; RECEIVED QTY.</td>
<td width="9.6%" style="border-top:1px solid #000; border-right:1px solid #000; border-bottom:1px solid #000; color:#000; height:12px; line-height:12px;font-size:8px; font-weight:bold; text-align:right;"> &nbsp; RATE &nbsp;</td>
<td width="15.96%" style="border-top:1px solid #000; border-right:1px solid #000; border-bottom:1px solid #000; color:#000; height:12px; line-height:12px;font-size:8px; font-weight:bold; text-align:right;"> &nbsp; PRICE (INR) &nbsp;</td>
<td width="9.6%" style="border-top:1px solid #000; border-right:1px solid #000; border-bottom:1px solid #000; color:#000;height:12px; line-height:12px; font-size:8px; font-weight:bold; text-align:center;"> &nbsp; TAX RATE</td>
<td width="9.6%" style="border-top:1px solid #000; border-right:1px solid #000; border-bottom:1px solid #000; color:#000;height:12px; line-height:12px; font-size:8px; font-weight:bold; text-align:right;"> &nbsp; TAX AMT &nbsp;</td>
<td width="9.6%" style="border-top:1px solid #000;  border-bottom:1px solid #000; color:#000; height:12px; line-height:12px;font-size:8px; font-weight:bold; text-align:right;">AMOUNT &nbsp;</td></tr>';



$s = 1;
foreach ($puritems as $value) {

  

  $getpo = $this->Comman->getpostockitem($value['po_id'], $value['item_id']);
  $itemname = $this->Comman->getitemname($value['item_id']);

  // pr($getpo);exit;
  $gettaxparent = $this->Comman->gettaxnameparent($value['tax_id']);
  if (empty($gettaxparent)) {

    $gettaxparent = $this->Comman->gettaxname2($value['tax_id']);
  }
  $i = 0;
  $taxx = '';

  foreach ($gettaxparent as $hh => $ty) {
    $taxx .= $ty['tax'] . '<br> &nbsp;';
    $i++;
  }

  if ($i == 2) {

    $taaxx = $value['tax'] / $i;
    $taxxs = number_format((float) $taaxx, 2, '.', '') . "<br> &nbsp;" . number_format((float) $taaxx, 2, '.', '');
  } else {
    $taxxs = number_format((float) $value['tax'], 2, '.', '');
  }

  if ($value['cost_price'] == $value['amount']) {
    $taxstatus = 'Tax Included';
  } else {
    $taxstatus = 'Tax Excluded';
  }

  $html .= '<tr>
<td width="4%" style=" border-right:1px solid #000; border-bottom:1px solid #000; color:#000;height:12px; line-height:10px; font-size:8px;  text-align:left;"> ' . $s . '.</td>

<td width="18.12%" style=" border-right:1px solid #000; border-bottom:1px solid #000; color:#000;height:12px; line-height:10px; font-size:8px;  text-align:left;"> &nbsp; ' . Ucfirst(($itemname['item_name'])) . '</td>
<td width="11.76%" style=" border-right:1px solid #000; border-bottom:1px solid #000; color:#000;height:12px; line-height:10px; font-size:8px;text-align:center;"> &nbsp; ' . $getpo['item_qty'] . ' ' . $itemname['unit_name'] . '</td>
<td width="11.76%" style=" border-right:1px solid #000; border-bottom:1px solid #000; color:#000;height:12px; line-height:10px; font-size:8px;text-align:center;"> &nbsp; ' . $value['quantity'] . ' ' . $itemname['unit_name'] . '</td>
<td width="9.6%" style=" border-right:1px solid #000; border-bottom:1px solid #000; color:#000;height:12px; line-height:10px; font-size:8px;  text-align:right;"> &nbsp; ' . number_format((float) $value['rate'], 2, '.', '') . ' &nbsp; </td>
<td width="15.96%" style=" border-right:1px solid #000; border-bottom:1px solid #000; color:#000;height:12px; line-height:10px; font-size:8px;  text-align:right;"> &nbsp; ' . number_format((float) $value['cost_price'], 2, '.', '') . ' &nbsp;</td>
<td width="9.6%" style=" border-right:1px solid #000; border-bottom:1px solid #000; color:#000;height:12px; line-height:10px; font-size:8px; text-align:center;"> &nbsp;' . $taxx . '</td>

<td width="9.6%" style=" border-right:1px solid #000; border-bottom:1px solid #000; color:#000; height:12px; line-height:10px; font-size:8px;  text-align:right;"> &nbsp;' . $taxxs . ' &nbsp;</td>

<td width="9.6%" style="  border-bottom:1px solid #000; color:#000;height:12px; line-height:10px; font-size:9px;  text-align:right;"> &nbsp; ' . number_format((float) $value['amount'], 2, '.', '') . ' &nbsp;</td>
</tr>



';

  $totalrate += $value['rate'];
  $totalqua += $value['quantity'];
  $totaltax += $value['tax'];
  $totalamaunt += $value['amount'];
  $s++;
}



$netamt = $totalamaunt + $users->freight;
$obj = new toWords($netamt);
$html .= '<tr>
<td width="71.20%" style="font-weight:bold; border:1px solid #000; color:#000;height:12px; line-height:12px; font-size:8px;  text-align:right;"> Amount &nbsp;</td>
<td width="10%" style="font-weight:bold; border-bottom:1px solid #000; color:#000;height:12px; line-height:12px; font-size:8px;  text-align:center;">'.$taxstatus.'</td>
<td width="18.8%" style="  border-bottom:1px solid #000; color:#000;height:12px; line-height:12px; font-size:8px;  text-align:right;"> &nbsp;&nbsp;&nbsp;' . number_format((float) $totalamaunt, 2, '.', '') . ' &nbsp;</td>
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


<td width="23%" style=" text-align:right; color:#000; font-size:8px; height:12px; line-height:12px;">
&nbsp;&nbsp;' . number_format((float) $users->freight, 2, '.', '') . ' &nbsp;
 
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
' . $obj->get_words() . '  
</td>

</tr>
</table>
</td>
<td width="36%">
<table width="100%">
<tr>

<td width="77%" style="font-weight:bold; text-align:left;border-bottom:1px solid #000;  color:#000; font-size:8px; height:12px; line-height:12px;">&nbsp;Total Amount</td>


<td width="23%" style="border-bottom:1px solid #000;  text-align:right; color:#000; font-size:8px; height:12px; line-height:12px;">
' . number_format((float) $netamt, 2, '.', '') . ' &nbsp;
 
</td>

</tr>

</table>
</td>
</tr>
</table>


<br>
<table width="100%">
<tr >

<td width="20%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;"> &nbsp;&nbsp;&nbsp;Remarks</td>

<td width="80%" style=" text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">
' . ucfirst(strtolower($users->remark)) . '
 
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
<td width="25%" style="height:30px; text-align:left; font-size:8px; border-top:1px solid #000; height:30px; line-height:16px;">&nbsp;<b>For</b>&nbsp;' . $vendorshipfrom['name'] . '</td>
<td width="15%" style="height:30px; text-align:center; font-size:8px; border-top:1px solid #000; height:30px; line-height:16px;"><b>Inspected By </b> </td>
<td width="15%" style="height:30px; text-align:center; font-size:8px; border-top:1px solid #000; height:30px; line-height:16px;"><b>Store Incharge </b></td>
<td width="15%" style="height:30px; text-align:center; font-size:8px; border-top:1px solid #000; height:30px; line-height:16px;"><b>Checked by </b></td>

<td width="30%" style="height:30px; text-align:center; font-size:8px; border-top:1px solid #000; height:30px; line-height:16px;"> <b>Signature Authority</b> </td>
</tr>


</table>
</div>

</body></html>';
// echo $html;
// die;
$pdf->WriteHTML($html, true, false, true, false, '');
ob_end_clean();
$date = date('d-m-Y');
echo $pdf->Output('GRN_Details_' .$users['id']. '-' . $date . '.pdf');
exit;

///status line of footer
// <tr>
// <td width="2%" style="height:20px; line-height:25px;"></td>
// <td width="25%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:20px; line-height:25px;">Status </td>
// <td width="5%" style="text-align:center; color:#000; font-size:8px; height:20px; line-height:25px;">:</td>
// <td width="66%" style=" text-align:left; color:#000; font-size:8px; height:20px; line-height:25px;">
// ' . $status . '
// </td>
// <td width="2%" style="height:20px; line-height:25px;"></td>
// </tr>