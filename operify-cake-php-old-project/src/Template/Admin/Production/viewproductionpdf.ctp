<?php 
class xtcpdf extends TCPDF {
}

   $this->set('pdf', new TCPDF('P','mm','A4'));
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);

// set document information

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->AddPage();
//$pdf->setHeaderMargin(0);

// set margins
//$//pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
 $pdf->SetAutoPageBreak(TRUE, 20);

$pdf->SetFont('', '', 8, '', 'true');
TCPDF_FONTS::addTTFfont('../Devanagari/Devanagari.ttf', 'TrueTypeUnicode', "", 32);

$logo = WWW_ROOT . "/images/" . $site_details['small_logo'];
$address  =  $site_details['address1'];
$email  =  $site_details['email'];
$mobile  =  $site_details['phone'];
$website  =  $site_details['website'];
$gst_no  =  $site_details['gst_no'];
$pan_no  =  $site_details['pan_number'];
$school_name  =  $sitesetting['first_name'];

$contractname = $this->comman->findcontractname($productionorder['contract_id']);
$finishedproduct = $this->comman->getitemname($productionorder['item_id']);
$prepareddailyqty = $this->comman->checkdailysheet($productionorder['po_id'], 8);

$totalorderqty = $this->Comman->getdesignsheetno($productionorder['contract_id'], $productionorder['item_id']);
$preparedqty = '';
foreach($prepareddailyqty as $outhersheathing){
    $preparedqty += $outhersheathing['production_shift_a'] + $outhersheathing['production_shift_b'];
  }
$i=1;
$j=1;



$html .='


<table border="1px">
  <tr>
    <td>

    <table width="100%" style="padding: 1px 1px 0px 0px;" align="left">
         <tbody>
            <tr>
               <td style="text-align:left" width="50%">
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src=src="'.$logo.'" alt="" border="0" style="display:block;" height="62px;"><br>
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
         </tbody>
      </table>
      <h3 style="text-align:center;font-size:10px; border-top:1px solid #000; border-bottom:1px solid #000;">Production Order Details</h3>
      <table cellspacing="0" cellpadding="3" border="0px" style="font-size:8px;">
        <thead>
          <tr>
          <td><b>Productuion Order No.:-</b>
           '. $productionorder['po_id'] .'
          </td>
          <td><b>Issue Date:-</b>
           '. date('d-M-Y', strtotime($productionorder['issuedate'])).'
          </td>
        </tr>
        <tr>
          <td><b>Product:-</b>
           '. $finishedproduct['item_name'] .'
          </td>
          <td><b>Contract Name:-</b>
           '. $contractname['title'] . '(' . $contractname['workorder'] . ')
          </td>
        </tr>
        <tr>
        <td><b>Quantity:-</b>
         '.$totalorderqty['quantity'].' KM
        </td>
        <td><b>Start Date:-</b>
        '. date('d-M-Y', strtotime($productionorder['startdate'])) .'
       </td>
      </tr>
        <tr>
          
          <td><b>End Date:-</b>
           '.date('d-M-Y', strtotime($productionorder['enddate'])).'
          </td>
        </tr>
        </thead>
        </table>
       ';
      
        $html .='
        <table cellspacing="0" cellpadding="3" border="1px" style="font-size:8px;">
        <thead>
          <tr>
          <th width="33.33%" ><strong>Planned Qty:-</strong>'. sprintf('%.2f', $productionorder['plannedqty']) .' KM</th>
          <th width="33.33%" ><strong>Prepared Qty:-</strong>'.sprintf('%.2f', $productionorder['plannedqty'] -$preparedqty) .' KM</th>
          <th width="33.34%" ><strong>Pending Qty:-</strong>'.sprintf('%.2f', $productionorder['plannedqty'] -$preparedqty) .' KM</th>
          </tr>
        </thead>
        </table>
    </td>
  </tr>
</table>';

  $html .='
  <h6 style="text-align:center;font-size:10px;">Process Details</h6>
<table cellspacing="0" cellpadding="3" border="1px" style="font-size:8px;">
    <thead>
      <tr>
      <th width="05%" ><strong>S.No.</strong></th>
      <th width="50%" ><strong>Process Name</strong></th>
      <th width="15%" ><strong>Start Date</strong></th>
      <th width="15%" ><strong>End Date</strong></th>
      <th width="15%" ><strong>Prepared Qty(KM)</strong></th>
      </tr>
    </thead>

    <tbody>';
    foreach ($processname as $process) {
      $checkdailysheet = $this->comman->checkdailysheet($productionorder['po_id'], $process['id']);

      if (!empty($checkdailysheet)) {
          $quantity = '';
          $startdate = '';
          $completedate = '';
          foreach ($checkdailysheet as $key => $value) {
              $quantity += $value['production_shift_a'] + $value['production_shift_b'];
  
              if ($key === array_key_first($checkdailysheet)) {
                  $startdate = date('d-m-Y', strtotime($value['production_date']));
              }
              if ($key === array_key_last($checkdailysheet)) {
                  $completedate = date('d-m-Y', strtotime($value['production_date']));
              }
          };

          $html .='
          <tr>
            <th width="05%" >'.$i.'.</th>
            <td width="50%">'.$process['process_name'].'</td>
            <td width="15%">'.$startdate.'</td>
            <td width="15%">'.$completedate.'</td>
            <td width="15%" style ="text-align:right;" >'.sprintf('%.2f', $quantity).'</td>
            </tr>';
            $i++;
          } else {
              continue;
          }
      }
    $html .='
      </tbody>
    </table>';



    $html .='
    <h6 style="text-align:center;font-size:10px;">Raw Material</h6>
  <table cellspacing="0" cellpadding="3" border="1px" style="font-size:8px;">
      <thead>
        <tr>
        <th width="05%" ><strong>S.No.</strong></th>
        <th width="51%" ><strong>Item Name</strong></th>
        <th width="22%" ><strong>Required Qty</strong></th>
        <th width="22%" ><strong>Available Qty</strong></th>
        </tr>
      </thead>
  
      <tbody>';
      $designsheetno = $this->Comman->getdesignsheetno($productionorder['contract_id'], $productionorder['item_id']);
      $designitemsdetails = $this->Comman->getdesignmaterials($designsheetno['designsheetno']);
     foreach ($designitemsdetails as $designsheet) {
         $itemname = $this->Comman->getitemname($designsheet['item_id']);
         $designitemqty = $this->Comman->getdesignmaterialqty($designsheet['designsheetno'], $designsheet['item_id']);
         $perkmQty = $designitemqty['sum']/$designsheetno['quantity'];
         $reqQty = $perkmQty * $productionorder['plannedqty'];

         $designitemqty = $this->Comman->todayopeningstock($designsheet['item_id'],$productionorder['issuedate']);;
  
            $html .='
            <tr>
              <th width="05%" >'.$j.'.</th>
              <td width="51%">'. $itemname['item_name'].'</td>
              <td width="22%" style ="text-align:right;" >'.sprintf('%.2f', $reqQty).'</td>
              <td width="22%" style ="text-align:right;" >'.sprintf('%.2f', $designitemqty).'</td>
              </tr>';
              $j++;
            } 
      $html .='
        </tbody>
      </table>';

    $date = date('d-m-y');
$pdf->writeHTMLCell(0, 0, '', '', utf8_encode($html), 0, 1, 0, true, '', true);
ob_end_clean();
echo $pdf->Output('Production_Order_Details_'.$date.'.pdf');
exit;
?>