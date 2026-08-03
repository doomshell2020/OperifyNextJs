
<div class="table-responsive">

	<table id="example1" class="table table-bordered table-striped">
		<thead>
			<tr>
				<th>#</th>
				<th>ASN No.</th>
				<th>ISBN No.</th>
				<th>Book Name</th>
				<th>Book Category</th>
				<th>Cupboard</th>
				<th>Cupboard Shelf</th>
				<th>Author</th>
				<th>Status</th>
			</tr>
		</thead>

		<tbody>

			<?php 

			$page = $this->request->params['paging']['books']['page'];
			$limit = $this->request->params['paging']['books']['perPage'];
			$counter = ($page * $limit) - $limit + 1;

			if(isset($books) && !empty($books)){ 
				foreach($books as $work){
	          //pr($work);die;
			?>
					<tr>
						<td><?php echo $counter;?></td>

						<td><?php if(isset($work['ISBN_NO'])){ echo ucfirst($work['ISBN_NO']);}else{ echo 'N/A';}?></td>

						<td><?php if(isset($work['name'])){ echo ucfirst($work['name']);}else{ echo 'N/A';}?></td>

						<td><?php if(isset($work['book_category']['name'])){ echo ucfirst($work['book_category']['name']);}else{ echo 'N/A';}?></td>

						<td><?php if(isset($work['author'])){ echo ucfirst($work['author']);}else{ echo 'N/A';}?></td>

						<td class="text-center">
							<?php if( isset($work['book_copy_detail']) ){ echo $copy=sizeof($work['book_copy_detail']); } else{ echo 'N/A'; } ?>
						</td>

					</tr>

					<?php $counter++;} }else{?>
					<tr>
						<td>NO Data Available</td>
					</tr>
					<?php } ?>	
		</tbody>

	</table>

</div>