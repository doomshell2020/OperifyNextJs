<!-- Content Wrapper. Contains page content -->
  <style>
  .checkbox input[type="checkbox"]{
    opacity:1;
}
 </style>
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
   <h1>
            <i class="fa fa-check-square"></i> Student Attendance  Report        </h1>
                     <ol class="breadcrumb">
          		<li><a href="<?php echo ADMIN_URL;?>dashboards"><i class="fa fa-home"></i>Home</a></li>
<li><a href="<?php echo ADMIN_URL;?>report/sattendance">Manage Student Attendance Report </a></li>  
   </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          
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
        
              <script inline="1">	  
				  
//<![CDATA[
$(document).ready(function () {
  $("#sattendans").bind("submit", function (event) {
    $.ajax({
      async:true,
      data:$("#sattendans").serialize(),
      dataType:"html", 
      type:"POST", 
     url:"<?php echo ADMIN_URL ;?>report/search10",
      success:function (data) {
		//  alert(data); 
		  
	//	$("#updt").show();   
        $("#updt").html(data); }, 
  });
    return false;
});});
//]]>
</script>

  <?php echo $this->Form->create('Sattendance',array('type'=>'file','inputDefaults'=>array('div'=>false,'label'=>false),'class'=>'form-horizontal','id'=>'sattendans')); ?>

 <div class="form-group">
	     <div class="col-sm-4" >
		  <label for="inputEmail3" class="control-label">Select Class<span style="color:red;">*</span></label>
	    	<?php echo $this->Form->input('class_id',array('class'=>'form-control','id'=>'cls','required','type'=>'select','empty'=>'Select Class','required','value'=>$class_id,'options'=>$classes,'label' =>false)); ?>  
		          </div>      
	 
	 <div class="col-sm-4">
 <label for="inputEmail3" class="control-label">Section<span style="color:red;">*</span></label>
 <select class="form-control" name="section_id" id="section-id"  required> 


<?php if(sections){


		foreach($sections as $sections=>$value){

			echo "<option  value=".$sections.">".$value."</option>";
		} }else{ ?>
   <option value="">----Select Section----</option>
<?php } ?>
</select>
    </div>
	     <script> 
$(document).ready(function(){
$('#cls').on('change',function(){
var id = $('#cls').val();
 $.ajax({ 
        type: 'POST', 
        url: '<?php echo ADMIN_URL ;?>report/find_section',
        data: {'id':id}, 
        success: function(data){  
//alert(data);
 $('#section-id').empty();
  $('#section-id').html(data);

  
        }, 
        
    });  
});
});
</script>	          	           
 <div class="col-sm-4">	
  <label for="inputEmail3" class="control-label">Acedamic Year</label>
      <select class="form-control" name="acedmicyear" required>
  <option value="">Select Acedamic Year</option>
 <?= $year=date("Y");  $year2=$year-1; $exyear=$year+3; ?>

  <?php for ($i = $year2; $i <= $exyear; $i++) :  $rt=$i+1; $rt= substr($rt,2);$st=$i.'-'.$rt?> 
        <option <?php if($i==$year){ ?>selected <?php } ?> value="<?php echo $i; ?>-<?php echo  $rt;?>" <?php if($st==$acedmicyear){ echo "selected";  } ?> ><?php echo $i; ?>-<?php echo  $rt;?></option>
    <?php endfor; ?>
</select>
    </div>       		                         
    </div>
    
 
    
   <div class="form-group">  
   <input type="submit" style="background-color:#00c0ef;width:100px !important; margin-left: 10px;" class="btn btn4 btn_pdf myscl-btn date" value="Submit">  
   <button type="reset" style="background-color:#f4f4f4;width:100px !important; margin-left: 10px;" class="btn btn4 btn_pdf myscl-btn date">Reset</button>  
   </div>
  
  <?php 
echo $this->Form->end();
?>          
            <!-- /.box-header -->
<?php echo $this->Flash->render(); ?>
		</div>
		<div id="updt"> 

<?php if($mid){ ?>
<div class="table-responsive">
  <table id="" class="table table-bordered table-striped">
<tbody> 
   <tr>
   <td><a id="" style="position: absolute;
top: 161px;
/* right: 0px; */
right: 46px;" class="btn btn-info btn-sm pull-right" href="<?php echo ADMIN_URL ;?>report/user_supportiv10"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel</a></td>
   </tr>
   <tr>
   <td colspan="13" align="center">Total Working Days - <span style="color:#00c0ef">
<?php $years=explode('-',$acedmicyear); $year=$years[0]; 
 $tdays=0;
 $holicount=$this->Comman->findholicount('8',$year);
foreach($holicount as $key=>$value)
{
	//pr($value);
   $endDate = strtotime($value['endtime']);
 //  echo $endDate."<br>";
    $startDate = strtotime($value['starttime']);
  //echo $startDate;
      $days = ($endDate - $startDate) / 86400 + 1;
     $tdays+=number_format($days);    
}
 
   echo getWorkingDaysss($year."-04-01",$year+'1'."-04-01");
   

  ?></span>
 <br>
  <span style="color:red">Total Holidays Days -<?php echo $tdays; ?>
 
  
  </td>
   </tr>
    <tr>
    <th>#</th>
    <th>Student-ID</th>
    <th><a href="<?php echo SITE_URL; ?>admin/report/register/<?php echo $mid; ?>/4">April</a>(<?php //echo getWorkingDaysss($year."-04-01",$year."-04-30"); 
         $mholicount=$this->Comman->findmonthholicount('8','4',$year);
      $number_of_days=0;
            if(!empty($mholicount)){
foreach($mholicount as $keyed => $value)
{
$start_dated=date('Y-m-d',strtotime($value['starttime']));
$end_dated=date('Y-m-d',strtotime($value['endtime']));
$number_of_days = calculateHolidays($start_dated, $end_dated, $monthd);
  if($number_of_days){  echo getWorkingDaysss($year."-04-01",$year."-04-30")- $number_of_days;   }          
} 
}else{
	
echo getWorkingDaysss($year."-04-01",$year."-04-30");
	} 
    ?>)</th>
    <th><a href="<?php echo SITE_URL; ?>admin/report/register/<?php echo $mid; ?>/5">May</a>(<?php //echo getWorkingDaysss($year."-05-01",$year."-05-31");
    
       $mholicount=$this->Comman->findmonthholicount('8','5',$year);
      $number_of_days=0;
      if(!empty($mholicount)){
foreach($mholicount as $keyed => $value)
{
$start_dated=date('Y-m-d',strtotime($value['starttime']));
$end_dated=date('Y-m-d',strtotime($value['endtime']));
$number_of_days = calculateHolidays($start_dated, $end_dated, $monthd);
     if($number_of_days){   echo getWorkingDaysss($year."-05-01",$year."-05-31")- $number_of_days;      }  
} 
    }else{
	
echo getWorkingDaysss($year."-05-01",$year."-05-31");
	} 
    
    ?>)</th>
    <th><a href="<?php echo SITE_URL; ?>admin/report/register/<?php echo $mid; ?>/6">June</a>(<?php //echo getWorkingDaysss($year."-06-01",$year."-06-30");
       $mholicount=$this->Comman->findmonthholicount('8','6',$year);
      $number_of_days=0;
       if(!empty($mholicount)){
foreach($mholicount as $keyed => $value)
{
$start_dated=date('Y-m-d',strtotime($value['starttime']));
$end_dated=date('Y-m-d',strtotime($value['endtime']));
$number_of_days = calculateHolidays($start_dated, $end_dated, $monthd);
  if($number_of_days){ echo getWorkingDaysss($year."-06-01",$year."-06-30")- $number_of_days; }             
} 
   }else{
	
echo getWorkingDaysss($year."-06-01",$year."-06-30");
	} 
    
    ?>)</th>
    <th><a href="<?php echo SITE_URL; ?>admin/report/register/<?php echo $mid; ?>/7">July</a>(<?php //echo getWorkingDaysss($year."-07-01",$year."-07-31");
    
    $mholicount=$this->Comman->findmonthholicount('8','7',$year);
      $number_of_days=0; $monthd='07'; //pr($mholicount);
      
      
      if(!empty($mholicount)){
foreach($mholicount as $keyed => $value)
{
$start_dated=date('Y-m-d',strtotime($value['starttime']));
$end_dated=date('Y-m-d',strtotime($value['endtime']));
$number_of_days = calculateHolidays($start_dated, $end_dated, $monthd); 
      if($number_of_days){ echo getWorkingDaysss($year."-07-01",$year."-07-31")- $number_of_days;  }       
}  
}else{
	
	 echo getWorkingDaysss($year."-07-01",$year."-07-31");
	}
    
    
    ?>)</th>
    <th><a href="<?php echo SITE_URL; ?>admin/report/register/<?php echo $mid; ?>/8">August</a>(<?php //echo getWorkingDaysss($year."-08-01",$year."-08-31");
        $mholicount=$this->Comman->findmonthholicount('8','8',$year);
      $number_of_days=0;
          if(!empty($mholicount)){
foreach($mholicount as $keyed => $value)
{
$start_dated=date('Y-m-d',strtotime($value['starttime']));
$end_dated=date('Y-m-d',strtotime($value['endtime']));
$number_of_days = calculateHolidays($start_dated, $end_dated, $monthd);
       if($number_of_days){    echo getWorkingDaysss($year."-08-01",$year."-08-31")- $number_of_days; }        
}
 }else{
	
	 echo getWorkingDaysss($year."-08-01",$year."-08-31");
	}
    
    
    
    ?>)</th>
    <th><a href="<?php echo SITE_URL; ?>admin/report/register/<?php echo $mid; ?>/9">September</a>(<?php //echo getWorkingDaysss($year."-09-01",$year."-09-30");
    
         $mholicount=$this->Comman->findmonthholicount('8','9',$year);
      $number_of_days=0;
      if(!empty($mholicount)){
foreach($mholicount as $keyed => $value)
{
$start_dated=date('Y-m-d',strtotime($value['starttime']));
$end_dated=date('Y-m-d',strtotime($value['endtime']));
$number_of_days = calculateHolidays($start_dated, $end_dated, $monthd);
          if($number_of_days){   echo getWorkingDaysss($year."-09-01",$year."-09-30")- $number_of_days; }        
} }else{
	
	 echo getWorkingDaysss($year."-09-01",$year."-09-30");
	} 
    
    
    
    ?>)</th>
    <th><a href="<?php echo SITE_URL; ?>admin/report/register/<?php echo $mid; ?>/10">October</a>(<?php //echo getWorkingDaysss($year."-10-01",$year."-10-31");  
    
     $mholicount=$this->Comman->findmonthholicount('8','10',$year);
      $number_of_days=0;
          if(!empty($mholicount)){
foreach($mholicount as $keyed => $value)
{
$start_dated=date('Y-m-d',strtotime($value['starttime']));
$end_dated=date('Y-m-d',strtotime($value['endtime']));
$number_of_days = calculateHolidays($start_dated, $end_dated, $monthd);
          if($number_of_days){ echo getWorkingDaysss($year."-10-01",$year."-10-31")- $number_of_days;  }       
} 
}else{
	
	 echo getWorkingDaysss($year."-10-01",$year."-10-31");
	} 
    
    
    ?>)</th>
    <th><a href="<?php echo SITE_URL; ?>admin/report/register/<?php echo $mid; ?>/11">November</a>(<?php //echo getWorkingDaysss($year."-11-01",$year."-11-30");  
    
      $mholicount=$this->Comman->findmonthholicount('8','11',$year);
      $number_of_days=0;
         if(!empty($mholicount)){
foreach($mholicount as $keyed => $value)
{
$start_dated=date('Y-m-d',strtotime($value['starttime']));
$end_dated=date('Y-m-d',strtotime($value['endtime']));
$number_of_days = calculateHolidays($start_dated, $end_dated, $monthd);
        if($number_of_days){  echo getWorkingDaysss($year."-11-01",$year."-11-30")- $number_of_days; }        
} 
}else{
	
	 echo getWorkingDaysss($year."-11-01",$year."-11-30");
	} 
    
    
    
    ?>)</th>
    <th><a href="<?php echo SITE_URL; ?>admin/report/register/<?php echo $mid; ?>/12">December</a>(<?php //echo getWorkingDaysss($year."-12-01",$year."-12-31");  
      $mholicount=$this->Comman->findmonthholicount('8','12',$year);
      $number_of_days=0;
           if(!empty($mholicount)){
foreach($mholicount as $keyed => $value)
{
$start_dated=date('Y-m-d',strtotime($value['starttime']));
$end_dated=date('Y-m-d',strtotime($value['endtime']));
$number_of_days = calculateHolidays($start_dated, $end_dated, $monthd);
           if($number_of_days){   echo getWorkingDaysss($year."-12-01",$year."-12-31")- $number_of_days; }   
} 
}else{
	
	 echo getWorkingDaysss($year."-12-01",$year."-12-31");
	} 
    
    ?>)</th>
    <th><a href="<?php echo SITE_URL; ?>admin/report/register/<?php echo $mid; ?>/1">January</a>(<?php //echo getWorkingDaysss($year+'1'."-01-01",$year+'1'."-01-31");
    
    
    $mholicount=$this->Comman->findmonthholicount('8','1',$year+1);
     $number_of_days=0;
      if(!empty($mholicount)){
foreach($mholicount as $keyed => $value)
{
$start_dated=date('Y-m-d',strtotime($value['starttime']));
$end_dated=date('Y-m-d',strtotime($value['endtime']));
$number_of_days = calculateHolidays($start_dated, $end_dated, $monthd);
   if($number_of_days){    echo getWorkingDaysss($year+'1'."-01-01",$year+'1'."-01-31")- $number_of_days; }         
} 
  }else{
	
	 echo getWorkingDaysss($year+'1'."-01-01",$year+'1'."-01-31");
	}   
    
    
    
    ?>)</th>
    <th><a href="<?php echo SITE_URL; ?>admin/report/register/<?php echo $mid; ?>/2">February</a>(<?php if( (0 == $year+1 % 4) and (0 != $year+1 % 100) or (0 == $year+1 % 400) )
		{ //echo getWorkingDaysss($year+'1'."-02-01",$year+'1'."-02-29");   
		  
    $mholicount=$this->Comman->findmonthholicount('8','2',$year+1);  $number_of_days=0;
      if(!empty($mholicount)){
foreach($mholicount as $keyed => $value)
{
$start_dated=date('Y-m-d',strtotime($value['starttime']));
$end_dated=date('Y-m-d',strtotime($value['endtime']));
$number_of_days = calculateHolidays($start_dated, $end_dated, $monthd);
           		   if($number_of_days){  echo getWorkingDaysss($year+'1'."-02-01",$year+'1'."-02-29")- $number_of_days;   }
		} 
		
		}else{
	
	 echo getWorkingDaysss($year+'1'."-02-01",$year+'1'."-02-29");
	}   
		
		
		

		 }else {  //echo getWorkingDaysss($year+'1'."-02-01",$year+'1'."-02-28");   
		      if(!empty($mholicount)){
		 $mholicount=$this->Comman->findmonthholicount('8','2',$year+1);  $number_of_days=0;
foreach($mholicount as $keyed => $value)
{
$start_dated=date('Y-m-d',strtotime($value['starttime']));
$end_dated=date('Y-m-d',strtotime($value['endtime']));
$number_of_days = calculateHolidays($start_dated, $end_dated, $monthd);
           if($number_of_days){   echo getWorkingDaysss($year+'1'."-02-01",$year+'1'."-02-28")- $number_of_days;   }
		
		  }  
		  }else{
	
echo getWorkingDaysss($year+'1'."-02-01",$year+'1'."-02-28")- $number_of_days;
	}   
		  
		   } ?>)</th>
    <th><a href="<?php echo SITE_URL; ?>admin/report/register/<?php echo $mid; ?>/3">March</a>(<?php //$w=getWorkingDaysss($year+'1'."-03-01",$year+'1'."-03-31");
    
    $mholicount=$this->Comman->findmonthholicount('8','3',$year+1);  $number_of_days=0;
    	      if(!empty($mholicount)){
foreach($mholicount as $keyed => $value)
{
$start_dated=date('Y-m-d',strtotime($value['starttime']));
$end_dated=date('Y-m-d',strtotime($value['endtime']));
$number_of_days = calculateHolidays($start_dated, $end_dated, $monthd);
            if($number_of_days){   echo getWorkingDaysss($year+'1'."-03-01",$year+'1'."-03-31")- $number_of_days;   }
} 
  }else{
	
 echo getWorkingDaysss($year+'1'."-03-01",$year+'1'."-03-31");
	} 
    
    ?>)</th>
    <th>Total(<?php echo getWorkingDaysss($year."-04-01",$year+'1'."-04-01")-$tdays;?>)</th>
    </tr>

  	
  	<?php 
  	   $session = $this->request->session();
       $session->delete($attend); 
	   $session->write('attend',$attend);
        $page = $this->request->params['paging']['Services']['page'];
		$limit = $this->request->params['pagc_c_iding']['Services']['perPage'];
		$counter = ($page * $limit) - $limit + 1; 
		if(isset($attend) && !empty($attend)){
		foreach($attend as $service){   if(!empty($service['id'])&& isset($service['id'])){ 	$total=0;  ?>
				 <tr>
			     <td><?php echo $counter;?></td>	
			      <td><?php echo $service['id']; ?></td>       	 	
			      <td><?php echo $totalatt=$this->Comman->findcount($service['id'],04,$service['acedmicyear']); 
			                  	$total+=$totalatt;
			      ?></td>  
			      <td><?php echo $totalatt=$this->Comman->findcount($service['id'],05,$service['acedmicyear']);
			       	$total+=$totalatt;
			       ?></td>  
			      <td><?php echo $totalatt=$this->Comman->findcount($service['id'],06,$service['acedmicyear']); 
			       	$total+=$totalatt;
			      ?></td>  
			      <td><?php echo $totalatt=$this->Comman->findcount($service['id'],07,$service['acedmicyear']); 
			       	$total+=$totalatt;
			      ?></td>  
			      <td><?php echo $totalatt=$this->Comman->findcount($service['id'],08,$service['acedmicyear']); 
			       	$total+=$totalatt;
			      ?></td>  
			      <td><?php echo $totalatt=$this->Comman->findcount($service['id'],09,$service['acedmicyear']); 
			       	$total+=$totalatt;
			      ?></td>  
			      <td><?php echo $totalatt=$this->Comman->findcount($service['id'],10,$service['acedmicyear']);
			       	$total+=$totalatt;
			       ?></td>  
			      <td><?php echo $totalatt=$this->Comman->findcount($service['id'],11,$service['acedmicyear']); 
			       	$total+=$totalatt;
			      ?></td>  
			      <td><?php echo $totalatt=$this->Comman->findcount($service['id'],12,$service['acedmicyear']);
			       	$total+=$totalatt;
			      ?></td>  
			      <td><?php echo $totalatt=$this->Comman->findcount($service['id'],01,$service['acedmicyear']);
			       	$total+=$totalatt;
			       ?></td>  
			      <td><?php echo $totalatt=$this->Comman->findcount($service['id'],02,$service['acedmicyear']);
			       	$total+=$totalatt;
			       ?></td>  
			      <td><?php echo $totalatt=$this->Comman->findcount($service['id'],03,$service['acedmicyear']); 
			       	$total+=$totalatt;
			      ?></td>  
			      <td><?php echo $total ;?></td>
                          </tr>
		<?php $counter++;} } } else {?>
		<td colspan="7" align="center">No Attendence Available</td></tr>
                    <?php } 

                    
                    ?>
         </table>      
      
      <div>      
    
<?php 
}


ini_set("display_errors","on");
error_reporting(E_ALL);
function getWorkingDaysss($startDate, $endDate)
{
    $begin = strtotime($startDate);
    $end   = strtotime($endDate);
    if ($begin > $end) {
        echo "startdate is in the future! <br />";

        return 0;
    } else {
        $no_days  = 0;
        $weekends = 0;
        while ($begin <= $end) {
            $no_days++; // no of days in the given interval
            $what_day = date("N", $begin);
            if ($what_day > 6) { // 6 and 7 are weekend days
                $weekends++;
            };
            $begin += 86400; // +1 day
        };
        $working_days = $no_days - $weekends;

        return $working_days;
    }
}
function calculateHolidays($start_date, $end_date, $month)
{
    $total_days = 0;
    $start_num_days = 0;
    $start_num_days = 0;
    $start_str = strtotime($start_date);
    $end_str = strtotime($end_date);
    
    $start_year = date('Y',$start_str);
    $end_year = date('Y',$end_str); 
    $start_month = date('m',$start_str); 
    $end_month = date('m',$end_str);
    $start_day = date('d',$start_str);
    $end_day = date('d',$end_str);
    
    $start_num_days_month = cal_days_in_month(CAL_GREGORIAN, $start_month, $start_year);
    $end_num_days_month = cal_days_in_month(CAL_GREGORIAN, $end_month, $end_year);

    if($start_month!=$end_month)
    {
		if($start_month==$month)
		{
		   $end_date = $start_year.'-'.$start_month.'-'.$start_num_days_month;
		   $sundays = findSundays($start_date, $end_date);
		   $total_days = ($start_num_days_month - $start_day)-$sundays; 
		}
		if($start_month < $month)
		{
		   $start_date = $start_year.'-'.$end_month.'-01';
		   $sundays = findSundays($start_date, $end_date);
		   $total_days = ($end_day - 0)-$sundays;
		}
	}
	else
	{
		   $days_str = $end_str - $start_str;
		   $days = number_format(($days_str) / 86400 + 1);
		$sundays = findSundays($start_date, $end_date);
		  $total_days = $days-$sundays;
	}	 
	 
	//echo $total_days;
    //$total_days = $start_num_days+$end_num_days; 
    return $total_days;
}
function findSundays($start_date, $end_date)
{
    //echo $start_date.'=>'.$end_date;
    $startDate = new DateTime($start_date);
    $endDate = new DateTime($end_date);
    $sundays = array();
    while ($startDate <= $endDate) {
        if ($startDate->format('w') == 0) {
            $sundays[] = $startDate->format('Y-m-d');
        }
        $startDate->modify('+1 day');
    }
    return count($sundays);
}

                  

?>


 
</div>

