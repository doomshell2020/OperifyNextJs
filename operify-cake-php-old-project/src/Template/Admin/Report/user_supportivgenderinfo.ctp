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

</td>';

$class = explode(',', $class_id);

if (!empty($section_id)) {
    $section = explode(',', $section_id);

} else {

    $section = array();

}

if (isset($class) && !empty($class) && !empty($section)) {
    $rrt = array();

    $notinstud = $this->Comman->findgendercountawsdroparray($acedmic);

    foreach ($notinstud as $ffg => $ddd) {
        if ($ddd['s_id'] != '') {
            $rrt[] = $ddd['s_id'];
        }
    }

    foreach ($class as $key => $element) {

        foreach ($section as $keys => $elements) {

            $classmalssssse = $this->Comman->findgendercountawsboth($element, $elements, $acedmic);
            if ($acedmic != $acedmicyear && $next_academic_year != $acedmic) {
                $classmalssssse += $this->Comman->findgendercountawsdrop("Male", $element, $elements, $acedmic);
                $classmalssssse += $this->Comman->findgendercountawshistory("Male", $element, $elements, $acedmic, $rrt);
            }

            if ($classmalssssse != '0') {

                $classtotalh = 0;
                // $classmaleh=$this->Comman->findgendercountaws("Male",$element,$elements,$acedmic);

                $classmaleh = $this->Comman->findgendercountaws("Male", $element, $elements, $acedmic);
                if ($acedmic != $acedmicyear && $next_academic_year != $acedmic) {
                    $classmaleh += $this->Comman->findgendercountawsdrop("Male", $element, $elements, $acedmic);
                    $classmaleh += $this->Comman->findgendercountawshistory("Male", $element, $elements, $acedmic, $rrt);
                }

                $classfemaleh = $this->Comman->findgendercountaws("Female", $element, $elements, $acedmic);
                if ($acedmic != $acedmicyear && $next_academic_year != $acedmic) {
                    $classfemaleh += $this->Comman->findgendercountawsdrop("Female", $element, $elements, $acedmic);
                    $classfemaleh += $this->Comman->findgendercountawshistory("Female", $element, $elements, $acedmic, $rrt);
                }

                $classmaletotalh += $classmaleh;

                $classfemaletotalh += $classfemaleh;

            }}}

    $totalh = $classmaletotalh + $classfemaletotalh;
    $html .= '<td style="text-align:right;" width="50%" align="right"><b>Total:</b>&nbsp;' . $totalh . '&nbsp;&nbsp;
<b>Total Male:</b>&nbsp;' . $classmaletotalh . '&nbsp;&nbsp;
<b>Total Female:</b>&nbsp;' . $classfemaletotalh . '
</td>';

} else if (isset($class) && !empty($class)) {
    $rrt = array();

    $notinstud = $this->Comman->findgendercountawsdroparray($acedmic);

    foreach ($notinstud as $ffg => $ddd) {
        if ($ddd['s_id'] != '') {
            $rrt[] = $ddd['s_id'];
        }
    }

    foreach ($class as $key => $element) {

        $classmalssssseh = $this->Comman->findgendercountwsawsbothh($element, $acedmic);
        if ($acedmic != $acedmicyear && $next_academic_year != $acedmic) {
            $classmalssssseh += $this->Comman->findgendercountwsawsdrop("Male", $element, $acedmic);
            $classmalssssseh += $this->Comman->findgendercountwsawsdhistory("Male", $element, $acedmic, $rrt);

        }

        if ($classmalssssseh != '0') {

            $classtotalh = 0;

            $classmaleh = $this->Comman->findgendercountwsaws("Male", $element, $acedmic);
            if ($acedmic != $acedmicyear && $next_academic_year != $acedmic) {
                $classmaleh += $this->Comman->findgendercountwsawsdrop("Male", $element, $acedmic);
                $classmaleh += $this->Comman->findgendercountwsawsdhistory("Male", $element, $acedmic, $rrt);
            }

            $classfemaleh = $this->Comman->findgendercountwsaws("Female", $element, $acedmic);
            if ($acedmic != $acedmicyear && $next_academic_year != $acedmic) {
                $classfemaleh += $this->Comman->findgendercountwsawsdrop("Female", $element, $acedmic);
                $classfemaleh += $this->Comman->findgendercountwsawsdhistory("Female", $element, $acedmic, $rrt);
            }

            $classmaletotalh += $classmaleh;

            $classfemaletotalh += $classfemaleh;

        }}

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
</table><table width="100%" border="0" align="center">
<tr>
 <td align="center" colspan="8" ><b style="font-size:13px;">GENDER REPORT SESSION ' . $acedmic . ' ALL AS ON ';

$dateto2 = date('d-m-Y');
$html .= $dateto2;

$html .= '</b></td>

    </tr>


    </table>
    <table width="100%" border="1" align="center">
<tr>

    <th  style="width:30%" ><b>&nbsp;Class Name</b></th>';

if ($gender == 'Both') {
    $html .= '<th style="width:30%"><b>Male</b></th>
    <th style="width:30%"><b>Female</b></th>';
} else if ($gender == 'Male') {
    $html .= '<th style="width:30%"><b>Male</b></th>';
} else if ($gender == 'Female') {
    $html .= '<th style="width:30%"><b>Female</b></th>';
}
$html .= ' <th style="width:10%"><b>Total</b></th> </tr>';

$class = explode(',', $class_id);

if (!empty($section_id)) {
    $section = explode(',', $section_id);

} else {
    $section = array();

}
$counter = 1;
if (isset($class) && !empty($class) && !empty($section)) {

    $rrt = array();

    $notinstud = $this->Comman->findgendercountawsdroparray($acedmic);

    foreach ($notinstud as $ffg => $ddd) {
        if ($ddd['s_id'] != '') {
            $rrt[] = $ddd['s_id'];
        }
    }
    foreach ($class as $key => $element) {

        foreach ($section as $keys => $elements) {

            $classssr = $this->Comman->findclass($element);
            $sectionssss = $this->Comman->findsectionsss($elements);
            $classmalssssse = $this->Comman->findgendercountawsboth($element, $elements, $acedmic);
            if ($acedmic != $acedmicyear && $next_academic_year != $acedmic) {
                $classmalssssse += $this->Comman->findgendercountawsdrop("Male", $element, $elements, $acedmic);
                $classmalssssse += $this->Comman->findgendercountawshistory("Male", $element, $elements, $acedmic, $rrt);
            }

            if ($classmalssssse != '0') {

                $html .= '<tr>
			     <td align="center"  >&nbsp;';

                $html .= $classssr['title'] . "-" . $sectionssss['title'];
                $html .= '</td>';

                $classtotal = 0;if ($gender == 'Both') {
                    $html .= '<td>';

                    $classmale = $this->Comman->findgendercountaws("Male", $element, $elements, $acedmic);
                    if ($acedmic != $acedmicyear && $next_academic_year != $acedmic) {
                        $classmale += $this->Comman->findgendercountawsdrop("Male", $element, $elements, $acedmic);
                        $classmale += $this->Comman->findgendercountawshistory("Male", $element, $elements, $acedmic, $rrt);
                    }

                    $html .= $classmale;

                    $classtotal += $classmale;

                    $classmaletotal += $classmale;

                    $html .= '</td>';
                    $html .= '<td>';

                    $classfemale = $this->Comman->findgendercountaws("Female", $element, $elements, $acedmic);
                    if ($acedmic != $acedmicyear && $next_academic_year != $acedmic) {
                        $classfemale += $this->Comman->findgendercountawsdrop("Female", $element, $elements, $acedmic);
                        $classfemale += $this->Comman->findgendercountawshistory("Female", $element, $elements, $acedmic, $rrt);
                    }

                    $html .= $classfemale;
                    $classtotal += $classfemale;
                    $classfemaletotal += $classfemale;

                    $html .= '</td><td>';

                    $html .= $classtotal;

                    $html .= '</td>';

                } else if ($gender == 'Male') {
                    $html .= '<td>';

                    $classmale = $this->Comman->findgendercountaws("Male", $element, $elements, $acedmic);
                    if ($acedmic != $acedmicyear && $next_academic_year != $acedmic) {
                        $classmale += $this->Comman->findgendercountawsdrop("Male", $element, $elements, $acedmic);
                        $classmale += $this->Comman->findgendercountawshistory("Male", $element, $elements, $acedmic, $rrt);
                    }

                    $html .= $classmale;
                    $classtotal += $classmale;
                    $classmaletotal += $classmale;

                    $html .= '</td><td>';
                    $html .= $classtotal;

                    $html .= '</td>';

                } else if ($gender == 'Female') {
                    $html .= '<td>';
                    $classfemale = $this->Comman->findgendercountaws("Female", $element, $elements, $acedmic);
                    if ($acedmic != $acedmicyear && $next_academic_year != $acedmic) {
                        $classfemale += $this->Comman->findgendercountawsdrop("Female", $element, $elements, $acedmic);
                        $classfemale += $this->Comman->findgendercountawshistory("Female", $element, $elements, $acedmic, $rrt);
                    }
                    $html .= $classfemale;
                    $classtotal += $classfemale;
                    $classfemaletotal += $classfemale;

                    $html .= '</td><td>';

                    $html .= $classtotal;
                    $html .= '</td>';

                }

                $html .= '</tr>';
                $counter++;
            }}}

    $html .= '<tr><td  style="width:30%"  style="text-align:center;"><b>Total</b></td>';

    if ($gender == 'Both') {
        $html .= '<td style="width:30%"><b>';
        $html .= $classmaletotal;
        $html .= '</b></td><td style="width:30%" ><b>';
        $html .= $classfemaletotal;

        $html .= '</b></td>';

        $html .= '<td  style="width:10%"><b>';
        $html .= $classmaletotal + $classfemaletotal;
        $html .= '</b></td></tr>';

    } else if ($gender == 'Male') {
        $html .= '<td style="width:30%" ><b>';
        $html .= $classmaletotal;
        $html .= '</b></td><td  style="width:10%"><b>';

        $html .= $classmaletotal;

        $html .= '</b></td></tr>';
    } else if ($gender == 'Female') {
        $html .= '<td style="width:30%"><b>';
        $html .= $classfemaletotal;

        $html .= '</b></td><td style="width:10%"><b>';
        $html .= $classfemaletotal;
        $html .= '</b></td></tr>';
    }
} else if (isset($class) && !empty($class)) {

    $rrt = array();

    $notinstud = $this->Comman->findgendercountawsdroparray($acedmic);

    foreach ($notinstud as $ffg => $ddd) {
        if ($ddd['s_id'] != '') {
            $rrt[] = $ddd['s_id'];
        }
    }

    foreach ($class as $key => $element) {

        $classssr = $this->Comman->findclass($element);

        $classmalssssse = $this->Comman->findgendercountwsawsbothh($element, $acedmic);
        if ($acedmic != $acedmicyear && $next_academic_year != $acedmic) {
            $classmalssssse += $this->Comman->findgendercountwsawsdrop("Male", $element, $acedmic);
            $classmalssssse += $this->Comman->findgendercountwsawsdhistory("Male", $element, $acedmic, $rrt);
        }

        if ($classmalssssse != '0') {

            $html .= '<tr>
			     <td align="center"  >&nbsp;';

            $html .= $classssr['title'];
            $html .= '</td>';

            $classtotal = 0;if ($gender == 'Both') {

                $html .= '<td>';

                $classmale = $this->Comman->findgendercountwsaws("Male", $element, $acedmic);
                if ($acedmic != $acedmicyear && $next_academic_year != $acedmic) {
                    $classmale += $this->Comman->findgendercountwsawsdrop("Male", $element, $acedmic);
                    $classmale += $this->Comman->findgendercountwsawsdhistory("Male", $element, $acedmic, $rrt);
                }

                $html .= $classmale;

                $classtotal += $classmale;

                $classmaletotal += $classmale;

                $html .= '</td>';
                $html .= '<td>';
                $classfemale = $this->Comman->findgendercountwsaws("Female", $element, $acedmic);
                if ($acedmic != $acedmicyear && $next_academic_year != $acedmic) {
                    $classfemale += $this->Comman->findgendercountwsawsdrop("Female", $element, $acedmic);
                    $classfemale += $this->Comman->findgendercountwsawsdhistory("Female", $element, $acedmic, $rrt);
                }

                $html .= $classfemale;
                $classtotal += $classfemale;
                $classfemaletotal += $classfemale;

                $html .= '</td><td>';

                $html .= $classtotal;

                $html .= '</td>';

            } else if ($gender == 'Male') {
                $html .= '<td>';

                $classmale = $this->Comman->findgendercountwsaws("Male", $element, $acedmic);
                if ($acedmic != $acedmicyear && $next_academic_year != $acedmic) {
                    $classmale += $this->Comman->findgendercountwsawsdrop("Male", $element, $acedmic);
                    $classmale += $this->Comman->findgendercountwsawsdhistory("Male", $element, $acedmic, $rrt);

                }

                $html .= $classmale;
                $classtotal += $classmale;
                $classmaletotal += $classmale;

                $html .= '</td><td>';
                $html .= $classtotal;

                $html .= '</td>';

            } else if ($gender == 'Female') {
                $html .= '<td>';
                $classfemale = $this->Comman->findgendercountwsaws("Female", $element, $acedmic);
                if ($acedmic != $acedmicyear && $next_academic_year != $acedmic) {
                    $classfemale += $this->Comman->findgendercountwsawsdrop("Female", $element, $acedmic);
                    $classfemale += $this->Comman->findgendercountwsawsdhistory("Female", $element, $acedmic, $rrt);

                }
                $html .= $classfemale;
                $classtotal += $classfemale;
                $classfemaletotal += $classfemale;

                $html .= '</td><td>';

                $html .= $classtotal;
                $html .= '</td>';

            }

            $html .= '</tr>';
            $counter++;
        }}

    $html .= '<tr><td  style="width:30%"  style="text-align:center;"><b>Total</b></td>';

    if ($gender == 'Both') {
        $html .= '<td style="width:30%"><b>';
        $html .= $classmaletotal;
        $html .= '</b></td><td style="width:30%" ><b>';
        $html .= $classfemaletotal;

        $html .= '</b></td>';

        $html .= '<td  style="width:10%"><b>';
        $html .= $classmaletotal + $classfemaletotal;
        $html .= '</b></td></tr>';

    } else if ($gender == 'Male') {
        $html .= '<td style="width:30%" ><b>';
        $html .= $classmaletotal;
        $html .= '</b></td><td  style="width:10%"><b>';

        $html .= $classmaletotal;

        $html .= '</b></td></tr>';
    } else if ($gender == 'Female') {
        $html .= '<td style="width:30%"><b>';
        $html .= $classfemaletotal;

        $html .= '</b></td><td style="width:10%"><b>';
        $html .= $classfemaletotal;
        $html .= '</b></td></tr>';
    }
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
