 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">

   <section class="content-header">
     <h1>
       Add Album Manager
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

             <h3 class="box-title">Add Album</h3>
           </div>
           <!-- /.box-header -->

           <div class="box-body">

             <?php echo $this->Form->create($album, array('class' => 'form-horizontal', 'controller' => 'imagegallery', 'action' => 'add', 'onsubmit' => 'return ValidateExtension(this);', 'enctype' => 'multipart/form-data')); ?>

             <div class="form-group">
               <div class="col-sm-3">
                 <label>Select Class<span>
                     <font color="red"> *</font>
                   </span></label>

                 <?php echo $this->Form->input('class_id', array('class' => 'form-control', 'type' => 'select', 'multiple' => 'multiple', 'options' => $class_id, 'empty' => '--Select Class--', 'id' => 'title', 'label' => false,
    'required')); ?>
               </div>
               <div class="col-sm-3">
                 <label>Select Section<span>
                     <font color="red"> *</font>
                   </span></label>

                 <?php echo $this->Form->input('section_id', array('class' => 'form-control', 'type' => 'select', 'multiple' => 'multiple', 'options' => $section_id, 'empty' => '--Select Class--', 'id' => 'title', 'label' => false,
    'required')); ?>
               </div>

               <div class="col-sm-3">
                 <label>Album Name<span>
                     <font color="red"> *</font>
                   </span></label>
                 <?php echo $this->Form->input('title', array('class' => 'form-control', 'placeholder' => 'Enter Album Name', 'type' => 'text', 'id' => 'title', 'label' => false,
    'required')); ?>
               </div>
             </div>
             <div class="form-group">
               <div class="col-sm-3">
                 <label>Album Cover Image<span>
                     <font color="red"> *</font>
                   </span></label>
                 <?php echo $this->Form->input('cover_image', array('class' => 'form-control file', 'id' => 'pic1', 'type' => 'file', 'label' => false, 'required')); ?>

               </div>
             </div>
             <input type="hidden" name="acad_year" value="<?php echo $academicYear; ?>">
             <div class="form-group">



               <div class="col-sm-6">


                 <button type="submit" style="margin-top: 23px;" class="btn btn-success">Create</button>
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


 <script language="javascript" type="text/javascript">
$(function() {
  $("#pic1").change(function() {
    var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.jpg|.jpeg|.gif|.png|.bmp)$/;
    if (regex.test($(this).val().toLowerCase())) {
      return true;

    } else {
      $('#pic1').val('');
      alert("Please upload a valid image file.");
    }
  });
});
 </script>






 <!-- /.content-wrapper -->