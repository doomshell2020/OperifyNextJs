<style>
/* #customers {
  font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
  border-collapse: collapse;
  width: 100%;
  margin-bottom:20px;
  }

#customers td, #customers th {
  border: 1px solid #ddd;
  padding: 8px;
}

#customers tr:nth-child(even){background-color: #f2f2f2;}

#customers tr:hover {background-color: #ddd; }

#customers th {
  padding-top: 12px;
  padding-bottom: 12px;
  text-align: left;
  background-color: #c8c8c8;
  color: #333333; 
} */

#testUL{
  position: relative;
}

#testUL ul{
  position: absolute;
  z-index: 999;
  top: 100%;
  left: 0px;
  right: 0px;  
  list-style-type: none;
  background-color: white;
  padding-left: 0px;
}

#testUL ul li{
  padding : 5px 8px;
  border: 1px solid lightgray;  
}

#testUL ul li a{
  color: black;

}
 

</style>
    
    <?php /*
    <tr class="video_details">    
      <td width="5%" class="cou"><?php echo $srno; ?></td>
      <td><?php echo $indentdetail['itemcategory']['category_name']; ?> </td>
      <td><?php echo $indentdetail['additem']['item_name']; ?></td>
      <td><?php echo $indentdetail['additem']['sale_price']; ?></td>
      <td><?php echo $indentdetail['quantity']; ?></td>
      <td><?php echo $indentdetail['measurementunit']['unit_name']; ?></td>
      <td>
      <?php foreach ($tax as $key => $value) {
        echo $value['tax_name']." : ".$value['tax']."% </br>";        
        $taxrate += $value['tax'];
      } 
      //echo $taxrate;
      $price = $indentdetail['additem']['sale_price'] * $indentdetail['quantity'];
      //  $totaltax = $price * $taxrate / 100;     
      //  $amount = $price + $totaltax;
      $amount = $price;
      ?>
      </td>
      <td><?php echo $amount; ?></td>
      <td><a href="javascript:void(0)" style="font-size: 15px;" data="<?php echo $indentdetail['id']; ?>" class="label label-primary remove">Remove</a></td>
    </tr>  <?php */ ?>
  <div class="form-group video_details">
    <div class="col-sm-3 autocomplete">
      <label for="inputEmail3">Items</label>
      <input type="hidden" name="item_id[]" id="retail_ids<?php echo $srno; ?>" value="<?php echo $indentdetail['additem']['id']; ?>">
      <?php // echo $this->Form->input('item', array('class' => 'form-control secrh-retail', 'id' => 'itemname', 'type' => 'text', 'value'=>$indentdetail['additem']['item_name'],'label' => false, 'autofocus', 'autocomplete' => 'off')); ?>
      <?php if(isset($indentdetail['sizemanager'])){ ?>
        <input type="text" name="item[]" value="<?php if($indentdetail['sizemanager']['id']!=6){  echo $indentdetail['additem']['item_name']." (".$indentdetail['sizemanager']['size_name'].")";    }else{ echo $indentdetail['additem']['item_name'];  }?>" class="form-control secrh-retail<?php echo $srno; ?>" id="itemname" autocomplete="off">
      <?php } else{ ?>
        <input type="text" name="item[]" value="<?php echo $indentdetail['additem']['item_name']; ?>" class="form-control secrh-retail<?php echo $srno; ?>" id="itemname" autocomplete="off">
      <?php } ?>
      
      <div id="testUL<?php echo $srno; ?>" style="position: relative;"><ul style="position: absolute; z-index: 999; top: 100%; left: 0px; right: 0px; list-style-type: none; background-color: white; padding-left: 0px;"></ul></div>
    </div>   

      <input type="hidden" name="size[]" id="size<?php echo $srno; ?>" value="<?php echo $indentdetail['sizemanager']['id']; ?>">

    <?php /* ?>    

    <div class="col-sm-2">
      <label for="inputEmail3">Size </label>
      <?php echo $this->Form->input('size[]', array('class' => 'form-control', 'id' => 'size', 'type' => 'select', 'options' => $size, 'label' => false, 'default'=>$indentdetail['sizemanager']['id'],'empty' => 'Select Size', 'autofocus', 'autocomplete' => 'off')); ?>
    </div> 

    <div class="col-sm-2">
    <label for="inputEmail3">Unit </label>
      <?php echo $this->Form->input('unitn[]', array('class' => 'form-control', 'id' => 'unit', 'type' => 'select', 'options' => $units, 'label' => false, 'default'=>$indentdetail['measurementunit']['id'],'empty' => 'Select Unit', 'autofocus', 'autocomplete' => 'off')); ?>
    </div>    
    
    <?php */ ?>

    
    <div class="col-sm-2">
    <label for="inputEmail3">Quantity</label>
      <?php //echo $this->Form->input('quant[]', array('class' => 'form-control', 'id' => 'quantity', 'type' => 'text', 'label' => false,'value'=>$indentdetail['quantity'], 'placeholder' => 'Quantity', 'autofocus', 'autocomplete' => 'off')); ?>
    <input name="quant[]" type="text" value="<?php echo $indentdetail['quantity']; ?>" class="form-control" id="quantity" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" placeholder="Quantity" autocomplete="off">

    </div>   

    <!-- <div class="col-sm-2">
    <label for="inputEmail3">Unit</label>
    <?php //$var = $this->Comman->getunitnamepoview($indentdetail['additem']['unit_id']); ?>
      <input type="text" name="unitname" class="form-control" id="unitna<?php //echo $srno; ?>" value="<?php //echo $var['unit_name']; ?>" readonly>
    </div> -->

    <a href="javascript:void(0)" style="font-size:30px; margin-top: 20px; display: inline-block; color:#e30000" data="<?php echo $indentdetail['id']; ?>" class="remove"><i class="fa fa-minus-circle" aria-hidden="true"></i></a>
  </div>  
  
</div>  

<script>
  function cllbckretail<?php echo $srno; ?>(id,cid,sid) { 
    $('.secrh-retail<?php echo $srno; ?>').val(id);
    $('#retail_ids<?php echo $srno; ?>').val(cid);
    $('#size<?php echo $srno; ?>').val(sid);
    $('#testUL<?php echo $srno; ?>' ).hide();
    $.ajax({ 
      type: 'POST', 
      url: '<?php echo ADMIN_URL; ?>indent/getitemdetail',
      data: {'fetch':cid},
      success: function(data){  
        console.log(data);
        //alert(data);
        $('#unitna<?php echo $srno; ?>').val(data);
      },    
    }); 
  }

 $( function() {
    $('.secrh-retail<?php echo $srno; ?>').bind('keyup',function(){  
    var pos=$(this).val();
    var check = '<?php echo $srno; ?>';
    //var catid=$('#subcategory').val();
    //alert(pos);
    $( '#testUL<?php echo $srno; ?>' ).show();
    $( '#retail_ids<?php echo $srno; ?>' ).val('');
    var count=pos.length;
    if(count > 0)
    {
    $.ajax({ 
      type: 'POST', 
      url: '<?php echo ADMIN_URL; ?>indent/getitemname',
      data: {'fetch':pos,'check':check},
      success: function(data){  
        console.log(data);
        $('#testUL<?php echo $srno; ?> ul').html(data);
      },    
    }); 
    }else{
    $( '#testUL<?php echo $srno; ?>' ).hide();  
    }   
    });     
  });
</script>
