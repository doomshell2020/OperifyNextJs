<div class="table-responsive">
<table id="example1" class="table table-bordered table-striped">


                <thead>    <span style="text:align:center;font-size: 16px;
    font-weight: bold;     color: #3c8dbc;
    text-decoration: overline;">  <? // echo $cho; ?></span>
			   <tr>

   <td><a id="" style="position: absolute;
top: 38px;
/* right: 0px; */
right: 9px; padding: 7px;"   class="btn btn-info btn-sm pull-right" href="<?php echo ADMIN_URL ;?>report/bankuser_defaulter"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Export Excel</a></td>


        </tr>

                <tr>




                  <th>S.No</th>
                  <th>Sr.No</th>
                  <th>Student</th>
                     <th>Class</th>

                       <th>Father Name</th>
                   <th>Mobile</th>
                <th>Discount Category</th>
                <th>Misc. FEE</th>
                <th><?  if($quaters[1]){ echo $quaters[1];  }else{ echo $quaters[0];   }?> Fee<strong style="color:red;">*</strong></th>
                                    <th>(-)Discount</th>
                        <th>Net Tution Fees<strong style="color:red;">*</strong></th>
                    <th>Practical Fee</th>
                    <th>Previous Dues</th>





                </tr>
                </thead>
                <tbody>
		<?php     $session = $this->request->session();
			         $session->delete($class_id);
			        $session->write('class_id',$class_id);
			         $session->delete($section_id);
			          $session->write('section_id',$section_id);
			                $session->delete($quaters);

			              $counter=1;
			           $session->write('quaters',$quaters);
	                   $session->delete($academicyear);
			           $session->write('academicyear',$academicyear);
			               $session->delete('res');
								 $session->write('res',$res);
								 $session->delete('mode');
			           $session->write('mode',$mode);
		$cnt=0;
		if(isset($res) && !empty($res)){

//pr($res); die;

			$fees=0;
		foreach($res as $h=>$service){
		       $studentfees=$this->Comman->finddisountstudent($service[0],$academicyear);
		        $quas=array();
		        $testpass=0;
                	$quaf=array();
                	$qua=array();
              	$rt=array();
						 foreach($studentfees as $k=>$value){
							$quas[]=unserialize($value['quarter']);


							}

							foreach($quas as $h=>$vale){

								$quaf=array_merge($quaf,$vale);

								}
							foreach($quaf as $j=>$t){

					            $qua[]=$j;
					             }


					             if($quaters[0]=='Quater4'){

									  if(in_array('Quater3',$qua)){


										 $testpass=1;

									  }else{
										 		 $testpass=0;
									  }

									 }else if($quaters[0]=='Quater3'){

										  if(in_array('Quater2',$qua)){
										   $testpass=1;
									  }else{
										 		 $testpass=0;
									  }
							 }else if($quaters[0]=='Quater2'){
								  if(in_array('Quater1',$qua)){
										 $testpass=1;
									  }else{
										 		 $testpass=0;
									  }
							 }else if($quaters[1]=='Quater1'){

										 $testpass=1;

							 }

				$fedd=0; if($testpass!='0'){
					$fetchdetail='--';


 $abcObj = new \App\Controller\Admin\ReportController;
 $fetchdetail = $abcObj->defaultersearchbyidhistory($service[0],$previous_year);

 if($fetchdetail=='--'){
		?>
                <tr>

                <td><?php echo $counter++;?></td>

                   <td><?php echo $service[1];?></td>
                  <td><a target="_blank" href="<?php echo SITE_URL;?>admin/studentfees/index/<?php echo $service[0] ; ?>/<?php echo $academicyear; ?>"><?php echo $service[2];?></a></td>
                          <td><?php echo $service[3];?></td>
                          <td><?php echo $service[4];?></td>
                          <td><?php echo $service[5];?></td>
                          <td><?php echo $service[6];?></td>
                          <td><?php echo $service[7];?></td>
													<td><?php echo $service[8];?></td>

													<?php  if($quaters[1]){ ?>
                             <td><?php echo $service[14];?></td>
                          <td><?php $fees +=$service[15]; echo $service[15];?></td>
													<td><?php echo $service[12];?></td>


													<?php }else{ ?>
                             <td><?php echo $service[11];?></td>
                          <td><?php $fees +=$service[12]; echo $service[12];?></td>
													<td><?php echo $service[9];?></td>

													<?php 	} ?>


         <td><?php $findpending=$this->Comman->findpendingssingle234fee($service[0],$academicyear);


						if($findpending[0]['sum']){    echo $findpending[0]['sum'];    }else{ echo "--";   }?></td>






                </tr>
		<?php } } } }else{?>
		<tr>
		<td style="text-align:center;" colspan="13">NO Data Available</td>
		</tr>
		<?php } ?>
<?php if($fees!='0'){  setlocale(LC_MONETARY, 'en_IN');

$fees = money_format('%!i', $fees);   $session->delete('fees');
$session->write('fees',$fees); ?>
		<tr>
		<td style="text-align:center;" colspan="13"><strong style="color:red;">Total = <?php echo $fees; ?>*</strong></td>
		</tr>
<?php } ?>
                </tbody>

              </table>
</div>

            <? die; ?>
