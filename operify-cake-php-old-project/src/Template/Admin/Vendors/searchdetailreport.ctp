<table id="" class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <td><a id="" style="position: absolute;top: 122px;right: 46px;"
                                class="btn btn-info btn-sm pull-right" target="_blank"
                                href="<?php echo ADMIN_URL ;?>vendors/exportstorereport"><i
                                    class="fa fa-file-excel-o" aria-hidden="true"></i> Export Excel</a></td>
                    </tr>
                   <?php  $datefrom=$datefrom;
                   $dateto=$dateto;

                    // session store datefrom
                   $datefromh=$datefrom;
                   $session = $this->request->session();    
                   $session->delete($datefromh); 
                   $session->write('datefrom',$datefromh); 


                   $session->delete($dateto); 
                   $session->write('dateto',$dateto);

                   function getDatesFromRange($start, $end, $format = 'Y-m-d') {
      
                    // Declare an empty array
                    $array = array();
                      
                    // Variable that store the date interval
                    // of period 1 day
                    $interval = new DateInterval('P1D');
                  
                    $realEnd = new DateTime($end);
                    $realEnd->add($interval);
                  
                    $period = new DatePeriod(new DateTime($start), $interval, $realEnd);
                  
                    // Use loop to store date into array
                    foreach($period as $date) {                 
                        $array[] = $date->format($format); 
                    }
                  
                    // Return the array elements
                    return $array;
                }
                  
                   $select_date = getDatesFromRange(date('Y-m-d',strtotime($datefrom)),date('Y-m-d',strtotime($dateto)));

                 ?>
                    <tr>
                        <td><b> Receipt Insert Date <?php  echo "( From ".date('d-m-Y',strtotime($datefrom))." To ".date('d-m-Y',strtotime($dateto))." )"; ?> </b>
                        </td>
                        <td><b>CASH</b></td>
                        <td><b>CHEQUE</b></td>
                        <td><b>Online</b></td>
                        <td><b>Total</b></td>
                        <!-- <td><b>Action</b></td> -->
                    </tr>               
                    
<?php 
foreach($select_date as $key=>$value){ 
    
   $cash_store_count =  $this->comman->cashstore($value);
 
   $cheque_store_count = $this->comman->chequestore($value);
   $online_store_count = $this->comman->onlinestore($value);
   $cash_store_total += $cash_store_count['sum'];
   $cheque_store_total += $cheque_store_count['sum'];
   $online_store_total += $online_store_count['sum'];

  $total_count =  $cash_store_count['sum'] + $cheque_store_count['sum'] + $online_store_count['sum'];
  $final_count += $total_count;
    ?>                                                                                                                            
                   <tr>
<td> <?php echo date('d-m-Y',strtotime($value)); ?> </td>
<td> ₹<?php  if($cash_store_count['sum']){ echo $cash_store_count['sum']; }else{ echo "0"; } ?> </td>
<td> ₹<?php  if($cheque_store_count['sum']){ echo $cheque_store_count['sum']; }else{ echo "0"; } ?></td>
<td> ₹<?php  if($online_store_count['sum']){ echo $online_store_count['sum']; }else{ echo "0"; } ?> </td>
<td> ₹<?php echo $total_count; ?> </td>
                      
                    </tr>

              


                    <?php } ?>
                    <tr>
<td> <b>Net Received </b> </td>
<td><b> ₹<?php echo $cash_store_total;  ?> </b> </td>
<td> <b> ₹<?php echo $cheque_store_total;  ?> </b> </td>
<td> <b> ₹<?php echo $online_store_total;  ?></b> </td>
<td><b> ₹<?php echo $final_count; ?></b> </td>
                      
                    </tr>
                </tbody>
            </table>