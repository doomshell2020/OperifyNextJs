<?php
class xtcpdf extends TCPDF
{

}

//$subject=$this->Comman->findexamsubjectsresult($students['id'],$students['section']['id'],$students['acedmicyear']);

$this->set('pdf', new TCPDF('L', 'mm', 'A4'));
$pdf = new TCPDF("P", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->AddPage();

$pdf->SetFont('', '', 9, '', 'false');

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
$html .= '<table  border="0"  align="center">
<hr>
<tr style="line-height:60px;">

<td style="text-align:left" width="50%" >

</td>
';

if (isset($class_id2) && !empty($class_id2)) {

    foreach ($class_id2 as $key => $element) {

        foreach ($section_id2 as $keys => $elements) {

            $classmalssssseh = $this->Comman->findgendercountaws("Male", $key, $keys, $acedmic);

            if ($classmalssssseh != '0') {

                $classtotalh = 0;
                $classmaleh = $this->Comman->findgendercountaws("Male", $key, $keys, $acedmic);
                $classfemaleh = $this->Comman->findgendercountaws("Female", $key, $keys, $acedmic);

                $classmaletotalh += $classmaleh;

                $classfemaletotalh += $classfemaleh;

            }}}

    $totalh = $classmaletotalh + $classfemaletotalh;
    $html .= '<td style="text-align:right;" width="50%" align="right"><b>Total:</b>&nbsp;' . $totalh . '&nbsp;&nbsp;
<b>Total Male:</b>&nbsp;' . $classmaletotalh . '&nbsp;&nbsp;
<b>Total Female:</b>&nbsp;' . $classfemaletotalh . '
</td>';

} else {

    $html .= '<td style="text-align:right;" width="50%" align="right"><b>Total:</b>&nbsp; 0&nbsp;&nbsp;
<b>Total Male:</b> &nbsp;0&nbsp;&nbsp;
<b>Total Female:</b>&nbsp; 0
</td>';

}

$html .= '</tr>
</table>
<table width="100%"  align="center">
<tr>
 <td align="center" colspan="8" ><b style="font-size:13px;">GENDER HOUSE REPORT SESSION ' . $acedmic . ' ALL AS ON ';

$dateto2 = date('d-m-Y');
$html .= $dateto2;

$html .= '</b></td>

    </tr>


    </table>

    <table width="100%" border="1"  class="productsTable"  align="center">

 <tr>
    <th width="5%">S.No</th>
    <th width="30%">Class Name</th>


    <th>Male</th>
    <th>Female</th>

    <th>Total</th>

    </tr>';

$counter = 1;
if (isset($class_id2) && !empty($class_id2)) {

    foreach ($class_id2 as $key => $element) {

        foreach ($section_id2 as $keys => $elements) {
            $tyu = 0;
            $tyu2 = 0;
            $tyu3 = 0;
            $classmalssssse = $this->Comman->findgendercountaws("Male", $key, $keys, $acedmic);

            if ($classmalssssse != '0') {

                $html .= '<tr>
			     <td width="5%">' . $counter . '<br><table><tr>
						 <td>&nbsp;</td>
					 <td>&nbsp;</td>
					 <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td></td>
					<td></td>
					 </tr></table></td>
			   <td width="30%" ><b>' . $element . "-" . $elements . '</b><br>';

                foreach ($houselist as $hkey => $helement) {
                    $html .= '<table ><tr>
						 <td>&nbsp;</td>
					 <td>&nbsp;</td>

					<td>' . $helement . '</td>
					 </tr></table>';
                }
                $html .= '</td>';

                $classtotal = 0;if ($gender == 'Both') {
                    $html .= '<td> <br>';
                    foreach ($houselist as $hkey => $helement) {
                        $html .= '<br><table><tr>
						 <td>&nbsp;</td>
					 <td>';

                        $classmale = $this->Comman->findgendercounthouseaws("Male", $key, $keys, $hkey, $acedmic);
                        $html .= $classmale;
                        $classtotal += $classmale;

                        $tyu += $classmale;
                        $classmaletotal += $classmale;

                        $html .= '</td></tr></table>';

                    }
                    $html .= '<table><tr><td><b>Total :-';
                    $html .= $tyu;
                    $html .= '</b></td></tr></table>
					  </td>

					<td> <br>';

                    foreach ($houselist as $hkey => $helement) {
                        $html .= '<br><table><tr>
						 <td>&nbsp;</td>
					 <td>&nbsp;</td>
					 <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td></td><td>';

                        $classfemale = $this->Comman->findgendercounthouseaws("Female", $key, $keys, $hkey, $acedmic);
                        $html .= $classfemale;
                        $classtotal += $classfemale;
                        $tyu2 += $classfemale;

                        $classfemaletotal += $classfemale;

                        $html .= '</td></tr></table>';

                    }
                    $html .= '<table><tr><td><b>Total :-';
                    $html .= $tyu2;
                    $html .= '</b></td></tr></table>
				  </td>';

                } else if ($gender == 'Male') {
                    $html .= '<td> <br>';
                    foreach ($houselist as $hkey => $helement) {
                        $html .= '<br><table><tr>
						 <td>&nbsp;</td>
					 <td>&nbsp;</td>
					 <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td></td><td>';
                        $classmale = $this->Comman->findgendercounthouseaws("Male", $key, $keys, $hkey, $acedmic);
                        $html .= $classmale;
                        $classtotal += $classmale;

                        $classmaletotal += $classmale;

                        $html .= '</td></tr></table>';

                    }
                    $html . '</td>';

                } else if ($gender == 'Female') {

                    $html .= '<td> <br>';
                    foreach ($houselist as $hkey => $helement) {
                        $html .= '<br><table><tr>
						 <td>&nbsp;</td>
					 <td>&nbsp;</td>
					 <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td></td><td>';

                        $classfemale = $this->Comman->findgendercounthouseaws("Female", $key, $keys, $hkey, $acedmic);
                        $html .= $classfemale;
                        $classtotal += $classfemale;

                        $classfemaletotal += $classfemale;

                        $html .= '</td></tr></table>';

                    }
                    $html .= '</td>';

                }

                $html .= '<td><br>';

                if ($gender == 'Both') {foreach ($houselist as $hkey => $helement) {

                    $html .= '<br><table><tr>
						 <td>&nbsp;</td>
					 <td>&nbsp;</td>
					 <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td></td>
					<td>';
                    $cla = $this->Comman->findgendercounthouse2aws($key, $keys, $hkey, $acedmic);

                    $html .= $cla;

                    $tyu3 += $cla;
                    $html .= '</td>
					 </tr></table>';
                }} else if ($gender == 'Male') {foreach ($houselist as $hkey => $helement) {

                    $html .= '<br><table><tr>
						 <td>&nbsp;</td>
					 <td>&nbsp;</td>
					 <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td></td>
					<td>';
                    $cla2 = $this->Comman->findgendercounthouseaws("Male", $key, $keys, $hkey, $acedmic);
                    $html .= $cla2;
                    $tyu3 += $cla2;
                    $html .= '</td>
					 </tr></table>';
                }} else if ($gender == 'Female') {foreach ($houselist as $hkey => $helement) {

                    $html .= '<br><table><tr>
						 <td>&nbsp;</td>
					 <td>&nbsp;</td>
					 <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td></td>
					<td>';
                    $cla3 = $this->Comman->findgendercounthouseaws("Female", $key, $keys, $hkey, $acedmic);
                    $html .= $cla3;
                    $tyu3 += $cla3;
                    $html .= '</td>
					 </tr></table>';
                }}
                $html .= '<table><tr><td><b>Total :-';
                $html .= $tyu3;
                $html .= '</b></td></tr></table>   </td>


                          </tr>';
                $counter++;}}}

} else {
    $html .= '<tr>';
    $html .= '<td colspan="8" style="text-align:center;">No Data Available</td>';
    $html .= '</tr>';
}

$html .= '</table>
</body>
</html>';

$pdf->WriteHTML($html, true, false, true, false, '');
ob_end_clean();
echo $pdf->Output('GenderReport.pdf');
exit;
?>



?>
