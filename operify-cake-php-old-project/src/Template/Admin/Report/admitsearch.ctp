    <div style="clear: both;"></div>
     
      <div><a id="" style="margin-right: 20px; margin-top: 10px;" class="btn btn-info btn-sm pull-right" href="<?php echo ADMIN_URL ;?>report/admit_excel"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Export Excel</a></div>
 <div style="clear: both;"></div>

         
     
      
 <div class="table-responsive">
                <table id="example1" class="table table-bordered table-striped">
                <thead>

        
        
                <tr>
             
                  <th>#</th>
                  <th>Class</th>
                  
                  <? foreach($academic as $hh=>$rg){ ?>
					  
				       <th><? echo $rg; ?><br>(Admission  Student)</th>	 
				       	       <th><? echo $rg; ?><br>(RTE Student)</th>
				       <th><? echo $rg; ?><br>(Drop Out Student)</th>	 
				 
				       
				       <?php $rolepresent=$this->request->session()->read('Auth.User.role_id');


if($rolepresent=='5'){   ?>
	      <th><? echo $rg; ?><br>(Migrated To International)</th>
				         <th><? echo $rg; ?><br>(Migrated From International)</th>
	
	
	
	<?php }else{  ?>
				        <th><? echo $rg; ?><br>(Migrated To CBSE)</th>
				         <th><? echo $rg; ?><br>(Migrated From CBSE)</th>
				         
				         <?php } ?>
				         	   
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
		$acedmic2=0;
		$acedmic3=0;
		$servicessrte2=0;
    foreach($classes as $rt=>$service){  //pr($service);
    ?>
                <tr>
               
                  <td><?php echo $counter;?></td>
                 
                  <td><?php if(isset($service)){ echo $service;}else{ echo 'N/A'; } ?></td>
                              <? foreach($academic as $hh=>$rg){ ?>
                  <td><?php 
             $c[]=$rt;
					   $servicess=$this->Comman->findacedemicstudentshisa2($rt,$rg,$from,$to);  
					  
			 $servicess=$servicess; 
        
                   if(isset($servicess)){ 
			
					  
					  ?><a target="_blank"  href="<?php echo ADMIN_URL; ?>students/findacedemicsummary/<?php echo $rt; ?>/<?php echo $rg; ?>/<?php echo $from; ?>/<?php echo $to; ?>"><?php echo $servicess; ?></a> <?php }else{ echo 'N/A';  } 
                  
                 $acedmic +=$servicess;  ?></td>
					  
					  
					   
                  
                     <td><?php 
             
					   $servicessrte=$this->Comman->findacedemicstudentrte($rt,$rg,$from,$to); 
                      
            if(isset($servicessrte)){ 
			
					  
					  ?><a target="_blank"  href="<?php echo ADMIN_URL; ?>students/findacedemicsummaryrte/<?php echo $rt; ?>/<?php echo $rg; ?>/<?php echo $from; ?>/<?php echo $to; ?>"><?php echo $servicessrte; ?></a> <?php }else{ echo 'N/A';  } 
                  
                  $servicessrte2 +=$servicessrte;  ?></td>
                  
					    <td><?php 
             
					   $servicess2=$this->Comman->findacedemicstudentsdrop2($rt,$rg,$from,$to); 
                      
            if(isset($servicess2)){ 
			
					  
					  ?><a target="_blank"  href="<?php echo ADMIN_URL; ?>students/findacedemicsummarydrop/<?php echo $rt; ?>/<?php echo $rg; ?>/<?php echo $from; ?>/<?php echo $to; ?>"><?php echo $servicess2; ?></a> <?php }else{ echo 'N/A';  } 
                  
                  $acedmic1 +=$servicess2;  ?></td>
					  
                  <td><?php 
             
					   $servicess12=$this->Comman->findacedemicstudentshisa21($rt,$rg,$from,$to);  
					  
					  
                     
                  if(isset($servicess12)){ 
			
					  
					  ?><a target="_blank"  href="<?php echo ADMIN_URL; ?>students/findacedemicsummary2/<?php echo $rt; ?>/<?php echo $rg; ?>/<?php echo $from; ?>/<?php echo $to; ?>"><?php echo $servicess12; ?></a><?php }else{ echo 'N/A';  } 
                  
                $acedmic2 +=$servicess12;   ?>
                  
                  
                  </td>
                  <td> <?php 
          
					   $servicess13=$this->Comman->findacedemicstudentshisa213($rt,$rg,$from,$to);  
					  
					  
                     
                  if(isset($servicess13)){ 
			
					  
					  ?><a target="_blank"  href="<?php echo ADMIN_URL; ?>students/findacedemicsummary3/<?php echo $rt; ?>/<?php echo $rg; ?>/<?php echo $from; ?>/<?php echo $to; ?>"><?php echo $servicess13; ?></a> <?php }else{ echo 'N/A';  } 
                  
                   $acedmic3 +=$servicess13;  ?>
                  
                  </td>
                  
                  
                  <td></td>
                  <? } ?>
                      
                  
                   <td></td>
         <td></td>
                 </tr>
    <?php $counter++;} }else{?>
    <tr>
    <td colspan="10" style="text-align:center;">NO Admission Available</td>
    </tr>
    <?php } ?>  

 

       
               <tr> <?php  $css=implode(',',$c); $cs=base64_encode($css); ?>
    <td colspan="2" style="text-align:center;"><b style="color:red;">Total :-</b></td>
   
    <td  style="text-align:left;"><a target="_blank" href="<?php echo ADMIN_URL; ?>students/findacedemicsummary/0/<?php echo $rg; ?>/<?php echo $from; ?>/<?php echo $to; ?>/<?php echo $cs; ?>"><b style="color:green;"><? echo $acedmic; ?></b></a></td>
    

    
    <td  style="text-align:left;"><a target="_blank" href="<?php echo ADMIN_URL; ?>students/findacedemicsummaryrte/0/<?php echo $rg; ?>/<?php echo $from; ?>/<?php echo $to; ?>/<?php echo $cs; ?>"><b style="color:green;">(+)<? echo $servicessrte2; ?></b></a></td>
    
        <td  style="text-align:left;"><a target="_blank" href="<?php echo ADMIN_URL; ?>students/findacedemicsummarydrop/0/<?php echo $rg; ?>/<?php echo $from; ?>/<?php echo $to; ?>/<?php echo $cs; ?>"><b style="color:green;">(+)<? echo $acedmic1; ?></b></a></td>
        
	<td  style="text-align:left;"><a target="_blank"  href="<?php echo ADMIN_URL; ?>students/findacedemicsummary2/0/<?php echo $rg; ?>/<?php echo $from; ?>/<?php echo $to; ?>/<?php echo $cs; ?>"><b style="color:green;">(-)<? echo $acedmic2; ?></b></a></td>
	    	
	<td  style="text-align:left;"><a target="_blank"  href="<?php echo ADMIN_URL; ?>students/findacedemicsummary3/0/<?php echo $rg; ?>/<?php echo $from; ?>/<?php echo $to; ?>/<?php echo $cs; ?>">(+)<? echo $acedmic3; ?></b></a></td>	
	  
	<td  style="text-align:left;"><b>Net Total Admission 
		<?php
	 $rolepresent=$this->request->session()->read('Auth.User.role_id');

if($academic[0]!='2018-19'){
if($rolepresent=='5'){  ?>
	 <a target="_blank" href="<?php echo ADMIN_URL; ?>students/admissionsummaryacedmic/cbse/<?php echo $rg; ?>/<?php echo $cs; ?>">
	 
	  <?php }else { ?>
	 <a target="_blank" href="<?php echo ADMIN_URL; ?>students/admissionsummaryacedmic/int/<?php echo $rg; ?>/<?php echo $cs; ?>">
		  	    <?php } ?>
	
	<?php $sum=$acedmic-$acedmic2;  echo $servicessrte2+$acedmic1+$acedmic3+$sum;?></a><br> 
	
	<span style='color: red;'>Active Students</span>
	
	
	<?php
	 $rolepresent=$this->request->session()->read('Auth.User.role_id');


if($rolepresent=='5'){  ?>
	 <a target="_blank" href="<?php echo ADMIN_URL; ?>students/admissionsummaryacedmic2/cbse/<?php echo $rg; ?>/<?php echo $cs; ?>">
	 
	  <?php }else { ?>
	 <a target="_blank" href="<?php echo ADMIN_URL; ?>students/admissionsummaryacedmic2/int/<?php echo $rg; ?>/<?php echo $cs; ?>">
		  	    <?php } ?>
<?php echo $servicessrte2+$acedmic3+$sum; ?></a> 
  <?php }else{ ?>
		<?php $sum=$acedmic-$acedmic2;  echo $servicessrte2+$acedmic1+$acedmic3+$sum;?><br> 
	<span style='color: red;'>Active Students</span>
	<?php echo $servicessrte2+$acedmic3+$sum; ?>
	<?php } ?></b></td>
 
    
    </tr>  
    


  
                </tbody>
               
              </table> 
  </div>
