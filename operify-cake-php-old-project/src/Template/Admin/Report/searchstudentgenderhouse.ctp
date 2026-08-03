
			<div class="table-responsive">
  <table id="" class="table table-bordered table-striped">
<tbody>


   <tr>
   <td><a id="" style="position: absolute;
top: 122px;

right: 46px;" class="btn btn-info btn-sm pull-right" target="_blank"
href="<?php echo ADMIN_URL ;?>report/studentgenderhousepdf2/<?php echo $acedmic; ?>"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Export PDF</a></td>
   </tr>


    <tr>
    <th>S.No</th>
    <th>Class Name</th>

    <? if($gender=='Both'){ ?>
    <th>Male</th>
    <th>Female</th>
    <? }else if($gender=='Male'){ ?>
      <th>Male</th>
    <? }else if($gender=='Female'){ ?>
     <th>Female</th>
    <? } ?>
    <th>Total</th>

    </tr>

  	<?php
      $page = $this->request->params['paging']['Services']['page'];
		$limit = $this->request->params['paging']['Services']['perPage'];
		$counter = ($page * $limit) - $limit + 1;
	   $total=0;
	   $totalfee=0;
	   $out=0;
	   $total_discount=0;
       $session = $this->request->session();
       $classmaletotal=0;
			$classfemaletotal=0;
			$notassmltotal=0;
      $notassfmltotal=0;
       $session->delete('gender');
       $session->write('gender',$gender);

       $session->delete('class_id');
       $session->write('class_id',$class_id);

              $session->delete('section_id');
       $session->write('section_id',$section_id);

         $class_id=explode(',',$class_id);
    $section_id=explode(',',$section_id);

		if($section_id[0]!='' && !empty($section_id)){



			foreach($class_id as $key=>$element) {



			foreach($section_id as $keys=>$elements) {
					$tyu=0;
					$tyu2=0;
					$tyu3=0;
						$classmalssssse=$this->Comman->findgendercountawsboth($element,$elements,$acedmic);


				if($classmalssssse!='0' ){
		?>
				 <tr>
			     <td><?php echo $counter;?></td>
			   <td><?php
			   $classssr=$this->Comman->findclass($element);
			   $sectionssss=$this->Comman->findsectionsss($elements);

			              echo "<b>".$classssr['title']."-".$sectionssss['title']."</b>";



			     ?><br>

			     <? 	foreach($houselist as $hkey=>$helement) {	?>

					 <table><tr>
						 <td>&nbsp;</td>
					 <td>&nbsp;</td>
					 <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td></td>
					<td><? echo $helement; ?></td>
					 </tr></table>
					<? }  ?>  </td>

										<?  $classtotal=0;
										$notassmale=0;
										$notassfemale=0;
										 if($gender=='Both'){   ?>
    <td> <br>
					  <?  foreach($houselist as $hkey=>$helement) { ?> <table><tr>
						 <td>&nbsp;</td>
					 <td>&nbsp;</td>
					 <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td></td><td><?

		 echo $classmale=$this->Comman->findgendercounthouseaws("Male",$element,$elements,$hkey,$acedmic);

     $classtotal +=$classmale;

     $tyu +=$classmale;
     $classmaletotal +=$classmale;

    ?></td></tr></table><? } ?> <table><tr><td><b>Total :-<? echo $tyu; ?></b></td></tr>
		<tr><td><b><span style="color:red">Total:-<?php
				echo $notassmale=$this->Comman->findnotassignedhouse("Male",$element,$elements,$hkey,$acedmic);
				 $notassmltotal+=$notassmale; ?></span></b></td></tr></table>
					  </td>

					<td> <br>

					 <?  foreach($houselist as $hkey=>$helement) { ?><table><tr>
						 <td>&nbsp;</td>
					 <td>&nbsp;</td>
					 <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td></td><td><?

		 echo $classfemale=$this->Comman->findgendercounthouseaws("Female",$element,$elements,$hkey,$acedmic);

     $classtotal +=$classfemale;
          $tyu2 +=$classfemale;

     $classfemaletotal +=$classfemale;

    ?></td></tr></table><? } ?><table><tr><td><b>Total :-<? echo $tyu2; ?></b></td></tr>
		<tr><td><b><span style="color:red">Total:-<?php
				echo $notassfemale=$this->Comman->findnotassignedhouse("Female",$element,$elements,$hkey,$acedmic);
				$notassfmltotal+=$notassfemale;?></span></b></td></tr></table>
				  </td>



    <? }else if($gender=='Male'){ ?>
       <td> <br>
					  <?  foreach($houselist as $hkey=>$helement) { ?> <table><tr>
						 <td>&nbsp;</td>
					 <td>&nbsp;</td>
					 <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td></td><td><?

		 echo $classmale=$this->Comman->findgendercounthouseaws("Male",$element,$elements,$hkey,$acedmic);

     $classtotal +=$classmale;
      $tyu +=$classmale;

     $classmaletotal +=$classmale;

    ?></td></tr></table><? } ?>
					  <table><tr><td><b>Total:-<?php echo $tyu; ?></b></td></tr><tr><td><b><span style="color:red">Total:-<?php
				echo $notassmale=$this->Comman->findnotassignedhouse("Male",$element,$elements,$hkey,$acedmic);

				$notassmltotal+=$notassmale;    ?></span></b></td></tr></table></td>



    <? }else if($gender=='Female'){ ?>

		<td> <br>
 <?  foreach($houselist as $hkey=>$helement) { ?><table><tr>
						 <td>&nbsp;</td>
					 <td>&nbsp;</td>
					 <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td></td><td><?

		 echo $classfemale=$this->Comman->findgendercounthouseaws("Female",$element,$elements,$hkey,$acedmic);

     $classtotal +=$classfemale;
     $tyu2 +=$classfemale;

     $classfemaletotal +=$classfemale;

   ?></td></tr></table><?  } ?>

				 <table><tr><td><b>Total:-<?php echo $tyu2; ?></b></td></tr><tr><td><b><span style="color:red">Total:-<?php
				echo $notassfemale=$this->Comman->findnotassignedhouse("Female",$element,$elements,$hkey,$acedmic);
				$notassfmltotal+=$notassfemale; ?></span></b></td></tr></table>

    <? }  ?> </td>


      <td><br>

			       <? 	if($gender=='Both'){  foreach($houselist as $hkey=>$helement) {	?>

					 <table><tr>
						 <td>&nbsp;</td>
					 <td>&nbsp;</td>
					 <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td></td>
					<td><?  echo $cla=$this->Comman->findgendercounthouse2aws($element,$elements,$hkey,$acedmic);  $tyu3+=$cla; $notasstotal=$notassfemale+$notassmale; ?></td>
					 </tr></table>
					<? } }else if($gender=='Male'){  foreach($houselist as $hkey=>$helement) {	?>

					 <table><tr>
						 <td>&nbsp;</td>
					 <td>&nbsp;</td>
					 <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td></td>
					<td><?  echo $cla2=$this->Comman->findgendercounthouseaws("Male",$element,$elements,$hkey,$acedmic);  $tyu3+=$cla2; $notasstotal=$notassmale;?></td>
					 </tr></table>
					<? } }else if($gender=='Female'){  foreach($houselist as $hkey=>$helement) {	?>

					 <table><tr>
						 <td>&nbsp;</td>
					 <td>&nbsp;</td>
					 <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td></td>
					<td><?  echo $cla3=$this->Comman->findgendercounthouseaws("Female",$element,$elements,$hkey,$acedmic); $tyu3+=$cla3; $notasstotal=$notassfemale; ?></td>
					 </tr></table>
					<? } }  ?> <table><tr><td><b>Total :-<? echo $tyu3; ?></b></td></tr><tr><td><b><span style="color:red">Total:-<?php
		    echo $notasstotal; ?></span></b></td></tr></table>    </td>


                          </tr>
		<?php $counter++;}  }  }

		?> <td colspan="2" style="text-align:center;"><b>Total</b></td>

		<?  if($gender=='Both'){ ?>
<td ><b><? echo $classmaletotal; ?></b></td>
	<td ><b><? echo $classfemaletotal; ?></b></td>
	<td ><b><? echo $classmaletotal+$classfemaletotal; ?></b></td>

    <? }else if($gender=='Male'){ ?>
<td ><b><? echo $classmaletotal; ?></b></td>
<td ><b><? echo $classmaletotal; ?></b></td>
    <? }else if($gender=='Female'){ ?>
 <td ><b><? echo $classfemaletotal; ?></b></td>
 <td ><b><? echo $classfemaletotal; ?></b></td>
		<? }  ?></tr>
		<tr style="color:red">
    <td colspan="2" style="text-align:center;"><b>Total Not Assigned</b></td>

		<?  if($gender=='Both'){ ?>

<td ><b><? echo $notassmltotal; ?></b></td>
	<td ><b><? echo $notassfmltotal; ?></b></td>
	<td ><b><? echo $notassmltotal+$notassfmltotal; ?></b></td>
    	<? }else if($gender=='Male'){ ?>
<td ><b><? echo $notassmltotal; ?></b></td>
<td ><b><? echo $notassmltotal; ?></b></td>
    <? }else if($gender=='Female'){ ?>
 <td ><b><? echo $notassfmltotal; ?></b></td>
 <td ><b><? echo $notassfmltotal; ?></b></td>
    <? }  ?> </tr>
	  <?php }else if(isset($class_id) && !empty($class_id)){



			foreach($class_id as $key=>$element) {




					$tyu=0;
					$tyu2=0;
					$tyu3=0;
						$classmalssssse=$this->Comman->findgendercountwsawsbothh($element,$acedmic);


				if($classmalssssse!='0' ){
		?>
				 <tr>
			     <td><?php echo $counter;?></td>
			   <td><?php
			   $classssr=$this->Comman->findclass($element);


			              echo "<b>".$classssr['title']."</b>";



			     ?><br>

			     <? 	foreach($houselist as $hkey=>$helement) {	?>

					 <table><tr>
						 <td>&nbsp;</td>
					 <td>&nbsp;</td>
					 <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td></td>
					<td><? echo $helement; ?></td>
					 </tr></table>
					<? }  ?>  </td>

			              <?  $classtotal=0; if($gender=='Both'){   ?>
    <td> <br>
					  <?  foreach($houselist as $hkey=>$helement) { ?> <table><tr>
						 <td>&nbsp;</td>
					 <td>&nbsp;</td>
					 <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td></td><td><?

		 echo $classmale=$this->Comman->findgendercounthousewcaws("Male",$element,$hkey,$acedmic);

     $classtotal +=$classmale;

     $tyu +=$classmale;
     $classmaletotal +=$classmale;

    ?></td></tr></table><? } ?> <table><tr><td><b>Total :-<? echo $tyu; ?></b></td></tr></table>
					  </td>

					<td> <br>

					 <?  foreach($houselist as $hkey=>$helement) { ?><table><tr>
						 <td>&nbsp;</td>
					 <td>&nbsp;</td>
					 <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td></td><td><?

		 echo $classfemale=$this->Comman->findgendercounthousewcaws("Female",$element,$hkey,$acedmic);

     $classtotal +=$classfemale;
          $tyu2 +=$classfemale;

     $classfemaletotal +=$classfemale;

    ?></td></tr></table><? } ?><table><tr><td><b>Total :-<? echo $tyu2; ?></b></td></tr></table>
				  </td>



    <? }else if($gender=='Male'){ ?>
       <td> <br>
					  <?  foreach($houselist as $hkey=>$helement) { ?> <table><tr>
						 <td>&nbsp;</td>
					 <td>&nbsp;</td>
					 <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td></td><td><?

		 echo $classmale=$this->Comman->findgendercounthousewcaws("Male",$element,$hkey,$acedmic);

     $classtotal +=$classmale;


     $classmaletotal +=$classmale;

    ?></td></tr></table><? } ?>
					  </td>


    <? }else if($gender=='Female'){ ?>

		<td> <br>
 <?  foreach($houselist as $hkey=>$helement) { ?><table><tr>
						 <td>&nbsp;</td>
					 <td>&nbsp;</td>
					 <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td></td><td><?

		 echo $classfemale=$this->Comman->findgendercounthousewcaws("Female",$element,$hkey,$acedmic);

     $classtotal +=$classfemale;


     $classfemaletotal +=$classfemale;

   ?></td></tr></table><?  } ?>
				  </td>


    <? }  ?>


      <td><br>

			          <? 	if($gender=='Both'){  foreach($houselist as $hkey=>$helement) {	?>

					 <table><tr>
						 <td>&nbsp;</td>
					 <td>&nbsp;</td>
					 <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td></td>
					<td><?  echo $cla=$this->Comman->findgendercounthouse2wcaws($element,$hkey,$acedmic);  $tyu3+=$cla; ?></td>
					 </tr></table>
					<? } }else if($gender=='Male'){  foreach($houselist as $hkey=>$helement) {	?>

					 <table><tr>
						 <td>&nbsp;</td>
					 <td>&nbsp;</td>
					 <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td></td>
					<td><?  echo $cla2=$this->Comman->findgendercounthousewcaws("Male",$element,$hkey,$acedmic);  $tyu3+=$cla2; ?></td>
					 </tr></table>
					<? } }else if($gender=='Female'){  foreach($houselist as $hkey=>$helement) {	?>

					 <table><tr>
						 <td>&nbsp;</td>
					 <td>&nbsp;</td>
					 <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td></td>
					<td><?  echo $cla3=$this->Comman->findgendercounthousewcaws("Female",$element,$hkey,$acedmic); $tyu3+=$cla3; ?></td>
					 </tr></table>
					<? } }  ?> <table><tr><td><b>Total :-<? echo $tyu3; ?></b></td></tr></table>   </td>


                          </tr>
		<?php $counter++;  }  }


		?>	<tr> <td colspan="2" style="text-align:center;"><b>Total</b></td>

		<?  if($gender=='Both'){ ?>
<td ><b><? echo $classmaletotal; ?></b></td>
	<td ><b><? echo $classfemaletotal; ?></b></td>
	<td ><b><? echo $classmaletotal+$classfemaletotal; ?></b></td>

    <? }else if($gender=='Male'){ ?>
<td ><b><? echo $classmaletotal; ?></b></td>
<td ><b><? echo $classmaletotal; ?></b></td>
    <? }else if($gender=='Female'){ ?>
 <td ><b><? echo $classfemaletotal; ?></b></td>
 <td ><b><? echo $classfemaletotal; ?></b></td>
    <? }  ?> </tr>
		<tr style="color:red">
    <td colspan="2" style="text-align:center;"><b>Total Not Assigned</b></td>

		<?  if($gender=='Both'){ ?>

<td ><b><? echo $notassmltotal; ?></b></td>
	<td ><b><? echo $notassfmltotal; ?></b></td>
	<td ><b><? echo $notassmltotal+$notassfmltotal; ?></b></td>
    	<? }else if($gender=='Male'){ ?>
<td ><b><? echo $notassmltotal; ?></b></td>
<td ><b><? echo $notassmltotal; ?></b></td>
    <? }else if($gender=='Female'){ ?>
 <td ><b><? echo $notassfmltotal; ?></b></td>
 <td ><b><? echo $notassfmltotal; ?></b></td>
    <? }  ?> </tr>




		<?
	   }else { ?>

	  <td colspan="8" style="text-align:center;">No Data Available</td>

	  <?	} ?>

         </table>

      <div>

