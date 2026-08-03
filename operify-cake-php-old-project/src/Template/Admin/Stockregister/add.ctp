<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Goods Received Note
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
            <h3 class="box-title"><i class="fa fa-plus-square" aria-hidden="true"></i> <?php if(isset($location['id'])){ echo 'Edit Post New'; }else{ echo 'Generate G.R.N.'; } ?></h3>
          </div>
          <!-- /.box-header -->
          <!-- form start -->
          <?php echo $this->Form->create('Goodsreceived', array(
            'class'=>'form-horizontal',
            'enctype' => 'multipart/form-data',
            'validate'
          )); ?>
          <div class="box-body"> 
            <div class="form-group">
              <label for="inputEmail3" class="col-sm-2">Purchase Order ID <strong style="color:red;">*</strong></label>
              <div class="col-sm-4">
                <?php echo $this->Form->input('purchaseorder_id', array('class' => 'form-control select2','id'=>'purchase','type'=>'select','options'=>$purchaseorderid,'label'=>false,'autofocus','empty'=>'Enter Purchase Order ID','autocomplete'=>'off','required')); ?><span id="estimateddevlierydate" style="color:red;"></span>
                 <input type="hidden" name="vendor_id" class="form-control" id="vendor_id" value="">
              </div>
               <label for="inputEmail3" class="col-sm-2">Inward Date <strong style="color:red;">*</strong></label>
              <div class="col-sm-4">
                <?php echo $this->Form->input('inwarddate', array('class' => 'form-control','id'=>'purchsase','type'=>'text','label'=>false,'autofocus','value'=>date('Y-m-d'),'autocomplete'=>'off','required')); ?>
              </div>
            </div>
             <div class="form-group">
              <label for="inputEmail3" class="col-sm-2">Bill No. <strong style="color:red;">*</strong></label>
              <div class="col-sm-4">
                <?php echo $this->Form->input('bill_no', array('class' => 'form-control','id'=>'bill_no','type'=>'text','label'=>false,'autofocus','empty'=>'Enter Bill No','autocomplete'=>'off','required')); ?>
              </div>
               <label for="inputEmail3" class="col-sm-2">Bill Date <strong style="color:red;">*</strong></label>
              <div class="col-sm-4">
                <?php echo $this->Form->input('bill_date', array('class' => 'form-control','id'=>'datepicker2','type'=>'text','label'=>false,'autofocus','empty'=>'Enter Bill No','autocomplete'=>'off','required')); ?>
              </div>
            </div>


            <div class="ctpcontent form-group" style="display:none">
            <label for="inputEmail3" class="col-sm-2">Items<strong style="color:red;">*</strong></label>
            <div class="col-sm-10">
				
             <table id="customers">
                <thead>
                  <tr class="totalColumn">
                   
            
                    <th>Item</th>
                    <th>Size</th> 
                    <th>Unit</th>    
                    <th>Qty</th>
                    <th>Received Qty</th>
                    <th>Unit Price</th>   
                                 
                    <th>Total Price</th>
                    <th>Tax Rate</th>
                    <th>Tax Amount</th>
                    <th>Total Amount</th>
                  </tr>
                </thead> 
                <tbody class="product_containes">
                  
                </tbody>
                <tfoot>
           
               
                      <tr class="titlerow" style="background-color: #c8c8c8;">    
						  <td colspan="6" class="text-right" style="font-weight:bold;font-size:16px;">Total Amount (INR)</td>                                   
                    <td  class="totala1"> </td>
                    <td  ></td>
                    <td class="totala2">0</td>                    

                    <td class="totala">0</td>
                  </tr>
                  
                </tfoot>
              </table>
            </div>   
            </div>

           
            

            <div class="form-group">
              <label for="inputEmail3" class="col-sm-2">Freight<strong style="color:red;">*</strong></label>

              <div class="col-sm-4">
                <?php echo $this->Form->input('freight', array('class' => 'form-control', 'id' => 'freight', 'type' => 'text', 'label' => false, 'placeholder' => 'Freight', 'autofocus', 'autocomplete' => 'off','required')); ?>
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
                  'Submit', 
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
  function cllbckretail(id,cid) { 
    $('.secrh-retail').val(id);
    $('#retail_ids').val(cid);
    $('#testUL' ).hide();
  }

 $( function() {
    $('.secrh-retail').bind('keyup',function(){  
    var pos=$(this).val();    
    //alert(pos);
    $( '#testUL' ).show();
    $( '#retail_ids' ).val('');
    var count=pos.length;
    if(count > 0)
    {
    $.ajax({ 
      type: 'POST', 
      url: '<?php echo ADMIN_URL; ?>purchaseorder/getpurchaseorderid',
      data: {'fetch':pos},
      success: function(data){  
        console.log(data);
        $('#testUL ul').html(data);
      },    
    }); 
    }else{
    $( '#testUL' ).hide();  
    }   
    });     
  });
</script>

<script type="text/javascript">
$(document).ready(function() {
  $("#purchase").on('change',function() {
    $('.totala').text('');
    $('.totalq').text(''); 
    $('.product_containes').html('');
    var indentid = $(this).val();
    //alert(indentid);
    if(indentid != ""){
      $.ajax({ 
        type: 'POST', 
        url: '<?php echo ADMIN_URL; ?>purchaseorder/purchaseorderitems',
        data: {'id':indentid},
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

0.