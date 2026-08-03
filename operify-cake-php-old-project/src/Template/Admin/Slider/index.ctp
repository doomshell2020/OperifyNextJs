 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">

   <section class="content-header">
     <h1>
       Slider Manager
     </h1>
     <ol class="breadcrumb">
       <li><a href="<?php echo SITE_URL; ?>"><i class="fa fa-home"></i>Home</a></li>
     </ol>
   </section>

   <!-- Main content -->
   <section class="content">
     <div class="row">
       <div class="col-xs-12">

         <div class="box">
           <div class="box-header">

             <?php echo $this->Flash->render(); ?>

             <h3 class="box-title">Add Slider</h3>
           </div>
           <!-- /.box-header -->

           <div class="box-body">

             <?php echo $this->Form->create($album, array('class' => 'form-horizontal slider_mngrfrm',  'controller' => 'slider', 'action' => 'index', 'onsubmit' => 'return ValidateExtension(this);', 'enctype' => 'multipart/form-data')); ?>

             <div class="form-group">


               <div class="col-sm-4 col-xs-6">
                 <label>Name<span>
                     <font color="red"> *</font>
                   </span></label>
                 <?php echo $this->Form->input('title', array('class' => 'form-control', 'placeholder' => 'Enter Slider Name', 'type' => 'text', 'id' => 'title', 'label' => false, 'required')); ?>
               </div>

               <div class="col-sm-4 col-xs-6">
                 <label>Select Image<span>
                     <font color="red"> *</font>
                   </span></label>
                 <?php echo $this->Form->input('image', array('class' => 'form-control', 'id' => 'file', 'type' => 'file', 'label' => false, 'required', 'onchange' => 'return fileValidation();')); ?>
                 <span style="color:red;display:none;" id="image_ext">Invalid Image Type</span>
               </div>




               <div class="col-sm-4 col-xs-12">


                 <button type="submit" style="margin-top: 23px;" class="btn btn-success">Add</button>
                 <button type="reset" style="margin-top: 23px;" class="btn btn-primary">Reset</button>


               </div>

             </div>

             <?php echo $this->Form->end(); ?>

           </div>

         </div>
       </div>
     </div>
     <div class="row">
       <div class="col-xs-12">
         <div>
           <?php echo $this->Flash->render(); ?>
         </div>
         <div class="box">
           <div class="box-body">


             <div class="table-responsive">
               <table id="example1" class="table table-bordered table-striped">
                 <thead>
                   <tr>
                     <th>#</th>
                     <th>Title</th>
                     <th>Slider image</th>
                     <th>Action</th>
                   </tr>
                 </thead>
                 <tbody>

                   <?php $counter = 1;
                    if (count($slider_data) > 0) { //pr($events);
                    ?>
                     <?php foreach ($slider_data as $key => $value) { ?>
                       <?php //pr($destination); die; 
                        ?>
                       <tr>



                         <td><?php echo $counter; ?></td>

                         <td> <?php echo ucfirst(substr($value['title'], 0, 11)); ?></td>
                         <td>
                           <img src="<?php echo SITE_URL; ?>/sliderimages/<?php echo $value['image'] ?>" height="100px" width="100px">
                         </td>

                         <td><a href="<?php echo ADMIN_URL ?>Slider/delete/<?php echo $value['id']; ?>" class="btn btn4 btn_trash_a" onClick="javascript: return confirm('Are you sure you want to delete this?')"><img src="<?php echo SITE_URL; ?>/images/trash.png"></a>

                           <a href="<?php echo ADMIN_URL ?>Slider/edit/<?php echo $value['id']; ?>" class="btn btn4 btn_trash_a"><img src="<?php echo SITE_URL; ?>/images/edit1.png"></a>
                           <?php if ($value['status'] == 'Y') { ?>
                             <a href="<?php echo ADMIN_URL; ?>Slider/status/<?php echo $value['id']; ?>/N" class="label label-danger">Inactivate</a><?php } else { ?>
                             <a href="<?php echo ADMIN_URL; ?>Slider/status/<?php echo $value['id']; ?>/Y" class="label label-success">Activate</a>
                           <?php } ?>
                         </td>
                       </tr>

                     <?php $counter++;
                      } ?>
                   <?php } else { ?>
                     <tr>
                       <td colspan="7" align="center">No Data Available</td>
                     </tr>
                   <?php } ?>
                 </tbody>
               </table>
             </div>
           </div>

         </div>

       </div>

     </div>
   </section>
   <!-- /.content -->
 </div>
 <!-- <script>
$(function() {
  $("#imagename").change(function() {
    var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.jpg|.jpeg|.png|.bmp)$/;s
    if (regex.test($(this).val().toLowerCase())) {
      return true;

    } else {
      $('#imagename').val('');
      alert("Please upload a valid image file.");
    }
  });
});
 </script> -->

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
     } else {

       $('#image_ext').hide('');
     }

   }
 </script>


 <script>
   $(document).ready(function() {

     var _URL = window.URL || window.webkitURL;

     $('#file').change(function() {
       //alert();;
       var file = $(this)[0].files[0];

       img = new Image();
       var imgwidth = 0;
       var imgheight = 0;
       var minwidth = 1080;
       var minheight = 480;

       img.src = _URL.createObjectURL(file);
       img.onload = function() {
         imgwidth = this.width;
         imgheight = this.height;


         if (imgwidth >= minwidth && imgheight >= minheight) {

           var formData = new FormData();
           formData.append('fileToUpload', $('#file')[0].files[0]);


         } else {
           $('#file').val("");
           alert("Image size must be " + minwidth + "X" + minheight);
         }
       };
       img.onerror = function() {

         alert("not a valid file: " + file.type);
       }

     });
   });
 </script>