<script>

$(document).ready(function () {
  
  $("#TaskAdminCustomerForm").bind("submit", function (event) {

    $.ajax({

      async: true,
      
      type: "POST",

      url: "<?php echo ADMIN_URL ;?>report/sms_delivered_staff_search",

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
      Employee SMS Delivery Report
    </h1>
    <ol class="breadcrumb">
		<li><a href="<?php echo ADMIN_URL;?>dashboards"><i class="fa fa-home"></i>Home</a></li>
<li><a href="<?php echo ADMIN_URL;?>Employees/index">Manage Student</a></li>
      <li><a href="#" class="active">Employee SMS Delivery Report</a></li>

    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
	     <div id="load2" style="display:none;"></div>
    <div class="row">
      <div class="col-xs-12">
        
       <div class="box">
        <div class="box-header">
          <i class="fa fa-search" aria-hidden="true"></i>
          <h3 class="box-title">Search Category</h3>

        </div>
        <!-- /.box-header -->

        <div class="box-body">

            <div class="manag-stu">

<?php echo $this->Form->create('Task',array('url'=>array('controller'=>'students','action'=>'search'),'type'=>'file','inputDefaults'=>array('div'=>false,'label'=>false),'id'=>'TaskAdminCustomerForm','class'=>'form-horizontal')); ?>

<div class="form-group margin_btmcol">
  




<div class="col-md-3 col-sm-4 col-xs-12">
 <label>SMS Template Category</label>
 <select class="form-control" name="sms_temp_id">

   <option value=""> Select Category </option>
   <?php  foreach($smsmanager as $er=>$e) { ?>
   <option value="<?php echo $e['id']; ?>"><?php echo $e['category']; ?></option>
   <?php } ?>
 </select>
</div>
   <div class="col-md-3 col-sm-4 col-xs-12" >
			   	  <script>
	$(document).ready(function(){		  
				$('#fdatefrom').datepicker({    
	dateFormat: 'yy-mm-dd',
      
    
    }); });
			   </script>	
		  <label for="inputEmail3" class="control-label">SMS Delivered Date</label>
	    	<?php echo $this->Form->input('fdatefrom',array('class'=>'form-control','readonly','id'=>'fdatefrom','placeholder'=>'SMS Delivered Date','label' =>false)); ?>  
		          </div>  

<div class="col-md-3 col-sm-4 col-xs-12 text-xs-center"  style="top: 24px;">
	 <label>&nbsp;</label>
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
      <h3 class="box-title">SMS Delivery Staff List</h3>
        </div>
        <!-- /.box-header -->
        <?php echo $this->Flash->render(); ?>
        <div class="box-body">
					<div class="table-responsive">
          <table id="examples1" class="table table-bordered table-striped">
            
<!--
               <tr>
   <td><a id="" style="position: absolute;
top: -163px;
/* right: 0px; */
right: 46px;" class="btn btn-info btn-sm pull-right" target="_blank" href="<?php echo ADMIN_URL ;?>report/smsdeliverdreports"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Export Excel</a></td>
   </tr>
-->
           <thead>
            <tr>
              <th>#</th>

              <th>Employee Id</th>
              <th> Name</th>
               <th>Father/Husband Name</th>
              <th>SMS Mobile</th>
              <th>Template Category</th>
              <th>SMS Deliverd Date</th>
                  
         
            </tr>
          </thead>
          <tbody id="example2">
            <?php
            $page = $this->request->params['paging']['DropOutStudent']['page'];
            $limit = $this->request->params['paging']['DropOutStudent']['perPage'];
            $counter = ($page * $limit) - $limit + 1; 

            if(isset($employees) && !empty($employees)){ 
              foreach($employees as $work){
               
            ?>
            
            <tr>
             <td><?php echo $counter;?></td>

             <td><?php echo $work['Employees']['id']; ?></td>
             
             <td><a href="#">
              <?php 
                $name = $work['Employees']['fname'].' ';

                if( !empty( $work['Employees']['middlename'] ) )
                  $name .= $work['Employees']['middlename'].' ';

                echo $name .= $work['Employees']['lname'];
              ?></a>
            </td>
                    <td><?php echo $work['Employees']['f_h_name']; ?></td>
             <td><?php if($work['smsmobile']){ echo $work['smsmobile'];   }else{ echo $work['Employees']['mobile'];} ?></td>
       
             
            
             <td><b><?php echo $work['Smsmanager']['category']; ?></b></td>
             <td><?php echo date('d-m-Y H:i:s A',strtotime($work['created'])); ?></td>
             
             
     		</tr>
     <?php $counter++;} }else{ ?>
     <tr>
      <td>NO SMS Delivered Yet</td>
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

