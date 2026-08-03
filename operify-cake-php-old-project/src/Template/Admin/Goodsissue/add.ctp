<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Goods Issue
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo SITE_URL; ?>admin/goodsreceived"><i class="fa fa-home"></i>Home</a></li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
<style>
#customers {
  font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
  border-collapse: collapse;
  width: 100%;
  margin-bottom:20px;
  }

#customers td, #customers th {
  border: 1px solid #ddd;
  padding: 8px;
}

#customers tr:nth-child(even){background-color: #f2f2f2;}

#customers tr:hover {background-color: #ddd; }

#customers th {
  padding-top: 12px;
  padding-bottom: 12px;
  text-align: left;
  background-color: #c8c8c8;
  color: #333333; 
}

#testUL ,#testULs{
  position: relative;
  display:none;
}

#testUL ul,#testULs ul{
  position: absolute;
  max-height: 140px;
    overflow: scroll;
  z-index: 999;
  top: 100%;
  left: 0px;
  right: 0px;  
  list-style-type: none;
  background-color: white;
  padding-left: 0px;
}

#testUL ul li,#testULs ul li{
  padding : 5px 8px;
  border: 1px solid lightgray;  
}

#testUL ul li a, #testULs ul li a{
  color: black;
}
</style>
      <!-- right column -->
      <div class="col-md-12">
        <!-- Horizontal Form -->
        <div class="box box-info">
          <?php echo $this->Flash->render(); ?>
          <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-plus-square" aria-hidden="true"></i> <?php if(isset($location['id'])){ echo 'Edit Post New'; }else{ echo 'Goods Issue'; } ?></h3>
          </div>
          <!-- /.box-header -->
          <!-- form start -->
          <?php echo $this->Form->create('Goodsissue', array(
            'class'=>'form-horizontal',
            'enctype' => 'multipart/form-data',
            'validate'
          )); ?>
          <div class="box-body"> 
			   <div class="form-group"> 
              <label for="inputEmail3" class="col-sm-1">Indent <strong style="color:red;">*</strong></label>
              <div class="col-sm-5">              

                <?php  if($id){

                echo $this->Form->input('indent_id', array('class' => 'form-control select2','id'=>'indent','type'=>'select','value'=>$id,'options'=>$indent,'label'=>false,'autofocus','autocomplete'=>'off','data-placeholder' => '  Enter Multiple Indent ID','required'));

                }else{

               echo $this->Form->input('indent_id', array('class' => 'form-control select2','id'=>'indent','type'=>'select','options'=>$indent,'label'=>false,'autofocus','autocomplete'=>'off','data-placeholder' => '  Enter Multiple Indent ID','required'));   } ?>

              </div>
            </div>
              <div class="ctpcontent form-group" style="display:none">
            <label for="inputEmail3" class="col-sm-1">Items</label>
            <div class="col-sm-11">
              <table id="customers">
                <thead>
                  <tr class="totalColumn">
                   
                    <th>Indent</th>
                    <th>Item</th>
                    <th>Requested Qty</th>
                    <th>In Stock Qty</th>
                    <th>Issue Qty</th>
                   
                  </tr>
                </thead> 
                <tbody class="product_containes">
                  
                </tbody>
                <tfoot>
           
               
                      <tr class="titlerow" style="background-color: #c8c8c8;">    
						  <td colspan="4" class="text-right" style="font-weight:bold;font-size:16px;">Total Goods Issue </td>                                   
                    

                    <td class="totala">0</td>

                    <input type="hidden" name="tqty" class="tqty" value="0">
                  </tr>
                  
                </tfoot>
              </table>
            </div>   
            </div>
             <div class="form-group">
              <label for="inputEmail3" class="col-sm-2">Remark</label>

              <div class="col-sm-4">
                <?php echo $this->Form->input('remark', array('class' => 'form-control', 'id' => 'remark', 'type' => 'textarea', 'label' => false, 'placeholder' => 'Remark', 'autofocus', 'autocomplete' => 'off')); ?>
              </div>
            </div>    

            <!-- <div class="form-group">
              <label for="inputEmail3" class="col-sm-4 control-label">Send Email To Vendor</label>

              <div class="col-sm-6">
                <input type="radio" name="email_vendor" value="Y">      
              </div>
            </div>    -->
            

          </div>
          <!-- /.box-body -->
          <div class="box-footer">
            <?php
            if(isset($location['id'])){
              echo $this->Form->submit(
                'Update', 
                array('class' => 'btn btn-info pull-right', 'title' => 'Update')
              ); }else{ 
                echo $this->Form->submit(
                  'Issue', 
                  array('class' => 'btn btn-info pull-right', 'title' => 'Add')
                );
              }
              ?><?php
              echo $this->Html->link('Back', [
                'action' => 'index'

              ],['class'=>'btn btn-default']); ?>
            </div>
            <!-- /.box-footer -->
            <?php echo $this->Form->end(); ?>


          </div>

        </div>
        <!--/.col (right) -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div> 



<script>
$(document).ready(function(){
  
    $(".add-batch-fields").click(function(){      
      var itemId = $('#itemname').val();
      var quanti = $('#quant').val();
      var purorid = $('#purchaseorder').val();
      var costprice = $('#costprice').val();
      var numItems = $('.video_details').length;
      numItems ++;
      var sum = 0;  
      // alert(itemId);
      // alert(quanti);
      // alert(purorid);
      $.ajax({
        type: "POST",
        url: '<?php echo SITE_URL;?>admin/purchaseorder/purchaseordertemp',
        data: {'item_id':itemId,'quantity':quanti,'purchaseorder':purorid,'srno':numItems,'cost_price':costprice},
        cache: false,
        success: function(html) {
          //alert(html);
          $(".product_containes").append(html);                            
        }
               
      });  

        $(".ctpcontent").css("display", "block");          
        $('#itemname').val('');          
        $('#quant').val('');
        $('#quant').val('');
        $('#costprice').val('');
                   
    });

   $("body").on("click",".remove",function(){ 
      var row = $(this).closest('.video_details').remove();
      //var row = $(this).closest('tr');
      var dynamicValue = $(row).find('.cou').text();
      dynamicValue = parseInt(dynamicValue);
      $('.cou').each(function(idx, elem){
        $(elem).text(idx+1);
      }); 
      
      // Check Total Quantity on click remove
      var checkval = $(this).attr('data-val'); 
      var checktoatalquant = $('.totalq').text();
      if(checktoatalquant != ""){
        tQuant = parseInt(checktoatalquant) - parseInt(checkval);
      }        
      $('.totalq').text(tQuant); 

      //Check Total Amount on click remove
      var checkamo = $(this).attr('data-amount'); 
      //alert(checkamo);
      var checktoatalamou = $('.totala').text();
      if(checktoatalamou != ""){
        tAmou = parseFloat(checktoatalamou) - parseFloat(checkamo);
      }        
      $('.totala').text(tAmou); 

      var purchaseorderId = $(this).attr('data');
      //alert(purchaseorderId);
      $.ajax({
        type: "POST",
        url: '<?php echo SITE_URL; ?>admin/purchaseorder/removepurchaseordertemp',
        data: {'id':purchaseorderId},
        cache: false,
        success: function(data) {
          alert('This item is successfully removed');
        }       
      });
      var numItems = $('.video_details').length;
      if (numItems < 1) {
        $(".ctpcontent").css("display", "none");
      }
   }); 
});
</script>

<script>
  $(document).ready(function(){		  
    $('#datepicker1').datepicker({    
      dateFormat: 'yy-mm-dd',
      yearRange: '2018:2025',
      minDate: new Date(),
      onSelect: function(date){ 
        var selectedDate = new Date(date);
        var endDate = new Date(selectedDate);
        endDate.setDate(endDate.getDate());            
        $("#datepicker2").datepicker( "option", "minDate", endDate );
        $("#datepicker2").val(date);
      }
    });
        
    $('#datepicker2').datepicker({    
    dateFormat: 'yy-mm-dd',
    yearRange: '2018:2025',
     maxDate: new Date(),
     });
  
  });
</script>

<script>
  $(document).ready(function(){
  
    var indentid = '<?php echo $id; ?>';
    //alert(id);
    $('.totala').text('');
    $('.totalq').text(''); 
    $('.product_containes').html('');
  
    //alert(indentid);
    if(indentid != ""){
      $.ajax({ 
        type: 'POST', 
        url: '<?php echo ADMIN_URL; ?>goodsissue/indentitems',
        data: {'fetch':indentid},
        success: function(data){  
        
          $(".ctpcontent").css("display", "block"); 
          $('.product_containes').html(data);
        },    
      });
    }else{
      $(".ctpcontent").css("display", "none"); 
    }      
  });
</script>


<script type="text/javascript">
$(document).ready(function() {
  $("#indent").on('change',function() {
    $('.totala').text('');
    $('.totalq').text(''); 
    $('.product_containes').html('');
    var indentid = $(this).val();
    //alert(indentid);
    if(indentid != ""){
      $.ajax({ 
        type: 'POST', 
        url: '<?php echo ADMIN_URL; ?>goodsissue/indentitems',
        data: {'fetch':indentid},
        success: function(data){  
        
          $(".ctpcontent").css("display", "block"); 
          $('.product_containes').html(data);
        },    
      });
    }else{
      $(".ctpcontent").css("display", "none"); 
    }
  });
});
</script>

