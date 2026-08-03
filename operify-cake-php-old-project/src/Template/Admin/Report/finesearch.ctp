<?php
  $session = $this->request->session();
  $role_id = $session->read('Auth.User.role_id');
?>


            
              <thead>
                <tr>
                  <th>#</th>
   
                  <th>Book Name</th>
                  <th>ASN No.</th>
                  <th>Holder Name</th>
                  <th>Holder Type</th>
                  <th>Fine Type</th>
                  <th>Fine Date</th>
                  <th>Amount</th>
                 
                </tr>
              </thead>
              <tbody>

                <?php 

                $page = $this->request->params['paging']['books']['page'];
                $limit = $this->request->params['paging']['books']['perPage'];
                $counter = ($page * $limit) - $limit + 1;

                if(isset($finede) && !empty($finede)){ 
                  foreach($finede as $work){
                     //pr($work);die;
                     $hol=explode('-',$work['holder_name']);
                     $asn=$work['asn_no'];
                     $bid= $this->Comman->findbookid($asn);
                     $biid= $bid['book_id'];
                     $bn= $this->Comman->findbookname($biid);
                     $bname=$bn['name'];
                    // pr($bn); die;
                     //pr($hol); die;

                    $d1 = $this->Time->format( $work['sub_date'], 'dd-MM-Y' );
                    //pr($d1); die;
                   

                    ?>
                    <tr>
                      <td><?php echo $counter;?></td>

                                           
                      <td><?php if(isset($bname)){ echo ucfirst($bname);}else{ echo 'N/A';}?></td>

                      <td><?php if(isset($work['asn_no'])){ echo ucfirst($work['asn_no']);}else{ echo 'N/A';}?></td>

                      <td><?php if(isset($work['holder_name'])){ echo ucfirst($work['holder_name']);}else{ echo 'N/A';}?></td>

                      <td><?php if(isset($work['holder_type_id'])){ echo ucfirst($work['holder_type_id']);}else{ echo 'N/A';}?></td>

                      <td><?php if(isset($work['fine_type'])){ echo ucfirst($work['fine_type']);}else{ echo 'N/A';}?></td>

                      <td><?php if( !empty( $d1 ) ) { echo $d1; } else { echo "N/A"; } ?></td>

                     <td><?php if(isset($work['amount'])){ echo ucfirst($work['amount']);}else{ echo 'N/A';}?></td>

                      


                    </tr>
                    <?php $am +=$work['amount']; $counter++;} ?>
<tr>
  <td colspan="8" ><b style="float: right;">Total</b></td>
  <td><span class="text-black">₹ </span><?php echo $am; ?></td>
</tr>


                 <?php    }else{?>
                    <tr>
                      <td>NO Data Available</td>
                    </tr>
                    <?php } ?>  
                  </tbody>

               
             

