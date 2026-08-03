<div class="form-group shipto_video_details">
        <div class="col-sm-2 shiptos<?php echo $srno; ?>">
        <label>State</label>
           <?php echo $this->Form->input('shipfromstate_id[]',array('class'=>'form-control', 'id'=>'shipto_state_ids', 'type'=>'select', 'options'=>$state, 'empty'=>'Select State', 'label' =>false,'required')); ?>
         </div>   

        <div class="col-sm-2 shiptoc<?php echo $srno; ?>">
          <label>City</label>
           <?php echo $this->Form->input('shipfromcity_id[]',array('class'=>'form-control', 'id'=>'shipto_city_ids','type'=>'select', 'empty'=>'Select City', 'label' =>false,'required')); ?>
        </div>  

      <div class="col-sm-2">
      <label>GST NO.</label>
        <?php echo $this->Form->input('shipfromgst_number[]', array('class' => 'form-control gst','type'=>'text','maxlength'=>'15','label'=>false,'placeholder'=>'GST No.','autofocus','autocomplete'=>'off')); ?>
      </div>

      <div class="col-sm-4">
        <label>Address</label>
        <?php echo $this->Form->textarea('shipfromaddress[]', array('rows'=>'2', 'class'=>'form-control','placeholder'=>'Address', 'label' =>false)); ?>
      </div>

      <div class="col-sm-2">
        <a href="javascript:void(0)" style="font-size:30px; margin-top: 20px; display: inline-block; color:#e30000" class="shipto_remove"><i class="fa fa-minus-circle" aria-hidden="true"></i></a>
      </div>
  </div>


  <script>
  $(document).ready(function() {
  $(".shiptos<?php echo $srno; ?> #shipto_state_ids").on('change',function() {
    var id = $(this).val();
    //alert(id);
    $(".shiptoc<?php echo $srno; ?> #shipto_city_ids").find('option').remove();
    //$("#city").find('option').remove();
    if (id) {
      var dataString =id;
      $.ajax({
        type: "POST",
        url: '<?php echo SITE_URL;?>/admin/vendors/getcity',
        data: {'dataString':id},
        cache: false,
        success: function(html) {
          //alert(html);
          $('<option>').val("").text("Select City").appendTo($(".shiptoc<?php echo $srno; ?> #shipto_city_ids"));
          $.each(html, function(key, value) {        
            $('<option>').val(key).text(value).appendTo($(".shiptoc<?php echo $srno; ?> #shipto_city_ids"));
          });
        }
      });
    }
  });

}); 
  </script>