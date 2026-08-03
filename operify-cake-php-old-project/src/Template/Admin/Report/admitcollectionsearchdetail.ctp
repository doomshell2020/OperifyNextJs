   <div style="clear: both;"></div>

   <div style="clear: both;"></div>

<div class="table-responsive">
   <table id="example1" class="table table-bordered table-striped">
     <thead>
       <tr>
         <td><a id="" style="position: absolute;
top: 85px;
 /* right: 0px;  */
right: 46px;" class="btn btn-info btn-sm pull-right"
             href="<?php echo SITE_URL; ?>admin/report/officecollection_excel/<?php echo $academic; ?>"><i
               class="fa fa-file-excel-o" aria-hidden="true"></i> Export Excel</a></td>
       </tr>



       <tr style="font-weight: bold;background-color: #ccc;">

         <td>#</td>
         <td style="
    width: 8%;
">Date</td>
         <td colspan="5" style="text-align: center;">CBSE <?php echo $academic; ?></td>
         <td colspan="5" style="text-align: center;">International <?php echo $academic; ?></td>
         <td colspan="5" style="text-align: center;">Other Fees <?php echo $academic; ?></td>





       </tr>
     </thead>
     <tbody id="example22">

       <?php $page = $this->request->params['paging']['Services']['page'];
    $limit = $this->request->params['paging']['Services']['perPage'];
    $counter = ($page * $limit) - $limit + 1;
     
		$acedmic=0;
		$acedmic1=0;

    ?>
       <tr style="font-weight: bold;font-size: 13px;">

         <td></td>
         <td></td>
         <td>Prospectus Sale</td>
         <td>Registration Sale </td>
         <td>Admission</td>
         <td>Fees Cash</td>
         <td>Fees Others Mode</td>

         <td>Prospectus Sale</td>
         <td>Registration Sale </td>
         <td>Admission</td>
         <td>Fees Cash</td>
         <td>Fees Others Mode</td>

         <td>Fees Cash</td>
         <td>Fees Others Mode</td>

       </tr>

       <?php 
                   
$date_from = strtotime($datefrom); // Convert date to a UNIX timestamp  
  
// Specify the end date. This date can be any English textual format  

$date_to = strtotime($dateto2); // Convert date to a UNIX timestamp  
  $cnt=1;
// Loop from the start date to end date and output all dates inbetween  
$prospecttotalcbse=0;
$registrationtotalcbse=0;
$admissionntotalcbse=0;
$feecashtotalcbse=0;
$feeotherstotalcbse=0;

$prospecttotalinter=0;
$registrationtotalinter=0;
$admissionntotalinter=0;
$feecashtotalinter=0;
$feeotherstotalinter=0;
$ofcashtotal=0;
$ofnotcashtotal=0;
$ofcashdaily=0;
$ofnotcashdaily=0;


for ($i=$date_from; $i<=$date_to; $i+=86400) {
	  ?>
       <tr>
         <td><?php echo $cnt++; ?></td>
         <td><?php echo date("d-m-Y", $i).'</td>';   ?>
         <td><a href="<?php echo ADMIN_URL;?>students/summaryprospectusreport/cbse/1/<?php echo date("Y-m-d", $i); ?>" ,
             target="_blank"><?php 

   $prospectcbse=$this->Comman->findprospectusstudents2stodaydetail2r('1',$academic,date("Y-m-d", $i)); echo $prospectcbse; 
    $prospecttotalcbse+=$prospectcbse; ?></a></td>
         <td><a href="<?php echo ADMIN_URL;?>students/findregistrationtudentsdetail/1/<?php echo date("Y-m-d", $i); ?>"
             ,
             target="_blank"><?php $registrationcbse=$this->Comman->findregistrationtudents2stodaydetail2r('1',$academic,date("Y-m-d", $i)); echo $registrationcbse; $registrationtotalcbse+=$registrationcbse;  ?></a>
         </td>


         <td><a
             href="<?php echo ADMIN_URL;?>students/findacedemicstudentsdetail/cbse/<?php if($academic){ echo $academic; }else{  echo 1; } ?>/<?php echo date("Y-m-d", $i); ?>"
             , target="_blank"><?php                    
                   $admissioncbse=$this->Comman->findacedemicstudents2stodaydetailhis('1',$academic,date("Y-m-d", $i)); 
                    $dropoutcbse = $this->Comman->findacedemicstudents2stodayoutdrop('1',$academic, date("Y-m-d", $i));
                   
                   echo $admissioncbse + $dropoutcbse;
                   $admissionntotalcbse+=$admissioncbse + $dropoutcbse; ?></a></td>

         <td><?php $feecashcbse=$this->Comman->findcollectiontudents2stodaydetail2r('CASH',$academic,date("Y-m-d", $i));
                        
                          $feecashcbse2=$this->Comman->findcollectiontudents2stodaydetaildroppp2r('CASH',$academic,date("Y-m-d", $i)); if($feecashcbse[0]['sum'] || $feecashcbse2[0]['sum'] ){ $dd=$feecashcbse[0]['sum']+$feecashcbse2[0]['sum']; 
							  echo $dd;
							  
							  $feecashtotalcbse+=$feecashcbse[0]['sum']+$feecashcbse2[0]['sum']; }else{ echo "0"; }   ?></td>

         <td><?php $feeotherscbse=$this->Comman->findcollectiontudents2stoday2detail2r('CASH',$academic,date("Y-m-d", $i)); $feeotherscbse2s=$this->Comman->findcollectiontudents2stoday2detaildropp2r('CASH',$academic,date("Y-m-d", $i)); 
                           
                           if($feeotherscbse[0]['sum']  || $feeotherscbse2s[0]['sum']){ $rrt=$feeotherscbse[0]['sum']+$feeotherscbse2s[0]['sum']; echo $rrt; $feeotherstotalcbse+=$rrt; }else{ echo "0"; }
                         ?></td>

         <td><a href="<?php echo ADMIN_URL;?>students/summaryprospectusreport/int/1/<?php echo date("Y-m-d", $i); ?>" ,
             target="_blank"><?php $prospectusinter=$this->Comman->findprospectusstudents2stodaydetailint2r('1',$academic,date("Y-m-d", $i)); echo $prospectusinter;   $prospecttotalinter+=$prospectusinter; ?></a>
         </td>
         <td><a href="<?php echo ADMIN_URL;?>students/findregistrationtudentsdetail/2/<?php echo date("Y-m-d", $i); ?>"
             ,
             target="_blank"><?php $registrationinter=$this->Comman->findregistrationtudents2stodaydetailint2r('1',$academic,date("Y-m-d", $i)); echo $registrationinter;  $registrationtotalinter+=$registrationinter; ?></a>
         </td>

         <td><a
             href="<?php echo ADMIN_URL;?>students/findacedemicstudentsdetail/int/<?php if($academic){ echo $academic; }else{  echo 1; } ?>/<?php echo date("Y-m-d", $i); ?>"
             , target="_blank"><?php $admissioninter=$this->Comman->findacedemicstudents2stodayintdetailhis('1',$academic,date("Y-m-d", $i)); 
                     $dropoutinter = $this->Comman->findacedemicstudents2stodayoutintdetaildrop('1', $academic,date("Y-m-d", $i));
                     echo $admissioninter + $dropoutinter;
                      $admissionntotalinter+=$admissioninter + $dropoutinter; ?></a></td>


         <td><?php $feecashint=$this->Comman->findcollectiontudents2stodayoutdetail2r('CASH',$academic,date("Y-m-d", $i));
                  $feecashint2=$this->Comman->findcollectiontudents2stodayoutdetaildropp2r('CASH',date("Y-m-d", $i));
                    if($feecashint[0]['sum']  || $feecashint2[0]['sum']){ $rrtd=$feecashint[0]['sum']+$feecashint2[0]['sum']; echo $rrtd; $feecashtotalinter+=$rrtd; }else{ echo "0"; }
           ?></td>
         <td><?php $feeothersint=$this->Comman->findcollectiontudents2stoday2outdetail2r('CASH',date("Y-m-d", $i)); $feeothersint2s=$this->Comman->findcollectiontudents2stoday2outdetaildropp2r('CASH',date("Y-m-d", $i));
                           
                           
                             if($feeothersint[0]['sum'] || $feeothersint2s[0]['sum']){ $rrrrr= $feeothersint[0]['sum']+$feeothersint2s[0]['sum']; $feeotherstotalinter+=$rrrrr; }else{ echo "0"; }
                          ?></td>

         <td>
           <?php $ofcashdaily=$this->Comman->findofcashdaily('CASH',date("Y-m-d", $i)); if($ofcashdaily[0]['sum']){ echo $ofcashdaily[0]['sum']; $ofcashtotal+=$ofcashdaily[0]['sum'];}else{ echo "0"; }?>
         </td>
         <td>
           <?php $ofnotcashdaily=$this->Comman->findofnotcashdaily('CASH',date("Y-m-d", $i)); if($ofnotcashdaily[0]['sum']){ echo $ofnotcashdaily[0]['sum']; $ofnotcashtotal+=$ofnotcashdaily[0]['sum']; }else{ echo "0"; }?>
         </td>


       </tr>

       <?php	} ?>

       <tr style="font-weight: bold;font-size: 18px;background-color: #ccc;">


         <td colspan="2">Total :</td>
         <td><?php echo $prospecttotalcbse; ?></td>
         <td><?php echo $registrationtotalcbse; ?></td>
         <td><?php echo $admissionntotalcbse; ?></td>
         <td><?php echo number_format($feecashtotalcbse,2); ?></td>
         <td><?php echo number_format($feeotherstotalcbse,2); ?></td>

         <td><?php echo $prospecttotalinter; ?></td>
         <td><?php echo $registrationtotalinter; ?></td>
         <td><?php echo $admissionntotalinter; ?></td>
         <td><?php echo number_format($feecashtotalinter,2); ?></td>
         <td><?php echo number_format($feeotherstotalinter,2); ?></td>
         <td><?php echo number_format($ofcashtotal,2); ?></td>
         <td><?php echo number_format($ofnotcashtotal,2); ?></td>

       </tr>


     </tbody>

   </table>
                          </div>