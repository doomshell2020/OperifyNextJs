<?php
class xtcpdf extends TCPDF
{
}

$this->set('pdf', new TCPDF('P', 'mm', 'A4'));
$pdf = new TCPDF("P", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);


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
    $w .= $this->single_word($crore, "Crore ");
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



$logo = WWW_ROOT . "/images/" . $site_details['small_logo'];
$address = $site_details['address1'];
$email = $site_details['email'];
$mobile = $site_details['phone'];
$website = $site_details['website'];
$gst_no = $site_details['gst_no'];
$pan_no = $site_details['pan_number'];
$school_name = $sitesetting['first_name'];


foreach ($users as $povalues) {
  // set document information
  $pdf->SetCreator(PDF_CREATOR);
  $pdf->SetPrintHeader(false);
  $pdf->SetPrintFooter(true);
  $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
  $pdf->AddPage();
  $pdf->SetFont('', '', 10, '', 'false');
  $pdf->SetMargins(10, 10, 10, 0);



  $vendorshipfrom = $this->Comman->vendorgst($povalues['vendor_id']);
  $podate = date('d-m-Y', strtotime($povalues['added_time']));

  $delivery_date = date('d-m-Y', strtotime($povalues['delivery_date']));
  $supliername = $vendorshipfrom['name'];
  if ($povalues['is_revised'] != 0) {
    $amedmentdate = date('d-m-Y', strtotime($povalues['revised_date']));
  }



  $html .= '
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Purchase Order</title><link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">';
  $html .= '</head>
<body style="border: 1px solid #000;">
<div style="border: 1px solid #000; ">

<!----------------------------------------------------------------------------------------------------------------------------header start  -->

<table width="100%">
<tr>
<td>
<table width="100%" style="padding: 1px 1px 0px 0px;" align="left">
<tr>
<td style="text-align:left" width="50%" >
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
</table><br><hr>


<table width="100%">
<tr>
<td width="100%" style="height:15px; line-height:18px; color:#000; text-align:center; border-top:1px solid #000; border-bottom:1px solid #000; font-size:14px; font-weight;bold;">Purchase Order</td>
</tr>
</table>
<table width="100%">
<tr>
<td width="50%" style="border-right:1px solid #000;">

<table width="100%">

<tr>
<td width="2%" style="height:12px; line-height:12px;"></td>
<td width="25%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">TO</td>
<td width="5%" style="text-align:center; color:#000; font-size:8px; height:12px; line-height:12px;">:</td>
<td width="66%" style=" text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">
<strong style="font-weight:bold; font-size:8px; text-align:left;">' . $supliername . '</strong><br>
' . nl2br($vendorshipfrom['address']) . '
</td>
<td width="2%" style="height:12px; line-height:12px;"></td>
</tr>


<tr>
<td width="2%" style="height:12px; line-height:12px;"></td>
<td width="25%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">GST No.</td>
<td width="5%" style="text-align:center; color:#000; font-size:8px; height:12px; line-height:12px;">:</td>
<td width="66%" style=" text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">
' . $vendorshipfrom['gst_number'] . '
 
</td>
<td width="2%" style="height:12px; line-height:12px;"></td>
</tr>


<tr>
<td width="2%" style="height:12px; line-height:12px;"></td>
<td width="25%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">State</td>
<td width="5%" style="text-align:center; color:#000; font-size:8px; height:12px; line-height:12px;">:</td>
<td width="66%" style=" text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">
' . $vendorshipfrom['state']['name'] . '
 
</td>
<td width="2%" style="height:12px; line-height:12px;"></td>
</tr>


<tr>
<td width="2%" style="height:12px; line-height:12px;"></td>
<td width="25%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">Phone No. </td>
<td width="5%" style="text-align:center; color:#000; font-size:8px; height:12px; line-height:12px;">:</td>
<td width="66%" style=" text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">
' . $vendorshipfrom['contact_no'] . '
 
</td>
<td width="2%" style="height:12px; line-height:12px;"></td>
</tr>

<tr>
<td width="2%" style="height:12px; line-height:12px;"></td>
<td width="25%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">Email</td>
<td width="5%" style="text-align:center; color:#000; font-size:8px; height:12px; line-height:12px;">:</td>
<td width="66%" style=" text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">
' . $vendorshipfrom['email'] . '
 
</td>
<td width="2%" style="height:12px; line-height:12px;"></td>
</tr>
</table>
</td>




<td width="50%">

<table width="100%">

<tr>
<td width="2%" style="height:12px; line-height:12px;"></td>
<td width="30%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">Purchase Order No. </td>
<td width="5%" style="text-align:center; color:#000; font-size:8px; height:12px; line-height:12px;">:</td>
<td width="61%" style=" text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">
' . $povalues['purchaseorder_id'] . ' 
</td>
<td width="2%" style="height:12px; line-height:12px;"></td>
</tr>



<tr>
<td width="2%" style="height:12px; line-height:12px;"></td>
<td width="30%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">Purchase Order Date</td>
<td width="5%" style="text-align:center; color:#000; font-size:8px; height:12px; line-height:12px;">:</td>
<td width="61%" style=" text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">
' . $podate . '
 
</td>
<td width="2%" style="height:12px; line-height:12px;"></td>
</tr>


<tr>
<td width="2%" style="height:12px; line-height:12px;"></td>
<td width="30%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">Delivery Date</td>
<td width="5%" style="text-align:center; color:#000; font-size:8px; height:12px; line-height:12px;">:</td>
<td width="61%" style=" text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">
' . $delivery_date . '
 
</td>
<td width="2%" style="height:12px; line-height:12px;"></td>
</tr>





<tr>
<td width="2%" style="height:12px; line-height:12px;"></td>
<td width="30%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">Amendment No </td>
<td width="5%" style="text-align:center; color:#000; font-size:8px; height:12px; line-height:12px;">:</td>
<td width="61%" style=" text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">';
  if ($povalues['is_revised'] != 0) {
    $html .= $povalues['is_revised'] . '&nbsp;(<b>Date : </b>' . $amedmentdate . ' )';
  } else {
    $html .= '---';

  }
  $html .= '</td>
<td width="2%" style="height:12px; line-height:12px;"></td>
</tr>
</table>
</td>

</tr>
</table>




<table width="100%">
<tr>
<td width="100%" style="text-align:center; font-size:8px; color:#000; height:20px; line-height:20px; border-top:1px solid #000; border-bottom:1px solid #000;">
Please Supply the undermentioned materials and send us your acceptance per return post.
</td>
</tr>
</table>



<table width="100%">
<tr>
<td width="50%" style="border-right:1px solid #000;">

<table width="100%">

<tr>
<td width="2%" style="height:12px; line-height:12px"></td>
<td width="25%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:12px; line-height:12px">Bill To </td>
<td width="5%" style="text-align:center; color:#000; font-size:8px; height:12px; line-height:12px">:</td>
<td width="66%" style=" text-align:left; color:#000; font-size:8px; height:12px; line-height:12px">
<strong style="font-weight:bold; font-size:8px; text-align:left;">' . $school_name . '</strong><br>
' . $address . '

</td>
<td width="2%" style="height:12px; line-height:12px"></td>
</tr>


<tr>
<td width="2%" style="height:12px; line-height:12px;"></td>
<td width="25%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">GSTIN </td>
<td width="5%" style="text-align:center; color:#000; font-size:8px; height:12px; line-height:12px;">:</td>
<td width="66%" style=" text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">
' . $gst_no . '
 
</td>
<td width="2%" style="height:12px; line-height:12px;"></td>
</tr>

<tr>
<td width="2%" style="height:12px; line-height:12px;"></td>
<td width="25%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">PAN </td>
<td width="5%" style="text-align:center; color:#000; font-size:8px; height:12px; line-height:12px;">:</td>
<td width="66%" style=" text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">
' . $pan_no . '
 
</td>
<td width="2%" style="height:12px; line-height:12px;"></td>
</tr>



<tr>
<td width="2%" style="height:12px; line-height:12px;"></td>
<td width="25%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">State </td>
<td width="5%" style="text-align:center; color:#000; font-size:8px; height:12px; line-height:12px;">:</td>
<td width="66%" style=" text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">
Rajasthan

 
</td>
<td width="2%" style="height:12px; line-height:12px;"></td>
</tr>

<tr>
<td width="2%" style="height:12px; line-height:12px;"></td>
<td width="25%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">Phone No.</td>
<td width="5%" style="text-align:center; color:#000; font-size:8px; height:12px; line-height:12px;">:</td>
<td width="66%" style=" text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">
' . $mobile . '
 
</td>
<td width="2%" style="height:12px; line-height:12px;"></td>
</tr>

</table>
</td>




<td width="50%">

<table width="100%">

<tr>
<td width="2%" style="height:12px; line-height:12px"></td>
<td width="30%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:12px; line-height:12px">Consignee Name  </td>
<td width="5%" style="text-align:center; color:#000; font-size:8px; height:12px; line-height:12px">:</td>
<td width="61%" style=" text-align:left; color:#000; font-size:8px; height:12px; line-height:12px"><b>
' . $school_name . '</b> 
</td>
<td width="2%" style="height:12px; line-height:12px"></td>
</tr>


<tr>
<td width="2%" style="height:12px; line-height:12px;"></td>
<td width="30%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">And Address Details
</td>
<td width="5%" style="text-align:center; color:#000; font-size:8px; height:12px; line-height:12px;">:</td>
<td width="61%" style=" text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">
' . $address . '
 
</td>
<td width="2%" style="height:12px; line-height:12px;"></td>
</tr>
<tr>
<td width="2%" style="height:12px; line-height:12px;"></td>
<td width="30%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">Email</td>
<td width="5%" style="text-align:center; color:#000; font-size:8px; height:12px; line-height:12px;">:</td>
<td width="61%" style=" text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">
' . $email . '
 
</td>
<td width="2%" style="height:12px; line-height:12px;"></td>
</tr>
<tr>
<td width="2%" style="height:12px; line-height:12px;"></td>
<td width="30%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">GSTIN </td>
<td width="5%" style="text-align:center; color:#000; font-size:8px; height:12px; line-height:12px;">:</td>
<td width="61%" style=" text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">
' . $gst_no . '
 
</td>
<td width="2%" style="height:12px; line-height:12px;"></td>
</tr>

<tr>
<td width="2%" style="height:12px; line-height:12px;"></td>
<td width="30%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">PAN </td>
<td width="5%" style="text-align:center; color:#000; font-size:8px; height:12px; line-height:12px;">:</td>
<td width="61%" style=" text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">
' . $pan_no . '
 
</td>
<td width="2%" style="height:12px; line-height:12px;"></td>
</tr>
</table>

</td>
</tr>
</table>

</td>
</tr>
</table>

<!----------------------------------------------------------------------------------------------------------------------------header end  -->


<!----------------------------------------------------------------------------------------------------------------------------Loop start  -->

<table width="100%">
<tr>
<td width="4%" style="border-top:1px solid #000; border-right:1px solid #000; border-bottom:1px solid #000; color:#000; max-height:12px !important; line-height:9px; font-size:8px; font-weight:bold; text-align:left;"> S.No</td>

<td width="39%" style="border-top:1px solid #000; border-right:1px solid #000; border-bottom:1px solid #000; color:#000; max-height:12px !important; line-height:9px;font-size:8px; font-weight:bold; text-align:left;"> &nbsp; ITEM</td>


<td width="9%" style="border-top:1px solid #000; border-right:1px solid #000; border-bottom:1px solid #000; color:#000; max-height:12px !important; line-height:9px; font-size:8px; font-weight:bold; text-align:center;"> QUANTITY  &nbsp; </td>
<td width="9%" style="border-top:1px solid #000; border-right:1px solid #000; border-bottom:1px solid #000; color:#000; max-height:12px !important; line-height:9px;font-size:8px; font-weight:bold; text-align:center;">UNIT PRICE &nbsp; </td>
<td width="10%" style="border-top:1px solid #000; border-right:1px solid #000; border-bottom:1px solid #000; color:#000; max-height:12px !important; line-height:9px;font-size:8px; font-weight:bold; text-align:center;"> PRICE &nbsp;</td>
<td width="8%" style="border-top:1px solid #000; border-right:1px solid #000; border-bottom:1px solid #000; color:#000; max-height:12px !important; line-height:9px; font-size:8px; font-weight:bold; text-align:center;">GST(%) &nbsp;</td>

<td width="10%" style="border-top:1px solid #000; border-right:1px solid #000; border-bottom:1px solid #000; color:#000; max-height:12px !important; line-height:9px; font-size:8px; font-weight:bold; text-align:center;"> GST VALUE &nbsp;</td>

<td width="11%" style="border-top:1px solid #000;  border-bottom:1px solid #000; color:#000; max-height:12px !important; line-height:9px;font-size:8px; font-weight:bold; text-align:center;"> TOTAL PRICE  &nbsp;</td>
</tr>

';


  $s = 1;
  $totalrate = 0;
  $totalqua = 0;
  $totaltax = 0;
  $totalamaunt = 0;

  $puritems = $this->Comman->getpurchaseorderdetails($povalues['purchaseorder_id'], $povalues['id']);
  foreach ($puritems as $value) {
    $itemname = $this->Comman->getitemname($value['item_id']);
    $PurchaseDetails = $this->Comman->PurchaseOrderDetails($value['po_id'], $value['item_id']);

    $sizename = $this->Comman->getsizename($value['additem']['size_id']);
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

    $costprice = $value['item_qty'] * $value['item_amt'];
    $totalamount = $value['item_total_amount'];
    if ($costprice == $totalamount) {
      $taxstatus = 'Tax Included';
    } else {
      $taxstatus = 'Tax Excluded';
    }

    $html .= '<tr>
<td  style=" border:1px solid #000; color:#000; height:12px !important; line-height:9px; font-size:8px;  text-align:left;"> ' . $s . '.</td>
<td  style=" border:1px solid #000; color:#000; height:12px !important; line-height:9px; font-size:8px;  text-align:left;"> &nbsp; ' . ucfirst(($itemname['item_name'])) . '</td>
<td  style=" border:1px solid #000; color:#000; height:12px !important; line-height:9px; font-size:8px;text-align:center;">' . $value['item_qty'] . ' ' . $value['uom'] . '  &nbsp; </td>
<td  style=" border:1px solid #000; color:#000; height:12px !important; line-height:9px; font-size:8px;  text-align:center;"> ' . number_format((float) $value['item_amt'], 2, '.', '') . ' &nbsp; </td>
<td  style=" border:1px solid #000; color:#000; height:12px !important; line-height:9px; font-size:8px;  text-align:center;"> ' . number_format((float) $costprice, 2, '.', '') . ' &nbsp; </td>
<td  style=" border:1px solid #000; color:#000; height:12px !important; line-height:9px; font-size:8px; text-align:center;"> ' . $taxx . '</td>
<td  style=" border:1px solid #000; color:#000; height:12px !important; line-height:9px; font-size:8px;  text-align:center;"> ' . number_format((float) $value['item_tax_amt'], 2, '.', '') . ' &nbsp;</td>
<td  style=" border:1px solid #000; color:#000; height:12px !important; line-height:9px; font-size:8px;  text-align:center;"> ' . number_format((float) $value['item_total_amount'], 2, '.', '') . ' &nbsp;</td>

</tr>';

    $totalrate += $value['rate'];
    $totalqua += $value['quantity'];
    $totaltax += $value['tax'];
    $totalamaunt += $value['item_total_amount'];

    $s++;
  }

  $netamt = $totalamaunt + $users->freight;
  $obj = new toWords($netamt);
  $html .= '

</table>

<!----------------------------------------------------------------------------------------------------------------------------Loop end  -->


<!----------------------------------------------------------------------------------------------------------------------------Amount Start  -->

<table width="100%">
<tr>
<td>
<table width="100%">

<tr>

<td width="64%" style="border-top:1px solid #000; border-right:1px solid #000;">
<table width="100%">
<tr>
<td width="20%" style="font-weight:bold; text-align:left;border-bottom:1px solid #000;  color:#000; font-size:8px; height:12px; line-height:12px;"> &nbsp;&nbsp;&nbsp;Amount <br> &nbsp;&nbsp;(In Words)</td>
<td width="80%" style=" text-align:left; color:#000; border-bottom:1px solid #000; font-size:8px; height:12px; line-height:12px;">
' . $obj->get_words() . ' 
</td>
</tr>
</table>
</td>

<td width="36%">
<table width="100%">
<tr rowspan="1">
<td width="74%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:12px;line-height:12px; ">&nbsp; &nbsp;Grand Total (INR)</td>
<td width="25%" style=" text-align:right; color:#000; font-size:8px; height:12px;line-height:12px; ">
' . number_format((float) $netamt, 2, '.', '') . '  &nbsp;
</td>
<td width="1%"></td>
</tr>
</table>
</td>

</tr>
</table>

<table width="100%" cellpadding="3px" style="border-top:1px solid #000;">
<tr >

<td width="09%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;"> &nbsp;Remarks</td>

<td width="91%" style=" text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">
' . nl2br(ucfirst(strtolower($users->remark))) . '
 
</td>
</tr>
</table>

</td>
</tr>
</table>
<hr>


<!-----------------------------------------------------------------------------------------------------------------------------------Amount End  -->


<!----------------------------------------------------------------------------------------------------------------------------Terms and Conditions Start  -->';

  // $pdf->SetAutoPageBreak(true);
  $html .= '<table width="100%">
<tr>
<td>

<table width="100%" cellpadding="3px" style="border-top:1px solid #000;">
<tr>
<td width="100%" style="border-top:1px solid #000; border-left:1px solid #000;border-right:1px solid #000;border-bottom:1px solid #000; height:12px;line-height:12px; font-weight:bold;">
Terms and Conditions
</td>
</tr>
</table>


<table width="100%">';
  $paymenterms = $this->Comman->paymenttermsdetail();
  $is = 1;
  foreach ($paymenterms as $kk => $item) {
    $html .= '<tr>
<td width="1%" style="height:12px; line-height:12px;"></td>
<td width="95%" style="font-size:8px; text-align:left; height:12px; line-height:12px;">' . $item['description'] . '</td></tr>';
    $is++;
  }
  $html .= '<tr>
<td width="1%" style="height:12px; line-height:12px;"></td>
<td width="95%" style="font-size:8px; text-align:left; height:12px; line-height:12px;">Please send the document through Courier Mode.<br>
<b>Payment Terms - 
' . $users['payment_term'] . '</b><br>Through Your Banker as per RBI directive under intimation to us</td></tr>';

  $html .= '</table>

<table width="100%">
<tr>
<td width="25%" style="text-align:left; border-top:1px solid #000;">
&nbsp;&nbsp; 
</td>

<td width="25%" style="text-align:center; border-top:1px solid #000;">
</td>

<td width="50%" style="text-align:right; border-top:1px solid #000;">
For : <b>Tirupati Plastomatics Pvt. Ltd.</b><br><br><br>
Authorised Signatory &nbsp;
</td>
</tr>
</table>
</td>
</tr>
</table>
	<table width="100%" style="border-top:1px solid #000;">
	<tr>
	<td width="100%" style="text-align:center; font-size:8px;font-weight:bold; color:#000; ">Subject to Jaipur Jurisdiction
</td>
	</tr>
	</table>


    </td>
</tr>
</table>

<!----------------------------------------------------------------------------------------------------------------------------Terms and Conditions End  -->

</div>

</body></html>';
  $pdf->WriteHTML($html, true, false, true, false, '');
}

ob_end_clean();
$date = date('d-m-Y');
echo $pdf->Output('PO-' . $users['purchaseorder_id'] . '.pdf');
exit;

// echo $html; die;

