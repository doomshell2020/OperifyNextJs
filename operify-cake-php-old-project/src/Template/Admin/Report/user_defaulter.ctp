<?php
class xtcpdf extends TCPDF
{
}


//$subject=$this->Comman->findexamsubjectsresult($students['id'],$students['section']['id'],$students['acedmicyear']);

$this->set('pdf', new TCPDF('P', 'mm', 'A4'));
$pdf = new TCPDF("L", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->AddPage();



$pdf->SetFont('', '', 9, '', 'false');

$html .= '
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Result</title><link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">';
$html .= '</head>
<body style="font-family:"trebuchet MS",Arial,Helvetica,sans-serif;">
<table width="100%" border="0" >
<tr>
<td width="10%" style="border-right:none; text-align:left">
<img src="images/L_58839.gif" alt="" border="0" style="width: 700%; display:block; ">
</td>
<td align="left" style="border-left:none;" width="80%">
<small style="display:block; color:#000; font-size:10px;">
<b>Sanskar School</b>
</small><br>
<small style="display:block; color:#000; font-size:8px;"> Affiliated to CBSE,Delhi<br>
 Vishwamitra Marg, Defence Colony<br> Sirsi Road, Jaipur(Rajasthan) 302012</small>
<br>
<span style="display:block; color:#000; font-size|:10px;"> Ph. No.:&nbsp;0141 - 2246189,2357844</span><br>


<br>
</td>
</tr>
</table>

<table width="100%" border="1" align="center">
<tr>

 <td align="center" colspan="8" ><b>Defaulter List For Class ';
if ($classname) {


  $html .= $classname;
} else {
}
$html .= '</b></td>
    
    </tr>
   <tr>
 <th align="center" style="width:4%;">&nbsp;S.No</th>
  <th align="left" style="width:15%;">&nbsp;Student Name</th>
   <th align="left">Father Name</th>
        <th align="left" style="width:8%;">&nbsp;Mobile</th>';
foreach ($quaters as $h => $ty) {

  $html .= '<th style="width:6%;">' . $ty . '</th>';
}

$html .= '<th style="width:4%;">Dues</th><th style="width:8%;">Due Fees <br>(Till current date)</th>
    </tr>';

if (isset($results) && !empty($results)) {
  $fees = 0;
  foreach ($results as $service) {

    $html .= '<tr>
                  <td>' . $service['enroll'] . '</td>
                  <td>' . ucwords(strtolower($service['fname'])) . " " . ucwords(strtolower($service['middlename'])) . " " . ucwords(strtolower($service['lname'])) . '</td><td>' . ucwords(strtolower($service['fathername'])) . '</td><td>' . $service['sms_mobile'] . '</td>';



    $studentfees = $this->Comman->finddisountstudent($service['id'], $academicyear);


    $quas = array();


    foreach ($studentfees as $k => $value) {
      $quas[] = unserialize($value['quarter']);
    }


    $quaf = array();

    foreach ($quas as $h => $vale) {

      $quaf = array_merge($quaf, $vale);
    }
    $rt = array();
    foreach ($quaf as $j => $t) {

      $qua[] = $j;
    }

    foreach ($quaters as $h => $ty) {


      if (!empty($quaf)) {
        $html .= '<td>';

        $dff = 0;
        foreach ($quaf as $t => $h) {
          if ($t == $ty) {

            //$html.=$h;
            $html .= '0';

            $dff++;
          } else {
          }
        }
        if ($dff == '0') {




          if ($ty == "Quater1") {

            $ty = "Tution Fee";
          } else if ($ty == "Quater2") {
            $ty = "Tution Fee";
          } else if ($ty == "Quater3") {

            $ty = "Tution Fee";
          } else if ($ty == "Quater4") {

            $ty = "Tution Fee";
          }

          $feeshead = $this->Comman->findfeeheadsid($ty);
          $err = $this->Comman->findfeeheadsamount($service['class_id'], $academicyear, $feeshead['id']);



          if ($ty == "Quater1") {

            $html .= $err[0]['qu1_fees'];
          } else if ($ty == "Quater2") {
            $html .= $err[0]['qu2_fees'];
          } else if ($ty == "Quater3") {

            $html .= $err[0]['qu3_fees'];
          } else if ($ty == "Quater4") {

            $html .= $err[0]['qu4_fees'];
          } else {


            $html .= $err[0]['qu1_fees'];
          }
        }

        $html .= '</td>';
      } else {
        $html .= '<td>';

        if ($ty == "Quater1") {

          $ty = "Tution Fee";
        } else if ($ty == "Quater2") {
          $ty = "Tution Fee";
        } else if ($ty == "Quater3") {

          $ty = "Tution Fee";
        } else if ($ty == "Quater4") {

          $ty = "Tution Fee";
        }

        $feeshead = $this->Comman->findfeeheadsid($ty);
        $err = $this->Comman->findfeeheadsamount($service['class_id'], $academicyear, $feeshead['id']);



        if ($ty == "Quater1") {

          $html .= $err[0]['qu1_fees'];
        } else if ($ty == "Quater2") {
          $html .= $err[0]['qu2_fees'];
        } else if ($ty == "Quater3") {

          $html .= $err[0]['qu3_fees'];
        } else if ($ty == "Quater4") {

          $html .= $err[0]['qu4_fees'];
        } else {


          $html .= $err[0]['qu1_fees'];
        }
        $html .= '</td>';
      }
    }



    $html .= '<td>';
    $findpending = $this->Comman->findpendingssinglefee($service['id']);

    if ($findpending[0]['sum']) {
      $html .= $findpending[0]['sum'];
    } else {
      $html .= "0";
    }

    $html .= '</td><td>';
    $findamountmonth = $this->Comman->findamountmonth($service['class_id'], $academicyear); // pr($findamountmonth); 
    $findamount3month = $this->Comman->findamount3month($service['class_id'], $academicyear);
    $findamount2month = $this->Comman->findamount2month($service['class_id'], $academicyear);
    $findamount1month = $this->Comman->findamount1month($service['class_id'], $academicyear);
    $findsum = $findamountmonth['qu4_fees'] + $findamount3month['qu3_fees'] + $findamount2month['qu2_fees'] + $findamount1month['qu1_fees'];

    $perticularamounts = $this->Comman->findperticularamount($service['id'], $academicyear);
    $paidfeestotal = 0;

    $discount = 0;
    foreach ($perticularamounts as $values) {

      $paidfeestotal += $values['fee'];
    }

    foreach ($perticularamounts as $values) {

      $discount += $values['discount'];
    }

    if ($findsum > $paidfeestotal) {

      $dueamt = $findsum - $paidfeestotal;
      $total_dues_amount += $dueamt;


      if ($discount > 0) {


        $html .= $dueamt = $dueamt - $discount;
      } else {

        $html .= $dueamt;
      }

      $html .= '<strong style="color:red;">*</strong>';
    } else {

      $html .= '0';
    }
    $html .= '</td></tr>';
    $counter++;
  }
} else {
  $html .= '<tr>
		<td>NO Data Available</td>
		</tr>';
}


$html .= '</table>
</body>
</html>';

$pdf->WriteHTML($html, true, false, true, false, '');
ob_end_clean();
echo $pdf->Output('Result');
exit;
?>



?>