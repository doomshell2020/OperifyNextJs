<?php 
$this->request->session()->delete('rtestud');
$this->request->session()->write('rtestud',$students); 
?>


<?php $role_id=$this->request->session()->read('Auth.User.role_id');
 $page = $this->request->params['paging']['Students']['page'];
$limit = $this->request->params['paging']['Students']['perPage'];
$counter = ($page * $limit) - $limit + 1; 
if(isset($students) && !empty($students)){ 
  foreach($students as $work){
    ?>
    <tr>
     <td><?php echo $counter;?></td>
     
     <td><?php echo $work['enroll']; ?></td>
     <td><?php echo $work['fname']; ?></td>
     <td><?php echo $work['fathername']; ?></td>
     <td><?php echo $work['classtitle']; ?></td>
     <td><?php echo $work['sectiontitle']; ?></td>
     <td><?php echo $work['category']; ?></td>

     <td> <?php if($role_id=='1'){ /*  if($work['status']=='Y'){ 
       echo $this->Html->link('Activate', [
         'action' => 'status',
         $work['id'],
         $work['status']  
         ],['class'=>'label label-success']);
       
     }else{ 
      echo $this->Html->link('Deactivate', [
       'action' => 'status',
       $work['id'],
       $work['status']
       ],['class'=>'label label-primary']);
      
     } */ }else{ /*
      if($work['status']=='Y'){ 
      echo $this->Html->link('Activate', [
         'action' => '#'  
         ],['class'=>'label label-success']);
       
     }else{ 
      echo $this->Html->link('Deactivate', [
       'action' => '#'

       ],['class'=>'label label-primary']);
      
     }
    */ 
     
     
     } ?>&nbsp;&nbsp;&nbsp;&nbsp;
     <?php /*if($role_id=='1'){  ?>
    <!--<a title= "Assign Hostal" href="<?php echo SITE_URL; ?>admin/students/assign_hostel/<?php echo $work['id']; ?>"  data-target="#globalModal<?php echo $work['id']; ?>" data-toggle="modal"<i class="fa fa-bed" aria-hidden="true"></i></a>
     &nbsp;&nbsp;-->
     <a title= "Assign Transport" href="<?php echo SITE_URL; ?>admin/students/assign_transport/<?php echo $work['id']; ?>"  data-target="#global<?php echo $work['id']; ?>" data-toggle="modal"<i class="fa fa-bus" aria-hidden="true"></i></a>
     
     &nbsp;&nbsp;&nbsp;&nbsp;
    
    <a title= "Discount" href="<?php echo SITE_URL; ?>admin/students/discount/<?php echo $work['id']; ?>"  data-target="#globaldiscount<?php echo $work['id']; ?>" data-toggle="modal"><i class="fa fa-percent" aria-hidden="true"></i></a>

      &nbsp;&nbsp;

     <a class='global1' title= "Drop out" href="<?php echo SITE_URL; ?>admin/students/drop_out/<?php echo $work['id']; ?>" 
     data-target="#global-drop-out<?php echo $work['id']; ?>" data-toggle="modal"><i class="fa fa-ban" aria-hidden="true"></i></a>

      <div class="modal" id="global-drop-out<?php echo $work['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="esModalLabel" aria-hidden="true" 
      style="display: none;">
      <div class="modal-dialog">
        <div class="modal-content modal-content-drop-out">
          <div class="modal-body">
            <div class="loader">
              <div class="es-spinner">
                <i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script>
          $(document).ready(function() {
        //prepare the dialog

        //respond to click event on anything with 'overlay' class
        $("#global-drop-out<?php echo $work['id']; ?>").click(function(event){

            //load content from href of link
            $('.modal-content-drop-out').load($(this).attr("href"));

          });
      }); 
    </script>

     <?php } */?> 
   </td>
                  <? /* <td><?php
      echo $this->Html->link('Edit', [
          'action' => 'add',
          $work['id']
      ],['class'=>'btn btn-primary']); ?>
      <?php
      echo $this->Html->link('View', [
          'action' => 'view',
          $work['id']
      ],['class'=>'btn btn-success']); ?>
      <?php 
      echo $this->Html->link('Delete', [
          'action' => 'delete',
          $work->id
         ],['title'=>'Delete','class'=> 'btn btn-danger',"onClick"=>"javascript: return confirm('Are you sure do you want to delete this')"]);  ?>
          <?php if($role_id=='1'){  ?>
         <a title='Delete' href="<?php  echo SITE_URL;?>admin/students/delete/<?php echo $work['id']; ?>" class="" onclick="javascript: return confirm('Are you sure do you want to delete this')">
         <span class="fa fa-trash"></span></a>
         <?php } ?>
       </td><? */ ?>
       
    
       
     </tr>
     <?php $counter++;} }else{ ?>
     <tr>
      <td colspan="11" style="text-align:center;">NO Data Available</td>
    </tr>
    <?php } ?>
    
    <?php    if(isset($students) && !empty($students)){ 
      foreach($students as $work){
        ?>
        <div class="modal" id="globalModal<?php echo $work->id; ?>" tabindex="-1" role="dialog" aria-labelledby="esModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-body">
                <div class="loader">
                  <div class="es-spinner">
                    <i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <script>
          $(document).ready(function() {
    //prepare the dialog

    //respond to click event on anything with 'overlay' class
    $("#globalModal<?php echo $work->id; ?>").click(function(event){

     
     
        $('.modal-content').load($(this).attr("href"));  //load content from href of link

      });
  }); 
</script>
<?php } }?>

<?php    if(isset($students) && !empty($students)){ 
  foreach($students as $work){
    ?>
    <div class="modal" id="global<?php echo $work->id; ?>" tabindex="-1" role="dialog" aria-labelledby="esModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-body">
            <div class="loader">
              <div class="es-spinner">
                <i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <script>
      $(document).ready(function() {
    //prepare the dialog

    //respond to click event on anything with 'overlay' class
    $("#globalModal<?php echo $work->id; ?>").click(function(event){

     
     
        $('.modal-content').load($(this).attr("href"));  //load content from href of link

      });
  }); 
</script>
<?php } }?>   

<?php    if(isset($students) && !empty($students)){ 
  foreach($students as $work){
    ?>
    <div class="modal" id="globaldiscount<?php echo $work->id; ?>" tabindex="-1" role="dialog" aria-labelledby="esModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-body">
            <div class="loader">
              <div class="es-spinner">
                <i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <script>
      $(document).ready(function() {
    //prepare the dialog

    //respond to click event on anything with 'overlay' class
    $("#globaldiscount<?php echo $work->id; ?>").click(function(event){

     
     
        $('.modal-content').load($(this).attr("href"));  //load content from href of link

      });
  }); 
</script>
<?php } }?>  

