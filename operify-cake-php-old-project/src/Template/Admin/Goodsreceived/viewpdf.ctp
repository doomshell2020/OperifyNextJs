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



$i=1;

if($req_data['Goodsreceived.vendor_id'] != ''){
  $vendorn = $this->Comman->findvendornames($req_data['Goodsreceived.vendor_id']);
  $vendorname = $vendorn['name'];
}

if($req_data['DATE(Goodsreceived.inwarddate) >='] != ''){
  $datefrom = date("d-m-Y", strtotime($req_data['DATE(Goodsreceived.inwarddate) >=']));
}

if($req_data['DATE(Goodsreceived.inwarddate) <='] != ''){
  $dateto = date("d-m-Y", strtotime($req_data['DATE(Goodsreceived.inwarddate) <=']));
}





$html .='
<h3 style="text-align:center;font-size:10px;">Vendor Details</h3>';

if($vendorname){;
  $html .='
  <p><b>Vendor Name:-</b>'. $vendorname .'</p>';
}

if($datefrom){;
  $html .='
  <span><b>Date From:-</b>'. $datefrom .'</span>       
  <span style ="text-align:right;"><b>Date To:-</b>'. $dateto .'</span>
  <p></p>';
}



   if($vendorname){
    $html .='

    <table cellspacing="0" cellpadding="3" border="1px" style="font-size:8px;">
        <thead>
          <tr>
          <th width="08%"><strong>S.No.</strong></th>
          <th width="25%"><strong>GRN Inward Date</strong></th>
          <th width="13%"><strong>GRN No.</strong></th>
          <th width="13%"><strong>PO No.</strong></th>
          <th width="19%"><strong>Bill No.</strong></th>
          <th width="22%"><strong>Total Amount</strong></th>
          </tr>
        </thead>
    
        <tbody>';
        foreach ($goodsreceived as $value) {
          $vendor_id = $this->Comman->findvendornames($value['vendor_id']);
          $getpo = $this->Comman->getPurchaseOrder($value['purchaseorder_id']);
    
              $html .='
              <tr>
              <td width="08%">'.$i.'.</td>
              <td width="25%">'.date("d-m-Y", strtotime($value['inwarddate'])).'</td>
              <td width="13%">'.$value['id'].'</td>
              <td width="13%">'.$getpo['id'].'</td>
              <td width="19%">'.$value['bill_no'].'</td>
              <td width="22%" style ="text-align:right;">'.number_format((float) $value['total_amt'], 2, '.', '').'</td>
              </tr>';
               
          $i++;
        }
        $html .='
          </tbody>
        </table>';

   }else{
    $html .='

    <table cellspacing="0" cellpadding="3" border="1px" style="font-size:8px;">
        <thead>
          <tr>
          <th width="06%"><strong>S.No.</strong></th>
          <th width="14%"><strong>GRN Inward Date</strong></th>
          <th width="10%"><strong>GRN No.</strong></th>
          <th width="10%"><strong>PO No.</strong></th>
          <th width="12%"><strong>Bill No.</strong></th>
          <th width="33%"><strong>Vendor</strong></th>
          <th width="15%"><strong>Total Amount</strong></th>
          </tr>
        </thead>
    
        <tbody>';
        foreach ($goodsreceived as $value) {
          $vendor_id = $this->Comman->findvendornames($value['vendor_id']);
          $getpo = $this->Comman->getPurchaseOrder($value['purchaseorder_id']);
    
              $html .='
              <tr>
              <td width="06%">'.$i.'</td>
              <td width="14%">'.date("d-m-Y", strtotime($value['inwarddate'])).'</td>
              <td width="10%">'.$value['id'].'</td>
              <td width="10%">'.$getpo['id'].'</td>
              <td width="12%">'.$value['bill_no'].'</td>
              <td width="33%">'.$vendor_id['name'].'</td>
              <td width="15%" style ="text-align:right;">'.number_format((float) $value['total_amt'], 2, '.', '').'</td>
              </tr>';
               
          $i++;
        }
        $html .='
          </tbody>
        </table>';
   }




   
// echo $html;die;

    $date = date('d-m-y');
$pdf->writeHTMLCell(0, 0, '', '', utf8_encode($html), 0, 1, 0, true, '', true);
//$pdf->WriteHTML($html, true, false, true, false, '');
ob_end_clean();
echo $pdf->Output('Vendor_Details'. $date.'.pdf');
exit;
?>