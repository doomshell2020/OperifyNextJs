<?php $i = $itemname['id'];
$lprcost = $this->Comman->lprcost($itemname['id']);
?>

 <tr>
 <td>Main operation</td>
 <td>
    <input type="hidden" name="raw_materials_item_id[]"  value ="<?php echo $i?>">
      <input type="text" name="product_id2[]" readonly  value ="<?php echo $itemname['item_name']?>" >
    </td>
    <td>
    <input type="text" name="quantity2[]"  autocomplete="off" class="numbe" required="required" id="qutoy<?php echo $i?>"><?php echo (" ".$unitname['unit_name'])?>
    </td>
    <td>
      <input type="text" name="price2[]" required="required" id="qutoy1<?php echo $i?>" autocomplete="off" style="text-align:end;" class='numbe'><br>
      <small style="font-weight:bold;position: relative;">LPR: <span style="color:red;">₹</span>&nbsp;&nbsp;<?php echo sprintf('%.2f', $lprcost); ?></small>
    </td>
    <td>
    <span class="fas fa-trash-alt delete-button" data-id="<?php echo $i; ?>" style="font-size: 21px; color:#cd0404"></span>
  </td>
</tr>


<script>
    $(document).ready(function() {
    $("#qutoy<?php echo $i?>").val(1);
    $("#qutoy1<?php echo $i?>").val(0);

    $(".delete-button").on("click", function() {
    $(this).closest("tr").remove();
    });

    $('.numbe').keypress(function(eve) {
      if ((eve.which != 46 || $(this).val().indexOf('.') != -1) && (eve.which < 48 || eve.which > 57) || (eve.which == 46 && $(this).caret().start == 0)) {
        eve.preventDefault();
      }
      $('.numbe').keyup(function(eve) {
        if ($(this).val().indexOf('.') == 0) {
          $(this).val($(this).val().substring(1));
        }
      });
    });
    
   });
</script>