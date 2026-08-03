<?php
class xtcpdf extends TCPDF
{

}

//$subject=$this->Comman->findexamsubjectsresult($students['id'],$students['section']['id'],$students['acedmicyear']);

$this->set('pdf', new TCPDF('L', 'mm', 'A4'));
$pdf = new TCPDF("L", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->AddPage();

$pdf->SetFont('', '', 10, '', 'false');

$rolepresents = $this->request->session()->read('Auth.User.role_id');

if ($rolepresents == '5') {
    $bordd = "Cbse Fee";

} else if ($rolepresents == '8') {
    $bordd = "International Fee";
} else {

    $bordd = "CBSE-INTERNATIONAL";
}

$temp = str_replace(array('{logo}', '{sitetitle}', '{subtitle1}', '{subtitle2}', '{address1}', '{address2}', '{phone}', '{fax}', '{email}', '{website}'), array($sitesetting['logo'], $sitesetting['sitetitle'], $sitesetting['subtitle1'], $sitesetting['subtitle2'], $sitesetting['address1'], $sitesetting['address2'], $sitesetting['phone'], $sitesetting['fax'], $sitesetting['email'], $sitesetting['website']), $det['template']);
$html .= '
 <!DOCTYPE HTML>
 <html>
 <head>
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
 <title>Result</title><link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">';
$html .= '</head>
 <body>';
$html .= $temp;

$html .= '<hr><h2> Today '.date('d-m-Y').' Office Summary Report </h2></br><table width="100%" border="1" align="center">
    <thead>



                <tr style="font-weight: bold;background-color: #ccc;">

                  <td style="width:3%">#</td>
                   <td style="width:18%">Area</td>
              <td colspan="3">Today</td>';

foreach ($academic as $hh => $rg) {

    $html .= '<td colspan="3">' . $rg . '</td>';
}

$html .= '</tr>
                </thead>
                <tbody id="example22">

            <tr style="font-weight: bold;font-size: 9px;">


                            <td  style="width:3%"></td>
                            <td  style="width:18%"></td>
                  <td>CBSE</td>
                     <td>INTER.</td>
                        <td>TOTAL</td>';
foreach ($academic as $hh => $rg) {
    $html .= '<td>CBSE</td>
                     <td>INTER.</td>
                        <td>TOTAL</td>';
}
$html .= '</tr>

                           <tr>
                        <td style="width:3%">1</td>
                  <td style="width:18%">Prospectus Sale</td><td>';
$servicesdst = $this->Comman->findprospectusstudents2stoday('1');

if (isset($servicesdst)) {$html .= $servicesdst;} else { $html .= 'N/A';}
$html .= '</td>



              <td>&nbsp;';
$servicesdst2 = $this->Comman->findprospectusstudents2stodayint('1');

if (isset($servicesdst2)) {$html .= $servicesdst2;} else { $html .= 'N/A';}
$html .= '</td>


                    <td><b>';
$servicessdst = $this->Comman->findprospectusstudents2stodaytotal();

if (isset($servicessdst)) {$html .= $servicessdst;} else { $html .= 'N/A';}
$html .= '</b>
              </td>';
foreach ($academic as $hh => $rg) {$tostd = '0';

    $html .= '<td>';

    $servicess = $this->Comman->findprospectusstudents2s($rg);
    $servicess = $servicess;
    if (isset($servicess)) {$tostd += $servicess;
        $html .= $servicess;} else { $html .= 'N/A';}
    $html .= '</td>

                      <td>';
    $servicessout = $this->Comman->findprospectusstudents2sout($rg);
    $servicessout = $servicessout;
    if (isset($servicessout)) {$tostd += $servicessout;
        $html .= $servicessout;} else { $html .= 'N/A';}
    $html .= '</td>

                       <td>';
    if (isset($tostd)) {$html .= $tostd;} else { $html .= 'N/A';}
    $html .= '</td>';
}

$html .= '</tr>

				<tr>

                  <td style="width:3%">2</td>
                  <td style="width:18%">Registration Sale</td>


            <td>';
$servicesdst3 = $this->Comman->findregistrationtudents2stoday('1');

if (isset($servicesdst3)) {$html .= $servicesdst3;} else { $html .= 'N/A';}
$html .= '</td>

              <td>&nbsp;';
$servicesdst23 = $this->Comman->findregistrationtudents2stodayint('1');

if (isset($servicesdst23)) {$html .= $servicesdst23;} else { $html .= 'N/A';}
$html .= '</td>

                    <td><b>';
$servicessdst4 = $this->Comman->findregistrationtudents2stodaytotal();

if (isset($servicessdst4)) {$html .= $servicessdst4;} else { $html .= 'N/A';}
$html .= '</b>
              </td>';

foreach ($academic as $hh => $rg) {
    $tostd24 = '0';
    $html .= '<td>';
    $servsicsess = $this->Comman->findregistrationstudents2s($rg);
    $servicsesssj = $servsicsess;
    if (isset($servicsesssj)) {$tostd24 += $servicsesssj;
        $html .= $servicsesssj;} else { $html .= 'N/A';}
    $html .= '</td>
                  <td>';
    $servicessout = $this->Comman->findregistrationstudents2sout($rg);
    $servicsess = $servicessout;
    if (isset($servicsess)) {$tostd24 += $servicsess;
        $html .= $servicsess;} else { $html .= 'N/A';}
    $html .= '</td><td>';

    if (isset($tostd24)) {$html .= $tostd24;} else { $html .= 'N/A';}

    $html .= '</td>';

}

$html .= '</tr><tr><td style="width:3%">3</td>
                  <td style="width:18%">Admission Total</td><td>';
$servicesdst34 = $this->Comman->findacedemicstudents2stoday('1');

if (isset($servicesdst34)) {$html .= $servicesdst34;} else { $html .= 'N/A';}
$html .= '</td>

              <td>&nbsp;';
$servicesdst234 = $this->Comman->findacedemicstudents2stodayint('1');

if (isset($servicesdst234)) {$html .= $servicesdst234;} else { $html .= 'N/A';}
$html .= '</td>

                    <td><b>';
$servicessdst44 = $this->Comman->findacedemicstudents2stodaytotal();

if (isset($servicessdst44)) {$html .= $servicessdst44;} else { $html .= 'N/A';}
$html .= '</b>
              </td>';

foreach ($academic as $hh => $rg) {

    $tostd244 = '0';

    $html .= '<td>';
    $servicess1 = $this->Comman->findacedemicstudents2srtr($rg, 1);
    $servicess21 = $this->Comman->findacedemicstudents21srtr($rg, 1);
    $servicess1 = $servicess1 + $servicess21;
    if (isset($servicess1)) {$tostd244 += $servicess1;
        $html .= $servicess1;
    } else { $html .= 'N/A';}
    $html .= '</td><td>';
    $servicess2 = $this->Comman->findacedemicstudents2srtrout($rg, 1);
    $servicess22 = $this->Comman->findacedemicstudents21srtrout($rg, 1);
    $servicess2 = $servicess2 + $servicess22;
    if (isset($servicess2)) {$tostd244 += $servicess2;
        $html .= $servicess2;} else { $html .= 'N/A';}
    $html .= '</td><td>';
    if (isset($tostd244)) {$html .= $tostd244;} else { $html .= 'N/A';}
    $html .= '</td>';

}

$html .= '</tr><tr><td style="width:3%">4</td>
                  <td style="width:18%">Fee Collection(Cash)</td>';

$totd = '0';
$totd2333 = '0';
$totd23331 = '0';
$totd2333123 = '0';

$html .= '<td>&nbsp;';
$servicesssts = $this->Comman->findcollectiontudents2stoday('CASH');
$servicesssts2 = $this->Comman->findcollectiontudents2stodaydropp('CASH');

if ($servicesssts[0]['sum'] || $servicesssts2[0]['sum']) {$totd += $servicesssts[0]['sum'] + $servicesssts2[0]['sum'];

    $totd2333 += $servicesssts[0]['sum'] + $servicesssts2[0]['sum'];
    $totss = $servicesssts[0]['sum'] + $servicesssts2[0]['sum'];
    $html .= number_format($totss, 2);} else { $html .= 'N/A';}

$html .= '</td>';

$html .= '<td>&nbsp;';
$servicessstsd = $this->Comman->findcollectiontudents2stodayout('CASH');
$servicessstsdtr3 = $this->Comman->findcollectiontudents2stodayoutdroppp('CASH');

if ($servicessstsd[0]['sum'] || $servicessstsdtr3[0]['sum']) {

    $totd += $servicessstsd[0]['sum'] + $servicessstsdtr3[0]['sum'];
    $totd23331 += $servicessstsd[0]['sum'] + $servicessstsdtr3[0]['sum'];
    $tsss = $servicessstsd[0]['sum'] + $servicessstsdtr3[0]['sum'];
    $html .= number_format($tsss, 2);} else { $html .= 'N/A';}

$html .= '</td>

                    <td><b>';
$html .= number_format($totd, 2);
$html .= '</b>
              </td>
               <td></td>
                     <td></td>
                      <td></td>


				</tr>
				 <tr>

                  <td style="width:3%">5</td>
                  <td style="width:18%">Fee Collection(Other Mode)</td>';

$totds = '0';
$html .= '<td>&nbsp;';
$servicessstsds = $this->Comman->findcollectiontudents2stoday2next('CASH');
$servicessstsds23s = $this->Comman->findcollectiontudents2stoday2droppenext('CASH');

if ($servicessstsds[0]['sum'] || $servicessstsds23s[0]['sum']) {

    $totds += $servicessstsds[0]['sum'] + $servicessstsds23s[0]['sum'];
    $totd2333 += $servicessstsds[0]['sum'] + $servicessstsds23s[0]['sum'];
    $totsss = $servicessstsds[0]['sum'] + $servicessstsds23s[0]['sum'];

    $html .= number_format($totsss, 2);} else { $html .= 'N/A';}
$html .= '</td><td>&nbsp;';
$servicessstsdt = $this->Comman->findcollectiontudents2stoday2outnext('CASH');
$servicessstsdt43d = $this->Comman->findcollectiontudents2stoday2outdroppenext('CASH');

if ($servicessstsdt[0]['sum'] || $servicessstsdt43d[0]['sum']) {
    $totds += $servicessstsdt[0]['sum'] + $servicessstsdt43d[0]['sum'];

    $totd23331 += $servicessstsdt[0]['sum'] + $servicessstsdt43d[0]['sum'];
    $totd2 = $servicessstsdt[0]['sum'] + $servicessstsdt43d[0]['sum'];

    $html .= number_format($totd2, 2);} else { $html .= 'N/A';}
$ofcash = $this->comman->findofcash('CASH');
$ofnotcash = $this->comman->findofnotcash('CASH');

$html .= '</td>

                    <td><b>';
$html .= number_format($totds, 2);
$html .= '</b>
              </td>
               <td></td>
                     <td></td>
                      <td></td>



                </tr>


                <tr>

                <td style="width:3%">6</td>
                <td style="width:18%">Fee Collection<strong style="color:green;">(SMART HUB)</strong></td>';

$totds2 = '0';
$html .= '<td>&nbsp;';
$servicessstsds2 = $this->Comman->findcollectiontudents2stoday2next2('CASH');
$servicessstsds23s2 = $this->Comman->findcollectiontudents2stoday2droppenext2('CASH');

if ($servicessstsds2[0]['sum'] || $servicessstsds23s2[0]['sum']) {

  $totds2 += $servicessstsds2[0]['sum'] + $servicessstsds23s2[0]['sum'];
  $totd2333 += $servicessstsds2[0]['sum'] + $servicessstsds23s2[0]['sum'];
  $totsss2 = $servicessstsds2[0]['sum'] + $servicessstsds23s2[0]['sum'];

  $html .= number_format($totsss2, 2);} else { $html .= 'N/A';}
$html .= '</td><td>&nbsp;';
$servicessstsdt2 = $this->Comman->findcollectiontudents2stoday2outnext2('CASH');
$servicessstsdt43d2 = $this->Comman->findcollectiontudents2stoday2outdroppenext2('CASH');

if ($servicessstsdt2[0]['sum'] || $servicessstsdt43d2[0]['sum']) {
  $totds2 += $servicessstsdt2[0]['sum'] + $servicessstsdt43d2[0]['sum'];

  $totd23331 += $servicessstsdt2[0]['sum'] + $servicessstsdt43d2[0]['sum'];
  $totd2 = $servicessstsdt2[0]['sum'] + $servicessstsdt43d2[0]['sum'];

  $html .= number_format($totd2, 2);} else { $html .= 'N/A';}
$ofcash = $this->comman->findofcash('CASH');
$ofnotcash = $this->comman->findofnotcash('CASH');

$html .= '</td>

                  <td><b>';
$html .= number_format($totds2, 2);
$html .= '</b>
            </td>
             <td></td>
                   <td></td>
                    <td></td>



              </tr>
				 <tr>
        <td style="width:3%">7</td>
                  <td style="width:18%">OtherFee Collection(cash)</td>
                  <td>N/A</td>
                  <td>N/A</td>
                  <td><b>';
$html .= number_format($ofcash[0]['sum'], 2);
$html .= '</b></td>
<td></td>
<td></td>
<td></td>
        </tr>
        <tr>
        <td style="width:3%">8</td>
                  <td style="width:18%">OtherFee Collection(Other Mode)</td>
                  <td>N/A</td>
                  <td>N/A</td>
                  <td><b>';
$html .= number_format($ofnotcash[0]['sum'], 2);
$html .= '</b></td>
<td></td>
<td></td>
<td></td>
        </tr>

				 <tr style="font-weight: bold;
    background-color: #ccc;">


                  <td colspan="2"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total :</td>';

$totd2333123 = '0';

$html .= '<td>&nbsp;
                <b>';
$totd2333123 += $totd2333;
$html .= number_format($totd2333, 2);
$html .= '</b>
              </td>

              <td>&nbsp;
                <b>';
$totd2333123 += $totd23331;
$html .= number_format($totd23331, 2);
$html .= '</b>
              </td>



                    <td><b>';
$totd2333123 += $ofcash[0]['sum'] + $ofnotcash[0]['sum'];
$html .= number_format($totd2333123, 2);
$html .= '</b>
              </td>
               <td></td>
                     <td></td>
                      <td></td>



				</tr>
                </tbody></table>
</body>
</html>';

$pdf->WriteHTML($html, true, false, true, false, '');
ob_end_clean();
$date = date('d-m-Y');
echo $pdf->Output('Daily-Summary-' . $bordd . '-' . $date . '.pdf');
exit;
