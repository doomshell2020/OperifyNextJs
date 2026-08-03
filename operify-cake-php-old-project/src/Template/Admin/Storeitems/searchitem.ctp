<table  class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>S.No.</th>
                    <th>Items Name</th>     
                    <th>MRP Price</th>                    
                    <th>Cost Price</th>                                     
                    <th>Sale Price</th>  
                    <th>Quantity</th>                  
                    <th>Min Stock Level</th>                    
                    <th>Max Stock Level</th> 
                    <th>HSN/ISBN Number</th> 
                    <th>Supplier Name</th> 
                    <th>Expiry Date</th> 
                    <th>Action</th>
                  </tr>
                </thead>

                <tbody>
                  <?php $page = $this->request->params['paging']['']['page'];
                  $limit = $this->request->params['paging']['']['perPage'];
                  $counter = ($page * $limit) - $limit + 1;
                  if(isset($store) && !empty($store)){ 
                    foreach($store as $intusr){ //pr($intusr);
                      ?>
                      <tr>
                        <td><?php echo $counter;?></td>                        
                        <td> <?php echo ucfirst($intusr['item_name']); ?></td>
                        <td> <?php echo $intusr['mrp_price']; ?></td>
                        <td> <?php echo $intusr['cost_price']; ?></td>
                        <td> <?php echo $intusr['sale_price']; ?></td>
                        <td> <?php echo $intusr['quantity']; ?></td>
                        <td> <?php echo $intusr['min_stock']; ?></td>
                        <td> <?php echo $intusr['max_stock']; ?></td>
                        <td> <?php echo $intusr['hsn_isbn']; ?></td>
                        <td> <?php echo $supplier[0]['name']; ?></td>
                        <td> <?php echo date('Y-m-d',strtotime($intusr['expiry_date']));?></td>

                          <td> <strong><?php
                         
                            echo $this->Html->link('', [
                                'action' => 'edit',
                                $intusr->id,
                            ], ['class' => 'glyphicon glyphicon-edit', 'style' => 'font-size: 21px;']);

                          ?>
                             &nbsp;<?php
                            echo $this->Html->link('', [
                              'action' => 'delete',
                              $intusr->id
                            ],['class'=> 'glyphicon glyphicon-remove','style'=>'font-size: 21px;'	
                ,"onClick"=>"javascript: return confirm('Are you sure do you want to delete this store item')"]); ?>



        <?php if($intusr['status']=='Y'){ 
                        echo $this->Html->link('', [
                          'action' => 'status',
                          $intusr->id,'Y'
                        ],['title'=>'Active','class'=>'fa fa-check-circle','style'=>'font-size: 21px !important; margin-left: 12px;     color: #36cb3c;']);
                        
                      }else{ 
                        echo $this->Html->link('', [
                          'action' => 'status',$intusr->id,'N'
                        ],['title'=>'Inactive','class'=>'fa fa-times-circle-o','style'=>'font-size: 21px !important; margin-left: 12px; color:#FF5722;']);
                        
                      } ?>
                
                          </strong></td>
                          
                        
                        </tr>
                        <?php $counter++; } }else{ ?>


                        <?php } ?>  
      </tbody>

    </table>