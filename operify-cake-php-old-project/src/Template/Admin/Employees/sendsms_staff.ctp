<div class="modal-header">
	
	<button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
	
	<h4 class="modal-title">
	<i class="fa fa-info-circle" aria-hidden="true"></i> Send SMS (Pending SMS : <?php echo $balance[0]['msg_count']; ?>)
	</h4>

</div>


<div class="modal-body">

<div class="box-body">  

		
  <script>



$('.sendtest').on('click',function(){
 $('.sucessmessge').html('');
var ssid = $('.lkoshjj').val();
var id = $('.catnid').val();
var messageid = $('.tmessage').text();


 $.ajax({ 
        type: 'POST', 
        url: '<?php echo ADMIN_URL ;?>Students/send_smsmobile',
        data: {'sid':ssid,'id':id,'messageid':messageid}, 
        async: true,
        success: function(data){ 
		
			 $('.sucessmessge').html(data);
			 

 //return true;
        }, 
        
    });  
});
</script>

<script>

$('.comp').change(function(){
var id =$(this).children("option:selected").val();

 $('.tmessage').html('');


 $.ajax({ 
        type: 'POST', 
        url: '<?php echo ADMIN_URL ;?>Students/find_smstemplate',
        data: {'id':id}, 
         async: true,
        success: function(data){  

$('.tmessage').html('');
  $('.tmessage').text(data);
   $('.catnid').val(id);
  $('.invjss').show();
 //  return true;
        }, 
        
    });  
});

</script>
			
			  <div class="form-group">
		          <label for="inputEmail3" class="col-sm-4 control-label">Select SMS Category :</label>
                   <div class="col-sm-8">
		<?php echo $this->Form->input('category',array('class'=>'form-control comp','id'=>'catp','type'=>'select','empty'=>'Select Category','required','options'=>$smscategoryslist,'label' =>false)); ?> 
	<input type="hidden" name="catn" class="catnid" >
		            </div>
		        </div>
		        	  <div class="form-group">
					</div>
		        <br><br><br>
		       <div class="form-group">
		          <label for="inputEmail3" class="col-sm-4 control-label">View Message :</label>
                   <div class="col-sm-8">
					   
					   <textarea name="message" class="tmessage" readonly="readonly" required="required"  id="tmessage" rows="4" cols="40" ></textarea>
		
		            </div>
		        </div>
		<br>
		       <div class="form-group">
		          <label for="inputEmail3" class="col-sm-4 control-label">Want To Send On Test Number ? :</label>
                   <div class="col-sm-3">
					   
					
					
					<input type="text" id="test3" name="sendtestnumber" value="" class="form-control lkoshjj" > 
					
					<a href="javascript:void(0);" class="sendtest">Send Test Now !!</a>
		
		            </div>
		            
		                <div class="col-sm-5 sucessmessge" >
							
							</div>
		        </div>
				
					
			

			</div>


</div>

<script type="text/javascript">
 
        $('.lkoshjj').on('blur',function(){
			
			$('.lkoshjj').val($(this).val());
           
           //  return true;
         
        });
        
        
        
        </script>
<script type="text/javascript">
 
        $('.invj').click(function(){
             $('.invjssjh').html("<b>Sending...........Please Wait !!!!</b>");
           //  return true;
           $(".invj").attr('readonly','readonly');
        });
        
        
        
        </script>
<div class="modal-footer invjss"  style="display:none;">
	<span class="invjssjh" style="color:green;"></span>
	<?  echo $this->Form->submit(
            'Send', 
            array('class' => 'btn btn-info pull-right invj', 'title' => 'Send To All')
        ); ?>
	<button data-dismiss="modal" class="btn btn-default pull-left inhj" type="button">Cancel</button>   
	
</div>


