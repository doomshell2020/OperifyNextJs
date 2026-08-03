<?php //pr($users); ?>

<div class="modal-header" style="padding: 0px;">
  <h3 style="text-align:center"><b>View Details </b></h3>
        <button type="button" class="close" data-dismiss="modal" style="margin-top: -75px;">&times;</button>
      </div>
     
      <div class="modal-body">           

        <div class="messages" id="form-messages">
            <div class="row">
                <div class="col-sm-3 col-md-3">
                    <img src="<?php if($users['item_image'] != ""){ echo SITE_URL . "itemimage/".$users['item_image']; }else{ echo SITE_URL . "itemimage/noimage.png"; } ?>" width="100px" alt="">
                </div>

                <div class="col-sm-9 col-md-9">
                    
                        <ul class="list-unstyled">
                            <li style="width:59%; border:1px black solid; padding: 5px 10px; display:inline-block">Category-Sub cat : <?php echo $users['Maincategory']['category_name']." (".$users['Subcategory']['category_name'].")"; ?> </li>

                            <li style="width:40%; border:1px black solid; padding: 5px 10px; display:inline-block">Cost Price : <?php echo $users['cost_price']; ?> </li>


                            <li style="width:59%; border:1px black solid; padding: 5px 10px; display:inline-block">Unit Price : <?php echo "0.00"; ?> </li>

                            <li style="width:40%; border:1px black solid; padding: 5px 10px; display:inline-block">Sale Price : <?php echo $users['sale_price']; ?> </li>

                            <li style="width:59%; border:1px black solid; padding: 5px 10px; display:inline-block">Total Item : <?php echo $users['quantity']; ?> </li>

                            <li style="width:40%; border:1px black solid; padding: 5px 10px; display:inline-block">Available Item : <?php echo $users['quantity']; ?> </li>

                            <li style="width:59%; border:1px black solid; padding: 5px 10px; display:inline-block">Issue Item : <?php echo "0" ?> </li>

                            <li style="width:40%; border:1px black solid; padding: 5px 10px; display:inline-block">Item Code : <?php echo "101" ?> </li>

                            <li style="width:59%; border:1px black solid; padding: 5px 10px; display:inline-block">Available Status : <?php echo "Available"; ?> </li>
                        </ul>
                    
                </div>

                <div class="col-sm-10 col-md-10">
                    <img src="" alt="">
                </div>                  
            </div>
        </div>    
    </div>
  



	



