<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> 
<script>


$(document).ready(function () {
  
  $("#TaskAdminCustomerForm").bind("submit", function (event) {

    $.ajax({

      async: true,
      
      type: "POST",

      url: "<?php echo ADMIN_URL ;?>report/detainedreport_student_search",

      data: $("#TaskAdminCustomerForm").serialize(),

      dataType:"html",
        beforeSend: function() {
        // setting a timeout
         $('#load2').css("display", "block");
    },
      success: function (data) {
        $("#example2").html(data);
      }, complete: function() {
           $('#load2').css("display", "none");
    },
      
    });

    return false;

  });

});

</script>


  <style>
         
         #load2{
    width:100%;
    height:100%;
    position:fixed;
    z-index:9999;
    background-color: white  !important;
    background:url("<? echo SITE_URL; ?>images/Preloader_2.gif") no-repeat center center rgba(0,0,0,0.75)
}

</style>
 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
    Detained Report
    </h1>
    <ol class="breadcrumb">
		<li><a href="<?php echo ADMIN_URL;?>studentfees/view"><i class="fa fa-home"></i>Home</a></li>
<li><a href="<?php echo ADMIN_URL;?>report/dropoutreport">Manage Detained Report</a></li>
      <li><a href="#" class="active">Detained Student</a></li>

    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        
       <div class="box">
        <div class="box-header">
          <i class="fa fa-search" aria-hidden="true"></i>
          <h3 class="box-title">Search Detained Student</h3>

        </div>
        <!-- /.box-header -->

        <div class="box-body">

            <div class="manag-stu">

<?php echo $this->Form->create('Task',array('url'=>array('controller'=>'students','action'=>'search'),'type'=>'file','inputDefaults'=>array('div'=>false,'label'=>false),'id'=>'TaskAdminCustomerForm','class'=>'form-horizontal')); ?>

<div class="form-group margin_btmcol">
  


<div class="col-md-3 col-sm-4 col-xs-12">
 <label>Class</label>
 <select class="form-control" name="class_id">
  <option value="">Select Class</option>
  <?php  foreach($classes as $esr=>$es) { ?>
  <option value="<?php echo $esr; ?>"><?php echo $es; ?></option>
  <?php } ?>
</select>
</div>  


<div class="col-md-3 col-sm-4 col-xs-12">
 <label>Section</label>
 <select class="form-control" name="section_id">

   <option value=""> Select Section </option>
   <?php  foreach($sections as $er=>$e) { ?>
   <option value="<?php echo $e['id']; ?>"><?php echo $e['title']; ?></option>
   <?php } ?>
 </select>
</div>
<div class="col-md-3 col-sm-4 col-xs-12">
 <label>Academic Year <strong style="color:red;">*</strong></label>
 <?php echo $this->Form->input('acedmicyear',array('class'=>'form-control','type'=>'select','required','options'=>$academic1,'empty'=>'Select Year', 'label' => false))?>
</div>

  

  <div class="col-md-3 col-sm-12 col-xs-12 text-sm-center" style="margin-top:25px;">
    <button type="submit" class="btn btn-success">Search</button>
    <button type="reset" class="btn btn-primary">Reset</button>
  </div>
</div>
<?php echo $this->Form->end(); ?>   

</div>

</div>

</div>	</div>	</div>

<div class="row" >
  <div class="col-xs-12">
    
   <div class="box" >
    <div class="box-header">
      <i class="fa fa-search" aria-hidden="true"></i>
      <h3 class="box-title">Student List</h3>
        </div>
        <!-- /.box-header -->
        <?php echo $this->Flash->render(); ?>
        <div class="box-body">
					   <div id="load2" style="display:none;"></div>

             <div class="table-responsive">
          <table id="" class="table table-bordered table-striped">
            
            
           <thead>
            <tr>
              <th>#</th>
              <th>Scholar No.</th>

            <th>Pupil Name</th>
               <th>Father Name</th>
               <th>Mother Name</th>
            
     
              <th>Class</th>
              <th>Section</th>
              <th>Academic Year</th>
              <th>Admission Year</th>
                     <th>SMS Mobile </th>
            
           
            </tr>
          </thead>
          <tbody id="example2">
            <?php
            $page = $this->request->params['paging']['DropOutStudent']['page'];
            $limit = $this->request->params['paging']['DropOutStudent']['perPage'];
            $counter = ($page * $limit) - $limit + 1; 

            if(isset($students) && !empty($students)){ 
              foreach($students as $work){
                // pr($work);die;
            ?>
            
            <tr>
             <td><?php echo $counter;?></td>
             <td><?php echo $work['enroll']; ?></td>

            
             <td >
              <?php 
                $name = $work['fname'].' ';

                if( !empty( $work['middlename'] ) )
                  $name .= $work['middlename'].' ';

                echo $name .= $work['lname'];
              ?> 
             </td>
                    <td ><?php echo $work['fathername']; ?></td>
                    <td><?php echo $work['mothername']; ?></td>
                  
                   
          
            
             <td style="font-size: 11px;"><?php echo $work['class']['title']; ?></td>
             <td style="font-size: 11px;"><?php echo $work['section']['title']; ?></td>
   
            <td><?php echo $work['acedmicyear']; ?></td>
                    <td><?php echo $work['admissionyear']; ?></td>
              <td><?php echo $work['sms_mobile']; ?></td>
     		</tr>
     <?php $counter++;} }else{ ?>
     <tr>
      <td>NO Data Available</td>
    </tr>
    
    <?php } ?>	
  </tbody>
  
</table>
</div>

</div>
<!-- /.box-body -->   
</div>
</div>
<!-- /.box -->
</div>
<!-- /.col -->
</div>

<div class="modal" id="globalModal" tabindex="-1" role="dialog" aria-labelledby="esModalLabel" aria-hidden="true" style="display: none;">
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


<div class="modal" id="globalModals" tabindex="-1" role="dialog" aria-labelledby="esModalLabel" aria-hidden="true" style="display: none;">
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

    $(".global").click(function(event){
        //load content from href of link
        $('.modal-content').load($(this).attr("href"));
      });
    
  });

</script>
