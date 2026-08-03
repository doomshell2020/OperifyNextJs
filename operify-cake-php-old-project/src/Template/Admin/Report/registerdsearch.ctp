
    
      <div style="clear: both;"></div>
       <div><a id="" style="margin-right: 20px; margin-top: 10px;" class="btn btn-info btn-sm pull-right" href="<?php echo ADMIN_URL ;?>report/registerd_excel"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Export Excel</a></div>
 <div style="clear: both;"></div>
          
      
 <div class="table-responsive">
                <table id="example1" class="table table-bordered table-striped">
                <thead>

        
        
                <tr>
                 
                  <th>#</th>
                  <th>Form No.</th>
                  <th>Academic Year</th>
                  <th>Pupil's Name</th>
                  <th>Father Mobile</th>
                  <th>Mother Mobile</th>
                  <th>Class</th>
                  
              
                   <th>Added On</th>
                  
                 
                </tr>
                </thead>
                <tbody id="example22">

  
         

   

           
    <?php $page = $this->request->params['paging']['Services']['page'];
    $limit = $this->request->params['paging']['Services']['perPage'];
    $counter = ($page * $limit) - $limit + 1;
    if(isset($t_enquiry) && !empty($t_enquiry)){ 
    foreach($t_enquiry as $service){ // pr($service);
    ?>
                <tr>
             
                  <td><?php echo $counter;?></td>
                   <td><?php if(isset($service['id'])){ echo $service['sno'];}else{ echo 'N/A'; } ?></td>
                        <td><?php if(isset($service['acedmicyear'])){ echo $service['acedmicyear'];}else{ echo 'N/A'; } ?></td>
                  <td><?php if(isset($service['fname'])){ echo ucfirst($service['fname']).'&nbsp;'.ucfirst($service['middlename']).'&nbsp;'.ucfirst($service['lname']);}else{ echo 'N/A'; } ?></td>
             <td><?php if(isset($service['f_phone'])){ echo ucfirst($service['f_phone']);}else{ echo 'N/A'; } ?></td>
                  <td><?php if(isset($service['m_phone'])){ echo ucfirst($service['m_phone']);}else{ echo 'N/A'; } ?></td>
                  <?php $cls=$this->Comman->showclasstitle($service['class_id']); //pr($cls); ?>
                  <td><?php if(isset($cls['title'])){ echo ucfirst($cls['title']);}else{ echo 'N/A'; } ?></td>
                  <?php $bls=$this->Comman->showboardtitle($service['enquire']['mode1_id']); //pr($cls); ?>

                  <td><?php if(isset($service['created'])){ echo date('d-M-Y',strtotime($service['created']));}else{ echo 'N/A'; } ?></td>
                  
                
             
                  
                </tr>
    <?php $counter++;} }else{?>
    <tr>
    <td colspan="9" style="text-align:center;">NO Data Available</td>
    </tr>
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
              <?php echo $this->Form->end(); ?> 

              <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="<?php echo SITE_URL; ?>daterangepicker/daterangepicker.js"></script>
  <link rel="stylesheet" href="<?php echo SITE_URL; ?>daterangepicker/daterangepicker.css">
<script type="text/javascript">
       //Date range picker with time picker
    $('#reservationtime').daterangepicker({timePicker: true, timePickerIncrement: 30, locale: {
            format: 'MM/DD/YYYY h:mm A'
        }   });
</script>
