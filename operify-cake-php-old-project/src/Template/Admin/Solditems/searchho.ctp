<table class="table table-bordered table-striped">
                     <thead>
                        <tr>
                        <th width="5%">Requisition No.</th>
                           <th width="25%">School</th>
                           <th width="10%">Branch Name</th>
                           <th width="20%">Description</th>
                           <th width="20%">Remark</th>
                           <th width="5%">View</th>
                           <th width="5%">Status</th>
                           <th width="10%">Rq.Date</th>

                        </tr>
                     </thead>
                     <tbody>
                        <?php 
                          $page = $this->request->params['paging'][$this->request->params['controller']]['page'];
                          $limit = $this->request->params['paging'][$this->request->params['controller']]['perPage'];
                          $counter = ($page * $limit) - $limit + 1;
                           if(isset($branch_request) && !empty($branch_request)){ 
                             foreach($branch_request as $intusr){ //pr($intusr);
                                $branch_name = explode("_",$intusr['branch_name']);
                               ?>
                        <tr>
                           <td><?php echo $intusr['id'];?></td>
                           <td><?php echo "Canvas International Pre School (<b>".ucfirst($branch_name[1])."</b>) <br> Unit Of Ingenious Edu Scholars Private Limited";  ?></td>
                           <td><?php echo $intusr['branch_name'];?></td>
                           <td><?php echo $intusr['description'];?></td>
                           <td><?php echo $intusr['remark'];?></td>
                           <td>  <a title="Bill PDF"
                                       href="<?php echo SITE_URL; ?>admin/solditems/soldhobillgenerate/<?php echo $intusr['id']; ?>"
                                       style="padding:5px; background:#870606; display:flex; align-items:center; color:#fff; width:max-content; border-radius:3px; font-weight:normal; margin-right:4px;"
                                       target="_blank">
                                       <i class="far fa-file-pdf"
                                             style="font-size:16px; margin-right:4px;"></i> <span
                                             style="line-height:1;">Bill</span>
                                    </a></td>
                           <td><?php echo $intusr['status'];?></td>
                           <td><?php echo date('d-m-Y ', strtotime($intusr['approved_date']));?></td>
   
                        </tr>
                        <?php $counter++; }  }?>
                        
                     </tbody>
                  </table>

              
                  