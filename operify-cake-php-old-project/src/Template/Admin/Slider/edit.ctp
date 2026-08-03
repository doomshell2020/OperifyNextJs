 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">

   <section class="content-header">
     <h1>
       Edit Slider
     </h1>
     <ol class="breadcrumb">
       <li><a href="<?php echo SITE_URL; ?>"><i class="fa fa-home"></i>Home</a></li>
       <li><a href="<?php echo ADMIN_URL; ?>Slider/index">Slider Manager</a></li>
     </ol>
   </section>

   <!-- Main content -->
   <section class="content">
     <div class="row">
       <div class="col-xs-12">

         <div class="box">
           <div class="box-header">

             <?php echo $this->Flash->render(); ?>

             <h3 class="box-title">Edit Slider</h3>
           </div>
           <!-- /.box-header -->

           <div class="box-body">

             <?php echo $this->Form->create($slider_det, array('class' => 'form-horizontal', 'enctype' => 'multipart/form-data')); ?>

             <div class="form-group">



               <div class="col-sm-3">
                 <label>Slider Name<span>
                     <font color="red"> *</font>
                   </span></label>
                 <?php echo $this->Form->input('title', array('class' => 'form-control', 'placeholder' => 'Enter Slider Name', 'type' => 'text', 'id' => 'title', 'label' => false,
    'required')); ?>
               </div>
             </div>
             <div class="form-group">
               <div class="col-sm-3">
                 <label>Album Cover Image<span>
                     <font color="red"> *</font>
                   </span></label>
                 <?php echo $this->Form->input('image', array('class' => 'form-control file', 'type' => 'file', 'id' => 'file', 'label' => false,'onchange'=>'return fileValidation();')); ?>
                 <span style="color:red;display:none;" id="image_ext">Invalid Image Type</span>
                 <div id="imagePreview" style= ></div>
               </div>
             </div>
             <div class="form-group">



               <div class="col-sm-6">


                 <button type="submit" style="margin-top: 23px;" class="btn btn-success">Edit</button>
                 <button type="reset" style="margin-top: 23px;" class="btn btn-primary">Reset</button>


               </div>

             </div>

             <?php echo $this->Form->end(); ?>

           </div>

         </div>
       </div>
     </div>



     <!-- /.row -->
   </section>
   <!-- /.content -->
 </div>
 <script>
    function fileValidation() {
      var fileInput =
        document.getElementById('file');
      var filePath = fileInput.value;
      // Allowing file type
      var allowedExtensions =
          /(\.jpg|\.jpeg|\.png|\.gif)$/i;
      
      if (!allowedExtensions.exec(filePath)) {
        $('#image_ext').show('');
        fileInput.value = '';
      }else{

        $('#image_ext').hide('');
//  // Image preview
//  if (fileInput.files && fileInput.files[0]) {
//           var reader = new FileReader();
//           reader.onload = function(e) {
//             document.getElementById(
//               'imagePreview').innerHTML =
//               '<img src="' + e.target.result
//               + '"/>';
//           };
          
//           reader.readAsDataURL(fileInput.files[0]);
//         }


      }

        }
  </script>



 <!-- /.content-wrapper -->