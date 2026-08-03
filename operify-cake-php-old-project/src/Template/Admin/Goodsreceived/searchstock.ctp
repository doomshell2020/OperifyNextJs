<table class="table table-bordered table-striped" width="100%">
                     <thead>
                        <tr>
                           <th width="5%">S.No.</th>
                           <th width="10%">GRN Inward Date</th>
                           <th width="10%">GRN No.</th>
                           <th width="10%">PO No.</th>
                           <th width="10%">Bill No.</th>
                           <th width="20%">Vendor</th>
                           <th width="10%">Total Amount</th>
                        </tr>

                     </thead>
                     <tbody>
                        <?php $page = $this->request->params['paging']['Goodsreceived']['page'];
                        $limit = $this->request->params['paging']['Goodsreceived']['perPage'];
                        $counter = ($page * $limit) - $limit + 1;
                        if (isset($goodsreceived) && !empty($goodsreceived)) {
                           foreach ($goodsreceived as $value) {
                              $vendor_id = $this->Comman->findvendornames($value['vendor_id']);
                              $getpo = $this->Comman->getPurchaseOrder($value['purchaseorder_id']);

                              ?>
                              <tr>
                                 <td>
                                    <?php echo $counter; ?>.
                                 </td>
                                 <td>
                                    <?php echo date("d-m-Y", strtotime($value['inwarddate'])); ?>
                                 </td>
                                 <td>
                                    <a class="viewgrndetails"
                                       href="<?php echo SITE_URL; ?>admin/goodsreceived/viewgrndetail/<?php echo $value['id']; ?>">
                                       <?php echo $value['id']; ?>
                                    </a>
                                 </td>
                                 <td>
                                    <a class="viewgrndetails"
                                       href="<?php echo ADMIN_URL; ?>purchaseorder/viewpodetail/<?php echo $value['purchaseorder_id'] . "/" . $getpo['is_revised'] . "/" . $getpo['id']; ?>">
                                       <?php echo $value['purchaseorder_id']; ?>
                                    </a>
                                 </td>
                                 <td>
                                    <?php echo $value['bill_no']; ?>
                                 </td>
                                 <td>
                                    <?php echo $vendor_id['name']; ?>
                                 </td>

                                 <td style="text-align:right;">
                                    <?php echo number_format((float) $value['total_amt'], 2, '.', ''); ?>
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
   $('.viewgrndetails').click(function (e) {
      e.preventDefault();
      $('#editsortsgrn').modal('show').find('.modal-body').load($(this).attr('href'));
   });
</script>

<div class="modal fade" id="editsortsgrn">
   <div class="modal-dialog " style="max-width: 900px !important;">
      <div class="modal-content">
         <div class="modal-body" style="background: white !important;"></div>
      </div>
   </div>
</div>