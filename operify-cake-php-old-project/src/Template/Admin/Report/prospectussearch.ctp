    <div style="clear: both;"></div>
     
      <div><a id="" style="margin-right: 20px; margin-top: 10px;" class="btn btn-info btn-sm pull-right" href="<?php echo ADMIN_URL ;?>report/prospectuss_excel"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Export Excel</a></div>
 <div style="clear: both;"></div>

         
     
      
 <div class="table-responsive">
                <table id="example1" class="table table-bordered table-striped">
                <thead>

        
        
                <tr>
             
                  <th>#</th>
                  <th>Class</th>
                  
                  <? $rolepresent=$this->request->session()->read('Auth.User.role_id'); 
                  foreach($academic as $hh=>$rg){ ?>
					         <th><? echo $rg; ?></th>	 
				       	    <?php  } ?>
           
        
              
                  </tr>
                </thead>
                <tbody id="example22">

    <?php $page = $this->request->params['paging']['Services']['page'];
    $limit = $this->request->params['paging']['Services']['perPage'];
    $counter = ($page * $limit) - $limit + 1;
    if(isset($classes) && !empty($classes)){ 
		$acedmic=0;
	
	
    foreach($classes as $rt=>$service){  //pr($service);
    ?>
                <tr>
               
                  <td><?php echo $counter;?></td>
                 
                  <td><?php if(isset($service)){ echo $service;}else{ echo 'N/A'; } ?></td>
                              <? foreach($academic as $hh=>$rg){ ?>
                  <td><?php 
             
					   $prospectussummary=$this->Comman->findprospectussummary($rt,$rg,$from,$to);  
					  

        
    if(isset($prospectussummary)){ 
			
					  
					  ?><a target="_blank"  href="<?php echo ADMIN_URL; ?>enquires/findacedemicprospectus/<?php echo $rt; ?>/<?php echo $rg; ?>/<?php echo $from; ?>/<?php echo $to; ?>"><?php echo $prospectussummary; ?></a> <?php }else{ echo 'N/A';  } 
                  
                 $acedmic +=$prospectussummary;   ?></td>
					   </tr>
    <?php $counter++;} } }else{?>
    <tr>
    <td colspan="10" style="text-align:center;">NO Admission Available</td>
    </tr>
    <?php } ?>  

  <tr><td colspan="2" style="text-align:center;"><b style="color:red;">Total :-</b></td> <td  style="text-align:left;"><b style="color:green;"><? echo $acedmic; ?></b></td></tr>  

    </tbody></table> 
    </div>
