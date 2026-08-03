<style>
   #testUL ul {
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

   #testUL {
      position: relative;
   }

   #testUL ul li a {
      color: black;
   }
</style>

<div class="content-wrapper">
   <!-- Content Header (Page header) -->
   <section class="content-header">
      <h1>
         Purchase Return
         <?php
         ?>
         </h1>
         <ol class="breadcrumb">
            <li><a href="<?php echo SITE_URL; ?>admin/purchasereturn"><i class="fa fa-home"></i>Home</a></li>
      </ol>
   </section>
   <!-- Main content -->
   <section class="content">
      <div class="row">
         <!-- right column -->
         <div class="col-md-12">
            <!-- Horizontal Form -->
            <div class="box box-info">
               <?php echo $this->Flash->render(); ?>
               <div class="box-header with-border" style="padding:0px; border-bottom:0px;">

               </div>
               <!-- /.box-header -->
               <!-- form start -->
               <div class="box-body" style="padding:0px; margin-bottom:10px;">

                  <?php echo $this->Form->create(
                     $item,
                     array(
                        'class' => 'form-horizontal',
                        'enctype' => 'multipart/form-data',
                        'validate'
                     )
                  ); ?>
                  <div class="container-fluid">
                     <div class="row" style="display: flex; align-items: flex-end;">
                     </div>

                  </div>
                  <div class="box-body" style="padding:0px; margin-bottom:10px;">
                     <div class="container-fluid">
                        <div class="row" style="display: flex; align-items: end; flex-wrap:wrap;">
                           <script>
                              $(document).ready(function () {
                                 $('#datepicker3').datepicker({
                                    dateFormat: 'dd-mm-yy',
                                    yearRange: '2018:2030',
                                    changeMonth: true,
                                    changeYear: true,
                                    autoclose: true,
                                    maxDate: new Date(),
                                    minDate: '18-03-2024',
                                 });
                                 $('#datepicker3').datepicker('setDate', 'today');
                              });
                           </script>
                           <div class="col-sm-3">
                              <label for="inputEmail3" class="control-label">Date <strong
                                    style="color:red;">*</strong></label>
                              <?php echo $this->Form->input('inwarddate', array('class' => 'form-control', 'id' => 'datepicker3', 'type' => 'text', 'readonly', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'required')); ?>
                           </div>

                           <div class="col-sm-3">
                              <label for="inputEmail3" class=" control-label"
                                 style="text-align: left !important;">Vendor Name
                                 <strong style="color:red;">*</strong></label>
                              <input type="hidden" required="required" name="vendor_id" id="retail_ids">
                              <?php echo $this->Form->input('supplier_id', array('class' => 'form-control secrh-retail', 'type' => 'text', 'required', 'label' => false, 'placeholder' => 'Enter Vendor Name', 'autofocus', 'autocomplete' => 'off')); ?>
                              <div id="testUL" style="display:none;">
                                 <ul></ul>
                              </div> 
                              <!-- <div id="testUL1" style="display:none;">
                                 <ul>
                                    <li
                                       style="padding: 5px 8px;list-style:none;color: black;font-weight: bold;margin-left:-32px; border: 1px solid lightgray;">
                                       No Record Found</li>
                                 </ul>
                              </div> -->
                              <span id="vendor_id" style="color: red;font-size:12px;"></span>
                           </div>


                           <div class="col-sm-3">
                              <label for="inputEmail3" class="control-label" style="text-align: left!important;">Bill
                                 No.<strong style="color:red;">*</strong></label>
                              <?php echo $this->Form->input('bill_no', array('class' => 'form-control secrh-retail', 'type' => 'select', 'id' => 'itemname', 'required', 'label' => false, 'placeholder' => 'Enter Bill No.', 'empty' => '--Select Bill No--', 'autofocus', 'autocomplete' => 'off', )); ?>
                           </div>

                           <div class="col-sm-3">
                              <label for="inputEmail3" class=" control-label" style="text-align: left !important;">Bill
                                 Date.<strong style="color:red;">*</strong></label>
                              <?php echo $this->Form->input('bill_date', array('class' => 'form-control', 'id' => 'billdate', 'readonly', 'placeholder' => 'Enter Bill Date', 'required', 'label' => false)); ?>
                              <span id="dateError" class="error" style="color: red;"></span>
                           </div>

                           <div class="col-sm-3">
                              <label for="inputEmail3" class="control-label" style="text-align: left !important;">GRN
                                 No.<strong style="color:red;">*</strong></label>
                              <div class="">
                                 <?php echo $this->Form->input('goods_id', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'placeholder' => 'Enter Grn ', 'readonly', 'autofocus', 'autocomplete' => 'off', 'id' => 'goodsId')); ?>
                              </div>
                           </div>

                           <div class="col-sm-3">
                              <label for="inputEmail3" class="control-label" style="text-align: left !important;">Purchaseorder ID
                                 <strong style="color:red;">*</strong></label>
                              <div class="">
                                 <?php echo $this->Form->input('purchaseorder_id', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'placeholder' => 'Enter Purchaseorder ID ', 'readonly', 'autofocus', 'autocomplete' => 'off', 'id' => 'poId')); ?>
                              </div>
                           </div>

                           <div class="ctpcontent form-group" style="display:none">
                              <label for="inputEmail3" class="col-sm-2">Items<strong
                                    style="color:red;">*</strong></label>
                              <div class="col-sm-12">
                                 <table class="table" id="customers" width="100%">
                                    <thead>
                                       <tr class="totalColumn">
                                          <th>Item</th>
                                          <th>Qty</th>
                                          <th>Return Qty</th>
                                          <th>Received Qty</th>
                                          <th>Unit Price</th>
                                          <th>Total Price</th>
                                          <th>Tax Rate</th>
                                          <th>Tax Amount</th>
                                          <th>Total Amount</th>
                                       </tr>
                                    </thead>
                                    <tbody class="product_containes" id="product_containes">
                                    </tbody>
                                    <tfoot>
                                       <tr class="titlerows" style="background-color: #c8c8c8;">
                                          <td colspan="8" class="text-right" style="font-weight:bold;font-size:16px;">
                                             Freight Amount (&#x20b9;)</td>
                                          <td colspan="1" class="totalha" style="text-align: right;">
                                             <?php echo $this->Form->input('freight', array('class' => 'form-control', 'id' => 'freight', 'onkeyup' => "if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')", 'onchange' => 'return total();', 'type' => 'text', 'label' => false, 'placeholder' => 'Freight', 'readonly', 'autofocus', 'autocomplete' => 'off', 'maxlength' => '6', 'value' => '0')); ?>
                                          </td>
                                       </tr>
                                       <tr class="titlerow" style="background-color: #c8c8c8;">
                                          <td class="totala1" style="text-align: right;"></td>
                                          <td>
                                          </td>
                                          <td class="totala2" style="text-align: right;"></td>
                                          <td colspan="5" class="text-right" style="font-weight:bold;font-size:16px;">
                                             Net Amount (&#x20b9;)</td>
                                          <td >
                                             <?php echo $this->Form->input('totalBillAmount', array('class' => 'form-control totalBillAmount', 'id' => '', 'type' => 'text', 'label' => false,'readonly', 'autofocus', 'autocomplete' => 'off')); ?>
                                          </td>
                                       </tr>
                                    </tfoot>
                                 </table>
                              </div>
                           </div>

                          
                  <div class="col-sm-12">
                     <label for="inputEmail3" class="control-label" style="text-align: left!important;">Description
                    <strong style="color:red;">*</strong></label>
                     <?php echo $this->Form->input('description', array('class' => 'form-control', 'id' => 'description', 'type' => 'textarea', 'label' => false, 'placeholder' => 'Enter Description', 'autofocus', 'autocomplete' => 'off','required')); ?>
                  </div>

                  <!-- <div class="col-md-4">
                <label for="inputEmail3" class=" control-label" style="text-align: left !important;"> 
                  Code<strong style="color:red;"></strong></label>
                <?php echo $this->Form->input('item', array('class' => 'form-control', 'type' => 'number', '', 'label' => false, 'placeholder' => 'Enter Number', 'autofocus', 'autocomplete' => 'off', 'id' => 'item-isbn')); ?>
              </div> -->
            
            </div>
                 </div>
                        <div class="col-md-12" style="text-align:right;margin-top:10px; margin-right: 12px;">
                        <?php
                        if (isset ($item['id'])) {
                           echo $this->Form->submit(
                              'Update',
                              array('class' => 'btn btn-info pull-right', 'title' => 'Update')
                           );
                        } else {
                           echo $this->Form->submit(
                              'Submit',
                              array('class' => 'btn btn-info pull-right', 'title' => 'Add')
                           );
                        }
                        ?>
                     </div>
                  </div>
                  <?php echo $this->Form->end(); ?>

                  <!-- /.box-footer -->

               </div>
            </div>
            <!--/.col (right) -->
         </div>
         <!-- /.row -->
   </section>
   <!-- /.content -->
</div>



<script type="text/javascript">

   function calculateqty(input, index) {
      var returnQty = parseFloat($(input).val());
      var totalreturnQty = parseFloat($('#total_return_qty_' + index).val());
      var receivedQty =parseFloat($('#received_qty_' + index).val());
      var unitPrice = $('#unit_price_' + index).val();
      var taxRate = $('#tax_rate_' + index).val();
      var oldTotal = $('#total_amount_' + index).val();

      var checkQty = returnQty + totalreturnQty;
      var maxQty = receivedQty - totalreturnQty;
   
      if(receivedQty < checkQty){
         alert('Return quantity can not be greater then ' + maxQty);
         $('#returnqty_' + index).val((0).toFixed(2));
      }else{
         var cost_price = (returnQty * unitPrice).toFixed(2);
      var tax_amt = ((cost_price * taxRate) / 100).toFixed(2);
      var total_amount = (parseFloat(cost_price) + parseFloat(tax_amt)).toFixed(2);

      $('#cost_price_' + index).val(cost_price);
      $('#tax_amount_' + index).val(tax_amt);
      $('#total_amount_' + index).val(total_amount);

      var total = 0;
      $('.totalAmount').each(function () {
         var value = parseFloat($(this).val());
         if (!isNaN(value)) {
            total += value;
         }
      });
      $('.totalBillAmount').val(total.toFixed(2));
      }
      
   }


   $("#itemname").on('change', function () {
      var goods_id = $(this).val();

      if (goods_id != "") {
         $.ajax({
            type: 'POST',
            url: '<?php echo ADMIN_URL; ?>goodsreceived/getgrndetails',
            data: {
               'goods_id': goods_id
            },
            success: function (data) {
               $('#goodsId').val(goods_id);
               var dataArray = JSON.parse(data);
               $('#billdate').val(dataArray.billDate);
               $('#poId').val(dataArray.po_id);
               $(".ctpcontent").css("display", "block");
               $(".product_containes").empty();
               $.each(dataArray.stockitems, function (index, item) {
                  var rowHtml = '<tr>' +
                     '<input type="hidden" readonly name = item_id[] class="form-control" id="itemid_' + index + '" value="' + item.item_id + '">' +
                     '<td  width="20%" ><input type="text"  name = item_name[] readonly class="form-control" id="itemname_' + index + '" value="' + item.itemname + '"></td>' +
                     '<td  width="10%" ><input type="text"  name = return_qty[] class="form-control" autocomplete = "off" onkeyup="calculateqty(this, ' + index + ')"  id="returnqty_' + index + '" value="' + item.return_qty +  '"></td>' +
                     '<td  width="10%" ><input type="text"  name = total_return_qty[] readonly class="form-control" id="total_return_qty_' + index + '" value="' + item.total_return_qty + '"></td>' +
                     '<td  width="10%" ><input type="text"  name = received_qty[] readonly class="form-control" id="received_qty_' + index + '" value="' + item.recived_qty + '"></td>' +
                     '<td  width="10%" ><input type="text"  name = rate[] readonly class="form-control" id="unit_price_' + index + '" value="' + item.rate + '"></td>' +
                     '<td  width="10%" ><input type="text"  name = cost_price[] readonly class="form-control" id="cost_price_' + index + '" value="' + item.cost_price + '"></td>' +
                     '<td  width="10%" ><input type="text"  name = taxrate[] readonly class="form-control" id="tax_rate_' + index + '" value="' + item.taxrate + '"></td>' +
                     '<td  width="10%" ><input type="text"  name = taxamount[] readonly class="form-control" id="tax_amount_' + index + '" value="' + item.taxamount + '"></td>' +
                     '<td  width="10%" ><input type="text"  name = total[] readonly class="form-control totalAmount" id="total_amount_' + index + '" value="' + item.total + '"></td>' +
                     '</tr>';
                  $(".product_containes").append(rowHtml);
               });
            }

         });
      }
   });


   function getbillno(vendor_id) {
      if (vendor_id != "") {
         $.ajax({
            type: 'POST',
            url: '<?php echo ADMIN_URL; ?>goodsreceived/getbillno',
            data: {
               'vendor_id': vendor_id,
            },
            success: function (data) {
               if (data) {
                  var select = $("#itemname");
                  select.empty();
                  select.append($('<option>', {
                     value: '',
                     text: '-- Select Bill No.--'
                  }));
                  var dataArray = JSON.parse(data);
                  dataArray.forEach(function (item) {
                     select.append($('<option>', {
                        value: item.id,
                        text: item.bill_no,
                     }));
                  });
               }
            },

         });

      };
   };

   function cllbckretail(name, id) {
      $('.secrh-retail').val(name);
      $('#retail_ids').val(id);
      getbillno(id);
      $('#testUL').hide();
      $('#testUL1').hide();
   }

   $(document).ready(function () {
      $('.secrh-retail').bind('keyup', function () {
         var pos = $(this).val();
         $('#testUL').show();
         $('#retail_ids').val('');
         var count = pos.length;
         if (count > 0) {
            $.ajax({
               type: 'POST',
               url: '<?php echo ADMIN_URL; ?>vendors/getname',
               data: {
                  'fetch': pos,
               },
               success: function (data) {
                  if (data) {
                     $('#testUL ul').html(data);
                  } else {
                     $('#testUL').hide();
                     $('#testUL1').show();
                  }
               },
            });
         } else {
            $('#testUL').hide();
            $('#testUL1').hide();
         }
      });
   });
</script>