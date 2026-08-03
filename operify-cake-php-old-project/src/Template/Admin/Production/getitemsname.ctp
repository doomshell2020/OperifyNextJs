<?php $i = $itemname['id'];?>

 <tr id="row-<?php echo $i; ?>">
    <td width = "45%">
    <input type="hidden" name="finished_pro_itemid[]"  value ="<?php echo $i?>">
     <?php echo $itemname['item_name']?>
    </td>
    <td width = "20%">
      <input type="text" name="quantity1[]"  autocomplete="off" required="required" class='numbe' onkeypress = "return isNumberKey(event)" id="qutoy<?php echo $i?>" >
      <?php echo (" ".$unitname['unit_name'])?>
    </td>
    <td width = "20%">
      <input type="text" name="price1[]" required="required" id="qutoy1<?php echo $i?>" style="text-align:end;" autocomplete="off" class='numbe' onkeypress = "return isNumberKey(event)" >
    </td width = "15%">
    <td>
    <span class="fas fa-trash-alt delete-button" data-id="<?php echo $i; ?>" title ="delete" style="font-size: 21px; color:#cd0404" onclick="deleteRow(<?php echo $i; ?>)"></span>
  </td>
</tr>

<script>
  $(document).ready(function() {
    $("#qutoy<?php echo $i?>").val(1); 
    $("#qutoy1<?php echo $i?>").val(0);
  });
</script>