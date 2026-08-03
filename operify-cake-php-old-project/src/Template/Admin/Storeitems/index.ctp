<style>
#testUL{
  position: relative;
}

#testUL ul{
  position: absolute;
  z-index: 999;
  overflow: scroll;
    height: 100px;
  top: 100%;
  left: 0px;
  right: 0px;  
  list-style-type: none;
  background-color: white;
  padding-left: 0px;
}

#testUL ul li{
  padding : 5px 8px;
  border: 1px solid lightgray;  
}

#testUL ul li a{
  color: black;

}

.preview{
  margin-right:15px;
}
 

</style>
 <div class="content-wrapper">
   <section class="content-header">
    <h1>
    Store Items Manager
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo SITE_URL; ?>admin/indent"><i class="fa fa-home"></i>Home</a></li>
      <li><a href="<?php echo SITE_URL; ?>admin/Storeitems">Add Store Item</a></li>
    </ol> 
  </section> <!-- content header -->

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-xs-12">    
        <div class="box">
        <div class="box-header">
        <?php echo $this->Flash->render(); ?>
                        <a href="<?php echo SITE_URL ;?>admin/Storeitems/add">
              <button class="btn btn-success pull-right m-top10"><i class="fa fa-plus" aria-hidden="true"></i>
              Add Store Items</button></a>
              <script>
  function cllbckretail(id,cid,sid) { 
    $('.secrh-retail').val(id);
    $('#retail_ids').val(cid);
    $('#size').val(sid);
    $('#testUL').hide();
    //alert(cid);
    $.ajax({ 
      type: 'POST', 
      url: '<?php echo ADMIN_URL; ?>indent/getitemdetail',
      data: {'fetch':cid},
      success: function(data){  
        console.log(data);
        //alert(data);
        $('#unitna').val(data);
      },    
    }); 

  }

 $( function() {
    $('.secrh-retail').bind('keyup',function(){  
    var pos=$(this).val();
    //alert(pos);
    var check = 0;
    //var catid=$('#subcategory').val();
    //alert(pos);
    $('#testUL').show();
    $('#retail_ids').val('');
    var count=pos.length;
    if(count > 0)
    {
    $.ajax({ 
      type: 'POST', 
      url: '<?php echo ADMIN_URL; ?>indent/getitemname',
      data: {'fetch':pos,'check':check},
      success: function(data){  
        console.log(data);
        $('#testUL ul').html(data);
      },    
    }); 
    }else{
    $('#testUL').hide();  
    }   
    });     
  });
</script>
               <script inline="1">
         $(document).ready(function() {
               $("#itemsdetails").bind("submit", function(event) {
                 $.ajax({
                   async: true,
                   data: $("#itemsdetails").serialize(),
                   dataType: "html",
                   type: "POST",
                   url: "<?php echo ADMIN_URL; ?>storeitems/searchitem",
                   beforeSend: function() {
                   
                   },
                   success: function(data) {
                      $("#updt").html(data);
                   },
                   complete: function(data) {
        
                   
                   },
                 });
                 return false;
               });
             });

             </script>

 <?php echo $this->Form->create('Additem', array('type' => 'file', 'inputDefaults' => array('div' => false, 'label' => false), 'id' => 'itemsdetails', 'class' => 'form-horizontal')); ?>    

<div class="form-group">
		
		

	  <div class="col-sm-3">
      <label for="inputEmail3" class="control-label">Search Items</label>
        <input type="hidden" required="required" name="item_id" id="retail_ids">
                <?php echo $this->Form->input('nitem', array('class' => 'form-control secrh-retail', 'id' => 'itemname', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off','placeholder'=>'Enter Item Name')); ?>
                <div id="testUL" style="display:none;"><ul></ul></div>
   
      </div>  
   
      <div class="col-md-3">
          
          <label for="inputEmail3" class="col-sm-12 control-label" style="text-align: left !important;">Supplier</label>
           <div class="col-md-12">
          <?php echo $this->Form->input('supplier_name', array('class' => 'form-control', 'type' => 'select', 'options'=>$suppliers,'label' => false, 'empty' => 'Select Supplier', 'autofocus', 'autocomplete' => 'off')); ?>
         </div>
        </div>


      <div class="col-sm-3">
      <label for="inputEmail3" class="control-label"></label>
      <input type="submit" style="background-color:#00c0ef;width:100px !important; margin-top: 23px;" id="Mysubscriptions" class="btn btn4 btn_pdf myscl-btn date" value="Search">       
      </div> 
        
       </div><?php echo $this->Form->end(); ?>  
           
        
         
            </div><!-- /.box-header -->
            <div class="box-body" id="updt">    
              <table id="example14" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th width= "3%">S.No.</th>
                    <th width= "15%">Items Name</th>    
                    <th width = "11%">Purchase Qty.</th>
                    <th width = "11%">Issue Qty.</th>
                    <th width = "11%">Sold Qty.</th>
                    <th width = "11%">Return Qty.</th>
                    <th width = "11%">Current Qty.</th>

                    <th width= "11%">Supplier Name</th> 
                    <th>Action</th>
                  </tr>
                </thead>

                <tbody>
                  <?php $page = $this->request->params['paging']['']['page'];
                  $limit = $this->request->params['paging']['']['perPage'];
                  $counter = ($page * $limit) - $limit + 1;
                  if(isset($store) && !empty($store)){ 
                    foreach($store as $intusr){ //pr($intusr);
                      ?>
                      <tr>
                        <td><?php echo $counter;?></td>                        
                        <td> <a href = "<?php echo SITE_URL; ?>/admin/storeitems/viewdetail/<?php echo $intusr['id']; ?>" class = "viewdetails"><?php echo ucfirst($intusr['item_name']); ?></a></td>
                        <?php 
      $totalrecivied=$this->Comman->totalstockregisteropeningrecivied($intusr['item_id']); $totaldispatched=$this->Comman->totalstockregisteropeningdispatched($intusr['item_id']);
      $remain=$totalrecivied[0]['sum']-$totaldispatched[0]['sum']; ?>

                        <td> <?php echo $totalrecivied[0]['sum']; ?></td> 
                        <td> <?php echo 0; ?></td>
                        <td> <?php if($totaldispatched[0]['sum']){
                          echo $totaldispatched[0]['sum'];
                        }else{
                          echo "--";   
                        }
                          ?></td>
                        <td> <?php echo 0; ?></td>
                        <td> <?php echo $remain; ?></td>

                        <td> <?php echo $supplier[0]['name']; ?></td>


                       


                       



                    
                    
                          <td> <strong><?php
                         
                            echo $this->Html->link('', [
                                'action' => 'edit',
                                $intusr->id,
                            ], ['class' => 'glyphicon glyphicon-edit', 'style' => 'font-size: 21px;']);

                          ?>
                             &nbsp;<?php
                            echo $this->Html->link('', [
                              'action' => 'delete',
                              $intusr->id
                            ],['class'=> 'glyphicon glyphicon-remove','style'=>'font-size: 21px;'	
                ,"onClick"=>"javascript: return confirm('Are you sure do you want to delete this store item')"]); ?>



        <?php if($intusr['status']=='Y'){ 
                        echo $this->Html->link('', [
                          'action' => 'status',
                          $intusr->id,'Y'
                        ],['title'=>'Active','class'=>'fa fa-check-circle','style'=>'font-size: 21px !important; margin-left: 12px;     color: #36cb3c;']);
                        
                      }else{ 
                        echo $this->Html->link('', [
                          'action' => 'status',$intusr->id,'N'
                        ],['title'=>'Inactive','class'=>'fa fa-times-circle-o','style'=>'font-size: 21px !important; margin-left: 12px; color:#FF5722;']);
                        
                      } ?>
                
                          </strong></td>
                          
                        
                        </tr>
                        <?php $counter++; } }else{ ?>


                        <?php } ?>  
      </tbody>

    </table>
  </div>

  <!-- /.box-body -->
</div>
<!-- /.box -->
</div>
<!-- /.col -->  
</div>
<!-- /.row -->      


</section>
<!-- /.content -->  

</div>     
<!-- /.   content-wrapper -->  


<script>
	$('.viewdetails').click(function(e){
		e.preventDefault();
		$('#editsorts').modal('show').find('.modal-body').load($(this).attr('href'));
	});
</script>


<div class="modal fade" id="editsorts">
	<div class="modal-dialog">
		<div class="modal-content">
			<!-- Modal Header -->
			<!-- Modal body -->
			<div class="modal-body">
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
  $(".add-batch-fields").click(function(){ 
      $.ajax({
        type: "POST",
        url: '<?php echo SITE_URL;?>admin/additem/add',        
        cache: false,
        success: function(html) {
          //alert(html);   
          $(".product_containes").append(html);                
        }       
      }); 
    });

    $("body").on("click",".remove",function(){ 
     //alert('hello');
      $(this).closest('.formdetails').remove();   
   }); 
});
</script>
