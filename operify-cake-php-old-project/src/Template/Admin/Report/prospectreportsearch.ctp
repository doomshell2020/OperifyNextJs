<div class="table-responsive">
  <table id="" class="table table-bordered table-striped">
<tbody> 
   <tr>
   <td><a id="" style="position: absolute;
top: 122px;
/* right: 0px; */
right: 46px;" class="btn btn-info btn-sm pull-right" target="_blank" href="<?php echo ADMIN_URL ;?>report/user_prospectus"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Export PDF</a></td>
   </tr>
    <br>
    <br>
    <br>
     <br> 
                <thead>
					
					
<tr> 
                  <th>S.No.</th>
                  <th>Rec.No.</th>
                  <th>Rg/Pro.No.</th>
                     <th>Date</th>
                       <th>Session</th>
                  <th>Student Name</th>
                  <th>Father's Name</th>
                  <th>Class</th>
                  
               <th>Fee</th>
                
                 
                  
                 
                </tr>
                </thead>
                <tbody id="example22">

  
         

   

           
    <?php 
    
     $page = $this->request->params['paging']['Services']['page'];
    $limit = $this->request->params['paging']['Services']['perPage'];
    $counter = ($page * $limit) - $limit + 1;
       if($stat){
 $this->request->session()->delete('stat');
       $this->request->session()->write('stat',$stat); 
       
       }
    	if($stat=='5'){
    if(isset($t_enquiry) && !empty($t_enquiry)){ 
    foreach($t_enquiry as $service){  //pr($service);
    ?>
                <tr>
        
                  <td><?php echo $counter;?></td>
                   <td><?php if(isset($service['recipietno'])){ echo $service['recipietno'];}else{ echo 'N/A'; } ?></td>
                   <td><?php if(isset($service['formno'])){ echo $service['formno'];}else{ echo 'N/A'; } ?></td>
                    <td><?php if(isset($service['created'])){ echo date('d-m-Y',strtotime($service['created']));}else{ echo 'N/A'; } ?></td>
                    <td><?php echo $acedmic; ?></td>
                  <td><?php if(isset($service['s_name'])){ echo ucfirst($service['s_name']);}else{ echo 'N/A'; } ?></td>
                  <td><?php if(isset($service['fee_submittedby'])){ echo ucfirst($service['fee_submittedby']);}else{ echo 'N/A'; } ?></td>
             
                  <?php $cls=$this->Comman->showclasstitle($service['class_id']); //pr($cls); ?>
                  <td><?php if(isset($cls['title'])){ echo ucfirst($cls['title']);}else{ echo 'N/A'; } ?></td>
                 
                 
                   <td><?php if(isset($service['p_fees'])){ echo $service['p_fees'];}else{ echo 'N/A'; } ?></td>
                 
                  
                </tr>
    <?php $counter++;} }else{?>
    <tr>
    <td colspan="10" style="text-align:center;">NO Data Available</td>
    </tr>
    <?php } }else if($stat=='1'){  if(isset($t_enquiry) && !empty($t_enquiry)){ 
    foreach($t_enquiry as $service){  //pr($service);
    ?>
                <tr>
        
                  <td><?php echo $counter;?></td>
                   <td><?php if(isset($service['recipietno'])){ echo $service['recipietno'];}else{ echo 'N/A'; } ?></td>
                   <td><?php if(isset($service['sno'])){ echo $service['sno'];}else{ echo 'N/A'; } ?></td>
                    <td><?php if(isset($service['created'])){ echo date('d-m-Y',strtotime($service['created']));}else{ echo 'N/A'; } ?></td>
                    <td><?php echo $acedmic; ?></td>
                  <td><?php if(isset($service['fname'])){ echo ucfirst($service['fname'])." ".ucfirst($service['middlename'])." ".ucfirst($service['lname']);}else{ echo 'N/A'; } ?></td>
                  <td><?php if(isset($service['f_name'])){ echo ucfirst($service['f_name']);}else{ echo 'N/A'; } ?></td>
             
                  <?php $cls=$this->Comman->showclasstitle($service['class_id']); ?>
                  <td><?php if(isset($cls['title'])){ echo ucfirst($cls['title']);}else{ echo 'N/A'; } ?></td>
                 
                 
                   <td><?php if(isset($service['reg_fee'])){ echo $service['reg_fee'];}else{ echo 'N/A'; } ?></td>
                 
                  
                </tr>
    <?php $counter++;} }else{?>
    <tr>
    <td colspan="10" style="text-align:center;">NO Data Available</td>
    </tr>
    <?php } }  ?>  

 

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
