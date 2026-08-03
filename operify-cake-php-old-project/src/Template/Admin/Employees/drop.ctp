<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/css/bootstrap-datepicker3.css"/>
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
<?php $role_id=$this->request->session()->read('Auth.User.role_id');  ?>
 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Employee Drop Out Manager
    </h1>
    <ol class="breadcrumb">
		<li><a href="<?php echo ADMIN_URL;?>Employees/index"><i class="fa fa-home"></i>Home</a></li>
<li><a href="<?php echo ADMIN_URL;?>Employees/index">Manage Employees</a></li>
      <li><a href="#" class="active">Drop Employees</a></li>

    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        
       <div class="box">
        <div class="box-header">
          <i class="fa fa-search" aria-hidden="true"></i>
          <h3 class="box-title">Search Employees</h3>

        </div>
        <!-- /.box-header -->

        <div class="box-body">
<div class="manag-stu">
<script>

$(document).ready(function () {
  
  $("#TaskAdminCustomerForm").bind("submit", function (event) {

    $.ajax({

      async: true,
      
      type: "POST",

      url: "<?php echo ADMIN_URL ;?>Employees/drop_out_employee_search",

      data: $("#TaskAdminCustomerForm").serialize(),

      dataType:"html",
      beforeSend: function() {
        // setting a timeout
         $('#load2').css("display", "block");
    },
      success: function (data) {
        $("#example2").html(data);
      } , complete: function() {
           $('#load2').css("display", "none");
    },  
      
    });

    return false;

  });

});

</script>
	 <?php echo $this->Form->create('Task',array('url'=>array('controller'=>'employees','action'=>'search'),'type'=>'file','inputDefaults'=>array('div'=>false,'label'=>false),'id'=>'TaskAdminCustomerForm','class'=>'form-horizontal')); ?>

<script>
$(document).ready(function(){
$('#department_id').on('change',function(){
var id = $('#department_id').val();

 $.ajax({ 
        type: 'POST', 
        url: '<?php echo ADMIN_URL ;?>Employees/find_designation',
        data: {'id':id}, 
        success: function(data){  

 $('#designation_id').empty();
  $('#designation_id').html(data);
        }, 
        
    });  
});
});

</script>
  
  <style>
  
.uploadconh{height: 20px;
width: 20px;
display: inline-block;
position:relative}
.uploadconh:before{content:"\f0c6";font-family:'FontAwesome'; font-size:1.33333em;color: rgb(30, 138, 210); position:absolute;left:0;top:0}
.uploadconh input{padding:0;opacity:0;cursor:pointer}
  </style>
  
  
  
  <div class="form-group">
    
    <div class="col-sm-2">
 <label></label>
  <?php echo $this->Form->input('department_id',array('class'=>'form-control','id'=>'department_id','type'=>'select','empty'=>'Select Department','options'=>$Departments,'label' =>false)); ?> 
    </div>  
    
    <div class="col-sm-2">
 <label></label>
  <?php echo $this->Form->input('designation_id',array('class'=>'form-control','type'=>'select','id'=>'designation_id','empty'=>'Select Designation','options'=>$Designations,'label' =>false)); ?> 
    </div>  
    
    
  <div class="col-sm-2">
 <label></label>
  <?php echo $this->Form->input('name',array('class'=>'form-control','placeholder'=>'Enter Name ','label' =>false)); ?> 
    </div>   
       
     <div class="col-sm-2">
 <label></label>
  <?php echo $this->Form->input('mobile',array('class'=>'form-control','placeholder'=>'Enter Mobile','label' =>false)); ?> 
    </div>   
 
    <div class="col-sm-4" style="margin-top: 20px;">
      <button type="submit" class="btn btn-success">Search</button>
       <button type="reset" class="btn btn-primary">Reset</button>
    </div>
  </div>
     <?php
echo $this->Form->end();
?>   

</div>

</div>

</div>	</div>	</div>

<div class="row" >
  <div class="col-xs-12">
    
   <div class="box" >
    <div class="box-header">
      <i class="fa fa-search" aria-hidden="true"></i>
      <h3 class="box-title">Drop Employees List</h3>
        </div>
        <!-- /.box-header -->
        <?php echo $this->Flash->render(); ?>
        <div class="box-body">
			            <div id="load2" style="display:none;"></div>
          <table id="example1" class="table table-bordered table-striped">


            
           <thead>
                  <tr>
       
                    <th>S.No.</th>
                    <th>Name</th>
                    <th>Email</th>

                    <th>Mobile</th>
                    <th>Father/Husband Name</th>
                    <th>DOB</th>
                    <th>Joining Date</th> 
                    <th>Drop Date</th> 
                    <th>Action</th>
</tr>
          </thead>
          <tbody id="example2">
            <?php
            $page = $this->request->params['paging']['DropOutStudent']['page'];
            $limit = $this->request->params['paging']['DropOutStudent']['perPage'];
            $counter = ($page * $limit) - $limit + 1; 

            if(isset($students) && !empty($students)){ 
               foreach ($students as $work) {

        ?><?php $att_exist = $this->Comman->findAttendance($work['id']);
        //pr($att_exist);

        //pr($period);die;?>
                  <tr <?php if (!empty($att_exist['id'])) {?>style="color:red" <?php }?>>


                  
                    <td><?php echo $counter; ?></td>
                    <!-- <td><img src="<?php //echo $this->request->webroot;?>img/studentlist_img.png" </td>-->
                    <td > 
                  <a title="View Emp;loyee Detail" href=""><?php echo strtoupper($work['fname']);
            ?>&nbsp;<?php echo strtoupper($work['middlename']);
            ?>&nbsp;<?php echo strtoupper($work['lname']);
            ?></a></td>
                    <td><?php echo $work['username'];

        ?></td>
                    
                    <td><?php echo $work['mobile'];
        ?></td>
                    <td><?php echo $work['f_h_name'];
        ?></td>
                    <td><?php echo date('d-m-Y', strtotime($work['dob']));
        ?></td>
                    <td><?php echo date('d-m-Y', strtotime($work['joiningdate']));
        ?></td>
         <td><?php echo date('d-m-Y', strtotime($work['drop_date']));
        ?></td>
                    <td><?php if ($role_id == '16' || $role_id == '5' || $role_id == '17') {
				 if($work['nodues'] !=''){ ?> 
            <a href="<?php echo SITE_URL;?>admin/employees/relieving_certificate_info/<?php echo $work['id']; ?>" title="Relieving Certificate" data-target="#globalModal"  data-toggle="modal" class="global" style="color: blue;">
                    <i class="fa fa-file-pdf-o"></i>
                  </a>&nbsp; <a download="nodues-<?php echo $work['username']; ?>" href="<?php echo SITE_URL;?>images/<?php echo $work['nodues']; ?>" title="NO DUES" style="color: green;"><i class="fa fa-file-pdf-o"></i></a> &nbsp;
                  <a href="<?php echo SITE_URL;?>admin/employees/restoreemployee/<?php echo $work['id']; ?>" title="Restore Employee" style="color: maroon;"><img src="https://use.fontawesome.com/releases/v5.0.13/svgs/solid/undo.svg" width="15" height="15"></a> 
                
                  <?php }else{   ?>  
<a  class="uploadconh uploadtawrd-222">
<input type="file" title="Upload No Dues" id="contractdoc-<?php echo $work['id']; ?>" data-type="<?php echo $work['id']; ?>" name="contractdoc" class="fa fa-paperclip upload form-control" style="width: 20px;"></a>
<a target="_blank" href="<?php echo SITE_URL;?>admin/employees/nodues_pdf/<?php echo $work['id']; ?>" title="NO DUES Form" style="color: green;"><i class="fa fa-file-pdf-o"></i></a> &nbsp;
<a href="<?php echo SITE_URL;?>admin/employees/restoreemployee/<?php echo $work['id']; ?>" title="Restore Employee" style="color: maroon;"><img src="https://use.fontawesome.com/releases/v5.0.13/svgs/solid/undo.svg" width="15" height="15"></a> 
                

			 <?php } ?>
                   
                 <?php } ?></td> 
                  </tr><?php $counter++;
    } }else{ ?>
     <tr>
      <td colspan="8" align="center"><b style="color:red;">NO Data Available</b></td>
    </tr>
    
    <?php } ?>	
  </tbody>
  
</table>

</div>
<!-- /.box-body -->   
</div>
</div>
<!-- /.box -->
</div>
<!-- /.col -->
</div>

<div class="modal" id="globalModal" tabindex="-1" role="dialog" aria-labelledby="esModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" >
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
$(document).ready(function(){
 $(document).on('change', '.upload', function(){

  var name = this.files[0].name;
var dtid=$(this).data("type");
 
  var form_data = new FormData();
  var ext = name.split('.').pop().toLowerCase();
  if(jQuery.inArray(ext, ['pdf','png','jpg','jpeg']) == -1) 
  {
   alert("Invalid Image File");
  }
  var oFReader = new FileReader();
  oFReader.readAsDataURL(this.files[0]);
  var f = this.files[0];
  var fsize = f.size||f.fileSize;
  if(fsize > 2000000)
  {
   alert("Image File Size is very big");
  }
  else
  {
	  

   form_data.append("file", this.files[0]);
   form_data.append("id", dtid);
   
   $.ajax({
    url:"<?php echo ADMIN_URL; ?>employees/noduesupload",
    method:"POST",
    data: form_data,
    contentType: false,
    cache: false,
    processData: false,
    beforeSend:function(){
  $('#load2').css("display", "block");
    },   
    success:function(data)
    {
	
window.location.href="<?php echo ADMIN_URL; ?>employees/drop";

    }
   });
  }
 });
});
</script>

<script>

  $(document).ready(function() {

    $(".global").click(function(event){
				
		 
        //load content from href of link
        $('.modal-content').html('<div class="modal-body"><div class="loader"><div class="es-spinner"><i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i></div> </div></div>');
         $('.modal-content-drop-out').html('<div class="modal-body"><div class="loader"><div class="es-spinner"><i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i></div> </div></div>');
        $('.modal-content').load($(this).attr("href"));
      });
    
  });

</script>
	
