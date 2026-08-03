   <div style="clear: both;"></div>

   <div style="clear: both;"></div>

<div class="table-responsive">
   <table id="example1" class="table table-bordered table-striped">
     <thead>

       <tr>
         <td><a id="" style="position: absolute;
top: 122px;
/* right: 0px; */
right: 46px;" class="btn btn-info btn-sm pull-right"  target="_blank" href="<?php echo SITE_URL; ?>admin/report/officecollection"><i
               class="fa fa-file-pdf-o" aria-hidden="true"></i> Export PDF</a></td>
       </tr>

       <tr style="font-weight: bold;background-color: #ccc;">
         <td>#</td>
         <td>Area</td>
         <td colspan="3" style="text-align: center;">Today</td>


         <? foreach ($academic as $hh => $rg) {?>

         <td colspan="3" style="text-align: center;">
           <?echo $rg; ?>
         </td>
         <?}?>



       </tr>
     </thead>
     <tbody id="example22">

       <?php $page = $this->request->params['paging']['Services']['page'];
$limit = $this->request->params['paging']['Services']['perPage'];
$counter = ($page * $limit) - $limit + 1;

$acedmic = 0;
$acedmic1 = 0;

?>
       <tr style="font-weight: bold;font-size: 13px;">


         <td></td>
         <td></td>
         <td>CBSE</td>
         <td>INTERNATIONAL</td>
         <td>TOTAL</td>
         <?foreach ($academic as $hh => $rg) {?>
         <td>CBSE</td>
         <td>INTERNATIONAL</td>
         <td>TOTAL</td>
         <?}?>
       </tr>

       <tr>
         <td>1</td>
         <td>Prospectus Sale</td>



         <td>
           <?php $servicesdst = $this->Comman->findprospectusstudents2stoday('1');

if (isset($servicesdst)) {?> <a href="<?php echo ADMIN_URL; ?>students/summaryprospectus/cbse/1"
             target="_blank"><?php echo $servicesdst; ?></a> <?php } else {echo 'N/A';}?>
         </td>



         <td>&nbsp;
           <?php $servicesdst2 = $this->Comman->findprospectusstudents2stodayint('1');

if (isset($servicesdst2)) {?> <a href="<?php echo ADMIN_URL; ?>students/summaryprospectus/int/1"
             target="_blank"><?php echo $servicesdst2; ?> </a><?php } else {echo 'N/A';}?>
         </td>


         <td><b>
             <?php $servicessdst = $this->Comman->findprospectusstudents2stodaytotal();

if (isset($servicessdst)) {?> <a href="<?php echo ADMIN_URL; ?>students/summaryprospectus/total/1"
               target="_blank"><?php echo $servicessdst; ?></a> <?php } else {echo 'N/A';}?></b>
         </td>





         <? foreach ($academic as $hh => $rg) {?>
         <?php $tostd = '0';?>

         <td><?php $servicess = $this->Comman->findprospectusstudents2s($rg);
    $servicess = $servicess;
    if (isset($servicess)) { $tostd += $servicess;?>
           <a href="<?php echo ADMIN_URL; ?>students/summaryprospectusacedmic/cbse/<?php echo $rg; ?>" target="_blank">
             <?php echo $servicess; ?></a><?php
} else {echo 'N/A';}
    ?></td>

         <td><?php $servicessout = $this->Comman->findprospectusstudents2sout($rg);
    $servicessout = $servicessout;
    if (isset($servicessout)) {$tostd += $servicessout;
        ?>
           <a href="<?php echo ADMIN_URL; ?>students/summaryprospectusacedmic/int/<?php echo $rg; ?>" target="_blank">
             <?php echo $servicessout;} else {echo 'N/A';}
    ?></td>

         <td><?php
if (isset($tostd)) {
        ?>
           <a href="<?php echo ADMIN_URL; ?>students/summaryprospectusacedmic/total/<?php echo $rg; ?>" target="_blank">
             <?php
echo $tostd; ?></a> <?php } else {echo 'N/A';}
    ?></td>
         <?}?>

       </tr>

       <tr>

         <td>2</td>
         <td>Registration </td>


         <td>
           <?php $servicesdst3 = $this->Comman->findregistrationtudents2stoday('1');

if (isset($servicesdst3)) {?> <a href="<?php echo ADMIN_URL; ?>students/summaryregistration/cbse/1"
             target="_blank"><?php echo $servicesdst3; ?></a> <?php } else {echo 'N/A';}?>
         </td>

         <td>&nbsp;
           <?php $servicesdst23 = $this->Comman->findregistrationtudents2stodayint('1');

if (isset($servicesdst23)) {?> <a href="<?php echo ADMIN_URL; ?>students/summaryregistration/int/1"
             target="_blank"><?php echo $servicesdst23; ?></a> <?php } else {echo 'N/A';}?>
         </td>

         <td><b>
             <?php $servicessdst4 = $this->Comman->findregistrationtudents2stodaytotal();

if (isset($servicessdst4)) {?> <a href="<?php echo ADMIN_URL; ?>students/summaryregistration/total/1"
               target="_blank"><?php echo $servicessdst4; ?></a><?php } else {echo 'N/A';}?></b>
         </td>

         <?foreach ($academic as $hh => $rg) {?>
         <?php $tostd24 = '0';?>
         <td><?php $servsicsess = $this->Comman->findregistrationstudents2s($rg);
    $servicsesssj = $servsicsess;
    if (isset($servicsesssj)) {$tostd24 += $servicsesssj;?>
           <a href="<?php echo ADMIN_URL; ?>students/summaryregistrationacedmic/cbse/<?php echo $rg; ?>"
             target="_blank">
             <?php echo $servicsesssj; ?></a> <?php } else {echo 'N/A';}
    ?></td>
         <td><?php $servicessout = $this->Comman->findregistrationstudents2sout($rg);
    $servicsess = $servicessout;
    if (isset($servicsess)) {
        ?>
           <a href="<?php echo ADMIN_URL; ?>students/summaryregistrationacedmic/int/<?php echo $rg; ?>" target="_blank">
             <?php

        $tostd24 += $servicsess;
        echo $servicsess;?></a><?php } else {echo 'N/A';}
    ?></td>

         <td><?php
if (isset($tostd24)) {?>
           <a href="<?php echo ADMIN_URL; ?>students/summaryregistrationacedmic/total/<?php echo $rg; ?>"
             target="_blank">
             <?php
echo $tostd24; ?></a><?php } else {echo 'N/A';}
    ?></td>

         <?}?>

       </tr>

       <tr>

         <td>3</td>
         <td>Admission Total</td>

         <td>
           <?php $servicesdst34 = $this->Comman->findacedemicstudents2stoday('1');

if (isset($servicesdst34)) {?> <a href="<?php echo ADMIN_URL; ?>students/admissionsummary/cbse/1"
             target="_blank"><?php echo $servicesdst34; ?></a><?php } else {echo 'N/A';}?>
         </td>

         <td>&nbsp;
           <?php $servicesdst234 = $this->Comman->findacedemicstudents2stodayint('1');

if (isset($servicesdst234)) {?> <a href="<?php echo ADMIN_URL; ?>students/admissionsummary/int/1"
             target="_blank"><?php echo $servicesdst234; ?></a> <?php } else {echo 'N/A';}?>
         </td>

         <td><b>
             <?php $servicessdst44 = $this->Comman->findacedemicstudents2stodaytotal();

if (isset($servicessdst44)) {?> <a href="<?php echo ADMIN_URL; ?>students/admissionsummary/total/1"
               target="_blank"><?php echo $servicessdst44; ?></a> <?php } else {echo 'N/A';}?></b>
         </td>

         <?foreach ($academic as $hh => $rg) {?>

         <?php $tostd244 = '0';?>

         <td><?php $servicess1 = $this->Comman->findacedemicstudents2srtr($rg,1);
    $servicess21 = $this->Comman->findacedemicstudents21srtr($rg,1);
    $servicess1 = $servicess1 + $servicess21;
    if (isset($servicess1)) {$tostd244 += $servicess1;?>
           <a href="<?php echo ADMIN_URL; ?>students/admissionsummaryacedmic/cbse/<?php echo $rg; ?>" target="_blank">
             <?php echo $servicess1; ?> </a> <?php } else {echo 'N/A';}
    ?></td>


         <td><?php $servicess2 = $this->Comman->findacedemicstudents2srtrout($rg,1);
    $servicess22 = $this->Comman->findacedemicstudents21srtrout($rg,1);
    $servicess2 = $servicess2 + $servicess22;
    if (isset($servicess2)) {$tostd244 += $servicess2;
        ?>
           <a href="<?php echo ADMIN_URL; ?>students/admissionsummaryacedmic/int/<?php echo $rg; ?>" target="_blank">
             <?php echo $servicess2; ?> <?php } else {echo 'N/A';}
    ?></td>



         <td><?php
if (isset($tostd244)) {?>
           <a href="<?php echo ADMIN_URL; ?>students/admissionsummaryacedmic/total/<?php echo $rg; ?>" target="_blank">
             <?php echo $tostd244; ?> <?php } else {echo 'N/A';}
    ?></td>

         <?}?>

       </tr>

       <tr>

         <td>4</td>
         <td>Fee Collection (CASH)</td>


         <?php $totd = '0';
$totd2333 = '0';
$totd23331 = '0';
$totd2333123 = '0';?>


         <td>&nbsp;
           <?php $servicesssts = $this->Comman->findcollectiontudents2stoday('CASH');
$servicesssts2 = $this->Comman->findcollectiontudents2stodaydropp('CASH');

if ($servicesssts[0]['sum'] || $servicesssts2[0]['sum']) {$totd += $servicesssts[0]['sum'] + $servicesssts2[0]['sum'];
    $totd2333 += $servicesssts[0]['sum'] + $servicesssts2[0]['sum'];

    $rrrr = $servicesssts[0]['sum'] + $servicesssts2[0]['sum'];
    echo number_format($rrrr, 2);} else {echo 'N/A';}
?>
         </td>

         <td>&nbsp;
           <?php $servicessstsd = $this->Comman->findcollectiontudents2stodayout('CASH');
$servicessstsdtr3 = $this->Comman->findcollectiontudents2stodayoutdroppp('CASH');

if ($servicessstsd[0]['sum'] || $servicessstsdtr3[0]['sum']) {$totd += $servicessstsd[0]['sum'] + $servicessstsdtr3[0]['sum'];
    $totd23331 += $servicessstsd[0]['sum'] + $servicessstsdtr3[0]['sum'];

    $rrrrr3 = $servicessstsd[0]['sum'] + $servicessstsdtr3[0]['sum'];
    echo number_format($rrrrr3, 2);} else {echo 'N/A';}
?>
         </td>

         <td><b>
             <?php echo number_format($totd, 2); ?> </b>
         </td>
         <td></td>
         <td></td>
         <td></td>



       </tr>
       <tr>

         <td>5</td>
         <td>Fee Collection (OTHER MODE)</td>


         <?php $totds = '0';?>


         <td>&nbsp;
           <?php $servicessstsds = $this->Comman->findcollectiontudents2stoday2next('CASH');

$servicessstsds23s = $this->Comman->findcollectiontudents2stoday2droppenext('CASH');

if ($servicessstsds[0]['sum'] || $servicessstsds23s[0]['sum']) {$totds += $servicessstsds[0]['sum'] + $servicessstsds23s[0]['sum'];
    $totd2333 += $servicessstsds[0]['sum'] + $servicessstsds23s[0]['sum'];

    $othrrre = $servicessstsds[0]['sum'] + $servicessstsds23s[0]['sum'];
    echo number_format($othrrre, 2);} else {echo 'N/A';}
?>
         </td>

         <td>&nbsp;
           <?php $servicessstsdt = $this->Comman->findcollectiontudents2stoday2outnext('CASH');

$servicessstsdt43d = $this->Comman->findcollectiontudents2stoday2outdroppenext('CASH');

if ($servicessstsdt[0]['sum'] || $servicessstsdt43d[0]['sum']) {$totds += $servicessstsdt[0]['sum'] + $servicessstsdt43d[0]['sum'];

    $totd23331 += $servicessstsdt[0]['sum'] + $servicessstsdt43d[0]['sum'];
    $dropotherfffff = $servicessstsdt[0]['sum'] + $servicessstsdt43d[0]['sum'];

    echo number_format($dropotherfffff, 2);} else {echo 'N/A';}
?>
         </td>

         <td><b>
             <?php echo number_format($totds, 2); ?> </b>
         </td>
         <td></td>
         <td></td>
         <td></td>



       </tr>

<?php if($this->request->session()->read('Auth.User.db')=="sanskar"){ ?>
       <tr>

<td>6</td>
<td >Fee Collection <strong style="color:green;">(SMART HUB)</strong></td>

<?php $totdssm = '0';?>


<td>&nbsp;
  <?php $servicessstsds2 = $this->Comman->findcollectiontudents2stoday2next2('CASH');

$servicessstsds23s2 = $this->Comman->findcollectiontudents2stoday2droppenext2('CASH');

if ($servicessstsds2[0]['sum'] || $servicessstsds23s2[0]['sum']) { $totdssm += $servicessstsds2[0]['sum'] + $servicessstsds23s2[0]['sum'];
$totd2333 += $servicessstsds2[0]['sum'] + $servicessstsds23s2[0]['sum'];

$othrrre = $servicessstsds2[0]['sum'] + $servicessstsds23s2[0]['sum'];
echo number_format($othrrre, 2);} else {echo 'N/A';}
?>
</td>

<td>&nbsp;
  <?php $servicessstsdt2 = $this->Comman->findcollectiontudents2stoday2outnext2('CASH');

$servicessstsdt43d2 = $this->Comman->findcollectiontudents2stoday2outdroppenext2('CASH');

if ($servicessstsdt2[0]['sum'] || $servicessstsdt43d2[0]['sum']) { $totdssm += $servicessstsdt2[0]['sum'] + $servicessstsdt43d2[0]['sum'];

$totd23331 += $servicessstsdt2[0]['sum'] + $servicessstsdt43d2[0]['sum'];
$dropotherfffff = $servicessstsdt2[0]['sum'] + $servicessstsdt43d2[0]['sum'];

echo number_format($dropotherfffff, 2);} else {echo 'N/A';}
?>
</td>

<td><b>
    <?php echo number_format($totdssm, 2); ?> </b>
</td>
<td></td>
<td></td>
<td></td>




</tr>
<?php } ?>

       <tr>
         <td>7</td>
         <td>OTHER FEE COLLECTION(CASH)</td>
         <td>&nbsp;&nbsp;N/A</td>
         <td>&nbsp;&nbsp;N/A</td>
         <?php $ofcash = $this->comman->findofcash('CASH');?>
         <td><b><?php echo number_format($ofcash[0]['sum'], 2); ?></b></td>

       </tr>
       </tr>
       <tr>
         <td>8</td>
         <td>OTHER FEE COLLECTION(OTHER MODE)</td>
         <td>&nbsp;&nbsp;N/A</td>
         <td>&nbsp;&nbsp;N/A</td>
         <?php $ofnotcash = $this->comman->findofnotcash('CASH');?>
         <td><b><?php echo number_format($ofnotcash[0]['sum'], 2); ?></b></td>

       </tr>

       <tr style="font-weight: bold;
    background-color: #ccc;">


         <td colspan="2"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total :</td>


         <?php $totd2333123 = '0';?>


         <td>&nbsp;
           <b>
             <?php $totd2333123 += $totd2333;
echo number_format($totd2333, 2);?> </b>
         </td>

         <td>&nbsp;
           <b>
             <?php $totd2333123 += $totd23331;
echo number_format($totd23331, 2);?> </b>
         </td>



         <td><b>
             <?php $totd2333123 += $ofcash[0]['sum'] + $ofnotcash[0]['sum'];
echo number_format($totd2333123, 2);?> </b>
         </td>
         <td></td>
         <td></td>
         <td></td>



       </tr>















     </tbody>

   </table>
   </div>
