<?php
$page = $this->request->params['paging']['books']['page'];
$limit = $this->request->params['paging']['books']['perPage'];
$counter = ($page * $limit) - $limit + 1;

if(isset($books) && !empty($books)){ 
	foreach($books as $work){
 //pr($work); 
	  if($work['typ']=='1'){
			   $jkl=$this->Comman->findperidetail($work['periodic_category_id']); 
			   //pr($jkl); die;
		   }
?>
		<tr>
			<td><?php echo $counter;?></td>

			<td><?php if(isset($work['accsnno'])){ echo ucfirst($work['accsnno']);}else{ echo 'N/A';}?></td>

			<?php if($work['typ']=='1'){ ?>
	   
              <td><?php if(isset($jkl['periodical_master']['ISBN_NO'])){ echo ucfirst($jkl['periodical_master']['ISBN_NO']);}else{ echo 'N/A';}?></td>
              <?php } else { ?>
				  <td><?php if(isset($work['ISBN_NO'])){ echo ucfirst($work['ISBN_NO']);}else{ echo 'N/A';}?></td>
				  <?php } ?>
			
				<td><?php if(isset($work['bilno'])){ echo ucfirst($work['bilno']);}else{ echo 'N/A';}?></td>

			<td><?php if(isset($work['bildt'])){ echo date('d-m-Y',strtotime($work['bildt']));}else{ echo 'N/A';}?></td>
			<td><?php if(isset($work['b_name'])){ echo ucfirst($work['b_name']);}else{ echo 'N/A';}?></td>
			
			
			
			<?php if($work['typ']=='1') { $percat=$this->Comman->findperiodicalmaster($work['periodic_category_id']);  ?>
			
			<td><?php if(isset($percat['name'])){ echo ucfirst($percat['name']);}else{ echo 'N/A';}?></td>
			
			<?php } else { ?>

			<td><?php if(isset($work['b_category'])){ echo ucfirst($work['b_category']);}else{ echo 'N/A';}?></td>
			<?php } ?>

			<td><?php if(isset($work['cupboard'])){ echo ucfirst($work['cupboard']);}else{ echo 'N/A';}?></td>

			<td><?php if(isset($work['shelf'])){ echo ucfirst($work['shelf']);}else{ echo 'N/A';}?></td>

			 <?php if($work['typ']=='1'){  $lasd=$this->Comman->findlang($jkl['periodical_master']['lang']);  ?>    
              <td><?php if(isset($lasd['language'])){ echo ucfirst($lasd['language']);}else{ echo 'N/A';}?></td>
               <?php } else { $lasd=$this->Comman->findlang($work['blang']); ?>
				    <td><?php if(isset($lasd['language'])){ echo ucfirst($lasd['language']);}else{ echo 'N/A';}?></td>
				   <?php } ?>


			<?php if($work['typ']=='1'){ ?>
              <td><?php if(isset($jkl['periodical_master']['author'])){ echo ucfirst($jkl['periodical_master']['author']);}else{ echo 'N/A';}?></td>
                <?php } else { ?>
					<td><?php if(isset($work['author'])){ echo ucfirst($work['author']);}else{ echo 'N/A';}?></td>
					<?php } ?>

			<td>
				<?php if($srch_status != 'ALL') {
					if(isset($srch_status) && $srch_status == 'Overdue')
					{
						echo '<span class="label label-danger" style="font-size:12px">'."Overdue: ".$work['NumberOfDays']." day(s)".'</span>';
					}
					else if(isset($work['status']))
					{
						echo ucfirst($work['status']);
					}
					else
					{
						echo 'N/A';
					}
				}
				?>
			</td>			

		</tr>

<?php $counter++;} }else{?>
<tr>
	<td colspan="10" style="text-align:center;">NO Data Available</td>
</tr>
<?php } ?>


