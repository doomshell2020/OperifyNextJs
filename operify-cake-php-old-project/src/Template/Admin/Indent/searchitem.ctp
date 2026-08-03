<table id="example14" class="table table-bordered table-striped">
                     <thead>
                        <tr>
                           <th>#</th>
                           <th>Indent Id</th>
                           <th>Items</th>
                           <th>Requested Qty</th>
                           <!-- <th>Indent Status</th> -->
                           <th>Genrated Date</th>
                           <th>Indent / PO</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php $page = $this->request->params['paging']['']['page'];
                        $limit = $this->request->params['paging']['']['perPage'];
                        $counter = ($page * $limit) - $limit + 1;
                        if (isset($users) && !empty($users)) {
                           $counter = 1;
                           foreach ($users as $intusr) {
                              //  pr($intusr); 
                              $var = $this->Comman->indentitemquantity($intusr['indent_id']);
                        ?>
                              <tr>
                                 <!-- <thead> -->
                                 <td><?php echo $counter; ?></td>
                                 <td><?php echo $intusr['indent_id']; ?></td>
                                 <td>
                                    <table class="table table-bordered" style="margin-bottom:0px;">
                                       <!-- <thead> -->
                                       <tr>
                                          <th>Name</th>

                                          <th>Qty</th>
                                          <th>PO In-Stock</th>

                                       </tr>
                                       <!-- </thead> -->
                                       <tbody>
                                          <?php
                                          $indentdetail = $this->Comman->indentdetail($intusr['indent_id']);
                                          // pr($indentdetail); die;

                                          $totl = 0;

                                          foreach ($indentdetail as $value) {
                                             //pr($value); 
                                             $unitname = $this->Comman->getunitnamepoview($value['additem']['unit_id']);

                                             $remain = $value['quantity'] - $value['return_qty'];
                                             if ($remain > 0) {
                                                $totl += $remain;
                                             }

                                          ?>
                                             <tr>
                                                <td>
                                                   <?php echo $value['additem']['item_name']; ?>
                                                </td>

                                                <td style="color:red;"><?php echo $value['quantity']; ?></td>
                                                <?php
                                                $totalrecivied = $this->Comman->totalstockregisteropeningrecivied($value['item_id']);
                                                $totaldispatched = $this->Comman->totalstockregisteropeningdispatched($value['item_id']);
                                                //  pr($totaldispatched); 
                                                // $remain=$totalrecivied[0]['quantity']-$totaldispatched[0]['sum']; 
                                                $remain = $totalrecivied[0]['quantity'];

                                                ?>
                                                <td style="color:green;"><?php echo $remain; ?></td>
                                                <!-- <td style="color:green;"><? php //echo $value['return_qty']; 
                                                                              ?></td> -->
                                             </tr>
                                          <?php } ?>
                                       </tbody>
                                    </table>
                                 </td>
                                 <td><?php echo $var[0]['quantity']; ?></td>
                                 <!-- <td><?php // if($totl==0){ echo "Completed"; }else{ echo "<strong style='color:red;'>Pending</strong>"; }
                                          ?></td> -->
                                 <td><?php echo date("d-m-Y", strtotime($intusr['added_time'])); ?></td>
                                 <td>
                                    <div style="display:flex; align-items:flex-start">
                                       <a target="_blank" title="Download Indent" href="<?php echo ADMIN_URL; ?>indent/view/<?php echo $intusr['indent_id']; ?>"><i class="fa fa-file-pdf-o" style="font-size: 20px !important;"></i>&nbsp;</a>

                                       <?php if ($totl != 0) { ?>&nbsp;
                                       <a target="_blank" title="PO" href="<?php echo ADMIN_URL; ?>purchaseorder/add/<?php echo $intusr['indent_id']; ?>"><img src="<?php echo SITE_URL; ?>/images/subMenu/po.png" style="width: 20px;">&nbsp;</a> <?php } ?>
                                    </div>
                                 </td>
                              </tr>
                           <?php $counter++;
                           }
                        } else { ?>
                        <?php } ?>
                     </tbody>
                  </table>