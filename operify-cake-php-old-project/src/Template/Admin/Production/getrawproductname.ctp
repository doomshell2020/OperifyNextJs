<?php $i = $item['id'];
// pr($item);die;
// $name = $bomname['title'];
// pr($name);die;
?>


 <tr>

 <td></td>
 <td></td>
 <td><?php echo $bomname['title'] ?></td>
 <td>
  <?php foreach($item as $value){
    echo $value['additem']['item_name']; ?> <br> <?php
  } ?> 
 </td>
 <td><input type="text" name="" id=""></td>
 <td>
  <?php foreach($item as $value){
    echo $value['quantity']; ?> <br> <?php
  } ?> 
 </td>
 <td>
  <?php foreach($item as $value){
       echo $value['quantity']." ".$value['additem']['measurementunit']['unit_name'] ?> <br> <?php
  } ?> 
 </td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>

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