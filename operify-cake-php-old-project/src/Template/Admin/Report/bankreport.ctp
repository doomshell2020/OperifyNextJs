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
       Bank Report Quarter wise

      </h1>

    </section>
    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
             <style>
          #loader2 {
    display: none;
    position: absolute;
    top: 1%;
    left: 45%;
    width: 200px;
    height: 200px;
    padding:30px 15px 0px;
    border: 3px solid #ababab;
    box-shadow:1px 1px 10px #ababab;
    border-radius:20px;
    background-color: white;
    z-index: 1002;
    text-align:center;
    overflow: auto;
}

          </style>

 <div id="loader2" >
  <img src="<?php echo SITE_URL; ?>img/loading-gif-loader-v4.gif" class="img-responsive" />
</div>
   <div class="box">
    <div class="box-header">


       <script inline="1">
//<![CDATA[
$(document).ready(function () {
  $("#feesexl").bind("submit", function (event) {
    $.ajax({
      async:true,
      data:$("#feesexl").serialize(),
      dataType:"html",
      type:"POST",
     url:"<?php echo ADMIN_URL ;?>report/bankdefaultersearch",
       beforeSend: function(){

 $("#loader2").show();
   },
      success:function (data) {

        $("#updt").html(data);
           $("#loader2").css("display","none");
             },
         complete:function(data){
    // Hide image container
    $("#loader2").css("display","none");

   },
  });
    return false;
});
});
//]]>
</script>
     <?php echo $this->Form->create('Fees',array('type'=>'file','inputDefaults'=>array('div'=>false,'label'=>false),'id'=>'feesexl','class'=>'form-horizontal')); ?>

 <div class="form-group mar_btminner">

 <div class="col-sm-4 col-xs-6" >

           <label for="inputEmail3" class="control-label">Select Academic</label>

             <select name="acedmicyear" class="form-control" id="acedmicyear">
             <option value="<?php echo $academicyear; ?>"><?php echo $academicyear; ?></option>
       </select>
                   </div>



     <div class="col-sm-4 col-xs-6" >
		  <label for="inputEmail3" class="control-label">Select Class</label>
	    	<?php echo $this->Form->input('class_id',array('class'=>'form-control','id'=>'subjt','type'=>'select','empty'=>'Select Class','options'=>$classes,'label' =>false)); ?>
		          </div>

	     <div class="col-sm-4 col-xs-6" >

		  <label for="inputEmail3" class="control-label">Select Section</label>
	    	<?php echo $this->Form->input('section_id',array('class'=>'form-control','options'=>$sectionslist,'id'=>'','empty'=>'Select Section','label' =>false)); ?>
		          </div>
		            <div class="col-sm-4 col-xs-6" >
		 		  <label for="inputEmail3" class="control-label">Quater(Tuition Fee)</label>
	    	<?php  $quat=array('Quater1'=>'Quater1','Quater2'=>'Quater2','Quater3'=>'Quater3','Quater4'=>'Quater4'); echo $this->Form->input('quarter',array('class'=>'form-control','options'=>$quat,'empty'=>'Select Quater','label' =>false)); ?>
		          </div>
              <div class="col-sm-4 col-xs-6">
   <label for="inputEmail3" class="control-label"></label>
   <br>
  <span><label class="checkbox-inline"><input id="radio1" name="mode"  value="1" type="checkbox"><b>Check Estimated Total Selected Quater Collection </b></label></span>

   </div>
		 <div class="col-sm-4 col-xs-6 text-xs-center" >
   <label for="inputEmail3" class="control-label"></label><br>
   <input type="submit" style="background-color:#00c0ef;color:#fff;width:100px !important; margin-right: 10px;" class="btn btn4 btn_pdf myscl-btn date" value="Submit">
   <button type="reset" style="background-color:#333;color:#fff;width:100px !important; margin-right: 10px;" class="btn btn4 btn_pdf myscl-btn date">Reset</button>
   </div>  </div>
  <?php
echo $this->Form->end();
?>


           <div id="updt">
         </div>

         <div style="display:none;">
           <script type="text/javascript">
$("#checkall").change(function(){
     var checked = $(this).is(':checked');
     if(checked){
       $(".checkbox").each(function(){
         $(this).prop("checked",true);
       });
     }else{
       $(".checkbox").each(function(){
         $(this).prop("checked",false);
       });
     }
   });
</script>

<script>
$(document).ready(function () {
$('tbody tr').each(function(){

    var hide = false;
    $('td',this).each(function(){
if ( $(this).hasClass("etotl") ) {

        //if($(this).html() == 0)
        //    hide = true;
}
    });

    if(hide){
    //    $(this).hide();
}
});
});
</script>
<script type="text/javascript">

        $('.inv').click(function(){
        var sd= $(".checkbox:checked").length;
        if(sd==0){
          alert("Please Select One Student Atleast.")
          return false;
        }else{
          return true;
        }
        });

        </script>
        <div class="table-responsive">
<table id="example1" class="table table-bordered table-striped">
                <thead>
			 <!--  <tr>
   <td><a id="" style="position: absolute;
top: 38px;
/* right: 0px; */
right: 46px;"  target="_blank" class="btn btn-info btn-sm pull-right" href="<?php echo ADMIN_URL ;?>report/user_defaulter"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Export PDF</a></td>
   </tr>
   -->

                <tr>



                 <th width="3%"><input type="checkbox" id='checkall' /></th>
                  <th>S.No</th>
                  <th>Student Name</th>
                       <th>Father Name</th>
                   <th>Mobile</th>
                <?   foreach($quaters as $h=>$ty) { ?>


                   <th ><? echo $ty; ?></th>
                <? } ?>
                    <th>Previous Dues</th>
           <!--      <th>(-)Discount</th>
                  <th>(+)Late Fee</th>  -->

               <th>Due Fees </th>

                </tr>
                </thead>
                <tbody>
		<?php $page = $this->request->params['paging']['Services']['page'];
		$limit = $this->request->params['paging']['Services']['perPage'];
		$counter = ($page * $limit) - $limit + 1;
	    $feestotalqus=0;
	    $feestotalqus2=0;
	    $feestotalqus3=0;
	    $feestotalqus4=0;
    $perticular_feestotal=0;
		$total_discount=0;
		$total_due_amount=0;
		$total_dues_amount=0;

	               $session = $this->request->session();
		             $session->delete($results);
			           $session->write('results',$results);
			        //  $session->delete($class_id);
			         //  $session->write('class_id',$class_id);
			          //$session->delete($section_id);
			          // $session->write('section_id',$section_id);
			                $session->delete($quaters);
			           $session->write('quaters',$quaters);
	                   $session->delete($academicyear);
			           $session->write('academicyear',$academicyear);
		if(isset($results) && !empty($results)){
			$fees=0;
		foreach($results as $h=>$service){

		$fedd=0;
		?>
                <tr>
                 <td><input  type="checkbox" class='checkbox'  name="p_id[]" value=<?php echo $service['id'];?> /> </td>
                  <td><?php echo $service['enroll'];?></td>
                  <td><a target="_blank" href="<?php echo SITE_URL;?>admin/studentfees/index/<?php echo $service['id'] ; ?>/<?php echo $academicyear; ?>"><?php echo $service['fname']." ".$service['middlename']." ".$service['lname'];?></a></td>
                  <td><?php echo $service['fathername'];?></td>
                   <td><?php echo $service['sms_mobile'];?></td>
               <?php


                $studentfees=$this->Comman->finddisountstudent($service['id'],$academicyear);


                $quas=array();


						 foreach($studentfees as $k=>$value){
							$quas[]=unserialize($value['quarter']);


							}


						$quaf=array();

							foreach($quas as $h=>$vale){

								$quaf=array_merge($quaf,$vale);

								}
						$rt=array();
				foreach($quaf as $j=>$t){

					$qua[]=$j;
				}

               foreach($quaters as $h=>$ty) {


                if(!empty($quaf)){ ?>
               <td><span class="text-black">&#8377; </span>
              <?
               $dff=0;
              foreach($quaf as $t=>$h){
              if($t==$ty){

           //   echo $h;
           echo "-";
              $dff++;
              }else{




              }


              }  if( $dff=='0'){


           if($ty=="Quater1"){

               $ty="Tution Fee";

               }else if($ty=="Quater2"){
               $ty="Tution Fee";


               }else if($ty=="Quater3"){

                $ty="Tution Fee";
               }else if($ty=="Quater4"){

                $ty="Tution Fee";
               }

               $feeshead=$this->Comman->findfeeheadsid($ty);  $err=$this->Comman->findfeeheadsamount($service['class_id'],$academicyear,$feeshead['id']);



               if($ty=="Quater1"){

              echo $err[0]['qu1_fees'];
               $fedd +=$err[0]['qu1_fees'];
               }else if($ty=="Quater2"){
              echo $err[0]['qu2_fees'];
                $fedd +=$err[0]['qu2_fees'];

               }else if($ty=="Quater3"){

                echo $err[0]['qu3_fees'];
                  $fedd +=$err[0]['qu3_fees'];
               }else if($ty=="Quater4"){

              echo $err[0]['qu4_fees'];
                   $fedd +=$err[0]['qu4_fees'];
               }else{


               echo $err[0]['qu1_fees'];
                 $fedd +=$err[0]['qu1_fees'];
               }
              } ?>




                </td>
                <?
                }else{ ?>
                   <td><span class="text-black">&#8377; </span>

               <? if($ty=="Quater1"){

               $ty="Tution Fee";

               }else if($ty=="Quater2"){
               $ty="Tution Fee";


               }else if($ty=="Quater3"){

                $ty="Tution Fee";
               }else if($ty=="Quater4"){

                $ty="Tution Fee";
               }

               $feeshead=$this->Comman->findfeeheadsid($ty);  $err=$this->Comman->findfeeheadsamount($service['class_id'],$academicyear,$feeshead['id']);



               if($ty=="Quater1"){

              echo $err[0]['qu1_fees'];
              $fedd +=$err[0]['qu1_fees'];

               }else if($ty=="Quater2"){
              echo $err[0]['qu2_fees'];
               $fedd +=$err[0]['qu2_fees'];

               }else if($ty=="Quater3"){

                echo $err[0]['qu3_fees'];
                 $fedd +=$err[0]['qu3_fees'];
               }else if($ty=="Quater4"){

              echo $err[0]['qu4_fees'];
               $fedd +=$err[0]['qu4_fees'];
               }else{


               echo $err[0]['qu1_fees'];
               $fedd +=$err[0]['qu1_fees'];
               } ?>
               </td>

               <? }
                }



              ?>

              <td><span class="text-black">&#8377; </span>
              <? $findpending=$this->Comman->findpendingssinglefee($service['id']);

						if($findpending[0]['sum']){  echo $findpending[0]['sum'];   $fedd +=$findpending[0]['sum'];   }else{ echo "-";   } ?>
              </td>
                  <!-- <td><span class="text-black">&#8377; </span><?php $perticularamount=$this->Comman->findperticularamount($service['id'],$academicyear);

                if( !empty($perticularamount) )
                {
                  $perticular_feestotal1=0;

                  foreach($perticularamount as $valued)
                  {

if($valued['discount'])
							{

$discounts=$valued['discount'];
     $perticular_feestotal1+=$discounts;


							}



                  }

                  $total_discount += $perticular_feestotal1;

                  echo $perticular_feestotal1;
                }
                else
                  echo "0";

              ?></td>
               <td><span class="text-black">&#8377; </span><?php $perticularamount=$this->Comman->findperticularamount($service['id'],$academicyear);

                if( !empty($perticularamount) )
                {
                  $perticular_feestotal12=0;

                  foreach($perticularamount as $valued)
                  {

if($valued['lfine'])
							{

$lfine=$valued['lfine'];
     $perticular_feestotal12+=$lfine;


							}



                  }

                  $total_late += $perticular_feestotal12;

                  echo $perticular_feestotal12;
                }
                else
                  echo "0";

              ?></td>  -->
               <? /* ?> <td><span class="text-black">&#8377; </span><?php $perticularamounts=$this->Comman->findperticularamount($service['id'],$academicyear);
                $paidfeestotal=0;
               foreach($perticularamounts as $values)
               {

                     $paidfeestotal+=$values['deposite_amt'];
                     $perticular_feestotal+= $values['deposite_amt'];
                }
            echo $paidfeestotal;

                 ?></td>

                 <? */ ?>


              <td class="etotl">
                  <?php $findamountmonth=$this->Comman->findamountmonth($service['class_id'],$academicyear);// pr($findamountmonth);
                  $findamount3month=$this->Comman->findamount3month($service['class_id'],$academicyear);
                  $findamount2month=$this->Comman->findamount2month($service['class_id'],$academicyear);
                  $findamount1month=$this->Comman->findamount1month($service['class_id'],$academicyear);
                $findsum= $findamountmonth['qu4_fees']+$findamount3month['qu3_fees']+$findamount2month['qu2_fees']+$findamount1month['qu1_fees'];

                $perticularamounts=$this->Comman->findperticularamount($service['id'],$academicyear);
                $paidfeestotal=0;

                $discount=0;
               foreach($perticularamounts as $values)
               {

                     $paidfeestotal+=$values['fee'];

                }

                foreach($perticularamounts as $values)
               {

                     $discount+=$values['discount'];

                }






                if($findsum>$paidfeestotal){



					$dueamt=$findsum-$paidfeestotal;
						$total_dues_amount+= $dueamt;


						if($discount>0){


             // echo $dueamt=$dueamt-$discount;

             if($fedd !='0'){
              ?><span class="text-black">&#8377; </span><?  echo $fedd; ?><strong style="color:red;">*</strong>  <?

               }else{

               echo $fedd;
               }
               }else{

             //echo $dueamt;
             if($fedd !='0'){
              ?><span class="text-black">&#8377; </span><?  echo $fedd; ?><strong style="color:red;">*</strong>  <?

               }else{

               echo $fedd;
               }

             }  }else{
					if($fedd !='0'){
              ?><span class="text-black">&#8377; </span><?  echo $fedd; ?><strong style="color:red;">*</strong>  <?

               }else{

               echo $fedd;
               } ?><?
					} ?>
                  </td>


                </tr>
		<?php $counter++;} }else{?>
		<tr>
		<td>NO Data Available</td>
		</tr>
		<?php } ?>
		                <? /* ?> <tr>
                     <td></td>
             <td class="text-bold text-green">GRAND TOTAL</td>
             <td class="text-bold text-green" colspan="4">    &nbsp;  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;     Total    =   <span class="text-black">&#8377; </span> <?php echo $feestotalqus+$feestotalqus2+$feestotalqus3+$feestotalqus4; ?></td>


                <td class="text-bold text-green"><span class="text-black">&#8377; </span><?php echo $total_dues_amount; ?></td>
             <td class="text-bold text-green"><span class="text-black">&#8377; </span><?php echo $perticular_feestotal; ?></td>
             <td class="text-bold text-green"><span class="text-black">&#8377; </span><?php echo $total_discount; ?></td>
             <td class="text-bold text-green"><span class="text-black">&#8377; </span><?php echo $total_due_amount; ?></td>
    </tr>  <? */ ?>
                </tbody>

              </table>
              </div>

</div>
            </div>



  <!-- /.content-wrapper -->
