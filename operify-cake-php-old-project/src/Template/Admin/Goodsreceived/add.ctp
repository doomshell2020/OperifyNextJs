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
               margin-bottom: 20px;
            }



            #customers td,
            #customers th {
               border: 1px solid #ddd;
               padding: 8px;
            }

            #customers tr:nth-child(even) {
               background-color: #f2f2f2;
            }

            #customers tr:hover {
               background-color: #ddd;
            }

            #customers th {
               padding-top: 12px;
               padding-bottom: 12px;
               text-align: left;
               background-color: #c8c8c8;
               color: #333333;
            }

            #testUL,
            #testULs {
               position: relative;
               display: none;
            }

            #testUL ul,
            #testULs ul {
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

            #testUL ul li,
            #testULs ul li {
               padding: 5px 8px;
               border: 1px solid lightgray;
            }

            #testUL ul li a,
            #testULs ul li a {
               color: black;
            }

            .select2.select2-container .select2-selection {
               margin-bottom: 0px !important;
            }
         </style>
         <!-- right column -->
         <div class="col-md-12">
            <!-- Horizontal Form -->
            <div class="box box-info">
               <?php echo $this->Flash->render(); ?>
               <div class="box-header with-border">
                  <h3 class="box-title">
                     <?php if (isset($location['id'])) {
                        echo 'Edit Post New';
                     } else {
                        echo 'Generate G.R.N.';
                     } ?>
                  </h3>
               </div>
               <!-- /.box-header -->
               <!-- form start -->
               <?php echo $this->Form->create('Goodsreceived', array(
                  'class' => 'form-horizontal',
                  'enctype' => 'multipart/form-data',
                  'id' => 'sevice_form',
                  'validate'
               )); ?>
               <div class="box-body">
                  <div class="form-group" style="margin-bottom:0px;">


                     <?php if ($userId) { ?>
                        <div class="row">
                           <div class="col-sm-4" style="margin-bottom:15px;">
                              <label for="inputEmail3" class="">Purchase Order ID <strong
                                    style="color:red;">*</strong></label>
                              <?php echo $this->Form->input('purchaseorder_id', array('class' => 'form-control select2 findpoid purchasedate', 'id' => 'purchase', 'type' => 'select', 'options' => $purchaseorderid, 'label' => false, 'autofocus', 'empty' => 'Enter Purchase Order ID', 'autocomplete' => 'off', 'required')); ?><span
                                 id="estimateddevlierydate" style="color:red;"></span>
                              <input type="hidden" name="vendor_id" class="form-control" id="vendor_id" value="">
                           </div>
                           <!-- test -->
                           <div class="col-sm-8" style="margin:auto;">
                              <span class="showdata"></span>
                           </div>
                           <div class="col-sm-3" style="margin-bottom:15px;">

                           </div>
                        </div>
                     <?php   } else { ?>
                        <div class="row">
                           <div class="col-sm-4" style="margin-bottom:15px;">
                              <label for="inputEmail3" class="">Inspection Id <strong
                                    style="color:red;">*</strong></label>
                              <?php echo $this->Form->input('inspection_id', array('class' => 'form-control select2 findpoid purchasedate', 'id' => 'inspection', 'type' => 'select', 'options' => $InspectionGrn, 'label' => false, 'autofocus', 'empty' => 'Enter Inspection ID', 'autocomplete' => 'off', 'required')); ?><span id="estimateddevlierydate" style="color:red;"></span>
                           </div>
                           <div class="col-sm-8" style="margin:auto;">
                              <span class="showdata"></span>
                           </div>
                           <div class="col-sm-3" style="margin-bottom:15px;">
                           </div>
                        </div>
                     <?php } ?>




                     <div class="inspection">

                        <div class="row">
                           <div class="col-sm-3" style="margin-bottom:15px;">
                              <label for="inputEmail3" class="">Inward Date <strong style="color:red;">*</strong></label>
                              <?php echo $this->Form->input('inwarddate', array('class' => 'form-control', 'id' => 'datepicker3', 'type' => 'text', '', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'required')); ?>
                           </div>
                           <div class="col-sm-3" style="margin-bottom:15px;">
                              <label for="inputEmail3">Bill No. <strong style="color:red;">*</strong></label>
                              <?php echo $this->Form->input('bill_no', array('class' => 'form-control', 'id' => 'bill_no', 'type' => 'text', 'label' => false, 'autofocus', 'empty' => 'Enter Bill No', 'autocomplete' => 'off', 'required')); ?>
                           </div>
                           <script>
                              $(document).ready(function() {
                                 $(".readonly").on('keyup', function(e) {
                                    $(".readonly").val('');
                                 });
                              });
                           </script>
                           <div class="col-sm-3" style="margin-bottom:15px;">
                              <label for="inputEmail3">Bill Date <strong style="color:red;">*</strong></label>
                              <?php echo $this->Form->input('bill_date', array('class' => 'form-control readonly', 'id' => 'datepicker2', 'type' => 'text', 'label' => false, 'autofocus', 'empty' => 'Enter Bill No', 'autocomplete' => 'off', 'required')); ?>
                           </div>
                        </div>



                        <div class="ctpcontent form-group" style="display:none">
                           <label for="inputEmail3" class="col-sm-2">Items<strong style="color:red;">*</strong></label>
                           <div class="col-sm-12">
                              <table id="customers">
                                 <thead>
                                    <tr class="totalColumn">
                                       <th colspan="2">Item</th>
                                       <th>Qty</th>
                                       <th>Received Qty</th>
                                       <th>UOM</th>
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



                                    <tr class="titlerow" style="background-color: #c8c8c8;">
                                       <td colspan="6" class="text-right" style="font-weight:bold;font-size:16px;">Net Amount
                                          (&#x20b9;)</td>
                                       <td class="totala1" style="text-align: right;"></td>
                                       <td></td>
                                       <td class="totala2" style="text-align: right;"></td>
                                       <td class="totala" style="text-align: right;"></td>
                                       <input type="hidden" name="tqty" class="tqty" value="0">
                                    </tr>
                                 </tfoot>
                              </table>
                           </div>
                        </div>

                        <div class="form-group">
                           <div class="col-sm-12">
                              <label for="inputEmail3">Remark</label> <strong style="color:red;">*</strong>
                              <?php echo $this->Form->input('remark', array('class' => 'form-control', 'id' => 'remark', 'type' => 'textarea', 'label' => false, 'placeholder' => 'Remark', 'autofocus', 'autocomplete' => 'off', 'required')); ?>
                           </div>
                        </div>


                     </div>


                  </div>
                  <!-- /.box-body -->
                  <div class="box-footer">
                     <?php
                     if (isset($location['id'])) {
                        echo $this->Form->submit(
                           'Update',
                           array('class' => 'btn btn-info pull-right', 'id' => 'formsubmitbtn', 'title' => 'Update')
                        );
                     } else {
                        echo $this->Form->submit(
                           'Submit',
                           array('class' => 'btn btn-info pull-right', 'id' => 'formsubmitbtn', 'title' => 'Add')
                        );
                     }
                     ?><?php
                  echo $this->Html->link('Back', [
                     'action' => 'index'
                  ], ['class' => 'btn btn-default']); ?>
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
   $(document).ready(function() {
      $(".add-batch-fields").click(function() {
         var itemId = $('#itemname').val();
         var quanti = $('#quant').val();
         var purorid = $('#purchaseorder').val();
         var costprice = $('#costprice').val();
         var numItems = $('.video_details').length;
         numItems++;
         var sum = 0;
         $.ajax({
            type: "POST",
            url: '<?php echo SITE_URL; ?>admin/purchaseorder/purchaseordertemp',
            data: {
               'item_id': itemId,
               'quantity': quanti,
               'purchaseorder': purorid,
               'srno': numItems,
               'cost_price': costprice
            },
            cache: false,
            success: function(html) {
               $(".product_containes").append(html);
            }
         });
         $(".ctpcontent").css("display", "block");
         $('#itemname').val('');
         $('#quant').val('');
         $('#quant').val('');
         $('#costprice').val('');
      });

      $("body").on("click", ".remove", function() {
         var row = $(this).closest('.video_details').remove();
         //var row = $(this).closest('tr');
         var dynamicValue = $(row).find('.cou').text();
         dynamicValue = parseInt(dynamicValue);
         $('.cou').each(function(idx, elem) {
            $(elem).text(idx + 1);
         });
         // Check Total Quantity on click remove
         var checkval = $(this).attr('data-val');
         var checktoatalquant = $('.totalq').text();
         if (checktoatalquant != "") {
            tQuant = parseInt(checktoatalquant) - parseInt(checkval);
         }
         $('.totalq').text(tQuant);

         //Check Total Amount on click remove
         var checkamo = $(this).attr('data-amount');
         //alert(checkamo);
         var checktoatalamou = $('.totala').text();
         if (checktoatalamou != "") {
            tAmou = parseFloat(checktoatalamou) - parseFloat(checkamo);
         }
         $('.totala').text(tAmou);
         var purchaseorderId = $(this).attr('data');
         //alert(purchaseorderId);
         $.ajax({
            type: "POST",
            url: '<?php echo SITE_URL; ?>admin/purchaseorder/removepurchaseordertemp',
            data: {
               'id': purchaseorderId
            },
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
   $(document).ready(function() {
      $('#datepicker1').datepicker({
         dateFormat: 'dd-mm-yy',
         yearRange: '2018:2025',
         minDate: new Date(),
         onSelect: function(date) {
            var selectedDate = new Date(date.split('-').reverse().join('-'));
            var endDate = new Date(selectedDate);
            endDate.setDate(endDate.getDate());
            // $("#datepicker2").datepicker("option", "minDate", endDate);
            // $("#datepicker2").val(date);
         }
      });

      $('#datepicker2').datepicker({
         dateFormat: 'dd-mm-yy',
         changeMonth: true,
         changeYear: true,
         maxDate: new Date(),
      });

      $('#datepicker3').datepicker({
         dateFormat: 'dd-mm-yy',
         changeMonth: true,
         changeYear: true,
         maxDate: new Date(),
      });

      $('.purchasedate').change(function() {
         var poId = $(this).val();
         if (poId) {
            $.ajax({
               url: '<?php echo SITE_URL; ?>admin/purchaseorder/getPoDetails',
               type: 'POST',
               data: {
                  purchaseorder_id: poId
               },
               success: function(response) {

                  var trimmedResponse = response.trim();

                  // Sanitize response to remove any unexpected characters
                  var sanitizedResponse = trimmedResponse.replace(/[^0-9\-]/g, '');

                  var parts = sanitizedResponse.split('-');

                  if (parts.length === 3) {
                     var day = parseInt(parts[0], 10);
                     var month = parseInt(parts[1], 10) - 1;
                     var year = parseInt(parts[2], 10);

                     // console.log('Day:', day, 'Month:', month, 'Year:', year);

                     if (!isNaN(day) && !isNaN(month) && !isNaN(year)) {
                        var minDate = new Date(year, month, day);
                        // console.log('Parsed minDate:', minDate);

                        if (!isNaN(minDate.getTime())) {
                           $('#datepicker3').datepicker('option', 'minDate', minDate);
                           $('#datepicker2').datepicker('option', 'minDate', minDate);
                           // alert('minDate set to: ' + minDate.toLocaleDateString());
                        } else {
                           alert('Failed to parse minDate: Invalid date constructed.');
                        }
                     } else {
                        alert('Invalid response data. Please check the server response.');
                        console.error('Invalid date components:', {
                           day,
                           month,
                           year
                        });
                     }
                  } else {
                     // alert('Incorrect response format. Expected dd-mm-yyyy.');
                     console.error('Unexpected response format:', trimmedResponse);
                  }
               },


               error: function() {
                  alert('Failed to fetch PO details.');
               }
            });
         }
      });
   });
</script>

<script>
   function cllbckretail(id, cid) {
      $('.secrh-retail').val(id);
      $('#retail_ids').val(cid);
      $('#testUL').hide();

   }
   $(function() {
      $('.secrh-retail').bind('keyup', function() {
         var pos = $(this).val();

         $('#testUL').show();
         $('#retail_ids').val('');
         var count = pos.length;
         if (count > 0) {
            $.ajax({
               type: 'POST',
               url: '<?php echo ADMIN_URL; ?>purchaseorder/getpurchaseorderid',
               data: {
                  'fetch': pos
               },
               success: function(data) {
                  console.log(data);
                  $('#testUL ul').html(data);
               },
            });
         } else {
            $('#testUL').hide();
         }
      });
   });
</script>




<script type="text/javascript">
   $(document).ready(function() {
      $("#inspection").on('change', function() {

         $('.totala').text('');
         $('.totalq').text('');
         $('.inspection').html('');
         var ponumber = $(this).val();
         // alert(ponumber);
         if (ponumber != "") {
            $.ajax({
               type: 'POST',
               url: '<?php echo ADMIN_URL; ?>goodsreceived/purchaseorderitems',
               data: {
                  'id': ponumber
               },
               success: function(data) {
                  $(".inspection").css("display", "block");
                  $('.inspection').html(data);
               },
            });
         } else {
            $(".inspection").css("display", "none");
         }
      });
   });






   $(document).ready(function() {
      $("#purchase").on('change', function() {

         $('.totala').text('');
         $('.totalq').text('');
         $('.product_containes').html('');
         var ponumber = $(this).val();
         // alert(ponumber);
         if (ponumber != "") {
            $.ajax({
               type: 'POST',
               url: '<?php echo ADMIN_URL; ?>purchaseorder/purchaseorderitems',
               data: {
                  'id': ponumber
               },
               success: function(data) {
                  $(".ctpcontent").css("display", "block");
                  $('.product_containes').html(data);
               },
            });
         } else {
            $(".ctpcontent").css("display", "none");
         }
      });
   });



   // findpoid
   $(".findpoid").on('change', function() {
      $('.product_containes').html('');
      var ponumber = $(this).val();
      $.ajax({
         type: 'POST',
         url: '<?php echo ADMIN_URL; ?>purchaseorder/checkdeliverynote',
         data: {
            'id': ponumber
         },
         success: function(data) {
            // console.log(data);
            if (data == 0) {
               $('.showdata').css("display", "none");
               $('#checkreq').removeAttr("required");
            } else {
               $('.showdata').html(data);
               $('.showdata').css("display", "block");
               $('#checkreq').attr("required", "required");

            }

         },
      });
   });
</script>
<script>
   $(document).ready(function() {
      $('#sevice_form').on('submit', function(e) {
         $("#formsubmitbtn").css("display", "none");
      });
   });
</script>