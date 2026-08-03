<div class="table-responsive">
  <table id="" class="table table-bordered table-striped">
<tbody>
   <tr>
   <td><a id="" style="position: absolute;
top: 122px;
/* right: 0px; */
right: 46px;" class="btn btn-info btn-sm pull-right" target="_blank" href="<?php echo ADMIN_URL ;?>report/user_supportivgenderinfo/<?php echo $acedmic; ?>"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Export PDF</a></td>
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
		$limit = $this->request->params['pagc_c_iding']['Services']['perPage'];
		$counter = ($page * $limit) - $limit + 1;
	   $total=0;
	   $totalfee=0;
	   $out=0;
	   $total_discount=0;
       $session = $this->request->session();
       $classmaletotal=0;
      $classfemaletotal=0;
       $session->delete('gender');
       $session->write('gender',$gender);

              $session->delete('class_id');
       $session->write('class_id',$class_id);





  $class=explode(',',$class_id);

  if($section_id){

	     $session->delete('section_id');
       $session->write('section_id',$section_id);
    $section=explode(',',$section_id);

}else{
	$section=array();
	   $session->delete('section_id');
}
		if(isset($class) && !empty($class) && !empty($section)){

				$rrt=array();


			$notinstud=$this->Comman->findgendercountawsdroparray($acedmic);



						foreach($notinstud as $ffg=>$ddd){
							if($ddd['s_id']!=''){
							$rrt[]=$ddd['s_id'];
						}
						}



			foreach($class as $key=>$element) {

			foreach($section as $keys=>$elements) {
						$classmalssssse=$this->Comman->findgendercountawsboth($element,$elements,$acedmic);
						  if($acedmic!=$acedmicyear && $next_academic_year!=$acedmic){
						$classmalssssse +=$this->Comman->findgendercountawsdrop("Male",$element,$elements,$acedmic);
						$classmalssssse +=$this->Comman->findgendercountawshistory("Male",$element,$elements,$acedmic,$rrt);
					}
				if($classmalssssse!='0' ){
		?>
				 <tr>
			     <td><?php echo $counter;?></td>
			   <td><?php $classssr=$this->Comman->findclass($element);
			   $sectionssss=$this->Comman->findsectionsss($elements);

			              echo $classssr['title']."-".$sectionssss['title'];
			     ?>    </td>

			              <?  $classtotal=0; if($gender=='Both'){ ?>
    <td>   <?

     $classmale=$this->Comman->findgendercountaws("Male",$element,$elements,$acedmic);

     if($acedmic!=$acedmicyear && $next_academic_year!=$acedmic){


     $classmale +=$this->Comman->findgendercountawsdrop("Male",$element,$elements,$acedmic);
     $classmale +=$this->Comman->findgendercountawshistory("Male",$element,$elements,$acedmic,$rrt);
 }


    echo $classmale;
     $classtotal +=$classmale;


     $classmaletotal +=$classmale; ?></td>
  <td>   <?

  $classfemale=$this->Comman->findgendercountaws("Female",$element,$elements,$acedmic);

   if($acedmic!=$acedmicyear && $next_academic_year!=$acedmic){
  $classfemale +=$this->Comman->findgendercountawsdrop("Female",$element,$elements,$acedmic);
  $classfemale +=$this->Comman->findgendercountawshistory("Female",$element,$elements,$acedmic,$rrt);
}
  echo $classfemale;

   $classtotal +=$classfemale;   $classfemaletotal +=$classfemale; ?></td>
  <td><? echo $classtotal; ?></td>


    <? }else if($gender=='Male'){
		 ?>
        <td>   <?
         $classmale=$this->Comman->findgendercountaws("Male",$element,$elements,$acedmic);


          if($acedmic!=$acedmicyear && $next_academic_year!=$acedmic){
         $classmale +=$this->Comman->findgendercountawsdrop("Male",$element,$elements,$acedmic);
         $classmale +=$this->Comman->findgendercountawshistory("Male",$element,$elements,$acedmic,$rrt);
	 }

        echo $classmale;

        $classtotal +=$classmale; $classmaletotal +=$classmale; ?></td>
         <td><? echo $classtotal; ?></td>



    <? }else if($gender=='Female'){ ?>
 <td>   <?
  $classfemale=$this->Comman->findgendercountaws("Female",$element,$elements,$acedmic);
   if($acedmic!=$acedmicyear && $next_academic_year!=$acedmic){
  $classfemale +=$this->Comman->findgendercountawsdrop("Female",$element,$elements,$acedmic);
  $classfemale +=$this->Comman->findgendercountawshistory("Female",$element,$elements,$acedmic,$rrt);
}

 echo  $classfemale;
  $classtotal +=$classfemale;  $classfemaletotal +=$classfemale; ?></td>
  <td><? echo $classtotal; ?></td>


    <? }  ?>



                          </tr>
		<?php $counter++;} } }

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
    <? }  ?>




		<?
	  }else if(isset($class) && !empty($class)){


		  $rrt=array();


			$notinstud=$this->Comman->findgendercountawsdroparray($acedmic);



						foreach($notinstud as $ffg=>$ddd){
							if($ddd['s_id']!=''){
							$rrt[]=$ddd['s_id'];
						}
						}


			foreach($class as $key=>$element) {


						$classmalssssse=$this->Comman->findgendercountwsawsbothh($element,$acedmic);
						  if($acedmic!=$acedmicyear && $next_academic_year!=$acedmic){
						$classmalssssse +=$this->Comman->findgendercountwsawsdrop("Male",$element,$acedmic);
						$classmalssssse +=$this->Comman->findgendercountwsawsdhistory("Male",$element,$acedmic,$rrt);

					}
				if($classmalssssse!='0' ){
		?>
				 <tr>
			     <td><?php echo $counter;?></td>
			   <td><?php $classssr=$this->Comman->findclass($element);


			              echo $classssr['title'];
			     ?>    </td>

			              <?  $classtotal=0; if($gender=='Both'){ ?>
    <td>   <?  $classmale=$this->Comman->findgendercountwsaws("Male",$element,$acedmic);
      if($acedmic!=$acedmicyear && $next_academic_year!=$acedmic){
               $classmale +=$this->Comman->findgendercountwsawsdrop("Male",$element,$acedmic);
	           $classmale +=$this->Comman->findgendercountwsawsdhistory("Male",$element,$acedmic,$rrt);
    }


    echo $classmale;
     $classtotal +=$classmale;


     $classmaletotal +=$classmale; ?></td>
  <td>   <?
  $classfemale=$this->Comman->findgendercountwsaws("Female",$element,$acedmic);
    if($acedmic!=$acedmicyear && $next_academic_year!=$acedmic){
  $classfemale +=$this->Comman->findgendercountwsawsdrop("Female",$element,$acedmic);
  $classfemale +=$this->Comman->findgendercountwsawsdhistory("Female",$element,$acedmic,$rrt);
}
  echo $classfemale;

   $classtotal +=$classfemale;   $classfemaletotal +=$classfemale; ?></td>
  <td><? echo $classtotal; ?></td>


    <? }else if($gender=='Male'){ ?>
        <td>   <?  $classmale=$this->Comman->findgendercountwsaws("Male",$element,$acedmic);
          if($acedmic!=$acedmicyear && $next_academic_year!=$acedmic){
         $classmale +=$this->Comman->findgendercountwsawsdrop("Male",$element,$acedmic);
	           $classmale +=$this->Comman->findgendercountwsawsdhistory("Male",$element,$acedmic,$rrt);

	}


        echo $classmale;
        $classtotal +=$classmale; $classmaletotal +=$classmale; ?></td>
         <td><? echo $classtotal; ?></td>



    <? }else if($gender=='Female'){ ?>
 <td>   <? $classfemale=$this->Comman->findgendercountwsaws("Female",$element,$acedmic);
   if($acedmic!=$acedmicyear && $next_academic_year!=$acedmic){
 $classfemale +=$this->Comman->findgendercountwsawsdrop("Female",$element,$acedmic);
  $classfemale +=$this->Comman->findgendercountwsawsdhistory("Female",$element,$acedmic,$rrt);

}
 echo  $classfemale;
 $classtotal +=$classfemale;  $classfemaletotal +=$classfemale; ?></td>
  <td><? echo $classtotal; ?></td>


    <? }  ?>



                          </tr>
		<?php $counter++;}  }

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
    <? }  ?>




		<?
	  } else { ?>

	  <td colspan="8" style="text-align:center;">No Data Available</td>

	  <?	} ?>

         </table>

      <div>



