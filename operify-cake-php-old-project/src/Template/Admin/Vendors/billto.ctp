<div class="all_vendorsdetails"><hr><h4 style="font-weight:bold;">Bill To</h4>
<div class="form-group billto_video_details">
    <div class="col-sm-2 billtos<?php echo $srno; ?>">
      <label>State</label>
          <?php echo $this->Form->input('billtostate_id[]',array('class'=>'form-control state', 'id'=>'billto_state_ids', 'type'=>'select', 'options'=>$state, 'empty'=>'Select State', 'label' =>false,'required')); ?>
        </div>   

        <div class="col-sm-2 billtoc<?php echo $srno; ?>">
      <label>City</label>
          <?php echo $this->Form->input('billtocity_id[]',array('class'=>'form-control city','id'=>'billto_city_ids', 'type'=>'select', 'empty'=>'Select City', 'label' =>false,'required')); ?>

    
        </div>

      <div class="col-sm-2">
      <label>GST NO.</label>
        <?php echo $this->Form->input('billtogst_number[]', array('class' => 'form-control gst','type'=>'text','maxlength'=>'15','label'=>false,'placeholder'=>'GST No.','autofocus','autocomplete'=>'off')); ?>
      </div>

        <div class="col-sm-4">
        <label>Address</label>
        <?php echo $this->Form->textarea('billtoaddress[]', array('rows'=>'2', 'class'=>'form-control address','placeholder'=>'Address', 'label' =>false)); ?>
      </div>


      <div class="col-sm-2">
  <label class="control-lable">Same As Copy</label>  <br>        
    <input type="checkbox" name="copy"  id="sameascopy<?php echo $srno; ?>" value="1" label=false>

    
    <script type="text/javascript">
            
              $("#sameascopy<?php echo $srno; ?>").on('change',function() {
                if($(this).prop("checked") == true){
         
           var ss= $(this).closest('.all_vendorsdetails').find('.state option:selected').val();
       
           $(this).closest('.all_vendorsdetails').find('.shipstate').val(ss);
            
           var cs= $(this).closest('.all_vendorsdetails').find('.city option:selected').val();
           
           $(this).closest('.all_vendorsdetails').find('.shipcity').val(cs);

                  
           var gst= $(this).closest('.all_vendorsdetails').find('.gst').val();
           
           $(this).closest('.all_vendorsdetails').find('.shipgst').val(gst);


           var shipaddress= $(this).closest('.all_vendorsdetails').find('.address').val();
           
           $(this).closest('.all_vendorsdetails').find('.shipaddress').val(shipaddress);
        
            }
            else if($(this).prop("checked") == false){

              $(this).closest('.all_vendorsdetails').find('.shipstate option[value=""]').prop("selected",true);
              $(this).closest('.all_vendorsdetails').find('.shipcity option[value=""]').prop("selected",true);
              $(this).closest('.all_vendorsdetails').find('.shipgst').val('');
              $(this).closest('.all_vendorsdetails').find('.shipaddress').text('');
                
            }
             
               
              });
        
            </script>
  </div>
</div>

     


  <script>
  $(document).ready(function() {
  $(".billtos<?php echo $srno; ?> #billto_state_ids").on('change',function() {
    var id = $(this).val();
    $(".billtoc<?php echo $srno; ?> #billto_city_ids").find('option').remove();
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
          $('<option>').val("").text("Select City").appendTo($(".billtoc<?php echo $srno; ?> #billto_city_ids"));
          $.each(html, function(key, value) {        
            $('<option>').val(key).text(value).appendTo($(".billtoc<?php echo $srno; ?> #billto_city_ids"));
          });
        }
      });
    }
  });

}); 
  </script>
  
<h4 style="font-weight:bold;">Ship From</h4>
<div class="form-group shipto_video_details">
        <div class="col-sm-2 shiptos<?php echo $srno; ?>">
        <label>State</label>
           <?php echo $this->Form->input('shipfromstate_id[]',array('class'=>'form-control shipstate', 'id'=>'shipto_state_ids', 'type'=>'select', 'options'=>$state, 'empty'=>'Select State', 'label' =>false,'required')); ?>
         </div>   

        <div class="col-sm-2 shiptoc<?php echo $srno; ?>">
          <label>City</label>
           <?php echo $this->Form->input('shipfromcity_id[]',array('class'=>'form-control shipcity', 'id'=>'shipto_city_ids','type'=>'select',  'options'=>$city, 'empty'=>'Select City', 'label' =>false,'required')); ?>
        </div>  

      <div class="col-sm-2">
      <label>GST NO.</label>
        <?php echo $this->Form->input('shipfromgst_number[]', array('class' => 'form-control shipgst','type'=>'text','maxlength'=>'15','label'=>false,'placeholder'=>'GST No.','autofocus','autocomplete'=>'off')); ?>
      </div>

      <div class="col-sm-4">
        <label>Address</label>
        <?php echo $this->Form->textarea('shipfromaddress[]', array('rows'=>'2', 'class'=>'form-control shipaddress','placeholder'=>'Address', 'label' =>false)); ?>
      </div>

      <div class="col-sm-2">
        <a href="javascript:void(0)" style="font-size:30px; margin-top: 20px; display: inline-block; color:#e30000" class="billto_remove"><i class="fa fa-minus-circle" aria-hidden="true"></i></a>
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
  </script> </div></div>