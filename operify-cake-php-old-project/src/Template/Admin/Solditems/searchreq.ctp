
 <table id="" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                          <th>Sale Id</th>
                          <th>Name</th>
                          <th>Description</th>
                          <th>Sale Date</th>
                          <th>View</th>
                          <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $page = $this->request->params['paging']['']['page'];
                          $limit = $this->request->params['paging']['']['perPage'];
                          $counter = ($page * $limit) - $limit + 1;
                          if(isset($sold_item) && !empty($sold_item)){
                            foreach($sold_item as $intusr){ //pr($intusr);
                              ?>
                        <tr>
                          <td><?php echo $intusr['id']; ?></td>
                          <td><?php echo $intusr['customer_name']; ?></td>
                          <td><?php echo $intusr['description']; ?></td>
                          <td><?php echo date('Y-m-d',strtotime(($intusr['pay_date']))); ?></td>

                          <td>
                              <?php if ($intusr['status'] != "Cancel"){ ?>
                              <div style= "display:flex";>
                                <a  title = "Bill PDF" href = "<?php echo SITE_URL; ?>admin/solditems/billgenerate/<?php echo $intusr['id']; ?>" style="padding:5px; background:#870606; display:flex; align-items:center; color:#fff; width:max-content; border-radius:3px; font-weight:normal; margin-right:5px;" target = "_blank">
                                <i class="far fa-file-pdf" style="font-size:16px; margin-right:4px;"></i> <span style="line-height:1;">School Receipt</span>
                                </a>
                                <a title = "Bill PDF" href = "<?php echo SITE_URL; ?>admin/solditems/salebillgenerate/<?php echo $intusr['id']; ?>" style="padding:5px; background:#06875d; display:flex; align-items:center; color:#fff; width:max-content; border-radius:3px; font-weight:normal;" target = "_blank">
                                <i class="far fa-file-pdf" style="font-size:16px; margin-right:4px;"></i> <span style="line-height:1;">Parent Receipt</span>
                                </a>
                              </div>
                              <?php }else{ ?>
                                <?php if ($intusr['status'] == "Cancel"  ){ ?>
<a   style="padding:5px 8px; background:#c50b0b; color:#fff; width:max-content; border-radius:3px; font-weight:normal;" target = "_blank" title = "Cancelled"><i class="fas fa-times"></i> Cancelled</a>
                               <?php   
                               } else { 

                                echo     $intusr['status'];
                                } ?>
                              <?php } ?>
                          </td>
                          <td> 
                        <?php 
                        if  ($intusr['status'] != "Cancel"  &&  date('Y-m-d',strtotime(($intusr['saledate']))) > '2022-03-29'){ ?> 
                            <a href = "<?php echo SITE_URL; ?>/admin/solditems/cancelrequest/<?php echo $intusr['id']; ?>" class = "cancelrequest" style="padding:5px 8px; background:#c50b0b; color:#fff; width:max-content; border-radius:3px; font-weight:normal;" title = "Cancel"><i class="fas fa-times"></i> Cancel</a>
                              <?php } ?>
                          </td>
                        </tr>
                        <?php $counter++; } }else{ ?>
                        <?php } ?>
                    </tbody>
                  </table>
