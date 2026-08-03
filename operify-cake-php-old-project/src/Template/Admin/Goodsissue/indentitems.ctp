 <?php $i=1;
     foreach ($indent as $key => $value) { 
      
       $tquant += $value['quantity'];
      $tamount += $value['amount'];

       $lprcost = $this->Comman->lprcost($value['item_id']);
    
       if($lprcost==""){
		 $lprcost=0;   
	   }
         //  $getpurchaseorderstatus = $this->Comman->getpurchaseorderstatus($value['item_id'],$value['indent_id']);

    ?>    
    <tr class="video_details">    


      <td width="6%"><?php echo $this->Form->input('indent_id[]', array('class' => 'form-control','id'=>'indentid','type'=>'hidden','value'=>$value['indent_id'],'label'=>false)); ?><?php echo $value['indent_id']; ?></td>

      <td width="20%">
      <?php echo $this->Form->input('pitemname[]', array('class' => 'form-control','id'=>'pitemname','type'=>'hidden','value'=>$value['item_id'],'label'=>false,'autofocus','autocomplete'=>'off')); ?>

      <?php $getsize=$this->Comman->getsizename($value['additem']['size_id']); if($value['additem']['size_id']==6){
		  
		  echo $this->Form->input('name[]', array('class' => 'form-control','id'=>'pitemname','type'=>'text','value'=>$value['additem']['item_name'],'label'=>false,'autofocus','autocomplete'=>'off','readonly'));
		  
		  }else{ echo $this->Form->input('name[]', array('class' => 'form-control','id'=>'pitemname','type'=>'text','value'=>$value['additem']['item_name']."-".$getsize['size_name'],'label'=>false,'autofocus','autocomplete'=>'off','readonly')); } ?> 
		  
      </td>
      
      
       
      <!-- <td width="18%"><?php// $getunit=$this->Comman->getunitnamepoview($value['additem']['unit_id']); echo $this->Form->input('pitemunit[]', array('class' => 'form-control','id'=>'pitemunit','type'=>'text','value'=>$getunit['unit_name'],'readonly','label'=>false,'autofocus','autocomplete'=>'off','readonly')); ?></td> -->

      <td width="26%">
        
      <input type="text" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" name="pitemquantity[]" class="form-control newquan quntt<?php echo $i; ?>" max="<?php echo $value['quantity']-$value['return_qty']; ?>" readonly="readonly" min="0" id="quan"  value="<?php echo $value['quantity']-$value['return_qty']; ?>"></td>

  <td width="8%">
      <?php 
      $totalrecivied=$this->Comman->totalstockregisteropeningrecivied($value['item_id']); $totaldispatched=$this->Comman->totalstockregisteropeningdispatched($value['item_id']);
     
      $remain=$totalrecivied[0]['quantity']-$totaldispatched[0]['quantity']; 
      ?>
<input type="text" readonly="readonly" name="remain[]" class="form-control" id="quntts<?php echo $i; ?>" value="<?php echo $remain; ?>">

  </td>

      <td width="12%"><input type="text" maxlength="10"  name="pitemrate[]" class="form-control filterme newpitra"  max="<?php echo $remain; ?>" min="0" id="pitra<?php echo $i; ?>" value="0"></td>

    </tr>
<script>
$(document).ready(function() {
	
	$('.filterme').keypress(function(eve) {
  if ((eve.which != 46 || $(this).val().indexOf('.') != -1) && (eve.which < 48 || eve.which > 57) || (eve.which == 46 && $(this).caret().start == 0) ) {
    eve.preventDefault();
  }
     
// this part is when left part of number is deleted and leaves a . in the leftmost position. For example, 33.25, then 33 is deleted
 $('.filterme').keyup(function(eve) {
  if($(this).val().indexOf('.') == 0) {    $(this).val($(this).val().substring(1));
  }
 });
});
	
 
  $('#pitra<?php echo $i; ?>').on('change',function() {
    var pitra = 0;
    var quat = 0;
     pitra = parseInt($(this).val());
     quat = parseInt($('#quntts<?php echo $i; ?>').val());
  
    if (quat < pitra)
   { 
     alert("Entered Quantity not greater than Stored Quantity.");
      $(this).val(0);
   }

   total();
   
  });

 
});
</script>

<?php $i++; } ?>


<script>
 function total(){
	  var totals=0; 
	 
	 
            var $dataRows=$("#customers tr:not('.totalColumn, .titlerow')");

            $dataRows.each(function() {
                $(this).find('.newpitra').each(function(i){        
                    totals+=parseFloat( $(this).val());
                });
            });
         
                $('.totala').html(totals);
                $('.tqty').val(totals);  
 }

 
</script>
