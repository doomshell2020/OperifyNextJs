<?php 
//pr($users); die;
class xtcpdf extends TCPDF
{

}

//$subject=$this->Comman->findexamsubjectsresult($students['id'],$students['section']['id'],$students['acedmicyear']);

$this->set('pdf', new TCPDF('L', 'mm', 'A5'));
$pdf = new TCPDF("L", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->AddPage();

$pdf->SetFont('', '', 10, '', 'false');

// pr($users);die;

$var = $this->Comman->getindentw($users['indent_id'],$users['status']);
$logo  = SITE_URL."/images/".$site_details['small_logo'];
$address  =  $site_details['address1'];
$email  =  $site_details['email'];
$mobile  =  $site_details['phone'];







$html .= '
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Result</title><link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">';
$html.='</head>
<body>

<table width="100%" align="left">
<tr>
<td style="text-align:left" width="50%" >
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="'.$logo.'"  alt="" border="0" style="display:block;"
width="130px" height="60px;"><br>
<!---<span style="display:block; color:#000; font-size:8px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<u><br>Affiliated to CBSE Delhi (Affiliation
no.1730236)</u></span>--->
</td>

<td style="text-align:left;" width="50%" align="right"><br><br>
'.$address.',<br>
Phone
:;'.$mobile.'&nbsp;&nbsp;&nbsp;&nbsp;<br>&nbsp;&nbsp;&nbsp;&nbsp;Email
: <u>
'.$email.'</u><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</td>


</tr>

</table>

<br>
<br>

<table cellpadding="10" style="background-color:#fbfafb; border:2px solid black; ">
<tr>
<td>
<table border="1" cellpadding="10" style="background-color:#fbfafb; border:1px solid black;">
<tr style="border:1px solid black;">
<td>
<p style="text-align:center; font-size:14px;font-weight:bold;">Purchase Requisition 
</p>
</td>
</tr>
<tr>
<td width="33.33%" style="text-align:left;font-weight:bold;">Indent No.: ';

if (empty($var)){
$html.=$users['indent_id']." Temporary";
}else{

  $html.=$users['indent_id'];

}
$html.='</td>
<td width="33.33%" style="text-align:left;font-weight:bold;">From: ';

//~ if($users->indent_status == "P"){ $status = "Pending"; }else if($users->indent_status == "A"){ $status = "Approve"; }else if($users->indent_status == "R"){ $status = "Reject"; } 
$tech_id=$this->request->session()->read('Auth.User.user_name');


$html.= $tech_id.' </td>
<td width="33.33%" style="text-align:left;font-weight:bold;">Date: '.date('d-m-Y', strtotime($users['added_time'])).'</td>
</tr>
</table>
</td>
</tr>


<tr>
<td>
<table border="1" cellpadding="10"s>

<tr>
<td width="25%">S.No.</td>
<td width="25%">Item</td>
<td width="25%">Category</td>

<td width="25%">Qty. Requested</td>

</tr>';
  $var = $this->Comman->getindentw($users['indent_id'],$users['status']);

if (empty($var)){

  $var = $this->Comman->getindent($users['indent_id'],$users['status']);
  


}
$i = 1;
foreach($var as $key => $value){
    //pr($value); 
    $itemcarcom = $this->Comman->getitemcatcom($value['item_id']);
    //pr($itemcarcom); die;

    $itemsizename = $this->Comman->getsizename($value['additem']['size_id']);

 
    $qua = $value->quantity;

    //pr($intemcom); die;
    $html.='<tr>
    <td width="25%">'.$i.'</td>
    <td width="25%">'.$value->additem->item_name.'</td>    
    <td width="25%">'.$itemcarcom->itemcategory->category_name.'</td>   
  
    <td width="25%">'.$value->quantity.'</td>
    </tr>';
    $i++;
    $totalquantity += $qua;
   
}  


$html.='<tr>
<td width="78%" style="text-align:right">Total Quantity</td>
<td width="22%">'.$totalquantity.'</td>
</tr>


</table>
</td>
</tr>
</table>
<table width="100%">
<tr>
<td width="100%" style="height:70px;"></td>
</tr>
</table>


<table width="100%">
<tr>
<td width="25%" style="text-align:left; border-top:1px solid #000;">
&nbsp;&nbsp; Signature of Sanctioning Authority 
</td>

<td width="50%" style="text-align:center; ">

</td>

<td width="25%" style="text-align:right; border-top:1px solid #000;">
 Signature of Person Requesting The Items &nbsp;&nbsp;
</td>
</tr>
</table>
</body>
</html>';

$pdf->WriteHTML($html, true, false, true, false, '');
ob_end_clean();
$date = date('d-m-Y');
echo $pdf->Output('Daily-Summary-' . $bordd . '-' . $date . '.pdf');
exit;
?>
