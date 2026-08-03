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
//$pdf->SetMargins(0, 25, 0, true);

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


$suppliername = $this->Comman->findvendornames($contractdetail['supplier_id']);
foreach ($finsheddetails as $finshed) {
  $contractpro = $this->Comman->checkproduction($contractdetail['id'], $finshed['product_id']);
  foreach ($contractpro as $contractpro1) {
  $labour += $contractpro1['manpower_day'] + $contractpro1['manpower_night'];
  $oprational += $contractpro1['nextday8am'] - $contractpro1['reading8am'];
  }
  }

$i=1;
$j=1;
$k=1;
$l=1;



$html .='
<table border="1px">
  <tr>
    <td>
      <table width="100%" style="padding: 1px 1px 0px 0px;" align="left">
         <tbody>
            <tr>
               <td style="text-align:left" width="50%">
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
         </tbody>
      </table>
 
    
      <h3 style="text-align:center;font-size:10px; border-top:1px solid #000; border-bottom:1px solid #000;">Contract Details </h3>
    
      <table cellspacing="0" cellpadding="3" border="0px" style="font-size:8px;">
        <thead>
          <tr>
          <td><b>Work Order:-</b>
           '. $contractdetail['workorder'] .'
          </td>
          <td></td>
        </tr>
        <tr>
          <td><b>Title:-</b>
           '. $contractdetail['title'] .'
          </td>
          <td><b>Issue Date:-</b>
           '. date('d-M-Y', strtotime($contractdetail['issuedate'])) .'
          </td>
        </tr>
        <tr>
          <td><b>Contract Start Date:-</b>
           '. date('d-M-Y', strtotime($contractdetail['contract_start_date'])) .'
          </td>
          <td><b>Contract End Date:-</b>
           '. date('d-M-Y', strtotime($contractdetail['contract_end_date'])) .'
          </td>
        </tr>
        <tr>
          <td><b>Supplier Name:-</b>
           '. $suppliername['name'] .'
          </td>
          <td><b>Cost:-</b>
           '.sprintf('%.2f',$contractdetail['cost']).'
          </td>
        </tr>
        <tr>
          <td><b>Labour Cost:-</b>
           '. $labour .'
          </td>
          <td><b>Operational Cost:-</b>
           '. sprintf('%.2f',$oprational ).'
          </td>
        </tr>
        </thead>
    
        </table>
    </td>
  </tr>
</table>';
  





  $html .='
<h6 style="text-align:center;font-size:10px;">Finished Products</h6>';


foreach ($finsheddetails as $finshed) {
  $finsheditemname = $this->Comman->getitemname($finshed['product_id']);
  $contractexists = $this->Comman->checkproduction($contractdetail['id'], $finshed['product_id']);
  $poexists = $this->Comman->findfinishedqty($contractdetail['id'], $finshed['product_id']); 

    $prepardqty = '';
    foreach($contractexists as $outhersheathing){
      if($outhersheathing['productprocess_id'] == 8){
        $prepardqty += $outhersheathing['production_shift_a'] + $outhersheathing['production_shift_b'];
      }
    }

    $plannedqty = '';
    foreach($poexists as $itemqty){
        $plannedqty += $itemqty['plannedqty'];
    }
  ;
  $html .='
  <table width ="100%" cellspacing="0" cellpadding="3" border="1px" style="font-size:8px; margin-top:5px;">
      <thead>
        <tr>
        <td width="31.5%"><b>Product:-</b> '.$finsheditemname['item_name'].'</td>
        <td width="16%" ><b>Quantity:-</b> '.sprintf('%.2f', $finshed['quantity']).' KM</td> 
        <td width="19.5%" ><b>Planned Qty:-</b> '.sprintf('%.2f', $plannedqty).' KM</td> 
        <td width="16%" ><b>Prep Qty:-</b> '.sprintf('%.2f', $prepardqty).' KM</td> 
        <td width="17%" ><b>Price:-</b> '.sprintf('%.2f', $finshed['price']).'</td>
        </tr>
      </thead>
  </table>
  
  
  <table width ="100%" cellspacing="0" cellpadding="3" border="1px" style="font-size:8px; margin-top:5px;">
       ';
  
      if ($contractexists) { ;
      $html .='
      <tbody>
      <tr>
      <th width="05%" ><strong>S.No.</strong></th>
      <th width="17.5%" ><strong>Process Name</strong></th>
      <th width="09%" ><strong>Start Date</strong></th>
      <th width="09%" ><strong>End Date</strong></th>
      <th width="34%" ><strong>PO No.</strong></th>
      <th width="13.5%" ><strong>Planned Qty(KM)</strong></th>
      <th width="12%" ><strong>Prep Qty(KM)</strong></th>
      </tr>
      ';

    $i = 1;
    foreach ($processname as $process) {
      $getdailysheet = $this->Comman->getdailysheet($contractdetail['id'], $finshed['product_id'], $process['id']);

      if (!empty($getdailysheet)) {
        $quantity = '';
        $startdate = '';
        $completedate = '';
        $po_no = [];
        foreach ($getdailysheet as $key => $value) {
          $quantity += $value['production_shift_a'] + $value['production_shift_b'];
          $po_no[] = $value['po_id'];
          if ($key === array_key_first($getdailysheet)) {
            $startdate = date('d-m-Y', strtotime($value['production_date']));
          }
          if ($key === array_key_last($getdailysheet)) {
            $completedate = date('d-m-Y', strtotime($value['production_date']));
          }
        }
        $newpo_no = array_unique($po_no);
          $html .='
          <tr>
            <th width="05%" >'.$i.'.</th>
            <td width="17.5%" >'.$process['process_name'].'</td>
            <td width="09%" >'.$startdate.'</td>
            <td width="09%" >'.$completedate.'</td>
            <td width="34%" >'.implode(',', $newpo_no).'</td>
            <td width="13.5%"  style ="text-align:right;" >'.sprintf('%.2f', $quantity).'</td>
            <td width="12%"  style ="text-align:right;" >'.sprintf('%.2f', $quantity).'</td>
            </tr>';
            $i++;
          } else {
            continue;
          }
        }
    $html .='
      </tbody>';

    } else { ;
      $html .='
      <tr>
      <td colspan="7" style="text-align:center;">Production Not Started Yet.</td>
      </tr>';
      } 
      $html .='
      </table>
      <table width ="100%" cellspacing="0" cellpadding="3" border="1px" style="font-size:8px; margin-top:5px;">
      <thead>
      <tr>
      <th width="100%" style="text-align:center;" ><strong>Raw Material</strong></th>
      </tr>
      </thead>
  </table>
      ';
      $html .='
    <table cellspacing="0" cellpadding="3" border="1px" style="font-size:8px;">
        <thead>
          <tr>
          <th width="04%" ><strong>No.</strong></th>
          <th width="54%" ><strong>Item Name</strong></th>
          <th width="16%" ><strong>Qty(As per Design)</strong></th>
          <th width="13%" ><strong>Issued Qty</strong></th>
          <th width="13%" ><strong>Pending Qty</strong></th>
          </tr>
        </thead>
    
        <tbody>';
        $designsheetno = $this->Comman->getdesignsheetno($contractdetail['id'],  $finshed['product_id']);
          $designitemsdetails = $this->Comman->getdesignmaterials($designsheetno['designsheetno']);
          $k = 1;
          foreach ($designitemsdetails as $designsheet) {
            $getitemname = $this->Comman->getitemname($designsheet['item_id']);

            if($designsheet['is_group'] > 0){
              $categoryName = $this->Comman->getcategorynmae($getitemname['category_id']);
              $itemname = $categoryName['category_name'];
            }else{
              $itemname = $getitemname['item_name'];
            }

            $designitemqty = $this->Comman->getdesignmaterialqty($designsheet['designsheetno'], $designsheet['item_id']);

            $issueitemqty = $this->Comman->rawitempendingqty($designsheet['item_id'], $finshed['product_id'],$contractdetail['id'],$designsheet['is_group']);
            $reverseqty = $this->Comman->rawitemreverseqty($designsheet['item_id'], $finshed['product_id'],$contractdetail['id'],$designsheet['is_group']);
            $pendingqty = $designitemqty['sum'] - $issueitemqty['sum'] + $reverseqty['sum'];

              $html .='
              <tr>
                <th width="04%" >'.$k.'.</th>
                <td width="54%" >'.$itemname.'</td>
                <td width="16%" style ="text-align:right;" >'.sprintf('%.2f',$designitemqty['sum']).'</td>
                <td width="13%" style ="text-align:right;" >'.sprintf('%.2f', $issueitemqty['sum'] - $reverseqty['sum']).'</td>
                <td width="13%" style ="text-align:right;" >'.sprintf('%.2f', $pendingqty).'</td>
                </tr>';

                if ($designsheet['is_group'] > 0) {
                  $categoryItems = $this->Comman->getitembycategory($getitemname['category_id']);
                  
                  foreach ($categoryItems as $category) {
                    $categoryitemname = $category['item_name'];
                    $issuecatItemqty = $this->Comman->rawitempendingqty($category['id'], $finshed['product_id'], $contractdetail['id'], 0);
                    $reverseCatqty = $this->Comman->rawitemreverseqty($category['id'], $finshed['product_id'], $contractdetail['id'], 0);
                    $actualIssued = $issuecatItemqty['sum'] - $reverseCatqty['sum'];
            
                    if($actualIssued == 0){
                      continue;
                    }
                    $html .= '
                    <tr>
                      <th width="04%" ></th>
                      <td width="54%" >' . $categoryitemname . '</td>
                      <td width="16%" style ="text-align:right;" colspan = "1"></td>
                      <td width="13%" style ="text-align:right;" colspan = "1">' . sprintf('%.2f', $actualIssued) . '</td>
                      <td width="13%" style ="text-align:right;" colspan = "1"></td>
                      </tr>';
            
                  }
                }

          $k++;
        }
        $html .='
          </tbody>
        </table>
        <p></p>
        ';
    }

    // <tr>
    // <td colspan ="5" style="border: none;"></td>
    // </tr>



    $html .='
  <h6 style="text-align:center;font-size:10px;">Production Orders</h6>
<table cellspacing="0" cellpadding="3" border="1px" style="font-size:8px;">
    <thead>
      <tr>

      <th width="06%"><strong>PO No.</strong></th>
      <th width="09%"><strong>Issue Date</strong></th>
      <th width="41%"><strong>Product</strong></th>
      <th width="10%"><strong>Planned Qty(KM)</strong></th>
      <th width="10%"><strong>Prepared Qty(KM)</strong></th>
      <th width="09%"><strong>Start Date</strong></th>
      <th width="09%"><strong>End Date</strong></th>
      <th width="06%"><strong>Status</strong></th>
      </tr>
    </thead>

    <tbody>';
    foreach ($podetails as $value) {
      $itemname1 = $this->comman->getitemname($value['item_id']);
      $checkdailysheet = $this->comman->checkdailysheet($value['po_id'], 8);
      $quantity = '';
      foreach ($checkdailysheet as $details) {
        $quantity += $details['production_shift_a'] + $details['production_shift_b'];
        $completedate = date('d-m-Y', strtotime($details['production_date']));
      }
      if($value['status'] == 'C'){
        $status = 'Close' ;
      }else{
        $status = 'Open';
      }
      ;
          $html .='
          <tr>

          <th width="06%">'.$value['po_id'].'</th>
          <th width="09%">'.date('d-m-Y', strtotime($value['issuedate'])).'</th>
          <th width="41%">'.$itemname1['item_name'].'</th>
          <th width="10%" style ="text-align:right;" >'.sprintf('%.2f', $value['plannedqty']).'</th>
          <th width="10%" style ="text-align:right;" >'.sprintf('%.2f',$quantity ? $quantity :0).'</th>
          <th width="09%">'.date('d-m-Y', strtotime($value['startdate'])).'</th>
          <th width="09%">'.date('d-m-Y', strtotime($value['enddate'])).'</th>
          <th width="06%">'.$status.'</th>
           
            </tr>';
           
    }
    $html .='
      </tbody>
    </table>';


    $html .='
    <h6 style="text-align:center;font-size:10px;">Inspection Report</h6>
  <table cellspacing="0" cellpadding="5" border="1px" style="font-size:8px;">
      <thead>
        <tr>
        <th width="15%" ><strong>S.No.</strong></th>
        <th width="55%" ><strong>Inspector Name</strong></th>
        <th width="30%" ><strong>Inspection Date</strong></th>
        </tr>
      </thead>
  
      <tbody>';
      foreach($inspection as $inspectionreport){
        $itemname = $this->Comman->getitemname($designsheet['item_id']);
            $html .='
            <tr>
              <th width="15%" >'.$l.'.</th>
              <td width="55%" >'.$inspectionreport['name'].'</td>
              <td width="30%" >'.date('d-M-Y', strtotime($inspectionreport['inspection_date'])).'</td>
              </tr>';
             
        $k++;
        $unitname="";
        $empty="";
      }
      $html .='
        </tbody>
      </table>';
  

$date = date('d-m-y');
$pdf->writeHTMLCell(0, 0, '', '', utf8_encode($html), 0, 1, 0, true, '', true);
ob_end_clean();
echo $pdf->Output('Contract_Details_'.$date.'.pdf');
exit;
?>