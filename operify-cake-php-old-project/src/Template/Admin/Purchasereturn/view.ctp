<?php 
// pr($puritems); 
class xtcpdf extends TCPDF
{

}



class toWords{

    public function __construct($amount){
      $this->amount=$amount;
      $this->hasPaisa=false;
      $arr=explode(".",$this->amount);
      $this->rupees=$arr[0];
      if(isset($arr[1])&&((int)$arr[1])>0){
        if(strlen($arr[1])>2){
          $arr[1]=substr($arr[1],0,2);
        }
        $this->hasPaisa=true;
        $this->paisa=$arr[1];
      }
    }
    
    public function get_words(){
      $w="";
      $crore=(int)($this->rupees/10000000);
      $this->rupees=$this->rupees%10000000;
      $w.=$this->single_word($crore,"Crore ");
      $lakh=(int)($this->rupees/100000);
      $this->rupees=$this->rupees%100000;
      $w.=$this->single_word($lakh,"Lakh ");
      $thousand=(int)($this->rupees/1000);
      $this->rupees=$this->rupees%1000;
      $w.=$this->single_word($thousand,"Thousand  ");
      $hundred=(int)($this->rupees/100);
      $w.=$this->single_word($hundred,"Hundred ");
      $ten=$this->rupees%100;
      $w.=$this->single_word($ten,"");
      $w.="Rupees ";
      if($this->hasPaisa){
        if($this->paisa[0]=="0"){
          $this->paisa=(int)$this->paisa;
        }
        else if(strlen($this->paisa)==1){
          $this->paisa=$this->paisa*10;
        }
        $w.=" and ".$this->single_word($this->paisa," Paisa");
      }
      return $w." Only";
    }
  
    private function single_word($n,$txt){
      $t="";
      if($n<=19){
        $t=$this->words_array($n);
      }else{
        $a=$n-($n%10);
        $b=$n%10;
        $t=$this->words_array($a)." ".$this->words_array($b);
      }
      if($n==0){
        $txt="";
      }
      return $t." ".$txt;
    }
  
    private function words_array($num){
      $n=[0=>"", 1=>"One", 2=>"Two", 3=>"Three", 4=>"Four", 5=>"Five", 6=>"Six", 7=>"Seven", 8=>"Eight", 9=>"Nine", 10=>"Ten", 11=>"Eleven", 12=>"Twelve", 13=>"Thirteen", 14=>"Fourteen", 15=>"Fifteen", 16=>"Sixteen", 17=>"Seventeen", 18=>"Eighteen", 19=>"Nineteen", 20=>"Twenty", 30=>"Thirty", 40=>"Forty", 50=>"Fifty", 60=>"Sixty", 70=>"Seventy", 80=>"Eighty", 90=>"Ninety", 100=>"Hundred",];
      return $n[$num];
    }
  }



$this->set('pdf', new TCPDF('P', 'mm', 'A4'));
$pdf = new TCPDF("P", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(true);
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->AddPage();
$pdf->SetFont('', '', 10, '', 'false');
$pdf->SetMargins (10, 10, 10 , 0);


$vendorshipfroms=$this->Comman->vendorshipfromdetail($users['vendor_id']);
$vendorshipfrom=$this->Comman->vendorgst($users['vendor_id']);

$vendorbilltodetail=$this->Comman->vendorbilltodetail($users['vendor_id']);
$returnDate = date('d-m-Y',strtotime($users['retrundate']));


$inwardDate = strtotime($users['retrundate']);
$financialYearStart = date('Y', $inwardDate) - ($inwardDate < strtotime(date('Y').'-04-01') ? 1 : 0);
$financialYearEnd = $financialYearStart + 1;
$financial_year =  $financialYearStart.'-'.$financialYearEnd;

$supliername = $sup['name'];   

$logo  = SITE_URL."/images/".$site_details['small_logo'];
$address  =  $site_details['address1'];
$email  =  $site_details['email'];
$mobile  =  $site_details['phone'];
$website  =  $site_details['website'];
$gst_no  =  $site_details['gst_no'];
$pan_no  =  $site_details['pan_number'];
$school_name  =  $sitesetting['first_name'];

$ac_holder  =  $site_details['ac_holder'];
$bank_name  =  $site_details['bank_name'];
$ac_no  =  $site_details['account_number'];
$bank_branch  =  $site_details['bank_branch_name'];
$ifsc  =  $site_details['ifsc'];

$html .= '
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Purchase Order</title><link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">';
$html.='</head>
<body style="border: 1px solid #000;">
<div style="border: 1px solid #000; ">

<!---------------------------------------------------------------------------------------------------------------------
-------header start  -->

<table width="100%">
<tr>
<td>
<table width="100%" style="padding: 1px 1px 0px 0px;" align="left">
<tr>
<td style="text-align:left" width="50%" >
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
</table><br><hr>


<table width="100%">
<tr>
<td width="100%" style="height:15px; line-height:18px; color:#000; text-align:center; border-top:1px solid #000; border-bottom:1px solid #000; font-size:14px; font-weight;bold;">Debit Note</td>
</tr>
</table>



<table width="100%" >
<tr>
<td width="50%" style="border-right:1px solid #000;">

<table width="100%" style= "border-bottom:1px solid #000; width:100%;">



<tr>
<td width="2%" style="height:12px; line-height:12px"></td>
<td width="25%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:12px; line-height:12px">From</td>
<td width="5%" style="text-align:center; color:#000; font-size:8px; height:12px; line-height:12px">:</td>
<td width="66%" style=" text-align:left; color:#000; font-size:8px; height:12px; line-height:12px">
<strong style="font-weight:bold; font-size:8px; text-align:left;">'.$school_name.'</strong><br>
'.$address.'

</td>
<td width="2%" style="height:12px; line-height:12px"></td>
</tr>


<tr>
<td width="2%" style="height:12px; line-height:12px;"></td>
<td width="25%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">GSTIN </td>
<td width="5%" style="text-align:center; color:#000; font-size:8px; height:12px; line-height:12px;">:</td>
<td width="66%" style=" text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">
'.$gst_no.'
 
</td>
<td width="2%" style="height:12px; line-height:12px;"></td>
</tr>

<tr>
<td width="2%" style="height:12px; line-height:12px;"></td>
<td width="25%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">PAN </td>
<td width="5%" style="text-align:center; color:#000; font-size:8px; height:12px; line-height:12px;">:</td>
<td width="66%" style=" text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">
'.$pan_no.'
 
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
'.$mobile.'
 
</td>
<td width="2%" style="height:12px; line-height:12px;"></td>
</tr>

</table>

<table width="100%">

<tr>
<td width="2%" style="height:12px; line-height:12px;"></td>
<td width="25%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">Bill To</td>
<td width="5%" style="text-align:center; color:#000; font-size:8px; height:12px; line-height:12px;">:</td>
<td width="66%" style=" text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">
<strong style="font-weight:bold; font-size:8px; text-align:left;">'.$supliername.'</strong><br>
'.$sup['address'].'
</td>
<td width="2%" style="height:12px; line-height:12px;"></td>
</tr>


<tr>
<td width="2%" style="height:12px; line-height:12px;"></td>
<td width="25%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">GST No.</td>
<td width="5%" style="text-align:center; color:#000; font-size:8px; height:12px; line-height:12px;">:</td>
<td width="66%" style=" text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">
'.$vendorshipfrom['gst_number'].'
 
</td>
<td width="2%" style="height:12px; line-height:12px;"></td>
</tr>


<tr>
<td width="2%" style="height:12px; line-height:12px;"></td>
<td width="25%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">State</td>
<td width="5%" style="text-align:center; color:#000; font-size:8px; height:12px; line-height:12px;">:</td>
<td width="66%" style=" text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">
'.$sup['state']['name'].'
 
</td>
<td width="2%" style="height:12px; line-height:12px;"></td>
</tr>


<tr>
<td width="2%" style="height:12px; line-height:12px;"></td>
<td width="25%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">Phone No. </td>
<td width="5%" style="text-align:center; color:#000; font-size:8px; height:12px; line-height:12px;">:</td>
<td width="66%" style=" text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">
'.$sup['contact_no'].'
 
</td>
<td width="2%" style="height:12px; line-height:12px;"></td>
</tr>

<tr>
<td width="2%" style="height:12px; line-height:12px;"></td>
<td width="25%" style="font-weight:bold; text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">Email</td>
<td width="5%" style="text-align:center; color:#000; font-size:8px; height:12px; line-height:12px;">:</td>
<td width="66%" style=" text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">
'.$sup['email'].'
 
</td>
<td width="2%" style="height:12px; line-height:12px;"></td>
</tr>
</table>

</td>





<td width="50%">

<table width="100%" style="border-bottom:1px solid #000" >
<tr>
<td width="50%" style="border-right:1px solid #000">
<table width="100%" style="padding-left:5px">
  <tr>
  
  <td width="55%" style=" text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">Debited Note No.</td>
  <td width="10%" style="text-align:center; color:#000; font-size:8px; height:12px; line-height:12px;"></td>
  <td width="35%" style=" text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;"></td>
  </tr>
  <tr>
  
  <td width="100%" style=" font-weight:bold; text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">'.$financial_year.'/'.$users['id'].'</td>


  </tr>
</table>
</td>
<td width="50%" style="text-align:center; font-size:8px; color:#000; height:20px; line-height:20px; border-top:1px solid #000; border-bottom:1px solid #000;">
<table width="100%" style="padding-left:5px">
  <tr>
  
  <td width="55%" style=" text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">Dated</td>
 
  <td width="45%" style=" text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">

 
</td>
  </tr>
  <tr>
  
  <td width="100%" style=" font-weight:bold; text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">'.$returnDate.'</td>


  </tr>
</table>
</td>


</tr>
</table>

<table width="100%" style="border-bottom:1px solid #000" >
<tr>
<td width="100%" style="border-right:1px solid #000">
<table width="100%" style="padding-left:5px">
  <tr>
  
  <td width="100%" style=" text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">Orignal Invoice No. & Date</td>
  </tr>
  <tr>
  <td width="100%" style=" font-weight:bold; text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">'.$users['bill_no'].'  &  '.date('d-m-Y',strtotime($users['bill_date'])).'</td>
  </tr>
</table>
</td>
<!-----------
<td width="50%" style="text-align:center; font-size:8px; color:#000; height:20px; line-height:20px; border-top:1px solid #000; border-bottom:1px solid #000;">
<table width="100%" style="padding-left:5px">
  <tr>
  <td width="100%" style=" text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;">Other Preffrence</td>
  </tr>
  <tr>
  <td width="100%" style=" font-weight:bold; text-align:left; color:#000; font-size:8px; height:12px; line-height:12px;"></td>
  </tr>
</table>
</td>
-->

</tr>
</table>

</td>




</tr>
</table>











<!---------------------------------------------------------------------------------------------------------------------
-------header end  -->


<!---------------------------------------------------------------------------------------------------------------------
-------Loop start  -->

<table width="100%">
<tr>
<td width="4%" style="border-top:1px solid #000; border-right:1px solid #000; border-bottom:1px solid #000; color:#000; max-height:12px !important; line-height:12px; font-size:8px; font-weight:bold; text-align:left;"> S.No</td>

<td width="46%" style="border-top:1px solid #000; border-right:1px solid #000; border-bottom:1px solid #000; color:#000; max-height:12px !important; line-height:12px;font-size:8px; font-weight:bold; text-align:left;"> &nbsp; ITEM</td>


<td width="7%" style="border-top:1px solid #000; border-right:1px solid #000; border-bottom:1px solid #000; color:#000; max-height:12px !important; line-height:12px; font-size:8px; font-weight:bold; text-align:right;"> QTY.  &nbsp; </td>
<td width="7%" style="border-top:1px solid #000; border-right:1px solid #000; border-bottom:1px solid #000; color:#000; max-height:12px !important; line-height:12px;font-size:8px; font-weight:bold; text-align:right;"> RATE &nbsp; </td>
<td width="10%" style="border-top:1px solid #000; border-right:1px solid #000; border-bottom:1px solid #000; color:#000; max-height:12px !important; line-height:12px;font-size:8px; font-weight:bold; text-align:right;"> PRICE (INR) &nbsp;</td>
<td width="7%" style="border-top:1px solid #000; border-right:1px solid #000; border-bottom:1px solid #000; color:#000; max-height:12px !important; line-height:12px; font-size:8px; font-weight:bold; text-align:right;"> TAX (%)</td>

<td width="9%" style="border-top:1px solid #000; border-right:1px solid #000; border-bottom:1px solid #000; color:#000; max-height:12px !important; line-height:12px; font-size:8px; font-weight:bold; text-align:right;"> TAX AMT &nbsp;</td>

<td width="10%" style="border-top:1px solid #000;  border-bottom:1px solid #000; color:#000; max-height:12px !important; line-height:12px;font-size:8px; font-weight:bold; text-align:right;"> AMOUNT &nbsp;</td>
</tr>

';


$s = 1;
foreach($puritems as $value){ 
    // if ($s % 9 == 0) {
    //     $html .= '<br pagebreak="true"/>';
    // }
// pr($value);die;
  

$sizename = $this->Comman->getsizename($value['additem']['size_id']);
$gettaxparent = $this->Comman->gettaxnameparent($value['tax_id']);
if(empty($gettaxparent)){
	
	$gettaxparent = $this->Comman->gettaxname2($value['tax_id']);
}
$i=0;
$taxx='';

foreach($gettaxparent as $hh=>$ty){
	$taxx.=$ty['tax'].'<br> &nbsp;';
	$i++;
	
}

if($i==2){
	
	$taaxx=$value['tax'] / $i;
	$taxxs=number_format((float)$taaxx, 2, '.', '')."<br> &nbsp;".number_format((float)$taaxx, 2, '.', '');
}else{
	$taxxs=number_format((float)$value['tax'], 2, '.', '');
	
}

$costprice = $value['quantity']*$value['rate'];
$totalamount = $value['amount'];
if($costprice == $totalamount){
$taxstatus = 'Tax Included';
}else{
$taxstatus = 'Tax Excluded';
}
$html.='<tr>
<td  style=" border:1px solid #000; color:#000; height:12px !important; line-height:10px; font-size:8px;  text-align:left;"> '.$s.'.</td>
<td  style=" border:1px solid #000; color:#000; height:12px !important; line-height:10px; font-size:8px;  text-align:left;"> &nbsp; '.ucfirst(($value['additem']['item_name'])).'</td>
<td  style=" border:1px solid #000; color:#000; height:12px !important; line-height:10px; font-size:8px;text-align:right;">'.$value['quantity'].' '.$value['additem']['measurementunit']['unit_name'].'  &nbsp; </td>
<td  style=" border:1px solid #000; color:#000; height:12px !important; line-height:10px; font-size:8px;  text-align:right;"> '.number_format((float)$value['rate'], 2, '.', '').' &nbsp; </td>
<td  style=" border:1px solid #000; color:#000; height:12px !important; line-height:10px; font-size:8px;  text-align:right;"> '.number_format((float)$value['cost_price'], 2, '.', '').' &nbsp; </td>
<td  style=" border:1px solid #000; color:#000; height:12px !important; line-height:10px; font-size:8px; text-align:center;"> '.$taxx.'</td>
<td  style=" border:1px solid #000; color:#000; height:12px !important; line-height:10px; font-size:8px;  text-align:right;"> '.number_format((float)$value['tax'], 2, '.', '').' &nbsp;</td>
<td  style=" border:1px solid #000; color:#000; height:12px !important; line-height:10px; font-size:8px;  text-align:right;"> '.number_format((float)$value['amount'], 2, '.', '').' &nbsp;</td>

</tr>';


$totalrate += $value['rate'];
$totalqua += $value['quantity'];
$totaltax += $value['tax'];
$totalamaunt += $value['amount']; 
            $s++;          
}


$netamt=$totalamaunt+$users->freight;
$obj    = new toWords($netamt);
$html.='
<tr>
<td width="64%" style=" border:1px solid #000; color:#000;height:12px; line-height:12px; font-size:8px;  text-align:center;"></td>
<td width="10%" style="font-weight:bold;  border:1px solid #000; color:#000;height:12px; line-height:12px; font-size:8px;  text-align:left;"> &nbsp;  Amount</td>
<td width="11.5%" style="font-weight:bold; border-bottom:1px solid #000; color:#000;height:12px; line-height:12px; font-size:8px;  text-align:left;">&nbsp;</td>
<td width="14.5%" style="  border-bottom:1px solid #000; color:#000;height:12px; line-height:12px; font-size:8px;  text-align:right;"> &nbsp;&nbsp;&nbsp;'.number_format((float)$totalamaunt, 2, '.', '').' &nbsp;</td>
</tr>
</table>

<!---------------------------------------------------------------------------------------------------------------------
-------Loop end  -->


<!---------------------------------------------------------------------------------------------------------------------
-------Amount Start  -->









<!---------------------------------------------------------------------------------------------------------------------
--------------Amount End  -->


<!---------------------------------------------------------------------------------------------------------------------
-------Terms and Conditions Start  -->
'; 

// $pdf->SetAutoPageBreak(true);


$html.='<table width="100%">
<tr>
<td >










<table width="100%" style="padding-left:5px">
<tr>
<td width="50%" style=" border-top:1px solid #000;">
<table>

  <tr>
    <td width="100%" style=" font-size:7.8px;">Ammount Chargeble (in words)
    </td>
  </tr>

    <tr>
      <td width="100%" style=" font-size:7.8px; font-weight:bold;">'.$obj->get_words().'</td>
    </tr>

  
    <tr style="">
    <td width="55%" height="25" style="  height:300px; color:#000;  height:12px; line-height:12px;"></td>
    <td width="10%" height="25" style=" height:300px; color:#000; ; height:12px; line-height:12px;"></td>
    <td width="35%" height="25" style=" height:300px; color:#000;  height:12px; line-height:12px;">

 
   
  </td>
    </tr>

    <tr style="">
    <td width="55%" height="25" style=" font-size:7.8px;  height:300px; color:#000;  height:12px; line-height:12px;">Company`s PAN</td>
    <td width="45%" height="25" style=" font-size:7.8px; height:300px; color:#000;  height:12px; line-height:12px;font-weight:bold;">:
    '.$pan_no.'
  </td>



    </tr>
   

</table>
</td>

<td width="50%">
<table width="100%">
    <tr>
<td width=" 100%" style="font-size:7.8px; "> Company`s Bank Details</td>

    </tr>
</table>
<table width="100%">
    <tr>
<td width=" 35%" style="font-size:7.8px; "> A/c holders Name</td>
<td width=" 5%" style=" ">:</td>
<td width=" 60%" style="font-size:7.8px;font-weight:bold;"> '.$ac_holder.'</td>
    </tr>
</table>

<table width="100%">
    <tr>
<td width=" 35%" style="font-size:7.8px; "> Bank Name</td>
<td width=" 5%" style=" ">:</td>
<td width=" 60%" style="font-size:7.8px;font-weight:bold;"> '.$bank_name.'</td>
    </tr>
</table>

<table width="100%">
    <tr>
<td width=" 35%" style="font-size:7.8px; "> A/c No</td>
<td width=" 5%" style=" ">:</td>
<td width=" 60%" style="font-size:7.8px;font-weight:bold;"> '.$ac_no.'</td>
    </tr>
</table>

<table width="100%">
    <tr>
<td width=" 35%" style=" font-size:7.8px;"> Branch & IFS Code</td>
<td width=" 5%" style=" ">:</td>
<td width=" 60%" style="font-size:7.8px;font-weight:bold;"> '.$bank_branch.' & '.$ifsc.'</td>
    </tr>
</table>

<table width="100%">
    <tr>
<td width=" 35%" style="font-size:7.8px; "> SWIFT Code</td>
<td width=" 5%" style=" ">:</td>
<td width=" 60%" style="font-size:7.8px;font-weight:bold;"></td>
    </tr>
</table>

<table width="100%" style="border-left:1px solid #000;">
<tr>
  <td width="100%"  style="text-align:right;height:40px; border-top:1px solid #000;">
   For : <b>Tirupati Plastomatics Pvt. Ltd.</b> <br>
  
   </td>
</tr>

<tr>
<td style="height:10px; text-align:right;">Authorised Signatory &nbsp;</td>
</tr>
</table>
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

<!---------------------------------------------------------------------------------------------------------------------
-------Terms and Conditions End  -->

</div>

</body></html>';

// echo $html; die;
$pdf->WriteHTML($html, true, false, true, false, '');
ob_end_clean();
$date = date('d-m-Y');
echo $pdf->Output('Debit Note-' . $users['id'].'.pdf');
exit;
