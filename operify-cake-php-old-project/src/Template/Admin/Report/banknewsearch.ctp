


<script type="text/javascript">

    // var table = $('#example1').DataTable();

   //  table.draw();


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
        <script>
$(document).ready(function() {
    //prepare the dialog

    //respond to click event on anything with 'overlay' class
    $(".globalModals").click(function(event){
        $('.modal-content').load($(this).attr("href"));  //load content from href of link
        });
    });

</script>

<div class="table-responsive">
<table id="example1" class="table table-bordered table-striped">


                <thead>    <span style="text:align:center;font-size: 16px;
    font-weight: bold;     color: #3c8dbc;
    text-decoration: overline;">  <? // echo $cho; ?></span>
			   <tr>

   <td><a id="" style="position: absolute;
top: 65px;
/* right: 0px; */
right: 9px; padding: 7px;"  class="btn btn-info btn-sm pull-right" href="<?php echo ADMIN_URL ;?>report/bankuser_defaulterlatests"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Export Excel</a></td>



        </tr>

                <tr>



                 <th width="3%"><input type="checkbox" id='checkall' /></th>

                 <? if($headerRow2){ foreach($headerRow2 as $j=>$t){ ?>
               <th><? echo $t; ?></th>

               <? } } ?>
                </tr>


                </thead>
                <tbody>
		<?php $page = $this->request->params['paging']['Services']['page'];
		$limit = $this->request->params['paging']['Services']['perPage'];
		$counter =1;
	    $feestotalqus=0;
	    $feestotalqus2=0;
	    $feestotalqus3=0;
	    $feestotalqus4=0;
    $perticular_feestotal=0;
		$total_discount=0;
		$total_due_amount=0;
		$total_dues_amount=0;


    $session = $this->request->session();
    $session->delete('resultssss');
    $session->write('resultssss',$results);
    $session = $this->request->session();
    $session->delete('headerRows2');
    $session->write('headerRows2',$headerRow2);
			         $session->delete($class_id);
			        $session->write('class_id',$class_id);
			         $session->delete($academicyear);
			        $session->write('academicyear',$academicyear);
			         $session->delete($section_id);
			          $session->write('section_id',$section_id);
			                $session->delete('quaterss');
                 $session->write('quaterss',$quaters);
                 $session->delete('quatersselected');
			           $session->write('quatersselected',$quatersselected);
	                   $session->delete($academicyear);
			           $session->write('academicyear',$academicyear);
	                   $session->delete($mode);
                 $session->write('mode',$mode);
                 $session->delete($mode1);
			           $session->write('mode1',$mode1);




		if(isset($results) && !empty($results)){


			$fees=0;
			$totals=0;


		foreach($results as $h=>$service){

			$ss=sizeof($service);

			$rt=explode('-',$service[1]);
			?>
	<tr>
		<td><input  type="checkbox" class='checkbox'  name="p_id[]" value="<?php  echo $rt[1];?>" /></td><?
			for($i=0;$i<=$ss;$i++){


		?>



                    <td><?php if($service[$i]){ if($i==1){  echo $rt[0]; }else{  echo $service[$i]; } }else{ echo '-'; }?></td>




            <?  }  ?> <? if(end($service)){
				$totals +=strip_tags(end($service));
				 } ?></tr><? } }else{?>
		<tr>
		<td style="text-align:center;" colspan="16">NO Defaulter Available</td>
		</tr>
		<?php } ?>
		                <tr>


                  <tr>
					  <?   $session->delete($totals);
			           $session->write('totals',$totals); setlocale(LC_MONETARY, 'en_IN');

$headerRow3 = money_format('%!i', $totals); ?>
		<td style="text-align:center;" colspan="16"><strong style="color:red;">Total = <? echo $headerRow3; ?>*</strong></td>
		</tr>




    </tr>
                </tbody>

              </table>
         </div>
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


