 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">

   <section class="content-header">
     <?php echo $this->Flash->render(); ?>
     <h1>
       <i class="fa fa-plus-square"> </i>Album Manager </h1>
     <ol class="breadcrumb">
       <li><a href="<?php echo ADMIN_URL; ?>"><i class="fa fa-home"></i>Home</a></li>
       <li class="active"><a href="<?php echo ADMIN_URL; ?>Books/index">Albums</a></li>
     </ol>

   </section>
   <style>
   .ui-autocomplete {
     max-height: 100px;
     overflow-y: auto;
     /* prevent horizontal scrollbar */
     overflow-x: hidden;
   }

   /* IE 6 doesn't support max-height
   * we use height instead, but this forces the menu to always be this tall
   */
   * html .ui-autocomplete {
     height: 100px;
   }
   </style>
   <!-- Main content -->
   <section class="content">
     <a class="btn btn-success pull-right" target="_blank" style="margin-right: 10px;"
       href="<?php echo SITE_URL; ?>admin/imagegallery/add"><i class="fa fa-plus"></i> Add Album</a>
     <div class="row">
       <div class="col-xs-12">
         <div>
           <?php echo $this->Flash->render(); ?>
         </div>
         <div class="box">
           <div class="box-body">



             <table id="example1" class="table table-bordered table-striped">
               <thead>
                 <tr>
                   <th>#</th>
                   <th>Class </th>
                   <th>Section </th>
                   <th>Album Name</th>
                   <th>Cover Image</th>
                   <th>Action</th>
                 </tr>
               </thead>
               <tbody>
                 <?php $page = $this->request->params['paging']['cupboards']['page'];
$limit = $this->request->params['paging']['cupboards']['perPage'];
$counter = ($page * $limit) - $limit + 1;?>
                 <?php if (count($destination) > 0) { //pr($events);?>
                 <?php foreach ($destination as $key => $value) {?>
                 <?php //pr($destination); die; ?>
                 <tr>



                   <td><?php echo $counter; ?></td>
                   <td><?php $class_id = explode(',', $value['class_id']);
    //pr($class_id); die;
    foreach ($class_id as $id) {
        $class = $this->Comman->showclasstitle($id);
        echo $class['title'];?> <br>
                     <?php }
    ?></td>
                   <td><?php $section_id = explode(',', $value['section_id']);
    //pr($class_id); die;
    foreach ($section_id as $id) {
        $section = $this->Comman->findsection123($id);
        echo $section['title'];?> <br>
                     <?php }
    ?></td>
                   <td><a href="<?php echo ADMIN_URL; ?>imagegallery/addimages/<?php echo $value['id']; ?>">
                       <?php echo ucfirst(substr($value['title'], 0, 11)); ?></a></td>
                   <td><img src="<?php echo SITE_URL; ?>galleryimages/<?php echo $value['cover_image']; ?>" height="100"
                       width="100" /></td>

                   <td><a href="<?php echo ADMIN_URL ?>imagegallery/deletealbum/<?php echo $value['id']; ?>"
                       class="btn btn4 btn_trash_a"
                       onClick="javascript: return confirm('Are you sure you want to delete this?')"><img
                         src="<?php echo SITE_URL; ?>/images/trash.png"></a>
                     <a href="<?php echo ADMIN_URL ?>imagegallery/editalbum/<?php echo $value['id']; ?>"
                       class="btn btn4 btn_trash_a"><img src="<?php echo SITE_URL; ?>/images/edit1.png"></a> </td>
                 </tr>

                 <?php $counter++;}?>
                 <?php } else {?>
                 <tr>
                   <td colspan="7" align="center">No Data Available</td>
                 </tr>
                 <?php }?>
               </tbody>
             </table>
           </div>

         </div>

       </div>

     </div>

     <!-- /.row -->
   </section>
   <!-- /.content -->
 </div>


 <!-- /.content-wrapper -->