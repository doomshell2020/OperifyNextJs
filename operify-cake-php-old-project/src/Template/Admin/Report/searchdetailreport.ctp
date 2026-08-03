<div class="table-responsive">


  <table id="" class="table table-bordered table-striped">
    <tbody>
      <tr>
        <td><a id="" style="position: absolute;top: 122px;right: 46px;" class="btn btn-info btn-sm pull-right" target="_blank" href="<?php echo ADMIN_URL; ?>report/user_dailyfee"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Export PDF</a></td>
      </tr>
      <?php
      $session = $this->request->session();
      if ($mode) {
        $session->delete($mode);
        $session->write('mode', $mode);
      }

      if ($selectField) {
        $session->delete($selectField);
        $session->write('selectField', $selectField);
      }

      ?>

      <tr>
        <td><b> Receipt Insert Date
            <? if ($datefrom && $dateto && $datefrom != '1970-01-01' && $dateto2 != '1970-01-01') {

              $datefromh = $datefrom;
              $session = $this->request->session();

              $session->delete($datefromh);
              $session->write('datefrom', $datefromh);
              $session->delete($dateto2);
              $session->write('dateto', $dateto2);
              $dateto2h = $dateto2;
              echo "( From " . date('d-m-Y', strtotime($datefrom)) . " To " . date('d-m-Y', strtotime($dateto2)) . " )";
            } else {
              $datefromh = date('Y') . "-04-01";
              $dateto2h = date('d-m-Y');
              $session = $this->request->session();
              $session->delete($datefromh);
              $session->write('datefrom', $datefromh);
              echo "( From 01-04-" . date('Y') . " To " . $dateto2h . " )";
              $dateto2h = date('Y-m-d', strtotime($dateto2h));
              $session->delete($dateto2h);
              $session->write('dateto', $dateto2h);
            } ?></b></td>

        <td><b>CASH</b></td>
        <td><b>CHEQUE</b></td>
        <td><b>DD</b></td>
        <td><b>NETBANKING</b></td>
        <td><b>CREDIT CARD/DEBIT CARD</b></td>
        <td><b>Total</b></td>
        <td><b>Action</b></td>
        <?php

        function getDatesFromRange($start, $end, $format = 'Y-m-d')
        {
          // Declare an empty array
          $array = array();

          // Variable that store the date interval
          // of period 1 day
          $interval = new DateInterval('P1D');

          $realEnd = new DateTime($end);
          $realEnd->add($interval);
          $period = new DatePeriod(new DateTime($start), $interval, $realEnd);
          // Use loop to store date into array
          foreach ($period as $date) {
            $array[] = $date->format($format);
          }

          // Return the array elements
          return $array;
        }

        $select_date = getDatesFromRange(date('Y-m-d', strtotime($datefrom)), date('Y-m-d', strtotime($dateto2)));
        // pr($select_date); die;
        ?>
      </tr>

      <?php
      foreach ($select_date as $key => $value) {

        $cash_fee_count =  $this->comman->cashfee($value);
        // pr($cash_fee_count); die;   
        $cheque_fee_count = $this->comman->chequefee($value);
        $dd_fee_count = $this->comman->ddfee($value);
        $netbanking_fee_count = $this->comman->netbankingfee($value);
        $ccdc_fee_count = $this->comman->ccdcfee($value);
        $total_count =  $cash_fee_count['sum'] + $cheque_fee_count['sum'] + $dd_fee_count['sum'] + $netbanking_fee_count['sum'] + $ccdc_fee_count['sum'];

      ?>
        <tr>
          <td> <?php echo date('d-m-Y', strtotime($value)); ?> </td>
          <td> <?php echo ($cash_fee_count['sum']) ? $cash_fee_count['sum'] : '0'; ?> </td>
          <td> <?php echo ($cheque_fee_count['sum']) ? $cheque_fee_count['sum'] : '0'; ?> </td>
          <td> <?php echo ($dd_fee_count['sum']) ? $dd_fee_count['sum'] : '0'; ?> </td>
          <td> <?php echo ($netbanking_fee_count['sum']) ? $netbanking_fee_count['sum'] : '0'; ?></td>
          <td> <?php echo ($ccdc_fee_count['sum']) ? $ccdc_fee_count['sum'] : '0'; ?></td>
          <td> <?php echo $total_count; ?> </td>
          <td> <a href="#">View</a></td>
        </tr>

      <?php } ?>
      <tr>
        <td><strong style="color:green;">Net Received </strong></td>
        <?
        $ttys = 0;
        $tothh = 0;
        foreach ($mode as $k => $rt) {
          $otherfee = $this->Comman->findofcashdate($datefromh, $dateto2h, $rt);
        ?>
          <td style="color:green;font-weight:bold;">
            <? if ($otherfee[0]['sum']) {
              $tothh += $otherfee[0]['sum'];
              echo $otherfee[0]['sum'];
            } else {
              $tothh += 0;
              echo '0';
            } ?>
          </td>
        <?

        } ?>

      </tr>

    </tbody>
  </table>