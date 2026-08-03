<table class="table table-bordered table-striped">
  <thead>
    <tr>
      <th>GRN No.</th>
      <th>PO Id</th>
      <th>G.R.N. Inward</th>
      <th>Bill No.</th>
      <th>Bill Date</th>
      <th>Supplier</th>
      <th>Total Qty.</th>
      <th>Total Received Qty.</th>
      <th>Total Amount (INR)</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    <?php $page = $this->request->params['paging']['Goodsreceived']['page'];
    $limit = $this->request->params['paging']['Goodsreceived']['perPage'];
    $counter = ($page * $limit) - $limit + 1;
    if (isset($goodsreceived) && !empty($goodsreceived)) {
      foreach ($goodsreceived as $intusr) {

        $vendor_id = $this->Comman->findvendornames($intusr['vendor_id']);
        $po = $this->Comman->getpoqty($intusr['purchaseorder_id']);
        $remain = $this->Comman->goodsrecivied($intusr['purchaseorder_id'], $intusr['id']);
        $getpo = $this->Comman->getPurchaseOrder($intusr['purchaseorder_id']);

    ?>
        <tr>
          <td>
            <a class="viewgrndetails"
              href="<?php echo SITE_URL; ?>admin/goodsreceived/viewgrndetail/<?php echo $intusr['id']; ?>">
              <?php echo $intusr['id']; ?>
            </a>
          </td>

          <td>
            <a class="viewgrndetails"
              href="<?php echo ADMIN_URL; ?>purchaseorder/viewpodetail/<?php echo $intusr['purchaseorder_id'] . "/" . $getpo['is_revised'] . "/" . $getpo['id']; ?>">
              <?php echo $intusr['purchaseorder_id']; ?>
            </a>
          </td>

          <td>
            <?php echo date("d-m-Y", strtotime($intusr['inwarddate'])); ?>
          </td>
          <td>
            <?php echo $intusr['bill_no']; ?>
          </td>
          <td>
            <?php echo date("d-m-Y", strtotime($intusr['bill_date'])); ?>
          </td>
          <td>
            <?php echo $vendor_id['name']; ?>
          </td>
          <td style="text-align:right;">
            <?php echo $po['total_qty']; ?>
          </td>
          <td style="text-align:right;">
            <?php echo $intusr['total_qty']; ?>
          </td>
          <!-- <td><? php // echo $remn=$po['total_qty']-$remain[0]['quantity']; 
                    ?></td> -->
          <td style="text-align:right;">
            <?php //echo $intusr['total_amt']; 
            ?>
            <?php echo number_format($intusr['total_amt']); ?>
          </td>
          <!-- <td>
                                    <?php if ($intusr['status'] == 'O') {
                                      echo "<strong style='color:red;'>Open</strong>";
                                    } else {
                                      echo "<strong style='color:green;'>Close</strong>";
                                    } ?>
                                 </td> -->
          <td><a title="View PDF" target="_blank"
              href="<?php echo ADMIN_URL; ?>goodsreceived/view/<?php echo $intusr['id']; ?>"><i class="fa fa-file-pdf-o"
                style="
                              font-size: 20px; color:#c51515;
                              "></i>&nbsp;</a>
            <a title="Download" class="fa fa-download fa-lg text-green"
              href="<?php echo ADMIN_URL; ?>goodsreceived/view/<?php echo $intusr['id']; ?>" download="NewName.pdf"></a>
          </td>
        </tr>
      <?php $counter++;
      }
    } else { ?>
    <?php } ?>
  </tbody>
</table>
<?php echo $this->element('admin/pagination'); ?>

<script>
  $('.viewgrndetails').click(function(e) {
    e.preventDefault();
    $('#editsortsgrn').modal('show').find('.modal-body').load($(this).attr('href'));
  });
</script>

<div class="modal fade" id="editsortsgrn">
  <div class="modal-dialog " style="max-width: 900px !important;">
    <div class="modal-content">
      <div class="modal-body" style="background:white;"></div>
    </div>
  </div>
</div>