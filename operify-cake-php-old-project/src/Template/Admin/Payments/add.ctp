<div class="content-wrapper">
   <!-- Content Header (Page header) -->
   <section class="content-header">
      <h1>
         Add Payment
      </h1>
      <ol class="breadcrumb">
         <li><a href="<?php echo SITE_URL; ?>admin/paymentmanager"><i class="fa fa-home"></i>Home</a></li>
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
                     <?php
                     echo 'Generate Payment';
                     ?>
                  </h3>
               </div>
               <!-- /.box-header -->
               <!-- form start -->
               <?php echo $this->Form->create(
                  '',
                  array(
                     'class' => 'form-horizontal',
                     'enctype' => 'multipart/form-data',
                     'id' => 'sevice_form',
                     'validate'
                  )
               ); ?>
               <div class="box-body">
                  <div class="form-group" style="margin-bottom:0px;">

                     <div class="row">
                        <script>
                           $(document).ready(function () {
                              $(".readonly").on('keyup', function (e) {
                                 $(".readonly").val('');
                              });
                           });
                        </script>
                        <div class="col-sm-4" style="margin-bottom:15px;">
                           <label for="inputEmail3">Pay Date <strong style="color:red;">*</strong></label>
                           <?php echo $this->Form->input('pay_date', array('class' => 'form-control readonly', 'id' => 'datepicker2', 'type' => 'text', 'label' => false, 'autofocus', 'readonly', 'empty' => 'Enter Pay Date', 'autocomplete' => 'off', 'required')); ?>
                        </div>

                        <div class="col-sm-4" style="margin-bottom:15px;">
                           <label for="inputEmail3">Recipt No. <strong style="color:red;">*</strong></label>
                           <?php echo $this->Form->input('receipt_no', array('class' => 'form-control', 'id' => 'bill_no', 'type' => 'text', 'label' => false, 'autofocus', 'value' => $receipt_no, 'readonly', 'placeholder' => 'Enter Recipt No', 'autocomplete' => 'off', 'required')); ?>
                        </div>

                        <div class="col-sm-4" style="margin-bottom:15px;">
                           <label for="inputEmail3" class="control-label">Vendor<strong
                                 style="color:red;">*</strong></label>
                           <input type="hidden" required="required" name="vendor_id" id="retail_ids">
                           <?php echo $this->Form->input('nitem', array('class' => 'form-control secrh-retail', 'id' => 'itemname', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'placeholder' => 'Enter Vendor Name','required')); ?>
                           <div id="testUL" style="display:none;">
                              <ul></ul>
                           </div>
                        </div>

                        <div class="col-sm-4" style="margin-bottom:15px;">
                           <label for="inputEmail3">Mobile No.<strong style="color:red;"></strong></label>
                           <?php echo $this->Form->input('mobile1', array('class' => 'form-control ', 'type' => 'text', 'id' => 'mobile', 'label' => false, 'autofocus', 'readonly', 'placeholder' => 'Enter Mobile No', 'autocomplete' => 'off')); ?>
                        </div>

                        <div class="col-sm-4" style="margin-bottom:15px;">
                           <label for="inputEmail3">Email<strong style="color:red;"></strong></label>
                           <?php echo $this->Form->input('emai1l', array('class' => 'form-control', 'readonly', 'id' => 'email', 'type' => 'text', 'label' => false, 'autofocus', 'placeholder' => 'Enter Email', 'autocomplete' => 'off')); ?>
                        </div>

                        <div class="col-sm-4" style="margin-bottom:15px;">
                           <label for="inputEmail3">Address<strong style="color:red;"></strong></label>
                           <?php echo $this->Form->input('address1', array('class' => 'form-control', 'readonly', 'id' => 'address', 'type' => 'text', 'label' => false, 'autofocus', 'placeholder' => 'Enter Address', 'autocomplete' => 'off', )); ?>
                        </div>
                        <div class="col-sm-4" style="margin-bottom:15px;">
                           <label for="inputEmail3">Outstanding Amount<strong style="color:red;">*</strong></label>
                           <?php echo $this->Form->input('out_amt', array('class' => 'form-control', 'readonly', 'id' => 'outs_amt', 'type' => 'text', 'label' => false, 'autofocus', 'placeholder' => 'Enter Outstanding Amount', 'autocomplete' => 'off', 'required')); ?>
                        </div>

                        <div class="col-sm-4" style="margin-bottom:15px;">
                           <label for="inputEmail3">Pay Amount<strong style="color:red;">*</strong></label>
                           <?php echo $this->Form->input('total_amt', array('class' => 'form-control', 'id' => '', 'type' => 'text', 'label' => false, 'autofocus','onkeypress' => 'return isNumberKey(event)', 'placeholder' => 'Enter Pay Amount', 'autocomplete' => 'off', 'required')); ?>
                        </div>
                     </div>
                  </div>
               </div>

               <div class="form-group">
                  <div class="col-sm-12">
                     <label for="inputEmail3">Remark</label><strong style="color:red;">*</strong>
                     <?php echo $this->Form->input('remark', array('class' => 'form-control', 'id' => 'remark', 'type' => 'textarea', 'label' => false, 'placeholder' => 'Remark', 'autofocus', 'autocomplete' => 'off','required')); ?>
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
               ?>
               <?php
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
   $(document).ready(function () {
      $('#datepicker1').datepicker({
         dateFormat: 'dd-mm-yy',
         yearRange: '2018:2025',
         minDate: new Date(),
         onSelect: function (date) {
            var selectedDate = new Date(date);
            var endDate = new Date(selectedDate);
            endDate.setDate(endDate.getDate());
            $("#datepicker2").datepicker("option", "minDate", endDate);

         }
      });
      $('#datepicker2').datepicker({
         dateFormat: 'dd-mm-yy',
         yearRange: '2018:2025',
         maxDate: new Date(),
      });
      $('#datepicker2').datepicker('setDate', 'today');

      $('#datepicker3').datepicker({
         dateFormat: 'dd-mm-yy',
         yearRange: '2018:2025',
         maxDate: new Date(),
      });
   });
</script>


<script>
   $(document).ready(function () {
      $('#sevice_form').on('submit', function (e) {
         $("#formsubmitbtn").css("display", "none");
      });
   });

   function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode != 46 && charCode > 31 &&
      (charCode < 48 || charCode > 57))
      return false;

    return true;
  }
</script>

<script>

function getvendorname(vendor_id) {
    $.ajax({
        type: 'POST',
        url: '<?php echo ADMIN_URL; ?>payments/getvendorname',
        data: {
            'vendor_id': vendor_id,
        },
        success: function (data) {
            console.log(data);
            var responseData = JSON.parse(data);
            var vendorDetails = responseData.vendordetails;
            var balance = responseData.balance;

            $('#mobile').val(vendorDetails.contact_no);
            $('#email').val(vendorDetails.email);
            $('#address').val(vendorDetails.address);
            $('#outs_amt').val(balance);
        },
    });
}

   function cllbckretail0(id, cid, sid) {
      $('.secrh-retail').val(id);
      $('#retail_ids').val(cid);
      $('#testUL').hide();
      getvendorname(cid);
   }

   $(function () {
      $('.secrh-retail').bind('keyup', function () {
         var pos = $(this).val();
         var check = 0;
         $('#testUL').show();
         $('#retail_ids').val('');
         var count = pos.length;
         if (count > 0) {
            $.ajax({
               type: 'POST',
               url: '<?php echo ADMIN_URL; ?>vendors/getname',
               data: {
                  'fetch': pos,
                  'check': check
               },
               success: function (data) {
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