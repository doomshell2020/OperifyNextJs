<?php //pr($users); ?>

<div id="printableArea">
<div class="modal-header" style="padding: 0px;">
    <h4 style="text-align:center"><b><?php echo $users['0']['companymaster']['name']; ?> </b></h4>
    <button type="button" class="close" data-dismiss="modal" style="margin-top: -75px;">&times;</button>
</div>
    <h4 style="text-align:center">Store Item Indent</h4>
      <div class="modal-body">

        <div class="messages" id="form-messages">
            <div class="row">

                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Indent Id : <?php echo $users['0']['indent_id']; ?></th>
                            <th></th>     
                            <th>Status : Approved</th>     
                            <th></th>     
                            <th>Date : <?php echo date("d/m/Y", strtotime($users['0']['added_time']));?></th>
                        </tr>
                    </thead>
                </table>
                
                <table id="example1" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>S. No.</th>
                    <th>Category</th>     
                    <th>Item (Company Name)</th>     
                    <th>Rate</th>     
                    <th>Quantity</th>     
                    <th>Units</th>     
                    <th>Amount</th>
                  </tr>
                </thead>

                <tbody>
                  <?php $page = $this->request->params['paging']['']['page'];
                  $limit = $this->request->params['paging']['']['perPage'];
                  $counter = ($page * $limit) - $limit + 1;
                  if(isset($users) && !empty($users)){ 
                    foreach($users as $intusr){ //pr($intusr);
                      ?>
                      <tr>
                        <td><?php echo $counter;?></td>  
                        <td><?php echo $intusr['itemcategory']['category_name']; ?></td>                        
                        <td><?php echo $intusr['itemname']['item_name']." (".$intusr['companymaster']['name'].")"; ?></td> 
                        <td><?php echo $intusr['rate']; ?></td> 
                        <td><?php echo $intusr['quantity']; ?></td> 
                        <td><?php echo $intusr['measurementunit']['unit_name']; ?></td> 
                        <td><?php echo $intusr['amount']; ?></td>                      
                    </tr>
                    <?php 
                    $quan += $intusr['quantity'];
                    $amou += $intusr['amount'];
                    
                    ?>
                    
                        <?php $counter++; } }else{ ?>


                        <?php } ?>  
                    <tr>
                        <td></td>  
                        <td></td>                        
                        <td></td> 
                        <td><strong> Total Quantity </strong></td> 
                        <td><strong><?php echo $quan; ?></strong></td> 
                        <td><strong>Total Amount</strong></td> 
                        <td><strong><?php echo $amou; ?></strong></td>                      
                    </tr>
                        </tbody>
                            
                        </table>
                        

                                <input class="pull-right" type="button" onclick="printDiv('printableArea')" value="print!" />
                
            </div>
            
        </div>
    </div>
    </div>
    
    <script>
    function printDiv(divName) {
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
    </script>








