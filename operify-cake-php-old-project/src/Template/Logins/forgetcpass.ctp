<script>
	function chk(x)
	{
	
		var pass =document.getElementById('pass').value;
		var cpass =document.getElementById('cpass').value;
                var len=$('#pass').val().length;
//alert(len);
if(pass != cpass)
{
	alert("password not match");
	document.getElementById('pass').value = '';
	document.getElementById('cpass').value = '';
	document.getElementById('pass').focus();
return false;
	}
if(len < 6)
{
	alert("Password Must be 6 character long");
return false;
	}

		}

	</script>


   


  
  
  
    
   </header>
  <div class="main mrgntp40">
    <div class="left-pannel">
  <?php //echo $this->element('memberarea-sidebar');
//echo $this->element('similar-sidebar');  ?>
    </div>
    <div class="right-pannel">
      <h1 class="title"><?php echo __('Reset Password'); ?></h1>
       <div class="mbr-area">
 <div class="usr-profile frgt-pass">
    <?php echo $this->Form->create('User',array('url'=>array('controller'=>'Users','action'=>'forget_cpass/kp/'.$usrid),'type'=>'file','inputDefaults'=>array('div'=>false,'label'=>false),'class'=>'stdform','onsubmit'=>'return chk(this);')); ?>
  

  <table cellpadding="0" cellspacing="5" border="0" width="75%">
 <tr>
  <?php if($ftyp==1) { ?>    

 <td> <label style="margin-left: 25%;"> <?php echo __('New Password'); ?></label>
 <?php echo $this->Form->password('password',array('required','id'=>'pass','minlength'=>'6','style'=>'margin-left: 19%;')); ?>
<br>
<br>
  <span style="color:red;margin-left: 29%;">**Password Must be 6 character long</span>

</td>

  </tr>

   <tr>
 <td> <label style="margin-left: 25%;"> <?php echo __('Confirm Password'); ?> </label>
 <?php echo $this->Form->password('cpassword',array('required','id'=>'cpass','minlength'=>'6','style'=>'margin-left: 19%;')); ?></td>

</tr>


 

 <tr>
 <td> <?php echo $this->Form->submit(__('Save'),array('class'=>'buttonblk','div'=>false,'style'=>'width:auto;margin-left: 25%;')); ?></td></tr>
  <?php }
 else {
 echo("<span style='color:red;align:center;margin-left:110px'>Invalid Key or Expired link</span>");    
 }
  
  ?>
    </table>
  <?php echo $this->Form->end();?>
       </div>
       
       </div>
       
    </div>
    <div class="cls"></div>
  </div>
 
  

