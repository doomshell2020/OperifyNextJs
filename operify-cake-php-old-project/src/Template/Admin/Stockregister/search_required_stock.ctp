<table id="" class="table table-bordered table-striped">
  <thead>
    <tr>
      <th style="width: 16%;">S.No</th>
      <th style="width: 16%;">Product Name</th>
      <?php
      foreach ($contracts as $contractsIds) {
        $getContractName = $this->comman->findcontractname($contractsIds['contract_id']); ?>
        <th><?= $getContractName['title']; ?></th>
      <?php  }
      ?>
      <th>Total Available</th>
      <th>Required</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <?php
      $page = $this->request->params['paging']['Additem']['page'];
      $limit = $this->request->params['paging']['Additem']['perPage'];
      $count = ($page * $limit) - $limit + 1;

      foreach ($products as $productsName) {

        $totalReqiredStock = 0;

        $todaydate = date('Y-m-d');
        $openingstock = $this->comman->todayopeningstock($productsName['id'], $todaydate);
        $receivedtock = $this->comman->todayrecivedstock($productsName['id'], $todaydate);
        $issuedstock = $this->comman->todayissuedtock($productsName['id'], $todaydate);
        $reversestock = $this->comman->todayreversestock($productsName['id'], $todaydate);
        $returnstock = $this->comman->todayreturnstock($productsName['id'], $todaydate);
        $closingstock = $openingstock + $receivedtock - $issuedstock + $reversestock - $returnstock;
        $closingstock = number_format((float)$closingstock, 2, '.', '');

      ?>

        <td><?= $count; ?></td>
        <td><?= $productsName['item_name']; ?></td>
        <?php
        foreach ($contracts as $contractsIds) {
          $stockCount = 0;

          $checkRawmaterial = $this->comman->checkRawmaterial($contractsIds['contract_id'], $productsName['id']);

          if ($checkRawmaterial) {
            // $startdate = '2025-03-01';
            // $enddate = '2025-03-31';
            $getContractFinished = $this->comman->getContractFinished($contractsIds['contract_id'], $startDate, $endDate);

            foreach ($getContractFinished as $finishedProduct) {
              $getDesignsheet = $this->comman->getdesignsheetno($contractsIds['contract_id'], $finishedProduct['item_id']);
              $getDesignSheetDetails = $this->comman->getdesignsheetitemname($productsName['id'], $getDesignsheet['id']);
              $stockCount += $getDesignSheetDetails['km_item_qty'] * $finishedProduct['plannedqty'];
            }
          } else {
            $stockCount = 0;
          }

          $totalReqiredStock += $stockCount;
        ?>
          <td><?= $stockCount; ?></td>
        <?php  }
        ?>
        <td><?= $closingstock; ?></td>
        <td><?= (($totalReqiredStock - $closingstock) > 0) ? $totalReqiredStock - $closingstock : 0; ?></td>
    </tr>
  <?php
        $count++;
      }
  ?>

  </tbody>
</table>
<?php echo $this->element('admin/pagination'); ?>