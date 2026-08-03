<table id="" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                          <!-- <th>Group Id</th> -->
                          <th>Sr.No</th>

                          <th>Name</th>
                          <th>View</th>
                          <th>Action</th>
                        </tr>
                    </thead>
                    <?php $page = $this->request->params['paging']['']['page'];
                  $limit = $this->request->params['paging']['']['perPage'];
                  $counter = ($page * $limit) - $limit + 1;
                  if(isset($user) && !empty($user)){ 
                    foreach($user as $intusr){ //pr($intusr);
                      ?>
                      <tr>
                      <td><?php echo $counter;?></td>                        

                      <!-- <td><?php //echo $intusr['id'];?></td>                         -->
                        <td> <?php echo ucfirst($intusr['category_name']); ?></td>
                   
                     


                    
                          <td> <strong><?php
                         
                            echo $this->Html->link('', [
                                'action' => 'edit',
                                $intusr->id,
                            ], ['class' => 'glyphicon glyphicon-edit', 'style' => 'font-size: 21px;']);

                          ?>
                          </td>
                          <td>
                             &nbsp;<?php
                            echo $this->Html->link('', [
                              'action' => 'delete',
                              $intusr->id
                            ],['class'=> 'glyphicon glyphicon-remove','style'=>'font-size: 21px;'	
                ,"onClick"=>"javascript: return confirm('Are you sure do you want to delete this Item')"]); ?>

                
                          </strong></td>
                          
                        
                        </tr>
                        <?php $counter++; } }else{ ?>


                        <?php } ?>  
      </tbody>
                  </table>