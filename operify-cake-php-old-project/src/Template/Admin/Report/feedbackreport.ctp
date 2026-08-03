 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Feedbacks
      </h1>
           <ol class="breadcrumb">
    <li><a href="<?php echo ADMIN_URL;?>dashboards"><i class="fa fa-home"></i>Home</a></li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <?php echo $this->Flash->render(); ?>
  <div class="box">
    <div class="box-header">
      <div class="modal" id="globalModal" tabindex="-1" role="dialog" aria-labelledby="esModalLabel" aria-hidden="true">
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
        
          

   
            <!-- /.box-header -->
<?php echo $this->Flash->render(); ?>
 
            </div>  
    
    <div id="updt">
      
      
            <div class="box-body">
 

     
    <?php    if(isset($t_enquiry) && !empty($t_enquiry)){  ?>
      <div class="table-responsive">
                <table id="example1" class="table table-bordered table-striped">
                <thead>

        
        
                <tr>
                   
                  <th>#</th>
                  <th>Form No.</th>
                  <th>Name</th>
                  <th>Mobile</th>
                  <th>Class</th>
                  
                  <th>Board</th>
                  <th>Receipt</th>
                  <th>Status</th>
                 
                  
                 
                </tr>
                </thead>
                <tbody id="example22">

  
         

   

           
    <?php $page = $this->request->params['paging']['Services']['page'];
    $limit = $this->request->params['paging']['Services']['perPage'];
    $counter = ($page * $limit) - $limit + 1;
   
    foreach($t_enquiry as $service){ // pr($service);
    ?>
                <tr>
                  
                  <td><?php echo $counter;?></td>
                   <td><?php if(isset($service['id'])){ echo $service['sno'];}else{ echo 'N/A'; } ?></td>
                  <td><?php if(isset($service['enquire']['s_name'])){ echo ucfirst($service['enquire']['s_name']);}else{ echo 'N/A'; } ?></td>
                  <td><?php if(isset($service['enquire']['mobile'])){ echo ucfirst($service['enquire']['mobile']);}else{ echo 'N/A'; } ?></td>
                  <?php $cls=$this->Comman->showclasstitle($service['class_id']); //pr($cls); ?>
                  <td><?php if(isset($cls['title'])){ echo ucfirst($cls['title']);}else{ echo 'N/A'; } ?></td>
                  <?php $bls=$this->Comman->showboardtitle($service['enquire']['mode1_id']); //pr($cls); ?>

                  <td><?php if(isset($bls['name'])){ echo ucfirst($bls['name']);}else{ echo 'N/A'; } ?></td>
                    <td> <a title="Registration Receipt" id="s<?php echo $service['id']; ?>" target="_blank" href="<?php echo SITE_URL; ?>admin/Students/applicant_recipt/<?php echo $service['id']; ?>"><i class="fa fa-file-text-o"></i></a> </td>

                  <td style="color:green;" ><?php if($service['status']){ echo "Approved"; ?>  <?  }else{ echo 'N/A'; } ?></td>
                   
                 
                  
                </tr>
    <?php $counter++;} }else{?>
    <h3 style="text-align: center;color: red;">NO Approved Applicants.</h3>
    <?php } ?>  

 

         <script type='text/javascript'>

  // Changing state of CheckAll checkbox 
  $(".checkbox").click(function(){

    if($(".checkbox").length == $(".checkbox:checked").length) {
      $("#checkall").prop("checked", true);
    } else {
      $("#checkall").removeAttr("checked");
    }

  });

</script>      
                </tbody>
               
              </table> 
</div>
               </div>  
               
             </div>
