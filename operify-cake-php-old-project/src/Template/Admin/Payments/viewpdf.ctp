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


if(isset($payments) && !empty($payments)){
  $vendorn = $this->Comman->findvendornames($payments[0]['vendor_id']);
  $vendorname = $vendorn['name'];
  $searchvendor = $payments[0]['vendor_id'];
}else{
  $searchvendor = $where['Payments.vendor_id'];
}
  

  if($where['Payments.vendor_id'] != ''){
    $vendorn1 = $this->Comman->findvendornames($where['Payments.vendor_id']);
    $vendorname = $vendorn1['name'];
  }
  
  if($where['DATE(Payments.bill_date) >='] != ''){
    $datefrom = date("d-M-Y", strtotime($where['DATE(Payments.bill_date) >=']));
    $searchdate = date("Y-m-d", strtotime($where['DATE(Payments.bill_date) >=']));
  }else{
    $datefrom = date("d-M-Y");
    $searchdate = date("Y-m-d");
  }
  
  if($where['DATE(Payments.bill_date) <='] != ''){
    $dateto = date("d-M-Y", strtotime($where['DATE(Payments.bill_date) <=']));
  }else{
    $dateto = date("d-M-Y");
  }
  $curbalance = $this->Comman->getvendorbalance($searchvendor,$searchdate);

$html .='
<h3 style="text-align:center;font-size:12px;">Ledger</h3>

<p style="text-align:center;font-size:08px;"><b>'. $vendorname .'</b></p>
<p style="text-align:center;font-size:08px;">Period (<b>'. $datefrom .'</b> To <b>'. $dateto .'</b>)</p>
<p style="text-align:center;font-size:08px;"><b>Opening Balance - '. $curbalance .'</b></p>';



$html .='
<table cellspacing="0" cellpadding="3" border="1px" style="font-size:8px;">
    <thead>
      <tr>
      <th width="10%"><strong>Date</strong></th>
      <th width="42%"><strong>Description</strong></th>
      <th width="16%"><strong>Credit Amount</strong></th>
      <th width="16%"><strong>Debit Amount</strong></th>
      <th width="16%"><strong>Balance</strong></th>
      </tr>
    </thead>

    <tbody>';
    
    foreach ($payments as $intusr) {
      $vendor_id = $this->Comman->findvendornames($intusr['vendor_id']);

      if ($intusr['store_type'] == '1') {
        $description = 'Bill No. ' . $intusr['bill_no'] . ' With <br> ' . $intusr['remark'];
      } else {
        $description = 'Recipt No. ' . $intusr['receipt_no'] . ' With <br> ' . $intusr['remark'];
      }

      if ($intusr['store_type'] == '1') {
        $credit = number_format((float) $intusr['total_amt'], 2, '.', '');
      } else {
        $credit = '-';
      }

      if ($intusr['store_type'] == '2') {
        $debit = number_format((float) $intusr['total_amt'], 2, '.', '');
      } else {
        $debit = '-';
      }

      if ($intusr['store_type'] == '1') {
        $curbalance = $curbalance + $intusr['total_amt'];
      } else {
        $curbalance = $curbalance - $intusr['total_amt'];
      }
          $html .='
          <tr>
          <td width="10%">'.date("d-m-Y", strtotime($intusr['bill_date'])).'</td>
          <td width="42%">'.$description.'</td>
          <td width="16%" style="text-align:right;">'.$credit.'</td>
          <td width="16%" style="text-align:right;">'.$debit.'</td>
          <td width="16%" style="text-align:right;">'.number_format((float) $curbalance, 2, '.', '').'</td>
          </tr>';
           
      $i++;
    
    }
    $html .='
    <tr>
    <th colspan = "4" style="text-align:right;"><strong>Closing Balance</strong></th>
    <th style="text-align:right;"><strong>'.number_format((float) $curbalance, 2, '.', '').'</strong></th>
    </tr>
      </tbody>
    </table>';



   
// echo $html;die;

    $date = date('d-m-y');
$pdf->writeHTMLCell(0, 0, '', '', utf8_encode($html), 0, 1, 0, true, '', true);
//$pdf->WriteHTML($html, true, false, true, false, '');
ob_end_clean();
echo $pdf->Output('Vendor_payments_Details'. $date.'.pdf');
exit;
?>