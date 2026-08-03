<?php //pr($purchaseorderdetail);  ?>


    <tr class="video_details">    
      <td width="5%" class="cou"><?php echo $srno; ?></td>
      <td width="30%"><?php echo $this->Form->input('name[]', array('class' => 'form-control','id'=>'pitemname','type'=>'select','options'=>$itemname,'default'=>$purchaseorderdetail['item_id'],'empty'=>'Select Item','label'=>false,'autofocus','autocomplete'=>'off','disabled'=>'disabled')); ?>
      
      <?php echo $this->Form->input('pitemname[]', array('class' => 'form-control','id'=>'pitemname','type'=>'hidden','value'=>$purchaseorderdetail['item_id'],'empty'=>'Select Item','label'=>false,'autofocus','autocomplete'=>'off')); ?>
      </td>

      <td width="15%"><?php echo $this->Form->input('pitemrate[]', array('class' => 'form-control','id'=>'pitemrate','type'=>'text','value'=>$purchaseorderdetail['cost_price'],'label'=>false,'autofocus','autocomplete'=>'off','readonly')); ?></td>

      <td width="15%"><?php echo $this->Form->input('pitemquantity[]', array('class' => 'form-control','id'=>'quan','type'=>'text','value'=>$purchaseorderdetail['quantity'],'label'=>false,'autofocus','autocomplete'=>'off','readonly')); ?></td>

      <td width="15%"><?php echo $this->Form->input('pitemunit[]', array('class' => 'form-control','id'=>'pitemunit','type'=>'text','value'=>$purchaseorderdetail['unit_name'],'readonly','label'=>false,'autofocus','autocomplete'=>'off','readonly','readonly')); ?></td>

      <td width="15%"><?php echo $this->Form->input('pitemamount[]', array('class' => 'form-control','id'=>'pitemamount','type'=>'text','value'=>$purchaseorderdetail['amount'],'label'=>false,'autofocus','autocomplete'=>'off','readonly')); ?></td>  

      <td width="5%"><a href="javascript:void(0)" style="font-size: 15px;" data-amount="<?php echo $purchaseorderdetail['amount']; ?>" data-val="<?php echo $purchaseorderdetail['quantity']; ?>" data="<?php echo $purchaseorderdetail['id']; ?>" class="label label-danger remove"><i class="fa fa-times" aria-hidden="true"></i></a></td>
    </tr>

<script type="text/javascript">
$(document).ready(function() {
  // Check Total Quantity
    var toatalquant = $('.totalq').text();
    var tQuant = '<?php echo $purchaseorderdetail['quantity']; ?>';    
    if(toatalquant != ""){
      tQuant = parseInt(toatalquant) + parseInt(tQuant);
    }    
    $('.totalq').text(tQuant); 

    //Check total amount
    var toatalamount = $('.totala').text();
    //alert(toatalamount);
    var tAmou = '<?php echo $purchaseorderdetail['amount']; ?>';    
    //alert(tAmou);
    if(toatalamount != ""){
      tAmou = parseFloat(toatalamount) + parseFloat(tAmou);
    }    
    $('.totala').text(tAmou);
});
</script>



