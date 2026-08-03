 <div class="content-wrapper">
    <section class="content-header">
      <h1>
      <!--  Employees Image Manager -->
      </h1>
     <ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-home"></i>Home</a></li>
        <li><a href="#">Employees</a></li>
        <li><a href="#" class="active">Manage Employees Image</a></li>
        <li><a href="#" class="active">Upload Image</a></li>
	 </ol>
    </section>
	
 <section class="content">
	      <div class="row">
	 <div class="col-sm-12">
		<ul class="nav nav-tabs responsive hidden-xs hidden-sm" id="profileTab">
		<!--	<li class="active" id="personal-tab"><a href="http://192.168.5.53/school/admin/students/studentimage/12009#personal" data-toggle="tab"><i class="fa fa-street-view"></i> Capture Image</a></li>-->
			<li id="academic-tab" class="active"><a href="http://192.168.5.53/school/admin/students/studentimage/12009#academic" data-toggle="tab"><i class="fa fa-graduation-cap"></i> Upload Image</a></li>
			</ul>
			</div></div>
			
			<div id="content" class="tab-content responsive hidden-xs hidden-sm">
			<div class="tab-pane " id="personal">	
			   <!-- form start -->
             <?= $this->Html->script('admin/webcam.js') ?>

                   <div class="box-body">
                    	<?php echo $this->Form->create($employees, array(
                       'class'=>'form-horizontal',
		              	'id' => 'sevice_form',
                       'enctype' => 'multipart/form-data'
                       
                     	)); ?>
			
			
			
			
			
			
			
			
			
			
      <div class="row">
       <!--/.col (left) -->
        <!-- right column -->
        <div class="col-md-12">
          <!-- Horizontal Form -->
          <div class="box box-info">
		    <div class="box-header with-border ">
		      <h3 class="box-title">Choose profile Picture</h3>
		    </div>
		     <?php echo $this->Flash->render(); ?>
            <!-- /.box-header -->
         
	<section class="content-header container-fluid">
        <h3 class="col-sm-6" id=rims>
<i class="fa fa-eye"></i> Capture Image <small><?php echo ucfirst($Employees['fname']); ?> <?php echo $Employees['middlename']; ?></small> </h3>
        
</section>

    <div class="form-group" >
     <div class="col-sm-6"  >
    <label></label>
	<div id="my_camera"></div>
    <input type="button" value="Click"  id="gh" onClick="take_snapshot()">
	<div id="results"> </div>
	<input type="hidden" name="file" value="" id="capt">
	<input type="hidden" name="id" value="<?php echo $Employees['id']; ?>" id="capt">
    </div>
    

    </div>
	     <?php
echo $this->Form->end();
?>  
    </div>
	
        <!-- /.box-body -->
		      <div class="box-footer">
			  <?php
				if(isset($Employees['id'])){
				echo $this->Form->submit(
				    'Update', 
				    array('class' => 'btn btn-info pull-left', 'title' => 'Update')
				); }else{ 
				echo $this->Form->submit(
				    'Add', 
				    array('class' => 'btn btn-info pull-left', 'title' => 'Add')
				);
				}  ?>
			<?php
			echo $this->Html->link('Back', [
			    'action' => 'view'
			   
			],['class'=>'btn btn-default pull-right']); ?>
		      
			
		      </div>
		      <!-- /.box-footer -->
		  
          </div>
   
        </div>
        <!--/.col (right) -->
      </div>
      <!-- /.row -->
       
      </div>
     
			<div class="tab-pane active" id="academic">	
				<?php echo $this->Form->create($employees, array(
                       'class'=>'form-horizontal',
		              	'id' => 'sevice_form',
                       'enctype' => 'multipart/form-data'
                       
                     	)); ?>
			
			
			   <div class="box-body">
                    
			
			
			
			
			
			
			
      <div class="row">
       <!--/.col (left) -->
        <!-- right column -->
        <div class="col-md-12">
          <!-- Horizontal Form -->
          <div class="box box-info">
		    <div class="box-header with-border">
		      <h3 class="box-title">Choose profile Picture</h3>
		    </div>
		     <?php echo $this->Flash->render(); ?>
            <!-- /.box-header -->
            <!-- form start -->
             <?= $this->Html->script('admin/webcam.js') ?>

                   <div class="box-body">
                    	<?php echo $this->Form->create($employees, array(
                       'class'=>'form-horizontal',
		              	'id' => 'sevice_form',
                       'enctype' => 'multipart/form-data',
                       'validate'
                     	)); ?>
	<section class="content-header container-fluid">

         <h3 class="col-sm-6 " id="upl">
 <i class="fa fa-eye" ></i> Upload Image | <small><?php echo ucfirst($Employees['fname']); ?> <?php echo $Employees['middlename']; ?></small>        </h3>
</section>

    <div class="form-group" >
 	<input type="hidden" name="id" value="<?php echo $Employees['id']; ?>">
 	<input type="hidden" name="file" value="" >
    
 <div class="col-sm-6" id="upll">
 <label for="inputEmail" class="control-label"></label>
      <?php echo $this->Form->input('photo',array('class'=>'form-control','type'=>'file','placeholder'=>'Choose File', 'id'=>'rem','label' =>false)); ?>
    </div>
    </div>
	     		     <?php
echo $this->Form->end();
?>  
    </div>
	
        <!-- /.box-body -->
		      <div class="box-footer">
			  <?php
				if(isset($Employees['id'])){
				echo $this->Form->submit(
				    'Update', 
				    array('class' => 'btn btn-info pull-left', 'title' => 'Update')
				); }else{ 
				echo $this->Form->submit(
				    'Add', 
				    array('class' => 'btn btn-info pull-left', 'title' => 'Add')
				);
				}  ?>
			<?php
			echo $this->Html->link('Back', [
			    'action' => 'view'
			   
			],['class'=>'btn btn-default pull-right']); ?>
		      
			
		      </div>
		      <!-- /.box-footer -->
		  
          </div>
   
        </div>
        <!--/.col (right) -->
      </div>
      <!-- /.row -->
      
      </div>
          <?php
echo $this->Form->end();
?>  
      </div>
      
      
       </div>
      
    </section>
    <!-- /.content -->
  </div>

<script language="JavaScript">

		Webcam.set({
			width: 267,
			height:190,
			image_format: 'jpeg',
			jpeg_quality: 90
		});
		Webcam.attach( '#my_camera' );
	</script>
	
	<!-- A button for taking snaps -->
	
		<!--<script language="JavaScript">
	function take_snapshot() {//alert('test');
		Webcam.snap
	}	</script>-->
Code to handle taking the snapshot and displaying it locally 
	<script language="JavaScript">
		function take_snapshot() {
			// take snapshot and get image data
			Webcam.snap( function(data_uri) {
				
				// display results in page
				document.getElementById('results').innerHTML = 
					
					'<img src="'+data_uri+'"/>';
					 Webcam.upload( data_uri, 'https://192.168.5.53/school/upload.php', function(code, text) {//alert(text);
					 
					 $('#capt').val(text);
                                            // Upload complete!
                                            // 'code' will be the HTTP response code from the server, e.g. 200
                                            // 'text' will be the raw response content
                                });
			} );
		}
		
		/*
		$(document).ready(function(){
        $('#gh').on('click', function(event) {   
          $('#upl').toggle('hide');	
           $('#upll').toggle('hide');			  
        });
    });
		$(document).ready(function(){
        $('#rem').on('click', function(event) {   
          $('#my_camera').toggle('hide');	
           $('#rims').toggle('hide');	
           $('#gh').toggle('hide');			   
        });
    }); */
	</script>
<script>
$(document).ready(function() {
  if (window.FormData) {
      $('input[type="file"]').bind({
          change : function()
          {
              var input = this,
              files = input.files;
              	 		   	var f=input.files[0];
			var k=f.size || f.fileSize;

			var sizeInkb = (k/1024);
var sizeInkba= Math.round(sizeInkb);
        var sizeLimit= 500;
        if (sizeInkba > sizeLimit) {

     
         alert('Upload  Max file size  500 kb !!');
      


			  $('input[type="file"]').val('');

		}
              else if (files.length > 0) {


                  var regExp = new RegExp('image.(jpeg|jpg|gif|png)', 'i');
                  for (var i = 0; i < files.length; i++)
                  {
                      var file = file = files[i];
                      var matcher = regExp.test(file.type);
                      
                      if (!matcher)
                      {
						  
						 alert('Invalid file,Use only jpg, jpeg, gif, png file format !!');
    

			  $('input[type="file"]').val('');

                      }
                  }
              }  
          }
      });
  } else {
      alert('Browser not support Formdata');
  }
});
		    </script>

