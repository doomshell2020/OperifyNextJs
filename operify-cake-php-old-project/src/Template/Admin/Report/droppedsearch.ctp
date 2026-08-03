   <div style="clear: both;"></div>
      <div><a id="" style="margin-right: 20px; margin-top: 10px;" class="btn btn-info btn-sm pull-right" href="<?php echo ADMIN_URL ;?>report/dropped_excel"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Export Excel</a></div>
 <div style="clear: both;"></div>

         
     
      
 <div class="table-responsive">
                <table id="example1" class="table table-bordered table-striped">
                <thead>

        
        
                <tr>
             
                  <th>#</th>
                  <th>Class</th>
                  
                  <? foreach($academic as $hh=>$rg){ ?>
					  
				       <th><? echo $rg; ?></th>	  
				<?  } ?>
           
        
              
                  </tr>
                </thead>
                <tbody id="example22">

    <?php $page = $this->request->params['paging']['Services']['page'];
    $limit = $this->request->params['paging']['Services']['perPage'];
    $counter = ($page * $limit) - $limit + 1;
    if(isset($classes) && !empty($classes)){ 
		$acedmic=0;
		$acedmic1=0;
    foreach($classes as $rt=>$service){  //pr($service);
    ?>
                <tr>
               
                  <td><?php echo $counter;?></td>
                 
                  <td><?php if(isset($service)){ echo $service;}else{ echo 'N/A'; } ?></td>
                              <? foreach($academic as $hh=>$rg){ 
                              // pr($rg);  die;
                                ?>
                  <td><?php 
                  $servicess2=$this->Comman->findacedemicstudents212($rt,$rg,$from,$to,$status_tc); 
                  // pr($servicess2); die;
                  $servicess=$servicess2; 

                  if(isset($servicess)){ echo $servicess;}else{ echo 'N/A';  } if($rg=='2022-23'){ $acedmic +=$servicess; }
					  else if($rg=='2022-23'){ $acedmic1 +=$servicess; }else{  $acedmic2 +=0; }  ?></td>
                  
                  <? } ?>
                      
                  
                   <td></td>
         <td></td>
                 </tr>
    <?php $counter++;}die; }else{?>
    <tr>
    <td colspan="10" style="text-align:center;">NO Admission Available</td>
    </tr>
    <?php } ?>  

 

       
               <tr>
    <td colspan="2" style="text-align:center;"><b style="color:red;">Total :-</b></td>
     <? foreach($academic as $hh=>$rg){ 
		 if($rg=='2020-21'){  ?>
    <td  style="text-align:left;"><b><? echo $acedmic; ?></b></td>
    
    <? }else if($rg=='2021-22'){ ?>
	  <td  style="text-align:left;"><b><? echo $acedmic1; ?></b></td>	
		<? }else if($rg=='2022-23'){ ?>
	  <td  style="text-align:left;"><b><? echo $acedmic2; ?></b></td>	
		<? } } ?>
    
    </tr>  
    


  
                </tbody>
               
              </table> 
    </div>