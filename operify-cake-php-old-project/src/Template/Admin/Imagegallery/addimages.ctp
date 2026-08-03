 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">

   <section class="content-header">
     <h1>
       Album Image Manager
     </h1>
     <ol class="breadcrumb">
       <li><a href="<?php echo SITE_URL; ?>"><i class="fa fa-home"></i>Home</a></li>
       <li><a href="<?php echo ADMIN_URL; ?>imagegallery/index">Album Manager</a></li>
     </ol>
   </section>

   <!-- Main content -->
   <section class="content">
     <div class="row">
       <div class="col-xs-12">

         <div class="box">
           <div class="box-header">

             <?php echo $this->Flash->render(); ?>

             <h3 class="box-title">Add Image - <?php echo $album_name['title']; ?></h3>
           </div>
           <!-- /.box-header -->

           <div class="box-body">

             <?php echo $this->Form->create($album, array('class' => 'form-horizontal', 'controller' => 'imagegallery', 'action' => 'addimages/' . $album_id, 'onsubmit' => 'return ValidateExtension();', 'enctype' => 'multipart/form-data')); ?>

             <div class="form-group">

               <div class="col-sm-3">
                 <label>Name<span>
                     <font color="red"> *</font>
                   </span></label>
                 <?php echo $this->Form->input('title', array('class' => 'form-control', 'placeholder' => 'Enter Image Name', 'type' => 'text', 'id' => 'title', 'label' => false,
    'required')); ?>
               </div>

               <div class="col-sm-3">
                 <label>Select Image<span>
                     <font color="red"> *</font>
                   </span></label>
                 <?php echo $this->Form->input('image_name[]', array('class' => 'form-control file', 'id' => 'imagename', 'type' => 'file', 'label' => false, 'multiple' => 'multiple', 'required')); ?>

               </div>
             </div>
             <div class="form-group">



               <div class="col-sm-6">


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

         <div class="box">
           <div class="box-header">
             <h3 class="box-title">View Images</h3>
           </div>
           <!-- /.box-header -->

           <div class="box-body">
             <div class="row">
               <?php if (isset($album_det) && !empty($album_det)) {
    foreach ($album_det as $value) {
        //pr($value); die;
        ?>
               <div class="col-sm-1">

                 <a href="<?php echo SITE_URL; ?>/galleryimages/<?php echo $value['image_name']; ?>" target="blank">
                   <img src="<?php echo SITE_URL; ?>/galleryimages/<?php echo $value['image_name']; ?>" height=100px,
                     width=100px> </a>
                 <a href="<?php echo ADMIN_URL; ?>imagegallery/deleteimage/<?php echo $album_id; ?>/<?php echo $value['id']; ?>"
                   style="position: absolute;top: -17px;left: 104px;color: red;"
                   onClick="javascript: return confirm('Are you sure you want to delete this?')"><i class="fa fa-times" aria-hidden="true"></i></a>
                 <p style="text-align:center; font-weight:bold"><?php echo ucfirst($value['title']); ?></p>
               </div>
               <?php }} else {?>
               No Data Available
               <?php }?>
             </div>
           </div>
           <!-- /.box-body -->
         </div>
         <!-- /.box -->
       </div>
       <!-- /.col -->
     </div>
     <!-- /.row -->


     <!-- /.row -->
   </section>
   <!-- /.content -->
 </div>
 <script>
function ValidateExtension() {
  var uploadImg = document.getElementById('imagename');
  //uploadImg.files: FileList

  for (var i = 0; i < uploadImg.files.length; i++) {
    var f = uploadImg.files[i];
    if (!endsWith(f.name, 'jpg') && !endsWith(f.name, 'png') && !endsWith(f.name, 'jpeg') && !endsWith(f.name, 'JPG')) {
      alert(f.name + " is not a valid file!");

      uploadImg.value = '';
      return false;

    }
  }

  function endsWith(str, suffix) {
    return str.indexOf(suffix, str.length - suffix.length) !== -1;
  }
}
 </script>



 <!-- /.content-wrapper -->
