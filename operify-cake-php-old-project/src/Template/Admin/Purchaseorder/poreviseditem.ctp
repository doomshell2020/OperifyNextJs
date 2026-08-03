<?php $i=1;
     foreach ($stockitems as $key => $value) {
     //pr($value); die;    
 
		   $tquant += $value['quantity'];
      $tamount += $value['amount'];
      $tax_find = $value['additem']['taxmaster']['tax'];
      $tax_key = $value['additem']['taxmaster']['id'];

       $lprcost = $this->Comman->lprcost($value['item_id']);
       if($lprcost==""){
		 $lprcost=0;   
	   }
     
      $unitname = $this->Comman->getunitnamepoview($value['additem']['unit_id']); ?>
   <tr class="video_details">    


      <td><?php echo $this->Form->input('indent_id[]', array('class' => 'form-control','id'=>'indentid','type'=>'hidden','value'=>$value['indent_id'],'label'=>false)); ?><?php echo $value['indent_id']; ?></td>

      <td>
      <?php echo $this->Form->input('pitemname[]', array('class' => 'form-control','id'=>'pitemname','type'=>'hidden','value'=>$value['item_id'],'label'=>false,'autofocus','autocomplete'=>'off')); ?>

      <?php $getsize=$this->Comman->getsizename($value['additem']['size_id']);
      if($value['additem']['size_id']==6){
		echo $this->Form->input('name[]', array('class' => 'form-control','id'=>'pitemname','type'=>'text','value'=>$value['additem']['item_name'],'label'=>false,'autofocus','autocomplete'=>'off','readonly'));  
		  
	  }else{ 
       echo $this->Form->input('name[]', array('class' => 'form-control','id'=>'pitemname','type'=>'text','value'=>$value['additem']['item_name']."-".$getsize['size_name'],'label'=>false,'autofocus','autocomplete'=>'off','readonly')); } ?>   
      </td>
      
    

      <td><input type="text" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" name="pitemquantity[]" class="form-control newquan quntt<?php echo $i; ?>" id="quan"  max="<?php echo $value['quantity']; ?>" min="0"  value="<?php echo $value['quantity']; ?>"></td>

      <td><input type="text"  name="pitemrate[]" class="form-control filterme newpitra pitra<?php echo $i; ?>" id="pitemrate" value="<?php echo $value['rate']; ?>" ><small style="font-weight:bold;position: relative;">LPR: <span style="color:red;">₹</span>&nbsp;&nbsp;<?php echo $lprcost; ?></small></td>



      <td>
		  <input type="text" name="pitemamount[]" class="form-control newtamo pitama<?php echo $i; ?>" id="pitemamount" value="<?php echo $value['cost_price']; ?>" readonly></td> 

      <td>
      <!-- <select name="tax_id[]" class="form-control taxamount<?php //echo $i; ?>">
      <option value="">Select</option>
        <?php //foreach ($tax as $key => $valaue) { ?>
          <option <?php// if($value['tax_id']==$key){ ?> selected <?php //} ?> value="<?php //echo $key; ?>"><?php //echo //$valaue; ?></option>
        <?php //} ?>
      </select> -->
      <input type="text" name="tax_value[]" class="form-control taxamount<?php echo $i; ?>" value="<?php echo $tax_find;  ?>" readonly>
      <input type="hidden" name="tax_id[]" class="form-control taxamount<?php echo $i; ?>" value="<?php echo $tax_key;  ?>" readonly>

      </td>
       <td >
		
		<span class="totaltax totaltax<?php echo $i; ?>"><?php echo $value['tax']; ?></span>
    <input type="hidden" name="pitemtax[]" class="form-control newtaxx pitax<?php echo $i; ?>" id="pitax" value="<?php echo $value['tax']; ?>" readonly>
    </td> 
    <td >
		 <input type="hidden" name="totalamount[]" class="form-control totalamount<?php echo $i; ?>" id="totalamount" value="<?php echo $value['amount']; ?>">
		<span class="newtamso pitamas<?php echo $i; ?>"><?php echo $value['amount']; ?></span>
 
    </td> 
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
  $('.pitra<?php echo $i; ?>').on('change',function() {

    var pitra = $(this).val();
    var quat = $('.quntt<?php echo $i; ?>').val();
    var tcrate = parseFloat(pitra) * parseInt(quat);
   
    var totalget = $('.totala').text();

$('.pitama<?php echo $i; ?>').val(tcrate);
$('.pitamas<?php echo $i; ?>').text(tcrate);
$('.totalamount<?php echo $i; ?>').val(tcrate);


 $('.pitax<?php echo $i; ?>').val(0);
 $('.totaltax<?php echo $i; ?>').text(0);
$(".taxamount<?php echo $i; ?>").trigger('change');
    total();
    total2();
     total3();
    
  });

 
  $('.quntt<?php echo $i; ?>').on('change',function() {
	  
	   var max = parseInt($(this).attr('max'));
	
   var min = parseInt($(this).attr('min'));
 
  //  if ($(this).val() > max)
  //  {
	//     alert("Enter Quantity doesn't allow!!");
  //     $(this).val(max);
  //  }
  //  else if ($(this).val() < max)
  //  {
	   
  //     $(this).val(min);
  //  }    
   
   
    var quat = $(this).val();
     $('.pitax<?php echo $i; ?>').val(0);
//$('.pitra<?php echo $i; ?>').val(0);
$('.totaltax<?php echo $i; ?>').text(0);
$('.pitama<?php echo $i; ?>').val(0);
$('.pitamas<?php echo $i; ?>').text(0);
$('.totalamount<?php echo $i; ?>').val(0);
      var pitra = $('.pitra<?php echo $i; ?>').val();
    var tcrate = parseFloat(pitra) * parseInt(quat);
   
    var totalget = $('.totala').text();

$('.pitama<?php echo $i; ?>').val(tcrate);
$('.pitamas<?php echo $i; ?>').text(tcrate);
$('.totalamount<?php echo $i; ?>').val(tcrate);

// $(".taxamount<?php //echo $i; ?>").on('change',function() {
    // var taxa = $(this).val();
    var taxa = $('.taxamount<?php echo $i; ?>').val();
  
    var pitra = $('.pitra<?php echo $i; ?>').val();
    var quat = $('.quntt<?php echo $i; ?>').val();
    var tcrate = parseFloat(pitra) * parseInt(quat);
   
    var tocost = $('.pitama<?php echo $i; ?>').val();
    var toamount = $('.totala').text();
    var toamountnew;
    var withtax;

    $.ajax({ 
        type: 'POST', 
        url: '<?php echo ADMIN_URL; ?>purchaseorder/gettax',
        data: {'fetch':taxa},
        success: function(data){  
            var totalget = $('.totala').text();
          withtax = data * (tcrate/100);
             withtax = withtax.toFixed(2);
        toamountnew = parseFloat(tocost) + parseFloat(withtax);
          $('.pitamas<?php echo $i; ?>').text(toamountnew);
          $('.totalamount<?php echo $i; ?>').val(toamountnew);
           $('.pitax<?php echo $i; ?>').val(withtax);
           $('.totaltax<?php echo $i; ?>').text(withtax);
      total();
      total2();
        },    
      });  
       
  // });






$(".taxamount<?php echo $i; ?>").trigger('change');

    total();
    total2();
     total3();
    
  });


});
</script>

<?php $i++; } ?>


<script>
 function total(){
	  var totals=0; 
	 
	 
            var $dataRows=$("#customers tr:not('.totalColumn, .titlerow, .titlerows')");

            $dataRows.each(function() {
                $(this).find('.newtamso').each(function(i){        
                    totals+=parseFloat( $(this).html());
                });
            });
          
            if($('#freight').val()!=''){
                    totals+=parseFloat( $('#freight').val());
 }

                $('.totala').html(totals);
           
 }

 function total2(){
	  var totals2=0; 
	 
	 
            var $dataRows=$("#customers tr:not('.totalColumn, .titlerow, .titlerows')");

            $dataRows.each(function() {
                $(this).find('.newtaxx').each(function(i){        
                    totals2+=parseFloat( $(this).val());
                });
            });
          
                $('.totala2').html(totals2);
           
 }
 function total3(){
	  var totals3=0; 
	 
	 
            var $dataRows=$("#customers tr:not('.totalColumn, .titlerow, .titlerows')");

            $dataRows.each(function() {
                $(this).find('.newtamo').each(function(i){        
                    totals3+=parseFloat( $(this).val());
                });
            });
          
                $('.totala1').html(totals3);
           
 }


</script>
